<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';


$err = 0;
$errMsg = "&nbsp;";
$statusType = 'default';
$myFiles = '';
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id'], 10) : FALSE;


try {
	if ($user_id === FALSE) {
		throw new Exception('Неизвестный пользователь');
	}

	$username = check_plain(User::getUserUsername($user_id));
} catch (Exception $e) {
	show_error_message($e->getMessage());
}

$out = <<<FMB
	<div id="status"><span type="$statusType">$errMsg</span></div>
	<h2>Файлы пользователя $username</h2>
FMB;

$myFiles = User::getUserFiles($user_id);

printPage($out.$myFiles);
?>
