<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Grouponapp_WeliamController extends Weliam_merchantModuleSite
{
	public function grouponlist()
	{
		global $_W;
		global $_GPC;

		if ($_W['agentset']['diypageset']['page_groupon']) {
			header('Location:' . app_url('diypage/diyhome/home', array('aid' => $_W['aid'], 'id' => $_W['agentset']['diypageset']['page_groupon'])));
			exit();
		}

		$config = Setting::agentsetting_read('groupon');
		$searchSet = array('search_float' => $config['search_float'], 'search_bgColor' => $config['search_bgColor']);
		$listtitle = $config['listtitle'] ? $config['listtitle'] : '团购列表';
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $listtitle . ' - ' . $_W['wlsetting']['base']['name'] : $listtitle;
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
			if (!empty($_W['agentset']['diypageset']['adv_groupon'])) {
				$advdata = Diy::BeOverdue($_W['agentset']['diypageset']['adv_groupon']);
			}

			if (!empty($_W['agentset']['diypageset']['menu_groupon'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_groupon']);
			}
		}

		$page_type = 4;
		include wl_template('grouponhtml/grouponlist');
	}

	public function grouponcate()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '团购列表 - ' . $_W['wlsetting']['base']['name'] : '团购列表';

		if (p('diypage')) {
			if (!empty($_W['agentset']['diypageset']['adv_groupon'])) {
				$advdata = Diy::BeOverdue($_W['agentset']['diypageset']['adv_groupon']);
			}

			if (!empty($_W['agentset']['diypageset']['menu_groupon'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_groupon']);
			}
		}

		$page_type = 4;
		include wl_template('grouponhtml/grouponcate');
	}

	public function groupondetail()
	{
		global $_W;
		global $_GPC;

		if (empty($_W['mid'])) {
			header('location:' . app_url('member/user/signin'));
		}

		$set = Setting::agentsetting_read('groupon');
		$type = 'groupon';
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '团购详情 - ' . $_W['wlsetting']['base']['name'] : '团购详情';
		$id = $_GPC['cid'];
		$goods = Groupon::getSingleActive($id, '*');
		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $goods['sid']), array('storename', 'enabled'));

		if ($merchant['enabled'] != 1) {
			pdo_update('wlmerchant_groupon_activity', array('status' => 0), array('id' => $id));
		}

		$collects = pdo_get(PDO_NAME . 'collect', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'storeid' => $goods['sid']), 'id');

		if ($goods['sharestatus'] == 2) {
			$invitid = $_GPC['invitid'];
			if ($invitid && $invitid != $_W['mid']) {
				$alshare2 = pdo_getcolumn(PDO_NAME . 'sharegift_record', array('uniacid' => $_W['uniacid'], 'plugin' => 2, 'mid' => $invitid, 'buymid' => $_W['mid'], 'goodsid' => $id, 'status' => 0, 'type' => 2), 'id');
				if (empty($alshare2) && $_W['mid']) {
					Sharegift::addrecord($id, $invitid, $_W['mid'], $goods['sharestatus'], $goods['sharemoney'], 2);
				}
			}
		}

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
				$title = str_replace('[团购价]', $goods['price'], $title);
				$title = str_replace('[特权类型]', $vipstatus, $title);
				$title = str_replace('[会员价]', $goods['vipprice'], $title);
				$title = str_replace('[原价]', $goods['oldprice'], $title);
				$title = str_replace('[副标题]', $goods['subtitle'], $title);
			}

			if ($goods['share_desc']) {
				$desc = $goods['share_desc'];

				if (empty($desc)) {
					$config = Setting::agentsetting_read('groupon');
					$sysname = $_W['wlsetting']['base']['name'];
					$desc = $config['share_desc'];
					$desc = str_replace('[系统名称]', $sysname, $desc);
				}

				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[商品名称]', $goods['name'], $desc);
				$desc = str_replace('[商户名称]', $merchant['storename'], $desc);
				$desc = str_replace('[团购价]', $goods['price'], $desc);
				$desc = str_replace('[特权类型]', $vipstatus, $desc);
				$desc = str_replace('[会员价]', $goods['vipprice'], $desc);
				$desc = str_replace('[原价]', $goods['oldprice'], $desc);
				$desc = str_replace('[副标题]', $goods['subtitle'], $desc);
			}
		}

		if (empty($desc)) {
			$desc = $set['share_desc'];
			$desc = str_replace('[昵称]', $nickname, $desc);
			$desc = str_replace('[时间]', $time, $desc);
		}

		if (empty($desc)) {
			$desc = $goods['subtitle'];
		}

		$_W['wlsetting']['share']['share_title'] = !empty($title) ? $title : $goods['name'];
		$_W['wlsetting']['share']['share_desc'] = $desc;
		$_W['wlsetting']['share']['share_image'] = !empty($goods['share_image']) ? $goods['share_image'] : $goods['thumb'];
		include wl_template('grouponhtml/groupondetail');
	}

	public function grouponInitApi()
	{
		global $_W;
		global $_GPC;
		$base = Setting::agentsetting_read('groupon');
		$advs = pdo_getall('wlmerchant_adv', array('type' => 7, 'enabled' => 1, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'cateid' => 0), array('thumb', 'link'));

		if ($advs) {
			foreach ($advs as $key => &$adv) {
				$adv['thumb'] = tomedia($adv['thumb']);
			}
		}

		if ($base['communqrcode']) {
			$sys = Setting::agentsetting_read('community');
			$community['qrcode'] = tomedia($base['communqrcode']);
			$community['name'] = $base['communname'] ? $base['communname'] : $sys['communname'];
			$community['desc'] = $base['commundesc'] ? $base['commundesc'] : $sys['commundesc'];
			$community['img'] = $base['communimg'] ? $base['communimg'] : $sys['communimg'];
			$community['img'] = tomedia($community['img']);
		}
		else {
			$community = '';
		}

		$display = Setting::agentsetting_read('groupon');
		$display = $display['display'];
		$cateWhere = ' uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND parentid = 0';

		if ($display == 1) {
			$cateWhere .= ' AND thumb != \'\' ';
		}

		$cates = pdo_fetchall('SELECT id,thumb,`name` FROM ' . tablename(PDO_NAME . 'groupon_category') . (' WHERE ' . $cateWhere . ' ORDER BY sort DESC '));

		foreach ($cates as $key => &$cat) {
			$cat['thumb'] = tomedia($cat['thumb']);
		}

		if ($base['rushids']) {
			$rushgoods = array();
			$base['rushids'] = unserialize($base['rushids']);

			foreach ($base['rushids'] as $key => $rushid) {
				$rushgood = pdo_get('wlmerchant_rush_activity', array('id' => $rushid, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id', 'name', 'thumb', 'price', 'oldprice', 'vipstatus', 'vipprice', 'num', 'endtime', 'allsalenum'));

				if (!empty($rushgood)) {
					$rushgoods[] = $rushgood;
				}
			}

			if ($rushgoods) {
				if (1 < count($rushgoods)) {
					foreach ($rushgoods as $key => &$rush) {
						$rush['thumb'] = tomedia($rush['thumb']);
						$sytimes[] = $rush['endtime'] - time();
					}

					$sytime = min($sytimes);
				}
				else {
					$rushgoods[0]['thumb'] = tomedia($rushgoods[0]['thumb']);
					$alreadyBuyNum = pdo_fetch('SELECT SUM(num) FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status IN (0,1,2,3,6,9) AND fkid = ' . $rushgoods[0]['id'] . ' '));
					$alreadyBuyNum = array_values($alreadyBuyNum);
					$rushgoods[0]['buynum'] = $alreadyBuyNum[0];
					$rushgoods[0]['buynum'] = $rushgoods[0]['buynum'] + $rushgoods[0]['allsalenum'];
					$rushgoods[0]['num'] = $rushgoods[0]['num'] + $rushgoods[0]['allsalenum'];
					$sytime = $rushgoods[0]['endtime'] - time();
				}
			}
		}
		else {
			$rushgoods = '';
		}

		$cubes = Dashboard::readSetting('grouponcube');

		foreach ($cubes as $key => &$cub) {
			if ($cub['on']) {
				$cub['thumb'] = tomedia($cub['thumb']);
				$cubess[] = $cub;
			}
		}

		$data = array('adv' => $advs, 'cate' => $cates, 'catenum' => $_W['agentset']['meroof']['navnum'], 'rushgood' => $rushgoods, 'sytime' => $sytime ? $sytime : '', 'cube' => $cubess, 'rushtitle' => $base['rushtitle'] ? $base['rushtitle'] : '超值抢购', 'titleimg' => $base['titleimg'] ? tomedia($base['titleimg']) : URL_MODULE . 'plugin/groupon/app/resource/image/recommend.png', 'titlename' => $base['titlename'] ? $base['titlename'] : '为您定制', 'titledis' => $base['titledis'] ? $base['titledis'] : 'Hi，辛苦一天，好好犒劳一下自己', 'catelevel' => intval($base['catelevel']), 'community' => $community);

		if ($data) {
			wl_json(0, '获取成功', $data);
		}
		else {
			wl_json(1, '获取失败', $data);
		}
	}

	public function grouponCateApi()
	{
		global $_W;
		global $_GPC;
		$base = Setting::agentsetting_read('groupon');
		$parentid = $_GPC['cid'];
		$data = pdo_getall('wlmerchant_groupon_category', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'parentid' => 0), array('id', 'name'), '', 'sort DESC');

		if ($base['catelevel'] == 1) {
			$data2 = pdo_getall('wlmerchant_groupon_category', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'parentid' => $parentid), array('id', 'name', 'thumb'), '', 'sort DESC');

			if ($data2) {
				foreach ($data2 as $key => &$cat2) {
					$cat2['thumb'] = tomedia($cat2['thumb']);
				}
			}
		}

		if ($data) {
			foreach ($data as $key => &$cat) {
				$cat['children'] = pdo_getall('wlmerchant_groupon_category', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'parentid' => $cat['id']), array('id', 'name'), '', 'sort DESC');
			}
		}

		$advs = pdo_getall('wlmerchant_adv', array('type' => 7, 'enabled' => 1, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'cateid' => $parentid), array('thumb', 'link'));

		if ($advs) {
			foreach ($advs as $key => &$adv) {
				$adv['thumb'] = tomedia($adv['thumb']);
			}
		}

		$areas = pdo_getall(PDO_NAME . 'area', array('pid' => $_W['areaid']), array('id', 'name'));
		wl_json(0, '获取成功', array('cate' => $data, 'children' => $data2, 'adv' => $advs, 'areas' => $areas));
	}

	public function grouponListApi()
	{
		global $_W;
		global $_GPC;
		$pindex = $_GPC['pindex'] ? $_GPC['pindex'] : 1;
		$cateid = $_GPC['cateid'];
		$area = $_GPC['area'];
		$distance = $_GPC['distance'];
		$sort = $_GPC['sort'];
		$newstore = $_GPC['newstore'];
		$pricearea = $_GPC['pricearea'];
		$appointment = $_GPC['appointment'];
		$memberlng = $_GPC['lng'];
		$memberlat = $_GPC['lat'];
		$set = Setting::agentsetting_read('groupon');
		$psize = 20;
		$where = ' uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 2 ';

		if ($_GPC['cate']) {
			$where .= ' AND recommend = 1';
		}

		if ($cateid == 'false') {
			$cateid = 0;
		}

		if ($cateid) {
			$isparent = pdo_getcolumn('wlmerchant_groupon_category', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'id' => $cateid), 'parentid');
			if (empty($isparent) && $set['catelevel']) {
				$allids = pdo_getall('wlmerchant_groupon_category', array('parentid' => $cateid), array('id'), '', 'sort DESC');

				if (1 < count($allids)) {
					$childs = '(' . $cateid . ',';
					$childs .= implode(',', array_column($allids, 'id'));
					$childs .= ')';
					$where .= ' AND cateid IN ' . $childs;
				}
				else {
					$where .= ' AND cateid = ' . $cateid;
				}
			}
			else {
				$where .= ' AND cateid = ' . $cateid;
			}
		}

		if (!empty($area) || !empty($newstore)) {
			$storewhere = array('uniacid' => $_W['uniacid']);

			if (!empty($area)) {
				$storewhere['distid'] = $area;
			}

			if (!empty($newstore)) {
				$storewhere['createtime>'] = time() - 24 * 86400;
			}

			$sids = pdo_getall('wlmerchant_merchantdata', $storewhere, array('id'));

			if ($sids) {
				foreach ($sids as $key => $v) {
					$merchantids[] = $v['id'];
				}

				$where .= ' AND sid = ' . $merchantids;
			}
			else {
				$where .= ' AND sid = 0';
			}
		}

		if (!empty($noappointment)) {
			$where .= ' AND appointment = 0';
		}

		if (!empty($pricearea)) {
			if ($pricearea == 1) {
				$where .= ' AND price < 50.01';
			}
			else if ($pricearea == 2) {
				$where .= ' AND price > 50 AND price < 100.01';
			}
			else if ($pricearea == 3) {
				$where .= ' AND price > 100 AND price < 300.01';
			}
			else {
				if ($pricearea == 4) {
					$where .= ' AND price > 300';
				}
			}
		}

		$activity = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_groupon_activity') . ('WHERE ' . $where . ' ORDER BY sort DESC,createtime DESC  LIMIT ') . ($pindex - 1) * $psize . ',' . $psize);

		foreach ($activity as $key => &$act) {
			$act['createtime'] = date('Y-m-d', $act['createtime']);
			$act['thumb'] = tomedia($act['thumb']);
			$act['cateid'] = pdo_getcolumn(PDO_NAME . 'groupon_category', array('id' => $act['cateid']), 'name');
			$act['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $act['sid']), 'storename');
			$act['salenum'] = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE fkid = ' . $act['id'] . ' and plugin = \'groupon\' and status in (0,1,2,3,4,6,7,8,9)'));
			$act['salenum'] = $act['salenum'] ? $act['salenum'] : 0;
			$act['salenum'] = $act['salenum'] + $act['falsesalenum'];
			$act['listtag'] = unserialize($act['listtag']);
			$act['score'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $act['sid']), 'score');

			if ($set['discountstatus']) {
				$act['discountstatus'] = 1;
			}
			else {
				$act['discountstatus'] = 0;
			}

			if ($distance || $sort == 1) {
				$location = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $act['sid']), 'location');
				$location = unserialize($location);
				$act['distance'] = Store::getdistance($location['lng'], $location['lat'], $memberlng, $memberlat);
			}

			if ($distance == 1) {
				if (1000 < $act['distance']) {
					unset($activity[$key]);
				}
			}
			else if ($distance == 2) {
				if (3000 < $act['distance']) {
					unset($activity[$key]);
				}
			}
			else if ($distance == 3) {
				if (5000 < $act['distance']) {
					unset($activity[$key]);
				}
			}
			else {
				if ($distance == 4) {
					if (10000 < $act['distance']) {
						unset($activity[$key]);
					}
				}
			}
		}

		if ($sort == 1) {
			$activity = Store::wl_sort($activity, 'distance', SORT_DESC);
		}
		else if ($sort == 2) {
			$activity = Store::wl_sort($activity, 'score', SORT_ASC);
		}
		else {
			if ($sort == 3) {
				$activity = Store::wl_sort($activity, 'pv', SORT_ASC);
			}
		}

		$data = array('activity' => $activity);

		if ($data) {
			wl_json(0, '获取成功', $data);
		}
		else {
			wl_json(1, '获取失败');
		}
	}

	public function grouponDetailApi()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['grouponId'];

		if (empty($id)) {
			wl_json(1, '无活动ID');
		}

		$base = Setting::agentsetting_read('groupon');
		$orderset = Setting::wlsetting_read('orderset');
		pdo_fetch('update' . tablename('wlmerchant_groupon_activity') . ('SET pv=pv+1 WHERE id = ' . $id));
		$activity = pdo_get('wlmerchant_groupon_activity', array('id' => $id));
		$activity['starttime'] = date('Y-m-d', $activity['starttime']);
		$activity['endtime'] = date('Y-m-d', $activity['endtime']);
		$activity['thumbs'] = unserialize($activity['thumbs']);
		$activity['describe'] = nl2br($activity['describe']);
		$activity['detail'] = nl2br($activity['detail']);

		if ($activity['thumbs']) {
			foreach ($activity['thumbs'] as $key => &$va) {
				$va = tomedia($va);
			}
		}

		$activity['thumb'] = tomedia($activity['thumbs'][0]);
		$activity['bgmusic'] = tomedia($activity['bgmusic']);
		$activity['catename'] = pdo_getcolumn(PDO_NAME . 'groupon_category', array('id' => $activity['cateid']), 'name');

		if (empty($activity['unit'])) {
			$activity['unit'] = '份';
		}

		$activity['merchant'] = pdo_get('wlmerchant_merchantdata', array('id' => $activity['sid']), array('logo', 'location', 'tag', 'score', 'mobile', 'store_quhao', 'storename', 'storehours', 'address'));
		$activity['merchant']['storehours'] = unserialize($activity['merchant']['storehours']);
		$activity['merchant']['location'] = unserialize($activity['merchant']['location']);
		$activity['merchant']['logo'] = tomedia($activity['merchant']['logo']);
		$activity['merchant']['tag'] = unserialize($activity['merchant']['tag']);

		if ($activity['merchant']['tag']) {
			foreach ($activity['merchant']['tag'] as $key => &$t) {
				$t = pdo_getcolumn(PDO_NAME . 'tags', array('id' => $t), 'title');
			}
		}

		if ($activity['merchant']['store_quhao']) {
			$activity['merchant']['mobile'] = $activity['merchant']['store_quhao'] . $activity['merchant']['mobile'];
		}

		if (0 < $activity['appointment']) {
			$activity['tags'][] = '提前' . $activity['appointment'] . '小时预约';
		}

		if (0 < $activity['retainage']) {
			$activity['tags'][] = '到店付尾款';
		}

		$tag = unserialize($activity['tag']);

		if ($tag) {
			foreach ($tag as $key => $ta) {
				$activity['tags'][] = pdo_getcolumn(PDO_NAME . 'tags', array('id' => $ta), 'title');
			}
		}

		$orders = pdo_fetchall('SELECT id,mid FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND plugin = \'groupon\' AND status = 3 AND fkid = ' . $id . ' ORDER BY id DESC limit 10'));
		$ordersIdStr = '';

		foreach ($orders as $key => &$order) {
			$ordersIdStr .= $order['id'] . ',';
		}

		$activity['salenum'] = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE fkid = ' . $activity['id'] . ' and plugin = \'groupon\' and status in (0,1,2,3,6,7,9,4,8)'));
		$recommend = pdo_fetchall('SELECT id,name,price,falsesalenum,thumb,oldprice,sid,thumbs FROM ' . tablename('wlmerchant_groupon_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 2 AND id != ' . $id . ' ORDER BY RAND() LIMIT 2'));

		foreach ($recommend as $key => &$re) {
			$re['thumbs'] = unserialize($re['thumbs']);
			$re['thumb'] = tomedia($re['thumbs'][0]);
			$re['salenum'] = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE fkid = ' . $re['id'] . ' and plugin = \'groupon\' and status in (0,1,2,3,6,7,9,4,8)'));
			$re['salenum'] = $re['salenum'] ? $re['salenum'] : 0;
			$re['salenum'] = $re['salenum'] + $re['falsesalenum'];
			$re['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $re['sid']), 'storename');

			if ($base['discountstatus']) {
				$re['discountstatus'] = 1;
			}
			else {
				$re['discountstatus'] = 0;
			}
		}

		$activity['recommend'] = $recommend;
		$ordersIdStr = trim($ordersIdStr, ',');
		$comments = pdo_fetchall('SELECT pic,mid,text,createtime,headimg,nickname,star,replytextone FROM ' . tablename('wlmerchant_comment') . ('WHERE sid = ' . $activity['sid'] . ' AND checkone = 2 AND idoforder IN (' . $ordersIdStr . ') ORDER BY id DESC LIMIT 10'));

		foreach ($comments as $key => &$comment) {
			$comment['headimg'] = tomedia($comment['headimg']);
			$comment['pic'] = unserialize($comment['pic']);
			$comment['createtime'] = date('Y-m-d H:i:s', $comment['createtime']);

			if ($comment['pic']) {
				foreach ($comment['pic'] as $key => &$pic) {
					$pic = tomedia($pic);
					$frist = Tools::createImage($pic);
					$comment['pics'][$key]['src'] = $pic;
					$comment['pics'][$key]['msrc'] = $pic;
					$comment['pics'][$key]['w'] = imagesx($frist);
					$comment['pics'][$key]['h'] = imagesy($frist);
				}
			}
		}

		$activity['comments'] = $comments;
		$activity['vipflag'] = Member::checkhalfmember();
		$activity['salenum'] = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE fkid = ' . $id . ' and plugin = \'groupon\' and status in (0,1,2,3,6,7,9)'));
		$activity['salenum'] = $activity['salenum'] ? $activity['salenum'] : 0;
		$activity['salenum'] = $activity['salenum'] + $activity['falsesalenum'];

		if ($activity['optionstatus']) {
			$specs = pdo_getall('wlmerchant_goods_spec', array('goodsid' => $id, 'type' => 3), array('title', 'content'), '', 'displayorder ASC');

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

			$options = pdo_getall('wlmerchant_goods_option', array('goodsid' => $id, 'type' => 3), array('id', 'specs', 'title', 'price', 'stock', 'vipprice'));
			$activity['levelnum'] = 0;

			foreach ($options as $key => &$op) {
				if (0 < $op['stock']) {
					$activity['levelnum'] += $op['stock'];
				}
				else {
					$op['stock'] = 0;
				}
			}
		}

		$activity['specs'] = $specs;
		$activity['options'] = $options;

		if (empty($base['showrelevant'])) {
			if (empty($base['relevantnum'])) {
				$relevantnum = 10;
			}
			else {
				$relevantnum = $base['relevantnum'];
			}

			$storegoods = pdo_fetchall('SELECT name,id,subtitle FROM ' . tablename('wlmerchant_groupon_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND sid = ' . $activity['sid'] . ' AND status IN (1,2) ORDER BY sort DESC LIMIT ' . $relevantnum));

			if ($storegoods) {
				foreach ($storegoods as $key => &$acti) {
					$acti['name'] = $acti['subtitle'] ? $acti['subtitle'] : $acti['name'];
				}
			}
			else {
				$storegoods = '';
			}

			$activity['storegood'] = $storegoods;
		}
		else {
			$activity['storegood'] = '';
		}

		$activity['orderinfo'] = unserialize($activity['orderinfo']);
		$address = pdo_get('wlmerchant_address', array('mid' => $_W['mid'], 'status' => 1), array('name', 'tel', 'province', 'city', 'county', 'detailed_address'));

		if (empty($address['name'])) {
			$address['name'] = $_W['wlmember']['realname'];
		}

		if (empty($address['tel'])) {
			$address['tel'] = $_W['wlmember']['mobile'];
		}

		if (empty($activity['usestatus']) && empty($activity['fastpay'])) {
			$activity['thname'] = $_W['wlmember']['nickname'];
			$activity['thmobile'] = $_W['wlmember']['mobile'];
			if (!empty($activity['thname']) && ($orderset['thmobile'] || !empty($activity['thmobile']))) {
				$activity['direct'] = 1;
			}
		}

		if (in_array('groupon', $_W['wlsetting']['share']['forcefollow']) && $_W['fans']['follow'] != 1 && is_weixin()) {
			$activity['forcefollow'] = 1;
		}
		else {
			$activity['forcefollow'] = 0;
		}

		if ($activity['levelnum'] < 0) {
			$activity['levelnum'] = 0;
		}

		if ($activity['sharestatus'] == 1) {
			$alshare = pdo_getcolumn(PDO_NAME . 'sharegift_record', array('uniacid' => $_W['uniacid'], 'plugin' => 2, 'mid' => $_W['mid'], 'goodsid' => $id, 'status' => 0, 'type' => 1), 'id');
			$activity['shareflag'] = 1;
		}

		if ($base['communqrcode']) {
			$sys = Setting::agentsetting_read('community');
			$activity['community']['qrcode'] = tomedia($base['communqrcode']);
			$activity['community']['name'] = $base['communname'] ? $base['communname'] : $sys['communname'];
			$activity['community']['desc'] = $base['commundesc'] ? $base['commundesc'] : $sys['commundesc'];
			$activity['community']['img'] = $base['communimg'] ? $base['communimg'] : $sys['communimg'];
			$activity['community']['img'] = tomedia($activity['community']['img']);
		}
		else {
			$activity['community'] = '';
		}

		if ($activity) {
			wl_json(0, '获取成功', $activity);
		}
		else {
			wl_json(1, '获取失败');
		}
	}

	public function orderdetail()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '订单详情 - ' . $_W['wlsetting']['base']['name'] : '订单详情';
		$id = $_GPC['orderid'];

		if (empty($id)) {
			wl_message('缺少重要参数');
		}

		$order_out = pdo_get(PDO_NAME . 'order', array('id' => $id));
		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $order_out['sid']));
		$goods = pdo_get('wlmerchant_groupon_activity', array('id' => $order_out['fkid']));
		$record = pdo_get('wlmerchant_groupon_userecord', array('id' => $order_out['recordid']));
		$order_out['levelnum'] = $order_out['num'] - $record['usetimes'];
		$order_out['usetimes'] = $record['usetimes'];
		$order_out['checkcode'] = $record['qrcode'];
		$order_out['optionid'] = $order_out['specid'];
		$hxurl = app_url('groupon/grouponapp/hexiaokey', array('sid' => $merchant['id'], 'orderid' => $id));
		$url = app_url('groupon/grouponapp/hexiao', array('id' => $id));

		if ($order_out['specid']) {
			$option = pdo_get('wlmerchant_goods_option', array('id' => $order_out['specid']), array('price', 'vipprice', 'title'));
			$goods['price'] = $option['price'];
			$goods['vipprice'] = $option['vipprice'];
		}

		if ($order_out['vipbuyflag']) {
			$goods['price'] = $goods['vipprice'];
		}

		include wl_template('order/orderdetail');
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
			$res = Groupon::hexiaoorder($orderid, 0, $num, 4);

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
		$goods = pdo_get('wlmerchant_groupon_activity', array('id' => $order_out['fkid']));
		$type = 'groupon';

		if ($order_out['neworderflag']) {
			$order_out['levelnum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'' . $type . '\' AND  orderid = ' . $id . ' AND status != 1'));
			$order_out['usetimes'] = $order_out['num'] - $order_out['levelnum'];
		}
		else {
			$record = pdo_get('wlmerchant_groupon_userecord', array('id' => $order_out['recordid']));
			$order_out['levelnum'] = $order_out['num'] - $record['usetimes'];
			$order_out['usetimes'] = $record['usetimes'];
		}

		$order_out['optionid'] = $order_out['specid'];

		if ($order_out['specid']) {
			$option = pdo_get('wlmerchant_goods_option', array('id' => $order_out['specid']), array('price', 'vipprice', 'title'));
			$goods['price'] = $option['price'];
			$goods['vipprice'] = $option['vipprice'];
		}

		if ($order_out['vipbuyflag']) {
			$goods['price'] = $goods['vipprice'];
		}

		include wl_template('order/hexiaoorder');
	}

	public function xiaofei()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$num = $_GPC['num'];
		$res = Groupon::hexiaoorder($id, $_W['mid'], $num, 2);

		if ($res) {
			exit(json_encode(array('errno' => 0, 'message' => '核销成功')));
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '核销失败')));
		}
	}

	public function topaygroupon()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '团购订单 - ' . $_W['wlsetting']['base']['name'] : '团购订单';
		$id = $_GPC['id'];
		$num = $_GPC['num'];
		$invitid = $_GPC['invitid'];
		$usestatus = $_GPC['usestatus'];
		$cardId = $_GPC['cardId'];
		$activity = Groupon::getSingleActive($id, '*');
		Member::checklogin(app_url('rush/home/detail', array('id' => $id)));

		if (empty($_W['mid'])) {
			wl_message(array('errno' => 2, 'message' => '未登录!'));
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
			else if ($activity['status'] == 7) {
				wl_message(array('errno' => 1, 'message' => '商品已抢完'));
			}
			else {
				wl_message(array('errno' => 1, 'message' => '商品已停售'));
			}
		}

		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
		if (empty($_W['wlmember']['mobile']) && in_array('groupon', $mastmobile)) {
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
			if ($halfcardflag || $cardId) {
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
			$alreadyBuyNum = pdo_fetch('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $_W['mid'] . ' AND plugin = \'groupon\' AND status IN (0,1,4,8,2,3,6,9) AND fkid = ' . $id . ' '));
			$alreadyBuyNum = array_values($alreadyBuyNum);
			$alreadyBuyNum = $alreadyBuyNum[0];

			if (empty($alreadyBuyNum)) {
				$alreadyBuyNum = 0;
			}

			$levelnum = $activity['op_one_limit'] - $alreadyBuyNum;

			if (!$levelnum) {
				wl_message(array('errno' => 1, 'message' => '限购商品!您已全部购买'));
			}
			else {
				if ($levelnum < $num) {
					if ($levelnum < 0) {
						$levelnum = 0;
					}

					wl_message(array('errno' => 1, 'message' => '限购商品!您还能购买' . $levelnum . $activity['unit']));
				}
			}
		}

		if ($activity['levelnum'] < $num) {
			wl_message(array('errno' => 1, 'message' => '库存不足'));
		}

		if ($activity['sharestatus'] == 1) {
			$alshare = pdo_getcolumn(PDO_NAME . 'sharegift_record', array('uniacid' => $_W['uniacid'], 'plugin' => 2, 'mid' => $_W['mid'], 'goodsid' => $id, 'status' => 0, 'type' => 1), 'id');

			if ($alshare) {
				$sharemoney = sprintf('%.2f', $activity['sharemoney'] * $num);
			}
		}
		else {
			if ($activity['sharestatus'] == 2) {
				$alshare = pdo_getcolumn(PDO_NAME . 'sharegift_record', array('uniacid' => $_W['uniacid'], 'plugin' => 2, 'buymid' => $_W['mid'], 'mid' => $invitid, 'goodsid' => $id, 'status' => 0, 'type' => 1), 'id');
			}
		}

		if (empty($sharemoney)) {
			$sharemoney = 0;
		}

		$settlementmoney = Store::getsettlementmoney(3, $id, $num, $activity['sid'], $vipbuyflag, $optionid);

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
			$username = trim($_GPC['thname']);
			$mobile = trim($_GPC['thmobile']);
			$expressprice = 0;
			$neworderflag = 1;
		}

		$prices = sprintf('%.2f', $price * $num - $sharemoney + $expressprice);
		$goodsprice = sprintf('%.2f', $price * $num);

		if ($num < 0) {
			wl_message(array('errno' => 1, 'message' => '数量错误，请刷新重试'));
		}

		$data = array('uniacid' => $activity['uniacid'], 'mid' => $_W['mid'], 'sid' => $activity['sid'], 'aid' => $activity['aid'], 'fkid' => $activity['id'], 'plugin' => 'groupon', 'payfor' => 'grouponOrder', 'orderno' => createUniontid(), 'status' => 0, 'createtime' => TIMESTAMP, 'price' => $prices, 'num' => $num, 'vipbuyflag' => $vipbuyflag, 'specid' => $optionid, 'name' => $username, 'mobile' => $mobile, 'address' => $addre, 'shareid' => $alshare, 'goodsprice' => $goodsprice, 'expressid' => $expressid, 'remark' => trim($_GPC['remark']), 'settlementmoney' => $settlementmoney, 'vip_card_id' => $cardId, 'neworderflag' => $neworderflag);
		pdo_insert(PDO_NAME . 'order', $data);
		$orderid = pdo_insertid();

		if ($optionid) {
			$nowstock = $option['stock'] - $num;
			pdo_update('wlmerchant_goods_option', array('stock' => $nowstock), array('id' => $optionid));
		}
		else {
			$data2['levelnum'] = $activity['levelnum'] - $num;
			Groupon::updateActive($data2, array('id' => $data['fkid']));
		}

		if ($alshare) {
			pdo_update('wlmerchant_sharegift_record', array('orderid' => $orderid, 'buymid' => $_W['mid']), array('id' => $alshare));
		}

		if (0 < $data['price']) {
			wl_json(0, '下单成功', $orderid);
		}
		else if (empty($cardId)) {
			if (0 < abs($data['price'])) {
				wl_message(array('errno' => 1, 'message' => '金额异常，请刷新重试'));
			}

			$newdata = array('status' => 1, 'paytime' => time());
			pdo_update(PDO_NAME . 'order', $newdata, array('orderno' => $data['orderno']));
			$url = app_url('order/userorder/orderlist', array('status' => 'all'));
			Message::paySuccess($orderid, 'groupon');
			wl_message(array('errno' => 4, 'message' => '购买成功!'));
		}
		else {
			wl_json(0, '下单成功', $orderid);
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

		$data = pdo_get(PDO_NAME . 'order', array('uniacid' => $_W['uniacid'], 'id' => $orderno), array('orderno', 'price', 'sid', 'fkid', 'vip_card_id'));
		$activity = Groupon::getSingleActive($data['fkid'], 'name');
		$bankrid = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $data['sid']), 'bankrid');
		$data = PayBuild::isOpenCard($data);
		$params = array('tid' => $data['orderno'], 'ordersn' => $data['orderno'], 'title' => mb_substr($activity['name'], 0, 25, 'utf-8'), 'fee' => $data['price'], 'module' => 'weliam_merchant');
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => 'Groupon', 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Groupon', 'payfor' => 'GrouponOrder', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}
}

?>
