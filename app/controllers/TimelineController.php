<?php
/**
 * Controller of timeline module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7
 * @package browser
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */


class TimelineController extends USVN_Controller 
{

	/**
	 * Project row object
	 *
	 * @var USVN_Db_Table_Row_Project
	 */
	protected $_project;
	
/**
 * Function which convert date at format 10/10/2007 to 20071010
 *
 * @param unknown_type $number
 * @return unknown
 */
	public function ConvertDate($number){
		if (strstr($number, '/') != FALSE) {
			$split = split("/",$number); 
			$jour = $split[0]; 
			$mois = $split[1]; 
			$annee = $split[2]; 
			return "{".$annee.$mois.$jour."}";
		}
		return $number;
	}
	
	public function indexAction()
	{
		$project = $this->getRequest()->getParam('project');
		$table = new USVN_Db_Table_Projects();
		$project = $table->fetchRow(array("projects_name = ?" => $project));
		/* @var $project USVN_Db_Table_Row_Project */
		if ($project === null) {
			$this->_redirect("/");
		}
		$this->_project = $project;
		
		
		//get the identity of the user
		$identity = Zend_Auth::getInstance()->getIdentity();

		$user_table = new USVN_Db_Table_Users();
		$users = $user_table->fetchRow(array('users_login = ?' => $identity['username']));

		$table = new USVN_Db_Table_Projects();
		$this->view->project = $table->fetchRow(array('projects_name = ?' => $project->name));

		$table = new USVN_Db_Table_UsersToProjects();
		$UserToProject = $table->fetchRow(array('users_id = ?' => $users->users_id, 'projects_id = ?' => $this->view->project->projects_id));
		
		if ($UserToProject == null) {
			$this->render("accesdenied");
			return;
		}

		$this->view->project = $this->_project;
		$SVN = new USVN_SVN($this->_project->name);
		$this->view->log = $SVN->log(100);
	}
	
	
	public function LastHundredRequestAction() {
		$project = $this->getRequest()->getParam('project');
		$table = new USVN_Db_Table_Projects();
		$project = $table->fetchRow(array("projects_name = ?" => $project));
		/* @var $project USVN_Db_Table_Row_Project */
		if ($project === null) {
			$this->_redirect("/");
		}		
			$this->_project = $project;
			$this->view->project = $this->_project;
			$SVN = new USVN_SVN($this->_project->name);
		try {
			$number_start = $project = $this->getRequest()->getParam('number_start');
			$number_end = $project = $this->getRequest()->getParam('number_end');
			$number_end = $this->ConvertDate($number_end);
			$number_start = $this->ConvertDate($number_start);
			$this->view->log = $SVN->log(100, $number_start, $number_end);
			$this->render("index");
		}
		catch(USVN_Exception $e) {
			$this->view->message = "No such revision found";
			$this->view->log = $SVN->log(100);
			$this->render("index");
		}
	}	
}