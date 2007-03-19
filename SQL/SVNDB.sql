/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     19/03/2007 16:38:03                          */
/*==============================================================*/


/*==============================================================*/
/* Table: usvn_files                                            */
/*==============================================================*/
create table usvn_files
(
   files_id             int not null,
   projects_id          int not null,
   files_filename       varchar(255) not null,
   files_path           text not null,
   files_date           datetime,
   files_isdir          bool,
   files_num_rev        int,
   files_typ_rev        char(1),
   files_message        varchar(1000),
   primary key (files_id)
);

/*==============================================================*/
/* Table: usvn_groups                                           */
/*==============================================================*/
create table usvn_groups
(
   groups_id            int not null,
   groups_name          varchar(150) not null,
   primary key (groups_id)
);

/*==============================================================*/
/* Table: usvn_projects                                         */
/*==============================================================*/
create table usvn_projects
(
   projects_id          int not null,
   projects_name        varchar(255) not null,
   projects_date_start  datetime,
   projects_description varchar(1000),
   primary key (projects_id)
);

/*==============================================================*/
/* Table: usvn_properties                                       */
/*==============================================================*/
create table usvn_properties
(
   properties_id        int not null,
   properties_version   int not null,
   properties_value     text not null,
   properties_label_property varchar(64) not null,
   properties_path      varchar(255) not null,
   primary key (properties_id)
);

/*==============================================================*/
/* Table: usvn_rights                                           */
/*==============================================================*/
create table usvn_rights
(
   rights_id            int not null,
   rights_label         varchar(255) not null,
   primary key (rights_id)
);

/*==============================================================*/
/* Table: usvn_to_assign                                        */
/*==============================================================*/
create table usvn_to_assign
(
   properties_id        int not null,
   files_id             int not null,
   primary key (properties_id, files_id)
);

/*==============================================================*/
/* Table: usvn_to_attribute                                     */
/*==============================================================*/
create table usvn_to_attribute
(
   rights_id            int not null,
   groups_id            int not null,
   projects_id          int not null,
   primary key (rights_id, groups_id, projects_id)
);

/*==============================================================*/
/* Table: usvn_to_belong                                        */
/*==============================================================*/
create table usvn_to_belong
(
   users_id             int not null,
   groups_id            int not null,
   primary key (users_id, groups_id)
);

/*==============================================================*/
/* Table: usvn_to_have                                          */
/*==============================================================*/
create table usvn_to_have
(
   users_id             int not null,
   projects_id          int not null,
   primary key (users_id, projects_id)
);

/*==============================================================*/
/* Table: usvn_users                                            */
/*==============================================================*/
create table usvn_users
(
   users_id             int not null,
   users_login          varchar(255) not null,
   users_password       varchar(44) not null,
   users_lastname       varchar(100),
   users_firstname      varchar(100),
   users_email          varchar(150),
   primary key (users_id)
);

alter table usvn_files add constraint fk_usvn_to_manage foreign key (projects_id)
      references usvn_projects (projects_id) on delete restrict on update restrict;

alter table usvn_to_assign add constraint fk_usvn_to_assign foreign key (properties_id)
      references usvn_properties (properties_id) on delete restrict on update restrict;

alter table usvn_to_assign add constraint fk_usvn_to_assign2 foreign key (files_id)
      references usvn_files (files_id) on delete restrict on update restrict;

alter table usvn_to_attribute add constraint fk_usvn_to_attribute foreign key (rights_id)
      references usvn_rights (rights_id) on delete restrict on update restrict;

alter table usvn_to_attribute add constraint fk_usvn_to_attribute2 foreign key (groups_id)
      references usvn_groups (groups_id) on delete restrict on update restrict;

alter table usvn_to_attribute add constraint fk_usvn_to_attribute3 foreign key (projects_id)
      references usvn_projects (projects_id) on delete restrict on update restrict;

alter table usvn_to_belong add constraint fk_usvn_to_belong foreign key (users_id)
      references usvn_users (users_id) on delete restrict on update restrict;

alter table usvn_to_belong add constraint fk_usvn_to_belong2 foreign key (groups_id)
      references usvn_groups (groups_id) on delete restrict on update restrict;

alter table usvn_to_have add constraint fk_usvn_to_have foreign key (users_id)
      references usvn_users (users_id) on delete restrict on update restrict;

alter table usvn_to_have add constraint fk_usvn_to_have2 foreign key (projects_id)
      references usvn_projects (projects_id) on delete restrict on update restrict;

