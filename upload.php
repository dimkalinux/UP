<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';
require UP_ROOT.'include/PasswordHash.php';

define('UPLOAD_ERROR_FOUND_VIRUS', 1);
define('UPLOAD_ERROR_SAVE', 2);
define('UPLOAD_ERROR_MAX_SIZE', 3);
define('UPLOAD_ERROR_SERVER_FAIL', 4);
define('UPLOAD_ERROR_FLOOD', 5);
define('UPLOAD_ERROR_NO_FILE', 6);
define('UPLOAD_ERROR_STORAGE', 7);
define('UPLOAD_ERROR_EMPTY_FILE', 8);


$log = null;
$error = UPLOAD_ERROR_SERVER_FAIL;
$item_id = -1;
$owner_id = 0;
$item_pass = $message = $add_error_message = null;
$file = $_POST;
$is_web = isset($_POST['progress_id']);

// errors text message
$a_err_msg = array("",
	"файл заражён вирусом",											// 1
	"сбой при сохранении файла",									// 2
	"превышен максимально разрешенный размер загружаемого файла",	// 3
	"сбой при обработке файла",										// 4
	"сработала зашита от флуда",									// 5
	"получен запрос без файла",										// 6
	'ошибка в системе хранения файлов',								// 7
	'получен пустой файл');											// 8



// real start here
do {
	// check all required file attrs
	$file_attrs = array('file_path', 'file_name', 'file_ip', 'file_storage_name', 'file_size');
	foreach ($file_attrs as $fa) {
		if (!isset($file[$fa])) {
			$error = UPLOAD_ERROR_SERVER_FAIL;
			$add_error_message = "'$fa' is empty";
			break;
		}
	}


	// antiflood
	$ip = $file['file_ip'];
	if ($ip && $is_web) {
		$cache = new Cache;
		$floodKey = 'uf'.$ip;
		$floodCounter = $cache->inc($floodKey, 120);
		if ($floodCounter === false) {
			$floodCounter == 1;
		}

		if ($floodCounter === 100) {
			$error = UPLOAD_ERROR_FLOOD;
			break;
		}
	}


	// check min filesize
	if ($file['file_size'] < 1) {
		$error = UPLOAD_ERROR_EMPTY_FILE;

		$add_error_message .= ' размер: '.$file['file_size'];
		$add_error_message .= ' имя: '.$file['file_name'];
		break;
	}

	// check max filesize
	if ($file['file_size'] > ($max_file_size*1048576)) {
		$error = UPLOAD_ERROR_MAX_SIZE;
		break;
	}

	// start process file
	$Upload = new Upload;
	$uploadfilename = $Upload->generateFilename($upload_dir, 10, $file['file_size']);

	try {
		$storage = new Storage;
		$subfolder = $storage->get_upload_subdir($file['file_storage_name']);
	} catch (Exception $e) {
		if (!$log) {
			$log = new Logger;
		}
		$log->error("Upload file: ".$e->getMessage());
		$error = UPLOAD_ERROR_STORAGE;
	}


	$uploadfile = $upload_dir.$subfolder.'/'.basename($uploadfilename);
	$item_pass = mt_rand();
	$up_file_name = $file['file_name'];
	$up_file_size = $file['file_size'];
	$md5 = (isset($file['file_md5'])) ? $file['file_md5'] : '';
	$is_spam = is_spam($file['file_name']);
	$is_adult = is_adult($file['file_name']);
	$hidden = isset($_POST['uploadHidden']) && $_POST['uploadHidden'] == 1;

	$desc = '';
	if (isset($_POST['uploadDesc'])) {
		$desc = check_plain(mb_substr($_POST['uploadDesc'], 0, 512));
	}

	// password
	$password = '';
	if (isset($_POST['uploadPassword']) && (mb_strlen($_POST['uploadPassword']) > 0)) {
		$t_hasher = new PasswordHash(8, FALSE);
		$password = $t_hasher->HashPassword($_POST['uploadPassword']);
	}

	// mime
	$up_file_mime = $file['file_content_type'];
	if (mb_strlen($up_file_mime) == 0) {
		$up_file_mime = $Upload->createMIME(get_file_ext($file['file_name']));
	}

	// rename file (move)
	$ret = -1;
	$output = null;
	$filepath = escapeshellcmd($file['file_path']);
	exec("/bin/mv -f '$filepath' '$uploadfile'", $output, $ret);
	if ($ret != 0) {
		$error = UPLOAD_ERROR_SAVE;
		$_udir = $upload_dir.$subfolder;
		$_udir_size = disk_free_space($_udir);

		$add_error_message = <<<ZZZ
ret: "$ret" filepath: "$filepath" uploadfile: "$uploadfile" free space: "$_udir_size" filesize: "$up_file_size"
ZZZ;
		break;
	}

	// add to DB
	try {
		$db = new DB;
		$db->query("INSERT INTO up VALUES('', ?, ?, NOW(), '', ?, ?, ?, ?, ?, ?, '0', '0', '0', '', '', ?, ?, ?, ?, ?, ?)",
			$password, $item_pass, $ip, $uploadfilename, $subfolder, $up_file_name, $up_file_mime, $up_file_size, $md5, $desc, $is_spam, $is_adult, $hidden, $user['id']);

		// dont add BAD files to DNOW
		if (!$is_adult && !$is_spam && !$hidden) {
			$db->query("DELETE from dnow WHERE ld < (NOW() - INTERVAL 24 HOUR)");
			$db->query("INSERT INTO dnow VALUES (?, NOW(), 1, 'up') ON DUPLICATE KEY UPDATE n=n+1", $item_id);
		}

		// update counters
		if (!$user['is_guest']) {
			User::updateUploadsCounters($user['id'], 1, $up_file_size);
		}
	} catch (Exception $e) {
		$error = UPLOAD_ERROR_SERVER_FAIL;
		$add_error_message = $e->getMessage();
		$add_error_message .= ' размер: '.$file['file_size'];
		$add_error_message .= ' имя: '.$file['file_name'];

		if (isset($uploadfile)) {
			if (is_file($uploadfile)) {
				unlink($uploadfile);
			}
		}

		if (isset($file['file_path'])) {
			if (is_file($file['file_path'])) {
				unlink ($file['file_path']);
			}
		}
	}

	if (is_file($uploadfile) && is_image($up_file_name, $uploadfile) && $password == '') {
		$key_name = md5($md5.$item_id);
		$thumbs_filename = 'thumbs/'.$key_name.'.jpg';
		$thumbs_preview_filename = 'thumbs/large/'.$key_name.'.jpg';
		$thumbs_original_filename = 'thumbs/original/'.$key_name.'.jpg';

		if (!@create_thumbs($uploadfile, $thumbs_filename, $thumbs_preview_filename)) {
			if (!$log) {
				$log = new Logger;
			}
			$log->error("создание миниатюры для графического файла. ID: '$item_id'");
		}

		// make link to original
		//symlink($uploadfile, $thumbs_original_filename);
	}

	// clear stat cache
	clear_stat_cache ();
	$error = 0;
} while (0);

// error
if ($error != 0) {
	if (isset($uploadfile)) {
		// unlink file (uploaded)
		if (is_file($uploadfile))
			unlink($uploadfile);
	}

	if (isset($file['file_path'])) {
		if (is_file($file['file_path']))
			unlink ($file['file_path']);
	}

	$message = $a_err_msg[$error];
	$message .= ' '.$add_error_message;

	if (!$log) {
		$log = new Logger;
	}
	$log->error("загрузкa файла. '$message'");
} else {
	$message = "OK";
}

$result = array('error'=> $error, 'id'=> $item_id, 'group' => $group_id, 'pass' => $item_pass, 'message' => $message);
exit(json_encode($result));

?>

