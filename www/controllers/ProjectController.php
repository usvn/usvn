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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AdminController.php';

class ProjectController extends AdminController
{
	public function getProjectDataFromPost()
	{
		$project = array('projects_name'			=> $_POST['projects_name'],
							'projects_description'	=> $_POST['projects_description']
						);
		if (isset($_POST['projects_id']) && !empty($_POST['projects_id'])) {
			$project['projects_id'] = $_POST['projects_id'];
		}
		if (isset($_POST['projects_start_date']) && !empty($_POST['projects_start_date'])) {
			$project['projects_start_date'] = $_POST['projects_start_date'];
		}
		return $project;
	}

	public function indexAction()
	{
		$table = new USVN_Db_Table_Projects();
		$where  = $table->getAdapter()->quoteInto('projects_name NOT IN (?)', $table->exceptedEntries['projects_name']);
		$this->_view->projects = $table->fetchAll($where, "projects_name");
		$this->_render('index.html');
	}

	public function newAction()
	{
		if (!empty($_POST) && $_POST['projects_name']) {
			$data = $this->getProjectDataFromPost();
			$this->save("USVN_Db_Table_Projects", "project", $data);
		}
		$this->_render('form.html');
	}

	public function editAction()
	{
		if (!empty($_POST) && $_POST['projects_name']) {
			$data = $this->getProjectDataFromPost();
			$this->save("USVN_Db_Table_Projects", "project", $data);
		} else {
			$projectTable = new USVN_Db_Table_Projects();
			$this->_view->project = $projectTable->fetchRow(array('projects_name = ?' => $this->getRequest()->getParam('name')));
		}
		$this->_render('form.html');
		$this->_forward('attachGroups');
	}

	public function deleteAction()
	{
		if (!empty($_POST)) {
			$data = $this->getProjectDataFromPost();
			$this->delete("USVN_Db_Table_Projects", "project", $data);
		} else {
			$projectTable = new USVN_Db_Table_Projects();
			$this->_view->project = $projectTable->fetchRow(array('projects_name = ?' => $this->getRequest()->getParam('name')));
		}
		$this->_render('form.html');
	}

	public function attachGroupsAction()
	{
		if (!empty($_POST) && $_POST['groups_id']) {
			$projectTable = new USVN_Db_Table_Projects();
			$project = $projectTable->fetchRow(array('projects_id = ?' => $_POST['project_id']));
			//$group->deleteAllProjects();
			foreach ($_POST['groups_id'] as $group_id) {
				$project->addGroup($group_id);
			}
		}
		$projectTable = new USVN_Db_Table_Projects();
		$project = $projectTable->fetchRow(array('projects_name = ?' => $this->getRequest()->getParam('name')));
		$this->_view->project_id = $project->id;

		$groupRow = $project->findManyToManyRowset('USVN_Db_Table_Groups', 'USVN_Db_Table_Workgroups');
		$this->_view->attachedGroups = $groupRow->toArray();

		$groupsTable = new USVN_Db_Table_Groups();
		$where = $groupsTable->getAdapter()->quoteInto('groups_name NOT IN (?)', $groupsTable->exceptedEntries['groups_name']);
		$this->_view->allGroups = $groupsTable->fetchAll($where, "groups_name");
		$this->_render('../group/attachment.html');
	}
}
