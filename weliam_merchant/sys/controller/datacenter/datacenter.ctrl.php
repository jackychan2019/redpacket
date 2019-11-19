<?php
//dezend by http://www.sucaihuo.com/
class Datacenter_WeliamController
{
	public function stat_operate()
	{
		global $_W;
		global $_GPC;

		if (!is_agent()) {
			$agents = pdo_getall('wlmerchant_agentusers', array('uniacid' => $_W['uniacid']), array('id', 'agentname'));
		}

		$store_where = is_agent() ? array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'enabled' => 1) : array('uniacid' => $_W['uniacid'], 'enabled' => 1);
		$stores = pdo_getall('wlmerchant_merchantdata', $store_where, array('id', 'storename'));
		$condition = is_agent() ? 'WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] : 'WHERE uniacid = ' . $_W['uniacid'];
		$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;

		if ($days == -1) {
			$starttime = strtotime(trim($_GPC['stat_day']['start']));
			$endtime = strtotime(trim($_GPC['stat_day']['end']));
			$lenth = ceil((time() - $starttime) / 86400);
			$i = ceil((time() - $endtime) / 86400);
			$lenth = $lenth - $i + 1;
		}
		else {
			$todaytime = strtotime(date('Y-m-d'));
			$starttime = strtotime('-' . $days . ' days', $todaytime);
			$endtime = $todaytime + 86399;
			$lenth = $days + 1;
			$i = 0;
		}

		$selstarttime = date('Y-m-d', $starttime);
		$selendtime = date('Y-m-d', $endtime);
		if (intval($_GPC['agentid']) && $_GPC['type'] == 2) {
			$aid = intval($_GPC['agentid']);
			$condition .= ' AND aid = ' . $aid . ' ';
		}

		if (intval($_GPC['sid']) && $_GPC['type'] == 1) {
			$sid = intval($_GPC['sid']);
			$condition .= ' AND sid = ' . $sid . ' ';
			//做显示而已 
			$wydata = Store::getSingleStore($sid);
			if($wydata['salesmid']){
				$salesrate = $wydata['salesrate'];
			}
		}

		$daycon = $condition;
		$condition .= ' AND paytime > ' . $starttime . ' AND paytime < ' . $endtime;

		if ($_W['isajax']) {
			$rushmoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition);

			if (empty($sid)) {
				$halfmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_halfcard_record') . $condition);
			}
			else {
				$halfmoney = 0;
			}

			$otherallmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition);
			$data['allmoney'] = sprintf('%.2f', $rushmoney + $otherallmoney + $halfmoney);
			$rushpaymember = pdo_fetchall('SELECT distinct mid FROM ' . tablename('wlmerchant_rush_order') . $condition);
			$otherpaymember = pdo_fetchall('SELECT distinct mid FROM ' . tablename('wlmerchant_order') . $condition);

			if (empty($sid)) {
				$halfpaymember = pdo_fetchall('SELECT distinct mid FROM ' . tablename('wlmerchant_halfcard_record') . $condition);
			}
			else {
				$halfpaymember = 0;
			}

			$data['paymember'] = count($rushpaymember) + count($otherpaymember) + count($halfpaymember);
			$rushorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . $condition . ' AND status NOT IN (6,7)');
			$otherorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition . ' AND status NOT IN (6,7)');

			if (empty($sid)) {
				$halforder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . ' AND status = 1');
			}
			else {
				$halforder = 0;
			}

			$data['ordernum'] = $rushorder + $otherorder + $halforder;
			$rushyxmoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition . ' AND status NOT IN (6,7)');
			$otheryxmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND status NOT IN (6,7)');

			if (empty($sid)) {
				$halfyxmoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_halfcard_record') . $condition . ' AND status = 1');
			}
			else {
				$halfyxmoney = 0;
			}

			$data['orderyxmoney'] = sprintf('%.2f', $rushyxmoney + $otheryxmoney + $halfyxmoney);
			$rushreorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . $condition . ' AND status IN (6,7)');
			$otherreorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition . ' AND status IN (6,7)');
			$data['reordernum'] = $rushreorder + $otherreorder + $halfreorder;
			$rushremoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition . ' AND status IN (6,7)');
			$otherremoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND status IN (6,7)');
			$data['reordermoney'] = sprintf('%.2f', $rushremoney + $otherremoney);

			if (empty($sid)) {
				$data['halfmoney'] = $halfmoney;
				$data['pocketmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'pocket\'');
				$data['chargemoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'store\'');
				$data['dismoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'distribution\'');
				$data['recmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'member\'');
				if ($_W['wlsetting']['distribution']['seetstatus'] && is_agent()) {
					$data['allmoney'] = sprintf('%.2f', $data['allmoney'] - $data['dismoney']);
					$data['dismoney'] = 0;
				}
			}

			$data['rushmoney'] = $rushmoney;
			$data['fightmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'wlfightgroup\'');
			$data['couponmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'coupon\'');
			$data['grouponmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'groupon\'');
			$data['actmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'activity\'');
			$data['onlinemoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'halfcard\'');
			$data['bargainmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'bargain\'');

			foreach ($data as $key => &$va) {
				if (empty($va)) {
					$va = 0;
				}
			}

			exit(json_encode($data));
		}

		$list = array();

		while ($i < $lenth) {
			$li = array();
			$testday = date('d') - $i;
			$teststa = mktime(0, 0, 0, date('m'), $testday, date('Y'));
			$testend = mktime(23, 59, 59, date('m'), $testday, date('Y'));

			if (empty($sid)) {
				$li['half'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_halfcard_record') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend));
				$li['chagre'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND plugin = \'store\''));
				$li['dismoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND plugin = \'distribution\''));
				$li['recmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND plugin = \'member\''));
				$li['pocket'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND plugin = \'pocket\''));
			}
			else {
				$li['half'] = 0;
				$li['chagre'] = 0;
				$li['dismoney'] = 0;
				$li['recmoney'] = 0;
				$li['pocket'] = 0;
			}

			if ($_W['wlsetting']['distribution']['seetstatus'] && is_agent()) {
				$li['dismoney'] = 0;
			}

			$li['rush'] = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend));
			$li['fight'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND plugin = \'wlfightgroup\''));
			$li['groupon'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND plugin = \'groupon\''));
			$li['coupon'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND plugin = \'coupon\''));
			$li['activity'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND plugin = \'activity\''));
			$li['payonline'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND plugin = \'halfcard\''));
			$li['bargain'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND plugin = \'bargain\''));
			$rushre = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND status IN (6,7)'));
			$otherr = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend . ' AND status IN (6,7)'));
			$li['reordermoney'] = sprintf('%.2f', $rushre + $otherr);
			$rusho = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend));
			$othero = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend));
			$halfo = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_halfcard_record') . $daycon . (' AND paytime > ' . $teststa . ' AND paytime < ' . $testend));
			$li['ordernum'] = $rusho + $othero + $halfo;

			foreach ($li as $key => &$l) {
				if (empty($l)) {
					$l = 0;
				}
			}

			$li['date'] = date('Y-m-d', $testend);
			$li['allmoney'] = sprintf('%.2f', $li['bargain'] + $li['half'] + $li['pocket'] + $li['chagre'] + $li['dismoney'] + $li['recmoney'] + $li['rush'] + $li['fight'] + $li['groupon'] + $li['coupon'] + $li['activity'] + $li['payonline']);
			$list[] = $li;
			++$i;
		}

		foreach ($list as $key => $va) {
			foreach ($va as $k => $vv) {
				if ($k != 'date' && $k != 'reordermoney' && $k != 'ordernum' && $k != 'allmoney') {
					$imgdata = array();
					$imgdata['dates'] = $va['date'];

					switch ($k) {
					case 'half':
						$imgdata['name'] = '一卡通';
						break;

					case 'chagre':
						$imgdata['name'] = '付费入驻';
						break;

					case 'dismoney':
						$imgdata['name'] = '分销商申请';
						break;

					case 'recmoney':
						$imgdata['name'] = '余额充值';
						break;

					case 'pocket':
						$imgdata['name'] = '掌上信息';
						break;

					case 'rush':
						$imgdata['name'] = '抢购';
						break;

					case 'fight':
						$imgdata['name'] = '拼团';
						break;

					case 'groupon':
						$imgdata['name'] = '团购';
						break;

					case 'coupon':
						$imgdata['name'] = '卡券';
						break;

					case 'activity':
						$imgdata['name'] = '活动';
						break;

					case 'payonline':
						$imgdata['name'] = '买单';
						break;

					case 'bargain':
						$imgdata['name'] = '砍价';
						break;

					default:
						$imgdata['name'] = '未知插件';
						break;
					}

					$imgdata['money'] = (double) $vv;
					$imgdatas[] = $imgdata;
				}
			}
		}

		$imgdatas = json_encode($imgdatas);
		include wl_template('datacenter/stat_operate');
	}

	public function stat_store()
	{
		global $_W;
		global $_GPC;
		$store_where = is_agent() ? array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'enabled' => 1) : array('uniacid' => $_W['uniacid'], 'enabled' => 1);
		$stores = pdo_getall('wlmerchant_merchantdata', $store_where, array('id', 'storename'));
		$condition = is_agent() ? 'WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] : 'WHERE uniacid = ' . $_W['uniacid'];
		$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;

		if ($days == -1) {
			$starttime = strtotime(trim($_GPC['stat_day']['start']));
			$endtime = strtotime(trim($_GPC['stat_day']['end']));
			$lenth = ceil((time() - $starttime) / 86400);
			$i = ceil((time() - $endtime) / 86400);
			$lenth = $lenth - $i + 1;
		}
		else {
			$todaytime = strtotime(date('Y-m-d'));
			$starttime = strtotime('-' . $days . ' days', $todaytime);
			$endtime = $todaytime + 86399;
			$lenth = $days + 1;
			$i = 0;
		}

		$selstarttime = date('Y-m-d', $starttime);
		$selendtime = date('Y-m-d', $endtime);
		$condition .= ' AND paytime > ' . $starttime . ' AND paytime < ' . $endtime;

		if (intval($_GPC['sid'])) {
			$sid = intval($_GPC['sid']);
			$condition .= ' AND sid = ' . $sid;
		}

		$orderby = $_GPC['orderby'] ? $_GPC['orderby'] : 'money';

		if ($_W['isajax']) {
			$data['rushmoney'] = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition);
			$data['fightmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND  plugin = \'wlfightgroup\' ');
			$data['couponmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND  plugin = \'coupon\' ');
			$data['groupmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND  plugin = \'groupon\' ');
			$data['actmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND  plugin = \'activity\' ');
			$data['paymoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND  plugin = \'halfcard\' ');
			$data['barmoney'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . ' AND  plugin = \'bargain\' ');
			$data['rushorder'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . $condition);
			$data['fightorder'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'wlfightgroup\' ');
			$data['couponorder'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'coupon\' ');
			$data['grouponorder'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'groupon\' ');
			$data['actorder'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'activity\' ');
			$data['payorder'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'halfcard\' ');
			$data['barorder'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition . ' AND plugin = \'bargain\' ');

			foreach ($data as $key => &$da) {
				if (empty($da)) {
					$da = 0;
				}
			}

			$data['allmoney'] = sprintf('%.2f', $data['rushmoney'] + $data['fightmoney'] + $data['couponmoney'] + $data['groupmoney'] + $data['actmoney'] + $data['paymoney'] + $data['barmoney']);
			$data['allorder'] = $data['rushorder'] + $data['fightorder'] + $data['couponorder'] + $data['grouponorder'] + $data['actorder'] + $data['payorder'] + $data['barorder'];

			if (empty($sid)) {
				$rushstores = pdo_fetchall('select distinct sid from ' . tablename(PDO_NAME . 'rush_order') . $condition);
				$othertores = pdo_fetchall('select distinct sid from ' . tablename(PDO_NAME . 'order') . $condition . ' AND plugin != \'store\' ');
				$stores = array_merge($rushstores, $othertores);
				$storeids = array();

				foreach ($stores as $key => $va) {
					if ($va['sid']) {
						$storeids[] = $va['sid'];
					}
				}

				$storeids = array_unique($storeids);
				$storelist = array();

				foreach ($storeids as $key => $storeid) {
					$store = array();
					$store['name'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $storeid), 'storename');
					$rushmoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND sid = ' . $storeid));
					$othermoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . (' AND sid = ' . $storeid . ' AND plugin != \'store\' '));
					$store['money'] = sprintf('%.2f', $rushmoney + $othermoney);
					$store['moneyrate'] = sprintf('%.2f', $store['money'] / $data['allmoney'] * 100);
					$rushorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND sid = ' . $storeid));
					$otherorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition . (' AND sid = ' . $storeid . ' AND plugin != \'store\' '));
					$store['order'] = $rushorder + $otherorder;
					$store['orderrate'] = sprintf('%.2f', $store['order'] / $data['allorder'] * 100);
					$storelist[] = $store;
				}

				if ($orderby == 'money') {
					$storelist = Store::wl_sort($storelist, 'money', SORT_DESC, SORT_NUMERIC);

					foreach ($storelist as $key => $st) {
						$li = array('year' => $st['name'], '数据' => (double) $st['money']);
						$list[] = $li;
					}
				}
				else {
					$storelist = Store::wl_sort($storelist, 'order', SORT_DESC, SORT_NUMERIC);

					foreach ($storelist as $key => $st) {
						$li = array('year' => $st['name'], '数据' => (double) $st['order']);
						$list[] = $li;
					}
				}

				$data['list'] = $list;
				$data['storelist'] = $storelist;
			}
			else {
				$store = array();
				$store['name'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $sid), 'storename');
				$rushmoney = pdo_fetchcolumn('SELECT SUM(actualprice) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND sid = ' . $sid));
				$othermoney = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename('wlmerchant_order') . $condition . (' AND sid = ' . $sid . ' AND plugin != \'store\' '));
				$store['money'] = sprintf('%.2f', $rushmoney + $othermoney);
				$store['moneyrate'] = sprintf('%.2f', $store['money'] / $data['allmoney'] * 100);
				$rushorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_rush_order') . $condition . (' AND sid = ' . $sid));
				$otherorder = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . $condition . (' AND sid = ' . $sid . ' AND plugin != \'store\' '));
				$store['order'] = $rushorder + $otherorder;
				$store['orderrate'] = sprintf('%.2f', $store['order'] / $data['allorder'] * 100);
				$storelist[] = $store;

				if ($orderby == 'money') {
					$li = array('year' => $store['name'], '数据' => (double) $store['money']);
					$list[] = $li;
				}
				else {
					$li = array('year' => $store['name'], '数据' => (double) $store['order']);
					$list[] = $li;
				}

				$data['list'] = $list;
				$data['storelist'] = $storelist;
			}

			exit(json_encode($data));
		}

		include wl_template('datacenter/stat_store');
	}

	public function stat_distri()
	{
		global $_W;
		global $_GPC;
		$distributornum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND disflag IN (-1,1) '));
		$distributor = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_distributor') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disflag IN (1,-1) ORDER BY id DESC'));
		$condition = ' WHERE uniacid = ' . $_W['uniacid'];

		if ($_GPC['disid']) {
			$disid = $_GPC['disid'];
			$condition .= ' AND ( oneleadid = ' . $disid . ' or twoleadid = ' . $disid . ')';
		}

		$orderby = $_GPC['orderby'] ? $_GPC['orderby'] : 'money';
		$daycon = $condition;
		$days = isset($_GPC['days']) ? intval($_GPC['days']) : 0;

		if ($days == -1) {
			$starttime = strtotime(trim($_GPC['stat_day']['start']));
			$endtime = strtotime(trim($_GPC['stat_day']['end']));
			$lenth = ceil((time() - $starttime) / 86400);
			$i = ceil((time() - $endtime) / 86400);
			$lenth = $lenth - $i + 1;
		}
		else {
			$todaytime = strtotime(date('Y-m-d'));
			$starttime = strtotime('-' . $days . ' days', $todaytime);
			$endtime = $todaytime + 86399;
			$lenth = $days + 1;
			$i = 0;
		}

		$selstarttime = date('Y-m-d', $starttime);
		$selendtime = date('Y-m-d', $endtime);
		$condition .= ' AND createtime > ' . $starttime . ' AND createtime < ' . $endtime;

		if ($_W['isajax']) {
			$data = array('disnum' => 0, 'allmoney' => 0, 'dismoney' => 0, 'rushmoney' => 0, 'rushnum' => 0, 'grouponmoney' => 0, 'grouponnum' => 0, 'fightmoney' => 0, 'fightnum' => 0, 'couponmoney' => 0, 'couponnum' => 0, 'chargemoney' => 0, 'chargenum' => 0, 'halfmoney' => 0, 'halfnum' => 0, 'distrimoney' => 0, 'distrinum' => 0, 'pocketmoney' => 0, 'pocketnum' => 0);
			$disorders = pdo_fetchall('SELECT orderprice,leadmoney,plugin,oneleadid,twoleadid FROM ' . tablename('wlmerchant_disorder') . $condition);
			$data['disnum'] = count($disorders);
			$list = array();

			if ($disorders) {
				foreach ($disorders as $key => $dis) {
					$data['allmoney'] += $dis['orderprice'];
					$disleadmoney = unserialize($dis['leadmoney']);
					$leadmoney = $disleadmoney['one'];

					if ($orderby == 'money') {
						$list[$dis['oneleadid']] += $disleadmoney['one'];
					}
					else {
						$list[$dis['oneleadid']] += 1;
					}

					if ($disleadmoney['two']) {
						$leadmoney += $disleadmoney['two'];

						if ($orderby == 'money') {
							$list[$dis['twoleadid']] += $disleadmoney['two'];
						}
						else {
							if ($dis['twoleadid'] != $dis['oneleadid']) {
								$list[$dis['twoleadid']] += 1;
							}
						}
					}

					$data['dismoney'] += $leadmoney;

					if ($dis['plugin'] == 'rush') {
						$data['rushmoney'] += $leadmoney;
						$data['rushnum'] += 1;
					}
					else if ($dis['plugin'] == 'groupon') {
						$data['grouponmoney'] += $leadmoney;
						$data['grouponnum'] += 1;
					}
					else if ($dis['plugin'] == 'fightgroup') {
						$data['fightmoney'] += $leadmoney;
						$data['fightnum'] += 1;
					}
					else if ($dis['plugin'] == 'coupon') {
						$data['couponmoney'] += $leadmoney;
						$data['couponnum'] += 1;
					}
					else if ($dis['plugin'] == 'charge') {
						$data['chargemoney'] += $leadmoney;
						$data['chargenum'] += 1;
					}
					else if ($dis['plugin'] == 'halfcard') {
						$data['halfmoney'] += $leadmoney;
						$data['halfnum'] += 1;
					}
					else if ($dis['plugin'] == 'distribution') {
						$data['distrimoney'] += $leadmoney;
						$data['distrinum'] += 1;
					}
					else {
						if ($dis['plugin'] == 'pocket') {
							$data['pocketmoney'] += $leadmoney;
							$data['pocketnum'] += 1;
						}
					}
				}

				arsort($list);
				$newlist = array();
				$ii = 1;

				foreach ($list as $key => &$l) {
					$nickname = pdo_getcolumn(PDO_NAME . 'distributor', array('id' => $key), 'nickname');
					if (empty($nickname) || !$nickname) {
						$nickname = '未知' . $ii;
						++$ii;
					}

					$li = array('year' => $nickname, '数据' => (double) $l);
					$newlist[] = $li;
				}

				$newlist = array_slice($newlist, 0, 20);
			}

			$data['list'] = $newlist;
			exit(json_encode($data));
		}

		$list = array();

		while ($i < $lenth) {
			$li = array('ordernum' => 0, 'dismoney' => 0, 'rush' => 0, 'groupon' => 0, 'fight' => 0, 'coupon' => 0, 'charge' => 0, 'half' => 0, 'distri' => 0, 'pocket' => 0);
			$testday = date('d') - $i;
			$teststa = mktime(0, 0, 0, date('m'), $testday, date('Y'));
			$testend = mktime(23, 59, 59, date('m'), $testday, date('Y'));
			$disorders = pdo_fetchall('SELECT orderprice,leadmoney,plugin FROM ' . tablename('wlmerchant_disorder') . $daycon . (' AND createtime > ' . $teststa . ' AND createtime < ' . $testend));

			if ($disorders) {
				$li['ordernum'] = count($disorders);

				foreach ($disorders as $key => $dis) {
					$disleadmoney = unserialize($dis['leadmoney']);
					$leadmoney = $disleadmoney['one'];

					if ($disleadmoney['two']) {
						$leadmoney += $disleadmoney['two'];
					}

					$li['dismoney'] += $leadmoney;

					if ($dis['plugin'] == 'rush') {
						$li['rush'] += $leadmoney;
					}
					else if ($dis['plugin'] == 'groupon') {
						$li['groupon'] += $leadmoney;
					}
					else if ($dis['plugin'] == 'fightgroup') {
						$li['fight'] += $leadmoney;
					}
					else if ($dis['plugin'] == 'coupon') {
						$li['coupon'] += $leadmoney;
					}
					else if ($dis['plugin'] == 'charge') {
						$li['charge'] += $leadmoney;
					}
					else if ($dis['plugin'] == 'halfcard') {
						$li['half'] += $leadmoney;
					}
					else if ($dis['plugin'] == 'distribution') {
						$li['distri'] += $leadmoney;
					}
					else {
						if ($dis['plugin'] == 'pocket') {
							$li['pocket'] += $leadmoney;
						}
					}
				}
			}

			$li['date'] = date('Y-m-d', $testend);
			$list[] = $li;
			++$i;
		}

		include wl_template('datacenter/stat_distri');
	}

	public function stat_store_card()
	{
		global $_W;
		global $_GPC;
		$condition = is_agent() ? array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']) : array('uniacid' => $_W['uniacid']);
		$days = isset($_GPC['days']) ? intval($_GPC['days']) : 6;

		if ($days == -1) {
			$starttime = strtotime(trim($_GPC['stat_day']['start']));
			$endtime = strtotime(trim($_GPC['stat_day']['end']));
			$lenth = ceil((time() - $starttime) / 86400);
			$i = ceil((time() - $endtime) / 86400);
			$lenth = $lenth - $i + 1;
		}
		else {
			$todaytime = strtotime(date('Y-m-d'));
			$starttime = strtotime('-' . $days . ' days', $todaytime);
			$endtime = $todaytime + 86399;
			$lenth = $days + 1;
			$i = 0;
		}

		$daystext = $days == 6 ? '近7天' : ($days == 29 ? '近30天' : date('Y-m-d', $starttime) . '-' . date('Y-m-d', $endtime));
		$where = array('createtime >' => $starttime, 'createtime <' => $endtime);
		if (!is_agent() && $_W['ispost']) {
			$agents = pdo_getall('wlmerchant_agentusers', array('uniacid' => $_W['uniacid']), array('id', 'agentname'));

			foreach ($agents as &$agent) {
				$condition['aid'] = $agent['id'];
				$adata = $this->stat_sc_get($condition, $where, 2);
				$agent = array_merge($agent, $adata);
				$agent['areaname'] = pdo_getcolumn('wlmerchant_area', array('id' => pdo_getcolumn('wlmerchant_oparea', array('uniacid' => $_W['uniacid'], 'aid' => $agent['id']), 'areaid')), 'name');
				unset($adata);
			}

			exit(json_encode($agents));
		}

		$data = $this->stat_sc_get($condition, $where, 1);
		include wl_template('datacenter/stat_store_card');
	}

	private function stat_sc_get($condition, $where, $status = 1)
	{
		$data = array();
		$data['storenum'] = pdo_getcolumn('wlmerchant_merchantdata', array_merge($condition, array('enabled' => 1)), 'count(id)');
		$data['cardnum'] = pdo_getcolumn('wlmerchant_halfcardmember', array_merge($condition), 'count(id)');
		$data['storenew'] = pdo_getcolumn('wlmerchant_merchantdata', array_merge($condition, $where, array('enabled' => 1)), 'count(id)');
		$data['cardnew'] = pdo_getcolumn('wlmerchant_halfcardmember', array_merge($condition, $where), 'count(id)');

		if ($status == 1) {
			$data['cardnew0'] = pdo_getcolumn('wlmerchant_halfcardmember', array_merge($condition, $where, array('from' => 0)), 'count(id)');
			$data['cardnew1'] = pdo_getcolumn('wlmerchant_halfcardmember', array_merge($condition, $where, array('from' => 1)), 'count(id)');
			$data['cardnew2'] = pdo_getcolumn('wlmerchant_halfcardmember', array_merge($condition, $where, array('from' => 2)), 'count(id)');
			$data['cardnew3'] = pdo_getcolumn('wlmerchant_halfcardmember', array_merge($condition, $where, array('from' => 3)), 'count(id)');
		}

		return $data;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
