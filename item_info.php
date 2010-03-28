<?php
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


$error = 0;
do {
	if (!isset($_GET['item'])) {
		$error = 1;
		break;
	}

	$item_id = intval($_GET['item'], 10);

	// first maybe GET
	isset($_GET['pass']) ? $magic = intval(get_get('pass'), 10) : $magic = null;

	// try a POST
	if ($magic === null) {
		isset($_POST['pass']) ? $magic = intval(get_post('pass'), 10) : $magic = null;
	}

	// build info
	try {
		$db = new DB;
		if ($magic !== null) {
			$row = $db->getRow("SELECT up.*, username, DATEDIFF(NOW(), GREATEST(up.last_downloaded_date,up.uploaded_date)) as NDI FROM up LEFT JOIN users ON up.user_id=users.id WHERE up.id=? AND up.delete_num=? LIMIT 1", $item_id, $magic);
		} else {
			$row = $db->getRow("SELECT up.*, username, DATEDIFF(NOW(), GREATEST(up.last_downloaded_date,up.uploaded_date)) as NDI FROM up LEFT JOIN users ON up.user_id=users.id WHERE up.id=? LIMIT 1", $item_id);
		}

		if (!$row) {
			$error = 2;
			break;
		}
	} catch (Exception $e) {
		error($e->getMessage());
	}


	// CHECK FOR DELETED
	if ($row['deleted'] == 1) {
		$deleted_reason = $row['deleted_reason'];
		$deleted_date = $row['deleted_date'];

		if (empty($deleted_reason)) {
			$deleted_reason = "неизвестна";
		}

		$out = <<<FMB
		<div id="status">&nbsp;</div>
		<h2>Файл удалён</h2>
		<p>Примечание: $deleted_reason
		<br/>Дата удаления: $deleted_date</p>
FMB;
		break;
	}


	$location = $row['location'];
	$fullFilename = htmlspecialchars_decode(stripslashes($row['filename']));
	$filename = get_cool_and_short_filename($fullFilename, 45);
	$filesize = $row['size'];
	$filesize_text = format_filesize($row['size']);
	$filesize_text_plain = format_filesize_plain($row['size']);
	$file_date = prettyDate($row['uploaded_date']);
	$file_last_downloaded_date = $row['last_downloaded_date'];
	$downloaded = $row['downloads'];
	$downloaded_text = format_raz($downloaded);
	$antivir_check_result = $row['antivir_checked'];
	$is_hidden = (bool) $row['hidden'];
	$is_spam = (bool) $row['spam'];
	$is_adult = (bool) $row['adult'];
	$owner_id = intval($row['user_id'], 10);
	$hash = $row['hash'];
	$password = $row['password'];
	$mime = $row['mime'];
	$username = $row['username'];

	$ndi = $row['NDI'];
	$wakkamakka = get_time_of_die($filesize, $downloaded, $ndi, $is_spam);
	if ($wakkamakka < 1) {
		$wakkamakka_text = '0 дней (сегодня будет удалён)';
	} else {
		$wakkamakka_text = format_days($wakkamakka);
	}

	// set title for page
	$page_title = "Скачать «${filename}»";




	// COMMENTS SECTION
	require UP_ROOT.'include/comments.inc.php';

	$commentAddFormAction = $base_url.$item_id.'/';
	$commentAddFormActionCSRF = generate_form_token($commentAddFormAction);
	$commentActionAdd = ACTION_COMMENTS_ADD;

	try {
		$comments = new Comments($item_id, $owner_id);

		if (isset($_POST['action'])) {
			// 1. check csrf
			if (!check_form_token($commentAddFormActionCSRF)) {
				throw new Exception('действие заблокировано системой безопасности');
			}

			switch(intval($_POST['action'], 10)) {
				case ACTION_COMMENTS_ADD:
					$comments->addComment($_POST['commentText']);
					if (isset($_REQUEST['json'])) {
						exit(json_encode(array('error'=> 0, 'message' => '')));
					} else {
						header("Location: {$base_url}{$item_id}/");
						exit();
					}
					break;

				default:
					throw new Exception('Неизвестный код команды');
			}
		}

		$commentsNum = $comments->commentsNum();
		$commentsLink = '<li><span class="as_js_link" rel="commentsBlock">комментарии&nbsp;(<span id="commentsNum">'.$commentsNum.'</span>)</span></li>';

		$commentsForm = '<br class="clear"/><p>Для комментирования необходимо <a href="'.$base_url.'login/" class="mainMenuLogin">войти в систему</a></p>';
		if (!$user['is_guest']) {
			$commentsForm = <<<FMB
				<form method="post" action="$commentAddFormAction" name="comments" enctype="multipart/form-data" accept-charset="utf-8">
					<input type="hidden" name="form_sent" value="1"/>
					<input type="hidden" name="action" value="$commentActionAdd"/>
					<input type="hidden" name="csrf_token" value="$commentAddFormActionCSRF"/>
					<div class="formRow">
						<label for="feedbackText">Ваш комментарий</label>
						<textarea name="commentText" rows="5" minLength="5" maxLength="1024" required="1" tabindex="1"></textarea>
					</div>
					<div class="formRow buttons">
						<input type="submit" name="do" value="Отправить" tabindex="2" class="default"/>
						<input type="reset" name="doClean" value="Очистить" tabindex="3"/>
					</div>
				</form>
FMB;
		}

		$commentsBlock = <<<FMB
		<div id="commentsBlock" class="superHidden">
			<h3>Комментарии</h3>
				<ul class="commentList"></ul>
				<div id="commentStatus">&nbsp;</div>
				$commentsForm
		</div>
FMB;
	} catch (Exception $e) {
		if (isset($_POST['json'])) {
			exit(json_encode(array('error'=> 1, 'message' => $e->getMessage())));
		} else {
			show_error_message($e->getMessage());
		}
	}



	// OWNER SECTION
	try {
		$im_owner = false;
		if (!$user['is_guest']) {
			$result = $db->numRows('SELECT user_id FROM up WHERE id=? AND user_id=?', $item_id, $user['id']);
			if ($result === 1) {
				$im_owner = true;
			}
		}

		$im_owner_block = '';
		if ($magic && !$user['is_guest']) {
			// check for owner
			$result = $db->numRows('SELECT user_id FROM up WHERE id=? AND user_id=0', $item_id);
			if ($result === 1) {
				$im_owner_block = <<<FMB
				<div id="im_owner_block" class="hint">
				Вы не являетесь владельцем этого файла, но вы можите добавить его к своим файлам.<br/>
				<span class="as_js_link" onclick="UP.owner.makeMeOWner({$user['id']}, '$item_id', '$magic');" title="Стать владельцем файла">Добавить</span> или <span class="as_js_link" onclick="$('#im_owner_block').hide(400);">отменить</span>?
				</div>
FMB;
			}
		}
	} catch (Exception $e) {
		error($e->getMessage());
	}

	// ANTIVIR SECTION
	switch ($antivir_check_result) {
		case ANTIVIR_VIRUS:
			$antivir_check = '<span class="red">файл заражен вирусом</span>';
			break;

		case ANTIVIR_ERROR:
			$antivir_check = '<span class="red">ошибка при проверке</span>';
			break;

		case ANTIVIR_CLEAN:
			$antivir_check = '<span class="green">вирусов&nbsp;нет</span>';
			break;

		case ANTIVIR_NOT_CHECKED:
		default:
			$antivir_check = 'не проверен';
			break;
	}

	$row_hash = '';
	if (!empty($hash)) {
		$row_hash = '<tr><td class="ab">md5-хеш</td><td class="bb">'.$hash.'</td></tr>';
	}


	// DOWNLOAD ROW
	if ($downloaded < 1) {
		$file_last_downloaded_date = 'неизвестно';
	} else {
		$downloaded_text .= ', '.$file_last_downloaded_date;
	}


	// SPAM SECTION
	$js_spam_warning_block = '';
	if ($is_spam && !$is_hidden) {
		if ($magic || $im_owner) {
			$js_spam_warning_block = "UP.statusMsg.show('Найден СПАМ: cрок хранения сокращён до 2-х дней', UP.env.msgWarn, false);";
		} else {
			$js_spam_warning_block = "UP.statusMsg.show('Внимание: возможно это СПАМ', UP.env.msgWarn, true);";
		}
	}


	// ADULT SECTION
	$js_adult_warning_block = '';
	if ($is_adult && !$is_hidden) {
		$js_spam_warning_block = '';
		if ($magic || $im_owner) {
			$js_adult_warning_block = "UP.statusMsg.show('Обнаружен контент «только для взрослых». Файл не будет показан в общем списке', UP.env.msgWarn, false);";
		} else {
			$js_adult_warning_block = "UP.statusMsg.show('Внимание: возможен контент «только для взрослых»', UP.env.msgWarn, true);";
		}
	}

	// AUTHOR BLOCK
	$author_block = '';
	if (!empty($username)) {
		if (!$is_hidden || ($user['is_admin'])) {
			$authorProfileLink = '<a href="'.$base_url.'user/'.$owner_id.'/files/" title="Перейти к файлам владельца">'.$username.'</a>';
			$author_block = '<tr><td class="ab">залил</td><td class="bb">'.$authorProfileLink.'</td></tr>';
		}
	}


	// NEW MAGIC LINKS SYSTEM
	$cache = new Cache;
	$dlmKey = 'dlm'.$item_id.ip2long($user['ip']);
	if (!$dlmValue = $cache->get($dlmKey)) {
		$prefix = substr(md5($user['ip'].'operaisSux'), 0, 4);
		$dlmValue = uniqid($prefix);
		$cache->set($dlmValue, $dlmKey, 36000);
	}



	// PASSWORD SECTION
	$is_password = mb_strlen($password) > 0;
	$pass_input = $js_pass_block = '';
	if ($is_password && !($magic || $im_owner)) {
		$pass_input = '<tr><td class="ab">Пароль:</td><td><input type="password" name="password" minLength="1" maxLength="128"/></td></tr>';
		$js_pass_block = "$('input[name=password]').change(UP.formCheck.search).keyup(UP.formCheck.search).focus(); UP.formCheck.search();";
	}

	// PASSWORD LABEL
	$passwordLabelOpacity = ($is_password) ? "1.0" : "0.0";
	$passwordLabel = '<span class="passwordLabel" style="opacity: '.$passwordLabelOpacity.';" title="Файл защищён паролем" id="passwordLabel">&beta;</span>';


	// CREATE DOWNLOAD LINK
	$dlink_raw = "{$base_url}download/{$item_id}/{$dlmValue}/";
	$dlink = '<input type="submit" value="Скачать файл"/>';

	$owner_block = '';
	if ($magic || $im_owner) {
		$ownerPasswordAction = '';
		if ($is_password) {
			$ownerPasswordAction = <<<FMB
			<li><span id="owner_password_link" status="on" class="as_js_link" title="Сменить пароль на&nbsp;файл" onclick="UP.owner.changePassword('$item_id', '$magic')" rel="1">изменить&nbsp;пароль</span></li>
FMB;
		} else {
			$ownerPasswordAction = <<<FMB
			<li><span id="owner_password_link" status="on" class="as_js_link" title="Установить пароль на&nbsp;файл" onclick="UP.owner.changePassword('$item_id', '$magic')" rel="0">установить&nbsp;пароль</span></li>
FMB;
		}

		$owner_block = <<<FMB
	<tr><td class="ab">управление</td>
	<td class="bb" id="owner_links">
		<ul class="as_js_link_list">
			<li><span id="owner_delete_link" status="on" class="as_js_link" title="Удалить файл" onclick="UP.owner.remove('$item_id', '$magic')">удалить</span></li>
			<li><span id="owner_rename_link" status="on" title="Переименовать файл" class="as_js_link" onclick="UP.owner.rename('$item_id', '$magic')">переименовать</span></li>
			$ownerPasswordAction
		</ul>
	</td></tr>
FMB;
	}


	// THUMBS SECTION
	$thumbs_block = $js_thumbs_block = '';
	$is_image = is_image_by_ext($filename);
	if ($is_image && !$is_password) {
		$thumbs_full_url = $base_url.'thumbs/'.sha1($item_id).'.jpg';
		$thumbs_preview_small_url = $base_url.'thumbs/'.sha1($item_id).'.jpg';
		$thumbs_preview_url = $base_url.'thumbs/large/'.sha1($item_id).'.jpg';
		$thumbs_block = <<<FMB
		<div class="thumbs"><a href="$thumbs_preview_url"><img src="$thumbs_full_url"/></a></div>
FMB;

		$js_thumbs_block = <<<FMB
			$(".thumbs a").fancybox({'zoomSpeedIn': 300, 'zoomSpeedOut': 0, 'overlayShow': false, 'hideOnContentClick': true  });
			var img = new Image;
			$(img).attr("src", "$thumbs_preview_url");
FMB;
		$addScript[] .= 'jquery.fancybox-1.2.1.js';
	}


	// FLV VIDEO SECTION
	$flv_block = $js_video_block = '';
	if (is_flv($filename, $row['mime'])) {
		$flv_block = <<<FMB
		<tr><td class="ab">видео</td>
		<td class="bb" id="videoBlock">
			<a href="#flvBlock" class="as_js_link" id="fancyVideo">смотреть</span>
		</td>
		<div id="flvBlock" class="superHidden">FLV</div>
FMB;

		$js_video_block = <<<FMB
			UP.media.flv('flvBlock', '$dlink_raw');

			$("#fancyVideo").fancybox({
				zoomSpeedIn: 300,
				zoomSpeedOut: 0,
				overlayShow: false,
				hideOnContentClick: false,
				frameWidth: 512,
				frameHeight: 384,
				padding: 1,
			});
FMB;

		$addScript[] .= 'jquery.fancybox-1.2.1.js';
	}


	// MP3 SECTION
	$mp3_block = '';
	if (is_mp3($filename, $mime)) {
		$mp3_block = <<<FMB
		<tr><td class="ab">аудио</td>
		<td class="bb" id="mp3Block">
			<span onclick="UP.media.mp3('mp3Block', '$dlink_raw');" class="as_js_link">слушать</span>
		</td>
		</tr>
FMB;
	}


	// SEARCH LIKE SECTION
	try {
		$search_like_block = $similar_num = '';
		$search_filename = convert_filename_to_similar($filename);
		$similar_num = get_similar_count($search_filename, $item_id);

		if ($similar_num > 0 && $similar_num < 50) {
			$search_like_block = '<tr><td class="ab">подобные файлы</td><td class="bb"><a title="Показать файлы с подобным именем" href="'.$base_url.'search/?s='.urlencode($search_filename).'&amp;doSubmit&amp;ft=1">'.$similar_num.'</a></td></tr>';
		}
	} catch (Exception $e) {
		error($e->getMessage());
	}


	// SELECT FORM METHOD
	$is_password ? $form_method = 'post' : $form_method = 'get';


	// LINKS SECTION
	$links_bbcode_raw = '';
	if ($is_image) {
		$links_bbcode_raw = "[url={$base_url}{$item_id}/][img]{$thumbs_preview_small_url}[/img][/url]";
		$links_bbcode = '<input size="35" value="'.$links_bbcode_raw.'" readonly="readonly" type="text" id="bbcode" onclick="this.select()"/>';
	} else {
		$links_bbcode_raw = "[url={$base_url}{$item_id}/]{$filename} — {$filesize_text_plain}[/url]";
		$links_bbcode = '<input size="35" value="'.$links_bbcode_raw.'" readonly="readonly" type="text" id="bbcode" onclick="this.select()"/>';
	}


	$out = <<<FMB
	<div id="status">&nbsp;</div>
	$im_owner_block
	<h2>{$passwordLabel}<span id="item_info_filename" title="$fullFilename">$filename</span></h2>
	<table class="asDiv">
	<tr><td>
		<table class="t1" id="file_info_table">
			<tr><td class="ab">размер</td><td class="bb">$filesize_text</td></tr>
			<tr><td class="ab">скачан</td><td class="bb">$downloaded_text</td></tr>
			<tr><td class="ab">срок хранения</td><td class="bb">$wakkamakka_text</td></tr>
			$author_block
			$mp3_block
			$flv_block
			$search_like_block
			<tr><td class="ab">антивирус</td><td class="bb">$antivir_check</td></tr>
			$row_hash
			<form method="$form_method" action="$dlink_raw" autocomplete="off">
			$pass_input
			$owner_block
			<tr><td class="ab"></td><td class="bb"><div id="download_link">$dlink</div></td></tr>
			</form>
			<tr>
				<td class="ab"></td>
				<td class="bb">
				<ul class="as_js_link_list itemNotOwnerActions">
					<li><span class="as_js_link" rel="links_block">ссылки на файл</span></li>
					{$commentsLink}
				</ul>
				</td>
			</tr>
		</table>

	<div id="links_block">
		<div class="tt-wedge tt-wedge-up tt-wedge-links"></div>
		<div id="formBlock">
			<label for="link">ссылка</label>
			 <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" class="clippy" id="clippy" height="14" width="110">
				<param name="movie" value="{$base_url}flash/clippy.swf">
				<param name="allowScriptAccess" value="always">
				<param name="quality" value="high">
				<param name="scale" value="noscale">
				<param name="FlashVars" value="text={$base_url}{$item_id}/">
				<param name="bgcolor" value="#DFEBF7">
				<param name="wmode" value="opaque">
				<embed src="{$base_url}flash/clippy.swf" name="clippy" quality="high" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="text={$base_url}{$item_id}/" bgcolor="#DFEBF7" wmode="opaque" height="14" width="110">
			</object>

			<input size="35" value="{$base_url}{$item_id}" readonly="readonly" type="text" id="link" onclick="this.select()"/>

			<label for="html">для сайта или блога</label>
			 <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" class="clippy" id="clippy" height="14" width="110">
				<param name="movie" value="{$base_url}flash/clippy.swf">
				<param name="allowScriptAccess" value="always">
				<param name="quality" value="high">
				<param name="scale" value="noscale">
				<param name="FlashVars" value="text=&lt;a href=&quot;{$base_url}{$item_id}/&quot;&gt;{$filename} — {$filesize_text_plain}&lt;/a&gt;">
				<param name="bgcolor" value="#DFEBF7">
				<param name="wmode" value="opaque">
				<embed src="{$base_url}flash/clippy.swf" name="clippy" quality="high" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="text=&lt;a href=&quot;{$base_url}{$item_id}/&quot;&gt;{$filename} — {$filesize_text_plain}&lt;/a&gt;" bgcolor="#DFEBF7" wmode="opaque" height="14" width="110">
			</object>
			<input size="35" value="&lt;a href=&quot;{$base_url}{$item_id}/&quot;&gt;{$filename} — {$filesize_text_plain}&lt;/a&gt;" readonly="readonly" type="text" id="html" onclick="this.select()"/>

			<label for="bbcode">для форума</label>
			 <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" class="clippy" id="clippy" height="14" width="110">
				<param name="movie" value="{$base_url}flash/clippy.swf">
				<param name="allowScriptAccess" value="always">
				<param name="quality" value="high">
				<param name="scale" value="noscale">
				<param name="FlashVars" value="text=$links_bbcode_raw">
				<param name="bgcolor" value="#DFEBF7">
				<param name="wmode" value="opaque">
				<embed src="{$base_url}flash/clippy.swf" name="clippy" quality="high" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="text=$links_bbcode_raw" bgcolor="#DFEBF7" wmode="opaque" height="14" width="110">
			</object>
			$links_bbcode

			<label for="dlink">прямая ссылка</label>
			 <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" class="clippy" id="clippy" height="14" width="110">
				<param name="movie" value="{$base_url}flash/clippy.swf">
				<param name="allowScriptAccess" value="always">
				<param name="quality" value="high">
				<param name="scale" value="noscale">
				<param name="FlashVars" value="text={$dlink_raw}">
				<param name="bgcolor" value="#DFEBF7">
				<param name="wmode" value="opaque">
				<embed src="{$base_url}flash/clippy.swf" name="clippy" quality="high" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="text={$dlink_raw}" bgcolor="#DFEBF7" wmode="opaque" height="14" width="110">
			</object>
			<input size="35" value="{$dlink_raw}" readonly="readonly" type="text" id="dlink" onclick="this.select()"/>
		</div>
	</div>
	$commentsBlock
	</td>
	<td>$thumbs_block</td>
	</tr>
	</table>
FMB;

	$jsBindActionList = '$(".itemNotOwnerActions li span.as_js_link").click(function () { UP.utils.JSLinkListToggle($(this)); });';

	$jsGetCommentsList = <<<FMB
	UP.comments.loadCommentsList($item_id, $owner_id);
	var form = $("form[name='comments']");
	UP.formCheck.register(form);

	$(form).find("input[required],textarea[required]")
		.change(function () { UP.formCheck.register(form);})
		.keyup(function () { UP.formCheck.register(form); })

	$('#wrap')
		.stopTime('checkCommentsFormTimer')
		.everyTime(500, 'checkCommentsFormTimer', function () { UP.formCheck.register(form); });

	// form
	var options = {
		url:	'$commentAddFormAction',
		dataType: 'json',
		resetForm: false,
		cleanForm: false,
		type: 'post',
		data: { json: 1 },

		beforeSubmit: function (formArray, jqForm) {
			$('#wrap').stopTime('checkCommentsFormTimer');
			$("#commentStatus").html('&nbsp;');
			$(form).find("input[type='submit']").attr("disabled", "disabled");

			$(document).oneTime(250, 'commentAddWaitTimer', function () {
				$("#commentStatus").html('<span type="waiting">Ожидайте, комментарий добавляется&hellip;</span>').show(200);
			});

			return true;
		},

		error: function () {
			$(document).stopTime('commentAddWaitTimer');
			$('#wrap').everyTime(500, 'checkCommentsFormTimer', function () { UP.formCheck.register(form); });
			$("#commentStatus").html('<span type="error">Невозможно добавить комментарий.</span>').show();
			$(form).find("input[type='submit']").removeAttr("disabled");
		},

		success: function (r) {
			$(document).stopTime('commentAddWaitTimer');
			$("#commentStatus").html('&nbsp;');

			if (r) {
				if (parseInt(r.error, 10) === 0) {
					$(form).clearForm().resetForm();
					UP.comments.loadCommentsList($item_id, $owner_id);
				} else {
					$("#commentStatus").html('<span type="error">'+r.message+'</span>').show();
					$(form).find("input[type='submit']").removeAttr("disabled");
				}
			} else {
				$("#commentStatus").html('<span type="error">Невозможно добавить комментарий.</span>').show();
				$(form).find("input[type='submit']").removeAttr("disabled");
			}
			$(form).find("[required='1'][value='']:first").focus();
		}
	};

	$(form).submit(function () {
		$(this).ajaxSubmit(options);
		return false;
	});

	UP.statusMsg.defferedClear();

	if ($.cookie(UP.env.itemInfoStatusCookie)) {
		$('#'+$.cookie(UP.env.itemInfoStatusCookie)).toggle();
	}

	$(form).bind("reset", function () {
		$(document).oneTime(100, 'z', function () { $(form).find("[required='1'][value='']:first").focus(); });
	});

	$(form).find("[required='1'][value='']:first").focus();
FMB;

	$onDOMReady = $js_spam_warning_block.$js_adult_warning_block.$js_pass_block.$js_thumbs_block.$js_video_block.$jsBindActionList.$jsGetCommentsList;
} while (0);

if ($error === 0) {
	printPage($out);
	exit();
} else {
	show_error_message("Ссылка не&nbsp;верна или устарела.");
}
?>
