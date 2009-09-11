<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}


class DB {
	private $link;
	private $query_result;
	private $affected_rows;


	public function __construct() {
		$this->link = $this->connect();
	}


	public function __destruct() {
		$this->close();
	}


	public function query_num() {
		$args = func_get_args();
		$sql = call_user_func_array(array($this, 'makeSafeSQL'), $args);
		return $this->makeSafeQuery($sql, false);
	}

	public function query() {
		$args = func_get_args();
		$sql = call_user_func_array(array($this, 'makeSafeSQL'), $args);
		return $this->makeSafeQuery($sql, false);
	}

	public function silentQuery() {
		$args = func_get_args();
		$sql = call_user_func_array(array($this, 'makeSafeSQL'), $args);
		return $this->makeSafeQuery($sql, true);
	}

	public function numRows()
	{
		$args = func_get_args();
		$sql = call_user_func_array(array($this, 'makeSafeSQL'), $args);
		return $this->safeNumRows($sql);
	}

	public function getRow() {
		$args = func_get_args();
		$sql = call_user_func_array(array($this, 'makeSafeSQL'), $args);
		return $this->getSafeRow($sql);
	}

	public function getData() {
		$args = func_get_args();
		$sql = call_user_func_array(array($this, 'makeSafeSQL'), $args);
		return $this->getSafeData($sql);
	}


	public function lastID() {
		return ($this->link) ? @mysqli_insert_id($this->link) : false;
	}

	public function affected() {
		return ($this->link) ? @mysqli_affected_rows($this->link) : false;
	}


	private function makeSafeQuery($sql, $silent=false) {
		if (mb_strlen($sql, 'UTF-8') > 140000) {
			throw new Exception('MySQL: Insane query.');
		}

		$this->query_result = @mysqli_query($this->link, $sql);

		if (!$this->query_result) {
			$err = @mysqli_error($this->link);

			if (!$silent) {
				$log = new Logger;
				$log->debug("DB query failed: '$sql'");

				throw new Exception('Ошибка базы данных: «'.$err.'»');
			}

			// close connection to DB
			$this->close();

			return false;
		} else {
			return $this->query_result;
		}
	}


	private function me($str) {
		return is_array($str) ? '' : mysqli_real_escape_string($this->link, $str);
	}


	private function getSafeRow($sql) {
		$result = $this->makeSafeQuery($sql);

		if ($result) {
			$row = @mysqli_fetch_assoc($result);

			// free query results
			$this->freeResult();

			return $row;
		} else {
			return false;
		}
	}


	private function getSafeData($sql) {
		$result = $this->makeSafeQuery($sql);

		if ($result) {
			$datas = array();
			while ($res = mysqli_fetch_assoc($result)) {
				$datas[] = $res;
			}
			// free query results
			$this->freeResult();
			return (count($datas) > 0) ? $datas : false;
		} else {
			return false;
		}
	}

	private function safeNumRows($sql) {
		$result = $this->makeSafeQuery($sql);

		if ($result) {
			$num = @mysqli_num_rows($result);

			// free query results
			$this->freeResult();
			return $num;
		} else {
			return false;
		}
	}


	private function connect() {
		$link = @mysqli_connect(MYSQL_ADDRESS, MYSQL_LOGIN, MYSQL_PASSWORD, MYSQL_DB);

		if (!$link || mysqli_connect_errno()) {
			throw new Exception('База данных недоступна.');
		}

		// Setup the client-server character set (UTF-8)
		//mysqli_query($link, "SET NAMES 'utf8'"); //or error(__FILE__, __LINE__);

		return $link;
	}

	private function makeSafeSQL() {
		$args = func_get_args();

		$tmpl =& $args[0];
		$tmpl = str_replace('%', '%%', $tmpl);
		$tmpl = str_replace('?', '%s', $tmpl);

		foreach ($args as $i=>$v) {
			if (!$i) {
				continue;
			}

			$args[$i] = "'".$this->me($v)."'";
		}

		for ($i=$c=count($args)-1; $i < $c+20; $i++) {
			$args[$i+1] = "UNKNOWN_PLACEHOLDER_$i";
		}

		return call_user_func_array('sprintf', $args);
	}

	private function freeResult() {
		return ($this->query_result) ? @mysqli_free_result($this->query_result) : false;
	}

	private function close() {
		if ($this->link) {
			if ($this->query_result) {
				@mysqli_free_result($this->query_result);
			}

			return @mysqli_close($this->link);
		} else {
			return false;
		}
	}
}

?>
