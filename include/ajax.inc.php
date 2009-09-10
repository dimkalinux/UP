<?

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}


class AJAX {

	public function search() {
		global $out, $result;

		if (!isset($_GET['s'])) {
			$this->exitWithError('Отсутствует аргумент поиска');
		}

		$req = $_GET['s'];

		try {
			$out = makeSearch($req, false);
			$result = 1;
		} catch (Exception $e) {
			$this->exitWithError($e->getMessage());
		}
	}


	public function on_air() {
		global $out, $result;
		try {
			$out = get_pe();
			$result = 1;
		} catch (Exception $e) {
			$this->exitWithError($e->getMessage());
		}
	}


	public function getUploadURL() {
		global $out, $result;

		try {
			$storage = new Storage;
			$out = $storage->get_upload_url();
			$result = 1;
		} catch (Exception $e) {
			$this->exitWithError($e->getMessage());
		}
	}


	public function exitWithError($msg) {
		exit(json_encode(array('result'=> 0, 'message' => $msg)));
	}
}

?>
