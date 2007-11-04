#!/usr/bin/php
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
 * $Id: importSVNRepositories.php 632 2007-10-17 15:51:08Z dolean_j $
 */

include_once('../www/USVN/autoload.php');

/**
 * Look after SVN repositories to import into USVN
 *
 * @param string $path
 * @param array $options
 * @return array
 */
function lookAfterSVNRepositoriesToImport($path, $options = array()) {
	$path = realpath($path);
	$results = array();
	if (is_dir($path) && USVN_SVNUtils::isSVNRepository($path)) {
		if (isset($options['verbose']) && $options['verbose'] == true) {
				print "OK " . $path . "\n";
			}
		return(array($path));
	}
	$folders = USVN_DirectoryUtils::listDirectory($path);
	foreach ($folders as $folder) {
		$current_path = $path . DIRECTORY_SEPARATOR . $folder;
		if (is_dir($current_path) && USVN_SVNUtils::isSVNRepository($current_path)) {
			if (isset($options['verbose']) && $options['verbose'] == true) {
				print "OK " . $current_path . "\n";
			}
			$results[] = $current_path;
		} else {
			if (isset($options['verbose']) && $options['verbose'] == true) {
				print "KO " . $current_path . "\n";
			}
			if (isset($options['recursive']) && $options['recursive'] == true) {
				$results = array_merge($results, lookAfterSVNRepositoriesToImport($current_path, $options));
			}
		}
	}
	return $results;
}

/**
 * Initialize some configurations details
 */
$config = new USVN_Config_Ini("../www/config.ini", "general");
Zend_Registry::set('config', $config);

USVN_Translation::initTranslation($config->translation->locale, "../www/locale");
date_default_timezone_set($config->timezone);

$db = Zend_Db::factory($config->database->adapterName, $config->database->options->toArray());
USVN_Db_Table::$prefix = $config->database->prefix;
Zend_Db_Table::setDefaultAdapter($db);

Zend_Registry::set('config', $config);

/**
 * Get options and directories paths to check
 */
$results = array();
$paths = array();
$usage = "Usage: $argv[0] [--recursive] [--verbose] [--creategroup] [--addmetogroup] [--admin] [login] path1 path2 path3 [...]\n";
if (count($argv) == 1) {
	die($usage);
}

$options = array('recursive' => false,
					'verbose' => false,
					'login' => 0,
					'creategroup' => 0,
					'addmetogroup' => 0,
					'admin' => 0);

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
		default:
			if (is_dir($arg)) {
				$paths[] = $arg;
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
if (count($paths)) {
	foreach ($paths as $path) {
		$results = array_merge($results, lookAfterSVNRepositoriesToImport($path, $options));
	}
} else {
	die($usage);
}

if (count($results)) {
	foreach ($results as $result) {
		$name = str_replace($config->subversion->path . 'svn' . DIRECTORY_SEPARATOR, '', $result);
		USVN_Project::createProject(array('projects_name' => $name), $options['login'], $options['creategroup'], $options['addmetogroup'], $options['admin'], 0);
		print "$result imported\n";
	}
}

?>
