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
			$where .= $table->getAdapter()->quoteInto("groups_id = ?", $group_id);
			$table->delete($where);
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
	public function groupIsMember($group)
	{
		$table = new USVN_Db_Table_GroupsToProjects();
		$res = $table->fetchRow(array("groups_id" => $group->id, "projects_id" => $this->id));
		if ($res === NULL) {
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
		USVN_SVNUtils::createSVN($config->subversion->path
		. DIRECTORY_SEPARATOR
		. 'svn'
		. DIRECTORY_SEPARATOR
		. $this->_data['projects_name']);
	}

	/**
     * Allows post-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
	protected function _postInsert()
	{
		$groups = new USVN_Db_Table_Groups();
		$group = $groups->createRow();
		$group->description = sprintf(T_("Autocreated group for project %s"), $this->_data['projects_name']);
		$group->name = $this->_data['projects_name'];
		$group->save();

		$files_rights = new USVN_Db_Table_FilesRights();
		$file_rights = $files_rights->createRow();
		$file_rights->projects_id = $this->_data['projects_id'];
		$file_rights->path = "/";
		$file_rights->save();

		$groups_to_files_rights = new USVN_Db_Table_GroupsToFilesRights();
		$group_to_file_rights = $groups_to_files_rights->createRow(array("groups_id" => $group->id, "files_rights_id" => $file_rights->id));
		$group_to_file_rights->files_rights_is_readable = true;
		$group_to_file_rights->files_rights_is_writable = true;
		$group_to_file_rights->save();
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
