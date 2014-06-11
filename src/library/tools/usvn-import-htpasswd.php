#!/usr/bin/env php
<?php
/**
 * Command line tools for import password into USVN
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6.4
 * @package tools
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../app'));

require_once '../../app/bootstrap.php';

if (!isset($argv[2]) || isset($argv[3])) {
	echo "Usage: usvn-import-htpasswd.php config-file htpasswd-file\n";
	exit(1);
}
$configfile = $argv[1];
$htpasswdfile = $argv[2];


try {
	$config = new USVN_Config_Ini($configfile, 'general');
	USVN_Translation::initTranslation($config->translation->locale, APPLICATION_PATH . '/locale');
	Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapterName, $config->database->options->toArray()));
	Zend_Db_Table::getDefaultAdapter()->getProfiler()->setEnabled(true);
	USVN_Db_Table::$prefix = $config->database->prefix;
	Zend_Registry::set('config', $config);
	$import = new USVN_ImportHtpasswd($htpasswdfile);
}
catch (Exception $e) {
	echo $e->getMessage() . "\n";
	exit(1);
}

exit(0);