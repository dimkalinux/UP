<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


$out = ('["", []]');
$max_results=12;



if (isset ($_GET['sug']))
{
    $sug =  $_GET['sug'];
	if (!$sug || mb_strlen($sug, 'UTF-8') <= 2) {
	        echo $out;
	        return;
	}

	$regexp = mb_strpos($sug, '*');
	if ($regexp === false) {
		$regexp = mb_strpos($sug, '?');
	}


	if ($regexp === false) {
		$sug = '%'.$sug.'%';
	} else {
		$trans = array ('*' => '%', '?' => '_');
		$sug = strtr($sug, $trans);
	}

	$db = new DB;
	$datas = $db->getData("SELECT DISTINCT filename FROM up WHERE deleted='0' AND hidden='0' AND spam='0' AND adult='0' AND filename LIKE ? ORDER BY filename LIMIT $max_results", "%{$queryE%}");

	if ($datas) {
	  	$out = "[\"$sug\", [";
	    $first = true;

		foreach($datas as $rec) {
	      	$first == true ? $out .= "\"{$rec['filename']}\"" : $out .= ", \"{$rec['filename']}\"";
			$first = false;
		}
		$out .= ']]';
	}
}

echo $out;

?>
