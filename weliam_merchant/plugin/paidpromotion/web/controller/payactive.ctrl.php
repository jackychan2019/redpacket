<?php
//dezend by http://www.sucaihuo.com/
class Payactive_WeliamController
{
	public function activelist()
	{
		global $_W;
		global $_GPC;
		$pindex = $_GPC['page'] ? $_GPC['page'] : 1;
		$psize = 10;
		$where = array();
		$where['uniacid'] = $_W['uniacid'];
		$where['aid'] = $_W['aid'];
		$lists = Util::getNumData('*', 'wlmerchant_payactive', $where, 'id DESC', $pindex, $psize, 1);
		$pager = $lists[1];
		$lists = $lists[0];
		include wl_template('payactive/active_list');
	}

	public function delate()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$res = pdo_delete('wlmerchant_payactive', array('id' => $id));

		if ($res) {
			show_json(1, '活动删除成功');
		}
		else {
			show_json(0, '活动删除失败，请重试');
		}
	}

	public function recodelist()
	{
		global $_W;
		global $_GPC;
		$pindex = $_GPC['page'] ? $_GPC['page'] : 1;
		$psize = 10;
		$where = array();
		$where['uniacid'] = $_W['uniacid'];
		$where['aid'] = $_W['aid'];

		if (!empty($_GPC['type'])) {
			$where['type'] = $_GPC['type'];
		}

		if ($_GPC['time_limit']) {
			$time_limit = $_GPC['time_limit'];
			$starttime = strtotime($_GPC['time_limit']['start']);
			$endtime = strtotime($_GPC['time_limit']['end']);
			$where['createtime>'] = $starttime;
			$where['createtime<'] = $endtime;
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time() + 86400;
		}

		$lists = Util::getNumData('*', 'wlmerchant_paidrecord', $where, 'createtime DESC', $pindex, $psize, 1);
		$pager = $lists[1];
		$lists = $lists[0];

		if ($lists) {
			foreach ($lists as $key => &$va) {
				if ($va['type'] == 1) {
					$order = pdo_get(PDO_NAME . 'rush_order', array('id' => $va['orderid']), array('mid', 'activityid'));
					$mid = $order['mid'];
					$va['goodname'] = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $order['activityid']), 'name');
				}
				else if ($va['type'] == 2) {
					$order = pdo_get(PDO_NAME . 'order', array('id' => $va['orderid']), array('mid', 'fkid'));
					$mid = $order['mid'];
					$va['goodname'] = pdo_getcolumn(PDO_NAME . 'fightgroup_goods', array('id' => $order['fkid']), 'name');
				}
				else if ($va['type'] == 3) {
					$order = pdo_get(PDO_NAME . 'order', array('id' => $va['orderid']), array('mid', 'fkid'));
					$mid = $order['mid'];
					$va['goodname'] = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $order['fkid']), 'title');
				}
				else if ($va['type'] == 4) {
					$order = pdo_get(PDO_NAME . 'order', array('id' => $va['orderid']), array('mid', 'fkid'));
					$mid = $order['mid'];
					$va['goodname'] = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $order['fkid']), 'name');
				}
				else if ($va['type'] == 5) {
					$order = pdo_get(PDO_NAME . 'halfcard_record', array('id' => $va['orderid']), array('mid', 'typeid'));
					$mid = $order['mid'];
					$va['goodname'] = pdo_getcolumn(PDO_NAME . 'halfcard_type', array('id' => $order['typeid']), 'name');
				}
				else if ($va['type'] == 6) {
					$order = pdo_get(PDO_NAME . 'order', array('id' => $va['orderid']), array('mid', 'fkid'));
					$mid = $order['mid'];
					$va['goodname'] = pdo_getcolumn(PDO_NAME . 'chargelist', array('id' => $order['fkid']), 'name');
				}
				else {
					if ($va['type'] == 7) {
						$order = pdo_get(PDO_NAME . 'order', array('id' => $va['orderid']), array('mid', 'fkid', 'sid'));
						$mid = $order['mid'];
						$va['goodname'] = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('id' => $order['fkid']), 'title');

						if (empty($va['goodname'])) {
							$va['goodname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $order['sid']), 'storename');
						}
					}
				}

				$member = pdo_get(PDO_NAME . 'member', array('id' => $mid), array('nickname', 'avatar', 'mobile'));
				$va['nickname'] = $member['nickname'];
				$va['headimg'] = $member['avatar'];
				$va['mobile'] = $member['mobile'];
				$va['activename'] = pdo_getcolumn(PDO_NAME . 'payactive', array('id' => $va['activeid']), 'title');

				if ($va['couponid']) {
					$couponIdList = explode(',', $va['couponid']);

					foreach ($couponIdList as $k => $v) {
						$va['couponname'][] = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $v), 'title');
					}
				}

				if ($va['codeid']) {
					$va['code'] = pdo_getcolumn(PDO_NAME . 'token', array('id' => $va['codeid']), 'number');
				}
			}
		}

		include wl_template('payactive/recodelist');
	}

	public function createactive()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'] ? $_GPC['id'] : 0;
		$active = pdo_get('wlmerchant_payactive', array('id' => $id));

		if ($active['rushids']) {
			$active['rushids'] = unserialize($active['rushids']);
		}

		if ($active['grouponids']) {
			$active['grouponids'] = unserialize($active['grouponids']);
		}

		if ($active['fightgroupids']) {
			$active['fightgroupids'] = unserialize($active['fightgroupids']);
		}

		if ($active['couponids']) {
			$active['couponids'] = unserialize($active['couponids']);
		}

		if ($active['halfcardids']) {
			$active['halfcardids'] = unserialize($active['halfcardids']);
		}

		if ($active['chargeids']) {
			$active['chargeids'] = unserialize($active['chargeids']);
		}

		if ($active['giftcouponid']) {
			$active['giftcouponid'] = explode(',', $active['giftcouponid']);
		}

		$rushgoods = pdo_getall('wlmerchant_rush_activity', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id', 'name'));
		$groupongoods = pdo_getall('wlmerchant_groupon_activity', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id', 'name'));
		$fightgroupgoods = pdo_getall('wlmerchant_fightgroup_goods', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id', 'name'));
		$coupongoods = pdo_getall('wlmerchant_couponlist', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'status' => 1), array('id', 'title'));
		$halfcardgoods = pdo_getall('wlmerchant_halfcard_type', array('uniacid' => $_W['uniacid']), array('id', 'name'));
		$chargegoods = pdo_getall('wlmerchant_chargelist', array('uniacid' => $_W['uniacid']), array('id', 'name'));
		$giftcoupons = $coupongoods;
		$giftcode = pdo_fetchall('SELECT distinct remark FROM ' . tablename('wlmerchant_token') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' ORDER BY id DESC'));

		if ($giftcode) {
			foreach ($giftcode as $key => $cas) {
				if (empty($cas['remark'])) {
					unset($giftcode[$key]);
				}
			}
		}

		if (empty($active['starttime']) || empty($active['endtime'])) {
			$active['starttime'] = time();
			$active['endtime'] = strtotime('+1 month');
		}

		if (checksubmit('submit')) {
			$activedata = $_GPC['active'];
			$activetime = $_GPC['activetime'];
			$activedata['starttime'] = strtotime($activetime['start']);
			$activedata['endtime'] = strtotime($activetime['end']);
			$activedata['userstatus'] = $_GPC['userstatus'];
			$activedata['orderstatus'] = $_GPC['orderstatus'];
			$activedata['status'] = $_GPC['status'];
			$activedata['giftstatus'] = $_GPC['giftstatus'];
			$activedata['getstatus'] = $_GPC['getstatus'];
			$plugin = $_GPC['plugin'];

			if (empty($plugin)) {
				wl_message('请选择一个适用插件', referer(), 'error');
			}

			if (in_array('rush', $plugin)) {
				$activedata['rushflag'] = 1;
			}
			else {
				$activedata['rushflag'] = 0;
			}

			if (in_array('groupon', $plugin)) {
				$activedata['grouponflag'] = 1;
			}
			else {
				$activedata['grouponflag'] = 0;
			}

			if (in_array('fightgroup', $plugin)) {
				$activedata['fightgroupflag'] = 1;
			}
			else {
				$activedata['fightgroupflag'] = 0;
			}

			if (in_array('coupon', $plugin)) {
				$activedata['couponflag'] = 1;
			}
			else {
				$activedata['couponflag'] = 0;
			}

			if (in_array('halfcard', $plugin)) {
				$activedata['halfcardflag'] = 1;
			}
			else {
				$activedata['halfcardflag'] = 0;
			}

			if (in_array('charge', $plugin)) {
				$activedata['chargeflag'] = 1;
			}
			else {
				$activedata['chargeflag'] = 0;
			}

			if (in_array('payonline', $plugin)) {
				$activedata['payonlineflag'] = 1;
			}
			else {
				$activedata['payonlineflag'] = 0;
			}

			if (empty($activedata['orderstatus'])) {
				if ($activedata['rushflag']) {
					$repeat = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND rushflag = 1 AND orderstatus = 0 AND id != ' . $id));

					if ($repeat) {
						wl_message('抢购已有支付有礼活动，请勿重复设置');
					}
				}

				if ($activedata['grouponflag']) {
					$repeat = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND grouponflag = 1 AND orderstatus = 0 AND id != ' . $id));

					if ($repeat) {
						wl_message('团购已有支付有礼活动，请勿重复设置');
					}
				}

				if ($activedata['fightgroupflag']) {
					$repeat = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND fightgroupflag = 1 AND orderstatus = 0 AND id != ' . $id));

					if ($repeat) {
						wl_message('拼团已有支付有礼活动，请勿重复设置');
					}
				}

				if ($activedata['couponflag']) {
					$repeat = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND couponflag = 1 AND orderstatus = 0 AND id != ' . $id));

					if ($repeat) {
						wl_message('卡券已有支付有礼活动，请勿重复设置');
					}
				}

				if ($activedata['halfcardflag']) {
					$repeat = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND halfcardflag = 1 AND orderstatus = 0 AND id != ' . $id));

					if ($repeat) {
						wl_message('一卡通已有支付有礼活动，请勿重复设置');
					}
				}

				if ($activedata['chargeflag']) {
					$repeat = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND chargeflag = 1 AND orderstatus = 0 AND id != ' . $id));

					if ($repeat) {
						wl_message('付费入驻已有支付有礼活动，请勿重复设置');
					}
				}

				if ($activedata['payonlineflag']) {
					$repeat = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND payonlineflag = 1 AND orderstatus = 0 AND id != ' . $id));

					if ($repeat) {
						wl_message('在线支付已有支付有礼活动，请勿重复设置');
					}
				}
			}
			else {
				$activedata['rushids'] = $_GPC['rushids'];
				$activedata['grouponids'] = $_GPC['grouponids'];
				$activedata['fightgroupids'] = $_GPC['fightgroupids'];
				$activedata['couponids'] = $_GPC['couponids'];
				$activedata['halfcardids'] = $_GPC['halfcardids'];
				$activedata['chargeids'] = $_GPC['chargeids'];
				if ($activedata['rushflag'] && $activedata['rushids']) {
					$repeats = pdo_fetchall('SELECT rushids,id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND rushflag = 1 AND orderstatus = 1 AND id != ' . $id));

					if ($repeats) {
						foreach ($repeats as $key => $repeat) {
							$repeat['rushids'] = unserialize($repeat['rushids']);

							foreach ($activedata['rushids'] as $key => $goodid) {
								if (in_array($goodid, $repeat['rushids'])) {
									$goodsname = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $rushid), 'name');
									$actname = pdo_getcolumn(PDO_NAME . 'payactive', array('id' => $repeat['id']), 'title');
									wl_message('抢购商品[' . $goodsname . ']在活动[' . $actname . ']中已设置，请勿重复设置');
								}
							}
						}
					}
				}

				if ($activedata['grouponflag'] && $activedata['grouponids']) {
					$repeats = pdo_fetchall('SELECT grouponids,id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND grouponflag = 1 AND orderstatus = 1 AND id != ' . $id));

					if ($repeats) {
						foreach ($repeats as $key => $repeat) {
							$repeat['grouponids'] = unserialize($repeat['grouponids']);

							foreach ($activedata['grouponids'] as $key => $goodid) {
								if (in_array($goodid, $repeat['grouponids'])) {
									$goodsname = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $goodid), 'name');
									$actname = pdo_getcolumn(PDO_NAME . 'payactive', array('id' => $repeat['id']), 'title');
									wl_message('团购商品[' . $goodsname . ']在活动[' . $actname . ']中已设置，请勿重复设置');
								}
							}
						}
					}
				}

				if ($activedata['fightgroupflag'] && $activedata['fightgroupids']) {
					$repeats = pdo_fetchall('SELECT fightgroupids,id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND fightgroupflag = 1 AND orderstatus = 1 AND id != ' . $id));

					if ($repeats) {
						foreach ($repeats as $key => $repeat) {
							$repeat['fightgroupids'] = unserialize($repeat['fightgroupids']);

							foreach ($activedata['fightgroupids'] as $key => $goodid) {
								if (in_array($goodid, $repeat['fightgroupids'])) {
									$goodsname = pdo_getcolumn(PDO_NAME . 'fightgroup_goods', array('id' => $goodid), 'name');
									$actname = pdo_getcolumn(PDO_NAME . 'payactive', array('id' => $repeat['id']), 'title');
									wl_message('拼团商品[' . $goodsname . ']在活动[' . $actname . ']中已设置，请勿重复设置');
								}
							}
						}
					}
				}

				if ($activedata['couponflag'] && $activedata['couponids']) {
					$repeats = pdo_fetchall('SELECT couponids,id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND couponflag = 1 AND orderstatus = 1 AND id != ' . $id));

					if ($repeats) {
						foreach ($repeats as $key => $repeat) {
							$repeat['couponids'] = unserialize($repeat['couponids']);

							foreach ($activedata['couponids'] as $key => $goodid) {
								if (in_array($goodid, $repeat['couponids'])) {
									$goodsname = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $goodid), 'title');
									$actname = pdo_getcolumn(PDO_NAME . 'payactive', array('id' => $repeat['id']), 'title');
									wl_message('卡券[' . $goodsname . ']在活动[' . $actname . ']中已设置，请勿重复设置');
								}
							}
						}
					}
				}

				if ($activedata['halfcardflag'] && $activedata['halfcardids']) {
					$repeats = pdo_fetchall('SELECT halfcardids,id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND halfcardflag = 1 AND orderstatus = 1 AND id != ' . $id));

					if ($repeats) {
						foreach ($repeats as $key => $repeat) {
							$repeat['halfcardids'] = unserialize($repeat['halfcardids']);

							foreach ($activedata['halfcardids'] as $key => $goodid) {
								if (in_array($goodid, $repeat['halfcardids'])) {
									$goodsname = pdo_getcolumn(PDO_NAME . 'halfcard_type', array('id' => $goodid), 'name');
									$actname = pdo_getcolumn(PDO_NAME . 'payactive', array('id' => $repeat['id']), 'title');
									wl_message('一卡通商品[' . $goodsname . ']在活动[' . $actname . ']中已设置，请勿重复设置');
								}
							}
						}
					}
				}

				if ($activedata['chargeflag'] && $activedata['chargeids']) {
					$repeats = pdo_fetchall('SELECT chargeids,id FROM ' . tablename('wlmerchant_payactive') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND chargeflag = 1 AND orderstatus = 1 AND id != ' . $id));

					if ($repeats) {
						foreach ($repeats as $key => $repeat) {
							$repeat['chargeids'] = unserialize($repeat['chargeids']);

							foreach ($activedata['chargeids'] as $key => $goodid) {
								if (in_array($goodid, $repeat['chargeids'])) {
									$goodsname = pdo_getcolumn(PDO_NAME . 'chargelist', array('id' => $goodid), 'name');
									$actname = pdo_getcolumn(PDO_NAME . 'payactive', array('id' => $repeat['id']), 'title');
									wl_message('一卡通商品[' . $goodsname . ']在活动[' . $actname . ']中已设置，请勿重复设置');
								}
							}
						}
					}
				}

				if ($activedata['rushids']) {
					$activedata['rushids'] = serialize($activedata['rushids']);
				}

				if ($activedata['grouponids']) {
					$activedata['grouponids'] = serialize($activedata['grouponids']);
				}

				if ($activedata['fightgroupids']) {
					$activedata['fightgroupids'] = serialize($activedata['fightgroupids']);
				}

				if ($activedata['couponids']) {
					$activedata['couponids'] = serialize($activedata['couponids']);
				}

				if ($activedata['halfcardids']) {
					$activedata['halfcardids'] = serialize($activedata['halfcardids']);
				}

				if ($activedata['chargeids']) {
					$activedata['chargeids'] = serialize($activedata['chargeids']);
				}

				$activedata['payonlineflag'] = 0;
			}

			if (is_array($activedata['giftcouponid'])) {
				$activedata['giftcouponid'] = implode(',', $activedata['giftcouponid']);
			}

			if ($id) {
				$res = pdo_update('wlmerchant_payactive', $activedata, array('id' => $id));
			}
			else {
				$activedata['uniacid'] = $_W['uniacid'];
				$activedata['aid'] = $_W['aid'];
				$activedata['createtime'] = time();
				$res = pdo_insert(PDO_NAME . 'payactive', $activedata);
			}

			if ($res) {
				wl_message('保存成功！', web_url('paidpromotion/payactive/activelist'), 'success');
			}
			else {
				wl_message('保存失败,请重试', referer(), 'error');
			}
		}

		include wl_template('payactive/createactive');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
