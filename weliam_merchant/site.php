<?php
/**
 * 周边商户入口
 *
 * @author 微蚁CMS
 * @url https://www.vipshw.cn/
 */
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT."/addons/weliam_merchant/core/common/defines.php";
require_once PATH_CORE."common/autoload.php";
Func_loader::core('global');

class Weliam_merchantModuleSite extends WeModuleSite {
	
	public function __call($name, $arguments) {
		global $_W,$_GPC;
		$isWeb = stripos($name, 'doWeb') === 0;
		$isMobile = stripos($name, 'doMobile') === 0;
		$_W['catalog'] = $catalog = !empty($isWeb) ? 'sys' : 'app';
		$_W['plugin'] = $plugin = !empty($_GPC['p']) ? $_GPC['p'] : 'dashboard';
		$_W['controller'] = $controller = !empty($_GPC['ac']) ? $_GPC['ac'] : 'dashboard';
		$_W['method'] = $method = !empty($_GPC['do']) ? $_GPC['do'] : 'index';
		$_W['wlsetting'] = Setting::wlsetting_load();
		$_W['wlsetting']['trade']['credittext'] = $_W['wlsetting']['trade']['credittext'] ? $_W['wlsetting']['trade']['credittext'] : '积分';
		$_W['wlsetting']['trade']['moneytext'] = $_W['wlsetting']['trade']['moneytext'] ? $_W['wlsetting']['trade']['moneytext'] : '余额';
		if (!in_array($_W['method'], array('qrcodeimg', 'Notify', 'captcha')) && !in_array($_W['controller'], array('wxapp')) && $_GPC['r'] != 'api') {
			Func_loader::$catalog('cover');
		}
		if($isWeb || $isMobile) {
			ob_clean();
			wl_new_method($plugin, $controller, $method, $_W['catalog']);
		}
		trigger_error("访问的模块 {$plugin} 不存在.", E_USER_WARNING);
		return null;
	}
	
	protected function pay($params = array(), $mine = array()) {
		global $_W;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '支付订单 - '.$_W['wlsetting']['base']['name'] : '支付订单';
		load()->model('activity');
		load()->model('module');
		activity_coupon_type_init();
		if(!$this->inMobile) {
			message('支付功能只能在手机上使用', '', '');
		}
		$params['module'] = 'weliam_merchant';
		if($params['fee'] <= 0) {
			$pars = array();
			$pars['from'] = 'return';
			$pars['result'] = 'success';
			$pars['type'] = '';
			$pars['tid'] = $params['tid'];
			$site = WeUtility::createModuleSite($params['module']);
			$method = 'payResult';
			if (method_exists($site, $method)) {
				exit($site->$method($pars));
			}
		}
		$log = pdo_get('core_paylog', array('uniacid' => $_W['uniacid'], 'module' => $params['module'], 'tid' => $params['tid']));
		if (empty($log)) {
			$log = array(
				'uniacid' => $_W['uniacid'],
				'acid' => $_W['acid'],
				'openid' => $_W['member']['uid'],
				'module' => 'weliam_merchant',
				'uniontid' => $params['tid'],
				'tid' => $params['tid'],
				'fee' => $params['fee'],
				'card_fee' => $params['fee'],
				'status' => '0',
				'is_usecard' => '0',
			);
			pdo_insert('core_paylog', $log);
		}
            if($log['status'] == '1') {
			message('这个订单已经支付成功, 不需要重复支付.', '', 'info');
		}
		$setting = uni_setting($_W['uniacid'], array('payment', 'creditbehaviors'));
		if(!is_array($setting['payment'])) {
			message('没有有效的支付方式, 请联系网站管理员.', '', 'error');
		}
		$pay = $setting['payment'];
		$we7_coupon_info = module_fetch('we7_coupon');
		if (!empty($we7_coupon_info)) {
			$cards = activity_paycenter_coupon_available();
			if (!empty($cards)) {
				foreach ($cards as $key => &$val) {
					if ($val['type'] == '1') {
						$val['discount_cn'] = sprintf("%.2f", $params['fee'] * (1 - $val['extra']['discount'] * 0.01));
						$coupon[$key] = $val;
					} else {
						$val['discount_cn'] = sprintf("%.2f", $val['extra']['reduce_cost'] * 0.01);
						$token[$key] = $val;
						if ($log['fee'] < $val['extra']['least_cost'] * 0.01) {
							unset($token[$key]);
						}
					}
					unset($val['icon']);
					unset($val['description']);
				}
			}
			$cards_str = json_encode($cards);
		}
		foreach ($pay as &$value) {
			$value['switch'] = array_key_exists('pay_switch', $value) ? $value['pay_switch'] : $value['switch'];
		}
		unset($value);
		if (empty($_W['member']['uid'])) {
			$pay['credit']['switch'] = false;
		}
		if ($params['module'] == 'paycenter') {
			$pay['delivery']['switch'] = false;
			$pay['line']['switch'] = false;
		}
		if (!empty($pay['credit']['switch'])) {
			$credtis = mc_credit_fetch($_W['member']['uid']);
			$credit_pay_setting = mc_fetch($_W['member']['uid'], array('pay_password'));
			$credit_pay_setting = $credit_pay_setting['pay_password'];
		}
		$you = 0;

		include wl_template('common/pay');
	}

	public function payResult($params) {
		define('IN_APP', true);
	    PayResult::main($params);
	}

}