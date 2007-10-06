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
	$db->query("CREATE UNIQUE INDEX users_to_projects ON usvn_users_to_projects(users_id, projects_id);");
	$db->query("CREATE UNIQUE INDEX groups_to_projects ON usvn_groups_to_projects(groups_id,projects_id);");
	$db->closeConnection();
}


$db = connection($config);
$Fnm = "../../config.ini";
$tableau = file($Fnm);
if (file_exists($Fnm)) {
	if ($config->database->adapterName == "PDO_SQLITE") {
		Sqlite_queries($db);
	}
}