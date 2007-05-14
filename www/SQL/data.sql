INSERT INTO `usvn_rights` (`rights_id`, `rights_label`) VALUES
(1, 'default_login'),
(2, 'default_logout'),
(3, 'default_index'),
(4, 'default_css'),
(5, 'default_js'),
(6, 'admin_index'),
(7, 'admin_user'),
(8, 'admin_user_new'),
(9, 'admin_user_edit'),
(10, 'admin_user_delete'),
(11, 'admin_user_editProfile'),
(12, 'admin_group'),
(13, 'admin_group_new'),
(14, 'admin_group_edit'),
(15, 'admin_group_delete'),
(16, 'admin_project'),
(17, 'admin_project_new'),
(18, 'admin_project_edit'),
(19, 'admin_project_delete'),
(20, 'admin_config'),
(21, 'admin_config_save'),
(22, 'wiki_index'),
(23, 'changeset_index');


INSERT INTO `usvn_workgroups` (`workgroups_id`, `groups_id`, `projects_id`) VALUES
/*Anonymous rights*/

(1, 1, 1),

/*Users rights*/
(2, 2, 1),

/*Admins rights*/

(3, 3, 1);


INSERT INTO `usvn_workgroups_to_rights` (`workgroups_id`, `rights_id`, `is_right`) VALUES
/*Anonymous rights*/
(1, 1, 1),
(1, 3, 1),
(1, 4, 1),
(1, 5, 1),
(1, 23, 1),
/*Users rights*/
(2, 11, 1),
/*Admins rights*/
(3, 6, 1),
(3, 7, 1),
(3, 12, 1),
(3, 16, 1),
(3, 20, 1);
