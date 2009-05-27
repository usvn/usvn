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
	/**
	 * Project row object
	 *
	 * @var USVN_Db_Table_Row_Project
	 */
	protected $_project;

	/**
     * Pre-dispatch routines
     *
     * Called before action method. If using class with
     * {@link Zend_Controller_Front}, it may modify the
     * {@link $_request Request object} and reset its dispatched flag in order
     * to skip processing the current action.
     *
     * @return void
     */
	public function preDispatch()
	{
		parent::preDispatch();

		$project = str_replace(USVN_URL_SEP, '/', $this->getRequest()->getParam('project'));
		$table = new USVN_Db_Table_Projects();
		$project = $table->fetchRow(array("projects_name = ?" => $project));
		/* @var $project USVN_Db_Table_Row_Project */
		if ($project === null) {
			$this->_redirect("/");
		}
		$this->_project = $project;

		$this->view->isAdmin = $this->isAdmin();

		$user = $this->getRequest()->getParam('user');
		$this->view->user = $user;
		$this->view->secret_id = $user->secret_id;
		/* @var $user USVN_Db_Table_Row_User */
		$groups = $user->findManyToManyRowset("USVN_Db_Table_Groups", "USVN_Db_Table_UsersToGroups");
		$find = false;
		foreach ($groups as $group) {
			if ($project->groupIsMember($group)) {
				$find = true;
				break;
			}
		}
		if (!$find && !$this->isAdmin()) {
			$this->_redirect("/");
		}
    $this->view->submenu = array(
        array('label' => $project->name),
        array('label' => 'Index',    'url' => array('action' => '', 'project' => $project->name), 'route' => 'project'),
        array('label' => 'Timeline', 'url' => array('action' => 'timeline', 'project' => $project->name), 'route' => 'project'),
        array('label' => 'Browser',  'url' => array('action' => 'browser', 'project' => $project->name), 'route' => 'project')
    );
	}

	protected function isAdmin()
	{
		if (!isset($this->view->isAdmin)) {
			$user = $this->getRequest()->getParam('user');
			$this->view->isAdmin = $this->_project->userIsAdmin($user) || $user->is_admin;
		}
		return $this->view->isAdmin;
	}

	protected function requireAdmin()
	{
		if (!$this->isAdmin()) {
			$this->_redirect("/project/{$this->_project->name}/");
		}
	}

	public function indexAction()
	{
		$this->view->project = $this->_project;
		$SVN = new USVN_SVN($this->_project->name);
		$config = Zend_Registry::get('config');
		$this->view->subversion_url = $config->subversion->url . $this->_project->name;
		foreach ($SVN->listFile('/') as $dir) {
			if ($dir['name'] == 'trunk')
				$this->view->subversion_url .= '/trunk';
		}
		$this->view->log = $SVN->log(5);
	}


	public function browserAction()
	{
		$this->view->project = $this->_project;
	}

	public function timelineAction()
	{
//		$project = $this->getRequest()->getParam('project');
//		$table = new USVN_Db_Table_Projects();
//		$project = $table->fetchRow(array("projects_name = ?" => $project));
//		/* @var $project USVN_Db_Table_Row_Project */
//		if ($project === null) {
//			$this->_redirect("/");
//		}
//		$this->_project = $project;
		$project = $this->_project;

		//get the identity of the user
		$identity = Zend_Auth::getInstance()->getIdentity();

		$user_table = new USVN_Db_Table_Users();
		$user = $user_table->fetchRow(array('users_login = ?' => $identity['username']));

//		$table = new USVN_Db_Table_Projects();
//		$this->view->project = $table->fetchRow(array('projects_name = ?' => $project->name));

		$table = new USVN_Db_Table_UsersToProjects();
		$userToProject = $table->fetchRow(array(
			'users_id = ?' => $user->users_id,
			'projects_id = ?' => $project->projects_id));

		if ($userToProject == null)
		{
		//search project
//			$this->view->project = $project;
//			$this->view->user = $user;
//			$this->render('accesdenied');
//			return;
		}

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
		$this->_redirect("/project/{$this->_project->name}/");
	}

	public function deleteuserAction()
	{
		$this->requireAdmin();
		$this->_project->deleteUser($this->getRequest()->getParam('users_id'));
		$this->_redirect("/project/{$this->_project->name}/");
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
		$this->_redirect("/project/{$this->_project->name}/");
	}

	public function deletegroupAction()
	{
		$this->requireAdmin();
		$this->_helper->viewRenderer->setNoRender();
		$this->_project->deleteGroup($this->getRequest()->getParam('groups_id'));
		$this->_redirect("/project/{$this->_project->name}/");
	}
	
	/**
   * Display a file using appropriate highlighting
   *
   * @return void
   * @author Zak
   */
	public function showAction()
	{
	  include_once('geshi/geshi.php');
	  $this->view->project = $this->_project;
    $config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
	  $project_name = str_replace(USVN_URL_SEP, '/',$this->_project->name);
    $svn_file_path = $this->getRequest()->getParam('file');
    $this->view->path = $svn_file_path;
    $local_file_path = USVN_SVNUtils::getRepositoryPath($config->subversion->path."/svn/".$project_name."/".$svn_file_path);
    $file_ext = pathinfo($svn_file_path, PATHINFO_EXTENSION);
		$revision = $this->getRequest()->getParam('rev');
		$file_rev = '';
		if (!empty($revision)) {
			if (is_numeric($revision) && $revision > 0) {
				$cmd = USVN_SVNUtils::svnCommand("log --non-interactive --revision $revision --quiet $local_file_path");
				$verif = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
				if (!$return) {
					$this->view->revision = $revision;
					$file_rev = '--revision '.$revision;
				}
			}
		}
		if (empty($file_rev)) {
			$cmd = USVN_SVNUtils::svnCommand("info $local_file_path");
			$infos = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
			if (preg_match_all('#^([^:]+): (.*)$#m', $infos, $tmp)) {
				$infos = array();
				foreach ($tmp[1] as $k => $v) {
					$infos[$v] = $tmp[2][$k];
				}
				$this->view->revision = $infos['Last Changed Rev'];
			}
		}
		$cmd = USVN_SVNUtils::svnCommand("log --non-interactive --quiet $local_file_path");
		$revs = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
		if (preg_match_all('#^r([0-9]+) \|#m', $revs, $tmp)) {
			$revs = array();
			$this->view->prev_revision = NULL;
			$this->view->next_revision = NULL;
			foreach ($tmp[1] as $k => $rev) {
				if ($this->view->prev_revision === NULL && $rev < $this->view->revision) {
					$this->view->prev_revision = $rev;
				}
				if ($rev > $this->view->revision) {
					$this->view->next_revision = $rev;
				}
				$revs[] = $rev;
			}
			$this->view->select_revisions = $revs;
		}
    $cmd = USVN_SVNUtils::svnCommand("cat --non-interactive $file_rev $local_file_path");
    $source = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
    if ($return) {
      throw new USVN_Exception(T_("Can't read from subversion repository.\nCommand:\n%s\n\nError:\n%s"), $cmd, $message);
		} else {
			if ($this->getRequest()->getParam('rev') !== NULL) {
				$this->view->color_view = $this->getRequest()->getParam('color');
				$this->view->diff_view = $this->getRequest()->getParam('diff');
			} else {
				$this->view->color_view = 1;
			}
      $geshi = new Geshi();
      $lang_name = $geshi->get_language_name_from_extension($file_ext);
      $this->view->language = $lang_name;
      $geshi->set_language(($this->view->color_view ? $lang_name : NULL), true);
			if (!$this->view->diff_view) {
      	$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
			}	elseif ($this->view->prev_revision) {
				$cmd = USVN_SVNUtils::svnCommand("diff --non-interactive --revision {$this->view->prev_revision}:{$this->view->revision} $local_file_path");
		    $diff = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe($cmd, $return);
				if (!$return) {
					$new_source = array();
					$source = explode("\n", $source);
					$diff = explode("\n", $diff);
					$source_line = NULL;
					$count_line = 0;
					$diff_lines = array();
					while (($line = array_shift($diff)) !== NULL) {
						if (preg_match('#^@@ \-[0-9,]+ \+([0-9]+),[0-9]+ @@$#', $line, $tmp)) {
							if ($source_line === NULL) {
								$source_line = 1;
							}
							while ($source_line < $tmp[1]) {
								array_push($new_source, array_shift($source));
								$source_line++;
								$count_line++;
							}
							continue;
						}
						if ($source_line !== NULL) {
							$diff_char = substr($line, 0, 1);
							if ($diff_char == '-') {
								array_push($new_source, substr($line, 1));
								$diff_lines[$count_line] = '-';
							}
							else {
								if ($diff_char == '+') {
									$diff_lines[$count_line] = '+';
								}
								array_push($new_source, array_shift($source));
								$source_line++;
							}
							$count_line++;
						}
					}
					$new_source = array_merge($new_source, $source);
					$source = implode("\n", $new_source);
					unset($new_source);
					$this->view->diff_lines = $diff_lines;
				}
			}
			$geshi->set_source($source);
      $geshi->set_header_type(GESHI_HEADER_DIV);
      $this->view->highlighted_source = $geshi->parse_code();
			if ($this->view->diff_view) {
				$tab_source = explode("\n", $this->view->highlighted_source);
			}
    }
	}
}
