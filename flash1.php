<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';

$needYUI = true;
$can_upload = true;
$js_flood_warning_block = null;

// antiflood
if (is_upload_flood()) {
	show_error_message('<p>Слишком много загрузок с&nbsp;вашего <nobr>ip-адреса</nobr>.
	<br/>Возможность загрузки для вас отключена на&nbsp;30&nbsp;минут.</p>
	<p>Если вы хотите загружать много файлов одновременно — рекомендуем использовать
	наше экспериментальное расширение для браузера
	Фаирфокс&nbsp;3 <a href="http://forum.lluga.net/viewtopic.php?pid=157347#p157347">iSkip</a> версии&nbsp;1.0.</p>');
} else {
	if (get_upload_flood_counter() > 2 && get_upload_flood_counter() < 5) {
		$js_flood_warning_block = "UP.statusMsg.show('Вы слишком быстро закачиваете. Сделайте паузу.', UP.env.msgWarn, true);";
	}
}

require UP_ROOT.'header.php';
$addScript[] = 'jquery.swf.js';


$geo = get_geo(get_client_ip());
if ($geo == 'world')
{
?>
			<div id="status">&nbsp;</div>
			<h2>Загрузите файл</h2>
				<div class="formRow">
					<div id="uploaderOverlay" style="position:absolute; z-index:2 !important; background: transparent;"></div>
					<div id="selectFilesLink" style="z-index:1 !important;"><a id="selectLink" href="#select">Выберите файл для загрузки</a></div>
					<div id="selectHelp">Максимальный размер — <? echo $GLOBALS['max_file_size']  ?> МБ.</div>
				</div>
				<div class="formRow">
					<div id="selectedFiles"></div>
				</div>


				<div class="formRow">
					<span class="as_js_link dfs" id="advancedUploadLink">дополнительные параметры</span>
				</div>

				<div id="advancedUpload">
					<div class="formRow">
						<table id="UploadFormTable">
						<tr>
						<td>
							<label for="uploadPassword">Пароль</label>
							<input type="password" name="uploadPassword" id="uploadPassword" maxLength="128"/>
							<div class="inputHelp">не более 128 символов</div>
						</td>
						<td id="uploadHiddenTD">
							<label for="uploadHidden">
							<input type="checkbox" name="uploadHidden" id="uploadHidden" value="1"/>
							Сделать скрытым</label>
							<div class="inputHelp">файл не будет показан в списках</div>
						</td>
						</tr>
						</table>
					</div>
					<div class="formRow">
						<label for="uploadDesc">Описание</label>
							<textarea name="uploadDesc" id="uploadDesc" maxLength="512"></textarea>
							<div class="inputHelp">не более 512 символов</div>

					</div>
				</div>

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
	$('#advancedUploadLink').mousedown(function () {
		$('#advancedUpload').slideToggle(70, function () {
			$('#uploadPassword:visible').focus();
		});
		$(this).toggleClass('open');
	});

	$js_flood_warning_block

	UP.uploadFlash.init();
ZZZ;
} else {
?>
	<div id="status">&nbsp;</div>
	<h2>Привет</h2>
	<p>Для гостей из «мира» загрузка файлов отключена. Но вы можете скачивать файлы без каких-либо ограничений.</p>
<?
}
include_once 'footer.php';
?>
