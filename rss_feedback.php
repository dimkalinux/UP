<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';

date_default_timezone_set ("Europe/Zaporozhye");
setlocale(LC_TIME, 'ru_RU', 'ru_RU.utf8', 'ru');

$rss_title = 'Feedback up.lluga.net';
$rss_desc = 'Feedback up.lluga.net';
$rss_link = 'http://up.lluga.net/feedback/';
$rss_icon = '';
$rss_num = 50;
$rss_date = date("r");

$cache = new Cache;
if (!$rss_out = $cache->get('RSS_FEEDBACK'))
{
	$rss_out = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss" xmlns:creativeCommons="http://backend.userland.com/creativeCommonsRssModule" xmlns:dtvmedia="http://participatoryculture.org/RSSModules/dtv/1.0" >
<channel>
	<title>$rss_title</title>
	<link>http://up.lluga.net/</link>
	<description>$rss_desc</description>
	<language>ru</language>
	<webMaster>dimkalinux@gmail.com</webMaster>
	<lastBuildDate>$rss_date</lastBuildDate>
EOD;

	$db = new DB;
	$datas = $db->getData("SELECT * FROM feedback ORDER BY id DESC LIMIT $rss_num");

	if ($datas) {
		foreach ($datas as $rec) {
			$id = (int) $rec['id'];
			$date = clear_for_rss($rec['date']);
			$email = clear_for_rss($rec['email']);
			$file = $rec['file'];
			$message = clear_for_rss($rec['message']);

			if ($email ) {
				$title = $email;
			} else {
				$title = 'No title';
			}

			if ($file) {
				$file = '<enclosure url="http://up.lluga.net/up_feedback/'.$file.'"/>';
			}

			$rss_out .= <<<EOD
	<item>
		<guid>http://up.lluga.net/feedback/{$rec['id']}</guid>
		<title>$title</title>
		<description><![CDATA[ $message ]]></description>
		<pubDate>$date</pubDate>
		$file
	</item>

EOD;
		}
	}

	// footer
	$rss_out .= '
</channel>
</rss>
	';
	$cache->set($rss_out, 'RSS_FEEDBACK', 600);
}

echo $rss_out;
exit();

function clear_for_rss($str) {
	$str =  strip_tags ($str, "<br>");
	$trans = array ('&mdash;' => '-', '&laquo;' => '"', '&raquo' => '"', '?' => '"', '?' => '"');
	$text = strtr ($str, $trans);
	return $text;
}


?>
