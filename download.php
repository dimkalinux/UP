<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


// item
if (!isset ($_GET['item']) || !isset ($_GET['magic'])) {
	httpError404();
}

$item_id = intval($_GET['item'], 10);
$geo = get_geo();
$magic = $_GET['magic'];

try {
	$db = DB::singleton();
	$row = $db->getRow("SELECT * FROM up WHERE id=? LIMIT 1", $item_id);
} catch (Exception $e) {
	httpError404();
}

if (!$row) {
	httpError404();
}

$cache = new Cache;
$dlmValue = $cache->get('dlm'.$item_id.ip2long($user['ip']));
if ($dlmValue != $magic) {
	$unuiq = uniqid();
	show_error_message("Сcылка для скачивания не&nbsp;верна или устарела.<br/>
	Чтобы обновить ссылку перейдите по&nbsp;адресу <a href=\"{$base_url}{$item_id}/?$unuiq\">{$base_url}{$item_id}/?$unuiq</a>.");
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
$file_location_on_server = $GLOBALS['upload_dir'].$row['sub_location'].'/'.$row['location'];
$path = '/'.$row['sub_location'].'/'.$row['location'];

// check is file fysicaly exists on server
if (!file_exists($file_location_on_server)) {
	show_error_message("Файл временно недоступен.<br/>Попробуйте скачать его немного позже.");
}

if (mb_strlen($filemime) !== 0) {
	header("Content-Type: ".$filemime);
} else {
	header("Content-Type: application/octet-stream");
}

header("Content-Disposition: attachment; filename=\"$filename\"");
header("X-Accel-Buffering: yes");
header("X-Accel-Redirect: /files/".$path);
exit();

?>
