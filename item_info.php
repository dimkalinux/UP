<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
$addScript[] = 'jquery.tools.min.js';


$error = 0;
do {
	if (!isset ($_GET['item'])) {
		$error = 1;
		break;
	}

	$item_id = intval(get_get('item'), 10);

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
	$filename = check_plain (get_cool_and_short_filename ($row['filename'], 45));
	$filesize = $row['size'];
	$filesize_text = format_filesize ($row['size']);
	$filesize_text_plain = format_filesize_plain ($row['size']);
	$file_date = prettyDate ($row['uploaded_date']);
	$file_last_downloaded_date = check_plain ($row['last_downloaded_date']);
	$downloaded = intval ($row['downloads']);
	$downloaded_text = format_raz ($downloaded);
	$antivir_check_result = $row['antivir_checked'];
	$user_ip = get_client_ip ();
	$desc = htmlspecialchars_decode ($row['description']);
	$hidden = (bool) $row['spam'];
	$is_spam = (bool) $row['spam'];
	$is_adult = (bool) $row['adult'];
	$md5 = $row['md5'];
	$ndi = $row['NDI'];
	$wakkamakka = get_time_of_die ($filesize, $downloaded, $ndi, $is_spam);
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
	$page_title = "Отгрузка файла «${filename}»";


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
	if ($antivir_check_result == ANTIVIR_CLEAN)
		$antivir_check = '<span class="green">вирусов&nbsp;нет</span>';
	else if ($antivir_check_result == ANTIVIR_VIRUS)
		$antivir_check = '<span class="red">файл заражен вирусом</span>';
	else
		$antivir_check = 'пока не проверен';

	// Download row
	if ($downloaded < 1)
		$file_last_downloaded_date = 'неизвестно';
	else
		$downloaded_text .= ', '.$file_last_downloaded_date;

	// SPAM
	$js_spam_warning_block = null;
	if ($is_spam && !$hidden) {
		if ($magic || $im_owner)
			$js_spam_warning_block = "UP.statusMsg.show('Найден СПАМ: cрок хранения сокращён до 2-х дней', UP.env.msgWarn, false);";
		else
			$js_spam_warning_block = "UP.statusMsg.show('Внимание: возможно это СПАМ', UP.env.msgWarn, true);";
	}

	$js_adult_warning_block = null;
	if ($is_adult && !$hidden) {
		$js_spam_warning_block = null;

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
	$pass_input = $js_pass_block = null;
	if ($is_password) {
		$pass_input = '<tr><td class="ab">Пароль:</td><td><input type="password" name="password" minLength="1" maxLength="128"/></td></tr>';
		$js_pass_block = "$('input[name=password]').change(UP.formCheck.search).keyup(UP.formCheck.search).focus(); UP.formCheck.search();";
	}


	// create download link
	$dlink_raw = "/download/$item_id/$dlmValue/";
	$dlink = '<input type="submit" value="Скачать файл" style="font-size: 1.1em; margin: .5em 0;"/>';

	$owner_block = null;
	if ($magic || $im_owner) {
		$md5_link = '';
		if (empty($md5)) {
			$md5_link = <<<FMB
			&nbsp;<span id="owner_md5_link" status="on" class="as_js_link" title="Вычислить контрольную сумму файла" onclick="UP.owner.md5('$item_id', '$magic')">md5</span>
FMB;
		}
		$owner_block = <<<ZZZ
	<tr><td class="ab">управление:</td>
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
		$thumbs_full_url = '/thumbs/'.md5($row['md5'].$item_id).'.jpg';
		$thumbs_preview_small_url = 'http://up.lluga.net/thumbs/'.md5($row['md5'].$item_id).'.jpg';
		$thumbs_preview_url = '/thumbs/large/'.md5($row['md5'].$item_id).'.jpg';
		$thumbs_block = <<<ZZZ
		<div class="thumbs">
			<a href="$thumbs_preview_url"><img src="$thumbs_full_url"/></a>
		</div>
ZZZ;

		$js_thumbs_block = <<<ZZZ
			$(".thumbs a").fancybox({'zoomSpeedIn': 300, 'zoomSpeedOut': 0, 'overlayShow': false, 'hideOnContentClick': true  });
			// preLoad
			var img = new Image;
			$(img).attr("src", "$thumbs_preview_url");

ZZZ;
			$addScript[] .= 'jquery.fancybox-1.2.1.js';
	}


	// flv block
	$flv_block = $js_video_block = null;
	if (is_flv($filename, $row['mime'])) {
		$flv_block = <<<ZZZ
		<tr><td class="ab">видео</td>
		<td class="bb" id="videoBlock">
			<a href="#flvBlock" class="as_js_link" id="fancyVideo">смотреть</span>
		</td>
		<div id="flvBlock" style="display: none;"></div>
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
			$search_like_block = '<tr><td class="ab">похожие файлы</td><td class="bb">'. $similar_num .' <a href="http://up.lluga.net/search/?s='. urlencode($search_filename) .'&amp;doSubmit&amp;ft=1">показать</a></td></tr>';
		}
	} catch (Exception $e) {
		error($e->getMessage());
	}

	$is_password ? $form_method = 'post' : $form_method = 'get';

	// links block
	if ($is_image) {
		$links_bbcode = <<<ZZZ
			<input size="35" value="[url=http://up.lluga.net/{$item_id}/][img]{$thumbs_preview_small_url}[/img][/url]" readonly="readonly" type="text" id="bbcode" onclick="this.select()"/>
ZZZ;
	} else {
		$links_bbcode = <<<ZZZ
			<input size="35" value="[url=http://up.lluga.net/{$item_id}/]{$filename} — {$filesize_text_plain}[/url]" readonly="readonly" type="text" id="bbcode" onclick="this.select()"/>
ZZZ;
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
		$desc_link = '|&nbsp;&nbsp;<span class="as_js_link" onclick="$(\'#links_block\').hide(); $(\'#desc_block\').toggle(0);">описание</span>';
	}


	$out = <<<ZZZ
	<div id="status">&nbsp;</div>
	$im_owner_block
	<h2 id="item_info_filename">$filename</h2>
	<form method="$form_method" action="/download/$item_id/$dlmValue/" autocomplete="off">
	<table class="asDiv">
	<tr><td style="vertical-align: top;">
		<table class="t1" id="file_info_table">
			<tr><td class="ab">размер</td><td class="bb">$filesize_text</td></tr>
			<tr><td class="ab">скачан</td><td class="bb">$downloaded_text</td></tr>
			<tr><td class="ab">срок хранения</td><td class="bb">$wakkamakka_text</td></tr>
			$mp3_block
			$flv_block
			$owner_block
			$pass_input
			$search_like_block
			<tr><td class="ab"></td><td class="bb"><div id="download_link">$dlink</div></td></tr>
			<tr>
				<td class="ab"></td>
				<td class="bb" style="color: #666;">
				<span class="as_js_link" onclick="$('#desc_block').hide(); $('#links_block').toggle(0);">ссылки на файл</span>
				&nbsp;
				$desc_link
				</td>
			</tr>
		</table>
	</form>

	<div id="links_block">
		<div class="tt-wedge tt-wedge-up tt-wedge-links"></div>
		<div id="formBlock">
			<label for="link">ссылка</label>
			<input size="35" value="http://up.lluga.net/$item_id/" readonly="readonly" type="text" id="link" onclick="this.select()"/>

			<label for="html">для сайта или блога</label>
			<input size="35" value="&lt;a href=&quot;http://up.lluga.net/$item_id/&quot;&gt;{$filename} — {$filesize_text_plain}&lt;/a&gt;" readonly="readonly" type="text" id="html" onclick="this.select()"/>

			<label for="bbcode">для форума</label>
			$links_bbcode

			<label for="dlink">прямая ссылка (только для скачивания)</label>
			<input size="35" value="http://up.lluga.net{$dlink_raw}" readonly="readonly" type="text" id="dlink" onclick="this.select()"/>
		</div>
	</div>
	$desc_block
	</td>
	<td>
		$thumbs_block

	</td>
	</tr>
	</table>
ZZZ;

	$onDOMReady = $js_spam_warning_block.$js_adult_warning_block.$js_pass_block.$js_thumbs_block.$desc_js_block.$js_video_block;
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
