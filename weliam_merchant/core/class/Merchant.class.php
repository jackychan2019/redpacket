<?php
//dezend by http://www.sucaihuo.com/
class Merchant
{
	/**
     * 异步支付结果回调 ，处理业务逻辑
     *
     * @access public
     * @name
     * @param mixed  参数一的说明
     * @return array
     */
	static public function payVipNotify($params)
	{
		global $_W;
		Util::wl_log('vip_notify', PATH_DATA . 'merchant/data/', $params);
		$data = self::getVipPayData($params);
		pdo_update(PDO_NAME . 'vip_record', $data, array('orderno' => $params['tid']));
		$order_out = pdo_get(PDO_NAME . 'vip_record', array('orderno' => $params['tid']));

		if ($order_out['disorderid']) {
			pdo_update('wlmerchant_disorder', array('status' => 1), array('id' => $order_out['disorderid'], 'status' => 0));
		}

		$memberData = array('level' => 1, 'vipstatus' => 1, 'vipleveldays' => $order_out['howlong'] * 30, 'lastviptime' => $order_out['limittime'], 'areaid' => $order_out['areaid'], 'aid' => $order_out['aid']);
		Message::paySuccess($order_out['openid'], $order_out['price'], '购买VIP服务', '');
		$url = app_url('member/vip/open');
		Message::beVip($order_out['openid'], $order_out['id'], $url);
		pdo_update(PDO_NAME . 'member', $memberData, array('id' => $order_out['mid']));

		if ($order_out['is_half']) {
			$mdata = array('uniacid' => $_W['uniacid'], 'mid' => $order_out['mid']);

			if ($_W['wlsetting']['halfcard']['halfcardtype'] != 1) {
				$mdata['aid'] = $_W['aid'];
			}

			$halfInfo = Util::getSingelData('*', PDO_NAME . 'halfcardmember', $mdata);
			$lastviptime = $halfInfo['expiretime'];
			if ($lastviptime && time() < $lastviptime) {
				$limittime = $lastviptime + $order_out['howlong'] * 24 * 60 * 60;
			}
			else {
				$limittime = time() + $order_out['howlong'] * 24 * 60 * 60;
			}

			$halfcarddata = array('uniacid' => $_W['uniacid'], 'aid' => $order_out['aid'], 'mid' => $order_out['mid'], 'expiretime' => $limittime, 'createtime' => time());

			if ($halfInfo) {
				pdo_update(PDO_NAME . 'halfcardmember', $halfcarddata, array('mid' => $order_out['mid']));
			}
			else {
				pdo_insert(PDO_NAME . 'halfcardmember', $halfcarddata);
			}

			$member = pdo_get('wlmerchant_member', array('id' => $halfcarddata['mid']), array('openid', 'mobile'));
			$openid = $member['openid'];
			$mobile = $member['mobile'];
			$url = app_url('halfcard/halfcard_app/userhalfcard');
			$time = date('Y-m-d H:i:s', $halfcarddata['expiretime']);
			Message::openSuccessNotice($openid, '一卡通', $time, $mobile, $url);
		}

		if ($order_out['todistributor']) {
			$disset = pdo_get(PDO_NAME . 'agentsetting', array('key' => 'distribution', 'uniacid' => $_W['uniacid'], 'aid' => $order_out['aid']), array('value'));

			if (is_array($disset)) {
				$disset = iunserializer($disset['value']);
			}
			else {
				$disset = array();
			}

			if ($disset['switch']) {
				$distributor = pdo_get('wlmerchant_distributor', array('mid' => $order_out['mid']));

				if ($distributor) {
					if (empty($distributor['disflag'])) {
						pdo_update('wlmerchant_distributor', array('disflag' => 1), array('mid' => $order_out['mid']));
					}
				}
				else {
					$member = pdo_get('wlmerchant_member', array('id' => $order_out['mid']), array('mobile', 'nickname', 'realname'));
					$data = array('uniacid' => $_W['uniacid'], 'aid' => $order_out['aid'], 'mid' => $order_out['mid'], 'createtime' => time(), 'disflag' => 1, 'nickname' => $member['nickname'], 'mobile' => $member['mobile'], 'realname' => $member['realname'], 'leadid' => -1);
					pdo_insert('wlmerchant_distributor', $data);
					$disid = pdo_insertid();
					pdo_update('wlmerchant_member', array('distributorid' => $disid), array('id' => $order_out['mid']));
				}
			}
		}
	}

	/**
     * 函数的含义说明
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function payVipReturn($params)
	{
		global $_W;
		Util::wl_log('Vip_return', PATH_DATA . 'merchant/data/', $params);
		$order_out = pdo_get(PDO_NAME . 'vip_record', array('orderno' => $params['tid']), array('id'));
		header('location:' . app_url('member/vip/vipSuccess', array('orderid' => $order_out['id'])));
	}

	/**
     * 异步支付结果回调 ，处理业务逻辑
     *
     * @access public
     * @name
     * @param mixed  参数一的说明
     * @return array
     */
	static public function payHalfcardNotify($params)
	{
		global $_W;
		Util::wl_log('vip_notify', PATH_DATA . 'merchant/data/', $params);
		$order_out = pdo_get(PDO_NAME . 'halfcard_record', array('orderno' => $params['tid']));

		if ($order_out['status'] == 0) {
			$data = self::getVipPayData($params);
			$halftype = pdo_get('wlmerchant_halfcard_type', array('id' => $order_out['typeid']));

			if (file_exists(IA_ROOT . '/addons/weliam_merchant/pTLjC21GjCGj.log')) {
				if (0 < $halftype['give_price']) {
					Member::credit_update_credit2($order_out['mid'], $halftype['give_price'], '一卡通赠送金额');
				}
			}

			if (p('distribution') && empty($halftype['isdistri'])) {
				$_W['aid'] = $order_out['aid'];
				$disorderid = Distribution::disCore($order_out['mid'], $order_out['price'], $halftype['onedismoney'], $halftype['twodismoney'], $halftype['threedismoney'], $order_out['id'], 'halfcard', 1);
				$data['disorderid'] = $disorderid;
			}

			if (p('paidpromotion')) {
				$data['paidprid'] = Paidpromotion::getpaidpr(5, $order_out['id'], $data['paytype']);
			}

			$res = pdo_update(PDO_NAME . 'halfcard_record', $data, array('orderno' => $params['tid']));

			if ($res) {
				Store::halfsettlement($order_out['id']);
			}

			$halfcarddata = array('uniacid' => $order_out['uniacid'], 'aid' => $order_out['aid'], 'mid' => $order_out['mid'], 'expiretime' => $order_out['limittime'], 'username' => $order_out['username'], 'levelid' => $halftype['levelid'], 'createtime' => time(), 'mototype' => $order_out['mototype'], 'platenumber' => $order_out['platenumber'], 'from' => 0);

			if ($order_out['cardid']) {
				pdo_update(PDO_NAME . 'halfcardmember', $halfcarddata, array('id' => $order_out['cardid']));
			}
			else {
				pdo_insert(PDO_NAME . 'halfcardmember', $halfcarddata);
			}

			$member = pdo_get('wlmerchant_member', array('id' => $halfcarddata['mid']), array('openid', 'mobile'));
			$openid = $member['openid'];
			$mobile = empty($member['mobile']) ? $order_out['mobile'] : $member['mobile'];

			if (empty($member['mobile'])) {
				pdo_update('wlmerchant_member', array('mobile' => $order_out['mobile']), array('id' => $halfcarddata['mid']));
			}

			$url = app_url('halfcard/halfcard_app/userhalfcard');
			$time = date('Y-m-d H:i:s', $halfcarddata['expiretime']);
			$settings = Setting::wlsetting_read('halfcard');
			$halftext = $settings['text']['halfcardtext'] ? $settings['text']['halfcardtext'] : '一卡通';
			Message::openSuccessNotice($openid, $halftext, $time, $mobile, $url);
			$nickname = $halfcarddata['username'];
			Message::openNoticeAdmin($nickname, $halftext, $time, $mobile);
		}

		$base = Setting::wlsetting_read('distribution');
		if ($base['appdis'] == 2 && $base['switch'] && $base['together'] == 1) {
			$member = pdo_get('wlmerchant_member', array('id' => $order_out['mid']), array('mobile', 'nickname', 'realname', 'distributorid'));
			$distributor = pdo_get('wlmerchant_distributor', array('id' => $member['distributorid']));

			if ($distributor) {
				if (empty($distributor['disflag'])) {
					pdo_update('wlmerchant_distributor', array('disflag' => 1, 'createtime' => time()), array('mid' => $order_out['mid']));
				}
			}
			else {
				$data = array('uniacid' => $order_out['uniacid'], 'aid' => $order_out['aid'], 'mid' => $order_out['mid'], 'createtime' => time(), 'disflag' => 1, 'nickname' => $member['nickname'], 'mobile' => $member['mobile'], 'realname' => $member['realname'], 'leadid' => -1);
				pdo_insert('wlmerchant_distributor', $data);
				$disid = pdo_insertid();
				pdo_update('wlmerchant_member', array('distributorid' => $disid), array('id' => $order_out['mid']));
			}
		}
	}

	/**
     * 函数的含义说明
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function payHalfcardReturn($params)
	{
		global $_W;
		Util::wl_log('Vip_return', PATH_DATA . 'merchant/data/', $params);
		$order_out = pdo_get(PDO_NAME . 'halfcard_record', array('orderno' => $params['tid']), array('id'));
		wl_message('支付成功', app_url('order/userorder/payover', array('id' => $order_out['id'], 'type' => 5)), 'success');
	}

	static public function payChargeNotify($params)
	{
		global $_W;
		Util::wl_log('payResult_notify', PATH_DATA . 'merchant/data/', $params);
		$order_out = pdo_get(PDO_NAME . 'order', array('orderno' => $params['tid']));
		$_W['aid'] = $order_out['aid'];

		if ($order_out['status'] == 0) {
			$data = self::getVipPayData($params);

			if ($data['status'] == 1) {
				$data['status'] = 3;
			}

			$chargetype = pdo_get('wlmerchant_chargelist', array('id' => $order_out['fkid']));
			if (p('distribution') && empty($chargetype['isdistri'])) {
				$disorderid = Distribution::disCore($order_out['mid'], $order_out['price'], $chargetype['onedismoney'], $chargetype['twodismoney'], $chargetype['threedismoney'], $order_out['id'], 'charge', 1);
				$data['disorderid'] = $disorderid;
			}

			if (p('paidpromotion')) {
				$data['paidprid'] = Paidpromotion::getpaidpr(6, $order_out['id'], $data['paytype']);
			}

			$res = pdo_update(PDO_NAME . 'order', $data, array('orderno' => $params['tid']));

			if ($res) {
				Store::ordersettlement($order_out['id']);
			}

			$merchant = pdo_get(PDO_NAME . 'merchantdata', array('id' => $order_out['sid']), array('endtime', 'status', 'enabled'));
			$endtime = $merchant['endtime'];
			$merstatus = $merchant['status'];

			if (time() < $endtime) {
				$newendtime = $order_out['num'] * 24 * 3600 + $endtime;
			}
			else {
				$newendtime = $order_out['num'] * 24 * 3600 + time();
			}

			$audits = pdo_getcolumn(PDO_NAME . 'chargelist', array('id' => $order_out['fkid']), 'audits');
			$groupid = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('aid' => $order_out['aid'], 'chargeid' => $chargetype['id'], 'enabled' => 1), 'id');

			if (empty($groupid)) {
				$groupid = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('aid' => $order_out['aid'], 'isdefault' => 1, 'enabled' => 1), 'id');
			}

			if ($merstatus == 2) {
				$merdata['endtime'] = $newendtime;
				$merdata['groupid'] = $groupid;

				if ($merchant['enabled'] == 3) {
					$merdata['enabled'] = 1;
				}

				pdo_update(PDO_NAME . 'merchantdata', $merdata, array('id' => $order_out['sid']));
				Message::settledtoadmin($order_out['sid']);
			}
			else if ($audits) {
				pdo_update(PDO_NAME . 'merchantdata', array('status' => 2, 'endtime' => $newendtime, 'enabled' => 1, 'audits' => 1, 'groupid' => $groupid), array('id' => $order_out['sid']));
				pdo_update(PDO_NAME . 'merchantuser', array('status' => 2), array('storeid' => $order_out['sid']));
			}
			else {
				pdo_update(PDO_NAME . 'merchantdata', array('status' => 1, 'endtime' => $newendtime, 'groupid' => $groupid), array('id' => $order_out['sid']));
				Message::settledtoadmin($order_out['sid']);
			}
		}
	}

	static public function payChargeReturn($params)
	{
		$order_out = pdo_get(PDO_NAME . 'order', array('orderno' => $params['tid']), array('id'));
		wl_message('支付成功', app_url('order/userorder/payover', array('id' => $order_out['id'], 'type' => 6)), 'success');
	}

	static public function payPayonlineNotify($params)
	{
		global $_W;
		$order_out = pdo_get(PDO_NAME . 'order', array('orderno' => $params['tid']));
		Util::wl_log('payResult_notify', PATH_DATA . 'merchant/data/', $params);

		if ($order_out['status'] == 0) {
			$data = self::getVipPayData($params);

			if ($data['status'] == 1) {
				$data['status'] = 2;
			}

			if (p('distribution')) {
				$_W['aid'] = $order_out['aid'];
				$disorderid = Distribution::disCore($order_out['mid'], $order_out['price'], 0, 0, 0, $order_out['id'], 'payonline', 1);
				$data['disorderid'] = $disorderid;
			}

			if (p('paidpromotion')) {
				$_W['uniacid'] = $order_out['uniacid'];
				$_W['aid'] = $order_out['aid'];
				$data['paidprid'] = Paidpromotion::getpaidpr(7, $order_out['id'], $data['paytype']);
			}

			$res = pdo_update(PDO_NAME . 'order', $data, array('orderno' => $params['tid']));

			if ($res) {
				Store::ordersettlement($order_out['id']);
			}

			if ($order_out['fkid']) {
				$record = array('uniacid' => $order_out['uniacid'], 'aid' => $order_out['aid'], 'mid' => $order_out['mid'], 'type' => 1, 'cardid' => $order_out['card_id'], 'activeid' => $order_out['fkid'], 'merchantid' => $order_out['sid'], 'freeflag' => $order_out['card_type'], 'ordermoney' => $order_out['goodsprice'], 'realmoney' => $order_out['price'], 'verfmid' => $order_out['mid'], 'usetime' => time(), 'createtime' => time(), 'commentflag' => 1, 'discount' => $order_out['spec'], 'undismoney' => $order_out['oprice']);
				$flagtime = time() - 5;
				$flag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_timecardrecord') . ('WHERE cardid = ' . $order_out['card_id'] . ' AND activeid = ' . $order_out['fkid'] . ' AND type = 1 AND createtime > ' . $flagtime . ' '));

				if (empty($flag)) {
					pdo_insert(PDO_NAME . 'timecardrecord', $record);
				}
			}

			if (empty($disorderid)) {
				$disorderid = 0;
			}

			$openid = pdo_getcolumn(PDO_NAME . 'member', array('uniacid' => $order_out['uniacid'], 'id' => $order_out['mid']), 'openid');
			$nickname = pdo_getcolumn(PDO_NAME . 'member', array('uniacid' => $order_out['uniacid'], 'id' => $order_out['mid']), 'nickname');
			$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $order_out['uniacid'], 'id' => $order_out['sid']), 'storename');

			if ($order_out['fkid']) {
				$goodsname = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('uniacid' => $order_out['uniacid'], 'id' => $order_out['fkid']), 'title');
			}
			else {
				$goodsname = $storename . '在线买单';
			}

			Message::paySuccess2($openid, $order_out['price'], $goodsname);
			VoiceAnnouncements::PushVoiceMessage($params['fee'], $order_out['sid'], $data['paytype']);
			$admins = pdo_fetchall('SELECT mid FROM ' . tablename('wlmerchant_merchantuser') . ('WHERE uniacid = ' . $order_out['uniacid'] . ' AND storeid = ' . $order_out['sid'] . ' AND ismain IN (1,3) ORDER BY id DESC'));

			if ($admins) {
				foreach ($admins as $key => $ad) {
					$openid = pdo_getcolumn(PDO_NAME . 'member', array('uniacid' => $order_out['uniacid'], 'id' => $ad['mid']), 'openid');
					$first = '用户:[' . $nickname . ']在商户:[' . $storename . ']在线买单付费成功';
					$keyword1 = '在线买单';
					$keyword2 = '已付款' . $order_out['price'] . '元';
					$remark = '点击查看订单';
					$url = app_url('store/supervise/switchstore', array('storeid' => $order_out['sid'], 'url' => urlencode(app_url('store/supervise/order', array('status' => 2, 'type' => 'halfcard')))));
					Message::jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url);
				}
			}
		}
	}

	static public function payPayonlineReturn($params)
	{
		$order_out = pdo_get(PDO_NAME . 'order', array('orderno' => $params['tid']), array('id'));
		wl_message('支付成功', app_url('order/userorder/payover', array('id' => $order_out['id'], 'type' => 7)), 'success');
	}

	/**
     * 函数的含义说明
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function getVipPayData($params)
	{
		global $_W;
		$data = array('status' => $params['result'] == 'success' ? 1 : 0);

		if ($params['is_usecard'] == 1) {
			$fee = $params['card_fee'];
			$data['is_usecard'] = 1;
		}
		else {
			$fee = $params['fee'];
		}

		$paytype = array('credit' => 1, 'wechat' => 2, 'alipay' => 3, 'delivery' => 4, 'wxapp' => 5);
		$data['paytype'] = $paytype[$params['type']];

		if ($params['tag']['transaction_id']) {
			$data['transid'] = $params['tag']['transaction_id'];
		}

		$data['paytime'] = TIMESTAMP;
		return $data;
	}

	/**
     * 获取系统运营概况（包括代理，会员等）
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function sysSurvey($refresh = 0)
	{
		global $_W;
		$agentUsers = Util::getNumData('id', PDO_NAME . 'agentusers', array());
		$members = Util::getNumData('*', PDO_NAME . 'member', array('vipstatus' => 1));
		$time = date('Y-m-d H:i:s', time());
		$merchantNumData = Util::getNumData('*', PDO_NAME . 'member', array(), 'id desc', 0, 0, 1);
		$today = strtotime(date('Ymd'));
		$firstday = strtotime(date('Y-m-01'));
		$yestoday = $today - 86400;
		$d = date('Ymd');
		$uv = pdo_fetchall('select distinct mid from' . tablename(PDO_NAME . 'puvrecord') . ('where uniacid = ' . $_W['uniacid'] . ' and date=\'' . $d . '\''));
		$todaypuv = pdo_get(PDO_NAME . 'puv', array('uniacid' => $_W['uniacid'], 'date' => date('Ymd')), array('pv', 'uv'));
		$allpuv = pdo_getall(PDO_NAME . 'puv', array('uniacid' => $_W['uniacid']), array('pv', 'uv'));
		$numPv = 0;
		$numUv = 0;

		foreach ($allpuv as $k => $v) {
			$numPv += $v['pv'];
			$numUv += $v['uv'];
		}

		$newfans = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'member') . (' WHERE uniacid = \'' . $_W['uniacid'] . '\' and createtime >= ' . $firstday));
		$totalInMoney = $totalOutMoney = $rushMoney = $vipMoney = $halfMoney = $orderMoney = $refundMoney = $settlementMoney = $waitSettlementMoney = $spercentMoney = $halfPercentMoney = $vipPercentMoney = 0;
		$rushOrders = Util::getNumData('actualprice,status,issettlement', PDO_NAME . 'rush_order', array('#status#' => '(1,2,3,4,6,7)'));

		foreach ($rushOrders[0] as $item) {
			$rushMoney += $item['actualprice'];

			if ($item['status'] == 7) {
				$refundMoney += $item['actualprice'];
			}

			if ($item['issettlement'] == 0) {
				$waitSettlementMoney += $item['actualprice'];
			}

			if ($item['issettlement'] == 1) {
				$settlementMoney += $item['actualprice'];
			}
		}

		$halfOrders = Util::getNumData('price,issettlement', PDO_NAME . 'halfcard_record', array('status' => 1));

		foreach ($halfOrders[0] as $item) {
			$halfMoney += $item['price'];

			if ($item['issettlement'] == 0) {
				$waitSettlementMoney += $item['price'];
			}

			if ($item['issettlement'] == 1) {
				$settlementMoney += $item['price'];
			}
		}

		$orderOrders = Util::getNumData('price,status,issettlement', PDO_NAME . 'order', array('#status#' => '(1,2,3,4,6,7,8)'));

		foreach ($orderOrders[0] as $item) {
			$orderMoney += $item['price'];

			if ($item['status'] == 7) {
				$refundMoney += $item['price'];
			}

			if ($item['issettlement'] == 0) {
				$waitSettlementMoney += $item['price'];
			}

			if ($item['issettlement'] == 1) {
				$settlementMoney += $item['price'];
			}
		}

		$settlementOrders = Util::getNumData('*', PDO_NAME . 'settlement_record', array('#status#' => '(1,2,3,4,5)'));

		foreach ($settlementOrders[0] as $item) {
			if ($item['type'] == 1) {
				$spercentMoney += $item['apercentmoney'];
			}

			if ($item['type'] == 2) {
				$halfPercentMoney += $item['apercentmoney'];
			}

			if ($item['type'] == 3) {
				$vipPercentMoney += $item['apercentmoney'];
			}
		}

		$totalInMoney = sprintf('%.2f', $rushMoney + $vipMoney + $halfMoney + $orderMoney);
		$totalOutMoney = sprintf('%.2f', $refundMoney + $settlementMoney);
		$spercentMoney = sprintf('%.2f', $spercentMoney);
		$halfPercentMoney = sprintf('%.2f', $halfPercentMoney);
		$vipPercentMoney = sprintf('%.2f', $vipPercentMoney);
		$settlementMoney = sprintf('%.2f', $settlementMoney);
		$waitSettlementMoney = sprintf('%.2f', $waitSettlementMoney);
		$data = array('agentNum' => count($agentUsers[0]), 'vipNum' => count($members[0]), 'todayUv' => $todaypuv['uv'], 'todayPv' => $todaypuv['pv'], 'totalPv' => $numPv, 'totalUv' => $numUv, 'ThisMonthNewFans' => $newfans, 'totalInMoney' => $totalInMoney, 'totalOutMoney' => $totalOutMoney, 'spercentMoney' => $spercentMoney, 'halfPercentMoney' => $halfPercentMoney, 'vipPercentMoney' => $vipPercentMoney, 'settlementMoney' => $settlementMoney, 'waitSettlementMoney' => $waitSettlementMoney);
		Cache::setCache('sysSurvey', 'allData', $data);
		return $data;
	}

	static public function sysCashSurvey($refresh = 0, $timetype = 0, $starttime, $endtime)
	{
		global $_W;
		$where = array();
		$agentsData = Util::getNumData('id,agentname', PDO_NAME . 'agentusers', $where);
		$agents = $agentsData[0];
		$children = array();

		if (!empty($agents)) {
			$allMoney = $allorder = $fishorder = $ingorder = $fishordermoney = $ingordermoney = $fishSettlement = $ingSettlement = $refund = $sysincome = 0;
			$rushallmoney = $rushallorder = $rushfishorder = $rushingorder = $rushfishordermoney = $rushingordermoney = 0;
			$grouponallmoney = $grouponallorder = $grouponfishorder = $grouponingorder = $grouponfishordermoney = $grouponingordermoney = 0;
			$fightallmoney = $fightallorder = $fightfishorder = $fightingorder = $fightfishordermoney = $fightingordermoney = 0;
			$couponallmoney = $couponallorder = $couponfishorder = $couponingorder = $couponfishordermoney = 0;
			$halfcardallmoney = $halfcardallorder = $halfcardfishordermoney = 0;
			$pocketallmoney = $pocketallorder = $pocketfishordermoney = 0;
			$sysincome = pdo_getcolumn('wlmerchant_settlement_record', array('uniacid' => $_W['uniacid'], 'status >=' => 4), array('SUM(spercentmoney)'));

			foreach ($agents as $index => &$row) {
				$aMoney = 0;
				$where2['aid'] = $row['id'];
				$data = Util::getNumData('id,storename,logo', PDO_NAME . 'merchantdata', $where2);
				$set = Area::getSingleAgent($row['id']);
				$percent = $set['percent'];

				foreach ($data[0] as $k => &$v) {
					$sMoney = 0;
					$where3['#status#'] = '(1,2,3,4,6,7,8)';
					$where3['sid'] = $v['id'];
					$where3['num>'] = 0;

					if ($timetype == 1) {
						$where3['createtime>'] = strtotime(date('Ymd'));
					}
					else if ($timetype == 2) {
						$where3['createtime>'] = strtotime('-7 days');
					}
					else if ($timetype == 3) {
						$where3['createtime>'] = strtotime('-30 days');
					}
					else {
						if ($timetype == 5) {
							$where3['createtime>'] = $starttime;
							$where3['createtime<'] = $endtime;
						}
					}

					$rush_orders = Util::getNumData('actualprice,status,issettlement', PDO_NAME . 'rush_order', $where3);

					if ($rush_orders[0]) {
						foreach ($rush_orders[0] as $order) {
							$sMoney += $order['actualprice'];
							$rushallmoney += $order['actualprice'];
							$allorder += 1;
							$rushallorder += 1;
							if ($order['status'] == 2 || $order['status'] == 3) {
								$fishorder += 1;
								$rushfishorder += 1;
								$fishordermoney += $order['actualprice'];
								$rushfishordermoney += $order['actualprice'];

								if ($order['issettlement'] == 1) {
									$fishSettlement += $order['actualprice'];
								}
								else {
									$ingSettlement += $order['actualprice'];
								}
							}
							else {
								$ingorder += 1;
								$rushingorder += 1;
								$ingordermoney += $order['actualprice'];
								$rushingordermoney += $order['actualprice'];

								if ($order['status'] == 7) {
									$refund += $order['actualprice'];
								}
							}
						}
					}

					$orders = Util::getNumData('price,status,num,issettlement,plugin', PDO_NAME . 'order', $where3);

					if ($orders[0]) {
						foreach ($orders[0] as $order) {
							$sMoney += $order['price'];
							$allorder += 1;

							if ($order['plugin'] == 'wlfightgroup') {
								$fightallmoney += $order['price'];
								$fightallorder += 1;
							}
							else if ($order['plugin'] == 'coupon') {
								$couponallorder += 1;
							}
							else {
								if ($order['plugin'] == 'groupon') {
									$grouponallmoney += $order['price'];
									$grouponallorder += 1;
								}
							}

							if ($order['status'] == 2 || $order['status'] == 3) {
								$fishordermoney += $order['price'];
								$fishorder += 1;

								if ($order['plugin'] == 'wlfightgroup') {
									$fightfishordermoney += $order['price'];
									$fightfishorder += 1;
								}
								else if ($order['plugin'] == 'coupon') {
									$couponallmoney += $order['price'];
									$couponfishorder += 1;
								}
								else {
									if ($order['plugin'] == 'groupon') {
										$grouponfishordermoney += $order['price'];
										$grouponfishorder += 1;
									}
								}

								if ($order['issettlement'] == 1) {
									$fishSettlement += $order['price'];

									if ($order['plugin'] == 'coupon') {
										$couponfishordermoney += $order['price'];
									}
								}
								else {
									$ingSettlement += $order['price'];
								}
							}
							else {
								$ingorder += 1;
								$ingordermoney += $order['price'];

								if ($order['status'] == 7) {
									$refund += $order['price'];
								}

								if ($order['plugin'] == 'wlfightgroup') {
									$fightingordermoney += $order['price'];
									$fightingorder += 1;
								}
								else if ($order['plugin'] == 'coupon') {
									$couponingorder += 1;
								}
								else {
									if ($order['plugin'] == 'groupon') {
										$grouponingordermoney += $order['price'];
										$grouponingorder += 1;
									}
								}
							}
						}
					}

					$v['sMoney'] = $sMoney;
					$aMoney += $sMoney;
				}

				foreach ($data[0] as &$money) {
					$money['forpercent'] = @sprintf('%.2f', $money['sMoney'] / $aMoney * 100);
				}

				$where4['aid'] = $row['id'];
				$where4['status'] = 1;

				if ($timetype == 1) {
					$where4['createtime>'] = strtotime(date('Ymd'));
				}
				else if ($timetype == 2) {
					$where4['createtime>'] = strtotime('-7 days');
				}
				else if ($timetype == 3) {
					$where4['createtime>'] = strtotime('-30 days');
				}
				else {
					if ($timetype == 5) {
						$where4['createtime>'] = $starttime;
						$where4['createtime<'] = $endtime;
					}
				}

				$halforder = Util::getNumData('price', PDO_NAME . 'halfcard_record', $where4);

				if ($halforder[0]) {
					foreach ($halforder[0] as $order) {
						$aMoney += $order['price'];
						$allorder += 1;
						$fishorder += 1;
						$fishordermoney += $order['price'];
						$halfcardallmoney += $order['price'];
						$halfcardallorder += 1;

						if ($order['issettlement'] == 1) {
							$fishSettlement += $order['price'];
							$halfcardfishordermoney += $order['price'];
						}
						else {
							$ingSettlement += $order['price'];
						}
					}
				}

				$where5['aid'] = $row['id'];
				$where5['status'] = 3;
				$where5['sid'] = 0;

				if ($timetype == 1) {
					$where5['createtime>'] = strtotime(date('Ymd'));
				}
				else if ($timetype == 2) {
					$where5['createtime>'] = strtotime('-7 days');
				}
				else if ($timetype == 3) {
					$where5['createtime>'] = strtotime('-30 days');
				}
				else {
					if ($timetype == 5) {
						$where5['createtime>'] = $starttime;
						$where5['createtime<'] = $endtime;
					}
				}

				$pocketorder = Util::getNumData('price', PDO_NAME . 'order', $where5);

				if ($pocketorder[0]) {
					foreach ($pocketorder[0] as $order) {
						$aMoney += $order['price'];
						$allorder += 1;
						$fishorder += 1;
						$fishordermoney += $order['price'];
						$pocketallmoney += $order['price'];
						$pocketallorder += 1;

						if ($order['issettlement'] == 1) {
							$fishSettlement += $order['price'];
							$pocketfishordermoney += $order['price'];
						}
						else {
							$ingSettlement += $order['price'];
						}
					}
				}

				$pocketnum = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_pocket_informations') . 'WHERE mid > 0 ORDER BY id DESC');
				$pocketnum = count($pocketnum);
				$children[$row['id']] = $data[0];
				$row['aMoney'] = $aMoney;
				$allMoney += $aMoney;
			}
		}

		$max = 0;

		foreach ($agents as $index => &$percent) {
			$percent['forpercent'] = @sprintf('%.2f', $percent['aMoney'] / $allMoney * 100);
			$allMoney = sprintf('%.2f', $allMoney);
			$max = $max < $percent['aMoney'] ? $max = $percent['aMoney'] : $max;
			$max = sprintf('%.2f', $max);
		}

		$time = date('Y-m-d H:i:s', time());
		$newdata['all']['allmoney'] = $allMoney;
		$newdata['all']['allorder'] = $allorder;
		$newdata['all']['fishorder'] = $fishorder;
		$newdata['all']['fishordermoney'] = $fishordermoney;
		$newdata['all']['ingorder'] = $ingorder;
		$newdata['all']['ingordermoney'] = $ingordermoney;
		$newdata['all']['fishSettlement'] = @sprintf('%.2f', $fishSettlement);
		$newdata['all']['ingSettlement'] = $ingSettlement;
		$newdata['all']['refund'] = $refund;
		$newdata['all']['sysincome'] = @sprintf('%.2f', $sysincome);
		$newdata['rush']['rushallmoney'] = $rushallmoney;
		$newdata['rush']['rushallorder'] = $rushallorder;
		$newdata['rush']['rushfishorder'] = $rushfishorder;
		$newdata['rush']['rushfishordermoney'] = $rushfishordermoney;
		$newdata['rush']['rushingorder'] = $rushingorder;
		$newdata['rush']['rushingordermoney'] = $rushingordermoney;
		$newdata['groupon']['grouponallmoney'] = $grouponallmoney;
		$newdata['groupon']['grouponallorder'] = $grouponallorder;
		$newdata['groupon']['grouponfishorder'] = $grouponfishorder;
		$newdata['groupon']['grouponfishordermoney'] = $grouponfishordermoney;
		$newdata['groupon']['grouponingorder'] = $grouponingorder;
		$newdata['groupon']['grouponingordermoney'] = $grouponingordermoney;
		$newdata['fight']['fightallmoney'] = $fightallmoney;
		$newdata['fight']['fightallorder'] = $fightallorder;
		$newdata['fight']['fightfishorder'] = $fightfishorder;
		$newdata['fight']['fightfishordermoney'] = $fightfishordermoney;
		$newdata['fight']['fightingorder'] = $fightingorder;
		$newdata['fight']['fightingordermoney'] = $fightingordermoney;
		$fightwhere['status'] = 2;

		if ($timetype == 1) {
			$fightwhere['successtime>'] = strtotime(date('Ymd'));
		}
		else if ($timetype == 2) {
			$fightwhere['successtime>'] = strtotime('-7 days');
		}
		else if ($timetype == 3) {
			$fightwhere['successtime>'] = strtotime('-30 days');
		}
		else {
			if ($timetype == 5) {
				$fightwhere['successtime>'] = $starttime;
				$fightwhere['successtime<'] = $endtime;
			}
		}

		$fightsuccess = Util::getNumData('id', PDO_NAME . 'fightgroup_group', $fightwhere);
		$newdata['fight']['successnum'] = count($fightsuccess[0]);
		$newdata['coupon']['couponallmoney'] = $couponallmoney;
		$newdata['coupon']['couponallorder'] = $couponallorder;
		$newdata['coupon']['couponfishorder'] = $couponfishorder;
		$newdata['coupon']['couponingorder'] = $couponingorder;
		$newdata['coupon']['couponfishordermoney'] = $couponfishordermoney;
		$couponwhere['usetimes'] = 0;

		if ($timetype == 1) {
			$couponwhere['createtime>'] = strtotime(date('Ymd'));
		}
		else if ($timetype == 2) {
			$couponwhere['createtime>'] = strtotime('-7 days');
		}
		else if ($timetype == 3) {
			$couponwhere['createtime>'] = strtotime('-30 days');
		}
		else {
			if ($timetype == 5) {
				$couponwhere['createtime>'] = $starttime;
				$couponwhere['createtime<'] = $endtime;
			}
		}

		$coupons = Util::getNumData('id', PDO_NAME . 'member_coupons', $couponwhere);
		$newdata['coupon']['couponnum'] = count($coupons[0]);
		$newdata['halfcard']['halfcardallmoney'] = $halfcardallmoney;
		$newdata['halfcard']['halfcardallorder'] = $halfcardallorder;
		$newdata['halfcard']['halfcardfishordermoney'] = $halfcardfishordermoney;
		$halfcardnum = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . 'WHERE expiretime > ' . time() . ' ORDER BY id DESC');
		$halfcardnum = count($halfcardnum);
		$newdata['halfcard']['halfcardnum'] = $halfcardnum;
		$newdata['vip']['vipallmoney'] = $vipallmoney;
		$newdata['vip']['vipallorder'] = $vipallorder;
		$newdata['vip']['vipfishordermoney'] = $vipfishordermoney;
		$vipnum = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_member') . 'WHERE lastviptime > ' . time() . ' ORDER BY id DESC');
		$vipnum = count($vipnum);
		$newdata['vip']['vipnum'] = $vipnum;
		$newdata['pocket']['pocketallmoney'] = $pocketallmoney;
		$newdata['pocket']['pocketallorder'] = $pocketallorder;
		$newdata['pocket']['pocketfishordermoney'] = $pocketfishordermoney;
		$newdata['pocket']['pocketnum'] = $pocketnum;
		$data = array($agents, $children, $max, $allMoney, $time, $newdata);
		Cache::setCache('sysCashSurvey', 'allData', $data);
		return $data;
	}

	static public function agentCashSurvey($refresh = 0, $timetype = 0, $starttime, $endtime, $merchantid = 0)
	{
		global $_W;
		$where = array();
		$where['id'] = $_W['agent']['id'];
		$agentsData = Util::getNumData('id,agentname', PDO_NAME . 'agentusers', $where);
		$agents = $agentsData[0];
		$children = array();

		if (!empty($agents)) {
			$allMoney = $allorder = $fishorder = $ingorder = $fishordermoney = $ingordermoney = $fishSettlement = $ingSettlement = $refund = $sysincome = 0;
			$rushallmoney = $rushallorder = $rushfishorder = $rushingorder = $rushfishordermoney = $rushingordermoney = 0;
			$grouponallmoney = $grouponallorder = $grouponfishorder = $grouponingorder = $grouponfishordermoney = $grouponingordermoney = 0;
			$fightallmoney = $fightallorder = $fightfishorder = $fightingorder = $fightfishordermoney = $fightingordermoney = 0;
			$couponallmoney = $couponallorder = $couponfishorder = $couponingorder = $couponfishordermoney = 0;
			$halfcardallmoney = $halfcardallorder = $halfcardfishordermoney = 0;
			$pocketallmoney = $pocketallorder = $pocketfishordermoney = 0;
			$agentAmount = Util::getSingelData('allmoney,nowmoney', PDO_NAME . 'agentusers', array('uniacid' => $_W['uniacid'], 'id' => $_W['aid']));
			$sysincome = $agentAmount['allmoney'];
			$fishSettlement = sprintf('%.2f', $agentAmount['allmoney'] - $agentAmount['nowmoney']);
			$ingSettlement = sprintf('%.2f', $agentAmount['nowmoney']);
			$aMoney = 0;
			$where2['aid'] = $agents[0]['id'];

			if ($merchantid) {
				$where2['id'] = $merchantid;
			}

			$data = Util::getNumData('id,storename,logo', PDO_NAME . 'merchantdata', $where2);
			$max = 0;

			foreach ($data[0] as $k => &$v) {
				$sMoney = 0;
				$where3['#status#'] = '(1,2,3,4,6,7,8)';
				$where3['sid'] = $v['id'];

				if ($timetype == 1) {
					$where3['createtime>'] = strtotime(date('Ymd'));
				}
				else if ($timetype == 2) {
					$where3['createtime>'] = strtotime('-7 days');
				}
				else if ($timetype == 3) {
					$where3['createtime>'] = strtotime('-30 days');
				}
				else {
					if ($timetype == 5) {
						$where3['createtime>'] = $starttime;
						$where3['createtime<'] = $endtime;
					}
				}

				$rush_orders = Util::getNumData('actualprice,status,issettlement,sid', PDO_NAME . 'rush_order', $where3);

				foreach ($rush_orders[0] as $order) {
					$sMoney += $order['actualprice'];
					$allMoney += $order['actualprice'];
					$rushallmoney += $order['actualprice'];
					$allorder += 1;
					$rushallorder += 1;
					if ($order['status'] == 2 || $order['status'] == 3) {
						$fishorder += 1;
						$rushfishorder += 1;
						$fishordermoney += $order['actualprice'];
						$rushfishordermoney += $order['actualprice'];

						if ($order['issettlement'] == 1) {
							$store = Store::getSingleStore($order['sid']);
						}
					}
					else {
						$ingorder += 1;
						$rushingorder += 1;
						$ingordermoney += $order['actualprice'];
						$rushingordermoney += $order['actualprice'];

						if ($order['status'] == 7) {
							$refund += $order['actualprice'];
						}
					}
				}

				$orders = Util::getNumData('price,status,num,issettlement,plugin,sid', PDO_NAME . 'order', $where3);

				foreach ($orders[0] as $order) {
					$sMoney += $order['price'];
					$allMoney += $order['price'];
					$allorder += 1;

					if ($order['plugin'] == 'wlfightgroup') {
						$fightallmoney += $order['price'];
						$fightallorder += 1;
					}
					else if ($order['plugin'] == 'coupon') {
						$couponallorder += 1;
					}
					else {
						if ($order['plugin'] == 'groupon') {
							$grouponallmoney += $order['price'];
							$grouponallorder += 1;
						}
					}

					if ($order['status'] == 2 || $order['status'] == 3) {
						$fishordermoney += $order['price'];
						$fishorder += 1;

						if ($order['plugin'] == 'wlfightgroup') {
							$fightfishordermoney += $order['price'];
							$fightfishorder += 1;
						}
						else if ($order['plugin'] == 'coupon') {
							$couponallmoney += $order['price'];
							$couponfishorder += 1;
						}
						else {
							if ($order['plugin'] == 'groupon') {
								$grouponfishordermoney += $order['price'];
								$grouponfishorder += 1;
							}
						}

						if ($order['issettlement'] == 1) {
							$store = Store::getSingleStore($order['sid']);

							if ($order['plugin'] == 'coupon') {
								$couponfishordermoney += $order['price'];
							}
						}
					}
					else {
						$ingorder += 1;
						$ingordermoney += $order['price'];

						if ($order['status'] == 7) {
							$refund += $order['price'];
						}

						if ($order['plugin'] == 'wlfightgroup') {
							$fightingordermoney += $order['price'];
							$fightingorder += 1;
						}
						else if ($order['plugin'] == 'coupon') {
							$couponingorder += 1;
						}
						else {
							if ($order['plugin'] == 'groupon') {
								$grouponingordermoney += $order['price'];
								$grouponingorder += 1;
							}
						}
					}
				}

				$v['sMoney'] = $sMoney;
				$aMoney += $sMoney;
			}

			foreach ($data[0] as &$money) {
				$money['forpercent'] = @sprintf('%.2f', $money['sMoney'] / $aMoney * 100);
				$max = $max < $money['sMoney'] ? $max = $money['sMoney'] : $max;
				$max = sprintf('%.2f', $max);
			}

			if (empty($merchantid)) {
				$where4['aid'] = $agents[0]['id'];
				$where4['status'] = 1;

				if ($timetype == 1) {
					$where4['createtime>'] = strtotime(date('Ymd'));
				}
				else if ($timetype == 2) {
					$where4['createtime>'] = strtotime('-7 days');
				}
				else if ($timetype == 3) {
					$where4['createtime>'] = strtotime('-30 days');
				}
				else {
					if ($timetype == 5) {
						$where4['createtime>'] = $starttime;
						$where4['createtime<'] = $endtime;
					}
				}

				$halforder = Util::getNumData('price', PDO_NAME . 'halfcard_record', $where4);

				if ($halforder[0]) {
					foreach ($halforder[0] as $order) {
						$aMoney += $order['price'];
						$allMoney += $order['price'];
						$allorder += 1;
						$fishorder += 1;
						$fishordermoney += $order['price'];
						$halfcardallmoney += $order['price'];
						$halfcardallorder += 1;

						if ($order['issettlement'] == 1) {
							$halfcardfishordermoney += $order['price'];
						}
					}
				}

				$where5['aid'] = $agents[0]['id'];
				$where5['status'] = 3;
				$where5['sid'] = 0;

				if ($timetype == 1) {
					$where5['createtime>'] = strtotime(date('Ymd'));
				}
				else if ($timetype == 2) {
					$where5['createtime>'] = strtotime('-7 days');
				}
				else if ($timetype == 3) {
					$where5['createtime>'] = strtotime('-30 days');
				}
				else {
					if ($timetype == 5) {
						$where5['createtime>'] = $starttime;
						$where5['createtime<'] = $endtime;
					}
				}

				$pocketorder = Util::getNumData('price', PDO_NAME . 'order', $where5);

				if ($pocketorder[0]) {
					foreach ($pocketorder[0] as $order) {
						$aMoney += $order['price'];
						$allMoney += $order['price'];
						$allorder += 1;
						$fishorder += 1;
						$fishordermoney += $order['price'];
						$pocketallmoney += $order['price'];
						$pocketallorder += 1;

						if ($order['issettlement'] == 1) {
							$pocketfishordermoney += $order['price'];
						}
					}
				}

				$pocketnum = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_pocket_informations') . 'WHERE mid > 0 ORDER BY id DESC');
				$pocketnum = count($pocketnum);
			}

			$children[$agents[0]['id']] = $data[0];
			$row['aMoney'] = $aMoney;
		}

		foreach ($agents as $index => &$percent) {
			$percent['forpercent'] = @sprintf('%.2f', $percent['aMoney'] / $allMoney * 100);
			$allMoney = @sprintf('%.2f', $allMoney);
		}

		$time = date('Y-m-d H:i:s', time());
		$newdata['all']['allmoney'] = $allMoney;
		$newdata['all']['allorder'] = $allorder;
		$newdata['all']['fishorder'] = $fishorder;
		$newdata['all']['fishordermoney'] = $fishordermoney;
		$newdata['all']['ingorder'] = $ingorder;
		$newdata['all']['ingordermoney'] = $ingordermoney;
		$newdata['all']['fishSettlement'] = $fishSettlement;
		$newdata['all']['ingSettlement'] = $ingSettlement;
		$newdata['all']['refund'] = $refund;
		$newdata['all']['sysincome'] = @sprintf('%.2f', $sysincome);
		$newdata['rush']['rushallmoney'] = $rushallmoney;
		$newdata['rush']['rushallorder'] = $rushallorder;
		$newdata['rush']['rushfishorder'] = $rushfishorder;
		$newdata['rush']['rushfishordermoney'] = $rushfishordermoney;
		$newdata['rush']['rushingorder'] = $rushingorder;
		$newdata['rush']['rushingordermoney'] = $rushingordermoney;
		$newdata['groupon']['grouponallmoney'] = $grouponallmoney;
		$newdata['groupon']['grouponallorder'] = $grouponallorder;
		$newdata['groupon']['grouponfishorder'] = $grouponfishorder;
		$newdata['groupon']['grouponfishordermoney'] = $grouponfishordermoney;
		$newdata['groupon']['grouponingorder'] = $grouponingorder;
		$newdata['groupon']['grouponingordermoney'] = $grouponingordermoney;
		$newdata['fight']['fightallmoney'] = $fightallmoney;
		$newdata['fight']['fightallorder'] = $fightallorder;
		$newdata['fight']['fightfishorder'] = $fightfishorder;
		$newdata['fight']['fightfishordermoney'] = $fightfishordermoney;
		$newdata['fight']['fightingorder'] = $fightingorder;
		$newdata['fight']['fightingordermoney'] = $fightingordermoney;
		$fightwhere['status'] = 2;

		if ($timetype == 1) {
			$fightwhere['successtime>'] = strtotime(date('Ymd'));
		}
		else if ($timetype == 2) {
			$fightwhere['successtime>'] = strtotime('-7 days');
		}
		else if ($timetype == 3) {
			$fightwhere['successtime>'] = strtotime('-30 days');
		}
		else {
			if ($timetype == 5) {
				$fightwhere['successtime>'] = $starttime;
				$fightwhere['successtime<'] = $endtime;
			}
		}

		if ($merchantid) {
			$fightwhere['sid'] = $merchantid;
		}

		$fightsuccess = Util::getNumData('id', PDO_NAME . 'fightgroup_group', $fightwhere);
		$newdata['fight']['successnum'] = count($fightsuccess[0]);
		$newdata['coupon']['couponallmoney'] = $couponallmoney;
		$newdata['coupon']['couponallorder'] = $couponallorder;
		$newdata['coupon']['couponfishorder'] = $couponfishorder;
		$newdata['coupon']['couponingorder'] = $couponingorder;
		$newdata['coupon']['couponfishordermoney'] = $couponfishordermoney;
		$couponwhere['usetimes'] = 0;

		if ($timetype == 1) {
			$couponwhere['createtime>'] = strtotime(date('Ymd'));
		}
		else if ($timetype == 2) {
			$couponwhere['createtime>'] = strtotime('-7 days');
		}
		else if ($timetype == 3) {
			$couponwhere['createtime>'] = strtotime('-30 days');
		}
		else {
			if ($timetype == 5) {
				$couponwhere['createtime>'] = $starttime;
				$couponwhere['createtime<'] = $endtime;
			}
		}

		if ($merchantid) {
			$coupons = pdo_getall('wlmerchant_couponlist', array('merchantid' => $merchantid), array('id'));

			if ($coupons) {
				$goodids = '(';
				$i = 0;

				while ($i < count($coupons)) {
					if ($i == 0) {
						$goodids .= $coupons[$i]['id'];
					}
					else {
						$goodids .= ',' . $coupons[$i]['id'];
					}

					++$i;
				}

				$goodids .= ')';
				$couponwhere['parentid#'] = $goodids;
			}
			else {
				$couponwhere['parentid#'] = '(0)';
			}
		}

		$coupons = Util::getNumData('id', PDO_NAME . 'member_coupons', $couponwhere);
		$newdata['coupon']['couponnum'] = count($coupons[0]);
		$newdata['halfcard']['halfcardallmoney'] = $halfcardallmoney;
		$newdata['halfcard']['halfcardallorder'] = $halfcardallorder;
		$newdata['halfcard']['halfcardfishordermoney'] = $halfcardfishordermoney;
		$halfcardnum = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . 'WHERE expiretime > ' . time() . ' ORDER BY id DESC');
		$halfcardnum = count($halfcardnum);
		$newdata['halfcard']['halfcardnum'] = $halfcardnum;
		$newdata['vip']['vipallmoney'] = $vipallmoney;
		$newdata['vip']['vipallorder'] = $vipallorder;
		$newdata['vip']['vipfishordermoney'] = $vipfishordermoney;
		$vipnum = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_member') . 'WHERE lastviptime > ' . time() . ' ORDER BY id DESC');
		$vipnum = count($vipnum);
		$newdata['vip']['vipnum'] = $vipnum;
		$newdata['pocket']['pocketallmoney'] = $pocketallmoney;
		$newdata['pocket']['pocketallorder'] = $pocketallorder;
		$newdata['pocket']['pocketfishordermoney'] = $pocketfishordermoney;
		$newdata['pocket']['pocketnum'] = $pocketnum;
		$data = array($agents, $children, $max, $allMoney, $time, $newdata);
		Cache::setCache('agentCashSurvey', 'allData', $data);
		return $data;
	}

	/**
     * 获取系统运营概况（包括代理，会员等）
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function sysMemberSurvey()
	{
		global $_W;
		global $_GPC;
		$stat = array();
		$today_starttime = strtotime(date('Y-m-d'));
		$yesterday_starttime = $today_starttime - 86400;
		$month_starttime = strtotime(date('Y-m'));
		$where = $_W['aid'] ? ' AND aid = ' . $_W['aid'] : '';
		$stat['yesterday_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('wlmerchant_member') . ' where uniacid = :uniacid and createtime >= :starttime and createtime <= :endtime' . $where, array(':uniacid' => $_W['uniacid'], ':starttime' => $yesterday_starttime, ':endtime' => $today_starttime)));
		$stat['today_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('wlmerchant_member') . ' where uniacid = :uniacid and createtime >= :starttime' . $where, array(':uniacid' => $_W['uniacid'], ':starttime' => $today_starttime)));
		$stat['month_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('wlmerchant_member') . ' where uniacid = :uniacid and createtime >= :starttime' . $where, array(':uniacid' => $_W['uniacid'], ':starttime' => $month_starttime)));
		$stat['total_num'] = intval(pdo_fetchcolumn('select count(*) from ' . tablename('wlmerchant_member') . ' where uniacid = :uniacid' . $where, array(':uniacid' => $_W['uniacid'])));
		return $stat;
	}

	/**
     * 获取系统运营概况（包括代理，会员等）
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function agentSurvey($refresh = 0)
	{
		global $_W;
		$members = Util::getNumData('*', PDO_NAME . 'member', array('vipstatus' => 1, 'aid' => $_W['agent']['id']));
		$time = date('Y-m-d H:i:s', time());
		$merchants = Util::getNumData('id', PDO_NAME . 'merchantdata', array('aid' => $_W['agent']['id']), 'id desc', 0, 0, 1);
		$areaids = Util::idSwitch('aid', 'areaid', $_W['agent']['id']);
		$s = '(0';

		foreach ($areaids as $k => $v) {
			$s .= ',' . '\'' . $v['areaid'] . '\'';
		}

		$s .= ')';
		$today = strtotime(date('Ymd'));
		$firstday = strtotime(date('Y-m-01'));
		$yestoday = $today - 86400;
		$where = array();
		$where['date'] = date('Ymd');
		$where['#areaid#'] = $s;
		$todaypuv = Util::getSingelData('pv,uv', PDO_NAME . 'puv', $where);

		if (empty($todaypuv)) {
			$todaypuv['pv'] = $todaypuv['uv'] = 0;
		}

		unset($where['date']);
		$allpuv = Util::getNumData('pv,uv', PDO_NAME . 'puv', $where);
		$numPv = 0;
		$numUv = 0;

		foreach ($allpuv[0] as $k => $v) {
			$numPv += $v['pv'];
			$numUv += $v['uv'];
		}

		$newfans = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'member') . (' WHERE uniacid = \'' . $_W['uniacid'] . '\' and createtime >= ' . $firstday));
		$totalInMoney = $totalOutMoney = $rushMoney = $vipMoney = $halfMoney = $orderMoney = $refundMoney = $settlementMoney = $waitSettlementMoney = $spercentMoney = $halfPercentMoney = $vipPercentMoney = 0;
		$rushOrders = Util::getNumData('actualprice,status,issettlement', PDO_NAME . 'rush_order', array('#status#' => '(1,2,3,4,6,7)', 'aid' => $_W['agent']['id']));

		foreach ($rushOrders[0] as $item) {
			$rushMoney += $item['actualprice'];

			if ($item['issettlement'] == 1) {
				$waitSettlementMoney += $item['actualprice'];
			}

			if ($item['issettlement'] == 2) {
				$settlementMoney += $item['actualprice'];
			}

			if ($item['status'] == 7) {
				$refundMoney += $item['actualprice'];
			}
		}

		$vipOrders = Util::getNumData('price,issettlement', PDO_NAME . 'vip_record', array('#status#' => '(1)', 'aid' => $_W['agent']['id']));

		foreach ($vipOrders[0] as $item) {
			$vipMoney += $item['price'];

			if ($item['issettlement'] == 1) {
				$waitSettlementMoney += $item['price'];
			}

			if ($item['issettlement'] == 2) {
				$settlementMoney += $item['price'];
			}
		}

		$halfOrders = Util::getNumData('price,issettlement', PDO_NAME . 'halfcard_record', array('#status#' => '(1)', 'aid' => $_W['agent']['id']));

		foreach ($vipOrders[0] as $item) {
			$halfMoney += $item['price'];

			if ($item['issettlement'] == 1) {
				$waitSettlementMoney += $item['price'];
			}

			if ($item['issettlement'] == 2) {
				$settlementMoney += $item['price'];
			}
		}

		$orderOrders = Util::getNumData('price,status,issettlement', PDO_NAME . 'order', array('#status#' => '(1,2,3,4,6,7,8)', 'aid' => $_W['agent']['id']));

		foreach ($orderOrders[0] as $item) {
			$orderMoney += $item['price'];

			if ($item['status'] == 7) {
				$refundMoney += $item['price'];
			}

			if ($item['issettlement'] == 1) {
				$waitSettlementMoney += $item['price'];
			}

			if ($item['issettlement'] == 2) {
				$settlementMoney += $item['price'];
			}
		}

		$settlementOrders = Util::getNumData('*', PDO_NAME . 'settlement_record', array('#status#' => '(1,2,3,4,5)', 'aid' => $_W['agent']['id']));

		foreach ($settlementOrders[0] as $item) {
			if ($item['type'] == 1) {
				$spercentMoney += $item['spercentmoney'];
			}

			if ($item['type'] == 2) {
				$halfPercentMoney += $item['agetmoney'];
			}

			if ($item['type'] == 3) {
				$vipPercentMoney += $item['agetmoney'];
			}
		}

		$totalInMoney = sprintf('%.2f', $rushMoney + $vipMoney + $halfMoney + $orderMoney);
		$totalOutMoney = sprintf('%.2f', $refundMoney + $settlementMoney);
		$spercentMoney = sprintf('%.2f', $spercentMoney);
		$halfPercentMoney = sprintf('%.2f', $halfPercentMoney);
		$vipPercentMoney = sprintf('%.2f', $vipPercentMoney);
		$settlementMoney = sprintf('%.2f', $settlementMoney);
		$waitSettlementMoney = sprintf('%.2f', $waitSettlementMoney);
		$data = array('merchantNum' => count($merchants[0]), 'vipNum' => count($members[0]), 'updateTime' => $time, 'todayPv' => $todaypuv['pv'], 'todayUv' => $todaypuv['uv'], 'totalPv' => $numPv, 'totalUv' => $numUv, 'ThisMouthNewFans' => $newfans, 'totalInMoney' => $totalInMoney, 'totalOutMoney' => $totalOutMoney, 'spercentMoney' => $spercentMoney, 'halfPercentMoney' => $halfPercentMoney, 'vipPercentMoney' => $vipPercentMoney, 'settlementMoney' => $settlementMoney, 'waitSettlementMoney' => $waitSettlementMoney);
		Cache::setCache('agentSurvey', 'allData', $data);
		return $data;
	}

	/**
     * 获取系统运营概况（包括代理，会员等）
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function agentMemberSurvey($refresh = 0)
	{
		global $_W;
		$data = Cache::getCache('memberSurvey', 'allData');
		if ($data && !$refresh) {
			return $data;
		}

		$members = Util::getNumData('*', PDO_NAME . 'member', array('vipstatus' => 1, 'aid' => $_W['agent']['id']));
		$address_arr['beijing'] = 0;
		$address_arr['tianjing'] = 0;
		$address_arr['shanghai'] = 0;
		$address_arr['chongqing'] = 0;
		$address_arr['hebei'] = 0;
		$address_arr['yunnan'] = 0;
		$address_arr['liaoning'] = 0;
		$address_arr['heilongjiang'] = 0;
		$address_arr['hunan'] = 0;
		$address_arr['anhui'] = 0;
		$address_arr['shandong'] = 0;
		$address_arr['xingjiang'] = 0;
		$address_arr['jiangshu'] = 0;
		$address_arr['zhejiang'] = 0;
		$address_arr['jiangxi'] = 0;
		$address_arr['hubei'] = 0;
		$address_arr['guangxi'] = 0;
		$address_arr['ganshu'] = 0;
		$address_arr['shanxi'] = 0;
		$address_arr['neimenggu'] = 0;
		$address_arr['sanxi'] = 0;
		$address_arr['jiling'] = 0;
		$address_arr['fujian'] = 0;
		$address_arr['guizhou'] = 0;
		$address_arr['guangdong'] = 0;
		$address_arr['qinghai'] = 0;
		$address_arr['xizhang'] = 0;
		$address_arr['shichuan'] = 0;
		$address_arr['ningxia'] = 0;
		$address_arr['hainan'] = 0;

		foreach ($members[0] as $key => $value) {
			$thisArea = pdo_get(PDO_NAME . 'area', array('id' => $value['areaid']));
			$name = pdo_get(PDO_NAME . 'area', array('id' => $thisArea['pid']));
			$address_name = mb_strcut($name['name'], 0, 6, 'utf-8');

			switch ($address_name) {
			case '北京':
				$address_arr['beijing'] += 1;
				break;

			case '天津':
				$address_arr['tianjing'] += 1;
				break;

			case '上海':
				$address_arr['shanghai'] += 1;
				break;

			case '重庆':
				$address_arr['chongqing'] += 1;
				break;

			case '河北':
				$address_arr['hebei'] += 1;
				break;

			case '河南':
				$address_arr['henan'] += 1;
				break;

			case '云南':
				$address_arr['yunnan'] += 1;
				break;

			case '辽宁':
				$address_arr['liaoning'] += 1;
				break;

			case '黑龙':
				$address_arr['heilongjiang'] += 1;
				break;

			case '湖南':
				$address_arr['hunan'] += 1;
				break;

			case '安徽':
				$address_arr['anhui'] += 1;
				break;

			case '山东':
				$address_arr['shandong'] += 1;
				break;

			case '新疆':
				$address_arr['xingjiang'] += 1;
				break;

			case '江苏':
				$address_arr['jiangshu'] += 1;
				break;

			case '浙江':
				$address_arr['zhejiang'] += 1;
				break;

			case '江西':
				$address_arr['jiangxi'] += 1;
				break;

			case '湖北':
				$address_arr['hubei'] += 1;
				break;

			case '广西':
				$address_arr['guangxi'] += 1;
				break;

			case '甘肃':
				$address_arr['ganshu'] += 1;
				break;

			case '山西':
				$address_arr['shanxi'] += 1;
				break;

			case '内蒙':
				$address_arr['neimenggu'] += 1;
				break;

			case '陕西':
				$address_arr['sanxi'] += 1;
				break;

			case '吉林':
				$address_arr['jiling'] += 1;
				break;

			case '福建':
				$address_arr['fujian'] += 1;
				break;

			case '贵州':
				$address_arr['guizhou'] += 1;
				break;

			case '广东':
				$address_arr['guangdong'] += 1;
				break;

			case '青海':
				$address_arr['qinghai'] += 1;
				break;

			case '西藏':
				$address_arr['xizhang'] += 1;
				break;

			case '四川':
				$address_arr['shichuan'] += 1;
				break;

			case '宁夏':
				$address_arr['ningxia'] += 1;
				break;

			case '海南':
				$address_arr['hainan'] += 1;
				break;
			}
		}

		$where = array();
		$stime = strtotime(date('Y-m-d')) - 86400;
		$etime = strtotime(date('Y-m-d'));
		$where['paytime>'] = $stime;
		$where['paytime<'] = $etime;
		$where['status'] = 1;
		$where['aid'] = $_W['agent']['id'];
		$yesterdayVip = Util::getNumData('*', PDO_NAME . 'vip_record', $where, 'id desc', 0, 0, 1);
		$stime = strtotime(date('Y-m-d'));
		$etime = strtotime(date('Y-m-d')) + 86400;
		$where['paytime>'] = $stime;
		$where['paytime<'] = $etime;
		$where['status'] = 1;
		$where['aid'] = $_W['agent']['id'];
		$todayVip = Util::getNumData('*', PDO_NAME . 'vip_record', $where, 'id desc', 0, 0, 1);
		$stime = strtotime(date('Y-m-d')) - 6 * 86400;
		$etime = strtotime(date('Y-m-d')) + 86400;
		$where['paytime>'] = $stime;
		$where['paytime<'] = $etime;
		$where['status'] = 1;
		$where['aid'] = $_W['agent']['id'];
		$weekVip = Util::getNumData('*', PDO_NAME . 'vip_record', $where, 'id desc', 0, 0, 1);
		$data = array(count($members), $address_arr, $yesterdayVip, $todayVip, $weekVip);
		Cache::setCache('agentMemberSurvey', 'allData', $data);
		return $data;
	}

	static public function cacheSurvey($aid)
	{
		global $_W;
		$condition = 'WHERE uniacid = ' . $_W['uniacid'];

		if ($aid) {
			$condition .= ' AND aid = ' . $aid;

			if ($_W['wlsetting']['distribution']['seetstatus']) {
				$condition2 = $condition . ' AND plugin != \'distribution\' ';
			}
		}

		if (empty($condition2)) {
			$condition2 = $condition;
		}

		$yesterday = date('d') - 1;
		$yessta = mktime(0, 0, 0, date('m'), $yesterday, date('Y'));
		$yesend = mktime(23, 59, 59, date('m'), $yesterday, date('Y'));
		$rushyesmoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend . ' '));
		$halfyesmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend . ' '));
		$otheryesmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend . ' '));
		$data['yesmoney'] = sprintf('%.2f', $rushyesmoney + $halfyesmoney + $otheryesmoney);
		$rushrefyesmoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend . ' AND status = 7'));
		$halfrefyesmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend . ' AND status = 7'));
		$otherrefyesmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend . ' AND status = 7'));
		$data['refyesmoney'] = sprintf('%.2f', $rushrefyesmoney + $halfrefyesmoney + $otherrefyesmoney);
		$data['yesnewmember'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_member') . $condition . (' AND createtime > ' . $yessta . ' AND createtime < ' . $yesend));
		$rushyespaymember = pdo_fetchall('select distinct mid from ' . tablename(PDO_NAME . 'rush_order') . $condition . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend));
		$rushyespaymember = count($rushyespaymember);
		$halfyespaymember = pdo_fetchall('select distinct mid from ' . tablename(PDO_NAME . 'halfcard_record') . $condition . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend));
		$halfyespaymember = count($halfyespaymember);
		$otheryespaymember = pdo_fetchall('select distinct mid from ' . tablename(PDO_NAME . 'order') . $condition2 . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend));
		$otheryespaymember = count($otheryespaymember);
		$data['yespaymember'] = $rushyespaymember + $halfyespaymember + $otheryespaymember;
		$data['yesnewmerchant'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_merchantdata') . $condition . (' AND createtime > ' . $yessta . ' AND createtime < ' . $yesend . ' AND status = 2'));
		$data['yesnewcharge'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend . ' AND plugin = \'store\''));
		$yesnewrushorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND createtime > ' . $yessta . ' AND createtime < ' . $yesend));
		$yesnewhalforder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . (' AND createtime > ' . $yessta . ' AND createtime < ' . $yesend));
		$yesnewotherorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND createtime > ' . $yessta . ' AND createtime < ' . $yesend));
		$data['yesneworder'] = $yesnewrushorder + $yesnewhalforder + $yesnewotherorder;
		$yespaynewrushorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend));
		$yespaynewhalforder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend));
		$yespaynewotherorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND paytime > ' . $yessta . ' AND paytime < ' . $yesend));
		$data['yesnewpayorder'] = $yespaynewrushorder + $yespaynewhalforder + $yespaynewotherorder;

		foreach ($data as $key => &$va) {
			if (empty($va)) {
				$va = 0;
			}
		}

		$i = 29;

		while (0 < $i) {
			$testday = date('d') - $i;
			$teststa = mktime(0, 0, 0, date('m'), $testday, date('Y'));
			$testend = mktime(23, 59, 59, date('m'), $testday, date('Y'));
			$rushyesmoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' '));
			$halfyesmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' '));
			$otheryesmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' '));
			$date = date('m-d', $testend);
			$sales = sprintf('%.2f', $rushyesmoney + $halfyesmoney + $otheryesmoney);
			$li = array('year' => $date, '金额' => (double) $sales);
			$list[] = $li;
			--$i;
		}

		$data['list'] = $list;
		$data['time'] = time();
		return $data;
	}

	static public function newSurvey($aid)
	{
		global $_W;
		$condition = 'WHERE uniacid = ' . $_W['uniacid'];

		if ($aid) {
			$condition .= ' AND aid = ' . $aid;

			if ($_W['wlsetting']['distribution']['seetstatus']) {
				$condition2 = $condition . ' AND plugin != \'distribution\' ';
			}
		}

		if (empty($condition2)) {
			$condition2 = $condition;
		}

		$todaytime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$rushallmoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND paytime > ' . $todaytime));
		$halfallmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . (' AND paytime > ' . $todaytime));
		$otherallmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND paytime > ' . $todaytime));
		$data['allmoney'] = sprintf('%.2f', $otherallmoney + $halfallmoney + $rushallmoney);
		$rushrefmoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND paytime > ' . $todaytime . ' AND status = 7'));
		$halfrefmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . (' AND paytime > ' . $todaytime . ' AND status = 7'));
		$otherrefmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND paytime > ' . $todaytime . ' AND status = 7'));
		$data['refmoney'] = sprintf('%.2f', $rushrefmoney + $halfrefmoney + $otherrefmoney);
		$data['newmember'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_member') . $condition . (' AND createtime > ' . $todaytime));
		$rushpaymember = pdo_fetchall('select distinct mid from ' . tablename(PDO_NAME . 'rush_order') . $condition . (' AND paytime > ' . $todaytime));
		$rushpaymember = count($rushpaymember);
		$halfpaymember = pdo_fetchall('select distinct mid from ' . tablename(PDO_NAME . 'halfcard_record') . $condition . (' AND paytime > ' . $todaytime));
		$halfpaymember = count($halfpaymember);
		$otherpaymember = pdo_fetchall('select distinct mid from ' . tablename(PDO_NAME . 'order') . $condition2 . (' AND paytime > ' . $todaytime));
		$otherpaymember = count($otherpaymember);
		$data['paymember'] = $rushpaymember + $halfpaymember + $otherpaymember;
		$data['newmerchant'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_merchantdata') . $condition . (' AND createtime > ' . $todaytime . ' AND status = 2'));
		$data['newcharge'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND paytime > ' . $todaytime . ' AND plugin = \'store\''));
		$newrushorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND createtime > ' . $todaytime));
		$newhalforder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . (' AND createtime > ' . $todaytime));
		$newotherorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND createtime > ' . $todaytime));
		$data['neworder'] = $newrushorder + $newhalforder + $newotherorder;
		$newpayrushorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND paytime > ' . $todaytime));
		$newpayhalforder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . (' AND paytime > ' . $todaytime));
		$newpayotherorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND paytime > ' . $todaytime));
		$data['newpayorder'] = $newpayrushorder + $newpayhalforder + $newpayotherorder;
		$dfhtotal1 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . $condition . ' AND status = 8');
		$dfhtotal2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'rush_order') . $condition . ' AND status = 8');
		$data['dfhorder'] = $dfhtotal1 + $dfhtotal2;
		$dtktotal1 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . $condition . ' AND status = 6');
		$dtktotal2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'rush_order') . $condition . ' AND status = 6');
		$data['dtkorder'] = $dtktotal2 + $dtktotal1;
		$sqtktotal1 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . $condition . ' AND status IN (1,8) AND applyrefund = 1 ');
		$sqtktotal2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'rush_order') . $condition . ' AND status IN (1,8) AND applyrefund = 1 ');
		$data['sqtkorder'] = $sqtktotal2 + $sqtktotal1;
		$data['merchantnum'] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'merchantuser') . $condition . ' AND status = 1');
		$data['dynamicnum'] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'store_dynamic') . $condition . ' AND status = 0');
		$data['commentnum'] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'comment') . $condition . ' AND checkone = 1');
		$data['storeapply'] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'settlement_record') . $condition . ' AND status = 2 AND type = 1');
		$data['agentapply'] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'settlement_record') . $condition . ' AND status = 2 AND type = 2');
		$data['disapply'] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'settlement_record') . $condition . ' AND status IN (6,7) AND type = 3');
		$data['pocketnum'] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'pocket_informations') . $condition . ' AND status = 1');
		$data['disnum'] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'applydistributor') . $condition . ' AND status = 0');
		$data['rushnum'] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'rush_activity') . $condition . ' AND status = 5');
		$data['grouponnum'] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'groupon_activity') . $condition . ' AND status = 5');

		foreach ($data as $key => &$va) {
			if (empty($va)) {
				$va = 0;
			}
		}

		$sevenday = date('d') - 6;
		$sevensta = mktime(0, 0, 0, date('m'), $sevenday, date('Y'));
		$sevenend = time();
		$rushsevenmoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND paytime > ' . $sevensta . ' AND paytime < ' . $sevenend . ' '));
		$halfsevenmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . (' AND paytime > ' . $sevensta . ' AND paytime < ' . $sevenend . ' '));
		$othersevenmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND paytime > ' . $sevensta . ' AND paytime < ' . $sevenend . ' '));
		$data['sevenmoney'] = sprintf('%.2f', $rushsevenmoney + $halfsevenmoney + $othersevenmoney);
		$threeday = date('d') - 29;
		$threesta = mktime(0, 0, 0, date('m'), $threeday, date('Y'));
		$threeend = time();
		$rushthreemoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND paytime > ' . $threesta . ' AND paytime < ' . $threeend . ' '));
		$halfthreemoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . (' AND paytime > ' . $threesta . ' AND paytime < ' . $threeend . ' '));
		$otherthreemoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition2 . (' AND paytime > ' . $threesta . ' AND paytime < ' . $threeend . ' '));
		$data['threemoney'] = sprintf('%.2f', $rushthreemoney + $halfthreemoney + $otherthreemoney);
		return $data;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
