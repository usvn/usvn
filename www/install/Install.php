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
			return new USVN_Config_Ini($config_file, 'general', array("create" => true));
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
	* @param string Database type (mysql or sqlite)
	* @param boolean Create the database before installing
	* @throw USVN_Exception
	*/
	static public function installDb($config_file, $path_sql, $host, $user, $password, $database, $prefix, $adapter, $createdb)
	{
		$params = array ('host' => $host,
		'username' => $user,
		'password' => $password,
		'dbname'   => $database);

		if ($createdb) {
			try {
				$tmp_params = $params;
				$tmp_params['dbname'] = "mysql";
				$db = Zend_Db::factory('PDO_MYSQL', $tmp_params);
				$db->getConnection();
				$db->query("CREATE DATABASE {$database};");
				$db->closeConnection();
			} catch (Exception $e) {
				throw new USVN_Exception(T_("Can't create database\n") . $e->getMessage());
			}
		}

		try {
			$db = Zend_Db::factory($adapter, $params);
			$db->getConnection();
		}
		catch (Exception $e) {
			throw new USVN_Exception(T_("Can't connect to database.\n") ." ". $e->getMessage());
		}
		Zend_Db_Table::setDefaultAdapter($db);
		USVN_Db_Table::$prefix = $prefix;

		try {
			if ($adapter == "PDO_MYSQL" || $adapter == "MYSQLI") {
				USVN_Db_Utils::loadFile($db, $path_sql . "/SVNDB.sql");
				USVN_Db_Utils::loadFile($db, $path_sql . "/mysql.sql");
			}
			else if ($adapter == "PDO_SQLITE") {
				USVN_Db_Utils::loadFile($db, $path_sql . "/SVNDBSQLITE.sql");
			}
            else {
                throw new USVN_Exception(T_("Invalid adapter %s.\n") . $adapter);
            }
		}
		catch (Exception $e) {
			try {
				USVN_Db_Utils::deleteAllTablesPrefixed($db, $prefix);
			}
			catch (Exception $e2) {
			}
			$db->closeConnection();
			throw new USVN_Exception(T_("Can't load SQL file.\n") . $e->getMessage());
		}
		$db->closeConnection();
		try {
			$config = Install::_loadConfig($config_file);
			/* @var $config USVN_Config */
			$config->database = array (
				"adapterName" => $adapter,
				"prefix" => $prefix,
				"options" => array (
					"host" => $host,
					"username" => $user,
					"password" => $password,
					"dbname" => $database
				)
			);
			$config->save();
		}
		catch (Exception $e) {
			USVN_Db_Utils::deleteAllTablesPrefixed($db, $prefix);
			$db->closeConnection();
			throw new USVN_Exception(T_("Can't write config file %s.\n") ." ". $e->getMessage(),  $config_file);
		}
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
	* This method will add subversion path
	*
	* @param string Path to the USVN config file
	* @param string Path to subversion directory
	* @param string Url of subversion repository
	*/
	static public function installSubversion($config_file, $path, $url)
	{
		if (substr($path, -1) != DIRECTORY_SEPARATOR) {
			$path .= DIRECTORY_SEPARATOR;
		}
		if (substr($url, -1) != '/') {
			$url .= '/';
		}
		$path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
		$config = Install::_loadConfig($config_file);
		if (file_exists($path) && is_writable($path)
		&& (file_exists($path . DIRECTORY_SEPARATOR . 'svn') || mkdir($path . DIRECTORY_SEPARATOR . 'svn'))) {
			$config->subversion = array(
			"path" => $path,
			"url" => $url
			);
			$config->save();
		}
		else {
			throw new USVN_Exception(T_("Invalid subversion path \"%s\", please check if directory exist and is writable."), $path);
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
		preg_match("#(.*)/install.*#", $_SERVER['REQUEST_URI'], $regs);
		$path = $regs[1];
		//here
		if (substr($path, strlen($path) - 1, strlen($path)) == "/") {
			$path = rtrim($path, "/");
		}
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
RewriteBase {$path}/
RewriteRule !\.(js|ico|gif|jpg|png|css)$ index.php

EOF;
		if (@file_put_contents($htaccess_file, $content) === false) {
			throw new USVN_Exception(T_("Can't write htaccess file %s.\n"),  $htaccess_file);
		}
		chmod($htaccess_file, 0644);
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
	* @param string Admin email
	* @throw USVN_Exception
	*/
	static public function installAdmin($config_file, $login, $password, $firstname, $lastname, $email)
	{
		if (empty($password)) {
			throw new USVN_Exception(T_("Password empty"));
		}
		$userTable = new USVN_Db_Table_Users();
		$user = $userTable->createRow();
		$user->login = $login;
		$user->password = $password;
		$user->firstname = $firstname;
		$user->lastname = $lastname;
		$user->email = $email;
		$user->is_admin = true;
		$user->save();
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
		$config->version = "0.6";
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
	* @return boolean
	* @throw USVN_Exception
	*/
	static public function installConfiguration($config_file, $title)
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
		$config->site->logo = "medias/default/images/USVN-logo.png";
		if (!isset($config->url)) {
			$config->url = array();
		}
		$config->save();
	}

	/**
	* Get apache configuration
	*
	* @param string Path to the USVN config file
	* @return string apache config
	*/
	static public function getApacheConfig($config_file)
	{
		$config = Install::_loadConfig($config_file);
		$path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $config->subversion->path . DIRECTORY_SEPARATOR);
		$location = preg_replace("#http[s]?://[^/]*#", "", $config->subversion->url);
		$location = str_replace("//", "/", $location);
		$res = "<Location $location>\n";
		$res .= "\tDAV svn\n";
		$res .= "\tRequire valid-user\n";
		$res .= "\tSVNParentPath " . $path . "svn\n";
		$res .= "\tSVNListParentPath off\n";
		$res .= "\tAuthType Basic\n";
		$res .= "\tAuthName \"" . $config->site->title . "\"\n";
		$res .= "\tAuthUserFile " . $path . "htpasswd\n";
		$res .= "\tAuthzSVNAccessFile " . $path . "authz\n";
		$res .= "</Location>";
		return $res;
	}

	/**
	 * Check if subversion is install on the computer. Else throw exception
	 *
	 * @trhow USVN_Exception
	 */
	static public function checkSystem()
	{
		if (ini_get("safe_mode")) {
			throw new USVN_Exception(T_("USVN can't run with php's safe mode."));
		}

		if (USVN_ConsoleUtils::runCmd('svn --version')) {
			throw new USVN_Exception(T_("Subversion is not install on your system. If you are under Windows install ") . "http://subversion.tigris.org/files/documents/15/36797/svn-1.4.3-setup.exe" . T_(" and after restart your system (WARNING it's mandatory). \n\nOtherwise under UNIX you probably need to install a package named subversion."));
		}

		if (isset($_SERVER['HTTPS'])) {
			$method = "https";
		}
		else {
			$method = "http";
		}
		$image = dirname($method . "://" . $_SERVER['HTTP_HOST'] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER['PHP_SELF']) . "/../medias/default/images/logo.png";
        if (php_sapi_name() != "cli") {
            if (function_exists("apache_get_modules") && !in_array("mod_rewrite", apache_get_modules())) {
                throw new  USVN_Exception(T_("mod_rewrite seems not to be loaded"));
            }
            elseif ((bool) ini_get("allow_url_fopen") && @file_get_contents($image) === false) {
                throw new  USVN_Exception(T_("mod_rewrite seems not to be loaded"));
            }
        }
		if (function_exists("apache_get_modules") && !in_array("mod_dav_svn", apache_get_modules())) {
                throw new  USVN_Exception(T_("mod_dav_svn seems not to be loaded"));
		}
	}
}
