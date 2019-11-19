<?php
//dezend by http://www.sucaihuo.com/
class WlCash_WeliamController
{
	public function cashSurvey()
	{
		global $_W;
		global $_GPC;
		$refresh = $_GPC['refresh'] ? 1 : 0;
		$timetype = $_GPC['timetype'];
		$time_limit = $_GPC['time_limit'];
		$merchantid = $_GPC['merid'];

		if ($time_limit) {
			$starttime = strtotime($_GPC['time_limit']['start']);
			$endtime = strtotime($_GPC['time_limit']['end']);
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		$merchants = pdo_getall('wlmerchant_merchantdata', array('aid' => $_W['aid']));
		$data = Merchant::agentCashSurvey($refresh, $timetype, $starttime, $endtime, $merchantid);
		$agents = $data[0];
		$children = $data[1];
		$max = $data[2];
		$allMoney = $data[3];
		$time = $data[4];
		$newdata = $data[5];
		include wl_template('finace/cashSurvey');
	}

	public function cashset()
	{
		global $_W;
		global $_GPC;
		$cashset = Setting::agentsetting_read('cashset');

		if (checksubmit('submit')) {
			$set = $_GPC['cashset'];
			$res1 = Setting::agentsetting_save($set, 'cashset');

			if ($res1) {
				wl_message('保存设置成功！', referer(), 'success');
			}
			else {
				wl_message('保存设置失败！', referer(), 'error');
			}
		}

		include wl_template('finace/cashset');
	}

	public function cashApply()
	{
		global $_W;
		global $_GPC;
		if ($_GPC['type'] == 'submit' && !empty($_GPC['id'])) {
			pdo_update(PDO_NAME . 'settlement_record', array('status' => 2, 'updatetime' => TIMESTAMP), array('id' => $_GPC['id']));
			wl_message('提交成功！', web_url('finace/wlCash/cashApply', array('status' => 2)), 'success');
		}
		else {
			if ($_GPC['type'] == 'reject' && !empty($_GPC['id'])) {
				pdo_update(PDO_NAME . 'settlement_record', array('status' => '-2', 'updatetime' => TIMESTAMP), array('id' => $_GPC['id']));
				$record = pdo_get(PDO_NAME . 'settlement_record', array('id' => $_GPC['id']), array('type', 'sid', 'aid', 'sapplymoney', 'id'));

				if ($record['type'] == 1) {
					Store::settlement($record['id'], 0, $record['sid'], $record['sapplymoney'], 0, $record['sapplymoney'], 7, 0);
				}
				else {
					if ($record['type'] == 2) {
						Store::settlement($record['id'], 0, $record['sid'], $record['sapplymoney'], 0, 0, 7, 0);
					}
				}

				wl_message('驳回成功！', web_url('finace/wlCash/cashApply', array('status' => 6)), 'success');
			}
			else {
				$where = array();
				$status = $_GPC['status'] ? $_GPC['status'] : 1;

				if ($status == 1) {
					$where['status'] = 1;
				}

				if ($status == 2) {
					$where['status'] = 2;
				}

				if ($status == 3) {
					$where['status'] = 3;
				}

				if ($status == 4) {
					$where['status'] = 4;
				}

				if ($status == 5) {
					$where['status'] = 5;
				}

				if ($status == 6) {
					$where['#status#'] = '(-1,-2)';
				}

				$where['type'] = 1;
				$where['aid'] = $_W['aid'];
				$list = Util::getNumData('*', PDO_NAME . 'settlement_record', $where);
				$list = $list[0];

				foreach ($list as $key => &$value) {
					$value['sName'] = Util::idSwitch('sid', 'sName', $value['sid']);
					$value['aName'] = Util::idSwitch('aid', 'aName', $value['aid']);
				}
			}
		}

		include wl_template('finace/cashConfirm');
	}

	public function cashApplyAgent()
	{
		global $_W;
		global $_GPC;
		$a = Util::getSingelData('percent,cashopenid,allmoney,nowmoney,payment_type,alipay,card_number', PDO_NAME . 'agentusers', array('id' => $_W['aid']));
		$user = pdo_get('wlmerchant_member', array('openid' => $a['cashopenid']), array('avatar', 'realname', 'nickname'));
		$p = unserialize($a['percent']);
		$cashsets = Setting::wlsetting_read('cashset');
		$agentpercent = $p['agentpercent'] ? $p['agentpercent'] : $cashsets['agentpercent'];
		$cashsets['lowsetmoney'] = $cashsets['lowsetmoney'] ? $cashsets['lowsetmoney'] : 0;
		$money = $num = 0;

		if (0 < $_GPC['money']) {
			$money = $_GPC['money'];
			$num = $_GPC['num'];

			if ($num < $cashsets['lowsetmoney']) {
				exit(json_encode(array('errno' => 1, 'message' => '申请金额小于最低结算金额！')));
			}
			else if ($a['nowmoney'] < $num) {
				exit(json_encode(array('errno' => 1, 'message' => '申请金额大于账户现有金额！')));
			}
			else {
				$data = array('uniacid' => $_W['uniacid'], 'sid' => 0, 'aid' => $_W['aid'], 'status' => 2, 'type' => 2, 'sapplymoney' => $num, 'sgetmoney' => sprintf('%.2f', $num - $agentpercent * $num / 100), 'spercentmoney' => sprintf('%.2f', $agentpercent * $num / 100), 'spercent' => $agentpercent, 'applytime' => TIMESTAMP, 'updatetime' => TIMESTAMP, 'sopenid' => $a['cashopenid']);

				if ($a['payment_type'] == 1) {
					if ($a['alipay']) {
						$data['payment_type'] = 1;
					}
					else {
						wl_json(1, '请填写支付宝账号信息');
					}
				}
				else if ($a['payment_type'] == 2) {
					$data['payment_type'] = 2;
				}
				else if ($a['payment_type'] == 3) {
					if ($a['alipay']) {
						$data['payment_type'] = 3;
					}
					else {
						wl_json(1, '请填写银行卡账号信息');
					}
				}
				else {
					wl_json(1, '请选择收款方式');
				}

				if ($cashsets['noaudit']) {
					$data['status'] = 3;
					$trade_no = time() . random(4, true);
					$data['trade_no'] = $trade_no;
					$data['updatetime'] = time();
				}

				if (pdo_insert(PDO_NAME . 'settlement_record', $data)) {
					$settid = pdo_insertid();
					$res = Store::settlement($settid, 0, 0, 0 - $num, 0, 0, 7);
					if ($cashsets['agentautocash'] && $data['payment_type'] == 2) {
						Queue::addTask(4, $settid, time(), $settid);
					}

					if ($res) {
						if (!empty($_W['wlsetting']['noticeMessage']['adminopenid'])) {
							$storename = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $_W['aid']), 'agentname');
							$first = '您好，有一个代理提现申请待审核。';
							$keyword1 = '代理[' . $storename . ']申请提现' . $num . '元';
							$keyword2 = '待审核';
							$remark = '请尽快前往系统后台审核';
							$url = app_url('dashboard/home/index');
							Message::jobNotice($_W['wlsetting']['noticeMessage']['adminopenid'], $first, $keyword1, $keyword2, $remark, $url);
						}

						exit(json_encode(array('errno' => 0, 'message' => '申请成功')));
					}
					else {
						exit(json_encode(array('errno' => 1, 'message' => '申请失败！')));
					}
				}
			}

			exit(json_encode(array('errno' => 1, 'message' => '申请失败！')));
		}

		if ($_GPC['num']) {
			$num = $_GPC['num'];

			if ($a['nowmoney'] < $num) {
				exit(json_encode(array('errno' => 1, 'message' => '申请金额大于账户现有金额！')));
			}

			$percentMoney = sprintf('%.2f', $agentpercent * $num / 100);
			$money = sprintf('%.2f', $num - $agentpercent * $num / 100);
			exit(json_encode(array('errno' => 0, 'num' => $num, 'percentMoney' => $percentMoney, 'money' => $money)));
		}

		include wl_template('finace/agentApply');
	}

	public function account()
	{
		global $_W;
		global $_GPC;
		$a = Util::getSingelData('cashopenid,bank_username,payment_type,bank_name,card_number,alipay', PDO_NAME . 'agentusers', array('id' => $_W['aid']));

		if ($a['cashopenid']) {
			$user = pdo_get(PDO_NAME . 'member', array('openid' => $a['cashopenid']), array('avatar', 'realname', 'nickname', 'openid'));
		}

		if (checksubmit('submit')) {
			$cashopenid = $_GPC['openid'];
			$realname = $_GPC['realname'];
			$data = $_GPC['data'];
			$data['cashopenid'] = $cashopenid;
			$res1 = pdo_update(PDO_NAME . 'agentusers', $data, array('id' => $_W['aid']));
			$res2 = pdo_update(PDO_NAME . 'member', array('realname' => $realname), array('openid' => $cashopenid));
			wl_message('保存成功！', referer(), 'success');
		}

		$cashset = Setting::wlsetting_read('cashset')['payment_type'];
		include wl_template('finace/useraccount');
	}

	public function cashApplyAgentRecord()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = array(
			'uniacid' => $_W['uniacid'],
			'type'    => array(1, 2),
			'aid'     => $_W['aid']
		);

		if ($_GPC['orderid']) {
			$where['id'] = $_GPC['orderid'];
		}

		$list = pdo_getslice(PDO_NAME . 'settlement_record', $where, array($pindex, $psize), $total, array(), '', 'id DESC');

		foreach ($list as $key => &$value) {
			if ($value['type'] == 1) {
				$value['aName'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $value['sid']), 'storename');
			}
			else {
				$value['aName'] = Util::idSwitch('aid', 'aName', $value['aid']);
				$value['spercent'] = sprintf('%.2f', $value['spercent']);
			}
		}

		$pager = pagination($total, $pindex, $psize);
		include wl_template('finace/cashApplyAgentRecord');
	}

	public function detail()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$where = array();
		$where['id'] = $id;
		$settlementRecord = Util::getSingelData('*', PDO_NAME . 'settlement_record', $where);
		$settlementRecord['sName'] = Util::idSwitch('sid', 'sName', $settlementRecord['sid']);
		$settlementRecord['aName'] = Util::idSwitch('aid', 'aName', $settlementRecord['aid']);
		$orders = unserialize($settlementRecord['ids']);
		$list = array();

		foreach ($orders as $id) {
			if ($settlementRecord['type'] == 1) {
				if ($settlementRecord['type2'] == 1) {
					$v = Util::getSingelData('*', PDO_NAME . 'order', array('id' => $id));
					$coupon = pdo_get('wlmerchant_couponlist', array('id' => $v['fkid']), array('title', 'logo'));
					$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $v['sid']), array('storename'));
					$member = pdo_get('wlmerchant_member', array('id' => $v['mid']), array('nickname', 'avatar', 'mobile'));
					$v['gname'] = $coupon['title'];
					$v['gimg'] = tomedia($coupon['logo']);
					$v['storename'] = $merchant['storename'];
					$v['nickname'] = $member['nickname'];
					$v['headimg'] = $member['avatar'];
					$v['mobile'] = $member['mobile'];
					$list[] = $v;
				}
				else if ($settlementRecord['type2'] == 2) {
					$v = Util::getSingelData('*', PDO_NAME . 'order', array('id' => $id));
					$fightgoods = pdo_get('wlmerchant_fightgroup_goods', array('id' => $v['fkid']), array('name', 'logo'));
					$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $v['sid']), array('storename'));
					$member = pdo_get('wlmerchant_member', array('id' => $v['mid']), array('nickname', 'avatar', 'mobile'));
					$v['gname'] = $fightgoods['name'];
					$v['gimg'] = tomedia($fightgoods['logo']);
					$v['storename'] = $merchant['storename'];
					$v['nickname'] = $member['nickname'];
					$v['headimg'] = $member['avatar'];
					$v['mobile'] = $member['mobile'];
					$list[] = $v;
				}
				else {
					$list[] = Rush::getSingleOrder($id, '*');
				}
			}

			if ($settlementRecord['type'] == 2) {
				$list[] = Util::getSingelData('*', PDO_NAME . 'vip_record', array('id' => $id));
			}

			if ($settlementRecord['type'] == 3) {
				$list[] = Util::getSingelData('*', PDO_NAME . 'halfcard_record', array('id' => $id));
			}
		}

		if ($settlementRecord['type'] == 2) {
			foreach ($list as $key => &$value) {
				$value['areaName'] = Util::idSwitch('areaid', 'areaName', $value['areaid']);
				$value['member'] = Member::getMemberByMid($value['mid']);
			}
		}

		if ($settlementRecord['type'] == 3) {
			foreach ($list as $key => &$v) {
				$user = pdo_get('wlmerchant_member', array('id' => $v['mid']));
				$v['nickname'] = $user['nickname'];
				$v['avatar'] = $user['avatar'];
				$v['mobile'] = $user['mobile'];
			}
		}

		include wl_template('finace/cashDetail');
	}

	public function settlement()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$where = array();
		$where['id'] = $id;
		$settlementRecord = Util::getSingelData('*', PDO_NAME . 'settlement_record', $where);
		$settlementRecord['sName'] = Util::idSwitch('sid', 'sName', $settlementRecord['sid']);
		$settlementRecord['aName'] = Util::idSwitch('aid', 'aName', $settlementRecord['aid']);
		$_GPC['type'] = $_GPC['type'] ? $_GPC['type'] : 'settlement';

		if ($_GPC['type'] == 'money_record') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 15;
			$moneyRecordData = SingleMerchant::getMoneyRecord($id, $pindex, $psize, 1);
			$moneyRecord = $moneyRecordData[0];
			$pager = $moneyRecordData[1];

			foreach ($moneyRecord as &$item) {
				if ($item['orderid']) {
					$item['order'] = Rush::getSingleOrder($item['orderid'], '*');
				}
			}
		}

		if ($_GPC['type'] == 'settlement_record') {
			$id = $_GPC['id'];
			$merchant = Store::getSingleStore($id);
			$account = pdo_fetch('SELECT * FROM ' . tablename('tg_merchant_account') . (' WHERE uniacid = ' . $_W['uniacid'] . ' and merchantid=' . $id));
			$merchant['amount'] = $account['amount'];
			$merchant['no_money'] = $account['no_money'];
			$merchant['no_money_doing'] = $account['no_money_doing'];
			$list = pdo_fetchall('select * from' . tablename('tg_merchant_record') . ('where merchantid=\'' . $id . '\' and uniacid=' . $_W['uniacid'] . ' '));
		}

		if ($_GPC['type'] == 'settlement') {
			$merchant = Store::getSingleStore($settlementRecord['sid']);
			$_GPC['accountType'] = $_GPC['accountType'] ? $_GPC['accountType'] : 'f2f';
			if (checksubmit('submit') && $_GPC['accountType'] == 'weixin') {
			}

			if (checksubmit('submit') && $_GPC['accountType'] == 'f2f') {
				if ($settlementRecord['status'] != 4) {
					wl_message('结算状态错误！', web_url('finace/wlCash/cashApply'), 'error');
				}

				$money = $_GPC['money'];
				$spercent = $_GPC['spercent'] ? $_GPC['spercent'] : $settlementRecord['spercent'];
				$spercentMoney = $_GPC['spercentMoney'];

				if (is_numeric($money)) {
					if (pdo_update(PDO_NAME . 'settlement_record', array('status' => 5, 'updatetime' => TIMESTAMP, 'spercentmoney' => $spercentMoney, 'spercent' => $spercent, 'sgetmoney' => $money), array('id' => $_GPC['id']))) {
						$orders = unserialize($settlementRecord['ids']);

						foreach ($orders as $iid) {
							if ($settlementRecord['type2']) {
								pdo_update(PDO_NAME . 'order', array('issettlement' => 2), array('id' => $iid));
							}
							else {
								pdo_update(PDO_NAME . 'rush_order', array('issettlement' => 2), array('id' => $iid));
							}
						}
					}

					wl_message('已结算给商家！', web_url('finace/wlCash/cashApply', array('status' => 5)), 'success');
				}
				else {
					wl_message('结算金额输入错误！', referer(), 'error');
				}

				wl_message('操作成功！', referer(), 'success');
			}
		}

		include wl_template('finace/account');
	}

	public function output()
	{
		global $_W;
		global $_GPC;
		$where['id'] = $_GPC['id'];
		$settlementRecord = Util::getSingelData('*', PDO_NAME . 'settlement_record', $where);
		$orders = unserialize($settlementRecord['ids']);
		$list = array();

		if ($settlementRecord['type'] == 1) {
			foreach ($orders as $id) {
				if ($settlementRecord['type2'] == 1) {
					$v = Util::getSingelData('*', PDO_NAME . 'order', array('id' => $id));
					$coupon = pdo_get('wlmerchant_couponlist', array('id' => $v['fkid']), array('title', 'logo'));
					$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $v['sid']), array('storename'));
					$member = pdo_get('wlmerchant_member', array('id' => $v['mid']), array('nickname', 'avatar', 'mobile'));
					$v['title'] = $coupon['title'];
					$v['gimg'] = tomedia($coupon['logo']);
					$v['storename'] = $merchant['storename'];
					$v['nickname'] = $member['nickname'];
					$v['headimg'] = $member['avatar'];
					$v['mobile'] = $member['mobile'];
					$v['actualprice'] = $v['price'];
					$v['gname'] = $v['title'];
					$list[] = $v;
				}
				else {
					$list[] = Rush::getSingleOrder($id, '*');
				}
			}
		}

		if ($settlementRecord['type'] == 2) {
			foreach ($orders as $id) {
				$order = Util::getSingelData('*', PDO_NAME . 'vip_record', array('id' => $id));
				$member = Member::getMemberByMid($order['mid']);
				$order['nickname'] = $member['nickname'];
				$order['actualprice'] = $order['price'];
				$order['mobile'] = $member['mobile'];
				$order['gname'] = 'VIP充值';
				$list[] = $order;
			}
		}

		if ($settlementRecord['type'] == 3) {
			foreach ($orders as $id) {
				$order = Util::getSingelData('*', PDO_NAME . 'halfcard_record', array('id' => $id));
				$member = Member::getMemberByMid($order['mid']);
				$order['nickname'] = $member['nickname'];
				$order['actualprice'] = $order['price'];
				$order['mobile'] = $member['mobile'];
				$order['gname'] = '一卡通充值';
				$list[] = $order;
			}
		}

		$orders = $list;

		if ($settlementRecord['status'] == 1) {
			$settleStatus = '代理审核中';
		}

		if ($settlementRecord['status'] == 2) {
			$settleStatus = '系统审核中';
		}

		if ($settlementRecord['status'] == 3) {
			$settleStatus = '系统审核通过，待结算';
		}

		if ($settlementRecord['status'] == 4) {
			$settleStatus = '已结算到代理';
		}

		if ($settlementRecord['status'] == 5) {
			$settleStatus = '已结算到商家';
		}

		if ($settlementRecord['status'] == -1) {
			$settleStatus = '系统审核不通过';
		}

		if ($settlementRecord['status'] == -2) {
			$settleStatus = '代理审核不通过';
		}

		$html = "\xef\xbb\xbf";
		$filter = array('aa' => '商户单号', 'bb' => '昵称', 'cc' => '电话', 'dd' => '支付金额', 'ee' => '订单状态', 'jj' => '结算状态', 'ff' => '支付时间', 'gg' => '商品名称', 'hh' => '微信订单号');

		foreach ($filter as $key => $title) {
			$html .= $title . '	,';
		}

		$html .= '
';

		foreach ($orders as $k => $v) {
			if ($v['status'] == '0') {
				$thisstatus = '未支付';
			}

			if ($v['status'] == '1') {
				$thisstatus = '已支付';
			}

			if ($v['status'] == '2') {
				$thisstatus = '已消费';
			}

			$time = date('Y-m-d H:i:s', $v['paytime']);
			$orders[$k]['aa'] = $v['orderno'];
			$orders[$k]['bb'] = $v['nickname'];
			$orders[$k]['cc'] = $v['mobile'];
			$orders[$k]['dd'] = $v['actualprice'];
			$orders[$k]['ee'] = $thisstatus;
			$orders[$k]['jj'] = $settleStatus;
			$orders[$k]['ff'] = $time;
			$orders[$k]['gg'] = $v['gname'];
			$orders[$k]['hh'] = $v['transid'];

			foreach ($filter as $key => $title) {
				$html .= $orders[$k][$key] . '	,';
			}

			$html .= '
';
		}

		header('Content-type:text/csv');
		header('Content-Disposition:attachment; filename=未结算订单.csv');
		echo $html;
		exit();
	}
}

defined('IN_IA') || exit('Access Denied');

?>
