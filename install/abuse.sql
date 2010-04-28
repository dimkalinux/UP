DROP TABLE IF EXISTS `abuse`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `abuse` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `ip` varchar(15) default NULL,
  `date` datetime NOT NULL,
  `abuse_type` tinyint(1),
  `message` blob NOT NULL,
  `weight` tinyint NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
