<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'include/comments.inc.php';

$out = '<div id="status">&nbsp;</div><h2>Свежие комментарии</h2><ul id="lastComments" class="commentList">'.Comments::getLastCommentList().'</ul>';

printPage($out);

?>
