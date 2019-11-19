<?php
//dezend by http://www.sucaihuo.com/
class Couponlist_WeliamController
{
	public function couponsList()
	{
		global $_W;
		global $_GPC;
		$set = Setting::agentsetting_read('coupon');
		$diy = unserialize($set['coupon']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$wheres = array();
		$wheres['uniacid'] = $_W['uniacid'];
		$wheres['aid'] = $_W['aid'];
		$status = $_GPC['status'];

		if ($status) {
			if ($status == 5) {
				$wheres['status'] = 0;
			}
			else {
				$wheres['status'] = $status;
			}
		}

		if ($_GPC['type']) {
			$wheres['type'] = $_GPC['type'];
		}

		if ($_GPC['keywordtype'] == 1) {
			$wheres['@title@'] = trim($_GPC['keyword']);
		}

		if ($_GPC['keywordtype'] == 2) {
			$keyword = $_GPC['keyword'];
			$params[':storename'] = '%' . $keyword . '%';
			$merchants = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_merchantdata') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND storename LIKE :storename'), $params);

			if ($merchants) {
				$sids = '(';

				foreach ($merchants as $key => $v) {
					if ($key == 0) {
						$sids .= $v['id'];
					}
					else {
						$sids .= ',' . $v['id'];
					}
				}

				$sids .= ')';
				$wheres['merchantid#'] = $sids;
			}
			else {
				$wheres['merchantid#'] = '(0)';
			}
		}

		if ($_GPC['time_limit'] && $_GPC['timetype']) {
			$time_limit = $_GPC['time_limit'];
			$starttime = strtotime($_GPC['time_limit']['start']);
			$endtime = strtotime($_GPC['time_limit']['end']);
			$wheres['starttime>'] = $starttime;
			$wheres['endtime<'] = $endtime;
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		$coupons = wlCoupon::getNumCoupons('*', $wheres, 'indexorder DESC', $pindex, $psize, 1);
		$pager = $coupons[1];
		$coupons = $coupons[0];

		foreach ($coupons as $key => &$value) {
			$coupons[$key]['discount'] = $coupons[$key]['discount'] / 10;
			$detail = pdo_get('wlmerchant_merchantdata', array('aid' => $_W['aid'], 'id' => $value['merchantid']));
			$coupons[$key]['storename'] = $detail['storename'];
		}

		$status0 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE uniacid=' . $_W['uniacid'] . ' and aid=' . $_W['aid']));
		$status1 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=1 and aid=' . $_W['aid']));
		$status2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=2 and aid=' . $_W['aid']));
		$status3 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=3 and aid=' . $_W['aid']));
		$status4 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=4 and aid=' . $_W['aid']));
		$status5 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=0 and aid=' . $_W['aid']));
		include wl_template('coupon/coupon_list');
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$status = $_GPC['status'];

		if ($status == 1) {
			$res = pdo_update('wlmerchant_couponlist', array('status' => 0), array('id' => $id));
		}
		else {
			$res = pdo_update('wlmerchant_couponlist', array('status' => 1), array('id' => $id));
		}

		if ($res) {
			exit(json_encode(array('errno' => 0)));
		}
		else {
			exit(json_encode(array('errno' => 1)));
		}
	}

	public function createcoupons()
	{
		global $_W;
		global $_GPC;
		$set = Setting::agentsetting_read('coupon');
		$diy = unserialize($set['coupon']);

		if (p('userlabel')) {
			$labels = pdo_getall('wlmerchant_userlabel', array('uniacid' => $_W['uniacid']), array('id', 'name'), '', 'sort DESC');
		}

		$levels = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halflevel') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 ORDER BY sort DESC'));
		$coupontype = $_GPC['coupontype'];
		if ($coupontype == 1 || $coupontype == '') {
			$coupontype = 1;
			$coupon_title = $diy['zkname'] ? $diy['zkname'] : '折扣券';
		}
		else if ($coupontype == 2) {
			$coupon_title = $diy['djname'] ? $diy['djname'] : '代金券';
		}
		else if ($coupontype == 3) {
			$coupon_title = $diy['tcname'] ? $diy['tcname'] : '套餐券';
		}
		else if ($coupontype == 4) {
			$coupon_title = $diy['tgname'] ? $diy['tgname'] : '团购券';
		}
		else {
			if ($coupontype == 5) {
				$coupon_title = $diy['yhname'] ? $diy['yhname'] : '优惠券';
			}
		}

		$url = app_url('wlcoupon/coupon_app/couponslist');
		$location_store = pdo_getall('wlmerchant_merchantdata', array('uniacid' => $_W['uniacid']));

		foreach ($location_store as $key => &$v) {
			$asd = substr($v['logo'], 0, 4);

			if ($asd != 'http') {
				$v['logo'] = tomedia($v['logo']);
			}
		}

		if (p('distribution')) {
			$distriset = Setting::wlsetting_read('distribution');
		}
		else {
			$distriset = 0;
		}

		if (checksubmit('submit')) {
			$time = $_GPC['time_limit'];
			$starttime = strtotime($time['start']);
			$endtime = strtotime($time['end']);
			$group = array();
			$data = array('status' => $_GPC['status'], 'type' => $coupontype, 'logo' => $_GPC['logo_url'], 'indeximg' => $_GPC['indeximg'], 'is_charge' => $_GPC['is_charge'], 'isdistri' => $_GPC['isdistri'], 'independent' => $_GPC['independent'], 'price' => $_GPC['price'], 'settlementmoney' => $_GPC['settlementmoney'], 'onedismoney' => $_GPC['onedismoney'], 'twodismoney' => $_GPC['twodismoney'], 'threedismoney' => $_GPC['threedismoney'], 'viponedismoney' => $_GPC['viponedismoney'], 'viptwodismoney' => $_GPC['viptwodismoney'], 'vipthreedismoney' => $_GPC['vipthreedismoney'], 'is_show' => $_GPC['is_show'], 'merchantid' => $_GPC['merchantid'], 'color' => $_GPC['color'], 'title' => $_GPC['title'], 'sub_title' => $_GPC['sub_title'], 'goodsdetail' => htmlspecialchars_decode($_GPC['goodsdetail']), 'time_type' => $_GPC['time_type'], 'starttime' => $starttime, 'endtime' => $endtime, 'createtime' => time(), 'deadline' => $_GPC['deadline'], 'quantity' => $_GPC['quantity'], 'surplus' => 0, 'get_limit' => $_GPC['get_limit'], 'description' => serialize($_GPC['description']), 'usetimes' => $_GPC['usetimes'], 'vipstatus' => $_GPC['vipstatus'], 'vipprice' => $_GPC['vipprice'], 'is_indexshow' => $_GPC['is_indexshow'], 'nostoreshow' => $_GPC['nostoreshow'], 'indexorder' => $_GPC['indexorder'], 'dk' => $_GPC['dk'], 'pv' => $_GPC['pv'], 'vipsettlementmoney' => $_GPC['vipsettlementmoney'], 'overrefund' => $_GPC['overrefund'], 'dissettime' => $_GPC['dissettime'], 'allowapplyre' => $_GPC['allowapplyre']);
			$userlabel = $_GPC['userlabel'];
			$data['userlabel'] = serialize($userlabel);
			$level = $_GPC['level'];
			$data['level'] = serialize($level);
			$res = wlCoupon::saveCoupons($data);

			if ($res) {
				wl_message('创建卡券成功', web_url('wlcoupon/couponlist/couponsList'), 'success');
			}
			else {
				wl_message('创建卡券失败', referer(), 'success');
			}
		}

		include wl_template('coupon/createcoupons');
	}

	public function qrcodeimg()
	{
		global $_W;
		global $_GPC;
		$url = $_GPC['url'];
		m('qrcode/QRcode')->png($url, false, QR_ECLEVEL_H, 4);
	}

	public function deleteCoupons()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$ids = $_GPC['ids'];

		if ($id) {
			$res = wlCoupon::deleteCoupons(array('id' => $id));

			if ($res) {
				exit(json_encode(array('errno' => 0, 'message' => $res, 'id' => $id)));
			}
			else {
				exit(json_encode(array('errno' => 2, 'message' => $res, 'id' => $id)));
			}
		}

		if ($ids) {
			foreach ($ids as $key => $id) {
				wlCoupon::deleteCoupons(array('id' => $id));
			}

			exit(json_encode(array('errno' => 0, 'message' => '', 'id' => '')));
		}
	}

	public function deleteCoupon()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$ids = $_GPC['ids'];

		if ($id) {
			$res = wlCoupon::deleteCoupon(array('id' => $id));

			if ($res) {
				show_json(1);
			}
			else {
				show_json(0, '删除失败，请刷新重试');
			}
		}

		if ($ids) {
			foreach ($ids as $key => $id) {
				wlCoupon::deleteCoupon(array('id' => $id));
			}

			exit(json_encode(array('errno' => 0, 'message' => '', 'id' => '')));
		}
	}

	public function disable()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$res = wlCoupon::updateCoupons(array('status' => 3), array('id' => $id));

		if ($res) {
			exit(json_encode(array('errno' => 0, 'message' => '优惠券已失效')));
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '设置优惠券失效失败')));
		}
	}

	public function pass()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$flag = $_GPC['flag'];

		if ($flag) {
			$res = wlCoupon::updateCoupons(array('status' => 1), array('id' => $id));
		}
		else {
			$res = wlCoupon::updateCoupons(array('status' => 4), array('id' => $id));
		}

		if ($res) {
			exit(json_encode(array('errno' => 0, 'message' => '')));
		}
		else {
			exit(json_encode(array('errno' => 1, 'message' => '')));
		}
	}

	public function editCoupons()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];

		if (p('distribution')) {
			$distriset = Setting::wlsetting_read('distribution');
		}
		else {
			$distriset = 0;
		}

		if (p('userlabel')) {
			$labels = pdo_getall('wlmerchant_userlabel', array('uniacid' => $_W['uniacid']), array('id', 'name'), '', 'sort DESC');
		}

		$levels = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halflevel') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 ORDER BY sort DESC'));
		$set = Setting::agentsetting_read('coupon');
		$diy = unserialize($set['coupon']);
		$coupons = wlCoupon::getSingleCoupons($_GPC['id'], '*');
		$storename = pdo_get('wlmerchant_merchantdata', array('id' => $coupons['merchantid']));
		$userlabel = unserialize($coupons['userlabel']);
		$coupons['level'] = unserialize($coupons['level']);

		foreach ($coupons as $key => $value) {
			$coupons['storename'] = $storename['storename'];
			$coupons['logolist'] = $storename['logo'];
		}

		$coupons['description'] = unserialize($coupons['description']);
		$coupontype = $coupons['type'];
		$location_store = pdo_getall('wlmerchant_merchantdata', array('uniacid' => $_W['uniacid']));

		foreach ($location_store as $key => &$v) {
			$asd = substr($v['logo'], 0, 4);

			if ($asd != 'http') {
				$v['logo'] = tomedia($v['logo']);
				$v['indeximg'] = tomedia($v['indeximg']);
			}
		}

		$url = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $id));
		if ($coupontype == 1 || $coupontype == '') {
			$coupontype = 1;
			$coupon_title = $diy['zkname'] ? $diy['zkname'] : '折扣券';
		}
		else if ($coupontype == 2) {
			$coupon_title = $diy['djname'] ? $diy['djname'] : '代金券';
		}
		else if ($coupontype == 3) {
			$coupon_title = $diy['tcname'] ? $diy['tcname'] : '套餐券';
		}
		else if ($coupontype == 4) {
			$coupon_title = $diy['tgname'] ? $diy['tgname'] : '团购券';
		}
		else {
			if ($coupontype == 5) {
				$coupon_title = $diy['yhname'] ? $diy['yhname'] : '优惠券';
			}
		}

		if (checksubmit('submit')) {
			if ($_GPC['quantity'] < $coupons['surplus']) {
				wl_message('更新卡券失败,库存不能小于已售数量', referer(), 'error');
			}

			$time = $_GPC['time_limit'];
			$starttime = strtotime($time['start']);
			$endtime = strtotime($time['end']);
			$group = array();
			$data = array('status' => $_GPC['status'], 'type' => $coupontype, 'logo' => $_GPC['logo_url'], 'indeximg' => $_GPC['indeximg'], 'is_charge' => $_GPC['is_charge'], 'isdistri' => $_GPC['isdistri'], 'independent' => $_GPC['independent'], 'is_show' => $_GPC['is_show'], 'price' => $_GPC['price'], 'settlementmoney' => $_GPC['settlementmoney'], 'onedismoney' => $_GPC['onedismoney'], 'twodismoney' => $_GPC['twodismoney'], 'threedismoney' => $_GPC['threedismoney'], 'viponedismoney' => $_GPC['viponedismoney'], 'viptwodismoney' => $_GPC['viptwodismoney'], 'vipthreedismoney' => $_GPC['vipthreedismoney'], 'merchantid' => $_GPC['merchantid'], 'color' => $_GPC['color'], 'title' => $_GPC['title'], 'sub_title' => $_GPC['sub_title'], 'goodsdetail' => htmlspecialchars_decode($_GPC['goodsdetail']), 'time_type' => $_GPC['time_type'], 'starttime' => $starttime, 'endtime' => $endtime, 'createtime' => time(), 'deadline' => $_GPC['deadline'], 'quantity' => $_GPC['quantity'], 'get_limit' => $_GPC['get_limit'], 'description' => serialize($_GPC['description']), 'usetimes' => $_GPC['usetimes'], 'vipstatus' => $_GPC['vipstatus'], 'vipprice' => $_GPC['vipprice'], 'is_indexshow' => $_GPC['is_indexshow'], 'nostoreshow' => $_GPC['nostoreshow'], 'indexorder' => $_GPC['indexorder'], 'dk' => $_GPC['dk'], 'pv' => $_GPC['pv'], 'vipsettlementmoney' => $_GPC['vipsettlementmoney'], 'overrefund' => $_GPC['overrefund'], 'dissettime' => $_GPC['dissettime'], 'allowapplyre' => $_GPC['allowapplyre']);
			$userlabel = $_GPC['userlabel'];
			$data['userlabel'] = serialize($userlabel);
			$level = $_GPC['level'];
			$data['level'] = serialize($level);
			$res = wlCoupon::updateCoupons($data, array('id' => $id));

			if ($res) {
				Tools::clearposter();
				wl_message('更新卡券成功', web_url('wlcoupon/couponlist/couponsList'), 'success');
			}
			else {
				wl_message('更新卡券失败', referer(), 'error');
			}
		}

		include wl_template('coupon/createcoupons');
	}

	public function merbercoupon()
	{
		global $_W;
		global $_GPC;
		$set = Setting::agentsetting_read('coupon');
		$diy = unserialize($set['coupon']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$wheres = array();
		$wheres['uniacid'] = $_W['uniacid'];
		$wheres['aid'] = $_W['aid'];

		if (!empty($_GPC['type'])) {
			$wheres['type'] = $_GPC['type'];
		}

		if ($_GPC['status']) {
			if ($_GPC['status'] == 1) {
				$wheres['usetimes>'] = 1;
			}
			else {
				$wheres['usetimes'] = 0;
			}
		}

		if ($_GPC['keyword']) {
			if ($_GPC['keywordtype'] == 2) {
				$keyword = $_GPC['keyword'];
				$params[':nickname'] = '%' . $keyword . '%';
				$member = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_member') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND nickname LIKE :nickname'), $params);

				if ($member) {
					$mids = '(';

					foreach ($member as $key => $v) {
						if ($key == 0) {
							$mids .= $v['id'];
						}
						else {
							$mids .= ',' . $v['id'];
						}
					}

					$mids .= ')';
					$wheres['mid#'] = $mids;
				}
				else {
					$wheres['mid#'] = '(0)';
				}
			}

			if ($_GPC['keywordtype'] == 1) {
				$keyword = $_GPC['keyword'];
				$params[':title'] = '%' . $keyword . '%';
				$coupons = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_couponlist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND title LIKE :title'), $params);

				if ($coupons) {
					$parentids = '(';

					foreach ($coupons as $key => $v) {
						if ($key == 0) {
							$parentids .= $v['id'];
						}
						else {
							$parentids .= ',' . $v['id'];
						}
					}

					$parentids .= ')';
					$wheres['parentid#'] = $parentids;
				}
				else {
					$wheres['parentid#'] = '(0)';
				}
			}
		}

		if (!empty($_GPC['time_limit'])) {
			$starttime = strtotime($_GPC['time_limit']['start']);
			$endtime = strtotime($_GPC['time_limit']['end']);
			$wheres['createtime>'] = $starttime;
			$wheres['createtime<'] = $endtime;
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if ($_GPC['export']) {
			$this->exportMemberCoupon($wheres);
		}

		$merber_coupon = wlCoupon::getNumCoupon('*', $wheres, 'ID DESC', $pindex, $psize, 1);
		$pager = $merber_coupon[1];
		$merber_coupon = $merber_coupon[0];

		foreach ($merber_coupon as $key => &$v) {
			$member = pdo_get('wlmerchant_member', array('id' => $v['mid']), array('avatar', 'nickname', 'mobile'));
			$coupon = pdo_get('wlmerchant_couponlist', array('id' => $v['parentid']), array('logo', 'title', 'merchantid'));
			$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $coupon['merchantid']), array('storename'));
			$v['avatar'] = $member['avatar'];
			$v['nickname'] = $member['nickname'];
			$v['mobile'] = $member['mobile'];
			$v['logo'] = $coupon['logo'];
			$v['title'] = $coupon['title'];
			$v['storename'] = $merchant['storename'];
		}

		include wl_template('coupon/merber_coupons');
	}

	public function exportMemberCoupon($wheres)
	{
		global $_W;
		global $_GPC;
		$merber_coupon = wlCoupon::getNumCoupon('*', $wheres, 'ID DESC', 0, 0, 0);
		$merber_coupon = $merber_coupon[0];

		foreach ($merber_coupon as $key => &$v) {
			$member = pdo_get('wlmerchant_member', array('id' => $v['mid']), array('nickname', 'mobile'));
			$coupon = pdo_get('wlmerchant_couponlist', array('id' => $v['parentid']), array('title', 'merchantid'));
			$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $coupon['merchantid']), array('storename'));
			$v['nickname'] = $member['nickname'];
			$v['mobile'] = $member['mobile'];
			$v['title'] = $coupon['title'];
			$v['storename'] = $merchant['storename'];
		}

		$filter = array('id' => '卡券ID', 'title' => '卡券名称', 'type' => '卡券类型', 'storename' => '所属商家', 'nickname' => '买家昵称', 'mobile' => '买家电话', 'price' => '卡券价格', 'concode' => '核销码', 'endtime' => '过期时间', 'usetimes' => '剩余使用次数', 'hexiaotime' => '核销时间', 'vermember' => '核销员');
		$orderlist = $merber_coupon;
		$data = array();
		$i = 0;

		while ($i < count($orderlist)) {
			foreach ($filter as $key => $title) {
				if ($key == 'endtime') {
					$data[$i][$key] = date('Y-m-d H:i:s', $orderlist[$i][$key]);
				}
				else if ($key == 'type') {
					switch ($orderlist[$i][$key]) {
					case '1':
						$data[$i][$key] = '折扣券';
						break;

					case '2':
						$data[$i][$key] = '代金券';
						break;

					case '3':
						$data[$i][$key] = '套餐券';
						break;

					case '4':
						$data[$i][$key] = '团购券';
						break;

					case '5':
						$data[$i][$key] = '优惠券';
						break;

					default:
						$data[$i][$key] = '未知类型';
						break;
					}
				}
				else if ($key == 'price') {
					if (empty($orderlist[$i][$key])) {
						$data[$i][$key] = '免费';
					}
					else {
						$data[$i][$key] = sprintf('%.2f', $orderlist[$i][$key]);
					}
				}
				else if ($key == 'hexiaotime') {
					$usedrecord = '';
					$usedtime = unserialize($orderlist[$i]['usedtime']);

					if ($usedtime) {
						foreach ($usedtime as $kK => $used) {
							if ($kK != 0) {
								$usedrecord .= ' || ' . date('Y-m-d H:i:s', $used['time']);
							}
							else {
								$usedrecord .= date('Y-m-d H:i:s', $used['time']);
							}
						}
					}

					$data[$i][$key] = $usedrecord;
				}
				else if ($key == 'vermember') {
					$vermembers = '';
					$usedtime = unserialize($orderlist[$i]['usedtime']);

					if ($usedtime) {
						foreach ($usedtime as $kKs => $user2) {
							$user2['vername'] = pdo_getcolumn(PDO_NAME . 'merchantuser', array('mid' => $user2['ver']), 'name');

							if ($user2['type'] == 3) {
								$user2['vername'] = '后台核销';
							}
							else {
								if ($user2['type'] == 4) {
									$user2['vername'] = '密码核销';
								}
							}

							if ($kKs != 0) {
								$vermembers .= ' || ' . $user2['vername'];
							}
							else {
								$vermembers .= $user2['vername'];
							}
						}
					}

					$data[$i][$key] = $vermembers;
				}
				else {
					$data[$i][$key] = $orderlist[$i][$key];
				}
			}

			++$i;
		}

		util_csv::export_csv_2($data, $filter, '用户卡券记录表.csv');
		exit();
	}

	public function entry()
	{
		global $_W;
		global $_GPC;
		$settings = $_W['wlsetting']['coverIndexCoupon'];
		$areaid = pdo_getcolumn(PDO_NAME . 'oparea', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), 'areaid');
		$settings['url'] = app_url('wlcoupon/coupon_app/couponslist', array('areaid' => $areaid));
		$settings['name'] = '优惠券入口';

		if (checksubmit('submit')) {
			$cover = Util::trimWithArray($_GPC['cover']);
			Setting::wlsetting_save($cover, 'coverIndexCoupon');
			$re = Setting::saveRule('coverIndexCoupon', $settings['url'], $cover);
			wl_message($re, web_url('wlcoupon/couponlist/entry'));
		}

		include wl_template('coupon/entry');
	}

	public function base()
	{
		global $_W;
		global $_GPC;
		$base = Setting::agentsetting_read('coupon');
		$coupon = unserialize($base['coupon']);
		$distri = Setting::wlsetting_read('distribution');

		if (checksubmit('submit')) {
			$data = $_GPC['base'];
			$data['noshare'] = $_GPC['noshare'];
			$data['mustmobile'] = $_GPC['mustmobile'];
			$data['coupon'] = serialize($_GPC['coupon']);
			$res1 = Setting::agentsetting_save($data, 'coupon');

			if ($res1) {
				wl_message('保存设置成功！', referer(), 'success');
			}
			else {
				wl_message('保存设置失败！', referer(), 'error');
			}
		}

		include wl_template('coupon/base');
	}

	public function writroff()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$num = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $id), 'usetimes');
		$res = wlCoupon::hexiaoorder($id, -1, $num, 3);

		if ($res) {
			show_json(1);
		}
		else {
			show_json(0, '核销失败，请刷新重试');
		}
	}

	public function hexiaotime()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$coupon = pdo_get('wlmerchant_member_coupons', array('id' => $id), array('usetimes', 'usedtime'));
		$coupon['usedtime'] = unserialize($coupon['usedtime']);

		foreach ($coupon['usedtime'] as $key => &$v) {
			$v = date('Y-m-d H:i:s', $v['time']);
		}

		exit(json_encode(array('errno' => 0, 'times' => $coupon['usetimes'], 'data' => $coupon['usedtime'])));
	}

	public function todetail()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$coupon = pdo_get('wlmerchant_couponlist', array('id' => $id), array('goodsdetail', 'description'));
		$data = $coupon['goodsdetail'];
		$data2 = unserialize($coupon['description']);
		exit(json_encode(array('errno' => 0, 'data' => $data, 'data2' => $data2)));
	}

	public function orderlist()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$wheres = array();
		$wheres['uniacid'] = $_W['uniacid'];
		$wheres['aid'] = $_W['aid'];
		$wheres['plugin'] = 'coupon';
		$sel1 = array(
			array('id' => 1, 'name' => '优惠券名称'),
			array('id' => 2, 'name' => '用户昵称'),
			array('id' => 3, 'name' => '按支付时间')
		);

		if ($_GPC['sel']['parentid'] == 1) {
			$keyword = $_GPC['keyword'];
			$params[':title'] = '%' . $keyword . '%';
			$coupons = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_couponlist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND title LIKE :title'), $params);

			if ($coupons) {
				$parentids = '(';

				foreach ($coupons as $key => $v) {
					if ($key == 0) {
						$parentids .= $v['id'];
					}
					else {
						$parentids .= ',' . $v['id'];
					}
				}

				$parentids .= ')';
				$wheres['fkid#'] = $parentids;
			}
			else {
				$wheres['fkid#'] = '(0)';
			}
		}

		if ($_GPC['sel']['parentid'] == 2) {
			$keyword = $_GPC['keyword'];
			$params[':nickname'] = '%' . $keyword . '%';
			$member = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_member') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND nickname LIKE :nickname'), $params);

			if ($member) {
				$mids = '(';

				foreach ($member as $key => $v) {
					if ($key == 0) {
						$mids .= $v['id'];
					}
					else {
						$mids .= ',' . $v['id'];
					}
				}

				$mids .= ')';
				$wheres['mid#'] = $mids;
			}
			else {
				$wheres['mid#'] = '(0)';
			}
		}

		if ($_GPC['sel']['parentid'] == 3) {
			if (!empty($_GPC['time_limit'])) {
				$starttime = strtotime($_GPC['time_limit']['start']);
				$endtime = strtotime($_GPC['time_limit']['end']);
				$wheres['paytime>'] = $starttime;
				$wheres['paytime<'] = $endtime;
			}
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if ($_GPC['orderid']) {
			$wheres['id'] = $_GPC['orderid'];
		}

		$orders = wlCoupon::getNumCouponOrder('*', $wheres, 'ID DESC', $pindex, $psize, 1);
		$pager = $orders[1];
		$orders = $orders[0];

		foreach ($orders as $key => &$v) {
			$coupon = pdo_get('wlmerchant_couponlist', array('id' => $v['fkid']), array('title', 'logo'));
			$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $v['sid']), array('storename'));
			$member = pdo_get('wlmerchant_member', array('id' => $v['mid']), array('nickname', 'avatar', 'mobile'));
			$v['title'] = $coupon['title'];
			$v['logo'] = tomedia($coupon['logo']);
			$v['storename'] = $merchant['storename'];
			$v['nickname'] = $member['nickname'];
			$v['avatar'] = $member['avatar'];
			$v['mobile'] = $member['mobile'];
		}

		if ($_GPC['export'] != '') {
			$this->export($wheres);
		}

		include wl_template('coupon/orderlist');
	}

	public function export($wheres)
	{
		if (empty($wheres)) {
			return false;
		}

		set_time_limit(0);
		$list = wlCoupon::getNumCouponOrder('*', $wheres, 'ID DESC', 0, 0, 0);
		$list = $list[0];

		foreach ($list as $key => &$v) {
			$coupon = pdo_get('wlmerchant_couponlist', array('id' => $v['fkid']), array('title', 'logo'));
			$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $v['sid']), array('storename'));
			$member = pdo_get('wlmerchant_member', array('id' => $v['mid']), array('nickname', 'avatar', 'mobile'));
			$v['title'] = $coupon['title'];
			$v['logo'] = tomedia($coupon['logo']);
			$v['storename'] = $merchant['storename'];
			$v['nickname'] = $member['nickname'];
			$v['avatar'] = $member['avatar'];
			$v['mobile'] = $member['mobile'];
			$v['paytime'] = date('Y-m-d H:i:s', $v['paytime']);
		}

		$html = "\xef\xbb\xbf";
		$filter = array('orderno' => '订单号', 'title' => '卡券名称', 'num' => '数量', 'storename' => '所属商家', 'nickname' => '买家昵称', 'mobile' => '买家电话', 'status' => '订单状态', 'paytype' => '支付方式', 'paytime' => '支付时间', 'price' => '支付金额', 'adminremark' => '备注');

		foreach ($filter as $key => $title) {
			$html .= $title . '	,';
		}

		$html .= '
';

		foreach ($list as $k => $v) {
			foreach ($filter as $key => $title) {
				if ($key == 'status') {
					switch ($v[$key]) {
					case '1':
						$html .= '已支付' . '	, ';
						break;

					case '2':
						$html .= '待评价' . '	, ';
						break;

					default:
						$html .= '未支付' . '	, ';
						break;
					}
				}
				else if ($key == 'paytype') {
					switch ($v[$key]) {
					case '1':
						$html .= '余额支付' . '	, ';
						break;

					case '2':
						$html .= '微信支付' . '	, ';
						break;

					case '3':
						$html .= '支付宝支付' . '	, ';
						break;

					case '4':
						$html .= '货到付款' . '	, ';
						break;

					default:
						$html .= '其他或未支付' . '	, ';
						break;
					}
				}
				else {
					$html .= $v[$key] . '	, ';
				}
			}

			$html .= '
';
		}

		header('Content-type:text/csv');
		header('Content-Disposition:attachment; filename=卡券订单.csv');
		echo $html;
		exit();
	}

	public function remark()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$remark = $_GPC['remark'];
		$res = pdo_update('wlmerchant_order', array('remark' => $remark), array('id' => $id));

		if ($res) {
			exit(json_encode(array('errno' => 0, 'message' => $res, 'id' => $id)));
		}
		else {
			exit(json_encode(array('errno' => 2, 'message' => $res, 'id' => $id)));
		}
	}

	public function deleteOrder()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$res = pdo_delete('wlmerchant_order', array('id' => $id));

		if ($res) {
			exit(json_encode(array('errno' => 0, 'message' => $res, 'id' => $id)));
		}
		else {
			exit(json_encode(array('errno' => 2, 'message' => $res, 'id' => $id)));
		}
	}

	public function description()
	{
		global $_W;
		global $_GPC;
		include wl_template('coupon/description');
	}

	public function copycoupon()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$coupon = wlCoupon::getSingleCoupons($id, '*');
		unset($coupon['id']);
		$coupon['status'] = 0;
		$coupon['surplus'] = 0;
		$res = wlCoupon::saveCoupons($coupon);

		if ($res) {
			exit(json_encode(array('errno' => 0, 'message' => '', 'id' => $id)));
		}
		else {
			exit(json_encode(array('errno' => 2, 'message' => '', 'id' => $id)));
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
