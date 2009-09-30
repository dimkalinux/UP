<?
define('ADMIN_PAGE', 1);

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require UP_ROOT.'functions.inc.php';

// admin js function
$admin_th_row = '<th class="first center"><input id="allCB" type="checkbox"/></th>';
$admin_actions_block = '<input type="button" value="not adult" onmousedown="UP.admin.unmarkItemAdult(false);" disabled="disabled" />';
$addScript[] = 'up.admin.js';

try {
	$db = new DB;
	$datas = $db->getData("SELECT * FROM up WHERE deleted='0' AND hidden='0' AND adult='1' ORDER BY uploaded_date DESC");
} catch (Exception $e) {
	error($e->getMessage());
}


if ($datas) {
	$out = <<<ZZZ
		<div id="status">&nbsp;</div>
		<h2>Adult</h2>
		$admin_actions_block
		<table class="t1" id="search_files_table">
		<thead>
		<tr>
			$admin_th_row
			<th class="right">Размер</th>
			<th class="left">Имя</th>
			<th class="left">ip</th>
			<th class="center">Скачан</th>
			<th class="right">Время</th>
		</tr>
		</thead>
		<tbody>
ZZZ;

	array_walk($datas, 'print_files_list_callback', $admin, $out);

	$out .= <<<ZZZ
		</tbody>
		</table>
ZZZ;
	$onDOMReady = 'UP.admin.cbStuffStart();';
} else {
	$out = '<div id="status">&nbsp;</div><h2>Adult</h2><p>Отсутствует.</p>';
}


require UP_ROOT.'header.php';
echo($out);
require UP_ROOT.'footer.php';
exit();


function print_files_list_callback ($item, $key, $admin, &$out) {
	$item_id = intval ($item['id']);
	$filename = get_cool_and_short_filename ($item['filename'], 40);
	$filesize_text = format_filesize ($item['size']);
	$downloaded = $item['downloads'];
	$ip = $item['ip'];
	$file_date = prettyDate($item['uploaded_date']);

	$admin_td_row = '';
	if ($admin) {
		$admin_td_row = '<td class="center"><input type="checkbox" value="1" id="item_cb_'.$item_id.'"/></td>';
	}


	$out .= <<<FMB
		<tr id="row_item_$item_id">
			$admin_td_row
			<td class="right">$filesize_text</td>
			<td class="left"><a href="/$item_id/">$filename</a></td>
			<td class="center">$ip</td>
			<td class="center">$downloaded</td>
			<td class="right">$file_date</td>
		</tr>
FMB;
}

?>
