<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'header.php';

// admin js function
$addScript = null;
$admin_th_row = '';
$admin_td_row = '';
$admin_actions_block = '';
if ($admin = is_admin()) {
	$admin_th_row = '<th class="first center"><input id="allCB" type="checkbox"/></th>';
	$admin_actions_block = '<input type="button" value="not SPAM" onmousedown="UP.admin.unmarkItemSpam(false);" disabled="disabled" />';
	$addScript[] = 'up.admin.js';
}

$db = new DB;
$datas = $db->getData("SELECT * FROM up WHERE deleted='0' AND hidden='0' AND spam='1' ORDER BY uploaded_date DESC");

if ($datas) {
	$out = <<<ZZZ
		<div id="status">&nbsp;</div>
		<h2>Спам</h2>
		$admin_actions_block
		<table class="t1" id="search_files_table">
		<thead>
		<tr>
			$admin_th_row
			<th class="first right">Размер</th>
			<th class="left">Имя</th>
			<th class="center">ip</th>
			<th class="center">Скачан</th>
			<th class="last right">Время</th>
		</tr>
		</thead>
		<tbody>
ZZZ;
	echo($out);

	array_walk($datas, 'print_files_list_callback', $admin);

	$out = <<<ZZZ
		</tbody>
		</table>
ZZZ;
	$onDOMReady = 'UP.admin.cbStuffStart();';
} else {
	$out = '<div id="status">&nbsp;</div><h2>Спам</h2><p>Отсутствует.</p>';
}
echo($out);
require UP_ROOT.'footer.php';
exit();


function print_files_list_callback($item, $key, $admin) {
	$item_id = intval($item['id'], 10);
	$filename = get_cool_and_short_filename($item['filename'], 40);
	$filesize_text = format_filesize($item['size']);
	$ip = $item['ip'];
	$downloaded = $item['downloads'];
	$file_date = prettyDate($item['uploaded_date']);

	if ($admin) {
		$admin_td_row = '<td class="center"><input type="checkbox" value="1" id="item_cb_'.$item_id.'"/></td>';
	} else {
		$admin_td_row = '';
	}


	$out = <<<ZZZ
		<tr id="row_item_$item_id">
			$admin_td_row
			<td class="right">$filesize_text</td>
			<td class="left"><a href="/$item_id/">$filename</a></td>
			<td class="center">$ip</td>
			<td class="center">$downloaded</td>
			<td class="right">$file_date</td>
		</tr>
ZZZ;
	echo ($out);
}

?>
