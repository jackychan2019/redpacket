<?php
//dezend by http://www.sucaihuo.com/
class PayResult
{
	static public function main($params, $wxapp = '')
	{
		global $_W;
		global $_GPC;
		define('IN_APP', true);

		if ($wxapp == 1) {
			define('IN_WXAPP', 'wxapp');
		}

		if ($params['result'] == 'success') {
			$_W['uniacid'] = $params['uniacid'] ? $params['uniacid'] : $_W['uniacid'];
			$_W['acid'] = pdo_getcolumn('account_wechats', array('uniacid' => $_W['uniacid']), 'acid');
			$log = pdo_get(PDO_NAME . 'paylog', array('tid' => $params['tid'], 'uniacid' => $_W['uniacid']));
			$className = $log['plugin'];
			$ret = array();
			$ret['weid'] = $log['uniacid'];
			$ret['uniacid'] = $log['uniacid'];
			$ret['result'] = 'success';
			$ret['type'] = $params['type'];
			$ret['tid'] = $log['tid'];
			$ret['uniontid'] = $params['uniontid'];
			$ret['user'] = $log['openid'];
			$ret['fee'] = $log['fee'];
			$ret['tag'] = $params['tag'];
			$ret['is_usecard'] = $log['is_usecard'];
			$ret['card_type'] = $log['card_type'];
			$ret['card_fee'] = $log['card_fee'];
			$ret['card_id'] = $log['card_id'];

			if ($params['from'] == 'notify') {
				$tid = $params['tid'];

				if ($log['plugin'] == 'Rush') {
					$orderInfo = pdo_get(PDO_NAME . 'rush_order', array('orderno' => $tid), array('mid', 'aid', 'vip_card_id'));
				}
				else {
					$orderInfo = pdo_get(PDO_NAME . 'order', array('orderno' => $tid), array('mid', 'aid', 'vip_card_id'));
				}

				if (0 < $orderInfo['vip_card_id'] && !empty($orderInfo['vip_card_id'])) {
					$halftype = pdo_get(PDO_NAME . 'halfcard_type', array('id' => $orderInfo['vip_card_id']));
					$userInfo = pdo_get(PDO_NAME . 'member', array('id' => $orderInfo['mid']), array('nickname', 'mobile'));
					$cardid = $orderInfo['vip_card_id'];
					$username = $userInfo['nickname'];
					$mobile = $userInfo['mobile'];

					if ($cardid) {
						$mdata = array('uniacid' => $_W['uniacid'], 'mid' => $orderInfo['mid'], 'id' => $cardid);
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

					$data = array('aid' => $orderInfo['aid'], 'uniacid' => $_W['uniacid'], 'mid' => $orderInfo['mid'], 'orderno' => createUniontid(), 'status' => 1, 'createtime' => TIMESTAMP, 'price' => $halftype['price'], 'limittime' => $limittime, 'typeid' => $halftype['id'], 'howlong' => $halftype['days'], 'todistributor' => $halftype['todistributor'], 'cardid' => $cardid, 'username' => $username, 'mobile' => $mobile);
					$paytype = array('credit' => 1, 'wechat' => 2, 'alipay' => 3, 'delivery' => 4, 'wxapp' => 5);
					$data['paytype'] = $paytype[$params['type']];
					$data['paytime'] = time();
					pdo_insert(PDO_NAME . 'halfcard_record', $data);
					$recordid = pdo_insertid();
					if (p('distribution') && empty($halftype['isdistri'])) {
						$_W['aid'] = $orderInfo['aid'];
						$disorderid = Distribution::disCore($orderInfo['mid'], $data['price'], $halftype['onedismoney'], $halftype['twodismoney'], $halftype['threedismoney'], $recordid, 'halfcard', 1);
						pdo_update(PDO_NAME . 'halfcard_record', array('disorderid' => $disorderid), array('id' => $recordid));
					}

					$halfcarddata = array('uniacid' => $_W['uniacid'], 'aid' => $data['aid'], 'mid' => $data['mid'], 'expiretime' => $data['limittime'], 'username' => $data['username'], 'levelid' => $halftype['levelid'], 'createtime' => time());
					pdo_insert(PDO_NAME . 'halfcardmember', $halfcarddata);
				}

				pdo_update(PDO_NAME . 'paylog', array('status' => 1, 'type' => $params['type']), array('tid' => $params['tid'], 'uniacid' => $_W['uniacid']));
				$ret['from'] = 'notify';
				$functionName = 'pay' . $log['payfor'] . 'Notify';
				$className::{$functionName}($ret);
			}

			if ($params['from'] == 'return') {
				$ret['from'] = 'return';
				$functionName2 = 'pay' . $log['payfor'] . 'Return';
				$className::{$functionName2}($ret);
			}
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
