-- MySQL dump 10.11
--
-- Host: localhost    Database: up
-- ------------------------------------------------------
-- Server version	5.0.67

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
-- Table structure for table `antiflood`
--

DROP TABLE IF EXISTS `antiflood`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `antiflood` (
  `id` int(10) unsigned NOT NULL,
  `last_downloaded_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip` varchar(15) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `message` blob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=165 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `description`
--

DROP TABLE IF EXISTS `description`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `description` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  UNIQUE KEY `item_id` (`item_id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dnow`
--

DROP TABLE IF EXISTS `dnow`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dnow` (
  `id` int(10) unsigned NOT NULL,
  `ld` datetime NOT NULL,
  `n` int(10) unsigned default '1',
  `type` enum('down','up') default NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `download_links`
--

DROP TABLE IF EXISTS `download_links`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `download_links` (
  `item_id` int(10) unsigned NOT NULL,
  `link_expire` datetime NOT NULL default '0000-00-00 00:00:00',
  `link_magic` varchar(32) NOT NULL,
  `ip` varchar(15) NOT NULL,
  UNIQUE KEY `comp` (`item_id`,`ip`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `downloads`
--

DROP TABLE IF EXISTS `downloads`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `downloads` (
  `item_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `feedback` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` varchar(15) default NULL,
  `date` datetime NOT NULL,
  `message` blob NOT NULL,
  `email` varchar(255) default NULL,
  `file` varchar(255) default NULL,
  `readed` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `guestbook_posts`
--

DROP TABLE IF EXISTS `guestbook_posts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `guestbook_posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` blob NOT NULL,
  `ip` varchar(15) NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `message` blob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` datetime NOT NULL,
  `type` enum('debug','info','warn','error') NOT NULL,
  `message` blob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3806 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `searchs`
--

DROP TABLE IF EXISTS `searchs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `searchs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `searchs` varchar(255) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `session` (
  `sid` varchar(40) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `expire` datetime NOT NULL,
  `username` varchar(128) NOT NULL,
  `email` varchar(129) default NULL,
  `admin` tinyint(1) default '0',
  KEY `sid` (`sid`,`uid`,`ip`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `storage`
--

DROP TABLE IF EXISTS `storage`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `storage` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `upload_url` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `device` varchar(32) NOT NULL,
  `mount_point` varchar(64) NOT NULL,
  `prio` int(10) unsigned NOT NULL default '5',
  `disabled` tinyint(1) default NULL,
  `hash` text,
  UNIQUE KEY `upload_url` (`upload_url`,`name`,`device`,`mount_point`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `up`
--

DROP TABLE IF EXISTS `up`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `up` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `password` varchar(80) default NULL,
  `delete_num` int(10) unsigned NOT NULL,
  `uploaded_date` datetime NOT NULL,
  `last_downloaded_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip` varchar(15) NOT NULL,
  `location` varchar(64) default NULL,
  `sub_location` int(1) unsigned default '1',
  `filename` varchar(256) default NULL,
  `filename_fuse` varchar(256) default NULL,
  `mime` varchar(64) NOT NULL,
  `size` bigint(20) default NULL,
  `downloads` int(10) unsigned NOT NULL default '0',
  `antivir_checked` int(1) unsigned default '0',
  `deleted` int(1) unsigned default '0',
  `deleted_reason` varchar(200) default NULL,
  `deleted_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `md5` varchar(32) default NULL,
  `spam` tinyint(1) default '0',
  `adult` tinyint(1) default NULL,
  `hidden` tinyint(1) default NULL,
  `user_id` int(10) unsigned default '0',
  PRIMARY KEY  (`id`),
  KEY `mime` (`mime`),
  KEY `password` (`password`),
  KEY `spam` (`spam`),
  KEY `hidden` (`hidden`),
  KEY `deleted` (`deleted`),
  KEY `adult` (`adult`),
  KEY `spam_idx` (`spam`),
  KEY `adult_idx` (`adult`),
  KEY `filename_nf_idx` (`filename`(10)),
  KEY `search_idx` (`deleted`,`hidden`,`spam`,`adult`,`filename`(10)),
  KEY `get_count_idx` (`deleted`,`hidden`,`spam`,`adult`),
  FULLTEXT KEY `filename` (`filename`),
  FULLTEXT KEY `filename_2` (`filename`),
  FULLTEXT KEY `filename_idx` (`filename`)
) ENGINE=MyISAM AUTO_INCREMENT=39496 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(256) default NULL,
  `regdate` datetime NOT NULL,
  `uploads` int(10) unsigned default '0',
  `uploads_size` bigint(20) unsigned default '0',
  `admin` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-11-15 18:03:50
