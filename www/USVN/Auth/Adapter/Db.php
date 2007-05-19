<?php
/**
 * Auth an user from the Database
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package auth
 * @subpackage db
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

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

		$table = new USVN_Db_Table_Users();
		$user = $table->fetchRow(array('users_login = ?' => $this->_login));

		if ($user === NULL) {
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
