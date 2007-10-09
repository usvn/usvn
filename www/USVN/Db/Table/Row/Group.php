<?php
/**
 * A rown into groups table
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info/
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package USVN_Db
 * @subpackage Row
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id $
 */
class USVN_Db_Table_Row_Group extends USVN_Db_Table_Row
{
	/**
	* Add an leader user to a group
	*
	* @param mixed User
	*/
	public function addLeaderUser($user)
	{
		if (is_object($user)) {
			$user_id = $user->id;
		} elseif (is_numeric($user)) {
			$user_id = intval($user);
		}
		if ($this->id && $user_id) {
			$user_groups = new USVN_Db_Table_UsersToGroups();
			$user_groups->insert(
				array(
					"groups_id" => $this->id,
					"users_id" 	=> $user_id,
					"is_leader" => 1,
				)
			);
		}
	}

	/**
	* Update leader user to a group
	*
	* @param mixed User
	*/
	public function updateLeaderUser($user, $type)
	{
		$user_groups = new USVN_Db_Table_UsersToGroups();
		$where = $user_groups->getAdapter()->quoteInto('users_id = ?', $user->users_id);
		$user_groups->update(
				array(
					"groups_id" => $this->id,
					"users_id" 	=> $user->users_id,
					"is_leader" => $type,
				), $where);
	}

	/**
	* Add an user to a group
	*
	* @param mixed User
	*/
	public function addUser($user)
	{
		if (is_object($user)) {
			$user_id = $user->id;
		} elseif (is_numeric($user)) {
			$user_id = intval($user);
		}
		if ($this->id && $user_id) {
			$user_groups = new USVN_Db_Table_UsersToGroups();
			$user_groups->insert(
				array(
					"groups_id" => $this->id,
					"users_id" 	=> $user_id,
					"is_leader" => false,
				)
			);
		}
	}

	/**
	* Delete an user to a group
	*
	* @param mixed User
	*/
	public function deleteUser($user)
	{
		if (is_object($user)) {
			$user_id = $user->id;
		} elseif (is_numeric($user)) {
			$user_id = intval($user);
		}
		if ($user_id) {
			$user_groups = new USVN_Db_Table_UsersToGroups();
			$where = $user_groups->getAdapter()->quoteInto('users_id = ?', $user_id);
			$user_groups->delete($where);
		}
	}

	/**
	* Check if an user is in the group
	*
	* @param USVN_Db_Table_Row_User $user
	* @return boolean
	*/
	public function hasUser($user)
	{
		if (!$this->__isset('groups_id')) {
			return false;
		}
		$user_groups = new USVN_Db_Table_UsersToGroups();
		$res = $user_groups->fetchRow(
		array(
			"users_id = ?" 	=> $user->id,
			"groups_id = ?" => $this->id
		)
		);
		if ($res === NULL) {
			return false;
		}
		return true;
	}

	/**
	 * Delete all users from usersToGroups
	 *
	 */
	public function deleteAllUsers()
	{
		$user_groups = new USVN_Db_Table_UsersToGroups();
		$where = $user_groups->getAdapter()->quoteInto('groups_id = ?', $this->id);
		$user_groups->delete($where);
	}

	/**
	* Add a group to a project
	*
	* @param mixed Group
	*/
	public function addProject($project)
	{
		if (is_object($project)) {
			$project_id = $project->id;
		} elseif (is_numeric($project)) {
			$project_id = intval($project);
		}
		if ($this->id && $project_id) {
			$workgroups = new USVN_Db_Table_Workgroups();
			$workgroups->insert(
				array(
					"groups_id" 	=> $this->id,
					"projects_id"	=> $project_id
				)
			);
		}
	}

	/**
	* Add an group to a project
	*
	* @param mixed User
	*/
	public function deleteProject($project)
	{
		if (is_object($project)) {
			$project_id = $project->id;
		} elseif (is_numeric($project)) {
			$project_id = intval($project);
		}
		if ($project_id) {
			$workgroups = new USVN_Db_Table_Workgroups();
			$where = $workgroups->getAdapter()->quoteInto('projects_id = ?', $project_id);
			$workgroups->delete($where);
		}
	}

	/**
	 * Delete all projects from workgroups
	 *
	 */
	public function deleteAllProjects()
	{
		$workgroups = new USVN_Db_Table_Workgroups();
		$workgroups->delete("");
	}

	/**
	* Check if an user is in the group
	*
	* @param USVN_Db_Table_Row_User User
	* @return boolean
	*/
	public function userIsMember($user)
	{
		return $this->getLinkUsersToGroups($user) === null ? false : true;
	}

	/**
	* Promote user as group leader. User need to be already members of group.
	*
	* @param USVN_Db_Table_User
	* @throw USVN_Exception
	*/
	public function promoteUser($user)
	{
		$link = $this->getLinkUsersToGroups($user);
		if ($link === null) {
			throw new USVN_Exception(T_("User %s is not member of group %s"), $user->login, $this->name);
		}
		$link->is_leader = true;
		$link->save();
	}

	/**
	* Demote user as group leader. User need to be already members of group.
	*
	* @param USVN_Db_Table_User
	* @throw USVN_Exception
	*/
	public function demoteUser($user)
	{
		$link = $this->getLinkUsersToGroups($user);
		if ($link === null) {
			throw new USVN_Exception(T_("User %s is not member of group %s"), $user->login, $this->name);
		}
		$link->is_leader = false;
		$link->save();
	}

	/**
	* Check if an user is in the group and is group leader;
	*
	* @param USVN_Db_Table_Row_User User
	* @return boolean
	*/
	public function userIsGroupLeader($user)
	{
		$res = $this->getLinkUsersToGroups($user);
		if ($res === NULL) {
			return false;
		}
		return (boolean)$res->is_leader;
	}

	/**
	* Return list of group leaders
	*
	* @return USVN_Db_Table_Rowset_Users
	*/
	public function getGroupLeaders()
	{
		return $this->getGroupMembersByIsLeader(true);
	}

	/**
	* Return list of users who are not group leaders
	*
	* @return USVN_Db_Table_Rowset_Users
	*/
	public function getNormalUsers()
	{
		return $this->getGroupMembersByIsLeader(false);
	}

	private function getGroupMembersByIsLeader($is_leader)
	{
		$user_groups = new USVN_Db_Table_UsersToGroups();
		$links = $user_groups->fetchAll(array('groups_id = ?' => $this->id, 'is_leader = ?' => $is_leader));
		if (count($links) === 0) {
			return array();
		}
		$users = new USVN_Db_Table_Users();
		$leaders = array();
		foreach ($links  as $link) {
			array_push($leaders, $link->users_id);
		}
		return $users->find($leaders);
	}

	/**
	* Get link between user and groups.  It's a row in table UsersToGroups.
	*
	* @param USVN_Db_Table_Row_User
	* @return USVN_Db_Table_Row_UsersToGroups|null
	*/
	private function getLinkUsersToGroups($user)
	{
		$user_groups = new USVN_Db_Table_UsersToGroups();
		$res = $user_groups->fetchRow(
			array(
				"groups_id = ?" => $this->id,
				"users_id = ?" 	=> $user->id
			)
		);
		return $res;
	}

	public function allLeader($group_id, $type)
	{
		$user_groups = new USVN_Db_Table_UsersToGroups();
		$links = $user_groups->fetchAll(array('groups_id = ?' => $group_id, 'is_leader = ?' => $type));
		if (count($links) === 0) {
			return array();
		}
		$users = new USVN_Db_Table_Users();
		$leaders = array();
		foreach ($links  as $link) {
			array_push($leaders, $link->users_id);
		}
		return $users->find($leaders);
	}

	public function isLeaderOrAdmin($user)
	{
		if ($user)
		{
			$user_groups = new USVN_Db_Table_UsersToGroups();
			$res = $user_groups->fetchRow(
				array(
					"groups_id = ?" => $this->id,
					"users_id = ?" 	=> $user->id
				)
			);
			if ($res)
			{
				if ($res->is_leader == 1)
					return 1;
			}
			if ($user->is_admin == 1)
				return 1;
			return 0;
		}
	}
}
