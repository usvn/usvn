<?php
/**
 * Controller for login into default module
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
 * $Id$
 */

class LoginController extends USVN_Controller
{
	public function loginAction()
	{
		/**
		 * Check for an existing identity
		 */
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$this->_redirect('/');
		}

		/**
		 * Check the authentication
		 */
		if (!empty($_POST)) {
			$this->_doLogin();
		}
	}

	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::destroy();
		$this->_redirect('/');
	}

	protected function _doLogin()
	{
		// Get a reference to the Singleton instance of Zend_Auth
		require_once 'Zend/Auth.php';
		$auth = Zend_Auth::getInstance();

		// Set up the authentication adapter
		$authAdapter = new USVN_Auth_Adapter_Db($_POST['login'], $_POST['password']);

		// Attempt authentication, saving the result
		$result = $auth->authenticate($authAdapter);

		if (!$result->isValid()) {
			$this->view->login = $_POST['login'];
			$this->view->messages = $result->getMessages();
			$this->render('errors');
			$this->render('login');
		} else {
			$this->_redirect("/");
		}
	}
}
