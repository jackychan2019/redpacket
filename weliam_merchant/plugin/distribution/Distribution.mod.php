<?php
//dezend by http://www.sucaihuo.com/
class Distribution
{
	static public function addJunior($invitid = '', $mid = '', $disflag = '', $rank = 1)
	{
		global $_W;
		$disset = $_W['wlsetting']['distribution'];
		if ($disset['switch'] != 1 || empty($invitid) || empty($mid) || $invitid == $mid) {
			return false;
		}

		$distributorid = pdo_getcolumn('wlmerchant_member', array('id' => $invitid), 'distributorid');
		$distributor = pdo_get('wlmerchant_distributor', array('id' => $distributorid));

		if (0 < $distributor['disflag']) {
			$member = Member::getMemberByMid($mid, array('distributorid', 'mobile', 'nickname', 'realname'));

			if (empty($member['distributorid'])) {
				$member = pdo_get('wlmerchant_member', array('id' => $mid), array('mobile', 'nickname', 'realname'));
				$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $mid, 'createtime' => time(), 'disflag' => 0, 'nickname' => $member['nickname'], 'mobile' => $member['mobile'], 'realname' => $member['realname'], 'leadid' => $invitid);
				if ($disset['lockstatus'] == 1 || $disset['lockstatus'] == 3) {
					$data['lockflag'] = 1;
				}

				pdo_insert('wlmerchant_distributor', $data);
				$disid = pdo_insertid();
				$res = pdo_update('wlmerchant_member', array('distributorid' => $disid), array('id' => $mid));
			}
			else {
				$distor = pdo_get('wlmerchant_distributor', array('id' => $member['distributorid']));
				if (empty($distor['leadid']) || $disset['lockstatus'] == 2 && empty($distor['disflag'])) {
					$data = array('leadid' => $invitid);

					if ($disset['lockstatus'] == 1) {
						$data['lockflag'] = 1;
					}

					$res = pdo_update('wlmerchant_distributor', $data, array('id' => $member['distributorid']));
				}
				else {
					if ($disset['mode'] && $rank == 2 && empty($distor['leadid'])) {
						$res = pdo_update('wlmerchant_distributor', array('leadid' => $invitid, 'lockflag' => 0), array('id' => $member['distributorid']));
					}
					else {
						if ($distor['lockflag'] && ($disset['lockstatus'] == 1 || $disset['lockstatus'] == 3)) {
							$res = pdo_update('wlmerchant_distributor', array('leadid' => $invitid), array('id' => $member['distributorid']));
						}
					}
				}

				$disid = $distor['id'];
			}

			if ($res && empty($disset['lockstatus'])) {
				$url = app_url('distribution/disappbase/lowpeople');
				$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $invitid), 'openid');
				Distribution::checkup($distributorid);
				Distribution::distriNotice($openid, $url, 2, $disid);

				if (1 < $disset['ranknum']) {
					$twodisid = pdo_getcolumn(PDO_NAME . 'distributor', array('mid' => $distributor['leadid']), 'id');
					Distribution::checkup($twodisid);

					if (0 < $disset['noticerank2']) {
						$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $distributor['leadid']), 'openid');
						Distribution::distriNotice($openid, $url, 2, $disid);
					}
				}

				if (2 < $disset['ranknum']) {
					$threeleadid = pdo_getcolumn(PDO_NAME . 'distributor', array('mid' => $distributor['leadid']), 'leadid');
					$threedisid = pdo_getcolumn(PDO_NAME . 'distributor', array('mid' => $threeleadid), 'id');
					Distribution::checkup($threedisid);

					if (1 < $disset['noticerank2']) {
						$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $threeleadid), 'openid');
						Distribution::distriNotice($openid, $url, 2, $disid);
					}
				}
			}

			if ($disflag) {
				$distor = pdo_get('wlmerchant_distributor', array('id' => $disid), array('disflag'));

				if (empty($distor['disflag'])) {
					header('location:' . app_url('distribution/disappbase/applyindex', array('goflag' => 1)));
				}
			}
		}
	}

	static public function getdishelp($goods, $type)
	{
		global $_W;
		$disset = Setting::wlsetting_read('distribution');
		$distributor = pdo_get('wlmerchant_distributor', array('mid' => $_W['mid'], 'disflag' => 1), array('id', 'dislevel'));

		if ($distributor['id']) {
			$disflag = 1;
		}

		if (empty($goods['isdistri'])) {
			if ($disset['helpstatus'] == 2 || $disset['helpstatus'] == 1 && $distributor['id']) {
				$helpflag = 1;
				$fxtext = $disset['fxtext'] ? $disset['fxtext'] : '分销';
				$xxtext = $disset['xxtext'] ? $disset['xxtext'] : '客户';
				$sjtext = $disset['sjtext'] ? $disset['sjtext'] : '上级';
				$yjtext = $disset['yjtext'] ? $disset['yjtext'] : '佣金';
				$fxstext = $disset['fxstext'] ? $disset['fxstext'] : '分销商';

				if ($goods['optionstatus']) {
					if ($type == 'rush') {
						$option = pdo_fetch('SELECT * FROM ' . tablename('wlmerchant_goods_option') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND type = 1 AND goodsid = ' . $goods['id'] . ' ORDER BY onedismoney DESC'));
					}
					else if ($type == 'wlfightgroup') {
						$option = pdo_fetch('SELECT * FROM ' . tablename('wlmerchant_goods_option') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND type = 2 AND goodsid = ' . $goods['id'] . ' ORDER BY onedismoney DESC'));
					}
					else {
						if ($type == 'groupon') {
							$option = pdo_fetch('SELECT * FROM ' . tablename('wlmerchant_goods_option') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND type = 3 AND goodsid = ' . $goods['id'] . ' ORDER BY onedismoney DESC'));
						}
					}

					$goods['onedismoney'] = $option['onedismoney'];
					$goods['twodismoney'] = $option['twodismoney'];
					$goods['price'] = $option['price'];
				}

				if (0 < $goods['onedismoney']) {
					if ($disset['mode']) {
						$alldismoney = $goods['onedismoney'] + $goods['twodismoney'];
					}
					else {
						$alldismoney = $goods['onedismoney'];
					}
				}
				else {
					$commission = pdo_get('wlmerchant_dislevel', array('isdefault' => 1, 'uniacid' => $_W['uniacid']), array('onecommission', 'twocommission'));

					if ($distributor['id']) {
						if ($distributor['dislevel']) {
							$commission = pdo_get('wlmerchant_dislevel', array('id' => $distributor['dislevel']), array('onecommission', 'twocommission'));
						}
					}

					if ($disset['mode']) {
						$allmax = $commission['onecommission'] + $commission['twocommission'];
					}
					else {
						$allmax = $commission['onecommission'];
					}

					$alldismoney = sprintf('%.2f', $allmax * $goods['price'] / 100);
				}

				if ($type == 'rush') {
					$copyurl = app_url('rush/home/detail', array('id' => $goods['id'], 'invitid' => $_W['mid']));
				}
				else if ($type == 'groupon') {
					$copyurl = app_url('groupon/grouponapp/groupondetail', array('cid' => $goods['id'], 'invitid' => $_W['mid']));
				}
				else if ($type == 'wlfightgroup') {
					$copyurl = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $goods['id'], 'invitid' => $_W['mid']));
				}
				else {
					if ($type == 'coupon') {
						$copyurl = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $goods['id'], 'invitid' => $_W['mid']));

						if (empty($goods['is_charge'])) {
							$helpflag = 0;
						}
					}
				}

				$result = Util::long2short($copyurl);
				if (!is_error($result) && $result['short_url'] != 'h') {
					$copyurl = $result['short_url'];
				}

				if ($type == 'wlfightgroup') {
					$type = 'fightgroup';
				}

				$posterurl = app_url('common/tools/poster', array('id' => $goods['id'], 'type' => $type));
				return array('disflag' => $disflag, 'helpimg' => $disset['helpimg'], 'helpflag' => $helpflag, 'alldismoney' => $alldismoney, 'copyurl' => $copyurl, 'posterurl' => $posterurl, 'fxtext' => $fxtext, 'xxtext' => $xxtext, 'sjtext' => $sjtext, 'yjtext' => $yjtext, 'fxstext' => $fxstext);
			}
		}
	}

	static public function checkdisflag($mid)
	{
		global $_W;
		$distributorid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $mid), 'distributorid');

		if ($distributorid) {
			$flag = pdo_getcolumn(PDO_NAME . 'distributor', array('id' => $distributorid), 'disflag');
			return $flag;
		}

		return 0;
	}

	static public function getDistText()
	{
		global $_W;
		$disset = Setting::wlsetting_read('distribution');
		$fxtext = $disset['fxtext'] ? $disset['fxtext'] : '分销';
		$xxtext = $disset['xxtext'] ? $disset['xxtext'] : '客户';
		$sjtext = $disset['sjtext'] ? $disset['sjtext'] : '上级';
		$yjtext = $disset['yjtext'] ? $disset['yjtext'] : '佣金';
		$fxstext = $disset['fxstext'] ? $disset['fxstext'] : '分销商';
		$myposter = $disset['myposter'] ? $disset['myposter'] : '我的海报';
		return array('fxtext' => $fxtext, 'xxtext' => $xxtext, 'sjtext' => $sjtext, 'yjtext' => $yjtext, 'fxstext' => $fxstext, 'myposter' => $myposter);
	}

	static public function getNumDistributor($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$distributorInfo = Util::getNumData($select, PDO_NAME . 'distributor', $where, $order, $pindex, $psize, $ifpage);
		return $distributorInfo;
	}

	static public function disCore($mid, $price, $onemoney, $twomoney, $threemoney, $orderid, $plugin, $settflag = 0)
	{
		global $_W;
		$disset = Setting::wlsetting_read('distribution');
		$disid = pdo_getcolumn('wlmerchant_member', array('id' => $mid), 'distributorid');
		if ($disset['switch'] && $disid) {
			$member = pdo_get('wlmerchant_distributor', array('uniacid' => $_W['uniacid'], 'mid' => $mid), array('id', 'dislevel', 'leadid', 'disflag', 'lockflag'));

			if ($member['disflag']) {
				$mleveid = $member['dislevel'];

				if (empty($mleveid)) {
					$mleveid = pdo_getcolumn('wlmerchant_dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'id');
				}

				$memberlevel = pdo_get(PDO_NAME . 'dislevel', array('id' => $mleveid), array('ownstatus'));

				if ($memberlevel['ownstatus']) {
					$one = pdo_get('wlmerchant_distributor', array('uniacid' => $_W['uniacid'], 'mid' => $mid, 'disflag' => 1), array('id', 'leadid', 'dislevel', 'mid', 'lockflag', 'subnum'));
				}
			}

			if (empty($one)) {
				$one = pdo_get('wlmerchant_distributor', array('uniacid' => $_W['uniacid'], 'mid' => $member['leadid'], 'disflag' => 1), array('id', 'leadid', 'dislevel', 'mid', 'lockflag', 'subnum'));
			}

			if ($one) {
				$leadid['one'] = $one['id'];
				$leveid = $one['dislevel'];

				if (empty($leveid)) {
					$leveid = pdo_getcolumn('wlmerchant_dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'id');
				}

				$onelevel = pdo_get(PDO_NAME . 'dislevel', array('id' => $leveid), array('onecommission', 'plugin'));
				$oneplugin = unserialize($onelevel['plugin']);

				if (in_array($plugin, $oneplugin)) {
					if (0 < $onemoney) {
						$leadmoney['one'] = $onemoney;
					}
					else {
						$leadmoney['one'] = number_format($price * $onelevel['onecommission'] / 100, 2);
					}
				}
				else {
					$leadmoney['one'] = 0;
				}

				if (1 < $disset['ranknum']) {
					if ($disset['mode'] && $one['leadid'] < 0) {
						$one['leadid'] = $one['mid'];
					}

					if (0 < $one['leadid'] && empty($one['lockflag'])) {
						$two = pdo_get('wlmerchant_distributor', array('uniacid' => $_W['uniacid'], 'mid' => $one['leadid'], 'disflag' => 1), array('id', 'leadid', 'dislevel'));

						if ($two) {
							$leadid['two'] = $two['id'];
							$leveid = $two['dislevel'];

							if (empty($leveid)) {
								$leveid = pdo_getcolumn('wlmerchant_dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'id');
							}

							$twolevel = pdo_get(PDO_NAME . 'dislevel', array('id' => $leveid), array('twocommission', 'plugin'));
							$twoplugin = unserialize($twolevel['plugin']);

							if (in_array($plugin, $twoplugin)) {
								if (0 < $twomoney) {
									$leadmoney['two'] = $twomoney;
								}
								else {
									$leadmoney['two'] = number_format($price * $twolevel['twocommission'] / 100, 2);
								}
							}
							else {
								$leadmoney['two'] = 0;
							}
						}
					}
				}

				if (0 < $leadmoney['one'] || 0 < $leadmoney['two']) {
					$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'status' => 0, 'plugin' => $plugin, 'orderid' => $orderid, 'orderprice' => $price, 'buymid' => $mid, 'oneleadid' => $leadid['one'], 'twoleadid' => $leadid['two'], 'leadmoney' => serialize($leadmoney), 'createtime' => time());

					if ($plugin == 'rush') {
						$order = pdo_get('wlmerchant_rush_order', array('id' => $orderid), array('neworderflag', 'num'));
						$neworderflag = $order['neworderflag'];
					}
					else {
						$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('neworderflag', 'fkid', 'num'));

						if ($plugin == 'coupon') {
							$usetimes = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $order['fkid']), 'usetimes');
							$order['num'] = $order['num'] * $usetimes;
						}

						$neworderflag = $order['neworderflag'];
					}

					if (!empty($neworderflag)) {
						$data['neworderflag'] = $neworderflag;
					}

					pdo_insert('wlmerchant_disorder', $data);
					$disorderid = pdo_insertid();

					if ($settflag) {
						$disvalue = array('type' => $plugin, 'orderid' => $disorderid);
						$disvalue = serialize($disvalue);
						Queue::addTask(3, $disvalue, time(), $disorderid);
					}

					if (!empty($neworderflag)) {
						$sdata = array('disorderid' => $disorderid, 'oneleadid' => $data['oneleadid'], 'twoleadid' => $data['twoleadid'], 'onedismoney' => sprintf('%.2f', $leadmoney['one'] / $order['num']), 'twodismoney' => sprintf('%.2f', $leadmoney['two'] / $order['num']));
						pdo_update('wlmerchant_smallorder', $sdata, array('plugin' => $plugin, 'orderid' => $orderid));
					}

					self::moneyNotice($mid, $plugin, $orderid, $one['id'], $disorderid, 1);
					if (0 < $disset['noticerank3'] && $two && $two['id'] != $one['id']) {
						self::moneyNotice($mid, $plugin, $orderid, $two['id'], $disorderid, 1);
					}

					if (1 < $disset['noticerank3'] && $three) {
						self::moneyNotice($mid, $plugin, $orderid, $three['id'], $disorderid, 1);
					}

					if (($disset['lockstatus'] == 1 || $disset['lockstatus'] == 3 && ($plugin = 'halfcard')) && $member['lockflag']) {
						$res = pdo_update('wlmerchant_distributor', array('lockflag' => 0), array('id' => $member['id']));

						if ($res) {
							$url = app_url('distribution/disappbase/lowpeople');
							$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $one['mid']), 'openid');
							if (!empty($disset['twoupone']) && $disset['mode'] == 1 && !empty($one['leadid'])) {
								$one_leadid = $one['leadid'];
								$onenum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $one['mid'] . ' AND lockflag = 0 '));

								if ($disset['twoupone'] <= $onenum + $one['subnum']) {
									pdo_update('wlmerchant_distributor', array('leadid' => -1), array('id' => $one['id']));
									pdo_update('wlmerchant_distributor', array('leadid' => $one['leadid']), array('leadid' => $one['mid']));
								}

								pdo_update('wlmerchant_distributor', array('subnum' => $one['subnum'] + 1), array('id' => $one['id']));

								if ($one_leadid != -1) {
									pdo_update('wlmerchant_distributor', array('leadid' => $one_leadid), array('id' => $member['id']));
								}

								pdo_update('wlmerchant_distributor', array('disflag' => 1), array('id' => $member['id']));
							}

							Distribution::checkup($one['id']);
							Distribution::distriNotice($openid, $url, 2, $member['id']);

							if (1 < $disset['ranknum']) {
								$twodisid = pdo_getcolumn(PDO_NAME . 'distributor', array('mid' => $one['leadid']), 'id');
								Distribution::checkup($twodisid);
								if (0 < $disset['noticerank2'] && empty($one['lockflag'])) {
									$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $one['leadid']), 'openid');
									Distribution::distriNotice($openid, $url, 2, $member['id']);
								}
							}
						}
					}
				}
				else {
					$disorderid = 0;
				}
			}
			else {
				$disorderid = 0;
			}
		}
		else {
			$disorderid = 0;
		}

		return $disorderid;
	}

	static public function checkup($distributorid)
	{
		global $_W;
		$distributor = pdo_get('wlmerchant_distributor', array('id' => $distributorid));
		$_W['uniacid'] = $distributor['uniacid'];
		$settings = Setting::wlsetting_read('distribution');
		$nowdislevel = $distributor['dislevel'];

		if (empty($nowdislevel)) {
			$nowdislevel = pdo_getcolumn(PDO_NAME . 'dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'id');
		}

		$nowupstandard = pdo_getcolumn(PDO_NAME . 'dislevel', array('id' => $nowdislevel), 'upstandard');

		if (empty($nowupstandard)) {
			$nowupstandard = 0;
		}

		if ($settings['levelupstatus'] == 0 || empty($settings['levelupstatus'])) {
			$disupstandard = $distributor['dismoney'];
		}
		else if ($settings['levelupstatus'] == 1) {
			$onelows = pdo_getall('wlmerchant_distributor', array('leadid' => $distributor['mid'], 'lockflag' => 0), array('mid'));
			$onenum = count($onelows);
			$twonum = 0;
			$threenum = 0;
			if (1 < $settings['ranknum'] && $onelows) {
				foreach ($onelows as $key => $one) {
					$twolows = pdo_getall('wlmerchant_distributor', array('leadid' => $one['mid'], 'lockflag' => 0), array('mid'));
					$twonum += count($twolows);
					if (2 < $settings['ranknum'] && $twolows) {
						foreach ($twolows as $key => $two) {
							$threelows = pdo_getall('wlmerchant_distributor', array('leadid' => $two['mid'], 'lockflag' => 0), array('mid'));
							$threenum += count($threelows);
						}
					}
				}
			}

			$disupstandard = $onenum + $twonum + $threenum;
		}
		else if ($settings['levelupstatus'] == 2) {
			$onelows = pdo_getall('wlmerchant_distributor', array('leadid' => $distributor['mid'], 'lockflag' => 0), array('mid'));
			$disupstandard = count($onelows);
		}
		else if ($settings['levelupstatus'] == 3) {
			$onelows = pdo_getall('wlmerchant_distributor', array('leadid' => $distributor['mid'], 'lockflag' => 0), array('mid', 'disflag'));
			$onenum = 0;
			$twonum = 0;
			$threenum = 0;

			if ($onelows) {
				foreach ($onelows as $key => $one) {
					if ($one['disflag']) {
						$onenum += 1;
					}

					if (1 < $settings['ranknum']) {
						$twolows = pdo_getall('wlmerchant_distributor', array('leadid' => $one['mid']), array('mid', 'disflag'));

						if ($twolows) {
							foreach ($twolows as $key => $two) {
								if ($two['disflag']) {
									$twonum += 1;
								}

								if (2 < $settings['ranknum']) {
									$threelows = pdo_getall('wlmerchant_distributor', array('leadid' => $two['mid'], 'disflag' => 1), array('mid'));
									$threenum += count($threelows);
								}
							}
						}
					}
				}
			}

			$disupstandard = $onenum + $twonum + $threenum;
		}
		else {
			if ($settings['levelupstatus'] == 4) {
				$onelows = pdo_getall('wlmerchant_distributor', array('leadid' => $distributor['mid'], 'disflag' => 1, 'lockflag' => 0), array('mid'));
				$disupstandard = count($onelows);
			}
		}

		if ($disupstandard) {
			$highlevel = pdo_fetch('SELECT * FROM ' . tablename('wlmerchant_dislevel') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND upstandard > ' . $nowupstandard . ' ORDER BY upstandard ASC'));

			if ($highlevel) {
				if ($highlevel['upstandard'] <= $disupstandard) {
					$res = pdo_update('wlmerchant_distributor', array('dislevel' => $highlevel['id']), array('id' => $distributorid));

					if ($res) {
						self::levelupNotice($distributorid, $nowdislevel, $highlevel['id']);
					}
				}
			}
		}
	}

	static public function payApplydisNotify($params)
	{
		global $_W;
		Util::wl_log('payResult_notify', PATH_DATA . 'merchant/data/', $params);
		$order_out = pdo_get(PDO_NAME . 'order', array('orderno' => $params['tid']));

		if ($order_out['status'] == 0) {
			$distributor = pdo_get('wlmerchant_distributor', array('mid' => $order_out['mid']));
			$settings = Setting::wlsetting_read('distribution');

			if ($order_out['specid'] == 2) {
				$examine = $settings['twoexamine'];
				$onegetmoney = $settings['onegetmoney'];
				$twogetmoney = 0;
			}
			else {
				$examine = $settings['examine'];

				if (empty($settings['mode'])) {
					$onegetmoney = $settings['modeonemoney'];
					$twogetmoney = $settings['modetwomoney'];
				}
			}

			$member = pdo_get('wlmerchant_member', array('id' => $order_out['mid']), array('mobile', 'nickname', 'realname'));
			$data = self::getPayData($params);

			if ($data['status'] == 1) {
				$data['status'] = 3;
			}

			$data['disorderid'] = self::disCore($order_out['mid'], $order_out['price'], $onegetmoney, $twogetmoney, 0, $order_out['id'], 'distribution', 1);
			pdo_update(PDO_NAME . 'order', $data, array('orderno' => $params['tid']));

			if ($examine == 1) {
				$data = array('uniacid' => $_W['uniacid'], 'aid' => $order_out['aid'], 'mid' => $order_out['mid'], 'status' => 0, 'realname' => $member['realname'], 'mobile' => $member['mobile'], 'createtime' => time(), 'rank' => $order_out['specid']);
				$res = pdo_insert('wlmerchant_applydistributor', $data);
				self::toadmin($member['realname']);
			}
			else {
				if ($distributor) {
					if (empty($distributor['disflag'])) {
						if ($order_out['specid'] == 1 && $base['mode']) {
							pdo_update('wlmerchant_distributor', array('disflag' => 1, 'leadid' => -1, 'createtime' => time(), 'lockflag' => 0), array('mid' => $order_out['mid']));
						}
						else {
							pdo_update('wlmerchant_distributor', array('disflag' => 1, 'createtime' => time(), 'lockflag' => 0), array('mid' => $order_out['mid']));
						}
					}
				}
				else {
					$data = array('uniacid' => $_W['uniacid'], 'aid' => $order_out['aid'], 'mid' => $order_out['mid'], 'createtime' => time(), 'disflag' => 1, 'nickname' => $member['nickname'], 'mobile' => $member['mobile'], 'realname' => $member['realname'], 'leadid' => -1);
					pdo_insert('wlmerchant_distributor', $data);
					$disid = pdo_insertid();
					pdo_update('wlmerchant_member', array('distributorid' => $disid), array('id' => $order_out['mid']));
				}

				$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $order_out['mid']), 'openid');
				Distribution::distriNotice($openid, $url, 1);
			}
		}
	}

	static public function payApplydisReturn($params)
	{
		wl_message('支付成功', app_url('distribution/disappbase/index'), 'success');
	}

	static public function getPayData($params)
	{
		global $_W;
		$data = array('status' => $params['result'] == 'success' ? 1 : 0);

		if ($params['is_usecard'] == 1) {
			$fee = $params['card_fee'];
			$data['is_usecard'] = 1;
		}
		else {
			$fee = $params['fee'];
		}

		$paytype = array('credit' => 1, 'wechat' => 2, 'alipay' => 3, 'delivery' => 4, 'wxapp' => 5);
		$data['paytype'] = $paytype[$params['type']];

		if ($params['type'] == 'wechat') {
			$data['transid'] = $params['tag']['transaction_id'];
		}

		$data['paytime'] = TIMESTAMP;
		return $data;
	}

	static public function toadmin($name)
	{
		global $_W;
		$first = '一个成为分销商的申请待审核';
		$keyword1 = '申请人:' . $name;
		$keyword2 = '待审核';
		$remark = '请管理员尽快审核';
		$url = app_url('dashboard/home/index');
		Message::jobNotice($_W['wlsetting']['noticeMessage']['adminopenid'], $first, $keyword1, $keyword2, $remark, $url);
	}

	static public function distriNotice($openid, $url, $flag, $lowdisid = '', $txmoney = '', $cashtype = '')
	{
		global $_W;
		$settings = Setting::wlsetting_read('distribution');
		$nickname = pdo_getcolumn(PDO_NAME . 'member', array('openid' => $openid), 'nickname');

		if ($flag == 1) {
			$keyword1 = $settings['noticetitle1'] ? $settings['noticetitle1'] : '成为分销商通知';
			$keyword2 = '已完成';
			$remark = $settings['noticecontent1'];
			$remark = str_replace('[昵称]', $nickname, $remark);
			$remark = str_replace('[时间]', date('Y-m-d H:i:s', time()), $remark);
			$keyword1 = str_replace('[昵称]', $nickname, $keyword1);
			$keyword1 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $keyword1);
		}
		else if ($flag == 2) {
			$keyword1 = $settings['noticetitle2'] ? $settings['noticetitle2'] : '新增下级通知';
			$keyword2 = '已完成';
			$remark = $settings['noticecontent2'];
			$lowdistri = pdo_get('wlmerchant_distributor', array('id' => $lowdisid), array('nickname', 'leadid'));
			$lowname = $lowdistri['nickname'];
			$leadid = pdo_getcolumn(PDO_NAME . 'member', array('openid' => $openid), 'id');
			$remark = str_replace('[昵称]', $nickname, $remark);
			$remark = str_replace('[下级昵称]', $lowname, $remark);
			$remark = str_replace('[时间]', date('Y-m-d H:i:s', time()), $remark);
			$keyword1 = str_replace('[昵称]', $nickname, $keyword1);
			$keyword1 = str_replace('[下级昵称]', $lowname, $keyword1);
			$keyword1 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $keyword1);

			if ($lowdistri['leadid'] == $leadid) {
				$lowrank = '一级';
			}
			else {
				$lowrank = '二级';
			}

			$remark = str_replace('[下线层级]', $lowrank, $remark);
			$keyword1 = str_replace('[下线层级]', $lowrank, $keyword1);
		}
		else if ($flag == 3) {
			$keyword1 = $settings['noticetitle5'] ? $settings['noticetitle5'] : '提现申请提交通知';
			$keyword2 = '申请已提交,待审核';
			$remark = $settings['noticecontent5'];
			$remark = str_replace('[昵称]', $nickname, $remark);
			$remark = str_replace('[时间]', date('Y-m-d H:i:s', time()), $remark);
			$remark = str_replace('[金额]', $txmoney, $remark);
			$keyword1 = str_replace('[昵称]', $nickname, $keyword1);
			$keyword1 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $keyword1);
			$keyword1 = str_replace('[金额]', $txmoney, $keyword1);
		}
		else {
			if ($flag == 4 || $flag == 5) {
				$keyword1 = $settings['noticetitle6'] ? $settings['noticetitle6'] : '提现申请审核完成通知';

				if ($flag == 4) {
					$keyword2 = '申请已通过,待打款';
				}
				else {
					$keyword2 = '申请被驳回,请重试';
				}

				$remark = $settings['noticecontent6'];
				$remark = str_replace('[昵称]', $nickname, $remark);
				$remark = str_replace('[时间]', date('Y-m-d H:i:s', time()), $remark);
				$remark = str_replace('[金额]', $txmoney, $remark);
				$keyword1 = str_replace('[昵称]', $nickname, $keyword1);
				$keyword1 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $keyword1);
				$keyword1 = str_replace('[金额]', $txmoney, $keyword1);
			}
			else {
				if ($flag == 6) {
					$keyword1 = $settings['noticetitle7'] ? $settings['noticetitle7'] : '提现申请审核完成通知';
					$keyword2 = '提现佣金已打款';
					$remark = $settings['noticecontent7'];
					$remark = str_replace('[昵称]', $nickname, $remark);
					$remark = str_replace('[时间]', date('Y-m-d H:i:s', time()), $remark);
					$remark = str_replace('[金额]', $txmoney, $remark);
					$remark = str_replace('[打款方式]', $cashtype, $remark);
					$keyword1 = str_replace('[昵称]', $nickname, $keyword1);
					$keyword1 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $keyword1);
					$keyword1 = str_replace('[金额]', $txmoney, $keyword1);
					$keyword1 = str_replace('[打款方式]', $cashtype, $keyword1);
				}
			}
		}

		$first = '您有一个新的业务通知';

		if ($remark != '') {
			Message::jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url);
		}
	}

	static public function moneyNotice($buymid, $plugin, $orderid, $senddisid, $disorderid, $flag)
	{
		global $_W;
		$settings = Setting::wlsetting_read('distribution');
		$url = app_url('distribution/disappbase/disorder');
		$time = date('Y-m-d H:i:s', time());
		$buyname = pdo_getcolumn('wlmerchant_member', array('id' => $buymid), 'nickname');
		$sendmid = pdo_getcolumn('wlmerchant_distributor', array('id' => $senddisid), 'mid');
		$openid = pdo_getcolumn('wlmerchant_member', array('id' => $sendmid), 'openid');
		$disorder = pdo_get('wlmerchant_disorder', array('id' => $disorderid));
		$money = unserialize($disorder['leadmoney']);

		if ($plugin == 'fightgroup') {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('fkid', 'orderno'));
			$goodsid = $order['fkid'];
			$orderno = $order['orderno'];
			$goodsname = pdo_getcolumn('wlmerchant_fightgroup_goods', array('id' => $goodsid), 'name');
			$orderstatus = '拼团商品';
		}
		else if ($plugin == 'rush') {
			$order = pdo_get('wlmerchant_rush_order', array('id' => $orderid), array('activityid', 'orderno'));
			$goodsid = $order['activityid'];
			$orderno = $order['orderno'];
			$goodsname = pdo_getcolumn('wlmerchant_rush_activity', array('id' => $goodsid), 'name');
			$orderstatus = '抢购商品';
		}
		else if ($plugin == 'halfcard') {
			$order = pdo_get('wlmerchant_halfcard_record', array('id' => $orderid), array('typeid', 'orderno'));
			$goodsid = $order['typeid'];
			$orderno = $order['orderno'];
			$goodsname = pdo_getcolumn('wlmerchant_halfcard_type', array('id' => $goodsid), 'name');
			$orderstatus = '一卡通';
		}
		else if ($plugin == 'pocket') {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('fkid', 'orderno'));
			$goodsid = $order['fkid'];
			$orderno = $order['orderno'];
			$typeid = pdo_getcolumn('wlmerchant_pocket_informations', array('id' => $goodsid), 'type');
			$goodsname = pdo_getcolumn('wlmerchant_pocket_type', array('id' => $typeid), 'title');
			$orderstatus = '掌上信息';
		}
		else if ($plugin == 'charge') {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('fkid', 'orderno'));
			$goodsid = $order['fkid'];
			$orderno = $order['orderno'];
			$goodsname = pdo_getcolumn('wlmerchant_chargelist', array('id' => $goodsid), 'name');
			$orderstatus = '付费入驻';
		}
		else if ($plugin == 'coupon') {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('fkid', 'orderno'));
			$goodsid = $order['fkid'];
			$orderno = $order['orderno'];
			$goodsname = pdo_getcolumn('wlmerchant_couponlist', array('id' => $goodsid), 'title');
			$orderstatus = '卡券';
		}
		else if ($plugin == 'groupon') {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('fkid', 'orderno'));
			$goodsid = $order['fkid'];
			$orderno = $order['orderno'];
			$goodsname = pdo_getcolumn('wlmerchant_groupon_activity', array('id' => $goodsid), 'name');
			$orderstatus = '团购商品';
		}
		else if ($plugin == 'distribution') {
			$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('fkid', 'orderno'));
			$goodsid = $order['fkid'];
			$orderno = $order['orderno'];
			$goodsname = '付费申请';
			$orderstatus = '付费申请';
		}
		else {
			if ($plugin == 'consumption') {
				$order = pdo_get('wlmerchant_order', array('id' => $orderid), array('fkid', 'orderno'));
				$goodsid = $order['fkid'];
				$orderno = $order['orderno'];
				$goodsname = pdo_getcolumn('wlmerchant_consumption_goods', array('id' => $goodsid), 'title');
				$orderstatus = '积分商城';
			}
		}

		$nickname = pdo_getcolumn(PDO_NAME . 'member', array('id' => $sendmid), 'nickname');

		if ($settings['mode']) {
			if ($senddisid == $disorder['oneleadid']) {
				$lowrank = '一级';

				if ($senddisid == $disorder['twoleadid']) {
					$leadmoney = $money['two'] + $money['one'];
				}
				else {
					$leadmoney = $money['one'];
				}
			}
			else {
				if ($senddisid == $disorder['twoleadid']) {
					$lowrank = '二级';
					$leadmoney = $money['two'];
				}
			}
		}
		else if ($senddisid == $disorder['oneleadid']) {
			$lowrank = '一级';
			$leadmoney = $money['one'];
		}
		else if ($senddisid == $disorder['twoleadid']) {
			$lowrank = '二级';
			$leadmoney = $money['two'];
		}
		else {
			if ($senddisid == $disorder['threeleadid']) {
				$lowrank = '三级';
				$leadmoney = $money['three'];
			}
		}

		if ($flag == 1) {
			$keyword1 = $settings['noticetitle3'] ? $settings['noticetitle3'] : '下级付款通知';
			$keyword2 = '下级已付款';
			$remark = $settings['noticecontent3'];
		}
		else {
			$keyword1 = $settings['noticetitle4'] ? $settings['noticetitle4'] : '佣金到账通知';
			$keyword2 = '已完成';
			$remark = $settings['noticecontent4'];
		}

		$keyword1 = str_replace('[昵称]', $nickname, $keyword1);
		$keyword1 = str_replace('[下级昵称]', $buyname, $keyword1);
		$keyword1 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $keyword1);
		$keyword1 = str_replace('[下线层级]', $lowrank, $keyword1);
		$keyword1 = str_replace('[佣金金额]', $leadmoney, $keyword1);
		$keyword1 = str_replace('[订单金额]', $disorder['orderprice'], $keyword1);
		$keyword1 = str_replace('[订单编号]', $orderno, $keyword1);
		$keyword1 = str_replace('[订单类型]', $orderstatus, $keyword1);
		$keyword1 = str_replace('[商品名称]', $goodsname, $keyword1);
		$remark = str_replace('[昵称]', $nickname, $remark);
		$remark = str_replace('[下级昵称]', $buyname, $remark);
		$remark = str_replace('[时间]', date('Y-m-d H:i:s', time()), $remark);
		$remark = str_replace('[下线层级]', $lowrank, $remark);
		$remark = str_replace('[佣金金额]', $leadmoney, $remark);
		$remark = str_replace('[订单金额]', $disorder['orderprice'], $remark);
		$remark = str_replace('[订单编号]', $orderno, $remark);
		$remark = str_replace('[订单类型]', $orderstatus, $remark);
		$remark = str_replace('[商品名称]', $goodsname, $remark);

		if ($flag == 2) {
			$distormoney = pdo_get(PDO_NAME . 'distributor', array('id' => $senddisid), array('nowmoney', 'dismoney'));
			$remark = str_replace('[可提现佣金]', $distormoney['nowmoney'], $remark);
			$remark = str_replace('[总获得佣金]', $distormoney['dismoney'], $remark);
			$keyword1 = str_replace('[可提现佣金]', $distormoney['nowmoney'], $keyword1);
			$keyword1 = str_replace('[总获得佣金]', $distormoney['dismoney'], $keyword1);
		}

		$first = '您有一个新的业务通知';
		Message::jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url);
	}

	static public function adddisdetail($disorderid, $leadid, $buymid, $type, $price, $plugin, $rank, $reason = '', $nowmoney, $checkcode = '')
	{
		global $_W;
		$data = array('uniacid' => $_W['uniacid'], 'disorderid' => $disorderid, 'leadid' => $leadid, 'buymid' => $buymid, 'type' => $type, 'price' => $price, 'createtime' => time(), 'plugin' => $plugin, 'rank' => $rank, 'reason' => $reason, 'nowmoney' => $nowmoney, 'checkcode' => $checkcode);
		pdo_insert(PDO_NAME . 'disdetail', $data);
	}

	static public function levelupNotice($disid, $oldlevelid, $newlevelid)
	{
		global $_W;
		$settings = Setting::wlsetting_read('distribution');
		$url = app_url('distribution/disappbase/index');
		$time = date('Y-m-d H:i:s', time());
		$mid = pdo_getcolumn('wlmerchant_distributor', array('id' => $disid), 'mid');
		$member = pdo_get('wlmerchant_member', array('id' => $mid), array('openid', 'nickname'));
		$oldlevel = pdo_get('wlmerchant_dislevel', array('id' => $oldlevelid), array('name', 'onecommission', 'twocommission', 'threecommission'));
		$newlevel = pdo_get('wlmerchant_dislevel', array('id' => $newlevelid), array('name', 'onecommission', 'twocommission', 'threecommission'));
		$keyword1 = $settings['noticetitle8'] ? $settings['noticetitle8'] : '分销商等级升级通知';
		$keyword2 = '已升级';
		$remark = $settings['noticecontent8'];
		$keyword1 = str_replace('[昵称]', $member['nickname'], $keyword1);
		$keyword1 = str_replace('[旧等级]', $oldlevel['name'], $keyword1);
		$keyword1 = str_replace('[旧一级分销比例]', $oldlevel['onecommission'], $keyword1);
		$keyword1 = str_replace('[旧二级分销比例]', $oldlevel['twocommission'], $keyword1);
		$keyword1 = str_replace('[旧三级分销比例]', $oldlevel['threecommission'], $keyword1);
		$keyword1 = str_replace('[新等级]', $newlevel['name'], $keyword1);
		$keyword1 = str_replace('[新一级分销比例]', $newlevel['onecommission'], $keyword1);
		$keyword1 = str_replace('[新二级分销比例]', $newlevel['twocommission'], $keyword1);
		$keyword1 = str_replace('[新三级分销比例]', $newlevel['threecommission'], $keyword1);
		$keyword1 = str_replace('[时间]', $time, $keyword1);
		$remark = str_replace('[昵称]', $member['nickname'], $remark);
		$remark = str_replace('[旧等级]', $oldlevel['name'], $remark);
		$remark = str_replace('[旧一级分销比例]', $oldlevel['onecommission'], $remark);
		$remark = str_replace('[旧二级分销比例]', $oldlevel['twocommission'], $remark);
		$remark = str_replace('[旧三级分销比例]', $oldlevel['threecommission'], $remark);
		$remark = str_replace('[新等级]', $newlevel['name'], $remark);
		$remark = str_replace('[新一级分销比例]', $newlevel['onecommission'], $remark);
		$remark = str_replace('[新二级分销比例]', $newlevel['twocommission'], $remark);
		$remark = str_replace('[新三级分销比例]', $newlevel['threecommission'], $remark);
		$remark = str_replace('[时间]', $time, $remark);
		$first = '您有一个新的业务通知';
		Message::jobNotice($member['openid'], $first, $keyword1, $keyword2, $remark, $url);
	}

	static public function refunddis($id)
	{
		global $_W;
		$order = pdo_get('wlmerchant_disorder', array('id' => $id));

		if ($order['neworderflag']) {
			$onemoney = 0;
			$twomoney = 0;
			$smallorder = pdo_getall('wlmerchant_smallorder', array('disorderid' => $id, 'status' => 3));

			foreach ($smallorder as $key => $small) {
				if ($order['status'] == 2) {
					if (0 < $small['onedismoney']) {
						$one = pdo_get('wlmerchant_distributor', array('id' => $small['oneleadid']));
						$onedismoney = $one['dismoney'] - $small['onedismoney'];
						$onenowmoney = $one['nowmoney'] - $small['onedismoney'];
						pdo_update('wlmerchant_distributor', array('dismoney' => $onedismoney, 'nowmoney' => $onenowmoney), array('id' => $one['id']));
						$leadid = pdo_getcolumn('wlmerchant_distributor', array('id' => $order['oneleadid']), 'mid');
						self::adddisdetail($order['id'], $leadid, $order['buymid'], 2, $small['onedismoney'], $order['plugin'], 1, '订单退款减佣', $onenowmoney, $small['checkcode']);
					}

					if (0 < $small['twodismoney']) {
						$two = pdo_get('wlmerchant_distributor', array('id' => $small['twoleadid']));
						$twodismoney = $two['dismoney'] - $small['twodismoney'];
						$twonowmoney = $two['nowmoney'] - $small['twodismoney'];
						pdo_update('wlmerchant_distributor', array('dismoney' => $twodismoney, 'nowmoney' => $twonowmoney), array('id' => $two['id']));
						$leadid = pdo_getcolumn('wlmerchant_distributor', array('id' => $order['twoleadid']), 'mid');
						self::adddisdetail($order['id'], $leadid, $order['buymid'], 2, $small['twodismoney'], $order['plugin'], 1, '订单退款减佣', $twonowmoney, $small['checkcode']);
					}
				}

				$onemoney += $small['onedismoney'];
				$twomoney += $small['twodismoney'];
			}

			$leadmoneys = unserialize($order['leadmoney']);
			$leadmoneys['one'] = sprintf('%.2f', $leadmoneys['one'] - $onemoney);
			$leadmoneys['two'] = sprintf('%.2f', $leadmoneys['two'] - $twomoney);
			$orderdata['leadmoney'] = serialize($leadmoneys);
			$hexiao = pdo_get('wlmerchant_smallorder', array('disorderid' => $id, 'status' => 2), array('id'));

			if ($hexiao) {
				$orderdata['status'] = 2;
			}
			else {
				$orderdata['status'] = 3;
			}

			pdo_update('wlmerchant_disorder', $orderdata, array('id' => $id));
		}
		else {
			if ($order['status'] == 2) {
				$leadmoneys = unserialize($order['leadmoney']);
				$one = pdo_get('wlmerchant_distributor', array('id' => $order['oneleadid']));

				if (0 < $leadmoneys['one']) {
					$onedismoney = $one['dismoney'] - $leadmoneys['one'];
					$onenowmoney = $one['nowmoney'] - $leadmoneys['one'];
					pdo_update('wlmerchant_distributor', array('dismoney' => $onedismoney, 'nowmoney' => $onenowmoney), array('id' => $one['id']));
					$leadid = pdo_getcolumn('wlmerchant_distributor', array('id' => $order['oneleadid']), 'mid');
					self::adddisdetail($order['id'], $leadid, $order['buymid'], 2, $leadmoneys['one'], $order['plugin'], 1, '分销订单退款', $onenowmoney);
				}

				if ($order['twoleadid'] && 0 < $leadmoneys['two']) {
					$two = pdo_get('wlmerchant_distributor', array('id' => $order['twoleadid']));
					$twodismoney = $two['dismoney'] - $leadmoneys['two'];
					$twonowmoney = $two['nowmoney'] - $leadmoneys['two'];
					pdo_update('wlmerchant_distributor', array('dismoney' => $twodismoney, 'nowmoney' => $twonowmoney), array('id' => $two['id']));
					$leadid = pdo_getcolumn('wlmerchant_distributor', array('id' => $order['twoleadid']), 'mid');
					self::adddisdetail($order['id'], $leadid, $order['buymid'], 2, $leadmoneys['two'], $order['plugin'], 2, '分销订单退款', $twonowmoney);
				}
			}
		}
	}

	static public function dissettlement($id)
	{
		global $_W;
		$order = pdo_get('wlmerchant_disorder', array('id' => $id));
		$nosetflag = pdo_getcolumn('wlmerchant_disdetail', array('disorderid' => $order['id'], 'plugin' => $order['plugin']), 'id');

		if (empty($nosetflag)) {
			$leadmoneys = unserialize($order['leadmoney']);
			$one = pdo_get('wlmerchant_distributor', array('id' => $order['oneleadid']));

			if (0 < $leadmoneys['one']) {
				$onedismoney = $one['dismoney'] + $leadmoneys['one'];
				$onenowmoney = $one['nowmoney'] + $leadmoneys['one'];
				$res1 = pdo_update('wlmerchant_distributor', array('dismoney' => $onedismoney, 'nowmoney' => $onenowmoney), array('id' => $one['id']));
				self::checkup($one['id']);
				$leadid = pdo_getcolumn('wlmerchant_distributor', array('id' => $order['oneleadid']), 'mid');
				self::adddisdetail($order['id'], $leadid, $order['buymid'], 1, $leadmoneys['one'], $order['plugin'], 1, '分销订单结算', $onenowmoney);
			}

			if ($order['twoleadid'] && 0 < $leadmoneys['two']) {
				$two = pdo_get('wlmerchant_distributor', array('id' => $order['twoleadid']));
				$twodismoney = $two['dismoney'] + $leadmoneys['two'];
				$twonowmoney = $two['nowmoney'] + $leadmoneys['two'];
				$res2 = pdo_update('wlmerchant_distributor', array('dismoney' => $twodismoney, 'nowmoney' => $twonowmoney), array('id' => $two['id']));
				self::checkup($two['id']);
				$leadid = pdo_getcolumn('wlmerchant_distributor', array('id' => $order['twoleadid']), 'mid');
				self::adddisdetail($order['id'], $leadid, $order['buymid'], 1, $leadmoneys['two'], $order['plugin'], 2, '分销订单结算', $twonowmoney);
			}

			if ($res1 || $res2) {
				pdo_update('wlmerchant_disorder', array('status' => 2), array('id' => $order['id']));

				if (!empty($order['neworderflag'])) {
					pdo_update('wlmerchant_smallorder', array('dissettletime' => time()), array('disorderid' => $id));
				}
			}
			else {
				if ($leadmoneys['one'] < 0.01 && $leadmoneys['two'] < 0.01) {
					pdo_update('wlmerchant_disorder', array('status' => 2), array('id' => $order['id']));
					return true;
				}
			}

			if (0 < $leadmoneys['one']) {
				self::moneyNotice($order['buymid'], $order['plugin'], $order['orderid'], $order['oneleadid'], $order['id'], 2);
			}

			if ($order['twoleadid'] != $order['oneleadid'] && $order['twoleadid'] && 0 < $leadmoneys['two']) {
				self::moneyNotice($order['buymid'], $order['plugin'], $order['orderid'], $order['twoleadid'], $order['id'], 2);
			}

			if ($order['threeleadid'] && 0 < $leadmoneys['three']) {
				$three = pdo_get('wlmerchant_distributor', array('id' => $order['threeleadid']));
				$threedismoney = $three['dismoney'] + $leadmoneys['three'];
				$threenowmoney = $three['nowmoney'] + $leadmoneys['three'];
				pdo_update('wlmerchant_distributor', array('dismoney' => $threedismoney, 'nowmoney' => $threenowmoney), array('id' => $three['id']));
				self::checkup($three['id']);
				self::moneyNotice($order['buymid'], $order['plugin'], $order['orderid'], $order['threeleadid'], $order['id'], 2);
				$leadid = pdo_getcolumn('wlmerchant_distributor', array('id' => $order['threeleadid']), 'mid');
				self::adddisdetail($order['id'], $leadid, $order['buymid'], 1, $leadmoneys['three'], $order['plugin'], 3, '分销订单结算', $threenowmoney);
			}

			if ($res1 || $res2) {
				return true;
			}
		}
		else {
			return true;
		}
	}

	static public function doTask()
	{
		global $_W;
		$overduetime = time() - 300;
		$overdueorders = pdo_fetchall('SELECT id FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 0 AND plugin = \'distribution\' AND createtime < ' . $overduetime . ' ORDER BY id DESC'));

		if ($overdueorders) {
			foreach ($overdueorders as $key => $over) {
				pdo_delete('wlmerchant_order', array('id' => $over['id']));
			}
		}

		$settings = Setting::wlsetting_read('distribution');

		if ($settings['bindvip']) {
			$now = time();

			if ($settings['bindvip'] == 2) {
				$overdis = pdo_fetchall('SELECT id,mid FROM ' . tablename('wlmerchant_distributor') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disflag = 1 AND expiretime < ' . $now . ' AND source = 0'));
			}
			else {
				$overdis = pdo_fetchall('SELECT id,mid FROM ' . tablename('wlmerchant_distributor') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disflag = 1 AND expiretime < ' . $now . ' '));
			}

			if ($overdis) {
				foreach ($overdis as $key => &$dis) {
					$halfmember = pdo_fetch('SELECT expiretime FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $dis['mid'] . ' ORDER BY expiretime DESC'));

					if ($halfmember['expiretime'] < $now) {
						pdo_update('wlmerchant_distributor', array('disflag' => -1, 'expiretime' => $halfmember['expiretime']), array('id' => $dis['id']));
					}
					else {
						pdo_update('wlmerchant_distributor', array('expiretime' => $halfmember['expiretime']), array('id' => $dis['id']));
					}
				}
			}

			$nodis = pdo_fetchall('SELECT id,mid FROM ' . tablename('wlmerchant_distributor') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disflag = -1 '));

			if ($nodis) {
				foreach ($nodis as $key => &$nod) {
					$half = pdo_fetch('SELECT expiretime FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $nod['mid'] . ' ORDER BY expiretime DESC'));

					if ($now < $half['expiretime']) {
						pdo_update('wlmerchant_distributor', array('disflag' => 1, 'expiretime' => $half['expiretime']), array('id' => $nod['id']));
					}
				}
			}

			if ($settings['bindvip'] == 2) {
				$nodis2 = pdo_fetchall('SELECT id,mid FROM ' . tablename('wlmerchant_distributor') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disflag = -1 AND source = 1'));

				if ($nodis2) {
					foreach ($nodis2 as $key => &$nod2) {
						pdo_update('wlmerchant_distributor', array('disflag' => 1), array('id' => $nod2['id']));
					}
				}
			}
		}
		else {
			$overdis = pdo_fetchall('SELECT id,mid FROM ' . tablename('wlmerchant_distributor') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disflag = -1 '));

			if ($overdis) {
				foreach ($overdis as $key => &$dis) {
					pdo_update('wlmerchant_distributor', array('disflag' => 1), array('id' => $dis['id']));
				}
			}
		}

		$rushrefund = pdo_fetchall('SELECT disorderid,id FROM ' . tablename('wlmerchant_rush_order') . 'WHERE status = 7 AND disorderid > 0 AND redisstatus = 0 ORDER BY id DESC LIMIT 0,20 ');

		if ($rushrefund) {
			foreach ($rushrefund as $key => $rush) {
				pdo_update('wlmerchant_disorder', array('status' => 3), array('id' => $rush['disorderid']));
				pdo_update('wlmerchant_rush_order', array('redisstatus' => 1), array('id' => $rush['id']));
			}
		}

		$orderrefund = pdo_fetchall('SELECT disorderid,id FROM ' . tablename('wlmerchant_order') . 'WHERE status = 7 AND disorderid > 0 AND redisstatus = 0 ORDER BY id DESC LIMIT 0,20 ');

		if ($orderrefund) {
			foreach ($orderrefund as $key => $order) {
				pdo_update('wlmerchant_disorder', array('status' => 3), array('id' => $order['disorderid']));
				pdo_update('wlmerchant_order', array('redisstatus' => 1), array('id' => $order['id']));
			}
		}
	}

	static public function getgzqrcode($mid)
	{
		global $_W;
		$qrid = pdo_getcolumn(PDO_NAME . 'qrcode', array('uniacid' => $_W['uniacid'], 'sid' => $mid, 'type' => 1, 'status' => 1), 'qrid');
		$qrcode = pdo_get('qrcode', array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'status' => 1, 'type' => 'scene', 'id' => $qrid));

		if (0 < $qrcode['expire']) {
			$createTime = $qrcode['createtime'];
			$expireTime = $qrcode['expire'];
			$endTime = $createTime + $expireTime - time();
		}
		else {
			$endTime = 1;
		}

		if (empty($qrid) || $endTime < 1 || empty($qrcode)) {
			if ($qrid) {
				pdo_update('qrcode', array('status' => 2), array('id' => $qrid));
				pdo_update(PDO_NAME . 'qrcode', array('status' => 2), array('qrid' => $qrid));
			}

			Weixinqrcode::createkeywords('分销关注二维码:Distribution', 'weliam_merchant_distribution');
			$posterType = Setting::wlsetting_read('distribution');
			$posterType = $posterType['posterType'];

			if ($posterType == 1) {
				$qrctype = 1;
			}
			else {
				$qrctype = 2;
			}

			$result = Weixinqrcode::createqrcode('分销关注二维码:Distribution', 'weliam_merchant_distribution', 1, $qrctype, -1, '分销关注二维码');

			if (!is_error($result)) {
				$qrid = $result;
				pdo_update(PDO_NAME . 'qrcode', array('sid' => $mid), array('uniacid' => $_W['uniacid'], 'qrid' => $qrid));
			}
		}

		$qrurl = pdo_get('qrcode', array('id' => $qrid, 'uniacid' => $_W['uniacid']), array('url', 'ticket'));
		return $qrurl;
	}

	static public function Processor($message)
	{
		global $_W;

		if (strtolower($message['msgtype']) == 'event') {
			$returnmess = array();
			$qrid = Weixinqrcode::get_qrid($message);
			$member = Member::mc_init_fans_info($message['from']);
			$mid = pdo_getcolumn(PDO_NAME . 'qrcode', array('uniacid' => $_W['uniacid'], 'qrid' => $qrid), 'sid');
			Distribution::addJunior($mid, $member['id']);
			$base = Setting::wlsetting_read('distribution');
			$returnmess[] = array('title' => urlencode($base['gztitle']), 'description' => urlencode($base['gzdesc']), 'picurl' => tomedia($base['gzthumb']), 'url' => app_url('distribution/disappbase/index', array('invitid' => $mid, 'qrentry' => 1)));
			Weixinqrcode::send_news($returnmess, $message);
		}
	}

	/**
     * Comment: 根据条件获取分销结算收益信息
     * Author: zzw
     * Date: 2019/7/16 13:55
     * @param $where
     * @return mixed
     */
	static public function getDisOrder($where = '', $field = '*')
	{
		global $_W;
		global $_GPC;
		!empty($where) && ($where .= ' AND ');
		$where .= ' a.uniacid = ' . $_W['uniacid'] . ' ';
		$sql = 'SELECT ' . $field . ' FROM ' . tablename(PDO_NAME . 'disorder') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'disdetail') . ' b ON a.id = b.disorderid ';
		!empty($where) && ($sql .= ' WHERE ' . $where . ' ');
		$result = pdo_fetchall($sql);
		return $result;
	}

	/**
     * Comment: 获取分销商提现申请信息状态列表
     * Author: zzw
     * Date: 2019/7/24 9:21
     * @return array
     */
	static public function getCashWithdrawalStateList()
	{
		$list = array(
			array(
				'title'  => '待审核',
				'status' => array(2, 6, 7)
			),
			array(
				'title'  => '待打款',
				'status' => array(3, 8)
			),
			array(
				'title'  => '已完成',
				'status' => array(4, 5, 9)
			),
			array(
				'title'  => '未通过',
				'status' => array(-1, 10, 11)
			)
		);
		return $list;
	}

	/**
     * Comment: 根据状态值 获取当前状态的详细信息
     * Author: zzw
     * Date: 2019/7/24 9:29
     * @param $status
     * @return bool
     */
	static public function getStatusDetailInfo($status)
	{
		$list = self::getCashWithdrawalStateList();
		$info = array('title' => '状态错误', 'status' => $status);

		foreach ($list as $k => $v) {
			if (in_array($status, $v['status'])) {
				$info = $v;
			}
		}

		return $info;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
