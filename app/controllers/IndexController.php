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
		
		$projects = new USVN_Db_Table_Projects();
		if ($this->_request->getParam('folder') != null && $this->_request->getParam('folder') != USVN_DIRECTORY_SEPARATOR) {
			$folder = str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR, $this->_request->getParam('folder'));
			$i = strripos(substr($folder, 0, -1), USVN_DIRECTORY_SEPARATOR, 2);
			$this->view->prev = ($i === false ? '' : substr($folder, 0, $i + 1));
		} else {
			$folder = null;
		}
		$this->view->prefix = str_replace(USVN_DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $folder);
		$this->view->projects = $this->displayDirectories($projects->fetchAllAssignedTo($this->getRequest()->getParam('user'), $folder), $folder);
		
		if ($this->_request->getParam('grpfolder') != null && $this->_request->getParam('grpfolder') != USVN_DIRECTORY_SEPARATOR) {
			$grpfolder = str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR, $this->_request->getParam('grpfolder'));
			$i = strripos(substr($grpfolder, 0, -1), USVN_DIRECTORY_SEPARATOR, 2);
			$this->view->grpprev = ($i === false ? '' : substr($grpfolder, 0, $i + 1));
		} else {
			$grpfolder = null;
		}
		$this->view->grpprefix = str_replace(USVN_DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $grpfolder);
		$this->view->groups = $this->displayDirectories($user->listGroups($grpfolder), $grpfolder);
		$this->view->maxlen = 12;
	}

	public function errorAction()
	{
	}
	
	private function displayDirectories($prtgrps, $folder)
	{
		$tmp_projects = array();
		$tmp_folders = array();
		foreach ($prtgrps as $prtgrp) {
			$tmp_project = substr($prtgrp->name, strlen($folder));
			if (strstr($tmp_project, USVN_DIRECTORY_SEPARATOR) === false) {
				$tmp_projects[$tmp_project] = $prtgrp->description;
			} elseif (preg_match('#^([^'.USVN_DIRECTORY_SEPARATOR.']+['.USVN_DIRECTORY_SEPARATOR.']).*#', $tmp_project, $tmp)) {
				$tmp_project = str_replace(USVN_DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $tmp[1]);
				if (!in_array($tmp_project, $tmp_folders)) {
					$tmp_folders[$tmp_project] = '';
				}
			}
		}
		ksort($tmp_folders);
		ksort($tmp_projects);
		return array_merge($tmp_folders, $tmp_projects);
	}
}
