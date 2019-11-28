<?php
//dezend by http://www.sucaihuo.com/
class Voteset_WeliamController
{
	public function setting()
	{
		global $_W;
		global $_GPC;
		$settings = Setting::wlsetting_read('wxappset');

		if (checksubmit('submit')) {
			$base = $_GPC['set'];
			$res1 = Setting::wlsetting_save($base, 'wxappset');
			wl_message('保存设置成功！', referer(), 'success');
		}

		include wl_template('wxapp/setting');
	}

	public function relation()
	{
		global $_W;
		global $_GPC;
		
	//	die('aaaaa');
	header("Location:".'https://'.$_SERVER['HTTP_HOST']."/web/index.php?c=site&a=entry&do=manage&m=tyzm_diamondvote");
		$relation = pdo_get(PDO_NAME . 'wxapp_relation', array('uniacid' => $_W['uniacid']));

		if (checksubmit('submit')) {
			$wxapp_uniacid = intval($_GPC['wxapp_uniacid']);
			$has = pdo_getcolumn(PDO_NAME . 'wxapp_relation', array('wxapp_uniacid' => $wxapp_uniacid), 'uniacid');
			if ($has && $has != $_W['uniacid']) {
				pdo_update(PDO_NAME . 'wxapp_relation', array('uniacid' => $_W['uniacid']), array('wxapp_uniacid' => $wxapp_uniacid));
				wl_message('关联小程序成功', referer(), 'success');
			}

			if (empty($relation)) {
				pdo_insert(PDO_NAME . 'wxapp_relation', array('uniacid' => $_W['uniacid'], 'wxapp_uniacid' => $wxapp_uniacid));
			}
			else {
				pdo_update(PDO_NAME . 'wxapp_relation', array('wxapp_uniacid' => $wxapp_uniacid), array('uniacid' => $_W['uniacid']));
			}

			wl_message('关联小程序成功', referer(), 'success');
		}

		$wxapps = pdo_getall('account_wxapp', array(), array('uniacid', 'name'));

		foreach ($wxapps as $key => &$value) {
			if (!user_is_founder($_W['uid']) || user_is_vice_founder()) {
				$value['has'] = pdo_getcolumn('uni_account_users', array('uniacid' => $value['uniacid'], 'uid' => $_W['uid']), 'id') ? 'yes' : 'no';
			}
			else {
				$value['has'] = 'yes';
			}
		}
		

		include wl_template('wxapp/relation');
	}

	public function applink()
	{
		global $_W;
		global $_GPC;
		$all_links = array(
			array('name' => '平台首页', 'link' => 'wxapp_web/pages/view/index'),
			array('name' => '个人中心', 'link' => 'wxapp_web/pages/view/index?scene=user%23'),
			array('name' => '我的订单', 'link' => 'wxapp_web/pages/view/index?scene=order%23status%3dall'),
			array('name' => '好店首页', 'link' => 'wxapp_web/pages/view/index?scene=storeindex%23'),
			array('name' => '商家入驻', 'link' => 'wxapp_web/pages/view/index?scene=storeenter%23'),
			array('name' => '商家管理入口', 'link' => 'wxapp_web/pages/view/index?scene=supervise%23')
		);

		if (p('halfcard')) {
			$all_links[] = array('name' => '一卡通首页', 'link' => 'wxapp_web/pages/view/index?scene=vipindex%23');
			$all_links[] = array('name' => '一卡通开通', 'link' => 'wxapp_web/pages/view/index?scene=cardopen%23');
		}

		if (p('pocket')) {
			$all_links[] = array('name' => '掌上信息首页', 'link' => 'wxapp_web/pages/view/index?scene=tcindex%23');
			$all_links[] = array('name' => '掌上信息发布', 'link' => 'wxapp_web/pages/view/index?scene=pocketissue%23');
		}

		if (p('rush')) {
			$all_links[] = array('name' => '抢购首页', 'link' => 'wxapp_web/pages/view/index?scene=rushindex%23');
		}

		if (p('wlcoupon')) {
			$all_links[] = array('name' => '卡券首页', 'link' => 'wxapp_web/pages/view/index?scene=couponindex%23');
		}

		if (p('wlfightgroup')) {
			$all_links[] = array('name' => '拼团首页', 'link' => 'wxapp_web/pages/view/index?scene=ptindex%23');
		}

		if (p('groupon')) {
			$all_links[] = array('name' => '团购首页', 'link' => 'wxapp_web/pages/view/index?scene=groupon%23');
		}

		if (p('wlsign')) {
			$all_links[] = array('name' => '签到首页', 'link' => 'wxapp_web/pages/view/index?scene=signindex%23');
		}

		if (p('distribution')) {
			$all_links[] = array('name' => '分销中心', 'link' => 'wxapp_web/pages/view/index?scene=distribution%23');
		}

		if (p('helper')) {
			$all_links[] = array('name' => '帮助中心', 'link' => 'wxapp_web/pages/view/index?scene=helper%23');
		}

		$all_links[] = array('name' => '积分商城', 'link' => 'wxapp_web/pages/view/index?scene=consumption%23');
		include wl_template('wxapp/applink');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
