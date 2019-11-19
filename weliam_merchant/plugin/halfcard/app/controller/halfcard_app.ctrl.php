<?php
//dezend by http://www.sucaihuo.com/
class Halfcard_app_WeliamController
{
	public function __construct()
	{
		global $_W;

		if (empty($_W['wlsetting']['halfcard']['status'])) {
			header('location:' . app_url('dashboard/home/index'));
		}
	}

	public function halfcardList()
	{
		global $_W;
		global $_GPC;
		header('location:' . app_url('halfcard/halfcard_app/userhalfcard'));
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $_W['wlsetting']['halfcard']['text']['halfcardtext'] . '商户 - ' . $_W['wlsetting']['base']['name'] : $_W['wlsetting']['halfcard']['text']['halfcardtext'] . '商户';
		$week = date('w');
		$day = date('m-j');

		if ($week == 0) {
			$week = 7;
		}

		$week1 = date('w', strtotime('+1 day'));
		$day1 = date('m-j', strtotime('+1 day'));
		$week2 = date('w', strtotime('+2 day'));
		$day2 = date('m-j', strtotime('+2 day'));
		$week3 = date('w', strtotime('+3 day'));
		$day3 = date('m-j', strtotime('+3 day'));
		$week4 = date('w', strtotime('+4 day'));
		$day4 = date('m-j', strtotime('+4 day'));
		$week5 = date('w', strtotime('+5 day'));
		$day5 = date('m-j', strtotime('+5 day'));

		if ($week1 == 0) {
			$week1 = 7;
		}

		if ($week2 == 0) {
			$week2 = 7;
		}

		if ($week3 == 0) {
			$week3 = 7;
		}

		if ($week4 == 0) {
			$week4 = 7;
		}

		if ($week5 == 0) {
			$week5 = 7;
		}

		$categorys = pdo_fetchall('SELECT name,id FROM ' . tablename('wlmerchant_category_store') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND parentid = 0 AND enabled = 1 ORDER BY displayorder DESC'));

		if ($base['halfcardtype'] == 1) {
			$halfcards = pdo_getall('wlmerchant_halfcardmember', array('mid' => $_W['mid'], 'uniacid' => $_W['uniacid']));
		}
		else {
			$halfcards = pdo_getall('wlmerchant_halfcardmember', array('mid' => $_W['mid'], 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));
		}

		$advs = pdo_getall('wlmerchant_adv', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'enabled' => 1, 'type' => 3));
		if ($base['share_title'] || $base['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];

			if ($base['share_title']) {
				$title = $base['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[系统名称]', $sysname, $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}
			else {
				$_W['wlsetting']['share']['share_title'] = !empty($base['share_title']) ? $base['share_title'] : $_W['wlsetting']['share']['share_title'];
			}

			if ($base['share_desc']) {
				$desc = $base['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[系统名称]', $sysname, $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
			else {
				$_W['wlsetting']['share']['share_desc'] = !empty($base['share_desc']) ? $base['share_desc'] : $_W['wlsetting']['share']['share_desc'];
			}
		}
		else {
			$_W['wlsetting']['share']['share_title'] = !empty($base['share_title']) ? $base['share_title'] : $_W['wlsetting']['share']['share_title'];
			$_W['wlsetting']['share']['share_desc'] = !empty($base['share_desc']) ? $base['share_desc'] : $_W['wlsetting']['share']['share_desc'];
		}

		$_W['wlsetting']['share']['share_image'] = !empty($base['share_image']) ? $base['share_image'] : $_W['wlsetting']['share']['share_image'];
		include wl_template('halfcardhtml/halfcardlist');
	}

	public function todaliylist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '非' . $_W['wlsetting']['halfcard']['text']['halfcardtext'] . '日 - ' . $_W['wlsetting']['base']['name'] : '非' . $_W['wlsetting']['halfcard']['text']['halfcardtext'] . '日';

		if (!empty($_GPC['cid'])) {
			$cid = intval($_GPC['cid']);
			$cname = pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $cid), 'name');
		}

		if (!empty($_GPC['pid'])) {
			$pid = intval($_GPC['pid']);
			$cname = pdo_getcolumn(PDO_NAME . 'category_store', array('id' => $pid), 'name');
		}

		include wl_template('halfcardhtml/todaliylist');
	}

	public function getstore()
	{
		global $_W;
		global $_GPC;
		$base = Setting::wlsetting_read('halfcard');
		$plugin = unserialize($base['plugin']);
		$lng = !empty($_GPC['lng']) ? $_GPC['lng'] : 0;
		$lat = !empty($_GPC['lat']) ? $_GPC['lat'] : 0;
		$dayflag = $_GPC['dayflag'] ? $_GPC['dayflag'] : 0;
		$cateid = $_GPC['cateid'];
		$cardid = $_GPC['cardid'];
		$pindex = $_GPC['pindex'];
		$typeid = $_GPC['typeid'] ? $_GPC['typeid'] : 1;
		$where['uniacid'] = $_W['uniacid'];
		$where['aid'] = $_W['aid'];
		$where['status'] = 1;
		$card = pdo_get('wlmerchant_halfcardmember', array('id' => $cardid), array('createtime', 'levelid'));
		if ($typeid == 1 || $typeid == 5) {
			$halflists = pdo_getslice(PDO_NAME . 'halfcardlist', $where, '', $total, array(), '', 'sort DESC');
			$where['aid'] = 0;
			$where['type'] = 1;
			$halflists2 = pdo_getslice(PDO_NAME . 'halfcardlist', $where, '', $total, array(), '', 'sort DESC');
			$halfcaid = intval($_GPC['halfcaid']);

			if ($halflists2) {
				$halflists = array_merge($halflists2, $halflists);
			}
		}
		else if ($typeid == 2) {
			if ($_GPC['gpsort']) {
				$plugin['lbsort'] = $_GPC['gpsort'];
			}

			if ($_GPC['gplimit']) {
				$gpLimit = array(1, $_GPC['gplimit']);
			}

			$packcaid = intval($_GPC['packcaid']);
			$where['listshow'] = 0;
			$halflists = pdo_getslice(PDO_NAME . 'package', $where, $gpLimit, $total, array(), '', 'sort DESC');
			$where['aid'] = 0;
			$where['type'] = 1;
			$halflists2 = pdo_getslice(PDO_NAME . 'package', $where, $gpLimit, $total, array(), '', 'sort DESC');

			if ($halflists2) {
				$halflists = array_merge($halflists2, $halflists);
			}
		}
		else if ($typeid == 3) {
			$rushlist = pdo_getslice(PDO_NAME . 'rush_activity', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'status' => 2, 'vipstatus !=' => 0), '', $total, array('id', 'sid', 'name', 'price', 'oldprice', 'vipprice', 'num', 'levelnum', 'status', 'starttime', 'endtime', 'follow', 'tag', 'thumb', 'op_one_limit', 'vipstatus'), '', 'sort DESC');
		}
		else if ($typeid == 4) {
			$couponlist = pdo_getslice(PDO_NAME . 'couponlist', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'status' => 1, 'vipstatus !=' => 0), '', $total, array(), '', 'indexorder DESC');
			$couponlist2 = pdo_getslice(PDO_NAME . 'couponlist', array('uniacid' => $_W['uniacid'], 'aid' => 0, 'status' => 1, 'extflag' => 1), '', $total, array(), '', 'indexorder DESC');

			if ($couponlist2) {
				$couponlist = array_merge($couponlist2, $couponlist);
			}
		}
		else if ($typeid == 6) {
			$grouponlist = pdo_getslice(PDO_NAME . 'groupon_activity', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'status' => 2, 'vipstatus !=' => 0), '', $total, array(), '', 'sort DESC');
		}
		else {
			if ($typeid == 7) {
				$consumlist = pdo_getslice(PDO_NAME . 'consumption_goods', array('uniacid' => $_W['uniacid'], 'status' => 1, 'vipstatus !=' => 0), '', $total, array(), '', 'displayorder DESC');
			}
		}

		if ($halflists && ($typeid == 1 || $typeid == 2 || $typeid == 5)) {
			foreach ($halflists as $key => &$half) {
				if ($half['merchantid']) {
					$store = pdo_get('wlmerchant_merchantdata', array('id' => $half['merchantid']), array('storename', 'payonline', 'logo', 'onelevel', 'id', 'location', 'enabled'));
				}
				else {
					if ($typeid == 1) {
						$store['onelevel'] = $packcaid;
					}
					else {
						$store['onelevel'] = $halfcaid;
					}

					$store['enabled'] = 1;
				}

				if ($typeid == 2 && $packcaid && $store['onelevel'] != $packcaid) {
					unset($halflists[$key]);
				}
				else {
					if (($typeid == 1 || $typeid == 5) && $halfcaid && $store['onelevel'] != $halfcaid) {
						unset($halflists[$key]);
					}
					else {
						if ($store['payonline']) {
							$half['payonline'] = $half['merchantid'];
						}
						else {
							$half['payonline'] = 0;
						}

						if ($half['merchantid']) {
							$half['storename'] = $store['storename'];
							$half['logo'] = tomedia($store['logo']);
							$half['storeid'] = $store['id'];
							$location = unserialize($store['location']);
							$half['distance'] = Store::getdistance($lng, $lat, $location['lng'], $location['lat']);
						}
						else {
							$info = unserialize($half['extinfo']);
							$half['storename'] = $info['storename'];
							$half['logo'] = tomedia($info['storelogo']);
							$half['distance'] = 999999999;
						}
					}
				}

				$level = unserialize($half['level']);
				if ($level && $cardid) {
					if (!in_array($card['levelid'], $level)) {
						unset($halflists[$key]);
					}
				}

				if ($store['enabled'] != 1) {
					unset($halflists[$key]);
				}
			}

			if ($typeid == 1 && $_GPC['indexflag']) {
				$agentplugin = Setting::agentsetting_read('pluginlist');

				switch ($agentplugin['wzsort']) {
				case '1':
					$sort_key = 'createtime';
					$sort_order = SORT_DESC;
					break;

				case '2':
					$sort_key = 'distance';
					$sort_order = SORT_ASC;
					break;

				case '3':
					$sort_key = 'sort';
					$sort_order = SORT_DESC;
					break;

				case '4':
					$sort_key = 'pv';
					$sort_order = SORT_DESC;
					break;

				default:
					$sort_key = 'distance';
					$sort_order = SORT_ASC;
					break;
				}
			}
			else {
				if ($typeid == 1 && empty($_GPC['indexflag'])) {
					switch ($plugin['zksort']) {
					case '1':
						$sort_key = 'createtime';
						$sort_order = SORT_DESC;
						break;

					case '2':
						$sort_key = 'distance';
						$sort_order = SORT_ASC;
						break;

					case '3':
						$sort_key = 'sort';
						$sort_order = SORT_DESC;
						break;

					case '4':
						$sort_key = 'pv';
						$sort_order = SORT_DESC;
						break;

					default:
						$sort_key = 'distance';
						$sort_order = SORT_ASC;
						break;
					}
				}
				else if ($typeid == 2) {
					switch ($plugin['lbsort']) {
					case '1':
						$sort_key = 'createtime';
						$sort_order = SORT_DESC;
						break;

					case '2':
						$sort_key = 'distance';
						$sort_order = SORT_ASC;
						break;

					case '3':
						$sort_key = 'sort';
						$sort_order = SORT_DESC;
						break;

					case '4':
						$sort_key = 'pv';
						$sort_order = SORT_DESC;
						break;

					default:
						$sort_key = 'distance';
						$sort_order = SORT_ASC;
						break;
					}
				}
				else {
					if ($typeid == 5) {
						switch ($plugin['przksort']) {
						case '1':
							$sort_key = 'createtime';
							$sort_order = SORT_DESC;
							break;

						case '2':
							$sort_key = 'distance';
							$sort_order = SORT_ASC;
							break;

						case '3':
							$sort_key = 'sort';
							$sort_order = SORT_DESC;
							break;

						case '4':
							$sort_key = 'pv';
							$sort_order = SORT_DESC;
							break;

						default:
							$sort_key = 'distance';
							$sort_order = SORT_ASC;
							break;
						}
					}
				}
			}

			$halflists = Store::wl_sort($halflists, $sort_key, $sort_order);
			if ($typeid == 1 && $_GPC['indexflag']) {
				$plugin = Setting::agentsetting_read('pluginlist');
				$limit = $plugin['wzlimit'] ? $plugin['wzlimit'] : 10;
				$halflists = array_slice($halflists, 0, $limit);
			}

			if ($typeid == 1) {
				$weekflag = date('w', strtotime('+' . $dayflag . ' day'));
				$dayflag2 = date('j', strtotime('+' . $dayflag . ' day'));
				$newlocations = array();

				if ($weekflag == 0) {
					$weekflag = 7;
				}

				foreach ($halflists as $wlf => $half2) {
					if ($half2['type']) {
						$half2['discount'] = $half2['activediscount'];
						$newlocations[] = $half2;
					}
					else if ($_GPC['indexflag']) {
						if ($half2['datestatus'] == 1) {
							$half2['week'] = unserialize($half2['week']);

							if (in_array($weekflag, $half2['week'])) {
								$half2['discount'] = $half2['activediscount'];
								$newlocations[] = $half2;
							}
							else {
								if ($half2['daily']) {
									$newlocations[] = $half2;
								}
							}
						}
						else if ($half2['datestatus'] == 2) {
							$half2['day'] = unserialize($half2['day']);

							if (in_array($dayflag2, $half2['day'])) {
								$half2['discount'] = $half2['activediscount'];
								$newlocations[] = $half2;
							}
							else {
								if ($half2['daily']) {
									$newlocations[] = $half2;
								}
							}
						}
						else {
							if ($half2['datestatus'] == 3) {
								$newlocations[] = $half2;
							}
						}
					}
					else if ($half2['datestatus'] == 1) {
						$half2['week'] = unserialize($half2['week']);
						if (in_array($weekflag, $half2['week']) || $half2['daily']) {
							if ($typeid == 1 && $plugin['przkstatus']) {
								if (in_array($weekflag, $half2['week'])) {
									$half2['discount'] = $half2['activediscount'];
									$newlocations[] = $half2;
								}
							}
							else {
								if ($typeid == 1 && empty($plugin['przkstatus'])) {
									$half2['discount'] = in_array($weekflag, $half2['week']) ? $half2['activediscount'] : $half2['discount'];
									$newlocations[] = $half2;
								}
								else {
									if (!in_array($weekflag, $half2['week'])) {
										$newlocations[] = $half2;
									}
								}
							}
						}
					}
					else if ($half2['datestatus'] == 2) {
						$half2['day'] = unserialize($half2['day']);
						if (in_array($dayflag2, $half2['day']) || $half2['daily']) {
							if ($typeid == 1 && $plugin['przkstatus'] && empty($_GPC['indexflag'])) {
								if (in_array($dayflag2, $half2['day'])) {
									$half2['discount'] = $half2['activediscount'];
									$newlocations[] = $half2;
								}
							}
							else {
								if ($typeid == 1 && (empty($plugin['przkstatus']) || $_GPC['indexflag'])) {
									$half2['discount'] = in_array($dayflag2, $half2['day']) ? $half2['activediscount'] : $half2['discount'];
									$newlocations[] = $half2;
								}
								else {
									if (!in_array($dayflag2, $half2['day'])) {
										$newlocations[] = $half2;
									}
								}
							}
						}
					}
					else {
						if ($half2['datestatus'] == 3 && $typeid == 5) {
							$newlocations[] = $half2;
						}
					}
				}
			}
			else {
				foreach ($halflists as $k => &$half) {
					if ($half['datestatus'] == 4) {
						$begin = time() - 31536000;
					}
					else if ($half['datestatus'] == 3) {
						$begin = mktime(0, 0, 0, date('m'), 1, date('Y'));
					}
					else if ($half['datestatus'] == 2) {
						$begin = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
					}
					else {
						$begin = 0;
					}

					if ($begin < $card['createtime'] && $half['resetswitch']) {
						$begin = $card['createtime'];
					}

					$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE cardid = ' . $cardid . ' AND activeid = ' . $half['id'] . ' AND merchantid = ' . $half['merchantid'] . ' AND type = 2 AND usetime > ' . $begin));
					$half['surplus'] = sprintf('%.0f', $half['usetimes'] - $times);
				}

				$newlocations = $halflists;
			}

			foreach ($newlocations as $key => $value) {
				if (!empty($value['distance'])) {
					if (1000 < $value['distance']) {
						$newlocations[$key]['distance'] = floor($value['distance'] / 1000 * 10) / 10 . 'km';
					}
					else {
						$newlocations[$key]['distance'] = round($value['distance']) . 'm';
					}
				}
			}

			exit(json_encode($newlocations));
		}
		else {
			if ($rushlist && $typeid == 3) {
				$newlocations = array();

				foreach ($rushlist as $key => &$rush) {
					$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $rush['sid']), array('storename', 'logo', 'location', 'enabled'));

					if ($merchant['enabled'] == 1) {
						$location = unserialize($merchant['location']);
						$rush['distance'] = Store::getdistance($lng, $lat, $location['lng'], $location['lat']);
						$rush['storename'] = $merchant['storename'];
						$rush['storelogo'] = tomedia($merchant['logo']);
						$rush['thumb'] = tomedia($rush['thumb']);
						$rush['endtime'] = date('Y-m-d H:i', $rush['endtime']);
						$rush['href'] = app_url('rush/home/detail', array('id' => $rush['id']));

						if ($rush['vipstatus'] == 1) {
							$rush['price'] = $rush['vipprice'];
						}

						$newlocations[] = $rush;
					}
				}

				switch ($plugin['qgsort']) {
				case '1':
					$sort_key = 'id';
					$sort_order = SORT_DESC;
					break;

				case '2':
					$sort_key = 'distance';
					$sort_order = SORT_ASC;
					break;

				case '3':
					$sort_key = 'sort';
					$sort_order = SORT_DESC;
					break;

				case '4':
					$sort_key = 'pv';
					$sort_order = SORT_DESC;
					break;

				default:
					$sort_key = 'id';
					$sort_order = SORT_DESC;
					break;
				}

				$sortArr = array_column($newlocations, $sort_key);
				array_multisort($sortArr, $sort_order, $newlocations);
				exit(json_encode($newlocations));
			}
			else {
				if ($couponlist && $typeid == 4) {
					$newlocations = array();

					foreach ($couponlist as $key => &$asd) {
						if ($asd['merchantid']) {
							$merchant = pdo_get(PDO_NAME . 'merchantdata', array('id' => $asd['merchantid']), array('storename', 'location', 'enabled'));
							$asd['storename'] = $merchant['storename'];
							$location = unserialize($merchant['location']);
							$asd['distance'] = Store::getdistance($lng, $lat, $location['lng'], $location['lat']);
							$asd['logo'] = tomedia($asd['logo']);
							$asd['indeximg'] = tomedia($asd['indeximg']);

							if ($asd['is_charge'] == 1) {
								if ($asd['vipstatus'] == 2) {
									$asd['charge'] = $asd['vipprice'];
									$asd['oldcharge'] = $asd['price'];
								}
								else {
									$asd['charge'] = $asd['price'];
								}
							}
							else {
								$asd['charge'] = '免费';
							}

							$asd['href'] = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $asd['id']));
							$asd['rate'] = round($asd['surplus'] / $asd['quantity'] * 100);
						}
						else {
							$merchant['enabled'] = 1;
							$asd['distance'] = 999999999;
							$extinfo = unserialize($asd['extinfo']);
							$asd['storename'] = $extinfo['storename'];
							$asd['logo'] = tomedia($asd['logo']);
						}

						if (0 < $asd['vipstatus'] && $merchant['enabled'] == 1) {
							$newlocations[] = $asd;
						}
					}

					switch ($plugin['kqsort']) {
					case '1':
						$sort_key = 'createtime';
						$sort_order = SORT_DESC;
						break;

					case '2':
						$sort_key = 'distance';
						$sort_order = SORT_ASC;
						break;

					case '3':
						$sort_key = 'sort';
						$sort_order = SORT_DESC;
						break;

					case '4':
						$sort_key = 'pv';
						$sort_order = SORT_DESC;
						break;

					default:
						$sort_key = 'distance';
						$sort_order = SORT_ASC;
						break;
					}

					$newlocations = Store::wl_sort($newlocations, $sort_key, $sort_order);
					exit(json_encode($newlocations));
				}
				else {
					if ($grouponlist && $typeid == 6) {
						$newlocations = array();

						foreach ($grouponlist as $key => &$group) {
							$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $group['sid']), array('storename', 'logo', 'location', 'enabled'));

							if ($merchant['enabled'] == 1) {
								$location = unserialize($merchant['location']);
								$group['distance'] = Store::getdistance($lng, $lat, $location['lng'], $location['lat']);
								$group['storename'] = $merchant['storename'];
								$group['storelogo'] = tomedia($merchant['logo']);
								$group['thumbs'] = unserialize($group['thumbs']);
								$group['thumb'] = tomedia($group['thumbs'][0]);
								$group['href'] = app_url('groupon/grouponapp/groupondetail', array('cid' => $group['id']));

								if ($group['vipstatus'] == 1) {
									$group['price'] = $group['vipprice'];
								}

								$group['endtime'] = date('Y-m-d H:i', $group['endtime']);
								$newlocations[] = $group;
							}
						}

						switch ($plugin['tgsort']) {
						case '1':
							$sort_key = 'createtime';
							$sort_order = SORT_DESC;
							break;

						case '2':
							$sort_key = 'distance';
							$sort_order = SORT_ASC;
							break;

						case '3':
							$sort_key = 'sort';
							$sort_order = SORT_DESC;
							break;

						case '4':
							$sort_key = 'pv';
							$sort_order = SORT_DESC;
							break;

						default:
							$sort_key = 'distance';
							$sort_order = SORT_ASC;
							break;
						}

						$newlocations = Store::wl_sort($newlocations, $sort_key, $sort_order);
						exit(json_encode($newlocations));
					}
					else {
						if ($consumlist && $typeid == 7) {
							$newlocations = array();

							foreach ($consumlist as $key => &$sum) {
								$sum['thumb'] = tomedia($sum['thumb']);
								$sum['href'] = app_url('consumption/goods/goods_detail', array('id' => $sum['id']), false);

								if ($sum['vipstatus'] == 1) {
									$sum['use_credit1'] = $sum['vipcredit1'];
									$sum['use_credit2'] = $sum['vipcredit2'];
								}

								$newlocations[] = $sum;
							}

							switch ($plugin['jfsort']) {
							case '1':
								$sort_key = 'id';
								$sort_order = SORT_DESC;
								break;

							case '3':
								$sort_key = 'displayorder';
								$sort_order = SORT_DESC;
								break;

							case '4':
								$sort_key = 'pv';
								$sort_order = SORT_DESC;
								break;

							default:
								$sort_key = 'id';
								$sort_order = SORT_DESC;
								break;
							}

							$newlocations = Store::wl_sort($newlocations, $sort_key, $sort_order);
							exit(json_encode($newlocations));
						}
						else {
							exit(json_encode(0));
						}
					}
				}
			}
		}
	}

	public function getdailystore()
	{
		global $_W;
		global $_GPC;
		$lng = !empty($_GPC['lng']) ? $_GPC['lng'] : '105.015615';
		$lat = !empty($_GPC['lat']) ? $_GPC['lat'] : '31.57425';
		$parm = array('uniacid' => $_W['uniacid'], 'status' => 2, 'enabled' => 1, 'aid' => $_W['aid']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$today = strtotime(date('Y-m-d', time()));
		$halfcardlist = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halfcardlist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid']));

		if ($halfcardlist) {
			$ids = '(';

			foreach ($halfcardlist as $key => $v) {
				if ($key == 0) {
					$ids .= $v['merchantid'];
				}
				else {
					$ids .= ',' . $v['merchantid'];
				}
			}

			$ids .= ')';
			$parm['id#'] = $ids;
		}
		else {
			$parm['id#'] = '(0)';
		}

		if (!empty($_GPC['cid'])) {
			$parm['onelevel'] = intval($_GPC['cid']);
		}

		if (!empty($_GPC['pid'])) {
			$parm['twolevel'] = intval($_GPC['pid']);
		}

		if (!empty($_GPC['distid'])) {
			$parm['distid'] = intval($_GPC['distid']);
		}

		$locations = Halfcard::getNumstore('*', $parm, 'ID DESC', 0, 0, 0);
		$locations = $locations[0];

		if ($locations) {
			$locations = Halfcard::getstores($locations, $lng, $lat);

			foreach ($locations as $key => &$v) {
				$active = pdo_get(PDO_NAME . 'halfcardlist', array('uniacid' => $_W['uniacid'], 'merchantid' => $v['id'], 'status' => 1));

				if ($active['datestatus'] == 1) {
					$week = date('w');

					if ($week == 0) {
						$week = 7;
					}

					$hweek = unserialize($active['week']);

					foreach ($hweek as $key => $value) {
						if ($week <= $value) {
							$needweek = $value;
							break;
						}
					}

					if (empty($needweek)) {
						$needweek = $hweek[0];
					}

					if ($week <= $needweek) {
						$needtime = $today + abs($needweek - $week) * 24 * 60 * 60;
					}
					else {
						$needtime = $today + abs($needweek + 7 - $week) * 24 * 60 * 60;
					}
				}
				else {
					$day = date('j');
					$hday = unserialize($active['day']);

					foreach ($hday as $key => $value) {
						if ($day <= $value) {
							$needday = $value;
							break;
						}
					}

					if (empty($needday)) {
						$needday = $hday[0];
					}

					if ($day <= $needday) {
						$needtime = $today + abs($needday - $day) * 24 * 60 * 60;
					}
					else {
						$needtime = mktime(0, 0, 0, date('m') + 1, 1, date('Y')) + abs($needday - 1) * 24 * 60 * 60;
					}
				}

				if ($needtime < time()) {
					$flag = 0;
				}
				else {
					$flag = ceil(($needtime - time()) / (24 * 3600));
				}

				$active['logo'] = tomedia($v['logo']);
				$active['href'] = app_url('halfcard/halfcard_app/halfcarddetail', array('id' => $active['id'], 'flag' => $flag));
				$v['active'] = $active;
				$v['storehours'] = unserialize($v['storehours']);
				$newlocations[] = $v;
			}

			exit(json_encode($newlocations));
		}
		else {
			exit(json_encode(0));
		}
	}

	public function halfcarddetail()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '特权详情 - ' . $_W['wlsetting']['base']['name'] : '特权详情';
		$playtype = 1;
		$id = $_GPC['id'];
		$flag = $_GPC['flag'];
		$dayflag = $_GPC['dayflag'];

		if (!$id) {
			wl_message('参数错误，请重试', app_url('halfcard/halfcard_app/userhalfcard'), 'er	ror');
		}

		$halfcard = pdo_get('wlmerchant_halfcardlist', array('id' => $id));
		$halfcard['pv'] = $halfcard['pv'] + 1;
		pdo_update('wlmerchant_halfcardlist', array('pv' => $halfcard['pv']), array('id' => $id));

		if ($halfcard['type']) {
			$extinfo = unserialize($halfcard['extinfo']);
			$merchant['logo'] = $extinfo['storelogo'];
			$merchant['storename'] = $extinfo['storename'];
			$levels = unserialize($halfcard['level']);
			$discount = $halfcard['activediscount'];

			if ($levels) {
				$halfcardflag = Halfcard::checklevel($_W['mid'], $levels);
			}
			else {
				$halfcardflag = Member::checkhalfmember();
			}
		}
		else {
			$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $halfcard['merchantid']));
			$storehours = unserialize($merchant['storehours']);
			pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET pv=pv+1 WHERE id = ' . $halfcard['merchantid']));
			$usetimes = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $id . ' AND type = 1'));
			$heads = pdo_fetchall('SELECT mid FROM ' . tablename('wlmerchant_timecardrecord') . ('WHERE activeid = ' . $id . ' AND type = 1 ORDER BY id DESC limit 0,5'));

			foreach ($heads as $key => &$he) {
				$he['avatar'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $he['mid']), 'avatar');
			}

			$location = unserialize($merchant['location']);
			if ($_SESSION['lng'] && $_SESSION['lat']) {
				$distance = Store::getdistance($_SESSION['lng'], $_SESSION['lat'], $location['lng'], $location['lat']);

				if (1000 < $distance) {
					$distance = floor($distance / 1000 * 10) / 10 . 'km';
				}
				else {
					$distance = round($distance) . 'm';
				}
			}
			else {
				$distance = '未定位';
			}

			if ($halfcard['datestatus'] == 1) {
				$weeks = unserialize($halfcard['week']);
				$today = date('w');

				if ($today == 0) {
					$today = 7;
				}

				if (in_array($today, $weeks)) {
					$discount = $halfcard['activediscount'];

					if ($halfcard['timeslimit']) {
						$limitflag = 1;
						$zerotime = strtotime(date('Y-m-d'), time());
						$times2 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfcard['id'] . ' AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND freeflag = 1'));
						$surplus = $halfcard['timeslimit'] - $times2;
					}
				}
				else {
					$discount = $halfcard['discount'];
				}
			}
			else {
				$days = unserialize($halfcard['day']);
				$today = date('j');

				if (in_array($today, $days)) {
					$discount = $halfcard['activediscount'];

					if ($halfcard['timeslimit']) {
						$limitflag = 1;
						$zerotime = strtotime(date('Y-m-d'), time());
						$times2 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfcard['id'] . ' AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND freeflag = 1'));
						$surplus = $halfcard['timeslimit'] - $times2;
					}
				}
				else {
					$discount = $halfcard['discount'];
				}
			}

			$halfcardflag = Member::checkhalfmember();

			if ($halfcardflag) {
				if ($playtype == 2) {
					$surplustimes = $halfcard['usetimes'];
				}

				$useurl = app_url('halfcard/halfcard_app/usetimescard', array('cardid' => $halfcardflag));
			}
		}

		$_W['wlsetting']['share']['share_title'] = !empty($halfcard['title']) ? $halfcard['title'] : $merchant['storename'];
		$_W['wlsetting']['share']['share_desc'] = !empty($halfcard['limit']) ? $halfcard['limit'] : $base['share_desc'];
		$_W['wlsetting']['share']['share_image'] = !empty($merchant['logo']) ? $merchant['logo'] : $base['share_image'];
		include wl_template('halfcardhtml/halfcarddetail');
	}

	public function packagedetail()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '礼包详情 - ' . $_W['wlsetting']['base']['name'] : '礼包详情';
		$playtype = 2;
		$id = $_GPC['id'];
		$cardid = $_GPC['cardid'];
		$card = pdo_get('wlmerchant_halfcardmember', array('id' => $cardid), array('expiretime', 'createtime'));

		if (!$id) {
			wl_message('参数错误，请重试', app_url('halfcard/halfcard_app/userhalfcard'), 'error');
		}

		$halfcard = pdo_get('wlmerchant_package', array('id' => $id));

		if ($halfcard['type']) {
			$extinfo = unserialize($halfcard['extinfo']);
			$merchant['logo'] = $extinfo['storelogo'];
			$merchant['storename'] = $extinfo['storename'];
			$levels = unserialize($halfcard['level']);

			if ($levels) {
				$halfcardflag = Halfcard::checklevel($_W['mid'], $levels);
			}
			else {
				$halfcardflag = Member::checkhalfmember();
			}
		}
		else {
			$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $halfcard['merchantid']));
			$storehours = unserialize($merchant['storehours']);
			pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET pv=pv+1 WHERE id = ' . $halfcard['merchantid']));
			$usetimes = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $id . ' AND type = 2'));
			$heads = pdo_fetchall('SELECT mid FROM ' . tablename('wlmerchant_timecardrecord') . ('WHERE activeid = ' . $id . ' AND type = 2 ORDER BY id DESC limit 0,5'));

			foreach ($heads as $key => &$he) {
				$he['avatar'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $he['mid']), 'avatar');
			}

			$location = unserialize($merchant['location']);
			if ($_SESSION['lng'] && $_SESSION['lat']) {
				$distance = Store::getdistance($_SESSION['lng'], $_SESSION['lat'], $location['lng'], $location['lat']);

				if (1000 < $distance) {
					$distance = floor($distance / 1000 * 10) / 10 . 'km';
				}
				else {
					$distance = round($distance) . 'm';
				}
			}
			else {
				$distance = '未定位';
			}

			$zerotime = strtotime(date('Y-m-d'), time());

			if ($halfcard['timeslimit']) {
				$limitflag = 1;
				$times2 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfcard['id'] . ' AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
				$surplus = $halfcard['timeslimit'] - $times2;
			}

			if (time() < $card['expiretime']) {
				$halfcardflag = 1;
			}
			else {
				$levels = unserialize($halfcard['level']);

				if ($levels) {
					$halfcardflag = Halfcard::checklevel($_W['mid'], $levels);
				}
				else {
					$halfcardflag = Member::checkhalfmember();
				}
			}

			if ($halfcard['datestatus'] == 4) {
				$begin = time() - 31536000;
			}
			else if ($halfcard['datestatus'] == 3) {
				$begin = mktime(0, 0, 0, date('m'), 1, date('Y'));
			}
			else if ($halfcard['datestatus'] == 2) {
				$begin = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
			}
			else {
				$begin = 0;
			}

			if ($begin < $card['createtime'] && $halfcard['resetswitch']) {
				$begin = $card['createtime'];
			}

			$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE cardid = ' . $cardid . ' AND activeid = ' . $halfcard['id'] . ' AND type = 2 AND usetime > ' . $begin));
			$surplustimes = $halfcard['usetimes'] - $times;

			if ($halfcard['oplimit']) {
				$times3 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfcard['id'] . ' AND mid = ' . $_W['mid'] . '  AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
				$opsurplu = $halfcard['oplimit'] - $times3;

				if ($opsurplu < $surplustimes) {
					$surplustimes = $opsurplu;
				}
			}

			if ($halfcard['weeklimit']) {
				$begin = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
				$timeflag = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfcard['id'] . ' AND mid = ' . $_W['mid'] . '  AND usetime > ' . $begin . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
				$opsurplu = $halfcard['weeklimit'] - $timeflag;

				if ($opsurplu < $surplustimes) {
					$surplustimes = $opsurplu;
				}
			}

			if ($halfcard['monthlimit']) {
				$begin = mktime(0, 0, 0, date('m'), 1, date('Y'));
				$timeflag = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfcard['id'] . ' AND mid = ' . $_W['mid'] . '  AND usetime > ' . $begin . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
				$opsurplu = $halfcard['monthlimit'] - $timeflag;

				if ($opsurplu < $surplustimes) {
					$surplustimes = $opsurplu;
				}
			}
		}

		$halfcard['pv'] = $halfcard['pv'] + 1;
		pdo_update('wlmerchant_package', array('pv' => $halfcard['pv']), array('id' => $id));

		if (empty($surplustimes)) {
			$surplustimes = 0;
		}

		$_W['wlsetting']['share']['share_title'] = $halfcard['title'] ? trim($halfcard['title']) : '大礼包';
		$_W['wlsetting']['share']['share_desc'] = $halfcard['limit'] ? trim($halfcard['limit']) : '大礼包';
		$_W['wlsetting']['share']['share_image'] = $merchant['logo'];
		include wl_template('halfcardhtml/halfcarddetail');
	}

	public function createqrcode()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$flag = $_GPC['flag'];
		$merchantid = $_GPC['merchantid'];
		$member = pdo_get('wlmerchant_halfcardmember', array('mid' => $_W['mid']), array('disable'));

		if ($member['disable']) {
			exit(json_encode(array('flag' => 0, 'message' => '您的特权已被禁用，请联系平台客服解决')));
		}

		$base = Setting::wlsetting_read('halfcard');
		$date = date('Ymd');
		$date2 = date('Y-m-d');
		$dayflag = date('Ymd');
		$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_halfcardrecord') . (' WHERE mid = ' . $_W['mid'] . ' AND date = \'' . $dayflag . '\' AND is_half = 1'));
		$halfcard = Halfcard::getSingleHalfcard($id, 'timeslimit');
		$timeslimit = $halfcard['timeslimit'];

		if ($timeslimit) {
			$times2 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_halfcardrecord') . (' WHERE activeid = ' . $id . ' AND date = \'' . $dayflag . '\' AND is_half = 1'));

			if ($timeslimit - 1 < $times2) {
				exit(json_encode(array('flag' => 0, 'message' => '该商户已经接待了' . $timeslimit . '次' . $_W['wlsetting']['halfcard']['text']['halfcardtext'] . '特权顾客')));
			}
		}

		if (!empty($base['daytimes']) && $base['daytimes'] - 1 < $times) {
			exit(json_encode(array('flag' => 0, 'message' => '您当日已使用过' . $base['daytimes'] . '次' . $_W['wlsetting']['halfcard']['text']['halfcardtext'] . '特权')));
		}
		else {
			$qrcode = Util::createConcode(2);
			$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'status' => 1, 'activeid' => $id, 'merchantid' => $merchantid, 'mid' => $_W['mid'], 'date' => $date, 'qrcode' => $qrcode, 'createtime' => time());

			if ($flag == 1) {
				$data['is_half'] = 1;
			}
			else {
				$data['is_half'] = 2;
			}

			$res = pdo_insert('wlmerchant_halfcardrecord', $data);
			$rid = pdo_insertid();
			$url = app_url('halfcard/halfcard_app/hexiaohalfcard', array('id' => $rid));
			$qrurl = app_url('halfcard/halfcard_app/qrcodeimg', array('url' => $url));

			if ($res) {
				exit(json_encode(array('flag' => 1, 'qrcode' => $qrcode, 'datetime' => $date2, 'qrsrc' => $qrurl)));
			}
			else {
				exit(json_encode(array('flag' => 0, 'qrcode' => $qrcode)));
			}
		}
	}

	public function userhalfcard()
	{
		global $_W;
		global $_GPC;

		if (!empty($_W['wlsetting']['base']['name'])) {
			$dys = '-' . $_W['wlsetting']['base']['name'];
		}

		if (!empty($_W['wlsetting']['halfcard']['text']['halfcardtext'])) {
			$pagetitlee = '我的' . $_W['wlsetting']['halfcard']['text']['halfcardtext'];
		}
		else {
			$pagetitlee = '我的一卡通';
		}

		if (!empty($_W['wlsetting']['halfcard']['text']['privilege'])) {
			$pagetitles = $_W['wlsetting']['halfcard']['text']['privilege'];
		}
		else {
			$pagetitles = '特权';
		}

		$pagetitle = $pagetitlee . $pagetitles . $dys;
		$base = Setting::wlsetting_read('halfcard');

		if ($base['noticestatus'] == 2) {
			$recodes = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' ORDER BY id DESC LIMIT 10'));

			if ($recodes) {
				$recode = array();

				foreach ($recodes as $key => &$re) {
					$re['day'] = round(($re['expiretime'] - $re['createtime']) / 3600 / 24);
					$recode[$key]['msg'] = $re['username'] . '开通了' . $re['day'] . '天会员';
					$head = pdo_getcolumn('wlmerchant_member', array('id' => $re['mid']), 'avatar');
					$recode[$key]['imgurl'] = tomedia($head);
				}
			}

			$recode = json_encode($recode);
		}

		if ($base['halfcardtype'] == 1) {
			$halfcards = pdo_getall('wlmerchant_halfcardmember', array('mid' => $_W['mid'], 'uniacid' => $_W['uniacid']), '', '', 'expiretime DESC');
		}
		else {
			$halfcards = pdo_getall('wlmerchant_halfcardmember', array('mid' => $_W['mid'], 'uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), '', '', 'expiretime DESC');
		}

		if (!empty($halfcards)) {
			foreach ($halfcards as $key => &$card) {
				if ($card['expiretime'] < time() && $key) {
					pdo_delete('wlmerchant_halfcardmember', array('id' => $card['id']));
					unset($halfcards[$key]);
				}
				else {
					$card['url'] = app_url('halfcard/halfcard_app/usetimescard', array('cardid' => $card['id']));
					$card['realno'] = pdo_getcolumn(PDO_NAME . 'halfcard_realcard', array('cardid' => $card['id']), 'cardsn');

					if ($card['levelid']) {
						$cardlevel = pdo_get(PDO_NAME . 'halflevel', array('id' => $card['levelid']), array('name', 'cardimg'));
						$card['levelname'] = $cardlevel['name'];

						if ($cardlevel['cardimg']) {
							$card['cardimg'] = $cardlevel['cardimg'];
						}
						else {
							$card['cardimg'] = $base['cardimg'];
						}
					}
					else {
						$card['levelname'] = $_W['wlsetting']['halflevel']['name'];
						$card['cardimg'] = $_W['wlsetting']['halflevel']['cardimg'] ? $_W['wlsetting']['halflevel']['cardimg'] : $base['cardimg'];
					}
				}
			}
		}

		if (!empty($halfcards) && empty($base['statisticsdiv'])) {
			$where['mid'] = $_W['mid'];
			$where['uniacid'] = $_W['uniacid'];
			$allorderprice = pdo_getcolumn('wlmerchant_timecardrecord', $where, array('SUM(ordermoney)'));
			$realprice = pdo_getcolumn('wlmerchant_timecardrecord', $where, array('SUM(realmoney)'));
			$ellipsismoney = $allorderprice - $realprice;

			if (empty($allorderprice)) {
				$allorderprice = 0;
			}

			if (empty($ellipsismoney)) {
				$ellipsismoney = 0;
			}
		}

		$plugin = unserialize($base['plugin']);
		$pluginlist[] = array('name' => '特权折扣', 'status' => $plugin['zkstatus'], 'id' => 1, 'order' => $plugin['zkorder'], 'newname' => $plugin['zkname']);
		$pluginlist[] = array('name' => '平日折扣', 'status' => $plugin['przkstatus'], 'id' => 5, 'order' => $plugin['przkorder'], 'newname' => $plugin['przkname']);
		$pluginlist[] = array('name' => '免费礼包', 'status' => $plugin['lbstatus'], 'id' => 2, 'order' => $plugin['lborder'], 'newname' => $plugin['lbname']);
		$pluginlist[] = array('name' => '尊享抢购', 'status' => $plugin['qgstatus'], 'id' => 3, 'order' => $plugin['qgorder'], 'newname' => $plugin['qgname']);
		$pluginlist[] = array('name' => '专属卡券', 'status' => $plugin['kqstatus'], 'id' => 4, 'order' => $plugin['kqorder'], 'newname' => $plugin['kqname']);
		$pluginlist[] = array('name' => '特惠团购', 'status' => $plugin['tgstatus'], 'id' => 6, 'order' => $plugin['tgorder'], 'newname' => $plugin['tgname']);
		$pluginlist[] = array('name' => '积分商品', 'status' => $plugin['jfstatus'], 'id' => 7, 'order' => $plugin['jforder'], 'newname' => $plugin['jfname']);
		$i = 0;

		while ($i < count($pluginlist) - 1) {
			$j = 0;

			while ($j < count($pluginlist) - 1 - $i) {
				if ($pluginlist[$j]['order'] < $pluginlist[$j + 1]['order']) {
					$temp = $pluginlist[$j + 1];
					$pluginlist[$j + 1] = $pluginlist[$j];
					$pluginlist[$j] = $temp;
				}

				++$j;
			}

			++$i;
		}

		$first = $_GPC['tabid'] ? $_GPC['tabid'] : $pluginlist[0]['id'];
		$cates = pdo_getall('wlmerchant_category_store', array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'parentid' => 0, 'enabled' => 1), array('id', 'name'));
		if ($plugin['lbstatus'] && empty($base['packagecate'])) {
			$packcate = $cates;
		}

		if (($plugin['zkstatus'] || $plugin['przkstatus']) && $base['halfcate']) {
			$halfcate = $cates;
		}

		$recommend = pdo_fetchall('SELECT vipprice,name,thumb,id,oldprice FROM ' . tablename('wlmerchant_rush_activity') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND vipprice > 0 AND status = 2 ORDER BY sort DESC'));
		$halfcardlist = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halfcardlist') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND  status = 1 ORDER BY sort DESC'));

		if ($halfcardlist) {
			foreach ($halfcardlist as $key => &$halfcard) {
				$halfcard['logo'] = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $halfcard['merchantid']), 'logo');
			}
		}

		$scoupon = pdo_getall(PDO_NAME . 'couponlist', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'status' => 1, 'vipstatus >' => 0));

		if ($scoupon) {
			foreach ($scoupon as $sk => &$sval) {
				if ($sval['is_charge'] == 1) {
					if ($sval['vipstatus'] == 2) {
						$sval['charge'] = $sval['vipprice'];
						$sval['oldcharge'] = $sval['price'];
					}
					else {
						$sval['charge'] = $sval['price'];
					}
				}
				else {
					$sval['charge'] = '免费';
				}
			}
		}

		$advs = pdo_getall('wlmerchant_adv', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'enabled' => 1, 'type' => 5));
		$navs = pdo_getall('wlmerchant_nav', array('uniacid' => $_W['uniacid'], 'aid' => -1, 'enabled' => 1, 'type' => 2), array(), '', 'displayorder DESC');

		foreach ($navs as $key => &$nav) {
			if (empty($nav['link'])) {
				$nav['link'] = app_url('halfcard/halfcard_app/userrightpage');
			}
		}

		$week = date('w');
		$day = date('m-j');

		if ($week == 0) {
			$week = 7;
		}

		$week1 = date('w', strtotime('+1 day'));
		$day1 = date('m-j', strtotime('+1 day'));
		$week2 = date('w', strtotime('+2 day'));
		$day2 = date('m-j', strtotime('+2 day'));
		$week3 = date('w', strtotime('+3 day'));
		$day3 = date('m-j', strtotime('+3 day'));
		$week4 = date('w', strtotime('+4 day'));
		$day4 = date('m-j', strtotime('+4 day'));
		$week5 = date('w', strtotime('+5 day'));
		$day5 = date('m-j', strtotime('+5 day'));

		if ($week1 == 0) {
			$week1 = 7;
		}

		if ($week2 == 0) {
			$week2 = 7;
		}

		if ($week3 == 0) {
			$week3 = 7;
		}

		if ($week4 == 0) {
			$week4 = 7;
		}

		if ($week5 == 0) {
			$week5 = 7;
		}

		$useflag = $_GPC['useflag'] ? $_GPC['useflag'] : 0;
		$actid = $_GPC['actid'];
		$type = $_GPC['type'];
		if ($base['share_title'] || $base['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];

			if ($base['share_title']) {
				$title = $base['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[系统名称]', $sysname, $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}
			else {
				$_W['wlsetting']['share']['share_title'] = !empty($base['share_title']) ? $base['share_title'] : $_W['wlsetting']['share']['share_title'];
			}

			if ($base['share_desc']) {
				$desc = $base['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[系统名称]', $sysname, $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
			else {
				$_W['wlsetting']['share']['share_desc'] = !empty($base['share_desc']) ? $base['share_desc'] : $_W['wlsetting']['share']['share_desc'];
			}
		}
		else {
			$_W['wlsetting']['share']['share_title'] = !empty($base['share_title']) ? $base['share_title'] : $_W['wlsetting']['share']['share_title'];
			$_W['wlsetting']['share']['share_desc'] = !empty($base['share_desc']) ? $base['share_desc'] : $_W['wlsetting']['share']['share_desc'];
		}

		$_W['wlsetting']['share']['share_image'] = !empty($base['share_image']) ? $base['share_image'] : $_W['wlsetting']['share']['share_image'];

		if (p('diypage')) {
			if (!empty($_W['agentset']['diypageset']['adv_index'])) {
				$advdata = Diy::BeOverdue($_W['agentset']['diypageset']['adv_index']);
			}

			if (!empty($_W['agentset']['diypageset']['menu_index'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_index']);
			}
		}

		$page_type = 2;
		include wl_template('newcard/usercard');
	}

	public function getopenrecodes()
	{
		global $_W;
		global $_GPC;
		$recodes = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' ORDER BY id DESC LIMIT 10'));

		if ($recodes) {
			$recode = array();

			foreach ($recodes as $key => &$re) {
				$re['day'] = round(($re['expiretime'] - $re['createtime']) / 3600 / 24);
				$recode[$key]['msg'] = $re['username'] . '开通了' . $re['day'] . '天会员';
				$head = pdo_getcolumn('wlmerchant_member', array('id' => $re['mid']), 'avatar');
				$recode[$key]['imgurl'] = tomedia($head);
			}
		}

		exit(json_encode(array('errno' => 0, 'data' => $recode)));
	}

	public function getlink()
	{
		global $_W;
		global $_GPC;
		$packid = $_GPC['id'];
		$flag = $_GPC['flag'];

		if ($flag == 1) {
			$active = pdo_get(PDO_NAME . 'halfcardlist', array('id' => $packid), array('extlink', 'status', 'level'));
		}
		else if ($flag == 3) {
			$active = pdo_get(PDO_NAME . 'couponlist', array('id' => $packid), array('extlink', 'status', 'level'));
		}
		else {
			$active = pdo_get(PDO_NAME . 'package', array('id' => $packid), array('extlink', 'status', 'level'));
		}

		if (empty($_W['mid'])) {
			$backurl = urlencode(app_url('halfcard/halfcard_app/userhalfcard'));
			exit(json_encode(array('errno' => 2, 'msg' => $backurl)));
		}
		else {
			$level = unserialize($active['level']);

			if ($level) {
				$halfcard = Halfcard::checklevel($_W['mid'], $level);
			}
			else {
				$halfcard = Member::checkhalfmember();
			}
		}

		if (empty($halfcard)) {
			exit(json_encode(array('errno' => 1, 'msg' => '您不是会员或所在会员等级无法享用')));
		}
		else if ($active['status'] != 1) {
			exit(json_encode(array('errno' => 1, 'msg' => '活动已下架')));
		}
		else {
			exit(json_encode(array('errno' => 0, 'msg' => $active['extlink'])));
		}
	}

	public function showqrimg()
	{
		global $_W;
		global $_GPC;
		$cardid = $_GPC['cardid'];
		$type = $_GPC['type'];

		if ($type == 1) {
			$halfid = $_GPC['id'];
		}
		else {
			if ($type == 2) {
				$packid = $_GPC['id'];
			}
		}

		if (empty($_W['mid'])) {
			$backurl = urlencode(app_url('halfcard/halfcard_app/userhalfcard'));
			exit(json_encode(array('errno' => 2, 'msg' => $backurl)));
		}
		else if (empty($cardid)) {
			$halfcard = Member::checkhalfmember();
		}
		else {
			$halfcard = pdo_get(PDO_NAME . 'halfcardmember', array('id' => $cardid), array('expiretime', 'id', 'createtime', 'disable', 'levelid'));
		}

		if (empty($halfcard)) {
			exit(json_encode(array('errno' => 1, 'msg' => '请先成为会员才能使用')));
		}
		else {
			if (empty($cardid)) {
				exit(json_encode(array('errno' => 1, 'msg' => '参数错误，请刷新重试')));
			}
		}

		$expiretime = $halfcard['expiretime'];

		if ($expiretime < time()) {
			exit(json_encode(array('errno' => 1, 'msg' => '此卡已过期，请续费或换卡重试')));
		}

		if ($halfcard['disable']) {
			exit(json_encode(array('errno' => 1, 'msg' => '此卡被禁用，请换卡或联系客服')));
		}

		$realcard = pdo_get('wlmerchant_halfcard_realcard', array('cardid' => $halfcard['id']), array('icestatus'));

		if ($realcard['icestatus']) {
			exit(json_encode(array('errno' => 1, 'msg' => '此卡被冻结，请换卡或联系客服')));
		}

		if (!empty($_W['wlsetting']['halfcard']['use_space']) && !empty($_W['wlsetting']['halfcard']['use_space_times'])) {
			$timepara = $_W['wlsetting']['halfcard']['use_space'] * 24 * 60 * 60;
			$ceiltime = (time() - $halfcard['createtime']) % $timepara;
			$use_space_time = time() - $ceiltime;
			$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE mid = ' . $_W['mid'] . ' AND usetime >= \'' . $use_space_time . '\' AND uniacid = ' . $_W['uniacid']));

			if ($_W['wlsetting']['halfcard']['use_space_times'] <= $times) {
				exit(json_encode(array('errno' => 1, 'msg' => '特权每' . $_W['wlsetting']['halfcard']['use_space'] . '天，限使用' . $_W['wlsetting']['halfcard']['use_space_times'] . '次')));
			}
		}

		if (!empty($_W['wlsetting']['halfcard']['use_space_days'])) {
			$use_space_times = time() - $_W['wlsetting']['halfcard']['use_space_days'] * 24 * 60 * 60;
			$flag = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE mid = ' . $_W['mid'] . ' AND usetime >= \'' . $use_space_times . '\' AND uniacid = ' . $_W['uniacid']));

			if ($flag) {
				exit(json_encode(array('errno' => 1, 'msg' => '您在' . $_W['wlsetting']['halfcard']['use_space_days'] . '天内使用过特权，请稍后再试')));
			}
		}

		if ($packid) {
			$url = app_url('halfcard/halfcard_app/usetimescard', array('cardid' => $cardid, 'packid' => $packid));
			$src = app_url('store/merchant/qrcodeimg', array('url' => $url));
			$active = pdo_get(PDO_NAME . 'package', array('id' => $packid));

			if ($active['status'] != 1) {
				exit(json_encode(array('errno' => 1, 'msg' => '礼包已下架')));
			}

			if ($active['type']) {
				exit(json_encode(array('errno' => 3, 'msg' => $active['extlink'])));
			}

			if ($active['timestatus']) {
				if ($halfcard['createtime'] < $active['starttime'] || $active['endtime'] < $halfcard['createtime']) {
					exit(json_encode(array('errno' => 1, 'msg' => '活动即将上线，敬请期待	')));
				}
			}

			if ($active['timeslimit']) {
				$begintime = strtotime(date('Y-m-d', time()));
				$todaytime = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $packid . ' AND createtime > ' . $begintime . ' AND type = 2'));

				if ($active['timeslimit'] <= $todaytime) {
					exit(json_encode(array('errno' => 1, 'msg' => '该礼包今日已发完')));
				}
			}

			if ($active['packtimestatus']) {
				if (time() < $active['datestarttime']) {
					exit(json_encode(array('errno' => 1, 'msg' => '该礼包活动还未开始')));
				}

				if ($active['dateendtime'] < time()) {
					exit(json_encode(array('errno' => 1, 'msg' => '该礼包活动已结束')));
				}
			}

			if ($active['oplimit']) {
				$zerotime = strtotime(date('Y-m-d'), time());
				$times3 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $active['id'] . ' AND mid = ' . $_W['mid'] . '  AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
				$surplus = $active['oplimit'] - $times3;

				if ($surplus < 1) {
					exit(json_encode(array('errno' => 1, 'msg' => '您今天只能领取' . $active['oplimit'] . '次该礼包')));
				}
			}

			if ($active['weeklimit']) {
				$begin = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
				$timeflag = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfcard['id'] . ' AND mid = ' . $_W['mid'] . '  AND usetime > ' . $begin . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
				$surplus = $active['weeklimit'] - $timeflag;

				if ($surplus < 1) {
					exit(json_encode(array('errno' => 1, 'msg' => '您每周只能领取' . $active['weeklimit'] . '次该礼包')));
				}
			}

			if ($active['monthlimit']) {
				$begin = mktime(0, 0, 0, date('m'), 1, date('Y'));
				$timeflag = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfcard['id'] . ' AND mid = ' . $_W['mid'] . '  AND usetime > ' . $begin . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
				$surplus = $active['monthlimit'] - $timeflag;

				if ($surplus < 1) {
					exit(json_encode(array('errno' => 1, 'msg' => '您每月只能领取' . $active['monthlimit'] . '次该礼包')));
				}
			}

			$active['level'] = unserialize($active['level']);

			if (is_array($active['level'])) {
				if (!in_array($halfcard['levelid'], $active['level'])) {
					exit(json_encode(array('errno' => 1, 'msg' => '您所在的会员等级不能领取该礼包')));
				}
			}

			$activename = $active['title'];
			$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $active['merchantid']), 'storename');

			if ($active['datestatus'] == 4) {
				$begin = time() - 31536000;
			}
			else if ($active['datestatus'] == 3) {
				$begin = mktime(0, 0, 0, date('m'), 1, date('Y'));
			}
			else if ($active['datestatus'] == 2) {
				$begin = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
			}
			else {
				$begin = 0;
			}

			if ($begin < $halfcard['createtime'] && $active['resetswitch']) {
				$begin = $halfcard['createtime'];
			}

			$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE cardid = ' . $cardid . ' AND activeid = ' . $packid . ' AND type = 2 AND usetime > ' . $begin));
			$surplustimes = $active['usetimes'] - $times;

			if ($surplustimes < 1) {
				exit(json_encode(array('errno' => 1, 'msg' => '该礼包您已全部领取完毕')));
			}
		}
		else {
			if ($halfid) {
				$url = app_url('halfcard/halfcard_app/usetimescard', array('cardid' => $cardid, 'halfid' => $halfid));
				$src = app_url('store/merchant/qrcodeimg', array('url' => $url));
				$active = pdo_get(PDO_NAME . 'halfcardlist', array('id' => $halfid), array('title', 'merchantid', 'limit', 'discount', 'activediscount', 'datestatus', 'week', 'day', 'describe', 'timeslimit', 'level'));

				if ($active['timeslimit']) {
					$begintime = strtotime(date('Y-m-d', time()));
					$todaytime = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfid . ' AND createtime > ' . $begintime . ' AND type = 1'));

					if ($active['timeslimit'] <= $todaytime) {
						exit(json_encode(array('errno' => 1, 'msg' => '该商户今日特权名额已用完')));
					}
				}

				$activename = $active['title'];
				$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $active['merchantid']), 'storename');
				$active['level'] = unserialize($active['level']);

				if (is_array($active['level'])) {
					if (!in_array($halfcard['levelid'], $active['level'])) {
						exit(json_encode(array('errno' => 1, 'msg' => '您所在的会员等级不能使用该特权')));
					}
				}

				if ($active['datestatus'] == 1) {
					$weeks = unserialize($active['week']);
					$today = date('w');

					if ($today == 0) {
						$today = 7;
					}

					if (in_array($today, $weeks)) {
						$halfflag = 1;
						$discount = $active['activediscount'];
					}
					else {
						$halfflag = 0;
						$discount = $active['discount'];
					}
				}
				else {
					$days = unserialize($active['day']);
					$today = date('j');

					if (in_array($today, $days)) {
						$halfflag = 1;
						$discount = $active['activediscount'];
					}
					else {
						$halfflag = 0;
						$discount = $active['discount'];
					}
				}
			}
		}

		exit(json_encode(array('describe' => $active['describe'], 'flag' => $type, 'qrsrc' => $src, 'limit' => $active['limit'], 'activename' => $activename, 'storename' => $storename, 'surplustimes' => $surplustimes, 'alltime' => $active['usetimes'], 'halfflag' => $halfflag, 'discount' => $discount)));
	}

	public function hexiaohalfcard()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$order = pdo_get('wlmerchant_halfcardrecord', array('id' => $id));
		$verifier = SingleMerchant::verifier($order['merchantid'], $_W['mid']);

		if ($verifier) {
			$res = pdo_update('wlmerchant_halfcardrecord', array('status' => 2), array('id' => $id));

			if ($res) {
				$res2 = pdo_update('wlmerchant_halfcardrecord', array('hexiaotime' => time(), 'verfmid' => $_W['mid']), array('id' => $id));

				if ($res2) {
					$member = pdo_get('wlmerchant_member', array('id' => $order['mid']), array('openid'));
					$openid = $member['openid'];
					$active = pdo_get('wlmerchant_halfcardlist', array('id' => $order['activeid']), array('title'));
					$cardname = $active['title'];
					$time = date('Y-m-d H:i:s', time());
					$qrcode = $order['qrcode'];
					$store = pdo_get('wlmerchant_merchantdata', array('id' => $order['merchantid']), array('storename'));
					$storename = $store['storename'];
					$url = app_url('halfcard/halfcard_app/userhalfcard');
					Message::hexiaoSuccess($openid, 1, $cardname, $url);
					Message::hexiaoTover($_W['wlmember']['openid'], 1, $cardname);
					$remark = $order['is_half'] == 2 ? $_W['agentset']['halfcard']['halfcardtext'] . '平日折扣' : $_W['agentset']['halfcard']['halfcardtext'] . '活动日';
					SingleMerchant::verifRecordAdd($order['aid'], $order['merchantid'], $order['mid'], 'halfcard', $order['id'], $order['qrcode'], $remark, 2);
					wl_message('核销成功', 'close', 'success');
				}
				else {
					wl_message('核销失败,时间参数错误，请联系管理员！', 'close', 'error');
				}
			}
			else {
				wl_message('核销失败,请勿二次核销。', 'close', 'error');
			}
		}
		else {
			wl_message('非管理员无法核销', 'close', 'error');
		}
	}

	public function usetimescard()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '使用' . $_W['wlsetting']['halfcard']['text']['halfcardtext'] . ' - ' . $_W['wlsetting']['base']['name'] : '使用' . $_W['wlsetting']['halfcard']['text']['halfcardtext'];
		$cardid = $_GPC['cardid'];
		$halfid = $_GPC['halfid'];
		$packid = $_GPC['packid'];
		$card = pdo_get('wlmerchant_halfcardmember', array('id' => $cardid));
		$merchantid = $_GPC['merchantid'];

		if (empty($card)) {
			wl_message('卡号错误或此卡已被删除', 'close', 'error');
		}

		$expiretime = $card['expiretime'];

		if ($expiretime < time()) {
			wl_message('此卡已过期，请续费或换卡重试', 'close', 'error');
		}

		if ($card['disable']) {
			wl_message('此卡已被禁用,如有疑问请联系管理员', 'close', 'error');
		}

		$realcard = pdo_get('wlmerchant_halfcard_realcard', array('cardid' => $cardid), array('icestatus'));

		if ($realcard['icestatus']) {
			wl_message('此卡已被冻结,如有疑问请联系管理员', 'close', 'error');
		}

		if (!empty($_W['wlsetting']['halfcard']['use_space']) && !empty($_W['wlsetting']['halfcard']['use_space_times'])) {
			$timepara = $_W['wlsetting']['halfcard']['use_space'] * 24 * 60 * 60;
			$ceiltime = (time() - $card['createtime']) % $timepara;
			$use_space_time = time() - $ceiltime;
			$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE mid = ' . $card['mid'] . ' AND usetime >= \'' . $use_space_time . '\' AND uniacid = ' . $_W['uniacid']));

			if ($_W['wlsetting']['halfcard']['use_space_times'] <= $times) {
				wl_message('特权每' . $_W['wlsetting']['halfcard']['use_space'] . '天，限使用' . $_W['wlsetting']['halfcard']['use_space_times'] . '次', 'close', 'error');
			}
		}

		if (!empty($_W['wlsetting']['halfcard']['use_space_days'])) {
			$use_space_times = time() - $_W['wlsetting']['halfcard']['use_space_days'] * 24 * 60 * 60;
			$flag = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE mid = ' . $card['mid'] . ' AND usetime >= \'' . $use_space_times . '\' AND uniacid = ' . $_W['uniacid']));

			if ($flag) {
				wl_message('该用户在' . $_W['wlsetting']['halfcard']['use_space_days'] . '天内使用过特权，请稍后再试', 'close', 'error');
			}
		}

		if (empty($merchantid) && empty($halfid) && empty($packid)) {
			$verifiers = pdo_getall('wlmerchant_merchantuser', array('mid' => $_W['mid'], 'status' => 2), array('storeid'));

			if (empty($verifiers)) {
				wl_message('非管理员无法核销', 'close', 'error');
			}
			else {
				$merchants = array();

				foreach ($verifiers as $key => &$vari) {
					$halfactive = pdo_get('wlmerchant_halfcardlist', array('merchantid' => $vari['storeid']));
					$packactive = pdo_get('wlmerchant_package', array('merchantid' => $vari['storeid']));
					if ($halfactive || $packactive) {
						$merchants[] = $vari['storeid'];
					}
				}
			}

			if (empty($merchants)) {
				wl_message('商户暂无特权活动', 'close', 'error');
			}
			else {
				foreach ($merchants as $key => &$mer) {
					$mer = pdo_get(PDO_NAME . 'merchantdata', array('id' => $mer), array('logo', 'id', 'storename', 'address'));
				}
			}
		}

		if (count($merchants) == 1 || $merchantid || $halfid || $packid) {
			if (empty($merchantid) && empty($halfid) && empty($packid)) {
				$merchantid = $merchants[0]['id'];
				$halfactive = pdo_get('wlmerchant_halfcardlist', array('merchantid' => $merchantid, 'status' => 1));
				$packactives = pdo_getall('wlmerchant_package', array('merchantid' => $merchantid, 'status' => 1));
			}
			else if ($halfid) {
				$halfactive = pdo_get('wlmerchant_halfcardlist', array('id' => $halfid, 'status' => 1));
				$merchantid = $halfactive['merchantid'];
				$packactives = pdo_getall('wlmerchant_package', array('merchantid' => $merchantid, 'status' => 1));
			}
			else if ($packid) {
				$packactives = pdo_getall('wlmerchant_package', array('id' => $packid, 'status' => 1));
				$merchantid = $packactives[0]['merchantid'];
			}
			else {
				$halfactive = pdo_get('wlmerchant_halfcardlist', array('merchantid' => $merchantid, 'status' => 1));
				$packactives = pdo_getall('wlmerchant_package', array('merchantid' => $merchantid, 'status' => 1));
			}

			$verifier = SingleMerchant::verifier($merchantid, $_W['mid']);

			if ($verifier) {
				$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $merchantid));

				if ($halfactive) {
					$halfactive['level'] = unserialize($halfactive['level']);

					if (is_array($halfactive['level'])) {
						if (in_array($card['levelid'], $halfactive['level'])) {
							$levelpass = 1;
						}
						else {
							$levelpass = 0;
						}
					}
					else {
						$levelpass = 1;
					}

					if ($levelpass) {
						if ($halfactive['timeslimit']) {
							$begintime = strtotime(date('Y-m-d', time()));
							$todaytime = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfactive['id'] . ' AND createtime > ' . $begintime . ' AND type = 1'));

							if ($halfactive['timeslimit'] <= $todaytime) {
								wl_message('该商户今日特权名额已用完', 'close', 'error');
							}
						}

						if ($halfactive['datestatus'] == 1) {
							$weeks = unserialize($halfactive['week']);
							$today = date('w');

							if ($today == 0) {
								$today = 7;
							}

							if (in_array($today, $weeks)) {
								if ($halfactive['timeslimit']) {
									$zerotime = strtotime(date('Y-m-d'), time());
									$times2 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfactive['id'] . ' AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 1 AND freeflag = 1'));
									$surplus = $halfactive['timeslimit'] - $times2;

									if ($surplus) {
										$actflag = 1;
										$discount = $halfactive['activediscount'];
									}
									else {
										$actflag = 0;
										$discount = $halfactive['discount'];
									}
								}
								else {
									$actflag = 1;
									$discount = $halfactive['activediscount'];
								}
							}
							else {
								$actflag = 0;
								$discount = $halfactive['discount'];
							}
						}
						else {
							$days = unserialize($halfactive['day']);
							$today = date('j');

							if (in_array($today, $days)) {
								if ($halfactive['timeslimit']) {
									$zerotime = strtotime(date('Y-m-d'), time());
									$times2 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $halfactive['id'] . ' AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 1 AND freeflag = 1'));
									$surplus = $halfactive['timeslimit'] - $times2;

									if ($surplus) {
										$actflag = 1;
										$discount = $halfactive['activediscount'];
									}
									else {
										$actflag = 0;
										$discount = $halfactive['discount'];
									}
								}
								else {
									$actflag = 1;
									$discount = $halfactive['activediscount'];
								}
							}
							else {
								$actflag = 0;
								$discount = $halfactive['discount'];
							}
						}
					}
					else {
						$halfactive = 0;
					}
				}

				if ($packactives) {
					foreach ($packactives as $key => &$packactive) {
						$packactive['level'] = unserialize($packactive['level']);

						if (is_array($packactive['level'])) {
							if (!in_array($card['levelid'], $packactive['level'])) {
								if ($packid) {
									wl_message('会员所在等级无法领取该礼包', 'close', 'error');
								}
								else {
									unset($packactives[$key]);
								}
							}
						}

						if ($packactive['status'] != 1) {
							if ($packid && empty($halfactive)) {
								wl_message('礼包已下架', 'close', 'error');
							}
							else {
								unset($packactives[$key]);
							}
						}

						if ($packactive['packtimestatus']) {
							if (time() < $packactive['datestarttime']) {
								if ($packid && empty($halfactive)) {
									wl_message('该礼包活动还未开始', 'close', 'error');
								}
								else {
									unset($packactives[$key]);
								}
							}

							if ($packactive['dateendtime'] < time()) {
								if ($packid && empty($halfactive)) {
									wl_message('该礼包活动已结束', 'close', 'error');
								}
								else {
									unset($packactives[$key]);
								}
							}
						}

						if ($packactive['timeslimit']) {
							$begintime = strtotime(date('Y-m-d', time()));
							$todaytime = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $packactive['id'] . ' AND createtime > ' . $begintime . ' AND type = 2'));

							if ($packactive['timeslimit'] <= $todaytime) {
								if ($packid && empty($halfactive)) {
									wl_message('礼包今日已发完', 'close', 'error');
								}
								else {
									unset($packactives[$key]);
								}
							}
						}

						if ($packactive['oplimit']) {
							$zerotime = strtotime(date('Y-m-d'), time());
							$times3 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $packactive['id'] . ' AND mid = ' . $card['mid'] . '  AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
							$surplus = $packactive['oplimit'] - $times3;

							if ($surplus < 1) {
								if ($packid && empty($halfactive)) {
									wl_message('该用户今天只能领取该礼包' . $packactive['oplimit'] . '次', 'close', 'error');
								}
								else {
									unset($packactives[$key]);
								}
							}
						}

						if ($packactive['weeklimit']) {
							$zerotime = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
							$times3 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $packactive['id'] . ' AND mid = ' . $card['mid'] . '  AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
							$surplus = $packactive['weeklimit'] - $times3;

							if ($surplus < 1) {
								if ($packid && empty($halfactive)) {
									wl_message('该用户每周只能领取该礼包' . $packactive['weeklimit'] . '次', 'close', 'error');
								}
								else {
									unset($packactives[$key]);
								}
							}
						}

						if ($packactive['monthlimit']) {
							$zerotime = mktime(0, 0, 0, date('m'), 1, date('Y'));
							$times3 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $packactive['id'] . ' AND mid = ' . $card['mid'] . '  AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
							$surplus = $packactive['monthlimit'] - $times3;

							if ($surplus < 1) {
								if ($packid && empty($halfactive)) {
									wl_message('该用户每月只能领取该礼包' . $packactive['monthlimit'] . '次', 'close', 'error');
								}
								else {
									unset($packactives[$key]);
								}
							}
						}

						if ($packactive['datestatus'] == 4) {
							$begin = time() - 31536000;
						}
						else if ($packactive['datestatus'] == 3) {
							$begin = mktime(0, 0, 0, date('m'), 1, date('Y'));
						}
						else if ($packactive['datestatus'] == 2) {
							$begin = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
						}
						else {
							$begin = 0;
						}

						if ($begin < $card['createtime'] && $packactive['resetswitch']) {
							$begin = $card['createtime'];
						}

						$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE cardid = ' . $cardid . ' AND activeid = ' . $packactive['id'] . ' AND merchantid = ' . $packactive['merchantid'] . ' AND type = 2 AND usetime > ' . $begin . ' '));
						$surplustimes = $packactive['usetimes'] - $times;

						if ($surplustimes <= 0) {
							if ($packid) {
								wl_message('用户已全部领取', 'close', 'error');
							}
							else {
								unset($packactives[$key]);
							}
						}

						if ($packactive['timeslimit']) {
							$zerotime = strtotime(date('Y-m-d'), time());
							$times2 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $packactive['id'] . ' AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2 '));
							$surplus = $packactive['timeslimit'] - $times2;

							if ($surplus <= 0) {
								if ($packid) {
									wl_message('今日礼包已全部发放', 'close', 'error');
								}
								else {
									unset($packactives[$key]);
								}
							}
						}
						else {
							$surplus = 1;
						}

						if ($packactive['timestatus']) {
							if ($card['createtime'] < $packactive['starttime'] || $packactive['endtime'] < $card['createtime']) {
								unset($packactives[$key]);
							}
						}

						$packactive['price'] = sprintf('%.2f', $packactive['price'] / $packactive['usetimes']);
					}
				}

				if (empty($packactives) && empty($halfactive)) {
					wl_message('该会员在本店无可用优惠', 'close', 'error');
				}

				include wl_template('halfcardhtml/usetimescard');
			}
			else {
				wl_message('非管理员无法核销', 'close', 'error');
			}
		}
		else {
			include wl_template('halfcardhtml/usetimescard2');
		}
	}

	public function createcardrecord()
	{
		global $_W;
		global $_GPC;
		$cardid = $_GPC['cardid'];
		$base = Setting::wlsetting_read('halfcard');
		$card = pdo_get(PDO_NAME . 'halfcardmember', array('id' => $cardid), array('mid', 'createtime'));
		$mid = $card['mid'];
		$halfactiveid = $_GPC['halfactiveid'];
		$packids = $_GPC['packids'];
		$merchantid = $_GPC['merchantid'];
		$discount = $_GPC['discount'];
		if (empty($mid) || empty($halfactiveid) && empty($packids) || empty($merchantid)) {
			exit(json_encode(array('errno' => 1, 'msg' => '缺少关键参数')));
		}

		$member = pdo_get('wlmerchant_member', array('id' => $mid), array('openid'));
		$openid = $member['openid'];
		$freeflag = $_GPC['freeflag'];
		$actflag = $_GPC['actflag'];
		$record['mid'] = $mid;
		$record['cardid'] = $cardid;
		$record['merchantid'] = $merchantid;
		$record['verfmid'] = $_W['mid'];

		if ($halfactiveid) {
			$halfua = pdo_get('wlmerchant_halfcardlist', array('id' => $halfactiveid), array('uniacid', 'aid'));
			$halfrecord = $record;
			$halfrecord['uniacid'] = $halfua['uniacid'];
			$halfrecord['aid'] = $halfua['aid'];
			$halfrecord['type'] = 1;
			$halfrecord['activeid'] = $halfactiveid;
			$halfrecord['ordermoney'] = $_GPC['ordermoney'];
			$halfrecord['usetime'] = time();
			$halfrecord['createtime'] = time();

			if ($actflag) {
				$halfrecord['freeflag'] = 1;
			}
			else {
				$halfrecord['freeflag'] = 0;
			}

			$halfrecord['realmoney'] = $_GPC['paymoney'];
			$halfrecord['discount'] = $discount;
			$halfrecord['undismoney'] = $_GPC['undismoney'];
			$flagtime = time() - 5;
			$flag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_timecardrecord') . ('WHERE cardid = ' . $cardid . ' AND activeid = ' . $halfactiveid . ' AND type = 1 AND createtime > ' . $flagtime . ' '));

			if (empty($flag)) {
				$res1 = pdo_insert(PDO_NAME . 'timecardrecord', $halfrecord);
			}
			else {
				$res1 = 1;
			}

			if ($res1) {
				$orderid = pdo_insertid();
				$remark = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('id' => $halfactiveid), 'title');
				SingleMerchant::verifRecordAdd($_W['aid'], $merchantid, $mid, 'halfcard1', $orderid, $orderid, $remark, 2);
				$url = app_url('halfcard/halfcard_app/userhalfcard');
				$halfactivename = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('id' => $halfactiveid), 'title');
				$first = '您好，您的买单特权已成功核销，只需现金支付' . $halfrecord['realmoney'] . '元';
				$url = app_url('halfcard/halfcard_app/userecord');
				Message::Halfhexiao($openid, 1, $halfactivename, $url, $first);
				$storeadminmid = pdo_fetch('SELECT mid FROM ' . tablename('wlmerchant_merchantuser') . (' WHERE storeid = ' . $merchantid . ' AND ismain = 1'));
				$storeadminopenid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $storeadminmid), 'openid');
				$first = '您好,一个商户特权已核销';
				$keyword1 = $halfactivename;
				$keyword2 = '已核销';
				$jobremark = '点击查看验证记录';
				$storeurl = app_url('store/supervise/switchstore', array('storeid' => $merchantid, 'url' => urlencode(app_url('store/supervise/verifrecord', array('type' => 'halfcard1')))));
				Message::jobNotice($storeadminopenid, $first, $keyword1, $keyword2, $jobremark, $storeurl);

				if ($base['carddeduct']) {
					$jifen = floor($halfrecord['realmoney'] / $base['carddeduct']);

					if (0 < $jifen) {
						$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $merchantid), 'storename');
						$remark = '会员折扣支付赠积分 ——' . $storename;
						Member::credit_update_credit1($mid, $jifen, $remark);
					}
				}
			}
		}

		if ($packids) {
			foreach ($packids as $key => $packactiveid) {
				$package = pdo_get(PDO_NAME . 'package', array('id' => $packactiveid), array('price', 'allnum', 'usetimes', 'datestatus', 'merchantid', 'resetswitch'));

				if ($package['datestatus'] == 4) {
					$begin = time() - 31536000;
				}
				else if ($package['datestatus'] == 3) {
					$begin = mktime(0, 0, 0, date('m'), 1, date('Y'));
				}
				else if ($package['datestatus'] == 2) {
					$begin = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
				}
				else {
					$begin = 0;
				}

				if ($begin < $card['createtime'] && $package['resetswitch']) {
					$begin = $card['createtime'];
				}

				$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE cardid = ' . $cardid . ' AND activeid = ' . $packactiveid . ' AND merchantid = ' . $package['merchantid'] . ' AND type = 2 AND usetime > ' . $begin . ' '));
				$surplustimes = $package['usetimes'] - $times;

				if (empty($surplustimes)) {
					exit(json_encode(array('errno' => 1, 'msg' => '消费失败，用户已全部领取')));
				}

				$packua = pdo_get('wlmerchant_package', array('id' => $packactiveid), array('uniacid', 'aid'));
				$packrecord = $record;
				$packrecord['uniacid'] = $packua['uniacid'];
				$packrecord['aid'] = $packua['aid'];
				$packrecord['type'] = 2;
				$packrecord['activeid'] = $packactiveid;
				$packrecord['ordermoney'] = sprintf('%.2f', $package['price'] / $package['usetimes']);
				$packrecord['usetime'] = time();
				$packrecord['createtime'] = time();
				$packrecord['freeflag'] = 0;
				$res2 = pdo_insert(PDO_NAME . 'timecardrecord', $packrecord);

				if ($res2) {
					$orderid = pdo_insertid();
					$remark = pdo_getcolumn(PDO_NAME . 'package', array('id' => $packactiveid), 'title');
					SingleMerchant::verifRecordAdd($_W['aid'], $merchantid, $mid, 'halfcard2', $orderid, $orderid, $remark, 2);
					$first = '您好，您的特权大礼包已成功核销';
					$url = app_url('halfcard/halfcard_app/userecord');
					Message::Halfhexiao($openid, 1, $remark, $url, $first);
					$storeadminmid = pdo_fetch('SELECT mid FROM ' . tablename('wlmerchant_merchantuser') . (' WHERE storeid = ' . $merchantid . ' AND ismain = 1'));
					$storeadminopenid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $storeadminmid), 'openid');
					$first = '您好,一个商家大礼包已核销';
					$keyword1 = $remark;
					$keyword2 = '已核销';
					$jobremark = '点击查看验证记录';
					$storeurl = app_url('store/supervise/switchstore', array('storeid' => $merchantid, 'url' => urlencode(app_url('store/supervise/verifrecord', array('type' => 'halfcard2')))));
					Message::jobNotice($storeadminopenid, $first, $keyword1, $keyword2, $jobremark, $storeurl);

					if ($base['carddeduct']) {
						$jifen = floor($packrecord['ordermoney'] / $base['packdeduct']);

						if ($jifen) {
							$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $merchantid), 'storename');
							$remark = '会员使用礼包赠积分 ——' . $storename;
							Member::credit_update_credit1($mid, $jifen, $remark);
						}
					}
				}

				if ($package['allnum']) {
					$newnum = $package['allnum'] - 1;
					pdo_fetch('update' . tablename('wlmerchant_package') . ('SET allnum=allnum-1 WHERE id = ' . $packactiveid));

					if ($newnum < 1) {
						pdo_update('wlmerchant_package', array('status' => 0), array('id' => $packactiveid));
					}
				}
			}
		}

		if ($res1 || $res2) {
			Store::addFans($merchantid, $mid);
			exit(json_encode(array('errno' => 0)));
		}
		else {
			exit(json_encode(array('errno' => 1, 'msg' => '消费失败，请重试')));
		}
	}

	public function userecord()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $_W['wlsetting']['halfcard']['text']['halfcardtext'] . '消费记录 - ' . $_W['wlsetting']['base']['name'] : $_W['wlsetting']['halfcard']['text']['halfcardtext'] . '消费记录';
		$cardid = $_GPC['id'];

		if (empty($cardid)) {
			$where['mid'] = $_W['mid'];
		}
		else {
			$where['cardid'] = $cardid;
			$username = pdo_getcolumn(PDO_NAME . 'halfcardmember', array('id' => $cardid), 'username');
		}

		if ($_W['ispost']) {
			$pindex = $_GPC['pindex'];
			$myorder = Util::getNumData('*', PDO_NAME . 'timecardrecord', $where, 'ID DESC', $pindex, 10, 1);
			$myorder = $myorder[0];

			foreach ($myorder as $key => &$order) {
				$order['createtime'] = date('m-d H:i', $order['createtime']);
				$order['url'] = app_url('halfcard/halfcard_app/userecord_detail', array('id' => $order['id']));

				if ($order['type'] == 1) {
					$order['title'] = pdo_getcolumn('wlmerchant_halfcardlist', array('id' => $order['activeid']), 'title');
				}
				else {
					$order['title'] = pdo_getcolumn('wlmerchant_package', array('id' => $order['activeid']), 'title');
				}
			}

			exit(json_encode(array('errno' => 0, 'data' => $myorder)));
		}

		include wl_template('halfcardhtml/userecord');
	}

	public function userecord_detail()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '消费记录 - ' . $_W['wlsetting']['base']['name'] : '消费记录';
		$id = intval($_GPC['id']);
		$record = pdo_get(PDO_NAME . 'timecardrecord', array('id' => $id, 'mid' => $_W['mid']));

		if (empty($record)) {
			wl_message('未找到消费记录');
		}

		$storeinfo = pdo_get(PDO_NAME . 'merchantdata', array('id' => $record['merchantid']), array('storename', 'logo', 'id'));
		$record['verifnickname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $record['verfmid']), 'nickname');
		$record['username'] = pdo_getcolumn(PDO_NAME . 'halfcardmember', array('id' => $record['cardid']), 'username');

		if (empty($record['username'])) {
			$record['username'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $record['mid']), 'nickname');
		}

		if ($record['type'] == 1) {
			$record['title'] = pdo_getcolumn('wlmerchant_halfcardlist', array('id' => $record['activeid']), 'title');
		}
		else {
			$record['title'] = pdo_getcolumn('wlmerchant_package', array('id' => $record['activeid']), 'title');
		}

		include wl_template('halfcardhtml/userecord_detail');
	}

	public function activation()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '激活' . $_W['agentset']['halfcard']['halfcardtext'] . ' - ' . $_W['wlsetting']['base']['name'] : '激活' . $_W['agentset']['halfcard']['halfcardtext'];
		$base = Setting::wlsetting_read('halfcard');
		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
		if (empty($_W['wlmember']['mobile']) && in_array('halfcard', $mastmobile)) {
			wl_message('未绑定手机号，去绑定！', app_url('member/user/binding', array('backurl' => urlencode(app_url('halfcard/halfcardopen/open', array('type' => 2))))), 'error');
		}

		$cardpa = $_GPC['cardpa'];

		if ($cardpa) {
			$type = Util::getSingelData('*', PDO_NAME . 'token', array('number' => $cardpa));
			if ($type['aid'] && $type['aid'] != $_W['aid'] && $base['halfcardtype'] == 2) {
				wl_message('该激活码不属于当前地区！');
			}

			if (empty($type)) {
				wl_message('激活码不存在！');
			}

			if ($type['status'] == 1) {
				wl_message('该激活码已使用！');
			}

			$dayNum = $type['days'];
			$cardid = $_GPC['cardid'];
			$username = $_GPC['username'];

			if (empty($username)) {
				wl_message('请填写持卡人姓名');
			}

			if ($cardid) {
				$mdata = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'id' => $cardid);
				$halfInfo = Util::getSingelData('*', PDO_NAME . 'halfcardmember', $mdata);
				$lastviptime = $halfInfo['expiretime'];
				$limittime = $lastviptime + $dayNum * 24 * 60 * 60;
			}
			else {
				$limittime = time() + $dayNum * 24 * 60 * 60;
			}

			$aid = Util::idSwitch('areaid', 'aid', $_W['areaid']);
			$halfcarddata = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'expiretime' => $limittime, 'username' => $username, 'levelid' => $type['levelid'], 'createtime' => time(), 'from' => 1);

			if (file_exists(IA_ROOT . '/addons/weliam_merchant/pTLjC21GjCGj.log')) {
				if (0 < $type['give_price']) {
					Member::credit_update_credit2($_W['mid'], $type['give_price'], '一卡通赠送金额');
				}
			}

			if (p('distribution')) {
				$base = Setting::wlsetting_read('distribution');
				if ($base['appdis'] == 2 && $base['switch'] && $base['together'] == 1) {
					$member = pdo_get('wlmerchant_member', array('id' => $_W['mid']), array('mobile', 'nickname', 'realname', 'distributorid'));
					$distributor = pdo_get('wlmerchant_distributor', array('id' => $member['distributorid']));

					if ($distributor) {
						if (empty($distributor['disflag'])) {
							pdo_update('wlmerchant_distributor', array('disflag' => 1, 'createtime' => time()), array('mid' => $_W['mid']));
						}
					}
					else {
						$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'createtime' => time(), 'disflag' => 1, 'nickname' => $member['nickname'], 'mobile' => $member['mobile'], 'realname' => $member['realname'], 'leadid' => -1);
						pdo_insert('wlmerchant_distributor', $data);
						$disid = pdo_insertid();
						pdo_update('wlmerchant_member', array('distributorid' => $disid), array('id' => $_W['mid']));
					}
				}
			}

			if ($cardid) {
				if (pdo_update(PDO_NAME . 'halfcardmember', $halfcarddata, array('id' => $cardid))) {
					pdo_update(PDO_NAME . 'token', array('status' => 1, 'mid' => $_W['mid'], 'openid' => $_W['openid']), array('number' => $cardpa));
					$limittime = date('Y-m-d H:i:s', $limittime);
					include wl_template('halfcardhtml/open_success');
					exit();
				}
			}
			else {
				if (pdo_insert(PDO_NAME . 'halfcardmember', $halfcarddata)) {
					pdo_update(PDO_NAME . 'token', array('status' => 1, 'mid' => $_W['mid'], 'openid' => $_W['openid']), array('number' => $cardpa));
					$limittime = date('Y-m-d H:i:s', $limittime);
					include wl_template('halfcardhtml/open_success');
					exit();
				}
			}
		}
	}

	public function checkuse()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$merchantid = $_GPC['merchantid'];
		$dayflag = date('Ymd');
		$daydate = date('Y-m-d');
		$merchanttime = pdo_fetch('SELECT * FROM ' . tablename('wlmerchant_halfcardrecord') . (' WHERE mid = ' . $_W['mid'] . ' AND date = \'' . $dayflag . '\' AND merchantid = ' . $merchantid));

		if ($merchanttime) {
			$url = app_url('halfcard/halfcard_app/hexiaohalfcard', array('id' => $merchanttime['id']));
			$qrurl = app_url('halfcard/halfcard_app/qrcodeimg', array('url' => $url));
			exit(json_encode(array('err' => 0, 'qrcode' => $merchanttime['qrcode'], 'datetime' => $daydate, 'qrsrc' => $qrurl, 'is_half' => $merchanttime['is_half'])));
		}
		else {
			exit(json_encode(array('err' => 1)));
		}
	}

	public function bindrealcard()
	{
		global $_W;
		global $_GPC;
		$cardsn = trim($_GPC['cardsn']);
		$salt = trim($_GPC['salt']);
		$card = pdo_get(PDO_NAME . 'halfcard_realcard', array('uniacid' => $_W['uniacid'], 'cardsn' => $cardsn, 'salt' => $salt));

		if (empty($card)) {
			exit(json_encode(array('errno' => 1, 'msg' => '参数错误，请重新获取')));
		}

		if ($card['icestatus']) {
			exit(json_encode(array('errno' => 1, 'msg' => '此卡已冻结,无法绑定')));
		}

		if ($card['status'] == 1) {
			$usercard = pdo_get(PDO_NAME . 'halfcardmember', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid']), array('id'));

			if (!empty($usercard)) {
				$cardsn = pdo_getcolumn(PDO_NAME . 'halfcard_realcard', array('cardid' => $usercard['id']), 'cardsn');

				if (!empty($cardsn)) {
					exit(json_encode(array('errno' => 1, 'msg' => '您已绑定过实卡，请勿重复绑定')));
				}
			}

			$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'aid' => $_W['aid'], 'expiretime' => time() + $card['days'] * 3600 * 24, 'createtime' => time(), 'levelid' => $card['levelid'], 'disable' => 0, 'username' => $_GPC['cardname'], 'mototype' => trim($_GPC['mototype']), 'platenumber' => trim($_GPC['platenumber']), 'from' => 2);
			pdo_insert(PDO_NAME . 'halfcardmember', $data);
			$cardid = pdo_insertid();

			if (0 < $cardid) {
				pdo_update('wlmerchant_member', array('mobile' => $_GPC['mobile']), array('id' => $_W['mid']));
				$res2 = pdo_update('wlmerchant_halfcard_realcard', array('cardid' => $cardid, 'status' => 2, 'bindtime' => time()), array('id' => $card['id']));
			}
			else {
				exit(json_encode(array('errno' => 1, 'msg' => '生成会员卡失败，请重试或联系管理员')));
			}

			if ($res2) {
				exit(json_encode(array('errno' => 0)));
			}
			else {
				exit(json_encode(array('errno' => 1, 'msg' => '操作错误，请联系管理员')));
			}
		}
		else if ($card['status'] == 2) {
			exit(json_encode(array('errno' => 1, 'msg' => '此卡已绑定')));
		}
		else {
			if ($card['status'] == 3) {
				exit(json_encode(array('errno' => 1, 'msg' => '此卡已失效')));
			}
		}
	}

	public function realcard()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '绑定' . $_W['wlsetting']['halfcard']['text']['halfcardtext'] . ' - ' . $_W['wlsetting']['base']['name'] : '绑定' . $_W['wlsetting']['halfcard']['text']['halfcardtext'];
		$cardsn = trim($_GPC['cardsn']);
		$salt = trim($_GPC['salt']);
		$card = pdo_get(PDO_NAME . 'halfcard_realcard', array('uniacid' => $_W['uniacid'], 'cardsn' => $cardsn, 'salt' => $salt));
		$settings = Setting::wlsetting_read('halfcard');

		if (empty($card)) {
			wl_message('参数错误，请重新获取二维码！', 'close');
		}

		Member::checklogin(app_url('halfcard/halfcard_app/realcard', array('cardsn' => $cardsn, 'salt' => $salt)));
		Halfcard::checkFollow($card['id']);

		if ($card['icestatus']) {
			wl_message('此卡已被冻结,无法使用', 'close', 'error');
		}

		if ($card['status'] == 1) {
			if ($settings['morecard']) {
				$halfcard = Member::checkhalfmember();

				if ($halfcard) {
					wl_message('您已拥有正在生效的特权卡', 'close', 'error');
				}
			}

			$limittime = time() + $card['days'] * 3600 * 24;
			$limittime = date('Y-m-d', $limittime);
			include wl_template('newcard/bindcard');
		}

		if ($card['status'] == 2) {
			$cardmid = pdo_getcolumn(PDO_NAME . 'halfcardmember', array('id' => $card['cardid']), 'mid');

			if ($cardmid == $_W['mid']) {
				header('Location: ' . app_url('halfcard/halfcard_app/userhalfcard'));
				exit();
			}

			$verifier = pdo_get('wlmerchant_merchantuser', array('mid' => $_W['mid']), array('storeid'));

			if ($verifier) {
				header('Location: ' . app_url('halfcard/halfcard_app/usetimescard', array('cardid' => $card['cardid'])));
				exit();
			}
			else {
				header('Location: ' . app_url('halfcard/halfcardopen/open'));
				exit();
			}
		}

		if ($card['status'] == 3) {
			wl_message('抱歉，此卡已失效！', 'close', 'error');
		}
	}

	public function getpackage()
	{
		global $_W;
		global $_GPC;
		$packid = $_GPC['packid'];

		if ($packid) {
			$cardid = pdo_getcolumn('wlmerchant_halfcardmember', array('mid' => $_W['mid'], 'expiretime >' => time()), 'id');

			if (empty($cardid)) {
				exit(json_encode(array('errno' => 2, 'msg' => '请先开通或续费会员')));
			}

			$packactive = pdo_get('wlmerchant_package', array('id' => $packid));
			$card = pdo_get('wlmerchant_halfcardmember', array('id' => $cardid), array('createtime'));
			$zerotime = strtotime(date('Y-m-d'), time());

			if ($packactive['timeslimit']) {
				$times2 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $packactive['id'] . ' AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
				$surplus = $packactive['timeslimit'] - $times2;

				if ($surplus < 1) {
					exit(json_encode(array('errno' => 1, 'msg' => '今天已送完，请明天再试')));
				}
			}

			if ($packactive['oplimit']) {
				$zerotime = strtotime(date('Y-m-d'), time());
				$times3 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $packactive['id'] . ' AND mid = ' . $_W['mid'] . '  AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
				$surplus = $packactive['oplimit'] - $times3;

				if ($surplus < 1) {
					exit(json_encode(array('errno' => 1, 'msg' => '您今天只能领取该礼包' . $packactive['oplimit'] . '次')));
				}
			}

			if ($packactive['weeklimit']) {
				$zerotime = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
				$times3 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $packactive['id'] . ' AND mid = ' . $_W['mid'] . '  AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
				$surplus = $packactive['weeklimit'] - $times3;

				if ($surplus < 1) {
					exit(json_encode(array('errno' => 1, 'msg' => '您本周只能领取该礼包' . $packactive['weeklimit'] . '次')));
				}
			}

			if ($packactive['monthlimit']) {
				$zerotime = mktime(0, 0, 0, date('m'), 1, date('Y'));
				$times3 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE activeid = ' . $packactive['id'] . ' AND mid = ' . $_W['mid'] . '  AND usetime > ' . $zerotime . ' AND aid = ' . $_W['aid'] . ' AND type = 2'));
				$surplus = $packactive['monthlimit'] - $times3;

				if ($surplus < 1) {
					exit(json_encode(array('errno' => 1, 'msg' => '您当月只能领取该礼包' . $packactive['monthlimit'] . '次')));
				}
			}

			if ($packactive['datestatus'] == 4) {
				$begin = time() - 31536000;
			}
			else if ($packactive['datestatus'] == 3) {
				$begin = mktime(0, 0, 0, date('m'), 1, date('Y'));
			}
			else if ($packactive['datestatus'] == 2) {
				$begin = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
			}
			else {
				$begin = 0;
			}

			if ($begin < $card['createtime'] && $packactive['resetswitch']) {
				$begin = $card['createtime'];
			}

			$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_timecardrecord') . (' WHERE cardid = ' . $cardid . ' AND activeid = ' . $packactive['id'] . ' AND type = 2 AND merchantid = ' . $packactive['merchantid'] . ' AND usetime > ' . $begin));
			$surplustimes = $packactive['usetimes'] - $times;

			if ($surplustimes < 1) {
				exit(json_encode(array('errno' => 1, 'msg' => '该礼包您已全部领取')));
			}

			$data = array('uniacid' => $packactive['uniacid'], 'aid' => $packactive['aid'], 'mid' => $_W['mid'], 'type' => 2, 'cardid' => $cardid, 'activeid' => $packid, 'merchantid' => $packactive['merchantid'], 'createtime' => time());
			$flagtime = time() - 5;
			$flag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_timecardrecord') . ('WHERE cardid = ' . $cardid . ' AND activeid = ' . $packid . ' AND type = 2 AND createtime > ' . $flagtime . ' '));

			if (empty($flag)) {
				$res = pdo_insert(PDO_NAME . 'timecardrecord', $data);
			}
			else {
				$res = 1;
			}

			if ($res) {
				exit(json_encode(array('errno' => 0, 'msg' => '礼包领取成功')));
			}
			else {
				exit(json_encode(array('errno' => 1, 'msg' => '礼包领取失败，请重试')));
			}
		}
		else {
			exit(json_encode(array('errno' => 1, 'msg' => '礼包信息错误，请重试')));
		}
	}

	public function userrightpage()
	{
		global $_W;
		global $_GPC;
		include wl_template('halfcardhtml/userrightpage');
	}

	/**
     * Comment: 首页选项卡/diy选项卡风格一的礼包列表
     * Author: zzw
     */
	public function getPackageList()
	{
		global $_W;
		global $_GPC;
		$plugin = Setting::agentsetting_read('pluginlist');
		$plugin['gplimit'] = $plugin['gplimit'] ? $plugin['gplimit'] : 20;
		$where = ' AND a.uniacid = ' . $_W['uniacid'] . ' AND a.aid IN (0,' . $_W['aid'] . ') ';

		switch ($plugin['gpsort']) {
		case '1':
			$where .= ' ORDER BY a.createtime DESC ';
			$limit = ' LIMIT 0,' . $plugin['gplimit'] . ' ';
			break;

		case '2':
			break;

		case '3':
			$where .= ' ORDER BY a.sort DESC ';
			$limit = ' LIMIT 0,' . $plugin['gplimit'] . ' ';
			break;

		case '4':
			$where .= ' ORDER BY a.pv DESC ';
			$limit = ' LIMIT 0,' . $plugin['gplimit'] . ' ';
			break;

		default:
			$where .= ' ORDER BY a.createtime DESC ';
			$limit = ' LIMIT 0,' . $plugin['gplimit'] . ' ';
			break;
		}

		$goods = pdo_fetchall('SELECT a.id,a.type,REPLACE(\'table\',\'table\',\'package\') as `plugin` FROM ' . tablename(PDO_NAME . 'package') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.merchantid = b.id WHERE a.status = 1  ' . $where . ' ' . $limit));

		foreach ($goods as $k => &$v) {
			if ($v['type'] == 1) {
				$v = pdo_get('wlmerchant_package', array('id' => $v['id']));
				$info = unserialize($v['extinfo']);
				$v['storename'] = $info['storename'];
				$v['logo'] = tomedia($info['storelogo']);
				$v['storename'] = $info['storename'];
			}
			else {
				$v = WeChat::getHomeGoods(4, $v['id']);
				$v['title'] = $v['name'];
			}
		}

		wl_json(1, '首页选项卡礼包信息', $goods);
	}
}

defined('IN_IA') || exit('Access Denied');

?>
