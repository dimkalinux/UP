<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require_once UP_ROOT.'functions.inc.php';
require_once UP_ROOT.'include/upload.inc.php';

function TryACheckForAVirus() {
	global $max_file_size_for_antivir_check;
	// get LA
	if (($load = getServerLoad()) > 1) {
		echo "Too high LA — $load, go to sleep\n";
		return;
	}

	try {
		$db = new DB;
		$datas = $db->getData("SELECT id,location,sub_location,size FROM up WHERE antivir_checked=? AND deleted='0' AND size < $max_file_size_for_antivir_check ORDER BY id DESC LIMIT 5", ANTIVIR_NOT_CHECKED);

		if ($datas) {
			foreach ($datas as $rec) {
				if (($load = getServerLoad()) > 1) {
					echo "Too high LA — $load, go to sleep\n";
					return;
				}

				$file_id = $rec['id'];
				$file = $GLOBALS['upload_dir'].$rec['sub_location'].'/'.$rec['location'];
				$fileSize = $rec['size'];

				$vircheck = ANTIVIR_NOT_CHECKED; // non checked
				echo "check: $file_id $file $fileSize ";
				$vircheck_result = Upload::antivirCheckFileClam($file);

				if ($vircheck_result == 0) {
					$vircheck = ANTIVIR_CLEAN;
				} elseif ($vircheck_result == 1) {
					$vircheck = ANTIVIR_VIRUS;
				} elseif ($vircheck_result > 1) {
					$vircheck = ANTIVIR_ERROR;
				}

				$db->query("UPDATE up SET antivir_checked=? WHERE id=? LIMIT 1", $vircheck, $file_id);
				echo "$vircheck\n";
			}
		}
	} catch (Exception $e) {
		die($e->getMessage());
	}
}


while (TRUE) {
	TryACheckForAVirus();
	echo "sleep for $makeVirusesTimeout seconds...\n";
	sleep($makeVirusesTimeout);
}

?>
