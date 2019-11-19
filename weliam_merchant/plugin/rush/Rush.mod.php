<?php
//dezend by http://www.sucaihuo.com/
class Rush
{
	/**
     * 保存抢购商品在仓库中
     *
     * @access static
     * @name saveRushGoodsHouse
     * @param mixed  参数一的说明
     * @return array
     */
	static public function saveGoodsHouse($goods, $param = array())
	{
		global $_W;

		if (!is_array($goods)) {
			return false;
		}

		$goods['uniacid'] = $_W['uniacid'];
		$goods['aid'] = $_W['aid'];
		$goods['sid'] = $_W['sid'] ? $_W['sid'] : $goods['sid'];

		if (empty($param)) {
			pdo_insert(PDO_NAME . 'goodshouse', $goods);
			return pdo_insertid();
		}

		return false;
	}

	/**
     * 获取单条仓库商品数据
     *
     * @access static
     * @name getSingleGoods
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getSingleGoods($id, $select, $where = array())
	{
		$where['id'] = $id;
		$goodsInfo = Cache::getDateByCacheFirst('goods', $id, array('Util', 'getSingelData'), array($select, PDO_NAME . 'goodshouse', $where));

		if (empty($goodsInfo)) {
			return array();
		}

		return self::initSingleGoods($goodsInfo);
	}

	/**
     * 获取多条仓库商品数据
     *
     * @access static
     * @name getNumGoods
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getNumGoods($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'goodshouse', $where, $order, $pindex, $psize, $ifpage);

		foreach ($goodsInfo[0] as $k => $v) {
			$newGoodInfo[$k] = self::initSingleGoods($v);
		}

		$newGoodInfo = $newGoodInfo ? $newGoodInfo : array();
		return array($newGoodInfo, $goodsInfo[1], $goodsInfo[2]) ? array($newGoodInfo, $goodsInfo[1], $goodsInfo[2]) : array();
	}

	/**
     * 获取全部商品数据By缓存
     *
     * @access static
     * @name getNumGoods
     * @param $where   查询条件
     * @param $select  查询参数
     * @return array
     */
	static public function getAllGoods($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Cache::getDataByCacheFirst('goods', 'allGoods', array('Util', 'getNumData'), array($select, PDO_NAME . 'goodshouse', $where, $order, $pindex, $psize, $ifpage));

		foreach ($goodsInfo[0] as $k => $v) {
			$newGoodInfo[$k] = self::initSingleGoods($v);
		}

		return array($newGoodInfo, $goodsInfo[1], $goodsInfo[2]);
	}

	/**
     * 更新商品
     *
     * @access static
     * @name updateGoods
     * @param $params  修改参数
     * @param $where   修改条件
     * @return array
     */
	static public function updateGoods($params, $where)
	{
		$res = pdo_update(PDO_NAME . 'goodshouse', $params, $where);

		if ($where['id']) {
			Cache::deleteCache('goods', $where['id']);
		}

		if ($res) {
			return 1;
		}

		return 0;
	}

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
		$goodsInfo['plugin'] = 'rush';
		$goodsInfo['a'] = app_url('rush/home/detail', array('id' => $goodsInfo['id']));
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
     * 保存抢购活动
     *
     * @access static
     * @name saveRushActive
     * @param mixed  参数一的说明
     * @return array
     */
	static public function saveRushActive($active, $param = array())
	{
		global $_W;

		if (!is_array($active)) {
			return false;
		}

		$active['uniacid'] = $_W['uniacid'];
		$active['aid'] = $_W['aid'];
		$active['sid'] = $_W['sid'] ? $_W['sid'] : $active['sid'];

		if (empty($param)) {
			pdo_insert(PDO_NAME . 'rush_activity', $active);
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
		$activeInfo = Util::getNumData($select, PDO_NAME . 'rush_activity', $where, $order, $pindex, $psize, $ifpage);
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
		$goodsInfo = Util::getSingelData($select, PDO_NAME . 'rush_activity', $where);

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
		$res = pdo_update(PDO_NAME . 'rush_activity', $params, $where);

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
		$res = pdo_delete(PDO_NAME . 'rush_activity', $where);

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
		$data = Util::getSingelData($select, PDO_NAME . 'rush_order', $where);
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
		$orderInfo = Util::getNumData($select, PDO_NAME . 'rush_order', $where, $order, $pindex, $psize, $ifpage);
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
		$active = self::getSingleActive($orderInfo['activityid'], '*');
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
		$orderInfo['plugin'] = 'rush';
		$orderInfo['goodsprice'] = sprintf('%.2f', $orderInfo['price'] / $orderInfo['num']);

		if ($orderInfo['optionid']) {
			$orderInfo['optiontitle'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $orderInfo['optionid']), 'title');
		}

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
		$res = pdo_update(PDO_NAME . 'rush_order', $params, $where);

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
		$res = pdo_delete(PDO_NAME . 'rush_order', $where);

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
	static public function payRushOrderNotify($params)
	{
		global $_W;
		Util::wl_log('rush_notify', PATH_DATA . 'rush/data/', $params);
		$order_out = pdo_fetch('select * from' . tablename(PDO_NAME . 'rush_order') . ('where orderno=\'' . $params['tid'] . '\''));
		$_W['uniacid'] = $order_out['uniacid'];
		$activeInfo = self::getSingleActive($order_out['activityid'], '*');
		$data = self::getRushOrderPayData($params, $order_out, $activeInfo);

		if ($order_out) {
			pdo_update(PDO_NAME . 'rush_order', $data, array('orderno' => $params['tid']));
			$orderid = $order_out['id'];

			if ($order_out['optionid']) {
				$levelnum = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $order_out['optionid']), 'stock');
			}
			else {
				$total = pdo_fetchcolumn('SELECT num FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE id = ' . $order_out['activityid']));
				$salesVolume = pdo_fetchcolumn('SELECT sum(num) FROM ' . tablename(PDO_NAME . 'rush_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND status IN (0,1,2,3,6,9,4,8) activityid = ' . $order_out['activityid']));
				$levelnum = $total - $salesVolume;
			}

			if ($levelnum < 0) {
				$data['status'] = 6;
				pdo_update(PDO_NAME . 'rush_order', $data, array('orderno' => $params['tid']));
			}
			else {
				if ($order_out['neworderflag']) {
					Order::createSmallorder($order_out['id'], 1);
				}

				if (p('distribution') && empty($activeInfo['isdistri'])) {
					$_W['aid'] = $order_out['aid'];

					if ($order_out['optionid']) {
						$option = pdo_get('wlmerchant_goods_option', array('id' => $order_out['optionid']), array('onedismoney', 'twodismoney', 'threedismoney'));
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

					$disorderid = Distribution::disCore($order_out['mid'], $order_out['price'], $onemoney, $twomoney, $threemoney, $order_out['id'], 'rush', $activeInfo['dissettime']);
					$data['disorderid'] = $disorderid;
				}

				if (p('paidpromotion')) {
					$data['paidprid'] = Paidpromotion::getpaidpr(1, $order_out['id'], $data['paytype']);
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
					Userlabel::addlabel($order_out['mid'], $order_out['activityid'], 'rush');
				}

				if ($order_out['expressid']) {
					$data['status'] = 8;
				}

				pdo_update(PDO_NAME . 'rush_order', $data, array('orderno' => $params['tid']));

				if ($activeInfo['integral']) {
					$remark = '抢购:[' . $activeInfo['name'] . ']赠送积分';
					Member::credit_update_credit1($order_out['mid'], $activeInfo['integral'] * $order_out['num'], $remark);
				}

				$url = app_url('order/userorder/orderlist', array('status' => 'all'));
				Store::addFans($order_out['sid'], $order_out['mid']);
				Message::paySuccess($order_out['id'], 'rush');
			}
		}
		else {
			if ($data['status']) {
				$data['status'] = 6;
				pdo_update(PDO_NAME . 'rush_order', $data, array('orderno' => $params['tid']));
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
	static public function payRushOrderReturn($params, $backurl = false)
	{
		Util::wl_log('payResult_return', PATH_PLUGIN . 'rush/data/', $params);
		$order_out = pdo_get(PDO_NAME . 'rush_order', array('orderno' => $params['tid']), array('id'));
		wl_message('支付成功', app_url('order/userorder/payover', array('id' => $order_out['id'], 'type' => 1)), 'success');
	}

	/**
     * 函数的含义说明
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function getRushOrderPayData($params, $order_out, $goodsInfo)
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
		SingleMerchant::updateAmount($fee, $order_out['sid'], $order_out['id'], 1, '订单支付成功');
		return $data;
	}

	static public function cancelorder($id)
	{
		global $_W;
		$res = pdo_update(PDO_NAME . 'rush_order', array('status' => 5), array('id' => $id));

		if ($res) {
			$order = pdo_get('wlmerchant_rush_order', array('id' => $id), array('num', 'activityid', 'optionid', 'dkcredit', 'mid'));

			if ($order['optionid']) {
				$levelnum = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $order['optionid']), 'stock');
				$nowstock = $levelnum + $order['num'];
				pdo_update('wlmerchant_goods_option', array('stock' => $nowstock), array('id' => $order['optionid']));
			}
			else {
				$stock = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $order['activityid']), 'num');
				$alreadyBuyNum = pdo_fetch('SELECT SUM(num) FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status IN (0,1,2,3,6,9) AND activityid = ' . $order['activityid']));
				$alreadyBuyNum = array_values($alreadyBuyNum);
				$buynum = $alreadyBuyNum[0];
				$data2['levelnum'] = $stock - $buynum;
				Rush::updateActive($data2, array('id' => $order['activityid']));
			}

			if ($order['dkcredit']) {
				$goodname = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $order['activityid']), 'name');
				Member::credit_update_credit1($order['mid'], $order['dkcredit'], '取消抢购商品:[' . $goodname . ']订单返还积分');
			}

			return true;
		}
	}

	static public function hexiaoorder($id, $mid, $num = 1, $type = 1, $checkcode = '')
	{
		global $_W;
		$order = pdo_get('wlmerchant_rush_order', array('id' => $id));

		if ($order['neworderflag']) {
			$order['usetimes'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'rush\' AND  orderid = ' . $id . ' AND status = 1'));
		}

		if ($order['usetimes'] < $num) {
			if (is_mobile()) {
				exit(json_encode(array('result' => 2, 'message' => '使用次数不足，无法核销')));
			}
			else {
				show_json(0, '使用次数不足，无法核销');
			}
		}

		$cutofftime = $order['estimatetime'];
		if ($cutofftime < time() && $type != 3) {
			if (is_mobile()) {
				exit(json_encode(array('result' => 2, 'message' => '已超过截止日期，无法核销')));
			}
			else {
				show_json(0, '已超过截止日期，无法核销');
			}
		}

		if ($order['status'] != 1 && $type != 3) {
			if (is_mobile()) {
				exit(json_encode(array('errno' => 1, 'message' => '订单已核销')));
			}
			else {
				show_json(0, '订单已核销');
			}
		}

		if ($order['neworderflag']) {
			if ($checkcode) {
				$smallorders = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_smallorder') . ('WHERE plugin = \'rush\' AND  orderid = ' . $id . ' AND status = 1 AND checkcode = ' . $checkcode));
			}
			else {
				$smallorders = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_smallorder') . ('WHERE plugin = \'rush\' AND  orderid = ' . $id . ' AND status = 1 ORDER BY id ASC LIMIT ' . $num));
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

			if ($order['usedtime']) {
				$a = unserialize($order['usedtime']);
				$i = 0;

				while ($i < $num) {
					$arr['time'] = time();
					$arr['type'] = $type;
					$arr['ver'] = $mid;
					$a[] = $arr;
					++$i;
				}

				$order['usedtime'] = serialize($a);
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

				$order['usedtime'] = serialize($a);
			}

			$params['usetimes'] = $order['usetimes'] - $num;
			$params['usedtime'] = $order['usedtime'];

			if ($params['usetimes'] < 1) {
				$params['status'] = 2;
				$rushtask = array('type' => 'rush', 'orderid' => $id);
				$rushtask = serialize($rushtask);
				Queue::addTask(1, $rushtask, time(), $id);

				if (!empty($order['disorderid'])) {
					$res = pdo_update('wlmerchant_disorder', array('status' => 1), array('id' => $order['disorderid'], 'status' => 0));

					if ($res) {
						$distask = array('type' => 'rush', 'orderid' => $order['disorderid']);
						$distask = serialize($distask);
						Queue::addTask(3, $distask, time(), $order['disorderid']);
					}
				}
			}

			$where['id'] = $id;
			$res = Rush::updateOrder($params, $where);
		}

		if ($res) {
			$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('openid'));
			$active = pdo_get('wlmerchant_rush_activity', array('id' => $order['activityid']), array('name'));
			Message::hexiaoSuccess($member['openid'], $num, $active['name'], '');

			if ($type == 2) {
				Message::hexiaoTover($_W['wlmember']['openid'], $num, $active['name']);
			}

			SingleMerchant::verifRecordAdd($order['aid'], $order['sid'], $order['mid'], 'rush', $order['id'], $order['checkcode'], $active['name'], $type, $num);
			return 1;
		}

		return 0;
	}

	static public function refund($id, $money = '', $unline = '')
	{
		global $_W;
		$item = Rush::getSingleOrder($id, '*');
		if (empty($money) && $item['neworderflag']) {
			$money = pdo_fetchcolumn('SELECT SUM(orderprice) FROM ' . tablename(PDO_NAME . 'smallorder') . (' WHERE plugin = \'rush\' AND orderid = ' . $id . ' AND status = 1'));
			$refundnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename(PDO_NAME . 'smallorder') . (' WHERE plugin = \'rush\' AND orderid = ' . $id . ' AND status = 1'));
		}
		else {
			$refundnum = $item['usetimes'];
		}

		if ($unline) {
			$res['status'] = 1;
		}
		else {
			$res = wlPay::refundMoney($id, $money, '抢购订单退款', 'rush', 2);
		}

		if ($res['status']) {
			if ($item['neworderflag']) {
				pdo_update('wlmerchant_smallorder', array('status' => 3, 'refundtime' => time()), array('plugin' => 'rush', 'orderid' => $id, 'status' => 1));

				if ($item['applyrefund']) {
					$reason = '买家申请退款。';
					$orderdata['applyrefund'] = 2;
				}
				else {
					$reason = '抢购系统退款。';
				}

				$hexiao = pdo_get('wlmerchant_smallorder', array('orderid' => $id, 'plugin' => 'rush', 'status' => 2), array('id'));

				if ($hexiao) {
					$orderdata['status'] = 2;
					$orderdata['issettlement'] = 1;
				}
				else {
					$orderdata['status'] = 7;
					$orderdata['refundtime'] = time();
				}

				pdo_update('wlmerchant_rush_order', $orderdata, array('id' => $item['id']));
			}
			else if ($item['applyrefund']) {
				pdo_update('wlmerchant_rush_order', array('status' => 7, 'refundtime' => time(), 'applyrefund' => 2), array('id' => $item['id']));
				$reason = '买家申请退款。';
			}
			else {
				pdo_update('wlmerchant_rush_order', array('status' => 7, 'refundtime' => time()), array('id' => $item['id']));
				$reason = '抢购系统退款。';
			}

			$url = app_url('rush/home/index');
			$money = $money ? '￥' . $money : '￥' . $item['price'];
			$member = pdo_get('wlmerchant_member', array('id' => $item['mid']), array('openid'));
			$openid = $member['openid'];

			if ($item['disorderid']) {
				Distribution::refunddis($item['disorderid']);
			}

			Message::refundNotice($openid, $reason, $money, $url);

			if ($item['optionid']) {
				$levelnum = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $item['optionid']), 'stock');
				$nowstock = $levelnum + $refundnum;
				pdo_update('wlmerchant_goods_option', array('stock' => $nowstock), array('id' => $item['optionid']));
			}
			else {
				$stock = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $item['activityid']), 'num');
				$alreadyBuyNum = pdo_fetch('SELECT SUM(num) FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status IN (0,1,2,3,6,9) AND activityid = ' . $item['activityid']));
				$alreadyBuyNum = array_values($alreadyBuyNum);
				$buynum = $alreadyBuyNum[0];
				$data2['levelnum'] = $stock - $buynum;
				Rush::updateActive($data2, array('id' => $item['activityid']));
			}

			if ($item['dkcredit']) {
				$refundcredit = sprintf('%.2f', $item['dkcredit'] / $item['num'] * $refundnum);
				$goodname = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $item['activityid']), 'name');
				Member::credit_update_credit1($item['mid'], $refundcredit, '退款抢购商品:[' . $goodname . ']订单返还积分');
			}
		}
		else {
			pdo_fetch('update' . tablename('wlmerchant_rush_order') . ('SET failtimes = failtimes+1 WHERE id = ' . $id));
		}

		return $res;
	}

	static public function settover($over, $overstatus)
	{
		global $_W;
		$flag = pdo_get(PDO_NAME . 'autosettlement_record', array('orderno' => $over['orderno']), array('id'));
		$orderset = Setting::wlsetting_read('orderset');

		if (empty($flag)) {
			if ($orderset['overmoney'] == 2) {
				$settnum = $over['num'];
			}
			else {
				$settnum = intval($over['num'] - $over['usetimes']);
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
				$price = round($over['actualprice'] * $settnum / $over['num'], 2);
				$settlementmoney = round($over['settlementmoney'] * $settnum / $over['num'], 2);
				$agentmoney = round($price - $settlementmoney - $distrimoney, 2);
			}
			else {
				$agentmoney = 0;
				$settlementmoney = 0;
			}

			if ($orderset['overmoney'] == 1) {
				$agentmoney = round($over['actualprice'] - $settlementmoney - $distrimoney, 2);
			}

			$data = array('uniacid' => $_W['uniacid'], 'aid' => $over['aid'], 'type' => 1, 'merchantid' => $over['sid'], 'orderid' => $over['id'], 'orderno' => $over['orderno'], 'goodsid' => $over['activityid'], 'orderprice' => $over['price'], 'agentmoney' => $agentmoney, 'merchantmoney' => $settlementmoney, 'distrimoney' => $distrimoney, 'createtime' => time(), 'specialstatus' => 1);
			$res = pdo_insert(PDO_NAME . 'autosettlement_record', $data);
			$settlementid = pdo_insertid();

			if ($res) {
				if ($settlementmoney) {
					pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET allmoney=allmoney+' . $settlementmoney . ',nowmoney=nowmoney+' . $settlementmoney . ' WHERE id = ' . $data['merchantid']));
					$change['merchantnowmoney'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $data['merchantid']), 'nowmoney');
					Store::addcurrent(1, 1, $over['sid'], $settlementmoney, $change['merchantnowmoney'], $over['id']);
				}

				if ($data['agentmoney']) {
					pdo_fetch('update' . tablename('wlmerchant_agentusers') . ('SET allmoney=allmoney+' . $data['agentmoney'] . ',nowmoney=nowmoney+' . $data['agentmoney'] . ' WHERE id = ' . $data['aid']));
					$change['agentnowmoney'] = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $data['aid']), 'nowmoney');
					Store::addcurrent(2, 1, $over['aid'], $data['agentmoney'], $change['agentnowmoney'], $over['id']);
				}

				pdo_update('wlmerchant_autosettlement_record', $change, array('id' => $settlementid));
				pdo_update('wlmerchant_rush_order', array('issettlement' => 1), array('id' => $over['id']));
			}
		}
	}

	static public function doTask()
	{
		global $_W;
		global $_GPC;
		$montime = time() - 30 * 24 * 3600;
		pdo_delete(PDO_NAME . 'rush_order', array('createtime <' => $montime, 'status' => 5));
		$canceltime = time() - 30 * 60;
		$orderdata = pdo_fetchall('select id,num,activityid,optionid,activityid from' . tablename(PDO_NAME . 'rush_order') . ('where status = 0 and createtime < \'' . $canceltime . '\''));

		if (!empty($orderdata)) {
			foreach ($orderdata as $k => $v) {
				self::cancelorder($v['id']);
			}
		}

		$refundOrders = pdo_fetchall('select id,price,activityid,optionid,num,activityid,dkcredit,mid,aid,uniacid from' . tablename(PDO_NAME . 'rush_order') . 'where status = 6 and actualprice > 0 and failtimes < 4 limit 0,10');

		if (!empty($refundOrders)) {
			foreach ($refundOrders as $k => $v) {
				$_W['aid'] = $v['aid'];
				$_W['uniacid'] = $v['uniacid'];
				self::refund($v['id']);
			}
		}

		$activitys1 = pdo_getall(PDO_NAME . 'rush_activity', array('starttime <' => time(), 'status' => 1), array('id'));

		if (!empty($activitys1)) {
			foreach ($activitys1 as $k => $v) {
				pdo_update(PDO_NAME . 'rush_activity', array('status' => 2), array('id' => $v['id']));
			}
		}

		$activitys2 = pdo_getall(PDO_NAME . 'rush_activity', array('endtime <' => time(), 'status' => 2), array('id'));

		if (!empty($activitys2)) {
			foreach ($activitys2 as $k => $v) {
				pdo_update(PDO_NAME . 'rush_activity', array('status' => 3), array('id' => $v['id']));
			}
		}

		$follows = pdo_getall(PDO_NAME . 'rush_follows', array('sendtime <=' => time()), array('actid', 'mid', 'id'), '', 'id', array(1, 50));

		if (!empty($follows)) {
			foreach ($follows as $k => $v) {
				$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $v['mid']), 'openid');
				$goodsname = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $v['actid']), 'name');
				$first = '您好,您关注的抢购即将开始';
				$keyword1 = '[' . $goodsname . ']抢购开始通知';
				$keyword2 = '已通知,活动即将开始';
				$remark = '点击立即参加抢购活动，赶快行动吧。';
				$url = app_url('rush/home/detail', array('id' => $v['actid']));
				Message::jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url);
				pdo_delete(PDO_NAME . 'rush_follows', array('id' => $v['id']));
			}
		}

		$cutofforder = pdo_getall(PDO_NAME . 'rush_order', array('status' => 1, 'cutoffnotice' => 0, 'estimatetime <' => time() + 7 * 24 * 3600), array('id', 'activityid', 'cutoffnotice', 'mid', 'sid', 'estimatetime', 'uniacid'));

		if (!empty($cutofforder)) {
			foreach ($cutofforder as $k => $v) {
				$_W['uniacid'] = $v['uniacid'];
				Message::cutoffFollow($v['activityid'], $v['mid'], $v['id'], $v['sid']);
				pdo_update(PDO_NAME . 'rush_order', array('cutoffnotice' => 1), array('id' => $v['id']));
			}
		}

		$noestorder = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_rush_order') . 'WHERE status = 1 AND estimatetime = 0 ORDER BY id DESC limit 20');

		if ($noestorder) {
			foreach ($noestorder as $key => $noest) {
				$activity = pdo_get('wlmerchant_rush_activity', array('id' => $noest['activityid']), array('cutoffstatus', 'cutofftime', 'cutoffday'));

				if ($activity['cutoffstatus']) {
					$estimatetime = $noest['paytime'] + $activity['cutoffday'] * 86400;
				}
				else {
					$estimatetime = $activity['cutofftime'];
				}

				pdo_update('wlmerchant_rush_order', array('estimatetime' => $estimatetime), array('id' => $noest['id']));
			}
		}

		$nowtime = time();
		$actorder3 = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_rush_order') . ('WHERE status = 1 AND estimatetime < ' . $nowtime . ' AND estimatetime > 0 ORDER BY id DESC'));

		if (!empty($actorder3)) {
			foreach ($actorder3 as $key => $actor3) {
				pdo_update('wlmerchant_rush_order', array('status' => 9, 'overtime' => time()), array('id' => $actor3['id']));
			}
		}

		$overorders = pdo_fetchall('SELECT id,num,usetimes,neworderflag,actualprice,settlementmoney,usedtime,disorderid,orderno,activityid,orderno,sid,price,aid,uniacid FROM ' . tablename('wlmerchant_rush_order') . 'WHERE status = 9 AND issettlement = 0 ORDER BY id DESC LIMIT 10');

		if ($overorders) {
			foreach ($overorders as $key => $over) {
				$_W['uniacid'] = $over['uniacid'];
				$_W['aid'] = $over['aid'];
				$base = Setting::wlsetting_read('distribution');
				$orderset = Setting::wlsetting_read('orderset');
				$goods = pdo_get('wlmerchant_rush_activity', array('id' => $over['activityid']), array('overrefund'));

				if ($over['neworderflag']) {
					if ($orderset['reovertime'] && $goods['overrefund']) {
						self::refund($over['id'], 0, 0);
					}
				}
				else {
					if ($orderset['reovertime'] && $goods['overrefund'] && empty($over['usedtime'])) {
						self::refund($over['id'], 0, 0);
					}
					else {
						self::settover($over, $base['overstatus']);
					}
				}
			}
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
