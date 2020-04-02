-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 02, 2020 at 06:34 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.1.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cloudpanda-hris_main`
--

-- --------------------------------------------------------

--
-- Table structure for table `additional_pays`
--

CREATE TABLE `additional_pays` (
  `id` int(11) NOT NULL,
  `payroll_ref_no` varchar(255) NOT NULL DEFAULT 'none',
  `employee_id` varchar(100) NOT NULL,
  `date_issued` date NOT NULL,
  `purpose` varchar(100) NOT NULL,
  `amount` int(100) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('waiting','approved','certified') NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `approved_by` varchar(50) NOT NULL,
  `certified_by` varchar(50) NOT NULL,
  `enabled` int(100) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hris_admin_user`
--

CREATE TABLE `hris_admin_user` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `mname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `position_id` int(11) NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT '1',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hris_admin_user`
--

INSERT INTO `hris_admin_user` (`id`, `username`, `password`, `fname`, `mname`, `lname`, `position_id`, `enabled`, `status`, `created_at`, `updated_at`) VALUES
(1, 'cp_marky', '$2y$12$tPuWc4/jXfT67K.ZdvJ.WehLC4oL8S4nha8sCh4ZtmNWvJWhx3luO', 'Marky', '', 'Neri', 1, 1, 'active', '2019-10-11 03:23:11', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `hris_branch`
--

CREATE TABLE `hris_branch` (
  `id` int(11) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `branch_code` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `mname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `timezone` varchar(50) NOT NULL,
  `location` enum('online','offline') NOT NULL DEFAULT 'online',
  `country_code` varchar(10) NOT NULL,
  `database_name` varchar(50) NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hris_branch`
--

INSERT INTO `hris_branch` (`id`, `branch_name`, `branch_code`, `username`, `password`, `fname`, `mname`, `lname`, `timezone`, `location`, `country_code`, `database_name`, `enabled`, `created_at`, `updated_at`) VALUES
(1, 'Cloud Panda', 'cloudpanda', 'admin', '$2y$12$pGgcA25nepYs8xjR2Cx5BOzp5ogOW0z/KkTQ3TE8rZ8AYrBv6T51S', 'Marky', '', 'Neri', 'Asia/Manila', 'offline', 'PH', 'cloudpanda-hris_dev3', 1, '2019-11-07 08:42:55', '0000-00-00 00:00:00'),
(2, 'JC WorldWide', 'jc_worldwide', 'admin', '$2y$12$pGgcA25nepYs8xjR2Cx5BOzp5ogOW0z/KkTQ3TE8rZ8AYrBv6T51S', 'Marky', '', 'Neri', 'Asia/Manila', 'offline', 'PH', 'cloudpanda-hris_jcww_latest', 0, '2019-11-07 08:42:57', '0000-00-00 00:00:00'),
(6, 'JC Premiere', 'hris_jc', 'admin_jc', '$2y$12$fsz6c61IsRUlRAuYKZR3KOcYv6L0sUgmLq18wlh2u0mI.pGad.HZG', 'Marky', '', 'Neri', 'Asia/Manila', 'offline', 'PH', 'cloudpanda-hris_jc', 1, '2019-11-08 03:14:19', '0000-00-00 00:00:00'),
(7, 'JC World Wide', 'hris_jcww', 'jcww_marky', '$2y$12$WBWnufGUY2tOQL.wfMuhuulCC/R.f/tHDd0JuBk2ydzpnmBZDhGEa', 'Marky', '', 'Neir', 'Asia/Manila', 'offline', 'PH', 'cloudpanda-hris_jcww_new', 1, '2019-11-07 08:47:53', '0000-00-00 00:00:00'),
(8, 'Juana Work', 'hris_juanawork', 'admin_juana', '$2y$12$nzeED1w3q1Sgv6HrTniiQu2S6CiX7FQ1.iTlpOa6puystua2U4.XG', 'Marky', '', 'Neri', 'Asia/Manila', 'offline', 'PH', 'cloudpanda-hris_juana', 1, '2020-01-09 10:31:03', '0000-00-00 00:00:00'),
(9, 'Demo', 'demo', 'cp_marky', '$2y$12$YMgODOubmPSOs4e6TEpZCe7BkZrKR0jqlXKgloD9th3xjw1X4kgAy', 'Marky', '', 'Neri', 'Asia/Manila', 'offline', 'PH', 'cloudpanda-hris_demo', 1, '2020-01-28 06:30:59', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `hris_content_navigation`
--

CREATE TABLE `hris_content_navigation` (
  `id` int(11) NOT NULL,
  `cn_url` varchar(255) NOT NULL,
  `cn_name` varchar(255) NOT NULL,
  `cn_description` varchar(255) NOT NULL,
  `cn_hasline` int(11) NOT NULL DEFAULT '0',
  `cn_fkey` int(11) NOT NULL COMMENT 'jcw_main_navigation->id',
  `date_created` datetime NOT NULL,
  `arrangement` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hris_main_navigation`
--

CREATE TABLE `hris_main_navigation` (
  `main_nav_id` int(11) NOT NULL,
  `main_nav_desc` varchar(255) NOT NULL,
  `main_nav_icon` varchar(255) NOT NULL,
  `main_nav_href` varchar(255) NOT NULL COMMENT 'name of function inside of the Main controller',
  `attr_val` varchar(255) NOT NULL COMMENT 'class,id,name attr of checkbox',
  `attr_val_edit` varchar(255) NOT NULL COMMENT 'class,id,name attr of checkbox (edit)',
  `arrangement` int(11) NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hris_main_navigation`
--

INSERT INTO `hris_main_navigation` (`main_nav_id`, `main_nav_desc`, `main_nav_icon`, `main_nav_href`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES
(1, 'Home', 'fa-home', 'home', 'acb_home', 'cb_home', 1, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
(2, 'Sales', 'fa-shopping-cart', 'sales_home', 'acb_sales', 'cb_sales', 2, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
(3, 'Purchases', 'fa-money', 'purchase_home', 'acb_purchases', 'cb_purchases', 3, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
(4, 'Inventory', 'fa-tag', 'inventory_home', 'acb_inventory', 'cb_inventory', 4, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
(5, 'Entity', 'fa-university', 'entity_home', 'acb_entity', 'cb_entity', 5, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
(6, 'Manufacturing', 'fa-refresh', 'manufacturing_home', 'acb_manufacturing', 'cb_manufacturing', 6, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
(7, 'Entity', 'fa-users', 'employees_home', 'acb_employees', 'cb_employees', 7, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
(8, 'Settings', 'fa-cog', 'settings_home', 'acb_settings', 'cb_settings', 99, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
(9, 'Cart Release', 'fa-square', 'cart_home', 'acb_packagecart', 'cb_packagecart', 9, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 0),
(10, 'Reports', 'fa-file-text', 'report_home', 'acb_reports', 'cb_reports', 97, '2018-02-14 00:00:00', '2018-02-14 00:00:00', 1),
(11, 'QR Quick Search', 'fa fa-qrcode', 'qrcode_home', 'acb_qr', 'cb_qr', 11, '2018-07-17 00:00:00', '2018-07-17 00:00:00', 0),
(12, 'Developer Settings', 'fa-wrench', 'dev_settings_home', 'acb_ds', 'cb_ds', 12, '2018-07-26 00:00:00', '2018-07-26 00:00:00', 1),
(13, 'Time Record', 'fa fa-clock-o', 'time_record', 'acb_timerecord', 'cb_timerecord', 13, '2019-01-26 00:00:00', '2019-01-26 00:00:00', 1),
(14, 'Transactions', 'fa fa-money', 'transaction_home', 'acb_tranactions', 'acb_tranactions', 14, '2019-01-26 00:00:00', '2019-01-26 00:00:00', 1),
(15, 'Payroll', 'fa fa-credit-card', 'payroll', 'acb_payroll', 'cp_payroll', 15, '2019-02-28 00:00:00', '2019-02-28 00:00:00', 1),
(16, 'Profile', 'fa-user-circle', 'profile', 'acb_profile', 'cb_profile', 16, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
(17, 'Announcement', 'fa-bullhorn', 'announcement_home', 'acb_announcement', 'cb_announcement', 17, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
(18, 'Payslip', 'fa fa-credit-card', 'payslip_home', 'acb_payslip', 'cb_payslip', 18, '2019-03-28 00:00:00', '2019-03-28 00:00:00', 1),
(19, 'Change Password', 'fa-lock', 'changepass_home', 'acb_changepass', 'cb_changepass', 98, '2019-03-28 00:00:00', '2019-03-28 00:00:00', 1),
(20, 'HR Assist', 'fa-id-badge', 'hrassist_home', 'acb_hrassist', 'cb_hrassist', 20, '2019-03-28 00:00:00', '2019-03-28 00:00:00', 1),
(21, 'Leave', 'fa-sticky-note', 'leave_home', 'acb_leave', 'cb_leave', 19, '2019-05-04 00:00:00', '2019-05-04 00:00:00', 1),
(22, 'Attendance', 'fa-bar-chart', 'attendance_home', 'acb_attendance', 'cb_attendance', 21, '2019-05-04 00:00:00', '2019-05-04 00:00:00', 1),
(23, 'Evaluations', 'fa-id-badge', 'evaluations_home', 'acb_evaluations', 'cb_evaluations', 22, '2019-05-04 00:00:00', '2019-05-04 00:00:00', 1),
(24, 'Register Id', 'fa-id-card-o', 'registerid_home', 'acb_registerid', 'cb_registerid', 23, '2019-07-04 00:00:00', '2019-07-04 00:00:00', 1),
(25, 'HRIS Branch', 'fa-university', 'branch_home', 'acb_branch_home', 'cb_branch_home', 24, '2019-10-11 00:00:00', '2019-10-11 00:00:00', 1),
(26, 'Transfer Data', 'fa-exchange', 'transfer_home', 'acb_transfer_home', 'cb_transfer_home', 26, '2020-01-24 00:00:00', '2020-01-24 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `hris_position`
--

CREATE TABLE `hris_position` (
  `position_id` int(11) NOT NULL,
  `position` varchar(255) NOT NULL,
  `access_nav` text NOT NULL,
  `access_sub_nav` text NOT NULL,
  `access_content_nav` text NOT NULL COMMENT 'jcw_sub_navigation -> sub_nav_id	',
  `hierarchy_lvl` double NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hris_position`
--

INSERT INTO `hris_position` (`position_id`, `position`, `access_nav`, `access_sub_nav`, `access_content_nav`, `hierarchy_lvl`, `date_updated`, `date_created`, `enabled`) VALUES
(1, 'Super User', '25, 26', '', '', 0, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
(2, 'Administrator', '19, 7, 23, 15, 24, 10, 8, 13, 14', '', '145, 155, 27, 173, 163, 161, 168, 132, 156, 6, 183, 7, 2, 10, 3, 134, 21, 187, 15, 162, 133, 1, 135, 4, 8, 18, 22, 5, 16, 17, 12, 9, 20, 160, 178, 14, 13, 175, 165, 167, 174, 188, 189, 184, 172, 171, 166, 182, 149, 136, 147, 139, 141, 157, 143, 148, 186, 140, 185, 150, 158, 159, 176, 177, 180, 181, 179', 1, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
(3, 'HR Manager', '17, 22, 19, 7, 23, 20, 21, 15, 18, 16, 24, 10, 8, 13, 14', '', '145, 155, 27, 173, 163, 161, 168, 132, 156, 6, 183, 7, 2, 10, 3, 134, 21, 187, 15, 162, 133, 1, 135, 4, 8, 18, 22, 5, 16, 17, 12, 20, 14, 13, 175, 165, 167, 174, 188, 189, 184, 172, 171, 166, 149, 136, 147, 139, 141, 157, 143, 148, 186, 140, 185, 150, 158, 159, 164, 176, 177, 180, 181, 179', 2, '2019-05-15 09:58:35', '2019-03-26 00:00:00', 1),
(4, 'HR Supervisor', '19', '', '', 3, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
(5, 'Manager', '19, 23, 10, 14', '', '188, 150, 176', 4, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
(6, 'Supervisor', '19', '', '', 5, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
(7, 'Officer', '19', '', '', 6, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(8, 'Staff', '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 19', '', '720, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 39, 128, 129, 40, 41, 46, 47, 48, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 16, 17, 18, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 132, 133, 134, 135, 136, 139, 140, 141, 142, 143, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 159, 160, 158, 157, 161, 162,  163, 165, 166', 7, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(9, 'Employee', '17, 22, 19, 20, 21, 18, 16, 14', '', '150, 170', 8, '2019-03-26 00:00:00', '2019-03-26 00:00:00', 1),
(10, 'Assistant HR Manager', '', '', '', 2.1, '2019-05-15 09:36:31', '2019-05-15 09:36:31', 1),
(11, 'Assistant Administrator', '19', '', '', 1.1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pb_company_helper`
--

CREATE TABLE `pb_company_helper` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_initial` varchar(255) NOT NULL,
  `company_logo` varchar(255) NOT NULL,
  `company_logo_small` varchar(255) NOT NULL,
  `company_address` varchar(255) NOT NULL,
  `company_website` varchar(255) NOT NULL,
  `company_phone` varchar(255) NOT NULL,
  `company_email` varchar(255) NOT NULL,
  `powered_by` varchar(255) NOT NULL,
  `paypanda_link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pb_company_helper`
--

INSERT INTO `pb_company_helper` (`id`, `company_name`, `company_initial`, `company_logo`, `company_logo_small`, `company_address`, `company_website`, `company_phone`, `company_email`, `powered_by`, `paypanda_link`) VALUES
(1, 'HRIS', 'PB', '1payroll4.jpg', 'pandabookslogo.png', '10th Floor Inoza Tower, 40th St., BGC, Taguig City 1634', 'https://www.pandabooks.ph/', '898-1309', 'support@cloudpanda.ph', 'Powered by <a href=\'http://cloudpanda.ph/\' class=\'external\' style=\'text-decoration:underline;\'>Cloud Panda PH</a>', 'www.paypanda.com.ph/bicore');

-- --------------------------------------------------------

--
-- Table structure for table `pb_userrole_main_nav`
--

CREATE TABLE `pb_userrole_main_nav` (
  `id` int(11) NOT NULL,
  `label_val` varchar(255) NOT NULL,
  `attr_val` varchar(255) NOT NULL,
  `attr_val_edit` varchar(255) NOT NULL,
  `arrangement` int(11) NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pb_userrole_main_nav`
--

INSERT INTO `pb_userrole_main_nav` (`id`, `label_val`, `attr_val`, `attr_val_edit`, `arrangement`, `date_updated`, `date_created`, `enabled`) VALUES
(1, 'Sales', 'acb_sales', 'cb_sales', 1, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
(2, 'Purchases', 'acb_purchases', 'cb_purchases', 2, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
(3, 'Inventory', 'acb_inventory', 'cb_inventory', 3, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
(4, 'Entity', 'acb_entity', 'cb_entity', 4, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
(5, 'Manufacturing', 'acb_manufacturing', 'cb_manufacturing', 5, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
(6, 'Accounts', 'acb_accounts', 'cb_accounts', 6, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
(7, 'Settings', 'acb_settings', 'cb_settings', 7, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
(8, 'Package Cart', 'acb_packagecart', 'cb_packagecart', 8, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
(9, 'Reports', 'acb_reports', 'cb_reports', 9, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1),
(10, 'QR Quick Search', 'acb_qr', 'cb_qr', 10, '2018-09-04 00:00:00', '2018-09-04 00:00:00', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `additional_pays`
--
ALTER TABLE `additional_pays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hris_admin_user`
--
ALTER TABLE `hris_admin_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hris_branch`
--
ALTER TABLE `hris_branch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hris_content_navigation`
--
ALTER TABLE `hris_content_navigation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hris_main_navigation`
--
ALTER TABLE `hris_main_navigation`
  ADD PRIMARY KEY (`main_nav_id`);

--
-- Indexes for table `hris_position`
--
ALTER TABLE `hris_position`
  ADD PRIMARY KEY (`position_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `additional_pays`
--
ALTER TABLE `additional_pays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hris_admin_user`
--
ALTER TABLE `hris_admin_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hris_branch`
--
ALTER TABLE `hris_branch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `hris_content_navigation`
--
ALTER TABLE `hris_content_navigation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hris_main_navigation`
--
ALTER TABLE `hris_main_navigation`
  MODIFY `main_nav_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `hris_position`
--
ALTER TABLE `hris_position`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
