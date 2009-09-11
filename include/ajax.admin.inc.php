<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}

require_once UP_ROOT.'include/ajax.inc.php';


class AJAX_ADMIN extends AJAX {
	public function action_admin_delete_file() {
		if (!is_admin ()) {
			$out = 'недостаточно прав для выполнения операции';
			return;
		}

		$reason = 'удалён администрацией сервера';
		$items = get_post('t_ids');
		$items = explode(':', $items, 100);
		$num = 0;
		$itemsOK = array();
		$db = new DB;

		foreach ($items as $id) {
			if (is_numeric($id) && (intval($id) > 0)) {
				if (!$db->query("UPDATE up SET deleted='1', deleted_reason=?, deleted_date=NOW() WHERE id=? LIMIT 1", $reason, $id)) {
					$out = "невозможно удалить файл ($id)";
					return;
				}

				$itemsOK[] = $id;
				$num++;
			}
		}

		// clear stat cache
		if ($num > 0) {
			clear_stat_cache();
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}


	public function action_admin_undelete_file() {
		if (!is_admin ()) {
			$out = 'недостаточно прав для выполнения операции';
			return;
		}

		$items = get_post('t_ids');
		$items = explode(':', $items, 100);
		$num = 0;
		$itemsOK = array();
		$db = new DB;

		foreach ($items as $id) {
			if (is_numeric($id) && (intval($id) > 0)) {
				if (!$db->query("UPDATE up SET deleted='0' WHERE id=? LIMIT 1", $id)) {
					$out = "невозможно восстановить файл ($id)";
					return;
				}

				$itemsOK[] = $id;
				$num++;
			}
		}

		// clear stat cache
		if ($num > 0) {
			clear_stat_cache();
		}

		$out = implode(":", $itemsOK);
		$result = 1;
		return;
	}




	public function action_admin_mark_as_spam_file() {
		if (!is_admin()) {
			$out = 'недостаточно прав для выполнения операции';
			return;
		}

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
		if (!is_admin ()) {
			$out = 'недостаточно прав для выполнения операции';
			return;
		}

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
		if (!is_admin()) {
			$out = 'недостаточно прав для выполнения операции';
			return;
		}

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
		if (!is_admin ()) {
			$out = 'недостаточно прав для выполнения операции';
			return;
		}

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
	}
}

?>
