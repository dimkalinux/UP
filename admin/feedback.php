<?php
define('ADMIN_PAGE', 1);

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}

require UP_ROOT.'functions.inc.php';


$feedbackList = getFeedbackList();

$out = <<<FMB
	<div id="status">&nbsp;</div>
	<h2>Сообщения «обратной связи» за 2 недели</h2>
	<ul class="feedbackList">
		$feedbackList
	</ul>
FMB;

$onDOMReady = <<<FMB
	$('.feedbackList li').each(function () {
		var li = $(this),
			id = parseInt($(this).attr('id').split('feeback_')[1], 10),
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
							li.fancyAnimate("#FA9CAC", "#ffffff", 250);
							UP.statusMsg.show(data.message, UP.env.msgError, false);
						}
					}
				});
			});
	});
FMB;

$addScript[] = 'up.admin.js';
printPage($out);
exit();


function getFeedbackList() {
	global $base_url;
	$out = '';

	try {
		$db = DB::singleton();
		$datas = $db->getData("SELECT * FROM feedback WHERE (date > (NOW()-INTERVAL 2 WEEK)) ORDER BY id DESC");

		if ($datas) {
			foreach ($datas as $rec) {
				$id = $rec['id'];
				$date = "<a href=\"{$base_url}feedback/{$id}/\">{$rec['date']}</a>";
				$email = (empty($rec['email'])) ? 'Mr. Anonymous' : "<a href=\"mailto:{$rec['email']}\">{$rec['email']}</a>";
				$file = $rec['file'];
				$message = stripslashes($rec['message']);

				if (empty($rec['email'])) {
					$identicon = '<img class="avatar" src="'.$base_url.'include/identicon.php?size=40&amp;hash='.md5("Mr. Anonymous").'" height="40" width="40" alt="Mr. Anonymous"/>';
				} else {
					$identicon = '<img class="avatar" src="'.$base_url.'include/identicon.php?size=40&amp;hash='.md5($rec["email"]).'" height="40" width="40" alt="'.$rec["email"].'"/>';
				}

				$deleteLink = ", <span class=\"as_js_link\" title=\"Удалить это сообщение\">X</span>";

				if ($file) {
					$file = "<a href=\"$base_url/up_feedback/$file\">файл</a>";
				}

				$out .= <<<FMB
				<li id="feedback_$id">
					<div class="commentID">$identicon</div>
					<div class="commentBody">
						<span class="commentAuthor">$email</span><small>{$date}{$deleteLink}</small><br/>
						$message
					</div>
					<br class="clear"/>
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
