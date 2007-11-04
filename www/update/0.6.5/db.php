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
	$db->query("CREATE TABLE usvn_users_to_groups AS SELECT * FROM tmp");
	$db->query("DROP TABLE tmp");
	$db->closeConnection();
}


function Mysql_queries ($db)
{
	$db->query("UPDATE usvn_users_to_groups set is_leader = 0 WHERE is_leader IS NULL");
	$db->query("ALTER TABLE usvn_users_to_groups MODIFY (is_leader bool not null)");
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
