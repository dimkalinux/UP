<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';

//$st = new Storage;
#$st->migrate_storage_to_db();

$items = array(1,2,3,4,"5","i6",7,8);
var_dump($items);
echo "<br/>";
var_dump(array_filter($items, "onlyDigit"));

//var_dump($items);

function onlyDigit($var) {
    return (is_numeric($var) && (intval($var, 10) > 0));
}


/*
foreach ($superItems as $it) {
	echo '('.implode(",", $it).')';
	//print_r($it);
}*/
?>
