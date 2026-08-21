-- MySQL dump 10.13  Distrib 9.7.1, for Linux (x86_64)
--
-- Host: localhost    Database: asset_manager
-- ------------------------------------------------------
-- Server version	9.7.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ 'd6ccd034-8647-11f1-8d66-c61046bf89b3:1-87';

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assets` (
  `asset_id` int NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(100) NOT NULL,
  `category_id` int DEFAULT NULL,
  `asset_type` enum('loan','consumable') NOT NULL,
  `stock` int unsigned DEFAULT NULL,
  `min_stock` int unsigned DEFAULT NULL,
  `unit` varchar(20) NOT NULL,
  `max_request_quantity` int DEFAULT NULL,
  `monthly_request_limit` int DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`asset_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `assets_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `loan_categories` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
INSERT INTO `assets` VALUES (1,'PC',1,'loan',NULL,NULL,'unit',NULL,NULL,'2026-07-27 08:52:56','2026-07-27 08:52:56'),(2,'Laptop PC',1,'loan',NULL,NULL,'台',NULL,NULL,'2026-08-03 01:07:04','2026-08-03 01:07:04'),(3,'Desktop PC',1,'loan',NULL,NULL,'台',NULL,NULL,'2026-08-03 01:07:04','2026-08-03 01:07:04'),(4,'Java入門書',2,'loan',NULL,NULL,'冊',NULL,NULL,'2026-08-03 01:07:04','2026-08-03 01:07:04'),(5,'Laravel入門書',2,'loan',NULL,NULL,'冊',NULL,NULL,'2026-08-03 01:07:04','2026-08-03 01:07:04'),(7,'ボールペン',NULL,'consumable',18,10,'本',2,1,'2026-08-03 01:07:12','2026-08-13 03:03:38'),(8,'ノート',NULL,'consumable',15,5,'冊',2,1,'2026-08-03 01:07:12','2026-08-03 01:07:12'),(9,'コピー用紙',NULL,'consumable',3,5,'箱',1,1,'2026-08-03 01:07:12','2026-08-03 01:07:12'),(10,'付箋',NULL,'consumable',4,10,'個',3,2,'2026-08-03 01:07:12','2026-08-03 01:07:12'),(11,'クリアファイル',NULL,'consumable',50,20,'枚',5,1,'2026-08-03 01:07:12','2026-08-03 01:07:12'),(12,'ホワイトボードマーカー',NULL,'consumable',10,8,'本',2,1,'2026-08-03 01:07:12','2026-08-05 23:58:54'),(13,'MacBook Pro',1,'loan',NULL,NULL,'台',NULL,NULL,'2026-08-06 04:10:10','2026-08-06 04:10:10'),(14,'Surface Laptop',1,'loan',NULL,NULL,'台',NULL,NULL,'2026-08-06 04:10:10','2026-08-06 04:10:10'),(15,'ThinkPad X1',1,'loan',NULL,NULL,'台',NULL,NULL,'2026-08-06 04:10:10','2026-08-06 04:10:10'),(16,'iPad Air',3,'loan',NULL,NULL,'台',NULL,NULL,'2026-08-06 04:10:10','2026-08-06 04:10:10'),(18,'プロジェクター',3,'loan',NULL,NULL,'台',NULL,NULL,'2026-08-06 04:10:10','2026-08-06 04:10:10'),(19,'ネットワーク入門',2,'loan',NULL,NULL,'冊',NULL,NULL,'2026-08-06 04:10:10','2026-08-06 04:10:10'),(20,'Python入門',2,'loan',NULL,NULL,'冊',NULL,NULL,'2026-08-06 04:10:10','2026-08-06 04:10:10'),(22,'Docker & Kubernetes',2,'loan',NULL,NULL,'冊',NULL,NULL,'2026-08-06 04:10:10','2026-08-06 04:10:10'),(23,'DELL PC',1,'loan',NULL,NULL,'台',NULL,NULL,'2026-08-05 23:00:56','2026-08-05 23:00:56'),(24,'ThinkBook',1,'loan',NULL,NULL,'台',NULL,NULL,'2026-08-05 23:03:24','2026-08-05 23:03:24'),(25,'トイレットペーパー',NULL,'consumable',8,4,'台',7,3,'2026-08-05 23:12:50','2026-08-06 00:00:02');
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consumable_histories`
--

DROP TABLE IF EXISTS `consumable_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consumable_histories` (
  `consumable_history_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `asset_id` int NOT NULL,
  `request_date` datetime NOT NULL,
  `quantity` int unsigned NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`consumable_history_id`),
  KEY `user_id` (`user_id`),
  KEY `asset_id` (`asset_id`),
  CONSTRAINT `consumable_histories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `consumable_histories_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`asset_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consumable_histories`
--

LOCK TABLES `consumable_histories` WRITE;
/*!40000 ALTER TABLE `consumable_histories` DISABLE KEYS */;
INSERT INTO `consumable_histories` VALUES (1,1,7,'2026-08-03 00:00:00',2,'2026-08-03 01:11:59','2026-08-03 01:11:59'),(2,2,8,'2026-08-03 00:00:00',1,'2026-08-03 01:11:59','2026-08-03 01:11:59'),(3,3,9,'2026-07-03 00:00:00',1,'2026-08-03 01:11:59','2026-08-03 01:11:59'),(4,4,10,'2026-08-03 00:00:00',2,'2026-08-03 01:11:59','2026-08-03 01:11:59'),(5,5,11,'2026-08-03 00:00:00',5,'2026-08-03 01:11:59','2026-08-03 01:11:59'),(6,1,7,'2026-08-03 00:00:00',4,'2026-08-03 00:02:56','2026-08-03 00:02:56'),(7,1,7,'2026-08-03 00:00:00',4,'2026-08-03 00:02:59','2026-08-03 00:02:59'),(8,1,7,'2026-08-03 00:00:00',1,'2026-08-03 00:03:49','2026-08-03 00:03:49'),(9,1,7,'2026-08-04 00:00:00',1,'2026-08-03 22:07:42','2026-08-03 22:07:42'),(10,1,7,'2026-08-06 00:00:00',1,'2026-08-05 21:45:02','2026-08-05 21:45:02'),(11,2,7,'2026-08-10 00:00:00',1,'2026-08-09 21:25:32','2026-08-09 21:25:32'),(12,2,7,'2026-08-12 00:00:00',1,'2026-08-12 06:02:09','2026-08-12 06:02:09');
/*!40000 ALTER TABLE `consumable_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `csv_files`
--

DROP TABLE IF EXISTS `csv_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `csv_files` (
  `csv_file_id` int NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `target_period_start` date NOT NULL,
  `record_count` int unsigned NOT NULL,
  `generated_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`csv_file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `csv_files`
--

LOCK TABLES `csv_files` WRITE;
/*!40000 ALTER TABLE `csv_files` DISABLE KEYS */;
INSERT INTO `csv_files` VALUES (1,'accounting_202608.csv','2026-08-01',5,'2026-08-06 11:39:31','2026-08-06 02:39:31'),(2,'accounting_202608.csv','2026-08-01',15,'2026-08-06 18:25:44','2026-08-06 09:25:44'),(3,'accounting_202608.csv','2026-07-01',1,'2026-08-07 01:05:37','2026-08-06 16:05:37');
/*!40000 ALTER TABLE `csv_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loan_categories`
--

DROP TABLE IF EXISTS `loan_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loan_categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  `max_loan_days` int NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loan_categories`
--

LOCK TABLES `loan_categories` WRITE;
/*!40000 ALTER TABLE `loan_categories` DISABLE KEYS */;
INSERT INTO `loan_categories` VALUES (1,'PC','2026-07-27 08:52:39','2026-07-27 08:52:39',14),(2,'Book','2026-08-03 01:05:13','2026-08-03 01:05:13',14),(3,'Equipment','2026-08-03 01:05:13','2026-08-03 01:05:13',30);
/*!40000 ALTER TABLE `loan_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loan_histories`
--

DROP TABLE IF EXISTS `loan_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loan_histories` (
  `loan_history_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `asset_id` int NOT NULL,
  `loan_date` datetime NOT NULL,
  `due_date` date NOT NULL,
  `return_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`loan_history_id`),
  KEY `user_id` (`user_id`),
  KEY `asset_id` (`asset_id`),
  CONSTRAINT `loan_histories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `loan_histories_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`asset_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loan_histories`
--

LOCK TABLES `loan_histories` WRITE;
/*!40000 ALTER TABLE `loan_histories` DISABLE KEYS */;
INSERT INTO `loan_histories` VALUES (1,1,1,'2026-07-27 17:00:00','2026-08-03','2026-07-30 02:37:05','2026-07-27 08:53:20','2026-07-29 17:37:05'),(2,1,1,'2026-07-27 00:00:00','2026-08-10','2026-08-04 05:44:24','2026-08-03 01:11:18','2026-08-03 20:44:24'),(3,2,2,'2026-07-19 00:00:00','2026-07-30',NULL,'2026-08-03 01:11:18','2026-08-03 01:11:18'),(4,3,3,'2026-07-14 00:00:00','2026-07-28',NULL,'2026-08-03 01:11:18','2026-08-03 01:11:18'),(5,4,4,'2026-07-13 00:00:00','2026-07-27',NULL,'2026-08-03 01:11:18','2026-08-03 01:11:18'),(6,5,5,'2026-07-04 00:00:00','2026-07-18','2026-07-19 10:11:18','2026-08-03 01:11:18','2026-08-03 01:11:18'),(7,1,1,'2026-08-04 07:08:02','2026-08-18','2026-08-06 03:34:27','2026-08-03 22:08:02','2026-08-05 18:34:27'),(8,2,1,'2026-08-03 10:17:47','2026-08-17','2026-08-12 15:09:28','2026-08-06 01:17:47','2026-08-12 06:09:28'),(9,3,2,'2026-07-22 10:17:58','2026-08-05',NULL,'2026-08-06 01:17:58','2026-08-06 01:17:58'),(10,2,5,'2026-07-22 11:37:35','2026-08-05',NULL,'2026-08-06 02:37:35','2026-08-06 02:37:35'),(11,1,1,'2026-08-06 12:22:25','2026-08-20','2026-08-06 03:56:45','2026-08-06 03:22:25','2026-08-05 18:56:45'),(12,2,13,'2026-08-06 04:24:13','2026-08-20',NULL,'2026-08-05 19:24:13','2026-08-05 19:24:13'),(13,1,14,'2026-08-10 07:02:39','2026-08-24','2026-08-10 16:33:50','2026-08-09 22:02:39','2026-08-10 07:33:50'),(14,1,18,'2026-08-10 16:06:30','2026-09-09','2026-08-10 16:34:12','2026-08-10 07:06:30','2026-08-10 07:34:12'),(15,1,14,'2026-08-10 16:34:39','2026-08-24','2026-08-10 16:34:54','2026-08-10 07:34:39','2026-08-10 07:34:54'),(16,1,14,'2026-08-10 16:45:23','2026-08-24','2026-08-10 16:45:44','2026-08-10 07:45:23','2026-08-10 07:45:44'),(17,1,14,'2026-08-10 16:46:29','2026-08-24','2026-08-10 16:48:51','2026-08-10 07:46:29','2026-08-10 07:48:51'),(18,1,18,'2026-08-10 16:50:07','2026-09-09','2026-08-10 16:50:21','2026-08-10 07:50:07','2026-08-10 07:50:21'),(19,1,1,'2026-08-13 11:23:49','2026-08-27','2026-08-13 11:24:21','2026-08-13 02:23:49','2026-08-13 02:24:21');
/*!40000 ALTER TABLE `loan_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

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

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'user','2026-07-24 02:10:24','2026-07-24 02:10:24'),(2,'admin','2026-07-24 02:10:24','2026-07-24 02:10:24');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `employee_no` varchar(32) NOT NULL,
  `user_name` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'1','suzuki ichiro','test@mail.jp','$2y$12$VEBBT8ICbzCYLwrcw8n.bOXS/BGZWIaJJQmzPK8rdSstVkzo.SNYO',1,'2026-07-24 07:00:27','2026-07-24 07:00:27'),(2,'2','tanaka jiro','tanajiro@mail.jp','$2y$12$VEBBT8ICbzCYLwrcw8n.bOXS/BGZWIaJJQmzPK8rdSstVkzo.SNYO',2,'2026-08-03 00:58:38','2026-08-03 00:58:38'),(3,'3','sato hanako','sato@mail.jp','abc12345!',1,'2026-08-03 01:11:02','2026-08-03 01:11:02'),(4,'4','yamada taro','yamada@mail.jp','abc12345!',1,'2026-08-03 01:11:02','2026-08-03 01:11:02'),(5,'5','saito jiro','saito@mail.jp','abc12345!',1,'2026-08-03 01:11:02','2026-08-03 01:11:02');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-17 12:47:18
