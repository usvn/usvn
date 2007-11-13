<?php


function connection ($config)
{
	try {
		Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapterName, $config->database->options->toArray()));
		if (isset($config->database->prefix)) {
			USVN_Db_Table::$prefix = $config->database->prefix;
		}
	}
	catch (Exception $e) {
		echo $e->getMessage();
		exit(0);
	}
	$db = Zend_Db_Table::getDefaultAdapter();
	return $db;
}



function Sqlite_queries($db)
{
	$db->query("ALTER TABLE usvn_users_to_groups RENAME TO tmp");
	$db->query("UPDATE tmp set is_leader = 0 WHERE is_leader IS NULL");
	$db->query("CREATE TABLE usvn_users_to_groups(users_id int not null, groups_id int not null, is_leader bool not null, primary key (users_id, groups_id))");
	$db->query("INSERT INTO usvn_users_to_groups select * from tmp");
	$db->query("DROP TABLE tmp");
	
	
	$db->query("ALTER TABLE usvn_groups_to_files_rights RENAME TO tmp");
	$db->query("create table usvn_groups_to_files_rights
	(
	   files_rights_id int not null,  groups_id int not null, 
	   files_rights_is_readable bool not null, files_rights_is_writable  bool not null,
	   primary key (files_rights_id, groups_id)
	)");
	$db->query("INSERT INTO usvn_groups_to_files select * from tmp");
	$db->query("DROP TABLE tmp");


	$db->query("ALTER TABLE usvn_users RENAME TO tmp");
	$db->query("
	Create table usvn_users
	(
	   users_id int not null, users_login varchar(255) not null,
	   users_password varchar(64) not null,
	   users_lastname varchar(100),
	   users_firstname varchar(100),
	   users_email varchar(150),
	   users_is_admin bool not null,
	   CONSTRAINT USERS_LOGIN_UNQ UNIQUE (users_login),
	   primary key (users_id)
	)");
	$db->query("INSERT INTO usvn_users select * from tmp");
	$db->query("DROP TABLE tmp");
	$db->closeConnection();
}


function Mysql_queries ($db)
{
	$db->query("UPDATE usvn_users_to_groups set is_leader = 0 WHERE is_leader IS NULL");
	$db->query("ALTER TABLE usvn_users_to_groups MODIFY (is_leader bool not null)");
	$db->query("ALTER TABLE usvn_groups_to_files MODIFY (files_rights_is_readable bool not null)");
	$db->query("ALTER TABLE usvn_groupd_to_files MODIFY (files_rightd_is_writable bool not null)");
	$db->query("ALTER TABLE usvn_users MODIFY (users_is_admin bool not null)");
	$db>closeConnection();
}


$db = connection($config);
$Fnm = "../../config.ini";
$tableau = file($Fnm);
if (file_exists($Fnm)) {
	if ($config->database->adapterName == "PDO_SQLITE") {
		Sqlite_queries($db);
	}
	else {
		Mysql_queries($db);
	}
}
