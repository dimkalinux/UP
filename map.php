<?php
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'header.php';
?>
	<div id="status">&nbsp;</div>
	<h2>Карта сайта</h2>

	<table class="layout" id="mapTable">
	<tr valign="top" class="layout">
	<td class="layout">
		<div class="header"><a href="<?php echo $base_url; ?>">Главная страница</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>register/">Регистрация</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>login/">Вход в&nbsp;систему</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>profile/">Профиль</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>files/">Мои файлы</a></div>
		<br/>
		<div class="header"><a href="<?php echo $base_url; ?>search/">Поиск</a></div>
	</td>
	<td class="layout">
		<div class="header"><a href="<?php echo $base_url; ?>about/">О проекте</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>rules/">История проекта</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>help/">Справка</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>stat/">Статистика</a></div>
		<br/>
		<div class="item"><a href="<?php echo $base_url; ?>feedback/">Обратная связь</a></div>
		<br/>
		<div class="item"><a href="<?php echo $base_url; ?>agreement/">Пользовательское соглашение</a></div>
	</td>
	<td class="layout">
		<div class="header"><a href="<?php echo $base_url; ?>top/new/">Список файлов</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>top/new/">Свежие</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>top/popular/">Популярные</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>top/size/">Большие</a></div>
		<br/>
		<div class="item"><a href="<?php echo $base_url; ?>top/mp3/">Музыка</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>top/video/">Видео</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>top/foto/">Картинки</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>top/archive/">Архивы</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>top/image/">Образы дисков</a></div>
		<br/>
		<div class="item"><a href="<?php echo $base_url; ?>on-air/">Прямой эфир</a></div>
		<div class="item"><a href="<?php echo $base_url; ?>spam/">Спам</a></div>
	</td>

</td>
</tr>
</table>

<?
require UP_ROOT.'footer.php';
?>
