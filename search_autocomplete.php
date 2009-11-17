<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


try {
	if (!isset($_GET['q']) || (mb_strlen($_GET['q']) <= 2)) {
		throw new Exception('No query');
	}

	$cache = new Cache;
	$cacheKey = sha1('sa_'.mb_substr($_GET['q'], 0, 90));

	if (!$out = $cache->get($cacheKey)) {
		$sug =  urldecode($_GET['q']);

		$regexp = (bool) preg_match('/\*|\?/u', $sug);
		if (!$regexp) {
			$sug = '%'.$sug.'%';
		} else {
			$trans = array('*' => '%', '?' => '_');
			$sug = strtr($sug, $trans);
		}

		$db = new DB;
		$datas = $db->getData("SELECT DISTINCT filename FROM up WHERE deleted='0' AND hidden='0' AND spam='0' AND adult='0' AND filename LIKE ? ORDER BY filename LIMIT $searchCompleteMaxResults", "%{$sug}%");

		$out = '';
		if ($datas) {
			foreach ($datas as $rec) {
	        	$out .= $rec['filename']."\n";
			}

			$cache->set($out, $cacheKey, $cache_timeout_search_complete);
		}
	}

	exit($out);
} catch (Exception $e) {
	exit('');
}

?>
