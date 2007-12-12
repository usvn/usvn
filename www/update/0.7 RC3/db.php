<?php
function connection_07RC3($config)
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

function Sqlite_queries_07RC3($db)
{
    $db->query("ALTER TABLE usvn_users_to_groups RENAME TO tmp");
	$db->query("UPDATE tmp set is_leader = 0 WHERE is_leader IS NULL");
	$db->query("CREATE TABLE usvn_users_to_groups
	(
		users_id integer not null,
		groups_id integer not null,
		is_leader bool not null,
		constraint fk_usvn_users_to_groups foreign key (users_id) references usvn_users (users_id) on delete restrict on update restrict,
		constraint fk_usvn_users_to_groups2 foreign key (groups_id) references usvn_groups (groups_id) on delete restrict on update restrict
	);
	");
	$db->query("INSERT INTO usvn_users_to_groups SELECT users_id,groups_id,is_leader from tmp  GROUP BY users_id,groups_id;");
	$db->query("DROP TABLE tmp");

	$db->query("CREATE UNIQUE INDEX users_to_groups ON usvn_users_to_groups(users_id, groups_id);");

    $db->query("ALTER TABLE usvn_users_to_projects RENAME TO tmp");
	$db->query("
    CREATE TABLE usvn_users_to_projects
        (
            users_id integer not null,
            projects_id integer not null,
            constraint fk_usvn_users_to_projects foreign key (users_id) references usvn_users (users_id) on delete restrict on update restrict,
            constraint fk_usvn_users_to_projects2 foreign key (projects_id) references usvn_projects (projects_id) on delete restrict on update restrict
        );
    ");
	$db->query("
		INSERT INTO usvn_users_to_projects (users_id, projects_id)
		SELECT users_id, projects_id FROM tmp");
	$db->query("DROP TABLE tmp");

}


function upgrade_sql_07RC3($config)
{
	$db = connection_07RC3($config);
	$Fnm = "../../config.ini";
	$tableau = file($Fnm);
	if (file_exists($Fnm)) {
		if ($config->database->adapterName == "PDO_SQLITE") {
			Sqlite_queries_07RC3($db);
		}
	}
	$db->closeConnection();
}
