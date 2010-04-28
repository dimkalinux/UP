<?
// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}
?>
		</div>
	</div>

	<div id="footer">
		<ul class="sitenav">
			<li><a href="http://portal.iteam.net.ua/">Портал</a></li>
			<li><a href="<?php echo $base_url; ?>map/">Карта сайта</a></li>
			<li><a href="<?php echo $base_url; ?>agreement/">Пользовательское соглашение</a></li>
			<li><a href="<?php echo $base_url; ?>feedback/" title="Форма для&nbsp;связи с&nbsp;администрацией сервиса">Обратная связь</a></li>
			<li><a href="<?php echo $base_url; ?>help/" title="Помощь сервиса">Справка</a></li>
			<li><a href="http://forum.lluga.net/forum/52/" title="Форум технической поддержки">Форум</a></li>
			<li><a href="http://twitter.com/up_ua" title="Твиттер проекта">@up_ua</a></li>
		</ul>
		<div class="clear separator"></div>
		<p>©&nbsp;<? date_default_timezone_set ("Europe/Zaporozhye"); echo (date ("Y")); ?> <a href="http://iteam.net.ua/">iTeam</a></p>
	</div>
	<div id="footerBottom">&nbsp;</div>
	<script src="<?php echo JS_BASE_URL; ?>js/up.js" type="text/javascript"></script>
<?php


// ADDON JS-SCRIPT BLOCK
if (isset($addScript) && is_array($addScript) && count($addScript) > 0) {
	// remove non-uniq values
	$addScript = array_unique($addScript);
	foreach ($addScript as $script) {
		echo '<script src="/js/'.check_plain($script).'" type="text/javascript"></script>';
	}
}


// ON-DOM-READY BLOCK
if (isset($onDOMReady)) {
	echo '<script type="text/javascript">jQuery(function () { '.$onDOMReady.'});</script>';
}


// GOOGLE ANALYTICS BLOCK
if (isset($googleAnalyticsCode) && !empty($googleAnalyticsCode)) {
	$gaCodeBlock = <<<FMB
<script type="text/javascript">$(document).ready( function() { $.ga.load('$googleAnalyticsCode'); } );</script>
FMB;
	echo $gaCodeBlock;
}

define('UP_FOOTER', 1);
?>
</body>
</html>
