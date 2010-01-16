CREATE TABLE IF NOT EXISTS `usvn_hooks` (
    `hooks_id` int(11) NOT NULL AUTO_INCREMENT,
    `hooks_event` char(255) NOT NULL,
    `hooks_path` char(255) NOT NULL,
    PRIMARY KEY (`hooks_id`)
) type = innodb;

CREATE TABLE IF NOT EXISTS `usvn_projects_to_hooks` (
    `projects_id` int(11) NOT NULL,
    `hooks_id`    int(11) NOT NULL,
    PRIMARY KEY (`projects_id`, `hooks_id`)
) type = innodb;

CREATE INDEX usvn_projects_to_hooks_fk  on `usvn_projects_to_hooks` (`projects_id`);
CREATE INDEX usvn_projects_to_hooks2_fk on `usvn_projects_to_hooks` (`hooks_id`);

ALTER TABLE `usvn_projects_to_hooks` ADD CONSTRAINT fk_usvn_projects_to_hooks  FOREIGN KEY (`projects_id`) REFERENCES `usvn_projects` (`projects_id`) on DELETE RESTRICT on UPDATE RESTRICT;
ALTER TABLE `usvn_projects_to_hooks` ADD CONSTRAINT fk_usvn_projects_to_hooks2 FOREIGN KEY (`hooks_id`)    REFERENCES `usvn_hooks` (`hooks_id`)       on DELETE RESTRICT on UPDATE RESTRICT;
