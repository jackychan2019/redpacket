<?php
//dezend by http://www.sucaihuo.com/
class Type_WeliamController
{
	/**
     * Comment: 进入帖子分类列表
     */
	public function lists()
	{
		global $_W;
		global $_GPC;
		$list = Pocket::gettypes();
		include wl_template('pocket/typelist');
	}

	/**
     * Comment: 进入编辑分类
     */
	public function operating()
	{
		global $_W;
		global $_GPC;

		if ($_GPC['id']) {
			$data = Util::getSingelData('*', PDO_NAME . 'pocket_type', array('id' => $_GPC['id']));
		}

		if ($_GPC['did']) {
			pdo_delete(PDO_NAME . 'pocket_type', array('id' => $_GPC['did']));
			wl_message('删除数据成功', web_url('pocket/Type/lists'), 'success');
		}

		if ($_GPC['data']) {
			$temp = $_GPC['data'];
			$temp['uniacid'] = $_W['uniacid'];
			$temp['aid'] = $_W['aid'];

			if ($_GPC['parentid']) {
				$temp['type'] = $_GPC['parentid'];
				$status = Util::getSingelData('status', PDO_NAME . 'pocket_type', array('id' => $temp['type']));

				if ($status['status'] === 0) {
					$temp['status'] = 0;
				}

				pdo_insert(PDO_NAME . 'pocket_type', $temp);
				wl_message('插入子类型成功', web_url('pocket/Type/lists'), 'success');
			}
			else if ($temp['id']) {
				$status = Util::getSingelData('status', PDO_NAME . 'pocket_type', array('id' => $temp['type']));

				if ($status['status'] === 0) {
					$temp['status'] = 0;
				}

				pdo_update(PDO_NAME . 'pocket_type', $temp, array('id' => $temp['id']));
				$temp = Util::getSingelData('*', PDO_NAME . 'pocket_type', array('id' => $temp['id']));

				if (!$temp['type']) {
					echo $temp['status'];

					if (!$temp['status']) {
						$temp1 = Util::getNumData('*', PDO_NAME . 'pocket_type', array('type' => $temp['id']));
						$temp1 = $temp1[0];

						foreach ($temp1 as $value) {
							$value['status'] = 0;
							pdo_update(PDO_NAME . 'pocket_type', $value, array('id' => $value['id']));
						}
					}

					if ($temp['status'] == 1) {
						$temp1 = Util::getNumData('*', PDO_NAME . 'pocket_type', array('type' => $temp['id']));
						$temp1 = $temp1[0];

						foreach ($temp1 as $value) {
							$value['status'] = 1;
							pdo_update(PDO_NAME . 'pocket_type', $value, array('id' => $value['id']));
						}
					}
				}

				wl_message('修改成功', web_url('pocket/Type/lists'), 'success');
			}
			else {
				pdo_insert(PDO_NAME . 'pocket_type', $temp);
				wl_message('添加类型成功', web_url('pocket/Type/lists'), 'success');
			}
		}

		include wl_template('pocket/typeadd');
	}

	/**
     * Comment: 设置/修改 帖子发帖需要支付的费用
     * Author: zzw
     */
	public function setPrice()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$price = trim($_GPC['value']);
		$res = pdo_update(PDO_NAME . 'pocket_type', array('price' => $price), array('id' => $id));

		if ($res) {
			show_json(1, '修改成功');
		}
		else {
			show_json(0, '修改失败');
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
