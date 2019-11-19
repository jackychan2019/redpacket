<?php
//dezend by http://www.sucaihuo.com/
class Sqrcode_WeliamController
{
	public function qrlist()
	{
		global $_W;
		global $_GPC;
		$wheresql = is_agent() ? ' WHERE uniacid = :uniacid AND type = 0 AND aid = :aid' : ' WHERE uniacid = :uniacid AND type = 0 ';
		$param = is_agent() ? array(':uniacid' => $_W['uniacid'], ':aid' => $_W['aid']) : array(':uniacid' => $_W['uniacid']);
		$keyword = trim($_GPC['keyword']);

		if (!empty($keyword)) {
			$wheresql .= ' AND (cardsn LIKE \'%' . $keyword . '%\' or remark LIKE \'%' . $keyword . '%\') ';
		}

		$starttime = empty($_GPC['time']['start']) ? TIMESTAMP - 86399 * 30 : strtotime($_GPC['time']['start']);
		$endtime = empty($_GPC['time']['end']) ? TIMESTAMP : strtotime($_GPC['time']['end']);

		if (!empty($_GPC['time']['start'])) {
			$wheresql .= ' AND createtime >= \'' . $starttime . '\' AND createtime <= \'' . $endtime . '\'';
		}

		if (!empty($_GPC['status'])) {
			$wheresql .= ' AND status = ' . $_GPC['status'];
		}

		if (!empty($_GPC['model']) && $_GPC['model'] != -1) {
			$wheresql .= ' AND model = ' . $_GPC['model'];
		}

		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$list = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'qrcode') . $wheresql . ' ORDER BY `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $param);

		if (!empty($list)) {
			foreach ($list as $index => &$qrcode) {
				if ($qrcode['aid']) {
					$qrcode['agentname'] = pdo_getcolumn(PDO_NAME . 'agentusers', array('uniacid' => $_W['uniacid'], 'id' => $qrcode['aid']), 'agentname');
				}
				else {
					$qrcode['agentname'] = '系统管理员';
				}

				$wq_qr = pdo_get('qrcode', array('id' => $qrcode['qrid']), array('ticket', 'scene_str', 'qrcid', 'id', 'createtime'));
				$qrcode['scene_str'] = $wq_qr['scene_str'];
				$qrcode['qrcid'] = $wq_qr['qrcid'];
				$qrcode['id'] = $wq_qr['id'];
				$qrcode['endtime'] = $wq_qr['createtime'] + 2592000;

				if ($qrcode['model'] == 2) {
					$qrcode['showurl'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($wq_qr['ticket']);
					$qrcode['modellabel'] = '含参';
					$qrcode['endtime'] = '<font color="green">永不</font>';
				}
				else if ($qrcode['model'] == 3) {
					$qrcode['showurl'] = app_url('qr/qrcode/show', array('ncnumber' => $qrcode['cardsn'], 'salt' => $qrcode['salt']));
					$qrcode['modellabel'] = '智能';
					$qrcode['endtime'] = '<font color="green">永不</font>';
				}
				else {
					$qrcode['modellabel'] = '临时';
					$qrcode['showurl'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($wq_qr['ticket']);

					if ($qrcode['endtime'] < TIMESTAMP) {
						$qrcode['endtime'] = '<font color="red">已过期</font>';
					}
					else {
						$qrcode['endtime'] = date('Y-m-d H:i:s', $qrcode['endtime']);
					}
				}
			}
		}

		$total = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'qrcode') . $wheresql, $param);
		$pager = pagination($total, $pindex, $psize);

		if ($_GPC['export'] != '') {
			$this->export($wheresql, $param);
		}

		include wl_template('sqrcode/qr-list');
	}

	public function post()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if ($id) {
			$apply = pdo_get(PDO_NAME . 'qrcode_apply', array('uniacid' => $_W['uniacid'], 'id' => $id));
		}

		if (checksubmit('submit')) {
			Storeqr::qr_createkeywords();
			$qrctype = intval($_GPC['qrc-model']);
			$allnum = intval($_GPC['qr_num']);
			$agentid = intval($_GPC['agentid']);

			if ($id) {
				pdo_update(PDO_NAME . 'qrcode_apply', array('status' => 2, 'pnum' => $allnum), array('id' => $id));
			}

			include wl_template('sqrcode/qr-process');
			exit();
		}

		$remark_arr = pdo_getall(PDO_NAME . 'agentusers', array('uniacid' => $_W['uniacid']), array('id', 'agentname'));
		include wl_template('sqrcode/qr-post');
	}

	public function get()
	{
		global $_W;
		global $_GPC;
		$qrctype = intval($_GPC['qrc-model']);
		$agentid = intval($_GPC['agentid']);
		$return = Storeqr::creatstoreqr($qrctype, $agentid, $_GPC['remark']);
		exit(json_encode($return));
	}

	public function export($wheresql, $param)
	{
		if (empty($wheresql) || empty($param)) {
			return false;
		}

		set_time_limit(0);
		$list = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'qrcode') . $wheresql . ' ORDER BY `id` DESC', $param);
		$html = "\xef\xbb\xbf";
		$filter = array('agentname' => '所属代理', 'showurl' => '二维码', 'cardsn' => '编号', 'status' => '使用状态', 'model' => '二维码类型', 'qrcid' => '场景ID', 'createtime' => '生成时间');

		foreach ($filter as $key => $title) {
			$html .= $title . '	,';
		}

		$html .= '
';

		foreach ($list as $k => $v) {
			$wq_qr = pdo_get('qrcode', array('id' => $v['qrid']), array('ticket', 'scene_str', 'model', 'id', 'url'));
			$v['scene_str'] = $wq_qr['scene_str'];

			if ($wq_qr['model'] == 3) {
				$str1 = substr($wq_qr['url'], 0, 15);

				if ($str1 == 'http://w.url.cn') {
					$v['showurl'] = $wq_qr['url'];
				}
				else {
					$v['showurl'] = app_url('qr/qrcode', array('ncnumber' => $v['cardsn'], 'salt' => $v['salt']));
				}
			}
			else {
				$v['showurl'] = $wq_qr['url'];
			}

			if ($v['aid']) {
				$v['agentname'] = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $v['aid']), 'agentname');
			}
			else {
				$v['agentname'] = '系统管理员';
			}

			foreach ($filter as $key => $title) {
				if ($key == 'createtime') {
					$html .= date('Y-m-d H:i:s', $v[$key]) . '	, ';
				}
				else if ($key == 'status') {
					switch ($v[$key]) {
					case '1':
						$html .= '未绑定' . '	, ';
						break;

					case '2':
						$html .= '已绑定' . '	, ';
						break;

					default:
						$html .= '已失效' . '	, ';
						break;
					}
				}
				else if ($key == 'model') {
					switch ($v[$key]) {
					case '1':
						$html .= '临时' . '	, ';
						break;

					case '2':
						$html .= '含参' . '	, ';
						break;

					default:
						$html .= '智能' . '	, ';
						break;
					}
				}
				else if ($key == 'qrcid') {
					if (!empty($v['qrcid'])) {
						$html .= $v['qrcid'] . '	, ';
					}
					else {
						$html .= $v['scene_str'] . '	, ';
					}
				}
				else {
					$html .= $v[$key] . '	, ';
				}
			}

			$html .= '
';
		}

		header('Content-type:text/csv');
		header('Content-Disposition:attachment; filename=全部数据.csv');
		echo $html;
		exit();
	}

	public function summary()
	{
		global $_W;
		global $_GPC;
		$where = is_agent() ? 'uniacid = \'' . $_W['uniacid'] . '\' and aid = \'' . $_W['aid'] . '\'' : 'uniacid = \'' . $_W['uniacid'] . '\'';
		$usednum = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'qrcode') . (' WHERE ' . $where . ' and type = 0 and status = 2'));
		$invalidnum = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'qrcode') . (' WHERE ' . $where . ' and type = 0 and status = 3'));
		$notusenum = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'qrcode') . (' WHERE ' . $where . ' and type = 0 and status = 1'));
		$remark_arr = pdo_fetchall('SELECT distinct remark FROM ' . tablename(PDO_NAME . 'qrcode') . ('WHERE ' . $where . ' and type = 0'));
		$remark_arr = Util::i_array_column($remark_arr, 'remark');

		foreach ($remark_arr as $key => $item) {
			$arr2[] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'qrcode') . (' WHERE ' . $where . ' and type = 0 and remark = \'' . $item . '\' '));
			$arr3[] = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'qrcode') . (' WHERE ' . $where . ' and type = 0 and remark = \'' . $item . '\' and status = 2 '));
		}

		$data = json_encode($remark_arr);
		$i = 0;

		while ($i < count($remark_arr)) {
			$data2[$i]['value'] = $arr2[$i];
			$data2[$i]['name'] = $remark_arr[$i];
			++$i;
		}

		$data2 = json_encode($data2);
		$arr2 = json_encode($arr2);
		$arr3 = json_encode($arr3);
		include wl_template('sqrcode/qr-summary');
	}

	public function apply()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, $_GPC['page']);
		$psize = 10;
		$where = is_agent() ? array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']) : array('uniacid' => $_W['uniacid']);
		$list = pdo_getslice(PDO_NAME . 'qrcode_apply', $where, array($pindex, $psize), $total, array(), '', array());
		$pager = pagination($total, $pindex, $psize);

		foreach ($list as $key => &$qrcode) {
			if ($qrcode['type'] == 2) {
				$qrcode['modellabel'] = '含参';
			}
			else if ($qrcode['type'] == 3) {
				$qrcode['modellabel'] = '智能';
			}
			else {
				$qrcode['modellabel'] = '临时';
			}

			$qrcode['agentname'] = pdo_getcolumn(PDO_NAME . 'agentusers', array('uniacid' => $_W['uniacid'], 'id' => $qrcode['aid']), 'agentname');
		}

		include wl_template('sqrcode/qr-apply');
	}

	public function applyno()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			wl_message('缺少重要参数');
		}

		$re = pdo_update(PDO_NAME . 'qrcode_apply', array('status' => 1), array('id' => $id));

		if ($re) {
			wl_message('拒绝代理的申请成功');
		}
		else {
			wl_message('拒绝代理的申请失败');
		}
	}

	public function applydel()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			wl_message('缺少重要参数');
		}

		$re = pdo_delete(PDO_NAME . 'qrcode_apply', array('id' => $id));

		if ($re) {
			wl_message('删除申请成功');
		}
		else {
			wl_message('删除申请失败');
		}
	}

	public function applypost()
	{
		global $_W;
		global $_GPC;

		if (checksubmit('submit')) {
			$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'status' => 0, 'type' => intval($_GPC['qrc-model']), 'num' => intval($_GPC['qr_num']), 'remark' => trim($_GPC['remark']), 'createtime' => time());
			pdo_insert(PDO_NAME . 'qrcode_apply', $data);
			wl_message('申请成功，管理员等待审核！', web_url('storeqr/sqrcode/apply'), 'success');
		}

		include wl_template('sqrcode/qr-applypost');
	}

	public function asetting()
	{
		global $_W;
		global $_GPC;
		$settings = Setting::agentsetting_read('storeqr');

		if (checksubmit('submit')) {
			$base = array('status' => intval($_GPC['status']));
			Setting::agentsetting_save($base, 'storeqr');
			wl_message('保存设置成功！', referer(), 'success');
		}

		include wl_template('sqrcode/qr-asetting');
	}

	public function setting()
	{
		global $_W;
		global $_GPC;
		$settings = Setting::wlsetting_read('storeqr');

		if (checksubmit('submit')) {
			$base = array('enterfast' => $_GPC['enterfast'], 'binding' => $_GPC['binding'], 'autostatus' => intval($_GPC['autostatus']));

			if ($base['autostatus'] == 1) {
				pdo_update('wlmerchant_merchantdata', array('autostoreqr' => 0), array('uniacid' => $_W['uniacid']));
			}

			Setting::wlsetting_save($base, 'storeqr');
			wl_message('保存设置成功！', referer(), 'success');
		}

		include wl_template('sqrcode/qr-setting');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
