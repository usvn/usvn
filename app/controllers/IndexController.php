<?php
/**
 * Main controller
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package default
 * @subpackage controller
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: IndexController.php 1250 2007-10-21 15:17:24Z crivis_s $
 */
class IndexController extends USVN_Controller {
	/**
	 * Default action for every controller.
	 *
	 */
	public function indexAction() {
		$identity = Zend_Auth::getInstance()->getIdentity();
		$user_table = new USVN_Db_Table_Users();
		$user = $user_table->fetchRow(array('users_login = ?' => $identity['username']));
		
		$table = new USVN_Db_Table_Projects();
		if ($this->_request->getParam('folder') != null) {
			$folder = str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR, $this->_request->getParam('folder'));
			$i = strripos(substr($folder, 0, -1), USVN_DIRECTORY_SEPARATOR, 2);
			$this->view->prev = ($i === false ? '' : substr($folder, 0, $i));
		} else {
			$folder = '';
		}
		$this->view->prev = $this->modifName($this->view->prev, -1);
		$this->view->prefix = $this->modifName($folder ? $folder.USVN_DIRECTORY_SEPARATOR : '', -1);
		$this->view->projects = $this->displayDirectories(array(), $table->fetchAllAssignedTo($this->getRequest()->getParam('user'), $folder), $folder);
		
		$projects = $table->fetchAll("projects_name LIKE '{$folder}%'", "projects_name");
		$this->view->groups = $this->displayDirectories($user->listGroups($folder), array(), $folder);
		
		$this->view->maxlen = 12;
	}

	public function errorAction()
	{
	}
	
	private function displayDirectories($groups, $projects, $folder)
	{
		$tmp_folders = array();
		$tmp_projects = array();
		foreach ($projects as $project) {
			if (!$folder) {
				$tmp_project = $project->name;
			} elseif (substr($project->name, strlen($folder), 1) == USVN_DIRECTORY_SEPARATOR) {
				$tmp_project = substr($project->name, strlen($folder) + 1);
			}
			if (!empty($tmp_project) && strstr($tmp_project, USVN_DIRECTORY_SEPARATOR) === false) {
				if ($project->folder) {
					$tmp_folders[$tmp_project] = $project;
				} else {
					$tmp_projects[$tmp_project] = $project;
				}
			}
		}
		foreach ($groups as $group) {
			if (!$folder) {
				$tmp_project = $group->name;
			} elseif (substr($group->name, strlen($folder), 1) == USVN_DIRECTORY_SEPARATOR) {
				$tmp_project = substr($group->name, strlen($folder) + 1);
			}
			if (!empty($tmp_project) && strstr($tmp_project, USVN_DIRECTORY_SEPARATOR) === false) {
				$tmp_projects[$tmp_project] = $group;
			}
		}
		ksort($tmp_folders);
		ksort($tmp_projects);
		return $tmp_folders + $tmp_projects;
	}
	
	private function modifName($name, $inout)
	{
		if ($inout > 0) {
			$name = str_replace(USVN_DIRECTORY_SEPARATOR, '#', $name);
			$name = str_replace(DIRECTORY_SEPARATOR, USVN_DIRECTORY_SEPARATOR, $name);
		} elseif ($inout < 0) {
			$name = str_replace(USVN_DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $name);
			$name = str_replace('#', USVN_DIRECTORY_SEPARATOR, $name);
		}
		return $name;
	}
}
