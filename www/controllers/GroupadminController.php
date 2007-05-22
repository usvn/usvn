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
		$group["groups_name"] = $data["groups_name"];
		$group["groups_description"] = $data["groups_description"];
		return $group;
	}

	public function indexAction()
    {
    	$table = new USVN_Db_Table_Groups();
		$this->_view->groups = $table->fetchAll(null, "groups_name");
		$this->_render();
    }

	public function newAction()
	{
		$table = new USVN_Db_Table_Groups();
		$this->_view->group = $table->createRow();
		$this->_render();
	}

	public function createAction()
	{
		$data = $this->getGroupData($_POST);
		if (empty($data)) {
			$this->_redirect("/admin/group/new");
		}
		$table = new USVN_Db_Table_Groups();
		$group = $table->createRow($data);
		try {
			$group->save();
			$this->_redirect("/admin/group/");
		}
		catch (Exception $e) {
			$this->_view->group = $group;
			$this->_view->message = $e->getMessage();
			$this->_render('new.html');
		}
	}

	public function editAction()
	{
		$table = new USVN_Db_Table_Groups();
		$this->_view->group = $table->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		if ($this->_view->group === null) {
			$this->_redirect("/admin/group/");
		}
		$this->_render();
	}

	public function updateAction()
	{
		$data = $this->getGroupData($_POST);
		if (empty($data)) {
			$this->_redirect("/admin/group/new");
		}
		$table = new USVN_Db_Table_Groups();
		$group = $table->fetchRow(array("groups_name = ?" => $this->getRequest()->getParam('name')));
		if ($group === null) {
			$this->_redirect("/admin/group/");
		}
		$group->setFromArray($data);
		try {
			$group->save();
			$this->_redirect("/admin/group/");
		}
		catch (Exception $e) {
			$this->_view->group = $group;
			$this->_view->message = $e->getMessage();
			$this->_render('edit.html');
		}
	}

	public function deleteAction()
	{
		$table = new USVN_Db_Table_Groups();
		$group = $table->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		if ($group === null) {
			$this->_redirect("/admin/group/");
		}
		$group->delete();
		$this->_redirect("/admin/group/");
	}

	public function attachUsersAction()
	{
		if (!empty($_POST) && $_POST['users_id']) {
			$groupTable = new USVN_Db_Table_Groups();
			$group = $groupTable->fetchRow(array('groups_id = ?' => $_POST['group_id']));
			$group->deleteAllUsers();
			foreach ($_POST['users_id'] as $user_id) {
				$group->addUser($user_id);
			}
		}
		$groupTable = new USVN_Db_Table_Groups();
		$group = $groupTable->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		$this->_view->group_id = $group->id;

		$groupRow = $group->findManyToManyRowset('USVN_Db_Table_Users', 'USVN_Db_Table_UsersToGroups');
		$this->_view->attachedUsers = $groupRow->toArray();

		$usersTable = new USVN_Db_Table_Users();
		$where = $usersTable->getAdapter()->quoteInto('users_login NOT IN (?)', $usersTable->exceptedEntries['users_login']);
		$this->_view->allUsers = $usersTable->fetchAll($where, "users_login");
		$this->_render('../user/attachment.html');
		$this->_forward('attachgroups');
	}

	public function attachgroupsAction()
	{
		if (!empty($_POST) && $_POST['groups_id']) {
			$groupTable = new USVN_Db_Table_Groups();
			$group = $groupTable->fetchRow(array('groups_id = ?' => $_POST['group_id']));
			foreach ($_POST['groups_id'] as $group_id) {
				$group->addgroup($group_id);
			}
		}
		$groupTable = new USVN_Db_Table_Groups();
		$group = $groupTable->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		$this->_view->group_id = $group->id;

		$groupRow = $group->findManyToManyRowset('USVN_Db_Table_groups', 'USVN_Db_Table_Workgroups');
		$this->_view->attachedgroups = $groupRow->toArray();

		$groupsTable = new USVN_Db_Table_groups();
		$where = $groupsTable->getAdapter()->quoteInto('groups_name NOT IN (?)', $groupsTable->exceptedEntries['groups_name']);
		$this->_view->allgroups = $groupsTable->fetchAll($where, "groups_name");
		$this->_render('../group/attachment.html');
	}
}
