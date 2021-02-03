-- MariaDB dump 10.18  Distrib 10.5.8-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: test_task
-- ------------------------------------------------------
-- Server version	10.5.8-MariaDB

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
-- Table structure for table `boosterpack`
--

DROP TABLE IF EXISTS `boosterpack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `boosterpack` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bank` decimal(10,2) NOT NULL DEFAULT 0.00,
  `time_created` timestamp NULL DEFAULT current_timestamp(),
  `time_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boosterpack`
--

LOCK TABLES `boosterpack` WRITE;
/*!40000 ALTER TABLE `boosterpack` DISABLE KEYS */;
INSERT INTO `boosterpack` VALUES (1,5.00,0.00,'2020-03-30 00:17:28','2021-02-03 16:23:12'),(2,20.00,0.00,'2020-03-30 00:17:28','2021-02-03 16:23:12'),(3,50.00,0.00,'2020-03-30 00:17:28','2021-02-01 20:30:39');
/*!40000 ALTER TABLE `boosterpack` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `assign_id` int(10) unsigned NOT NULL,
  `reply_to` int(10) DEFAULT NULL,
  `replies_count` int(10) NOT NULL DEFAULT 0,
  `text` text NOT NULL,
  `time_created` timestamp NULL DEFAULT current_timestamp(),
  `time_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` VALUES (1,1,1,NULL,1,'Ну чо ассигн проверим','2020-03-27 21:39:44','2021-02-01 22:50:49'),(2,1,1,1,2,'Второй коммент','2020-03-27 21:39:55','2021-02-02 15:11:07'),(3,2,1,2,1,'Второй коммент от второго человека','2020-03-27 21:40:22','2021-02-01 22:50:49'),(4,2,1,3,1,'Third comment reply','2021-02-01 22:15:12','2021-02-01 22:50:49'),(5,2,1,4,1,'Fourth nested comment','2021-02-01 22:16:35','2021-02-01 22:50:49'),(6,2,1,1,0,'Второй ответ на первый комент','2021-02-01 22:26:45','2021-02-01 22:26:45');
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `img` varchar(1024) DEFAULT NULL,
  `likes_count` int(11) NOT NULL DEFAULT 0,
  `time_created` timestamp NULL DEFAULT current_timestamp(),
  `time_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post`
--

LOCK TABLES `post` WRITE;
/*!40000 ALTER TABLE `post` DISABLE KEYS */;
INSERT INTO `post` VALUES (1,1,'Тестовый постик 1','/images/posts/1.png',0,'2018-08-30 13:31:14','2021-02-03 16:22:51'),(2,1,'Печальный пост','/images/posts/2.png',0,'2018-10-11 01:33:27','2021-02-01 20:30:39');
/*!40000 ALTER TABLE `post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `transaction_type` enum('withdraw','refill') COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_subject` enum('booster_pack','like','topup') COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_record` int(11) DEFAULT NULL,
  `wallet_type` enum('wallet','likes') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double NOT NULL,
  `time_created` timestamp NULL DEFAULT current_timestamp(),
  `time_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction`
--

LOCK TABLES `transaction` WRITE;
/*!40000 ALTER TABLE `transaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `transaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(60) DEFAULT NULL,
  `password` varchar(72) DEFAULT NULL,
  `personaname` varchar(50) NOT NULL DEFAULT '',
  `avatarfull` varchar(150) NOT NULL DEFAULT '',
  `rights` tinyint(4) NOT NULL DEFAULT 0,
  `wallet_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wallet_total_refilled` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wallet_total_withdrawn` decimal(10,2) NOT NULL DEFAULT 0.00,
  `likes_balance` int(11) NOT NULL DEFAULT 0,
  `time_created` datetime NOT NULL,
  `time_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `time_created` (`time_created`),
  KEY `time_updated` (`time_updated`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin@niceadminmail.pl','$2y$10$Oj6tfq2Q9pNiwOAJHXfw9.YcKqoEQ0Avst6yrWZr56BYjQij/J/xW','AdminProGod','https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/96/967871835afdb29f131325125d4395d55386c07a_full.jpg',1,0.00,0.00,0.00,0,'2019-07-26 01:53:54','2021-02-03 16:22:38'),(2,'simpleuser@niceadminmail.pl',NULL,'simpleuser','https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/86/86a0c845038332896455a566a1f805660a13609b_full.jpg',0,0.00,0.00,0.00,0,'2019-07-26 01:53:54','2021-02-01 20:30:40');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-02-03 18:24:50
