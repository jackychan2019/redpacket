<?php
//dezend by http://www.sucaihuo.com/
class wlCoupon
{
	/**
     * 保存优惠券母类
     *
     * @access static
     * @name saveCoupon
     * @param mixed  参数一的说明
     * @return array
     */
	static public function saveCoupons($coupon, $param = array())
	{
		global $_W;

		if (!is_array($coupon)) {
			return false;
		}

		$coupon['uniacid'] = $_W['uniacid'];
		$coupon['aid'] = $_W['aid'];

		if (empty($param)) {
			pdo_insert(PDO_NAME . 'couponlist', $coupon);
			return pdo_insertid();
		}

		return false;
	}

	/**
     * 更新优惠券母类
     *
     * @access static
     * @name updateCoupons
     * @param $params  修改参数
     * @param $where   修改条件
     * @return array
     */
	static public function updateCoupons($params, $where)
	{
		$res = pdo_update(PDO_NAME . 'couponlist', $params, $where);

		if ($res) {
			return 1;
		}

		return 0;
	}

	/**
     * 删除优惠券母类
     *
     * @access static
     * @name deleteOrder
     * @param $params  修改参数
     * @param $where   修改条件
     * @return array
     */
	static public function deleteCoupons($where)
	{
		$res = pdo_delete(PDO_NAME . 'couponlist', $where);

		if ($res) {
			return 1;
		}

		return 0;
	}

	/**
     * 删除优惠券用户记录
     *
     * @access static
     * @name deleteOrder
     * @param $params  修改参数
     * @param $where   修改条件
     * @return array
     */
	static public function deleteCoupon($where)
	{
		$res = pdo_delete(PDO_NAME . 'member_coupons', $where);

		if ($res) {
			return 1;
		}

		return 0;
	}

	/**
     * 获取多条优惠券母类记录
     *
     * @access static
     * @name getNumGoods
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getNumCoupons($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'couponlist', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	/**
     * 获取单条优惠券母类
     *
     * @access static
     * @name getSingleOrder
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getSingleCoupons($id, $select, $where = array())
	{
		$where['id'] = $id;
		return Util::getSingelData($select, PDO_NAME . 'couponlist', $where);
	}

	/**
     * 储存单条优惠券
     *
     * @access static
     * @name getSingleOrder
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function saveMemberCoupons($coupon, $param = array())
	{
		global $_W;

		if (!is_array($coupon)) {
			return false;
		}

		$coupon['uniacid'] = $_W['uniacid'];

		if (empty($param)) {
			pdo_insert(PDO_NAME . 'member_coupons', $coupon);
			return pdo_insertid();
		}

		return false;
	}

	/**
     * 获取单条优惠券数量
     *
     * @access static
     * @name getCouponNum
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getCouponNum($parentid, $type)
	{
		global $_W;
		$mid = $_W['mid'];

		if ($type == 1) {
			$num = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_member_coupons') . (' WHERE mid = \'' . $mid . '\' AND parentid = ' . $parentid . ' AND status in (1,2)'));
		}
		else {
			$allorder = pdo_getall('wlmerchant_order', array('mid' => $_W['mid'], 'aid' => $_W['aid'], 'fkid' => $parentid, 'status' => 1, 'plugin' => 'coupon'), array('num'));
			$num = 0;

			foreach ($allorder as $key => $v) {
				$num = $num + $v['num'];
			}
		}

		return $num;
	}

	/**
     * 获取多条优惠券
     *
     * @access static
     * @name getNumCoupon
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getNumCoupon($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$orderInfo = Util::getNumData($select, PDO_NAME . 'member_coupons', $where, $order, $pindex, $psize, $ifpage);
		return $orderInfo;
	}

	/**
     * 获取单条优惠券
     *
     * @access static
     * @name getSingleOrder
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getSingleCoupon($id, $select, $where = array())
	{
		$where['id'] = $id;
		return Util::getSingelData($select, PDO_NAME . 'member_coupons', $where);
	}

	/**
     * 更新优惠券
     *
     * @access static
     * @name updateCoupons
     * @param $params  修改参数
     * @param $where   修改条件
     * @return array
     */
	static public function updateCoupon($params, $where)
	{
		$res = pdo_update(PDO_NAME . 'member_coupons', $params, $where);

		if ($res) {
			return 1;
		}

		return 0;
	}

	/**
     * 保存优惠券母类
     *
     * @access static
     * @name saveCoupon
     * @param mixed  参数一的说明
     * @return array
     */
	static public function saveCouponOrder($data, $param = array())
	{
		global $_W;

		if (!is_array($data)) {
			return false;
		}

		if (empty($param)) {
			pdo_insert(PDO_NAME . 'order', $data);
			return pdo_insertid();
		}

		return false;
	}

	/**
     * 获取多条订单记录
     *
     * @access static
     * @name getNumCouponOrder
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getNumCouponOrder($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'order', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	/**
     * 获取多条店铺记录
     *
     * @access static
     * @name getNumCouponOrder
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getNumstore($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'merchantdata', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	static public function getstores($locations, $lng, $lat)
	{
		global $_W;

		foreach ($locations as $key => $val) {
			$loca = unserialize($val['location']);
			$locations[$key]['distance'] = Store::getdistance($loca['lng'], $loca['lat'], $lng, $lat);
		}

		$i = 0;

		while ($i < count($locations) - 1) {
			$j = 0;

			while ($j < count($locations) - 1 - $i) {
				if ($locations[$j + 1]['distance'] < $locations[$j]['distance']) {
					$temp = $locations[$j + 1];
					$locations[$j + 1] = $locations[$j];
					$locations[$j] = $temp;
				}

				++$j;
			}

			++$i;
		}

		foreach ($locations as $key => $value) {
			if (!empty($value['distance'])) {
				if (1000 < $value['distance']) {
					$locations[$key]['distance'] = floor($value['distance'] / 1000 * 10) / 10 . 'km';
				}
				else {
					$locations[$key]['distance'] = round($value['distance']) . 'm';
				}
			}
		}

		return $locations;
	}

	/**
     * 异步支付结果回调 ，处理业务逻辑
     *
     * @access public
     * @name
     * @param mixed  参数一的说明
     * @return array
     */
	static public function payCouponshargeNotify($params)
	{
		global $_W;
		$order = pdo_get('wlmerchant_order', array('orderno' => $params['tid']), array('id', 'neworderflag', 'sid', 'mid', 'num', 'price', 'orderno', 'fkid', 'aid', 'status', 'vipbuyflag', 'paytype'));
		$coupons = pdo_get('wlmerchant_couponlist', array('id' => $order['fkid']));

		if ($order['status'] == 0) {
			$data1 = self::getCouponshargePayData($params, $order);

			if ($order['neworderflag']) {
				Order::createSmallorder($order['id'], 4);
			}

			if (p('distribution') && empty($coupons['isdistri'])) {
				$_W['aid'] = $order['aid'];
				$dismoney = sprintf('%.2f', $order['price']);

				if ($order['vipbuyflag']) {
					$onemoney = sprintf('%.2f', $coupons['viponedismoney'] * $order['num']);
					$twomoney = sprintf('%.2f', $coupons['viptwodismoney'] * $order['num']);
					$threemoney = sprintf('%.2f', $coupons['vipthreedismoney'] * $order['num']);
				}
				else {
					$onemoney = sprintf('%.2f', $coupons['onedismoney'] * $order['num']);
					$twomoney = sprintf('%.2f', $coupons['twodismoney'] * $order['num']);
					$threemoney = sprintf('%.2f', $coupons['threedismoney'] * $order['num']);
				}

				$disorderid = Distribution::disCore($order['mid'], $dismoney, $onemoney, $twomoney, $threemoney, $order['id'], 'coupon', $coupons['dissettime']);
				$data1['disorderid'] = $disorderid;
			}

			if (p('paidpromotion')) {
				$data1['paidprid'] = Paidpromotion::getpaidpr(3, $order['id'], $data1['paytype']);
			}

			if (p('userlabel')) {
				$_W['aid'] = $order['aid'];
				Userlabel::addlabel($order['mid'], $order['fkid'], 'coupon');
			}

			if ($coupons['time_type'] == 1) {
				$starttime = $coupons['starttime'];
				$endtime = $coupons['endtime'];
			}
			else {
				$starttime = time();
				$endtime = time() + $coupons['deadline'] * 24 * 3600;
			}

			$data = array('mid' => $order['mid'], 'aid' => $order['aid'], 'parentid' => $coupons['id'], 'status' => 1, 'type' => $coupons['type'], 'title' => $coupons['title'], 'sub_title' => $coupons['sub_title'], 'content' => $coupons['goodsdetail'], 'description' => $coupons['description'], 'color' => $coupons['color'], 'starttime' => $starttime, 'endtime' => $endtime, 'createtime' => time(), 'orderno' => $params['tid'], 'price' => $order['price'] / $order['num'], 'usetimes' => $coupons['usetimes'] * $order['num'], 'concode' => Util::createConcode(4));
			$data1['recordid'] = self::saveMemberCoupons($data);
			$data1['estimatetime'] = $data['endtime'];
			pdo_update(PDO_NAME . 'order', $data1, array('id' => $order['id']));
			$newsurplus = $coupons['surplus'] - $order['num'];
			self::updateCoupons(array('surplus' => $newsurplus), array('id' => $coupons['id']));
			$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('openid'));
			$openid = $member['openid'];
			Store::addFans($order['sid'], $order['mid']);
			VoiceAnnouncements::PushVoiceMessage($order['price'], $order['sid'], $order['paytype']);
			$storeadminopenid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $order['mid']), 'openid');
			$first = '您的优惠卷已经成功付款';
			$acname = '优惠券到账';
			$acresult = '已获取';
			$remark = '';
			$url = app_url('wlcoupon/coupon_app/couponDetail', array('id' => $data1['recordid']));
			Message::jobNotice($storeadminopenid, $first, $acname, $acresult, $remark, $url);
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
	static public function getCouponshargePayData($params, $order_out)
	{
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
		$data['price'] = $fee;
		SingleMerchant::updateAmount($fee, $order_out['sid'], $order_out['id'], 1, '卡券订单支付成功');
		return $data;
	}

	/**
     * 函数的含义说明
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function payCouponshargeReturn($params)
	{
		$res = $params['result'] == 'success' ? 1 : 0;
		$order_out = pdo_get(PDO_NAME . 'order', array('orderno' => $params['tid']), array('id'));
		wl_message('支付成功', app_url('order/userorder/payover', array('id' => $order_out['id'], 'type' => 3)), 'success');
	}

	static public function hexiaoorder($id, $mid, $num = 1, $type = 1, $checkcode = '')
	{
		global $_W;
		$coupon = self::getSingleCoupon($id, 'mid,status,orderno,usedtime,endtime,starttime,usetimes,parentid,orderno,title,concode,aid');

		if ($coupon['orderno']) {
			$order = pdo_get('wlmerchant_order', array('orderno' => $coupon['orderno']), array('id', 'neworderflag', 'sid'));
		}

		if ($order['neworderflag']) {
			$coupon['usetimes'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'coupon\' AND  orderid = ' . $order['id'] . ' AND status = 1'));
		}

		if ($coupon['usetimes'] < 1) {
			show_json(0, '该优惠券已失效');
		}

		if ($coupon['usetimes'] < $num) {
			show_json(0, '可核销次数不足');
		}

		if (time() < $coupon['starttime']) {
			$starttime = date('Y-m-d H:i:s', $coupon['starttime']);
			show_json(0, '该优惠券在' . $starttime . '后方可使用');
		}

		if ($coupon['endtime'] < time()) {
			show_json(0, '该优惠券已过期');
		}

		if ($order['neworderflag']) {
			if ($checkcode) {
				$smallorders = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_smallorder') . ('WHERE plugin = \'coupon\' AND  orderid = ' . $order['id'] . ' AND status = 1 AND checkcode = ' . $checkcode));
			}
			else {
				$smallorders = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_smallorder') . ('WHERE plugin = \'coupon\' AND  orderid = ' . $order['id'] . ' AND status = 1 ORDER BY id ASC LIMIT ' . $num));
			}

			if ($smallorders) {
				if ($mid) {
					$uid = pdo_getcolumn(PDO_NAME . 'merchantuser', array('storeid' => $order['sid'], 'mid' => $mid), 'id');
				}
				else {
					$uid = 0;
				}

				foreach ($smallorders as $k => $small) {
					$res = Order::finishSmallorder($small['id'], $uid, $type);
				}
			}
			else if (is_mobile()) {
				exit(json_encode(array('errno' => 1, 'message' => '无可用核销码')));
			}
			else {
				show_json(0, '无可用核销码');
			}
		}
		else {
			$arr = array();

			if ($coupon['usedtime']) {
				$a = unserialize($coupon['usedtime']);
				$i = 0;

				while ($i < $num) {
					$arr['time'] = time();
					$arr['type'] = $type;
					$arr['ver'] = $mid;
					$a[] = $arr;
					++$i;
				}

				$coupon['usedtime'] = serialize($a);
			}
			else {
				$a = array();
				$i = 0;

				while ($i < $num) {
					$arr['time'] = time();
					$arr['type'] = $type;
					$arr['ver'] = $mid;
					$a[] = $arr;
					++$i;
				}

				$coupon['usedtime'] = serialize($a);
			}

			$params['usetimes'] = $coupon['usetimes'] - $num;
			$params['usedtime'] = $coupon['usedtime'];
			$res = self::updateCoupon($params, array('id' => $id));
		}

		if ($res) {
			$member = pdo_get('wlmerchant_member', array('id' => $coupon['mid']), array('openid'));
			$openid = $member['openid'];
			$url = app_url('wlcoupon/coupon_app/couponDetail', array('id' => $id));
			Message::hexiaoSuccess($openid, $num, $coupon['title'], $url);

			if ($type == 2) {
				Message::hexiaoTover($_W['wlmember']['openid'], $num, $coupon['title']);
			}

			if (empty($order['neworderflag'])) {
				$order = pdo_get('wlmerchant_order', array('orderno' => $coupon['orderno']), array('id'));
				$goods = pdo_get('wlmerchant_couponlist', array('id' => $coupon['parentid']), array('merchantid', 'title'));
				SingleMerchant::verifRecordAdd($coupon['aid'], $goods['merchantid'], $coupon['mid'], 'wlcoupon', $order['id'], $coupon['concode'], $goods['title'], $type, $num);

				if ($params['usetimes'] < 1) {
					$res2 = pdo_update('wlmerchant_order', array('status' => 2), array('orderno' => $coupon['orderno']));
					$orderid = pdo_getcolumn(PDO_NAME . 'order', array('orderno' => $coupon['orderno']), 'id');
					$ordertask = array('type' => 'wlcoupon', 'orderid' => $orderid);
					$ordertask = serialize($ordertask);
					Queue::addTask(2, $ordertask, time(), $orderid);
					$disorderid = pdo_getcolumn('wlmerchant_order', array('orderno' => $coupon['orderno']), 'disorderid');

					if ($disorderid) {
						$res = pdo_update('wlmerchant_disorder', array('status' => 1), array('id' => $disorderid, 'status' => 0));

						if ($res) {
							$distask = array('type' => 'wlcoupon', 'orderid' => $disorderid);
							$distask = serialize($distask);
							Queue::addTask(3, $distask, time(), $disorderid);
						}
					}
				}
			}

			return 1;
		}

		return 0;
	}

	static public function cutoffFollow($id, $mid, $title, $sid, $cutofftime)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$where2['id'] = $mid;
		$member = Util::getSingelData('nickname,openid', 'wlmerchant_member', $where2);
		$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $sid), 'storename');
		$aid = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $sid), 'aid');
		$orderno = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $sid), 'orderno');
		$num = pdo_getcolumn(PDO_NAME . 'order', array('orderno' => $orderno), 'num');

		if (empty($orderno)) {
			$orderno = '免费卡券';
		}

		if (empty($num)) {
			$num = 1;
		}

		$url = app_url('wlcoupon/coupon_app/couponDetail', array('id' => $id, 'aid' => $aid));

		if (!$notice['overtimeSwitch']) {
			$msg = '您好，您有即将过期的待消费订单。' . '
';
			$msg .= '用户名：' . $member['nickname'] . '
';
			$msg .= '商品名称：' . $title . '
';
			$msg .= '截止时间：' . date('Y年m月d日 H:i:s', $cutofftime) . '
';
			$msg .= '地点：' . $storename . '
';
			$msg .= '点击立即去消费，赶快行动吧。' . '
';
			return Message::sendCustomNotice($member['openid'], $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => '您好，您有即将过期的待使用卡券。', 'color' => '#173177'),
			'keyword1' => array('value' => $orderno, 'color' => '#173177'),
			'keyword2' => array('value' => $title, 'color' => '#173177'),
			'keyword3' => array('value' => $num, 'color' => '#173177'),
			'keyword4' => array('value' => date('Y年m月d日 H:i:s', $cutofftime), 'color' => '#173177'),
			'remark'   => array('value' => '点击立即使用卡券，赶快行动吧。', 'color' => '#173177')
		);
		return Message::sendtplnotice($member['openid'], $notice['overtime'], $postdata, $url);
	}

	static public function refund($id, $money, $unline = '')
	{
		global $_W;
		$item = pdo_get(PDO_NAME . 'order', array('id' => $id));
		if (empty($money) && $item['neworderflag']) {
			$money = pdo_fetchcolumn('SELECT SUM(orderprice) FROM ' . tablename(PDO_NAME . 'smallorder') . (' WHERE plugin = \'coupon\' AND orderid = ' . $id . ' AND status = 1'));
			$refundnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename(PDO_NAME . 'smallorder') . (' WHERE plugin = \'coupon\' AND orderid = ' . $id . ' AND status = 1'));
		}
		else {
			$refundnum = $item['usetimes'];
		}

		if ($unline) {
			$res['status'] = 1;
		}
		else {
			$res = wlPay::refundMoney($id, $money, '卡券订单退款', 'coupon', 2);
		}

		if ($res['status']) {
			if ($item['neworderflag']) {
				pdo_update('wlmerchant_smallorder', array('status' => 3, 'refundtime' => time()), array('plugin' => 'coupon', 'orderid' => $id, 'status' => 1));

				if ($item['applyrefund']) {
					$reason = '买家申请退款。';
					$orderdata['applyrefund'] = 2;
				}
				else {
					$reason = '卡券系统退款。';
				}

				$hexiao = pdo_get('wlmerchant_smallorder', array('orderid' => $id, 'plugin' => 'coupon', 'status' => 2), array('id'));

				if ($hexiao) {
					$orderdata['status'] = 2;
					$orderdata['issettlement'] = 1;
				}
				else {
					$orderdata['status'] = 7;
					$orderdata['refundtime'] = time();
				}

				pdo_update('wlmerchant_order', $orderdata, array('id' => $item['id']));
			}
			else if ($item['applyrefund']) {
				pdo_update('wlmerchant_order', array('status' => 7, 'refundtime' => time(), 'applyrefund' => 2), array('id' => $item['id']));
				$reason = '买家申请退款。';
			}
			else {
				pdo_update('wlmerchant_order', array('status' => 7, 'refundtime' => time()), array('id' => $item['id']));
				$reason = '卡券系统退款。';
			}

			pdo_update('wlmerchant_member_coupons', array('status' => 2, 'usetimes' => 0), array('id' => $item['recordid']));
			$url = app_url('wlcoupon/coupon_app/couponslist');
			$money = $money ? '￥' . $money : '￥' . $item['price'];
			$member = pdo_get('wlmerchant_member', array('id' => $item['mid']), array('openid'));
			$openid = $member['openid'];

			if ($item['disorderid']) {
				Distribution::refunddis($item['disorderid']);
			}

			Message::refundNotice($openid, $reason, $money, $url);
			pdo_fetch('update' . tablename('wlmerchant_order') . ('SET surplus = surplus+' . $item['num'] . ' WHERE id = ' . $id));
		}
		else {
			pdo_fetch('update' . tablename('wlmerchant_order') . ('SET failtimes = failtimes+1 WHERE id = ' . $id));
		}

		return $res;
	}

	static public function doTask()
	{
		global $_W;
		$cutoffcoupon = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_member_coupons') . 'WHERE usetimes > 1 AND cutoffnotice = 0 ORDER BY id ASC limit 0,20 ');

		if (!empty($cutoffcoupon)) {
			foreach ($cutoffcoupon as $k => $v) {
				$_W['uniacid'] = $v['uniacid'];
				$_W['aid'] = $v['aid'];
				$sid = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $v['parentid']), 'merchantid');
				$cutofftime = $v['endtime'];
				$config = Setting::agentsetting_read('coupon');
				$cutoff_time = $config['cutoff_time'] ? intval($config['cutoff_time']) : 7;
				if (time() < $cutofftime && $cutofftime < time() + $cutoff_time * 24 * 3600) {
					self::cutoffFollow($v['id'], $v['mid'], $v['title'], $sid, $cutofftime);
					pdo_update(PDO_NAME . 'member_coupons', array('cutoffnotice' => 1), array('id' => $v['id']));
				}
			}
		}

		$noestorder = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_order') . 'WHERE plugin = \'coupon\' AND status = 1 AND estimatetime = 0 ORDER BY id DESC limit 20');

		if ($noestorder) {
			foreach ($noestorder as $key => $noest) {
				$estimatetime = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $noest['recordid']), 'endtime');
				pdo_update('wlmerchant_order', array('estimatetime' => $estimatetime), array('id' => $noest['id']));
			}
		}

		$nowtime = time();
		$cutoffcou = pdo_fetchall('SELECT orderno FROM ' . tablename('wlmerchant_member_coupons') . ('WHERE status = 1 AND usetimes > 0 AND orderno > 0 AND endtime < ' . $nowtime . ' ORDER BY id DESC'));

		if (!empty($cutoffcou)) {
			foreach ($cutoffcou as $key => $cutcou) {
				pdo_update('wlmerchant_member_coupons', array('status' => 3), array('orderno' => $cutcou['orderno']));
				pdo_update('wlmerchant_order', array('status' => 9), array('orderno' => $cutcou['orderno']));
			}
		}

		pdo_fetch('update' . tablename('wlmerchant_couponlist') . 'SET name=title WHERE name = 0');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
