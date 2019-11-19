<?php
//dezend by http://www.sucaihuo.com/
class Area
{
	static public function getAllAgent($page = 0, $pagenum = 10, $con = '')
	{
		global $_W;
		$condition = '';
		if (!empty($con) && is_array($con)) {
			foreach ($con as $key => $val) {
				if ($key == 'username') {
					$condition .= ' and ' . $key . ' like \'%' . $val . '%\'';
				}

				if ($key == 'groupid') {
					$condition .= ' and ' . $key . '=' . $val;
				}

				if ($key == 'status') {
					$condition .= ' and ' . $key . '=' . $val;
				}

				if ($key == 'agentname') {
					$condition .= ' and (' . $key . ' like \'%' . $val . '%\' or mobile like \'%' . $val . '%\' or realname like \'%' . $val . '%\')';
				}
			}
		}

		$re['data'] = pdo_fetchall('select * from' . tablename(PDO_NAME . 'agentusers') . 'where uniacid=:uniacid  ' . $condition . ' order by groupid desc, starttime desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid']));
		$re['count'] = pdo_fetchcolumn('select count(*) from' . tablename(PDO_NAME . 'agentusers') . 'where uniacid=:uniacid  ' . $condition, array(':uniacid' => $_W['uniacid']));
		return $re;
	}

	static public function getSingleAgent($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		$re = pdo_get(PDO_NAME . 'agentusers', array('id' => $id, 'uniacid' => $_W['uniacid']));
		$re['percent'] = unserialize($re['percent']);
		return $re;
	}

	static public function editAgent($arr, $id = '')
	{
		global $_W;

		if (empty($arr)) {
			return false;
		}

		if (empty($id)) {
			$arr['uniacid'] = $_W['uniacid'];
			pdo_insert(PDO_NAME . 'agentusers', $arr);
			$id = pdo_insertid();
		}
		else {
			pdo_update(PDO_NAME . 'agentusers', $arr, array('id' => $id, 'uniacid' => $_W['uniacid']));
		}

		return $id;
	}

	static public function deleteAgent($aid)
	{
		global $_W;

		if (empty($aid)) {
			return false;
		}

		$areaids = pdo_getall(PDO_NAME . 'oparea', array('aid' => $aid), 'id');

		if (pdo_delete(PDO_NAME . 'oparea', array('uniacid' => $_W['uniacid'], 'aid' => $aid))) {
			return pdo_delete(PDO_NAME . 'agentusers', array('uniacid' => $_W['uniacid'], 'id' => $aid));
		}

		return false;
	}

	static public function getAllGroup($page = 0, $pagenum = 10, $enabled = '')
	{
		global $_W;
		$condition = '';
		if (!empty($enabled) && $enabled != '') {
			$condition = ' and enabled=' . $enabled;
		}

		$re['data'] = pdo_fetchall('select * from' . tablename(PDO_NAME . 'agentusers_group') . 'where uniacid=:uniacid  ' . $condition . ' order by enabled desc, createtime desc limit ' . $page * $pagenum . ',' . $pagenum, array(':uniacid' => $_W['uniacid']));

		foreach ($re['data'] as $key => &$value) {
			if (!empty($value['package'])) {
				$value['package'] = iunserializer($value['package']);
			}
		}

		$re['count'] = pdo_fetchcolumn('select count(*) from' . tablename(PDO_NAME . 'agentusers_group') . 'where uniacid=:uniacid  ' . $condition, array(':uniacid' => $_W['uniacid']));
		return $re;
	}

	static public function getSingleGroup($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		$group = pdo_get(PDO_NAME . 'agentusers_group', array('id' => $id, 'uniacid' => $_W['uniacid']));

		if (!empty($group)) {
			$group['package'] = iunserializer($group['package']);
		}

		return $group;
	}

	static public function editGroup($arr, $id = '')
	{
		global $_W;

		if (empty($arr)) {
			return false;
		}

		if ($arr['isdefault'] == 1) {
			pdo_update(PDO_NAME . 'agentusers_group', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'isdefault' => 1));
		}

		if (!empty($id) && $id != '') {
			return pdo_update(PDO_NAME . 'agentusers_group', $arr, array('id' => $id, 'uniacid' => $_W['uniacid']));
		}

		$arr['uniacid'] = $_W['uniacid'];
		return pdo_insert(PDO_NAME . 'agentusers_group', $arr);
	}

	static public function deleteGroup($id)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		$isuse = pdo_getcolumn(PDO_NAME . 'agentusers', array('groupid' => $id, 'uniacid' => $_W['uniacid']), 'id');

		if (!empty($isuse)) {
			return false;
		}

		return pdo_delete(PDO_NAME . 'agentusers_group', array('id' => $id, 'uniacid' => $_W['uniacid']));
	}

	static public function address_tree_in_use()
	{
		global $_W;
		$provinces = pdo_getall(PDO_NAME . 'area', array(
			'visible'      => 2,
			'level'        => 1,
			'displayorder' => array('0', $_W['uniacid'])
		), array('id', 'name'), 'id');
		$cities = pdo_getall(PDO_NAME . 'area', array(
			'visible'      => 2,
			'level'        => 2,
			'displayorder' => array('0', $_W['uniacid'])
		), array('id', 'pid', 'name'), 'id');
		$districts = pdo_getall(PDO_NAME . 'area', array(
			'visible'      => 2,
			'level'        => 3,
			'displayorder' => array('0', $_W['uniacid'])
		), array('id', 'pid', 'name'), 'id');
		$towns = pdo_getall(PDO_NAME . 'area', array(
			'visible'      => 2,
			'level'        => 4,
			'displayorder' => array('0', $_W['uniacid'])
		), array('id', 'pid', 'name'), 'id');
		$address_tree = array();

		foreach ($provinces as $province_id => $province) {
			$address_tree[$province_id] = array(
				'title'  => $province['name'],
				'cities' => array()
			);

			foreach ($cities as $city_id => $city) {
				if ($city['pid'] == $province_id) {
					$address_tree[$province_id]['cities'][$city_id] = array(
						'title'     => $city['name'],
						'districts' => array()
					);

					foreach ($districts as $district_id => $district) {
						if ($district['pid'] == $city_id) {
							$address_tree[$province_id]['cities'][$city_id]['districts'][$district_id] = array(
								'title' => $district['name'],
								'towns' => array()
							);

							foreach ($towns as $town_id => $town) {
								if ($town['pid'] == $district_id) {
									$address_tree[$province_id]['cities'][$city_id]['districts'][$district_id]['towns'][$town_id] = $town['name'];
								}
							}
						}
					}
				}
			}
		}

		return $address_tree;
	}

	static public function get_all_in_use($type = 0)
	{
		global $_W;
		$area = pdo_get(PDO_NAME . 'oparea', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));

		if ($area['level'] == 1) {
			$address_tree[$area['areaid']] = array(
				'title'  => pdo_getcolumn(PDO_NAME . 'area', array('id' => $area['areaid']), 'name'),
				'cities' => array()
			);
			$cities = pdo_getall(PDO_NAME . 'area', array('pid' => $area['areaid']), array('id', 'name'));

			foreach ($cities as $key => $value) {
				$address_tree[$area['areaid']]['cities'][$value['id']] = array(
					'title'     => $value['name'],
					'districts' => array()
				);
				$districts = pdo_getall(PDO_NAME . 'area', array('pid' => $value['id']), array('id', 'name'));

				foreach ($districts as $district_id => $district) {
					$address_tree[$area['areaid']]['cities'][$value['id']]['districts'][$district['id']] = $district['name'];
				}
			}
		}
		else if ($area['level'] == 2) {
			$provinceid = pdo_getcolumn(PDO_NAME . 'area', array('id' => $area['areaid']), 'pid');
			$address_tree[$provinceid] = array(
				'title'  => pdo_getcolumn(PDO_NAME . 'area', array('id' => $provinceid), 'name'),
				'cities' => array()
			);
			$address_tree[$provinceid]['cities'][$area['areaid']] = array(
				'title'     => pdo_getcolumn(PDO_NAME . 'area', array('id' => $area['areaid']), 'name'),
				'districts' => array()
			);
			$districts = pdo_getall(PDO_NAME . 'area', array('pid' => $area['areaid']), array('id', 'name'));

			foreach ($districts as $district_id => $district) {
				$address_tree[$provinceid]['cities'][$area['areaid']]['districts'][$district['id']] = $district['name'];
			}
		}
		else {
			$cityid = pdo_getcolumn(PDO_NAME . 'area', array('id' => $area['areaid']), 'pid');
			$provinceid = pdo_getcolumn(PDO_NAME . 'area', array('id' => $cityid), 'pid');
			$address_tree[$provinceid] = array(
				'title'  => pdo_getcolumn(PDO_NAME . 'area', array('id' => $provinceid), 'name'),
				'cities' => array()
			);
			$address_tree[$provinceid]['cities'][$cityid] = array(
				'title'     => pdo_getcolumn(PDO_NAME . 'area', array('id' => $cityid), 'name'),
				'districts' => array()
			);
			$address_tree[$provinceid]['cities'][$cityid]['districts'][$area['areaid']] = pdo_getcolumn(PDO_NAME . 'area', array('id' => $area['areaid']), 'name');
		}

		if ($type == 1) {
			$address_tree = array_values($address_tree);

			foreach ($address_tree as $key => &$value) {
				$value['name'] = $value['title'];
				$value['sub'] = array_values($value['cities']);
				unset($value['title']);
				unset($value['cities']);

				foreach ($value['sub'] as $key1 => &$value1) {
					$value1['name'] = $value1['title'];
					$value1['sub'] = array_values($value1['districts']);
					unset($value1['title']);
					unset($value1['districts']);

					foreach ($value1['sub'] as $key2 => &$value2) {
						$value2 = array('name' => $value2);
					}
				}
			}
		}

		return $address_tree;
	}

	static public function get_all_wx_use()
	{
		global $_W;
		$address_tree = self::address_tree_in_use();
		$address_tree = array_values($address_tree);

		foreach ($address_tree as $key => &$value) {
			$value['name'] = $value['title'];
			$value['sub'] = array_values($value['cities']);
			unset($value['title']);
			unset($value['cities']);

			foreach ($value['sub'] as $key1 => &$value1) {
				$value1['name'] = $value1['title'];
				$value1['sub'] = array_values($value1['districts']);
				unset($value1['title']);
				unset($value1['districts']);

				foreach ($value1['sub'] as $key2 => &$value2) {
					$value2 = array('name' => $value2['title']);
					unset($value2['title']);
					unset($value2['districts']);
				}
			}
		}

		return $address_tree;
	}

	static public function get_agent_area($aid = '')
	{
		global $_W;
		$data = array('uniacid' => $_W['uniacid']);

		if (!empty($aid)) {
			$data['aid'] = $aid;
		}

		$terarea = pdo_getall(PDO_NAME . 'oparea', $data, 'areaid');
		$terarea = Util::i_array_column($terarea, 'areaid');
		return $terarea;
	}

	static public function save_agent_area($districts, $level, $aid)
	{
		global $_W;
		global $_GPC;
		if (empty($districts) || $level == 1 && empty($districts['province']) || $level == 2 && empty($districts['city']) || $level == 3 && empty($districts['district']) || $level == 4 && empty($districts['town'])) {
			wl_message('请选择代理地区');
		}

		$data = array('uniacid' => $_W['uniacid'], 'aid' => $aid, 'level' => $level, 'status' => intval($_GPC['status']));

		switch ($level) {
		case '1':
			$data['areaid'] = $districts['province'];
			break;

		case '2':
			$data['areaid'] = $districts['city'];
			break;

		case '4':
			$data['areaid'] = $districts['town'];
			break;

		default:
			$data['areaid'] = $districts['district'];
			break;
		}

		$hasarea = pdo_getcolumn(PDO_NAME . 'oparea', array('uniacid' => $_W['uniacid'], 'aid !=' => $aid, 'areaid' => $data['areaid']), 'id');

		if ($hasarea) {
			wl_message('当前地区已被代理，请重新选择地区');
		}

		$opareaid = pdo_getcolumn(PDO_NAME . 'oparea', array('uniacid' => $_W['uniacid'], 'aid' => $aid), 'id');

		if ($opareaid) {
			pdo_update(PDO_NAME . 'oparea', $data, array('id' => $opareaid));
		}
		else {
			pdo_insert(PDO_NAME . 'oparea', $data);
		}

		return true;
	}

	static public function get_all_area($type = '')
	{
		global $_W;
		$address_tree = array();
		$terarea = pdo_getall(PDO_NAME . 'oparea', array('uniacid' => $_W['uniacid'], 'status' => 1), array('areaid', 'aid'));
		$terareas = Util::i_array_column($terarea, 'areaid');

		if ($type == 1) {
			foreach ($terarea as $key => $val) {
				$name = pdo_getcolumn(PDO_NAME . 'area', array('id' => $val['areaid']), 'name');
				$address_tree[$key] = array('id' => $val['areaid'], 'name' => $name, 'aid' => $val['aid']);
			}

			return $address_tree;
		}

		$provinces = pdo_getall(PDO_NAME . 'area', array(
			'visible'      => 2,
			'level'        => 1,
			'displayorder' => array('0', $_W['uniacid'])
		), array('id', 'name'));
		$cities = pdo_getall(PDO_NAME . 'area', array(
			'visible'      => 2,
			'level'        => 2,
			'displayorder' => array('0', $_W['uniacid'])
		), array('id', 'pid', 'name'));

		foreach ($provinces as $province_id => $province) {
			$address_tree[$province_id] = array(
				'id'       => $province['id'],
				'name'     => $province['name'],
				'children' => array()
			);

			foreach ($cities as $city_id => $city) {
				if (@in_array($city['id'], $terareas)) {
					if ($city['pid'] == $province['id']) {
						$address_tree[$province_id]['children'][$city_id] = array('id' => $city['id'], 'name' => $city['name']);
					}
				}
			}

			if (empty($address_tree[$province_id]['children'])) {
				unset($address_tree[$province_id]);
			}
		}

		return $address_tree;
	}

	static public function get_area()
	{
		global $_W;
		$maera = Util::httpPost('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $_W['clientip']);
		$maera = Util::object_array(json_decode($maera));
		$allarea = self::get_all_area(1);

		if (count($allarea) == 1) {
			$areaid = $allarea[0]['id'];
			$name = $allarea[0]['name'];
		}
		else {
			if (!empty($maera['district'])) {
				foreach ($allarea as $key => $val) {
					if (mb_substr($maera['district'], 0, 2, 'utf-8') == mb_substr($val['name'], 0, 2, 'utf-8')) {
						$areaid = $val['id'];
						$name = $val['name'];
						break;
					}
				}
			}

			if (empty($areaid)) {
				foreach ($allarea as $key => $val) {
					if (mb_substr($maera['city'], 0, 2, 'utf-8') == mb_substr($val['name'], 0, 2, 'utf-8')) {
						$areaid = $val['id'];
						$name = $val['name'];
						break;
					}
				}
			}
		}

		return array('id' => $areaid, 'name' => $name, 'lc' => $maera['district'] ? $maera['city'] . $maera['district'] : $maera['province'] . $maera['city']);
	}

	static public function getIdByName($name)
	{
		global $_W;

		if (empty($name)) {
			return false;
		}

		$re = pdo_get(PDO_NAME . 'area', array('name' => $name), 'id');
		return $re['id'];
	}

	static public function getAreaNameById($id, $type = 0)
	{
		global $_W;

		if (empty($id)) {
			return false;
		}

		if ($type == 0) {
			$city = pdo_getcolumn(PDO_NAME . 'area', array('id' => $id), 'name');
			$proId = intval($id / 10000) * 10000;
			$pro = pdo_getcolumn(PDO_NAME . 'area', array('id' => $proId), 'name');
			return $pro . '-' . $city;
		}
	}

	static public function initAgent()
	{
		global $_W;

		if (empty($_W['uniacid'])) {
			return false;
		}

		$oparea = array('uniacid' => $_W['uniacid'], 'areaid' => 110100, 'aid' => 0, 'ishot' => 1, 'level' => 2, 'status' => 1);
		$default = pdo_getcolumn(PDO_NAME . 'oparea', array('uniacid' => $_W['uniacid'], 'aid' => 0), 'id');

		if (empty($default)) {
			$all = pdo_get(PDO_NAME . 'oparea', array('uniacid' => $_W['uniacid']), 'id');

			if (!empty($all)) {
				$oparea['status'] = 0;
			}

			pdo_insert(PDO_NAME . 'oparea', $oparea);
		}

		return true;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
