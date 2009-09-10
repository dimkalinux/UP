<?
if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require UP_ROOT.'functions.inc.php';



$handle = fopen("/var/upload/files.txt", "r");
if ($handle) {
    $db = new DB;

    while (!feof($handle)) {
    	$line = chop(fgets($handle));
	list(,,,$subdir, $name) = explode('/', $line);
	
	//echo "$subdir - $name\n";

	if (!$subdir || !$name) {
	    continue;
	}

    	$row = $db->getRow("SELECT id,deleted,deleted_date FROM up WHERE sub_location=? AND location=?", $subdir, $name);
	if (!$row) {
		if (is_file($line)) {
		    print("ERROR: $subdir\t$name\n");
		    unlink($line);
		}
	} else {
		if ($row['deleted'] == 1) {
		    if (is_file($line)) {
			print("DELETED ERROR ({$row['deleted_date']}): $subdir\t$name\n");
			unlink($line);
		    }
		}
	}

	unset($row);
    }
    fclose($handle);
}

?>
