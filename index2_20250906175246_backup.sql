-- MySQL dump 10.13  Distrib 5.7.26, for Win64 (x86_64)
--
-- Host: localhost    Database: index2
-- ------------------------------------------------------
-- Server version	5.7.26

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `file_types`
--

DROP TABLE IF EXISTS `file_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_name` (`type_name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_types`
--

LOCK TABLES `file_types` WRITE;
/*!40000 ALTER TABLE `file_types` DISABLE KEYS */;
INSERT INTO `file_types` VALUES (3,'测试号','2025-09-06 08:12:39');
/*!40000 ALTER TABLE `file_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inspections`
--

DROP TABLE IF EXISTS `inspections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inspections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_time` datetime NOT NULL,
  `inspector` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '呈办人',
  `inspected_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文件名',
  `inspection_details` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '主要内容',
  `remarks` text COLLATE utf8mb4_unicode_ci COMMENT '批示',
  `leader1_status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '张三签批状态',
  `leader2_status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '李四签批状态',
  `leader3_status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '王五签批状态',
  `leader4_status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '赵六签批状态',
  `leader5_status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '钱七签批状态',
  `leader6_status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '孙八签批状态',
  `leader7_status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '周九签批状态',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `file_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '类型一' COMMENT '文件类型',
  `file_header` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '文件头',
  `leader1_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '张三' COMMENT '领导1姓名',
  `leader2_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '李四' COMMENT '领导2姓名',
  `leader3_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '王五' COMMENT '领导3姓名',
  `leader4_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '赵六' COMMENT '领导4姓名',
  `leader5_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '钱七' COMMENT '领导5姓名',
  `leader6_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '孙八' COMMENT '领导6姓名',
  `leader7_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '周九' COMMENT '领导7姓名',
  `type_one_id` int(11) DEFAULT NULL COMMENT '类型一序号',
  `type_two_id` int(11) DEFAULT NULL COMMENT '类型二序号',
  `security_level` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leader8_status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leader9_status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leader10_status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leader8_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leader9_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leader10_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_check_time` (`check_time`),
  KEY `idx_inspector` (`inspector`),
  KEY `idx_inspected_unit` (`inspected_unit`),
  KEY `idx_type_one_id` (`type_one_id`),
  KEY `idx_type_two_id` (`type_two_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inspections`
--

LOCK TABLES `inspections` WRITE;
/*!40000 ALTER TABLE `inspections` DISABLE KEYS */;
INSERT INTO `inspections` VALUES (7,'2025-03-14 00:00:00','王宇','关于进一步加强员工管控的通知','啊实打实大师大师大师大苏打说','按照要求执行','已批','已批','已批',NULL,NULL,NULL,NULL,'2025-03-14 06:49:28','2025-09-06 08:13:44','测试号','2025-01','张三','李四','王五','赵六','钱七','孙八','周九',4,NULL,'',NULL,NULL,NULL,'吴十','郑十一','王十二');
/*!40000 ALTER TABLE `inspections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leader_config`
--

DROP TABLE IF EXISTS `leader_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leader_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(50) NOT NULL COMMENT '职位序号(leader1~leader7)',
  `name` varchar(50) NOT NULL COMMENT '领导姓名',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leader_config`
--

LOCK TABLES `leader_config` WRITE;
/*!40000 ALTER TABLE `leader_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `leader_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'xncz','$2y$10$mMmVGxfUC2DJm.loZ/as9OfFBTbcKljEk7ueHZoTnuls6ADLZiAD2','2025-03-14 02:02:26');
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

-- Dump completed on 2025-09-06 17:52:46
