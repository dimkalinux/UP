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


	public function markSpamItem() {
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
				$db->query("UPDATE up SET spam='1' WHERE id IN $IN");
				if ($db->affected() == count($chunkItems)) {
					$itemsOK = array_merge($itemsOK, $chunkItems);
				} else {
					throw new Exception('DB affected != items count');
				}
			}
		} catch (Exception $e) {
			parent::exitWithError('Невозможно установить метку спама: '.$e->getMessage());
		}

		// clear stat cache
		if (count($itemsOK) > 0) {
			clear_stat_cache();
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}


	public function unMarkSpamItem() {
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
				$db->query("UPDATE up SET spam='0' WHERE id IN $IN");
				if ($db->affected() == count($chunkItems)) {
					$itemsOK = array_merge($itemsOK, $chunkItems);
				} else {
					throw new Exception('DB affected != items count');
				}
			}
		} catch (Exception $e) {
			parent::exitWithError('Невозможно снять метку спама: '.$e->getMessage());
		}

		// clear stat cache
		if (count($itemsOK) > 0) {
			clear_stat_cache();
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}


	public function markAdultItem() {
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
				$db->query("UPDATE up SET adult='1' WHERE id IN $IN");
				if ($db->affected() == count($chunkItems)) {
					$itemsOK = array_merge($itemsOK, $chunkItems);
				} else {
					throw new Exception('DB affected != items count');
				}
			}
		} catch (Exception $e) {
			parent::exitWithError('Невозможно установить метку +16: '.$e->getMessage());
		}

		// clear stat cache
		if (count($itemsOK) > 0) {
			clear_stat_cache();
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}


	public function unMarkAdultItem() {
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
				$db->query("UPDATE up SET adult='0' WHERE id IN $IN");
				if ($db->affected() == count($chunkItems)) {
					$itemsOK = array_merge($itemsOK, $chunkItems);
				} else {
					throw new Exception('DB affected != items count');
				}
			}
		} catch (Exception $e) {
			parent::exitWithError('Невозможно снять метку +16: '.$e->getMessage());
		}

		// clear stat cache
		if (count($itemsOK) > 0) {
			clear_stat_cache();
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}


	public function hideItem() {
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
				$db->query("UPDATE up SET hidden='1' WHERE id IN $IN");
				if ($db->affected() == count($chunkItems)) {
					$itemsOK = array_merge($itemsOK, $chunkItems);
				} else {
					throw new Exception('DB affected != items count');
				}
			}
		} catch (Exception $e) {
			parent::exitWithError('Невозможно скрыть файл: '.$e->getMessage());
		}

		// clear stat cache
		if (count($itemsOK) > 0) {
			clear_stat_cache();
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}


	public function unHideItem() {
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
				$db->query("UPDATE up SET hidden='0' WHERE id IN $IN");
				if ($db->affected() == count($chunkItems)) {
					$itemsOK = array_merge($itemsOK, $chunkItems);
				} else {
					throw new Exception('DB affected != items count');
				}
			}
		} catch (Exception $e) {
			parent::exitWithError('Невозможно показать файл: '.$e->getMessage());
		}

		// clear stat cache
		if (count($itemsOK) > 0) {
			clear_stat_cache();
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}


	private function onlyDigit($var) {
		return (is_numeric($var) && (intval($var, 10) > 0));
	}

}

?>
