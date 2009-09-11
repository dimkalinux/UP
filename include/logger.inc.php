<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}


class Logger {
	private $db;

	public function __construct() {
		$this->db = @new DB;
	}

	public function __destruct() {
		// ???
	}

	public function info($message) {
		$this->log('info', $message);
	}

	public function debug($message) {
		if (DEBUG === true) {
			$this->log('debug', $message);
		}
	}

	public function warn($message) {
		$this->log('warn', $message);
	}

	public function error($message) {
		$this->log('error', $message);
	}


	private function log($type, $message) {
		if (!$this->db) {
			return;
		}

		if ($message && mb_strlen($message) > 0) {
			$message = mb_substr($message, 0, 2048);

			if (!$this->db->silentQuery("INSERT INTO `logs` VALUES('', NOW(), ?, ?)", $type, $message)) {
				// try log to file
				return;
			}
		}
	}
}

?>
