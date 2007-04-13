<?php
/**
 * Installation operations
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package install
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class Install
{
	/**
	* This method will test connection to the database, load database schemas
	* and finally write the config file.
	*
	* Throw an exception in case of problems.
	*
	* @param Path to the USVN config file
	* @param Database host
	* @param Database user
	* @param Database password
	* @param Database name
	* @param Database table prefix (ex: usvn_)
	*/
	static public function installDb($config_file, $host, $user, $password, $database, $prefix)
	{
		$params = array ('host' => $host,
			 'username' => $user,
			 'password' => $password,
			 'dbname'   => $database);
		try {
			$db = Zend_Db::factory('PDO_MYSQL', $params);
			$db->beginTransaction();
		}
		catch (Exception $e) {
			throw new USVN_Exception(T_("Can't connect to database.\n") ." ". $e->getMessage());
		}
		Zend_Db_Table::setDefaultAdapter($db);
		USVN_Db_Table::$prefix = $prefix;
		try {
			USVN_Db_Utils::loadFile($db, "SQL/SVNDB.sql");
			USVN_Db_Utils::loadFile($db, "SQL/mysql.sql");
			USVN_Db_Utils::loadFile($db, "SQL/data.sql");
		}
		catch (Exception $e) {
			throw new USVN_Exception(T_("Can't load SQL file.\n") ." ". $e->getMessage());
			$db->rollBack();
		}
		try {
			$config = new USVN_Config($config_file, 'general', array("create" => true));
		}
		catch (Exception $e) {
			throw new USVN_Exception(T_("Can't write config file.\n") ." ". $e->getMessage());
			$db->rollBack();
		}
		$config->database = array (
			"adapterName" => "pdo_mysql",
			"prefix" => $prefix,
			"options" => array (
				"host" => $host,
				"username" => $user,
				"password" => $password,
				"dbname" => $database
			)
		);
		$config->save();
		$db->commit();
	}
}
