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
	 * @param string $name
	 * @return array ex: ("read" => true, "write" => false)
	 */
	public function findByPath($group_id, $path)
	{
		$response = array('read' => false, 'write' => false);
		$table_files = new USVN_Db_Table_FilesRights();
		$res_file_rights = $table_files->findByPath($this->_project, $path);
		if ($res_file_rights === null)
			return $response;
		$table_groupsfiles = new USVN_Db_Table_GroupsToFilesRights();
		$res_groupstofiles = $table_groupsfiles->findByIdRightsAndIdGroup($res_file_rights->files_rights_id, $group_id);
		if ($res_groupstofiles != null) {
			if ($res_groupstofiles->files_rights_is_readable) {
				$response['read'] = true;
			}
			if ($res_groupstofiles->files_rights_is_writable) {
				$response['write'] = true;
			}
		}
		return $response;
	}
}
