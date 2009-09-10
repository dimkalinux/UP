<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
require 'include/PasswordHash.php';

// item
if (!isset ($_GET['item']) || !isset ($_GET['magic'])) {
	header('Location: /404.html');
	exit();
}

$user_ip = get_client_ip();
$item_id = intval(get_get('item'), 10);
$geo = get_geo($user_ip);
$magic = get_get('magic');
$db = new DB;

$row = $db->getRow("SELECT * FROM up WHERE id=? LIMIT 1", $item_id);
if (!$row) {
	header('Location: /404.html');
	exit();
}


$cache = new Cache;
$dlmKey = 'dlm'.$item_id.ip2long($user_ip);
$dlmValue = $cache->get($dlmKey);
if ($dlmValue != $magic) {
	$unuiq = uniqid();
	show_error_message("Сcылка не&nbsp;верна или устарела.<br/>
	Чтобы обновить ссылку перейдите по&nbsp;адресу <a href=\"/$item_id/?$unuiq\">http://up.lluga.net/$item_id/?$unuiq</a>.");
}

// password?
if (mb_strlen($row['password']) > 0) {
	$t_hasher = new PasswordHash(8, FALSE);
	if (!isset($_POST['password']) || !$t_hasher->CheckPassword($_POST['password'], $row['password'])) {
		show_error_message("Для доступа к&nbsp;файлу требуется ввести верный пароль.");
	}
}

// download
$filename = check_plain($row['filename']);
$filesize = $row['size'];
$filemime = $row['mime'];
$is_spam = (bool) $row['spam'];
$is_adult = (bool) $row['adult'];
$is_hidden = (bool) $row['hidden'];

$file_location_on_server = $GLOBALS['upload_dir'].$row['sub_location'].'/'.$row['location'];

// antiflood
$floodKey = 'df'.$user_ip.$item_id;
$floodCounter = $cache->inc($floodKey, 10);
if ($floodCounter === false) {
	$floodCounter = 1;
}

$maxFlood = 3;
if (($geo === 'lds') || ($geo === 'world')) {
	$maxFlood = 2;
}

$is_flood = (bool) $floodCounter > $maxFlood;

if ($is_flood) {
	header('HTTP/1.1 503 Service Temporarily Unavailable');
	header('Status: 503 Service Temporarily Unavailable');
	header('Retry-After: 10');

	if (isset($_SERVER['HTTP_REFERER'])) {
		header('Location: /503.html');
	}
	exit();
}

$path = '/'.$row['sub_location'].'/'.$row['location'];
if (strlen ($filemime) != 0) {
	header("Content-Type: ".$filemime);
} else {
	header("Content-Type: application/octet-stream");
}

header("Content-Disposition: attachment; filename=\"$filename\"");
header("X-Accel-Buffering: yes");
header("X-Accel-Redirect: /files/".$path);

/*$log = new Logger;
$log->info("download: '$item_id', geo: '$geo'");*/

// simple antiflood counter
if (!$is_flood && !$is_spam && !isset($_SERVER['HTTP_RANGE'])) {
	// update download-counter ONLY for localnet
	if (($geo === 'lds') || ($geo === 'lluga')) {
		$db->query("UPDATE up SET last_downloaded_date=NOW(), downloads=downloads+1 WHERE id=? LIMIT 1", $item_id);

		if (!$is_adult && !$is_hidden) {
			$db->query("DELETE FROM dnow WHERE ld < (NOW() - INTERVAL 2 HOUR)");
			$db->query("INSERT INTO dnow VALUES (?, NOW(), 1, 'down') ON DUPLICATE KEY UPDATE ld=NOW(), n=n+1", $item_id);
		}
	}
}

header('Location: http://up.lluga.net/'.$item_id.'/?'.uniqid());
exit();
?>
