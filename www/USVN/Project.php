<?php
/**
 * Usefull methods for project management
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6
 * @package usvn
 * @subpackage Table
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_Project
{
	/**
	 * Create SVN repositories
	 * 
	 * @param string Project name
	 * @param bool Create standard directories (/trunk, /tags, /branches)
	 */
	static private function createProjectSVN($project_name, $create_dir)
	{
		$config = Zend_Registry::get('config');
		$path = $config->subversion->path
			. DIRECTORY_SEPARATOR
			. 'svn'
			. DIRECTORY_SEPARATOR
			. $project_name;
		if (!USVN_SVNUtils::isSVNRepository($path)) {
			$directories = explode(DIRECTORY_SEPARATOR, $path);
			$tmp_path = '';
			foreach ($directories as $directory) {
				$tmp_path .= $directory . DIRECTORY_SEPARATOR;
				if (USVN_SVNUtils::isSVNRepository($tmp_path)) {
					$tmp_path = '';
					break;
				}
			}
			if ($tmp_path === $path . DIRECTORY_SEPARATOR) {
				@mkdir($path, 0700, true);
				USVN_SVNUtils::createSVN($path);
				if ($create_dir) {
					USVN_SVNUtils::createStandardDirectories($path);
				}
			} else {
				$message = "One of these repository's subfolders is a subversion repository.";
				throw new USVN_Exception(T_("Can't create subversion repository: %s"), $message);
			}
		}		
	}
	
	
	/**
	* Create a project
	*
	* @param array Fields data
	* @param string The creating user
	* @param bool Create a group for the project
	* @param bool Add user into group
	* @param bool Add user as admin for the project
	* @param bool Create SVN standard directories
	* @return USVN_Db_Table_Row_Project
	*/
	static public function createProject(array $data, $login, $create_group, $add_user_to_group, $create_admin, $create_svn_directories)
	{
		//We need check if admin exist before create project because we can't go back
		$user_table = new USVN_Db_Table_Users();
		$user = $user_table->fetchRow(array('users_login = ?' => $login));
		if ($user === null) {
			throw new USVN_Exception(T_('Login %s not found'), $login);
		}
		
		try {
			$table = new USVN_Db_Table_Projects();
			$table->getAdapter()->beginTransaction();
			$project = $table->createRow($data);
			$project->save();
	
			USVN_Project::createProjectSVN($data['projects_name'], $create_svn_directories);
			
			if ($create_group) {
				$groups = new USVN_Db_Table_Groups();
				$group = $groups->createRow();
				$group->description = sprintf(T_("Autocreated group for project %s"), $data['projects_name']);
				$group->name = $data['projects_name'];
				$group->save();
	
				$project->addGroup($group);
	
				$files_rights = new USVN_Db_Table_FilesRights();
				$files_rights = $files_rights->findByPath($project->id, "/");
	
				$groups_to_files_rights = new USVN_Db_Table_GroupsToFilesRights();
				$group_to_file_rights = $groups_to_files_rights->findByIdRightsAndIdGroup($files_rights->id, $group->id);
				$group_to_file_rights->files_rights_is_readable = true;
				$group_to_file_rights->files_rights_is_writable = true;
				$group_to_file_rights->save();
			}
	
			if ($create_group && $add_user_to_group) {
				$group->addUser($user);
				$group->promoteUser($user);
			}
			if ($create_admin) {
				$project->addUser($user);
			}
		}
		catch (Exception $e) {
			$table->getAdapter()->rollBack();
			throw $e;
		}
		$table->getAdapter()->commit();
		return $project;
	}

	public static function deleteProject($project_name)
	{
		$table = new USVN_Db_Table_Projects();
		$project = $table->fetchRow(array('projects_name = ?' => $project_name));
		if ($project === null) {
			throw new USVN_Exception(T_("Project %s doesn't exist."), $project_name);
		}
		$project->delete();
		$groups = new USVN_Db_Table_Groups();
		$where = $groups->getAdapter()->quoteInto("groups_name = ?", $project_name);
		$group = $groups->fetchRow($where);
		if ($group !== null) {
			$group->delete();
		}
		
		USVN_DirectoryUtils::removeDirectory(Zend_Registry::get('config')->subversion->path
		. DIRECTORY_SEPARATOR
		. 'svn'
		. DIRECTORY_SEPARATOR
		. $project_name);
		
	}
}
