<?php
//dezend by http://www.sucaihuo.com/
class newCash_WeliamController
{
	public function currentlist()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = is_agent() ? array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']) : array('uniacid' => $_W['uniacid']);
		$type = trim($_GPC['type']);
		$where['status'] = $type == 'store' ? 1 : 2;

		if ($type == 'store') {
			if (is_agent()) {
				$stores = pdo_getall('wlmerchant_merchantdata', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), array('storename', 'id'));
			}
			else {
				$stores = pdo_getall('wlmerchant_merchantdata', array('uniacid' => $_W['uniacid']), array('storename', 'id'));
			}
		}
		else {
			$agents = pdo_getall('wlmerchant_agentusers', array('uniacid' => $_W['uniacid']), array('agentname', 'id'));
		}

		$objid = intval($_GPC['objid']);

		if ($objid) {
			$where['objid'] = $objid;
		}
		else {
			if ($type == 'agent' && is_agent()) {
				$where['objid'] = $_W['aid'];
			}
		}

		$trade_type = intval($_GPC['trade_type']);

		if ($trade_type) {
			$where['type'] = $trade_type;
		}

		$days = isset($_GPC['days']) ? intval($_GPC['days']) : -2;
		$todaytime = strtotime(date('Y-m-d'));
		$starttime = $todaytime;
		$endtime = $starttime + 86399;

		if (-2 < $days) {
			if ($days == -1) {
				if (empty($_GPC['addtime'])) {
					$days = -2;
				}
				else {
					$starttime = strtotime($_GPC['addtime']['start']);
					$endtime = strtotime($_GPC['addtime']['end']);
					$where['createtime>'] = $starttime;
					$where['createtime<'] = $endtime;
				}
			}
			else {
				$starttime = strtotime('-' . $days . ' days', $todaytime);
				$where['createtime>'] = $starttime;
			}
		}

		$records = Util::getNumData('*', 'wlmerchant_current', $where, 'ID DESC', $pindex, $psize, 1);
		$pager = $records[1];
		$records = $records[0];

		foreach ($records as $key => &$re) {
			if ($re['status'] == 1) {
				$re['objname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $re['objid']), 'storename');
			}
			else {
				if ($re['status'] == 2) {
					$re['objname'] = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $re['objid']), 'agentname');
				}
			}

			if ($re['type'] == 1) {
				$re['typename'] = '抢购订单结算';
				$re['css'] = 'success';
			}
			else if ($re['type'] == 10) {
				$re['typename'] = '团购订单结算';
				$re['css'] = 'info';
			}
			else if ($re['type'] == 2) {
				$re['typename'] = '拼团订单结算';
				$re['css'] = 'warning';
			}
			else if ($re['type'] == 3) {
				$re['typename'] = '卡券订单结算';
				$re['css'] = 'success';
			}
			else if ($re['type'] == 4) {
				$re['typename'] = '一卡通订单结算';
				$re['css'] = 'info';
			}
			else if ($re['type'] == 5) {
				$re['typename'] = '掌上信息订单结算';
				$re['css'] = 'success';
			}
			else if ($re['type'] == 6) {
				$re['typename'] = '付费入驻订单结算';
				$re['css'] = 'info';
			}
			else if ($re['type'] == 7) {
				if ($re['fee'] < 0) {
					$re['typename'] = '提现申请';
					$re['css'] = 'default';
				}
				else {
					$re['typename'] = '提现申请驳回';
					$re['css'] = 'danger';
				}
			}
			else if ($re['type'] == 8) {
				$re['typename'] = '分销合伙人订单结算';
				$re['css'] = 'warning';
			}
			else if ($re['type'] == 9) {
				$re['typename'] = '商户活动订单结算';
				$re['css'] = 'warning';
			}
			else if ($re['type'] == -1) {
				$re['typename'] = '后台修改';
				$re['css'] = 'default';
			}
			else if ($re['type'] == 11) {
				$re['typename'] = '在线买单';
				$re['css'] = 'warning';
			}
			else {
				if ($re['type'] == 12) {
					$re['typename'] = '砍价订单结算';
					$re['css'] = 'success';
				}
			}
		}

		include wl_template('finace/currentlist');
	}

	public function cashrecord()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = is_agent() ? array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']) : array('uniacid' => $_W['uniacid']);

		if (!empty($_GPC['keyword'])) {
			if (!empty($_GPC['keywordtype'])) {
				switch ($_GPC['keywordtype']) {
				case 1:
					$where['@orderno@'] = $_GPC['keyword'];
					break;

				case 2:
					$where['orderprice>'] = $_GPC['keyword'];
					break;

				case 3:
					$where['orderprice<'] = $_GPC['keyword'];
					break;

				case 4:
					$where['agentmoney>'] = $_GPC['keyword'];
					break;

				case 5:
					$where['agentmoney<'] = $_GPC['keyword'];
					break;

				default:
					break;
				}

				if ($_GPC['keywordtype'] == 6) {
					$keyword = $_GPC['keyword'];
					$params[':storename'] = '%' . $keyword . '%';
					$stores = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_merchantdata') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND storename LIKE :storename'), $params);

					if ($stores) {
						$storesids = '(';

						foreach ($stores as $key => $v) {
							if ($key == 0) {
								$storesids .= $v['id'];
							}
							else {
								$storesids .= ',' . $v['id'];
							}
						}

						$storesids .= ')';
						$where['merchantid#'] = $storesids;
					}
					else {
						$where['merchantid#'] = '(0)';
					}
				}
			}
		}

		$cashtype = trim($_GPC['cashtype']);

		if (!empty($cashtype)) {
			$where['type'] = $cashtype;
		}

		if ($_GPC['time_limit']) {
			$time_limit = $_GPC['time_limit'];
			$starttime = strtotime($_GPC['time_limit']['start']);
			$endtime = strtotime($_GPC['time_limit']['end']);
			$where['createtime>'] = $starttime;
			$where['createtime<'] = $endtime;
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time() + 86400;
		}

		if ($_GPC['export'] != '') {
			$this->export($where);
		}

		$records = Util::getNumData('*', 'wlmerchant_autosettlement_record', $where, 'ID DESC', $pindex, $psize, 1);
		$pager = $records[1];
		$records = $records[0];

		foreach ($records as $key => &$va) {
			$va['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $va['merchantid']), 'storename');

			if (empty($va['orderno'])) {
				$va['orderno'] = $va['orderid'];
			}

			if ($va['type'] == 1) {
				$va['typename'] = '抢购';
				$va['title'] = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $va['goodsid']), 'name');
				$va['orderurl'] = web_url('order/wlOrder/orderdetail', array('type' => 1, 'orderid' => $va['orderid']));
			}
			else if ($va['type'] == 10) {
				$va['typename'] = '团购';
				$va['title'] = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $va['goodsid']), 'name');
				$va['orderurl'] = web_url('order/wlOrder/orderdetail', array('type' => 10, 'orderid' => $va['orderid']));
			}
			else if ($va['type'] == 2) {
				$va['typename'] = '拼团';
				$va['title'] = pdo_getcolumn(PDO_NAME . 'fightgroup_goods', array('id' => $va['goodsid']), 'name');
				$va['orderurl'] = web_url('order/wlOrder/orderdetail', array('type' => 2, 'orderid' => $va['orderid']));
			}
			else if ($va['type'] == 3) {
				$va['typename'] = '卡券';
				$va['title'] = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $va['goodsid']), 'title');
				$va['orderurl'] = web_url('order/wlOrder/orderdetail', array('type' => 3, 'orderid' => $va['orderid']));
			}
			else if ($va['type'] == 4) {
				$va['typename'] = '一卡通';
				$va['title'] = pdo_getcolumn(PDO_NAME . 'halfcard_type', array('id' => $va['goodsid']), 'name');
				$va['orderurl'] = web_url('order/wlOrder/orderdetail', array('type' => 4, 'orderid' => $va['orderid']));
			}
			else if ($va['type'] == 5) {
				$va['typename'] = '掌上信息';
				$va['title'] = '付费发帖';
				$va['orderurl'] = web_url('order/wlOrder/orderdetail', array('type' => 5, 'orderid' => $va['orderid']));
			}
			else if ($va['type'] == 6) {
				$va['typename'] = '付费入驻';
				$va['title'] = pdo_getcolumn(PDO_NAME . 'chargelist', array('id' => $va['goodsid']), 'name');
				$va['orderurl'] = web_url('order/wlOrder/orderdetail', array('type' => 6, 'orderid' => $va['orderid']));
			}
			else if ($va['type'] == 7) {
				if ($va['merchantid']) {
					if ($va['merchantmoney'] < 0) {
						$va['typename'] = '提现申请';
						$va['title'] = '商户申请提现';
					}
					else {
						$va['typename'] = '提现申请驳回';
						$va['title'] = '商户申请被驳回';
					}
				}
				else if ($va['agentmoney'] < 0) {
					$va['typename'] = '提现申请';
					$va['title'] = '代理申请提现';
				}
				else {
					$va['typename'] = '提现申请驳回';
					$va['title'] = '代理申请被驳回';
				}

				$va['orderurl'] = web_url('finace/wlCash/cashApply', array('orderid' => $va['orderid']));
			}
			else if ($va['type'] == 8) {
				$va['typename'] = '分销合伙人';
				$va['title'] = '付费成为分销商';
				$va['orderurl'] = web_url('order/wlOrder/orderdetail', array('type' => 8, 'orderid' => $va['orderid']));
			}
			else if ($va['type'] == 9) {
				$va['typename'] = '商户活动';
				$va['title'] = pdo_getcolumn(PDO_NAME . 'activitylist', array('id' => $va['goodsid']), 'title');
				$va['orderurl'] = web_url('order/wlOrder/orderdetail', array('type' => 9, 'orderid' => $va['orderid']));
			}
			else if ($va['type'] == 11) {
				$va['typename'] = '在线买单';
				$va['title'] = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('id' => $va['goodsid']), 'title');
				$va['title'] = $va['title'] ? $va['title'] : '买家在线买单';
				$va['orderurl'] = web_url('order/wlOrder/orderdetail', array('type' => 11, 'orderid' => $va['orderid']));
			}
			else {
				if ($va['type'] == 12) {
					$va['typename'] = '砍价商品';
					$va['title'] = pdo_getcolumn(PDO_NAME . 'bargain_activity', array('id' => $va['goodsid']), 'name');
					$va['orderurl'] = web_url('order/wlOrder/orderdetail', array('type' => 12, 'orderid' => $va['orderid']));
				}
			}
		}

		include wl_template('finace/cashrecord');
	}

	public function editremark()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$remark = trim($_GPC['value']);
		$type = $_GPC['type'];

		if ($type == 1) {
			$res = pdo_update('wlmerchant_rush_order', array('adminremark' => $remark), array('id' => $id));
		}
		else {
			$res = pdo_update('wlmerchant_order', array('remark' => $remark), array('id' => $id));
		}

		if ($res) {
			show_json(1, '备注修改成功');
		}
		else {
			show_json(0, '备注修改失败,请刷新页面重试！');
		}
	}

	public function refundrecord()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where['uniacid'] = $_W['uniacid'];

		if (is_agent()) {
			$where['aid'] = $_W['aid'];
		}

		if ($_GPC['paytype']) {
			$where['paytype'] = $_GPC['paytype'];
		}

		if ($_GPC['type']) {
			$where['type'] = $_GPC['type'];
		}

		if ($_GPC['plugin'] && $_GPC['plugin'] != 'all') {
			$where['plugin'] = $_GPC['plugin'];
		}

		if (!empty($_GPC['keyword'])) {
			if (!empty($_GPC['keywordtype'])) {
				switch ($_GPC['keywordtype']) {
				case 1:
					$where['@orderno@'] = $_GPC['keyword'];
					break;

				case 2:
					$where['@transid@'] = $_GPC['keyword'];
					break;

				default:
					break;
				}

				if ($_GPC['keywordtype'] == 3) {
					$keyword = $_GPC['keyword'];
					$params[':name'] = '%' . $keyword . '%';
					$stores = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_member') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND nickname LIKE :name'), $params);

					if ($stores) {
						$storesids = '(';

						foreach ($stores as $key => $v) {
							if ($key == 0) {
								$storesids .= $v['id'];
							}
							else {
								$storesids .= ',' . $v['id'];
							}
						}

						$storesids .= ')';
						$where['mid#'] = $storesids;
					}
					else {
						$where['mid#'] = '(0)';
					}
				}

				if ($_GPC['keywordtype'] == 4) {
					$keyword = $_GPC['keyword'];
					$params[':mobile'] = '%' . $keyword . '%';
					$stores = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_member') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mobile LIKE :mobile'), $params);

					if ($stores) {
						$storesids = '(';

						foreach ($stores as $key => $v) {
							if ($key == 0) {
								$storesids .= $v['id'];
							}
							else {
								$storesids .= ',' . $v['id'];
							}
						}

						$storesids .= ')';
						$where['mid#'] = $storesids;
					}
					else {
						$where['mid#'] = '(0)';
					}
				}

				if ($_GPC['keywordtype'] == 5) {
					$keyword = $_GPC['keyword'];
					$params[':storename'] = '%' . $keyword . '%';
					$stores = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_merchantdata') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND storename LIKE :storename'), $params);

					if ($stores) {
						$storesids = '(';

						foreach ($stores as $key => $v) {
							if ($key == 0) {
								$storesids .= $v['id'];
							}
							else {
								$storesids .= ',' . $v['id'];
							}
						}

						$storesids .= ')';
						$where['sid#'] = $storesids;
					}
					else {
						$where['sid#'] = '(0)';
					}
				}
			}
		}

		if ($_GPC['time_limit']) {
			$time_limit = $_GPC['time_limit'];
			$starttime = strtotime($_GPC['time_limit']['start']);
			$endtime = strtotime($_GPC['time_limit']['end']);
			$where['createtime>'] = $starttime;
			$where['createtime<'] = $endtime;
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time() + 86400;
		}

		$records = Util::getNumData('*', 'wlmerchant_refund_record', $where, 'createtime DESC', $pindex, $psize, 1);
		$pager = $records[1];
		$records = $records[0];

		foreach ($records as $key => &$re) {
			$re['createtime'] = date('Y-m-d H:i:s', $re['createtime']);
			$re['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $re['sid']), 'storename');
			$member = pdo_get('wlmerchant_member', array('id' => $re['mid']), array('avatar', 'nickname'));
			$re['avatar'] = $member['avatar'];
			$re['nickname'] = $member['nickname'];

			if ($re['plugin'] == 'rush') {
				$goodsid = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $re['orderid']), 'activityid');
				$re['goodsname'] = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $goodsid), 'name');
				$re['pluginname'] = '抢购';
			}
			else if ($re['plugin'] == 'wlfightgroup') {
				$goodsid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $re['orderid']), 'fkid');
				$re['goodsname'] = pdo_getcolumn(PDO_NAME . 'fightgroup_goods', array('id' => $goodsid), 'name');
				$re['pluginname'] = '拼团';
			}
			else if ($re['plugin'] == 'groupon') {
				$goodsid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $re['orderid']), 'fkid');
				$re['goodsname'] = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $goodsid), 'name');
				$re['pluginname'] = '团购';
			}
			else {
				if ($re['plugin'] == 'bargain') {
					$goodsid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $re['orderid']), 'fkid');
					$re['goodsname'] = pdo_getcolumn(PDO_NAME . 'bargain_activity', array('id' => $goodsid), 'name');
					$re['pluginname'] = '砍价';
				}
			}

			switch ($re['paytype']) {
			case '2':
				$re['paytype'] = '微信支付';
				break;

			case '1':
				$re['paytype'] = '余额支付';
				break;

			case '3':
				$re['paytype'] = '支付宝';
				break;

			case '4':
				$re['paytype'] = '货到付款';
				break;

			case '5':
				$re['paytype'] = '小程序';
				break;

			default:
				$re['paytype'] = '未知方式';
				break;
			}

			switch ($re['type']) {
			case '1':
				$re['type'] = '手机端退款';
				break;

			case '2':
				$re['type'] = '后台退款';
				break;

			case '3':
				$re['type'] = '自动退款';
				break;

			default:
				$re['type'] = '其他';
				break;
			}
		}

		include wl_template('finace/refundrecord');
	}

	public function export($where)
	{
		if (empty($where)) {
			return false;
		}

		set_time_limit(0);
		$records = Util::getNumData('*', 'wlmerchant_autosettlement_record', $where, 'ID DESC', 0, 0, 1);
		$records = $records[0];

		foreach ($records as $key => &$rec) {
			$rec['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $rec['merchantid']), 'storename');

			if (empty($rec['orderno'])) {
				$rec['orderno'] = $rec['orderid'];
			}

			if ($rec['type'] == 1) {
				$rec['typename'] = '抢购';
				$rec['title'] = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $rec['goodsid']), 'name');
			}
			else if ($rec['type'] == 2) {
				$rec['typename'] = '拼团';
				$rec['title'] = pdo_getcolumn(PDO_NAME . 'fightgroup_goods', array('id' => $rec['goodsid']), 'name');
			}
			else if ($rec['type'] == 3) {
				$rec['typename'] = '卡券';
				$rec['title'] = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $rec['goodsid']), 'title');
			}
			else if ($rec['type'] == 4) {
				$rec['typename'] = '一卡通';
				$rec['title'] = pdo_getcolumn(PDO_NAME . 'halfcard_type', array('id' => $rec['goodsid']), 'name');
			}
			else if ($rec['type'] == 5) {
				$rec['typename'] = '掌上信息';
				$rec['title'] = '付费发帖';
			}
			else if ($rec['type'] == 6) {
				$rec['typename'] = '付费入驻';
				$rec['title'] = pdo_getcolumn(PDO_NAME . 'chargelist', array('id' => $rec['goodsid']), 'name');
			}
			else if ($rec['type'] == 7) {
				if ($rec['agentmoney'] < 0) {
					$rec['typename'] = '提现申请';
					$rec['title'] = '代理申请提现';
				}
				else {
					$rec['typename'] = '提现申请驳回';
					$rec['title'] = '代理申请被驳回';
				}
			}
			else if ($rec['type'] == 8) {
				$rec['typename'] = '分销合伙人';
				$rec['title'] = '付费成为分销商';
			}
			else if ($rec['type'] == 9) {
				$rec['typename'] = '商户活动';
				$rec['title'] = pdo_getcolumn(PDO_NAME . 'activitylist', array('id' => $rec['goodsid']), 'title');
			}
			else if ($rec['type'] == 10) {
				$rec['typename'] = '团购';
				$rec['title'] = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $rec['goodsid']), 'name');
			}
			else if ($rec['type'] == 11) {
				$rec['typename'] = '在线买单';
				$rec['title'] = pdo_getcolumn(PDO_NAME . 'halfcardlist', array('id' => $rec['goodsid']), 'title');
				$rec['title'] = $rec['title'] ? $rec['title'] : '买家在线买单';
			}
			else {
				if ($rec['type'] == 12) {
					$rec['typename'] = '砍价商品';
					$rec['title'] = pdo_getcolumn(PDO_NAME . 'bargain_activity', array('id' => $rec['goodsid']), 'name');
				}
			}

			$rec['createtime'] = date('Y-m-d H:i:s', $rec['createtime']);
			$rec['title'] = str_replace(',', '，', $rec['title']);
		}

		$html = "\xef\xbb\xbf";
		$filter = array('orderno' => '订单号', 'title' => '商品名称', 'storename' => '所属商家', 'typename' => '结算类型', 'orderprice' => '订单金额', 'merchantmoney' => '商户收入', 'agentmoney' => '代理收入', 'agentnowmoney' => '结算后代理余额', 'createtime' => '结算时间');

		foreach ($filter as $key => $title) {
			$html .= $title . '	,';
		}

		$html .= '
';

		foreach ($records as $k => $v) {
			foreach ($filter as $key => $title) {
				if ($key == 'orderprice' || $key == 'merchantmoney' || $key == 'agentmoney' || $key == 'agentnowmoney') {
					$html .= trim($v[$key]) . trim('	') . ',';
				}
				else {
					$html .= $v[$key] . '	, ';
				}
			}

			$html .= '
';
		}

		header('Content-type:text/csv');
		header('Content-Disposition:attachment; filename=结算记录.csv');
		echo $html;
		exit();
	}
}

defined('IN_IA') || exit('Access Denied');

?>
