<?php
//dezend by http://www.sucaihuo.com/
class Wlfightgroup
{
	static public function saveCategory($category)
	{
		global $_W;

		if (!is_array($category)) {
			return false;
		}

		$category['uniacid'] = $_W['uniacid'];
		$category['aid'] = $_W['aid'];
		pdo_insert(PDO_NAME . 'fightgroup_category', $category);
		return pdo_insertid();
	}

	static public function getNumCategory($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'fightgroup_category', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	static public function getCategory($id)
	{
		$res = pdo_get('wlmerchant_fightgroup_category', array('id' => $id));
		return $res;
	}

	static public function updateCategory($category, $id)
	{
		global $_W;

		if (!is_array($category)) {
			return false;
		}

		$res = pdo_update('wlmerchant_fightgroup_category', $category, array('id' => $id));
		return $res;
	}

	static public function deteleCategory($id)
	{
		$res = pdo_delete('wlmerchant_fightgroup_category', array('id' => $id));
		return $res;
	}

	static public function getHouseGoods($id, $select, $where = array())
	{
		$where['id'] = $id;
		$goodsInfo = Util::getSingelData($select, PDO_NAME . 'goodshouse', $where);
		return $goodsInfo;
	}

	static public function getNumMerchant($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$merchantInfo = Util::getNumData($select, PDO_NAME . 'merchantdata', $where, $order, $pindex, $psize, $ifpage);
		return $merchantInfo;
	}

	static public function getSingleMerchant($id, $select, $where = array())
	{
		$where['id'] = $id;
		return Util::getSingelData($select, PDO_NAME . 'merchantdata', $where);
	}

	static public function getSingleGood($id, $select, $where = array())
	{
		$where['id'] = $id;
		$goodsInfo = Util::getSingelData($select, PDO_NAME . 'fightgroup_goods', $where);
		return $goodsInfo;
	}

	static public function saveGoods($goods)
	{
		global $_W;

		if (!is_array($goods)) {
			return false;
		}

		$goods['uniacid'] = $_W['uniacid'];
		$goods['aid'] = $_W['aid'];
		pdo_insert(PDO_NAME . 'fightgroup_goods', $goods);
		return pdo_insertid();
	}

	static public function updateGoods($goods, $id)
	{
		global $_W;

		if (!is_array($goods)) {
			return false;
		}

		$res = pdo_update('wlmerchant_fightgroup_goods', $goods, array('id' => $id));
		return $res;
	}

	static public function getNumGoods($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'fightgroup_goods', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	static public function deteleGoods($id)
	{
		$res = pdo_delete('wlmerchant_fightgroup_goods', array('id' => $id));
		return $res;
	}

	static public function recoveryGoods($id)
	{
		$res = pdo_update('wlmerchant_fightgroup_goods', array('status' => 0), array('id' => $id));
		return $res;
	}

	static public function getNumOrder($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'order', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	static public function getSingleOrder($id, $select, $where = array())
	{
		$where['id'] = $id;
		return Util::getSingelData($select, PDO_NAME . 'order', $where);
	}

	static public function getNumGroup($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'fightgroup_group', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	static public function getSingleGroup($id, $select, $where = array())
	{
		$where['id'] = $id;
		return Util::getSingelData($select, PDO_NAME . 'fightgroup_group', $where);
	}

	static public function saveFightOrder($data, $param = array())
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

	static public function saveFightGroup($data, $param = array())
	{
		global $_W;

		if (!is_array($data)) {
			return false;
		}

		if (empty($param)) {
			$data['uniacid'] = $_W['uniacid'];
			pdo_insert(PDO_NAME . 'fightgroup_group', $data);
			return pdo_insertid();
		}

		return false;
	}

	static public function saveExpress($data)
	{
		global $_W;

		if (!is_array($data)) {
			return false;
		}

		$data['uniacid'] = $_W['uniacid'];
		$data['aid'] = $_W['aid'];
		pdo_insert(PDO_NAME . 'express_template', $data);
		return pdo_insertid();
	}

	static public function updateExpress($data, $id)
	{
		global $_W;

		if (!is_array($data)) {
			return false;
		}

		$res = pdo_update('wlmerchant_express_template', $data, array('id' => $id));
		return $res;
	}

	static public function getNumExpress($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'express_template', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	static public function deteleExpress($id)
	{
		$res = pdo_delete('wlmerchant_express_template', array('id' => $id));
		return $res;
	}

	static public function createRecord($orderid, $num)
	{
		global $_W;
		$data['uniacid'] = $_W['uniacid'];
		$data['orderid'] = $orderid;
		$data['qrcode'] = Util::createConcode(5);
		$data['createtime'] = time();
		$data['usetimes'] = $num;
		pdo_insert(PDO_NAME . 'fightgroup_userecord', $data);
		return pdo_insertid();
	}

	static public function createfalsemember($imgs, $names)
	{
		global $_W;
		$success = $fail = 0;
		$member['uniacid'] = $_W['uniacid'];
		$member['aid'] = $_W['aid'];
		$len = count($imgs);
		$k = 0;

		while ($k < $len) {
			$member['avatar'] = $imgs[$k];
			$member['nickname'] = $names[$k];
			$member['createtime'] = time();
			$res = pdo_insert(PDO_NAME . 'fightgroup_falsemember', $member);

			if ($res) {
				++$success;
			}
			else {
				++$fail;
			}

			++$k;
		}

		$arr = array('success' => $success, 'fail' => $fail);
		return $arr;
	}

	static public function getNumfalsemember($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'fightgroup_falsemember', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	static public function getSingleFalsemember($id, $select, $where = array())
	{
		$where['id'] = $id;
		return Util::getSingelData($select, PDO_NAME . 'fightgroup_falsemember', $where);
	}

	static public function payFightshargeNotify($params)
	{
		global $_W;
		Util::wl_log('payResult_notify', PATH_PLUGIN . 'wlfightgroup/data/', $params);
		$order = pdo_get('wlmerchant_order', array('orderno' => $params['tid']));

		if ($order['status'] == 0) {
			if ($order['specid']) {
				$option = pdo_get('wlmerchant_goods_option', array('id' => $order['specid']), array('stock', 'onedismoney', 'twodismoney', 'threedismoney'));
			}

			$good = pdo_get('wlmerchant_fightgroup_goods', array('id' => $order['fkid']));
			$data1 = self::getFightshargePayData($params, $order);
			if (p('distribution') && empty($good['isdistri'])) {
				$_W['aid'] = $order['aid'];

				if ($order['expressid']) {
					$expressprice = pdo_getcolumn(PDO_NAME . 'express', array('id' => $order['expressid']), 'expressprice');
				}
				else {
					$expressprice = 0;
				}

				$dismoney = sprintf('%.2f', $order['price'] - $expressprice);

				if ($order['specid']) {
					$onemoney = sprintf('%.2f', $option['onedismoney'] * $order['num']);
					$twomoney = sprintf('%.2f', $option['twodismoney'] * $order['num']);
					$threemoney = sprintf('%.2f', $option['threedismoney'] * $order['num']);
				}
				else {
					$onemoney = sprintf('%.2f', $good['onedismoney'] * $order['num']);
					$twomoney = sprintf('%.2f', $good['twodismoney'] * $order['num']);
					$threemoney = sprintf('%.2f', $good['threedismoney'] * $order['num']);
				}

				$disorderid = Distribution::disCore($order['mid'], $dismoney, $onemoney, $twomoney, $threemoney, $order['id'], 'fightgroup', $good['dissettime']);
				$data1['disorderid'] = $disorderid;
			}

			if (p('paidpromotion')) {
				$data1['paidprid'] = Paidpromotion::getpaidpr(2, $order['id'], $data1['paytype']);
			}

			if (p('userlabel')) {
				$_W['aid'] = $order['aid'];
				Userlabel::addlabel($order['mid'], $order['fkid'], 'fightgroup');
			}

			if ($order['expressid']) {
				$data1['estimatetime'] = 2147483647;
			}
			else if ($good['cutoffstatus']) {
				$data1['estimatetime'] = time() + $good['cutoffday'] * 86400;
			}
			else {
				$data1['estimatetime'] = $good['cutofftime'];
			}

			pdo_update(PDO_NAME . 'order', $data1, array('id' => $order['id']));

			if ($order['fightstatus'] == 1) {
				if ($order['fightgroupid']) {
					$group = pdo_get('wlmerchant_fightgroup_group', array('id' => $order['fightgroupid']));

					if ($group['status'] == 1) {
						$newlack = $group['lacknum'] - 1;

						if (0 < $newlack) {
							$newdata['lacknum'] = $newlack;
						}
						else {
							$newdata['lacknum'] = $newlack;
							$newdata['status'] = 2;
							$newdata['successtime'] = time();
							$orders = pdo_getall('wlmerchant_order', array('fightgroupid' => $group['id'], 'uniacid' => $group['uniacid'], 'aid' => $group['aid'], 'status' => 1));

							foreach ($orders as $key => $or) {
								if ($or['expressid']) {
									$res = pdo_update(PDO_NAME . 'order', array('status' => 8), array('id' => $or['id']));
									$member = pdo_get('wlmerchant_member', array('id' => $or['mid']), array('openid'));
								}
								else {
									if ($order['neworderflag']) {
										Order::createSmallorder($or['id'], 3);
										pdo_update(PDO_NAME . 'order', array('status' => 1), array('id' => $or['id']));
									}
									else {
										$recordid = self::createRecord($or['id'], $or['num']);
										$res = pdo_update(PDO_NAME . 'order', array('status' => 1, 'recordid' => $recordid), array('id' => $or['id']));
									}

									$member = pdo_get('wlmerchant_member', array('id' => $or['mid']), array('openid'));
								}

								if ($res) {
									Message::groupresult($member['openid'], $or['fightgroupid'], 1);
								}
							}
						}

						pdo_update(PDO_NAME . 'fightgroup_group', $newdata, array('id' => $order['fightgroupid']));
					}
					else {
						$newgroupflag = 1;
					}
				}
				else {
					$newgroupflag = 1;
				}

				if ($newgroupflag) {
					$group = array('status' => 1, 'goodsid' => $order['fkid'], 'aid' => $good['aid'], 'sid' => $good['merchantid'], 'neednum' => $good['peoplenum'], 'lacknum' => $good['peoplenum'] - 1, 'starttime' => time(), 'failtime' => time() + $good['grouptime'] * 3600);
					$fightgroupid = self::saveFightGroup($group);
					pdo_update(PDO_NAME . 'order', array('fightgroupid' => $fightgroupid), array('id' => $order['id']));
				}
			}
			else if ($order['expressid']) {
				pdo_update(PDO_NAME . 'order', array('status' => 8), array('id' => $order['id']));
			}
			else if ($order['neworderflag']) {
				Order::createSmallorder($order['id'], 3);
				pdo_update(PDO_NAME . 'order', array('status' => 1), array('id' => $order['id']));
			}
			else {
				$recordid = self::createRecord($order['id'], $order['num']);
				pdo_update(PDO_NAME . 'order', array('status' => 1, 'recordid' => $recordid), array('id' => $order['id']));
			}

			if ($order['specid']) {
				$optionstock = $option['stock'] - $order['num'];
				pdo_update('wlmerchant_goods_option', array('stock' => $optionstock), array('id' => $order['specid']));
			}
			else {
				$data2['stock'] = $good['stock'] - $order['num'];
			}

			$data2['realsalenum'] = $good['realsalenum'] + $order['num'];
			$res2 = Wlfightgroup::updateGoods($data2, $order['fkid']);
			$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('openid', 'uid'));

			if ($good['markid']) {
				$mark = pdo_get('wlmerchant_marking', array('id' => $good['markid']));

				if ($mark['getcredit']) {
					$remark = '购买商品:[' . $good['name'] . ']赠送积分';
					Member::credit_update_credit1($order['mid'], $mark['getcredit'] * $order['num'], $remark);
				}
			}

			if ($order['card_id']) {
				$remark = '购买商品:[' . $good['name'] . ']抵扣积分';
				Member::credit_update_credit1($order['mid'], 0 - $order['card_id'], $remark);
			}

			$openid = $member['openid'];
			Store::addFans($order['sid'], $order['mid']);
			Message::paySuccess($order['id'], 'wlfightgroup');
		}
	}

	static public function getFightshargePayData($params, $order_out)
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
		SingleMerchant::updateAmount($fee, $order_out['sid'], $order_out['id'], 1, '拼团订单支付成功');
		return $data;
	}

	static public function payFightshargeReturn($params)
	{
		$res = $params['result'] == 'success' ? 1 : 0;
		$order = pdo_get('wlmerchant_order', array('orderno' => $params['tid']), array('id'));
		wl_message('支付成功', app_url('order/userorder/payover', array('id' => $order['id'], 'type' => 2)), 'success');
	}

	static public function hexiaoorder($id, $mid, $num = 1, $type = 1, $checkcode = '')
	{
		global $_W;
		$item = pdo_get('wlmerchant_order', array('id' => $id), array('estimatetime', 'recordid', 'mid', 'neworderflag', 'fkid', 'id', 'aid', 'sid', 'disorderid'));

		if ($item['neworderflag']) {
			$record['usetimes'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'wlfightgroup\' AND  orderid = ' . $id . ' AND status = 1'));
		}
		else {
			$record = pdo_get('wlmerchant_fightgroup_userecord', array('id' => $item['recordid']));
		}

		if ($item['estimatetime'] < time()) {
			if (is_mobile()) {
				exit(json_encode(array('result' => 2, 'msg' => '订单已过期，无法核销')));
			}
			else {
				show_json(0, '订单已过期，无法核销');
			}
		}

		if ($record['usetimes'] < $num) {
			if (is_mobile()) {
				exit(json_encode(array('result' => 2, 'msg' => '使用次数不足，无法核销')));
			}
			else {
				show_json(0, '使用次数不足，无法核销');
			}
		}

		if ($item['neworderflag']) {
			if ($checkcode) {
				$smallorders = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_smallorder') . ('WHERE plugin = \'wlfightgroup\' AND  orderid = ' . $id . ' AND status = 1 AND checkcode = ' . $checkcode));
			}
			else {
				$smallorders = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_smallorder') . ('WHERE plugin = \'wlfightgroup\' AND  orderid = ' . $id . ' AND status = 1 ORDER BY id ASC LIMIT ' . $num));
			}

			if ($smallorders) {
				if ($mid) {
					$uid = pdo_getcolumn(PDO_NAME . 'merchantuser', array('storeid' => $item['sid'], 'mid' => $mid), 'id');
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
				$record['usedtime'] = unserialize($record['usedtime']);
				$a = $record['usedtime'];
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

			$data['usetimes'] = $record['usetimes'] - $num;
			$data['usedtime'] = $record['usedtime'];
			$res = pdo_update('wlmerchant_fightgroup_userecord', $data, array('id' => $item['recordid']));
		}

		if ($res) {
			$member = pdo_get('wlmerchant_member', array('id' => $item['mid']), array('openid'));
			$goods = pdo_get('wlmerchant_fightgroup_goods', array('id' => $item['fkid']), array('name'));
			$url = app_url('wlfightgroup/fightapp/expressorder', array('id' => $item['id']));
			Message::hexiaoSuccess($member['openid'], $num, $goods['name'], $url);

			if ($type == 2) {
				Message::hexiaoTover($_W['wlmember']['openid'], $num, $goods['name']);
			}

			SingleMerchant::verifRecordAdd($item['aid'], $item['sid'], $item['mid'], 'wlfightgroup', $item['id'], $record['qrcode'], $goods['name'], $type, $num);
			if ($data['usetimes'] == 0 || $data['usetimes'] < 0) {
				pdo_update('wlmerchant_order', array('status' => 2), array('id' => $id));
				$ordertask = array('type' => 'wlfightgroup', 'orderid' => $id);
				$ordertask = serialize($ordertask);
				Queue::addTask(2, $ordertask, time(), $id);

				if ($item['disorderid']) {
					$res = pdo_update('wlmerchant_disorder', array('status' => 1), array('id' => $item['disorderid'], 'status' => 0));

					if ($res) {
						$distask = array('type' => 'wlfightgroup', 'orderid' => $item['disorderid']);
						$distask = serialize($distask);
						Queue::addTask(3, $distask, time(), $item['disorderid']);
					}
				}
			}

			return 1;
		}

		return 0;
	}

	static public function refund($id, $money, $unline = '')
	{
		global $_W;
		$order = pdo_get('wlmerchant_order', array('id' => $id));
		if (empty($money) && $order['neworderflag']) {
			$money = pdo_fetchcolumn('SELECT SUM(orderprice) FROM ' . tablename(PDO_NAME . 'smallorder') . (' WHERE plugin = \'wlfightgroup\' AND orderid = ' . $id . ' AND status = 1'));
			$refundnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename(PDO_NAME . 'smallorder') . (' WHERE plugin = \'wlfightgroup\' AND orderid = ' . $id . ' AND status = 1'));
		}
		else {
			$refundnum = $order['usetimes'];
		}

		if ($unline) {
			$res['status'] = 1;
		}
		else {
			$res = wlPay::refundMoney($order['id'], $money, '代理后台退款', 'wlfightgroup', 2);
		}

		if ($res['status']) {
			if ($order['neworderflag']) {
				pdo_update('wlmerchant_smallorder', array('status' => 3, 'refundtime' => time()), array('plugin' => 'wlfightgroup', 'orderid' => $id, 'status' => 1));

				if ($order['applyrefund']) {
					$reason = '买家申请退款。';
					$orderdata['applyrefund'] = 2;
				}
				else {
					$reason = '拼团系统退款。';
				}

				$hexiao = pdo_get('wlmerchant_smallorder', array('orderid' => $id, 'plugin' => 'wlfightgroup', 'status' => 2), array('id'));

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
				$reason = '拼团系统退款。';
			}

			$url = app_url('wlfightgroup/fightapp/expressorder', array('orderid' => $id));
			$money = $money ? '￥' . $money : '￥' . $order['price'];
			$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('openid', 'uid'));
			$openid = $member['openid'];

			if ($order['card_id']) {
				$remark = '订单退款恢复抵扣积分';
				Member::credit_update_credit1($order['mid'], $order['card_id'], $remark);
			}

			if ($order['disorderid']) {
				Distribution::refunddis($order['disorderid']);
			}

			$good = pdo_get('wlmerchant_fightgroup_goods', array('id' => $order['fkid']), array('stock', 'realsalenum'));

			if ($order['specid']) {
				$option = pdo_get('wlmerchant_goods_option', array('id' => $order['specid']), array('stock'));
				$optionstock = $option['stock'] + $refundnum;
				pdo_update('wlmerchant_goods_option', array('stock' => $optionstock), array('id' => $order['specid']));
			}
			else {
				$data2['stock'] = $good['stock'] + $refundnum;
			}

			$data2['realsalenum'] = $good['realsalenum'] + $refundnum;
			Wlfightgroup::updateGoods($data2, $order['fkid']);
			Message::refundNotice($openid, $reason, $money, $url);
		}
		else {
			pdo_fetch('update' . tablename('wlmerchant_order') . ('SET failtimes = failtimes+1 WHERE id = ' . $id));
		}

		return $res;
	}

	static public function doTask()
	{
		global $_W;
		$groups = pdo_getall('wlmerchant_fightgroup_group', array('status' => 1, 'failtime <' => time()));

		foreach ($groups as $key => $v) {
			$_W['uniacid'] = $v['uniacid'];
			pdo_update('wlmerchant_fightgroup_group', array('status' => 3), array('id' => $v['id']));
			$orders = pdo_getall('wlmerchant_order', array('fightgroupid' => $v['id'], 'status' => 1));
			$goods = self::getSingleGood($v['goodsid'], 'stock,realsalenum');
			$num = 0;

			foreach ($orders as $key => $order) {
				pdo_update('wlmerchant_order', array('status' => 6), array('id' => $order['id']));
				$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('openid'));
				Message::groupresult($member['openid'], $order['fightgroupid'], 2);
				$num = $num + $order['num'];

				if ($orders['specid']) {
					$optionstock = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $orders['specid']), 'stock');
					$newstock = $optionstock + $num;
					pdo_update('wlmerchant_goods_option', array('stock' => $newstock), array('id' => $orders['specid']));
				}
			}

			if (empty($goods['specstatus'])) {
				$updata['stock'] = $num + $goods['stock'];
			}

			$updata['realsalenum'] = $goods['realsalenum'] - $num;
			pdo_update('wlmerchant_fightgroup_goods', $updata, array('id' => $v['goodsid']));
		}

		$remoneyorders = pdo_fetchall('SELECT mid,id,price,card_id,failtimes,aid,uniacid FROM ' . tablename('wlmerchant_order') . 'WHERE status = 6 AND plugin = \'wlfightgroup\' AND failtimes < 3 ORDER BY id DESC');

		foreach ($remoneyorders as $key => $or) {
			$_W['uniacid'] = $or['uniacid'];
			$_W['aid'] = $v['aid'];
			self::refund($or['id']);
		}

		$nowtime = time();
		$startgoods = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_fightgroup_goods') . ('WHERE islimittime = 1 AND status = 0 AND limitstarttime < ' . $nowtime . ' AND limitendtime > ' . $nowtime . ' ORDER BY id DESC'));

		if ($startgoods) {
			foreach ($startgoods as $key => $start) {
				pdo_update('wlmerchant_fightgroup_goods', array('status' => 1), array('id' => $start['id']));
			}
		}

		$endgoods = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_fightgroup_goods') . ('WHERE islimittime = 1 AND status = 1 AND limitendtime < ' . $nowtime . ' ORDER BY id DESC'));

		if ($endgoods) {
			foreach ($endgoods as $key => $end) {
				pdo_update('wlmerchant_fightgroup_goods', array('status' => 0), array('id' => $end['id']));
			}
		}

		$actorder3 = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_order') . ('WHERE plugin = \'wlfightgroup\' AND status = 1 AND estimatetime < ' . $nowtime . ' AND estimatetime > 0 ORDER BY id DESC'));

		if (!empty($actorder3)) {
			foreach ($actorder3 as $key => $actor3) {
				pdo_update('wlmerchant_order', array('status' => 9, 'overtime' => time()), array('id' => $actor3['id']));
			}
		}
	}
}

defined('IN_IA') || exit('Access Denied');
echo ' 
';

?>
