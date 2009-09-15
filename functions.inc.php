<?php

if (!defined('UP_ROOT')) {
	exit('The constant UP_ROOT must be defined.');
}


mb_internal_encoding("UTF-8");

// Reverse the effect of register_globals
up_unregister_globals();

// Ignore any user abort requests
ignore_user_abort(true);

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
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');		// For HTTP/1.0 compability

	exit;
}

// only for debug
if (DEBUG === true) {
	error_reporting(8191);
}

// load all libs
require_once UP_ROOT.'include/db.inc.php';
require_once UP_ROOT.'include/logger.inc.php';
require_once UP_ROOT.'include/cache_empty.inc.php';
require_once UP_ROOT.'include/storage.inc.php';
require_once UP_ROOT.'include/user.inc.php';


// get user info
try {
	$user = User::getCurrentUser();
} catch(Exception $e) {
	error($e->getMessage());
}

// is admin rights?
if (defined('ADMIN_PAGE')) {
	if (is_admin() != true) {
		show_error_message('Доступ защищён зарослями фиалок и лютиков.');
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
	// bytes
	if ($bytes < 1024)
		return "${bytes}&nbsp;б";
	// kb
	else if ($bytes < 1048576)
		return round(($bytes/1024), 1).'&nbsp;КБ';
	// megabytes
	else if ($bytes < 1073741824)
		return round(($bytes/1048576), 1).'&nbsp;МБ';
	// gigabytes
	else if ($bytes < 1099511627776)
		return round(($bytes/1073741824), 1).'&nbsp;ГБ';
	// terabytes
	else
		return round(($bytes/1099511627776), 2).'&nbsp;ТБ';
}

function format_filesize($bytes, $quoted=false) {
	if ($quoted === true) {
		$span_start = '<span class=\"filesize\">';
	} else {
		$span_start = '<span class="filesize">';
	}

	// bytes
	if ($bytes < 1024)
		return "${bytes}&nbsp;".$span_start.'б</span>';
	// kb
	else if ($bytes < 1048576)
		return round(($bytes/1024), 1).'&nbsp;'.$span_start.'КБ</span>';
	// megabytes
	else if ($bytes < 1073741824)
		return round(($bytes/1048576), 1).'&nbsp;'.$span_start.'МБ</span>';
	// gigabytes
	else if ($bytes < 1099511627776)
		return round(($bytes/1073741824), 1).'&nbsp;'.$span_start.'ГБ</span>';
	// terabytes
	else
		return round(($bytes/1099511627776), 2).'&nbsp;'.$span_start.'ТБ</span>';
}


function format_speed($bytes, $raw=false) {
	if ($raw) {
		$space = ' ';
	} else {
		$space = '&nbsp;';
	}
	$bytes *= 8;

	// bytes
	if ($bytes < 1000)
		return "${bytes}{$space}б/с";
	// kb
	else if ($bytes < 1000000)
		return round(($bytes/1000), 1)."{$space}кб/с";
	// megabytes
	else if ($bytes < 1000000000)
		return round(($bytes/1000000), 1)."{$space}Мб/с";
	// gigabytes
	else if ($bytes < 1000000000000)
		return round(($bytes/1000000000), 1)."{$space}Гб/с";
	// terabytes
	else
		return round(($bytes/1000000000000), 1)."{$space}Тб/с";
}


function time_to_string($value, $str1, $str2, $str5) {
	if (!$value)
		return 0;

	$mod = $value % 10;

	if (($value % 100) >= 10 && ($value % 100) <= 19)
		return $str5;

	if ($mod == 1)
		return $str1;

	if ($mod >= 2 && $mod <= 4)
		return $str2;

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


function is_admin() {
	return false;
	if (get_client_ip() === '192.168.10.50') {
		return true;
	} else {
		return false;
	}
}


function clear_stat_cache() {
	$cache = new Cache();
	$cache->unlink('up_stats');
	$cache->unlink('rss_lenta');
	$cache->unlink('api_new');
	$cache->unlink('up_storage');
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
		return true;
	} else {
		return false;
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
	$is_image = false;
	$is_valid_type = false;
	$is_valid_mime = false;

	$is_valid_type = is_image_by_ext($file_name);

	if (!$is_valid_type) {
		return false;
	}

	// 3. check mime
	$is_valid_mime = is_image_by_mime($full_filename);

	//
	return ($is_valid_type && $is_valid_mime);
}


function is_flv($file_name, $mime) {
	return (get_file_ext($file_name) == 'flv' || get_file_ext($file_name) == 'mov');
}


function create_thumbs($file, $thumbs_full_filename, $thumbs_preview_full_filename=null) {
	require_once UP_ROOT.'include/phpThumb/phpthumb.class.php';

	$phpThumb = new phpThumb();
	$phpThumb->setSourceFilename($file);
	$phpThumb->w = $GLOBALS['thumbs_w'];
	$phpThumb->h = $GLOBALS['thumbs_h'];
	$phpThumb->config_output_format = 'jpeg';
	$phpThumb->config_error_die_on_error = false;
	$phpThumb->config_allow_src_above_docroot = true;

	if ($phpThumb->GenerateThumbnail()) {
		$phpThumb->RenderToFile($thumbs_full_filename);
		unset ($phpThumb);

		// create large
		if ($thumbs_preview_full_filename) {
			$phpThumb = new phpThumb();
			$phpThumb->setSourceFilename($file);
			$phpThumb->w = $GLOBALS['thumbs_preview_w'];
			$phpThumb->h = $GLOBALS['thumbs_preview_h'];
			$phpThumb->config_output_format = 'jpeg';
			$phpThumb->config_error_die_on_error = false;
			$phpThumb->config_allow_src_above_docroot = true;

			if ($phpThumb->GenerateThumbnail()) {
				$phpThumb->RenderToFile($thumbs_preview_full_filename);
				return true;
			}
		}
	}

	return false;
}


function get_thumbs_filename($location_name) {
	return 'thumbs/'.md5($location_name).'.png';
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


function get_geo($user_ip) {
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
	} else if (($downloads == 0) && !($size <= ($GLOBALS['very_small_file_size']*1048576))) {
		$wakkamakka = $GLOBALS['non_downloaded_interval'] - $ndi;
	} else if ($size <= ($GLOBALS['very_small_file_size']*1048576)) {
		$wakkamakka = ($is_popular ? $GLOBALS['non_downloaded_very_small_files_popular_interval'] : $GLOBALS['non_downloaded_very_small_files_interval']) - $ndi;
	} else if ($size > ($GLOBALS['small_file_size']*1048576)) {
		$wakkamakka = ($is_popular ? $GLOBALS['non_downloaded_big_files_popular_interval'] : $GLOBALS['non_downloaded_big_files_interval']) - $ndi;
	} else {
		$wakkamakka = ($is_popular ? $GLOBALS['non_downloaded_small_files_popular_interval'] : $GLOBALS['non_downloaded_small_files_interval']) - $ndi;
	}

	return $wakkamakka;
}



function is_spam($str) {
	if (!$str) {
		return false;
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

	$is_spam = false;

	foreach ($a_patterns as $pattern) {
		$pattern = '/'.$pattern.'/ui';

		if (preg_match($pattern, $str)) {
			$is_spam = true;
			break;
		}
	}

	return $is_spam;
}


function is_adult($str) {
	if (!$str || !is_can_be_adult($str)) {
		return false;
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

	$is_adult = false;

	foreach ($a_patterns as $pattern) {
		$pattern = '/'.$pattern.'/ui';

		if (preg_match($pattern, $str)) {
			$is_adult = true;
			break;
		}
	}

	return $is_adult;
}


function prettyDate($mysqlDate) {
	date_default_timezone_set ("Europe/Zaporozhye");
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
    }
	elseif ($dayDiff == 1) {
		return 'вчера';
	} else {
		return $defaultDateFormat;
	}
}



function makeSearch($req, $fooltext=false) {
	$out = '';
	$regexp = (bool) preg_match('/\*|\?/u', $req);

	try {
		$db = new DB;

		if ($fooltext === true) {
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
	} catch (Exception $e) {
		throw new Exception($e->getMessage());
	}

	if ($datas) {
		$r = '';

		foreach ($datas as $rec) {
			$item_id = intval ($rec['id']);
			$filename = get_cool_and_short_filename ($rec['filename'], 40);
			$filesize_text = format_filesize ($rec['size']);
			$downloaded = $rec['downloads'];
			$file_date = $rec['uploaded_date'];

			$r .= <<<ZZZ
				<tr>
					<td class="right">$filesize_text</td>
					<td class="left"><a rel="nofollow" href="/$item_id/">$filename</a></td>
					<td class="center">$downloaded</td>
			</tr>
ZZZ;
			}

			// make all answer with header
			$out = <<<ZZZ
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
ZZZ;
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
		$out = <<<ZZZ
		<table class="t1" id="pe_files_table">
		<tbody>
ZZZ;
		foreach ($datas as $item) {
			$item_id = intval ($item['id']);
			$n = intval ($item['n']);
			$filename = get_cool_and_short_filename ($item['filename'], 40);
			$type = $item['type'];

			$out .= <<<ZZZ
			<tr><td class="first left $type"><a rel="nofollow" href="/$item_id/">$filename</a></td></tr>
ZZZ;
		}

		$out .= <<<ZZZ
		</tbody>
		</table>
ZZZ;
	}

	return $out;
}


function top_get($type, $page, $link_base) {
	if ($page > 15 && !is_admin()) {
		return '<div id="status">&nbsp;</div><h2>Внимание</h2><p>Для просмотра более старых файлов воспользуйтесь <a href="/search/">поиском</a>.</p>';
	}

	$admin = is_admin();
	$items_per_page = 50;

	try {
		$db = new DB;
		$res = $db->getRow("SELECT COUNT(*) as num FROM up WHERE deleted='0' AND hidden='0' AND spam='0' AND adult='0' AND size>1048576");
	} catch (Exception $e) {
		throw new Exception($e->getMessage());
	}

	$num_items = $res['num'];
	$num_pages = ceil ($num_items / $items_per_page);
	$start_from = $items_per_page * ($page - 1);

	if ($page > 1) {
		$back_page_number = $page - 1;
		$back_page = "<a class=\"page_links\" rev='$back_page_number' href=\"$link_base/$back_page_number/\" title=\"Предыдущая страница\">&larr;</a>";
	} else {
		$back_page = '&larr;';
		$back_page_number = -1;
	}

	if ($page < $num_pages) {
		$next_page_number =  $page + 1;
		$next_page = "<a class=\"page_links\" rel='$next_page_number' href=\"$link_base/$next_page_number/\" title=\"Следующая страница\">&rarr;</a>";
	} else {
		$next_page = "&rarr;";
		$next_page_number = -1;
	}

	$page_links = '<ul class="page_links" id="page_links">'.$back_page.'<span class="ctrl_links">&nbsp;'.$page.'/'.$num_pages.'</span>'.$next_page.'</ul>';

	$th_size = '<th class="right"><a href="/top/size/">Размер</a></th>';
	$th_name = '<th class="left"><a href="/top/name/">Имя файла</a></th>';
	$th_downloads = '<th class="center"><a href="/top/popular/">Скачан</a></th>';
	$th_date = '<th class="right"><a href="/top/new/">Время</a></th>';

	$td_date_class = $td_name_class = $td_size_class = $td_downloads_class = '';
	$admin_th_row = $admin_td_row = $admin_actions_block = '';
	$colspan = 4;

	switch ($type) {
		case 'new':
			$cache_id = 'top_new';
			$header = '<em>Top</em>&nbsp;свежих файлов';
			$th_date = '<th class="right current">Время</th>';
			$td_date_class = "current";
			$order_by = 'uploaded_date';
			break;

		case 'size':
			$cache_id = 'top_size';
			$header = '<em>Top</em>&nbsp;больших файлов';
			$th_size = '<th class="right current">Размер</th>';
			$td_size_class = "current";
			$order_by = 'size';
			break;

		case 'name':
			$cache_id = 'top_name';
			$header = '<em>Сортировка:</em>&nbsp;имя';
			$th_name = '<th class="left current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'id';
			break;

		case 'mp3':
			$cache_id = 'top_mp3';
			$header = 'MP3';
			$th_name = '<th class="left current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'uploaded_date';
			$query = "SELECT * FROM up
					WHERE spam='0'
					AND deleted='0'
					AND hidden='0'
					AND adult='0'
					AND filename REGEXP BINARY '.mp3$'
					ORDER BY $order_by DESC
					LIMIT $start_from,$items_per_page";
			break;

		case 'video':
			$cache_id = 'top_video';
			$header = 'Видео';
			$th_name = '<th class="left current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'uploaded_date';
			$query = "SELECT * FROM up
					WHERE spam='0'
					AND deleted='0'
					AND hidden='0'
					AND adult='0'
					AND filename REGEXP BINARY '.avi$|.mpg$|.mp4$|.mpeg$'
					ORDER BY $order_by DESC
					LIMIT $start_from,$items_per_page";
			break;

		case 'archive':
			$cache_id = 'top_archive';
			$header = 'Архивы';
			$th_name = '<th class="left current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'uploaded_date';
			$query = "SELECT * FROM up
					WHERE spam='0'
					AND deleted='0'
					AND hidden='0'
					AND adult='0'
					AND filename REGEXP BINARY '.rar$|.zip$|.gz$|.bz2$|.7z$|.arj$|.ace$'
					ORDER BY $order_by DESC
					LIMIT $start_from,$items_per_page";
			break;

		case 'image':
			$cache_id = 'top_image';
			$header = 'Образы';
			$th_name = '<th class="left current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'uploaded_date';
			$query = "SELECT * FROM up
					WHERE spam='0'
					AND deleted='0'
					AND hidden='0'
					AND adult='0'
					AND filename REGEXP BINARY '.iso$|.nrg$|.mdf$|.mds$'
					ORDER BY $order_by DESC
					LIMIT $start_from,$items_per_page";
			break;

		case 'pic':
			$cache_id = 'top_pic';
			$header = 'Картинки';
			$th_name = '<th class="left current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'uploaded_date';
			$query = "SELECT * FROM up
					WHERE spam='0'
					AND deleted='0'
					AND hidden='0'
					AND adult='0'
					AND filename REGEXP BINARY '.jpeg$|.jpg$|.png$|.gif$|.tiff$|.psd$|.bmp$'
					ORDER BY $order_by DESC
					LIMIT $start_from,$items_per_page";
			break;


		case 'popular':
		default:
			$cache_id = 'top_top';
			$header = '<em>Top</em>&nbsp;популярных файлов';
			$th_downloads = '<th class="center current">Скачан</th>';
			$td_downloads_class = "current";
			$order_by = 'downloads';
			break;
	}



	if ($admin) {
		$addScript[] = 'up.admin.js';
		$colspan = 5;
		$admin_th_row = '<th class="center"><input type="checkbox" id="allCB"/></th>';
		$admin_actions_block = '
			<div>
			Выбранные файлы:
				<span class="as_js_link" onlick="UP.admin.markItemSpam();">спам</span>
				<span class="as_js_link" onlick="UP.admin.markItemSpam();">adult</span>
				<span class="as_js_link" onlick="UP.admin.markItemSpam();">удалить</span>
				<span class="as_js_link" onlick="UP.admin.markItemSpam();">скрыть</span>
				<!--<input type="button" value="spam" onmousedown="UP.admin.markItemSpam();" disabled="disabled"/>
				<input type="button" value="delete" onmousedown="UP.admin.deleteItem();" disabled="disabled"/>
				<input type="button" value="adult" onmousedown="UP.admin.markItemAdult();" disabled="disabled"/>-->
			</div>';
	}


	try {
		if (isset($query)) {
			$datas = $db->getData($query);
		} else {
			$datas = $db->getData("SELECT * FROM up
					WHERE spam='0'
					AND deleted='0'
					AND hidden='0'
					AND adult='0'
					AND size>1048576
					ORDER BY $order_by DESC
					LIMIT $start_from,$items_per_page");
		}
	} catch (Exception $e) {
		throw new Exception($e->getMessage());
	}


	if ($datas) {
		$blocks = <<<ZZZ
		<div id="status">&nbsp;</div>
		<h2>$header</h2>
		$admin_actions_block
		<table class="t1" id="top_files_table">
		<thead>
		<tr>
			<th class="right noborder" id="pageLinks" colspan="$colspan">$back_page $page $next_page</th>
		</tr>
		<tr>
			$admin_th_row
			$th_size
			$th_name
			$th_downloads
			$th_date
		</tr>
		</thead>
		<tbody>
ZZZ;
		foreach ($datas as $rec) {
			$item_id = (int)$rec['id'];
			$filename = get_cool_and_short_filename($rec['filename'], 45);
			$filesize = format_filesize($rec['size']);
			$downloaded = $rec['downloads'];
			$file_date = prettyDate($rec['uploaded_date']);
			$file_last_downloaded_date = $rec['last_downloaded_date'];
			$desc = $rec['description'];
			$spam = $rec['spam'];

			if ($spam == 1) {
				$spam_class="spam";
			} else {
				$spam_class="zz";
			}

			if ($admin) {
				$admin_td_row = '<td class="center"><input type="checkbox" value="1" id="item_cb_'.$item_id.'"/></td>';
			} else {
				$admin_td_row = '';
			}


			if ($downloaded < 1) {
				$file_last_downloaded_date = 'неизвестно';
			}

			$blocks .= <<<ZZZ
		<tr id="row_item_{$rec['id']}">
			$admin_td_row
			<td class="right $td_size_class">$filesize</td>
			<td id="cell_item_{$rec['id']}" class="left $td_name_class"><a href="/{$rec['id']}/" title="{$rec['filename']}">${filename}&nbsp;</a></td>
			<td class="center $td_downloads_class">$downloaded</td>
			<td class="right $td_date_class">$file_date</td>
		</tr>
ZZZ;
		}

		$blocks .= <<<ZZZ
		</tbody>
		<tfoot>
		<tr>
			<td class="right noborder" style="" id="pageLinks" colspan="$colspan">$back_page $page $next_page</td>
		</tr>
		</tfoot>
		</table>
ZZZ;
	} else {
 		$blocks = '<div id="status">&nbsp;</div><h2>Уппс!</h2><p>Я пустая страница.</p>';
	}

	return $blocks;
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
	$is_bad = false;

	foreach ($bad_words as $pattern) {
		$pattern = '/'.$pattern.'/ui';

		if (preg_match($pattern, $var)) {
			$is_bad = true;
			break;
		}
	}

	return !$is_bad;
}



function remove_file_ext($filename) {
	$rs = $filename;
	$extPos = mb_strrpos($filename, '.');
	if ($extPos !== false) {
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
		return false;
	}

	return ($csrf === $_REQUEST['csrf_token']);
}

function getRealFileSize($filename) {
	list($result, ) = explode("\t",exec("/usr/bin/du -b ".escapeshellarg($filename)));
	return $result;
}

function upSetCookie($name, $value, $expire)
{
	global $cookie_path, $cookie_domain, $cookie_secure;

	// Enable sending of a P3P header
	header('P3P: CP="CUR ADM"');

	if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
		setcookie($name, $value, $expire, $cookie_path, $cookie_domain, $cookie_secure, true);
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

	if (isset($message))
		echo '<p>'.$message.'</p>'."\n";

	if ($num_args > 1) {
		if (defined('DEBUG')) {
			if (isset($file) && isset($line)) {
				echo '<p><em>Ошибка в строке '.$line.' в '.$file.'</em></p>'."\n";
			}
		}
	}

	echo 'Мы уже в&nbsp;курсе и&nbsp;стараемся исправить как можно быстрее. Возвращайтесь немного позже, всё уже будет работать.';
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
		return false;
	}

	return preg_match('/^(([^<>()[\]\\.,;:\s@"\']+(\.[^<>()[\]\\.,;:\s@"\']+)*)|("[^"\']+"))@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\])|(([a-zA-Z\d\-]+\.)+[a-zA-Z]{2,}))$/ui', $email);
}

function getWelcomeMessage() {
	$messages = array('Привет, Гость!');
	return $messages[array_rand($messages)];
}

function printPage($content) {
	global $base_url, $user;

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



?>
