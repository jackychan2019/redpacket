<?php
//dezend by http://www.sucaihuo.com/
class BasicSetting_WeliamController
{
	public function basicsetting()
	{
		global $_W;
		global $_GPC;
		$data = Setting::agentsetting_read('pocket');
		$distri = Setting::wlsetting_read('distribution');
		$blacklist = pdo_getall('wlmerchant_pocket_blacklist', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));

		foreach ($blacklist as $key => &$black) {
			$black['avatar'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $black['mid']), 'avatar');
			$black['nickname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $black['mid']), 'nickname');
		}

		if ($data['noticeopenid']) {
			$noticename = pdo_getcolumn(PDO_NAME . 'member', array('openid' => $data['noticeopenid']), 'nickname');
		}

		if (checksubmit('submit')) {
			$data = $_GPC['data'];
			$status = $_GPC['status'];
			$search = $_GPC['search'];
			$pass = $_GPC['passstatus'];
			$free = $_GPC['freestatus'];
			$auto = $_GPC['automobile'];
			$listorder = $_GPC['listorder'];
			$data['search_float'] = $_GPC['search_float'];
			$data['search_bgColor'] = $_GPC['search_bgColor'];
			$data['search'] = $search;
			$data['status'] = $status;
			$data['passstatus'] = $pass;
			$data['freestatus'] = $free;
			$data['locastatus'] = $_GPC['locastatus'];
			$data['automobile'] = $auto;
			$data['listorder'] = $listorder;
			$day = $_GPC['day'];
			$price = $_GPC['price'];
			$paramids = array();
			$len = count($day);
			$k = 0;

			while ($k < $len) {
				$paramids[$k]['day'] = $day[$k];
				$paramids[$k]['price'] = $price[$k];
				++$k;
			}

			$data['price'] = $paramids;
			Setting::agentsetting_save($data, 'pocket');
			wl_message('设置成功', web_url('pocket/BasicSetting/basicsetting'));
		}

		include wl_template('pocket/basicsetting');
	}

	public function entranceinfo()
	{
		global $_W;
		global $_GPC;
		$url = app_url('pocket/pocket/index');
		include wl_template('pocket/entranceinfo');
	}

	public function qrcodeimg()
	{
		global $_W;
		global $_GPC;
		$url = $_GPC['url'];
		m('qrcode/QRcode')->png($url, false, QR_ECLEVEL_H, 4);
	}

	public function dayandprice()
	{
		include wl_template('pocket/dayandprice');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
