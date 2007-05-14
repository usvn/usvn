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
Zend_Registry::set('config', $config);
USVN_Translation::initTranslation('en_US', 'www/locale');

$db = Zend_Db::factory($config->database->adapterName, $config->database->options->asArray());
USVN_Db_Table::$prefix = $config->database->prefix;
Zend_Db_Table::setDefaultAdapter($db);
USVN_Db_Utils::deleteAllTables($db);
USVN_DirectoryUtils::removeDirectory("www/files");
`svn up www/files`;
USVN_Db_Utils::loadFile($db, "www/SQL/SVNDB.sql");
USVN_Db_Utils::loadFile($db, "www/SQL/mysql.sql");
USVN_Db_Utils::loadFile($db, "www/SQL/data.sql");

$table = new USVN_Db_Table_Users();
$obj = $table->fetchNew();
$obj->setFromArray(array('users_login' 		=> 'admin',
						'users_password' 	=> 'adpexzg3FUZAk',
						'users_firstname' 	=> 'System',
						'users_lastname' 	=> 'Administrator',
						'users_email' 		=> ''));
$obj->save();

$obj = $table->fetchNew();
$obj->setFromArray(array('users_login' 		=> 'attal_m',
						'users_password' 	=> 'atxBfaSI43jDs',
						'users_firstname' 	=> 'Mathieu',
						'users_lastname' 	=> 'Attal',
						'users_email' 		=> ''));
$obj->save();

$obj = $table->fetchNew();
$obj->setFromArray(array('users_login' 		=> 'billar_m',
						'users_password' 	=> 'bicTTCzMDeU4',
						'users_firstname' 	=> 'Marie',
						'users_lastname' 	=> 'Billard',
						'users_email' 		=> ''));
$obj->save();

$obj = $table->fetchNew();
$obj->setFromArray(array('users_login' 		=> 'crivis_s',
						'users_password' 	=> 'crGzUc/D4cb5U',
						'users_firstname' 	=> 'Stephane',
						'users_lastname' 	=> 'Crivisier',
						'users_email' 		=> ''));
$obj->save();

$obj = $table->fetchNew();
$obj->setFromArray(array('users_login' 		=> 'dolean_j',
						'users_password' 	=> '$1$PRxOzeKA$Ij5uFSIk41M9B2Lk0d8Ml1',
						'users_firstname' 	=> 'Julien',
						'users_lastname' 	=> 'Doleans',
						'users_email' 		=> ''));
$obj->save();

$obj = $table->fetchNew();
$obj->setFromArray(array('users_login' 		=> 'duponc_j',
						'users_password' 	=> 'duMve6sI7pq3E',
						'users_firstname' 	=> 'Julien',
						'users_lastname' 	=> 'Duponchelle',
						'users_email' 		=> ''));
$obj->save();

$obj = $table->fetchNew();
$obj->setFromArray(array('users_login' 		=> 'guyoll_o',
						'users_password' 	=> 'gu2JqgcmE3/II',
						'users_firstname' 	=> 'Olivier',
						'users_lastname' 	=> 'Guyollot',
						'users_email' 		=> ''));
$obj->save();

$obj = $table->fetchNew();
$obj->setFromArray(array('users_login' 		=> 'joanic_g',
						'users_password' 	=> 'jok6qdG.SljvQ',
						'users_firstname' 	=> 'Gabriel',
						'users_lastname' 	=> 'Joanicot',
						'users_email' 		=> ''));
$obj->save();


mkdir('tmp');
$table = new USVN_Db_Table_Projects();
$obj = $table->fetchNew();
$obj->setFromArray(array('projects_name' => 'usvn',  'projects_start_date' => '2007-11-01 00:00:00'));
$id = $obj->save();

$oldpath= getcwd();
`svn co file://{$config->subversion->path}/svn/usvn tmp/usvn`;
`svn export --force --ignore-externals -q . tmp/usvn/trunk`;
chdir('tmp/usvn');
`svn add --force trunk/`;
`svn commit -m 'Test'`;
chdir($oldpath);

$table = new USVN_Db_Table_Projects();
$obj = $table->fetchNew();
$obj->setFromArray(array('projects_name' => 'love',  'projects_start_date' => '1984-12-03 00:00:00'));
$id = $obj->save();

$oldpath= getcwd();
`svn co file://{$config->subversion->path}/svn/usvn tmp/love`;
chdir('tmp/love');
file_put_contents('trunk/test', 'TEST');
`svn add trunk/test`;
`svn commit -m 'Test'`;
chdir($oldpath);

USVN_DirectoryUtils::removeDirectory('tmp');
