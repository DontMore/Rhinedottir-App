-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2026 at 05:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.5.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reagen-app2`
--

-- --------------------------------------------------------

--
-- Table structure for table `reagen_msds`
--

CREATE TABLE `reagen_msds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reagen_guid` char(36) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `version` varchar(255) DEFAULT NULL,
  `revision_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_latest` tinyint(1) NOT NULL DEFAULT 0,
  `organization_guid` char(36) NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reagen_msds`
--
ALTER TABLE `reagen_msds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reagen_msds_reagen_guid_is_latest_index` (`reagen_guid`,`is_latest`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reagen_msds`
--
ALTER TABLE `reagen_msds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reagen_msds`
--
ALTER TABLE `reagen_msds`
  ADD CONSTRAINT `reagen_msds_reagen_guid_foreign` FOREIGN KEY (`reagen_guid`) REFERENCES `reagens` (`guid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
