<?php

define('DEBUG', TRUE);


define('MYSQL_ADDRESS', '194.146.132.67');
define('MYSQL_LOGIN', 'up2user');
define('MYSQL_PASSWORD', 'uCanStealThisPasswordIDontCare');
define('MYSQL_DB', 'up2');
//define('MYSQL_CHARSET', 'latin1');

// memcache
define('MEMCACHE_HOST', '194.146.132.67');
define('MEMCACHE_PORT', '11211');
define('MEMCACHE_PERSISTENT_CONNECT', TRUE);

// top page
define('SORT_BY_DOWNLOADS', 1);
define('SORT_BY_DATE', 2);
define('SORT_BY_SIZE', 3);
define('SORT_BY_NAME', 4);

// ajax
define('ACTION_DELETE_FILE', 1);
define('ACTION_UNDELETE_FILE', 2);
define('ACTION_RENAME_FILE', 3);
define('ACTION_CHANGE_PASSWORD', 4);
define('ACTION_MAKE_ME_OWNER', 5);
define('ACTION_SEARCH', 10);
define('ACTION_GET_PE', 11);
define('ACTION_GET_UPLOAD_URL', 13);
define('ACTION_GET_COMMENTS', 14);
define('ACTION_OWNER_DELETE_ITEM', 15);
define('ACTION_OWNER_UNDELETE_ITEM', 16);
define('ACTION_OWNER_GET_UPDATED_ITEMS', 17);



//
define('ANTIVIR_NOT_CHECKED', 7);
define('ANTIVIR_CLEAN', 0);
define('ANTIVIR_VIRUS', 1);
define('ANTIVIR_ERROR', 2);

// admin
define('ACTION_ADMIN_UNDELETE_ITEM', 24);
define('ACTION_ADMIN_DELETE_ITEM', 25);
define('ACTION_ADMIN_MARK_AS_SPAM_FILE', 26);
define('ACTION_ADMIN_UNMARK_AS_SPAM_FILE', 27);
define('ACTION_ADMIN_MARK_AS_ADULT_FILE', 28);
define('ACTION_ADMIN_UNMARK_AS_ADULT_FILE', 29);
define('ACTION_ADMIN_HIDE_ITEM', 30);
define('ACTION_ADMIN_UNHIDE_ITEM', 31);

define('ACTION_ADMIN_DELETE_FEEDBACK_MESSAGE', 50);
define('ACTION_ADMIN_DELETE_COMMENT', 51);

//
define('ACTION_COMMENTS_ADD', 100);
define('ACTION_COMMENTS_DEL', 101);


// BASE URL aka CDN
define('CSS_BASE_URL', 'http://up2.lluga.net/');
define('JS_BASE_URL', 'http://up2.lluga.net/');
define('JS_BASE_URL_1', 'http://up2.lluga.net/');

define('UPLOAD_NO_ERROR', 0);
define('UPLOAD_ERROR_FOUND_VIRUS', 1);
define('UPLOAD_ERROR_SAVE', 2);
define('UPLOAD_ERROR_MAX_SIZE', 3);
define('UPLOAD_ERROR_SERVER_FAIL', 4);
define('UPLOAD_ERROR_FLOOD', 5);
define('UPLOAD_ERROR_NO_FILE', 6);
define('UPLOAD_ERROR_STORAGE', 7);
define('UPLOAD_ERROR_EMPTY_FILE', 8);

define('UPLOAD_FILE_RIGHTS', 0444);


$base_url = 'http://up2.lluga.net/';

// LOGIN METHOD
define('USE_OPENID', FALSE);


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
$small_file_size = 400;
$max_file_size_for_antivir_check = 800*1048576;
$maxSPAM_Size = 128*1048576;


// FTP access
$ftpbaseDir = '/var/fuse/';
$ftpAccessEnabled = TRUE;
$ftpUploadRate = 1048576*5;
$ftpDownloadRate = 1048576*5;
$ftpRateK = 1.5;
$ftpUIDBase = 5000;
$ftpGIDBase = 5000;


// PATH SECTION
$upload_dir = '/var/upload/';
$server_root = '/var/www/up2/';
$thumbs_dir = $server_root.'thumbs/';
$feedback_upload_dir = $upload_dir.'/up_feedback_files/';


// EMAIL
$feedback_email = 'webmaster@iteam.lg.ua';


// CACHE TIMEOUTS
$cache_timeout_rss = 300;
$cache_timeout_search_complete = 120;


// MAKE HASH TIMEOUT
$makeHashTimeout = 60;
$makeVirusesTimeout = 30;


// TIMEOUT SECTION
// in days
$undelete_interval = 2;
$non_downloaded_interval = 10;
$non_downloaded_spam_interval = 2;
$non_downloaded_count = 3;

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


// COMMENT SECTION
$maxCommentLength = 1024;

// FILE LIST SECTION
$minFileSizeForTOP = 1048576;

// THUMBS SECTION
// in px
$thumbs_w = 150;
$thumbs_h = 150;
//
$thumbs_preview_w = 1024;
$thumbs_preview_h = 768;


// GOOGLE ANALYTICS SECTION
$googleAnalyticsCode = 'UA-6106025-1';

// SEARCH
$searchCompleteMaxResults = 12;

//
$enableCleaner = TRUE;
$enableChecker = TRUE;

// CHECKER
$checker_fix_problems = FALSE;
$checker_check_size = FALSE;
$checker_check_hash = FALSE;
$checker_check_thumbs = FALSE;




// STORAGE SECTION
$storage_1 = array(
	'upload_url' => '/upload_1',
	'name' => 'upload_1',
	'device' => '/dev/sdc1',
	'mount_point' => '/var/upload/1',
	'prio' => 5,
	'disabled' => 0,
	'hash' => array('1')
);

$storage_2 = array(
	'upload_url' => '/upload_2',
	'name' => 'upload_2',
	'device' => '/dev/sdd1',
	'mount_point' => '/var/upload/2',
	'prio' => 5,
	'disabled' => 0,
	'hash' => array('2')
);

$storage_3 = array(
	'upload_url' => '/upload_3',
	'name' => 'upload_3',
	'device' => '/dev/sda1',
	'mount_point' => '/var/upload/3',
	'prio' => 5,
	'disabled' => 0,
	'hash' => array('3')
);


$storage_4 = array(
	'upload_url' => '/upload_4',
	'name' => 'upload_4',
	'device' => '/dev/sdb1',
	'mount_point' => '/var/upload/4',
	'prio' => 5,
	'disabled' => 0,
	'hash' => array('4')
);


$storage_5 = array(
	'upload_url' => '/upload_5',
	'name' => 'upload_5',
	'device' => '/dev/sdf1',
	'mount_point' => '/var/upload/5',
	'prio' => 5,
	'disabled' => 0,
	'hash' => array('5')
);



define('UP', 1);

?>
