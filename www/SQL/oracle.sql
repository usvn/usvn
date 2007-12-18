CREATE TABLE usvn_projects
	(
		projects_id integer PRIMARY KEY,
		projects_name varchar(255) not null,
		projects_start_date timestamp not null,
		projects_description varchar(1000)
	);
CREATE UNIQUE INDEX usvn_projects_projects_name ON usvn_projects(projects_name);

CREATE SEQUENCE seq_usvn_projects MINVALUE 0 INCREMENT BY 1;
CREATE TRIGGER trigger_usvn_projects
BEFORE INSERT ON usvn_projects
FOR EACH ROW
BEGIN
	SELECT seq_usvn_projects.nextval into :new.projects_id FROM dual;
END;

CREATE TABLE usvn_files_rights
	(
		files_rights_id integer PRIMARY KEY,
		files_rights_path varchar(4000),
		projects_id integer,
		constraint fk_to_belong foreign key (projects_id) references usvn_projects (projects_id)
	);

CREATE SEQUENCE seq_usvn_file_rights MINVALUE 0 INCREMENT BY 1;
CREATE TRIGGER trigger_usvn_file_rights
BEFORE INSERT ON usvn_files_rights
FOR EACH ROW
BEGIN
	SELECT seq_usvn_file_rights.nextval into :new.files_rights_id FROM dual;
END;

CREATE TABLE usvn_groups
	(
		groups_id integer PRIMARY KEY,
		groups_name varchar(150) not null,
		groups_description varchar(1000)
	);
CREATE UNIQUE INDEX usvn_groups_groups_name ON usvn_groups(groups_name);

CREATE SEQUENCE seq_usvn_groups MINVALUE 0 INCREMENT BY 1;
CREATE TRIGGER trigger_usvn_groups
BEFORE INSERT ON usvn_groups
FOR EACH ROW
BEGIN
	SELECT seq_usvn_groups.nextval into :new.groups_id FROM dual;
END;


CREATE TABLE usvn_groups_to_files_rights
	(
		files_rights_is_readable number(1) not null,
		files_rights_is_writable number(1) not null,
		files_rights_id integer not null,
		groups_id integer not null,
		constraint fk_usvn_groups_to_filesrights foreign key (files_rights_id) references usvn_files_rights (files_rights_id),
		constraint fk_usvn_groups_to_filesrights2 foreign key (groups_id) references usvn_groups (groups_id)
	);

CREATE TABLE usvn_groups_to_projects
	(
		groups_id integer not null,
		projects_id integer not null,
		constraint fk_usvn_groups_to_projects foreign key (groups_id) references usvn_groups (groups_id),
		constraint fk_usvn_groups_to_projects2 foreign key (projects_id) references usvn_projects (projects_id)
	);


CREATE TABLE usvn_users
	(
		users_id integer PRIMARY KEY,
		users_login varchar(255) not null,
		users_password varchar(64) not null,
		users_lastname varchar(100),
		users_firstname varchar(100),
		users_email varchar(150),
		users_is_admin number(1) not null,
		users_secret_id varchar(32)
	);
CREATE UNIQUE INDEX usvn_users_users_login ON usvn_users(users_login);


CREATE SEQUENCE seq_usvn_users MINVALUE 0 INCREMENT BY 1;
CREATE TRIGGER trigger_usvn_usets
BEFORE INSERT ON usvn_users
FOR EACH ROW
BEGIN
	SELECT seq_usvn_users.nextval into :new.users_id FROM dual;
END;


CREATE TABLE usvn_users_to_groups
	(
		users_id integer not null,
		groups_id integer not null,
		is_leader number(1) not null,
		constraint fk_usvn_users_to_groups foreign key (users_id) references usvn_users (users_id),
		constraint fk_usvn_users_to_groups2 foreign key (groups_id) references usvn_groups (groups_id)
	);
CREATE UNIQUE INDEX users_to_groups ON usvn_users_to_groups(users_id, groups_id);

CREATE TABLE usvn_users_to_projects
	(
		users_id integer not null,
		projects_id integer not null,
		constraint fk_usvn_users_to_projects foreign key (users_id) references usvn_users (users_id),
		constraint fk_usvn_users_to_projects2 foreign key (projects_id) references usvn_projects (projects_id)
	);
CREATE UNIQUE INDEX users_to_projects ON usvn_users_to_projects(users_id, projects_id);
CREATE UNIQUE INDEX groups_to_projects ON usvn_groups_to_projects(groups_id,projects_id);
