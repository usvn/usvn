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
	static private function createProjectSVN($project_name, $project_id, $create_dir)
	{
		$config = Zend_Registry::get('config');
		$path = $config->subversion->path
		. DIRECTORY_SEPARATOR
		. 'svn'
		. DIRECTORY_SEPARATOR
		. $project_name;
		if (!USVN_SVNUtils::isSVNRepository($path, true)) {
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
				//
				$hook_template_path = implode(DIRECTORY_SEPARATOR, array(APPLICATION_PATH, 'templates', 'hooks', 'generic-hook.php'));
				$bat_template_path = implode(DIRECTORY_SEPARATOR, array(APPLICATION_PATH, 'templates', 'hooks', 'generic-bat.bat'));
				$template = file_get_contents($hook_template_path);
				$bat_template = file_get_contents($bat_template_path);
				$hooks_path = Zend_Registry::get("config")->subversion->hooksPath;
				$svn_hooks_path = $path . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR;
				foreach (USVN_SVNUtils::$hooks as $hook_event)
				{
					if ($hook_event == 'pre-revprop-change')
					{
						// pre-revprop-change not handled right now
						continue;
					}
					$hook_file_name = $svn_hooks_path . DIRECTORY_SEPARATOR . $hook_event;
					$hook_tpl = '';
					$hook_tpl = preg_replace('!\${USVN_project_id}!', $project_id, $template);
					$hook_tpl = preg_replace('!\${USVN_hooks_path}!', $hooks_path, $hook_tpl);
					$hook_tpl = preg_replace('!\${USVN_hook_event}!', $hook_event, $hook_tpl);
					if (file_put_contents($hook_file_name, $hook_tpl) === false)
					{
						throw new USVN_Exception(T_("Can't create subversion hook:<br />%s"), $hook_file_name);
					}
					if (chmod($hook_file_name, 0744) === false)
					{
						throw new USVN_Exception(T_("Can't chmod hook file:<br />%s"), $hook_file_name);
					}
					if (strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN')
					{
						$bat_mobile = '';
						$bat_mobile = preg_replace('!\${USVN_hook_file}!', $bat_template, $bat_template);
						$bat_man = $hook_file_name . '.bat';
						if (file_put_contents($batman, $bat_mobile) === false)
						{
							throw new USVN_Exception(T_("Can't create Windows subversion hook:<br />%s"), $batman);
						}
					}
				}
				
				//
			} else {
				$message = "One of these repository's subfolders is a subversion repository.";
				throw new USVN_Exception(T_("Can't create subversion repository:<br />%s"), $message);
			}
		} else {
			$message = $project_name." is a SVN repository.";
			throw new USVN_Exception(T_("Can't create subversion repository:<br />%s"), $message);
		}
	}

	static private function ApplyFileRights($project, $group, $create_svn_directories)
	{
		$acces_rights = new USVN_FilesAccessRights($project->projects_id);
		
		
		if ($create_svn_directories) {
			$acces_rights->setRightByPath($group->groups_id, '/', true, false);
			$acces_rights->setRightByPath($group->groups_id, '/branches', true, true);
			$acces_rights->setRightByPath($group->groups_id, '/trunk', true, true);
		}
		else {
			$acces_rights->setRightByPath($group->groups_id, '/', true, true);
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

		$groups = new USVN_Db_Table_Groups();
		if ($create_group) {
			$group = $groups->fetchRow(array('groups_name = ?' => $data['projects_name']));
			if ($group !== null) {
				throw new USVN_Exception(T_("Group %s already exists."), $data['projects_name']);
			}
		}
		try {
			$table = new USVN_Db_Table_Projects();
			$table->getAdapter()->beginTransaction();
			$project = $table->createRow($data);
			$project->save();

			USVN_Project::createProjectSVN($project->name, $project->id, $create_svn_directories);
				
			if ($create_group) {
				$group = $groups->createRow();
				$group->description = sprintf(T_("Autocreated group for project %s"), $data['projects_name']);
				$group->name = $data['projects_name'];
				$group->save();

				$project->addGroup($group);

				USVN_Project::ApplyFileRights($project, $group, $create_svn_directories);
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
