<?php
//dezend by http://www.sucaihuo.com/
class Goods_WeliamController
{
	public function goods_list()
	{
		global $_W;
		global $_GPC;
		$condition = ' where uniacid = :uniacid';
		$params = array(':uniacid' => $_W['uniacid']);
		$type = trim($_GPC['type']);

		if (!empty($type)) {
			$condition .= ' and type = :type';
			$params[':type'] = $type;
		}

		if (!empty($_GPC['keyword'])) {
			$condition .= ' AND title LIKE \'%' . $_GPC['keyword'] . '%\'';
		}

		$lists = pdo_fetchall('select * from' . tablename('wlmerchant_consumption_goods') . $condition . ' order by displayorder desc', $params);
		include wl_template('consumption/goods');
	}

	public function goods_post()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (p('distribution')) {
			$distriset = Setting::wlsetting_read('distribution');
		}
		else {
			$distriset = 0;
		}

		if (0 < $id) {
			$item = Consumption::creditshop_goods_get($id);
			$item['redpacket'] = iunserializer($item['redpacket']);
			$advs = unserialize($item['advs']);
		}

		$categorys = Consumption::creditshop_category_get();
		$express = Wlfightgroup::getNumExpress('*', array('uniacid' => $_W['uniacid']), 'ID DESC', 0, 0, 0);
		$express = $express[0];
		$halfcardlist = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halfcard_type') . ('WHERE uniacid = ' . $_W['uniacid'] . ' ORDER BY sort DESC'));

		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'], 'title' => trim($_GPC['title']), 'category_id' => intval($_GPC['creditshop_category_id']), 'type' => trim($_GPC['type']), 'credit2' => trim($_GPC['credit2']), 'old_price' => trim($_GPC['old_price']), 'status' => intval($_GPC['status']), 'thumb' => trim($_GPC['thumb']), 'chance' => intval($_GPC['chance']), 'use_credit1' => trim($_GPC['use_credit1']), 'use_credit2' => trim($_GPC['use_credit2']), 'description' => htmlspecialchars_decode($_GPC['description']), 'displayorder' => intval($_GPC['displayorder']), 'vipstatus' => intval($_GPC['vipstatus']), 'stock' => intval($_GPC['stock']), 'vipcredit1' => intval($_GPC['vipcredit1']), 'vipcredit2' => trim($_GPC['vipcredit2']), 'dissettime' => trim($_GPC['dissettime']), 'halfcardid' => trim($_GPC['halfcardid']), 'advs' => serialize($_GPC['advs']));
			if ($data['vipstatus'] == 1 && $data['vipcredit1'] < 1) {
				show_json(0, array('message' => '会员消耗积分必须大于0'));
			}

			if ($data['type'] == 'halfcard' && empty($data['halfcardid'])) {
				show_json(0, array('message' => '请选择一卡通会员类型'));
			}

			$hour = array();
			$category = array();

			if (!empty($_GPC['category_id'])) {
				foreach ($_GPC['category_id'] as $key => $value) {
					if (empty($value)) {
						continue;
					}

					$category[] = array('id' => intval($value), 'title' => trim($_GPC['category_title'][$key]), 'src' => trim($_GPC['category_src'][$key]));
				}
			}

			$redpacket = array('name' => trim($_GPC['name']), 'discount' => trim($_GPC['discount']), 'condition' => trim($_GPC['condition']), 'grant_days_effect' => intval($_GPC['grant_days_effect']), 'use_days_limit' => intval($_GPC['use_days_limit']), 'hour' => $hour, 'category' => $category);
			$data['redpacket'] = iserializer($redpacket);
			$data['expressid'] = $_GPC['expressid'];
			$data['isdistri'] = $_GPC['isdistri'];

			if ($data['isdistri']) {
				$data['onedismoney'] = $_GPC['onedismoney'];
				$data['twodismoney'] = $_GPC['twodismoney'];
			}

			if (0 < $id) {
				pdo_update('wlmerchant_consumption_goods', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
			}
			else {
				pdo_insert('wlmerchant_consumption_goods', $data);
			}

			show_json(1, array('message' => '编辑商品成功', 'url' => web_url('consumption/goods/goods_list')));
		}

		include wl_template('consumption/goods');
	}

	public function goods_del()
	{
		global $_W;
		global $_GPC;
		$ids = $_GPC['id'];

		if (!is_array($ids)) {
			$ids = array($ids);
		}

		foreach ($ids as $v) {
			pdo_delete('wlmerchant_consumption_goods', array('uniacid' => $_W['uniacid'], 'id' => $v));
		}

		show_json(1, '删除商品成功');
	}

	public function isUpperShelf()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$status = $_GPC['status'];

		if ($status == 1) {
			$updata['status'] = 0;
		}
		else {
			$updata['status'] = 1;
		}

		pdo_update(PDO_NAME . 'consumption_goods', $updata, array('id' => $id));
		wl_json(1, '请求成功', $updata);
	}
}

defined('IN_IA') || exit('Access Denied');

?>
