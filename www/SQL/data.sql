INSERT INTO `usvn_users` ( `users_id` , `users_login` , `users_password` , `users_lastname` , `users_firstname` , `users_email` )
VALUES (
1 , 'anonymous', '', 'Anonymous', '', ''
);


INSERT INTO `usvn_projects` ( `projects_id` , `projects_name` , `projects_start_date` , `projects_description` )
VALUES (
1 , 'default', '2117-14-12 25:29:57', ''
);

INSERT INTO `usvn_groups` ( `groups_id` , `groups_name` )
VALUES (
1 , 'All'
);

INSERT INTO `usvn_groups` ( `groups_id` , `groups_name` )
VALUES (
2 , 'Admin'
);

INSERT INTO `usvn_rights` ( `rights_id` , `rights_label` )
VALUES (
1 , 'default_login_login'
);


INSERT INTO `usvn_rights` ( `rights_id` , `rights_label` )
VALUES (
2 , 'svnhooks_index_index'
);

INSERT INTO `usvn_to_attribute` ( `rights_id` , `groups_id` , `projects_id` )
VALUES (
1, 1, 1
);

INSERT INTO `usvn_to_attribute` ( `rights_id` , `groups_id` , `projects_id` )
VALUES (
2, 1, 1
);

INSERT INTO `usvn_users_to_groups` ( `users_id` , `groups_id` )
VALUES (
1, 1
);
