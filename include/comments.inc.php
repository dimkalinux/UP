<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}


class Comments {
	private $item_id;
	private $item_owner_id;

	public function __construct($item_id, $owner_id=-1) {
		if ($item_id < 1) {
			throw new Exception('Неверный ID в классе Comments');
		}
		$this->item_id = $item_id;
		$this->item_owner_id = $owner_id;
	}

	public function __destruct() {
		$this->item_id = 0;
		$this->item_owner_id = -1;
	}


	public function commentsNum() {
		try {
			$db = new DB;
			$row = $db->getRow("SELECT COUNT(*) AS N FROM comments WHERE item_id=?", $this->item_id);
			return intval($row['N'], 10);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function addComment($text) {
		global $user, $maxCommentLength;

		if ($user['is_guest']) {
			throw new Exception("Анонимные комментарии запрещены");
		}

		$text = mb_substr($text, 0, $maxCommentLength);
		$text = str_replace(array("\n\n"), array("\n"), $text);
		if (mb_strlen($text) < 1) {
			throw new Exception("Слишком короткий комментарий");
		}

		try {
			// typografy comments
			$text = $this->typografyComments($text);
			$db = new DB;
			// check last poster
			/*$row = $db->getRow("SELECT id,user_id,message FROM comments WHERE item_id=? ORDER BY id DESC LIMIT 1", $this->item_id);
			$lastPosterID = ($row === null) ? -1 : $row['user_id'];
			$lastCommentID = ($row === null) ? -1 : $row['id'];
			$lastCommentText = ($row === null) ? -1 : $row['message'];

			if ($lastPosterID == $user['id']) {
				// update prev comments
				$lastCommentText .= "\n\n$text";
				$db->query("UPDATE comments SET message=?,date=NOW() WHERE id=? LIMIT 1", $lastCommentText, $lastCommentID);
				return
			} else {*/
			$db->query("INSERT INTO comments VALUES('', ?, ?, NOW(), ?)", $this->item_id, $user['id'], $text);
			//}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}


	public function getCommentList($lastCommentID=0) {
		global $base_url, $user;

		$out = '';

		try {
			$db = new DB;
			$datas = $db->getData("SELECT comments.id,user_id,date,message,username FROM comments LEFT JOIN users ON user_id=users.id WHERE item_id=? AND comments.id > ? ORDER BY id", $this->item_id, $lastCommentID);

			if ($datas) {
				$out = '';
				foreach ($datas as $rec) {
					$id = $rec['id'];
					$text = stripslashes($rec['message']);
					$date = $rec['date'];
					$username = "<a href=\"{$base_url}user/{$rec['user_id']}/\">{$rec['username']}</a>";
					$identicon = '<img class="avatar" src="'.$base_url.'include/identicon.php?size=48&amp;hash='.md5($rec["username"]).'" height="48" width="48" alt="'.$rec["username"].'"/>';
					$deleteLink = '';
					if ($user['is_admin']) {
						$deleteLink = ', <span class="as_js_link" title="Удалить комментарий" onclick="UP.comments.remove('.$id.')">X</span>';
					}

					$ownerClass = '';
					if ($user['id'] == $this->item_owner_id) {
						$ownerClass = 'itemOwner';
					}

					$out .= <<<FMB
				<li id="comment_$id" class="$ownerClass">
					$identicon
					$username<br/>
					<small>{$date}{$deleteLink}<br/><br/>
					</small>
					<p>$text</p>
				</li>
FMB;
				}
			}
			return $out;

		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}


	private function typografyComments($text) {
		require UP_ROOT.'include/jevix/jevix.class.php';
		require UP_ROOT.'include/markdown.php';

		// 1 — Markdown
		$text = Markdown($text);

		// 2 — Jevix
		$jevix = new Jevix();
		//Конфигурация
		// 1. Устанавливаем разрешённые теги. (Все не разрешенные теги считаются запрещенными.)
		$jevix->cfgAllowTags(array('a', 'i', 'b', 'u', 'em', 'strong', 'nobr', 'li', 'ol', 'ul', 'sup', 'abbr', 'pre', 'acronym', 'h4', 'h5', 'h6', 'adabracut', 'br', 'code'));

		// 2. Устанавливаем коротие теги. (не имеющие закрывающего тега)
		$jevix->cfgSetTagShort(array('br'));

		// 3. Устанавливаем преформатированные теги. (в них все будет заменятся на HTML сущности)
		$jevix->cfgSetTagPreformatted(array('pre'));

		// 4. Устанавливаем теги, которые необходимо вырезать из текста вместе с контентом.
		$jevix->cfgSetTagCutWithContent(array('script', 'object', 'iframe', 'style'));

		// 5. Устанавливаем разрешённые параметры тегов. Также можно устанавливать допустимые значения этих параметров.
		$jevix->cfgAllowTagParams('a', array('title', 'href'));
		//$jevix->cfgAllowTagParams('img', array('src', 'alt' => '#text', 'title', 'align' => array('right', 'left', 'center'), 'width' => '#int', 'height' => '#int', 'hspace' => '#int', 'vspace' => '#int'));


		// 6. Устанавливаем параметры тегов являющиеся обязяательными. Без них вырезает тег оставляя содержимое.
		//$jevix->cfgSetTagParamsRequired('img', 'src');
		$jevix->cfgSetTagParamsRequired('a', 'href');

		// 7. Устанавливаем теги которые может содержать тег контейнер
		//    cfgSetTagChilds($tag, $childs, $isContainerOnly, $isChildOnly)
		//       $isContainerOnly : тег является только контейнером для других тегов и не может содержать текст (по умолчанию false)
		//       $isChildOnly : вложенные теги не могут присутствовать нигде кроме указанного тега (по умолчанию false)
		$jevix->cfgSetTagChilds('ul', 'li', true, true);

		// 8. Устанавливаем атрибуты тегов, которые будут добавлятся автоматически
		$jevix->cfgSetTagParamsAutoAdd('a', array('rel' => 'nofollow'));
		//$jevix->cfgSetTagParamsAutoAdd('img', array('width' => '300', 'height' => '300'));

		// 9. Устанавливаем автозамену
		$jevix->cfgSetAutoReplace(array('+/-', '(c)', '(r)'), array('±', '©', '®'));

		// 10. Включаем или выключаем режим XHTML. (по умолчанию включен)
		$jevix->cfgSetXHTMLMode(true);

		// 11. Включаем или выключаем режим замены переноса строк на тег <br/>. (по умолчанию включен)
		$jevix->cfgSetAutoBrMode(true);

		// 12. Включаем или выключаем режим автоматического определения ссылок. (по умолчанию включен)
		$jevix->cfgSetAutoLinkMode(true);

		// 13. Отключаем типографирование в определенном теге
		$jevix->cfgSetTagNoTypography('code');

		$jevix_errors = null;
		// Парсим
		$message = $jevix->parse($text, $jevix_errors);

		return $message;
	}

}

?>
