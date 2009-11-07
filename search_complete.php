<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


$out = ('["", []]');
$max_results = 12;



if (isset($_GET['sug'])) {
    $sug =  $_GET['sug'];
	if (!$sug || mb_strlen($sug) <= 2) {
		exit ($out);
	}

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

	try {
		$db = new DB;
		$datas = $db->getData("SELECT DISTINCT filename FROM up WHERE deleted='0' AND hidden='0' AND spam='0' AND adult='0' AND filename LIKE ? ORDER BY filename LIMIT $max_results", "%{$sug}%");
	} catch (Exception $e) {
		exit ($out);
	}

	if ($datas) {
	  	$out = "[\"$sug\", [";
	    $first = TRUE;

		foreach ($datas as $rec) {
	      	$first == TRUE ? $out .= "\"{$rec['filename']}\"" : $out .= ", \"{$rec['filename']}\"";
			$first = FALSE;
		}
		$out .= ']]';
	}
}

exit ($out);

?>
