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
	* @param string Path to the SQL files
	* @param string Database host
	* @param string Database user
	* @param string Database password
	* @param string Database name
	* @param string Database table prefix (ex: usvn_)
	* @throw USVN_Exception
	*/
	static public function installDb($config_file, $path_sql, $host, $user, $password, $database, $prefix)
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
			USVN_Db_Utils::loadFile($db, $path_sql . "/SVNDB.sql");
			USVN_Db_Utils::loadFile($db, $path_sql . "/mysql.sql");
			USVN_Db_Utils::loadFile($db, $path_sql . "/data.sql");
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
		ereg("(.*)/install.*", $_SERVER['REQUEST_URI'], $regs);
		$path =$regs[1];
		$config = Install::_loadConfig($config_file);
		if (!isset($config->url)) {
			$config->url = array();
		}
		$config->url->base = $path;
		$config->url->host = $_SERVER['SERVER_NAME'];
		$config->save();
		$content = <<<EOF
<Files *.ini>
Order Allow,Deny
Deny from all
</Files>
RewriteEngine on
RewriteCond %{REQUEST_URI} !/install*
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
	* @param string Admin login
	* @param string Admin password
	* @param string Admin first name
	* @param string Admin last name
	* @throw USVN_Exception
	*/
	static public function installAdmin($config_file, $login, $password, $firstname, $lastname)
	{
		$userTable = new USVN_Db_Table_Users();
		$user = $userTable->fetchNew();
		$user->setFromArray(array(
									'users_login' => $login,
		   							'users_password' => crypt($password),
									'users_firstname' => $firstname,
									'users_lastname' => $lastname
									)
                                 );
		$user->save();
		$groupTable = new USVN_Db_Table_Groups();
		$group = $groupTable->fetchRow(array('groups_name = ?' => 'Admins'));
		if ($group === null) {
			throw new USVN_Exception(T_('Group %s not found.'), 'Admins');
		}
		$group->addUser($user);
		$group = $groupTable->fetchRow(array('groups_name = ?' => 'Users'));
		if ($group === null) {
			throw new USVN_Exception(T_('Group %s not found.'), 'Users');
		}
		$group->addUser($user);
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

	/**
	* Some configurations informations
	*
	* @param string Path to the USVN config file
	* @param string USVN page title
	* @param string USVN description
	* @return boolean
	* @throw USVN_Exception
	*/
	static public function installConfiguration($config_file, $title, $description)
	{
		$title = rtrim($title);
		if (strlen($title) == 0) {
			throw new USVN_Exception(T_("Need a title."));
		}
		$config = Install::_loadConfig($config_file);
		$config->template = array("name" => "default");
		if (!isset($config->site)) {
			$config->site = array();
		}
		$config->site->title = strip_tags($title);
		$config->site->ico = "medias/default/images/USVN.ico";
		$config->site->description = strip_tags($description);
		$config->site->logo = "medias/default/images/USVN-logo.png";
		if (!isset($config->url)) {
			$config->url = array();
		}
		$config->url->title = strip_tags($title);
		$config->url->description = strip_tags($description);
		$config->save();
	}
}
