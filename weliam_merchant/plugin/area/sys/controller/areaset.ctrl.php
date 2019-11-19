<?php
//dezend by http://www.sucaihuo.com/
class Areaset_WeliamController
{
	public function setting()
	{
		global $_W;
		global $_GPC;
		$settings = Setting::wlsetting_read('areaset');

		if (checksubmit('submit')) {
			$base = array('location' => intval($_GPC['location']), 'datashow' => intval($_GPC['datashow']));
			Setting::wlsetting_save($base, 'areaset');
			wl_message('更新设置成功！', web_url('area/areaset/setting'));
		}

		include wl_template('area/areasetting');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
