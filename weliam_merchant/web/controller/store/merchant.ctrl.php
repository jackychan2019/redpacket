<?php
//dezend by http://www.sucaihuo.com/
class Merchant_WeliamController
{
	public function index()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = array('uniacid' => $_W['uniacid'], 'status' => 2, 'aid' => $_W['aid']);
		$groups = pdo_fetchall('SELECT id,name FROM ' . tablename('wlmerchant_storeusers_group') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' ORDER BY id DESC'));

		if ($_GPC['keyword']) {
			if ($_GPC['keywordtype'] == 1) {
				$where['id'] = trim($_GPC['keyword']);
			}
			else if ($_GPC['keywordtype'] == 2) {
				$where['@storename@'] = trim($_GPC['keyword']);
			}
			else if ($_GPC['keywordtype'] == 3) {
				$where['@realname@'] = trim($_GPC['keyword']);
			}
			else if ($_GPC['keywordtype'] == 4) {
				$where['mobile^tel'] = trim($_GPC['keyword']);
			}
			else {
				if ($_GPC['keywordtype'] == 5) {
					$where['salesmid'] = trim($_GPC['keyword']);
				}
			}
		}

		if ($_GPC['groupid']) {
			$where['groupid'] = $_GPC['groupid'];
		}

		if ($_GPC['enabled'] == 5) {
			$where['enabled'] = 0;
			$where['status'] = 1;
		}
		else if ($_GPC['enabled'] == 6) {
			$where['enabled'] = 0;
		}
		else {
			$where['enabled'] = $_GPC['enabled'] = $_GPC['enabled'] != '' ? $_GPC['enabled'] : 1;
		}

		$storesData = Util::getNumData('*', PDO_NAME . 'merchantdata', $where, 'id desc', $pindex, $psize, 1);
		$stores = $storesData[0];

		foreach ($stores as $key => &$value) {
			$value['logo'] = tomedia($value['logo']);

			if ($_GPC['enabled'] != 4) {
				if ($value['endtime'] < time() && !empty($value['endtime'])) {
					$res = pdo_update(PDO_NAME . 'merchantdata', array('enabled' => 3), array('uniacid' => $_W['uniacid'], 'id' => $value['id']));

					if ($res) {
						pdo_update('wlmerchant_rush_activity', array('status' => 4), array('uniacid' => $_W['uniacid'], 'sid' => $value['id']));
						pdo_update('wlmerchant_fightgroup_goods', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $value['id']));
						pdo_update('wlmerchant_couponlist', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $value['id']));
						pdo_update('wlmerchant_halfcardlist', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $value['id']));
						pdo_update('wlmerchant_package', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $value['id']));
					}
				}
			}

			$value['onelevel'] = Util::idSwitch('cateParentId', 'cateParentName', $value['onelevel']);
			$value['twolevel'] = Util::idSwitch('cateChildId', 'cateChildName', $value['twolevel']);

			if (!empty($value['cardsn'])) {
				$qrid = pdo_getcolumn(PDO_NAME . 'qrcode', array('sid' => $value['id'], 'status' => 2), 'qrid');
				$ticket = pdo_getcolumn('qrcode', array('id' => $qrid), 'ticket');
				$value['showurl'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket);
			}
		}

		$pager = $storesData[1];
		$status0 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE enabled = 0 and uniacid=' . $_W['uniacid'] . ' and status=2 and aid=' . $_W['aid']));
		$status1 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE enabled = 1 and uniacid=' . $_W['uniacid'] . ' and status=2 and aid=' . $_W['aid']));
		$status2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE enabled = 2 and uniacid=' . $_W['uniacid'] . ' and status=2 and aid=' . $_W['aid']));
		$status3 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE enabled = 3 and uniacid=' . $_W['uniacid'] . ' and status=2 and aid=' . $_W['aid']));
		$status4 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE enabled = 4 and uniacid=' . $_W['uniacid'] . ' and status=2 and aid=' . $_W['aid']));
		$status5 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE enabled = 0 and uniacid=' . $_W['uniacid'] . ' and status=1 and aid=' . $_W['aid']));
		include wl_template('store/userIndex');
	}

	public function edit()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$page = $_GPC['page'];
		$categoryes = pdo_getall(PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'state' => '0'));

		if (empty($categoryes)) {
			wl_message('请先添加商家类别!', web_url('store/category/Edit'));
		}

		$allgroups = Store::getAllGroup(0, 100, 1);
		$allgroup = $allgroups['data'];

		if (empty($allgroup)) {
			wl_message('请先添加商户分组!', web_url('store/group/edit'));
		}

		$moduels = uni_modules();

		if (!empty($categoryes)) {
			$parent = $children = array();

			foreach ($categoryes as $cid => $cate) {
				if (!empty($cate['parentid'])) {
					$children[$cate['parentid']][] = $cate;
				}
				else {
					$parent[$cate['id']] = $cate;
				}
			}
		}

		if ($id) {
			$register = Store::getSingleStore($id);
		}

		if ($register['cloudspeaker']) {
			$cloudspeaker = unserialize($register['cloudspeaker']);
		}

		$register['onelevelname'] = Util::idSwitch('cateParentId', 'cateParentName', $register['onelevel']);

		if ($id) {
			$register['twolevelname'] = Util::idSwitch('cateChildId', 'cateChildName', $register['twolevel']);
			$register['member'] = Store::getSingleRegister(array('uniacid' => $_W['uniacid'], 'storeid' => $id, 'ismain' => 1));
		}

		$member = Member::getMemberByMid($register['member']['mid'], array('id', 'nickname', 'openid'));
		$register['location'] = unserialize($register['location']);
		$register['location'] = Util::Convert_GCJ02_To_BD09($register['location']['lat'], $register['location']['lng']);
		$register['storehours'] = unserialize($register['storehours']);
		$register['endtime'] = $register['endtime'] ? $register['endtime'] : time() + 31536000;
		$register['adv'] = unserialize($register['adv']);
		$register['album'] = unserialize($register['album']);
		$register['examineimg'] = unserialize($register['examineimg']);
		$allArea = json_encode(Area::get_all_in_use());
		$sett = unserialize($register['settlementtext']);
		$on_array = array();
		$off_array = array();

		if (empty($register['score'])) {
			$register['score'] = 5;
		}

		if ($register['score'] == 1) {
			$html = '<span class="label label-default">非常差</span>';
		}
		else if ($register['score'] == 2) {
			$html = '<span class="label label-warning">不太好</span>';
		}
		else if ($register['score'] == 3) {
			$html = '<span class="label label-info">一般</span>';
		}
		else if ($register['score'] == 4) {
			$html = '<span class="label label-success">很好!</span>';
		}
		else {
			if ($register['score'] == 5) {
				$html = '<span class="label label-danger">非常棒!!</span>';
			}
		}

		$i = 1;

		while ($i <= $register['score']) {
			$on_array[$i] = $i;
			++$i;
		}

		$j = $register['score'];

		while ($j < 5) {
			$off_array[$j] = $j + 1;
			++$j;
		}

		$presettags = pdo_getall('wlmerchant_tags', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'type' => 0), array('id', 'title'), '', 'sort DESC');
		$tags = unserialize($register['tag']);

		if ($id) {
			$mains = pdo_getall('wlmerchant_merchantuser', array('uniacid' => $_W['uniacid'], 'storeid' => $id));

			foreach ($mains as $key => &$main) {
				$main['avatar'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $main['mid']), 'avatar');
			}
		}

		$logo = $register['logo'];

		if (checksubmit('submit')) {
			if ($member['openid'] != $_GPC['openid']) {
				$noticeflag = 1;
			}

			$uid = intval($_GPC['uid']);

			if (!empty($_GPC['openid'])) {
				$member = Member::getMemberByMid($_GPC['openid'], 'id');
			}

			$register = $_GPC['register'];
			$register['enabled'] = intval($_GPC['enabled']);
			$register['autocash'] = intval($_GPC['autocash']);
			$register['payonline'] = intval($_GPC['payonline']);
			$register['audits'] = intval($_GPC['audits']);
			$register['wxappswitch'] = intval($_GPC['wxappswitch']);
			$register['iscommon'] = intval($_GPC['iscommon']);
			$register['location'] = Util::Convert_BD09_To_GCJ02($register['location']['lat'], $register['location']['lng']);
			$register['location'] = serialize($register['location']);
			$register['adv'] = serialize($register['adv']);
			$register['album'] = serialize($register['album']);
			$register['tag'] = serialize($register['tag']);
			$register['storename'] = trim($register['storename']);
			$register['endtime'] = strtotime($register['endtime']);
			$register['introduction'] = htmlspecialchars_decode($register['introduction']);
			$register['onelevel'] = intval($_GPC['category']['parentid']);
			$register['twolevel'] = $_GPC['category']['childid'] ? intval($_GPC['category']['childid']) : intval($_GPC['category']['parentid']);
			$register['uniacid'] = $_W['uniacid'];
			$register['aid'] = $_W['aid'];
			$user['name'] = $register['realname'];
			$user['mobile'] = $register['tel'];

			if (!empty($_GPC['openid'])) {
				$user['mid'] = $member['id'];
			}

			$user['enabled'] = 1;
			$user['uniacid'] = $_W['uniacid'];
			$user['aid'] = $_W['aid'];
			$registerdate = $_GPC['registerdate'];
			$register['storehours'] = serialize($registerdate);
			$sett = $_GPC['sett'];
			$desett = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('id' => $register['groupid']), 'defaultrate');

			foreach ($sett as $key => &$se) {
				if (100 < $se) {
					$se = 100;
				}

				if (empty($se)) {
					$se = $desett;
				}

				if (0.01 < $se) {
					$se = sprintf('%.2f', $se);
				}
			}

			$register['settlementtext'] = serialize($sett);
			$cloudspeaker = $_GPC['cloudspeaker'];

			if ($cloudspeaker) {
				$cloudspeaker['volume'] = $cloudspeaker['volume'] ? $cloudspeaker['volume'] : 50;
			}

			$register['cloudspeaker'] = serialize($cloudspeaker);

			if ($id) {
				$result = Store::registerEditData($register, $id);

				if (empty($uid)) {
					$user['storeid'] = $id;
					$user['ismain'] = 1;
					$user['status'] = 2;
					$user['createtime'] = time();
				}

				$result2 = Store::registerEditUser($user, $uid);
			}
			else {
				$register['aid'] = $_W['aid'];
				$register['status'] = 2;
				$register['createtime'] = time();
				$uid = Store::registerEditData($register);
				$user['storeid'] = $uid;
				$user['ismain'] = 1;
				$user['status'] = 2;
				$user['createtime'] = time();
				$result = Store::registerEditUser($user);
				$id = $uid;
			}

			if ($result) {
				if ($register['enabled'] != 1 && !empty($id)) {
					pdo_update('wlmerchant_rush_activity', array('status' => 4), array('uniacid' => $_W['uniacid'], 'sid' => $id));
					pdo_update('wlmerchant_fightgroup_goods', array('status' => 5), array('uniacid' => $_W['uniacid'], 'merchantid' => $id));
					pdo_update('wlmerchant_couponlist', array('status' => 3), array('uniacid' => $_W['uniacid'], 'merchantid' => $id));
					pdo_update('wlmerchant_halfcardlist', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $id));
					pdo_update('wlmerchant_package', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $id));
				}

				if ($noticeflag) {
					$firse = '您已被管理员设置为[' . $register['storename'] . ']的店长';
					$keyword1 = '绑定店长通知';
					$keyword2 = '已绑定';
					$remark = '点击链接进入商户管理页面';
					$url = app_url('store/supervise/platform', array('storeid' => $id, 'staid' => $_W['aid']));
					Message::jobNotice($_GPC['openid'], $firse, $keyword1, $keyword2, $remark, $url);
				}

				if ($register['logo'] != $logo) {
					Tools::clearwxapp();
					Tools::clearposter();
				}

				wl_message('商家信息保存成功', web_url('store/merchant/index', array('enabled' => $register['enabled'], 'page' => $page)), 'success');
			}
			else {
				wl_message('商家信息保存失败，请重试', web_url('store/merchant/index', array('enabled' => $register['enabled'])), 'success');
			}
		}

		include wl_template('store/userEdit');
	}

	public function detelemain()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$ismain = pdo_getcolumn(PDO_NAME . 'merchantuser', array('id' => $id), 'ismain');

		if ($ismain == 1) {
			show_json(0, '不能删除店长');
		}
		else {
			$res = pdo_delete('wlmerchant_merchantuser', array('id' => $id));
		}

		if ($res) {
			show_json(1, '删除成功');
		}
		else {
			show_json(0, '删除失败，请重试');
		}
	}

	public function urlindex()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$register = Store::getSingleStore($id);
		$register['onelevelname'] = Util::idSwitch('cateParentId', 'cateParentName', $register['onelevel']);
		$register['twolevelname'] = Util::idSwitch('cateChildId', 'cateChildName', $register['twolevel']);
		$naves = Dashboard::getAllNav($pindex - 1, $psize, '', $id);
		$navs = $naves['data'];
		$pager = pagination($naves['count'], $pindex, $psize);
		include wl_template('store/userEdit');
	}

	public function addurl()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$register = Store::getSingleStore($id);
		$register['onelevelname'] = Util::idSwitch('cateParentId', 'cateParentName', $register['onelevel']);
		$register['twolevelname'] = Util::idSwitch('cateChildId', 'cateChildName', $register['twolevel']);

		if (!empty($_GPC['navid'])) {
			$nav = Dashboard::getSingleNav($_GPC['navid']);
		}

		if (checksubmit('submit')) {
			$nav = $_GPC['nav'];
			$nav['name'] = trim($nav['name']);
			$nav['displayorder'] = intval($nav['displayorder']);
			$nav['enabled'] = intval($_GPC['enabled']);

			if (!empty($_GPC['navid'])) {
				if (Dashboard::editNav($nav, $_GPC['navid'])) {
					wl_message('保存成功', web_url('store/merchant/urlindex', array('id' => $nav['merchantid'])), 'success');
				}
			}
			else {
				if (Dashboard::editNav($nav)) {
					wl_message('保存成功', web_url('store/merchant/urlindex', array('id' => $nav['merchantid'])), 'success');
				}
			}

			wl_message('保存失败', referer(), 'error');
		}

		include wl_template('store/userEdit');
	}

	public function delete()
	{
		global $_W;
		global $_GPC;

		if (pdo_update(PDO_NAME . 'merchantdata', array('enabled' => 4), array('id' => $_GPC['id']))) {
			pdo_delete('wlmerchant_collect', array('storeid' => $_GPC['id']));
			pdo_update('wlmerchant_rush_activity', array('status' => 4), array('uniacid' => $_W['uniacid'], 'sid' => $_GPC['id']));
			pdo_update('wlmerchant_fightgroup_goods', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $_GPC['id']));
			pdo_update('wlmerchant_couponlist', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $_GPC['id']));
			pdo_update('wlmerchant_halfcardlist', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $_GPC['id']));
			pdo_update('wlmerchant_package', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $_GPC['id']));
			show_json(1, '删除成功');
		}
		else {
			show_json(0, '删除失败，请重试');
		}
	}

	public function deletes()
	{
		global $_W;
		global $_GPC;
		$ids = $_GPC['ids'];
		$type = $_GPC['type'];

		if ($type == 1) {
			foreach ($ids as $key => $id) {
				pdo_update(PDO_NAME . 'merchantdata', array('enabled' => 4), array('id' => $id));
				pdo_delete('wlmerchant_collect', array('storeid' => $id));
			}
		}
		else {
			foreach ($ids as $key => $id) {
				pdo_delete(PDO_NAME . 'merchantdata', array('id' => $id));
				pdo_delete(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'storeid' => $id));
			}
		}

		exit(json_encode(array('errno' => 0, 'message' => '', 'id' => '')));
	}

	public function sureDelete()
	{
		global $_W;
		global $_GPC;

		if (pdo_delete(PDO_NAME . 'merchantdata', array('id' => $_GPC['id']))) {
			pdo_delete(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'storeid' => $_GPC['id']));
			show_json(1, '删除成功');
		}

		show_json(0, '删除失败，请重试');
	}

	/**
     * 函数的含义说明
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	public function keeper()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$register = Store::getSingleStore($id);
		$register['onelevel'] = Util::idSwitch('cateParentId', 'cateParentName', $register['onelevel']);
		$register['twolevel'] = Util::idSwitch('cateChildId', 'cateChildName', $register['twolevel']);
		$where['storeid'] = $id;
		$keeperData = Util::getNumData('*', PDO_NAME . 'merchantuser', $where, 'ismain asc');
		$keeper = $keeperData[0];

		foreach ($keeper as $key => &$value) {
			$value['member'] = Member::getMemberByMid($value['mid']);
		}

		include wl_template('store/userEdit');
	}

	/**
     * Comment: 代理后台进行店铺余额提现申请的操作
     * Author: zzw
     */
	public function cash()
	{
		global $_W;
		global $_GPC;
		$sid = $_GPC['sid'];
		$money = $_GPC['money'];
		$cashsets = Setting::wlsetting_read('cashset');
		$agent = Area::getSingleAgent($_W['aid']);
		$syssalepercent = $agent['percent']['syssalepercent'];
		$userInfo = Store::getShopOwnerInfo($sid, $_W['aid']);
		$shopIntervalTime = $cashsets['shopIntervalTime'];

		if (0 < $shopIntervalTime) {
			$startTime = pdo_fetchcolumn('SELECT applytime FROM ' . tablename(PDO_NAME . 'settlement_record') . (' WHERE sid = ' . $sid . ' AND uniacid = ' . $_W['uniacid'] . ' ORDER BY applytime DESC '));
			$interval = time() - $startTime;
			$intervalDay = $interval / 3600 / 24;
			$intercalRes = ceil($shopIntervalTime - $intervalDay);

			if (0 < $intercalRes) {
				wl_json(0, '请等' . $intercalRes . '天后再申请！');
			}
		}

		if ($money < $cashsets['lowsetmoney']) {
			wl_json(0, '申请失败，最低提现金额为' . $cashsets['lowsetmoney'] . '元。');
		}

		$data = array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'aid' => $_W['aid'], 'status' => 2, 'type' => 1, 'sapplymoney' => $money, 'sgetmoney' => sprintf('%.2f', $money - $syssalepercent * $money / 100), 'spercentmoney' => sprintf('%.2f', $syssalepercent * $money / 100), 'spercent' => $syssalepercent, 'applytime' => TIMESTAMP, 'updatetime' => TIMESTAMP, 'sopenid' => $userInfo['openid'], 'payment_type' => 5);

		if (pdo_insert(PDO_NAME . 'settlement_record', $data)) {
			$orderid = pdo_insertid();
			$res = Store::settlement($orderid, 0, $data['sid'], 0 - $money, 0, 0 - $money, 7, $orderid, 0, $_W['aid']);

			if ($res) {
				if (!empty($_W['wlsetting']['noticeMessage']['adminopenid'])) {
					$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $sid), 'storename');
					$first = '您好，有一个商户提现申请待审核。';
					$keyword1 = '商户[' . $storename . ']申请提现' . $money . '元';
					$keyword2 = '待审核';
					$remark = '请尽快前往系统后台审核';
					$url = app_url('dashboard/home/index');
					Message::jobNotice($_W['wlsetting']['noticeMessage']['adminopenid'], $first, $keyword1, $keyword2, $remark, $url);
				}

				wl_json(1, '申请成功');
			}
			else {
				wl_json(0, '申请失败，请重试');
			}
		}
		else {
			wl_json(0, '申请失败，请重试');
		}
	}

	public function moneychange()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$balance = trim($_GPC['balance']);

		if ($_W['ispost']) {
			$moneychange = trim($_GPC['moneychange']);
			$moneynum = sprintf('%.2f', trim($_GPC['moneynum']));
			$remark = trim($_GPC['remark']);

			if ($moneynum < 0) {
				show_json(0, '变更金额错误');
			}

			if ($moneychange == 1) {
				$settlementmoney = $moneynum;
			}
			else {
				$settlementmoney = 0 - $moneynum;
			}

			pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET nowmoney = nowmoney + ' . $settlementmoney . ',allmoney = allmoney + ' . $settlementmoney . ' WHERE id = ' . $id));
			$merchantnowmoney = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $id), 'nowmoney');
			Store::addcurrent(1, -1, $id, $settlementmoney, $merchantnowmoney, 0, $remark);
			show_json(1, '商户余额变更成功');
		}

		include wl_template('store/moneychange');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
