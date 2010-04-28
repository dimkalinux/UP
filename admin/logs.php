<?php
define('ADMIN_PAGE', 1);

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '.././');
}

require UP_ROOT.'functions.inc.php';

try {
	$db = DB::singleton();
	$datas = $db->getData("SELECT * FROM logs WHERE (date > (NOW()-INTERVAL 1 WEEK)) ORDER BY date DESC");
} catch (Exception $e) {
	error($e->getMessage());
}

if ($datas) {
	$out = <<<FMB
		<div id="status">&nbsp;</div>
		<h2>Журнал событий</h2>
		<table class="t1" id="search_files_table">
		<thead>
		<tr>
			<th class="right">Время</th>
			<th class="left">Сообщение</th>
		</tr>
		</thead>
		<tbody>
FMB;

	array_walk($datas, 'print_logs_list_callback', &$out);

	$out .= <<<FMB
		</tbody>
		</table>
FMB;
} else {
	$out = '<div id="status">&nbsp;</div><h2>Журнал событий</h2><p>Он пустой.</p>';
}

printPage($out);
exit();


function print_logs_list_callback($item, $key, $out) {
	$item_id = intval($item['id']);
	$message = $item['message'];
	$date = $item['date'];
	$type = $item['type'];

	$out .= <<<FMB
		<tr class="logs_$type">
			<td class="right nobr">$date</td>
			<td class="left">$message</td>
		</tr>
FMB;
}

?>
