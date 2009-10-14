<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}

require_once UP_ROOT.'include/ajax.inc.php';

class AJAX_OWNER extends AJAX {

	public function deleteItems() {
		global $user, $out, $result;

		$reason = 'удалён владельцем файла';
		$items = explode(':', get_post('t_ids'), 101);
		$itemsOK = array();

		function onlyDigit($var) {
			return (is_numeric($var) && (intval($var, 10) > 0));
		}

		try {
			if (!is_array($items) || count($items) < 1) {
				throw new Exception('Empty items');
			}

			$db = new DB;

			$superItems = array_chunk(array_filter($items, "onlyDigit"), 10, FALSE);

			foreach ($superItems as $chunkItems) {
				$IN = '('.implode(",", $chunkItems).')';
				$db->query("UPDATE up SET deleted='1', deleted_reason=?, deleted_date=NOW() WHERE id IN $IN AND user_id=?", $reason, $user['id']);
				if ($db->affected() == count($chunkItems)) {
					$itemsOK = array_merge($itemsOK, $chunkItems);
				} else {
					throw new Exception('DB affected != items count');
				}
			}
		} catch (Exception $e) {
			parent::exitWithError('Невозможно удалить файл: '.$e->getMessage());
		}

		// clear stat cache
		if (count($itemsOK) > 0) {
			clear_stat_cache();
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}

	public function unDeleteItems() {
		global $user, $out, $result;

		$items = explode(':', get_post('t_ids'), 101);
		$itemsOK = array();

		function onlyDigit($var) {
			return (is_numeric($var) && (intval($var, 10) > 0));
		}

		try {
			if (!is_array($items) || count($items) < 1) {
				throw new Exception('Empty items');
			}

			$db = new DB;

			$superItems = array_chunk(array_filter($items, "onlyDigit"), 10, FALSE);

			foreach ($superItems as $chunkItems) {
				$IN = '('.implode(",", $chunkItems).')';
				$db->query("UPDATE up SET deleted='0', deleted_reason='' WHERE id IN $IN AND user_id=?", $user['id']);
				if ($db->affected() == count($chunkItems)) {
					$itemsOK = array_merge($itemsOK, $chunkItems);
				} else {
					throw new Exception('DB affected != items count');
				}
			}
		} catch (Exception $e) {
			parent::exitWithError('Невозможно восстановить файл: '.$e->getMessage());
		}

		// clear stat cache
		if (count($itemsOK) > 0) {
			clear_stat_cache();
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}


	public function deleteItem() {
		global $user, $out, $result;

		$item_id = intval(get_get('t_id'), 10);
		$owner_key = intval(get_get('t_magic'), 10);
		$reason = 'удалён владельцем файла';

		try {
			$db = new DB;

			// check for magic
			if ($owner_key === 0) {
				// no magic - try login
				if ($user['is_guest']) {
					$out = "вы не авторизированы";
					return;
				}

				// delete as owner
				$db->query("UPDATE up SET deleted='1', deleted_reason=?, deleted_date=NOW() WHERE id=? AND user_id=? LIMIT 1", $reason, $item_id, $user['id']);
			} else {
				// delete with magic key
				$db->query("UPDATE up SET deleted='1', deleted_reason=?, deleted_date=NOW() WHERE id=? AND delete_num=? LIMIT 1", $reason, $item_id, $owner_key);
			}

			if ($db->affected() == 1) {
				clear_stat_cache();

				// delete file from dnow table
				$db->query("DELETE FROM dnow where id=?", $item_id);

				$out = <<<ZZZ
				Файл успешно удален.<span class=\"as_js_link\" onmousedown=\"UP.owner.unRemove('$item_id', '$owner_key')\">Отменить</span>
ZZZ;
				$result = 1;
				return;
			}
		} catch (Exception $e) {
			parent::exitWithError('невозможно удалить файл');
		}

		$out = "невозможно удалить файл";
	}


	public function unDeleteItem() {
		global $user, $out, $result;

		$item_id = intval(get_get('t_id'), 10);
		$owner_key = intval(get_get('t_magic'), 10);

		try {
			$db = new DB;

			// check for magic
			if ($owner_key === 0) {
				// no magic - try login
				if ($user['is_guest']) {
					$out = "вы не авторизированы";
					return;
				}

				// UNdelete as owner
				$db->query("UPDATE up SET deleted='0', deleted_reason='' WHERE id=? AND user_id=? LIMIT 1", $item_id, $user['id']);
			} else {
				// UNdelete with magic key
				$db->query("UPDATE up SET deleted='0', deleted_reason='' WHERE id=? AND delete_num=? LIMIT 1", $item_id, $owner_key);
			}

			if ($db->affected() == 1) {
				clear_stat_cache ();
				$out = "Файл восстановлен";
				$result = 1;
				return;
			}
		} catch (Exception $e) {
			parent::exitWithError('невозможно отменить удаление');
		}

		$out = "невозможно отменить удаление";
	}


	public function makeMeOwner() {
		global $user, $out, $result;

		$item_id = intval(get_get ('t_id'), 10);
		$owner_key = intval(get_get ('t_magic'), 10);
		$owner_id = intval(get_get ('t_uid'), 10);

		// check logged
		if (($user['is_guest']) || ($owner_id !== $user['id'])) {
			$out = "вы не авторизированы";
			return;
		}


		try {
			$db = new DB;

			// make me owner
			$db->query("UPDATE up SET user_id=? WHERE id=? AND delete_num=? AND user_id=0", $user['id'], $item_id, $owner_key);

			if ($db->affected() == 1) {
				$row = $db->getRow("SELECT size FROM up WHERE id=?", $item_id);
				User::updateUploadsCounters($user['id'], 1, $row['size']);
				$out = "Вы стали владельцем этого файла";
				$result = 1;
				return;
			}
		} catch (Exception $e) {
			parent::exitWithError('невозможно сменить владельца файла');
		}

		$out = "невозможно сменить владельца файла";
	}


	public function renameItem() {
		global $user, $out, $result;

		$item_id = intval(get_post('t_id'), 10);
		$owner_key = intval(get_post('t_magic'), 10);

		if (!isset($_POST['t_new_name']) || mb_strlen($_POST['t_new_name']) < 1) {
			$out = "невозможно переименовать файл";
			return;
		} else {
			$newName = $_POST['t_new_name'];
		}


		try {
			$db = new DB;

			// check for magic
			if ($owner_key === 0) {
				// no magic - try login
				if ($user['is_guest']) {
					$out = "вы не авторизированы";
					return;
				}

				// rename as owner
				$db->query("UPDATE up SET filename=? WHERE id=? AND user_id=? LIMIT 1", $newName, $item_id, $user['id']);
			} else {
				$db->query("UPDATE up SET filename=? WHERE id=? AND delete_num=? LIMIT 1", $newName, $item_id, $owner_key);
			}

			if ($db->affected() == 1) {
				$out = check_plain($newName);
				$result = 1;
				return;
			}
		} catch (Exception $e) {
			parent::exitWithError('невозможно переименовать файл');
		}

		$out = "невозможно переименовать файл";
	}

	public function changePassword() {
		global $user, $out, $result;

		$item_id = intval(get_post('t_id'), 10);
		$owner_key = intval(get_post('t_magic'), 10);

		if (!isset($_POST['t_password']) || mb_strlen($_POST['t_password']) < 1) {
			$out = "невозможно сменить пароль";
			return;
		}

		try {
			require UP_ROOT.'include/PasswordHash.php';
			$t_hasher = new PasswordHash(8, FALSE);
			$cryptPassword = $t_hasher->HashPassword($_POST['t_password']);

			$db = new DB;

			// check for magic
			if ($owner_key === 0) {
				// no magic - try login
				if ($user['is_guest']) {
					$out = "вы не авторизированы";
					return;
				}

				$db->query("UPDATE up SET password=? WHERE id=? AND user_id=? LIMIT 1", $cryptPassword, $item_id, $user['id']);
			} else {
				$db->query("UPDATE up SET password=? WHERE id=? AND delete_num=? LIMIT 1", $cryptPassword, $item_id, $owner_key);
			}

			if ($db->affected() == 1) {
				$out = ':-)';
				$result = 1;
				return;
			}
		} catch (Exception $e) {
			parent::exitWithError('невозможно сменить пароль');
		}

		$out = "невозможно сменить пароль";
	}
}

?>
