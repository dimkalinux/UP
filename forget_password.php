<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';

$form_action = $base_url.'profile/change_password/';
$csrf = generate_form_token($form_action);
$err = 0;
$errMsg = "&nbsp;";
$statusType = 'default';
$oldPasswordLabelClass = $newPasswordLabelClass = 'good';
$errFields = array();

// check for cancel
if (isset($_POST['cancel'])) {
	header('Location: /');
	exit();
}


if (isset ($_GET['first'])) {
	$out = <<<ZZZ
	<div id="status">&nbsp;</div>
	<h2>Поздравляем</h2>
	<p>Мы выслали новый пароль на ваш электронный адрес.</p>
ZZZ;

	printPage($out);
}



if (isset($_POST['form_sent'])) {
	do {
		// 1. check csrf
		if (!check_form_token($csrf)) {
			$err = 1;
			$errMsg='действие заблокировано системой безопасности: ';
			$statusType = 'error';
			break;
		}


		if ($user['is_guest'] == 1) {
			$err = 1;
			$errMsg='вы не авторизированы';
			$statusType = 'error';
			break;
		}

		// check new password
		if (!isset($_POST['np']) || (mb_strlen($_POST['np']) < 8) || (mb_strlen($_POST['np']) > 64)) {
			$err = 1;
			$newPasswordLabelClass = 'bad';
			$errFields[] = 'np';
		}


		if ($err !== 0) {
			$errMsg = 'Исправьте ошибки в полях, выделенных красным цветом';
			$statusType = 'error';
			break;
		}

		$email = mb_substr($_POST['np'], 0, 64);


		// part 2
		try {
			$db = DB::singleton();
			$row = $db->getRow('SELECT password FROM users WHERE id=? LIMIT 1', $user['id']);

			$t_hasher = new PasswordHash(8, FALSE);
			if (!$t_hasher->CheckPassword($old_password, $row['password'])) {
				$err = 1;
				$errMsg = 'Неверный текущий пароль ';
				$statusType = 'error';
				break;
			}

			$db->query('UPDATE users SET password=? WHERE id=? LIMIT 1', $new_cryptPassword, $user['id']);
			if ($db->affected() !== 1) {
				$err = 1;
				$errMsg = 'Внутреняя ошибка при смене пароля';
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



if (!$user['is_guest']) {
	$out = <<<FMB
	<div id="status"><span type="$statusType">$errMsg</span></div>
	<h2>Напомните мне пароль</h2>
	<p>Для смены пароля необходимо <a href="/login/" class="mainMenuLogin">войти в систему</a>.</p>
FMB;
} else {
	$out = <<<ZZZ
	<div id="status"><span type="$statusType">$errMsg</span></div>
	<h2>Напомните мне пароль</h2>
	<p class="pageDescription">Восстановить забытый пароль невозможно,
	потому что он хранится в зашифрованном виде.
	Но его можно поменять. Укажите адрес своей электронной почты,
	с которым вы регистрировались на АПе ранее, чтобы получить новый пароль.</p>
	<form method="POST" action="$form_action" name="forget_password" accept-charset="utf-8" autocomplete="on">
		<input type="hidden" name="form_sent" value="1"/>
		<input type="hidden" name="csrf_token" value="$csrf"/>
		<div class="formRow">
			<label for="np" id="label_np" class="$newPasswordLabelClass">Электропочта</label>
			<input type="text" id="np" name="np" tabindex="1" maxlength="128" minlength="4" required="1" pattern="\w{1,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,3}$"/>
		</div>
		<div class="formRow buttons">
			<input type="submit" name="do" value="Напомнить" tabindex="2"/>
		</div>
	</form>
ZZZ;
}

$onDOMReady = <<<ZZZ
	var form = $("form[name='forget_password']");
	UP.formCheck.register(form);

	form.find("input[required],textarea[required]")
		.change(function () { UP.formCheck.register(form); })
		.keyup(function () { UP.formCheck.register(form); })

	$('#wrap')
		.stopTime('checkChangePasswordFormTimer')
		.everyTime(500, 'checkChangePasswordFormTimer', function () { UP.formCheck.register(form); });

	// form
	var options = {
		url:	'/profile/change_password/?json',
		dataType: 'json',
		resetForm: false,
		cleanForm: false,

		beforeSubmit: function (formArray, jqForm) {
			UP.wait.start();
			$('#wrap').stopTime('checkChangePasswordFormTimer');
			form.find("input[type='submit']").attr("disabled", "disabled");
			return true;
		},

		error: function () {
			UP.wait.stop();
			$('#wrap').everyTime(500, 'checkChangePasswordFormTimer', function () { UP.formCheck.register(form); });
			UP.statusMsg.show('Невозможно сменить пароль. Попробуйте позже.', UP.env.msgError, false);
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
					$('#primary').fadeOut(350, function() {
						$('#primary').html('<div id="status">&nbsp;</div><h2>Поздравляем</h2><p>Вы успешно изменили пароль.</p>' +
								'<div style="margin-top: .6em;">' +
								'<a href="/">Перейти на главную страницу</a></div>');
							}).fadeIn(250);
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
					$('#wrap').everyTime(500, 'checkChangePasswordFormTimer', function () { UP.formCheck.register(form); });
				}
			} else {
				UP.statusMsg.show('Невозможно сменить пароль. Попробуйте позже.', UP.env.msgError, false);
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

printPage($out);

?>
