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

require_once 'USVN/modules/admin/controllers/IndexController.php';

class admin_UserController extends admin_IndexController
{
	public static function getUserDataFromPost()
	{
		$user = array('users_login'			=> $_POST['users_login'],
						'users_lastname'	=> $_POST['users_lastname'],
						'users_firstname'	=> $_POST['users_firstname'],
						'users_email'		=> $_POST['users_email']
						);
		if (isset($_POST['users_id']) && !empty($_POST['users_id'])) {
			$user['users_id'] = $_POST['users_id'];
		}
		if (isset($_POST['users_password'])) {
			$user['users_password']	= $_POST['users_password'];
		}
		return $user;
	}

	public function indexAction()
    {
    	$table = new USVN_Db_Table_Users();
    	$table->isAUser('anonymous');
		$this->_view->users = $table->fetchAll(null, "users_login");
		$this->_render('index.html');
    }

	public function newAction()
	{
		if (!empty($_POST)) {
			if (!empty($_POST['users_password']) && !empty($_POST['users_password2'])
				&& $_POST['users_password'] !== $_POST['users_password2']) {
					throw new Exception(T_('Not the same password.'));
			}
			$data = admin_UserController::getUserDataFromPost();
			$this->save("USVN_Db_Table_Users", "user", $data);
		}
		$this->_render('form.html');
	}

	public function editAction()
	{
		if (!empty($_POST)) {
			if (!empty($_POST['users_password']) && !empty($_POST['users_password2'])
				&& $_POST['users_password'] !== $_POST['users_password2']) {
					throw new Exception(T_('Not the same password.'));
			} elseif(empty($_POST['users_password']) && empty($_POST['users_password2'])) {
				unset($_POST['users_password']);
			}
			$data = admin_UserController::getUserDataFromPost();
			$this->save("USVN_Db_Table_Users", "user", $data);
		} else {
			$userTable = new USVN_Db_Table_Users();
			$this->_view->user = $userTable->fetchRow(array('users_login = ?' => $this->getRequest()->getParam('login')));
		}
		$this->_render('form.html');
		$this->_render('../group/attachment.html');
	}

	public function editProfileAction()
	{
		$userTable = new USVN_Db_Table_Users();
		$identity = Zend_Auth::getInstance()->getIdentity();
		if (!empty($_POST)) {
			$_POST['users_login'] = $identity["username"];
			if (!empty($_POST['users_password']) && !empty($_POST['users_password2'])
				&& $_POST['users_password'] !== $_POST['users_password2']) {
					throw new Exception(T_('Not the same password.'));
			} elseif (empty($_POST['users_password']) && empty($_POST['users_password2'])) {
				unset($_POST['users_password']);
			}
			$data = admin_UserController::getUserDataFromPost();
			$this->save("USVN_Db_Table_Users", "user", $data);
		} else {
			$this->_view->user = $userTable->fetchRow(array('users_login = ?' => $identity["username"]));
		}
		$this->_render('profile.html');
	}

	public function importAction()
	{
		$this->_render('import.html');
	}

	public function importFileAction()
	{
		new USVN_modules_admin_models_ImportHtpasswd($_FILES['file']['tmp_name']);
        $this->_redirect('/admin/user');
	}

	public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
