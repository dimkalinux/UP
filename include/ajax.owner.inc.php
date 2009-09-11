<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}

require_once UP_ROOT.'include/ajax.inc.php';

class AJAX_OWNER extends AJAX {

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
			$this->exitWithError('невозможно удалить файл');
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
			$this->exitWithError('невозможно отменить удаление');
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
			$this->exitWithError('невозможно сменить владельца файла');
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
			$this->exitWithError('невозможно переименовать файл');
		}

		$out = "невозможно переименовать файл";
	}

	public function md5() {
		global $user, $out, $result, $upload_dir;

		$item_id = intval(get_post('t_id'), 10);
		$owner_key = intval(get_post('t_magic'), 10);
		$md5 = '';

		try {
			$db = new DB;

			// check for magic
			if ($owner_key === 0) {
				// no magic - try login
				if ($user['is_guest']) {
					$out = "вы не авторизированы";
					return;
				}

				// get file
				$row = $db->getRow("SELECT sub_location, location WHERE id=? AND user_id=? LIMIT 1", $item_id, $user['id']);
				if (!$row) {
					$out = 'невозможно вычислить контрольную сумму';
					return;
				}

				$file = $upload_dir.$row['sub_location'].'/'.$row['location'];
				if (!file_exists($file)) {
					$out = 'невозможно вычислить контрольную сумму';
					return;
				}

				$md5 = md5_file($file);
				$db->query("UPDATE up SET md5=? WHERE id=? AND user_id=? LIMIT 1", $md5, $item_id, $user['id']);
			} else {
				$db->query("SELECT sub_location, location WHERE id=? AND delete_num=? LIMIT 1", $item_id, $owner_key);
				if (!$row) {
					$out = 'невозможно вычислить контрольную сумму';
					return;
				}

				$file = $upload_dir.$row['sub_location'].'/'.$row['location'];
				if (!file_exists($file)) {
					$out = 'невозможно вычислить контрольную сумму';
					return;
				}

				$md5 = md5_file($file);
				$db->query("UPDATE up SET md5=? WHERE id=? AND delete_num=? LIMIT 1", $md5, $item_id, $owner_key);
			}

			if ($db->affected() == 1) {
				$out = $md5;
				$result = 1;
				return;
			}
		} catch (Exception $e) {
			$this->exitWithError('невозможно вычислить контрольную сумму');
		}

		$out = "невозможно вычислить контрольную сумму";
	}
}

?>
