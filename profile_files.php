<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';


$err = 0;
$errMsg = "&nbsp;";
$statusType = 'default';
$myFiles = '';
$onDOMReady = 'UP.userFiles.cbStuffStart(); $(document).everyTime("10s", "updateFilesTimer", UP.userFiles.getUpdatedItems);';


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

if (!empty($myFiles)) {
	$myFiles = '
	<table class="t1" id="top_files_table">
	<thead>
	<tr>
		<th colspan="2" class="noborder"></th>
		<th class="noborder">
			<div class="controlButtonsBlock">
				<button type="button" class="btn" disabled="disabled" onmousedown="UP.userFiles.deleteItem();"><span><span>удалить</span></span></button>
			</div>
		</th>
	</tr>
	<th colspan="2" class="noborder"></th>
	<tr>
		<th class="center checkbox"><input type="checkbox" id="allCB"/></th>
		<th class="size">Размер</th>
		<th class="name">Имя файла</th>
		<th class="download">Скачан</th>
		<th class="time">Срок</th>
	</tr>
	</thead>
	<tbody>'.$myFiles.'</tbody></table>';
}

require UP_ROOT.'header.php';
echo($out.$myFiles);

require UP_ROOT.'footer.php';
?>
