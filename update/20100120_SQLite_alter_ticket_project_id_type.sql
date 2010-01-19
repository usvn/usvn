ALTER TABLE usvn_tickets RENAME TO tmp_usvn_tickets;
CREATE TABLE usvn_tickets
	(
		ticket_id         integer primary key autoincrement not null,
		project_id        integer not null,
		creation_date     date not null,
		creator_id        integer not null,
		modification_date date null,
		modificator_id    integer null,
		title             text not null,
		description       text not null,
		milestone_id      integer null,
		type              text null,
		priority          integer null,
		status            integer not null,
		assigned_to_id    integer null,
		constraint fk_usvn_tickets_to_users foreign key (creator_id) references usvn_users (users_id) on delete restrict on update restrict,
		constraint fk_usvn_tickets_to_projects foreign key (project_id) references usvn_projects (projects_id) on delete restrict on update restrict,
		constraint fk_usvn_tickets_to_users2 foreign key (modificator_id) references usvn_users (users_id) on delete restrict on update restrict,
		constraint fk_usvn_tickets_to_milestones foreign key (milestone_id) references usvn_milestones (milestone_id) on delete restrict on update restrict
	);
INSERT INTO usvn_tickets (ticket_id, project_id, creation_date, creator_id, modification_date, modificator_id, title, description, milestone_id, type, priority, status, assigned_to_id) SELECT * FROM tmp_usvn_tickets;
DROP TABLE tmp_usvn_tickets;
