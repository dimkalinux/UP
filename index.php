<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';
require UP_ROOT.'include/upload.inc.php';

$can_upload = true;
$unuiq = uniqid();
$js_flood_warning_block = null;

$Upload = new Upload;
// antiflood
if ($Upload->is_upload_flood()) {
	show_error_message('<p>Слишком много загрузок с&nbsp;вашего <nobr>ip-адреса</nobr>.
	<br/>Возможность загрузки для вас отключена на&nbsp;30&nbsp;минут.</p>
	<p>Если вы хотите загружать много файлов одновременно — рекомендуем использовать <a href="/ftp_access/">доступ по фтп-протоколу</a>.</p>');
} else {
	$uploadFloodCounter = $Upload->get_upload_flood_counter();
	if ($uploadFloodCounter > 2 && $uploadFloodCounter < 5) {
		$js_flood_warning_block = "UP.statusMsg.show('Вы слишком быстро закачиваете. Сделайте паузу на пару минут.', UP.env.msgWarn, true);";
	}
}

require UP_ROOT.'header.php';

$geo = get_geo(get_client_ip());
if ($geo != 'world') {
?>
			<div id="status">&nbsp;</div>
			<h2>Загрузите файл</h2>
			<form id="uploadForm" method="post" enctype="multipart/form-data" action="/upload" target="target_upload" autocomplete="off">
				<iframe id="target_upload" name="target_upload" src="about:blank"></iframe>
				<div class="formRow">
					<input value="<?php echo $unuiq; ?>" name="progress_id" type="hidden" id="progress_id"/>
					<input value="<?php echo ($GLOBALS['max_file_size']*1048576); ?>" name="MAX_FILE_SIZE" type="hidden"/>
					<input name="file" id="uploadFile" type="file" tabindex="10"/>
					<input value="Закачать"type="submit" tabindex="11" id="uploadSubmit" disabled="disabled"/>
				</div>
				<div class="formRow">
					<span class="as_js_link dfs" id="advancedUploadLink">дополнительные параметры</span>
				</div>
				<div id="advancedUpload">
					<div class="tt-wedge tt-wedge-up"></div>
					<div id="formBlock">
						<div class="formRow">
							<table id="UploadFormTable">
							<tr>
							<td>
								<label for="uploadPassword">Пароль</label>
								<input type="password" name="uploadPassword" id="uploadPassword" maxLength="128"/>
								<div class="inputHelp">не&nbsp;более 128&nbsp;символов</div>
							</td>
							<td id="uploadHiddenTD">
								<label for="uploadHidden">
								<input type="checkbox" name="uploadHidden" id="uploadHidden" value="1"/>
								Сделать скрытым</label>
								<div class="inputHelp">файл не&nbsp;будет показан в&nbsp;списках</div>
							</td>
							</tr>
							</table>
						</div>
						<div class="formRow">
							<label for="uploadDesc">Описание</label>
								<textarea name="uploadDesc" id="uploadDesc" maxLength="512"></textarea>
								<div class="inputHelp">не&nbsp;более 512&nbsp;символов</div>

						</div>
					</div>
				</div>
				<br class="clear"/>
			</form>



			<div id="upload_status" class="dfs">&nbsp;</div>
			<div id="upload_progress" class="dfs">
				<div id="upload_info">
					<div class="x-progress-wrap left-align">
						<div class="x-progress-inner">
							<div style="width: 0%;" id="num_progress" class="x-progress-bar"></div>
							<div id="ext-gen10" class="x-progress-text x-progress-text-back"></div>
					</div>
				</div>
				<div id="progress_text"></div>
			</div>
			</div>
			<noscript>Сервис требует браузера с включённым JavaScript</noscript>
<?
$onDOMReady = <<<ZZZ
	window.setTimeout(function(){
		$("input[type='file']").bind("change", UP.formCheck.upload).bind("keyup", UP.formCheck.upload);
		UP.formCheck.upload();

		// form
		var options = {
			dataType: 'json',
			resetForm: true,
			cleanForm: true,
			url: '/upload',
			type: 'POST',
			success: function (r) {
				if (r.error == 0) {
					UP.uploadForm.finish(r.id, r.pass);
				} else {
					UP.uploadForm.error(r.message);
				}
			},
			error: function (r) {
				UP.uploadForm.error("Сервер загрузки недоступен. Попробуйте немного позже.");
			}
		};

		$("#uploadForm").bind("submit", function () {
			var canUpload = false;
			UP.statusMsg.clear();

			if ($('#uploadFile').val().length < 1) {
				UP.statusMsg.show('Выберите файл для загрузки', UP.env.msgError, true);
				return;
			}

			if ($('#advancedUpload').is(':visible')) {
				$('#advancedUpload').slideToggle();
				$('#advancedUploadLink').toggleClass('open');
			}


			$('#wrap').oneTime(400, 'selectUploadServer', function () {
				$('#upload_status')
				.html('Ожидайте, выбирается сервер для загрузки&hellip; <a href="/" id="link_abort_upload">отменить</a>')
				.fadeIn(250);
			});

			// get upload url
			$.ajaxSetup({async: false});
			$.getJSON(UP.env.ajaxBackend +'?t_action=' +UP.env.actionGetUploadUrl +'&t=' +UP.utils.gct(), function (data) {
				$.ajaxSetup({async: true});
				$('#wrap').stopTime('selectUploadServer');
				if (parseInt(data.result, 10) === 1) {
					options.url = data.message +'?X-Progress-ID=' +$('#progress_id').val();
					canUpload = true;
				} else {
					UP.uploadForm.error("Нет свободных серверов. Попробуйте немного позже.");
				}
			});


			if (canUpload == true) {
				$(this).ajaxSubmit(options);
				UP.uploadForm.start();
			}

			return false;
		});


		$('#advancedUploadLink').click(function () {
			$('#advancedUpload').slideToggle(0, function () {
				$('#uploadPassword:visible').focus();
			});
			$(this).toggleClass('open');
		});


		$js_flood_warning_block


		// at the end
		$('#uploadFile').focus();
	},100);
ZZZ;
} else {
?>
	<div id="status">&nbsp;</div>
	<h2>Привет</h2>
	<p>Для гостей из «мира» загрузка файлов отключена.<br/>Но вы можете скачивать <a href="/top/">файлы</a> без каких-либо ограничений.</p>
<?
}
include_once 'footer.php';
?>
