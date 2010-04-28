<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';

$bytes = intval($_REQUEST['bs'], 10);
$geo = $_REQUEST['geo'];
$uri = $_REQUEST['uri'];

list(,,$item_id,) = split('/', $uri);
$item_id = intval($item_id, 10);

// get info
try {
	if ($item_id < 1) {
		throw new Exception("item_id < 1: $uri");
	}

	$db = DB::singleton();
	$row = $db->getRow("SELECT size,adult,hidden FROM up WHERE id=? LIMIT 1", $item_id);
	$db_size = intval($row['size'], 10);
	$is_adult = (bool) $row['adult'];
	$is_hidden = (bool) $row['hidden'];

	if ($db_size !== $bytes) {
		return;
	}

	$rec = $db->getRow("SELECT COUNT(*) AS dc FROM downloads WHERE (date > (NOW()-INTERVAL 1 WEEK)) and item_id=?", $item_id);
	$numHotDownloads = intval($rec['dc'], 10);

	if (($geo === 'lds') || ($geo === 'iteam')) {
		$db->query("UPDATE up SET last_downloaded_date=NOW(), downloads=downloads+1, hot_downloads=? WHERE id=? LIMIT 1", $numHotDownloads, $item_id);
			// Update downloads table
		$db->query("INSERT INTO downloads VALUES(?, NOW())", $item_id);
	}

	if (!$is_adult && !$is_hidden) {
		$db->query("DELETE FROM dnow WHERE ld < (NOW() - INTERVAL 2 HOUR)");
		$db->query("INSERT INTO dnow VALUES(?, NOW(), 1, 'down') ON DUPLICATE KEY UPDATE ld=NOW(), n=n+1", $item_id);
	}
} catch(Exception $e) {
	$log = new Logger;
	$log->error("After download error: ".$e->getMessage());
}

?>
