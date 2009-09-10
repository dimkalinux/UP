<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'header.php';

$geo = get_geo(get_client_ip());
/*if ($geo == 'world') {
	show_error_message('Для гостей из «мира» список файлов не доступен.');
	exit();
}*/

$blocks = null;

$type = get_get ('type');
// page
$page = get_get ('page');
if (!$page || $page < 1)
	$page = 1;

switch ($type) {
	case 'new':
		$blocks = top_get ($type, $page, "/top/new");
		break;

	case 'size':
		$blocks = top_get ($type, $page, "/top/size");
		break;

	case 'name':
		$blocks = top_get ($type, $page, "/top/name");
		break;

	case 'mp3':
		$blocks = top_get ($type, $page, "/top/mp3");
		break;

	case 'video':
		$blocks = top_get ($type, $page, "/top/video");
		break;

	case 'archive':
		$blocks = top_get ($type, $page, "/top/archive");
		break;

	case 'image':
		$blocks = top_get ($type, $page, "/top/image");
		break;

	case 'popular':
	default:
		$blocks = top_get ($type, $page, "/top/popular");
		break;
}

echo ($blocks);

$addScript = '';
$onDOMReady = '';
$admin = is_admin();
if ($admin) {
		$addScript[] = 'up.admin.js';
		$onDOMReady = 'UP.admin.cbStuffStart();';
}

//$onDOMReady .= "UP.utils.prefetchPage('$type', $page + 1, $admin);";

require UP_ROOT.'footer.php';
exit ();







?>
