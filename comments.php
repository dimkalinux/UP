<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'include/comments.inc.php';

$out = Comments::getLastCommentList();

require UP_ROOT.'header.php';
echo('	<div id="status">&nbsp;</div><h2>Свежие комментарии</h2><ul id="lastComments" class="commentList">'.$out.'</ul>');
require UP_ROOT.'footer.php';
exit();

?>
