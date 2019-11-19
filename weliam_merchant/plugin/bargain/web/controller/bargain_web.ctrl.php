<?php
//dezend by http://www.sucaihuo.com/
class Bargain_web_WeliamController
{
	public function activitylist()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$data = array();

		if (!empty($_GPC['status'])) {
			if ($_GPC['status'] == 4) {
				$data['status'] = 0;
			}
			else {
				$data['status'] = intval($_GPC['status']);
			}
		}

		$data['aid'] = $_W['aid'];

		if (!empty($_GPC['keyword'])) {
			if (!empty($_GPC['keywordtype'])) {
				switch ($_GPC['keywordtype']) {
				case 1:
					$data['@name@'] = $_GPC['keyword'];
					break;

				case 2:
					$data['id'] = $_GPC['keyword'];
					break;

				default:
					break;
				}

				if ($_GPC['keywordtype'] == 3) {
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
						$data['sid#'] = $sids;
					}
					else {
						$data['sid#'] = '(0)';
					}
				}
			}
		}

		$activity = Bargain::getNumActive('*', $data, 'sort DESC,ID DESC', $pindex, $psize, 1);
		$pager = $activity[1];
		$activity = $activity[0];

		foreach ($activity as $key => &$act) {
			$act['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $act['sid']), 'storename');
			$act['pv'] = $act['pv'] + $act['falselooknum'];
			$act['alreadypay'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND  fkid = ' . $act['id'] . ' AND plugin = \'bargain\' AND status IN (1,2,3,4,8,6,7,9) '));

			if (empty($act['alreadypay'])) {
				$act['alreadypay'] = 0;
			}

			$act['alreadyuse'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND  fkid = ' . $act['id'] . ' AND plugin = \'bargain\' AND status IN (2,3) '));

			if (empty($act['alreadyuse'])) {
				$act['alreadyuse'] = 0;
			}

			$act['bargaining'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_bargain_userlist') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND  activityid = ' . $act['id'] . ' AND status = 1 '));

			if (empty($act['bargaining'])) {
				$act['bargaining'] = 0;
			}
		}

		$status0 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and aid=' . $_W['aid']));
		$status1 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=1 and aid=' . $_W['aid']));
		$status2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=2 and aid=' . $_W['aid']));
		$status3 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=3 and aid=' . $_W['aid']));
		$status4 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=0 and aid=' . $_W['aid']));
		$status5 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=5 and aid=' . $_W['aid']));
		$status6 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=6 and aid=' . $_W['aid']));
		include wl_template('bargain/activitylist');
	}

	public function createbargain()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$set = Setting::agentsetting_read('bargainset');

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
		$express = Store::getNumExpress('*', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), 'ID DESC', 0, 0, 0);
		$express = $express[0];

		if ($id) {
			$goods = Bargain::getSingleActive($id, '*');
			$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $goods['sid']), array('id', 'storename', 'logo'));
			$goods['rules'] = unserialize($goods['rules']);
			$userlabel = unserialize($goods['userlabel']);
			$goods['thumbs'] = unserialize($goods['thumbs']);
			$goods['level'] = unserialize($goods['level']);
		}

		$cate = pdo_getall('wlmerchant_bargain_category', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
		if (empty($goods['starttime']) || empty($goods['endtime'])) {
			$goods['starttime'] = time();
			$goods['endtime'] = strtotime('+1 month');
			$goods['cutofftime'] = strtotime('+2 month');
		}

		if ($_W['ispost']) {
			$goods = $_GPC['goods'];

			if (empty($goods['sid'])) {
				wl_message('请选择商品所属商家');
			}

			if (empty($goods['price'])) {
				wl_message('请填写商品底价');
			}

			if (empty($goods['name'])) {
				wl_message('请填写商品名称');
			}

			if (empty($goods['stock'])) {
				wl_message('请填写商品库存');
			}

			if (empty($goods['submitmoneylimit'])) {
				wl_message('请填写商品可下单金额');
			}

			$goods['status'] = $_GPC['status'];
			$goods['isdistri'] = $_GPC['isdistri'];
			$goods['vipstatus'] = $_GPC['vipstatus'];
			$goods['detail'] = htmlspecialchars_decode($goods['detail']);
			$userlabel = $_GPC['userlabel'];
			$goods['userlabel'] = serialize($userlabel);
			$goods['thumbs'] = serialize($goods['thumbs']);
			$time = $_GPC['time'];
			$goods['starttime'] = strtotime($time['start']);
			$goods['endtime'] = strtotime($time['end']);
			$goods['aid'] = $_W['aid'];
			$goods['cateid'] = intval($_GPC['cateid']);
			$rule_pice = $_GPC['rule_pice'];
			$rule_start = $_GPC['rule_start'];
			$rule_end = $_GPC['rule_end'];
			$len = count($rule_pice);
			$bargainrule = array();
			$k = 0;

			while ($k < $len) {
				$bargainrule[$k]['rule_pice'] = $rule_pice[$k];
				$bargainrule[$k]['rule_start'] = $rule_start[$k];
				$bargainrule[$k]['rule_end'] = $rule_end[$k];
				++$k;
			}

			$bargainrule = serialize($bargainrule);
			$goods['rules'] = $bargainrule;
			$level = $_GPC['level'];
			$goods['level'] = serialize($level);

			if ($goods['status']) {
				if (time() < $goods['starttime']) {
					$goods['status'] = 1;
				}
				else {
					if ($goods['starttime'] < time() && time() < $goods['endtime']) {
						$goods['status'] = 2;
					}
					else {
						if ($goods['endtime'] < time()) {
							$goods['status'] = 3;
						}
					}
				}
			}

			if ($id) {
				$res = Bargain::updateActive($goods, array('id' => $id));
			}
			else {
				$res = Bargain::saveActive($goods);
			}

			if ($res) {
				Tools::clearposter();
				wl_message('保存成功！', web_url('bargain/bargain_web/activitylist'), 'success');
			}
			else {
				wl_message('保存失败,请重试', referer(), 'error');
			}
		}

		include wl_template('bargain/createbargain');
	}

	public function rules()
	{
		include wl_template('bargain/rules');
	}

	public function changepv()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];
		$newvalue = trim($_GPC['value']);

		if ($type == 1) {
			$pv = pdo_getcolumn('wlmerchant_bargain_activity', array('id' => $id), 'pv');
			$newvalue = intval($newvalue - $pv);
			$res = pdo_update('wlmerchant_bargain_activity', array('falselooknum' => $newvalue), array('id' => $id));
		}
		else {
			if ($type == 2) {
				$res = pdo_update('wlmerchant_bargain_activity', array('sort' => $newvalue), array('id' => $id));
			}
		}

		if ($res) {
			show_json(1, '修改成功');
		}
		else {
			show_json(0, '修改失败，请重试');
		}
	}

	public function changestatus()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$status = $_GPC['status'];

		if ($status) {
			$res = Bargain::updateActive(array('status' => 0), array('id' => $id));
		}
		else {
			$res = Bargain::updateActive(array('status' => 1), array('id' => $id));
		}

		if ($res) {
			exit(json_encode(array('errno' => 0)));
		}
		else {
			exit(json_encode(array('errno' => 1)));
		}
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$res = pdo_delete('wlmerchant_bargain_category', array('id' => $id));

		if ($res) {
			show_json(1, '删除成功');
		}
		else {
			show_json(0, '删除失败，请重试');
		}
	}

	public function copygood()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$da = Bargain::getSingleActive($id, '*');
		unset($da['id']);
		$da['status'] = 0;
		$da['pv'] = 0;
		$da['sharenum'] = 0;
		$res = pdo_insert('wlmerchant_bargain_activity', $da);

		if ($res) {
			exit(json_encode(array('errno' => 0)));
		}
		else {
			exit(json_encode(array('errno' => 1)));
		}
	}

	public function pass()
	{
		global $_W;
		global $_GPC;
		$flag = $_GPC['flag'];
		$id = intval($_GPC['id']);

		if ($flag) {
			$res = pdo_update('wlmerchant_bargain_activity', array('status' => 1), array('id' => $id));
		}
		else {
			$res = pdo_update('wlmerchant_bargain_activity', array('status' => 6), array('id' => $id));
		}

		if ($res) {
			show_json(1, '活动审核成功');
		}
		else {
			show_json(0, '活动审核失败，请重试');
		}
	}

	public function delall()
	{
		global $_W;
		global $_GPC;
		$res = pdo_delete('wlmerchant_bargain_activity', array('id' => intval($_GPC['id'])));

		if ($res) {
			show_json(1, '活动删除成功');
		}
		else {
			show_json(0, '活动删除失败，请重试');
		}
	}

	public function categorylist()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and aid=:aid and uniacid=:uniacid ';
		$keyword = trim($_GPC['keyword']);

		if (!empty($keyword)) {
			$condition .= ' and name like \'%' . $keyword . '%\' ';
		}

		$list = pdo_fetchall('select id,thumb,sort, `name` from ' . tablename('wlmerchant_bargain_category') . ' where 1 ' . $condition . ' order by sort desc limit ' . ($pindex - 1) * $psize . ',' . $psize, array(':aid' => intval($_W['aid']), ':uniacid' => $_W['uniacid']));
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('wlmerchant_bargain_category') . ' where aid=:aid and uniacid=:uniacid ', array(':aid' => intval($_W['aid']), ':uniacid' => $_W['uniacid']));
		$pager = pagination($total, $pindex, $psize);
		include wl_template('bargain/category');
	}

	public function addcate()
	{
		global $_W;
		global $_GPC;
		$name = trim($_GPC['name']);
		$thumb = trim($_GPC['thumb']);
		$sort = intval($_GPC['sort']);
		$id = intval($_GPC['id']);

		if (empty($name)) {
			show_json(0, '分类名称为空！');
		}

		if ($id) {
			pdo_update('wlmerchant_bargain_category', array('name' => $name, 'thumb' => $thumb, 'sort' => $sort), array('id' => $id, 'aid' => intval($_W['aid'])));
		}
		else {
			pdo_insert('wlmerchant_bargain_category', array('name' => $name, 'thumb' => $thumb, 'sort' => $sort, 'uniacid' => $_W['uniacid'], 'aid' => intval($_W['aid'])));
		}

		show_json(1);
	}

	public function edit()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$va = trim($_GPC['value']);
		$item = pdo_fetch('SELECT id, name, uniacid FROM ' . tablename('wlmerchant_bargain_category') . ' WHERE id=:id and aid=:aid and uniacid=:uniacid ', array(':aid' => intval($_W['aid']), ':uniacid' => $_W['uniacid'], ':id' => $id));
		$type = intval($_GPC['type']);

		if (!empty($item)) {
			if ($type == 1) {
				pdo_update('wlmerchant_bargain_category', array('name' => $va), array('id' => $id, 'aid' => intval($_W['aid'])));
			}
			else {
				if ($type == 2) {
					pdo_update('wlmerchant_bargain_category', array('sort' => $va), array('id' => $id, 'aid' => intval($_W['aid'])));
				}
			}

			show_json(1, '分类修改成功');
		}
		else {
			show_json(0, '分类不存在,请刷新页面重试！');
		}
	}

	public function bargainrecord()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']);

		if (!empty($_GPC['keyword'])) {
			if (!empty($_GPC['keywordtype'])) {
				switch ($_GPC['keywordtype']) {
				case 1:
					$where['activityid'] = $_GPC['keyword'];
					break;

				case 2:
					$where['userid'] = $_GPC['keyword'];
					break;

				case 3:
					$where['authorid'] = $_GPC['keyword'];
					break;

				case 4:
					$where['mid'] = $_GPC['keyword'];
					break;

				default:
					break;
				}

				if ($_GPC['keywordtype'] == 5) {
					$keyword = $_GPC['keyword'];
					$params[':storename'] = '%' . $keyword . '%';
					$members = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_bargain_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND name LIKE :storename'), $params);

					if ($members) {
						$mids = '(';

						foreach ($members as $key => $v) {
							if ($key == 0) {
								$mids .= $v['id'];
							}
							else {
								$mids .= ',' . $v['id'];
							}
						}

						$mids .= ')';
						$where['activityid#'] = $mids;
					}
					else {
						$where['activityid#'] = '(0)';
					}
				}

				if ($_GPC['keywordtype'] == 6) {
					$keyword = $_GPC['keyword'];
					$params[':storename'] = '%' . $keyword . '%';
					$members = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_member') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND nickname LIKE :storename'), $params);

					if ($members) {
						$mids = '(';

						foreach ($members as $key => $v) {
							if ($key == 0) {
								$mids .= $v['id'];
							}
							else {
								$mids .= ',' . $v['id'];
							}
						}

						$mids .= ')';
						$where['authorid#'] = $mids;
					}
					else {
						$where['authorid#'] = '(0)';
					}
				}

				if ($_GPC['keywordtype'] == 7) {
					$keyword = $_GPC['keyword'];
					$params[':storename'] = '%' . $keyword . '%';
					$members = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_member') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND nickname LIKE :storename'), $params);

					if ($members) {
						$mids = '(';

						foreach ($members as $key => $v) {
							if ($key == 0) {
								$mids .= $v['id'];
							}
							else {
								$mids .= ',' . $v['id'];
							}
						}

						$mids .= ')';
						$where['mid#'] = $mids;
					}
					else {
						$where['mid#'] = '(0)';
					}
				}
			}
		}

		if (!empty($_GPC['time_limit'])) {
			$starttime = strtotime($_GPC['time_limit']['start']);
			$endtime = strtotime($_GPC['time_limit']['end']);
			$where['createtime>'] = $starttime;
			$where['createtime<'] = $endtime + 86400;
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if ($_GPC['userlistid']) {
			$where['userid'] = $_GPC['userlistid'];
		}

		$records = Util::getNumData('*', PDO_NAME . 'bargain_helprecord', $where, 'createtime DESC', $pindex, $psize, 1);
		$pager = $records[1];
		$records = $records[0];

		if ($records) {
			foreach ($records as $key => &$re) {
				$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $re['activityid']), array('name', 'thumb', 'sid'));
				$re['logo'] = $goods['thumb'];
				$re['gname'] = $goods['name'];
				$re['sid'] = $goods['sid'];
				$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $goods['sid']), array('storename', 'logo'));
				$re['storename'] = $merchant['storename'];
				$re['merchantlogo'] = $merchant['logo'];
				$author = pdo_get('wlmerchant_member', array('id' => $re['authorid']), array('nickname', 'avatar'));
				$re['username'] = $author['nickname'];
				$re['useravatar'] = $author['avatar'];
				$member = pdo_get('wlmerchant_member', array('id' => $re['mid']), array('nickname', 'avatar'));
				$re['nickname'] = $member['nickname'];
				$re['avatar'] = $member['avatar'];
				$re['createtime'] = date('Y-m-d H:i:s', $re['createtime']);
			}
		}

		include wl_template('bargain/bargainrecord');
	}

	public function orderlist()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = array('aid' => $_W['aid'], 'plugin' => 'bargain');

		if (!empty($_GPC['status'])) {
			if (intval($_GPC['status']) == 10) {
				$where['status'] = 0;
			}
			else {
				$where['status'] = intval($_GPC['status']);
			}
		}
		else {
			$where['#status'] = '(1,2,3,6,7,9,4,8)';
		}

		if ($_GPC['specid']) {
			$where['specid'] = intval($_GPC['specid']);
		}

		if (!empty($_GPC['keyword'])) {
			if (!empty($_GPC['keywordtype'])) {
				switch ($_GPC['keywordtype']) {
				case 1:
					$where['id'] = $_GPC['keyword'];
					break;

				case 2:
					$where['@orderno@'] = $_GPC['keyword'];
					break;

				case 3:
					$where['fkid'] = $_GPC['keyword'];
					break;

				case 4:
					$where['@sid@'] = $_GPC['keyword'];
					break;

				case 5:
					$where['@name@'] = $_GPC['keyword'];
					break;

				case 6:
					$where['@mobile@'] = $_GPC['keyword'];
					break;

				default:
					break;
				}

				if ($_GPC['keywordtype'] == 8) {
					$keyword = $_GPC['keyword'];
					$params[':storename'] = '%' . $keyword . '%';
					$members = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_merchantdata') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND storename LIKE :storename'), $params);

					if ($members) {
						$mids = '(';

						foreach ($members as $key => $v) {
							if ($key == 0) {
								$mids .= $v['id'];
							}
							else {
								$mids .= ',' . $v['id'];
							}
						}

						$mids .= ')';
						$where['sid#'] = $mids;
					}
					else {
						$where['sid#'] = '(0)';
					}
				}
			}
		}

		if ($_GPC['export'] != '') {
			$this->exportlist($where);
		}

		$orders = Util::getNumData('*', PDO_NAME . 'order', $where, 'ID DESC', $pindex, $psize, 1);
		$pager = $orders[1];
		$orders = $orders[0];

		foreach ($orders as $key => &$order) {
			$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $order['fkid']), array('name', 'thumb', 'oldprice'));
			$order['gname'] = $goods['name'];
			$order['oldprice'] = $goods['oldprice'];
			$order['gimg'] = tomedia($goods['thumb']);
			$order['merchantName'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $order['sid']), 'storename');
			$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('nickname', 'mobile', 'avatar'));
			$order['mobile'] = $order['mobile'] ? $order['mobile'] : $member['mobile'];
			$order['name'] = $order['name'] ? $order['name'] : $member['nickname'];
			$order['headimg'] = $member['avatar'];

			if ($order['expressid']) {
				$express = pdo_get(PDO_NAME . 'express', array('id' => $order['expressid']));
				$order['name'] = $express['name'];
				$order['mobile'] = $express['tel'];
			}
			else {
				$userlist = pdo_get('wlmerchant_bargain_userlist', array('id' => $order['specid']), array('usedtime'));
				$order['usedtime'] = $userlist['usedtime'];
			}
		}

		$status0 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and plugin = \'bargain\' and status in (1,2,3,5,6,7,9,4,8) and aid=' . $_W['aid']));
		$status1 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and plugin = \'bargain\' and status = 1 and aid=' . $_W['aid']));
		$status2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and plugin = \'bargain\' and status = 2 and aid=' . $_W['aid']));
		$status3 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and plugin = \'bargain\' and status = 3 and aid=' . $_W['aid']));
		$status4 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and plugin = \'bargain\' and status = 4 and aid=' . $_W['aid']));
		$status8 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and plugin = \'bargain\' and status = 8 and aid=' . $_W['aid']));
		$status5 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and plugin = \'bargain\' and status = 5 and aid=' . $_W['aid']));
		$status6 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and plugin = \'bargain\' and status = 6 and aid=' . $_W['aid']));
		$status7 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and plugin = \'bargain\' and status = 7 and aid=' . $_W['aid']));
		$status9 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and plugin = \'bargain\' and status = 9 and aid=' . $_W['aid']));
		include wl_template('bargain/order_list');
	}

	public function hexiaotime()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$record = pdo_get('wlmerchant_bargain_userlist', array('orderid' => $id), array('usetimes', 'usedtime'));
		$record['usedtime'] = unserialize($record['usedtime']);

		foreach ($record['usedtime'] as $key => &$v) {
			$v['time'] = date('Y-m-d H:i:s', $v['time']);

			switch ($v['type']) {
			case '1':
				$v['typename'] = '输码核销';
				break;

			case '2':
				$v['typename'] = '扫码核销';
				break;

			case '3':
				$v['typename'] = '后台核销';
				break;

			case '4':
				$v['typename'] = '密码核销';
				break;

			default:
				$v['typename'] = '未知方式';
				break;
			}

			if ($v['type'] == 1 || $v['type'] == 2) {
				$v['vername'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $v['ver']), 'nickname');
			}
			else {
				$v['vername'] = '无';
			}
		}

		exit(json_encode(array('errno' => 0, 'times' => $record['usetimes'], 'data' => $record['usedtime'])));
	}

	public function sendexpress()
	{
		global $_W;
		global $_GPC;
		$express = trim($_GPC['express']);
		$expresssn = trim($_GPC['expresssn']);
		$id = intval($_GPC['id']);

		if (empty($expresssn)) {
			show_json(0, '订单编号为空！');
		}

		$orderid = pdo_getcolumn(PDO_NAME . 'order', array('expressid' => $id), 'id');
		$res = pdo_update('wlmerchant_express', array('expressname' => $express, 'expresssn' => $expresssn, 'orderid' => $orderid, 'sendtime' => time()), array('id' => $id));

		if ($res) {
			$res = pdo_update('wlmerchant_order', array('status' => 8), array('id' => $orderid));
			show_json(1);
		}
		else {
			show_json(0, '发货失败请重试');
		}
	}

	public function exportlist($where)
	{
		if (empty($where)) {
			return false;
		}

		set_time_limit(0);
		$list = Util::getNumData('*', PDO_NAME . 'order', $where, 'ID DESC', $pindex, $psize, 1);
		$list = $list[0];

		foreach ($list as $key => &$order) {
			$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $order['fkid']), array('name', 'thumb', 'oldprice'));
			$order['gname'] = $goods['name'];
			$order['oldprice'] = $goods['oldprice'];
			$order['gimg'] = tomedia($goods['thumb']);
			$order['merchantName'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $order['sid']), 'storename');
			$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('nickname', 'mobile', 'avatar'));
			$order['mobile'] = $order['mobile'] ? $order['mobile'] : $member['mobile'];
			$order['name'] = $order['name'] ? $order['name'] : $member['nickname'];
		}

		$filter = array('orderno' => '订单号', 'gname' => '商品名称', 'merchantName' => '所属商家', 'name' => '买家昵称', 'mobile' => '买家电话', 'address' => '买家地址', 'status' => '订单状态', 'paytype' => '支付方式', 'createtime' => '下单时间', 'paytime' => '支付时间', 'price' => '实付金额', 'remark' => '备注');
		$data = array();

		foreach ($list as $k => $v) {
			foreach ($filter as $key => $title) {
				if ($key == 'status') {
					switch ($v[$key]) {
					case '1':
						$data[$k][$key] = '已支付';
						break;

					case '2':
						$data[$k][$key] = '已消费';
						break;

					case '3':
						$data[$k][$key] = '已完成';
						break;

					case '4':
						$data[$k][$key] = '待使用';
						break;

					case '5':
						$data[$k][$key] = '已取消';
						break;

					case '6':
						$data[$k][$key] = '待退款';
						break;

					case '7':
						$data[$k][$key] = '已退款';
						break;

					case '9':
						$data[$k][$key] = '已过期';
						break;

					default:
						$data[$k][$key] = '未支付';
						break;
					}
				}
				else if ($key == 'createtime') {
					$data[$k][$key] = date('Y-m-d H:i:s', $v[$key]);
				}
				else if ($key == 'paytime') {
					if (!empty($v['paytime'])) {
						$data[$k][$key] = date('Y-m-d H:i:s', $v[$key]);
					}
					else {
						$data[$k][$key] = '未支付';
					}
				}
				else if ($key == 'paytype') {
					switch ($v[$key]) {
					case '1':
						$data[$k][$key] = '余额支付';
						break;

					case '2':
						$data[$k][$key] = '微信支付';
						break;

					case '3':
						$data[$k][$key] = '支付宝支付';
						break;

					case '4':
						$data[$k][$key] = '货到付款';
						break;

					default:
						$data[$k][$key] = '其他或未支付';
						break;
					}
				}
				else {
					$data[$k][$key] = $v[$key];
				}
			}
		}

		util_csv::export_csv_2($data, $filter, '砍价订单.csv');
		exit();
	}

	public function confirmHexiao()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$usetimes = pdo_getcolumn(PDO_NAME . 'bargain_userlist', array('orderid' => $id), 'usetimes');
		$res = Bargain::hexiaoorder($id, -1, $usetimes, 3);

		if ($res) {
			exit(json_encode(array('errno' => 0, 'message' => '核销成功', 'id' => $id)));
		}
		else {
			exit(json_encode(array('errno' => 2, 'message' => 'error', 'id' => $id)));
		}
	}

	public function refundOrder()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$res = Bargain::refund($id);

		if ($res['status']) {
			exit(json_encode(array('errno' => 0, 'message' => $res['message'], 'id' => $id)));
		}
		else {
			exit(json_encode(array('errno' => 2, 'message' => $res['message'], 'id' => $id)));
		}
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

	public function userlist()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid']);

		if (!empty($_GPC['status'])) {
			$where['status'] = intval($_GPC['status']);
		}

		if (!empty($_GPC['keyword'])) {
			if (!empty($_GPC['keywordtype'])) {
				switch ($_GPC['keywordtype']) {
				case 1:
					$where['activityid'] = $_GPC['keyword'];
					break;

				case 2:
					$where['merchantid'] = $_GPC['keyword'];
					break;

				default:
					break;
				}

				if ($_GPC['keywordtype'] == 3) {
					$keyword = $_GPC['keyword'];
					$params[':storename'] = '%' . $keyword . '%';
					$merchants = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_bargain_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND name LIKE :storename'), $params);

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
						$where['activityid#'] = $sids;
					}
					else {
						$where['activityid#'] = '(0)';
					}
				}

				if ($_GPC['keywordtype'] == 4) {
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
						$where['merchantid#'] = $sids;
					}
					else {
						$where['merchantid#'] = '(0)';
					}
				}
			}
		}

		if (!empty($_GPC['time_limit']) && $_GPC['timetype']) {
			$starttime = strtotime($_GPC['time_limit']['start']);
			$endtime = strtotime($_GPC['time_limit']['end']);

			if ($_GPC['timetype'] == 1) {
				$where['createtime>'] = $starttime;
				$where['createtime<'] = $endtime + 86400;
			}
			else {
				$where['updatetime>'] = $starttime;
				$where['updatetime<'] = $endtime;
			}
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		$users = Util::getNumData('*', PDO_NAME . 'bargain_userlist', $where, 'ID DESC', $pindex, $psize, 1);
		$pager = $users[1];
		$users = $users[0];

		foreach ($users as $key => &$user) {
			$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $user['activityid']), array('name', 'thumb', 'oldprice', 'sid'));
			$user['logo'] = $goods['thumb'];
			$user['name'] = $goods['name'];
			$user['oldprice'] = $goods['oldprice'];
			$user['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $goods['sid']), 'storename');
			$user['orderno'] = pdo_getcolumn(PDO_NAME . 'order', array('id' => $user['orderid']), 'orderno');
		}

		include wl_template('bargain/userlist');
	}

	public function entry()
	{
		global $_W;
		global $_GPC;
		$settings['name'] = '砍价入口';
		$areaid = pdo_getcolumn(PDO_NAME . 'oparea', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), 'areaid');
		$settings['url'] = app_url('bargain/bargain_app/bargainlist');
		include wl_template('bargain/entry');
	}

	public function setting()
	{
		global $_W;
		global $_GPC;
		$set = Setting::agentsetting_read('bargainset');

		if (checksubmit('submit')) {
			$data = $_GPC['set'];
			$res1 = Setting::agentsetting_save($data, 'bargainset');
			wl_message('保存设置成功！', referer(), 'success');
		}

		include wl_template('bargain/set');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
