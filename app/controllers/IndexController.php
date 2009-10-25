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
		$projects = new USVN_Db_Table_Projects();
		if ($this->_request->getParam('folder') != null) {
			$folder = str_replace(USVN_URL_SEP, '/', $this->_request->getParam('folder'));
			$i = strripos(substr($folder, 0, -1), '/', 2);
			$this->view->prev = ($i === false ? '' : substr($folder, 0, $i + 1));
			$projects = $projects->fetchAllAssignedTo($this->getRequest()->getParam('user'), $folder);
		} else {
			$folder = '';
			$projects = $projects->fetchAllAssignedTo($this->getRequest()->getParam('user'));
		}
		$this->view->prefix = $folder;
		$tmp_projects = array();
		$tmp_folders = array();
		foreach ($projects as $project) {
			$tmp_project = substr($project->name, strlen($folder));
			if (strstr($tmp_project, '/') === false) {
				$tmp_projects[] = $tmp_project;
			} elseif (preg_match('#^([^/]+/).*#', $tmp_project, $tmp) && !in_array($tmp[1], $tmp_folders)) {
				$tmp_folders[] = $tmp[1];
			}
		}
		sort($tmp_folders);
		sort($tmp_projects);
		$this->view->projects = array_merge($tmp_folders, $tmp_projects);
		
		$identity = Zend_Auth::getInstance()->getIdentity();
		$user_table = new USVN_Db_Table_Users();
		$user = $user_table->fetchRow(array('users_login = ?' => $identity['username']));
		$this->view->groups = $user->listGroups();
		$this->view->maxlen = 12;
	}

	public function errorAction()
	{
	}
}
