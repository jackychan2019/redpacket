<?php
//dezend by http://www.sucaihuo.com/
class Cache
{
	static public function getDateByCacheFirst($key, $name, $funcname, $valuearray)
	{
		$data = self::getCache($key, $name);

		if (empty($data)) {
			$data = call_user_func_array($funcname, $valuearray);
			self::setCache($key, $name, $data);
		}

		return $data;
	}

	static public function getCache($key, $name)
	{
		global $_W;
		if (empty($key) || empty($name)) {
			return false;
		}

		return cache_read(MODULE_NAME . ':' . $_W['uniacid'] . ':' . $key . ':' . $name);
	}

	static public function setCache($key, $name, $value)
	{
		global $_W;
		if (empty($key) || empty($name)) {
			return false;
		}

		return cache_write(MODULE_NAME . ':' . $_W['uniacid'] . ':' . $key . ':' . $name, $value);
	}

	static public function deleteCache($key, $name)
	{
		global $_W;
		if (empty($key) || empty($name)) {
			return false;
		}

		return cache_delete(MODULE_NAME . ':' . $_W['uniacid'] . ':' . $key . ':' . $name);
	}

	static public function deleteThisModuleCache()
	{
		return cache_clean(MODULE_NAME);
	}

	static public function setSingleLockByCache($arr, $time = 15)
	{
		if ($arr == '' || empty($arr) || $arr['single'] == 'table') {
			return false;
		}

		$tableCache = self::getCache($arr['tablename'], 'table');
		if (!empty($tableCache) && time() < $tableCache) {
			return false;
		}

		$singleCache = self::getCache($arr['tablename'], $arr['single']);
		if (!empty($singleCache) && time() < $singleCache) {
			return false;
		}

		return self::setCache($arr['tablename'], $arr['single'], time() + $time);
	}

	static public function setTableLockByCache($arr, $time = 15)
	{
		if ($arr == '' || empty($arr) || $arr['single'] == 'table') {
			return false;
		}

		$tableCache = self::getCache($arr['tablename'], 'table');
		if (!empty($tableCache) && time() < $tableCache) {
			return false;
		}

		return self::setCache($arr['tablename'], 'table', time() + $time);
	}
}

defined('IN_IA') || exit('Access Denied');

?>
