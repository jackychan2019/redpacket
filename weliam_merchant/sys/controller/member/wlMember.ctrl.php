<?php
//dezend by http://www.sucaihuo.com/
class wlMember_WeliamController
{
	public function index()
	{
		global $_W;
		global $_GPC;
		$start = $_GPC['start'] ? strtotime($_GPC['start']) : strtotime(date('Y-m'));
		$end = $_GPC['end'] ? strtotime($_GPC['end']) + 86399 : strtotime(date('Y-m-d')) + 86399;
		$day_num = ($end - $start) / 86400;

		if ($_W['isajax']) {
			$days = array();
			$i = 0;

			while ($i < $day_num) {
				$key = date('m-d', $start + 86400 * $i);
				$days[$key] = 0;
				++$i;
			}

			$where = $_W['aid'] ? ' AND aid = ' . $_W['aid'] : '';
			$data = pdo_fetchall('SELECT createtime FROM ' . tablename('wlmerchant_member') . 'WHERE uniacid = :uniacid AND createtime >= :starttime and createtime <= :endtime' . $where, array(':uniacid' => $_W['uniacid'], ':starttime' => $start, ':endtime' => $end));

			foreach ($data as $da) {
				$key = date('m-d', $da['createtime']);

				if (in_array($key, array_keys($days))) {
					++$days[$key];
				}
			}

			$newdata = array();

			foreach ($days as $k => $val) {
				$newdata[] = array('day' => $k, '新增客户' => $val);
			}

			exit(json_encode($newdata));
		}

		$stat = Merchant::sysMemberSurvey();
		include wl_template('member/summary');
	}

	public function memberIndex()
	{
		global $_W;
		global $_GPC;
		$where = $_W['aid'] ? array('aid' => $_W['aid']) : array();
		static $account_api;

		if (empty($account_api)) {
			$account_api = WeAccount::create();
		}

		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		if ($_GPC['keyword'] && $_GPC['type']) {
			if (!empty($_GPC['keyword'])) {
				switch ($_GPC['type']) {
				case 2:
					$where['@mobile@'] = $_GPC['keyword'];
					break;

				case 3:
					$where['@realname@'] = $_GPC['keyword'];
					break;

				case 5:
					$where['@id@'] = $_GPC['keyword'];
					break;

				default:
					$where['@nickname@'] = $_GPC['keyword'];
				}
			}
		}

		if ($_GPC['mid']) {
			$where['id'] = $_GPC['mid'];
		}

		if ($_GPC['blackflag']) {
			if ($_GPC['blackflag'] == 1) {
				$where['blackflag'] = 1;
			}
			else {
				$where['blackflag'] = 0;
			}
		}

		$memberData = Util::getNumData('*', PDO_NAME . 'member', $where, 'id desc', $pindex, $psize, 1);
		$list = $memberData[0];
		$pager = $memberData[1];
		load()->model('mc');

		foreach ($list as $key => &$value) {
			if (empty($value['openid']) && $value['uid']) {
				$mfans = pdo_get('mc_mapping_fans', array('uid' => $value['uid'], 'uniacid' => $_W['uniacid']), array('openid'));

				if ($mfans['openid']) {
					$value['openid'] = $mfans['openid'];
					pdo_update('wlmerchant_member', array('openid' => $mfans['openid']), array('id' => $value['id']));
				}
			}

			$result = mc_fansinfo($value['openid']);
			$credit = pdo_get('mc_members', array('uid' => $value['uid']), array('credit1', 'credit2'));
			$value['follow'] = $result['follow'];
			$value['unfollowtime'] = $result['unfollowtime'];
			$value['credit1'] = $credit['credit1'];
			$value['credit2'] = $credit['credit2'];
			if (empty($value['avatar']) || empty($value['nickname'])) {
				$fans = pdo_get('mc_mapping_fans', array('openid' => $value['openid']), array('tag'));
				$fans = base64_decode($fans['tag']);
				$fans = unserialize($fans);
				$value['avatar'] = $fans['headimgurl'];
				$value['nickname'] = $fans['nickname'];
				pdo_update('wlmerchant_member', array('avatar' => $value['avatar'], 'nickname' => $value['nickname']), array('id' => $value['id']));
			}

			if ($value['distributorid']) {
				$tjmid = pdo_getcolumn(PDO_NAME . 'distributor', array('id' => $value['distributorid']), 'leadid');

				if (0 < $tjmid) {
					$value['tjname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $tjmid), 'nickname');
				}
			}

			$dealnum1 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . (' WHERE mid = ' . $value['id'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status IN (1,2,3,4,6,8,9)'));
			$dealnum2 = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . (' WHERE mid = ' . $value['id'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status IN (1,2,3,4,6,8,9)'));
			$dealnum = $dealnum2 + $dealnum1;
			$dealmoney1 = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . (' WHERE mid = ' . $value['id'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status IN (1,2,3,4,6,8,9)'));
			$dealmoney2 = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . (' WHERE mid = ' . $value['id'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status IN (1,2,3,4,6,8,9)'));
			$dealmoney = $dealmoney1 + $dealmoney2;
			$value['dealnum'] = $dealnum;
			$value['dealmoney'] = $dealmoney;
			pdo_update('wlmerchant_member', array('dealnum' => $dealnum, 'dealmoney' => $dealmoney), array('id' => $value['id']));
		}

		include wl_template('member/listIndex');
	}

	public function memberDetail()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if ($_W['ispost']) {
			$data = is_array($_GPC['data']) ? $_GPC['data'] : array();

			if (!empty($data['mobile'])) {
				$m = pdo_fetch('select id from ' . tablename('wlmerchant_member') . ' where mobile=:mobile and uniacid=:uniaicd limit 1 ', array(':mobile' => $data['mobile'], ':uniaicd' => $_W['uniacid']));
				if (!empty($m) && $m['id'] != $id) {
					show_json(0, '此手机号已绑定其他用户!(mid:' . $m['id'] . ')');
				}
			}

			$data['password'] = trim($data['password']);

			if (!empty($data['password'])) {
				$salt = pdo_getcolumn(PDO_NAME . 'member', array('id' => $id, 'uniacid' => $_W['uniacid']), 'salt');

				if (empty($salt)) {
					$salt = random(8);
				}

				$data['password'] = md5($data['password'] . $salt);
				$data['salt'] = $salt;
			}
			else {
				unset($data['password']);
				unset($data['salt']);
			}

			pdo_update('wlmerchant_member', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
			show_json(1);
		}

		$member = Member::getMemberByMid($id);
		$result = mc_fansinfo($member['openid']);

		if (!empty($result)) {
			$member['follow'] = $result['follow'];
			$member['unfollowtime'] = $result['unfollowtime'];
		}

		include wl_template('member/listDetail');
	}

	public function toblack()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$flag = intval($_GPC['flag']);
		pdo_update('wlmerchant_member', array('blackflag' => $flag), array('id' => $id, 'uniacid' => $_W['uniacid']));

		if ($flag) {
			pdo_update('wlmerchant_pocket_informations', array('status' => 3), array('mid' => $id, 'uniacid' => $_W['uniacid']));
		}

		show_json(1, array('url' => referer()));
	}

	public function memberDelete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		pdo_delete('wlmerchant_member', array('id' => $id, 'uniacid' => $_W['uniacid']));
		show_json(1, array('url' => referer()));
	}

	public function memberRecharge()
	{
		global $_W;
		global $_GPC;
		$type = trim($_GPC['type']);
		$id = intval($_GPC['id']);
		$profile = Member::getMemberByMid($id);

		if ($_W['ispost']) {
			$typestr = $type == 'credit1' ? '积分' : '余额';
			$num = floatval($_GPC['num']);
			$remark = trim($_GPC['remark']) ? trim($_GPC['remark']) : '后台手动操作';

			if ($num <= 0) {
				show_json(0, array('message' => '请填写大于0的数字!'));
			}

			$changetype = intval($_GPC['changetype']);

			if ($changetype == 0) {
				$changenum = $num;
			}
			else {
				$changenum = 0 - $num;
			}

			$data = $type == 'credit1' ? Member::credit_update_credit1($id, $changenum, $remark) : Member::credit_update_credit2($id, $changenum, $remark);

			if (is_error($data)) {
				show_json(0, array('message' => $data['message']));
			}

			show_json(1, array('url' => referer()));
		}

		include wl_template('member/listRecharge');
	}

	public function selectMember()
	{
		global $_W;
		global $_GPC;
		$where = array();
		$keyword = trim($_GPC['keyword']);

		if ($keyword != '') {
			$where['nickname^openid^uid'] = $keyword;
		}

		$dsData = Util::getNumData('nickname,avatar,openid', PDO_NAME . 'member', $where, 'id desc', 0, 0, 0);
		$ds = $dsData[0];
		include wl_template('member/selectMember');
	}

	public function recharge()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where['uniacid'] = $_W['uniacid'];
		$where['status'] = 3;
		$where['plugin'] = 'member';

		if ($_GPC['paytype']) {
			$where['paytype'] = $_GPC['paytype'];
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
			$endtime = time();
		}

		$details = Util::getNumData('orderno,paytype,mid,paytime,price', PDO_NAME . 'order', $where, 'ID DESC', $pindex, $psize, 1);
		$pager = $details[1];
		$details = $details[0];

		if ($details) {
			foreach ($details as $key => &$det) {
				$member = pdo_get('wlmerchant_member', array('id' => $det['mid']), array('nickname', 'avatar'));
				$det['nickname'] = $member['nickname'];
				$det['avatar'] = tomedia($member['avatar']);
				$det['paytime'] = date('Y-m-d H:i:s', $det['paytime']);
			}
		}

		include wl_template('member/rechargelist');
	}

	public function integral()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$nickname = $_GPC['nickname'];
		$type = $_GPC['type'];
		$timeLimit = $_GPC['time_limit'];
		$where = ' a.type = 1 AND a.uniacid = ' . $_W['uniacid'] . ' ';

		if ($type) {
			if ($type == 1) {
				$where .= ' AND a.num >= 0 ';
			}
			else {
				$where .= ' AND a.num <= 0 ';
			}
		}

		if ($timeLimit) {
			$starttime = strtotime($timeLimit['start']);
			$endtime = strtotime($timeLimit['end']);
			$where .= ' AND a.createtime >= ' . $starttime . ' ';
			$where .= ' AND a.createtime <= ' . $endtime . ' ';
		}

		if ($nickname) {
			$where .= ' AND b.nickname LIKE \'%' . $nickname . '%\'';
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		$start = $pindex * $psize - $psize;
		$limit = ' LIMIT ' . $start . ',' . $psize . ' ';
		$sql = 'SELECT a.id,a.num,a.createtime,a.mid,a.remark,b.nickname,b.avatar FROM ' . tablename(PDO_NAME . 'creditrecord') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'member') . (' b ON a.mid = b.id WHERE ' . $where . ' AND a.mid > 0 ORDER BY createtime DESC');
		$total = count(pdo_fetchall($sql));
		$details = pdo_fetchall($sql . $limit);

		if ($details) {
			foreach ($details as $key => &$det) {
				$det['avatar'] = tomedia($det['avatar']);
				$det['createtime'] = date('Y-m-d H:i:s', $det['createtime']);
			}
		}

		$pager = pagination($total, $pindex, $psize);
		include wl_template('member/integrallist');
	}

	public function balance()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$keyword = $_GPC['keyword'];
		$keywordtype = $_GPC['keywordtype'];
		$type = $_GPC['type'];
		$timeLimit = $_GPC['time_limit'];
		$where = ' a.credittype = \'credit2\' AND a.uniacid = ' . $_W['uniacid'] . ' ';

		if ($type) {
			if ($type == 1) {
				$where .= ' AND a.num >= 0 ';
			}
			else {
				$where .= ' AND a.num <= 0 ';
			}
		}

		if ($timeLimit) {
			$starttime = strtotime($timeLimit['start']);
			$endtime = strtotime($timeLimit['end']);
			$where .= ' AND a.createtime >= ' . $starttime . ' ';
			$where .= ' AND a.createtime <= ' . $endtime . ' ';
		}

		if ($keyword) {
			if ($keywordtype == 1) {
				$where .= ' AND b.id LIKE \'%' . $keyword . '%\'';
			}
			else if ($keywordtype == 2) {
				$where .= ' AND b.nickname LIKE \'%' . $keyword . '%\'';
			}
			else {
				if ($keywordtype == 3) {
					$where .= ' AND b.mobile LIKE \'%' . $keyword . '%\'';
				}
			}
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		$start = $pindex * $psize - $psize;
		$limit = ' LIMIT ' . $start . ',' . $psize . ' ';
		$sql = 'SELECT a.id,a.num,a.createtime,a.uid,a.remark,b.nickname,b.avatar FROM ' . tablename('mc_credits_record') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'member') . (' b ON a.uid = b.uid WHERE ' . $where . ' AND b.id > 0  ORDER BY createtime DESC');
		$total = pdo_fetchcolumn('SELECT count(a.id) FROM ' . tablename('mc_credits_record') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'member') . (' b ON a.uid = b.uid WHERE ' . $where . ' AND b.id > 0 '));
		$details = pdo_fetchall($sql . $limit);

		if ($details) {
			foreach ($details as $key => &$det) {
				$det['avatar'] = tomedia($det['avatar']);
				$det['createtime'] = date('Y-m-d H:i:s', $det['createtime']);
			}
		}

		$pager = pagination($total, $pindex, $psize);
		include wl_template('member/balancelist');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
