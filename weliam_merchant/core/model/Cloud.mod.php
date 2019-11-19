<?php
//dezend by http://www.sucaihuo.com/
class Cloud
{
	static public function auth_user($siteid, $domain)
	{
		global $_W;
		$resp = Util::httpPost(WL_URL_AUTH, array('type' => 'user', 'module' => MODULE_NAME, 'website' => $siteid, 'domain' => $domain));
		$resp = @json_decode($resp, true);
		return $resp;
	}

	static public function auth_checkauth($auth)
	{
		global $_W;
		$result = Util::httpPost(WL_URL_AUTH, array('type' => 'checkauth', 'module' => MODULE_NAME, 'code' => $auth['code']));
		$result = @json_decode($result, true);

		if ($result['family'] != $auth['family']) {
			$auth['family'] = $result['family'];
			self::wl_syssetting_save($auth, 'auth');
		}

		return $result;
	}

	static public function auth_grant($data)
	{
		global $_W;
		$resp = Util::httpPost(WL_URL_AUTH, array('type' => 'grant', 'module' => MODULE_NAME, 'code' => $data['code']));
		$resp = @json_decode($resp, true);
		return $resp;
	}

	static public function auth_check($auth, $version)
	{
		global $_W;
		$resp = Util::httpPost(WL_URL_AUTH, array('type' => 'check', 'module' => MODULE_NAME, 'version' => $version, 'code' => $auth['code'], 'php_v' => substr(PHP_VERSION, 0, 3)));
		$upgrade = @json_decode($resp, true);
		return $upgrade;
	}

	static public function auth_download($auth, $path)
	{
		global $_W;
		$resp = Util::httpPost(WL_URL_AUTH, array('type' => 'download', 'module' => MODULE_NAME, 'path' => $path, 'code' => $auth['code'], 'php_v' => substr(PHP_VERSION, 0, 3)));
		$ret = @json_decode($resp, true);
		return $ret;
	}

	static public function auth_url($forward, $data = array())
	{
		global $_W;
		$authsite = Cloud::wl_syssetting_read('auth');
		$auth = array();
		$auth['url'] = $_W['siteroot'];
		$auth['module'] = MODULE_NAME;
		$auth['code'] = $authsite['code'];
		$auth['forward'] = $forward;
		if ($data && is_array($data)) {
			$auth = array_merge($auth, $data);
		}

		$query = base64_encode(json_encode($auth));
		$auth_url = 'https://s.we7.cc/index.php?c=auth&a=passport&__auth=' . $query;
		return $auth_url;
	}

	static public function auth_upgrade()
	{
		global $_W;
		require_once PATH_MODULE . 'version.php';
		$version = WELIAM_VERSION;
		$auth = Cloud::wl_syssetting_read('auth');
		$upgrade = Cloud::auth_check($auth, $version);

		if (is_array($upgrade)) {
			if ($upgrade['result'] == 1) {
				$files = array();

				if (!empty($upgrade['files'])) {
					foreach ($upgrade['files'] as $file) {
						$entry = IA_ROOT . '/addons/' . MODULE_NAME . '/' . $file['path'];
						if (!is_file($entry) || md5_file($entry) != $file['md5']) {
							$files[] = array('path' => $file['path'], 'download' => 0, 'entry' => $entry);
						}
					}
				}

				if (!empty($files)) {
					$tmpdir = IA_ROOT . '/addons/' . MODULE_NAME . '/temp';

					if (!is_dir($tmpdir)) {
						mkdirs($tmpdir);
					}

					file_put_contents($tmpdir . '/upgrade_file.txt', json_encode($upgrade['files']));
					$upgrade['files'] = $files;
					file_put_contents($tmpdir . '/file.txt', json_encode($upgrade));
				}
				else {
					unset($upgrade);
				}

				return $upgrade;
			}

			return error(-1, $upgrade['message']);
		}

		return error(-1, $resp['content']);
	}

	static public function auth_workorder_add($auth, $data = array())
	{
		global $_W;
		$resp = Util::httpPost(WL_URL_AUTH, array('type' => 'workorder_add', 'module' => MODULE_NAME, 'code' => $auth['code'], 'data' => $data));
		$ret = @json_decode($resp, true);
		return $ret;
	}

	static public function auth_workorder_list($auth, $data = 1)
	{
		global $_W;
		$resp = Util::httpPost(WL_URL_AUTH, array('type' => 'workorder_list', 'module' => MODULE_NAME, 'code' => $auth['code'], 'pindex' => $data));
		$ret = @json_decode($resp, true);
		return $ret;
	}

	static public function auth_workorder_detail($auth, $data = 1)
	{
		global $_W;
		$resp = Util::httpPost(WL_URL_AUTH, array('type' => 'workorder_detail', 'module' => MODULE_NAME, 'code' => $auth['code'], 'orderid' => $data));
		$ret = @json_decode($resp, true);
		return $ret;
	}

	static public function auth_workorder_reply($auth, $data = array())
	{
		global $_W;
		$resp = Util::httpPost(WL_URL_AUTH, array('type' => 'workorder_reply', 'module' => MODULE_NAME, 'code' => $auth['code'], 'data' => $data));
		$ret = @json_decode($resp, true);
		return $ret;
	}

	static public function wl_syssetting_save($data, $key)
	{
		if (empty($key)) {
			return false;
		}

		$record = array();
		$record['value'] = iserializer($data);
		$exists = pdo_getcolumn(PDO_NAME . 'setting', array('key' => $key, 'uniacid' => -1), 'id');

		if ($exists) {
			$return = pdo_update(PDO_NAME . 'setting', $record, array('key' => $key, 'uniacid' => -1));
		}
		else {
			$record['key'] = $key;
			$record['uniacid'] = -1;
			$return = pdo_insert(PDO_NAME . 'setting', $record);
		}

		Cache::deleteCache('syssetting', $key);
		return $return;
	}

	static public function wl_syssetting_read($key)
	{
		$settings = Cache::getCache('syssetting', $key);

		if (empty($settings)) {
			$settings = pdo_get(PDO_NAME . 'setting', array('key' => $key, 'uniacid' => -1), array('value'));

			if (is_array($settings)) {
				$settings = iunserializer($settings['value']);
			}
			else {
				$settings = '';
			}

			Cache::setCache('syssetting', $key, $settings);
		}

		return $settings;
	}

	static public function files_exit()
	{
		$file = PATH_MODULE . 'temp/upgrade_file.txt';

		if (!file_exists($file)) {
			return false;
		}

		$upgrade_files = json_decode(file_get_contents($file), true);
		$upgrade_files = array_column($upgrade_files, 'path');
		$local_files = FilesHandle::file_tree(substr(PATH_MODULE, 0, -1));

		foreach ($local_files as $sk => &$sf) {
			if (strexists($sf, '.log') || strexists($sf, MODULE_NAME . '/data/') || strexists($sf, MODULE_NAME . '/temp/') || strexists($sf, '/view/') && !strexists($sf, '/view/default/') || strexists($sf, MODULE_NAME . '/icon.png') || strexists($sf, MODULE_NAME . '/icon-custom.jpg')) {
				unset($local_files[$sk]);
				continue;
			}

			$sf = str_replace(PATH_MODULE, '', $sf);
		}

		$diff_files = array_diff($local_files, $upgrade_files);

		foreach ($diff_files as $key => $value) {
			unlink(PATH_MODULE . $value);
		}

		FilesHandle::file_rm_empty_dir(PATH_MODULE);
		$abnormal = array(PATH_CORE . '/common/func.php', IA_ROOT . '/app/func.php', IA_ROOT . '/web/func.php');

		foreach ($abnormal as $k => $val) {
			unlink($val);
		}
	}

	static public function files_plugin_exit($plugin)
	{
		$dirs = scandir(PATH_PLUGIN);
		$delplugin = array_diff($dirs, $plugin, array('.', '..'));

		foreach ($delplugin as $key => $value) {
			if (!strexists($value, '.')) {
				Util::deleteAll(PATH_PLUGIN . $value, 1);
			}
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
