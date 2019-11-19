<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Disappbase_WeliamController extends Weliam_merchantModuleSite
{
	public function __construct()
	{
		global $_W;
		global $_GPC;
		$config = Setting::wlsetting_read('distribution');

		if ($_GPC['disflag']) {
			$rank = 2;
		}
		else {
			$rank = $_GPC['rank'] ? $_GPC['rank'] : 1;
		}

		if (!in_array($_W['method'], array('applyindex', 'applywait', 'applydis', 'topayorder'))) {
			$distributor = pdo_get('wlmerchant_distributor', array('id' => $_W['wlmember']['distributorid']));

			if ($rank == 2) {
				header('location:' . app_url('distribution/disappbase/applyindex', array('rank' => $rank)));
			}
			else if ($_GPC['qrentry']) {
				if ($config['qrcodeurlstatus'] == 1) {
					header('location:' . app_url('dashboard/home/index'));
				}
				else if ($config['qrcodeurlstatus'] == 2) {
					header('location:' . $config['qrcodeurl']);
				}
				else {
					header('location:' . app_url('distribution/disappbase/applyindex'));
				}
			}
			else if (empty($distributor['disflag'])) {
				header('location:' . app_url('distribution/disappbase/applyindex', array('rank' => $rank)));
			}
			else {
				if ($distributor['disflag'] == -1) {
					wl_message('您的分销商资格已过期，请续费会员', app_url('halfcard/halfcardopen/open'), 'error');
				}
			}
		}

		if ($config['share_title'] || $config['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];

			if ($config['share_title']) {
				$title = $config['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[系统名称]', $sysname, $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}
			else {
				$_W['wlsetting']['share']['share_title'] = !empty($config['share_title']) ? $config['share_title'] : $_W['wlsetting']['share']['share_title'];
			}

			if ($config['share_desc']) {
				$desc = $config['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[系统名称]', $sysname, $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
			else {
				$_W['wlsetting']['share']['share_desc'] = !empty($config['share_desc']) ? $config['share_desc'] : $_W['wlsetting']['share']['share_desc'];
			}
		}

		$_W['wlsetting']['share']['share_image'] = !empty($config['share_image']) ? $config['share_image'] : $_W['wlsetting']['share']['share_image'];
	}

	public function index()
	{
		global $_W;
		global $_GPC;
		$DistText = Distribution::getDistText();
		extract($DistText);
		$disset = Setting::wlsetting_read('distribution');
		$distributor = pdo_get('wlmerchant_distributor', array('id' => $_W['wlmember']['distributorid']));

		if ($disset['levelshow']) {
			$levelname = pdo_getcolumn(PDO_NAME . 'dislevel', array('id' => $distributor['dislevel']), 'name');

			if (empty($distributor['dislevel'])) {
				$levelname = pdo_getcolumn(PDO_NAME . 'dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'name');
			}
			else {
				$levelname = pdo_getcolumn(PDO_NAME . 'dislevel', array('id' => $distributor['dislevel']), 'name');
			}
		}

		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $fxtext . '中心 - ' . $_W['wlsetting']['base']['name'] : $fxtext . '中心';

		if ($distributor['leadid']) {
			$lead = pdo_get('wlmerchant_member', array('id' => $distributor['leadid']), array('nickname', 'realname'));

			if ($lead['nickname']) {
				$leadname = $lead['nickname'];
			}
			else {
				$leadname = $lead['realname'];
			}
		}

		$disid = $distributor['id'];
		$djsorder = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_disorder') . ('WHERE  (oneleadid = ' . $disid . ' OR twoleadid = ' . $disid . ' OR threeleadid = ' . $disid . ' ) AND status IN (0,1) '));
		$djsmoney = 0;

		if ($djsorder) {
			foreach ($djsorder as $key => $order) {
				$money = unserialize($order['leadmoney']);

				if ($order['oneleadid'] == $disid) {
					$djsmoney += $money['one'];
				}
				else if ($order['twoleadid'] == $disid) {
					$djsmoney += $money['two'];
				}
				else {
					if ($order['threeleadid'] == $disid) {
						$djsmoney += $money['three'];
					}
				}
			}
		}

		$alldismoney = number_format($distributor['dismoney'] + $djsmoney, 2);

		if ($disset['mode']) {
			if ($distributor['leadid'] == -1 && $disset['communqrcode']) {
				$community['qrcode'] = tomedia($disset['communqrcode']);
				$community['name'] = $disset['communname'];
				$community['desc'] = $disset['commundesc'];
				$community['img'] = $disset['communimg'];
				$community['img'] = tomedia($community['img']);
			}
			else {
				if ($disset['twocommunqrcode']) {
					$community['qrcode'] = tomedia($disset['twocommunqrcode']);
					$community['name'] = $disset['twocommunname'];
					$community['desc'] = $disset['twocommundesc'];
					$community['img'] = $disset['twocommunimg'];
					$community['img'] = tomedia($community['img']);
				}
			}
		}
		else {
			if ($disset['communqrcode']) {
				$community['qrcode'] = tomedia($disset['communqrcode']);
				$community['name'] = $disset['communname'];
				$community['desc'] = $disset['commundesc'];
				$community['img'] = $disset['communimg'];
				$community['img'] = tomedia($community['img']);
			}
		}

		$ordernum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_disorder') . ('WHERE uniacid = ' . $_W['uniacid'] . '  AND (oneleadid = ' . $disid . ' or twoleadid = ' . $disid . ' or threeleadid = ' . $disid . ')'));
		$applynum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename(PDO_NAME . 'settlement_record') . ('WHERE disid = ' . $_W['wlmember']['distributorid'] . ' AND `type` = 3'));

		if ($_W['wlsetting']['distribution']['showlock']) {
			$onenum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $_W['wlmember']['id']));
			$twonum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . ' WHERE leadid in (select mid from ' . tablename('wlmerchant_distributor') . (' where `leadid` = ' . $_W['wlmember']['id'] . ')'));
		}
		else {
			$onenum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $_W['wlmember']['id'] . ' AND lockflag = 0'));
			$twonum = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename('wlmerchant_distributor') . ' WHERE leadid in (select mid from ' . tablename('wlmerchant_distributor') . (' where `leadid` = ' . $_W['wlmember']['id'] . ') AND lockflag = 0'));
		}

		include wl_template('disapp/index');
	}

	public function recommendgoods()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '推荐商品 - ' . $_W['wlsetting']['base']['name'] : '推荐商品';
		$DistText = Distribution::getDistText();
		extract($DistText);
		$disset = Setting::wlsetting_read('distribution');
		$distributor = pdo_get('wlmerchant_distributor', array('id' => $_W['wlmember']['distributorid']));

		if ($distributor['dislevel']) {
			$plugin = pdo_getcolumn('wlmerchant_dislevel', array('id' => $distributor['dislevel']), 'plugin');
		}
		else {
			$plugin = pdo_getcolumn('wlmerchant_dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'plugin');
		}

		$plugin = unserialize($plugin);
		$sort = array();
		$sort[] = array('img' => $disset['zkimg'], 'type' => 4, 'status' => $disset['zkstatus'], 'yhnaem' => '团购', 'updname' => $disset['zkname']);
		$sort[] = array('img' => $disset['djimg'], 'type' => 1, 'status' => $disset['djstatus'], 'yhnaem' => '抢购', 'updname' => $disset['djname']);
		$sort[] = array('img' => $disset['tcimg'], 'type' => 2, 'status' => $disset['tcstatus'], 'yhnaem' => '拼团', 'updname' => $disset['tcname']);
		$sort[] = array('img' => $disset['tgimg'], 'type' => 3, 'status' => $disset['tgstatus'], 'yhnaem' => '卡卷', 'updname' => $disset['tgname']);
		$sort[] = array('img' => $disset['yhimg'], 'type' => 5, 'status' => $disset['yhstatus'], 'yhnaem' => '积分', 'updname' => $disset['yhname']);
		$sort[] = array('img' => $disset['kjimg'], 'type' => 6, 'status' => $disset['kjstatus'], 'kjnaem' => '砍价', 'updname' => $disset['kjname']);
		$i = 0;

		while ($i < count($sort) - 1) {
			$j = 0;

			while ($j < count($sort) - 1 - $i) {
				if ($sort[$j]['img'] < $sort[$j + 1]['img']) {
					$temp = $sort[$j + 1];
					$sort[$j + 1] = $sort[$j];
					$sort[$j] = $temp;
				}

				++$j;
			}

			++$i;
		}

		include wl_template('disapp/recommendgoods');
	}

	public function getGoods()
	{
		global $_W;
		global $_GPC;
		$where = array('aid' => $_W['aid'], 'status' => 1, 'isdistri' => 0);
		$pindex = $_GPC['pindex'] ? $_GPC['pindex'] : 1;
		$psize = $_GPC['psize'] ? $_GPC['psize'] : 10;
		$type = $_GPC['cateid'] ? $_GPC['cateid'] : 1;
		$dislevelid = pdo_getcolumn(PDO_NAME . 'distributor', array('id' => $_W['wlmember']['distributorid']), 'dislevel');

		if (empty($dislevelid)) {
			$dislevelid = pdo_getcolumn('wlmerchant_dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'id');
		}

		$onecom = pdo_getcolumn(PDO_NAME . 'dislevel', array('id' => $dislevelid), 'onecommission');

		if ($type == 1) {
			$where['status'] = 2;
			$active = Rush::getNumActive('*', $where, 'sort DESC', $pindex, $psize, 1);
			$active = $active[0];

			foreach ($active as $key => &$rush) {
				$rush['thumb'] = tomedia($rush['thumb']);
				$rush['a'] = app_url('rush/home/detail', array('id' => $rush['id']));

				if ($rush['optionstatus']) {
					$option = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_goods_option') . ('WHERE type = 1 AND goodsid = ' . $rush['id'] . ' ORDER BY onedismoney DESC'));
					$option = $option[0];
					$rush['onedismoney'] = $option['onedismoney'];
					$rush['price'] = $option['price'];
				}

				if (!(0 < $rush['onedismoney'])) {
					$rush['onedismoney'] = number_format($rush['price'] * $onecom / 100, 2);
				}
			}
		}
		else if ($type == 2) {
			$active = Util::getNumData('*', PDO_NAME . 'fightgroup_goods', $where, 'listorder DESC', $pindex, $psize, 1);
			$active = $active[0];

			foreach ($active as $key => &$fight) {
				$fight['thumb'] = tomedia($fight['logo']);
				$fight['levelnum'] = $fight['stock'];
				$fight['a'] = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $fight['id']));

				if ($fight['optionstatus']) {
					$option = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_goods_option') . ('WHERE type = 2 AND goodsid = ' . $fight['id'] . ' ORDER BY onedismoney DESC'));
					$option = $option[0];
					$fight['onedismoney'] = $option['onedismoney'];
					$fight['price'] = $option['price'];
				}

				if (!(0 < $fight['onedismoney'])) {
					$fight['onedismoney'] = number_format($fight['price'] * $onecom / 100, 2);
				}
			}
		}
		else if ($type == 3) {
			$where['is_charge'] = 1;
			$active = Util::getNumData('*', PDO_NAME . 'couponlist', $where, 'indexorder DESC', $pindex, $psize, 1);
			$active = $active[0];

			foreach ($active as $key => &$cou) {
				$cou['thumb'] = tomedia($cou['logo']);
				$cou['name'] = $cou['title'];
				$cou['levelnum'] = $cou['quantity'] - $cou['surplus'];
				$cou['a'] = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $cou['id']));

				if (!(0 < $cou['onedismoney'])) {
					$cou['onedismoney'] = number_format($cou['price'] * $onecom / 100, 2);
				}
			}
		}
		else if ($type == 4) {
			$where['status'] = 2;
			$active = Groupon::getNumActive('*', $where, 'sort DESC', $pindex, $psize, 1);
			$active = $active[0];

			foreach ($active as $key => &$group) {
				$group['thumb'] = tomedia($group['thumb']);
				$group['a'] = app_url('groupon/grouponapp/groupondetail', array('cid' => $group['id']));

				if ($group['optionstatus']) {
					$option = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_goods_option') . ('WHERE type = 3 AND goodsid = ' . $group['id'] . ' ORDER BY onedismoney DESC'));
					$option = $option[0];
					$group['onedismoney'] = $option['onedismoney'];
					$group['price'] = $option['price'];
				}

				if (!(0 < $group['onedismoney'])) {
					$group['onedismoney'] = number_format($group['price'] * $onecom / 100, 2);
				}
			}
		}
		else if ($type == 5) {
			$active = pdo_getall(PDO_NAME . 'consumption_goods', array('status' => 1, 'uniacid' => $_W['uniacid']), '', '', 'displayorder DESC', array($pindex, $psize));

			foreach ($active as $key => &$group) {
				$group['thumb'] = tomedia($group['thumb']);
				$group['a'] = app_url('consumption/goods/goods_detail', array('id' => $group['id']));
				$group['price'] = $group['use_credit2'];
				$group['name'] = $group['title'];

				if (!(0 < $group['onedismoney'])) {
					$group['onedismoney'] = number_format($group['price'] * $onecom / 100, 2);
				}
			}
		}
		else {
			if ($type == 6) {
				$active = pdo_getall(PDO_NAME . 'bargain_activity', array('status' => 2, 'uniacid' => $_W['uniacid']), '', '', 'sort DESC', array($pindex, $psize));

				foreach ($active as $key => &$bar) {
					$bar['thumb'] = tomedia($bar['thumb']);
					$bar['a'] = app_url('bargain/bargain_app/bargaindetail', array('cid' => $bar['id']));
					$bar['price'] = $bar['price'];
					$bar['name'] = $bar['name'];

					if (!(0 < $bar['onedismoney'])) {
						$bar['onedismoney'] = number_format($bar['price'] * $onecom / 100, 2);
					}
				}
			}
		}

		exit(json_encode($active));
	}

	public function apply()
	{
		global $_W;
		global $_GPC;
		$DistText = Distribution::getDistText();
		extract($DistText);
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $fxtext . '提现 - ' . $_W['wlsetting']['base']['name'] : $fxtext . '提现';
		$distributor = pdo_get('wlmerchant_distributor', array('id' => $_W['wlmember']['distributorid']));
		$distributor['succmoney'] = $distributor['ingmoney'] = 0;
		$applys = pdo_getall(PDO_NAME . 'settlement_record', array('disid' => $distributor['id'], 'type' => 3));

		if ($applys) {
			foreach ($applys as $key => $v) {
				if ($v['status'] == 6 || $v['status'] == 7 || $v['status'] == 8) {
					$distributor['ingmoney'] += $v['sgetmoney'];
				}
				else {
					if ($v['status'] == 9) {
						$distributor['succmoney'] += $v['sgetmoney'];
					}
				}
			}
		}

		$distributor['ingmoney'] = number_format($distributor['ingmoney'], 2);
		$distributor['succmoney'] = number_format($distributor['succmoney'], 2);

		if ($_GPC['type'] == 'deling') {
			$record = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'settlement_record') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disid = ' . $distributor['id'] . ' AND status IN (3,6,7,8) AND mid = ' . $_W['mid'] . ' AND `type` = 3 ORDER BY applytime DESC'));
		}

		if ($_GPC['type'] == 'finish') {
			$record = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'settlement_record') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disid = ' . $distributor['id'] . ' AND status = 9 AND mid = ' . $_W['mid'] . ' AND `type` = 3 ORDER BY applytime DESC'));
		}

		if ($_GPC['type'] == 'reject') {
			$record = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'settlement_record') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disid = ' . $distributor['id'] . ' AND status IN (-1,10,11) AND `type` = 3 AND mid = ' . $_W['mid'] . ' ORDER BY applytime DESC'));
		}

		$payment = Setting::wlsetting_read('distribution');
		$payment = $payment['payment_type'];
		include wl_template('disapp/apply');
	}

	public function applying()
	{
		global $_W;
		global $_GPC;
		$agent = Area::getSingleAgent($_W['aid']);
		$appmoney = $_GPC['money'];
		$payment_type = $_GPC['payment_type'];

		if (empty($payment_type)) {
			wl_json(1, '请选择收款方式');
		}

		$storeInfo = Member::getMemberByMid($_W['wlmember']['id']);
		if ($payment_type == 1 && empty($storeInfo['alipay'])) {
			wl_json(1, '请先前往个人中心完善支付宝账号信息');
		}
		else {
			if ($payment_type == 3 && (empty($storeInfo['card_number']) || empty($storeInfo['bank_name']))) {
				wl_json(1, '请先前往个人中心完善银行账号信息');
			}
		}

		$disset = Setting::wlsetting_read('distribution');
		$cashsets = Setting::wlsetting_read('cashset');
		$distributor = pdo_get('wlmerchant_distributor', array('id' => $_W['wlmember']['distributorid']));

		if ($appmoney < $disset['lowestmoney']) {
			wl_json(1, '提现金额小于最低提现佣金' . $disset['lowestmoney'] . '元');
		}

		if ($distributor['nowmoney'] < $appmoney) {
			wl_json(1, '可提现金额不足');
		}

		if ($disset['frequency']) {
			$lastapp = pdo_fetch('SELECT * FROM ' . tablename(PDO_NAME . 'settlement_record') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $_W['wlmember']['id'] . ' AND `type` = 3 ORDER BY applytime DESC'));
			$limittime = $lastapp['applytime'] + $disset['frequency'] * 24 * 3600;

			if (time() < $limittime) {
				wl_json(1, '您在' . $disset['frequency'] . '天内有提现申请，请稍后再试');
			}
		}

		$nowmoney = $distributor['nowmoney'] - $appmoney;
		$res1 = pdo_update('wlmerchant_distributor', array('nowmoney' => $nowmoney), array('id' => $distributor['id']));

		if ($res1) {
			$money = $appmoney - $appmoney * $disset['withdrawcharge'] / 100;
			$money = sprintf('%.2f', $money);
			$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'status' => 7, 'type' => 3, 'mid' => $_W['wlmember']['id'], 'sopenid' => $_W['wlmember']['openid'], 'disid' => $distributor['id'], 'sgetmoney' => $money, 'sapplymoney' => $appmoney, 'spercentmoney' => sprintf('%.2f', $appmoney - $money), 'spercent' => sprintf('%.4f', ($appmoney - $money) / $appmoney * 100), 'applytime' => time(), 'payment_type' => $payment_type);

			if ($cashsets['disnoaudit']) {
				$data['status'] = 3;
				$trade_no = time() . random(4, true);
				$data['trade_no'] = $trade_no;
				$data['updatetime'] = time();
			}

			$res2 = pdo_insert(PDO_NAME . 'settlement_record', $data);
			$disorderid = pdo_insertid();

			if ($res2) {
				if ($cashsets['disautocash'] && ($data['payment_type'] == 2 || $data['payment_type'] == 4)) {
					Queue::addTask(4, $disorderid, time(), $disorderid);
				}

				$url = app_url('distribution/disappbase/apply', array('type' => 'deling'));
				Distribution::distriNotice($_W['wlmember']['openid'], $url, 3, 0, $appmoney);
				Distribution::adddisdetail($disorderid, $_W['wlmember']['id'], $_W['wlmember']['id'], 2, $appmoney, 'cash', 1, '分销佣金提现', $nowmoney);
				$first = '您好，有一个分销提现申请待审核。';
				$keyword1 = '分销用户[' . $_W['wlmember']['nickname'] . ']申请提现' . $appmoney . '元';
				$keyword2 = '待审核';
				$remark = '请尽快前往系统后台审核';
				Message::jobNotice($_W['wlsetting']['noticeMessage']['adminopenid'], $first, $keyword1, $keyword2, $remark, '');
				wl_json(0, '申请成功！');
			}
			else {
				wl_json(1, '申请失败，请联系管理员！');
			}
		}
		else {
			wl_json(1, '更新个人数据失败，请联系管理员！');
		}
	}

	public function lowpeople()
	{
		global $_W;
		global $_GPC;
		$DistText = Distribution::getDistText();
		extract($DistText);
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '我的' . $xxtext . ' - ' . $_W['wlsetting']['base']['name'] : '我的' . $xxtext;

		if ($_W['wlsetting']['distribution']['showlock']) {
			$num = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $_W['wlmember']['id'] . '  '));
			$fxsnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $_W['wlmember']['id'] . ' and disflag = 1 '));
			$todaynum = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = \'' . $_W['wlmember']['id'] . '\' and createtime >= ') . strtotime(date('Y-m-d')));
		}
		else {
			$num = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $_W['wlmember']['id'] . ' AND lockflag = 0 '));
			$fxsnum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $_W['wlmember']['id'] . ' and disflag = 1 AND lockflag = 0'));
			$todaynum = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = \'' . $_W['wlmember']['id'] . '\' and lockflag = 0 and createtime >= ') . strtotime(date('Y-m-d')));
		}

		include wl_template('disapp/lowpeople');
	}

	public function getlowpeople()
	{
		global $_W;
		global $_GPC;
		$disset = Setting::wlsetting_read('distribution');
		$pindex = $_GPC['pindex'] ? $_GPC['pindex'] : 1;
		$psize = 10;
		$disid = pdo_getcolumn(PDO_NAME . 'distributor', array('mid' => $_W['mid']), 'id');
		$where = $_GPC['level'] == 'two' ? 'a.leadid in (select mid from ' . tablename('wlmerchant_distributor') . (' where `leadid` = ' . $_W['mid'] . ')') : 'a.leadid = ' . $_W['mid'];

		if (!$disset['showlock']) {
			$where .= ' AND a.lockflag = 0 ';
		}

		if ($_GPC['sear']) {
			$where .= ' AND b.nickname LIKE \'%' . trim($_GPC['sear']) . '%\' ';
		}

		$lowpeople = pdo_fetchall('select a.`mid`,a.`lockflag`,a.`dislevel`,a.`disflag`,b.`nickname`,b.`mobile`,b.`avatar` from ' . tablename('wlmerchant_distributor') . ' a LEFT JOIN ' . tablename('wlmerchant_member') . (' b on a.`mid` = b.`id` where ' . $where . ' ORDER BY a.id DESC LIMIT ') . ($pindex - 1) * $psize . ',' . $psize);

		if ($lowpeople) {
			foreach ($lowpeople as $key => &$li) {
				$li['avatar'] = tomedia($li['avatar']);
				$li['levelname'] = $li['disflag'] == 1 ? (empty($li['dislevel']) ? pdo_getcolumn('wlmerchant_dislevel', array('isdefault' => 1), 'name') : pdo_getcolumn('wlmerchant_dislevel', array('id' => $li['dislevel']), 'name')) : '普通会员';

				if (!empty($li['mobile'])) {
					$li['mobile'] = substr($li['mobile'], 0, 3) . '****' . substr($li['mobile'], -4, 4);
				}
					$li['nickname'] = substr($li['nickname'], 0, 3) . '*****';
				if ($disset['showlock']) {
					$li['lownum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $li['mid']));
				}
				else {
					$li['lownum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $li['mid'] . ' AND lockflag = 0'));
				}

				$dismoney = 0;
				$disorders = pdo_getall('wlmerchant_disorder', array('buymid' => $li['mid'], 'oneleadid' => $disid), array('leadmoney'));

				if ($disorders) {
					foreach ($disorders as $key => $v) {
						$leadmoney = unserialize($v['leadmoney']);
						$dismoney += $leadmoney['one'];
					}
				}

				$sql = 'SELECT sum(price) FROM' . tablename('wlmerchant_disdetail') . (' WHERE leadid = \'' . $_W['wlmember']['id'] . '\' AND buymid = \'' . $li['mid'] . '\' AND plugin = \'system\'');
				$apifee = pdo_fetchcolumn($sql);
				$dismoney += floatval($apifee);
				$li['dismoney'] = sprintf('%.2f', $dismoney);
			}
		}

		exit(json_encode($lowpeople));
	}

	public function lowteam()
	{
		global $_W;
		global $_GPC;
		$DistText = Distribution::getDistText();
		extract($DistText);
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '我的团队 - ' . $_W['wlsetting']['base']['name'] : '我的团队';
		$level = !empty($_GPC['level']) ? trim($_GPC['level']) : 'one';

		if ($_W['wlsetting']['distribution']['showlock']) {
			$onenum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $_W['wlmember']['id']));
			$twonum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . ' WHERE leadid in (select mid from ' . tablename('wlmerchant_distributor') . (' where `leadid` = ' . $_W['wlmember']['id'] . ')'));
		}
		else {
			$onenum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $_W['wlmember']['id'] . ' AND lockflag = 0'));
			$twonum = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename('wlmerchant_distributor') . ' WHERE leadid in (select mid from ' . tablename('wlmerchant_distributor') . (' where `leadid` = ' . $_W['wlmember']['id'] . ') AND lockflag = 0'));
		}

		include wl_template('disapp/lowteam');
	}

	public function moneylist()
	{
		global $_W;
		global $_GPC;
		$DistText = Distribution::getDistText();
		extract($DistText);
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $fxtext . $yjtext . ' - ' . $_W['wlsetting']['base']['name'] : $fxtext . $yjtext;
		$distributor = pdo_get('wlmerchant_distributor', array('id' => $_W['wlmember']['distributorid']));
		$applymoney = $cashmoney = $successmoney = 0;
		$applys = pdo_getall(PDO_NAME . 'settlement_record', array('mid' => $_W['wlmember']['id'], 'type' => 3));

		if ($applys) {
			foreach ($applys as $key => $v) {
				if ($v['status'] == 6 || $v['status'] == 7) {
					$applymoney += $v['sgetmoney'];
				}
				else if ($v['status'] == 8) {
					$cashmoney += $v['sgetmoney'];
				}
				else {
					if ($v['status'] == 9) {
						$successmoney += $v['sgetmoney'];
					}
				}
			}
		}

		$applymoney = number_format($applymoney, 2);
		$cashmoney = number_format($cashmoney, 2);
		$successmoney = number_format($successmoney, 2);
		include wl_template('disapp/moneylist');
	}

	public function disorder()
	{
		global $_W;
		global $_GPC;
		$DistText = Distribution::getDistText();
		extract($DistText);
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $yjtext . '明细 - ' . $_W['wlsetting']['base']['name'] : $yjtext . '明细';
		$type = $_GPC['type'] ? $_GPC['type'] : 'pay';
		include wl_template('disapp/disorder');
	}

	public function getdisorder()
	{
		global $_W;
		global $_GPC;
		$disset = Setting::wlsetting_read('distribution');
		$pindex = $_GPC['pindex'] ? $_GPC['pindex'] : 1;
		$type = $_GPC['type'];
		$disid = pdo_getcolumn('wlmerchant_distributor', array('mid' => $_W['wlmember']['id']), 'id');
		$where = ' uniacid = ' . $_W['uniacid'] . ' ';

		if ($type == 'pay') {
			$where .= ' AND status = 2';
		}
		else if ($type == 'over') {
			$where .= ' AND status IN (0,1)';
		}
		else {
			if ($type == 'refund') {
				$where .= ' AND status = 3';
			}
		}

		$myorder = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_disorder') . ('WHERE ' . $where . ' AND (oneleadid = ' . $disid . ' or twoleadid = ' . $disid . ' or threeleadid = ' . $disid . ') ORDER BY id DESC LIMIT ') . ($pindex - 1) * 8 . ',8');

		if ($myorder) {
			foreach ($myorder as $key => &$order) {
				$buymember = pdo_get('wlmerchant_member', array('id' => $order['buymid']), array('nickname', 'avatar'));
				$order['nickname'] = $buymember['nickname'];

				if ($order['plugin'] == 'rush') {
					$rush = pdo_get('wlmerchant_rush_order', array('id' => $order['orderid']));
					$goods = Rush::getSingleActive($rush['activityid'], 'thumb,name,id');
					$order['gimg'] = tomedia($goods['thumb']);
					$order['name'] = $goods['name'];
					$order['ga'] = app_url('rush/home/detail', array('id' => $goods['id']));
					$order['pluginname'] = '抢购';
				}
				else if ($order['plugin'] == 'groupon') {
					$groupon = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$goods = Groupon::getSingleActive($groupon['fkid'], 'thumb,name,id');
					$order['gimg'] = tomedia($goods['thumb']);
					$order['name'] = $goods['name'];
					$order['ga'] = app_url('groupon/grouponapp/groupondetail', array('cid' => $goods['id']));
					$order['pluginname'] = '团购';
				}
				else if ($order['plugin'] == 'fightgroup') {
					$fightgroup = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$goods = Wlfightgroup::getSingleGood($fightgroup['fkid'], 'name,logo,id');
					$order['gimg'] = tomedia($goods['logo']);
					$order['name'] = $goods['name'];
					$order['ga'] = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $goods['id']));
					$order['pluginname'] = '拼团';
				}
				else if ($order['plugin'] == 'coupon') {
					$coupon = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$goods = wlCoupon::getSingleCoupons($coupon['fkid'], 'title,logo,id');
					$order['gimg'] = tomedia($goods['logo']);
					$order['name'] = $goods['title'];
					$order['ga'] = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $goods['id']));
					$order['pluginname'] = '超级券';
				}
				else if ($order['plugin'] == 'pocket') {
					$pocket = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$goods = pdo_get('wlmerchant_pocket_informations', array('id' => $pocket['fkid']), array('share_title', 'type'));
					$type = pdo_get('wlmerchant_pocket_type', array('id' => $goods['type']), array('title', 'img'));
					$order['gimg'] = tomedia($type['img']);

					if ($goods['share_title']) {
						$order['name'] = $goods['share_title'];
					}
					else {
						$order['name'] = $type['title'];
					}

					$order['ga'] = app_url('pocket/pocket/detail', array('id' => $pocket['fkid']));
					$order['pluginname'] = '掌上信息';
				}
				else if ($order['plugin'] == 'halfcard') {
					$halforder = pdo_get('wlmerchant_halfcard_record', array('id' => $order['orderid']));
					$goods = pdo_get('wlmerchant_halfcard_type', array('id' => $halforder['typeid']), array('name', 'days'));
					$order['gimg'] = tomedia($buymember['avatar']);
					$order['name'] = $goods['name'];
					$order['ga'] = app_url('halfcard/halfcard_app/userhalfcard');
					$order['pluginname'] = '一卡通';
				}
				else if ($order['plugin'] == 'charge') {
					$chargeorder = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$goods = pdo_get('wlmerchant_merchantdata', array('id' => $chargeorder['sid']), array('storename', 'logo'));
					$order['gimg'] = tomedia($goods['logo']);
					$order['name'] = $goods['storename'];
					$order['ga'] = app_url('store/merchant/detail', array('id' => $chargeorder['sid']));
					$order['pluginname'] = '商户入驻';
				}
				else if ($order['plugin'] == 'distribution') {
					$chargeorder = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$avatar = pdo_getcolumn(PDO_NAME . 'member', array('id' => $order['buymid']), 'avatar');
					$order['gimg'] = tomedia($avatar);
					$order['name'] = '付费成为分销商';
					$order['ga'] = 'javascript:;';
					$order['pluginname'] = '分销商申请';
				}
				else if ($order['plugin'] == 'consumption') {
					$chargeorder = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$goods = pdo_get(PDO_NAME . 'consumption_goods', array('id' => $chargeorder['fkid']), array('thumb', 'title'));
					$order['gimg'] = tomedia($goods['thumb']);
					$order['name'] = $goods['title'];
					$order['ga'] = app_url('consumption/goods/goods_detail', array('id' => $chargeorder['fkid']));
					$order['pluginname'] = '积分商城';
				}
				else if ($order['plugin'] == 'payonline') {
					$chargeorder = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$goods = pdo_get(PDO_NAME . 'halfcardlist', array('id' => $chargeorder['fkid']), array('title'));
					$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $chargeorder['sid']), array('storename', 'logo'));

					if (empty($goods['title'])) {
						$goods['title'] = $merchant['storename'];
					}

					$order['gimg'] = tomedia($merchant['logo']);
					$order['name'] = $goods['title'] . '在线买单';
					$order['ga'] = app_url('halfcard/halfcard_app/halfcarddetail', array('id' => $chargeorder['fkid']));
					$order['pluginname'] = '在线买单';
				}
				else {
					if ($order['plugin'] == 'bargain') {
						$chargeorder = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
						$goods = pdo_get(PDO_NAME . 'bargain_activity', array('id' => $chargeorder['fkid']), array('name', 'unit', 'thumb'));
						$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $chargeorder['sid']), array('storename', 'logo'));
						$order['gimg'] = tomedia($goods['thumb']);
						$order['name'] = $goods['name'];
						$order['ga'] = app_url('bargain/bargain_app/bargaindetail', array('cid' => $chargeorder['fkid']));
						$order['pluginname'] = '砍价';
					}
				}

				$leadmoney = unserialize($order['leadmoney']);
				$order['date'] = date('Y-m-d H:i:s', $order['createtime']);

				if ($disset['mode']) {
					if ($order['oneleadid'] == $disid) {
						$order['ranklevel'] = '1';

						if ($order['twoleadid'] == $disid) {
							$order['leadmoney'] = $leadmoney['one'] + $leadmoney['two'];
						}
						else {
							$order['leadmoney'] = $leadmoney['one'];
						}
					}
					else {
						if ($order['twoleadid'] == $disid) {
							$order['ranklevel'] = '2';
							$order['leadmoney'] = $leadmoney['two'];
						}
					}
				}
				else if ($order['oneleadid'] == $disid) {
					$order['ranklevel'] = '1';
					$order['leadmoney'] = $leadmoney['one'];
				}
				else if ($order['twoleadid'] == $disid) {
					$order['ranklevel'] = '2';
					$order['leadmoney'] = $leadmoney['two'];
				}
				else {
					if ($order['threeleadid'] == $disid) {
						$order['ranklevel'] = '3';
						$order['leadmoney'] = $leadmoney['three'];
					}
				}
			}
		}

		exit(json_encode(array('errno' => 0, 'list' => $myorder)));
	}

	public function applyindex()
	{
		global $_W;
		global $_GPC;
		$DistText = Distribution::getDistText();
		extract($DistText);

		if (pdo_getcolumn('wlmerchant_distributor', array('mid' => $_W['mid']), 'disflag')) {
			header('location:' . app_url('distribution/disappbase/index'));
		}

		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '申请' . $fxstext . ' - ' . $_W['wlsetting']['base']['name'] : '申请' . $fxstext;
		$goflag = $_GPC['goflag'] ? $_GPC['goflag'] : 0;
		$distritor = pdo_get('wlmerchant_distributor', array('mid' => $_W['mid']));
		$realname = $distritor['realname'] ? $distritor['realname'] : $_W['wlmember']['realname'];
		$mobile = $distritor['mobile'] ? $distritor['mobile'] : $_W['wlmember']['mobile'];

		if ($_GPC['reflag']) {
			pdo_delete('wlmerchant_applydistributor', array('mid' => $_W['mid']));
		}

		$applymoney = 0;
		$base = Setting::wlsetting_read('distribution');

		if (empty($base['switch'])) {
			wl_message('系统已关闭，如有疑问请联系管理员', 'close', 'error');
		}

		$rank = $_GPC['rank'] ? $_GPC['rank'] : 1;
		if ($base['mode'] && $rank == 2) {
			if ($base['twoappdis'] == 3) {
				$applymoney = $base['twoapplymoney'];
			}
			else {
				if (empty($base['twoappdis'])) {
					wl_message('二级' . $fxstext . '已关闭申请', 'close', 'error');
				}
			}
		}
		else {
			if ($base['mode'] && $rank == 1) {
				if ($base['appdis'] == 3) {
					$applymoney = $base['applymoney'];
				}
				else {
					if (empty($base['appdis'])) {
						wl_message('一级' . $fxstext . '已关闭申请', 'close', 'error');
					}
				}
			}
			else if ($base['appdis'] == 3) {
				$applymoney = $base['applymoney'];
			}
			else {
				if (empty($base['appdis'])) {
					wl_message($fxstext . '已关闭申请', 'close', 'error');
				}
			}
		}

		$flag = pdo_get('wlmerchant_applydistributor', array('mid' => $_W['mid']), array('id', 'status'));

		if ($flag) {
			if ($flag['status'] == 1 && empty($distritor['disflag'])) {
				pdo_delete('wlmerchant_applydistributor', array('mid' => $_W['mid']));
			}
			else {
				header('location:' . app_url('distribution/disappbase/applywait'));
			}
		}

		if ($base['rollstatus'] == 1) {
			$rolllist = pdo_fetchall('SELECT mid,sapplymoney FROM ' . tablename(PDO_NAME . 'settlement_record') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND `type` = 3 ORDER BY applytime DESC limit 20'));

			foreach ($rolllist as $key => &$va) {
				$va['nickname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $va['mid']), 'nickname');
				$va['nickname'] = mb_substr($va['nickname'], 0, 1, 'utf-8') . '***' . mb_substr($va['nickname'], -1, 1, 'utf-8');
				$va['applymoney'] = $va['sapplymoney'];
			}
		}
		else {
			if ($base['rollstatus'] == 2) {
				$rolllist = array();
				$rolllist[] = array('nickname' => 'A***E', 'applymoney' => Util::currency_format(60 + $base['lowestmoney']));
				$rolllist[] = array('nickname' => '圣***者', 'applymoney' => Util::currency_format(88 + $base['lowestmoney']));
				$rolllist[] = array('nickname' => 'E***e', 'applymoney' => Util::currency_format(120 + $base['lowestmoney']));
				$rolllist[] = array('nickname' => '微***君', 'applymoney' => Util::currency_format(50 + $base['lowestmoney']));
				$rolllist[] = array('nickname' => '若***叶', 'applymoney' => Util::currency_format(5 + $base['lowestmoney']));
				$rolllist[] = array('nickname' => 'k***t', 'applymoney' => Util::currency_format(15 + $base['lowestmoney']));
				$rolllist[] = array('nickname' => '倩***倩', 'applymoney' => Util::currency_format(40 + $base['lowestmoney']));
				$rolllist[] = array('nickname' => '余***生', 'applymoney' => Util::currency_format(210 + $base['lowestmoney']));
				$rolllist[] = array('nickname' => '不***鹅', 'applymoney' => Util::currency_format(14 + $base['lowestmoney']));
				$rolllist[] = array('nickname' => '咸***刺', 'applymoney' => Util::currency_format(36 + $base['lowestmoney']));
			}
		}

		$thumbs = $base['thumbs'];
		$applymoney = sprintf('%.2f', $applymoney);
		include wl_template('disapp/applyindex');
	}

	public function applywait()
	{
		global $_W;
		global $_GPC;
		$DistText = Distribution::getDistText();
		extract($DistText);
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '申请' . $fxstext . ' - ' . $_W['wlsetting']['base']['name'] : '申请' . $fxtext;
		$apply = pdo_get('wlmerchant_applydistributor', array('mid' => $_W['mid']));

		if ($apply['status'] == 1) {
			header('location:' . app_url('distribution/disappbase/index'));
		}

		include wl_template('disapp/applywait');
	}

	public function applydis()
	{
		global $_W;
		global $_GPC;
		$base = Setting::wlsetting_read('distribution');
		$url = app_url('distribution/disappbase/index');
		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
		if (empty($_W['wlmember']['mobile']) && in_array('distribution', $mastmobile)) {
			exit(json_encode(array('errno' => 5, 'message' => '您未绑定手机,去绑定？')));
		}

		$rank = $_GPC['rank'] ? $_GPC['rank'] : 1;
		$member = pdo_get('wlmerchant_member', array('id' => $_W['mid']), array('mobile', 'nickname', 'realname'));
		$distributor = pdo_get('wlmerchant_distributor', array('mid' => $_W['mid']));
		if ($base['mode'] && $rank == 2) {
			$examine = $base['twoexamine'];
			$applymoney = $base['twoapplymoney'];
			$appdis = $base['twoappdis'];
		}
		else {
			$examine = $base['examine'];
			$applymoney = $base['applymoney'];
			$appdis = $base['appdis'];
		}

		if ($appdis == 2) {
			$flag = Member::checkhalfmember();

			if ($flag) {
				if ($examine == 1) {
					$appflag = pdo_getcolumn('wlmerchant_applydistributor', array('mid' => $_W['mid']), 'id');

					if ($appflag) {
						exit(json_encode(array('errno' => 1, 'message' => '请勿重复申请')));
					}

					$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'status' => 0, 'realname' => $member['realname'], 'mobile' => $member['mobile'], 'createtime' => time(), 'rank' => $rank);
					$res = pdo_insert('wlmerchant_applydistributor', $data);

					if ($res) {
						Distribution::toadmin($data['realname'], $_W['aid']);
						exit(json_encode(array('errno' => 1)));
					}
					else {
						exit(json_encode(array('errno' => 4, 'message' => '申请失败')));
					}
				}
				else {
					if ($distributor) {
						if (empty($distributor['disflag'])) {
							if ($rank == 1 && $base['mode']) {
								pdo_update('wlmerchant_distributor', array('disflag' => 1, 'leadid' => -1, 'lockflag' => 0), array('mid' => $_W['mid']));
							}
							else {
								pdo_update('wlmerchant_distributor', array('disflag' => 1, 'lockflag' => 0), array('mid' => $_W['mid']));
							}
						}
					}
					else {
						$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'createtime' => time(), 'disflag' => 1, 'nickname' => $member['nickname'], 'mobile' => $member['mobile'], 'realname' => $member['realname'], 'leadid' => -1);
						pdo_insert('wlmerchant_distributor', $data);
						$disid = pdo_insertid();
						pdo_update('wlmerchant_member', array('distributorid' => $disid), array('id' => $_W['mid']));
					}

					Distribution::distriNotice($_W['wlmember']['openid'], $url, 1);
					exit(json_encode(array('errno' => 2, 'message' => '')));
				}
			}
			else {
				exit(json_encode(array('errno' => 3, 'message' => '申请失败,请先开启一卡通')));
			}
		}
		else if ($appdis == 3) {
			if ($examine == 1) {
				$flag = pdo_getcolumn('wlmerchant_applydistributor', array('mid' => $_W['mid']), 'id');

				if ($flag) {
					exit(json_encode(array('errno' => 1, 'message' => '请勿重复申请')));
				}
			}

			$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'aid' => $_W['aid'], 'fkid' => 0, 'sid' => 0, 'status' => 0, 'paytype' => 0, 'createtime' => time(), 'orderno' => createUniontid(), 'price' => $applymoney, 'num' => 1, 'plugin' => 'distribution', 'payfor' => 'applydis', 'specid' => $rank);
			$res = pdo_insert(PDO_NAME . 'order', $data);
			$orderid = pdo_insertid();

			if ($orderid) {
				exit(json_encode(array('errno' => 6, 'message' => $orderid)));
			}
			else {
				exit(json_encode(array('errno' => 4, 'message' => '申请失败,请重试')));
			}
		}
		else if ($examine == 1) {
			$flag = pdo_getcolumn('wlmerchant_applydistributor', array('mid' => $_W['mid']), 'id');

			if ($flag) {
				exit(json_encode(array('errno' => 1, 'message' => '请勿重复申请')));
			}

			$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'status' => 0, 'realname' => $member['realname'], 'mobile' => $member['mobile'], 'createtime' => time(), 'rank' => $rank);
			$res = pdo_insert('wlmerchant_applydistributor', $data);

			if ($res) {
				Distribution::toadmin($data['realname'], $_W['aid']);
				exit(json_encode(array('errno' => 1)));
			}
			else {
				exit(json_encode(array('errno' => 4, 'message' => '申请失败')));
			}
		}
		else {
			$distritor = pdo_get('wlmerchant_distributor', array('mid' => $_W['mid']), array('id'));

			if ($distritor) {
				if ($rank == 1 && $base['mode']) {
					$res = pdo_update('wlmerchant_distributor', array('disflag' => 1, 'leadid' => -1, 'realname' => $realname, 'mobile' => $mobile, 'lockflag' => 0), array('id' => $distritor['id']));
				}
				else {
					$res = pdo_update('wlmerchant_distributor', array('disflag' => 1, 'realname' => $realname, 'mobile' => $mobile, 'lockflag' => 0), array('id' => $distritor['id']));
				}
			}
			else {
				$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'createtime' => time(), 'disflag' => 1, 'nickname' => $member['nickname'], 'mobile' => $member['mobile'], 'realname' => $member['realname'], 'leadid' => -1);
				pdo_insert('wlmerchant_distributor', $data);
				$disid = pdo_insertid();
				$res = pdo_update('wlmerchant_member', array('distributorid' => $disid), array('id' => $_W['mid']));
			}

			if ($res) {
				Distribution::distriNotice($_W['wlmember']['openid'], $url, 1);
				exit(json_encode(array('errno' => 2)));
			}
			else {
				exit(json_encode(array('errno' => 4, 'message' => '申请失败')));
			}
		}
	}

	public function topayorder()
	{
		global $_W;
		global $_GPC;
		$DistText = Distribution::getDistText();
		extract($DistText);
		$orderid = $_GPC['orderid'];
		$order = pdo_get('wlmerchant_order', array('id' => $orderid));
		$params = array('tid' => $order['orderno'], 'ordersn' => $order['orderno'], 'title' => '申请成为' . $fxstext, 'fee' => $order['price']);
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Distribution', 'payfor' => 'Applydis', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}

	public function cancel()
	{
		global $_W;
		global $_GPC;
		$res = pdo_delete('wlmerchant_applydistributor', array('mid' => $_W['mid']));

		if ($res) {
			header('location:' . app_url('distribution/disappbase/applyindex'));
		}
		else {
			wl_message('取消失败，请联系管理员', referer(), 'error');
		}
	}

	public function disposter()
	{
		global $_W;
		global $_GPC;

		if (empty($_W['mid'])) {
			$backurl = app_url('distribution/disappbase/disposter');
			header('location:' . app_url('member/user/signin', array('backurl' => $backurl)));
		}
		else {
			header('location:' . app_url('common/tools/poster', array('id' => $_W['mid'], 'type' => 'distribution')));
		}
	}
}

?>
