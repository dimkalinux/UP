<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';
require UP_ROOT.'header.php';

$out = null;
$cache = new Cache;
if (!$out = $cache->get('up_stats')) {
	$db = new DB;
	$row = $db->getRow("SELECT COUNT(*) AS num_files, SUM(size) AS sum_sizes FROM up");
	$num_files = $row['num_files'];
	$sum_file_size = format_filesize($row['sum_sizes']);

	// Средние значения
	$row = $db->getRow("SELECT COUNT(id)/DATEDIFF(MAX(uploaded_date),MIN(uploaded_date)) AS m_upload FROM up");
	$m_upload_per_day = (int) $row['m_upload'];

	$row = $db->getRow("SELECT COUNT(*) AS n FROM downloads WHERE date >= DATE_SUB(CURDATE(),INTERVAL 1 DAY)");
	$m_download_per_day = (int) $row['n'];

	$storage = new Storage;
	$fs = $storage->get_stat();

	$storageAll = format_filesize($fs['totalSpace']);
	$storageUse = format_filesize($fs['totalUseSpace']);
	$storageFree = format_filesize($fs['totalFreeSpace']);

	$storagePercentFree = 0;
	$storagePercentUse = 0;
	if ($fs['totalSpace'] > 0) {
		$storagePercentFree = round($fs['totalFreeSpace']/$fs['totalSpace'], 2)*100;
		$storagePercentUse = round($fs['totalUseSpace']/$fs['totalSpace'], 2)*100;
	}


	$a = get_download_logs();

	$out = <<<ZZZ
	<div id="status">&nbsp;</div>
	<h2>Статистика</h2>
	<div class="asTable">
		<div class="asRow">
		<div class="asCell">
			<table class="t1">
				<tr><td class="ab">Всего загруженных файлов:</td><td>$num_files</td></tr>
				<tr><td class="ab">Загрузок на сервер в день, файлов:</td><td class="last">$m_upload_per_day</td></tr>
				<tr><td class="ab">Скачиваний в день, файлов:</td><td class="last">$m_download_per_day</td></tr>
				<tr><td class="ab">Общий объем загруженных файлов:</td><td class="last">$sum_file_size</td></tr>

				<tr><td class="ab">Объём хранилища:</td><td class="last">$storageAll</td></tr>
				<tr><td class="ab">Используется:</td><td class="last">$storageUse</td></tr>
				<tr><td class="ab">Свободно для хранения:</td><td class="last">$storageFree</td></tr>
				<tr><td colspan="2">&nbsp;</td></tr>

				<tr><td class="ab">LDS:</td><td class="last">{$a['lds']}</td></tr>
				<tr><td class="ab">iTeam:</td><td class="last">{$a['iteam']}</td></tr>
				<tr><td class="ab">Мир:</td><td class="last">{$a['world']}</td></tr>

			</table>
		</div>
		<div class="asCell">
			<img src="http://chart.apis.google.com/chart?cht=p&chd=t:$storagePercentUse,$storagePercentFree&chs=320x160&chl=Занято|Свободно&chtt=Дисковое+пространство&chco=058DC7,50B432"/>
		</div>
		</div>
		<div class="asRow">
			<div class="asCell">
				<table class="t1">
				</table>
			</div>
			<div class="asCell">
				<img src="http://chart.apis.google.com/chart?cht=p&chd=t:{$a['lds_p']},{$a['iteam_p']},{$a['world_p']}&chs=320x160&chl=LDS|iTeam|World&chtt=Geo+targeting&chco=058DC7,ED561B,50B432"/>
			</div>
		</div>
	</div>
ZZZ;

	$cache->set($out, 'up_stats', 300);
}

echo $out;

require UP_ROOT.'footer.php';
?>
