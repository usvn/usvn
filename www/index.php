<?php
/**
 * Root of USVN
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

require_once 'USVN/bootstrap.php';
define('USVN_MEDIAS_DIRECTORY', dirname(__FILE__) . '/medias');
define('USVN_ROUTES_CONFIG_FILE', USVN_DIRECTORY . '/routes.ini');
define('USVN_CONTROLLERS_DIR', dirname(__FILE__) . '/controllers/');
define('USVN_VIEWS_DIR', dirname(__FILE__) . '/views/');
define('USVN_MENUS_DIR', dirname(__FILE__) . '/menus/');
define('USVN_HELPERS_DIR', dirname(__FILE__) . '/helpers/');


$routes_config = new USVN_Config_Ini(USVN_ROUTES_CONFIG_FILE, USVN_CONFIG_SECTION);

/**
 * Configure template
 */
USVN_Template::initTemplate($config->template->name, USVN_MEDIAS_DIRECTORY);

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
