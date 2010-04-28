<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', '/srv/www/apache/up/htdocs/');
}
require UP_ROOT.'functions.inc.php';

$out = <<<FMB
auth_ok:-1
uid:-1
gid:-1
dir:/tmp
end
\n
FMB;

do {
	// no FTP access
	if ($ftpAccessEnabled === FALSE) {
		break;
	}

	$login = mb_substr(getenv('AUTHD_ACCOUNT'), 0, 32);
	$pass = mb_substr(getenv('AUTHD_PASSWORD'), 0, 64);

	if ($login === FALSE || $pass === FALSE) {
		break;
	}

	try {
		$db = DB::singleton;
		$row = $db->getRow('SELECT id,password,username FROM users WHERE username=? LIMIT 1', $login);
		if (!$row) {
			break;
		}

		$user_id = $row['id'];
		$user_password_hash = $row['password'];

		// check password
		$t_hasher = new PasswordHash(8, FALSE);
		if (!$t_hasher->CheckPassword($pass, $user_password_hash)) {
			break;
		} else {
			$dir = $ftpbaseDir.stripslashes($row['username']);
			$uid = intval($ftpUIDBase + $user_id, 10);
			$gid = intval($ftpGIDBase + $user_id, 10);

			if (is_dir($dir) === FALSE) {
				exec('ls /var/fuse/ >> /dev/null');
				sleep (1);
				if (is_dir($dir) === FALSE) {
					break 2;
				}
			}


			// IF SYSTEM not BUSY
			if (getServerLoad() < 1) {
				$ftpUploadRate = $ftpUploadRate * $ftpRateK;
				$ftpDownloadRate = $ftpDownloadRate * $ftpRateK;
			}

			// LOGIN OK
			$out = <<<FMB
auth_ok:1
uid:$uid
gid:$gid
dir:$dir
throttling_bandwidth_ul:$ftpUploadRate
throttling_bandwidth_dl:$ftpDownloadRate
end
\n
FMB;

			$log = new Logger;
			$log->debug("Вход по фтп: $login");
			break;
		}
	} catch (Exception $e) {
		$log = new Logger;
		$log->debug("Вход по фтп: exception");
		exit($out);
	}
} while(0);

exit($out);


?>
