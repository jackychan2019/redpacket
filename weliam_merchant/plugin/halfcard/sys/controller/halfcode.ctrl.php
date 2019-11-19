<?php
//dezend by http://www.sucaihuo.com/
class Halfcode_WeliamController
{
	public function lists()
	{
		global $_W;
		global $_GPC;
		$where = array('uniacid' => $_W['uniacid']);

		if ($_GPC['status']) {
			if ($_GPC['status'] == 1) {
				$where['status'] = 1;
			}

			if ($_GPC['status'] == 2) {
				$where['status'] = 0;
			}
		}

		if (!empty($_GPC['keyword'])) {
			if (!empty($_GPC['keywordtype'])) {
				switch ($_GPC['keywordtype']) {
				case 1:
					$where['days'] = $_GPC['keyword'];
					break;

				case 2:
					$member = Util::getSingelData('id', PDO_NAME . 'member', array('@nickname@' => $_GPC['keyword']));

					if ($member) {
						$where['mid'] = $member['id'];
					}
				case 3:
					$where['@remark@'] = $_GPC['keyword'];
					break;

				case 4:
					$where['@number@'] = $_GPC['keyword'];
					break;

				default:
					break;
				}
			}
		}

		if ($_GPC['export']) {
			$this->output($where);
		}

		$pindex = max(1, $_GPC['page']);
		$listData = Util::getNumData('*', PDO_NAME . 'token', $where, 'createtime desc', $pindex, 10, 1);
		$list = $listData[0];
		$pager = $listData[1];

		foreach ($list as $key => &$value) {
			if (!empty($value['aid'])) {
				$value['agentname'] = pdo_getcolumn(PDO_NAME . 'agentusers', array('uniacid' => $_W['uniacid'], 'id' => $value['aid']), 'agentname');
			}
			else {
				$value['agentname'] = '总平台';
			}

			if (!empty($value['mid'])) {
				$value['member'] = Member::getMemberByMid($value['mid']);
			}

			if ($value['levelid']) {
				$value['levelname'] = pdo_getcolumn(PDO_NAME . 'halflevel', array('id' => $value['levelid']), 'name');
			}
			else {
				$value['levelname'] = $_W['wlsetting']['halflevel']['name'];
			}

			if (file_exists(PATH_MODULE . 'lsh.log')) {
				$value['caragentname'] = !empty($value['caraid']) ? pdo_getcolumn('weliam_shiftcar_agentusers', array('id' => $value['caraid']), 'agentname') : '总平台';
			}
		}

		include wl_template('halfcardsys/codelist');
	}

	public function add()
	{
		global $_W;
		global $_GPC;
		$delevel = Setting::wlsetting_read('halflevel');
		$levels = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halflevel') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 ORDER BY sort DESC'));
		$agents = pdo_getall('wlmerchant_agentusers', array('uniacid' => $_W['uniacid']), array('id', 'agentname'));

		if (file_exists(PATH_MODULE . 'lsh.log')) {
			$caragents = pdo_getall('weliam_shiftcar_agentusers', array('uniacid' => $_W['uniacid']), array('id', 'agentname'));
		}

		if (checksubmit()) {
			$num = !empty($_GPC['num']) ? intval($_GPC['num']) : 1;

			if (0 < $num) {
				$k = 0;

				while ($k < $num) {
					$data['uniacid'] = $_W['uniacid'];
					$data['caraid'] = intval($_GPC['caraid']);
					$data['aid'] = intval($_GPC['aid']);
					$data['days'] = intval($_GPC['days']);
					$data['number'] = $_GPC['prefix'] . Util::createConcode(3);
					$data['remark'] = $_GPC['remark'];
					$data['levelid'] = $_GPC['levelid'];
					$data['give_price'] = $_GPC['give_price'];
					$data['createtime'] = TIMESTAMP;
					pdo_insert(PDO_NAME . 'token', $data);
					++$k;
				}

				wl_message('创建成功!需要创建' . $num . '个，成功创建' . $k . '个。', web_url('halfcard/halfcode/lists'), 'success');
			}
			else {
				wl_message('数量填写不正确!', web_url('halfcard/halfcode/add'), 'error');
			}
		}

		include wl_template('halfcardsys/codeadd');
	}

	public function editcode()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$code = pdo_get('wlmerchant_token', array('id' => $id));
		$levels = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halflevel') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 ORDER BY sort DESC'));
		$agents = pdo_getall('wlmerchant_agentusers', array('uniacid' => $_W['uniacid']), array('id', 'agentname'));

		if (file_exists(PATH_MODULE . 'lsh.log')) {
			$caragents = pdo_getall('weliam_shiftcar_agentusers', array('uniacid' => $_W['uniacid']), array('id', 'agentname'));
		}

		if ($_W['ispost']) {
			if ($id) {
				$data = array('days' => intval($_GPC['days']), 'levelid' => intval($_GPC['levelid']), 'remark' => trim($_GPC['remark']), 'caraid' => intval($_GPC['caraid']), 'aid' => intval($_GPC['aid']), 'give_price' => trim($_GPC['give_price']));

				if ($_GPC['range']) {
					$res = pdo_update('wlmerchant_token', $data, array('remark' => $code['remark']));
				}
				else {
					$res = pdo_update('wlmerchant_token', $data, array('id' => $id));
				}
			}

			if ($res) {
				show_json(1, '操作成功');
			}
			else {
				show_json(0, '操作失败,请重试');
			}
		}

		include wl_template('halfcardsys/codemodel');
	}

	public function remark()
	{
		global $_W;
		global $_GPC;
		pdo_update(PDO_NAME . 'token', array('remark' => $_GPC['remark']), array('id' => $_GPC['id']));
		exit(json_encode(array('message' => '备注成功')));
	}

	public function delcode()
	{
		global $_W;
		global $_GPC;
		pdo_delete(PDO_NAME . 'token', array('id' => $_GPC['id']));
		header('Location:' . web_url('halfcard/halfcode/lists'));
	}

	public function output($where)
	{
		global $_W;
		global $_GPC;
		$listData = Util::getNumData('*', PDO_NAME . 'token', $where, 'createtime desc', 1, 40000, 1);
		$list = $listData[0];

		foreach ($list as $key => &$value) {
			if (!empty($value['mid'])) {
				$member = Member::getMemberByMid($value['mid']);
				$value['nickname'] = $member['nickname'];
			}
			else {
				$value['nickname'] = '';
			}
		}

		$filter = array('number' => '激活码', 'days' => '时长(天)', 'remark' => '备注', 'nickname' => '使用人昵称', 'createtime' => '生成时间');
		$data = array();
		$i = 0;

		while ($i < count($list)) {
			foreach ($filter as $key => $title) {
				if ($key == 'createtime') {
					$data[$i][$key] = date('Y-m-d H:i:s', $list[$i][$key]);
				}
				else {
					$data[$i][$key] = $list[$i][$key];
				}
			}

			++$i;
		}

		util_csv::export_csv_2($data, $filter, '激活码列表.csv');
		exit();
	}

	public function deletejihuoqr()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$ids = $_GPC['ids'];

		if ($id) {
			$res = pdo_delete('wlmerchant_token', array('id' => $id));

			if ($res) {
				exit(json_encode(array('errno' => 0, 'message' => $res, 'id' => $id)));
			}
			else {
				exit(json_encode(array('errno' => 2, 'message' => $res, 'id' => $id)));
			}
		}

		if ($ids) {
			foreach ($ids as $key => $id) {
				pdo_delete('wlmerchant_token', array('id' => $id));
			}

			exit(json_encode(array('errno' => 0, 'message' => '', 'id' => '')));
		}
	}

	public function csv_add()
	{
		global $_W;
		$filename = $_FILES['csv_file']['name'];
		$filename = substr($filename, -4, 4);

		if (empty($filename)) {
			wl_message('请选择要导入的CSV文件', web_url('halfcard/halfcode/add'), 'success');
			exit();
		}

		if ($filename !== '.csv') {
			wl_message('请选择CSV文件', web_url('halfcard/halfcode/add'), 'success');
			exit();
		}

		$file_path = $_FILES['csv_file']['tmp_name'];
		$handle = fopen($file_path, 'r');
		$file_size = filesize($file_path);

		if ($file_size == 0) {
			wl_message('没有任何数据', web_url('halfcard/halfcode/add'), 'success');
			exit();
		}

		$sql = 'INSERT INTO  ' . tablename('wlmerchant_token') . ' (id,number,uniacid,aid,days,	
	price,type,tokentype,typename,status,remark,openid,mid,createtime) values';
		$result = fgetcsv($handle);

		while ($result = fgetcsv($handle)) {
			$sql .= '(' . '\'\'' . ',\'' . $result[1] . '\',' . '\'' . $_W['uniacid'] . '\',' . '\'' . $_W['aid'] . '\',' . '\'' . $result[2] . '\',' . '\'' . NULL . '\',' . '\'' . NULL . '\',' . '\'' . NULL . '\',' . '\'' . NULL . '\',' . '\'' . NULL . '\',' . '\'' . iconv('gb2312', 'utf-8', $result[3]) . '\',' . '\'' . NULL . '\',' . '\'' . NULL . '\',' . '\'' . time() . '\' ),';
		}

		fclose($handle);
		pdo_query(substr($sql, 0, strlen($sql) - 1)) || exit('导入失败');
		wl_message('导入csv成功', web_url('halfcard/halfcode/add'), 'success');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
