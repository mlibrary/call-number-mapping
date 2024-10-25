/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.5.2-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: callnumber
-- ------------------------------------------------------
-- Server version	11.5.2-MariaDB-ubu2404

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `dewey`
--

DROP TABLE IF EXISTS `dewey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dewey` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `numStart` decimal(6,3) DEFAULT NULL,
  `numEnd` decimal(6,3) DEFAULT NULL,
  `notes` blob DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numStart` (`numStart`),
  KEY `numEnd` (`numEnd`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deweyMap`
--

DROP TABLE IF EXISTS `deweyMap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deweyMap` (
  `dewey` mediumint(9) NOT NULL DEFAULT 0,
  `levelTwo` mediumint(9) DEFAULT NULL,
  KEY `dewey` (`dewey`),
  KEY `levelTwo` (`levelTwo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `encompasses`
--

DROP TABLE IF EXISTS `encompasses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encompasses` (
  `levelOne` mediumint(9) NOT NULL DEFAULT 0,
  `levelTwo` mediumint(9) NOT NULL DEFAULT 0,
  KEY `levelOne` (`levelOne`),
  KEY `levelTwo` (`levelTwo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hlb3_dewey`
--

DROP TABLE IF EXISTS `hlb3_dewey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hlb3_dewey` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `numStart` decimal(6,3) DEFAULT NULL,
  `numEnd` decimal(6,3) DEFAULT NULL,
  `notes` blob DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numStart` (`numStart`),
  KEY `numEnd` (`numEnd`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hlb3_deweyMap`
--

DROP TABLE IF EXISTS `hlb3_deweyMap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hlb3_deweyMap` (
  `dewey` mediumint(9) NOT NULL DEFAULT 0,
  `topic` mediumint(9) DEFAULT NULL,
  KEY `dewey` (`dewey`),
  KEY `levelTwo` (`topic`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hlb3_encompasses`
--

DROP TABLE IF EXISTS `hlb3_encompasses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hlb3_encompasses` (
  `levelOne` mediumint(9) NOT NULL DEFAULT 0,
  `levelTwo` mediumint(9) NOT NULL DEFAULT 0,
  KEY `levelOne` (`levelOne`),
  KEY `levelTwo` (`levelTwo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hlb3_lc`
--

DROP TABLE IF EXISTS `hlb3_lc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hlb3_lc` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `alphaStart` char(3) NOT NULL DEFAULT '',
  `numStart` decimal(7,3) DEFAULT 0.000,
  `cutStart` varchar(4) DEFAULT NULL,
  `alphaEnd` char(3) NOT NULL DEFAULT '',
  `numEnd` decimal(7,3) DEFAULT 9999.999,
  `cutEnd` varchar(4) DEFAULT NULL,
  `notes` blob DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numEnd` (`numEnd`),
  KEY `numStart` (`numStart`),
  KEY `alphaStart` (`alphaStart`),
  KEY `alphaEnd` (`alphaEnd`),
  KEY `cutEnd` (`cutEnd`),
  KEY `cutStart` (`cutStart`)
) ENGINE=MyISAM AUTO_INCREMENT=82488 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hlb3_lcMap`
--

DROP TABLE IF EXISTS `hlb3_lcMap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hlb3_lcMap` (
  `lc` mediumint(9) NOT NULL DEFAULT 0,
  `topic` mediumint(9) DEFAULT NULL,
  KEY `levelTwo` (`topic`),
  KEY `lc` (`lc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hlb3_lcslim`
--

DROP TABLE IF EXISTS `hlb3_lcslim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hlb3_lcslim` (
  `id` mediumint(9) NOT NULL DEFAULT 0,
  `start` varchar(15) DEFAULT NULL,
  `end` varchar(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hlb3_levelOneTopic`
--

DROP TABLE IF EXISTS `hlb3_levelOneTopic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hlb3_levelOneTopic` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hlb3_levelTwoTopic`
--

DROP TABLE IF EXISTS `hlb3_levelTwoTopic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hlb3_levelTwoTopic` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=163 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hlb3_subranges`
--

DROP TABLE IF EXISTS `hlb3_subranges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hlb3_subranges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lcid` int(11) DEFAULT NULL,
  `numStart` decimal(7,3) DEFAULT NULL,
  `alphaEnd` char(3) DEFAULT NULL,
  `numEnd` decimal(7,3) DEFAULT NULL,
  `cutEnd` varchar(4) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `alphaStart` char(3) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `cutStart` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hlb3_topic`
--

DROP TABLE IF EXISTS `hlb3_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hlb3_topic` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=294 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hlb3_topic_topic`
--

DROP TABLE IF EXISTS `hlb3_topic_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hlb3_topic_topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT NULL,
  `child` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=380 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lc`
--

DROP TABLE IF EXISTS `lc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lc` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `alphaStart` char(3) NOT NULL DEFAULT '',
  `numStart` decimal(7,3) DEFAULT NULL,
  `cutStart` varchar(4) DEFAULT NULL,
  `alphaEnd` char(3) NOT NULL DEFAULT '',
  `numEnd` decimal(7,3) DEFAULT NULL,
  `cutEnd` varchar(4) DEFAULT NULL,
  `notes` blob DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numEnd` (`numEnd`),
  KEY `numStart` (`numStart`),
  KEY `alphaStart` (`alphaStart`),
  KEY `alphaEnd` (`alphaEnd`),
  KEY `cutEnd` (`cutEnd`),
  KEY `cutStart` (`cutStart`)
) ENGINE=MyISAM AUTO_INCREMENT=65279 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lcMap`
--

DROP TABLE IF EXISTS `lcMap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lcMap` (
  `lc` mediumint(9) NOT NULL DEFAULT 0,
  `levelTwo` mediumint(9) NOT NULL DEFAULT 0,
  KEY `levelTwo` (`levelTwo`),
  KEY `lc` (`lc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lccopy`
--

DROP TABLE IF EXISTS `lccopy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lccopy` (
  `id` mediumint(9) NOT NULL DEFAULT 0,
  `alphaStart` char(3) NOT NULL DEFAULT '',
  `numStart` decimal(7,3) DEFAULT NULL,
  `cutStart` varchar(4) DEFAULT NULL,
  `alphaEnd` char(3) NOT NULL DEFAULT '',
  `numEnd` decimal(7,3) DEFAULT NULL,
  `cutEnd` varchar(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lccopy_redundancies`
--

DROP TABLE IF EXISTS `lccopy_redundancies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lccopy_redundancies` (
  `id1` mediumint(9) NOT NULL DEFAULT 0,
  `start1` varchar(15) DEFAULT NULL,
  `end1` varchar(15) DEFAULT NULL,
  `id2` mediumint(9) NOT NULL DEFAULT 0,
  `start2` varchar(15) DEFAULT NULL,
  `end2` varchar(15) DEFAULT NULL,
  `newstart` varchar(15) DEFAULT NULL,
  `newend` varchar(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lcslim`
--

DROP TABLE IF EXISTS `lcslim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lcslim` (
  `id` mediumint(9) NOT NULL DEFAULT 0,
  `start` varchar(15) DEFAULT NULL,
  `end` varchar(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `levelOneTopic`
--

DROP TABLE IF EXISTS `levelOneTopic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `levelOneTopic` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `levelTwoTopic`
--

DROP TABLE IF EXISTS `levelTwoTopic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `levelTwoTopic` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `hidden` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=205 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subranges`
--

DROP TABLE IF EXISTS `subranges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subranges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lcid` int(11) DEFAULT NULL,
  `numStart` decimal(7,3) DEFAULT NULL,
  `alphaEnd` char(3) DEFAULT NULL,
  `numEnd` decimal(7,3) DEFAULT NULL,
  `cutEnd` varchar(4) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `alphaStart` char(3) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `cutStart` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1528425 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2024-10-25 15:05:56
