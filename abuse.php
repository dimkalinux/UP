<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';


//
if (isset ($_GET['thanks'])) {
	$out = <<<FMB
	<div id="status">&nbsp;</div>
	<h2>Спасибо</h2>
	<p>Ваше жалоба отправлена администрации сервиса.</p>
	<p><a href="/">Перейти на главную страницу</a></p>
	</p>
FMB;
	// PRINT PAGE and EXIT
	printPage($out);
	exit();
}


$form_action_abuse = $base_url.'abuse.php';
$csrf = generate_form_token($form_action_abuse);

$wasError = 0;
$errMsg = "&nbsp;";


if (isset ($_POST['form_sent']) || isset($_POST['json'])) {
	do {
		// 1. check csrf
		if (!check_form_token($csrf)) {
			$wasError = 1;
			$errMsg = 'Действие заблокировано системой безопасности';
			break;
		}

		// get all inputs
		$ip = get_client_ip();
		$item_id = isset($_POST['item_id']) ? intval($_POST['item_id'], 10) : 0;
		$abuseType = isset($_POST['abuseType']) ? intval($_POST['abuseType'], 10) : 0;
		$abuseMessage = isset($_POST['abuseMessage']) ? mb_substr($_POST['abuseMessage'], 0, 2048) : '';
		$abuseWeight = User::get_abuse_weight($user);

		// add to database
		try {
			$db = DB::singleton();

			// CHECK LAST ABUSE
			if (!User::can_abuse_this_file($item_id, $user)) {
				$wasError = 1;
				$errMsg = 'С вашего IP-адреса уже была жалоба на этот файл';
				break;
			}

			$db->query("INSERT INTO abuse (item_id,user_id,ip,date,abuse_type,message,weight) VALUES(?, ?, ?, NOW(), ?, ?, ?)", $item_id, $user['id'], $ip, $abuseType, $abuseMessage, $abuseWeight);

			$row = $db->getRow('SELECT COALESCE(SUM(weight), 0) AS sw FROM abuse WHERE item_id=?', $item_id);
			$spamWeight = intval($row['sw'], 10);

			// HIDE ABUSED file
			if ($spamWeight > 9) {
				$db->query("UPDATE up SET hidden=1 WHERE id=? LIMIT 1", $item_id);
			}
		} catch (Exception $e) {
			$errMsg = 'Ошибка на сервере. Попробуйте позже';

			if (isset($_POST['json'])) {
				exit(json_encode(array('error'=> 1, 'message' => $errMsg)));
			} else {
				error($e->getMessage());
			}
		}

		// if we here — no errors
		$wasError = 0;

		if (isset($_POST['do'])) {
			header("Location: {$base_url}abuse.php?thanks");
		}
	}
	while (0);
} else {
	$item_id = isset($_GET['item_id']) ? intval($_GET['item_id'], 10) : 0;
	if ($item_id < 1) {
		show_error_message('Не указан  идентификатор файла');
	}

}

// is async request
if (isset($_POST['json'])) {
	exit(json_encode(array('error'=> $wasError, 'message' => $errMsg)));
}


$abuseTypes = array(
	'1'		=> 'Файл является порнографией',
	'2'		=> 'Файл является спамом',
	'3'		=> 'Файл нарушает авторские права',
	'4'		=> 'Другая причина, укажу её ниже',
);

$abuseTypesAsOptions = '';
foreach ($abuseTypes as $type => $typeName) {
	$abuseTypesAsOptions .= '<option value="'.$type.'">'.$typeName.'</option>';
}

$form = <<<FMB
	<form method="post" action="$form_action_abuse" name="abuse" accept-charset="utf-8">
		<input type="hidden" name="form_sent" value="1"/>
		<input type="hidden" name="csrf_token" value="$csrf"/>
		<input type="hidden" name="item_id" value="$item_id"/>
		<div class="formRow">
			<label for="abuseType">Причина жалобы</label>
			<select name="abuseType" id="abuseType" tabindex="1">$abuseTypesAsOptions</select>
		</div>
		<div class="formRow">
			<label for="abuseMessage">Подробности</label>
			<textarea id="abuseMessage" name="abuseMessage" rows="4" minLength="0" maxLength="2048" required="0" tabindex="2"></textarea>
			<div class="inputHelp">не&nbsp;обязательно</div>
		</div>
		<div class="formRow buttons">
			<input type="submit" name="do" value="Отправить" tabindex="3"/>
		</div>
	</form>
FMB;


$errMsgType = 'none';
if ($wasError == 1) {
	$errMsgType = 'error';
}


$out = <<<FMB
	<div id="status"><span type="$errMsgType">$errMsg</span></div>
	<h2>Abuse</h2>
	<p class="pageDescription">Перед вами специальная штука.<br /></p>
	$form
FMB;

$onDOMReady = <<<ZZZ
	var form = $("form[name='abuse']");
	UP.formCheck.register(form);

	$(form).find("input[required],textarea[required]")
		.change(function () { UP.formCheck.register(form);})
		.keyup(function () { UP.formCheck.register(form); })

	$('#wrap')
		.stopTime('checkAbuseFormTimer')
		.everyTime(500, 'checkAbuseFormTimer', function () { UP.formCheck.register(form); });

	// form
	var options = {
		url:	'$form_action_abuse',
		dataType: 'json',
		resetForm: false,
		cleanForm: false,
		data: { json: 1 },
		beforeSubmit: function (formArray, jqForm) {
			UP.wait.start();
			$('#wrap').stopTime('checkFeedbackFormTimer');
			$(form).find("input[type='submit']").attr("disabled", "disabled");
			return true;
		},

		error: function () {
			UP.wait.stop();
			$('#wrap').everyTime(500, 'checkAbuseFormTimer', function () { UP.formCheck.register(form); });
			UP.statusMsg.show('Невозможно отправить жалобу. Попробуйте позже.', UP.env.msgError, true);
		},

		success: function (r) {
			UP.wait.stop();
			$(form).find("input[type='submit']").removeAttr("disabled");

			if (r) {
				if (parseInt(r.error, 10) === 0) {
					$(form).clearForm().resetForm();
					$('#primary').fadeOut(350, function() {
						$('#primary').html('<div id="status">&nbsp;</div><h2>Жалоба добавлена</h2>' +
							'<p>Ваше жалоба отправлена администрации сервиса.</p>' +
							'<a href="/" class="oneLineLink">Перейти на&nbsp;главную страницу</a>');
							}).fadeIn(250);
				} else {
					UP.statusMsg.show(r.message, UP.env.msgError, true);
				}
			} else {
				UP.statusMsg.show('Невозможно отправить жалобу. Попробуйте позже.', UP.env.msgError, false);
			}
		}
	};

	$(form).submit(function () {
		$(this).ajaxSubmit(options);
		return false;
	});

	$(form).bind("reset", function () {
		$(document).oneTime(100, 'z', function () { $(form).find("[required='1'][value='']:first").focus(); });
	});

	UP.statusMsg.defferedClear();
	$(form).find("[required='1'][value='']:first").focus();
ZZZ;

printPage($out);
exit();



?>
