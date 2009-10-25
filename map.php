<?
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
		<div class="header"><a href="/">Главная страница</a></div>
		<div class="item"><a href="/register/">Регистрация</a></div>
		<div class="item"><a href="/login/">Вход в&nbsp;систему</a></div>
		<div class="item"><a href="/profile/">Профиль</a></div>
		<div class="item"><a href="/files/">Мои файлы</a></div>
		<br/>
		<div class="header"><a href="/search/">Поиск</a></div>
	</td>
	<td class="layout">
		<div class="header"><a href="/about/">О проекте</a></div>
		<div class="item"><a href="/rules/">История проекта</a></div>
		<div class="item"><a href="/help/">Справка</a></div>
		<div class="item"><a href="/stat/">Статистика</a></div>
		<br/>
		<div class="item"><a href="/feedback/">Обратная связь</a></div>
		<br/>
		<div class="item"><a href="/agreement/">Пользовательское соглашение</a></div>
	</td>
	<td class="layout">
		<div class="header"><a href="/top/new/">Список файлов</a></div>
		<div class="item"><a href="/top/new/">Свежие</a></div>
		<div class="item"><a href="/top/popular/">Популярные</a></div>
		<div class="item"><a href="/top/size/">Большие</a></div>
		<br/>
		<div class="item"><a href="/top/mp3/">Музыка</a></div>
		<div class="item"><a href="/top/video/">Видео</a></div>
		<div class="item"><a href="/top/foto/">Картинки</a></div>
		<div class="item"><a href="/top/archive/">Архивы</a></div>
		<div class="item"><a href="/top/image/">Образы дисков</a></div>
		<br/>
		<div class="item"><a href="/on-air/">Прямой эфир</a></div>
		<div class="item"><a href="/spam/">Спам</a></div>
	</td>

</td>
</tr>
</table>

<?
require UP_ROOT.'footer.php';
?>
