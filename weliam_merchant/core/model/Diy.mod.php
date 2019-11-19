<?php
//dezend by http://www.sucaihuo.com/
class Diy
{
	/**
     * Comment: 获取页面列表
     * Author: zzw
     * @param $pageName 页面名称，搜索页面时使用
     * @param $pindex   页码，查询第几页的内容
     * @param $pageClass  页面所属:1=公众号页面   2=小程序页面
     * @return mixed
     */
	static public function pageList($pageName, $pindex, $pageClass)
	{
		global $_W;
		global $_GPC;
		$psize = 10;
		$where = ' (aid = ' . $_W['aid'] . ' OR is_public = 1) AND uniacid = ' . $_W['uniacid'] . ' AND page_class = ' . $pageClass;

		if ($_GPC['page_type']) {
			$where .= ' AND type = ' . $_GPC['page_type'];
		}

		if ($pageName) {
			$where .= ' AND `name` LIKE \'%' . $pageName . '%\'';
		}

		$sql = 'SELECT * FROM ' . tablename(PDO_NAME . 'diypage') . (' WHERE ' . $where . ' ORDER BY lastedittime DESC');
		$sql .= ' limit ' . ($pindex - 1) * $psize . ',' . $psize;
		$list = pdo_fetchall($sql);
		$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename(PDO_NAME . 'diypage') . (' WHERE ' . $where));
		$pager = pagination($total, $pindex, $psize);
		$data['list'] = $list;
		$data['pager'] = $pager;
		return $data;
	}

	/**
     * Comment: 获取菜单列表的信息
     * Author: zzw
     * @param $name    菜单名称，搜索菜单时使用
     * @param $pindex  页码，查询第几页的内容
     * @return mixed
     */
	static public function menuList($name, $pindex, $menuClass)
	{
		global $_W;
		$psize = 10;
		$where = ' (aid = ' . $_W['aid'] . ' OR is_public = 1) AND uniacid = ' . $_W['uniacid'] . ' AND menu_class = ' . $menuClass . ' ';

		if ($name) {
			$where .= ' AND name LIKE \'%' . $name . '%\'';
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'diypage_menu') . ' WHERE ' . $where . ' order by id desc limit ' . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'diypage_menu') . ' WHERE ' . $where);
		$pager = pagination($total, $pindex, $psize);
		$data['list'] = $list;
		$data['pager'] = $pager;
		return $data;
	}

	/**
     * Comment: 获取广告列表的信息
     * Author: zzw
     */
	static public function advList($name, $pindex, $advClass)
	{
		global $_W;
		$where = ' (aid = ' . $_W['aid'] . ' OR is_public = 1) AND uniacid = ' . $_W['uniacid'] . ' AND adv_class = ' . $advClass . ' ';

		if ($name) {
			$where .= ' AND name LIKE \'%' . $name . '%\' ';
		}

		$psize = 10;
		$list = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'diypage_adv') . ' WHERE ' . $where . ' ORDER BY id DESC limit ' . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'diypage_adv') . ' WHERE ' . $where);
		$pager = pagination($total, $pindex, $psize);
		$data['list'] = $list;
		$data['pager'] = $pager;
		return $data;
	}

	/**
     * Comment: 获取模板列表
     * Author: zzw
     * @param $cate //分类id
     * @param $tempName //模板名称
     * @param $pindex //页码
     * @return bool
     */
	static public function tempList($cate, $tempName, $pindex, $pageClass)
	{
		global $_W;
		$psize = 8;
		$where = ' (uniacid = ' . $_W['uniacid'] . ' AND page_class = ' . $pageClass . ') OR (uniacid = 0 AND page_class = 0)';

		if ($tempName) {
			$where .= ' AND `name` LIKE \'%' . $tempName . '%\'';
		}

		if (0 <= intval($cate) && 0 < strlen($cate)) {
			$where .= ' AND cate = ' . $cate . ' ';
		}

		$sql = 'SELECT id,uniacid,aid,`name`,`type`,preview,page_class FROM ' . tablename(PDO_NAME . 'diypage_temp') . (' WHERE ' . $where . ' ORDER BY id DESC');
		$sql .= ' limit ' . ($pindex - 1) * $psize . ',' . $psize;
		$list = pdo_fetchall($sql);
		$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename(PDO_NAME . 'diypage_temp') . (' WHERE ' . $where));
		$pager = pagination($total, $pindex, $psize);
		$allpagetype = self::getPageType();
		$category = pdo_fetchall('SELECT id,name FROM ' . tablename(PDO_NAME . 'diypage_temp_cate') . (' WHERE aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND cate_class = ' . $pageClass . '  ORDER BY id ASC '));
		$data['list'] = $list;
		$data['pager'] = $pager;
		$data['allpagetype'] = $allpagetype;
		$data['category'] = $category;
		return $data;
	}

	/**
     * Comment: 获取模板分类列表
     * Author: zzw
     */
	static public function cateList($pindex, $keyword, $pageClass)
	{
		global $_W;
		$psize = 10;
		$where = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND cate_class = ' . $pageClass;

		if (!empty($keyword)) {
			$where .= ' AND name LIKE \'%' . $keyword . '%\' ';
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'diypage_temp_cate') . (' WHERE ' . $where . ' ORDER BY id DESC LIMIT ') . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn('SELECT COUNT(1) FROM ' . tablename(PDO_NAME . 'diypage_temp_cate') . (' WHERE ' . $where));
		$pager = pagination($total, $pindex, $psize);
		$data['list'] = $list;
		$data['pager'] = $pager;
		return $data;
	}

	/**
     * Comment: 信息验证
     * Author: zzw
     */
	static public function verify($id, $type, $tid)
	{
		global $_W;
		global $_GPC;
		$result = array('id' => $id, 'type' => $type);
		if ($id < 0 && $tid < 0) {
			if (!empty($type)) {
				$getpagetype = self::getPageType($type);
				$result['pagetype'] = $getpagetype['pagetype'];
			}
		}
		else {
			if (0 <= $id || 0 <= $tid) {
				if (0 <= $tid) {
					$info = pdo_get(PDO_NAME . 'diypage_temp', array('id' => $tid), array('id', 'data'));
				}
				else {
					$info = pdo_get(PDO_NAME . 'diypage', array('id' => $id), array('id', 'data'));
				}

				$info['data'] = base64_decode($info['data']);
				$result['data'] = json_decode($info['data'], true);
				$getpagetype = self::getPageType($type);
				$result['pagetype'] = $getpagetype['pagetype'];

				if ($result['data']['page']['title'] == '圣诞主题首页') {
					unset($result['data']['items']['M1542621275922']);
				}
			}
		}

		return $result;
	}

	/**
     * 获取页面类型
     * @param null $type
     * @return array|mixed
     */
	static public function getPageType($type = NULL)
	{
		$pagetype = array(
			1  => array('name' => '自定义', 'pagetype' => 'diy', 'class' => ''),
			2  => array('name' => '平台首页', 'pagetype' => 'sys', 'class' => 'success'),
			3  => array('name' => '会员中心', 'pagetype' => 'sys', 'class' => 'primary'),
			4  => array('name' => '分销中心', 'pagetype' => 'plu', 'class' => 'warning'),
			5  => array('name' => '商品详情页', 'pagetype' => 'sys', 'class' => 'danger'),
			6  => array('name' => '积分商城', 'pagetype' => 'plu', 'class' => 'info'),
			7  => array('name' => '整点秒杀', 'pagetype' => 'plu', 'class' => 'danger'),
			8  => array('name' => '兑换中心', 'pagetype' => 'plu', 'class' => 'success'),
			9  => array('name' => '快速购买', 'pagetype' => 'sys', 'class' => 'warning'),
			99 => array('name' => '公用模块', 'pagetype' => 'mod', 'class' => '')
		);

		if (!empty($type)) {
			return $pagetype[$type];
		}

		return $pagetype;
	}

	/**
     * Comment: 获取菜单
     * @param $id
     * @return bool
     */
	static public function getMenu($id, $datatype = false)
	{
		global $_W;
		$menu = pdo_get(PDO_NAME . 'diypage_menu', array('id' => $id));

		if (!empty($menu)) {
			$menu['data'] = json_decode(base64_decode($menu['data']), true);

			foreach ($menu['data']['data'] as $key => &$val) {
				$val['imgurl'] = tomedia($val['imgurl']);
			}

			if ($datatype) {
			}
		}

		return $menu;
	}

	/**
     * Comment: 获取广告
     * @param $id
     * @return bool
     */
	static public function getAdv($id, $datatype = false)
	{
		global $_W;
		$adv = pdo_get(PDO_NAME . 'diypage_adv', array('id' => $id));

		if (!empty($adv)) {
			$adv['data'] = json_decode(base64_decode($adv['data']), true);

			if ($datatype) {
				foreach ($adv['data']['data'] as $k => &$v) {
					$v['imgurl'] = tomedia($v['imgurl']);
				}
			}
		}

		return $adv;
	}

	/**
     * Comment: 获取页面配置
     * @param $id
     * @return bool
     */
	static public function getPage($id, $datatype = false)
	{
		global $_W;
		$page = pdo_get(PDO_NAME . 'diypage', array('id' => $id));

		if (!empty($page)) {
			$page['data'] = json_decode(base64_decode($page['data']), true);

			if ($datatype) {
				$page['data']['items'] = self::getPageInfo($page['data']['items'], $page['page_class'], $id, $page['type']);
			}
		}

		return $page;
	}

	/**
     * Comment: 完善页面的配置信息
     * Author: zzw
     * @param $info  原始的页面配置信息
     * @param $pageClass 1=公众号页面   2=小程序页面
     * @return mixed
     */
	static public function getPageInfo($info, $pageClass, $id, $type)
	{
		global $_W;
		global $_GPC;
		$lng = $_GPC['lng'];
		$lat = $_GPC['lat'];
		$goodsArr = array('rush_goods', 'groupon_goods', 'coupon_goods', 'fightgroup_goods', 'packages', 'discount_card', 'bargain_goods');

		foreach ($info as $k => &$v) {
			if ($v['data']) {
				foreach ($v['data'] as $img_key => &$img_val) {
					$img_val['imgurl'] = tomedia($img_val['imgurl']);
				}
			}

			$v['currentTime'] = time();
			if ($v['group_name'] && in_array($v['group_name'], $goodsArr) || $v['group_name'] == 'public_goods') {
				if ($v['params']['type'] != 1 && $v['plugin'] != 0) {
					$v['data'] = self::getDiyGoods($v['params'], $v['plugin'], $type);
				}

				$publicPlugin = $v['plugin'];

				foreach ($v['data'] as $goods_key => $goods_val) {
					if (!$goods_val['id']) {
						unset($v['data'][$goods_key]);
						continue;
					}

					if ($publicPlugin == 0) {
						switch ($goods_val['plugin']) {
						case 'rush':
							$v['plugin'] = 1;
							break;

						case 'groupon':
							$v['plugin'] = 2;
							break;

						case 'wlfightgroup':
							$v['plugin'] = 3;
							break;

						case 'coupon':
							$v['plugin'] = 5;
							break;

						case 'bargain':
							$v['plugin'] = 7;
							break;
						}
					}

					$v['data'][$goods_key] = WeChat::getHomeGoods($v['plugin'], $goods_val['id']);

					if ($pageClass == 1) {
						switch ($v['plugin']) {
						case 1:
							$url = app_url('rush/home/detail', array('id' => $goods_val['id']), true);
							break;

						case 2:
							$url = app_url('groupon/grouponapp/groupondetail', array('cid' => $goods_val['id']), true);
							break;

						case 3:
							$url = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $goods_val['id']), true);
							break;

						case 4:
							$url = app_url('halfcard/halfcard_app/packagedetail', array('id' => $goods_val['id']));
							break;

						case 5:
							$url = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $goods_val['id']), true);
							break;

						case 6:
							$url = app_url('order/paybill/paycheck', array('id' => $goods_val['sid']), true);
							unset($v['data'][$goods_key]['card']);
							break;

						case 7:
							$url = app_url('bargain/bargain_app/bargaindetail', array('cid' => $goods_val['id']), true);
							break;
						}
					}
					else {
						if ($pageClass == 2) {
							switch ($v['plugin']) {
							case 1:
								$url = '/pages/mainPages/goodsdetail/index?i=' . $goods_val['id'] . '&c=rush';
								break;

							case 2:
								$url = '/pages/mainPages/goodsdetail/index?i=' . $goods_val['id'] . '&c=groupon';
								break;

							case 3:
								$url = '/pages/mainPages/goodsdetail/index?i=' . $goods_val['id'] . '&c=fightgroup';
								break;

							case 5:
								$url = '/pages/mainPages/goodsdetail/index?i=' . $goods_val['id'] . '&c=coupon';
								break;
							}

							$v['data'][$goods_key]['url_type'] = 'navigate';
						}
					}

					$v['data'][$goods_key]['url'] = $url;
					if ($v['plugin'] == 1 || $v['plugin'] == 2) {
						$time = time();
						$v['data'][$goods_key]['goods_state'] = 0;

						if ($time < $v['data'][$goods_key]['starttime']) {
							$v['data'][$goods_key]['goods_state'] = 1;
						}
						else {
							if ($v['data'][$goods_key]['endtime'] < $time) {
								$v['data'][$goods_key]['goods_state'] = 2;
							}
						}
					}

					if ($v['plugin'] == 1 || $v['plugin'] == 2) {
						$discount_price = $v['data'][$goods_key]['price'] - $v['data'][$goods_key]['vipprice'];
						$v['data'][$goods_key]['discount_price'] = sprintf('%.2f', $discount_price);
					}

					if ($v['data'][$goods_key]['sid']) {
						$v['data'][$goods_key]['shop_url'] = app_url('store/merchant/detail', array('id' => $v['data'][$goods_key]['sid']), true);
						$v['data'][$goods_key]['distance'] = Store::shopLocation($v['data'][$goods_key]['sid'], $lng, $lat);
					}
				}

				if (count($v['data']) == 0) {
					unset($info[$k]);
				}

				$v['data'] = array_values($v['data']);
			}
			else {
				if ($v['group_name'] && $v['group_name'] == 'headline') {
					foreach ($v['data'] as $line_key => $line_val) {
						$v['data'][$line_key] = WeChat::getHomeLine($line_val['id']);
					}
				}
				else {
					if ($v['id'] == 'magic_cube') {
						$min_w = get_object_vars($v['style'])['min_w'];
						$height = 0;

						foreach ($v['data'] as $cube_key => &$cube_val) {
							if ($height < $cube_val['top'] + $cube_val['height']) {
								$height = $cube_val['top'] + $cube_val['height'];
							}
						}

						$v['height'] = $height;
					}

					if ($v['group_name'] == 'notice' || $v['id'] == 'notice') {
						$notice = pdo_getall(PDO_NAME . 'notice', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'enabled' => 1), array('id', 'title', 'link'));

						foreach ($notice as $notice_k => &$notice_v) {
							if (!$notice_v['link']) {
								if ($pageClass == 1) {
									$notice_v['link'] = app_url('dashboard/home/noticedetail', array('id' => $notice_v['id']), true);
								}
								else {
									if ($pageClass == 2) {
										$notice_v['link'] = '小程序公告详情页面链接';
									}
								}
							}
						}

						$v['data'] = $notice;
					}

					if ($v['id'] == 'community') {
						$v['params']['imgUrl'] = tomedia($v['params']['imgUrl']);
						$v['params']['qrcodeUrl'] = tomedia($v['params']['qrcodeUrl']);
						$v['params']['community'] = false;
					}

					if ($v['id'] == 'search2' && $pageClass == 1) {
						if ($type == 1) {
							$areaUrl = app_url('area/region/select_region', array('backurl' => app_url('diypage/diyhome/home', array('aid' => $_W['aid'], 'id' => $id))));
						}
						else {
							if ($type == 2) {
								$areaUrl = app_url('area/region/select_region', array('backurl' => app_url('dashboard/home/index', array('id' => $id))));
							}
						}

						$v['params']['url'] = $areaUrl;
						$v['params']['areaname'] = $_W['areaname'];
					}

					if (($v['group_name'] == 'banner' || $v['id'] == 'pictures') && $pageClass == 2) {
						$max_height = 0;
						$max_width = 0;

						foreach ($v['data'] as $banner_k => $banner_v) {
							$imgInfo = Tools::createImage($banner_v['imgurl']);
							$height = imagesy($imgInfo);
							if ($max_height < $height && 0 < $height) {
								$max_height = $height;
								$max_width = imagesx($imgInfo);
							}

							unset($imgInfo);
						}

						$v['max_height'] = $max_height;
						$v['max_width'] = $max_width;
					}

					if ($v['group_name'] == 'shop' || $v['id'] == 'shop') {
						if ($v['params']['type'] == 2) {
							$getShopWhere = ' WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 2 AND enabled = 1 ';

							switch ($v['params']['rule']) {
							case 1:
								$getShopWhere .= ' ORDER BY createtime DESC LIMIT ' . $v['params']['show_num'] . ' ';
								break;

							case 3:
								$getShopWhere .= ' ORDER BY id DESC  LIMIT ' . $v['params']['show_num'] . ' ';
								break;

							case 4:
								$getShopWhere .= ' ORDER BY pv DESC  LIMIT ' . $v['params']['show_num'] . ' ';
								break;
							}

							$v['data'] = pdo_fetchall('SELECT id FROM ' . tablename(PDO_NAME . 'merchantdata') . $getShopWhere);
						}

						foreach ($v['data'] as $shop_key => &$shop_val) {
							$v['data'][$shop_key] = pdo_get(PDO_NAME . 'merchantdata', array('id' => $shop_val['id']), array('id', 'storename', 'logo', 'address', 'location', 'storehours', 'pv', 'score', 'onelevel', 'twolevel'));
							$v['data'][$shop_key]['oneType'] = pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $v['data'][$shop_key]['onelevel']), 'name');
							$v['data'][$shop_key]['twoType'] = pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $v['data'][$shop_key]['twolevel']), 'name');

							if ($v['id'] == 'shop2') {
								$v['data'][$shop_key]['salesVolume'] = Store::getShopSales($v['data'][$shop_key]['id'], 1, 0);
								$v['data'][$shop_key]['score'] = sprintf('%.1f', $v['data'][$shop_key]['score']);
							}

							unset($v['data'][$shop_key]['onelevel']);
							unset($v['data'][$shop_key]['twolevel']);
							if (!$shop_val || !$v['data'][$shop_key]) {
								unset($v['data'][$shop_key]);
								continue;
							}

							if ($pageClass == 1) {
								$url = app_url('store/merchant/detail', array('id' => $shop_val['id']), false);
							}
							else {
								if ($pageClass == 2) {
									$url = '/pagesGoodstore/storedetail/index?i=' . $shop_val['id'];
									$v['data'][$shop_key]['url_type'] = 'navigate';
								}
							}

							$v['data'][$shop_key]['shop_url'] = $url;
						}

						if ($v['params']['type'] == 1) {
							$v['data'] = Store::getstores($v['data'], $lng, $lat, 5);
						}
						else {
							$shopSortRule = $v['params']['rule'] ? $v['params']['rule'] : 3;
							$v['data'] = Store::getstores($v['data'], $lng, $lat, $shopSortRule);
						}

						foreach ($v['data'] as $delLocationK => &$delLocationV) {
							unset($delLocationV['location']);
						}

						if ($v['id'] == 'shop') {
							$v['data'] = WeChat::getStoreList($v['data']);
						}
					}

					if ($v['group_name'] == 'area_select' || $v['id'] == 'area_select') {
						if ($type == 1) {
							$areaUrl = app_url('area/region/select_region', array('backurl' => app_url('diypage/diyhome/home', array('aid' => $_W['aid'], 'id' => $id))));
						}
						else {
							if ($type == 2) {
								$areaUrl = app_url('area/region/select_region', array('backurl' => app_url('dashboard/home/index', array('id' => $id))));
							}
						}

						$v['params']['url'] = $areaUrl;
						$v['params']['areaname'] = $_W['areaname'] ? $_W['areaname'] : '未定位';
						$v['params']['nickname'] = $_W['wlmember']['nickname'] ? $_W['wlmember']['nickname'] : '未登录';
						$v['params']['avatar'] = $_W['wlmember']['avatar'] ? $_W['wlmember']['avatar'] : tomedia('/addons/weliam_merchant/web/resource/images/default.png');
					}

					if ($v['group_name'] == 'options' || $v['id'] == 'options') {
						$option_sort = array_column($v['data'], 'sort');
						array_multisort($option_sort, SORT_ASC, $v['data']);

						if ($v['id'] == 'options') {
						}
						else {
							if ($v['id'] == 'options2') {
								switch ($v['params']['goods_type']) {
								case 'rush':
									$table = tablename(PDO_NAME . 'rush_activity');
									$field = ' a.id ';
									$join = '  b ON a.sid = b.id ';
									$plugin = 1;
									break;

								case 'groupon':
									$table = tablename(PDO_NAME . 'groupon_activity');
									$field = ' a.id ';
									$join = '  b ON a.sid = b.id  ';
									$plugin = 2;
									break;

								case 'wlfightgroup':
									$table = tablename(PDO_NAME . 'fightgroup_goods');
									$field = ' a.id ';
									$join = '  b ON a.merchantid = b.id  ';
									$plugin = 3;
									break;

								case 'bargain':
									$table = tablename(PDO_NAME . 'bargain_activity');
									$field = ' a.id ';
									$join = '  b ON a.sid = b.id ';
									$plugin = 7;
									break;
								}

								foreach ($v['data'] as $O2K => &$O2V) {
									$optionWhere = ' WHERE a.status = ' . $O2V['status'] . ' AND a.aid = ' . $_W['aid'] . ' ';

									if (1 < count(explode(',', $O2V['status']))) {
										$optionWhere = ' WHERE a.status IN (' . $O2V['status'] . ') ';
									}

									$O2V['goods_list'] = pdo_fetchall('SELECT ' . $field . ' FROM ' . $table . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . $join . $optionWhere . ' ORDER BY id DESC LIMIT 0,5 ');

									foreach ($O2V['goods_list'] as $O2Vkey => &$O2Vval) {
										$O2Vval = WeChat::getHomeGoods($plugin, $O2Vval['id']);

										if ($pageClass == 1) {
											switch ($plugin) {
											case 1:
												$url = app_url('rush/home/detail', array('id' => $O2Vval['id']));
												break;

											case 2:
												$url = app_url('groupon/grouponapp/groupondetail', array('cid' => $O2Vval['id']));
												break;

											case 3:
												$url = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $O2Vval['id']));
												break;

											case 7:
												$url = app_url('bargain/bargain_app/bargaindetail', array('cid' => $O2Vval['id']));
												break;
											}
										}
										else {
											if ($pageClass == 2) {
												switch ($plugin) {
												case 1:
													$url = '/pagesBuy/rushdetail/index?i=' . $O2Vval['id'] . '&c=rush';
													break;

												case 2:
													$url = '/pagesBuy/rushdetail/index?i=' . $O2Vval['id'] . '&c=groupon';
													break;

												case 3:
													$url = '/pagesBuy/rushdetail/index?i=' . $O2Vval['id'] . '&c=fightgroup';
													break;
												}

												$v['data'][$O2K]['goods_list'][$O2Vkey]['url_type'] = 'navigate';
											}
										}

										$v['data'][$O2K]['goods_list'][$O2Vkey]['url'] = $url;
									}
								}
							}
						}
					}

					if ($v['id'] == 'richtext') {
						$v['params']['content'] = base64_decode($v['params']['content']);
					}
				}
			}

			unset($v['nav_class']);
			unset($v['name']);
			unset($v['pageClass']);
			unset($v['plugin']);
		}

		return $info;
	}

	/**
     * Comment: 判断广告是否过期  过期返回空
     * Author: zzw
     */
	static public function BeOverdue($id, $state = true)
	{
		$advdata = self::getAdv($id, true);
		$name = 'advstate_' . $advdata['id'];
		$advstate = Util::getCookie($name);
		if ($advdata['data']['params']['showtype'] == 1 && $state) {
			if ($advstate['endtime']) {
				return NULL;
			}

			$time = $advdata['data']['params']['showtime'] * 60;
			$endtime = time() + $time;
			Util::setCookie($name, array('endtime' => $endtime), $time);
		}
		else {
			if ($advstate) {
				Util::setCookie($name, array('endtime' => time()), 0);
			}
		}

		return $advdata;
	}

	/**
     * Comment: 进入模板，页面编辑页面的公共操作
     * Author: zzw
     */
	static public function getCommon($pageClass)
	{
		global $_W;

		if ($pageClass == 1) {
			$hasplugins = json_encode(array('groupon' => p('groupon') ? 1 : 0, 'fightgroup' => p('wlfightgroup') ? 1 : 0, 'coupon' => p('wlcoupon') ? 1 : 0, 'rush' => p('rush') ? 1 : 0, 'bargain' => p('bargain') ? 1 : 0));
		}
		else {
			$hasplugins = json_encode(array('groupon' => p('groupon') ? 1 : 0, 'fightgroup' => p('wlfightgroup') ? 1 : 0, 'coupon' => p('wlcoupon') ? 1 : 0, 'rush' => p('rush') ? 1 : 0, 'headline' => p('headline') ? 1 : 0));
		}

		$goodsWhere = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' ';
		$goodsListW = array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid']);
		$goodCate['rush']['list'] = pdo_getall(PDO_NAME . 'rush_category', $goodsListW, array('id', 'name'));
		$goodCate['rush']['count'] = count(pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE ' . $goodsWhere . ' AND status IN (1,2)')));
		$goodCate['groupon']['list'] = pdo_getall(PDO_NAME . 'groupon_category', $goodsListW, array('id', 'name'));
		$goodCate['groupon']['count'] = count(pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE ' . $goodsWhere . ' AND status IN (1,2) ')));
		$goodCate['fightgroup']['list'] = pdo_getall(PDO_NAME . 'fightgroup_category', $goodsListW, array('id', 'name'));
		$goodCate['fightgroup']['count'] = count(pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . (' WHERE ' . $goodsWhere . ' AND status = 1')));
		$goodCate['coupon']['list'] = array(
			array('id' => 1, 'name' => '折扣卷'),
			array('id' => 2, 'name' => '代金券'),
			array('id' => 3, 'name' => '套餐券'),
			array('id' => 4, 'name' => '团购券'),
			array('id' => 5, 'name' => '优惠券')
		);
		$goodCate['coupon']['count'] = count(pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE ' . $goodsWhere . ' AND status = 1')));
		$goodCate['bargain']['list'] = pdo_getall(PDO_NAME . 'bargain_category', $goodsListW, array('id', 'name'));
		$goodCate['bargain']['count'] = count(pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE ' . $goodsWhere . ' AND status IN (1,2)')));
		$data['hasplugins'] = $hasplugins;
		$data['goodCate'] = $goodCate;
		return $data;
	}

	/**
     * Comment: 根据条件获取装修功能中的商品信息
     * Author: zzw
     * @param $params  商品类型
     * @param $data    当前的商品数据
     * @param $plugin  商品类型  1=抢购  2=团购  3=拼团  5=优惠卷
     * @return array
     */
	static private function getDiyGoods($params, $plugin, $type)
	{
		global $_W;
		$where = ' aid = ' . $_W['aid'] . ' AND uniacid = ' . $_W['uniacid'] . ' ';
		$limit = ' LIMIT 0,' . $params['show_num'] . ' ';

		if ($params['type'] == 2) {
			switch ($plugin) {
			case 1:
				$where .= ' AND status IN (1,2)  ';

				if (0 < $params['classs']) {
					$where .= ' AND cateid = ' . $params['classs'] . '  ';
				}

				switch ($params['orders']) {
				case 1:
					$order = ' ORDER BY sort DESC ';
					break;

				case 2:
					$order = ' ORDER BY (IFNULL((SELECT SUM(num) FROM ' . tablename(PDO_NAME . 'rush_order') . ' WHERE `activityid` = goods_id  AND status IN (0,1,2,3,6,9)),0) + allsalenum) DESC ';
					break;

				case 3:
					$order = ' ORDER BY price DESC ';
					break;

				case 4:
					$order = ' ORDER BY price ASC ';
					break;

				case 5:
					$order = ' ORDER BY id DESC ';
					break;
				}

				$info = pdo_fetchall('SELECT id,id as goods_id FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE ' . $where . $order . $limit));
				break;

			case 2:
				$where .= ' AND status IN (1,2) ';

				if (0 < $params['classs']) {
					$where .= ' AND cateid = ' . $params['classs'] . ' ';
				}

				if ($type == 2) {
					$where .= ' AND is_indexshow = 0 ';
				}
				else {
					if ($type == 4) {
						$where .= ' AND recommend = 1 ';
					}
				}

				switch ($params['orders']) {
				case 1:
					$order = ' ORDER BY sort DESC ';
					break;

				case 2:
					$order = ' ORDER BY (IFNULL((SELECT SUM(num) FROM ' . tablename(PDO_NAME . 'order') . ' WHERE `fkid` = goods_id AND plugin = \'groupon\' AND status IN (0,1,2,3,6,9)),0) + falsesalenum) DESC ';
					break;

				case 3:
					$order = ' ORDER BY price DESC ';
					break;

				case 4:
					$order = ' ORDER BY price ASC ';
					break;

				case 5:
					$order = ' ORDER BY id DESC ';
					break;
				}

				$info = pdo_fetchall('SELECT id,id as goods_id FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE ' . $where . $order . $limit));
				break;

			case 3:
				$where .= ' AND status = 1 ';

				if (0 < $params['classs']) {
					$where .= ' AND categoryid = ' . $params['classs'] . ' ';
				}

				switch ($params['orders']) {
				case 1:
					$order = ' ORDER BY listorder DESC ';
					break;

				case 2:
					$order = ' ORDER BY (IFNULL(realsalenum,0) + falsesalenum) DESC';
					break;

				case 3:
					$order = ' ORDER BY price DESC ';
					break;

				case 4:
					$order = ' ORDER BY price ASC ';
					break;

				case 5:
					$order = ' ORDER BY id DESC ';
					break;
				}

				$info = pdo_fetchall('SELECT id FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . (' WHERE ' . $where . $order . $limit));
				break;

			case 4:
				$where .= ' AND a.status = 1 ';

				switch ($params['orders']) {
				case 1:
					$order = ' ORDER BY a.sort DESC ';
					break;

				case 2:
					$order = ' ORDER BY (SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'timecardrecord') . ' WHERE `activeid` = a.id AND type = 2) DESC ';
					break;

				case 3:
					$order = ' ORDER BY a.price DESC ';
					break;

				case 4:
					$order = ' ORDER BY a.price ASC ';
					break;

				case 5:
					$order = ' ORDER BY a.id DESC ';
					break;
				}

				$info = pdo_fetchall('SELECT id FROM ' . tablename(PDO_NAME . 'package') . (' as a WHERE ' . $where . $order . $limit));
				break;

			case 5:
				$where .= '  AND status = 1 ';

				if (0 < $params['classs']) {
					$where .= ' AND type = ' . $params['classs'] . ' ';
				}

				switch ($params['orders']) {
				case 1:
					$order = ' ORDER BY indexorder DESC ';
					break;

				case 2:
					$order = ' ORDER BY surplus DESC ';
					break;

				case 3:
					$order = ' ORDER BY price DESC ';
					break;

				case 4:
					$order = ' ORDER BY price ASC ';
					break;

				case 5:
					$order = ' ORDER BY id DESC ';
					break;
				}

				$info = pdo_fetchall('SELECT id,id as goods_id FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE ' . $where . $order . $limit));
				break;

			case 6:
				$where .= ' AND status = 1 ';

				switch ($params['orders']) {
				case 1:
					$order = ' ORDER BY sort DESC ';
					break;

				case 2:
					$order = ' ORDER BY (SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'timecardrecord') . ' WHERE `activeid` = a.id AND type = 1) DESC ';
					break;

				case 3:
					$order = ' ORDER BY activediscount DESC ';
					break;

				case 4:
					$order = ' ORDER BY activediscount ASC ';
					break;

				case 5:
					$order = ' ORDER BY id DESC ';
					break;
				}

				$info = pdo_fetchall('SELECT id FROM ' . tablename(PDO_NAME . 'halfcardlist') . (' as a WHERE ' . $where . $order . $limit));
				break;

			case 7:
				$where .= ' AND status IN (1,2) ';

				if (0 < $params['classs']) {
					$where .= ' AND cateid = ' . $params['classs'] . '  ';
				}

				switch ($params['orders']) {
				case 1:
					$order = ' ORDER BY sort DESC ';
					break;

				case 2:
					$order = ' ORDER BY (IFNULL((SELECT SUM(num) FROM ' . tablename(PDO_NAME . 'order') . ' WHERE `fkid` = goods_id AND plugin = \'bargain\' AND status IN (0,1,2,3,6,9)),0)) DESC ';
					break;

				case 3:
					$order = ' ORDER BY oldprice DESC ';
					break;

				case 4:
					$order = ' ORDER BY oldprice ASC ';
					break;

				case 5:
					$order = ' ORDER BY id DESC ';
					break;
				}

				$info = pdo_fetchall('SELECT id,id as goods_id FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE ' . $where . $order . $limit));
				break;
			}
		}
		else {
			$where .= ' AND status = ' . $params['status'] . ' ';

			switch ($params['orders']) {
			case 1:
				$order = ' ORDER BY sort DESC ';
				break;

			case 2:
				$order = ' ORDER BY (IFNULL((SELECT SUM(num) FROM ' . tablename(PDO_NAME . 'rush_order') . ' WHERE `activityid` = goods_id  AND status IN (0,1,2,3,6,9)),0) + allsalenum) DESC ';
				break;

			case 3:
				$order = ' ORDER BY price DESC ';
				break;

			case 4:
				$order = ' ORDER BY price ASC ';
				break;

			case 5:
				$order = ' ORDER BY id DESC ';
				break;
			}

			$info = pdo_fetchall('SELECT id,id as goods_id FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE ' . $where . $order . $limit));
		}

		return $info;
	}

	static public function checkuniac($agentset, $wlset)
	{
		if (empty($agentset)) {
			$agentset = $wlset;
		}
		else {
			foreach ($agentset as $key => &$v) {
				if (empty($v)) {
					$v = $wlset[$key];
				}
			}
		}

		return $agentset;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
