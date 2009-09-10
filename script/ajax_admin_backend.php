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
	case ACTION_ADMIN_DELETE_FILE:
		action_admin_delete_file();
		break;

	case ACTION_ADMIN_UNDELETE_FILE:
		action_admin_undelete_file();
		break;

	case ACTION_ADMIN_MARK_AS_SPAM_FILE:
		action_admin_mark_as_spam_file();
		break;

	case ACTION_ADMIN_UNMARK_AS_SPAM_FILE:
		action_admin_unmark_as_spam_file();
		break;

	case ACTION_ADMIN_MARK_AS_ADULT_FILE:
		action_admin_mark_as_adult_file();
		break;

	case ACTION_ADMIN_UNMARK_AS_ADULT_FILE:
		action_admin_unmark_as_adult_file();
		break;

	default:
		$out = 'Отсутствует код действия';
		break;
}


// Log errors
if ($result == 0) {
	$log = new Logger;
	$ip = $user['ip'];
	$login = $user['login'];
	$addMessage = " ip: $ip, login: $login, action: $action";
	$log->debug('AJAX backend error: «'.$out.$addMessage.'»');
}

exit(json_encode(array('result'=> $result, 'message' => $out)));





?>
