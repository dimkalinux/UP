<?php
if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}

require UP_ROOT.'functions.inc.php';

function convert_filename_in_db() {
	$db = DB::singleton();
	$datas = $db->getData('SELECT id,filename,filename_fuse,deleted_reason FROM up');
	foreach ($datas as $row) {
		echo "{$row['id']}\t{$row['filename']}\t{$row['filename_fuse']}\t{$row['deleted_reason']}\n";
	}
}

function convert_username_in_db() {
	$db = DB::singleton();
	$datas = $db->getData('SELECT id,username FROM users');
	foreach ($datas as $row) {
		echo "{$row['id']}\t{$row['username']}\n";
	}
}


/*
$handle = fopen("/tmp/u_ip_2", "r");
if ($handle) {
    $db = DB::singleton();

    while (!feof($handle)) {
    	$line = chop(fgets($handle));
		@/**///list($id,$filename,$filename_fuse,$deleted_reason) = explode("\t", $line);
		/*if (empty($deleted_reason)) {
			$deleted_reason = ' ';
		}

		if (empty($filename_fuse)) {
			$filename_fuse = $filename;
		}

		echo "$id '$filename' '$deleted_reason'\n";

		if (empty($id) || empty($filename) || empty($filename_fuse)) {
		    continue;
		}

		$id = intval($id, 10);
		if ($id < 1) {
			die('invalid id '.$line);
		}

		$db->query('UPDATE up SET filename=?, filename_fuse=?, deleted_reason=? WHERE id=?', $filename, $filename_fuse, $deleted_reason, $id);

	}
	fclose($handle);
}
*
*
*/
//convert_filename_in_db(74148);

$handle = fopen("/tmp/users", "r");
if ($handle) {
    $db = DB::singleton();

    while (!feof($handle)) {
    	$line = chop(fgets($handle));
		@/**/list($id,$username) = explode("\t", $line);

		echo "$id '$username'\n";

		if (empty($id) || empty($username)) {
		    continue;
		}

		$id = intval($id, 10);
		if ($id < 1) {
			die('invalid id '.$line);
		}

		$db->query('UPDATE users SET username=? WHERE id=?', $username, $id);

	}
	fclose($handle);
}

//convert_username_in_db();
?>
