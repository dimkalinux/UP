<?

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}

class Cache {
	private $link;

	public function __construct() {
		$this->link = $this->connect();
	}

	public function __destruct() {
		//$this->close();
	}

	public function add($object, $key, $expire) {
		return true;
	}

	public function set($object, $key, $expire) {
		return true;
	}


	public function inc($key, $expire) {

	}

	public function get($key) {
		return null;
	}

	public function unlink($key) {
		return true;
	}

	public function replace($object, $key) {
		return true;
	}

	public function flush() {
		return true;
	}

	private function connect() {
		return true;
	}
}

?>
