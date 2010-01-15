<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}

if (empty($page_title)) {
	$page_title = 'Пункт сбыта файлов';
}

$login_form_action = $base_url.'login/';
$csrf = generate_form_token($login_form_action);
$loginForm = <<<FMB
<div id="fancyLogin">
	<div id="fancyLoginForm">
		<div id="fancyLoginFormHeader">Вход на&nbsp;сайт</div>
		<form method="POST" action="$login_form_action" name="fancyLogin" accept-charset="utf-8">
			<input type="hidden" name="form_sent" value="1"/>
			<input type="hidden" name="csrf_token" value="$csrf"/>
			<div class="formRow">
				<label for="l" id="label_l">Логин</label>
				<input type="text" id="l" name="l" tabindex="100" maxlength="32" minlength="4" required="1"/>
			</div>
			<div class="formRow">
				<label for="p" id="label_p">Пароль</label>
				<input type="password" id="p" name="p" tabindex="101" maxlength="64" minlength="8" required="1"/>
			</div>
			<div class="formRow buttons">
				<input type="submit" name="do" value="Войти" class="default" tabindex="102"/>
				<input type="button" name="close" value="Закрыть" tabindex="103"/>
			</div>
		</form>
		<!--<div class="formRow buttons">
			<a href="{$base_url}forget_password/">Напомните&nbsp;мне&nbsp;пароль</a>
		</div>-->
	</div>
</div>
FMB;


$logDiv = 'Привет, Гость!&nbsp;&nbsp;Можно <span class="relative"><a href="'.$base_url.'login/" title="Войти в систему" id="mainMenuLogin" class="mainMenuLogin">войти</a>'.$loginForm.'</span> или <a href="'.$base_url.'register/" title="Зарегистрироваться на сервисе, бесплатно.">зарегистрироваться</a>';
$user_login = '';

if (defined('USE_OPENID') && USE_OPENID === TRUE) {
	$logDiv = 'Привет, Гость!&nbsp;&nbsp;Можно <span class="relative"><a href="'.$base_url.'login_openid.php" title="Войти в систему используя OpenID" id="mainMenuLoginOpenID" class="mainMenuLoginOpenID">войти в систему</a></span>';
}

try {
	if (!$user['is_guest']) {
		$user_login	= $user['login'];
		$logDiv = <<<FMB
	Вы зашли как&nbsp;&nbsp;<a href="{$base_url}profile/" title="Зайти к себе в профиль">$user_login</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{$base_url}files/my/" id="mainMenuMyFiles" title="Перейти к вашим файлам">Мои файлы</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{$base_url}logout/" title="Выйти из системы">Выйти</a></div>
FMB;
	}
} catch(Exception $e) {
	error($e->getMessage());
}

//
function print_menu() {
	global $user, $base_url;

	$menuArrow = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAAPCAMAAADeWG8gAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAlQTFRF2trawMDA////FCoqwwAAACdJREFUeNpiYMIADFiEGKivihECUFTBRZDMgokgm8VIC3dhCgEEGAAduAIBgj6YfQAAAABJRU5ErkJggg==';
	$cpage = $_SERVER['PHP_SELF'];

	$mainMenu = <<<FMB
		<li class="niceMenuFirst" style="padding-right: 6px;"><a href="{$base_url}" title="Вернуться на главную страницу">Главная</a></li>
FMB;

	$searchMenu = <<<FMB
		<li class="niceMenuLast" style="padding-left: 6px;"><a href="{$base_url}search/">Поиск</a></li>
FMB;

	$projectMenu = <<<FMB
	<li>
		<span class="head_menu">
			<a href="{$base_url}about/">О&nbsp;проекте</a>
			<img src="$menuArrow" width="18" height="15" class="arrow" />
		</span>
 		<div class="sub_menu">
	        <a href="{$base_url}rules/">История</a>
	        <a href="{$base_url}help/">Справка</a>
			<a href="{$base_url}stat/">Статистика</a>
			<a href="{$base_url}comments/">Комментарии</a>
			<a href="{$base_url}feedback/" class="item_line">Обратная связь</a>
			<a href="{$base_url}map/">Карта сайта</a>
			<a href="{$base_url}agreement/" class="item_line">Пользовательское соглашение</a>
        </div>
	</li>
FMB;


	$myFilesMenuEntry = '';
	if (!$user['is_guest']) {
		$myFilesMenuEntry = '<a href="/files/my/" class="item_line">Мои файлы</a>';
	}

	$filelistMenu = <<<FMB
	<li>
		<span class="head_menu">
			<a href="{$base_url}files/new/">Список файлов</a>
			<img src="$menuArrow" width="18" height="15" class="arrow" />
		</span>
 		<div class="sub_menu">
	       	<a href="{$base_url}files/new/">Cвежие</a>
			<a href="{$base_url}files/size/">Большие</a>
			<a href="{$base_url}files/popular/">Популярные</a>
			<a href="{$base_url}files/video/" class="item_line">Видео</a>
			<a href="{$base_url}files/mp3/">Музыка</a>
			<a href="{$base_url}files/archive/">Архивы</a>
			<a href="{$base_url}files/photo/">Картинки</a>
			<a href="{$base_url}files/image/">Образы дисков</a>
			<a href="{$base_url}on-air/" class="item_line">Прямой эфир</a>
			<a href="{$base_url}files/spam/" class="item_line">Спам</a>
			$myFilesMenuEntry
      	</div>
	</li>
FMB;

	$topMenu = <<<FMB
	<li>
		<span class="head_menu">
			<a href="{$base_url}top/">Топ файлов</a>
			<img src="$menuArrow" width="18" height="15" class="arrow" />
		</span>
 		<div class="sub_menu">
			<a href="{$base_url}top/video/">Видео</a>
			<a href="{$base_url}top/mp3/">Музыка</a>
			<a href="{$base_url}top/archive/">Архивы</a>
			<a href="{$base_url}top/photo/">Картинки</a>
			<a href="{$base_url}top/image/">Образы дисков</a>
      	</div>
	</li>
FMB;


	$serviceMenu = <<<FMB
	<li>
		<span class="head_menu">
			<a href="{$base_url}explore/">Сервисы</a>
			<img src="$menuArrow" width="18" height="15" class="arrow" />
		</span>
 		<div class="sub_menu">
			<a href="http://forum.lluga.net/">Форум</a>
			<a href="http://film.lg.ua/" class="item_line">Фильмы</a>
			<a href="https://hosting.iteam.lg.ua/">Хостинг</a>
			<a href="http://photo.lluga.net/">Фотопечать</a>
			<a href="http://torrents.iteam.net.ua/">Торрент-трекер</a>
			<a href="http://bf2.iteam.net.ua/">Игровой сервер</a>
			<a href="http://radio.iteam.net.ua/">Интернет радио</a>
			<a href="http://files.iteam.net.ua/">Файловый сервер</a>
			<a href="http://forum.iteam.net.ua/labs/" class="item_line">Labs</a>
		</div>
	</li>
FMB;

	$adminMenu = <<<FMB
	<li>
		<span class="head_menu">
			<a href="{$base_url}admin/">Управление</a>
			<img src="$menuArrow" width="18" height="15" class="arrow"/>
		</span>
 		<div class="sub_menu">
			<a href="{$base_url}admin/adult/">+16</a>
			<a href="{$base_url}spam/">Спам</a>
			<a href="{$base_url}admin/hidden/">Скрытые</a>
			<a href="{$base_url}admin/logs/" class="item_line">Журнал событий</a>
			<a href="{$base_url}admin/feedback/">Журнал сообщений</a>
			<a href="{$base_url}admin/storage/" class="item_line">Хранилища</a>
		</div>
	</li>
FMB;


	if (!$user['is_admin']) {
		$adminMenu = '';
	}

	$menu = <<<FMB
		<div id="nicemenu">
			<ul>
				$mainMenu
				$projectMenu
				$filelistMenu
				$topMenu
				$serviceMenu
				$adminMenu
				$searchMenu
			</ul>
		</div>
FMB;

	return $menu;
}

// Send no-cache headers
header('Expires: Thu, 21 Jul 1977 07:30:00 GMT');	// When yours truly first set eyes on this world! :)
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');		// For HTTP/1.0 compability
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $page_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="keywords" content="скачать, upload, загрузить, файлы, выложить, files, файлообменник, обменник"/>
	<meta name="robots" content="index, follow"/>
	<meta name="document-state" content="Dynamic"/>
	<meta name="resource-type" content="Document"/>
	<link rel="stylesheet" type="text/css" href="/style/style.css"/>
	<!--[if IE]><link rel="stylesheet" type="text/css" href="/style/ie_style.css" /><![endif]-->
	<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo $base_url; ?>rss/"/>
	<link rel="search" type="application/opensearchdescription+xml" title="up@lluga.net search" href="<?php echo $base_url; ?>misc/up_search.xml"/>
</head>
<?php flush(); ?>
<body>
	<div id="loginMenu"><?php echo $logDiv; ?></div>
	<div id="headerTop">&nbsp;</div>
	<div id="header">
		<h1><strong>ап</strong><em>, сервис обмена и&nbsp;хранения файлов</em></h1>
<?php echo print_menu(); ?>
	</div>
	<div id="wrap">
		<div id="primary">
<?php define('UP_HEADER', 1); //ob_start(); ?>
