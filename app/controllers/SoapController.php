<?php

class SoapController extends Zend_Controller_Action {
	
	protected $xml;
	protected $_userRow;
	
	public function init() {
		// Disable layout
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		
		// Retrieve XML from raw data
		$this->xml = simplexml_load_string($this->_request->getRawBody());
		if(!$this->xml) {
			throw new Zend_Exception("XML cannot be parsed");
		}
		
		// Authenticate
		if(!$this->_auth()) {
			throw new Zend_Exception("Authentication failure");
		}
	}
	
	/**
	 * Return list of projects
	 */  
	public function listAction() {
		$projects = new USVN_Db_Table_Projects();
		$list = $projects->fetchAllAssignedTo($this->_userRow);
		
		$dom = new DOMDocument("1.0", "UTF-8");
		$xmlService = $dom->createElement("service");
		$xmlService = $dom->appendChild($xmlService);
		
		$xmlError = $xmlService->appendChild($dom->createElement("error"));
		$xmlProjects = $xmlService->appendChild($dom->createElement("projects"));
		
		foreach($list as $row) {
			$xmlNode = $xmlProjects->appendChild($dom->createElement("project"));
			$xmlNode->appendChild($dom->createElement("projects_id", $row->projects_id));
			$xmlNode->appendChild($dom->createElement("projects_name", $row->projects_name));
			$xmlNode->appendChild($dom->createElement("projects_start_date", $row->projects_start_date));
			$xmlNode->appendChild($dom->createElement("projects_description", $row->projects_description));
		}
		
		echo $dom->saveXML();
	}
        
	/**
	 * Realiza a autenticação
	 */
	private function _auth($auth) {
		
		$username = "";
		$password = "";
		
		$model = new USVN_Db_Table_Users();
		$this->_userRow = $model->findByName($username);
		
		if(!$this->_userRow) {
			return FALSE;
		}
		
		return TRUE;
	}
}
