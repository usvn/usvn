create table usvn_files
(
   projects_id          int not null,
   revisions_num        int not null,
   files_id             int not null,
   files_path           text not null,
   files_isdir          bool,
   files_typ_rev        char(1) not null,
   primary key (projects_id, revisions_num, files_id)
) type=INNODB;

create table usvn_groups
(
   groups_id            int not null,
   groups_name          varchar(150) not null,
   primary key (groups_id)
) type=INNODB;

create table usvn_projects
(
   projects_id          int not null,
   projects_name        varchar(255) not null,
   projects_start_date  datetime not null,
   projects_description varchar(1000),
   projects_auth varchar(255),
   primary key (projects_id)
) type=INNODB;

create table usvn_properties
(
   projects_id          int not null,
   revisions_num        int not null,
   files_id             int not null,
   properties_name      varchar(64) not null,
   properties_value     text not null,
   primary key (projects_id, revisions_num, files_id, properties_name)
) type=INNODB;

create table usvn_revisions
(
   projects_id          int not null,
   revisions_num        int not null,
   users_id             int not null,
   revisions_message    text not null,
   revisions_date       datetime not null,
   primary key (projects_id, revisions_num)
) type=INNODB;

create table usvn_rights
(
   rights_id            int not null,
   rights_label         varchar(255) not null,
   primary key (rights_id)
) type=INNODB;

create table usvn_to_attribute
(
   rights_id            int not null,
   groups_id            int not null,
   projects_id          int not null,
   files_id             int not null,
   primary key (rights_id, groups_id, projects_id)
) type=INNODB;

create table usvn_users
(
   users_id             int not null,
   users_login          varchar(255) not null,
   users_password       varchar(64) not null,
   users_lastname       varchar(100),
   users_firstname      varchar(100),
   users_email          varchar(150),
   primary key (users_id)
) type=INNODB;

create table usvn_users_to_groups
(
   users_id             int not null,
   groups_id            int not null,
   primary key (users_id, groups_id)
) type=INNODB;

create table usvn_users_to_projects
(
   users_id             int not null,
   projects_id          int not null,
   primary key (users_id, projects_id)
) type=INNODB;

alter table usvn_files add constraint fk_usvn_to_link foreign key (projects_id, revisions_num)
      references usvn_revisions (projects_id, revisions_num) on delete restrict on update restrict;

alter table usvn_properties add constraint fk_usvn_to_assign foreign key (projects_id, revisions_num, files_id)
      references usvn_files (projects_id, revisions_num, files_id) on delete restrict on update restrict;

alter table usvn_revisions add constraint fk_usvn_to_add foreign key (users_id)
      references usvn_users (users_id) on delete restrict on update restrict;

alter table usvn_revisions add constraint fk_usvn_to_manage foreign key (projects_id)
      references usvn_projects (projects_id) on delete restrict on update restrict;

alter table usvn_to_attribute add constraint fk_usvn_to_attribute foreign key (rights_id)
      references usvn_rights (rights_id) on delete restrict on update restrict;

alter table usvn_to_attribute add constraint fk_usvn_to_attribute2 foreign key (groups_id)
      references usvn_groups (groups_id) on delete restrict on update restrict;

alter table usvn_to_attribute add constraint fk_usvn_to_attribute3 foreign key (projects_id)
      references usvn_projects (projects_id) on delete restrict on update restrict;

alter table usvn_users_to_groups add constraint fk_usvn_users_to_groups foreign key (users_id)
      references usvn_users (users_id) on delete restrict on update restrict;

alter table usvn_users_to_groups add constraint fk_usvn_users_to_groups2 foreign key (groups_id)
      references usvn_groups (groups_id) on delete restrict on update restrict;

alter table usvn_users_to_projects add constraint fk_usvn_users_to_projects foreign key (users_id)
      references usvn_users (users_id) on delete restrict on update restrict;

alter table usvn_users_to_projects add constraint fk_usvn_users_to_projects2 foreign key (projects_id)
      references usvn_projects (projects_id) on delete restrict on update restrict;

