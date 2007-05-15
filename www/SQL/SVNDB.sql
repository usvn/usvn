create table usvn_projects
(
   projects_id          int not null,
   projects_name        varchar(255) not null,
   projects_start_date  datetime not null,
   projects_description varchar(1000),
   projects_url         varchar(300),
   primary key (projects_id)
);

create table usvn_files_rights
(
   files_rights_id      int not null,
   projects_id          int not null,
   files_rights_is_readable bool,
   files_rights_is_writable bool,
   files_rights_path    text,
   primary key (files_rights_id),
   constraint fk_to_belong foreign key (projects_id)
      references usvn_projects (projects_id) on delete restrict on update restrict
);

create table usvn_groups
(
   groups_id            int not null,
   groups_name          varchar(150) not null,
   groups_description   varchar(1000),
   primary key (groups_id)
);

create table usvn_groups_to_files_rights
(
   files_rights_id      int not null,
   groups_id            int not null,
   primary key (files_rights_id, groups_id),
   constraint fk_usvn_groups_to_files_rights foreign key (files_rights_id)
      references usvn_files_rights (files_rights_id) on delete restrict on update restrict,
   constraint fk_usvn_groups_to_files_rights2 foreign key (groups_id)
      references usvn_groups (groups_id) on delete restrict on update restrict
);

create table usvn_rights
(
   rights_id            int not null,
   rights_label         varchar(255) not null,
   rights_description   varchar(1000),
   primary key (rights_id)
);

create table usvn_users
(
   users_id             int not null,
   users_login          varchar(255) not null,
   users_password       varchar(64) not null,
   users_lastname       varchar(100),
   users_firstname      varchar(100),
   users_email          varchar(150),
   primary key (users_id)
);

create table usvn_users_to_groups
(
   users_id             int not null,
   groups_id            int not null,
   primary key (users_id, groups_id),
   constraint fk_usvn_users_to_groups foreign key (users_id)
      references usvn_users (users_id) on delete restrict on update restrict,
   constraint fk_usvn_users_to_groups2 foreign key (groups_id)
      references usvn_groups (groups_id) on delete restrict on update restrict
);

create table usvn_workgroups
(
   workgroups_id        int not null,
   projects_id          int not null,
   groups_id            int not null,
   primary key (workgroups_id),
   constraint fk_usvn_projects_to_workgroups foreign key (projects_id)
      references usvn_projects (projects_id) on delete restrict on update restrict,
   constraint fk_usvn_groups_to_workgroups foreign key (groups_id)
      references usvn_groups (groups_id) on delete restrict on update restrict
);

create table usvn_workgroups_to_rights
(
   workgroups_id        int not null,
   rights_id            int not null,
   is_right             bool,
   primary key (workgroups_id, rights_id),
   constraint fk_usvn_workgroups_to_rights foreign key (workgroups_id)
      references usvn_workgroups (workgroups_id) on delete restrict on update restrict,
   constraint fk_usvn_workgroups_to_rights2 foreign key (rights_id)
      references usvn_rights (rights_id) on delete restrict on update restrict
);

