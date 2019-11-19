<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
header('Cache-control: private');
class Supervise_WeliamController extends Weliam_merchantModuleSite
{
	public function __construct()
	{
		global $_W;
		global $_GPC;
		if (!empty($_GPC['__wl_storeid']) || !empty($_GPC['storeid'])) {
			$_W['storeid'] = intval($_GPC['__wl_storeid']) ? intval($_GPC['__wl_storeid']) : intval($_GPC['storeid']);
		}

		if (!empty($_GPC['__wl_storeaid']) || !empty($_GPC['staid'])) {
			$_W['aid'] = intval($_GPC['__wl_storeaid']) ? intval($_GPC['__wl_storeaid']) : intval($_GPC['staid']);
			$_W['agentset'] = Setting::agentsetting_load();
		}

		if (!in_array($_W['method'], array('renewing', 'storerenew', 'storelist', 'information', 'confirmadmin', 'chargeorder', 'submitInformation', 'switchstore'))) {
			$this->checkstoreid();
		}
	}

	public function checkstoreid()
	{
		global $_W;

		if (empty($_W['storeid'])) {
			header('location: ' . app_url('store/supervise/storelist'));
			exit();
		}

		$enabled = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $_W['storeid']), 'enabled');

		if (empty($enabled)) {
			$storeid = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $_W['storeid']), 'id');

			if ($storeid) {
				header('location: ' . app_url('store/storeManage/index'));
			}
			else {
				wl_message('商户已被删除', app_url('store/storeManage/enter'), 'warning');
			}
		}
		else if ($enabled == 4) {
			wl_message('商户已被禁用', app_url('store/storeManage/enter'), 'warning');
		}
		else if ($enabled == 2) {
			wl_message('商户暂停中，请联系管理员', app_url('store/storeManage/enter'), 'warning');
		}
		else {
			if ($enabled == 3) {
				if ($_W['wlsetting']['register']['chargestatus']) {
					wl_message('商户已过期，请联系管理员或续费', app_url('store/supervise/storerenew'), 'warning');
				}
				else {
					wl_message('商户已过期，请联系管理员', app_url('store/storeManage/enter'), 'warning');
				}
			}
		}

		$storeuser = pdo_get(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'storeid' => $_W['storeid'], 'mid' => $_W['mid']), 'ismain');
		if (empty($storeuser) || $storeuser == 2) {
			wl_message('非商户店员无法管理店铺', app_url('store/storeManage/enter'), 'warning');
		}
	}

	public function checkauthority($plugin)
	{
		global $_W;
		$store = Store::getSingleStore($_W['storeid']);
		$authority = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('id' => $store['groupid']), 'authority');
		$authority = unserialize($authority);

		if (!empty($authority)) {
			if (!in_array($plugin, $authority)) {
				wl_message('暂无权限', app_url('store/supervise/platform'), 'warning');
			}
		}
	}

	/**
	 * 店铺列表
	 */
	public function storelist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '店铺列表 - ' . $_W['wlsetting']['base']['name'] : '店铺列表';
		$storeids = pdo_getall(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'ismain !=' => 2), array('storeid', 'id'));

		if (empty($storeids)) {
			wl_message('您还不是商家或账户正在审核中，请先申请入驻平台。', app_url('store/storeManage/enter'), 'warning');
		}

		foreach ($storeids as $key => $value) {
			$store = pdo_get(PDO_NAME . 'merchantdata', array('id' => $value['storeid']));

			if ($store['enabled'] != 4) {
				$store['userid'] = $value['id'];
				$stores[] = $store;
			}
		}

		if (empty($stores)) {
			wl_message('您还不是商家或账户正在审核中，请先申请入驻平台。', app_url('store/storeManage/enter'), 'warning');
		}

		if (count($stores) == 1) {
			header('Location:' . app_url('store/supervise/switchstore', array('storeid' => $stores[0]['id'])));
		}

		if (p('diypage')) {
			if (!empty($_W['agentset']['diypageset']['menu_index'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_index']);
			}
		}

		include wl_template('store/storelist');
	}

	public function switchstore()
	{
		global $_W;
		global $_GPC;
		$storeid = intval($_GPC['storeid']);
		$role = pdo_get(PDO_NAME . 'merchantuser', array('mid' => $_W['mid'], 'storeid' => $storeid), array('id', 'aid'));

		if (empty($role)) {
			wl_message('操作失败, 您没有访问权限.');
		}

		if ($role['aid'] == 0) {
			$role['aid'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $storeid), 'aid');
			pdo_update(PDO_NAME . 'merchantuser', array('aid' => $role['aid']), array('mid' => $_W['mid'], 'storeid' => $storeid));
		}

		isetcookie('__wl_storeid', $storeid, 7 * 86400);
		isetcookie('__wl_storeaid', $role['aid'], 7 * 86400);
		$url = $_GPC['url'] ? urldecode($_GPC['url']) : app_url('store/supervise/platform', array('storeid' => $storeid, 'staid' => $role['aid']));
		header('location: ' . $url);
	}

	public function fans()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '客户管理 - ' . $_W['wlsetting']['base']['name'] : '客户管理';
		$fansnum = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'storefans') . (' WHERE uniacid = \'' . $_W['uniacid'] . '\' and sid = ' . $_W['storeid']));
		$todaynum = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'storefans') . (' WHERE uniacid = \'' . $_W['uniacid'] . '\' and sid = ' . $_W['storeid'] . ' and createtime >= ') . strtotime(date('Y-m-d')));
		include wl_template('store/fans');
	}

	public function platform()
	{
		global $_W;
		global $_GPC;
		$store = Store::getSingleStore($_W['storeid']);
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $store['storename'] . ' - ' . $_W['wlsetting']['base']['name'] : $store['storename'];
		$fansnum = pdo_getcolumn('wlmerchant_storefans', array('sid' => $_W['storeid'], 'uniacid' => $_W['uniacid']), 'COUNT(id)');

		if (empty($store['groupid'])) {
			$store['groupid'] = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'isdefault' => 1), 'id');
			pdo_update('wlmerchant_merchantdata', array('groupid' => $store['groupid']), array('id' => $store['id']));
		}

		$authority = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('id' => $store['groupid']), 'authority');
		$authority = unserialize($authority);
		$halfset = Setting::wlsetting_read('halfcard');
		$halfname = $halfset['text']['halfcardtext'] ? $halfset['text']['halfcardtext'] : '一卡通';
		include wl_template('store/platform');
	}

	public function shopset()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '店铺管理 - ' . $_W['wlsetting']['base']['name'] : '店铺管理';
		$store = Store::getSingleStore($_W['storeid']);
		$_W['wlsetting']['share']['share_title'] = $store['storename'];
		$_W['wlsetting']['share']['share_url'] = app_url('store/merchant/detail', array('id' => $_W['storeid']));
		$_W['wlsetting']['share']['share_image'] = $store['logo'];
		$authority = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('id' => $store['groupid']), 'authority');
		$authority = unserialize($authority);
		$renewflag = pdo_fetch('SELECT * FROM ' . tablename('wlmerchant_chargelist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 AND price > 0 ORDER BY sort DESC'));
		include wl_template('store/shopset');
	}

	public function storerenew()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '店铺续费 - ' . $_W['wlsetting']['base']['name'] : '店铺续费';
		$groupid = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $_W['storeid']), 'groupid');

		if ($groupid) {
			$chargeid = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('id' => $groupid), 'chargeid');

			if ($chargeid) {
				$chargelist = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_chargelist') . ('WHERE id = ' . $chargeid));
			}
		}

		if (empty($chargelist)) {
			$chargelist = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_chargelist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid IN (0,' . $_W['aid'] . ') AND status = 1 AND renewstatus = 1 ORDER BY sort DESC'));
		}

		include wl_template('store/storerenew');
	}

	public function renewing()
	{
		global $_W;
		global $_GPC;
		$typeid = $_GPC['typeid'];

		if (empty($_W['storeid'])) {
			wl_json(1, '无商户ID,请重新选择');
		}

		if ($typeid) {
			$chargetype = pdo_get('wlmerchant_chargelist', array('id' => $typeid));
			$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'aid' => $_W['aid'], 'fkid' => $typeid, 'sid' => $_W['storeid'], 'status' => 0, 'paytype' => 0, 'createtime' => time(), 'orderno' => createUniontid(), 'price' => $chargetype['price'], 'num' => $chargetype['days'], 'plugin' => 'store', 'payfor' => 'merchant');
			$res = pdo_insert(PDO_NAME . 'order', $data);
			$orderid = pdo_insertid();

			if ($orderid) {
				wl_json(0, $orderid);
			}
			else {
				wl_json(1, '创建订单失败，请重试');
			}
		}
		else {
			wl_json(1, '获取续费类型失败，请重试');
		}
	}

	public function storeqr()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '店铺二维码 - ' . $_W['wlsetting']['base']['name'] : '店铺二维码';
		$store = Store::getSingleStore($_W['storeid']);

		if (empty($store['cardsn'])) {
			$showurl = app_url('store/merchant/qrcodeimg', array('url' => app_url('store/merchant/detail', array('id' => $_W['storeid'], 'fansflag' => 1))));
		}
		else {
			$qrid = pdo_getcolumn(PDO_NAME . 'qrcode', array('sid' => $_W['storeid'], 'status' => 2), 'qrid');

			if (empty($qrid)) {
				$res = Storeqr::auto_createqr($_W['storeid']);

				if ($res) {
					$qrid = pdo_getcolumn(PDO_NAME . 'qrcode', array('sid' => $_W['storeid'], 'status' => 2), 'qrid');
					$ticket = pdo_getcolumn('qrcode', array('id' => $qrid), 'ticket');
					$showurl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket);
				}
				else {
					$showurl = app_url('store/merchant/qrcodeimg', array('url' => app_url('store/merchant/detail', array('id' => $_W['storeid']))));
				}
			}
			else {
				$ticket = pdo_getcolumn('qrcode', array('id' => $qrid), 'ticket');
				$showurl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket);
			}
		}

		include wl_template('store/storeqr');
	}

	public function information()
	{
		global $_W;
		global $_GPC;
		$set = Setting::wlsetting_read('register');
		$meroof = Setting::agentsetting_read('meroof');
		if (!empty($_GPC['cardsn']) && !empty($_GPC['salt'])) {
			$qrcode = Storeqr::check_qrcode($_GPC['cardsn'], $_GPC['salt']);

			if (!empty($qrcode['sid'])) {
				wl_message('二维码已绑定商户，请检查二维码是否可用！', 'close', 'warning');
			}
		}

		$applyflag = $_GPC['applyflag'];
		$appstoreid = $_GPC['appstoreid'];
		if ($applyflag || $appstoreid) {
			$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商户入驻 - ' . $_W['wlsetting']['base']['name'] : '商户入驻';
		}
		else {
			$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '店铺信息 - ' . $_W['wlsetting']['base']['name'] : '店铺信息';
			$this->checkstoreid();
		}

		if ($appstoreid) {
			$store = Store::getSingleStore($appstoreid);
			$backurl = app_url('store/supervise/shopset');
		}
		else {
			if ($_GPC['storeid']) {
				$store = Store::getSingleStore($_GPC['storeid']);
				$backurl = app_url('store/supervise/shopset');
			}
		}

		$store['adv'] = unserialize($store['adv']);
		$store['album'] = unserialize($store['album']);
		$store['examineimg'] = unserialize($store['examineimg']);

		if (empty($backurl)) {
			$users = Store::getSingleRegister($_W['mid']);

			if ($users) {
				$backurl = app_url('store/storeManage/index');
			}
			else {
				$backurl = app_url('dashboard/home/index');
			}
		}

		$authority = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('id' => $store['groupid']), 'authority');
		$authority = unserialize($authority);
		$categoryes = pdo_getall(PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'parentid' => 0, 'state' => 0), '', '', 'displayorder DESC');

		if (!empty($categoryes)) {
			$parent = array();

			foreach ($categoryes as $cid => $cate) {
				$ch = pdo_getall(PDO_NAME . 'category_store', array('parentid' => $cate['id'], 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('name'), '', 'displayorder DESC');
				$parent[] = array('name' => $cate['name'], 'sub' => $ch);
			}
		}

		if ($store['onelevel'] && $store['twolevel']) {
			$cateval1 = pdo_getcolumn(PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'id' => $store['onelevel']), 'name');

			if ($store['onelevel'] != $store['twolevel']) {
				$cateval2 = pdo_getcolumn(PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'id' => $store['twolevel']), 'name');
			}
			else {
				$cateval2 = '';
			}

			$cateval = $cateval1 . ' ' . $cateval2;
		}
		else {
			$cateval = $parent[0]['name'] . ' ' . $parent[0]['sub'][0]['name'];
		}

		$store['location'] = unserialize($store['location']);

		if (empty($_W['aid'])) {
			$allArea = Area::get_all_wx_use();
		}
		else {
			$allArea = Area::get_all_in_use(1);
		}

		if ($store['provinceid'] && $store['areaid'] && $store['distid']) {
			$storeaddr1 = pdo_getcolumn(PDO_NAME . 'area', array('id' => $store['provinceid']), 'name');
			$storeaddr2 = pdo_getcolumn(PDO_NAME . 'area', array('id' => $store['areaid']), 'name');
			$storeaddr3 = pdo_getcolumn(PDO_NAME . 'area', array('id' => $store['distid']), 'name');
			$storeaddr = $storeaddr1 . ' ' . $storeaddr2 . ' ' . $storeaddr3;
		}
		else {
			$storeaddr = $allArea[0]['name'] . ' ' . $allArea[0]['sub'][0]['name'] . ' ' . $allArea[0]['sub'][0]['sub'][0]['name'];
		}

		$hours = unserialize($store['storehours']);
		$storehours = $hours ? $hours['startTime'] . ' — ' . $hours['endTime'] : '08:00 — 21:00';
		if ($set['chargestatus'] && !$store['status']) {
			$chargetypes = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_chargelist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 AND aid IN (0,' . $_W['aid'] . ') ORDER BY sort DESC'));
		}

		if ($store['verkey'] == 0) {
			$store['verkey'] = '';
		}

		$cashset = Setting::wlsetting_read('cashset');
		include wl_template('store/information');
	}

	public function submitInformation()
	{
		global $_W;
		global $_GPC;
		$set = Setting::wlsetting_read('register');
		$data = $_GPC['merchant'];

		if (empty($data['storename'])) {
			wl_message('返回链接错误', app_url('dashboard/home/index'), 'warning');
		}

		$storeid = $_GPC['storeid'];

		if ($_GPC['applyflag']) {
			if ($storeid) {
				$alflag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_merchantdata') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND storename = ' . $data['storename'] . ' AND id != ' . $storeid));
			}
			else {
				$alflag = pdo_get('wlmerchant_merchantdata', array('storename' => $data['storename'], 'uniacid' => $_W['uniacid']), array('id'));
			}

			if ($alflag) {
				wl_message('该商户已入住，请更换商户名', referer(), 'warning');
			}
		}

		$typeid = $_GPC['typeid'];
		$data['logo'] = $_GPC['images'][0] ? $_GPC['images'][0] : $_W['wlsetting']['base']['logo'];
		$data['qrcode'] = $_GPC['qrcode'][0];
		$data['adv'] = serialize($_GPC['advimages']);
		$data['album'] = serialize($_GPC['albumimages']);
		$data['examineimg'] = serialize($_GPC['examineimgs']);
		$data['location'] = serialize(array('lng' => $_GPC['lng'], 'lat' => $_GPC['lat']));
		$cateval = explode(' ', $_GPC['cate-picker']);
		$storehours = $_GPC['time-picker'];
		$storehours = explode('—', $storehours);
		$hours['startTime'] = trim($storehours[0]);
		$hours['endTime'] = trim($storehours[1]);
		$data['storehours'] = serialize($hours);
		$storethumb = $_GPC['thumbimages'];
		$data['store_quhao'] = $_GPC['store_quhao'];
		$data['note_quhao'] = $_GPC['note_quhao'];

		if ($storethumb) {
			foreach ($storethumb as $key => $th) {
				$th = tomedia($th);
				$data['introduction'] .= '<img src= "' . $th . '" />';
			}
		}

		if (!empty($data['introduction'])) {
			$data['introduction'] = htmlspecialchars_decode($data['introduction']);
		}

		if ($cateval[0]) {
			$data['onelevel'] = pdo_getcolumn(PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'name' => $cateval[0], 'parentid' => 0), 'id');
			$data['twolevel'] = pdo_getcolumn(PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'name' => $cateval[1], 'parentid' => $data['onelevel']), 'id');

			if (empty($data['twolevel'])) {
				$data['twolevel'] = $data['onelevel'];
			}
		}

		$storeaddr = explode(' ', $_GPC['city-picker']);
		if ($storeaddr[0] && $storeaddr[1] && $storeaddr[2]) {
			$data['provinceid'] = pdo_getcolumn(PDO_NAME . 'area', array('name' => $storeaddr[0], 'level' => 1), 'id');
			$data['areaid'] = pdo_getcolumn(PDO_NAME . 'area', array('name' => $storeaddr[1], 'level' => 2), 'id');
			$data['distid'] = pdo_getcolumn(PDO_NAME . 'area', array('name' => $storeaddr[2], 'level' => 3), 'id');
		}

		if ($storeid) {
			Tools::clearwxapp();
			Tools::clearposter();
			pdo_update(PDO_NAME . 'merchantdata', $data, array('id' => $storeid));
		}
		else {
			$data['uniacid'] = $_W['uniacid'];
			$data['aid'] = $_W['aid'];

			if (empty($data['areaid'])) {
				$data['areaid'] = $_W['areaid'];
			}

			$data['createtime'] = time();

			if ($set['chargestatus']) {
				$data['status'] = 0;
				$data['endtime'] = time();
			}
			else {
				$data['status'] = 1;
				$data['endtime'] = time() + 365 * 24 * 60 * 60;
			}

			$data['enabled'] = 0;
			if (!empty($_GPC['cardsn']) && !empty($_GPC['salt'])) {
				$qrcode = Storeqr::check_qrcode($_GPC['cardsn'], $_GPC['salt']);

				if (!empty($qrcode['sid'])) {
					wl_message('二维码已绑定商户，请检查二维码是否可用！', 'close', 'warning');
				}

				$base = Setting::agentsetting_read('storeqr');

				if ($base['status'] == 2) {
					$data['enabled'] = 1;
				}

				$data['cardsn'] = trim($_GPC['cardsn']);
			}

			$data['score'] = 5;
			$data['groupid'] = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('uniacid' => $_W['uniacid'], 'enabled' => 1, 'isdefault' => 1, 'aid' => $_W['aid']), 'id');
			$storeid = Store::registerEditData($data, 0);

			if (empty($storeid)) {
				wl_message('商户创建失败，请重试！', referer(), 'warning');
			}

			$arr['storeid'] = $storeid;
			$arr['name'] = trim($data['realname']);
			$arr['mobile'] = $data['mobile'];
			$arr['createtime'] = time();

			if (empty($data['areaid'])) {
				$arr['areaid'] = $_W['areaid'];
			}
			else {
				$arr['areaid'] = $data['areaid'];
			}

			$arr['status'] = 1;
			$arr['enabled'] = 1;
			$arr['ismain'] = 1;
			$arr['uniacid'] = $_W['uniacid'];
			$arr['aid'] = $_W['aid'];
			$re = Store::saveSingleRegister($arr, $_GPC['mid']);

			if (empty($re)) {
				wl_message('店铺管理员创建失败，请重试！', referer(), 'warning');
			}

			isetcookie('__wl_storeid', $arr['storeid'], 7 * 86400);
		}

		if ($typeid) {
			$chargetype = pdo_get('wlmerchant_chargelist', array('id' => $typeid));

			if (0 < $chargetype['price']) {
				$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'aid' => $_W['aid'], 'fkid' => $typeid, 'sid' => $storeid, 'status' => 0, 'paytype' => 0, 'createtime' => time(), 'orderno' => createUniontid(), 'price' => $chargetype['price'], 'num' => $chargetype['days'], 'plugin' => 'store', 'payfor' => 'merchant');
				$res = pdo_insert(PDO_NAME . 'order', $data);
				$orderid = pdo_insertid();
				header('location: ' . app_url('store/supervise/chargeorder', array('orderId' => $orderid)));
			}
			else {
				$endtime = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $storeid), 'endtime');
				$groupid = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('uniacid' => $_W['uniacid'], 'enabled' => 1, 'chargeid' => $typeid, 'aid' => $_W['aid']), 'id');

				if (empty($groupid)) {
					$groupid = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('uniacid' => $_W['uniacid'], 'enabled' => 1, 'isdefault' => 1, 'aid' => $_W['aid']), 'id');
				}

				if (time() < $endtime) {
					$newendtime = $chargetype['days'] * 24 * 3600 + $endtime;
				}
				else {
					$newendtime = $chargetype['days'] * 24 * 3600 + time();
				}

				if ($chargetype['audits']) {
					pdo_update(PDO_NAME . 'merchantdata', array('status' => 2, 'endtime' => $newendtime, 'enabled' => 1, 'audits' => 1, 'groupid' => $groupid), array('id' => $storeid));
					pdo_update(PDO_NAME . 'merchantuser', array('status' => 2), array('storeid' => $storeid));
				}
				else {
					pdo_update(PDO_NAME . 'merchantdata', array('status' => 1, 'endtime' => $newendtime, 'groupid' => $groupid), array('id' => $storeid));
					Message::settledtoadmin($storeid);
				}

				header('location: ' . app_url('store/storeManage/index'));
			}
		}
		else if ($_GPC['applyflag']) {
			Message::settledtoadmin($storeid);
			header('location: ' . app_url('store/storeManage/index'));
		}
		else {
			header('location: ' . app_url('store/storeManage/index'));
		}
	}

	public function chargeorder()
	{
		global $_W;
		global $_GPC;
		$orderid = $_GPC['orderId'];
		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
		if (empty($_W['wlmember']['mobile']) && in_array('charge', $mastmobile)) {
			wl_message('手机未绑定,请先绑定手机', app_url('member/user/binding', array('backurl' => urlencode(app_url('store/supervise/chargeorder', array('orderId' => $orderid))))), 'error');
		}

		$order = pdo_get('wlmerchant_order', array('id' => $orderid));
		$chargetypename = pdo_getcolumn(PDO_NAME . 'chargelist', array('id' => $order['fkid']), 'name');
		$params = array('tid' => $order['orderno'], 'ordersn' => $order['orderno'], 'title' => '商户入驻' . $chargetypename, 'fee' => $order['price']);
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Merchant', 'payfor' => 'Charge', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}

	public function verificationtool()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '核销工具 - ' . $_W['wlsetting']['base']['name'] : '核销工具';
		include wl_template('store/verificationtool');
	}

	public function oldverificationtool()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '核销工具 - ' . $_W['wlsetting']['base']['name'] : '核销工具';
		include wl_template('store/oldverificationtool');
	}

	public function newverifcode()
	{
		global $_W;
		global $_GPC;
		$code = trim($_GPC['verifcode']);
		$order = pdo_get(PDO_NAME . 'smallorder', array('uniacid' => $_W['uniacid'], 'sid' => $_W['storeid'], 'checkcode' => $code));

		if ($order) {
			if ($order['status'] == 1) {
				if ($order['plugin'] == 'rush') {
					$res = Rush::hexiaoorder($order['orderid'], $_W['mid'], 1, 1, $order['checkcode']);
				}
				else if ($order['plugin'] == 'groupon') {
					$res = Groupon::hexiaoorder($order['orderid'], $_W['mid'], 1, 1, $order['checkcode']);
				}
				else if ($order['plugin'] == 'wlfightgroup') {
					$res = Wlfightgroup::hexiaoorder($order['orderid'], $_W['mid'], 1, 1, $order['checkcode']);
				}
				else if ($order['plugin'] == 'bargain') {
					$res = Bargain::hexiaoorder($order['orderid'], $_W['mid'], 1, 1, $order['checkcode']);
				}
				else {
					if ($order['plugin'] == 'coupon') {
						$couponid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $order['orderid']), 'recordid');
						$res = wlCoupon::hexiaoorder($couponid, $_W['mid'], 1, 1, $order['checkcode']);
					}
				}

				if ($res) {
					exit(json_encode(array('result' => 1)));
				}
				else {
					exit(json_encode(array('result' => 2, 'msg' => '核销失败,请重试')));
				}
			}
			else {
				exit(json_encode(array('result' => 2, 'msg' => '此核销码已核销完成')));
			}
		}
		else {
			exit(json_encode(array('result' => 2, 'msg' => '核销码无效,请检查')));
		}
	}

	public function verifcode()
	{
		global $_W;
		global $_GPC;
		$type = intval($_GPC['veriftype']);
		$code = trim($_GPC['verifcode']);
		$num = intval($_GPC['couponnum']) ? intval($_GPC['couponnum']) : 1;

		if ($type == 1) {
			$order = pdo_get(PDO_NAME . 'rush_order', array('uniacid' => $_W['uniacid'], 'sid' => $_W['storeid'], 'checkcode' => $code));

			if ($order) {
				if ($order['usetimes'] < $num) {
					if (empty($order['usetimes'])) {
						$order['usetimes'] = 0;
					}

					exit(json_encode(array('result' => 2, 'msg' => '剩余核销次数为' . $order['usetimes'] . '次')));
				}

				$cutofftime = $order['estimatetime'];

				if ($cutofftime < time()) {
					exit(json_encode(array('result' => 2, 'msg' => '已超过截止日期，无法核销')));
				}

				if ($order && $order['status'] == 1) {
					$res = Rush::hexiaoorder($order['id'], $_W['mid'], $num, 1);

					if ($res) {
						exit(json_encode(array('result' => 1)));
					}
					else {
						exit(json_encode(array('result' => 2, 'msg' => '核销失败,请重试')));
					}
				}
				else {
					if ($order['status'] == 2) {
						exit(json_encode(array('result' => 2, 'msg' => '请勿重复核销')));
					}
				}
			}
			else {
				exit(json_encode(array('result' => 2, 'msg' => '核销码无效，请检查')));
			}
		}

		if ($type == 5) {
			$record = pdo_get('wlmerchant_groupon_userecord', array('qrcode' => $code, 'uniacid' => $_W['uniacid']));
			$order = pdo_get(PDO_NAME . 'order', array('id' => $record['orderid']));

			if ($order['sid'] != $_W['storeid']) {
				exit(json_encode(array('result' => 2, 'msg' => '此订单不属于本商户')));
			}

			$cutoff = pdo_get(PDO_NAME . 'groupon_activity', array('id' => $order['fkid']), array('cutofftime', 'cutoffstatus', 'cutoffday'));

			if ($record['usetimes'] < $num) {
				exit(json_encode(array('result' => 2, 'msg' => '剩余核销次数为' . $record['usetimes'] . '次')));
			}

			if ($order['estimatetime'] < time()) {
				exit(json_encode(array('result' => 2, 'msg' => '已超过截止日期，无法核销')));
			}

			if ($order && $order['status'] == 1) {
				$res = Groupon::hexiaoorder($order['id'], $_W['mid'], $num, 1);

				if ($res) {
					exit(json_encode(array('result' => 1)));
				}
				else {
					exit(json_encode(array('result' => 2, 'msg' => '核销失败,请重试')));
				}
			}
			else {
				if ($order['status'] == 2) {
					exit(json_encode(array('result' => 2, 'msg' => '请勿重复核销')));
				}
			}

			exit(json_encode(array('result' => 2, 'msg' => '核销码无效，请检查')));
		}

		if ($type == 2) {
			$record = pdo_get(PDO_NAME . 'activity_record', array('uniacid' => $_W['uniacid'], 'sid' => $_W['storeid'], 'checkcode' => $code), array('id', 'status', 'sid', 'usedtime', 'usetimes', 'orderid'));
			if ($record && $record['status'] == 1) {
				$order = pdo_get(PDO_NAME . 'order', array('id' => $record['orderid']), array('id'));

				if ($record['usetimes'] < $num) {
					exit(json_encode(array('result' => 2, 'msg' => '剩余核销次数为' . $record['usetimes'] . '次')));
				}
				else {
					$res = Activity::hexiaoorder($order['id'], $_W['mid'], $num, 1);

					if ($res) {
						exit(json_encode(array('result' => 1)));
					}
					else {
						exit(json_encode(array('result' => 2, 'msg' => '核销失败,请重试')));
					}
				}
			}
			else {
				if ($record['status'] == 2) {
					exit(json_encode(array('result' => 2, 'msg' => '请勿重复核销')));
				}
			}

			exit(json_encode(array('result' => 2, 'msg' => '核销码无效，请检查')));
		}

		if ($type == 3) {
			$order = pdo_get(PDO_NAME . 'member_coupons', array('uniacid' => $_W['uniacid'], 'concode' => $code), array('id', 'mid', 'parentid', 'title', 'id', 'usetimes', 'starttime', 'endtime', 'usedtime', 'orderno', 'aid'));

			if ($order) {
				$storeid = pdo_getcolumn(PDO_NAME . 'couponlist', array('uniacid' => $_W['uniacid'], 'id' => $order['parentid']), 'merchantid');

				if ($storeid != $_W['storeid']) {
					exit(json_encode(array('result' => 2, 'msg' => '此订单不属于本商户')));
				}

				if ($order['starttime'] < time()) {
					if (time() < $order['endtime']) {
						if (0 < $order['usetimes']) {
							if ($num <= $order['usetimes']) {
								$res = wlCoupon::hexiaoorder($order['id'], $_W['mid'], $num, 1);

								if ($res) {
									exit(json_encode(array('result' => 1)));
								}
								else {
									exit(json_encode(array('result' => 2, 'msg' => '订单核销请重试')));
								}
							}
							else {
								exit(json_encode(array('result' => 2, 'msg' => '卡券剩余使用次数为' . $order['usetimes'] . '次')));
							}
						}
						else {
							exit(json_encode(array('result' => 2, 'msg' => '卡券已使用，无法继续核销')));
						}
					}
					else {
						exit(json_encode(array('result' => 2, 'msg' => '卡券已过期，无法继续核销')));
					}
				}
				else {
					exit(json_encode(array('result' => 2, 'msg' => '卡券未到使用时间，无法核销')));
				}
			}
			else {
				exit(json_encode(array('result' => 2, 'msg' => '消费码无效，请检查')));
			}
		}

		if ($type == 4) {
			$record = pdo_get('wlmerchant_fightgroup_userecord', array('qrcode' => $code, 'uniacid' => $_W['uniacid']));

			if ($record) {
				$storeid = pdo_getcolumn(PDO_NAME . 'order', array('uniacid' => $_W['uniacid'], 'id' => $record['orderid']), 'sid');

				if ($storeid != $_W['storeid']) {
					exit(json_encode(array('result' => 2, 'msg' => '此订单不属于本商户')));
				}

				if ($record['usetimes']) {
					if ($num <= $record['usetimes']) {
						$res = Wlfightgroup::hexiaoorder($record['orderid'], $_W['mid'], $num, 1);

						if ($res) {
							exit(json_encode(array('result' => 1)));
						}
						else {
							exit(json_encode(array('result' => 2, 'msg' => '订单核销失败')));
						}
					}
					else {
						exit(json_encode(array('result' => 2, 'msg' => '订单剩余使用次数为' . $record['usetimes'] . '次')));
					}
				}
				else {
					exit(json_encode(array('result' => 2, 'msg' => '订单已使用，无法继续核销')));
				}
			}
			else {
				exit(json_encode(array('result' => 2, 'msg' => '消费码无效，请检查')));
			}
		}

		if ($type == 6) {
			$record = pdo_get('wlmerchant_bargain_userlist', array('qrcode' => $code, 'uniacid' => $_W['uniacid'], 'merchantid' => $_W['storeid']));

			if ($record) {
				if ($record['usetimes']) {
					if ($num <= $record['usetimes']) {
						$res = Bargain::hexiaoorder($record['orderid'], $_W['mid'], $num, 1);

						if ($res) {
							exit(json_encode(array('result' => 1)));
						}
						else {
							exit(json_encode(array('result' => 2, 'msg' => '订单核销失败')));
						}
					}
					else {
						exit(json_encode(array('result' => 2, 'msg' => '订单剩余使用次数为' . $record['usetimes'] . '次')));
					}
				}
				else {
					exit(json_encode(array('result' => 2, 'msg' => '订单已使用，无法继续核销')));
				}
			}
			else {
				exit(json_encode(array('result' => 2, 'msg' => '消费码无效，请检查')));
			}
		}

		exit(json_encode(array('result' => 2, 'msg' => '核销码类型错误')));
	}

	public function verifrecord()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '验证历史 - ' . $_W['wlsetting']['base']['name'] : '验证历史';
		$halfcardtext = !empty($_W['agentset']['halfcard']['halfcardtext']) ? $_W['agentset']['halfcard']['halfcardtext'] : '一卡通';
		$type = trim($_GPC['type']);
		$admin = intval($_GPC['admin']);
		$time = trim($_GPC['time']);
		$pindex = intval($_GPC['pindex']);
		$where = array('uniacid' => $_W['uniacid'], 'storeid' => $_W['storeid']);

		if (!empty($type)) {
			$where['plugin'] = $type;
		}

		if (!empty($admin)) {
			$where['verifmid'] = $admin;
		}

		if (!empty($time)) {
			if ($time == 'today') {
				$starttime = strtotime(date('Y-m-d'));
				$endtime = $starttime + 86399;
			}
			else if ($time == 'week') {
				$starttime = strtotime('previous monday');
				$endtime = time();
			}
			else if ($time == 'month') {
				$starttime = mktime(0, 0, 0, date('m'), 1, date('Y'));
				$endtime = time();
			}
			else {
				$times = explode('.', $time);
				$starttime = time() < strtotime($times[0]) ? strtotime(date('Y-m-d')) : strtotime($times[0]);
				$endtime = strtotime($times[1]) < $starttime ? $starttime + 86399 : strtotime($times[1]) + 86399;
			}

			$where['createtime>'] = $starttime;
			$where['createtime<'] = $endtime;
		}
		else {
			$starttime = strtotime('previous monday');
			$endtime = strtotime(date('Y-m-d'));
		}

		$meroof = Setting::agentsetting_read('meroof');

		if ($meroof['veradmin']) {
			$ismain = pdo_getcolumn(PDO_NAME . 'merchantuser', array('mid' => $_W['mid'], 'storeid' => $_W['storeid']), 'ismain');

			if ($ismain == 1) {
				$alladmin = pdo_getall(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'storeid' => $_W['storeid']), array('mid', 'name', 'mobile'), 'mid');

				foreach ($alladmin as $ak => &$av) {
					$member = Member::getMemberByMid($av['mid'], array('nickname', 'mobile'));
					$av['name'] = !empty($av['name']) ? $av['name'] : $member['nickname'];
					$av['mobile'] = !empty($av['mobile']) ? $av['mobile'] : $member['mobile'];
				}

				$adminname = '所有店员';
			}
			else {
				$where['verifmid'] = $_W['mid'];
				$adminname = pdo_getcolumn(PDO_NAME . 'merchantuser', array('mid' => $_W['mid'], 'storeid' => $_W['storeid']), 'name');
			}
		}
		else {
			$alladmin = pdo_getall(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'storeid' => $_W['storeid']), array('mid', 'name', 'mobile'), 'mid');

			foreach ($alladmin as $ak => &$av) {
				$member = Member::getMemberByMid($av['mid'], array('nickname', 'mobile'));
				$av['name'] = !empty($av['name']) ? $av['name'] : $member['nickname'];
				$av['mobile'] = !empty($av['mobile']) ? $av['mobile'] : $member['mobile'];
			}

			$adminname = '所有店员';
		}

		if ($_W['ispost']) {
			$data = Util::createStandardWhereString($where);
			$allfen = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename(PDO_NAME . 'verifrecord') . (' WHERE ' . $data[0] . ' '), $data[1]);
			$myorder = Util::getNumData('*', PDO_NAME . 'verifrecord', $where, 'ID DESC', $pindex, 10, 1);
			$allnum = $myorder[2];
			$myorder = $myorder[0];

			foreach ($myorder as $key => &$value) {
				$member = Member::getMemberByMid($value['mid'], array('nickname', 'mobile'));
				$value['nickname'] = $member['nickname'];
				$value['mobile'] = $member['mobile'];
				$value['verifnickname'] = $alladmin[$value['verifmid']]['name'];
				$value['verifmobile'] = $alladmin[$value['verifmid']]['mobile'];
				$value['createtime'] = date('Y-m-d H:i:s', $value['createtime']);

				if (empty($value['verifnickname'])) {
					$value['verifnickname'] = '后台';
				}

				if (empty($value['verifmobile'])) {
					$value['verifmobile'] = '无电话';
				}

				if ($value['plugin'] == 'rush') {
					$optionid = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $value['orderid']), 'optionid');
				}
				else {
					if ($value['plugin'] == 'groupon' || $value['plugin'] == 'wlfightgroup') {
						$optionid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $value['orderid']), 'specid');
					}
				}

				if ($optionid) {
					$value['optionname'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $optionid), 'title');
				}
			}

			exit(json_encode(array('errno' => 0, 'data' => $myorder, 'allnum' => intval($allnum), 'allfen' => intval($allfen))));
		}

		$alltype = array('halfcard1' => $halfcardtext, 'halfcard2' => '大礼包', 'rush' => '抢购', 'wlcoupon' => '卡券', 'wlfightgroup' => '拼团', 'groupon' => '团购', 'bargain' => '砍价');
		include wl_template('store/verifrecord');
	}

	public function goods()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商品管理 - ' . $_W['wlsetting']['base']['name'] : '商品管理';
		$merchantGoodsData = Util::getNumData('*', PDO_NAME . 'goodshouse', array('sid' => $_W['storeid']));
		$goods = $merchantGoodsData[0];

		foreach ($goods as $key => &$value) {
			$value['plugin'] = 'goodshouse';

			if ($_GPC['type'] == 'rush') {
				$value['a'] = app_url('store/supervise/createGoodsStep3', array('goodsid' => $value['id'], 'func' => 1));
			}
			else if ($_GPC['type'] == 'fightgroup') {
				$value['a'] = app_url('store/supervise/createGoodsStep3', array('goodsid' => $value['id'], 'func' => 2));
			}
			else {
				$value['a'] = app_url('store/supervise/createGoodsStep2', array('id' => $value['id']));
			}
		}

		include wl_template('store/goodsList');
	}

	public function createGoodsStep1()
	{
		global $_W;
		global $_GPC;
		$func = $_GPC['func'];
		include wl_template('store/createGoodsStep1');
	}

	public function createGoodsStep2()
	{
		global $_W;
		global $_GPC;
		$func = $_GPC['func'];

		if ($func) {
			switch ($func) {
			case 1:
				$name = '抢购';
				break;

			case 2:
				$name = '拼团';
				break;
			}
		}

		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商品管理 - ' . $_W['wlsetting']['base']['name'] : '商品管理';
		$id = $_GPC['id'];

		if ($id) {
			$goods = Util::getSingelData('*', PDO_NAME . 'goodshouse', array('id' => $_GPC['id']));
			$goods['thumbs'] = unserialize($goods['thumbs']);
		}

		if ($_GPC['token']) {
			$data = $_GPC['goods'];
			$images = $_GPC['images'];
			$data['thumb'] = $images[0];
			unset($images[0]);
			$data['thumbs'] = serialize($images);
			$data['uniacid'] = $_W['uniacid'];
			$data['sid'] = $_W['storeid'];
			$data['aid'] = $_W['aid'];

			if ($id) {
				pdo_update(PDO_NAME . 'goodshouse', $data, array('id' => $_GPC['id']));
			}
			else {
				$res = pdo_insert(PDO_NAME . 'goodshouse', $data);
				$id = pdo_insertid();
			}

			if ($func) {
				header('location:' . app_url('store/supervise/createGoodsStep3', array('goodsid' => $id, 'func' => $func)));
			}
			else {
				wl_message('保存成功！', app_url('store/supervise/goods'), 'success');
			}
		}

		include wl_template('store/createGoodsStep2');
	}

	public function createGoodsStep3()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商品管理 - ' . $_W['wlsetting']['base']['name'] : '商品管理';
		$meroof = Setting::agentsetting_read('meroof');
		$func = $_GPC['func'];
		$goodsid = $_GPC['goodsid'];
		$id = $_GPC['id'];

		if ($func) {
			switch ($func) {
			case 1:
				$name = '抢购';
				break;

			case 2:
				$name = '拼团';
				break;
			}
		}

		$wheres = array();
		$wheres['uniacid'] = $_W['uniacid'];
		$wheres['aid'] = $_W['aid'];
		$express = Store::getNumExpress('*', $wheres, 'ID DESC', 0, 0, 0);
		$express = $express[0];
		if (empty($goods['starttime']) || empty($goods['endtime'])) {
			$goods['starttime'] = time();
			$goods['endtime'] = strtotime('+1 month');
			$goods['cutofftime'] = strtotime('+1 month');
		}

		if ($func == 1) {
			if ($id) {
				$goods = pdo_get('wlmerchant_rush_activity', array('id' => $id));
				$goodtags = unserialize($goods['tag']);
				$goods['thumbs'] = unserialize($goods['thumbs']);
				$goods['describe'] = nl2br($goods['describe']);
				$goods['detail'] = nl2br($goods['detail']);
			}

			$presettags = pdo_getall('wlmerchant_tags', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'type' => 1), array('id', 'title'), '', 'sort DESC');
			$cate = pdo_getall('wlmerchant_rush_category', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
			$sale = $goods['num'] - $goods['levelnum'];

			if ($_GPC['token']) {
				$data = $_GPC['goods'];
				$data['starttime'] = strtotime($data['starttime']);
				$data['endtime'] = strtotime($data['endtime']);
				$data['cutofftime'] = strtotime($data['cutofftime']);
				$data['levelnum'] = $data['num'] - $sale;
				$data['tag'] = serialize($_GPC['tag']);
				$detailthumb = $_GPC['thumbimages'];

				if ($detailthumb) {
					foreach ($detailthumb as $key => $th) {
						$th = tomedia($th);
						$data['detail'] .= '<img src= "' . $th . '" />';
					}
				}

				$data['detail'] = htmlspecialchars_decode($data['detail']);
				$data['describe'] = htmlspecialchars_decode($data['describe']);
				$data['uniacid'] = $_W['uniacid'];
				$data['sid'] = $_W['storeid'];
				$data['aid'] = $_W['aid'];
				$images = $_GPC['images'];
				$data['thumb'] = $images[0];
				unset($images[0]);
				$images = array_values($images);
				$data['thumbs'] = serialize($images);
				$audits = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $data['sid']), 'audits');

				if ($audits) {
					$data['status'] = 1;
				}
				else {
					Store::toadmin($data['name'], $_W['storeid'], 'rush');
					$data['status'] = 5;
				}

				if ($id) {
					$res = Rush::updateActive($data, array('id' => $id));
				}
				else {
					$rushset = Setting::agentsetting_read('rush');

					if (!$rushset['distribution']) {
						$data['isdistri'] = 1;
					}

					if (!$rushset['settlement']) {
						$data['independent'] = 1;
					}

					$data['levelnum'] = $data['num'] - $sale;
					$res = Rush::saveRushActive($data);
				}

				if ($res) {
					Tools::clearposter();
					wl_message('保存成功！', app_url('store/supervise/rush'), 'success');
				}

				wl_message('保存失败！', referer(), 'error');
			}
		}

		if ($func == 2) {
			if ($id) {
				$goods = pdo_get('wlmerchant_fightgroup_goods', array('id' => $id));
				$goods['adv'] = unserialize($goods['adv']);
				$goods['detail'] = nl2br($goods['detail']);
			}

			if (empty($goods['islimittime'])) {
				$goods['limitstarttime'] = time();
				$goods['limitendtime'] = strtotime('+1 month');
			}

			$categorys = Wlfightgroup::getNumCategory('*', $wheres, 'ID DESC', 0, 0, 0);
			$categorys = $categorys[0];

			if ($_GPC['token']) {
				$data = $_GPC['goods'];
				$data['uniacid'] = $_W['uniacid'];
				$data['merchantid'] = $_W['storeid'];
				$data['aid'] = $_W['aid'];
				$images = $_GPC['images'];
				$data['logo'] = $images[0];
				unset($images[0]);
				$images = array_values($images);
				$data['adv'] = serialize($images);

				if ($data['islimittime']) {
					$data['limitstarttime'] = strtotime($data['limitstarttime']);
					$data['limitendtime'] = strtotime($data['limitendtime']);
				}

				$data['cutofftime'] = strtotime($data['cutofftime']);
				$detailthumb = $_GPC['thumbimages'];

				if ($detailthumb) {
					foreach ($detailthumb as $key => $th) {
						$th = tomedia($th);
						$data['detail'] .= '<img src= "' . $th . '" />';
					}
				}

				$data['detail'] = htmlspecialchars_decode($data['detail']);
				$audits = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $data['merchantid']), 'audits');

				if ($audits) {
					$data['status'] = 1;
				}
				else {
					Store::toadmin($data['name'], $_W['storeid'], 'fightgroup');
					$data['status'] = 5;
				}

				if ($id) {
					$res = Wlfightgroup::updateGoods($data, array('id' => $id));
				}
				else {
					$fightset = Setting::agentsetting_read('fightgroup');

					if (!$fightset['distribution']) {
						$data['isdistri'] = 1;
					}

					if (!$fightset['settlement']) {
						$data['independent'] = 1;
					}

					$res = Wlfightgroup::saveGoods($data);
				}

				if ($res) {
					wl_message('保存成功！', app_url('store/supervise/fightgroup'), 'success');
				}

				wl_message('保存失败！', referer(), 'error');
			}
		}

		include wl_template('store/createGoodsStep3');
	}

	public function close()
	{
		global $_W;
		global $_GPC;
		$goodsid = $_GPC['goodsid'];
		$func = $_GPC['func'];

		if ($func == 1) {
			$res = Rush::updateActive(array('status' => 4), array('id' => $goodsid));
		}
		else if ($func == 2) {
			$res = Wlfightgroup::updateGoods(array('status' => 0), $goodsid);
		}
		else if ($func == 3) {
			$res = pdo_update('wlmerchant_halfcardlist', array('status' => 0), array('id' => $goodsid));
		}
		else if ($func == 4) {
			$res = pdo_update('wlmerchant_couponlist', array('status' => 0), array('id' => $goodsid));
		}
		else if ($func == 5) {
			$res = pdo_update('wlmerchant_package', array('status' => 0), array('id' => $goodsid));
		}
		else if ($func == 6) {
			$res = pdo_update('wlmerchant_groupon_activity', array('status' => 0), array('id' => $goodsid));
		}
		else {
			if ($func == 7) {
				$res = pdo_update('wlmerchant_bargain_activity', array('status' => 0), array('id' => $goodsid));
			}
		}

		if ($res) {
			exit(json_encode(array('status' => 1)));
		}
		else {
			exit(json_encode(array('status' => 0)));
		}
	}

	public function createCategory()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商品管理 - ' . $_W['wlsetting']['base']['name'] : '商品管理';
		$merchantGoodsData = Util::getNumData('*', PDO_NAME . 'goodshouse', array('sid' => $_W['storeid']));
		$merchantGoods = $merchantGoodsData[0];
		include wl_template('store/createCategory');
	}

	public function admin()
	{
		global $_W;
		global $_GPC;
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

		if ($op == 'display') {
			$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '店员管理 - ' . $_W['wlsetting']['base']['name'] : '店员管理';
			$admin = pdo_get(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'storeid' => $_W['storeid'], 'ismain' => 1));
			$admin['member'] = Member::getMemberByMid($admin['mid'], array('avatar'));
			$hxadmin = pdo_getall(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'storeid' => $_W['storeid'], 'ismain' => 2));
			$gladmin = pdo_getall(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'storeid' => $_W['storeid'], 'ismain' => 3));

			if ($hxadmin) {
				foreach ($hxadmin as $key => $value) {
					$hxadmin[$key]['member'] = Member::getMemberByMid($value['mid'], array('avatar'));
				}
			}

			if ($gladmin) {
				foreach ($gladmin as $key => $value) {
					$gladmin[$key]['member'] = Member::getMemberByMid($value['mid'], array('avatar'));
				}
			}

			include wl_template('store/admin');
		}

		if ($op == 'add') {
			$ismian = pdo_getcolumn(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'storeid' => $_W['storeid']), 'ismain');

			// if ($ismian != 1) {
			// 	wl_message('只有店铺超级管理员才能管理店员。', app_url('store/supervise/admin'), 'warning');
			// }

			$id = intval($_GPC['id']);

			if ($_W['ispost']) {
				if ($id) {
					$data = array('name' => $_GPC['adminname'], 'mobile' => $_GPC['admintel'], 'ismain' => $_GPC['adminstatus']);

					if (pdo_update(PDO_NAME . 'merchantuser', $data, array('id' => $id))) {
						exit(json_encode(array('result' => 1, 'msg' => '店员修改成功')));
					}
					else {
						exit(json_encode(array('result' => 2, 'msg' => '店员修改失败，请重试')));
					}
				}
				else {
					$data = array('uniacid' => $_W['uniacid'], 'mid' => $_GPC['adminid'], 'storeid' => $_W['storeid'], 'name' => $_GPC['adminname'], 'mobile' => $_GPC['admintel'], 'areaid' => $_W['areaid'], 'createtime' => time(), 'status' => 2, 'enabled' => 1, 'aid' => $_W['aid'], 'ismain' => $_GPC['adminstatus']);

					if (pdo_insert(PDO_NAME . 'merchantuser', $data)) {
						exit(json_encode(array('result' => 1, 'msg' => '店员添加成功')));
					}
					else {
						exit(json_encode(array('result' => 2, 'msg' => '店员添加失败，请重试')));
					}
				}
			}

			if ($id) {
				$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '编辑店员 - ' . $_W['wlsetting']['base']['name'] : '编辑店员';
				$admin = pdo_get(PDO_NAME . 'merchantuser', array('id' => $id));
			}
			else {
				$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '添加店员 - ' . $_W['wlsetting']['base']['name'] : '添加店员';
				$qrcode = time();
				$showurl = app_url('store/supervise/confirmadmin', array('codes' => $qrcode, 'storeid' => $_W['storeid']));
				pdo_insert(PDO_NAME . 'merchantuser_qrlog', array('uniacid' => $_W['uniacid'], 'codes' => $qrcode, 'createtime' => $qrcode));
				pdo_delete(PDO_NAME . 'merchantuser_qrlog', array('uniacid' => $_W['uniacid'], 'createtime <' => $qrcode - 300, 'status !=' => 1));
			}

			include wl_template('store/admin_add');
		}

		if ($op == 'ajax') {
			$qrcode = $_GPC['codes'];
			$item = pdo_get(PDO_NAME . 'merchantuser_qrlog', array('uniacid' => $_W['uniacid'], 'codes' => $qrcode));
			$itemtime = $item['createtime'] + 300;
			if (empty($item) || $itemtime < time()) {
				exit(json_encode(array('success' => false, 'msg' => '二维码过期', 'reload' => true)));
			}

			if ($item['status'] == 0) {
				exit(json_encode(array('success' => false, 'msg' => '', 'reload' => false)));
			}

			$data = pdo_get(PDO_NAME . 'member', array('id' => $item['memberid']), array('avatar', 'nickname', 'id'));
			exit(json_encode(array('success' => true, 'dat' => $data)));
		}

		if ($op == 'del') {
			$id = intval($_GPC['id']);

			if ($id) {
				$admin = pdo_get(PDO_NAME . 'merchantuser', array('id' => $id));

				if ($admin['ismain'] == 1) {
					wl_message('店铺超级管理员无法删除');
				}

				if (pdo_delete(PDO_NAME . 'merchantuser', array('id' => $id))) {
					wl_message('删除店员成功', app_url('store/supervise/admin'), 'success');
				}

				wl_message('删除店员失败，请返回重试！');
			}
			else {
				wl_message('缺少关键参数，请返回重试！');
			}
		}
	}

	public function order()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '订单管理 - ' . $_W['wlsetting']['base']['name'] : '订单管理';
		$type = $_GPC['type'];
		$status = intval($_GPC['status']);
		$sort = intval($_GPC['sort']);
		$time = trim($_GPC['time']);

		switch ($type) {
		case 'rush':
			$name = '抢购订单';
			break;

		case 'coupon':
			$name = '卡券订单';
			break;

		case 'wlfightgroup':
			$name = '拼团订单';
			break;

		case 'groupon':
			$name = '团购订单';
			break;

		case 'halfcard':
			$name = '在线买单';
			break;

		case 'bargain':
			$name = '砍价订单';
			break;

		default:
			$name = '全部订单';
			break;
		}

		$where = ' where uniacid = ' . $_W['uniacid'] . ' and sid = ' . $_W['storeid'] . ' and orderno != 666666';

		if (!empty($time)) {
			if ($time == 'today') {
				$starttime = strtotime(date('Y-m-d'));
				$endtime = $starttime + 86399;
			}
			else if ($time == 'week') {
				$starttime = strtotime('previous monday');
				$endtime = time();
			}
			else if ($time == 'month') {
				$starttime = mktime(0, 0, 0, date('m'), 1, date('Y'));
				$endtime = time();
			}
			else {
				$times = explode('.', $time);
				$starttime = time() < strtotime($times[0]) ? strtotime(date('Y-m-d')) : strtotime($times[0]);
				$endtime = strtotime($times[1]) < $starttime ? $starttime + 86399 : strtotime($times[1]) + 86399;
			}

			$where .= ' AND createtime > ' . $starttime;
			$where .= ' AND createtime < ' . $endtime;
			$starttime2 = $starttime;
			$endtime2 = $endtime;
		}
		else {
			$starttime = strtotime('previous monday');
			$endtime = strtotime(date('Y-m-d'));
		}

		if ($status) {
			if ($status == 2) {
				$where .= ' AND status IN (2,3)';
			}
			else {
				$where .= ' AND status = ' . $status;
			}

			switch ($status) {
			case '1':
				$statusname = '待消费';
				break;

			case '2':
				$statusname = '已消费';
				break;

			case '3':
				$statusname = '已完成';
				break;

			case '4':
				$statusname = '待收货';
				break;

			case '5':
				$statusname = '已取消';
				break;

			case '6':
				$statusname = '待退款';
				break;

			case '7':
				$statusname = '已退款';
				break;

			case '8':
				$statusname = '待发货';
				break;

			default:
				$name = '全部';
				break;
			}
		}
		else {
			$statusname = '全部';
			$where .= ' AND status IN (1,2,3,4,8,6,7,9)';
		}

		if ($type) {
			if ($type == 'rush') {
				$where2 = $where . ' AND plugin IN (\'null\') ';
			}
			else {
				$where2 = $where . (' AND plugin = \'' . $type . '\' ');
				$where .= ' and orderno = 00000 ';
			}
		}
		else {
			$where2 = $where . ' AND plugin IN (\'wlfightgroup\',\'groupon\',\'coupon\',\'bargain\',\'halfcard\')';
		}

		$myorder = pdo_fetchall('SELECT id,createtime,sid,status,paidprid,mid,orderno,num,price,paytype,vipbuyflag,paytime,changedispatchprice,changeprice,disorderid, "a" FROM ' . tablename(PDO_NAME . 'order') . (' ' . $where2) . ' UNION ALL SELECT id,createtime,sid,status,paidprid,mid,orderno,num,price,paytype,vipbuyflag,paytime,changedispatchprice,changeprice,disorderid, "b" FROM ' . tablename(PDO_NAME . 'rush_order') . (' ' . $where . ' ORDER BY createtime DESC '));
		$allorder = count($myorder);
		$allmoney = 0;
		$allnum = 0;

		foreach ($myorder as $k => &$v) {
			if ($v['a'] == 'a') {
				$allmoney += $v['price'];
			}
			else {
				$actualprice = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $v['id']), 'actualprice');
				$allmoney += $actualprice;
			}

			$allnum += $v['num'];
		}

		include wl_template('store/order');
	}

	public function summary()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '数据统计 - ' . $_W['wlsetting']['base']['name'] : '数据统计';
		$store = Store::getSingleStore($_W['storeid']);
		include wl_template('store/summary');
	}

	public function showqrcode()
	{
		global $_W;
		global $_GPC;
		$url = urldecode($_GPC['url']);
		require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
		QRcode::png($url, false, QR_ECLEVEL_L, 8, 0);
	}

	public function confirmadmin()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '店员确认 - ' . $_W['wlsetting']['base']['name'] : '店员确认';
		$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

		if ($op == 'display') {
			$qrcode = $_GPC['codes'];
			if (empty($qrcode) || empty($_GPC['storeid'])) {
				wl_message('缺少重要参数，请重新扫描二维码', 'close');
			}

			$store = Store::getSingleStore($_GPC['storeid']);
			include wl_template('store/admin_confirmadmin');
		}

		if ($op == 'confirm') {
			$qrcode = $_GPC['codes'];

			if (empty($qrcode)) {
				wl_message('缺少重要参数，请重新扫描二维码', 'close');
			}

			$item = pdo_get(PDO_NAME . 'merchantuser_qrlog', array('uniacid' => $_W['uniacid'], 'codes' => $qrcode));
			$itemtime = $item['createtime'] + 300;
			if (empty($item) || $itemtime < time() || $item['status'] == 1) {
				wl_message('二维码已过期，请重新扫描二维码', 'close');
			}

			pdo_update(PDO_NAME . 'merchantuser_qrlog', array('memberid' => $_W['mid'], 'status' => 1), array('id' => $item['id']));
			wl_message('恭喜您，成为店铺店员', 'close', 'success');
		}
	}

	public function applist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '营销 - ' . $_W['wlsetting']['base']['name'] : '营销';
		include wl_template('store/applist');
	}

	public function rush()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '抢购管理 - ' . $_W['wlsetting']['base']['name'] : '抢购管理';
		$this->checkauthority('rush');
		$data = array();

		if ($_GPC['status'] == 4) {
			$data['status#'] = '(5,6)';
		}
		else if ($_GPC['status'] == 3) {
			$data['status#'] = '(3,4)';
		}
		else {
			$data['status'] = empty($_GPC['status']) ? '2' : $_GPC['status'];
		}

		$data['aid'] = $_W['aid'];
		$data['sid'] = $_W['storeid'];
		$activity = Rush::getNumActive('*', $data, 'ID DESC', 0, 0, 0);
		$goods = $activity[0];

		if ($goods) {
			foreach ($goods as $key => &$value) {
				$value['plugin'] = 'rush';
				$value['a'] = app_url('store/supervise/createGoodsStep3', array('id' => $value['id'], 'func' => 1));
				$value['salenum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . (' WHERE activityid = ' . $value['id'] . ' AND status IN (1,2,3,4,6,7,9)'));
				$value['hexiaonum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . (' WHERE activityid = ' . $value['id'] . ' AND status IN (2,3)'));
			}
		}

		$data['status'] = empty($_GPC['status']) ? '2' : $_GPC['status'];
		include wl_template('store/rush');
	}

	public function groupon()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '团购管理 - ' . $_W['wlsetting']['base']['name'] : '团购管理';
		$this->checkauthority('groupon');
		$data = array();

		if ($_GPC['status'] == 4) {
			$data['status#'] = '(5,6)';
			$status = 4;
		}
		else if ($_GPC['status'] == 2) {
			$data['status#'] = '(0,4)';
			$status = 2;
		}
		else {
			$data['status#'] = '(1,2,3,7)';
			$status = 1;
		}

		$data['aid'] = $_W['aid'];
		$data['sid'] = $_W['storeid'];
		$activity = Groupon::getNumActive('name,id,thumb,starttime,endtime,status,price,vipstatus,vipprice', $data, 'id DESC', 0, 0, 0);
		$goods = $activity[0];

		if ($goods) {
			foreach ($goods as $key => &$value) {
				$value['a'] = app_url('store/supervise/creategroupon', array('id' => $value['id']));
			}
		}

		include wl_template('store/groupon');
	}

	public function bargainlist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '砍价管理 - ' . $_W['wlsetting']['base']['name'] : '砍价管理';
		$this->checkauthority('bargain');
		$data = array();

		if ($_GPC['status'] == 4) {
			$data['status#'] = '(5,6)';
			$status = 4;
		}
		else if ($_GPC['status'] == 2) {
			$data['status'] = 0;
			$status = 2;
		}
		else {
			$data['status#'] = '(1,2,3,4,7)';
			$status = 1;
		}

		$data['aid'] = $_W['aid'];
		$data['sid'] = $_W['storeid'];
		$activity = Util::getNumData('*', 'wlmerchant_bargain_activity', $data);
		$activity = $activity[0];

		if ($activity) {
			foreach ($activity as $key => &$va) {
				$va['a'] = app_url('store/supervise/createbargain', array('id' => $va['id']));
				$va['alreadypay'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND  fkid = ' . $va['id'] . ' AND plugin = \'bargain\' AND status IN (1,2,3,4,8,6,7,9) '));

				if (empty($va['alreadypay'])) {
					$va['alreadypay'] = 0;
				}

				$va['bargaining'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_bargain_userlist') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND  activityid = ' . $va['id'] . ' AND status = 1 '));

				if (empty($va['bargaining'])) {
					$va['bargaining'] = 0;
				}
			}
		}

		include wl_template('store/bargainlist');
	}

	public function creategroupon()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '团购商品 - ' . $_W['wlsetting']['base']['name'] : '团购商品';
		$meroof = Setting::agentsetting_read('meroof');
		$id = $_GPC['id'];
		$wheres = array();
		$wheres['uniacid'] = $_W['uniacid'];
		$wheres['aid'] = $_W['aid'];
		$express = Store::getNumExpress('*', $wheres, 'ID DESC', 0, 0, 0);
		$express = $express[0];
		$base = Setting::agentsetting_read('groupon');

		if ($id) {
			$groupon = Groupon::getSingleActive($id, '*');
			$groupon['thumbs'] = unserialize($groupon['thumbs']);
			$starttime = $groupon['starttime'];
			$endtime = $groupon['endtime'];
			$cutofftime = $groupon['cutofftime'];
			$groupon['describe'] = nl2br($groupon['describe']);
			$groupon['detail'] = nl2br($groupon['detail']);
			$classInfo = pdo_get(PDO_NAME . 'groupon_category', array('id' => $groupon['cateid']), array('id', 'name', 'parentid'));

			if ($base['catelevel'] == 1) {
				if (0 < $classInfo['parentid']) {
					$groupon['one_id'] = $classInfo['parentid'];
					$groupon['two_id'] = $classInfo['id'];
					$oneClass = pdo_getall(PDO_NAME . 'groupon_category', array('parentid' => 0, 'aid' => $_W['aid'], 'uniacid' => $_W['uniacid']), array('id', 'name'));
					$twoClass = pdo_getall(PDO_NAME . 'groupon_category', array('parentid' => $classInfo['parentid']), array('id', 'name'));
				}
				else {
					$groupon['one_id'] = $classInfo['id'];
					$groupon['two_id'] = 0;
					$oneClass = $classInfo;
				}
			}
			else {
				$oneClass = pdo_getall(PDO_NAME . 'groupon_category', array('aid' => $_W['aid']), array('id', 'name'));
				$groupon['one_id'] = $classInfo['id'];
			}
		}
		else {
			$starttime = time();
			$endtime = time() + 30 * 24 * 3600;
			$cutofftime = $endtime;

			if ($base['catelevel'] == 1) {
				$oneClass = pdo_getall(PDO_NAME . 'groupon_category', array('parentid' => 0, 'aid' => $_W['aid'], 'uniacid' => $_W['uniacid']), array('id', 'name'));

				if (0 < count($oneClass)) {
					$twoClass = pdo_getall(PDO_NAME . 'groupon_category', array('parentid' => $oneClass[0]['id']), array('id', 'name'));
				}
			}
			else {
				$oneClass = pdo_getall(PDO_NAME . 'groupon_category', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid']), array('id', 'name'));
			}
		}

		if ($_GPC['token']) {
			$data = $_GPC['groupon'];
			$images = $_GPC['images'];
			$data['thumb'] = $images[0];
			unset($images[0]);
			$images = array_values($images);
			$data['thumbs'] = serialize($images);
			$data['starttime'] = strtotime($data['starttime']);
			$data['endtime'] = strtotime($data['endtime']);
			$data['cutofftime'] = strtotime($data['cutofftime']);
			$detailthumb = $_GPC['thumbimages'];

			if ($detailthumb) {
				foreach ($detailthumb as $key => $th) {
					$th = tomedia($th);
					$data['detail'] .= '<img src= "' . $th . '" />';
				}
			}

			$data['detail'] = htmlspecialchars_decode($data['detail']);

			if ($_GPC['two_id']) {
				$data['cateid'] = $_GPC['two_id'];
			}
			else {
				$data['cateid'] = $_GPC['one_id'];
			}

			if (empty($data['num'])) {
				$data['quantity'] = 1000;
			}

			$audits = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $_W['storeid']), 'audits');

			if ($audits) {
				$data['status'] = 1;
			}
			else {
				Store::toadmin($data['name'], $_W['storeid'], 'groupon');
				$data['status'] = 5;
			}

			if ($_GPC['grouponid']) {
				$res = pdo_update('wlmerchant_groupon_activity', $data, array('id' => $_GPC['grouponid']));
			}
			else {
				$data['uniacid'] = $_W['uniacid'];
				$data['levelnum'] = $data['num'];
				$data['aid'] = $_W['aid'];
				$data['sid'] = $_W['storeid'];
				$data['createtime'] = time();

				if (!$base['distribution']) {
					$data['isdistri'] = 1;
				}

				if (!$base['settlement']) {
					$data['independent'] = 1;
				}

				$res = pdo_insert('wlmerchant_groupon_activity', $data);
			}

			if ($res) {
				Tools::clearposter();
				wl_message('保存成功！', app_url('store/supervise/groupon'), 'success');
			}
			else {
				wl_message('保存失败！', referer(), 'error');
			}
		}

		include wl_template('store/creategroupon');
	}

	/**
     * Comment: 根据一级分类id获取该一级分类下面所有的二级分类
     * Author: zzw
     */
	public function getTwoClass()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$twoClass = pdo_getall(PDO_NAME . 'groupon_category', array('parentid' => $id), array('id', 'name'));
		wl_json(1, '二级分类信息', $twoClass);
	}

	public function createbargain()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '砍价商品 - ' . $_W['wlsetting']['base']['name'] : '砍价商品';
		$id = $_GPC['id'];
		$set = Setting::agentsetting_read('bargainset');
		$meroof = Setting::agentsetting_read('meroof');
		$cate = pdo_getall('wlmerchant_bargain_category', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id', 'name'));
		$wheres = array();
		$wheres['uniacid'] = $_W['uniacid'];
		$wheres['aid'] = $_W['aid'];
		$express = Store::getNumExpress('*', $wheres, 'ID DESC', 0, 0, 0);
		$express = $express[0];

		if ($id) {
			$bargain = pdo_get('wlmerchant_bargain_activity', array('id' => $id));
			$bargain['thumbs'] = unserialize($bargain['thumbs']);
			$bargain['detail'] = nl2br($bargain['detail']);
			$starttime = $bargain['starttime'];
			$endtime = $bargain['endtime'];
			$rules = unserialize($bargain['rules']);

			if (empty($rules)) {
				$rules = array(1);
			}
		}
		else {
			$starttime = time();
			$endtime = time() + 30 * 24 * 3600;
			$rules = array(1);
		}

		if ($_GPC['token']) {
			$data = $_GPC['bargain'];
			$images = $_GPC['images'];
			$data['thumb'] = $images[0];
			unset($images[0]);
			$images = array_values($images);
			$data['thumbs'] = serialize($images);
			$data['starttime'] = strtotime($data['starttime']);
			$data['endtime'] = strtotime($data['endtime']);
			$detailthumb = $_GPC['thumbimages'];

			if ($detailthumb) {
				foreach ($detailthumb as $key => $th) {
					$th = tomedia($th);
					$data['detail'] .= '<img src= "' . $th . '" />';
				}
			}

			$data['detail'] = htmlspecialchars_decode($data['detail']);

			if (empty($data['stock'])) {
				$data['stock'] = 1000;
			}

			$audits = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $_W['storeid']), 'audits');

			if ($audits) {
				$data['status'] = 1;
			}
			else {
				Store::toadmin($data['name'], $_W['storeid'], 'bargain');
				$data['status'] = 5;
			}

			$rule_pice = $_GPC['rule_pice'];
			$rule_start = $_GPC['rule_start'];
			$rule_end = $_GPC['rule_end'];
			$len = count($rule_pice);
			$bargainrule = array();
			$k = 0;

			while ($k < $len) {
				$bargainrule[$k]['rule_pice'] = $rule_pice[$k];
				$bargainrule[$k]['rule_start'] = $rule_start[$k];
				$bargainrule[$k]['rule_end'] = $rule_end[$k];
				++$k;
			}

			$bargainrule = serialize($bargainrule);
			$data['rules'] = $bargainrule;

			if ($id) {
				$res = pdo_update('wlmerchant_bargain_activity', $data, array('id' => $id));
			}
			else {
				$data['uniacid'] = $_W['uniacid'];
				$data['aid'] = $_W['aid'];
				$data['sid'] = $_W['storeid'];
				$data['createtime'] = time();

				if (!$set['settlement']) {
					$data['independent'] = 1;
				}

				$res = pdo_insert('wlmerchant_bargain_activity', $data);
			}

			if ($res) {
				Tools::clearposter();
				wl_message('保存成功！', app_url('store/supervise/bargainlist'), 'success');
			}
			else {
				wl_message('保存失败！', referer(), 'error');
			}
		}

		include wl_template('store/createbargain');
	}

	public function fightgroup()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '拼团管理 - ' . $_W['wlsetting']['base']['name'] : '拼团管理';
		$this->checkauthority('fightgroup');
		$data = array();

		if ($_GPC['status'] == 4) {
			$data['status#'] = '(5,6)';
		}
		else if ($_GPC['status'] == 2) {
			$data['status'] = 0;
		}
		else {
			$data['status'] = empty($_GPC['status']) ? '1' : $_GPC['status'];
		}

		$data['aid'] = $_W['aid'];
		$data['merchantid'] = $_W['storeid'];
		$activity = Wlfightgroup::getNumGoods('*', $data, 'ID DESC', 0, 0, 0);
		$goods = $activity[0];

		if ($goods) {
			foreach ($goods as $key => &$value) {
				$value['plugin'] = 'fightgroup';
				$value['a'] = app_url('store/supervise/createGoodsStep3', array('id' => $value['id'], 'func' => 2));
			}
		}

		$data['status'] = empty($_GPC['status']) ? '1' : $_GPC['status'];
		include wl_template('store/fightgroup');
	}

	public function get_rush_order()
	{
		global $_W;
		global $_GPC;
		$pindex = $_GPC['pindex'];
		$psize = 20;
		$type = $_GPC['type'];
		$sort = $_GPC['sort'];
		$starttime = $_GPC['starttime'];
		$endtime = $_GPC['endtime'];
		$status = $_GPC['status'];
		$where = ' where uniacid = ' . $_W['uniacid'] . ' and sid = ' . $_W['storeid'] . ' and orderno != 666666';

		if ($status) {
			if ($status == 1) {
				$where .= ' AND status IN (1,4,8)';
			}
			else if ($status == 2) {
				$where .= ' AND status IN (2,3)';
			}
			else {
				$where .= ' AND status = ' . $status;
			}
		}
		else {
			$where .= ' AND status IN (1,2,3,4,8,6,7,9)';
		}

		if (!empty($starttime) && !empty($endtime)) {
			$where .= ' AND createtime > ' . $starttime;
			$where .= ' AND createtime < ' . $endtime;
		}

		if ($type) {
			if ($type == 'rush') {
				$where2 = $where . ' AND plugin IN (\'null\') ';
			}
			else {
				$where2 = $where . (' AND plugin = \'' . $type . '\' ');
				$where .= ' and orderno = 00000 ';
			}
		}
		else {
			$where2 = $where . ' AND plugin IN (\'wlfightgroup\',\'groupon\',\'coupon\',\'bargain\',\'halfcard\') ';
		}

		if ($sort) {
			$myorder = pdo_fetchall('SELECT id,createtime,sid,status,paidprid,mid,orderno,num,price,paytype,vipbuyflag,paytime,changedispatchprice,changeprice,disorderid,mobile,remark, "a" FROM ' . tablename(PDO_NAME . 'order') . (' ' . $where2) . ' UNION ALL SELECT id,createtime,sid,status,paidprid,mid,orderno,num,price,paytype,vipbuyflag,paytime,changedispatchprice,changeprice,disorderid,mobile,remark, "b" FROM ' . tablename(PDO_NAME . 'rush_order') . (' ' . $where . ' ORDER BY createtime ASC LIMIT ') . ($pindex - 1) * $psize . ',' . $psize);
		}
		else {
			$myorder = pdo_fetchall('SELECT id,createtime,sid,status,paidprid,mid,orderno,num,price,paytype,vipbuyflag,paytime,changedispatchprice,changeprice,disorderid,mobile,remark, "a" FROM ' . tablename(PDO_NAME . 'order') . (' ' . $where2) . ' UNION ALL SELECT id,createtime,sid,status,paidprid,mid,orderno,num,price,paytype,vipbuyflag,paytime,changedispatchprice,changeprice,disorderid,mobile,remark, "b" FROM ' . tablename(PDO_NAME . 'rush_order') . (' ' . $where . ' ORDER BY createtime DESC LIMIT ') . ($pindex - 1) * $psize . ',' . $psize);
		}

		foreach ($myorder as $key => &$value) {
			if ($value['a'] == 'a') {
				$value['fkid'] = pdo_getcolumn(PDO_NAME . 'order', array('id' => $value['id']), 'fkid');
				$value['plugin'] = pdo_getcolumn(PDO_NAME . 'order', array('id' => $value['id']), 'plugin');

				if ($value['plugin'] == 'coupon') {
					$goods = wlCoupon::getSingleCoupons($value['fkid'], 'title');
					$value['gname'] = $goods['title'];
				}

				if ($value['plugin'] == 'wlfightgroup') {
					$goods = Wlfightgroup::getSingleGood($value['fkid'], 'name');
					$value['gname'] = $goods['name'];

					if ($value['expressid']) {
						$value['express'] = pdo_get('wlmerchant_express', array('id' => $value['expressid']));
					}
				}

				if ($value['plugin'] == 'groupon') {
					$value['gname'] = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $value['fkid']), 'name');
				}

				if ($value['plugin'] == 'halfcard') {
					if ($value['fkid']) {
						$value['gname'] = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('id' => $value['fkid']), 'title');
					}
					else {
						$value['gname'] = '在线买单';
					}
				}

				if ($value['plugin'] == 'bargain') {
					$value['gname'] = pdo_getcolumn(PDO_NAME . 'bargain_activity', array('id' => $value['fkid']), 'name');
				}

				$value['expressid'] = pdo_getcolumn(PDO_NAME . 'order', array('id' => $value['id']), 'expressid');

				if ($value['expressid']) {
					$value['express'] = pdo_get('wlmerchant_express', array('id' => $value['expressid']), array('name', 'tel', 'address'));
				}

				$optionid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $value['id']), 'specid');
			}
			else {
				$value['activityid'] = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $value['id']), 'activityid');
				$value['gname'] = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $value['activityid']), 'name');
				$value['address'] = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $value['id']), 'address');
				$value['username'] = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $value['id']), 'username');
				$optionid = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $value['id']), 'optionid');
			}

			$member = pdo_get(PDO_NAME . 'member', array('id' => $value['mid'], 'uniacid' => $_W['uniacid']), array('nickname', 'mobile'));
			$value['nickname'] = $member['nickname'];

			if (empty($value['mobile'])) {
				$value['mobile'] = $member['mobile'];
			}

			$value['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
			$value['optiontitle'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $optionid), 'title');
		}

		exit(json_encode(array('errno' => 0, 'data' => $myorder)));
	}

	public function send()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['orderid'];
		$settings = Setting::wlsetting_read('orderset');
		$plugin = $_GPC['plugin'];

		if ($plugin == 'b') {
			$item = pdo_get(PDO_NAME . 'rush_order', array('id' => $id), array('expressid'));
		}
		else {
			$item = pdo_get(PDO_NAME . 'order', array('id' => $id), array('expressid'));
		}

		$data['expressname'] = $_GPC['expressname'];
		$data['expresssn'] = $_GPC['expresssn'];
		if (!$data['expressname'] || !$data['expresssn']) {
			exit(json_encode(array('errno' => 1, 'message' => '请完善快递信息')));
		}

		$data['sendtime'] = time();
		$data['orderid'] = $id;
		$res = pdo_update(PDO_NAME . 'express', $data, array('id' => $item['expressid']));

		if ($res) {
			if ($plugin == 'b') {
				pdo_update(PDO_NAME . 'rush_order', array('status' => 4), array('id' => $id));

				if (0 < $settings['receipt']) {
					$receipttime = time() + $settings['receipt'] * 24 * 3600;
					$task = array('type' => 'rush', 'orderid' => $id);
					$task = serialize($task);
					Queue::addTask(6, $task, $receipttime, $id);
				}
			}
			else {
				pdo_update(PDO_NAME . 'order', array('status' => 4), array('id' => $id));

				if (0 < $settings['receipt']) {
					$receipttime = time() + $settings['receipt'] * 24 * 3600;
					$task = array('type' => 'order', 'orderid' => $id);
					$task = serialize($task);
					Queue::addTask(6, $task, $receipttime, $id);
				}
			}

			Message::sendremind($id, $plugin);
			exit(json_encode(array('errno' => 0, 'message' => '')));
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '发货失败，请重试')));
		}
	}

	public function get_fans()
	{
		global $_W;
		global $_GPC;
		$pindex = $_GPC['pindex'];
		$where = array('uniacid' => $_W['uniacid'], 'sid' => $_W['storeid']);
		$myorder = Util::getNumData(' mid,createtime ', PDO_NAME . 'storefans', $where, 'ID DESC', $pindex, 20, 1);
		$myorder = $myorder[0];

		foreach ($myorder as $k => &$v) {
			$member = Member::getMemberByMid($v['mid'], array('avatar', 'nickname'));
			$v['avatar'] = $member['avatar'];
			$v['nickname'] = $member['nickname'];
			$v['createtime'] = date('Y-m-d H:i', $v['createtime']);
			$rushmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_rush_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND sid = ' . $_W['storeid'] . ' AND mid = ' . $v['mid'] . ' AND status IN (1,2,3,9)'));
			$rushnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND sid = ' . $_W['storeid'] . ' AND mid = ' . $v['mid'] . ' AND status IN (1,2,3,9)'));
			$ordermoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND sid = ' . $_W['storeid'] . ' AND mid = ' . $v['mid'] . ' AND status IN (1,2,3,4,8) AND plugin IN (\'wlfightgroup\',\'coupon\')'));
			$ordernum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND sid = ' . $_W['storeid'] . ' AND mid = ' . $v['mid'] . ' AND status IN (1,2,3,4,8) AND plugin IN (\'wlfightgroup\',\'coupon\')'));
			$halfmoney = pdo_fetchcolumn('SELECT SUM(realmoney) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND merchantid = ' . $_W['storeid'] . ' AND mid = ' . $v['mid'] . ' AND type = 1'));
			$halfnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND merchantid = ' . $_W['storeid'] . ' AND mid = ' . $v['mid'] . ' AND type = 1'));
			$v['usemoney'] = sprintf('%.2f', $rushmoney + $ordermoney + $halfmoney);
			$v['usetimes'] = $rushnum + $ordernum + $halfnum;
		}

		exit(json_encode(array('errno' => 0, 'data' => $myorder)));
	}

	public function cash()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '申请结算 - ' . $_W['wlsetting']['base']['name'] : '申请结算';
		$ismian = pdo_getcolumn(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'storeid' => $_W['storeid']), 'ismain');

		if ($ismian != 1) {
			wl_message('只有店铺超级管理员才能进行财务结算。', app_url('store/supervise/platform'), 'warning');
		}

		$cashsets = Setting::wlsetting_read('cashset');
		$_GPC['type'] = $_GPC['type'] ? $_GPC['type'] : 'apply';
		$payment_type = $cashsets['payment_type'];
		$agent = Area::getSingleAgent($_W['aid']);
		$syssalepercent = $agent['percent']['syssalepercent'];

		if (empty($syssalepercent)) {
			$syssalepercent = sprintf('%.2f', $_W['wlsetting']['cashset']['syssalepercent']);
		}
		else {
			$syssalepercent = sprintf('%.2f', $syssalepercent);
		}

		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $_W['storeid']), array('allmoney', 'nowmoney', 'reservestatus','salesmid','salesrate', 'reservemoney'));
		
		if ($merchant['reservestatus']) {
			$reservemoney = $merchant['reservemoney'];
		}
		else {
			$reservemoney = sprintf('%.2f', $cashsets['reservemoney']);
		}

		$usemoney = sprintf('%.2f', $merchant['nowmoney'] - $reservemoney);
		// 扣除商会业务员提成
		if($merchant['salesmid']&&$merchant['salesrate']>0){
			$usemoney = $usemoney  -  $usemoney *$merchant['salesrate'] /100;
		}

		if ($usemoney < 0) {
			$usemoney = 0;
		}

		if (0 < $_GPC['settlementmoney']) {
			$shopIntervalTime = $cashsets['shopIntervalTime'];

			if (0 < $shopIntervalTime) {
				$startTime = pdo_fetchcolumn('SELECT applytime FROM ' . tablename(PDO_NAME . 'settlement_record') . (' WHERE sid = ' . $_W['storeid'] . ' AND uniacid = ' . $_W['uniacid'] . ' ORDER BY applytime DESC '));
				$interval = time() - $startTime;
				$intervalDay = $interval / 3600 / 24;
				$intercalRes = ceil($shopIntervalTime - $intervalDay);

				if (0 < $intercalRes) {
					exit(json_encode(array('errno' => 1, 'message' => '请等' . $intercalRes . '天后再申请！')));
				}
			}

			if ($_GPC['settlementmoney'] < $cashsets['lowsetmoney']) {
				exit(json_encode(array('errno' => 1, 'message' => '申请失败，最低提现金额为' . $cashsets['lowsetmoney'] . '元。')));
			}

			if ($usemoney < $_GPC['settlementmoney']) {
				exit(json_encode(array('errno' => 1, 'message' => '申请失败，申请金额不能大于可提现余额')));
			}

			$settlementmoney = $_GPC['settlementmoney'];
			$payment_type = $_GPC['payment_type'];
			$storeInfo = Store::getShopOwnerInfo($_W['storeid'], $_W['aid']);
			if ($payment_type == 1 && empty($storeInfo['alipay'])) {
				wl_json(1, '请先前往个人中心完善支付宝账号信息');
			}
			else {
				if ($payment_type == 3 && (empty($storeInfo['card_number']) || empty($storeInfo['bank_name']))) {
					wl_json(1, '请先前往个人中心完善银行账号信息');
				}
			}

			$nowmoney = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $_W['storeid']), 'nowmoney');

			if ($nowmoney < $settlementmoney) {
				wl_json(1, '商户可提现余额不足');
			}
			// 
			$midmoney = $merchant['salesrate']&&$merchant['salesmid']?$settlementmoney*$merchant['salesrate']/100:0;
			$data = array(
				'uniacid' => $_W['uniacid'], 
				'sid' => $_W['storeid'], 
				'aid' => $_W['aid'], 
				'status' => 2, 
				'type' => 1, 
				'sapplymoney' => $settlementmoney,//商家申请的金额 
				'sgetmoney' => sprintf('%.2f', $settlementmoney - $syssalepercent * $settlementmoney / 100 -$midmoney), //商家实际得到的钱
				'spercentmoney' => sprintf('%.2f', $syssalepercent * $settlementmoney / 100), //商家缴纳佣金
				'spercent' => $syssalepercent,//商家给代理的提成比例
				'midmoney'=>$midmoney,
				'midid'=>$merchant['salesmid'],
				'applytime' => TIMESTAMP, 'updatetime' => TIMESTAMP, 'sopenid' => $storeInfo['openid'], 'payment_type' => $payment_type);

			if ($cashsets['noaudit']) {
				$data['status'] = 3;
				$trade_no = time() . random(4, true);
				$data['trade_no'] = $trade_no;
				$data['updatetime'] = time();
			}

			if (pdo_insert(PDO_NAME . 'settlement_record', $data)) {
				$orderid = pdo_insertid();
				$res = Store::settlement($orderid, 0, $data['sid'], 0 - $settlementmoney, 0, 0 - $settlementmoney, 7, 0, 0, $_W['aid']);
				if ($cashsets['autocash'] && $data['payment_type'] == 2) {
					Queue::addTask(4, $orderid, time(), $orderid);
				}

				if ($res) {
					if (!empty($_W['wlsetting']['noticeMessage']['adminopenid'])) {
						$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $_W['storeid']), 'storename');
						$first = '您好，有一个商户提现申请待审核。';
						$keyword1 = '商户[' . $storename . ']申请提现' . $settlementmoney . '元';
						$keyword2 = '待审核';
						$remark = '请尽快前往系统后台审核';
						$url = app_url('dashboard/home/index');
						Message::jobNotice($_W['wlsetting']['noticeMessage']['adminopenid'], $first, $keyword1, $keyword2, $remark, $url);
					}

					exit(json_encode(array('errno' => 0, 'message' => '申请成功！')));
				}
				else {
					exit(json_encode(array('errno' => 1, 'message' => '申请失败，请重试')));
				}
			}

			exit(json_encode(array('errno' => 1, 'message' => '申请失败！')));
		}

		if ($_GPC['type'] == 'apply') {
			if ($_GPC['num']) {
				$num = $_GPC['num'];

				if ($usemoney < $num) {
					exit(json_encode(array('errno' => 1, 'msg' => '账户可提现余额不足')));
				}
				else {
					$surplusmoney = sprintf('%.2f', $merchant['nowmoney'] - $num);
					$realmoney = sprintf('%.2f', $num - $syssalepercent * $num / 100);
					exit(json_encode(array('errno' => 0, 'syssalepercent' => $syssalepercent, 'realmoney' => $realmoney, 'num' => $num, 'surplusmoney' => $surplusmoney)));
				}
			}
		}

		if ($_GPC['type'] == 'deling' || $_GPC['type'] == 'finish' || $_GPC['type'] == 'reject') {
			$where = array('uniacid' => $_W['uniacid'], 'sid' => $_W['storeid'], 'type' => 1);

			if ($_GPC['type'] == 'deling') {
				$where['#status#'] = '(1,2,3,4)';
			}

			if ($_GPC['type'] == 'finish') {
				$where['status'] = 5;
			}

			if ($_GPC['type'] == 'reject') {
				$where['#status#'] = '(-1,-2)';
			}

			$recordData = Util::getNumData('*', PDO_NAME . 'settlement_record', $where);
			$record = $recordData[0];
		}

		include wl_template('store/cash');
	}

	public function settlementrecode()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '交易明细 - ' . $_W['wlsetting']['base']['name'] : '交易明细';
		$type = $_GPC['type'];
		$pindex = $_GPC['pindex'];
		$where = array('uniacid' => $_W['uniacid'], 'merchantid' => $_W['storeid']);

		if (!empty($type)) {
			$wk = $type == 1 ? 'merchantmoney>' : 'merchantmoney<';
			$where[$wk] = 0;
		}

		if ($_W['ispost']) {
			$myorder = Util::getNumData('*', PDO_NAME . 'autosettlement_record', $where, 'ID DESC', $pindex, 10, 1);
			$myorder = $myorder[0];

			foreach ($myorder as $key => &$recode) {
				if ($recode['type'] == 1) {
					$recode['goodsname'] = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $recode['goodsid']), 'name');
				}
				else if ($recode['type'] == 2) {
					$recode['goodsname'] = pdo_getcolumn(PDO_NAME . 'fightgroup_goods', array('id' => $recode['goodsid']), 'name');
				}
				else if ($recode['type'] == 3) {
					$recode['goodsname'] = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $recode['goodsid']), 'title');
				}
				else if ($recode['type'] == 10) {
					$recode['goodsname'] = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $recode['goodsid']), 'name');
				}
				else if ($recode['type'] == 7) {
					if (0 < $recode['merchantmoney']) {
						$recode['goodsname'] = '提现申请被驳回';
					}
					else {
						$recode['goodsname'] = '商户申请提现';
					}
				}
				else if ($recode['type'] == 11) {
					if ($recode['goodsid']) {
						$recode['goodsname'] = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('id' => $recode['goodsid']), 'title');
					}
					else {
						$recode['goodsname'] = '在线买单';
					}
				}
				else {
					if ($recode['type'] == 12) {
						$recode['goodsname'] = pdo_getcolumn(PDO_NAME . 'bargain_activity', array('id' => $recode['goodsid']), 'name');
					}
				}

				$recode['createtime'] = date('Y-m-d H:i:s', $recode['createtime']);
			}

			exit(json_encode(array('errno' => 0, 'data' => $myorder)));
		}

		include wl_template('store/settlementrecode');
	}

	public function comment()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '评价管理 - ' . $_W['wlsetting']['base']['name'] : '评价管理';
		$where = array('uniacid' => $_W['uniacid'], 'sid' => $_W['storeid']);
		$this->checkauthority('comment');
		$replyone = intval($_GPC['replyone']);
		$checkone = intval($_GPC['checkone']);

		if (!empty($checkone)) {
			$where['checkone'] = $checkone;
		}

		if (!empty($replyone)) {
			$where['replyone'] = $replyone;
		}

		if ($_W['ispost']) {
			$pindex = max(1, $_GPC['pindex']);
			$psize = 10;
			$myorder = pdo_getslice(PDO_NAME . 'comment', $where, array($pindex, $psize), $total, array(), '', 'id DESC');

			foreach ($myorder as $key => &$value) {
				$value['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
			}

			exit(json_encode(array('errno' => 0, 'data' => $myorder)));
		}

		include wl_template('store/comment');
	}

	public function commentreply()
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['orderid']);
		$replytext = trim($_GPC['replytext']);
		if (empty($orderid) || empty($replytext)) {
			wl_message(error(1, '缺少回复内容或参数'));
		}

		pdo_update(PDO_NAME . 'comment', array('replytextone' => $replytext, 'replyone' => 2), array('id' => $orderid));
		$comment = pdo_get('wlmerchant_comment', array('id' => $orderid), array('mid', 'replytextone', 'sid'));
		$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $comment['sid']), 'storename');
		$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $comment['mid']), 'openid');
		$first = '商家回复了您的评论';
		$keyword1 = '商家[' . $storename . ']的回复';
		$keyword2 = '已回复';
		$remark = '回复内容:' . $comment['replytextone'];
		$url = app_url('store/merchant/commentpage', array('merchantid' => 7));
		Message::jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url);
		wl_message(error(0));
	}

	public function commentcheck()
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['orderid']);
		$checkone = intval($_GPC['checkone']);
		if (empty($orderid) || empty($checkone)) {
			wl_message(error(1, '缺少参数'));
		}

		pdo_update(PDO_NAME . 'comment', array('checkone' => $checkone), array('id' => $orderid));
		if ($_W['wlsetting']['creditset']['commentcredit'] && $checkone == 2) {
			$mid = pdo_getcolumn(PDO_NAME . 'comment', array('id' => $orderid), 'mid');
			Member::credit_update_credit1($mid, $_W['wlsetting']['creditset']['commentcredit'], '评价赠送积分');
		}

		wl_message(error(0));
	}

	public function halfcard()
	{
		global $_W;
		global $_GPC;
		$this->checkauthority('halfcard');

		if ($_GPC['token']) {
			$data = $_GPC['halfcard'];
			$images = $_GPC['images'];
			$data['adv'] = serialize($images);

			if ($data['level']) {
				$data['level'] = serialize($data['level']);
			}

			if ($data['datestatus'] == 1) {
				$data['week'] = serialize($data['week']);
				$data['day'] = '';
			}

			if ($data['datestatus'] == 2) {
				$data['day'] = serialize($data['day']);
				$data['week'] = '';
			}

			if (empty($data['discount'])) {
				$data['daily'] = 0;
			}
			else {
				$data['daily'] = 1;
			}

			$data['detail'] = htmlspecialchars_decode($data['detail']);
			$data['describe'] = htmlspecialchars_decode($data['describe']);
			$audits = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $_W['storeid']), 'audits');

			if ($audits) {
				$data['status'] = 1;
			}
			else {
				Store::toadmin($data['title'], $_W['storeid'], 'halfcard');
				$data['status'] = 2;
			}

			if ($_GPC['halfcardid']) {
				$res = pdo_update('wlmerchant_halfcardlist', $data, array('id' => $_GPC['halfcardid']));
			}
			else {
				$data['uniacid'] = $_W['uniacid'];
				$data['aid'] = $_W['aid'];
				$data['merchantid'] = $_W['storeid'];
				$data['createtime'] = time();
				$res = pdo_insert('wlmerchant_halfcardlist', $data);
			}

			if ($res) {
				wl_message('保存成功！', app_url('store/supervise/halfcard'), 'success');
			}
			else {
				wl_message('保存失败！', referer(), 'error');
			}
		}

		$halfcard = pdo_get('wlmerchant_halfcardlist', array('merchantid' => $_W['storeid']));

		if ($halfcard['level']) {
			$halfcard['level'] = unserialize($halfcard['level']);
		}

		$monthday = range(1, 31);
		$halfset = Setting::wlsetting_read('halfcard');
		$halfname = $halfset['text']['halfcardtext'] ? $halfset['text']['halfcardtext'] : '一卡通';
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $halfname . ' - ' . $_W['wlsetting']['base']['name'] : $halfname;

		if ($halfcard) {
			$halfcard['adv'] = unserialize($halfcard['adv']);

			if ($halfcard['datestatus'] == 1) {
				$halfcard['week'] = unserialize($halfcard['week']);
			}

			if ($halfcard['datestatus'] == 2) {
				$halfcard['day'] = unserialize($halfcard['day']);
			}
		}
		else {
			$halfcard['title'] = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $_W['storeid']), 'storename');
		}

		$levels = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halflevel') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 ORDER BY sort DESC'));
		include wl_template('store/creathalf');
	}

	public function bigpackagelist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '礼包列表 - ' . $_W['wlsetting']['base']['name'] : '礼包列表';
		$this->checkauthority('package');
		$where['merchantid'] = $_W['storeid'];

		if ($_GPC['status']) {
			$where['status'] = $_GPC['status'] != 3 ? $_GPC['status'] : 0;
		}
		else {
			$where['status'] = 1;
		}

		$packages = Util::getNumData('*', PDO_NAME . 'package', $where, 'sort DESC', 0, 0, 0);
		$packages = $packages[0];

		foreach ($packages as $key => &$val) {
			$val['a'] = app_url('store/supervise/bigpackage', array('packageid' => $val['id']));
			$val['thumb'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $_W['storeid']), 'logo');
			$val['salenum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $val['id']));
		}

		include wl_template('store/bigpackagelist');
	}

	public function bigpackage()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商户礼包 - ' . $_W['wlsetting']['base']['name'] : '商户礼包';
		$this->checkauthority('package');
		$id = $_GPC['packageid'];

		if ($_GPC['token']) {
			$data = $_GPC['package'];

			if ($data['level']) {
				$data['level'] = serialize($data['level']);
			}

			$data['describe'] = htmlspecialchars_decode($data['describe']);
			$data['datestarttime'] = strtotime($data['datestarttime']);
			$data['dateendtime'] = strtotime($data['dateendtime']);
			$audits = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $_W['storeid']), 'audits');

			if ($audits) {
				$data['status'] = 1;
			}
			else {
				Store::toadmin($data['title'], $_W['storeid'], 'package');
				$data['status'] = 2;
			}

			if ($id) {
				$res = pdo_update('wlmerchant_package', $data, array('id' => $id));
			}
			else {
				$data['uniacid'] = $_W['uniacid'];
				$data['aid'] = $_W['aid'];
				$data['merchantid'] = $_W['storeid'];
				$data['createtime'] = time();
				$res = pdo_insert('wlmerchant_package', $data);
			}

			if ($res) {
				wl_message('保存成功！', app_url('store/supervise/bigpackagelist'), 'success');
			}
			else {
				wl_message('保存失败！', referer(), 'error');
			}
		}

		$package = pdo_get('wlmerchant_package', array('id' => $id));
		$levels = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halflevel') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 ORDER BY sort DESC'));

		if ($package['level']) {
			$package['level'] = unserialize($package['level']);
		}

		include wl_template('store/creatpack');
	}

	public function couponlist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '超级券 - ' . $_W['wlsetting']['base']['name'] : '超级券';
		$this->checkauthority('coupon');
		$where['uniacid'] = $_W['uniacid'];
		$where['merchantid'] = $_W['storeid'];
		$status = $_GPC['status'] ? $_GPC['status'] : 1;

		if ($status == 1) {
			$where['status'] = 1;
		}
		else if ($status == 2) {
			$where['status'] = 0;
		}
		else {
			if ($status == 4) {
				$where['status#'] = '(2,4)';
			}
		}

		$goods = wlCoupon::getNumCoupons('*', $where, 'ID DESC', 0, 0, 0);
		$goods = $goods[0];
		$set = Setting::agentsetting_read('coupon');
		$diy = unserialize($set['coupon']);
		$diy['zkname'] = $diy['zkname'] ? $diy['zkname'] : '折扣券';
		$diy['djname'] = $diy['djname'] ? $diy['djname'] : '代金券';
		$diy['tcname'] = $diy['tcname'] ? $diy['tcname'] : '套餐券';
		$diy['tgname'] = $diy['tgname'] ? $diy['tgname'] : '团购券';
		$diy['yhname'] = $diy['yhname'] ? $diy['yhname'] : '优惠券';
		include wl_template('store/couponlist');
	}

	public function createcou()
	{
		global $_W;
		global $_GPC;
		$set = Setting::agentsetting_read('coupon');
		$meroof = Setting::agentsetting_read('meroof');
		$diy = unserialize($set['coupon']);
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '超级券 - ' . $_W['wlsetting']['base']['name'] : '超级券';
		$id = $_GPC['id'];

		if ($id) {
			$coupon = wlCoupon::getSingleCoupons($id, '*');
			$starttime = $coupon['starttime'];
			$endtime = $coupon['endtime'];
			$type = $coupon['type'];
			$description = unserialize($coupon['description']);
			$coupon['goodsdetail'] = nl2br($coupon['goodsdetail']);
		}
		else {
			$type = $_GPC['type'];
			$starttime = time();
			$endtime = strtotime('+1 month');
			$description[] = '';
		}

		switch ($type) {
		case '1':
			$typename = $diy['zkname'] ? $diy['zkname'] : '折扣券';
			break;

		case '2':
			$typename = $diy['djname'] ? $diy['djname'] : '代金券';
			break;

		case '3':
			$typename = $diy['tcname'] ? $diy['tcname'] : '套餐券';
			break;

		case '4':
			$typename = $diy['tgname'] ? $diy['tgname'] : '团购券';
			break;

		case '5':
			$typename = $diy['yhname'] ? $diy['yhname'] : '优惠券';
			break;
		}

		if ($_GPC['token']) {
			$data = $_GPC['coupon'];
			$images = $_GPC['images'];
			$data['logo'] = $images[0];
			$data['indeximg'] = $images[1];

			if (0 < $data['price']) {
				$data['is_charge'] = 1;
			}
			else {
				$data['is_charge'] = 0;
			}

			$data['starttime'] = strtotime($data['starttime']);
			$data['endtime'] = strtotime($data['endtime']);
			$detailthumb = $_GPC['thumbimages'];

			if ($detailthumb) {
				foreach ($detailthumb as $key => $th) {
					$th = tomedia($th);
					$data['goodsdetail'] .= '<img src= "' . $th . '" />';
				}
			}

			$data['goodsdetail'] = htmlspecialchars_decode($data['goodsdetail']);
			$data['description'] = serialize($_GPC['description']);

			if (empty($data['quantity'])) {
				$data['quantity'] = 100;
			}

			if (empty($data['usetimes'])) {
				$data['usetimes'] = 1;
			}

			$audits = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $_W['storeid']), 'audits');

			if ($audits) {
				$data['status'] = 1;
			}
			else {
				Store::toadmin($data['title'], $_W['storeid'], 'coupon');
				$data['status'] = 2;
			}

			if ($_GPC['couponid']) {
				$res = pdo_update('wlmerchant_couponlist', $data, array('id' => $_GPC['couponid']));
			}
			else {
				$data['uniacid'] = $_W['uniacid'];
				$data['aid'] = $_W['aid'];
				$data['merchantid'] = $_W['storeid'];
				$data['createtime'] = time();
				$data['type'] = $_GPC['typeid'];

				if (!$set['distribution']) {
					$data['isdistri'] = 1;
				}

				if (!$set['settlement']) {
					$data['independent'] = 1;
				}

				$res = pdo_insert('wlmerchant_couponlist', $data);
			}

			if ($res) {
				Tools::clearposter();
				wl_message('保存成功！', app_url('store/supervise/couponlist'), 'success');
			}
			else {
				wl_message('保存失败！', referer(), 'error');
			}
		}

		include wl_template('store/createcou');
	}

	public function dynamiclist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商户动态 - ' . $_W['wlsetting']['base']['name'] : '商户动态';
		$this->checkauthority('dynamic');
		$id = $_W['storeid'];
		include wl_template('store/dynamiclist');
	}

	public function deletedy()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$res = pdo_delete('wlmerchant_store_dynamic', array('id' => $id));

		if ($res) {
			exit(json_encode(1));
		}
		else {
			exit(json_encode(0));
		}
	}

	public function adddynamic()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '发布动态 - ' . $_W['wlsetting']['base']['name'] : '发布动态';
		$this->checkauthority('dynamic');
		$aa = pdo_get('wlmerchant_merchantdata', array('id' => $_W['storeid']), array('storename', 'audits'));
		$sum = 0;

		if ($aa['audits'] == '1') {
			$sum = 1;
		}

		if ($_W['ispost']) {
			$content = $_GPC['commenttext'];
			$thumbs = serialize($_GPC['thumbs']);
			$data = array('uniacid' => $_W['uniacid'], 'status' => 0, 'aid' => $_W['aid'], 'sid' => $_W['storeid'], 'mid' => $_W['mid'], 'content' => $content, 'imgs' => $thumbs, 'createtime' => time(), 'status' => $sum);
			$res = pdo_insert('wlmerchant_store_dynamic', $data);

			if ($res) {
				if ($aa['audits'] != '1') {
					$first = '一个商家动态等待审核，请尽快在后台审核';
					$keyword1 = '商家动态审核';
					$keyword2 = '待审核';
					$openids = pdo_getall('wlmerchant_agentadmin', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'notice' => 1), 'openid');
					$remark = '所属商家:' . $aa['storename'];

					if ($openids) {
						foreach ($openids as $key => $mem) {
							Message::jobNotice($mem['openid'], $first, $keyword1, $keyword2, $remark);
						}
					}
				}

				exit(json_encode(array('errno' => 0, 'message' => '发布成功,请等待审核')));
			}
			else {
				exit(json_encode(array('errno' => 1, 'message' => '发布失败,请重试')));
			}
		}

		include wl_template('store/adddynamic');
	}
}

?>
