<?php
define('ADMIN_PAGE', 1);

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}

require UP_ROOT.'functions.inc.php';


$feedbackList = getFeedbackList();

$out = <<<FMB
	<div id="status">&nbsp;</div>
	<h2>Сообщения «обратной связи»</h2>
	<p>Всего сообщений: 5</p>
	<ul class="as_js_link_list">
		<li><span class="as_js_link currentLinkAsTab">за неделю</span></li>
		<li><span class="as_js_link">месяц</span></li>
		<li><span class="as_js_link">3 месяца</span></li>
		<li><span class="as_js_link">год</span></li>
	</ul>
	<ol class="feedbackList">
		$feedbackList
	</ol>
FMB;

$onDOMReady = <<<FMB
	$('.feedbackList li').each(function () {
		var li = $(this),
			id = parseInt($(this).attr('id').split('item_')[1], 10),
			deleteLink = li.find('span.as_js_link');

			deleteLink.click(function () {
				UP.wait.start();

				$.ajax({
					type: 	'GET',
					url: 	UP.env.ajaxAdminBackend,
					data: 	{ t_action: UP.env.actionAdminRemoveFeedbackMessage, t_id: id },
					dataType: 'json',
					error: function() {
						UP.wait.stop();
						UP.statusMsg.show('Невозможно удалить сообщение', UP.env.msgError, false);
					},
					success: function(data) {
						UP.wait.stop();
						if (parseInt(data.result, 10) === 1) {
							li.hide(350, function () {
								$(this).remove();
							});
						} else {
							li.animate({backgroundColor: "#FA9CAC"}, 250)
								.animate({backgroundColor: "#ffffff"}, 250)
								.animate({backgroundColor: "#FA9CAC"}, 250)
								.animate({backgroundColor: "#ffffff"}, 250);

							UP.statusMsg.show(data.message, UP.env.msgError, false);
						}
					}
				});
			})

	});
FMB;


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
				$date = "<a href=\"{$base_url}feedback/{$id}/\">{$rec['date']}</a>";
				$email = (empty($rec['email'])) ? 'Mr. Anonymous' : "<a href=\"mailto:{$rec['email']}\">{$rec['email']}</a>";
				$file = $rec['file'];
				$message = stripslashes($rec['message']);

				if (empty($rec['email'])) {
					$identicon = '<img class="avatar" src="'.$base_url.'include/identicon.php?size=64&amp;hash='.md5("Mr. Anonymous").'" height="64" width="64" alt="Mr. Anonymous"/>';
				} else {
					$identicon = '<img class="avatar" src="'.$base_url.'include/identicon.php?size=64&amp;hash='.md5($rec["email"]).'" height="64" width="64" alt="'.$rec["email"].'"/>';
				}

				$deleteLink = ", <span class=\"as_js_link\" title=\"Удалить это сообщение\">X</span>";

				if ($file) {
					$file = "<a href=\"$base_url/up_feedback/$file\">файл</a>";
				}

				$out .= <<<FMB
				<li id="item_$id">
					$identicon
					$email<br/>
					<small>
						{$date}{$deleteLink}<br/>
						$file<br/>
					</small>
					<p>$message</p>
				</li>
FMB;

			}
		}

		return $out;
	} catch (Exception $e) {
		error($e->getMessage());
	}
}

?>
