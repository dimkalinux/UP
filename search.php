<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';
require UP_ROOT.'header.php';

define('UP_SHOW_ADS', 1);

$examples_a = array("firefox", "winamp", ".mp3", ".avi", "linux", "любовь", "ubuntu", "solaris", "rt*.mp3");
$ex = $examples_a[array_rand($examples_a)];

$search_form = <<<ZZZ
	<form method="get" action="/search.php" name="search" id="search_form">
		<input type="search" id="search_req2" name="s" size="50" minLength="3" maxlength="80" placeholder="поиск" results="15" required="1" />
		<input type="submit" name="doSubmit" value="Найти" disabled="disabled"/>
	</form>
	<div class="" style="margin: .5em 0 1em; font-size: .94em; color: #666;">
		<!--Например:&nbsp;
		<span class="as_js_link"
			id="search_example"
			onmousedown="
				$('#search_req2').val($('#search_example').text());
				$('input[type=submit]').removeAttr('disabled');">$ex</span><br/>-->
				Слова, в которых меньше трех букв, мы чаще всего игнорируем
	</div>
ZZZ;

$wasErrror = 0;
$errMsg = "&nbsp;";
$searchResults = "";

if (isset ($_GET['doSubmit'])) {
	do {
		if (!isset($_GET['s']) || (mb_strlen ($_GET['s']) < 3)) {
			$wasError=1;
			$errMsg='Для начала поиска введите как минимум 3 символа.';
			break;
		}

		//$req = urldecode($_GET['s']);
		$req = $_GET['s'];
		$fooltext = isset($_GET['ft']);
		if (!$searchResults = makeSearch($req, $fooltext)) {
			$errMsg = 'Файлов с таким именем не найдено';
		}
	}
	while (0);
}

$out = <<<ZZZ
	<div id="status">$errMsg</div>
	<h2>Тот самый поиск</h2>
	$search_form
	<div id="result">$searchResults</div>
ZZZ;
echo ($out);

//
$onDOMReady = <<<ZZZ
	var form = $("form[name='search']");
	UP.formCheck.register(form);

	form.find("input[required],textarea[required]")
		.change(function () { UP.formCheck.register(form); })
		.keyup(function () { UP.formCheck.register(form); })

	$('#wrap')
		.stopTime('checkSearchFormTimer')
		.everyTime(500, 'checkSearchFormTimer', function () { UP.formCheck.register(form); });



	// autocomplete
	$('#search_req2').autocomplete('/search_autcomplete.php', {minChars: 3, cacheLength: 100, delay: 300, scroll: false, autoFill: false, selectFirst: false});
	$('#search_req2').result(function(event, data, formatted) {
		if (data) {
 			form.submit();
		}
	});


	$('#result').data('needAnimate', 0);

	// form
	var options = {
		type: 	'GET',
		url: 	UP.env.ajaxBackend,
		dataType: 'json',
		data: 	{ t_action: UP.env.actionSearch },
		resetForm: false,
		cleanForm: false,

		beforeSubmit: function (formArray, jqForm) {
			UP.wait.start();
			form.find("input[type='submit']").attr("disabled", "disabled");
			$('.ac_results').hide();
			$('#wrap').stopTime('checkSearchFormTimer');
			return true;
		},

		error: function () {
			UP.wait.stop();
			$('.ac_results').hide();
			$('#wrap').everyTime(500, 'checkSearchFormTimer', function () { UP.formCheck.register(form); });
			UP.statusMsg.show('Внутреняя ошибка. Попробуйте позже.', UP.env.msgError, false);
		},

		success: function (r) {
			UP.wait.stop();
			$('.ac_results').hide();
			form.find("input[type='submit']").removeAttr("disabled");


			if (r.result === 1) {
				if (r.message) {
					UP.statusMsg.clear();
					var fadeOutSpeed = $('#result').data('needAnimate') === 1 ? 250 : 0;

					$('#result').fadeOut(fadeOutSpeed, function() {
						$('#result').html(r.message).data('needAnimate', 1);
					}).fadeIn(250);
				} else {
					$('#result').fadeOut(250).html();
					UP.statusMsg.show('Файлов с таким именем не найдено', UP.env.msgInfo, true);
				}
			} else {
				UP.statusMsg.show(r.message, UP.env.msgError, true);
			}

			$('#wrap').everyTime(500, 'checkSearchFormTimer', function () { UP.formCheck.register(form); });
			form.find("input[required]:first").focus();
			window.location.hash=$('#search_req2').val();
		}
	};

	form.submit(function () {
		$(this).ajaxSubmit(options);
		return false;
	});

	UP.statusMsg.defferedClear();
	form.find("[required]:first").focus();

	if (window.location.hash && window.location.hash.length > 1 && window.location.hash.charAt(0) == '#') {
		$('#search_req2').val(window.location.hash.substring(1));
		form.trigger('submit');
	}
ZZZ;

$addScript[] = 'jquery.autocomplete.js';
require UP_ROOT.'footer.php';
?>
