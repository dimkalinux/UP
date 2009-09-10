<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';

$form_action = $base_url.'profile.php';
$csrf = generate_form_token($form_action);
$err = 0;

// check for cancel
if (isset($_POST['cancel'])) {
	header('Location: /');
	exit();
}

$out = <<<ZZZ
	<div id="status"><span type="$statusType">$errMsg</span></div>
	<h2>Профиль</h2>
	<ul id="tabmenu">
		<li><a href="/profile_files.php">Файлы</a></li>
		<li class="current"><a href="/profile_bookmarks.php">Закладки</a></li>
		<li><a href="/profile_options.php">Настройки</a></li>
	</ul>
ZZZ;
$is_logged = User::logged();
$bookmarks = getUserBookmarkedFiles($is_logged);

require UP_ROOT.'header.php';
echo($out.$bookmarks);


$onDOMReady = 'UP.owner.cbStarredStart();';
require UP_ROOT.'footer.php';
?>
