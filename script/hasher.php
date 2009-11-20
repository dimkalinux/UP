<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require_once UP_ROOT.'functions.inc.php';
require_once UP_ROOT.'include/upload.inc.php';

function TryAMakeHash() {
	// get LA
	if (($load = getServerLoad()) > 1) {
		echo "Too high LA — $load, go to sleep\n";
		return;
	}

	try {
		$db = new DB;
		$datas = $db->getData("SELECT id,location,sub_location,size FROM up WHERE hash='' AND deleted='0' ORDER BY id DESC LIMIT 5");

		if ($datas) {
			foreach ($datas as $rec) {
				$hash = '';
				if (($load = getServerLoad()) > 1) {
					echo "Too high LA — $load, go to sleep\n";
					return;
				}

				$file_id = $rec['id'];
				$file = $GLOBALS['upload_dir'].$rec['sub_location'].'/'.$rec['location'];
				$fileSize = $rec['size'];

				echo "hash: $file_id $file $fileSize ";
				$hash = Upload::makeHashMD5($file);
				if ($hash !== FALSE) {
					$db->query("UPDATE up SET hash=? WHERE id=? LIMIT 1", $hash, $file_id);
					echo "$hash OK\n";
				} else {
					echo "$hash ERROR\n";
				}
			}
		}
	} catch (Exception $e) {
		$log = new Logger;
		$log->error("Сбой хешера: ".$e->getMessage());
		die($e->getMessage());
	}
}


while (TRUE) {
	TryAMakeHash();
	echo "sleep for $makeHashTimeout seconds...\n";
	sleep($makeHashTimeout);
}

?>
