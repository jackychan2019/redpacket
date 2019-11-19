<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Bargain_app_WeliamController extends Weliam_merchantModuleSite
{
	public function bargainlist()
	{
		global $_W;
		global $_GPC;

		if ($_W['agentset']['diypageset']['page_bargain']) {
			header('Location:' . app_url('diypage/diyhome/home', array('aid' => $_W['aid'], 'id' => $_W['agentset']['diypageset']['page_bargain'])));
			exit();
		}

		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '砍价列表 - ' . $_W['wlsetting']['base']['name'] : '砍价列表';
		$advs = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_adv') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND enabled = 1 AND type = 9 AND aid = ' . $_W['aid'] . ' ORDER BY displayorder DESC'));
		$set = Setting::agentsetting_read('bargainset');
		$searchSet = array('search_float' => $set['search_float'], 'search_bgColor' => $set['search_bgColor']);

		if (p('diypage')) {
			if (!empty($_W['agentset']['diypageset']['adv_bargain'])) {
				$advdata = Diy::BeOverdue($_W['agentset']['diypageset']['adv_bargain']);
			}

			if (!empty($_W['agentset']['diypageset']['menu_bargain'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_bargain']);
			}
		}

		$page_type = 7;
		include wl_template('bargainhtml/bargainlist');
	}

	public function getindexlist()
	{
		global $_W;
		global $_GPC;
		$lng = !empty($_GPC['lng']) ? $_GPC['lng'] : $_SESSION['lng'];
		$lat = !empty($_GPC['lat']) ? $_GPC['lat'] : $_SESSION['lat'];
		$limlt = $_W['agentset']['pluginlist']['kjlimit'] ? $_W['agentset']['pluginlist']['kjlimit'] : 20;
		$bargainlist = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_bargain_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status IN (1,2) LIMIT 0,' . $limlt . ' '));

		foreach ($bargainlist as $key => &$v) {
			$store = pdo_get('wlmerchant_merchantdata', array('id' => $v['sid']), array('storename', 'logo', 'location'));
			$location = unserialize($store['location']);
			$v['distance'] = Store::getdistance($lng, $lat, $location['lng'], $location['lat']);
			$v['href'] = app_url('bargain/bargain_app/bargaindetail', array('cid' => $v['id'], 'userid' => ''));
			$v['thumb'] = tomedia($v['thumb']);
			$v['pv'] = $v['pv'] + $v['falselooknum'];

			if (99 < $v['price']) {
				$v['price'] = sprintf('%.0f', $v['price']);
				$v['oldprice'] = sprintf('%.0f', $v['oldprice']);
			}
		}

		$plugin = Setting::agentsetting_read('pluginlist');

		switch ($plugin['kjsort']) {
		case '1':
			$sort_key = 'createtime';
			$sort_order = SORT_DESC;
			break;

		case '2':
			$sort_key = 'distance';
			$sort_order = SORT_ASC;
			break;

		case '3':
			$sort_key = 'sort';
			$sort_order = SORT_DESC;
			break;

		case '4':
			$sort_key = 'pv';
			$sort_order = SORT_DESC;
			break;

		default:
			$sort_key = 'distance';
			$sort_order = SORT_ASC;
			break;
		}

		$bargainlist = Store::wl_sort($bargainlist, $sort_key, $sort_order);
		$limit = $plugin['kjlimit'] ? $plugin['kjlimit'] : 10;
		exit(json_encode($bargainlist));
	}

	public function mybargain()
	{
		global $_W;
		global $_GPC;
		include wl_template('bargainhtml/mybargain');
	}

	public function bargaindetail()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '活动详情 - ' . $_W['wlsetting']['base']['name'] : '活动详情';
		$id = $_GPC['cid'];
		$userid = $_GPC['userid'];

		if (empty($_W['mid'])) {
			header('location:' . app_url('member/user/signin', array('backurl' => urlencode(app_url('bargain/bargain_app/bargaindetail', array('cid' => $id, 'userid' => $userid))))));
		}

		if (empty($userid)) {
			$userid = pdo_getcolumn(PDO_NAME . 'bargain_userlist', array('activityid' => $id, 'mid' => $_W['mid'], 'uniacid' => $_W['uniacid']), 'id');

			if ($userid) {
				header('location:' . app_url('bargain/bargain_app/bargaindetail', array('cid' => $id, 'userid' => $userid)));
			}
		}

		$activity = pdo_get('wlmerchant_bargain_activity', array('id' => $id));
		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $activity['sid']), array('storename', 'enabled'));

		if ($merchant['enabled'] != 1) {
			pdo_update('wlmerchant_bargain_activity', array('status' => 0), array('id' => $id));
		}

		$nickname = $_W['wlmember']['nickname'];
		$time = date('Y-m-d H:i:s', time());
		if ($activity['share_title'] || $activity['share_desc']) {
			if ($activity['vipstatus'] == 1) {
				$vipstatus = '会员特价';
			}
			else if ($activity['vipstatus'] == 2) {
				$vipstatus = '会员特供';
			}
			else {
				$vipstatus = '';
			}

			if ($activity['share_title']) {
				$title = $activity['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[商品名称]', $activity['name'], $title);
				$title = str_replace('[商户名称]', $merchant['storename'], $title);
				$title = str_replace('[原价]', $activity['oldprice'], $title);
				$title = str_replace('[底价]', $activity['price'], $title);
				$title = str_replace('[特权类型]', $vipstatus, $title);
				$title = str_replace('[会员底价]', $activity['vipprice'], $title);
			}

			if ($activity['share_desc']) {
				$desc = $activity['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[商品名称]', $activity['name'], $desc);
				$desc = str_replace('[商户名称]', $merchant['storename'], $desc);
				$desc = str_replace('[原价]', $activity['oldprice'], $desc);
				$desc = str_replace('[底价]', $activity['price'], $desc);
				$desc = str_replace('[特权类型]', $vipstatus, $desc);
				$desc = str_replace('[会员底价]', $activity['vipprice'], $desc);
			}
		}

		$_W['wlsetting']['share']['share_title'] = !empty($title) ? $title : $activity['name'];
		$_W['wlsetting']['share']['share_desc'] = $desc;
		$_W['wlsetting']['share']['share_image'] = !empty($activity['share_image']) ? $activity['share_image'] : $activity['thumb'];
		include wl_template('bargainhtml/bargaindetail');
	}

	public function fenxiang_ajax()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		pdo_fetch('update' . tablename('wlmerchant_bargain_activity') . ('SET sharenum=sharenum+1 WHERE id = ' . $id));
		exit(json_encode(array('errno' => 0, 'message' => '')));
	}

	public function bargainlistAPI()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$liststatus = $_GPC['liststatus'];
		$psize = 5;
		$data = array();
		$data['aid'] = $_W['aid'];

		if ($liststatus == 1) {
			$data['status'] = 2;
		}
		else {
			if ($liststatus == 2) {
				$data['status'] = 3;
			}
		}

		$activity = Bargain::getNumActive('id,sid,name,thumb,falselooknum,pv,price,vipprice,vipstatus,oldprice', $data, 'sort DESC,ID DESC', $pindex, $psize, 1);
		$activity = $activity[0];

		if ($activity) {
			foreach ($activity as $key => &$act) {
				$act['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $act['sid']), 'storename');
				$act['pv'] = $act['pv'] + $act['falselooknum'];
				$act['thumb'] = tomedia($act['thumb']);
			}
		}

		wl_json(0, 'OKOK', $activity);
	}

	public function bargaindetailAPI()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$userid = $_GPC['userid'];

		if (empty($_W['mid'])) {
			wl_json(3, '未登录');
		}

		$activity = Bargain::getSingleActive($id, '*');
		$activity['sytime'] = $activity['endtime'] - time();
		$activity['starttime'] = date('Y-m-d H:i:s', $activity['starttime']);
		$activity['endtime'] = date('Y-m-d H:i:s', $activity['endtime']);
		$activity['pv'] = $activity['pv'] + $activity['falselooknum'];
		pdo_fetch('update' . tablename('wlmerchant_bargain_activity') . ('SET pv=pv+1 WHERE id = ' . $id));
		$joinnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_bargain_userlist') . (' WHERE activityid = ' . $id));
		$activity['joinnum'] = $joinnum + $activity['falsejoinnum'];
		$activity['sharenum'] = $activity['falsesharenum'] + $activity['sharenum'];
		$activity['thumbs'] = unserialize($activity['thumbs']);

		if ($activity['thumbs']) {
			foreach ($activity['thumbs'] as $key => &$thumb) {
				$thumb = tomedia($thumb);
			}
		}

		$activity['bgmusic'] = tomedia($activity['bgmusic']);
		$alreadynum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND  fkid = ' . $id . ' AND plugin = \'bargain\' AND status IN (1,2,3,4,8,6,7,9) '));
		$activity['levelnum'] = $activity['stock'] - $alreadynum;

		if ($activity['levelnum'] < 0) {
			$activity['levelnum'] = 0;
		}

		$data['vipflag'] = Member::checkhalfmember();
		$data['halfa'] = app_url('halfcard/halfcard_app/userhalfcard');
		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $activity['sid']), array('storename', 'mobile', 'address', 'location', 'id'));
		$merchant['location'] = unserialize($merchant['location']);

		if ($userid) {
			$userorder = pdo_get('wlmerchant_bargain_userlist', array('id' => $userid));
			$bargainflag = pdo_getcolumn(PDO_NAME . 'bargain_helprecord', array('mid' => $_W['mid'], 'userid' => $userid), 'id');

			if ($bargainflag) {
				$data['already'] = 1;
			}
			else {
				$data['already'] = 0;
			}
		}

		if (empty($userid)) {
			$userorder = pdo_get('wlmerchant_bargain_userlist', array('activityid' => $id, 'mid' => $_W['mid']));
		}

		if ($userorder) {
			if ($userorder['mid'] == $_W['mid']) {
				$data['ownbargain'] = 1;
			}
			else {
				$data['ownbargain'] = 0;
			}
		}
		else {
			$data['ownbargain'] = 1;
		}

		$data['userorder'] = $userorder;

		if ($userorder) {
			$data['helplist'] = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_bargain_helprecord') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND userid = ' . $userorder['id'] . ' ORDER BY createtime DESC'));

			if ($data['helplist']) {
				foreach ($data['helplist'] as $key => &$help) {
					$mem = pdo_get('wlmerchant_member', array('id' => $help['mid']), array('avatar', 'nickname'));
					$help['nickname'] = $mem['nickname'];
					$help['avatar'] = tomedia($mem['avatar']);
				}
			}

			$member = pdo_get('wlmerchant_member', array('id' => $userorder['mid']), array('avatar', 'nickname'));
			$member['avatar'] = tomedia($member['avatar']);
			$data['user'] = $member;
			$data['userorderurl'] = app_url('order/userorder/orderdetail', array('orderid' => $userorder['orderid'], 'type' => 'bargain'));
		}
		else {
			$data['nouser'] = 1;
			$member = pdo_get('wlmerchant_member', array('id' => $_W['mid']), array('avatar', 'nickname', 'mobile'));
			$member['avatar'] = tomedia($member['avatar']);
			$data['user'] = $member;
		}

		$data['userlist'] = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_bargain_userlist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND activityid = ' . $activity['id'] . ' ORDER BY price ASC'));

		if ($data['userlist']) {
			foreach ($data['userlist'] as $key => &$rank) {
				$mem2 = pdo_get('wlmerchant_member', array('id' => $rank['mid']), array('avatar', 'nickname'));
				$rank['nickname'] = $mem2['nickname'];
				$rank['avatar'] = tomedia($mem2['avatar']);
				$rank['updatetime'] = date('Y-m-d H:i:s', $rank['updatetime']);
			}
		}

		$recommend = pdo_fetchall('SELECT id,name,price,thumb,oldprice FROM ' . tablename('wlmerchant_bargain_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 2 AND id != ' . $id . ' ORDER BY RAND() LIMIT 2'));

		foreach ($recommend as $key => &$re) {
			$re['thumb'] = tomedia($re['thumb']);
		}

		$data['recommend'] = $recommend;
		$setting = Setting::wlsetting_read('share');
		$setting = $setting['forcefollow'];
		if (in_array('bargain', $setting) && $_W['fans']['follow'] != 1 && is_weixin()) {
			$data['forcefollow'] = 1;
		}
		else {
			$data['forcefollow'] = 0;
		}

		if (in_array('helpBargain', $setting) && $_W['fans']['follow'] != 1 && is_weixin()) {
			$data['helpBargain'] = 1;
		}
		else {
			$data['helpBargain'] = 0;
		}

		$data['detail'] = $activity;
		$data['merchant'] = $merchant;
		wl_json(0, 'OKOK', $data);
	}

	public function bargainAPI()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$userid = $_GPC['userid'];

		if (empty($_W['mid'])) {
			wl_json(3, '未登录');
		}

		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);

		if (empty($userid)) {
			$userid = pdo_getcolumn('wlmerchant_bargain_userlist', array('uniacid' => $_W['uniacid'], 'activityid' => $id, 'mid' => $_W['mid']), 'id');
		}

		$activity = Bargain::getSingleActive($id, '*');

		if ($activity['status'] != 2) {
			wl_json(1, '活动未开始或已结束,无法砍价');
		}

		if (0 < $_W['agentset']['bargainset']['syslimit']) {
			$zreo = strtotime(date('Y-m-d'));
			$systime = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_bargain_helprecord') . (' WHERE mid = ' . $_W['mid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND createtime > ' . $zreo));

			if ($_W['agentset']['bargainset']['syslimit'] <= $systime) {
				wl_json(1, '您每天只能砍价' . $_W['agentset']['bargainset']['syslimit'] . '次');
			}
		}

		$alreadynum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND  fkid = ' . $id . ' AND plugin = \'bargain\' AND status IN (1,2,3,4,8,6,7,9) '));
		$levelnum = $activity['stock'] - $alreadynum;

		if ($levelnum < 1) {
			wl_json(1, '该商品已售罄，无法继续砍价');
		}

		if (time() < $activity['starttime']) {
			wl_json(1, '活动时间未开始，无法砍价');
		}

		if ($activity['endtime'] < time()) {
			wl_json(1, '活动时间已结束，无法继续砍价');
		}

		if (empty($userid)) {
			if (empty($_W['wlmember']['mobile']) && in_array('bargain', $mastmobile)) {
				wl_json(2, '未绑定手机号，去绑定？');
			}

			if ($activity['vipstatus'] == 2) {
				$halfcardflag = Member::checkhalfmember();
				$level = unserialize($activity['level']);

				if (empty($halfcardflag)) {
					wl_json(1, '该砍价活动只有会员可以参与，请先成为会员');
				}
				else {
					if ($level) {
						$flag = Halfcard::checklevel($_W['mid'], $level);

						if (empty($flag)) {
							wl_message(array('errno' => 1, 'message' => '您所在的会员等级无法参与该活动'));
						}
					}
				}
			}

			if ($activity['joinlimit']) {
				$joinnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_bargain_userlist') . (' WHERE activityid = ' . $id));
				if ($activity['joinlimit'] == $joinnum || $activity['joinlimit'] < $joinnum) {
					wl_json(1, '参与失败，已超出活动人数');
				}
			}

			$userid = Bargain::createuserlist($_W['mid'], $id);
		}

		if ($userid) {
			if (empty($_W['wlmember']['mobile']) && in_array('helpbargain', $mastmobile)) {
				wl_json(2, '未绑定手机号，去绑定？');
			}

			if ($activity['helplimit']) {
				$helpnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_bargain_helprecord') . (' WHERE userid = ' . $userid));
				$helpnum = $helpnum - 1;
				if ($helpnum == $activity['helplimit'] || $activity['helplimit'] < $helpnum) {
					wl_json(1, '砍价失败，最多' . $activity['helplimit'] . '个好友帮忙砍价');
				}
			}

			if ($activity['dayhelpcount']) {
				$todaytime = strtotime(date('Y-m-d'), time());
				$dayhelpnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_bargain_helprecord') . (' WHERE userid = ' . $userid . ' AND createtime > ' . $todaytime));
				if ($dayhelpnum == $activity['dayhelpcount'] || $activity['dayhelpcount'] < $dayhelpnum) {
					wl_json(1, '砍价失败，今日最多' . $activity['dayhelpcount'] . '个好友帮忙砍价');
				}
			}

			if (0 < $activity['onlytimes']) {
				$ownuserid = pdo_getcolumn(PDO_NAME . 'bargain_userlist', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'activityid' => $activity['id']), 'id');

				if (empty($ownuserid)) {
					$ownuserid = 0;
				}

				$onlytime = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_bargain_helprecord') . (' WHERE mid = ' . $_W['mid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . '  AND activityid = ' . $activity['id'] . ' AND  userid != ' . $ownuserid . '  '));

				if ($activity['onlytimes'] <= $onlytime) {
					wl_json(1, '砍价失败，您只能帮助' . $activity['onlytimes'] . '个好友对该商品砍价');
				}
			}

			$res = Bargain::bargaining($_W['mid'], $id, $userid);
		}

		if ($res) {
			$data['userid'] = $userid;
			$data['bargainprice'] = pdo_getcolumn(PDO_NAME . 'bargain_helprecord', array('id' => $res), 'bargainprice');
			wl_json(0, 'OKOK', $data);
		}
	}

	public function receipt()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['orderid'];
		$exip = $_GPC['exid'];
		$res1 = pdo_update('wlmerchant_order', array('status' => 2), array('id' => $id));
		$res2 = pdo_update('wlmerchant_express', array('receivetime' => time()), array('id' => $exip));
		$disorderid = pdo_getcolumn('wlmerchant_order', array('id' => $id), 'disorderid');

		if ($disorderid) {
			pdo_update('wlmerchant_disorder', array('status' => 1), array('id' => $disorderid, 'status' => 0));
		}

		if ($res1 && $res2) {
			exit(json_encode(array('status' => 1, 'message' => $res)));
		}
		else {
			exit(json_encode(array('status' => 0, 'message' => '未知错误')));
		}
	}

	public function hexiaokey()
	{
		global $_W;
		global $_GPC;
		$sid = $_GPC['sid'];
		$verkey = $_GPC['verkey'];
		$orderid = $_GPC['orderid'];
		$num = $_GPC['num'];
		$merchantkey = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $sid), 'verkey');

		if ($verkey == $merchantkey) {
			$res = Bargain::hexiaoorder($orderid, 0, $num, 4);

			if ($res) {
				exit(json_encode(array('errno' => 0, 'message' => '核销成功')));
			}
			else {
				exit(json_encode(array('errno' => 1, 'message' => '核销失败')));
			}
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '密码错误!')));
		}
	}

	public function hexiao()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '订单核销 - ' . $_W['wlsetting']['base']['name'] : '订单核销';
		$id = $_GPC['id'];
		$order_out = pdo_get('wlmerchant_order', array('id' => $id));
		$verifier = SingleMerchant::verifier($order_out['sid'], $_W['mid']);
		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $order_out['sid']));
		$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $order_out['fkid']));
		$record = pdo_get('wlmerchant_bargain_userlist', array('id' => $order_out['specid']));
		$order_out['levelnum'] = $order_out['num'] - $record['usetimes'];
		$order_out['usetimes'] = $record['usetimes'];
		$type = 'bargain';
		$goods['price'] = $goods['oldprice'];
		include wl_template('order/hexiaoorder');
	}

	public function xiaofei()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$num = $_GPC['num'];
		$res = Bargain::hexiaoorder($id, $_W['mid'], $num, 2);

		if ($res) {
			exit(json_encode(array('errno' => 0, 'message' => '核销成功')));
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '核销失败')));
		}
	}

	public function expressorder()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '订单详情 - ' . $_W['wlsetting']['base']['name'] : '订单详情';
		$id = $_GPC['orderid'];
		$order = pdo_get('wlmerchant_order', array('id' => $id));
		$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $order['fkid']));
		$express = pdo_get('wlmerchant_express', array('id' => $order['expressid']));
		$address = pdo_get('wlmerchant_address', array('id' => $express['addressid']));
		$expressprice = $express['expressprice'];
		$member = pdo_get('wlmerchant_member', array('id' => $order['mid']));
		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $order['sid']));
		include wl_template('bargainhtml/expressorder');
	}

	public function userlist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '我的砍价 - ' . $_W['wlsetting']['base']['name'] : '我的砍价';
		$status = $_GPC['status'];

		if ($_W['ispost']) {
			$where = ' uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $_W['mid'] . ' ';
			if ($status != 'all' && !empty($status)) {
				$where .= ' AND status = {intval(' . $status . ')}';
			}

			$myorder = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_bargain_userlist') . ('WHERE ' . $where . ' ORDER BY createtime DESC'));

			if ($myorder) {
				foreach ($myorder as $k => &$v) {
					$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $v['activityid']), array('name', 'id', 'oldprice', 'sid', 'thumb'));
					$v['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $goods['sid']), 'storename');
					$v['goodsname'] = $goods['name'];
					$v['goodsimg'] = tomedia($goods['thumb']);
					$v['oldprice'] = $goods['oldprice'];
					$v['createtime'] = date('Y-m-d H:i', $v['createtime']);
					$v['url'] = app_url('bargain/bargain_app/bargaindetail', array('cid' => $goods['id'], 'userid' => $v['id']));
					$v['xiaofei'] = app_url('order/userorder/orderdetail', array('type' => 'bargain', 'orderid' => $v['orderid']));
				}
			}

			exit(json_encode(array('errno' => 0, 'data' => $myorder)));
		}

		include wl_template('bargainhtml/userlist');
	}

	public function createorderAPI()
	{
		global $_W;
		global $_GPC;
		$userid = $_GPC['userid'];

		if (empty($userid)) {
			wl_json(1, '缺少活动ID，请刷新重试');
		}

		$userorder = pdo_get('wlmerchant_bargain_userlist', array('id' => $userid));

		if ($userorder['orderid']) {
			wl_json(1, '订单已支付,请勿重复下单');
		}

		$activity = Bargain::getSingleActive($userorder['activityid'], '*');

		if (!empty($activity['submitmoneylimit'])) {
			if ($activity['submitmoneylimit'] < $userorder['price']) {
				$needbar = sprintf('%.2f', $userorder['price'] - $activity['submitmoneylimit']);
				wl_json(1, '您还需要砍去' . $needbar . '元才可以下单');
			}
		}

		wl_json(0);
	}

	public function createorder()
	{
		global $_W;
		global $_GPC;
		$userid = $_GPC['userid'];
		$usestatus = $_GPC['usestatus'];
		$addressid = $_GPC['addressid'];
		$userorder = pdo_get('wlmerchant_bargain_userlist', array('id' => $userid));
		$remark = $_GPC['remark'];
		$activity = Bargain::getSingleActive($userorder['activityid'], '*');
		$num = 1;
		$nopayorder = pdo_getcolumn('wlmerchant_order', array('mid' => $_W['mid'], 'status' => 0, 'fkid' => $activity['id'], 'specid' => $userid, 'plugin' => 'bargain'), 'id');

		if (!empty($nopayorder)) {
			wl_json(3, '请先支付或取消未支付的订单');
		}

		$alreadynum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_bargain_userlist') . (' WHERE activityid = ' . $activity['id'] . ' AND orderid > 0'));
		$levelnum = $activity['stock'] - $alreadynum;
		if ($levelnum == 0 || $levelnum < 0) {
			wl_json(1, '该商品已被抢完，请下次手快些哦');
		}

		if ($activity['vipstatus'] == 1) {
			$now = time();

			if ($_W['wlsetting']['halfcard']['halfcardtype'] == 2) {
				$halfcardflag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $userorder['mid'] . ' AND aid = ' . $_W['aid'] . ' AND expiretime > ' . $now . ' AND disable != 1'));
			}
			else {
				$halfcardflag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $userorder['mid'] . ' AND expiretime > ' . $now . ' AND disable != 1'));
			}

			if ($halfcardflag) {
				$vipbuyflag = 1;
			}
		}

		if (empty($vipbuyflag)) {
			$vipbuyflag = 0;
		}

		if ($usestatus) {
			if ($activity['expressid']) {
				$address = pdo_get('wlmerchant_address', array('id' => $addressid));
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

				$data['uniacid'] = $_W['uniacid'];
				$data['mid'] = $_W['mid'];
				$data['goodsid'] = $activity['id'];
				$data['merchantid'] = $activity['sid'];
				$data['address'] = $addre = $address['province'] . $address['city'] . $address['county'] . $address['detailed_address'];
				$data['name'] = $username = $address['name'];
				$data['tel'] = $mobile = $address['tel'];
				$data['expressprice'] = $expressprice;
				pdo_insert(PDO_NAME . 'express', $data);
				$expressid = pdo_insertid();
			}
			else {
				$expressprice = 0;
				$expressid = 0;
			}

			$prices = sprintf('%.2f', $userorder['price'] + $expressprice);
		}
		else {
			$username = trim($_GPC['thname']);
			$mobile = trim($_GPC['thmobile']);
			$prices = sprintf('%.2f', $userorder['price']);
		}

		$settlementmoney = Store::getsettlementmoney(5, $userid, $num, $activity['sid'], $vipbuyflag);

		if ($expressprice) {
			$settlementmoney = sprintf('%.2f', $expressprice + $settlementmoney);
		}

		$random = Util::createConcode(1);
		$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'sid' => $activity['sid'], 'aid' => $activity['aid'], 'fkid' => $activity['id'], 'plugin' => 'bargain', 'payfor' => 'bargainOrder', 'orderno' => createUniontid(), 'status' => 0, 'createtime' => TIMESTAMP, 'price' => $prices, 'num' => 1, 'vipbuyflag' => $vipbuyflag, 'specid' => $userid, 'name' => $username, 'mobile' => $mobile, 'address' => $addre, 'fightstatus' => $usestatus, 'expressid' => $expressid, 'buyremark' => $remark, 'goodsprice' => $userorder['price'], 'settlementmoney' => $settlementmoney);
		pdo_insert(PDO_NAME . 'order', $data);
		$orderid = pdo_insertid();

		if ($orderid) {
			wl_json(0, '下单成功', $orderid);
		}
	}

	public function topay()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$orderid = $_GPC['orderid'];
		if (empty($id) || empty($orderid)) {
			wl_message('缺少重要参数，请重试');
		}

		$activity = Bargain::getSingleActive($id, 'name');
		$data = pdo_get(PDO_NAME . 'order', array('uniacid' => $_W['uniacid'], 'id' => $orderid), array('orderno', 'price', 'sid'));
		$bankrid = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $data['sid']), 'bankrid');
		$params = array('tid' => $data['orderno'], 'ordersn' => $data['orderno'], 'title' => $activity['name'], 'fee' => $data['price'], 'module' => 'weliam_merchant');
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => 'Bargain', 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Bargain', 'payfor' => 'BargainOrder', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}
}

?>
