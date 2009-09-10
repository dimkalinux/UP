<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';

$error = 0;
$out = null;

do {
	if (!isset ($_GET['set'])) {
		$error = 1;
		break;
	}

	$group_id = intval (get_get('set'));
	if (!$group_id) $group_id = 0;
	isset ($_GET['pass']) ? $owner_key = get_get('pass') : $owner_key = null;

	$db = new DB;
	$row = $db->getRow("SELECT COUNT(*) AS num FROM up WHERE group_id=? LIMIT 100", $group_id);
	if (!$row || $row['num'] < 1) {
		$error = 1;
		break;
	}

	if ($owner_key !== null) {
		$datas = $db->getData("SELECT * FROM up WHERE group_id=? AND group_secret_code=? ORDER BY id LIMIT 10", $group_id, $owner_key);
	} else {
		$datas = $db->getData("SELECT * FROM up WHERE group_id=? ORDER BY id LIMIT 10", $group_id);
	}

	if ($datas) {
		$header_block = <<<ZZZ
		<div id="status">&nbsp;</div>
		<h2><div id="item_info_filename">$group_id</div></h2>
		<table class="t1" id="file_info_table">
		<tbody>
		<tr class="header">
ZZZ;
		$o = null;
		$total_size = 0;
		foreach ($datas as $rec) {
			$item_id = intval($rec['id']);
			$location = $rec['location'];
			$filename = check_plain (get_cool_and_short_filename ($rec['filename'], 40));
			$total_size += $rec['size'];
			$filesize_text = format_filesize ($rec['size']);
			$file_date = check_plain ($rec['uploaded_date']);
			$downloaded = intval ($rec['downloads']);
			$downloaded_text = format_raz ($downloaded);
			$deleted = (bool) $rec['deleted'];
			$deleted_date = $rec['deleted_date'];
			$item_magic = $rec['delete_num'];

			if ($owner_key !== null)
				$dlink = "/$item_id/$item_magic/";
			else
				$dlink = "/$item_id/";


			$o .= <<<ZZZ
			<tr>
				<td class="left"><a href="$dlink">$filename</a></td>
				<td class="right">$filesize_text</td>
				<td class="center">$downloaded</td>
				<td class="right">$file_date</td>
			</tr>
ZZZ;
		}
		$total_size = format_filesize($total_size);

		$footer_block = <<<ZZZ
		</tbody>
		<tfoot>
			<tr><td></td><td class="right"><strong>$total_size</strong></td><td></td><td></td></tr>
		</tfoot>
		</table>
		<span class="as_js_link dfs" onclick="$('#links_block').toggle();">Ссылки на группу файлов</span>
		<div id="links_block">
			<label for="link">ссылка</label>
			<input size="35" value="http://up.lluga.net/set/$group_id/" readonly="readonly" type="text" id="link" onclick="this.select()"/>

			<label for="html">для сайта или блога</label>
			<input size="35" value="&lt;a href=&quot;http://up.lluga.net/set/$group_id/&quot;&gt;http://up.lluga.net/set/$group_id/&lt;/a&gt;" readonly="readonly" type="text" id="html" onclick="this.select()"/>

			<label for="bbcode">для форума</label>
			<input size="35" value="[url=http://up.lluga.net/set/$group_id/]http://up.lluga.net/set/$group_id/[/url]" readonly="readonly" type="text" id="bbcode" onclick="this.select()"/>
		</div>
ZZZ;
		$out = $header_block.$o.$footer_block;
} while (0);

if ($error === 0) {
	require UP_ROOT.'header.php';
	echo ($out);
	require UP_ROOT.'footer.php';
	exit();
} else {
	show_error_message ("Ссылка не&nbsp;верна или устарела.");
}

?>
