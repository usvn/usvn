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
			if( !empty( $rawdata ) )
			{
			if (array_key_exists("/", $rawdata)) {
				$data = $rawdata['/'];
				}
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
