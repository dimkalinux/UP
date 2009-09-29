<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


if (isset ($_GET['thanks'])) {
	require UP_ROOT.'header.php';

	$out = <<<ZZZ
	<div id="status">&nbsp;</div>
	<h2>Спасибо</h2>
	<p>Ваше сообщение отправлено администратору сервиса.</p>
ZZZ;
	echo($out);
	require UP_ROOT.'footer.php';
	exit();
}

$user_email = '';
if (!$user['is_guest']) {
	$user_email = User::getUserEmail($user['id']);
}

$form_action = $base_url.'feedback';
$csrf = generate_form_token($form_action);
$form = <<<ZZZ
	<form method="post" action="$form_action" name="feedback" enctype="multipart/form-data" accept-charset="utf-8">
		<input type="hidden" name="form_sent" value="1" />
		<input type="hidden" name="csrf_token" value="$csrf" />
	<div class="formRow">
		<label for="feedbackText">Сообщение</label>
		<textarea name="feedbackText" rows="10" minLength="1" maxLength="2048" required="1" tabindex="1"></textarea>
	</div>
	<div class="formRow">
		<label for="feedbackUserEmail">Ваш&nbsp;e-mail, на&nbsp;который будет выслан ответ</label>
		<input type="text" name="feedbackUserEmail" maxLength="80" tabindex="2" value="$user_email"/>
		<div class="inputHelp">не&nbsp;обязательно</div>
	</div>
	<div class="formRow">
		<label for="feedbackUserFile">Можно добавить файл, например скриншот</label>
		<input type="hidden" name="MAX_FILE_SIZE" value="5242880"/>
		<input type="file" name="feedbackUserFile" tabindex="3"/>
		<div class="inputHelp">объём файла не&nbsp;должен превышать 5&nbsp;МБ.</div>
	</div>
	<div class="formRow buttons">
		<input type="submit" name="do" value="Отправить" tabindex="4"/>
	</div>
	</form>
ZZZ;

$wasError = 0;
$errMsg = "&nbsp;";


if (isset ($_POST['form_sent']) || isset($_GET['json'])) {
	do {
		// 1. check csrf
		if (!check_form_token($csrf)) {
			$wasError=1;
			$errMsg='Действие заблокировано системой безопасности.';
			break;
		}

		if (!isset($_POST['feedbackText']) ||
			(mb_strlen($_POST['feedbackText'], 'UTF-8') < 1)) {
			$wasError=1;
			$errMsg='Заполните все необходимые поля';
			break;
		}

		// get all inputs
		$ip = get_client_ip();
		$subject = '';
		$message = mb_substr($_POST['feedbackText'], 0, 2048, 'UTF-8');

		$email = '';
		if (isset($_POST['feedbackUserEmail'])) {
			$email = mb_substr($_POST['feedbackUserEmail'], 0, 80, 'UTF-8');
		}

		$uploadfilename = '';
		if (isset($_FILES['feedbackUserFile'])) {
			$up_file = $_FILES['feedbackUserFile'];
			// check for errors
			if ($up_file['error'] != 0 && !is_uploaded_file($up_file['tmp_name'])) {
				$wasError=1;
				$errMsg='Ошибка при загрузке файла';
				break;
			}

			$uploadfilename = a_generate_filename ($GLOBALS['feedback_upload_dir'], 10, $up_file['size']);
			$uploadfile = $GLOBALS['feedback_upload_dir'].'/'.$uploadfilename;

			if (!move_uploaded_file($up_file['tmp_name'], $uploadfile)) {
				$wasError=1;
				$errMsg='Ошибка при сохранении файла';
				break;
			}
		}

		// add to database
		$db = new DB;
		if (!$db->query("INSERT INTO feedback VALUES('', ?, NOW(), ?, ?, ?, '0')", $ip, $message, $email, $uploadfilename)) {
			$wasError=1;
			$errMsg='Ошибка на сервере. Попробуйте позже';
			break;
		}

 		$headers = "MIME-Version: 1.0\n" ;
        $headers .= "Content-Type: text/html; charset=\"utf-8\"\n";
		mail('dark@lluga.net', 'UP feedback', $message, $headers);

		if (isset($_GET['do'])) {
			header('Location: http://up.lluga.net/feedback?thanks');
		}
	}
	while (0);
}

// is async request
if (isset($_GET['json'])) {
	exit(json_encode(array('error'=> $wasError, 'message' => $errMsg)));
}



require UP_ROOT.'header.php';
$out = <<<ZZZ
	<div id="status">$errMsg</div>
	<h2>Обратная связь</h2>
	<p>Перед вами специальная штука.<br />
С&nbsp;её помощью можно задать вопрос администраторам, высказать свои мысли и&nbsp;предложения, выругаться матом или попросить денег в&nbsp;долг.
А&nbsp;если <nobr>какая-то</nobr> штуковина на&nbsp;сайте не&nbsp;работает&nbsp;&mdash; здесь можно рассказать об&nbsp;этом службе поддержки.<br/></p>
	$form
ZZZ;
echo ($out);

$onDOMReady = <<<ZZZ
	var form = $("form[name='feedback']");
	UP.formCheck.register(form);

	form.find("input[required],textarea[required]")
		.change(function () { UP.formCheck.register(form);})
		.keyup(function () { UP.formCheck.register(form); })

	$('#wrap')
		.stopTime('checkFeedbackFormTimer')
		.everyTime(500, 'checkFeedbackFormTimer', function () { UP.formCheck.register(form); });

	// form
	var options = {
		url:	'/feedback?json',
		dataType: 'json',
		resetForm: false,
		cleanForm: false,

		beforeSubmit: function (formArray, jqForm) {
			UP.wait.start();
			$('#wrap').stopTime('checkFeedbackFormTimer');
			form.find("input[type='submit']").attr("disabled", "disabled");
			return true;
		},

		error: function () {
			UP.wait.stop();
			$('#wrap').everyTime(500, 'checkFeedbackFormTimer', function () { UP.formCheck.register(form); });
			UP.statusMsg.show('Невозможно отправить сообщение. Попробуйте позже.', UP.env.msgError, true);
		},

		success: function (r) {
			UP.wait.stop();
			form.find("input[type='submit']").removeAttr("disabled");

			if (r) {
				if (parseInt(r.error, 10) === 0) {
					form.clearForm().resetForm();
					$('#primary').fadeOut(350, function() {
						$('#primary').html('<div id="status">&nbsp;</div><h2>Сообщение отправлено</h2>' +
								'<p>Спасибо, что потратили время для связи&nbsp;с&nbsp;нами. Мы&nbsp;ценим все ваши комментарии, касающиеся работы сервиса.</p>' +
								'<a href="/" class="oneLineLink">Перейти на&nbsp;главную страницу</a>');
							}).fadeIn(250);
				} else {
					UP.statusMsg.show(r.message, UP.env.msgError, true);
				}
			} else {
				UP.statusMsg.show('Невозможно отправить сообщение. Попробуйте позже.', UP.env.msgError, false);
			}
		}
	};

	form.submit(function () {
		$(this).ajaxSubmit(options);
		return false;
	});

	UP.statusMsg.defferedClear();
	$("[required='1'][value='']:first").focus();
ZZZ;

//
require UP_ROOT.'footer.php';
?>
