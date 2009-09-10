<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


if (is_admin() != true) {
	show_error_message('Доступ защищён зарослями фиалок и лютиков.');
	exit();
}


try {
	$storage = new Storage;
	$st_list = $storage->get_list();
	$st_stat = $storage->get_stat();

	$storageAll = format_filesize($st_stat['totalSpace']);
	$storageUse = format_filesize($st_stat['totalUseSpace']);
	$storageFree = format_filesize($st_stat['totalFreeSpace']);
	$storagePercentFree = round($fs['totalFreeSpace']/$fs['totalSpace'], 2)*100;
	$storagePercentUse = round($fs['totalUseSpace']/$fs['totalSpace'], 2)*100;
} catch (Exception $e) {
	error($e->getMessage());
}

$out = <<<ZZZ
	<div id="status">&nbsp;</div>
	<h2>Storage</h2>
	<table class="t1" id="search_files_table">
	<tbody>
		<tr><td>Всего storage: </td><td>{$st_stat['countStorage']}</td></tr>
		<tr><td>Объём: </td><td>$storageAll</td></tr>
		<tr><td>Занято: </td><td>$storageUse</td></tr>
		<tr><td>Свободно: </td><td>$storageFree</td></tr>
	</tbody>
	</table>

	<table class="t1" id="search_files_table">
	<thead>
	<tr>
		<th>Name</th>
		<th>Device</th>
		<th>Mount</th>
		<th>Upload</th>
		<th class="center">Size</th>
		<th class="center">Use</th>
		<th class="center">Free</th>
		<th class="center">Use&nbsp;%</th>
	</tr>
	</thead>
	<tbody>
ZZZ;

array_walk($st_list, 'storage_list_callback', &$out);

$out .= <<<ZZZ
	</tbody>
	</table>
ZZZ;


require UP_ROOT.'header.php';
echo($out);
require UP_ROOT.'footer.php';
exit();


function storage_list_callback($storage, $key, $out) {
	try {
		$st = new Storage;
		$st_stat = $st->get_stat_by_name($storage['name']);

		$storageAll = format_filesize($st_stat['totalSpace']);
		$storageUse = format_filesize($st_stat['totalUseSpace']);
		$storageFree = format_filesize($st_stat['totalFreeSpace']);
		$storagePercentFree = round($st_stat['totalFreeSpace']/$st_stat['totalSpace'], 2)*100;
		$storagePercentUse = round($st_stat['totalUseSpace']/$st_stat['totalSpace'], 2)*100;

		$out .= <<<ZZZ
		<tr>
			<td class="left">{$storage['name']}</td>
			<td class="left">{$storage['device']}</td>
			<td class="left">{$storage['mount_point']}</td>
			<td class="left">{$storage['upload_url']}</td>
			<td class="center">$storageAll</td>
			<td class="center">$storageUse</td>
			<td class="center">$storageFree</td>
			<td class="center">$storagePercentUse</td>
		</tr>
ZZZ;
	} catch (Exception $e) {
		error($e->getMessage());
		exit();
	}
}

?>

