<?php
/**
 * Root for installation
 *
 * @author Jean-Philippe Meunier <contact@usvn.info>
 * @version 1.0
 * @copyright USVN Team, 23 April, 2009
 * @package intall
 **/

header("Content-encoding: UTF-8");

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
define('USVN_CONFIG_VERSION',   '1.0.0');

set_include_path(USVN_LIB_DIR . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();
require_once USVN_APP_DIR . '/functions.php';
require_once USVN_APP_DIR . '/install/install.php';
$GLOBALS['language'] = 'en_US';


if (file_exists(USVN_CONFIG_FILE))
{
	try
	{
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, 'general');
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
USVN_Translation::initTranslation($GLOBALS['language'], USVN_LOCALE_DIRECTORY);

$installSteps = array(
 	1 => ('System Check'),
 	2 => ('Language Selection'),
 	3 => ('License Agreement'),
 	4 => ('USVN Configuration'),
 	5 => ('Database Installation'),
 	6 => ('Administrator User Creation'),
 	7 => ('Check for a Newer Version'),
 	8 => ('Installation is over'));

//------------------------------------------------------------------------------------------------

include 'views/head.html';

try
{
	$install_is_possible = Install::installPossible(USVN_CONFIG_FILE);
}
catch (USVN_Exception $e)
{
	displayError($e->getMessage());
	include 'views/footer.html';
	exit(0);
}
if ($install_is_possible)
{
	if (!isset($_GET['step']))
		$step = 0;
	else
		$step = $_GET['step'];
	try
	{
		installationOperation($step);
	}
	catch (USVN_Exception $e)
	{
		displayError($e->getMessage());
		$step--;
	}
	include 'views/steps.php';
	installationStep($step);
}
else
	displayError(T_('USVN is already install.'));

include 'views/footer.html';

//------------------------------------------------------------------------------------------------

function displayError($message)
{
	echo "<div class='usvn_error'>" . nl2br($message) . '</div>';
}

function displayWarning($message)
{
	echo "<div class='usvn_warning'>" . nl2br($message) . '</div>';
}

function installationOperation(&$step)
{
	global $errors;
	$language = isset($_POST['language']) ? $_POST['language'] : $GLOBALS['language'];
	switch ($step)
	{
		case 1:
			$errors = Install::check();
			break;

		case 2:
			$errors = Install::check();
			Install::installUrl(USVN_CONFIG_FILE, USVN_HTACCESS_FILE, $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'], isset($_SERVER['HTTPS']));
			break;

		case 3:
			Install::installLanguage(USVN_CONFIG_FILE, $language);
			Install::installTimezone(USVN_CONFIG_FILE, $_POST['timezone']);
			Install::installLocale(USVN_CONFIG_FILE);
			$GLOBALS['language'] = $_POST['language'];
			USVN_Translation::initTranslation($GLOBALS['language'], USVN_LOCALE_DIRECTORY);
		break;

		case 4:
			if ($_POST['agreement'] != 'ok')
				throw new USVN_Exception(T_('You need to accept the licence to continue installation.'));
		break;

		case 5:
			Install::installConfiguration(USVN_CONFIG_FILE, $_POST['title']);
			$import = Install::installSubversion(USVN_CONFIG_FILE, $_POST['pathSubversion'], $_POST['passwdFile'], $_POST['authzFile'], $_POST['urlSubversion'], $_FILES['configFile']);
			if ($import === true) {
				displayWarning(T_("A copy of the old SVN repository has been made, any modification done on the old one won't interact on the new one. You should disable the old repository access."));
				$step = 7;
			}
		break;

		case 6:
			if (isset($_POST['createdb']))
				$createdb = true;
			else
				$createdb = false;
			Install::installDb(USVN_CONFIG_FILE, USVN_APP_DIR . '/install/sql/', $_POST['host'], $_POST['user'], $_POST['password'], $_POST['database'], $_POST['prefix'], $_POST['adapter'], $createdb);
		break;

		case 7:
			Install::installAdmin(USVN_CONFIG_FILE, $_POST['login'], $_POST['password'], $_POST['firstname'], $_POST['lastname'], $_POST['email']);
		break;

		case 8:
			Install::installCheckForUpdate(USVN_CONFIG_FILE, $_POST['update']);
			Install::installEnd(USVN_CONFIG_FILE);
			$GLOBALS['apacheConfig'] = Install::getApacheConfig(USVN_CONFIG_FILE);
		break;
	}
}

function installationStep($step)
{
	global $errors;
	global $installSteps;
	$language = $GLOBALS['language'];
	if ($step >= 0 && $step <= 8)
		include "views/step$step.html";
}
?>
