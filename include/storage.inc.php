<?php

// version 1.0.2

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}



class Storage {
	private $storage = array();
	private $cacheKey = 'up_storage';
	// add 10 Gb
	private $addSpace = 1073741824;

	//
	public function __construct() {
		for ($i = 1; $i <= 32; $i++) {
			if (isset($GLOBALS["storage_$i"])) {
				$storage = $GLOBALS["storage_$i"];

				// skip disabled items
				if ($storage['disabled'] == 1) {
					continue;
				}

				// check for mount point
				if (is_dir($storage['mount_point'])) {
					$this->storage[] = $storage;
				} else {
					$log = new Logger;
					$log->error("Storage module invalid mount_point: ".$storage['mount_point']);
				}
			}
		}
	}

	//
	public function __destruct() {
		unset($storage);
	}


	public function get_list() {
		return $this->storage;
	}

	public function migrate_storage_to_db() {
		for ($i = 1; $i <= 32; $i++) {
			if (!isset($GLOBALS["storage_$i"])) {
				continue;
			}

			$storage = $GLOBALS["storage_$i"];

			$st_upload_url = $storage['upload_url'];
			$st_name = $storage['name'];
			$st_device = $storage['device'];
			$st_mount_point = $storage['mount_point'];
			$st_disabled = $storage['disabled'];
			$st_prio = $storage['prio'];
			$st_hash = serialize($storage['hash']);

			try {
				$db = DB::singleton();
				$db->query("INSERT INTO storage VALUES('', ?, ?, ?, ?, ?, ?, ?)", $st_upload_url, $st_device, $st_mount_point, $st_name, $st_prio, $st_disabled, $st_hash);
			} catch (Exception $e) {
				error($e->getMessage());
			}
		}
	}


	public function get_stat_by_name($name) {
		$st = null;

		// find storage by name
		foreach ($this->storage as $storage) {
			if ($storage['name'] == $name) {
				$st = $storage;
				break;
			}
		}

		if ($st == null) {
			throw new Exception('Storage error: «no storage with this name»');
		}

		$result = array('totalSpace' => 0, 'totalFreeSpace' => 0, 'totalUseSpace' => 0);
		$result['totalFreeSpace'] = round(disk_free_space($st['mount_point']), 0);
		$result['totalSpace'] = round(disk_total_space($st['mount_point']), 0);
		$result['totalUseSpace'] = $result['totalSpace'] - $result['totalFreeSpace'];

		return $result;
	}


	public function get_stat($sync=false) {
		$cache = null;

		if ($sync === false) {
			$cache = new Cache;
			if ($result = $cache->get($this->cacheKey)) {
				return $result;
			}
		}

		$result = array('totalSpace' => 0, 'totalFreeSpace' => 0, 'totalUseSpace' => 0, 'countStorage' => 0);
		foreach ($this->storage as $storage) {
			$device = $storage['device'];
			$mount = $storage['mount_point'];
			$deviceFreeSpace = round(disk_free_space($mount), 0);
			$deviceTotalSpace = round(disk_total_space($mount), 0);

			$result['totalSpace'] += $deviceTotalSpace;
			$result['totalFreeSpace'] += $deviceFreeSpace;
			$result['countStorage']++;
		}

		$result['totalUseSpace'] = $result['totalSpace'] - $result['totalFreeSpace'];

		if ($sync === false) {
			$cache->set($result, $this->cacheKey, 600);
		}

		return $result;
	}


	public function get_upload_url($getForFUSE=false) {
		$url = null;
		$storageIndex = array();
		$storageBestIndex = array();

		foreach ($this->storage as $i=>$storage) {
			$storageFreeSpace = ceil(disk_free_space($storage['mount_point'])) - $this->addSpace;
			// должен поместиться самый большой файл
			if ($GLOBALS['max_file_size']*2 < $storageFreeSpace) {
				$storageIndex[] = $i;

				// good storage or maybe best?
				$curent_uploads = 0;
				$upload_dir = $storage['mount_point'].'/'.'tmp_up';
				if ($dh = opendir($upload_dir)) {
        			while (false !== ($file = readdir($dh))) {
            			if ($file == '.' || $file == '..') {
                			continue;
						}

						$p = $upload_dir."/".$file;
            			if (is_file($p)) {
							$curent_uploads++;
						}
        			}
        			closedir($dh);

					// is best upload
					if ($curent_uploads == 0) {
						$storageBestIndex[] = $i;
						//return $storage['upload_url'];
					}
				} else {
					throw new Exception('Storage error: «cant count current uploads»');
				}
			}
		}


		// fitrst try a Best storage
		if (count($storageBestIndex) > 1) {
			$goodStorage = $this->storage[$storageBestIndex[array_rand($storageBestIndex, 1)]];
			$url = $goodStorage['upload_url'];

			if ($url === null) {
				throw new Exception('StorageBest error: «upload url not exists»');
			}
		} else if (count($storageIndex) > 0) {
			$goodStorage = $this->storage[$storageIndex[array_rand($storageIndex, 1)]];
			$url = $goodStorage['upload_url'];

			if ($url === null) {
				throw new Exception('Storage error: «upload url not exists»');
			}
		} else {
			throw new Exception('Storage error: «no storage for upload»');
		}

		return $url;
	}



	public function get_upload_subdir($storageName) {
		$subdir = null;

		foreach ($this->storage as $i=>$storage) {
			if ($storageName == $storage['name']) {
				$subdirs = $storage['hash'];
				$subdir = $subdirs[array_rand($subdirs, 1)];
				break;
			}
		}

		if ($subdir === null) {
			throw new Exception('Storage error: «no good storage»');
		} else {
			$fulldir = $GLOBALS['upload_dir'].'/'.$subdir;
			if (!is_dir($fulldir)) {
				throw new Exception('Storage error: «subdir not exists: '.$subdir.'»');
			}
		}

		return $subdir;
	}
}


?>
