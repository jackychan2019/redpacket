<?php
//dezend by http://www.sucaihuo.com/
class Order
{
	static protected $name = 'order';
	static protected $rush = 'rush_order';
	static protected $tableName;
	static protected $rushTableName;

	/**
     * 构造方法
     * Order constructor.
     */
	public function __construct()
	{
		self::$tableName = tablename(PDO_NAME . self::$name);
		self::$rushTableName = tablename(PDO_NAME . self::$rush);
	}

	/**
     * Comment: 根据条件获取商品的销量
     * Author: zzw
     * Date: 2019/7/12 18:38
     * @param string $params
     * @param bool   $isRush
     * @return mixed
     */
	public function getPurchaseQuantity($params = '', $isRush = false)
	{
		if ($isRush) {
			$sql = 'SELECT SUM(num) FROM ' . self::$rushTableName;
		}
		else {
			$sql = 'SELECT SUM(num) FROM ' . self::$tableName;
		}

		!empty($params) && ($sql .= ' WHERE ' . $params . ' ');
		return pdo_fetchcolumn($sql);
	}

	public function createSmallorder($orderid, $type)
	{
		if ($type == 1) {
			$order = pdo_get(PDO_NAME . self::$rush, array('id' => $orderid), array('orderno', 'actualprice', 'uniacid', 'mid', 'sid', 'aid', 'num', 'settlementmoney'));
			$settmoney = sprintf('%.2f', $order['settlementmoney'] / $order['num']);
			$orderprice = sprintf('%.2f', $order['actualprice'] / $order['num']);
			$plugin = 'rush';
		}
		else if ($type == 2) {
			$order = pdo_get(PDO_NAME . self::$name, array('id' => $orderid), array('orderno', 'price', 'uniacid', 'mid', 'sid', 'aid', 'num', 'settlementmoney'));
			$settmoney = sprintf('%.2f', $order['settlementmoney'] / $order['num']);
			$orderprice = sprintf('%.2f', $order['price'] / $order['num']);
			$plugin = 'groupon';
		}
		else if ($type == 3) {
			$order = pdo_get(PDO_NAME . self::$name, array('id' => $orderid), array('orderno', 'price', 'uniacid', 'mid', 'sid', 'aid', 'num', 'settlementmoney'));
			$settmoney = sprintf('%.2f', $order['settlementmoney'] / $order['num']);
			$orderprice = sprintf('%.2f', $order['price'] / $order['num']);
			$plugin = 'wlfightgroup';
		}
		else if ($type == 4) {
			$order = pdo_get(PDO_NAME . self::$name, array('id' => $orderid), array('orderno', 'price', 'fkid', 'uniacid', 'mid', 'sid', 'aid', 'num', 'settlementmoney'));
			$usetimes = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $order['fkid']), 'usetimes');
			$order['num'] = $order['num'] * $usetimes;
			$settmoney = sprintf('%.2f', $order['settlementmoney'] / $order['num']);
			$orderprice = sprintf('%.2f', $order['price'] / $order['num']);
			$plugin = 'coupon';
		}
		else {
			if ($type == 5) {
				$order = pdo_get(PDO_NAME . self::$name, array('id' => $orderid), array('orderno', 'price', 'uniacid', 'mid', 'sid', 'aid', 'num', 'settlementmoney'));
				$settmoney = sprintf('%.2f', $order['settlementmoney'] / $order['num']);
				$orderprice = sprintf('%.2f', $order['price'] / $order['num']);
				$plugin = 'bargain';
			}
		}

		$i = 0;

		while ($i < $order['num']) {
			$sdata = array('uniacid' => $order['uniacid'], 'mid' => $order['mid'], 'aid' => $order['aid'], 'sid' => $order['sid'], 'status' => 1, 'plugin' => $plugin, 'orderid' => $orderid, 'orderno' => $order['orderno'], 'createtime' => time(), 'checkcode' => Util::createConcode(7, 8), 'orderprice' => $orderprice, 'settlemoney' => $settmoney);
			pdo_insert(PDO_NAME . 'smallorder', $sdata);
			++$i;
		}
	}

	public function finishSmallorder($orderid, $uid = 0, $type = 1)
	{
		$order = pdo_get(PDO_NAME . 'smallorder', array('id' => $orderid));
		$sdata['status'] = 2;
		$sdata['hxuid'] = $uid;
		$sdata['hexiaotime'] = time();
		$sdata['settletime'] = time();
		$sdata['hexiaotype'] = $type;
		$flag = pdo_get(PDO_NAME . 'autosettlement_record', array('checkcode' => $order['checkcode']), array('id'));

		if ($flag) {
			return 1;
		}

		if ($order['plugin'] == 'rush') {
			$type = 1;
			$goodsid = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $order['orderid']), 'activityid');
		}
		else if ($order['plugin'] == 'groupon') {
			$type = 10;
			$goodsid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $order['orderid']), 'fkid');
		}
		else if ($order['plugin'] == 'wlfightgroup') {
			$type = 2;
			$goodsid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $order['orderid']), 'fkid');
		}
		else if ($order['plugin'] == 'bargain') {
			$type = 12;
			$goodsid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $order['orderid']), 'fkid');
		}
		else {
			if ($order['plugin'] == 'coupon') {
				$type = 3;
				$goodsid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $order['orderid']), 'fkid');
			}
		}

		$data = array('uniacid' => $order['uniacid'], 'aid' => $order['aid'], 'type' => $type, 'merchantid' => $order['sid'], 'orderid' => $order['orderid'], 'orderno' => $order['orderno'], 'goodsid' => $goodsid, 'orderprice' => $order['orderprice'], 'agentmoney' => round($order['orderprice'] - $order['settlemoney'] - $order['onedismoney'] - $order['twodismoney'], 2), 'merchantmoney' => $order['settlemoney'], 'distrimoney' => round($order['onedismoney'] + $order['twodismoney'], 2), 'sharemoney' => 0, 'createtime' => time(), 'checkcode' => $order['checkcode']);
		$res = pdo_insert(PDO_NAME . 'autosettlement_record', $data);
		$settlementid = pdo_insertid();

		if ($res) {
			if (0 < abs($data['merchantmoney'])) {
				pdo_fetch('update' . tablename('wlmerchant_merchantdata') . ('SET allmoney=allmoney+' . $data['merchantmoney'] . ',nowmoney=nowmoney+' . $data['merchantmoney'] . ' WHERE id = ' . $data['merchantid']));
				$change['merchantnowmoney'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $data['merchantid']), 'nowmoney');
				Store::addcurrent(1, $type, $data['merchantid'], $data['merchantmoney'], $change['merchantnowmoney'], $order['orderid'], $data['checkcode'], $order['uniacid'], $order['aid']);
			}

			if (0 < abs($data['agentmoney'])) {
				pdo_fetch('update' . tablename('wlmerchant_agentusers') . ('SET allmoney=allmoney+' . $data['agentmoney'] . ',nowmoney=nowmoney+' . $data['agentmoney'] . ' WHERE id = ' . $data['aid']));
				$change['agentnowmoney'] = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $data['aid']), 'nowmoney');
				Store::addcurrent(2, $type, $data['aid'], $data['agentmoney'], $change['agentnowmoney'], $order['orderid'], $data['checkcode'], $order['uniacid'], $order['aid']);
			}

			pdo_update('wlmerchant_autosettlement_record', $change, array('id' => $settlementid));

			if ($order['disorderid']) {
				$disorder = pdo_get('wlmerchant_disorder', array('id' => $order['disorderid']));

				if (empty($disorder['status'])) {
					$nosetflag = pdo_getcolumn('wlmerchant_disdetail', array('checkcode' => $order['checkcode']), 'id');

					if (empty($nosetflag)) {
						if (0 < $order['onedismoney']) {
							pdo_fetch('update' . tablename('wlmerchant_distributor') . ('SET dismoney=dismoney+' . $order['onedismoney'] . ',nowmoney=nowmoney+' . $order['onedismoney'] . ' WHERE id = ' . $order['oneleadid']));
							$leadid = pdo_getcolumn('wlmerchant_distributor', array('id' => $order['oneleadid']), 'mid');
							$onenowmoney = pdo_getcolumn(PDO_NAME . 'distributor', array('id' => $order['oneleadid']), 'nowmoney');
							Distribution::adddisdetail($order['disorderid'], $leadid, $disorder['buymid'], 1, $order['onedismoney'], $disorder['plugin'], 1, '分销订单结算', $onenowmoney, $data['checkcode']);
							Distribution::moneyNotice($disorder['buymid'], $disorder['plugin'], $disorder['orderid'], $disorder['oneleadid'], $disorder['id'], 2);
							Distribution::checkup($order['oneleadid']);
						}

						if (0 < $order['twodismoney']) {
							pdo_fetch('update' . tablename('wlmerchant_distributor') . ('SET dismoney=dismoney+' . $order['twodismoney'] . ',nowmoney=nowmoney+' . $order['twodismoney'] . ' WHERE id = ' . $order['twoleadid']));
							$leadid = pdo_getcolumn('wlmerchant_distributor', array('id' => $order['twoleadid']), 'mid');
							$twonowmoney = pdo_getcolumn(PDO_NAME . 'distributor', array('id' => $order['twoleadid']), 'nowmoney');
							Distribution::adddisdetail($order['disorderid'], $leadid, $disorder['buymid'], 1, $order['twodismoney'], $disorder['plugin'], 2, '分销订单结算', $twonowmoney, $data['checkcode']);
							Distribution::moneyNotice($disorder['buymid'], $disorder['plugin'], $disorder['orderid'], $disorder['twoleadid'], $disorder['id'], 2);
							Distribution::checkup($order['twoleadid']);
						}

						$sdata['dissettletime'] = time();
					}
				}
			}

			pdo_update('wlmerchant_smallorder', $sdata, array('id' => $orderid));

			if ($order['plugin'] == 'rush') {
				$goodsid = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $order['orderid']), 'activityid');
				$goodsname = pdo_getcolumn(PDO_NAME . 'rush_activity', array('id' => $goodsid), 'name');
			}
			else {
				if ($order['plugin'] == 'groupon') {
					$goodsid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $order['orderid']), 'fkid');
					$goodsname = pdo_getcolumn(PDO_NAME . 'groupon_activity', array('id' => $goodsid), 'name');
				}
			}

			SingleMerchant::verifRecordAdd($order['aid'], $order['sid'], $order['mid'], $order['plugin'], $order['orderid'], $order['checkcode'], $goodsname, $type, 1);
			$plugin = $order['plugin'];
			$finishflag = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_smallorder') . (' WHERE plugin = \'' . $plugin . '\' AND  orderid = ' . $order['orderid'] . ' AND status = 1'));

			if (empty($finishflag)) {
				if ($order['plugin'] == 'rush') {
					pdo_update('wlmerchant_rush_order', array('issettlement' => 1, 'status' => 2), array('id' => $order['orderid']));
				}
				else {
					pdo_update('wlmerchant_order', array('issettlement' => 1, 'status' => 2), array('id' => $order['orderid']));

					if ($order['plugin'] == 'coupon') {
						$recordid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $order['orderid']), 'recordid');
						pdo_update('wlmerchant_member_coupons', array('status' => 2, 'usetimes' => 0), array('id' => $recordid));
					}
				}

				if ($order['disorderid']) {
					pdo_update('wlmerchant_disorder', array('status' => 2), array('id' => $order['disorderid']));
				}
			}

			return true;
		}

		return false;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
