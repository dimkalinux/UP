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
		<p>Активность пользователей сервиса.</p>
		<div id="result"><? echo $pe; ?></div>
<?
$addScript[] = 'jquery.sha1.js';
$onDOMReady = <<<ZZZ
		$('#wrap').everyTime(7000, '123', UP.utils.getPE);
ZZZ;

require UP_ROOT.'footer.php';
?>
