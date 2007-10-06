<?php

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
$db->query("ALTER TABLE usvn_projects DROP COLUMN projects_url");
$db->query("ALTER TABLE usvn_users_to_groups ADD is_leader bool");
$db->query("ALTER TABLE usvn_users ADD UNIQUE (users_login)");
$db->closeConnection();

