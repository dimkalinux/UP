<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', '.././');
}
require UP_ROOT.'functions.inc.php';


$admin = is_admin();
if (!$admin) {
	show_error_message('Доступ к журналу событий защищён зарослями фиалок и лютиков.');
	exit();
}

// admin js function
$admin_th_row = '<th class="first center"><input id="allCB" type="checkbox"/></th>';
$admin_actions_block = '<input type="button" value="delete row" onmousedown="UP.admin.clearLogRow(false);" disabled="disabled"/>
	<input type="button" value="clear logs" onmousedown="UP.admin.clearLogAll();"/>';
$addScript[] = 'up.admin.js';

$db = new DB;
$datas = $db->getData("SELECT * FROM logs ORDER BY date DESC LIMIT 100");

if ($datas) {
	$out = <<<ZZZ
		<div id="status">&nbsp;</div>
		<h2>Журнал событий</h2>
		$admin_actions_block
		<table class="t1" id="search_files_table">
		<thead>
		<tr>
			$admin_th_row
			<th class="right">Время</th>
			<th class="left">Сообщение</th>
		</tr>
		</thead>
		<tbody>
ZZZ;

	array_walk($datas, 'print_logs_list_callback', &$out);

	$out .= <<<ZZZ
		</tbody>
		</table>
ZZZ;
	//$onDOMReady = 'UP.admin.cbStuffStart();';
} else {
	$out = '<div id="status">&nbsp;</div><h2>Журнал событий</h2><p>Он пустой.</p>';
}

require UP_ROOT.'header.php';
echo($out);
require UP_ROOT.'footer.php';
exit();


function print_logs_list_callback($item, $key, $out) {
	$item_id = intval($item['id']);
	$message = $item['message'];
	$date = prettyDate($item['date']);

	$admin_td_row = '<td class="center"><input type="checkbox" value="1" id="item_cb_'.$item_id.'"/></td>';


	$out .= <<<ZZZ
		<tr id="row_item_$item_id">
			$admin_td_row
			<td class="right">$date</td>
			<td class="left">$message</td>
		</tr>
ZZZ;
}

?>
