<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Paybill_WeliamController extends Weliam_merchantModuleSite
{
	public function paycheck()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '在线买单 - ' . $_W['wlsetting']['base']['name'] : '在线买单';
		$id = $_GPC['id'];

		if (empty($id)) {
			wl_message('缺少重要参数请刷新重试', referer(), 'error');
		}

		$store = pdo_get(PDO_NAME . 'merchantdata', array('id' => $id, 'uniacid' => $_W['uniacid']), array('storename', 'payonline'));

		if (empty($store['payonline'])) {
			wl_message('该商户未开启在线买单功能', referer(), 'error');
		}

		$storename = $store['storename'];
		$cardid = $_GPC['cardid'];
		$backurl = app_url('order/paybill/paycheck', array('id' => $id));
		Member::checklogin($backurl);

		if (empty($cardid)) {
			$cardid = Member::checkhalfmember();
		}

		$card = pdo_get(PDO_NAME . 'halfcardmember', array('id' => $cardid));

		if (empty($card)) {
			$discount = 10;
			$tip = '您不是会员';
		}
		else {
			$expiretime = $card['expiretime'];
			$realcard = pdo_get('wlmerchant_halfcard_realcard', array('cardid' => $card['id']), array('icestatus'));

			if ($realcard['icestatus']) {
				$discount = 10;
				$tip = '此卡已被冻结,如有疑问请联系管理员';
			}

			if ($expiretime < time()) {
				$discount = 10;
				$tip = '此卡已过期，请续费或换卡重试';
			}
			else if ($card['disable']) {
				$discount = 10;
				$tip = '此卡已被禁用,如有疑问请联系管理员';
			}
			else {
				$halfactive = pdo_get('wlmerchant_halfcardlist', array('merchantid' => $id, 'status' => 1));

				if ($halfactive) {
					$halfactive['level'] = unserialize($halfactive['level']);

					if (is_array($halfactive['level'])) {
						if (in_array($card['levelid'], $halfactive['level'])) {
							$levelpass = 1;
						}
						else {
							$levelpass = 0;
						}
					}
					else {
						$levelpass = 1;
					}

					if ($levelpass) {
						if ($halfactive['timeslimit']) {
							$begintime = strtotime(date('Y-m-d', time()));
							$todaytime = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfactive['id'] . ' AND createtime > ' . $begintime . ' AND type = 1'));

							if ($halfactive['timeslimit'] <= $todaytime) {
								$discount = 10;
								$tip = '该商户今日特权名额已用完';
							}
						}

						if ($halfactive['datestatus'] == 1) {
							$weeks = unserialize($halfactive['week']);
							$today = date('w');

							if ($today == 0) {
								$today = 7;
							}

							if (in_array($today, $weeks)) {
								if ($halfactive['timeslimit']) {
									$zerotime = strtotime(date('Y-m-d'), time());
									$times2 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfactive['id'] . ' AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 1 AND freeflag = 1'));
									$surplus = $halfactive['timeslimit'] - $times2;

									if ($surplus) {
										$actflag = 1;
										$discount = $halfactive['activediscount'];
									}
									else {
										$actflag = 0;

										if ($halfactive['daily']) {
											$discount = $halfactive['discount'];
										}
										else {
											$discount = 10;
											$tip = '该商户今日无会员优惠';
										}
									}
								}
								else {
									$actflag = 1;
									$discount = $halfactive['activediscount'];
								}
							}
							else {
								$actflag = 0;

								if ($halfactive['daily']) {
									$discount = $halfactive['discount'];
								}
								else {
									$discount = 10;
									$tip = '该商户今日无会员优惠';
								}
							}
						}
						else {
							$days = unserialize($halfactive['day']);
							$today = date('j');

							if (in_array($today, $days)) {
								if ($halfactive['timeslimit']) {
									$zerotime = strtotime(date('Y-m-d'), time());
									$times2 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfactive['id'] . ' AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 1 AND freeflag = 1'));
									$surplus = $halfactive['timeslimit'] - $times2;

									if ($surplus) {
										$actflag = 1;
										$discount = $halfactive['activediscount'];
									}
									else {
										$actflag = 0;

										if ($halfactive['daily']) {
											$discount = $halfactive['discount'];
										}
										else {
											$discount = 10;
											$tip = '该商户今日无会员优惠';
										}
									}
								}
								else {
									$actflag = 1;
									$discount = $halfactive['activediscount'];
								}
							}
							else {
								$actflag = 0;

								if ($halfactive['daily']) {
									$discount = $halfactive['discount'];
								}
								else {
									$discount = 10;
									$tip = '该商户今日无会员优惠';
								}
							}
						}
					}
					else {
						$discount = 10;
						$tip = '您的会员等级无法使用优惠';
					}
				}
				else {
					$discount = 10;
					$tip = '该商户无会员优惠';
				}
			}
		}

		if (empty($actflag)) {
			$actflag = 0;
		}

		$setting = Setting::wlsetting_read('share')['forcefollow'];
		if (in_array('payOnline', $setting) && $_W['fans']['follow'] != 1 && is_weixin()) {
			$payOnline = 1;
		}
		else {
			$payOnline = 0;
		}

		if (is_array($cardid)) {
			$cardid = $cardid['id'];
		}

		include wl_template('order/paycheck');
	}

	public function createorder()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$halfid = $_GPC['halfid'];
		$ordermoney = $_GPC['ordermoney'];
		$isoutmoney = $_GPC['isoutmoney'];
		$discount = $_GPC['discount'];
		$actflag = $_GPC['actflag'];
		$halfactive = pdo_get('wlmerchant_halfcardlist', array('id' => $halfid));
		$trueordermoney = sprintf('%.2f', $ordermoney - $isoutmoney);
		$paymoney = sprintf('%.2f', $trueordermoney * 0.10000000000000001 * $discount);
		$paymoney = sprintf('%.2f', $paymoney + $isoutmoney);
		$jianmoney = sprintf('%.2f', $ordermoney - $paymoney);

		if ($paymoney < 0.01) {
			exit(json_encode(array('errno' => 1, 'message' => '实际支付金额必须大于0')));
		}

		if ($discount < 10) {
			$vipbuyflag = 1;
		}
		else {
			$vipbuyflag = 0;
		}

		if ($vipbuyflag) {
			$remark = '不可优惠金额:' . $isoutmoney . '元，优惠折扣：' . $discount . '折';
		}
		else {
			$remark = '不可优惠金额:' . $isoutmoney . '元，无优惠折扣';
		}

		$settlementmoney = Store::gethalfsettlementmoney($paymoney, $id, $vipbuyflag);
		$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'sid' => $id, 'aid' => $_W['aid'], 'fkid' => $halfid, 'plugin' => 'halfcard', 'payfor' => 'halfcardOrder', 'orderno' => createUniontid(), 'status' => 0, 'createtime' => TIMESTAMP, 'oprice' => $isoutmoney, 'price' => $paymoney, 'num' => 1, 'vipbuyflag' => $vipbuyflag, 'specid' => 0, 'goodsprice' => $ordermoney, 'card_type' => $actflag, 'card_id' => $cardid, 'card_fee' => $jianmoney, 'remark' => $remark, 'spec' => $discount, 'settlementmoney' => $settlementmoney);
		pdo_insert(PDO_NAME . 'order', $data);
		$orderid = pdo_insertid();
		exit(json_encode(array('errno' => 0, 'message' => $orderid)));
	}

	public function topay()
	{
		global $_W;
		global $_GPC;
		$orderid = $_GPC['orderid'];

		if (empty($orderid)) {
			wl_message('缺少重要参数，请重试');
		}

		$data = pdo_get(PDO_NAME . 'order', array('uniacid' => $_W['uniacid'], 'id' => $orderid), array('orderno', 'price', 'sid'));
		$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $data['sid']), 'storename');
		$params = array('tid' => $data['orderno'], 'ordersn' => $data['orderno'], 'title' => $storename . '在线买单', 'fee' => $data['price'], 'module' => 'weliam_merchant');
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => 'Rush', 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Merchant', 'payfor' => 'payonline', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}
}

?>
