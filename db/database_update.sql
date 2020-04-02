### APRIL 12 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'contracts/Payout/index/', 'Payout Account Information', 'Manage employee payout information', '0', '7', '2019-04-06 00:00:00', '0', '1');
CREATE TABLE `contract_payout_medium` ( `id` INT NOT NULL AUTO_INCREMENT , `contract_id` INT NOT NULL , `payout_medium_id` INT NOT NULL , `bank_id` INT NOT NULL , `card_number` INT NOT NULL , `account_number` INT NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `contract_payout_medium` CHANGE `card_number` `card_number` VARCHAR(50) NOT NULL;
ALTER TABLE `contract_payout_medium` CHANGE `account_number` `account_number` VARCHAR(50) NOT NULL;
CREATE TABLE `bank` ( `bank_id` INT NOT NULL AUTO_INCREMENT , `bank_name` VARCHAR(100) NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`bank_id`)) ENGINE = InnoDB;
INSERT INTO `bank` (`bank_id`, `bank_name`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'BDO', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Metro Bank', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'BPI', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
UPDATE `hris_main_navigation` SET `enabled` = '1' WHERE `hris_main_navigation`.`main_nav_id` = 10;
UPDATE `hris_main_navigation` SET `arrangement` = '97' WHERE `hris_main_navigation`.`main_nav_id` = 10;
UPDATE `hris_main_navigation` SET `arrangement` = '98' WHERE `hris_main_navigation`.`main_nav_id` = 19;
DELETE FROM `hris_content_navigation` WHERE cn_fkey = 10;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/Attendance_reports/index/', 'Attendance Reports', 'Reports regarding all about the attendance in HRIS', '0', '10', '2019-04-10 00:00:00', '0', '1');
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/Transaction_reports/index/', 'Trasaction Reports', 'Reports regarding all about the trasactions in HRIS', '0', '10', '2019-04-10 00:00:00', '0', '1');
UPDATE `hris_position` SET `access_content_nav` = '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162, \r\n163, 165, 166' WHERE `hris_position`.`position_id` = 2;
ALTER TABLE `applicant_record` ADD `app_sss_no` VARCHAR(50) NOT NULL DEFAULT '' AFTER `app_email`, ADD `app_philhealth_no` VARCHAR(50) NOT NULL DEFAULT '' AFTER `app_sss_no`, ADD `app_pagibig_no` VARCHAR(50) NOT NULL DEFAULT '' AFTER `app_philhealth_no`, ADD `app_tin_no` VARCHAR(50) NOT NULL DEFAULT '' AFTER `app_pagibig_no`;
ALTER TABLE `employee_record` ADD `sss_no` VARCHAR(50) NOT NULL DEFAULT '' AFTER `email`, ADD `philhealth_no` VARCHAR(50) NOT NULL DEFAULT '' AFTER `sss_no`, ADD `pagibig_no` VARCHAR(50) NOT NULL DEFAULT '' AFTER `philhealth_no`, ADD `tin_no` VARCHAR(50) NOT NULL DEFAULT '' AFTER `pagibig_no`;
ALTER TABLE `employee_record`
  DROP `username`,
  DROP `password`;
ALTER TABLE `time_record_summary` ADD `overbreak` INT(100) NOT NULL AFTER `undertime`;

###  APRIL 26, 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/Compensation_reports/index/', 'Compensation Reports', 'Reports regarding all about the compensations in HRIS', '0', '10', '2019-04-26 00:00:00', '0', '1');
UPDATE `hris_position` SET `access_content_nav` = '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162, \r\n163, 165, 166, 167' WHERE `hris_position`.`position_id` = 2;

### APRIL 27, 2019 ### status => DONE
CREATE TABLE `hris_compensation_reports` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(30) NOT NULL , `payroll_ref_no` VARCHAR(30) NOT NULL , `sss` DOUBLE NOT NULL , `philhealth` DOUBLE NOT NULL , `pagibig` DOUBLE NOT NULL , `tax` DOUBLE NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_compensation_reports` ADD `cutoff_from` DATE NOT NULL AFTER `tax`, ADD `cutoff_to` DATE NOT NULL AFTER `cutoff_from`;

### MAY 2, 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Bank/index/', 'Bank', 'Manage Bank Information', '0', '8', '2019-05-02 00:00:00', '0', '1');
UPDATE `hris_position` SET `access_content_nav` = '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162, \r\n163, 165, 166, 167, 168' WHERE `hris_position`.`position_id` = 2;
ALTER TABLE `time_record_summary_trial` CHANGE `date_created` `date_created` DATETIME NOT NULL;

### MAY 3, 2019 ### status => DONE
INSERT INTO `hris_content_navigation`
(`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES
(NULL, 'profile/View_profile/index/', 'View Profile', 'View your personal information here', '0', '16', '2019-04-06 00:00:00', '0', '1');

INSERT INTO `hris_content_navigation`
(`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES
(NULL, 'profile/Edit_profile/index/', 'Employee Profile', 'View and manage your personal information here', '0', '16', '2019-04-06 00:00:00', '0', '1');

### MAY 4, 2019 ### status => DONE
INSERT INTO `hris_main_navigation` (`main_nav_id`, `main_nav_desc`, `main_nav_icon`, `main_nav_href`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES (NULL, 'Leave', 'fa-sticky-note', 'leave_home', 'acb_leave', 'cb_leave', '19', '2019-05-04 00:00:00', '2019-05-04 00:00:00', '1');
UPDATE `hris_position` SET `access_nav` = '16, 17, 18, 19, 20, 21' WHERE `hris_position`.`position_id` = 9;
UPDATE `hris_main_navigation` SET `arrangement` = '20' WHERE `hris_main_navigation`.`main_nav_id` = 20;
INSERT INTO `hris_main_navigation` (`main_nav_id`, `main_nav_desc`, `main_nav_icon`, `main_nav_href`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES (NULL, 'Attendance', 'fa-bar-chart', 'attendance_home', 'acb_attendance', 'cb_attendance', '21', '2019-05-04 00:00:00', '2019-05-04 00:00:00', '1');
UPDATE `hris_position` SET `access_nav` = '16, 17, 18, 19, 20, 21, 22' WHERE `hris_position`.`position_id` = 9;

### May 10, 2019 ### status => DONE
CREATE TABLE `hris_payslip` ( `id` INT NOT NULL , `employee_idno` VARCHAR(100) NOT NULL ,
  `name` VARCHAR(100) NOT NULL , `paytype_desc` VARCHAR(20) NOT NULL , `date_from` DATE NOT NULL , `date_to` DATE NOT NULL ,
  `gross_salary` DOUBLE NOT NULL , `days_duration` INT(10) NOT NULL , `overtime` DOUBLE NOT NULL , `ot_duration` INT(10) NOT NULL ,
  `additionals` DOUBLE NOT NULL , `regular_holiday` DOUBLE NOT NULL , `regular_holiday_duration` INT(10) NOT NULL ,
  `special_holiday` DOUBLE NOT NULL , `special_holiday_duration` INT(10) NOT NULL , `sundays` DOUBLE NOT NULL , `sunday_duration` INT(10)
  NOT NULL , `absent` DOUBLE NOT NULL , `absent_duration` INT(10) NOT NULL , `late` DOUBLE NOT NULL , `late_duration` INT NOT NULL ,
  `undertime` DOUBLE NOT NULL , `undertime_duration` INT NOT NULL , `sss` DOUBLE NOT NULL , `philhealth` DOUBLE NOT NULL , `pag-ibig` DOUBLE NOT NULL ,
  `sss_loan` DOUBLE NOT NULL , `pag_ibig_loan` DOUBLE NOT NULL , `cashadvance` DOUBLE NOT NULL , `salary_deduction` DOUBLE NOT NULL ,
  `total_deductions` DOUBLE NOT NULL , `date_created` DATETIME NOT NULL , `enabled` INT NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `hris_payslip` CHANGE `pag-ibig` `pag_ibig` DOUBLE NOT NULL;
ALTER TABLE `hris_payslip` ADD `netpay` DOUBLE NOT NULL AFTER `total_deductions`;

### MAY 15, 2019 ### status => DONE
ALTER TABLE `hris_position` ADD `hierarchy_lvl` DOUBLE NOT NULL AFTER `access_content_nav`;
UPDATE `hris_position` SET `hierarchy_lvl` = '1' WHERE `hris_position`.`position_id` = 2;
UPDATE `hris_position` SET `hierarchy_lvl` = '2' WHERE `hris_position`.`position_id` = 3;
UPDATE `hris_position` SET `hierarchy_lvl` = '3' WHERE `hris_position`.`position_id` = 4;
UPDATE `hris_position` SET `hierarchy_lvl` = '4' WHERE `hris_position`.`position_id` = 5;
UPDATE `hris_position` SET `hierarchy_lvl` = '5' WHERE `hris_position`.`position_id` = 6;
UPDATE `hris_position` SET `hierarchy_lvl` = '6' WHERE `hris_position`.`position_id` = 7;
UPDATE `hris_position` SET `hierarchy_lvl` = '7' WHERE `hris_position`.`position_id` = 8;
UPDATE `hris_position` SET `hierarchy_lvl` = '8' WHERE `hris_position`.`position_id` = 9;

### MAY 17, 2019 ### status => DONE
TRUNCATE TABLE time_record_summary;

### MAY 21, 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/SSS_reports/index/', 'SSS Reports', 'Reports regarding about monthly SSS contribution', '0', '10', '2019-05-21 00:00:00', '0', '1');
UPDATE `hris_position` SET `access_content_nav` = '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162, \r\n163, 165, 166, 167, 168, 171' WHERE `hris_position`.`position_id` = 2;

### MAY 22, 2019 ### status => DONE
DROP TABLE `hris_payslip`;

CREATE TABLE `hris_payslip` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(100) NOT NULL , `name` VARCHAR(100) NOT NULL ,
  `paytype_desc` VARCHAR(20) NOT NULL , `date_from` DATE NOT NULL , `date_to` DATE NOT NULL , `gross_salary` DOUBLE NOT NULL , `days_duration` INT(10) NOT NULL ,
  `overtime` DOUBLE NOT NULL , `ot_duration` INT(10) NOT NULL , `additionals` DOUBLE NOT NULL , `regular_holiday` DOUBLE NOT NULL , `regular_holiday_duration` INT(10) NOT NULL ,
  `special_holiday` DOUBLE NOT NULL , `special_holiday_duration` INT(10) NOT NULL , `sundays` DOUBLE NOT NULL , `sunday_duration` INT(10) NOT NULL , `absent` DOUBLE NOT NULL ,
  `absent_duration` INT(10) NOT NULL , `late` DOUBLE NOT NULL , `late_duration` INT(10) NOT NULL , `undertime` DOUBLE NOT NULL , `undertime_duration` INT(10) NOT NULL , `sss` DOUBLE NOT NULL ,
  `philhealth` DOUBLE NOT NULL , `pag_ibig` DOUBLE NOT NULL , `sss_loan` DOUBLE NOT NULL , `philhealth_loan` DOUBLE NOT NULL , `pag_ibig_loan` DOUBLE NOT NULL , `cashadvance` DOUBLE NOT NULL ,
  `salary_deduction` DOUBLE NOT NULL , `total_deductions` DOUBLE NOT NULL , `netpay` DOUBLE NOT NULL , `date_created` DATETIME NOT NULL , `enabled` INT(10) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `hris_payslip`
  DROP `philhealth_loan`;

ALTER TABLE `worksite` ADD `distance` INT(100) NOT NULL DEFAULT '200' COMMENT 'Actual Distance in Meters' AFTER `loc_longitude`;

### MAY 23, 2019 ### status => DONE
UPDATE `paytype` SET `date_range` = '12-17' WHERE `paytype`.`paytypeid` = 2;
ALTER TABLE `hris_payroll_summary` ADD `pay_day` DATE NOT NULL AFTER `department_id`;
CREATE TABLE `hris_sss_reports` ( `id` INT NOT NULL AUTO_INCREMENT , `sss_no` VARCHAR(30) NOT NULL , `month` VARCHAR(15) NOT NULL , `employee_idno` VARCHAR(30) NOT NULL , `department` INT NOT NULL , `EE` DOUBLE NOT NULL , `ER` DOUBLE NOT NULL , `EC` DOUBLE NOT NULL , `total` DOUBLE NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_sss_reports` ADD `payroll_ref_no` TEXT NOT NULL AFTER `employee_idno`;

### MAY 25, 2019 ### status => DONE
ALTER TABLE `hris_sss_reports` ADD `employee_name` VARCHAR(50) NOT NULL AFTER `employee_idno`;
ALTER TABLE `hris_sss_reports` ADD `department_name` VARCHAR(60) NOT NULL AFTER `department`;
UPDATE `hris_content_navigation` SET `cn_url` = 'settings/Sss_controller/index/' WHERE `hris_content_navigation`.`id` = 17;
UPDATE `hris_content_navigation` SET `cn_url` = 'reports/Sss_reports/index/' WHERE `hris_content_navigation`.`id` = 171;

### MAY 29, 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/Philhealth_reports/index/', 'Philhealth Reports', 'Reports regarding about monthly philhealth contribution', '0', '10', '2019-05-29 00:00:00', '0', '1');
UPDATE `hris_position` SET `access_content_nav` = '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162, \r\n163, 165, 166, 167, 168, 171, 172' WHERE `hris_position`.`position_id` = 2;
CREATE TABLE `hris_philhealth_reports` ( `id` INT NOT NULL AUTO_INCREMENT , `philhealth_no` VARCHAR(30) NOT NULL , `employee_idno` VARCHAR(30) NOT NULL , `employee_name` VARCHAR(50) NOT NULL , `payroll_ref_no` VARCHAR(30) NOT NULL , `department` INT NOT NULL , `department_name` VARCHAR(30) NOT NULL , `EE` DOUBLE NOT NULL , `ER` DOUBLE NOT NULL , `total` DOUBLE NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_philhealth_reports` ADD `month` VARCHAR(15) NOT NULL AFTER `philhealth_no`;

### MAY 31, 2019 ###  status => DONE
CREATE TABLE `hris_employment_history` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(30) NOT NULL , `reason` TEXT NOT NULL , `termination_date` DATE NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `created_by` VARCHAR(50) NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'employees/Employment_history/index/', 'Employment History', 'History of all previous employee', '0', '7', '2019-04-10 00:00:00', '0', '1');
UPDATE `hris_position` SET `access_content_nav` = '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162, \r\n163, 165, 166, 167, 168, 171, 172, 173' WHERE `hris_position`.`position_id` = 2;

### MAY 31, 2019 REN ### status => DONE
ALTER TABLE `worksite` CHANGE `distance` `distance` INT(100) NOT NULL DEFAULT '1000' COMMENT 'Actual Distance in Meters';
UPDATE `hris_content_navigation` SET `status` = '0' WHERE `hris_content_navigation`.`id` = 169;


CREATE TABLE `valid_id_details` ( `id` INT NOT NULL AUTO_INCREMENT , `valid_id_type` VARCHAR(100) NOT NULL ,
  `id_number` VARCHAR(100) NOT NULL , `id_value` INT(45) NOT NULL , `upload_date` DATE NOT NULL , `picture_extension` VARCHAR(100) NOT NULL ,
  `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `valid_id_details` CHANGE `id_value` `id_value` VARCHAR(45) NOT NULL;
ALTER TABLE `valid_id_details` ADD `employee_idno` VARCHAR(100) NOT NULL AFTER `id`;

CREATE TABLE `employee_details_temp`
( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(100)
  NOT NULL , `first_name` VARCHAR(100) NOT NULL , `middle_name`
  VARCHAR(100) NOT NULL , `last_name` VARCHAR(100) NOT NULL , `birthday`
  VARCHAR(100) NOT NULL , `gender` VARCHAR(100) NOT NULL , `marital_status`
  VARCHAR(100) NOT NULL , `home_address_1` VARCHAR(100) NOT NULL , `home_address_2`
  VARCHAR(100) NOT NULL , `country` VARCHAR(100) NOT NULL , `contact_number`
  VARCHAR(100) NOT NULL , `date_created` DATETIME NOT NULL , `enabled`
  INT(10) NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;

UPDATE hris_payslip SET enabled = 1;
ALTER TABLE `hris_payslip` CHANGE `enabled` `enabled` INT(10) NOT NULL DEFAULT '1';

### JUNE 1, 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/Contract_expiration_reports/index/', 'Contract Expiration Reports', 'List of all upcoming contracts that\'s going to expire. ', '0', '10', '2019-05-31 00:00:00', '0', '1');
UPDATE `hris_position` SET `access_content_nav` = '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162, \r\n163, 165, 166, 167, 168, 171, 172, 173, 174' WHERE `hris_position`.`position_id` = 2;

### JUNE 4, 2019 ### status => DONE ### NEED TO THINK ABOUT THIS
UPDATE `hris_content_navigation` SET `arrangement` = '1' WHERE `hris_content_navigation`.`id` = 145;
UPDATE `hris_content_navigation` SET `arrangement` = '1' WHERE `hris_content_navigation`.`id` = 155;
UPDATE `hris_content_navigation` SET `arrangement` = '1' WHERE `hris_content_navigation`.`id` = 27;
UPDATE `hris_content_navigation` SET `arrangement` = '1' WHERE `hris_content_navigation`.`id` = 173;
UPDATE `hris_content_navigation` SET `arrangement` = '1' WHERE `hris_content_navigation`.`id` = 163;

### JUNE 7, 2019 ###
TRUNCATE TABLE additional_pays;
TRUNCATE TABLE applicant_dependents;
TRUNCATE TABLE applicant_education;
TRUNCATE TABLE applicant_form_link;
TRUNCATE TABLE applicant_record;
TRUNCATE TABLE applicant_workhistory;
TRUNCATE TABLE cash_advance_pay;
TRUNCATE TABLE cash_advance_payment_scheme;
TRUNCATE TABLE cash_advance_tran;
TRUNCATE TABLE contract;
TRUNCATE TABLE contract_payout_medium;
TRUNCATE TABLE deduction;
TRUNCATE TABLE employee_dependents;
TRUNCATE TABLE employee_charges;
TRUNCATE TABLE employee_dependents;
TRUNCATE TABLE employee_details_temp;
TRUNCATE TABLE employee_education;
TRUNCATE TABLE employee_photos;
TRUNCATE TABLE employee_record;
TRUNCATE TABLE employee_workhistory;
TRUNCATE TABLE hris_additional_log;
TRUNCATE TABLE hris_additional_summary;
TRUNCATE TABLE hris_announcement;
TRUNCATE TABLE hris_compensation_reports;
TRUNCATE TABLE hris_deduction_log;
TRUNCATE TABLE hris_deduction_summary;
TRUNCATE TABLE hris_employment_history;
TRUNCATE TABLE hris_hrassists;
TRUNCATE TABLE hris_manhours_log;
TRUNCATE TABLE hris_manhours_summary;
TRUNCATE TABLE hris_payroll_log;
TRUNCATE TABLE hris_payroll_summary;
TRUNCATE TABLE hris_payslip;
TRUNCATE TABLE hris_philhealth_reports;
TRUNCATE TABLE hris_sss_reports;
TRUNCATE TABLE leave_tran;
TRUNCATE TABLE overtime_pays;
TRUNCATE TABLE pagibig;
TRUNCATE TABLE payoutmedium;
TRUNCATE TABLE paytype;
TRUNCATE TABLE salary_deduction;
TRUNCATE TABLE time_record_summary;
TRUNCATE TABLE time_record_summary_trial;
TRUNCATE TABLE time_record_summary_range;
TRUNCATE TABLE valid_id_details;
TRUNCATE TABLE work_order;
TRUNCATE TABLE work_order_itenerary;
TRUNCATE TABLE work_schedule;
DELETE FROM hris_users WHERE position_id > 2;

### JUNE 11, 2019 ### status => DONE (UNSURE)
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/Attendance_graph_analysis/index/', 'Attendance Graph Analysis', 'Attendance Graph Analysis reports of all employees in hris', '0', '10', '2019-05-31 00:00:00', '0', '1');
UPDATE `hris_position` SET `access_content_nav` = '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162, \r\n163, 165, 166, 167, 168, 171, 172, 173, 174, 175' WHERE `hris_position`.`position_id` = 2;

### JUNE 15, 2019 ### status => DONE (UNSURE)
INSERT INTO `hris_main_navigation` (`main_nav_id`, `main_nav_desc`, `main_nav_icon`, `main_nav_href`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES (NULL, 'Evaluations', 'fa-id-badge', 'evaluations_home', 'acb_evaluations', 'cb_evaluations', '22', '2019-05-04 00:00:00', '2019-05-04 00:00:00', '1');
UPDATE `hris_position` SET `access_nav` = '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 19, 23' WHERE `hris_position`.`position_id` = 2;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'evaluations/Evaluations/index/', 'Evaluation History', 'List of all evaluation history', '0', '23', '2019-06-15 00:00:00', '0', '1');
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'evaluations/Evaluations_settings/index/', 'Evaluations Settings', 'Edit Evaluations Form', '0', '23', '2019-06-15 00:00:00', '0', '1');
UPDATE `hris_position` SET `access_content_nav` = '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162, \r\n163, 165, 166, 167, 168, 171, 172, 173, 174, 175, 176, 177' WHERE `hris_position`.`position_id` = 2;

### JUNE 16, 2019 ### status => DONE
CREATE TABLE `hris_evaluations` ( `id` INT NOT NULL AUTO_INCREMENT , `ref_no` VARCHAR(100) NOT NULL , `management_id` VARCHAR(100) NOT NULL , `employee_idno` VARCHAR(100) NOT NULL , `department_id` INT NOT NULL , `eval_type` VARCHAR(100) NOT NULL , `eval_score` DOUBLE NOT NULL , `eval_remarks` TEXT NOT NULL , `eval_recommendations` TEXT NOT NULL , `eval_purpose_type` VARCHAR(100) NOT NULL , `eval_purpose` VARCHAR(255) NOT NULL , `eval_comments` TEXT NOT NULL , `eval_project` TEXT NOT NULL , `eval_proj_comment` TEXT NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `hris_eval_ratings` ( `id` INT NOT NULL AUTO_INCREMENT , `eval_type` ENUM('type_1','type_2') NOT NULL DEFAULT 'type_1' , `rating` DOUBLE NOT NULL , `description` TEXT NOT NULL , `equivalent_rating` VARCHAR(100) NOT NULL , `score` VARCHAR(100) NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `hris_eval_questions` ( `id` INT NOT NULL AUTO_INCREMENT , `title` VARCHAR(100) NOT NULL , `description` TEXT NOT NULL , `section` VARCHAR(10) NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `hris_eval_purpose` ( `id` INT NOT NULL AUTO_INCREMENT , `title` VARCHAR(100) NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `hris_eval_section` ( `id` INT NOT NULL AUTO_INCREMENT , `section` VARCHAR(10) NOT NULL , `title` VARCHAR(100) NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_eval_ratings` (`id`, `eval_type`, `rating`, `description`, `equivalent_rating`, `score`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'type_1', '5', 'Contributions have tremendous and consistently positive impact and value to the department and or the organization. May be unique, often one-time achievements that measurably improve progress towards organizational goals. Easily recognized as a top performer compared to peers. Viewed as an excellent resource for providing training, guidance, and support to others. Demonstrates high-level capabilities and proactively takes on higher levels of responsibility. ', 'OUTSTANDING PERFORMANCE (O) ', '95%-100% ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'type_1', '4', 'Consistently demonstrates high level of performance. Consistently works toward overall objectives of the department and or organization. Viewed as a role model in position. Demonstrates high levels of effort, effectiveness, and judgment with limited or no supervision. ', 'Very Good Performance (VG) ', '86%-94%  ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'type_1', '3', 'Consistently demonstrates effective performance. Performance is reflective of a fully qualified and experienced individual in this position. Viewed as someone who gets the job done and effectively prioritizes work. Contributes to the overall objectives of the department and or the organization. Achieves valuable accomplishments in several critical areas of the job. ', 'Good Performance (G) ', '80%–85% ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'type_1', '2', 'Working toward gaining proficiency. Demonstrates satisfactory performance inconsistently. Achieves some but not all goals and is acquiring necessary knowledge and skills. ', 'Fair Performance (F) ', '75%–79% ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'type_1', '1', 'The quality of performance is inadequate and shows little or no improvement. Knowledge, skills, and abilities have not been demonstrated at appropriate levels. ', 'Poor Performance (P) ', '74%–below ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
INSERT INTO `hris_eval_section` (`id`, `section`, `title`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'A', 'TECHNICAL FACTORS RATINGS', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'B', 'CORE VALUES', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'C', 'POLICY-ORIENTED FACTORS', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'D', 'OVER-ALL ASSESSMENT ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
INSERT INTO `hris_eval_questions` (`id`, `title`, `description`, `section`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'KNOWLEDGE OF WORK', 'Evaluate the employee’s familiarity with all phases and details of the job. Measures effectiveness in keeping knowledgeable in methods, techniques and required skills; \r\nremaining current on new technologies and be able to apply it into the job in a short span of time.', 'A', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'QUALITY OF WORK', ' Evaluate the employee’s ability to deliver products that conforms to its requirements, work reliably and accurately in its intended manner, delivered on time, and freefrom defects. Take into account clients’ feedbacks ', 'A', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'QUANTITY OF WORK', 'Evaluate the employee’s ability to produce large amount / volume of work efficiently. Consider the number of projects and tasks accomplished (refer to Appendix page) ', 'A', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'DECISION MAKING/PROBLEM SOLVING', 'Evaluate the employee’s ability to identify problem areas, gather facts and making timely, practical decisions. ', 'A', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'ADMINISTRATION', 'Measures employee’s effectiveness in planning, organizing and efficiently handling work hours and eliminating unnecessary activities.', 'A', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'COMMITMENT', 'Make things happen by  creatively maximizing our resources to ensure each others success. Evaluate the employees’ tendency towards self-initiated actions without waiting for instructions. ', 'B', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'ACCOUNTABILITY', 'he obligations of an individual or organization to account for its activities, accept responsibilities for  them and to disclose the results  in a transparent manner. Makes carefully weighed decisions and accepts consequences for action and willingness to assume Responsibility. ', 'B', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'RESPONSIVENESS', 'The quality of reacting quickly and positively. Strive to be the best, not just better or good enough. Have the highest chance of becoming productive. Paying attention, care enough about what he or she is talking about, no waiting time. ', 'B', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'TRUSTWORTHY ', 'Honesty in everything you do; Take into consideration the employee’s willingness to put company interests above self-interest. Be reliable and keep your word. When you say that you will do something for someone, then do it. Make good friends with Manager/ \r\nSupervisor, Colleagues, Subordinates Clients and Customers. ', 'B', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'TEAMWORK AND COOPERATION', 'he process of working collaboratively with a group of people in order to achieve the common goal. Always seeks ways to continuously improve existing procedure  or process to prevent recurrence of problems. Actively participates in any problem solving activities with his team and uses these opportunities to coach and guide his staff. Proactively working together and being accountable to each other to achieve  \r\nour goal. Has a positive approach towards work, company policies and people. ', 'B', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'ADHERENCE TO COMPANY RULES & REGULATIONS', 'Evaluate the employees’ conformity  to company rules and regulations and policies. ', 'C', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'ATTENDANCE & PUNCTUALITY', 'Consider the employee’s absences and tardiness based on Attendance reports ', 'C', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
INSERT INTO `hris_eval_purpose` (`id`, `title`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'Promotion', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Change Position', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Transfer (Dept., Section, Area)', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Dismissal / End of Contract ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Others (specify)', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
CREATE TABLE `hris_eval_recommendations` ( `id` INT NOT NULL AUTO_INCREMENT , `description` TEXT NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_eval_recommendations` (`id`, `description`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'Identify the employee’s strengths and other areas for improvement ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Necessary steps to improve employee’s performance  ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Training needs of the employee ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
CREATE TABLE `hris_eval_formula` ( `id` INT NOT NULL AUTO_INCREMENT , `formula` VARCHAR(255) NOT NULL DEFAULT '(Total_Points / 60) * 100' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_eval_formula` (`id`, `formula`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, '(Total_Points / 60) * 100', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
ALTER TABLE `hris_evaluations` ADD `status` ENUM('delivered','seen') NOT NULL DEFAULT 'delivered' AFTER `updated_at`;
ALTER TABLE `hris_evaluations` ADD `eval_date` DATE NOT NULL AFTER `eval_proj_comment`, ADD `eval_from` DATE NOT NULL AFTER `eval_date`, ADD `eval_to` DATE NOT NULL AFTER `eval_from`;
ALTER TABLE `hris_evaluations` CHANGE `eval_type` `eval_type` ENUM('type_1','type_2') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'type_1';
UPDATE `hris_position` SET `access_nav` = '19, 23' WHERE `hris_position`.`position_id` = 5;
UPDATE `hris_position` SET `access_content_nav` = '176' WHERE `hris_position`.`position_id` = 5;
ALTER TABLE `hris_evaluations` ADD `status2` ENUM('ongoing','evaluated','certified') NOT NULL DEFAULT 'ongoing' AFTER `status`;
ALTER TABLE `hris_evaluations` ADD `eval_score_percent` INT NOT NULL AFTER `eval_score`, ADD `eval_equivalent_rate` VARCHAR(100) NOT NULL AFTER `eval_score_percent`;
ALTER TABLE `hris_evaluations` ADD `eval_action_hr` TEXT NOT NULL AFTER `eval_to`, ADD `certify_by` VARCHAR(100) NOT NULL AFTER `eval_action_hr`;

### JUNE 27, 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/User_role/index/', 'User Role', 'Manage user role for each hris position', '0', '8', '2019-06-15 00:00:00', '0', '1');
UPDATE `hris_position` SET `access_content_nav` = '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162, \r\n163, 165, 166, 167, 168, 171, 172, 173, 174, 175, 176, 177, 178' WHERE `hris_position`.`position_id` = 2;

### JUNE 29, 2019 ### status => DONE
CREATE TABLE `hris_rfid` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(155) NOT NULL , `rf_number` VARCHAR(155) NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### JULY 4, 2019 ### status => DONE
CREATE TABLE `hris_biometrics_id` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(100) NOT NULL , `bio_id` VARCHAR(100) NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_main_navigation` (`main_nav_id`, `main_nav_desc`, `main_nav_icon`, `main_nav_href`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES (NULL, 'Register Id', 'fa-id-card-o', 'registerid_home', 'acb_registerid', 'cb_registerid', '23', '2019-07-04 00:00:00', '2019-07-04 00:00:00', '1');
UPDATE `hris_position` SET `access_nav` = '19, 7, 23, 15, 24, 10, 8, 13, 14' WHERE `hris_position`.`position_id` = 2;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'registerid/Register_bio/index/', 'Rf Id Number ', 'Register or update rf id number', '0', '24', '2019-07-04 00:00:00', '0', '1');
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'registerid/Register_rf/index/', 'Boimetrics Id', 'Register or update biometrics id', '0', '24', '2019-07-04 00:00:00', '0', '1');
UPDATE `hris_position` SET `access_content_nav` = '145, 155, 27, 173, 163, 161, 168, 132, 156, 6, 7, 2, 10, 3, 134, 21, 15, 162, 133, 1, 135, 4, 8, 18, 22, 5, 16, 17, 12, 9, 20, 160, 178, 14, 13, 175, 165, 167, 174, 172, 171, 166, 149, 136, 147, 139, 141, 157, 143, 148, 140, 150, 158, 159, 176, 177, 179, 180' WHERE `hris_position`.`position_id` = 2;

### JULY 11, 2019 ### status => PENDING NEED TO THING ABOUT THIS
ALTER TABLE `hris_position`
  DROP `deptId`,
  DROP `subDeptId`;

### JULY 24, 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'registerid/Register_facial/index/', 'Register Facial Features', 'Register facial features for attendance facial recognition', '0', '24', '2019-07-04 00:00:00', '0', '1');

### JULY 25, 2019 ### status => DONE
CREATE TABLE `hris_facial_recog` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(100) NOT NULL , `facial_landmarks` TEXT NOT NULL , `accuracy` INT NOT NULL , `img_src` VARCHAR(255) NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_facial_recog` ADD `descriptor` TEXT NOT NULL AFTER `accuracy`;

### JULY 26, 2019 ### status => DONE HRIS CP
UPDATE `hris_position` SET `access_content_nav` = '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162, \r\n163, 165, 166, 167, 168, 171, 172, 173, 174, 175' WHERE `hris_position`.`position_id` = 2;

### JULY 30, 2019 ### status => DONE
CREATE TABLE `hris_worksched_settings` ( `id` INT NOT NULL AUTO_INCREMENT , `min_whours` FLOAT NOT NULL , `max_whours` FLOAT NOT NULL , `min_bhours` FLOAT NOT NULL , `max_bhours` FLOAT NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_worksched_settings` (`id`, `min_whours`, `max_whours`, `min_bhours`, `max_bhours`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, '8', '12', '1', '2', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');

### AUG 7, 2019 ### status => DONE
CREATE TABLE `hris_timelog_logs` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(100) NOT NULL , `admin_id` VARCHAR(100) NOT NULL , `logs` TEXT NOT NULL , `time_in` TIME NOT NULL , `time_out` TIME NOT NULL , `date` DATE NOT NULL , `status` ENUM('update','delete') NOT NULL , `type` ENUM('timelog','workorder') NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_timelog_logs` DROP `time_in`, DROP `time_out`;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'time_record/Timerecord_logs/index/', 'Time Record Logs', 'List of all activity in time record', '0', '13', '2019-07-04 00:00:00', '0', '1')

### AUG 15, 2019 ### status => DONE
CREATE TABLE `hris_companies` ( `id` INT NOT NULL AUTO_INCREMENT , `company` VARCHAR(150) NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `contract` ADD `company_id` INT NOT NULL AFTER `position_access_lvl`;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Companies/index/', 'Companies', 'Manage the list of companies inside HRIS', '0', '8', '2019-06-15 00:00:00', '0', '1');
ALTER TABLE `hris_additional_summary` CHANGE `department_id` `company_id` INT(11) NOT NULL;
ALTER TABLE `hris_deduction_summary` CHANGE `department_id` `company_id` INT(11) NOT NULL;
ALTER TABLE `hris_manhours_summary` CHANGE `department_id` `company_id` INT(11) NOT NULL;
ALTER TABLE `hris_payroll_summary` CHANGE `department_id` `company_id` INT(11) NOT NULL;

### AUG 16, 2019 ### status => DONE
ALTER TABLE `hris_additional_summary` ADD `department_id` INT NOT NULL AFTER `company_id`;
ALTER TABLE `hris_deduction_summary` ADD `department_id` INT NOT NULL AFTER `company_id`;
ALTER TABLE `hris_manhours_summary` ADD `department_id` INT NOT NULL AFTER `company_id`;
ALTER TABLE `hris_payroll_summary` ADD `department_id` INT NOT NULL AFTER `company_id`;

### AUG 17, 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/Pagibig_reports/index/', 'Pagibig Reports', 'Reports regarding about monthly Pagibig contribution', '0', '10', '2019-05-21 00:00:00', '0', '1');
CREATE TABLE `hris_pagibig_reports` ( `id` INT NOT NULL AUTO_INCREMENT , `pagibig_no` VARCHAR(50) NOT NULL , `month` VARCHAR(15) NOT NULL , `employee_idno` VARCHAR(50) NOT NULL , `employee_name` VARCHAR(50) NOT NULL , `payroll_ref_no` VARCHAR(50) NOT NULL , `department` INT NOT NULL , `department_name` VARCHAR(100) NOT NULL , `EE` DOUBLE NOT NULL , `ER` DOUBLE NOT NULL , `total` DOUBLE NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_pagibig_reports` ADD `company_id` INT NOT NULL AFTER `payroll_ref_no`;
ALTER TABLE `hris_pagibig_reports` ADD `company_name` VARCHAR(100) NOT NULL AFTER `company_id`;
ALTER TABLE `hris_sss_reports` ADD `company_id` INT NOT NULL AFTER `payroll_ref_no`, ADD `company_name` VARCHAR(100) NOT NULL AFTER `company_id`;
ALTER TABLE `hris_philhealth_reports` ADD `company_id` INT NOT NULL AFTER `payroll_ref_no`, ADD `company_name` VARCHAR(100) NOT NULL AFTER `company_id`;

### AUG 20, 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'transactions/Sss_loans/index/', 'SSS Loans', 'Manage all SSS Loans', '0', '14', '2019-02-27 00:00:00', '0', '1');
CREATE TABLE `hris_sss_loans` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(100) NOT NULL , `sss_loan_voucher` VARCHAR(155) NOT NULL , `sss_loan_start` DATE NOT NULL , `sss_loan_end` DATE NOT NULL , `sss_deduction_start` DATE NOT NULL , `sss_total_loan` DOUBLE NOT NULL , `monthly_amortization` DOUBLE NOT NULL , `status` ENUM('active','done') NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_sss_loans` ADD `sss_total_balance` DOUBLE NOT NULL AFTER `sss_total_loan`;
ALTER TABLE `hris_sss_loans` ADD `sss_total_paid` DOUBLE NOT NULL AFTER `sss_total_balance`;

### AUG 27, 2019 ### status => DONE
CREATE TABLE `cashadvance_pending_deduction` ( `id` INT NOT NULL AUTO_INCREMENT , `ca_id` INT NOT NULL , `ca_payment` DOUBLE NOT NULL , `ca_balance` DOUBLE NOT NULL , `ca_from` DATE NOT NULL , `ca_to` DATE NOT NULL , `status` ENUM('pending','approved') NOT NULL DEFAULT 'pending' , `enabled` INT NOT NULL DEFAULT '1' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `cashadvance_pending_deduction` ADD `employee_idno` VARCHAR(100) NOT NULL AFTER `ca_id`;
ALTER TABLE `cashadvance_pending_deduction` ADD `payroll_refno` VARCHAR(100) NOT NULL AFTER `employee_idno`;
CREATE TABLE `hris_sss_loan_pending_deduction` ( `id` INT NOT NULL AUTO_INCREMENT , `sss_loan_id` INT NOT NULL , `employee_idno` VARCHAR(100) NOT NULL , `payroll_refno` VARCHAR(100) NOT NULL , `monthly_amortization` DOUBLE NOT NULL , `status` ENUM('pending','approved') NOT NULL DEFAULT 'pending' , `enabled` INT NOT NULL DEFAULT '1' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_sss_loan_pending_deduction` ADD `sss_loan_from` DATE NOT NULL AFTER `status`, ADD `sss_loan_to` DATE NOT NULL AFTER `sss_loan_from`, ADD `payday` DATE NOT NULL AFTER `sss_loan_to`;

### SEP 05, 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'transactions/Pagibig_loans/index/', 'Pagibig Loans', 'Manage all PagibigLoans', '0', '14', '2019-02-27 00:00:00', '0', '1');
CREATE TABLE `hris_pagibig_loans` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(100) NOT NULL , `pagibig_loan_voucher` VARCHAR(155) NOT NULL , `pagibig_loan_start` DATE NOT NULL , `pagibig_loan_end` DATE NOT NULL , `pagibig_deduction_start` DATE NOT NULL , `pagibig_total_loan` DOUBLE NOT NULL , `pagibig_total_balance` DOUBLE NOT NULL , `pagibig_total_paid` DOUBLE NOT NULL , `monthly_amortization` DOUBLE NOT NULL , `status` ENUM('active','done') NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `hris_pagibig_loan_pending_deduction` ( `id` INT NOT NULL AUTO_INCREMENT , `pagibig_loan_id` INT NOT NULL , `employee_idno` VARCHAR(100) NOT NULL , `payroll_refno` VARCHAR(100) NOT NULL , `monthly_amortization` DOUBLE NOT NULL , `status` ENUM('pending','approved') NOT NULL , `pagibig_loan_from` DATE NOT NULL , `pagibig_loan_to` DATE NOT NULL , `payday` DATE NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### SEP 10, 2019 ### status => DONE
INSERT INTO `hris_eval_section` (`id`, `section`, `title`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'E', 'LEADERSHIP-ORIENTED FACTORS', '2019-09-10 12:24:41', '0000-00-00 00:00:00', '1');
UPDATE `hris_eval_section` SET `title` = 'LEADERSHIP-ORIENTED FACTORS' WHERE `hris_eval_section`.`id` = 4;
UPDATE `hris_eval_section` SET `title` = 'OVER-ALL ASSESSMENT ' WHERE `hris_eval_section`.`id` = 5;
INSERT INTO `hris_eval_ratings` (`id`, `eval_type`, `rating`, `description`, `equivalent_rating`, `score`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'type_2', '5', 'Models the way', 'OUTSTANDING PERFORMANCE (O) ', '95%-100%', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'type_2', '4', 'Always exhibits competency', 'Very Good Performance (VG) ', '86%-94% ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'type_2', '3', 'Exhibits competency most of the time', 'Good Performance (G) ', '80%-85% ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'type_2', '2', 'Exhibits competency half of the time or occasionally ', 'Fair Performance (F) ', '75%-79% ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'type_2', '1', 'Does not exhibits competency', 'Poor Performance (P) ', '75%-below', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
INSERT INTO `hris_eval_questions` (`id`, `title`, `description`, `section`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'Product / Technical Knowledge', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Energy, Determination and Work Rate', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
INSERT INTO `hris_eval_questions` (`id`, `title`, `description`, `section`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'Problem Solving and Decision Making', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Adaptability, Flexibility, and Mobility', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Planning, Budgeting and forecasting', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Time Management - Meeting deadlines and commitments', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Commercial Judgement', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Team working and developing others', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Delegation skills', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Communication skills', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Reporting and Administration', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Creativity', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Steadiness under pressure - Composure', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Corporate responsibility and Professional ethics', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Personal appearance and image', 'N/A', 'D', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
INSERT INTO `hris_eval_formula` (`id`, `formula`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, '(Total_Points / 135) * 100', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
ALTER TABLE `hris_eval_formula` ADD `type` ENUM('type_1','type_2') NOT NULL AFTER `formula`;
UPDATE `hris_eval_formula` SET `type` = 'type_2' WHERE `hris_eval_formula`.`id` = 2;
CREATE TABLE `hris_eval_self_assessment` ( `id` INT NOT NULL AUTO_INCREMENT , `question` TEXT NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_eval_self_assessment` (`id`, `question`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'What is your understanding of your main duties and responsibilities?', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'What element or part of your job that you find most difficult?  ', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'What element/ part of your job interest you the most? And least?', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'What do you consider your most important achievement/s from past months/year? You can specify specific task/s.', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'What are your aims/goals/objectives/tasks for the coming months/year?', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'What action could be taken to improve your performance in your current position by you? And by your boss? You may indicate personal strength and passions you would like also to develop that can benefit you and your work.', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'What kind of work or job do you like to be doing in the following months/ year?', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
ALTER TABLE `hris_evaluations` ADD `eval_assessment` TEXT NOT NULL AFTER `eval_recommendations`;

### SEPT 17, 2019 ### status => DONE
CREATE TABLE `hris_exchange_rates` ( `id` INT NOT NULL AUTO_INCREMENT , `base` VARCHAR(5) NOT NULL DEFAULT 'PHP' , `currency_code` VARCHAR(5) NOT NULL , `currency_name` VARCHAR(50) NOT NULL , `exchange_rate` DOUBLE NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_exchange_rates` (`id`, `base`, `currency_code`, `currency_name`, `exchange_rate`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'PHP', 'PHP', 'Philippines', '1.00', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Exchange_rates/index/', 'Exchange Rates', 'Manage Exchange Rates of HRIS', '0', '8', '2019-06-15 00:00:00', '0', '1');
ALTER TABLE `contract` ADD `currency` VARCHAR(5) NOT NULL DEFAULT 'PHP' AFTER `total_sal`;

### SEPT 18, 2019 ### status => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/Incident_reports/index/', 'Incident Reports', 'Reports regarding about incidents', '0', '10', '2019-05-21 00:00:00', '0', '1');
CREATE TABLE `hris_incident_reports` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(50) NOT NULL , `position_id` INT NOT NULL , `dept_id` INT NOT NULL , `date_reported` DATE NOT NULL , `date_happened` DATE NOT NULL , `time_of_incidence` TIME NOT NULL , `place_of_incidence` VARCHAR(100) NOT NULL , `resulting_damage` TEXT NOT NULL , `incident_brief` TEXT NOT NULL , `reported_by` VARCHAR(50) NOT NULL , `reporting_dept_head` VARCHAR(50) NOT NULL , `concerned_dept_head` VARCHAR(50) NOT NULL , `hr_dept_head` VARCHAR(50) NOT NULL , `account_dept_head` VARCHAR(50) NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `status` ENUM('active','inactive') NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_incident_reports` ADD `reporting_head_id` VARCHAR(50) NOT NULL AFTER `reported_by`, ADD `concerned_head_id` VARCHAR(50) NOT NULL AFTER `reporting_head_id`;

### SEPT 19, 2019 ### status => PENDING -> SEMI DONE NOT SURE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/Memos/index/', 'Memorandum', 'Reports regarding about all memorandum in hris', '0', '10', '2019-09-20 00:00:00', '0', '1');
CREATE TABLE `hris_memorandum` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(50) NOT NULL , `dept_id` VARCHAR(50) NOT NULL , `re` VARCHAR(100) NOT NULL , `date` DATE NOT NULL , `memo_file` VARCHAR(255) NOT NULL , `status` ENUM('approved','pending') NOT NULL DEFAULT 'pending' , `createt_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### SEPT 24, 2019 ### status => DONE
ALTER TABLE `contract` ADD `total_sal_converted` DOUBLE NOT NULL AFTER `total_sal`;

### SEPT 28, 2019 ### status => PENDING
UPDATE `hris_content_navigation` SET `cn_url` = 'payroll/Payroll_history_new/index/' WHERE `hris_content_navigation`.`id` = 159;

### OCT 3, 2019 ### status => DONE
ALTER TABLE `hris_payroll_log` ADD `contract_refno` VARCHAR(100) NOT NULL AFTER `emp_id`;
ALTER TABLE `hris_manhours_log` ADD `contract_refno` VARCHAR(100) NOT NULL AFTER `emp_id`;
ALTER TABLE `hris_deduction_log` ADD `contract_refno` VARCHAR(100) NOT NULL AFTER `employee_idno`;
ALTER TABLE `hris_additional_log` ADD `contract_refno` VARCHAR(100) NOT NULL AFTER `emp_id`;
ALTER TABLE `hris_additional_log` ADD `currency` VARCHAR(5) NOT NULL DEFAULT 'PHP' AFTER `overtimepay`, ADD `ex_rate` DOUBLE NOT NULL DEFAULT '1.00' AFTER `currency`;
ALTER TABLE `hris_deduction_log` ADD `currency` VARCHAR(5) NOT NULL DEFAULT 'PHP' AFTER `salary_deduction`, ADD `ex_rate` DOUBLE NOT NULL DEFAULT '1.00' AFTER `currency`;
ALTER TABLE `hris_payroll_log` ADD `currency` VARCHAR(5) NOT NULL DEFAULT 'PHP' AFTER `others2`, ADD `ex_rate` DOUBLE NOT NULL DEFAULT '1.00' AFTER `currency`;
ALTER TABLE `hris_manhours_log` ADD `currency` VARCHAR(5) NOT NULL DEFAULT 'PHP' AFTER `sunday`, ADD `ex_rate` DOUBLE NOT NULL DEFAULT '1.00' AFTER `currency`;

### OCT 8, 2019 ### status => DONE
CREATE TABLE `hris_branch` ( `id` INT NOT NULL AUTO_INCREMENT , `branch_name` VARCHAR(100) NOT NULL , `branch_code` VARCHAR(100) NOT NULL , `username` VARCHAR(100) NOT NULL , `password` VARCHAR(255) NOT NULL , `fname` VARCHAR(100) NOT NULL , `mname` VARCHAR(100) NOT NULL , `lname` VARCHAR(100) NOT NULL , `timezone` VARCHAR(50) NOT NULL , `country_code` VARCHAR(10) NOT NULL , `database_name` VARCHAR(50) NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### OCT 10, 2019 ### status => DONE
CREATE NEW DATABASE cloudpanda-hris_main;
CREATE TABLE `hris_admin_user` ( `id` INT NOT NULL AUTO_INCREMENT , `username` VARCHAR(100) NOT NULL , `password` VARCHAR(255) NOT NULL , `fname` VARCHAR(50) NOT NULL , `mname` VARCHAR(50) NOT NULL , `lname` VARCHAR(50) NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### OCT 11, 2019 ### status => DONE
ALTER TABLE `hris_admin_user` ADD `position_id` INT NOT NULL AFTER `lname`;
INSERT INTO `hris_main_navigation` (`main_nav_id`, `main_nav_desc`, `main_nav_icon`, `main_nav_href`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES (NULL, 'Branch', 'fa-university', 'branch_home', 'acb_branch_home', 'cb_branch_home', '24', '2019-10-11 00:00:00', '2019-10-11 00:00:00', '1');

### OCT 22, 2019 ### status => DONE LIVE
ALTER TABLE `contract` CHANGE `work_site_id` `work_site_id` VARCHAR(50) NOT NULL;

### OCT 24, 2019 ### status => DONE LIVE-------------------------------------------------------------------
ALTER TABLE `contract` ADD `contract_type` ENUM('fixed','open') NOT NULL DEFAULT 'fixed' AFTER `updated_at`;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Contract_template/index/', 'Contract Template', 'Manage available contract template', '0', '8', '2019-09-20 00:00:00', '0', '1');

### NOV 5, 2019 ### status => DONE
ALTER TABLE `contract` ADD `base_pay` DOUBLE NOT NULL DEFAULT '0' AFTER `sal_cat`;

### NOV 7, 2019 ### status => PENDING
ALTER TABLE `hris_branch` ADD `location` ENUM('online','offline') NOT NULL DEFAULT 'offline' AFTER `timezone`;

### NOV 11, 2019 ### status => DONE
CREATE TABLE `hris_registered_device` ( `id` INT NOT NULL AUTO_INCREMENT , `activation_code` VARCHAR(100) NOT NULL , `device_id` VARCHAR(100) NOT NULL , `status` ENUM('open','closed') NOT NULL DEFAULT 'open' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Registered_device/index/', 'Registered Device', 'Manage all registered device for time in/out in HRIS', '0', '8', '2019-09-20 00:00:00', '0', '1');

### NOV 15, 2019 ### status => DONE
CREATE TABLE `hris_contract_template` ( `id` INT NOT NULL AUTO_INCREMENT , `template_name` VARCHAR(50) NOT NULL , `template_format` TEXT NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `hris_template_settings` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(100) NOT NULL , `field_name` VARCHAR(100) NOT NULL , `table_name` VARCHAR(100) NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_template_settings` (`id`, `name`, `field_name`, `table_name`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'First Name', 'first_name', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Middle Name', 'middle_name', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Last Name', 'last_name', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Birthday', 'birthday', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Gender', 'gender', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Marital Status', 'marital_status', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Home Address 1', 'home_address1', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Home Address 2', 'home_address2', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'City', 'city', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Country', 'country', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Contact No.', 'contact_no', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Email', 'email', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'SSS Number', 'sss_no', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Philhealth Number', 'philhealth_no', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Pagibig Number', 'pagibig_no', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Tin number', 'tin_no', 'employee_record', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
INSERT INTO `hris_template_settings` (`id`, `name`, `field_name`, `table_name`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'Contract Start', 'Contract End', 'contract', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Salary', 'sal_cat', 'contract', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Basic Pay', 'base_pay', 'contract', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Total Salary', 'total_sal', 'contract', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Total Converted Salary to Peso', 'total_sal_converted', 'contract', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Leave', 'emp_leave', 'contract', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1'), (NULL, 'Total Leave', 'total_leave', 'contract', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');

### NOV 21, 2019 ### status => DONE
ALTER TABLE `work_schedule` CHANGE `break_sched` `break_sched` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `contract` CHANGE `emp_lvl` `emp_lvl` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `hris_users` CHANGE `avatar_file` `avatar_file` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

### NOV 22, 2019 status => PENDING
ALTER TABLE `contract` CHANGE `sss` `sss` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `contract` CHANGE `philhealth` `philhealth` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `contract` CHANGE `pagibig` `pagibig` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `contract` CHANGE `tax` `tax` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `hris_users` CHANGE `user_mname` `user_mname` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE `contract` CHANGE `sss` `sss` INT(11) NULL DEFAULT '0';
ALTER TABLE `contract` CHANGE `philhealth` `philhealth` INT(11) NULL DEFAULT '0';
ALTER TABLE `contract` CHANGE `pagibig` `pagibig` INT(11) NULL DEFAULT '0';
ALTER TABLE `contract` CHANGE `tax` `tax` INT(11) NULL DEFAULT '0';

### NOV 23, 2019 status => PENDING
CREATE TABLE `hris_contract_files` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(100) NOT NULL , `contract_id` INT NOT NULL , `template_id` INT NOT NULL , `content` TEXT NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### NOV 27, 2019 ### => DONE
ALTER TABLE `employee_record` CHANGE `city` `city` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '';
ALTER TABLE `employee_record` CHANGE `country` `country` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '';
### NOV 27, 2019 ### => DONE
ALTER TABLE `applicant_record` CHANGE `app_city` `app_city` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '';
ALTER TABLE `applicant_record` CHANGE `app_country` `app_country` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '';

### NOV 28, 2019 ### => DONE
ALTER TABLE `worksite` CHANGE `city` `city` INT(11) NULL DEFAULT '0';

### NOV 29, 2019 ### => DONE
ALTER TABLE `time_record_summary_trial` CHANGE `type` `type` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'auto';
ALTER TABLE `time_record_summary_trial` CHANGE `status_absent` `status_absent` INT(20) NOT NULL DEFAULT '0';

### DEC 03, 2019 ### => PENDING
ALTER TABLE `applicant_record` CHANGE `app_status` `app_status` ENUM('in_process','interview','fail_interview','job_offer','reject_joboffer','requirements','hired') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'in_process';
CREATE TABLE `hris_requirements` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(100) NOT NULL , `file_path` VARCHAR(255) NOT NULL , `req_type` ENUM('resume','2x2_pic','colloge_diploma','tor','2_valid_id','tin','sss_e1_form','philhealth_no','pagibig_no','psa_birth_certificate','marriage_certificate','child_birth_certificate','nbi_clearance','police_clearance','brgy_clearance','med_certificate') NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `position` CHANGE `levelid` `levelid` INT(11) NOT NULL DEFAULT '0' COMMENT 'level->levelid';
ALTER TABLE `position` CHANGE `user_id` `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT 'user who create/update';

### DEC 06, 2019 ### => DONE
CREATE TABLE `hris_contract_audit_trail` ( `id` INT NOT NULL AUTO_INCREMENT , `contract_id` VARCHAR(100) NOT NULL , `prev_contract_id` VARCHAR(100) NOT NULL , `employee_idno` VARCHAR(100) NOT NULL , `audit_trail` TEXT NOT NULL , `fields` VARCHAR(100) NOT NULL, `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
CREATE TABLE `hris_applicant_interview` ( `id` INT NOT NULL AUTO_INCREMENT , `app_ref_no` VARCHAR(100) NOT NULL , `interviewer` VARCHAR(100) NOT NULL , `interview_notes` TEXT NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;

### DEC 11, 2019 ### => DONE
CREATE TABLE `hris_applicant_job_offer` ( `id` INT NOT NULL AUTO_INCREMENT , `app_ref_no` VARCHAR(100) NOT NULL , `content` TEXT NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/Contract_audit_trail_reports/index/', 'Contract Audit Trail Reports', 'Reports of all the changes happening in contracts.', '0', '10', '2019-12-12 00:00:00', '0', '1')

### DEC 17, 2019 ### => DONE
ALTER TABLE `employee_record` CHANGE `app_ref_no` `app_ref_no` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `work_schedule` CHANGE `break_sched` `break_sched` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `additional_pays` CHANGE `approved_by` `approved_by` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `additional_pays` CHANGE `certified_by` `certified_by` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `overtime_pays` CHANGE `approved_by` `approved_by` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `overtime_pays` CHANGE `certified_by` `certified_by` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `cash_advance_tran` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `cash_advance_tran` CHANGE `certified_by` `certified_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_pagibig_loans` CHANGE `pagibig_total_loan` `pagibig_total_loan` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_pagibig_loans` CHANGE `pagibig_total_paid` `pagibig_total_paid` DOUBLE NULL DEFAULT '0';
ALTER TABLE `hris_pagibig_loans` CHANGE `pagibig_total_balance` `pagibig_total_balance` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `salary_deduction` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `salary_deduction` CHANGE `certified_by` `certified_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_sss_loans` CHANGE `sss_total_loan` `sss_total_loan` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_sss_loans` CHANGE `sss_total_balance` `sss_total_balance` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_sss_loans` CHANGE `sss_total_paid` `sss_total_paid` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `leave_tran` CHANGE `balance` `balance` INT(100) NOT NULL DEFAULT '0';
ALTER TABLE `leave_tran` CHANGE `hrd` `hrd` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `leave_tran` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `leave_tran` CHANGE `certified_by` `certified_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `work_order` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `work_order` CHANGE `certified_by` `certified_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_manhours_summary` CHANGE `department_id` `department_id` INT(11) NULL DEFAULT NULL;
ALTER TABLE `hris_manhours_summary` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_manhours_summary` CHANGE `approved_date` `approved_date` DATE NULL;
ALTER TABLE `hris_deduction_summary` CHANGE `department_id` `department_id` INT(11) NULL DEFAULT NULL;
ALTER TABLE `hris_deduction_summary` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_deduction_summary` CHANGE `approved_date` `approved_date` DATE NULL;
ALTER TABLE `hris_deduction_summary` CHANGE `total_deduction` `total_deduction` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_additional_summary` CHANGE `department_id` `department_id` INT(11) NULL DEFAULT NULL;
ALTER TABLE `hris_additional_summary` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_additional_summary` CHANGE `approved_date` `approved_date` DATE NULL;
ALTER TABLE `hris_payroll_summary` CHANGE `department_id` `department_id` INT(11) NULL DEFAULT NULL;
ALTER TABLE `hris_payroll_summary` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_payroll_summary` CHANGE `approved_date` `approved_date` DATE NULL;
ALTER TABLE `hris_manhours_log` CHANGE `days` `days` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `hours` `hours` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `absent` `absent` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `late` `late` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `ut` `ut` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `ot` `ot` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `adj1` `adj1` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `adj2` `adj2` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `holiday1` `holiday1` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `holiday2` `holiday2` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `nightdiff` `nightdiff` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `sunday` `sunday` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_manhours_log` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_deduction_log` CHANGE `deduction_total` `deduction_total` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_deduction_log` CHANGE `sss` `sss` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_deduction_log` CHANGE `sss_loan` `sss_loan` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_deduction_log` CHANGE `philhealth` `philhealth` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_deduction_log` CHANGE `philhealth_loan` `philhealth_loan` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_deduction_log` CHANGE `pag_ibig` `pag_ibig` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_deduction_log` CHANGE `pag_ibig_loan` `pag_ibig_loan` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_deduction_log` CHANGE `cashadvance` `cashadvance` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_deduction_log` CHANGE `salary_deduction` `salary_deduction` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_deduction_log` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_additional_log` CHANGE `additionalpay` `additionalpay` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_additional_log` CHANGE `overtimepay` `overtimepay` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_additional_log` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_payroll_log` CHANGE `deductions` `deductions` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_payroll_log` CHANGE `grosspay` `grosspay` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_payroll_log` CHANGE `additionals` `additionals` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_payroll_log` CHANGE `netpay` `netpay` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_payroll_log` CHANGE `others1` `others1` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_payroll_log` CHANGE `others2` `others2` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_payroll_log` CHANGE `approved_by` `approved_by` VARCHAR(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_compensation_reports` CHANGE `sss` `sss` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_compensation_reports` CHANGE `philhealth` `philhealth` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_compensation_reports` CHANGE `pagibig` `pagibig` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_compensation_reports` CHANGE `tax` `tax` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_evaluations` CHANGE `eval_score` `eval_score` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_evaluations` CHANGE `eval_score_percent` `eval_score_percent` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `hris_evaluations` CHANGE `eval_equivalent_rate` `eval_equivalent_rate` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_evaluations` CHANGE `eval_remarks` `eval_remarks` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hris_evaluations` CHANGE `eval_recommendations` `eval_recommendations` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hris_evaluations` CHANGE `eval_assessment` `eval_assessment` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hris_evaluations` CHANGE `eval_purpose_type` `eval_purpose_type` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hris_evaluations` CHANGE `eval_purpose` `eval_purpose` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hris_evaluations` CHANGE `eval_comments` `eval_comments` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hris_evaluations` CHANGE `eval_project` `eval_project` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hris_evaluations` CHANGE `eval_proj_comment` `eval_proj_comment` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hris_evaluations` CHANGE `certify_by` `certify_by` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_evaluations` CHANGE `eval_action_hr` `eval_action_hr` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hris_incident_reports` CHANGE `reporting_dept_head` `reporting_dept_head` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_incident_reports` CHANGE `concerned_dept_head` `concerned_dept_head` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_incident_reports` CHANGE `hr_dept_head` `hr_dept_head` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `hris_incident_reports` CHANGE `account_dept_head` `account_dept_head` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';

### DEC 18, 2019 ### STATUS => DONE
ALTER TABLE `hris_position` CHANGE `access_nav` `access_nav` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hris_position` CHANGE `access_sub_nav` `access_sub_nav` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `hris_position` CHANGE `access_content_nav` `access_content_nav` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL COMMENT 'jcw_sub_navigation -> sub_nav_id ';
ALTER TABLE `hris_registered_device` CHANGE `device_id` `device_id` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `salarycat` CHANGE `type` `type` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `sss` CHANGE `SV_VM_OFW` `SV_VM_OFW` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `hris_users` CHANGE `date_updated` `date_updated` TIMESTAMP NOT NULL;
ALTER TABLE `hris_position` CHANGE `date_updated` `date_updated` TIMESTAMP NOT NULL;
ALTER TABLE `hris_position` CHANGE `date_created` `date_created` TIMESTAMP NOT NULL;
ALTER TABLE `worksite` CHANGE `loc_latitude` `loc_latitude` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `worksite` CHANGE `loc_longitude` `loc_longitude` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

### JAN 7, 2020 ### STATUS => DONE
ALTER TABLE `applicant_form_link` CHANGE `app_status` `app_status` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `applicant_record` ADD `app_status` ENUM('in_process','interview','fail_interview','job_offer','reject_joboffer','requirements','hired') NOT NULL DEFAULT 'in_process' AFTER `app_isActive`;

### JAN 8, 2020 ### STATUS => DONE
ALTER TABLE `hris_contract_files` CHANGE `template_id` `template_id` INT(11) NULL;
ALTER TABLE `hris_contract_files` CHANGE `content` `content` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

### JAN 9, 2020 ### STATUS => DONE
ALTER TABLE `employee_record` ADD `first_month` INT NOT NULL DEFAULT '0' AFTER `tin_no`;

### JAN 21, 2020 ### STATUS => PENDING
ALTER TABLE `hris_eval_ratings` ADD `rating_2` VARCHAR(10) NULL DEFAULT '' AFTER `rating`;
UPDATE `hris_eval_ratings` SET `rating_2` = '4.5 to 5.0' WHERE `hris_eval_ratings`.`id` = 1;
UPDATE `hris_eval_ratings` SET `rating_2` = '3.5 to 4.4' WHERE `hris_eval_ratings`.`id` = 2;
UPDATE `hris_eval_ratings` SET `rating_2` = '2.5 to 3.4' WHERE `hris_eval_ratings`.`id` = 3;
UPDATE `hris_eval_ratings` SET `rating_2` = '1.5 to 2.4' WHERE `hris_eval_ratings`.`id` = 4;
UPDATE `hris_eval_ratings` SET `rating_2` = '0 to 1.4' WHERE `hris_eval_ratings`.`id` = 5;
ALTER TABLE `hris_eval_questions` ADD `template` INT NOT NULL DEFAULT '1' AFTER `section`;
UPDATE `hris_eval_questions` SET `template` = '2' WHERE `hris_eval_questions`.`id` = 1;
UPDATE `hris_eval_questions` SET `template` = '2' WHERE `hris_eval_questions`.`id` = 2;
UPDATE `hris_eval_questions` SET `template` = '2' WHERE `hris_eval_questions`.`id` = 3;
UPDATE `hris_eval_questions` SET `template` = '2' WHERE `hris_eval_questions`.`id` = 4;
UPDATE `hris_eval_questions` SET `template` = '2' WHERE `hris_eval_questions`.`id` = 11;
UPDATE `hris_eval_questions` SET `template` = '2' WHERE `hris_eval_questions`.`id` = 12;
ALTER TABLE `hris_eval_questions` ADD `weights` DOUBLE NULL DEFAULT '0' AFTER `template`;
UPDATE `hris_eval_questions` SET `weights` = '20' WHERE `hris_eval_questions`.`id` = 1;
UPDATE `hris_eval_questions` SET `weights` = '20' WHERE `hris_eval_questions`.`id` = 2;
UPDATE `hris_eval_questions` SET `weights` = '20' WHERE `hris_eval_questions`.`id` = 3;
UPDATE `hris_eval_questions` SET `weights` = '20' WHERE `hris_eval_questions`.`id` = 4;
UPDATE `hris_eval_questions` SET `weights` = '10' WHERE `hris_eval_questions`.`id` = 11;
UPDATE `hris_eval_questions` SET `weights` = '10' WHERE `hris_eval_questions`.`id` = 12;
UPDATE `hris_eval_questions` SET `template` = '2' WHERE `hris_eval_questions`.`id` = 6;
UPDATE `hris_eval_questions` SET `template` = '2' WHERE `hris_eval_questions`.`id` = 7;
UPDATE `hris_eval_questions` SET `template` = '2' WHERE `hris_eval_questions`.`id` = 8;
UPDATE `hris_eval_questions` SET `template` = '2' WHERE `hris_eval_questions`.`id` = 9;
UPDATE `hris_eval_questions` SET `template` = '2' WHERE `hris_eval_questions`.`id` = 10;
UPDATE `hris_eval_questions` SET `weights` = '20' WHERE `hris_eval_questions`.`id` = 6;
UPDATE `hris_eval_questions` SET `weights` = '20' WHERE `hris_eval_questions`.`id` = 7;
UPDATE `hris_eval_questions` SET `weights` = '20' WHERE `hris_eval_questions`.`id` = 8;
UPDATE `hris_eval_questions` SET `weights` = '20' WHERE `hris_eval_questions`.`id` = 9;
UPDATE `hris_eval_questions` SET `weights` = '20' WHERE `hris_eval_questions`.`id` = 10;

### JAN 22 2020 ### => DONE
ALTER TABLE `applicant_dependents` CHANGE `birthday` `birthday` DATE NULL DEFAULT '0000-00-00';
ALTER TABLE `employee_record` CHANGE `first_month` `first_month` INT(11) NOT NULL DEFAULT '1';
UPDATE employee_record SET first_month = 1 WHERE first_month = 0;
ALTER TABLE `employee_dependents` CHANGE `birthday` `birthday` DATE NULL DEFAULT '0000-00-00';

### JAN 24, 2020 ### => PENDING [ MAIN ]
INSERT INTO `hris_main_navigation` (`main_nav_id`, `main_nav_desc`, `main_nav_icon`, `main_nav_href`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES (NULL, 'Transfer Data', 'fa-exchange', 'transfer_home', 'acb_transfer_home', 'cb_transfer_home', '26', '2020-01-24 00:00:00', '2020-01-24 00:00:00', '1');
UPDATE `hris_position` SET `access_nav` = '25, 26' WHERE `hris_position`.`position_id` = 1;

### JAN 28, 2019 ### => DONE
ALTER TABLE `leaves` ADD `days_before_filling` INT NULL DEFAULT '0' AFTER `description`;
ALTER TABLE `leave_tran` ADD `paid` ENUM('with_pay','without_pay') NULL DEFAULT 'without_pay' AFTER `status`;
ALTER TABLE `hris_payslip` ADD `payroll_refno` VARCHAR(100) NULL AFTER `employee_idno`;
ALTER TABLE `hris_payslip` ADD `gross_salary_less` DOUBLE NOT NULL DEFAULT '0' AFTER `gross_salary`;

### JAN 29, 2019 ### => DONE
ALTER TABLE `time_record_summary_trial` CHANGE `current_location` `current_location` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

### JAN 31, 2019 ### => DONE
CREATE TABLE `hris_functions` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(100) NOT NULL , `main_nav_access` TEXT NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_functions` (`id`, `name`, `main_nav_access`, `created_at`, `updated_at`, `enabled`) VALUES
(1, 'Create', '177', '2020-01-31 11:13:11', '0000-00-00 00:00:00', 1),
(2, 'View', '', '2020-01-31 11:12:04', '0000-00-00 00:00:00', 0),
(3, 'Update', '177', '2020-01-31 11:13:08', '0000-00-00 00:00:00', 1),
(4, 'Delete', '177', '2020-01-31 11:13:06', '0000-00-00 00:00:00', 1),
(5, 'Evaluate', '176', '2020-01-31 11:09:00', '0000-00-00 00:00:00', 1),
(6, 'Approve', '139, 140, 141, 143, 147, 148, 150, 157, 185, 186', '2020-01-31 11:05:10', '0000-00-00 00:00:00', 1),
(7, 'Certify', '139, 140, 141, 143, 147, 148, 150, 157, 185, 186', '2020-01-31 11:05:14', '0000-00-00 00:00:00', 1);
ALTER TABLE `hris_position` ADD `access_func_nav` TEXT NULL AFTER `access_content_nav`;

### FEB 4 2020 ### => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Access_functions/index/', 'Content Navigation Functions', 'Manage which content navigation will be able to access create, edit, view, delete, approve, certify and evaluate.', '0', '8', '2019-09-20 00:00:00', '0', '1');

### FEB 12 2020, ### => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Clockinout_settings/index/', 'Clock In / Out', 'Manage the rules and regulation for clock in / out .', '0', '8', '2019-09-20 00:00:00', '0', '1');
CREATE TABLE `hris_clockinout_settings` ( `id` INT NOT NULL AUTO_INCREMENT , `rules` VARCHAR(100) NOT NULL , `description` TEXT NULL , `minutes` INT NOT NULL , `status` ENUM('on','off') NOT NULL DEFAULT 'off' , `type` ENUM('late','undertime','overtime','half_day','over_break','default') NOT NULL DEFAULT 'default' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_clockinout_settings` (`id`, `rules`, `description`, `minutes`, `status`, `type`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'Lates', 'Set the grace period for lates', '30', 'on', 'late', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
ALTER TABLE `hris_contract_template` ADD `template_type` ENUM('default','job_offer','agreement','addendum') NOT NULL DEFAULT 'default' AFTER `template_format`;

### FEB 14 2020, ###  => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Work_schedule/index/', 'Work Schedule', 'Manage the work schedule for particular department or employee.', '0', '8', '2019-09-20 00:00:00', '0', '1');
CREATE TABLE `hris_custom_schedule` ( `id` INT NOT NULL AUTO_INCREMENT , `department_id` INT NOT NULL DEFAULT '0' , `employee_idno` VARCHAR(155) NULL , `date_from` DATE NOT NULL , `date_to` DATE NOT NULL , `work_sched` TEXT NOT NULL , `type` ENUM('department','employee') NOT NULL DEFAULT 'department' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_custom_schedule` ADD `status` ENUM('waiting','approve','certify') NOT NULL DEFAULT 'waiting' AFTER `type`;

### FEB 19 2020 ###  => DONE
CREATE TABLE `hris_clockinout_deductions` ( `id` INT NOT NULL AUTO_INCREMENT , `department_id` INT NOT NULL DEFAULT '0' , `deductions` DOUBLE NOT NULL DEFAULT '0.00' , `type` ENUM('late','undertime','overbreak') NOT NULL , `status` ENUM('on','off') NOT NULL DEFAULT 'off' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_clockinout_deductions` ADD `min_from` INT NOT NULL DEFAULT '0' AFTER `deductions`, ADD `min_to` INT NOT NULL DEFAULT '0' AFTER `min_from`;
ALTER TABLE `hris_clockinout_deductions` CHANGE `deductions` `min_deduct` INT NOT NULL DEFAULT '0';
ALTER TABLE `hris_clockinout_deductions` ADD `whours` INT NOT NULL DEFAULT '0' COMMENT 'minus break time' AFTER `min_to`;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Clockinout_deductions/index/', 'Clock In / Out Deductions', 'Manage the deductions for every late, undertime , overbreak', '0', '8', '2019-09-20 00:00:00', '0', '1');
ALTER TABLE `hris_requirements` CHANGE `req_type` `req_type` ENUM('resume','two_by_two_pic','college_diploma','tor','two_valid_id','tin','sss_e1_form','philhealth_no','pagibig_no','psa_birth_certificate','marriage_certificate','child_birth_certificate','nbi_clearance','police_clearance','brgy_clearance','med_certificate') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

### FEB 21 2020 ### => DONE
ALTER TABLE `work_order` ADD `rejected_by` VARCHAR(100) NULL AFTER `certified_by`;
ALTER TABLE `work_order` CHANGE `status` `status` ENUM('waiting','approved','certified','rejected') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `work_order` ADD `reject_reason` TEXT NULL AFTER `status`;
ALTER TABLE `overtime_pays` ADD `rejected_by` VARCHAR(100) NULL AFTER `certified_by`;
ALTER TABLE `overtime_pays` CHANGE `status` `status` ENUM('waiting','approved','certified','rejected') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `overtime_pays` ADD `reject_reason` TEXT NULL AFTER `status`;
ALTER TABLE `additional_pays` ADD `rejected_by` VARCHAR(100) NULL AFTER `certified_by`;
ALTER TABLE `additional_pays` CHANGE `status` `status` ENUM('waiting','approved','certified','rejected') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `additional_pays` ADD `reject_reason` TEXT NULL AFTER `status`;
ALTER TABLE `leave_tran` ADD `rejected_by` VARCHAR(100) NULL AFTER `certified_by`;
ALTER TABLE `leave_tran` CHANGE `status` `status` ENUM('waiting','approved','certified','rejected') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `leave_tran` ADD `reject_reason` TEXT NULL AFTER `status`;
ALTER TABLE `cash_advance_tran` ADD `rejected_by` VARCHAR(100) NULL AFTER `certified_by`;
ALTER TABLE `cash_advance_tran` CHANGE `status` `status` ENUM('waiting','approved','certified','rejected') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `cash_advance_tran` ADD `reject_reason` TEXT NULL AFTER `status`;
ALTER TABLE `salary_deduction` ADD `rejected_by` VARCHAR(100) NULL AFTER `certified_by`;
ALTER TABLE `salary_deduction` CHANGE `status` `status` ENUM('waiting','approved','certified','rejected') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `salary_deduction` ADD `reject_reason` TEXT NULL AFTER `status`;

### FEB 24, 2020 ### => DONE
UPDATE `pb_company_helper` SET `company_logo_small` = 'juanpayroll-logo-05.png' WHERE `pb_company_helper`.`id` = 1;
UPDATE `pb_company_helper` SET `company_logo` = 'juanpayroll-logo-03.png' WHERE `pb_company_helper`.`id` = 1;
UPDATE `pb_company_helper` SET `company_name` = 'JuanPayroll' WHERE `pb_company_helper`.`id` = 1;
UPDATE `pb_company_helper` SET `company_initial` = 'JP' WHERE `pb_company_helper`.`id` = 1;

### FEB 26, 2020 ### => DONE
ALTER TABLE `overtime_pays` ADD `type` ENUM('overtime','offset') NOT NULL DEFAULT 'overtime' AFTER `status`;

### FEB 27, 2020 ### => DONE
ALTER TABLE `work_order` CHANGE `rejected_by` `rejected_by` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `overtime_pays` CHANGE `rejected_by` `rejected_by` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `additional_pays` CHANGE `rejected_by` `rejected_by` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `leave_tran` CHANGE `rejected_by` `rejected_by` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `cash_advance_tran` CHANGE `rejected_by` `rejected_by` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `salary_deduction` CHANGE `rejected_by` `rejected_by` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `overtime_pays` CHANGE `purpose` `purpose` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

### FEB 28, 2020 ###  => PENDING (HINDI NATULOY)
ALTER TABLE `work_schedule` ADD `shift` ENUM('morning','night') NOT NULL DEFAULT 'morning' AFTER `sched_type2`;

### FEB 29, 2020 ### => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Nightdiff/index/', 'Night Differentials', 'Manage Night Differentials computation', '0', '8', '2019-09-20 00:00:00', '0', '1');
CREATE TABLE `hris_nightdiff_settings` ( `id` INT NOT NULL AUTO_INCREMENT , `start` TIME NOT NULL DEFAULT '22:00' , `end` TIME NOT NULL DEFAULT '06:00' , `percent` DOUBLE NOT NULL DEFAULT '10' , `status` ENUM('on','off') NOT NULL DEFAULT 'off' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_nightdiff_settings` (`id`, `start`, `end`, `percent`, `status`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, '22:00:00.000000', '06:00:00.000000', '10', 'off', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
ALTER TABLE `time_record_summary` ADD `night_diff` DOUBLE NOT NULL DEFAULT '0' AFTER `man_hours`;

### MARCH 05, 2020 ### => DONE
ALTER TABLE `empstatus` ADD `leave_pay` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `special_non_working_holiday`;
ALTER TABLE `hris_payslip` ADD `nightdiff_hours` DOUBLE NOT NULL DEFAULT '0' AFTER `sunday_duration`, ADD `night_diff` DOUBLE NOT NULL DEFAULT '0' AFTER `nightdiff_hours`;

### MARCH 06, 2020 ### => DONE
ALTER TABLE `position` ADD `department_access` VARCHAR(255) NOT NULL DEFAULT '' AFTER `pos_access_lvl`;
UPDATE position SET department_access = deptId
ALTER TABLE `holidaytype` ADD `type` ENUM('regular','special') NOT NULL DEFAULT 'regular' AFTER `description`;

### MARCH 10, 2020 ### => DONE
UPDATE hris_content_navigation SET cn_url = `transactions/Work_schedule/index/`, cn_fkey = `14` WHERE cn_name = `Work Schedule`;

### MARCH 11, 2020 ### => DONE
ALTER TABLE `hris_custom_schedule` ADD `total_whours` DOUBLE NOT NULL DEFAULT '0' AFTER `work_sched`, ADD `total_bhours` DOUBLE NOT NULL DEFAULT '0' AFTER `total_whours`;
ALTER TABLE `hris_custom_schedule` ADD `created_by` VARCHAR(155) NULL DEFAULT '' AFTER `status`, ADD `approved_by` VARCHAR(155) NULL DEFAULT '' AFTER `created_by`, ADD `certified_by` VARCHAR(155) NULL DEFAULT '' AFTER `approved_by`, ADD `rejected_by` VARCHAR(155) NULL DEFAULT '' AFTER `certified_by`;
ALTER TABLE `hris_custom_schedule` ADD `reject_reason` TEXT NULL DEFAULT '' AFTER `rejected_by`;
ALTER TABLE `hris_custom_schedule` CHANGE `status` `status` ENUM('waiting','approve','certify','rejected') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'waiting';

### MARCH 12, 2020 ### => DONE
CREATE TABLE `department_type` ( `id` INT NOT NULL AUTO_INCREMENT , `type` VARCHAR(155) NOT NULL DEFAULT '' , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `department_type` (`id`, `type`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'Administration', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '0'), (NULL, 'HumanResource', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '0'), (NULL, 'Accounting', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '0'), (NULL, 'Operations', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '0');
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'transactions/Offset/index/', 'Offset', 'Manage all offset transactions', '0', '14', '2019-09-20 00:00:00', '0', '1');
CREATE TABLE `hris_offset` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(255) NOT NULL , `date_rendered` DATE NOT NULL , `offset_min` INT NOT NULL , `offset_type` ENUM('late','undertime','wholeday') NOT NULL , `status` ENUM('waiting','approved','certified','rejected') NOT NULL , `created_at` TIMESTAMP NOT NULL , `updated_at` TIMESTAMP NOT NULL , `created_by` VARCHAR(255) NOT NULL DEFAULT '' , `approved_by` VARCHAR(255) NOT NULL DEFAULT '' , `certified_by` VARCHAR(255) NOT NULL DEFAULT '' , `rejected_by` VARCHAR(255) NOT NULL DEFAULT '' , `reject_reason` TEXT NOT NULL DEFAULT '' , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `employee_record` ADD `offset_bal` INT NOT NULL DEFAULT '0' AFTER `first_month`;
ALTER TABLE `hris_offset` CHANGE `status` `status` ENUM('waiting','approved','certified','rejected') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'waiting';

### MARCH 19, 2020 ### => DONE
ALTER TABLE `hris_offset` CHANGE `offset_type` `offset_type` ENUM('late','undertime','wholeday','halfday') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

### MARCH 26, 2020 ### => DONE
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'reports/Employee_info/index/', 'Employee Information Reports', 'Reports regarding all the information about employee', '0', '10', '2019-12-12 00:00:00', '0', '1');
CREATE TABLE `department_type` (
  `id` int(11) NOT NULL,
  `type` varchar(155) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `enabled` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `department_type` (`id`, `type`, `created_at`, `updated_at`, `enabled`) VALUES
(1, 'Administration', '2020-03-17 02:26:06', '2020-03-26 03:42:31', 1),
(2, 'HumanResource', '2020-03-17 02:26:06', '2020-03-26 03:42:33', 1),
(3, 'Accounting', '2020-03-17 02:26:06', '2020-03-26 03:42:35', 1),
(4, 'Operations', '2020-03-17 02:26:06', '2020-03-26 03:42:38', 1),
(5, 'Default', '2020-03-26 03:22:10', '2020-03-26 03:42:40', 1);


ALTER TABLE `department` ADD `department_type` INT NOT NULL DEFAULT '5' AFTER `description`;
UPDATE hris_users SET deptId = 0, subDeptId = 0 WHERE user_id IN(1, 2);
INSERT INTO `hris_functions` (`id`, `name`, `main_nav_access`, `created_at`, `updated_at`, `enabled`) VALUES (NULL, 'Reject', '', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');

### MARCH 27, 2020 => PENDING
CREATE TABLE `hris_transaction_email_settings` ( `id` INT NOT NULL AUTO_INCREMENT , `content_nav_id` INT NOT NULL , `department_id` INT NOT NULL , `approver` TEXT NOT NULL DEFAULT '' , `certifier` TEXT NOT NULL DEFAULT '' , `rejector` TEXT NOT NULL DEFAULT '' , `updated_at` TIMESTAMP NOT NULL , `create_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Transaction_email_settings/index/', 'Transaction Email Settings', 'Manage who will receive email on transactions', '0', '8', '2019-09-20 00:00:00', '0', '1');
CREATE TABLE `hris_items_category` ( `id` INT NOT NULL AUTO_INCREMENT , `cat_name` VARCHAR(255) NOT NULL , `updated_at` TIMESTAMP NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Items_category/index/', 'Items Category', 'Manage different kind of item category', '0', '8', '2019-09-20 00:00:00', '0', '1');
CREATE TABLE `hris_issued_items` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(255) NOT NULL DEFAULT '' , `serial_no` VARCHAR(255) NOT NULL DEFAULT '' , `item_condition` ENUM('great','good','damage','') NOT NULL DEFAULT 'great' , `price` DOUBLE NOT NULL DEFAULT '0' , `date_issued` DATE NOT NULL , `date_received` DATE NOT NULL , `date_returned` DATE NULL , `issued_by` VARCHAR(255) NOT NULL , `notes` TEXT NULL , `updated_at` TIMESTAMP NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_issued_items` ADD `item_name` VARCHAR(255) NOT NULL AFTER `employee_idno`, ADD `cat_id` INT NOT NULL AFTER `item_name`;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'employees/Issued_items/index/', 'Issued Items', 'Manage all the issued items on employees', '0', '7', '2019-09-20 00:00:00', '0', '1');
CREATE TABLE `hris_benefits_setting` ( `id` INT NOT NULL AUTO_INCREMENT , `benefits_name` VARCHAR(255) NOT NULL , `updated_at` TIMESTAMP NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'settings/Benefits_settings/index/', 'Benefits Settings', 'Manage available benefits', '0', '8', '2020-03-30 00:00:00', '0', '1');
CREATE TABLE `hris_assign_benefits` ( `id` INT NOT NULL AUTO_INCREMENT , `employee_idno` VARCHAR(255) NOT NULL , `benefits_id` INT NOT NULL , `updated_at` TIMESTAMP NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `hris_assign_benefits` ADD `assign_by` VARCHAR(255) NOT NULL AFTER `benefits_id`;
INSERT INTO `hris_content_navigation` (`id`, `cn_url`, `cn_name`, `cn_description`, `cn_hasline`, `cn_fkey`, `date_created`, `arrangement`, `status`) VALUES (NULL, 'employees/Assign_benefits/index/', 'Assign Benefits', 'Manage which benefits will be assign for employee.', '0', '7', '2020-03-30 00:00:00', '0', '1');
ALTER TABLE `hris_assign_benefits` CHANGE `benefits_id` `benefits_id` VARCHAR(100) NOT NULL;
ALTER TABLE `leaves` ADD `late_filling` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `days_before_filling`, ADD `consecutive_filling` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `late_filling`;
CREATE TABLE `hris_holiday_policy` ( `id` INT NOT NULL AUTO_INCREMENT , `day_before` ENUM('on','off') NOT NULL DEFAULT 'off' , `day_after` ENUM('on','off') NOT NULL DEFAULT 'off' , `before_and_after` ENUM('on','off') NOT NULL DEFAULT 'off' , `updated_at` TIMESTAMP NOT NULL , `created_at` TIMESTAMP NOT NULL , `enabled` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `hris_holiday_policy` (`id`, `day_before`, `day_after`, `before_and_after`, `updated_at`, `created_at`, `enabled`) VALUES (NULL, 'off', 'off', 'off', CURRENT_TIMESTAMP, '0000-00-00 00:00:00.000000', '1');
