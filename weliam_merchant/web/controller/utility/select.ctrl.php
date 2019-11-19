<?php
//dezend by http://www.sucaihuo.com/
class select_WeliamController
{
	/**
     * Comment: 获取公众号地址
     * Author: zzw
     */
	public function comurl()
	{
		global $_W;
		global $_GPC;
		$pageClass = $_GPC['pageClass'];
		$system = array(
			'basic'  => array(
				'name' => '基本链接',
				'list' => array(
					array('name' => '首页入口', 'url' => app_url('dashboard/home/index', '', true)),
					array('name' => '好店入口', 'url' => app_url('store/merchant/newindex', '', true)),
					array('name' => '一卡通首页', 'url' => app_url('halfcard/halfcard_app/userhalfcard', '', true)),
					array('name' => '商户入驻', 'url' => app_url('store/supervise/information', array('applyflag' => 1), true)),
					array('name' => '商户中心', 'url' => app_url('store/storeManage/index', '', true)),
					array('name' => '个人中心', 'url' => app_url('member/user/index', '', true))
				)
			),
			'plugin' => array(
				'name' => '模块链接',
				'list' => array()
			),
			'other'  => array(
				'name' => '其他链接',
				'list' => array(
					array('name' => '我的订单', 'url' => app_url('order/userorder/orderlist', array('status' => 'all'), true)),
					array('name' => '帮助中心', 'url' => app_url('helper/helper_app/index', '', true)),
					array('name' => '我的卡卷', 'url' => app_url('wlcoupon/coupon_app/couponList', '', true)),
					array('name' => '消费记录', 'url' => app_url('halfcard/halfcard_app/userecord', '', true)),
					array('name' => '入驻首页', 'url' => app_url('store/storeManage/index', '', true))
				)
			)
		);

		if (p('rush')) {
			$system['plugin']['list'][] = array('name' => '抢购列表', 'url' => app_url('rush/home/index', '', true));
		}

		if (p('wlfightgroup')) {
			$system['plugin']['list'][] = array('name' => '拼团列表', 'url' => app_url('wlfightgroup/fightapp/fightindex', '', true));
		}

		if (p('groupon')) {
			$system['plugin']['list'][] = array('name' => '团购列表', 'url' => app_url('groupon/grouponapp/grouponlist', '', true));
		}

		if (p('bargain')) {
			$system['plugin']['list'][] = array('name' => '砍价列表', 'url' => app_url('bargain/bargain_app/bargainlist', '', true));
		}

		if (p('wlcoupon')) {
			$system['plugin']['list'][] = array('name' => '优惠劵', 'url' => app_url('wlcoupon/coupon_app/couponslist', '', true));
		}

		if (p('pocket')) {
			$system['plugin']['list'][] = array('name' => '掌上信息', 'url' => app_url('pocket/pocket/index', '', true));
			$system['plugin']['list'][] = array('name' => '我的贴子', 'url' => app_url('pocket/pocket/myinform', array('status' => 'all'), true));
			$system['plugin']['list'][] = array('name' => '发布帖子', 'url' => app_url('pocket/pocket/category', '', true));
		}

		if (p('wlsign')) {
			$system['plugin']['list'][] = array('name' => '签到页面', 'url' => app_url('wlsign/signapp/signindex', '', true));
			$system['plugin']['list'][] = array('name' => '签到记录', 'url' => app_url('wlsign/signapp/signrecord', '', true));
			$system['plugin']['list'][] = array('name' => '签到排行', 'url' => app_url('wlsign/signapp/signrank', '', true));
		}

		if (p('halfcard')) {
			$system['plugin']['list'][] = array('name' => '购卡入口', 'url' => app_url('halfcard/halfcardopen/open', array('type' => 1), true));
			$system['plugin']['list'][] = array('name' => '激活入口', 'url' => app_url('halfcard/halfcardopen/open', array('type' => 2), true));
		}

		if (p('consumption')) {
			$system['plugin']['list'][] = array('name' => '积分商城首页', 'url' => app_url('consumption/goods/goods_index', '', true));
			$system['plugin']['list'][] = array('name' => '积分兑换记录', 'url' => app_url('consumption/goods/recordlist', '', true));
		}

		if (p('headline')) {
			$system['plugin']['list'][] = array('name' => '头条列表', 'url' => h5_url('pages/mainPages/headline/index', '', true), 'page_path' => 'pages/mainPages/headline/index');
		}

		if (p('distribution')) {
			$system['distribution']['name'] = '分销链接';
			$system['distribution']['list'][] = array('name' => '分销商中心', 'url' => app_url('distribution/disappbase/index', '', true));
			$system['distribution']['list'][] = array('name' => '分销商海报', 'url' => app_url('distribution/disappbase/disposter', '', true));
			$system['distribution']['list'][] = array('name' => '分销客户管理', 'url' => app_url('distribution/disappbase/lowpeople', '', true));
			$system['distribution']['list'][] = array('name' => '分销团队管理', 'url' => app_url('distribution/disappbase/lowteam', '', true));
			$system['distribution']['list'][] = array('name' => '分销推广订单', 'url' => app_url('distribution/disappbase/disorder', '', true));
			$system['distribution']['list'][] = array('name' => '分销提现记录', 'url' => app_url('distribution/disappbase/apply', '', true));
			$system['distribution']['list'][] = array('name' => '分销推广商品', 'url' => app_url('distribution/disappbase/recommendgoods', '', true));
			$system['distribution']['list'][] = array('name' => '分销结算中心', 'url' => app_url('distribution/disappbase/moneylist', '', true));
		}

		$cate = pdo_getall(PDO_NAME . 'category_store', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'parentid' => 0), array('id', 'name'));

		foreach ($cate as $k => $v) {
			$list[] = array('name' => $v['name'], 'url' => app_url('store/merchant/index', array('cid' => $v['id']), true));
		}

		$cateUrl = array('name' => '选择分类', 'list' => $list);
		$shop_pageNum = 5;
		$shopWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND enabled = 1 ';
		$shop = pdo_fetchall('SELECT id,storename,logo,storehours FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE ' . $shopWhere . ' LIMIT 0,' . $shop_pageNum));
		$shop_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE ' . $shopWhere));

		foreach ($shop as $k => &$v) {
			$v['url'] = app_url('store/merchant/detail', array('id' => $v['id']), true);
			$v['logo'] = tomedia($v['logo']);
			$storehours = unserialize($v['storehours']);
			$v['storehours'] = $storehours['startTime'] . '-' . $storehours['endTime'];
			unset($v['id']);
		}

		$shopList['name'] = '选择店铺';
		$shopList['list'] = $shop;
		$rush_pageNum = 5;
		$rushWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status IN (1,2) ';
		$rush = pdo_fetchall(' SELECT id,name,thumb FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE ' . $rushWhere . ' LIMIT 0,' . $rush_pageNum));
		$rush_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE ' . $rushWhere));

		foreach ($rush as $k => &$v) {
			$v['url'] = app_url('rush/home/detail', array('id' => $v['id']), true);
			$v['logo'] = tomedia($v['thumb']);
			unset($v['id']);
		}

		$fightgroup_pageNum = 5;
		$fightgroupWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status = 1 ';
		$fightgroup = pdo_fetchall(' SELECT id,name,logo FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . (' WHERE ' . $fightgroupWhere . ' LIMIT 0,' . $fightgroup_pageNum));
		$fightgroup_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . (' WHERE ' . $fightgroupWhere));

		foreach ($fightgroup as $k => &$v) {
			$v['url'] = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $v['id']), true);
			$v['logo'] = tomedia($v['logo']);
			unset($v['id']);
		}

		$groupon_pageNum = 5;
		$grouponWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status IN (1,2)  ';
		$groupon = pdo_fetchall(' SELECT id,name,thumb FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE ' . $grouponWhere . ' LIMIT 0,' . $groupon_pageNum));
		$groupon_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE ' . $grouponWhere));

		foreach ($groupon as $k => &$v) {
			$v['url'] = app_url('groupon/grouponapp/groupondetail', array('cid' => $v['id']), true);
			$v['logo'] = tomedia($v['thumb']);
			unset($v['id']);
		}

		$coupon_pageNum = 5;
		$couponWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status = 1 ';
		$coupon = pdo_fetchall(' SELECT id,title,logo FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE ' . $couponWhere . ' LIMIT 0,' . $coupon_pageNum));
		$coupon_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE ' . $couponWhere));

		foreach ($coupon as $k => &$v) {
			$v['name'] = $v['title'];
			$v['url'] = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $v['id']), true);
			$v['logo'] = tomedia($v['logo']);
			unset($v['id']);
			unset($v['title']);
		}

		$bargain_pageNum = 5;
		$bargainWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status IN (1,2) ';
		$bargain = pdo_fetchall(' SELECT id,`name`,thumb FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE ' . $bargainWhere . ' LIMIT 0,' . $bargain_pageNum));
		$bargain_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE ' . $bargainWhere));

		foreach ($bargain as $k => &$v) {
			$v['url'] = app_url('bargain/bargain_app/bargaindetail', array('cid' => $v['id']), true);
			$v['logo'] = tomedia($v['thumb']);
			unset($v['id']);
		}

		$diyList = pdo_getall(PDO_NAME . 'diypage', array('type' => 1, 'page_class' => 1, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id', 'name'));

		if ($diyList) {
			$pageInfo['diy']['list'] = $diyList;
			$pageInfo['diy']['name'] = '自定义页面';

			foreach ($pageInfo['diy']['list'] as $k => &$v) {
				$v['url'] = app_url('diypage/diyhome/home', array('id' => $v['id']), true);
			}
		}

		$homeList = pdo_getall(PDO_NAME . 'diypage', array('type' => 2, 'page_class' => 1, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id', 'name'));

		if ($homeList) {
			$pageInfo['home']['name'] = '平台首页';
			$pageInfo['home']['list'] = $homeList;

			foreach ($pageInfo['home']['list'] as $k => &$v) {
				$v['url'] = app_url('dashboard/home/index', '', true);
			}
		}

		$rushList = pdo_getall(PDO_NAME . 'diypage', array('type' => 3, 'page_class' => 1, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id', 'name'));

		if ($rushList) {
			$pageInfo['rush']['name'] = '抢购首页';
			$pageInfo['rush']['list'] = $rushList;

			foreach ($pageInfo['rush']['list'] as $k => &$v) {
				$v['url'] = app_url('rush/home/index', '', true);
			}
		}

		$groupList = pdo_getall(PDO_NAME . 'diypage', array('type' => 4, 'page_class' => 1, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id', 'name'));

		if ($groupList) {
			$pageInfo['groupon']['name'] = '团购首页';
			$pageInfo['groupon']['list'] = $groupList;

			foreach ($pageInfo['groupon']['list'] as $k => &$v) {
				$v['url'] = app_url('groupon/grouponapp/grouponlist', '', true);
			}
		}

		$couponList = pdo_getall(PDO_NAME . 'diypage', array('type' => 5, 'page_class' => 1, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id', 'name'));

		if ($couponList) {
			$pageInfo['coupon']['name'] = '卡卷首页';
			$pageInfo['coupon']['list'] = $couponList;

			foreach ($pageInfo['coupon']['list'] as $k => &$v) {
				$v['url'] = app_url('wlcoupon/coupon_app/couponslist', array('id' => $v['id']), true);
			}
		}

		if (p('distribution')) {
			$system['distribution_vue']['name'] = 'vue页面链接 —— 开发中';
			$system['distribution_vue']['list'][] = array('name' => '分销链接', 'url' => h5_url('pages/subPages/dealer/apply/apply', '', true), 'page_path' => 'pages/subPages/dealer/index/index');
			$system['distribution_vue']['list'][] = array('name' => '首页链接', 'url' => h5_url('pages/mainPages/index/index', '', true), 'page_path' => 'pages/mainPages/index/index');
			$system['distribution_vue']['list'][] = array('name' => '搜索链接', 'url' => h5_url('pages/mainPages/search/search', '', true), 'page_path' => 'pages/mainPages/search/search');
		}

		include wl_template('utility/selecturl');
	}

	/**
     * Comment: 获取店铺信息
     * Author: zzw
     */
	public function getShop()
	{
		global $_W;
		global $_GPC;
		$search = $_GPC['search'] ? $_GPC['search'] : '';
		$page = $_GPC['page'];
		$pageNum = $_GPC['pageNum'];
		$pageClass = $_GPC['pageClass'];
		$limit = ' LIMIT ' . ($page * $pageNum - $pageNum) . ',' . $pageNum;
		$shopWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND enabled = 1 ';

		if ($search) {
			$shopWhere .= ' AND storename LIKE \'%' . $search . '%\' ';
		}

		$shop = pdo_fetchall('SELECT id,storename,logo,storehours FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE ' . $shopWhere . ' ' . $limit));
		$shop_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE ' . $shopWhere));

		foreach ($shop as $k => &$v) {
			if ($pageClass == 1) {
				$v['url'] = app_url('store/merchant/detail', array('id' => $v['id']), true);
			}
			else {
				$v['url'] = '/pagesGoodstore/storedetail/index?i=' . $v['id'];
				$v['url_type'] = 'navigate';
			}

			$v['logo'] = tomedia($v['logo']);
			$storehours = unserialize($v['storehours']);
			$v['storehours'] = $storehours['startTime'] . '-' . $storehours['endTime'];
			unset($v['id']);
		}

		$data['page'] = $page;
		$data['pageNum'] = $pageNum;
		$data['total'] = $shop_total;
		$data['list'] = $shop;
		$data['search'] = $search;
		wl_json(1, '商品分页信息', $data);
	}

	/**
     * Comment: 获取商品信息
     * Author: zzw
     */
	public function getGoods()
	{
		global $_W;
		global $_GPC;
		$search = $_GPC['search'] ? $_GPC['search'] : '';
		$page = $_GPC['page'];
		$pageNum = $_GPC['pageNum'];
		$limit = ' LIMIT ' . ($page * $pageNum - $pageNum) . ',' . $pageNum;
		$pageClass = $_GPC['pageClass'] ? $_GPC['pageClass'] : 1;
		$type = $_GPC['type'];
		$where = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' ';

		switch ($type) {
		case 1:
			if ($search) {
				$where .= ' AND name LIKE \'%' . $search . '%\' ';
			}

			$info = pdo_fetchall(' SELECT id,name,thumb FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE status IN (1,2) AND ' . $where . ' ' . $limit));
			$infoTotal = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE status IN (1,2) AND ' . $where));

			foreach ($info as $k => &$v) {
				if ($pageClass == 1) {
					$v['url'] = app_url('rush/home/detail', array('id' => $v['id']), true);
				}
				else {
					$v['url'] = '/pagesBuy/rushdetail/index?i=' . $v['id'] . '&c=rush';
					$v['url_type'] = 'navigate';
				}

				$v['logo'] = tomedia($v['thumb']);
				unset($v['id']);
			}

			break;

		case 2:
			if ($search) {
				$where .= ' AND name LIKE \'%' . $search . '%\' ';
			}

			$info = pdo_fetchall(' SELECT id,name,thumb FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE status IN (1,2) AND ' . $where . ' ' . $limit));
			$infoTotal = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE status IN (1,2) AND ' . $where));

			foreach ($info as $k => &$v) {
				if ($pageClass == 1) {
					$v['url'] = app_url('groupon/grouponapp/groupondetail', array('id' => $v['id']), true);
				}
				else {
					$v['url'] = '/pagesBuy/rushdetail/index?i=' . $v['id'] . '&c=groupon';
					$v['url_type'] = 'navigate';
				}

				$v['logo'] = tomedia($v['thumb']);
				unset($v['id']);
			}

			break;

		case 3:
			if ($search) {
				$where .= ' AND name LIKE \'%' . $search . '%\' ';
			}

			$info = pdo_fetchall(' SELECT id,name,logo FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . (' WHERE status = 1 AND ' . $where . ' ' . $limit));
			$infoTotal = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . (' WHERE status = 1 AND ' . $where));

			foreach ($info as $k => &$v) {
				if ($pageClass == 1) {
					$v['url'] = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $v['id']), true);
				}
				else {
					$v['url'] = '/pagesBuy/rushdetail/index?i=' . $v['id'] . '&c=fightgroup';
					$v['url_type'] = 'navigate';
				}

				$v['logo'] = tomedia($v['logo']);
				unset($v['id']);
			}

			break;

		case 5:
			if ($search) {
				$where .= ' AND title LIKE \'%' . $search . '%\' ';
			}

			$info = pdo_fetchall(' SELECT id,title,logo FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE status = 1 AND ' . $where . ' ' . $limit));
			$infoTotal = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE status = 1 AND ' . $where));

			foreach ($info as $k => &$v) {
				if ($pageClass == 1) {
					$v['url'] = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $v['id']), true);
				}
				else {
					$v['url'] = '/pagesBuy/rushdetail/index?i=' . $v['id'] . '&c=coupon';
					$v['url_type'] = 'navigate';
				}

				$v['name'] = $v['title'];
				$v['logo'] = tomedia($v['logo']);
				unset($v['id']);
				unset($v['title']);
			}

			break;

		case 6:
			if ($search) {
				$where .= ' AND name LIKE \'%' . $search . '%\' ';
			}

			$info = pdo_fetchall(' SELECT id,name,thumb FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE status IN (1,2) AND ' . $where . ' ' . $limit));
			$infoTotal = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE status IN (1,2) AND ' . $where));

			foreach ($info as $k => &$v) {
				if ($pageClass == 1) {
					$v['url'] = app_url('bargain/bargain_app/bargaindetail', array('cid' => $v['id']), true);
				}
				else {
					$v['url'] = '/pagesBuy/rushdetail/index?i=' . $v['id'] . '&c=bargain';
					$v['url_type'] = 'navigate';
				}

				$v['logo'] = tomedia($v['thumb']);
				unset($v['id']);
				unset($v['title']);
			}

			break;
		}

		$data['page'] = $page;
		$data['pageNum'] = $pageNum;
		$data['total'] = $infoTotal;
		$data['list'] = $info;
		$data['search'] = $search;
		wl_json(1, '商品信息', $data);
	}

	/**
     * Comment: 获取小程序地址
     * Author: zzw
     */
	public function getWeChatUrl()
	{
		global $_W;
		global $_GPC;
		$pageClass = $_GPC['pageClass'];
		$system = array(
			array(
				'name' => '系统页面',
				'list' => array(
					array('name' => '首页', 'url' => '/pages/index/index', 'url_type' => 'switchTab'),
					array('name' => '头条', 'url' => '/pages/headline/index', 'url_type' => 'switchTab'),
					array('name' => '好店', 'url' => '/pages/goodstore/index', 'url_type' => 'switchTab'),
					array('name' => '粉丝卡', 'url' => '/pages/fanscard/index', 'url_type' => 'switchTab'),
					array('name' => '我的', 'url' => '/pages/myself/index', 'url_type' => 'switchTab')
				)
			),
			array(
				'name' => '应用链接',
				'list' => array(
					array('name' => '全部订单', 'url' => '/pagesMyself/order/index', 'url_type' => 'navigate'),
					array('name' => '我的卡卷', 'url' => '/pagesMyself/coupon/index', 'url_type' => 'navigate'),
					array('name' => '核销记录', 'url' => '/pagesMyself/verific/index', 'url_type' => 'navigate')
				)
			)
		);
		$shop_pageNum = 5;
		$shopWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND enabled = 1';
		$shop = pdo_fetchall('SELECT id,storename,logo,storehours FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE ' . $shopWhere . ' LIMIT 0,' . $shop_pageNum));
		$shop_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE ' . $shopWhere));

		foreach ($shop as $k => &$v) {
			$v['url'] = '/pagesGoodstore/storedetail/index?i=' . $v['id'];
			$v['url_type'] = 'navigate';
			$v['logo'] = tomedia($v['logo']);
			$storehours = unserialize($v['storehours']);
			$v['storehours'] = $storehours['startTime'] . '-' . $storehours['endTime'];
			unset($v['id']);
		}

		$shopList['name'] = '选择店铺';
		$shopList['list'] = $shop;
		$rush_pageNum = 5;
		$rushWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status IN (1,2) ';
		$rush = pdo_fetchall(' SELECT id,name,thumb FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE ' . $rushWhere . ' LIMIT 0,' . $rush_pageNum));
		$rush_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE ' . $rushWhere));

		foreach ($rush as $k => &$v) {
			$v['url'] = '/pagesBuy/rushdetail/index?i=' . $v['id'] . '&c=rush';
			$v['url_type'] = 'navigate';
			$v['logo'] = tomedia($v['thumb']);
			unset($v['id']);
		}

		$fightgroup_pageNum = 5;
		$fightgroupWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status = 1 ';
		$fightgroup = pdo_fetchall(' SELECT id,name,logo FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . (' WHERE ' . $fightgroupWhere . ' LIMIT 0,' . $fightgroup_pageNum));
		$fightgroup_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . (' WHERE ' . $fightgroupWhere));

		foreach ($fightgroup as $k => &$v) {
			$v['url'] = '/pagesBuy/rushdetail/index?i=' . $v['id'] . '&c=fightgroup';
			$v['url_type'] = 'navigate';
			$v['logo'] = tomedia($v['logo']);
			unset($v['id']);
		}

		$groupon_pageNum = 5;
		$grouponWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status IN (1,2) ';
		$groupon = pdo_fetchall(' SELECT id,name,thumb FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE ' . $grouponWhere . ' LIMIT 0,' . $groupon_pageNum));
		$groupon_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE ' . $grouponWhere));

		foreach ($groupon as $k => &$v) {
			$v['url'] = '/pagesBuy/rushdetail/index?i=' . $v['id'] . '&c=groupon';
			$v['url_type'] = 'navigate';
			$v['logo'] = tomedia($v['thumb']);
			unset($v['id']);
		}

		$coupon_pageNum = 5;
		$couponWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status = 1 ';
		$coupon = pdo_fetchall(' SELECT id,title,logo FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE ' . $couponWhere . ' LIMIT 0,' . $coupon_pageNum));
		$coupon_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE ' . $couponWhere));

		foreach ($coupon as $k => &$v) {
			$v['url'] = '/pagesBuy/rushdetail/index?i=' . $v['id'] . '&c=coupon';
			$v['url_type'] = 'navigate';
			$v['name'] = $v['title'];
			$v['logo'] = tomedia($v['logo']);
			unset($v['id']);
			unset($v['title']);
		}

		$pageInfo = pdo_getall(PDO_NAME . 'diypage', array('type' => 1, 'page_class' => 2), array('id', 'name'));

		foreach ($pageInfo as $k => &$v) {
			$v['url'] = '/pagesIndex/diypage/index?i=' . $v['id'];
			$v['url_type'] = 'navigate';
		}

		include wl_template('utility/selecturl');
	}

	/**
     * Comment: 获取小图标信息
     * Author: zzw
     */
	public function comicon()
	{
		global $_W;
		global $_GPC;
		include wl_template('utility/selecticon');
	}

	/**
     * Comment: 根据状态获取商品信息
     * Author: zzw
     * @param $plugin  商品类型1=抢购  2=团购  3=拼团  5=优惠卷
     * @param $search  搜索内容
     * @return array
     */
	protected function getGoodsReturn($plugin, $search)
	{
		global $_W;
		$where = ' AND a.uniacid = ' . $_W['uniacid'] . ' ';

		if (0 < $_W['aid']) {
			$where .= ' AND a.aid = ' . $_W['aid'] . ' ';
		}

		switch ($plugin) {
		case 1:
			$goods = pdo_fetchall('SELECT a.id,REPLACE(\'table\',\'table\',\'rush\') as `plugin` FROM ' . tablename(PDO_NAME . 'rush_activity') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.sid = b.id WHERE a.status IN (1,2,3) ' . $where . ' AND b.storename != \'\' AND a.name LIKE \'%' . $search . '%\''));
			break;

		case 2:
			$goods = pdo_fetchall('SELECT a.id,REPLACE(\'table\',\'table\',\'groupon\') as `plugin` FROM ' . tablename(PDO_NAME . 'groupon_activity') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.sid = b.id WHERE a.status IN (1,2,3) ' . $where . ' AND b.storename != \'\' AND a.name LIKE \'%' . $search . '%\''));
			break;

		case 3:
			$goods = pdo_fetchall('SELECT a.id,REPLACE(\'table\',\'table\',\'wlfightgroup\') as `plugin` FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.merchantid = b.id WHERE a.status = 1 ' . $where . ' AND b.storename != \'\' AND a.name LIKE \'%' . $search . '%\''));
			break;

		case 4:
			$goods = pdo_fetchall('SELECT a.id,REPLACE(\'table\',\'table\',\'package\') as `plugin` FROM ' . tablename(PDO_NAME . 'package') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.merchantid = b.id WHERE a.status = 1 ' . $where . ' AND b.storename != \'\' AND a.title LIKE \'%' . $search . '%\''));
			break;

		case 5:
			$goods = pdo_fetchall('SELECT a.id,REPLACE(\'table\',\'table\',\'coupon\') as `plugin` FROM ' . tablename(PDO_NAME . 'couponlist') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.merchantid = b.id WHERE a.status = 1 ' . $where . ' AND b.storename != \'\' AND a.title LIKE \'%' . $search . '%\''));
			break;

		case 6:
			$goods = pdo_fetchall('SELECT a.id,REPLACE(\'table\',\'table\',\'halfcard\') as `plugin` FROM ' . tablename(PDO_NAME . 'halfcardlist') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.merchantid = b.id WHERE a.status = 1 ' . $where . ' AND b.storename != \'\' AND a.title LIKE \'%' . $search . '%\''));
			break;

		case 7:
			$goods = pdo_fetchall('SELECT a.id,REPLACE(\'table\',\'table\',\'bargain\') as `plugin` FROM ' . tablename(PDO_NAME . 'bargain_activity') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.sid = b.id WHERE a.status IN (1,2,3) ' . $where . ' AND b.storename != \'\' AND a.name LIKE \'%' . $search . '%\''));
			break;
		}

		return $goods;
	}

	/**
     * Comment: 获取全部商品信息
     * Author: zzw
     * Date: 2019/7/9 17:50
     */
	public function getWholeGoods()
	{
		global $_W;
		global $_GPC;
		$plugin = 0;
		$search = $_GPC['search'];
		$pageClass = 2;
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$pageNum = $_GPC['pageNum'] ? $_GPC['pageNum'] : 7;
		$geturl = $_GPC['geturl'] ? $_GPC['geturl'] : 0;
		$start = $page * $pageNum - $pageNum;
		$rush = self::getGoodsReturn(1, $search);
		$groupon = self::getGoodsReturn(2, $search);
		$wlfightgroup = self::getGoodsReturn(3, $search);
		$coupon = self::getGoodsReturn(5, $search);

		if ($pageClass != 2) {
			$bargain = self::getGoodsReturn(7, $search);
			$goods = array_merge($rush, $groupon, $wlfightgroup, $coupon, $bargain);
		}
		else {
			$goods = array_merge($rush, $groupon, $wlfightgroup, $coupon);
		}

		if (!$goods) {
			$popup = '暂无该类型商品';
			include wl_template('utility/selecetgoods');
			exit();
		}

		$totalPgae = ceil(count($goods) / $pageNum);
		$goods = array_slice($goods, $start, $pageNum);
		$initPlugin = $plugin;

		foreach ($goods as $k => &$v) {
			if ($initPlugin == 0) {
				switch ($v['plugin']) {
				case 'rush':
					$plugin = 1;
					break;

				case 'groupon':
					$plugin = 2;
					break;

				case 'wlfightgroup':
					$plugin = 3;
					break;

				case 'coupon':
					$plugin = 5;
					break;

				case 'bargain':
					$plugin = 7;
					break;
				}
			}

			$v = WeChat::getHomeGoods($plugin, $v['id']);
			if ($plugin == 1 || $plugin == 2) {
				$v['discount_price'] = sprintf('%.2f', $v['price'] - $v['vipprice']);
				unset($v['vipprice']);
			}

			if ($geturl == 1) {
				switch ($plugin) {
				case 1:
					$v['detail_url'] = app_url('rush/home/detail', array('id' => $v['id']));
					break;

				case 2:
					$v['detail_url'] = app_url('groupon/grouponapp/groupondetail', array('cid' => $v['id']));
					break;

				case 3:
					$v['detail_url'] = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $v['id']));
					break;

				case 5:
					$v['detail_url'] = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $v['id']));
					break;

				case 7:
					$v['detail_url'] = app_url('bargain/bargain_app/bargaindetail', array('cid' => $v['id']));
					break;
				}
			}
		}

		include wl_template('utility/selecetgoods');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
