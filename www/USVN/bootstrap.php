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

try {
	/**
	 * Load our ini conf file
	 */
	$config = new USVN_Config(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
	$routes_config = new USVN_Config(USVN_ROUTES_CONFIG_FILE, USVN_CONFIG_SECTION);

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
	Zend_Registry::set('url', array('title' 		=> $config->url->title,
									'description' 	=> $config->url->description,
									'keywords'		=> $config->url->keywords));
	Zend_Registry::set('site', array('name' 		=> $config->site->name,
									'ico'			=> $config->site->ico,
									'description'	=> $config->site->description,
									'logo'			=> $config->site->logo));

	/**
	 * Configure our default db adapter
	 */
	Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapterName, $config->database->options->asArray()));
	Zend_Db_Table::getDefaultAdapter()->getProfiler()->setEnabled(true);
	if (isset($config->database->prefix)) {
		USVN_Db_Table::$prefix = $config->database->prefix;
	}


	/**
	 * Get back the front controller and initialize some values
	 */
	$front = Zend_Controller_Front::getInstance();
	$front->setParam('view', new Zend_View());
	$front->throwExceptions(true);

	$front->setBaseUrl($config->url->base);

	/**
	 * Initialize router
	 */
	$router = new Zend_Controller_Router_Rewrite();
	$router->addConfig($routes_config, 'routes');

	$front->setRouter($router);

	/**
	 * Configure current modules
	 */
	$tmp = $modules = array();
	//  ca posait trop de confusion d'avoir deux repertoire de modules alors on supprime le global
	//	$glob_path = '{' . USVN_DIRECTORY . ',' . dirname(__FILE__) . '}/modules/[a-zA-Z0-9]*';
	$glob_path = USVN_DIRECTORY . '/modules/[a-zA-Z0-9]*';
	foreach (glob($glob_path, GLOB_BRACE | GLOB_ONLYDIR) as $path) {
		$module = basename($path);
		if (isset($tmp[$module])) {
			continue;
		}
		$tmp[$module] = true;
		$modules[$module] = $path .'/controllers';
		if (isset($config->$module) && isset($config->$module->routes)) {
			$router->addConfig($config->$module, 'routes');

		}
	}
	//	$modules['default'] = dirname(__FILE__) . '/modules/_default/controllers';
	$front->setControllerDirectory($modules);

	$tmp = array();
	//  ca posait trop de confusion d'avoir deux repertoire de plugins alors on supprime le global
	//	$glob_path = '{' . USVN_DIRECTORY . ',' . dirname(__FILE__) . '}/plugins/[a-zA-Z0-9]*.php';
	$glob_path = USVN_DIRECTORY . '/plugins/[a-zA-Z0-9]*.php';
	foreach (glob($glob_path, GLOB_BRACE) as $path) {
		$plugin = basename($path);
		if (isset($tmp[$plugin])) {
			continue;
		}
		$tmp[$plugin] = true;
		$class = substr($plugin, 0, -4);

		require_once $path;
		$front->registerPlugin(new $class());
	}

	$front->dispatch();

} catch (Exception $e) {
	echo $e->getMessage();
}
