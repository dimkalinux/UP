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

if ($item_id < 1) {
	$log = new Logger;
	$log->error("After download item_id < 1: $uri");
	return;
}

// get info
try {
	$db = new DB;
	$row = $db->getRow("SELECT size FROM up WHERE id=? LIMIT 1", $item_id);
	$db_size = intval($row['size'], 10);

	if ($db_size !== $bytes) {
		return;
	}

	if (($geo === 'lds') || ($geo === 'iteam')) {
		$db->query("UPDATE up SET last_downloaded_date=NOW(), downloads=downloads+1 WHERE id=? LIMIT 1", $item_id);
	}
} catch(Exception $e) {
	$log = new Logger;
	$log->error("After download error: ".$e->getMessage());
}

?>
