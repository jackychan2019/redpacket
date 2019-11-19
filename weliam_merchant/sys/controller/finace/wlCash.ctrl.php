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

		if ($time_limit) {
			$starttime = strtotime($_GPC['time_limit']['start']);
			$endtime = strtotime($_GPC['time_limit']['end']);
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		$data = Merchant::sysCashSurvey(1, $timetype, $starttime, $endtime);
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

		if (checksubmit('submit')) {
			$set = $_GPC['cashset'];
			$res1 = Setting::wlsetting_save($set, 'cashset');

			if ($res1) {
				wl_message('保存设置成功！', referer(), 'success');
			}
			else {
				wl_message('保存设置失败！', referer(), 'error');
			}
		}

		$cashset = Setting::wlsetting_read('cashset');
		include wl_template('finace/cashset');
	}

	public function cashApply()
	{
		global $_W;
		global $_GPC;
		if ($_GPC['type'] == 'submit' && !empty($_GPC['id'])) {
			$trade_no = time() . random(4, true);
			pdo_update(PDO_NAME . 'settlement_record', array('status' => 3, 'updatetime' => TIMESTAMP, 'trade_no' => $trade_no), array('id' => $_GPC['id']));
			$record = pdo_get(PDO_NAME . 'settlement_record', array('id' => $_GPC['id']), array('type', 'sopenid', 'sid'));
			$openid = $record['sopenid'];
			$first = '您的提现申请已通过审核';
			$keyword1 = '提现申请';
			$keyword2 = '已通过审核';

			if ($record['sid']) {
				$remark = '系统管理员会尽快打款,点击查看申请记录';
				$url = app_url('store/supervise/cash', array('type' => 'deling', 'storeid' => $record['sid']));
			}
			else if ($record['type'] == 3) {
				$remark = '系统管理员会尽快打款,点击查看申请记录';
				$url = app_url('distribution/disappbase/apply', array('type' => 'deling'));
			}
			else {
				$remark = '系统管理员会尽快打款';
				$url = '';
			}

			Message::jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url);
			show_json(1, '提交成功');
		}
		else {
			if ($_GPC['type'] == 'reject' && !empty($_GPC['id'])) {
				$record = pdo_get(PDO_NAME . 'settlement_record', array('id' => $_GPC['id']), array('type', 'sid', 'aid', 'sapplymoney', 'id', 'mid'));

				if ($record['type'] == 1) {
					$res = Store::settlement($record['id'], 0, $record['sid'], $record['sapplymoney'], 0, $record['sapplymoney'], 7, 0, 0, $record['aid']);
					$status = -1;
				}
				else if ($record['type'] == 2) {
					$res = Store::settlement($record['id'], 0, 0, $record['sapplymoney'], 0, 0, 7, 0, 0, $record['aid']);
					$status = -1;
				}
				else if ($record['type'] == 3) {
					$nowmoney = pdo_getcolumn(PDO_NAME . 'distributor', array('mid' => $record['mid']), 'nowmoney');
					$totalNowMonet = $nowmoney + $record['sapplymoney'];
					$res = pdo_update(PDO_NAME . 'distributor', array('nowmoney' => $totalNowMonet), array('mid' => $record['mid']));
					Distribution::adddisdetail($record['id'], $record['mid'], $record['mid'], 1, $record['sapplymoney'], 'cash', 1, '分销佣金驳回', $totalNowMonet);
					$status = 11;
				}
				else {
					if ($record['type'] == 4) {
						$res = Member::credit_update_credit2($record['mid'], $record['sapplymoney'], '用户提现被驳回', $record['id']);
						$status = -1;
					}
				}

				pdo_update(PDO_NAME . 'settlement_record', array('status' => $status, 'updatetime' => TIMESTAMP), array('id' => $_GPC['id']));
				$record = pdo_get(PDO_NAME . 'settlement_record', array('id' => $_GPC['id']), array('sopenid', 'sid'));
				$openid = $record['sopenid'];
				$first = '您的提现申请已被驳回';
				$keyword1 = '提现申请';
				$keyword2 = '申请被驳回';

				if ($record['sid']) {
					$remark = '您可以重新提交申请,点击查看申请记录';
					$url = app_url('store/supervise/cash', array('type' => 'reject'));
				}
				else {
					$remark = '您可以在后台重新提交申请';
					$url = app_url('dashboard/home/index');
				}

				Message::jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url);
				show_json(1, '驳回成功');
			}
			else {
				$pindex = max(1, intval($_GPC['page']));
				$psize = 10;
				$where = array('uniacid' => $_W['uniacid']);
				if ($_GPC['export'] && $_GPC['startTime'] && $_GPC['endTime']) {
					$where['applytime >'] = $_GPC['startTime'] . ' AND applytime < ' . $_GPC['endTime'];
				}

				if (!empty($_GPC['status'])) {
					switch ($_GPC['status']) {
					case 1:
						break;

					case 2:
						$where['status'] = array(2, 6, 7);
						break;

					case 3:
						$where['status'] = array(3, 8);
						break;

					case 4:
						$where['status'] = array(4, 5, 9);
						break;

					case 5:
						$where['status'] = array(-1, 10, 11);
						break;
					}
				}

				if (!empty($_GPC['type'])) {
					$where['type'] = intval($_GPC['type']);
				}

				if (!empty($_GPC['orderid'])) {
					$where['id'] = intval($_GPC['orderid']);
				}

				if (!empty($_GPC['time'])) {
					$starttime = strtotime($_GPC['time']['start']);
					$endtime = strtotime($_GPC['time']['end']);
					$where['applytime >'] = $starttime;
					$where['applytime <'] = $endtime;
				}

				if (empty($starttime) || empty($endtime)) {
					$starttime = strtotime('-1 month');
					$endtime = time();
				}

				if ($_GPC['export']) {
					$list = pdo_getslice(PDO_NAME . 'settlement_record', $where, '', $total, array(), '', 'id DESC');
				}
				else {
					$list = pdo_getslice(PDO_NAME . 'settlement_record', $where, array($pindex, $psize), $total, array(), '', 'id DESC');
				}

				foreach ($list as $key => &$relue) {
					$relue['spercent'] = sprintf('%.2f', $relue['spercent']);

					if ($relue['type'] == 1) {
						$accountInfo = Store::getShopOwnerInfo($relue['sid'], $relue['aid']);
						$relue['name'] = Util::idSwitch('sid', 'sName', $relue['sid']);
						$relue['currurl'] = web_url('finace/newCash/currentlist', array('type' => 'store', 'objid' => $relue['sid']));
					}
					else if ($relue['type'] == 2) {
						$accountInfo = pdo_get(PDO_NAME . 'agentusers', array('id' => $relue['aid'], 'uniacid' => $_W['uniacid']), array('alipay', 'bank_name', 'card_number', 'bank_username'));
						$relue['name'] = Util::idSwitch('aid', 'aName', $relue['aid']);
						$relue['currurl'] = web_url('finace/newCash/currentlist', array('type' => 'agent', 'objid' => $relue['aid']));
					}
					else if ($relue['type'] == 3) {
						$accountInfo = pdo_get(PDO_NAME . 'member', array('id' => $relue['mid'], 'uniacid' => $_W['uniacid']), array('alipay', 'bank_name', 'card_number', 'nickname', 'bank_username'));
						$relue['name'] = $accountInfo['nickname'];
						$relue['currurl'] = web_url('distribution/dissysbase/disdetail', array('keywordtype' => '1', 'keyword' => $relue['mid']));
					}
					else {
						if ($relue['type'] == 4) {
							$accountInfo = pdo_get(PDO_NAME . 'member', array('id' => $relue['mid'], 'uniacid' => $_W['uniacid']), array('alipay', 'bank_name', 'card_number', 'nickname', 'bank_username'));
							$relue['name'] = $accountInfo['nickname'];
							$relue['currurl'] = web_url('member/wlMember/balance', array('keywordtype' => '1', 'keyword' => $relue['mid']));
						}
					}

					if ($relue['payment_type'] == 1 || $relue['payment_type'] == 3 || $relue['payment_type'] == 5) {
						if ($accountInfo) {
							$relue['alipay'] = $accountInfo['alipay'];
							$relue['bank_name'] = $accountInfo['bank_name'];
							$relue['card_number'] = $accountInfo['card_number'];
							$relue['bank_username'] = $accountInfo['bank_username'];
						}
					}

					if ($_GPC['export']) {
						$data[$key]['name'] = $relue['name'];
						$data[$key]['sapplymoney'] = $relue['sapplymoney'];
						$data[$key]['spercentmoney'] = $relue['spercentmoney'];
						$data[$key]['sgetmoney'] = $relue['sgetmoney'];
						$data[$key]['applytime'] = date('Y-m-d H:i:s', $relue['applytime']);
						$data[$key]['updatetime'] = $relue['updatetime'] ? date('Y-m-d H:i:s', $relue['updatetime']) : '';

						switch ($relue['status']) {
						case 1:
							$statueRes = '审核中';
							break;

						case 2:
						case 6:
						case 7:
							$statueRes = '待审核';
							break;

						case 3:
						case 8:
							$statueRes = '待打款';
							break;

						case 4:
						case 5:
						case 9:
							$statueRes = '提现成功';
							break;

						case -1:
						case 10:
						case 11:
							$statueRes = '驳回申请';
							break;
						}

						$data[$key]['status'] = $statueRes;

						switch ($relue['type']) {
						case 1:
							$typeRes = '商家提现';
							break;

						case 2:
							$typeRes = '代理提现';
							break;

						case 3:
							$typeRes = '分销提现';
							break;
						}

						$data[$key]['type'] = $typeRes;

						switch ($relue['payment_type']) {
						case 1:
							$paymentTypeRes = '支付宝';
							break;

						case 2:
							$paymentTypeRes = '微信';
							break;

						case 3:
							$paymentTypeRes = '银行卡';
							break;

						case 4:
							$paymentTypeRes = '余额';
							break;

						case 5:
							$paymentTypeRes = '任意';
							break;

						default:
							$paymentTypeRes = '微信';
							break;
						}

						$data[$key]['payment_type'] = $paymentTypeRes;

						switch ($relue['settletype']) {
						case 1:
						case 3:
							$settleTypeRes = '手动处理';
							break;

						case 2:
							$settleTypeRes = '微信零钱';
							break;

						case 4:
							$settleTypeRes = '余额到账';
							break;

						default:
							$settleTypeRes = '未打款';
							break;
						}

						$data[$key]['settletype'] = $settleTypeRes;
					}
				}

				if ($_GPC['export']) {
					$titleInfo = array('name' => '提现人信息', 'sapplymoney' => '申请提现金额', 'spercentmoney' => '手续费', 'sgetmoney' => '实际到账金额', 'applytime' => '申请时间', 'updatetime' => '操作时间', 'status' => '打款状态', 'type' => '提现类型', 'payment_type' => '提现方式', 'settletype' => '到账类型');
					util_csv::export_csv_2($data, $titleInfo, '提现申请信息.csv');
					exit();
				}

				$pager = pagination($total, $pindex, $psize);
			}
		}

		include wl_template('finace/cashConfirm');
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

			if ($settlementRecord['type'] == 4) {
				$list[] = Util::getSingelData('*', PDO_NAME . 'order', array('id' => $id));
			}
		}

		if ($settlementRecord['type'] == 2) {
			foreach ($list as $key => &$relue) {
				$relue['areaName'] = Util::idSwitch('areaid', 'areaName', $relue['areaid']);
				$relue['member'] = Member::getMemberByMid($relue['mid']);
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

		if ($settlementRecord['type'] == 4) {
			foreach ($list as $key => &$pock) {
				$user = pdo_get('wlmerchant_member', array('id' => $pock['mid']));
				$infor = pdo_get('wlmerchant_pocket_informations', array('id' => $pock['fkid']));
				$typename = pdo_getcolumn('wlmerchant_pocket_type', array('id' => $infor['type']), 'title');
				$pock['nickname'] = $infor['nickname'];
				$pock['avatar'] = $infor['avatar'];
				$pock['mobile'] = $infor['phone'];
				$pock['typename'] = $typename;

				if (empty($infor['avatar'])) {
					$pock['avatar'] = $user['avatar'];
				}
			}
		}

		include wl_template('finace/cashDetail');
	}

	public function settlementing()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$where = array();
		$where['id'] = $id;
		$type = $_GPC['type'];
		$settlementRecord = Util::getSingelData('*', PDO_NAME . 'settlement_record', $where);
		if ($settlementRecord['status'] != 3 && $settlementRecord['status'] != 8) {
			wl_message('该申请未审核或已打款', referer(), 'error');
		}

		$sgetmoney = sprintf('%.2f', $settlementRecord['sgetmoney']);
		$spercent = sprintf('%.2f', $settlementRecord['spercent']);
		$spercentmoney = sprintf('%.2f', $settlementRecord['spercentmoney']);

		if (!is_numeric($sgetmoney)) {
			show_json(0, '结算金额错误');
		}

		if ($type == 'wechat') {
			if (empty($settlementRecord['sopenid'])) {
				$settlementRecord['sopenid'] = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $settlementRecord['aid']), 'cashopenid');

				if (empty($settlementRecord['sopenid'])) {
					show_json(0, '该用户未绑定提现微信号');
				}
			}

			if ($sgetmoney < 1) {
				show_json(0, '到账金额需要大于1元');
			}

			if ($settlementRecord['type'] == 1) {
				$rem = '结算给商家';
			}
			else if ($settlementRecord['type'] == 2) {
				$rem = '结算给代理';
			}
			else if ($settlementRecord['type'] == 3) {
				$rem = '结算给分销商';
			}
			else {
				if ($settlementRecord['type'] == 4) {
					$rem = '用户余额提现';
				}
			}

			$realname = pdo_getcolumn(PDO_NAME . 'member', array('openid' => $settlementRecord['sopenid']), 'realname');
			$result1 = wlPay::finance($settlementRecord['sopenid'], $sgetmoney, $rem, $realname, $settlementRecord['trade_no']);
			if ($result1['return_code'] == 'SUCCESS' && $result1['result_code'] == 'SUCCESS') {
				if ($settlementRecord['type'] == 1) {
					$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 5, 'updatetime' => TIMESTAMP, 'sgetmoney' => $sgetmoney, 'spercent' => $spercent, 'spercentmoney' => $spercentmoney, 'settletype' => 2), array('id' => $_GPC['id']));

					if ($res) {
						show_json(1, '已结算给商户');
					}
					else {
						show_json(0, '结算失败,请重试');
					}
				}

				if ($settlementRecord['type'] == 2) {
					$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 4, 'updatetime' => TIMESTAMP, 'sgetmoney' => $sgetmoney, 'spercent' => $spercent, 'spercentmoney' => $spercentmoney, 'settletype' => 2), array('id' => $_GPC['id']));

					if ($res) {
						show_json(1, '已结算给代理');
					}
					else {
						show_json(0, '结算失败,请重试');
					}
				}

				if ($settlementRecord['type'] == 3) {
					$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 9, 'updatetime' => TIMESTAMP, 'sgetmoney' => $sgetmoney, 'spercent' => $spercent, 'spercentmoney' => $spercentmoney, 'settletype' => 2), array('id' => $_GPC['id']));

					if ($res) {
						$url = app_url('distribution/disappbase/apply', array('type' => 'finish'));
						Distribution::distriNotice($settlementRecord['sopenid'], $url, 6, 0, $settlementRecord['sapplymoney'], '微信打款');
						show_json(1, '已结算给分销商');
					}
					else {
						show_json(0, '结算失败,请重试');
					}
				}

				if ($settlementRecord['type'] == 4) {
					$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 5, 'updatetime' => TIMESTAMP, 'sgetmoney' => $sgetmoney, 'spercent' => $spercent, 'spercentmoney' => $spercentmoney, 'settletype' => 2), array('id' => $_GPC['id']));

					if ($res) {
						show_json(1, '已打款给用户');
					}
					else {
						show_json(0, '打款失败,请重试');
					}
				}
			}
			else {
				if (empty($result1['err_code_des'])) {
					$result1['err_code_des'] = $result1['message'];
				}

				show_json(0, '微信钱包提现失败: ' . $result1['err_code_des']);
			}
		}
		else if ($type == 'f2f') {
			if ($settlementRecord['type'] == 1) {
				$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 5, 'updatetime' => TIMESTAMP, 'sgetmoney' => $sgetmoney, 'spercent' => $spercent, 'spercentmoney' => $spercentmoney, 'settletype' => 1), array('id' => $_GPC['id']));

				if ($res) {
					show_json(1, '已结算给商户');
				}
				else {
					show_json(0, '结算失败,请重试');
				}
			}

			if ($settlementRecord['type'] == 2) {
				$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 4, 'updatetime' => TIMESTAMP, 'sgetmoney' => $sgetmoney, 'spercent' => $spercent, 'spercentmoney' => $spercentmoney, 'settletype' => 1), array('id' => $_GPC['id']));

				if ($res) {
					show_json(1, '已结算给代理');
				}
				else {
					show_json(0, '结算失败,请重试');
				}
			}

			if ($settlementRecord['type'] == 3) {
				$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 9, 'updatetime' => TIMESTAMP, 'sgetmoney' => $sgetmoney, 'spercent' => $spercent, 'spercentmoney' => $spercentmoney, 'settletype' => 1), array('id' => $_GPC['id']));

				if ($res) {
					$url = app_url('distribution/disappbase/apply', array('type' => 'finish'));
					Distribution::distriNotice($settlementRecord['sopenid'], $url, 6, 0, $settlementRecord['sapplymoney'], '线下转账');
					show_json(1, '已结算给分销商');
				}
				else {
					show_json(0, '结算失败,请重试');
				}
			}

			if ($settlementRecord['type'] == 4) {
				$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 5, 'updatetime' => TIMESTAMP, 'sgetmoney' => $sgetmoney, 'spercent' => $spercent, 'spercentmoney' => $spercentmoney, 'settletype' => 1), array('id' => $_GPC['id']));

				if ($res) {
					show_json(1, '已打款给用户');
				}
				else {
					show_json(0, '打款失败,请重试');
				}
			}
		}
		else if ($type == 'redEnvelopes') {
			$redPackets['mch_billno'] = $settlementRecord['trade_no'];
			$redPackets['re_openid'] = $settlementRecord['sopenid'];
			$redPackets['total_amount'] = $settlementRecord['sgetmoney'] * 100;
			$redPackets['remark'] = time();
			$result = WeixinPay::sendingRedPackets($redPackets);

			if (empty($result['return_msg'])) {
				$result = WeixinPay::getRedPacketsInfo($redPackets);
			}

			if ($result['send_listid']) {
				if ($settlementRecord['type'] == 1) {
					$status = 5;
				}
				else if ($settlementRecord['type'] == 2) {
					$status = 4;
				}
				else if ($settlementRecord['type'] == 3) {
					$status = 9;
				}
				else {
					if ($settlementRecord['type'] == 4) {
						$status = 5;
					}
				}

				$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => $status, 'updatetime' => TIMESTAMP, 'sgetmoney' => $sgetmoney, 'spercent' => $spercent, 'spercentmoney' => $spercentmoney, 'settletype' => 2), array('id' => $_GPC['id']));

				if ($settlementRecord['type'] == 3) {
					$url = app_url('distribution/disappbase/apply', array('type' => 'finish'));
					Distribution::distriNotice($settlementRecord['sopenid'], $url, 6, 0, $settlementRecord['sapplymoney'], '微信红包');
				}

				show_json(1, '红包发送成功');
			}
			else if ($result['reason']) {
				show_json(0, $result['reason']);
			}
			else {
				show_json(0, $result['return_msg']);
			}
		}
		else {
			if ($type == 'balance') {
				$result = Member::credit_update_credit2($settlementRecord['mid'], $settlementRecord['sgetmoney']);

				if ($result) {
					$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 9, 'updatetime' => TIMESTAMP, 'sgetmoney' => $sgetmoney, 'spercent' => $spercent, 'spercentmoney' => $spercentmoney, 'settletype' => 4), array('id' => $_GPC['id']));

					if ($res) {
						if ($settlementRecord['type'] == 3) {
							$url = app_url('distribution/disappbase/apply', array('type' => 'finish'));
							Distribution::distriNotice($settlementRecord['sopenid'], $url, 6, 0, $settlementRecord['sapplymoney'], '账户余额');
						}

						show_json(1, '已结算给分销商');
					}
					else {
						show_json(0, '结算失败,请重试');
					}
				}
				else {
					show_json(0, '结算失败,请重试');
				}
			}
		}
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

		$str = '未结算订单_' . time();
		header('Content-type:text/csv');
		header('Content-Disposition:attachment; filename=' . $str . '.csv');
		echo $html;
		exit();
	}
}

defined('IN_IA') || exit('Access Denied');

?>
