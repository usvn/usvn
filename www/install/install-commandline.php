#!/usr/bin/env php
<?php
/**
 * Command line installer for USVN
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6.3
 * @package install
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

require_once 'USVN/autoload.php';
require_once 'install/Install.php';

if (!isset($argv[4]) || isset($argv[5])) {
	echo "Usage: install-commandline.php config-file htaccess-file admin-login admin-password\n";
	exit(1);
}
$configfile = $argv[1];
$htaccessfile = $argv[2];
$login = $argv[3];
$password = $argv[4];


USVN_Translation::initTranslation('en_US', 'locale');
if (!Install::installPossible($configfile)) {
	echo T_("Error") . ": " . T_("USVN is already install.");
	exit(1);
}
try {
	$config = new USVN_Config_Ini($configfile, 'general');
	Zend_Registry::set('config', $config);
	Install::installUrl($configfile, $htaccessfile, "/usvn", "localhost", false);
	Install::installLanguage($configfile, $config->translation->locale);
	Install::installConfiguration($configfile, $config->site->title);
	Install::installSubversion($configfile, $config->subversion->path, $config->subversion->passwd, $config->subversion->authz, $config->subversion->url);
	Install::installDb($configfile, "SQL/", $config->database->options->host, $config->database->options->username, $config->database->options->password, $config->database->options->dbname, $config->database->prefix, $config->database->adapterName, true);
	Install::installAdmin($configfile, $login, $password, 'System', 'Administrator', '');
	Install::installEnd($configfile);
}
catch (USVN_Exception $e) {
	echo $e->getMessage();
	exit(1);
}
exit(0);