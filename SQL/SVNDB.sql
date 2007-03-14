/*==============================================================*/
/* Nom de SGBD :  MySQL 4.0                                     */
/* Date de création :  14/03/2007 17:55:51                      */
/*==============================================================*/


drop index TO_MANAGE_FK on FILES;

drop index TO_ASSIGN2_FK on TO_ASSIGN;

drop index TO_ASSIGN_FK on TO_ASSIGN;

drop index TO_ATTRIBUTE2_FK on TO_ATTRIBUTE;

drop index TO_ATTRIBUTE3_FK on TO_ATTRIBUTE;

drop index TO_ATTRIBUTE_FK on TO_ATTRIBUTE;

drop index TO_BELONG2_FK on TO_BELONG;

drop index TO_BELONG_FK on TO_BELONG;

drop index TO_HAVE2_FK on TO_HAVE;

drop index TO_HAVE_FK on TO_HAVE;

drop table if exists FILES;

drop table if exists GROUPS;

drop table if exists PROJECTS;

drop table if exists PROPERTIES;

drop table if exists RIGHTS;

drop table if exists TO_ASSIGN;

drop table if exists TO_ATTRIBUTE;

drop table if exists TO_BELONG;

drop table if exists TO_HAVE;

drop table if exists USERS;

/*==============================================================*/
/* Table : FILES                                                */
/*==============================================================*/
create table FILES
(
   FILES_ID                       int                            not null,
   PROJECTS_ID                    int                            not null,
   FILES_FILENAME                 varchar(512)                   not null,
   FILES_ISDIR                    bool                           not null,
   FILES_DATE                     datetime,
   FILES_DIRECTORY                text,
   FILES_NUM_REV                  int,
   FILES_TYP_REV                  char(1),
   FILES_MESSAGE                  varchar(1000),
   primary key (FILES_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index : TO_MANAGE_FK                                         */
/*==============================================================*/
create index TO_MANAGE_FK on FILES
(
   PROJECTS_ID
);

/*==============================================================*/
/* Table : GROUPS                                               */
/*==============================================================*/
create table GROUPS
(
   GROUPS_ID                      int                            not null,
   GROUPS_LABEL                   varchar(100)                   not null,
   GROUPS_NAME                    varchar(150),
   primary key (GROUPS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table : PROJECTS                                             */
/*==============================================================*/
create table PROJECTS
(
   PROJECTS_ID                    int                            not null,
   PROJECTS_NAME                  varchar(255)                   not null,
   PROJECTS_DATE_START            datetime,
   PROJECTS_DESCRIPTION           varchar(1000),
   primary key (PROJECTS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table : PROPERTIES                                           */
/*==============================================================*/
create table PROPERTIES
(
   PROPERTIES_ID                  int                            not null,
   PROPERTIES_VERSION             int                            not null,
   PROPERTIES_VALUE               text                           not null,
   PROPERTIES_LABEL_PROPERTY      varchar(64)                    not null,
   PROPERTIES_PATH                varchar(255)                   not null,
   primary key (PROPERTIES_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table : RIGHTS                                               */
/*==============================================================*/
create table RIGHTS
(
   RIGHTS_ID                      int                            not null,
   RIGHTS_LABEL                   varchar(255)                   not null,
   primary key (RIGHTS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table : TO_ASSIGN                                            */
/*==============================================================*/
create table TO_ASSIGN
(
   PROPERTIES_ID                  int                            not null,
   FILES_ID                       int                            not null,
   primary key (PROPERTIES_ID, FILES_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index : TO_ASSIGN_FK                                         */
/*==============================================================*/
create index TO_ASSIGN_FK on TO_ASSIGN
(
   PROPERTIES_ID
);

/*==============================================================*/
/* Index : TO_ASSIGN2_FK                                        */
/*==============================================================*/
create index TO_ASSIGN2_FK on TO_ASSIGN
(
   FILES_ID
);

/*==============================================================*/
/* Table : TO_ATTRIBUTE                                         */
/*==============================================================*/
create table TO_ATTRIBUTE
(
   RIGHTS_ID                      int                            not null,
   GROUPS_ID                      int                            not null,
   PROJECTS_ID                    int                            not null,
   primary key (RIGHTS_ID, GROUPS_ID, PROJECTS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index : TO_ATTRIBUTE_FK                                      */
/*==============================================================*/
create index TO_ATTRIBUTE_FK on TO_ATTRIBUTE
(
   RIGHTS_ID
);

/*==============================================================*/
/* Index : TO_ATTRIBUTE2_FK                                     */
/*==============================================================*/
create index TO_ATTRIBUTE2_FK on TO_ATTRIBUTE
(
   GROUPS_ID
);

/*==============================================================*/
/* Index : TO_ATTRIBUTE3_FK                                     */
/*==============================================================*/
create index TO_ATTRIBUTE3_FK on TO_ATTRIBUTE
(
   PROJECTS_ID
);

/*==============================================================*/
/* Table : TO_BELONG                                            */
/*==============================================================*/
create table TO_BELONG
(
   USERS_ID                       int                            not null,
   GROUPS_ID                      int                            not null,
   primary key (USERS_ID, GROUPS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index : TO_BELONG_FK                                         */
/*==============================================================*/
create index TO_BELONG_FK on TO_BELONG
(
   USERS_ID
);

/*==============================================================*/
/* Index : TO_BELONG2_FK                                        */
/*==============================================================*/
create index TO_BELONG2_FK on TO_BELONG
(
   GROUPS_ID
);

/*==============================================================*/
/* Table : TO_HAVE                                              */
/*==============================================================*/
create table TO_HAVE
(
   USERS_ID                       int                            not null,
   PROJECTS_ID                    int                            not null,
   primary key (USERS_ID, PROJECTS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index : TO_HAVE_FK                                           */
/*==============================================================*/
create index TO_HAVE_FK on TO_HAVE
(
   USERS_ID
);

/*==============================================================*/
/* Index : TO_HAVE2_FK                                          */
/*==============================================================*/
create index TO_HAVE2_FK on TO_HAVE
(
   PROJECTS_ID
);

/*==============================================================*/
/* Table : USERS                                                */
/*==============================================================*/
create table USERS
(
   USERS_ID                       int                            not null,
   USERS_LOGIN                    varchar(255)                   not null,
   USERS_PASSWD                   varchar(44)                    not null,
   USERS_NAME                     varchar(100),
   USERS_FIRSTNAME                varchar(100),
   USERS_EMAIL                    varchar(150),
   primary key (USERS_ID)
)
type = InnoDB;

alter table FILES add constraint FK_TO_MANAGE foreign key (PROJECTS_ID)
      references PROJECTS (PROJECTS_ID) on delete restrict on update restrict;

alter table TO_ASSIGN add constraint FK_TO_ASSIGN foreign key (PROPERTIES_ID)
      references PROPERTIES (PROPERTIES_ID) on delete restrict on update restrict;

alter table TO_ASSIGN add constraint FK_TO_ASSIGN2 foreign key (FILES_ID)
      references FILES (FILES_ID) on delete restrict on update restrict;

alter table TO_ATTRIBUTE add constraint FK_TO_ATTRIBUTE foreign key (RIGHTS_ID)
      references RIGHTS (RIGHTS_ID) on delete restrict on update restrict;

alter table TO_ATTRIBUTE add constraint FK_TO_ATTRIBUTE2 foreign key (GROUPS_ID)
      references GROUPS (GROUPS_ID) on delete restrict on update restrict;

alter table TO_ATTRIBUTE add constraint FK_TO_ATTRIBUTE3 foreign key (PROJECTS_ID)
      references PROJECTS (PROJECTS_ID) on delete restrict on update restrict;

alter table TO_BELONG add constraint FK_TO_BELONG foreign key (USERS_ID)
      references USERS (USERS_ID) on delete restrict on update restrict;

alter table TO_BELONG add constraint FK_TO_BELONG2 foreign key (GROUPS_ID)
      references GROUPS (GROUPS_ID) on delete restrict on update restrict;

alter table TO_HAVE add constraint FK_TO_HAVE foreign key (USERS_ID)
      references USERS (USERS_ID) on delete restrict on update restrict;

alter table TO_HAVE add constraint FK_TO_HAVE2 foreign key (PROJECTS_ID)
      references PROJECTS (PROJECTS_ID) on delete restrict on update restrict;

