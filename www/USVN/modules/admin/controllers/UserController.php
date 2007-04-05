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
			if (!empty($_POST['users_password']) && !empty($_POST['users_password'])
				&& $_POST['users_password'] !== $_POST['users_password2']) {
					throw new Exception(T_('Not the same password.'));
			}
			$this->save("USVN_Db_Table_Users", "user");
		}
		$this->_render('form.html');
	}

	public function editAction()
	{
		if (!empty($_POST)) {
			if (!empty($_POST['users_password']) && !empty($_POST['users_password'])
				&& $_POST['users_password'] !== $_POST['users_password2']) {
					throw new Exception(T_('Not the same password.'));
			}
			$this->save("USVN_Db_Table_Users", "user");
		} else {
			$userTable = new USVN_Db_Table_Users();
			$this->_view->user = $userTable->fetchRow(array('users_id = ?' => $this->getRequest()->getParam('id')));
		}
		$this->_render('form.html');
	}

	public function editProfileAction()
	{
		if (!empty($_POST)) {
			if (!empty($_POST['users_login'])) {
				unset($_POST['users_login']);
			}
			if (!empty($_POST['users_password']) && !empty($_POST['users_password'])
				&& $_POST['users_password'] !== $_POST['users_password2']) {
					throw new Exception(T_('Not the same password.'));
			}
			$this->save("USVN_Db_Table_Users", "user");
		} else {
			$userTable = new USVN_Db_Table_Users();
			$identity = Zend_Auth::getInstance()->getIdentity();
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
