<?php
//dezend by http://www.sucaihuo.com/
class WlOrder_WeliamController
{
	public function orderlist()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = ' where uniacid = ' . $_W['uniacid'] . ' and orderno != 666666';
		$where2 = ' where a.uniacid = ' . $_W['uniacid'] . ' and a.orderno != 666666';

		if (is_agent()) {
			$where .= ' and aid = ' . $_W['aid'];
			$where2 .= ' and a.aid = ' . $_W['aid'];
		}

		if ($_GPC['status']) {
			if ($_GPC['status'] == 'zero') {
				$where .= ' and status = 0';
				$where2 .= ' and a.status = 0';
			}
			else if (intval($_GPC['status']) == 10) {
				$where .= ' and applyrefund = 1 and status IN (1,8)';
				$where2 .= ' and a.applyrefund = 1 and a.status IN (1,8)';
			}
			else if (intval($_GPC['status']) == 11) {
				$where .= ' and status IN (1,2,3,4,6,8,9)';
				$where2 .= ' and a.status IN (1,2,3,4,6,8,9)';
			}
			else if (intval($_GPC['status']) == 12) {
				$where .= ' and status IN (2,3)';
				$where2 .= ' and a.status IN (2,3)';
			}
			else if (intval($_GPC['status']) == 13) {
				$where .= ' and status IN  (0,1,2,3,4,6,8,9)';
				$where2 .= ' and a.status IN  (0,1,2,3,4,6,8,9)';
			}
			else {
				$where .= ' and status = ' . $_GPC['status'] . ' ';
				$where2 .= ' and a.status = ' . $_GPC['status'] . ' ';
			}
		}

		if ($_GPC['paytype']) {
			$where .= ' and paytype = ' . $_GPC['paytype'] . ' ';
			$where2 .= ' and a.paytype = ' . $_GPC['paytype'] . ' ';
		}

		if ($_GPC['keyword']) {
			$keyword = $_GPC['keyword'];

			if ($_GPC['keywordtype'] == 1) {
				$where .= ' and orderno LIKE \'%' . $keyword . '%\'';
				$where2 .= ' and a.orderno LIKE \'%' . $keyword . '%\'';
			}
			else if ($_GPC['keywordtype'] == 3) {
				$where .= ' and mid = \'' . $keyword . '\'';
				$where2 .= ' and a.mid = \'' . $keyword . '\'';
			}
			else if ($_GPC['keywordtype'] == 4) {
				$where .= ' and sid = \'' . $keyword . '\'';
				$where2 .= ' and a.sid = \'' . $keyword . '\'';
			}
			else if ($_GPC['keywordtype'] == 5) {
				$params[':name'] = '%' . $keyword . '%';
				$members = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_member') . (' WHERE uniacid = ' . $_W['uniacid'] . '  AND nickname LIKE :name'), $params);

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
					$where .= ' and mid IN ' . $mids;
					$where2 .= ' and a.mid IN ' . $mids;
				}
			}
			else if ($_GPC['keywordtype'] == 6) {
				$params[':name'] = '%' . $keyword . '%';
				$members = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_member') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND mobile LIKE :name'), $params);

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
					$where .= ' and mid IN ' . $mids;
					$where2 .= ' and a.mid IN ' . $mids;
				}
			}
			else if ($_GPC['keywordtype'] == 7) {
				$params[':name'] = '%' . $keyword . '%';
				$members = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_merchantdata') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND storename LIKE :name'), $params);

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
					$where .= ' and sid IN ' . $mids;
					$where2 .= ' and a.sid IN ' . $mids;
				}
			}
			else if ($_GPC['keywordtype'] == 8) {
				$smorder = pdo_get(PDO_NAME . 'smallorder', array('checkcode' => $keyword), array('orderid', 'plugin'));

				if ($smorder) {
					if ($smorder['plugin'] == 'rush') {
						$where .= ' AND id = ' . $smorder['orderid'] . ' ';
						$where2 .= ' AND a.id = 0';
					}
					else {
						$where .= ' AND id = 0 ';
						$where2 .= ' AND a.id = ' . $smorder['orderid'];
					}
				}
				else {
					$where .= ' AND checkcode = ' . $keyword . ' ';
					$where2 .= ' AND (f.qrcode = ' . $keyword . ' OR g.qrcode = ' . $keyword . ' OR `c`.concode = ' . $keyword . ' OR b.qrcode = ' . $keyword . ') ';
				}
			}
			else if ($_GPC['keywordtype'] == 9) {
				$keyword = '\'%' . $keyword . '%\'';
				$where .= ' AND mobile like ' . $keyword . ' ';
				$where2 .= ' AND a.mobile like ' . $keyword . ' ';
			}
			else {
				if ($_GPC['keywordtype'] == 10) {
					$params[':name'] = '%' . $keyword . '%';
					$members = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_merchantdata') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND salesmid LIKE :name'), $params);

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
						$where .= ' and sid IN ' . $mids;
						$where2 .= ' and a.sid IN ' . $mids;
					}
				}
			}
		}

		if (!empty($_GPC['time_limit']) && $_GPC['timetype']) {
			$starttime = strtotime($_GPC['time_limit']['start']);
			$endtime = strtotime($_GPC['time_limit']['end']);

			if ($_GPC['timetype'] == 1) {
				$where .= ' and createtime > ' . $starttime . ' and createtime < ' . $endtime;
				$where2 .= ' and a.createtime > ' . $starttime . ' and a.createtime < ' . $endtime;
			}
			else {
				$where .= ' and paytime > ' . $starttime . ' and paytime < ' . $endtime;
				$where2 .= ' and a.paytime > ' . $starttime . ' and a.paytime < ' . $endtime;
			}
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if ($_GPC['plugin']) {
			$plugin = $_GPC['plugin'];

			if ($plugin == 'rush') {
				$where2 .= ' AND  a.plugin IN (\'null\') ';
			}
			else {
				$where2 .= ' AND  a.plugin = \'' . $plugin . '\' ';
				$where .= ' and orderno = 00000 ';
			}
		}
		else {
			$where2 .= ' AND  a.plugin IN (\'wlfightgroup\',\'groupon\',\'coupon\',\'bargain\') ';
		}

		if ($_GPC['keywordtype'] == 2 && $_GPC['keyword']) {
			$where .= ' and activityid = ' . $keyword . ' ';
			$where2 .= ' and  a.fkid = ' . $keyword . ' ';
		}

		if ($_GPC['fightgroupid']) {
			$where2 .= ' and  a.fightgroupid = ' . $_GPC['fightgroupid'] . ' ';
		}

		if ($_GPC['export']) {
			$this->export($where, $where2);
		}

		$total1 = pdo_fetchcolumn('SELECT COUNT(a.id) FROM ' . tablename(PDO_NAME . 'order') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'fightgroup_userecord') . ' f ON a.id = f.orderid AND a.plugin = \'wlfightgroup\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'groupon_userecord') . ' g ON a.id = g.orderid AND a.plugin = \'groupon\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'member_coupons') . ' `c` ON a.orderno = `c`.orderno AND a.plugin = \'coupon\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'bargain_userlist') . (' b ON a.id = b.orderid AND a.plugin = \'bargain\' ' . $where2));
		$total2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'rush_order') . (' ' . $where));
		$total = $total1 + $total2;
		$goodtotal1 = pdo_fetchcolumn('SELECT SUM(a.num) FROM ' . tablename(PDO_NAME . 'order') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'fightgroup_userecord') . ' f ON a.id = f.orderid AND a.plugin = \'wlfightgroup\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'groupon_userecord') . ' g ON a.id = g.orderid AND a.plugin = \'groupon\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'member_coupons') . ' `c` ON a.orderno = `c`.orderno AND a.plugin = \'coupon\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'bargain_userlist') . (' b ON a.id = b.orderid AND a.plugin = \'bargain\' ' . $where2));
		$goodtotal2 = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename(PDO_NAME . 'rush_order') . (' ' . $where));
		$goodtotal = $goodtotal1 + $goodtotal2;
		$allprice1 = pdo_fetchcolumn('SELECT SUM(a.price) FROM ' . tablename(PDO_NAME . 'order') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'fightgroup_userecord') . ' f ON a.id = f.orderid AND a.plugin = \'wlfightgroup\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'groupon_userecord') . ' g ON a.id = g.orderid AND a.plugin = \'groupon\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'member_coupons') . ' `c` ON a.orderno = `c`.orderno AND a.plugin = \'coupon\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'bargain_userlist') . (' b ON a.id = b.orderid AND a.plugin = \'bargain\' ' . $where2));
		$allprice2 = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename(PDO_NAME . 'rush_order') . (' ' . $where));
		$allprice = sprintf('%.2f', $allprice1 + $allprice2);
		$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
		$orderlist = self::getOrderInfo($where, $where2, $limit);

		foreach ($orderlist as $key => &$v) {
			if ($v['a'] == 'a') {
				$ndorder = pdo_get(PDO_NAME . 'order', array('id' => $v['id'], 'uniacid' => $_W['uniacid']), array('id', 'name', 'mobile', 'fightgroupid', 'payfor', 'plugin', 'fkid', 'specid', 'fightstatus', 'expressid', 'recordid', 'goodsprice', 'card_fee', 'remark', 'buyremark'));

				if (empty($v['paytype'])) {
					$paylog = pdo_get('core_paylog', array('tid' => $v['orderno']), array('type'));
					$paytypearray = array('credit' => 1, 'wechat' => 2, 'alipay' => 3, 'delivery' => 4, 'wxapp' => 5);
					$paytype = $paytypearray[$paylog['type']];
					$v['paytype'] = $paytype;
					pdo_update('wlmerchant_order', array('paytype' => $paytype), array('id' => $v['id']));
				}

				if ($ndorder['plugin'] == 'coupon') {
					$goods = wlCoupon::getSingleCoupons($ndorder['fkid'], 'title,logo,id,isdistri');
					$v['goodsname'] = $goods['title'];
					$v['isdistri'] = $goods['isdistri'];
					$v['unit'] = '张';
					$v['goodsimg'] = tomedia($goods['logo']);
					$v['actualprice'] = $v['price'];
					$v['price'] = $ndorder['goodsprice'];
					$v['goodsprice'] = sprintf('%.2f', $ndorder['goodsprice'] / $v['num']);
					$v['plugintext'] = '卡券';
					$v['plugincss'] = 'danger';
					$v['detailurl'] = web_url('order/wlOrder/orderdetail', array('orderid' => $v['id'], 'type' => 3));
				}
				else if ($ndorder['plugin'] == 'wlfightgroup') {
					$goods = Wlfightgroup::getSingleGood($ndorder['fkid'], 'name,logo,id,unit,vipdiscount,isdistri');
					$group = pdo_get('wlmerchant_fightgroup_group', array('id' => $ndorder['fightgroupid']));

					if ($group['status'] == 1) {
						$v['status'] = 10;
					}

					$v['goodsname'] = $goods['name'];
					$v['isdistri'] = $goods['isdistri'];
					$v['unit'] = $goods['unit'];
					$v['goodsimg'] = tomedia($goods['logo']);
					$v['actualprice'] = $v['price'];
					$v['price'] = $ndorder['goodsprice'];
					$v['goodsprice'] = sprintf('%.2f', $ndorder['goodsprice'] / $v['num']);
					$v['plugintext'] = '拼团';
					$v['plugincss'] = 'warning';
					$v['detailurl'] = web_url('order/wlOrder/orderdetail', array('orderid' => $v['id'], 'type' => 2));

					if ($ndorder['specid']) {
						$v['optiontitle'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $ndorder['specid']), 'title');
					}

					if ($v['vipbuyflag']) {
						$v['vipdiscount'] = sprintf('%.2f', $goods['vipdiscount'] * $v['num']);
					}

					$v['dkmoney'] = sprintf('%.2f', $ndorder['card_fee'] - $v['vipdiscount']);
				}
				else if ($ndorder['plugin'] == 'groupon') {
					$goods = pdo_get('wlmerchant_groupon_activity', array('id' => $ndorder['fkid']), array('name', 'isdistri', 'thumb', 'id', 'unit'));
					$v['goodsname'] = $goods['name'];
					$v['isdistri'] = $goods['isdistri'];
					$v['unit'] = $goods['unit'];
					$v['goodsimg'] = tomedia($goods['thumb']);
					$v['actualprice'] = $v['price'];
					$v['price'] = $ndorder['goodsprice'];
					$v['goodsprice'] = sprintf('%.2f', $ndorder['goodsprice'] / $v['num']);
					$v['plugintext'] = '团购';
					$v['plugincss'] = 'info';
					$v['detailurl'] = web_url('order/wlOrder/orderdetail', array('orderid' => $v['id'], 'type' => 10));

					if ($ndorder['specid']) {
						$v['optiontitle'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $ndorder['specid']), 'title');
					}
				}
				else {
					if ($ndorder['plugin'] == 'bargain') {
						$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $ndorder['fkid']), array('name', 'isdistri', 'thumb', 'id', 'unit'));
						$v['goodsname'] = $goods['name'];
						$v['isdistri'] = $goods['isdistri'];
						$v['unit'] = $goods['unit'];
						$v['goodsimg'] = tomedia($goods['thumb']);
						$v['actualprice'] = $v['price'];
						$v['price'] = $ndorder['goodsprice'];
						$v['goodsprice'] = sprintf('%.2f', $ndorder['goodsprice'] / $v['num']);
						$v['plugintext'] = '砍价';
						$v['plugincss'] = 'primary';
						$v['detailurl'] = web_url('order/wlOrder/orderdetail', array('orderid' => $v['id'], 'type' => 12));
					}
				}

				$v['expressid'] = $ndorder['expressid'];
				$v['remark'] = $ndorder['remark'];
				$v['buyremark'] = $ndorder['buyremark'];
				$v['username'] = $ndorder['name'];
				$v['mobile'] = $ndorder['mobile'];
				$v['goodsid'] = $ndorder['fkid'];
			}
			else {
				$rushorder = pdo_get(PDO_NAME . 'rush_order', array('id' => $v['id'], 'uniacid' => $_W['uniacid']), array('activityid', 'username', 'mobile', 'optionid', 'dkmoney', 'actualprice', 'remark', 'adminremark', 'expressid'));

				if (empty($v['paytype'])) {
					$paylog = pdo_get('core_paylog', array('tid' => $v['orderno']), array('type'));
					$paytypearray = array('credit' => 1, 'wechat' => 2, 'alipay' => 3, 'delivery' => 4, 'wxapp' => 5);
					$paytype = $paytypearray[$paylog['type']];
					$v['paytype'] = $paytype;
					pdo_update('wlmerchant_rush_order', array('paytype' => $paytype), array('id' => $v['id']));
				}

				$v['activityid'] = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $v['id'], 'uniacid' => $_W['uniacid']), 'activityid');
				$goods = Rush::getSingleActive($v['activityid'], 'name,thumb,id,isdistri,cutofftime,unit');
				$v['unit'] = $goods['unit'];
				$v['isdistri'] = $goods['isdistri'];
				$v['mobile'] = $goods['mobile'];
				$v['goodsimg'] = tomedia($goods['thumb']);
				$v['goodsname'] = $goods['name'];

				if ($rushorder['expressid']) {
					$expressprice = pdo_getcolumn(PDO_NAME . 'express', array('id' => $rushorder['expressid']), 'expressprice');
					$rushprice = $v['price'] - $expressprice;
				}
				else {
					$rushprice = $v['price'];
				}

				$v['goodsprice'] = sprintf('%.2f', $rushprice / $v['num']);
				$v['actualprice'] = $rushorder['actualprice'];
				$v['plugin'] = 'rush';
				$v['plugintext'] = '抢购';
				$v['plugincss'] = 'success';
				$v['detailurl'] = web_url('order/wlOrder/orderdetail', array('orderid' => $v['id'], 'type' => 1));

				if ($rushorder['optionid']) {
					$v['optiontitle'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $rushorder['optionid']), 'title');
				}

				$v['remark'] = $rushorder['adminremark'];
				$v['buyremark'] = $rushorder['remark'];
				$v['expressid'] = $rushorder['expressid'];
				$v['dkmoney'] = $rushorder['dkmoney'];
				$v['username'] = $rushorder['username'];
				$v['mobile'] = $rushorder['mobile'];
				$v['goodsid'] = $rushorder['activityid'];
			}

			$v['merchantName'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $v['sid'], 'uniacid' => $_W['uniacid']), 'storename');
			$member = pdo_get(PDO_NAME . 'member', array('id' => $v['mid']), array('nickname', 'avatar', 'mobile', 'realname'));

			if (!empty($v['username'])) {
				$member['realname'] = $v['username'];
			}

			if (!empty($v['mobile'])) {
				$member['mobile'] = $v['mobile'];
			}

			$v['member'] = $member;

			if ($v['expressid']) {
				$v['express'] = pdo_get(PDO_NAME . 'express', array('id' => $v['expressid']), array('id', 'expressprice', 'expressname', 'expresssn'));
			}

			if ($v['disorderid']) {
				$v['merchname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $v['sid']), 'storename');
				$disorder = pdo_get('wlmerchant_disorder', array('id' => $v['disorderid']));
				$v['disorderstatus'] = $disorder['status'];
				$leadmoney = unserialize($disorder['leadmoney']);

				if ($disorder['twoleadid']) {
					$v['level'] = '2';
				}
				else {
					$v['level'] = '1';
				}

				$v['onecommission'] = $leadmoney['one'];
				$onecom = pdo_get(PDO_NAME . 'distributor', array('id' => $disorder['oneleadid']), array('nickname', 'mid'));
				$v['onecomname'] = $onecom['nickname'];
				$v['onecommid'] = $onecom['mid'];
				$v['twocommission'] = $leadmoney['two'];
				$twocom = pdo_get(PDO_NAME . 'distributor', array('id' => $disorder['twoleadid']), array('nickname', 'mid'));
				$v['twocomname'] = $twocom['nickname'];
				$v['twocommid'] = $twocom['mid'];
				$v['commission'] = sprintf('%.2f', $v['onecommission'] + $v['twocommission']);
			}

			switch ($v['status']) {
			case '0':
				$v['statuscss'] = 'defualt';
				$v['statustext'] = '未支付';
				break;

			case '1':
				$v['statuscss'] = 'info';
				$v['statustext'] = '已支付';
				break;

			case '2':
				$v['statuscss'] = 'success';
				$v['statustext'] = '待评价';
				break;

			case '3':
				$v['statuscss'] = 'success';
				$v['statustext'] = '已完成';
				break;

			case '4':
				$v['statuscss'] = 'success';
				$v['statustext'] = '待收货';
				break;

			case '5':
				$v['statuscss'] = 'defualt';
				$v['statustext'] = '已取消';
				break;

			case '6':
				$v['statuscss'] = 'warning';
				$v['statustext'] = '待退款';
				break;

			case '7':
				$v['statuscss'] = 'defualt';
				$v['statustext'] = '已退款';
				break;

			case '8':
				$v['statuscss'] = 'info';
				$v['statustext'] = '待发货';
				break;

			case '9':
				$v['statuscss'] = 'danger';
				$v['statustext'] = '已过期';
				break;

			case '10':
				$v['statuscss'] = 'info';
				$v['statustext'] = '组团中';
				break;

			default:
				$v['statuscss'] = 'danger';
				$v['statustext'] = '错误状态';
				break;
			}
		}

		$pager = pagination($total, $pindex, $psize);
		include wl_template('order/orderlist');
	}

	public function export($where, $where2)
	{
		global $_W;
		global $_GPC;
		$limit = ' LIMIT 20000';
		$orderlist = self::getOrderInfo($where, $where2, $limit);

		foreach ($orderlist as $key => &$va) {
			if ($va['a'] == 'a') {
				$ndorder = pdo_get(PDO_NAME . 'order', array('id' => $va['id'], 'uniacid' => $_W['uniacid']), array('id', 'payfor', 'plugin', 'mobile', 'fkid', 'fightstatus', 'neworderflag', 'expressid', 'recordid', 'goodsprice', 'card_fee', 'remark', 'buyremark', 'specid'));

				if ($ndorder['plugin'] == 'wlfightgroup') {
					$va['plugin'] = '拼团';
					$va['gname'] = pdo_getcolumn(PDO_NAME . 'fightgroup_goods', array('id' => $ndorder['fkid']), 'name');

					if ($ndorder['specid']) {
						$va['option'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $ndorder['specid']), 'title');
					}

					if ($ndorder['recordid']) {
						$va['usedtime'] = pdo_getcolumn(PDO_NAME . 'fightgroup_userecord', array('id' => $ndorder['recordid']), 'usedtime');
						$va['checkcode'] = pdo_getcolumn(PDO_NAME . 'fightgroup_userecord', array('id' => $ndorder['recordid']), 'qrcode');
					}
				}
				else if ($ndorder['plugin'] == 'groupon') {
					$va['plugin'] = '团购';
					$va['gname'] = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $ndorder['fkid']), 'name');

					if ($ndorder['specid']) {
						$va['option'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $ndorder['specid']), 'title');
					}

					if ($ndorder['recordid']) {
						$va['usedtime'] = pdo_getcolumn(PDO_NAME . 'groupon_userecord', array('id' => $ndorder['recordid']), 'usedtime');
						$va['checkcode'] = pdo_getcolumn(PDO_NAME . 'groupon_userecord', array('id' => $ndorder['recordid']), 'qrcode');
					}
				}
				else if ($ndorder['plugin'] == 'coupon') {
					$va['plugin'] = '卡券';
					$va['gname'] = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $ndorder['fkid']), 'title');

					if ($ndorder['recordid']) {
						$va['usedtime'] = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $ndorder['recordid']), 'usedtime');
						$va['checkcode'] = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $ndorder['recordid']), 'concode');
					}
				}
				else {
					if ($ndorder['plugin'] == 'bargain') {
						$va['plugin'] = '砍价';
						$va['gname'] = pdo_getcolumn(PDO_NAME . 'bargain_activity', array('id' => $ndorder['fkid']), 'name');
						$va['usedtime'] = pdo_getcolumn(PDO_NAME . 'bargain_userlist', array('id' => $ndorder['specid']), 'usedtime');
						$va['checkcode'] = pdo_getcolumn(PDO_NAME . 'bargain_userlist', array('id' => $ndorder['specid']), 'qrcode');
					}
				}

				$va['remark'] = $ndorder['buyremark'];
				$va['expressid'] = $ndorder['expressid'];
				$va['mobile'] = $ndorder['mobile'];
				$va['remark'] = $ndorder['remark'];
				$va['buyremark'] = $ndorder['buyremark'];
				$va['small'] = pdo_getall('wlmerchant_smallorder', array('orderid' => $ndorder['id'], 'plugin' => $ndorder['plugin']), '', '', 'status ASC,hexiaotime ASC');
				$va['neworderflag'] = $ndorder['neworderflag'];
			}
			else {
				$rushorder = pdo_get(PDO_NAME . 'rush_order', array('id' => $va['id'], 'uniacid' => $_W['uniacid']), array('actualprice', 'neworderflag', 'remark', 'adminremark', 'usedtime', 'optionid', 'checkcode', 'adminremark', 'activityid', 'mobile', 'address', 'username'));
				$va['plugin'] = '抢购';
				$va['gname'] = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $rushorder['activityid']), 'name');
				$va['usedtime'] = $rushorder['usedtime'];
				$va['checkcode'] = $rushorder['checkcode'];

				if ($rushorder['optionid']) {
					$va['option'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $rushorder['optionid']), 'title');
				}

				$va['peoplename'] = $rushorder['username'];
				$va['tel'] = $rushorder['mobile'];
				$va['address'] = $rushorder['address'];
				$va['mobile'] = $rushorder['mobile'];
				$va['remark'] = $rushorder['adminremark'];
				$va['buyremark'] = $rushorder['remark'];
				$va['small'] = pdo_getall('wlmerchant_smallorder', array('orderid' => $va['id'], 'plugin' => 'rush'), '', '', 'status ASC,hexiaotime ASC');
				$va['neworderflag'] = $rushorder['neworderflag'];
			}

			$va['merchantName'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $va['sid']), 'storename');
			$va['salesmid'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $va['sid']), 'salesmid');
			$member = pdo_get('wlmerchant_member', array('id' => $va['mid']), array('nickname', 'mobile'));
			$va['nickname'] = $member['nickname'];

			if (empty($va['mobile'])) {
				$va['mobile'] = $member['mobile'];
			}

			if ($va['expressid']) {
				$express = pdo_get('wlmerchant_express', array('id' => $va['expressid']), array('name', 'tel', 'address', 'expressname', 'expresssn'));
				$va['peoplename'] = $express['name'];
				$va['tel'] = $express['tel'];
				$va['address'] = $express['address'];
				$va['expressname'] = $express['expressname'];
				$va['expresssn'] = $express['expresssn'];
			}

			if ($va['disorderid']) {
				$disorder = pdo_get('wlmerchant_disorder', array('id' => $va['disorderid']));
				$leadmoney = unserialize($disorder['leadmoney']);
				$va['onecommission'] = $leadmoney['one'];
				$onecom = pdo_get(PDO_NAME . 'distributor', array('id' => $disorder['oneleadid']), array('nickname', 'mid'));
				$va['onecomname'] = $onecom['nickname'];
				$va['onecommid'] = $onecom['mid'];
				$va['twocommission'] = $leadmoney['two'];
				$twocom = pdo_get(PDO_NAME . 'distributor', array('id' => $disorder['twoleadid']), array('nickname', 'mid'));
				$va['twocomname'] = $twocom['nickname'];
				$va['twocommid'] = $twocom['mid'];
				$va['commission'] = sprintf('%.2f', $va['onecommission'] + $va['twocommission']);
			}

			$va['orderno'] = $va['orderno'] . '	';
		}

		$filter = array('id' => '订单id', 'orderno' => '订单号', 'plugin' => '所属应用', 'gname' => '商品名称', 'option' => '规格名称', 'num' => '数量', 'merchantName' => '所属商家', 'nickname' => '买家昵称', 'mobile' => '买家电话', 'status' => '订单状态', 'paytype' => '支付方式', 'createtime' => '下单时间', 'paytime' => '支付时间', 'price' => '实付金额', 'buyremark' => '买家备注', 'remark' => '卖家备注', 'peoplename' => '收货人姓名', 'tel' => '收货人电话', 'address' => '收货人地址', 'expressname' => '物流公司', 'expresssn' => '快递单号', 'checkcode' => '核销码', 'hexiaotime' => '核销时间', 'vermember' => '核销员', 'salesmid' => '业务员', 'commission' => '分销总佣金', 'onecommission' => '一级分销佣金', 'onecommid' => '一级分销商MID', 'onecomname' => '一级分销商昵称', 'twocommission' => '二级分销佣金', 'twocommid' => '二级分销商MID', 'twocomname' => '二级分销商昵称');
		$data = array();
		$i = 0;

		while ($i < count($orderlist)) {
			foreach ($filter as $key => $title) {
				$data[$i][$key] = $orderlist[$i]['id'];
				if ($key == 'createtime' || $key == 'paytime') {
					$data[$i][$key] = date('Y-m-d H:i:s', $orderlist[$i][$key]);
				}
				else if ($key == 'status') {
					switch ($orderlist[$i][$key]) {
					case '1':
						$data[$i][$key] = '已支付';
						break;

					case '2':
						$data[$i][$key] = '已消费';
						break;

					case '3':
						$data[$i][$key] = '已完成';
						break;

					case '4':
						$data[$i][$key] = '待使用';
						break;

					case '5':
						$data[$i][$key] = '已取消';
						break;

					case '6':
						$data[$i][$key] = '待退款';
						break;

					case '7':
						$data[$i][$key] = '已退款';
						break;

					case '8':
						$data[$i][$key] = '待收货';
						break;

					case '9':
						$data[$i][$key] = '已过期';
						break;

					default:
						$data[$i][$key] = '未支付';
						break;
					}
				}
				else if ($key == 'paytype') {
					switch ($orderlist[$i][$key]) {
					case '1':
						$data[$i][$key] = '余额支付';
						break;

					case '2':
						$data[$i][$key] = '微信支付';
						break;

					case '3':
						$data[$i][$key] = '支付宝支付';
						break;

					case '4':
						$data[$i][$key] = '货到付款';
						break;

					default:
						$data[$i][$key] = '其他或未支付';
						break;
					}
				}
				else if ($key == 'checkcode') {
					if ($orderlist[$i]['neworderflag']) {
						$checkcode = '';

						foreach ($orderlist[$i]['small'] as $kchcek => $sm) {
							if ($kchcek != 0) {
								$checkcode .= '||' . $sm['checkcode'];
							}
							else {
								$checkcode .= $sm['checkcode'];
							}
						}

						$data[$i][$key] = $checkcode;
					}
					else {
						$data[$i][$key] = $orderlist[$i][$key];
					}
				}
				else if ($key == 'hexiaotime') {
					$usedrecord = '';

					if ($orderlist[$i]['neworderflag']) {
						foreach ($orderlist[$i]['small'] as $ktime => $sm) {
							if ($ktime != 0) {
								if ($sm['status'] == 1) {
									$usedrecord .= ' || 未核销';
								}
								else if ($sm['status'] == 2) {
									$usedrecord .= ' || ' . date('Y-m-d H:i:s', $sm['hexiaotime']);
								}
								else {
									if ($sm['status'] == 3) {
										$usedrecord .= ' || ' . date('Y-m-d H:i:s', $sm['refundtime']);
									}
								}
							}
							else if ($sm['status'] == 1) {
								$usedrecord .= '未核销';
							}
							else if ($sm['status'] == 2) {
								$usedrecord .= date('Y-m-d H:i:s', $sm['hexiaotime']);
							}
							else {
								if ($sm['status'] == 3) {
									$usedrecord .= date('Y-m-d H:i:s', $sm['refundtime']);
								}
							}
						}
					}
					else {
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
					}

					$data[$i][$key] = $usedrecord;
				}
				else if ($key == 'vermember') {
					$vermembers = '';

					if ($orderlist[$i]['neworderflag']) {
						foreach ($orderlist[$i]['small'] as $kuid => $sm) {
							if ($sm['status'] == 1) {
								$verm = '未核销';
							}
							else if ($sm['status'] == 2) {
								$verm = pdo_getcolumn(PDO_NAME . 'merchantuser', array('id' => $sm['hxuid']), 'name');
							}
							else {
								if ($sm['status'] == 3) {
									$verm = '已退款';
								}
							}

							if ($kuid != 0) {
								$vermembers .= ' || ' . $verm;
							}
							else {
								$vermembers .= $verm;
							}
						}
					}
					else {
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
					}

					$data[$i][$key] = $vermembers;
				}
				else {
					$data[$i][$key] = $orderlist[$i][$key];
				}
			}

			++$i;
		}

		util_csv::export_csv_2($data, $filter, '订单表.csv');
		exit();
	}

	public function orderdetail()
	{
		global $_W;
		global $_GPC;
		$currentid = $_GPC['currentid'];
		$orderid = $_GPC['orderid'];
		$type = $_GPC['type'];

		if ($currentid) {
			$current = pdo_get('wlmerchant_current', array('id' => $currentid), array('type', 'orderid'));
			$type = $current['type'];
			$orderid = $current['orderid'];
		}

		if ($type == 1) {
			$order = pdo_get('wlmerchant_rush_order', array('id' => $orderid));
			$order['remark'] = $order['adminremark'];
			$goodsprice = $order['price'];
			$order['ordera'] = 'b';

			if ($order['optionid']) {
				$order['optiontitle'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $order['optionid']), 'title');
			}
		}
		else if ($type == 4) {
			$order = pdo_get('wlmerchant_halfcard_record', array('id' => $orderid));
			$goodsprice = $order['price'];
		}
		else {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid));
			$goodsprice = $order['goodsprice'];
			$order['ordera'] = 'a';
			if ($order['specid'] && $type != 12 && $type != 8) {
				$order['optiontitle'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $order['specid']), 'title');
			}

			$order['username'] = $order['name'];

			if ($order['recordid']) {
				if ($order['plugin'] == 'groupon') {
					$order['checkcode'] = pdo_getcolumn(PDO_NAME . 'groupon_userecord', array('id' => $order['recordid']), 'qrcode');
				}
				else if ($order['plugin'] == 'wlfightgroup') {
					$order['checkcode'] = pdo_getcolumn(PDO_NAME . 'fightgroup_userecord', array('id' => $order['recordid']), 'qrcode');
				}
				else {
					if ($order['plugin'] == 'bargain') {
						$order['checkcode'] = pdo_getcolumn(PDO_NAME . 'bargain_userlist', array('id' => $order['recordid']), 'qrcode');
					}
				}
			}
		}

		if ($order['transid']) {
			$order['wqorderno'] = pdo_getcolumn('core_paylog', array('uniacid' => $_W['uniacid'], 'tid' => $order['orderno']), 'uniontid');
		}

		switch ($type) {
		case '1':
			$plugin = 'rush';
			break;

		case '2':
			$plugin = 'wlfightgroup';
			break;

		case '3':
			$plugin = 'coupon';
			break;

		case '9':
			$plugin = 'activity';
			break;

		case '10':
			$plugin = 'groupon';
			break;

		case '11':
			$plugin = 'halfcard';
			break;

		case '12':
			$plugin = 'bargain';
			break;
		}

		$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('nickname', 'mobile', 'avatar', 'realname', 'id'));
		$order['avatar'] = $member['avatar'];

		switch ($order['status']) {
		case '0':
			$order['statuscss'] = 'default';
			$order['statustext'] = '未支付';
			break;

		case '1':
			$order['statuscss'] = 'info';
			$order['statustext'] = '已支付';
			break;

		case '2':
			$order['statuscss'] = 'success';
			$order['statustext'] = '待评价';
			break;

		case '3':
			$order['statuscss'] = 'success';
			$order['statustext'] = '已完成';
			break;

		case '4':
			$order['statuscss'] = 'success';
			$order['statustext'] = '待收货';
			break;

		case '5':
			$order['statuscss'] = 'defualt';
			$order['statustext'] = '已取消';
			break;

		case '6':
			$order['statuscss'] = 'warning';
			$order['statustext'] = '待退款';
			break;

		case '7':
			$order['statuscss'] = 'defualt';
			$order['statustext'] = '已退款';
			break;

		case '8':
			$order['statuscss'] = 'info';
			$order['statustext'] = '待发货';
			break;

		case '9':
			$order['statuscss'] = 'danger';
			$order['statustext'] = '已过期';
			break;

		default:
			$order['statuscss'] = 'danger';
			$order['statustext'] = '错误状态';
			break;
		}

		switch ($order['paytype']) {
		case '1':
			$order['paytypecss'] = 'info';
			$order['paytypetext'] = '余额支付';
			break;

		case '2':
			$order['paytypecss'] = 'success';
			$order['paytypetext'] = '微信支付';
			break;

		case '3':
			$order['paytypecss'] = 'danger';
			$order['paytypetext'] = '支付宝';
			break;

		case '4':
			$order['paytypecss'] = 'warning';
			$order['paytypetext'] = '货到付款';
			break;

		case '5':
			$order['paytypecss'] = 'warning';
			$order['paytypetext'] = '小程序支付';
			break;

		default:
			$order['paytypecss'] = 'default';
			$order['paytypetext'] = '未知方式';
			break;
		}

		$logs = array();
		$logs[] = array('time' => $order['createtime'], 'title' => '订单提交成功', 'detail' => '单号:' . $order['orderno'] . '，等待买家付款');

		if ($type == 1) {
			$usedtime = unserialize($order['usedtime']);

			if ($order['paytime']) {
				$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '支付成功，等待买家前往商户核销订单或商家发货');
			}

			if ($order['expressid']) {
				$express = pdo_get('wlmerchant_express', array('id' => $order['expressid']), array('expressname', 'expresssn', 'expressprice', 'sendtime', 'receivetime'));
				$expressprice = $express['expressprice'];

				if ($express['sendtime']) {
					$logs[] = array('time' => $express['sendtime'], 'title' => '商品已发货', 'detail' => '快递公司:' . $express['expressname'] . ',快递单号:' . $express['expresssn'] . ',等待买家收货');
				}

				if ($express['receivetime']) {
					$logs[] = array('time' => $express['receivetime'], 'title' => '商品已签收', 'detail' => '用户已签收商品，等待系统结算订单');
				}
			}
		}

		if ($type == 2) {
			if ($order['fightstatus'] == 1) {
				if ($order['paytime']) {
					$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '支付成功，等待组团成功');
				}

				$group = pdo_get('wlmerchant_fightgroup_group', array('id' => $order['fightgroupid']));

				if ($group['status'] == 2) {
					$logs[] = array('time' => $group['successtime'], 'title' => '组团成功', 'detail' => '拼团组团成功，等待买家前往商户核销订单或商家发货');
				}
				else {
					if ($group['status'] == 3) {
						$logs[] = array('time' => $group['failtime'], 'title' => '组团失败', 'detail' => '拼团组团失败，即将给买家退款');
					}
				}
			}
			else {
				if ($order['paytime']) {
					$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '支付成功，等待买家前往商户核销订单或商家发货');
				}
			}

			if ($order['recordid']) {
				$record = pdo_get('wlmerchant_fightgroup_userecord', array('id' => $order['recordid']), array('usedtime'));
				$usedtime = unserialize($record['usedtime']);
			}
			else {
				if ($order['expressid']) {
					$express = pdo_get('wlmerchant_express', array('id' => $order['expressid']), array('expressname', 'expresssn', 'expressprice', 'sendtime', 'receivetime'));
					$expressprice = $express['expressprice'];

					if ($express['sendtime']) {
						$logs[] = array('time' => $express['sendtime'], 'title' => '商品已发货', 'detail' => '快递公司:' . $express['expressname'] . ',快递单号:' . $express['expresssn'] . ',等待买家收货');
					}

					if ($express['receivetime']) {
						$logs[] = array('time' => $express['receivetime'], 'title' => '商品已签收', 'detail' => '用户已签收商品，等待系统结算订单');
					}
				}
			}
		}

		if ($type == 3) {
			if ($order['paytime']) {
				$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '支付成功，等待买家前往商户核销订单');
			}

			$usedtime = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $order['recordid']), 'usedtime');
			$usedtime = unserialize($usedtime);
		}

		if ($type == 4) {
			if ($order['paytime']) {
				$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '支付成功，买家已成功开通会员，等待系统结算');
			}
		}

		if ($type == 5) {
			if ($order['paytime']) {
				$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '支付成功，帖子已发布，等待系统结算');
			}
		}

		if ($type == 6) {
			if ($order['paytime']) {
				$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '支付成功，商家已入住，等待审核，订单等待系统结算');
			}
		}

		if ($type == 8) {
			if ($order['paytime']) {
				$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '支付成功，分销商申请成功，等待审核，订单等待系统结算');
			}
		}

		if ($type == 10) {
			if ($order['paytime']) {
				$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '支付成功，等待买家前往商户核销订单');
			}

			if ($order['expressid']) {
				$express = pdo_get('wlmerchant_express', array('id' => $order['expressid']), array('expressname', 'expresssn', 'expressprice', 'sendtime', 'receivetime'));
				$expressprice = $express['expressprice'];

				if ($express['sendtime']) {
					$logs[] = array('time' => $express['sendtime'], 'title' => '商品已发货', 'detail' => '快递公司:' . $express['expressname'] . ',快递单号:' . $express['expresssn'] . ',等待买家收货');
				}

				if ($express['receivetime']) {
					$logs[] = array('time' => $express['receivetime'], 'title' => '商品已签收', 'detail' => '用户已签收商品，等待系统结算订单');
				}
			}
			else {
				$usedtime = pdo_getcolumn(PDO_NAME . 'groupon_userecord', array('id' => $order['recordid']), 'usedtime');
				$usedtime = unserialize($usedtime);
			}
		}

		if ($type == 11) {
			if ($order['paytime']) {
				$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '在线买单支付成功，订单等待系统结算');
			}
		}

		if ($type == 12) {
			if ($order['expressid']) {
				if ($order['paytime']) {
					$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '订单支付成功,等待商家发货');
				}

				$express = pdo_get('wlmerchant_express', array('id' => $order['expressid']), array('expressname', 'expresssn', 'expressprice', 'sendtime', 'receivetime'));
				$expressprice = $express['expressprice'];

				if ($express['sendtime']) {
					$logs[] = array('time' => $express['sendtime'], 'title' => '商品已发货', 'detail' => '快递公司:' . $express['expressname'] . ',快递单号:' . $express['expresssn'] . ',等待买家收货');
				}

				if ($express['receivetime']) {
					$logs[] = array('time' => $express['receivetime'], 'title' => '商品已签收', 'detail' => '用户已签收商品，等待系统结算订单');
				}
			}
			else {
				if ($order['paytime']) {
					$logs[] = array('time' => $order['paytime'], 'title' => '订单支付成功', 'detail' => '订单支付成功,等待买家到店核销');
				}

				$record = pdo_get('wlmerchant_bargain_userlist', array('id' => $order['specid']), array('usedtime'));
				$usedtime = unserialize($record['usedtime']);
			}
		}

		if ($usedtime) {
			foreach ($usedtime as $key => $used) {
				switch ($used['type']) {
				case '1':
					$used['typename'] = '输码核销';
					break;

				case '2':
					$used['typename'] = '扫码核销';
					break;

				case '3':
					$used['typename'] = '后台核销';
					break;

				case '4':
					$used['typename'] = '密码核销';
					break;

				default:
					$used['typename'] = '未知方式';
					break;
				}

				if ($used['type'] == 1 || $used['type'] == 2) {
					$used['vername'] = pdo_getcolumn(PDO_NAME . 'merchantuser', array('mid' => $used['ver'], 'storeid' => $order['sid']), 'name');
				}
				else {
					$used['vername'] = '无';
				}

				$kk = $key + 1;
				$logs[] = array('time' => $used['time'], 'title' => '第' . $kk . '次核销', 'detail' => '核销方式:' . $used['typename'] . ',核销员:' . $used['vername'] . '。');
				if (empty($order['usetimes']) && $kk == count($usedtime)) {
					$logs[] = array('time' => $used['time'], 'title' => '核销完成', 'detail' => '订单已全部核销，等待系统结算订单');
				}
			}
		}

		if ($order['neworderflag']) {
			$hexiaotype = array(1 => '输码核销', 2 => '扫码核销', 3 => '后台核销', 4 => '密码核销');
			$smallorders = pdo_getall('wlmerchant_smallorder', array('orderid' => $order['id'], 'plugin' => $plugin));

			foreach ($smallorders as $key => $small) {
				if ($small['status'] == 2) {
					$smalltype = $small['hexiaotype'];

					if ($small['hxuid']) {
						$vername = pdo_getcolumn(PDO_NAME . 'merchantuser', array('id' => $small['hxuid']), 'name');
					}
					else {
						$vername = '无';
					}

					$logs[] = array('time' => $small['hexiaotime'], 'title' => '核销码：' . $small['checkcode'], 'detail' => '核销方式:' . $hexiaotype[$smalltype] . ',核销员:' . $vername . '。');
				}
				else {
					if ($small['status'] == 3) {
						$logs[] = array('time' => $small['refundtime'], 'title' => '核销码[' . $small['checkcode'] . ']已退款', 'detail' => '');
					}
				}
			}
		}

		if ($order['status'] == 7) {
			$logs[] = array('time' => $order['refundtime'], 'title' => '订单已退款', 'detail' => '订单已退款，此订单已结束');
		}

		if ($order['status'] == 9) {
			$logs[] = array('time' => $order['overtime'], 'title' => '订单已过期', 'detail' => '订单已过期，等待系统结算订单');
		}

		if ($order['issettlement'] == 1) {
			$settlement = pdo_fetch('SELECT * FROM ' . tablename('wlmerchant_autosettlement_record') . ('WHERE orderid = ' . $order['id'] . ' AND type = ' . $type . ' ORDER BY id DESC'));
			$logs[] = array('time' => $settlement['createtime'], 'title' => '订单已结算', 'detail' => '订单已结算，此订单已完成');
		}

		if ($order['status'] == 3) {
			$commenttime = pdo_getcolumn(PDO_NAME . 'comment', array('plugin' => $plugin, 'idoforder' => $order['id']), 'createtime');

			if ($commenttime) {
				$logs[] = array('time' => $commenttime, 'title' => '订单已评价', 'detail' => '用户就此订单对商户发表了评价');
			}
		}

		if ($type == 1) {
			$goods = pdo_get('wlmerchant_rush_activity', array('id' => $order['activityid']), array('id', 'price', 'aid', 'vipprice', 'name', 'thumb'));

			if ($order['optionid']) {
				$option = pdo_get('wlmerchant_goods_option', array('id' => $order['optionid']), array('price', 'vipprice'));
				$goods['price'] = $option['price'];
				$goods['vipprice'] = $option['vipprice'];
			}

			$dkmoney = $order['dkmoney'];
			$dkcredit = $order['dkcredit'];
			$actualprice = $order['actualprice'];
			$editurl = './agent.php?uniacid=' . $_W['uniacid'] . '&aid=' . $goods['aid'] . '&p=rush&ac=active&do=createactive&id=' . $goods['id'];
		}
		else if ($type == 2) {
			$goods = pdo_get('wlmerchant_fightgroup_goods', array('id' => $order['fkid']), array('id', 'price', 'aid', 'aloneprice', 'vipdiscount', 'name', 'logo'));

			if ($order['vipbuyflag']) {
				$vipdiscount = sprintf('%.2f', $goods['vipdiscount'] * $order['num']);
			}

			$dkmoney = sprintf('%.2f', $order['card_fee'] - $vipdiscount);
			$dkcredit = $order['card_id'];
			$goods['thumb'] = $goods['logo'];
			$actualprice = $order['price'];
			$editurl = './agent.php?uniacid=' . $_W['uniacid'] . '&aid=' . $goods['aid'] . '&p=wlfightgroup&ac=fightgoods&do=creategood&id=' . $goods['id'];
		}
		else if ($type == 3) {
			$goods = pdo_get('wlmerchant_couponlist', array('id' => $order['fkid']), array('id', 'price', 'aid', 'vipprice', 'title', 'logo'));
			$goods['name'] = $goods['title'];
			$goods['thumb'] = $goods['logo'];
			$actualprice = $order['price'];
			$editurl = './agent.php?uniacid=' . $_W['uniacid'] . '&aid=' . $goods['aid'] . '&p=wlcoupon&ac=couponlist&do=editCoupons&id=' . $goods['id'];
		}
		else if ($type == 10) {
			$goods = pdo_get('wlmerchant_groupon_activity', array('id' => $order['fkid']), array('id', 'aid', 'price', 'vipprice', 'name', 'thumb'));
			$actualprice = $order['price'];
			$editurl = './agent.php?uniacid=' . $_W['uniacid'] . '&aid=' . $goods['aid'] . '&p=groupon&ac=active&do=createactive&id=' . $goods['id'];
		}
		else if ($type == 4) {
			$goods = pdo_get('wlmerchant_halfcard_type', array('id' => $order['typeid']), array('id', 'price', 'name', 'logo'));
			$order['num'] = 1;
			$goods['thumb'] = $goods['logo'];
			$actualprice = $order['price'];
			$editurl = web_url('halfcard/halftype/add', array('id' => $goods['id']));
		}
		else if ($type == 5) {
			$informations = pdo_get('wlmerchant_pocket_informations', array('id' => $order['fkid']), array('type'));
			$goods = pdo_get('wlmerchant_pocket_type', array('id' => $informations['type']), array('title', 'price', 'img'));
			$goods['thumb'] = $goods['img'];
			$goods['name'] = '发布' . $goods['title'] . '信息';
			$actualprice = $order['price'];
			$editurl = web_url('pocket/Type/operating', array('eid' => $goods['id']));
		}
		else if ($type == 6) {
			$goods = pdo_get('wlmerchant_chargelist', array('id' => $order['fkid']), array('id', 'price', 'name'));
			$goods['thumb'] = URL_MODULE . 'web/resource/image/store.png';
			$goods['name'] = '付费入驻:' . $goods['name'];
			$actualprice = $order['price'];
			$order['num'] = 1;
			$editurl = web_url('setting/register/add', array('id' => $goods['id']));
		}
		else if ($type == 8) {
			$goods['thumb'] = URL_MODULE . 'web/resource/image/store.png';
			$goods['name'] = '付费申请成为分销商';
			$actualprice = $order['price'];
			$order['num'] = 1;
			$editurl = web_url('distribution/dissysbase/disbaseset');
		}
		else if ($type == 11) {
			$goods['thumb'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $order['sid']), 'logo');
			$goods['thumb'] = tomedia($goods['thumb']);
			$actualprice = $order['price'];
			$order['num'] = 1;

			if ($order['fkid']) {
				$goods['name'] = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('uniacid' => $_W['uniacid'], 'id' => $order['fkid']), 'title');
				$editurl = web_url('halfcard/halfcard_web/editHalfcard', array('id' => $order['fkid']));
			}
			else {
				$goods['name'] = '买家在线买单';
			}

			$vipdiscount = $order['card_fee'];
		}
		else {
			if ($type == 12) {
				$actualprice = $order['price'];
				$goods = pdo_get(PDO_NAME . 'bargain_activity', array('uniacid' => $_W['uniacid'], 'id' => $order['fkid']), array('id', 'thumb', 'name'));
				$goods['thumb'] = tomedia($goods['thumb']);
				$editurl = './agent.php?uniacid=' . $_W['uniacid'] . '&aid=' . $goods['aid'] . '&p=bargain&ac=bargain_web&do=createbargain&id=' . $goods['id'];
			}
		}

		if ($order['issettlement'] == 1) {
			if ($order['neworderflag']) {
				$merchantmoney = 0;
				$agentmoney = 0;
				$distrimoney = 0;
				$refundmoney = 0;

				foreach ($smallorders as $key => $small) {
					if ($small['status'] == 2) {
						$merchantmoney += $small['settlemoney'];
						$agentmoney += sprintf('%.2f', $small['orderprice'] - $small['settlemoney'] - $small['onedismoney'] - $small['twodismoney']);
						$distrimoney += sprintf('%.2f', $small['onedismoney'] + $small['twodismoney']);
					}
					else {
						if ($small['status'] == 3) {
							$refundmoney += $small['orderprice'];
						}
					}
				}
			}
			else {
				$agentmoney = $settlement['agentmoney'];
				$merchantmoney = $settlement['merchantmoney'];
				$distrimoney = $settlement['distrimoney'];
			}

			if (0 < $distrimoney) {
				$disorder = pdo_get('wlmerchant_disorder', array('id' => $order['disorderid']), array('oneleadid', 'twoleadid', 'leadmoney'));
				$onename = pdo_getcolumn(PDO_NAME . 'distributor', array('id' => $disorder['oneleadid']), 'nickname');
				$twoname = pdo_getcolumn(PDO_NAME . 'distributor', array('id' => $disorder['twoleadid']), 'nickname');
				$leadmoney = unserialize($disorder['leadmoney']);
			}

			if ($order['shareid']) {
				$shares = pdo_get('wlmerchant_sharegift_record', array('id' => $order['shareid']), array('price', 'type', 'mid'));
				$sharemoney = sprintf('%.2f', $shares['price'] * $order['num']);

				if ($shares['type'] == 2) {
					$sharename = pdo_getcolumn(PDO_NAME . 'member', array('id' => $shares['mid']), 'nickname');
				}
			}
		}

		if ($order['status'] == 7 && $order['neworderflag']) {
			$refundmoney = $actualprice;
			$merchantmoney = 0;
		}

		if ($order['expressid']) {
			$express = pdo_get('wlmerchant_express', array('id' => $order['expressid']));
		}

		include wl_template('finace/newcashorder');
	}

	public function payonlinelist()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$store_where = is_agent() ? array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']) : array('uniacid' => $_W['uniacid']);
		$stores = pdo_getall('wlmerchant_merchantdata', $store_where, array('id', 'storename'));
		$where = array();
		$where['uniacid'] = $_W['uniacid'];
		$where['plugin'] = 'halfcard';
		$where['status>'] = 1;

		if (is_agent()) {
			$where['aid'] = $_W['aid'];
		}

		$sid = intval($_GPC['sid']);

		if ($sid) {
			$where['sid'] = $sid;
		}

		if ($_GPC['paytype']) {
			$where['paytype'] = $_GPC['paytype'];
		}

		if ($_GPC['keyword']) {
			$keyword = $_GPC['keyword'];

			if ($_GPC['keywordtype'] == 1) {
				$params[':name'] = '%' . $keyword . '%';
				$members = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_member') . (' WHERE uniacid = ' . $_W['uniacid'] . '  AND nickname LIKE :name'), $params);

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
			}
			else {
				if ($_GPC['keywordtype'] == 2) {
					$params[':name'] = '%' . $keyword . '%';
					$members = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_member') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND mobile LIKE :name'), $params);

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
				}
			}
		}

		if ($_GPC['time_limit']) {
			$time_limit = $_GPC['time_limit'];
			$starttime = strtotime($_GPC['time_limit']['start']);
			$endtime = strtotime($_GPC['time_limit']['end']);
			$where['paytime>'] = $starttime;
			$where['paytime<'] = $endtime + 86400;
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if ($_GPC['export']) {
			$this->exportpayonline($where);
		}

		$payonlinelist = Util::getNumData('*', 'wlmerchant_order', $where, 'paytime DESC', $pindex, $psize, 1);
		$pager = $payonlinelist[1];
		$list = $payonlinelist[0];

		foreach ($list as $key => &$li) {
			$member = pdo_get('wlmerchant_member', array('id' => $li['mid']), array('avatar', 'nickname'));
			$li['avatar'] = tomedia($member['avatar']);
			$li['nickname'] = $member['nickname'];
			$store = pdo_get(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $li['sid']), array('logo', 'storename'));
			$li['logo'] = tomedia($store['logo']);

			if ($li['fkid']) {
				$li['title'] = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('uniacid' => $_W['uniacid'], 'id' => $li['fkid']), 'title');
			}
			else {
				$li['title'] = $store['storename'] . '买家在线买单';
			}

			$li['paytime'] = date('Y-m-d H:i:s', $li['paytime']);
		}

		include wl_template('order/payonlinelist');
	}

	public function exportpayonline($where)
	{
		global $_W;
		global $_GPC;
		$payonlinelist = Util::getNumData('*', 'wlmerchant_order', $where, 'paytime DESC', $pindex, $psize, 1);
		$pager = $payonlinelist[1];
		$list = $payonlinelist[0];

		foreach ($list as $key => &$li) {
			$member = pdo_get('wlmerchant_member', array('id' => $li['mid']), array('mobile', 'nickname'));
			$li['mobile'] = $member['mobile'];
			$li['nickname'] = $member['nickname'];
			$store = pdo_get(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $li['sid']), array('logo', 'storename'));

			if ($li['fkid']) {
				$li['title'] = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('uniacid' => $_W['uniacid'], 'id' => $li['fkid']), 'title');
			}
			else {
				$li['title'] = $store['storename'] . '买家在线买单';
			}

			$li['paytime'] = date('Y-m-d H:i:s', $li['paytime']);
		}

		$filter = array('orderno' => '订单号', 'title' => '所属商家', 'nickname' => '买家昵称', 'mobile' => '买家电话', 'goodsprice' => '订单金额', 'oprice' => '不可优惠金额', 'spec' => '优惠折扣', 'card_fee' => '已优惠金额', 'price' => '实付金额', 'paytype' => '支付方式', 'paytime' => '支付时间');
		$data = array();
		$i = 0;

		while ($i < count($list)) {
			foreach ($filter as $key => $title) {
				if ($key == 'paytype') {
					switch ($list[$i][$key]) {
					case '1':
						$data[$i][$key] = '余额支付';
						break;

					case '2':
						$data[$i][$key] = '微信支付';
						break;

					case '3':
						$data[$i][$key] = '支付宝支付';
						break;

					case '4':
						$data[$i][$key] = '货到付款';
						break;

					default:
						$data[$i][$key] = '其他或未支付';
						break;
					}
				}
				else if ($key == 'spec') {
					if ($list[$i][$key] < 10) {
						$data[$i][$key] = $list[$i][$key] . '折';
					}
					else {
						$data[$i][$key] = '无折扣';
					}
				}
				else {
					$data[$i][$key] = $list[$i][$key];
				}
			}

			++$i;
		}

		util_csv::export_csv_2($data, $filter, '在线买单记录表.csv');
		exit();
	}

	public function freightlist()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$wheres = array();
		$wheres['uniacid'] = $_W['uniacid'];

		if (is_agent()) {
			$wheres['aid'] = $_W['aid'];
		}

		$freightlist = Store::getNumExpress('*', $wheres, 'ID DESC', $pindex, $psize, 1);
		$pager = $freightlist[1];
		$list = $freightlist[0];
		include wl_template('order/freightlist');
	}

	public function creatfreight()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];

		if (!is_agent()) {
			$agents = pdo_getall('wlmerchant_agentusers', array('uniacid' => $_W['uniacid']), array('id', 'agentname'));
		}

		if ($id) {
			$info = pdo_get('wlmerchant_express_template', array('id' => $id));
			$info['expressarray'] = unserialize($info['expressarray']);
		}

		if (checksubmit('submit')) {
			$data['name'] = htmlspecialchars($_GPC['expressname']);
			$data['defaultnum'] = intval($_GPC['defaultnum']);
			$data['defaultmoney'] = sprintf('%.2f', $_GPC['defaultmoney']);
			$data['defaultnumex'] = intval($_GPC['defaultnumex']);
			$data['defaultmoneyex'] = sprintf('%.2f', $_GPC['defaultmoneyex']);
			if (!empty($_GPC['express']['area']) && is_array($_GPC['express']['area'])) {
				foreach ($_GPC['express']['area'] as $k => $v) {
					$expressarray[] = array('area' => $v, 'num' => intval($_GPC['express']['num'][$k]), 'money' => sprintf('%.2f', $_GPC['express']['money'][$k]), 'numex' => intval($_GPC['express']['numex'][$k]), 'moneyex' => sprintf('%.2f', $_GPC['express']['moneyex'][$k]));
				}
			}

			$data['expressarray'] = serialize($expressarray);
			$data['createtime'] = time();

			if ($_GPC['aid']) {
				$data['aid'] = $_GPC['aid'];
			}
			else {
				$data['aid'] = $_W['aid'];
			}

			if ($id) {
				$res = Store::updateExpress($data, $id);

				if ($res) {
					wl_message('更新运费模板成功', web_url('order/wlOrder/freightlist'), 'success');
				}
				else {
					wl_message('更新运费模板失败', referer(), 'error');
				}
			}
			else {
				$res = Store::saveExpress($data);

				if ($res) {
					wl_message('创建运费模板成功', web_url('order/wlOrder/freightlist'), 'success');
				}
				else {
					wl_message('创建运费模板失败', referer(), 'error');
				}
			}
		}

		include wl_template('order/creatfreight');
	}

	public function deleteExpress()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$res = Store::deteleExpress($id);

		if ($res) {
			exit(json_encode(array('errno' => 0, 'message' => $res, 'id' => $id)));
		}
		else {
			exit(json_encode(array('errno' => 2, 'message' => $res, 'id' => $id)));
		}
	}

	public function delerefund()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($type == 'a') {
			$res = pdo_update('wlmerchant_order', array('applyrefund' => 0), array('id' => $id));
		}
		else {
			$res = pdo_update('wlmerchant_rush_order', array('applyrefund' => 0), array('id' => $id));
		}

		if ($res) {
			show_json(1);
		}
		else {
			show_json(0, '驳回失败，请重试');
		}
	}

	public function send()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];
		$settings = Setting::wlsetting_read('orderset');
		$edit_flag = $_GPC['edit_flag'];

		if ($type == 'a') {
			$expressid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'expressid');
		}
		else if ($type == 'consumption') {
			$expressid = pdo_getcolumn(PDO_NAME . 'consumption_record', array('id' => $id), 'expressid');
		}
		else {
			$expressid = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $id), 'expressid');
		}

		if (empty($expressid)) {
			show_json(0, '无收货地址，无法发货！');
		}

		$express = pdo_get(PDO_NAME . 'express', array('id' => $expressid));

		if ($_W['ispost']) {
			if (empty($_GPC['expresssn'])) {
				show_json(0, '请输入快递单号！');
			}

			$expressname = $_GPC['express'];
			$expresssn = $_GPC['expresssn'];
			$res = pdo_update('wlmerchant_express', array('expressname' => $expressname, 'expresssn' => $expresssn, 'orderid' => $id, 'sendtime' => time()), array('id' => $expressid));

			if ($res) {
				if ($type == 'a') {
					$res = pdo_update('wlmerchant_order', array('status' => 4), array('id' => $id));

					if (0 < $settings['receipt']) {
						if ($edit_flag) {
							pdo_delete('wlmerchant_waittask', array('important' => $id, 'key' => 6, 'status' => 0));
						}

						$receipttime = time() + $settings['receipt'] * 24 * 3600;
						$task = array('type' => 'order', 'orderid' => $id);
						$task = serialize($task);
						Queue::addTask(6, $task, $receipttime, $id);
					}

					Message::sendremind($id, 'a');
				}
				else if ($type == 'consumption') {
					$res = pdo_update('wlmerchant_consumption_record', array('status' => 2), array('id' => $id));

					if (0 < $settings['receipt']) {
						if ($edit_flag) {
							pdo_delete('wlmerchant_waittask', array('important' => $id, 'key' => 6, 'status' => 0));
						}

						$receipttime = time() + $settings['receipt'] * 24 * 3600;
						$task = array('type' => 'consumption', 'orderid' => $id);
						$task = serialize($task);
						Queue::addTask(6, $task, $receipttime, $id);
					}
				}
				else {
					$res = pdo_update('wlmerchant_rush_order', array('status' => 4), array('id' => $id));

					if (0 < $settings['receipt']) {
						if ($edit_flag) {
							pdo_delete('wlmerchant_waittask', array('important' => $id, 'key' => 6, 'status' => 0));
						}

						$receipttime = time() + $settings['receipt'] * 24 * 3600;
						$task = array('type' => 'rush', 'orderid' => $id);
						$task = serialize($task);
						Queue::addTask(6, $task, $receipttime, $id);
					}

					Message::sendremind($id, 'b');
				}

				show_json(1);
			}
			else {
				show_json(0, '发货失败请重试');
			}
		}

		$express_list = array(
			array(name => '顺丰', express => 'shunfeng'),
			array(name => '申通', express => 'shentong'),
			array(name => '韵达快运', express => 'yunda'),
			array(name => '天天快递', express => 'tiantian'),
			array(name => '圆通速递', express => 'yuantong'),
			array(name => '中通速递', express => 'zhongtong'),
			array(name => 'ems快递', express => 'ems'),
			array(name => '汇通快运', express => 'huitongkuaidi'),
			array(name => '全峰快递', express => 'quanfengkuaidi'),
			array(name => '宅急送', express => 'zhaijisong'),
			array(name => 'aae全球专递', express => 'aae'),
			array(name => '安捷快递', express => 'anjie'),
			array(name => '安信达快递', express => 'anxindakuaixi'),
			array(name => '彪记快递', express => 'biaojikuaidi'),
			array(name => 'bht', express => 'bht'),
			array(name => '百福东方国际物流', express => 'baifudongfang'),
			array(name => '中国东方（COE）', express => 'coe'),
			array(name => '长宇物流', express => 'changyuwuliu'),
			array(name => '大田物流', express => 'datianwuliu'),
			array(name => '德邦物流', express => 'debangwuliu'),
			array(name => 'dhl', express => 'dhl'),
			array(name => 'dpex', express => 'dpex'),
			array(name => 'd速快递', express => 'dsukuaidi'),
			array(name => '递四方', express => 'disifang'),
			array(name => 'fedex（国外）', express => 'fedex'),
			array(name => '飞康达物流', express => 'feikangda'),
			array(name => '凤凰快递', express => 'fenghuangkuaidi'),
			array(name => '飞快达', express => 'feikuaida'),
			array(name => '国通快递', express => 'guotongkuaidi'),
			array(name => '港中能达物流', express => 'ganzhongnengda'),
			array(name => '广东邮政物流', express => 'guangdongyouzhengwuliu'),
			array(name => '共速达', express => 'gongsuda'),
			array(name => '恒路物流', express => 'hengluwuliu'),
			array(name => '华夏龙物流', express => 'huaxialongwuliu'),
			array(name => '海红', express => 'haihongwangsong'),
			array(name => '海外环球', express => 'haiwaihuanqiu'),
			array(name => '佳怡物流', express => 'jiayiwuliu'),
			array(name => '京广速递', express => 'jinguangsudikuaijian'),
			array(name => '急先达', express => 'jixianda'),
			array(name => '佳吉物流', express => 'jjwl'),
			array(name => '加运美物流', express => 'jymwl'),
			array(name => '金大物流', express => 'jindawuliu'),
			array(name => '嘉里大通', express => 'jialidatong'),
			array(name => '晋越快递', express => 'jykd'),
			array(name => '快捷速递', express => 'kuaijiesudi'),
			array(name => '联邦快递（国内）', express => 'lianb'),
			array(name => '联昊通物流', express => 'lianhaowuliu'),
			array(name => '龙邦物流', express => 'longbanwuliu'),
			array(name => '立即送', express => 'lijisong'),
			array(name => '乐捷递', express => 'lejiedi'),
			array(name => '民航快递', express => 'minghangkuaidi'),
			array(name => '美国快递', express => 'meiguokuaidi'),
			array(name => '门对门', express => 'menduimen'),
			array(name => 'OCS', express => 'ocs'),
			array(name => '配思货运', express => 'peisihuoyunkuaidi'),
			array(name => '全晨快递', express => 'quanchenkuaidi'),
			array(name => '全际通物流', express => 'quanjitong'),
			array(name => '全日通快递', express => 'quanritongkuaidi'),
			array(name => '全一快递', express => 'quanyikuaidi'),
			array(name => '如风达', express => 'rufengda'),
			array(name => '三态速递', express => 'santaisudi'),
			array(name => '盛辉物流', express => 'shenghuiwuliu'),
			array(name => '速尔物流', express => 'sue'),
			array(name => '盛丰物流', express => 'shengfeng'),
			array(name => '赛澳递', express => 'saiaodi'),
			array(name => '天地华宇', express => 'tiandihuayu'),
			array(name => 'tnt', express => 'tnt'),
			array(name => 'ups', express => 'ups'),
			array(name => '万家物流', express => 'wanjiawuliu'),
			array(name => '文捷航空速递', express => 'wenjiesudi'),
			array(name => '伍圆', express => 'wuyuan'),
			array(name => '万象物流', express => 'wxwl'),
			array(name => '新邦物流', express => 'xinbangwuliu'),
			array(name => '信丰物流', express => 'xinfengwuliu'),
			array(name => '亚风速递', express => 'yafengsudi'),
			array(name => '一邦速递', express => 'yibangwuliu'),
			array(name => '优速物流', express => 'youshuwuliu'),
			array(name => '邮政包裹挂号信', express => 'youzhengguonei'),
			array(name => '邮政国际包裹挂号信', express => 'youzhengguoji'),
			array(name => '远成物流', express => 'yuanchengwuliu'),
			array(name => '源伟丰快递', express => 'yuanweifeng'),
			array(name => '元智捷诚快递', express => 'yuanzhijiecheng'),
			array(name => '运通快递', express => 'yuntongkuaidi'),
			array(name => '越丰物流', express => 'yuefengwuliu'),
			array(name => '源安达', express => 'yad'),
			array(name => '银捷速递', express => 'yinjiesudi'),
			array(name => '中铁快运', express => 'zhongtiekuaiyun'),
			array(name => '中邮物流', express => 'zhongyouwuliu'),
			array(name => '忠信达', express => 'zhongxinda'),
			array(name => '芝麻开门', express => 'zhimakaimen')
		);
		include wl_template('order/send');
	}

	public function changeprice()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($type == 'a') {
			$order = pdo_get('wlmerchant_order', array('id' => $id), array('expressid', 'price', 'originalprice', 'changeprice', 'changedispatchprice'));

			if (0 < $order['originalprice']) {
				$price = $order['originalprice'];
			}
			else {
				$price = $order['price'];
			}
		}
		else {
			$order = pdo_get('wlmerchant_rush_order', array('id' => $id), array('expressid', 'price', 'actualprice', 'originalprice', 'changeprice', 'changedispatchprice'));

			if (0 < $order['originalprice']) {
				$price = $order['originalprice'];
			}
			else {
				$price = $order['price'];
			}
		}

		if ($_W['ispost']) {
			$price_type = $_GPC['price_type'];
			$price_value = trim($_GPC['price_value']);
			$price_value = sprintf('%.2f', $price_value);
			$express_type = $_GPC['express_type'];
			$express_value = $_GPC['express_value'];
			$express_value = sprintf('%.2f', $express_value);

			if ($price_type == 2) {
				$price_value = 0 - $price_value;
			}

			if ($express_type == 2) {
				$express_value = 0 - $express_value;
			}

			$newprice = $price + $price_value + $express_value;
			if ($newprice < 0 || $newprice == 0) {
				show_json(0, '改价失败，改价后订单金额不能小于或等于0');
			}

			if ($type == 'a') {
				$res = pdo_update('wlmerchant_order', array('price' => $newprice, 'changeprice' => $price_value, 'changedispatchprice' => $express_value, 'originalprice' => $price, 'orderno' => createUniontid()), array('id' => $id));
			}
			else {
				$res = pdo_update('wlmerchant_rush_order', array('actualprice' => $newprice, 'changeprice' => $price_value, 'changedispatchprice' => $express_value, 'originalprice' => $price, 'orderno' => createUniontid()), array('id' => $id));
			}

			if ($res) {
				show_json(1);
			}
			else {
				show_json(0, '改价失败,请重试');
			}
		}

		include wl_template('order/changeprice');
	}

	public function changecommission()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$order = pdo_get('wlmerchant_disorder', array('id' => $id));
		$leadmoney = unserialize($order['leadmoney']);
		$order['onemember'] = pdo_getcolumn(PDO_NAME . 'distributor', array('id' => $order['oneleadid']), 'nickname');

		if ($order['twoleadid']) {
			$order['twomember'] = pdo_getcolumn(PDO_NAME . 'distributor', array('id' => $order['twoleadid']), 'nickname');
		}

		if ($_W['ispost']) {
			$onemoney = $_GPC['onemoney'];
			$twomoney = $_GPC['twomoney'];
			if ($onemoney < 0 || $twomoney < 0) {
				show_json(0, '分销佣金不能为0,请重试');
			}

			if ($order['neworderflag']) {
				$num = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE disorderid = ' . $id));
				$newone = sprintf('%.2f', $onemoney / $num);
				$newtow = sprintf('%.2f', $twomoney / $num);
				pdo_update('wlmerchant_smallorder', array('onedismoney' => $newone, 'twodismoney' => $newtow), array('disorderid' => $id, 'status' => 1));
			}

			$newleadmoney['one'] = $onemoney;
			$newleadmoney['two'] = $twomoney;
			$newleadmoney = serialize($newleadmoney);
			$res = pdo_update('wlmerchant_disorder', array('leadmoney' => $newleadmoney), array('id' => $id));

			if ($res) {
				show_json(1);
			}
			else {
				show_json(0, '修改失败,请重试');
			}
		}

		include wl_template('order/changecommission');
	}

	public function changetime()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($type == 'a') {
			$order = pdo_get('wlmerchant_order', array('id' => $id), array('id', 'aid', 'sid', 'plugin', 'estimatetime', 'recordid', 'fkid', 'issettlement', 'orderno'));

			if ($order['plugin'] == 'wlfightgroup') {
				$plugin = 2;
			}

			if ($order['plugin'] == 'coupon') {
				$plugin = 3;
			}

			if ($order['plugin'] == 'groupon') {
				$plugin = 10;
			}
		}
		else {
			$order = pdo_get('wlmerchant_rush_order', array('id' => $id), array('id', 'aid', 'sid', 'estimatetime', 'activityid', 'issettlement', 'orderno'));
			$plugin = 1;
		}

		if ($_W['ispost']) {
			$time = strtotime($_GPC['estimatetime']);

			if ($type == 'a') {
				if ($_GPC['classtype']) {
					$orders = pdo_getall('wlmerchant_order', array('fkid' => $order['fkid'], 'plugin' => $order['plugin'], 'issettlement' => 1, 'status' => 9), array('orderno', 'id', 'aid', 'sid', 'issettlement', 'plugin'));

					foreach ($orders as $key => $aor) {
						$settlement = pdo_get('wlmerchant_autosettlement_record', array('orderid' => $aor['id'], 'type' => $plugin));

						if (0 < $settlement['agentmoney']) {
							pdo_fetch('update' . tablename('wlmerchant_agentusers') . ('SET allmoney=allmoney-' . $settlement['agentmoney'] . ',nowmoney=nowmoney-' . $settlement['agentmoney'] . ' WHERE id = ' . $aor['aid']));
							$changeagentnowmoney = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $aor['aid']), 'nowmoney');
							Store::addcurrent(2, -1, $aor['aid'], 0 - $settlement['agentmoney'], $changeagentnowmoney, '', '后台修改[' . $aor['orderno'] . ']订单时限扣除已结算金额');
						}

						if (0 < $settlement['merchantmoney']) {
							pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET allmoney=allmoney-' . $settlement['merchantmoney'] . ',nowmoney=nowmoney-' . $settlement['merchantmoney'] . ' WHERE id = ' . $aor['sid']));
							$changemerchantnowmoney = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $aor['sid']), 'nowmoney');
							Store::addcurrent(1, -1, $aor['sid'], 0 - $settlement['merchantmoney'], $changemerchantnowmoney, '', '后台修改[' . $aor['orderno'] . ']订单时限扣除已结算金额');
						}

						pdo_delete('wlmerchant_autosettlement_record', array('id' => $settlement['id']));
					}

					$res2 = pdo_update('wlmerchant_order', array('estimatetime' => $time), array('fkid' => $order['fkid'], 'plugin' => $order['plugin'], 'status' => 1));
					$res1 = pdo_update('wlmerchant_order', array('estimatetime' => $time, 'status' => 1, 'issettlement' => 0), array('fkid' => $order['fkid'], 'plugin' => $order['plugin'], 'status' => 9));
					if ($order['plugin'] == 'coupon' && ($res1 || $res2)) {
						pdo_update('wlmerchant_member_coupons', array('endtime' => $time), array('parentid' => $order['fkid'], 'status' => 1));
						pdo_update('wlmerchant_member_coupons', array('endtime' => $time, 'status' => 1), array('parentid' => $order['fkid'], 'status' => 3));
					}
				}
				else {
					if ($order['issettlement']) {
						$settlement = pdo_get('wlmerchant_autosettlement_record', array('orderid' => $order['id'], 'type' => $plugin));

						if (0 < $settlement['agentmoney']) {
							pdo_fetch('update' . tablename('wlmerchant_agentusers') . ('SET allmoney=allmoney-' . $settlement['agentmoney'] . ',nowmoney=nowmoney-' . $settlement['agentmoney'] . ' WHERE id = ' . $order['aid']));
							$changeagentnowmoney = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $order['aid']), 'nowmoney');
							Store::addcurrent(2, -1, $order['aid'], 0 - $settlement['agentmoney'], $changeagentnowmoney, '', '后台修改[' . $order['orderno'] . ']订单时限扣除已结算金额');
						}

						if (0 < $settlement['merchantmoney']) {
							pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET allmoney=allmoney-' . $settlement['merchantmoney'] . ',nowmoney=nowmoney-' . $settlement['merchantmoney'] . ' WHERE id = ' . $order['sid']));
							$changemerchantnowmoney = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $order['sid']), 'nowmoney');
							Store::addcurrent(1, -1, $order['sid'], 0 - $settlement['merchantmoney'], $changemerchantnowmoney, '', '后台修改[' . $order['orderno'] . ']订单时限扣除已结算金额');
						}

						pdo_delete('wlmerchant_autosettlement_record', array('id' => $settlement['id']));
					}

					$res1 = pdo_update('wlmerchant_order', array('estimatetime' => $time, 'status' => 1, 'issettlement' => 0), array('id' => $id));
					if ($order['plugin'] == 'coupon' && $res1) {
						pdo_update('wlmerchant_member_coupons', array('endtime' => $time, 'status' => 1), array('id' => $order['recordid']));
					}
				}
			}
			else if ($_GPC['classtype']) {
				$orders = pdo_getall('wlmerchant_rush_order', array('activityid' => $order['activityid'], 'issettlement' => 1, 'status' => 9), array('id', 'aid', 'sid', 'issettlement', 'orderno'));

				foreach ($orders as $key => $aor) {
					$settlement = pdo_get('wlmerchant_autosettlement_record', array('orderid' => $aor['id'], 'type' => $plugin));

					if (0 < $settlement['agentmoney']) {
						pdo_fetch('update' . tablename('wlmerchant_agentusers') . ('SET allmoney=allmoney-' . $settlement['agentmoney'] . ',nowmoney=nowmoney-' . $settlement['agentmoney'] . ' WHERE id = ' . $aor['aid']));
						$changeagentnowmoney = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $aor['aid']), 'nowmoney');
						Store::addcurrent(2, -1, $aor['aid'], 0 - $settlement['agentmoney'], $changeagentnowmoney, '', '后台修改[' . $aor['orderno'] . ']订单时限扣除已结算金额');
					}

					if (0 < $settlement['merchantmoney']) {
						pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET allmoney=allmoney-' . $settlement['merchantmoney'] . ',nowmoney=nowmoney-' . $settlement['merchantmoney'] . ' WHERE id = ' . $aor['sid']));
						$changemerchantnowmoney = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $aor['sid']), 'nowmoney');
						Store::addcurrent(1, -1, $aor['sid'], 0 - $settlement['merchantmoney'], $changemerchantnowmoney, '', '后台修改[' . $aor['orderno'] . ']订单时限扣除已结算金额');
					}

					pdo_delete('wlmerchant_autosettlement_record', array('id' => $settlement['id']));
				}

				$res2 = pdo_update('wlmerchant_rush_order', array('estimatetime' => $time), array('activityid' => $order['activityid'], 'status' => 1));
				$res1 = pdo_update('wlmerchant_rush_order', array('estimatetime' => $time, 'status' => 1, 'issettlement' => 0), array('activityid' => $order['activityid'], 'status' => 9));
			}
			else {
				if ($order['issettlement']) {
					$settlement = pdo_get('wlmerchant_autosettlement_record', array('orderid' => $order['id'], 'type' => $plugin));

					if (0 < $settlement['agentmoney']) {
						pdo_fetch('update' . tablename('wlmerchant_agentusers') . ('SET allmoney=allmoney-' . $settlement['agentmoney'] . ',nowmoney=nowmoney-' . $settlement['agentmoney'] . ' WHERE id = ' . $order['aid']));
						$changeagentnowmoney = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $order['aid']), 'nowmoney');
						Store::addcurrent(2, -1, $order['aid'], 0 - $settlement['agentmoney'], $changeagentnowmoney, '', '后台修改[' . $order['orderno'] . ']订单时限扣除已结算金额');
					}

					if (0 < $settlement['merchantmoney']) {
						pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET allmoney=allmoney-' . $settlement['merchantmoney'] . ',nowmoney=nowmoney-' . $settlement['merchantmoney'] . ' WHERE id = ' . $order['sid']));
						$changemerchantnowmoney = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $order['sid']), 'nowmoney');
						Store::addcurrent(1, -1, $order['sid'], 0 - $settlement['merchantmoney'], $changemerchantnowmoney, '', '后台修改[' . $order['orderno'] . ']订单时限扣除已结算金额');
					}

					pdo_delete('wlmerchant_autosettlement_record', array('id' => $settlement['id']));
				}

				$res1 = pdo_update('wlmerchant_rush_order', array('estimatetime' => $time, 'status' => 1, 'issettlement' => 0), array('id' => $id));
			}

			if ($res1 || $res2) {
				show_json(1);
			}
			else {
				show_json(0, '修改失败,请重试');
			}
		}

		include wl_template('order/changetime');
	}

	public function collect()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($type == 'a') {
			$expressid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'expressid');
		}
		else if ($type == 'consumption') {
			$expressid = pdo_getcolumn(PDO_NAME . 'consumption_record', array('id' => $id), 'expressid');
		}
		else {
			$expressid = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $id), 'expressid');
		}

		if (empty($expressid)) {
			show_json(0, '无收货信息，无法收货！');
		}

		$express = pdo_get(PDO_NAME . 'express', array('id' => $expressid));
		$res = pdo_update('wlmerchant_express', array('receivetime' => time()), array('id' => $expressid));

		if ($res) {
			if ($type == 'a') {
				pdo_update('wlmerchant_order', array('status' => 2), array('id' => $id));
				$plugin = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'plugin');
				$ordertask = array('type' => $plugin, 'orderid' => $id);
				$ordertask = serialize($ordertask);
				Queue::addTask(2, $ordertask, time(), $id);
				$disorderid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'disorderid');
			}
			else if ($type == 'consumption') {
				pdo_update('wlmerchant_consumption_record', array('status' => 3), array('id' => $id));
				$orderid = pdo_getcolumn(PDO_NAME . 'consumption_record', array('id' => $id), 'orderid');
				$plugin = 'consumption';
				$disorderid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $orderid), 'disorderid');
			}
			else {
				pdo_update('wlmerchant_rush_order', array('status' => 2), array('id' => $id));
				$plugin = 'rush';
				$rushtask = array('type' => 'rush', 'orderid' => $id);
				$rushtask = serialize($rushtask);
				Queue::addTask(1, $rushtask, time(), $id);
				$disorderid = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $id), 'disorderid');
			}

			if ($disorderid) {
				$res = pdo_update('wlmerchant_disorder', array('status' => 1), array('id' => $disorderid, 'status' => 0));

				if ($res) {
					$distask = array('type' => $plugin, 'orderid' => $disorderid);
					$distask = serialize($distask);
					Queue::addTask(3, $distask, time(), $disorderid);
				}
			}

			show_json(1);
		}
		else {
			show_json(0, '收货失败请重试');
		}
	}

	public function sendcancel()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];
		$settings = Setting::wlsetting_read('orderset');

		if ($type == 'a') {
			$res = pdo_update('wlmerchant_order', array('status' => 8), array('id' => $id));
			$expressid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'expressid');
		}
		else if ($type == 'consumption') {
			$res = pdo_update('wlmerchant_consumption_record', array('status' => 1), array('id' => $id));
			$expressid = pdo_getcolumn(PDO_NAME . 'consumption_record', array('id' => $id), 'expressid');
		}
		else {
			$res = pdo_update('wlmerchant_rush_order', array('status' => 8), array('id' => $id));
			$expressid = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $id), 'expressid');
		}

		pdo_update('wlmerchant_express', array('expressname' => '', 'expresssn' => '', 'sendtime' => 0), array('id' => $expressid));

		if (0 < $settings['receipt']) {
			pdo_delete('wlmerchant_waittask', array('important' => $id, 'key' => 6, 'status' => 0));
		}

		if ($res) {
			show_json(1);
		}
		else {
			show_json(0, '取消失败请重试');
		}
	}

	public function express()
	{
		global $_W;
		global $_GPC;
		$express = $_GPC['express'];
		$expresssn = $_GPC['expresssn'];
		$express == 'jymwl' ? 'jiayunmeiwuliu' : $express;
		$express == 'TTKD' ? 'tiantian' : $express;
		$express == 'jjwl' ? 'jiajiwuliu' : $express;
		$express == 'zhongtiekuaiyun' ? 'ztky' : $express;
		$url = 'https://www.kuaidi100.com/chaxun?com=' . $express . '&nu=' . $expresssn;
		$response = ihttp_request($url);
		$content = $response['content'];
		$info = json_decode($content, true);
		$list = array();
		if (!empty($info['data']) && is_array($info['data'])) {
			foreach ($info['data'] as $index => $data) {
				$list[] = array('time' => trim($data['time']), 'step' => trim($data['context']));
			}
		}

		include wl_template('order/express');
	}

	public function hexiaorecord()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($type == 'a') {
			$order = pdo_get('wlmerchant_order', array('id' => $id), array('plugin', 'recordid', 'specid', 'neworderflag'));

			if ($order['neworderflag']) {
				$usetimes = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'' . $order['plugin'] . '\' AND  orderid = ' . $id . ' AND status = 1'));
				$smallorder = pdo_getall('wlmerchant_smallorder', array('orderid' => $id, 'plugin' => $order['plugin']), '', '', 'status ASC,hexiaotime ASC');
			}
			else if ($order['plugin'] == 'wlfightgroup') {
				$record = pdo_get('wlmerchant_fightgroup_userecord', array('id' => $order['recordid']), array('usetimes', 'usedtime'));
				$usetimes = $record['usetimes'];
				$usedtime = unserialize($record['usedtime']);
			}
			else if ($order['plugin'] == 'coupon') {
				$record = pdo_get('wlmerchant_member_coupons', array('id' => $order['recordid']), array('usetimes', 'usedtime'));
				$usetimes = $record['usetimes'];
				$usedtime = unserialize($record['usedtime']);
			}
			else if ($order['plugin'] == 'groupon') {
				$record = pdo_get('wlmerchant_groupon_userecord', array('id' => $order['recordid']), array('usetimes', 'usedtime'));
				$usetimes = $record['usetimes'];
				$usedtime = unserialize($record['usedtime']);
			}
			else {
				if ($order['plugin'] == 'bargain') {
					$record = pdo_get('wlmerchant_bargain_userlist', array('id' => $order['specid']), array('usetimes', 'usedtime'));
					$usetimes = $record['usetimes'];
					$usedtime = unserialize($record['usedtime']);
				}
			}
		}
		else {
			$order = pdo_get('wlmerchant_rush_order', array('id' => $id), array('usetimes', 'usedtime', 'neworderflag'));

			if ($order['neworderflag']) {
				$usetimes = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'rush\' AND  orderid = ' . $id . ' AND status = 1'));
				$smallorder = pdo_getall('wlmerchant_smallorder', array('orderid' => $id, 'plugin' => 'rush'), '', '', 'status ASC,hexiaotime ASC');
			}
			else {
				$usetimes = $order['usetimes'];
				$usedtime = unserialize($order['usedtime']);
			}
		}

		if ($smallorder) {
			$usedtime = array();

			foreach ($smallorder as $key => $sm) {
				$va['status'] = $sm['status'];

				if ($sm['status'] == 2) {
					$va['time'] = date('Y-m-d H:i:s', $sm['hexiaotime']);
				}
				else {
					if ($sm['status'] == 3) {
						$va['time'] = date('Y-m-d H:i:s', $sm['refundtime']);
					}
				}

				if ($sm['hxuid']) {
					$va['ver'] = pdo_getcolumn(PDO_NAME . 'merchantuser', array('id' => $sm['hxuid']), 'name');
				}
				else {
					$va['ver'] = '无';
				}

				switch ($sm['hexiaotype']) {
				case '1':
					$va['type'] = '输码核销';
					break;

				case '2':
					$va['type'] = '扫码核销';
					break;

				case '3':
					$va['type'] = '后台核销';
					break;

				case '4':
					$va['type'] = '密码核销';
					break;

				default:
					$va['type'] = '未知方式';
					break;
				}

				$va['checkcode'] = $sm['checkcode'];
				$usedtime[] = $va;
			}
		}
		else {
			if ($usedtime) {
				foreach ($usedtime as $key => &$va) {
					$va['status'] = 2;
					$va['time'] = date('Y-m-d H:i:s', $va['time']);
					$va['ver'] = pdo_getcolumn(PDO_NAME . 'merchantuser', array('mid' => $va['ver']), 'name');

					if (empty($va['ver'])) {
						$va['ver'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $va['ver']), 'nickname');

						if (empty($va['ver'])) {
							$va['ver'] = '无';
						}
					}

					switch ($va['type']) {
					case '1':
						$va['type'] = '输码核销';
						break;

					case '2':
						$va['type'] = '扫码核销';
						break;

					case '3':
						$va['type'] = '后台核销';
						break;

					case '4':
						$va['type'] = '密码核销';
						break;

					default:
						$va['type'] = '未知方式';
						break;
					}
				}
			}
		}

		include wl_template('order/hexiaorecord');
	}

	public function changeexpress()
	{
		global $_W;
		global $_GPC;
		$expressid = $_GPC['expressid'];
		$express = pdo_get(PDO_NAME . 'express', array('id' => $expressid));

		if ($_W['ispost']) {
			$data['name'] = trim($_GPC['name']);
			$data['tel'] = trim($_GPC['tel']);
			$data['address'] = $_GPC['address'];
			$res = pdo_update('wlmerchant_express', $data, array('id' => $expressid));

			if ($res) {
				show_json(1);
			}
			else {
				show_json(0, '修改失败请重试');
			}
		}

		include wl_template('order/changeexpress');
	}

	public function fetch()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($type == 'a') {
			$order = pdo_get('wlmerchant_order', array('id' => $id), array('neworderflag'));
			$plugin = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'plugin');

			if ($order['neworderflag']) {
				if ($plugin == 'groupon') {
					$num = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'groupon\' AND  orderid = ' . $id . ' AND status = 1'));
					$res = Groupon::hexiaoorder($id, -1, $num, 3);
				}
			}
			else if ($plugin == 'wlfightgroup') {
				$recordid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'recordid');
				$num = pdo_getcolumn(PDO_NAME . 'fightgroup_userecord', array('id' => $recordid), 'usetimes');
				$res = Wlfightgroup::hexiaoorder($id, -1, $num, 3);
			}
			else if ($plugin == 'coupon') {
				$recordid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'recordid');
				$num = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $recordid), 'usetimes');
				$res = wlCoupon::hexiaoorder($recordid, -1, $num, 3);
			}
			else if ($plugin == 'groupon') {
				$recordid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'recordid');
				$num = pdo_getcolumn(PDO_NAME . 'groupon_userecord', array('id' => $recordid), 'usetimes');
				$res = Groupon::hexiaoorder($id, -1, $num, 3);
			}
			else {
				if ($plugin == 'bargain') {
					$usetimes = pdo_getcolumn(PDO_NAME . 'bargain_userlist', array('orderid' => $id), 'usetimes');
					$res = Bargain::hexiaoorder($id, -1, $usetimes, 3);
				}
			}
		}
		else {
			$item = Rush::getSingleOrder($id, '*');

			if ($item['neworderflag']) {
				$item['usetimes'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'rush\' AND  orderid = ' . $id . ' AND status = 1'));
			}

			$res = Rush::hexiaoorder($id, -1, $item['usetimes'], 3);
		}

		if ($res) {
			show_json(1);
		}
		else {
			show_json(0, '使用失败，请刷新重试');
		}
	}

	public function finish()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($type == 'a') {
			$res = pdo_update('wlmerchant_order', array('status' => 3), array('id' => $id));
		}
		else {
			$res = pdo_update('wlmerchant_rush_order', array('status' => 3), array('id' => $id));
		}

		if ($res) {
			show_json(1);
		}
		else {
			show_json(0, '完成失败，请刷新重试');
		}
	}

	public function refund()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($_W['ispost']) {
			$unline = $_GPC['refund_type'];
			$retype = $_GPC['price_type'];

			if ($retype) {
				$money = sprintf('%.2f', $_GPC['price_value']);

				if ($money < 0.01) {
					show_json(0, '退款金额不能为0');
				}
			}
			else {
				$money = 0;
			}

			if ($type == 'a') {
				$plugin = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'plugin');

				if ($plugin == 'wlfightgroup') {
					$res = Wlfightgroup::refund($id, $money, $unline);
				}
				else if ($plugin == 'coupon') {
					$res = wlCoupon::refund($id, $money, $unline);
				}
				else if ($plugin == 'groupon') {
					$res = Groupon::refund($id, $money, $unline);
				}
				else {
					if ($plugin == 'bargain') {
						$res = Bargain::refund($id, $money, $unline);
					}
				}
			}
			else {
				$res = Rush::refund($id, $money, $unline);
			}

			if ($res['status']) {
				show_json(1);
			}
			else {
				show_json(0, '退款失败：' . $res['message']);
			}
		}

		include wl_template('order/refund');
	}

	public function close()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($type == 'a') {
			$res = pdo_update('wlmerchant_order', array('status' => 5), array('id' => $id));

			if ($res) {
				$order = pdo_get('wlmerchant_order', array('id' => $id), array('num', 'specid'));

				if ($order['specid']) {
					pdo_fetch('update' . tablename('wlmerchant_goods_option') . ('SET stock=stock+' . $order['num'] . ' WHERE id = ' . $order['specid']));
				}
			}
		}
		else {
			$res = pdo_update('wlmerchant_rush_order', array('status' => 5), array('id' => $id));

			if ($res) {
				$order = pdo_get('wlmerchant_rush_order', array('id' => $id), array('num', 'optionid'));

				if ($order['optionid']) {
					pdo_fetch('update' . tablename('wlmerchant_goods_option') . ('SET stock=stock+' . $order['num'] . ' WHERE id = ' . $order['optionid']));
				}
			}
		}

		if ($res) {
			show_json(1);
		}
		else {
			show_json(0, '完成失败，请刷新重试');
		}
	}

	public function remarksaler()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($type == 'a') {
			$remark = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'remark');
		}
		else {
			$remark = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $id), 'adminremark');
		}

		if ($_W['ispost']) {
			$newremark = trim($_GPC['remark']);

			if ($type == 'a') {
				$res = pdo_update('wlmerchant_order', array('remark' => $newremark), array('id' => $id));
			}
			else {
				$res = pdo_update('wlmerchant_rush_order', array('adminremark' => $newremark), array('id' => $id));
			}

			if ($res) {
				show_json(1);
			}
			else {
				show_json(0, '备注失败，请刷新重试');
			}
		}

		include wl_template('order/remarksaler');
	}

	public function createdisorder()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($type == 'a') {
			$order = pdo_get('wlmerchant_order', array('id' => $id), array('fkid', 'mid', 'expressid', 'price', 'plugin', 'specid', 'num', 'vipbuyflag'));

			if ($order['plugin'] == 'groupon') {
				$plugin = 'groupon';
				$goods = pdo_get('wlmerchant_groupon_activity', array('id' => $order['fkid']), array('dissettime', 'onedismoney', 'twodismoney', 'viponedismoney', 'viptwodismoney'));

				if ($order['specid']) {
					$option = pdo_get('wlmerchant_goods_option', array('id' => $order['specid']), array('onedismoney', 'twodismoney', 'threedismoney'));
					$onemoney = sprintf('%.2f', $option['onedismoney'] * $order['num']);
					$twomoney = sprintf('%.2f', $option['twodismoney'] * $order['num']);
				}
				else if ($order['vipbuyflag']) {
					$onemoney = sprintf('%.2f', $goods['viponedismoney'] * $order['num']);
					$twomoney = sprintf('%.2f', $goods['viptwodismoney'] * $order['num']);
				}
				else {
					$onemoney = sprintf('%.2f', $goods['onedismoney'] * $order['num']);
					$twomoney = sprintf('%.2f', $goods['twodismoney'] * $order['num']);
				}
			}
			else if ($order['plugin'] == 'wlfightgroup') {
				$plugin = 'fightgroup';
				$goods = pdo_get('wlmerchant_fightgroup_goods', array('id' => $order['fkid']), array('dissettime', 'onedismoney', 'twodismoney'));
				$onemoney = sprintf('%.2f', $goods['onedismoney'] * $order['num']);
				$twomoney = sprintf('%.2f', $goods['twodismoney'] * $order['num']);
			}
			else if ($order['plugin'] == 'coupon') {
				$plugin = 'coupon';
				$goods = pdo_get('wlmerchant_couponlist', array('id' => $order['fkid']), array('dissettime', 'onedismoney', 'twodismoney', 'viponedismoney', 'viptwodismoney'));

				if ($order['vipbuyflag']) {
					$onemoney = sprintf('%.2f', $goods['viponedismoney'] * $order['num']);
					$twomoney = sprintf('%.2f', $goods['viptwodismoney'] * $order['num']);
				}
				else {
					$onemoney = sprintf('%.2f', $goods['onedismoney'] * $order['num']);
					$twomoney = sprintf('%.2f', $goods['twodismoney'] * $order['num']);
				}
			}
			else {
				if ($order['plugin'] == 'bargain') {
					$plugin = 'bargain';
					$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $order['fkid']), array('dissettime', 'onedismoney', 'twodismoney', 'viponedismoney', 'viptwodismoney'));

					if ($order['vipbuyflag']) {
						$onemoney = sprintf('%.2f', $goods['viponedismoney'] * $order['num']);
						$twomoney = sprintf('%.2f', $goods['viptwodismoney'] * $order['num']);
					}
					else {
						$onemoney = sprintf('%.2f', $goods['onedismoney'] * $order['num']);
						$twomoney = sprintf('%.2f', $goods['twodismoney'] * $order['num']);
					}
				}
			}
		}
		else {
			$order = pdo_get('wlmerchant_rush_order', array('id' => $id), array('activityid', 'mid', 'expressid', 'optionid', 'actualprice', 'num', 'vipbuyflag'));
			$order['price'] = $order['actualprice'];
			$plugin = 'rush';
			$goods = pdo_get('wlmerchant_rush_activity', array('id' => $order['activityid']), array('dissettime', 'onedismoney', 'twodismoney', 'viponedismoney', 'viptwodismoney'));

			if ($order['optionid']) {
				$option = pdo_get('wlmerchant_goods_option', array('id' => $order['optionid']), array('onedismoney', 'twodismoney', 'threedismoney'));
				$onemoney = sprintf('%.2f', $option['onedismoney'] * $order['num']);
				$twomoney = sprintf('%.2f', $option['twodismoney'] * $order['num']);
			}
			else if ($order['vipbuyflag']) {
				$onemoney = sprintf('%.2f', $goods['viponedismoney'] * $order['num']);
				$twomoney = sprintf('%.2f', $goods['viptwodismoney'] * $order['num']);
			}
			else {
				$onemoney = sprintf('%.2f', $goods['onedismoney'] * $order['num']);
				$twomoney = sprintf('%.2f', $goods['twodismoney'] * $order['num']);
			}
		}

		$distributorid = pdo_getcolumn(PDO_NAME . 'member', array('uniacid' => $_W['uniacid'], 'id' => $order['mid']), 'distributorid');

		if (empty($distributorid)) {
			show_json(0, '该用户无分销上级，无法生成分销订单');
		}
		else {
			$distributor = pdo_get(PDO_NAME . 'distributor', array('uniacid' => $_W['uniacid'], 'id' => $distributorid), array('leadid', 'disflag', 'dislevel'));

			if ($distributor['disflag']) {
				$mleveid = $distributor['dislevel'];

				if (empty($mleveid)) {
					$mleveid = pdo_getcolumn('wlmerchant_dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'id');
				}

				$memberlevel = pdo_get(PDO_NAME . 'dislevel', array('id' => $mleveid), array('ownstatus'));
			}

			if ($distributor['leadid'] < 0 && empty($memberlevel['ownstatus'])) {
				show_json(0, '该用户无分销上级，无法生成分销订单');
			}
		}

		if ($order['expressid']) {
			$expprice = pdo_getcolumn(PDO_NAME . 'express', array('uniacid' => $_W['uniacid'], 'id' => $order['expressid']), 'expressprice');
			$price = sprintf('%.2f', $order['price'] - $expprice);
		}
		else {
			$price = $order['price'];
		}

		if (empty($plugin)) {
			show_json(0, '订单插件错误，请联系管理员');
		}

		$disorderid = Distribution::disCore($order['mid'], $price, $onemoney, $twomoney, 0, $id, $plugin, $goods['dissettime']);

		if ($disorderid) {
			if ($type == 'a') {
				$res = pdo_update('wlmerchant_order', array('disorderid' => $disorderid), array('id' => $id));
			}
			else {
				$res = pdo_update('wlmerchant_rush_order', array('disorderid' => $disorderid), array('id' => $id));
			}

			if ($res) {
				show_json(1);
			}
			else {
				show_json(0, '关联分销订单失败，请联系管理员');
			}
		}
		else {
			show_json(0, '该订单无法生成分销订单。');
		}
	}

	/**
     * Comment: 根据条件获取订单信息
     * Author: zzw
     * @param $where   条件一 查询rush_order表使用
     * @param $where2  条件二 查询order表使用
     * @return array
     */
	static protected function getOrderInfo($where, $where2, $limit)
	{
		$sql = 'SELECT a.id,a.mobile,a.createtime,a.sid,a.status,a.paidprid,a.mid,a.orderno,a.num,a.price,a.paytype,a.vipbuyflag,a.paytime,a.changedispatchprice,a.changeprice,a.disorderid,a.applyrefund, "a",
            f.qrcode as fqrcode,
            g.qrcode as gqrcode,
            `c`.concode as cqrcode,
            b.qrcode as bqrcode FROM ' . tablename(PDO_NAME . 'order') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'fightgroup_userecord') . ' f ON a.id = f.orderid AND a.plugin = \'wlfightgroup\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'groupon_userecord') . ' g ON a.id = g.orderid AND a.plugin = \'groupon\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'member_coupons') . ' `c` ON a.orderno = `c`.orderno AND a.plugin = \'coupon\'' . ' LEFT JOIN ' . tablename(PDO_NAME . 'bargain_userlist') . (' b ON a.id = b.orderid AND a.plugin = \'bargain\' ' . $where2 . ' ') . ' UNION ALL SELECT id,mobile,createtime,sid,status,paidprid,mid,orderno,num,price,paytype,vipbuyflag,paytime,changedispatchprice,changeprice,disorderid,applyrefund, "b","rush","rush","rush","rush" FROM ' . tablename(PDO_NAME . 'rush_order') . (' ' . $where . ' ORDER BY createtime DESC ' . $limit);
		$orderlist = pdo_fetchall($sql);
		return $orderlist;
	}

	public function orderset()
	{
		global $_W;
		global $_GPC;
		$settings = Setting::wlsetting_read('orderset');
		$settings['plugin'] = unserialize($settings['plugin']);

		if (checksubmit('submit')) {
			$base = Util::trimWithArray($_GPC['shop']);
			$base['plugin'] = serialize($_GPC['plugin']);
			Setting::wlsetting_save($base, 'orderset');
			wl_message('更新设置成功！', web_url('order/wlOrder/orderset'));
		}

		include wl_template('order/orderset');
	}

	/**
     * Comment: 储存批量发货的.cvs文件  然后返回文件名称
     * Author: zzw
     */
	public function bulkShipment()
	{
		global $_W;
		global $_GPC;
		$imageName = 'excel' . time() . rand(1000, 9999) . '.csv';
		$imageName = 'images/' . MODULE_NAME . '/' . $imageName;
		$fullName = PATH_ATTACHMENT . $imageName;
		$res = move_uploaded_file($_FILES['file']['tmp_name'], $fullName);

		if (!$res) {
			wl_json(0, '操作失败，文件上传错误');
		}

		wl_json(1, '文件上传成功,正在处理信息...', $imageName);
	}

	/**
     * Comment: 批量发货 并且返回结果信息表
     * Author: zzw
     */
	public function batchSend()
	{
		global $_W;
		global $_GPC;
		$name = $_GPC['name'];
		$fullName = PATH_ATTACHMENT . $name;
		$info = util_csv::read_csv_lines($fullName, 999, 0);
		unlink($fullName);

		foreach ($info as $k => &$v) {
			if (!is_array($v)) {
				unset($info[$k]);
				continue;
			}

			$separator = '*separator*';
			$v = explode($separator, iconv('gbk', 'utf-8', implode($separator, $v)));

			if (trim($v[8]) != '待收货') {
				$v['send_result'] = '不进行发货操作';
				continue;
			}

			$orderType = trim($v[1]);
			$orderId = trim($v[21]);
			$expressName = trim($v[17]);
			$expressNum = trim($v[18]);
			$sendResult = '发货成功';

			if ($orderType == '抢购') {
				$expressId = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $orderId), 'expressid');
			}
			else {
				if ($orderType == '拼团' || $orderType == '团购' || $orderType == '卡券' || $orderType == '砍价') {
					$expressId = pdo_getcolumn(PDO_NAME . 'order', array('id' => $orderId), 'expressid');
				}
				else {
					$sendResult = '发货失败,仅支持抢购、拼团、团购、卡卷、砍价商品的批量发货';
				}
			}

			$logistics = array('顺丰' => 'shunfeng', '申通' => 'shentong', '韵达快运' => 'yunda', '天天快递' => 'tiantian', '圆通速递' => 'yuantong', '中通速递' => 'zhongtong', 'ems快递' => 'ems', '汇通快运' => 'huitongkuaidi', '全峰快递' => 'quanfengkuaidi', '宅急送' => 'zhaijisong', 'aae全球专递' => 'aae', '安捷快递' => 'anjie', '安信达快递' => 'anxindakuaixi', '彪记快递' => 'biaojikuaidi', 'bht' => 'bht', '百福东方国际物流' => 'baifudongfang', '中国东方（COE）' => 'coe', '中国东方(COE)' => 'coe', '长宇物流' => 'changyuwuliu', '大田物流' => 'datianwuliu', '德邦物流' => 'debangwuliu', 'dhl' => 'dhl', 'dpex' => 'dpex', 'd速快递' => 'dsukuaidi', '递四方' => 'disifang', 'fedex（国外）' => 'fedex', 'fedex(国外)' => 'fedex', '飞康达物流' => 'feikangda', '凤凰快递' => 'fenghuangkuaidi', '飞快达' => 'feikuaida', '国通快递' => 'guotongkuaidi', '港中能达物流' => 'ganzhongnengda', '广东邮政物流' => 'guangdongyouzhengwuliu', '共速达' => 'gongsuda', '恒路物流' => 'hengluwuliu', '华夏龙物流' => 'huaxialongwuliu', '海红' => 'haihongwangsong', '海外环球' => 'haiwaihuanqiu', '佳怡物流' => 'jiayiwuliu', '京广速递' => 'jinguangsudikuaijian', '急先达' => 'jixianda', '佳吉物流' => 'jjwl', '加运美物流' => 'jymwl', '金大物流' => 'jindawuliu', '嘉里大通' => 'jialidatong', '晋越快递' => 'jykd', '快捷速递' => 'kuaijiesudi', '联邦快递（国内）' => 'lianb', '联邦快递(国内)' => 'lianb', '联昊通物流' => 'lianhaowuliu', '龙邦物流' => 'longbanwuliu', '立即送' => 'lijisong', '乐捷递' => 'lejiedi', '民航快递' => 'minghangkuaidi', '美国快递' => 'meiguokuaidi', '门对门' => 'menduimen', 'OCS' => 'ocs', '配思货运' => 'peisihuoyunkuaidi', '全晨快递' => 'quanchenkuaidi', '全际通物流' => 'quanjitong', '全日通快递' => 'quanritongkuaidi', '全一快递' => 'quanyikuaidi', '如风达' => 'rufengda', '三态速递' => 'santaisudi', '盛辉物流' => 'shenghuiwuliu', '速尔物流' => 'sue', '盛丰物流' => 'shengfeng', '赛澳递' => 'saiaodi', '天地华宇' => 'tiandihuayu', 'tnt' => 'tnt', 'ups' => 'ups', '万家物流' => 'wanjiawuliu', '文捷航空速递' => 'wenjiesudi', '伍圆' => 'wuyuan', '万象物流' => 'wxwl', '新邦物流' => 'xinbangwuliu', '信丰物流' => 'xinfengwuliu', '亚风速递' => 'yafengsudi', '一邦速递' => 'yibangwuliu', '优速物流' => 'youshuwuliu', '邮政包裹挂号信' => 'youzhengguonei', '邮政国际包裹挂号信' => 'youzhengguoji', '远成物流' => 'yuanchengwuliu', '源伟丰快递' => 'yuanweifeng', '元智捷诚快递' => 'yuanzhijiecheng', '运通快递' => 'yuntongkuaidi', '越丰物流' => 'yuefengwuliu', '源安达' => 'yad', '银捷速递' => 'yinjiesudi', '中铁快运' => 'zhongtiekuaiyun', '中邮物流' => 'zhongyouwuliu', '忠信达' => 'zhongxinda', '芝麻开门' => 'zhimakaimen');
			$expressEName = $logistics[$expressName];

			if (!$expressEName) {
				$sendResult = '不支持使用当前快递公司发货!';
			}

			if ($expressName && $expressId) {
				$expressDate['expressname'] = $expressEName;
				$expressDate['expresssn'] = $expressNum;
				$expressDate['orderid'] = $orderId;
				$expressDate['sendtime'] = time();
				$updateRes = pdo_update(PDO_NAME . 'express', $expressDate, array('id' => $expressId));

				if ($orderType == '抢购') {
					$UstateRes = pdo_update(PDO_NAME . 'rush_order', array('status' => 4), array('id' => $orderId));
					Message::sendremind($orderId, 'b');
				}
				else {
					if ($orderType == '拼团' || $orderType == '团购' || $orderType == '卡券' || $orderType == '砍价') {
						$UstateRes = pdo_update(PDO_NAME . 'order', array('status' => 4), array('id' => $orderId));
						Message::sendremind($orderId, 'a');
					}
				}

				if ($updateRes && $UstateRes) {
					$sendResult = '发货成功';
				}
				else {
					$sendResult = '发货失败，请检查信息是否填写正确或是否已发货!';
				}
			}

			$v['send_result'] = $sendResult;
		}

		$filter = array(0 => '订单号', 1 => '所属应用', 2 => '商品名称', 3 => '规格名称', 4 => '数量', 5 => '所属商家', 6 => '买家昵称', 7 => '买家电话', 8 => '订单状态', 9 => '支付方式', 10 => '下单时间', 11 => '支付时间', 12 => '实付金额', 13 => '备注', 14 => '收货人姓名', 15 => '收货人电话', 16 => '收货人地址', 17 => '物流公司', 18 => '快递单号', 19 => '核销时间', 20 => '核销员', 21 => '订单id', 'send_result' => '发货结果');
		util_csv::export_csv_2($info, $filter, '批量发货结果信息.csv');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
