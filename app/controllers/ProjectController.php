<?php
/**
* Display project homepage.
*
* @author Team USVN <contact@usvn.info>
* @link http://www.usvn.info
* @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
* @copyright Copyright 2007, Team USVN
* @since 0.5
* @package usvn
* @subpackage project
*
* This software has been written at EPITECH <http://www.epitech.net>
* EPITECH, European Institute of Technology, Paris - FRANCE -
* This project has been realised as part of
* end of studies project.
*
* $Id$
*/

class ProjectController extends USVN_Controller
{

	protected $_project;

	public function preDispatch()
	{
		parent::preDispatch();
		$project = str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR, $this->getRequest()->getParam('project'));
		$table = new USVN_Db_Table_Projects();
		$project = $table->fetchRow(array("projects_name = ?" => $project));
		if ($project === null)
			$this->_redirect('/');
		$this->_project = $project;

		$this->view->isAdmin = $this->isAdmin();

		$user = $this->getRequest()->getParam('user');
		$this->view->user = $user;
		$this->view->secret_id = $user->secret_id;
		$groups = $user->findManyToManyRowset('USVN_Db_Table_Groups', 'USVN_Db_Table_UsersToGroups');
		$find = false;
		foreach ($groups as $group)
		{
			if ($project->groupIsMember($group))
			{
				$find = true;
				break;
			}
		}
		if (!$find && !$this->isAdmin())
			$this->_redirect('/');
		if (strlen($project->name) > 12)
			$shortName = substr($project->name, 0, 12) . '..';
		else
			$shortName = $project->name;
		$this->view->project = $this->_project;
		$this->view->submenu = array(
			array('label' => $shortName),
			array('label' => 'Index',    'url' => array('action' => '', 'project' => $project->name), 'route' => 'project'),
			array('label' => 'Timeline', 'url' => array('action' => 'timeline', 'project' => $project->name), 'route' => 'project'),
			array('label' => 'Browser',  'url' => array('action' => 'browser', 'project' => $project->name), 'route' => 'project'),
			array('label' => 'Roadmap',  'url' => array('action' => 'milestones', 'project' => $project->name), 'route' => 'project')
			);
	}

	protected function isAdmin()
	{
		if (!isset($this->view->isAdmin))
		{
			$user = $this->getRequest()->getParam('user');
			$this->view->isAdmin = $this->_project->userIsAdmin($user) || $user->is_admin;
		}
		return $this->view->isAdmin;
	}

	protected function requireAdmin()
	{
		if (!$this->isAdmin())
			$this->_redirect("/project/".str_replace('/', USVN_URL_SEP, $this->_project->name)."/");
	}

	public function indexAction()
	{
		$this->view->project = $this->_project;
		$SVN = new USVN_SVN($this->_project->name);
		$config = Zend_Registry::get('config');
		$this->view->subversion_url = $config->subversion->url . $this->_project->name;
		foreach ($SVN->listFile('/') as $dir)
		{
			if ($dir['name'] == 'trunk')
				$this->view->subversion_url .= '/trunk';
		}
		$this->view->log = $SVN->log(5);
	}

	public function browserAction()
	{
		$project = $this->_project;
		$path = $this->getRequest()->getParam('path');
		if ($path[0] != '/')
			$path = '/' . $path;
		$SVN = new USVN_SVN($project->name);
		$this->view->files = $SVN->listFile($path);
		$this->view->project = $project;
		$this->view->path = $path;
	}

	public function timelineAction()
	{
		$project = $this->_project;

		//get the identity of the user
		$identity = Zend_Auth::getInstance()->getIdentity();

		$user_table = new USVN_Db_Table_Users();
		$user = $user_table->fetchRow(array('users_login = ?' => $identity['username']));

		$table = new USVN_Db_Table_UsersToProjects();
		$userToProject = $table->fetchRow(array(
			'users_id = ?' => $user->users_id,
			'projects_id = ?' => $project->projects_id));

		$this->view->project = $project;
		$SVN = new USVN_SVN($this->_project->name);
		$this->view->log = $SVN->log(100);
	}

	public function adduserAction()
	{
		$this->requireAdmin();
		$table = new USVN_Db_Table_Users();
		$user = $table->fetchRow(array("users_login = ?" => $this->getRequest()->getParam('users_login')));
		if ($user !== null) {
			try {
				$this->_project->addUser($user);
			}
			catch (Exception $e) {
			}
		}
		$this->_redirect("/project/".str_replace('/', USVN_URL_SEP, $this->_project->name)."/");
	}

	public function deleteuserAction()
	{
		$this->requireAdmin();
		$this->_project->deleteUser($this->getRequest()->getParam('users_id'));
		$this->_redirect("/project/".str_replace('/', USVN_URL_SEP, $this->_project->name)."/");
	}

	public function addgroupAction()
	{
		$this->requireAdmin();
		$table = new USVN_Db_Table_Groups();
		$group = $table->fetchRow(array("groups_name = ?" => $this->getRequest()->getParam('groups_name')));
		if ($group !== null) {
			try {
				$this->_project->addGroup($group);
			}
			catch (Exception $e) {
			}
		}
		$this->_redirect("/project/".str_replace('/', USVN_URL_SEP, $this->_project->name)."/");
	}

	public function deletegroupAction()
	{
		$this->requireAdmin();
		$this->_helper->viewRenderer->setNoRender();
		$this->_project->deleteGroup($this->getRequest()->getParam('groups_id'));
		$this->_redirect("/project/".str_replace('/', USVN_URL_SEP, $this->_project->name)."/");
	}

	public function showAction()
	{

		$svn_file_path = $this->getRequest()->getParam('file');
		$file_ext = pathinfo($svn_file_path, PATHINFO_EXTENSION);
		include_once('geshi/geshi.php');
		$this->view->project = $this->_project;
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$project_name = str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR,$this->_project->name);
		$this->view->path = $svn_file_path;
		$local_file_path = USVN_SVNUtils::getRepositoryPath($config->subversion->path."/svn/".$project_name."/".$svn_file_path);
		$revision = $this->getRequest()->getParam('rev');
		$file_rev = '';
		if (!empty($revision))
		{
			if (is_numeric($revision) && $revision > 0)
			{
				$cmd = USVN_SVNUtils::svnCommand("log --non-interactive --quiet {$local_file_path}".($revision ? "@{$revision}" : ''));
				$verif = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
				if (!$return)
				{
					$this->view->revision = $revision;
					$file_rev = '--revision '.$revision;
				}
			}
		}
		if (empty($this->view->revision))
		{
			$tmp_revision = $revision - 1;
			$cmd = USVN_SVNUtils::svnCommand("info {$local_file_path}".($revision ? "@{$tmp_revision}" : ''));
			$infos = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
			if (preg_match_all('#^\s*([^:]+)\s*:\s*(.*)\s*$#m', $infos, $tmp))
			{
				$infos = array();
				foreach ($tmp[1] as $k => $v) {
					$infos[$v] = $tmp[2][$k];
				}
				if (!isset($infos['Last Changed Rev'])) {
					throw new USVN_Exception(T_("There is a problem with the file info."));
				}
				$this->view->revision = $infos['Last Changed Rev'];
				if ($revision) {
					$this->view->message = T_("The requested revision does not exist. Switching to the last changed revision.");
				}
				$revision = $this->view->revision;
			}
		}
		$cmd = USVN_SVNUtils::svnCommand("log --non-interactive --quiet {$local_file_path}");
		$revs = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
		if (!preg_match_all('#^\s*r([0-9]+)\s*\|#m', $revs, $tmp)) {
			$cmd = USVN_SVNUtils::svnCommand("log --non-interactive --quiet {$local_file_path}@{$revision}");
			$revs = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
		}
		if (preg_match_all('#^\s*r([0-9]+)\s*\|#m', $revs, $tmp)) {
			$revs = array();
			$this->view->prev_revision = NULL;
			$this->view->next_revision = NULL;
			foreach ($tmp[1] as $k => $rev) {
				if (!$k && $revision != $rev) {
					$this->view->revision = $rev;
					$revision = $this->view->revision;
				}
				if ($this->view->prev_revision === NULL && intval($rev) < intval($this->view->revision)) {
					$this->view->prev_revision = $rev;
				}
				if ($rev > $this->view->revision) {
					$this->view->next_revision = $rev;
				}
				$revs[] = $rev;
			}
			$this->view->select_revisions = $revs;
		}
		$cmd = USVN_SVNUtils::svnCommand("cat --non-interactive {$local_file_path}@{$revision}");
		$source = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
		if ($return)
		{
			throw new USVN_Exception(T_("Can't read from subversion repository.\nCommand:\n%s\n\nError:\n%s"), $cmd, $message);
		}
		else
		{
			$this->view->color_view = $this->getRequest()->getParam('color');
			$this->view->diff_view = $this->getRequest()->getParam('diff');
			$this->view->diff_revision = $this->getRequest()->getParam('drev');
			if ($this->view->diff_revision >= $this->view->revision)
			{
				$this->view->diff_revision = $this->view->prev_revision;
			}
			if ($this->getRequest()->getParam('post') === NULL)
			{
				$this->view->color_view = 1;
			}
			$geshi = new Geshi();
			$lang_name = $geshi->get_language_name_from_extension($file_ext);
			if ($geshi->error())
			{
				$this->view->message = T_('The file type is known.');
				return ;
			}
			$this->view->language = $lang_name;
			$geshi->set_language(($this->view->color_view ? $lang_name : NULL), true);
			$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
			if ($this->view->diff_view && ($this->view->diff_revision || $this->view->prev_revision))
			{
				$d_revs = ($this->view->diff_revision ? $this->view->diff_revision : $this->view->prev_revision).':'.$this->view->revision;
				$cmd = USVN_SVNUtils::svnCommand("diff --non-interactive --revision {$d_revs} {$local_file_path}@{$revision}");
				$diff = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
				if ($return)
				{
					$this->view->message = T_('The requested diff revision does not exist.');
				}
				else
				{
					$new_source = array();
					$source = explode("\n", $source);
					array_pop($source); // Skip the final "\n"
					$diff = explode("\n", $diff);
					array_pop($diff); // Skip the final "\n"
					$source_line = NULL;
					$count_line = 0;
					$diff_lines = array();
					while (($line = array_shift($diff)) !== NULL)
					{
						$line = trim($line);
						if (preg_match('#^@@ \-[0-9,]+ \+([0-9]+),[0-9]+ @@$#', $line, $tmp))
						{
							if ($source_line === NULL)
							{
								$source_line = 1;
							}
							while (intval($source_line) < intval($tmp[1]))
							{
								array_push($new_source, array_shift($source));
								$source_line++;
								$count_line++;
							}
							continue;
						}
						if ($source_line !== NULL)
						{
							$diff_char = substr($line, 0, 1);
							if ($diff_char == '\\')
							{
								continue;
							}
							elseif ($diff_char == '-')
							{
								array_push($new_source, substr($line, 1));
								$diff_lines[$count_line] = '-';
							}
							else
							{
								if ($diff_char == '+')
									$diff_lines[$count_line] = '+';
								array_push($new_source, array_shift($source));
								$source_line++;
							}
							$count_line++;
						}
					}
					if (count($source))
					{
						$new_source = array_merge($new_source, $source);
					}
					$source = implode("\n", $new_source);
					unset($new_source);
					$this->view->diff_lines = $diff_lines;
				}
			}
			$geshi->set_source($source);
			$geshi->set_header_type(GESHI_HEADER_DIV);
			$this->view->highlighted_source = $geshi->parse_code();
			if ($geshi->error())
			{
				$this->view->highlighted_source = T_('Unkown file type, can\'t display');
				return ;
			}
			if ($this->view->diff_view)
			{
				if (preg_match('#^<div ([^>]*)><ol>(.*)</ol></div>(\s*)$#s', $this->view->highlighted_source, $tmp))
				{
					$this->view->diff_div = $tmp[1];
					$this->view->highlighted_source = $tmp[2];
				}
			}
		}
	}

	public function commitAction()
	{
		include_once('geshi/geshi.php');
		$this->view->project = $this->_project;
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$project_name = str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR,$this->_project->name);
		$local_project_path = USVN_SVNUtils::getRepositoryPath($config->subversion->path."/svn/".$project_name."/");
		$commit = $this->getRequest()->getParam('commit');
		$base = $commit - 1;
		$cmd = USVN_SVNUtils::svnCommand("log --non-interactive --revision {$commit} $local_project_path");
		$log = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
		if (!$return)
		{
			$cmd = USVN_SVNUtils::svnCommand("diff --non-interactive --revision ".($commit - 1).":{$commit} $local_project_path");
			$diff = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
			if (!$return)
			{
				$diff = explode("\n", $diff);
				array_pop($diff); // Skip the final "\n"
				$file = NULL;
				$count = 0;
				$indiff = FALSE;
				$tab_diff = array();
				$tab_index = NULL;
				foreach ($diff as $line)
				{
					if (strpos($line, 'Index: ') === 0)
					{
						$file = substr($line, 7);
						$tab_diff[$file] = array();
						$indiff = FALSE;
					}
					elseif (!$indiff)
					{
						if (preg_match('#^@@ \-([0-9]+)(,([0-9]+))? \+([0-9]+)(,([0-9]+))? @@$#', $line, $tmp))
						{
							$tab_index = count($tab_diff[$file]);
							$tab_diff[$file][$tab_index] = array(
								$base => array('begin' => $tmp[1], 'end' => (empty($tmp[3]) ? $tmp[1] : $tmp[3]), 'content' => array()),
								$commit => array('begin' => $tmp[4], 'end' => (empty($tmp[6]) ? $tmp[4] : $tmp[6]), 'content' => array()),
								'common' => array()
								);
							$count = 0;
							$indiff = TRUE;
						}
					}
					else
					{
						$diff_char = substr($line, 0, 1);
						if ($diff_char == '\\')
						{
							continue;
						}
						$line = htmlentities(substr($line, 1));
						if ($diff_char == '-')
						{
							$tab_diff[$file][$tab_index][$base]['content'][$count++] = $line;
						}
						elseif ($diff_char == '+')
						{
							$tab_diff[$file][$tab_index][$commit]['content'][$count++] = $line;
						}
						else
						{
							$tab_diff[$file][$tab_index]['common'][$count++] = $line;
						}
					}
				}
				$this->view->diff = $tab_diff;
				$this->view->commit = $commit;
				$this->view->base = $base;
				unset($tab_diff);
				$log = explode("\n", $log);
				if (preg_match('#^r[0-9]* \| (.*) \| ([0-9-]+ [0-9:]+) [^\|]* \| ([0-9]*)[^\|]*$#', $log[1], $tmp))
				{
					$this->view->author = $tmp[1];
					$this->view->date = $tmp[2];
					$this->view->log = NULL;
					for ($i = 0; $i < $tmp[3]; ++$i)
					{
						$this->view->log .= ($this->view->log != NULL ? "\n" : '').$log[3 + $i];
					}
				}
			}
			else
			{
				throw new USVN_Exception(T_("Can't read from subversion repository.\nCommand:\n%s\n\nError:\n%s"), $cmd, $message);
			}
		}
		else
		{
			throw new USVN_Exception(T_("Can't read from subversion repository.\nCommand:\n%s\n\nError:\n%s"), $cmd, $message);
		}
	}

	public function lasthundredrequestAction()
	{
		$project = $this->getRequest()->getParam('project');
		$table = new USVN_Db_Table_Projects();
		$project = $table->fetchRow(array("projects_name = ?" => $project));
		if ($project === null)
			$this->_redirect("/");
		$this->_project = $project;
		$this->view->project = $this->_project;
		$SVN = new USVN_SVN($this->_project->name);
		try
		{
			$number_start = $project = $this->getRequest()->getParam('number_start');
			$number_end = $project = $this->getRequest()->getParam('number_end');
			$this->view->number_start = $number_start;
			$this->view->number_end = $number_end;
			if (empty($number_end))
				$number_end = null;
			else
				$number_end = $this->convertDate($number_end);
			$number_start = $this->convertDate($number_start);
			$this->view->log = $SVN->log(100, $number_start, $number_end);
			$this->render("timeline");
		}
		catch(USVN_Exception $e)
		{
			$this->view->message = "No such revision found";
			$this->view->log = $SVN->log(100);
			$this->render("timeline");
		}
	}    

	public function ticketsAction()
	{
		$this->view->project = $this->_project;
		$this->view->tickets = Default_Model_Ticket::fetchAll(sprintf('project_id = %d', $this->_project->projects_id));
		$ticketsByMilestoneId = array();
		// foreach ($tickets as $ticket)
		// {
			// }
	}

	public function milestonesAction()
	{
		$this->view->project = $this->_project;
		$this->view->milestones = Default_Model_Milestone::fetchAll();
	}

	public function addmilestoneAction()
	{
		// $milestone = new Default_Model_Milestone(array('project_id' => 1, 'title' => 'My first Milestone', 'description' => 'Blah', 'creator_id' => 1, 'status' => 'new'));
		// //$milestone->save();
		// $this->view->milestone = $milestone;
		// return true;

		if (!empty($_POST['milestone']))
		{
			$data = $_POST['milestone'];
			$data['creator_id'] = $this->view->user->users_id;
			$data['creation_date'] = null;
			$data['modificator_id'] = $this->view->user->users_id;
			$data['modification_date'] = null;
			$milestone = new Default_Model_Milestone($data);
			if (1/* validate */)
			{
				if ($milestone->save())
					$this->_redirect($this->view->url(array('action' => 'milestones', 'project' => $this->_project->name), 'project', true), array('prependBase' => false));
			}
			$this->view->milestone = $milestone;
		}
	}

	public function showticketAction()
	{
		$this->view->ticket = Default_Model_Ticket::find($this->getRequest()->getParam('id'));
		$this->view->ticketId = $this->getRequest()->getParam('id');
	}

	public function addticketAction()
	{
		//		$this->_redirect($this->view->url(array('action' => 'showticket', 'project' => $this->_project->name, 'id' => '0'), 'ticket', true));
		if (!empty($_POST['ticket']))
		{
			$data = $_POST['ticket'];
			$data['creator_id'] = $this->view->user->users_id;
			$data['creation_date'] = null;
			$data['modificator_id'] = $this->view->user->users_id;
			$data['modification_date'] = null;
			$ticket = new Default_Model_Ticket($data);
			if (1/* validate */)
			{
				if ($ticket->save())
					$this->_redirect($this->view->url(array('action' => 'showticket', 'project' => $this->_project->name, 'id' => $ticket->getId()), 'ticket', true), array('prependBase' => false));
			}
			$this->view->ticket = $ticket;
		}
		$this->view->milestones = Default_Model_Milestone::fetchAll(null, 'title ASC');
	}

	public function deleteticketAction()
	{
		Default_Model_Ticket::deleteId($this->getRequest()->getParam('id'));
		$this->_redirect($this->view->url(array('action' => 'tickets', 'project' => $this->_project->name), 'project', true), array('prependBase' => false));
	}

	protected function convertDate($number)
	{
		if (strstr($number, '/') != FALSE)
		{
			$split = explode('/', $number);
			$jour = $split[0];
			$mois = $split[1];
			$annee = $split[2];
			return '{'.$annee.$mois.$jour.'}';
		}
		return $number;
	}
}
