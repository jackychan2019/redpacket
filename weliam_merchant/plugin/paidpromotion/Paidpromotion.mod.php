<?php
//dezend by http://www.sucaihuo.com/
class Paidpromotion
{
	static public function getpaidpr($type, $orderid, $paytype)
	{
		global $_W;
		$nowtime = time();

		if ($type == 1) {
			$order = pdo_get('wlmerchant_rush_order', array('id' => $orderid), array('actualprice', 'uniacid', 'activityid', 'mid', 'aid'));
			$activitys = pdo_getall('wlmerchant_payactive', array('uniacid' => $order['uniacid'], 'rushflag' => 1, 'starttime <' => $nowtime, 'endtime >' => $nowtime, 'status' => 1), array(), '', 'createtime DESC');
			$price = $order['actualprice'];
			$goodsid = $order['activityid'];

			foreach ($activitys as $key => $act) {
				if (empty($activity)) {
					if (empty($act['orderstatus'])) {
						$activity = $act;
					}
					else {
						$goodsids = unserialize($act['rushids']);

						if (in_array($goodsid, $goodsids)) {
							$activity = $act;
						}
					}
				}
			}
		}
		else if ($type == 2) {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('price', 'uniacid', 'fkid', 'mid', 'aid'));
			$activitys = pdo_getall('wlmerchant_payactive', array('uniacid' => $order['uniacid'], 'fightgroupflag' => 1, 'starttime <' => $nowtime, 'endtime >' => $nowtime, 'status' => 1), array(), '', 'createtime DESC');
			$price = $order['price'];
			$goodsid = $order['fkid'];

			foreach ($activitys as $key => $act) {
				if (empty($activity)) {
					if (empty($act['orderstatus'])) {
						$activity = $act;
					}
					else {
						$goodsids = unserialize($act['fightgroupids']);

						if (in_array($goodsid, $goodsids)) {
							$activity = $act;
						}
					}
				}
			}
		}
		else if ($type == 3) {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('price', 'fkid', 'mid', 'uniacid', 'aid'));
			$activitys = pdo_getall('wlmerchant_payactive', array('uniacid' => $order['uniacid'], 'couponflag' => 1, 'starttime <' => $nowtime, 'endtime >' => $nowtime, 'status' => 1), array(), '', 'createtime DESC');
			$price = $order['price'];
			$goodsid = $order['fkid'];

			foreach ($activitys as $key => $act) {
				if (empty($activity)) {
					if (empty($act['orderstatus'])) {
						$activity = $act;
					}
					else {
						$goodsids = unserialize($act['couponids']);

						if (in_array($goodsid, $goodsids)) {
							$activity = $act;
						}
					}
				}
			}
		}
		else if ($type == 4) {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('price', 'uniacid', 'aid', 'fkid', 'mid'));
			$activitys = pdo_getall('wlmerchant_payactive', array('uniacid' => $order['uniacid'], 'grouponflag' => 1, 'starttime <' => $nowtime, 'endtime >' => $nowtime, 'status' => 1), array(), '', 'createtime DESC');
			$price = $order['price'];
			$goodsid = $order['fkid'];

			foreach ($activitys as $key => $act) {
				if (empty($activity)) {
					if (empty($act['orderstatus'])) {
						$activity = $act;
					}
					else {
						$goodsids = unserialize($act['grouponids']);

						if (in_array($goodsid, $goodsids)) {
							$activity = $act;
						}
					}
				}
			}
		}
		else if ($type == 5) {
			$order = pdo_get('wlmerchant_halfcard_record', array('id' => $orderid), array('price', 'uniacid', 'aid', 'typeid', 'mid'));
			$activitys = pdo_getall('wlmerchant_payactive', array('uniacid' => $order['uniacid'], 'halfcardflag' => 1, 'starttime <' => $nowtime, 'endtime >' => $nowtime, 'status' => 1), array(), '', 'createtime DESC');
			$price = $order['price'];
			$goodsid = $order['typeid'];

			foreach ($activitys as $key => $act) {
				if (empty($activity)) {
					if (empty($act['orderstatus'])) {
						$activity = $act;
					}
					else {
						$goodsids = unserialize($act['halfcardids']);

						if (in_array($goodsid, $goodsids)) {
							$activity = $act;
						}
					}
				}
			}
		}
		else if ($type == 6) {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('price', 'uniacid', 'aid', 'fkid', 'mid'));
			$activitys = pdo_getall('wlmerchant_payactive', array('uniacid' => $order['uniacid'], 'chargeflag' => 1, 'starttime <' => $nowtime, 'endtime >' => $nowtime, 'status' => 1), array(), '', 'createtime DESC');
			$price = $order['price'];
			$goodsid = $order['fkid'];

			foreach ($activitys as $key => $act) {
				if (empty($activity)) {
					if (empty($act['orderstatus'])) {
						$activity = $act;
					}
					else {
						$goodsids = unserialize($act['chargeids']);

						if (in_array($goodsid, $goodsids)) {
							$activity = $act;
						}
					}
				}
			}
		}
		else {
			if ($type == 7) {
				$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('price', 'uniacid', 'fkid', 'mid', 'aid'));
				$activity = pdo_get('wlmerchant_payactive', array('uniacid' => $order['uniacid'], 'aid' => $order['aid'], 'payonlineflag' => 1, 'starttime <' => $nowtime, 'endtime >' => $nowtime, 'status' => 1), array(), '', 'createtime DESC');
				$price = $order['price'];
			}
		}

		if ($activity) {
			if ($activity['userstatus']) {
				$halfflag = Member::checkhalfmember();

				if (empty($halfflag)) {
					return 0;
				}
			}

			if ($price < $activity['orderprice'] && empty($activity['orderstatus'])) {
				return 0;
			}

			if ($activity['integralrate']) {
				$integral = floor($price / $activity['integralrate']);
			}

			if ($activity['giftstatus'] == 1) {
				$couponid = $activity['giftcouponid'];
			}
			else {
				if ($activity['giftstatus'] == 2) {
					$codeid = pdo_getcolumn('wlmerchant_token', array('uniacid' => $order['uniacid'], 'remark' => $activity['codereamrk'], 'status' => 0), 'id');
					pdo_update('wlmerchant_token', array('status' => 2), array('id' => $codeid));
				}
			}

			$data = array('uniacid' => $order['uniacid'], 'aid' => $order['aid'], 'activeid' => $activity['id'], 'integral' => $integral, 'couponid' => $couponid, 'getcouflag' => 0, 'codeid' => $codeid, 'paytype' => $paytype, 'price' => $price, 'createtime' => time(), 'img' => $activity['img'], 'type' => $type, 'orderid' => $orderid);
			pdo_insert(PDO_NAME . 'paidrecord', $data);
			$paidprid = pdo_insertid();
			if ($paidprid && $integral) {
				Member::credit_update_credit1($order['mid'], $integral, '支付有礼赠送积分');
			}

			if ($data['couponid'] && $activity['getstatus'] && $paidprid) {
				$couponIdList = explode(',', $activity['giftcouponid']);

				if (is_array($couponIdList)) {
					$acresult = '';

					foreach ($couponIdList as $k => $v) {
						$coupons = wlCoupon::getSingleCoupons($v, '*');
						$num = wlCoupon::getCouponNum($v, 1);
						if ($coupons['time_type'] == 1 && $coupons['endtime'] < time()) {
							$acresult = '[失败]已停止发放';
						}

						if ($coupons['status'] == 0) {
							$acresult = '[失败]已被禁用';
						}

						if ($coupons['status'] == 3) {
							$acresult = '[失败]已失效';
						}

						if ($coupons['quantity'] - 1 < $coupons['surplus']) {
							$acresult = '[失败]已被领光';
						}

						if ($num) {
							if (($coupons['get_limit'] < $num || $num == $coupons['get_limit']) && 0 < $coupons['get_limit']) {
								$acresult = '[失败]只能领取' . $coupons['get_limit'] . '张';
							}
						}

						if (empty($acresult)) {
							if ($coupons['time_type'] == 1) {
								$starttime = $coupons['starttime'];
								$endtime = $coupons['endtime'];
							}
							else {
								$starttime = time();
								$endtime = time() + $coupons['deadline'] * 24 * 3600;
							}

							$data = array('mid' => $order['mid'], 'aid' => $order['aid'], 'parentid' => $coupons['id'], 'status' => 1, 'type' => $coupons['type'], 'title' => $coupons['title'], 'sub_title' => $coupons['sub_title'], 'content' => $coupons['goodsdetail'], 'description' => $coupons['description'], 'color' => $coupons['color'], 'starttime' => $starttime, 'endtime' => $endtime, 'createtime' => time(), 'usetimes' => $coupons['usetimes'], 'concode' => Util::createConcode(4), 'uniacid' => $order['uniacid']);
							$res = pdo_insert(PDO_NAME . 'member_coupons', $data);
							$couponUserId = pdo_insertid();

							if ($res) {
								$newsurplus = $coupons['surplus'] + 1;
								wlCoupon::updateCoupons(array('surplus' => $newsurplus), array('id' => $v));
								$url = app_url('wlcoupon/coupon_app/coupondetail', array('id' => $couponUserId));
								$acresult = '[成功]领取成功';
							}
							else {
								$acresult = '[失败]领取失败';
							}
						}

						$storeadminopenid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $order['mid']), 'openid');
						$first = '“' . $coupons['title'] . '”领取结果通知';
						$acname = '支付有礼-卡卷领取';
						$remark = '';
						Message::jobNotice($storeadminopenid, $first, $acname, $acresult, $remark, $url);
						$acresult = '';
					}
				}

				pdo_update(PDO_NAME . 'paidrecord', array('getcouflag' => 1, 'getcoutime' => time()), array('id' => $paidprid));
			}

			return $paidprid;
		}

		return 0;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
