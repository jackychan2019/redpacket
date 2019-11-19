<?php
//dezend by http://www.sucaihuo.com/
class Queue
{
	private $islock = array('value' => 0, 'expire' => 0);
	private $expiretime = 900;

	public function __construct()
	{
		$lock = cache_read('queuelockfirst');

		if (!empty($lock)) {
			$this->islock = $lock;
		}
	}

	private function setLock()
	{
		$array = array('value' => 1, 'expire' => time());
		cache_write('queuelockfirst', $array);
		cache_write(MODULE_NAME . ':task:status', $array);
		$this->islock = $array;
	}

	public function deleteLock()
	{
		cache_delete('queuelockfirst');
		$this->islock = array('value' => 0, 'expire' => time());
		return true;
	}

	public function checkLock()
	{
		$lock = $this->islock;
		if ($lock['value'] == 1 && $lock['expire'] < time() - $this->expiretime) {
			$this->deleteLock();
			return false;
		}

		if (empty($lock['value'])) {
			return false;
		}

		return true;
	}

	public function queueMain($on = '', $ex = '')
	{
		global $_W;

		if ($this->checkLock()) {
			exit('LOCK');
		}
		else {
			$this->setLock();
		}

		$this->doTask();
		$plugins = App::getPlugins(3);

		foreach ($plugins as $plu) {
			if ($plu['setting']['task'] == 'true') {
				$class_name = ucfirst($plu['ident']);

				if ($class_name == 'Wlcoupon') {
					wlCoupon::doTask();
				}
				else {
					if (method_exists($class_name, 'doTask')) {
						@$class_name::doTask();
					}
				}
			}
		}

		$this->deleteLock();
		exit('TRUE');
	}

	public function addTask($key, $value, $dotime, $important)
	{
		global $_W;
		$flag = pdo_get('wlmerchant_waittask', array('key' => $key, 'important' => $important), array('id'));

		if (empty($flag)) {
			if (empty($_W['uniacid'])) {
				if ($key == 1) {
					$_W['uniacid'] = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $important), 'uniacid');
				}
				else if ($key == 2) {
					$_W['uniacid'] = pdo_getcolumn(PDO_NAME . 'order', array('id' => $important), 'uniacid');
				}
				else {
					if ($key == 3) {
						$_W['uniacid'] = pdo_getcolumn(PDO_NAME . 'disorder', array('id' => $important), 'uniacid');
					}
				}
			}

			$data = array('uniacid' => $_W['uniacid'], 'key' => $key, 'value' => $value, 'status' => 0, 'createtime' => time(), 'dotime' => $dotime, 'important' => $important);
			$res = pdo_insert('wlmerchant_waittask', $data);
			return $res;
		}
	}

	public function finishTask($id)
	{
		global $_W;
		pdo_update('wlmerchant_waittask', array('status' => 1, 'finishtime' => time()), array('id' => $id));
	}

	public function laterTask($id)
	{
		$time = time() + 600;
		pdo_update('wlmerchant_waittask', array('dotime' => $time), array('id' => $id));
	}

	public function getNeedTaskItem()
	{
		global $_W;
		$nowtime = time();
		return pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_waittask') . (' WHERE status = 0 AND dotime < ' . $nowtime . ' ORDER BY `dotime` ASC  LIMIT 10'));
	}

	public function doTask()
	{
		global $_W;
		set_time_limit(0);
		$nowtime = time();
		$message = self::getNeedTaskItem();

		if ($message) {
			foreach ($message as $k => $v) {
				$_W['uniacid'] = $v['uniacid'];
				$data = unserialize($v['value']);

				if ($v['key'] == 1) {
					$res = Store::rushsettlement($v['important']);
				}

				if ($v['key'] == 2) {
					$res = Store::ordersettlement($v['important']);
				}

				if ($v['key'] == 3) {
					$res = Distribution::dissettlement($v['important']);
				}

				if ($v['key'] == 4) {
					$cash = pdo_get(PDO_NAME . 'settlement_record', array('id' => $v['important']));

					if ($cash['payment_type'] == 2) {
						if ($cash['sopenid']) {
							$_W['account'] = uni_fetch($_W['uniacid']);
							$realname = pdo_getcolumn(PDO_NAME . 'member', array('openid' => $cash['sopenid']), 'realname');
							$result2 = wlPay::finance($cash['sopenid'], $cash['sgetmoney'], '结算给分销商', $realname, $cash['trade_no']);
							if ($result2['return_code'] == 'SUCCESS' && $result2['result_code'] == 'SUCCESS') {
								$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 9, 'updatetime' => TIMESTAMP, 'settletype' => 2), array('id' => $cash['id']));
							}
						}
					}
					else {
						if ($cash['payment_type'] == 4) {
							if ($cash['mid']) {
								$result = Member::credit_update_credit2($cash['mid'], $cash['sgetmoney'], '分销商余额提现', 0);

								if ($result) {
									$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 9, 'updatetime' => TIMESTAMP, 'settletype' => 4), array('id' => $cash['id']));
								}
							}
						}
					}
				}

				if ($v['key'] == 5) {
					if ($data['type'] == 'order') {
						$over = pdo_fetchall('SELECT id,recordid,plugin,fkid FROM ' . tablename('wlmerchant_order') . ('WHERE id = ' . $v['important'] . ' AND failtimes < 3'));

						if ($over['plugin'] == 'wlfightgroup') {
							$usedtime = pdo_getcolumn(PDO_NAME . 'fightgroup_userecord', array('id' => $over['recordid']), 'usedtime');
							$overrefund = pdo_getcolumn(PDO_NAME . 'fightgroup_goods', array('id' => $over['fkid']), 'allowapplyre');
							if (empty($usedtime) && $overrefund) {
								$res = Wlfightgroup::refund($over['id'], 0, 0);
							}
							else {
								$res['status'] = 1;
							}
						}
						else if ($over['plugin'] == 'groupon') {
							$usedtime = pdo_getcolumn(PDO_NAME . 'groupon_userecord', array('id' => $over['recordid']), 'usedtime');
							$overrefund = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $over['fkid']), 'allowapplyre');
							if (empty($usedtime) && $overrefund) {
								$res = Groupon::refund($over['id'], 0, 0);
							}
							else {
								$res['status'] = 1;
							}
						}
						else {
							if ($over['plugin'] == 'coupon') {
								$usedtime = pdo_getcolumn(PDO_NAME . 'member_coupons', array('id' => $over['recordid']), 'usedtime');
								$overrefund = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $over['fkid']), 'allowapplyre');
								if (empty($usedtime) && $overrefund) {
									$res = wlCoupon::refund($over['id'], 0, 0);
								}
								else {
									$res['status'] = 1;
								}
							}
						}
					}
					else {
						if ($data['type'] == 'rush') {
							$over = pdo_get('wlmerchant_rush_order', array('id' => $v['important']));
							$overrefund = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $over['activityid']), 'allowapplyre');
							if (empty($over['usedtime']) && $overrefund) {
								$res = Rush::refund($over['id'], 0, 0);
							}
							else {
								$res['status'] = 1;
							}
						}
					}

					$res = $res['status'];
				}

				if ($v['key'] == 6) {
					if ($data['type'] == 'order') {
						$order = pdo_get('wlmerchant_order', array('id' => $v['important']));
						if ($order['expressid'] && $order['status'] == 4) {
							$res = pdo_update('wlmerchant_order', array('status' => 2), array('id' => $order['id']));
							pdo_update('wlmerchant_express', array('receivetime' => time()), array('id' => $order['expressid']));

							if ($res) {
								$ordertask = array('type' => $order['plugin'], 'orderid' => $v['important']);
								$ordertask = serialize($ordertask);
								Queue::addTask(2, $ordertask, time(), $v['important']);
							}

							if ($order['disorderid']) {
								$disres = pdo_update('wlmerchant_disorder', array('status' => 1), array('status' => 0, 'id' => $order['disorderid']));

								if ($disres) {
									$distask = array('type' => $order['plugin'], 'orderid' => $order['disorderid']);
									$distask = serialize($distask);
									Queue::addTask(3, $distask, time(), $order['disorderid']);
								}
							}
						}
						else {
							$res = 1;
						}
					}
					else if ($data['type'] == 'rush') {
						$order = pdo_get('wlmerchant_rush_order', array('id' => $v['important']));
						if ($order['expressid'] && $order['status'] == 4) {
							$res = pdo_update('wlmerchant_rush_order', array('status' => 2), array('id' => $order['id']));
							pdo_update('wlmerchant_express', array('receivetime' => time()), array('id' => $order['expressid']));

							if ($res) {
								$rushtask = array('type' => 'rush', 'orderid' => $v['important']);
								$rushtask = serialize($rushtask);
								Queue::addTask(1, $rushtask, time(), $v['important']);
							}

							if ($order['disorderid']) {
								$disres = pdo_update('wlmerchant_disorder', array('status' => 1), array('status' => 0, 'id' => $order['disorderid']));

								if ($disres) {
									$distask = array('type' => 'rush', 'orderid' => $order['disorderid']);
									$distask = serialize($distask);
									Queue::addTask(3, $distask, time(), $order['disorderid']);
								}
							}
						}
						else {
							$res = 1;
						}
					}
					else {
						if ($data['type'] == 'consumption') {
							$order = pdo_get('wlmerchant_consumption_record', array('id' => $v['important']));
							if ($order['expressid'] && $order['status'] == 2) {
								pdo_update('wlmerchant_consumption_record', array('status' => 3), array('id' => $order['id']));
								pdo_update('wlmerchant_express', array('receivetime' => time()), array('id' => $order['expressid']));
								$order['disorderid'] = pdo_getcolumn(PDO_NAME . 'order', array('id' => $order['orderid']), 'disorderid');

								if ($order['disorderid']) {
									$disres = pdo_update('wlmerchant_disorder', array('status' => 1), array('status' => 0, 'id' => $order['disorderid']));

									if ($disres) {
										$distask = array('type' => 'consumption', 'orderid' => $order['disorderid']);
										$distask = serialize($distask);
										Queue::addTask(3, $distask, time(), $order['disorderid']);
									}
								}
							}
							else {
								$res = 1;
							}
						}
					}
				}

				if (empty($res)) {
					$res = 0;
				}

				if ($res) {
					self::finishTask($v['id']);
				}
				else {
					self::laterTask($v['id']);
				}
			}
		}

		pdo_delete(PDO_NAME . 'order', array('createtime <' => strtotime(date('Ymd')), 'plugin' => 'consumption', 'status' => 0));
		pdo_update(PDO_NAME . 'merchantdata', array('enabled' => 3), array('enabled' => 1, 'endtime <' => $nowtime));
		$rushorder = pdo_fetchall('SELECT id,uniacid FROM ' . tablename('wlmerchant_rush_order') . 'WHERE status IN (2,3) AND issettlement = 0 AND neworderflag = 0 ORDER BY id ASC limit 10');

		if ($rushorder) {
			foreach ($rushorder as $key => $rush) {
				$flag = pdo_get('wlmerchant_waittask', array('key' => 1, 'important' => $rush['id']), array('id'));

				if (empty($flag)) {
					$rushtask = array('type' => 'rush', 'orderid' => $rush['id']);
					$rushtask = serialize($rushtask);
					$_W['uniacid'] = $rush['uniacid'];
					Queue::addTask(1, $rushtask, time(), $rush['id']);
				}
			}
		}

		$otherorder = pdo_fetchall('SELECT id,plugin,uniacid FROM ' . tablename('wlmerchant_order') . 'WHERE status IN (2,3) AND issettlement = 0 AND neworderflag = 0 AND plugin != \'consumption\' ORDER BY id ASC limit 10');

		if ($otherorder) {
			foreach ($otherorder as $key => $order) {
				$flag = pdo_get('wlmerchant_waittask', array('key' => 2, 'important' => $order['id']), array('id'));

				if (empty($flag)) {
					$rushtask = array('type' => $order['plugin'], 'orderid' => $order['id']);
					$rushtask = serialize($rushtask);
					$_W['uniacid'] = $order['uniacid'];
					Queue::addTask(2, $rushtask, time(), $order['id']);
				}
			}
		}

		$disorders = pdo_fetchall('SELECT id,plugin,uniacid FROM ' . tablename('wlmerchant_disorder') . 'WHERE status = 1 AND neworderflag = 0 ORDER BY id ASC limit 10');

		if ($disorders) {
			foreach ($disorders as $key => $disorder) {
				$flag = pdo_get('wlmerchant_waittask', array('key' => 3, 'important' => $disorder['id']), array('id'));

				if (empty($flag)) {
					$rushtask = array('type' => $disorder['plugin'], 'orderid' => $disorder['id']);
					$rushtask = serialize($rushtask);
					$_W['uniacid'] = $disorder['uniacid'];
					Queue::addTask(3, $rushtask, time(), $disorder['id']);
				}
				else {
					$detail = pdo_get('wlmerchant_disdetail', array('disorderid' => $disorder['id']), array('id'));

					if (!empty($detail)) {
						pdo_update('wlmerchant_disorder', array('status' => 2), array('id' => $disorder['id']));
					}
				}
			}
		}

		$details = pdo_getall('wlmerchant_disdetail', array('uniacid' => 0), array('id', 'disorderid'));

		if ($details) {
			foreach ($details as $key => $va) {
				$uniacid = pdo_getcolumn('wlmerchant_disorder', array('id' => $va['disorderid']), 'uniacid');
				pdo_update('wlmerchant_disdetail', array('uniacid' => $uniacid), array('id' => $va['id']));
			}
		}

		$vipmembers = pdo_fetchall('SELECT id,lastviptime,aid,uniacid,nickname FROM ' . tablename('wlmerchant_member') . 'WHERE lastviptime >' . time() . ' ORDER BY id DESC limit 2');

		if ($vipmembers) {
			foreach ($vipmembers as $key => $v) {
				$halfmember = pdo_fetch('SELECT expiretime,id FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE mid = ' . $v['id'] . ' ORDER BY expiretime DESC'));

				if ($halfmember) {
					if ($halfmember['expiretime'] < $v['lastviptime']) {
						$res = pdo_update('wlmerchant_halfcardmember', array('expiretime' => $v['lastviptime']), array('id' => $halfmember['id']));
					}
				}
				else {
					$data = array('mid' => $v['id'], 'uniacid' => $v['uniacid'], 'aid' => $v['aid'], 'expiretime' => $v['lastviptime'], 'username' => $v['nickname'], 'createtime' => time());
					$res = pdo_insert(PDO_NAME . 'halfcardmember', $data);
				}

				if ($res) {
					pdo_update('wlmerchant_member', array('lastviptime' => 999), array('id' => $v['id']));
				}
			}
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
