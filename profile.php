<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';

$form_action = $base_url.'profile.php';
$csrf = generate_form_token($form_action);
$err = 0;
$errMsg = "&nbsp;";
$statusType = 'default';
$loginLabelClass = $passwordLabelClass = $agreementLabelClass = 'good';

// check for cancel
if (isset($_POST['cancel'])) {
	header('Location: /');
	exit();
}


if (isset($_POST['form_sent'])) {
	do {
		// part 1

		// 1. check csrf
		if (!check_form_token($csrf)) {
			$err = 1;
			$errMsg='действие заблокировано системой безопасности';
			$statusType = 'error';
			break;
		}

		// check login
		if (!isset($_POST['l']) || (mb_strlen($_POST['l'], 'UTF-8') < 6) || (mb_strlen($_POST['l'], 'UTF-8') > 32)) {
			$err = 1;
			$loginLabelClass = 'bad';
		}

		// check password
		if (!isset($_POST['p']) || (mb_strlen($_POST['p'], 'UTF-8') < 6) || (mb_strlen($_POST['p'], 'UTF-8') > 128)) {
			$err = 1;
			$passwordLabelClass = 'bad';
		}

		if ($err !== 0) {
			$errMsg = 'исправьте ошибки в полях, выделенных красным цветом';
			$statusType = 'error';
			break;
		}

		$form_username = mb_substr($_POST['l'], 0, 32, 'UTF-8');
		$form_password = mb_substr($_POST['p'], 0, 128, 'UTF-8');
		$save_pass = isset($_POST['s']);

		// part 2
		try {
			$db = DB::singleton();
			$row = $db->getRow('SELECT id,password FROM users WHERE username=? LIMIT 1', $form_username);
			if (!$row) {
				$err = 1;
				$errMsg = 'Неверный логин или пароль';
				$statusType = 'error';
				break;
			}


			$user_id = $row['id'];
			$user_password_hash = $row['password'];

			// check password
			$t_hasher = new PasswordHash(8, FALSE);
			if (!$t_hasher->CheckPassword($form_password, $user_password_hash)) {
				$err = 1;
				$errMsg = 'Неверный логин или пароль';
				$statusType = 'error';
				break;
			}

		} catch (Exception $e) {
			error($e->getMessage());
		}


		// realy login
		User::login($user_id, $save_pass);
		if (isset($_POST['do'])) {
			header('Location: /');
			exit();
		}
	}
	while (0);
}



// is async request
if (isset($_GET['json'])) {
	$result = array('error'=> $err, 'message' => $errMsg);
	exit(json_encode($result));
}


$out = <<<ZZZ
	<div id="status"><span type="$statusType">$errMsg</span></div>
	<h2>{$user['login']}</h2>
	<ul>
		<li><a href="/profile/change_password/">Сменить пароль</a></li>
	</ul>
	</form>
ZZZ;

printPage($out);

?>
