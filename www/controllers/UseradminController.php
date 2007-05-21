<?php
/**
 * User management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage users
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AdminadminController.php';

class UseradminController extends AdminadminController
{
	protected function getUserData($data)
	{
		if (!isset($data['users_login'])
			|| !isset($data['users_lastname'])
			|| !isset($data['users_firstname'])
			|| !isset($data['users_email'])
			|| !isset($data['users_password'])
			|| !isset($data['users_is_admin'])
			) {
			return array();
		}
		$user = array('users_login'     => $data['users_login'],
					  'users_lastname'  => $data['users_lastname'],
					  'users_firstname' => $data['users_firstname'],
					  'users_email'     => $data['users_email'],
					  'users_is_admin'  => $data['users_is_admin'],
						);
		if (!empty($_POST['users_password']) && !empty($_POST['users_password2'])) {
			if ($_POST['users_password'] !== $_POST['users_password2']) {
				throw new Exception(T_('Not the same password.'));
			}
			$user['users_password'] = $data['users_password'];
		}
		return $user;
	}

	public function indexAction()
    {
    	$table = new USVN_Db_Table_Users();
		$this->_view->users = $table->fetchAll(null, "users_login");
		$this->_render();
    }

	public function newAction()
	{
		$table = new USVN_Db_Table_Users();
		$this->_view->user = $table->createRow();
		$this->_render();
	}

	public function createAction()
	{
		$data = $this->getUserData($_POST);
		if (empty($data)) {
			$this->_redirect("/admin/user/new");
		}
		$table = new USVN_Db_Table_Users();
		$user = $table->createRow($data);
		try {
			$user->save();
			$this->_redirect("/admin/user/");
		}
		catch (Exception $e) {
			$this->_view->user = $user;
			$this->_view->message = $e->getMessage();
			$this->_render('new.html');
		}
	}

	public function editAction()
	{
		$table = new USVN_Db_Table_Users();
		$this->_view->user = $table->fetchRow(array('users_login = ?' => $this->getRequest()->getParam('login')));
		if ($this->_view->user === null) {
			$this->_redirect("/admin/user/");
		}
		$this->_render();
	}

	public function updateAction()
	{
		$data = $this->getUserData($_POST);
		if (empty($data)) {
			$this->_redirect("/admin/user/new");
		}
		$table = new USVN_Db_Table_Users();
		$user = $table->fetchRow(array("users_login = ?" => $this->getRequest()->getParam('login')));
		if ($user === null) {
			$this->_redirect("/admin/user/");
		}
		$user->setFromArray($data);
		try {
			$user->save();
			$this->_redirect("/admin/user/");
		}
		catch (Exception $e) {
			$this->_view->user = $user;
			$this->_view->message = $e->getMessage();
			$this->_render('edit.html');
		}
	}

	public function deleteAction()
	{
		$table = new USVN_Db_Table_Users();
		$user = $table->fetchRow(array('users_login = ?' => $this->getRequest()->getParam('login')));
		if ($user === null || $user->login == $this->getRequest()->getParam('user')->login) {
			$this->_redirect("/admin/user/");
		}
		$user->delete();
		$this->_redirect("/admin/user/");
	}



	public function attachGroupsAction()
	{
		if (!empty($_POST) && $_POST['groups_id']) {
			$userTable = new USVN_Db_Table_Users();
			$user = $userTable->fetchRow(array('users_id = ?' => $_POST['user_id']));
			$user->deleteAllGroups();
			foreach ($_POST['groups_id'] as $group_id) {
				$user->addGroup($group_id);
			}
		}
		$userTable = new USVN_Db_Table_Users();
		$user = $userTable->fetchRow(array('users_login = ?' => $this->getRequest()->getParam('login')));
		$this->_view->user_id = $user->id;

		$userRow = $user->findManyToManyRowset('USVN_Db_Table_Groups', 'USVN_Db_Table_UsersToGroups');
		$this->_view->attachedGroups = $userRow->toArray();

		$groupsTable = new USVN_Db_Table_Groups();
		$where = $groupsTable->getAdapter()->quoteInto('groups_name NOT IN (?)', $groupsTable->exceptedEntries['groups_name']);
		$this->_view->allGroups = $groupsTable->fetchAll($where, "groups_name");
		$this->_render('../group/attachment.html');
	}

	public function importAction()
	{
		$this->_render('import.html');
	}

	public function importFileAction()
	{
		new USVN_ImportHtpasswd($_FILES['file']['tmp_name']);
        $this->_redirect('/admin/user');
	}
}
