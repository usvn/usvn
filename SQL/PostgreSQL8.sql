/*==============================================================*/
/* DBMS name:      PostgreSQL 8                                 */
/* Created on:     20/03/2007 21:04:18                          */
/*==============================================================*/


/*==============================================================*/
/* Table: usvn_files                                            */
/*==============================================================*/
create table usvn_files (
   files_id             INT4                 not null,
   projects_id          INT4                 not null,
   files_filename       VARCHAR(255)         not null,
   files_path           TEXT                 not null,
   files_date           DATE                 null,
   files_isdir          BOOL                 null,
   files_num_rev        INT4                 null,
   files_typ_rev        CHAR(1)              null,
   files_message        VARCHAR(1000)        null,
   constraint pk_usvn_files primary key (files_id)
);

/*==============================================================*/
/* Table: usvn_groups                                           */
/*==============================================================*/
create table usvn_groups (
   groups_id            INT4                 not null,
   groups_name          VARCHAR(150)         not null,
   constraint pk_usvn_groups primary key (groups_id)
);

/*==============================================================*/
/* Table: usvn_projects                                         */
/*==============================================================*/
create table usvn_projects (
   projects_id          INT4                 not null,
   projects_name        VARCHAR(255)         not null,
   projects_date_start  DATE                 null,
   projects_description VARCHAR(1000)        null,
   constraint pk_usvn_projects primary key (projects_id)
);

/*==============================================================*/
/* Table: usvn_properties                                       */
/*==============================================================*/
create table usvn_properties (
   properties_id        INT4                 not null,
   properties_version   INT4                 not null,
   properties_value     TEXT                 not null,
   properties_label_property VARCHAR(64)          not null,
   properties_path      VARCHAR(255)         not null,
   constraint pk_usvn_properties primary key (properties_id)
);

/*==============================================================*/
/* Table: usvn_rights                                           */
/*==============================================================*/
create table usvn_rights (
   rights_id            INT4                 not null,
   rights_label         VARCHAR(255)         not null,
   constraint pk_usvn_rights primary key (rights_id)
);

/*==============================================================*/
/* Table: usvn_to_assign                                        */
/*==============================================================*/
create table usvn_to_assign (
   properties_id        INT4                 not null,
   files_id             INT4                 not null,
   constraint pk_usvn_to_assign primary key (properties_id, files_id)
);

/*==============================================================*/
/* Table: usvn_to_attribute                                     */
/*==============================================================*/
create table usvn_to_attribute (
   rights_id            INT4                 not null,
   groups_id            INT4                 not null,
   projects_id          INT4                 not null,
   constraint pk_usvn_to_attribute primary key (rights_id, groups_id, projects_id)
);

/*==============================================================*/
/* Table: usvn_to_belong                                        */
/*==============================================================*/
create table usvn_to_belong (
   users_id             INT4                 not null,
   groups_id            INT4                 not null,
   constraint pk_usvn_to_belong primary key (users_id, groups_id)
);

/*==============================================================*/
/* Table: usvn_to_have                                          */
/*==============================================================*/
create table usvn_to_have (
   users_id             INT4                 not null,
   projects_id          INT4                 not null,
   constraint pk_usvn_to_have primary key (users_id, projects_id)
);

/*==============================================================*/
/* Table: usvn_users                                            */
/*==============================================================*/
create table usvn_users (
   users_id             INT4                 not null,
   users_login          VARCHAR(255)         not null,
   users_password       VARCHAR(44)          not null,
   users_lastname       VARCHAR(100)         null,
   users_firstname      VARCHAR(100)         null,
   users_email          VARCHAR(150)         null,
   constraint pk_usvn_users primary key (users_id)
);

alter table usvn_files
   add constraint fk_usvn_fil_usvn_to_m_usvn_pro foreign key (projects_id)
      references usvn_projects (projects_id)
      on delete restrict on update restrict;

alter table usvn_to_assign
   add constraint fk_usvn_to__usvn_to_a_usvn_pro foreign key (properties_id)
      references usvn_properties (properties_id)
      on delete restrict on update restrict;

alter table usvn_to_assign
   add constraint fk_usvn_to__usvn_to_a_usvn_fil foreign key (files_id)
      references usvn_files (files_id)
      on delete restrict on update restrict;

alter table usvn_to_attribute
   add constraint fk_usvn_to__usvn_to_a_usvn_rig foreign key (rights_id)
      references usvn_rights (rights_id)
      on delete restrict on update restrict;

alter table usvn_to_attribute
   add constraint fk_usvn_to__usvn_to_a_usvn_gro foreign key (groups_id)
      references usvn_groups (groups_id)
      on delete restrict on update restrict;

alter table usvn_to_attribute
   add constraint fk_usvn_to__usvn_to_a_usvn_pro foreign key (projects_id)
      references usvn_projects (projects_id)
      on delete restrict on update restrict;

alter table usvn_to_belong
   add constraint fk_usvn_to__usvn_to_b_usvn_use foreign key (users_id)
      references usvn_users (users_id)
      on delete restrict on update restrict;

alter table usvn_to_belong
   add constraint fk_usvn_to__usvn_to_b_usvn_gro foreign key (groups_id)
      references usvn_groups (groups_id)
      on delete restrict on update restrict;

alter table usvn_to_have
   add constraint fk_usvn_to__usvn_to_h_usvn_use foreign key (users_id)
      references usvn_users (users_id)
      on delete restrict on update restrict;

alter table usvn_to_have
   add constraint fk_usvn_to__usvn_to_h_usvn_pro foreign key (projects_id)
      references usvn_projects (projects_id)
      on delete restrict on update restrict;

