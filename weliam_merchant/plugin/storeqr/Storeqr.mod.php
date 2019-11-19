<?php
//dezend by http://www.sucaihuo.com/
class Storeqr
{
	static public function auto_createqr($sid = '')
	{
		global $_W;

		if (!empty($sid)) {
			self::qr_createkeywords();
			$return = self::creatstoreqr(2);

			if ($return['result'] == 1) {
				$qrid = $return['qrid'];
				$qrcode = pdo_get(PDO_NAME . 'qrcode', array('uniacid' => $_W['uniacid'], 'qrid' => $qrid));
				pdo_update(PDO_NAME . 'qrcode', array('sid' => $sid, 'status' => 2), array('id' => $qrcode['id']));
				pdo_update(PDO_NAME . 'merchantdata', array('cardsn' => $qrcode['cardsn']), array('id' => $sid));
				return true;
			}

			pdo_update('wlmerchant_merchantdata', array('autostoreqr' => 1), array('id' => $sid));
		}

		return false;
	}

	static public function creatstoreqr($qrctype = 2, $agentid = -1, $remark = '自动获取')
	{
		global $_W;
		global $_GPC;
		load()->func('communication');
		$barcode = array(
			'expire_seconds' => '',
			'action_name'    => '',
			'action_info'    => array(
				'scene' => array()
			)
		);
		$qrcid = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'qrcode') . ' WHERE uniacid = :uniacid AND model in(2,3) ', array(':uniacid' => $_W['uniacid']));
		$sstr = !empty($qrcid) ? $qrcid + 1 : 1;

		if ($qrctype == 2) {
			$uniacccount = WeAccount::create($_W['uniacid']);

			if ($uniacccount['errno'] == -1) {
				$result = $uniacccount;
			}
			else {
				$scene_str = 'STOREQR' . $sstr;
				$is_exist = pdo_fetchcolumn('SELECT id FROM ' . tablename('qrcode') . ' WHERE uniacid = :uniacid AND scene_str = :scene_str AND model = 2', array(':uniacid' => $_W['uniacid'], ':scene_str' => $scene_str));

				if (!empty($is_exist)) {
					$scene_str = 'YJ' . date('YmdHis') . rand(1000, 9999);
				}

				$barcode['action_info']['scene']['scene_str'] = $scene_str;
				$barcode['action_name'] = 'QR_LIMIT_STR_SCENE';
				$result = $uniacccount->barCodeCreateFixed($barcode);
			}
		}

		if (!is_error($result)) {
			$insert = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'qrcid' => $barcode['action_info']['scene']['scene_id'], 'scene_str' => $barcode['action_info']['scene']['scene_str'], 'keyword' => 'weliam_merchant_storeqr', 'name' => '智慧城市商户二维码', 'model' => $qrctype, 'ticket' => $result['ticket'], 'url' => $result['url'], 'expire' => $result['expire_seconds'], 'createtime' => TIMESTAMP, 'status' => '1', 'type' => 'scene');
			pdo_insert('qrcode', $insert);
			$qrid = pdo_insertid();
			$qrinsert = array('uniacid' => $_W['uniacid'], 'aid' => $agentid, 'qrid' => $qrid, 'model' => $qrctype, 'cardsn' => 'STOREQR-' . $sstr, 'salt' => random(8), 'createtime' => TIMESTAMP, 'status' => '1', 'remark' => $remark);
			pdo_insert(PDO_NAME . 'qrcode', $qrinsert);
			return array('result' => 1, 'qrid' => $qrid);
		}

		$success = '公众平台返回接口错误. <br />错误代码为: ' . $result['errorcode'] . ' <br />错误信息为: ' . $result['message'];
		return array('result' => 2, 'message' => $success);
	}

	static public function qr_createkeywords()
	{
		global $_W;
		$rid = pdo_fetchcolumn('select id from ' . tablename('rule') . 'where uniacid=:uniacid and module=:module and name=:name', array(':uniacid' => $_W['uniacid'], ':module' => 'weliam_merchant', ':name' => '智慧城市商户二维码'));

		if (empty($rid)) {
			$rule_data = array('uniacid' => $_W['uniacid'], 'name' => '智慧城市商户二维码', 'module' => 'weliam_merchant', 'displayorder' => 0, 'status' => 1);
			pdo_insert('rule', $rule_data);
			$rid = pdo_insertid();
			$keyword_data = array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => 'weliam_merchant', 'content' => 'weliam_merchant_storeqr', 'type' => 1, 'displayorder' => 0, 'status' => 1);
			pdo_insert('rule_keyword', $keyword_data);
		}

		return $rid;
	}

	static public function check_qrcode($cardsn, $salt)
	{
		global $_W;
		if (empty($cardsn) || empty($salt)) {
			wl_message('二维码缺少关键参数，请联系管理员进行处理！', 'close');
		}

		$qrcode = pdo_get(PDO_NAME . 'qrcode', array('cardsn' => $cardsn, 'uniacid' => $_W['uniacid']));
		if (empty($qrcode) || $qrcode['salt'] != $salt) {
			wl_message('二维码不存在或存在异常，请联系管理员进行处理！', 'close');
		}

		return $qrcode;
	}

	static public function filter_url($params)
	{
		global $_W;

		if (empty($params)) {
			return '';
		}

		$query_arr = array();
		$parse = parse_url($_W['siteurl']);

		if (!empty($parse['query'])) {
			$query = $parse['query'];
			parse_str($query, $query_arr);
		}

		$params = explode(',', $params);

		foreach ($params as $val) {
			if (!empty($val)) {
				$data = explode(':', $val);
				$query_arr[$data[0]] = trim($data[1]);
			}
		}

		$query_arr['page'] = 1;
		$query = http_build_query($query_arr);
		return './agent.php?' . $query;
	}

	static public function Processor($message)
	{
		global $_W;

		if (strtolower($message['msgtype']) == 'event') {
			$returnmess = array();
			$qrid = self::get_qrid($message);
			$data = self::get_member($message, $qrid);
			$card = $data['card'];
			$storedata = $data['store'];
			$aid = $storedata['aid'];
			$member = $data['member'];
			$base = Setting::wlsetting_read('storeqr');

			if ($card['status'] == 1) {
				$returnmess[] = array('title' => '店铺快速入驻', 'description' => '', 'picurl' => tomedia($base['enterfast']), 'url' => app_url('store/supervise/information', array('applyflag' => 1, 'cardsn' => $card['cardsn'], 'salt' => $card['salt'], 'aid' => $card['aid'])));

				if ($member['storeid']) {
					$returnmess[] = array('title' => '店铺二维码绑定', 'description' => '', 'picurl' => tomedia($base['binding']), 'url' => app_url('storeqr/bdstoreqr/binding', array('cardsn' => $card['cardsn'], 'salt' => $card['salt'], 'aid' => $card['aid'])));
				}

				return array('type' => 'news', 'data' => $returnmess);
			}

			if ($card['status'] == 2) {
				$returnmess[] = array('title' => $storedata['storename'], 'description' => $storedata['address'], 'picurl' => tomedia($storedata['logo']), 'url' => app_url('store/merchant/detail', array('id' => $storedata['id'], 'aid' => $aid)));
				$halfcard = pdo_get(PDO_NAME . 'halfcardlist', array('merchantid' => $storedata['id'], 'status' => 1), array('title', 'id', 'activediscount'));
				if (!empty($halfcard) && empty($storedata['payonline'])) {
					$returnmess[] = array('title' => '【折扣】' . $halfcard['title'], 'description' => '', 'picurl' => tomedia($storedata['logo']), 'url' => app_url('halfcard/halfcard_app/userhalfcard', array('actid' => $halfcard['id'], 'type' => 1, 'useflag' => 1, 'aid' => $aid)));
				}

				if ($storedata['payonline'] == 1) {
					if (!empty($halfcard)) {
						$payonlinename = '【买单】在线优惠买单(最低' . $halfcard['activediscount'] . '折)';
					}
					else {
						$payonlinename = '【买单】在线买单';
					}

					$returnmess[] = array('title' => $payonlinename, 'description' => '', 'picurl' => tomedia($storedata['logo']), 'url' => app_url('order/paybill/paycheck', array('id' => $storedata['id'], 'aid' => $aid)));
				}

				$package = pdo_get(PDO_NAME . 'package', array('merchantid' => $storedata['id'], 'status' => 1), array('id', 'title'));

				if (!empty($package)) {
					$returnmess[] = array('title' => '【大礼包】' . $package['title'], 'description' => '', 'picurl' => tomedia($storedata['logo']), 'url' => app_url('halfcard/halfcard_app/userhalfcard', array('actid' => $package['id'], 'type' => 2, 'useflag' => 1, 'aid' => $aid)));
				}

				$rush = pdo_get(PDO_NAME . 'rush_activity', array('sid' => $storedata['id'], 'status' => 2), array('name', 'thumb', 'id'), '', '', 'sort DESC');

				if (!empty($rush)) {
					$returnmess[] = array('title' => '【抢购】' . $rush['name'], 'description' => '', 'picurl' => tomedia($rush['thumb']), 'url' => app_url('rush/home/detail', array('id' => $rush['id'], 'aid' => $aid)));
				}

				$groupon = pdo_get(PDO_NAME . 'groupon_activity', array('sid' => $storedata['id'], 'status' => 2), array('name', 'thumb', 'id'), '', '', 'sort DESC');

				if (!empty($groupon)) {
					$returnmess[] = array('title' => '【团购】' . $groupon['name'], 'description' => '', 'picurl' => tomedia($groupon['thumb']), 'url' => app_url('groupon/grouponapp/groupondetail', array('cid' => $groupon['id'], 'aid' => $aid)));
				}

				$bargain = pdo_get(PDO_NAME . 'bargain_activity', array('sid' => $storedata['id'], 'status' => 2), array('name', 'thumb', 'id'), '', '', 'sort DESC');

				if (!empty($bargain)) {
					$returnmess[] = array('title' => '【砍价】' . $bargain['name'], 'description' => '', 'picurl' => tomedia($bargain['thumb']), 'url' => app_url('bargain/bargain_app/bargaindetail', array('cid' => $bargain['id'], 'aid' => $aid)));
				}

				$coupon = pdo_get(PDO_NAME . 'couponlist', array('merchantid' => $storedata['id'], 'status' => 1), array('title', 'logo', 'id'), '', '', 'indexorder DESC');

				if (!empty($coupon)) {
					$returnmess[] = array('title' => '【卡券】' . $coupon['title'], 'description' => '', 'picurl' => tomedia($coupon['logo']), 'url' => app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $coupon['id'], 'aid' => $aid)));
				}

				$fightgroup = pdo_get(PDO_NAME . 'fightgroup_goods', array('merchantid' => $storedata['id'], 'status' => 1), array('name', 'logo', 'id'), '', '', 'listorder DESC');

				if (!empty($fightgroup)) {
					$returnmess[] = array('title' => '【拼团】' . $fightgroup['name'], 'description' => '', 'picurl' => tomedia($fightgroup['logo']), 'url' => app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $fightgroup['id'], 'aid' => $aid)));
				}

				$returnmess = array_slice($returnmess, 0, 7);
				return array('type' => 'news', 'data' => $returnmess);
			}

			if ($card['status'] == 3) {
				return array('type' => 'text', 'data' => '抱歉，此二维码已失效！');
			}
		}
	}

	static public function get_qrid($message)
	{
		global $_W;

		if (!empty($message['ticket'])) {
			if (is_numeric($message['scene'])) {
				$qrid = pdo_fetchcolumn('select id from ' . tablename('qrcode') . ' where uniacid=:uniacid and qrcid=:qrcid', array(':uniacid' => $_W['uniacid'], ':qrcid' => $message['scene']));
			}
			else {
				$qrid = pdo_fetchcolumn('select id from ' . tablename('qrcode') . ' where uniacid=:uniacid and scene_str=:scene_str', array(':uniacid' => $_W['uniacid'], ':scene_str' => $message['scene']));
			}

			if ($message['event'] == 'subscribe') {
				self::qr_log($qrid, $message['from'], 1);
			}
			else {
				self::qr_log($qrid, $message['from'], 2);
			}
		}
		else {
			return array('type' => 'text', 'data' => '欢迎关注我们!');
		}

		return $qrid;
	}

	static public function get_member($message, $qrid)
	{
		global $_W;
		$card = pdo_get(PDO_NAME . 'qrcode', array('uniacid' => $_W['uniacid'], 'qrid' => $qrid));
		$_W['aid'] = $card['aid'];
		$member = pdo_get(PDO_NAME . 'member', array('uniacid' => $_W['uniacid'], 'openid' => $message['from']), array('id'));

		if (empty($member['id'])) {
			$member = array('uniacid' => $_W['uniacid'], 'openid' => $message['from'], 'createtime' => time());
			pdo_insert(PDO_NAME . 'member', $member);
			$member['id'] = pdo_insertid();
		}

		$member['storeid'] = pdo_getcolumn(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'mid' => $member['id'], 'status' => 2, 'enabled' => 1), 'storeid');

		if ($card['sid']) {
			$storedata = pdo_get(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $card['sid']), array('id', 'storename', 'aid', 'logo', 'address', 'payonline'));
			Store::addFans($storedata['id'], $member['id'], 3);

			if (p('distribution')) {
				$adminmid = pdo_getcolumn(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'storeid' => $card['sid'], 'ismain' => 1), 'mid');

				if ($adminmid) {
					Distribution::addJunior($adminmid, $member['id']);
				}
			}
		}

		return array('card' => $card, 'store' => $storedata, 'member' => $member);
	}

	static public function qr_log($qrid, $openid, $type)
	{
		global $_W;
		if (empty($qrid) || empty($openid)) {
			return NULL;
		}

		$qrcode = pdo_get('qrcode', array('id' => $qrid), array('scene_str', 'name'));
		$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'qid' => $qrid, 'openid' => $openid, 'type' => $type, 'scene_str' => $qrcode['scene_str'], 'name' => $qrcode['name'], 'createtime' => time());
		pdo_insert('qrcode_stat', $log);
	}

	static public function doTask()
	{
		global $_W;
		$stores = pdo_fetchall('select id,uniacid from ' . tablename(PDO_NAME . 'merchantdata') . 'where (cardsn = \'\' or cardsn is null) and enabled = 1 and status = 2 and autostoreqr = 0 ORDER BY id DESC LIMIT 0,10');

		if ($stores) {
			foreach ($stores as $key => $value) {
				$_W['uniacid'] = $value['uniacid'];
				$settings = Setting::wlsetting_read('storeqr');

				if ($settings['autostatus'] == 1) {
					self::auto_createqr($value['id']);
				}
				else {
					pdo_update('wlmerchant_merchantdata', array('autostoreqr' => 1), array('id' => $value['id']));
				}
			}
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
