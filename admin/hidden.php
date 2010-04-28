<?
define('ADMIN_PAGE', 1);

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require UP_ROOT.'functions.inc.php';

$addScript[] = 'up.admin.js';

try {
	$db = DB::singleton();
	$datas = $db->getData("SELECT * FROM up WHERE deleted='0' AND hidden='1' ORDER BY id DESC LIMIT 100");
} catch(Exception $e) {
	error($e->getMessage());
}


if ($datas) {
	$out = <<<FMB
		<div id="status">&nbsp;</div>
		<h2>Скрытые <em>(100 последних)</em></h2>
		<table class="t1" id="search_files_table">
		<thead>
		<tr>
			<th class="noborder" colspan="2"></th>
			<th class="left noborder">
			<div class="controlButtonsBlock">
			<button type="button" class="btn" disabled="disabled" onmousedown="UP.admin.unHideItem(false);"><span><span>открыть</span></span></button></div></th>
			<th class="right noborder" id="pageLinks" colspan="2"></th>
		</tr>
		<tr>
			<th class="center"><input id="allCB" type="checkbox"/></th>
			<th class="size">Размер</th>
			<th class="name">Имя</th>
			<th class="download">Скачан</th>
			<th class="time">Время</th>
		</tr>
		</thead>
		<tbody>
FMB;

	array_walk($datas, 'print_files_list_callback', &$out);

	$out .= '</tbody></table>';
	$onDOMReady = 'UP.admin.cbStuffStart();';
} else {
	$out = '<div id="status">&nbsp;</div><h2>Скрытые</h2><p>Отсутствует.</p>';
}

printPage($out);
exit();


function print_files_list_callback($item, $key, $out) {
	global $user, $base_url;

	$item_id = intval($item['id'], 10);
	$fullFilename = htmlspecialchars_decode(stripslashes($item['filename']));
	$filename = get_cool_and_short_filename($fullFilename, 55);
	$filenameTitle = '';
	if (5 < (mb_strlen($fullFilename) - mb_strlen($filename))) {
		$filenameTitle = 'title="Полное имя: '.$fullFilename.'"';
	}
	$filesize_text = format_filesize($item['size']);
	$downloaded = $item['downloads'];
	$file_date = prettyDate($item['uploaded_date']);

	$out .= <<<FMB
		<tr id="row_item_$item_id" class="row_item">
			<td class="center"><input type="checkbox" value="1" id="item_cb_$item_id"/></td>
			<td class="size">$filesize_text</td>
			<td class="name"><a href="{$base_url}$item_id/">$filename</a></td>
			<td class="download">$downloaded</td>
			<td class="time">$file_date</td>
		</tr>
FMB;
}

?>
