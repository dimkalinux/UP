<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';



$form_action = $base_url.'register/';
$csrf = generate_form_token($form_action);
$err = 0;
$errMsg = "&nbsp;";
$statusType = 'default';
$loginLabelClass = $passwordLabelClass = $agreementLabelClass = $emailLabelClass = 'good';
$errFields = array();

// check for cancel
if (isset($_POST['cancel'])) {
	header('Location: /');
	exit();
}


if (isset ($_GET['first'])) {
	require UP_ROOT.'header.php';

	$out = <<<ZZZ
	<div id="status">&nbsp;</div>
	<h2>Спасибо</h2>
	<p>Вы зарегистрированны.</p>
ZZZ;
	echo($out);
	require UP_ROOT.'footer.php';
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

		$username = isset($_POST['l']) ? $_POST['l'] : '';
		$password = isset($_POST['p']) ? $_POST['p'] : '';
		$email = isset($_POST['e']) ? mb_strtolower(trim($_POST['e'])) : '';

		// check login
		if ((mb_strlen($username) < 4) || (mb_strlen($username) > 32)) {
			$err = 1;
			$loginLabelClass = 'bad';
			$errFields[] = 'l';
		}

		// check password
		if ((mb_strlen($password) < 8) || (mb_strlen($password) > 64)) {
			$err = 1;
			$passwordLabelClass = 'bad';
			$errFields[] = 'p';
		}

		// check email
		if ((mb_strlen($email) < 3) || (mb_strlen($email) > 128) || !is_valid_email($email)) {
			$err = 1;
			$emailLabelClass = 'bad';
			$errFields[] = 'e';
		}

		// check agreement
		if (!isset($_POST['a'])) {
			$err = 1;
			$agreementLabelClass = 'bad';
			$errFields[] = 'a';
		}

		if ($err !== 0) {
			$errMsg = 'Исправьте ошибки в полях, выделенных красным цветом';
			$statusType = 'error';
			break;
		}


		// part 2
		// check if login already exists
		try {
			$db = new DB;
			$result = $db->numRows('SELECT id FROM users WHERE username=? LIMIT 1', $username);
			if ($result !== 0) {
				$err = 1;
				$errMsg = 'Такой логин уже зарегистрирован. Придумайте другой.';
				$statusType = 'error';
				break;
			}

			require UP_ROOT.'include/PasswordHash.php';
			$t_hasher = new PasswordHash(8, FALSE);
			$cryptPassword = $t_hasher->HashPassword($password);

			$db->query("INSERT INTO users VALUES('', ?, ?, ?, NOW(), 0, 0, 0)", $username, $cryptPassword, $email);
		} catch (Exception $e) {
			// is async request
			if (isset($_GET['json'])) {
				exit(json_encode(array('error'=> 1, 'message' => 'Внутренняя ошибка сервиса. Попробуйте позже.')));
			} else {
				error($e->getMessage());
			}
		}

		if (isset($_POST['do'])) {
			header('Location: /');
			exit();
		}
	}
	while (0);
}


// is async request
if (isset($_GET['json'])) {
	exit(json_encode(array('error'=> $err, 'message' => $errMsg, 'fields' => implode(' ', $errFields))));
}



$regForm = <<<ZZZ
<form method="POST" action="$form_action" name="register" accept-charset="utf-8" autocomplete="off">
<input type="hidden" name="form_sent" value="1"/>
<input type="hidden" name="csrf_token" value="$csrf"/>
<table class="form">
<tr>
	<td>
		<div class="formRow">
			<label for="l" id="label_l" class="$loginLabelClass">Логин</label>
			<input type="text" id="l" name="l" tabindex="1" maxlength="32" minlength="4" required="1"/>
			<div class="inputHelp">от 4 до 32 символов</div>
		</div>
	</td>
	<td>
		<div class="formRow">
			<label for="p" id="label_p" class="$passwordLabelClass">Пароль</label>
			<input type="text" id="p" name="p" tabindex="2" maxlength="64" minlength="8" required="1"/>
			<div class="inputHelp">от 8 до 64 символов</div>
		</div>
	</td>
</tr>
<tr>
	<td colspan="2">
		<div class="formRow">
			<label for="e" id="label_e" class="$emailLabelClass">Электропочта</label>
			<input type="text" id="e" name="e" tabindex="3" maxlength="128" minlength="4" required="1" pattern="\w{1,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,3}$"/>
		</div>
	</td>
</tr>
<tr>
	<td colspan="2">
		<div class="formRow buttons">
			<label for="a" id="label_a" class="$agreementLabelClass">
			<input type="checkbox" id="a" name="a" tabindex="4" required="1"/>
			Я&nbsp;принимаю условия <a href="/agreement.php" target="_blank">соглашения</a><br/> <em>(Откроется в&nbsp;новом окне)</em></label>
		</div>
	</td>
</tr>
</table>
<div class="formRow buttons">
	<input type="submit" name="do" value="Зарегистрироваться" tabindex="5"/>
</div>
</form>
ZZZ;



require UP_ROOT.'header.php';
$out = <<<ZZZ
	<div id="status"><span type="$statusType">$errMsg</span></div>
	<h2>Регистрация</h2>
	<p class="pageDescription">Эти данные мы разместим на всех сайтах знакомств и продадим спамерам.</p>
	$regForm
ZZZ;
echo($out);


$onDOMReady = <<<ZZZ
	var form = $("form[name='register']");
	UP.formCheck.register(form);

	form.find("input[required],textarea[required]")
		.change(function () { UP.formCheck.register(form); })
		.keyup(function () { UP.formCheck.register(form); })

	$('#wrap')
		.stopTime('checkRegisterFormTimer')
		.everyTime(500, 'checkRegisterFormTimer', function () { UP.formCheck.register(form); });

	// form
	var options = {
		url:	'/register/',
		dataType: 'json',
		resetForm: false,
		cleanForm: false,
		data: { json: 1 },

		beforeSubmit: function (formArray, jqForm) {
			UP.wait.start();
			$('#wrap').stopTime('checkRegisterFormTimer');
			form.find("input[type='submit']").attr("disabled", "disabled");
			return true;
		},

		error: function () {
			UP.wait.stop();
			$('#wrap').everyTime(500, 'checkRegisterFormTimer', function () { UP.formCheck.register(form); });
			UP.statusMsg.show('Невозможно зарегистрироваться. Попробуйте позже.', UP.env.msgError, false);
		},

		success: function (r) {
			UP.wait.stop();
			form.find("input[type='submit']").removeAttr("disabled");

			if (r) {
				form.find("label").each(function () {
					$(this).removeClass('bad').addClass('good');
				});

				if (parseInt(r.error, 10) === 0) {
					form.clearForm().resetForm();
					$('#primary').fadeOut(350, function() {
						$('#primary').html('<div id="status">&nbsp;</div><h2>Поздравляем, регистрация завершена</h2>' +
								'<p>Спасибо, что потратили время на&nbsp;регистрацию.</p>' +
								'<a href="/" class="oneLineLink">Перейти на&nbsp;главную страницу</a>');
							}).fadeIn(250);
				} else {
					UP.statusMsg.show(r.message, UP.env.msgError, true);

					if (r.fields) {
						var fields = r.fields.split(' ');
						jQuery.each(fields, function() {
      						$("label#label_" + this).removeClass('good').addClass('bad');
    					});
					}

					$(".bad:first").focus();
					$('#wrap').everyTime(500, 'checkRegisterFormTimer', function () { UP.formCheck.register(form); });
				}
			} else {
				UP.statusMsg.show('Невозможно зарегистрироваться. Попробуйте позже.', UP.env.msgError, false);
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
