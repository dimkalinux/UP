<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';

$form_action = $base_url.'login/';
$csrf = generate_form_token($form_action);
$err = 0;
$errMsg = "&nbsp;";
$statusType = 'default';
$loginLabelClass = $passwordLabelClass = 'good';
$errFields = array();

// check for cancel
if (isset($_POST['cancel'])) {
	header('Location: /');
	exit();
}


if (isset($_POST['form_sent'])) {
	do {
		// 1. check csrf
		if (!check_form_token($csrf)) {
			$err = 1;
			$errMsg='действие заблокировано системой безопасности';
			$statusType = 'error';
			break;
		}

		// check login
		if (!isset($_POST['l']) || (mb_strlen($_POST['l'], 'UTF-8') < 4) || (mb_strlen($_POST['l'], 'UTF-8') > 32)) {
			$err = 1;
			$loginLabelClass = 'bad';
			$errFields[] = 'l';
		}

		// check password
		if (!isset($_POST['p']) || (mb_strlen($_POST['p'], 'UTF-8') < 8) || (mb_strlen($_POST['p'], 'UTF-8') > 64)) {
			$err = 1;
			$passwordLabelClass = 'bad';
			$errFields[] = 'p';
		}

		if ($err !== 0) {
			$errMsg = 'Исправьте ошибки в полях, выделенных красным цветом';
			$statusType = 'error';
			break;
		}

		$form_username = mb_substr($_POST['l'], 0, 32, 'UTF-8');
		$form_password = mb_substr($_POST['p'], 0, 64, 'UTF-8');

		// part 2
		try {
			$db = new DB;
			$row = $db->getRow('SELECT id,password,email FROM users WHERE username=? LIMIT 1', $form_username);
			if (!$row) {
				$err = 1;
				$errMsg = 'Неверный логин или пароль';
				$statusType = 'error';
				break;
			}

			$user_id = $row['id'];
			$user_password_hash = $row['password'];
			$user_email = $row['email'];

			// check password
			require UP_ROOT.'include/PasswordHash.php';
			$t_hasher = new PasswordHash(8, FALSE);
			if (!$t_hasher->CheckPassword($form_password, $user_password_hash)) {
				$err = 1;
				$errMsg = 'Неверный логин или пароль';
				$statusType = 'error';
				break;
			}

		} catch (Exception $e) {
			// is async request
			if (isset($_GET['json'])) {
				$result = array('error'=> 1, 'message' => 'Внутренняя ошибка сервиса. Попробуйте позже.');
				exit(json_encode($result));
			} else {
				error($e->getMessage());
			}
		}


		// realy login
		User::login($form_username, $user_id, $user_email, true);
		if (isset($_POST['do'])) {
			header('Location: /');
			exit();
		}
	}
	while (0);
}



// is async request
if (isset($_GET['json'])) {
	$result = array('error'=> $err, 'message' => $errMsg, 'fields' => implode(' ', $errFields));
	exit(json_encode($result));
}


$out = <<<ZZZ
	<div id="status"><span type="$statusType">$errMsg</span></div>
	<h2>Вход на сайт</h2>
	<form method="POST" action="$form_action" name="login" accept-charset="utf-8">
		<input type="hidden" name="form_sent" value="1"/>
		<input type="hidden" name="csrf_token" value="$csrf"/>
		<div class="formRow">
			<label for="l" id="label_l" class="$loginLabelClass">Логин</label>
			<input type="text" id="l" name="l" tabindex="1" maxlength="32" minlength="4" required="1"/>
		</div>
		<div class="formRow">
			<label for="p" id="label_p" class="$passwordLabelClass">Пароль</label>
			<input type="password" id="p" name="p" tabindex="2" maxlength="64" minlength="8" required="1"/>
		</div>
		<div class="formRow buttons">
			<input type="submit" name="do" value="Войти" tabindex="3"/>
		</div>
	</form>
ZZZ;
require UP_ROOT.'header.php';
echo($out);

$onDOMReady = <<<ZZZ
	var form = $("form[name='login']");
	UP.formCheck.register(form);

	form.find("input[required],textarea[required]")
		.change(function () { UP.formCheck.register(form); })
		.keyup(function () { UP.formCheck.register(form); })

	$('#wrap')
		.stopTime('checkLoginFormTimer')
		.everyTime(500, 'checkLoginFormTimer', function () { UP.formCheck.register(form); });


	// form
	var options = {
		url:	'/login/?json',
		dataType: 'json',
		resetForm: false,
		cleanForm: false,

		beforeSubmit: function (formArray, jqForm) {
			UP.wait.start();
			$('#wrap').stopTime('checkLoginFormTimer');
			form.find("input[type='submit']").attr("disabled", "disabled");
			return true;
		},

		error: function () {
			UP.wait.stop();
			$('#wrap').everyTime(500, 'checkLoginFormTimer', function () { UP.formCheck.register(form); });
			UP.statusMsg.show('Невозможно авторизироваться. Попробуйте позже.', UP.env.msgError, false);
		},

		success: function (r) {
			UP.wait.stop();
			form.find("input[type='submit']").removeAttr("disabled");


			if (r) {
				form.find("label").each(function () {
					$(this).removeClass('bad').addClass('good');
				});


				if (r.error === 0) {
					form.clearForm().resetForm();
					$('#primary').fadeTo(350, 0.01, function() {
						document.location = '/';
					});
				} else {
					UP.statusMsg.show(r.message, UP.env.msgError, true);
					UP.formCheck.register(true, true);

					if (r.fields) {
						var fields = r.fields.split(' ');
						jQuery.each(fields, function() {
      						$("label#label_" + this).removeClass('good').addClass('bad');
    					});
					}

					$(".bad:first").focus();
					$('#wrap').everyTime(500, 'checkLoginFormTimer', function () { UP.formCheck.register(form); });
				}
			} else {
				UP.statusMsg.show('Невозможно авторизироваться. Попробуйте позже.', UP.env.msgError, false);
			}
		}
	};

	form.submit(function () {
		$(this).ajaxSubmit(options);
		return false;
	});

	UP.statusMsg.defferedClear();
	form.find("[required][value='']:first").focus();
ZZZ;


require UP_ROOT.'footer.php';
?>
