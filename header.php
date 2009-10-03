<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}

if (empty($page_title)) {
	$page_title = 'Пункт сбыта файлов';
}

$form_action = $base_url.'login/';
$csrf = generate_form_token($form_action);
$loginForm = <<<FMB
<div id="fancyLogin">
	<div id="fancyLoginForm">
		<div id="fancyLoginFormHeader">Вход на&nbsp;сайт</div>
		<form method="POST" action="$form_action" name="fancyLogin" accept-charset="utf-8">
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
				<input type="submit" name="do" value="Войти" tabindex="102"/>
				<input type="button" name="close" value="Закрыть" tabindex="103"/>
			</div>
		</form>
		<div class="formRow buttons">
			<a href="/forget_password.php">Напомните&nbsp;мне&nbsp;пароль</a>
		</div>
	</div>
</div>

FMB;


$logDiv = getWelcomeMessage().'&nbsp;&nbsp;Можно <span class="relative"><a href="/login/" title="Войти в систему" id="mainMenuLogin" class="mainMenuLogin">войти</a>'.$loginForm.'</span> или <a href="/register/" title="Зарегистрироваться на сервисе, бесплатно.">зарегистрироваться</a>';
$user_login = '';

try {
	if (!$user['is_guest']) {
		$user_login	= $user['login'];
		$logDiv = <<<FMB
	Вы зашли как&nbsp;&nbsp;<a href="/profile/" title="Зайти к себе в профиль">$user_login</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/files/" id="mainMenuMyFiles" title="Перейти к вашим файлам">Мои файлы</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="/logout/" title="Выйти из системы">Выйти</a></div>
FMB;
	}
} catch(Exception $e) {
	error($e->getMessage());
}

//
function print_menu() {
	global $user;

	$menuArrow = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAAPCAMAAADeWG8gAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAlQTFRF2trawMDA////FCoqwwAAACdJREFUeNpiYMIADFiEGKivihECUFTBRZDMgokgm8VIC3dhCgEEGAAduAIBgj6YfQAAAABJRU5ErkJggg==';
	$cpage = $_SERVER['PHP_SELF'];
	$class = "z";


	$cpage == '/index.php' ? $class = "current" : $class = "z";
	$main_url = <<<ZZZ
		<li class="$class niceMenuFirst" style="padding-right: 6px;"><a href="/" title="Вернуться на главную страницу">Главная</a></li>
ZZZ;

	$cpage == '/search/' ? $class = "current" : $class = "z";
	$search_url = <<<ZZZ
		<li class="$class niceMenuLast" style="padding-left: 6px;"><a href="/search/">Поиск</a></li>
ZZZ;

	stripos($cpage, 'about') !== false ? $class = "current" : $class = "z";
	$project_url = <<<ZZZ
	<li class="$class">
		<span class="head_menu">
			<a href="/about/">О&nbsp;проекте</a>
			<img src="$menuArrow" width="18" height="15" class="arrow" />
		</span>
 		<div class="sub_menu">
	        <a href="/rules/">История</a>
			<a href="/stat/">Статистика</a>
			<a href="/compability.php">Совместимость</a>
			<a href="/feedback/" class="item_line">Обратная связь</a>
			<a href="/map/">Карта сайта</a>
			<a href="/agree/" class="item_line">Пользовательское соглашение</a>
			<a href="/service/uploaders.php">Дополнительные программы</a>
			<a href="/ftp_access.php">Доступ по фтп</a>
        </div>
	</li>
ZZZ;


	$myFilesMenuEntry = '';
	if (!$user['is_guest']) {
		$myFilesMenuEntry = '<a href="/files/" class="item_line">Мои файлы</a>';
	}
	stripos($cpage, '/top/') !== false ? $class = "current" : $class = "z";
	$filelist_url = <<<ZZZ
	<li class="$class">
		<span class="head_menu">
			<a href="/top/new/">Список файлов</a>
			<img src="$menuArrow" width="18" height="15" class="arrow" />
		</span>
 		<div class="sub_menu">
	       	<a href="/top/new/">Cвежие</a>
			<a href="/top/size/">Большие</a>
			<a href="/top/popular/">Популярные</a>
			<a href="/top/mp3/" class="item_line">Музыка</a>
			<a href="/top/video/">Видео</a>
			<a href="/top/archive/">Архивы</a>
			<a href="/top/photo/">Картинки</a>
			<a href="/top/image/">Образы дисков</a>
			<a href="/on-air/" class="item_line">Прямой эфир</a>
			<a href="/spam/" class="item_line">Спам</a>
			$myFilesMenuEntry
      	</div>
	</li>
ZZZ;


	$cpage == '/explore.php' ? $class = "current" : $class = "z";
	$service_url = <<<ZZZ
	<li class="$class">
		<span class="head_menu">
			<a href="/explore/">Сервисы</a>
			<img src="$menuArrow" width="18" height="15" class="arrow" />
		</span>
 		<div class="sub_menu">
			<a href="http://forum.lluga.net/">Форум</a>
			<a href="http://film.lg.ua/" class="item_line">Фильмы</a>
			<a href="https://hosting.iteam.lg.ua/">Хостинг</a>
			<a href="http://photo.lluga.net/">Фотопечать</a>
			<a href="http://radio.lluga.net:8000/">Интернет радио</a>
			<a href="http://files.iteam.net.ua/">Файловый сервер</a>
			<a href="http://bf2.iteam.net.ua/">Игровой сервер</a>
			<a href="http://forum.iteam.net.ua/labs/" class="item_line">Labs</a>
		</div>
	</li>
ZZZ;

	$adminMenu = <<<FMB
	<li>
		<span class="head_menu">
			<a href="/admin/">Управление</a>
			<img src="$menuArrow" width="18" height="15" class="arrow"/>
		</span>
 		<div class="sub_menu">
			<a href="/adult.php" class="item_admin">adult</a>
			<a href="/hidden.php" class="item_admin">скрытые</a>
			<a href="/admin/logs.php" class="item_admin item_line">Журнал событий</a>
			<a href="/admin/storage.php" class="item_admin">Хранилища</a>
			<a href="/admin/feedback.php" class="item_admin">Сообщения</a>
		</div>
	</li>
FMB;


	if (!$user['is_admin']) {
		$adminMenu = '';
	}

	$menu = <<<ZZZ
		<div id="nicemenu">
			<ul>
				$main_url
				$project_url
				$filelist_url
				$service_url
				$adminMenu
				$search_url
			</ul>
		</div>
ZZZ;

	return $menu;
}
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
