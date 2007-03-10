/*==============================================================*/
/* DBMS name:      MySQL 4.0                                    */
/* Created on:     10/03/2007 10:19:12                          */
/*==============================================================*/


drop index to_manage_fk on repository;

drop index to_assign2_fk on to_assign;

drop index to_assign_fk on to_assign;

drop index to_attribute2_fk on to_attribute;

drop index to_attribute3_fk on to_attribute;

drop index to_attribute_fk on to_attribute;

drop index to_belong2_fk on to_belong;

drop index to_belong_fk on to_belong;

drop index to_have2_fk on to_have;

drop index to_have3_fk on to_have;

drop index to_have_fk on to_have;

drop table if exists groups;

drop table if exists project;

drop table if exists property;

drop table if exists repository;

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
   group_id                       int                            not null,
   group_label                    varchar(100),
   group_nom                      varchar(150),
   primary key (group_id)
)
type = innodb;

/*==============================================================*/
/* Table: project                                               */
/*==============================================================*/
create table project
(
   project_id                     int                            not null,
   project_name                   varchar(255),
   project_date_start             date,
   project_description            varchar(1000),
   primary key (project_id)
)
type = innodb;

/*==============================================================*/
/* Table: property                                              */
/*==============================================================*/
create table property
(
   version                        int                            not null,
   value                          text                           not null,
   label_property                 varchar(64)                    not null,
   path                           varchar(255)                   not null,
   pro_id                         int                            not null,
   primary key (pro_id)
)
type = innodb;

/*==============================================================*/
/* Table: repository                                            */
/*==============================================================*/
create table repository
(
   date                           date,
   filename                       varchar(255),
   num_rev                        int,
   typ_rev                        char(1),
   rep_id                         int                            not null,
   project_id                     int                            not null,
   message                        varchar(1000),
   primary key (rep_id)
)
type = innodb;

/*==============================================================*/
/* Index: to_manage_fk                                          */
/*==============================================================*/
create index to_manage_fk on repository
(
   project_id
);

/*==============================================================*/
/* Table: rights                                                */
/*==============================================================*/
create table rights
(
   right_id                       int                            not null,
   right_label                    varchar(255),
   primary key (right_id)
)
type = innodb;

/*==============================================================*/
/* Table: to_assign                                             */
/*==============================================================*/
create table to_assign
(
   pro_id                         int                            not null,
   rep_id                         int                            not null,
   primary key (pro_id, rep_id)
)
type = innodb;

/*==============================================================*/
/* Index: to_assign_fk                                          */
/*==============================================================*/
create index to_assign_fk on to_assign
(
   pro_id
);

/*==============================================================*/
/* Index: to_assign2_fk                                         */
/*==============================================================*/
create index to_assign2_fk on to_assign
(
   rep_id
);

/*==============================================================*/
/* Table: to_attribute                                          */
/*==============================================================*/
create table to_attribute
(
   right_id                       int                            not null,
   group_id                       int                            not null,
   project_id                     int                            not null,
   primary key (right_id, group_id, project_id)
)
type = innodb;

/*==============================================================*/
/* Index: to_attribute_fk                                       */
/*==============================================================*/
create index to_attribute_fk on to_attribute
(
   right_id
);

/*==============================================================*/
/* Index: to_attribute2_fk                                      */
/*==============================================================*/
create index to_attribute2_fk on to_attribute
(
   group_id
);

/*==============================================================*/
/* Index: to_attribute3_fk                                      */
/*==============================================================*/
create index to_attribute3_fk on to_attribute
(
   project_id
);

/*==============================================================*/
/* Table: to_belong                                             */
/*==============================================================*/
create table to_belong
(
   users_id                       int                            not null,
   group_id                       int                            not null,
   primary key (users_id, group_id)
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
   group_id
);

/*==============================================================*/
/* Table: to_have                                               */
/*==============================================================*/
create table to_have
(
   right_id                       int                            not null,
   users_id                       int                            not null,
   project_id                     int                            not null,
   primary key (right_id, users_id, project_id)
)
type = innodb;

/*==============================================================*/
/* Index: to_have_fk                                            */
/*==============================================================*/
create index to_have_fk on to_have
(
   right_id
);

/*==============================================================*/
/* Index: to_have2_fk                                           */
/*==============================================================*/
create index to_have2_fk on to_have
(
   users_id
);

/*==============================================================*/
/* Index: to_have3_fk                                           */
/*==============================================================*/
create index to_have3_fk on to_have
(
   project_id
);

/*==============================================================*/
/* Table: users                                                 */
/*==============================================================*/
create table users
(
   users_id                       int                            not null,
   users_login                    varchar(255)                   not null,
   users_passwd                   varchar(44)                    not null,
   users_nom                      varchar(100),
   users_prenom                   varchar(100),
   users_email                    varchar(150),
   primary key (users_id)
)
type = innodb;

alter table repository add constraint fk_to_manage foreign key (project_id)
      references project (project_id) on delete restrict on update restrict;

alter table to_assign add constraint fk_to_assign foreign key (pro_id)
      references property (pro_id) on delete restrict on update restrict;

alter table to_assign add constraint fk_to_assign2 foreign key (rep_id)
      references repository (rep_id) on delete restrict on update restrict;

alter table to_attribute add constraint fk_to_attribute foreign key (right_id)
      references rights (right_id) on delete restrict on update restrict;

alter table to_attribute add constraint fk_to_attribute2 foreign key (group_id)
      references groups (group_id) on delete restrict on update restrict;

alter table to_attribute add constraint fk_to_attribute3 foreign key (project_id)
      references project (project_id) on delete restrict on update restrict;

alter table to_belong add constraint fk_to_belong foreign key (users_id)
      references users (users_id) on delete restrict on update restrict;

alter table to_belong add constraint fk_to_belong2 foreign key (group_id)
      references groups (group_id) on delete restrict on update restrict;

alter table to_have add constraint fk_to_have foreign key (right_id)
      references rights (right_id) on delete restrict on update restrict;

alter table to_have add constraint fk_to_have2 foreign key (users_id)
      references users (users_id) on delete restrict on update restrict;

alter table to_have add constraint fk_to_have3 foreign key (project_id)
      references project (project_id) on delete restrict on update restrict;

