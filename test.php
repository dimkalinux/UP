<?php
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';

$quoted = FALSE;
$span_start = ($quoted === TRUE) ? '1' : '2';

echo $span_start;

?>
