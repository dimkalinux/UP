<?php
if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'header.php';
?>
<div id="status">&nbsp;</div>
<h2>Помощь</h2>
<ul class="simple">
	<li><a href="<?php echo $base_url ?>help/labels/">Метки</a></li>
	<li><a href="<?php echo $base_url ?>help/ftp/">Доступ по фтп</a></li>
	<li><a href="<?php echo $base_url ?>help/compability/">Совместимость с браузерами</a></li>
	<li><a href="<?php echo $base_url ?>help/uploaders/">Дополнительные программы</a></li>
</ul>


<?php
require UP_ROOT.'footer.php';
?>
