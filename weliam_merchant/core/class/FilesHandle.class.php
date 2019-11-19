<?php
//dezend by http://www.sucaihuo.com/
class FilesHandle
{
	static public function file_copy($fromFile, $toFile)
	{
		self::file_create_folder($toFile);
		$folder1 = opendir($fromFile);

		while ($f1 = readdir($folder1)) {
			if ($f1 != '.' && $f1 != '..') {
				$path2 = $fromFile . '/' . $f1;

				if (is_file($path2)) {
					$file = $path2;
					$newfile = $toFile . '/' . $f1;
					copy($file, $newfile);
				}
				else {
					if (is_dir($path2)) {
						$toFiles = $toFile . '/' . $f1;
						self::file_copy($path2, $toFiles);
					}
				}
			}
		}
	}

	static public function file_create_folder($dir, $mode = 511)
	{
		if (is_dir($dir) || @mkdir($dir, $mode)) {
			return true;
		}

		if (!self::file_create_folder(dirname($dir), $mode)) {
			return false;
		}

		return @mkdir($dir, $mode);
	}

	static public function file_list_dir($dir)
	{
		$result = array();

		if (is_dir($dir)) {
			$file_dir = scandir($dir);

			foreach ($file_dir as $file) {
				if ($file == '.' || $file == '..') {
					continue;
				}

				if (is_dir($dir . $file)) {
					$result = array_merge($result, self::file_list_dir($dir . $file . '/'));
				}
				else {
					array_push($result, $dir . $file);
				}
			}
		}

		return $result;
	}

	static public function file_tree($path)
	{
		$files = array();
		$ds = glob($path . '/*');

		if (is_array($ds)) {
			foreach ($ds as $entry) {
				if (is_file($entry)) {
					$files[] = $entry;
				}

				if (is_dir($entry)) {
					$rs = self::file_tree($entry);

					foreach ($rs as $f) {
						$files[] = $f;
					}
				}
			}
		}

		return $files;
	}

	static public function file_mkdirs($path)
	{
		if (!is_dir($path)) {
			self::file_mkdirs(dirname($path));
			mkdir($path);
		}

		return is_dir($path);
	}

	static public function file_delete_all($path, $delall = '')
	{
		$op = dir($path);

		while (false != ($item = $op->read())) {
			if ($item == '.' || $item == '..') {
				continue;
			}

			if (is_dir($op->path . '/' . $item)) {
				self::file_delete_all($op->path . '/' . $item);
				rmdir($op->path . '/' . $item);
			}
			else {
				unlink($op->path . '/' . $item);
			}
		}

		if ($delall == 1) {
			rmdir($path);
		}
	}

	static public function file_findphp($path)
	{
		$up_filestree = self::file_tree($path);
		$upgrade = array();

		foreach ($up_filestree as $sf) {
			$file_bs = substr($sf, -3);

			if ($file_bs == 'php') {
				$upgrade[] = array('path' => str_replace($path . '/', '', $sf), 'fullpath' => $sf);
			}
		}

		return $upgrade;
	}

	static public function file_rm_empty_dir($path)
	{
		if (is_dir($path) && ($handle = opendir($path)) !== false) {
			while (($file = readdir($handle)) !== false) {
				if ($file != '.' && $file != '..') {
					$curfile = $path . '/' . $file;

					if (is_dir($curfile)) {
						self::file_rm_empty_dir($curfile);

						if (count(scandir($curfile)) == 2) {
							rmdir($curfile);
						}
					}
				}
			}

			closedir($handle);
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
