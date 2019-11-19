<?php
//dezend by http://www.sucaihuo.com/
class Bargain
{
	static public function saveActive($active, $param = array())
	{
		global $_W;

		if (!is_array($active)) {
			return false;
		}

		$active['uniacid'] = $_W['uniacid'];
		$active['aid'] = $_W['aid'];
		$active['sid'] = $active['sid'] ? $active['sid'] : $_W['sid'];
		$active['createtime'] = time();

		if (empty($param)) {
			pdo_insert(PDO_NAME . 'bargain_activity', $active);
			return pdo_insertid();
		}

		return false;
	}

	static public function updateActive($params, $where)
	{
		$res = pdo_update(PDO_NAME . 'bargain_activity', $params, $where);

		if ($where['id']) {
			Cache::deleteCache('active', $where['id']);
		}

		if ($res) {
			return 1;
		}

		return 0;
	}

	static public function getSingleActive($id, $select, $where = array())
	{
		$where['id'] = $id;
		$goodsInfo = Util::getSingelData($select, PDO_NAME . 'bargain_activity', $where);

		if (empty($goodsInfo)) {
			return array();
		}

		return $goodsInfo;
	}

	static public function getNumActive($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$activeInfo = Util::getNumData($select, PDO_NAME . 'bargain_activity', $where, $order, $pindex, $psize, $ifpage);
		return $activeInfo;
	}

	static public function createuserlist($mid, $activityid)
	{
		global $_W;
		if (empty($mid) || empty($activityid)) {
			return false;
		}

		$goods = pdo_get(PDO_NAME . 'bargain_activity', array('id' => $activityid), array('oldprice', 'sid'));
		$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'activityid' => $activityid, 'merchantid' => $goods['sid'], 'mid' => $mid, 'status' => 1, 'price' => $goods['oldprice'], 'createtime' => time(), 'updatetime' => time());
		pdo_insert(PDO_NAME . 'bargain_userlist', $data);
		$res = pdo_insertid();
		return $res;
	}

	static public function bargaining($mid, $activityid, $userid)
	{
		global $_W;
		$activity = self::getSingleActive($activityid, '*');
		$userlist = pdo_get('wlmerchant_bargain_userlist', array('id' => $userid));
		$helpflag = pdo_getcolumn('wlmerchant_bargain_helprecord', array('uniacid' => $_W['uniacid'], 'userid' => $userid, 'mid' => $_W['mid']), 'id');

		if ($helpflag) {
			wl_json(1, '您已砍过价了');
		}

		if ($activity['vipstatus'] == 1) {
			$now = time();

			if ($_W['wlsetting']['halfcard']['halfcardtype'] == 2) {
				$halfcardflag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $userlist['mid'] . ' AND aid = ' . $_W['aid'] . ' AND expiretime > ' . $now . ' AND disable != 1'));
			}
			else {
				$halfcardflag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $userlist['mid'] . ' AND expiretime > ' . $now . ' AND disable != 1'));
			}

			if ($halfcardflag) {
				$lowprice = $activity['vipprice'];
			}
		}

		$lowprice = $lowprice ? $lowprice : $activity['price'];

		if ($userlist['price'] <= $lowprice) {
			wl_json(1, '已砍至底价，无法继续砍价');
		}

		$price = self::getBargainPrice($activity, $userlist['price'], $lowprice);

		if ($price) {
			$afterprice = sprintf('%.2f', $userlist['price'] - $price);
			$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'activityid' => $activityid, 'authorid' => $userlist['mid'], 'mid' => $mid, 'userid' => $userid, 'bargainprice' => $price, 'afterprice' => $afterprice, 'createtime' => time());
			$res = pdo_insert(PDO_NAME . 'bargain_helprecord', $data);
			$barid = pdo_insertid();

			if ($res) {
				$res2 = pdo_update('wlmerchant_bargain_userlist', array('price' => $afterprice, 'updatetime' => time()), array('id' => $userid));
			}

			if ($res2) {
				return $barid;
			}

			return false;
		}
	}

	public function getBargainPrice($activity, $userNowPrice, $lowprice)
	{
		if ($userNowPrice <= $lowprice) {
			return 0;
		}

		$rules = unserialize($activity['rules']);
		$price = 0;
		$inRule = false;

		foreach ($rules as $rule) {
			if ($rule['rule_pice'] <= $userNowPrice) {
				$price = rand($rule['rule_start'] * 100, $rule['rule_end'] * 100) / 100;
				$inRule = true;
				break;
			}
		}

		if (!$inRule) {
			$price = rand(0.5 * 100, 1 * 100) / 100;
		}

		if ($userNowPrice - $price < $lowprice) {
			$price = $userNowPrice - $lowprice;
		}

		$price = sprintf('%.2f', $price);
		return $price;
	}

	static public function hexiaoorder($id, $mid, $num = 1, $type = 1)
	{
		global $_W;
		$order = pdo_get('wlmerchant_order', array('id' => $id));
		$record = pdo_get('wlmerchant_bargain_userlist', array('id' => $order['specid']));
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
			pdo_update('wlmerchant_disorder', array('status' => 1), array('id' => $order['disorderid'], 'status' => 0));
			pdo_update('wlmerchant_order', array('status' => 2), array('id' => $order['id']));
		}

		$res = pdo_update('wlmerchant_bargain_userlist', $params, array('id' => $record['id']));

		if ($res) {
			$active = pdo_get('wlmerchant_bargain_activity', array('id' => $order['fkid']), array('name'));
			$order['checkcode'] = pdo_getcolumn(PDO_NAME . 'bargain_userlist', array('id' => $order['specid']), 'qrcode');
			SingleMerchant::verifRecordAdd($order['aid'], $order['sid'], $order['mid'], 'bargain', $order['id'], $order['checkcode'], $active['name'], $type);
			$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('openid'));
			Message::hexiaoSuccess($member['openid'], $num, $active['name'], '');

			if ($type == 2) {
				Message::hexiaoTover($_W['wlmember']['openid'], $num, $active['name']);
			}

			return 1;
		}

		return 0;
	}

	static public function paybargainOrderNotify($params)
	{
		global $_W;
		Util::wl_log('payResult_notify', PATH_PLUGIN . 'bargain/data/', $params);
		$order_out = pdo_fetch('select * from' . tablename(PDO_NAME . 'order') . ('where orderno=\'' . $params['tid'] . '\''));
		$activeInfo = self::getSingleActive($order_out['fkid'], '*');
		$data = self::getbargainOrderPayData($params, $order_out);

		if ($order_out['status'] == 0) {
			$_W['aid'] = $order_out['aid'];

			if ($order_out['expressid']) {
				$data['status'] = 8;
			}

			self::createRecord($order_out['id'], $order_out['num'], $order_out['specid'], $fightstatus);
			if (p('distribution') && empty($activeInfo['isdistri'])) {
				if ($order_out['vipbuyflag']) {
					$onemoney = sprintf('%.2f', $activeInfo['viponedismoney'] * $order_out['num']);
					$twomoney = sprintf('%.2f', $activeInfo['viptwodismoney'] * $order_out['num']);
					$threemoney = sprintf('%.2f', $activeInfo['vipthreedismoney'] * $order_out['num']);
				}
				else {
					$onemoney = sprintf('%.2f', $activeInfo['onedismoney'] * $order_out['num']);
					$twomoney = sprintf('%.2f', $activeInfo['twodismoney'] * $order_out['num']);
					$threemoney = sprintf('%.2f', $activeInfo['threedismoney'] * $order_out['num']);
				}

				$disorderid = Distribution::disCore($order_out['mid'], $order_out['price'], $onemoney, $twomoney, $threemoney, $order_out['id'], 'bargain', $activeInfo['dissettime']);
				$data['disorderid'] = $disorderid;
			}

			if (p('userlabel')) {
				$_W['aid'] = $order_out['aid'];
				Userlabel::addlabel($order_out['mid'], $order_out['fkid'], 'bargain');
			}

			pdo_update(PDO_NAME . 'order', $data, array('orderno' => $params['tid']));
			$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $order_out['mid']), 'openid');
			$orderid = $order_out['id'];
			$url = app_url('order/userorder/orderlist', array('status' => 'all'));
			Store::addFans($order_out['sid'], $order_out['mid']);
			Message::paySuccess($order_out['id'], 'bargain');
		}
		else {
			if ($data['status']) {
				$data['status'] = 6;
				pdo_update(PDO_NAME . 'order', $data, array('orderno' => $params['tid']));
			}
		}
	}

	static public function getbargainOrderPayData($params, $order_out)
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

	static public function createRecord($orderid, $num, $userid, $fightstatus)
	{
		global $_W;
		$record['orderid'] = $orderid;

		if ($fightstatus) {
			$expressid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $orderid), 'expressid');
			$record['expressid'] = $expressid;
		}
		else {
			$record['qrcode'] = Util::createConcode(5);
			$record['usetimes'] = $num;
		}

		$record['updatetime'] = time();
		$record['status'] = 2;
		pdo_update(PDO_NAME . 'bargain_userlist', $record, array('id' => $userid));
	}

	static public function paybargainOrderReturn($params, $backurl = false)
	{
		Util::wl_log('payResult_return', PATH_PLUGIN . 'bargain/data/', $params);
		$order_out = pdo_get(PDO_NAME . 'order', array('orderno' => $params['tid']), array('id'));
		wl_message('购买成功', app_url('order/userorder/payover', array('id' => $order_out['id'], 'type' => 8)), 'success');
	}

	static public function refund($id, $money, $unline = '')
	{
		$order = pdo_get(PDO_NAME . 'order', array('id' => $id));

		if ($unline) {
			$res['status'] = 1;
		}
		else {
			$res = wlPay::refundMoney($id, $money, '砍价订单退款', 'bargain', 2);
		}

		if ($res['status']) {
			if ($order['applyrefund']) {
				pdo_update('wlmerchant_order', array('status' => 7, 'refundtime' => time(), 'applyrefund' => 2), array('id' => $order['id']));
				$reason = '买家申请退款。';
			}
			else {
				pdo_update('wlmerchant_order', array('status' => 7, 'refundtime' => time()), array('id' => $order['id']));
				$reason = '砍价系统退款。';
			}

			if ($order['disorderid']) {
				Distribution::refunddis($order['disorderid']);
			}

			$url = app_url('bargain/bargain_app/bargainlist');
			$money = $money ? '￥' . $money : '￥' . $order['price'];
			$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('openid'));
			$openid = $member['openid'];
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
		global $_GPC;
		$sets = pdo_fetchall('select distinct aid from ' . tablename(PDO_NAME . 'oparea') . ('where uniacid = ' . $_W['uniacid'] . ' and status = 1'));

		foreach ($sets as $set) {
			$_W['aid'] = $set['aid'];
			if (empty($_W['aid']) || $_W['aid'] == -1) {
				continue;
			}

			$activitys1 = pdo_getall(PDO_NAME . 'bargain_activity', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'starttime <' => time(), 'status' => 1), array('id'));

			if (!empty($activitys1)) {
				foreach ($activitys1 as $k => $v) {
					pdo_update(PDO_NAME . 'bargain_activity', array('status' => 2), array('id' => $v['id']));
				}
			}

			$activitys2 = pdo_getall(PDO_NAME . 'bargain_activity', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'endtime <' => time(), 'status' => 2), array('id'));

			if (!empty($activitys2)) {
				foreach ($activitys2 as $k => $v2) {
					pdo_update(PDO_NAME . 'bargain_activity', array('status' => 3), array('id' => $v2['id']));
					$bargainuser = pdo_getall('wlmerchant_bargain_userlist', array('activityid' => $v2['id'], 'status' => 1), array('id'));

					if (!empty($bargainuser)) {
						foreach ($bargainuser as $k => $user) {
							pdo_update(PDO_NAME . 'bargain_userlist', array('status' => 3), array('id' => $user['id']));
						}
					}
				}
			}
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
