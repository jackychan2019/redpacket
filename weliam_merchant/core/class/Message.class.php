<?php
//dezend by http://www.sucaihuo.com/
class Message
{
	static public function sendCustomNotice($openid, $msg, $url = '', $account = NULL)
	{
		global $_W;

		if (!$account) {
			load()->model('account');
			$acid = pdo_fetchcolumn('SELECT acid FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid LIMIT 1', array(':uniacid' => $_W['uniacid']));
			$account = WeAccount::create($acid);
		}

		if (!$account || array_key_exists('errno', $account)) {
			return 0;
		}

		$content = '';

		if (is_array($msg)) {
			foreach ($msg as $key => $value) {
				if (!empty($value['title'])) {
					$content .= $value['title'] . ':' . $value['value'] . '
';
				}
				else {
					$content .= $value['value'] . '
';

					if ($key == 0) {
						$content .= '
';
					}
				}
			}
		}
		else {
			$content = $msg;
		}

		if (!empty($url)) {
			$content .= '<a href=\'' . $url . '\'>点击查看详情</a>';
		}

		return $account->sendCustomNotice(array(
			'touser'  => $openid,
			'msgtype' => 'text',
			'text'    => array('content' => urlencode($content))
		));
	}

	static public function sendtplnotice($touser, $template_id, $postdata, $url = '', $account = NULL)
	{
		global $_W;
		load()->model('account');

		if (!$account) {
			if (!empty($_W['acid'])) {
				$account = WeAccount::create($_W['acid']);
			}
			else {
				$acid = pdo_fetchcolumn('SELECT acid FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid LIMIT 1', array(':uniacid' => $_W['uniacid']));
				$result = $account = WeAccount::create($acid);
			}
		}

		if (!$account) {
			return NULL;
		}

		return $account->sendTplNotice($touser, $template_id, $postdata, $url);
	}

	static public function paySuccess($orderid, $type = '')
	{
		global $_W;

		if ($type == 'rush') {
			$order = pdo_get('wlmerchant_rush_order', array('id' => $orderid), array('aid', 'uniacid', 'mid', 'sid', 'activityid', 'num', 'actualprice', 'paytype', 'remark'));
			$_W['uniacid'] = $order['uniacid'];
			$goodsname = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $order['activityid']), 'name');
			$storeurl = app_url('store/supervise/switchstore', array('storeid' => $order['sid'], 'url' => urlencode(app_url('store/supervise/order', array('status' => 1, 'type' => 'rush')))));
			$adminurl = app_url('rush/home/detail', array('id' => $order['activityid']));
			$price = $order['actualprice'];
			$plugin = '抢购';
		}
		else if ($type == 'groupon') {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('aid', 'uniacid', 'mid', 'sid', 'fkid', 'num', 'price', 'paytype', 'remark'));
			$_W['uniacid'] = $order['uniacid'];
			$goodsname = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $order['fkid']), 'name');
			$storeurl = app_url('store/supervise/switchstore', array('storeid' => $order['sid'], 'url' => urlencode(app_url('store/supervise/order', array('status' => 1, 'type' => 'groupon')))));
			$adminurl = app_url('groupon/grouponapp/groupondetail', array('cid' => $order['fkid']));
			$price = $order['price'];
			$plugin = '团购';
		}
		else if ($type == 'wlfightgroup') {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('aid', 'uniacid', 'mid', 'sid', 'fkid', 'num', 'price', 'paytype', 'remark'));
			$_W['uniacid'] = $order['uniacid'];
			$goodsname = pdo_getcolumn(PDO_NAME . 'fightgroup_goods', array('id' => $order['fkid']), 'name');
			$storeurl = app_url('store/supervise/switchstore', array('storeid' => $order['sid'], 'url' => urlencode(app_url('store/supervise/order', array('status' => 1, 'type' => 'wlfightgroup')))));
			$adminurl = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $order['fkid']));
			$price = $order['price'];
			$plugin = '拼团';
		}
		else if ($type == 'coupon') {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('aid', 'uniacid', 'mid', 'sid', 'fkid', 'num', 'price', 'paytype', 'remark'));
			$_W['uniacid'] = $order['uniacid'];
			$goodsname = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $order['fkid']), 'title');
			$storeurl = app_url('store/supervise/switchstore', array('storeid' => $order['sid'], 'url' => urlencode(app_url('store/supervise/order', array('status' => 1, 'type' => 'coupon')))));
			$adminurl = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $order['fkid']));
			$price = $order['price'];
			$plugin = '卡券';
		}
		else {
			if ($type == 'bargain') {
				$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('aid', 'uniacid', 'mid', 'sid', 'fkid', 'num', 'price', 'paytype', 'remark'));
				$_W['uniacid'] = $order['uniacid'];
				$goodsname = pdo_getcolumn(PDO_NAME . 'bargain_activity', array('id' => $order['fkid']), 'name');
				$storeurl = app_url('store/supervise/switchstore', array('storeid' => $order['sid'], 'url' => urlencode(app_url('store/supervise/order', array('status' => 1, 'type' => 'bargain')))));
				$adminurl = app_url('bargain/bargain_app/bargaindetail', array('cid' => $order['fkid']));
				$price = $order['price'];
				$plugin = '砍价';
			}
		}

		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$message = unserialize($settings['message']);
		$member = pdo_get(PDO_NAME . 'member', array('id' => $order['mid']), array('openid', 'nickname'));
		$nickname = $member['nickname'];
		$merchantName = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $order['sid']), 'storename');
		$storeadmins = pdo_fetchall('SELECT mid FROM ' . tablename('wlmerchant_merchantuser') . (' WHERE storeid = ' . $order['sid'] . ' AND ismain IN (1,3) '));
		$first = '您好,用户[' . $nickname . ']购买的[' . $goodsname . ']已支付';
		$keyword1 = $plugin . '商品订单支付';
		$keyword2 = '已付款';
		$remark = '订单金额:' . $price . '元,购买数量:' . $order['num'] . ',请商家注意备货' . '。买家备注:' . $order['remark'];
		VoiceAnnouncements::PushVoiceMessage($price, $order['sid'], $order['paytype']);

		foreach ($storeadmins as $key => $storeadmin) {
			$storeadminopenid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $storeadmin['mid']), 'openid');
			Message::jobNotice($storeadminopenid, $first, $keyword1, $keyword2, $remark, $storeurl);
		}

		$openids = pdo_getall('wlmerchant_agentadmin', array('aid' => $order['aid'], 'notice' => 1), array('openid'));
		$remark = '所属商家:' . $merchantName;

		if ($openids) {
			foreach ($openids as $key => $mem) {
				Message::jobNotice($mem['openid'], $first, $keyword1, $keyword2, $remark, $adminurl);
			}
		}

		$openid = $member['openid'];
		$url = app_url('order/userorder/orderlist', array('status' => 1));

		if (!$notice['submitOrderSwitch']) {
			$msg = '您已成功付款' . '
';
			$msg .= '支付金额：￥' . $price . '
';
			$msg .= '商品信息：' . $goodsname . '
';
			return self::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'            => array('value' => '您的订单已经成功付款', 'color' => '#173177'),
			'orderMoneySum'    => array('value' => '￥ ' . $price, 'color' => '#173177'),
			'orderProductName' => array('value' => $goodsname, 'color' => '#173177'),
			'Remark'           => array('value' => '点击可查看订单详情，如有疑问请联系客服。', 'color' => '#173177')
		);
		return self::sendtplnotice($openid, $notice['submitOrder'], $postdata, $url);
	}

	static public function paySuccess2($openid, $price, $goodsname)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$message = unserialize($settings['message']);
		$url = app_url('order/userorder/orderlist', array('status' => 1));

		if (!$notice['submitOrderSwitch']) {
			$msg = '您已成功付款' . '
';
			$msg .= '支付金额：￥' . $price . '
';
			$msg .= '商品信息：' . $goodsname . '
';
			return self::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'            => array('value' => '您的订单已经成功付款', 'color' => '#173177'),
			'orderMoneySum'    => array('value' => '￥ ' . $price, 'color' => '#173177'),
			'orderProductName' => array('value' => $goodsname, 'color' => '#173177'),
			'Remark'           => array('value' => '点击可查看订单详情，如有疑问请联系客服。', 'color' => '#173177')
		);
		return self::sendtplnotice($openid, $notice['submitOrder'], $postdata, $url);
	}

	static public function checkMessage($openid)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$message = unserialize($settings['message']);

		if (!$notice['MobileSwitch']) {
			return '未开启改模板消息';
		}

		$where2['openid'] = $openid;
		$member = Util::getSingelData('nickname,mobile', 'wlmerchant_member', $where2);
		$postdata = array(
			'first'    => array('value' => '尊敬的' . $member['nickname'] . '用户，恭喜您绑定手机成功。', 'color' => '#173177'),
			'keyword1' => array('value' => $member['mobile'], 'color' => '#173177'),
			'keyword2' => array('value' => date('Y年m月d日', time()), 'color' => '#173177'),
			'remark'   => array('value' => '请知悉并确定这是您本人的操作。', 'color' => '#173177')
		);
		return self::sendtplnotice($openid, $notice['Mobile'], $postdata, '');
	}

	static public function Halfhexiao($openid, $num, $goodsname, $url = '', $first)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$message = unserialize($settings['message']);

		if (empty($url)) {
			$url = app_url('order/userorder/orderlist', array('status' => 2));
		}

		if (!$notice['hexiaoSwitch']) {
			$msg = $first . '
';
			$msg .= '商品名称：' . $goodsname . '买单特权' . '
';
			$msg .= '商品数量：' . $num . '
';
			$msg .= '核销时间：' . date('Y年m月d日 H:i:s', time()) . '
';
			return self::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => $first, 'color' => '#173177'),
			'keyword1' => array('value' => $goodsname . '买单特权', 'color' => '#173177'),
			'keyword2' => array('value' => $num, 'color' => '#173177'),
			'keyword3' => array('value' => date('Y年m月d日 H:i:s', time()), 'color' => '#173177'),
			'remark'   => array('value' => '点击可查看订单详情，如有疑问请联系客服。', 'color' => '#173177')
		);
		return self::sendtplnotice($openid, $notice['hexiao'], $postdata, $url);
	}

	static public function hexiaoSuccess($openid, $num, $goodsname, $url = '')
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$message = unserialize($settings['message']);

		if (empty($url)) {
			$url = app_url('order/userorder/orderlist', array('status' => 2));
		}

		if (!$notice['hexiaoSwitch']) {
			$msg = '您好，您的商品已经成功核销' . '
';
			$msg .= '商品名称：' . $goodsname . '
';
			$msg .= '商品数量：' . $num . '
';
			$msg .= '核销时间：' . date('Y年m月d日 H:i:s', time()) . '
';
			return self::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => '您好，您的商品已经成功核销', 'color' => '#173177'),
			'keyword1' => array('value' => $goodsname, 'color' => '#173177'),
			'keyword2' => array('value' => $num, 'color' => '#173177'),
			'keyword3' => array('value' => date('Y年m月d日 H:i:s', time()), 'color' => '#173177'),
			'remark'   => array('value' => '点击可查看订单详情，如有疑问请联系客服。', 'color' => '#173177')
		);
		return self::sendtplnotice($openid, $notice['hexiao'], $postdata, $url);
	}

	static public function hexiaoTover($openid, $num, $goodsname)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$message = unserialize($settings['message']);
		$url = app_url('dashboard/home/index');

		if (!$notice['hexiaoSwitch']) {
			$msg = '核销成功' . '
';
			$msg .= '商品名称：' . $goodsname . '
';
			$msg .= '商品数量：' . $num . '
';
			$msg .= '核销时间：' . date('Y年m月d日 H:i:s', time()) . '
';
			return self::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => '核销成功', 'color' => '#173177'),
			'keyword1' => array('value' => $goodsname, 'color' => '#173177'),
			'keyword2' => array('value' => $num, 'color' => '#173177'),
			'keyword3' => array('value' => date('Y年m月d日 H:i:s', time()), 'color' => '#173177'),
			'remark'   => array('value' => '点击返回商城首页', 'color' => '#173177')
		);
		return self::sendtplnotice($openid, $notice['hexiao'], $postdata, $url);
	}

	static public function settledSuccess($openid, $storename, $flag)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$message = unserialize($settings['message']);
		$url = app_url('store/storeManage/enter');

		if (!$notice['settledSwitch']) {
			$msg = '您好，您的商家入驻审核已完成' . '
';
			$msg .= '入驻商家：' . $storename . '
';

			if ($flag == 'pass') {
				$msg .= '审核：审核通过' . '
';
				$msg .= '请您尽快完善商家信息' . '
';
			}
			else {
				$msg .= '审核：申请驳回' . '
';
				$msg .= '点击查看驳回原因' . '
';
			}

			return self::sendCustomNotice($openid, $msg, $url);
		}

		if ($flag == 'pass') {
			$postdata = array(
				'first'    => array('value' => '您好，您的商家入驻审核已完成', 'color' => '#173177'),
				'keyword1' => array('value' => $storename, 'color' => '#173177'),
				'keyword2' => array('value' => '审核通过', 'color' => '#173177'),
				'remark'   => array('value' => '请您尽快完善商家信息。', 'color' => '#173177')
			);
		}
		else {
			$postdata = array(
				'first'    => array('value' => '您好，您的商家入驻审核已完成', 'color' => '#173177'),
				'keyword1' => array('value' => $storename, 'color' => '#173177'),
				'keyword2' => array('value' => '申请驳回', 'color' => '#173177'),
				'remark'   => array('value' => '点击查看驳回原因。', 'color' => '#173177')
			);
		}

		return self::sendtplnotice($openid, $notice['settled'], $postdata, $url);
	}

	static public function settledtoadmin($storeid)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$message = unserialize($settings['message']);
		$store = pdo_get(PDO_NAME . 'merchantdata', array('id' => $storeid), array('storename', 'aid'));
		$storename = $store['storename'];
		$appname = pdo_getcolumn(PDO_NAME . 'merchantuser', array('storeid' => $storeid, 'ismain' => 1), 'name');
		$url = app_url('store/storeManage/adminpage', array('appstoreid' => $storeid));
		$openids = pdo_getall('wlmerchant_agentadmin', array('aid' => $_W['aid'], 'notice' => 1), array('openid'));

		if ($openids) {
			foreach ($openids as $key => $member) {
				self::toadmin($member['openid'], $notice, $storename, $appname, $url);
			}
		}
	}

	static public function toadmin($openid, $notice, $storename, $appname, $url)
	{
		if (!$notice['settledSwitch']) {
			$msg = $appname . '申请了店铺：' . $storename . '的入驻' . '
';
			$msg .= '快去审核吧！' . '
';
			return Message::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => '您好,' . $appname . '申请了新商家入驻', 'color' => '#173177'),
			'keyword1' => array('value' => $storename, 'color' => '#173177'),
			'keyword2' => array('value' => '待审核', 'color' => '#173177'),
			'remark'   => array('value' => '请您尽快审核商家资料。', 'color' => '#173177')
		);
		return self::sendtplnotice($openid, $notice['settled'], $postdata, $url);
	}

	static public function refundNotice($openid, $reason, $money, $url)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$message = unserialize($settings['message']);

		if (!$notice['refundNoticeSwitch']) {
			$msg = '您好，您的订单已经退款' . '
';
			$msg .= '退款原因：' . $reason . '
';
			$msg .= '退款金额：' . $money . '
';
			return self::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'  => array('value' => '您好，您的订单已经退款', 'color' => '#173177'),
			'reason' => array('value' => $reason, 'color' => '#173177'),
			'refund' => array('value' => $money, 'color' => '#173177'),
			'remark' => array('value' => '点击重新购买，如有疑问请联系客服。', 'color' => '#173177')
		);
		return self::sendtplnotice($openid, $notice['refundNotice'], $postdata, $url);
	}

	static public function jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url = '')
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$message = unserialize($settings['message']);

		if (!$notice['jobSwitch']) {
			$msg = $first . '
';
			$msg .= $remark . '
';
			return self::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => $first, 'color' => '#173177'),
			'keyword1' => array('value' => $keyword1, 'color' => '#173177'),
			'keyword2' => array('value' => $keyword2, 'color' => '#173177'),
			'keyword3' => array('value' => date('Y-m-d H:i:s', time()), 'color' => '#173177'),
			'remark'   => array('value' => $remark, 'color' => '#173177')
		);
		return self::sendtplnotice($openid, $notice['jobnotice'], $postdata, $url);
	}

	static public function cutoffFollow($id, $mid, $orderid, $sid)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$where2['id'] = $mid;
		$member = Util::getSingelData('nickname,openid', 'wlmerchant_member', $where2);
		$goods = Rush::getSingleActive($id, 'name,cutofftime,cutoffstatus,cutoffday');
		$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $sid), 'storename');
		$url = app_url('order/userorder/orderdetail', array('orderid' => $orderid, 'type' => 'rush'));
		$order = pdo_get(PDO_NAME . 'rush_order', array('id' => $orderid), array('orderno', 'num', 'paytime'));

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
			return self::sendCustomNotice($member['openid'], $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => '您好，您有即将过期的待消费订单。', 'color' => '#173177'),
			'keyword1' => array('value' => $order['orderno'], 'color' => '#173177'),
			'keyword2' => array('value' => $goods['name'], 'color' => '#173177'),
			'keyword3' => array('value' => $order['num'], 'color' => '#173177'),
			'keyword4' => array('value' => date('Y年m月d日 H:i:s', $cutofftime), 'color' => '#173177'),
			'remark'   => array('value' => '点击立即参加抢购活动，赶快行动吧。', 'color' => '#173177')
		);
		return self::sendtplnotice($member['openid'], $notice['overtime'], $postdata, $url);
	}

	static public function groupresult($openid, $groupid, $flag)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$order = pdo_fetch('SELECT * FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND fightgroupid = ' . $groupid . ' ORDER BY createtime ASC'));
		$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('nickname'));
		$good = pdo_get('wlmerchant_fightgroup_goods', array('id' => $order['fkid']), array('name'));
		$url = app_url('wlfightgroup/fightapp/groupdetail', array('id' => $groupid));

		if ($flag == 1) {
			if (!$notice['groupresultSwitch']) {
				$msg = '恭喜您，您参加的拼团已组团成功' . '
';
				$msg .= '商品名称：' . $good['name'] . '
';
				$msg .= '团长：' . $member['nickname'] . '
';
				return Message::sendCustomNotice($openid, $msg, $url);
			}

			$postdata = array(
				'first'              => array('value' => '恭喜您，您参加的拼团已组团成功', 'color' => '#173177'),
				'Pingou_ProductName' => array('value' => $good['name'], 'color' => '#173177'),
				'Weixin_ID'          => array('value' => $member['nickname'], 'color' => '#173177'),
				'Remark'             => array('value' => '点击查看团详情，如有疑问请联系客服。', 'color' => '#173177')
			);
			return Message::sendtplnotice($openid, $notice['groupresult'], $postdata, $url);
		}

		if (!$notice['groupresultSwitch']) {
			$msg = '抱歉，您参加的拼团组团失败' . '
';
			$msg .= '商品名称：' . $good['name'] . '
';
			$msg .= '团长：' . $member['nickname'] . '
';
			return Message::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'              => array('value' => '抱歉，您参加的拼团组团失败', 'color' => '#173177'),
			'Pingou_ProductName' => array('value' => $good['name'], 'color' => '#173177'),
			'Weixin_ID'          => array('value' => $member['nickname'], 'color' => '#173177'),
			'Remark'             => array('value' => '点击查看团详情，如有疑问请联系客服。', 'color' => '#173177')
		);
		return Message::sendtplnotice($openid, $notice['groupresult'], $postdata, $url);
	}

	static public function sendremind($orderid, $plugin)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);

		if ($plugin == 'b') {
			$order = pdo_get(PDO_NAME . 'rush_order', array('id' => $orderid));
			$type = 'rush';
		}
		else {
			$order = pdo_get(PDO_NAME . 'order', array('id' => $orderid));
			$type = $order['plugin'];
		}

		$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('openid'));
		$express = pdo_get('wlmerchant_express', array('id' => $order['expressid']));
		$url = app_url('order/userorder/orderdetail', array('orderid' => $order['id'], 'type' => $type));

		if (!$notice['sendremindSwitch']) {
			$msg = '亲，宝贝已经启程了，好想快点来到你身边' . '
';
			$msg .= '订单编号：' . $order['orderno'] . '
';
			$msg .= '物流公司：' . $express['expressname'] . '
';
			$msg .= '物流单号：' . $express['expresssn'] . '
';
			return Message::sendCustomNotice($member['openid'], $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => '亲，宝贝已经启程了，好想快点来到你身边', 'color' => '#173177'),
			'keyword1' => array('value' => $order['orderno'], 'color' => '#173177'),
			'keyword2' => array('value' => $express['expressname'], 'color' => '#173177'),
			'keyword3' => array('value' => $express['expresssn'], 'color' => '#173177'),
			'remark'   => array('value' => '点击查看订单详情，如有疑问请联系客服。', 'color' => '#173177')
		);
		return Message::sendtplnotice($member['openid'], $notice['sendremind'], $postdata, $url);
	}

	static public function signNotice($openid, $nickname, $times, $url)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);

		if (empty($notice['signSuccessSwitch'])) {
			$msg = '恭喜您签到成功，信息如下' . '
';
			$msg .= '用户名：' . $nickname . '
';
			$msg .= '连续签到次数：' . $times . '
';
			$msg .= '坚持签到有好礼赠送哦' . '
';
			return Message::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => '恭喜您签到成功，信息如下', 'color' => '#173177'),
			'keyword1' => array('value' => $nickname, 'color' => '#173177'),
			'keyword2' => array('value' => $times, 'color' => '#173177'),
			'remark'   => array('value' => '坚持签到有好礼赠送哦。', 'color' => '#173177')
		);
		return Message::sendtplnotice($openid, $notice['signSuccess'], $postdata, $url);
	}

	static public function openSuccessNotice($openid, $cardname, $time, $mobile, $url)
	{
		global $_W;
		$settings = Setting::wlsetting_read('halfcard');
		$halftext = $settings['text']['halfcardtext'] ? $settings['text']['halfcardtext'] : '一卡通';
		$tqtext = $settings['text']['privilege'] ? $settings['text']['privilege'] : '特权';
		$sets = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($sets['notice']);

		if (!$notice['OpenHalfcardSwitch']) {
			$msg = '您已成功开通' . $halftext . $tqtext . '
';
			$msg .= '开通账号：' . $mobile . '
';
			$msg .= '开通商品：' . $cardname . '
';
			$msg .= '到期时间：' . $time . '
';
			return Message::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => '您已成功开通' . $halftext . $tqtext, 'color' => '#173177'),
			'keyword1' => array('value' => $mobile, 'color' => '#173177'),
			'keyword2' => array('value' => $cardname, 'color' => '#173177'),
			'keyword3' => array('value' => $time, 'color' => '#173177'),
			'remark'   => array('value' => '点击查看我的' . $halftext . '，如有疑问请联系客服。', 'color' => '#173177')
		);
		return Message::sendtplnotice($openid, $notice['OpenHalfcard'], $postdata, $url);
	}

	static public function openNoticeAdmin($nickname, $cardname, $time, $mobile)
	{
		global $_W;
		$settings = Setting::wlsetting_read('halfcard');
		$halftext = $settings['text']['halfcardtext'] ? $settings['text']['halfcardtext'] : '一卡通';
		$tqtext = $settings['text']['privilege'] ? $settings['text']['privilege'] : '特权';
		$sets = Setting::wlsetting_read('noticeMessage');
		$openid = $sets['adminopenid'];
		$notice = unserialize($sets['notice']);

		if (!$notice['OpenHalfcardSwitch']) {
			$msg = '客户:[' . $nickname . ']已成功开通' . $halftext . $tqtext . '
';
			$msg .= '开通账号：' . $mobile . '
';
			$msg .= '开通商品：' . $cardname . '
';
			$msg .= '到期时间：' . $time . '
';
			return Message::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => '客户:[' . $nickname . ']已成功开通' . $halftext . $tqtext, 'color' => '#173177'),
			'keyword1' => array('value' => $mobile, 'color' => '#173177'),
			'keyword2' => array('value' => $cardname, 'color' => '#173177'),
			'keyword3' => array('value' => $time, 'color' => '#173177'),
			'remark'   => array('value' => '可以在后台查看用户详情', 'color' => '#173177')
		);
		return Message::sendtplnotice($openid, $notice['OpenHalfcard'], $postdata);
	}
}


?>
