<?php
//dezend by http://www.sucaihuo.com/
class Func_loader
{
	static public function core($name)
	{
		global $_W;

		if (isset($_W['wlfunc'][$name])) {
			return true;
		}

		$file = PATH_CORE . 'function/' . $name . '.func.php';

		if (file_exists($file)) {
			include_once $file;
			$_W['wlfunc'][$name] = true;
			return true;
		}

		trigger_error('Invalid Helper Function ' . PATH_CORE . 'function/' . $name . '.func.php', 256);
		return false;
	}

	static public function sys($name)
	{
		global $_W;

		if (isset($_W['wlsys'][$name])) {
			return true;
		}

		$file = PATH_SYS . 'common/' . $name . '.func.php';

		if (file_exists($file)) {
			include_once $file;
			$_W['wlsys'][$name] = true;
			return true;
		}

		trigger_error('Invalid Sys Helper ' . PATH_SYS . 'common/' . $name . '.func.php', 256);
		return false;
	}

	static public function web($name)
	{
		global $_W;

		if (isset($_W['wlweb'][$name])) {
			return true;
		}

		$file = PATH_WEB . 'common/' . $name . '.func.php';

		if (file_exists($file)) {
			include_once $file;
			$_W['wlweb'][$name] = true;
			return true;
		}

		trigger_error('Invalid Web Helper ' . PATH_WEB . 'common/' . $name . '.func.php', 256);
		return false;
	}

	static public function app($name)
	{
		global $_W;

		if (isset($_W['wlapp'][$name])) {
			return true;
		}

		$file = PATH_APP . 'common/' . $name . '.func.php';

		if (file_exists($file)) {
			include_once $file;
			$_W['wlapp'][$name] = true;
			return true;
		}

		trigger_error('Invalid App Function ' . PATH_APP . 'common/' . $name . '.func.php', 256);
		return false;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
