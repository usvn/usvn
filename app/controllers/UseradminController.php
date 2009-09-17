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
		|| !isset($data['users_password2'])
		|| !isset($data['users_is_admin'])
		) {
			throw new USVN_Exception(T_('Missing fields.'));
		}
		$user = array('users_login'     => $data['users_login'],
		'users_lastname'  => $data['users_lastname'],
		'users_firstname' => $data['users_firstname'],
		'users_email'     => $data['users_email'],
		'users_is_admin'  => $data['users_is_admin'],
		);

		if (!empty($_POST['users_password']) && !empty($_POST['users_password2'])) {
			if ($_POST['users_password'] !== $_POST['users_password2']) {
				throw new USVN_Exception(T_('Not the same password.'));
			}
			$user['users_password'] = $data['users_password'];
		}
		return $user;
	}

	public function indexAction()
	{
		$table = new USVN_Db_Table_Users();
		$this->view->users = $table->fetchAll(null, "users_login");
	}

	public function newAction()
	{
		$table = new USVN_Db_Table_Users();
		$this->view->user = $table->createRow();
		$table = new USVN_Db_Table_Groups();
		$this->view->groups = $table->fetchAll(null, 'groups_name');
	}

	public function createAction()
	{
		try {
			$data = $this->getUserData($_POST);
		}
		catch (Exception $e) {
			$this->view->user = USVN_User::create($_POST, false, false)->getRowObject();
			$this->view->message = $e->getMessage();
			$table = new USVN_Db_Table_Groups();
			$this->view->groups = $table->fetchAll(null, 'groups_name');
			$this->render('new');
			return ;
		}
		$user = USVN_User::create($data,
									isset($_POST['create_group']) ? $_POST['create_group'] : false,
									isset($_POST['groups']) ? $_POST['groups'] : null);
		try {
			$user->save();
			$this->_redirect("/admin/user/");
		}
		catch (USVN_Exception $e) {
			$this->view->user = $user->getRowObject();
			$this->view->message = $e->getMessage();
			$table = new USVN_Db_Table_Groups();
			$this->view->groups = $table->fetchAll(null, 'groups_name');
			$this->render('new');
		}
	}

	public function editAction()
	{
		$table = new USVN_Db_Table_Users();
		$this->view->user = $table->fetchRow(array('users_login = ?' => $this->getRequest()->getParam('login')));
		if ($this->view->user === null) {
			throw new USVN_Exception(T_("Invalid user %s."), $this->getRequest()->getParam('login'));
		}
		$table = new USVN_Db_Table_Groups();
		$this->view->groups = $table->fetchAll(null, 'groups_name');
	}

	public function updateAction()
	{
		$data = $this->getUserData($_POST);
		if (empty($data)) {
			$this->_redirect("/admin/user/new");
		}
		$user = null; /* some ugly hack of variable scope... */
		try {
			$table = new USVN_Db_Table_Users();
			$login = $this->getRequest()->getParam('login');
			$user = $table->fetchRow(array("users_login = ?" => $login));
			if ($user === null) {
				throw new USVN_Exception(sprintf(T_("User %s does not exist."), $login));
			}
			$user->setFromArray($data);
			$u = USVN_User::update($user,
										$data,
										isset($_POST['groups']) ? $_POST['groups'] : null);
			$u->save();
			$this->_redirect("/admin/user/");
		}
		catch (USVN_Exception $e) {
			$this->view->user = $user;
			$this->view->message = $e->getMessage();
			$table = new USVN_Db_Table_Groups();
			$this->view->groups = $table->fetchAll(null, 'groups_name');
			$this->render('edit');
		}
	}

	public function deleteAction()
	{
		$table = new USVN_Db_Table_Users();
		$user = $table->fetchRow(array('users_login = ?' => $this->getRequest()->getParam('login')));
		if ($user === null) {
			throw new USVN_Exception(T_("Invalid user %s."), $this->getRequest()->getParam('login'));
		}
		if ($user->login == $this->getRequest()->getParam('user')->login) {
			throw new USVN_Exception(T_("You can't delete yourself."));
		}
		$user->delete();
		$this->_redirect("/admin/user/");
	}

	public function importAction()
	{
		$this->render('import');
	}

	public function importFileAction()
	{
		try {
			new USVN_ImportHtpasswd($_FILES['file']['tmp_name']);
		}
		catch (USVN_Exception $e) {
			$this->view->message = $e->getMessage();
			$this->render('import');
			return;
		}	
		$this->_redirect('/admin/user');
	}
}
