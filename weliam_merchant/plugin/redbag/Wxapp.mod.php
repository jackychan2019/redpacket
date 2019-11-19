<?php
//dezend by http://www.sucaihuo.com/
class Wxapp
{
	static public function get_uniacid($wxapp_uniacid = '')
	{
		global $_W;
		global $_GPC;
		$wxapp_uniacid = $wxapp_uniacid ? $wxapp_uniacid : $_W['uniacid'];
		return pdo_getcolumn(PDO_NAME . 'wxapp_relation', array('wxapp_uniacid' => $wxapp_uniacid), 'uniacid');
	}

	static public function get_wxapp_uniacid($uniacid = '')
	{
		global $_W;
		global $_GPC;
		$uniacid = $uniacid ? $uniacid : $_W['uniacid'];
		return pdo_getcolumn(PDO_NAME . 'wxapp_relation', array('uniacid' => $uniacid), 'wxapp_uniacid');
	}

	static public function get_wxapp_qrcode($scene, $name, $logo = '')
	{
		global $_W;
		$path = IA_ROOT . '/addons/' . MODULE_NAME . '/data/wxapp/' . $_W['uniacid'] . '/';

		if (!is_dir($path)) {
			load()->func('file');
			mkdirs($path);
		}

		$filename = $path . $name . '.png';

		if (!is_file($filename)) {
			$wxapp_uniacid = self::get_wxapp_uniacid($_W['uniacid']);

			if (empty($wxapp_uniacid)) {
				return error(1, '公众号未关联小程序');
			}

			$account_api = WeAccount::create(pdo_getcolumn('account', array('uniacid' => $wxapp_uniacid), 'acid'));
			$response = $account_api->getCodeUnlimit($scene);

			if (is_error($response)) {
				return error(1, $response['errno'] . $response['message']);
			}

			if (!empty($logo)) {
				set_time_limit(0);
				@ini_set('memory_limit', '256M');
				$target = imagecreatetruecolor(430, 430);
				imagecopy($target, imagecreatefromstring($response), 0, 0, 0, 0, 430, 430);
				$logobg = imagecreatetruecolor(200, 200);
				$img = Tools::createImage($logo);
				$w = imagesx($img);
				$h = imagesy($img);
				$needw = $h <= $w ? $h : $w;
				imagecopyresized($logobg, $img, 0, 0, 0, 0, 200, 200, $needw, $needw);
				imagecopyresized($target, Tools::imageRadius($logobg, true), 115, 115, 0, 0, 200, 200, 200, 200);
				imagepng($target, $filename);
				imagedestroy($target);
			}
			else {
				file_put_contents($filename, $response);
			}
		}

		return true;
	}

	static public function get_store_qrcode($id)
	{
		global $_W;
		$name = md5($id . 'storeid' . $_W['mid']);
		$logo = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $id), 'logo');
		$response = self::get_wxapp_qrcode('store#id=' . $id . '#invitid=' . $_W['mid'], $name, tomedia($logo));

		if (is_error($response)) {
			return $response;
		}

		return '../addons/' . MODULE_NAME . '/data/wxapp/' . $_W['uniacid'] . '/' . $name . '.png';
	}
}

defined('IN_IA') || exit('Access Denied');

?>
