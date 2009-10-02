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
	$feedbackList

ZZZ;


require UP_ROOT.'header.php';
echo $out;
$addScript[] = 'up.admin.js';
require UP_ROOT.'footer.php';
exit();



function getFeedbackList() {
	global $base_url;
	$out = '';

	try {
		$db = new DB;
		$datas = $db->getData("SELECT * FROM feedback ORDER BY id DESC LIMIT 100");

		if ($datas) {
			foreach ($datas as $rec) {
				$id = $rec['id'];
				$date = $rec['date'];
				$email = (empty($rec['email'])) ? 'Mr. Anonymous' : "<a href=\"mailto:{$rec['email']}\">{$rec['email']}</a>";
				$file = $rec['file'];
				$message = stripslashes($rec['message']);
				$title = mb_substr($message, 0, 70);

				if ($file) {
					$file = ", <a href=\"$base_url/up_feedback/$file\">файл</a>";
				}

				$out .= <<<FMB
				<div class="adminFeedbackBlock">
					<p>
						$message
						<div class="adminFeedbackBlockFooter">$email — {$date}{$file}</div>
						<div class="adminFeedbackBlockControl">
							<span class="as_js_link">ссылка</span> | <span class="as_js_link">удалить</span>
						</div>
					</p>
				</div>
FMB;

			}
		}

		return $out;
	} catch (Exception $e) {
		error($e->getMessage());
	}
}

?>
