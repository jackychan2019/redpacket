<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Fightapp_WeliamController extends Weliam_merchantModuleSite
{
	public function fightindex()
	{
		global $_W;
		global $_GPC;

		if ($_W['agentset']['diypageset']['page_wlfightgroup']) {
			header('Location:' . app_url('diypage/diyhome/home', array('aid' => $_W['aid'], 'id' => $_W['agentset']['diypageset']['page_wlfightgroup'])));
			exit();
		}

		$meroof = Setting::agentsetting_read('meroof');
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '拼团首页 - ' . $_W['wlsetting']['base']['name'] : '拼团首页';
		$set = Setting::agentsetting_read('fightgroup');
		$searchSet = array('search_float' => $set['search_float'], 'search_bgColor' => $set['search_bgColor']);
		$advs = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_adv') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND enabled = 1 AND type = 8 AND aid = ' . $_W['aid'] . ' ORDER BY displayorder DESC'));
		$nav = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_fightgroup_category') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND is_show = 0 AND aid = ' . $_W['aid'] . ' ORDER BY listorder DESC'));

		foreach ($nav as $key => &$v) {
			$v['link'] = app_url('wlfightgroup/fightapp/goodsindex', array('cateid' => $v['id']));
		}

		$wheres = array();
		$wheres['uniacid'] = $_W['uniacid'];
		$wheres['aid'] = $_W['aid'];
		$wheres['status'] = 1;
		$goodslist = Wlfightgroup::getNumGoods('*', $wheres, 'listorder DESC', 0, 0, 0);
		$list = $goodslist[0];
		if ($set['share_title'] || $set['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];

			if ($set['share_title']) {
				$title = $set['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[系统名称]', $sysname, $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}
			else {
				$_W['wlsetting']['share']['share_title'] = !empty($set['share_title']) ? $set['share_title'] : $_W['wlsetting']['share']['share_title'];
			}

			if ($set['share_desc']) {
				$desc = $set['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[系统名称]', $sysname, $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
			else {
				$_W['wlsetting']['share']['share_desc'] = !empty($set['share_desc']) ? $set['share_desc'] : $_W['wlsetting']['share']['share_desc'];
			}
		}
		else {
			$_W['wlsetting']['share']['share_title'] = !empty($set['share_title']) ? $set['share_title'] : $_W['wlsetting']['share']['share_title'];
			$_W['wlsetting']['share']['share_desc'] = !empty($set['share_desc']) ? $set['share_desc'] : $_W['wlsetting']['share']['share_desc'];
		}

		$_W['wlsetting']['share']['share_image'] = !empty($set['share_image']) ? $set['share_image'] : $_W['wlsetting']['share']['share_image'];

		if (p('diypage')) {
			if (!empty($_W['agentset']['diypageset']['adv_wlfightgroup'])) {
				$advdata = Diy::BeOverdue($_W['agentset']['diypageset']['adv_wlfightgroup']);
			}

			if (!empty($_W['agentset']['diypageset']['menu_wlfightgroup'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_wlfightgroup']);
			}
		}

		$page_type = 6;
		include wl_template('fightgrouphtml/index');
	}

	public function goodsindex()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '拼团列表 - ' . $_W['wlsetting']['base']['name'] : '拼团列表';
		$id = $_GPC['cateid'];
		$name = pdo_get('wlmerchant_fightgroup_category', array('id' => $id), array('name'));
		$name = $name['name'];
		include wl_template('fightgrouphtml/goodsindex');
	}

	public function getindexgoods()
	{
		global $_W;
		global $_GPC;
		$lng = !empty($_GPC['lng']) ? $_GPC['lng'] : $_SESSION['lng'];
		$lat = !empty($_GPC['lat']) ? $_GPC['lat'] : $_SESSION['lat'];
		$wheres = array();
		$set = Setting::agentsetting_read('fightgroup');

		if ($_GPC['id']) {
			$wheres['id!='] = $_GPC['id'];
		}

		if ($set['shout']) {
			$shout = rtrim($set['shout'], ',');
			$shout = explode(',', $shout);
		}

		if ($_GPC['cateid']) {
			$wheres['categoryid'] = $_GPC['cateid'];
		}

		$wheres['uniacid'] = $_W['uniacid'];
		$wheres['aid'] = $_W['aid'];
		$wheres['status'] = 1;
		$goodslist = Wlfightgroup::getNumGoods('id,name,logo,price,peoplenum,oldprice,realsalenum,falsesalenum,unit,listorder,vipdiscount,merchantid', $wheres, 'listorder DESC', 0, 0, 0);
		$data = $goodslist[0];

		foreach ($data as $key => &$v) {
			$location = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $v['merchantid']), 'location');
			$location = unserialize($location);
			$v['distance'] = Store::getdistance($lng, $lat, $location['lng'], $location['lat']);
			$v['disc'] = round($v['price'] / $v['oldprice'], 1);
			$v['href'] = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $v['id']));
			$v['logo'] = tomedia($v['logo']);
			$v['salenum'] = $v['realsalenum'] + $v['falsesalenum'];
			$group = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_fightgroup_group') . ('WHERE status = 1 AND goodsid = ' . $v['id'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . '  ORDER BY starttime  ASC LIMIT 3'));

			if ($group) {
				foreach ($group as $key => $gr) {
					$order = pdo_fetch('SELECT mid FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND fightgroupid = ' . $gr['id'] . ' ORDER BY createtime ASC LIMIT 3'));
					$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('avatar'));
					$v['head'][] = tomedia($member['avatar']);

					if ($shout) {
						$flag = rand(0, count($shout) - 1);
						$v['tip'] = $shout[$flag];
					}
					else {
						$v['tip'] = '等你加入';
					}
				}
			}
		}

		if ($_GPC['pageflag'] == 1) {
			$plugin = Setting::agentsetting_read('pluginlist');

			switch ($plugin['ptsort']) {
			case '1':
				$sort_key = 'createtime';
				$sort_order = SORT_DESC;
				break;

			case '2':
				$sort_key = 'distance';
				$sort_order = SORT_ASC;
				break;

			case '3':
				$sort_key = 'listorder';
				$sort_order = SORT_DESC;
				break;

			case '4':
				$sort_key = 'pv';
				$sort_order = SORT_DESC;
				break;

			default:
				$sort_key = 'distance';
				$sort_order = SORT_DESC;
				break;
			}

			$data = Store::wl_sort($data, $sort_key, $sort_order);
			$limit = $plugin['ptlimit'] ? $plugin['ptlimit'] : 10;
			$data = array_slice($data, 0, $limit);
		}

		exit(json_encode($data));
	}

	public function goodsdetail()
	{
		global $_W;
		global $_GPC;
		$type = 'wlfightgroup';
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商品详情 - ' . $_W['wlsetting']['base']['name'] : '商品详情';
		$id = $_GPC['id'];
		$good = Wlfightgroup::getSingleGood($id, '*');
		$goods = $good;
		$good['detail'] = nl2br($good['detail']);
		pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET pv=pv+1 WHERE id = ' . $good['merchantid']));
		pdo_fetch('update' . tablename(PDO_NAME . 'fightgroup_goods') . ('SET pv=pv+1 WHERE id = ' . $id));
		$advs = unserialize($good['adv']);
		$tag = unserialize($good['tag']);

		foreach ($tag as $key => $va) {
			$tags[] = pdo_getcolumn(PDO_NAME . 'tags', array('id' => $va), 'title');
		}

		$set = Setting::agentsetting_read('fightgroup');

		if ($good['buylimit']) {
			$arbuy = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND fkid = ' . $good['id'] . ' AND mid = ' . $_W['mid'] . ' AND plugin = \'wlfightgroup\' AND status IN (1,2,3,4,8)'));
			$good['buylimit'] = $good['buylimit'] - $arbuy;
		}
		else {
			$good['buylimit'] = 9999;
		}

		$merchant = Wlfightgroup::getSingleMerchant($good['merchantid'], '*');

		if ($merchant['enabled'] != 1) {
			pdo_update('wlmerchant_fightgroup_goods', array('status' => 0), array('id' => $id));
			$good['status'] = 0;
		}

		if ($merchant['store_quhao']) {
			$merchant['mobile'] = $merchant['store_quhao'] . $merchant['mobile'];
		}

		$location = unserialize($merchant['location']);
		$storehours = unserialize($merchant['storehours']);

		if ($good['specstatus'] == 1) {
			$specs = pdo_getall('wlmerchant_goods_spec', array('goodsid' => $id, 'type' => 2), array('title', 'content'), '', 'displayorder ASC');

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
		}

		if ($good['islimittime']) {
			if (time() < $good['limitstarttime']) {
				$good['timelimitstatus'] = 2;
				$good['sytime'] = $good['limitstarttime'] - time();
			}
			else if ($good['limitendtime'] < time()) {
				$good['timelimitstatus'] = 3;
			}
			else {
				$good['timelimitstatus'] = 1;
				$good['sytime'] = $good['limitendtime'] - time();
			}
		}

		$neargroup = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_fightgroup_group') . ('WHERE goodsid = ' . $id . ' AND status = 1 AND aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' ORDER BY starttime ASC limit 3'));

		if ($neargroup) {
			foreach ($neargroup as $key => &$v) {
				$order = pdo_fetch('SELECT mid FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND fightgroupid = ' . $v['id'] . ' ORDER BY createtime ASC'));
				$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('nickname', 'avatar'));
				$v['avatar'] = $member['avatar'];
				$v['nickname'] = $member['nickname'];
				$v['surplustime'] = $v['failtime'] - time();
				if (empty($degroupid) && $set['remind']) {
					$res = pdo_get('wlmerchant_order', array('mid' => $_W['mid'], 'fightgroupid' => $v['id']), array('orderno'));

					if (!$res) {
						$degroupid = $v['id'];
					}
				}
			}
		}

		$comorders = pdo_fetchall('SELECT id,mid FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND plugin = \'wlfightgroup\' AND status = 3 AND fkid = ' . $id . ' ORDER BY id DESC limit 10'));
		$ordersIdStr = '';

		foreach ($comorders as $key => &$corder) {
			$ordersIdStr .= $corder['id'] . ',';
		}

		$ordersIdStr = trim($ordersIdStr, ',');
		$comments = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_comment') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND checkone = 2 AND plugin = \'wlfightgroup\'  AND idoforder IN (' . $ordersIdStr . ') ORDER BY id DESC'));

		if ($comments) {
			foreach ($comments as $key => &$com) {
				$com['createtime'] = date('Y-m-d H:i:s', $com['createtime']);
				$com['headimg'] = tomedia($com['headimg']);
				$com['pic'] = unserialize($com['pic']);
			}
		}

		$commnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_comment') . ('WHERE sid = ' . $good['merchantid'] . ' AND aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid']));
		if ($good['share_title'] || $good['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());

			if ($good['share_title']) {
				$title = $good['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[商品名称]', $good['name'], $title);
				$title = str_replace('[商户名称]', $merchant['storename'], $title);
				$title = str_replace('[拼团价]', $good['price'], $title);
				$title = str_replace('[单购价]', $good['aloneprice'], $title);
				$title = str_replace('[会员减免金额]', $good['vipdiscount'], $title);
				$title = str_replace('[开团人数]', $good['peoplenum'], $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}
			else {
				$_W['wlsetting']['share']['share_title'] = !empty($good['share_title']) ? $good['share_title'] : $good['name'];
			}

			if ($good['share_desc']) {
				$desc = $good['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[商品名称]', $good['name'], $desc);
				$desc = str_replace('[商户名称]', $merchant['storename'], $desc);
				$desc = str_replace('[拼团价]', $good['price'], $desc);
				$desc = str_replace('[单购价]', $good['aloneprice'], $desc);
				$desc = str_replace('[会员减免金额]', $good['vipdiscount'], $desc);
				$desc = str_replace('[开团人数]', $good['peoplenum'], $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
			else {
				$_W['wlsetting']['share']['share_desc'] = !empty($good['share_desc']) ? $good['share_desc'] : $good['share_desc'];
			}
		}
		else {
			$_W['wlsetting']['share']['share_title'] = !empty($good['share_title']) ? $good['share_title'] : $good['name'];
			$_W['wlsetting']['share']['share_desc'] = !empty($good['share_desc']) ? $good['share_desc'] : $good['share_desc'];
		}

		$_W['wlsetting']['share']['share_image'] = !empty($good['share_image']) ? $good['share_image'] : $good['logo'];
		$checkhalfmember = Member::checkhalfmember();
		include wl_template('fightgrouphtml/goodsdetail');
	}

	public function groupdetail()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '团详情 - ' . $_W['wlsetting']['base']['name'] : '团详情';
		$id = $_GPC['id'];
		$group = pdo_get('wlmerchant_fightgroup_group', array('id' => $id));

		if (time() < $group['failtime']) {
			$group['surplustime'] = $group['failtime'] - time();
		}

		$good = Wlfightgroup::getSingleGood($group['goodsid'], '*');

		if ($good['buylimit']) {
			$arbuy = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND fkid = ' . $good['id'] . ' AND mid = ' . $_W['mid'] . ' AND plugin = \'wlfightgroup\' AND status IN (1,2,3,4,8)'));
			$good['buylimit'] = $good['buylimit'] - $arbuy;
		}
		else {
			$good['buylimit'] = 9999;
		}

		$merchant = Wlfightgroup::getSingleMerchant($good['merchantid'], 'storename');
		$orders = pdo_fetchall('SELECT mid,createtime,orderno FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND fightgroupid = ' . $group['id'] . ' AND status != 0 ORDER BY createtime ASC'));

		foreach ($orders as $key => &$v) {
			if ($v['orderno'] == '666666') {
				if ($v['mid']) {
					$member = pdo_get('wlmerchant_fightgroup_falsemember', array('id' => $v['mid']), array('nickname', 'avatar'));
				}
				else {
					$member = pdo_get('wlmerchant_fightgroup_falsemember', array('id' => 1), array('nickname', 'avatar'));
				}

				$v['avatar'] = tomedia($member['avatar']);
				$v['nickname'] = $member['nickname'];
			}
			else {
				$member = pdo_get('wlmerchant_member', array('id' => $v['mid']), array('nickname', 'avatar'));
				$v['avatar'] = tomedia($member['avatar']);
				$v['nickname'] = $member['nickname'];
			}
		}

		$num_arr = array();
		$i = 0;

		while ($i < $group['lacknum']) {
			$num_arr[$i] = $i;
			++$i;
		}

		if ($good['specstatus']) {
			$specs = pdo_getall('wlmerchant_goods_spec', array('goodsid' => $group['goodsid'], 'type' => 2), array('title', 'content'), '', 'displayorder ASC');

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
		}

		$flag = pdo_get('wlmerchant_order', array('fightgroupid' => $id, 'mid' => $_W['mid'], 'status' => 1));
		if ($good['share_title'] || $good['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());

			if ($good['share_title']) {
				$title = $good['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[商品名称]', $good['name'], $title);
				$title = str_replace('[商户名称]', $merchant['storename'], $title);
				$title = str_replace('[拼团价]', $good['price'], $title);
				$title = str_replace('[单购价]', $good['aloneprice'], $title);
				$title = str_replace('[会员减免金额]', $good['vipdiscount'], $title);
				$title = str_replace('[开团人数]', $good['peoplenum'], $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}
			else {
				$_W['wlsetting']['share']['share_title'] = !empty($good['share_title']) ? $good['share_title'] : $good['name'];
			}

			if ($good['share_desc']) {
				$desc = $good['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[商品名称]', $good['name'], $desc);
				$desc = str_replace('[商户名称]', $merchant['storename'], $desc);
				$desc = str_replace('[拼团价]', $good['price'], $desc);
				$desc = str_replace('[单购价]', $good['aloneprice'], $desc);
				$desc = str_replace('[会员减免金额]', $good['vipdiscount'], $desc);
				$desc = str_replace('[开团人数]', $good['peoplenum'], $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
			else {
				$_W['wlsetting']['share']['share_desc'] = !empty($good['share_desc']) ? $good['share_desc'] : $good['share_desc'];
			}
		}
		else {
			$_W['wlsetting']['share']['share_title'] = !empty($good['share_title']) ? $good['share_title'] : $good['name'];
			$_W['wlsetting']['share']['share_desc'] = !empty($good['share_desc']) ? $good['share_desc'] : $good['share_desc'];
		}

		$_W['wlsetting']['share']['share_image'] = !empty($good['share_image']) ? $good['share_image'] : $good['logo'];
		include wl_template('fightgrouphtml/groupdetail');
	}

	public function getoption()
	{
		global $_W;
		global $_GPC;
		$specs = trim($_GPC['specids'], '_');
		$buystatus = $_GPC['buystatus'];

		if (empty($specs)) {
			wl_json(2, 'error');
		}

		$option = pdo_get('wlmerchant_goods_option', array('specs' => $specs), array('id', 'stock', 'price', 'vipprice'));

		if ($option) {
			if ($buystatus == 1) {
				$price = $option['price'];
			}
			else {
				$price = $option['vipprice'];
			}

			$data = array('price' => $price, 'optionid' => $option['id'], 'sort' => $option['stock']);

			if ($option['stock'] < 1) {
				wl_json(1, '抱歉，该规格库存不足', $data);
			}
			else {
				wl_json(0, 'success', $data);
			}
		}
		else {
			wl_json(2, 'error');
		}
	}

	public function topayFight()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '确认订单 - ' . $_W['wlsetting']['base']['name'] : '确认订单';
		$id = $_GPC['id'];
		$buystatus = $_GPC['buystatus'];
		$spec = $_GPC['spec'];
		$specid = $_GPC['optionid'];
		$groupid = $_GPC['groupid'];
		$good = Wlfightgroup::getSingleGood($id, '*');

		if ($good['buylimit']) {
			$arbuy = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND fkid = ' . $good['id'] . ' AND mid = ' . $_W['mid'] . ' AND plugin = \'wlfightgroup\' AND status IN (1,2,3,4,8)'));
			if ($good['buylimit'] < $arbuy || $arbuy == $good['buylimit']) {
				wl_message('您已达到商品购买数量上限', referer(), 'error');
			}
			else {
				$good['buylimit'] = $good['buylimit'] - $arbuy;
			}
		}

		$set = Setting::agentsetting_read('fightgroup');
		Member::checklogin(app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $id)));

		if ($good['status'] != 1) {
			wl_message('该商品已下架，请您选购其他商品。', app_url('wlfightgroup/fightapp/fightindex'), 'error');
		}

		if ($buystatus == 1 && $set['onlyone']) {
			$orders = pdo_fetchall('SELECT fightgroupid FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND fkid = ' . $id . ' AND fightgroupid > 0 AND mid = ' . $_W['mid'] . ' ORDER BY id DESC'));

			if ($orders) {
				foreach ($orders as $key => $gr) {
					$flag = pdo_getcolumn(PDO_NAME . 'fightgroup_group', array('id' => $gr['fightgroupid']), 'status');

					if ($flag == 1) {
						wl_message('您要在上一个团结束后方可参与新团', referer(), 'error');
					}
				}
			}
		}

		if ($good['islimittime']) {
			if (time() < $good['limitstarttime']) {
				wl_message('该商品未到发售时间', referer(), 'error');
			}

			if ($good['limitendtime'] < time()) {
				wl_message('该商品已停止发售', referer(), 'error');
			}
		}

		if ($good['specstatus']) {
			if ($specid) {
				$option = pdo_get('wlmerchant_goods_option', array('id' => $specid));
				$buylimit = min($option['stock'], $good['buylimit']);

				if ($buystatus == 1) {
					$goodprice = $option['price'];
				}
				else {
					$goodprice = $option['vipprice'];
				}
			}
			else {
				wl_message('商品规格错误，请重新选择', referer(), 'error');
			}
		}
		else {
			if ($buystatus == 1) {
				$goodprice = $good['price'];
			}
			else {
				$goodprice = $good['aloneprice'];
			}

			$buylimit = min($good['stock'], $good['buylimit']);
		}

		$member = pdo_get('wlmerchant_member', array('id' => $_W['mid']), array('mobile'));

		if ($member['mobile']) {
			$mobile = substr($member['mobile'], 0, 3) . '****' . substr($member['mobile'], -4, 4);
		}
		else {
			$mobile = '未绑定手机';
		}

		$halfcardflag = Member::checkhalfmember();

		if ($halfcardflag) {
			$vipdiscount = 0 - $good['vipdiscount'];
			$price = $goodprice + $vipdiscount;
		}
		else {
			$price = $goodprice;
		}

		if ($good['markid']) {
			$mark = pdo_get('wlmerchant_marking', array('id' => $good['markid']));
			if (0 < $mark['creditmoney'] && 0 < $mark['deduct']) {
				$maxcredit = floor($mark['deduct'] / $mark['creditmoney']);
				$membercredit = floor($_W['member']['credit1']);
				$creditmoney = sprintf('%.2f', $mark['creditmoney']);
				$deduct = sprintf('%.2f', $mark['deduct']);
			}
		}

		if ($good['usestatus']) {
			if ($_GPC['addressid']) {
				$address = pdo_get('wlmerchant_address', array('id' => $_GPC['addressid']));
			}
			else {
				$address = pdo_get('wlmerchant_address', array('mid' => $_W['mid'], 'status' => 1));

				if (empty($address)) {
					$address = pdo_get('wlmerchant_address', array('mid' => $_W['mid']));
				}
			}

			if ($address) {
				$addressid = $address['id'];

				if ($good['expressid']) {
					$express = pdo_get('wlmerchant_express_template', array('id' => $good['expressid']));

					if ($express['expressarray']) {
						$expressarray = unserialize($express['expressarray']);

						foreach ($expressarray as $key => &$v) {
							$v['area'] = rtrim($v['area'], ',');
							$v['area'] = explode(',', $v['area']);

							if (in_array($address['province'], $v['area'])) {
								$expressprice = $v['money'];
							}
						}
					}

					if (empty($expressprice)) {
						$expressprice = $express['defaultmoney'];
					}
				}
			}
			else {
				$address = 0;
			}
		}

		$url = app_url('wlfightgroup/fightapp/topayFight', array('id' => $id, 'buystatus' => $buystatus, 'spec' => $spec, 'optionid' => $specid, 'groupid' => $groupid));
		include wl_template('fightgrouphtml/topayFight');
	}

	public function calculatefreight()
	{
		global $_W;
		global $_GPC;
		$num = $_GPC['num'];
		$expressid = $_GPC['expressid'];
		$addressid = $_GPC['addressid'];
		$address = pdo_get('wlmerchant_address', array('id' => $_GPC['addressid']));
		$express = pdo_get('wlmerchant_express_template', array('id' => $expressid));

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

		if ($expressprice) {
			exit(json_encode(array('status' => 1, 'price' => $expressprice)));
		}
		else {
			exit(json_encode(array('status' => 0, 'price' => 'error')));
		}
	}

	public function createFightorder()
	{
		global $_W;
		global $_GPC;
		$set = Setting::agentsetting_read('fightgroup');

		if ($_W['ispost']) {
			$id = $_GPC['id'];
			$buystatus = $_GPC['buystatus'];
			$usestatus = $_GPC['usestatus'];
			$spec = $_GPC['spec'];
			$specid = $_GPC['specid'];
			$groupid = $_GPC['groupid'];
			$buyremark = $_GPC['remark'];
			$dk = $_GPC['dk'];
			$goodnum = $_GPC['num'];
			$cardId = $_GPC['cardId'];
			$good = Wlfightgroup::getSingleGood($id, '*');

			if ($groupid) {
				$group = pdo_get('wlmerchant_fightgroup_group', array('id' => $groupid));
				if ($group['status'] == 2 || $group['lacknum'] == 0) {
					wl_message(array('errno' => 3, 'message' => '该团已经组团成功，请您新开一团。'));
				}
				else {
					if ($group['status'] == 3 || $group['failtime'] < time()) {
						wl_message(array('errno' => 3, 'message' => '该团已经组团失败，请您新开一团。'));
					}
				}
			}

			if ($good['islimittime']) {
				if (time() < $good['limitstarttime']) {
					wl_message(array('errno' => 1, 'message' => '该商品未到发售时间'));
				}

				if ($good['limitendtime'] < time()) {
					wl_message(array('errno' => 1, 'message' => '该商品已停止发售'));
				}
			}

			if ($good['status'] != 1) {
				wl_message(array('errno' => 1, 'message' => '抱歉，商品已下架'));
			}

			if ($good['buylimit']) {
				$arbuy = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND fkid = ' . $good['id'] . ' AND mid = ' . $_W['mid'] . ' AND plugin = \'wlfightgroup\' AND status IN (1,2,3,4,8)'));
				if ($good['buylimit'] < $arbuy || $arbuy == $good['buylimit']) {
					wl_message(array('errno' => 1, 'message' => '抱歉，您已达到商品购买数量上限'));
				}
				else {
					$good['buylimit'] = $good['buylimit'] - $arbuy;
				}
			}

			if ($good['specstatus']) {
				if ($specid) {
					$option = pdo_get('wlmerchant_goods_option', array('id' => $specid));
					$buylimit = min($option['stock'], $good['buylimit']);

					if ($buystatus == 1) {
						$price = $option['price'];
					}
					else {
						$price = $option['vipprice'];
					}
				}
				else {
					wl_message(array('errno' => 1, 'message' => '商品规格错误，请返回重新选择'));
				}
			}
			else if ($buystatus == 1) {
				$price = $good['price'];
			}
			else {
				$price = $good['aloneprice'];
			}

			$goodsprice = $price * $goodnum;
			$member = Util::getSingelData('mobile', PDO_NAME . 'member', array('id' => $_W['mid']));
			if ($good['markid'] && $dk == 'true') {
				$mark = pdo_get('wlmerchant_marking', array('id' => $good['markid']));
				if (0 < $mark['creditmoney'] && 0 < $mark['deduct']) {
					$allcredit = sprintf('%.0f', $_W['member']['credit1']);
					$dkcredit = sprintf('%.0f', $mark['deduct'] * $goodnum / $mark['creditmoney']);
					$dkcredit = $dkcredit;

					if ($allcredit < $dkcredit) {
						$dkcredit = $allcredit;
					}

					$dkmoney = sprintf('%.2f', $dkcredit * $mark['creditmoney']);
					$credit_fee = $dkmoney;
					$credit = $dkcredit;
				}
			}
			else {
				$credit_fee = 0;
				$credit = 0;
			}

			$halfcardflag = Member::checkhalfmember();
			if ($halfcardflag || !empty($cardId)) {
				$vipdiscount = 0 - $good['vipdiscount'];
				$price = $price + $vipdiscount;
				$vipbuyflag = 1;

				if (0 < $credit_fee) {
					$card_type = 3;
					$card_fee = $good['vipdiscount'] * $goodnum + $credit_fee;
				}
				else {
					$card_type = 1;
					$card_fee = $good['vipdiscount'] * $goodnum;
				}
			}
			else if (0 < $credit_fee) {
				$card_type = 2;
				$card_fee = $credit_fee;
			}
			else {
				$price = $price;
				$card_type = 0;
				$card_fee = 0;
			}

			if (empty($vipbuyflag)) {
				$vipbuyflag = 0;
			}

			$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
			if (empty($member['mobile']) && in_array('fightgroup', $mastmobile)) {
				wl_message(array('errno' => 2, 'message' => '未绑定手机号，去绑定？'));
			}

			if ($good['stock'] < $goodnum) {
				wl_message(array('errno' => 1, 'message' => '抱歉，商品库存不足'));
			}

			if ($usestatus) {
				$addressid = $_GPC['addressid'];
				pdo_update('wlmerchant_address', array('status' => 0), array('mid' => $_W['mid'], 'uniacid' => $_W['uniacid']));
				pdo_update('wlmerchant_address', array('status' => 1), array('id' => $addressid));
				$address = pdo_get('wlmerchant_address', array('id' => $addressid));
				$data['uniacid'] = $_W['uniacid'];
				$data['mid'] = $_W['mid'];
				$data['goodsid'] = $good['id'];
				$data['merchantid'] = $good['merchantid'];
				$data['address'] = $addre = $address['province'] . $address['city'] . $address['county'] . $address['detailed_address'];
				$data['name'] = $username = $address['name'];
				$data['tel'] = $mobile = $address['tel'];

				if ($good['expressid']) {
					$express = pdo_get('wlmerchant_express_template', array('id' => $good['expressid']));

					if (mb_substr($address['province'], -1, 1, 'utf-8') == '省') {
						$address['province'] = mb_substr($address['province'], 0, mb_strlen($address['province']) - 1, 'utf-8');
					}

					if ($express['expressarray']) {
						$expressarray = unserialize($express['expressarray']);

						foreach ($expressarray as $key => &$v) {
							$v['area'] = rtrim($v['area'], ',');
							$v['area'] = explode(',', $v['area']);

							if (in_array($address['province'], $v['area'])) {
								if ($v['num'] < $goodnum) {
									$expressprice = $v['money'] + ceil(($goodnum - $v['num']) / $v['numex']) * $v['moneyex'];
								}
								else {
									$expressprice = $v['money'];
								}
							}
						}
					}

					if (empty($expressprice)) {
						if ($express['defaultnum'] < $goodnum) {
							$expressprice = $express['defaultmoney'] + ceil(($goodnum - $express['defaultnum']) / $express['defaultnumex']) * $express['defaultmoneyex'];
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
				$neworderflag = 0;
			}
			else {
				$username = trim($_GPC['thname']);
				$mobile = trim($_GPC['thmobile']);
				$expressprice = 0;
				$neworderflag = 1;
			}

			if ($buystatus) {
				$buyflah = 0;
			}
			else {
				$buyflah = 1;
			}

			$settlementmoney = Store::getsettlementmoney(2, $id, $goodnum, $good['merchantid'], 0, $specid, $buyflah);
			$settlementmoney = $settlementmoney + $expressprice;
			$orderprice = $price * $goodnum + $expressprice - $credit_fee;
			$data = array('uniacid' => $good['uniacid'], 'mid' => $_W['mid'], 'aid' => $good['aid'], 'fkid' => $id, 'sid' => $good['merchantid'], 'status' => 0, 'paytype' => 0, 'createtime' => time(), 'orderno' => createUniontid(), 'price' => $orderprice, 'num' => $goodnum, 'plugin' => 'wlfightgroup', 'payfor' => 'fightsharge', 'spec' => $spec, 'specid' => $specid, 'name' => $username, 'mobile' => $mobile, 'address' => $addre, 'fightstatus' => $buystatus, 'fightgroupid' => $groupid, 'expressid' => $expressid, 'buyremark' => $buyremark, 'card_type' => $card_type, 'card_id' => $credit, 'card_fee' => $card_fee, 'vipbuyflag' => $vipbuyflag, 'goodsprice' => $goodsprice, 'settlementmoney' => $settlementmoney, 'vip_card_id' => $cardId, 'neworderflag' => $neworderflag);
			$res = Wlfightgroup::saveFightOrder($data);

			if ($res) {
				wl_json(0, '下单成功', $res);
			}
			else {
				wl_json(1, '未知错误，请重试。', 0);
			}
		}

		$orderId = $_GPC['orderid'];
		$order = pdo_get('wlmerchant_order', array('id' => $orderId));

		if (empty($order)) {
			wl_message('传入的参数有误，请重试');
		}

		$good = Wlfightgroup::getSingleGood($order['fkid'], 'name');
		$bankrid = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), 'bankrid');
		$order = PayBuild::isOpenCard($order);
		$params = array('tid' => $order['orderno'], 'ordersn' => $order['orderno'], 'title' => mb_substr($good['name'], 0, 25, 'utf-8'), 'fee' => $order['price'], 'bankrid' => $bankrid);
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => 'Rush', 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Wlfightgroup', 'payfor' => 'Fightsharge', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}

	public function expressorder()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '订单详情 - ' . $_W['wlsetting']['base']['name'] : '订单详情';
		$id = $_GPC['orderid'];
		$order = Wlfightgroup::getSingleOrder($id, '*');
		$goods = pdo_get('wlmerchant_fightgroup_goods', array('id' => $order['fkid']));

		if ($order['fightgroupid']) {
			$group = pdo_get('wlmerchant_fightgroup_group', array('id' => $order['fightgroupid']));
		}

		if ($order['expressid']) {
			$express = pdo_get('wlmerchant_express', array('id' => $order['expressid']));
			$address = pdo_get('wlmerchant_address', array('id' => $express['addressid']));
			$expressprice = $express['expressprice'];
		}
		else {
			$expressprice = 0;
		}

		if ($order['recordid'] || $order['neworderflag']) {
			if ($order['recordid']) {
				$record = pdo_get('wlmerchant_fightgroup_userecord', array('id' => $order['recordid']));
				$record['usedtime'] = unserialize($record['usedtime']);
			}

			if ($order['neworderflag']) {
				$smalls = pdo_getall('wlmerchant_smallorder', array('orderid' => $id, 'plugin' => 'wlfightgroup'), array('id', 'status', 'hexiaotime', 'createtime', 'checkcode'));
				$usetimes = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE orderid = ' . $id . ' AND plugin = \'wlfightgroup\' AND status = 1 '));
			}

			$hexiaourl = app_url('wlfightgroup/fightapp/hexiao', array('id' => $id));
			$qrcodeimg = app_url('store/merchant/qrcodeimg', array('url' => $hexiaourl));
		}

		$member = pdo_get('wlmerchant_member', array('id' => $order['mid']));
		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $order['sid']));

		if ($order['card_type'] == 1) {
			$order['vipdk'] = $order['card_fee'];
		}
		else if ($order['card_type'] == 2) {
			$order['creditdk'] = $order['card_fee'];
		}
		else {
			if ($order['card_type'] == 3) {
				$order['vipdk'] = $order['num'] * $goods['vipdiscount'];
				$order['creditdk'] = $order['card_fee'] - $order['vipdk'];
			}
		}

		$order['goodprice'] = $order['price'] - $expressprice + $order['card_fee'];
		include wl_template('fightgrouphtml/expressorder');
	}

	public function qrcodeimg()
	{
		global $_W;
		global $_GPC;
		$url = $_GPC['url'];
		ob_clean();
		m('qrcode/QRcode')->png($url, false, QR_ECLEVEL_H, 4);
	}

	public function hexiaokey()
	{
		global $_W;
		global $_GPC;
		$sid = $_GPC['sid'];
		$verkey = $_GPC['verkey'];
		$orderid = $_GPC['orderid'];
		$merchantkey = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $sid), 'verkey');

		if ($verkey == $merchantkey) {
			$order = Wlfightgroup::getSingleOrder($orderid, '*');
			$record = pdo_get('wlmerchant_fightgroup_userecord', array('orderid' => $orderid));

			if ($record['usetimes']) {
				$res = Wlfightgroup::hexiaoorder($orderid, $_W['mid'], 1, 4);

				if ($res) {
					exit(json_encode(array('result' => 0, 'msg' => '核销成功')));
				}
				else {
					exit(json_encode(array('result' => 1, 'msg' => '核销失败')));
				}
			}
			else {
				exit(json_encode(array('result' => 1, 'msg' => '订单已核销')));
			}
		}
		else {
			exit(json_encode(array('result' => 1, 'msg' => '核销密码错误')));
		}
	}

	public function hexiao()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '订单核销 - ' . $_W['wlsetting']['base']['name'] : '订单核销';
		$id = $_GPC['id'];
		$order_out = pdo_get('wlmerchant_order', array('id' => $id));

		if ($order_out['status'] == 4) {
			$order_out['status'] = 1;
		}

		$verifier = SingleMerchant::verifier($order_out['sid'], $_W['mid']);
		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $order_out['sid']));
		$goods = pdo_get('wlmerchant_fightgroup_goods', array('id' => $order_out['fkid']));
		$goods['thumb'] = $goods['logo'];

		if ($goods['specstatus'] == 2) {
			$specdetail = unserialize($goods['specdetail']);

			foreach ($specdetail as $key => $v) {
				if ($order_out['spec'] == $v['diffname']) {
					if ($order_out['fightstatus'] == 2) {
						$goods['price'] = $v['diffalprice'];
					}
					else {
						$goods['price'] = $v['diffprice'];
					}
				}
			}
		}
		else {
			if ($order_out['fightstatus'] == 2) {
				$goods['price'] = $goods['aloneprice'];
			}
		}

		if ($order_out['neworderflag']) {
			$order_out['levelnum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'wlfightgroup\' AND  orderid = ' . $id . ' AND status != 1'));
			$order_out['usetimes'] = $order_out['num'] - $order_out['levelnum'];
		}
		else {
			$record = pdo_get('wlmerchant_fightgroup_userecord', array('orderid' => $id));
			$order_out['usetimes'] = $record['usetimes'];
			$order_out['levelnum'] = intval($order_out['num'] - $order_out['usetimes']);
		}

		$type = 'fightgroup';
		include wl_template('order/hexiaoorder');
	}

	public function hexiaoing()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$num = $_GPC['num'];
		$order = Wlfightgroup::getSingleOrder($id, '*');
		$record = pdo_get('wlmerchant_fightgroup_userecord', array('orderid' => $id));
		$verifier = SingleMerchant::verifier($order['sid'], $_W['mid']);

		if ($verifier) {
			$res = Wlfightgroup::hexiaoorder($id, $_W['mid'], $num, 2);

			if ($res) {
				exit(json_encode(array('result' => 0, 'msg' => '核销成功')));
			}
			else {
				exit(json_encode(array('result' => 1, 'msg' => '订单核销失败')));
			}
		}
		else {
			exit(json_encode(array('result' => 1, 'msg' => '非管理员无法核销')));
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
}

?>
