<?php

define('DEBUG', true);


define('MYSQL_ADDRESS', 'localhost');
define('MYSQL_LOGIN', 'root');
define('MYSQL_PASSWORD', '');
define('MYSQL_DB', 'up');
define('MYSQL_CHARSET', 'utf8');

// memcache
define('MEMCACHE_HOST', '194.146.132.66');
define('MEMCACHE_PORT', '11211');
define('MEMCACHE_PERSISTENT_CONNECT', true);

// top page
define('SORT_BY_DOWNLOADS', 1);
define('SORT_BY_DATE', 2);
define('SORT_BY_SIZE', 3);
define('SORT_BY_NAME', 4);

// ajax
define('ACTION_DELETE_FILE', 1);
define('ACTION_UNDELETE_FILE', 2);
define('ACTION_RENAME_FILE', 3);
define('ACTION_GET_MD5', 4);
define('ACTION_MAKE_ME_OWNER', 5);
define('ACTION_SEARCH', 10);
define('ACTION_GET_PE', 11);
define('ACTION_GET_UPLOAD_URL', 13);

//
define('ANTIVIR_NOT_CHECKED', 7);
define('ANTIVIR_CLEAN', 0);
define('ANTIVIR_VIRUS', 1);
define('ANTIVIR_ERROR', 2);

// admin
define('ACTION_ADMIN_UNDELETE_FILE', 24);
define('ACTION_ADMIN_DELETE_FILE', 25);
define('ACTION_ADMIN_MARK_AS_SPAM_FILE', 26);
define('ACTION_ADMIN_UNMARK_AS_SPAM_FILE', 27);
define('ACTION_ADMIN_MARK_AS_ADULT_FILE', 28);
define('ACTION_ADMIN_UNMARK_AS_ADULT_FILE', 29);
define('ACTION_ADMIN_DELETE_FEEDBACK_MESSAGE', 50);


// BASE URL aka CDN
define('CSS_BASE_URL', 'http://up.lluga.net/');
define('JS_BASE_URL', 'http://ftp.lluga.net/');
define('JS_BASE_URL_1', 'http://up.iteam.net.ua/');



$base_url = 'http://up.lluga.net/';


// COOKIE SECTION
$cookie_name = 'up_cookie_login';
$cookie_domain = '';
$cookie_path = '/';
$cookie_secure = 0;
$cookieSalt = 'zz554d_$ddd;%767,dds';
$csrfKey = '05dddlaoezz:_=dd';


// FILES SIZE SECTION
$max_file_size = 9000;
$very_small_file_size = 3;
$small_file_size = 200;
$max_file_size_for_upload_antivir_check = 10;


// FTP access
$ftpAccessEnabled = TRUE;
$ftpUploadRate = 1048576*5;
$ftpDownloadRate = 1048576*5;
$ftpUIDBase = 5000;
$ftpGIDBase = 5000;


// PATH SECTION
$upload_dir = '/var/upload/';
$thumbs_dir = $upload_dir.'/thumbs/';
$feedback_upload_dir = $upload_dir.'/up_feedback_files/';


// EMAIL
$feedback_email = 'dark@iteam.net.ua';


// CACHE TIMEOUTS
$cache_timeout_rss = 300;


// TIMEOUT SECTION
// in days
$undelete_interval = 2;
$non_downloaded_interval = 10;
$non_downloaded_spam_interval = 2;

// very small files
$non_downloaded_very_small_files_interval = intval($non_downloaded_interval * 30, 10);
$non_downloaded_very_small_files_popular_interval = intval($non_downloaded_very_small_files_interval * 2, 10);

// small files
$non_downloaded_small_files_interval = intval($non_downloaded_interval * 6, 10);
$non_downloaded_small_files_popular_interval = intval($non_downloaded_small_files_interval * 5, 10);

// big files
$non_downloaded_big_files_interval = intval($non_downloaded_interval * 2, 10);
$non_downloaded_big_files_popular_interval = intval($non_downloaded_big_files_interval * 2, 10);

//
$popular_num = 20;



// THUMBS SECTION
$thumbs_w = 250;
$thumbs_h = 250;
//
$thumbs_preview_w = 1024;
$thumbs_preview_h = 768;


// GOOGLE ANALYTICS SECTION
$googleAnalyticsCode = '';



// STORAGE SECTION
$storage_1 = array(
	'upload_url' => '/upload',
	'name' => 'upload',
	'device' => '/dev/sdb1',
	'mount_point' => '/var/upload',
	'prio' => 5,
	'disabled' => 0,
	'hash' => array('1','2','3','4')
);

$storage_2 = array(
	'upload_url' => '/upload17',
	'name' => 'upload17',
	'device' => '/dev/sdd1',
	'mount_point' => '/var/upload/17',
	'prio' => 3,
	'disabled' => 0,
	'hash' => array('17')
);

$storage_3 = array(
	'upload_url' => '/upload18',
	'name' => 'upload18',
	'device' => '/dev/mapper/pdc_gcdcfciap1',
	'mount_point' => '/var/upload/18',
	'disabled' => 1,
	'prio' => 3,
	'hash' => array('18')
);

$storage_4 = array(
	'upload_url' => '/upload19',
	'name' => 'upload19',
	'device' => '/dev/sde1',
	'mount_point' => '/var/upload/19',
	'prio' => 3,
	'disabled' => 0,
	'hash' => array('19')
);

$storage_5 = array(
	'upload_url' => '/upload15',
	'name' => 'upload15',
	'device' => '/dev/sda1',
	'mount_point' => '/var/upload/15',
	'prio' => 3,
	'disabled' => 0,
	'hash' => array('15')
);

define('UP', 1);

?>
