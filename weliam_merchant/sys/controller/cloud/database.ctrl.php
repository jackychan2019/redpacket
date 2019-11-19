<?php
//dezend by http://www.sucaihuo.com/
class Database_WeliamController
{
	public function __construct()
	{
		global $_W;

		if (!$_W['isfounder']) {
			wl_message('无权访问!');
		}
	}

	public function run()
	{
		global $_W;
		global $_GPC;

		if (checksubmit()) {
			$sql = $_POST['sql'];
			pdo_run($sql);
			wl_message('查询执行成功.', 'refresh');
		}

		include wl_template('cloud/database');
	}

	public function upgrade()
	{
		global $_W;
		global $_GPC;
		require IA_ROOT . '/addons/' . MODULE_NAME . '/upgrade.php';
		wl_message('数据更新成功', web_url('cloud/database/datemana'), 'success');
	}

	public function datemana()
	{
		global $_W;
		global $_GPC;
		include wl_template('cloud/datemana');
	}

	public function areadata()
	{
		global $_W;
		global $_GPC;
		global $GLOBALS;
		$type = !empty($_GPC['type']) ? $_GPC['type'] : 'install';

		if ($type == 'install') {
			$this->areadatainit();
			wl_message('地区数据安装成功.', web_url('cloud/database/datemana'), 'success');
		}

		if ($type == 'clear') {
			$id = pdo_getcolumn(PDO_NAME . 'area', array('id' => 110000), 'id');

			if (empty($id)) {
				wl_message('不存在地区数据，无需再清除.', web_url('cloud/database/datemana'), 'warning');
			}

			pdo_query('TRUNCATE TABLE ' . tablename('wlmerchant_area') . ';');
			wl_message('地区数据清除成功.', web_url('cloud/database/datemana'), 'success');
		}
	}

	public function areadataup()
	{
		$t1 = microtime(true);
		ini_set('display_errors', '1');
		error_reporting(32767 ^ 8);
		$this->areadatainit();
		$t2 = microtime(true);
		wl_debug('执行完成，耗时' . round($t2 - $t1, 3) . '秒');
	}

	public function movedata()
	{
		global $_W;
		global $_GPC;

		if (checksubmit('submit')) {
			$from = intval($_GPC['from']);
			$to = intval($_GPC['to']);
			if (empty($from) || empty($to)) {
				wl_message('请填写公众号ID', referer(), 'warning');
			}

			$tablenames = pdo_fetchall('SHOW TABLES LIKE :tablename', array(':tablename' => '%wlmerchant%'));

			foreach ($tablenames as $tablename) {
				$table = str_replace($_W['config']['db']['tablepre'], '', end($tablename));
				pdo_update($table, array('uniacid' => $to), array('uniacid' => $from));
			}

			wl_message('数据迁移成功.', web_url('cloud/database/movedata'), 'success');
		}

		include wl_template('cloud/movedata');
	}

	private function areadatainit()
	{
		$address = json_decode(file_get_contents(PATH_WEB . 'resource/download/aliarea.json'), true);
		$locations = json_decode(file_get_contents(PATH_WEB . 'resource/download/locations.json'), true);

		foreach ($address['children'] as $province) {
			$this->aliareainsert($province, $locations);

			foreach ($province['children'] as $city) {
				$this->aliareainsert($city, $locations);

				if (!empty($city['children'])) {
					foreach ($city['children'] as $district) {
						$this->aliareainsert($district, $locations);
					}
				}
			}
		}
	}

	private function aliareainsert($item, $location)
	{
		$name = pdo_getcolumn('wlmerchant_area', array('id' => $item['divisionCode']), 'name');
		$data = array('id' => $item['divisionCode'], 'pid' => $item['parentId'] == 1 ? 0 : $item['parentId'], 'name' => $item['divisionName'], 'visible' => 2, 'level' => $item['divisionLevel'] - 1, 'lat' => $location[$item['divisionCode']]['lat'], 'lng' => $location[$item['divisionCode']]['lng'], 'pinyin' => $item['pinyin'] ? str_replace(' ', '', $item['pinyin']) : '', 'initial' => $item['pinyin'] ? strtoupper(substr($item['pinyin'], 0, 1)) : '');

		if (empty($name)) {
			pdo_insert('wlmerchant_area', $data);
		}
		else {
			pdo_update('wlmerchant_area', $data, array('id' => $item['divisionCode']));
		}
	}

	private function clear_data()
	{
		$table_name = array('wlmerchant_waittask', 'wlmerchant_vip_record', 'wlmerchant_storeusers_group', 'wlmerchant_rush_order', 'wlmerchant_rush_activity', 'wlmerchant_role', 'wlmerchant_refund_record', 'wlmerchant_puvrecord', 'wlmerchant_adv', 'wlmerchant_agentusers', 'wlmerchant_agentusers_group', 'wlmerchant_apirecord', 'wlmerchant_banner', 'wlmerchant_category_store', 'wlmerchant_collect', 'wlmerchant_comment', 'wlmerchant_creditrecord', 'wlmerchant_goodshouse', 'wlmerchant_indexset', 'wlmerchant_member', 'wlmerchant_merchant_account', 'wlmerchant_merchant_money_record', 'wlmerchant_merchant_record', 'wlmerchant_merchantdata', 'wlmerchant_merchantuser', 'wlmerchant_merchantuser_qrlog', 'wlmerchant_nav', 'wlmerchant_notice', 'wlmerchant_oparea', 'wlmerchant_oplog', 'wlmerchant_paylog', 'wlmerchant_puv', 'wlmerchant_agentsetting', 'wlmerchant_rush_follows', 'wlmerchant_storefans', 'wlmerchant_halfcard_record', 'wlmerchant_halfcardlist', 'wlmerchant_halfcardmember', 'wlmerchant_halfcardrecord', 'wlmerchant_settlement_record', 'wlmerchant_qrcode', 'wlmerchant_halfcard_type', 'wlmerchant_member_type', 'wlmerchant_token', 'wlmerchant_token_apply', 'wlmerchant_couponlist', 'wlmerchant_member_coupons', 'wlmerchant_order', 'wlmerchant_qrcode_apply', 'wlmerchant_verifrecord', 'wlmerchant_signmember', 'wlmerchant_signreceive', 'wlmerchant_signrecord', 'wlmerchant_consumption', 'wlmerchant_areagroup');

		foreach ($table_name as $key => $value) {
			pdo_query('DROP TABLE IF EXISTS ' . tablename($value) . ';');
		}
	}

	private function install_data()
	{
		$updatefile = IA_ROOT . '/addons/' . MODULE_NAME . '/upgrade.php';
		require $updatefile;
	}
}

defined('IN_IA') || exit('Access Denied');
set_time_limit(0);

?>
