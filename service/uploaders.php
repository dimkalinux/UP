<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}

require UP_ROOT.'functions.inc.php';
require UP_ROOT.'header.php';
?>

<div id="status">&nbsp;</div>
<h2>Дополнительные программы</h2>

<p>Существуют несколько программ, позволяющих закачивать файлы на&nbsp;наш сервис.
Мы рекомендуем использовать эти программы для закачки очень больших файлов&nbsp;&mdash; более 2&nbsp;гигабайт.</p>
<ul>
	<li><a href="http://forum.lluga.net/labs/fireUP/">fireUP&nbsp;&mdash; расширение для браузера&nbsp;Фаирфокс</a></li>
	<li><a href="http://forum.lluga.net/labs/sendtoup/">Send to&nbsp;UP&nbsp;&mdash; <acronym title="Practical Extraction and Reporting Language" lang="en">PERL</acronym> скрипт для консольной загрузки&nbsp;файлов</a></li>
</ul>
<p>Также можно закачивать файлы <a href="/ftp_access.php">с помощью фтп-клиентов</a>.</p>


<?
require UP_ROOT.'footer.php';
?>
