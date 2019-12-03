<?php
//dezend by http://www.sucaihuo.com/
class Jkactive
{
	/**
     * 从数据库获取幻灯片数据
     */
	static public function getslides($uniacid)
	{
		global $_W;
		global $_GPC;
		$psize = 25;
		$pindex = max(1, $_GPC['page']);
		$data = Util::getNumData('*', PDO_NAME . 'pocket_slide', array('uniacid' => $uniacid, 'status' => 1), 'sort desc', $pindex, $psize);

		if ($data) {
			foreach ($data[0] as $k => $v) {
				if ($v['aid']) {
					if ($v['aid'] != $_W['aid']) {
						unset($data[0][$k]);
					}
				}
			}

			return $data;
		}
	}

	static public function saveFightOrder($data, $param = array())
	{
		global $_W;

		if (!is_array($data)) {
			return false;
		}

		if (empty($param)) {
			pdo_insert(PDO_NAME . 'order', $data);
			return pdo_insertid();
		}

		return false;
	}

	/**
     * 获取分类
     */
	static public function gettypes($flag = 0)
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];

		if ($flag == 'all') {
			$data = Util::getNumData('*', PDO_NAME . 'pocket_type', array('uniacid' => $uniacid, 'aid' => $_W['aid'], 'type' => 0), 'sort desc', 0, 0);
		}
		else {
			$data = Util::getNumData('*', PDO_NAME . 'pocket_type', array('uniacid' => $uniacid, 'aid' => $_W['aid'], 'type' => 0, 'isnav' => 0), 'sort desc', 0, 0);
		}

		if (!empty($data)) {
			$data = $data[0];

			foreach ($data as $key => $value) {
				if ($value['aid']) {
					if ($value['aid'] != $_W['aid']) {
						unset($data[$key]);
					}
					else {
						$temp = Util::getNumData('*', PDO_NAME . 'pocket_type', array('type' => $value['id']), 'sort desc');
						$data[$key]['children'] = $temp[0];
					}
				}
				else {
					$temp = Util::getNumData('*', PDO_NAME . 'pocket_type', array('type' => $value['id']), 'sort desc');
					$data[$key]['children'] = $temp[0];
				}
			}

			return $data;
		}
	}

	/**
     * 根据id检查某一级分类是否含有二级分类
     */
	static public function checkFType($id)
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$data = NULL;

		if ($id) {
			$data = Util::getSingelData('*', PDO_NAME . 'pocket_type', array('uniacid' => $uniacid, 'type' => $id));

			if ($data) {
				return true;
			}
		}

		return false;
	}

	/**
     * 获取发帖信息
     */
	static public function getInformations($id = 0)
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];

		if (!$id) {
			$data = Util::getNumData('*', PDO_NAME . 'pocket_informations', array('uniacid' => $uniacid));
		}
		else {
			$data = Util::getSingelData('*', PDO_NAME . 'pocket_informations', array('uniacid' => $uniacid, 'id' => $id));
		}

		if ($data) {
			return $data;
		}
	}

	/**
     * 根据帖子id获取评论
     */
	static public function getcomments($id)
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];

		if ($id) {
			$data = Util::getNumData('*', PDO_NAME . 'pocket_comment', array('uniacid' => $uniacid, 'tid' => $id));
		}

		if ($data) {
			return $data;
		}
	}

	/**
     * 根据评论id获取回复
     */
	static public function getreplys($id)
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];

		if ($id) {
			$data = Util::getNumData('*', PDO_NAME . 'pocket_reply', array('uniacid' => $uniacid, 'cid' => $id));
		}

		if ($data) {
			return $data;
		}
	}

	/**
     * 根据类型获得帖子
     */
	static public function getInfoByType($id)
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$data = NULL;

		if ($id) {
			$data = Util::getNumData('*', PDO_NAME . 'pocket_informations', array('type' => $id));
		}

		if ($data) {
			return $data;
		}
	}

	static public function payPocketshargeNotify($params)
	{
		global $_W;
		Util::wl_log('payResult_notify', PATH_PLUGIN . 'pocket/data/', $params);
		$order = pdo_get('wlmerchant_order', array('orderno' => $params['tid']), array('id', 'mid', 'num', 'price', 'orderno', 'fkid', 'aid', 'status'));

		if ($order['id']) {
			$inform = self::getInformations($order['fkid']);
			$type = pdo_get('wlmerchant_pocket_type', array('id' => $inform['type']), array('isdistri', 'onedismoney', 'twodismoney'));

			if (p('distribution')) {
				$_W['aid'] = $order['aid'];

				if (0 < $inform['redpack']) {
					$disprice = sprintf('%.2f', $order['price'] - $inform['redpack']);
				}
				else {
					$disprice = $order['price'];
				}

				if (0 < $disprice && empty($type['isdistri'])) {
					$disorderid = Distribution::disCore($order['mid'], $disprice, $type['onedismoney'], $type['twodismoney'], 0, $order['id'], 'pocket', 1);
				}
			}

			if (empty($disorderid)) {
				$disorderid = 0;
			}

			$data['disorderid'] = $disorderid;
			$data['status'] = 3;
			$data['paytime'] = time();
			$paytype = array('credit' => 1, 'wechat' => 2, 'alipay' => 3, 'delivery' => 4, 'wxapp' => 5);
			$data['paytype'] = $paytype[$params['type']];

			if ($params['tag']['transaction_id']) {
				$data['transid'] = $params['tag']['transaction_id'];
			}

			$res = pdo_update('wlmerchant_order', $data, array('id' => $order['id']));

			if ($res) {
				Store::ordersettlement($order['id']);
			}

			if (time() < $inform['endtime']) {
				$endtime = $inform['endtime'] + $order['num'] * 24 * 3600;
			}
			else {
				$endtime = time() + $order['num'] * 24 * 3600;
			}

			$data = array('top' => 1, 'endtime' => $endtime);

			if (0 < $inform['redpack']) {
				$data['redpackstatus'] = 1;
			}

			pdo_update('wlmerchant_pocket_informations', $data, array('id' => $order['fkid']));
		}
	}

	static public function payPocketshargeReturn($params)
	{
		$res = $params['result'] == 'success' ? 1 : 0;
		$order = pdo_get('wlmerchant_order', array('orderno' => $params['tid']), array('fkid'));

		if ($res) {
			wl_message('支付成功', app_url('pocket/pocket/detail', array('id' => $order['fkid'])), 'success');
		}
		else {
			wl_message('您已支付该订单', app_url('pocket/pocket/detail', array('id' => $order['fkid'])), 'error');
		}
	}

	static public function payPocketfabushargeNotify($params)
	{
		global $_W;
		Util::wl_log('payResult_notify', PATH_PLUGIN . 'pocket/data/', $params);
		$order = pdo_get('wlmerchant_order', array('orderno' => $params['tid']), array('id', 'mid', 'num', 'price', 'orderno', 'fkid', 'aid', 'status'));

		if ($order['id']) {
			$inform = self::getInformations($order['fkid']);

			if (empty($_W['aid'])) {
				$_W['aid'] = $order['aid'];
			}

			$set = Setting::agentsetting_read('pocket');
			$inf = array();

			if ($set['passstatus']) {
				$inf['status'] = 0;
			}
			else {
				$inf['status'] = 1;
			}

			$day = $order['num'] - 1;

			if (0 < $day) {
				$inf['top'] = 1;
				$inf['endtime'] = time() + $day * 24 * 3600;
			}

			if (0 < $inform['redpack']) {
				$inf['redpackstatus'] = 1;
			}

			pdo_update('wlmerchant_pocket_informations', $inf, array('id' => $order['fkid']));
			$type = pdo_get('wlmerchant_pocket_type', array('id' => $inform['type']), array('isdistri', 'onedismoney', 'twodismoney'));

			if (p('distribution')) {
				$_W['aid'] = $order['aid'];
				if (0 < $inform['redpack'] && $inform['redpackstatus'] == 0) {
					$disprice = sprintf('%.2f', $order['price'] - $inform['redpack']);
				}
				else {
					$disprice = $order['price'];
				}

				if (0 < $disprice && empty($type['isdistri'])) {
					$disorderid = Distribution::disCore($order['mid'], $disprice, $type['onedismoney'], $type['twodismoney'], 0, $order['id'], 'pocket', 1);
				}
			}

			if (empty($disorderid)) {
				$disorderid = 0;
			}

			$data = array('status' => 3, 'disorderid' => $disorderid, 'paytime' => time());
			$paytype = array('credit' => 1, 'wechat' => 2, 'alipay' => 3, 'delivery' => 4, 'wxapp' => 5);
			$data['paytype'] = $paytype[$params['type']];

			if ($params['tag']['transaction_id']) {
				$data['transid'] = $params['tag']['transaction_id'];
			}

			$res = pdo_update('wlmerchant_order', $data, array('id' => $order['id']));

			if ($res) {
				Store::ordersettlement($order['id']);
			}

			if ($inf['status']) {
				self::examinenotice($order['fkid']);
			}
			else {
				self::examinenotice($order['fkid'], 1);
			}
		}
	}

	static public function payPocketfabushargeReturn($params)
	{
		$res = $params['result'] == 'success' ? 1 : 0;
		$order = pdo_get('wlmerchant_order', array('orderno' => $params['tid']), array('fkid'));

		if ($res) {
			wl_message('支付成功', app_url('pocket/pocket/detail', array('id' => $order['fkid'])), 'success');
		}
		else {
			wl_message('您已支付该订单', app_url('pocket/pocket/detail', array('id' => $order['fkid'])), 'error');
		}
	}

	static public function payPocketredpackNotify($params)
	{
		global $_W;
		Util::wl_log('payResult_notify', PATH_PLUGIN . 'pocket/data/', $params);
		$order = pdo_get('wlmerchant_order', array('orderno' => $params['tid']), array('id', 'mid', 'num', 'price', 'orderno', 'fkid', 'aid', 'status', 'package'));

		if ($order['status'] == 0) {
			$inform = self::getInformations($order['fkid']);

			if (empty($_W['aid'])) {
				$_W['aid'] = $order['aid'];
			}

			pdo_update('wlmerchant_pocket_informations', array('redpackstatus' => 1), array('id' => $order['fkid']));
			$data = array('status' => 3, 'paytime' => time());
			$paytype = array('credit' => 1, 'wechat' => 2, 'alipay' => 3, 'delivery' => 4, 'wxapp' => 5);
			$data['paytype'] = $paytype[$params['type']];

			if ($params['tag']['transaction_id']) {
				$data['transid'] = $params['tag']['transaction_id'];
			}

			$res = pdo_update('wlmerchant_order', $data, array('id' => $order['id']));

			if ($res) {
				Store::ordersettlement($order['id']);
			}
		}
	}

	static public function payPocketredpackReturn($params)
	{
		$res = $params['result'] == 'success' ? 1 : 0;
		$order = pdo_get('wlmerchant_order', array('orderno' => $params['tid']), array('fkid'));

		if ($res) {
			wl_message('支付成功', app_url('pocket/pocket/detail', array('id' => $order['fkid'])), 'success');
		}
		else {
			wl_message('您已支付该订单', app_url('pocket/pocket/detail', array('id' => $order['fkid'])), 'error');
		}
	}

	static public function getNumTiezi($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'pocket_informations', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	static public function getNumOrder($select, $where, $order, $pindex, $psize, $ifpage)
	{
		$goodsInfo = Util::getNumData($select, PDO_NAME . 'order', $where, $order, $pindex, $psize, $ifpage);
		return $goodsInfo;
	}

	static public function commentnotice($commentid, $comment, $mid)
	{
		global $_W;
		$amid = pdo_getcolumn(PDO_NAME . 'pocket_informations', array('id' => $commentid), 'mid');
		$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $amid), 'openid');
		$smnane = pdo_getcolumn(PDO_NAME . 'member', array('id' => $mid), 'nickname');
		$msg = $smnane . '对您的帖子评论道:' . '
';
		$msg .= $comment . '
';
		$msg .= '快去看看吧！' . '
';
		$url = app_url('pocket/pocket/detail', array('id' => $commentid));
		return Message::sendCustomNotice($openid, $msg, $url);
	}

	static public function replaynotice($amid, $comment, $mid, $cid)
	{
		global $_W;
		$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $amid), 'openid');
		$smnane = pdo_getcolumn(PDO_NAME . 'member', array('id' => $mid), 'nickname');
		$tid = pdo_getcolumn(PDO_NAME . 'pocket_comment', array('id' => $cid), 'tid');
		$msg = $smnane . '回复您说道:' . '
';
		$msg .= $comment . '
';
		$msg .= '快去看看吧！' . '
';
		$url = app_url('pocket/pocket/detail', array('id' => $tid));
		return Message::sendCustomNotice($openid, $msg, $url);
	}

	static public function examinenotice($commentid, $flag = 0)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$comment = pdo_get(PDO_NAME . 'pocket_informations', array('id' => $commentid), array('nickname', 'type', 'createtime'));
		$typename = pdo_getcolumn(PDO_NAME . 'pocket_type', array('id' => $comment['type']), 'title');
		$url = app_url('pocket/pocket/detail', array('id' => $commentid));
		$openids = pdo_getall('wlmerchant_agentadmin', array('aid' => $_W['aid'], 'notice' => 1), array('openid'));

		if ($openids) {
			foreach ($openids as $key => $member) {
				self::adminenotice($member['openid'], $notice, $typename, $comment, $url, $flag);
			}
		}
	}

	static public function adminenotice($openid, $notice, $typename, $comment, $url, $flag)
	{
		global $_W;

		if (!$notice['pocketSwitch']) {
			$msg = $comment['nickname'] . '在' . $_W['areaname'] . '发布了一个关于' . $typename . '信息的帖子.' . '
';

			if ($flag) {
				$msg .= '快去检查吧！' . '
';
			}
			else {
				$msg .= '快去审核吧！' . '
';
			}

			return Message::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => '您好，有用户在' . $_W['areaname'] . '发布了一个同城信息', 'color' => '#173177'),
			'keyword1' => array('value' => $comment['nickname'], 'color' => '#173177'),
			'keyword2' => array('value' => $typename, 'color' => '#173177'),
			'keyword3' => array('value' => date('Y年m月d日 H:i:s', $comment['createtime']), 'color' => '#173177'),
			'keyword4' => array('value' => '待审核', 'color' => '#173177'),
			'remark'   => array('value' => '点击立即去查看帖子吧。', 'color' => '#173177')
		);

		if ($flag) {
			$postdata['keyword4']['value'] = '已发布';
		}

		return Message::sendtplnotice($openid, $notice['pocketnotice'], $postdata, $url);
	}

	static public function passnotice($informationsid)
	{
		global $_W;
		$settings = Setting::wlsetting_read('noticeMessage');
		$notice = unserialize($settings['notice']);
		$informations = pdo_get('wlmerchant_pocket_informations', array('id' => $informationsid), array('mid', 'type', 'nickname', 'status', 'reason', 'createtime'));
		$typename = pdo_getcolumn(PDO_NAME . 'pocket_type', array('id' => $informations['type']), 'title');
		$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $informations['mid']), 'openid');

		if ($informations['status']) {
			$result = '已驳回';
			$remark = '您发布的信息已被驳回,驳回原因是:' . $informations['reason'] . ',请您重新发布';
			$url = app_url('pocket/pocket/fabupage', array('inid' => $informationsid));
		}
		else {
			$result = '已通过';
			$remark = '您发布的信息已通过审核,点击查看帖子';
			$url = app_url('pocket/pocket/detail', array('id' => $informationsid));
		}

		if (!$notice['pocketSwitch']) {
			$msg = '您发布的一个关于' . $typename . '信息的帖子已审核.' . '
';
			$msg .= '审核结果：' . $result . '
';
			return Message::sendCustomNotice($openid, $msg, $url);
		}

		$postdata = array(
			'first'    => array('value' => '您好，您发布的帖子已审核。', 'color' => '#173177'),
			'keyword1' => array('value' => $informations['nickname'], 'color' => '#173177'),
			'keyword2' => array('value' => $typename, 'color' => '#173177'),
			'keyword3' => array('value' => date('Y年m月d日 H:i:s', $informations['createtime']), 'color' => '#173177'),
			'keyword4' => array('value' => $result, 'color' => '#173177'),
			'remark'   => array('value' => $remark, 'color' => '#173177')
		);
		return Message::sendtplnotice($openid, $notice['pocketnotice'], $postdata, $url);
	}

	static public function doTask()
	{
		global $_W;
		$tops = pdo_getall('wlmerchant_pocket_informations', array('top' => 1));

		foreach ($tops as $key => $v) {
			if ($v['endtime'] < time()) {
				pdo_update('wlmerchant_pocket_informations', array('top' => 0), array('id' => $v['id']));
			}
		}

		$replys = pdo_fetchall('SELECT cid,id FROM ' . tablename('wlmerchant_pocket_reply') . 'WHERE tid = 0 ORDER BY id DESC LIMIT 20');

		if ($replys) {
			foreach ($replys as $key => $va) {
				$tid = pdo_getcolumn(PDO_NAME . 'pocket_comment', array('id' => $va['cid']), 'tid');
				pdo_update('wlmerchant_pocket_reply', array('tid' => $tid), array('id' => $va['id']));
			}
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
