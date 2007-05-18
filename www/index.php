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

define('USVN_DIRECTORY', dirname(__FILE__) . '/USVN');
define('USVN_LIBRARY_DIRECTORY', dirname(__FILE__) . '/library');
define('USVN_LOCALE_DIRECTORY', dirname(__FILE__) . '/locale');
define('USVN_MEDIAS_DIRECTORY', dirname(__FILE__) . '/medias');
define('USVN_CONFIG_FILE', dirname(__FILE__) . '/config.ini');
define('USVN_CONFIG_SECTION', 'general');
define('USVN_ROUTES_CONFIG_FILE', USVN_DIRECTORY . '/routes.ini');
define('USVN_CONTROLLERS_DIR', dirname(__FILE__) . '/controllers/');
define('USVN_VIEWS_DIR', dirname(__FILE__) . '/views/');
define('USVN_MENUS_DIR', dirname(__FILE__) . '/menus/');

if (!file_exists(USVN_CONFIG_FILE)) {
	header("Location: install");
	exit(0);
}

require_once USVN_DIRECTORY . '/bootstrap.php';
