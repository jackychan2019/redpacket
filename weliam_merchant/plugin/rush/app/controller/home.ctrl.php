<?php
//dezend by http://www.sucaihuo.com/
class Home_WeliamController
{
	public function index()
	{
		global $_W;
		global $_GPC;

		if ($_W['agentset']['diypageset']['page_rush']) {
			header('Location:' . app_url('diypage/diyhome/home', array('aid' => $_W['aid'], 'id' => $_W['agentset']['diypageset']['page_rush'])));
			exit();
		}

		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '抢购列表 - ' . $_W['wlsetting']['base']['name'] : '抢购列表';
		$status = !empty($_GPC['status']) ? $_GPC['status'] : 2;
		$config = Setting::agentsetting_read('rush');
		$searchSet = array('search_float' => $config['search_float'], 'search_bgColor' => $config['search_bgColor']);
		$advs = pdo_getall('wlmerchant_adv', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'enabled' => 1, 'type' => 4), '', '', 'displayorder DESC');
		$cate = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'rush_category') . (' WHERE aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' ORDER BY sort DESC'));

		if ($config['communqrcode']) {
			$sys = Setting::agentsetting_read('community');
			$community['qrcode'] = tomedia($config['communqrcode']);
			$community['name'] = $config['communname'] ? $config['communname'] : $sys['communname'];
			$community['desc'] = $config['commundesc'] ? $config['commundesc'] : $sys['commundesc'];
			$community['img'] = $config['communimg'] ? $config['communimg'] : $sys['communimg'];
			$community['img'] = tomedia($community['img']);
		}

		if ($config['share_title'] || $config['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];

			if ($config['share_title']) {
				$title = $config['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[系统名称]', $sysname, $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}
			else {
				$_W['wlsetting']['share']['share_title'] = !empty($config['share_title']) ? $config['share_title'] : $_W['wlsetting']['share']['share_title'];
			}

			if ($config['share_desc']) {
				$desc = $config['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[系统名称]', $sysname, $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
			else {
				$_W['wlsetting']['share']['share_desc'] = !empty($config['share_desc']) ? $config['share_desc'] : $_W['wlsetting']['share']['share_desc'];
			}
		}
		else {
			$_W['wlsetting']['share']['share_title'] = !empty($config['share_title']) ? $config['share_title'] : $_W['wlsetting']['share']['share_title'];
			$_W['wlsetting']['share']['share_desc'] = !empty($config['share_desc']) ? $config['share_desc'] : $_W['wlsetting']['share']['share_desc'];
		}

		$_W['wlsetting']['share']['share_image'] = !empty($config['share_image']) ? $config['share_image'] : $_W['wlsetting']['share']['share_image'];

		if (p('diypage')) {
			if (!empty($_W['agentset']['diypageset']['adv_rush'])) {
				$advdata = Diy::BeOverdue($_W['agentset']['diypageset']['adv_rush']);
			}

			if (!empty($_W['agentset']['diypageset']['menu_rush'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_rush']);
			}
		}

		$page_type = 3;
		include wl_template('home/goods_list');
	}

	public function getGoods()
	{
		global $_W;
		global $_GPC;
		$where = array('aid' => $_W['aid']);
		$pindex = $_GPC['pindex'];
		$psize = $_GPC['psize'];
		$cateid = $_GPC['cateid'];

		if (!empty($cateid)) {
			$where['cateid'] = $cateid;
		}

		if (intval($_GPC['status']) == 2) {
			$where['status#'] = '(2,7)';
		}
		else {
			$where['status'] = intval($_GPC['status']);
		}

		$active = Rush::getNumActive('*', $where, 'status ASC,sort DESC,id DESC', $pindex, $psize, 1);

		foreach ($active[0] as $key => &$v) {
			Rush::changeActivestatus($v);
			$v['thumbs'] = unserialize($v['thumbs']);
			$v['thumb'] = tomedia($v['thumbs'][0]);
			$v['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $v['sid']), 'storename');
			if ($v['status'] == 2 || $v['status'] == 7) {
				$v['sytime'] = $v['endtime'] - time();
				$v['a'] = app_url('rush/home/detail', array('id' => $v['id']));
			}
			else {
				if ($v['status'] == 1) {
					$v['sytime'] = $v['starttime'] - time();
				}
				else {
					$v['sytime'] = 0;
				}

				$v['a'] = app_url('rush/home/detail', array('id' => $v['id']));
				$v['start_time'] = $v['starttime'];
				$v['starttime'] = date('Y-m-d H:i:s', $v['starttime']);
			}

			$folllow = pdo_getcolumn(PDO_NAME . 'rush_follows', array('mid' => $_W['mid'], 'actid' => $v['id']), 'id');

			if ($folllow) {
				$v['follow'] = '已关注';
			}
			else {
				$v['follow'] = '关注抢购';
			}

			if ($v['status'] == 1 || $v['status'] == 2) {
				$alreadyBuyNum = pdo_fetch('SELECT SUM(num) FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status IN (0,1,2,3,6,9,4,8) AND activityid = ' . $v['id'] . ' '));
				$alreadyBuyNum = array_values($alreadyBuyNum);
				$v['buynum'] = $alreadyBuyNum[0];

				if ($v['optionstatus']) {
					$v['levelnum'] = 0;
					$options = pdo_getall('wlmerchant_goods_option', array('goodsid' => $v['id'], 'type' => 1), array('stock'));

					foreach ($options as $key => &$op) {
						if (0 < $op['stock']) {
							$v['levelnum'] += $op['stock'];
						}
					}
				}
				else {
					$v['levelnum'] = $v['num'] - $v['buynum'];
				}
			}
			else {
				$v['levelnum'] = 0;
			}
		}

		exit(json_encode($active[0]));
	}

	public function follow()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['qgid']);

		if ($id) {
			$config = Setting::agentsetting_read('rush');

			if ($config['follow_time']) {
				$sendtime = 60 * $config['follow_time'];
			}
			else {
				$sendtime = 600;
			}

			$goods = Rush::getSingleActive($id, '*');
			$lasttime = time() + $sendtime;

			if ($goods['starttime'] < $lasttime) {
				exit(json_encode(array('result' => 2, 'msg' => '无需关注，活动即将开始')));
			}

			$folllow = pdo_getcolumn(PDO_NAME . 'rush_follows', array('mid' => $_W['mid'], 'actid' => $id), 'id');

			if ($folllow) {
				exit(json_encode(array('result' => 2, 'msg' => '小淘气，不要重复关注哦')));
			}

			$res = pdo_insert(PDO_NAME . 'rush_follows', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'aid' => $_W['aid'], 'actid' => $id, 'sendtime' => $goods['starttime'] - $sendtime));

			if ($res) {
				exit(json_encode(array('result' => 1, 'msg' => '抢购关注成功！')));
			}
			else {
				exit(json_encode(array('result' => 2, 'msg' => '参数错误，请重试')));
			}
		}

		exit(json_encode(array('result' => 2, 'msg' => '参数错误，请刷新重试！')));
	}

	public function checkstatus()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$goods = pdo_get('wlmerchant_rush_activity', array('id' => $id), array('status', 'starttime', 'endtime'));

		if (time() < $goods['starttime']) {
			$status = 1;
		}
		else {
			if ($goods['starttime'] < time() && time() < $goods['endtime']) {
				$status = 2;
			}
			else {
				if ($goods['endtime'] < time()) {
					$status = 3;
				}
			}
		}

		if ($goods['status'] != $status) {
			pdo_update('wlmerchant_rush_activity', array('status' => $status), array('id' => $id));
		}

		exit(json_encode($status));
	}

	public function detail()
	{
		global $_W;
		global $_GPC;
		$type = 'rush';
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '抢购详情 - ' . $_W['wlsetting']['base']['name'] : '抢购详情';
		$config = Setting::agentsetting_read('rush');
		$id = $_GPC['id'];
		$set = Setting::agentsetting_read('rush');
		$orderset = Setting::wlsetting_read('orderset');
		$cancle_time = $set['cancle_time'] ? $set['cancle_time'] : 3;

		if (empty($id)) {
			wl_message('缺少重要参数');
		}

		$folllow = pdo_getcolumn(PDO_NAME . 'rush_follows', array('mid' => $_W['mid'], 'actid' => $id), 'id');
		$goods = Rush::getSingleActive($id, '*');
		$goods['describe'] = nl2br($goods['describe']);
		$goods['detail'] = nl2br($goods['detail']);
		if (empty($goods['usestatus']) && $goods['creditmoney'] < 0.01 && empty($goods['fastpay'])) {
			$thname = $_W['wlmember']['nickname'];
			$thmobile = $_W['wlmember']['mobile'];
			if (!empty($thname) && ($orderset['thmobile'] || !empty($thmobile))) {
				$direct = 1;
			}
		}

		Rush::changeActivestatus($goods);
		pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET pv=pv+1 WHERE id = ' . $goods['sid']));
		pdo_fetch('update' . tablename('wlmerchant_rush_activity') . ('SET pv=pv+1 WHERE id = ' . $id));
		$alreadyBuyNum = pdo_fetch('SELECT SUM(num) FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status IN (0,1,2,3,6,9,4,8) AND activityid = ' . $id . ' '));
		$alreadyBuyNum = array_values($alreadyBuyNum);
		$goods['buynum'] = $alreadyBuyNum[0];

		if (empty($goods['op_one_limit'])) {
			$goods['op_one_limit'] = 999999;
		}

		$alreadyBuyNum2 = pdo_fetch('SELECT SUM(num) FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status IN (0,1,2,3,6,9,4,8) AND mid = ' . $_W['mid'] . ' AND activityid = ' . $id . ' '));
		$alreadyBuyNum2 = array_values($alreadyBuyNum2);
		$userbuynum = $alreadyBuyNum2[0];
		$limitnum = intval($goods['op_one_limit'] - $userbuynum);

		if ($goods['optionstatus']) {
			$specs = pdo_getall('wlmerchant_goods_spec', array('goodsid' => $id, 'type' => 1), array('title', 'content'), '', 'displayorder ASC');

			if ($specs) {
				foreach ($specs as $key => &$spec) {
					$spec['content'] = unserialize($spec['content']);

					foreach ($spec['content'] as $key => &$item) {
						$it = pdo_get('wlmerchant_goods_spec_item', array('id' => $item, 'show' => 1));

						if ($it) {
							$it['thumb'] = tomedia($it['thumb']);
							$spec['item'][] = $it;
						}
					}
				}
			}

			$goods['levelnum'] = 0;
			$options = pdo_getall('wlmerchant_goods_option', array('goodsid' => $id, 'type' => 1), array('stock'));

			foreach ($options as $key => &$op) {
				if (0 < $op['stock']) {
					$goods['levelnum'] += $op['stock'];
				}
			}
		}
		else {
			$goods['levelnum'] = $goods['num'] - $goods['buynum'];
		}

		$goods['buynum'] = $goods['buynum'] + $goods['allsalenum'];
		$goods['num'] = $goods['num'] + $goods['allsalenum'];
		$tags = array();

		if (0 < $goods['retainage']) {
			$tags[] = '到店付尾款';
		}

		if ($goods['appointment']) {
			$tags[] = '提前' . $goods['appointment'] . '小时预约';
		}

		if ($goods['integral']) {
			$sett = Setting::wlsetting_read('trade');
			$typeTitle = $sett['credittext'] ? $sett['credittext'] : '积分';
			$consumptionSet = Setting::wlsetting_read('consumption');

			if ($consumptionSet['status'] == 1) {
				$tags[] = '<a href=\'' . app_url('consumption/goods/goods_index') . '\' style=\'color: #666;\'>' . '购买赠' . $goods['integral'] . $typeTitle . '</a>';
			}
			else {
				$tags[] = '购买赠' . $goods['integral'] . $typeTitle;
			}
		}

		$tag = unserialize($goods['tag']);

		foreach ($tag as $key => $va) {
			$tags[] = pdo_getcolumn(PDO_NAME . 'tags', array('id' => $va), 'title');
		}

		$advs = unserialize($goods['thumbs']);
		$merchant = Store::getSingleStore($goods['sid']);

		if ($merchant['enabled'] != 1) {
			pdo_update('wlmerchant_rush_activity', array('status' => 0), array('id' => $id));
			$goods['status'] = 0;
		}

		$merchant['storehours'] = unserialize($merchant['storehours']);

		if ($merchant['store_quhao']) {
			$merchant['mobile'] = $merchant['store_quhao'] . $merchant['mobile'];
		}

		$location = unserialize($merchant['location']);
		if ($_SESSION['lng'] && $_SESSION['lat']) {
			$distance = Store::getdistance($_SESSION['lng'], $_SESSION['lat'], $location['lng'], $location['lat']);

			if (1000 < $distance) {
				$distance = floor($distance / 1000 * 10) / 10 . 'km';
			}
			else {
				$distance = round($distance) . 'm';
			}
		}
		else {
			$distance = '';
		}

		$where['activityid'] = $id;
		$where['#status#'] = '(0,1,2,3,4,8,9)';
		$orders = Rush::getNumOrder('*', $where, 'paytime DESC', 0, 0, 1);
		$goods['ordernum'] = $orders[2];
		$orders = $orders[0];

		if (5 < $goods['ordernum']) {
			$orders = array_slice($orders, 0, 5);
			$moreflag = 1;
		}
		else {
			$falseorder = unserialize($goods['falseorder']);

			if ($falseorder) {
				$falsenum = count($falseorder);
				$neednum = intval(5 - $goods['ordernum']);

				if ($neednum < $falsenum) {
					$falseorder = array_slice($falseorder, 0, $neednum);
				}

				$orders = array_merge($orders, $falseorder);
			}
		}

		foreach ($orders as $k => &$v) {
			if ($v['mid']) {
				$user = Rush::getSingleMember($v['mid'], 'avatar,nickname');
				$v['userimg'] = tomedia($user['avatar']);
			}
			else {
				$v['userimg'] = tomedia($v['avatar']);
			}
		}

		if ($set['barrage']) {
			if ($orders) {
				$recode = array();

				foreach ($orders as $key => &$re) {
					$recode[$key]['msg'] = '恭喜' . $re['nickname'] . '抢购成功';
					$recode[$key]['imgurl'] = tomedia($re['userimg']);
				}

				$leng = count($recode) - 1;
				$recode = json_encode($recode);
			}
		}

		$halfcardflag = Member::checkhalfmember();
		if ($halfcardflag && $goods['vipstatus'] == 1) {
			$price = $goods['vipprice'];
		}
		else {
			$price = $goods['price'];
		}

		if (time() < $goods['starttime']) {
			$word = '开始';
			$sytime = $goods['starttime'] - time();
		}
		else {
			$word = '结束';
			$sytime = $goods['endtime'] - time();
		}

		$hjprice = $price;

		if ($config['communqrcode']) {
			$sys = Setting::agentsetting_read('community');
			$community['qrcode'] = tomedia($config['communqrcode']);
			$community['name'] = $config['communname'] ? $config['communname'] : $sys['communname'];
			$community['desc'] = $config['commundesc'] ? $config['commundesc'] : $sys['commundesc'];
			$community['img'] = $config['communimg'] ? $config['communimg'] : $sys['communimg'];
			$community['img'] = tomedia($community['img']);
		}

		if ($goods['sharestatus']) {
			$sharemoney = $goods['sharemoney'];

			if ($goods['sharestatus'] == 1) {
				$alshare = pdo_getcolumn(PDO_NAME . 'sharegift_record', array('uniacid' => $_W['uniacid'], 'plugin' => 1, 'mid' => $_W['mid'], 'goodsid' => $id, 'status' => 0, 'type' => 1), 'id');

				if ($alshare) {
					$hjprice = sprintf('%.2f', $hjprice - $sharemoney);
				}
			}
			else {
				if ($goods['sharestatus'] == 2) {
					$invitid = $_GPC['invitid'];
					if ($invitid && $invitid != $_W['mid']) {
						$alshare2 = pdo_getcolumn(PDO_NAME . 'sharegift_record', array('uniacid' => $_W['uniacid'], 'plugin' => 1, 'mid' => $invitid, 'buymid' => $_W['mid'], 'goodsid' => $id, 'status' => 0, 'type' => 1), 'id');
						if (empty($alshare2) && $_W['mid']) {
							Sharegift::addrecord($id, $invitid, $_W['mid'], $goods['sharestatus'], $goods['sharemoney'], 1);
						}
					}
				}
			}
		}

		$comorders = pdo_fetchall('SELECT id,mid FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 3 AND activityid = ' . $id . ' ORDER BY id DESC limit 10'));
		$ordersIdStr = '';

		foreach ($comorders as $key => &$corder) {
			$ordersIdStr .= $corder['id'] . ',';
		}

		$ordersIdStr = trim($ordersIdStr, ',');
		$comment = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_comment') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND checkone = 2 AND sid = ' . $goods['sid'] . ' AND idoforder IN (' . $ordersIdStr . ') ORDER BY id DESC'));

		if ($comment) {
			$merchantid = $goods['sid'];

			foreach ($comment as $sk => &$com) {
				$com['pic'] = unserialize($com['pic']);

				if ($com['pic']) {
					foreach ($com['pic'] as $k => &$v) {
						$v = tomedia($v);
					}
				}
			}
		}

		$recomgoods = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_rush_activity') . ('WHERE id != ' . $id . ' AND uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 2 ORDER BY RAND() LIMIT 2'));
		$nickname = $_W['wlmember']['nickname'];
		$time = date('Y-m-d H:i:s', time());
		if ($goods['share_title'] || $goods['share_desc']) {
			if ($goods['vipstatus'] == 1) {
				$vipstatus = '会员特价';
			}
			else if ($goods['vipstatus'] == 2) {
				$vipstatus = '会员特供';
			}
			else {
				$vipstatus = '';
			}

			if ($goods['share_title']) {
				$title = $goods['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[商品名称]', $goods['name'], $title);
				$title = str_replace('[商户名称]', $merchant['storename'], $title);
				$title = str_replace('[抢购价]', $goods['price'], $title);
				$title = str_replace('[特权类型]', $vipstatus, $title);
				$title = str_replace('[会员价]', $goods['vipprice'], $title);
				$title = str_replace('[原价]', $goods['oldprice'], $title);
			}

			if ($goods['share_desc']) {
				$desc = $goods['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[商品名称]', $goods['name'], $desc);
				$desc = str_replace('[商户名称]', $merchant['storename'], $desc);
				$desc = str_replace('[抢购价]', $goods['price'], $desc);
				$desc = str_replace('[特权类型]', $vipstatus, $desc);
				$desc = str_replace('[会员价]', $goods['vipprice'], $desc);
				$desc = str_replace('[原价]', $goods['oldprice'], $desc);
			}
		}

		if (empty($desc)) {
			$desc = $set['share_desc'];
			$desc = str_replace('[昵称]', $nickname, $desc);
			$desc = str_replace('[时间]', $time, $desc);
		}

		$_W['wlsetting']['share']['share_title'] = !empty($title) ? $title : $goods['name'];
		$_W['wlsetting']['share']['share_desc'] = $desc;
		$_W['wlsetting']['share']['share_image'] = !empty($goods['share_image']) ? $goods['share_image'] : $goods['thumb'];

		if ($goods['levelnum'] < 0) {
			$goods['levelnum'] = 0;
		}

		if ($limitnum < 0) {
			$limitnum = 0;
		}

		Rush::updateActive(array('levelnum' => $goods['levelnum']), array('id' => $id));
		include wl_template('home/goods_detail');
	}

	public function getoption()
	{
		global $_W;
		global $_GPC;
		$goodsid = $_GPC['goodsid'];
		$specs = trim($_GPC['specids'], '_');
		$option = pdo_get('wlmerchant_goods_option', array('specs' => $specs), array('id', 'stock', 'price', 'vipprice'));

		if ($option) {
			$goods = Rush::getSingleActive($goodsid, 'vipstatus');
			$price = $option['price'];

			if ($goods['vipstatus'] == 1) {
				$halfcardflag = Member::checkhalfmember();

				if ($halfcardflag) {
					$price = $option['vipprice'];
				}
			}

			if ($option['stock'] < 0) {
				$option['stock'] = 0;
			}

			$data = array('price' => $price, 'optionid' => $option['id'], 'sort' => $option['stock']);

			if ($option['stock'] < 1) {
				wl_json(1, '抱歉，该规格库存不足', $data);
			}

			wl_json(0, 'success', $data);
		}
		else {
			wl_json(2, 'error');
		}
	}

	public function getxggood()
	{
		global $_W;
		global $_GPC;
		$where['id!='] = $_GPC['id'];
		$where['uniacid'] = $_W['uniacid'];
		$where['aid'] = $_W['aid'];
		$where['status'] = 2;
		$rushs = Rush::getNumActive('*', $where, 'starttime ASC', 0, 0, 0);
		$rushs = $rushs[0];

		if ($rushs) {
			foreach ($rushs as $key => &$v) {
				$store = pdo_get('wlmerchant_merchantdata', array('id' => $v['sid']), array('storename'));
				$v['store'] = $store['storename'];
				$v['href'] = app_url('rush/home/detail', array('id' => $v['id']));
				$v['starttime'] = date('Y-m-d H:i:s', $v['starttime']);
				$v['endtime'] = date('Y-m-d H:i:s', $v['endtime']);
				$v['thumb'] = tomedia($v['thumb']);
			}
		}

		exit(json_encode($rushs));
	}

	public function orderConfirm()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '订单支付 - ' . $_W['wlsetting']['base']['name'] : '订单支付';
		$id = $_GPC['id'];

		if (empty($id)) {
			wl_message('缺少重要参数');
		}

		$activity = Rush::getSingleActive($id, '*');
		$config = Setting::agentsetting_read('rush');

		if ($_W['isajax']) {
			if (empty($_W['mid'])) {
				wl_message(array('errno' => 3, 'message' => '未登录!'));
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
				wl_message(array('errno' => 2, 'message' => '未绑定手机号，去绑定？'));
			}

			$halfcardflag = Member::checkhalfmember();

			if ($activity['vipstatus'] == 1) {
				if ($halfcardflag) {
					$activity['price'] = $activity['vipprice'];
				}
			}
			else {
				if ($activity['vipstatus'] == 2) {
					if (empty($halfcardflag)) {
						wl_message(array('errno' => 1, 'message' => '该商品会员特供，请先成为会员'));
					}
				}
			}

			$alreadyBuyNum = pdo_fetch('SELECT SUM(num) FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $_W['mid'] . ' AND status IN (0,1,2,3,6) AND activityid = ' . $id . ' '));
			$alreadyBuyNum = array_values($alreadyBuyNum);
			$alreadyBuyNum = $alreadyBuyNum[0];
			if ($activity['op_one_limit'] <= $alreadyBuyNum && 0 < $activity['op_one_limit']) {
				wl_message(array('errno' => 1, 'message' => '超过抢购数量!'));
			}

			$nopayorder = pdo_getcolumn('wlmerchant_rush_order', array('mid' => $_W['mid'], 'status' => 0, 'activityid' => $id), 'id');

			if ($nopayorder) {
				wl_message(array('errno' => 5, 'message' => '您有未支付订单!'));
			}

			wl_message(array('errno' => 0, 'message' => 'ok'));
		}
		else {
			include wl_template('home/catch_success');
		}
	}

	public function paySuccess()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '订单详情 - ' . $_W['wlsetting']['base']['name'] : '订单详情';
		$id = $_GPC['orderid'];

		if (empty($id)) {
			wl_message('缺少重要参数');
		}

		$url = app_url('rush/home/hexiao', array('id' => $id));
		$order_out = pdo_get(PDO_NAME . 'rush_order', array('id' => $id));
		if ($order_out['status'] == 6 || $order_out['status'] == 7) {
			include wl_template('home/buyFail');
			exit();
		}
		else if ($order_out['status'] == 1) {
			$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $order_out['sid']));
			$goods = pdo_get('wlmerchant_rush_activity', array('id' => $order_out['activityid']));
			$order_out['levelnum'] = intval($order_out['num'] - $order_out['usetimes']);
			$hxurl = app_url('rush/home/hexiaokey', array('sid' => $merchant['id'], 'orderid' => $id));

			if ($order_out['optionid']) {
				$option = pdo_get('wlmerchant_goods_option', array('id' => $order_out['optionid']), array('price', 'vipprice', 'title'));
				$goods['price'] = $option['price'];
				$goods['vipprice'] = $option['vipprice'];
			}

			if ($order_out['vipbuyflag']) {
				$goods['price'] = $goods['vipprice'];
			}

			$order_out['price'] = $order_out['actualprice'];
			$location = unserialize($merchant['location']);
			$dkmoney = $order_out['dkmoney'];
			$dkcredit = $order_out['dkcredit'];
			include wl_template('order/orderdetail');
			exit();
		}
		else {
			wl_message('该订单已核销或已退款');
		}
	}

	public function getOrderDetail()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];

		if (empty($id)) {
			wl_message('缺少重要参数');
		}

		$order = Rush::getSingleOrder($id, '*');
		$goods = Rush::getSingleActive($order['activityid'], '*');
		$order['storename'] = Util::idSwitch('sid', 'sName', $goods['sid']);
		$order['goodsname'] = $goods['name'];
		$order['describe'] = $goods['describe'];
		$order['goodsimg'] = tomedia($goods['thumb']);
		$order['cutofftime'] = date('Y-m-d', $goods['cutofftime']);
		$order['sa'] = app_url('store/merchant/detail', array('id' => $goods['sid']));
		exit(json_encode(array('errno' => 0, 'data' => $order, 'img' => '$img')));
	}

	public function hexiao()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '订单核销 - ' . $_W['wlsetting']['base']['name'] : '订单核销';
		$id = $_GPC['id'];
		$order_out = pdo_get('wlmerchant_rush_order', array('id' => $id));

		if (empty($_W['mid'])) {
			$_W['mid'] = pdo_getcolumn(PDO_NAME . 'member', array('openid' => $_W['openid']), 'id');
		}

		$verifier = SingleMerchant::verifier($order_out['sid'], $_W['mid']);
		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $order_out['sid']));
		$goods = pdo_get('wlmerchant_rush_activity', array('id' => $order_out['activityid']));
		$type = 'rush';

		if ($order_out['neworderflag']) {
			$order_out['levelnum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'' . $type . '\' AND  orderid = ' . $id . ' AND status != 1'));
			$order_out['usetimes'] = $order_out['num'] - $order_out['levelnum'];
		}
		else {
			$order_out['levelnum'] = intval($order_out['num'] - $order_out['usetimes']);
		}

		if ($order_out['optionid']) {
			$option = pdo_get('wlmerchant_goods_option', array('id' => $order_out['optionid']), array('price', 'vipprice', 'title'));
			$goods['price'] = $option['price'];
			$goods['vipprice'] = $option['vipprice'];
		}

		if ($order_out['vipbuyflag']) {
			$goods['price'] = $goods['vipprice'];
		}

		include wl_template('order/hexiaoorder');
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
			$res = Rush::hexiaoorder($orderid, 0, $num, 4);

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

	public function xiaofei()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$num = $_GPC['num'];

		if (empty($_W['mid'])) {
			$_W['mid'] = pdo_getcolumn(PDO_NAME . 'member', array('openid' => $_W['openid']), 'id');
		}

		$res = Rush::hexiaoorder($id, $_W['mid'], $num, 2);

		if ($res) {
			exit(json_encode(array('errno' => 0, 'message' => '核销成功')));
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '核销失败')));
		}
	}

	public function specialindex()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];

		if (empty($id)) {
			wl_message('缺少重要参数');
		}

		$special = pdo_get('wlmerchant_rush_special', array('id' => $id));
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $special['title'] . ' - ' . $_W['wlsetting']['base']['name'] : $special['title'];
		$rushs = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_rush_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND specialid = ' . $id . ' AND status = 2 ORDER BY sort DESC,id DESC'));

		if ($rushs) {
			foreach ($rushs as $key => &$v) {
				$store = pdo_get('wlmerchant_merchantdata', array('id' => $v['sid']), array('storename', 'logo', 'location'));
				$v['storename'] = $store['storename'];
				$v['storelogo'] = tomedia($store['logo']);
				$v['href'] = app_url('rush/home/detail', array('id' => $v['id']));
				$v['endtime'] = date('Y-m-d H:i', $v['endtime']);
				$v['thumb'] = tomedia($v['thumb']);
			}
		}

		$_W['wlsetting']['share']['share_title'] = $special['share_title'];
		$_W['wlsetting']['share']['share_desc'] = $special['share_desc'];
		$_W['wlsetting']['share']['share_image'] = $special['thumb'];
		include wl_template('home/specialindex');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
