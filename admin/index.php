<?php

define('ADMIN_PAGE', 1);

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}

require UP_ROOT.'functions.inc.php';


$out = <<<FMB
<div id="status">&nbsp;</div>
<h2>Панель управления</h2>
<h3>Списки</h3>
<ul class="simple">
	<li><a href="{$base_url}admin/adult/">Adult</a></li>
	<li><a href="{$base_url}admin/hidden/">Скрытые</a></li>
</ul>

<h3>Разное</h3>
<ul class="simple">
	<li><a href="{$base_url}admin/logs/">Журнал событий (логи)</a></li>
	<li><a href="{$base_url}admin/storage/">Хранилища</a></li>
	<li><a href="{$base_url}admin/feedback/">Обратная связь</a></li>
</ul>
FMB;



printPage($out);

?>

