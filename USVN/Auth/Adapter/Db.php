<?php

require_once 'Zend/Auth/Adapter/Interface.php';

class USVN_Auth_Adapter_Db implements Zend_Auth_Adapter_Interface {
	protected $_login;
	protected $_password;

	/**
	 * Sets username and password for authentication
	 *
	 * @return void
	 */
	public function __construct($username, $password)
	{
		$this->_login = $username;
		$this->_password = $password;
	}

	/**
	 * Performs an authentication attempt
	 *
	 * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
	 * @return Zend_Auth_Result
	 */
	public function authenticate()
	{
		$result = array();
		$result['isValid'] = false;
		$result['identity'] = array();
		$result['identity']['username'] = $this->_login;
		$result['messages'] = array();

		$table = new USVN_modules_default_models_Users();
		$user = $table->fetchRow(array('user_login = ?' => $this->_login));


		if (! $user->login) {
			$result['messages'][] = sprintf(T_('Login %s not found'), $this->_login);
			return new Zend_Auth_Result($result['isValid'], $result['identity'], $result['messages']);
		}

		if (crypt($this->_password, $user->password) != $user->password) {
			$result['messages'][] = T_('Incorrect password');
			return new Zend_Auth_Result($result['isValid'], $result['identity'], $result['messages']);
		}

		$result['isValid'] = true;
		return new Zend_Auth_Result($result['isValid'], $result['identity'], $result['messages']);
	}
}
