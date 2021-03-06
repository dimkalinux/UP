<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}

require_once UP_ROOT.'functions.inc.php';
require_once UP_ROOT.'include/ajax.owner.inc.php';

// -----------------
$result = 0;
$out = '';

if (!isset($_REQUEST['t_action'])) {
	$action = -1;
} else {
	$action = intval($_REQUEST['t_action'], 10);
}

switch ($action) {

	case ACTION_OWNER_GET_UPDATED_ITEMS:
		$ajax = new AJAX_OWNER;
		$ajax->getUpdatedUserFiles();
		break;


	case ACTION_OWNER_DELETE_ITEM:
		$ajax = new AJAX_OWNER;
		$ajax->deleteItems();
		break;

	case ACTION_OWNER_UNDELETE_ITEM:
		$ajax = new AJAX_OWNER;
		$ajax->unDeleteItems();
		break;

	case ACTION_GET_COMMENTS:
		$ajax = new AJAX;
		$ajax->getComments();
		break;


	case ACTION_SEARCH:
		$ajax = new AJAX;
		$ajax->search();
		break;

	case ACTION_GET_PE:
		$ajax = new AJAX;
		$ajax->on_air();
		break;

	case ACTION_GET_UPLOAD_URL:
		$ajax = new AJAX;
		$ajax->getUploadURL();
		break;

	case ACTION_DELETE_FILE:
		$ajax_owner = new AJAX_OWNER;
		$ajax_owner->deleteItem();
		break;

	case ACTION_UNDELETE_FILE:
		$ajax_owner = new AJAX_OWNER;
		$ajax_owner->unDeleteItem();
		break;

	case ACTION_MAKE_ME_OWNER:
		$ajax_owner = new AJAX_OWNER;
		$ajax_owner->makeMeOwner();
		break;

	case ACTION_RENAME_FILE:
		$ajax_owner = new AJAX_OWNER;
		$ajax_owner->renameItem();
		break;

	case ACTION_CHANGE_PASSWORD:
		$ajax_owner = new AJAX_OWNER;
		$ajax_owner->changePassword();
		break;

	default:
		$out = 'Неизвестная команда';
		break;
}


// Log errors
if ($result === 0) {
	$log = new Logger;
	$ip = $user['ip'];
	$login = $user['login'];
	$addMessage = " ip: $ip, login: $login, action: $action";
	$log->debug('AJAX backend error: «'.$out.$addMessage.'»');
}

exit(json_encode(array('result'=> $result, 'message' => $out)));


?>
