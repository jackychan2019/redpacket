<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class wlPay extends WeModuleSite
{
	static public function wl_pay($params = array())
	{
		global $_W;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '支付订单 - ' . $_W['wlsetting']['base']['name'] : '支付订单';

		if ($params['fee'] <= 0) {
			wl_message('支付金额不得小于0');
		}

		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => $params['plugin'], 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => $params['plugin'], 'payfor' => $params['payfor'], 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		if ($log['status'] == '1') {
			wl_message('这个订单已经支付成功, 不需要重复支付.');
		}

		$pay = unserialize($_W['wlsetting']['payset']['status']);
		$payvalue = unserialize($_W['wlsetting']['payset']['value']);
		if (!is_array($pay) || empty($pay)) {
			wl_message('没有有效的支付方式, 请联系网站管理员.');
		}

		if (empty($_W['member']['uid'])) {
			$key = array_search('credit', $pay);

			if ($key !== false) {
				array_splice($pay, $key, 1);
			}
		}

		if (in_array('credit', $pay)) {
			$credtis = mc_credit_fetch($_W['member']['uid']);
		}

		if (in_array('brankwechat', $pay)) {
			if (!empty($params['bankrid'])) {
				$payvalue['bankrid'] = $params['bankrid'];
			}

			if (empty($log['plid'])) {
				$we7log = $log;
				unset($we7log['plugin']);
				unset($we7log['payfor']);
				pdo_insert('core_paylog', $we7log);
			}

			$bankpay = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&tid=' . $params['tid'] . '&title=' . $params['title'] . '&fee=' . $params['fee'] . '&ordersn=' . $params['ordersn'] . '&user=' . $_W['member']['uid'] . '&rid=' . $payvalue['bankrid'] . '&ms=weliam_merchant&do=payex&m=bm_payms';
		}

		$payset = Setting::wlsetting_read('payset');

		if ($payset['wechatstatus'] == 2) {
			$wqlog = pdo_get('core_paylog', array('uniacid' => $_W['uniacid'], 'module' => 'weliam_merchant', 'tid' => $params['tid']));

			if (empty($wqlog)) {
				$wqlog = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $params['user'], 'module' => 'weliam_merchant', 'uniontid' => date('YmdHis') . random(14, 1), 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
				pdo_insert('core_paylog', $wqlog);
			}

			$wparams = array('tid' => $params['tid'], 'title' => $params['title'], 'fee' => $params['fee'], 'ordersn' => $params['ordersn'], 'user' => $_W['member']['uid'], 'module' => 'weliam_merchant');
		}

		if ($payset['wechatstatus'] == 3) {
			$value = unserialize($payset['value']);
			$host = $value['ymf_host'];
			$uid = 'weliam_merchant';
			$customerId = $value['ymf_customerId'];
			$secret = $value['ymf_secret'];
			$notifyUrl = app_url('common/ymfNotify/Notify');
			$successUrl = app_url('common/ymfNotify/Result');
			$notifyUrl = base64_encode(urlencode($notifyUrl));
			$successUrl = base64_encode(urlencode($successUrl));
			$post_data['money'] = $params['fee'];
			$post_data['selfOrdernum'] = $params['tid'];
			$post_data['remark'] = 'NULL';
			$post_data['openId'] = $_W['openid'];
			$post_data['customerId'] = $customerId;
			$post_data['notifyUrl'] = $notifyUrl;
			$post_data['successUrl'] = $successUrl;
			$post_data['uid'] = $uid;
			$post_data['goodsName'] = $params['title'];
			ksort($post_data);
			$o = '';

			foreach ($post_data as $k => $v) {
				$o .= $k . '=' . $v . '&';
			}

			$post_data = mb_substr($o, 0, -1, 'utf-8');
			$post_data_temp = $secret . $post_data;
			$sign = strtoupper(md5($post_data_temp));
			$url = $host . '/index.php?s=/Home/linenew/m_pay';
			$ymfurl = $url . '/selfOrdernum/' . $params['tid'] . '/openId/' . $_W['openid'] . '/customerId/' . $customerId . '/money/' . $params['fee'] . '/notifyUrl/' . $notifyUrl . '/successUrl/' . $successUrl . '/uid/' . $uid . '/goodsName/' . $params['title'] . '/remark/' . 'NULL' . '/sign/' . $sign;
		}

		include wl_template('common/pay');
	}

	static public function refundMoney($id, $money, $remark, $plugin, $type = 3)
	{
		global $_W;
		load()->model('refund');
		$refund = false;
		$pluginArray = array('rush', 'vip', 'coupon', 'merchant', 'wlfightgroup', 'activity', 'groupon', 'bargain');

		switch ($plugin) {
		case 'rush':
			$orderinfo = Rush::getSingleOrder($id, '*');
			$orderinfo['price'] = $orderinfo['actualprice'];
			break;

		case 'wlfightgroup':
			$orderinfo = Wlfightgroup::getSingleOrder($id, '*');
			break;

		case 'activity':
			$orderinfo = pdo_get('wlmerchant_order', array('id' => $id));
			break;

		case 'groupon':
			$orderinfo = pdo_get('wlmerchant_order', array('id' => $id));
			break;

		case 'bargain':
			$orderinfo = pdo_get('wlmerchant_order', array('id' => $id));
			break;

		default:
			$orderinfo = pdo_get('wlmerchant_order', array('id' => $id));
			break;
		}

		$payfee = $orderinfo['price'];

		if ($orderinfo['price'] < $money) {
			$errMsg = '退款金额大于实际支付金额，无法退款';
		}
		else if (0 < $money) {
			$orderinfo['price'] = $money;
		}
		else {
			$money = $orderinfo['price'];
		}

		$refundRecord = array('sid' => $orderinfo['sid'], 'orderno' => $orderinfo['orderno'], 'mid' => $orderinfo['mid'], 'aid' => $orderinfo['aid'], 'paytype' => $orderinfo['paytype'], 'transid' => $orderinfo['transid'], 'createtime' => TIMESTAMP, 'status' => 0, 'type' => $type, 'orderid' => $id, 'payfee' => $payfee, 'refundfee' => $money, 'uniacid' => $orderinfo['uniacid'], 'remark' => $remark, 'plugin' => $plugin);
		pdo_insert(PDO_NAME . 'refund_record', $refundRecord);

		if (!in_array($plugin, $pluginArray)) {
			pdo_update(PDO_NAME . 'refund_record', array('errmsg' => '退款订单插件' . $plugin . '错误'), array('orderid' => $orderinfo['id']));
			$errMsg = '退款订单插件' . $plugin . '错误';
		}

		if ($orderinfo['price'] <= 0) {
			pdo_update(PDO_NAME . 'refund_record', array('errmsg' => '退款金额小于0'), array('orderid' => $orderinfo['id']));
			$errMsg = '退款金额小于0';
		}

		if (empty($orderinfo['transid']) && $orderinfo['paytype'] == 2) {
			pdo_update(PDO_NAME . 'refund_record', array('errmsg' => '无微信订单号'), array('orderid' => $orderinfo['id']));
			$errMsg = '微信订单无微信订单号';
		}

		if (empty($errMsg)) {
			if ($orderinfo['paytype'] == 1) {
				$res2 = Member::credit_update_credit2($orderinfo['mid'], $orderinfo['price'], '代理退款', $orderinfo['orderno']);

				if ($res2) {
					$refund = true;
					pdo_update(PDO_NAME . 'refund_record', array('status' => 1), array('orderid' => $orderinfo['id']));
				}
				else {
					$errMsg = '余额更改失败';
					pdo_update(PDO_NAME . 'refund_record', array('errmsg' => $errMsg), array('orderid' => $orderinfo['id']));
				}
			}
			else {
				if (empty($orderinfo['paytype']) || $orderinfo['paytype'] == 5) {
					$_W['uniacid'] = Wxapp::get_wxapp_uniacid($_W['uniacid']);
					pdo_update('core_paylog', array('type' => 'wechat'), array('uniacid' => $_W['uniacid'], 'tid' => $orderinfo['orderno']));
					$refund_id = refund_create_order($orderinfo['orderno'], 'weliam_merchant', $orderinfo['price'], '代理后台退款');

					if (is_error($refund_id)) {
						return $refund_id;
					}

					$res = refund($refund_id);
					$_W['uniacid'] = Wxapp::get_uniacid($_W['uniacid']);
				}
				else {
					$refund_id = refund_create_order($orderinfo['orderno'], 'weliam_merchant', $orderinfo['price'], '代理后台退款');

					if (is_error($refund_id)) {
						return $refund_id;
					}

					$res = refund($refund_id);
				}

				if ($res['refund_id'] && $res['return_code'] == 'SUCCESS' || $res['msg'] == 'Success' && $res['code']) {
					$refund = true;
					pdo_update(PDO_NAME . 'refund_record', array('status' => 1, 'refund_id' => $res['refund_id']), array('orderid' => $orderinfo['id']));

					if ($plugin == 'rush') {
						pdo_update(PDO_NAME . 'rush_order', array('status' => 7), array('id' => $orderinfo['id']));
					}

					SingleMerchant::updateAmount(0 - $orderinfo['price'], $orderinfo['sid'], $orderinfo['id'], 1, '退款：订单号' . $orderinfo['orderno'], $plugin);
					Util::wl_log('refundSuccess', PATH_DATA . $plugin . '/data/log/', $orderinfo);
				}
				else {
					pdo_update(PDO_NAME . 'refund_record', array('errmsg' => $res['err_code_des']), array('orderid' => $orderinfo['id']));
					$errMsg = $res['err_code_des'];

					if (empty($errMsg)) {
						$errMsg = $res['message'];
					}

					Util::wl_log('refundFail', PATH_DATA . $plugin . '/data/log/', $orderinfo);
				}
			}
		}

		if ($refund) {
			$res['status'] = true;
			$res['message'] = '退款成功';
		}
		else {
			if (empty($errMsg)) {
				$errMsg = '未知错误，请联系管理员';
			}

			$res['status'] = false;
			$res['message'] = $errMsg;
		}

		return $res;
	}

	static public function finance($openid = '', $money = 0, $desc = '', $realname = '', $trade_no)
	{
		global $_W;
		$pay = new WeixinPay();
		$arr = $pay->finance($openid, $money, $desc, $realname, $trade_no);
		return $arr;
	}
}

?>
