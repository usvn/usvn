<?php
/**
 * Group management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage group
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This group has been realised as part of
 * end of studies group.
 *
 * $Id$
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AdminadminController.php';

class GroupadminController extends AdminadminController
{
	public static function getGroupData($data)
	{
		if (!isset($data["groups_name"]) || !isset($data["groups_description"])) {
			return array();
		}
		$group = array();
		$group["groups_name"] = $data['prefix'].DIRECTORY_SEPARATOR.$data["groups_name"];
		$group["groups_description"] = $data["groups_description"];
		return $group;
	}

	public function indexAction()
	{
		$table = new USVN_Db_Table_Groups();
		$tablep = new USVN_Db_Table_Projects();
		if ($this->_request->getParam('folder') != null) {
			$folder = str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR, $this->_request->getParam('folder'));
			$i = strripos(substr($folder, 0, -1), USVN_DIRECTORY_SEPARATOR, 2);
			$this->view->prev = ($i === false ? '' : substr($folder, 0, $i));
		} else {
			$folder = '';
		}
		$this->view->prev = $this->modifName($this->view->prev, -1);
		$this->view->prefix = $this->modifName($folder ? $folder.USVN_DIRECTORY_SEPARATOR : '', -1);
		$groups = $table->fetchAll("groups_name LIKE '{$folder}%'", "groups_name");
		$projects = $tablep->fetchAll("projects_name LIKE '{$folder}%'", "projects_name");
		$this->view->groups = $this->displayDirectories($groups, $projects, $folder);
	}

	public function newAction()
	{
		$table = new USVN_Db_Table_Groups();
		$this->view->group = $table->createRow();
		$table = new USVN_Db_Table_Users();
		$this->view->users = $table->fetchAll(null, "users_login");
		$folder = str_replace(USVN_URL_SEP, DIRECTORY_SEPARATOR, $this->_request->getParam('folder'));
		$this->view->prefix = $folder;
	}

	public function createAction()
	{
		$data = $this->getGroupData($_POST);
		if (empty($data)) {
			$this->_redirect("/admin/group/new");
		}
		$table = new USVN_Db_Table_Groups();
		$data['groups_name'] = $this->modifName($data['groups_name'], 1);
		$group = $table->createRow($data);
		$this->view->group = $group;
		if ($table->isAGroup($data['groups_name'])) {
			$this->view->message = sprintf(T_("Group %s already exist"), $data['groups_name']);
			$this->render('new');
			return;
		}
		try {
			$group->save();
			foreach ($_POST['users'] as $user) {
				$group->addUser($user);
			}
			$this->_redirect("/admin/group/");
		}
		catch (USVN_Exception $e) {
			$this->view->message = $e->getMessage();
			$table = new USVN_Db_Table_Users();
			$this->view->users = $table->fetchAll(null, "users_login");
			$data['groups_name'] = $this->modifName($data['groups_name'], -1);
			$this->render('new');
		}
	}

	public function editAction()
	{
		$group_name = str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR, $this->getRequest()->getParam('name'));
		$table = new USVN_Db_Table_Groups();
		$this->view->group = $table->fetchRow(array('groups_name = ?' => $group_name));
		$this->view->group->name = $this->modifName($this->view->group->name, -1);
		if ($this->view->group === null) {
			throw new USVN_Exception(T_("Invalid group %s."), $group_name);
		}
		$table = new USVN_Db_Table_Users();
		$this->view->users = $table->fetchAll(null, "users_login");
	}

	public function updateAction()
	{
		$data = $this->getGroupData($_POST);
		if (empty($data)) {
			$this->_redirect("/admin/group/");
		}
		$data['groups_name'] = $this->modifName($data['groups_name'], 1);
		
		$table = new USVN_Db_Table_Groups();
		$group = $table->fetchRow(array("groups_name = ?" => $data['groups_name']));
		if ($group === null) {
			throw new USVN_Exception(T_("Invalid group %s."), $data['groups_name']);
		}
		$group->setFromArray($data);
		try {
			$group->save();
			$this->_redirect("/admin/group/");
		}
		catch (USVN_Exception $e) {
			$this->view->group = $group;
			$this->view->message = $e->getMessage();

			$table = new USVN_Db_Table_Users();
			$this->view->users = $table->fetchAll(null, "users_login");

			$this->render('edit');
		}
	}

	public function deleteAction()
	{
		$table = new USVN_Db_Table_Groups();
		$group_name = str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR, $this->getRequest()->getParam('name'));
		$group = $table->fetchRow(array('groups_name = ?' => $group_name));
		if ($group === null) {
			throw new USVN_Exception(T_("Invalid group %s."), $group_name);
		}
		$group->delete();
		$this->_redirect("/admin/group/");
	}
	
	private function displayDirectories($groups, $projects, $folder)
	{
		$tmp_folders = array();
		foreach ($projects as $project) {
			if (!$folder) {
				$tmp_project = $project->name;
			} elseif (substr($project->name, strlen($folder), 1) == USVN_DIRECTORY_SEPARATOR) {
				$tmp_project = substr($project->name, strlen($folder) + 1);
			}
			if (!empty($tmp_project) && strstr($tmp_project, USVN_DIRECTORY_SEPARATOR) === false) {
				if ($project->folder) {
					$tmp_folders[$tmp_project] = $project;
				}
			}
		}
		$tmp_projects = array();
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
