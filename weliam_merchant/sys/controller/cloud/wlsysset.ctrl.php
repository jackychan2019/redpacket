<?php
//dezend by http://www.sucaihuo.com/
class Wlsysset_WeliamController
{
	public function taskcover()
	{
		global $_W;
		global $_GPC;
		$settings['url'] = $_W['siteroot'] . 'addons/weliam_merchant/core/common/task.php';
		$settings['name'] = '计划任务入口';
		$lock = cache_read(MODULE_NAME . ':task:status');

		if (empty($lock)) {
			$status = 1;
		}
		else if ($lock['expire'] < time() - 600) {
			$status = 3;
		}
		else {
			$status = 2;
		}

		include wl_template('cloud/taskcover');
	}

	public function base()
	{
		global $_W;
		global $_GPC;
		$settings = Cloud::wl_syssetting_read('base');

		if (checksubmit('submit')) {
			$base = array('name' => $_GPC['name'], 'logo' => $_GPC['logo'], 'copyright' => $_GPC['copyright']);
			Cloud::wl_syssetting_save($base, 'base');
			wl_message('更新设置成功！', web_url('cloud/wlsysset/base'));
		}

		include wl_template('cloud/base');
	}

	public function restartqueen()
	{
		global $_W;
		global $_GPC;
		$queue = new Queue();
		$queue->deleteLock();
		show_json(1);
	}
}

defined('IN_IA') || exit('Access Denied');

?>
