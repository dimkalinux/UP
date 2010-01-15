<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'include/openid.inc.php';

$errMsg = "&nbsp;";
$statusType = 'default';

if (isset($_GET['status'])) {
    switch (intval($_GET['status'], 10)) {
	case OPENID_LOGIN_CANCELED:
	    $errMsg = 'Вход не выполнен OPENID_LOGIN_CANCELED';
	    $statusType = 'warning';
	    break;
	case OPENID_LOGIN_INVALID_ID:
	    $errMsg = 'Вход не выполнен OPENID_LOGIN_INVALID_ID';
	    $statusType = 'error';
	    break;
	case OPENID_LOGIN_ID_NOT_EXISTS:
	    $errMsg = 'Вход не выполнен OPENID_LOGIN_ID_NOT_EXISTS';
	    $statusType = 'error';
	    break;
	case OPENID_LOGIN_FAILED:
	    $errMsg = 'Неверный логин или пароль';
	    $statusType = 'error';
	    break;
	case OPENID_LOGIN_ERROR_REDIRECT:
	    $errMsg = 'Невозможно перейти к серверу авторизации';
	    $statusType = 'error';
	    break;

	default:
	    break;
    }
}


$out = <<<FMB
    <div id="status"><span type="$statusType">$errMsg</span></div>
    <h2>Вход на сайт</h2>

    <p>Сервис АП использует для аутентификации систему <a href="http://openid.com/">OpenID</a>.<br/>
    Вы можете войти на сервис, используя вашу учётную запись на нашем форуме или на другом сервисе.</p>

    <form action="{$base_url}openid_try_auth.php" method="get" id="openid_form">
	<input type="hidden" name="action" value="verify" />

	<fieldset>
	    <div id="openid_choice">

		<p><a href="http://up.lluga.net/openid_try_auth.php?action=verify&amp;openid_identifier=http://forum.lluga.net/forum/">Войти, используя Форум</a> или выберите вашего OpenID-провайдера:</p>
		<div id="openid_btns"></div>
	    </div>

	    <div id="openid_input_area" class="formRow">
		<input id="openid_identifier" name="openid_identifier" type="text" value="http://" />
		<input id="openid_submit" type="submit" value="Войти"/>
	    </div>
	    <noscript>
		<p>OpenID is service that allows you to log-on to many different websites using a single indentity.
		Find out <a href="http://openid.net/what/">more about OpenID</a> and <a href="http://openid.net/get/">how to get an OpenID enabled account</a>.</p>
	    </noscript>
	</fieldset>
</form>
FMB;


$addScript[] = 'openid-jquery.js';
$onDOMReady = 'openid.init("openid_identifier"); UP.statusMsg.defferedClear();';

printPage($out);

?>
