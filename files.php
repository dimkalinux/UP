<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


$blocks = null;

$type = get_get('type');
$page = get_get('page');

if (!$page || $page < 1) {
	$page = 1;
}

switch ($type) {
	case 'new':
		$blocks = files_get($type, $page, "/files/new");
		break;

	case 'size':
		$blocks = files_get($type, $page, "/files/size");
		break;

	case 'name':
		$blocks = files_get($type, $page, "/files/name");
		break;

	case 'mp3':
		$blocks = files_get($type, $page, "/files/mp3");
		break;

	case 'video':
		$blocks = files_get($type, $page, "/files/video");
		break;

	case 'archive':
		$blocks = files_get($type, $page, "/files/archive");
		break;

	case 'image':
		$blocks = files_get($type, $page, "/files/image");
		break;

	case 'photo':
		$blocks = files_get($type, $page, "/files/photo");
		break;

	case 'popular':
	default:
		$blocks = files_get($type, $page, "/files/popular");
		break;
}

require UP_ROOT.'header.php';
echo $blocks;
$addScript = $onDOMReady = '';
if ($user['is_admin']) {
		$addScript[] = 'up.admin.js';
		$onDOMReady = 'UP.admin.cbStuffStart();';
}
require UP_ROOT.'footer.php';
exit();


function files_get($type, $page, $link_base) {
	global $user, $minFileSizeForTOP;

	if ($page > 15 && !$user['is_admin']) {
		return '<div id="status">&nbsp;</div><h2>Внимание</h2><p>Для просмотра более старых файлов воспользуйтесь <a href="/search/">поиском</a>.</p>';
	}

	$admin = $user['is_admin'];
	$items_per_page = 100;

	try {
		$db = new DB;
		$res = $db->getRow("SELECT COUNT(*) as num FROM up WHERE deleted='0' AND hidden='0' AND spam='0' AND adult='0'");
	} catch (Exception $e) {
		error($e->getMessage());
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

	$th_size = '<th class="size"><a href="/files/size/">Размер</a></th>';
	$th_name = '<th class="name"><a href="/files/name/">Имя файла</a></th>';
	$th_downloads = '<th class="download"><a href="/files/popular/">Скачан</a></th>';
	$th_date = '<th class="time"><a href="/files/new/">Время</a></th>';

	$td_date_class = $td_name_class = $td_size_class = $td_downloads_class = '';
	$admin_th_row = $admin_td_row = $admin_actions_block = '';
	$colspanPreAdmin = 1;
	$colspan = 3;

	switch ($type) {
		case 'new':
			$header = 'Список&nbsp;свежих файлов';
			$th_date = '<th class="time current">Время</th>';
			$td_date_class = "current";
			$order_by = 'uploaded_date';
			break;

		case 'size':
			$header = 'Список&nbsp;больших файлов';
			$th_size = '<th class="size current">Размер</th>';
			$td_size_class = "current";
			$order_by = 'size';
			break;

		case 'name':
			$header = 'Сортировка:&nbsp;имя';
			$th_name = '<th class="name current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'id';
			break;

		case 'mp3':
			$header = 'MP3';
			$th_name = '<th class="name current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'uploaded_date';
			$query = "SELECT * FROM up WHERE spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						AND filename REGEXP BINARY '.mp3$'
						ORDER BY $order_by DESC LIMIT $start_from,$items_per_page";
			break;

		case 'video':
			$header = 'Видео';
			$th_name = '<th class="name current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'uploaded_date';
			$query = "SELECT * FROM up WHERE spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						AND filename REGEXP BINARY '.avi$|.mpg$|.mp4$|.mpeg$'
						ORDER BY $order_by DESC LIMIT $start_from,$items_per_page";
			break;

		case 'archive':
			$header = 'Архивы';
			$th_name = '<th class="name current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'uploaded_date';
			$query = "SELECT * FROM up WHERE spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						AND filename REGEXP BINARY '.rar$|.zip$|.gz$|.bz2$|.7z$|.arj$|.ace$'
						ORDER BY $order_by DESC LIMIT $start_from,$items_per_page";
			break;

		case 'image':
			$header = 'Образы';
			$th_name = '<th class="name current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'uploaded_date';
			$query = "SELECT * FROM up WHERE spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						AND filename REGEXP BINARY '.iso$|.nrg$|.mdf$|.mds$'
						ORDER BY $order_by DESC LIMIT $start_from,$items_per_page";
			break;

		case 'photo':
			$header = 'Картинки';
			$th_name = '<th class="name current">Имя файла</th>';
			$td_name_class = "current";
			$order_by = 'uploaded_date';
			$query = "SELECT * FROM up WHERE spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						AND filename REGEXP BINARY '.jpeg$|.jpg$|.png$|.gif$|.tiff$|.psd$|.bmp$'
						ORDER BY $order_by DESC LIMIT $start_from,$items_per_page";
			break;


		case 'popular':
		default:
			$header = '<em>Список</em>&nbsp;популярных файлов';
			$th_downloads = '<th class="download current">Скачан</th>';
			$td_downloads_class = "current";
			$order_by = 'downloads';
			break;
	}



	if ($admin) {
		$colspanPreAdmin = 2;
		$colspan = 2;
		$admin_th_row = '<th class="center"><input type="checkbox" id="allCB"/></th>';
		$admin_actions_block = '
			<div class="controlButtonsBlock">
				<button type="button" class="btn pill-l" disabled="disabled" onmousedown="UP.admin.markItemSpam();"><span><span>спам</span></span></button>
				<button type="button" class="btn pill-c" disabled="disabled" onmousedown="UP.admin.markItemAdult();"><span><span>+16</span></span></button>
				<button type="button" class="btn pill-r" disabled="disabled" onmousedown="UP.admin.hideItem();"><span><span>cкрыть</span></span></button>
				&nbsp;
				<button type="button" class="btn" disabled="disabled" onmousedown="UP.admin.deleteItem();"><span><span>удалить</span></span></button>
			</div>';
	}


	try {
		if (!isset($query)) {
			$query = "SELECT * FROM up WHERE spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						ORDER BY $order_by DESC LIMIT $start_from,$items_per_page";
		}

		$datas = $db->getData($query);
	} catch (Exception $e) {
		error($e->getMessage());
	}


	if ($datas) {
		$blocks = <<<ZZZ
		<div id="status">&nbsp;</div>
		<h2>$header</h2>
		<table class="t1" id="top_files_table">
		<thead>
		<tr>
			<th class="noborder" colspan="$colspanPreAdmin"></th>
			<th class="left noborder">$admin_actions_block</th>
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
			$fullFilename = htmlspecialchars_decode(stripslashes($rec['filename']));
			$filename = get_cool_and_short_filename($fullFilename, 55);
			$filenameTitle = '';
			if (5 < (mb_strlen($fullFilename) - mb_strlen($filename))) {
				$filenameTitle = 'title="Полное имя: '.$fullFilename.'"';
			}
			$filesize = format_filesize($rec['size']);
			$downloaded = $rec['downloads'];
			$hotDownloads = intval($rec['hot_downloads'], 10);
			$file_date = prettyDate($rec['uploaded_date']);
			$file_last_downloaded_date = $rec['last_downloaded_date'];
			$spam = $rec['spam'];

			$spam_class = "";
			if ($spam == 1) {
				$spam_class = "spam";
			}

			$admin_td_row = '';
			if ($admin) {
				$admin_td_row = '<td class="center"><input type="checkbox" value="1" id="item_cb_'.$item_id.'"/></td>';
			}


			if ($downloaded < 1) {
				$file_last_downloaded_date = 'неизвестно';
			}

			$popularLabel = '';
			if ($hotDownloads > 1000) {
				$popularLabel = '<span class="popularLabel" title="Более 1000 скачиваний за неделю">+1k</span>';
			} else if ($hotDownloads > 100) {
				$popularLabel = '<span class="popularLabel" title="Более 100 скачиваний за неделю">+100</span>';
			} else if ($hotDownloads > 20) {
				$popularLabel = '<span class="popularLabel" title="Более 20 скачиваний за неделю">+20</span>';
			}

			$blocks .= <<<ZZZ
		<tr id="row_item_{$item_id}" class="row_item">
			$admin_td_row
			<td class="size $td_size_class">$filesize</td>
			<td id="cell_item_{$rec['id']}" class="name $td_name_class" $filenameTitle>$popularLabel <a href="/{$rec['id']}/">${filename}</a></td>
			<td class="download $td_downloads_class">$downloaded</td>
			<td class="time $td_date_class">$file_date</td>
		</tr>
ZZZ;
		}

		$blocks .= <<<ZZZ
		</tbody>
		<tfoot>
		<tr>
			<td class="noborder" colspan="$colspanPreAdmin"></td>
			<td class="left noborder"></td>
			<td class="right noborder" id="pageLinks" colspan="$colspan">$back_page $page $next_page</td>
		</tr>
		</tfoot>
		</table>
ZZZ;
	} else {
 		$blocks = '<div id="status">&nbsp;</div><h2>Список файлов</h2><p>Файлы отсутствуют.</p>';
	}

	return $blocks;
}

?>
