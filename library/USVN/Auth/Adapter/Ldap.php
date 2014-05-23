<?php
/**
 * Authenticate a user from LDAP
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2009, Team USVN
 * @since 0.8
 * @package auth
 * @subpackage ldap
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 */

class USVN_Auth_Adapter_Ldap extends Zend_Auth_Adapter_Ldap
{
	private $_identityUserName;
	
	private $_user_data;

	/**
	 * Sets username and password for authentication
	 *
	 * @return void
	 */
	public function __construct($username, $password, array $arrayOfOptions = array())
	{
		$this->_identityUserName = $username;
		try
		{
			if (!is_array($arrayOfOptions))
			{
				throw new USVN_Exception(T_("LDAP options must be an array!"));
			}
			if (!isset($arrayOfOptions[0]) || !is_array($arrayOfOptions[0]))
			{
				$arrayOfOptions = array($arrayOfOptions);
			}
			foreach ($arrayOfOptions as &$options)
			{
				if (array_key_exists('bindDnFormat', $options))
				{
					$username = sprintf($options['bindDnFormat'], $username);
					unset($options['bindDnFormat']);
				}
			}
			parent::__construct($arrayOfOptions, $username, $password);
			
			$_user_data = $this->getAccountObject(array('givenName','sn','mail'), array());
		}
		catch (Exception $e)
		{
			throw new USVN_Exception($e->getMessage());
		}
	}

	public function getIdentityUserName()
	{
		return $this->_identityUserName;
	}

	public function createUserInDB()
	{
		return true;
	}
	
	public function getEmail() 
	{
		$this->_user_data['mail'];
	}
	
	public function getFirstName()
	{
		$this->_user_data['givenName'];
	}
	
	public function getLastName()
	{
		$this->_user_data['sn'];
	}
	
}
