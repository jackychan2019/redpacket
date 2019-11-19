<?php
//dezend by http://www.sucaihuo.com/
class Diy_WeliamController
{
	public function editPage()
	{
		global $_W;
		global $_GPC;
		$back = $_GPC['back_url'];
		$pageClass = $_GPC['page_class'];
		$id = $_GPC['id'] ? $_GPC['id'] : -1;
		$tid = $_GPC['tid'] ? $_GPC['tid'] : -1;
		$type = $_GPC['type'];
		$result = Diy::verify($id, $type, $tid);
		extract($result);
		$common = Diy::getCommon($pageClass);

		if (empty($_W['aid'])) {
			$diyadvs = pdo_getall(PDO_NAME . 'diypage_adv', array('uniacid' => $_W['uniacid'], 'adv_class' => $pageClass));
			$diymenu = pdo_getall(PDO_NAME . 'diypage_menu', array('uniacid' => $_W['uniacid'], 'menu_class' => $pageClass));
			$category = pdo_getall(PDO_NAME . 'diypage_temp_cate', array('uniacid' => $_W['uniacid']), array('id', 'name'));
		}
		else {
			$diyadvs = pdo_getall(PDO_NAME . 'diypage_adv', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'adv_class' => $pageClass));
			$diymenu = pdo_getall(PDO_NAME . 'diypage_menu', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'menu_class' => $pageClass));
			$category = pdo_getall(PDO_NAME . 'diypage_temp_cate', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid']), array('id', 'name'));
		}

		include wl_template('diy/page_edit');
	}

	public function savePage()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$data = json_decode(base64_decode($_GPC['data']), true);
		$diypage = array('data' => base64_encode(json_encode($data, JSON_UNESCAPED_UNICODE)), 'name' => $data['page']['name'], 'type' => $data['page']['type'], 'lastedittime' => time(), 'page_class' => $data['page']['pageClass']);

		if (0 < $id) {
			pdo_update(PDO_NAME . 'diypage', $diypage, array('id' => $id));
		}
		else {
			$diypage['uniacid'] = $_W['uniacid'];
			$diypage['aid'] = $_W['aid'];
			$diypage['createtime'] = time();

			if (empty($diypage['aid'])) {
				$diypage['is_public'] = 1;
			}

			pdo_insert(PDO_NAME . 'diypage', $diypage);
			$id = pdo_insertid();
		}

		if ($data['page']['pageClass'] == 1) {
			$backUrl = 'diypage/diy/pagelist';
		}
		else {
			$backUrl = 'chosen/diy/pagelist';
		}

		wl_json(1, '操作成功', array('id' => $id, 'url' => web_url('diy/diy/editPage', array('id' => $id, 'type' => $data['page']['type'], 'page_type' => 'page', 'page_class' => $data['page']['pageClass'], 'back_url' => $backUrl))));
	}

	public function delPage()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$result = pdo_delete(PDO_NAME . 'diypage', array('id' => $id));

		if ($result) {
			show_json(1);
		}
		else {
			show_json(0, '删除失败');
		}
	}

	public function saveMenu()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$data = $_GPC['menu'];
		$menuClass = intval($_GPC['menu_class']);
		$menudata = array('name' => $data['name'], 'data' => base64_encode(json_encode($data)), 'lastedittime' => time());

		if (!empty($id)) {
			pdo_update(PDO_NAME . 'diypage_menu', $menudata, array('id' => $id, 'uniacid' => $_W['uniacid']));
		}
		else {
			$menudata['uniacid'] = $_W['uniacid'];
			$menudata['aid'] = $_W['aid'];

			if (empty($menudata['aid'])) {
				$menudata['is_public'] = 1;
			}

			$menudata['createtime'] = time();
			$menudata['menu_class'] = $menuClass;
			pdo_insert(PDO_NAME . 'diypage_menu', $menudata);
			$id = pdo_insertid();
		}

		if ($menuClass == 1) {
			$Url = 'diypage/diy/menuEdit';
		}
		else {
			$Url = 'chosen/diy/menuEdit';
		}

		wl_json(1, '保存成功', web_url($Url, array('id' => $id, 'menu_class' => $menuClass)));
	}

	public function delMenu()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			show_json(0, '参数错误，请刷新重试！');
		}
		else {
			pdo_delete(PDO_NAME . 'diypage_menu', array('id' => $id, 'aid' => intval($_W['aid'])));
			show_json(1);
		}
	}

	public function saveAdv()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$advClass = $_GPC['adv_class'];
		$data = $_GPC['advs'];
		$advsdata = array('name' => $data['name'], 'data' => base64_encode(json_encode($data)), 'lastedittime' => time(), 'type' => 1, 'aid' => intval($_W['aid']));

		if (empty($advsdata['aid'])) {
			$advsdata['is_public'] = 1;
		}

		if (!empty($id)) {
			pdo_update(PDO_NAME . 'diypage_adv', $advsdata, array('id' => $id, 'uniacid' => $_W['uniacid']));
		}
		else {
			$advsdata['uniacid'] = $_W['uniacid'];
			$advsdata['createtime'] = time();
			$advsdata['adv_class'] = $advClass;
			pdo_insert(PDO_NAME . 'diypage_adv', $advsdata);
			$id = pdo_insertid();
		}

		if ($advClass == 1) {
			$Url = 'diypage/diy/advEdit';
		}
		else {
			$Url = 'chosen/diy/advEdit';
		}

		wl_json(1, '保存成功', web_url($Url, array('id' => $id, 'adv_class' => $advClass)));
	}

	public function delAdv()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			show_json(0, '参数错误，请刷新重试！');
		}
		else {
			pdo_delete(PDO_NAME . 'diypage_adv', array('id' => $id, 'aid' => intval($_W['aid'])));
			show_json(1);
		}
	}

	public function saveTemp()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];
		$cate = $_GPC['cate'];
		$name = $_GPC['name'];
		$data = $_GPC['data'];
		$pageImg = $_GPC['pageImg'];
		$imageName = 'page_image' . time() . rand(1000, 9999) . '.png';
		$imageName = 'images/' . MODULE_NAME . '/' . $imageName;
		$fullName = PATH_ATTACHMENT . $imageName;

		if (strstr($pageImg, ',')) {
			$pageImg = explode(',', $pageImg);
			$pageImg = $pageImg[1];
		}

		file_put_contents($fullName, base64_decode($pageImg));
		$temp = array('type' => intval($type), 'cate' => intval($cate), 'name' => trim($name), 'preview' => trim($imageName), 'data' => base64_encode(json_encode($data, JSON_UNESCAPED_UNICODE)), 'page_class' => $data['page']['pageClass'], 'aid' => $_W['aid'], 'uniacid' => $_W['uniacid']);
		pdo_insert(PDO_NAME . 'diypage_temp', $temp);
		$id = pdo_insertid();
		wl_json(1, '操作成功');
	}

	public function delTemp()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$result = pdo_delete(PDO_NAME . 'diypage_temp', array('id' => $id));

		if ($result) {
			show_json(0);
		}
		else {
			show_json(1, '删除失败');
		}
	}

	public function saveCate()
	{
		global $_W;
		global $_GPC;
		$name = trim($_GPC['name']);
		$cateClass = $_GPC['cateClass'];

		if (empty($name)) {
			show_json(0, '分类名称为空！');
		}

		$result = pdo_insert(PDO_NAME . 'diypage_temp_cate', array('name' => $name, 'uniacid' => $_W['uniacid'], 'aid' => intval($_W['aid']), 'cate_class' => $cateClass));

		if ($result) {
			show_json(1);
		}
		else {
			show_json(1, '添加失败');
		}
	}

	public function editCate()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$name = trim($_GPC['value']);
		$item = pdo_fetch('SELECT id, name, uniacid FROM ' . tablename(PDO_NAME . 'diypage_temp_cate') . ' WHERE id=:id and aid=:aid and uniacid=:uniacid ', array(':aid' => intval($_W['aid']), ':uniacid' => $_W['uniacid'], ':id' => $id));

		if (!empty($item)) {
			pdo_update(PDO_NAME . 'diypage_temp_cate', array('name' => $name), array('id' => $id, 'aid' => intval($_W['aid'])));
			show_json(1, '分类名称编辑成功！');
		}
		else {
			show_json(0, '分类不存在,请刷新页面重试！');
		}
	}

	public function delCate()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			show_json(0, '参数错误，请刷新重试！');
		}

		$item = pdo_fetch('SELECT id, name, uniacid FROM ' . tablename(PDO_NAME . 'diypage_temp_cate') . ' WHERE id=:id and aid=:aid and uniacid=:uniacid ', array(':aid' => intval($_W['aid']), ':uniacid' => $_W['uniacid'], ':id' => $id));

		if (!empty($item)) {
			pdo_delete(PDO_NAME . 'diypage_temp_cate', array('id' => $id, 'aid' => intval($_W['aid'])));
		}

		show_json(1);
	}

	public function getImgInfo()
	{
		global $_W;
		global $_GPC;
		$imgUrl = $_GPC['img_url'];

		foreach ($imgUrl as $k => $v) {
			$imgResources = file_get_contents($v);
			$setting = $_W['setting']['upload']['image'];
			$setting['folder'] = 'images/' . MODULE_NAME;
			$imageName = date('Y-m-dHisw') . time() . rand(1000, 9999) . '.png';
			$imageName = $setting['folder'] . '/' . $imageName;
			$fullName = PATH_ATTACHMENT . $imageName;
			file_put_contents($fullName, $imgResources);
			$image_info = getimagesize($fullName);
			$image_data = fread(fopen($fullName, 'r'), filesize($fullName));
			$base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
			$base64[$k] = $base64_image;
			unlink($fullName);
		}

		wl_json(1, '处理后的图片', $base64);
	}

	public function getGoodsInfo()
	{
		global $_W;
		global $_GPC;
		$plugin = $_GPC['plugin'];
		$search = $_GPC['search'];
		$pageClass = $_GPC['page_class'];
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$pageNum = $_GPC['pageNum'] ? $_GPC['pageNum'] : 8;
		$geturl = $_GPC['geturl'] ? $_GPC['geturl'] : 0;
		$start = $page * $pageNum - $pageNum;

		if ($plugin == 0) {
			$rush = self::getGoods(1, $search);
			$groupon = self::getGoods(2, $search);
			$wlfightgroup = self::getGoods(3, $search);
			$coupon = self::getGoods(5, $search);

			if ($pageClass != 2) {
				$bargain = self::getGoods(7, $search);
				$goods = array_merge($rush, $groupon, $wlfightgroup, $coupon, $bargain);
			}
			else {
				$goods = array_merge($rush, $groupon, $wlfightgroup, $coupon);
			}
		}
		else {
			$goods = self::getGoods($plugin, $search, $start, $pageNum);
		}

		if (!$goods) {
			wl_json(0, '暂无该类型商品!');
		}

		$data['page_number'] = ceil(count($goods) / $pageNum);
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

		$data['goods'] = $goods;
		$data['page'] = $page;

		if ($goods) {
			wl_json(1, '获取商品信息', $data);
		}
		else {
			wl_json(0, '获取失败');
		}
	}

	public function getHeadline()
	{
		global $_W;
		global $_GPC;
		$search = $_GPC['search'];
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$pageNum = $_GPC['pageNum'] ? $_GPC['pageNum'] : 10;
		$start = $page * $pageNum - $pageNum;
		$where = ' uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' ';

		if ($search) {
			$where .= ' AND (title LIKE \'%' . $search . '%\' || author LIKE \'%' . $search . '%\') ';
		}

		$list = pdo_fetchall('SELECT id,title,summary,display_img,author,author_img,browse,one_id,two_id FROM ' . tablename(PDO_NAME . 'headline_content') . (' WHERE ' . $where . ' ORDER BY release_time DESC '));
		$data['page_number'] = ceil(count($list) / $pageNum);
		$list = array_slice($list, $start, $pageNum);

		foreach ($list as $k => &$v) {
			$v['display_img'] = tomedia($v['display_img']);
			$v['author_img'] = tomedia($v['author_img']);
			$v['one_name'] = implode(pdo_get(PDO_NAME . 'headline_class', array('id' => $v['one_id']), array('name')));
			$v['two_name'] = implode(pdo_get(PDO_NAME . 'headline_class', array('id' => $v['two_id']), array('name')));
			unset($v['one_id']);
			unset($v['two_id']);
		}

		$data['list'] = $list;
		$data['page'] = $page;

		if ($list) {
			wl_json(1, '获取商品信息', $data);
		}
		else {
			wl_json(0, '获取失败');
		}
	}

	public function getShop()
	{
		global $_W;
		global $_GPC;
		$search = $_GPC['search'];
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$pageNum = $_GPC['pageNum'] ? $_GPC['pageNum'] : 10;
		$start = $page * $pageNum - $pageNum;
		$where = ' uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 2 AND enabled = 1 ';

		if ($search) {
			$where .= ' AND storename LIKE \'%' . $search . '%\' ';
		}

		$shopList = pdo_fetchall('SELECT id,storename,logo,address,location,storehours,pv,score,onelevel,twolevel FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE ' . $where));

		foreach ($shopList as $shop_key => &$shop_val) {
			$shop_val['oneType'] = pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $shop_val['onelevel']), 'name');
			$shop_val['twoType'] = pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $shop_val['twolevel']), 'name');
			$shop_val['salesVolume'] = Store::getShopSales($shop_val['id'], 1, 0);
			$shop_val['score'] = sprintf('%.1f', $shop_val['score']);
			unset($shop_val['onelevel']);
			unset($shop_val['twolevel']);
		}

		$shopList = Store::getstores($shopList, 0, 0, 4);
		$data['page_number'] = ceil(count($shopList) / $pageNum);
		$shopList = array_slice($shopList, $start, $pageNum);
		$shopList = WeChat::getStoreList($shopList);
		$data['list'] = $shopList;
		$data['page'] = $page;

		if ($shopList) {
			wl_json(1, '获取商品信息', $data);
		}
		else {
			wl_json(0, '获取失败');
		}
	}

	public function previewPage()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$url = app_url('diypage/diyhome/home', array('aid' => $_W['aid'], 'id' => $id));
		include wl_template('diy/preview');
	}

	protected function getGoods($plugin, $search)
	{
		global $_W;
		$where = ' AND a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'] . ' ';

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

	public function getOption()
	{
		$configInfo = array(
			array('status' => 1, 'name' => '商家', 'plugin' => '', 'nickname' => '商家', 'type' => 'store'),
			array('status' => 1, 'name' => '抢购', 'plugin' => 'rush', 'nickname' => '抢购', 'type' => 'rush'),
			array('status' => 1, 'name' => '卡券', 'plugin' => 'wlcoupon', 'nickname' => '卡券', 'type' => 'coupon'),
			array('status' => 1, 'name' => '特权', 'plugin' => 'halfcard', 'nickname' => '特权', 'type' => 'halfcard'),
			array('status' => 1, 'name' => '拼团', 'plugin' => 'wlfightgroup', 'nickname' => '拼团', 'type' => 'fight'),
			array('status' => 1, 'name' => '同城', 'plugin' => 'pocket', 'nickname' => '同城', 'type' => 'pocket'),
			array('status' => 1, 'name' => '团购', 'plugin' => 'groupon', 'nickname' => '团购', 'type' => 'goupon'),
			array('status' => 1, 'name' => '砍价', 'plugin' => 'bargain', 'nickname' => '砍价', 'type' => 'bargain'),
			array('status' => 1, 'name' => '积分', 'plugin' => '', 'nickname' => '积分', 'type' => 'consumption'),
			array('status' => 1, 'name' => '礼包', 'plugin' => 'halfcard', 'nickname' => '礼包', 'type' => 'package')
		);
		$data = array();

		foreach ($configInfo as $k => &$v) {
			if ($v['plugin']) {
				if (!p($v['plugin'])) {
					unset($configInfo[$k]);
					continue;
				}
			}

			unset($v['plugin']);
			$name = 'C012345678910' . (count($data) + 1);
			$v['sort'] = count($data) + 1;
			$data[$name] = $v;
		}

		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
}

defined('IN_IA') || exit('Access Denied');

?>
