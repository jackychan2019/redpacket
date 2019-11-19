<?php
//dezend by http://www.sucaihuo.com/
class Store
{
	/**
	 * 商户用户信息查询
	 */
	static public function getAllRegister($page = 0, $pagenum = 10, $status = 1)
	{
		global $_W;
		$re['data'] = pdo_fetchall('select * from' . tablename(PDO_NAME . 'merchantuser') . 'where uniacid=:uniacid and aid=:aid and status=:status order by createtime desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid'], ':status' => $status));
		$re['count'] = pdo_fetchcolumn('select count(*) from' . tablename(PDO_NAME . 'merchantuser') . 'where uniacid=:uniacid and aid=:aid and status=:status order by createtime desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid'], ':status' => $status));
		return $re;
	}

	/**
	 * 根据mid获取单个商户用户信息
	 */
	static public function getSingleRegister($mid)
	{
		global $_W;

		if (empty($mid)) {
			return '';
		}

		if (is_array($mid)) {
			return pdo_get(PDO_NAME . 'merchantuser', $mid);
		}

		$res = pdo_get(PDO_NAME . 'merchantuser', array('mid' => $mid, 'uniacid' => $_W['uniacid'], 'status' => 2));

		if (empty($res)) {
			$res2 = pdo_get(PDO_NAME . 'merchantuser', array('mid' => $mid, 'uniacid' => $_W['uniacid']));
			return $res2;
		}

		return $res;
	}

	/**
	 * 保存商户用户信息
	 */
	static public function saveSingleRegister($arr, $mid = '')
	{
		global $_W;

		if (!empty($mid)) {
			return pdo_update(PDO_NAME . 'merchantuser', $arr, array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $mid));
		}

		$arr['mid'] = $_W['mid'];
		return pdo_insert(PDO_NAME . 'merchantuser', $arr);
	}

	/**
	 * 修改商户用户
	 */
	static public function editSingleRegister($id, $arr)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_update(PDO_NAME . 'merchantuser', $arr, array('id' => $id, 'uniacid' => $_W['uniacid']));
	}

	/**
	 * 获取所有商户分组
	 */
	static public function getAllGroup($page = 0, $pagenum = 10, $enabled = '', $aid = '')
	{
		global $_W;
		$condition = '';

		if (!empty($aid)) {
			$condition .= ' and aid=' . $aid;
		}

		if (!empty($enabled) && $enabled != '') {
			$condition .= ' and enabled=' . $enabled;
		}

		$re['data'] = pdo_fetchall('select * from' . tablename(PDO_NAME . 'storeusers_group') . 'where uniacid=:uniacid and aid=:aid  ' . $condition . ' order by enabled desc, createtime desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid']));
		$re['count'] = pdo_fetchcolumn('select count(*) from' . tablename(PDO_NAME . 'storeusers_group') . 'where uniacid=:uniacid and aid=:aid  ' . $condition, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid']));
		return $re;
	}

	static public function getSingleGroup($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_get(PDO_NAME . 'storeusers_group', array('id' => $id, 'uniacid' => $_W['uniacid']));
	}

	static public function editGroup($arr, $id = '')
	{
		global $_W;

		if (empty($arr)) {
			return false;
		}

		if ($arr['isdefault'] == 1) {
			pdo_update(PDO_NAME . 'storeusers_group', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'isdefault' => 1));
		}

		if (!empty($id) && $id != '') {
			return pdo_update(PDO_NAME . 'storeusers_group', $arr, array('id' => $id, 'uniacid' => $_W['uniacid']));
		}

		$arr['uniacid'] = $_W['uniacid'];
		$arr['aid'] = $_W['aid'];
		return pdo_insert(PDO_NAME . 'storeusers_group', $arr);
	}

	static public function deleteGroup($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_delete(PDO_NAME . 'storeusers_group', array('id' => $id, 'uniacid' => $_W['uniacid']));
	}

	static public function getSingleCategory($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		return pdo_get(PDO_NAME . 'category_store', array('id' => $id, 'uniacid' => $_W['uniacid']));
	}

	static public function getAllCategory($page = 0, $pagenum = 10, $parentid = 0)
	{
		global $_W;
		$re['data'] = pdo_fetchall('select * from' . tablename(PDO_NAME . 'category_store') . 'where uniacid=:uniacid and aid=:aid and parentid=:parentid order by displayorder desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid'], ':parentid' => $parentid));
		$re['count'] = pdo_fetchcolumn('select count(*) from' . tablename(PDO_NAME . 'category_store') . 'where uniacid=:uniacid and aid=:aid and parentid=:parentid', array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid'], ':parentid' => $parentid));
		return $re;
	}

	static public function categoryEdit($arr, $id = '')
	{
		global $_W;

		if (empty($arr)) {
			return false;
		}

		if (!empty($id) && $id != '') {
			return pdo_update(PDO_NAME . 'category_store', $arr, array('id' => $id, 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
		}

		$arr['aid'] = $_W['aid'];
		$arr['uniacid'] = $_W['uniacid'];
		return pdo_insert(PDO_NAME . 'category_store', $arr);
	}

	static public function categoryDelete($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		$arr = pdo_getall(PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'parentid' => $id));

		if (empty($arr)) {
			return pdo_delete(PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'id' => $id));
		}

		foreach ($arr as $key => $value) {
			if (!self::categoryDelete($value['id'])) {
				return false;
			}
		}

		return pdo_delete(PDO_NAME . 'category_store', array('uniacid' => $_W['uniacid'], 'id' => $id));
	}

	static public function registerEdit($arr)
	{
		global $_W;

		if (empty($arr)) {
			return false;
		}

		$arr['uniacid'] = $_W['uniacid'];
		return pdo_insert(PDO_NAME . 'merchantuser', $arr);
	}

	static public function registerCheck()
	{
		global $_W;
		return pdo_fetchall('select * from' . tablename(PDO_NAME . 'storeusers_group'));
	}

	static public function registerNickname($arr)
	{
		global $_W;
		$con = $arr;
		return pdo_fetchall('select * from' . tablename(PDO_NAME . 'member') . ('where ' . $con));
	}

	static public function getAllUser($page = 0, $pagenum = 10, $enabled = 0)
	{
		global $_W;
		$re['data'] = pdo_fetchall('select * from' . tablename(PDO_NAME . 'merchantuser') . 'where uniacid=:uniacid and status=:status and enabled=:enabled order by createtime desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid'], ':status' => 2, ':enabled' => $enabled));

		foreach ($re['data'] as $key => $value) {
			if (strtotime($re['data'][$key]['endtime']) < time()) {
				$re['data'][$key]['enabled'] = 3;
				pdo_update(PDO_NAME . 'merchantuser', $re['data'][$key], array('id' => $re['data'][$key]['id'], 'uniacid' => $_W['uniacid']));
			}
		}

		$re['count'] = pdo_fetchcolumn('select count(*) from' . tablename(PDO_NAME . 'merchantuser') . 'where uniacid=:uniacid and status=:status and enabled=:enabled order by createtime desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid'], ':status' => 2, ':enabled' => $enabled));
		return $re;
	}

	static public function registerEditUser($arr, $id = '')
	{
		global $_W;

		if (empty($arr)) {
			return false;
		}

		if (!empty($id) && $id != '') {
			pdo_update(PDO_NAME . 'merchantuser', $arr, array('id' => $id, 'uniacid' => $_W['uniacid']));
			return $id;
		}

		$arr['uniacid'] = $_W['uniacid'];
		$arr['aid'] = $_W['aid'];
		pdo_insert(PDO_NAME . 'merchantuser', $arr);
		$uid = pdo_insertid();
		return $uid;
	}

	static public function registerEditData($arr, $id = '')
	{
		global $_W;

		if (empty($arr)) {
			return false;
		}

		if (!empty($id) && $id != '') {
			pdo_update(PDO_NAME . 'merchantdata', $arr, array('id' => $id, 'uniacid' => $_W['uniacid']));
			return $id;
		}

		$arr['uniacid'] = $_W['uniacid'];
		$arr['aid'] = $_W['aid'];
		pdo_insert(PDO_NAME . 'merchantdata', $arr);
		$uid = pdo_insertid();
		return $uid;
	}

	static public function deleteUser($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		$arr = pdo_get(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'id' => $id));

		if ($arr['storeid'] != 0) {
			pdo_delete(PDO_NAME . 'merchantdata', array('id' => $arr['storeid'], 'uniacid' => $_W['uniacid']));
		}

		return pdo_delete(PDO_NAME . 'merchantuser', array('id' => $id, 'uniacid' => $_W['uniacid']));
	}

	static public function getSingleStore($id)
	{
		global $_W;

		if (empty($id)) {
			return '';
		}

		return pdo_get(PDO_NAME . 'merchantdata', array('id' => $id, 'uniacid' => $_W['uniacid']));
	}

	static public function deleteStoreByMid($userid)
	{
		global $_W;

		if (empty($userid)) {
			return false;
		}

		$arr = pdo_get(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'id' => $userid));

		if (!empty($arr['storeid'])) {
			pdo_delete(PDO_NAME . 'merchantdata', array('id' => $arr['storeid'], 'uniacid' => $_W['uniacid']));
		}

		return pdo_delete(PDO_NAME . 'merchantuser', array('id' => $userid, 'uniacid' => $_W['uniacid']));
	}

	static public function getstores($locations, $lng, $lat, $nearid)
	{
		global $_W;

		foreach ($locations as $key => $val) {
			$loca = unserialize($val['location']);
			$storehours = unserialize($val['storehours']);
			$locations[$key]['distance'] = self::getdistance($loca['lng'], $loca['lat'], $lng, $lat);

			if (empty($locations[$key]['distance'])) {
				$locations[$key]['distance'] = 99999999;
			}

			$locations[$key]['logo'] = tomedia($val['logo']);
			$locations[$key]['url'] = app_url('store/merchant/detail', array('id' => $val['id']));
			$locations[$key]['storehours'] = $storehours['startTime'] . '—' . $storehours['endTime'] . '  营业';
			$cate = '';

			if ($val['onelevel']) {
				$cate .= pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $val['onelevel']), 'name');
			}

			if ($val['twolevel']) {
				$cate .= ' | ';
				$cate .= pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $val['twolevel']), 'name');
			}

			$locations[$key]['cate'] = $cate;
		}

		if ($nearid == 2) {
			$sort_key = 'distance';
			$sort_order = SORT_ASC;
		}
		else if ($nearid == 1) {
			$sort_key = 'createtime';
			$sort_order = SORT_DESC;
		}
		else if ($nearid == 4) {
			$sort_key = 'pv';
			$sort_order = SORT_DESC;
		}
		else {
			$sort_key = 'listorder';
			$sort_order = SORT_DESC;
		}

		if ($nearid != 5) {
			$locations = self::wl_sort($locations, $sort_key, $sort_order, SORT_NUMERIC);
		}

		foreach ($locations as $key => $value) {
			if (!empty($value['distance'])) {
				if (99999998 < $value['distance']) {
					$locations[$key]['distance'] = ' ';
				}
				else if (1000 < $value['distance']) {
					$locations[$key]['distance'] = floor($value['distance'] / 1000 * 10) / 10 . 'km';
				}
				else {
					$locations[$key]['distance'] = round($value['distance']) . 'm';
				}
			}
		}

		return $locations;
	}

	static public function getdistance($lng1, $lat1, $lng2, $lat2)
	{
		if (empty($lng1) || empty($lat1) || empty($lng2) || empty($lat2)) {
			return '';
		}

		$radLat1 = @deg2rad($lat1);
		$radLat2 = @deg2rad($lat2);
		$radLng1 = @deg2rad($lng1);
		$radLng2 = @deg2rad($lng2);
		$a = $radLat1 - $radLat2;
		$b = $radLng1 - $radLng2;
		$s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.1369999999997 * 1000;
		return $s;
	}

	static public function wl_sort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC)
	{
		if (is_array($arrays)) {
			foreach ($arrays as $array) {
				if (is_array($array)) {
					$key_arrays[] = $array[$sort_key];
				}
				else {
					return false;
				}
			}
		}
		else {
			return false;
		}

		array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
		return $arrays;
	}



//受打击了飞洒发进阿里发
	static public function settlement($orderid, $goodsid, $merchantid, $price, $orderno, $settlementmoney, $type, $disorderid = 0, $sharemoney = 0, $aid = 0)
	{
		global $_W;

		if (empty($aid)) {
			$aid = $_W['aid'];
		}

		if ($orderno) {
			$flag = pdo_get(PDO_NAME . 'autosettlement_record', array('orderno' => $orderno), array('id'));
		}
		else {
			$flag = pdo_get(PDO_NAME . 'autosettlement_record', array('type' => 7, 'orderid' => $orderid, 'orderprice' => $price), array('id'));
		}

		if ($flag) {
			return 1;
		}

		if ($disorderid) {
			$disorder = pdo_get('wlmerchant_disorder', array('id' => $disorderid), array('leadmoney'));
			$leadmoneys = unserialize($disorder['leadmoney']);

			foreach ($leadmoneys as $key => $money) {
				$distrimoney += $money;
			}
		}
		else {
			$distrimoney = 0;
		}

		$data = array('uniacid' => $_W['uniacid'], 'aid' => $aid, 'type' => $type, 'merchantid' => $merchantid, 'orderid' => $orderid, 'orderno' => $orderno, 'goodsid' => $goodsid, 'orderprice' => $price, 'agentmoney' => round($price - $settlementmoney - $distrimoney - $sharemoney, 2), 'merchantmoney' => $settlementmoney, 'distrimoney' => $distrimoney, 'sharemoney' => $sharemoney, 'createtime' => time());

		if ($type == 8) {
			$settings = Setting::wlsetting_read('distribution');

			if ($settings['seetstatus']) {
				$data['agentmoney'] = 0;
			}
		}

		$res = pdo_insert(PDO_NAME . 'autosettlement_record', $data);
		$settlementid = pdo_insertid();

		if ($res) {
			if (0 < abs($settlementmoney)) {
				if ($type == 7) {
					pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET nowmoney=nowmoney+' . $settlementmoney . ' WHERE id = ' . $merchantid));
				}
				else {
					pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET allmoney=allmoney+' . $settlementmoney . ',nowmoney=nowmoney+' . $settlementmoney . ' WHERE id = ' . $merchantid));
				}

				$change['merchantnowmoney'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $data['merchantid']), 'nowmoney');
				self::addcurrent(1, $type, $merchantid, $settlementmoney, $change['merchantnowmoney'], $orderid);
			}

			if (0 < abs($data['agentmoney'])) {
				if ($type == 7) {
					pdo_fetch('update' . tablename('wlmerchant_agentusers') . ('SET nowmoney=nowmoney+' . $data['agentmoney'] . ' WHERE id = ' . $data['aid']));
				}
				else {
					pdo_fetch('update' . tablename('wlmerchant_agentusers') . ('SET allmoney=allmoney+' . $data['agentmoney'] . ',nowmoney=nowmoney+' . $data['agentmoney'] . ' WHERE id = ' . $data['aid']));
				}

				$change['agentnowmoney'] = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $data['aid']), 'nowmoney');
				self::addcurrent(2, $type, $data['aid'], $data['agentmoney'], $change['agentnowmoney'], $orderid);
			}

			pdo_update('wlmerchant_autosettlement_record', $change, array('id' => $settlementid));
		}

		return $res;
	}

	static public function addcurrent($status, $type, $objid, $fee, $nowmoney, $orderid, $remark = '', $uniacid = '', $aid = '')
	{
		global $_W;
		$data = array('uniacid' => $uniacid ? $uniacid : $_W['uniacid'], 'status' => $status, 'type' => $type, 'objid' => $objid, 'fee' => $fee, 'nowmoney' => $nowmoney, 'orderid' => $orderid, 'remark' => $remark, 'createtime' => time(), 'aid' => $aid ? $aid : $_W['aid']);
		pdo_insert(PDO_NAME . 'current', $data);
	}

	static public function saveExpress($data)
	{
		global $_W;

		if (!is_array($data)) {
			return false;
		}

		$data['uniacid'] = $_W['uniacid'];
		pdo_insert(PDO_NAME . 'express_template', $data);
		return pdo_insertid();
	}

	static public function updateExpress($data, $id)
	{
		global $_W;

		if (!is_array($data)) {
			return false;
		}

		$res = pdo_update('wlmerchant_express_template', $data, array('id' => $id));
		return $res;
	}

	static public function getNumExpress($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'express_template', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	static public function deteleExpress($id)
	{
		$res = pdo_delete('wlmerchant_express_template', array('id' => $id));
		return $res;
	}

	static public function gethalfsettlementmoney($money, $sid, $vipbuyflag)
	{
		global $_W;
		$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $sid), array('groupid', 'settlementtext'));
		$sett = unserialize($merchant['settlementtext']);
		if (0 < $sett['paysett'] && empty($vipbuyflag)) {
			$settlementmoney = sprintf('%.2f', $money * $sett['paysett'] / 100);
		}
		else {
			if (0 < $sett['payvip'] && $vipbuyflag) {
				$settlementmoney = sprintf('%.2f', $money * $sett['payvip'] / 100);
			}
			else {
				$grouprete = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('id' => $merchant['groupid']), 'defaultrate');
				$settlementmoney = sprintf('%.2f', $money * $grouprete / 100);
			}
		}

		return $settlementmoney;
	}

	static public function getsettlementmoney($type, $goodsid, $num, $sid, $vipbuyflag = 0, $optionid = 0, $fightflah = 0)
	{
		global $_W;

		if ($type == 1) {
			$goods = pdo_get(PDO_NAME . 'rush_activity', array('id' => $goodsid), array('price', 'vipprice', 'settlementmoney', 'vipsettlementmoney', 'independent'));
		}
		else if ($type == 2) {
			$goods = pdo_get(PDO_NAME . 'fightgroup_goods', array('id' => $goodsid), array('price', 'aloneprice', 'settlementmoney', 'independent', 'independent'));
			$goods['vipsettlementmoney'] = $goods['settlementmoney'];

			if ($fightflah) {
				$goods['price'] = $goods['aloneprice'];
				$goods['vipprice'] = $goods['aloneprice'];
			}
			else {
				$goods['vipprice'] = $goods['price'];
			}
		}
		else if ($type == 3) {
			$goods = pdo_get(PDO_NAME . 'groupon_activity', array('id' => $goodsid), array('price', 'vipprice', 'settlementmoney', 'vipsettlementmoney', 'independent'));
		}
		else if ($type == 4) {
			$goods = pdo_get(PDO_NAME . 'couponlist', array('id' => $goodsid), array('price', 'vipprice', 'settlementmoney', 'vipsettlementmoney', 'independent'));
		}
		else {
			if ($type == 5) {
				$goods = pdo_get(PDO_NAME . 'bargain_userlist', array('id' => $goodsid), array('price', 'activityid'));
				$activity = pdo_get(PDO_NAME . 'bargain_activity', array('id' => $goods['activityid']), array('settlementmoney', 'independent', 'vipsettlementmoney'));
				$goods['settlementmoney'] = $activity['settlementmoney'];
				$goods['independent'] = $activity['independent'];
				$goods['vipsettlementmoney'] = $activity['vipsettlementmoney'];
			}
		}

		if ($optionid) {
			$option = pdo_get(PDO_NAME . 'goods_option', array('id' => $optionid), array('price', 'vipprice', 'settlementmoney', 'vipsettlementmoney'));
			$goods['price'] = $option['price'];
			$goods['vipprice'] = $option['vipprice'];
			$goods['settlementmoney'] = $option['settlementmoney'];
			$goods['vipsettlementmoney'] = $option['vipsettlementmoney'];
		}

		if ($vipbuyflag) {
			$settlementmoney = $goods['vipsettlementmoney'];
		}
		else {
			$settlementmoney = $goods['settlementmoney'];
		}

		if (empty($goods['independent'])) {
			$settlementmoney = $settlementmoney * $num;
		}
		else {
			$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $sid), array('groupid', 'settlementtext'));
			$sett = unserialize($merchant['settlementtext']);

			if ($type == 1) {
				$settlementrate = $sett['rushsett'];
				$vipsettlementrate = $sett['rushvip'];
			}
			else if ($type == 2) {
				$settlementrate = $sett['fightsett'];
				$vipsettlementrate = $sett['fightvip'];
			}
			else if ($type == 3) {
				$settlementrate = $sett['grouponsett'];
				$vipsettlementrate = $sett['grouponvip'];
			}
			else if ($type == 4) {
				$settlementrate = $sett['couponsett'];
				$vipsettlementrate = $sett['couponvip'];
			}
			else {
				if ($type == 5) {
					$settlementrate = $sett['bargainsett'];
					$vipsettlementrate = $sett['bargainvip'];
				}
			}

			if (0 < $settlementrate && empty($vipbuyflag)) {
				$settlementmoney = sprintf('%.2f', $goods['price'] * $settlementrate / 100 * $num);
			}
			else {
				if (0 < $vipsettlementrate && $vipbuyflag) {
					$settlementmoney = sprintf('%.2f', $goods['vipprice'] * $vipsettlementrate / 100 * $num);
				}
				else {
					$grouprete = pdo_getcolumn(PDO_NAME . 'storeusers_group', array('id' => $merchant['groupid']), 'defaultrate');

					if (empty($vipbuyflag)) {
						$settlementmoney = sprintf('%.2f', $goods['price'] * $grouprete / 100 * $num);
					}
					else {
						$settlementmoney = sprintf('%.2f', $goods['vipprice'] * $grouprete / 100 * $num);
					}
				}
			}
		}

		if (empty($settlementmoney)) {
			$settlementmoney = 0;
		}

		return $settlementmoney;
	}

	static public function rushsettlement($id)
	{
		global $_W;
		$rush = pdo_get('wlmerchant_rush_order', array('id' => $id));

		if (empty($rush['issettlement'])) {
			if ($rush['shareid']) {
				$sharemoney = pdo_getcolumn(PDO_NAME . 'sharecurrent', array('shareid' => $rush['shareid']), 'price');
			}

			if (empty($sharemoney)) {
				$sharemoney = 0;
			}

			$res = self::settlement($rush['id'], $rush['activityid'], $rush['sid'], $rush['actualprice'], $rush['orderno'], $rush['settlementmoney'], 1, $rush['disorderid'], $sharemoney, $rush['aid']);

			if ($res) {
				pdo_update('wlmerchant_rush_order', array('issettlement' => 1), array('id' => $rush['id']));
			}
		}
		else {
			$res = 1;
		}

		return $res;
	}

	static public function halfsettlement($id)
	{
		global $_W;
		$order = pdo_get('wlmerchant_halfcard_record', array('id' => $id));

		if (empty($order['issettlement'])) {
			$res = self::settlement($order['id'], $order['typeid'], 0, $order['price'], $order['orderno'], 0, 4, $order['disorderid'], 0, $order['aid']);

			if ($res) {
				pdo_update('wlmerchant_halfcard_record', array('issettlement' => 1), array('id' => $order['id']));
			}
		}
		else {
			$res = 1;
		}

		return $res;
	}

	static public function ordersettlement($id)
	{
		global $_W;
		$order = pdo_get('wlmerchant_order', array('id' => $id));

		if (empty($order['issettlement'])) {
			if ($order['plugin'] == 'wlfightgroup') {
				$type = 2;
			}
			else if ($order['plugin'] == 'coupon') {
				$type = 3;
			}
			else if ($order['plugin'] == 'pocket') {
				$type = 5;
			}
			else if ($order['plugin'] == 'store') {
				$type = 6;
			}
			else if ($order['plugin'] == 'distribution') {
				$type = 8;
			}
			else if ($order['plugin'] == 'activity') {
				$type = 9;
			}
			else if ($order['plugin'] == 'groupon') {
				$type = 10;
			}
			else if ($order['plugin'] == 'halfcard') {
				$type = 11;
			}
			else {
				if ($order['plugin'] == 'bargain') {
					$type = 12;
				}
			}

			if ($order['shareid']) {
				$sharemoney = pdo_getcolumn(PDO_NAME . 'sharecurrent', array('shareid' => $order['shareid']), 'price');
			}

			if (empty($sharemoney)) {
				$sharemoney = 0;
			}

			$res = self::settlement($order['id'], $order['fkid'], $order['sid'], $order['price'], $order['orderno'], $order['settlementmoney'], $type, $order['disorderid'], $sharemoney, $order['aid']);

			if ($res) {
				pdo_update('wlmerchant_order', array('issettlement' => 1), array('id' => $order['id']));
			}
		}
		else {
			$res = 1;
		}

		return $res;
	}

	static public function doTask()
	{
		global $_W;
		pdo_delete(PDO_NAME . 'order', array('createtime <' => strtotime(date('Ymd')), 'plugin' => 'consumption', 'status' => 0, 'uniacid' => $_W['uniacid']));
		$sets = pdo_fetchall('select distinct aid from ' . tablename(PDO_NAME . 'oparea') . ('where uniacid = ' . $_W['uniacid'] . ' and status = 1'));

		foreach ($sets as $set) {
			$_W['aid'] = $set['aid'];
			if (empty($_W['aid']) || $_W['aid'] == -1) {
				continue;
			}

			$cashset = Setting::agentsetting_read('cashset');
			$cashsets = Setting::wlsetting_read('cashset');
			$rushorder = pdo_fetchall('SELECT id,sid,actualprice,activityid,orderno,disorderid,num,vipbuyflag,optionid,shareid,settlementmoney FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status IN (2,3) AND issettlement = 0 ORDER BY id ASC limit 10'));

			if ($rushorder) {
				foreach ($rushorder as $key => $rush) {
					if ($rush['shareid']) {
						$sharemoney = pdo_getcolumn(PDO_NAME . 'sharecurrent', array('shareid' => $rush['shareid']), 'price');
					}

					if (empty($sharemoney)) {
						$sharemoney = 0;
					}

					$res = self::settlement($rush['id'], $rush['activityid'], $rush['sid'], $rush['actualprice'], $rush['orderno'], $rush['settlementmoney'], 1, $rush['disorderid'], $sharemoney, $_W['aid']);

					if ($res) {
						pdo_update('wlmerchant_rush_order', array('issettlement' => 1), array('id' => $rush['id']));
					}
				}
			}

			$orders = pdo_fetchall('SELECT id,sid,price,fkid,orderno,plugin,num,disorderid,vipbuyflag,specid,expressid,shareid,settlementmoney FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status IN (2,3) AND issettlement = 0 AND orderno != 666666 ORDER BY id ASC limit 20'));

			if ($orders) {
				foreach ($orders as $key => $order) {
					if ($order['plugin'] == 'store') {
						$type = 6;
					}
					else if ($order['plugin'] == 'distribution') {
						$type = 8;
					}
					else if ($order['plugin'] == 'pocket') {
						$type = 5;
					}
					else if ($order['plugin'] == 'wlfightgroup') {
						$type = 2;
					}
					else if ($order['plugin'] == 'groupon') {
						$type = 10;
					}
					else if ($order['plugin'] == 'coupon') {
						$type = 3;
					}
					else if ($order['plugin'] == 'activity') {
						$type = 9;
					}
					else if ($order['plugin'] == 'halfcard') {
						$type = 11;
					}
					else {
						if ($order['plugin'] == 'bargain') {
							$type = 12;
						}
					}

					if ($order['shareid']) {
						$sharemoney = pdo_getcolumn(PDO_NAME . 'sharecurrent', array('shareid' => $order['shareid']), 'price');
					}

					if (empty($sharemoney)) {
						$sharemoney = 0;
					}

					$res = self::settlement($order['id'], $order['fkid'], $order['sid'], $order['price'], $order['orderno'], $order['settlementmoney'], $type, $order['disorderid'], $sharemoney, $_W['aid']);

					if ($res) {
						pdo_update('wlmerchant_order', array('issettlement' => 1), array('id' => $order['id']));
					}
				}
			}

			$halfcardorders = pdo_fetchall('SELECT id,typeid,price,orderno,disorderid FROM ' . tablename('wlmerchant_halfcard_record') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 1 AND issettlement = 0 ORDER BY id ASC limit 10'));

			if ($halfcardorders) {
				foreach ($halfcardorders as $key => $half) {
					$settlementmoney = 0;
					$type = 4;
					$res = self::settlement($half['id'], $half['typeid'], 0, $half['price'], $half['orderno'], $settlementmoney, $type, $half['disorderid'], 0, $_W['aid']);

					if ($res) {
						pdo_update('wlmerchant_halfcard_record', array('issettlement' => 1), array('id' => $half['id']));
					}
				}
			}

			if ($cashsets['noaudit']) {
				$allrecords = pdo_getall(PDO_NAME . 'settlement_record', array('status' => 2, 'uniacid' => $_W['uniacid']), array('id'));

				foreach ($allrecords as $key => $allrec) {
					$trade_no = time() . random(4, true);
					pdo_update(PDO_NAME . 'settlement_record', array('status' => 3, 'updatetime' => time(), 'trade_no' => $trade_no), array('id' => $allrec['id']));
				}

				$allrecords2 = pdo_getall(PDO_NAME . 'settlement_record', array('status' => 7, 'uniacid' => $_W['uniacid']), array('id'));

				if ($allrecords2) {
					foreach ($allrecords2 as $key => $allrec2) {
						$trade_no = time() . random(4, true);
						pdo_update(PDO_NAME . 'settlement_record', array('status' => 3, 'updatetime' => time(), 'trade_no' => $trade_no), array('id' => $allrec2['id']));
					}
				}
			}

			if (0 < $cashsets['autocash']) {
				$cashrecords = pdo_getall(PDO_NAME . 'settlement_record', array('status' => 3, 'type' => 1, 'uniacid' => $_W['uniacid'], 'payment_type' => 2));

				foreach ($cashrecords as $key => $cash) {
					if ($cash['sopenid']) {
						$autocash = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $cash['sid']), 'autocash');

						if ($autocash) {
							$realname = pdo_getcolumn(PDO_NAME . 'member', array('openid' => $cash['sopenid']), 'realname');
							$result1 = wlPay::finance($cash['sopenid'], $cash['sgetmoney'], '结算给商家', $realname, $cash['trade_no']);
							if ($result1['return_code'] == 'SUCCESS' && $result1['result_code'] == 'SUCCESS') {
								pdo_update(PDO_NAME . 'settlement_record', array('status' => 5, 'updatetime' => TIMESTAMP, 'settletype' => 2), array('id' => $cash['id']));
							}
						}
					}
				}
			}

			if (0 < $cashsets['agentautocash']) {
				$cashrecords = pdo_getall(PDO_NAME . 'settlement_record', array('status' => 3, 'type' => 2, 'uniacid' => $_W['uniacid'], 'payment_type' => 2));

				foreach ($cashrecords as $key => $cash) {
					if ($cash['sopenid']) {
						$realname = pdo_getcolumn(PDO_NAME . 'member', array('openid' => $cash['sopenid']), 'realname');
						$result2 = wlPay::finance($cash['sopenid'], $cash['sgetmoney'], '结算给代理', $realname, $cash['trade_no']);
						if ($result2['return_code'] == 'SUCCESS' && $result2['result_code'] == 'SUCCESS') {
							pdo_update(PDO_NAME . 'settlement_record', array('status' => 4, 'updatetime' => TIMESTAMP, 'settletype' => 2), array('id' => $cash['id']));
						}
					}
				}
			}

			if (0 < $cashsets['disautocash']) {
				$cashrecords = pdo_getall(PDO_NAME . 'settlement_record', array('status' => 3, 'type' => 3, 'uniacid' => $_W['uniacid'], 'payment_type' => 2));

				if ($cashrecords) {
					foreach ($cashrecords as $key => $cash) {
						if ($cash['sopenid']) {
							$realname = pdo_getcolumn(PDO_NAME . 'member', array('openid' => $cash['sopenid']), 'realname');
							$result2 = wlPay::finance($cash['sopenid'], $cash['sgetmoney'], '结算给分销商', $realname, $cash['trade_no']);
							if ($result2['return_code'] == 'SUCCESS' && $result2['result_code'] == 'SUCCESS') {
								pdo_update(PDO_NAME . 'settlement_record', array('status' => 9, 'updatetime' => TIMESTAMP, 'settletype' => 2), array('id' => $cash['id']));
							}
						}
					}
				}

				$cashrecords = pdo_getall(PDO_NAME . 'settlement_record', array('status' => 3, 'type' => 3, 'uniacid' => $_W['uniacid'], 'payment_type' => 4));

				if ($cashrecords) {
					foreach ($cashrecords as $key => $cash) {
						if ($cash['mid']) {
							$result = Member::credit_update_credit2($cash['mid'], $cash['sgetmoney'], '分销商余额提现');

							if ($result) {
								pdo_update(PDO_NAME . 'settlement_record', array('status' => 9, 'updatetime' => TIMESTAMP, 'settletype' => 4), array('id' => $cash['id']));
							}
						}
					}
				}
			}

			$errorcu = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_current') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 AND type IN (4,5,6,8) ORDER BY id DESC'));

			if ($errorcu) {
				foreach ($errorcu as $key => &$cu) {
					pdo_delete('wlmerchant_current', array('id' => $cu['id']));
				}
			}

			$nosettorder = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_rush_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND settlementmoney = 0 AND issettlement = 0 ORDER BY id DESC limit 10'));

			if ($nosettorder) {
				foreach ($nosettorder as $key => $noset) {
					$settlementmoney = self::getsettlementmoney(1, $noset['activityid'], $noset['num'], $noset['sid'], $noset['vipbuyflag'], $noset['optionid']);

					if (0 < $settlementmoney) {
						pdo_update('wlmerchant_rush_order', array('settlementmoney' => $settlementmoney), array('id' => $noset['id']));
					}
				}
			}

			$nowtime = time();
			$overmerchants = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_merchantdata') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND endtime < ' . $nowtime . ' AND enabled = 1 ORDER BY id DESC'));

			if ($overmerchants) {
				foreach ($overmerchants as $key => $over) {
					$res = pdo_update(PDO_NAME . 'merchantdata', array('enabled' => 3), array('uniacid' => $_W['uniacid'], 'id' => $over['id']));

					if ($res) {
						pdo_update('wlmerchant_rush_activity', array('status' => 4), array('uniacid' => $_W['uniacid'], 'sid' => $over['id']));
						pdo_update('wlmerchant_fightgroup_goods', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $over['id']));
						pdo_update('wlmerchant_couponlist', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $over['id']));
						pdo_update('wlmerchant_halfcardlist', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $over['id']));
						pdo_update('wlmerchant_package', array('status' => 0), array('uniacid' => $_W['uniacid'], 'merchantid' => $over['id']));
					}
				}
			}

			$settings = Setting::wlsetting_read('orderset');

			if (0 < $settings['autoapplyre']) {
				pdo_update('wlmerchant_order', array('status' => 6, 'applyrefund' => 2), array('status' => 1, 'applyrefund' => 1, 'uniacid' => $_W['uniacid']));
				pdo_update('wlmerchant_order', array('status' => 6, 'applyrefund' => 2), array('status' => 8, 'applyrefund' => 1, 'uniacid' => $_W['uniacid']));
				pdo_update('wlmerchant_rush_order', array('status' => 6, 'applyrefund' => 2), array('status' => 1, 'applyrefund' => 1, 'uniacid' => $_W['uniacid']));
				pdo_update('wlmerchant_rush_order', array('status' => 6, 'applyrefund' => 2), array('status' => 8, 'applyrefund' => 1, 'uniacid' => $_W['uniacid']));
			}

			if (0 < $settings['reovertime']) {
				$overorders = pdo_fetchall('SELECT id,recordid,plugin,fkid FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 6 AND failtimes < 3 ORDER BY id DESC'));

				if ($overorders) {
					foreach ($overorders as $key => $over) {
						if ($over['plugin'] == 'wlfightgroup') {
							$usedtime = pdo_getcolumn(PDO_NAME . 'fightgroup_userecord', array('id' => $over['recordid']), 'usedtime');
							$overrefund = pdo_getcolumn(PDO_NAME . 'fightgroup_goods', array('id' => $over['fkid']), 'overrefund');
							if (empty($usedtime) && $overrefund) {
								Wlfightgroup::refund($over['id']);
							}
						}
						else if ($over['plugin'] == 'groupon') {
							$usedtime = pdo_getcolumn(PDO_NAME . 'groupon_userecord', array('id' => $over['recordid']), 'usedtime');
							$overrefund = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $over['fkid']), 'overrefund');
							if (empty($usedtime) && $overrefund) {
								Groupon::refund($over['id']);
							}
						}
						else {
							if ($over['plugin'] == 'coupon') {
								$usedtime = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $over['recordid']), 'usedtime');
								$overrefund = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $over['fkid']), 'overrefund');
								if (empty($usedtime) && $overrefund) {
									wlCoupon::refund($over['id']);
								}
							}
						}
					}
				}
			}

			if ($settings['receipt']) {
				$receipttime = time() - $settings['receipt'] * 24 * 3600;
				$receiptorders = pdo_getall('wlmerchant_order', array('status' => 4, 'uniacid' => $_W['uniacid']), array('id', 'expressid', 'disorderid'));

				foreach ($receiptorders as $key => $order) {
					if ($order['expressid']) {
						$express = pdo_get('wlmerchant_express', array('id' => $order['expressid']), array('sendtime'));

						if ($express['sendtime'] < $receipttime) {
							pdo_update('wlmerchant_order', array('status' => 2), array('id' => $order['id']));
							pdo_update('wlmerchant_express', array('receivetime' => time()), array('id' => $order['expressid']));

							if ($order['disorderid']) {
								pdo_update('wlmerchant_disorder', array('status' => 1), array('status' => 0, 'id' => $order['disorderid']));
							}
						}
					}
				}

				$receiptrushorders = pdo_getall('wlmerchant_rush_order', array('status' => 4, 'uniacid' => $_W['uniacid']), array('id', 'expressid', 'disorderid'));

				foreach ($receiptrushorders as $key => $rushorder) {
					if ($rushorder['expressid']) {
						$express = pdo_get('wlmerchant_express', array('id' => $rushorder['expressid']), array('sendtime'));

						if ($express['sendtime'] < $receipttime) {
							pdo_update('wlmerchant_rush_order', array('status' => 2), array('id' => $rushorder['id']));
							pdo_update('wlmerchant_express', array('receivetime' => time()), array('id' => $rushorder['expressid']));

							if ($rushorder['disorderid']) {
								pdo_update('wlmerchant_disorder', array('status' => 1), array('status' => 0, 'id' => $rushorder['disorderid']));
							}
						}
					}
				}

				$receiptconsumptions = pdo_getall('wlmerchant_consumption_record', array('status' => 2, 'uniacid' => $_W['uniacid']), array('id', 'orderid', 'expressid'));

				if ($receiptconsumptions) {
					foreach ($receiptconsumptions as $key => $consum) {
						if ($consum['expressid']) {
							$express = pdo_get('wlmerchant_express', array('id' => $consum['expressid']), array('sendtime'));

							if ($express['sendtime'] < $receipttime) {
								pdo_update('wlmerchant_consumption_record', array('status' => 3), array('id' => $consum['id']));
								pdo_update('wlmerchant_express', array('receivetime' => time()), array('id' => $consum['expressid']));
								$consum['disorderid'] = pdo_getcolumn(PDO_NAME . 'order', array('id' => $consum['orderid']), 'disorderid');

								if ($consum['disorderid']) {
									pdo_update('wlmerchant_disorder', array('status' => 1), array('status' => 0, 'id' => $consum['disorderid']));
								}
							}
						}
					}
				}
			}

			pdo_update('wlmerchant_verifrecord', array('num' => 1), array('num' => 0));
		}
	}

	static public function addFans($sid = '', $mid = '', $source = 1)
	{
		global $_W;
		$mid = $mid ? $mid : $_W['mid'];
		if (empty($sid) || empty($mid)) {
			return false;
		}

		$fansst = pdo_getcolumn(PDO_NAME . 'storefans', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'mid' => $mid), 'id');

		if (empty($fansst)) {
			pdo_insert(PDO_NAME . 'storefans', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'mid' => $mid, 'createtime' => time(), 'source' => $source));
			$collect = pdo_getcolumn(PDO_NAME . 'collect', array('uniacid' => $_W['uniacid'], 'storeid' => $sid, 'mid' => $mid), 'id');

			if (empty($collect)) {
				pdo_insert(PDO_NAME . 'collect', array('uniacid' => $_W['uniacid'], 'storeid' => $sid, 'mid' => $mid, 'createtime' => time()));
			}

			$admin = pdo_get(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'storeid' => $sid, 'ismain' => 1));
			$member = Member::getMemberByMid($admin['mid'], array('openid'));

			if (!empty($member['openid'])) {
				$fans = Member::getMemberByMid($mid, array('nickname'));
				Message::jobNotice($member['openid'], '您好，' . $fans['nickname'] . '刚刚关注了您的店铺，成为了您的客户！', '客户关注', '关注成功', '点击查看详情，祝您财源广进~~', app_url('store/supervise/switchstore', array('storeid' => $sid, 'url' => urlencode(app_url('store/supervise/fans')))));
			}
		}
	}

	static public function toadmin($goodsname, $storeid, $pluginname)
	{
		global $_W;
		$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $storeid), 'storename');

		if ($pluginname == 'rush') {
			$first = '一个抢购商品待审核,请尽快在后台审核';
		}
		else if ($pluginname == 'fightgroup') {
			$first = '一个拼团商品待审核,请尽快在后台审核';
		}
		else if ($pluginname == 'coupon') {
			$first = '一个超级券待审核,请尽快在后台审核';
		}
		else if ($pluginname == 'halfcard') {
			$first = '一个特权折扣待审核,请尽快在后台审核';
		}
		else if ($pluginname == 'package') {
			$first = '一个大礼包待审核,请尽快在后台审核';
		}
		else {
			if ($pluginname == 'bargain') {
				$first = '一个砍价商品待审核,请尽快在后台审核';
			}
		}

		$keyword1 = '名称:' . $goodsname;
		$keyword2 = '待审核';
		$remark = '所属商家:' . $storename;
		$url = app_url('dashboard/home/index');
		$openids = pdo_getall('wlmerchant_agentadmin', array('aid' => $_W['aid'], 'notice' => 1), array('openid'));

		if ($openids) {
			foreach ($openids as $key => $member) {
				Message::jobNotice($member['openid'], $first, $keyword1, $keyword2, $remark, $url);
			}
		}
	}

	/**
     * Comment: 获取当前店铺店长的信息
     * Author: zzw
     * @param $sid   店铺id
     * @return bool  店长信息
     */
	static public function getShopOwnerInfo($sid, $aid)
	{
		global $_W;
		$aid = $aid ? $aid : $_W['aid'];
		$shopownerInfo = pdo_fetch('SELECT * FROM ' . tablename(PDO_NAME . 'merchantdata') . ' a LEFT JOIN  ' . tablename(PDO_NAME . 'merchantuser') . ' b  ON a.id = b.storeid LEFT JOIN ' . tablename(PDO_NAME . 'member') . (' m ON b.mid = m.id WHERE a.id = ' . $sid . ' AND b.uniacid = ' . $_W['uniacid'] . ' AND b.aid = ' . $aid . ' AND b.ismain = 1'));
		return $shopownerInfo;
	}

	/**
     * Comment: 获取一定时间段内的店铺销量
     * Author: zzw
     * @param $id   店铺id
     * @param $month  月时间，不能与天时间同时存在
     * @param $day    天时间，不能与月时间同时存在
     * @return bool   返回时间内的总销量
     */
	static public function getShopSales($id, $month, $day)
	{
		$where = ' WHERE status IN (1,2,3,4) AND sid = ' . $id . ' ';

		if ($month) {
			$where .= ' AND paytime > ' . strtotime('-' . $month . ' month');
		}
		else {
			if ($day) {
				$where .= ' AND paytime > ' . strtotime('-' . $day . ' day');
			}
		}

		$rushSum = pdo_fetchcolumn('SELECT sum(num) FROM ' . tablename(PDO_NAME . 'rush_order') . $where);
		$orderSum = pdo_fetchcolumn('SELECT sum(num) FROM ' . tablename(PDO_NAME . 'order') . $where);
		return $rushSum + $orderSum;
	}

	/**
     * Comment: 获取店铺定位并且计算距离
     * Author: zzw
     * @param $sid   店铺id
     * @param $lng   经度
     * @param $lat   纬度
     * @return int|string   返回当前位置到店铺的距离
     */
	static public function shopLocation($sid, $lng, $lat)
	{
		$location = unserialize(pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $sid), 'location'));
		$distance = self::getdistance($location['lng'], $location['lat'], $lng, $lat);

		if (empty($distance)) {
			$distance = 99999999;
		}

		if (!empty($distance)) {
			if (99999998 < $distance) {
				$distance = ' ';
			}
			else if (1000 < $distance) {
				$distance = floor($distance / 1000 * 10) / 10 . 'km';
			}
			else {
				$distance = round($distance) . 'm';
			}
		}

		return $distance;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
