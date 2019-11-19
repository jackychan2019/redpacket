<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Coupon_app_WeliamController extends Weliam_merchantModuleSite
{
	public function getCoupon()
	{
		global $_W;
		global $_GPC;
		$couponid = $_GPC['id'];
		Member::checklogin(app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $id)));
		$coupons = wlCoupon::getSingleCoupons($couponid, '*');
		$num = wlCoupon::getCouponNum($couponid, 1);
		$halfcardflag = Member::checkhalfmember();
		$level = unserialize($coupons['level']);

		if ($coupons['vipstatus'] == 1) {
			if (empty($halfcardflag)) {
				wl_message('抱歉，您不是会员', app_url('halfcard/halfcardopen/open'), 'error');
			}
			else {
				if ($level) {
					$flag = Halfcard::checklevel($_W['mid'], $level);

					if (empty($flag)) {
						wl_message(array('errno' => 1, 'message' => '您所在的会员等级无权领取该卡券'));
					}
				}
			}
		}

		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
		if (empty($_W['wlmember']['mobile']) && in_array('coupon', $mastmobile)) {
			wl_message('未绑定手机号，去绑定？', app_url('member/user/binding', array('backurl' => urlencode($_W['siteurl']))), 'error');
		}

		if ($coupons['time_type'] == 1 && $coupons['endtime'] < time()) {
			wl_message('抱歉，优惠券已停止发放', app_url('wlcoupon/coupon_app/couponList'), 'error');
		}

		if ($coupons['status'] == 0) {
			wl_message('抱歉，优惠券已被禁用', app_url('wlcoupon/coupon_app/couponList'), 'error');
		}

		if ($coupons['status'] == 3) {
			wl_message('抱歉，优惠券已经失效', app_url('wlcoupon/coupon_app/couponList'), 'error');
		}

		if ($coupons['quantity'] - 1 < $coupons['surplus']) {
			wl_message('抱歉，优惠券已经被领光了', app_url('wlcoupon/coupon_app/couponList'), 'error');
		}

		if ($num) {
			if (($coupons['get_limit'] < $num || $num == $coupons['get_limit']) && 0 < $coupons['get_limit']) {
				wl_message('抱歉，一个用户只能领取' . $coupons['get_limit'] . '张。', app_url('wlcoupon/coupon_app/couponList'), 'error');
			}
		}

		$newsurplus = $coupons['surplus'] + 1;
		wlCoupon::updateCoupons(array('surplus' => $newsurplus), array('id' => $couponid));

		if ($coupons['time_type'] == 1) {
			$starttime = $coupons['starttime'];
			$endtime = $coupons['endtime'];
		}
		else {
			$starttime = time();
			$endtime = time() + $coupons['deadline'] * 24 * 3600;
		}

		$data = array('mid' => $_W['mid'], 'aid' => $_W['aid'], 'parentid' => $coupons['id'], 'status' => 1, 'type' => $coupons['type'], 'title' => $coupons['title'], 'sub_title' => $coupons['sub_title'], 'content' => $coupons['goodsdetail'], 'description' => $coupons['description'], 'color' => $coupons['color'], 'starttime' => $starttime, 'endtime' => $endtime, 'createtime' => time(), 'usetimes' => $coupons['usetimes'], 'concode' => Util::createConcode(4));
		$res = wlCoupon::saveMemberCoupons($data);

		if ($res) {
			$storeadminopenid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $_W['mid']), 'openid');
			$first = '您有一个新的优惠券通知';
			$acname = '优惠券到账';
			$acresult = '已获取';
			$remark = '';
			$url = app_url('wlcoupon/coupon_app/coupondetail', array('id' => $res));
			Message::jobNotice($storeadminopenid, $first, $acname, $acresult, $remark, $url);
			wl_message('恭喜你获得一张优惠券', app_url('wlcoupon/coupon_app/couponList'), 'success');
		}
		else {
			wl_message('获取优惠券失败', app_url('wlcoupon/coupon_app/couponList'), 'error');
		}
	}

	public function createCouponorder()
	{
		global $_W;
		global $_GPC;
		$base = Setting::agentsetting_read('coupon');

		if ($_W['ispost']) {
			$couponid = $_GPC['id'];
			$coupons = wlCoupon::getSingleCoupons($couponid, '*');
			$member = Util::getSingelData('mobile', PDO_NAME . 'member', array('id' => $_W['mid']));
			$ifMobile = $member['mobile'] ? true : false;
			$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
			if (!$ifMobile && in_array('coupon', $mastmobile)) {
				wl_message(array('errno' => 2, 'message' => '未绑定手机号，去绑定？'));
			}

			$num = wlCoupon::getCouponNum($couponid, 2);
			$couponnum = $_GPC['num'];
			$allnum = $num + $couponnum;
			if ($coupons['time_type'] == 1 && $coupons['endtime'] < time()) {
				wl_message(array('errno' => 1, 'message' => '抱歉，优惠券已停止发放'));
			}

			if ($coupons['quantity'] - $coupons['surplus'] < $couponnum) {
				wl_message(array('errno' => 1, 'message' => '抱歉，优惠券库存不足'));
			}

			if ($coupons['get_limit'] < $allnum && 0 < $coupons['get_limit']) {
				wl_message(array('errno' => 1, 'message' => '抱歉，一个用户只能购买' . $coupons['get_limit'] . '张,您已购买' . $num . '张。'));
			}

			$halfcardflag = Member::checkhalfmember();
			if ($coupons['vipstatus'] == 2 && $halfcardflag) {
				$coupons['price'] = $coupons['vipprice'];
				$vipbuyflag = 1;
			}

			if (empty($vipbuyflag)) {
				$vipbuyflag = 0;
			}

			$settlementmoney = Store::getsettlementmoney(4, $couponid, $couponnum, $coupons['merchantid'], $vipbuyflag);
			$data = array('uniacid' => $coupons['uniacid'], 'mid' => $_W['mid'], 'aid' => $coupons['aid'], 'fkid' => $couponid, 'sid' => $coupons['merchantid'], 'status' => 0, 'paytype' => 2, 'createtime' => time(), 'orderno' => createUniontid(), 'price' => $coupons['price'] * $couponnum, 'num' => $couponnum, 'plugin' => 'coupon', 'payfor' => 'couponsharge', 'vipbuyflag' => $vipbuyflag, 'goodsprice' => $coupons['price'] * $couponnum, 'settlementmoney' => $settlementmoney, 'neworderflag' => 1);
			$res = wlCoupon::saveCouponOrder($data);

			if ($res) {
				wl_message(array('errno' => 0, 'message' => $res));
			}
			else {
				wl_message(array('errno' => 1, 'message' => '未知错误，请重试。'));
			}
		}

		$orderId = $_GPC['orderId'];
		$order = pdo_get('wlmerchant_order', array('id' => $orderId));

		if (empty($order)) {
			wl_message('传入的参数有误，请重试');
		}

		$coupon = wlCoupon::getSingleCoupons($order['fkid'], 'title');
		$bankrid = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), 'bankrid');
		$params = array('tid' => $order['orderno'], 'ordersn' => $order['orderno'], 'title' => $coupon['title'], 'fee' => $order['price'], 'bankrid' => $bankrid);
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => 'Rush', 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'wlCoupon', 'payfor' => 'Couponsharge', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}

	public function couponList()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '我的卡券 - ' . $_W['wlsetting']['base']['name'] : '我的卡券';
		$status = $_GPC['status'];

		if ($status == '') {
			$status = 1;
		}

		if (p('diypage')) {
			if (!empty($_W['agentset']['diypageset']['adv_wlcoupon'])) {
				$advdata = Diy::BeOverdue($_W['agentset']['diypageset']['adv_wlcoupon']);
			}

			if (!empty($_W['agentset']['diypageset']['menu_wlcoupon'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_wlcoupon']);
			}
		}

		$page_type = 5;
		include wl_template('couponhtml/couponlist');
	}

	public function couponDetail()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '卡券详情 - ' . $_W['wlsetting']['base']['name'] : '卡券详情';
		$id = $_GPC['id'];
		$orderno = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $id), 'orderno');

		if ($orderno) {
			$order = pdo_get(PDO_NAME . 'order', array('orderno' => $orderno), array('status', 'id', 'neworderflag', 'applyrefund'));

			if ($order['status'] == 6) {
				wl_message('卡券已退款');
			}

			if ($order['applyrefund']) {
				wl_message('卡券申请退款中');
			}

			if ($order['neworderflag']) {
				$smalls = pdo_getall('wlmerchant_smallorder', array('orderid' => $order['id'], 'plugin' => 'coupon'), array('checkcode', 'status'));
			}
		}
		else {
			$checkcode = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $id), 'concode');
		}

		include wl_template('couponhtml/coupondetail');
	}

	public function getcoupons()
	{
		global $_W;
		global $_GPC;

		if ($_GPC['status']) {
			$status = $_GPC['status'];
		}
		else {
			$status = 1;
		}

		$where['mid'] = $_W['mid'];

		if ($status == 1) {
			$where['usetimes>'] = 1;
			$where['endtime>'] = time();
		}
		else if ($status == 2) {
			$where['usetimes'] = 0;
		}
		else {
			$where['usetimes>'] = 1;
			$where['endtime<'] = time();
		}

		$couponlist = wlCoupon::getNumCoupon('*', $where, 'ID DESC', 0, 0, 0);
		$couponlist = $couponlist[0];

		foreach ($couponlist as $key => &$v) {
			if ($v['type'] == 1) {
				$v['type'] = '折扣券';
			}
			else if ($v['type'] == 2) {
				$v['type'] = '代金券';
			}
			else if ($v['type'] == 3) {
				$v['type'] = '套餐券';
			}
			else if ($v['type'] == 4) {
				$v['type'] = '团购券';
			}
			else {
				if ($v['type'] == 5) {
					$v['type'] = '其他券';
				}
			}

			$v['endtime'] = date('Y-m-d H:i:s', $v['endtime']);
			$parent = pdo_get('wlmerchant_couponlist', array('id' => $v['parentid']), array('logo', 'merchantid'));
			$store = pdo_get('wlmerchant_merchantdata', array('id' => $parent['merchantid']), array('storename'));
			$v['storename'] = $store['storename'];
			$v['url'] = app_url('wlcoupon/coupon_app/couponDetail', array('id' => $v['id']));
			$v['storeurl'] = app_url('store/merchant/detail', array('id' => $parent['merchantid']));

			if ($v['orderno']) {
				$v['applyrefund'] = pdo_getcolumn(PDO_NAME . 'order', array('orderno' => $v['orderno']), 'applyrefund');
			}
			else {
				$v['applyrefund'] = 0;
			}
		}

		exit(json_encode(array('errno' => 0, 'data' => $couponlist)));
	}

	public function getcoupondetail()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$res = wlCoupon::getSingleCoupon($id, '*');
		$res['timess'] = $res['endtime'];
		$res['starttime'] = date('Y-m-d H:i:s', $res['starttime']);
		$res['endtime'] = date('Y-m-d H:i:s', $res['endtime']);

		if ($res['orderno']) {
			$order = pdo_get('wlmerchant_order', array('orderno' => $res['orderno']), array('id', 'neworderflag', 'sid'));
			$res['neworderflag'] = $order['neworderflag'];
		}

		if ($order['neworderflag']) {
			$alsmalls = pdo_getall('wlmerchant_smallorder', array('orderid' => $order['id'], 'plugin' => 'coupon', 'status' => 2), array('hexiaotime'));

			if (!empty($alsmalls)) {
				foreach ($alsmalls as $key => &$v) {
					$v = date('Y-m-d H:i:s', $v['hexiaotime']);
				}
			}

			$res['usedtime'] = $alsmalls;
			$res['usetimes'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE orderid = ' . $order['id'] . ' AND plugin = \'coupon\' AND status = 1'));
		}
		else {
			if ($res['usedtime']) {
				$res['usedtime'] = unserialize($res['usedtime']);

				foreach ($res['usedtime'] as $key => &$v) {
					$v = date('Y-m-d H:i:s', $v['time']);
				}
			}
		}

		$parent = wlCoupon::getSingleCoupons($res['parentid'], 'merchantid,logo');
		$store = pdo_get('wlmerchant_merchantdata', array('id' => $parent['merchantid']));
		$res['couponlogo'] = tomedia($parent['logo']);
		$res['storename'] = $store['storename'];
		$res['storelogo'] = tomedia($store['logo']);
		$url = app_url('wlcoupon/coupon_app/hexiaocoupon', array('id' => $id));
		$res['qrimgurl'] = app_url('store/merchant/qrcodeimg', array('url' => $url));
		$res['description'] = unserialize($res['description']);

		if ($store['verkey']) {
			$res['verkeyflag'] = 1;
		}

		exit(json_encode(array('errno' => 0, 'data' => $res)));
	}

	public function couponslist()
	{
		global $_W;
		global $_GPC;

		if ($_W['agentset']['diypageset']['page_wlcoupon']) {
			header('Location:' . app_url('diypage/diyhome/home', array('aid' => $_W['aid'], 'id' => $_W['agentset']['diypageset']['page_wlcoupon'])));
			exit();
		}

		$base = Setting::agentsetting_read('coupon');
		$searchSet = array('search_float' => $base['search_float'], 'search_bgColor' => $base['search_bgColor']);
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '优惠券列表 - ' . $_W['wlsetting']['base']['name'] : '优惠券列表';
		$advs = pdo_getall('wlmerchant_adv', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'enabled' => 1, 'type' => 2));
		$storecategory = pdo_fetchall('SELECT id,name FROM ' . tablename('wlmerchant_category_store') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND parentid = 0 AND enabled = 1 ORDER BY displayorder DESC'));
		$opennum = count($storecategory);
		$coupon = unserialize($base['coupon']);
		$coupons[] = array('name' => '折扣券', 'status' => $coupon['zkstatus'], 'id' => 1, 'order' => $coupon['zkorder'], 'newname' => $coupon['zkname'], 'img' => $coupon['zkimg'] ? $coupon['zkimg'] : URL_MODULE . 'plugin/wlcoupon/app/resource/images/2.png');
		$coupons[] = array('name' => '代金券', 'status' => $coupon['djstatus'], 'id' => 2, 'order' => $coupon['djorder'], 'newname' => $coupon['djname'], 'img' => $coupon['djimg'] ? $coupon['djimg'] : URL_MODULE . 'plugin/wlcoupon/app/resource/images/3.png');
		$coupons[] = array('name' => '套餐券', 'status' => $coupon['tcstatus'], 'id' => 3, 'order' => $coupon['tcorder'], 'newname' => $coupon['tcname'], 'img' => $coupon['tcimg'] ? $coupon['tcimg'] : URL_MODULE . 'plugin/wlcoupon/app/resource/images/4.png');
		$coupons[] = array('name' => '团购券', 'status' => $coupon['tgstatus'], 'id' => 4, 'order' => $coupon['tgorder'], 'newname' => $coupon['tgname'], 'img' => $coupon['tgimg'] ? $coupon['tgimg'] : URL_MODULE . 'plugin/wlcoupon/app/resource/images/5.png');
		$coupons[] = array('name' => '优惠券', 'status' => $coupon['yhstatus'], 'id' => 5, 'order' => $coupon['yhorder'], 'newname' => $coupon['yhname'], 'img' => $coupon['yhimg'] ? $coupon['yhimg'] : URL_MODULE . 'plugin/wlcoupon/app/resource/images/1.png');
		$i = 0;

		while ($i < count($coupons) - 1) {
			$j = 0;

			while ($j < count($coupons) - 1 - $i) {
				if ($coupons[$j]['order'] < $coupons[$j + 1]['order']) {
					$temp = $coupons[$j + 1];
					$coupons[$j + 1] = $coupons[$j];
					$coupons[$j] = $temp;
				}

				++$j;
			}

			++$i;
		}

		$typeid = $_GPC['typeid'];
		if ($base['share_title'] || $base['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];

			if ($base['share_title']) {
				$title = $base['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[系统名称]', $sysname, $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}
			else {
				$_W['wlsetting']['share']['share_title'] = !empty($base['share_title']) ? $base['share_title'] : $_W['wlsetting']['share']['share_title'];
			}

			if ($base['share_desc']) {
				$desc = $base['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[系统名称]', $sysname, $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
			else {
				$_W['wlsetting']['share']['share_desc'] = !empty($base['share_desc']) ? $base['share_desc'] : $_W['wlsetting']['share']['share_desc'];
			}
		}
		else {
			$_W['wlsetting']['share']['share_title'] = !empty($base['share_title']) ? $base['share_title'] : $_W['wlsetting']['share']['share_title'];
			$_W['wlsetting']['share']['share_desc'] = !empty($base['share_desc']) ? $base['share_desc'] : $_W['wlsetting']['share']['share_desc'];
		}

		$_W['wlsetting']['share']['share_image'] = !empty($base['share_image']) ? $base['share_image'] : $_W['wlsetting']['share']['share_image'];

		if (p('diypage')) {
			if (!empty($_W['agentset']['diypageset']['adv_wlcoupon'])) {
				$advdata = Diy::BeOverdue($_W['agentset']['diypageset']['adv_wlcoupon']);
			}

			if (!empty($_W['agentset']['diypageset']['menu_wlcoupon'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_wlcoupon']);
			}
		}

		$page_type = 5;
		include wl_template('couponhtml/couponslist');
	}

	public function couponsdetail()
	{
		global $_W;
		global $_GPC;
		$type = 'coupon';
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '卡券详情 - ' . $_W['wlsetting']['base']['name'] : '卡券详情';
		$id = $_GPC['id'];
		$coupons = wlCoupon::getSingleCoupons($id, '*');
		$goods = $coupons;
		pdo_fetch('update' . tablename('wlmerchant_couponlist') . ('SET pv=pv+1 WHERE id = ' . $id));
		pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET pv=pv+1 WHERE id = ' . $coupons['merchantid']));
		$store = pdo_get('wlmerchant_merchantdata', array('id' => $coupons['merchantid']));

		if ($store['enabled'] != 1) {
			pdo_update('wlmerchant_couponlist', array('status' => 0), array('id' => $id));
			$coupons['status'] = 0;
		}

		$store['storehours'] = unserialize($store['storehours']);
		$coupons['description'] = unserialize($coupons['description']);
		$coupons['storename'] = $store['storename'];
		$coupons['storelogo'] = tomedia($store['logo']);
		$surplus = $coupons['quantity'] - $coupons['surplus'];

		if ($coupons['time_type']) {
			$coupons['starttime'] = date('y-m-d', $coupons['starttime']);
			$coupons['endtime'] = date('y-m-d', $coupons['endtime']);
		}

		$usetimes = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_member_coupons') . (' WHERE parentid = ' . $id));
		$heads = pdo_fetchall('SELECT mid FROM ' . tablename('wlmerchant_member_coupons') . ('WHERE parentid = ' . $id . ' ORDER BY id DESC limit 0,5'));

		foreach ($heads as $key => &$head) {
			$head['avatar'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $head['mid']), 'avatar');
		}

		$location = unserialize($store['location']);
		if ($_SESSION['lng'] && $_SESSION['lat']) {
			$distance = Store::getdistance($_SESSION['lng'], $_SESSION['lat'], $location['lng'], $location['lat']);

			if (1000 < $distance) {
				$distance = floor($distance / 1000 * 10) / 10 . 'km';
			}
			else {
				$distance = round($distance) . 'm';
			}
		}
		else {
			$distance = '未定位';
		}

		$halfcardflag = Member::checkhalfmember();
		$base = Setting::agentsetting_read('coupon');

		if (empty($base['noshare'])) {
			$noshare = 1;
		}

		$_W['wlsetting']['share']['share_title'] = !empty($coupons['title']) ? $coupons['title'] : $base['share_title'];
		$_W['wlsetting']['share']['share_desc'] = !empty($coupons['sub_title']) ? $coupons['sub_title'] : $base['share_desc'];
		$_W['wlsetting']['share']['share_image'] = !empty($coupons['logo']) ? $coupons['logo'] : $base['share_image'];
		include wl_template('couponhtml/couponsdetail2');
	}

	public function tips()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		wl_message('请点选下方按钮领取或购买', app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $id)), 'error');
	}

	public function topayCoupon()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '购买卡券 - ' . $_W['wlsetting']['base']['name'] : '购买卡券';
		$id = $_GPC['id'];
		Member::checklogin(app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $id)));
		$coupons = wlCoupon::getSingleCoupons($id, '*');

		if ($coupons['status'] != 1) {
			wl_message(array('errno' => 1, 'message' => '卡券已下架，无法购买'));
		}

		$member = pdo_get('wlmerchant_member', array('id' => $_W['mid']), array('mobile', 'vipstatus'));

		if ($member['mobile']) {
			$mobile = substr($member['mobile'], 0, 3) . '****' . substr($member['mobile'], -4, 4);
		}
		else {
			$mobile = '未绑定手机';
		}

		$halfcardflag = Member::checkhalfmember();
		$level = unserialize($coupons['level']);

		if ($coupons['vipstatus'] == 1) {
			if (empty($halfcardflag)) {
				wl_message('抱歉，您不是会员', app_url('halfcard/halfcardopen/open'), 'error');
			}
			else {
				if ($level) {
					$flag = Halfcard::checklevel($_W['mid'], $level);

					if (empty($flag)) {
						wl_message(array('errno' => 1, 'message' => '您所在的会员等级无权购买该卡券'));
					}
				}
			}
		}

		if ($coupons['vipstatus'] == 2 && $halfcardflag) {
			$coupons['price'] = $coupons['vipprice'];
		}

		$url = app_url('wlcoupon/coupon_app/topayCoupon', array('id' => $id));
		include wl_template('couponhtml/topaycoupon');
	}

	public function hexiaocoupon()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '订单核销 - ' . $_W['wlsetting']['base']['name'] : '订单核销';
		$id = $_GPC['id'];
		$record = pdo_get('wlmerchant_member_coupons', array('id' => $id));

		if (empty($record['orderno'])) {
			if (0 < $record['usetimes']) {
				$order_out['status'] = 1;
				$order_out['num'] = 1;
				$order_out['sid'] = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $record['parentid']), 'merchantid');
			}
		}
		else {
			$order_out = pdo_get('wlmerchant_order', array('orderno' => $record['orderno']));
		}

		$verifier = SingleMerchant::verifier($order_out['sid'], $_W['mid']);
		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $order_out['sid']));
		$goods = pdo_get('wlmerchant_couponlist', array('id' => $record['parentid']));
		$goods['thumb'] = $goods['logo'];
		$goods['name'] = $goods['title'];

		if ($order_out['neworderflag']) {
			$order_out['levelnum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'coupon\' AND  orderid = ' . $order_out['id'] . ' AND status != 1'));
			$order_out['usetimes'] = intval($order_out['num'] * $goods['usetimes'] - $order_out['levelnum']);
		}
		else {
			$order_out['usetimes'] = $record['usetimes'];
			$order_out['levelnum'] = intval($order_out['num'] * $goods['usetimes'] - $order_out['usetimes']);
		}

		$type = 'wlcoupon';
		include wl_template('order/hexiaoorder');
	}

	public function hexiaokey()
	{
		global $_W;
		global $_GPC;
		$verkey = $_GPC['verkey'];
		$id = $_GPC['orderid'];
		$coupon = wlCoupon::getSingleCoupon($id, 'mid,status,usedtime,endtime,starttime,usetimes,parentid,orderno,title,concode,aid');

		if (empty($num)) {
			$num = 1;
		}

		$merchantid = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $coupon['parentid']), 'merchantid');
		$merchantkey = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $merchantid), 'verkey');

		if ($verkey == $merchantkey) {
			$res = wlCoupon::hexiaoorder($id, $_W['mid'], $num, 4);

			if ($res) {
				exit(json_encode(array('errno' => 0, 'message' => '核销成功')));
			}
			else {
				exit(json_encode(array('errno' => 1, 'message' => '使用优惠券失败')));
			}
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '密码错误')));
		}
	}

	public function hexiaocouponing()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$num = $_GPC['num'];
		$coupon = wlCoupon::getSingleCoupon($id, 'mid,status,usedtime,endtime,starttime,usetimes,parentid,orderno,title,concode,aid');

		if (empty($num)) {
			$num = 1;
		}

		$parent = wlCoupon::getSingleCoupons($coupon['parentid'], 'merchantid');
		$verifier = SingleMerchant::verifier($parent['merchantid'], $_W['mid']);

		if ($verifier) {
			$res = wlCoupon::hexiaoorder($id, $_W['mid'], $num, 2);

			if ($res) {
				show_json(1, '核销优惠券成功');
			}
			else {
				show_json(0, '核销优惠券失败,请重试');
			}
		}
		else {
			show_json(0, '非管理员无法核销');
		}
	}

	public function buysuccess()
	{
		global $_W;
		global $_GPC;
		$orderno = $_GPC['orderno'];
		$paidprid = pdo_get('wlmerchant_order', array('orderno' => $orderno), array('paidprid'));

		if ($paidprid) {
			header('location:' . app_url('paidpromotion/paidapp/paiddetail', array('id' => $paidprid)));
		}
		else {
			header('location:' . app_url('wlcoupon/coupon_app/couponList'));
		}
	}

	/**
     * Comment: 首页选项卡卡卷信息
     * Author: zzw
     */
	public function getstore()
	{
		global $_W;
		global $_GPC;
		$pagetype = $_GPC['pagetype'];
		$lng = $_GPC['lng'];
		$lat = $_GPC['lat'];
		$_SESSION['lng'] = $lng;
		$_SESSION['lat'] = $lat;
		$typeid = $_GPC['type'];
		$storetype = $_GPC['storetype'];
		$where = ' a.uniacid = ' . $_W['uniacid'] . ' AND a.aid IN  (0,' . $_W['aid'] . ') AND b.uniacid = ' . $_W['uniacid'] . ' AND b.aid = ' . $_W['aid'];
		$where .= ' AND a.status = 1 AND is_show = 0 AND b.status = 2 AND b.enabled = 1 ';

		if ($storetype) {
			$where .= ' AND b.onelevel = ' . $storetype . ' ';
		}

		if ($typeid && $typeid != 'undefined') {
			$where .= ' AND a.type = ' . $typeid . ' ';
		}

		$plugin = Setting::agentsetting_read('pluginlist');
		$plugin['kqlimit'] = $plugin['kqlimit'] ? $plugin['kqlimit'] : 10;

		switch ($plugin['kqsort']) {
		case '1':
			$where .= ' ORDER BY a.createtime DESC ';
			$limit = ' LIMIT 0,' . $plugin['kqlimit'] . ' ';
			break;

		case '2':
			$sort_key = 'distance';
			$sort_order = SORT_ASC;
			break;

		case '3':
			$where .= ' ORDER BY  a.indexorder DESC ';
			$limit = ' LIMIT 0,' . $plugin['kqlimit'] . ' ';
			break;

		case '4':
			$where .= ' ORDER BY  a.pv DESC ';
			$limit = ' LIMIT 0,' . $plugin['kqlimit'] . ' ';
			break;

		default:
			$sort_key = 'distance';
			$sort_order = SORT_ASC;
			break;
		}

		$field = ' a.id,a.title,a.logo,a.indeximg,a.is_charge,a.vipstatus,a.vipprice,a.price,a.surplus,a.quantity,a.pv,a.sub_title, ';
		$field .= 'b.storename,b.location ';
		$info = pdo_fetchall('SELECT ' . $field . ' FROM ' . tablename(PDO_NAME . 'couponlist') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.merchantid = b.id  WHERE ' . $where . ' ' . $limit));

		if ($_W['mid']) {
			$halfcardflag = Member::checkhalfmember();
		}

		$extwhere['uniacid'] = $_W['uniacid'];
		$extwhere['aid'] = 0;
		$extwhere['extflag'] = 1;
		$extwhere['status'] = 1;
		$extwhere['is_indexshow'] = 0;
		$info2 = pdo_getall('wlmerchant_couponlist', $extwhere);

		if ($info2) {
			$info = array_merge($info2, $info);
		}

		foreach ($info as $k => &$asd) {
			$asd['logo'] = tomedia($asd['logo']);
			$asd['indeximg'] = tomedia($asd['indeximg']);

			if ($asd['is_charge'] == 1) {
				if ($asd['vipstatus'] == 2 && $halfcardflag) {
					$asd['charge'] = $asd['vipprice'];
					$asd['oldcharge'] = $asd['price'];
				}
				else {
					$asd['charge'] = $asd['price'];
				}
			}
			else {
				$asd['charge'] = 0;
			}

			$asd['href'] = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $asd['id']));

			if (empty($asd['quantity'])) {
				$asd['quantity'] = 1;
			}

			$asd['rate'] = round($asd['surplus'] / $asd['quantity'] * 100);

			if ($asd['location']) {
				$location = unserialize($asd['location']);
				$asd['distance'] = Store::getdistance($location['lng'], $location['lat'], $lng, $lat);
			}
			else {
				$extinfo = unserialize($asd['extinfo']);
				$asd['storename'] = $extinfo['storename'];
				$asd['distance'] = 0;
			}
		}

		if ($sort_key == 'distance' && !$pagetype) {
			$sortList = array_column($info, 'distance');
			array_multisort($sortList, SORT_DESC, $info);

			if ($_GPC['pageflag'] == 1) {
				$info = array_slice($info, 0, $plugin['kqlimit']);
			}
		}

		exit(json_encode($info));
	}

	/**
     * Comment: 获取卡卷列表信息
     * Author: zzw
     */
	public function getListInfo()
	{
		global $_W;
		global $_GPC;
		$lng = $_GPC['lng'];
		$lat = $_GPC['lat'];
		$_SESSION['lng'] = $lng;
		$_SESSION['lat'] = $lat;
		$typeid = $_GPC['type'];
		$storetype = $_GPC['storetype'];
		$parm = array('uniacid' => $_W['uniacid'], 'status' => 2, 'enabled' => 1, 'aid' => $_W['aid']);
		$pindex = max(1, intval($_GPC['page']));

		if ($_W['wlsetting']['halfcard']['halfcardtype'] == 2) {
			$halfcardflag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $_W['mid'] . ' AND aid = ' . $_W['aid'] . ' AND expiretime > ' . $now . ' AND disable != 1'));
		}
		else {
			$halfcardflag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $_W['mid'] . ' AND expiretime > ' . $now . ' AND disable != 1'));
		}

		if ($storetype) {
			$parm['onelevel'] = intval($storetype);
		}

		$couponlist = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_couponlist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid']));

		if ($couponlist) {
			$ids = '(';

			foreach ($couponlist as $key => $v) {
				if ($key == 0) {
					$ids .= $v['merchantid'];
				}
				else {
					$ids .= ',' . $v['merchantid'];
				}
			}

			$ids .= ')';
			$parm['id#'] = $ids;
		}
		else {
			$parm['id#'] = '(0)';
		}

		$locations = wlCoupon::getNumstore('*', $parm, 'ID DESC', 0, 0, 0);
		$locations = $locations[0];
		$locations = wlCoupon::getstores($locations, $lng, $lat);
		$where = array('status' => 1, 'is_show' => 0);
		if ($typeid && $typeid != 'undefined') {
			$where['type'] = $typeid;
		}

		foreach ($locations as $key => &$v) {
			$where['merchantid'] = $v['id'];
			$coupon = pdo_getall(PDO_NAME . 'couponlist', $where);

			if ($coupon) {
				foreach ($coupon as $k => &$asd) {
					$asd['storename'] = $v['storename'];
					$asd['distance'] = $v['distance'];
					$asd['logo'] = tomedia($asd['logo']);
					$asd['indeximg'] = tomedia($asd['indeximg']);

					if ($asd['is_charge'] == 1) {
						if ($asd['vipstatus'] == 2 && $halfcardflag) {
							$asd['charge'] = $asd['vipprice'];
							$asd['oldcharge'] = $asd['price'];
						}
						else {
							$asd['charge'] = $asd['price'];
						}
					}
					else {
						$asd['charge'] = 0;
					}

					$asd['href'] = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $asd['id']));
					$asd['rate'] = round($asd['surplus'] / $asd['quantity'] * 100);

					if ($asd['time_type'] == 1) {
						if (time() < $asd['endtime']) {
							$newlocations[] = $asd;
						}
					}
					else {
						$newlocations[] = $asd;
					}
				}
			}
		}

		$plugin = Setting::agentsetting_read('pluginlist');

		switch ($plugin['kqsort']) {
		case '1':
			$sort_key = 'createtime';
			$sort_order = SORT_DESC;
			break;

		case '2':
			$sort_key = 'distance';
			$sort_order = SORT_ASC;
			break;

		case '3':
			$sort_key = 'indexorder';
			$sort_order = SORT_DESC;
			break;

		case '4':
			$sort_key = 'pv';
			$sort_order = SORT_DESC;
			break;

		default:
			$sort_key = 'distance';
			$sort_order = SORT_DESC;
			break;
		}

		$newlocations = Store::wl_sort($newlocations, $sort_key, $sort_order);

		if ($_GPC['pageflag'] == 1) {
			$plugin = Setting::agentsetting_read('pluginlist');
			$limit = $plugin['kqlimit'] ? $plugin['kqlimit'] : 10;
			$locations = array_slice($locations, 0, $limit);
		}

		exit(json_encode($newlocations));
	}
}

?>
