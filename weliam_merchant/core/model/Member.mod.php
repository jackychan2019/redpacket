<?php
//dezend by http://www.sucaihuo.com/
class Member
{
	static public function wl_member_auth($userinfo = '')
	{
		global $_W;
		global $_GPC;
		load()->model('mc');
		if (empty($userinfo) || !is_array($userinfo)) {
			return false;
		}

		if ($userinfo['mobile'] && $userinfo['pwd']) {
			$member = pdo_get('wlmerchant_member', array('mobile' => $userinfo['mobile'], 'password' => $userinfo['pwd'], 'uniacid' => $_W['uniacid']));
		}
		else if ($userinfo['openid']) {
			$member = pdo_get('wlmerchant_member', array('openid' => $userinfo['openid'], 'uniacid' => $_W['uniacid']));
		}
		else if ($userinfo['unionid']) {
			$member = pdo_get('wlmerchant_member', array('unionid' => $userinfo['unionid'], 'uniacid' => $_W['uniacid']));
		}
		else {
			if ($userinfo['tokey']) {
				$member = pdo_get('wlmerchant_member', array('tokey' => $userinfo['tokey'], 'uniacid' => $_W['uniacid']));
			}
		}

		if (empty($member)) {
			return false;
		}

		$upgrade = array('dotime' => time());
		$fansinfo = mc_oauth_userinfo();

		if (!empty($fansinfo)) {
			$uid = 0;

			if ($_W['fans']['follow'] == 1) {
				$uid = mc_openid2uid($fansinfo['openid']);
			}

			if (empty($uid)) {
				$uid = $member['uid'];
			}

			if (!empty($uid)) {
				if (0 < $member['credit1']) {
					mc_credit_update($uid, 'credit1', $member['credit1']);
					$upgrade['credit1'] = 0;
				}

				if (0 < $member['credit2']) {
					mc_credit_update($uid, 'credit2', $member['credit2']);
					$upgrade['credit2'] = 0;
				}

				if ($member['uid'] != $uid) {
					$upgrade['uid'] = $uid;
				}
			}

			if (empty($member['tokey'])) {
				$upgrade['tokey'] = strtoupper(MD5(sha1(time() . random(12))));
			}

			if ($fansinfo['nickname'] != $member['nickname']) {
				$upgrade['nickname'] = $fansinfo['nickname'];
			}

			if ($fansinfo['avatar'] != $member['avatar']) {
				$upgrade['avatar'] = str_replace('132132', '132', $fansinfo['avatar']);
			}

			if ($fansinfo['sex'] != $member['gender']) {
				$upgrade['gender'] = $fansinfo['sex'];
			}

			if ($fansinfo['unionid'] != $member['unionid']) {
				$upgrade['unionid'] = $fansinfo['unionid'];
			}
		}

		if (!empty($upgrade)) {
			pdo_update(PDO_NAME . 'member', $upgrade, array('id' => $member['id']));
			$member = array_merge($member, $upgrade);
		}

		if ($member['uid']) {
			$credit = pdo_get('mc_members', array('uid' => $member['uid']), array('credit1', 'credit2'));
			$member['credit1'] = $credit['credit1'];
			$member['credit2'] = $credit['credit2'];
		}

		if ($member['distributorid']) {
			pdo_update('wlmerchant_distributor', array('mobile' => $member['mobile'], 'nickname' => $member['nickname'], 'realname' => $member['realname']), array('mid' => $member['id']));
		}

		if (p('distribution')) {
			$disbase = Setting::wlsetting_read('distribution');

			if ($disbase['appdis'] == 4) {
				if ($member['distributorid']) {
					$res = pdo_update('wlmerchant_distributor', array('disflag' => 1), array('id' => $member['distributorid']));
				}
				else {
					$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $member['id'], 'createtime' => time(), 'disflag' => 1, 'nickname' => $member['nickname'], 'mobile' => $member['mobile'], 'realname' => $member['realname'], 'leadid' => 0);
					$res = pdo_insert('wlmerchant_distributor', $data);
					$disid = pdo_insertid();
					pdo_update('wlmerchant_member', array('distributorid' => $disid), array('id' => $member['id']));
				}

				if ($res) {
					$url = app_url('distribution/disappbase/index');
					Distribution::distriNotice($member['openid'], $url, 1);
				}
			}
		}

		return $member;
	}

	static public function mc_init_fans_info($openid)
	{
		global $_W;
		$member = self::getMemberByMid($openid);

		if (empty($member)) {
			load()->model('mc');
			$fansinfo = mc_fansinfo($openid);
			$member = array('uniacid' => $_W['uniacid'], 'openid' => $openid, 'nickname' => $fansinfo['nickname'], 'avatar' => $fansinfo['avatar'], 'createtime' => time());
			pdo_insert(PDO_NAME . 'member', $member);
			$member['id'] = pdo_insertid();
		}

		return $member;
	}

	static public function getMemberByMid($id, $arr = '')
	{
		global $_W;

		if (is_array($id)) {
			$re = pdo_get(PDO_NAME . 'member', array_merge($id, array('uniacid' => $_W['uniacid'])), $arr);
		}
		else {
			$flag = intval($id);

			if (empty($flag)) {
				$re = pdo_get(PDO_NAME . 'member', array('uniacid' => $_W['uniacid'], 'openid' => $id), $arr);
			}
			else {
				$re = pdo_get(PDO_NAME . 'member', array('uniacid' => $_W['uniacid'], 'id' => $id), $arr);
			}
		}

		if (!empty($re['uid'])) {
			load()->model('mc');
			$credits = pdo_get('mc_members', array('uid' => $re['uid']), array('credit1', 'credit2'));
			$re['credit1'] = $credits['credit1'];
			$re['credit2'] = $credits['credit2'];
		}

		return $re;
	}

	static public function credit_update_credit1($mid, $credit1 = 0, $remark = '', $orderno = '')
	{
		global $_W;
		$member = self::getMemberByMid($mid);
		$settings = Cloud::wl_syssetting_read('base');

		if (empty($member)) {
			return error(-1, '用户不存在');
		}

		if ($member['credit1'] + $credit1 < 0) {
			return error(-1, '用户积分不足');
		}

		if (empty($member['uid'])) {
			pdo_update(PDO_NAME . 'member', array('credit1' => $info['credit1'] + $credit1), array('id' => $member['id']));
		}
		else {
			load()->model('mc');

			if (empty($remark)) {
				$remark = $settings['name'] ? $settings['name'] . '积分操作' : '智慧城市积分操作';
			}

			mc_credit_update($member['uid'], 'credit1', $credit1, array($member['uid'], $remark, 'weliam_merchant'));
		}

		$data = array('uid' => $member['uid'], 'uniacid' => $_W['uniacid'], 'mid' => $member['id'], 'num' => $credit1, 'createtime' => TIMESTAMP, 'type' => 1, 'remark' => $remark, 'ordersn' => $orderno);
		pdo_insert(PDO_NAME . 'creditrecord', $data);
		return true;
	}

	static public function credit_update_credit2($mid, $credit2 = 0, $remark = '', $orderno = '')
	{
		global $_W;
		$member = self::getMemberByMid($mid);
		$settings = Cloud::wl_syssetting_read('base');

		if (empty($member)) {
			return error(-1, '用户不存在');
		}

		if ($member['credit2'] + $credit2 < 0) {
			return error(-1, '用户余额不足');
		}

		if (empty($member['uid'])) {
			pdo_update(PDO_NAME . 'member', array('credit2' => $member['credit2'] + $credit2), array('id' => $member['id']));
		}
		else {
			load()->model('mc');
			$header = $remark ? $settings['name'] . ':' : '智慧城市余额操作';
			mc_credit_update($member['uid'], 'credit2', $credit2, array($member['uid'], $header . $remark, 'weliam_merchant'));
		}

		$data = array('uid' => $member['uid'], 'uniacid' => $_W['uniacid'], 'mid' => $member['id'], 'num' => $credit2, 'createtime' => TIMESTAMP, 'type' => 2, 'remark' => $remark, 'ordersn' => $orderno);
		pdo_insert(PDO_NAME . 'creditrecord', $data);
		return true;
	}

	/**
     * 判断会员VIP情况
     *
     * @access public
     * @name 方法名称
     * @param mixed  参数一的说明
     * @return array
     */
	static public function vip($id)
	{
		global $_W;
		$vipInfo = Util::getSingelData(' lastviptime ', PDO_NAME . 'member', array('uniacid' => $_W['uniacid'], 'id' => $id));
		$viptime = $vipInfo['lastviptime'] - time();

		if ($viptime < 0) {
			pdo_update(PDO_NAME . 'member', array('vipstatus' => 0, 'vipleveldays' => 0), array('id' => $id));
			return false;
		}

		$vipleveltime = floor($viptime / (24 * 60 * 60));
		pdo_update(PDO_NAME . 'member', array('vipstatus' => 1, 'vipleveldays' => $vipleveltime), array('id' => $id));
		return $vipInfo['lastviptime'];
	}

	static public function shareAddress()
	{
		global $_W;
		global $_GPC;
		$appid = $_W['account']['key'];
		$secret = $_W['account']['secret'];
		load()->func('communication');
		$url = $_W['siteroot'] . 'app/index.php?' . $_SERVER['QUERY_STRING'];

		if (empty($_GPC['code'])) {
			$oauth2_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($url) . '&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
			header('location: ' . $oauth2_url);
			exit();
		}

		$code = $_GPC['code'];
		$token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';
		$resp = ihttp_get($token_url);
		$token = @json_decode($resp['content'], true);
		if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
			return false;
		}

		$package = array('appid' => $appid, 'url' => $url, 'timestamp' => time() . '', 'noncestr' => random(8, true) . '', 'accesstoken' => $token['access_token']);
		ksort($package, SORT_STRING);
		$addrSigns = array();

		foreach ($package as $k => $v) {
			$addrSigns[] = $k . '=' . $v;
		}

		$string = implode('&', $addrSigns);
		$addrSign = strtolower(sha1(trim($string)));
		$data = array('appId' => $appid, 'scope' => 'jsapi_address', 'signType' => 'sha1', 'addrSign' => $addrSign, 'timeStamp' => $package['timestamp'], 'nonceStr' => $package['noncestr']);
		return $data;
	}

	static public function checklogin($url = '')
	{
		global $_W;

		if (empty($_W['mid'])) {
			header('location:' . app_url('member/user/signin', array('backurl' => urlencode($url))));
			exit();
		}
	}

	static public function checkhalfmember($url = '')
	{
		global $_W;

		if (empty($_W['mid'])) {
			header('location:' . app_url('member/user/signin', array('backurl' => urlencode($url))));
			exit();
		}

		$now = time();

		if ($_W['wlsetting']['halfcard']['halfcardtype'] == 2) {
			$halfcardflag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $_W['mid'] . ' AND aid = ' . $_W['aid'] . ' AND expiretime > ' . $now . ' AND disable != 1'));
		}
		else {
			$halfcardflag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $_W['mid'] . ' AND expiretime > ' . $now . ' AND disable != 1'));
		}

		return $halfcardflag;
	}

	static public function payChargeNotify($params)
	{
		global $_W;
		Util::wl_log('payResult_notify', PATH_DATA . 'merchant/data/', $params);
		$order_out = pdo_get(PDO_NAME . 'order', array('orderno' => $params['tid']));
		$_W['uniacid'] = $order_out['uniacid'];

		if ($order_out['status'] == 0) {
			$data = self::getVipPayData($params);

			if ($data['status'] == 1) {
				$data['status'] = 3;
			}

			pdo_update(PDO_NAME . 'order', $data, array('orderno' => $params['tid']));
			$res1 = self::credit_update_credit2($order_out['mid'], $order_out['price'], '余额充值', $order_out['orderno']);
			$settings = Setting::wlsetting_read('recharge');
			$count = count($settings['kilometre']);
			$i = 0;

			while ($i < $count) {
				$array[$i]['kilometre'] = $settings['kilometre'][$i];
				$array[$i]['kilmoney'] = $settings['kilmoney'][$i];
				++$i;
			}

			$give = 0;

			foreach ($array as $key => $val) {
				$dos[$key] = $val['kilometre'];
			}

			array_multisort($dos, SORT_ASC, $array);

			foreach ($array as $key => $ar) {
				if ($ar['kilometre'] < $order_out['price'] || $order_out['price'] == $ar['kilometre']) {
					$give = $ar['kilmoney'];
				}
			}

			if (0 < $give) {
				$res2 = self::credit_update_credit2($order_out['mid'], $give, '余额充值赠送', $order_out['orderno']);
			}
		}
	}

	static public function payChargeReturn($params)
	{
		wl_message('充值成功', app_url('member/user/index'), 'success');
	}

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

		$paytype = array('credit' => 1, 'wechat' => 2, 'alipay' => 3, 'delivery' => 4);
		$data['paytype'] = $paytype[$params['type']];

		if ($params['type'] == 'wechat') {
			$data['transid'] = $params['tag']['transaction_id'];
		}

		$data['paytime'] = TIMESTAMP;
		return $data;
	}

	/**
     * Comment: 合并用户的账号
     * Author: zzw
     * @param bool $type 类型 等于true用电话号码为关键   等于false用unionid为关键  默认用电话
     * @param $keyval   合并关键内容的值  手机号 || unionid
     * @return bool
     */
	static public function AccountMerging($keyval, $type = true)
	{
		global $_W;

		if (empty($keyval)) {
			return false;
		}

		if ($type) {
			$info = pdo_getall(PDO_NAME . 'member', array('uniacid' => $_W['uniacid'], 'mobile' => $keyval), '', '', 'createtime');
		}
		else {
			$info = pdo_getall(PDO_NAME . 'member', array('uniacid' => $_W['uniacid'], 'unionid' => $keyval), '', '', 'createtime');
		}

		if (2 <= count($info)) {
			$earliest = $info[0];

			foreach ($info as $key => $val) {
				if ($val['id'] != $earliest['id']) {
					if (file_exists(PATH_MODULE . 'wbh5180.log')) {
						unset($earliest['openid']);
					}

					if ($val['openid'] && !$earliest['openid']) {
						$updateOneInfo['openid'] = $val['openid'];
					}

					if ($val['wechat_openid'] && !$earliest['wechat_openid']) {
						$updateOneInfo['wechat_openid'] = $val['wechat_openid'];
					}

					if ($val['webapp_openid'] && !$earliest['webapp_openid']) {
						$updateOneInfo['webapp_openid'] = $val['webapp_openid'];
					}

					if ($val['unionid'] && !$earliest['unionid']) {
						$updateOneInfo['unionid'] = $val['unionid'];
					}

					if ($val['mobile'] && !$earliest['mobile']) {
						$updateOneInfo['mobile'] = $val['mobile'];
					}

					if (1 <= count($updateOneInfo)) {
						pdo_update(PDO_NAME . 'member', $updateOneInfo, array('id' => $earliest['id']));
					}

					pdo_update(PDO_NAME . 'order', array('mid' => $earliest['id']), array('mid' => $val['id']));
					pdo_update(PDO_NAME . 'rush_order', array('mid' => $earliest['id']), array('mid' => $val['id']));
					pdo_update(PDO_NAME . 'disorder', array('buymid' => $earliest['id']), array('buymid' => $val['id']));

					if (empty($earliest['distributorid'])) {
						$earliest['distributorid'] = $val['distributorid'];
					}
					else {
						if (0 < $val['distributorid']) {
							$eardis = pdo_get('wlmerchant_distributor', array('id' => $earliest['distributorid']), array('dismoney', 'nowmoney', 'mid'));
							$valdis = pdo_get('wlmerchant_distributor', array('id' => $val['distributorid']), array('dismoney', 'nowmoney', 'mid'));

							if (0 < $valdis['dismoney']) {
								$newdismoney = sprintf('%.2f', $eardis['dismoney'] + $valdis['dismoney']);
								pdo_update('wlmerchant_distributor', array('dismoney' => $newdismoney), array('id' => $earliest['distributorid']));
							}

							if (0 < $valdis['nowmoney']) {
								$newnowmoney = sprintf('%.2f', $eardis['nowmoney'] + $valdis['nowmoney']);
								pdo_update('wlmerchant_distributor', array('nowmoney' => $newnowmoney), array('id' => $earliest['distributorid']));
							}

							pdo_update('wlmerchant_distributor', array('leadid' => $earliest['id']), array('leadid' => $val['id']));
							pdo_delete('wlmerchant_distributor', array('id' => $val['distributorid']));
							pdo_update('wlmerchant_disorder', array('oneleadid' => $earliest['distributorid']), array('oneleadid' => $val['distributorid'], 'status' => 0));
							pdo_update('wlmerchant_disorder', array('twoleadid' => $earliest['distributorid']), array('twoleadid' => $val['distributorid'], 'status' => 0));
							pdo_update('wlmerchant_disapply', array('mid' => $earliest['id'], 'disid' => $earliest['distributorid']), array('mid' => $val['id']));
						}
					}

					pdo_delete(PDO_NAME . 'member', array('id' => $val['id']));
				}
			}

			return true;
		}

		return false;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
