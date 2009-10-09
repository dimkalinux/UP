<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}

require_once UP_ROOT.'include/ajax.inc.php';


class AJAX_ADMIN extends AJAX {
	public function __construct() {
		global $user;

		if ($user['is_admin'] !== True) {
			parent::exitWithError('Недостаточно прав для выполнения операции');
		}
	}

	public function deleteComment() {
		global $out, $result;

		$item_id = intval(get_get('t_id'), 10);

		try {
			$db = new DB;
			$db->query("DELETE FROM comments WHERE id=? LIMIT 1", $item_id);

			if ($db->affected() == 1) {
				$out = '';
				$result = 1;
				return;
			}
		} catch (Exception $e) {
			parent::exitWithError('Невозможно удалить комментарий: '.$e->getMessage());
		}

		$out = "Невозможно удалить комментарий";
	}

	public function deleteFeedbackMessage() {
		global $out, $result;

		$item_id = intval(get_get('t_id'), 10);

		try {
			$db = new DB;
			$db->query("DELETE FROM feedback WHERE id=? LIMIT 1", $item_id);

			if ($db->affected() == 1) {
				$out = '';
				$result = 1;
				return;
			}
		} catch (Exception $e) {
			parent::exitWithError('Невозможно удалить сообщение: '.$e->getMessage());
		}

		$out = "Невозможно удалить сообщение";
	}

	public function deleteItem() {
		global $out, $result;

		$reason = 'удалён администрацией сервера';
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
				$db->query("UPDATE up SET deleted='1', deleted_reason=?, deleted_date=NOW() WHERE id IN $IN", $reason);
				if ($db->affected() == count($chunkItems)) {
					$itemsOK = array_merge($itemsOK, $chunkItems);
				} else {
					throw new Exception('Admin deleteItem: DB affected != items count');
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


	public function unDeleteItem() {
		global $out, $result;

		$items = get_post('t_ids');
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
				$db->query("UPDATE up SET deleted='0', deleted_reason='' WHERE id IN $IN");
				if ($db->affected() == count($chunkItems)) {
					$itemsOK = array_merge($itemsOK, $chunkItems);
				} else {
					throw new Exception('Admin deleteItem: DB affected != items count');
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

/*


	public function action_admin_mark_as_spam_file() {
		$items = get_post('t_ids');
		$items = explode(':', $items, 100);
		$num = 0;
		$itemsOK = array();
		$db = new DB;

		foreach ($items as $id) {
			if (is_numeric($id) && intval($id) > 0) {
				if (!$db->query("UPDATE up SET spam='1' WHERE id=? LIMIT 1", $id)) {
					$out = "невозможно установить метку спама";
					return;
				}

				$itemsOK[] = $id;
				$num++;
			}
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}

	public function action_admin_unmark_as_spam_file() {
		$items = get_post('t_ids');
		$items = explode(':', $items, 100);
		$num = 0;
		$itemsOK = array();
		$db = new DB;

		foreach ($items as $id) {
			if (is_numeric($id) && intval($id) > 0) {
				if (!$db->query("UPDATE up SET spam='0' WHERE id=? LIMIT 1", $id)) {
					$out = "невозможно снять метку спама";
					return;
				}

				$itemsOK[] = $id;
				$num++;
			}
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}


	public function action_admin_mark_as_adult_file() {
		$items = get_post('t_ids');
		$items = explode(':', $items, 100);
		$num = 0;
		$itemsOK = array();
		$db = new DB;

		foreach ($items as $id) {
			if (is_numeric($id) && intval($id) > 0) {
				if (!$db->query("UPDATE up SET adult='1' WHERE id=? LIMIT 1", $id)) {
					$out = "невозможно установить метку XXX";
					return;
				}

				$itemsOK[] = $id;
				$num++;
			}
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}


	public function action_admin_unmark_as_adult_file() {
		$items = get_post('t_ids');
		$items = explode(':', $items, 100);
		$num = 0;
		$itemsOK = array();
		$db = new DB;

		foreach ($items as $id) {
			if (is_numeric($id) && intval($id) > 0) {
				if (!$db->query("UPDATE up SET adult='0' WHERE id=? LIMIT 1", $id)) {
					$out = "невозможно снять метку XXX";
					return;
				}

				$itemsOK[] = $id;
				$num++;
			}
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}*/

	public function onlyDigit($var) {
		return (is_numeric($var) && (intval($var, 10) > 0));
	}

}

?>
