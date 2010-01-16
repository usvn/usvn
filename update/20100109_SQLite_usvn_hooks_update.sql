CREATE TABLE IF NOT EXISTS `usvn_hooks` (
    `hooks_id` integer PRIMARY KEY AUTOINCREMENT NOT NULL,
    `hooks_event` VARCHAR(255) NOT NULL,
    `hooks_path` char(255) NOT NULL
);


CREATE TABLE IF NOT EXISTS `usvn_projects_to_hooks` (
    `projects_id` integer NOT NULL,
    `hooks_id`    integer NOT NULL,
    PRIMARY KEY (`projects_id`, `hooks_id`),
	CONSTRAINT fk_usvn_projects_to_hooks  FOREIGN KEY (`projects_id`) REFERENCES `usvn_projects` (`projects_id`) on DELETE RESTRICT on UPDATE RESTRICT,
	CONSTRAINT fk_usvn_projects_to_hooks2 FOREIGN KEY (`hooks_id`)    REFERENCES `usvn_hooks` (`hooks_id`)       on DELETE RESTRICT on UPDATE RESTRICT
);