INSERT INTO `usvn_groups` (`groups_id`, `groups_name`) VALUES 
(1, 'anonymous'),
(2, 'toto');


INSERT INTO `usvn_projects` (`projects_id`, `projects_name`, `projects_start_date`, `projects_description`, `projects_auth`, `projects_url`) VALUES 
(1, '__NONE__', '2007-04-01 15:29:57', 'poisson', NULL, NULL);


INSERT INTO `usvn_rights` (`rights_id`, `rights_label`) VALUES 
(1, 'default_login_login'),
(2, 'wiki_index'),
(3, 'admin_user'),
(4, 'admin_user_edit'),
(5, 'default_css'),
(6, 'default_js'),
(7, 'default_index');



INSERT INTO `usvn_to_attribute` (`rights_id`, `groups_id`, `projects_id`, `is_right`) VALUES 
(1, 1, 1, 1),
(2, 1, 1, 1),
(3, 1, 1, 1),
(4, 1, 1, 0),
(4, 2, 1, 1),
(5, 1, 1, 1),
(6, 1, 1, 1),
(7, 1, 1, 1);



INSERT INTO `usvn_users` (`users_id`, `users_login`, `users_password`, `users_lastname`, `users_firstname`, `users_email`) VALUES 
(1, 'anonymous', 'usvn', 'anonymous', 'anonymous', 'anonymous@anonymous.com'),
(2, 'toto', '$1$522.o6..$e6kb7ZuxLgFE0.eoOUzY90', 'lala', 'fd', 'fds@dfs.com');



INSERT INTO `usvn_users_to_groups` (`users_id`, `groups_id`) VALUES 
(1, 1),
(2, 1),
(2, 2);
