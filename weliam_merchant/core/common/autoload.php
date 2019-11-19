<?php
//dezend by http://www.sucaihuo.com/
function Weliam_AutoLoad($class_name)
{
	if (strexists($class_name, 'Table')) {
		$file = PATH_CORE . 'table/' . lcfirst(str_replace('Table', '', $class_name)) . '.table.php';

		if (file_exists($file)) {
			require_once $file;
			return true;
		}
	}

	if (in_array($class_name, array('CURLFile')) || stripos($class_name, 'OSS') !== false || stripos($class_name, 'qcloudcos') !== false || stripos($class_name, 'Qiniu') !== false) {
		return false;
	}

	$file = PATH_CORE . 'model/' . $class_name . '.mod.php';

	if (!file_exists($file)) {
		$file = PATH_CORE . 'class/' . $class_name . '.class.php';
	}

	if (!file_exists($file)) {
		$file = PATH_PLUGIN . strtolower($class_name) . '/' . $class_name . '.mod.php';
	}

	if (!file_exists($file)) {
		trigger_error('访问的类 ' . $class_name . ' 不存在.');
	}

	require_once $file;
	return true;
}

spl_autoload_register('Weliam_AutoLoad');

?>
