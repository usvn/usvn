<?php
/**
 * Check if a group can access to a file on the subversion
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: DirectoryUtils.php 404 2007-05-14 10:43:41Z duponc_j $
 */
class USVN_FilesAccessRights
{
	private $_project;

	/**
	 * @param int Project id
	 */
	public function __construct($project)
	{
		$this->_project = $project;
	}

	/**
	 * Return the rights of a groups for the given path
	 *
	 * @param integer Group id
	 * @param string Path
	 * @return array ex: ("read" => true, "write" => false)
	 */
	public function findByPath($group_id, $path)
	{
        $path = str_replace('//', '/', $path);
		if (strlen($path) == 0 || $path{0} !== '/') {
			throw new USVN_Exception(T_("Invalid path %s."), $path);
		}
		$response = array('read' => false, 'write' => false);
		$table_files = new USVN_Db_Table_FilesRights();
		$res_file_rights = $table_files->findByPath($this->_project, $path);
		if ($res_file_rights !== null) {
			$table_groupsfiles = new USVN_Db_Table_GroupsToFilesRights();
			$res_groupstofiles = $table_groupsfiles->findByIdRightsAndIdGroup($res_file_rights->files_rights_id, $group_id);
			if ($res_groupstofiles != null) {
				if ($res_groupstofiles->files_rights_is_readable) {
					$response['read'] = true;
				}
				if ($res_groupstofiles->files_rights_is_writable) {
					$response['write'] = true;
				}
			} else {
				if ($path != '/') {
					return $this->findByPath($group_id, str_replace(basename($path), '', $path)); // Ugly hack do not use dirname because problems with \
				}
			}
		}
		else {
			if ($path != '/') {
				return $this->findByPath($group_id, str_replace(basename($path), '', $path)); // Ugly hack do not use dirname because problems with \
			}
		}
		return $response;
	}

	/**
	* Set right for a group
	*
	* @param integer Group id
	* @param string Path
	* @param bool read
	* @param bool write
	*/
	public function setRightByPath($group_id, $path, $read, $write, $recursive = false)
	{
		$path = preg_replace('#[/]{2,}#', '/', $path);
		if (strlen($path) == 0 || $path{0} !== '/') {
			throw new USVN_Exception(T_("Invalid path %s."), $path);
		}
		$table_files = new USVN_Db_Table_FilesRights();
		$res_files = $table_files->findByPath($this->_project, $path);
		$table_groupstofiles = new USVN_Db_Table_GroupsToFilesRights();
		if ($res_files === null) {
			$file_id = $table_files->insert(array(
				'projects_id' 	   			=> $this->_project,
				'files_rights_path' 	   	=> $path
			));
			$table_groupstofiles->insert(array(
				'files_rights_id' => $file_id,
				'files_rights_is_readable' => ($read === true ? 1 : 0),
				'files_rights_is_writable' => ($write === true ? 1 : 0),
				'groups_id' => $group_id
			));
		}
		else {
			$file_id = $res_files->files_rights_id;
			$where = $table_groupstofiles->getAdapter()->quoteInto('files_rights_id = ?', $file_id);
			$where .= $table_groupstofiles->getAdapter()->quoteInto(' and groups_id = ?', $group_id);
			$groupstofiles = $table_groupstofiles->fetchRow($where);
			if ($groupstofiles === null) {
				$table_groupstofiles->insert(array(
					'files_rights_id' => $file_id,
					'files_rights_is_readable' => ($read === true ? 1 : 0),
					'files_rights_is_writable' => ($write === true ? 1 : 0),
					'groups_id' => $group_id
				));
			}
			else {
				$groupstofiles->files_rights_is_readable = ($read === true ? 1 : 0);
				$groupstofiles->files_rights_is_writable = ($write === true ? 1 : 0);
				$groupstofiles->save();
			}
		}

		if ($recursive === true) {
			$path = rtrim($path, "/");
			$this->unsetRightByPath($group_id, "{$path}/_%");
		}
	}

	private function unsetRightByPath($group_id, $path)
	{
		$filesRightsId = array();
		$tableFilesRights = new USVN_Db_Table_FilesRights();
		$whereFilesRights  = $tableFilesRights->getAdapter()->quoteInto("files_rights_path LIKE ? ", $path);
		$whereFilesRights .= $tableFilesRights->getAdapter()->quoteInto(" AND projects_id = ? ", $this->_project);

		foreach ($tableFilesRights->fetchAll($whereFilesRights) as $filesRights) {
			$filesRightsId[] = $filesRights->id;
		}

		if (count($filesRightsId) > 0) { 
			$table = new USVN_Db_Table_GroupsToFilesRights();
			$where  = $table->getAdapter()->quoteInto("files_rights_id IN (?) ", $filesRightsId);
			$where .= $table->getAdapter()->quoteInto("and groups_id = ? ", $group_id);
			$table->delete($where);
	
			$where  = $table->getAdapter()->quoteInto("files_rights_id IN (?) ", $filesRightsId);
			if (count($table->fetchAll($where)) == 0) {
				$tableFilesRights->delete($whereFilesRights);
			}
		}
	}
}
