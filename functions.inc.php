<?php

if (!defined('UP_ROOT')) {
	exit('The constant UP_ROOT must be defined.');
}


mb_internal_encoding("UTF-8");

// Reverse the effect of register_globals
up_unregister_globals();

// Ignore any user abort requests
ignore_user_abort(TRUE);

// Attempt to load the configuration file config.php
if (file_exists(UP_ROOT.'config.inc.php')) {
	include UP_ROOT.'config.inc.php';
}

if (!defined('UP')) {
	die("Файл конфигурации «config.inc.php» не найден или повреждён.");
}

// Block prefetch requests
if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch') {
	header('HTTP/1.1 403 Prefetching Forbidden');

	// Send no-cache headers
	header('Expires: Thu, 21 Jul 1977 07:30:00 GMT');	// When yours truly first set eyes on this world! :)
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: post-check=0, pre-check=0', FALSE);
	header('Pragma: no-cache');		// For HTTP/1.0 compability

	exit;
}

// only for debug
if (DEBUG === TRUE) {
	error_reporting(8191);
}

// load all libs
require_once UP_ROOT.'include/db.inc.php';
require_once UP_ROOT.'include/logger.inc.php';
require_once UP_ROOT.'include/cache_empty.inc.php';
require_once UP_ROOT.'include/storage.inc.php';
require_once UP_ROOT.'include/gravatar.inc.php';
require_once UP_ROOT.'include/user.inc.php';


// remove status cookie
if ('item_info.php' !== strtolower(substr(strrchr($_SERVER['SCRIPT_FILENAME'], "/"), 1))) {
	setcookie("up_itemInfoStatusCookie", '', time() - 36000);
}

// get user info
try {
	$user = User::getCurrentUser();
} catch(Exception $e) {
	error($e->getMessage());
}


// is admin rights?
if (defined('ADMIN_PAGE')) {
	if ($user['is_admin'] !== TRUE) {
		show_error_message('Доступ защищён зарослями фиалок и&nbsp;лютиков.');
		exit();
	}
}



function check_plain($text) {
  	return htmlspecialchars ($text, ENT_QUOTES);
}

function get_safe_string($str) {
	return preg_replace ("/[^a-z0-9]/i", "", $str);
}


function get_post($str) {
	if (isset($_POST[$str])) {
		if (preg_match("/^([a-z0-9\.\:\,\*\?\-\%\@\+\ ]*)$/i", $_POST[$str])) {
			return $_POST[$str];
		}
	}

	return null;
}

function get_get($str) {
	if (isset($_GET[$str])) {
		if (preg_match ("/^[a-z0-9_]{1,20}$/", $_GET[$str])) {
			return $_GET[$str];
		}
	}

	return null;
}


function format_filesize_plain($bytes) {
	if ($bytes < 1024) {
		return "${bytes}&nbsp;б";
	} else if ($bytes < 1048576) {
		return round(($bytes/1024), 1).'&nbsp;КБ';
	} else if ($bytes < 1073741824) {
		return round(($bytes/1048576), 1).'&nbsp;МБ';
	} else if ($bytes < 1099511627776) {
		return round(($bytes/1073741824), 1).'&nbsp;ГБ';
	} else {
		return round(($bytes/1099511627776), 2).'&nbsp;ТБ';
	}
}


function format_filesize($bytes, $quoted=FALSE) {
	$span_start = ($quoted === TRUE) ? '<span class=\"filesize\">' : '<span class="filesize">';

	if ($bytes < 1024) {
		return "${bytes}&nbsp;".$span_start.'б</span>';
	} else if ($bytes < 1048576) {
		return round(($bytes/1024), 1).'&nbsp;'.$span_start.'КБ</span>';
	} else if ($bytes < 1073741824) {
		return round(($bytes/1048576), 1).'&nbsp;'.$span_start.'МБ</span>';
	} else if ($bytes < 1099511627776) {
		return round(($bytes/1073741824), 1).'&nbsp;'.$span_start.'ГБ</span>';
	} else {
		return round(($bytes/1099511627776), 2).'&nbsp;'.$span_start.'ТБ</span>';
	}
}


function format_speed($bytes, $raw=FALSE) {
	$space = ($raw === TRUE) ? ' ' : '&nbsp;';
	$bytes *= 8;

	// bytes
	if ($bytes < 1000) {
		return "${bytes}{$space}б/с";
	} else if ($bytes < 1000000) {
		return round(($bytes/1000), 1)."{$space}кб/с";
	} else if ($bytes < 1000000000) {
		return round(($bytes/1000000), 1)."{$space}Мб/с";
	} else if ($bytes < 1000000000000) {
		return round(($bytes/1000000000), 1)."{$space}Гб/с";
	} else {
		return round(($bytes/1000000000000), 1)."{$space}Тб/с";
	}
}


function time_to_string($value, $str1, $str2, $str5) {
	if (!$value) {
		return 0;
	}

	$mod = $value % 10;

	if (($value % 100) >= 10 && ($value % 100) <= 19) {
		return $str5;
	}

	if ($mod == 1) {
		return $str1;
	}

	if ($mod >= 2 && $mod <= 4) {
		return $str2;
	}

	return $str5;
}


function format_seconds($sec) {
	if ($sec > 59) {
		return $sec.'&nbsp;'.time_to_string ($sec, 'секунда', 'секунды', 'секунд');
	} else {
		return "меньше минуты";
	}
}

function format_minutes($min) {
	if ($min != 0) {
		return $min.'&nbsp;'.time_to_string ($min, 'минута', 'минуты', 'минут');
	} else {
		return "меньше часа";
	}
}

function format_days($day) {
	if ($day == 0) {
		return "сегодня";
	} else {
		return $day.'&nbsp;'.time_to_string ($day, 'день', 'дня', 'дней');
	}
}


function format_raz($num=0) {
	if ($num == 0) {
		return '0&nbsp;<span class="filesize">раз</span>';
	}

	return $num.'&nbsp;'.time_to_string ($num, '<span class="filesize">раз</span>', '<span class="filesize">раза</span>', '<span class="filesize">раз</span>');
}


function show_error_message($message) {
	$out = <<<FMB
	<div id="status">&nbsp;</div>
	<h2>Ошибка</h2>
	<div class="message">$message</div>
FMB;
	printPage($out);
	exit();
}


function clear_stat_cache() {
	$cache = new Cache();
	$cache->clearStat();
}


function get_cool_and_short_filename($str, $max_len=55) {
	if (mb_strlen($str) > $max_len) {
		$str = mb_substr($str, 0 , 39).'… '.mb_substr($str, -10);
	}
	return $str;
}


function get_file_ext($file_name) {
	if (mb_strlen($file_name) == 0) {
		return null;
	}

	return strtolower(substr(strrchr($file_name, "."), 1));
}


function is_mp3($file_name, $mime) {
	if ('mp3' == get_file_ext($file_name)) {
		return TRUE;
	} else {
		return FALSE;
	}
}


function is_mp3_by_mime($mime) {
	$valid_mime = array('application/x-force-download', 'audio/mpeg', 'application/force-download');

	return in_array($mime, $valid_type);
}


function is_image_by_ext($file_name) {
	$valid_type = array('jpg', 'jpeg', 'png', 'gif');
	$type = get_file_ext($file_name);

	return in_array($type, $valid_type);
}


function is_can_be_adult($file_name) {
	$valid_type = array('avi', 'wmv', 'mpeg', 'mgp', 'zip', 'rar', 'bat', 'exe');
	$type = get_file_ext($file_name);

	return in_array($type, $valid_type);
}


function is_archive($file_name) {
	$valid_type = array('zip', 'tar', 'rar', 'gz', 'bz2', 'gz', 'arj', 'lha', 'cue', 'mp3');
	$type = get_file_ext($file_name);

	return in_array($type, $valid_type);
}


function is_image_by_mime($full_filename) {
	$valid_mime = array('image/jpg', 'image/jpeg', 'image/png', 'image/gif');
	$a = getimagesize($full_filename);
	$mime = $a['mime'];

	return in_array($mime, $valid_mime);
}


function is_image($file_name, $full_filename) {
	$is_image = FALSE;
	$is_valid_type = FALSE;
	$is_valid_mime = FALSE;

	$is_valid_type = is_image_by_ext($file_name);

	if (!$is_valid_type) {
		return FALSE;
	}

	// 3. check mime
	$is_valid_mime = is_image_by_mime($full_filename);

	//
	return ($is_valid_type && $is_valid_mime);
}


function is_flv($file_name, $mime) {
	return (get_file_ext($file_name) == 'flv' || get_file_ext($file_name) == 'mov');
}

function get_client_ip() {
	if (isset ($_SERVER['REMOTE_ADDR'])) {
		return $_SERVER['REMOTE_ADDR'];
	} else {
		return null;
	}
}


function ip_check($IP, $CIDR) {
    	list ($net, $mask) = split ("/", $CIDR);

    	$ip_net = ip2long ($net);
    	$ip_mask = ~((1 << (32 - $mask)) - 1);

    	$ip_ip = ip2long ($IP);
    	$ip_ip_net = $ip_ip & $ip_mask;

    	return ($ip_ip_net == $ip_net);
}


function get_geo() {
	$apache_geo = 'world';	// default is 'world'

	if (!function_exists('apache_request_headers')) {
		return $apache_geo;
	}

	$headers = apache_request_headers();
	if ($headers && isset($headers['X-GEO'])) {
		$apache_geo = $headers['X-GEO'];
	}

	return $apache_geo;
}


// size in bytes
// ndi in days
function get_time_of_die($size, $downloads, $ndi, $spam) {
	$is_popular = (bool)($downloads >= $GLOBALS['popular_num']);
	$wakkamakka = 0;

	if ($spam) {
		$wakkamakka = $GLOBALS['non_downloaded_spam_interval'] - $ndi;
	} else if (($downloads <= $non_downloaded_count) && ($size >= ($GLOBALS['very_small_file_size']*1048576))) {
		$wakkamakka = $GLOBALS['non_downloaded_interval'] - $ndi;
	} else if ($size <= ($GLOBALS['very_small_file_size'] * 1048576)) {
		$wakkamakka = ($is_popular ? $GLOBALS['non_downloaded_very_small_files_popular_interval'] : $GLOBALS['non_downloaded_very_small_files_interval']) - $ndi;
	} else if ($size > ($GLOBALS['small_file_size'] * 1048576)) {
		$wakkamakka = ($is_popular ? $GLOBALS['non_downloaded_big_files_popular_interval'] : $GLOBALS['non_downloaded_big_files_interval']) - $ndi;
	} else {
		$wakkamakka = ($is_popular ? $GLOBALS['non_downloaded_small_files_popular_interval'] : $GLOBALS['non_downloaded_small_files_interval']) - $ndi;
	}

	return $wakkamakka;
}



function is_spam($str) {
	if (!$str) {
		return FALSE;
	}

	$a_patterns = array(
	'[пp][лl][иie][зz]',
	'качать\sвсем',
	'закинь',
	'пидар',
	'[зz][аa][лl][еe]й',
	'[дd][аa]й[тt][еei]',
	'[сc][кk][иie][нnh]ь*[тt][еei]',
	'[кkc][аa]ч[аa]й*[тt][еei]',
	'[пp][рpr][оo][сcs]ь*[бb][аa]',
	'[пp][оo]ж[аa][лl][уyu]й*[сcs]*[тt]*[аa]*');

	$is_spam = FALSE;

	foreach ($a_patterns as $pattern) {
		$pattern = '/'.$pattern.'/ui';

		if (preg_match($pattern, $str)) {
			$is_spam = TRUE;
			break;
		}
	}

	return $is_spam;
}


function is_adult($str) {
	if (!$str || !is_can_be_adult($str)) {
		return FALSE;
	}

	$a_patterns = array(
	'(_|\W|^)teen(_|\W|^)',
	'(_|\W|^)lesbi(_|\W|^)',
	'sex',
	'(_|\W|^)[pп][oо][rр][nн][oо](_|\W|^)',
	'pus[s*]y',
	'секс',
	'(_|\W|^)tits(_|\W|^)',
	'(_|\W|^)sperma(_|\W|^)',
	'(_|\W|^)anal(_|\W)',
	'(_|\W|^)pron(_|\W)',
	'masturb',
	'minet',
	'(_|\W|^)ass(_|\W)',
	'(_|\W|^)cum(_|\W)',
	'(_|\W|^)incest(_|\W)',
	'(_|\W|^)порево(_|\W)',
	'порево',
	'fuck'
	);

	$is_adult = FALSE;

	foreach ($a_patterns as $pattern) {
		$pattern = '/'.$pattern.'/ui';

		if (preg_match($pattern, $str)) {
			$is_adult = TRUE;
			break;
		}
	}

	return $is_adult;
}


function prettyDate($mysqlDate) {
	date_default_timezone_set("Europe/Zaporozhye");
	setlocale(LC_TIME, 'ru_RU', 'ru_RU.utf8', 'ru');

	$diff = date_format(date_create('now'), 'U') - date_format(date_create($mysqlDate), 'U');
    $dayDiff = floor($diff / 86400);
    $defaultDateFormat = date_format(date_create($mysqlDate), 'M j');

 	if(is_nan($dayDiff) || $dayDiff < 0) {
    	return '';
	}

	if ($dayDiff == 0) {
            if ($diff < 60) return 'сейчас';
            /*elseif ($diff < 120) 	return 'минуту назад';
            elseif($diff < 3600) 	return format_minutes(floor($diff/60));
            elseif($diff < 7200) 	return 'час назад';*/
            elseif($diff < 86400) 	return 'сегодня';//return floor($diff/3600) . ' часов';*/
    } elseif ($dayDiff == 1) {
		return 'вчера';
	} else {
		return $defaultDateFormat;
	}
}


function makeSearch($req, $fooltext=FALSE, $useSaveFeature=FALSE) {
	global $user;
	$out = '';
	$regexp = (bool) preg_match('/\*|\?/u', $req);

	try {
		$db = new DB;

		if ($fooltext === TRUE) {
			$datas = $db->getData("SELECT * FROM up WHERE
				deleted='0' AND
				hidden='0' AND
				spam='0' AND
				adult='0' AND
				MATCH (filename) AGAINST (? IN BOOLEAN MODE) LIMIT 100", "{$req}*");
		} else {
			if (!$regexp) {
				$query = '%'.$req.'%';
			} else {
				$trans = array('*' => '%', '?' => '_');
				$query = strtr($req, $trans);
			}

			$datas = $db->getData("SELECT * FROM up WHERE
					deleted='0'
				AND hidden='0'
				AND spam='0'
				AND adult='0'
				AND filename LIKE ? LIMIT 100", $query);
		}

		/*$saveThisSearch = ;
		if ($useSaveFeature) {*/
			$saveThisSearch = $db->numRows("SELECT * FROM searchs WHERE user_id=? AND searchs=?", $user['id'], $req);
		//}
	} catch (Exception $e) {
		throw new Exception($e->getMessage());
	}

	if ($datas) {
		$r = '';

		foreach ($datas as $rec) {
			$item_id = $rec['id'];
			$fullFilename = htmlspecialchars_decode(stripslashes($rec['filename']));
			$filename = get_cool_and_short_filename($fullFilename, 55);
			$filesize_text = format_filesize ($rec['size']);
			$downloaded = $rec['downloads'];
			$file_date = $rec['uploaded_date'];

			$r .= <<<FMB
			<tr>
				<td class="right size">$filesize_text</td>
				<td class="left name"><a rel="nofollow" href="/$item_id/">$filename</a></td>
				<td class="center download">$downloaded</td>
			</tr>
FMB;
		}

		if ($saveThisSearch === 0) {
			$saveSearchLink = <<<FMB
			<span class="as_js_link">сохранить этот поиск</span>
FMB;
		} else {
			$saveSearchLink = <<<FMB
			<span class="as_js_link">удалить этот поиск</span>
FMB;
		}

		// make all answer with header
		$out = <<<FMB
			<h3>Результаты поиска</h3>
			<table class="t1" id="search_files_table">
			<thead>
			<tr>
				<th class="right">Размер</th>
				<th class="left">Имя файла</th>
				<th class="center">Скачан</th>
			</tr>
			</thead>
			<tbody>
				$r
			</tbody>
			</table>
FMB;
	}

	return $out;
}


function get_download_logs() {
	$cache = new Cache;
	if (!$a = $cache->get('get_download_logs')) {
		$a = array('lds'=>0, 'iteam'=>0, 'office'=>0, 'world'=>0, 'lds_p'=>0, 'iteam_p'=>0, 'world_p'=>0, 'lluga'=>0);
		$handle = @fopen("/var/log/nginx/files_access_log", "r");
		if ($handle) {
			while (!feof($handle)) {
				$line = chop(fgets($handle));
				if (preg_match('/(\S+)\s(\d+)\s(\d+)\s(\S+)/', $line, $matches)) {
					$geo = $matches[1];
					$status = intval($matches[2], 10);
					$size = $matches[3];

					// Good value only in Good status
					if ($status == 200 || $status == 206) {
						$a[$geo] += $size;
					}
				}
			}
			fclose($handle);
		}

		// add old logs value
		$a['lds'] += 52*1099511627776;
		$a['iteam'] += 68*1099511627776;
		$a['world'] += 15*1099511627776;

		// percents
		$all = $a['lds'] + $a['iteam'] + $a['office'] + $a['world'];
		$a['lds_p'] = round ($a['lds']/$all, 3)*100;
		$a['iteam_p'] = round (($a['iteam'] + $a['office'])/$all, 3)*100;
		$a['world_p'] = round ($a['world']/$all, 3)*100;
		//
		$a['lds'] = format_filesize($a['lds']);
		$a['iteam'] = format_filesize($a['iteam'] + $a['office']);
		$a['world'] = format_filesize($a['world']);

		$cache->set($a, 'get_download_logs', 3600);
	}

	return $a;
}


function get_pe() {
	$out = '';

	try {
		$db = new DB;
		$datas = $db->getData("SELECT dnow.id, up.filename, n, type FROM dnow
				LEFT JOIN up
				ON dnow.id = up.id
				ORDER BY ld DESC, n DESC LIMIT 20");
	} catch (Exception $e) {
		throw new Exception($e->getMessage());
	}

	if ($datas) {
		$out = <<<FMB
		<table class="t1" id="pe_files_table">
		<tbody>
FMB;
		foreach ($datas as $item) {
			$item_id = intval ($item['id']);
			$n = intval ($item['n']);
			$filename = get_cool_and_short_filename ($item['filename'], 40);
			$type = $item['type'];

			$out .= <<<FMB
			<tr><td class="first left $type"><a rel="nofollow" href="/$item_id/">$filename</a></td></tr>
FMB;
		}

		$out .= <<<FMB
		</tbody>
		</table>
FMB;
	}

	return $out;
}


function get_similar_count($req, $id) {
	if (mb_strlen($req) < 3) {
		return 0;
	}

	try {
		$db = new DB;
		$row = $db->getRow("SELECT COUNT(*) AS n FROM up WHERE
				id!=?
			AND deleted='0'
			AND hidden='0'
			AND spam='0'
			AND adult='0'
			AND MATCH (filename) AGAINST (? IN BOOLEAN MODE) LIMIT 100", $id, "{$req}*");
	} catch (Exception $e) {
		throw new Exception($e->getMessage());
	}

	return intval($row['n'], 10);
}


function convert_filename_to_similar($filename) {
	$rs = '';
	setlocale(LC_TIME, 'ru_RU', 'ru_RU.utf8', 'ru');

	$is_archive = is_archive($filename);
	// 1. remove ext
	$rs = remove_file_ext($filename);

	// 2. for archive doit twise
	if ($is_archive) {
		$rs = remove_file_ext($rs);
	}

	// 3. remove digits
	$rs = preg_replace('/\d+/ui', ' ', $rs);

	// 4. remove punct
	$rs = preg_replace('/[[:punct:]]/ui', ' ', $rs);

	// 5. remove spaces
	$rs = preg_replace('/(\s+)/ui', ' ', $rs);

	// 6. str to array
	$ars = explode(" ", $rs);
	if (! ($ars)) {
		if (mb_strlen($rs) > 3) {
			return $rs;
		} else {
			return '';
		}
	}

	// 7. remove short words
	$ars_w_short = array_filter($ars, "similarRemoveShorts");

	// 8. remove bad words
	$ars_w_bad = array_filter($ars_w_short, "similarRemoveBad");

	// array to str
	$rs = implode(' ', $ars_w_bad);

	return $rs;
}

function similarRemoveShorts($var) {
    return (mb_strlen($var) > 3);
}

function similarRemoveBad($var) {
	$bad_words = array('серии', 'part', '(tv|dvd)rip', 'novafilm', 'torrent(s*)', 'beta', 'there', 'this', 'setup', 'install', 'nforall', 'info', 'edition', 'lostfilm', 'xvid', 'hdtv', 'live', 'season');
	$is_bad = FALSE;

	foreach ($bad_words as $pattern) {
		$pattern = '/'.$pattern.'/ui';

		if (preg_match($pattern, $var)) {
			$is_bad = TRUE;
			break;
		}
	}

	return !$is_bad;
}



function remove_file_ext($filename) {
	$rs = $filename;
	$extPos = mb_strrpos($filename, '.');
	if ($extPos !== FALSE) {
		$extPos = mb_strlen($filename) - $extPos;
		if ($extPos >= 2 && $extPos <= 6) {
			$rs = strtolower(substr($filename, 0, strrpos($filename, '.')));
		}
	}

	return $rs;
}

// Unset any variables instantiated as a result of register_globals being enabled
function up_unregister_globals()
{
	$register_globals = @ini_get('register_globals');
	if ($register_globals === "" || $register_globals === "0" || strtolower($register_globals) === "off") {
		return;
	}

	// Prevent script.php?GLOBALS[foo]=bar
	if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) {
		exit('I\'ll have a steak sandwich and... a steak sandwich.');
	}

	// Variables that shouldn't be unset
	$no_unset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

	// Remove elements in $GLOBALS that are present in any of the superglobals
	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach ($input as $k => $v) {
		if (!in_array($k, $no_unset) && isset($GLOBALS[$k])) {
			unset($GLOBALS[$k]);
			unset($GLOBALS[$k]);	// Double unset to circumvent the zend_hash_del_key_or_index hole in PHP <4.4.3 and <5.1.4
		}
	}
}

// Generates a valid CSRF token for use when submitting a form to $target_url
// $target_url should be an absolute URL and it should be exactly the URL that the user is going to
// Alternately, if the form token is going to be used in GET (which would mean the token is going to be
// a part of the URL itself), $target_url may be a plain string containing information related to the URL.
function generate_form_token($target_url) {
	return sha1(str_replace('&amp;', '&', $target_url).get_client_ip());
}

function check_form_token($csrf='ss11254BINGO') {
	if (!isset($_REQUEST['csrf_token'])) {
		return FALSE;
	}

	return ($csrf === $_REQUEST['csrf_token']);
}

function getRealFileSize($filename) {
	list($result, ) = explode("\t",exec("/usr/bin/du -b ".escapeshellarg($filename)));
	return $result;
}

function upSetCookie($name, $value, $expire) {
	global $cookie_path, $cookie_domain, $cookie_secure;

	// Enable sending of a P3P header
	header('P3P: CP="CUR ADM"');

	if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
		setcookie($name, $value, $expire, $cookie_path, $cookie_domain, $cookie_secure, TRUE);
	} else {
		setcookie($name, $value, $expire, $cookie_path.'; HttpOnly', $cookie_domain, $cookie_secure);
	}
}


// Display a simple error message
function error() {
	if (!headers_sent()) {
		header('Content-type: text/html; charset=utf-8');
		header('HTTP/1.1 503 Service Temporarily Unavailable');
	}

	$num_args = func_num_args();
	if ($num_args == 3) {
		$message = func_get_arg(0);
		$file = func_get_arg(1);
		$line = func_get_arg(2);
	} else if ($num_args == 2) {
		$file = func_get_arg(0);
		$line = func_get_arg(1);
	} else if ($num_args == 1) {
		$message = func_get_arg(0);
	}

	// Empty all output buffers and stop buffering
	while (@ob_end_clean());

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title>Error</title>
</head>
<body style="width: 35em; margin: 40px; color: #2b2b2b; background: #fff; font:13px/1.331 arial,helvetica,clean,sans-serif; *font-size:small; /* for IE */ 	*font:x-small; /* for IE in quirks mode */">
<h2>Роковая ошибка сервиса</h2>
<hr/>
<?php

	if (isset($message)) {
		echo '<p>'.$message.'</p>'."\n";
	}

	if ($num_args > 1 && DEBUG === TRUE) {
		if (isset($file) && isset($line)) {
			echo '<p><em>Ошибка в строке '.$line.' в '.$file.'</em></p>'."\n";
		}
	}

	echo '<p>Мы уже в&nbsp;курсе и&nbsp;стараемся исправить как можно быстрее.<br/>Возвращайтесь немного позже, всё уже будет работать.</p>';
?>

</body>
</html>
<?php
	exit;
}

//
// Validate an e-mail address
//
function is_valid_email($email) {
	if (mb_strlen($email) > 128) {
		return FALSE;
	}

	return preg_match('/^(([^<>()[\]\\.,;:\s@"\']+(\.[^<>()[\]\\.,;:\s@"\']+)*)|("[^"\']+"))@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\])|(([a-zA-Z\d\-]+\.)+[a-zA-Z]{2,}))$/ui', $email);
}


function printPage($content) {
	global $base_url, $user;

	if (!defined('UP_ROOT')) {
		die('Not defined UP_ROOT');
	}

	if (!defined('UP_HEADER')) {
		require_once UP_ROOT.'header.php';
	}

	echo $content;

	if (!defined('UP_FOOTER')) {
		require_once UP_ROOT.'footer.php';
	}
}

function getServerLoad() {
	$load = sys_getloadavg();
	return $load[0];
}

function httpError404() {
	global $base_url;

	header("Location: {$base_url}404.html");
	exit();
}


?>
