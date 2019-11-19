<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Rushpay_WeliamController extends Weliam_merchantModuleSite
{
	public function topayrush()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '抢购订单 - ' . $_W['wlsetting']['base']['name'] : '抢购订单';
		$id = $_GPC['id'];
		$num = $_GPC['num'];
		$invitid = $_GPC['invitid'];
		$usestatus = $_GPC['usestatus'];
		$cardId = $_GPC['cardId'];
		$uid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $_W['mid']), 'uid');

		if ($num < 1) {
			wl_message(array('errno' => 1, 'message' => '数量选择错误，请重新选择'));
		}

		$activity = Rush::getSingleActive($id, '*');
		Member::checklogin(app_url('rush/home/detail', array('id' => $id)));

		if (empty($_W['mid'])) {
			wl_message(array('errno' => 2, 'message' => '未登录!'));
		}

		$nopayorder = pdo_getcolumn('wlmerchant_rush_order', array('mid' => $_W['mid'], 'status' => 0, 'activityid' => $id), 'id');

		if (!empty($nopayorder)) {
			wl_message(array('errno' => 3, 'message' => '请先支付或取消未支付的抢购订单'));
		}

		if ($activity['levelnum'] < 1) {
			wl_message(array('errno' => 1, 'message' => '您来晚一步,最后的机会已经被抢走。'));
		}

		if ($activity['status'] != 2) {
			if ($activity['status'] == 1) {
				wl_message(array('errno' => 1, 'message' => '活动未开始'));
			}
			else if ($activity['status'] == 3) {
				wl_message(array('errno' => 1, 'message' => '活动已结束'));
			}
			else if ($activity['status'] == 4) {
				wl_message(array('errno' => 1, 'message' => '活动已下架'));
			}
			else {
				if ($activity['status'] == 7) {
					wl_message(array('errno' => 1, 'message' => '商品已抢完'));
				}
			}
		}

		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
		if (empty($_W['wlmember']['mobile']) && in_array('rush', $mastmobile)) {
			wl_message(array('errno' => 5, 'message' => '未绑定手机号，去绑定？'));
		}

		if ($activity['optionstatus']) {
			$optionid = $_GPC['optionid'];

			if ($optionid) {
				$option = pdo_get('wlmerchant_goods_option', array('id' => $optionid), array('stock', 'price', 'vipprice', 'title'));
				$activity['price'] = $option['price'];
				$activity['vipprice'] = $option['vipprice'];
				$activity['levelnum'] = $option['stock'];
				$optiontext = $option['title'];
			}
			else {
				wl_message(array('errno' => 1, 'message' => '规格参数错误，请重新选择'));
			}
		}
		else {
			$optionid = 0;
		}

		$price = $activity['price'];
		$level = unserialize($activity['level']);
		$halfcardflag = Member::checkhalfmember();

		if ($activity['vipstatus'] == 1) {
			if ($halfcardflag || !empty($cardId)) {
				$price = $activity['vipprice'];
				$vipbuyflag = 1;
			}
		}
		else {
			if ($activity['vipstatus'] == 2) {
				if (empty($halfcardflag) && empty($cardId)) {
					wl_message(array('errno' => 1, 'message' => '该商品会员特供，请先成为会员'));
				}
				else {
					if ($level && empty($cardId)) {
						$flag = Halfcard::checklevel($_W['mid'], $level);

						if (empty($flag)) {
							wl_message(array('errno' => 1, 'message' => '您所在的会员等级无权购买该商品'));
						}
					}
					else {
						if (!empty($cardId)) {
							$levelId = pdo_getcolumn(PDO_NAME . 'halfcard_type', array('id' => $cardId), 'levelid');

							if (!in_array($levelId, $level)) {
								wl_message(array('errno' => 1, 'message' => '您所开通的会员卡无权购买该商品'));
							}
						}
					}
				}
			}
		}

		if (empty($vipbuyflag)) {
			$vipbuyflag = 0;
		}

		if ($activity['op_one_limit']) {
			$alreadyBuyNum = pdo_fetch('SELECT SUM(num) FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $_W['mid'] . ' AND status IN (0,1,4,8,2,3,6,9) AND activityid = ' . $id . ' '));
			$alreadyBuyNum = array_values($alreadyBuyNum);
			$alreadyBuyNum = $alreadyBuyNum[0];
			$levelnum = $activity['op_one_limit'] - $alreadyBuyNum;

			if ($levelnum < 0) {
				$levelnum = 0;
			}

			if (!$levelnum) {
				wl_message(array('errno' => 1, 'message' => '限购商品!您已全部购买'));
			}
			else {
				if ($levelnum < $num) {
					wl_message(array('errno' => 1, 'message' => '限购商品!您还能购买' . $levelnum . $activity['unit']));
				}
			}
		}

		if ($_GPC['dk'] == 'true') {
			if ($price < $activity['creditmoney']) {
				$activity['creditmoney'] = $price;
			}

			$onecreditmoney = sprintf('%.2f', 1 / $_W['wlsetting']['creditset']['proportion']);
			$allcredit = sprintf('%.0f', $_W['wlmember']['credit1']);
			$dkmoney = sprintf('%.2f', $activity['creditmoney'] * $num);
			$dkcredit = ceil($dkmoney / $onecreditmoney);

			if ($allcredit < $dkcredit) {
				$dkcredit = $allcredit;
				$dkmoney = sprintf('%.2f', $onecreditmoney * $dkcredit);
			}

			$remark = '抢购[' . $activity['name'] . ']抵扣积分';
			Member::credit_update_credit1($_W['mid'], 0 - $dkcredit, $remark);
		}
		else {
			$dkcredit = 0;
			$dkmoney = 0;
		}

		if ($activity['sharestatus'] == 1) {
			$alshare = pdo_getcolumn(PDO_NAME . 'sharegift_record', array('uniacid' => $_W['uniacid'], 'plugin' => 1, 'mid' => $_W['mid'], 'goodsid' => $id, 'status' => 0, 'type' => 1), 'id');

			if ($alshare) {
				$sharemoney = sprintf('%.2f', $activity['sharemoney'] * $num);
			}
		}
		else {
			if ($activity['sharestatus'] == 2) {
				$alshare = pdo_getcolumn(PDO_NAME . 'sharegift_record', array('uniacid' => $_W['uniacid'], 'plugin' => 1, 'buymid' => $_W['mid'], 'mid' => $invitid, 'goodsid' => $id, 'status' => 0, 'type' => 2), 'id');
			}
		}

		if (empty($sharemoney)) {
			$sharemoney = 0;
		}

		$settlementmoney = Store::getsettlementmoney(1, $id, $num, $activity['sid'], $vipbuyflag, $optionid);

		if ($usestatus) {
			$addressid = $_GPC['addressid'];
			pdo_update('wlmerchant_address', array('status' => 0), array('mid' => $_W['mid'], 'uniacid' => $_W['uniacid']));
			pdo_update('wlmerchant_address', array('status' => 1), array('id' => $addressid));
			$address = pdo_get('wlmerchant_address', array('id' => $addressid));
			$data['uniacid'] = $_W['uniacid'];
			$data['mid'] = $_W['mid'];
			$data['goodsid'] = $activity['id'];
			$data['merchantid'] = $activity['sid'];
			$data['address'] = $addre = $address['province'] . $address['city'] . $address['county'] . $address['detailed_address'];
			$data['name'] = $username = $address['name'];
			$data['tel'] = $mobile = $address['tel'];

			if ($activity['expressid']) {
				$express = pdo_get('wlmerchant_express_template', array('id' => $activity['expressid']));

				if (mb_substr($address['province'], -1, 1, 'utf-8') == '省') {
					$address['province'] = mb_substr($address['province'], 0, mb_strlen($address['province']) - 1, 'utf-8');
				}

				if ($express['expressarray']) {
					$expressarray = unserialize($express['expressarray']);

					foreach ($expressarray as $key => &$v) {
						$v['area'] = rtrim($v['area'], ',');
						$v['area'] = explode(',', $v['area']);

						if (in_array($address['province'], $v['area'])) {
							if ($v['num'] < $num) {
								$expressprice = $v['money'] + ceil(($num - $v['num']) / $v['numex']) * $v['moneyex'];
							}
							else {
								$expressprice = $v['money'];
							}
						}
					}
				}

				if (empty($expressprice)) {
					if ($express['defaultnum'] < $num) {
						$expressprice = $express['defaultmoney'] + ceil(($num - $express['defaultnum']) / $express['defaultnumex']) * $express['defaultmoneyex'];
					}
					else {
						$expressprice = $express['defaultmoney'];
					}
				}
			}
			else {
				$expressprice = 0;
			}

			$data['expressprice'] = $expressprice;
			pdo_insert(PDO_NAME . 'express', $data);
			$expressid = pdo_insertid();
			$settlementmoney = sprintf('%.2f', $settlementmoney + $expressprice);
			$neworderflag = 0;
		}
		else {
			$random = Util::createConcode(1);
			$username = trim($_GPC['thname']);
			$mobile = trim($_GPC['thmobile']);
			$expressprice = 0;
			$neworderflag = 1;
		}

		if ($price * $num < $dkmoney || $dkmoney == $price * $num) {
			$dkmoney = sprintf('%.2f', $price * $num - 0.01);
		}

		$prices = sprintf('%.2f', $price * $num + $expressprice);
		$actualprice = sprintf('%.2f', $prices - $dkmoney - $sharemoney);

		if ($prices < $actualprice) {
			$actualprice = $prices;
		}

		$data = array('uniacid' => $activity['uniacid'], 'unionid' => $_W['unionid'], 'mid' => $_W['mid'], 'openid' => $_W['openid'], 'sid' => $activity['sid'], 'aid' => $activity['aid'], 'activityid' => $activity['id'], 'orderno' => createUniontid(), 'status' => 0, 'createtime' => TIMESTAMP, 'price' => $prices, 'actualprice' => $actualprice, 'num' => $num, 'username' => $username, 'mobile' => $mobile, 'address' => $addre, 'vipbuyflag' => $vipbuyflag, 'optionid' => $optionid, 'dkcredit' => $dkcredit, 'dkmoney' => $dkmoney, 'shareid' => $alshare, 'expressid' => $expressid, 'remark' => trim($_GPC['remark']), 'settlementmoney' => $settlementmoney, 'neworderflag' => $neworderflag, 'vip_card_id' => $cardId);
		pdo_insert(PDO_NAME . 'rush_order', $data);
		$orderid = pdo_insertid();

		if ($optionid) {
			$nowstock = $option['stock'] - $num;
			pdo_update('wlmerchant_goods_option', array('stock' => $nowstock), array('id' => $optionid));
		}
		else {
			$stock = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $data['activityid']), 'num');
			$alreadyBuyNum = pdo_fetch('SELECT SUM(num) FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status IN (0,1,2,3,6,9) AND activityid = ' . $data['activityid'] . ' '));
			$alreadyBuyNum = array_values($alreadyBuyNum);
			$buynum = $alreadyBuyNum[0];
			$data2['levelnum'] = $stock - $buynum;
			Rush::updateActive($data2, array('id' => $data['activityid']));
		}

		if ($alshare) {
			pdo_update('wlmerchant_sharegift_record', array('orderid' => $orderid, 'buymid' => $_W['mid']), array('id' => $alshare));
		}

		if (0 < $data['price']) {
			wl_json(0, '下单成功', $orderid);
		}
		else {
			if ($price == 0 && empty($cardId)) {
				$newdata = array('status' => 1, 'paytime' => time());

				if ($expressid) {
					$newdata['status'] = 8;
				}

				pdo_update(PDO_NAME . 'rush_order', $newdata, array('orderno' => $data['orderno']));

				if ($activity['integral']) {
					$remark = '抢购:[' . $activity['name'] . ']赠送积分';
					Member::credit_update_credit1($_W['mid'], $activity['integral'] * $num, $remark);
				}

				$url = app_url('order/userorder/orderlist', array('status' => 'all'));
				Message::paySuccess($orderid, 'rush');
				Store::addFans($activity['sid'], $_W['mid']);
				wl_message(array('errno' => 4, 'message' => '购买成功!'));
			}
			else {
				wl_json(0, '下单成功', $orderid);
			}
		}
	}

	public function topay()
	{
		global $_W;
		global $_GPC;
		$orderno = $_GPC['orderid'];

		if (empty($orderno)) {
			wl_message('缺少重要参数，请重试');
		}

		$data = pdo_get(PDO_NAME . 'rush_order', array('uniacid' => $_W['uniacid'], 'id' => $orderno), array('orderno', 'actualprice', 'sid', 'activityid', 'vip_card_id'));
		$activity = Rush::getSingleActive($data['activityid'], 'name');
		$bankrid = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $data['sid']), 'bankrid');
		$data = PayBuild::isOpenCard($data, 'actualprice');
		$params = array('tid' => $data['orderno'], 'ordersn' => $data['orderno'], 'title' => mb_substr($activity['name'], 0, 25, 'utf-8'), 'fee' => $data['actualprice'], 'module' => 'weliam_merchant');
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => 'Rush', 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Rush', 'payfor' => 'RushOrder', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}
}

?>
