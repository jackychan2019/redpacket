<?php
//dezend by http://www.sucaihuo.com/
class Comment_WeliamController
{
	public function index()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = array();
		$where['uniacid'] = $_W['uniacid'];
		$where['aid'] = $_W['aid'];
		$stores = pdo_getall('wlmerchant_merchantdata', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('id'));
		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (!empty($_GPC['time'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);

			switch ($_GPC['timetype']) {
			case 1:
				$where['createtime>'] = $starttime;
				$where['createtime<'] = $endtime;
				break;
			}
		}

		if (!empty($_GPC['type'])) {
			$where['true'] = $_GPC['type'];
		}

		if (!empty($_GPC['keyword'])) {
			$where['sid'] = $_GPC['keyword'];
		}

		if (!empty($_GPC['checkone'])) {
			$where['checkone'] = $_GPC['checkone'];
		}

		$data = Util::getNumData('*', PDO_NAME . 'comment', $where, 'createtime desc', $pindex, $psize, 1);
		$lists = $data[0];
		$pager = $data[1];

		foreach ($lists as $key => &$value) {
			$starNum = array();
			$i = 0;

			while ($i < $value['star']) {
				$starNum[$i] = $i;
				++$i;
			}

			$value['star'] = $starNum;
			$value['sName'] = Util::idSwitch('sid', 'sName', $value['sid']);
		}

		include wl_template('store/comment');
	}

	public function check()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$pindex = $_GPC['pindex'];
		$page = $_GPC['page'];
		$data = Util::getSingelData('*', PDO_NAME . 'comment', array('id' => $id));
		$starNum = array();
		$i = 0;

		while ($i < $data['star']) {
			$starNum[$i] = $i;
			++$i;
		}

		$data['star'] = $starNum;
		$data['pic'] = unserialize($data['pic']);
		$data['sName'] = Util::idSwitch('sid', 'sName', $data['sid']);

		if ($_GPC['checkone']) {
			$update = array('checkone' => $_GPC['checkone'], 'pic' => serialize($_GPC['pic']));
			if ($_W['wlsetting']['creditset']['commentcredit'] && $update['checkone'] == 2) {
				Member::credit_update_credit1($data['mid'], $_W['wlsetting']['creditset']['commentcredit'], '评价赠送积分');
			}

			pdo_update(PDO_NAME . 'comment', $update, array('id' => $id));
			wl_message('操作成功！', web_url('store/comment/index', array('page' => $page)), 'success');
		}

		if ($_GPC['ids']) {
			$ids = explode(',', $_GPC['ids']);

			foreach ($ids as $k => $v) {
				pdo_update(PDO_NAME . 'comment', array('checkone' => $_GPC['check']), array('id' => $v));
				if ($_W['wlsetting']['creditset']['commentcredit'] && $_GPC['check'] == 2) {
					$mid = pdo_getcolumn(PDO_NAME . 'comment', array('id' => $v), 'mid');
					Member::credit_update_credit1($mid, $_W['wlsetting']['creditset']['commentcredit'], '评价赠送积分');
				}
			}

			wl_message('操作成功！', web_url('store/comment/index'), 'success');
		}

		include wl_template('store/comment_check');
	}

	public function dyncheck()
	{
		global $_W;
		global $_GPC;

		if ($_GPC['ids']) {
			$ids = explode(',', $_GPC['ids']);

			foreach ($ids as $k => $v) {
				pdo_update(PDO_NAME . 'store_dynamic', array('status' => $_GPC['check']), array('id' => $v));
			}

			wl_message('操作成功！', web_url('store/comment/dynamic'), 'success');
		}
	}

	public function checkdyn()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$data = pdo_get(PDO_NAME . 'store_dynamic', array('id' => $id));
		$data['pic'] = unserialize($data['imgs']);
		$data['sName'] = Util::idSwitch('sid', 'sName', $data['sid']);

		if ($_GPC['token']) {
			$update = array('content' => $_GPC['content'], 'status' => $_GPC['checkone'], 'imgs' => serialize($_GPC['pic']));
			pdo_update(PDO_NAME . 'store_dynamic', $update, array('id' => $id));
			wl_message('操作成功！', web_url('store/comment/dynamic'), 'success');
		}

		include wl_template('store/dynamic_check');
	}

	public function reply()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$pindex = $_GPC['pindex'];
		$page = $_GPC['page'];
		$data = Util::getSingelData('*', PDO_NAME . 'comment', array('id' => $id));
		$starNum = array();
		$i = 0;

		while ($i < $data['star']) {
			$starNum[$i] = $i;
			++$i;
		}

		$data['star'] = $starNum;
		$data['pic'] = unserialize($data['pic']);
		$data['sName'] = Util::idSwitch('sid', 'sName', $data['sid']);

		if ($_GPC['replytextone']) {
			$replyone = $_GPC['replytextone'] ? 2 : 1;
			$update = array('replytextone' => $_GPC['replytextone'], 'replypicone' => serialize($_GPC['replypicone']), 'replyone' => $replyone);
			pdo_update(PDO_NAME . 'comment', $update, array('id' => $id));
			$comment = pdo_get('wlmerchant_comment', array('id' => $id), array('mid', 'replytextone', 'sid'));
			$storename = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $comment['sid']), 'storename');
			$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $comment['mid']), 'openid');
			$first = '商家回复了您的评论';
			$keyword1 = '商家[' . $storename . ']的回复';
			$keyword2 = '已回复';
			$remark = '回复内容:' . $comment['replytextone'];
			$url = app_url('store/merchant/commentpage', array('merchantid' => 7));
			Message::jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url);
			wl_message('操作成功！', web_url('store/comment/index', array('page' => $page)), 'success');
		}

		include wl_template('store/comment_reply');
	}

	public function add()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$pindex = $_GPC['pindex'];
		$page = $_GPC['page'];
		$data = Util::getSingelData('*', PDO_NAME . 'comment', array('id' => $id));

		if ($_GPC['data']) {
			unset($data['id']);
			$update = $_GPC['data'];
			$data['star'] = $update['star'];
			$data['headimg'] = tomedia($update['headimg']);
			$data['nickname'] = $update['nickname'];
			$data['text'] = $update['text'];
			$data['createtime'] = strtotime($update['time']);
			$data['replytextone'] = $update['replytextone'];
			$data['true'] = 2;
			$data['checkone'] = 2;
			$data['pic'] = serialize($_GPC['pic']);
			$data['replypicone'] = serialize($_GPC['replypicone']);
			pdo_insert(PDO_NAME . 'comment', $data);
			wl_message('操作成功！', web_url('store/comment/index', array('page' => $page)), 'success');
		}

		include wl_template('store/comment_add');
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$pindex = $_GPC['pindex'];

		if ($_GPC['id']) {
			$ids = explode(',', $_GPC['id']);

			foreach ($ids as $k => $v) {
				pdo_delete(PDO_NAME . 'comment', array('id' => $v));
			}
		}

		wl_message('操作成功！', web_url('store/comment/index', array('page' => $pindex)), 'success');
	}

	public function dyndelete()
	{
		global $_W;
		global $_GPC;

		if ($_GPC['ids']) {
			$ids = explode(',', $_GPC['ids']);

			foreach ($ids as $k => $v) {
				pdo_delete(PDO_NAME . 'store_dynamic', array('id' => $v));
			}
		}

		wl_message('操作成功！', web_url('store/comment/dynamic'), 'success');
	}

	public function setting()
	{
		global $_W;
		global $_GPC;
		$data = Setting::agentsetting_read('comment');

		if ($_GPC['token']) {
			Setting::agentsetting_save($_GPC['data'], 'comment');
			wl_message('操作成功！', web_url('store/comment/index'), 'success');
		}

		include wl_template('store/comment_setting');
	}

	public function dynamic()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = array();
		$where['uniacid'] = $_W['uniacid'];
		$where['aid'] = $_W['aid'];
		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (!empty($_GPC['time'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);

			switch ($_GPC['timetype']) {
			case 1:
				$where['createtime>'] = $starttime;
				$where['createtime<'] = $endtime;
				break;

			case 2:
				$where['passtime>'] = $starttime;
				$where['passtime<'] = $endtime;
				break;

			case 3:
				$where['sendtime>'] = $starttime;
				$where['sendtime<'] = $endtime;
				break;
			}
		}

		if (!empty($_GPC['type'])) {
			if ($_GPC['type'] == 4) {
				$where['status'] = 0;
			}
			else {
				$where['status'] = $_GPC['type'];
			}
		}

		if (!empty($_GPC['keyword'])) {
			$where['sid'] = $_GPC['keyword'];
		}

		$data = Util::getNumData('*', PDO_NAME . 'store_dynamic', $where, 'createtime desc', $pindex, $psize, 1);
		$lists = $data[0];
		$pager = $data[1];

		foreach ($lists as $key => &$dyn) {
			$dyn['sName'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $dyn['sid']), 'storename');
			$member = pdo_get(PDO_NAME . 'member', array('id' => $dyn['mid']), array('avatar', 'nickname'));
			$dyn['headimg'] = tomedia($member['avatar']);
			$dyn['nickname'] = $member['nickname'];
		}

		include wl_template('store/dynamic');
	}

	public function deletedyn()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$id = intval($_GPC['id']);

			if (empty($id)) {
				show_json(0, '参数错误，请刷新重试！');
			}
			else {
				$res = pdo_delete('wlmerchant_store_dynamic', array('id' => $id, 'aid' => intval($_W['aid'])));
			}

			if ($res) {
				show_json(1);
			}
			else {
				show_json(0, '删除失败,请刷新页面重试！');
			}
		}
	}

	public function passdyn()
	{
		global $_W;
		global $_GPC;

		if ($_W['ispost']) {
			$id = intval($_GPC['id']);

			if (empty($id)) {
				show_json(0, '参数错误，请刷新重试！');
			}
			else {
				$type = $_GPC['type'];

				if ($type == 'pass') {
					$res = pdo_update('wlmerchant_store_dynamic', array('status' => 1, 'passtime' => time()), array('id' => $id));
				}
				else {
					if ($type == 'reject') {
						$res = pdo_update('wlmerchant_store_dynamic', array('status' => 3, 'passtime' => time()), array('id' => $id));
					}
				}
			}

			if ($res) {
				show_json(1, '审核成功');
			}
			else {
				show_json(0, '删除失败,请刷新页面重试！');
			}
		}
	}

	public function senddyn()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$dynamic = pdo_get('wlmerchant_store_dynamic', array('id' => $id, 'status' => 1));

		if (empty($dynamic)) {
			wl_message('此动态已删除或已推送');
		}

		$fans = pdo_getall('wlmerchant_storefans', array('uniacid' => $_W['uniacid'], 'sid' => $dynamic['sid']), array('mid'));
		$fannum = count($fans);

		if (checksubmit('submit')) {
			$firsttext = $_GPC['firsttext'];
			$remark = $_GPC['remark'];
			$content = $_GPC['content'];
			include wl_template('store/dyn-process');
			exit();
		}

		include wl_template('store/dynamicmodel');
	}

	public function senddyning()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$firsttext = $_GPC['firsttext'];
		$remark = $_GPC['remark'];
		$content = $_GPC['content'];
		$psize = 50;
		$request_body = file_get_contents('php://input');
		$data = json_decode($request_body, true);
		$pindex = $data['pindex'];
		$success = $data['success'];
		$dynamic = pdo_get('wlmerchant_store_dynamic', array('id' => $id, 'status' => 1));
		$fans = pdo_fetchall('SELECT mid FROM ' . tablename('wlmerchant_storefans') . ('WHERE sid = ' . $dynamic['sid'] . ' AND uniacid = ' . $_W['uniacid'] . ' ORDER BY id DESC  LIMIT ') . $pindex * $psize . ',' . $psize);

		if ($fans) {
			$url = app_url('store/merchant/detail', array('id' => $dynamic['sid'], 'dynflag' => 1));

			foreach ($fans as $key => $fan) {
				$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $fan['mid']), 'openid');
				$res = Message::jobNotice($openid, $firsttext, $content, '已完成', $remark, $url);

				if ($res) {
					++$success;
				}
			}

			$return = array('result' => 1, 'success' => $success);
		}
		else {
			pdo_update('wlmerchant_store_dynamic', array('status' => 2, 'sendtime' => time(), 'successnum' => $success), array('id' => $id));
			$return = array('result' => 3, 'success' => $success);
		}

		exit(json_encode($return));
	}

	public function storeSet()
	{
		global $_W;
		global $_GPC;

		if (checksubmit('submit')) {
			$storeSet = $_GPC['store_set'];
			$res1 = Setting::agentsetting_save($storeSet, 'agentsStoreSet');
			wl_message('保存设置成功！', referer(), 'success');
		}

		$storeSet = Setting::agentsetting_read('agentsStoreSet');
		include wl_template('store/storeSet');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
