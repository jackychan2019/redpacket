<?php
//dezend by http://www.sucaihuo.com/
class Groupon
{
	/**
     * 初始化商品数据
     *
     * @access static
     * @name  initSingleGoods
     * @param $goodsInfo  商品数据
     * @return $goodsInfo
     */
	static public function initSingleGoods($goodsInfo)
	{
		$goodsInfo['thumb'] = tomedia($goodsInfo['thumb']);
		$goodsInfo['plugin'] = 'groupon';
		$goodsInfo['a'] = app_url('groupon/home/detail', array('id' => $goodsInfo['id']));
		return $goodsInfo;
	}

	/**
     * 删除商品
     *
     * @access static
     * @name deleteActive
     * @param $params  修改参数
     * @param $where   修改条件
     * @return array
     */
	static public function deleteGoods($where)
	{
		$res = pdo_delete(PDO_NAME . 'goodshouse', $where);

		if ($where['id']) {
			Cache::deleteCache('goods', $where['id']);
		}

		if ($res) {
			return 1;
		}

		return 0;
	}

	/**
     * 获取所有商户
     *
     * @access static
     * @name getNumMerchant
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getNumMerchant($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$merchantInfo = Util::getNumData($select, PDO_NAME . 'merchantdata', $where, $order, $pindex, $psize, $ifpage);
		return $merchantInfo;
	}

	/**
     * 获取单条商户数据
     *
     * @access static
     * @name getSingleMerchant
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getSingleMerchant($id, $select, $where = array())
	{
		$where['id'] = $id;
		return Util::getSingelData($select, PDO_NAME . 'merchantdata', $where);
	}

	/**
     * 保存团购活动
     *
     * @access static
     * @name savegrouponActive
     * @param mixed  参数一的说明
     * @return array
     */
	static public function savegrouponActive($active, $param = array())
	{
		global $_W;

		if (!is_array($active)) {
			return false;
		}

		$active['uniacid'] = $_W['uniacid'];
		$active['aid'] = $_W['aid'];
		$active['sid'] = $_W['sid'] ? $_W['sid'] : $active['sid'];
		$active['createtime'] = time();

		if (empty($param)) {
			pdo_insert(PDO_NAME . 'groupon_activity', $active);
			return pdo_insertid();
		}

		return false;
	}

	/**
     * 获取多条活动数据
     *
     * @access static
     * @name getNumActive
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getNumActive($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$activeInfo = Util::getNumData($select, PDO_NAME . 'groupon_activity', $where, $order, $pindex, $psize, $ifpage);
		return $activeInfo;
	}

	/**
     * 获取单条活动数据
     *
     * @access static
     * @name getSingleActive
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getSingleActive($id, $select, $where = array())
	{
		$where['id'] = $id;
		$goodsInfo = Util::getSingelData($select, PDO_NAME . 'groupon_activity', $where);

		if (empty($goodsInfo)) {
			return array();
		}

		return self::initSingleGoods($goodsInfo);
	}

	/**
     * 更新活动
     *
     * @access static
     * @name updateActive
     * @param $params  修改参数
     * @param $where   修改条件
     * @return array
     */
	static public function updateActive($params, $where)
	{
		$res = pdo_update(PDO_NAME . 'groupon_activity', $params, $where);

		if ($where['id']) {
			Cache::deleteCache('active', $where['id']);
		}

		if ($res) {
			return 1;
		}

		return 0;
	}

	/**
     * 删除活动
     *
     * @access static
     * @name deleteActive
     * @param $params  修改参数
     * @param $where   修改条件
     * @return array
     */
	static public function deleteActive($where)
	{
		$res = pdo_delete(PDO_NAME . 'groupon_activity', $where);

		if ($where['id']) {
			Cache::deleteCache('active', $where['id']);
		}

		if ($res) {
			return 1;
		}

		return 0;
	}

	/**
     * 活动状态判断
     *
     * @access static
     * @name deleteActive
     * @param $params  修改参数
     * @param $arr   修改条件
     * @return array
     */
	static public function changeActivestatus($arr)
	{
		if (empty($arr)) {
			return false;
		}

		if (is_numeric($arr)) {
			$arr = self::getSingleActive($arr, 'id,starttime,endtime,levelnum');
		}

		if (!is_array($arr) || empty($arr)) {
			return false;
		}

		if ($arr['status'] == 1 || $arr['status'] == 2 || $arr['status'] == 3 || $arr['status'] == 7) {
			if (time() < $arr['starttime']) {
				$goods['status'] = 1;
			}
			else {
				if ($arr['starttime'] < time() && time() < $arr['endtime'] && 0 < $arr['levelnum']) {
					$goods['status'] = 2;
				}
				else {
					if ($arr['endtime'] < time()) {
						$goods['status'] = 3;
					}
				}
			}

			self::updateActive($goods, array('id' => $arr['id']));
		}
	}

	/**
     * 获取单条订单数据
     *
     * @access static
     * @name getSingleOrder
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getSingleOrder($id, $select, $where = array())
	{
		$where['id'] = $id;
		$data = Util::getSingelData($select, PDO_NAME . 'order', $where);
		return self::initSingleOrder($data);
	}

	/**
     * 获取多条订单数据
     *
     * @access static
     * @name getNumOrder
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getNumOrder($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$where['plugin'] = 'groupon';
		$orderInfo = Util::getNumData($select, PDO_NAME . 'order', $where, $order, $pindex, $psize, $ifpage);
		$newOrderInfo = array();

		foreach ($orderInfo[0] as $k => $v) {
			$newOrderInfo[$k] = self::initSingleOrder($v);
		}

		return array($newOrderInfo, $orderInfo[1], $orderInfo[2]) ? array($newOrderInfo, $orderInfo[1], $orderInfo[2]) : array();
	}

	/**
     * 初始化商品数据
     *
     * @access static
     * @name  initSingleGoods
     * @param $goodsInfo  商品数据
     * @return $goodsInfo
     */
	static public function initSingleOrder($orderInfo)
	{
		$active = self::getSingleActive($orderInfo['fkid'], '*');
		$member = self::getSingleMember($orderInfo['mid'], '*');
		$orderInfo['gimg'] = $active['thumb'];
		$orderInfo['unit'] = $active['unit'];
		$orderInfo['gname'] = $active['name'];
		$orderInfo['nickname'] = $orderInfo['username'] ? $orderInfo['username'] : $member['nickname'];
		$orderInfo['headimg'] = $member['avatar'];
		$orderInfo['mobile'] = $orderInfo['mobile'] ? $orderInfo['mobile'] : $member['mobile'];
		$orderInfo['addname'] = $orderInfo['address'];
		$merchant = SingleMerchant::getSingleMerchant($orderInfo['sid'], '*');
		$orderInfo['merchantName'] = $merchant['storename'];
		$orderInfo['merchantId'] = $merchant['id'];
		$orderInfo['merchantLogo'] = tomedia($merchant['logo']);
		$orderInfo['plugin'] = 'groupon';
		$orderInfo['goodsprice'] = sprintf('%.2f', $orderInfo['price'] / $orderInfo['num']);

		if ($orderInfo['specid']) {
			$orderInfo['optiontitle'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $orderInfo['specid']), 'title');
		}

		$record = pdo_get(PDO_NAME . 'groupon_userecord', array('orderid' => $orderInfo['id']), array('usedtime', 'qrcode'));
		$orderInfo['checkcode'] = $record['qrcode'];
		$orderInfo['usedtime'] = $record['usedtime'];
		return $orderInfo;
	}

	/**
     * 更新订单
     *
     * @access static
     * @name updateOrder
     * @param $params  修改参数
     * @param $where   修改条件
     * @return array
     */
	static public function updateOrder($params, $where)
	{
		$res = pdo_update(PDO_NAME . 'order', $params, $where);

		if ($res) {
			return 1;
		}

		return 0;
	}

	/**
     * 删除订单
     *
     * @access static
     * @name deleteOrder
     * @param $params  修改参数
     * @param $where   修改条件
     * @return array
     */
	static public function deleteOrder($where)
	{
		$res = pdo_delete(PDO_NAME . 'order', $where);

		if ($res) {
			return 1;
		}

		return 0;
	}

	/**
     * 获取单条用户数据
     *
     * @access static
     * @name getSingleMember
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getSingleMember($id, $select, $where = array())
	{
		$where['id'] = $id;
		return Util::getSingelData($select, PDO_NAME . 'member', $where);
	}

	/**
     * 异步支付结果回调 ，处理业务逻辑
     *
     * @access public
     * @name
     * @param mixed  参数一的说明
     * @return array
     */
	static public function paygrouponOrderNotify($params)
	{
		global $_W;
		Util::wl_log('payResult_notify', PATH_PLUGIN . 'groupon/data/', $params);
		$order_out = pdo_fetch('select * from' . tablename(PDO_NAME . 'order') . ('where orderno=\'' . $params['tid'] . '\''));
		$activeInfo = self::getSingleActive($order_out['fkid'], '*');
		$data = self::getgrouponOrderPayData($params, $order_out);

		if ($order_out) {
			$orderid = $order_out['id'];
			$_W['aid'] = $order_out['aid'];

			if (empty($order_out['expressid'])) {
				if ($order_out['neworderflag']) {
					Order::createSmallorder($order_out['id'], 2);
				}
				else {
					$recordid = self::createRecord($order_out['id'], $order_out['num']);
					$data['recordid'] = $recordid;
				}
			}
			else {
				$data['status'] = 8;
			}

			if (p('distribution') && empty($activeInfo['isdistri'])) {
				if ($order_out['specid']) {
					$option = pdo_get('wlmerchant_goods_option', array('id' => $order_out['specid']), array('onedismoney', 'twodismoney', 'threedismoney'));
					$onemoney = sprintf('%.2f', $option['onedismoney'] * $order_out['num']);
					$twomoney = sprintf('%.2f', $option['twodismoney'] * $order_out['num']);
					$threemoney = sprintf('%.2f', $option['threedismoney'] * $order_out['num']);
				}
				else if ($order_out['vipbuyflag']) {
					$onemoney = sprintf('%.2f', $activeInfo['viponedismoney'] * $order_out['num']);
					$twomoney = sprintf('%.2f', $activeInfo['viptwodismoney'] * $order_out['num']);
					$threemoney = sprintf('%.2f', $activeInfo['vipthreedismoney'] * $order_out['num']);
				}
				else {
					$onemoney = sprintf('%.2f', $activeInfo['onedismoney'] * $order_out['num']);
					$twomoney = sprintf('%.2f', $activeInfo['twodismoney'] * $order_out['num']);
					$threemoney = sprintf('%.2f', $activeInfo['threedismoney'] * $order_out['num']);
				}

				$disorderid = Distribution::disCore($order_out['mid'], $order_out['price'], $onemoney, $twomoney, $threemoney, $order_out['id'], 'groupon', $activeInfo['dissettime']);
				$data['disorderid'] = $disorderid;
			}

			if (p('paidpromotion')) {
				$data['paidprid'] = Paidpromotion::getpaidpr(4, $order_out['id'], $data['paytype']);
			}

			if ($activeInfo['sharestatus']) {
				pdo_update('wlmerchant_sharegift_record', array('status' => 1), array('id' => $order_out['shareid']));
			}

			if ($activeInfo['cutoffstatus']) {
				$data['estimatetime'] = time() + $activeInfo['cutoffday'] * 86400;
			}
			else {
				$data['estimatetime'] = $activeInfo['cutofftime'];
			}

			if (p('userlabel')) {
				$_W['aid'] = $order_out['aid'];
				Userlabel::addlabel($order_out['mid'], $order_out['fkid'], 'groupon');
			}

			pdo_update(PDO_NAME . 'order', $data, array('orderno' => $params['tid']));
			$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $order_out['mid']), 'openid');
			$url = app_url('order/userorder/orderlist', array('status' => 'all'));
			Store::addFans($order_out['sid'], $order_out['mid']);
			Message::paySuccess($order_out['id'], 'groupon');
		}
		else {
			if ($data['status']) {
				$data['status'] = 6;
				pdo_update(PDO_NAME . 'order', $data, array('orderno' => $params['tid']));
			}
		}
	}

	static public function createRecord($orderid, $num)
	{
		global $_W;
		$record['uniacid'] = $_W['uniacid'];
		$record['aid'] = $_W['aid'];
		$record['orderid'] = $orderid;
		$record['qrcode'] = Util::createConcode(5);
		$record['createtime'] = time();
		$record['usetimes'] = $num;
		pdo_insert(PDO_NAME . 'groupon_userecord', $record);
		return pdo_insertid();
	}

	/**
     * 函数的含义说明
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function paygrouponOrderReturn($params, $backurl = false)
	{
		Util::wl_log('payResult_return', PATH_PLUGIN . 'groupon/data/', $params);
		$order_out = pdo_get(PDO_NAME . 'order', array('orderno' => $params['tid']), array('id'));
		wl_message('团购成功', app_url('order/userorder/payover', array('id' => $order_out['id'], 'type' => 4)), 'success');
	}

	/**
     * 函数的含义说明
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function getgrouponOrderPayData($params, $order_out)
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
		$data['createtime'] = TIMESTAMP;
		SingleMerchant::updateAmount($fee, $order_out['sid'], $order_out['id'], 1, '订单支付成功');
		return $data;
	}

	static public function cutoffFollow($id, $mid, $orderid, $sid)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$where2['id'] = $mid;
		$member = Util::getSingelData('nickname,openid', 'wlmerchant_member', $where2);
		$goods = groupon::getSingleActive($id, 'name,cutofftime,cutoffstatus,cutoffday');
		$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $sid), 'storename');
		$url = app_url('order/userorder/orderdetail', array('orderid' => $orderid, 'type' => 'groupon'));
		$order = pdo_get(PDO_NAME . 'order', array('id' => $orderid), array('orderno', 'num', 'paytime'));

		if ($goods['cutoffstatus']) {
			$cutofftime = $order['paytime'] + $goods['cutoffday'] * 24 * 3600;
		}
		else {
			$cutofftime = $goods['cutofftime'];
		}

		if (!$notice['overtimeSwitch']) {
			$msg = '您好，您有即将过期的待消费订单。' . '
';
			$msg .= '用户名：' . $member['nickname'] . '
';
			$msg .= '商品名称：' . $goods['name'] . '
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
			'first'    => array('value' => '您好，您有即将过期的待消费订单。', 'color' => '#173177'),
			'keyword1' => array('value' => $order['orderno'], 'color' => '#173177'),
			'keyword2' => array('value' => $goods['name'], 'color' => '#173177'),
			'keyword3' => array('value' => $order['num'], 'color' => '#173177'),
			'keyword4' => array('value' => date('Y年m月d日 H:i:s', $cutofftime), 'color' => '#173177'),
			'remark'   => array('value' => '点击立即参加抢购活动，赶快行动吧。', 'color' => '#173177')
		);
		return Message::sendtplnotice($member['openid'], $notice['overtime'], $postdata, $url);
	}

	static public function hexiaoorder($id, $mid, $num = 1, $type = 1, $checkcode = '')
	{
		global $_W;
		$order = pdo_get('wlmerchant_order', array('id' => $id));
		$cutoff = pdo_get(PDO_NAME . 'groupon_activity', array('id' => $order['fkid']), array('cutofftime', 'cutoffstatus', 'cutoffday'));

		if ($order['neworderflag']) {
			$order['usetimes'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'groupon\' AND  orderid = ' . $id . ' AND status = 1'));
		}
		else {
			$record = pdo_get('wlmerchant_groupon_userecord', array('id' => $order['recordid']));
		}

		if ($order['estimatetime'] < time()) {
			if (is_mobile()) {
				exit(json_encode(array('errno' => 2, 'message' => '已超过截止日期，无法核销')));
			}
			else {
				show_json(0, '已超过截止日期，无法核销');
			}
		}

		if ($order['status'] != 1) {
			if (is_mobile()) {
				exit(json_encode(array('errno' => 1, 'message' => '核销失败,订单已核销')));
			}
			else {
				show_json(0, '核销失败,订单已核销');
			}
		}

		if ($order['neworderflag']) {
			if ($checkcode) {
				$smallorders = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_smallorder') . ('WHERE plugin = \'groupon\' AND  orderid = ' . $id . ' AND status = 1 AND checkcode = ' . $checkcode));
			}
			else {
				$smallorders = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_smallorder') . ('WHERE plugin = \'groupon\' AND  orderid = ' . $id . ' AND status = 1 ORDER BY id ASC LIMIT ' . $num));
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

			if ($record['usedtime']) {
				$a = unserialize($record['usedtime']);
				$i = 0;

				while ($i < $num) {
					$arr['time'] = time();
					$arr['type'] = $type;
					$arr['ver'] = $mid;
					$a[] = $arr;
					++$i;
				}

				$record['usedtime'] = serialize($a);
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

				$record['usedtime'] = serialize($a);
			}

			$params['usetimes'] = $record['usetimes'] - $num;
			$params['usedtime'] = $record['usedtime'];

			if ($params['usetimes'] < 1) {
				pdo_update('wlmerchant_order', array('status' => 2), array('id' => $order['id']));
				$ordertask = array('type' => 'groupon', 'orderid' => $order['id']);
				$ordertask = serialize($ordertask);
				Queue::addTask(2, $ordertask, time(), $order['id']);

				if ($order['disorderid']) {
					$res = pdo_update('wlmerchant_disorder', array('status' => 1), array('id' => $order['disorderid'], 'status' => 0));

					if ($res) {
						$distask = array('type' => 'groupon', 'orderid' => $order['disorderid']);
						$distask = serialize($distask);
						Queue::addTask(3, $distask, time(), $order['disorderid']);
					}
				}
			}

			$res = pdo_update('wlmerchant_groupon_userecord', $params, array('id' => $record['id']));
		}

		if ($res) {
			$active = pdo_get('wlmerchant_groupon_activity', array('id' => $order['fkid']), array('name'));
			$order['checkcode'] = pdo_getcolumn(PDO_NAME . 'groupon_userecord', array('id' => $order['recordid']), 'qrcode');
			SingleMerchant::verifRecordAdd($order['aid'], $order['sid'], $order['mid'], 'groupon', $order['id'], $order['checkcode'], $active['name'], $type, $num);
			$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('openid'));
			Message::hexiaoSuccess($member['openid'], $num, $active['name'], '');

			if ($type == 2) {
				Message::hexiaoTover($_W['wlmember']['openid'], $num, $active['name']);
			}

			return 1;
		}

		return 0;
	}

	static public function refund($id, $money, $unline = '')
	{
		global $_W;
		$order = pdo_get(PDO_NAME . 'order', array('id' => $id));
		if (empty($money) && $order['neworderflag']) {
			$money = pdo_fetchcolumn('SELECT SUM(orderprice) FROM ' . tablename(PDO_NAME . 'smallorder') . (' WHERE plugin = \'groupon\' AND orderid = ' . $id . ' AND status = 1'));
			$refundnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename(PDO_NAME . 'smallorder') . (' WHERE plugin = \'groupon\' AND orderid = ' . $id . ' AND status = 1'));
		}
		else {
			$refundnum = $order['usetimes'];
		}

		if ($unline) {
			$res['status'] = 1;
		}
		else {
			$res = wlPay::refundMoney($id, $money, '团购订单退款', 'groupon', 2);
		}

		if ($res['status']) {
			if ($order['neworderflag']) {
				pdo_update('wlmerchant_smallorder', array('status' => 3, 'refundtime' => time()), array('plugin' => 'groupon', 'orderid' => $id, 'status' => 1));

				if ($order['applyrefund']) {
					$reason = '买家申请退款。';
					$orderdata['applyrefund'] = 2;
				}
				else {
					$reason = '团购系统退款。';
				}

				$hexiao = pdo_get('wlmerchant_smallorder', array('orderid' => $id, 'plugin' => 'groupon', 'status' => 2), array('id'));

				if ($hexiao) {
					$orderdata['status'] = 2;
					$orderdata['issettlement'] = 1;
				}
				else {
					$orderdata['status'] = 7;
					$orderdata['refundtime'] = time();
				}

				pdo_update('wlmerchant_order', $orderdata, array('id' => $order['id']));
			}
			else if ($order['applyrefund']) {
				pdo_update('wlmerchant_order', array('status' => 7, 'refundtime' => time(), 'applyrefund' => 2), array('id' => $order['id']));
				$reason = '买家申请退款。';
			}
			else {
				pdo_update('wlmerchant_order', array('status' => 7, 'refundtime' => time()), array('id' => $order['id']));
				$reason = '团购系统退款。';
			}

			$url = app_url('order/userorder/orderlist', array('status' => 7));
			$money = $money ? '￥' . $money : '￥' . $order['price'];
			$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('openid'));
			$openid = $member['openid'];

			if ($order['disorderid']) {
				Distribution::refunddis($order['disorderid']);
			}

			Message::refundNotice($openid, $reason, $money, $url);

			if ($order['specid']) {
				pdo_fetch('update' . tablename('wlmerchant_goods_option') . ('SET stock=stock+' . $refundnum . ' WHERE id = ' . $order['specid']));
			}
			else {
				$levelnum = pdo_getcolumn('wlmerchant_groupon_activity', array('id' => $order['fkid']), 'levelnum');
				$levelnum = intval($levelnum + $refundnum);
				pdo_update('wlmerchant_groupon_activity', array('levelnum' => $levelnum), array('id' => $order['activityid']));
			}
		}
		else {
			pdo_fetch('update' . tablename('wlmerchant_order') . ('SET failtimes = failtimes+1 WHERE id = ' . $id));
		}

		return $res;
	}

	static public function cancelorder($id)
	{
		global $_W;
		$order = pdo_get(PDO_NAME . 'order', array('id' => $id), array('id', 'num', 'fkid', 'specid'));
		$res = pdo_update(PDO_NAME . 'order', array('status' => 5), array('id' => $order['id']));

		if ($res) {
			if ($order['specid']) {
				$levelnum = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $order['specid']), 'stock');
				$nowstock = $levelnum + $order['num'];
				pdo_update('wlmerchant_goods_option', array('stock' => $nowstock), array('id' => $order['specid']));
			}
			else {
				$levelnum = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $order['fkid']), 'levelnum');
				$data2['levelnum'] = $levelnum + $order['num'];
				groupon::updateActive($data2, array('id' => $order['fkid']));
			}

			return true;
		}
	}

	static public function doTask()
	{
		global $_W;
		global $_GPC;
		$nowtime = time();
		$montime = time() - 30 * 24 * 3600;
		pdo_delete(PDO_NAME . 'order', array('createtime <' => $montime, 'status' => 5));
		$canceltime = time() - 30 * 60;
		$orderdata = pdo_fetchall('select id,num,fkid,specid from' . tablename(PDO_NAME . 'order') . ('where plugin = \'groupon\' and status = 0 and createtime < \'' . $canceltime . '\''));

		if (!empty($orderdata)) {
			foreach ($orderdata as $k => $v) {
				self::cancelorder($v['id']);
			}
		}

		$refundOrders = pdo_fetchall('select id,price,aid,uniacid from' . tablename(PDO_NAME . 'order') . 'where plugin = \'groupon\' and status = 6 and price > 0 and failtimes < 4 limit 0,10');

		if (!empty($refundOrders)) {
			foreach ($refundOrders as $k => $v) {
				$_W['aid'] = $v['aid'];
				$_W['uniacid'] = $v['uniacid'];
				self::refund($v['id']);
			}
		}

		$activitys1 = pdo_getall(PDO_NAME . 'groupon_activity', array('starttime <' => time(), 'status' => 1), array('id'));

		if (!empty($activitys1)) {
			foreach ($activitys1 as $k => $v) {
				pdo_update(PDO_NAME . 'groupon_activity', array('status' => 2), array('id' => $v['id']));
			}
		}

		$activitys2 = pdo_getall(PDO_NAME . 'groupon_activity', array('endtime <' => time(), 'status' => 2), array('id'));

		if (!empty($activitys2)) {
			foreach ($activitys2 as $k => $v) {
				pdo_update(PDO_NAME . 'groupon_activity', array('status' => 3), array('id' => $v['id']));
			}
		}

		$cutofforder = pdo_getall(PDO_NAME . 'order', array('status' => 1, 'plugin' => 'groupon'), array('id', 'fkid', 'cutoffnotice', 'mid', 'sid', 'estimatetime', 'uniacid', 'aid'));

		if (!empty($cutofforder)) {
			foreach ($cutofforder as $k => $v) {
				if (empty($v['cutoffnotice']) && $v['estimatetime']) {
					$_W['uniacid'] = $v['uniacid'];
					$_W['aid'] = $v['aid'];
					$config = Setting::agentsetting_read('groupon');
					$cutoff_time = $config['cutoff_time'] ? intval($config['cutoff_time']) : 7;
					if (time() < $v['estimatetime'] && $v['estimatetime'] < time() + $cutoff_time * 24 * 3600) {
						self::cutoffFollow($v['fkid'], $v['mid'], $v['id'], $v['sid']);
						pdo_update(PDO_NAME . 'order', array('cutoffnotice' => 1), array('id' => $v['id']));
					}
				}
			}
		}

		$noestorder = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_order') . 'WHERE plugin = \'groupon\' AND status = 1 AND estimatetime = 0 ORDER BY id DESC limit 20');

		if ($noestorder) {
			foreach ($noestorder as $key => $noest) {
				$activity = pdo_get('wlmerchant_groupon_activity', array('id' => $noest['activityid']), array('cutoffstatus', 'cutofftime', 'cutoffday'));

				if ($activity['cutoffstatus']) {
					$estimatetime = $noest['paytime'] + $activity['cutoffday'] * 86400;
				}
				else {
					$estimatetime = $activity['cutofftime'];
				}

				pdo_update('wlmerchant_order', array('estimatetime' => $estimatetime), array('id' => $noest['id']));
			}
		}

		$actorder3 = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_order') . ('WHERE plugin = \'groupon\' AND status = 1 AND estimatetime < ' . $nowtime . ' AND estimatetime > 0 ORDER BY id DESC'));

		if (!empty($actorder3)) {
			foreach ($actorder3 as $key => $actor3) {
				pdo_update('wlmerchant_order', array('status' => 9, 'overtime' => time()), array('id' => $actor3['id']));
			}
		}

		$overorders = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_order') . 'WHERE status = 9 AND issettlement = 0 AND plugin = \'groupon\' ORDER BY id DESC LIMIT 10');

		if ($overorders) {
			foreach ($overorders as $key => $over) {
				$_W['uniacid'] = $over['uniacid'];
				$_W['aid'] = $over['aid'];
				$base = Setting::wlsetting_read('distribution');
				$orderset = Setting::wlsetting_read('orderset');
				$goods = pdo_get('wlmerchant_groupon_activity', array('id' => $over['fkid']), array('overrefund'));

				if ($over['neworderflag']) {
					if ($orderset['reovertime'] && $goods['overrefund']) {
						self::refund($over['id'], 0, 0);
					}
				}
				else {
					$goods = pdo_get('wlmerchant_groupon_activity', array('id' => $over['fkid']), array('overrefund'));
					$record = pdo_get('wlmerchant_groupon_userecord', array('id' => $over['recordid']), array('usedtime'));
					if ($orderset['reovertime'] && $goods['overrefund'] && empty($record['usedtime'])) {
						self::refund($over['id'], 0, 0);
					}
					else {
						self::settover($over, $base['overstatus']);
					}
				}
			}
		}
	}

	static public function settover($over, $overstatus)
	{
		global $_W;
		$flag = pdo_get(PDO_NAME . 'autosettlement_record', array('orderno' => $over['orderno']), array('id'));
		$orderset = Setting::wlsetting_read('orderset');

		if (empty($flag)) {
			$record = pdo_get('wlmerchant_groupon_userecord', array('id' => $over['recordid']));

			if ($orderset['overmoney'] == 2) {
				$settnum = $over['num'];
			}
			else {
				$settnum = intval($over['num'] - $record['usetimes']);
			}

			if ($over['disorderid']) {
				if ($overstatus) {
					$dissettnum = $over['num'];
				}
				else {
					$dissettnum = $settnum;
				}

				if ($dissettnum) {
					$distrimoney = 0;
					$disorder = pdo_get('wlmerchant_disorder', array('id' => $over['disorderid']), array('leadmoney'));
					$leadmoneys = unserialize($disorder['leadmoney']);

					foreach ($leadmoneys as $key => &$money) {
						$money = sprintf('%.2f', $money * $dissettnum / $over['num']);
						$distrimoney += $money;
					}

					$newleadmoneys = serialize($leadmoneys);
					pdo_update('wlmerchant_disorder', array('status' => 1, 'leadmoney' => $newleadmoneys), array('id' => $over['disorderid'], 'status' => 0));
				}
			}

			if (empty($distrimoney)) {
				$distrimoney = 0;
			}

			if ($settnum) {
				$price = round($over['price'] * $settnum / $over['num'], 2);
				$settlementmoney = round($over['settlementmoney'] * $settnum / $over['num'], 2);
				$agentmoney = round($price - $settlementmoney - $distrimoney, 2);
			}
			else {
				$agentmoney = 0;
				$settlementmoney = 0;
			}

			if ($orderset['overmoney'] == 1) {
				$agentmoney = round($over['price'] - $settlementmoney - $distrimoney, 2);
			}

			$data = array('uniacid' => $_W['uniacid'], 'aid' => $over['aid'], 'type' => 10, 'merchantid' => $over['sid'], 'orderid' => $over['id'], 'orderno' => $over['orderno'], 'goodsid' => $over['fkid'], 'orderprice' => $over['price'], 'agentmoney' => $agentmoney, 'merchantmoney' => $settlementmoney, 'distrimoney' => $distrimoney, 'createtime' => time(), 'specialstatus' => 1);
			$res = pdo_insert(PDO_NAME . 'autosettlement_record', $data);
			$settlementid = pdo_insertid();

			if ($res) {
				if ($settlementmoney) {
					pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET allmoney=allmoney+' . $settlementmoney . ',nowmoney=nowmoney+' . $settlementmoney . ' WHERE id = ' . $data['merchantid']));
					$change['merchantnowmoney'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $data['merchantid']), 'nowmoney');
					Store::addcurrent(1, 10, $over['sid'], $settlementmoney, $change['merchantnowmoney'], $over['id']);
				}

				if ($data['agentmoney']) {
					pdo_fetch('update' . tablename('wlmerchant_agentusers') . ('SET allmoney=allmoney+' . $data['agentmoney'] . ',nowmoney=nowmoney+' . $data['agentmoney'] . ' WHERE id = ' . $data['aid']));
					$change['agentnowmoney'] = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $data['aid']), 'nowmoney');
					Store::addcurrent(2, 10, $over['aid'], $data['agentmoney'], $change['agentnowmoney'], $over['id']);
				}

				pdo_update('wlmerchant_autosettlement_record', $change, array('id' => $settlementid));
				pdo_update('wlmerchant_order', array('issettlement' => 1), array('id' => $over['id']));
			}
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
