<?php
/**
 * Class for authz file manipulation
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
 * This project has been realized as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_Authz
{
	static public function generate()
	{
		$config = Zend_Registry::get('config');

		$oldUserConfig = self::loadExistingAuthzData($config->subversion->authz);
		$text  = "# This is an auto generated file! Edit at your own risk!\n";
		$text .= "# You can edit this \"/\" section. Settings will be kept.\n#\n";
		$text .= "[/]\n";
		foreach ($oldUserConfig as $key => $value) {
			$text .= $key . " = " . $value . "\n";
		}
		$text .= "\n#\n# Don't edit anything below! All manual changes will be overwritten. \n#\n";
		$text .= "\n[groups]\n";

		$array = array();
		$groups = new USVN_Db_Table_Groups();
		foreach ($groups->fetchAllAndUsers() as $group) {
			$array[$group->name][] = $group->users_login;
		}
		foreach ($array as $group => $users) {
			$users = implode(", ", $users);
			$text .= "{$group} = {$users}\n";
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
			$text .= "\n\n# Project {$project}\n";
			foreach ($files_paths as $files_path => $files_rights) {
				$text .= "[{$project}:{$files_path}]\n";
				foreach ($files_rights as $group => $rights) {
					$text .= "@{$group} = {$rights}\n";
				}
				$text .= "\n";
			}
		}

        @file_put_contents($config->subversion->authz, $text);
	}

	static public function load($fileName){
        $config = Zend_Registry::get('config');

        $oldUserConfig = self::parse_ini_file($fileName);

        foreach ($oldUserConfig as $key => $value) {
            if ($key == "groups") {
                $groups_list = $value;
                foreach ($groups_list as $group_name => $users_list_str){
                    $table_groups = new USVN_Db_Table_Groups();
                    $group = $table_groups->fetchRow(array('groups_name = ?' => $group_name));
                    if ($group === null) {
                        $data = array(
                            'groups_name' => $group_name,
                            'groups_description' => '',
                        );
                        $group = $table_groups->createRow($data);
                        $group->save();
                    }

                    $user_list = explode(",", $users_list_str);
                    foreach ($user_list as $user_name){
                        $table_users = new USVN_Db_Table_Users();
                        $user = $table_users->fetchRow(array('users_login = ?' => $user_name));
                        if ($user === null){
                            echo $user_name . "\n";
                            $data = array(
                                'users_login' => $user_name,
                                'users_password' => 'ipassword',
                                'users_is_admin' => 0
                            );
                            $user = $table_users->createRow($data);
                            $user->save();
                        }

                        $table_users_to_groups = new USVN_Db_Table_UsersToGroups();
                        $users_to_groups = $table_users_to_groups->fetchRow(array('users_id = ?' => $user->users_id, 'groups_id = ?' => $group->groups_id));
                        if($users_to_groups === null){
                            $group->addUser($user);
                        }
                    }
                }
            }

            if (strpos($key, ":") !== false) {
                $project_path_pair = explode(":", $key);
                $project_name = $project_path_pair[0];
                $path = $project_path_pair[1];
                $table_projects = new USVN_Db_Table_Projects();
                $project = $table_projects->fetchRow(array('projects_name = ?' => $project_name));
                if ($project === null){
                    $data = array(
                        'projects_name' => $project_name,
                        'projects_description' => '',
                    );
                    $project = $table_projects->createRow($data);
                    $project->save();
                }

                $table_files_rights = new USVN_Db_Table_FilesRights();
                $file_rights = $table_files_rights->fetchRow(array('projects_id = ?' => $project->projects_id, 'files_rights_path = ?' => $path));
                if ($file_rights === null){
                    $data = array(
                        'projects_id' => $project->id,
                        'files_rights_path' => $path,
                    );
                    $file_rights = $table_files_rights->createRow($data);
                    $file_rights->save();
                }
                $group_rights_list = $value;

                foreach ($group_rights_list as $group_name => $rights){
                    if ($group_name == "*"){
                        continue;
                    }

                    $group_name = str_replace("@", "", $group_name);
                    $table_groups = new USVN_Db_Table_Groups();
                    $group = $table_groups->fetchRow(array('groups_name = ?' => $group_name));

                    $table_groups_to_projects = new USVN_Db_Table_GroupsToProjects();
                    $groups_to_projects = $table_groups_to_projects->fetchRow(array('projects_id' => $project->projects_id, 'groups_id' => $group->groups_id));
                    if ($groups_to_projects === null){
                        $project->addGroup($group);
                    }

                    $table_groups_to_files_rights = new USVN_Db_Table_GroupsToFilesRights();
                    $groups_to_files_rights = $table_groups_to_files_rights->fetchRow(array('files_rights_id = ?' => $file_rights->files_rights_id, 'groups_id = ?' => $group->groups_id));

                    $r = 0;
                    $w = 0;
                    if (strpos($rights, "r") !== false) {
                        $r = 1;
                    }
                    if (strpos($rights, "w") !== false) {
                        $w = 1;
                    }
                    if ($groups_to_files_rights === null){
                        $data = array(
                            'files_rights_id' => $file_rights->files_rights_id,
                            'groups_id' => $group->groups_id,
                            'files_rights_is_readable' => $r,
                            'files_rights_is_writable' => $w,
                        );
                        $groups_to_files_rights = $table_groups_to_files_rights->createRow($data);
                        $groups_to_files_rights->save();
                    }
                }
            }
        }
    }

	/**
	 * Loads the "/" Section from existing authz file.
	 * All other lines and comments are ignored.
	 *
	 * @param String $fileName
	 * @return array containing old lines
	 */
	static private function loadExistingAuthzData($fileName) {
		$data = array("*" => "", ); // default values
		try {
			$rawdata = self::parse_ini_file($fileName);
			if (array_key_exists("/", $rawdata)) {
				$data = $rawdata['/'];
			}
		} catch (Exception $e) {
			// we can ignore this, cause we preinitialized the data with an empty array.
		}
		return $data;
	}

	/**
	 * Loads an INI file. Similar to built in parse_ini_file,
	 * but accepts not only ";" but also "#" for comment lines.
	 *
	 * @param String $fileName
	 * @return an array of all sections, each containing an array of key/value pairs.
	 */
	static private function parse_ini_file($fileName) {
		$r = null;
		$sec = null;
		$f = @file($fileName);
		if ($f === false) {
			return array();
		}
		for ($i=0;$i<count($f);$i++)
		{
			$newsec=0;
			$w=trim($f[$i]);
			if ( ($w) and (substr($w,0,1)!="#") and (substr($w,0,1)!=";") )
			{
				if ((!$r) or ($sec))
				{
					if ((substr($w,0,1)=="[") and (substr($w,-1,1))=="]") {$sec=substr($w,1,strlen($w)-2);$newsec=1;}
				}
				if (!$newsec)
				{
					$w=explode("=",$w);
					$k=trim($w[0]);
					unset($w[0]);
					$v=trim(implode("=",$w));
					if ($sec) {$r[$sec][$k]=$v;} else {$r[$k]=$v;}
				}
			}
		}
		return $r;
	}
}
