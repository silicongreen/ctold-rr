DROP DATABASE IF EXISTS export;
CREATE DATABASE export CHARACTER SET utf8 COLLATE utf8_general_ci;
USE export;

SET @company_id := 10;

# companies
CREATE TABLE `companies` LIKE `cysoco_db`.`companies`;
INSERT INTO `export`.`companies` ( SELECT * FROM `cysoco_db`.`companies` WHERE id = @company_id );

# users
CREATE TABLE `users` LIKE `cysoco_db`.`users`;
INSERT IGNORE INTO `export`.`users` ( SELECT * FROM `cysoco_db`.`users` WHERE company_id = @company_id AND deleted = 0 );

# parent users for GW-child users
INSERT IGNORE INTO `export`.`users` ( SELECT * FROM `cysoco_db`.`users` WHERE id IN (SELECT parent_id FROM `cysoco_db`.`users` WHERE company_id = @company_id AND deleted = 0));

# absent_time
CREATE TABLE `absent_time` LIKE `cysoco_db`.`absent_time`;
INSERT INTO `export`.`absent_time` ( SELECT * FROM `cysoco_db`.`absent_time` WHERE user_id IN (SELECT id FROM `users`));

#admins
CREATE TABLE `admins` LIKE `cysoco_db`.`admins`;
INSERT INTO `export`.`admins` ( SELECT * FROM `cysoco_db`.`admins` );


#admins_actions
CREATE TABLE `admins_actions` LIKE `cysoco_db`.`admins_actions`;
INSERT INTO `export`.`admins_actions` ( SELECT * FROM `cysoco_db`.`admins_actions` );


#apps_docs
CREATE TABLE `apps_docs` LIKE `cysoco_db`.`apps_docs`;
INSERT INTO `export`.`apps_docs` ( SELECT * FROM `cysoco_db`.`apps_docs` );


#apps_info
CREATE TABLE `apps_info` LIKE `cysoco_db`.`apps_info`;
INSERT INTO `export`.`apps_info` ( SELECT * FROM `cysoco_db`.`apps_info` );


#cache_day_work_time
CREATE TABLE `cache_day_work_time` LIKE `cysoco_db`.`cache_day_work_time`;
INSERT INTO `export`.`cache_day_work_time` ( SELECT * FROM `cysoco_db`.`cache_day_work_time` WHERE user_id IN (SELECT id FROM `users`));


#cache_task_work_time
CREATE TABLE `cache_task_work_time` LIKE `cysoco_db`.`cache_task_work_time`;
INSERT INTO `export`.`cache_task_work_time` ( SELECT * FROM `cysoco_db`.`cache_task_work_time` WHERE user_id IN (SELECT id FROM `users`) );


#clients
CREATE TABLE `clients` LIKE `cysoco_db`.`clients`;
INSERT INTO `export`.`clients` ( SELECT * FROM `cysoco_db`.`clients` WHERE company_id = @company_id );


#clients_archive
CREATE TABLE `clients_archive` LIKE `cysoco_db`.`clients_archive`;
INSERT INTO `export`.`clients_archive` ( SELECT * FROM `cysoco_db`.`clients_archive` WHERE company_id = @company_id );


#clients_projects
CREATE TABLE `clients_projects` LIKE `cysoco_db`.`clients_projects`;
INSERT INTO `export`.`clients_projects` ( SELECT * FROM `cysoco_db`.`clients_projects` );


#clients_users
CREATE TABLE `clients_users` LIKE `cysoco_db`.`clients_users`;
INSERT INTO `export`.`clients_users` ( SELECT * FROM `cysoco_db`.`clients_users` WHERE user_id IN (SELECT id FROM `users`) );


#companies_reports
CREATE TABLE `companies_reports` LIKE `cysoco_db`.`companies_reports`;
INSERT INTO `export`.`companies_reports` ( SELECT * FROM `cysoco_db`.`companies_reports` );


#company_flags
CREATE TABLE `company_flags` LIKE `cysoco_db`.`company_flags`;
INSERT INTO `export`.`company_flags` ( SELECT * FROM `cysoco_db`.`company_flags` WHERE company_id = @company_id );


#company_payments
CREATE TABLE `company_payments` LIKE `cysoco_db`.`company_payments`;
INSERT INTO `export`.`company_payments` ( SELECT * FROM `cysoco_db`.`company_payments` WHERE company_id = @company_id );


#company_plans
CREATE TABLE `company_plans` LIKE `cysoco_db`.`company_plans`;
INSERT INTO `export`.`company_plans` ( SELECT * FROM `cysoco_db`.`company_plans` );


#company_regs
CREATE TABLE `company_regs` LIKE `cysoco_db`.`company_regs`;
INSERT INTO `export`.`company_regs` ( SELECT * FROM `cysoco_db`.`company_regs` WHERE user_id IN (SELECT id FROM `users`) );


#company_reminders
CREATE TABLE `company_reminders` LIKE `cysoco_db`.`company_reminders`;
INSERT INTO `export`.`company_reminders` ( SELECT * FROM `cysoco_db`.`company_reminders` WHERE company_id = @company_id );


#company_reports
CREATE TABLE `company_reports` LIKE `cysoco_db`.`company_reports`;
INSERT INTO `export`.`company_reports` ( SELECT * FROM `cysoco_db`.`company_reports` WHERE company_id = @company_id );


#gcalendar_added_events
CREATE TABLE `gcalendar_added_events` LIKE `cysoco_db`.`gcalendar_added_events`;
INSERT INTO `export`.`gcalendar_added_events` ( SELECT * FROM `cysoco_db`.`gcalendar_added_events` );


#gcalendar_temp
CREATE TABLE `gcalendar_temp` LIKE `cysoco_db`.`gcalendar_temp`;
INSERT INTO `export`.`gcalendar_temp` ( SELECT * FROM `cysoco_db`.`gcalendar_temp` );


#goals
CREATE TABLE `goals` LIKE `cysoco_db`.`goals`;
INSERT INTO `export`.`goals` ( SELECT * FROM `cysoco_db`.`goals` WHERE user_id IN (SELECT id FROM `users`) );


#integration_sync_status
CREATE TABLE `integration_sync_status` LIKE `cysoco_db`.`integration_sync_status`;
INSERT INTO `export`.`integration_sync_status` ( SELECT * FROM `cysoco_db`.`integration_sync_status` );


#integration_time
CREATE TABLE `integration_time` LIKE `cysoco_db`.`integration_time`;
INSERT INTO `export`.`integration_time` ( SELECT * FROM `cysoco_db`.`integration_time` );


#jira_integration
CREATE TABLE `jira_integration` LIKE `cysoco_db`.`jira_integration`;
INSERT INTO `export`.`jira_integration` ( SELECT * FROM `cysoco_db`.`jira_integration` );


#notifs
CREATE TABLE `notifs` LIKE `cysoco_db`.`notifs`;
INSERT INTO `export`.`notifs` ( SELECT * FROM `cysoco_db`.`notifs` WHERE user_id IN (SELECT id FROM `users`) );


#notifs_logs
CREATE TABLE `notifs_logs` LIKE `cysoco_db`.`notifs_logs`;
INSERT INTO `export`.`notifs_logs` ( SELECT * FROM `cysoco_db`.`notifs_logs` WHERE user_id IN (SELECT id FROM `users`) );


#notifs_settings
CREATE TABLE `notifs_settings` LIKE `cysoco_db`.`notifs_settings`;
INSERT INTO `export`.`notifs_settings` ( SELECT * FROM `cysoco_db`.`notifs_settings` WHERE user_id IN (SELECT id FROM `users`) );


#notifs_types
CREATE TABLE `notifs_types` LIKE `cysoco_db`.`notifs_types`;
INSERT INTO `export`.`notifs_types` ( SELECT * FROM `cysoco_db`.`notifs_types` );


#options
CREATE TABLE `options` LIKE `cysoco_db`.`options`;
INSERT INTO `export`.`options` ( SELECT * FROM `cysoco_db`.`options` WHERE userid IN (SELECT id FROM `users`) );


#people_watched
CREATE TABLE `people_watched` LIKE `cysoco_db`.`people_watched`;
INSERT INTO `export`.`people_watched` ( SELECT * FROM `cysoco_db`.`people_watched` WHERE user_id IN (SELECT id FROM `users`) );


#plan_codes
CREATE TABLE `plan_codes` LIKE `cysoco_db`.`plan_codes`;
INSERT INTO `export`.`plan_codes` ( SELECT * FROM `cysoco_db`.`plan_codes` );


#plans
CREATE TABLE `plans` LIKE `cysoco_db`.`plans`;
INSERT INTO `export`.`plans` ( SELECT * FROM `cysoco_db`.`plans` );


#poor_sites
CREATE TABLE `poor_sites` LIKE `cysoco_db`.`poor_sites`;
INSERT INTO `export`.`poor_sites` ( SELECT * FROM `cysoco_db`.`poor_sites` );


#project_integrations
CREATE TABLE `project_integrations` LIKE `cysoco_db`.`project_integrations`;
INSERT INTO `export`.`project_integrations` ( SELECT * FROM `cysoco_db`.`project_integrations` );


#report_uploading_error
CREATE TABLE `report_uploading_error` LIKE `cysoco_db`.`report_uploading_error`;
INSERT INTO `export`.`report_uploading_error` ( SELECT * FROM `cysoco_db`.`report_uploading_error` WHERE user_id IN (SELECT id FROM `users`) );


#reset_requests
CREATE TABLE `reset_requests` LIKE `cysoco_db`.`reset_requests`;
INSERT INTO `export`.`reset_requests` ( SELECT * FROM `cysoco_db`.`reset_requests` WHERE user_id IN (SELECT id FROM `users`) );


#screens2
CREATE TABLE `screens2` LIKE `cysoco_db`.`screens2`;
INSERT INTO `export`.`screens2` ( SELECT * FROM `cysoco_db`.`screens2` WHERE user_id IN (SELECT id FROM `users`) );


#st_visits
CREATE TABLE `st_visits` LIKE `cysoco_db`.`st_visits`;
INSERT INTO `export`.`st_visits` ( SELECT * FROM `cysoco_db`.`st_visits` );


#st_visits_answers
CREATE TABLE `st_visits_answers` LIKE `cysoco_db`.`st_visits_answers`;
INSERT INTO `export`.`st_visits_answers` ( SELECT * FROM `cysoco_db`.`st_visits_answers` );


#st_visits_pages
CREATE TABLE `st_visits_pages` LIKE `cysoco_db`.`st_visits_pages`;
INSERT INTO `export`.`st_visits_pages` ( SELECT * FROM `cysoco_db`.`st_visits_pages` );


#survey_choices
CREATE TABLE `survey_choices` LIKE `cysoco_db`.`survey_choices`;
INSERT INTO `export`.`survey_choices` ( SELECT * FROM `cysoco_db`.`survey_choices` );


#survey_responses
CREATE TABLE `survey_responses` LIKE `cysoco_db`.`survey_responses`;
INSERT INTO `export`.`survey_responses` ( SELECT * FROM `cysoco_db`.`survey_responses` );


#surveys
CREATE TABLE `surveys` LIKE `cysoco_db`.`surveys`;
INSERT INTO `export`.`surveys` ( SELECT * FROM `cysoco_db`.`surveys` );


#tags
CREATE TABLE `tags` LIKE `cysoco_db`.`tags`;
INSERT INTO `export`.`tags` ( SELECT * FROM `cysoco_db`.`tags` );


#tags_used
CREATE TABLE `tags_used` LIKE `cysoco_db`.`tags_used`;
INSERT INTO `export`.`tags_used` ( SELECT * FROM `cysoco_db`.`tags_used` );


#task_assigned_users
CREATE TABLE `task_assigned_users` LIKE `cysoco_db`.`task_assigned_users`;
INSERT INTO `export`.`task_assigned_users` ( SELECT * FROM `cysoco_db`.`task_assigned_users` WHERE user_id IN (SELECT id FROM `users`) );


#task_comments
CREATE TABLE `task_comments` LIKE `cysoco_db`.`task_comments`;
INSERT INTO `export`.`task_comments` ( SELECT * FROM `cysoco_db`.`task_comments` WHERE user_id IN (SELECT id FROM `users`) );


#task_comments_files
CREATE TABLE `task_comments_files` LIKE `cysoco_db`.`task_comments_files`;
INSERT INTO `export`.`task_comments_files` ( SELECT * FROM `cysoco_db`.`task_comments_files` WHERE user_id IN (SELECT id FROM `users`) );


#task_integration
CREATE TABLE `task_integration` LIKE `cysoco_db`.`task_integration`;
INSERT INTO `export`.`task_integration` ( SELECT * FROM `cysoco_db`.`task_integration` );


#task_proj_lastused
CREATE TABLE `task_proj_lastused` LIKE `cysoco_db`.`task_proj_lastused`;
INSERT INTO `export`.`task_proj_lastused` ( SELECT * FROM `cysoco_db`.`task_proj_lastused` );


#task_team_lastused
CREATE TABLE `task_team_lastused` LIKE `cysoco_db`.`task_team_lastused`;
INSERT INTO `export`.`task_team_lastused` ( SELECT * FROM `cysoco_db`.`task_team_lastused` );


#tasks
CREATE TABLE `tasks` LIKE `cysoco_db`.`tasks`;
INSERT INTO `export`.`tasks` ( SELECT `cysoco_db`.`tasks`.* FROM `cysoco_db`.`tasks`, `users` WHERE user_id = `users`.id );


#tasks_attachments
CREATE TABLE `tasks_attachments` LIKE `cysoco_db`.`tasks_attachments`;
INSERT INTO `export`.`tasks_attachments` ( SELECT * FROM `cysoco_db`.`tasks_attachments` );


#tasks_categories
CREATE TABLE `tasks_categories` LIKE `cysoco_db`.`tasks_categories`;
INSERT INTO `export`.`tasks_categories` ( SELECT * FROM `cysoco_db`.`tasks_categories` );


#tasks_modified
CREATE TABLE `tasks_modified` LIKE `cysoco_db`.`tasks_modified`;
INSERT INTO `export`.`tasks_modified` ( SELECT * FROM `cysoco_db`.`tasks_modified` );


#tasks_prev
CREATE TABLE `tasks_prev` LIKE `cysoco_db`.`tasks_prev`;
INSERT INTO `export`.`tasks_prev` ( SELECT * FROM `cysoco_db`.`tasks_prev` );


#timeuse_report_temp
CREATE TABLE `timeuse_report_temp` LIKE `cysoco_db`.`timeuse_report_temp`;


#timezones
CREATE TABLE `timezones` LIKE `cysoco_db`.`timezones`;
INSERT INTO `export`.`timezones` ( SELECT * FROM `cysoco_db`.`timezones` );


#user_company_prev
CREATE TABLE `user_company_prev` LIKE `cysoco_db`.`user_company_prev`;
INSERT INTO `export`.`user_company_prev` ( SELECT * FROM `cysoco_db`.`user_company_prev` WHERE user_id IN (SELECT id FROM `users`) );


#user_relations
CREATE TABLE `user_relations` LIKE `cysoco_db`.`user_relations`;
INSERT INTO `export`.`user_relations` ( SELECT * FROM `cysoco_db`.`user_relations` WHERE user_id IN (SELECT id FROM `users`) );


#users_cache_files
CREATE TABLE `users_cache_files` LIKE `cysoco_db`.`users_cache_files`;
INSERT INTO `export`.`users_cache_files` ( SELECT * FROM `cysoco_db`.`users_cache_files` WHERE user_id IN (SELECT id FROM `users`));


#users_dashboard_activity
CREATE TABLE `users_dashboard_activity` LIKE `cysoco_db`.`users_dashboard_activity`;
INSERT INTO `export`.`users_dashboard_activity` ( SELECT * FROM `cysoco_db`.`users_dashboard_activity` WHERE user_id IN (SELECT id FROM `users`) );


#users_dashboard_timeuse
CREATE TABLE `users_dashboard_timeuse` LIKE `cysoco_db`.`users_dashboard_timeuse`;
INSERT INTO `export`.`users_dashboard_timeuse` ( SELECT * FROM `cysoco_db`.`users_dashboard_timeuse` WHERE user_id IN (SELECT id FROM `users`) );


#users_dashboard_websites
CREATE TABLE `users_dashboard_websites` LIKE `cysoco_db`.`users_dashboard_websites`;


#users_dashboard_websites_temp
CREATE TABLE `users_dashboard_websites_temp` LIKE `cysoco_db`.`users_dashboard_websites_temp`;


#users_deleted
CREATE TABLE `users_deleted` LIKE `cysoco_db`.`users_deleted`;


#users_invitations
CREATE TABLE `users_invitations` LIKE `cysoco_db`.`users_invitations`;
INSERT INTO `export`.`users_invitations` ( SELECT * FROM `cysoco_db`.`users_invitations` WHERE user_id IN (SELECT id FROM `users`) );


#users_messages
CREATE TABLE `users_messages` LIKE `cysoco_db`.`users_messages`;
INSERT INTO `export`.`users_messages` ( SELECT * FROM `cysoco_db`.`users_messages` WHERE user_id IN (SELECT id FROM `users`) );


#users_notifications
CREATE TABLE `users_notifications` LIKE `cysoco_db`.`users_notifications`;
INSERT INTO `export`.`users_notifications` ( SELECT * FROM `cysoco_db`.`users_notifications` WHERE user_id IN (SELECT id FROM `users`) );


#users_regs
CREATE TABLE `users_regs` LIKE `cysoco_db`.`users_regs`;
INSERT INTO `export`.`users_regs` ( SELECT * FROM `cysoco_db`.`users_regs` WHERE user_id IN (SELECT id FROM `users`));


#users_screencast
CREATE TABLE `users_screencast` LIKE `cysoco_db`.`users_screencast`;
INSERT INTO `export`.`users_screencast` ( SELECT * FROM `cysoco_db`.`users_screencast` WHERE user_id IN (SELECT id FROM `users`) );


#users_tags
CREATE TABLE `users_tags` LIKE `cysoco_db`.`users_tags`;
INSERT INTO `export`.`users_tags` ( SELECT * FROM `cysoco_db`.`users_tags` WHERE user_id IN (SELECT id FROM `users`) );


#worklog3
CREATE TABLE `worklog3` LIKE `cysoco_db`.`worklog3`;
INSERT INTO `export`.`worklog3` ( SELECT `cysoco_db`.`worklog3`.* FROM `cysoco_db`.`worklog3`, `users` WHERE user_id = `users`.id);


#worklog3_edited
CREATE TABLE `worklog3_edited` LIKE `cysoco_db`.`worklog3_edited`;
INSERT INTO `export`.`worklog3_edited` ( SELECT * FROM `cysoco_db`.`worklog3_edited` WHERE worklog_id IN (SELECT id FROM `worklog3`));


#worklog_time_jumps
CREATE TABLE `worklog_time_jumps` LIKE `cysoco_db`.`worklog_time_jumps`;
INSERT INTO `export`.`worklog_time_jumps` ( SELECT * FROM `cysoco_db`.`worklog_time_jumps` WHERE user_id IN (SELECT id FROM `users`) );


#worktimes
CREATE TABLE `worktimes` LIKE `cysoco_db`.`worktimes`;
INSERT INTO `export`.`worktimes` ( SELECT * FROM `cysoco_db`.`worktimes` WHERE user_id IN (SELECT id FROM `users`) );

