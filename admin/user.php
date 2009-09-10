<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require UP_ROOT.'functions.inc.php';

if (is_admin() != true) {
	show_error_message('Доступ защищён зарослями фиалок и лютиков.');
	exit();
}

$out = '<div id="status">&nbsp;</div><h2>Users</h2>';
$out .= User::getUsersTable();


require UP_ROOT.'header.php';
echo ($out);
require UP_ROOT.'footer.php';

exit();
?>

