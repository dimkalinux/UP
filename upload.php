<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';
require UP_ROOT.'include/PasswordHash.php';
require UP_ROOT.'include/upload.inc.php';

define('UPLOAD_NO_ERROR', 0);
define('UPLOAD_ERROR_EMPTY_FILE', 8);
define('UPLOAD_ERROR_FOUND_VIRUS', 1);
define('UPLOAD_ERROR_SAVE', 2);
define('UPLOAD_ERROR_MAX_SIZE', 3);
define('UPLOAD_ERROR_SERVER_FAIL', 4);
define('UPLOAD_ERROR_FLOOD', 5);
define('UPLOAD_ERROR_NO_FILE', 6);
define('UPLOAD_ERROR_STORAGE', 7);


// DEFAULT ERROR
$error = UPLOAD_NO_ERROR;
$item_id = -1;
$owner_id = 0;
$log = $owner_key = $message = $add_error_message = null;
$file = $_POST;
$is_web = isset($_POST['progress_id']);

// ERRORS TEXT MESSAGE
$a_err_msg = array(
	'',
	'файл заражён вирусом',											// 1
	'сбой при сохранении файла',									// 2
	'превышен максимально разрешенный размер загружаемого файла',	// 3
	'сбой при обработке файла',										// 4
	'сработала зашита от флуда',									// 5
	'получен запрос без файла',										// 6
	'ошибка в системе хранения файлов',								// 7
	'получен пустой файл',											// 0
	);


try {
	// CHECK ALL REQUIRED FILE ATTRS
	$file_attrs = array('file_path', 'file_name', 'file_ip', 'file_storage_name', 'file_size');
	foreach ($file_attrs as $fa) {
		if (!isset($file[$fa])) {
			$add_error_message = "'$fa' is empty";
			throw new Exception(UPLOAD_ERROR_SERVER_FAIL);
		}
	}


	// ANTIFLOOD
	$up_file_ip = $file['file_ip'];
	if ($up_file_ip && $is_web) {
		$cache = new Cache;
		$floodCounter = $cache->inc('uf'.$up_file_ip, 120);
		if ($floodCounter === false) {
			$floodCounter = 1;
		}
	}


	// CHECK MIN FILESIZE
	if ($file['file_size'] < 1) {
		$add_error_message .= 'размер: '.$file['file_size'].' имя: '.$file['file_name'];
		throw new Exception(UPLOAD_ERROR_EMPTY_FILE);
	}

	// CHECK MAX FILESIZE
	if ($file['file_size'] > ($max_file_size*1048576)) {
		throw new Exception(UPLOAD_ERROR_MAX_SIZE);
	}


	$Upload = new Upload;
	$uploadfilename = $Upload->generateFilename($upload_dir, 10, $file['file_size']);

	$storage = new Storage;
	$subfolder = $storage->get_upload_subdir($file['file_storage_name']);

	$uploadfile = $upload_dir.$subfolder.'/'.basename($uploadfilename);
	$owner_key = mt_rand();
	$up_file_name = $file['file_name'];
	$up_file_size = $file['file_size'];
	$is_spam = is_spam($file['file_name'], $file['file_size']);
	$is_adult = is_adult($file['file_name'], $file['file_size']);
	$hidden = isset($_POST['uploadHidden']) && $_POST['uploadHidden'] == 1;
	$filepath = $file['file_path'];

	// get filename for FUSE
	if (!$user['is_guest']) {
		$up_file_name_fuse = $Upload->getFilenameForFUSE($up_file_name, $user['id']);
	} else {
		$up_file_name_fuse = $up_file_name;
	}

	// PASSWORD
	$password = '';
	if (isset($_POST['uploadPassword']) && (mb_strlen($_POST['uploadPassword']) > 0)) {
		$t_hasher = new PasswordHash(8, FALSE);
		$password = $t_hasher->HashPassword($_POST['uploadPassword']);
	}

	// mime
	$up_file_mime = $file['file_content_type'];
	if (empty($up_file_mime)) {
		$up_file_mime = $Upload->createMIME(get_file_ext($file['file_name']));
	}

	// rename file (move) USE LINK
	if (!link($filepath, $uploadfile)) {
		$add_error_message = "filepath: '$filepath' uploadfile: '$uploadfile'";
		throw new Exception(UPLOAD_ERROR_SAVE);
	} else {
		if (file_exists($file['file_path'])) {
			unlink($file['file_path']);
		}
	}


	$db = new DB;
	$db->query("INSERT INTO up VALUES('', ?, ?, NOW(), '', ?, ?, ?, ?, ?, ?, ?, '0', ?, '0', '', '', '', ?, ?, ?, ?)",
		$password, $owner_key, $up_file_ip, $uploadfilename, $subfolder, $up_file_name, $up_file_name_fuse, $up_file_mime, $up_file_size, ANTIVIR_NOT_CHECKED, $is_spam, $is_adult, $hidden, $user['id']);

	// get ITEM_ID
	$item_id = $db->lastID();


	// COMMENT/DESCRIPTION
	if (isset($_POST['uploadDesc']) && mb_strlen(trim($_POST['uploadDesc']) > 1)) {
		$comments = new Comments($item_id, $user['id']);
		$comments->addComment($_POST['uploadDesc']);
	}

	// dont add BAD files to DNOW
	if (!$is_adult && !$is_spam && !$hidden) {
		$db->query("DELETE FROM dnow WHERE ld < (NOW() - INTERVAL 24 HOUR)");
		$db->query("INSERT INTO dnow VALUES (?, NOW(), 1, 'up') ON DUPLICATE KEY UPDATE n=n+1", $item_id);
	}

	// update counters
	if (!$user['is_guest']) {
		User::updateUploadsCounters($user['id'], 1, $up_file_size);
	}

	// CREATE THUMBS
	if (empty($password)) {
		$Upload->generateThumbs($uploadfile, $up_file_name, $item_id);
	}

	// clear stat cache
	clear_stat_cache();
	$error = 0;
} catch (Exception $e) {
	if (is_numeric($e->getMessage())) {
		$error = $e->getMessage();
	} else {
		$add_error_message = $e->getMessage();
	}

	if (isset($uploadfile) && file_exists($uploadfile)) {
		unlink($uploadfile);
	}

	if (isset($file['file_path']) && file_exists($file['file_path'])) {
		unlink($file['file_path']);
	}

	if (isset($a_err_msg[$error])) {
		$message = $a_err_msg[$error];
	}
	$message .= ' '.$add_error_message;

	// LOG ERROR
	$log = new Logger;
	$log->error("Сбой загрузки. '$message'");

	exit(json_encode(array('error'=>$error, 'id'=>$item_id, 'pass'=>$owner_key, 'message'=>$message)));
}

exit(json_encode(array('error'=>$error, 'id'=>$item_id, 'pass'=>$owner_key, 'message'=>$message)));

?>

