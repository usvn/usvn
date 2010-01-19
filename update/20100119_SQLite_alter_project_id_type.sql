ALTER TABLE usvn_milestones RENAME TO tmp_usvn_milestones;
CREATE TABLE usvn_milestones
	(
		milestone_id      integer primary key autoincrement not null,
		project_id        integer not null,
		creation_date     date not null,
		creator_id        integer not null,
		modification_date date null,
		modificator_id    integer null,
		title             text not null,
		description       text not null,
		due_date          date null,
		status            text not null,
		constraint fk_usvn_milestones_to_users foreign key (creator_id) references usvn_users (users_id) on delete restrict on update restrict,
		constraint fk_usvn_milestones_to_projects foreign key (project_id) references usvn_projects (projects_id) on delete restrict on update restrict,
		constraint fk_usvn_milestones_to_users2 foreign key (modificator_id) references usvn_users (users_id) on delete restrict on update restrict
	);
INSERT INTO usvn_milestones (milestone_id, project_id, creation_date, creator_id, modification_date, modificator_id, title, description, due_date, status) SELECT * FROM tmp_usvn_milestones;
DROP TABLE tmp_usvn_milestones;
