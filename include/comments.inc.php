<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}


class Comments {
	private $item_id;

	public function __construct($item_id) {
		if ($item_id < 1) {
			throw new Exception('Неверный ID в классе Comments');
		}
		$this->item_id = $item_id;
	}

	public function __destruct() {
		$this->item_id = 0;
	}


	public function commentsNum() {
		try {
			$db = new DB;
			$row = $db->getRow("SELECT COUNT(*) AS N FROM comments WHERE item_id=?", $this->item_id);
			return $row['N'];
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function addComment($text) {
		global $user;

		$text = mb_substr($text, 0, 2048);
		if (mb_strlen($text) < 1) {
			throw new Exception("Слишком короткий комментарий");
		}

		if ($user['is_guest']) {
			throw new Exception("Анонимные комментарии запрещены");
		}

		try {
			$db = new DB;
			$row = $db->query("INSERT INTO comments VALUES('', ?, ?, NOW(), ?)", $this->item_id, $user['id'], $text);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function getCommentList() {
		global $base_url;

		$out = '';

		try {
			$db = new DB;
			$datas = $db->getData("SELECT comments.id,user_id,date,message,username FROM comments LEFT JOIN users ON user_id=users.id WHERE item_id=? ORDER BY id", $this->item_id);

			if ($datas) {
				$out = '';
				foreach ($datas as $rec) {
					$id = $rec['id'];
					$text = check_plain(stripslashes($rec['message']));
					$date = $rec['date'];
					$username = "<a href=\"{$base_url}user/{$rec['user_id']}/\">{$rec['username']}</a>";
					$identicon = '<img class="avatar" src="'.$base_url.'include/identicon.php?size=48&amp;hash='.md5($rec["username"]).'" height="48" width="48" alt="'.$rec["username"].'"/>';

					$out .= <<<FMB
				<li id="comment_$id">
					$identicon
					$username<br/>
					<small>
						{$date}<br/><br/>
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

}

?>
