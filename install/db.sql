USE up;

-- DROP TABLE IF EXISTS `up`;
-- CREATE TABLE `up`
-- (
-- 	`id` int(10) unsigned NOT NULL auto_increment,
-- 	`group_id` int(10) unsigned NOT NULL DEFAULT 1,
--  `password` varchar(80),
-- 	`delete_num` int(10) unsigned NOT NULL,
-- 	`uploaded_date` datetime NOT NULL,
-- 	`last_downloaded_date` datetime NOT NULL default '0000-00-00 00:00:00',
-- 	`ip` varchar(15) NOT NULL,
-- 	`location` text NOT NULL,
--	`sub_location` int(1) unsigned DEFAULT 1,
-- 	`filename` varchar(255) NOT NULL DEFAULT 'error.file',
-- 	`mime` varchar(64) NOT NULL,
-- 	`size` int(10) unsigned NOT NULL DEFAULT 0,
-- 	`downloads` int(10) unsigned NOT NULL DEFAULT 0,
-- 	`antivir_checked` int(1) unsigned DEFAULT 0,
-- 	`deleted` int(1) unsigned DEFAULT 0,
-- 	`deleted_reason` text,
-- 	`deleted_date` datetime NOT NULL default '0000-00-00 00:00:00',
-- 	`md5` varchar(32),
-- 	PRIMARY KEY  (`id`)
-- ) ENGINE=MyISAM DEFAULT CHARSET=UTF8;

-- ALTER TABLE up ADD COLUMN owner INT(10) UNSIGNED NOT NULL AFTER group_id;


/*DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback`
(
	`id` int(10) unsigned NOT NULL auto_increment,
   	`ip` INT UNSIGNED NOT NULL,
   	`date` datetime NOT NULL,
   	`message` blob NOT NULL,
	`email` varchar(129),
	`file` varchar(255),
	`filename` varchar(255),
	`readed` bool,
   	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs`
(
	`id` int(10) unsigned NOT NULL auto_increment,
   	`date` datetime NOT NULL,
	`type` enum('debug','info','warn','error') NOT NULL,
   	`message` blob NOT NULL,
   	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;*/

/*DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`
(
	`id` int(10) unsigned NOT NULL auto_increment,
	`username` varchar(33) NOT NULL,
	`password` varchar(129) NOT NULL,
	`email` varchar(129),
   	`regdate` datetime NOT NULL,
   	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;*/

/*DROP TABLE IF EXISTS `session`;
CREATE TABLE `session`
(
	`sid` VARCHAR(40) NOT NULL,
	`uid` INT(10) UNSIGNED NOT NULL,
	`ip` INT UNSIGNED NOT NULl,
   	`expire` datetime NOT NULL,
   	UNIQUE (sid,uid),
	KEY (sid,uid,ip)
) ENGINE=Memory DEFAULT CHARSET=utf8;
*/

/*DROP TABLE IF EXISTS `bookmarks`;
CREATE TABLE `bookmarks`
(
	`userid` int(10) unsigned NOT NULL,
	`itemid` int(10) unsigned NOT NULL,
	UNIQUE (userid,itemid),
	KEY(userid, itemid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
/*
DROP TABLE IF EXISTS `storage`;
CREATE TABLE `storage`
(
	`id` int(10) unsigned NOT NULL auto_increment,
	`upload_url` varchar(64) NOT NULL,
	`name` varchar(64) NOT NULL,
	`device` varchar(32) NOT NULL,
	`mount_point` varchar(64) NOT NULL,
	`prio` int unsigned NOT NULL DEFAULT 5,
	`disabled` BOOL,
	`hash` TEXT,
	UNIQUE (upload_url,name,device,mount_point),
	KEY(id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;*/

/*
DROP TABLE IF EXISTS `description`;
CREATE TABLE `description`
(
	`id` int(10) unsigned NOT NULL auto_increment,
	`item_id` int(10) unsigned NOT NULL,
	`description` TEXT NOT NULL,
	UNIQUE (item_id),
	KEY(id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments`
(
	`id` int(10) unsigned NOT NULL auto_increment,
	`item_id` int(10) unsigned NOT NULL,
	`user_id` int(10) unsigned NOT NULL,
   	`date` datetime NOT NULL,
   	`message` blob NOT NULL,
   	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*
alter table up change column filename filename varchar(256);
alter table up change column filename_fuse filename_fuse varchar(256);
alter table up change column location location varchar(64);
alter table up change column deleted_reason deleted_reason varchar(200);
alter table up drop column group_secret_key;
alter table up drop column group_id;
alter table up drop column description;
ALTER TABLE session ADD COLUMN email VARCHAR(129);
*/
