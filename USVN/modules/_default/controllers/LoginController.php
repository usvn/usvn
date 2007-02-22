<?php

class LoginController extends IndexController
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
		
		/**
		 * Render the template form
		 */
		$this->_render();
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
			$this->_view->messages = $result->getMessages();
		} else {
			$this->_redirect("/");
		}
	}
}