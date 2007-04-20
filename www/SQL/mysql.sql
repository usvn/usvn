ALTER TABLE `usvn_groups` CHANGE `groups_id` `groups_id` INT( 11 ) NOT NULL AUTO_INCREMENT ;
ALTER TABLE `usvn_projects` CHANGE `projects_id` `projects_id` INT( 11 ) NOT NULL AUTO_INCREMENT ;
ALTER TABLE `usvn_rights` CHANGE `rights_id` `rights_id` INT( 11 ) NOT NULL AUTO_INCREMENT ;
ALTER TABLE `usvn_users` CHANGE `users_id` `users_id` INT( 11 ) NOT NULL AUTO_INCREMENT ;
ALTER TABLE `usvn_users` CHANGE `users_login` `users_login` VARCHAR(255) NOT NULL UNIQUE;
ALTER TABLE `usvn_to_attribute` DROP PRIMARY KEY ,
ADD PRIMARY KEY ( `rights_id` , `groups_id` , `projects_id`) ;
