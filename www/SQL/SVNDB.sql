

/*==============================================================*/
/* Table: USVN_FILES_RIGHTS                                     */
/*==============================================================*/
create table USVN_FILES_RIGHTS
(
   FILES_RIGHTS_ID                int                            not null,
   PROJECTS_ID                    int                            not null,
   FILES_RIGHTS_IS_READABLE       bool,
   FILES_RIGHTS_IS_WRITABLE       bool,
   FILES_RIGHTS_PATH              varchar(150),
   primary key (FILES_RIGHTS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: TO_BELONG_FK                                          */
/*==============================================================*/
create index TO_BELONG_FK on USVN_FILES_RIGHTS
(
   PROJECTS_ID
);

/*==============================================================*/
/* Table: USVN_GROUPS                                           */
/*==============================================================*/
create table USVN_GROUPS
(
   GROUPS_ID                      int                            not null,
   GROUPS_NAME                    varchar(150)                   not null,
   GROUPS_DESCRIPTION             varchar(1000),
   primary key (GROUPS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table: USVN_GROUPS_TO_FILES_RIGHTS                           */
/*==============================================================*/
create table USVN_GROUPS_TO_FILES_RIGHTS
(
   FILES_RIGHTS_ID                int                            not null,
   GROUPS_ID                      int                            not null,
   primary key (FILES_RIGHTS_ID, GROUPS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: USVN_GROUPS_TO_FILES_RIGHTS_FK                        */
/*==============================================================*/
create index USVN_GROUPS_TO_FILES_RIGHTS_FK on USVN_GROUPS_TO_FILES_RIGHTS
(
   FILES_RIGHTS_ID
);

/*==============================================================*/
/* Index: USVN_GROUPS_TO_FILES_RIGHTS2_FK                       */
/*==============================================================*/
create index USVN_GROUPS_TO_FILES_RIGHTS2_FK on USVN_GROUPS_TO_FILES_RIGHTS
(
   GROUPS_ID
);

/*==============================================================*/
/* Table: USVN_PROJECTS                                         */
/*==============================================================*/
create table USVN_PROJECTS
(
   PROJECTS_ID                    int                            not null,
   PROJECTS_NAME                  varchar(255)                   not null,
   PROJECTS_START_DATE            datetime                       not null,
   PROJECTS_DESCRIPTION           varchar(1000),
   PROJECTS_AUTH                  varchar(255),
   PROJECTS_URL                   varchar(300),
   primary key (PROJECTS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table: USVN_RIGHTS                                           */
/*==============================================================*/
create table USVN_RIGHTS
(
   RIGHTS_ID                      int                            not null,
   RIGHTS_LABEL                   varchar(255)                   not null,
   RIGHTS_DESCRIPTION             varchar(1000),
   primary key (RIGHTS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table: USVN_USERS                                            */
/*==============================================================*/
create table USVN_USERS
(
   USERS_ID                       int                            not null,
   USERS_LOGIN                    varchar(255)                   not null,
   USERS_PASSWORD                 varchar(64)                    not null,
   USERS_LASTNAME                 varchar(100),
   USERS_FIRSTNAME                varchar(100),
   USERS_EMAIL                    varchar(150),
   primary key (USERS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Table: USVN_USERS_TO_GROUPS                                  */
/*==============================================================*/
create table USVN_USERS_TO_GROUPS
(
   USERS_ID                       int                            not null,
   GROUPS_ID                      int                            not null,
   primary key (USERS_ID, GROUPS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: USVN_USERS_TO_GROUPS_FK                               */
/*==============================================================*/
create index USVN_USERS_TO_GROUPS_FK on USVN_USERS_TO_GROUPS
(
   USERS_ID
);

/*==============================================================*/
/* Index: USVN_USERS_TO_GROUPS2_FK                              */
/*==============================================================*/
create index USVN_USERS_TO_GROUPS2_FK on USVN_USERS_TO_GROUPS
(
   GROUPS_ID
);

/*==============================================================*/
/* Table: USVN_WORKGROUPS                                       */
/*==============================================================*/
create table USVN_WORKGROUPS
(
   WORKGROUPS_ID                  int                            not null,
   PROJECTS_ID                    int                            not null,
   GROUPS_ID                      int                            not null,
   primary key (WORKGROUPS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: USVN_PROJECTS_TO_WORKGROUPS_FK                        */
/*==============================================================*/
create index USVN_PROJECTS_TO_WORKGROUPS_FK on USVN_WORKGROUPS
(
   PROJECTS_ID
);

/*==============================================================*/
/* Index: USVN_GROUPS_TO_WORKGROUPS_FK                          */
/*==============================================================*/
create index USVN_GROUPS_TO_WORKGROUPS_FK on USVN_WORKGROUPS
(
   GROUPS_ID
);

/*==============================================================*/
/* Table: USVN_WORKGROUPS_TO_RIGHTS                             */
/*==============================================================*/
create table USVN_WORKGROUPS_TO_RIGHTS
(
   WORKGROUPS_ID                  int                            not null,
   RIGHTS_ID                      int                            not null,
   IS_RIGHT                       bool,
   primary key (WORKGROUPS_ID, RIGHTS_ID)
)
type = InnoDB;

/*==============================================================*/
/* Index: USVN_WORKGROUPS_TO_RIGHTS_FK                          */
/*==============================================================*/
create index USVN_WORKGROUPS_TO_RIGHTS_FK on USVN_WORKGROUPS_TO_RIGHTS
(
   WORKGROUPS_ID
);

/*==============================================================*/
/* Index: USVN_WORKGROUPS_TO_RIGHTS2_FK                         */
/*==============================================================*/
create index USVN_WORKGROUPS_TO_RIGHTS2_FK on USVN_WORKGROUPS_TO_RIGHTS
(
   RIGHTS_ID
);

alter table USVN_FILES_RIGHTS add constraint FK_TO_BELONG foreign key (PROJECTS_ID)
      references USVN_PROJECTS (PROJECTS_ID) on delete restrict on update restrict;

alter table USVN_GROUPS_TO_FILES_RIGHTS add constraint FK_USVN_GROUPS_TO_FILES_RIGHTS foreign key (FILES_RIGHTS_ID)
      references USVN_FILES_RIGHTS (FILES_RIGHTS_ID) on delete restrict on update restrict;

alter table USVN_GROUPS_TO_FILES_RIGHTS add constraint FK_USVN_GROUPS_TO_FILES_RIGHTS2 foreign key (GROUPS_ID)
      references USVN_GROUPS (GROUPS_ID) on delete restrict on update restrict;

alter table USVN_USERS_TO_GROUPS add constraint FK_USVN_USERS_TO_GROUPS foreign key (USERS_ID)
      references USVN_USERS (USERS_ID) on delete restrict on update restrict;

alter table USVN_USERS_TO_GROUPS add constraint FK_USVN_USERS_TO_GROUPS2 foreign key (GROUPS_ID)
      references USVN_GROUPS (GROUPS_ID) on delete restrict on update restrict;

alter table USVN_WORKGROUPS add constraint FK_USVN_GROUPS_TO_WORKGROUPS foreign key (GROUPS_ID)
      references USVN_GROUPS (GROUPS_ID) on delete restrict on update restrict;

alter table USVN_WORKGROUPS add constraint FK_USVN_PROJECTS_TO_WORKGROUPS foreign key (PROJECTS_ID)
      references USVN_PROJECTS (PROJECTS_ID) on delete restrict on update restrict;

alter table USVN_WORKGROUPS_TO_RIGHTS add constraint FK_USVN_WORKGROUPS_TO_RIGHTS foreign key (WORKGROUPS_ID)
      references USVN_WORKGROUPS (WORKGROUPS_ID) on delete restrict on update restrict;

alter table USVN_WORKGROUPS_TO_RIGHTS add constraint FK_USVN_WORKGROUPS_TO_RIGHTS2 foreign key (RIGHTS_ID)
      references USVN_RIGHTS (RIGHTS_ID) on delete restrict on update restrict;

