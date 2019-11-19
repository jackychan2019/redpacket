<?php
//dezend by http://www.sucaihuo.com/
class Userset_WeliamController
{
	public function profile()
	{
		global $_W;
		global $_GPC;
		$user = pdo_get(PDO_NAME . 'agentusers', array('uniacid' => $_W['uniacid'], 'id' => $_W['aid']));

		if (checksubmit('submit')) {
			if (empty($_GPC['pw']) || empty($_GPC['pw2'])) {
				wl_message('密码不能为空，请重新填写！', 'referer', 'error');
			}

			if ($_GPC['pw'] == $_GPC['pw2']) {
				wl_message('新密码与原密码一致，请检查！', 'referer', 'error');
			}

			$password_old = Util::encryptedPassword($_GPC['pw'], $user['salt']);

			if ($user['password'] != $password_old) {
				wl_message('原密码错误，请重新填写！', 'referer', 'error');
			}

			$result = '';
			$members = array('password' => Util::encryptedPassword($_GPC['pw2'], $user['salt']));
			$result = pdo_update(PDO_NAME . 'agentusers', $members, array('id' => $_W['aid']));
			wl_message('修改成功！', 'referer', 'success');
		}

		include wl_template('agentset/profile');
	}

	public function customer()
	{
		global $_W;
		global $_GPC;
		$settings = Setting::agentsetting_read('customer');

		if (checksubmit('submit')) {
			$base = Util::trimWithArray($_GPC['customer']);
			Setting::agentsetting_save($base, 'customer');
			wl_message('更新设置成功！', web_url('agentset/userset/customer'));
		}

		include wl_template('agentset/customer');
	}

	public function community()
	{
		global $_W;
		global $_GPC;
		$settings = Setting::agentsetting_read('community');

		if (checksubmit('submit')) {
			$base = Util::trimWithArray($_GPC['community']);
			Setting::agentsetting_save($base, 'community');
			wl_message('更新设置成功！', web_url('agentset/userset/community'));
		}

		include wl_template('agentset/community');
	}

	public function adminset()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$datas = Util::getNumData('*', 'wlmerchant_agentadmin', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), 'id DESC', $pindex, $psize, 1);
		$tags = $datas[0];
		$pager = $datas[1];

		foreach ($tags as $key => &$value) {
			$value['nickname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $value['mid']), 'nickname');
		}

		include wl_template('agentset/adminset');
	}

	public function adminedit()
	{
		global $_W;
		global $_GPC;

		if (!empty($_GPC['id'])) {
			$admin = pdo_get('wlmerchant_agentadmin', array('id' => $_GPC['id']));
			$admin['nickname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $admin['mid']), 'nickname');
			$jurisdiction = unserialize($admin['jurisdiction']);
		}

		if (checksubmit('submit')) {
			$admin = array();
			$admin['account'] = trim($_GPC['account']);
			$password = trim($_GPC['password']);
			$admin['openid'] = trim($_GPC['openid']);
			$admin['mid'] = trim($_GPC['mid']);
			$admin['notice'] = intval($_GPC['notice']);
			$admin['manage'] = intval($_GPC['manage']);
			$admin['jurisdiction'] = serialize($_GPC['jurisdiction']);

			if (empty($_GPC['adminid'])) {
				$admin['uniacid'] = $_W['uniacid'];
				$admin['aid'] = $_W['aid'];
				$admin['createtime'] = time();
				$admin['password'] = md5($password);
				$res = pdo_insert(PDO_NAME . 'agentadmin', $admin);
			}
			else {
				$pwd = pdo_getcolumn(PDO_NAME . 'agentadmin', array('id' => $_GPC['adminid']), 'password');

				if ($password != $pwd) {
					$admin['password'] = md5($password);
				}

				$res = pdo_update('wlmerchant_agentadmin', $admin, array('id' => $_GPC['adminid']));
			}

			if ($res) {
				wl_message('保存成功', web_url('agentset/userset/adminset'), 'success');
			}
			else {
				wl_message('保存失败', referer(), 'error');
			}
		}

		$list = Jurisdiction::menuList();
		include wl_template('agentset/adminedit');
	}

	public function changeadmin()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];
		$newvalue = trim($_GPC['value']);

		if ($type == 1) {
			$res = pdo_update('wlmerchant_agentadmin', array('notice' => $newvalue), array('id' => $id));
		}
		else if ($type == 2) {
			$res = pdo_update('wlmerchant_agentadmin', array('manage' => $newvalue), array('id' => $id));
		}
		else {
			if ($type == 3) {
				$res = pdo_delete('wlmerchant_agentadmin', array('id' => $id));
			}
		}

		if ($res) {
			show_json(1, '修改成功');
		}
		else {
			show_json(0, '修改失败，请重试');
		}
	}

	public function meroof()
	{
		global $_W;
		global $_GPC;
		$set = Setting::wlsetting_read('base');
		$meroof = Setting::agentsetting_read('meroof');

		if (empty($meroof['tablink'])) {
			$meroof['tablink'] = array(
				array('name' => '附近', 'near' => 2),
				array('name' => '推荐', 'near' => 3),
				array('name' => '人气', 'near' => 4),
				array('name' => '最新', 'near' => 1)
			);
		}

		if (checksubmit('submit')) {
			$base = $_GPC['base'];
			$base['tablink'] = $_GPC['tablink'];
			$i = 0;

			while ($i < sizeof($base['tablink']['name'])) {
				$base['tablink'][] = array('name' => $base['tablink']['name'][$i], 'near' => $base['tablink']['near'][$i]);
				++$i;
			}

			unset($base['tablink']['name']);
			unset($base['tablink']['near']);
			$res1 = Setting::agentsetting_save($base, 'meroof');

			if ($res1) {
				wl_message('保存设置成功！', referer(), 'success');
			}
			else {
				wl_message('保存设置失败！', referer(), 'error');
			}
		}

		include wl_template('agentset/meroofIndex');
	}

	public function shareset()
	{
		global $_W;
		global $_GPC;
		$base = Setting::agentsetting_read('shareset');

		if (checksubmit('submit')) {
			$base = $_GPC['base'];
			$res1 = Setting::agentsetting_save($base, 'shareset');

			if ($res1) {
				wl_message('保存设置成功！', referer(), 'success');
			}
			else {
				wl_message('保存设置失败！', referer(), 'error');
			}
		}

		include wl_template('agentset/shareset');
	}

	public function userindex()
	{
		global $_W;
		global $_GPC;
		$base = Setting::agentsetting_read('userindex');

		if (checksubmit('submit')) {
			$base = $_GPC['base'];
			$res1 = Setting::agentsetting_save($base, 'userindex');

			if ($res1) {
				wl_message('保存设置成功！', referer(), 'success');
			}
			else {
				wl_message('保存设置失败！', referer(), 'error');
			}
		}

		include wl_template('agentset/userindex');
	}

	public function tags()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$type = $_GPC['type'] ? $_GPC['type'] : 0;
		$datas = Util::getNumData('*', 'wlmerchant_tags', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'type' => $type), 'type ASC,sort DESC', $pindex, $psize, 1);
		$tags = $datas[0];
		$pager = $datas[1];
		include wl_template('agentset/tagsindex');
	}

	public function tagsedit()
	{
		global $_W;
		global $_GPC;

		if (!empty($_GPC['id'])) {
			$tag = pdo_get('wlmerchant_tags', array('id' => $_GPC['id']));
		}

		if (checksubmit('submit')) {
			$tag = $_GPC['tag'];
			$tag['title'] = trim($tag['title']);
			$tag['sort'] = intval($tag['sort']);
			$tag['enabled'] = intval($_GPC['enabled']);

			if (empty($_GPC['tagid'])) {
				$tag['uniacid'] = $_W['uniacid'];
				$tag['aid'] = $_W['aid'];
				$tag['createtime'] = time();
				$res = pdo_insert(PDO_NAME . 'tags', $tag);
			}
			else {
				$res = pdo_update('wlmerchant_tags', $tag, array('id' => $_GPC['tagid']));
			}

			if ($res) {
				wl_message('保存成功', web_url('agentset/userset/tags'), 'success');
			}
			else {
				wl_message('保存失败', referer(), 'error');
			}
		}

		include wl_template('agentset/tagsedit');
	}

	public function changeinfo()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];
		$newvalue = trim($_GPC['value']);

		if ($type == 1) {
			$res = pdo_update('wlmerchant_tags', array('title' => $newvalue), array('id' => $id));
		}
		else if ($type == 2) {
			$res = pdo_update('wlmerchant_tags', array('sort' => $newvalue), array('id' => $id));
		}
		else if ($type == 3) {
			$res = pdo_update('wlmerchant_tags', array('enabled' => $newvalue), array('id' => $id));
		}
		else {
			if ($type == 4) {
				$res = pdo_delete('wlmerchant_tags', array('id' => $id));
			}
		}

		if ($res) {
			show_json(1, '修改成功');
		}
		else {
			show_json(0, '修改失败，请重试');
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
