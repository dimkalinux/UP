<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', '../');
}
require UP_ROOT.'functions.inc.php';

$maxDiffDays = 5;
$minSize = 1048576*50;


$handle = fopen("/var/log/nginx/files_access_log", "r");
    $sizes = array('lds'=>0, 'lluga'=>0, 'lluga_office'=>0, 'world'=>0);
    $times = array('lds'=>0, 'lluga'=>0, 'lluga_office'=>0, 'world'=>0);
    $averageSpeed = array('lds'=>0, 'lluga'=>0, 'lluga_office'=>0, 'world'=>0);
    if ($handle) {
        while (!feof($handle)) {
			$line = chop(fgets($handle));
			if (preg_match('/(\S+)\s(\d+)\s(\d+)\s(\S+)/', $line, $matches)) {
				$geo = $matches[1];
				$status = $matches[2];
				$size = $matches[3];
				$itime = round($matches[4]);
				if ($itime < 1) {
					$itime = 1;
				}

				$diffDate = date_format(date_create('now'), 'U') - date_format(date_create($date), 'U');
					$dayDiff = floor($diffDate / 86400);

				if ($dayDiff > $maxDiffDays) {
					continue;
				}

				$rawspeed = $size/$itime;
				$speed = format_speed($rawspeed, true);

				if ($size < $minSize) {
					continue;
				}

				$sizes[$geo] += $size;
				$times[$geo] += $itime;

				print("$geo\t$dayDiff\t$speed\n");
			}
		}
		fclose($handle);

		print "\n======== Average speed by GEO ========\n";

		foreach ($averageSpeed as $key=>$g) {
			if ($sizes[$key] > 0 && $times[$key] > 0) {
	    		$averageSpeed[$key] = $sizes[$key]/$times[$key];
	    		echo "$key:\t".format_speed($averageSpeed[$key], true)."\n";
			}
		}
    }
?>
