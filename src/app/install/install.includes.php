<?php
/**
 * Includes for both Web and CLI install
 *
 * @author Thibault Martin-Lagardette
 * @version 1.0
 * @copyright USVN Team, 29 September, 2009
 * @package install
 **/

define('USVN_BASE_DIR',         realpath(dirname(__FILE__) . '/../..'));
define('USVN_APP_DIR',          USVN_BASE_DIR   . '/app');
define('USVN_LIB_DIR',          USVN_BASE_DIR   . '/library');
define('USVN_PUB_DIR',          USVN_BASE_DIR   . '/public');
define('USVN_CONFIG_DIR',       USVN_BASE_DIR   . '/config');
define('USVN_FILES_DIR',        USVN_BASE_DIR   . '/files');

define('USVN_CONFIG_FILE',      USVN_CONFIG_DIR . '/config.ini');
define('USVN_HTACCESS_FILE',    USVN_PUB_DIR    . '/.htaccess');
define('USVN_LOCALE_DIRECTORY', USVN_APP_DIR    . '/locale');

define('USVN_CONFIG_SECTION',   'general');
define('USVN_CONFIG_VERSION',   '1.0.8');

set_include_path(USVN_LIB_DIR . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace("Zend_");
$autoloader->registerNamespace("USVN_");
require_once USVN_APP_DIR . '/functions.php';
require_once USVN_APP_DIR . '/install/install.class.php';

$GLOBALS['language'] = 'en_US';

?>
