#!/usr/bin/env php
<?php

/****************/
/* Include USVN */
/****************/

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../app'));
if (!defined('USVN_BASE_DIR'))
{
  define('USVN_BASE_DIR', realpath(dirname(__FILE__) . '/../../../'));
}

define('USVN_APP_DIR',          USVN_BASE_DIR   . '/app');
define('USVN_LIB_DIR',          USVN_BASE_DIR   . '/library');
define('USVN_PUB_DIR',          USVN_BASE_DIR   . '/public');
define('USVN_CONFIG_DIR',       USVN_BASE_DIR   . '/config');
define('USVN_FILES_DIR',        USVN_BASE_DIR   . '/files');

define('USVN_CONFIG_FILE',      USVN_CONFIG_DIR . '/config.ini');
define('USVN_HTACCESS_FILE',    USVN_PUB_DIR    . '/.htaccess');
define('USVN_LOCALE_DIRECTORY', USVN_APP_DIR    . '/locale');

// Define application environment
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

define('USVN_BASE_DIR',         realpath(dirname(__FILE__) . '/../..'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('USVN_');
$autoloader->setFallbackAutoloader(true);

/********************/
/* End Include USVN */
/********************/

if (file_exists(USVN_CONFIG_FILE))
{
	try
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		if (isset($config->translation->locale))
			$GLOBALS['language'] = $config->translation->locale;
		if (isset($config->timezone))
			date_default_timezone_set($config->timezone);
		if (isset($config->system->locale))
			USVN_ConsoleUtils::setLocale($config->system->locale);
		if (isset($config->database->adapterName))
		{
			Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapterName, $config->database->options->toArray()));
			Zend_Db_Table::getDefaultAdapter()->getProfiler()->setEnabled(true);
			USVN_Db_Table::$prefix = $config->database->prefix;
		}
		Zend_Registry::set('config', $config);
	}
	catch (Exception $e)
	{
	}
}

// Begin code

// Auto-generated
$projectId = '5';
$hooksPath = '/Users/naixn/Sites/usvn/files/hooks/';
//

$table = new USVN_Db_Table_Projects();
$project = $table->find($projectId)->current();
$hooks = $project->findManyToManyRowset('USVN_Db_Table_Hooks', 'USVN_Db_Table_ProjectsToHooks');
foreach ($hooks as $hook)
{
	$hook = "$hooksPath/$hook->path";
	echo `$hook`;
}
