<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';


try {
	if (!isset($_GET['sug']) || (mb_strlen($_GET['sug']) <= 2)) {
		throw new Exception('No query');
	}

	$cache = new Cache;
	$cacheKey = sha1('sc_'.mb_substr($_GET['sug'], 0, 90));

	if (!$out = $cache->get($cacheKey)) {
		$sug =  $_GET['sug'];
		$regexp = mb_strpos($sug, '*');
		if ($regexp === FALSE) {
			$regexp = mb_strpos($sug, '?');
		}

		if ($regexp === FALSE) {
			$sug = '%'.$sug.'%';
		} else {
			$trans = array ('*' => '%', '?' => '_');
			$sug = strtr($sug, $trans);
		}

		$db = DB::singleton();
		$datas = $db->getData("SELECT DISTINCT filename FROM up WHERE deleted='0' AND hidden='0' AND spam='0' AND adult='0' AND filename LIKE ? ORDER BY filename LIMIT $searchCompleteMaxResults", "%{$sug}%");

		if (!$datas) {
			throw new Exception('No matches');
		}

		$out = $result = array();
		array_push($out, $_GET['sug']);

		foreach ($datas as $rec) {
	      	array_push($result, $rec['filename']);
		}

		array_push($out, $result);
		$cache->set($out, $cacheKey, $cache_timeout_search_complete);
	}

	exit(json_encode($out));
} catch (Exception $e) {
	exit('["", []]');
}

?>
