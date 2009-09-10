<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';

$rss_title = 'ап@lluga.net';
$rss_desc = 'RSS-лента файлообменника up.lluga.net';
$rss_link = 'ап@lluga.net';
$rss_icon = '';
$rss_num = 20;

date_default_timezone_set ("Europe/Zaporozhye");
$rss_date = date("r");

$cache = new Cache;
if (!$rss_out = $cache->get('rss_lenta')) {
	$rss_out = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
<channel>
	<title>$rss_title</title>
	<link>http://up.lluga.net/</link>
	<description>$rss_desc</description>
	<language>ru</language>
	<webMaster>webmaster@lluga.net</webMaster>
	<lastBuildDate>$rss_date</lastBuildDate>
EOD;

	$db = new DB;
	$datas = $db->getData("SELECT * FROM up WHERE deleted='0' AND hidden='0' AND spam='0' AND adult='0' ORDER BY id DESC LIMIT $rss_num");
	if ($datas) {
		foreach ($datas as $rec) {
			$filename = clear_for_rss($rec['filename']);
			$desc = check_plain(clear_for_rss($rec['description']));

			$rss_out .= <<<EOD
	<item>
		<guid>http://up.lluga.net/{$rec['id']}/</guid>
		<title>$filename</title>
		<description>$desc</description>
		<pubDate>{$rec['uploaded_date']}</pubDate>
	</item>
EOD;
		}
	}

	// footer
	$rss_out .= '
</channel>
</rss>
	';
	$cache->set($rss_out, 'rss_lenta', 0);
}


if ($rss_out) {
	echo $rss_out;
}
exit ();


function clear_for_rss($str) {
	$str =  strip_tags($str, "<br>");
	$trans = array('&mdash;' => '-', '&laquo;' => '"', '&raquo' => '"', '?' => '"', '?' => '"');
	$text = strtr($str, $trans);
	return $text;
}

?>
