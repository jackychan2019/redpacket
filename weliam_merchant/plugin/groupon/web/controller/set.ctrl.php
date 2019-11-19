<?php
//dezend by http://www.sucaihuo.com/
class Set_WeliamController
{
	public function entry()
	{
		global $_W;
		global $_GPC;
		$settings['name'] = '团购入口';
		$settings['url'] = app_url('groupon/grouponapp/grouponlist', array('aid' => $_W['aid']));
		include wl_template('grouponset/entry');
	}

	public function base()
	{
		global $_W;
		global $_GPC;
		$base = Setting::agentsetting_read('groupon');
		$rushgoods = pdo_fetchall('SELECT id,name FROM ' . tablename('wlmerchant_rush_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' and aid = ' . $_W['aid'] . ' and status IN (1,2) ORDER BY id DESC'));

		if ($base['rushids']) {
			$base['rushids'] = unserialize($base['rushids']);
		}

		$cubes = Dashboard::readSetting('grouponcube');

		if (empty($cubes)) {
			$cubes = array(
				array(''),
				array(''),
				array(''),
				array(''),
				array(''),
				array('')
			);
		}

		if (checksubmit('submit')) {
			$data_img = $_GPC['data_img'];
			$data_url = $_GPC['data_url'];
			$paramids = array();
			$len = count($data_img);
			$k = 0;

			while ($k < $len) {
				if (!empty($data_img[$k])) {
					$paramids[$k]['data_img'] = $data_img[$k];
					$paramids[$k]['data_url'] = $data_url[$k];
				}

				++$k;
			}

			$base = $_GPC['base'];
			$base['content'] = $paramids;
			$base['rushids'] = $_GPC['rushids'];

			if ($base['rushids']) {
				$base['rushids'] = serialize($base['rushids']);
			}

			$res1 = Setting::agentsetting_save($base, 'groupon');
			$cubes_thumbs = $_GPC['cubes_thumbs'];
			$cubes_links = $_GPC['cubes_links'];
			$checkbox = $_GPC['on'];
			$new_arr = array();
			$i = 0;

			while ($i < 6) {
				$new_arr[$i]['thumb'] = $cubes_thumbs[$i];
				$new_arr[$i]['link'] = trim($cubes_links[$i]);
				if (!empty($checkbox) && in_array($i, $checkbox)) {
					$new_arr[$i]['on'] = 1;
				}
				else {
					$new_arr[$i]['on'] = 0;
				}

				++$i;
			}

			Dashboard::saveSetting($new_arr, 'grouponcube');
			wl_message('保存设置成功！', referer(), 'success');
		}

		include wl_template('grouponset/base');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
