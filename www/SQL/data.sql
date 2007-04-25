INSERT INTO `usvn_users` (`users_id`, `users_login`, `users_password`, `users_lastname`, `users_firstname`, `users_email`) VALUES 
(1, 'anonymous', 'usvn', 'anonymous', 'anonymous', 'anonymous@anonymous.com'),
(2, 'user', '$1$XE0.Uo1.$g3tY8fdDeZa/K6qeHIHLi0', 'user', NULL, NULL),
(3, 'admin', '$1$TF..gu5.$ZOLlWKWvnphIws40vZqjH0', 'admin', 'admin', 'admin@admin.com');

INSERT INTO `usvn_groups` (`groups_id`, `groups_name`, `groups_description`) VALUES 
(1, 'Anonymous', NULL),
(2, 'Users', NULL),
(3, 'Admins', NULL);



INSERT INTO `usvn_projects` (`projects_id`, `projects_name`, `projects_start_date`, `projects_description`, `projects_auth`, `projects_url`) VALUES 
(1, '__NONE__', '2007-04-01 15:29:57', '', NULL, NULL),
(2, 'test', '2007-04-25 09:54:49', 'Un projet de test.', 'USVN Team', 'http://pasencoredesite.org');


INSERT INTO `usvn_rights` (`rights_id`, `rights_label`, `rights_description`) VALUES 
(1, 'default_login', NULL),
(2, 'default_logout', NULL),
(3, 'default_index', NULL),
(4, 'default_css', NULL),
(5, 'default_js', NULL),
(6, 'admin_index', NULL),
(7, 'admin_user', NULL),
(8, 'admin_user_new', NULL),
(9, 'admin_user_edit', NULL),
(10, 'admin_user_delete', NULL),
(11, 'admin_user_editProfile', NULL),
(12, 'admin_group', NULL),
(13, 'admin_group_new', NULL),
(14, 'admin_group_edit', NULL),
(15, 'admin_group_delete', NULL),
(16, 'admin_project', NULL),
(17, 'admin_project_new', NULL),
(18, 'admin_project_edit', NULL),
(19, 'admin_project_delete', NULL),
(20, 'admin_config', NULL),
(21, 'admin_config_save', NULL),
(22, 'wiki_index', NULL);



INSERT INTO `usvn_users_to_groups` (`users_id`, `groups_id`) VALUES 
(1, 1),
(3, 3);


INSERT INTO `usvn_workgroups` (`workgroups_id`, `groups_id`, `projects_id`) VALUES 
(1, 1, 1),
(6, 2, 1),
(7, 3, 1);


INSERT INTO `usvn_workgroups_to_rights` (`workgroups_id`, `rights_id`, `is_right`) VALUES 
(1, 1, 1),
(1, 3, 1),
(1, 4, 1),
(1, 5, 1),
(6, 8, 1),
(7, 6, 1),
(7, 7, 1),
(7, 12, 1),
(7, 16, 1),
(7, 20, 1);

INSERT INTO `usvn_modules` (`modules_id`, `modules_name`, `modules_description`) VALUES 
(1, 'default', 'default  module'),
(2, 'admin', 'admin module'),
(3, 'wiki', 'wiki module');


INSERT INTO `usvn_modules_to_projects` (`modules_id`, `projects_id`) VALUES 
(1, 1),
(2, 1),
(3, 1);