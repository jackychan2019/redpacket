<?php
//dezend by http://www.sucaihuo.com/
class Halfcard
{
	static public function checkFollow($cardid)
	{
		global $_W;

		if ($_W['fans']['follow'] != 1) {
			$showurl = !empty($_W['wlsetting']['share']['gz_image']) ? $_W['wlsetting']['share']['gz_image'] : 'qrcode_' . $_W['acid'] . '.jpg';
			pdo_insert('wlmerchant_halfcard_qrscan', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'scantime' => time(), 'cardid' => $cardid));
			include wl_template('newcard/qrcode');
			exit();
		}
	}

	static public function saveHalfcard($halfcard, $param = array())
	{
		global $_W;

		if (!is_array($halfcard)) {
			return false;
		}

		$halfcard['uniacid'] = $_W['uniacid'];
		$halfcard['aid'] = $_W['aid'];

		if (empty($param)) {
			pdo_insert(PDO_NAME . 'halfcardlist', $halfcard);
			return pdo_insertid();
		}

		return false;
	}

	static public function deleteHalfcard($where)
	{
		$res = pdo_delete(PDO_NAME . 'halfcardlist', $where);

		if ($res) {
			return 1;
		}

		return 0;
	}

	static public function deleteHalfcardRecord($where)
	{
		$res = pdo_delete(PDO_NAME . 'timecardrecord', $where);

		if ($res) {
			return 1;
		}

		return 0;
	}

	static public function getSingleHalfcard($id, $select, $where = array())
	{
		$where['id'] = $id;
		return Util::getSingelData($select, PDO_NAME . 'halfcardlist', $where);
	}

	static public function getSingleMember($id, $select, $where = array())
	{
		$where['mid'] = $id;
		return Util::getSingelData($select, PDO_NAME . 'halfcardmember', $where);
	}

	static public function updateHalfcard($params, $where)
	{
		$res = pdo_update(PDO_NAME . 'halfcardlist', $params, $where);

		if ($res) {
			return 1;
		}

		return 0;
	}

	static public function getNumRecord($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'halfcardrecord', $where, $order, $pindex, $psize, $ifpage);
		$newGoodInfo = $newGoodInfo ? $newGoodInfo : array();
		return array($newGoodInfo, $goodsInfo[1], $goodsInfo[2]) ? array($newGoodInfo, $goodsInfo[1], $goodsInfo[2]) : array();
	}

	static public function checklevel($mid, $levels)
	{
		global $_W;
		$now = time();

		if ($_W['wlsetting']['halfcard']['halfcardtype'] == 2) {
			$cards = pdo_fetchall('SELECT levelid FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $mid . ' AND aid = ' . $_W['aid'] . ' AND expiretime > ' . $now . ' AND disable != 1'));
		}
		else {
			$cards = pdo_fetchall('SELECT levelid FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $mid . ' AND expiretime > ' . $now . ' AND disable != 1'));
		}

		$flag = 0;

		if ($cards) {
			foreach ($cards as $key => $cs) {
				if (in_array($cs['levelid'], $levels)) {
					$flag = 1;
				}
			}
		}

		return $flag;
	}

	static public function getNumstore($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'merchantdata', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	static public function getstores($locations, $lng, $lat)
	{
		global $_W;
		if (empty($lat) || empty($lng)) {
			return false;
		}

		foreach ($locations as $key => $val) {
			$loca = unserialize($val['location']);
			$locations[$key]['distance'] = Store::getdistance($loca['lng'], $loca['lat'], $lng, $lat);
		}

		$i = 0;

		while ($i < count($locations) - 1) {
			$j = 0;

			while ($j < count($locations) - 1 - $i) {
				if ($locations[$j + 1]['distance'] < $locations[$j]['distance']) {
					$temp = $locations[$j + 1];
					$locations[$j + 1] = $locations[$j];
					$locations[$j] = $temp;
				}

				++$j;
			}

			++$i;
		}

		foreach ($locations as $key => $value) {
			if (1000 < $value['distance']) {
				$locations[$key]['distance'] = floor($value['distance'] / 1000 * 10) / 10 . 'km';
			}
			else {
				$locations[$key]['distance'] = round($value['distance']) . 'm';
			}
		}

		return $locations;
	}

	static public function payHalfcardNotify($params)
	{
		global $_W;
		Util::wl_log('vip_notify', PATH_DATA . 'merchant/data/', $params);
		$data = self::getVipPayData($params);
		pdo_update(PDO_NAME . 'halfcard_record', $data, array('orderno' => $params['tid']));
		$order_out = pdo_get(PDO_NAME . 'halfcard_record', array('orderno' => $params['tid']));
		$memberData = array('halfcardstatus' => 1, 'lasthalfcardtime' => $order_out['limittime'], 'areaid' => $order_out['areaid'], 'aid' => $order_out['aid']);
		pdo_update(PDO_NAME . 'member', $memberData, array('id' => $order_out['mid']));
	}

	static public function payHalfcardReturn($params)
	{
		global $_W;
		Util::wl_log('Vip_return', PATH_DATA . 'merchant/data/', $params);
		$order_out = pdo_get(PDO_NAME . 'halfcard_record', array('orderno' => $params['tid']), array('id'));
		header('location:' . app_url('halfcard/halfcardopen/openSuccess', array('orderid' => $order_out['id'])));
	}

	static public function getNumActive($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$activeInfo = Util::getNumData($select, PDO_NAME . 'halfcardlist', $where, $order, $pindex, $psize, $ifpage);
		return $activeInfo;
	}

	static public function getNumPackActive($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$activeInfo = Util::getNumData($select, PDO_NAME . 'package', $where, $order, $pindex, $psize, $ifpage);
		return $activeInfo;
	}

	static public function getNumActive1($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$activeInfo = Util::getNumData($select, PDO_NAME . 'halfcardrecord', $where, $order, $pindex, $psize, $ifpage);
		return $activeInfo;
	}

	static public function getNumActive2($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$activeInfo = Util::getNumData($select, PDO_NAME . 'timecardrecord', $where, $order, $pindex, $psize, $ifpage);
		return $activeInfo;
	}

	static public function getNumhalfcardmember($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$activeInfo = Util::getNumData($select, PDO_NAME . 'halfcardmember', $where, $order, $pindex, $psize, $ifpage);
		return $activeInfo;
	}

	static public function getNumhalfcardpay($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$activeInfo = Util::getNumData($select, PDO_NAME . 'halfcard_record', $where, $order, $pindex, $psize, $ifpage);
		return $activeInfo;
	}

	static public function doTask()
	{
		global $_W;
		global $_GPC;
		$settings = Setting::wlsetting_read('halfcard');

		if ($settings['urlstatus'] == 1) {
			$realcard = pdo_getall(PDO_NAME . 'halfcard_realcard', array('uniacid' => $_W['uniacid'], 'url' => ''), array('id', 'cardsn', 'salt'), '', '', 100);

			foreach ($realcard as $key => $value) {
				$result = Util::long2short(app_url('halfcard/halfcard_app/realcard', array('cardsn' => $value['cardsn'], 'salt' => $value['salt'])));
				if (!is_error($result) && $result['short_url'] != 'h') {
					pdo_update(PDO_NAME . 'halfcard_realcard', array('url' => $result['short_url']), array('id' => $value['id']));
				}
			}
		}

		$nowtime = time();
		$datepackage = pdo_fetchall('SELECT status,id FROM ' . tablename('wlmerchant_package') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 AND packtimestatus = 1 AND dateendtime < ' . $nowtime . ' ORDER BY id DESC'));

		if ($datepackage) {
			foreach ($datepackage as $key => $package) {
				pdo_update('wlmerchant_package', array('status' => 0), array('id' => $package['id']));
			}
		}

		$datehalf = pdo_fetchall('SELECT status,id FROM ' . tablename('wlmerchant_halfcardlist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 AND timingstatus = 1 AND endtime < ' . $nowtime . ' ORDER BY id DESC'));

		if ($datehalf) {
			foreach ($datehalf as $key => $halfcard) {
				pdo_update('wlmerchant_halfcardlist', array('status' => 0), array('id' => $halfcard['id']));
			}
		}

		$datepack = pdo_fetchall('SELECT status,id FROM ' . tablename('wlmerchant_package') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 0 AND packtimestatus = 1 AND datestarttime < ' . $nowtime . ' AND dateendtime > ' . $nowtime . ' ORDER BY id DESC'));

		if ($datepack) {
			foreach ($datepack as $key => $pack) {
				pdo_update('wlmerchant_package', array('status' => 1), array('id' => $pack['id']));
			}
		}

		$datehalfcard = pdo_fetchall('SELECT status,id FROM ' . tablename('wlmerchant_halfcardlist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 0 AND timingstatus = 1 AND starttime < ' . $nowtime . ' AND endtime > ' . $nowtime . ' ORDER BY id DESC'));

		if ($datehalfcard) {
			foreach ($datehalfcard as $key => $half) {
				pdo_update('wlmerchant_halfcardlist', array('status' => 1), array('id' => $half['id']));
			}
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
