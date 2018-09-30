<?php

/* Defines */
define('USVN_BASE_DIR',           realpath(dirname(__FILE__) . '/..'));

/* ROOTS */
define('USVN_APP_DIR',            USVN_BASE_DIR . '/app');
define('USVN_LIB_DIR',            USVN_BASE_DIR . '/library');
define('USVN_PUB_DIR',            USVN_BASE_DIR . '/public');
define('USVN_CONFIG_DIR',         USVN_BASE_DIR . '/config');

/* Libraries */
define('ZEND_DIRECTORY',          USVN_LIB_DIR . '/Zend');
define('USVN_DIRECTORY',          USVN_LIB_DIR . '/USVN');
define('USVN_ROUTES_CONFIG_FILE', USVN_APP_DIR . '/routes.ini');

/* Application */
define('USVN_CONTROLLERS_DIR',    USVN_APP_DIR . '/controllers');
define('USVN_HELPERS_DIR',        USVN_APP_DIR . '/helpers');
define('USVN_VIEWS_DIR',          USVN_APP_DIR . '/views/scripts');
define('USVN_LAYOUTS_DIR',        USVN_APP_DIR . '/layouts');
define('USVN_MODEL_DIR',          USVN_APP_DIR . '/models');
define('USVN_MEDIAS_DIR',         USVN_PUB_DIR . '/medias/');
define('USVN_LOCALE_DIR',         USVN_APP_DIR . '/locale');

/* Config */
define('USVN_CONFIG_FILE',        USVN_CONFIG_DIR . '/config.ini');
define('USVN_CONFIG_SECTION',     'general');
define('USVN_CONFIG_VERSION',     '1.0.8');


/* Misc */
define('USVN_URL_SEP', ':');
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

/* Necessary Includes */
set_include_path(USVN_LIB_DIR . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace("Zend_");
$autoloader->registerNamespace("USVN_");
$autoloader->registerNamespace("menus_");

require_once 'functions.php';

/* Config Loading or Installation */
try
{
	$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
	if (empty($config) && file_exists(USVN_CONFIG_FILE))
	{
		echo 'Config file unreadeable';
		exit(0);
	}
	if (!isset($config->version))
	{
		header('Location: install.php');
		exit(0);
	}
	if ($config->version != USVN_CONFIG_VERSION)
	{
		USVN_Update::runUpdate();
	}
}
catch (Exception $e)
{
	header('Location: install.php');
	// echo '<pre>' . "\n";
	// echo $e;
	// echo '</pre>' . "\n";
	exit(0);
}

/* USVN Configuration */
date_default_timezone_set($config->timezone);

USVN_ConsoleUtils::setLocale($config->system->locale);
USVN_Translation::initTranslation($config->translation->locale, USVN_LOCALE_DIR);
USVN_Template::initTemplate($config->template->name, USVN_MEDIAS_DIR);

/* Zend Configuration */
Zend_Registry::set('config', $config);
Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapterName, $config->database->options->toArray()));
if (isset($config->database->prefix)) {
	USVN_Db_Table::$prefix = $config->database->prefix;
}

$front = Zend_Controller_Front::getInstance();
Zend_Layout::startMvc(array('layoutPath' => USVN_LAYOUTS_DIR));

$front->setRequest(new USVN_Controller_Request_Http());
$front->throwExceptions(true);
$front->setBaseUrl($config->url->base);

/* Initialize router */
$router = new Zend_Controller_Router_Rewrite();
$routes_config = new USVN_Config_Ini(USVN_ROUTES_CONFIG_FILE, USVN_CONFIG_SECTION);
$router->addConfig($routes_config, 'routes');
$front->setRouter($router);
$front->setControllerDirectory(USVN_CONTROLLERS_DIR);
