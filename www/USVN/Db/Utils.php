<?php
/**
 * Provide usefull static methods to manipulate database.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package USVN_Db
 * @subpackage utils
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_Db_Utils
{
	/**
    * Load an SQL file to the database
	*
	*  @param Zend_Db_Adapter_Abstract Connection to Database
	*  @param string Path to the SQL file
	*  @throw USVN_Exception
    */
	static public function loadFile($db, $path)
	{
		$query = @file_get_contents($path);
		if ($query === false) {
			throw new USVN_Exception(T_("Can't open file %s."), $path);
		}
		$query = ereg_replace("--[^\n]*\n", "", $query);
		$tab = explode(";", $query);
		foreach($tab as $ligne) {
			$ligne = trim($ligne, " \n\t\r");
			$ligne = rtrim($ligne, " \n\t\r");
			if (strlen($ligne)) {
				try {
					$ligne = str_replace("usvn_", USVN_Db_Table::$prefix, $ligne);
					$db->query($ligne);
				}
				catch (Exception $e) {
					throw new USVN_Exception(T_("Can't load file %s at:\n%s"), $path, $ligne);
				}
			}
		}
	}

	/**
	 * Delete all tables from the database.
	 *
	 *  Warning this code can do an infinity loop in case of problems.
	 *
	 *  @param Zend_Db_Adapter_Abstract Connection to Database
	 */
	static public function deleteAllTables($db)
	{
		self::deleteAllTablesPrefixed($db, '');
	}

	/**
	 * Delete all tables from the database matching a prefix.
	 *
	 *  Warning this code can do an infinity loop in case of problems.
	 *
	 *  @param Zend_Db_Adapter_Abstract Connection to Database
	 *  @param string table prefix
	 */
	static public function deleteAllTablesPrefixed($db, $prefix)
	{
		$fnc = create_function('$var', "return !strcmp(substr(\$var, 0, strlen('$prefix')), '$prefix');");
		$todelete = array_filter($db->listTables(), $fnc);
		while (count($todelete)) {
			$table = array_shift($todelete);
			try {
				$db->query("DROP TABLE $table CASCADE");
			}
			catch (Exception $e) {
				array_push($todelete, $table);
			}
		}
	}
}
