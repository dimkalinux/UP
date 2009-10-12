<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


$addScript = $admin_th_row = $admin_td_row = $admin_actions_block = '';
$colspanPreAdmin = 1;
$colspan = 2;

// admin js function
if ($user['is_admin']) {
	$admin_th_row = '<th class="center"><input id="allCB" type="checkbox"/></th>';
	$admin_actions_block = '<div class="controlButtonsBlock"><button type="button" class="btn" disabled="disabled" onmousedown="UP.admin.unmarkItemSpam(false);"><span><span>не спам</span></span></button></div>';
	$addScript[] = 'up.admin.js';
	$colspanPreAdmin = 2;
}


try {
	$db = new DB;
	$datas = $db->getData("SELECT * FROM up WHERE deleted='0' AND hidden='0' AND spam='1' ORDER BY id DESC");
} catch(Exception $e) {
	error($e->getMessage());
}


if ($datas) {
	$out = <<<FMB
		<div id="status">&nbsp;</div>
		<h2>Спам</h2>
		<table class="t1" id="search_files_table">
		<thead>
		<tr>
			<th class="noborder" colspan="$colspanPreAdmin"></th>
			<th class="left noborder">$admin_actions_block</th>
			<th class="right noborder" id="pageLinks" colspan="$colspan"></th>
		</tr>
		<tr>
			$admin_th_row
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
	$out = '<div id="status">&nbsp;</div><h2>Спам</h2><p>Отсутствует.</p>';
}

require UP_ROOT.'header.php';
echo($out);
require UP_ROOT.'footer.php';
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

	$admin_td_row = '';
	if ($user['is_admin']) {
		$admin_td_row = '<td class="center"><input type="checkbox" value="1" id="item_cb_'.$item_id.'"/></td>';
	}


	$out .= <<<FMB
		<tr id="row_item_$item_id" class="row_item">
			$admin_td_row
			<td class="size">$filesize_text</td>
			<td class="name"><a href="{$base_url}$item_id/">$filename</a></td>
			<td class="download">$downloaded</td>
			<td class="time">$file_date</td>
		</tr>
FMB;
}

?>
