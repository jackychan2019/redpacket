<?php
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT."/addons/weliam_merchant/core/common/defines.php";
require_once PATH_CORE."common/autoload.php";
Func_loader::core('global');

class Weliam_merchantModule extends WeModule {
	public function welcomeDisplay() {
		global $_W, $_GPC;
		$type = pdo_getcolumn('account', array('uniacid' => $_W['uniacid']), 'type');
		if ($type == 4) {
			$has = pdo_getcolumn(PDO_NAME . 'wxapp_relation', array('wxapp_uniacid' => $_W['uniacid']), 'uniacid');
			if (empty($has)) {
				die('小程序还未关联公众号，请关联以后再操作');
			}
			header('location: ' . url('account/display/switch', array('module_name' => MODULE_NAME, 'uniacid' => $has, 'type' => 1)));
			exit();
		}
		header('location: ' . web_url('dashboard/dashboard/index'));
		exit();
	}
}