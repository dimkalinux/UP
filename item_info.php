<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


$error = 0;
do {
	if (!isset ($_GET['item'])) {
		$error = 1;
		break;
	}

	$item_id = intval($_GET['item'], 10);

	// first maybe GET
	isset ($_GET['pass']) ? $magic = intval(get_get('pass'), 10) : $magic = null;

	// try a POST
	if ($magic === null) {
		isset ($_POST['pass']) ? $magic = intval(get_post('pass'), 10) : $magic = null;
	}

	// build info
	try {
		$db = new DB;
		if ($magic !== null) {
			$row = $db->getRow("SELECT *, DATEDIFF(NOW(), GREATEST(last_downloaded_date,uploaded_date)) as NDI FROM up WHERE id=? AND delete_num=? LIMIT 1", $item_id, $magic);
		} else {
			$row = $db->getRow("SELECT *, DATEDIFF(NOW(), GREATEST(last_downloaded_date,uploaded_date)) as NDI FROM up WHERE id=? LIMIT 1", $item_id);
		}
	} catch (Exception $e) {
		error($e->getMessage());
	}

	if (!$row) {
		$error = 2;
		break;
	}

	// first - check for deleted
	if ($row['deleted'] == 1) {
		$deleted_reason = $row['deleted_reason'];
		$deleted_date = $row['deleted_date'];

		if (! $deleted_reason) {
			$deleted_reason = "неизвестна";
		}

		$out = <<<ZZZ
		<div id="status">&nbsp;</div>
		<h2>Файл удалён</h2>
		<p>Примечание: $deleted_reason</p>
		<p>Дата удаление: $deleted_date</p>
ZZZ;
		// go
		break;
	}

	$cache = new Cache;
	// normal file
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
	$user_ip = get_client_ip();
	$hidden = (bool) $row['spam'];
	$is_spam = (bool) $row['spam'];
	$is_adult = (bool) $row['adult'];
	$md5 = $row['md5'];

	$ndi = $row['NDI'];
	$wakkamakka = get_time_of_die($filesize, $downloaded, $ndi, $is_spam);
	if ($wakkamakka < 1) {
		$wakkamakka_text = '0 дней (сегодня будет удалён)';
	} else {
		$wakkamakka_text = format_days($wakkamakka);
	}


	$im_owner = false;
	if (!$user['is_guest']) {
		$result = $db->numRows('SELECT user_id FROM up WHERE id=? AND user_id=?', $item_id, $user['id']);
		if ($result === 1) {
			$im_owner = true;
		}
	}

	// set title for page
	$page_title = "Скачать «${filename}»";

	// get desc
	try {
		$row = $db->getRow("SELECT description FROM description WHERE id=? LIMIT 1", $item_id);
		$desc = isset($row['description']) ? htmlspecialchars_decode($row['description']) : '';
	} catch (Exception $e) {
		error($e->getMessage());
	}


	// im owner block
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



	// Antivir
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


	// Download row
	if ($downloaded < 1) {
		$file_last_downloaded_date = 'неизвестно';
	} else {
		$downloaded_text .= ', '.$file_last_downloaded_date;
	}


	// SPAM?
	$js_spam_warning_block = '';
	if ($is_spam && !$hidden) {
		if ($magic || $im_owner) {
			$js_spam_warning_block = "UP.statusMsg.show('Найден СПАМ: cрок хранения сокращён до 2-х дней', UP.env.msgWarn, false);";
		} else {
			$js_spam_warning_block = "UP.statusMsg.show('Внимание: возможно это СПАМ', UP.env.msgWarn, true);";
		}
	}


	// Adult?
	$js_adult_warning_block = '';
	if ($is_adult && !$hidden) {
		$js_spam_warning_block = '';

		if ($magic || $im_owner) {
			$js_adult_warning_block = "UP.statusMsg.show('Обнаружен контент «только для взрослых». Файл не будет показан в общем списке', UP.env.msgWarn, false);";
		} else {
			$js_adult_warning_block = "UP.statusMsg.show('Внимание: возможен контент «только для взрослых»', UP.env.msgWarn, true);";
		}
	}


	// new magic links system
	$dlmKey = 'dlm'.$item_id.ip2long($user_ip);
	if (!$dlmValue = $cache->get($dlmKey)) {
		$prefix = substr(md5($user_ip.'operaisSux'), 0, 4);
		$dlmValue = uniqid($prefix);
		$cache->set($dlmValue, $dlmKey, 36000);
	}

	// password present
	$is_password = mb_strlen($row['password']) > 0;
	$pass_input = $js_pass_block = '';
	if ($is_password) {
		$pass_input = '<tr><td class="ab">Пароль:</td><td><input type="password" name="password" minLength="1" maxLength="128"/></td></tr>';
		$js_pass_block = "$('input[name=password]').change(UP.formCheck.search).keyup(UP.formCheck.search).focus(); UP.formCheck.search();";
	}


	// create download link
	$dlink_raw = "/download/$item_id/$dlmValue/";
	$dlink = '<input type="submit" value="Скачать файл"/>';

	$owner_block = '';
	if ($magic || $im_owner) {
		$md5_link = '';
		if (empty($md5)) {
			$md5_link = <<<FMB
			&nbsp;<span id="owner_md5_link" status="on" class="as_js_link" title="Вычислить контрольную сумму файла" onclick="UP.owner.md5('$item_id', '$magic')">md5</span>
FMB;
		}

		$owner_block = <<<ZZZ
	<tr><td class="ab">управление</td>
	<td class="bb" id="owner_links">
		<span id="owner_delete_link" status="on" class="as_js_link" title="Удалить файл" onclick="UP.owner.remove('$item_id', '$magic')">удалить</span>
		&nbsp;<span id="owner_rename_link" status="on" title="Переименовать файл" class="as_js_link" onclick="UP.owner.rename('$item_id', '$magic')">переименовать</span>
		$md5_link
	</td></tr>
ZZZ;
	}

	// thumbs handle
	$thumbs_block = $js_thumbs_block = '';
	$is_image = is_image_by_ext($filename);
	if ($is_image && !$is_password) {
		$thumbs_full_url = $base_url.'thumbs/'.md5($row['md5'].$item_id).'.jpg';
		$thumbs_preview_small_url = $base_url.'thumbs/'.md5($row['md5'].$item_id).'.jpg';
		$thumbs_preview_url = $base_url.'thumbs/large/'.md5($row['md5'].$item_id).'.jpg';
		$thumbs_block = <<<ZZZ
		<div class="thumbs"><a href="$thumbs_preview_url"><img src="$thumbs_full_url"/></a></div>
ZZZ;

		$js_thumbs_block = <<<ZZZ
			$(".thumbs a").fancybox({'zoomSpeedIn': 300, 'zoomSpeedOut': 0, 'overlayShow': false, 'hideOnContentClick': true  });
			var img = new Image;
			$(img).attr("src", "$thumbs_preview_url");
ZZZ;
			$addScript[] .= 'jquery.fancybox-1.2.1.js';
	}


	// flv block
	$flv_block = $js_video_block = '';
	if (is_flv($filename, $row['mime'])) {
		$flv_block = <<<ZZZ
		<tr><td class="ab">видео</td>
		<td class="bb" id="videoBlock">
			<a href="#flvBlock" class="as_js_link" id="fancyVideo">смотреть</span>
		</td>
		<div id="flvBlock superHidden"></div>
ZZZ;

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

		$addScript[] .= 'swfobject.js';
		$addScript[] .= 'jquery.fancybox-1.2.1.js';
	}

	// mp3 block
	$mp3_block = null;
	if (is_mp3 ($filename, $row['mime'])) {
		$mp3_block = <<<ZZZ
		<tr><td class="ab">аудио</td>
		<td class="bb" id="mp3Block">
			<span onclick="UP.media.mp3('mp3Block', '$dlink_raw');" class="as_js_link">слушать</span>
		</td>
		</tr>
ZZZ;
	}


	try {
		$search_like_block = $similar_num = '';
		$search_filename = convert_filename_to_similar($filename);
		$similar_num = get_similar_count($search_filename, $item_id);

		if ($similar_num > 0 && $similar_num < 50) {
			$search_like_block = '<tr><td class="ab">похожие файлы</td><td class="bb">'. $similar_num .' <a href="'.$base_url.'search/?s='. urlencode($search_filename) .'&amp;doSubmit&amp;ft=1">показать</a></td></tr>';
		}
	} catch (Exception $e) {
		error($e->getMessage());
	}

	$is_password ? $form_method = 'post' : $form_method = 'get';

	// links block
	$links_bbcode_raw = '';
	if ($is_image) {
		$links_bbcode_raw = "[url={$base_url}{$item_id}/][img]{$thumbs_preview_small_url}[/img][/url]";
		$links_bbcode = '<input size="35" value="'.$links_bbcode_raw.'" readonly="readonly" type="text" id="bbcode" onclick="this.select()"/>';
	} else {
		$links_bbcode_raw = "[url={$base_url}{$item_id}/]{$filename} — {$filesize_text_plain}[/url]";
		$links_bbcode = '<input size="35" value="'.$links_bbcode_raw.'" readonly="readonly" type="text" id="bbcode" onclick="this.select()"/>';
	}


	$desc_block = $desc_link = $desc_js_block = '';
	if (mb_strlen($desc) > 0) {
		$desc_block = <<<ZZZ
		<div id="desc_block">
			<div class="tt-wedge tt-wedge-up tt-wedge-desc"></div>
			<div id="formBlock">
			<h3>Описание</h3>
	    	<div id="desc_text">$desc</div>
		</div>
		<br class="clear"/>
ZZZ;
		$desc_link = '<li><span class="as_js_link" rel="desc_block">описание</span></li>';
	}

	// COMMENTS SECTION
	require UP_ROOT.'include/comments.inc.php';
	try {
		$comments = new Comments($item_id);

		if (isset($_POST['action'])) {
			switch(intval($_POST['action'], 10)) {
				case ACTION_COMMENTS_ADD:
					$comments->addComment($_POST['commentText']);
					break;

				default:
					throw new Exception('Неизвестный код команды');
			}
		}

		$commentsNum = $comments->commentsNum();

		if ($commentsNum > 0) {
			$commentsLink = '<li><span class="as_js_link" rel="commentsBlock">комментарии ('.$commentsNum.')</span></li>';
		} else {
			$commentsLink = '<li><span class="as_js_link" rel="commentsBlock">комментарии</span></li>';
		}

		$commentAddFormAction = $base_url.$item_id.'/';
		$commentAddFormActionCSRF = generate_form_token($commentAddFormAction);
		$commentActionAdd = ACTION_COMMENTS_ADD;

		$commentsBlock = <<<FMB
		<div id="commentsBlock" class="superHidden">
			<h3>Комментарии</h3>
				<ul class="commentList"><li>Ожидайте, комментарии загружаются&hellip;</li></ul>
				<form method="post" action="$commentAddFormAction" name="comments" enctype="multipart/form-data" accept-charset="utf-8">
				<input type="hidden" name="form_sent" value="1"/>
				<input type="hidden" name="action" value="$commentActionAdd"/>
				<input type="hidden" name="csrf_token" value="$commentAddFormActionCSRF"/>
				<div class="formRow">
					<label for="feedbackText">Ваш комментарий</label>
					<textarea name="commentText" rows="6" minLength="1" maxLength="2048" required="1" tabindex="1"></textarea>
				</div>
				<div class="formRow buttons">
					<input type="submit" name="do" value="Отправить" tabindex="4"/>
				</div>
			</form>
		</div>
FMB;


	} catch (Exception $e) {
		show_error_message($e->getMessage());
	}


	$out = <<<ZZZ
	<div id="status">&nbsp;</div>
	$im_owner_block
	<h2 id="item_info_filename" title="$fullFilename">$filename</h2>
	<form method="$form_method" action="/download/$item_id/$dlmValue/" autocomplete="off">
	<table class="asDiv">
	<tr><td>
		<table class="t1" id="file_info_table">
			<tr><td class="ab">размер</td><td class="bb">$filesize_text</td></tr>
			<tr><td class="ab">скачан</td><td class="bb">$downloaded_text</td></tr>
			<tr><td class="ab">срок хранения</td><td class="bb">$wakkamakka_text</td></tr>
			$mp3_block
			$flv_block
			$search_like_block
			<tr><td class="ab">антивирус</td><td class="bb">$antivir_check</td></tr>
			$pass_input
			$owner_block
			<tr><td class="ab"></td><td class="bb"><div id="download_link">$dlink</div></td></tr>
			<tr>
				<td class="ab"></td>
				<td class="bb">
				<ul class="as_js_link_list">
					<li><span class="as_js_link" rel="links_block">ссылки на файл</span></li>
					{$commentsLink}
					{$desc_link}
				</ul>
				</td>
			</tr>
		</table>
	</form>

	<div id="links_block">
		<div class="tt-wedge tt-wedge-up tt-wedge-links"></div>
		<div id="formBlock">
			<label for="link">ссылка</label>
			 <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" class="clippy" id="clippy" height="14" width="110">
				<param name="movie" value="/flash/clippy.swf">
				<param name="allowScriptAccess" value="always">
				<param name="quality" value="high">
				<param name="scale" value="noscale">
				<param name="FlashVars" value="text={$base_url}{$item_id}/">
				<param name="bgcolor" value="#DFEBF7">
				<param name="wmode" value="opaque">
				<embed src="/flash/clippy.swf" name="clippy" quality="high" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="text={$base_url}{$item_id}/" bgcolor="#DFEBF7" wmode="opaque" height="14" width="110">
			</object>

			<input size="35" value="{$base_url}{$item_id}" readonly="readonly" type="text" id="link" onclick="this.select()"/>

			<label for="html">для сайта или блога</label>
			 <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" class="clippy" id="clippy" height="14" width="110">
				<param name="movie" value="/flash/clippy.swf">
				<param name="allowScriptAccess" value="always">
				<param name="quality" value="high">
				<param name="scale" value="noscale">
				<param name="FlashVars" value="text=&lt;a href=&quot;{$base_url}{$item_id}/&quot;&gt;{$filename} — {$filesize_text_plain}&lt;/a&gt;">
				<param name="bgcolor" value="#DFEBF7">
				<param name="wmode" value="opaque">
				<embed src="/flash/clippy.swf" name="clippy" quality="high" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="text=&lt;a href=&quot;{$base_url}{$item_id}/&quot;&gt;{$filename} — {$filesize_text_plain}&lt;/a&gt;" bgcolor="#DFEBF7" wmode="opaque" height="14" width="110">
			</object>
			<input size="35" value="&lt;a href=&quot;{$base_url}{$item_id}/&quot;&gt;{$filename} — {$filesize_text_plain}&lt;/a&gt;" readonly="readonly" type="text" id="html" onclick="this.select()"/>

			<label for="bbcode">для форума</label>
			 <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" class="clippy" id="clippy" height="14" width="110">
				<param name="movie" value="/flash/clippy.swf">
				<param name="allowScriptAccess" value="always">
				<param name="quality" value="high">
				<param name="scale" value="noscale">
				<param name="FlashVars" value="text=$links_bbcode_raw">
				<param name="bgcolor" value="#DFEBF7">
				<param name="wmode" value="opaque">
				<embed src="/flash/clippy.swf" name="clippy" quality="high" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="text=$links_bbcode_raw" bgcolor="#DFEBF7" wmode="opaque" height="14" width="110">
			</object>
			$links_bbcode

			<label for="dlink">прямая ссылка</label>
			 <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" class="clippy" id="clippy" height="14" width="110">
				<param name="movie" value="/flash/clippy.swf">
				<param name="allowScriptAccess" value="always">
				<param name="quality" value="high">
				<param name="scale" value="noscale">
				<param name="FlashVars" value="text={$base_url}{$dlink_raw}">
				<param name="bgcolor" value="#DFEBF7">
				<param name="wmode" value="opaque">
				<embed src="/flash/clippy.swf" name="clippy" quality="high" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="text={$base_url}{$dlink_raw}" bgcolor="#DFEBF7" wmode="opaque" height="14" width="110">
			</object>
			<input size="35" value="{$base_url}{$dlink_raw}" readonly="readonly" type="text" id="dlink" onclick="this.select()"/>
		</div>
	</div>
	$desc_block
	$commentsBlock
	</td>
	<td>
		$thumbs_block

	</td>
	</tr>
	</table>
ZZZ;

	$jsBindActionList = '$(".as_js_link_list li span.as_js_link").click(function () { UP.utils.JSLinkListToggle($(this)); });';
	$jsGetCommentsList = "UP.utils.loadCommentsList($item_id)";

	$onDOMReady = $js_spam_warning_block.$js_adult_warning_block.$js_pass_block.$js_thumbs_block.$desc_js_block.$js_video_block.$jsBindActionList.$jsGetCommentsList;
} while (0);

if ($error === 0) {
	require UP_ROOT.'header.php';
	echo ($out);
	require UP_ROOT.'footer.php';
	exit();
} else {
	show_error_message("Ссылка не&nbsp;верна или устарела.");
}
?>
