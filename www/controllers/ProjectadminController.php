<?php
/**
 * Project management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage project
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AdminadminController.php';

class ProjectadminController extends AdminadminController
{
	protected function getProjectData($data)
	{
		if (!isset($data["projects_name"]) || !isset($data["projects_description"])) {
			return array();
		}

		$project = array();
		$project["projects_name"] = $data["projects_name"];
		$project["projects_description"] = $data["projects_description"];
		return $project;
	}

	public function indexAction()
	{
		$table = new USVN_Db_Table_Projects();
		$this->_view->projects = $table->fetchAll(null, "projects_name");
		$this->_render();
	}

	public function newAction()
	{
		$table = new USVN_Db_Table_Projects();
		$this->_view->project = $table->createRow();
		$this->_render();
	}

	public function createAction()
	{
		$data = $this->getProjectData($_POST);
		if (empty($data)) {
			$this->_redirect("/admin/project/new");
		}
		$table = new USVN_Db_Table_Projects();
		$project = $table->createRow($data);
		try {
			$project->save();
			$this->_redirect("/admin/project/");
		}
		catch (Exception $e) {
			$this->_view->project = $project;
			$this->_view->message = nl2br($e->getMessage());
			$this->_render('new.html');
		}
	}

	public function editAction()
	{
		$table = new USVN_Db_Table_Projects();
		$this->_view->project = $table->fetchRow(array('projects_name = ?' => $this->getRequest()->getParam('name')));
		if ($this->_view->project === null) {
			$this->_redirect("/admin/project/");
		}
		$this->_render();
	}

	public function updateAction()
	{
		$data = $this->getProjectData($_POST);
		if (empty($data)) {
			$this->_redirect("/admin/project/new");
		}
		$table = new USVN_Db_Table_Projects();
		$project = $table->fetchRow(array("projects_name = ?" => $this->getRequest()->getParam('name')));
		if ($project === null) {
			$this->_redirect("/admin/project/");
		}
		$project->setFromArray($data);
		try {
			$project->save();
			$this->_redirect("/admin/project/");
		}
		catch (Exception $e) {
			$this->_view->project = $project;
			$this->_view->message = nl2br($e->getMessage());
			$this->_render('edit.html');
		}
	}

	public function deleteAction()
	{
		$table = new USVN_Db_Table_Projects();
		$project = $table->fetchRow(array('projects_name = ?' => $this->getRequest()->getParam('name')));
		if ($project === null) {
			$this->_redirect("/admin/project/");
		}
		$project->delete();
		$this->_redirect("/admin/project/");
	}
}
