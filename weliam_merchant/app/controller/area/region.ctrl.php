<?php
//dezend by http://www.sucaihuo.com/
class Region_WeliamController
{
	public function select_region()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '切换城市 - ' . $_W['wlsetting']['base']['name'] : '切换城市';
		$backurl = urldecode($_GPC['backurl']);

		if ($_W['wlsetting']['areaset']['location'] == 1) {
			header('location: ' . h5_url('pages/mainPages/city/selectAddress', array('id' => $_W['citycode'], 'url' => $backurl)));
			exit();
		}

		extract($this->get_terarea());
		$areagroup = pdo_getall(PDO_NAME . 'areagroup', array('uniacid' => $_W['uniacid']), array(), '', 'sort DESC');

		if (count($terarea) == 1) {
			if ($backurl) {
				header('location: ' . $backurl . '&areaid=' . $terarea[0]['areaid']);
			}
			else {
				header('location: ' . app_url('dashboard/home/index', array('areaid' => $terarea[0]['areaid'])));
			}

			exit();
		}

		isetcookie('__wl_storeid', 0, -100);
		isetcookie('__wl_storeaid', 0, -100);
		$search = trim($_GPC['search']);

		if ($search) {
			$searchArea = pdo_fetchall('SELECT b.areaid,a.name FROM ' . tablename(PDO_NAME . 'area') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'oparea') . (' b ON a.id = b.areaid WHERE b.status = 1 AND b.uniacid = ' . $_W['uniacid'] . ' AND a.name LIKE \'%' . $search . '%\' ORDER BY b.id ASC '));
		}

		$searchurl = app_url('area/region/select_region');
		include wl_template('area/select_region');
	}

	private function get_terarea()
	{
		global $_W;
		$areas = Cache::getCache('area', 'terarea' . $_W['uniacid']);
		if (empty($areas) || !is_array($areas)) {
			$terarea = pdo_getall(PDO_NAME . 'oparea', array('uniacid' => $_W['uniacid'], 'status' => 1), '', '', 'sort DESC,aid ASC');

			foreach ($terarea as $key => &$val) {
				$name = pdo_getcolumn(PDO_NAME . 'area', array('id' => $val['areaid']), 'name');

				if ($val['ishot'] == 1) {
					$address_tree[$key] = array('areaid' => $val['areaid'], 'name' => $name);
				}

				$val['name'] = $name;
			}

			$areas = array('terarea' => $terarea, 'address_tree' => $address_tree);
			Cache::setCache('area', 'terarea' . $_W['uniacid'], $areas);
		}

		return $areas;
	}

	/**
     * Comment: 定位用户所在城市  判断用户当前选择区域是否为其所在城市
     * Author: zzw
     * Date: 2019/7/3 11:26
     */
	public function get_area()
	{
		global $_W;
		global $_GPC;
		$data['list'] = Area::get_all_area(1);
		$data['location'] = $_W['wlsetting']['areaset']['location'];
		exit(json_encode($data));
	}

	public function get_geocoder()
	{
		global $_W;
		global $_GPC;
		$res = ihttp_post('https://apis.map.qq.com/ws/geocoder/v1/', array('location' => $_GPC['lat'] . ',' . $_GPC['lng'], 'key' => $_W['wlsetting']['api']['txmapkey'] ? $_W['wlsetting']['api']['txmapkey'] : 'PSFBZ-MH2WW-QOYRR-O6DJA-NCXQT-WAFQL', 'get_poi' => 1));
		exit($res['content']);
	}

	/**
     * Comment: 精确定位时 用户选择精确地区后的操作
     * Author:
     * Date: 2019/7/2 14:02
     */
	public function get_location()
	{
		global $_W;
		global $_GPC;
		if (empty($_GPC['lat']) || empty($_GPC['lng']) || empty($_GPC['title']) || empty($_GPC['adcode'])) {
			wl_message('缺少重要参数，请查证后重试');
		}

		$params['lat'] = trim($_GPC['lat']);
		$params['lng'] = trim($_GPC['lng']);
		$params['citycode'] = substr($_GPC['adcode'], 0, -2) . '00';
		$params['title'] = trim($_GPC['title']);
		$areainfo = $this->get_area_aid(intval($_GPC['adcode']));
		if (empty($areainfo['areaid']) && !empty($_W['wlsetting']['areaset']['datashow'])) {
			$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $_W['wlsetting']['base']['name'] : '暂未开通';
			$_W['areaname'] = $params['title'];
			$_W['location']['lat'] = $params['lat'];
			$_W['location']['lng'] = $params['lng'];
			$_W['siteurl'] = '';
			include wl_template('area/location');
		}

		$_W['areaid'] = $areainfo['areaid'] ? $areainfo['areaid'] : pdo_getcolumn('wlmerchant_oparea', array('uniacid' => $_W['uniacid'], 'aid' => 0), 'areaid');
		wl_setcookie('agentareaid', $_W['areaid'], 30 * 86400);
		wl_setcookie('locate_information', $params, 30 * 86400);
		$locationurl = $_GPC['url'] ? urldecode($_GPC['url']) : app_url('dashboard/home/index');
		header('location: ' . $locationurl);
	}

	private function get_area_aid($id)
	{
		global $_W;
		$areainfo = (new AreaTable())->getAreaById($id);

		if (empty($areainfo)) {
			return false;
		}

		$agentinfo = (new AgentareaTable())->selectFields(array('aid', 'areaid'))->where(array('uniacid' => $_W['uniacid'], 'areaid' => $id, 'status' => 1))->get();
		if (empty($agentinfo) && !empty($areainfo['pid'])) {
			return $this->get_area_aid($areainfo['pid']);
		}

		return array_merge($areainfo, $agentinfo);
	}

	public function index()
	{
		require_once PATH_MODULE . 'h5/index.html';
	}
}

defined('IN_IA') || exit('Access Denied');

?>
