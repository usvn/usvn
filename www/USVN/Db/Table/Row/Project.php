<?php
/**
 * A rown into project table
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
class USVN_Db_Table_Row_Project extends USVN_Db_Table_Row
{
	/**
	 * Add a group to a project
	 *
	 * @param mixed Group
	 */
	public function addGroup($group)
	{
		$group_id = 0;
		if (is_object($group)) {
			$group_id = $group->id;
		} elseif (is_numeric($group)) {
			$group_id = intval($group);
		}
		if ($this->id && $group_id) {
			$table = new USVN_Db_Table_GroupsToProjects();
			$row = $table->createRow(array("groups_id" => $group->id, "projects_id" => $this->id));
			$row->save();
		}
	}

	/**
	 * Delete a group to a project
	 *
	 * @param mixed User
	 */
	public function deleteGroup($group)
	{
		$group_id = 0;
		if (is_object($group)) {
			$group_id = $group->id;
		} elseif (is_numeric($group)) {
			$group_id = intval($group);
		}
		if ($group_id) {
			$table = new USVN_Db_Table_GroupsToProjects();
			$where  = $table->getAdapter()->quoteInto("projects_id = ?", $this->id);
			$where .= " AND " . $table->getAdapter()->quoteInto("groups_id = ?", $group_id);
			if ($table->delete($where) == 0)
				throw new USVN_Exception(T_("Invalid group %s for project %s."), $group, $this->id);
		}
		else {
			throw new USVN_Exception(T_("Invalid group %s for project %s."), $group, $this->id);
		}
	}

	/**
	 * Delete all groups from workgroups
	 *
	 */
	public function deleteAllGroups()
	{
		$table = new USVN_Db_Table_GroupsToProjects();
		$table->delete($table->getAdapter()->quoteInto("projects_id = ?", $this->id));
	}

	/**
	 * Check if an group is in the project
	 *
	 * @param USVN_Db_Table_Row_Group Group
	 * @return boolean
	 */
	public function groupIsMember(USVN_Db_Table_Row_Group $group)
	{
		$table = new USVN_Db_Table_GroupsToProjects();
		$res = $table->fetchRow(array("groups_id = ?" => $group->id, "projects_id = ?" => $this->id));
		if ($res === null) {
			return false;
		}
		return true;
	}


	/**
	 * Add a user to a project
	 *
	 * @param mixed user
	 */
	public function addUser($user)
	{
		$user_id = 0;
		if (is_object($user)) {
			$user_id = $user->id;
		} elseif (is_numeric($user)) {
			$user_id = intval($user);
		}
		if ($this->id && $user_id) {
			$table = new USVN_Db_Table_UsersToProjects();
			$row = $table->createRow(array("users_id" => $user->id, "projects_id" => $this->id));
			$row->save();
		}
	}

	/**
	 * Delete a user to a project
	 *
	 * @param mixed User
	 */
	public function deleteUser($user)
	{
		$user_id = 0;
		if (is_object($user)) {
			$user_id = $user->id;
		} elseif (is_numeric($user)) {
			$user_id = intval($user);
		}
		if ($user_id) {
			$table = new USVN_Db_Table_UsersToProjects();
			$where  = $table->getAdapter()->quoteInto("projects_id = ?", $this->id);
			$where .= " AND " . $table->getAdapter()->quoteInto("users_id = ?", $user_id);
			$table->delete($where);
		}
	}

	/**
	 * Delete all users from workusers
	 *
	 */
	public function deleteAllUsers()
	{
		$table = new USVN_Db_Table_UsersToProjects();
		$table->delete($table->getAdapter()->quoteInto("projects_id = ?", $this->id));
	}

	/**
	 * Check if an user is in the project
	 *
	 * @param USVN_Db_Table_Row_User User
	 * @return boolean
	 */
	public function userIsAdmin(USVN_Db_Table_Row_User $user)
	{
		$table = new USVN_Db_Table_UsersToProjects();
		$res = $table->fetchRow(array("users_id = ?" => $user->id, "projects_id = ?" => $this->id));
		if ($res === null) {
			return false;
		}
		return true;
	}



	/**
	 * Check if the project's name is valid or not.
	 *
	 * A valid name is only composed by alphanumeric characters.
	 *
	 * @param string $name project's name
	 *
	 * @throws USVN_Exception
	 */
	public function checkProjectName($name)
	{
		if (empty($name) || preg_match('/^\s+$/', $name)) {
			throw new USVN_Exception(T_("The project's name is empty."));
		}
		if (!preg_match('/^[0-9a-zA-Z_]+$/', $name)) {
			throw new USVN_Exception(T_("The project's name is invalid."));
		}
	}

	/**
	 * Create the SVN repository
	 *
	 * @return void
	 * @throws USVN_Exception, Zend_Exception
	 */
	protected function _insert()
	{
		$this->checkProjectName($this->_data['projects_name']);
		$config = Zend_Registry::get('config');
		$path = $config->subversion->path
			. DIRECTORY_SEPARATOR
			. 'svn'
			. DIRECTORY_SEPARATOR
			. $this->_data['projects_name'];
		if (!USVN_SVNUtils::isSVNRepository($path)) {
			USVN_SVNUtils::createSVN($path);
		}
	}

	/**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
	protected function _update()
	{
		if ($this->_cleanData['projects_name'] != $this->_data['projects_name']) {
			throw new USVN_Exception(T_("You can't rename a project."));
		}
	}

	/**
     * Allows pre-delete logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
	protected function _postDelete()
	{
		$groups = new USVN_Db_Table_Groups();
		$where = $groups->getAdapter()->quoteInto("groups_name = ?", $this->_cleanData['projects_name']);
		$group = $groups->fetchRow($where);
		if ($group !== null) {
			$group->delete();
		}
		USVN_DirectoryUtils::removeDirectory(Zend_Registry::get('config')->subversion->path
		. DIRECTORY_SEPARATOR
		. 'svn'
		. DIRECTORY_SEPARATOR
		. $this->_data['projects_name']);
	}
}
