<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Halfcardopen_WeliamController extends Weliam_merchantModuleSite
{
	public function __construct()
	{
		global $_W;

		if (empty($_W['wlsetting']['halfcard']['status'])) {
			header('location:' . app_url('dashboard/home/index'));
		}
	}

	public function open()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '开通' . $_W['wlsetting']['halfcard']['text']['halfcardtext'] . ' - ' . $_W['wlsetting']['base']['name'] : '开通' . $_W['wlsetting']['halfcard']['text']['halfcardtext'];
		$base = Setting::wlsetting_read('halfcard');
		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
		Member::checklogin(app_url('halfcard/halfcardopen/open'));
		$type = $_GPC['type'] ? $_GPC['type'] : 1;
		$areaid = pdo_getcolumn(PDO_NAME . 'oparea', array('aid' => $_W['aid']), 'areaid');
		$areaname = pdo_getcolumn(PDO_NAME . 'area', array('id' => $areaid), 'name');
		$selectregionurl = app_url('area/region/select_region', array('backurl' => app_url('halfcard/halfcardopen/open')));
		$activityid = $_GPC['activityid'];
		$activitytype = $_GPC['activitytype'];
		$cardwhere = ' WHERE status = 1 AND uniacid = ' . $_W['uniacid'] . ' ';
		$cardid = $_GPC['id'];
		if (empty($cardid) && $base['morecard'] == 1 && $base['halfcardtype'] == 1) {
			$cardid = pdo_getcolumn(PDO_NAME . 'halfcardmember', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid']), 'id');
		}

		if ($cardid) {
			$cardwhere .= ' AND renew IN (0,2)';
		}
		else {
			$cardwhere .= ' AND renew != 2';
		}

		if ($cardid && $base['renewstatus']) {
			$levelid = pdo_getcolumn(PDO_NAME . 'halfcardmember', array('id' => $cardid), 'levelid');
			$cardwhere .= ' AND levelid = ' . $levelid;
		}

		if ($base['halfcardtype'] == 2) {
			$cardwhere .= ' AND aid IN (0,' . $_W['aid'] . ')';
		}

		$cards = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halfcard_type') . $cardwhere . ' ORDER BY sort DESC');

		if (empty($cards)) {
			$type = 2;
		}

		if (empty($_GPC['qrentry']) && !empty($cards)) {
			foreach ($cards as $kk => $vv) {
				if ($vv['qrshow'] == 1) {
					unset($cards[$kk]);
				}
			}
		}

		if ($activityid) {
			if ($activitytype == 1) {
				$level = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('id' => $activityid), 'level');
			}
			else {
				$level = pdo_getcolumn(PDO_NAME . 'package', array('id' => $activityid), 'level');
			}

			$level = unserialize($level);
		}

		if ($level) {
			foreach ($cards as $key => $va) {
				if (!in_array($va['levelid'], $level)) {
					unset($cards[$key]);
				}
			}

			$cards = array_values($cards);
		}

		if ($cards && empty($cardid)) {
			foreach ($cards as $key => &$v) {
				$consum = pdo_get('wlmerchant_consumption_goods', array('halfcardid' => $v['id'], 'status' => 1), array('id'));
				$v['consumid'] = $consum['id'];
			}
		}

		if (empty($cardid) && $base['morecard']) {
			if ($base['halfcardtype'] == 2) {
				$cardid = pdo_getcolumn(PDO_NAME . 'halfcardmember', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid']), 'id');
			}
			else {
				if ($base['halfcardtype'] == 1) {
					$cardid = pdo_getcolumn(PDO_NAME . 'halfcardmember', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid']), 'id');
				}
			}
		}

		if ($cardid) {
			$user = pdo_get(PDO_NAME . 'halfcardmember', array('id' => $cardid), array('username', 'mid', 'expiretime'));

			if ($user['mid'] != $_W['mid']) {
				wl_message('用户不匹配,请从个人中心进入', 'close', 'error');
			}

			$expiretime = date('Y年m月d日', $user['expiretime']);
			$username = $user['username'];
			$avatar = pdo_getcolumn(PDO_NAME . 'member', array('id' => $user['mid']), 'avatar');
		}
		else {
			$username = $_W['wlmember']['nickname'];
		}

		$mobile = $_W['wlmember']['mobile'];
		$backurl = urlencode($_W['siteurl']);
		include wl_template('newcard/opencard');
	}

	public function createOrder()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
			if (empty($_W['wlmember']['mobile']) && in_array('halfcard', $mastmobile)) {
				wl_message(array('errno' => 2, 'message' => '未绑定手机号，去绑定？'));
			}

			$base = Setting::wlsetting_read('halfcard');

			if ($base['status']) {
				$halftype = pdo_get(PDO_NAME . 'halfcard_type', array('id' => $_GPC['radioValue']));

				if (empty($halftype)) {
					wl_message(array('errno' => 1, 'message' => '选择的充值类型错误，请重试'));
				}

				if ($halftype['num']) {
					$times = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename('wlmerchant_halfcard_record') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $_W['mid'] . ' AND status = 1 AND typeid = ' . $halftype['id']));
					if ($halftype['num'] < $times || $times == $halftype['num']) {
						wl_message(array('errno' => 1, 'message' => '选择的充值卡最多充值' . $halftype['num'] . '次。'));
					}
				}

				$cardid = $_GPC['cardid'];
				$username = $_GPC['username'];
				$mobile = $_W['wlmember']['mobile'];

				if ($cardid) {
					$mdata = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'id' => $cardid);
					$vipInfo = Util::getSingelData('*', PDO_NAME . 'halfcardmember', $mdata);
					$lastviptime = $vipInfo['expiretime'];
					if ($lastviptime && time() < $lastviptime) {
						$limittime = $lastviptime + $halftype['days'] * 24 * 60 * 60;
					}
					else {
						$limittime = time() + $halftype['days'] * 24 * 60 * 60;
					}
				}
				else {
					$limittime = time() + $halftype['days'] * 24 * 60 * 60;
				}

				$data = array('aid' => $_W['aid'], 'uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'orderno' => createUniontid(), 'status' => 0, 'createtime' => TIMESTAMP, 'price' => $halftype['price'], 'limittime' => $limittime, 'typeid' => $halftype['id'], 'howlong' => $halftype['days'], 'todistributor' => $halftype['todistributor'], 'cardid' => $cardid, 'username' => $username, 'mobile' => $mobile, 'mototype' => trim($_GPC['mototype']), 'platenumber' => trim($_GPC['platenumber']));

				if ($halftype['price'] < 0.01) {
					$data['status'] = 1;
					$data['paytime'] = time();
					$data['issettlement'] = 1;

					if (file_exists(IA_ROOT . '/addons/weliam_merchant/pTLjC21GjCGj.log')) {
						if (0 < $halftype['give_price']) {
							Member::credit_update_credit2($data['mid'], $halftype['give_price'], '一卡通赠送金额');
						}
					}

					$halfcarddata = array('uniacid' => $_W['uniacid'], 'aid' => $data['aid'], 'mid' => $data['mid'], 'expiretime' => $data['limittime'], 'username' => $data['username'], 'levelid' => $halftype['levelid'], 'createtime' => time(), 'mototype' => $data['mototype'], 'platenumber' => $data['platenumber']);

					if ($data['cardid']) {
						pdo_update(PDO_NAME . 'halfcardmember', $halfcarddata, array('id' => $data['cardid']));
					}
					else {
						pdo_insert(PDO_NAME . 'halfcardmember', $halfcarddata);
					}

					$member = pdo_get('wlmerchant_member', array('id' => $halfcarddata['mid']), array('openid', 'mobile'));
					$openid = $member['openid'];
					$mobile = empty($member['mobile']) ? $data['mobile'] : $member['mobile'];

					if (empty($member['mobile'])) {
						pdo_update('wlmerchant_member', array('mobile' => $data['mobile']), array('id' => $halfcarddata['mid']));
					}

					$url = app_url('halfcard/halfcard_app/userhalfcard');
					$time = date('Y-m-d H:i:s', $halfcarddata['expiretime']);
					$settings = Setting::wlsetting_read('halfcard');
					$halftext = $settings['text']['halfcardtext'] ? $settings['text']['halfcardtext'] : '一卡通';
					Message::openSuccessNotice($openid, $halftext, $time, $mobile, $url);
					$nickname = $halfcarddata['username'];
					Message::openNoticeAdmin($nickname, $halftext, $time, $mobile);
					$base = Setting::wlsetting_read('distribution');
					if ($base['appdis'] == 2 && $base['switch'] && $base['together'] == 1) {
						$member = pdo_get('wlmerchant_member', array('id' => $data['mid']), array('mobile', 'nickname', 'realname', 'distributorid'));
						$distributor = pdo_get('wlmerchant_distributor', array('id' => $member['distributorid']));

						if ($distributor) {
							if (empty($distributor['disflag'])) {
								pdo_update('wlmerchant_distributor', array('disflag' => 1, 'createtime' => time()), array('mid' => $data['mid']));
							}
						}
						else {
							$data = array('uniacid' => $_W['uniacid'], 'aid' => $data['aid'], 'mid' => $data['mid'], 'createtime' => time(), 'disflag' => 1, 'nickname' => $member['nickname'], 'mobile' => $member['mobile'], 'realname' => $member['realname'], 'leadid' => -1);
							pdo_insert('wlmerchant_distributor', $data);
							$disid = pdo_insertid();
							pdo_update('wlmerchant_member', array('distributorid' => $disid), array('id' => $data['mid']));
						}
					}
				}

				pdo_insert(PDO_NAME . 'halfcard_record', $data);
				$halfid = pdo_insertid();

				if ($halftype['price'] < 0.01) {
					wl_message(array('errno' => 3, 'message' => $halfid));
				}
				else {
					wl_message(array('errno' => 0, 'message' => $halfid));
				}
			}
			else {
				wl_message(array('errno' => 1, 'message' => '功能已禁用'));
			}
		}

		$recordid = $_GPC['recordid'];
		$record = pdo_get('wlmerchant_halfcard_record', array('id' => $recordid));
		$halftypename = pdo_getcolumn(PDO_NAME . 'halfcard_type', array('id' => $record['typeid']), 'name');
		$params = array('tid' => $record['orderno'], 'ordersn' => $record['orderno'], 'title' => $halftypename, 'fee' => $record['price']);
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Merchant', 'payfor' => 'Halfcard', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}

	public function openSuccess()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '开通' . $_W['wlsetting']['halfcard']['text']['halfcardtext'] . '成功 - ' . $_W['wlsetting']['base']['name'] : '开通' . $_W['wlsetting']['halfcard']['text']['halfcardtext'] . '成功';
		$orderid = $_GPC['orderid'];
		$order = pdo_get('wlmerchant_halfcard_record', array('id' => $orderid), array('limittime', 'paidprid'));

		if ($order['paidprid']) {
			header('location:' . app_url('paidpromotion/paidapp/paiddetail', array('id' => $order['paidprid'])));
		}

		$limittime = date('Y年m月d日', $order['limittime']);
		include wl_template('halfcardhtml/open_success');
	}

	public function changename()
	{
		global $_W;
		global $_GPC;
		$cardid = $_GPC['cardid'];
		$newname = $_GPC['newname'];
		if (empty($cardid) || empty($newname)) {
			exit(json_encode(array('errno' => 1, 'msg' => '参数错误，请重试')));
		}
		else {
			$res = pdo_update('wlmerchant_halfcardmember', array('username' => $newname), array('id' => $cardid));

			if ($res) {
				exit(json_encode(array('errno' => 0)));
			}
			else {
				exit(json_encode(array('errno' => 1, 'msg' => '修改失败，请重试')));
			}
		}
	}
}

?>
