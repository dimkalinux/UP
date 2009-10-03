<?

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}

if (DEBUG === true) {
	require_once UP_ROOT.'include/formaldehyde.inc.php';
}

class AJAX {

	public function getComments() {
		global $out, $result;

		if (!isset($_POST['t_id'])) {
			$this->exitWithError('Отсутствует аргумент');
		}

		$item_id = intval($_POST['t_id'], 10);

		try {
			require UP_ROOT.'include/comments.inc.php';
			$comments = new Comments($item_id);
			$out = $comments->getCommentList();
			$result = 1;
		} catch (Exception $e) {
			$this->exitWithError($e->getMessage());
		}
	}

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


	protected function exitWithError($msg) {
		exit(json_encode(array('result'=> 0, 'message' => $msg)));
	}
}

?>
