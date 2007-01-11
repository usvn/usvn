/*==============================================================*/
/* DBMS name:      MySQL 4.0                                    */
/* Created on:     11/01/2007 17:14:40                          */
/*==============================================================*/


drop index TO_MANAGE_FK on PROJECT_MANAGEMENT;

drop index TO_BELONG2_FK on TO_BELONG;

drop index TO_BELONG_FK on TO_BELONG;

drop index TO_HAVE2_FK on TO_HAVE;

drop index TO_HAVE6_FK on TO_HAVE;

drop index TO_HAVE_FK on TO_HAVE;

drop index TO_HAVE3_FK on TO_HAVE2;

drop index TO_HAVE4_FK on TO_HAVE2;

drop index TO_HAVE5_FK on TO_HAVE2;

drop table if exists "GROUP";

drop table if exists PROJECT;

drop table if exists PROJECT_MANAGEMENT;

drop table if exists RIGHTS;

drop table if exists TO_BELONG;

drop table if exists TO_HAVE;

drop table if exists TO_HAVE2;

drop table if exists USERS;

/*==============================================================*/
/* Table: "GROUP"                                               */
/*==============================================================*/
create table "GROUP"
(
   GROUP_ID                       numeric(8,0)                   not null,
   GROUP_LABEL                    varchar(100),
   GROUP_NOM                      text,
   primary key (GROUP_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table: PROJECT                                               */
/*==============================================================*/
create table PROJECT
(
   PROJECT_ID                     numeric(8,0)                   not null,
   PROJECT_NAME                   varchar(255),
   PROJECT_DATE_START             date,
   PROJECT_DESCRIPTION            text,
   primary key (PROJECT_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table: PROJECT_MANAGEMENT                                    */
/*==============================================================*/
create table PROJECT_MANAGEMENT
(
   DATE                           date,
   FILENAME                       varchar(255),
   PATHFILE                       text,
   NUM_REV                        numeric(8,0),
   TYP_REV                        char(1),
   PM_ID                          numeric(8,0)                   not null,
   PROJECT_ID                     numeric(8,0)                   not null,
   PROPERTY                       text,
   primary key (PM_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: TO_MANAGE_FK                                          */
/*==============================================================*/
create index TO_MANAGE_FK on PROJECT_MANAGEMENT
(
   PROJECT_ID
);

/*==============================================================*/
/* Table: RIGHTS                                                */
/*==============================================================*/
create table RIGHTS
(
   RIGHT_ID                       numeric(8,0)                   not null,
   RIGHT_LABEL                    varchar(255),
   primary key (RIGHT_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table: TO_BELONG                                             */
/*==============================================================*/
create table TO_BELONG
(
   USERS_ID                       numeric(8,0)                   not null,
   GROUP_ID                       numeric(8,0)                   not null,
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
   RIGHT_ID                       numeric(8,0)                   not null,
   USERS_ID                       numeric(8,0)                   not null,
   PROJECT_ID                     numeric(8,0)                   not null,
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
/* Index: TO_HAVE6_FK                                           */
/*==============================================================*/
create index TO_HAVE6_FK on TO_HAVE
(
   PROJECT_ID
);

/*==============================================================*/
/* Table: TO_HAVE2                                              */
/*==============================================================*/
create table TO_HAVE2
(
   RIGHT_ID                       numeric(8,0)                   not null,
   GROUP_ID                       numeric(8,0)                   not null,
   PROJECT_ID                     numeric(8,0)                   not null,
   primary key (RIGHT_ID, GROUP_ID, PROJECT_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: TO_HAVE3_FK                                           */
/*==============================================================*/
create index TO_HAVE3_FK on TO_HAVE2
(
   RIGHT_ID
);

/*==============================================================*/
/* Index: TO_HAVE4_FK                                           */
/*==============================================================*/
create index TO_HAVE4_FK on TO_HAVE2
(
   GROUP_ID
);

/*==============================================================*/
/* Index: TO_HAVE5_FK                                           */
/*==============================================================*/
create index TO_HAVE5_FK on TO_HAVE2
(
   PROJECT_ID
);

/*==============================================================*/
/* Table: USERS                                                 */
/*==============================================================*/
create table USERS
(
   USERS_ID                       numeric(8,0)                   not null,
   USERS_LOGIN                    varchar(255)                   not null,
   USERS_PASSWD                   text                           not null,
   USERS_NOM                      text,
   USERS_PRENOM                   text,
   USERS_EMAIL                    text,
   primary key (USERS_ID)
)
type = InnoDB;

alter table PROJECT_MANAGEMENT add constraint FK_TO_MANAGE foreign key (PROJECT_ID)
      references PROJECT (PROJECT_ID) on delete restrict on update restrict;

alter table TO_BELONG add constraint FK_TO_BELONG foreign key (USERS_ID)
      references USERS (USERS_ID) on delete restrict on update restrict;

alter table TO_BELONG add constraint FK_TO_BELONG2 foreign key (GROUP_ID)
      references "GROUP" (GROUP_ID) on delete restrict on update restrict;

alter table TO_HAVE add constraint FK_TO_HAVE foreign key (RIGHT_ID)
      references RIGHTS (RIGHT_ID) on delete restrict on update restrict;

alter table TO_HAVE add constraint FK_TO_HAVE2 foreign key (USERS_ID)
      references USERS (USERS_ID) on delete restrict on update restrict;

alter table TO_HAVE add constraint FK_TO_HAVE6 foreign key (PROJECT_ID)
      references PROJECT (PROJECT_ID) on delete restrict on update restrict;

alter table TO_HAVE2 add constraint FK_TO_HAVE3 foreign key (RIGHT_ID)
      references RIGHTS (RIGHT_ID) on delete restrict on update restrict;

alter table TO_HAVE2 add constraint FK_TO_HAVE4 foreign key (GROUP_ID)
      references "GROUP" (GROUP_ID) on delete restrict on update restrict;

alter table TO_HAVE2 add constraint FK_TO_HAVE5 foreign key (PROJECT_ID)
      references PROJECT (PROJECT_ID) on delete restrict on update restrict;

