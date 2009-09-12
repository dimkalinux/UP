<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require UP_ROOT.'functions.inc.php';

if (is_admin() != true) {
	show_error_message('Доступ защищён зарослями фиалок и лютиков.');
	exit();
}

$out = <<<FMB
<div id="status">&nbsp;</div>
<h2>Панель управления</h2>
<h3>Списки</h3>
<ul class="simple">
	<li><a href="">Adult</a></li>
	<li><a href="">Скрытые</a></li>
</ul>

<h3>Разное</h3>
<ul class="simple">
	<li><a href="">Журнал событий (логи)</a></li>
	<li><a href="">Хранилища</a></li>
	<li><a href="">Обратная связь</a></li>
</ul>
FMB;



printPage($out);

?>

