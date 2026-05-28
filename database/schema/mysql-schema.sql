/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `al_exam_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `al_exam_imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint unsigned NOT NULL,
  `scope` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'province',
  `division_id` bigint unsigned DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_rows` int unsigned NOT NULL DEFAULT '0',
  `matched_rows` int unsigned NOT NULL DEFAULT '0',
  `unmatched_rows` int unsigned NOT NULL DEFAULT '0',
  `imported_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `al_imports_unique` (`year`,`scope`,`division_id`),
  KEY `al_exam_imports_division_id_foreign` (`division_id`),
  CONSTRAINT `al_exam_imports_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `al_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `al_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `import_id` bigint unsigned NOT NULL,
  `year` smallint unsigned NOT NULL,
  `census_no` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` bigint unsigned DEFAULT NULL,
  `division_id` bigint unsigned DEFAULT NULL,
  `gender` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stream` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_1_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_1_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_1_medium` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_2_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_2_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_2_medium` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_3_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_3_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_3_medium` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passes_a` tinyint NOT NULL DEFAULT '0',
  `passes_b` tinyint NOT NULL DEFAULT '0',
  `passes_c` tinyint NOT NULL DEFAULT '0',
  `passes_s` tinyint NOT NULL DEFAULT '0',
  `total_subjects` tinyint NOT NULL DEFAULT '0',
  `is_qualified` tinyint(1) NOT NULL DEFAULT '0',
  `cgt_marks` tinyint DEFAULT NULL,
  `gen_english_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_rank` int DEFAULT NULL,
  `island_rank` int DEFAULT NULL,
  `z_score` decimal(5,4) DEFAULT NULL,
  `attempt` tinyint NOT NULL DEFAULT '1',
  `school_matched` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `al_results_import_id_foreign` (`import_id`),
  KEY `al_results_school_id_foreign` (`school_id`),
  KEY `al_results_division_id_foreign` (`division_id`),
  KEY `al_results_year_division_id_stream_is_qualified_index` (`year`,`division_id`,`stream`,`is_qualified`),
  KEY `al_results_year_school_id_index` (`year`,`school_id`),
  KEY `al_results_year_stream_index` (`year`,`stream`),
  KEY `al_results_z_score_index` (`z_score`),
  CONSTRAINT `al_results_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `al_results_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `al_exam_imports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `al_results_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `al_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `al_subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `al_subjects_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `module` enum('school_info','student_stats','physical_resources','resource_programs','quality_circle','staff','news','notice','download','user_management','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` enum('created','updated','deleted','submitted','approved','rejected','uploaded','downloaded') COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` bigint unsigned DEFAULT NULL,
  `record_id` bigint unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_index` (`user_id`),
  KEY `audit_logs_module_index` (`module`),
  KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_school_id_index` (`school_id`),
  KEY `audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `audit_logs_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL,
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('new','assigned','replied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `assigned_to` bigint unsigned DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_messages_assigned_to_foreign` (`assigned_to`),
  CONSTRAINT `contact_messages_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `districts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `province_id` bigint unsigned NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `districts_province_id_foreign` (`province_id`),
  CONSTRAINT `districts_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `division_isa_schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `division_isa_schools` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `isa_id` bigint unsigned NOT NULL,
  `school_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `division_isa_schools_isa_id_school_id_unique` (`isa_id`,`school_id`),
  KEY `division_isa_schools_school_id_foreign` (`school_id`),
  CONSTRAINT `division_isa_schools_isa_id_foreign` FOREIGN KEY (`isa_id`) REFERENCES `division_isas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `division_isa_schools_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `division_isas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `division_isas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `division_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_area` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `division_isas_division_id_foreign` (`division_id`),
  CONSTRAINT `division_isas_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `division_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `division_staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `division_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `division_staff_division_id_foreign` (`division_id`),
  CONSTRAINT `division_staff_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `divisions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `director_id` bigint unsigned DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_map_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `acting_director_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `divisions_director_id_foreign` (`director_id`),
  KEY `divisions_acting_director_id_foreign` (`acting_director_id`),
  CONSTRAINT `divisions_acting_director_id_foreign` FOREIGN KEY (`acting_director_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `divisions_director_id_foreign` FOREIGN KEY (`director_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `downloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `downloads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title_si` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drive_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` year DEFAULT NULL,
  `office_section_id` bigint unsigned DEFAULT NULL,
  `download_count` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `downloads_office_section_id_foreign` (`office_section_id`),
  CONSTRAINT `downloads_office_section_id_foreign` FOREIGN KEY (`office_section_id`) REFERENCES `office_sections` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `essential_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `essential_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_si` text COLLATE utf8mb4_unicode_ci,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `grade5_exam_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grade5_exam_imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint unsigned NOT NULL,
  `scope` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'province',
  `division_id` bigint unsigned DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_rows` int unsigned NOT NULL DEFAULT '0',
  `imported` int unsigned NOT NULL DEFAULT '0',
  `skipped` int unsigned NOT NULL DEFAULT '0',
  `unmatched` int unsigned NOT NULL DEFAULT '0',
  `imported_by` bigint unsigned DEFAULT NULL,
  `imported_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_grade5_import_scope` (`year`,`scope`,`division_id`),
  KEY `grade5_exam_imports_division_id_foreign` (`division_id`),
  KEY `grade5_exam_imports_imported_by_foreign` (`imported_by`),
  KEY `grade5_exam_imports_year_index` (`year`),
  CONSTRAINT `grade5_exam_imports_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `grade5_exam_imports_imported_by_foreign` FOREIGN KEY (`imported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `grade5_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grade5_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `import_id` bigint unsigned NOT NULL,
  `year` smallint unsigned NOT NULL,
  `school_id` bigint unsigned DEFAULT NULL,
  `census_no` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `schid` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `division_id` bigint unsigned DEFAULT NULL,
  `medium` enum('sinhala','tamil','english') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sinhala',
  `sex` tinyint NOT NULL DEFAULT '0',
  `income` enum('above','below') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'below',
  `total_marks` smallint unsigned NOT NULL DEFAULT '0',
  `is_qualified` tinyint(1) NOT NULL DEFAULT '0',
  `school_matched` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grade5_results_import_id_foreign` (`import_id`),
  KEY `grade5_results_year_index` (`year`),
  KEY `grade5_results_school_id_index` (`school_id`),
  KEY `grade5_results_census_no_index` (`census_no`),
  KEY `grade5_results_division_id_index` (`division_id`),
  KEY `grade5_results_medium_index` (`medium`),
  KEY `grade5_results_sex_index` (`sex`),
  KEY `grade5_results_income_index` (`income`),
  KEY `grade5_results_is_qualified_index` (`is_qualified`),
  KEY `grade5_results_year_division_id_index` (`year`,`division_id`),
  KEY `grade5_results_year_school_id_index` (`year`,`school_id`),
  CONSTRAINT `grade5_results_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `grade5_results_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `grade5_exam_imports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grade5_results_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lookup_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lookup_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_en` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_si` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lookup_values_category_value_unique` (`category`,`value`),
  KEY `lookup_values_category_index` (`category`),
  KEY `lookup_values_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` bigint unsigned NOT NULL,
  `label_si` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_items_menu_id_foreign` (`menu_id`),
  KEY `menu_items_parent_id_foreign` (`parent_id`),
  CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `menu_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menus_location_unique` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mutual_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mutual_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `current_school_id` bigint unsigned NOT NULL,
  `preferred_division_id` bigint unsigned DEFAULT NULL,
  `preferred_subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes_en` text COLLATE utf8mb4_unicode_ci,
  `notes_si` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mutual_transfers_user_id_foreign` (`user_id`),
  KEY `mutual_transfers_current_school_id_foreign` (`current_school_id`),
  KEY `mutual_transfers_preferred_division_id_foreign` (`preferred_division_id`),
  CONSTRAINT `mutual_transfers_current_school_id_foreign` FOREIGN KEY (`current_school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mutual_transfers_preferred_division_id_foreign` FOREIGN KEY (`preferred_division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `mutual_transfers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title_si` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body_si` longtext COLLATE utf8mb4_unicode_ci,
  `body_en` longtext COLLATE utf8mb4_unicode_ci,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','review','approved','rejected','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `submitted_by` bigint unsigned DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `news_slug_unique` (`slug`),
  KEY `news_submitted_by_foreign` (`submitted_by`),
  KEY `news_reviewed_by_foreign` (`reviewed_by`),
  KEY `news_approved_by_foreign` (`approved_by`),
  CONSTRAINT `news_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `news_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `news_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title_si` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_si` longtext COLLATE utf8mb4_unicode_ci,
  `body_en` longtext COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `published_at` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `office_section_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `office_section_staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `office_section_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `office_section_staff_office_section_id_foreign` (`office_section_id`),
  CONSTRAINT `office_section_staff_office_section_id_foreign` FOREIGN KEY (`office_section_id`) REFERENCES `office_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `office_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `office_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_si` text COLLATE utf8mb4_unicode_ci,
  `head_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ol_exam_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ol_exam_imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint unsigned NOT NULL,
  `scope` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'province',
  `division_id` bigint unsigned DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_rows` int unsigned NOT NULL DEFAULT '0',
  `matched_rows` int unsigned NOT NULL DEFAULT '0',
  `unmatched_rows` int unsigned NOT NULL DEFAULT '0',
  `imported_by` bigint unsigned DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ol_imports_unique_scope` (`year`,`scope`,`division_id`),
  KEY `ol_exam_imports_division_id_foreign` (`division_id`),
  KEY `ol_exam_imports_imported_by_foreign` (`imported_by`),
  CONSTRAINT `ol_exam_imports_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ol_exam_imports_imported_by_foreign` FOREIGN KEY (`imported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ol_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ol_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `import_id` bigint unsigned NOT NULL,
  `school_id` bigint unsigned DEFAULT NULL,
  `census_no` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exam_school_id` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attempt_no` tinyint unsigned NOT NULL DEFAULT '1',
  `gender` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `medium` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `division_id` bigint unsigned DEFAULT NULL,
  `subj1_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj1_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj2_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj2_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj3_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj4_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj4_medium` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj5_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj5_medium` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj6_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj6_medium` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj7_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj7_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj7_medium` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj8_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj8_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj8_medium` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj9_code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj9_grade` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj9_medium` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_a_count` tinyint unsigned NOT NULL DEFAULT '0',
  `grade_b_count` tinyint unsigned NOT NULL DEFAULT '0',
  `grade_c_count` tinyint unsigned NOT NULL DEFAULT '0',
  `grade_s_count` tinyint unsigned NOT NULL DEFAULT '0',
  `grade_w_count` tinyint unsigned NOT NULL DEFAULT '0',
  `subjects_sat_count` tinyint unsigned NOT NULL DEFAULT '0',
  `school_matched` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ol_results_school_id_foreign` (`school_id`),
  KEY `ol_results_import_id_school_id_index` (`import_id`,`school_id`),
  KEY `ol_results_import_id_medium_index` (`import_id`,`medium`),
  KEY `ol_results_import_id_gender_index` (`import_id`,`gender`),
  KEY `ol_results_division_id_index` (`division_id`),
  KEY `ol_results_census_no_index` (`census_no`),
  CONSTRAINT `ol_results_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ol_results_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `ol_exam_imports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ol_results_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ol_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ol_subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_group` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_mother_language` tinyint(1) NOT NULL DEFAULT '0',
  `is_mathematics` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ol_subjects_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profile_change_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile_change_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `requested_fields` json NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewer_notes` text COLLATE utf8mb4_unicode_ci,
  `reviewer_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `reference_no` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_change_requests_reviewed_by_foreign` (`reviewed_by`),
  KEY `profile_change_requests_teacher_id_index` (`teacher_id`),
  KEY `profile_change_requests_status_index` (`status`),
  CONSTRAINT `profile_change_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `profile_change_requests_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `programmes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `programmes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title_si` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_si` longtext COLLATE utf8mb4_unicode_ci,
  `description_en` longtext COLLATE utf8mb4_unicode_ci,
  `youtube_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flier_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_artwork` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('draft','review','approved','rejected','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `submitted_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `programmes_submitted_by_foreign` (`submitted_by`),
  KEY `programmes_approved_by_foreign` (`approved_by`),
  CONSTRAINT `programmes_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `programmes_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `provinces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provinces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `qualifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qualifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('educational','professional') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'educational',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `quality_circle_criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quality_circle_criteria` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order` tinyint unsigned NOT NULL,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `quality_circle_marks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quality_circle_marks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `record_id` bigint unsigned NOT NULL,
  `criteria_id` bigint unsigned NOT NULL,
  `indicators_assessed` smallint unsigned NOT NULL DEFAULT '0',
  `maximum_marks` smallint unsigned NOT NULL DEFAULT '0',
  `obtained_marks` smallint unsigned NOT NULL DEFAULT '0',
  `percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quality_circle_marks_record_id_criteria_id_unique` (`record_id`,`criteria_id`),
  KEY `quality_circle_marks_criteria_id_foreign` (`criteria_id`),
  CONSTRAINT `quality_circle_marks_criteria_id_foreign` FOREIGN KEY (`criteria_id`) REFERENCES `quality_circle_criteria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quality_circle_marks_record_id_foreign` FOREIGN KEY (`record_id`) REFERENCES `quality_circle_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `quality_circle_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quality_circle_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `academic_year` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inspection_date` date NOT NULL,
  `inspected_by` bigint unsigned DEFAULT NULL,
  `inspector_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspector_designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','submitted','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `final_index` decimal(5,2) DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_note` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quality_circle_records_inspected_by_foreign` (`inspected_by`),
  KEY `quality_circle_records_approved_by_foreign` (`approved_by`),
  KEY `quality_circle_records_created_by_foreign` (`created_by`),
  KEY `quality_circle_records_school_id_foreign` (`school_id`),
  CONSTRAINT `quality_circle_records_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quality_circle_records_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quality_circle_records_inspected_by_foreign` FOREIGN KEY (`inspected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quality_circle_records_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `school_compliance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_compliance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `stat_deadline_id` bigint unsigned NOT NULL,
  `status` enum('pending','submitted','overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `school_compliance_school_id_foreign` (`school_id`),
  KEY `school_compliance_stat_deadline_id_foreign` (`stat_deadline_id`),
  CONSTRAINT `school_compliance_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `school_compliance_stat_deadline_id_foreign` FOREIGN KEY (`stat_deadline_id`) REFERENCES `stat_deadlines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `school_inspections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_inspections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `submitted_by` bigint unsigned NOT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `academic_year` year NOT NULL,
  `status` enum('draft','submitted','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `school_inspections_school_id_foreign` (`school_id`),
  KEY `school_inspections_submitted_by_foreign` (`submitted_by`),
  KEY `school_inspections_approved_by_foreign` (`approved_by`),
  CONSTRAINT `school_inspections_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `school_inspections_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `school_inspections_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `school_physical_resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_physical_resources` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `classrooms_count` smallint unsigned NOT NULL DEFAULT '0',
  `smart_classrooms_count` smallint unsigned NOT NULL DEFAULT '0',
  `multi_story_buildings` tinyint(1) NOT NULL DEFAULT '0',
  `library` tinyint(1) NOT NULL DEFAULT '0',
  `staff_room` tinyint(1) NOT NULL DEFAULT '0',
  `administrative_block` tinyint(1) NOT NULL DEFAULT '0',
  `hostel` tinyint(1) NOT NULL DEFAULT '0',
  `teachers_quarters` tinyint(1) NOT NULL DEFAULT '0',
  `canteen` tinyint(1) NOT NULL DEFAULT '0',
  `electricity` tinyint(1) NOT NULL DEFAULT '0',
  `water_supply_type` enum('none','well','pipe','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `drinking_water` tinyint(1) NOT NULL DEFAULT '0',
  `toilets_boys` smallint unsigned NOT NULL DEFAULT '0',
  `toilets_girls` smallint unsigned NOT NULL DEFAULT '0',
  `toilets_disabled` smallint unsigned NOT NULL DEFAULT '0',
  `hand_washing` tinyint(1) NOT NULL DEFAULT '0',
  `solar_power` tinyint(1) NOT NULL DEFAULT '0',
  `waste_management` tinyint(1) NOT NULL DEFAULT '0',
  `computer_lab` tinyint(1) NOT NULL DEFAULT '0',
  `computers_count` smallint unsigned NOT NULL DEFAULT '0',
  `laptops_count` smallint unsigned NOT NULL DEFAULT '0',
  `internet_access` tinyint(1) NOT NULL DEFAULT '0',
  `internet_speed` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `internet_type` enum('fiber','copper','gsm') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wifi` tinyint(1) NOT NULL DEFAULT '0',
  `smart_boards_count` smallint unsigned NOT NULL DEFAULT '0',
  `projectors_count` smallint unsigned NOT NULL DEFAULT '0',
  `printers_count` smallint unsigned NOT NULL DEFAULT '0',
  `school_mis` tinyint(1) NOT NULL DEFAULT '0',
  `cctv` tinyint(1) NOT NULL DEFAULT '0',
  `digital_attendance` tinyint(1) NOT NULL DEFAULT '0',
  `science_lab` tinyint(1) NOT NULL DEFAULT '0',
  `home_economics_unit` tinyint(1) NOT NULL DEFAULT '0',
  `music_room` tinyint(1) NOT NULL DEFAULT '0',
  `dancing_room` tinyint(1) NOT NULL DEFAULT '0',
  `playground` tinyint(1) NOT NULL DEFAULT '0',
  `volleyball_court` tinyint(1) NOT NULL DEFAULT '0',
  `netball_court` tinyint(1) NOT NULL DEFAULT '0',
  `athletic_track` tinyint(1) NOT NULL DEFAULT '0',
  `cctv_monitoring` tinyint(1) NOT NULL DEFAULT '0',
  `security_fence` tinyint(1) NOT NULL DEFAULT '0',
  `fire_extinguishers` tinyint(1) NOT NULL DEFAULT '0',
  `emergency_exit_plan` tinyint(1) NOT NULL DEFAULT '0',
  `disaster_preparedness` tinyint(1) NOT NULL DEFAULT '0',
  `student_safety_committee` tinyint(1) NOT NULL DEFAULT '0',
  `annual_budget` decimal(15,2) DEFAULT NULL,
  `sbm_funds` decimal(15,2) DEFAULT NULL,
  `donor_contributions` tinyint(1) NOT NULL DEFAULT '0',
  `ngo_support` tinyint(1) NOT NULL DEFAULT '0',
  `infrastructure_grants` tinyint(1) NOT NULL DEFAULT '0',
  `access_road_condition` enum('good','fair','poor') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_transport_access` tinyint(1) NOT NULL DEFAULT '0',
  `school_van` tinyint(1) NOT NULL DEFAULT '0',
  `disabled_accessibility` tinyint(1) NOT NULL DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `school_physical_resources_updated_by_foreign` (`updated_by`),
  KEY `school_physical_resources_school_id_index` (`school_id`),
  KEY `school_physical_resources_computer_lab_index` (`computer_lab`),
  KEY `school_physical_resources_science_lab_index` (`science_lab`),
  KEY `school_physical_resources_library_index` (`library`),
  KEY `school_physical_resources_internet_access_index` (`internet_access`),
  KEY `school_physical_resources_electricity_index` (`electricity`),
  KEY `school_physical_resources_solar_power_index` (`solar_power`),
  KEY `school_physical_resources_playground_index` (`playground`),
  KEY `school_physical_resources_hostel_index` (`hostel`),
  KEY `school_physical_resources_canteen_index` (`canteen`),
  KEY `school_physical_resources_access_road_condition_index` (`access_road_condition`),
  KEY `school_physical_resources_water_supply_type_index` (`water_supply_type`),
  KEY `school_physical_resources_internet_type_index` (`internet_type`),
  CONSTRAINT `school_physical_resources_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `school_physical_resources_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `school_resource_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_resource_programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `special_education_unit` tinyint(1) NOT NULL DEFAULT '0',
  `counseling_unit` tinyint(1) NOT NULL DEFAULT '0',
  `school_health_unit` tinyint(1) NOT NULL DEFAULT '0',
  `first_aid_room` tinyint(1) NOT NULL DEFAULT '0',
  `midday_meal_program` tinyint(1) NOT NULL DEFAULT '0',
  `dengue_prevention` tinyint(1) NOT NULL DEFAULT '0',
  `scouts` tinyint(1) NOT NULL DEFAULT '0',
  `girl_guides` tinyint(1) NOT NULL DEFAULT '0',
  `cadet_corps` tinyint(1) NOT NULL DEFAULT '0',
  `school_band` tinyint(1) NOT NULL DEFAULT '0',
  `dancing_team` tinyint(1) NOT NULL DEFAULT '0',
  `drama_society` tinyint(1) NOT NULL DEFAULT '0',
  `media_unit` tinyint(1) NOT NULL DEFAULT '0',
  `debate_club` tinyint(1) NOT NULL DEFAULT '0',
  `environmental_society` tinyint(1) NOT NULL DEFAULT '0',
  `it_club` tinyint(1) NOT NULL DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `school_resource_programs_updated_by_foreign` (`updated_by`),
  KEY `school_resource_programs_school_id_index` (`school_id`),
  KEY `school_resource_programs_special_education_unit_index` (`special_education_unit`),
  KEY `school_resource_programs_counseling_unit_index` (`counseling_unit`),
  KEY `school_resource_programs_scouts_index` (`scouts`),
  KEY `school_resource_programs_cadet_corps_index` (`cadet_corps`),
  KEY `school_resource_programs_midday_meal_program_index` (`midday_meal_program`),
  CONSTRAINT `school_resource_programs_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `school_resource_programs_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `school_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nic` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('M','F') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salary_slip_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appointed_date` date DEFAULT NULL,
  `joined_school_date` date DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `non_academic_role` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appointment_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `added_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `school_staff_user_id_foreign` (`user_id`),
  KEY `school_staff_added_by_foreign` (`added_by`),
  KEY `school_staff_school_id_index` (`school_id`),
  KEY `school_staff_is_active_index` (`is_active`),
  KEY `school_staff_non_academic_role_index` (`non_academic_role`),
  CONSTRAINT `school_staff_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `school_staff_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `school_staff_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `school_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_stats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `academic_year` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade_1_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_1_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_2_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_2_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_3_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_3_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_4_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_4_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_5_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_5_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_6_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_6_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_7_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_7_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_8_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_8_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_9_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_9_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_10_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_10_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_11_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_11_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_12_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_12_girls` smallint unsigned NOT NULL DEFAULT '0',
  `grade_13_boys` smallint unsigned NOT NULL DEFAULT '0',
  `grade_13_girls` smallint unsigned NOT NULL DEFAULT '0',
  `disabled_boys` smallint unsigned NOT NULL DEFAULT '0',
  `disabled_girls` smallint unsigned NOT NULL DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_stats_school_id_academic_year_unique` (`school_id`,`academic_year`),
  KEY `school_stats_updated_by_foreign` (`updated_by`),
  KEY `school_stats_school_id_index` (`school_id`),
  KEY `school_stats_academic_year_index` (`academic_year`),
  CONSTRAINT `school_stats_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `school_stats_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schools` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `census_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `division_id` bigint unsigned NOT NULL,
  `type` enum('1AB','1C','2','3') COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_span_from` int DEFAULT NULL,
  `class_span_to` int DEFAULT NULL,
  `established_date` date DEFAULT NULL,
  `divisional_secretariat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grama_niladari_division` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `address_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_id` bigint unsigned DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `medium` enum('sinhala','tamil','english','mixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sinhala',
  `ownership` enum('national','provincial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'provincial',
  `convenience_level` enum('easy','difficult','very_difficult','more_convenient') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `school_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schools_census_no_unique` (`census_no`),
  KEY `schools_division_id_foreign` (`division_id`),
  KEY `schools_principal_id_foreign` (`principal_id`),
  CONSTRAINT `schools_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schools_principal_id_foreign` FOREIGN KEY (`principal_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sliders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sliders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `button_text_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_text_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stat_deadlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stat_deadlines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deadline_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `triggered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stat_snapshots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stat_snapshots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stat_deadline_id` bigint unsigned DEFAULT NULL,
  `total_students` int NOT NULL DEFAULT '0',
  `total_teachers` int NOT NULL DEFAULT '0',
  `total_schools` int NOT NULL DEFAULT '0',
  `total_divisions` int NOT NULL DEFAULT '0',
  `generated_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stat_snapshots_stat_deadline_id_foreign` (`stat_deadline_id`),
  CONSTRAINT `stat_snapshots_stat_deadline_id_foreign` FOREIGN KEY (`stat_deadline_id`) REFERENCES `stat_deadlines` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `teacher_qualifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher_qualifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `qualification_id` bigint unsigned NOT NULL,
  `type` enum('educational','professional') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'educational',
  `year_obtained` year DEFAULT NULL,
  `institution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teacher_qualifications_qualification_id_foreign` (`qualification_id`),
  KEY `teacher_qualifications_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `teacher_qualifications_qualification_id_foreign` FOREIGN KEY (`qualification_id`) REFERENCES `qualifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_qualifications_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `teacher_working_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher_working_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint unsigned NOT NULL,
  `school_id` bigint unsigned DEFAULT NULL,
  `school_name_manual` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_id` bigint unsigned DEFAULT NULL,
  `province_id` bigint unsigned DEFAULT NULL,
  `zonal_office` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_taught` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appointed_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teacher_working_history_school_id_foreign` (`school_id`),
  KEY `teacher_working_history_district_id_foreign` (`district_id`),
  KEY `teacher_working_history_province_id_foreign` (`province_id`),
  KEY `teacher_working_history_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `teacher_working_history_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `teacher_working_history_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE SET NULL,
  CONSTRAINT `teacher_working_history_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL,
  CONSTRAINT `teacher_working_history_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nic` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('M','F') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salary_slip_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `appointed_date` date DEFAULT NULL,
  `joined_school_date` date DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staff_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'teacher',
  `appointment_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_grade` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `added_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teachers_user_id_foreign` (`user_id`),
  KEY `teachers_subject_id_foreign` (`subject_id`),
  KEY `teachers_added_by_foreign` (`added_by`),
  KEY `teachers_school_id_index` (`school_id`),
  KEY `teachers_staff_type_index` (`staff_type`),
  KEY `teachers_is_active_index` (`is_active`),
  KEY `teachers_appointment_type_index` (`appointment_type`),
  CONSTRAINT `teachers_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `teachers_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teachers_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `transfer_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transfer_applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `from_school_id` bigint unsigned NOT NULL,
  `to_school_id` bigint unsigned DEFAULT NULL,
  `status` enum('draft','submitted','principal_review','officer_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `principal_comment` text COLLATE utf8mb4_unicode_ci,
  `officer_comment` text COLLATE utf8mb4_unicode_ci,
  `director_decision` text COLLATE utf8mb4_unicode_ci,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transfer_applications_reference_no_unique` (`reference_no`),
  KEY `transfer_applications_user_id_foreign` (`user_id`),
  KEY `transfer_applications_from_school_id_foreign` (`from_school_id`),
  KEY `transfer_applications_to_school_id_foreign` (`to_school_id`),
  CONSTRAINT `transfer_applications_from_school_id_foreign` FOREIGN KEY (`from_school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transfer_applications_to_school_id_foreign` FOREIGN KEY (`to_school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transfer_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `appointed_date` date DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `division_id` bigint unsigned DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `school_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_nic_unique` (`nic`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `users_subject_id_foreign` (`subject_id`),
  KEY `users_division_id_foreign` (`division_id`),
  KEY `users_school_id_index` (`school_id`),
  CONSTRAINT `users_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `visitor_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitor_counts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `page` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visitor_counts_date_page_unique` (`date`,`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `zonal_offices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `zonal_offices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `district_id` bigint unsigned NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_si` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `zonal_offices_district_id_foreign` (`district_id`),
  CONSTRAINT `zonal_offices_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_05_19_000001_create_essential_links_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_05_11_174317_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_05_12_025044_create_divisions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_05_12_030402_create_schools_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_05_12_031154_add_fields_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_05_12_032101_create_menus_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_05_12_032249_create_sliders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_05_12_032530_create_news_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_05_12_032935_create_notices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_05_12_033240_create_programmes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_05_12_033606_create_downloads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_05_12_033758_create_visitor_counts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_05_14_051935_add_publish_dates_to_notices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_05_14_055936_add_artwork_image_to_programmes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_05_15_134049_add_photo_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_05_15_134109_add_details_to_divisions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_05_15_134121_create_division_staff_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_05_15_134140_create_division_isas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_05_15_134153_create_division_isa_schools_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2026_05_15_154725_add_extra_fields_to_schools_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2026_05_15_165623_create_site_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_05_17_020619_add_slug_to_news_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2026_05_17_144846_add_drive_url_to_downloads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_05_17_151015_make_file_path_nullable_in_downloads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_05_17_155734_create_contact_messages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_05_17_160400_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_05_18_145526_create_stat_deadlines_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_05_18_145528_create_stat_snapshots_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_05_18_145533_create_school_compliance_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_05_18_174528_create_office_sections_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_05_18_174530_create_office_section_staff_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2026_05_18_174535_add_section_id_to_downloads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_05_20_000001_create_al_results_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_05_20_000002_create_ol_results_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_05_20_000003_create_grade5_results_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_05_20_000004_add_remarks_to_exam_imports',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_05_22_000001_create_qualifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2026_05_22_000002_create_provinces_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2026_05_22_000003_create_districts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2026_05_22_000004_create_zonal_offices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2026_05_22_000005_create_subjects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2026_05_22_000006_add_columns_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2026_05_22_000007_create_teacher_working_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2026_05_22_000008_create_teacher_qualifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2026_05_22_000009_create_mutual_transfers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2026_05_22_000010_create_school_inspections_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2026_05_22_000011_create_transfer_applications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2026_05_26_000001_create_quality_circle_criteria_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2026_05_26_000002_create_quality_circle_tables',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2026_05_26_102219_add_school_logo_to_schools_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2026_05_26_000003_add_school_logo_to_schools_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2026_05_26_000004_add_staff_fields_to_users_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2026_05_26_000005_create_school_stats_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2026_05_26_000006_create_school_physical_resources_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2026_05_26_000007_create_school_resource_programs_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2026_05_26_000008_add_deadline_id_to_stat_snapshots_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_05_26_000009_create_audit_logs_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2026_05_28_072606_remove_unique_from_quality_circle_records',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2026_05_28_072607_create_lookup_values_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2026_05_28_072608_create_teachers_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2026_05_28_072609_create_school_staff_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2026_05_28_072610_update_teacher_tables_to_use_teacher_id',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2026_05_28_072611_create_profile_change_requests_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2026_05_28_072612_revert_staff_columns_from_users_table',7);
