<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
define('IN_SYS', true);
global $_W;
global $_GPC;
load()->web('common');
load()->web('template');
load()->func('tpl');
Func_loader::core('tpl');
$_W['token'] = token();
$session = json_decode(base64_decode($_GPC['__wlagent_session']), true);

if (is_array($session)) {
	$user = User::agentuser_single(array('id' => $session['id']));
	if (is_array($user) && $session['hash'] == md5($user['password'] . $user['salt'])) {
		$_W['aid'] = $user['id'];
		$_W['uniacid'] = $user['uniacid'];
		isetcookie('__uniacid', $_W['uniacid'], 7 * 86400);
		$_W['agent'] = $user;
	}
	else {
		isetcookie('__wlagent_session', false, -100);
	}

	unset($user);
}

unset($session);

if (!empty($_W['uniacid'])) {
	$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);
	$_W['acid'] = $_W['account']['acid'];
}

if (empty($_W['aid'])) {
	$_W['aid'] = $_GPC['aid'];
}

if (empty($_W['uniacid'])) {
	$_W['uniacid'] = $_GPC['uniacid'];
}

if ((empty($_W['aid']) || empty($_W['uniacid'])) && $_W['controller'] != 'login') {
	wl_message('抱歉，您无权进行该操作，请先登录！', web_url('user/login/agent_login'), 'warning');
}

$ESession = json_decode(base64_decode($_GPC['__wlagent_staff_session']), true);

if ($ESession) {
	if (!$ESession['uniacid']) {
		$ESession['uniacid'] = $_W['uniacid'];
	}

	$_W['EInfor'] = $EInfo = pdo_get(PDO_NAME . 'agentadmin', $ESession);

	if (!$EInfo) {
		isetcookie('__wlagent_session', '', -10000);
		isetcookie('__wlagent_staff_session', '', -10000);
		wl_message('您的信息不存在!请联系管理员。', web_url('user/login/agent_login', array('aid' => $ESession['aid'])));
	}

	$_W['jurisdiction'] = $jurisdiction = unserialize($EInfo['jurisdiction']);
	$goToPath = $_GPC['p'] . '/' . $_GPC['ac'] . '/' . $_GPC['do'];
	$permissionList = Jurisdiction::menuList();
	$JListList = array_column($permissionList, 'list');
	$JUrlList = array();

	foreach ($JListList as $listKey => $ListVal) {
		$JUrlList = array_merge($JUrlList, array_column($ListVal, 'url'));
	}

	$_W['JUrlList'] = $JUrlList;
	if (in_array($goToPath, $JUrlList) && !in_array($goToPath, $jurisdiction)) {
		$VisitFun = $permissionList[$_GPC['p']];
		$sortList = array_column($VisitFun['list'], 'url');

		foreach ($sortList as $VisitKey => $VisitVal) {
			if (in_array($VisitVal, $jurisdiction)) {
				header('Location: ' . web_url($VisitVal));
				exit();
			}
		}

		if (0 < count($jurisdiction)) {
			$hasPageUrl = web_url($jurisdiction[0]);
			header('Location: ' . $hasPageUrl);
			exit();
		}

		wl_message('对不起！您没有访问权限。', getenv('HTTP_REFERER'));
	}
}

?>
