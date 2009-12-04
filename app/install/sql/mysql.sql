create table usvn_files_rights
(
   files_rights_id                int                            not null,
   projects_id                    int                            not null,
   files_rights_path            text,
   primary key (files_rights_id)
)
type = innodb;

create index to_belong_fk on usvn_files_rights
(
   projects_id
);

create table usvn_groups
(
   groups_id                      int                            not null,
   groups_name                    varchar(150)                   not null,
   groups_description             varchar(1000),
   CONSTRAINT GROUPS_NAME_UNQ UNIQUE (groups_name),
   primary key (groups_id)
)
type = innodb;

create table usvn_groups_to_files_rights
(
   files_rights_id                int                            not null,
   groups_id                      int                            not null,
   files_rights_is_readable       bool					not null,
   files_rights_is_writable       bool					not null,
   primary key (files_rights_id, groups_id)
)
type = innodb;

create index usvn_groups_to_files_rights_fk on usvn_groups_to_files_rights
(
   files_rights_id
);

create index usvn_groups_to_files_rights2_fk on usvn_groups_to_files_rights
(
   groups_id
);

create table usvn_groups_to_projects
(
   projects_id                    int                            not null,
   groups_id                      int                            not null,
   primary key (projects_id, groups_id)
)
type = innodb;

create index usvn_groups_to_projects_fk on usvn_groups_to_projects
(
   projects_id
);

create index usvn_groups_to_projects2_fk on usvn_groups_to_projects
(
   groups_id
);

create table usvn_projects
(
   projects_id                    int                            not null,
   projects_name                  varchar(255)                   not null,
   projects_start_date            datetime                       not null,
   projects_description           varchar(1000),
   CONSTRAINT PROJECTS_NAME_UNQ UNIQUE (projects_name),
   primary key (projects_id)
)
type = innodb;

create table usvn_users
(
   users_id                       int                            not null,
   users_login                    varchar(255)                   not null,
   users_password                 varchar(64)                    not null,
   users_lastname                 varchar(100),
   users_firstname                varchar(100),
   users_email                    varchar(150),
   users_is_admin                 bool						not null,
   users_secret_id		  varchar(32)			not null,	
   CONSTRAINT USERS_LOGIN_UNQ UNIQUE (users_login),
   primary key (users_id)
)
type = innodb;


create table usvn_users_to_groups
(
   users_id                       int                            not null,
   groups_id                      int                            not null,
   is_leader				      bool							not null,
   primary key (users_id, groups_id)
)
type = innodb;

create index usvn_users_to_groups_fk on usvn_users_to_groups
(
   users_id
);

create index usvn_users_to_groups2_fk on usvn_users_to_groups
(
   groups_id
);

create table usvn_users_to_projects
(
   projects_id                    int                            not null,
   users_id                       int                            not null,
   primary key (projects_id, users_id)
)
type = innodb;

create index usvn_users_to_projects_fk on usvn_users_to_projects
(
   projects_id
);

create index usvn_users_to_projects2_fk on usvn_users_to_projects
(
   users_id
);

alter table usvn_files_rights add constraint fk_usvn_file_rights foreign key (projects_id)
      references usvn_projects (projects_id) on delete restrict on update restrict;

alter table usvn_groups_to_files_rights add constraint fk_usvn_groups_to_files_rights foreign key (files_rights_id)
      references usvn_files_rights (files_rights_id) on delete restrict on update restrict;

alter table usvn_groups_to_files_rights add constraint fk_usvn_groups_to_files_rights2 foreign key (groups_id)
      references usvn_groups (groups_id) on delete restrict on update restrict;

alter table usvn_groups_to_projects add constraint fk_usvn_groups_to_projects foreign key (projects_id)
      references usvn_projects (projects_id) on delete restrict on update restrict;

alter table usvn_groups_to_projects add constraint fk_usvn_groups_to_projects2 foreign key (groups_id)
      references usvn_groups (groups_id) on delete restrict on update restrict;

alter table usvn_users_to_groups add constraint fk_usvn_users_to_groups foreign key (users_id)
      references usvn_users (users_id) on delete restrict on update restrict;

alter table usvn_users_to_groups add constraint fk_usvn_users_to_groups2 foreign key (groups_id)
      references usvn_groups (groups_id) on delete restrict on update restrict;

alter table usvn_users_to_projects add constraint fk_usvn_users_to_projects foreign key (projects_id)
      references usvn_projects (projects_id) on delete restrict on update restrict;

alter table usvn_users_to_projects add constraint fk_usvn_users_to_projects2 foreign key (users_id)
      references usvn_users (users_id) on delete restrict on update restrict;

ALTER TABLE `usvn_groups` CHANGE `groups_id` `groups_id` INT( 11 ) NOT NULL AUTO_INCREMENT ;
ALTER TABLE `usvn_projects` CHANGE `projects_id` `projects_id` INT( 11 ) NOT NULL AUTO_INCREMENT ;
ALTER TABLE `usvn_users` CHANGE `users_id` `users_id` INT( 11 ) NOT NULL AUTO_INCREMENT ;
ALTER TABLE `usvn_files_rights` CHANGE `files_rights_id` `files_rights_id` INT( 11 ) NOT NULL AUTO_INCREMENT ;

CREATE TABLE usvn_tickets
	(
		ticket_id integer primary key autoincrement not null,
		project_id date not null,
		creation_date date not null,
		creator_id integer not null,
		modification_date date null,
		modificator_id integer null,
		title varchar(200) not null,
		description text not null,
		milestone_id integer null,
		type varchar(50) null,
		priority varchar(50) null,
		status varchar(50) not null,
		constraint fk_usvn_tickets_to_users foreign key (creator_id) references usvn_users (users_id) on delete restrict on update restrict,
		constraint fk_usvn_tickets_to_projects foreign key (project_id) references usvn_projects (projects_id) on delete restrict on update restrict,
		constraint fk_usvn_tickets_to_users2 foreign key (modificator_id) references usvn_users (users_id) on delete restrict on update restrict,
		constraint fk_usvn_tickets_to_milestones foreign key (milestone_id) references usvn_milestones (milestone_id) on delete restrict on update restrict
	);

CREATE INDEX usvn_tickets_creation ON usvn_tickets (creation_date);
CREATE INDEX usvn_tickets_modification ON usvn_tickets (modification_date);

CREATE TABLE usvn_milestones
	(
		milestone_id integer primary key autoincrement not null,
		project_id date not null,
		creation_date date not null,
		creator_id integer not null,
		modification_date date null,
		modificator_id integer null,
		title text not null,
		description text not null,
		due_date date null,
		status varchar(50) not null,
		constraint fk_usvn_milestones_to_users foreign key (creator_id) references usvn_users (users_id) on delete restrict on update restrict,
		constraint fk_usvn_milestones_to_projects foreign key (project_id) references usvn_projects (projects_id) on delete restrict on update restrict,
		constraint fk_usvn_milestones_to_users2 foreign key (modificator_id) references usvn_users (users_id) on delete restrict on update restrict
	);

CREATE INDEX usvn_milestones_creation ON usvn_milestones (creation_date);
CREATE INDEX usvn_milestones_modification ON usvn_milestones (modification_date);

ALTER TABLE `usvn_projects` ADD `projects_folder` bool not null default 0;
