<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require UP_ROOT.'functions.inc.php';

$db = new DB;

//	1. Realy remove deleted files from filesystem
$datas = $db->getData("SELECT id,sub_location,location,deleted_date FROM up
		WHERE deleted='1' AND deleted_date < NOW()-interval $undelete_interval day");

if ($datas) {
	// clear disk cache
	clearstatcache();

	foreach ($datas as $rec) {
		$file = $GLOBALS['upload_dir'].$rec['sub_location'].'/'.$rec['location'];
		if (file_exists($file)) {
			unlink($file);
		}

		$thumbs = 'thumbs/'.md5($rec['location']).'.png';
		if (is_file($thumbs)) {
			unlink ($thumbs);
		}
	}

	unset($datas);
}


// 2.
$datas = $db->getData("SELECT *,
	DATEDIFF(NOW(), GREATEST(last_downloaded_date,uploaded_date)) as NDI
	FROM up
	WHERE deleted='0'");

@array_walk($datas, 'remove_files_callback');


// clear stat cache
clear_stat_cache ();
exit();


function remove_files_callback($item, $key) {
	global $db;
	$item_id = intval ($item['id']);
	$size = $item['size'];
	$ndi = $item['NDI'];
	$downloaded = $item['downloads'];
	$is_spam = $item['spam'];
	$reason = "файл удалён роботом Wall-E";


	$wakkamakka = get_time_of_die($size, $downloaded, $ndi, $is_spam);

	if (($wakkamakka < 1) && ($item_id > 0)) {
		if (!$db->query("UPDATE up SET deleted='1', deleted_reason=?, deleted_date=NOW() WHERE id=?", $reason, $item_id)) {
			die('DB error');
		}
	}
}

?>
