<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}


class User {

	public static function getCurrentUser() {
		$user = array("ip" => '', "id" => 0, "login" => '', "is_admin" => false, "is_guest" => true);

		$user['ip'] = get_client_ip();

		try {
			$is_logged = User::logged();
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}

		if ($is_logged === false) {
			// is guest
			$user['id'] = 0;
			$user['is_admin'] = false;
			$user['is_guest'] = true;
		} else {
			$user['id'] = $is_logged;
			$user['is_guest'] = false;
			$user['login'] = User::getUsername($is_logged);
		}

		return $user;
	}


	public static function login($login, $uid, $savePass=true) {
		global $cookie_name, $cookieSalt;
		$sid = sha1(uniqid(rand(), true));
		$ip = get_client_ip();

		// expires
		$expire = time() + 1209600;
		$dbExpire = 'NOW() + INTERVAL 14 DAY';

		try {
			$db = new DB;
       		$db->query("DELETE FROM session WHERE sid=? AND uid=?", $sid, $uid);
		   	$db->query("INSERT INTO session VALUES(?, ?, INET_ATON(?), $dbExpire, ?)", $sid, $uid, $ip, $login);
		} catch(Exception $e) {
			error($e->getMessage());
		}

		// set login cookie
		upSetCookie($cookie_name, base64_encode($uid.'|'.$sid.'|'.$expire.'|'.sha1($cookieSalt.$uid.$sid.$expire)), $expire);
	}

	public static function getUserFiles($user_id) {
		$out = '';
		if (!$user_id) {
			return $out;
		}

		try {
			$db = new DB;
			$datas = $db->getData("SELECT *, DATEDIFF(NOW(), GREATEST(last_downloaded_date,uploaded_date)) as NDI FROM up WHERE user_id=? AND deleted=0 LIMIT 100", $user_id);
		} catch (Exception $e) {
			error($e->getMessage());
		}

		if ($datas) {
			$out = <<<FMB
				<table class="t1" id="myFilesTable">
					<thead>
					<tr>
						<th class="center checkbox"><input type="checkbox" id="allCB"/></th>
						<th class="left size">Размер</th>
						<th class="left name">Имя файла</th>
						<th class="center download">Скачан</th>
						<th class="center time">Срок</th>
					</tr>
					</thead>
					<tbody>
FMB;

			foreach ($datas as $item) {
				$item_id = intval($item['id']);
				$filename = get_cool_and_short_filename ($item['filename'], 45);
				$filesize_text = format_filesize ($item['size']);
				$downloaded = $item['downloads'];
				$item_pass = $item['delete_num'];
				$wakkamakka = get_time_of_die ($item['size'], $item['downloads'], $item['NDI'], (bool)$item['spam']);
				if ($wakkamakka < 1) {
					$wakkamakka_text = 'сегодня будет удалён';
				} else {
					$wakkamakka_text = format_days($wakkamakka);
				}


				$out .= <<<FMB
					<tr>
						<td class="center"><input type="checkbox" value="1" id="item_cb_'.$item_id.'"/></td>
						<td class="left">$filesize_text</td>
						<td class="left"><a rel="nofollow" href="/$item_id/$item_pass/">$filename</a></td>
						<td class="center">$downloaded</td>
						<td class="center">$wakkamakka_text</td>
					</tr>
FMB;
				}

				// make all answer with header
				$out .= <<<FMB
					</tbody>
					</table>
FMB;
		}

		return ($out);
	}


	public static function updateUploadsCounters($uid, $upload, $uploadSize) {
		try {
			$db = new DB;
			$db->query("UPDATE DELAYED users SET uploads=uploads+?, uploads_size=uploads_size+? WHERE id=?", $upload, $uploadSize, $uid);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public static function getUsersTable() {
		try {
			$db = new DB;
			$datas = $db->getData("SELECT id,username,uploads,uploads_size FROM users ORDER BY id");
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}


		if ($datas) {
			$out = <<<FMB
			<table class="t1" id="search_files_table">
			<thead>
			<tr>
				<th class="left">Логин</th>
				<th class="center">Файлов</th>
				<th class="center">Размер</th>
			</tr>
			</thead>
			<tbody>
FMB;

			foreach ($datas as $rec) {
				$login = "<a href=\"{$rec['id']}\">".check_plain($rec['username'])."</a>";
				$uploadsSize = format_filesize($rec['uploads_size']);

				$out .= <<<FMB
				<tr>
					<td class="left">$login</td>
					<td class="center">{$rec['uploads']}</td>
					<td class="center">$uploadsSize</td>
			</tr>
FMB;
			}

			$out .= <<<FMB
			</tbody>
			</table>
FMB;

		}

		return $out;
	}


	public static function logout() {
		global $cookie_name, $cookieSalt;

		if (!isset($_COOKIE[$cookie_name])) {
			return false;
		}

		$ip = get_client_ip();
		list($uid, $sid, $expire, $checksum) = explode('|', base64_decode($_COOKIE[$cookie_name]), 4);
		// safe data
		$uid = (int) $uid;
		$expire = (int) $expire;

		// logouted cookie?
		if ($uid === 0) {
			return false;
		}

		// check checksum
		if ($checksum != sha1($cookieSalt.$uid.$sid.$expire)) {
			$log = new Logger;
			$log->info('Invalid cookie checksum: logout');
			return false;
		}

		try {
			$db = new DB;

			// delete all expires from session DB
			$db->query('DELETE FROM session WHERE expire < NOW()');

			// check sid
			$result = $db->numRows('SELECT sid FROM session WHERE sid=? AND uid=? AND ip=INET_ATON(?) LIMIT 1', $sid, $uid, $ip);
			if ($result !== 1) {
				return false;
			}

			// all OK
			// 1. delete from session DB
			$db->query('DELETE FROM session WHERE sid=? AND uid=? AND ip=INET_ATON(?) LIMIT 1', $sid, $uid, $ip);

			// 2. set logouted cookie
			$expire += 1209600;
			$randomSID = sha1(uniqid(rand(), true));
			upSetCookie($cookie_name, base64_encode('0|'.$randomSID.'|'.$expire.'|'.sha1($cookieSalt.'0'.$randomSID.$expire)), $expire);
		} catch(Exception $e) {
			error($e->getMessage());
		}
	}


	public static function getUsername($uid) {
		try {
			$db = new DB;

			$row = $db->getRow("SELECT username FROM session WHERE uid=? LIMIT 1", $uid);
			if ($row) {
				return $row['username'];
			}

			return '';
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}


	public static function getUserEmail($uid) {
		try {
			$db = new DB;

			$row = $db->getRow("SELECT email FROM users WHERE id=? LIMIT 1", $uid);
			if ($row) {
				return $row['email'];
			}

			return '';
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}


	public static function logged($ip=false) {
		global $cookie_name, $cookieSalt;

		if (!isset($_COOKIE[$cookie_name])) {
			return false;
		}

		if ($ip === false) {
			$ip = get_client_ip();
		}

		list($uid, $sid, $expire, $checksum) = explode('|', base64_decode($_COOKIE[$cookie_name]), 4);
		// safe data
		$uid = intval ($uid, 10);
		$expire = (int) $expire;

		// logouted cookie?
		if ($uid === 0) {
			return false;
		}

		// check checksum
		if ($checksum != sha1($cookieSalt.$uid.$sid.$expire)) {
			$log = new Logger;
			$log->info('Invalid cookie checksum: logged '.$uid);
			return false;
		}

		try {
			$db = new DB;

			// delete all expires from session DB
			$db->query('DELETE FROM session WHERE expire < NOW()');

			// check sid
			$result = $db->numRows('SELECT sid FROM session WHERE sid=? AND uid=? AND ip=INET_ATON(?) LIMIT 1', $sid, $uid, $ip);
			if ($result !== 1) {
				return false;
			}

			// all OK
			// 1. update expire on DB and Cookie
			$db->query('UPDATE session SET expire=(NOW() + INTERVAL 14 DAY) WHERE sid=? AND uid=? AND ip=INET_ATON(?) LIMIT 1', $sid, $uid, $ip);
			$expire = time() + 1209600;
			upSetCookie($cookie_name, base64_encode($uid.'|'.$sid.'|'.$expire.'|'.sha1($cookieSalt.$uid.$sid.$expire)), $expire);

			// 2. return UID
			return $uid;
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
}

?>
