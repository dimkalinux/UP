<?php

define('ADMIN_PAGE', 1);
if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}

require UP_ROOT.'functions.inc.php';

$out = '<div id="status">&nbsp;</div><h2>Users</h2>';
$out .= User::getUsersTable();


printPage($out);
?>

