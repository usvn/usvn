<?php

if (is_dir(dirname(__FILE__) . '/library')) {
	set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/library');
}

require_once 'Zend.php';
require_once dirname(__FILE__) . '/modules/_default/controllers/IndexController.php';

/**
 * Autoload a class when requested.
 * 
 * This is a PHP magic function which is call
 * when a script use a class that does not exist.
 *
 * @param string $class
 */
function __autoload($class)
{
	Zend::loadClass($class);
}

try {
	/**
	 * Load our ini conf file
	 */
	$config = new Zend_Config_Ini(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);

	/**
	 * Configure our default db adapter
	 */
	Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapterName, $config->database->options->asArray()));


	/**
	 * Get back the front controller and initialize some values
	 */
	$front = Zend_Controller_Front::getInstance();
	$front->setParam('view', new Zend_View());
	$front->throwExceptions(true);

	$front->setBaseUrl(BASE_URL);

	/**
	 * Initialize router
	 */
	$router = new Zend_Controller_Router_Rewrite();
	$router->addConfig($config, 'routes');

	$front->setRouter($router);

	/**
	 * Configure current modules
	 */
	$tmp = $modules = array();
	$glob_path = '{' . USVN_DIRECTORY . ',' . dirname(__FILE__) . '}/modules/[a-zA-Z0-9]*';
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
	$modules['default'] = dirname(__FILE__) . '/modules/_default/controllers';
	$front->setControllerDirectory($modules);

	$tmp = array();
	$glob_path = '{' . USVN_DIRECTORY . ',' . dirname(__FILE__) . '}/plugins/[a-zA-Z0-9]*.php';
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
