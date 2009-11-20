<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require UP_ROOT.'functions.inc.php';

try {
	$db = new DB;

	//	1. Realy remove deleted files from filesystem
	$datas = $db->getData("SELECT id,sub_location,location,deleted_date FROM up WHERE deleted='1' AND deleted_date < NOW()-INTERVAL $undelete_interval DAY");

	if ($datas) {
		clearstatcache();

		foreach ($datas as $rec) {
			$file = $upload_dir.$rec['sub_location'].'/'.$rec['location'];
			safeUnlink($file);

			// REMOVE THUMBS
			$small_thumb = $server_root.'thumbs/'.sha1($rec['id']).'.jpg';
			safeUnlink($small_thumb);

			$large_thumb = $server_root.'thumbs/large/'.sha1($rec['id']).'.jpg';
			safeUnlink($large_thumb);
		}

		unset($datas);
	}


// 2.
	$datas = $db->getData("SELECT *, DATEDIFF(NOW(), GREATEST(last_downloaded_date,uploaded_date)) as NDI FROM up WHERE deleted='0'");
	if ($datas) {
		array_walk($datas, 'remove_files_callback');
	}
} catch (Exception $e) {
	$log = new Logger;
	$log->error("Сбой модуля очистки: ".$e->getMessage());
}

function remove_files_callback($item, $key) {
	global $db;
	$item_id = intval($item['id']);
	$size = $item['size'];
	$ndi = $item['NDI'];
	$downloaded = $item['downloads'];
	$is_spam = $item['spam'];
	$reason = "файл удалён роботом Wall-E";

	$wakkamakka = get_time_of_die($size, $downloaded, $ndi, $is_spam);

	if (($wakkamakka < 1) && ($item_id > 0)) {
		$db->query("UPDATE up SET deleted='1', deleted_reason=?, deleted_date=NOW() WHERE id=?", $reason, $item_id);
	}
}

?>
