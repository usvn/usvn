<?php
/**
 * Class for autthz file manipulation
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
 * $Id$
 */
class USVN_Authz
{
	static public function generate()
	{
		$config = Zend_Registry::get('config');
		$file = "[/]\n* = \n\n[groups]\n";

		$array = array();
		$groups = new USVN_Db_Table_Groups();
		foreach ($groups->fetchAllAndUsers() as $group) {
			$array[$group->name][] = $group->users_login;
		}
		foreach ($array as $group => $users) {
			$users = implode(", ", $users);
			$file .= "{$group} = {$users}\n";
		}

		$array = array();
		$projects = new USVN_Db_Table_Projects();
		foreach ($projects->fetchAllAndFilesRightsAndGroups() as $project) {
			/* @var $project USVN_Db_Table_Row_Project */
			if (!isset($array[$project->name])) {
				$array[$project->name] = array();
			}
			if ($project->files_rights_path) {
				if (!isset($array[$project->name][$project->files_rights_path])) {
					$array[$project->name][$project->files_rights_path] = array();
				}
				if ($project->groups_name) {
					$rights  = (($project->files_rights_is_readable) ? "r" : "");
					$rights .= (($project->files_rights_is_writable) ? "w" : "");
					$array[$project->name][$project->files_rights_path][$project->groups_name] = $rights;
				}
			}
		}
		foreach ($array as $project => $files_paths) {
			$file .= "\n\n# Project {$project}\n";
			foreach ($files_paths as $files_path => $files_rights) {
				$file .= "[{$project}:{$files_path}]\n";
				foreach ($files_rights as $group => $rights) {
					$file .= "@{$group} = {$rights}\n";
				}
				$file .= "\n";
			}
		}

		@file_put_contents($config->subversion->path . "authz", $file);
	}
}
