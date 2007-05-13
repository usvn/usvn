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

		$groups = new USVN_Db_Table_Groups();
		foreach ($groups->fetchAll(null, "groups_name") as $group) {
			/* @var $group USVN_Db_Table_Row_Group */

			$tmp = array();
			$users = $group->findManyToManyRowset("USVN_Db_Table_Users", "USVN_Db_Table_UsersToGroups");
			foreach ($users as $user) {
				$tmp[] = $user->login;
			}
			$users = implode(", ", $tmp);
			$file .= "{$group->name} = {$users}\n";
		}

		$projects = new USVN_Db_Table_Projects();
		foreach ($projects->fetchAll(null, 'projects_name') as $project) {
			/* @var $project USVN_Db_Table_Row_Project */

			$file .= "\n\n# Project {$project->name}\n";
		}

		file_put_contents($config->subversion->path . "authz", $file);
	}
}
