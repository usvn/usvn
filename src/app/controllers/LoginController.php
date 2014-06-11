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
 * $Id: LoginController.php 1188 2007-10-06 12:03:17Z crivis_s $
 */

class LoginController extends USVN_Controller
{
	const IgnoreLogin = true;

	public function loginAction()
	{
		// Check for an existing identity
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity())
			$this->_redirect('/' . $this->getRequest()->getParam('path'));

		$this->view->request = $this->getRequest();

		// Check the authentication
		if (!empty($_POST))
			$this->_doLogin();
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
		$auth = Zend_Auth::getInstance();

		// Find the authentication adapter from the config file
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, 'general');
		$authAdapterMethod = "database"; // Default method is databse
		if (empty($config->alwaysUseDatabaseForLogin))
		{
			$config->alwaysUseDatabaseForLogin = 'admin';
		}
		if ($config->alwaysUseDatabaseForLogin != $_POST['login'] && $config->authAdapterMethod)
		{
			$authAdapterMethod = strtolower($config->authAdapterMethod);
		}
		$authAdapterClass = 'USVN_Auth_Adapter_' . ucfirst($authAdapterMethod);
		if (!class_exists($authAdapterClass))
		{
			throw new USVN_Exception(T_('The authentication adapter method set in the config file is not valid.'));
		}
		// Retrieve auth-options, if any, from the config file
		$authOptions = null;
		if ($config->$authAdapterMethod && $config->$authAdapterMethod->options)
		{
			$authOptions = $config->$authAdapterMethod->options->toArray();
		}
		// Set up the authentication adapter
		$authAdapter = new $authAdapterClass($_POST['login'], $_POST['password'], $authOptions);
		
		// Attempt authentication, saving the result
		$result = $auth->authenticate($authAdapter);

		if (!$result->isValid())
		{
			$this->view->login = $_POST['login'];
			$this->view->messages = $result->getMessages();
			$this->render('errors');
			$this->render('login');
		}
		else
		{
			$identity = $auth->getStorage()->read();
			/**
			 * Workaround for LDAP. We need the identity to match the database,
			 * but LDAP identities can be in the following form:
			 * uid=username,ou=people,dc=foo,dc=com
			 * We need to simply keep username, as passed to the constructor method.
			 *
			 * Using in_array(..., get_class_methods()) instead of method_exists() or is_callable(),
			 * because none of them really check if the method is actually callable (ie. not protected/private).
			 * See comments @ http://us.php.net/manual/en/function.method-exists.php
			*/
			if (in_array("getIdentityUserName", get_class_methods($authAdapter)))
			{
				// Because USVN uses an array (...) when Zend uses a string
				if (!is_array($identity))
				{
					$identity = array();
				}
				$identity['username'] = $authAdapter->getIdentityUserName();
				$auth->getStorage()->write($identity);
			}
			/**
			 * Another workaround for LDAP. As long as we don't provide real
			 * and full LDAP support (add, remove, etc.), if a user managed to
			 * log in with LDAP, or any other non-DB support, we need to add
			 * the user in the database :)
			*/
			if ($config->$authAdapterMethod->createUserInDBOnLogin)
			{
				$table = new USVN_Db_Table_Users();
				$user = $table->fetchRow(array("users_login = ?" => $identity['username']));
				// Not very sure if we need to ask the authAdapter if we need to
				// create user in DB, as it is redundant with the config...
				if (!$user && in_array("createUserInDB", get_class_methods($authAdapter)) && $authAdapter->createUserInDB())
				{
					$data = array(
						'users_login' => $identity['username'],
						'users_is_admin' => 0,
						'users_password' => $_POST['password']
					);
					
					/* Request firstname, lastname, and username if possible (e.g., can be read from LDAP) */
					$authAdapterClassMethods = get_class_methods($authAdapter);
					if (in_array('getFirstName', $authAdapterClassMethods))
					{
						$data['users_firstname'] = $authAdapter->getFirstName();
					};
					if (in_array('getLastName', $authAdapterClassMethods))
					{
						$data['users_lastname'] = $authAdapter->getLastName();
					};			
					if (in_array('getEmail', $authAdapterClassMethods))
					{
						$data['users_email'] = $authAdapter->getEmail();
					};
					
					$user = USVN_User::create($data, $config->$authAdapterMethod->createGroupForUserInDB, null);
					$user->save();
				}
			}
			$this->_redirect('/' . $this->getRequest()->getParam('path'));
			exit(0);
		}
	}
}
