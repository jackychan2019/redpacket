<?php
//dezend by http://www.sucaihuo.com/
class Userorder_WeliamController
{
	public function orderlist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '我的订单 - ' . $_W['wlsetting']['base']['name'] : '我的订单';
		$status = $_GPC['status'];

		if ($_W['ispost']) {
			$where = ' uniacid = ' . $_W['uniacid'] . ' and mid = ' . $_W['mid'];

			if ($status != 'all') {
				if ($status == 1) {
					$where .= ' and status in (1,4,8)';
				}
				else {
					$where .= ' and status = {intval(' . $status . ')}';
				}
			}

			$myorder = pdo_fetchall('SELECT id,createtime,sid,status,price,paidprid,applyrefund,expressid, "a" FROM ' . tablename(PDO_NAME . 'order') . (' where ' . $where . ' and orderno != 666666') . ' UNION ALL SELECT id,createtime,sid,status,price,paidprid,applyrefund,expressid, "b" FROM ' . tablename(PDO_NAME . 'rush_order') . (' where ' . $where . ' ORDER BY createtime DESC'));
			$settings = Setting::wlsetting_read('orderset');

			if ($settings['appre']) {
				$plugin = unserialize($settings['plugin']);

				if (empty($plugin)) {
					$rerush = $regroupon = $refight = $recoupon = $rebargain = 1;
				}
				else {
					if (in_array('rush', $plugin)) {
						$rerush = 1;
					}

					if (in_array('groupon', $plugin)) {
						$regroupon = 1;
					}

					if (in_array('fightgroup', $plugin)) {
						$refight = 1;
					}

					if (in_array('coupon', $plugin)) {
						$recoupon = 1;
					}

					if (in_array('bargain', $plugin)) {
						$rebargain = 1;
					}
				}
			}

			foreach ($myorder as $k => &$v) {
				if ($v['a'] == 'a') {
					$ndorder = pdo_get(PDO_NAME . 'order', array('id' => $v['id'], 'uniacid' => $_W['uniacid']), array('id', 'payfor', 'plugin', 'fkid', 'orderno', 'fightstatus', 'fightgroupid', 'recordid'));

					if ($ndorder['plugin'] == 'coupon') {
						$goods = wlCoupon::getSingleCoupons($ndorder['fkid'], 'title,logo,id,allowapplyre');
						$v['goodsname'] = $goods['title'];
						$v['goodsimg'] = tomedia($goods['logo']);
						$coupon = pdo_get(PDO_NAME . 'member_coupons', array('uniacid' => $_W['uniacid'], 'orderno' => $ndorder['orderno']), array('status', 'id', 'usetimes', 'endtime', 'usedtime'));
						$this->checkcoupon($coupon, $ndorder);
						$v['xiaofei'] = app_url('wlcoupon/coupon_app/couponDetail', array('id' => $coupon['id']));
						$v['url'] = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $ndorder['fkid']));
						$v['zhifu'] = app_url('wlcoupon/coupon_app/createCouponorder', array('orderId' => $ndorder['id']));
						$v['paidurl'] = app_url('order/userorder/payover', array('id' => $v['id'], 'type' => 3));
						if ($recoupon && empty($goods['allowapplyre']) && $coupon['status'] == 1) {
							if (empty($coupon['usedtime'])) {
								$v['surerefund'] = 1;
							}
						}
					}
					else if ($ndorder['plugin'] == 'wlfightgroup') {
						$goods = Wlfightgroup::getSingleGood($ndorder['fkid'], 'name,logo,id,allowapplyre');
						$v['goodsname'] = $goods['name'];
						$v['goodsimg'] = tomedia($goods['logo']);
						$v['fightstatus'] = $ndorder['fightstatus'];
						$v['recordid'] = $ndorder['recordid'];

						if ($v['expressid']) {
							$v['receive'] = app_url('order/userorder/orderdetail', array('orderid' => $v['id'], 'type' => 'wlfightgroup'));
						}
						else {
							$v['xiaofei'] = app_url('wlfightgroup/fightapp/expressorder', array('orderid' => $v['id']));
						}

						if ($v['fightstatus'] == 1) {
							$v['buystatus'] = '拼团';
						}
						else {
							$v['buystatus'] = '单购';
						}

						switch ($v['status']) {
						case '1':
							if ($ndorder['fightgroupid']) {
								$groupstatus = pdo_getcolumn(PDO_NAME . 'fightgroup_group', array('id' => $ndorder['fightgroupid']), 'status');

								if ($groupstatus == 1) {
									$v['statusName'] = '组团中';
								}
								else {
									if ($groupstatus == 2) {
										$v['statusName'] = '待消费';
									}
								}
							}
							else {
								$v['statusName'] = '待消费';
							}

							if ($refight && empty($goods['allowapplyre'])) {
								if ($ndorder['recordid']) {
									$usedtime = pdo_getcolumn(PDO_NAME . 'fightgroup_userecord', array('id' => $ndorder['recordid']), 'usedtime');

									if (empty($usedtime)) {
										$v['surerefund'] = 1;
									}
								}
								else {
									$v['surerefund'] = 1;
								}
							}

							break;

						case '2':
							$v['statusName'] = '已消费';
							break;

						case '3':
							$v['statusName'] = '已完成';
							break;

						case '8':
							$v['statusName'] = '待发货';
							if ($refight && empty($goods['allowapplyre'])) {
								$v['surerefund'] = 1;
							}

							break;

						case '4':
							$v['statusName'] = '待收货';
							break;

						case '5':
							$v['statusName'] = '已取消';
							break;

						case '6':
							$v['statusName'] = '待退款';
							break;

						case '7':
							$v['statusName'] = '已退款';
							break;

						case '9':
							$v['statusName'] = '已过期';
							break;

						default:
							$v['statusName'] = '待付款';
							break;
						}

						$v['url'] = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $ndorder['fkid']));
						$v['zhifu'] = app_url('wlfightgroup/fightapp/createFightorder', array('orderid' => $ndorder['id']));
						$v['paidurl'] = app_url('order/userorder/payover', array('id' => $v['id'], 'type' => 2));
					}
					else if ($ndorder['plugin'] == 'groupon') {
						$goods = pdo_get('wlmerchant_groupon_activity', array('id' => $ndorder['fkid']), array('name', 'thumb', 'id', 'allowapplyre'));
						$v['goodsname'] = $goods['name'];
						$v['goodsimg'] = tomedia($goods['thumb']);
						$v['xiaofei'] = $v['receive'] = app_url('order/userorder/orderdetail', array('orderid' => $v['id'], 'type' => 'groupon'));
						$v['comment'] = app_url('order/comment/add', array('orderid' => $v['id'], 'plugin' => 'groupon'));
						$v['url'] = app_url('groupon/grouponapp/groupondetail', array('cid' => $goods['id']));
						$v['zhifu'] = app_url('groupon/grouponapp/topay', array('orderid' => $v['id'], 'id' => $ndorder['fkid']));
						$v['paidurl'] = app_url('order/userorder/payover', array('id' => $v['id'], 'type' => 4));
						if ($regroupon && empty($goods['allowapplyre'])) {
							if ($ndorder['recordid'] && $v['status'] == 1) {
								$usedtime = pdo_getcolumn(PDO_NAME . 'groupon_userecord', array('id' => $ndorder['recordid']), 'usedtime');

								if (empty($usedtime)) {
									$v['surerefund'] = 1;
								}
							}
							else {
								if ($v['expressid'] && $v['status'] == 8) {
									$v['surerefund'] = 1;
								}
							}
						}
					}
					else if ($ndorder['plugin'] == 'pocket') {
						$infrom = Pocket::getInformations($ndorder['fkid']);
						$typename = pdo_getcolumn(PDO_NAME . 'pocket_type', array('uniacid' => $_W['uniacid'], 'id' => $infrom['type']), 'title');
						$typeimg = pdo_getcolumn(PDO_NAME . 'pocket_type', array('uniacid' => $_W['uniacid'], 'id' => $infrom['type']), 'img');

						if ($ndorder['payfor'] == 'pocketfabusharge') {
							$v['goodsname'] = '发布' . $typename . '信息';
						}
						else if ($ndorder['payfor'] == 'pocketsharge') {
							$v['goodsname'] = '置顶' . $typename . '信息';
						}
						else {
							$v['goodsname'] = '支付' . $typename . '信息红包';
						}

						$v['goodsimg'] = tomedia($typeimg);
						$v['url'] = app_url('pocket/pocket/detail', array('id' => $ndorder['fkid']));
						$v['zhifu'] = app_url('pocket/pocket/createFabuOrder', array('orderId' => $ndorder['id']));
					}
					else if ($ndorder['plugin'] == 'store') {
						$chargetype = pdo_get('wlmerchant_chargelist', array('id' => $ndorder['fkid']), array('name'));
						$v['goodsname'] = '商户入驻' . $chargetype['name'];
						$typeimg = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $v['sid']), 'logo');
						$v['goodsimg'] = tomedia($typeimg);
						$v['url'] = app_url('store/merchant/detail', array('id' => $v['sid']));
						$v['zhifu'] = app_url('store/supervise/chargeorder', array('orderId' => $ndorder['id']));
						$v['paidurl'] = app_url('order/userorder/payover', array('id' => $v['id'], 'type' => 6));
					}
					else if ($ndorder['plugin'] == 'activity') {
						$activity = pdo_get('wlmerchant_activitylist', array('id' => $ndorder['fkid']), array('title', 'thumb'));
						$v['goodsname'] = $activity['title'];
						$typeimg = $activity['thumb'];
						$v['goodsimg'] = tomedia($typeimg);
						$v['url'] = app_url('activity/activity_app/activitydetail', array('id' => $v['fkid']));
						$v['zhifu'] = app_url('activity/activity_app/topaying', array('orderid' => $ndorder['id']));
						$v['xiaofei'] = app_url('activity/activity_app/orderdetail', array('id' => $ndorder['id']));
					}
					else {
						if ($ndorder['plugin'] == 'distribution' || $ndorder['plugin'] == 'consumption') {
							unset($myorder[$k]);
						}
						else if ($ndorder['plugin'] == 'member') {
							$v['goodsname'] = '余额充值';
							$v['goodsimg'] = tomedia($_W['wlmember']['avatar']);
							$v['url'] = app_url('member/user/recharge');
							$v['zhifu'] = app_url('member/user/topay', array('orderid' => $ndorder['id']));
							$v['storename'] = '系统平台';
						}
						else if ($ndorder['plugin'] == 'halfcard') {
							$v['goodsname'] = '在线买单';
							$v['goodsimg'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $v['sid'], 'uniacid' => $_W['uniacid']), 'logo');
							$v['goodsimg'] = tomedia($v['goodsimg']);
							$v['url'] = app_url('order/paybill/paycheck', array('id' => $v['sid']));
							$v['zhifu'] = app_url('order/paybill/topay', array('orderid' => $v['id']));
							$v['paidurl'] = app_url('order/userorder/payover', array('id' => $v['id'], 'type' => 7));
						}
						else {
							if ($ndorder['plugin'] == 'bargain') {
								$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $ndorder['fkid']), array('name', 'thumb', 'allowapplyre'));
								$v['goodsname'] = $goods['name'];
								$v['goodsimg'] = tomedia($goods['thumb']);
								$v['fightstatus'] = $ndorder['fightstatus'];

								if ($v['fightstatus']) {
									$v['receive'] = app_url('order/userorder/orderdetail', array('orderid' => $v['id'], 'type' => 'bargain'));
								}
								else {
									$v['xiaofei'] = app_url('order/userorder/orderdetail', array('orderid' => $v['id'], 'type' => 'bargain'));
								}

								$v['url'] = app_url('bargain/bargain_app/bargaindetail', array('cid' => $ndorder['fkid']));
								$v['zhifu'] = app_url('bargain/bargain_app/topay', array('orderid' => $v['id'], 'id' => $ndorder['fkid']));
								if ($v['status'] == 1 || $v['status'] == 8) {
									if ($rebargain && empty($goods['allowapplyre'])) {
										$v['surerefund'] = 1;
									}

									if ($v['expressid']) {
										$v['statusName'] = '待发货';
									}
								}
							}
						}
					}

					$v['comment'] = app_url('order/comment/add', array('orderid' => $v['id']));
					$v['plugin'] = $ndorder['plugin'];
				}

				if ($v['a'] == 'b') {
					$v['activityid'] = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $v['id'], 'uniacid' => $_W['uniacid']), 'activityid');
					$goods = Rush::getSingleActive($v['activityid'], 'name,isdistri,thumb,id,cutofftime,viponedismoney,viptwodismoney,vipthreedismoney,dissettime,onedismoney,twodismoney,threedismoney,allowapplyre');
					if ($v['status'] == 0 || $v['status'] == 5) {
						$orderno = pdo_getcolumn(PDO_NAME . 'rush_order', array('uniacid' => $_W['uniacid'], 'id' => $v['id']), 'orderno');
						$paylog = pdo_get('core_paylog', array('uniacid' => $_W['uniacid'], 'tid' => $orderno), array('status', 'uniontid', 'type'));

						if ($paylog['status'] == 1) {
							$paytype = array('credit' => 1, 'wechat' => 2, 'alipay' => 3, 'delivery' => 4, 'wxapp' => 5);
							$data['paytype'] = $paytype[$paylog['type']];

							if ($v['expressid']) {
								$data['status'] = 8;
							}
							else {
								$data['status'] = 1;
							}

							$data['transid'] = $paylog['uniontid'];
							pdo_update(PDO_NAME . 'rush_order', $data, array('id' => $v['id']));
							$v = pdo_get('wlmerchant_rush_order', array('id' => $v['id']));
						}
					}

					$actualprice = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $v['id'], 'uniacid' => $_W['uniacid']), 'actualprice');
					$v['goodsname'] = $goods['name'];
					$v['goodsimg'] = tomedia($goods['thumb']);
					$v['xiaofei'] = $v['receive'] = app_url('order/userorder/orderdetail', array('orderid' => $v['id'], 'type' => 'rush'));
					$v['comment'] = app_url('order/comment/add', array('orderid' => $v['id'], 'plugin' => 'rush'));
					$v['url'] = app_url('rush/home/detail', array('id' => $goods['id']));
					$v['zhifu'] = app_url('rush/rushpay/topay', array('orderid' => $v['id']));
					$v['paidurl'] = app_url('order/userorder/payover', array('id' => $v['id'], 'type' => 1));
					$v['plugin'] = 'rush';
					$v['price'] = $actualprice;
					if ($rerush && empty($goods['allowapplyre'])) {
						if ($v['status'] == 8 && $v['expressid']) {
							$v['surerefund'] = 1;
						}

						if ($v['status'] == 1) {
							$usedtime = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $v['id']), 'usedtime');

							if (empty($usedtime)) {
								$v['surerefund'] = 1;
							}
						}
					}
				}

				if (!$v['storename']) {
					$v['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $v['sid'], 'uniacid' => $_W['uniacid']), 'storename');

					if (!$v['storename']) {
						$v['storename'] = '掌上信息';
					}
				}

				$v['createtime'] = date('Y-m-d H:i', $v['createtime']);
			}

			$myorder = array_values($myorder);
			exit(json_encode(array('errno' => 0, 'data' => $myorder)));
		}

		include wl_template('order/orderlist');
	}

	public function orderdetail()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '订单详情 - ' . $_W['wlsetting']['base']['name'] : '订单详情';
		$backurl = urlencode($_W['siteurl']);
		$id = $_GPC['orderid'];
		$type = $_GPC['type'];

		if (empty($id)) {
			wl_message('缺少重要参数');
		}

		if ($type == 'rush') {
			$url = app_url('rush/home/hexiao', array('id' => $id));
			$order_out = pdo_get(PDO_NAME . 'rush_order', array('id' => $id));

			if ($order_out['mid'] != $_W['mid']) {
				wl_message('用户数据不匹配');
			}

			if ($order_out['status'] == 6 || $order_out['status'] == 7) {
				include wl_template('order/buyFail');
				exit();
			}
			else {
				if ($order_out['status'] == 1 || $order_out['status'] == 8 || $order_out['status'] == 2 || $order_out['status'] == 4) {
					$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $order_out['sid']));
					$goods = pdo_get('wlmerchant_rush_activity', array('id' => $order_out['activityid']));
					if ($order_out['checkcode'] || $order_out['neworderflag']) {
						$usetype = 'writeoff';

						if ($order_out['neworderflag']) {
							$order_out['levelnum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'' . $type . '\' AND  orderid = ' . $id . ' AND status != 1'));
							$smallorders = pdo_getall('wlmerchant_smallorder', array('plugin' => $type, 'orderid' => $id), array('status', 'checkcode'));
						}
						else {
							$order_out['levelnum'] = intval($order_out['num'] - $order_out['usetimes']);
						}

						if ($order_out['neworderflag']) {
							$order_out['usetimes'] = $order_out['num'] - $order_out['levelnum'];
						}

						$hxurl = app_url('rush/home/hexiaokey', array('sid' => $merchant['id'], 'orderid' => $id));
					}
					else {
						if ($order_out['expressid']) {
							$usetype = 'express';
							$express = pdo_get('wlmerchant_express', array('id' => $order_out['expressid']), array('name', 'tel', 'address', 'expressname', 'expresssn'));
							$express_list = array(
								array(name => '顺丰', express => 'shunfeng'),
								array(name => '申通', express => 'shentong'),
								array(name => '韵达快运', express => 'yunda'),
								array(name => '天天快递', express => 'tiantian'),
								array(name => '圆通速递', express => 'yuantong'),
								array(name => '中通速递', express => 'zhongtong'),
								array(name => 'ems快递', express => 'ems'),
								array(name => '汇通快运', express => 'huitongkuaidi'),
								array(name => '全峰快递', express => 'quanfengkuaidi'),
								array(name => '宅急送', express => 'zhaijisong'),
								array(name => 'aae全球专递', express => 'aae'),
								array(name => '安捷快递', express => 'anjie'),
								array(name => '安信达快递', express => 'anxindakuaixi'),
								array(name => '彪记快递', express => 'biaojikuaidi'),
								array(name => 'bht', express => 'bht'),
								array(name => '百福东方国际物流', express => 'baifudongfang'),
								array(name => '中国东方（COE）', express => 'coe'),
								array(name => '长宇物流', express => 'changyuwuliu'),
								array(name => '大田物流', express => 'datianwuliu'),
								array(name => '德邦物流', express => 'debangwuliu'),
								array(name => 'dhl', express => 'dhl'),
								array(name => 'dpex', express => 'dpex'),
								array(name => 'd速快递', express => 'dsukuaidi'),
								array(name => '递四方', express => 'disifang'),
								array(name => 'fedex（国外）', express => 'fedex'),
								array(name => '飞康达物流', express => 'feikangda'),
								array(name => '凤凰快递', express => 'fenghuangkuaidi'),
								array(name => '飞快达', express => 'feikuaida'),
								array(name => '国通快递', express => 'guotongkuaidi'),
								array(name => '港中能达物流', express => 'ganzhongnengda'),
								array(name => '广东邮政物流', express => 'guangdongyouzhengwuliu'),
								array(name => '共速达', express => 'gongsuda'),
								array(name => '恒路物流', express => 'hengluwuliu'),
								array(name => '华夏龙物流', express => 'huaxialongwuliu'),
								array(name => '海红', express => 'haihongwangsong'),
								array(name => '海外环球', express => 'haiwaihuanqiu'),
								array(name => '佳怡物流', express => 'jiayiwuliu'),
								array(name => '京广速递', express => 'jinguangsudikuaijian'),
								array(name => '急先达', express => 'jixianda'),
								array(name => '佳吉物流', express => 'jjwl'),
								array(name => '加运美物流', express => 'jymwl'),
								array(name => '金大物流', express => 'jindawuliu'),
								array(name => '嘉里大通', express => 'jialidatong'),
								array(name => '晋越快递', express => 'jykd'),
								array(name => '快捷速递', express => 'kuaijiesudi'),
								array(name => '联邦快递（国内）', express => 'lianb'),
								array(name => '联昊通物流', express => 'lianhaowuliu'),
								array(name => '龙邦物流', express => 'longbanwuliu'),
								array(name => '立即送', express => 'lijisong'),
								array(name => '乐捷递', express => 'lejiedi'),
								array(name => '民航快递', express => 'minghangkuaidi'),
								array(name => '美国快递', express => 'meiguokuaidi'),
								array(name => '门对门', express => 'menduimen'),
								array(name => 'OCS', express => 'ocs'),
								array(name => '配思货运', express => 'peisihuoyunkuaidi'),
								array(name => '全晨快递', express => 'quanchenkuaidi'),
								array(name => '全际通物流', express => 'quanjitong'),
								array(name => '全日通快递', express => 'quanritongkuaidi'),
								array(name => '全一快递', express => 'quanyikuaidi'),
								array(name => '如风达', express => 'rufengda'),
								array(name => '三态速递', express => 'santaisudi'),
								array(name => '盛辉物流', express => 'shenghuiwuliu'),
								array(name => '速尔物流', express => 'sue'),
								array(name => '盛丰物流', express => 'shengfeng'),
								array(name => '赛澳递', express => 'saiaodi'),
								array(name => '天地华宇', express => 'tiandihuayu'),
								array(name => 'tnt', express => 'tnt'),
								array(name => 'ups', express => 'ups'),
								array(name => '万家物流', express => 'wanjiawuliu'),
								array(name => '文捷航空速递', express => 'wenjiesudi'),
								array(name => '伍圆', express => 'wuyuan'),
								array(name => '万象物流', express => 'wxwl'),
								array(name => '新邦物流', express => 'xinbangwuliu'),
								array(name => '信丰物流', express => 'xinfengwuliu'),
								array(name => '亚风速递', express => 'yafengsudi'),
								array(name => '一邦速递', express => 'yibangwuliu'),
								array(name => '优速物流', express => 'youshuwuliu'),
								array(name => '邮政包裹挂号信', express => 'youzhengguonei'),
								array(name => '邮政国际包裹挂号信', express => 'youzhengguoji'),
								array(name => '远成物流', express => 'yuanchengwuliu'),
								array(name => '源伟丰快递', express => 'yuanweifeng'),
								array(name => '元智捷诚快递', express => 'yuanzhijiecheng'),
								array(name => '运通快递', express => 'yuntongkuaidi'),
								array(name => '越丰物流', express => 'yuefengwuliu'),
								array(name => '源安达', express => 'yad'),
								array(name => '银捷速递', express => 'yinjiesudi'),
								array(name => '中铁快运', express => 'zhongtiekuaiyun'),
								array(name => '中邮物流', express => 'zhongyouwuliu'),
								array(name => '忠信达', express => 'zhongxinda'),
								array(name => '芝麻开门', express => 'zhimakaimen')
							);

							foreach ($express_list as $key => $ex) {
								if ($express['expressname'] == $ex['express']) {
									$express['exname'] = $ex['name'];
								}
							}
						}
					}

					if ($order_out['optionid']) {
						$option = pdo_get('wlmerchant_goods_option', array('id' => $order_out['optionid']), array('price', 'vipprice', 'title'));
						$goods['price'] = $option['price'];
						$goods['vipprice'] = $option['vipprice'];
					}

					if ($order_out['vipbuyflag']) {
						$goods['price'] = $goods['vipprice'];
					}

					$order_out['price'] = $order_out['actualprice'];
					$location = unserialize($merchant['location']);
					$dkmoney = $order_out['dkmoney'];
					$dkcredit = $order_out['dkcredit'];
				}
				else {
					wl_message('该订单已完成或已退款');
				}
			}
		}
		else if ($type == 'groupon') {
			$order_out = pdo_get(PDO_NAME . 'order', array('id' => $id));

			if ($order_out['mid'] != $_W['mid']) {
				wl_message('用户数据不匹配');
			}

			if ($order_out['expressid']) {
				$usetype = 'express';
				$express = pdo_get('wlmerchant_express', array('id' => $order_out['expressid']), array('name', 'tel', 'address', 'expressname', 'expresssn'));
				$express_list = array(
					array(name => '顺丰', express => 'shunfeng'),
					array(name => '申通', express => 'shentong'),
					array(name => '韵达快运', express => 'yunda'),
					array(name => '天天快递', express => 'tiantian'),
					array(name => '圆通速递', express => 'yuantong'),
					array(name => '中通速递', express => 'zhongtong'),
					array(name => 'ems快递', express => 'ems'),
					array(name => '汇通快运', express => 'huitongkuaidi'),
					array(name => '全峰快递', express => 'quanfengkuaidi'),
					array(name => '宅急送', express => 'zhaijisong'),
					array(name => 'aae全球专递', express => 'aae'),
					array(name => '安捷快递', express => 'anjie'),
					array(name => '安信达快递', express => 'anxindakuaixi'),
					array(name => '彪记快递', express => 'biaojikuaidi'),
					array(name => 'bht', express => 'bht'),
					array(name => '百福东方国际物流', express => 'baifudongfang'),
					array(name => '中国东方（COE）', express => 'coe'),
					array(name => '长宇物流', express => 'changyuwuliu'),
					array(name => '大田物流', express => 'datianwuliu'),
					array(name => '德邦物流', express => 'debangwuliu'),
					array(name => 'dhl', express => 'dhl'),
					array(name => 'dpex', express => 'dpex'),
					array(name => 'd速快递', express => 'dsukuaidi'),
					array(name => '递四方', express => 'disifang'),
					array(name => 'fedex（国外）', express => 'fedex'),
					array(name => '飞康达物流', express => 'feikangda'),
					array(name => '凤凰快递', express => 'fenghuangkuaidi'),
					array(name => '飞快达', express => 'feikuaida'),
					array(name => '国通快递', express => 'guotongkuaidi'),
					array(name => '港中能达物流', express => 'ganzhongnengda'),
					array(name => '广东邮政物流', express => 'guangdongyouzhengwuliu'),
					array(name => '共速达', express => 'gongsuda'),
					array(name => '恒路物流', express => 'hengluwuliu'),
					array(name => '华夏龙物流', express => 'huaxialongwuliu'),
					array(name => '海红', express => 'haihongwangsong'),
					array(name => '海外环球', express => 'haiwaihuanqiu'),
					array(name => '佳怡物流', express => 'jiayiwuliu'),
					array(name => '京广速递', express => 'jinguangsudikuaijian'),
					array(name => '急先达', express => 'jixianda'),
					array(name => '佳吉物流', express => 'jjwl'),
					array(name => '加运美物流', express => 'jymwl'),
					array(name => '金大物流', express => 'jindawuliu'),
					array(name => '嘉里大通', express => 'jialidatong'),
					array(name => '晋越快递', express => 'jykd'),
					array(name => '快捷速递', express => 'kuaijiesudi'),
					array(name => '联邦快递（国内）', express => 'lianb'),
					array(name => '联昊通物流', express => 'lianhaowuliu'),
					array(name => '龙邦物流', express => 'longbanwuliu'),
					array(name => '立即送', express => 'lijisong'),
					array(name => '乐捷递', express => 'lejiedi'),
					array(name => '民航快递', express => 'minghangkuaidi'),
					array(name => '美国快递', express => 'meiguokuaidi'),
					array(name => '门对门', express => 'menduimen'),
					array(name => 'OCS', express => 'ocs'),
					array(name => '配思货运', express => 'peisihuoyunkuaidi'),
					array(name => '全晨快递', express => 'quanchenkuaidi'),
					array(name => '全际通物流', express => 'quanjitong'),
					array(name => '全日通快递', express => 'quanritongkuaidi'),
					array(name => '全一快递', express => 'quanyikuaidi'),
					array(name => '如风达', express => 'rufengda'),
					array(name => '三态速递', express => 'santaisudi'),
					array(name => '盛辉物流', express => 'shenghuiwuliu'),
					array(name => '速尔物流', express => 'sue'),
					array(name => '盛丰物流', express => 'shengfeng'),
					array(name => '赛澳递', express => 'saiaodi'),
					array(name => '天地华宇', express => 'tiandihuayu'),
					array(name => 'tnt', express => 'tnt'),
					array(name => 'ups', express => 'ups'),
					array(name => '万家物流', express => 'wanjiawuliu'),
					array(name => '文捷航空速递', express => 'wenjiesudi'),
					array(name => '伍圆', express => 'wuyuan'),
					array(name => '万象物流', express => 'wxwl'),
					array(name => '新邦物流', express => 'xinbangwuliu'),
					array(name => '信丰物流', express => 'xinfengwuliu'),
					array(name => '亚风速递', express => 'yafengsudi'),
					array(name => '一邦速递', express => 'yibangwuliu'),
					array(name => '优速物流', express => 'youshuwuliu'),
					array(name => '邮政包裹挂号信', express => 'youzhengguonei'),
					array(name => '邮政国际包裹挂号信', express => 'youzhengguoji'),
					array(name => '远成物流', express => 'yuanchengwuliu'),
					array(name => '源伟丰快递', express => 'yuanweifeng'),
					array(name => '元智捷诚快递', express => 'yuanzhijiecheng'),
					array(name => '运通快递', express => 'yuntongkuaidi'),
					array(name => '越丰物流', express => 'yuefengwuliu'),
					array(name => '源安达', express => 'yad'),
					array(name => '银捷速递', express => 'yinjiesudi'),
					array(name => '中铁快运', express => 'zhongtiekuaiyun'),
					array(name => '中邮物流', express => 'zhongyouwuliu'),
					array(name => '忠信达', express => 'zhongxinda'),
					array(name => '芝麻开门', express => 'zhimakaimen')
				);

				foreach ($express_list as $key => $ex) {
					if ($express['expressname'] == $ex['express']) {
						$express['exname'] = $ex['name'];
					}
				}
			}
			else {
				$usetype = 'writeoff';
			}

			if ($order_out['neworderflag']) {
				$order_out['levelnum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'' . $type . '\' AND  orderid = ' . $id . ' AND status != 1'));
				$smallorders = pdo_getall('wlmerchant_smallorder', array('plugin' => $type, 'orderid' => $id), array('status', 'checkcode'));
				$order_out['usetimes'] = $order_out['num'] - $order_out['levelnum'];
			}
			else {
				$record = pdo_get('wlmerchant_groupon_userecord', array('id' => $order_out['recordid']));
				$order_out['levelnum'] = $order_out['num'] - $record['usetimes'];
				$order_out['usetimes'] = $record['usetimes'];
				$order_out['checkcode'] = $record['qrcode'];
			}

			$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $order_out['sid']));
			$goods = pdo_get('wlmerchant_groupon_activity', array('id' => $order_out['fkid']));
			$order_out['optionid'] = $order_out['specid'];
			$hxurl = app_url('groupon/grouponapp/hexiaokey', array('sid' => $merchant['id'], 'orderid' => $id));
			$url = app_url('groupon/grouponapp/hexiao', array('id' => $id));

			if ($order_out['specid']) {
				$option = pdo_get('wlmerchant_goods_option', array('id' => $order_out['specid']), array('price', 'vipprice', 'title'));
				$goods['price'] = $option['price'];
				$goods['vipprice'] = $option['vipprice'];
			}

			if ($order_out['vipbuyflag']) {
				$goods['price'] = $goods['vipprice'];
			}
		}
		else if ($type == 'bargain') {
			$order_out = pdo_get(PDO_NAME . 'order', array('id' => $id));

			if ($order_out['mid'] != $_W['mid']) {
				wl_message('用户数据不匹配');
			}

			if ($order_out['expressid']) {
				$usetype = 'express';
				$express = pdo_get('wlmerchant_express', array('id' => $order_out['expressid']), array('name', 'tel', 'address', 'expressname', 'expresssn'));
				$express_list = array(
					array(name => '顺丰', express => 'shunfeng'),
					array(name => '申通', express => 'shentong'),
					array(name => '韵达快运', express => 'yunda'),
					array(name => '天天快递', express => 'tiantian'),
					array(name => '圆通速递', express => 'yuantong'),
					array(name => '中通速递', express => 'zhongtong'),
					array(name => 'ems快递', express => 'ems'),
					array(name => '汇通快运', express => 'huitongkuaidi'),
					array(name => '全峰快递', express => 'quanfengkuaidi'),
					array(name => '宅急送', express => 'zhaijisong'),
					array(name => 'aae全球专递', express => 'aae'),
					array(name => '安捷快递', express => 'anjie'),
					array(name => '安信达快递', express => 'anxindakuaixi'),
					array(name => '彪记快递', express => 'biaojikuaidi'),
					array(name => 'bht', express => 'bht'),
					array(name => '百福东方国际物流', express => 'baifudongfang'),
					array(name => '中国东方（COE）', express => 'coe'),
					array(name => '长宇物流', express => 'changyuwuliu'),
					array(name => '大田物流', express => 'datianwuliu'),
					array(name => '德邦物流', express => 'debangwuliu'),
					array(name => 'dhl', express => 'dhl'),
					array(name => 'dpex', express => 'dpex'),
					array(name => 'd速快递', express => 'dsukuaidi'),
					array(name => '递四方', express => 'disifang'),
					array(name => 'fedex（国外）', express => 'fedex'),
					array(name => '飞康达物流', express => 'feikangda'),
					array(name => '凤凰快递', express => 'fenghuangkuaidi'),
					array(name => '飞快达', express => 'feikuaida'),
					array(name => '国通快递', express => 'guotongkuaidi'),
					array(name => '港中能达物流', express => 'ganzhongnengda'),
					array(name => '广东邮政物流', express => 'guangdongyouzhengwuliu'),
					array(name => '共速达', express => 'gongsuda'),
					array(name => '恒路物流', express => 'hengluwuliu'),
					array(name => '华夏龙物流', express => 'huaxialongwuliu'),
					array(name => '海红', express => 'haihongwangsong'),
					array(name => '海外环球', express => 'haiwaihuanqiu'),
					array(name => '佳怡物流', express => 'jiayiwuliu'),
					array(name => '京广速递', express => 'jinguangsudikuaijian'),
					array(name => '急先达', express => 'jixianda'),
					array(name => '佳吉物流', express => 'jjwl'),
					array(name => '加运美物流', express => 'jymwl'),
					array(name => '金大物流', express => 'jindawuliu'),
					array(name => '嘉里大通', express => 'jialidatong'),
					array(name => '晋越快递', express => 'jykd'),
					array(name => '快捷速递', express => 'kuaijiesudi'),
					array(name => '联邦快递（国内）', express => 'lianb'),
					array(name => '联昊通物流', express => 'lianhaowuliu'),
					array(name => '龙邦物流', express => 'longbanwuliu'),
					array(name => '立即送', express => 'lijisong'),
					array(name => '乐捷递', express => 'lejiedi'),
					array(name => '民航快递', express => 'minghangkuaidi'),
					array(name => '美国快递', express => 'meiguokuaidi'),
					array(name => '门对门', express => 'menduimen'),
					array(name => 'OCS', express => 'ocs'),
					array(name => '配思货运', express => 'peisihuoyunkuaidi'),
					array(name => '全晨快递', express => 'quanchenkuaidi'),
					array(name => '全际通物流', express => 'quanjitong'),
					array(name => '全日通快递', express => 'quanritongkuaidi'),
					array(name => '全一快递', express => 'quanyikuaidi'),
					array(name => '如风达', express => 'rufengda'),
					array(name => '三态速递', express => 'santaisudi'),
					array(name => '盛辉物流', express => 'shenghuiwuliu'),
					array(name => '速尔物流', express => 'sue'),
					array(name => '盛丰物流', express => 'shengfeng'),
					array(name => '赛澳递', express => 'saiaodi'),
					array(name => '天地华宇', express => 'tiandihuayu'),
					array(name => 'tnt', express => 'tnt'),
					array(name => 'ups', express => 'ups'),
					array(name => '万家物流', express => 'wanjiawuliu'),
					array(name => '文捷航空速递', express => 'wenjiesudi'),
					array(name => '伍圆', express => 'wuyuan'),
					array(name => '万象物流', express => 'wxwl'),
					array(name => '新邦物流', express => 'xinbangwuliu'),
					array(name => '信丰物流', express => 'xinfengwuliu'),
					array(name => '亚风速递', express => 'yafengsudi'),
					array(name => '一邦速递', express => 'yibangwuliu'),
					array(name => '优速物流', express => 'youshuwuliu'),
					array(name => '邮政包裹挂号信', express => 'youzhengguonei'),
					array(name => '邮政国际包裹挂号信', express => 'youzhengguoji'),
					array(name => '远成物流', express => 'yuanchengwuliu'),
					array(name => '源伟丰快递', express => 'yuanweifeng'),
					array(name => '元智捷诚快递', express => 'yuanzhijiecheng'),
					array(name => '运通快递', express => 'yuntongkuaidi'),
					array(name => '越丰物流', express => 'yuefengwuliu'),
					array(name => '源安达', express => 'yad'),
					array(name => '银捷速递', express => 'yinjiesudi'),
					array(name => '中铁快运', express => 'zhongtiekuaiyun'),
					array(name => '中邮物流', express => 'zhongyouwuliu'),
					array(name => '忠信达', express => 'zhongxinda'),
					array(name => '芝麻开门', express => 'zhimakaimen')
				);

				foreach ($express_list as $key => $ex) {
					if ($express['expressname'] == $ex['express']) {
						$express['exname'] = $ex['name'];
					}
				}
			}
			else {
				$usetype = 'writeoff';
			}

			$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $order_out['sid']));
			$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $order_out['fkid']));

			if ($order_out['neworderflag']) {
				$order_out['levelnum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'' . $type . '\' AND  orderid = ' . $id . ' AND status != 1'));
				$smallorders = pdo_getall('wlmerchant_smallorder', array('plugin' => $type, 'orderid' => $id), array('status', 'checkcode'));
				$order_out['usetimes'] = $order_out['num'] - $order_out['levelnum'];
			}
			else {
				$record = pdo_get('wlmerchant_bargain_userlist', array('id' => $order_out['specid']));
				$order_out['levelnum'] = $order_out['num'] - $record['usetimes'];
				$order_out['checkcode'] = $record['qrcode'];
				$order_out['usetimes'] = $record['usetimes'];
			}

			$goods['price'] = $goods['oldprice'];
			$url = app_url('bargain/bargain_app/hexiao', array('id' => $id));
			$hxurl = app_url('bargain/bargain_app/hexiaokey', array('sid' => $merchant['id'], 'orderid' => $id));
			$goods['retainage'] = 0;
		}
		else {
			if ($type == 'wlfightgroup') {
				$order_out = pdo_get(PDO_NAME . 'order', array('id' => $id));

				if ($order_out['mid'] != $_W['mid']) {
					wl_message('用户数据不匹配');
				}

				if ($order_out['expressid']) {
					$usetype = 'express';
					$express = pdo_get(PDO_NAME . 'express', array('id' => $order_out['expressid']), array('name', 'tel', 'address', 'expressname', 'expresssn'));
					$express_list = array(
						array(name => '顺丰', express => 'shunfeng'),
						array(name => '申通', express => 'shentong'),
						array(name => '韵达快运', express => 'yunda'),
						array(name => '天天快递', express => 'tiantian'),
						array(name => '圆通速递', express => 'yuantong'),
						array(name => '中通速递', express => 'zhongtong'),
						array(name => 'ems快递', express => 'ems'),
						array(name => '汇通快运', express => 'huitongkuaidi'),
						array(name => '全峰快递', express => 'quanfengkuaidi'),
						array(name => '宅急送', express => 'zhaijisong'),
						array(name => 'aae全球专递', express => 'aae'),
						array(name => '安捷快递', express => 'anjie'),
						array(name => '安信达快递', express => 'anxindakuaixi'),
						array(name => '彪记快递', express => 'biaojikuaidi'),
						array(name => 'bht', express => 'bht'),
						array(name => '百福东方国际物流', express => 'baifudongfang'),
						array(name => '中国东方（COE）', express => 'coe'),
						array(name => '长宇物流', express => 'changyuwuliu'),
						array(name => '大田物流', express => 'datianwuliu'),
						array(name => '德邦物流', express => 'debangwuliu'),
						array(name => 'dhl', express => 'dhl'),
						array(name => 'dpex', express => 'dpex'),
						array(name => 'd速快递', express => 'dsukuaidi'),
						array(name => '递四方', express => 'disifang'),
						array(name => 'fedex（国外）', express => 'fedex'),
						array(name => '飞康达物流', express => 'feikangda'),
						array(name => '凤凰快递', express => 'fenghuangkuaidi'),
						array(name => '飞快达', express => 'feikuaida'),
						array(name => '国通快递', express => 'guotongkuaidi'),
						array(name => '港中能达物流', express => 'ganzhongnengda'),
						array(name => '广东邮政物流', express => 'guangdongyouzhengwuliu'),
						array(name => '共速达', express => 'gongsuda'),
						array(name => '恒路物流', express => 'hengluwuliu'),
						array(name => '华夏龙物流', express => 'huaxialongwuliu'),
						array(name => '海红', express => 'haihongwangsong'),
						array(name => '海外环球', express => 'haiwaihuanqiu'),
						array(name => '佳怡物流', express => 'jiayiwuliu'),
						array(name => '京广速递', express => 'jinguangsudikuaijian'),
						array(name => '急先达', express => 'jixianda'),
						array(name => '佳吉物流', express => 'jjwl'),
						array(name => '加运美物流', express => 'jymwl'),
						array(name => '金大物流', express => 'jindawuliu'),
						array(name => '嘉里大通', express => 'jialidatong'),
						array(name => '晋越快递', express => 'jykd'),
						array(name => '快捷速递', express => 'kuaijiesudi'),
						array(name => '联邦快递（国内）', express => 'lianb'),
						array(name => '联昊通物流', express => 'lianhaowuliu'),
						array(name => '龙邦物流', express => 'longbanwuliu'),
						array(name => '立即送', express => 'lijisong'),
						array(name => '乐捷递', express => 'lejiedi'),
						array(name => '民航快递', express => 'minghangkuaidi'),
						array(name => '美国快递', express => 'meiguokuaidi'),
						array(name => '门对门', express => 'menduimen'),
						array(name => 'OCS', express => 'ocs'),
						array(name => '配思货运', express => 'peisihuoyunkuaidi'),
						array(name => '全晨快递', express => 'quanchenkuaidi'),
						array(name => '全际通物流', express => 'quanjitong'),
						array(name => '全日通快递', express => 'quanritongkuaidi'),
						array(name => '全一快递', express => 'quanyikuaidi'),
						array(name => '如风达', express => 'rufengda'),
						array(name => '三态速递', express => 'santaisudi'),
						array(name => '盛辉物流', express => 'shenghuiwuliu'),
						array(name => '速尔物流', express => 'sue'),
						array(name => '盛丰物流', express => 'shengfeng'),
						array(name => '赛澳递', express => 'saiaodi'),
						array(name => '天地华宇', express => 'tiandihuayu'),
						array(name => 'tnt', express => 'tnt'),
						array(name => 'ups', express => 'ups'),
						array(name => '万家物流', express => 'wanjiawuliu'),
						array(name => '文捷航空速递', express => 'wenjiesudi'),
						array(name => '伍圆', express => 'wuyuan'),
						array(name => '万象物流', express => 'wxwl'),
						array(name => '新邦物流', express => 'xinbangwuliu'),
						array(name => '信丰物流', express => 'xinfengwuliu'),
						array(name => '亚风速递', express => 'yafengsudi'),
						array(name => '一邦速递', express => 'yibangwuliu'),
						array(name => '优速物流', express => 'youshuwuliu'),
						array(name => '邮政包裹挂号信', express => 'youzhengguonei'),
						array(name => '邮政国际包裹挂号信', express => 'youzhengguoji'),
						array(name => '远成物流', express => 'yuanchengwuliu'),
						array(name => '源伟丰快递', express => 'yuanweifeng'),
						array(name => '元智捷诚快递', express => 'yuanzhijiecheng'),
						array(name => '运通快递', express => 'yuntongkuaidi'),
						array(name => '越丰物流', express => 'yuefengwuliu'),
						array(name => '源安达', express => 'yad'),
						array(name => '银捷速递', express => 'yinjiesudi'),
						array(name => '中铁快运', express => 'zhongtiekuaiyun'),
						array(name => '中邮物流', express => 'zhongyouwuliu'),
						array(name => '忠信达', express => 'zhongxinda'),
						array(name => '芝麻开门', express => 'zhimakaimen')
					);

					foreach ($express_list as $key => $ex) {
						if ($express['expressname'] == $ex['express']) {
							$express['exname'] = $ex['name'];
						}
					}
				}
				else {
					$usetype = 'writeoff';
				}

				$merchant = pdo_get(PDO_NAME . 'merchantdata', array('id' => $order_out['sid']));
				$goods = pdo_get(PDO_NAME . 'fightgroup_goods', array('id' => $order_out['fkid']));
				$record = pdo_get(PDO_NAME . 'fightgroup_userecord', array('id' => $order_out['recordid']));
				$goods['thumb'] = $goods['logo'];
				$order_out['levelnum'] = $order_out['num'] - $record['usetimes'];
				$order_out['usetimes'] = $record['usetimes'];
				$order_out['checkcode'] = $record['qrcode'];
				$order_out['optionid'] = $order_out['specid'];
				$hxurl = app_url('wlfightgroup/fightapp/hexiaokey', array('sid' => $merchant['id'], 'orderid' => $id));
				$url = app_url('wlfightgroup/fightapp/hexiao', array('id' => $id));

				if ($order_out['specid']) {
					$option = pdo_get(PDO_NAME . 'goods_option', array('id' => $order_out['specid']), array('price', 'vipprice', 'title'));
					$goods['price'] = $option['price'];
				}
			}
		}

		$location = unserialize($merchant['location']);

		if ($type == 'rush') {
			$order_out['buyremark'] = $order_out['adminremark'];
			$order_out['remark'] = $order_out['remark'];
		}

		include wl_template('order/orderdetail');
	}

	public function receiveorder()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];
		if (empty($id) || empty($type)) {
			wl_json(1, '缺少重要参数');
		}

		if ($type == 'rush') {
			$res = pdo_update('wlmerchant_rush_order', array('status' => 2), array('id' => $id, 'status' => 4));
			$rushtask = array('type' => 'rush', 'orderid' => $id);
			$rushtask = serialize($rushtask);
			Queue::addTask(1, $rushtask, time(), $id);

			if ($res) {
				$expressid = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $id), 'expressid');
				pdo_update('wlmerchant_express', array('receivetime' => time()), array('id' => $expressid));
				$disorderid = pdo_getcolumn('wlmerchant_rush_order', array('id' => $id), 'disorderid');

				if (!empty($disorderid)) {
					$res = pdo_update('wlmerchant_disorder', array('status' => 1), array('status' => 0, 'id' => $disorderid));

					if ($res) {
						$distask = array('type' => 'rush', 'orderid' => $disorderid);
						$distask = serialize($distask);
						Queue::addTask(3, $distask, time(), $disorderid);
					}
				}

				wl_json(0);
			}
			else {
				wl_json(1, '收货失败,请重试');
			}
		}

		if ($type == 'groupon' || $type == 'wlfightgroup') {
			$res = pdo_update('wlmerchant_order', array('status' => 2), array('id' => $id, 'status' => 4));
			$ordertask = array('type' => $type, 'orderid' => $id);
			$ordertask = serialize($ordertask);
			Queue::addTask(2, $ordertask, time(), $id);

			if ($res) {
				$expressid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'expressid');
				pdo_update('wlmerchant_express', array('receivetime' => time()), array('id' => $expressid));
				$disorderid = pdo_getcolumn('wlmerchant_order', array('id' => $id), 'disorderid');

				if (!empty($disorderid)) {
					$res = pdo_update('wlmerchant_disorder', array('status' => 1), array('status' => 0, 'id' => $disorderid));

					if ($res) {
						$distask = array('type' => $type, 'orderid' => $disorderid);
						$distask = serialize($distask);
						Queue::addTask(3, $distask, time(), $disorderid);
					}
				}

				wl_json(0);
			}
			else {
				wl_json(1, '收货失败,请重试');
			}
		}
	}

	public function ordersubmit()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '确认订单 - ' . $_W['wlsetting']['base']['name'] : '确认订单';
		$type = $_GPC['type'];
		$specid = $_GPC['specid'];
		$id = $_GPC['id'];
		$num = $_GPC['num'] ? intval($_GPC['num']) : 1;
		$settings = Setting::wlsetting_read('orderset');
		$halfcardflag = Member::checkhalfmember();

		if ($type == 'bargain') {
			$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $id), array('name', 'thumb', 'usestatus', 'expressid', 'unit', 'sid'));
			$user = pdo_get('wlmerchant_bargain_userlist', array('id' => $specid), array('price'));
			$goodsprice = $goods['price'] = $user['price'];
			$price2 = $goodsprice;
			$goods['goodsimg'] = tomedia($goods['thumb']);
			$storename = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $goods['sid']), 'storename');
			$createorderurl = app_url('bargain/bargain_app/createorder', array('userid' => $specid));
			$topayurl = app_url('bargain/bargain_app/topay', array('id' => $id));
		}

		if ($type == 'consumption') {
			$goods = Consumption::creditshop_goods_get($id);
			$vipReduce = $goods['use_credit2'] - $goods['vipcredit2'];
			if ($goods['vipstatus'] == 1 && $halfcardflag) {
				$goods['use_credit1'] = $goods['vipcredit1'];
				$goods['use_credit2'] = $goods['vipcredit2'];
			}

			if ($goods['type'] == 'credit2' || $goods['type'] == 'halfcard') {
				$conflag = 1;
				$goods['usestatus'] = 0;
			}

			if ($goods['type'] == 'goods') {
				$goods['usestatus'] = 1;
			}

			$goodsprice = $goods['price'] = $goods['use_credit2'];
			$price2 = $goodsprice;
			$goods['goodsimg'] = tomedia($goods['thumb']);
			$storename = '积分商城';
			$createorderurl = app_url('consumption/goods/createorder', array('id' => $id));
			$goods['unit'] = '件';
			$goods['name'] = $goods['title'];
			$topayurl = app_url('consumption/goods/topay', array('id' => $id));
		}

		if ($type == 'wlfightgroup') {
			$buystatus = $_GPC['buystatus'];
			$groupid = $_GPC['groupid'];
			$spec = $_GPC['spec'];
			$goods = pdo_get('wlmerchant_fightgroup_goods', array('id' => $id));
			$goods['goodsimg'] = tomedia($goods['logo']);
			$storename = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $goods['merchantid']), 'storename');

			if ($specid) {
				$option = pdo_get('wlmerchant_goods_option', array('id' => $specid), array('title', 'price', 'vipprice'));

				if ($buystatus == 1) {
					$goods['price'] = $option['price'];
				}
				else {
					$goods['price'] = $option['vipprice'];
				}
			}
			else {
				if ($buystatus == 2) {
					$goods['price'] = $goods['aloneprice'];
				}
			}

			$goodsprice = sprintf('%.2f', $goods['price'] * $num);
			$vipReduce = sprintf('%.2f', $num * $goods['vipdiscount']);
			$goods['vipstatus'] = 1;
			if (0 < $goods['vipdiscount'] && $halfcardflag) {
				$vipdiscount = sprintf('%.2f', $num * $goods['vipdiscount']);
			}
			else {
				$vipdiscount = 0;
			}

			$price2 = $goodsprice - $vipdiscount;
			$createorderurl = app_url('wlfightgroup/fightapp/createFightorder', array('num' => $num, 'id' => $id, 'spec' => $spec, 'specid' => $specid, 'buystatus' => $buystatus, 'groupid' => $groupid));
			$topayurl = app_url('wlfightgroup/fightapp/createFightorder');

			if ($goods['markid']) {
				$mark = pdo_get('wlmerchant_marking', array('id' => $goods['markid']));
				if (0 < $mark['creditmoney'] && 0 < $mark['deduct']) {
					$allcredit = sprintf('%.0f', $_W['wlmember']['credit1']);
					$dkcredit = sprintf('%.0f', $mark['deduct'] * $num / $mark['creditmoney']);
					$dkcredit = $dkcredit;

					if ($allcredit < $dkcredit) {
						$dkcredit = $allcredit;
					}

					$dkmoney = sprintf('%.2f', $dkcredit * $mark['creditmoney']);
				}
			}
		}

		if ($type == 'rush') {
			$goods = pdo_get('wlmerchant_rush_activity', array('id' => $id));
			$goods['goodsimg'] = tomedia($goods['thumb']);
			$storename = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $goods['sid']), 'storename');

			if ($specid) {
				$option = pdo_get('wlmerchant_goods_option', array('id' => $specid), array('title', 'price', 'vipprice'));
				$goods['price'] = $option['price'];
				$goods['vipprice'] = $option['vipprice'];
				$spec = $option['title'];
			}

			if ($halfcardflag && $goods['vipstatus'] == 1) {
				$price = $goods['vipprice'];
			}
			else {
				$price = $goods['price'];
			}

			$goodsprice = sprintf('%.2f', $price * $num);
			$oldprice = sprintf('%.2f', $goods['price'] * $num);
			$vipPrice = sprintf('%.2f', $goods['vipprice'] * $num);
			$vipReduce = sprintf('%.2f', $oldprice - $vipPrice);
			$price2 = $goodsprice;
			$createorderurl = app_url('rush/rushpay/topayrush', array('num' => $num, 'id' => $id, 'optionid' => $specid));
			$topayurl = app_url('rush/rushpay/topay');
			if ($goods['creditmoney'] && $_W['wlsetting']['creditset']['dkstatus']) {
				if ($price < $goods['creditmoney']) {
					$goods['creditmoney'] = $price;
				}

				$onedkcredit = ceil($goods['creditmoney'] * $_W['wlsetting']['creditset']['proportion']);
				$dkcredit = $onedkcredit * $num;
				$allcredit = sprintf('%.0f', $_W['wlmember']['credit1']);

				if ($allcredit < $dkcredit) {
					$dkcredit = $allcredit;
				}

				$dkmoney = sprintf('%.2f', $dkcredit / $_W['wlsetting']['creditset']['proportion']);
				if ($goodsprice < $dkmoney || $dkmoney == $goodsprice) {
					$dkmoney = sprintf('%.2f', $goodsprice - 0.01);
				}
			}
		}

		if ($type == 'groupon') {
			$goods = pdo_get('wlmerchant_groupon_activity', array('id' => $id));
			$goods['goodsimg'] = tomedia($goods['thumb']);
			$storename = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $goods['sid']), 'storename');

			if ($specid) {
				$option = pdo_get('wlmerchant_goods_option', array('id' => $specid), array('title', 'price', 'vipprice'));
				$goods['price'] = $option['price'];
				$goods['vipprice'] = $option['vipprice'];
				$spec = $option['title'];
			}

			if ($halfcardflag && $goods['vipstatus'] == 1) {
				$price = $goods['vipprice'];
			}
			else {
				$price = $goods['price'];
			}

			$goodsprice = sprintf('%.2f', $price * $num);
			$oldprice = sprintf('%.2f', $goods['price'] * $num);
			$vipPrice = sprintf('%.2f', $goods['vipprice'] * $num);
			$vipReduce = sprintf('%.2f', $oldprice - $vipPrice);
			$price2 = $goodsprice;
			$createorderurl = app_url('groupon/grouponapp/topaygroupon', array('num' => $num, 'id' => $id, 'optionid' => $specid));
			$topayurl = app_url('groupon/grouponapp/topay');
		}

		if ($type == 'wlfightgroup') {
			$backurl = app_url('order/userorder/ordersubmit', array('type' => $type, 'specid' => $specid, 'id' => $id, 'num' => $num, 'spec' => $spec, 'buystatus' => $buystatus, 'groupid' => $groupid));
		}
		else {
			$backurl = app_url('order/userorder/ordersubmit', array('type' => $type, 'specid' => $specid, 'id' => $id, 'num' => $num));
		}

		if (empty($goods['unit'])) {
			$goods['unit'] = '个';
		}

		if (0 < $goods['usestatus']) {
			$addressid = $_GPC['addressid'];

			if (empty($addressid)) {
				$address = pdo_get('wlmerchant_address', array('status' => 1, 'mid' => $_W['mid']));
				$addressid = $address['id'];
			}
			else {
				$address = pdo_get('wlmerchant_address', array('id' => $addressid));
			}

			if ($goods['expressid']) {
				$express = pdo_get('wlmerchant_express_template', array('id' => $goods['expressid']));

				if (mb_substr($address['province'], -1, 1, 'utf-8') == '省') {
					$address['province'] = mb_substr($address['province'], 0, mb_strlen($address['province']) - 1, 'utf-8');
				}

				if ($express['expressarray']) {
					$expressarray = unserialize($express['expressarray']);

					foreach ($expressarray as $key => &$v) {
						$v['area'] = rtrim($v['area'], ',');
						$v['area'] = explode(',', $v['area']);

						if (in_array($address['province'], $v['area'])) {
							if ($v['num'] < $num) {
								$expressprice = $v['money'] + ceil(($num - $v['num']) / $v['numex']) * $v['moneyex'];
							}
							else {
								$expressprice = $v['money'];
							}
						}
					}
				}

				if (empty($expressprice)) {
					if ($express['defaultnum'] < $num) {
						$expressprice = $express['defaultmoney'] + ceil(($num - $express['defaultnum']) / $express['defaultnumex']) * $express['defaultmoneyex'];
					}
					else {
						$expressprice = $express['defaultmoney'];
					}
				}
			}
			else {
				$expressprice = 0;
			}

			$price1 = sprintf('%.2f', $price2 + $expressprice);
		}

		if (empty($addressid)) {
			$addressid = false;
		}

		$level = unserialize($goods['level']);
		if ($goods['level'] && 0 < $level[0]) {
			$goods['level'] = unserialize($goods['level']);
		}
		else {
			$goods['level'] = '';
		}

		$levels = pdo_fetchall('SELECT id,`name`,price,sort,detail,days,levelid FROM ' . tablename(PDO_NAME . 'halfcard_type') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND (aid = 0 OR aid = ' . $_W['aid'] . ') AND is_hot = 1 AND status = 1 ORDER BY sort DESC,id DESC'));
		$meroof = Setting::agentsetting_read('meroof');
		include wl_template('order/ordersubmit');
	}

	public function cancelorder()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$plugin = $_GPC['pl'];

		if ($plugin != 'rush') {
			$plugin = pdo_getcolumn(PDO_NAME . 'order', array('id' => $id), 'plugin');

			if ($plugin == 'groupon') {
				$res = Groupon::cancelorder($id);
			}
			else {
				$res = pdo_update('wlmerchant_order', array('status' => 5), array('id' => $id));
			}
		}
		else {
			$res = Rush::cancelorder($id);
		}

		if ($res) {
			exit(json_encode(1));
		}
		else {
			exit(json_encode(0));
		}
	}

	public function cancelrefundorder()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$plugin = $_GPC['pl'];

		if ($plugin != 'rush') {
			$res = pdo_update('wlmerchant_order', array('applyrefund' => 0), array('id' => $id));
		}
		else {
			$res = pdo_update('wlmerchant_rush_order', array('applyrefund' => 0), array('id' => $id));
		}

		if ($res) {
			exit(json_encode(1));
		}
		else {
			exit(json_encode(0));
		}
	}

	public function refundorder()
	{
		global $_W;
		global $_GPC;
		$settings = Setting::wlsetting_read('orderset');
		$id = $_GPC['id'];
		$plugin = $_GPC['pl'];
		$memberr = pdo_get(PDO_NAME . 'rush_order', array('id' => $id));
		$member = pdo_get(PDO_NAME . 'order', array('id' => $id));
		$nickname = pdo_getcolumn(PDO_NAME . 'member', array('id' => $member['mid']), 'nickname');
		$nicknamee = pdo_getcolumn(PDO_NAME . 'member', array('id' => $memberr['mid']), 'nickname');
		$storeadminss = pdo_fetchall('SELECT mid FROM ' . tablename('wlmerchant_merchantuser') . (' WHERE storeid = ' . $memberr['sid'] . ' '));
		$storeadminopenidd = pdo_getcolumn(PDO_NAME . 'member', array('id' => $storeadminss[0]['mid']), 'openid');
		$storeadmins = pdo_fetchall('SELECT mid FROM ' . tablename('wlmerchant_merchantuser') . (' WHERE storeid = ' . $member['sid'] . ' '));
		$storeadminopenid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $storeadmins[0]['mid']), 'openid');

		if ($plugin == 'groupon') {
			$goodsname = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $member['fkid']), 'name');
			$plugin = '团购';
		}
		else if ($plugin == 'wlfightgroup') {
			$goodsname = pdo_getcolumn(PDO_NAME . 'fightgroup_goods', array('id' => $member['fkid']), 'name');
			$plugin = '拼团';
		}
		else if ($plugin == 'coupon') {
			$goodsname = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $member['fkid']), 'title');
			$plugin = '卡卷';
		}
		else if ($plugin == 'bargain') {
			$goodsname = pdo_getcolumn(PDO_NAME . 'bargain_activity', array('id' => $member['fkid']), 'name');
			$plugin = '砍价';
		}
		else {
			if ($plugin == 'rush') {
				$goodsname = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $memberr['activityid']), 'name');
				$plugin = '抢购';
			}
		}

		if (0 < $settings['autoapplyre']) {
			$data = array('applyrefund' => 2, 'applytime' => time(), 'status' => 6);
		}
		else {
			$data = array('applyrefund' => 1, 'applytime' => time());
		}

		if ($plugin != '抢购') {
			$res = pdo_update('wlmerchant_order', $data, array('id' => $id));
		}
		else {
			$res = pdo_update('wlmerchant_rush_order', $data, array('id' => $id));
		}

		if ($res) {
			if ($plugin != '抢购') {
				$nickname = $nickname;
				$storeadminopenid = $storeadminopenid;
			}
			else {
				$nickname = $nicknamee;
				$storeadminopenid = $storeadminopenidd;
			}

			$first = '您好,用户[' . $nickname . ']退款商品[' . $goodsname . ']的通知';
			$keyword1 = '[' . $plugin . ']商品订单退款';
			$keyword2 = '申请退款中';
			$remark = '订单退款消息请及时处理';
			$adminurl = app_url('dashboard/home/index');
			$openids = pdo_getall('wlmerchant_agentadmin', array('notice' => 1, 'aid' => $_W['aid']), array('openid'));

			foreach ($openids as $key => $mem) {
				Message::jobNotice($mem['openid'], $first, $keyword1, $keyword2, $remark, $adminurl);
			}

			Message::jobNotice($storeadminopenid, $first, $keyword1, $keyword2, $remark, $adminurl);
			exit(json_encode(1));
		}
		else {
			exit(json_encode(0));
		}
	}

	private function checkcoupon($coupon, $ndorder)
	{
		global $_W;
		global $_GPC;
		if (empty($coupon) || empty($ndorder)) {
			return false;
		}

		if (($coupon['usetimes'] < 1 || $coupon['endtime'] < time()) && $ndorder['status'] == 1) {
			pdo_update(PDO_NAME . 'order', array('status' => 2), array('orderno' => $ndorder['orderno']));
		}
	}

	public function payover()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '支付成功 - ' . $_W['wlsetting']['base']['name'] : '支付成功';
		$id = $_GPC['id'];
		$type = $_GPC['type'];

		if ($type == 1) {
			$order = pdo_get('wlmerchant_rush_order', array('id' => $id), array('paytype', 'paidprid', 'actualprice', 'vip_card_id'));
			$price = $order['actualprice'];
			$detaila = app_url('order/userorder/orderdetail', array('orderid' => $id, 'type' => 'rush'));
		}
		else if ($type == 4) {
			$order = pdo_get('wlmerchant_order', array('id' => $id), array('paytype', 'paidprid', 'price', 'vip_card_id'));
			$price = $order['price'];
			$detaila = app_url('order/userorder/orderdetail', array('orderid' => $id, 'type' => 'groupon'));
		}
		else if ($type == 2) {
			$order = pdo_get('wlmerchant_order', array('id' => $id), array('paytype', 'paidprid', 'price', 'vip_card_id'));
			$price = $order['price'];
			$detaila = app_url('wlfightgroup/fightapp/expressorder', array('orderid' => $id));
		}
		else if ($type == 3) {
			$order = pdo_get('wlmerchant_order', array('id' => $id), array('paytype', 'paidprid', 'price', 'recordid', 'vip_card_id'));
			$price = $order['price'];
			$detaila = app_url('wlcoupon/coupon_app/couponDetail', array('id' => $order['recordid']));
		}
		else if ($type == 5) {
			$order = pdo_get('wlmerchant_halfcard_record', array('id' => $id), array('paytype', 'paidprid', 'price'));
			$price = $order['price'];
			$detaila = app_url('halfcard/halfcard_app/userhalfcard');
			$detailtext = '返回会员中心';
		}
		else if ($type == 6) {
			$order = pdo_get('wlmerchant_order', array('id' => $id), array('paytype', 'paidprid', 'price', 'vip_card_id', 'sid'));
			$price = $order['price'];
			$detaila = app_url('store/supervise/platform', array('storeid' => $order['sid']));
			$detailtext = '查看我的店铺';
		}
		else if ($type == 7) {
			$order = pdo_get('wlmerchant_order', array('id' => $id), array('paytype', 'paidprid', 'price', 'vip_card_id'));
			$price = $order['price'];
			$detaila = app_url('halfcard/halfcard_app/userhalfcard');
			$detailtext = '返回会员中心';
		}
		else if ($type == 8) {
			$order = pdo_get('wlmerchant_order', array('id' => $id), array('paytype', 'paidprid', 'price', 'vip_card_id'));
			$price = $order['price'];

			if ($order['expressid']) {
				$detaila = app_url('bargain/bargain_app/expressorder', array('orderid' => $id));
			}
			else {
				$detaila = app_url('order/userorder/orderdetail', array('orderid' => $id, 'type' => 'bargain'));
			}
		}
		else {
			if ($type == 9) {
				$order = pdo_get('wlmerchant_order', array('id' => $id), array('paytype', 'paidprid', 'price', 'vip_card_id'));
				$price = $order['price'];
				$detaila = app_url('consumption/goods/recordlist');
				$detailtext = '查看兑换记录';
			}
		}

		if ($order['vip_card_id']) {
			$cardprice = pdo_getcolumn(PDO_NAME . 'halfcard_type', array('uniacid' => $_W['uniacid'], 'id' => $order['vip_card_id']), 'price');
			$price = sprintf('%.2f', $price + $cardprice);
		}

		if (p('paidpromotion') && empty($order['paidprid'])) {
			$paidprid = Paidpromotion::getpaidpr($type, $id, $order['paytype']);

			if ($paidprid) {
				if ($type == 1) {
					pdo_update('wlmerchant_rush_order', array('paidprid' => $paidprid), array('id' => $id));
				}
				else if ($type == 5) {
					pdo_update('wlmerchant_halfcard_record', array('paidprid' => $paidprid), array('id' => $id));
				}
				else {
					pdo_update('wlmerchant_order', array('paidprid' => $paidprid), array('id' => $id));
				}
			}
		}

		if ($order['paidprid'] || $paidprid) {
			$parid = $order['paidprid'] ? $order['paidprid'] : $paidprid;
			$paid = pdo_get('wlmerchant_paidrecord', array('id' => $parid));
			$pactivity = pdo_get('wlmerchant_payactive', array('id' => $paid['activeid']));
			$couponIdList = explode(',', $pactivity['giftcouponid']);

			if (is_array($couponIdList)) {
				foreach ($couponIdList as $key => $val) {
					$couponNameList[$key] = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $val), 'title');
				}
			}

			if ($paid['codeid']) {
				$code = pdo_get(PDO_NAME . 'token', array('id' => $paid['codeid']), array('number', 'status'));
			}
		}

		include wl_template('order/payover');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
