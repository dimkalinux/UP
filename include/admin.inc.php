<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}


class User {

	public function login($login, $uid, $savePass) {
		global $cookie_name, $cookieSalt;
		$sid = sha1(uniqid(rand(), true));
		$ip = get_client_ip();

		// expires
		$expire = ($savePass) ? time() + 1209600 : time() + 7200;
		$dbExpire = ($savePass) ? "NOW() + INTERVAL 14 DAY" : "NOW() + INTERVAL 2 HOUR";

		try {
			$db = DB::singleton();
       		$db->query("DELETE FROM session WHERE sid=? AND uid=?", $sid, $uid);
		   	$db->query("INSERT INTO session VALUES(?, ?, INET_ATON(?), $dbExpire, ?)", $sid, $uid, $ip, $login);
		} catch(Exception $e) {
			error($e->getMessage());
		}

		// set login cookie
		upSetCookie($cookie_name, base64_encode($uid.'|'.$sid.'|'.$expire.'|'.sha1($cookieSalt.$uid.$sid.$expire)), $expire);
	}

	public function updateUploadsCounters($uid, $upload, $uploadSize) {
		try {
			$db = DB::singleton();
			$db->query("UPDATE users SET uploads=uploads+?, uploads_size=uploads_size+? WHERE id=?", $upload, $uploadSize, $uid);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}


	public function logout() {
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
			$db = DB::singleton();

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


	public function getUsername($uid) {
		try {
			$db = DB::singleton();

			$row = $db->getRow("SELECT username FROM session WHERE uid=? LIMIT 1", $uid);
			if ($row) {
				return $row['username'];
			}

			return $uid;
		} catch(Exception $e) {
			error($e->getMessage());
		}
	}


	public function logged($ip=false) {
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
			return false;
		}

		try {
			$db = DB::singleton();

			// delete all expires from session DB
			$db->query('DELETE FROM session WHERE expire < NOW()');

			// check sid
			$result = $db->numRows('SELECT sid FROM session WHERE sid=? AND uid=? AND ip=INET_ATON(?) LIMIT 1', $sid, $uid, $ip);
			if ($result !== 1) {
				return false;
			}

			// all OK
			// 1. update expire on DB and Cookie
			$db->query('UPDATE session SET expire=(NOW() + INTERVAL 1 HOUR) WHERE sid=? AND uid=? AND ip=INET_ATON(?) LIMIT 1', $sid, $uid, $ip);
			$expire += 3600;
			upSetCookie($cookie_name, base64_encode($uid.'|'.$sid.'|'.$expire.'|'.sha1($cookieSalt.$uid.$sid.$expire)), $expire);

			// 2. return UID
			return $uid;
		} catch(Exception $e) {
			error($e->getMessage());
		}
	}
}

?>
