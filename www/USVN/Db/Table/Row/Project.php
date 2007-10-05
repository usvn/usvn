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
			$row = $table->createRow(array("groups_id" => $group_id, "projects_id" => $this->id));
			$row->save();
			$acces_rights = new USVN_FilesAccessRights($this->id);
			$acces_rights->setRightByPath($group_id, "/", true, false);
		}
	}

	/**
	 * Delete a group from a project
	 *
	 * @param mixed Group
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
			$table_groupstoproject = new USVN_Db_Table_GroupsToProjects();
			$p = $table_groupstoproject->getAdapter()->quoteInto("projects_id = ?", $this->id);
			$g = $table_groupstoproject->getAdapter()->quoteInto("groups_id = ?", $group_id);
			if ($table_groupstoproject->delete(array($p, $g)) == 0) {
				throw new USVN_Exception(T_("Invalid group %s for project %s."), $group_id, $this->id);
			}
			$table_groupstofilesrights = new USVN_Db_Table_GroupsToFilesRights();
			$table_groupstofilesrights->delete($g);
		}
		else {
			throw new USVN_Exception(T_("Invalid group %s for project %s."), $group, $this->id);
		}
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
	* Return all users where are member of a group attach to the project
	*
	* @return USVN_Db_Table_Rowset_User
	*/
	public function getUsersGroupMembers()
	{
		$db = $this->getTable()->getAdapter();

		$groups_to_projects = USVN_Db_Table_GroupsToProjects::$prefix . "groups_to_projects";
		$users_to_groups = USVN_Db_Table_UsersToGroups::$prefix . "users_to_groups";
		$users = USVN_Db_Table_Users::$prefix . "users";

		$select = $db->select()
			->from(array('u' => $users), '*')
			->join(array("ug" => $users_to_groups), "ug.users_id = u.users_id", array())
			->join(array("gp" => $groups_to_projects), "gp.groups_id = ug.groups_id", array())
			->where("gp.projects_id = ?", $this->id)
			->group("u.users_id");

		$stmt = $db->query($select);
		$data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
		$data  = array(
			'table'    => $this,
			'data'     => $data,
			'rowClass' => $this->getTable()->getRowClass(),
			'stored'   => true
		);
		return new USVN_Db_Table_Rowset_User($data);
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
			$db = $table->getAdapter();
			$db->getProfiler()->setEnabled(true);
			$where  = $db->quoteInto('projects_id = ?', $this->id) . $db->quoteInto(' AND users_id = ?', $user_id);
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
	 * @param USVN_Db_Table_Row_User or string User
	 * @return boolean
	 */
	public function userIsAdmin($user)
	{
		if (!is_object($user)) {
			$table = new USVN_Db_Table_Users();
			$user = $table->fetchRow(array('users_login = ?' => $user));
		}
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
		if (!preg_match('/^[0-9a-zA-Z_\-]+$/', $name)) {
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
		USVN_DirectoryUtils::removeDirectory(Zend_Registry::get('config')->subversion->path
		. DIRECTORY_SEPARATOR
		. 'svn'
		. DIRECTORY_SEPARATOR
		. $this->_data['projects_name']);
	}
}
