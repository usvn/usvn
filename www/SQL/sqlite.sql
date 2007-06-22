
CREATE TABLE usvn_files_rights ( projects_id int not null, files_rights_path text, files_rights_id integer primary key autoincrement, constraint fk_to_belong foreign key (projects_id) references usvn_projects (projects_id) on delete restrict on update restrict );



CREATE TABLE usvn_groups ( groups_name varchar(150) not null, groups_description varchar(1000), groups_id integer primary key autoincrement );
CREATE UNIQUE INDEX usvn_groups_groups_name ON usvn_groups(groups_name);



CREATE TABLE usvn_groups_to_files_rights ( files_rights_is_readable bool, files_rights_is_writable bool, files_rights_id, groups_id integer, constraint fk_usvn_groups_to_files_rights foreign key (files_rights_id) references usvn_files_rights (files_rights_id) on delete restrict on update restrict, constraint fk_usvn_groups_to_files_rights2 foreign key (groups_id) references usvn_groups (groups_id) on delete restrict on update restrict );



CREATE TABLE usvn_groups_to_projects ( groups_id, projects_id integer, constraint fk_usvn_groups_to_projects foreign key (groups_id) references usvn_groups (groups_id) on delete restrict on update restrict, constraint fk_usvn_groups_to_projects2 foreign key (projects_id) references usvn_projects (projects_id) on delete restrict on update restrict );


CREATE TABLE usvn_projects ( projects_name varchar(255) not null, projects_start_date datetime not null, projects_description varchar(1000), projects_id INTEGER PRIMARY KEY AUTOINCREMENT );
CREATE UNIQUE INDEX usvn_projects_projects_name ON usvn_projects(projects_name);


CREATE TABLE usvn_users ( users_login varchar(255) not null, users_password varchar(64) not null, users_lastname varchar(100), users_firstname varchar(100), users_email varchar(150), users_is_admin bool, users_id integer primary key autoincrement );
CREATE UNIQUE INDEX usvn_users_users_login ON usvn_users(users_login);



CREATE TABLE usvn_users_to_groups ( users_id, groups_id integer , is_leader bool, constraint fk_usvn_users_to_groups foreign key (users_id) references usvn_users (users_id) on delete restrict on update restrict, constraint fk_usvn_users_to_groups2 foreign key (groups_id) references usvn_groups (groups_id) on delete restrict on update restrict );



CREATE TABLE usvn_users_to_projects ( users_id, projects_id integer, constraint fk_usvn_users_to_projects foreign key (users_id) references usvn_users (users_id) on delete restrict on update restrict, constraint fk_usvn_users_to_projects2 foreign key (projects_id) references usvn_projects (projects_id) on delete restrict on update restrict );
CREATE UNIQUE INDEX users_to_projects ON usvn_users_to_projects(users_id, projects_id);
CREATE UNIQUE INDEX groups_to_projects ON usvn_groups_to_projects(groups_id,projects_id)