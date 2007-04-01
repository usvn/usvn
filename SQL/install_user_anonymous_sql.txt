INSERT INTO `usvn_users` ( `users_id` , `users_login` , `users_password` , `users_lastname` , `users_firstname` , `users_email` )
VALUES (
0 , 'anonymous', 'usvn', 'anonymous', 'anonymous', 'anonymous@anonymous.com'
);


INSERT INTO `usvn_projects` ( `projects_id` , `projects_name` , `projects_start_date` , `projects_description` )
VALUES (
0 , 'default', '2007-04-01 15:29:57', 'poisson'
);

INSERT INTO `usvn_groups` ( `groups_id` , `groups_name` )
VALUES (
0 , 'anonymous'
);

INSERT INTO `usvn_rights` ( `rights_id` , `rights_label` )
VALUES (
0 , 'default_login_login'
);


INSERT INTO `usvn_rights` ( `rights_id` , `rights_label` )
VALUES (
1 , 'svnhooks_index_index'
);

INSERT INTO `usvn_to_attribute` ( `rights_id` , `groups_id` , `projects_id` , `files_id` )
VALUES (
'0', '0', '0', '0'
);

INSERT INTO `usvn_to_attribute` ( `rights_id` , `groups_id` , `projects_id` , `files_id` )
VALUES (
'1', '0', '0', '0'
);

INSERT INTO `usvn_users_to_groups` ( `users_id` , `groups_id` )
VALUES (
'0', '0'
);

INSERT INTO `usvn_users_to_projects` ( `users_id` , `projects_id` )
VALUES (
'0', '0'
);
