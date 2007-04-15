<?php
/**
 * Reinstall database
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package utils
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

require_once('www/USVN/autoload.php');

$config = new USVN_Config("www/config.ini", "general");

$db = Zend_Db::factory($config->database->adapterName, $config->database->options->asArray());
USVN_Db_Table::$prefix = $config->database->prefix;
Zend_Db_Table::setDefaultAdapter($db);
USVN_Db_Utils::deleteAllTables($db);
USVN_Db_Utils::loadFile($db, "www/SQL/SVNDB.sql");
USVN_Db_Utils::loadFile($db, "www/SQL/mysql.sql");
USVN_Db_Utils::loadFile($db, "www/SQL/data.sql");
USVN_Db_Utils::loadFile($db, "www/SQL/test.sql");
