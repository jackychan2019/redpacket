<?php
//dezend by http://www.sucaihuo.com/
class Setting_WeliamController
{
	public function base()
	{
		global $_W;
		global $_GPC;

		if (checksubmit('submit')) {
			$base = array('storepid' => intval($_GPC['storepid']), 'rushpid' => intval($_GPC['rushpid']), 'cardpid' => intval($_GPC['cardpid']), 'distpid' => intval($_GPC['distpid']), 'grouponpid' => intval($_GPC['grouponid']), 'fgrouppid' => intval($_GPC['fgrouppid']), 'bargainid' => intval($_GPC['bargainid']), 'h5_poster' => intval($_GPC['h5_poster']), 'wxapp_poster' => intval($_GPC['wxapp_poster']), 'dis_poster' => intval($_GPC['dis_poster']));
			Setting::wlsetting_save($base, 'diyposter');
			Tools::clearwxapp();
			Tools::clearposter();
			wl_message('更新设置成功！', web_url('diyposter/setting/base'));
		}

		$settings = Setting::wlsetting_read('diyposter');
		$allposter = pdo_getall(PDO_NAME . 'poster', array('uniacid' => $_W['uniacid']), array('title', 'type', 'id'));
		$store = $rush = $card = $dist = array();

		foreach ($allposter as $key => $value) {
			if ($value['type'] == 1) {
				$store[] = $value;
			}
			else if ($value['type'] == 2) {
				$rush[] = $value;
			}
			else if ($value['type'] == 3) {
				$card[] = $value;
			}
			else if ($value['type'] == 4) {
				$dist[] = $value;
			}
			else if ($value['type'] == 5) {
				$groupon[] = $value;
			}
			else if ($value['type'] == 6) {
				$fightgroup[] = $value;
			}
			else {
				if ($value['type'] == 7) {
					$bargain[] = $value;
				}
			}
		}

		include wl_template('poster/setting');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
