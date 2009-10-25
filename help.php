<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'header.php';
?>
<div id="status">&nbsp;</div>
<h2>F.A.Q.</h2>
<ol class="simple mb">
	<li><a href="#labels">Метки</a></li>
	<li><a href="#ftpAccess">Доступ по фтп</a></li>
	<li><a href="#compability">Совместимость с браузерами</a></li>
	<li><a href="#addons">Дополнительные программы</a></li>
</ol>

<h3 id="labels">Метки</h3>
<table class="t2">
<tr>
	<td class="center"><span class="passwordLabel" title="Файл защищён паролем">&beta;</span></td>
	<td>файл защищён паролем</td>
</tr>
<tr>
	<td class="center"><span class="popularLabel" title="Более 100 скачиваний">+100</span></td>
	<td>файл скачан более 100 раз</td>
</tr>
<tr>
	<td class="center"><span class="popularLabel" title="Более 1000 скачиваний">+1k</span></td>
	<td>файл скачан более 1000 раз</td>
</tr>
</table><br/>


<h3 id="ftpAccess">Доступ по фтп</h3>
<p>Сервис поддерживает доступ к личным файлам по фтп.
Для этого необходимо быть зарегистрированным пользователем.</p>
<h4>Параметры фтп-клиента для доступа к своим файлам</h4><br/>
<ol class="simple mb">
	<li><strong>Сервер</strong>: up.iteam.net.ua</li>
	<li><strong>Логин</strong>: ваш логин на АПе</li>
	<li><strong>Пароль</strong>: ваш пароль на АПе</li>
</ol>

<h3 id="compability">Совместимость с браузерами</h3>
<h4>Mozilla Firefox</h4>
<p>Работает со всеми версиями&nbsp;1.00 — 3.6.</p>

<h4>Opera</h4>
<p>Работает начиная с&nbsp;верси&nbsp;9.27 и выше.<br/>
Для удобства работы с сервисом рекомендуем отключить настройку <a href="opera:config#UserPrefs|AutomaticSelectMenu">AutomaticSelectMenu</a>.</p>

<h4>Google Chrome, Chromium</h4>
<p>Работает.</p>

<h4>Safari</h4>
<p>Работает с&nbsp;версией&nbsp;4. Возможно работает и с&nbsp;более ранними версиями.</p>

<h4>Internet Explorer</h4>
<p>
Возможно работает с версиям 7 и&nbsp;8.<br/>
Не работает с версиями 5 и&nbsp;6.
</p>


<h3 id="addons">Дополнительные программы</h3>
<p>Существуют несколько программ, позволяющих закачивать файлы на&nbsp;наш сервис.
Мы рекомендуем использовать эти программы для закачки очень больших файлов&nbsp;&mdash; более 2&nbsp;гигабайт.</p>
<ul>
	<li><a href="http://forum.lluga.net/labs/fireUP/">fireUP&nbsp;&mdash; расширение для браузера&nbsp;Фаирфокс</a></li>
	<li><a href="http://forum.lluga.net/labs/sendtoup/">Send to&nbsp;UP&nbsp;&mdash; <acronym title="Practical Extraction and Reporting Language" lang="en">PERL</acronym> скрипт для консольной загрузки&nbsp;файлов</a></li>
</ul>



<?
require UP_ROOT.'footer.php';
?>
