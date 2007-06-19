<?php
/**
 * Root for upgrade
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
 * $Id: index.php 734 2007-06-10 04:29:29Z dolean_j $
 */


define('USVN_CONFIG_FILE', "../../config.ini");
define('USVN_HTACCESS_FILE', "../../.htaccess");

header("Content-encoding: UTF-8");

set_include_path(get_include_path() .PATH_SEPARATOR ."../../");
require_once 'USVN/autoload.php';

$config = new USVN_Config_Ini(USVN_CONFIG_FILE, 'general');

include dirname(__FILE__) . '/unlink.php';
include dirname(__FILE__) . '/db.php';

$config->version = "0.6";
$config->save();

header("Location: ../../");
