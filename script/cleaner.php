<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require UP_ROOT.'functions.inc.php';

$numDeletedFromStorage = 0;
$numDeletedFromDB = 0;
$numNotExistsFiles = 0;
$numNotDeletedFiles = 0;

try {
	$db = new DB;
	$datas = $db->getData("SELECT id,sub_location,location,deleted_date FROM up WHERE deleted='1' AND deleted_date < NOW()-INTERVAL $undelete_interval DAY");

	if ($datas) {
		clearstatcache();

		foreach ($datas as $rec) {
			$file = $upload_dir.$rec['sub_location'].'/'.$rec['location'];
			// TRY to REMOVE FILE
			if (file_exists($file)) {
				if (!unlink($file)) {
					$numNotDeletedFiles++;
				} else {
					$numDeletedFromStorage++;
				}
			} else {
				$numNotExistsFiles++;
			}

			// REMOVE THUMBS
			$small_thumb = $server_root.'thumbs/'.sha1($rec['id']).'.jpg';
			safeUnlink($small_thumb);

			$large_thumb = $server_root.'thumbs/large/'.sha1($rec['id']).'.jpg';
			safeUnlink($large_thumb);
		}

		unset($datas);
	}

	$datas = $db->getData("SELECT *, DATEDIFF(NOW(), GREATEST(last_downloaded_date,uploaded_date)) as NDI FROM up WHERE deleted='0'");
	if ($datas) {
		array_walk($datas, 'remove_files_callback');
	}
} catch (Exception $e) {
	$log = new Logger;
	$log->error("Cleaner: ".$e->getMessage());
}

// LOG REPORT
$log = new Logger;
$log->info("Cleaner: from storage: $numDeletedFromStorage, from DB: $numDeletedFromDB");

// LOG NON-CRITICAL ERRORS
if (($numNotExistsFiles != 0) || ($numNotDeletedFiles)) {
	$log->error("Cleaner: not exists: $numNotExistsFiles, not deleted: $numNotDeletedFiles");
}


clear_stat_cache ();
exit();


function remove_files_callback($item, $key) {
	global $db, $numDeletedFromDB;
	$item_id = intval($item['id'], 10);
	$size = $item['size'];
	$ndi = $item['NDI'];
	$downloaded = $item['downloads'];
	$is_spam = $item['spam'];
	$reason = "файл удалён роботом Wall-E";

	$wakkamakka = get_time_of_die($size, $downloaded, $ndi, $is_spam);

	if (($wakkamakka < 1) && ($item_id > 0)) {
		$db->query("UPDATE up SET deleted='1', deleted_reason=?, deleted_date=NOW() WHERE id=?", $reason, $item_id);
		$numDeletedFromDB++;
	}
}

?>
