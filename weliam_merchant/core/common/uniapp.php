<?php
//dezend by http://www.sucaihuo.com/
class Uniapp
{
	public $disSetting;

	public function __construct()
	{
		global $_W;
		global $_GPC;

		if (!empty($_GPC['p'])) {
			$api = $_GPC['p'] . '/' . $_GPC['do'];
			$list = $this->noLoginApiList();

			if (!in_array($api, $list)) {
				if (!$_W['mid']) {
					$this->reLogin();
				}
			}
		}

		$this->disSetting = Setting::wlsetting_read('distribution');
	}

	public function result($errno, $message, $data = '')
	{
		exit(json_encode(array('errno' => $errno, 'message' => $message, 'data' => $data)));
	}

	/**     * Comment: 操作成功输出方法     * Author: zzw     * Date: 2019/7/16 9:35     * @param string $message     * @param array  $data     */
	public function renderSuccess($message = '操作成功', $data = array())
	{
		exit(json_encode(array('errno' => 0, 'message' => $message, 'data' => $data)));
	}

	/**     * Comment: 操作失败返回内容     * Author: zzw     * Date: 2019/7/16 9:36     * @param string $message     * @param array  $data     */
	public function renderError($message = '操作失败', $data = array())
	{
		exit(json_encode(array('errno' => 1, 'message' => $message, 'data' => $data)));
	}

	/**     * Comment: 登录错误，重新登录     * Author: zzw     * Date: 2019/7/29 16:40     * @param string $message     */
	public function reLogin($message = '请先登录')
	{
		exit(json_encode(array(
			'errno'   => 2,
			'message' => $message,
			'data'    => array('h5_login' => app_url('member/user/signin'), 'weChat_login' => app_url('member/user/wechatsign'))
		)));
	}

	/**     * Comment: 不需要登录验证的接口列表     * Author: zzw     * Date: 2019/7/29 16:24     * @return array     */
	protected function noLoginApiList()
	{
		$list = array('headline/HeadlineList', 'distribution/getSetting', 'store/homeList', 'rush/homeList', 'wlcoupon/homeList', 'halfcard/homeList', 'wlfightgroup/homeList', 'pocket/homeList', 'groupon/homeList', 'bargain/homeList', 'consumption/homeList', 'halfcard/packageList', 'member/getRegisterSet', 'member/register', 'member/userLogin', 'member/resetPassword');
		return $list;
	}
}

define('IN_UNIAPP', true);
header('Access-Control-Allow-Origin:*');
require '../../../../framework/bootstrap.inc.php';
require '../../../../addons/weliam_merchant/core/common/defines.php';
require '../../../../addons/weliam_merchant/core/common/autoload.php';
require '../../../../addons/weliam_merchant/core/function/global.func.php';
global $_W;
global $_GPC;
load()->model('attachment');
$_W['siteroot'] = str_replace(array('/addons/weliam_merchant/core/common', '/addons/weliam_merchant'), '', $_W['siteroot']);
$_W['method'] = $method = !empty($_GPC['do']) ? $_GPC['do'] : 'index';
$_W['aid'] = $_GPC['aid'] ? intval($_GPC['aid']) : 1;
$_W['uniacid'] = intval($_GPC['i']);

if (empty($_W['uniacid'])) {
	$_W['uniacid'] = intval($_GPC['weid']);
}

$_W['uniaccount'] = $_W['account'] = uni_fetch($_W['uniacid']);

if (empty($_W['uniaccount'])) {
	header('HTTP/1.1 404 Not Found');
	header('status: 404 Not Found');
	exit();
}

$_W['acid'] = $_W['uniaccount']['acid'];
$_W['attachurl'] = attachment_set_attach_url();
$_W['wlsetting'] = Setting::wlsetting_load();

if (!empty($_GPC['token'])) {
	$user = Member::getMemberByMid(array('tokey' => trim($_GPC['token'])));
	if (is_array($user) && !empty($user)) {
		$_W['wlmember'] = $user;
		$_W['mid'] = $user['id'];
	}
}

$agentareaid = intval($_GPC['areaid']);

if ($agentareaid) {
	$istrue_aid = Dashboard::set_agent_cookie($agentareaid, 'areaid');
}

$plugin = trim($_GPC['p']);

if (empty($plugin)) {
	require IA_ROOT . '/addons/weliam_merchant/uniapp.php';
	$instance = new Weliam_merchantModuleUniapp();

	if (!method_exists($instance, $method)) {
		$instance->securityVerification($method);
	}
}
else {
	require IA_ROOT . '/addons/weliam_merchant/plugin/' . $plugin . '/uniapp.php';
	$className = ucfirst($plugin) . 'ModuleUniapp';
	$instance = new $className();
	$instance->{$method}();
}

?>
