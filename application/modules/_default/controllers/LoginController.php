<?php

class LoginController extends IndexController 
{
	public function loginAction()
	{
		if (!empty($_POST)) {
			$this->_doLogin();
		}
		$this->_render();
	}
	
	public function logoutAction()
	{
		$this->_redirect('/');
	}
	
	protected function _doLogin()
	{
	}
}