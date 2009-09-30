<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';

$rss_title = 'ап@lluga.net';
$rss_desc = 'RSS-лента файлообменника up.lluga.net';
$rss_link = 'ап@lluga.net';
$rss_num = 20;

date_default_timezone_set ("Europe/Zaporozhye");
$rss_date = date("r");

$cache = new Cache;
if (!$rss_out = $cache->get('rss_lenta')) {
	$rss_out = <<<FMB
<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
<channel>
	<title>$rss_title</title>
	<link>http://up.lluga.net/</link>
	<description>$rss_desc</description>
	<language>ru</language>
	<webMaster>webmaster@lluga.net</webMaster>
	<lastBuildDate>$rss_date</lastBuildDate>
FMB;

	try {
		$db = new DB;
		$datas = $db->getData("SELECT * FROM up WHERE deleted='0' AND hidden='0' AND spam='0' AND adult='0' ORDER BY id DESC LIMIT $rss_num");
	} catch (Exception $e) {
			error($e->getMessage());
	}

	if ($datas) {
		foreach ($datas as $rec) {
			$filename = clear_for_rss($rec['filename']);
			$rss_out .= <<<FMB
	<item>
		<guid>{$base_url}{$rec['id']}/</guid>
		<title>$filename</title>
		<pubDate>{$rec['uploaded_date']}</pubDate>
	</item>
FMB;
		}
	}

	// footer
	$rss_out .= '</channel></rss>';
	$cache->set($rss_out, 'rss_lenta', $cache_timeout_rss);
}

exit($rss_out);



function clear_for_rss($str) {
	$str =  strip_tags($str, "<br>");
	$trans = array('&mdash;' => '-', '&laquo;' => '"', '&raquo' => '"', '?' => '"', '?' => '"');
	$text = strtr($str, $trans);
	return $text;
}

?>
