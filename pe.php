<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';

try {
	$pe = get_pe();
} catch (Exception $e) {
	error($e->getMessage());
}

require UP_ROOT.'header.php';
?>
		<div id="status">&nbsp;</div>
		<h2>Прямой эфир</h2>
		<p>Активность пользователей сервиса.
			<!--<span id="peReload">
				<a href="/pe.php" id="peReloadLink">Обновить</a>
				<span class="as_js_link" id="peReloadJS" style="display: none;">Обновить</span>
			</span>-->
		</p>
		<div id="result" style="margin-top: 1.3em;"><? echo $pe; ?></div>
<?
$addScript[] = 'jquery.sha1.js';
$onDOMReady = <<<ZZZ
		//$("#peReloadJS").click(UP.utils.getPE).show();
		//$("#peReloadLink").hide();
		$('#wrap').everyTime(7000, '123', UP.utils.getPE);
ZZZ;

require UP_ROOT.'footer.php';
?>
