<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Goods_WeliamController extends Weliam_merchantModuleSite
{
	public function goods_index()
	{
		global $_W;
		global $_GPC;
		$trade = Setting::wlsetting_read('trade');
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? $trade['credittext'] . '商城 - ' . $_W['wlsetting']['base']['name'] : $trade['credittext'] . '商城';
		$settings = Setting::wlsetting_read('consumption');
		$goods = array('goods' => Consumption::creditshop_goodsall_get(array('type' => 'goods', 'category_id' => $_GPC['cateid'])), 'credit2' => Consumption::creditshop_goodsall_get(array('type' => 'credit2', 'category_id' => $_GPC['cateid'])), 'halfcard' => Consumption::creditshop_goodsall_get(array('type' => 'halfcard', 'category_id' => $_GPC['cateid'])));
		$adv = Consumption::creditshop_adv_get(1);
		$category = Consumption::creditshop_category_get(1);

		if ($settings['communqrcode']) {
			$sys = Setting::agentsetting_read('community');
			$community['qrcode'] = tomedia($settings['communqrcode']);
			$community['name'] = $settings['communname'] ? $settings['communname'] : $sys['communname'];
			$community['desc'] = $settings['commundesc'] ? $settings['commundesc'] : $sys['commundesc'];
			$community['img'] = $settings['communimg'] ? $settings['communimg'] : $sys['communimg'];
			$community['img'] = tomedia($community['img']);
		}

		if ($settings['share_title'] || $settings['share_desc']) {
			$nickname = $_W['wlmember']['nickname'];
			$time = date('Y-m-d H:i:s', time());
			$sysname = $_W['wlsetting']['base']['name'];

			if ($settings['share_title']) {
				$title = $settings['share_title'];
				$title = str_replace('[昵称]', $nickname, $title);
				$title = str_replace('[时间]', $time, $title);
				$title = str_replace('[系统名称]', $sysname, $title);
				$_W['wlsetting']['share']['share_title'] = $title;
			}

			if ($settings['share_desc']) {
				$desc = $settings['share_desc'];
				$desc = str_replace('[昵称]', $nickname, $desc);
				$desc = str_replace('[时间]', $time, $desc);
				$desc = str_replace('[系统名称]', $sysname, $desc);
				$_W['wlsetting']['share']['share_desc'] = $desc;
			}
		}

		if ($settings['share_image']) {
			$_W['wlsetting']['share']['share_image'] = $settings['share_image'];
		}

		include wl_template('creditshop/goods_index');
	}

	public function goods_detail()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '商品详情 - ' . $_W['wlsetting']['base']['name'] : '商品详情';
		$id = intval($_GPC['id']);
		$goods = Consumption::creditshop_goods_get($id);
		$goods['starttime'] = date('Y-m-d H:i:s', $goods['starttime']);
		$goods['endtime'] = date('Y-m-d H:i:s', $goods['endtime']);
		$goods['advs'] = unserialize($goods['advs']);
		pdo_fetch('update' . tablename('wlmerchant_consumption_goods') . ('SET pv=pv+1 WHERE id = ' . $id));

		if ($goods['vipstatus'] == 1) {
			$halfmember = Member::checkhalfmember();

			if ($halfmember) {
				$goods['use_credit1'] = $goods['vipcredit1'];
				$goods['use_credit2'] = $goods['vipcredit2'];
			}
		}

		$settings = Setting::wlsetting_read('consumption');
		$nickname = $_W['wlmember']['nickname'];
		$time = date('Y-m-d H:i:s', time());
		$sysname = $_W['wlsetting']['base']['name'];

		if ($settings['goods_title']) {
			$title = $settings['goods_title'];
			$title = str_replace('[昵称]', $nickname, $title);
			$title = str_replace('[时间]', $time, $title);
			$title = str_replace('[系统名称]', $sysname, $title);
			$title = str_replace('[商品名称]', $goods['title'], $title);
			$title = str_replace('[原价]', $goods['old_price'], $title);
			$title = str_replace('[所需积分]', $goods['use_credit1'], $title);
			$title = str_replace('[所需金额]', $goods['use_credit2'], $title);
			$_W['wlsetting']['share']['share_title'] = $title;
		}
		else {
			$_W['wlsetting']['share']['share_title'] = $goods['title'];
		}

		if ($settings['goods_desc']) {
			$desc = $settings['goods_desc'];
			$desc = str_replace('[昵称]', $nickname, $desc);
			$desc = str_replace('[时间]', $time, $desc);
			$desc = str_replace('[系统名称]', $sysname, $desc);
			$desc = str_replace('[商品名称]', $goods['title'], $desc);
			$desc = str_replace('[原价]', $goods['old_price'], $desc);
			$desc = str_replace('[所需积分]', $goods['use_credit1'], $desc);
			$desc = str_replace('[所需金额]', $goods['use_credit2'], $desc);
			$_W['wlsetting']['share']['share_desc'] = $desc;
		}

		$_W['wlsetting']['share']['share_image'] = !empty($settings['goods_image']) ? $settings['goods_image'] : $goods['thumb'];
		include wl_template('creditshop/goods_detail');
	}

	public function getintegrallist()
	{
		global $_W;
		global $_GPC;
		$lng = !empty($_GPC['lng']) ? $_GPC['lng'] : $_SESSION['lng'];
		$lat = !empty($_GPC['lat']) ? $_GPC['lat'] : $_SESSION['lat'];
		$where = ' uniacid = ' . $_W['uniacid'] . ' AND status = 1 ';
		$plugin = Setting::agentsetting_read('pluginlist');
		$limitNum = $plugin['jflimit'] ? $plugin['jflimit'] : 10;

		switch ($plugin['jfsort']) {
		case '1':
			$where .= ' ORDER BY id DESC ';
			$limit = ' LIMIT 0,' . $limitNum . ' ';
			break;

		case '3':
			$where .= ' ORDER BY displayorder DESC ';
			$limit = ' LIMIT 0,' . $limitNum . ' ';
			break;

		case '4':
			$where .= ' ORDER BY pv DESC ';
			$limit = ' LIMIT 0,' . $limitNum . ' ';
			break;

		default:
			$where .= ' ORDER BY id DESC ';
			$limit = ' LIMIT 0,' . $limitNum . ' ';
			break;
		}

		$integrallist = pdo_fetchall('SELECT id,title,thumb,use_credit1,use_credit2,old_price FROM ' . tablename(PDO_NAME . 'consumption_goods') . (' WHERE ' . $where . ' ' . $limit . ' '));

		foreach ($integrallist as $k => &$v) {
			$v['thumb'] = tomedia($v['thumb']);
			$v['href'] = app_url('consumption/goods/goods_detail', array('id' => $v['id']), false);
		}

		exit(json_encode($integrallist));
	}

	public function recordlist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '兑换记录 - ' . $_W['wlsetting']['base']['name'] : '兑换记录';

		if ($_W['ispost']) {
			$pindex = $_GPC['pindex'];
			$where['uniacid'] = $_W['uniacid'];
			$where['mid'] = $_W['mid'];
			$listData = Util::getNumData('*', PDO_NAME . 'consumption_record', $where, 'createtime desc', $pindex, 10, 1);
			$listData = $listData[0];

			foreach ($listData as $key => &$v) {
				$goods = Consumption::creditshop_goods_get($v['goodsid']);
				$v['type'] = $goods['type'];
				$v['goodsname'] = $goods['title'];
				$v['createtime'] = date('Y-m-d H:i:s', $v['createtime']);
				$v['goodsimg'] = tomedia($goods['thumb']);
				$v['url'] = app_url('consumption/goods/goods_detail', array('id' => $goods['id']));

				if ($v['expressid']) {
					$express = pdo_get('wlmerchant_express', array('id' => $v['expressid']), array('expressname', 'expresssn'));
					$v['expressname'] = $express['expressname'];
					$v['expresssn'] = $express['expresssn'];
				}
			}

			exit(json_encode(array('errno' => 0, 'data' => $listData)));
		}

		include wl_template('creditshop/recordlist');
	}

	public function collect()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$expressid = pdo_getcolumn(PDO_NAME . 'consumption_record', array('id' => $id), 'expressid');
		$res1 = pdo_update('wlmerchant_consumption_record', array('status' => 3), array('id' => $id));
		$res2 = pdo_update('wlmerchant_express', array('receivetime' => time()), array('id' => $expressid));
		if ($res1 && $res2) {
			$orderid = pdo_getcolumn(PDO_NAME . 'consumption_record', array('id' => $id), 'orderid');
			$disorderid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $orderid), 'disorderid');

			if ($disorderid) {
				pdo_update('wlmerchant_disorder', array('status' => 1), array('id' => $disorderid, 'status' => 0));
			}

			wl_json(0);
		}
		else {
			wl_json(1);
		}
	}

	public function checkcon()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$goods = Consumption::creditshop_goods_get($id);

		if (empty($id)) {
			wl_json(1, '商品参数错误，请刷新重试');
		}

		if (0 < $goods['stock']) {
			$selenum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_consumption_record') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND  goodsid = ' . $id));
			if ($selenum == $goods['stock'] || $goods['stock'] < $selenum) {
				wl_json(1, '该商品已被全部兑换');
			}
		}

		if ($goods['vipstatus'] == 2) {
			$halfmember = Member::checkhalfmember();

			if (!$halfmember) {
				wl_json(1, '该商品只有会员可以兑换');
			}
		}

		if (0 < $goods['chance']) {
			$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_consumption_record') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND  goodsid = ' . $id . ' AND mid = ' . $_W['mid'] . ' '));
			if ($times == $goods['chance'] || $goods['chance'] < $times) {
				wl_json(1, '该商品最多兑换' . $goods['chance'] . '次');
			}
		}

		if ($_W['wlmember']['credit1'] < $goods['use_credit1']) {
			wl_json(1, '积分不足，无法兑换');
		}

		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
		if (empty($_W['wlmember']['mobile']) && in_array('consumption', $mastmobile)) {
			wl_json(2, '未绑定手机号，去绑定？');
		}

		wl_json(0, '可以兑换');
	}

	public function createorder()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			wl_json(1, '商品参数错误，请刷新重试');
		}

		$goods = Consumption::creditshop_goods_get($id);
		$remark = $_GPC['remark'];
		$cardId = $_GPC['cardId'];
		$halfcardflag = Member::checkhalfmember();
		if ((!empty($cardId) || $halfcardflag) && $goods['vipstatus'] == 1) {
			$goods['use_credit1'] = $goods['vipcredit1'];
			$goods['use_credit2'] = $goods['vipcredit2'];
		}
		else {
			if ($goods['vipstatus'] == 2) {
				if (empty($halfcardflag) && empty($cardId)) {
					wl_json(1, '该商品是会员特供，请先成为会员');
				}
			}
		}

		if (0 < $goods['chance']) {
			$times = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_consumption_record') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND  goodsid = ' . $id . ' AND mid = ' . $_W['mid'] . ' '));
			if ($times == $goods['chance'] || $goods['chance'] < $times) {
				wl_json(1, '该商品最多兑换' . $goods['chance'] . '次');
			}
		}

		if ($_W['wlmember']['credit1'] < $goods['use_credit1']) {
			wl_json(1, '积分不足，无法兑换');
		}

		if ($_GPC['addressid'] && $goods['type'] == 'goods') {
			$addressid = $_GPC['addressid'];
			$address = pdo_get('wlmerchant_address', array('id' => $addressid));
			$data['uniacid'] = $_W['uniacid'];
			$data['mid'] = $_W['mid'];
			$data['goodsid'] = $id;
			$data['address'] = $address['province'] . $address['city'] . $address['county'] . $address['detailed_address'];
			$data['name'] = $address['name'];
			$data['tel'] = $address['tel'];

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
							$expressprice = $v['money'];
						}
					}
				}

				if (empty($expressprice)) {
					$expressprice = $express['defaultmoney'];
				}
			}
			else {
				$expressprice = 0;
			}

			$data['expressprice'] = $expressprice;
			pdo_insert(PDO_NAME . 'express', $data);
			$expressid = pdo_insertid();
		}

		if ($goods['use_credit2'] < 0.01 && empty($cardId) && $expressprice < 0.01) {
			$res = Member::credit_update_credit1($_W['mid'], 0 - $goods['use_credit1'], '兑换[' . $goods['title'] . ']消耗');

			if ($res) {
				if ($goods['type'] == 'credit2') {
					$res2 = Member::credit_update_credit2($_W['mid'], $goods['credit2'], '兑换[' . $goods['title'] . ']获得余额');
				}
				else if ($goods['type'] == 'halfcard') {
					$res2 = Consumption::conhalfcard($_W['mid'], $goods['halfcardid'], $_GPC['thname']);
				}
				else {
					$res2 = 1;
				}

				if ($res2) {
					$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'goodsid' => $id, 'status' => 3, 'createtime' => time(), 'integral' => $goods['use_credit1'], 'expressid' => $expressid);

					if ($expressid) {
						$data['status'] = 1;
					}

					$res3 = pdo_insert(PDO_NAME . 'consumption_record', $data);

					if ($res3) {
						$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $_W['mid']), 'openid');
						$first = '恭喜您,一个商品兑换成功';
						$keyword1 = $goods['title'];
						$keyword2 = '已完成';
						$remark = '点击查看兑换记录，如有问题请联系管理员';
						$url = app_url('consumption/goods/recordlist');
						Message::jobNotice($openid, $first, $keyword1, $keyword2, $remark, $url);
						wl_json(4, '兑换成功');
					}
				}
			}
		}
		else {
			$random = Util::createConcode(1);
			$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'sid' => 0, 'aid' => $_W['aid'], 'fkid' => $goods['id'], 'plugin' => 'consumption', 'payfor' => 'consumOrder', 'orderno' => createUniontid(), 'status' => 0, 'createtime' => TIMESTAMP, 'price' => sprintf('%.2f', $goods['use_credit2'] + $expressprice), 'num' => 1, 'expressid' => $expressid, 'buyremark' => $remark, 'goodsprice' => $goods['old_price'], 'settlementmoney' => 0, 'settlementmoney' => 0, 'vip_card_id' => $cardId, 'name' => $_GPC['thname']);
			pdo_insert(PDO_NAME . 'order', $data);
			$orderid = pdo_insertid();

			if ($expressid) {
				pdo_update('wlmerchant_express', array('orderid' => $orderid), array('id' => $expressid));
			}

			if ($orderid) {
				wl_json(0, '下单成功', $orderid);
			}
		}
	}

	public function topay()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$orderid = $_GPC['orderid'];
		if (empty($id) || empty($orderid)) {
			wl_message('缺少重要参数，请重试');
		}

		$goods = Consumption::creditshop_goods_get($id);
		$data = pdo_get(PDO_NAME . 'order', array('uniacid' => $_W['uniacid'], 'id' => $orderid), array('orderno', 'price', 'vip_card_id'));
		$bankrid = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $data['sid']), 'bankrid');
		$data = PayBuild::isOpenCard($data);
		$params = array('tid' => $data['orderno'], 'ordersn' => $data['orderno'], 'title' => $goods['title'], 'fee' => $data['price'], 'module' => 'weliam_merchant');
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'plugin' => 'Bargain', 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Consumption', 'payfor' => 'consumOrder', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}
}

?>
