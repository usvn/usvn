<?php
/**
 * Root for installation
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package install
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

define('CONFIG_FILE', "../config.ini");
define('HTACCESS_FILE', "../.htaccess");

header("Content-encoding: UTF-8");

set_include_path(get_include_path() .PATH_SEPARATOR ."..");
require_once 'USVN/autoload.php';
require_once 'Install.php';

$GLOBALS['language'] = 'en_US';
if (file_exists(CONFIG_FILE)) {
	$config = new USVN_Config_Ini(CONFIG_FILE, 'general');
	if (isset($config->translation->locale)) {
		$GLOBALS['language'] = $config->translation->locale;
	}
	if (isset($config->database->adapterName)) {
		Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapterName, $config->database->options->asArray()));
		Zend_Db_Table::getDefaultAdapter()->getProfiler()->setEnabled(true);
		USVN_Db_Table::$prefix = $config->database->prefix;
	}
	Zend_Registry::set('config', $config);
}
USVN_Translation::initTranslation($GLOBALS['language'], '../locale');


include "views/head.html";

if (Install::installPossible(CONFIG_FILE)) {
	if (!isset($_GET['step'])) {
		$step = 1;
	}
	else {
		$step = $_GET['step'];
	}
	try {
		installationOperation($step);
	}
	catch (USVN_Exception $e) {
		echo "<div class='usvn_error'>" . T_("Error"). ": " . nl2br($e->getMessage()) . "</div>";
		$step--;
	}
	installationStep($step);
}
else {
	echo "<div class='usvn_error'>" . T_("Error") . ": " . T_("USVN is already install.") . "</div>";
}

include "views/footer.html";

//------------------------------------------------------------------------------------------------
function installationOperation($step)
{
	$agreement = isset($_POST['agreement']) ? $_POST['agreement'] : $GLOBALS['agreement'];
	$language = isset($_POST['language']) ? $_POST['language'] : $GLOBALS['language'];
	$step = $step == 4 && $agreement != "ok" ? 3 : $step;
	switch ($step) {
			case 3:
				Install::installLanguage(CONFIG_FILE, $language);
				$GLOBALS['language'] = $_POST['language'];
				USVN_Translation::initTranslation($GLOBALS['language'], '../locale');
			break;

			case 4:
				$GLOBALS['agreement'] = $_POST['agreement'];
			break;

			case 5:
				Install::installConfiguration(CONFIG_FILE, $_POST['title']);
				Install::installSubversion(CONFIG_FILE, $_POST['pathSubversion'], $_POST['urlSubversion']);
			break;

			case 6:
				Install::installDb(CONFIG_FILE, "../SQL/", $_POST['host'], $_POST['user'], $_POST['password'], $_POST['database'], $_POST['prefix']);
			break;

			case 7:
				Install::installAdmin(CONFIG_FILE, $_POST['login'], $_POST['password'], $_POST['firstname'], $_POST['lastname']);
				Install::installUrl(CONFIG_FILE, HTACCESS_FILE);
				Install::installEnd(CONFIG_FILE);
				$GLOBALS['apacheConfig'] = Install::getApacheConfig(CONFIG_FILE);
			break;
		}
}

function installationStep($step)
{
	$language = $GLOBALS['language'];
	if ($step >= 1 && $step <= 7) {
		$step = $step == 4 && $GLOBALS['agreement'] != "ok" ? 3 : $step;
		include "views/step$step.html";
	}
}
?>

