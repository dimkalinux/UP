<?php
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';

$out = array('error' => 1, 'message' => 'error');

try {
	$storage = new Storage;
	$out['message'] = $storage->get_upload_url();
	$out['error'] = 0;
} catch (Exception $e) {
	$out['message'] = $e->getMessage();
}

exit(json_encode($out));


?>
