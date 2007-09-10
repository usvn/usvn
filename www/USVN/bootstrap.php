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
 * $Id$
 */

if (is_dir(dirname(__FILE__) . '/library')) {
	set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/library');
}

require_once dirname(__FILE__) . '/autoload.php';
date_default_timezone_set('UTC');
error_reporting(E_ALL | E_STRICT);

	/**
	 * Load our ini conf file
	 */
	try {
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		if (!isset($config->version)) {
			header("Location: install");
			exit(0);
		}
		if ($config->version != "0.6.4") {
			header("Location: {$config->url->base}/update/{$config->version}/");
			exit(0);
		}
	}
	catch (Exception $e) {
		header("Location: install");
		exit(0);
	}

	date_default_timezone_set($config->timezone);

	USVN_ConsoleUtils::setLocale($config->system->locale);

	$routes_config = new USVN_Config_Ini(USVN_ROUTES_CONFIG_FILE, USVN_CONFIG_SECTION);

	/**
	 * Configure language
	 */
	USVN_Translation::initTranslation($config->translation->locale, USVN_LOCALE_DIRECTORY);

	/**
	 * Configure template
	 */
	USVN_Template::initTemplate($config->template->name, USVN_MEDIAS_DIRECTORY);

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
	$router->addConfig($routes_config, 'routes');

	$front->setRouter($router);

	$front->setControllerDirectory(USVN_CONTROLLERS_DIR);

	$front->registerPlugin(new USVN_plugins_layout());

	$front->dispatch();
