/*==============================================================*/
/* DBMS name:      MySQL 4.0                                    */
/* Created on:     13/03/2007 14:38:40                          */
/*==============================================================*/


drop index to_manage_fk on repositories;

drop index to_assign2_fk on to_assign;

drop index to_assign_fk on to_assign;

drop index to_attribute2_fk on to_attribute;

drop index to_attribute3_fk on to_attribute;

drop index to_attribute_fk on to_attribute;

drop index to_belong2_fk on to_belong;

drop index to_belong_fk on to_belong;

drop index to_have2_fk on to_have;

drop index to_have_fk on to_have;

drop table if exists groups;

drop table if exists projects;

drop table if exists properties;

drop table if exists repositories;

drop table if exists rights;

drop table if exists to_assign;

drop table if exists to_attribute;

drop table if exists to_belong;

drop table if exists to_have;

drop table if exists users;

/*==============================================================*/
/* Table: groups                                                */
/*==============================================================*/
create table groups
(
   groups_id                      int                            not null,
   groups_label                   varchar(100),
   groups_nom                     varchar(150),
   primary key (groups_id)
)
type = innodb;

/*==============================================================*/
/* Table: projects                                              */
/*==============================================================*/
create table projects
(
   projects_id                    int                            not null,
   projects_name                  varchar(255),
   projects_date_start            date,
   projects_description           varchar(1000),
   primary key (projects_id)
)
type = innodb;

/*==============================================================*/
/* Table: properties                                            */
/*==============================================================*/
create table properties
(
   properties_id                  int                            not null,
   properties_version             int                            not null,
   properties_value               text                           not null,
   properties_label_property      varchar(64)                    not null,
   properties_path                varchar(255)                   not null,
   primary key (properties_id)
)
type = innodb;

/*==============================================================*/
/* Table: repositories                                          */
/*==============================================================*/
create table repositories
(
   repositories_rep_id            int                            not null,
   projects_id                    int                            not null,
   repositories_date              date,
   repositories_filename          varchar(255),
   repositories_num_rev           int,
   repositories_typ_rev           char(1),
   repositories_message           varchar(1000),
   primary key (repositories_rep_id)
)
type = innodb;

/*==============================================================*/
/* Index: to_manage_fk                                          */
/*==============================================================*/
create index to_manage_fk on repositories
(
   projects_id
);

/*==============================================================*/
/* Table: rights                                                */
/*==============================================================*/
create table rights
(
   rights_id                      int                            not null,
   rights_label                   varchar(255),
   primary key (rights_id)
)
type = innodb;

/*==============================================================*/
/* Table: to_assign                                             */
/*==============================================================*/
create table to_assign
(
   properties_id                  int                            not null,
   repositories_rep_id            int                            not null,
   primary key (properties_id, repositories_rep_id)
)
type = innodb;

/*==============================================================*/
/* Index: to_assign_fk                                          */
/*==============================================================*/
create index to_assign_fk on to_assign
(
   properties_id
);

/*==============================================================*/
/* Index: to_assign2_fk                                         */
/*==============================================================*/
create index to_assign2_fk on to_assign
(
   repositories_rep_id
);

/*==============================================================*/
/* Table: to_attribute                                          */
/*==============================================================*/
create table to_attribute
(
   rights_id                      int                            not null,
   groups_id                      int                            not null,
   projects_id                    int                            not null,
   primary key (rights_id, groups_id, projects_id)
)
type = innodb;

/*==============================================================*/
/* Index: to_attribute_fk                                       */
/*==============================================================*/
create index to_attribute_fk on to_attribute
(
   rights_id
);

/*==============================================================*/
/* Index: to_attribute2_fk                                      */
/*==============================================================*/
create index to_attribute2_fk on to_attribute
(
   groups_id
);

/*==============================================================*/
/* Index: to_attribute3_fk                                      */
/*==============================================================*/
create index to_attribute3_fk on to_attribute
(
   projects_id
);

/*==============================================================*/
/* Table: to_belong                                             */
/*==============================================================*/
create table to_belong
(
   users_id                       int                            not null,
   groups_id                      int                            not null,
   primary key (users_id, groups_id)
)
type = innodb;

/*==============================================================*/
/* Index: to_belong_fk                                          */
/*==============================================================*/
create index to_belong_fk on to_belong
(
   users_id
);

/*==============================================================*/
/* Index: to_belong2_fk                                         */
/*==============================================================*/
create index to_belong2_fk on to_belong
(
   groups_id
);

/*==============================================================*/
/* Table: to_have                                               */
/*==============================================================*/
create table to_have
(
   users_id                       int                            not null,
   projects_id                    int                            not null,
   primary key (users_id, projects_id)
)
type = innodb;

/*==============================================================*/
/* Index: to_have_fk                                            */
/*==============================================================*/
create index to_have_fk on to_have
(
   users_id
);

/*==============================================================*/
/* Index: to_have2_fk                                           */
/*==============================================================*/
create index to_have2_fk on to_have
(
   projects_id
);

/*==============================================================*/
/* Table: users                                                 */
/*==============================================================*/
create table users
(
   users_id                       int                            not null,
   users_login                    varchar(255)                   not null,
   users_password                 varchar(44)                    not null,
   users_nom                      varchar(100),
   users_prenom                   varchar(100),
   users_email                    varchar(150),
   primary key (users_id)
)
type = innodb;

alter table repositories add constraint fk_to_manage foreign key (projects_id)
      references projects (projects_id) on delete restrict on update restrict;

alter table to_assign add constraint fk_to_assign foreign key (properties_id)
      references properties (properties_id) on delete restrict on update restrict;

alter table to_assign add constraint fk_to_assign2 foreign key (repositories_rep_id)
      references repositories (repositories_rep_id) on delete restrict on update restrict;

alter table to_attribute add constraint fk_to_attribute foreign key (rights_id)
      references rights (rights_id) on delete restrict on update restrict;

alter table to_attribute add constraint fk_to_attribute2 foreign key (groups_id)
      references groups (groups_id) on delete restrict on update restrict;

alter table to_attribute add constraint fk_to_attribute3 foreign key (projects_id)
      references projects (projects_id) on delete restrict on update restrict;

alter table to_belong add constraint fk_to_belong foreign key (users_id)
      references users (users_id) on delete restrict on update restrict;

alter table to_belong add constraint fk_to_belong2 foreign key (groups_id)
      references groups (groups_id) on delete restrict on update restrict;

alter table to_have add constraint fk_to_have foreign key (users_id)
      references users (users_id) on delete restrict on update restrict;

alter table to_have add constraint fk_to_have2 foreign key (projects_id)
      references projects (projects_id) on delete restrict on update restrict;

