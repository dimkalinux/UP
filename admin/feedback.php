<?php
define('ADMIN_PAGE', 1);

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}

require UP_ROOT.'functions.inc.php';


$feedbackList = getFeedbackList();

$out = <<<ZZZ
	<div id="status">&nbsp;</div>
	<h2>Сообщения «обратной связи»</h2>

	<table class="t1" id="search_files_table">
	<thead>
	<tr>
		<th class="first center"><input id="allCB" type="checkbox"/></th>
		<th style="width: 10em;">Дата</th>
		<th>Текст</th>
	</tr>
	</thead>
	<tbody>
	$feedbackList
	</tbody>
	</table>
ZZZ;


require UP_ROOT.'header.php';
echo($out);
$addScript[] = 'up.admin.js';
$onDOMReady = 'UP.admin.cbStuffStart();';
require UP_ROOT.'footer.php';
exit();



function getFeedbackList() {
	$out = '';

	try {
		$db = new DB;
		$datas = $db->getData("SELECT * FROM feedback ORDER BY id DESC LIMIT 100");

		if ($datas) {
			foreach ($datas as $rec) {
				$id = $rec['id'];
				$date = $rec['date'];
				$email = $rec['email'];
				$file = $rec['file'];
				$message = $rec['message'];
				$title = mb_substr($message, 0, 70);

			/*if ($file) {
				$file = '<enclosure url="http://up.lluga.net/up_feedback/'.$file.'"/>';
			}*/
				$out .= <<<FMB
				<tr>
					<td class="center"><input type="checkbox" value="1" id="item_cb_'.$id.'"/></td>
					<td>$date</td>
					<td id="text_$id"><span id="title_$id">$title</span></td>
					<td style="display: none;"><span id="title_$id">$title</span><span id="message_$id">$message</span></td>
				</tr>
FMB;

			}
		}

		return $out;
	} catch (Exception $e) {
		error($e->getMessage());
	}
}

?>
