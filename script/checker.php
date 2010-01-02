<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'include/upload.inc.php';

//
$problems = 0;
$problems_fixed = 0;

if (!$enableChecker) {
	$log = new Logger;
	$log->info("Checker: disabled by config");
	exit();
}


try {
	$db = new DB;
	$datas = $db->getData("SELECT * FROM up WHERE deleted='0'");
} catch (Exception $e) {
	$log = new Logger;
	$log->error("Checker: ".$e->getMessage());
}

if (!$datas) {
	exit();
}

clearstatcache ();

foreach ($datas as $rec) {
	$file_id = $rec['id'];
	$file = $upload_dir.$rec['sub_location'].'/'.$rec['location'];
	$file_size = $rec['size'];
	$file_hash = $rec['hash'];
	$file_name = $rec['filename'];

		echo ("$file_id\t");

		if (!file_exists($file)) {
			if ($checker_check_size) {
				Checker::check_size($file, $file_size, $file_id);
			}

			if ($checker_check_hash) {
				Checker::check_hash($file, $file_md5);
			}

			if ($checker_check_thumbs) {
				Checker::check_thumbs($file, $file_name, $file_id, $file_md5);
			}
			echo ("\n");
		} else {
			$count_problems++;
			echo ("fe: ERR ($file)\t");

			if ($fix_problems) {
				$reason = 'Файл удалён системой (checker)';
				if ($db->query("UPDATE up SET deleted='1', deleted_reason=?, deleted_date=NOW() WHERE id=?", $reason, $file_id)) {
					$count_problems_fixed++;
					echo (" fixed\t\n");
				} else {
					echo (" not fixed\t\n");
				}
			}
		}
	}


	$footer = <<<FMB
====================================================
founded problems:\t$count_problems
fixed problems:  \t$count_problems_fixed
FMB;
	echo $footer;
}

exit ();


class Checker {
	public static function check_size($file, $db_size, $file_id) {
		global $problems, $problems_fixed;

		$real_file_size = filesize($file);
		if ($real_file_size != $db_size) {
			$GLOBALS['count_problems']++;
			echo("\tsize: ERR ($real_file_size != $db_size)\t");
		}
	} else {
		echo("size: OK");
	}
}

	public static function check_hash($file, $db_md5) {
		$real_md5 = md5_file ($file);
		if ($db_md5 && ($real_md5 != $db_md5)) {
			$GLOBALS['count_problems']++;
			echo ("\tmd5: ERR ($file $real_md5 != $db_md5)\t");
		} else {
			echo ("\tmd5: OK");
		}
	}


	public static function check_thumbs($file, $filename, $id, $md5) {
		global $server_root, $problems, $problems_fixed;

		if (is_image($filename, $file)) {
			$small_t = $server_root.'thumbs/'.sha1($id).'.jpg';
			$large_t = $server_root.'thumbs/large/'.sha1($id).'.jpg';

			if (!file_exists ($small_t) || !file_exists ($large_t)) {
				echo ("\tthumbs: ERR not exists");

				if ($GLOBALS['fix_problems']) {
					$err = 0;
					// unlink old thumbs
					if (file_exists($small_t)) {
						if (!unlink($small_t)) {
							$err = 1;
							echo " ERR removed old small";
						}
					}

					if (file_exists($large_t)) {
						if (!unlink($large_t)) {
							$err = 1;
							echo " ERR removed old large";
						}
					}

					$upload = new Upload();

					if ($err == 0 && $upload->generateThumbs($file, $filename, $id)) {
						echo "\t FIXED";
					} else {
						$GLOBALS['count_problems']++;
					}
				}
			}
		}

	}
}


?>
