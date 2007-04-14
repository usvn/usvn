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
	private static function _loadConfig($config_file)
	{
		try {
			return new USVN_Config($config_file, 'general', array("create" => true));
		}
		catch (Exception $e) {
			throw new USVN_Exception(T_("Can't write config file %s.\n") ." ". $e->getMessage(),  $config_file);
		}
	}

	/**
	* This method will test connection to the database, load database schemas
	* and finally write the config file.
	*
	* Throw an exception in case of problems.
	*
	* @param string Path to the USVN config file
	* @param string Database host
	* @param string Database user
	* @param string Database password
	* @param string Database name
	* @param string Database table prefix (ex: usvn_)
	* @throw USVN_Exception
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
			$config = Install::_loadConfig($config_file);
		}
		catch (Exception $e) {
			throw new USVN_Exception(T_("Can't write config file %s.\n") ." ". $e->getMessage(),  $config_file);
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

	/**
	* This method will  write the choosen language into config file.
	*
	* Throw an exception in case of problems.
	*
	* @param string Path to the USVN config file
	* @param string Language
	* @throw USVN_Exception
	*/
	static public function installLanguage($config_file, $language)
	{
		if (in_array($language, USVN_Translation::listTranslation())) {
			$config = Install::_loadConfig($config_file);
			$config->translation = array("locale"  => $language);
			$config->save();
		}
		else {
			throw new USVN_Exception(T_("Invalid language"));
		}
	}

	/**
	* This method will write htaccess and config file with urls informations
	*
	* Throw an exception in case of problems.
	*
	* @param string Path to the USVN config file
	* @param string Path to the USVN htaccess file
	* @throw USVN_Exception
	*/
	static public function installUrl($config_file, $htaccess_file)
	{
		$path = str_replace("/install", "", $_SERVER['REQUEST_URI']);
		$config = Install::_loadConfig($config_file);
		$config->url = array(
			"base" => $path,
			"host" => $_SERVER['SERVER_NAME']
		);
		$config->save();
		$content = <<<EOF
<Files *.ini>
Order Allow,Deny
Deny from all
</Files>
RewriteEngine on
RewriteBase {$path}
RewriteRule !\.(js|ico|gif|jpg|png|css)$ index.php
EOF;
		if (@file_put_contents($htaccess_file, $content) === false) {
			throw new USVN_Exception(T_("Can't write htaccess file %s.\n"),  $htaccess_file);
		}
	}

	/**
	* This method will write create an admin
	*
	* Throw an exception in case of problems.
	*
	* @param string Path to the USVN config file
	* @param string login
	* @param string password
	* @throw USVN_Exception
	*/
	static public function installAdmin($config_file, $login, $password)
	{
		$userTable = new USVN_Db_Table_Users();
		$user = $userTable->insert(array(
									'users_login' => $login,
		   							'users_password' => crypt($password)
										)
                                 );
	}

	/**
	* Mark USVN install
	*
	* @param string Path to the USVN config file
	* @throw USVN_Exception
	*/
	static public function installEnd($config_file)
	{
		$config = Install::_loadConfig($config_file);
		$config->version = "0.5";
		$config->save();
	}

	/**
	* Return true if USVN is not already install.
	*
	* @param string Path to the USVN config file
	* @return boolean
	* @throw USVN_Exception
	*/
	static public function installPossible($config_file)
	{
		if (!file_exists($config_file)) {
			return true;
		}
		$config = Install::_loadConfig($config_file);
		if (!isset($config->version)) {
			return true;
		}
		return false;
	}
}
