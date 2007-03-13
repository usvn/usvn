<?php
/**
* @package db
* @since 0.5
*/

/**
* Provide usefull static methods to manipulate database
*/
class USVN_Db_Utils
{
    /**
    * Load an SQL file to the database
	*
	*  @param Zend_Db_Adapter_Abstract Connection to Database
	*  @param string Path to the SQL file
    */
    public function loadFile($db, $path)
    {
		$query = file_get_contents($path);
		$query = ereg_replace("--[^\n]*\n", "", $query);
		$tab = explode(";", $query);
		foreach($tab as $ligne) {
			$ligne = trim($ligne, " \n\t\r");
			$ligne = rtrim($ligne, " \n\t\r");
			if (strlen($ligne)) {
				try {
					$db->query($ligne);
				}
				catch (Exception $e) {
					throw new USVN_Exception("Can't load file at:\n$ligne");
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
	public function deleteAllTables($db)
	{
		$todelete = $db->listTables();
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
