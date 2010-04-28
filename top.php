<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


$blocks = null;

$type = get_get('type');

switch ($type) {
	case 'mp3':
		$blocks = top_get($type, "/top/mp3");
		break;

	case 'video':
		$blocks = top_get($type, "/top/video");
		break;

	case 'archive':
		$blocks = top_get($type, "/top/archive");
		break;

	case 'image':
		$blocks = top_get($type, "/top/image");
		break;

	case 'photo':
		$blocks = top_get($type, "/top/photo");
		break;

	default:
		$blocks = top_get($type, "/top/");
		break;
}

$addScript = $onDOMReady = '';
if ($user['is_admin']) {
	$addScript[] = 'up.admin.js';
	$onDOMReady = 'UP.admin.cbStuffStart();';
}
printPage($blocks);


function top_get($type, $link_base) {
	global $user;

	$admin = $user['is_admin'];
	$admin_th_row = $admin_td_row = $admin_actions_block = '';
	$colspanPreAdmin = 1;
	$colspan = 3;

	switch ($type) {
		case 'mp3':
			$header = 'ТОП MP3';
			$query = "SELECT item_id AS id,COUNT(*) AS dc,filename,size,downloads,hot_downloads,UNIX_TIMESTAMP(uploaded_date) AS UT,last_downloaded_date FROM downloads LEFT JOIN up on up.id=downloads.item_id
						WHERE (date > NOW() - INTERVAL 1 WEEK)
						AND spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						AND filename REGEXP BINARY '.mp3$'
						GROUP by item_id ORDER BY dc DESC LIMIT 100";
			break;

		case 'video':
			$header = 'ТОП Видео';
			$query = "SELECT item_id AS id,COUNT(*) AS dc,filename,size,downloads,hot_downloads,UNIX_TIMESTAMP(uploaded_date) AS UT,last_downloaded_date FROM downloads LEFT JOIN up on up.id=downloads.item_id
						WHERE (date > NOW() - INTERVAL 1 WEEK)
						AND spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						AND filename REGEXP BINARY '.avi$|.mpg$|.mp4$|.mpeg$'
						GROUP by item_id ORDER BY dc DESC LIMIT 100";
			break;

		case 'archive':
			$header = 'ТОП Архивы';
			$query = "SELECT item_id AS id,COUNT(*) AS dc,filename,size,downloads,hot_downloads,UNIX_TIMESTAMP(uploaded_date) AS UT,last_downloaded_date FROM downloads LEFT JOIN up on up.id=downloads.item_id
						WHERE (date > NOW() - INTERVAL 1 WEEK)
						AND spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						AND filename REGEXP BINARY '.rar$|.zip$|.gz$|.bz2$|.7z$|.arj$|.ace$'
						GROUP by item_id ORDER BY dc DESC LIMIT 100";
			break;

		case 'image':
			$header = 'ТОП Образы';
			$query = "SELECT item_id AS id,COUNT(*) AS dc,filename,size,downloads,hot_downloads,UNIX_TIMESTAMP(uploaded_date) AS UT,last_downloaded_date FROM downloads LEFT JOIN up on up.id=downloads.item_id
						WHERE (date > NOW() - INTERVAL 1 WEEK)
						AND spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						AND filename REGEXP BINARY '.iso$|.nrg$|.mdf$|.mds$'
						GROUP by item_id ORDER BY dc DESC LIMIT 100";
			break;

		case 'photo':
			$header = 'ТОП Картинки';
			$query = "SELECT item_id AS id,COUNT(*) AS dc,filename,size,downloads,hot_downloads,UNIX_TIMESTAMP(uploaded_date) AS UT,last_downloaded_date FROM downloads LEFT JOIN up on up.id=downloads.item_id
						WHERE (date > NOW() - INTERVAL 1 WEEK)
						AND spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						AND filename REGEXP BINARY '.jpeg$|.jpg$|.png$|.gif$|.tiff$|.psd$|.bmp$'
						GROUP by item_id ORDER BY dc DESC LIMIT 100";
			break;

		default:
			$header = 'ТОП 100 за неделю';
			$query = "SELECT item_id AS id,COUNT(*) AS dc,filename,size,downloads,hot_downloads,UNIX_TIMESTAMP(uploaded_date) AS UT,last_downloaded_date FROM downloads LEFT JOIN up on up.id=downloads.item_id
						WHERE (date > NOW() - INTERVAL 1 WEEK)
						AND spam='0' AND deleted='0' AND hidden='0' AND adult='0'
						GROUP by item_id ORDER BY dc DESC LIMIT 100";
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
		$db = DB::singleton();
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
			<th class="right noborder" id="pageLinks" colspan="$colspan"></th>
		</tr>
		<tr>
			$admin_th_row
			<th class="size">Размер</th>
			<th class="name">Имя файла</th>
			<th class="download">Скачан</th>
			<th class="time">Время</th>
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
			$file_date = prettyDate($rec['UT']);
			$file_last_downloaded_date = $rec['last_downloaded_date'];


			$admin_td_row = '';
			if ($admin) {
				$admin_td_row = '<td class="center"><input type="checkbox" value="1" id="item_cb_'.$item_id.'"/></td>';
			}


			if ($downloaded < 1) {
				$file_last_downloaded_date = 'неизвестно';
			}

			$popularLabel = '';

			$blocks .= <<<ZZZ
		<tr id="row_item_{$item_id}" class="row_item">
			$admin_td_row
			<td class="size">$filesize</td>
			<td id="cell_item_{$rec['id']}" class="name" $filenameTitle><a href="/{$rec['id']}/">${filename}</a></td>
			<td class="download">$downloaded</td>
			<td class="time">$file_date</td>
		</tr>
ZZZ;
		}

		$blocks .= <<<ZZZ
		</tbody>
		<tfoot>
		<tr>
			<td class="noborder" colspan="$colspanPreAdmin"></td>
			<td class="left noborder"></td>
			<td class="right noborder" id="pageLinks" colspan="$colspan"></td>
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
