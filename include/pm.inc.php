<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}


class PM {
	private $db;

	public function __construct() {
		try {
			$this->db = @new DB;
		} catch(Exception $e) {
			throw new Exception('PM error: '.$e->getMessage());
		}
	}

	public function __destruct() {
		// ???
	}


	public function deliver($user_id) {
		try {
			if (!$user_id) {
				throw new Exception('PM error: «invalid user id»');
			}
			$this->db->query("UPDATE messages SET status='delivered' WHERE receive_id=? AND status='sent'", $user_id);
		} catch(Exception $e) {
			throw new Exception('PM error: '.$e->getMessage());
		}
	}

	public function getUnreadCount($user_id) {
		try {
			$this->checkUserID();

			$row = $this->db->getRow("SELECT count(id) AS num_unread FROM messages WHERE receiver_id=? AND status='delivered' AND deleted_by_receiver=0", $user_id);
			return intval($row['num_unread'], 10);
		} catch(Exception $e) {
			throw new Exception('PM error: '.$e->getMessage());
		}
	}


	public function deleteMessage($message_id) {
		try {


		} catch() {

		}

	}

	public function sendMessage() {

	}


	public function getMyInbox($user_id) {

	}


	public function getMyOutbox($user_id) {


	}






	private function checkUserID($user_id) {
		$user_id = intval($user_id, 10) || 0;

		if ($user_id < 0) {
			throw new Exception('PM error: «invalid user id»');
		}
	}
}

?>
