<?php
//dezend by http://www.sucaihuo.com/
class Weixinqrcode
{
	static public function createqrcode($name, $keyword, $ptype = 0, $qrctype = 2, $agentid = -1, $remark = '自动获取')
	{
		global $_W;
		global $_GPC;
		if (empty($name) || empty($keyword)) {
			return error('-1', '二维码关键字和名称不能为空');
		}

		load()->func('communication');
		$barcode = array(
			'expire_seconds' => '',
			'action_name'    => '',
			'action_info'    => array(
				'scene' => array()
			)
		);
		$scene_str = date('YmdHis') . rand(1000, 9999);
		$uniacccount = WeAccount::create($_W['acid']);

		if ($qrctype == 1) {
			$qrcid = pdo_fetchcolumn('SELECT qrcid FROM ' . tablename('qrcode') . ' WHERE acid = :acid AND model = \'1\' AND type = \'scene\' ORDER BY qrcid DESC LIMIT 1', array(':acid' => $_W['acid']));
			$barcode['action_info']['scene']['scene_id'] = !empty($qrcid) ? $qrcid + 1 : 100001;
			$barcode['expire_seconds'] = 2592000;
			$barcode['action_name'] = 'QR_SCENE';
			$result = $uniacccount->barCodeCreateDisposable($barcode);
		}
		else {
			if ($qrctype == 2) {
				$is_exist = pdo_fetchcolumn('SELECT id FROM ' . tablename('qrcode') . ' WHERE uniacid = :uniacid AND scene_str = :scene_str AND model = 2', array(':uniacid' => $_W['uniacid'], ':scene_str' => $scene_str));

				if (!empty($is_exist)) {
					$scene_str = date('YmdHis') . rand(1000, 9999);
				}

				$barcode['action_info']['scene']['scene_str'] = $scene_str;
				$barcode['action_name'] = 'QR_LIMIT_STR_SCENE';
				$result = $uniacccount->barCodeCreateFixed($barcode);
			}
		}

		if (!is_error($result)) {
			$insert = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'qrcid' => $barcode['action_info']['scene']['scene_id'], 'scene_str' => $barcode['action_info']['scene']['scene_str'], 'keyword' => $keyword, 'name' => $name, 'model' => $qrctype, 'ticket' => $result['ticket'], 'url' => $result['url'], 'expire' => $result['expire_seconds'], 'createtime' => TIMESTAMP, 'status' => '1', 'type' => 'scene');
			pdo_insert('qrcode', $insert);
			$qrid = pdo_insertid();
			$qrinsert = array('uniacid' => $_W['uniacid'], 'aid' => $agentid, 'qrid' => $qrid, 'type' => $ptype, 'model' => $qrctype, 'cardsn' => $scene_str, 'salt' => random(8), 'createtime' => TIMESTAMP, 'status' => '1', 'remark' => $remark);
			pdo_insert(PDO_NAME . 'qrcode', $qrinsert);
			return $qrid;
		}

		return $result;
	}

	static public function get_qrid($message)
	{
		global $_W;

		if (!empty($message['ticket'])) {
			if (is_numeric($message['scene']) && mb_strlen($message['scene']) != 18) {
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
			self::send_text('欢迎关注我们!', $message);
		}

		return $qrid;
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

	static public function createkeywords($name, $keyword)
	{
		global $_W;
		if (empty($name) || empty($keyword)) {
			return error('-1', '二维码关键字和名称不能为空');
		}

		$rid = pdo_fetchcolumn('select id from ' . tablename('rule') . 'where uniacid=:uniacid and module=:module and name=:name', array(':uniacid' => $_W['uniacid'], ':module' => 'weliam_merchant', ':name' => $name));

		if (empty($rid)) {
			$rule_data = array('uniacid' => $_W['uniacid'], 'name' => $name, 'module' => 'weliam_merchant', 'displayorder' => 0, 'status' => 1);
			pdo_insert('rule', $rule_data);
			$rid = pdo_insertid();
			$keyword_data = array('uniacid' => $_W['uniacid'], 'rid' => $rid, 'module' => 'weliam_merchant', 'content' => $keyword, 'type' => 1, 'displayorder' => 0, 'status' => 1);
			pdo_insert('rule_keyword', $keyword_data);
		}

		return $rid;
	}

	static public function send_news($returnmess, $message, $end = 1)
	{
		global $_W;

		if (1 < count($returnmess)) {
			$returnmess = array_slice($returnmess, 0, 1);
		}

		$send['touser'] = $message['from'];
		$send['msgtype'] = 'news';
		$send['news']['articles'] = $returnmess;
		$acc = WeAccount::create($_W['acid']);
		$data = $acc->sendCustomNotice($send);

		if (is_error($data)) {
			self::salerEmpty();
		}
		else {
			if ($end == 1) {
				self::salerEmpty();
			}
		}
	}

	static public function send_text($mess, $message, $end = 1)
	{
		global $_W;
		$send['touser'] = $message['from'];
		$send['msgtype'] = 'text';
		$send['text'] = array('content' => urlencode($mess));
		$acc = WeAccount::create($_W['acid']);
		$data = $acc->sendCustomNotice($send);

		if (is_error($data)) {
			self::salerEmpty();
		}
		else {
			if ($end == 1) {
				self::salerEmpty();
			}
		}
	}

	static public function send_wxapp($mess, $message, $end = 1)
	{
		global $_W;
		$send['touser'] = $message['from'];
		$send['msgtype'] = 'text';
		$send['text'] = array('content' => urlencode($mess));
		$acc = WeAccount::create($_W['acid']);
		$data = $acc->sendCustomNotice($send);

		if (is_error($data)) {
			self::salerEmpty();
		}
		else {
			if ($end == 1) {
				self::salerEmpty();
			}
		}
	}

	static public function salerEmpty()
	{
		ob_clean();
		ob_start();
		echo '';
		ob_flush();
		ob_end_flush();
		exit(0);
	}
}

defined('IN_IA') || exit('Access Denied');

?>
