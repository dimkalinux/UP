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
			<li><a href="<?php echo $base_url; ?>map/">Карта сайта</a></li>
			<li><a href="<?php echo $base_url; ?>agreement/">Пользовательское соглашение</a></li>
			<li><a href="<?php echo $base_url; ?>feedback/" title="Форма для связи с администрацией сервиса">Обратная связь</a></li>
			<li><a href="http://forum.lluga.net/forum/up/" title="Форум технчиеской поддержки">Форум</a></li>
			<li><a href="http://twitter.com/up_ua" title="Твиттер проекта">@up_ua</a></li>
		</ul>
		<div class="clear separator"></div>
			<p>
		© <? date_default_timezone_set ("Europe/Zaporozhye"); echo (date ("Y")); ?> <a href="http://iteam.net.ua/">iTeam</a>.</p>
		</div>
		<div id="footerBottom">&nbsp;</div>
		<script src="<?php echo JS_BASE_URL; ?>js/jquery-1.3.2.min.js" type="text/javascript"></script>
		<script src="<?php echo JS_BASE_URL_1; ?>js/up.js" type="text/javascript"></script>
<?php


// ADDON JS-SCRIPT BLOCK
if (isset($addScript) && is_array($addScript) && count($addScript) > 0) {
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
<?php //ob_end_flush(); ?>
