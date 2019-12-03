<?php
//dezend by http://www.sucaihuo.com/
class Slide_WeliamController
{
	public function lists()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$list1 = Pocket::getslides($uniacid);
		$list = $list1[0];
		$pager = $list1[1];
		include wl_template('pocket/slidelist');
	}

	public function operating()
	{
		global $_W;
		global $_GPC;
		$did = $_GPC['did'];
		$eid = $_GPC['id'];
		$temp = $_GPC['data'];

		if (!empty($did)) {
			pdo_delete(PDO_NAME . 'pocket_slide', array('id' => $did));
			wl_message('删除数据成功!', web_url('pocket/Slide/lists'), 'success');
		}

		if (!empty($eid)) {
			$data = Util::getSingelData('*', PDO_NAME . 'pocket_slide', array('id' => $eid));
		}

		if ($temp) {
			$temp['uniacid'] = $_W['uniacid'];
			$temp['aid'] = $_W['aid'];

			if ($temp['id']) {
				pdo_update(PDO_NAME . 'pocket_slide', $temp, array('id' => $temp['id']));
			}
			else {
				pdo_insert(PDO_NAME . 'pocket_slide', $temp);
			}

			wl_message('操作成功!', web_url('pocket/Slide/lists'), 'success');
		}

		include wl_template('pocket/slideadd');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
