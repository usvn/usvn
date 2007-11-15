#!/usr/bin/env php
<?php
/**
 * Import SVN repositories
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7.0
 * @package utils
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: usvn-import-svn-repositories.php 632 2007-10-17 15:51:08Z dolean_j $
 */

include_once('../USVN/autoload.php');

/**
 * Initialize some configurations details
 */
try {
	$config = new USVN_Config_Ini("../config.ini", "general");
	Zend_Registry::set('config', $config);

	USVN_Translation::initTranslation($config->translation->locale, "../locale");
	date_default_timezone_set($config->timezone);

	$db = Zend_Db::factory($config->database->adapterName, $config->database->options->toArray());
	USVN_Db_Table::$prefix = $config->database->prefix;
	Zend_Db_Table::setDefaultAdapter($db);

	Zend_Registry::set('config', $config);
}
catch (Exception $e) {
	echo $e->getMessage() . "\n";
	exit(1);
}
/**
 * Get options and directories paths to check
 */
$results = array();
$repos = array();
$paths = array();
$usage = "Usage: $argv[0] [--recursive] [--verbose] [--creategroup] [--addmetogroup] [--admin] [--noimport] [login] path1 path2 path3 [...]\n";
if (count($argv) == 1) {
	die($usage);
}

$options = array('recursive' => false,
					'verbose' => false,
					'login' => 0,
					'creategroup' => 0,
					'addmetogroup' => 0,
					'admin' => 0,
					'noimport' => 0);

foreach ($argv as $arg) {
	switch ($arg) {
		case '--verbose':
			$options['verbose'] = true;
			break;
		case '--recursive':
			$options['recursive'] = true;
			break;
		case '--creategroup':
			$options['creategroup'] = 1;
			break;
		case '--addmetogroup':
			$options['addmetogroup'] = 1;
			break;
		case '--admin':
			$options['admin'] = 1;
			break;
		case '--noimport':
			$options['noimport'] = 1;
			break;
		default:
			if (is_dir($arg)) {
				$paths[] = $arg;
			} elseif (preg_match('/^--/', $arg)) {
				die ("'$arg' option unknown\n");
			} elseif ($arg != $argv[0] && preg_match('/\w+/', $arg)) {
				$options['login'] = $arg;
			} else {
				if ($arg != $argv[0]) {
					die ($usage);
				}
			}
			break;
	}
}

/**
 * Look for SVN repositories to import into USVN and finally perform the action
 */

$svnImport = new USVN_ImportSVNRepositories();
foreach ($paths as $path) {
	$tmp = $svnImport->lookAfterSVNRepositoriesToImport($path, $options);
	if (count($tmp)) {
		$repos = array_merge($repos, $tmp);
	} else {
		if ($options['verbose']) {
			print "No SVN repository here: $path\n";
		}
	}
}

//if we don't have any repository to import then die
if (!count($repos)) {
	die($usage);
}

/**
 * Now we can import (if there is no option 'noimport') our SVN Repositories into USVN
 */
if (!$options['noimport']) {
	$svnImport->addSVNRepositoriesToImport($repos, $options);
	$results = $svnImport->importSVNRepositories();
	print "SVN repositories imported:\n".implode("\n", $repos);
} else {
	print "SVN repositories ready to import:\n".implode("\n", $repos);
}

exit(0);
