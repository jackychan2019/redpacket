<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Pocket_WeliamController extends Weliam_merchantModuleSite
{
	public function index()
	{
		global $_GPC;
		global $_W;
		$data = Setting::agentsetting_read('pocket');
		$searchSet = array('search_float' => $data['search_float'], 'search_bgColor' => $data['search_bgColor']);
		$data['pocketname'] = $data['pocketname'] ? $data['pocketname'] : '掌上信息';
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $data['pocketname'] . ' - ' . $_W['wlsetting']['base']['name'] : $data['pocketname'];
		$data['look'] = intval($data['look']);

		if (empty($data['status'])) {
			wl_message(array('errno' => 1, 'message' => $data['pocketname'] . '尚未开启'));
		}

		$fabu = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_pocket_informations') . (' WHERE status = 0 AND uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid']));
		$share = pdo_fetchcolumn('SELECT SUM(share) FROM ' . tablename('wlmerchant_pocket_informations') . (' WHERE status = 0 AND uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid']));
		$minup = $data['minup'] ? $data['minup'] : 1;
		$maxup = $data['maxup'] ? $data['maxup'] : 5;
		$look = rand($minup, $maxup);

		if ($all[0]) {
			$fabu = count($all[0]);

			foreach ($all[0] as $key => $v) {
				$share += $v['share'];
			}
		}

		$fabu = $fabu + $data['fabu'];
		$look = $look + $data['look'];
		$share = $share + $data['share'];
		$data['look'] = $look;
		Setting::agentsetting_save($data, 'pocket');
		$uniacid = $_W['uniacid'];
		$list = Pocket::getslides($uniacid);
		$slide = $list[0];

		foreach ($slide as $key => &$value) {
			$value['link'] = $value['url'];
			$value['thumb'] = $value['img'];
		}

		$type = Util::getNumData('*', PDO_NAME . 'pocket_type', array('type' => 0, 'status' => 1, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), 'sort desc');
		$type = $type[0];

		foreach ($type as $nk => &$nval) {
			$nval['name'] = $nval['title'];
			$nval['thumb'] = $nval['img'];
			$nval['link'] = $nval['isnav'] ? $nval['url'] : app_url('pocket/pocket/catepage', array('id' => $nval['id']));
		}

		$categroy = Util::getNumData('*', PDO_NAME . 'pocket_type', array('type' => 0, 'status' => 1, 'uniacid' => $_W['uniacid'], 'isnav' => 0, 'aid' => $_W['aid']), 'sort desc');
		$categroy = $categroy[0];
		$top = Util::getNumData('*', PDO_NAME . 'pocket_informations', array('top#' => '(0,1)', 'status' => 0, 'uniacid' => $uniacid, 'aid' => $_W['aid']), 'top DESC,id DESC limit 10');

		if ($top) {
			$top = $top[0];

			if ($top) {
				foreach ($top as $key => &$v) {
					$v['time'] = date('m-d', $v['createtime']);

					if ($v['type']) {
						$v['typename'] = pdo_getcolumn(PDO_NAME . 'pocket_type', array('id' => $v['type']), 'title');
					}
					else {
						$v['typename'] = '官方公告';
					}

					$v['a'] = app_url('pocket/pocket/detail', array('id' => $v['id']));
				}
			}
		}

		if ($data['share_title'] || $data['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];

			if ($data['share_title']) {
				$title = $data['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[系统名称]', $sysname, $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}
			else {
				$_W['wlsetting']['share']['share_title'] = !empty($data['share_title']) ? $data['share_title'] : $_W['wlsetting']['share']['share_title'];
			}

			if ($data['share_desc']) {
				$desc = $data['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[系统名称]', $sysname, $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
			else {
				$_W['wlsetting']['share']['share_desc'] = !empty($data['share_desc']) ? $data['share_desc'] : $_W['wlsetting']['share']['share_desc'];
			}
		}
		else {
			$_W['wlsetting']['share']['share_title'] = !empty($data['share_title']) ? $data['share_title'] : $_W['wlsetting']['share']['share_title'];
			$_W['wlsetting']['share']['share_desc'] = !empty($data['share_desc']) ? $data['share_desc'] : $_W['wlsetting']['share']['share_desc'];
		}

		$_W['wlsetting']['share']['share_image'] = !empty($data['share_image']) ? $data['share_image'] : $_W['wlsetting']['share']['share_image'];
		include wl_template('pocket/index');
	}

	public function gettops()
	{
		global $_GPC;
		global $_W;
		$data = Setting::agentsetting_read('pocket');
		$page = $_GPC['pageindex'];
		$pagesize = $_GPC['pagesize'];
		$lng = !empty($_GPC['lng']) ? $_GPC['lng'] : 0;
		$lat = !empty($_GPC['lat']) ? $_GPC['lat'] : 0;

		if ($data['listorder'] == 1) {
			$where = 'top DESC,look DESC';
		}
		else if ($data['listorder'] == 2) {
			$where = 'top DESC,share DESC';
		}
		else if ($data['listorder'] == 3) {
			$where = 'top DESC,likenum DESC';
		}
		else {
			$where = 'top DESC,id DESC';
		}

		$top = Util::getNumData('*', PDO_NAME . 'pocket_informations', array('top#' => '(0,1,3)', 'status' => 0, 'uniacid' => $uniacid, 'aid' => $_W['aid']), $where, $page, $pagesize, 1);

		if ($top) {
			$top = $top[0];

			if ($top) {
				foreach ($top as $key => &$v) {
					$v['content'] = nl2br($v['content']);

					if (empty($v['avatar'])) {
						if ($v['mid']) {
							$v['avatar'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $v['mid']), 'avatar');
						}
						else {
							$v['avatar'] = $data['kefu_avatar'];
						}
					}

					$v['avatar'] = tomedia($v['avatar']);
					$v['name'] = $v['nickname'];
					$v['time'] = date('m-d', $v['createtime']);

					if ($v['type']) {
						$v['typename'] = pdo_getcolumn(PDO_NAME . 'pocket_type', array('id' => $v['type']), 'title');
					}
					else {
						$v['typename'] = '官方公告';
					}

					$v['img'] = unserialize($v['img']);
					$v['a'] = app_url('pocket/pocket/detail', array('id' => $v['id']));

					if ($v['img']) {
						foreach ($v['img'] as $key => &$w) {
							$w = tomedia($w);
						}
					}

					$v['keyword'] = unserialize($v['keyword']);
					$conum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_pocket_comment') . (' WHERE tid = ' . $v['id']));
					$renum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_pocket_reply') . (' WHERE tid = ' . $v['id']));
					$v['commentnum'] = $conum + $renum;
					pdo_update('wlmerchant_pocket_informations', array('look' => $v['look'] + 1), array('id' => $v['id']));
					if ($v['locastatus'] && $lng && $lat) {
						$location = unserialize($v['location']);
						$v['distance'] = Store::getdistance($location['lng'], $location['lat'], $lng, $lat);

						if (1000 < $v['distance']) {
							$v['showdistance'] = floor($v['distance'] / 1000 * 10) / 10 . 'km';
						}
						else {
							$v['showdistance'] = round($v['distance']) . 'm';
						}
					}
					else {
						$v['distance'] = 0;
					}
				}
			}
		}

		exit(json_encode($top));
	}

	public function category()
	{
		global $_GPC;
		global $_W;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '分类列表 - ' . $_W['wlsetting']['base']['name'] : '分类列表';
		$data = Setting::agentsetting_read('pocket');
		$data['pocketname'] = $data['pocketname'] ? $data['pocketname'] : '掌上信息';
		Member::checklogin(app_url('pocket/pocket/category'));
		$black = pdo_getcolumn('wlmerchant_pocket_blacklist', array('mid' => $_W['mid'], 'aid' => $_W['aid'], 'uniacid' => $_W['uniacid']), 'id');

		if ($black) {
			wl_message('您已被加入黑名单，如有疑问请联系平台');
		}

		$categroy = Util::getNumData('*', PDO_NAME . 'pocket_type', array('type' => 0, 'status' => 1, 'isnav' => 0, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), 'sort desc');
		$categroy = $categroy[0];

		foreach ($categroy as $key => &$value) {
			$second = pdo_getall(PDO_NAME . 'pocket_type', array('type' => $value['id'], 'status' => 1, 'uniacid' => $_W['uniacid']), array('id', 'title'), '', 'sort desc');

			if ($second) {
				foreach ($second as $kk => &$vv) {
					$vv['url'] = app_url('pocket/pocket/fabupage', array('id' => $vv['id']));
				}

				$value['second'] = json_encode($second);
				$value['url'] = 'javascript:;';
			}
			else {
				$value['url'] = app_url('pocket/pocket/fabupage', array('id' => $value['id']));
			}
		}

		$this->share_info();
		include wl_template('pocket/category');
	}

	public function fabupage()
	{
		global $_GPC;
		global $_W;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '发布帖子 - ' . $_W['wlsetting']['base']['name'] : '发布帖子';
		$typeid = $_GPC['id'];
		$data = Setting::agentsetting_read('pocket');
		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);

		if (in_array('pocket', $mastmobile)) {
			$mtel = 1;
		}
		else {
			$mtel = 0;
		}

		$type = pdo_get('wlmerchant_pocket_type', array('id' => $typeid), array('price', 'keyword'));

		if ($type['keyword']) {
			$keywords = rtrim($type['keyword'], ',');
			$keywords = explode(',', $keywords);
		}

		$inid = $_GPC['inid'];

		if ($inid) {
			$inform = Pocket::getInformations($inid);

			if ($inform['mid'] != $_W['mid']) {
				wl_message('非法路径', 'close', 'error');
			}

			$member['nickname'] = $inform['nickname'];
			$member['mobile'] = $inform['phone'];
			$type = pdo_get('wlmerchant_pocket_type', array('id' => $inform['type']), array('price', 'keyword'));

			if ($type['keyword']) {
				$keywords = rtrim($type['keyword'], ',');
				$keywords = explode(',', $keywords);
			}

			$imgs = unserialize($inform['img']);
			$informkeyword = unserialize($inform['keyword']);
			$price = $data['price'];

			if ($data['freestatus']) {
				$free = 1;

				foreach ($price as $key => &$v) {
					$v['price'] = '0';
				}
			}
		}
		else {
			$member = pdo_get('wlmerchant_member', array('id' => $_W['mid']), array('mobile', 'nickname'));
			$price = $data['price'];

			if ($data['freestatus']) {
				$free = 1;

				foreach ($price as $key => &$v) {
					$v['price'] = '0';
				}
			}
		}

		$url = app_url('pocket/pocket/fabupage', array('id' => $typeid, 'inid' => $inid));
		$this->share_info();
		include wl_template('pocket/fabuinfo');
	}

	public function fabu_ajax()
	{
		global $_GPC;
		global $_W;
		$type = $_GPC['typeid'];
		$tieziid = $_GPC['tieziid'];
		$freeday = $_GPC['freeday'];
		$redpack = $_GPC['redpack'];
		$package = $_GPC['package'];
		$set = Setting::agentsetting_read('pocket');
		$member = Util::getSingelData('mobile', PDO_NAME . 'member', array('id' => $_W['mid']));
		$data = Setting::agentsetting_read('pocket');
		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
		if (empty($member['mobile']) && in_array('pocket', $mastmobile)) {
			wl_message(array('errno' => 2, 'message' => '未绑定手机号，去绑定？'));
		}

		$black = pdo_getcolumn('wlmerchant_pocket_blacklist', array('mid' => $_W['mid'], 'aid' => $_W['aid'], 'uniacid' => $_W['uniacid']), 'id');

		if ($black) {
			wl_message(array('errno' => 1, 'message' => '您已被加入黑名单，如有疑问请联系平台'));
		}

		$keyword = $_GPC['keyword'];

		if ($keyword) {
			$keyword = rtrim($keyword, ',');
			$keyword = explode(',', $keyword);

			if (5 < count($keyword)) {
				$keyword = array_slice($keyword, 0, 5);
			}

			$keyword = serialize($keyword);
		}

		$locastatus = $_GPC['locastatus'];
		$location = array('lat' => $_GPC['lat'], 'lng' => $_GPC['lng']);
		$location = serialize($location);
		$address = $_GPC['address'];

		if ($tieziid) {
			$inform = Pocket::getInformations($tieziid);
			$data = array('content' => $_GPC['commenttext'], 'img' => serialize($_GPC['thumbs']), 'nickname' => $_GPC['username'], 'phone' => $_GPC['mobile'], 'share_title' => $_GPC['sharetitle'], 'keyword' => $keyword, 'locastatus' => $locastatus, 'location' => $location, 'address' => $address);
			$data['content'] = htmlspecialchars_decode($data['content']);

			if ($freeday) {
				$data['top'] = 1;

				if (time() < $inform['endtime']) {
					$data['endtime'] = $inform['endtime'] + $freeday * 24 * 3600;
				}
				else {
					$data['endtime'] = time() + $freeday * 24 * 3600;
				}
			}

			if ($set['passstatus']) {
				$data['status'] = 0;
			}
			else {
				$data['status'] = 1;
			}

			$res = pdo_update('wlmerchant_pocket_informations', $data, array('id' => $tieziid));

			if ($data['status']) {
				Pocket::examinenotice($tieziid);
			}
			else {
				Pocket::examinenotice($tieziid, 1);
			}

			exit(json_encode(array('errno' => 0, 'tieziid' => $tieziid)));
		}
		else {
			if ($set['alllimit']) {
				$allnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_pocket_informations') . (' WHERE mid = ' . $_W['mid'] . ' AND aid = ' . $_W['aid'] . ' AND status != 3'));

				if ($set['alllimit'] <= $allnum) {
					exit(json_encode(array('errno' => 4, 'message' => '您发布的帖子过多，请删除过期帖子')));
				}
			}

			if ($set['daylimit']) {
				$today = strtotime(date('Y-m-d', time()));
				$daynum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_pocket_informations') . (' WHERE mid = ' . $_W['mid'] . ' AND aid = ' . $_W['aid'] . ' AND createtime > ' . $today . ' AND status != 3'));

				if ($set['daylimit'] <= $daynum) {
					exit(json_encode(array('errno' => 5, 'message' => '您今天发布的帖子过多，请明天再来')));
				}
			}

			$onetype = pdo_getcolumn(PDO_NAME . 'pocket_type', array('id' => $type), 'type');
			$typeprice = pdo_getcolumn(PDO_NAME . 'pocket_type', array('id' => $type), 'price');

			if ($onetype == 0) {
				$onetype = $type;
			}

			$avatar = pdo_getcolumn('wlmerchant_member', array('id' => $_W['mid']), 'avatar');
			$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'content' => $_GPC['commenttext'], 'img' => serialize($_GPC['thumbs']), 'mid' => $_W['mid'], 'top' => 0, 'look' => 0, 'likenum' => 0, 'share' => 0, 'endtime' => 0, 'avatar' => $avatar, 'onetype' => $onetype, 'type' => $type, 'nickname' => $_GPC['username'], 'phone' => $_GPC['mobile'], 'share_title' => $_GPC['sharetitle'], 'createtime' => time(), 'keyword' => $keyword, 'redpackstatus' => 0, 'locastatus' => $locastatus, 'location' => $location, 'address' => $address);
			$data['content'] = htmlspecialchars_decode($data['content']);
			if (p('sharegift') && $redpack) {
				$data['redpack'] = sprintf('%0.2f', $redpack);
				$data['sredpack'] = sprintf('%0.2f', $redpack);
				$data['package'] = intval($package);

				if (empty($data['package'])) {
					$data['package'] = 1;
				}
			}

			if (0 < $typeprice) {
				$data['status'] = 5;
			}
			else if ($set['passstatus']) {
				$data['status'] = 0;
				$mes = '发帖成功';
			}
			else {
				$data['status'] = 1;
				$mes = '发帖成功,请等待系统审核';
			}

			if ($freeday) {
				$data['top'] = 1;
				$data['endtime'] = time() + $freeday * 24 * 3600;
			}

			pdo_insert(PDO_NAME . 'pocket_informations', $data);
			$res = pdo_insertid();

			if ($res) {
				if (0 < $typeprice) {
					exit(json_encode(array('errno' => 6, 'message' => '进入支付页面', 'tieziid' => $res)));
				}
				else {
					if ($data['status']) {
						Pocket::examinenotice($res);
					}
					else {
						Pocket::examinenotice($res, 1);
					}

					exit(json_encode(array('errno' => 0, 'message' => $mes, 'tieziid' => $res)));
				}
			}
			else {
				exit(json_encode(array('errno' => 1, 'message' => '发帖失败,请重试')));
			}
		}
	}

	public function createOrder()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$fk_id = $_GPC['fk_id'];
			$day = $_GPC['day'];
			$redpack = $_GPC['redpack'] ? $_GPC['redpack'] : 0;
			$data = Setting::agentsetting_read('pocket');
			$num = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_pocket_informations') . (' where uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 0 AND top = 1'));
			if (0 < $data['number'] && $data['number'] <= $num) {
				wl_message(array('errno' => 1, 'message' => '置顶帖子过多，无法申请置顶'));
			}

			$price = $data['price'];

			foreach ($price as $key => $v) {
				if ($day == $v['day']) {
					$orderprice = $v['price'];
				}
			}

			$orderprice = $orderprice + $redpack;
			$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'aid' => $_W['aid'], 'fkid' => $fk_id, 'sid' => 0, 'status' => 0, 'paytype' => 0, 'createtime' => time(), 'orderno' => createUniontid(), 'price' => $orderprice, 'num' => $day, 'plugin' => 'pocket', 'payfor' => 'pocketsharge');
			$res = Pocket::saveFightOrder($data);

			if ($res) {
				wl_message(array('errno' => 0, 'message' => $res));
			}
			else {
				wl_message(array('errno' => 1, 'message' => '未知错误，请重试。'));
			}
		}

		$orderId = $_GPC['orderId'];
		$order = pdo_get('wlmerchant_order', array('id' => $orderId));

		if (empty($order)) {
			wl_message('传入的参数有误，请重试');
		}

		$params = array('tid' => $order['orderno'], 'ordersn' => $order['orderno'], 'title' => '掌上信息发帖', 'fee' => $order['price'], 'bankrid' => '');
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => 'Pocket', 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Pocket', 'payfor' => 'Pocketsharge', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}

	/**
     * Comment: 红包订单支付
     * Author: zzw
     */
	public function RedOrderPay()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$fk_id = $_GPC['fk_id'];
			$redpack = $_GPC['redpack'];

			if ($redpack <= 0) {
				wl_message('传入的参数有误，请重试');
			}

			$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'aid' => $_W['aid'], 'fkid' => $fk_id, 'sid' => 0, 'status' => 0, 'paytype' => 0, 'createtime' => time(), 'orderno' => createUniontid(), 'price' => $redpack, 'num' => 1, 'plugin' => 'pocket', 'payfor' => 'pocketredpack');
			$res = Pocket::saveFightOrder($data);

			if ($res) {
				wl_message(array('errno' => 0, 'message' => $res));
			}
			else {
				wl_message(array('errno' => 1, 'message' => '未知错误，请重试。'));
			}
		}

		$orderId = $_GPC['orderId'];
		$order = pdo_get(PDO_NAME . 'order', array('id' => $orderId));

		if (empty($order)) {
			wl_message('传入的参数有误，请重试');
		}

		$params = array('tid' => $order['orderno'], 'ordersn' => $order['orderno'], 'title' => '掌上信息发帖', 'fee' => $order['price'], 'bankrid' => '');
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => 'Pocket', 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Pocket', 'payfor' => 'Pocketredpack', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}

	public function createFabuOrder()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$fk_id = $_GPC['fk_id'];
			$day = $_GPC['day'];
			$redpack = $_GPC['redpack'] ? $_GPC['redpack'] : 0;
			$zhidingprice = 0;

			if (0 < $day) {
				$data = Setting::agentsetting_read('pocket');
				$price = $data['price'];

				foreach ($price as $key => $v) {
					if ($day == $v['day']) {
						$zhidingprice = $v['price'];
					}
				}
			}

			$day = $day + 1;
			$typeid = pdo_getcolumn(PDO_NAME . 'pocket_informations', array('id' => $fk_id), 'type');
			$fabuprice = pdo_getcolumn(PDO_NAME . 'pocket_type', array('id' => $typeid), 'price');
			$orderprice = $zhidingprice + $fabuprice + $redpack;
			$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'aid' => $_W['aid'], 'fkid' => $fk_id, 'sid' => 0, 'status' => 0, 'paytype' => 0, 'createtime' => time(), 'orderno' => createUniontid(), 'price' => $orderprice, 'num' => $day, 'plugin' => 'pocket', 'payfor' => 'pocketfabusharge');
			$res = Pocket::saveFightOrder($data);

			if ($res) {
				wl_message(array('errno' => 0, 'message' => $res));
			}
			else {
				wl_message(array('errno' => 1, 'message' => '未知错误，请重试。'));
			}
		}

		$orderId = $_GPC['orderId'];
		$order = pdo_get('wlmerchant_order', array('id' => $orderId));

		if (empty($order)) {
			wl_message('传入的参数有误，请重试');
		}

		$params = array('tid' => $order['orderno'], 'ordersn' => $order['orderno'], 'title' => '掌上信息发帖', 'fee' => $order['price'], 'bankrid' => '');
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => 'Pocket', 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Pocket', 'payfor' => 'pocketfabusharge', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}

	public function createRedOrder()
	{
		global $_W;
		global $_GPC;
		$fk_id = intval($_GPC['id']);
		$orderprice = intval($_GPC['price']);
		$package = intval($_GPC['package']);
		$tiezi = pdo_get(PDO_NAME . 'pocket_informations', array('id' => $fk_id));

		if (empty($tiezi)) {
			wl_message(error(1, '帖子不存在，请检查后重试'));
		}

		$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'aid' => $_W['aid'], 'fkid' => $fk_id, 'sid' => 0, 'status' => 0, 'paytype' => 0, 'createtime' => time(), 'orderno' => createUniontid(), 'price' => $orderprice, 'num' => 1, 'plugin' => 'pocket', 'payfor' => 'pocketredpack', 'package' => $package);
		pdo_insert(PDO_NAME . 'order', $data);
		$params = array('tid' => $data['orderno'], 'ordersn' => $data['orderno'], 'title' => '掌上信息发帖', 'fee' => $data['price'], 'bankrid' => '');
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Pocket', 'payfor' => 'Pocketredpack', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}

	public function detail()
	{
		global $_GPC;
		global $_W;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '帖子详情 - ' . $_W['wlsetting']['base']['name'] : '帖子详情';
		$set = Setting::agentsetting_read('pocket');
		$id = $_GPC['id'];
		$inform = pdo_get('wlmerchant_pocket_informations', array('id' => $id));
		$sharecontent = str_replace('
', '', $inform['content']);
		$sharecontent = str_replace('
', '', $sharecontent);
		$inform['content'] = nl2br($inform['content']);

		if (empty($inform)) {
			wl_message('该帖子不存在或已删除', 'close', 'error');
		}

		if ($inform['status'] == 1) {
			$manage = pdo_getcolumn('wlmerchant_agentadmin', array('mid' => $_W['mid'], 'aid' => $_W['aid']), 'manage');

			if ($manage) {
				$examineflag = 1;
			}
			else {
				if ($_W['mid'] != $inform['mid']) {
					wl_message('无管理权限', 'close', 'error');
				}
			}
		}

		if ($inform['locastatus']) {
			$location = unserialize($inform['location']);
		}

		$look = $inform['look'] + 1;
		pdo_update('wlmerchant_pocket_informations', array('look' => $look), array('id' => $id));
		$inform['img'] = unserialize($inform['img']);
		$inform['keyword'] = unserialize($inform['keyword']);

		if (is_array($inform['img'])) {
			foreach ($inform['img'] as $key => &$value) {
				$value = tomedia($value);
				$imgs .= $value . ',';
			}

			$imgs = substr($imgs, 0, strlen($imgs) - 1);
			$shareimg = tomedia($inform['img'][0]);
		}

		if (empty($inform['avatar'])) {
			if ($inform['mid']) {
				$avatar = pdo_getcolumn(PDO_NAME . 'member', array('id' => $inform['mid']), 'avatar');
			}
			else {
				$avatar = tomedia($set['kefu_avatar']);
			}
		}
		else {
			$avatar = $inform['avatar'];
		}

		if ($inform['type']) {
			$typename = pdo_getcolumn(PDO_NAME . 'pocket_type', array('id' => $inform['type']), 'title');
		}
		else {
			$typename = '官方公告';
		}

		if ($inform['likeids']) {
			$likeids = unserialize($inform['likeids']);
			$i = 0;

			while ($i < min(count($likeids), 20)) {
				$likemember[] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $likeids[$i]), 'avatar');
				++$i;
			}
		}

		$comment = Util::getNumData('*', PDO_NAME . 'pocket_comment', array('tid' => $id), 'createtime asc');
		$comment = $comment[0];

		if ($comment) {
			foreach ($comment as $key => &$v) {
				$v['avatar'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $v['mid']), 'avatar');
				$v['nickname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $v['mid']), 'nickname');
				$v['time'] = date('m-d', $v['createtime']);
				$reply = Util::getNumData('id,smid,amid,content', PDO_NAME . 'pocket_reply', array('cid' => $v['id']), 'createtime desc limit 5');
				$v['reply'] = $reply[0];

				if ($v['reply']) {
					foreach ($v['reply'] as $key => &$w) {
						$w['sname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $w['smid']), 'nickname');
						$w['aname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $w['amid']), 'nickname');
					}
				}
			}
		}

		if ($inform['status'] != 0) {
			$noshare = 1;
		}

		if ($inform['share_title']) {
			$_W['wlsetting']['share']['share_title'] = $inform['share_title'];
		}
		else {
			$_W['wlsetting']['share']['share_title'] = $inform['nickname'] . '发布的' . $typename . '信息';
			$pocketnotitle = 1;
		}

		$_W['wlsetting']['share']['share_desc'] = $sharecontent;
		$_W['wlsetting']['share']['share_image'] = $shareimg ? $shareimg : $avatar;

		if (p('sharegift')) {
			$userGRE = pdo_get(PDO_NAME . 'red_envelope', array('mid' => $_W['mid'], 'pid' => $id));
			if (0 < $inform['redpack'] && $inform['sredpack'] < $inform['redpack']) {
				$getList = pdo_fetchall('SELECT from_unixtime(a.gettime,\'%Y-%m-%d %H:%i:%s\') as gettime,a.money,b.nickname,b.avatar FROM ' . tablename(PDO_NAME . 'red_envelope') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'member') . (' b ON a.mid = b.id WHERE a.pid = ' . $inform['id'] . ' ORDER BY a.gettime DESC'));
			}

			$mid = $_GPC['invitid'];

			if ($mid) {
				$pocket = pdo_get(PDO_NAME . 'pocket_informations', array('id' => $id));
				$count = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename(PDO_NAME . 'red_envelope') . (' WHERE pid = ' . $id));
				$surplus = $pocket['package'] - $count;
				$invitGRE = pdo_get(PDO_NAME . 'red_envelope', array('mid' => $mid, 'pid' => $id));
				if (0 < $surplus && empty($invitGRE)) {
					$money = $this->redEnvelopeAlgorithm($pocket['sredpack'], $surplus);
					$getInfo = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'pid' => $id, 'mid' => $mid, 'gettime' => time(), 'money' => $money);
					pdo_insert(PDO_NAME . 'red_envelope', $getInfo);
					$balance = $pocket['sredpack'] - $money;
					pdo_update(PDO_NAME . 'pocket_informations', array('sredpack' => $balance), array('id' => $id));
					Member::credit_update_credit2($mid, $money, '掌上信息抢红包');
					$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $mid), 'openid');
					$first = '您好，您获得了一个分享红包。';
					$keyword1 = '红包金额' . $money . '元';
					$keyword2 = '已领取';
					$remark = '点击查看帖子详情';
					$url = app_url('pocket/pocket/detail', array('id' => $id));
					Message::jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url);
				}
			}
		}

		include wl_template('pocket/detail');
	}

	public function comment()
	{
		global $_GPC;
		global $_W;
		$black = pdo_getcolumn('wlmerchant_pocket_blacklist', array('mid' => $_W['mid'], 'aid' => $_W['aid'], 'uniacid' => $_W['uniacid']), 'id');

		if ($black) {
			exit(json_encode(array('errno' => 1, 'message' => '您已被加入黑名单，如有疑问请联系平台')));
		}

		$id = $_GPC['id'];
		$content = $_GPC['mark'];
		$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'tid' => $id, 'content' => $content, 'mid' => $_W['mid'], 'createtime' => time());
		$res = pdo_insert('wlmerchant_pocket_comment', $data);

		if ($res) {
			Pocket::commentnotice($id, $content, $_W['mid']);
			exit(json_encode(array('errno' => 0, 'message' => '')));
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '回复失败')));
		}
	}

	public function replay()
	{
		global $_GPC;
		global $_W;
		$black = pdo_getcolumn('wlmerchant_pocket_blacklist', array('mid' => $_W['mid'], 'aid' => $_W['aid'], 'uniacid' => $_W['uniacid']), 'id');

		if ($black) {
			exit(json_encode(array('errno' => 1, 'message' => '您已被加入黑名单，如有疑问请联系平台')));
		}

		$id = $_GPC['id'];
		$content = $_GPC['mark'];
		$flag = $_GPC['flag'];

		if ($flag == 2) {
			$cid = $id;
			$comment = pdo_get(PDO_NAME . 'pocket_comment', array('id' => $cid), array('tid', 'mid'));
			$tid = $comment['tid'];
			$amid = $comment['mid'];
			$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'cid' => $id, 'tid' => $tid, 'content' => $content, 'smid' => $_W['mid'], 'amid' => $amid, 'createtime' => time());
		}
		else {
			$amid = pdo_getcolumn(PDO_NAME . 'pocket_reply', array('id' => $id), 'smid');
			$cid = pdo_getcolumn(PDO_NAME . 'pocket_reply', array('id' => $id), 'cid');
			$comment = pdo_get(PDO_NAME . 'pocket_comment', array('id' => $cid), array('tid', 'mid'));
			$tid = $comment['tid'];
			$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'cid' => $cid, 'tid' => $tid, 'content' => $content, 'smid' => $_W['mid'], 'amid' => $amid, 'createtime' => time());
		}

		$res = pdo_insert('wlmerchant_pocket_reply', $data);

		if ($res) {
			Pocket::replaynotice($amid, $content, $_W['mid'], $cid);
			exit(json_encode(array('errno' => 0, 'message' => '')));
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '回复失败')));
		}
	}

	public function dianzan_ajax()
	{
		global $_GPC;
		global $_W;

		if (empty($_W['mid'])) {
			exit(json_encode(array('errno' => 3, 'message' => '未登录')));
		}

		$cid = $_GPC['cid'];
		$mid = $_W['mid'];
		$inform = pdo_get(PDO_NAME . 'pocket_informations', array('id' => $cid), array('likeids', 'likenum', 'mid'));
		$likeids = $inform['likeids'];

		if ($likeids) {
			$likeids = unserialize($likeids);

			if (in_array($mid, $likeids)) {
				exit(json_encode(array('errno' => 1, 'message' => '您已经赞过了')));
			}
			else {
				$likeids[] = $mid;
				$data['likeids'] = serialize($likeids);
				$data['likenum'] = $inform['likenum'] + 1;
				$res = pdo_update('wlmerchant_pocket_informations', $data, array('id' => $cid));
			}
		}
		else {
			$likeids[] = $mid;
			$data['likeids'] = serialize($likeids);
			$data['likenum'] = $inform['likenum'] + 1;
			$res = pdo_update('wlmerchant_pocket_informations', $data, array('id' => $cid));
		}

		if ($res) {
			$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $inform['mid']), 'openid');
			$first = '有人喜欢您的帖子';
			$keyword1 = '用户[' . $_W['wlmember']['nickname'] . ']为您点赞';
			$keyword2 = '已点赞';
			$remark = '点击查看帖子详情';
			$url = app_url('pocket/pocket/detail', array('id' => $cid));
			Message::jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url);
			exit(json_encode(array('errno' => 0, 'message' => '')));
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '点赞失败，请重试')));
		}
	}

	public function fenxiang_ajax()
	{
		global $_GPC;
		global $_W;
		$cid = $_GPC['cid'];
		$inform = pdo_get('wlmerchant_pocket_informations', array('id' => $cid), array('share'));
		$share = $inform['share'] + 1;
		$res = pdo_update('wlmerchant_pocket_informations', array('share' => $share), array('id' => $cid));

		if ($res) {
			exit(json_encode(array('errno' => 0, 'message' => '')));
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '')));
		}
	}

	public function catepage()
	{
		global $_GPC;
		global $_W;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '帖子列表 - ' . $_W['wlsetting']['base']['name'] : '帖子列表';
		$id = $_GPC['id'];
		$keyword = $_GPC['keyword'];

		if ($id) {
			$type = pdo_get(PDO_NAME . 'pocket_type', array('id' => $id));

			if ($type['type']) {
				$typename = pdo_getcolumn(PDO_NAME . 'pocket_type', array('id' => $type['type']), 'title');
				$second = pdo_getall('wlmerchant_pocket_type', array('type' => $type['type']));
				$secondid = $id;
			}
			else {
				$typename = pdo_getcolumn(PDO_NAME . 'pocket_type', array('id' => $id), 'title');
				$second = pdo_getall('wlmerchant_pocket_type', array('type' => $id));
			}
		}
		else {
			$typename = '热门推荐';
		}

		$type = pdo_getall('wlmerchant_pocket_type', array('type' => 0, 'status' => 1, 'isnav' => 0, 'aid' => $_W['aid']));

		if (empty($secondid)) {
			$secondid = 0;
		}

		$data = Setting::agentsetting_read('pocket');
		if ($data['share_title'] || $data['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];

			if ($data['share_title']) {
				$title = $data['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[系统名称]', $sysname, $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}
			else {
				$_W['wlsetting']['share']['share_title'] = !empty($data['share_title']) ? $data['share_title'] : $_W['wlsetting']['share']['share_title'];
			}

			if ($data['share_desc']) {
				$desc = $data['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[系统名称]', $sysname, $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
			else {
				$_W['wlsetting']['share']['share_desc'] = !empty($data['share_desc']) ? $data['share_desc'] : $_W['wlsetting']['share']['share_desc'];
			}
		}
		else {
			$_W['wlsetting']['share']['share_title'] = !empty($data['share_title']) ? $data['share_title'] : $_W['wlsetting']['share']['share_title'];
			$_W['wlsetting']['share']['share_desc'] = !empty($data['share_desc']) ? $data['share_desc'] : $_W['wlsetting']['share']['share_desc'];
		}

		$_W['wlsetting']['share']['share_image'] = !empty($data['share_image']) ? $data['share_image'] : $_W['wlsetting']['share']['share_image'];
		include wl_template('pocket/catepage');
	}

	public function getinform()
	{
		global $_GPC;
		global $_W;
		$set = Setting::agentsetting_read('pocket');
		$oneid = $_GPC['oneid'];
		$twoid = $_GPC['twoid'];
		$keyword = $_GPC['keyword'];
		$page = $_GPC['page'];
		$pagesize = $_GPC['pagesize'];
		$lng = !empty($_GPC['lng']) ? $_GPC['lng'] : 0;
		$lat = !empty($_GPC['lat']) ? $_GPC['lat'] : 0;

		if ($oneid) {
			$where1['#top#'] = '(0,1,2,3)';
		}
		else {
			$where1['#top#'] = '(0,1,3)';
		}

		$where2['top'] = 0;
		$where1['status'] = $where2['status'] = 0;
		$where1['uniacid'] = $where2['uniacid'] = $_W['uniacid'];
		$where1['aid'] = $where2['aid'] = $_W['aid'];

		if ($keyword) {
			$where1['@content@'] = $where2['@content@'] = $keyword;
		}

		if (empty($twoid) && $oneid) {
			$where1['onetype#'] = $where2['onetype#'] = '(' . $oneid . ',0)';
		}
		else {
			if ($twoid) {
				$where1['type#'] = $where2['type#'] = '(' . $twoid . ',0)';
			}
		}

		$plugin = Setting::agentsetting_read('pluginlist');

		switch ($plugin['tcsort']) {
		case '1':
			$listorder = 'top DESC,id DESC';
			break;

		case '2':
			$listorder = 'top DESC,look DESC';
			break;

		case '3':
			$listorder = 'top DESC,share DESC';
			break;

		case '4':
			$listorder = 'top DESC,likenum DESC';
			break;

		default:
			$listorder = 'top DESC,id DESC';
			break;
		}

		$top = Util::getNumData('*', PDO_NAME . 'pocket_informations', $where1, $listorder, $page, $pagesize, 1);

		if ($top) {
			$top = $top[0];

			if ($top) {
				if ($_GPC['pageflag'] == 1) {
					$plugin = Setting::agentsetting_read('pluginlist');
					$limit = $plugin['tclimit'] ? $plugin['tclimit'] : 10;
					$top = array_slice($top, 0, $limit);
				}

				foreach ($top as $key => &$v) {
					if (empty($v['avatar'])) {
						if ($v['mid']) {
							$v['avatar'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $v['mid']), 'avatar');
						}
						else {
							$v['avatar'] = tomedia($set['kefu_avatar']);
						}
					}
					else {
						$v['avatar'] = tomedia($v['avatar']);
					}

					$v['name'] = $v['nickname'];

					if ($v['type']) {
						$v['typename'] = pdo_getcolumn(PDO_NAME . 'pocket_type', array('id' => $v['type']), 'title');
					}
					else {
						$v['typename'] = '官方公告';
					}

					$v['time'] = date('m-d', $v['createtime']);
					$v['keyword'] = unserialize($v['keyword']);
					$v['img'] = unserialize($v['img']);
					$v['a'] = app_url('pocket/pocket/detail', array('id' => $v['id']));

					if ($v['img']) {
						foreach ($v['img'] as $key => &$w) {
							$w = tomedia($w);
						}
					}

					$conum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_pocket_comment') . (' WHERE tid = ' . $v['id']));
					$renum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_pocket_reply') . (' WHERE tid = ' . $v['id']));
					$v['commentnum'] = $conum + $renum;
					pdo_update('wlmerchant_pocket_informations', array('look' => $v['look'] + 1), array('id' => $v['id']));
					if ($v['locastatus'] && $lng && $lat) {
						$location = unserialize($v['location']);
						$v['distance'] = Store::getdistance($location['lng'], $location['lat'], $lng, $lat);

						if (1000 < $v['distance']) {
							$v['showdistance'] = floor($v['distance'] / 1000 * 10) / 10 . 'km';
						}
						else {
							$v['showdistance'] = round($v['distance']) . 'm';
						}
					}
					else {
						$v['distance'] = 0;
					}
				}
			}
		}

		exit(json_encode($top));
	}

	public function myinform()
	{
		global $_GPC;
		global $_W;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '我的帖子 - ' . $_W['wlsetting']['base']['name'] : '我的帖子';
		Member::checklogin(app_url('pocket/pocket/myinform'));
		$status = $_GPC['status'];
		$where['uniacid'] = $_W['uniacid'];
		$where['aid'] = $_W['aid'];
		$where['mid'] = $_W['mid'];

		if ($_W['ispost']) {
			if ($status != 'all') {
				$where['status'] = $status;
			}

			$lists = Util::getNumData('*', PDO_NAME . 'pocket_informations', $where);
			$lists = $lists[0];

			if ($lists) {
				foreach ($lists as $key => &$v) {
					$typename = pdo_getcolumn(PDO_NAME . 'pocket_type', array('uniacid' => $_W['uniacid'], 'id' => $v['type']), 'title');
					$typeimg = pdo_getcolumn(PDO_NAME . 'pocket_type', array('uniacid' => $_W['uniacid'], 'id' => $v['type']), 'img');
					$v['goodsname'] = $typename . '信息';
					$v['goodsimg'] = tomedia($typeimg);
					$v['url'] = app_url('pocket/pocket/detail', array('id' => $v['id']));
					$v['createtime'] = date('Y-m-d H:i', $v['createtime']);
					$v['content'] = mb_substr($v['content'], 0, 8, 'utf-8');
					$v['bianji'] = app_url('pocket/pocket/fabupage', array('inid' => $v['id']));

					if ($v['status'] == 5) {
						$orderid = pdo_getcolumn('wlmerchant_order', array('uniacid' => $_W['uniacid'], 'plugin' => 'pocket', 'fkid' => $v['id']), 'id');
						$v['zhifu'] = app_url('pocket/pocket/createFabuOrder', array('orderId' => $orderid));
					}
				}
			}

			exit(json_encode($lists));
		}

		$data = Setting::agentsetting_read('pocket');
		$_W['wlsetting']['share']['share_title'] = !empty($data['share_title']) ? $data['share_title'] : $_W['wlsetting']['share']['share_title'];
		$_W['wlsetting']['share']['share_desc'] = !empty($data['share_desc']) ? $data['share_desc'] : $_W['wlsetting']['share']['share_desc'];
		$_W['wlsetting']['share']['share_image'] = !empty($data['share_image']) ? $data['share_image'] : $_W['wlsetting']['share']['share_image'];
		include wl_template('pocket/myinform');
	}

	public function delete()
	{
		global $_GPC;
		global $_W;
		$id = $_GPC['id'];
		$res = pdo_delete('wlmerchant_pocket_informations', array('id' => $id));

		if ($res) {
			exit(json_encode(1));
		}
		else {
			exit(json_encode(0));
		}
	}

	public function pass()
	{
		global $_GPC;
		global $_W;
		$id = $_GPC['id'];
		$flag = $_GPC['flag'];

		if ($flag) {
			$res = pdo_update('wlmerchant_pocket_informations', array('status' => 0), array('id' => $id));
		}
		else {
			$reason = $_GPC['reason'];

			if (empty($reason)) {
				$reason = '时间：' . date('Y-m-d H:i:s', time()) . '驳回管理员MID：' . $_W['mid'];
				pdo_update('wlmerchant_pocket_informations', array('reason' => $reason), array('id' => $id));
			}
			else {
				$res = pdo_update('wlmerchant_pocket_informations', array('status' => 2, 'reason' => $reason), array('id' => $id));
			}
		}

		if ($res) {
			Pocket::passnotice($id);
			exit(json_encode(1));
		}
		else {
			exit(json_encode(0));
		}
	}

	private function share_info()
	{
		global $_GPC;
		global $_W;
		$data = Setting::agentsetting_read('pocket');
		if ($data['share_title'] || $data['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];

			if ($data['share_title']) {
				$title = $data['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[系统名称]', $sysname, $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}
			else {
				$_W['wlsetting']['share']['share_title'] = !empty($data['share_title']) ? $data['share_title'] : $_W['wlsetting']['share']['share_title'];
			}

			if ($data['share_desc']) {
				$desc = $data['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[系统名称]', $sysname, $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
			else {
				$_W['wlsetting']['share']['share_desc'] = !empty($data['share_desc']) ? $data['share_desc'] : $_W['wlsetting']['share']['share_desc'];
			}
		}
		else {
			$_W['wlsetting']['share']['share_title'] = !empty($data['share_title']) ? $data['share_title'] : $_W['wlsetting']['share']['share_title'];
			$_W['wlsetting']['share']['share_desc'] = !empty($data['share_desc']) ? $data['share_desc'] : $_W['wlsetting']['share']['share_desc'];
		}

		$_W['wlsetting']['share']['share_image'] = !empty($data['share_image']) ? $data['share_image'] : $_W['wlsetting']['share']['share_image'];
	}

	/**
     * Comment: 用户领取红包的操作
     * Author: zzw
     */
	public function getRedEnvelope()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['pid'];
		$mid = $_W['mid'];

		if (!$mid) {
			wl_json(1, '请先登录!');
		}

		$pocket = pdo_get(PDO_NAME . 'pocket_informations', array('id' => $id));
		$count = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename(PDO_NAME . 'red_envelope') . (' WHERE pid = ' . $id));
		$surplus = $pocket['package'] - $count;

		if ($surplus <= 0) {
			wl_json(1, '来晚一步,红包已被抢完！');
		}

		$userGRE = pdo_get(PDO_NAME . 'red_envelope', array('mid' => $_W['mid'], 'pid' => $id));

		if ($userGRE) {
			wl_json(1, '已领取过该红包！');
		}

		$money = $this->redEnvelopeAlgorithm($pocket['sredpack'], $surplus);
		$getInfo = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'pid' => $id, 'mid' => $mid, 'gettime' => time(), 'money' => $money);
		pdo_insert(PDO_NAME . 'red_envelope', $getInfo);
		$balance = $pocket['sredpack'] - $money;
		pdo_update(PDO_NAME . 'pocket_informations', array('sredpack' => $balance), array('id' => $id));
		Member::credit_update_credit2($mid, $money, '掌上信息抢红包');
		wl_json(0, '领取成功', $money);
	}

	/**
     * Comment: 红包算法
     * Author: zzw
     * @param $balance   红包余额
     * @param $surplus   剩余的包
     * @return mixed     返回一个红包中拥有的金额
     */
	protected function redEnvelopeAlgorithm($balance, $surplus)
	{
		if (1 < $surplus) {
			$total = 0;
			$frequency = 5;
			$maxMoney = 0;
			$meanValue = $balance / $surplus * 2;
			$i = 0;

			while ($i < $frequency) {
				$rand = mt_rand() / mt_getrandmax();
				$value = sprintf('%0.2f', $rand * $meanValue);
				$total += $value;

				if ($maxMoney < $value) {
					$maxMoney = $value;
				}

				++$i;
			}

			$total = sprintf('%0.2f', $total - $maxMoney);
			$money = sprintf('%0.2f', $total / $frequency);
			$currentBalance = $balance - $money;
			$minBalance = 0.01 * ($surplus - 1);
			if ($money <= 0 || $balance <= $money || $currentBalance <= $minBalance) {
				$this->redEnvelopeAlgorithm($balance, $surplus);
			}
		}
		else {
			$money = $balance;
		}

		return $money;
	}
}

?>
