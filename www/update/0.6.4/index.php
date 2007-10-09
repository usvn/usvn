<?php
/**
 * Upgrade from 0.6.3 to 0.6.4
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6.4
 * @package update
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */


define('USVN_CONFIG_FILE', "../../config.ini");

header("Content-encoding: UTF-8");

set_include_path(get_include_path() .PATH_SEPARATOR ."../../");
require_once 'USVN/autoload.php';
USVN_Translation::initTranslation('en_US', '../../locale');

try {
	$config = new USVN_Config_Ini(USVN_CONFIG_FILE, 'general');
	$config->version = "0.6.5";
	$config->save();
}
catch (Exception $e) {
	echo "<h1>Update error</h1>";
	echo $e->getMessage();
	exit(1);
}

header("Location: ../../");
