<?php
/**
 * Setup USVN environement
 *
 * Now if try new USVN_tutu() it will load require file USVN/tutu.php
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
 * $Id: bootstrap.php 1536 2008-11-01 16:08:37Z duponc_j $
 */

$globalUSVNLogs = array();
function USVNLogObject($name, $value)
{
	global $globalUSVNLogs;

	$globalUSVNLogs[] = array('name' => $name, 'value' => $value);
}
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('UTC');

define('USVN_APP_DIR', dirname(__FILE__));
define('USVN_LIB_DIR', dirname(__FILE__) . '/../library');
define('USVN_PUB_DIR', dirname(__FILE__) . '/../public');

define('USVN_DIRECTORY', USVN_LIB_DIR . '/USVN');
define('USVN_CONFIG_FILE', dirname(__FILE__) . '/../config/config.ini');
define('USVN_CONFIG_SECTION', 'general');
define('USVN_LOCALE_DIRECTORY', USVN_APP_DIR . '/locale');
define('USVN_ROUTES_CONFIG_FILE', USVN_DIRECTORY . '/routes.ini');

define('USVN_CONTROLLERS_DIR', USVN_APP_DIR . '/controllers');
define('USVN_HELPERS_DIR',     USVN_APP_DIR . '/helpers');
define('USVN_VIEWS_DIR',       USVN_APP_DIR . '/views');
define('USVN_MEDIAS_DIR',      USVN_PUB_DIR . '/medias');

/*TEMP*/
define('USVN_MENUS_DIR', USVN_LIB_DIR . '/menus');
define('USVN_MEDIAS_DIRECTORY', USVN_MEDIAS_DIR);
/*!TEMP*/

define('USVN_URL_SEP', ':');

set_include_path(
	USVN_LIB_DIR . PATH_SEPARATOR
	. get_include_path());

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();
//spl_autoload_register(array('Zend_Loader', 'autoload'));

function T_($str)
{
	return USVN_Translation::_($str);
}

/**
 * Load our ini conf file
 */
try {
	$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
	if (!isset($config->version)) {
		header("Location: install");
		exit(0);
	}
	if ($config->version != "0.7.2") {
		header("Location: {$config->url->base}/update/{$config->version}/");
		exit(0);
	}
}
catch (Exception $e) {
	echo '<pre>';
	echo USVN_CONFIG_FILE . "\n";
	echo $e . "\n";
	echo '</pre>';
	// header("Location: install");
	exit(0);
}

date_default_timezone_set($config->timezone);

USVN_ConsoleUtils::setLocale($config->system->locale);

/**
 * Configure language
 */
USVN_Translation::initTranslation($config->translation->locale, USVN_LOCALE_DIRECTORY);


/**
 * register info
 */
Zend_Registry::set('config', $config);

/**
 * Configure our default db adapter
 */
Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapterName, $config->database->options->toArray()));
if (isset($config->database->prefix)) {
	USVN_Db_Table::$prefix = $config->database->prefix;
}


/*
** Previous index.php
*/

/**
 * Configure template
 */
USVN_Template::initTemplate($config->template->name, USVN_MEDIAS_DIR);

try {
	/**
	 * Get back the front controller and initialize some values
	 */
	$front = Zend_Controller_Front::getInstance();
	$front->setRequest(new USVN_Controller_Request_Http());

	$front->throwExceptions(true);

	$front->setBaseUrl($config->url->base);

	/**
	 * Initialize router
	 */
	$router = new Zend_Controller_Router_Rewrite();

	$routes_config = new USVN_Config_Ini(USVN_ROUTES_CONFIG_FILE, USVN_CONFIG_SECTION);

	$router->addConfig($routes_config, 'routes');

	$front->setRouter($router);

	$front->setControllerDirectory(USVN_CONTROLLERS_DIR);

	$front->registerPlugin(new USVN_plugins_layout());

	$front->dispatch();
}
catch (Zend_Controller_Dispatcher_Exception $e)
{
	try {

		/**
		 * Get back the front controller and initialize some values
		 */
		Zend_Controller_Front::getInstance()->resetInstance();
		$front = Zend_Controller_Front::getInstance();
		$request = new USVN_Controller_Request_Http();

		$front->setRequest($request);

		$front->throwExceptions(true);

		$front->setBaseUrl($config->url->base);
		$request->setBasePath($config->url->base);
		$request->setBaseUrl($config->url->base);
		$request->setRequestUri($config->url->base . "/404/");

		/**
		 * Initialize router
		 */
		$router = new Zend_Controller_Router_Rewrite();
		$router->addConfig($routes_config, 'routes');

		$front->setRouter($router);

		$front->setControllerDirectory(USVN_CONTROLLERS_DIR);

		$front->registerPlugin(new USVN_plugins_layout());

		$front->dispatch();
	}
	catch (Exception $e) {
		echo $e->getMessage();
	}
}
catch (Exception $e) {
	echo $e->getMessage() . '<br/><br/>Stacktrace:<br />' . nl2br($e->getTraceAsString());
	if (count($globalUSVNLogs) != 0)
	{
		echo '<table style="border-collapse:collapse">';
		foreach ($globalUSVNLogs as $logRow)
		{
			echo '<tr style="border-top: 1px solid black;border-bottom: 1px solid black;vertical-align:top;"><td>' . $logRow['name'] . '</td><td><pre>';
			var_dump($logRow['value']);
			echo '</pre></td></tr>';
		}
		echo '</table>';
	}
}

