<?

define('ADMIN_PAGE', 1);

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';


// admin js function
$addScript = $admin_th_row = $admin_td_row = $admin_actions_block = '';
if ($user['is_admin']) {
	$admin_th_row = '<th class="first center"><input id="allCB" type="checkbox"/></th>';
	$admin_actions_block = '<input type="button" value="not SPAM" onmousedown="UP.admin.unmarkItemSpam(false);" disabled="disabled" />';
}

try {
	$db = new DB;
	$datas = $db->getData("SELECT * FROM up WHERE deleted='0' AND hidden='1' ORDER BY uploaded_date DESC LIMIT 100");
} catch(Exception $e) {
	error($e->getMessage());
}

if ($datas) {
	$out = <<<FMB
		<div id="status">&nbsp;</div>
		<h2>Скрытые файлы</h2>
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
FMB;

	array_walk($datas, 'print_files_list_callback', $out);

	$out .= '</tbody></table>';
} else {
	$out = '<div id="status">&nbsp;</div><h2>Спам</h2><p>Отсутствует.</p>';
}

require UP_ROOT.'header.php';
echo $out;
require UP_ROOT.'footer.php';
exit();


function print_files_list_callback($item, $key, &$out) {
	global $user;

	$item_id = intval($item['id'], 10);
	$filename = get_cool_and_short_filename($item['filename'], 40);
	$filesize_text = format_filesize($item['size']);
	$ip = $item['ip'];
	$downloaded = $item['downloads'];
	$file_date = prettyDate($item['uploaded_date']);

	$admin_td_row = '';
	if ($user['is_admin']) {
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
