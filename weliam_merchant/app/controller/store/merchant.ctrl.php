<?php
//dezend by http://www.sucaihuo.com/
class Merchant_WeliamController
{
	public function newindex()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商家 - ' . $_W['wlsetting']['base']['name'] : '商家';
		$meroof = Setting::agentsetting_read('meroof');
		$cates = Util::getNumData('id,name,thumb,abroad', PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'parentid' => 0, 'enabled' => 1), 'displayorder DESC');
		$navs = $cates[0];

		foreach ($navs as $key => &$nav) {
			if (empty($nav['abroad'])) {
				$nav['link'] = app_url('store/merchant/index', array('cid' => $nav['id']));
			}
			else {
				$nav['link'] = $nav['abroad'];
			}
		}

		$advs = pdo_getall('wlmerchant_adv', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'enabled' => 1, 'type' => 1));
		$top = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE enabled = 1 and uniacid = \'' . $_W['uniacid'] . '\' and aid = ' . $_W['aid'] . ' AND ( listshow = 0 OR listshow IS NULL ) ORDER BY id DESC LIMIT 10'));

		if ($top) {
			foreach ($top as $key => &$v) {
				$v['a'] = app_url('store/merchant/detail', array('id' => $v['id']));
				$v['time'] = date('m-d', $v['createtime']);
			}
		}

		$tablink = Setting::agentsetting_read('meroof');
		$tablink = $tablink['tablink'];

		if (empty($tablink)) {
			$tablink = array(
				array('name' => '附近', 'near' => 2),
				array('name' => '推荐', 'near' => 3),
				array('name' => '人气', 'near' => 4),
				array('name' => '最新', 'near' => 1)
			);
		}

		$share = Setting::agentsetting_read('shareset');

		if ($share['merlist_title']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];
			$title = $share['merlist_title'];
			$title = str_replace('[昵称]', $nickname, $title);
			$title = str_replace('[时间]', $time, $title);
			$title = str_replace('[系统名称]', $sysname, $title);
			$desc = $share['merlist_desc'];
			$desc = str_replace('[昵称]', $nickname, $desc);
			$desc = str_replace('[时间]', $time, $desc);
			$desc = str_replace('[系统名称]', $sysname, $desc);
			$_W['wlsetting']['share']['share_title'] = $title;
			$_W['wlsetting']['share']['share_desc'] = $desc;
			$_W['wlsetting']['share']['share_image'] = $share['merlist_image'];
		}

		$storeSet = Setting::agentsetting_read('agentsStoreSet');
		$searchSet = array('search_float' => $storeSet['search_float'], 'search_bgColor' => $storeSet['search_bgColor']);

		if (p('diypage')) {
			if (!empty($_W['agentset']['diypageset']['menu_index'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_index']);
			}
		}

		include wl_template('store/newindex');
	}

	public function index()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商户列表 - ' . $_W['wlsetting']['base']['name'] : '商户列表';
		$storeSet = Setting::agentsetting_read('agentsStoreSet');

		if ($storeSet['sortdefault'] == 1) {
			$sname = '入驻时间';
		}
		else if ($storeSet['sortdefault'] == 2) {
			$sname = '附近优先';
		}
		else {
			$sname = '默认排序';
		}

		$fujin = $_GPC['fujin'];

		if ($fujin) {
			$status = $_GPC['status'];
			$cate = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_category_store') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND parentid = 0 ORDER BY displayorder DESC'));
			$flag = $_GPC['parentid'];

			if ($flag) {
				$parentname = pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $flag), 'name');
				$seccate = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_category_store') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND parentid = ' . $flag . ' ORDER BY displayorder DESC LIMIT 7'));
			}
			else {
				$parentname = '热门推荐';
				$flag = 0;
			}
		}

		if (!empty($_GPC['cid'])) {
			$cid = intval($_GPC['cid']);
			$cname = pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $cid), 'name');
		}

		if (!empty($_GPC['pid'])) {
			$pid = intval($_GPC['pid']);
			$cname = pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $pid), 'name');
			$cid = $pid;
		}

		if (!empty($_GPC['groupid'])) {
			$groupid = intval($_GPC['groupid']);
		}
		else {
			$groupid = 0;
		}

		$keyword = $_GPC['keyword'];

		if (p('diypage')) {
			if (!empty($_W['agentset']['diypageset']['menu_index'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_index']);
			}
		}

		$share = Setting::agentsetting_read('shareset');

		if ($share['merlist_title']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];
			$title = $share['merlist_title'];
			$title = str_replace('[昵称]', $nickname, $title);
			$title = str_replace('[时间]', $time, $title);
			$title = str_replace('[系统名称]', $sysname, $title);
			$desc = $share['merlist_desc'];
			$desc = str_replace('[昵称]', $nickname, $desc);
			$desc = str_replace('[时间]', $time, $desc);
			$desc = str_replace('[系统名称]', $sysname, $desc);
			$_W['wlsetting']['share']['share_title'] = $title;
			$_W['wlsetting']['share']['share_desc'] = $desc;
			$_W['wlsetting']['share']['share_image'] = $share['merlist_image'];
		}

		include wl_template('store/index');
	}

	public function getwxappqr()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$src = Wxapp::get_store_qrcode($id);

		if (is_error($src)) {
			exit(json_encode(array('errno' => 1, 'msg' => $src['message'])));
		}
		else {
			exit(json_encode(array('errno' => 0, 'msg' => $src)));
		}
	}

	public function detail()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商户详情 - ' . $_W['wlsetting']['base']['name'] : '商户详情';
		$id = intval($_GPC['id']);
		$commentset = Setting::agentsetting_read('comment');
		$store = Store::getSingleStore($id);

		if ($store['enabled'] != 1) {
			wl_message('商户暂停中或已到期，暂无法访问', referer(), 'error');
		}

		$store['pv'] = $store['pv'] + 1;
		$res = pdo_update('wlmerchant_merchantdata', array('pv' => $store['pv']), array('id' => $id));

		if (!empty($store['externallink'])) {
			header('location:' . $store['externallink']);
		}

		if ($_GPC['fansflag']) {
			Store::addFans($id, $_W['mid']);
		}

		$storehours = unserialize($store['storehours']);
		$store['storehours'] = $storehours['startTime'] . '—' . $storehours['endTime'];
		$location = unserialize($store['location']);
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

		$advs = unserialize($store['adv']);
		$advnum = count($advs);

		foreach ($advs as $key => &$val) {
			$val = tomedia($val);
		}

		$album = unserialize($store['album']);

		foreach ($album as $key => &$albu) {
			$albu = tomedia($albu);
		}

		$albumnum = count($album);
		$collects = pdo_get(PDO_NAME . 'collect', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'storeid' => $id), 'id');
		$halfcardflag = Member::checkhalfmember();
		$tags = unserialize($store['tag']);

		foreach ($tags as &$tag) {
			$tag = pdo_getcolumn('wlmerchant_tags', array('id' => $tag), 'title');
		}

		$active = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_rush_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND sid= ' . $id . ' AND status IN (1,2) ORDER BY sort DESC'));

		foreach ($active as $key => &$v) {
			$v['a'] = app_url('rush/home/detail', array('id' => $v['id']));
			$v['thumb'] = tomedia($v['thumb']);
		}

		if (p('groupon')) {
			$groupon = pdo_fetchall('SELECT id,name,thumb,price,oldprice FROM ' . tablename('wlmerchant_groupon_activity') . ('WHERE sid = ' . $id . ' AND status IN (1,2) ORDER BY sort DESC'));

			foreach ($groupon as $key => &$gr) {
				$gr['a'] = app_url('groupon/grouponapp/groupondetail', array('cid' => $gr['id']));
				$gr['thumb'] = tomedia($gr['thumb']);
			}
		}

		if ($store['payonline']) {
			$payurl = app_url('order/paybill/paycheck', array('id' => $id));
		}

		if ($_W['wlsetting']['halfcard']['status']) {
			$halfcard = pdo_get(PDO_NAME . 'halfcardlist', array('uniacid' => $_W['uniacid'], 'merchantid' => $id, 'status' => 1), array('limit', 'id', 'title', 'discount', 'activediscount', 'datestatus'));

			if (!empty($halfcard)) {
				$halfcard['url'] = app_url('halfcard/halfcard_app/userhalfcard', array('actid' => $halfcard['id'], 'type' => 1, 'useflag' => 1));
				$halfcard['detailurl'] = app_url('halfcard/halfcard_app/halfcarddetail', array('id' => $halfcard['id']));
				$halfcard['num'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfcard['id'] . ' AND type = 1'));
			}

			$packages = pdo_getall(PDO_NAME . 'package', array('uniacid' => $_W['uniacid'], 'merchantid' => $id, 'status' => 1), array('limit', 'id', 'title'));

			if (!empty($packages)) {
				foreach ($packages as $key => &$pack) {
					$pack['url'] = app_url('halfcard/halfcard_app/userhalfcard', array('actid' => $pack['id'], 'type' => 2, 'useflag' => 1));
					$pack['detailurl'] = app_url('halfcard/halfcard_app/packagedetail', array('id' => $pack['id']));
				}
			}
		}

		$scoupon = pdo_getall(PDO_NAME . 'couponlist', array('merchantid' => $id, 'uniacid' => $_W['uniacid'], 'status' => 1, 'nostoreshow' => 0), '', '', 'indexorder DESC');

		if ($scoupon) {
			foreach ($scoupon as $sk => &$sval) {
				if ($sval['is_charge'] == 1) {
					if ($sval['vipstatus'] == 2 && $halfcardflag) {
						$sval['charge'] = $sval['vipprice'];
						$sval['oldcharge'] = $sval['price'];
					}
					else {
						$sval['charge'] = $sval['price'];
					}
				}
				else {
					$sval['charge'] = '免费';
				}
			}
		}

		$fightgroup = pdo_getall(PDO_NAME . 'fightgroup_goods', array('merchantid' => $id, 'uniacid' => $_W['uniacid'], 'status' => 1));

		if (p('bargain')) {
			$sbargain = pdo_getall(PDO_NAME . 'bargain_activity', array('sid' => $id, 'uniacid' => $_W['uniacid'], 'status' => 2), array('thumb', 'id', 'name', 'thumb', 'price', 'oldprice'));
		}

		$comment = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_comment') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND checkone = 2 AND sid = ' . $id . ' ORDER BY id DESC limit 3'));

		if ($comment) {
			$merchantid = $id;

			foreach ($comment as $sk => &$com) {
				$com['pic'] = unserialize($com['pic']);

				if ($com['pic']) {
					foreach ($com['pic'] as $k => &$v) {
						$v = tomedia($v);
					}
				}
			}
		}

		$share = Setting::agentsetting_read('shareset');

		if ($share['merdetail_title']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$typename = pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $store['twolevel']), 'name');
			$title = $share['merdetail_title'];
			$title = str_replace('[昵称]', $nickname, $title);
			$title = str_replace('[时间]', $time, $title);
			$title = str_replace('[商户名称]', $store['storename'], $title);
			$title = str_replace('[商户电话]', $store['mobile'], $title);
			$title = str_replace('[商户分类]', $typename, $title);
			$desc = $share['merdetail_desc'];
			$desc = str_replace('[昵称]', $nickname, $desc);
			$desc = str_replace('[时间]', $time, $desc);
			$desc = str_replace('[商户名称]', $store['storename'], $desc);
			$desc = str_replace('[商户电话]', $store['mobile'], $desc);
			$desc = str_replace('[商户分类]', $typename, $desc);
			$_W['wlsetting']['share']['share_title'] = $title;
			$_W['wlsetting']['share']['share_desc'] = $desc;
			$_W['wlsetting']['share']['share_image'] = $share['merdetail_image'] ? $share['merdetail_image'] : $store['logo'];
		}
		else {
			$_W['wlsetting']['share']['share_title'] = !empty($store['storename']) ? $store['storename'] : $_W['wlsetting']['share']['share_title'];
			$_W['wlsetting']['share']['share_image'] = !empty($store['logo']) ? $store['logo'] : $_W['wlsetting']['share']['share_image'];
		}

		include wl_template('store/detail');
	}

	public function getstore()
	{
		global $_W;
		global $_GPC;
		$lng = !empty($_GPC['lng']) ? $_GPC['lng'] : 0;
		$lat = !empty($_GPC['lat']) ? $_GPC['lat'] : 0;
		if ($lng && $lat) {
			$_SESSION['lng'] = $lng;
			$_SESSION['lat'] = $lat;
		}

		if (trim($_GPC['iscommon']) == 'yes') {
			$where = ' WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 2 AND enabled = 1 AND iscommon = 1 AND ( listshow = 0 OR listshow IS NULL )';
		}
		else {
			$where = ' WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 2 AND enabled = 1 AND aid = ' . $_W['aid'] . ' AND ( listshow = 0 OR listshow IS NULL )';
		}

		if (!empty($_GPC['pid'])) {
			$parentid = pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $_GPC['pid']), 'parentid');

			if ($parentid) {
				$where .= ' AND twolevel = ' . $_GPC['pid'] . ' ';
			}
			else {
				$where .= ' AND onelevel = ' . $_GPC['pid'] . ' ';
			}
		}
		else {
			if (!empty($_GPC['cid'])) {
				$where .= ' AND onelevel = ' . $_GPC['cid'] . ' ';
			}
		}

		if (!empty($_GPC['groupid'])) {
			$where .= ' AND groupid = ' . $_GPC['groupid'] . ' ';
		}

		if (!empty($_GPC['distid'])) {
			$level = pdo_getcolumn(PDO_NAME . 'area', array('id' => $_W['areaid']), 'level');

			if ($level == 1) {
				$where .= ' AND areaid = ' . $_GPC['distid'] . ' ';
			}
			else {
				$where .= ' AND distid = ' . $_GPC['distid'] . ' ';
			}
		}

		if (!empty($_GPC['keyword'])) {
			$where .= ' AND storename LIKE \'%' . $_GPC['keyword'] . '%\' ';
		}

		if (!empty($_GPC['near'])) {
			$nearid = $_GPC['near'];
		}
		else {
			$nearid = 3;
		}

		$locations = pdo_fetchall('SELECT id,storename,logo,location,storehours,onelevel,twolevel,createtime,listorder,score,pv,panorama,videourl FROM ' . tablename(PDO_NAME . 'merchantdata') . $where);
		$locations = Store::getstores($locations, $lng, $lat, $nearid);

		if ($_GPC['pageflag'] == 1) {
			$plugin = Setting::agentsetting_read('pluginlist');
			$limit = $plugin['sjlimit'] ? $plugin['sjlimit'] : 10;
			$locations = array_slice($locations, 0, $limit);
		}

		foreach ($locations as $key => &$v) {
			if ($_W['wlsetting']['halfcard']['status']) {
				$halfcard = pdo_get(PDO_NAME . 'halfcardlist', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'merchantid' => $v['id'], 'status' => 1), array('title'));
				$package = pdo_get(PDO_NAME . 'package', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'merchantid' => $v['id'], 'status' => 1), array('title'));
			}

			$rush = pdo_get(PDO_NAME . 'rush_activity', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'sid' => $v['id'], 'status' => 2), array('name'));
			$coupon = pdo_get(PDO_NAME . 'couponlist', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'merchantid' => $v['id'], 'status' => 1), array('title'));
			$fightgroup = pdo_get(PDO_NAME . 'fightgroup_goods', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'merchantid' => $v['id'], 'status' => 1), array('name'));

			if (p('groupon')) {
				$groupon = pdo_get(PDO_NAME . 'groupon_activity', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'sid' => $v['id'], 'status' => 2), array('name'));
			}

			if ($halfcard) {
				$activity['type'] = 1;
				$activity['name'] = $halfcard['title'];
				$v['activity'][] = $activity;
			}

			if ($rush) {
				$activity['type'] = 2;
				$activity['name'] = $rush['name'];
				$v['activity'][] = $activity;
			}

			if ($coupon) {
				$activity['type'] = 3;
				$activity['name'] = $coupon['title'];
				$v['activity'][] = $activity;
			}

			if ($fightgroup) {
				$activity['type'] = 4;
				$activity['name'] = $fightgroup['name'];
				$v['activity'][] = $activity;
			}

			if ($package) {
				$activity['type'] = 5;
				$activity['name'] = $package['title'];
				$v['activity'][] = $activity;
			}

			if ($groupon) {
				$activity['type'] = 6;
				$activity['name'] = $groupon['name'];
				$v['activity'][] = $activity;
			}

			if ($lng == 100 || $lat == 100) {
				$v['distance'] = '未定位';
			}
		}

		exit(json_encode($locations));
	}

	public function getxgstore()
	{
		global $_W;
		global $_GPC;
		$lng = !empty($_GPC['lng']) ? $_GPC['lng'] : 0;
		$lat = !empty($_GPC['lat']) ? $_GPC['lat'] : 0;
		$id = $_GPC['id'];
		$parm = array('uniacid' => $_W['uniacid'], 'status' => 2, 'enabled' => 1, 'aid' => $_W['aid']);
		$merchant = Store::getSingleStore($id);
		$parm['onelevel'] = $merchant['onelevel'];
		$locations = pdo_getall(PDO_NAME . 'merchantdata', $parm, array('id', 'storename', 'logo', 'location', 'storehours', 'onelevel', 'twolevel', 'createtime', 'listorder', 'score'));
		$locations = Store::getstores($locations, $lng, $lat, 2);

		foreach ($locations as $key => &$v) {
			if ($v['id'] == $id) {
				$v['flag'] = 0;
			}
			else {
				$v['flag'] = 1;
			}
		}

		exit(json_encode($locations));
	}

	public function getcoupon()
	{
		global $_W;
		global $_GPC;
		$lng = $_GPC['lng'];
		$lat = $_GPC['lat'];
		$parm = array('uniacid' => $_W['uniacid'], 'status' => 2, 'enabled' => 1, 'aid' => $_W['aid']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;

		if (!empty($_GPC['cid'])) {
			$parm['onelevel'] = intval($_GPC['cid']);
		}

		if (!empty($_GPC['pid'])) {
			$parm['twolevel'] = intval($_GPC['pid']);
		}

		if (!empty($_GPC['distid'])) {
			$parm['distid'] = intval($_GPC['distid']);
		}

		$couponlist = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_couponlist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid']));

		if ($couponlist) {
			$ids = '(';

			foreach ($couponlist as $key => $v) {
				if ($key == 0) {
					$ids .= $v['merchantid'];
				}
				else {
					$ids .= ',' . $v['merchantid'];
				}
			}

			$ids .= ')';
			$parm['id#'] = $ids;
		}
		else {
			$parm['id#'] = '(0)';
		}

		$locations = wlCoupon::getNumstore('*', $parm, 'ID DESC', 0, 0, 0);
		$locations = $locations[0];
		$locations = wlCoupon::getstores($locations, $lng, $lat);

		foreach ($locations as $key => &$v) {
			$coupon = pdo_getall(PDO_NAME . 'couponlist', array('status' => 1, 'merchantid' => $v['id'], 'is_show' => 0));

			if ($coupon) {
				foreach ($coupon as $k => &$asd) {
					$asd['storename'] = $v['storename'];
					$asd['distance'] = $v['distance'];
					$asd['logo'] = tomedia($asd['logo']);
					$asd['indeximg'] = tomedia($asd['indeximg']);

					if ($asd['is_charge'] == 1) {
						$asd['charge'] = '￥' . $asd['price'];
					}
					else {
						$asd['charge'] = '免费';
					}

					$asd['href'] = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $asd['id']));

					if ($asd['time_type'] == 1) {
						if (time() < $asd['endtime']) {
							$newlocations[] = $asd;
						}
					}
					else {
						$newlocations[] = $asd;
					}
				}
			}
		}

		$i = 0;

		while ($i < count($newlocations) - 1) {
			$j = 0;

			while ($j < count($newlocations) - 1 - $i) {
				if ($newlocations[$j]['indexorder'] < $newlocations[$j + 1]['indexorder']) {
					$temp = $newlocations[$j + 1];
					$newlocations[$j + 1] = $newlocations[$j];
					$newlocations[$j] = $temp;
				}

				++$j;
			}

			++$i;
		}

		exit(json_encode($newlocations));
	}

	public function getrush()
	{
		global $_W;
		global $_GPC;
		$lng = !empty($_GPC['lng']) ? $_GPC['lng'] : $_SESSION['lng'];
		$lat = !empty($_GPC['lat']) ? $_GPC['lat'] : $_SESSION['lat'];
		$rushs = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_rush_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND is_indexshow = 1 AND status IN (1,2) ORDER BY id DESC'));

		foreach ($rushs as $key => &$v) {
			$store = pdo_get('wlmerchant_merchantdata', array('id' => $v['sid']), array('storename', 'logo', 'location'));
			$location = unserialize($store['location']);
			$v['distance'] = Store::getdistance($lng, $lat, $location['lng'], $location['lat']);
			$v['storename'] = $store['storename'];
			$v['storelogo'] = tomedia($store['logo']);
			$v['href'] = app_url('rush/home/detail', array('id' => $v['id']));
			$v['endtime'] = date('Y-m-d H:i', $v['endtime']);
			$v['starttime'] = date('Y-m-d H:i', $v['starttime']);
			$v['thumb'] = tomedia($v['thumb']);
		}

		$plugin = Setting::agentsetting_read('pluginlist');

		switch ($plugin['qgsort']) {
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

		if ($plugin['qgsort'] != 1) {
			$rushs = Store::wl_sort($rushs, $sort_key, $sort_order);
		}

		$limit = $plugin['qglimit'] ? $plugin['qglimit'] : 10;
		$rushs = array_slice($rushs, 0, $limit);
		exit(json_encode($rushs));
	}

	public function getgroupon()
	{
		global $_W;
		global $_GPC;
		$base = Setting::agentsetting_read('groupon');
		$lng = !empty($_GPC['lng']) ? $_GPC['lng'] : $_SESSION['lng'];
		$lat = !empty($_GPC['lat']) ? $_GPC['lat'] : $_SESSION['lat'];
		$groupons = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_groupon_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND is_indexshow = 0 AND status IN (1,2) ORDER BY status DESC,starttime ASC'));

		foreach ($groupons as $key => &$v) {
			$store = pdo_get('wlmerchant_merchantdata', array('id' => $v['sid']), array('storename', 'logo', 'location'));
			$location = unserialize($store['location']);
			$v['distance'] = Store::getdistance($lng, $lat, $location['lng'], $location['lat']);
			$v['storename'] = $store['storename'];
			$v['storelogo'] = tomedia($store['logo']);
			$v['href'] = app_url('groupon/grouponapp/groupondetail', array('cid' => $v['id']));
			$v['endtime'] = date('Y-m-d H:i', $v['endtime']);
			$v['thumb'] = tomedia($v['thumb']);
			$v['salenum'] = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE fkid = ' . $v['id'] . ' and plugin = \'groupon\' and status in (0,1,2,3,6,7,9)'));
			$v['salenum'] = $v['salenum'] ? $v['salenum'] : 0;
			$v['salenum'] = $v['salenum'] + $v['falsesalenum'];
			$v['tags'] = unserialize($v['listtag']);

			if ($base['discountstatus']) {
				$v['discountstatus'] = 1;
			}
		}

		$plugin = Setting::agentsetting_read('pluginlist');

		switch ($plugin['tgsort']) {
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

		$groupons = Store::wl_sort($groupons, $sort_key, $sort_order);
		$limit = $plugin['tglimit'] ? $plugin['tglimit'] : 10;
		$groupons = array_slice($groupons, 0, $limit);

		foreach ($groupons as $kk => &$vv) {
			if (0 < $vv['distance']) {
				if (1000 < $vv['distance']) {
					$vv['distance'] = floor($vv['distance'] / 1000 * 10) / 10 . 'km';
				}
				else {
					$vv['distance'] = round($vv['distance']) . 'm';
				}
			}
			else {
				$vv['distance'] = 0;
			}
		}

		exit(json_encode($groupons));
	}

	public function getcategory()
	{
		global $_W;
		global $_GPC;
		$parentid = intval($_GPC['pid']);

		if ($parentid) {
			$categoryes = Util::getNumData('id,name', PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'parentid' => $parentid, 'enabled' => 1), 'displayorder DESC');
		}
		else {
			$categoryes = Util::getNumData('id,name', PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'parentid' => 0, 'enabled' => 1), 'displayorder DESC');
		}

		exit(json_encode($categoryes[0]));
	}

	public function getarea()
	{
		global $_W;
		global $_GPC;
		$areas = pdo_getall(PDO_NAME . 'area', array('pid' => $_W['areaid']), array('id', 'name'));
		exit(json_encode($areas));
	}

	public function collect()
	{
		global $_W;
		global $_GPC;
		$storeid = intval($_GPC['id']);
		$collects = pdo_get(PDO_NAME . 'collect', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'storeid' => $storeid), 'id');

		if ($collects) {
			$re = pdo_delete(PDO_NAME . 'collect', array('id' => $collects));
			pdo_delete(PDO_NAME . 'storefans', array('uniacid' => $_W['uniacid'], 'sid' => $storeid, 'mid' => $_W['mid']));
		}
		else {
			$re = pdo_insert(PDO_NAME . 'collect', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'storeid' => $storeid, 'createtime' => time()));
			Store::addFans($storeid, $_W['mid'], 1);
		}

		if ($re) {
			exit(json_encode(array('result' => 1)));
		}
		else {
			exit(json_encode(array('result' => 2)));
		}
	}

	public function commentpage()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商户评价 - ' . $_W['wlsetting']['base']['name'] : '商户评价';
		$status = $_GPC['status'];
		$id = $_GPC['merchantid'];
		$num1 = $num2 = $num3 = $num4 = 0;
		$time = time() - 3600 * 24 * 7;

		if (isset($_GPC['goodsid'])) {
			$goodsId = $_GPC['goodsid'];
			$orderIdStr = implode(array_column(pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND plugin = \'wlfightgroup\' AND fkid = ' . $goodsId . ' ORDER BY id DESC')), 'id'), ',');
			$where = 'uniacid = ' . $_W['uniacid'] . ' AND checkone = 2 AND sid = ' . $id . ' AND idoforder IN (' . $orderIdStr . ')';
		}
		else {
			$where = 'uniacid = ' . $_W['uniacid'] . ' AND checkone = 2 AND sid = ' . $id;
		}

		$num1 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename('wlmerchant_comment') . (' WHERE ' . $where));
		$num2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename('wlmerchant_comment') . (' WHERE ' . $where . ' AND ispic = 1'));
		$num3 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename('wlmerchant_comment') . (' WHERE ' . $where . ' AND level = 3'));
		$num4 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename('wlmerchant_comment') . (' WHERE ' . $where . ' AND createtime > ' . $time));
		include wl_template('store/commentpage');
	}

	public function getcomment()
	{
		global $_W;
		global $_GPC;
		$status = $_GPC['status'];
		$id = $_GPC['merchantid'];

		if (isset($_GPC['goodsid'])) {
			$goodsId = $_GPC['goodsid'];
			$orderIdStr = implode(array_column(pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND plugin = \'wlfightgroup\' AND fkid = ' . $goodsId . ' ORDER BY id DESC')), 'id'), ',');
			$where = 'uniacid = ' . $_W['uniacid'] . ' AND checkone = 2 AND sid = ' . $id . ' AND idoforder IN (' . $orderIdStr . ')';
		}
		else {
			$where = 'uniacid = ' . $_W['uniacid'] . ' AND checkone = 2 AND sid = ' . $id;
		}

		if (empty($status)) {
			$comment = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_comment') . ('WHERE ' . $where . ' ORDER BY id DESC'));
		}
		else if ($status == 2) {
			$comment = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_comment') . ('WHERE ' . $where . ' AND level = 3 ORDER BY id DESC'));
		}
		else if ($status == 3) {
			$time = time() - 3600 * 24 * 7;
			$comment = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_comment') . ('WHERE ' . $where . ' AND createtime > ' . $time . ' ORDER BY id DESC'));
		}
		else {
			if ($status == 1) {
				$comment = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_comment') . ('WHERE ' . $where . ' AND ispic = 1 ORDER BY id DESC'));
			}
		}

		if ($comment) {
			foreach ($comment as $sk => &$com) {
				$com['pic'] = unserialize($com['pic']);
				$com['createtime'] = date('Y-m-d', $com['createtime']);

				if ($com['pic']) {
					foreach ($com['pic'] as $k => &$v) {
						$v = tomedia($v);
					}
				}
			}
		}

		exit(json_encode($comment));
	}

	public function dynamiclist()
	{
		global $_W;
		global $_GPC;
		$where = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'sid' => intval($_GPC['storeid']));

		if ($_GPC['dod'] == 'detail') {
			$where['status'] = array(1, 2);
		}

		$list = pdo_getall('wlmerchant_store_dynamic', $where, '', '', 'createtime DESC');

		foreach ($list as $key => &$dyn) {
			$member = pdo_get('wlmerchant_member', array('id' => $dyn['mid']), array('avatar', 'nickname'));
			$dyn['avatar'] = tomedia($member['avatar']);
			$dyn['name'] = $member['nickname'];
			$dyn['time'] = date('m-d H:i:s', $dyn['createtime']);
			$dyn['a'] = 'javascript:;';
			$imgs = unserialize($dyn['imgs']);

			foreach ($imgs as $key => &$img) {
				$dyn['img'][] = tomedia($img);
			}
		}

		exit(json_encode($list));
	}

	public function getdistance()
	{
		global $_W;
		global $_GPC;
		$_SESSION['lng'] = $_GPC['lng2'];
		$_SESSION['lat'] = $_GPC['lat2'];
		$distance = Store::getdistance($_GPC['lng1'], $_GPC['lat1'], $_GPC['lng2'], $_GPC['lat2']);

		if (1000 < $distance) {
			$distance = floor($distance / 1000 * 10) / 10 . 'km';
		}
		else {
			$distance = round($distance) . 'm';
		}

		exit(json_encode($distance));
	}

	public function qrcodeimg()
	{
		global $_W;
		global $_GPC;
		load()->library('qrcode');
		$url = $_GPC['url'];

		if (empty($_W['wlsetting']['base']['qrstatus'])) {
			ob_clean();
		}

		$result = Util::long2short($url);

		if (!is_error($result)) {
			$url = $result['short_url'];
		}

		header('Content-type: image/png');
		QRcode::png($url, false, QR_ECLEVEL_L, 16, 2);
	}

	public function location()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$store = Store::getSingleStore($id);
		$data['location'] = unserialize($store['location']);
		$data['storename'] = $store['storename'];
		$data['address'] = $store['address'];

		if ($data) {
			wl_json(0, '获取成功', $data);
		}
		else {
			wl_json(1, '获取失败', $data);
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
