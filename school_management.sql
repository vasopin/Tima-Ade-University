-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: moon_college
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `school_class_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned NOT NULL,
  `marked_by` bigint(20) unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL DEFAULT 'present',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_student_id_attendance_date_unique` (`student_id`,`attendance_date`),
  KEY `attendances_section_id_foreign` (`section_id`),
  KEY `attendances_marked_by_foreign` (`marked_by`),
  KEY `attendances_school_class_id_attendance_date_index` (`school_class_id`,`attendance_date`),
  CONSTRAINT `attendances_marked_by_foreign` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_school_class_id_foreign` FOREIGN KEY (`school_class_id`) REFERENCES `school_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_subject`
--

DROP TABLE IF EXISTS `class_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_subject` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_class_id` bigint(20) unsigned NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `teacher_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `class_subject_school_class_id_foreign` (`school_class_id`),
  KEY `class_subject_subject_id_foreign` (`subject_id`),
  KEY `class_subject_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `class_subject_school_class_id_foreign` FOREIGN KEY (`school_class_id`) REFERENCES `school_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_subject_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_subject_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_subject`
--

LOCK TABLES `class_subject` WRITE;
/*!40000 ALTER TABLE `class_subject` DISABLE KEYS */;
INSERT INTO `class_subject` VALUES (1,1,7,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(2,1,5,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(3,1,3,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(4,1,4,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(5,1,1,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(6,1,6,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(7,1,2,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(8,2,7,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(9,2,5,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(10,2,3,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(11,2,4,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(12,2,1,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(13,2,6,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(14,2,2,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(15,3,7,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(16,3,5,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(17,3,3,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(18,3,4,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(19,3,1,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(20,3,6,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(21,3,2,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(22,4,7,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(23,4,5,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(24,4,3,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(25,4,4,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(26,4,1,2,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(27,4,6,3,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(28,4,2,2,'2026-08-15 21:42:29','2026-08-15 21:42:29');
/*!40000 ALTER TABLE `class_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_payments`
--

DROP TABLE IF EXISTS `fee_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `fee_structure_id` bigint(20) unsigned NOT NULL,
  `receipt_number` varchar(255) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `late_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','bank_transfer','cheque','online') NOT NULL DEFAULT 'cash',
  `status` enum('paid','partial','pending','overdue') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `received_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fee_payments_receipt_number_unique` (`receipt_number`),
  KEY `fee_payments_fee_structure_id_foreign` (`fee_structure_id`),
  KEY `fee_payments_received_by_foreign` (`received_by`),
  KEY `fee_payments_student_id_status_index` (`student_id`,`status`),
  CONSTRAINT `fee_payments_fee_structure_id_foreign` FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fee_payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fee_payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_payments`
--

LOCK TABLES `fee_payments` WRITE;
/*!40000 ALTER TABLE `fee_payments` DISABLE KEYS */;
INSERT INTO `fee_payments` VALUES (1,1,5,'RCPT-1001',1500.00,0.00,0.00,'2024-09-05','cash','paid',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(2,1,6,'RCPT-1002',100.00,0.00,0.00,'2024-09-05','cash','paid',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(3,1,7,'RCPT-1003',0.00,0.00,0.00,'2026-08-16','cash','pending',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(4,1,8,'RCPT-1004',0.00,0.00,0.00,'2026-08-16','cash','pending',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(5,2,5,'RCPT-1005',1500.00,0.00,0.00,'2024-09-05','cash','paid',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(6,2,6,'RCPT-1006',100.00,0.00,0.00,'2024-09-05','cash','paid',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(7,2,7,'RCPT-1007',0.00,0.00,0.00,'2026-08-16','cash','pending',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(8,2,8,'RCPT-1008',0.00,0.00,0.00,'2026-08-16','cash','pending',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(9,3,5,'RCPT-1009',1500.00,0.00,0.00,'2024-09-05','cash','paid',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(10,3,6,'RCPT-1010',100.00,0.00,0.00,'2024-09-05','cash','paid',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(11,3,7,'RCPT-1011',0.00,0.00,0.00,'2026-08-16','cash','pending',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(12,3,8,'RCPT-1012',0.00,0.00,0.00,'2026-08-16','cash','pending',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(13,4,9,'RCPT-1013',1500.00,0.00,0.00,'2024-09-05','cash','paid',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(14,4,10,'RCPT-1014',100.00,0.00,0.00,'2024-09-05','cash','paid',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(15,4,11,'RCPT-1015',0.00,0.00,0.00,'2026-08-16','cash','pending',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(16,4,12,'RCPT-1016',0.00,0.00,0.00,'2026-08-16','cash','pending',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(17,5,9,'RCPT-1017',1500.00,0.00,0.00,'2024-09-05','cash','paid',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(18,5,10,'RCPT-1018',100.00,0.00,0.00,'2024-09-05','cash','paid',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(19,5,11,'RCPT-1019',0.00,0.00,0.00,'2026-08-16','cash','pending',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(20,5,12,'RCPT-1020',0.00,0.00,0.00,'2026-08-16','cash','pending',NULL,1,'2026-08-15 21:42:29','2026-08-15 21:42:29');
/*!40000 ALTER TABLE `fee_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee_structures`
--

DROP TABLE IF EXISTS `fee_structures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fee_structures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_class_id` bigint(20) unsigned NOT NULL,
  `fee_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `academic_year` varchar(255) NOT NULL,
  `term` varchar(255) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_structures_school_class_id_foreign` (`school_class_id`),
  CONSTRAINT `fee_structures_school_class_id_foreign` FOREIGN KEY (`school_class_id`) REFERENCES `school_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_structures`
--

LOCK TABLES `fee_structures` WRITE;
/*!40000 ALTER TABLE `fee_structures` DISABLE KEYS */;
INSERT INTO `fee_structures` VALUES (1,1,'Tuition Fee',1500.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(2,1,'Library Fee',100.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(3,1,'Transport Fee',300.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(4,1,'Laboratory Fee',200.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(5,2,'Tuition Fee',1500.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(6,2,'Library Fee',100.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(7,2,'Transport Fee',300.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(8,2,'Laboratory Fee',200.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(9,3,'Tuition Fee',1500.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(10,3,'Library Fee',100.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(11,3,'Transport Fee',300.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(12,3,'Laboratory Fee',200.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(13,4,'Tuition Fee',1500.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(14,4,'Library Fee',100.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(15,4,'Transport Fee',300.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(16,4,'Laboratory Fee',200.00,'2024-2025','Term 1','2024-09-30',1,'2026-08-15 21:42:29','2026-08-15 21:42:29');
/*!40000 ALTER TABLE `fee_structures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000001_create_cache_table',1),(2,'0001_01_01_000002_create_jobs_table',1),(3,'2024_01_01_000001_create_roles_table',1),(4,'2024_01_01_000002_create_users_table',1),(5,'2024_01_01_000003_create_classes_sections_table',1),(6,'2024_01_01_000004_create_subjects_table',1),(7,'2024_01_01_000005_create_teachers_table',1),(8,'2024_01_01_000006_create_students_table',1),(9,'2024_01_01_000007_create_attendances_table',1),(10,'2024_01_01_000008_create_fees_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','admin','Full system access','2026-08-15 21:42:24','2026-08-15 21:42:24'),(2,'Teacher','teacher','Manage classes, attendance, and grades','2026-08-15 21:42:24','2026-08-15 21:42:24'),(3,'Student','student','View own profile and grades','2026-08-15 21:42:25','2026-08-15 21:42:25'),(4,'Parent','parent','View child profile and reports','2026-08-15 21:42:25','2026-08-15 21:42:25');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_classes`
--

DROP TABLE IF EXISTS `school_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_classes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `grade_level` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_classes`
--

LOCK TABLES `school_classes` WRITE;
/*!40000 ALTER TABLE `school_classes` DISABLE KEYS */;
INSERT INTO `school_classes` VALUES (1,'Grade 9','9','Ninth Grade',1,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(2,'Grade 10','10','Tenth Grade',1,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(3,'Grade 11','11','Eleventh Grade',1,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(4,'Grade 12','12','Twelfth Grade',1,'2026-08-15 21:42:28','2026-08-15 21:42:28');
/*!40000 ALTER TABLE `school_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_class_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 40,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sections_school_class_id_foreign` (`school_class_id`),
  CONSTRAINT `sections_school_class_id_foreign` FOREIGN KEY (`school_class_id`) REFERENCES `school_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,1,'Section A',40,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(2,1,'Section B',40,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(3,2,'Section A',40,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(4,2,'Section B',40,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(5,3,'Section A',40,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(6,3,'Section B',40,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(7,4,'Section A',40,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(8,4,'Section B',40,'2026-08-15 21:42:28','2026-08-15 21:42:28');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('5ZESJJc9LPJtykuoHrykjkcw1d1KFu6OzUgvN7i7',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Microsoft Windows 10.0.26200; en-US) PowerShell/7.6.5','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQU81SEJQVGRXNWpCa0JZY2l5c2o0UGJ4dmRqWFQ3MndJc2U5Z2NxZCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovL2xvY2FsaG9zdC9Nb29uLUNvbGxlZ2UvcHVibGljIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly9sb2NhbGhvc3QvTW9vbi1Db2xsZWdlL3B1YmxpYy9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1786841414),('GqJyub45vVnutlaESCy9UCTizOoiRS1Y11GFZN4W',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Microsoft Windows 10.0.26200; en-US) PowerShell/7.6.5','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN2pIdEJGWm9DTHBEUkR3d2xlbDdBTVo0U1VLTEtoajdoUm44U1RMbyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovL2xvY2FsaG9zdC9Nb29uLUNvbGxlZ2UvcHVibGljIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly9sb2NhbGhvc3QvTW9vbi1Db2xsZWdlL3B1YmxpYy9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1786841205),('TEKttu37vf5AZ73GG9lqAmmFvM1ExvyPvTzdo7Lx',NULL,'::1','Mozilla/5.0 (Windows NT 10.0; Microsoft Windows 10.0.26200; en-US) PowerShell/7.6.5','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiakp2VWFsUVJaVHdLRGlocHBnMTZ2bGdWWUMyUTUzdjlzdElsVzBuNyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozODoiaHR0cDovL2xvY2FsaG9zdC9Nb29uJTIwQ29sbGVnZS9wdWJsaWMiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo0NDoiaHR0cDovL2xvY2FsaG9zdC9Nb29uJTIwQ29sbGVnZS9wdWJsaWMvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1786841392);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `roll_number` varchar(255) NOT NULL,
  `admission_number` varchar(255) NOT NULL,
  `school_class_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned NOT NULL,
  `admission_date` date NOT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `parent_name` varchar(255) DEFAULT NULL,
  `parent_phone` varchar(255) DEFAULT NULL,
  `parent_email` varchar(255) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `blood_group` varchar(255) DEFAULT NULL,
  `religion` varchar(255) DEFAULT NULL,
  `nationality` varchar(255) NOT NULL DEFAULT 'Not Specified',
  `status` enum('active','inactive','graduated','expelled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_roll_number_unique` (`roll_number`),
  UNIQUE KEY `students_admission_number_unique` (`admission_number`),
  KEY `students_user_id_foreign` (`user_id`),
  KEY `students_school_class_id_foreign` (`school_class_id`),
  KEY `students_section_id_foreign` (`section_id`),
  CONSTRAINT `students_school_class_id_foreign` FOREIGN KEY (`school_class_id`) REFERENCES `school_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,4,'ROLL-001','ADM-2024-001',2,3,'2024-01-15','female','2008-05-12','10 Maple Drive','Robert Thompson','+1-555-3001','rthompson@email.com',NULL,'A+',NULL,'Not Specified','active','2026-08-15 21:42:29','2026-08-15 21:42:29'),(2,5,'ROLL-002','ADM-2024-002',2,3,'2024-01-15','male','2008-11-20','22 Pine Road','Maria Martinez','+1-555-3002','mmartinez@email.com',NULL,'O+',NULL,'Not Specified','active','2026-08-15 21:42:29','2026-08-15 21:42:29'),(3,6,'ROLL-003','ADM-2024-003',2,4,'2024-01-15','female','2008-03-08','55 Cedar Lane','James Davis','+1-555-3003','jdavis@email.com',NULL,'B+',NULL,'Not Specified','active','2026-08-15 21:42:29','2026-08-15 21:42:29'),(4,7,'ROLL-004','ADM-2024-004',3,5,'2023-01-10','male','2007-09-15','77 Birch Boulevard','Susan Lee','+1-555-3004','slee@email.com',NULL,'AB+',NULL,'Not Specified','active','2026-08-15 21:42:29','2026-08-15 21:42:29'),(5,8,'ROLL-005','ADM-2024-005',3,5,'2023-01-10','female','2007-12-25','99 Willow Way','Carlos Garcia','+1-555-3005','cgarcia@email.com',NULL,'O-',NULL,'Not Specified','active','2026-08-15 21:42:29','2026-08-15 21:42:29');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'Mathematics','MATH101','Algebra, Calculus, and Statistics',1,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(2,'Science','SCI101','Physics, Chemistry, Biology',1,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(3,'English Language','ENG101','Grammar, Literature, Composition',1,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(4,'History','HIST101','World and Local History',1,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(5,'Computer Science','CS101','Programming and Digital Literacy',1,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(6,'Physical Education','PE101','Sports and Physical Fitness',1,'2026-08-15 21:42:28','2026-08-15 21:42:28'),(7,'Art','ART101','Fine Arts and Crafts',1,'2026-08-15 21:42:28','2026-08-15 21:42:28');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teachers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `is_class_teacher` tinyint(1) NOT NULL DEFAULT 0,
  `class_teacher_of` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teachers_employee_id_unique` (`employee_id`),
  KEY `teachers_user_id_foreign` (`user_id`),
  KEY `teachers_class_teacher_of_foreign` (`class_teacher_of`),
  CONSTRAINT `teachers_class_teacher_of_foreign` FOREIGN KEY (`class_teacher_of`) REFERENCES `school_classes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (1,2,'EMP-001','Ph.D. in Mathematics','Mathematics & Computer Science','2020-08-15','123 Elm Street, Springfield','female','1985-03-22','+1-555-9001',1,2,'2026-08-15 21:42:29','2026-08-15 21:42:29'),(2,3,'EMP-002','M.Sc. in Science Education','Science & History','2019-01-10','456 Oak Avenue, Riverside','male','1982-07-14','+1-555-9002',1,3,'2026-08-15 21:42:29','2026-08-15 21:42:29');
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'System Administrator','admin@school.com',NULL,'$2y$12$FKhrs8pD3EL2FdT9yEGJNu79krRBkxWIVteIOvLwjezYXa3R6HC9.','+1-555-0100',NULL,1,NULL,'2026-08-15 21:42:25','2026-08-15 21:42:25'),(2,2,'Dr. Sarah Johnson','teacher1@school.com',NULL,'$2y$12$sFsugNLDSfNSDQebq.3FHOux21Ec0.JoPwMO5Ee2w4gpHaBbzXQcC','+1-555-0101',NULL,1,NULL,'2026-08-15 21:42:25','2026-08-15 21:42:25'),(3,2,'Mr. James Wilson','teacher2@school.com',NULL,'$2y$12$ZlRZTEMsX0aqmSHoGG5wwO1r2DAF6VhF9ShXzzD92.5MYFB8IHfoC','+1-555-0102',NULL,1,NULL,'2026-08-15 21:42:26','2026-08-15 21:42:26'),(4,3,'Alice Thompson','student1@school.com',NULL,'$2y$12$6mig2FQXhfY7H05AanRLfe5UbApr3qO6LT8IQKdDJHuiGzG7vlA9a','+1-555-0201',NULL,1,NULL,'2026-08-15 21:42:26','2026-08-15 21:42:26'),(5,3,'Bob Martinez','student2@school.com',NULL,'$2y$12$8wK6PFO47nqxUiKupy72mu5vbZClIcfsFQr5j0UfMH8mJKgyVgQ8S','+1-555-0202',NULL,1,NULL,'2026-08-15 21:42:26','2026-08-15 21:42:26'),(6,3,'Carol Davis','student3@school.com',NULL,'$2y$12$FCat.rTnBqke5IcK2P7myuBOrJacqn.WKAy4redgPxzEmGYWel8Zm','+1-555-0203',NULL,1,NULL,'2026-08-15 21:42:27','2026-08-15 21:42:27'),(7,3,'David Lee','student4@school.com',NULL,'$2y$12$nwSQvG4xl.J.YggUGCb.P.qtTYLyjFU/tNtZplukh1S/2fj2zpDVe','+1-555-0204',NULL,1,NULL,'2026-08-15 21:42:27','2026-08-15 21:42:27'),(8,3,'Emma Garcia','student5@school.com',NULL,'$2y$12$Ad6VPska1ojC0OKBrA/49e.2HlHuMqg.wrh.jTxb977Oan1Pt6/jq','+1-555-0205',NULL,1,NULL,'2026-08-15 21:42:27','2026-08-15 21:42:27');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-16  3:50:26
