<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';


$err = 0;
$errMsg = "&nbsp;";
$statusType = 'default';
$myFiles = '';
$onDOMReady = 'UP.userFiles.cbStuffStart();';


$out = <<<ZZZ
	<div id="status"><span type="$statusType">$errMsg</span></div>
	<h2>Мои файлы</h2>
ZZZ;

if ($user['is_guest']) {
	$out = <<<ZZZ
	<div id="status"><span type="$statusType">$errMsg</span></div>
	<h2>Ошибка</h2>
	<p>Для доступа к этой странице необходимо <a href="/login/">войти в систему</a>.</p>
ZZZ;
} else {
	$myFiles = User::getUserFiles($user['id']);
}

require UP_ROOT.'header.php';
echo($out.$myFiles);

require UP_ROOT.'footer.php';
?>
