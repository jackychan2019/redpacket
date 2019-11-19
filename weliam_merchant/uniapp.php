<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Weliam_merchantModuleUniapp extends Uniapp
{
	/**
     * Comment: 进行安全验证
     * Author: zzw
     * @param $method  将要访问的方法的名称
     */
	public function securityVerification($method)
	{
		global $_W;
		global $_GPC;
		$privateMethod = array('Card', 'GetCoiling', 'WriteOffRecord', 'ReceiveCoupon', 'BindingPhone', 'GetOrder', 'GroupWriteOff', 'CouponWriteOff', 'Comment', 'GetOrderSum', 'GetPayment', 'WeChatPay', 'SuccessCallback', 'Refund', 'HeadlineComment', 'Fabulous', 'BuyGoods', 'Exchange', 'OpenUpVip', 'VipGetPayment');

		if (in_array($method, $privateMethod)) {
			$res = WeChat::loginJudge($_W['mid'], $_W['token']);

			if ($res['erron'] == 1) {
				$this->reLogin($res['message']);
			}
		}

		$method = 'doPage' . ucfirst($method);
		$this->{$method}();
	}

	/**
     * Comment: 获取当前平台的信息(公共)
     * Author: zzw
     * Date: 2019/7/9 11:09
     */
	public function doPageGetPlatformInfor()
	{
		global $_W;
		global $_GPC;
		$type = $_GPC['type'] ? $_GPC['type'] : 1;

		switch ($type) {
		case 1:
			$set = Setting::wlsetting_read('base');
			$info['name'] = $set['name'];
			$info['logo'] = tomedia($set['logo']);
			$info['phone'] = $set['phone'];
			$title = '本平台基本信息';
			break;

		case 2:
			$set = Setting::agentsetting_read('meroof');

			if (1 < strlen($set['shout'])) {
				$info = explode(',', $set['shout']);
			}

			$title = '热门搜索信息';
			break;

		default:
			$set = Setting::wlsetting_read('base');
			$info['name'] = $set['name'];
			$info['logo'] = tomedia($set['logo']);
			$info['phone'] = $set['phone'];
			$title = '本平台基本信息';
			break;
		}

		$this->renderSuccess($title, $info);
	}

	/**
     * Comment: 文件上传
     * Author: zzw
     * Date: 2019/7/23 9:32
     */
	public function doPageUploadFiles()
	{
		global $_W;
		global $_GPC;
		$uploadType = $_GPC['upload_type'] ? $_GPC['upload_type'] : 1;
		UploadFile::uploadIndex($_FILES, $uploadType, $_GPC['id']);
	}

	/**
     * Comment: 搜索内容
     * Author: zzw
     */
	public function doPageSearch()
	{
		global $_W;
		global $_GPC;
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$search = $_GPC['search'];
		$lng = $_GPC['lng'] ? $_GPC['lng'] : 0;
		$lat = $_GPC['lat'] ? $_GPC['lat'] : 0;

		if (empty($search)) {
			$this->result(1, '请输入搜索内容');
		}

		$data = WeChat::getSearch($page, $search, $lng, $lat);
		$this->result(0, '搜索内容', $data);
	}

	/**
     * Comment: 获取图片验证码
     * Author: zzw
     * Date: 2019/8/8 9:09
     */
	public function doPageGVC()
	{
		global $_W;
		global $_GPC;
		load()->classs('captcha');
		error_reporting(0);
		session_start();
		$captcha = new Captcha();
		$captcha->build(108, 44);
		$hash = md5(strtolower($captcha->phrase) . $_W['config']['setting']['authkey']);
		isetcookie('__code', $hash);
		$_SESSION['__code'] = $hash;
		ob_start();
		imagepng($captcha->image);
		$image_data = base64_encode(ob_get_contents());
		ob_end_clean();
		$image_data = 'data:image/png;base64,' . $image_data;
		$this->renderSuccess('图形验证码信息', $image_data);
	}

	/**
     * Comment: 发送短信验证码
     * Author: zzw
     * Date: 2019/8/8 9:37
     */
	public function doPagePIN()
	{
		global $_W;
		global $_GPC;
		$phone = $_GPC['phone'];
		$mid = $_W['mid'];

		if (!$phone) {
			$this->renderError('请输入手机号码');
		}

		$where['mobile'] = $phone;
		$where['uniacid'] = $_W['uniacid'];

		if (-1 < $mid) {
			$where['id !='] = $mid;
		}

		$have = pdo_get(PDO_NAME . 'member', $where);

		if ($have) {
			$this->renderError('该手机已被绑定');
		}

		$code = rand(1000, 9999);
		isetcookie('__pin_info', $code);
		$res = WeChat::smsSF($code, $phone);

		if ($res['result'] == 1) {
			$this->renderSuccess('发送成功', array('code' => $code));
		}
		else {
			$this->renderError('验证码发送失败' . $res['msg']);
		}
	}

	/**
     * Comment: 切换城市列表
     * Author: Hexin
     */
	public function doPageCityList()
	{
		global $_W;
		global $_GPC;
		$keyword = trim($_GPC['keyword']);
		$set = Setting::wlsetting_read('areaset');
		$location = $set['location'] ? $set['location'] : 0;

		if ($location == 0) {
			$citylists = Cache::getCache('urbanLocationData', 'citylist');
			if (!$citylists || !empty($keyword)) {
				$where = ' WHERE a.uniacid = ' . $_W['uniacid'] . ' AND a.status = 1 ';

				if (!empty($keyword)) {
					$where .= ' AND b.name LIKE \'%' . $keyword . '%\' ';
				}

				$list = pdo_fetchall('SELECT b.* FROM ' . tablename(PDO_NAME . 'oparea') . ' as a RIGHT JOIN ' . tablename(PDO_NAME . 'area') . ' as b ON a.areaid = b.id ' . $where);
				$hotcityids = (new AgentareaTable())->selectFields('areaid')->searchWithUniacid($_W['uniacid'])->searchWithHot()->searchWithOpen()->searchWithLevel(2)->getAreaList();
				$hotcityids = array_column($hotcityids, 'areaid');

				if (0 < count($list)) {
					foreach ($list as $city) {
						$newcitys[$city['initial']][] = $city;
						if (!empty($hotcityids) && in_array($city['id'], $hotcityids)) {
							$hotcitys[] = $city;
						}
					}

					ksort($newcitys);
				}

				$citylists = array('hotcity' => $hotcitys, 'citylist' => $newcitys);

				if (!$keyword) {
					Cache::setCache('urbanLocationData', 'citylist', $citylists);
				}
			}
		}
		else {
			$areatable = new AreaTable();

			if (!empty($keyword)) {
				$citylists['citylist'] = $areatable->searchWithLevel(2)->searchWithKeyword($keyword)->searchWithOpen()->searchWithUniacid($_W['uniacid'])->selectFields(array('initial', 'id', 'name'))->getAreaList();
			}
			else {
				$citylists = Cache::getCache('area', 'citylist');

				if (empty($citylists)) {
					$citys = $areatable->searchWithLevel(2)->searchWithOpen()->searchWithUniacid($_W['uniacid'])->selectFields(array('initial', 'id', 'name'))->getAreaList();
					$hotcityids = (new AgentareaTable())->selectFields('areaid')->searchWithUniacid($_W['uniacid'])->searchWithHot()->searchWithOpen()->searchWithLevel(2)->getAreaList();
					$hotcityids = array_column($hotcityids, 'areaid');
					$hotcitys = $newcitys = array();

					foreach ($citys as $city) {
						$newcitys[$city['initial']][] = $city;
						if (!empty($hotcityids) && in_array($city['id'], $hotcityids)) {
							$hotcitys[] = $city;
						}
					}

					ksort($newcitys);
					$citylists = array('hotcity' => $hotcitys, 'citylist' => $newcitys);
					Cache::setCache('area', 'citylist', $citylists);
				}
			}
		}

		$citylists['location'] = $location;
		$this->renderSuccess('获取地址信息', $citylists);
	}

	/**
     * Comment: 根据城市ID或经纬度获取当前位置信息
     * Author: Hexin
     */
	public function doPageCityLocation()
	{
		global $_GPC;

		if (!empty($_GPC['citycode'])) {
			$areatable = new AreaTable();
			$areatable->selectFields(array('lat', 'lng'));
			$cityinfo = $areatable->getAreaById(intval($_GPC['citycode']));
		}

		$lat = $cityinfo['lat'] ? $cityinfo['lat'] : trim($_GPC['lat']);
		$lng = $cityinfo['lng'] ? $cityinfo['lng'] : trim($_GPC['lng']);
		if (empty($lat) || empty($lng)) {
			$this->result(1, '请传入有效的参数');
		}

		$location = MapService::guide_gcoder($lat . ',' . $lng, 1);

		if (is_error($location)) {
			$this->result($location['errno'], $location['message']);
		}

		$this->result(0, 'success', $location['result']);
	}

	/**
     * Comment: 根据城市搜索地点
     * Author: Hexin
     */
	public function doPageCitySearch()
	{
		global $_GPC;
		$keyword = empty($_GPC['keyword']) ? $this->result(1, '请填写搜索内容') : trim($_GPC['keyword']);
		$city_name = empty($_GPC['city_name']) ? $this->result(1, '请指定地区名称') : trim($_GPC['city_name']);
		$location = MapService::guide_search($keyword, 'region(' . urlencode($city_name) . ',0)');

		if (is_error($location)) {
			$this->result($location['errno'], $location['message']);
		}

		$this->result(0, 'success', $location['data']);
	}

	/**
     * Comment: 获取微信jssdk
     * Author: Hexin
     */
	public function doPageGetJssdk()
	{
		global $_W;
		global $_GPC;
		$url = !empty($_GPC['sign_url']) ? urldecode($_GPC['sign_url']) : $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=' . MODULE_NAME . '&p=area&ac=region&do=index';
		$account_api = WeAccount::create();

		if (!empty($account_api)) {
			$jssdkconfig = $account_api->getJssdkConfig($url);
		}

		$this->result(0, 'success', $jssdkconfig);
	}

	/**
     * Comment: 获取平台菜单信息
     * Author: zzw
     * Date: 2019/7/25 16:17
     */
	public function doPageBottomMenu()
	{
		global $_W;
		global $_GPC;
		$set = Setting::agentsetting_read('diypageset');

		if (0 < $set['menu_index']) {
			$menudata = Diy::getMenu($set['menu_index']);
		}
		else {
			$menudata = DiyMenu::defaultBottomMenu();
		}

		$this->renderSuccess('平台菜单信息', $menudata);
	}

	/**
     * Comment: 获取自定义装修页面配置信息
     * Author: zzw
     */
	public function doPageHomePage()
	{
		global $_W;
		global $_GPC;
		$type = $_GPC['type'] ? $_GPC['type'] : 2;
		$page_id = $_GPC['page_id'] ? $_GPC['page_id'] : 0;
		$settings = Setting::agentsetting_read('diypageset');
		$titleList = array(1 => '自定义页面', 2 => '平台首页', 3 => '抢购首页', 4 => '团购首页', 5 => '卡卷首页', 6 => '拼团首页', 7 => '砍价首页');

		switch ($type) {
		case 1:
			break;

		case 2:
			$id = $settings['page_index'];

			if (0 < $id) {
				$pageset = Diy::getPage($id, true);
			}
			else {
				$pageset = DiyPage::getHomePageDefaultInfo();
			}

			$advId = $settings['adv_index'];
			$menuId = $settings['menu_index'];
			$pageInfo = $pageset['data']['page'];
			break;

		case 3:
			break;

		case 4:
			break;

		case 5:
			break;

		case 6:
			break;

		case 7:
			break;
		}

		$page['title'] = $pageInfo['title'];
		$page['background'] = $pageInfo['background'];
		$page['share_title'] = $pageInfo['share_title'];
		$page['share_image'] = tomedia($pageInfo['share_image']);

		if (0 < $menuId) {
			$menudata = Diy::getMenu($menuId)['data'];
		}

		if (0 < $advId) {
			$advdata = Diy::BeOverdue($advId, false)['data'];
		}

		$data['page'] = $page ? $page : array();
		$data['menu'] = $menudata ? $menudata : array();
		$data['adv'] = $advdata ? $advdata : array();
		$data['item'] = $pageset['data']['items'] ? $pageset['data']['items'] : array();
		$this->renderSuccess($titleList[$type] . '配置信息', $data);
	}

	/**
     * Comment: 获取自定义页面的信息
     * Author: zzw
     */
	public function doPageCustomPage()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$pageset = Diy::getPage($id, true);
		$pageInfo = $pageset['data']['page'];
		$data = WeChat::getPageInfo($pageInfo, $pageInfo['diymenu'], $pageInfo['diyadv']);
		$data['item'] = $pageset['data']['items'];
		$this->result(0, '自定义页面的信息', $data);
	}

	/**
     * Comment: 用户在小程序中进行登录操作
     * Author: zzw
     */
	public function doPageLogin()
	{
		global $_W;
		global $_GPC;
		$code = $_GPC['code'];
		$mobile = $_GPC['mobile'] ? $_GPC['mobile'] : '99999999999';

		if (!$code) {
			$this->result(1, '缺少参数');
		}

		$info = WeChat::getOpenid($code);

		if (!$info['openid']) {
			$error = WeChat::errorCode($info['errcode']);
			$this->result(1, $error);
		}

		$userInfo = pdo_get(PDO_NAME . 'member', array('mobile' => $mobile, 'wechat_openid' => $info['openid'], 'uniacid' => $_W['uniacid']));

		if (!$userInfo) {
			$existence = pdo_fetch('SELECT * FROM ' . tablename(PDO_NAME . 'member') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND ( mobile = \'' . $mobile . '\' OR wechat_openid = \'' . $info['openid'] . '\' )'));

			if ($existence) {
				$userInfo['id'] = $existence['id'];
				$update['wechat_openid'] = $info['openid'];
			}
			else {
				if ($mobile == '99999999999') {
					$mobile = '';
				}

				$insert['uniacid'] = $_W['uniacid'];
				$insert['aid'] = $_W['aid'] ? $_W['aid'] : 0;
				$insert['wechat_openid'] = $info['openid'];
				$insert['nickname'] = $_GPC['nickname'];
				$insert['gender'] = $_GPC['gender'];
				$insert['avatar'] = $_GPC['avatar'];
				$insert['mobile'] = $mobile;
				$insert['createtime'] = time();
				pdo_insert(PDO_NAME . 'member', $insert);
				$userInfo['id'] = pdo_insertid();
			}
		}

		$update['dotime'] = time();
		pdo_update(PDO_NAME . 'member', $update, array('id' => $userInfo['id']));
		$userInfo = pdo_get(PDO_NAME . 'member', array('wechat_openid' => $info['openid'], 'uniacid' => $_W['uniacid']));
		$phone = $userInfo['mobile'] ? 1 : 0;
		$areaname = implode(pdo_fetch('SELECT b.name FROM ' . tablename(PDO_NAME . 'oparea') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'area') . (' b ON a.areaid = b.id WHERE a.aid = ' . $userInfo['aid'] . ' ')));
		$token = WeChat::SecurityVerification($userInfo['id']);
		$this->result(0, '用户信息数据', array('id' => $userInfo['id'], 'session_key' => $info['session_key'], 'phone' => $phone, 'aid' => $userInfo['aid'], 'areaname' => $areaname, 'token' => $token));
	}

	/**
     * Comment: 卡片制造
     * Author: zzw
     */
	public function doPageCard()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$mid = $_W['mid'];
		$type = $_GPC['type'];
		$set = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'diyposter'), 'value'));
		$tplarr = array(1 => $set['rushpid'], 2 => $set['grouponpid'], 4 => $set['storepid'], 5 => $set['cardpid']);
		$bgtpl = pdo_get(PDO_NAME . 'poster', array('uniacid' => $_W['uniacid'], 'id' => $tplarr[$type]), array('bg', 'otherbg', 'data'));
		$member = pdo_get(PDO_NAME . 'member', array('id' => $mid), array('nickname', 'avatar'));

		switch ($type) {
		case 1:
			$poster = Poster::createRushPoster($id, 'wechat', '', $mid);
			break;

		case 2:
			$poster = Poster::createGrouponPoster($id, 'wechat', '', $mid);
			break;

		case 4:
			$poster = Poster::createStorePoster($id, 'wechat', '', $mid);
			break;

		case 5:
			$poster = Poster::createCouponPoster($id, 'wechat', '', $mid);
			break;
		}

		if (is_array($poster)) {
			$this->result(1, $poster['erron']);
		}
		else {
			$this->result(0, '分享卡片链接路径', $poster);
		}
	}

	/**
     * Comment: 获取用户的卡卷信息
     * Author: zzw
     */
	public function doPageGetCoiling()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$state = $_GPC['state'] ? $_GPC['state'] : 1;
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$pageNum = $_GPC['pageNum'] ? $_GPC['pageNum'] : 10;
		$start = $page * $pageNum - $pageNum;

		if ($state == 3) {
			$where = ' AND a.endtime < ' . time();
		}
		else {
			$where = ' AND a.status = ' . $state;
		}

		$list = pdo_fetchall('SELECT 
            o.id,
            a.title,
            FROM_UNIXTIME(a.endtime,\'%Y-%m-%d\') as endtime,
            CASE a.type
            WHEN 1 THEN \'折扣券\' 
            WHEN 2 THEN \'代金券\' 
            WHEN 3 THEN \'礼品券\' 
            WHEN 4 THEN \'团购券\' 
            WHEN 5 THEN \'优惠券\' 
            END \'types\',
            a.sub_title,
            a.concode,
            b.logo,
            s.storename FROM ' . tablename(PDO_NAME . 'member_coupons') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'couponlist') . ' b ON a.parentid = b.id LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . ' s ON b.merchantid = s.id LEFT JOIN ' . tablename(PDO_NAME . 'order') . ' o ON  a.id = o.recordid ' . ('  WHERE a.mid = ' . $mid . ' ' . $where . ' ORDER BY a.id DESC LIMIT ' . $start . ',' . $pageNum));

		foreach ($list as $k => &$v) {
			$v['logo'] = tomedia($v['logo']);
			$v['day_num'] = intval((strtotime($v['endtime']) - time()) / (3600 * 24));
		}

		$this->result(0, '获取用户卡卷信息', $list);
	}

	/**
     * Comment: 用户核销记录
     * Author: zzw
     */
	public function doPageWriteOffRecord()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$pageNum = $_GPC['pageNum'] ? $_GPC['pageNum'] : 10;
		$start = $page * $pageNum - $pageNum;
		$list = pdo_fetchall('SELECT 
            a.id,
            b.title,
            p.title as name,
            a.ordermoney, 
            FROM_UNIXTIME(a.usetime,\'%m-%d %H:%i\') as usetime,
            a.commentflag,
            m.storename,
            m.logo FROM ' . tablename(PDO_NAME . 'timecardrecord') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'halfcardlist') . ' b ON a.activeid = b.id AND a.type = 1 LEFT JOIN ' . tablename(PDO_NAME . 'package') . ' p ON a.activeid = p.id AND a.type = 2 LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' m ON a.merchantid = m.id WHERE a.mid = ' . $mid . ' ORDER BY a.id DESC LIMIT ' . $start . ',' . $pageNum . ' '));

		foreach ($list as $k => &$v) {
			$v['logo'] = tomedia($v['logo']);
		}

		$this->result(0, '用户核销记录列表', $list);
	}

	/**
     * Comment: 用户打折卡，大礼包核销详细信息
     * Author: zzw
     */
	public function doPageWriteOffDetail()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$info = pdo_get(PDO_NAME . 'timecardrecord', array('id' => $id), array('id', 'ordermoney', 'verfmid', 'usetime', 'commentflag', 'mid', 'merchantid', 'activeid', 'type'));
		$info['usetime'] = date('Y-m-d H:i:s', $info['usetime']);
		$info['commentflag'] = intval($info['commentflag']);
		$shop = pdo_get(PDO_NAME . 'merchantdata', array('id' => $info['merchantid']), array('id', 'storename', 'logo'));
		$info['shop_id'] = $shop['id'];
		$info['shop_name'] = $shop['storename'];
		$info['shop_logo'] = tomedia($shop['logo']);
		unset($info['merchantid']);

		if ($info['type'] == 1) {
			$info['goods_name'] = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('id' => $info['activeid']), 'title');
		}
		else {
			$info['goods_name'] = pdo_getcolumn(PDO_NAME . 'package', array('id' => $info['activeid']), 'title');
		}

		unset($info['activeid']);
		unset($info['type']);
		$info['cardholder'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $info['mid']), 'nickname');
		unset($info['mid']);
		$info['clerk'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $info['verfmid']), 'nickname');
		unset($info['verfmid']);
		$this->result(0, '核销详细信息', $info);
	}

	/**
     * Comment: 支付有礼页面信息
     * Author: zzw
     */
	public function doPagePayCourtesy()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$table = $_GPC['table'];
		$tables = 'order';
		$field = 'paytype,paidprid,price,plugin,fkid as goods_id,status';

		if ($table == 'b') {
			$tables = 'rush_order';
			$field = 'paytype,paidprid,actualprice as price,REPLACE(\'table\',\'table\',\'rush\') as `plugin`,activityid as goods_id,status';
		}

		$orderInfo = pdo_fetch('SELECT ' . $field . ' FROM ' . tablename(PDO_NAME . $tables) . (' WHERE id = ' . $id));
		$goods = WeChat::getGoods($orderInfo['goods_id'], $orderInfo['plugin']);
		$orderInfo['goods_class'] = $goods['goods_class'];

		switch ($orderInfo['status']) {
		case '0':
			$orderInfo['state'] = '待付款';
			break;

		case '1':
			if ($orderInfo['plugin'] == 'wlfightgroup') {
				$orderInfo['state'] = '待收货';
			}
			else {
				$orderInfo['state'] = '待使用';
			}

			break;

		case '2':
			if ($orderInfo['plugin'] == 'wlfightgroup') {
				$orderInfo['state'] = '已消费';
			}
			else {
				$orderInfo['state'] = '待评价';
			}

			break;

		case '3':
			$orderInfo['state'] = '已完成';
			break;

		case '4':
			if ($orderInfo['expressid']) {
				$orderInfo['statusName'] = '待收货';
			}
			else {
				$orderInfo['statusName'] = '待消费';
			}

			break;

		case '5':
			$orderInfo['state'] = '已取消';
			break;

		case '6':
			$orderInfo['state'] = '待退款';
			break;

		case '7':
			$orderInfo['state'] = '已退款';
			break;

		case '8':
			$orderInfo['state'] = '待发货';
			break;

		case '9':
			$orderInfo['state'] = '已过期';
			break;
		}

		$paidrecord = pdo_get(PDO_NAME . 'paidrecord', array('id' => $orderInfo['paidprid']), array('activeid', 'integral', 'couponid', 'getcouflag'));
		$orderInfo['img'] = tomedia(pdo_getcolumn(PDO_NAME . 'payactive', array('id' => $paidrecord['activeid']), 'img'));
		$orderInfo['integral'] = $paidrecord['integral'];
		$orderInfo['paidprid'] = $paidrecord['paidprid'];
		$orderInfo['getcouflag'] = intval($paidrecord['getcouflag']);

		if ($paidrecord['couponid']) {
			$orderInfo['coupon_name'] = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $paidrecord['couponid']), 'title');
		}

		unset($orderInfo['paidprid']);
		unset($orderInfo['plugin']);
		unset($orderInfo['goods_id']);
		unset($orderInfo['status']);
		$this->result(0, '支付有礼页面信息', $orderInfo);
	}

	/**
     * Comment: 领取支付有礼赠送的卡卷
     * Author: zzw
     */
	public function doPageReceiveCoupon()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$mid = $_W['mid'];
		$paid = pdo_get('wlmerchant_paidrecord', array('id' => $id));

		if ($paid['getcouflag']) {
			$this->result(1, '你已经领取该卡卷,请勿重复领取!');
		}

		$couponid = $paid['couponid'];
		$coupons = wlCoupon::getSingleCoupons($couponid, '*');
		$num = wlCoupon::getCouponNum($couponid, 1);
		if ($coupons['time_type'] == 1 && $coupons['endtime'] < time()) {
			$this->result(1, '抱歉，优惠券已停止发放!');
		}

		if ($coupons['status'] == 0) {
			$this->result(1, '抱歉，优惠券已被禁用!');
		}

		if ($coupons['status'] == 3) {
			$this->result(1, '抱歉，优惠券已经失效!');
		}

		if ($coupons['quantity'] - 1 < $coupons['surplus']) {
			$this->result(1, '抱歉，卡券已经被领光了!');
		}

		if ($num) {
			if (($coupons['get_limit'] < $num || $num == $coupons['get_limit']) && 0 < $coupons['get_limit']) {
				$this->result(1, '抱歉，一个用户只能领取' . $coupons['get_limit'] . '张!');
			}
		}

		$newsurplus = $coupons['surplus'] + 1;
		wlCoupon::updateCoupons(array('surplus' => $newsurplus), array('id' => $couponid));

		if ($coupons['time_type'] == 1) {
			$starttime = $coupons['starttime'];
			$endtime = $coupons['endtime'];
		}
		else {
			$starttime = time();
			$endtime = time() + $coupons['deadline'] * 24 * 3600;
		}

		$data = array('mid' => $mid, 'aid' => $_W['aid'], 'parentid' => $coupons['id'], 'status' => 1, 'type' => $coupons['type'], 'title' => $coupons['title'], 'sub_title' => $coupons['sub_title'], 'content' => $coupons['goodsdetail'], 'description' => $coupons['description'], 'color' => $coupons['color'], 'starttime' => $starttime, 'endtime' => $endtime, 'createtime' => time(), 'usetimes' => $coupons['usetimes'], 'concode' => Util::createConcode(4));
		$res = wlCoupon::saveMemberCoupons($data);

		if ($res) {
			pdo_update(PDO_NAME . 'paidrecord', array('getcouflag' => 1, 'getcoutime' => time()), array('id' => $id));
			$this->result(0, '领取成功');
		}
		else {
			$this->result(0, '抱歉！领取失败,请重试');
		}
	}

	/**
     * Comment: 用户绑定手机并且合并数据
     * Author: zzw
     */
	public function doPageBindingPhone()
	{
		global $_W;
		global $_GPC;
		$phone = $_GPC['phone'];
		$mid = $_W['mid'];
		$type = $_GPC['type'];
		if (!$phone || !$mid) {
			$this->result(1, '参数不完整', array('phone' => $phone, 'mid' => $mid));
		}

		if ($type == 2) {
			$set = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'city_selection_set'), 'value'));
			$appID = $set['appid'];
			$sessionKey = $_GPC['session_key'];
			$iv = $_GPC['iv'];

			if (strlen($sessionKey) != 24) {
				$this->result(1, 'session_key非法');
			}

			$aesKey = base64_decode($sessionKey);

			if (strlen($iv) != 24) {
				$this->result(1, '加密算法的初始向量不合格');
			}

			$aesIV = base64_decode($iv);
			$aesCipher = base64_decode($phone);
			$result = openssl_decrypt($aesCipher, 'AES-128-CBC', $aesKey, 1, $aesIV);
			$dataObj = json_decode($result);

			if ($dataObj == NULL) {
				$this->result(1, 'aes 解密失败');
			}

			$dataArr = get_object_vars($dataObj);
			$phone = $dataArr['phoneNumber'];
		}

		pdo_update(PDO_NAME . 'member', array('mobile' => $phone), array('id' => $mid));
		$result = 1;

		if ($result) {
			if ($phone) {
				Member::AccountMerging($phone, true);
			}

			$this->result(0, '绑定成功', 0);
		}
		else {
			$this->result(1, '绑定失败，请重试');
		}
	}

	/**
     * Comment: 用户选择地区区域
     * Author: zzw
     */
	public function doPageSelectArea()
	{
		global $_W;
		global $_GPC;
		$area = pdo_getall(PDO_NAME . 'oparea', array('uniacid' => $_W['uniacid'], 'status' => 1));

		foreach ($area as $k => &$v) {
			$aid = $v['aid'];
			$gid = $v['gid'];
			$name = pdo_getcolumn(PDO_NAME . 'area', array('id' => $v['areaid']), 'name');

			if ($v['ishot'] == 1) {
				$hot[$k] = array('aid' => $aid, 'name' => $name);
			}

			array_splice($v, 0, count($v));
			$v['aid'] = $aid;
			$v['gid'] = $gid;
			$v['name'] = $name;
		}

		$data['hot'] = $hot;
		$data['area'] = $area;
		$areagroup = pdo_getall(PDO_NAME . 'areagroup', array('uniacid' => $_W['uniacid']), array('id', 'name'), '', 'sort DESC');

		if ($areagroup) {
			foreach ($areagroup as $group_k => &$group_v) {
				foreach ($area as $area_k => $area_v) {
					if ($area_v['gid'] == $group_v['id']) {
						$group_v['list'][count($group_v['list'])] = $area_v;
					}
				}
			}

			unset($data['area']);
			$data['group'] = $areagroup;
		}

		$this->result(0, '当前可选的地区信息', $data);
	}

	/**
     * Comment: 获取公告详情信息
     * Author: zzw
     */
	public function doPageGetNotice()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$notice = pdo_get(PDO_NAME . 'notice', array('id' => $id), array('id', 'title', 'content'));
		$this->result(0, '公告详情', $notice);
	}

	/**
     * Comment: 通过经纬度进行城市定位
     * Author: zzw
     */
	public function doPagePosition()
	{
		global $_W;
		global $_GPC;
		$lat = $_GPC['lat'];
		$lng = $_GPC['lng'];
		$api = Setting::wlsetting_read('api')['txmapkey'];
		$res = ihttp_post('https://apis.map.qq.com/ws/geocoder/v1/', array('location' => $lat . ',' . $lng, 'key' => $api ? $api : 'PBNBZ-GPKWJ-6KDFT-KKMCC-SI7EH-DRFHX', 'get_poi' => 1));
		$info = json_decode($res['content'], true)['result']['ad_info'];
		list(, $areaid) = explode($info['nation_code'], $info['city_code']);
		$data['aid'] = pdo_getcolumn(PDO_NAME . 'oparea', array('areaid' => $areaid), 'aid');
		$data['city'] = $info['city'];
		$this->result(0, '获取当前城市的代理信息', $data);
	}

	/**
     * Comment: 获取用户订单
     * Author: zzw
     */
	public function doPageGetOrder()
	{
		global $_W;
		global $_GPC;
		$status = $_GPC['status'];
		$mid = $_W['mid'];
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$pageNum = $_GPC['pageNum'] ? $_GPC['pageNum'] : 10;
		$start = ($page - 1) * $pageNum;
		$where = ' uniacid = ' . $_W['uniacid'] . ' and mid = ' . $mid . ' AND aid = ' . $_W['aid'] . ' ';
		$orderType = '\'coupon\',\'wlfightgroup\',\'groupon\',\'activity\'';

		if (is_numeric($status)) {
			$where .= ' AND status = {intval(' . $status . ')}';
		}

		$order = pdo_fetchall('SELECT id,createtime,sid,status,price,paidprid,orderno,num,expressid,recordid,fkid as goods_id,plugin,REPLACE("table","table","a") as `table` FROM ' . tablename(PDO_NAME . 'order') . (' WHERE ' . $where . ' AND orderno != 666666 AND plugin IN (' . $orderType . ')') . ' UNION ALL SELECT id,createtime,sid,status,price,paidprid,orderno,num,"expressid","recordid",activityid as goods_id,"rush","b" FROM ' . tablename(PDO_NAME . 'rush_order') . (' WHERE ' . $where . ' ORDER BY createtime DESC limit ' . $start . ',' . $pageNum));

		foreach ($order as $k => &$v) {
			$shop = pdo_get(PDO_NAME . 'merchantdata', array('id' => $v['sid']), array('storename', 'logo'));
			$v['shop_name'] = $shop['storename'];
			$v['shop_logo'] = tomedia($shop['logo']);
			unset($v['sid']);
			$goods = WeChat::getGoods($v['goods_id'], $v['plugin']);
			$v['goods_name'] = $goods['name'];
			$v['goods_logo'] = tomedia($goods['logo']);
			$v['goods_oldprice'] = intval($goods['oldprice'] ? $goods['oldprice'] : 0);
			$v['goods_class'] = $goods['goods_class'];
			$v['createtime'] = date('Y-m-d H:i', $v['createtime']);

			switch ($v['status']) {
			case '0':
				$v['state'] = '待付款';
				break;

			case '1':
				if ($v['plugin'] == 'wlfightgroup') {
					$v['state'] = '待收货';
				}
				else {
					$v['state'] = '待使用';
				}

				break;

			case '2':
				if ($v['plugin'] == 'wlfightgroup') {
					$v['state'] = '已消费';
				}
				else {
					$v['state'] = '待评价';
				}

				break;

			case '3':
				$v['state'] = '已完成';
				break;

			case '4':
				if ($v['expressid']) {
					$v['statusName'] = '待收货';
				}
				else {
					$v['statusName'] = '待消费';
				}

				break;

			case '5':
				$v['state'] = '已取消';
				break;

			case '6':
				$v['state'] = '待退款';
				break;

			case '7':
				$v['state'] = '已退款';
				break;

			case '8':
				$v['state'] = '待发货';
				break;

			case '9':
				$v['state'] = '已过期';
				break;
			}
		}

		$this->result(0, '用户订单信息', $order);
	}

	/**
     * Comment: 获取去使用订单的信息
     * Author: zzw
     */
	public function doPageGetUseOrderInfo()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$plugin = $_GPC['plugin'];
		$order = pdo_get(PDO_NAME . 'order', array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
		$shop = pdo_get(PDO_NAME . 'merchantdata', array('id' => $order['sid']));

		if ($plugin == 'groupon') {
			$goods = pdo_get(PDO_NAME . 'groupon_activity', array('id' => $order['fkid']));
			$record = pdo_get(PDO_NAME . 'groupon_userecord', array('id' => $order['recordid']));

			if ($order['specid']) {
				$option = pdo_get(PDO_NAME . 'goods_option', array('id' => $order['specid']), array('price', 'vipprice', 'title'));
				$goods['price'] = $option['price'];
				$goods['vipprice'] = $option['vipprice'];
			}

			if ($order['vipbuyflag']) {
				$goods['price'] = $goods['vipprice'];
			}

			$url = app_url('groupon/grouponapp/hexiao', array('id' => $id));
			$url = WeChat::getShortConnection($url);
			$data['shop_id'] = $shop['id'];
			$data['shop_name'] = $shop['storename'];
			$data['shop_address'] = $shop['address'];
			$data['shop_logo'] = tomedia($shop['logo']);
			$data['location'] = unserialize($shop['location']);
			$data['good_logo'] = tomedia($goods['thumb']);
			$data['good_name'] = $goods['name'];
			$data['num'] = $order['num'];
			$data['price'] = $goods['price'];
			$data['retainage'] = $goods['retainage'];
			$data['optionid'] = $option['title'] ? $option['title'] : '';
			$data['pay_price'] = $order['price'];
			$data['levelnum'] = $order['num'] - $record['usetimes'];
			$data['checkcode'] = $record['qrcode'];
			$data['qrcode'] = $url;
		}
		else if ($plugin == 'coupon') {
			$res = wlCoupon::getSingleCoupon($order['recordid'], '*');
			$res['timess'] = $res['endtime'];
			$res['starttime'] = date('Y-m-d H:i:s', $res['starttime']);
			$res['endtime'] = date('Y-m-d H:i:s', $res['endtime']);

			if ($res['usedtime']) {
				$res['usedtime'] = unserialize($res['usedtime']);

				foreach ($res['usedtime'] as $key => &$v) {
					$v = date('Y-m-d H:i:s', $v['time']);
				}
			}

			$parent = wlCoupon::getSingleCoupons($res['parentid'], 'merchantid,logo');
			$store = pdo_get(PDO_NAME . 'merchantdata', array('id' => $parent['merchantid']));
			$res['couponlogo'] = tomedia($parent['logo']);
			$res['storename'] = $store['storename'];
			$res['storelogo'] = tomedia($store['logo']);
			$url = app_url('wlcoupon/coupon_app/hexiaocoupon', array('id' => $id));
			$url = WeChat::getShortConnection($url);
			list($res['description']) = unserialize($res['description']);

			if ($store['verkey']) {
				$res['verkeyflag'] = 1;
			}

			$data['shop_mobile'] = $shop['mobile'];
			$data['storehours'] = unserialize($shop['storehours'])['startTime'] . '-' . unserialize($shop['storehours'])['endTime'];
			$data['shop_id'] = $shop['id'];
			$data['shop_name'] = $shop['storename'];
			$data['shop_address'] = $shop['address'];
			$data['location'] = unserialize($shop['location']);
			$data['title'] = $res['title'];
			$data['price'] = $res['price'] ? $res['price'] : '免费';
			$data['concode'] = $res['concode'];
			$data['usetimes'] = intval($res['usetimes']);
			$data['description'] = $res['description'];
			$data['qrimgurl'] = $url;
			$data['sub_title'] = $res['sub_title'];
			$data['goodsdetail'] = $res['goodsdetail'];
		}
		else {
			if ($plugin == 'rush') {
				$order_out = pdo_get(PDO_NAME . 'rush_order', array('id' => $id));
				$merchant = pdo_get(PDO_NAME . 'merchantdata', array('id' => $order_out['sid']));
				$goods = pdo_get(PDO_NAME . 'rush_activity', array('id' => $order_out['activityid']));
				$order_out['levelnum'] = intval($order_out['num'] - $order_out['usetimes']);

				if ($order_out['optionid']) {
					$option = pdo_get(PDO_NAME . 'goods_option', array('id' => $order_out['optionid']), array('price', 'vipprice', 'title'));
					$goods['price'] = $option['price'];
					$goods['vipprice'] = $option['vipprice'];
				}

				if ($order_out['vipbuyflag']) {
					$goods['price'] = $goods['vipprice'];
				}

				$order_out['price'] = $order_out['actualprice'];
				$url = $url = app_url('rush/home/hexiao', array('id' => $id));
				$url = WeChat::getShortConnection($url);
				$data['shop_id'] = $merchant['id'];
				$data['shop_name'] = $merchant['storename'];
				$data['shop_address'] = $merchant['address'];
				$data['shop_logo'] = tomedia($merchant['logo']);
				$data['location'] = unserialize($merchant['location']);
				$data['good_logo'] = tomedia($goods['thumb']);
				$data['good_name'] = $goods['name'];
				$data['num'] = $order_out['num'];
				$data['price'] = $goods['price'];
				$data['optionid'] = $option['title'] ? $option['title'] : '';
				$data['pay_price'] = $order_out['price'];
				$data['levelnum'] = $order_out['num'] - $order_out['usetimes'];
				$data['checkcode'] = $order_out['checkcode'];
				$data['qrcode'] = $url;
			}
		}

		$this->result(0, '去使用的信息', $data);
	}

	/**
     * Comment: 团购进行手动核销处理
     * Author: zzw
     */
	public function doPageGroupWriteOff()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$verkey = $_GPC['verkey'];
		$orderid = $_GPC['orderid'];
		$num = $_GPC['num'];
		$mid = $_W['mid'];
		$plugin = $_GPC['plugin'];
		$merchantkey = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $id), 'verkey');

		if ($verkey == $merchantkey) {
			if ($plugin == 'rush') {
				$res = Rush::hexiaoorder($orderid, $mid, $num, 4);
			}
			else if ($plugin == 'groupon') {
				$res = Groupon::hexiaoorder($orderid, $mid, $num, 4);
			}
			else {
				$this->result(1, '该类型暂时不可核销!');
			}

			if ($res) {
				$this->result(0, '核销成功');
			}
			else {
				$this->result(1, '核销失败');
			}
		}
		else {
			$this->result(1, '密码错误');
		}
	}

	/**
     * Comment: 卡卷请求手动核销
     * Author: zzw
     */
	public function doPageCouponWriteOff()
	{
		global $_W;
		global $_GPC;
		$verkey = $_GPC['verkey'];
		$id = $_GPC['id'];
		$num = $_GPC['num'] ? $_GPC['num'] : 1;
		$mid = $_W['mid'];
		$order = pdo_get(PDO_NAME . 'order', array('id' => $id));
		$coupon = wlCoupon::getSingleCoupon($order['recordid'], 'mid,status,usedtime,endtime,starttime,usetimes,parentid,orderno,title,concode,aid');
		$merchantid = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $coupon['parentid']), 'merchantid');
		$merchantkey = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $order['sid']), 'verkey');

		if ($verkey == $merchantkey) {
			$res = wlCoupon::hexiaoorder($order['recordid'], $mid, $num, 4);

			if ($res) {
				$this->result(0, '核销成功');
			}
			else {
				$this->result(1, '使用优惠券失败');
			}
		}
		else {
			$this->result(1, '密码错误');
		}
	}

	/**
     * Comment: 用户进行订单评论
     * Author: zzw
     */
	public function doPageComment()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$mid = $_W['mid'];
		$pic = explode(',', $_GPC['pic']);
		$text = $_GPC['text'];
		$star = $_GPC['star'];
		$table = $_GPC['table'];
		$setting = $_W['setting']['upload']['image'];
		$setting['folder'] = 'images/' . MODULE_NAME;
		$imageType = $setting['extentions'];
		$imageSize = $setting['limit'] * 1024;
		$img_url = '';

		if ($_FILES) {
			$v = $_FILES['file'];
			$imgSuffix = '';
			list(, $type) = explode('/', $v['type']);

			if (in_array(strtolower($type), $imageType)) {
				$imgSuffix = '.' . strtolower($type);
			}
			else {
				$typeStr = implode(',', $imageType);
				$this->result(1, '格式错误,只能上传' . $typeStr . '格式的图片');
			}

			if ($imageSize < $v['size']) {
				$size = $imageSize / (1024 * 1024);
				$this->result(1, '图片不能超过' . $size . 'M');
			}

			$fileName = time() . rand(10000, 99999) . $imgSuffix;
			$pathName = $setting['folder'] . '/' . $fileName;
			$fullName = PATH_ATTACHMENT . $pathName;
			$attachurl = attachment_set_attach_url();
			$img_url = $attachurl . $pathName;
			if (!empty($_W['setting']['remote']['type']) && !empty($pathName)) {
				$arr = move_uploaded_file($v['tmp_name'], $fullName);
				$remotestatus = WeChat::file_remote_upload($pathName);

				if ($remotestatus) {
					$this->result(1, '远程附件上传失败，请检查配置并重新上传', $remotestatus);
				}
				else {
					$this->result(0, '图片上传成功', $img_url);
				}
			}
			else {
				$this->result(1, '图片上传失败，请重新上传');
			}
		}

		$member = pdo_get(PDO_NAME . 'member', array('id' => $mid));
		$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $mid, 'status' => 1, 'pic' => serialize($pic), 'idoforder' => $id, 'text' => $text, 'star' => intval($star), 'createtime' => time(), 'headimg' => $member['avatar'], 'nickname' => $member['nickname']);

		if ($table == 'b') {
			$data['sid'] = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $id, 'uniacid' => $_W['uniacid']), 'sid');
			$data['plugin'] = 'rush';
			pdo_update(PDO_NAME . 'rush_order', array('status' => 3), array('id' => $id));
		}
		else if ($table == 'a') {
			$order = pdo_get(PDO_NAME . 'order', array('id' => $id, 'uniacid' => $_W['uniacid']), array('sid', 'plugin'));
			$data['sid'] = $order['sid'];
			$data['plugin'] = $order['plugin'];
			pdo_update(PDO_NAME . 'order', array('status' => 3), array('id' => $id, 'uniacid' => $_W['uniacid']));
		}
		else {
			$info = pdo_get(PDO_NAME . 'timecardrecord', array('id' => $id, 'uniacid' => $_W['uniacid']), array('merchantid'));
			$data['sid'] = $info['merchantid'];
			$data['plugin'] = 'usehalf';
			pdo_update(PDO_NAME . 'timecardrecord', array('commentflag' => 1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		}

		if (empty($data['sid']) || empty($data['plugin'])) {
			$this->result(1, '未找到订单信息，请重试');
		}

		if (3 < $data['star']) {
			$data['level'] = 1;
		}
		else if ($data['star'] == 3) {
			$data['level'] = 2;
		}
		else {
			$data['level'] = 3;
		}

		if ($pic) {
			$data['ispic'] = 1;
		}

		$result = pdo_insert(PDO_NAME . 'comment', $data);

		if ($result) {
			$this->result(0, '评论成功', pdo_insertid());
		}
		else {
			$this->result(1, '评论失败，请重试');
		}
	}

	/**
     * Comment: 获取订单详细信息
     * Author: zzw
     */
	public function doPageOrderDetail()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$table = $_GPC['table'];
		$where = ' WHERE a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'] . ' AND a.id = ' . $id . ' ';

		if ($table == 'a') {
			$orderInfo = pdo_fetch('SELECT 
                b.storename,
                b.logo,
                b.mobile,
                a.mid,
                a.id,
                a.price,
                a.num,
                a.specid,
                a.orderno,
                a.createtime,
                a.fkid as goods_id,
                a.plugin,
                a.recordid FROM ' . tablename(PDO_NAME . 'order') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.sid = b.id ' . $where));

			if (0 < $orderInfo['recordid']) {
				switch ($orderInfo['plugin']) {
				case 'coupon':
					$orderInfo['checkcode'] = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $orderInfo['recordid']), 'concode');
					break;

				case 'wlfightgroup':
					$orderInfo['checkcode'] = pdo_getcolumn(PDO_NAME . 'fightgroup_userecord', array('id' => $orderInfo['recordid']), 'qrcode');
					break;

				case 'groupon':
					$orderInfo['checkcode'] = pdo_getcolumn(PDO_NAME . 'groupon_userecord', array('id' => $orderInfo['recordid']), 'qrcode');
					break;
				}

				unset($orderInfo['recordid']);
			}
		}
		else {
			$orderInfo = pdo_fetch('SELECT  
                b.storename,
                b.logo,
                b.mobile,
                a.mid,
                a.id,
                a.actualprice as price,
                a.num,
                a.optionid as specid,
                a.orderno,
                a.createtime,
                a.activityid as goods_id,
                REPLACE(\'plugin\',\'plugin\',\'rush\') as plugin,
                a.checkcode FROM ' . tablename(PDO_NAME . 'rush_order') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.sid = b.id ' . $where));
		}

		$orderInfo['createtime'] = date('Y-m-d H:i:s', $orderInfo['createtime']);
		$goods = WeChat::getGoods($orderInfo['goods_id'], $orderInfo['plugin']);
		$orderInfo['goods_name'] = $goods['name'];
		$orderInfo['goods_logo'] = tomedia($goods['logo']);
		$orderInfo['oldprice'] = intval($goods['oldprice'] ? $goods['oldprice'] : 0);
		$orderInfo['logo'] = tomedia($orderInfo['logo']);
		$orderInfo['option_title'] = '';

		if ($orderInfo['specid']) {
			$option = pdo_get(PDO_NAME . 'goods_option', array('id' => $orderInfo['specid']), array('price', 'vipprice', 'title'));
			$orderInfo['price'] = $option['price'];
			$vip = WeChat::VipVerification($orderInfo['mid']);

			if ($vip) {
				$orderInfo['price'] = $option['vipprice'];
			}

			$orderInfo['option_title'] = $option['title'];
		}

		$this->result(0, '订单详细信息', $orderInfo);
	}

	/**
     * Comment: 取消订单
     * Author: zzw
     */
	public function doPageCancelOrder()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$table = $_GPC['table'];
		if (!$id || !$table) {
			$this->result(1, '缺少参数');
		}

		if ($table == 'a') {
			$res = pdo_update(PDO_NAME . 'order', array('status' => 5), array('id' => $id));
		}
		else {
			$res = Rush::cancelorder($id);
		}

		if ($res) {
			$this->result(0, '成功取消订单');
		}
		else {
			$this->result(1, '取消失败');
		}
	}

	/**
     * Comment: 获取订单
     * Author: zzw
     */
	public function doPageGetOrderSum()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$data['pendingPayment'] = WeChat::getOrderNum($mid, 0);
		$data['toBeUsed'] = WeChat::getOrderNum($mid, 1);
		$data['toBeEvaluated'] = WeChat::getOrderNum($mid, 2);
		$data['pendingRefund'] = WeChat::getOrderNum($mid, 6);
		$this->result(0, '不同状态订单数量', $data);
	}

	/**
     * Comment: 获取收银台(支付页面)信息
     * Author: zzw
     */
	public function doPageGetPayment()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$tables = $_GPC['table'];
		$mid = $_W['mid'];

		if ($tables == 'a') {
			$orderTable = 'order';
		}
		else {
			$orderTable = 'rush_order';
		}

		$member = pdo_get(PDO_NAME . 'member', array('id' => $mid));
		$order = pdo_get(PDO_NAME . $orderTable, array('id' => $id));

		if (!$order) {
			$this->result(1, '订单不存在');
		}

		switch ($order['plugin']) {
		case 'coupon':
			$goodTable = 'couponlist';
			$goodID = $order['fkid'];
			$field = array('title');
			$plugin = 'wlCoupon';
			$payfor = 'Couponsharge';
			break;

		case 'wlfightgroup':
			$goodTable = 'fightgroup_goods';
			$goodID = $order['fkid'];
			$field = array('name');
			$plugin = 'Wlfightgroup';
			$payfor = 'Fightsharge';
			break;

		case 'groupon':
			$goodTable = 'groupon_activity';
			$goodID = $order['fkid'];
			$field = array('name');
			$plugin = 'Groupon';
			$payfor = 'GrouponOrder';
			break;

		default:
			$goodTable = 'rush_activity';
			$goodID = $order['activityid'];
			$field = array('name');
			$plugin = 'Rush';
			$payfor = 'RushOrder';
			break;
		}

		$goodName = pdo_getcolumn(PDO_NAME . $goodTable, array('id' => $goodID), $field);
		$bankrid = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), 'bankrid');

		if ($tables == 'a') {
			$price = $order['price'];
		}
		else {
			$price = $order['actualprice'];
		}

		$params = array('tid' => $order['orderno'], 'ordersn' => $order['orderno'], 'title' => $goodName, 'fee' => $price, 'bankrid' => $bankrid);
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => $plugin, 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $member['openid'], 'module' => 'weliam_merchant', 'plugin' => $plugin, 'payfor' => $payfor, 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		if ($member['uid']) {
			$credtis = mc_fetch($member['uid'], array('credit1', 'credit2'));
		}
		else {
			$credtis['credit1'] = $member['credit1'];
			$credtis['credit2'] = $member['credit2'];
		}

		$switch = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'city_selection_set'), 'value'));
		unset($switch['appid']);
		unset($switch['secret']);
		$data['params'] = $params;
		$data['switch'] = $switch;
		$data['user']['integral'] = $credtis['credit1'];
		$data['user']['balance'] = $credtis['credit2'];
		$this->result(0, '获取收银台信息', $data);
	}

	/**
     * Comment: 获取用小程序微信支付需要的信息
     * Author: zzw
     */
	public function doPageWeChatPay()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$ordersn = $_GPC['ordersn'];
		$title = $_GPC['title'];
		$fee = $_GPC['fee'];
		$info = WeChatPay::pay($mid, $ordersn, $title, $fee);
		$this->result(0, '获取用微信支付所需要的信息', $info);
	}

	/**
     * Comment: 支付完成 回调数据处理  &&  使用余额支付的信息处理
     * Author: zzw
     */
	public function doPageSuccessCallback()
	{
		global $_W;
		global $_GPC;
		$pay = $_GPC['pay'];
		$id = $_GPC['id'];
		$mid = $_W['mid'];
		$table = $_GPC['table'];
		$params = array('ordersn' => $_GPC['ordersn'], 'title' => $_GPC['title'], 'fee' => $_GPC['fee']);
		$paytype = 2;

		if ($table == 'a') {
			$orderTab = 'order';
		}
		else if ($table == 'b') {
			$orderTab = 'rush_order';
		}
		else {
			if ($table == 'c') {
				$orderTab = 'halfcard_record';
			}
		}

		$order = pdo_get(PDO_NAME . $orderTab, array('id' => $id));

		if (0 < $order['status']) {
			if ($order['status'] == 1 || $order['status'] == 2 || $order['status'] == 3 || $order['status'] == 4) {
				$this->result(1, '订单已支付，请勿重复操作');
			}
			else if ($order['status'] == 9) {
				$this->result(1, '订单已过期，请重新购买');
			}
			else {
				$this->result(1, '订单已取消，请重新购买');
			}
		}

		$member = pdo_get(PDO_NAME . 'member', array('id' => $mid), array('uid', 'credit2', 'nickname'));

		if ($pay == 0) {
			if (!empty($member) && 0 < $member['uid']) {
				$member['credit2'] = implode(mc_credit_fetch($member['uid'], 'credit2'));
			}

			if ($member['credit2'] < $params['fee']) {
				$this->result(1, '支付失败,余额不足');
			}

			Member::credit_update_credit2($mid, 0 - $params['fee'], '', $params['ordersn']);
			$paytype = 1;
		}

		if ($table == 'b') {
			$type = 1;
		}
		else if ($table == 'a') {
			switch ($order['plugin']) {
			case 'wlfightgroup':
				$type = 2;
				break;

			case 'coupon':
				$type = 3;
				break;

			case 'groupon':
				$type = 4;
				break;

			case 'store':
				$type = 6;
				break;
			}
		}
		else {
			if ($table == 'c') {
				$type = 5;
			}
		}

		$paidprid = Paidpromotion::getpaidpr($type, $id, $order['paytype']);
		$patInfo = array('status' => 1, 'paytime' => time(), 'paytype' => $paytype, 'paidprid' => $paidprid);
		$result = pdo_update(PDO_NAME . $orderTab, $patInfo, array('id' => $id));

		if ($result) {
			if ($table == 'c') {
				$cardID = $order['cardid'];
				$halftype = pdo_get(PDO_NAME . 'halfcard_type', array('id' => $order['typeid']));

				if ($cardID) {
					$mdata = array('uniacid' => $_W['uniacid'], 'mid' => $mid, 'id' => $cardID);
					$vipInfo = Util::getSingelData('*', PDO_NAME . 'halfcardmember', $mdata);
					$lastviptime = $vipInfo['expiretime'];
					if ($lastviptime && time() < $lastviptime) {
						$limittime = $lastviptime + $halftype['days'] * 24 * 60 * 60;
					}
					else {
						$limittime = time() + $halftype['days'] * 24 * 60 * 60;
					}
				}
				else {
					$limittime = time() + $halftype['days'] * 24 * 60 * 60;
				}

				$halfcarddata = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $mid, 'expiretime' => $limittime, 'username' => $member['nickname'], 'levelid' => $order['typeid'], 'createtime' => time());

				if ($cardID) {
					pdo_update(PDO_NAME . 'halfcardmember', $halfcarddata, array('id' => $cardID));
				}
				else {
					pdo_insert(PDO_NAME . 'halfcardmember', $halfcarddata);
				}
			}

			$this->result(0, '支付成功');
		}
		else {
			$this->result(1, '支付失败，请重新支付');
		}
	}

	/**
     * Comment: 用户申请退款功能
     * Author: zzw
     */
	public function doPageRefund()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$table = $_GPC['table'];
		$mid = $_W['mid'];

		if ($table == 'a') {
			$orderTab = 'order';
		}
		else {
			if ($table == 'b') {
				$orderTab = 'rush_order';
			}
		}

		$result = pdo_update(PDO_NAME . $orderTab, array('status' => 6, 'applyrefund' => 1, 'applytime' => time()), array('id' => $id));

		if ($result) {
			$this->result(0, '申请成功');
		}
		else {
			$this->result(1, '申请失败');
		}
	}

	/**
     * Comment: 获取店铺列表
     * Author: zzw
     */
	public function doPageShopList()
	{
		global $_W;
		global $_GPC;
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$pageNum = $_GPC['pageNum'] ? $_GPC['pageNum'] : 10;
		$lng = $_GPC['lng'] ? $_GPC['lng'] : 0;
		$lat = $_GPC['lat'] ? $_GPC['lat'] : 0;
		$nearid = $_GPC['near'] ? $_GPC['near'] : 4;
		$shopList = pdo_getall(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'status' => 2, 'enabled' => 1), array('id', 'storename', 'logo', 'address', 'location', 'storehours', 'pv'));
		$shopList = Store::getstores($shopList, $lng, $lat, $nearid);
		$shopList = array_slice($shopList, $page * $pageNum - $pageNum, $pageNum);
		$shopList = WeChat::getStoreList($shopList);
		$info['year'] = date('Y', time());
		$info['pv'] = pdo_fetchcolumn('SELECT SUM(pv) FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 2 AND enabled = 1'));
		$data['info'] = $info;
		$data['list'] = $shopList;
		$this->result(0, '店铺列表', $data);
	}

	/**
     * Comment: 获取店铺详细信息
     * Author: zzw
     */
	public function doPageShopDetails()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$info = pdo_fetch('SELECT location,storename,mobile,logo,introduction,address,realname,tel,panorama,merqrimg,videourl,album,bgmusic,storehours FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE id = ' . $id . ' AND uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid']));
		$info['logo'] = tomedia($info['logo']);
		$info['merqrimg'] = tomedia($info['merqrimg']);
		$info['album'] = unserialize($info['album']);
		$info['location'] = unserialize($info['location']);
		$storehours = unserialize($info['storehours']);
		$info['storehours'] = $storehours['startTime'] . '-' . $storehours['endTime'];

		foreach ($info['album'] as $k => &$v) {
			$v = tomedia($v);
		}

		$headline = WeChat::getHeadline($id, 1, 1);
		$headline = $headline[0] ? $headline[0] : '';
		$info['headline'] = $headline;
		$this->result(0, '店铺详细信息', $info);
	}

	/**
     * Comment: 获取店铺商品信息（公共）
     * Author: zzw
     * Date: 2019/7/8 17:11
     */
	public function doPageShopGoods()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$mid = $_W['mid'];
		$halfcardflag = WeChat::VipVerification($mid);

		if (p('rush')) {
			$rush = pdo_fetchall('SELECT id,thumb,name,price,oldprice,REPLACE(\'rush\', \'rush\', \'rush\') as plugin FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE sid = ' . $id . ' AND status IN (1,2) ORDER BY id DESC '));

			foreach ($rush as $key => &$v) {
				$v['thumb'] = tomedia($v['thumb']);
				if ($halfcardflag && $v['vipstatus'] == 1) {
					$v['price'] = $v['vipprice'];
				}
			}
		}

		if (p('groupon')) {
			$groupon = pdo_fetchall('SELECT id,name,thumb,price,oldprice,levelnum as stk,vipprice,REPLACE(\'groupon\', \'groupon\', \'groupon\') as plugin FROM ' . tablename(PDO_NAME . 'groupon_activity') . ('WHERE sid = ' . $id . ' AND status IN (1,2) ORDER BY sort DESC'));

			foreach ($groupon as $key => &$gr) {
				$gr['thumb'] = tomedia($gr['thumb']);

				if ($halfcardflag) {
					$gr['price'] = $gr['vipprice'];
				}
			}
		}

		if (p('wlcoupon')) {
			$scoupon = pdo_fetchall('SELECT color,id,title,surplus,price,vipstatus,vipprice,is_charge,logo,quantity as stk,REPLACE(\'coupon\', \'coupon\', \'coupon\') as plugin FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE merchantid = ' . $id . ' AND uniacid = ' . $_W['uniacid'] . ' AND status = 1 '));
			$halfcardflag = false;

			foreach ($scoupon as $sk => &$sval) {
				$sval['logo'] = tomedia($sval['logo']);

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

				unset($sval['is_charge']);
				unset($sval['vipstatus']);
				unset($sval['vipprice']);
				unset($sval['price']);
			}
		}

		if (p('wlfightgroup')) {
			$fightgroup = pdo_fetchall('SELECT id,logo,`name`,price,oldprice,peoplenum,stock as stk,vipdiscount,REPLACE(\'fight\', \'fight\', \'fight\') as plugin FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . (' WHERE merchantid = ' . $id . ' AND uniacid = ' . $_W['uniacid'] . ' AND status = 1'));

			foreach ($fightgroup as $k => &$v) {
				$v['logo'] = tomedia($v['logo']);

				if ($halfcardflag) {
					$v['price'] = $v['price'] - $v['vipdiscount'];
				}
			}
		}

		$shop = pdo_get(PDO_NAME . 'merchantdata', array('id' => $id), array('id', 'storename', 'logo'));
		$shop['logo'] = tomedia($shop['logo']);
		$data['active'] = $rush;
		$data['groupon'] = $groupon;
		$data['scoupon'] = $scoupon;
		$data['fightgroup'] = $fightgroup;
		$data['shop'] = $shop;
		$this->result(0, '店铺商品数据', $data);
	}

	/**
     * Comment: 获取好评信息 并且获取评价的商品的商品信息
     * Author: zzw
     */
	public function doPagePraise()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$state = $_GPC['state'];
		$mid = $_W['mid'];
		$getGoods = $_GPC['goods'];
		$where = '';
		if ($id && $state) {
			switch ($state) {
			case 1:
				$where = 'AND r.activityid = ' . $id . ' AND a.plugin = \'rush\' ';
				break;

			case 2:
				$where = 'AND b.fkid = ' . $id . ' AND a.plugin = \'groupon\' ';
				break;

			case 3:
				$where = 'AND b.fkid = ' . $id . ' AND a.plugin = \'wlfightgroup\' ';
				break;

			case 5:
				$where = 'AND b.fkid = ' . $id . ' AND a.plugin = \'coupon\' ';
				break;
			}
		}

		$pindex = max(1, intval($_GPC['page']));
		$psize = $_GPC['pageNum'] ? $_GPC['pageNum'] : 10;
		$limit = ' limit ' . ($pindex - 1) * $psize . ',' . $psize;
		$data = WeChat::getPraise($mid, $where, $limit, $getGoods);
		$this->result(0, '好评信息', $data);
	}

	/**
     * Comment: 好评详细信息
     * Author: zzw
     */
	public function doPagePraiseDetails()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$state = $_GPC['state'];
		$mid = $_W['mid'];
		$where = '';

		switch ($state) {
		case 1:
			$where = ' AND a.id = ' . $id . ' AND a.plugin = \'rush\' ';
			break;

		case 2:
			$where = ' AND a.id = ' . $id . ' AND a.plugin = \'groupon\' ';
			break;

		case 3:
			$where = ' AND a.id = ' . $id . ' AND a.plugin = \'wlfightgroup\' ';
			break;

		case 4:
			$where = ' AND a.id = ' . $id . ' AND a.plugin = \'coupon\' ';
			break;
		}

		$info = WeChat::getPraise($mid, $where, '', true)['info'][0];
		$sW = '';
		if ($id && $state) {
			switch ($state) {
			case 1:
				$sW = ' AND a.id <> ' . $info['id'] . ' AND b.sid = ' . $info['sid'] . ' OR r.sid = ' . $info['sid'] . ' AND a.plugin = \'rush\' ';
				break;

			case 2:
				$sW = ' AND a.id <> ' . $info['id'] . ' AND b.sid = ' . $info['sid'] . ' OR r.sid = ' . $info['sid'] . ' AND a.plugin = \'groupon\' ';
				break;

			case 3:
				$sW = ' AND a.id <> ' . $info['id'] . ' AND b.sid = ' . $info['sid'] . ' OR r.sid = ' . $info['sid'] . ' AND a.plugin = \'wlfightgroup\' ';
				break;

			case 4:
				$sW = ' AND a.id <> ' . $info['id'] . ' AND b.sid = ' . $info['sid'] . ' OR r.sid = ' . $info['sid'] . ' AND a.plugin = \'coupon\' ';
				break;
			}
		}

		$limit = 'limit 3';
		$other = WeChat::getPraise($mid, $sW, $limit, true);
		$avatar = pdo_fetchall('SELECT a.mid,b.avatar FROM ' . tablename(PDO_NAME . 'fabulous') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'member') . (' b ON a.mid = b.id WHERE a.relation_id = ' . $id . ' AND a.class = 1 '));

		foreach ($avatar as $av_k => &$av_v) {
			$av_v['ismylf'] = false;

			if ($av_v['mid'] == $mid) {
				$av_v['ismylf'] = true;
			}

			unset($av_v['mid']);
		}

		$info['avatar'] = $avatar;
		$data['info'] = $info;
		$data['other'] = $other;
		$this->result(0, '好评详细信息', $data);
	}

	/**
     * Comment: 商品详情页面
     * Author: zzw
     */
	public function doPageGoodsDetails()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];
		$mid = $_W['mid'];
		$pickNum = $_GPC['pickNum'] ? $_GPC['pickNum'] : 2;
		$time = time();

		switch ($type) {
		case 1:
			$table = 'rush_activity';
			$field = 'id,sid,thumb as logo,pv,price,op_one_limit as buy_limit,oldprice,vipprice,starttime,(endtime - ' . $time . ') as count_down,name,num as totalnum,detail,`describe` as description';
			$pickField = 'id,thumb as logo,name,price,oldprice';
			break;

		case 2:
			$plugin = 'groupon';
			$table = 'groupon_activity';
			$field = 'id,sid,thumb as logo,pv,price,op_one_limit as buy_limit,oldprice,vipprice,starttime,(endtime - ' . $time . ') as count_down,name,num as totalnum,detail,`describe` as description';
			$pickField = 'id,thumb as logo,name,price,oldprice';
			break;

		case 3:
			$plugin = 'wlfightgroup';
			$table = 'fightgroup_goods';
			$field = 'id,merchantid as sid,logo,price,buylimit as buy_limit,aloneprice,oldprice,peoplenum,CASE limitstarttime > 10000 WHEN 1 THEN limitstarttime ELSE UNIX_TIMESTAMP(limitstarttime) END   as starttime,(CASE limitstarttime > 10000 WHEN 1 THEN limitstarttime ELSE UNIX_TIMESTAMP(limitstarttime) END - ' . $time . ') as count_down,name,detail';
			$pickField = 'id,logo,name,price,oldprice';
			break;

		case 5:
			$plugin = 'coupon';
			$table = 'couponlist';
			$field = 'id,merchantid as sid,logo,price,get_limit as buy_limit,vipstatus,vipprice,starttime,(endtime - ' . $time . ') as count_down,title as name,quantity as totalnum,goodsdetail as detail,description';
			$pickField = 'id,logo,title as name,price';
			break;
		}

		$goods = pdo_fetch('SELECT ' . $field . ' FROM ' . tablename(PDO_NAME . $table) . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND id = ' . $id));
		if ($goods['description'] && $type == 5) {
			$goods['description'] = unserialize($goods['description']);
		}

		$orderGoodW = ' activityid = ' . $id . ' ';
		$orderTable = 'rush_order';

		if ($type != 1) {
			$orderGoodW = ' fkid = ' . $id . ' AND plugin = \'' . $plugin . '\' ';
			$orderTable = 'order';
		}

		$orderGoodW .= ' AND uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND mid = ' . $mid . '  AND status IN (0,1,2,3,4,6,9) ';
		$stopBuyNum = implode(pdo_fetch('SELECT sum(num) FROM ' . tablename(PDO_NAME . $orderTable) . (' WHERE ' . $orderGoodW)));
		$vip = WeChat::VipVerification($mid);

		if ($type == 5) {
			if ($goods['vipstatus'] == 1 && !$vip) {
				$goods['price'] = '会员特供';
			}
			else {
				if ($goods['vipstatus'] == 2 && $vip) {
					$goods['price'] = $goods['vipprice'];
				}
			}

			unset($goods['vipstatus']);
		}

		if ($vip && $type != 5 && $type != 3 && 0 < $goods['vipprice'] && $goods['vipstatus'] == 1) {
			$goods['price'] = $goods['vipprice'];
			unset($goods['vipprice']);
		}

		$homeGooda = WeChat::getHomeGoods($type, $id);
		$purchase['user_list'] = $homeGooda['user_list'];
		$purchase['user_num'] = $homeGooda['user_num'];
		$goods['stk'] = $homeGooda['stk'];
		$goods['sales_volume'] = $homeGooda['buy_num'];
		$goods['buy_limit'] = $goods['buy_limit'] ? $goods['buy_limit'] : 0;

		if (0 < $goods['buy_limit']) {
			$goods['limit_num'] = $goods['buy_limit'] - $stopBuyNum;
		}

		$shop = pdo_get(PDO_NAME . 'merchantdata', array('id' => $goods['sid']), array('id', 'storename', 'mobile', 'location', 'address', 'storehours'));
		unset($goods['sid']);
		$shop['location'] = unserialize($shop['location']);
		$shop['storehours'] = unserialize($shop['storehours'])['startTime'] . ' - ' . unserialize($shop['storehours'])['endTime'];
		$recommendGoodsList = pdo_fetchall('SELECT ' . $pickField . ' FROM ' . tablename(PDO_NAME . $table) . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid']));
		$recommend = array();

		foreach (array_rand($recommendGoodsList, $pickNum) as $k => $v) {
			$recommendGoodsList[$v]['logo'] = tomedia($recommendGoodsList[$v]['logo']);
			$recommend[$k] = $recommendGoodsList[$v];
		}

		$spec = WeChat::getSpec($id, $type, $vip);
		$goods['time'] = time();
		$goods['date'] = date('Y-m-d H:i', $goods['starttime']);
		$goods['logo'] = tomedia($goods['logo']);
		$data['goods'] = $goods;
		$data['purchase'] = $purchase;
		$data['shop'] = $shop;
		$data['recommend'] = $recommend;
		$data['spec'] = $spec;
		$this->result(0, '获取商品详情信息', $data);
	}

	/**
     * Comment: 商品购买
     * Author: zzw
     */
	public function doPageBuyGoods()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$mid = $_W['mid'];
		$type = $_GPC['type'];
		$spec = $_GPC['spec'];
		$buyNum = $_GPC['num'];
		$specId = $_GPC['spec_id'];
		$invitid = $_GPC['invitid'];
		$groupId = $_GPC['group_id'];
		$buyremark = $_GPC['leave_word'];
		$groupMethod = $_GPC['group_method'];
		$useIntegral = $_GPC['use_integral'] ? $_GPC['use_integral'] : 0;
		$collectGoodsTel = $_GPC['tel'];
		$collectGoodsName = $_GPC['name'];
		$collectGoodsAddress = $_GPC['address'];
		$member = pdo_get(PDO_NAME . 'member', array('id' => $mid), array('uid', 'credit1', 'nickname'));
		$vip = WeChat::VipVerification($mid);
		$HomeGoods = WeChat::getHomeGoods($type, $id);

		switch ($type) {
		case 1:
			$table = 'b';
			$orderTable = 'rush_order';
			$goods = pdo_fetch('SELECT sid,`name`,op_one_limit as buy_limit,num as totalnum,optionstatus,price,price,vipprice,unit,creditmoney,sharestatus,integral FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE id = ' . $id));
			break;

		case 2:
			$table = 'a';
			$plugin = 'groupon';
			$payfor = 'grouponOrder';
			$orderTable = 'order';
			$goods = pdo_fetch('SELECT sid,`name`,op_one_limit as buy_limit,num as totalnum,status,optionstatus,price,vipprice,unit,sharestatus FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE id = ' . $id));
			break;

		case 3:
			$table = 'a';
			$plugin = 'wlfightgroup';
			$payfor = 'fightsharge';
			$orderTable = 'order';
			$goods = pdo_fetch('SELECT grouptime,peoplenum,buylimit as buy_limit,expressid,stock as stk,merchantid as sid,`name`,specstatus as optionstatus,price,aloneprice,markid,unit FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . (' WHERE id = ' . $id));
			break;

		case 5:
			$table = 'a';
			$plugin = 'coupon';
			$payfor = 'couponsharge';
			$orderTable = 'order';
			$goods = pdo_fetch('SELECT id,deadline,usetimes,get_limit as buy_limit,starttime,endtime,`type`,time_type,sub_title,goodsdetail,color,description,merchantid as sid,quantity as totalnum,title as `name`,price,vipstatus,vipprice,REPLACE(\'unit\',\'unit\',\'张\') as unit FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE id = ' . $id));
			break;
		}

		$goods['stk'] = $HomeGoods['stk'];
		if ($type != 5 && $specId) {
			if ($goods['optionstatus']) {
				$option = pdo_get(PDO_NAME . 'goods_option', array('id' => $specId), array('stock', 'price', 'vipprice'));
				$goods['price'] = $option['price'];
				$goods['vipprice'] = $option['vipprice'];
				$goods['stk'] = $option['stock'];
			}
			else {
				$this->result(1, '规格参数错误，请重新选择');
			}
		}

		if ($goods['stk'] < $buyNum) {
			$this->result(1, '购买失败，库存不足');
		}

		$orderGoodW = ' activityid = ' . $id . ' ';

		if ($type != 1) {
			$orderGoodW = ' fkid = ' . $id . ' AND plugin = \'' . $plugin . '\' ';
		}

		$orderGoodW .= ' AND uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND mid = ' . $mid . ' AND status IN (0,1,2,3,4,6,9) ';
		$stopBuyNum = implode(pdo_fetch('SELECT sum(num) FROM ' . tablename(PDO_NAME . $orderTable) . (' WHERE ' . $orderGoodW)));
		if ($goods['buy_limit'] < $stopBuyNum + $buyNum && 0 < $goods['buy_limit']) {
			$this->result(1, '当前商品是限购商品，你还能购买' . ($goods['buy_limit'] - $stopBuyNum) . $goods['unit']);
		}

		if ($type == 1) {
			$nopayorder = pdo_get(PDO_NAME . 'rush_order', array('mid' => $mid, 'status' => 0, 'activityid' => $id));

			if ($nopayorder) {
				$this->result(1, '购买失败,存在未支付的抢购订单!');
			}
		}

		if ($type == 2) {
			if ($goods['status'] != 2) {
				if ($goods['status'] == 1) {
					$this->result(1, '活动未开始');
				}
				else if ($goods['status'] == 3) {
					$this->result(1, '活动已结束');
				}
				else if ($goods['status'] == 4) {
					$this->result(1, '活动已下架');
				}
				else {
					if ($goods['status'] == 7) {
						$this->result(1, '商品已抢完');
					}
				}
			}
		}

		if ($type == 3) {
			if ($groupId) {
				$group = pdo_get(PDO_NAME . 'fightgroup_group', array('id' => $groupId));
				if ($group['status'] == 2 || $group['lacknum'] == 0) {
					$this->result(1, '加入失败,该团人数已满');
				}
				else {
					if ($group['status'] == 3 || $group['failtime'] < time()) {
						$this->result(1, '加入失败,该团已解散');
					}
				}
			}
		}

		if (($type == 1 || $type == 2) && $vip && 0 < $goods['vipprice'] && $goods['vipstatus'] == 1) {
			$goods['price'] = $goods['vipprice'];
		}

		if ($type == 5) {
			if ($goods['vipstatus'] == 1) {
				if (!$vip) {
					$this->result(1, '此卡卷为会员特供,请先成为会员!');
				}
			}
			else {
				if ($goods['vipstatus'] == 2) {
					if ($vip) {
						$goods['price'] = $goods['vipprice'];
					}
				}
			}

			if ($goods['endtime'] < time()) {
				$this->result(1, '抱歉!该优惠卷已停止发放');
			}
		}

		if ($type == 3) {
			if ($groupMethod == 2) {
				$goods['price'] = $goods['aloneprice'];
			}
		}

		if ($useIntegral == 1 && ($type == 1 || $type == 3)) {
			$set = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'creditset'), 'value'));
			$integral = $member['credit1'];

			if ($member['uid']) {
				$integral = mc_fetch($member['uid'], array('credit1'))['credit1'];
			}

			$Counterbalance = 0;
			$needIntegral = 0;

			if ($set['dkstatus'] == 1) {
				if ($type == 1) {
					if (0 < $goods['creditmoney']) {
						$Counterbalance = $goods['creditmoney'];
						$needIntegral = $set['proportion'];
					}

					$remark = '抢购[' . $goods['name'] . ']抵扣积分';
				}
				else {
					if ($type == 3) {
						if ($goods['markid']) {
							$marking = pdo_get(PDO_NAME . 'marking', array('id' => $goods['markid'], 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('creditmoney', 'deduct', 'manydeduct'));
							if (0 < $marking['creditmoney'] && 0 < $marking['deduct']) {
								$addNum = floor($marking['deduct'] / $marking['creditmoney']);
								$i = 1;

								while ($i <= $addNum) {
									$quota = $i * $marking['creditmoney'];
									$credit = $i * $set['proportion'];

									if ($credit < $integral) {
										$Counterbalance = $quota;
										$needIntegral = $credit;
									}

									++$i;
								}
							}
						}

						$remark = '拼团[' . $goods['name'] . ']抵扣积分';
					}
				}
			}

			if ($integral < $needIntegral) {
				$this->result(1, '积分抵扣失败,积分不足!');
			}

			if (0 < $needIntegral) {
				Member::credit_update_credit1($mid, 0 - $needIntegral, $remark);
			}
		}

		$shareID = 0;
		if ($type == 1 || $type == 2) {
			if ($goods['sharestatus'] == 1) {
				$shareID = pdo_getcolumn(PDO_NAME . 'sharegift_record', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'plugin' => $type, 'mid' => $mid, 'goodsid' => $id, 'status' => 0, 'orderid' => 0), 'id');
			}
			else {
				if ($goods['sharestatus'] == 2) {
					$shareID = pdo_getcolumn(PDO_NAME . 'sharegift_record', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'plugin' => $type, 'buymid' => $mid, 'mid' => $invitid, 'goodsid' => $id, 'status' => 0, 'orderid' => 0), 'id');
				}
			}
		}

		if ($type == 3 && $groupId) {
			$groupInfo = pdo_get(PDO_NAME . 'fightgroup_group', array('id' => $groupId));
			$updateGroup['lacknum'] = $groupInfo['lacknum'] - 1;

			if ($updateGroup['lacknum'] == 0) {
				$updateGroup['status'] = 2;
				$updateGroup['status'] = time();
			}

			pdo_update(PDO_NAME . 'fightgroup_group', $updateGroup, array('id' => $groupId));
		}

		if ($type == 3 && !$groupId) {
			$groupData = array('uniacid' => $_W['uniacid'], 'status' => 1, 'goodsid' => $goods['id'], 'aid' => $_W['aid'], 'sid' => $goods['sid'], 'neednum' => $goods['peoplenum'], 'lacknum' => $goods['peoplenum'] - 1, 'starttime' => time(), 'failtime' => time() + $goods['grouptime'] * 3600);
			pdo_insert(PDO_NAME . 'fightgroup_group', $groupData);
			$groupId = pdo_insertid();
		}

		if ($type == 3 && $collectGoodsAddress) {
			$data['uniacid'] = $_W['uniacid'];
			$data['mid'] = $mid;
			$data['goodsid'] = $id;
			$data['merchantid'] = $goods['sid'];
			$data['address'] = $collectGoodsAddress;
			$data['name'] = $collectGoodsName;
			$data['tel'] = $collectGoodsTel;

			if ($goods['expressid']) {
				$express = pdo_get(PDO_NAME . 'express_template', array('id' => $goods['expressid']));

				if ($express['expressarray']) {
					$expressarray = unserialize($express['expressarray']);

					foreach ($expressarray as $key => &$v) {
						$v['area'] = rtrim($v['area'], ',');
						$v['area'] = explode(',', $v['area']);

						if (in_array($collectGoodsAddress, $v['area'])) {
							if ($v['num'] < $buyNum) {
								$expressprice = $v['money'] + ceil(($buyNum - $v['num']) / $v['numex']) * $v['moneyex'];
							}
							else {
								$expressprice = $v['money'];
							}
						}
					}
				}

				if (empty($expressprice)) {
					if ($express['defaultnum'] < $buyNum) {
						$expressprice = $express['defaultmoney'] + ceil(($buyNum - $express['defaultnum']) / $express['defaultnumex']) * $express['defaultmoneyex'];
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
		}
		else {
			$expressprice = 0;
		}

		$totalPrice = $goods['price'] * $buyNum;
		$payPrice = $totalPrice - $Counterbalance;
		$goods['price'] = $payPrice + $expressprice;
		$orderInfo = array('mid' => $mid, 'aid' => $_W['aid'], 'sid' => $goods['sid'], 'num' => $buyNum, 'price' => sprintf('%.2f', $goods['price']), 'mobile' => $collectGoodsTel, 'status' => 0, 'address' => $collectGoodsAddress, 'shareid' => $shareID, 'orderno' => createUniontid(), 'uniacid' => $_W['uniacid'], 'createtime' => time(), 'vipbuyflag' => $vip ? 1 : 0, 'settlementmoney' => sprintf('%.2f', $goods['price']));

		if ($orderTable == 'order') {
			$orderInfo['fkid'] = $id;
			$orderInfo['plugin'] = $plugin;
			$orderInfo['payfor'] = $payfor;
			$orderInfo['spec'] = $spec;
			$orderInfo['specid'] = $specId;

			if ($type == 3) {
				$orderInfo['fightstatus'] = $groupMethod;
				$orderInfo['fightgroupid'] = $groupId;
				$orderInfo['expressid'] = $expressid;
				$orderInfo['buyremark'] = $buyremark;

				if ($vip) {
					if ($Counterbalance) {
						$card_type = 3;
					}
					else {
						$card_type = 1;
					}
				}
				else if ($Counterbalance) {
					$card_type = 2;
				}
				else {
					$card_type = 0;
				}

				$orderInfo['card_type'] = $card_type;
				$orderInfo['card_id'] = $needIntegral;
				$orderInfo['card_fee'] = $Counterbalance;
			}

			$orderInfo['goodsprice'] = sprintf('%.2f', $totalPrice);
			$orderInfo['name'] = $collectGoodsName ? $collectGoodsName : $member['nickname'];
			$orderInfo['oprice'] = sprintf('%.2f', $totalPrice);
		}
		else {
			$orderInfo['activityid'] = $id;
			$orderInfo['actualprice'] = $goods['price'];
			$orderInfo['usetimes'] = $buyNum;
			$random = Util::createConcode(1);
			$orderInfo['checkcode'] = $random;
			$orderInfo['username'] = $collectGoodsName ? $collectGoodsName : $member['nickname'];
			$orderInfo['optionid'] = $specId;
			$orderInfo['dkcredit'] = $needIntegral;
			$orderInfo['dkmoney'] = $Counterbalance;
		}

		pdo_insert(PDO_NAME . $orderTable, $orderInfo);
		$orderid = pdo_insertid();
		$GoodsStk = $goods['stk'] - $buyNum;

		if ($specId) {
			pdo_update(PDO_NAME . 'goods_option', array('stock' => $GoodsStk), array('id' => $specId));
		}
		else {
			if ($type == 3) {
				pdo_update(PDO_NAME . 'fightgroup_goods', array('stock' => $GoodsStk), array('id' => $id));
			}
		}

		if ($shareID) {
			pdo_update(PDO_NAME . 'sharegift_record', array('orderid' => $orderid), array('id' => $shareID));
		}

		if ($type == 1) {
			if ($goods['integral']) {
				$remark = '抢购:[' . $goods['name'] . ']赠送积分';
				Member::credit_update_credit1($mid, $goods['integral'] * $buyNum, $remark);
			}
		}

		if ($orderTable == 'order') {
			$record['uniacid'] = $_W['uniacid'];
			$record['orderid'] = $orderid;
			$record['createtime'] = time();
			$record['usetimes'] = $buyNum;

			if ($type == 2) {
				$record['aid'] = $_W['aid'];
				$record['qrcode'] = Util::createConcode(6);
				pdo_insert(PDO_NAME . 'groupon_userecord', $record);
				$recordid = pdo_insertid();
			}
			else if ($type == 3) {
				$record['qrcode'] = Util::createConcode(5);
				pdo_insert(PDO_NAME . 'fightgroup_userecord', $record);
				$recordid = pdo_insertid();
			}
			else {
				if ($type == 5) {
					if ($goods['time_type'] == 1) {
						$starttime = $goods['starttime'];
						$endtime = $goods['endtime'];
					}
					else {
						$starttime = time();
						$endtime = time() + $goods['deadline'] * 24 * 3600;
					}

					$couponRecord = array('mid' => $orderInfo['mid'], 'aid' => $orderInfo['aid'], 'uniacid' => $_W['uniacid'], 'parentid' => $goods['id'], 'status' => 1, 'type' => $goods['type'], 'title' => $goods['name'], 'sub_title' => $goods['sub_title'], 'content' => $goods['goodsdetail'], 'description' => $goods['description'], 'color' => $goods['color'], 'starttime' => $starttime, 'endtime' => $endtime, 'createtime' => time(), 'orderno' => $orderInfo['orderno'], 'price' => $orderInfo['price'] / $orderInfo['num'], 'usetimes' => $goods['usetimes'] * $orderInfo['num'], 'concode' => Util::createConcode(4));
					pdo_insert(PDO_NAME . 'member_coupons', $couponRecord);
					$recordid = pdo_insertid();
				}
			}

			pdo_update(PDO_NAME . $orderTable, array('recordid' => $recordid), array('id' => $orderid));
		}

		$returnData = array('id' => $orderid, 'table' => $table);

		if (0 < $orderInfo['price']) {
			$returnData['state'] = 1;
			$this->result(0, '购买成功', $returnData);
		}
		else {
			$newdata = array('status' => 1, 'paytime' => time());
			pdo_update(PDO_NAME . $orderTable, $newdata, array('id' => $orderid));
			$returnData['state'] = 0;
			$this->result(0, '0元购,购买成功!', $returnData);
		}
	}

	/**
     * Comment: 获取集call信息详情
     * Author: zzw
     */
	public function doPageCall()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$mid = $_W['mid'];
		$info = pdo_fetch('SELECT 
            a.id,
            a.number,
            a.explain,
            a.limit,
            a.explain,
            a.receive_time,
            a.use_time,
            b.title,
            b.logo,
            (b.quantity - b.surplus) as stk,
            c.id as shop_id,
            c.storename,
            c.storehours,
            c.mobile,
            c.address,
            c.location FROM ' . tablename(PDO_NAME . 'call') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'couponlist') . ' b ON a.prize_id = b.id ' . ' LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . ' `c` ON b.merchantid = c.id ' . (' WHERE a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'] . ' AND a.id = ' . $id . ' ORDER BY id DESC '));
		$member = pdo_get(PDO_NAME . 'member', array('id' => $mid), array('avatar'));
		$info['avatar'] = $member['avatar'];
		$info['logo'] = tomedia($info['logo']);
		$info['receive_time'] = date('Y年m月d日', $info['receive_time']);
		$info['use_time'] = date('Y年m月d日', $info['use_time']);
		$info['storehours'] = unserialize($info['storehours'])['startTime'] . '-' . unserialize($info['storehours'])['endTime'];
		$info['location'] = unserialize($info['location']);
		$info['lat'] = $info['location']['lat'];
		$info['lng'] = $info['location']['lng'];
		$info['number'] = intval($info['number']);
		unset($info['location']);
		$hitCall = pdo_fetchall('SELECT m.avatar FROM ' . tablename(PDO_NAME . 'call_list') . ' a RIGHT JOIN ' . tablename(PDO_NAME . 'call_hit') . ' b ON a.id = b.list_id RIGHT JOIN ' . tablename(PDO_NAME . 'member') . (' m ON b.mid = m.id WHERE a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'] . ' AND a.mid = ' . $mid . ' AND a.call_id = ' . $id . ' '));
		$hitCall = array_column($hitCall, 'avatar');
		$info['surplus'] = intval($info['number']) - intval(count($hitCall));
		$info['hit_call'] = $hitCall;
		$this->result(0, '', $info);
	}

	/**
     * Comment: 发起打call活动
     * Author: zzw
     * @param $id     打call活动的id
     * @param $mid    发起打call用户的id
     * @return mixed
     */
	private function doPageLaunchCall($id, $mid)
	{
		global $_W;
		global $_GPC;
		$data['call_id'] = $id;
		$data['mid'] = $mid;
		$data['uniacid'] = $_W['uniacid'];
		$data['aid'] = $_W['aid'];
		$existence = pdo_get(PDO_NAME . 'call_list', $data);

		if ($existence) {
			return $existence['id'];
		}

		$data['start_time'] = time();
		$result = pdo_insert(PDO_NAME . 'call_list', $data);

		if ($result) {
			return pdo_insertid();
		}

		$this->result(1, '打call失败，请重试!');
	}

	/**
     * Comment: 用户为另一个用户打call的操作
     * Author: zzw
     */
	public function doPageHitCall()
	{
		global $_W;
		global $_GPC;
		$call_id = $_GPC['id'];
		$mid = $_W['mid'];
		$data['mid'] = $_GPC['hit_mid'];
		$data['uniacid'] = $_W['uniacid'];
		$data['aid'] = $_W['aid'];

		if ($mid == $data['mid']) {
			$this->result(1, '抱歉,不能给自己打call哦！');
		}

		$callInfo = pdo_get(PDO_NAME . 'call', array('id' => $call_id), array('id', 'number', 'prize_id', 'receive_time'));

		if ($callInfo['receive_time'] < time()) {
			$this->result(1, '活动已过期!');
		}

		$id = $this->doPageLaunchCall($call_id, $mid);
		$listInfo = implode(pdo_get(PDO_NAME . 'call_list', array('id' => $id), array('grant')));
		$data['list_id'] = $id;
		$existence = pdo_get(PDO_NAME . 'call_hit', $data);

		if ($existence) {
			$this->result(1, '请不要重复打call');
		}

		$data['hit_time'] = time();
		$result = pdo_insert(PDO_NAME . 'call_hit', $data);
		$hit_id = pdo_insertid();
		$quantityCollected = implode(pdo_fetch('SELECT COUNT(*) as num FROM ' . tablename(PDO_NAME . 'call_hit') . (' WHERE list_id = ' . $id)));

		if ($callInfo['number'] == $quantityCollected) {
			if ($listInfo == 1) {
				$prizeInfo = pdo_get(PDO_NAME . 'couponlist', array('id' => $callInfo['prize_id']));
				$stk = $prizeInfo['quantity'] - $prizeInfo['surplus'];

				if ($stk <= 0) {
					$this->result(1, '来晚一步，奖品已兑换完毕');
				}

				$newsurplus = $prizeInfo['surplus'] + 1;
				wlCoupon::updateCoupons(array('surplus' => $newsurplus), array('id' => $callInfo['prize_id']));

				if ($prizeInfo['time_type'] == 1) {
					$starttime = $prizeInfo['starttime'];
					$endtime = $prizeInfo['endtime'];
				}
				else {
					$starttime = time();
					$endtime = time() + $prizeInfo['deadline'] * 24 * 3600;
				}

				$data = array('mid' => $mid, 'aid' => $_W['aid'], 'parentid' => $prizeInfo['id'], 'status' => 1, 'type' => $prizeInfo['type'], 'title' => $prizeInfo['title'], 'sub_title' => $prizeInfo['sub_title'], 'content' => $prizeInfo['goodsdetail'], 'description' => $prizeInfo['description'], 'color' => $prizeInfo['color'], 'starttime' => $starttime, 'endtime' => $endtime, 'createtime' => time(), 'usetimes' => $prizeInfo['usetimes'], 'concode' => Util::createConcode(4));
				$res = wlCoupon::saveMemberCoupons($data);

				if ($res) {
					pdo_update(PDO_NAME . 'call_list', array('grant' => 2), array('id' => $id));
					$this->result(2, '恭喜你，集call成功!奖品已发放到账号');
				}
			}
			else {
				$this->result(1, 'call已收集完毕!');
			}
		}

		if ($result) {
			$this->result(0, '打call成功', $hit_id);
		}
		else {
			$this->result(1, '打call失败，请重试!');
		}
	}

	/**
     * Comment: 获取会员页面信息
     * Author: zzw
     */
	public function doPageVipInfo()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$pageNum = $_GPC['pageNum'] ? $_GPC['pageNum'] : 10;
		$lng = $_GPC['lng'] ? $_GPC['lng'] : 0;
		$lat = $_GPC['lat'] ? $_GPC['lat'] : 0;
		$set = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'halfcard'), 'value'));
		$vipWhere = ' WHERE mid = ' . $mid . ' AND uniacid = ' . $_W['uniacid'] . ' AND disable = 0 ';

		if ($set['halfcardtype'] != 1) {
			$vipWhere .= ' AND aid = ' . $_W['aid'] . ' ';
		}

		$vipInfo = pdo_fetchall('SELECT id,expiretime,username,levelid,createtime FROM ' . tablename(PDO_NAME . 'halfcardmember') . (' ' . $vipWhere . ' ORDER BY expiretime DESC,levelid DESC '));

		if ($vipInfo) {
			foreach ($vipInfo as $k => &$v) {
				if ($v['expiretime'] < time()) {
					pdo_delete('wlmerchant_halfcardmember', array('id' => $v['id']));
					unset($v);
				}
				else if ($v['levelid']) {
					$cardLevel = pdo_get(PDO_NAME . 'halflevel', array('id' => $v['levelid']), array('name', 'cardimg'));
					$v['vip_name'] = $cardLevel['name'];
					$v['vip_bg'] = $cardLevel['cardimg'] ? tomedia($cardLevel['cardimg']) : tomedia($set['cardimg']);
				}
				else {
					$halflevel = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'halflevel'), 'value'));
					$v['vip_name'] = $halflevel['name'];
					$v['vip_bg'] = $halflevel['cardimg'] ? tomedia($halflevel['cardimg']) : tomedia($set['cardimg']);
				}
			}

			$vipInfo = $vipInfo[0];
			$vipInfo['expiretime'] = date('Y-m-d', $vipInfo['expiretime']);
			unset($vipInfo['levelid']);
		}

		$vipTotal = count(pdo_fetchall('SELECT id FROM ' . tablename(PDO_NAME . 'halfcardmember') . ' GROUP BY mid '));
		$vipInfo['total'] = $vipTotal;
		$discountCardList = pdo_fetchall('SELECT a.datestatus,a.activediscount,a.discount,a.daily,a.week,a.day,a.limit,a.id,a.title,b.id as shop_id,b.storename,b.logo,b.location FROM ' . tablename(PDO_NAME . 'halfcardlist') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.merchantid = b.id WHERE a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'] . ' AND a.status = 1 AND b.status = 2 AND b.enabled = 1 '));
		$discountCardList = Store::getstores($discountCardList, $lng, $lat, 4);
		$discountCardList = array_slice($discountCardList, $page * $pageNum - $pageNum, $pageNum);

		foreach ($discountCardList as $k => &$v) {
			if ($v['datestatus'] == 1) {
				$takeTime = unserialize($v['week']);
				$dayTime = date('w', time());

				if ($dayTime == 0) {
					$dayTime = 7;
				}
			}
			else {
				if ($v['datestatus'] == 2) {
					$takeTime = unserialize($v['day']);
					$dayTime = date('d', time());
				}
			}

			if (in_array($dayTime, $takeTime)) {
				$v['discount'] = $v['activediscount'];
			}
			else {
				if ($v['daily'] == 0) {
					unset($v);
				}
			}

			unset($v['location']);
			unset($v['url']);
			unset($v['storehours']);
			unset($v['cate']);
			unset($v['activediscount']);
			unset($v['daily']);
			unset($v['week']);
			unset($v['day']);
			unset($v['datestatus']);
		}

		$giftBag = pdo_fetchall('SELECT a.id,a.title,a.limit,a.price,a.usetimes,a.datestatus,b.id as shop_id,b.logo,b.storename FROM ' . tablename(PDO_NAME . 'package') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.merchantid = b.id WHERE a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'] . ' AND a.listshow = 0 AND a.status = 1 AND b.status = 2 AND b.enabled = 1 '));
		$giftBag = array_slice($giftBag, $page * $pageNum - $pageNum, $pageNum);

		foreach ($giftBag as $k => &$v) {
			if ($v['datestatus'] == 4) {
				$begin = mktime(0, 0, 0, 1, 1, date('Y'));
			}
			else if ($v['datestatus'] == 3) {
				$begin = mktime(0, 0, 0, date('m'), 1, date('Y'));
			}
			else if ($v['datestatus'] == 2) {
				$begin = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
			}
			else {
				$begin = 0;
			}

			if ($vipInfo) {
				if ($begin < $vipInfo['createtime'] && $v['resetswitch']) {
					$begin = $vipInfo['createtime'];
				}
			}

			$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename(PDO_NAME . 'timecardrecord') . (' WHERE cardid = ' . $vipInfo['id'] . ' AND activeid = ' . $v['id'] . ' AND merchantid = ' . $v['shop_id'] . ' AND type = 2 AND usetime > ' . $begin));
			$v['stk'] = $v['usetimes'] - $times;
			$v['logo'] = tomedia($v['logo']);
			unset($v['datestatus']);
		}

		if ($set['noticestatus'] == 2) {
			$recodes = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'halfcardmember') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND username != \'\' ORDER BY id DESC LIMIT 15'));

			if ($recodes) {
				$recode = array();

				foreach ($recodes as $key => &$re) {
					$re['day'] = round(($re['expiretime'] - $re['createtime']) / 3600 / 24);
					$recode[$key]['msg'] = $re['username'] . '开通了' . $re['day'] . '天会员';
					$head = pdo_getcolumn(PDO_NAME . 'member', array('id' => $re['mid']), 'avatar');
					$recode[$key]['imgurl'] = tomedia($head);
				}
			}
		}

		if ($vipInfo) {
			unset($vipInfo['createtime']);
		}

		$data['info'] = $vipInfo;
		$data['discount_card'] = $discountCardList;
		$data['gift_bag'] = $giftBag;
		$data['recode'] = $recode;
		$this->result(0, '会员卡(粉丝卡)页面信息', $data);
	}

	/**
     * Comment: 不同等级的会员卡的信息
     * Author: zzw
     */
	public function doPageVipLvInfo()
	{
		global $_W;
		global $_GPC;
		$mid = $_GPC['mid'];
		$cardid = $_GPC['id'];
		$set = Setting::wlsetting_read('halfcard');
		if ($cardid && $set['renewstatus']) {
			$levelid = pdo_getcolumn(PDO_NAME . 'halfcardmember', array('id' => $cardid), 'levelid');
			$cards = pdo_getall(PDO_NAME . 'halfcard_type', array('status' => 1, 'uniacid' => $_W['uniacid'], 'levelid' => $levelid), array('id', 'name', 'days', 'price', 'is_hot'), '', 'sort DESC');
		}
		else if ($set['halfcardtype'] == 2) {
			$cards = pdo_getall(PDO_NAME . 'halfcard_type', array('status' => 1, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id', 'name', 'days', 'price', 'is_hot'), '', 'sort DESC');
		}
		else {
			if ($set['halfcardtype'] == 1) {
				$cards = pdo_getall(PDO_NAME . 'halfcard_type', array('status' => 1, 'uniacid' => $_W['uniacid']), array('id', 'name', 'days', 'price', 'is_hot'), '', 'sort DESC');
			}
		}

		$phone = intval(pdo_getcolumn(PDO_NAME . 'member', array('id' => $mid), 'mobile'));
		$data['cards'] = $cards;
		$data['phone'] = $phone;
		$this->result(0, '会员卡信息列表', $data);
	}

	/**
     * Comment: 通过兑换码，开通vip会员卡
     * Author: zzw
     */
	public function doPageExchange()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$cardid = $_GPC['card_id'];
		$redeemCode = $_GPC['redeem_code'];
		$pluginSet = unserialize(unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'base'), 'value'))['plugin']);
		$member = pdo_get(PDO_NAME . 'member', array('id' => $mid));
		if (empty($member['mobile']) && in_array('halfcard', $pluginSet)) {
			$this->result(1, '未绑定手机号，请先绑定！');
		}

		if ($redeemCode) {
			$type = Util::getSingelData('*', PDO_NAME . 'token', array('number' => $redeemCode));

			if (!$type) {
				$this->result(1, '激活码不存在！');
			}

			if ($type['status'] == 1) {
				$this->result(1, '该激活码已使用！');
			}

			$dayNum = $type['days'];

			if ($cardid) {
				$mdata = array('uniacid' => $_W['uniacid'], 'mid' => $mid, 'id' => $cardid);
			}
			else {
				$mdata = array('uniacid' => $_W['uniacid'], 'mid' => $mid);
			}

			$halfInfo = pdo_GET(PDO_NAME . 'halfcardmember', $mdata);
			$lastviptime = $halfInfo['expiretime'];
			if ($lastviptime && time() < $lastviptime) {
				$limittime = $lastviptime + $dayNum * 24 * 60 * 60;
			}
			else {
				$limittime = time() + $dayNum * 24 * 60 * 60;
			}

			$halfcarddata = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $mid, 'expiretime' => $limittime, 'username' => $member['nickname'], 'levelid' => $type['levelid'], 'createtime' => time());
			$lastviptime = Member::vip($mid);
			if (empty($lastviptime) || $lastviptime < $limittime) {
				$vipleveldays = floor($limittime / 24 * 60 * 60);
				$memberData = array('level' => 1, 'vipstatus' => 1, 'vipleveldays' => $vipleveldays, 'lastviptime' => $limittime, 'aid' => $_W['aid']);
				pdo_update(PDO_NAME . 'member', $memberData, array('id' => $mid));
			}

			if ($cardid) {
				if (pdo_update(PDO_NAME . 'halfcardmember', $halfcarddata, array('id' => $cardid))) {
					pdo_update(PDO_NAME . 'token', array('status' => 1, 'mid' => $mid, 'openid' => $member['openid']), array('number' => $redeemCode));
					$limittime = date('Y-m-d', $limittime);
					$this->result(1, '兑换成功,到期时间为' . $limittime);
				}
			}
			else {
				if (pdo_insert(PDO_NAME . 'halfcardmember', $halfcarddata)) {
					pdo_update(PDO_NAME . 'token', array('status' => 1, 'mid' => $mid, 'openid' => $member['openid']), array('number' => $redeemCode));
					$limittime = date('Y-m-d', $limittime);
					$this->result(1, '兑换成功,到期时间为' . $limittime);
				}
			}
		}
		else {
			$this->result(1, '请输入正确的兑换码!');
		}
	}

	/**
     * Comment: 开通会员卡，续费会员卡
     * Author: zzw
     */
	public function doPageOpenUpVip()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$mid = $_W['mid'];
		$cardID = $_GPC['card_id'];
		$plugin = unserialize(Setting::wlsetting_read('base')['plugin']);
		$member = pdo_get(PDO_NAME . 'member', array('id' => $mid));
		if (empty($member['mobile']) && in_array('halfcard', $plugin)) {
			$this->result(1, '未绑定手机号，去绑定？');
		}

		$base = Setting::wlsetting_read('halfcard');

		if ($base['status']) {
			$halftype = pdo_get(PDO_NAME . 'halfcard_type', array('id' => $id));

			if (empty($halftype)) {
				$this->result(1, '选择的充值类型错误，请重试');
			}

			if ($halftype['num']) {
				$times = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'halfcard_record') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $mid . ' AND status = 1 AND typeid = ' . $halftype['id']));
				if ($halftype['num'] < $times || $times == $halftype['num']) {
					$this->result(1, '选择的充值卡最多充值' . $halftype['num'] . '次。');
				}
			}

			if ($cardID) {
				$mdata = array('uniacid' => $_W['uniacid'], 'mid' => $mid, 'id' => $cardID);
				$vipInfo = Util::getSingelData('*', PDO_NAME . 'halfcardmember', $mdata);
				$lastviptime = $vipInfo['expiretime'];
				if ($lastviptime && time() < $lastviptime) {
					$limittime = $lastviptime + $halftype['days'] * 24 * 60 * 60;
				}
				else {
					$limittime = time() + $halftype['days'] * 24 * 60 * 60;
				}
			}
			else {
				$limittime = time() + $halftype['days'] * 24 * 60 * 60;
			}

			$data = array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'mid' => $mid, 'orderno' => createUniontid(), 'status' => 0, 'createtime' => TIMESTAMP, 'price' => $halftype['price'], 'limittime' => $limittime, 'typeid' => $halftype['id'], 'howlong' => $halftype['days'], 'todistributor' => $halftype['todistributor'], 'cardid' => $cardID, 'username' => $member['nickname']);
			pdo_insert(PDO_NAME . 'halfcard_record', $data);
			$halfid = pdo_insertid();
			$returnData['id'] = $halfid;
			$returnData['table'] = 'c';
			$this->result(0, '订单生成成功，进入付款页面', $returnData);
		}
		else {
			$this->result(1, '功能已禁用');
		}
	}

	/**
     * Comment: 获取会员卡的收银台信息
     * Author: zzw
     */
	public function doPageVipGetPayment()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$mid = $_W['mid'];
		$member = pdo_get(PDO_NAME . 'member', array('id' => $mid));
		$record = pdo_get(PDO_NAME . 'halfcard_record', array('id' => $id));
		$halftypename = pdo_getcolumn(PDO_NAME . 'halfcard_type', array('id' => $record['typeid']), 'name');
		$params = array('tid' => $record['orderno'], 'ordersn' => $record['orderno'], 'title' => $halftypename, 'fee' => $record['price']);
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $member['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Merchant', 'payfor' => 'Halfcard', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		if ($member['uid']) {
			$credtis = mc_fetch($member['uid'], array('credit1', 'credit2'));
		}
		else {
			$credtis['credit1'] = $member['credit1'];
			$credtis['credit2'] = $member['credit2'];
		}

		$switch = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'city_selection_set'), 'value'));
		unset($switch['appid']);
		unset($switch['secret']);
		unset($params['tid']);
		$data['params'] = $params;
		$data['switch'] = $switch;
		$data['user']['integral'] = $credtis['credit1'];
		$data['user']['balance'] = $credtis['credit2'];
		$this->result(0, '会员卡的收银台信息', $data);
	}

	/**
     * Comment: 获取使用大礼包&打折卡的信息
     * Author: zzw
     */
	public function doPageUseGoods()
	{
		global $_W;
		global $_GPC;
		load()->library('qrcode');
		$mid = $_W['mid'];
		$id = $_GPC['id'];
		$cardid = $_GPC['cardid'];
		$type = $_GPC['type'];
		$vipInfo = pdo_get(PDO_NAME . 'halfcardmember', array('id' => $cardid), array('expiretime', 'createtime', 'disable', 'levelid'));

		if (!$vipInfo) {
			$this->result(1, '请先成为会员才能使用');
		}

		if ($vipInfo['expiretime'] < time()) {
			$this->result(1, '此卡已过期，请续费重试');
		}

		if ($vipInfo['disable']) {
			$this->result(1, '此卡被禁用，请联系客服咨询');
		}

		if ($type == 1) {
			$url = app_url('halfcard/halfcard_app/usetimescard', array('cardid' => $cardid, 'halfid' => $id));
			$url = WeChat::getShortConnection($url);
			$active = pdo_get(PDO_NAME . 'halfcardlist', array('id' => $id), array('title', 'merchantid', 'limit', 'discount', 'activediscount', 'datestatus', 'week', 'day', 'describe', 'timeslimit', 'level'));

			if ($active['timeslimit']) {
				$begintime = strtotime(date('Y-m-d', time()));
				$todaytime = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename(PDO_NAME . 'timecardrecord') . (' WHERE activeid = ' . $id . ' AND createtime > ' . $begintime . ' AND type = 1'));

				if ($active['timeslimit'] <= $todaytime) {
					$this->result(1, '失败,该商户今日特权名额已用完');
				}
			}

			$activename = $active['title'];
			$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $active['merchantid']), 'storename');
			$active['level'] = unserialize($active['level']);

			if (is_array($active['level'])) {
				if (!in_array($vipInfo['levelid'], $active['level'])) {
					$this->result(1, '您所在的会员等级不能使用该特权');
				}
			}

			if ($active['datestatus'] == 1) {
				$weeks = unserialize($active['week']);
				$today = date('w');

				if ($today == 0) {
					$today = 7;
				}

				if (in_array($today, $weeks)) {
					$halfflag = 1;
					$discount = $active['activediscount'];
				}
				else {
					$halfflag = 0;
					$discount = $active['discount'];
				}
			}
			else {
				$days = unserialize($active['day']);
				$today = date('j');

				if (in_array($today, $days)) {
					$halfflag = 1;
					$discount = $active['activediscount'];
				}
				else {
					$halfflag = 0;
					$discount = $active['discount'];
				}
			}
		}
		else {
			if ($type == 2) {
				$url = app_url('halfcard/halfcard_app/usetimescard', array('cardid' => $cardid, 'packid' => $id));
				$url = WeChat::getShortConnection($url);
				$active = pdo_get(PDO_NAME . 'package', array('id' => $id), array('title', 'oplimit', 'merchantid', 'limit', 'usetimes', 'datestatus', 'describe', 'status', 'timeslimit', 'timestatus', 'starttime', 'endtime', 'level', 'id', 'resetswitch'));

				if ($active['status'] != 1) {
					$this->result(1, '礼包已下架');
				}

				if ($active['timeslimit']) {
					$begintime = strtotime(date('Y-m-d', time()));
					$todaytime = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename(PDO_NAME . 'timecardrecord') . (' WHERE activeid = ' . $id . ' AND createtime > ' . $begintime . ' AND type = 2'));

					if ($active['timeslimit'] <= $todaytime) {
						$this->result(1, '该礼包今日已发完');
					}
				}

				if ($active['oplimit']) {
					$zerotime = strtotime(date('Y-m-d'), time());
					$times3 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename(PDO_NAME . 'timecardrecord') . (' WHERE activeid = ' . $active['id'] . ' AND mid = ' . $mid . '  AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
					$surplus = $active['oplimit'] - $times3;

					if ($surplus < 1) {
						$this->result(1, '您今天只能领取' . $active['oplimit'] . '次该礼包');
					}
				}

				$active['level'] = unserialize($active['level']);

				if (is_array($active['level'])) {
					if (!in_array($vipInfo['levelid'], $active['level'])) {
						$this->result(1, '您所在的会员等级不能领取该礼包');
					}
				}

				$activename = $active['title'];
				$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $active['merchantid']), 'storename');

				if ($active['datestatus'] == 4) {
					$begin = mktime(0, 0, 0, 1, 1, date('Y'));
				}
				else if ($active['datestatus'] == 3) {
					$begin = mktime(0, 0, 0, date('m'), 1, date('Y'));
				}
				else if ($active['datestatus'] == 2) {
					$begin = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
				}
				else {
					$begin = 0;
				}

				if ($begin < $vipInfo['createtime'] && $active['resetswitch']) {
					$begin = $vipInfo['createtime'];
				}

				$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename(PDO_NAME . 'timecardrecord') . (' WHERE cardid = ' . $cardid . ' AND activeid = ' . $id . ' AND type = 2 AND usetime > ' . $begin));
				$surplustimes = $active['usetimes'] - $times;

				if ($surplustimes < 1) {
					$this->result(1, '该礼包已全部领取完毕');
				}
			}
		}

		$data = array('describe' => $active['describe'], 'qrsrc' => $url, 'limit' => $active['limit'], 'activename' => $activename, 'storename' => $storename, 'surplustimes' => $surplustimes, 'alltime' => $active['usetimes'], 'halfflag' => $halfflag, 'discount' => $discount);
		$this->result(0, '大礼包&打折卡使用信息', $data);
	}

	/**
     * Comment: 获取客服信息
     * Author: zzw
     */
	public function doPageGetInfo()
	{
		global $_W;
		global $_GPC;
		$input = $_GPC['__input'];
		$verRes = WeChat::doPagePleaseVerification($_GET);

		if ($verRes) {
			echo $verRes;
		}

		$access_token = WeChat::doPageGetAccessToken();
		$touser = $input['FromUserName'];
		$msgtype = 'link';
		$setInfo = Setting::wlsetting_read('csd_set');
		$info = array(
			'touser'  => $touser,
			'msgtype' => $msgtype,
			'link'    => array('title' => $setInfo['title'], 'description' => $setInfo['detail'], 'url' => $setInfo['url'], 'thumb_url' => tomedia($setInfo['img']))
		);
		$apiurl = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $access_token;
		$info = WeChat::ijson_encode($info);
		ihttp_post($apiurl, $info);
	}
}

?>
