<?php
/**
 * Group management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage group
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AdminController.php';

class GroupController extends AdminController
{
	public static function getGroupDataFromPost()
	{
		$group = array('groups_name'			=> $_POST['groups_name'],
						'groups_description'	=> $_POST['groups_description']);
		if (isset($_POST['groups_id']) && !empty($_POST['groups_id'])) {
			$group['groups_id'] = $_POST['groups_id'];
		}
		return $group;
	}

	public function indexAction()
    {
    	$table = new USVN_Db_Table_Groups();
    	$where  = $table->getAdapter()->quoteInto('groups_name NOT IN (?)', $table->exceptedEntries['groups_name']);
		$this->_view->groups = $table->fetchAll($where, "groups_name");
		$this->_render('index.html');
    }

	public function newAction()
	{
		if (!empty($_POST) && isset($_POST['groups_name'])
			&& isset($_POST['groups_description'])) {
			$data = $this->getGroupDataFromPost();
			$this->save("USVN_Db_Table_Groups", "group", $data);
		}
		$this->_render('form.html');
	}

	public function editAction()
	{
		if (!empty($_POST) && isset($_POST['groups_name'])
			&& isset($_POST['groups_description'])) {
			$data = $this->getGroupDataFromPost();
			$this->save("USVN_Db_Table_Groups", "group", $data);
			$this->_redirect("/admin/group/edit/name/{$data['groups_name']}");
		} else {
			$groupTable = new USVN_Db_Table_Groups();
			$this->_view->group = $groupTable->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		}
		$this->_render('form.html');
		$this->_forward('attachUsers');
	}

	public function deleteAction()
	{
		if (!empty($_POST) && isset($_POST['groups_name'])
			&& isset($_POST['groups_description'])) {
			$data = $this->getGroupDataFromPost();
			$this->delete("USVN_Db_Table_Groups", "group", $data);
		} else {
			$groupTable = new USVN_Db_Table_Groups();
			$this->_view->group = $groupTable->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		}
		$this->_render('form.html');
	}

	public function attachUsersAction()
	{
		if (!empty($_POST) && $_POST['users_id']) {
			$groupTable = new USVN_Db_Table_Groups();
			$group = $groupTable->fetchRow(array('groups_id = ?' => $_POST['group_id']));
			$group->deleteAllUsers();
			foreach ($_POST['users_id'] as $user_id) {
				$group->addUser($user_id);
			}
		}
		$groupTable = new USVN_Db_Table_Groups();
		$group = $groupTable->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		$this->_view->group_id = $group->id;

		$groupRow = $group->findManyToManyRowset('USVN_Db_Table_Users', 'USVN_Db_Table_UsersToGroups');
		$this->_view->attachedUsers = $groupRow->toArray();

		$usersTable = new USVN_Db_Table_Users();
		$where = $usersTable->getAdapter()->quoteInto('users_login NOT IN (?)', $usersTable->exceptedEntries['users_login']);
		$this->_view->allUsers = $usersTable->fetchAll($where, "users_login");
		$this->_render('../user/attachment.html');
		$this->_forward('attachProjects');
	}

	public function attachProjectsAction()
	{
		if (!empty($_POST) && $_POST['projects_id']) {
			$groupTable = new USVN_Db_Table_Groups();
			$group = $groupTable->fetchRow(array('groups_id = ?' => $_POST['group_id']));
			foreach ($_POST['projects_id'] as $project_id) {
				$group->addProject($project_id);
			}
		}
		$groupTable = new USVN_Db_Table_Groups();
		$group = $groupTable->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		$this->_view->group_id = $group->id;

		$projectRow = $group->findManyToManyRowset('USVN_Db_Table_Projects', 'USVN_Db_Table_Workgroups');
		$this->_view->attachedProjects = $projectRow->toArray();

		$projectsTable = new USVN_Db_Table_Projects();
		$where = $projectsTable->getAdapter()->quoteInto('projects_name NOT IN (?)', $projectsTable->exceptedEntries['projects_name']);
		$this->_view->allProjects = $projectsTable->fetchAll($where, "projects_name");
		$this->_render('../project/attachment.html');
	}

	public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
