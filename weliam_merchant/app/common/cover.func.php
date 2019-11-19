<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
define('IN_APP', true);
require_once PATH_MODULE . 'version.php';
global $_W;
global $_GPC;
@session_start();

if (!empty($_GPC['invitid'])) {
	$_SESSION['invitid'] = intval($_GPC['invitid']);
}

$userinfo = wl_getcookie('usersign');
if (empty($userinfo) && !empty($_SESSION['usersign'])) {
	$userinfo = $_SESSION['usersign'];
}

if ($userinfo) {
	$user = Member::wl_member_auth($userinfo);
	if (is_array($user) && !empty($user)) {
		$_W['wlmember'] = $user;
		$_W['mid'] = $user['id'];
		wl_setcookie('user_token', $user['tokey'], 3600 * 24 * 30);
	}
	else {
		wl_setcookie('usersign', '', -100);
		wl_setcookie('user_token', '', -100);
	}

	unset($user);
}
else {
	$exitcode = wl_getcookie('exitlogin_code');
	if ((is_weixin() || is_wxapp()) && $exitcode['code'] != 1 && $_W['method'] != 'wechatsign') {
		header('location: ' . app_url('member/user/wechatsign', array('backurl' => urlencode($_W['siteurl']))));
		exit();
	}
}

if ($_W['wlmember']['blackflag']) {
	$black_desc = $_W['wlsetting']['share']['black_desc'] ? $_W['wlsetting']['share']['black_desc'] : '您被禁止访问，请联系客服';
	wl_message($black_desc, 'close', 'error');
}

if (!empty($_SESSION['movecar_invite']) && !empty($_W['mid'])) {
	$_GPC['invitid'] = $_SESSION['movecar_invite']['invitid'];
	$_W['wlsetting']['distribution']['lockstatus'] = 0;
}

$invitid = intval($_GPC['invitid']);
if (empty($invitid) && !empty($_SESSION['invitid'])) {
	$invitid = $_SESSION['invitid'];
}

if (p('distribution') && !empty($invitid) && !empty($_W['mid'])) {
	Distribution::addJunior($invitid, $_W['mid'], intval($_GPC['disflag']), intval($_GPC['rank']));

	if (!empty($_SESSION['movecar_invite'])) {
		header('location: ' . $_SESSION['movecar_invite']['carback']);
		exit();
	}
}

$istrue_aid = false;
$agentareaid = intval($_GPC['areaid']);

if ($agentareaid) {
	$istrue_aid = Dashboard::set_agent_cookie($agentareaid, 'areaid');
}

$agentaid = intval($_GPC['aid']);
if (!$istrue_aid && !empty($agentaid)) {
	$istrue_aid = Dashboard::set_agent_cookie($agentaid, 'aid');
}

if (!$istrue_aid && !!wl_getcookie('agentareaid')) {
	$istrue_aid = Dashboard::set_agent_cookie(wl_getcookie('agentareaid'), 'areaid');
}

if (!$istrue_aid && !empty($_W['clientip'])) {
	$areaInfo = MapService::guide_ip($_W['clientip']);

	if (!is_error($areaInfo)) {
		$districeId = $areaInfo['result']['ad_info']['adcode'];
		$istrue_aid = Dashboard::set_agent_cookie($districeId, 'areaid');

		if (!$istrue_aid) {
			$thisInfo = (new AreaTable())->selectFields('pid')->getAreaById($districeId);
			$istrue_aid = Dashboard::set_agent_cookie($thisInfo['pid'], 'areaid');

			if (!$istrue_aid) {
				$thisInfo1 = (new AreaTable())->selectFields('pid')->getAreaById($thisInfo['pid']);
				$istrue_aid = Dashboard::set_agent_cookie($thisInfo1['pid'], 'areaid');
			}
		}
	}
}

if (!empty($_W['mid'])) {
	if (!$istrue_aid && !empty($_W['wlmember']['aid'])) {
		$istrue_aid = Dashboard::set_agent_cookie($_W['wlmember']['aid'], 'aid');
	}

	if ($_W['wlsetting']['areaset']['location'] == 1 && !empty($_W['citycode']) && (empty($_W['location']['lat']) || empty($_W['location']['lng'])) && Dashboard::check_need_latlng()) {
		header('location: ' . h5_url('pages/mainPages/city/selectAddress', array('id' => $_W['citycode'], 'url' => urlencode($_W['siteurl']), 'loca' => 'now')));
		exit();
	}

	if (!$istrue_aid && $_W['plugin'] != 'area') {
		if (empty($_W['uniacid'])) {
			$_W['uniacid'] = $_W['wlmember']['uniacid'];
		}

		$url = $_W['wlsetting']['areaset']['location'] == 1 ? h5_url('pages/mainPages/city/city') : app_url('area/region/select_region', array('backurl' => urlencode($_W['siteurl'])));
		header('location: ' . $url);
		exit();
	}

	if (!empty($_W['aid']) && empty($_W['wlmember']['aid'])) {
		pdo_update('wlmerchant_member', array('aid' => $_W['aid']), array('id' => $_W['mid'], 'uniacid' => $_W['uniacid']));
	}
}

$_W['agentset'] = Setting::agentsetting_load();
$defaultdiyset = pdo_getcolumn('wlmerchant_agentsetting', array('uniacid' => $_W['uniacid'], 'aid' => 0, 'key' => 'diypageset'), 'value');

if (!empty($defaultdiyset)) {
	$defaultdiyset = unserialize($defaultdiyset);
	$_W['agentset']['diypageset'] = Diy::checkuniac($_W['agentset']['diypageset'], $defaultdiyset);
}

if (empty($_W['agentset']['meroof']['navnum'])) {
	$_W['agentset']['meroof']['navnum'] = 10;
}

if ($_W['agentset']['meroof']['siteName'] && $_W['wlsetting']['base']['independent_name'] == 1) {
	$_W['wlsetting']['base']['name'] = $_W['agentset']['meroof']['siteName'];
}

?>
