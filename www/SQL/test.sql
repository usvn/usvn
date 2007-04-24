INSERT INTO `usvn_users` (`users_id`, `users_login`, `users_password`, `users_lastname`, `users_firstname`, `users_email`)
VALUES
	(2, 'admin', 'adpexzg3FUZAk', 'Admin', NULL, NULL),
	(3, 'attal_m', 'atxBfaSI43jDs', 'Attal', 'Mathieu', NULL),
	(4, 'billar_m', 'bicTTCzMDeU4.', 'Billard', 'Marie', NULL),
	(5, 'crivis_s', 'crGzUc/D4cb5U', 'Crivisier', 'Stéphane', NULL),
	(6, 'dolean_j', 'crGzUc/D4cb5U', 'Doleans', 'Julien', NULL),
	(7, 'duponc_j', 'duMve6sI7pq3E', 'Duponchelle', 'Julien', NULL),
	(8, 'guyoll_o', 'gu2JqgcmE3/II', 'Guyollot', 'Olivier', NULL),
	(9, 'joanic_g', 'jok6qdG.SljvQ', 'Joanicot', 'Gabriel', NULL)
;

INSERT INTO `usvn_groups` (`groups_id`, `groups_name`)
VALUES
	(6, 'usvn-admin'),
	(7, 'usvn-user'),
	(4, 'love-admin'),
	(5, 'love-user')
;

INSERT INTO `usvn_users_to_groups` (`users_id`, `groups_id`)
VALUES
	/*Admin*/
	(2, 1),
	(2, 2),
	(2, 3),
	/*attal_m*/
	(3, 1),
	(3, 2),
	(3, 7),
	(3, 4),
	/*billar_m*/
	(4, 1),
	(4, 2),
	(4, 7),
	(4, 4),
	/*crivis_s*/
	(5, 1),
	(5, 2),
	(5, 7),
	(5, 4),
	/*dolean_j*/
	(6, 1),
	(6, 2),
	(6, 7),
	(6, 4),
	/*duponc_j*/
	(7, 1),
	(7, 2),
	(7, 6),
	(7, 4),
	/*guyoll_o*/
	(8, 1),
	(8, 2),
	(8, 7),
	(8, 4),
	/*joanic_g*/
	(9, 1),
	(9, 2),
	(9, 7),
	(9, 4)
;

INSERT INTO `usvn_projects` (`projects_id`, `projects_name`, `projects_start_date`, `projects_description`, `projects_auth`, `projects_url`)
VALUES
	(2, 'love', '2007-04-12 11:10:22', 'Site de musique similaire à last.fm', '007', NULL),
	(3, 'usvn', '2006-09-01 11:10:22', 'User Friendly SVN', '008', 'http://www.usvn.info')
;

INSERT INTO `usvn_workgroups` (`workgroups_id`, `groups_id`, `projects_id`) 
VALUES
	/*usvn-admin*/
	(4, 6, 3),
	/*usvn-user*/
	(5, 7, 3),
	/*love-admin*/
	(6, 4, 2),
	/*love-user*/
	(7, 5, 2)
;

INSERT INTO `usvn_workgroups_to_rights` (`workgroups_id`, `rights_id`, `is_right`) 
VALUES
	/*usvn-user*/
	(5, 1, 1),
	(5, 2, 1),
	(5, 3, 1),
	(5, 4, 1),
	(5, 5, 1),
	(5, 11, 1),
	/*usvn-admin*/
	(4, 1, 1),
	(4, 2, 1),
	(4, 3, 1),
	(4, 4, 1),
	(4, 5, 1),
	(4, 6, 1),
	(4, 7, 1),
	(4, 8, 1),
	(4, 9, 1),
	(4, 10, 1),
	(4, 11, 1),
	(4, 12, 1),
	(4, 13, 1),
	(4, 14, 1),
	(4, 15, 1),
	(4, 16, 1),
	(4, 17, 1),
	(4, 18, 1),
	(4, 19, 1),
	(4, 20, 1),
	(4, 21, 1),
	(4, 22, 1),
	/*love-user*/
	(7, 1, 1),
	(7, 2, 1),
	(7, 3, 1),
	(7, 4, 1),
	(7, 5, 1),
	(7, 11, 1),
	/*love-admin*/
	(6, 1, 1),
	(6, 2, 1),
	(6, 3, 1),
	(6, 4, 1),
	(6, 5, 1),
	(6, 6, 1),
	(6, 7, 1),
	(6, 8, 1),
	(6, 9, 1),
	(6, 10, 1),
	(6, 11, 1),
	(6, 12, 1),
	(6, 13, 1),
	(6, 14, 1),
	(6, 15, 1),
	(6, 16, 1),
	(6, 17, 1),
	(6, 18, 1),
	(6, 19, 1),
	(6, 20, 1),
	(6, 21, 1),
	(6, 22, 1)
;

INSERT INTO `usvn_revisions` (`projects_id`, `revisions_num`, `users_id`, `revisions_message`, `revisions_date`)
VALUES
	#Projet USVN
	(3, 1, 7, 'First commit', '2007-04-12 12:14:26'),
	(3, 2, 5, 'Update build.xml
Add client', '2007-04-13 12:14:58'),
	(3, 3, 7, 'Change charset', '2007-04-13 13:14:58'),
	#Projet love
	(2, 1, 7, 'First commit', '2007-04-13 13:14:58')
;

INSERT INTO `usvn_files` (`projects_id`, `revisions_num`, `files_id`, `files_path`, `files_isdir`, `files_typ_rev`)
VALUES
	#Rev 1 projet usvn
	(3, 1, 1, 'build.xml', 0, 'A'),
	(3, 1, 2, 'phpDocumentor.ini', 0, 'A'),
	(3, 1, 3, 'www', 1, 'A'),
	(3, 1, 4, 'www/index.php', 0, 'A'),
	#Rev 2 projet usvn
	(3, 2, 1, 'build.xml', 0, 'U'),
	(3, 2, 5, 'client', 1, 'A'),
	(3, 2, 6, 'client/usvn', 0, 'A'),
	#Rev 3 projet usvn
	(3, 3, 4, 'www/index.php', 0, 'U'),
	(3, 3, 2, 'phpDocumentor.ini', 0, 'D'),
	#Rev 1 projet love
	(2, 1, 7, 'index.php', 0, 'A'),
	(2, 1, 8, 'user.php', 0, 'A'),
	(2, 1, 9, 'css', 1, 'A'),
	(2, 1, 10, 'css/style.css', 0, 'A')
;
