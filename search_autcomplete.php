<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


$out = '';
$max_results=15;


if (isset($_GET['q'])) {
	$sug =  urldecode($_GET['q']);

	$cache = new Cache;
	$cache_key = md5(mb_substr($sug, 0, 10, 'UTF-8'));

	if (!$out = $cache->get($cache_key)) {
		if (!$sug || mb_strlen($sug) <= 2) {
	        return;
		}

		$regexp = (bool) preg_match('/\*|\?/u', $sug);
		if (!$regexp) {
			$sug = '%'.$sug.'%';
		} else {
			$trans = array('*' => '%', '?' => '_');
			$sug = strtr($sug, $trans);
		}

		try {
			$db = new DB;
			$datas = $db->getData("SELECT DISTINCT filename FROM up WHERE deleted='0' AND hidden='0' AND spam='0' AND adult='0' AND filename LIKE ? ORDER BY filename LIMIT $max_results", $sug);
		} catch (Exception $e) {
			echo '';
		}

		if ($datas) {
			foreach($datas as $rec) {
	        	$out .= $rec['filename']."\n";
			}
		}

		// set cache
		$cache->set($out, $cache_key, 60);
	}
}

echo $out;
?>
