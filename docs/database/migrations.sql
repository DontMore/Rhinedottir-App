-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi server:                 8.4.3 - MySQL Community Server - GPL
-- OS Server:                    Win64
-- HeidiSQL Versi:               12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Membuang struktur basisdata untuk rhinedottir
CREATE DATABASE IF NOT EXISTS `rhinedottir` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `rhinedottir`;

-- membuang struktur untuk table rhinedottir.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Membuang data untuk tabel rhinedottir.migrations: ~44 rows (lebih kurang)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2026_09_01_094525_create_msds_documents_table', 1),
	(2, '2026_09_06_103224_add_trainers_to_msds_documents_table', 2),
	(3, '2014_10_12_000000_create_users_table', 3),
	(4, '2014_10_12_100000_create_password_reset_tokens_table', 4),
	(5, '2018_08_08_100000_create_telescope_entries_table', 5),
	(6, '2019_08_19_000000_create_failed_jobs_table', 6),
	(7, '2019_12_14_000001_create_personal_access_tokens_table', 7),
	(8, '2023_06_07_000001_create_pulse_tables', 8),
	(9, '2024_01_08_061555_create_logbook_reagens_table', 9),
	(10, '2024_01_09_073729_create_orders_table', 10),
	(11, '2023_12_11_131615_reagens', 11),
	(12, '2024_01_10_143854_reagen_in', 12),
	(13, '2024_01_10_144143_create_stock_reagens_table', 13),
	(14, '2024_01_10_create_email_settings_table', 14),
	(15, '2024_01_11_103859_create_stock_histories_table ', 14),
	(16, '2024_01_11_103859_create_stock_histories_table', 14),
	(17, '2024_01_12_133957_create_stock_opnames_table', 14),
	(18, '2024_01_15_add_user_id_to_reagens_in', 14),
	(19, '2024_01_17_000001_add_storage_to_reagens_table', 15),
	(20, '2024_01_17_000002_add_order_level_to_reagens_table', 15),
	(21, '2024_02_08_214501_modify_logbook_reagens', 15),
	(22, '2025_09_13_125728_add_guid_to_users_table', 16),
	(23, '2025_09_13_125732_add_guid_to_all_tables', 16),
	(24, '2025_09_13_125759_update_foreign_keys_to_guid', 16),
	(25, '2026_04_11_100629_add_is_active_to_users_table', 16),
	(26, '2026_05_02_143222_create_audits_table', 16),
	(27, '2026_05_05_160000_create_api_settings_table', 16),
	(28, '2026_05_05_163200_add_api_token_to_api_settings', 16),
	(29, '2026_05_06_222400_add_push_settings_to_api_settings', 16),
	(30, '2026_05_06_224300_add_buffer_stock_to_reagens', 16),
	(31, '2026_05_09_010359_add_selected_tables_to_api_settings_table', 16),
	(32, '2026_05_10_003341_create_reagen_groups_table', 16),
	(33, '2026_05_10_003344_create_reagen_categories_table', 16),
	(34, '2026_05_10_003351_add_group_category_to_reagens_table', 16),
	(35, '2026_05_10_013120_create_order_recommendations_table', 16),
	(36, '2026_05_10_224856_create_storage_locations_table', 16),
	(37, '2026_05_10_225518_add_storage_location_to_reagens_table', 16),
	(38, '2026_05_10_235034_add_reagent_form_to_reagens_table', 16),
	(39, '2026_08_27_000001_add_coa_to_reagens_in_table', 16),
	(40, '2026_09_06_104530_change_uploaded_by_to_uuid_in_msds_documents_table', 17),
	(41, '2026_09_06_115428_fix_uploaded_by_column_type_in_msds_documents_table', 17),
	(42, '2026_09_06_115859_create_msds_documents_table', 17),
	(43, '2026_09_11_092522_create_reagen_msds_table', 18),
	(44, '2026_09_11_102932_add_is_active_to_reagens_table', 18);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
