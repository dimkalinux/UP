<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'header.php';
?>
<div id="status">&nbsp;</div>
<h2>История проекта</h2>

<p>Простой и&nbsp;быстрый файлообменник.
Исходный код проекта доступен на&nbsp;условиях <a href="http://www.gnu.org/copyleft/gpl.html">GNU&nbsp;General&nbsp;Public&nbsp;License</a>. Полный текст лицензии на&nbsp;<a href="http://www.infolex.narod.ru/gpl_gnu/gplrus.html" title="неофициальный перевод">русском</a> и&nbsp;на&nbsp;<a href="/COPYING" title="">английском</a> языках.
</p>


<h3>Требования</h3>
<p><strong>АП</strong>&nbsp;написан на&nbsp;<a href="http://www.php.net/">PHP: Hypertext Preprocessor</a>. Тестировался в&nbsp;операционной системе Linux, возможно будет работать и&nbsp;в&nbsp;Windowz. Для хранения информации о&nbsp;загруженных файлах требуется база данных <a href="http://www.mysql.com/">MySQL</a>. Для PHP требуются расширения <a href="http://www.php.net/mysqli/">mysqli</a> и&nbsp;<a href="http://www.php.net/memcache/">memcache</a>.</p>
<p>
В качестве вебсервера используется связка <a href="http://sysoev.ru/nginx/">Nginx</a> + <a href="http://httpd.apache.org/">Apache2</a>.</p>

<!--<h3>Исходный код проекта</h3>
<p>Код проекта доступен в <a href="http://code.google.com/p/up2-0/">Google Code</a>.</p>-->


<h3>Автор</h3>
<p>Идея и реализация, а также дизайн &mdash; <a href="mailto:dimka.linux@gmail.com">dimkalinux</a>.</p>
<h3>Благодарности</h3>
<p><cite>Jiv4ik</cite> за вдохновение  &mdash; без тебя я бы никогда не написал этот проект ;-)<br/>
<a href="http://forum.lluga.net/profile.php?id=537">Ирине</a>&nbsp;&mdash; без её&nbsp;ценных
замечаний я&nbsp;бы никогда не&nbsp;реализовал список всех файлов и&nbsp;систему поиска.<br/>
<cite>Ami.able</cite>&nbsp;&mdash; без неё проект был бы совсем другим.</p>

<!--<h3>Дизайн</h3>
<p>Хороший дизайн &mdash; незаметный дизайн.</p>-->

<h3>Обратная связь</h3>
<p>Чтобы связаться с&nbsp;администрацией проекта воспользуйтесь <a href="/feedback/">формой обратной связи</a>.</p>



<?
require UP_ROOT.'footer.php';
?>
