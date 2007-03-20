<?php
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
		//Se connecte à la DB (cherche $this->db = Zend_Db::factory('PDO_MYSQL', $params); dans le code tu verra un exemple d'utilisation)
		//Charge le fichier SQL/SVNDB.sql dans la DB (voir USVN_Db_Utils::loadFile)
		//Ecrit le fichier de conf
	}
}
