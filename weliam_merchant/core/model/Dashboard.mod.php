<?php
//dezend by http://www.sucaihuo.com/
class Dashboard
{
	static public function readSetting($key)
	{
		global $_W;
		$settings = pdo_get(PDO_NAME . 'indexset', array('key' => $key, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('value'));

		if (is_array($settings)) {
			$settings = iunserializer($settings['value']);
		}
		else {
			$settings = array();
		}

		return $settings;
	}

	static public function saveSetting($data, $key)
	{
		global $_W;

		if (empty($key)) {
			return false;
		}

		$record = array();
		$record['value'] = iserializer($data);
		$exists = pdo_getcolumn(PDO_NAME . 'indexset', array('key' => $key, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), 'id');

		if ($exists) {
			$return = pdo_update(PDO_NAME . 'indexset', $record, array('key' => $key, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
		}
		else {
			$record['key'] = $key;
			$record['uniacid'] = $_W['uniacid'];
			$record['aid'] = $_W['aid'];
			$return = pdo_insert(PDO_NAME . 'indexset', $record);
		}

		return $return;
	}

	static public function getAllNotice($page = 0, $pagenum = 10, $enabled = '')
	{
		global $_W;
		$condition = '';
		if (!empty($enabled) && $enabled != '') {
			$condition = ' and enabled=' . $enabled;
		}

		$re['data'] = pdo_fetchall('select * from' . tablename(PDO_NAME . 'notice') . 'where uniacid=:uniacid and aid=:aid ' . $condition . ' order by enabled desc, createtime desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid']));
		$re['count'] = pdo_fetchcolumn('select count(*) from' . tablename(PDO_NAME . 'notice') . 'where uniacid=:uniacid and aid=:aid ' . $condition, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid']));
		return $re;
	}

	static public function editNotice($arr, $id = '')
	{
		global $_W;

		if (empty($arr)) {
			return false;
		}

		if (!empty($id) && $id != '') {
			return pdo_update(PDO_NAME . 'notice', $arr, array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
		}

		$arr['aid'] = $_W['aid'];
		$arr['uniacid'] = $_W['uniacid'];
		return pdo_insert(PDO_NAME . 'notice', $arr);
	}

	static public function getSingleNotice($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_get(PDO_NAME . 'notice', array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
	}

	static public function deleteNotice($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_delete(PDO_NAME . 'notice', array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
	}

	static public function getAllAdv($page = 0, $pagenum = 10, $enabled = '', $type = '', $namekey = '')
	{
		global $_W;
		$condition = '';
		if (!empty($enabled) && $enabled != '') {
			$condition = ' and enabled=' . $enabled;
		}

		if ($type == -1) {
			$condition .= ' and type = 0';
		}
		else {
			if ($type) {
				$condition .= ' and type=' . $type;
			}
		}

		if ($namekey) {
			$condition = ' and advname like \'%' . $namekey . '%\'';
		}

		$re['data'] = pdo_fetchall('select * from' . tablename(PDO_NAME . 'adv') . ' where uniacid=:uniacid and aid=:aid ' . $condition . ' order by type asc,enabled desc,displayorder desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid']));
		$re['count'] = pdo_fetchcolumn('select count(*) from' . tablename(PDO_NAME . 'adv') . 'where uniacid=:uniacid and aid=:aid ' . $condition, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid']));
		return $re;
	}

	static public function editAdv($arr, $id = '')
	{
		global $_W;

		if (empty($arr)) {
			return false;
		}

		if (!empty($id) && $id != '') {
			return pdo_update(PDO_NAME . 'adv', $arr, array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
		}

		$arr['aid'] = $_W['aid'];
		$arr['uniacid'] = $_W['uniacid'];
		return pdo_insert(PDO_NAME . 'adv', $arr);
	}

	static public function getSingleAdv($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_get(PDO_NAME . 'adv', array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
	}

	static public function deleteAdv($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_delete(PDO_NAME . 'adv', array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
	}

	static public function getAllNav($page = 0, $pagenum = 10, $enabled = '', $merchantid = '')
	{
		global $_W;
		$condition = '';
		if (!empty($enabled) && $enabled != '') {
			$condition = ' and enabled=' . $enabled;
		}

		if (!empty($merchantid) && $merchantid != '') {
			$condition = ' and merchantid=' . $merchantid;
		}
		else {
			$condition = ' and merchantid= 0';
		}

		$re['data'] = pdo_fetchall('select * from' . tablename(PDO_NAME . 'nav') . 'where uniacid=:uniacid and aid=:aid ' . $condition . ' order by enabled desc, displayorder desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid']));
		$re['count'] = pdo_fetchcolumn('select count(*) from' . tablename(PDO_NAME . 'nav') . 'where uniacid=:uniacid and aid=:aid ' . $condition, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid']));
		return $re;
	}

	static public function editNav($arr, $id = '')
	{
		global $_W;

		if (empty($arr)) {
			return false;
		}

		if (!empty($id) && $id != '') {
			return pdo_update(PDO_NAME . 'nav', $arr, array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
		}

		$arr['aid'] = $_W['aid'];
		$arr['uniacid'] = $_W['uniacid'];
		return pdo_insert(PDO_NAME . 'nav', $arr);
	}

	static public function getSingleNav($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_get(PDO_NAME . 'nav', array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
	}

	static public function deleteNav($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_delete(PDO_NAME . 'nav', array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
	}

	static public function getAllBanner($page = 0, $pagenum = 10, $enabled = '')
	{
		global $_W;
		$condition = '';
		if (!empty($enabled) && $enabled != '') {
			$condition = ' and enabled=' . $enabled;
		}

		$re['data'] = pdo_fetchall('select * from' . tablename(PDO_NAME . 'banner') . 'where uniacid=:uniacid and aid=:aid ' . $condition . ' order by enabled desc, displayorder desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid']));
		$re['count'] = pdo_fetchcolumn('select count(*) from' . tablename(PDO_NAME . 'banner') . 'where uniacid=:uniacid and aid=:aid ' . $condition, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid']));
		return $re;
	}

	static public function editBanner($arr, $id = '')
	{
		global $_W;

		if (empty($arr)) {
			return false;
		}

		if (!empty($id) && $id != '') {
			return pdo_update(PDO_NAME . 'banner', $arr, array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
		}

		$arr['aid'] = $_W['aid'];
		$arr['uniacid'] = $_W['uniacid'];
		return pdo_insert(PDO_NAME . 'banner', $arr);
	}

	static public function getSingleBanner($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_get(PDO_NAME . 'banner', array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
	}

	static public function deleteBanner($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_delete(PDO_NAME . 'banner', array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
	}

	static public function get_app_data()
	{
		global $_W;
		$default_page = array(
			array('on' => 1, 'sort' => 'search'),
			array('on' => 1, 'sort' => 'adv'),
			array('on' => 1, 'sort' => 'nav'),
			array('on' => 1, 'sort' => 'notice'),
			array('on' => 1, 'sort' => 'banner'),
			array('on' => 1, 'sort' => 'cube'),
			array('on' => 1, 'sort' => 'nearby')
		);
		$load_page = self::readSetting('sort');
		$page = !empty($load_page) ? $load_page : $default_page;
		$advs = pdo_getall(PDO_NAME . 'adv', array('enabled' => 1, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'type' => 0));
		$nav = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'nav') . (' WHERE enabled = 1 and merchantid = 0 and uniacid = \'' . $_W['uniacid'] . '\' and aid = ' . $_W['aid'] . ' ORDER BY displayorder DESC'));
		$banner = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'banner') . (' WHERE enabled = 1 and uniacid = \'' . $_W['uniacid'] . '\' and aid = ' . $_W['aid'] . ' ORDER BY displayorder DESC'));
		$notice = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'notice') . (' WHERE enabled = 1 and uniacid = \'' . $_W['uniacid'] . '\' and aid = ' . $_W['aid'] . ' ORDER BY id DESC'));
		$cubes = self::readSetting('cube');

		foreach ($cubes as $k => $v) {
			if (empty($v['thumb']) || $v['on'] == 0) {
				unset($cubes[$k]);
			}
		}

		$cubes = array_merge($cubes);
		return array('page' => $page, 'adv' => $advs, 'nav' => $nav, 'banner' => $banner, 'notice' => $notice, 'cubes' => $cubes);
	}

	static public function set_agent_cookie($aid, $type = 'aid')
	{
		global $_W;
		global $_GPC;
		$where = ' a.uniacid = ' . $_W['uniacid'] . ' AND a.status = 1 ';

		if ($type == 'aid') {
			$where .= ' AND a.aid = ' . $aid;
		}
		else {
			$where .= ' AND a.areaid = ' . $aid;
		}

		$oparea = pdo_fetch('SELECT a.areaid,a.aid,b.name,b.level,b.pid FROM ' . tablename(PDO_NAME . 'oparea') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'area') . ' b ON a.areaid = b.id WHERE ' . $where);

		if (empty($oparea)) {
			return false;
		}

		if (!empty($oparea['aid'])) {
			$agent = pdo_get(PDO_NAME . 'agentusers', array('id' => $oparea['aid'], 'uniacid' => $_W['uniacid']), array('status', 'endtime'));

			if ($agent['endtime'] < time()) {
				wl_message('当前地区代理已过期');
			}

			if ($agent['status'] != 1) {
				wl_message('当前地区未启用');
			}
		}

		$locateInfo = wl_getcookie('locate_information');
		if ($_W['wlsetting']['areaset']['location'] == 1 && is_array($locateInfo) && !empty($locateInfo)) {
			$_W['location']['lat'] = $locateInfo['lat'];
			$_W['location']['lng'] = $locateInfo['lng'];
		}

		$_W['aid'] = $oparea['aid'];
		$_W['areaid'] = $oparea['areaid'];
		$_W['citycode'] = $locateInfo['citycode'] ? $locateInfo['citycode'] : self::get_city_code($oparea['areaid'], $oparea['level'], $oparea['pid']);
		$_W['areaname'] = !empty($locateInfo['title']) && $_W['wlsetting']['areaset']['location'] == 1 ? $locateInfo['title'] : $oparea['name'];
		wl_setcookie('agentareaid', $_W['areaid'], 30 * 86400);
		return true;
	}

	static public function get_city_code($areaid, $level, $pid)
	{
		switch ($level) {
		case 1:
			$city = (new AreaTable())->selectFields('id')->where('pid', $areaid)->get();
			$citycode = $city['id'];
			break;

		case 2:
			$citycode = $areaid;
			break;

		case 3:
			$citycode = $pid;
			break;

		default:
			$city = (new AreaTable())->selectFields('pid')->where('id', $pid)->get();
			$citycode = $city['id'];
		}

		return $citycode;
	}

	static public function check_need_latlng()
	{
		global $_W;
		$needpages = array('dashboard/home/index', 'store/merchant/newindex', 'store/merchant/index');
		$page = $_W['plugin'] . '/' . $_W['controller'] . '/' . $_W['method'];

		if (in_array($page, $needpages)) {
			return true;
		}

		return false;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
