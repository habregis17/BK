-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 27, 2026 at 09:40 AM
-- Server version: 10.3.39-MariaDB-0ubuntu0.20.04.2
-- PHP Version: 7.4.3-4ubuntu2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"whistleblower\",\"table\":\"cases\"},{\"db\":\"whistleblower\",\"table\":\"admin_users\"},{\"db\":\"whistleblower\",\"table\":\"clients\"},{\"db\":\"whistleblower\",\"table\":\"user_client_assignments\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

--
-- Dumping data for table `pma__table_uiprefs`
--

INSERT INTO `pma__table_uiprefs` (`username`, `db_name`, `table_name`, `prefs`, `last_update`) VALUES
('root', 'whistleblower', 'cases', '{\"sorted_col\":\"`id`  DESC\"}', '2025-11-10 06:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2026-04-27 09:39:40', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `whistleblower`
--
CREATE DATABASE IF NOT EXISTS `whistleblower` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `whistleblower`;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` varchar(64) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `Telephone` varchar(256) NOT NULL,
  `user_type` varchar(256) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `name`, `email`, `password_hash`, `Telephone`, `user_type`, `created_at`) VALUES
('ADM2025001', 'HABUWITEKA F REGIS', 'francois.habuwiteka@bdo-ea.com', '$2y$10$ooOFOqIu4eGKZvjdYP8BP.ZvYUikvSQYqoNhSQwMAcWP9l4Zd6uFi', '', 'Super Admin', '2025-07-28 21:13:04'),
('ADM2025002', 'Devote Tuyisabe', 'devote.tuyisabe@bdo-ea.com', '$2y$10$cpFYS9CAL5v1zSOmnUIaWe01gRGAq3F5UuZ9NB9QPpIlbR/mI0Byi', '0788000011', 'BDO User', '2025-11-12 08:51:33'),
('ADM2025003', 'Patrick Sibomana', 'patrick.sibomana@bdo-ea.com', '$2y$10$oXbYLNcpR.dzY1pPhV7HjuMXlV0a.vG7z4EAEbrLfxIooCg42bEoy', '0788000000', 'BDO User', '2025-11-12 08:54:38'),
('ADM2025004', 'Clement Niyitegeka Kabano Egide', 'clement.niyitegeka@bdo-ea.com', '$2y$10$N9W2USpuAP7s6/c2jf4JRu7RpDXtXuh/MIk3nOCzeSutA/rHH3Z4y', '0788000000', 'BDO User', '2025-11-12 08:55:45'),
('ADM2025005', 'Arlette Umwari', 'arlette.umwari@bdo-ea.com', '$2y$10$Lw9qQGDQ5XB8PpgZN2cI0.kOp0gBhcbgPl1ZgGd0csqcSwE9uOTT.', '0788000011', 'BDO User', '2025-11-12 16:56:09');

-- --------------------------------------------------------

--
-- Table structure for table `cases`
--

CREATE TABLE `cases` (
  `id` int(11) NOT NULL,
  `casenumber` varchar(100) NOT NULL,
  `client_token` varchar(64) NOT NULL,
  `affiliation` varchar(50) NOT NULL,
  `identity_choice` varchar(256) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `incident_date` date DEFAULT NULL,
  `incident_description` longtext DEFAULT NULL,
  `files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`files`)),
  `status` varchar(256) NOT NULL DEFAULT 'New',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `incident_when` longtext DEFAULT NULL,
  `incident_where` longtext DEFAULT NULL,
  `incident_division` longtext DEFAULT NULL,
  `updated_by` varchar(150) DEFAULT 'N/A',
  `feedback` varchar(256) DEFAULT NULL,
  `Case_sensitivity` varchar(256) DEFAULT 'Low',
  `Case_manager` varchar(256) NOT NULL,
  `language` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cases`
--

INSERT INTO `cases` (`id`, `casenumber`, `client_token`, `affiliation`, `identity_choice`, `full_name`, `email`, `phone`, `department`, `incident_date`, `incident_description`, `files`, `status`, `submitted_at`, `last_updated`, `incident_when`, `incident_where`, `incident_division`, `updated_by`, `feedback`, `Case_sensitivity`, `Case_manager`, `language`) VALUES
(12, 'CASE-BOKP-TOWAGQ-202511005', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-10 06:35:32', '2025-11-10 06:35:32', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', ''),
(13, 'CASE-BOKP-7NTR64-202511002', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-10 09:16:05', '2025-11-10 09:16:05', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', ''),
(14, 'CASE-BOKP-3OGETV-202511003', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Identifiable', 'Felix', 'uwitonzefelix8@gmail.com', '', '', NULL, 'Hello,nitwa Felix ndi umukiriya wa banki ya Kigali ishami rya Nyanza ndimo guhabwa service mbi aho natse inguzanyo nkahabwa iyo ntasabye kdi nari nujuje ibisabwa,ndabaza niba haba hari amabwiriza make Yaba ari muri bk', '[]', 'Closed', '2025-11-11 13:56:22', '2025-11-12 10:51:52', '', '', 'ishami rya Nyanza ', 'clement.niyitegeka@bdo-ea.com', 'The Credit Department to make a follow up and conclude.', 'Low', 'ADM2025001', ''),
(15, 'CASE-BOKP-QLE3UJ-202511004', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Identifiable', 'Felix', 'uwitonzefelix8@gmail.com', '', '', NULL, 'Hello,nitwa Felix ndi umukiriya wa banki ya Kigali ishami rya Nyanza ndimo guhabwa service mbi aho natse inguzanyo nkahabwa iyo ntasabye kdi nari nujuje ibisabwa,ndabaza niba haba hari amabwiriza make Yaba ari muri bk', '[]', 'Closed', '2025-11-11 13:56:37', '2025-11-12 10:51:10', '', '', 'ishami rya Nyanza ', 'clement.niyitegeka@bdo-ea.com', 'Normal business incident. The Credit department to make a follow and decide accordingly', 'Low', 'ADM2025001', ''),
(16, 'CASE-BOKP-8FSHWV-202511005', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'Closed', '2025-11-12 09:42:29', '2025-11-12 10:48:56', '', '', '', 'clement.niyitegeka@bdo-ea.com', 'No case reported, to close the case', 'Low', 'ADM2025001', 'sw'),
(17, 'CASE-BOKP-S0V1GN-202511006', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'Closed', '2025-11-12 10:23:40', '2025-11-12 10:49:48', '', '', '', 'clement.niyitegeka@bdo-ea.com', 'No case Reported. To be closed', 'Low', 'ADM2025001', 'fr'),
(18, 'CASE-BOKP-HYIR7N-202511007', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-12 13:23:50', '2025-11-12 13:23:50', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(19, 'CASE-ATFR-AWV2SD-202511001', '1248da4eb3798aa0752cfc8ad8500466', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-12 16:53:55', '2025-11-12 16:53:55', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(20, 'CASE-BOKP-G8PAL4-202511008', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-14 07:48:46', '2025-11-14 07:48:46', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(21, 'CASE-BOKP-AE8UV1-202511009', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Identifiable', 'M. PONNUSAMY', 'dean.ponnusamy@gmail.com', '+91 9566020733', '', NULL, 'From.                             15/11/2025                                                                         \r\nM. PONNUSAMY    (Male. Age - 67)\r\nS/O. Maruthasalam\r\nIndian National Crime Reporting Ack No: 32907250038788\r\nMobile: +91 9566020733     \r\nBank of Kigali Ltd case No. ABXF89159937  \r\nIndian Aadhar No: 6339 6054 6797\r\nEmail ID: dean.ponnusamy@gmail.com\r\nD – 52, Third Street, Anna Nagar East, Chennai – 600102, India.\r\n\r\nTo,\r\nHon. Great Bankers of Rwanda.\r\n\r\nRespected Sir,\r\n\r\nSub: Online Financial Fraud – Commodity Trading – Total amount = INR. 8,92,226.68 (Rupees. Eight Lakhs ninety two thousand  two twenty six and sixty eight paise)\r\n\r\n ICICI - Anna Nagar East branch, Chennai, India (Complaint number: SR1065808944)\r\nTotal two transactions Payments made through my ICICI Anna Nagar East branch, Chennai, India, Branch Code: 6027.  My Account No. 605701313061.  IFSC Code No. ICIC0006027\r\n\r\n1).  On 30 / 6 / 2025 paid INR. 4,45,857.16. from my ICICI bank A/C No. 605701313061  to  VIN/BITWAVEINN/202506301324/518107033216//TCS RS 0.00.\r\nBank of Kigali Ltd, Kigali RW. \r\n\r\n2). On 1 / 7 / 2025 paid INR. 4,46,369.68 from my ICICI bank A/C No. 605701313061  to VIN/BITWAVEINN/202507012024/518214039762//TCS Rs 0.00.\r\nBank of Kigali Ltd, Kigali RW.\r\n\r\nSince VIN/BITWAVE INN is the main gang member in this international crime, using fake FXonet trading platform and loot millions from innocent Rwandans, other Africans and Indians, i am sending this letter to you, hoping that these informations of my case will help you to investigate to retrieve the money, punish the culprits, so that other innocent people are not affected, by these fraudulent online traders, claiming as World - Class Platform, for Online trading, in a very attractive terms, looking more than real, in every aspect. And I am told, they attract thousands of innocent investors, who are interested in Online Trading, especially retired people and house-wives, who think it as convenient to do some income earning business, sitting at home.\r\nThanking you,\r\nYours sincerely,\r\nM. PONNUSAMY              \r\nChennai  India\r\nMobile: +91 9566020733', '[\"Uploads\\/1763263807_Icici.Cyb.pdf\"]', 'Pending', '2025-11-16 03:30:07', '2025-11-17 06:31:01', '30/6/2025 and 1/7/2025', 'from my India, Chennai Anna Nagar east branch of ICICI to VIN/,BITWAVEINN through Bank of Kigali Ltd, Kigali', 'Online trading financial fraud ', 'patrick.sibomana@bdo-ea.com', '', 'Medium', 'ADM2025001', 'en'),
(22, 'CASE-BOKP-SOY5QT-202511010', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Identifiable', 'M. PONNUSAMY', 'dean.ponnusamy@gmail.com', '+91 9566020733', '', NULL, 'From.                             15/11/2025                                                                         \r\nM. PONNUSAMY    (Male. Age - 67)\r\nS/O. Maruthasalam\r\nIndian National Crime Reporting Ack No: 32907250038788\r\nMobile: +91 9566020733     \r\nBank of Kigali Ltd case No. ABXF89159937  \r\nIndian Aadhar No: 6339 6054 6797\r\nEmail ID: dean.ponnusamy@gmail.com\r\nD – 52, Third Street, Anna Nagar East, Chennai – 600102, India.\r\n\r\nTo,\r\nHon. Great Bankers of Rwanda.\r\n\r\nRespected Sir,\r\n\r\nSub: Online Financial Fraud – Commodity Trading – Total amount = INR. 8,92,226.68 (Rupees. Eight Lakhs ninety two thousand  two twenty six and sixty eight paise)\r\n\r\n ICICI - Anna Nagar East branch, Chennai, India (Complaint number: SR1065808944)\r\nTotal two transactions Payments made through my ICICI Anna Nagar East branch, Chennai, India, Branch Code: 6027.  My Account No. 605701313061.  IFSC Code No. ICIC0006027\r\n\r\n1).  On 30 / 6 / 2025 paid INR. 4,45,857.16. from my ICICI bank A/C No. 605701313061  to  VIN/BITWAVEINN/202506301324/518107033216//TCS RS 0.00.\r\nBank of Kigali Ltd, Kigali RW. \r\n\r\n2). On 1 / 7 / 2025 paid INR. 4,46,369.68 from my ICICI bank A/C No. 605701313061  to VIN/BITWAVEINN/202507012024/518214039762//TCS Rs 0.00.\r\nBank of Kigali Ltd, Kigali RW.\r\n\r\nSince VIN/BITWAVE INN is the main gang member in this international crime, using fake FXonet trading platform and loot millions from innocent Rwandans, other Africans and Indians, i am sending this letter to you, hoping that these informations of my case will help you to investigate to retrieve the money, punish the culprits, so that other innocent people are not affected, by these fraudulent online traders, claiming as World - Class Platform, for Online trading, in a very attractive terms, looking more than real, in every aspect. And I am told, they attract thousands of innocent investors, who are interested in Online Trading, especially retired people and house-wives, who think it as convenient to do some income earning business, sitting at home.\r\nThanking you,\r\nYours sincerely,\r\nM. PONNUSAMY              \r\nChennai  India\r\nMobile: +91 9566020733', '[\"Uploads\\/1763263824_Icici.Cyb.pdf\"]', 'Pending', '2025-11-16 03:30:24', '2025-11-17 06:30:39', '30/6/2025 and 1/7/2025', 'from my India, Chennai Anna Nagar east branch of ICICI to VIN/,BITWAVEINN through Bank of Kigali Ltd, Kigali', 'Online trading financial fraud ', 'patrick.sibomana@bdo-ea.com', '', 'Medium', 'ADM2025001', 'en'),
(23, 'CASE-BOKP-0DZ7SY-202511011', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-21 07:45:50', '2025-11-21 07:45:50', 'Ndashak bank statement and current balance ', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(24, 'CASE-BOKP-HUOKF9-202511012', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-21 07:46:00', '2025-11-21 07:46:00', 'Ndashak bank statement and current balance ', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(25, 'CASE-BOKP-QX2SMW-202511013', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-21 22:23:30', '2025-11-21 22:23:30', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(26, 'CASE-BOKP-L25YFO-202511014', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-21 22:23:39', '2025-11-21 22:23:39', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(27, 'CASE-BOKP-CKBF5R-202511015', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-21 22:23:46', '2025-11-21 22:23:46', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(28, 'CASE-BOKP-RF1KZW-202511016', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-21 22:23:53', '2025-11-21 22:23:53', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(29, 'CASE-BOKP-FQJ2BD-202511017', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-11-21 22:23:59', '2025-11-21 22:23:59', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(30, 'CASE-BOKP-MF0QAZ-202512001', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, 'Uyu muclient yageragerje gusaba inguzanyo muri Banki ya Kigali ishami rya Remera inshuro nyishi yujuje ibyangombwa bisabwa kugirango ahabwe iyo nguzanyo yasabaga ariko ntiyayihabwa mumakuru yaduhaye yavuze ko byasanga nkaho bashaka kumusaba ruswa kugeza ubu ntarahabwa iyo nguzanyo yasabye ahubwo buri uko agiye kuri iyo banki bamusubizayo bamubwira ko agomba kuzagaruka kandi nta mpamvu ahabwa ifatika ituma adahabwa iyo inguzanyo.\r\n\r\nDokima zimufasha gutanga amakuru kugirango ikirego cye gikurikiranwe neza murazisanga kuri email yo gutangiraho amakuru ariyo bk.tangamakuru@bdo-ea.com.', '[]', 'New', '2025-12-08 13:16:15', '2025-12-08 13:16:15', '15/ Ukwakira 2025', 'Banki ya Kigali', 'Banki ya Kigali ishami rya Remera', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(31, 'CASE-BOKP-L4NSFC-202512002', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, 'NDASHAKA KUBAZA KURI COMPTE ZUNGUKA BURI MEZI ATATU.KUBERA IKI UKWEZI KWA 9 NTANYUNGU BASHYIZEHO', '[]', 'New', '2025-12-09 15:55:29', '2025-12-09 15:55:29', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(32, 'CASE-BOKP-41YN7D-202512003', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, 'NDASHAKA KUBAZA KURI COMPTE ZUNGUKA BURI MEZI ATATU.KUBERA IKI UKWEZI KWA 9 NTANYUNGU BASHYIZEHO', '[]', 'New', '2025-12-09 15:55:38', '2025-12-09 15:55:38', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(33, 'CASE-BOKP-91BQNC-202512004', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-12-09 19:01:04', '2025-12-09 19:01:04', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(34, 'CASE-BOKP-GBYI6R-202512005', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-12-10 17:19:57', '2025-12-10 17:19:57', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(35, 'CASE-BOKP-1S4V5X-202512006', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'nukuvugango nafunguye onlinebanking biraranjyira , bampa credential muri message zo kwinjiraho ubundi ngahita nemererwa kuzihindura none ndikujya guhindura password nakanda kuri confirm bikanga kandi password iraba expired  muri 24 hours ubwo urumva  koko account yanjye ntag iri secure kuko nintayihindura muri bunyinjirire muri konti kuko nimwe mwampaye password\r\nkandi izo  credantial ahanu zibitse naho sihizewe ntawamenya harigihe bazibona nintazihindura\r\n\r\nerror:iri muba developer bakoze iyo system kbx mubabwire babikoreho', '[\"Uploads\\/1765637338_Screenshot__1242_.png\"]', 'New', '2025-12-13 14:48:58', '2025-12-13 14:48:58', '13/12/2025', 'Bank of kigali ', 'kicukiro branch', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(36, 'CASE-BOKP-LR8Y2O-202512007', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-12-19 14:18:24', '2025-12-19 14:18:24', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(37, 'CASE-BOKP-TW3QYI-202512008', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-12-22 12:40:56', '2025-12-22 12:40:56', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(38, 'CASE-BOKP-KLCE96-202512009', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gvgh', '[]', 'New', '2025-12-23 14:00:32', '2025-12-23 14:00:32', 'jyugyg', 'ku', 'hf', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(39, 'CASE-BOKP-FSOZH0-202512010', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gvgh', '[]', 'New', '2025-12-23 14:00:51', '2025-12-23 14:00:51', 'jyugyg', 'ku', 'hf', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(40, 'CASE-BOKP-2DU7PQ-202512011', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gvgh', '[]', 'New', '2025-12-23 14:01:06', '2025-12-23 14:01:06', 'jyugyg', 'ku', 'hf', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(41, 'CASE-BOKP-ZUFBI2-202512012', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-12-27 17:55:40', '2025-12-27 17:55:40', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(42, 'CASE-BOKP-RVBNLO-202512013', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2025-12-27 17:55:50', '2025-12-27 17:55:50', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(43, 'CASE-BOKP-6J5CLK-202601001', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-01-05 09:43:41', '2026-01-05 09:43:41', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(44, 'CASE-BOKP-GMNKI4-202601002', '077e3ee0dc6c06f0fbb998b77c547f40', 'Employee', 'Anonymous', '', '', '', '', NULL, 'On 13/12/2025 at Kabarondo Branch, the Branch Operations Manager (BOM), Kabanda Joseph, handed over his system credentials and operational authority to Customer Service Officer Karasira Benoit and then left the branch, in violation of internal controls and segregation of duties.\r\n\r\nOn the same day, Teller Gabriel Ntukanyagwe deposited 100,000 RWF into his own personal staff account, an act constituting serious misconduct and suspected fraud, enabled by the absence of authorized supervision.\r\n\r\nI request an immediate Head Office investigation, including review of system access logs, CCTV footage, and transaction records for 13/12/2025, and I request strict confidentiality and whistleblower protection.\r\n\r\nManzi Elia', '[\"Uploads\\/1767699560_support_document.pdf\"]', 'New', '2026-01-06 11:39:20', '2026-01-06 11:39:20', '13 DECEMBER 2025', 'KABARONDO BRANCH', 'RETAIL DEPARTMENT', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(45, 'CASE-BOKP-WGZ9RO-202601003', '077e3ee0dc6c06f0fbb998b77c547f40', 'Employee', 'Anonymous', '', '', '', '', NULL, 'I would like to add a crucial detail to my previous message regarding the incident at the Kabarondo Branch on 13/12/2025.\r\n\r\nIt has come to my attention that on that day, Branch Operations Manager (BOM) Mr. Kabanda Joseph arrived early, before other staff, with the intent to offload and reload the ATM. During this process, he accessed the back office system and authorized transactions, despite the fact that the back office staff were on sick leave. This effectively allowed him to both input and authorize transactions using the back office credentials, further compounding the breach of internal controls and segregation of duties.\r\n\r\nI hope this additional information provides further clarity for the investigation.\r\n\r\nThank you for your attention to this matter.\r\n\r\nSincerely,\r\nManzi Elia', '[\"Uploads\\/1767718605_SUPPORTING_DOCUMENT_BOM_BACK_OFFICE.pdf\"]', 'New', '2026-01-06 16:56:45', '2026-01-06 16:56:45', '13 December 2025', 'kabarondo branch', 'retail department', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(46, 'CASE-BOKP-G9DKUB-202601004', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-01-09 02:03:54', '2026-01-09 02:03:54', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(47, 'CASE-BOKP-IACD7O-202601005', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-01-24 21:01:42', '2026-01-24 21:01:42', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(48, 'CASE-BOKP-8YQA50-202601006', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-01-24 21:02:37', '2026-01-24 21:02:37', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(49, 'CASE-BOKP-6WR43Q-202601007', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-01-24 21:04:09', '2026-01-24 21:04:09', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(50, 'CASE-BOKP-JCS52X-202601008', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-01-28 16:20:30', '2026-01-28 16:20:30', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(51, 'CASE-BOKP-HBPNRV-202602001', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-02-04 16:07:47', '2026-02-04 16:07:47', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(52, 'CASE-BOKP-CPJBDH-202602002', '077e3ee0dc6c06f0fbb998b77c547f40', 'Employee', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-02-11 13:05:13', '2026-02-11 13:05:13', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(53, 'CASE-BOKP-FVJ2Q7-202602003', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-02-18 07:51:03', '2026-02-18 07:51:03', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(54, 'CASE-BOKP-TINBP1-202602004', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-02-18 07:51:45', '2026-02-18 07:51:45', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(55, 'CASE-BOKP-E0VIFC-202602005', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-02-18 07:51:52', '2026-02-18 07:51:52', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(56, 'CASE-BOKP-MZ93VQ-202602006', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-02-18 07:52:42', '2026-02-18 07:52:42', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(57, 'CASE-BOKP-RAYLVS-202602007', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'Ndabasuhuje nitwa ngirabakunzi damascene \r\nJye nfite ikibazo guhuzu a court yajye ya BK of Kigali kuyihuza na app ya bk byarange Burundu iyoshyizemo amazing bambwirako bidahura nakonti Kandi amazin nkoreshaho no ayanjye pe mufashe mubwire ubundi buryo murakoze kuko sindi mugihugu we ngo najya kwishami rya bk murakoze.', '[]', 'New', '2026-02-20 08:54:32', '2026-02-20 08:54:32', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(58, 'CASE-BOKP-3D0ZQI-202602008', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Identifiable to BDO only', 'Patrice Ntwari', 'patricentwari458@gmail.com', '+4797260245', '', NULL, '', '[]', 'New', '2026-02-22 13:22:24', '2026-02-22 13:22:24', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(59, 'CASE-BOKP-0NW3BV-202602009', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-02-22 17:35:42', '2026-02-22 17:35:42', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(60, 'CASE-BOKP-AZFWB9-202602010', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-02-24 05:18:58', '2026-02-24 05:18:58', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(61, 'CASE-BOKP-KHAD5W-202603001', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-03-01 20:53:23', '2026-03-01 20:53:23', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(62, 'CASE-BOKP-XSCONM-202603002', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-03-01 20:53:33', '2026-03-01 20:53:33', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(63, 'CASE-BOKP-3DA8EF-202603003', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-03-04 09:53:49', '2026-03-04 09:53:49', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(64, 'CASE-BOKP-1EBQ80-202603004', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-03-10 14:52:39', '2026-03-10 14:52:39', '0781703555', 'Phone', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(65, 'CASE-BOKP-IFB6RM-202603005', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Identifiable', 'Henri Tuzagi', 'henrituzagi@hotmail.no', '40944540', '', NULL, 'Non', '[]', 'New', '2026-03-12 16:28:17', '2026-03-12 16:28:17', 'Pas de.incident', 'Non', 'Non', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(66, 'CASE-BOKP-4HJL61-202603006', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-03-12 16:30:04', '2026-03-12 16:30:04', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(67, 'CASE-BOKP-NH3EJC-202603007', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-03-12 16:30:13', '2026-03-12 16:30:13', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(68, 'CASE-BOKP-7ELZ3O-202603008', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Identifiable', 'Sebera francais Xavier ', '', '0783333347', '', NULL, 'Ndashaka kumenya amakuru agendanye na konti yange', '[]', 'New', '2026-03-13 15:17:47', '2026-03-13 15:17:47', 'Kuri 10/03/2026', '', 'Bk ', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(69, 'CASE-BOKP-ENOMSG-202603009', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Identifiable to BDO only', 'Mwitirehe Vincent ', '', '0790226162', '', NULL, 'Ndashaka kubaza impamvu amafaranga yange ya pansion ataragera kuri konti Kandi mbibona muri sms ko ariho najya kuri bank bakayabura bakambwira ngo yarayobye', '[\"Uploads\\/1773415418_Screenshot_20260313_172318_Truecaller.jpg\"]', 'New', '2026-03-13 15:23:38', '2026-03-13 15:23:38', '04/03/2026', 'Kuri telephone ', 'Bk bank of Kigali ', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(70, 'CASE-BOKP-UX43HN-202603010', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Identifiable to BDO only', 'Eric MUNYABUHORO', 'eric1stmunyabuhoro@gmail.com', '0796383753', '', NULL, '', '[]', 'New', '2026-03-15 07:39:59', '2026-03-15 07:39:59', 'kuwa 15werurwe 2026', 'muhanga district', 'kuri mudasobwa igendanwa', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(71, 'CASE-BOKP-IK6GSL-202603011', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-03-19 17:14:44', '2026-03-19 17:14:44', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(72, 'CASE-BOKP-1ZH5N3-202603012', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, 'Hi, there’s someone who’s spoofing messages using your exact bank name and sms channel “BKeBank” to scam people, I got a fake deposit SMS -even the debited account in the screenshot is fake, it doesn’t exist- the thing is, the message came in messages from the your legit SMS channel “BKeBank”, and to my knowledge you don’t get SMS notifications when you receive a deposit which helped me notice the scam and not fall for it. Usually scam SMS come from different channels and numbers, not the exact bank SMS channel. I’m not sure how this was possible, but you can check if someone has compromised your SMS channel or spoofed it, and how?\r\n\r\nNotes: I never shared my personal number that’s linked to my BK account with this person, only BK account number, so he must have some sort of access in your bank to get people’s information using only the BK account number.\r\n\r\nThe person name is: JEAN D\'AMOUR DUSENGIMANA\r\n\r\nTips: \r\nCan you try to check if Event #:\r\nFTCM25256GDHKGRE8 belongs to an actual transaction? (Which he possibly copied from and added to the spoofed text?)\r\n\r\nI can’t reveal my identity currently, but thanks for whistleblowing service!\r\n', '[\"Uploads\\/1773943599_IMG_3744.jpeg\"]', 'New', '2026-03-19 18:06:39', '2026-03-19 18:06:39', '15, jun, 2025 - Sunday ', 'Rwanda', 'I’m just a client, your bank services are amazing, but what happened shocked me, that’s why I’m reporting this. ', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(73, 'CASE-BOKP-NQL4MC-202603013', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-03-20 13:10:11', '2026-03-20 13:10:11', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(74, 'CASE-BOKP-UCE4Q3-202603014', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-03-26 13:19:30', '2026-03-26 13:19:30', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(75, 'CASE-BOKP-63L4F9-202603015', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, 'Amafaranga ari kuri BK MTN iyatwara nta burenganzira nabahaye', '[]', 'New', '2026-03-26 15:25:07', '2026-03-26 15:25:07', 'Vuba', 'Muri System', 'Mu Rwanda', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(76, 'CASE-BOKP-708BAV-202604001', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-02 10:18:50', '2026-04-02 10:18:50', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(77, 'CASE-BOKP-FOSG6X-202604002', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-02 13:35:23', '2026-04-02 13:35:23', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(78, 'CASE-BOKP-XGKSH2-202604003', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:44:17', '2026-04-05 08:44:17', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(79, 'CASE-BOKP-DOS04J-202604004', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:44:28', '2026-04-05 08:44:28', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(80, 'CASE-BOKP-WU8LT4-202604005', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:44:36', '2026-04-05 08:44:36', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(81, 'CASE-BOKP-YKZ6IA-202604006', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:44:45', '2026-04-05 08:44:45', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(82, 'CASE-BOKP-34JHEO-202604007', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:44:52', '2026-04-05 08:44:52', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(83, 'CASE-BOKP-3BX1QD-202604008', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:44:59', '2026-04-05 08:44:59', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(84, 'CASE-BOKP-LG0EUZ-202604009', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:45:07', '2026-04-05 08:45:07', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(85, 'CASE-BOKP-CN5GQJ-202604010', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:45:14', '2026-04-05 08:45:14', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(86, 'CASE-BOKP-CHQGZU-202604011', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:45:22', '2026-04-05 08:45:22', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(87, 'CASE-BOKP-ECD83P-202604012', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:45:30', '2026-04-05 08:45:30', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(88, 'CASE-BOKP-AW6MLZ-202604013', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:45:36', '2026-04-05 08:45:36', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(89, 'CASE-BOKP-6ITOVH-202604014', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:45:45', '2026-04-05 08:45:45', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(90, 'CASE-BOKP-SIZ57W-202604015', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:45:54', '2026-04-05 08:45:54', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(91, 'CASE-BOKP-ZN74GD-202604016', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:46:05', '2026-04-05 08:46:05', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(92, 'CASE-BOKP-ATJWDB-202604017', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-05 08:46:14', '2026-04-05 08:46:14', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(93, 'CASE-BOKP-I4H7VE-202604018', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-06 03:42:48', '2026-04-06 03:42:48', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(94, 'CASE-BOKP-JI1SR4-202604019', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Identifiable', 'Gisa kazoya', 'gisakazoya6@gmail.com', '0788710347', '', NULL, 'Pfite card ya Bk Arena ya prepaid \r\n\r\nAriko abantu bayikoresha kuma web bishyura online, ndashak ko mubifunga ibya online nkajya nyikoresha ku machine Guse nkakoresha password ', '[]', 'New', '2026-04-07 06:12:11', '2026-04-07 06:12:11', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(95, 'CASE-BOKP-Z8HVU1-202604020', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Identifiable', 'Gisa kazoya', 'gisakazoya6@gmail.com', '0788710347', '', NULL, 'Pfite card ya Bk Arena ya prepaid \r\n\r\nAriko abantu bayikoresha kuma web bishyura online, ndashak ko mubifunga ibya online nkajya nyikoresha ku machine Guse nkakoresha password ', '[]', 'New', '2026-04-07 06:12:25', '2026-04-07 06:12:25', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(96, 'CASE-BOKP-93HJR1-202604021', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Identifiable', 'Gisa kazoya', 'gisakazoya6@gmail.com', '0788710347', '', NULL, 'Pfite card ya Bk Arena ya prepaid \r\n\r\nAriko abantu bayikoresha kuma web bishyura online, ndashak ko mubifunga ibya online nkajya nyikoresha ku machine Guse nkakoresha password ', '[]', 'New', '2026-04-07 06:12:39', '2026-04-07 06:12:39', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(97, 'CASE-BOKP-0XAGHI-202604022', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Identifiable', 'Gisa kazoya', 'gisakazoya6@gmail.com', '0788710347', '', NULL, 'Pfite card ya Bk Arena ya prepaid \r\n\r\nAriko abantu bayikoresha kuma web bishyura online, ndashak ko mubifunga ibya online nkajya nyikoresha ku machine Guse nkakoresha password ', '[]', 'New', '2026-04-07 06:12:52', '2026-04-07 06:12:52', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(98, 'CASE-BOKP-LFH5OJ-202604023', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-08 19:49:35', '2026-04-08 19:49:35', '5/3/2009', 'Muhanga', 'Bdg', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(99, 'CASE-BOKP-LHE6GA-202604024', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-08 19:49:44', '2026-04-08 19:49:44', '5/3/2009', 'Muhanga', 'Bdg', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(100, 'CASE-BOKP-MLBIJO-202604025', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-08 19:49:51', '2026-04-08 19:49:51', '5/3/2009', 'Muhanga', 'Bdg', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(101, 'CASE-BOKP-AW2YTC-202604026', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-08 19:49:58', '2026-04-08 19:49:58', '5/3/2009', 'Muhanga', 'Bdg', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(102, 'CASE-BOKP-0U6VKR-202604027', '077e3ee0dc6c06f0fbb998b77c547f40', 'Supplier', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-09 07:40:39', '2026-04-09 07:40:39', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(103, 'CASE-BOKP-1UW9AX-202604028', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-12 15:12:56', '2026-04-12 15:12:56', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(104, 'CASE-BOKP-Q7BR5D-202604029', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-13 15:41:33', '2026-04-13 15:41:33', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(105, 'CASE-BOKP-51YTN9-202604030', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-14 06:36:50', '2026-04-14 06:36:50', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(106, 'CASE-BOKP-W8RZAT-202604031', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Identifiable to BDO only', 'Julius', 'juliuskarenzi@gmail.com', '0788406767', '', NULL, 'advjanabjovnojSIOD VOID', '[]', 'Closed', '2026-04-15 11:37:55', '2026-04-20 13:00:28', 'dfnldklkldklmdkmlkclmkcmlkcmlk', 'fskbvfkmvfklmldlk;d', 'dakjcbadjbvj;SBVJ;sjk', 'arlette.umwari@bdo-ea.com', 'Empty information, not relavant to Whistleblowing services ', 'Low', 'ADM2025005', 'en'),
(107, 'CASE-BOKP-A25PHX-202604032', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Identifiable', 'Ishimwee Didier gisubizo ', '', '0724358046', '', NULL, '', '[]', 'New', '2026-04-20 19:49:36', '2026-04-20 19:49:36', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(108, 'CASE-BOKP-LQFMXW-202604033', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'I lost my login credentials ', '[\"Uploads\\/1776715149_Screenshot_20260420-215717.png\"]', 'New', '2026-04-20 19:59:09', '2026-04-20 19:59:09', 'Now', 'During login on mobile app', 'Person account', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en'),
(109, 'CASE-BOKP-KGCYD2-202604034', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-21 07:42:04', '2026-04-21 07:42:04', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(110, 'CASE-BOKP-C6I0RM-202604035', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, 'Nibagiwekontendumwishingizi\r\n', '[]', 'New', '2026-04-22 22:59:11', '2026-04-22 22:59:11', 'Uyumutsi', '', 'Remerabranch', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(111, 'CASE-BOKP-UWQYMB-202604036', '077e3ee0dc6c06f0fbb998b77c547f40', 'Client', 'Anonymous', '', '', '', '', NULL, 'Nibagiwekontendumwishingizi\r\n', '[]', 'New', '2026-04-22 22:59:21', '2026-04-22 22:59:21', 'Uyumutsi', '', 'Remerabranch', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'rw'),
(112, 'CASE-BOKP-RW3YA7-202604037', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-26 00:55:53', '2026-04-26 00:55:53', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(113, 'CASE-BOKP-OZLXYN-202604038', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-26 00:56:07', '2026-04-26 00:56:07', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(114, 'CASE-BOKP-6I4D7Q-202604039', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-26 00:56:15', '2026-04-26 00:56:15', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(115, 'CASE-BOKP-IME207-202604040', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:57:45', '2026-04-26 00:57:45', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(116, 'CASE-BOKP-GEVBFC-202604041', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:57:54', '2026-04-26 00:57:54', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(117, 'CASE-BOKP-GT568D-202604042', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:58:00', '2026-04-26 00:58:00', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(118, 'CASE-BOKP-B3ZG18-202604043', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:58:08', '2026-04-26 00:58:08', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(119, 'CASE-BOKP-Y9RQXB-202604044', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:58:18', '2026-04-26 00:58:18', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(120, 'CASE-BOKP-HAK6OI-202604045', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:58:26', '2026-04-26 00:58:26', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(121, 'CASE-BOKP-BDSG31-202604046', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:58:33', '2026-04-26 00:58:33', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(122, 'CASE-BOKP-5LFEY6-202604047', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:58:42', '2026-04-26 00:58:42', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(123, 'CASE-BOKP-0ECFKH-202604048', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:58:50', '2026-04-26 00:58:50', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(124, 'CASE-BOKP-SJ8YLK-202604049', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:58:58', '2026-04-26 00:58:58', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(125, 'CASE-BOKP-09K8UT-202604050', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:59:05', '2026-04-26 00:59:05', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(126, 'CASE-BOKP-5146MY-202604051', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, 'gradyboylusongo@gmail.com', '[]', 'New', '2026-04-26 00:59:13', '2026-04-26 00:59:13', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'gradyboylusongo@gmail.com', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'fr'),
(127, 'CASE-BOKP-N5IAXJ-202604052', '077e3ee0dc6c06f0fbb998b77c547f40', 'Other', 'Anonymous', '', '', '', '', NULL, '', '[]', 'New', '2026-04-27 08:08:00', '2026-04-27 08:08:00', '', '', '', 'N/A', NULL, 'Low', 'alert.rw@bdo-ea.com', 'en');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `Telephone` varchar(256) NOT NULL,
  `Description` varchar(256) NOT NULL,
  `BDO_contact` varchar(256) NOT NULL,
  `Client_Contact` varchar(256) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `token`, `Telephone`, `Description`, `BDO_contact`, `Client_Contact`, `created_at`) VALUES
(2, 'Bank of Kigali Plc', 'alert.rw@bdo-ea.com', '077e3ee0dc6c06f0fbb998b77c547f40', '0787282621', 'Bank of Kigali (BK) is a commercial bank in Rwanda. It is licensed by the National Bank of Rwanda. Bank of Kigali. Company type, Public.', '0', '0', '2025-07-29 07:44:26'),
(3, 'BDO EAST AFRICA RWANDA', 'habregos17@bdo-ea.com', 'be438c96a4a269efc413fd228655905f', '+! 234 7864 3 3 3', 'BDO EAST AFRICA', '0', '0', '2025-07-29 13:20:13'),
(4, 'Fred Hollows Foundation', 'francois.habuwiteka@bdo-ea.com', 'c3a8e325b91917d0bb11f965d52084b3', '0788549191', 'Fred Hollows Foundation is the key to all assignments', '0', '0', '2025-07-30 06:33:00'),
(5, 'BK GROUP PLC', 'alert.rw@bdo-ea.com', '67f0c19801a4378e5987347e5cb04f41', '0788000034', 'YES', '0', '0', '2025-08-05 12:50:34'),
(6, 'Access to Finance Rwanda', 'clement.niyitegeka@bdo-ea.com', '1248da4eb3798aa0752cfc8ad8500466', '0788000011', 'AFR', '0', '0', '2025-11-12 16:28:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_client_assignments`
--

CREATE TABLE `user_client_assignments` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `client_token` varchar(255) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_client_assignments`
--

INSERT INTO `user_client_assignments` (`id`, `user_id`, `client_token`, `assigned_at`) VALUES
(5, 'ADM2025001', 'be438c96a4a269efc413fd228655905f', '2025-07-30 07:32:43'),
(14, 'ADM2025003', '077e3ee0dc6c06f0fbb998b77c547f40', '2025-11-12 08:54:54'),
(15, 'ADM2025004', '077e3ee0dc6c06f0fbb998b77c547f40', '2025-11-12 08:56:03'),
(16, 'ADM2025005', '1248da4eb3798aa0752cfc8ad8500466', '2025-11-12 16:56:38'),
(17, 'ADM2025005', '077e3ee0dc6c06f0fbb998b77c547f40', '2026-03-13 08:41:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `casenumber` (`casenumber`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `user_client_assignments`
--
ALTER TABLE `user_client_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`client_token`),
  ADD KEY `client_token` (`client_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_client_assignments`
--
ALTER TABLE `user_client_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_client_assignments`
--
ALTER TABLE `user_client_assignments`
  ADD CONSTRAINT `user_client_assignments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_client_assignments_ibfk_2` FOREIGN KEY (`client_token`) REFERENCES `clients` (`token`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
