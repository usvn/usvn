<?php

/**
 * Class to provide funcions external integrations
 * 
 * @package USVN (https://github.com/usvn/usvn)
 * @author Bruno Pittlei Gonçalves (scorninpc - https://github.com/scorninpc)
 */
class ServiceController extends Zend_Controller_Action
{
	/**
	 * Store parsed XML
	 */
	protected $_xml;
	
	/**
	 * Store logged user
	 */
	protected $_userRow;
	
	/**
	 * Initialize the service
	 */
	public function init()
	{
		// Disable layout
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
	}
	
	/**
	 * Retrieve project history
	 * 
	 * 	http://usvn.localhost/service/history
	 * 
	 * 	<?xml version="1.0" encoding="UTF-8"?>
	 * 	<usvn>
	 * 		<auth>
	 * 			<username>USERNAME</username>
	 * 			<password>PASSWORD</password>
	 * 		</auth>
	 * 		<project>PROJECT_NAME</project>
	 * </usvn>
	 * 
	 * @todo Add translation for "You don't have permission to access project %s"
	 */
	public function historyAction() {
		// Parse XML request
		$this->_parseRequest();
		
		// Authenticate
		if (!$this->_auth()) {
			throw new Zend_Exception("Authentication failure");
		}
		
		// Get project information
		$project_name = (string)$this->_xml->project;
		$table = new USVN_Db_Table_Projects();
		$project = $table->fetchRow(array("projects_name = ?" => (string)$project_name));
		if(!$project) {
			$this->_outputErrorResponse(sprintf(T_("Invalid project name %s."), $project_name), 10);
		}
		
		// Verify user groups
		$hasGroup = FALSE;
		$groups = $this->_userRow->findManyToManyRowset("USVN_Db_Table_Groups", "USVN_Db_Table_UsersToGroups");
		foreach ($groups as $group) {
			// Verify project groups
			if ($project->groupIsMember($group)) {
				$hasGroup = TRUE;
				break;
			}
		}
		
		// Verify user permission
		if (($this->_userRow['users_is_admin'] === 1) || ($hasGroup === TRUE)) {
			$SVN = new USVN_SVN($project_name);
			$logs = $SVN->log(100);
			
			// Start create XML return
			$dom = new DOMDocument("1.0", "UTF-8");
			$xmlService = $dom->createElement("usvn");
			$xmlService = $dom->appendChild($xmlService);
			
			// Loop into logs
			$xmlLogs = $xmlService->appendChild($dom->createElement("history"));
			foreach ($logs as $log) {
				$xmlNode = $xmlLogs->appendChild($dom->createElement("log"));
				
				$xmlNode->appendChild($dom->createElement("author", $log['author']));
				$xmlNode->appendChild($dom->createElement("comment", $log['msg']));
				$xmlNode->appendChild($dom->createElement("date", date("Y-m-d H:i:s", $log['date'])));
			}
			
			// Output
			$this->_outputResponse($dom);
		}
		else {
			$this->_outputErrorResponse(sprintf(T_("You don't have permission to access project %s"), $project_name), 10);
		}
			
	}
	
	/**
	 * Return list of projects
	 * 
	 * 	http://usvn.localhost/service/list
	 * 
	 * 	<?xml version="1.0" encoding="UTF-8"?>
	 * 	<usvn>
	 * 		<auth>
	 * 			<username>USERNAME</username>
	 * 			<password>PASSWORD</password>
	 * 		</auth>
	 * </usvn>
	 */
	public function listAction()
	{
		// Parse XML request
		$this->_parseRequest();
		
		// Authenticate
		if (!$this->_auth()) {
			throw new Zend_Exception("Authentication failure");
		}
		
		// Retrieve projects from the authenticated user 
		$projects = new USVN_Db_Table_Projects();
		$list = $projects->fetchAllAssignedTo($this->_userRow);
		
		// Start create XML return
		$dom = new DOMDocument("1.0", "UTF-8");
		$xmlService = $dom->createElement("usvn");
		$xmlService = $dom->appendChild($xmlService);
		
		// Create project rows
		$xmlProjects = $xmlService->appendChild($dom->createElement("projects"));
		foreach ($list as $row) {
			$xmlNode = $xmlProjects->appendChild($dom->createElement("project"));
			$xmlNode->appendChild($dom->createElement("id", $row->projects_id));
			$xmlNode->appendChild($dom->createElement("name", $row->projects_name));
			$xmlNode->appendChild($dom->createElement("start_date", $row->projects_start_date));
			$xmlNode->appendChild($dom->createElement("description", $row->projects_description));
		}
		
		// Output xml
		$this->_outputResponse($dom);
	}
	
	/**
	 * Realiza a autenticação
	 * 
	 * @todo Create an abstract authenticated method to use around all project
	 */
	private function _auth()
	{
			// Get auth informations
		$username = (string)$this->_xml->auth->username;
		$password = (string)$this->_xml->auth->password;
		
		$auth = Zend_Auth::getInstance();
		
		// Find the authentication adapter from the config file
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, 'general');
		$authAdapterMethod = "database";
		
		if (empty($config->alwaysUseDatabaseForLogin)) {
			$config->alwaysUseDatabaseForLogin = 'admin';
		}
		
		if ($config->alwaysUseDatabaseForLogin != $username && $config->authAdapterMethod) {
			$authAdapterMethod = strtolower($config->authAdapterMethod);
		}
		$authAdapterClass = 'USVN_Auth_Adapter_' . ucfirst($authAdapterMethod);
		if (! class_exists($authAdapterClass)) {
			throw new USVN_Exception(T_('The authentication adapter method set in the config file is not valid.'));
		}
		
		// Retrieve auth-options, if any, from the config file
		$authOptions = null;
		if ($config->$authAdapterMethod && $config->$authAdapterMethod->options) {
			$authOptions = $config->$authAdapterMethod->options->toArray();
		}
		
		// Set up the authentication adapter
		$authAdapter = new $authAdapterClass($username, $password, $authOptions);
		
		// Attempt authentication, saving the result
		$result = $auth->authenticate($authAdapter);
		
		if (! $result->isValid()) {
			return FALSE;
		} else {
			
			$identity = $auth->getStorage()->read();
			
			$table = new USVN_Db_Table_Users();
			$this->_userRow = $table->fetchRow(array(
				"users_login = ?" => $username
			));
			
			/**
			 * Workaround for LDAP.
			 * We need the identity to match the database,
			 * but LDAP identities can be in the following form:
			 * uid=username,ou=people,dc=foo,dc=com
			 * We need to simply keep username, as passed to the constructor method.
			 *
			 * Using in_array(..., get_class_methods()) instead of method_exists() or is_callable(),
			 * because none of them really check if the method is actually callable (ie. not protected/private).
			 * See comments @ http://us.php.net/manual/en/function.method-exists.php
			 */
			if (in_array("getIdentityUserName", get_class_methods($authAdapter))) {
				// Because USVN uses an array (...) when Zend uses a string
				if (! is_array($identity)) {
					$identity = array();
				}
				$username = $authAdapter->getIdentityUserName();
				$auth->getStorage()->write($identity);
			}
			
			/**
			 * Another workaround for LDAP.
			 * As long as we don't provide real
			 * and full LDAP support (add, remove, etc.), if a user managed to
			 * log in with LDAP, or any other non-DB support, we need to add
			 * the user in the database :)
			 */
			if ($config->$authAdapterMethod->createUserInDBOnLogin) {
				$table = new USVN_Db_Table_Users();
				$this->_userRow = $table->fetchRow(array(
					"users_login = ?" => $username
				));
			}
			
			return TRUE;
		}
	}
	
	/**
	 * Parse XML
	 */
	private function _parseRequest()
	{
		// Retrieve XML from raw data
		$this->_xml = simplexml_load_string($this->_request->getRawBody());
		if (!$this->_xml) {
			throw new Zend_Exception("XML cannot be parsed");
		}
	}
	
	/**
	 * Output xml return
	 */
	private function _outputResponse($xml) {
		$this->_response
			->setHeader('Content-Type', 'text/xml; charset=utf-8')
			->setBody($xml->saveXML());
	}
	
	/**
	 * Output xml error return
	 */
	private function _outputErrorResponse($message, $code=0) {
		// Create xml to return erros
		$dom = new DOMDocument("1.0", "UTF-8");
		$xmlService = $dom->createElement("service");
		$xmlService = $dom->appendChild($xmlService);
			
		$xmlError = $xmlService->appendChild($dom->createElement("error"));
		$xmlError->appendChild($dom->createElement("message", $message));
		$xmlError->appendChild($dom->createElement("code", $code));
			
		// Output
		$this->_outputResponse($dom);
	}
}
