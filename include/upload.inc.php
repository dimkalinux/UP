<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}


class Upload {
	private function generatePathname($storagepath='') {
		if (mb_strlen($storagepath) > 0) {
			//we have to check so that path doesn't exist already...
			$not_unique = true;

			while ($not_unique) {
				$newdir = $this->generatePathname();
				if (!is_dir ($storagepath.$newdir)) {
					return $newdir;
				}
			}
		} else {
			return mb_substr(sha1(time().'24111988'), 0, 32);
		}
	}


	public function generateFilename($storagepath, $messagelenght=0, $filesize=0) {
		clearstatcache();

		$not_unique = true;
		while ($not_unique) {
			$newfile = sha1($this->generate_pathname().$messagelenght.$filesize.'24111988').'.attach';

			if (!is_file($storagepath.$newfile)) {
				return $newfile;
			}
		}
	}


	public function createMIME($extension) {
		$mimecodes = array (
			'rtf' 			=>		'text/richtext',
			'html'			=>		'text/html',
			'htm'			=>		'text/html',
			'aiff'			=>		'audio/x-aiff',
			'iff'			=>		'audio/x-aiff',
			'basic'			=>		'audio/basic',  // no idea about extention
			'wav'			=>		'audio/wav',
			'gif'			=>		'image/gif',
			'jpg'			=>		'image/jpeg',
			'jpeg'			=>		'image/pjpeg',
			'tif'			=>		'image/tiff',
			'png'			=>		'image/x-png',
			'xbm'			=>		'image/x-xbitmap',  // no idea about extention
			'bmp'			=>		'image/bmp',
			'xjg'			=>		'image/x-jg',  // no idea about extention
			'emf'			=>		'image/x-emf',  // no idea about extention
			'wmf'			=>		'image/x-wmf',  // no idea about extention
			'avi'			=>		'video/avi',
			'mpg'			=>		'video/mpeg',
			'mpeg'			=>		'video/mpeg',
			'ps'			=>		'application/postscript',
			'b64'			=>		'application/base64',  // no idea about extention
			'macbinhex'		=>		'application/macbinhex40',  // no idea about extention
			'pdf'			=>		'application/pdf',
			'xzip'			=>		'application/x-compressed',  // no idea about extention
			'zip'			=>		'application/x-zip-compressed',
			'gzip'			=>		'application/x-gzip-compressed',
			'java'			=>		'application/java',
			'msdownload'	=>		'application/x-msdownload'  // no idea about extention
		);

		foreach ($mimecodes as $type => $mime ) {
			if ($extension == $type) {
				return $mime;
			}
		}

		return 'application/octet-stream';	// default, if not defined above...
	}

	public function get_upload_flood_counter() {
		$cache = new Cache;
		$floodKey = 'uf'.get_client_ip();
		$floodCounter = $cache->get($floodKey);

		return ($floodCounter === false) ? 1 : $floodCounter;
	}

	public function is_upload_flood() {
		$cache = new Cache;
		$floodKey = 'uf'.get_client_ip();
		$floodCounter = $cache->get($floodKey);

		if ($floodCounter === false) {
			$floodCounter = 1;
		}

		if ($floodCounter < 4) {
			return false;
		}

		// flood
		if ($floodCounter > 4 && $floodCounter != 100) {
			$floodCounter = 100;
			$cache->set($floodCounter, $floodKey, 1800);
		}

		return ($floodCounter == 100);
	}

	public static function getFilenameForFUSE($filename, $user_id) {
		try {
			$db = new DB;
			$datas = $db->getData("SELECT filename_fuse FROM up WHERE user_id=? AND deleted=0 LIMIT 1000", $user_id);
		} catch (Exception $e) {
			error($e->getMessage());
		}

		if (!$datas) {
			return $filename;
		}

		$files = array();

		foreach ($datas as $item) {
			$files[] = $item['filename_fuse'];
		}

		if (!in_array($filename, $files)) {
			return $filename;
		}


		$ext = get_file_ext($filename);
		$name = mb_substr($filename, 0, (strripos($filename, $ext)-1));
		for ($i=1; $i<999999; $i++) {
			$_ext = get_file_ext($filename);
			$_name = mb_substr($filename, 0, (strripos($filename, $ext)-1));
			$fullname = $_name.'_'.$i.'.'.$_ext;
			if (!in_array($fullname, $files)) {
				return $fullname;
			}
		}

		return $filename;
	}


	public function antivirCheckFileDrWEB($file) {
		$ret = 2;

		if (is_file($file)) {
			$file = escapeshellcmd($file);
			exec("/opt/drweb/drwebdc -nlocalhost -f '$file'", $output, $ret);
		}

		return $ret;
	}

	public function antivirCheckFileClam($file) {
		$ret = 2;

		if (is_file($file)) {
			$file = escapeshellcmd($file);
			exec("/usr/bin/clamdscan --no-summary '$file'", $output, $ret);
		}

		return $ret;
	}

	public static function updateUploadsCounters($uid, $upload, $uploadSize) {
		try {
			$db = new DB;
			$db->query("UPDATE DELAYED users SET uploads=uploads+?, uploads_size=uploads_size+? WHERE id=?", $upload, $uploadSize, $uid);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function generateThumbs($uploadfile, $up_file_name, $item_id) {
		global $thumbs_w, $thumbs_h, $thumbs_preview_w, $thumbs_preview_h;

		if (is_file($uploadfile) && is_image($up_file_name, $uploadfile)) {
			$key_name = $this->getThumbsFilename($item_id);
			$thumbs_filename = 'thumbs/'.$key_name.'.jpg';
			$thumbs_preview_filename = 'thumbs/large/'.$key_name.'.jpg';

			require_once UP_ROOT.'include/phpThumb/phpthumb.class.php';

			$phpThumb = new phpThumb();
			$phpThumb->setSourceFilename($uploadfile);
			$phpThumb->w = $thumbs_w;
			$phpThumb->h = $thumbs_h;
			$phpThumb->config_output_format = 'jpeg';
			$phpThumb->config_error_die_on_error = false;
			$phpThumb->config_allow_src_above_docroot = true;

			if ($phpThumb->GenerateThumbnail()) {
				$phpThumb->RenderToFile($thumbs_filename);
				unset($phpThumb);
			}

			// CREATE LARGE
			if (!empty($thumbs_preview_filename)) {
				$phpThumb = new phpThumb();
				$phpThumb->setSourceFilename($uploadfile);
				$phpThumb->w = $thumbs_preview_w;
				$phpThumb->h = $thumbs_preview_h;
				$phpThumb->config_output_format = 'jpeg';
				$phpThumb->config_error_die_on_error = false;
				$phpThumb->config_allow_src_above_docroot = true;

				if ($phpThumb->GenerateThumbnail()) {
					$phpThumb->RenderToFile($thumbs_preview_filename);
				}
			}
		}
	}

	public static function makeHash($file) {
		if (!is_file($file)) {
			throw new Exception("makeHash: file '$file' not exists");
		}

		return sha1_file($file);
	}


	private static function getThumbsFilename($item_id) {
		return 'thumbs/'.sha1($item_id).'.jpg';
	}
}

?>
