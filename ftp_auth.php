<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', '/srv/www/apache/up/htdocs/');
}
require UP_ROOT.'functions.inc.php';

$out = <<<FMB
auth_ok:-1
uid:5000
gid:5000
dir:/tmp
end
\n
FMB;

do {
	$login = mb_substr(getenv('AUTHD_ACCOUNT'), 0, 32);
	$pass = mb_substr(getenv('AUTHD_PASSWORD'), 0, 64);

	if ($login === FALSE || $pass === FALSE) {
		break;
	}

	try {
		$db = new DB;
		$row = $db->getRow('SELECT id,password,username FROM users WHERE username=? LIMIT 1', $login);
		if (!$row) {
			break;
		}

		$user_id = $row['id'];
		$user_password_hash = $row['password'];

		// check password
		require_once UP_ROOT.'include/PasswordHash.php';
		$t_hasher = new PasswordHash(8, FALSE);
		if (!$t_hasher->CheckPassword($pass, $user_password_hash)) {
			break;
		} else {
			$dir = '/var/fuse/'.stripslashes($row['username']);
			$uid = intval(5000+$user_id, 10);
			$gid = intval(5000+$user_id, 10);

			if (is_dir($dir) === FALSE) {
				break;
			}

			$uploadRate = 1048576*5;
			$downloadRate = 1048576*5;

			// login ok
			$out = <<<FMB
auth_ok:1
uid:$uid
gid:$gid
dir:$dir
throttling_bandwidth_ul:$uploadRate
throttling_bandwidth_dl:$downloadRate
end
\n
FMB;

			$log = new Logger;
			$log->info("Вход по фтп: $login $dir");
			break;
		}
	} catch (Exception $e) {
		exit($out);
	}
} while(0);

exit($out);


?>
