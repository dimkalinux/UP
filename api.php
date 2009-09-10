<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';

$blocks = null;
$type = get_get('a');

switch ($type) {
	case 'new':
		$blocks = export_new_files(15);
		break;

	default:
		break;
}
header('Content-Type: application/xml');
echo ($blocks);
exit();


function export_new_files($num) {
	$blocks = null;
	$cache = new Cache;
	if (!$blocks = $cache->get('api_new')) {
		$db = new DB;
		$datas = $db->getData("SELECT * FROM up WHERE deleted='0' AND hidden='0' AND spam='0' AND adult='0' ORDER BY uploaded_date DESC LIMIT $num");
		if ($datas) {
			$blocks = <<<ZZZ
<?xml version="1.0" encoding="utf-8"?>
<lluga>
ZZZ;
			foreach ($datas as $rec) {
				$id = (int) $rec['id'];
				$filename = htmlspecialchars ($rec['filename'], ENT_QUOTES);
				$filesize = $rec['size'];
				$blocks .= <<<ZZZ
<item id="$id" name="$filename" size="$filesize"/>
ZZZ;
			}

			$blocks .= <<<ZZZ
</lluga>
ZZZ;
		}

	$cache->set($blocks, 'api_new', 0);

	}
	return $blocks;
}

?>
