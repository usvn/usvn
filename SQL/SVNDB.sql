/*==============================================================*/
/* DBMS name:      MySQL 4.0                                    */
/* Created on:     06/03/2007 14:33:38                          */
/*==============================================================*/


drop index TO_MANAGE_FK on REPOSITORY;

drop index TO_ASSIGN2_FK on TO_ASSIGN;

drop index TO_ASSIGN_FK on TO_ASSIGN;

drop index TO_ATTRIBUTE2_FK on TO_ATTRIBUTE;

drop index TO_ATTRIBUTE3_FK on TO_ATTRIBUTE;

drop index TO_ATTRIBUTE_FK on TO_ATTRIBUTE;

drop index TO_BELONG2_FK on TO_BELONG;

drop index TO_BELONG_FK on TO_BELONG;

drop index TO_HAVE2_FK on TO_HAVE;

drop index TO_HAVE3_FK on TO_HAVE;

drop index TO_HAVE_FK on TO_HAVE;

drop table if exists GROUPS;

drop table if exists PROJECT;

drop table if exists PROPERTY;

drop table if exists REPOSITORY;

drop table if exists RIGHTS;

drop table if exists TO_ASSIGN;

drop table if exists TO_ATTRIBUTE;

drop table if exists TO_BELONG;

drop table if exists TO_HAVE;

drop table if exists USERS;

/*==============================================================*/
/* Table: GROUPS                                                */
/*==============================================================*/
create table GROUPS
(
   GROUP_ID                       int                            not null,
   GROUP_LABEL                    varchar(100),
   GROUP_NOM                      varchar(150),
   primary key (GROUP_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table: PROJECT                                               */
/*==============================================================*/
create table PROJECT
(
   PROJECT_ID                     int                            not null,
   PROJECT_NAME                   varchar(255),
   PROJECT_DATE_START             date,
   PROJECT_DESCRIPTION            varchar(1000),
   primary key (PROJECT_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table: PROPERTY                                              */
/*==============================================================*/
create table PROPERTY
(
   VERSION                        int                            not null,
   VALUE                          text                           not null,
   LABEL_PROPERTY                 varchar(64)                    not null,
   PATH                           varchar(256)                   not null,
   primary key (PATH, LABEL_PROPERTY, VERSION)
)
type = InnoDB;

/*==============================================================*/
/* Table: REPOSITORY                                            */
/*==============================================================*/
create table REPOSITORY
(
   DATE                           date,
   FILENAME                       varchar(255),
   NUM_REV                        int,
   TYP_REV                        char(1),
   PM_ID                          int                            not null,
   PROJECT_ID                     int                            not null,
   MESSAGE                        varchar(1000),
   primary key (PM_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: TO_MANAGE_FK                                          */
/*==============================================================*/
create index TO_MANAGE_FK on REPOSITORY
(
   PROJECT_ID
);

/*==============================================================*/
/* Table: RIGHTS                                                */
/*==============================================================*/
create table RIGHTS
(
   RIGHT_ID                       int                            not null,
   RIGHT_LABEL                    varchar(255),
   primary key (RIGHT_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table: TO_ASSIGN                                             */
/*==============================================================*/
create table TO_ASSIGN
(
   PATH                           varchar(256)                   not null,
   LABEL_PROPERTY                 varchar(64)                   not null,
   VERSION                        int                            not null,
   PM_ID                          int                            not null,
   primary key (PATH, LABEL_PROPERTY, VERSION, PM_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: TO_ASSIGN_FK                                          */
/*==============================================================*/
create index TO_ASSIGN_FK on TO_ASSIGN
(
   PATH,
   LABEL_PROPERTY,
   VERSION
);

/*==============================================================*/
/* Index: TO_ASSIGN2_FK                                         */
/*==============================================================*/
create index TO_ASSIGN2_FK on TO_ASSIGN
(
   PM_ID
);

/*==============================================================*/
/* Table: TO_ATTRIBUTE                                          */
/*==============================================================*/
create table TO_ATTRIBUTE
(
   RIGHT_ID                       int                            not null,
   GROUP_ID                       int                            not null,
   PROJECT_ID                     int                            not null,
   primary key (RIGHT_ID, GROUP_ID, PROJECT_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: TO_ATTRIBUTE_FK                                       */
/*==============================================================*/
create index TO_ATTRIBUTE_FK on TO_ATTRIBUTE
(
   RIGHT_ID
);

/*==============================================================*/
/* Index: TO_ATTRIBUTE2_FK                                      */
/*==============================================================*/
create index TO_ATTRIBUTE2_FK on TO_ATTRIBUTE
(
   GROUP_ID
);

/*==============================================================*/
/* Index: TO_ATTRIBUTE3_FK                                      */
/*==============================================================*/
create index TO_ATTRIBUTE3_FK on TO_ATTRIBUTE
(
   PROJECT_ID
);

/*==============================================================*/
/* Table: TO_BELONG                                             */
/*==============================================================*/
create table TO_BELONG
(
   USERS_ID                       int                            not null,
   GROUP_ID                       int                            not null,
   primary key (USERS_ID, GROUP_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: TO_BELONG_FK                                          */
/*==============================================================*/
create index TO_BELONG_FK on TO_BELONG
(
   USERS_ID
);

/*==============================================================*/
/* Index: TO_BELONG2_FK                                         */
/*==============================================================*/
create index TO_BELONG2_FK on TO_BELONG
(
   GROUP_ID
);

/*==============================================================*/
/* Table: TO_HAVE                                               */
/*==============================================================*/
create table TO_HAVE
(
   RIGHT_ID                       int                            not null,
   USERS_ID                       int                            not null,
   PROJECT_ID                     int                            not null,
   primary key (RIGHT_ID, USERS_ID, PROJECT_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: TO_HAVE_FK                                            */
/*==============================================================*/
create index TO_HAVE_FK on TO_HAVE
(
   RIGHT_ID
);

/*==============================================================*/
/* Index: TO_HAVE2_FK                                           */
/*==============================================================*/
create index TO_HAVE2_FK on TO_HAVE
(
   USERS_ID
);

/*==============================================================*/
/* Index: TO_HAVE3_FK                                           */
/*==============================================================*/
create index TO_HAVE3_FK on TO_HAVE
(
   PROJECT_ID
);

/*==============================================================*/
/* Table: USERS                                                 */
/*==============================================================*/
create table USERS
(
   USERS_ID                       int                            not null,
   USERS_LOGIN                    varchar(100)                   not null,
   USERS_PASSWD                   varchar(44)                    not null,
   USERS_NOM                      varchar(100),
   USERS_PRENOM                   varchar(100),
   USERS_EMAIL                    varchar(150),
   primary key (USERS_ID)
)
type = InnoDB;

alter table REPOSITORY add constraint FK_TO_MANAGE foreign key (PROJECT_ID)
      references PROJECT (PROJECT_ID) on delete restrict on update restrict;

alter table TO_ASSIGN add constraint FK_TO_ASSIGN foreign key (PATH, LABEL_PROPERTY, VERSION)
      references PROPERTY (PATH, LABEL_PROPERTY, VERSION) on delete restrict on update restrict;

alter table TO_ASSIGN add constraint FK_TO_ASSIGN2 foreign key (PM_ID)
      references REPOSITORY (PM_ID) on delete restrict on update restrict;

alter table TO_ATTRIBUTE add constraint FK_TO_ATTRIBUTE foreign key (RIGHT_ID)
      references RIGHTS (RIGHT_ID) on delete restrict on update restrict;

alter table TO_ATTRIBUTE add constraint FK_TO_ATTRIBUTE2 foreign key (GROUP_ID)
      references GROUPS (GROUP_ID) on delete restrict on update restrict;

alter table TO_ATTRIBUTE add constraint FK_TO_ATTRIBUTE3 foreign key (PROJECT_ID)
      references PROJECT (PROJECT_ID) on delete restrict on update restrict;

alter table TO_BELONG add constraint FK_TO_BELONG foreign key (USERS_ID)
      references USERS (USERS_ID) on delete restrict on update restrict;

alter table TO_BELONG add constraint FK_TO_BELONG2 foreign key (GROUP_ID)
      references GROUPS (GROUP_ID) on delete restrict on update restrict;

alter table TO_HAVE add constraint FK_TO_HAVE foreign key (RIGHT_ID)
      references RIGHTS (RIGHT_ID) on delete restrict on update restrict;

alter table TO_HAVE add constraint FK_TO_HAVE2 foreign key (USERS_ID)
      references USERS (USERS_ID) on delete restrict on update restrict;

alter table TO_HAVE add constraint FK_TO_HAVE3 foreign key (PROJECT_ID)
      references PROJECT (PROJECT_ID) on delete restrict on update restrict;

