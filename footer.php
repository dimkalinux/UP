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
			<li><a href="/map/">Карта сайта</a></li>
			<li><a href="/agreement/">Пользовательское соглашение</a></li>
			<li><a href="/feedback/" title="Форма для связи с администрацией сервиса">Обратная связь</a></li>
			<li><a href="http://forum.lluga.net/forum/up/" title="Форум технчиеской поддержки">Форум</a></li>
			<li><a href="http://twitter.com/up_ua" title="Твиттер проекта">@up_ua</a></li>
		</ul>
		<div class="clear separator"></div>
			<p>
		© <? date_default_timezone_set ("Europe/Zaporozhye"); echo (date ("Y")); ?> <a href="/">iTeam</a>.</p>
		</div>
		<div id="footerBottom">&nbsp;</div>
		<script src="/js/jquery-1.3.2.min.js" type="text/javascript"></script>
		<script src="/js/up.js" type="text/javascript"></script>
<?php

//
if (isset($addScript) && is_array ($addScript) && count ($addScript) > 0) {
	foreach ($addScript as $script) {
		echo ('<script src="/js/'.check_plain($script).'" type="text/javascript"></script>');
	}
}

//
if (isset($onDOMReady)) {
	echo ('<script type="text/javascript">jQuery(function () { window.setTimeout(function () {'.$onDOMReady.'}, 100);});</script>');
}

?>
	<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
		try {
			var pageTracker = _gat._getTracker("UA-6106025-1");
			pageTracker._trackPageview();
		} catch(err) {}
	</script>

<?
define('UP_FOOTER', 1);
?>
</body>
</html>
