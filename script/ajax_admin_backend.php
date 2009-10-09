<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}

require UP_ROOT.'functions.inc.php';
require_once UP_ROOT.'include/ajax.admin.inc.php';


$result = 0;
$out = 'Внутренняя ошибка AJAX';

if (!isset($_REQUEST['t_action'])) {
	$action = -1;
} else {
	$action = intval($_REQUEST['t_action'], 10);
}


switch ($action) {
	case ACTION_ADMIN_DELETE_FEEDBACK_MESSAGE:
		$ajax = new AJAX_ADMIN;
		$ajax->deleteFeedbackMessage();
		break;

	case ACTION_ADMIN_DELETE_COMMENT:
		$ajax = new AJAX_ADMIN;
		$ajax->deleteComment();
		break;


	case ACTION_ADMIN_DELETE_ITEM:
		$ajax = new AJAX_ADMIN;
		$ajax->deleteItem();
		break;

	case ACTION_ADMIN_UNDELETE_ITEM:
		$ajax = new AJAX_ADMIN;
		$ajax->unDeleteItem();
		break;

	case ACTION_ADMIN_MARK_AS_SPAM_FILE:
		$ajax = new AJAX_ADMIN;
		$ajax->markSpamItem();
		break;

	case ACTION_ADMIN_UNMARK_AS_SPAM_FILE:
		$ajax = new AJAX_ADMIN;
		$ajax->unMarkSpamItem();
		break;

	case ACTION_ADMIN_MARK_AS_ADULT_FILE:
		$ajax = new AJAX_ADMIN;
		$ajax->markAdultItem();
		break;

	case ACTION_ADMIN_UNMARK_AS_ADULT_FILE:
		$ajax = new AJAX_ADMIN;
		$ajax->unMarkAdultItem();
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
