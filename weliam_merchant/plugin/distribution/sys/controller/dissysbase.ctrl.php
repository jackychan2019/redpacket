<?php
//dezend by http://www.sucaihuo.com/
class Dissysbase_WeliamController
{
	public function distributorlist()
	{
		global $_W;
		global $_GPC;
		$todo = $_GPC['todo'] ? $_GPC['todo'] : 'dislist';
		$base = Setting::wlsetting_read('distribution');
		$dislevels = pdo_fetchall('SELECT id,name FROM ' . tablename('wlmerchant_dislevel') . ('WHERE uniacid = ' . $_W['uniacid'] . ' ORDER BY isdefault DESC,id ASC'));

		if ($todo == 'dislist') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$wheres = array();
			$wheres['uniacid'] = $_W['uniacid'];
			$wheres['disflag#'] = '(1,-1)';
			$type = intval($_GPC['type']);
			$keyword = trim($_GPC['keyword']);

			if (!empty($keyword)) {
				switch ($type) {
				case 2:
					$wheres['mobile@'] .= $keyword;
					break;

				case 3:
					$wheres['nickname@'] .= $keyword;
					break;

				case 4:
					$wheres['realname@'] .= $keyword;
					break;

				case 5:
					$wheres['mid@'] .= $keyword;
					break;
				}
			}

			if ($_GPC['time_limit']) {
				$time_limit = $_GPC['time_limit'];
				$starttime = strtotime($_GPC['time_limit']['start']);
				$endtime = strtotime($_GPC['time_limit']['end']);

				if ($_GPC['timetype']) {
					$wheres['createtime>'] = $starttime;
					$wheres['createtime<'] = $endtime;
				}
			}

			if (empty($starttime) || empty($endtime)) {
				$starttime = strtotime('-1 month');
				$endtime = time();
			}

			if ($_GPC['levelid']) {
				$default = pdo_getcolumn(PDO_NAME . 'dislevel', array('id' => $_GPC['levelid']), 'isdefault');

				if ($default) {
					$wheres['dislevel#'] = '(0,' . intval($_GPC['levelid']) . ')';
				}
				else {
					$wheres['dislevel'] = intval($_GPC['levelid']);
				}
			}

			if ($_GPC['export'] != '') {
				$this->exportlist($wheres);
			}

			$list = Distribution::getNumDistributor('*', $wheres, 'ID DESC', $pindex, $psize, 1);
			$pager = $list[1];
			$list = $list[0];

			foreach ($list as $key => &$v) {
				$mem = pdo_get('wlmerchant_member', array('id' => $v['mid']), array('mobile', 'realname', 'nickname', 'avatar'));
				if (empty($v['mobile']) && $mem['mobile']) {
					$v['mobile'] = $mem['mobile'];
					pdo_update('wlmerchant_distributor', array('mobile' => $mem['mobile']), array('id' => $v['id']));
				}

				if (empty($v['realname']) && $mem['realname']) {
					$v['realname'] = $mem['realname'];
					pdo_update('wlmerchant_distributor', array('realname' => $mem['realname']), array('id' => $v['id']));
				}

				if (empty($v['nickname']) && $mem['nickname']) {
					$v['nickname'] = $mem['nickname'];
					pdo_update('wlmerchant_distributor', array('nickname' => $mem['nickname']), array('id' => $v['id']));
				}

				$v['avatar'] = $mem['avatar'];
				$v['lownum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $v['mid'] . ' AND lockflag = 0 '));

				if ($v['leadid']) {
					$topname = pdo_get('wlmerchant_member', array('id' => $v['leadid']), array('nickname', 'realname'));

					if ($topname['nickname']) {
						$v['topname'] = $topname['nickname'];
					}
					else {
						$v['topname'] = $topname['realname'];
					}
				}

				if ($v['dislevel']) {
					$v['rankname'] = pdo_getcolumn(PDO_NAME . 'dislevel', array('id' => $v['dislevel']), 'name');
				}
				else {
					$v['rankname'] = pdo_getcolumn(PDO_NAME . 'dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'name');
				}

				$v['lowdisnum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $v['mid'] . ' AND lockflag = 0 AND disflag = 1'));
				$disqrcode = Distribution::getgzqrcode($v['mid']);
				$v['qrcode'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($disqrcode['ticket']);
			}
		}

		if ($todo == 'adddis') {
			if (checksubmit()) {
				$memberid = $_GPC['memberid'];
				$member = pdo_get('wlmerchant_member', array('id' => $memberid), array('mobile', 'nickname', 'realname'));
				$distributorid = pdo_getcolumn('wlmerchant_member', array('id' => $memberid), 'distributorid');

				if ($distributorid) {
					$distributor = pdo_get('wlmerchant_distributor', array('id' => $distributorid));

					if ($distributor['disflag']) {
						wl_message('不能重复添加', referer(), 'error');
					}
					else {
						$res = pdo_update('wlmerchant_distributor', array('disflag' => 1, 'leadid' => trim($_GPC['leadid']), 'source' => 1, 'lockflag' => 0), array('id' => $distributorid));

						if ($res) {
							wl_message('添加分销商成功', web_url('distribution/dissysbase/distributorlist'), 'success');
						}
						else {
							wl_message('添加分销商失败', referer(), 'error');
						}
					}
				}
				else {
					$data = array('uniacid' => $_W['uniacid'], 'mid' => $memberid, 'createtime' => time(), 'disflag' => 1, 'nickname' => $member['nickname'], 'mobile' => $member['mobile'], 'realname' => $member['realname'], 'leadid' => trim($_GPC['leadid']), 'source' => 1);
					pdo_insert('wlmerchant_distributor', $data);
					$disid = pdo_insertid();
					$res = pdo_update('wlmerchant_member', array('distributorid' => $disid), array('id' => $memberid));

					if ($res) {
						wl_message('添加分销商成功', web_url('distribution/dissysbase/distributorlist'), 'success');
					}
					else {
						wl_message('添加分销商失败', referer(), 'error');
					}
				}
			}

			if ($_W['wlsetting']['distribution']['mode']) {
				$leadlists = pdo_fetchall('SELECT nickname,mid FROM ' . tablename('wlmerchant_distributor') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disflag = 1 AND leadid < 0 ORDER BY createtime ASC'));
			}
			else {
				$leadlists = pdo_fetchall('SELECT nickname,mid FROM ' . tablename('wlmerchant_distributor') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disflag = 1 ORDER BY createtime ASC'));
			}
		}

		include wl_template('disysbase/distributorlist');
	}

	public function exportlist($where)
	{
		global $_W;
		global $_GPC;

		if (empty($where)) {
			return false;
		}

		$list = Distribution::getNumDistributor('*', $where, 'ID DESC', 0, 0, 1);
		$list = $list[0];

		foreach ($list as $key => &$v) {
			$mem = pdo_get('wlmerchant_member', array('id' => $v['mid']), array('mobile', 'realname', 'nickname'));
			$v['mobile'] = $mem['mobile'];
			$v['realname'] = $mem['realname'];
			$v['nickname'] = $mem['nickname'];
			$v['lowdis'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $v['mid'] . ' AND disflag = 1'));
			$v['lownum'] = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_distributor') . (' WHERE leadid = ' . $v['mid']));

			if ($v['leadid']) {
				$topname = pdo_get('wlmerchant_member', array('id' => $v['leadid']), array('nickname', 'realname'));

				if ($topname['nickname']) {
					$v['topname'] = $topname['nickname'];
				}
				else {
					$v['topname'] = $topname['realname'];
				}
			}

			if (empty($v['topname'])) {
				$v['topname'] = '系统直属';
			}

			if ($v['dislevel']) {
				$v['rankname'] = pdo_getcolumn(PDO_NAME . 'dislevel', array('id' => $v['dislevel']), 'name');
			}
			else {
				$v['rankname'] = pdo_getcolumn(PDO_NAME . 'dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'name');
			}
		}

		$filter = array('id' => '分销商ID', 'nickname' => '昵称', 'realname' => '真实姓名', 'mobile' => '电话', 'dismoney' => '累计佣金', 'nowmoney' => '未结算佣金', 'rankname' => '等级', 'topname' => '上级', 'lowdis' => '下级分销商数量', 'lownum' => '下级人数', 'createtime' => '创建时间');
		$data = array();
		$i = 0;

		while ($i < count($list)) {
			foreach ($filter as $key => $title) {
				if ($key == 'createtime') {
					$data[$i][$key] = date('Y-m-d H:i:s', $list[$i][$key]);
				}
				else {
					$data[$i][$key] = $list[$i][$key];
				}
			}

			++$i;
		}

		util_csv::export_csv_2($data, $filter, '分销商列表.csv');
		exit();
	}

	public function distributordetail()
	{
		global $_W;
		global $_GPC;
		$memid = $_GPC['memid'];
		$todo = $_GPC['todo'] ? $_GPC['todo'] : 'base';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = array();
		$where['uniacid'] = $_W['uniacid'];
		$disid = pdo_getcolumn('wlmerchant_distributor', array('uniacid' => $_W['uniacid'], 'mid' => $memid), 'id');

		if ($todo == 'base') {
			$messagesaler = pdo_get('wlmerchant_member', array('id' => $memid), array('mobile', 'realname', 'nickname', 'avatar', 'distributorid'));
			$distributor = pdo_get('wlmerchant_distributor', array('mid' => $memid), array('nowmoney', 'dismoney'));
			$messagesaler['nowmoney'] = $distributor['nowmoney'];
			$messagesaler['dismoney'] = $distributor['dismoney'];
			$applymoney = $cashmoney = $successmoney = 0;
			$apply = pdo_getall(PDO_NAME . 'settlement_record', array('mid' => $memid, 'type' => 3), array('status', 'sapplymoney'));

			if ($apply) {
				foreach ($apply as $key => $app) {
					if ($app['status'] == 6 || $app['status'] == 7) {
						$applymoney += $app['sapplymoney'];
					}
					else if ($app['status'] == 8) {
						$cashmoney += $app['sapplymoney'];
					}
					else {
						if ($app['status'] == 9) {
							$successmoney += $app['sapplymoney'];
						}
					}
				}
			}

			$applymoney = number_format($applymoney, 2);
			$cashmoney = number_format($cashmoney, 2);
			$successmoney = number_format($successmoney, 2);
		}
		else if ($todo == 'lowpeople') {
			$where['leadid'] = $memid;
			$where['lockflag'] = 0;
			$type = intval($_GPC['type']);
			$keyword = trim($_GPC['keyword']);

			if (!empty($keyword)) {
				switch ($type) {
				case 2:
					$where['mobile@'] .= $keyword;
					break;

				case 3:
					$where['nickname@'] .= $keyword;
					break;

				case 4:
					$where['realname@'] .= $keyword;
					break;

				case 5:
					$where['mid@'] .= $keyword;
					break;
				}
			}

			$lowpeople = Util::getNumData('mid,id', PDO_NAME . 'distributor', $where, 'ID DESC', $pindex, $psize, 1);
			$pager = $lowpeople[1];
			$lowpeople = $lowpeople[0];

			foreach ($lowpeople as $key => &$peo) {
				$member = pdo_get('wlmerchant_member', array('id' => $peo['mid']), array('mobile', 'realname', 'nickname', 'avatar'));
				$peo['nickname'] = $member['nickname'];
				$peo['realname'] = $member['realname'];
				$peo['mobile'] = $member['mobile'];
				$peo['avatar'] = $member['avatar'];
			}
		}
		else if ($todo == 'loworder') {
			$loworderwhere['uniacid'] = $_W['uniacid'];

			if ($_GPC['time']) {
				$time = $_GPC['time'];
				$starttime = strtotime($time['start']);
				$endtime = strtotime($time['end']);
				$loworderwhere['createtime>'] = $starttime;
				$loworderwhere['createtime<'] = $endtime;
			}

			if ($_GPC['ordertype']) {
				$loworderwhere['plugin'] = $_GPC['ordertype'];
			}

			if (empty($starttime) || empty($endtime)) {
				$starttime = strtotime('-1 month');
				$endtime = time();
			}

			if ($_GPC['buymid']) {
				$buymid = $_GPC['buymid'];
				$loworderwhere['buymid'] = $buymid;
			}

			$loworderwhere['no*'] = '(oneleadid = ' . $disid . ' or twoleadid = ' . $disid . ' or threeleadid = ' . $disid . ' )';
			$loworder = Util::getNumData('*', PDO_NAME . 'disorder', $loworderwhere, 'ID DESC', $pindex, $psize, 1);
			$pager = $loworder[1];
			$loworder = $loworder[0];

			foreach ($loworder as $key => &$order) {
				if ($order['plugin'] == 'rush') {
					$rush = pdo_get('wlmerchant_rush_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $rush['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = Rush::getSingleActive($rush['activityid'], 'name,thumb,unit');
					$order['gnum'] = $rush['num'];
					$order['goodsprice'] = $rush['price'] / $rush['num'];
					$order['merchantname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $rush['sid']), 'storename');
					$order['orderno'] = $rush['orderno'];
					$order['gname'] = $goods['name'];
					$order['gimg'] = $goods['thumb'];
					$order['unit'] = $goods['unit'];
				}
				else if ($order['plugin'] == 'fightgroup') {
					$fightgroup = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $fightgroup['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = Wlfightgroup::getSingleGood($fightgroup['fkid'], 'name,logo,unit');
					$order['gnum'] = $fightgroup['num'];
					$order['goodsprice'] = $order['orderprice'] / $fightgroup['num'];
					$order['merchantname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $fightgroup['sid']), 'storename');
					$order['orderno'] = $fightgroup['orderno'];
					$order['gname'] = $goods['name'];
					$order['gimg'] = $goods['logo'];
					$order['unit'] = $goods['unit'];
				}
				else if ($order['plugin'] == 'coupon') {
					$coupon = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $coupon['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = wlCoupon::getSingleCoupons($coupon['fkid'], 'title,logo');
					$order['gnum'] = $coupon['num'];
					$order['goodsprice'] = $order['orderprice'] / $coupon['num'];
					$order['merchantname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $coupon['sid']), 'storename');
					$order['orderno'] = $coupon['orderno'];
					$order['gname'] = $goods['title'];
					$order['gimg'] = $goods['logo'];
					$order['unit'] = '张';
				}
				else if ($order['plugin'] == 'pocket') {
					$pocket = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $pocket['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = pdo_get('wlmerchant_pocket_informations', array('id' => $pocket['fkid']), array('share_title', 'type'));
					$type = pdo_get('wlmerchant_pocket_type', array('id' => $goods['type']), array('title', 'img'));
					$order['gnum'] = 1;
					$order['goodsprice'] = $order['orderprice'];
					$order['merchantname'] = '掌上信息';
					$order['orderno'] = $pocket['orderno'];

					if ($goods['share_title']) {
						$order['gname'] = $goods['share_title'];
					}
					else {
						$order['gname'] = $type['title'];
					}

					$order['gimg'] = $type['img'];
					$order['unit'] = '次';
				}
				else if ($order['plugin'] == 'halfcard') {
					$halforder = pdo_get('wlmerchant_halfcard_record', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $halforder['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = pdo_get('wlmerchant_halfcard_type', array('id' => $halforder['typeid']), array('name', 'days', 'logo'));
					$order['gnum'] = $goods['days'];
					$order['goodsprice'] = $order['orderprice'];
					$order['merchantname'] = '一卡通充值';
					$order['orderno'] = $halforder['orderno'];
					$order['gname'] = $goods['name'];
					$order['gimg'] = $goods['logo'];
					$order['unit'] = '天';
				}
				else if ($order['plugin'] == 'vip') {
					$viporder = pdo_get('wlmerchant_vip_record', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $viporder['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = pdo_get('wlmerchant_member_type', array('id' => $viporder['typeid']), array('name', 'days', 'logo'));
					$order['gnum'] = $goods['days'];
					$order['goodsprice'] = $order['orderprice'];
					$order['merchantname'] = 'VIP充值';
					$order['orderno'] = $viporder['orderno'];
					$order['gname'] = $goods['name'];
					$order['gimg'] = $goods['logo'];
					$order['unit'] = '天';
				}
				else {
					if ($order['plugin'] == 'payonline') {
						$payorder = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
						$member = pdo_get('wlmerchant_member', array('id' => $payorder['mid']), array('mobile', 'nickname', 'avatar'));
						$goods = pdo_get('wlmerchant_halfcardlist', array('id' => $payorder['fkid']), array('title'));
						$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $payorder['fkid']), array('storename', 'logo'));
						$order['gnum'] = 1;
						$order['goodsprice'] = $order['orderprice'];
						$order['merchantname'] = $merchant['storename'];
						$order['orderno'] = $payorder['orderno'];
						$order['gname'] = $goods['title'];
						$order['gimg'] = $merchant['logo'];
						$order['unit'] = '次';
					}
				}

				$order['nickname'] = $member['nickname'];
				$order['mobile'] = $member['mobile'];
				$order['avatar'] = $member['avatar'];

				if ($order['status'] == 0) {
					$order['statusCss'] = 'default';
					$order['statusName'] = '不可结算';
				}

				if ($order['status'] == 1) {
					$order['statusCss'] = 'info';
					$order['statusName'] = '可结算';
				}
				else {
					if ($order['status'] == 2) {
						$order['statusCss'] = 'success';
						$order['statusName'] = '已结算';
					}
				}

				$leadmoney = unserialize($order['leadmoney']);

				if ($order['oneleadid'] == $disid) {
					$order['leadmoney'] = $leadmoney['one'];
					$order['rank'] = 1;
				}
				else if ($order['twoleadid'] == $disid) {
					$order['leadmoney'] = $leadmoney['two'];
					$order['rank'] = 2;
				}
				else {
					if ($order['threeleadid'] == $disid) {
						$order['leadmoney'] = $leadmoney['three'];
						$order['rank'] = 3;
					}
				}
			}
		}
		else {
			if ($todo == 'applylist') {
				$where['mid'] = $memid;
				$applylist = Util::getNumData('*', PDO_NAME . 'settlement_record', $where, 'ID DESC', $pindex, $psize, 1);
				$pager = $applylist[1];
				$applylist = $applylist[0];

				if ($applylist) {
					foreach ($applylist as $key => &$apply) {
						$member = pdo_get('wlmerchant_member', array('id' => $memid), array('mobile', 'avatar', 'nickname'));
						$apply['avatar'] = $member['avatar'];
						$apply['mobile'] = $member['mobile'];
						$apply['nickname'] = $member['nickname'];
					}
				}
			}
		}

		include wl_template('disysbase/adddistributor');
	}

	public function applist()
	{
		global $_W;
		global $_GPC;
		header('Location:' . web_url('finace/wlCash/cashApply', array('type' => 3)));
	}

	public function export($status)
	{
		if (empty($status)) {
			return false;
		}

		set_time_limit(0);

		if ($status == 1) {
			$where['status'] = 7;
			$name = '审核中分销商提现记录';
		}
		else if ($status == 2) {
			$where['status'] = 8;
			$name = '已审核分销商提现记录';
		}
		else if ($status == 3) {
			$where['status'] = 11;
			$name = '已驳回分销商提现记录';
		}
		else {
			if ($status == 4) {
				$where['status'] = 9;
				$name = '已打款分销商提现记录';
			}
		}

		$applylist = Util::getNumData('*', PDO_NAME . 'settlement_record', $where, 'ID DESC', 0, 0, 1);
		$list = $applylist[0];

		if ($list) {
			foreach ($list as $key => &$apply) {
				$member = pdo_get('wlmerchant_member', array('id' => $apply['mid']), array('mobile', 'avatar', 'nickname'));
				$apply['avatar'] = $member['avatar'];
				$apply['mobile'] = $member['mobile'];
				$apply['nickname'] = $member['nickname'];
				$apply['applytime'] = date('Y-m-d H:i:s', $apply['applytime']);

				if ($apply['updatetime']) {
					$apply['updatetime'] = date('Y-m-d H:i:s', $apply['updatetime']);
				}
				else {
					$apply['updatetime'] = '未操作';
				}
			}
		}

		$html = "\xef\xbb\xbf";
		$filter = array('nickname' => '用户名', 'mobile' => '用户手机', 'sgetmoney' => '金额', 'status' => '申请状态', 'settletype' => '打款方式', 'applytime' => '申请时间', 'updatetime' => '处理时间');

		foreach ($filter as $key => $title) {
			$html .= $title . '	,';
		}

		$html .= '
';

		foreach ($list as $k => $v) {
			foreach ($filter as $key => $title) {
				if ($key == 'status') {
					switch ($v[$key]) {
					case '6':
						$html .= '审核中' . '	,';
						break;

					case '7':
						$html .= '审核中' . '	,';
						break;

					case '8':
						$html .= '已审核' . '	,';
						break;

					case '9':
						$html .= '已打款' . '	,';
						break;

					case '10':
						$html .= '已驳回' . '	, ';
						break;

					case '11':
						$html .= '已驳回' . '	, ';
						break;

					default:
						$html .= 'null' . '	, ';
						break;
					}
				}
				else if ($key == 'settletype') {
					switch ($v[$key]) {
					case '1':
						$html .= '手动完成' . '	, ';
						break;

					case '2':
						$html .= '微信打款' . '	, ';
						break;

					case '3':
						$html .= '微信打款' . '	, ';
						break;

					default:
						$html .= '未打款' . '	, ';
						break;
					}
				}
				else {
					$html .= $v[$key] . trim('	,');
				}
			}

			$html .= '
';
		}

		header('Content-type:text/csv');
		header('Content-Disposition:attachment; filename=' . $name . '.csv');
		echo $html;
		exit();
	}

	public function disbaseset()
	{
		global $_W;
		global $_GPC;
		$base = Setting::wlsetting_read('distribution');
		$halfcardtypes = pdo_getall('wlmerchant_halfcard_type', array('uniacid' => $_W['uniacid']), array('name', 'id'));

		if (checksubmit('submit')) {
			$data = $_GPC['base'];
			$data['moneynptice'] = $_GPC['moneynptice'];
			$data['noticeSwitch'] = $_GPC['noticeSwitch'];

			if (empty($data['lowestmoney'])) {
				$data['lowestmoney'] = 1;
			}

			$data['appdetail'] = htmlspecialchars_decode($data['appdetail']);
			$data['distriqa'] = htmlspecialchars_decode($data['distriqa']);
			if ($data['lockstatus'] != 1 && $data['lockstatus'] != 3) {
				pdo_update('wlmerchant_distributor', array('lockflag' => 0, 'uniacid' => $_W['uniacid']), array('lockflag' => 1));
			}

			$res1 = Setting::wlsetting_save($data, 'distribution');

			if ($res1) {
				Tools::clearposter();
				wl_message('分销设置保存成功！', referer(), 'success');
			}
			else {
				wl_message('分销设置保存失败！', referer(), 'error');
			}
		}

		include wl_template('disysbase/disbaseset');
	}

	public function reject()
	{
		global $_W;
		global $_GPC;
		$appid = $_GPC['id'];
		$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 11, 'updatetime' => time()), array('id' => $appid));

		if ($res) {
			$apply = pdo_get(PDO_NAME . 'settlement_record', array('id' => $appid), array('sgetmoney', 'disid', 'mid', 'sapplymoney'));
			$nowmoney = pdo_getcolumn('wlmerchant_distributor', array('id' => $apply['disid']), 'nowmoney');
			$newmoney = $apply['sapplymoney'] + $nowmoney;
			$res2 = pdo_update('wlmerchant_distributor', array('nowmoney' => $newmoney), array('id' => $apply['disid']));
			$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $apply['mid']), 'openid');
			$url = app_url('distribution/disappbase/apply', array('type' => 'reject'));
			Distribution::distriNotice($openid, $url, 5, 0, $apply['sapplymoney']);
			Distribution::adddisdetail($appid, $apply['mid'], '-1', 1, $apply['sapplymoney'], 'cash', 1);
			wl_message('驳回申请成功！', referer(), 'success');
		}
		else {
			wl_message('驳回申请失败！', referer(), 'error');
		}
	}

	public function pass()
	{
		global $_W;
		global $_GPC;
		$appid = $_GPC['id'];
		$trade_no = time() . random(4, true);
		$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 8, 'updatetime' => time(), 'trade_no' => $trade_no), array('id' => $appid));

		if ($res) {
			$apply = pdo_get(PDO_NAME . 'settlement_record', array('id' => $appid), array('mid', 'sapplymoney'));
			$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $apply['mid']), 'openid');
			$url = app_url('distribution/disappbase/apply', array('type' => 'deling'));
			Distribution::distriNotice($openid, $url, 4, 0, $apply['sapplymoney']);
			wl_message('审核通过成功！', referer(), 'success');
		}
		else {
			wl_message('审核通过失败！', referer(), 'error');
		}
	}

	public function tocash()
	{
		global $_W;
		global $_GPC;
		$appid = $_GPC['id'];
		$apply = pdo_get(PDO_NAME . 'settlement_record', array('id' => $appid));

		if ($apply['status'] != 8) {
			wl_message('申请状态异常，请刷新重试', referer(), 'error');
		}

		if (is_numeric($apply['sgetmoney'])) {
			if ($apply['sgetmoney'] < 1) {
				wl_message('到账金额需要大于1元！', referer(), 'error');
			}

			$applyopenid = pdo_getcolumn('wlmerchant_member', array('id' => $apply['mid']), 'openid');
			$realname = pdo_getcolumn(PDO_NAME . 'member', array('id' => $apply['mid']), 'realname');
			$result = wlPay::finance($applyopenid, $apply['sgetmoney'], '结算给分销商', $realname, $apply['trade_no']);
			if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
				$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 9, 'updatetime' => time(), 'settletype' => 3), array('id' => $appid));

				if ($res) {
					$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $apply['mid']), 'openid');
					$url = app_url('distribution/disappbase/apply', array('type' => 'finish'));
					Distribution::distriNotice($openid, $url, 6, 0, $apply['sapplymoney'], '微信零钱');
					wl_message('微信钱包打款成功！', referer(), 'success');
				}
				else {
					wl_message('微信钱包打款失败！', referer(), 'error');
				}
			}
			else {
				if (empty($result['err_code_des'])) {
					$result['err_code_des'] = $result['message'];
				}

				wl_message('微信钱包打款失败: ' . $result['err_code_des'], '', 'error');
			}
		}
		else {
			wl_message('申请金额错误！', referer(), 'error');
		}
	}

	public function tofinish()
	{
		global $_W;
		global $_GPC;
		$appid = $_GPC['id'];
		$res = pdo_update(PDO_NAME . 'settlement_record', array('status' => 9, 'updatetime' => time(), 'settletype' => 3), array('id' => $appid));
		$apply = pdo_get(PDO_NAME . 'settlement_record', array('id' => $appid));

		if ($res) {
			$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $apply['mid']), 'openid');
			$url = app_url('distribution/disappbase/apply', array('type' => 'finish'));
			Distribution::distriNotice($openid, $url, 6, 0, $apply['sapplymoney'], '线下打款');
			wl_message('标记打款成功！', referer(), 'success');
		}
		else {
			wl_message('标记打款失败！', referer(), 'error');
		}
	}

	public function unbind()
	{
		global $_W;
		global $_GPC;
		$res = pdo_update('wlmerchant_distributor', array('leadid' => 0), array('id' => $_GPC['id']));

		if ($res) {
			show_json(1, '解除绑定成功');
		}
		else {
			show_json(0, '解除绑定失败，请重试');
		}
	}

	public function passdis()
	{
		global $_W;
		global $_GPC;
		$appid = $_GPC['id'];
		$base = Setting::wlsetting_read('distribution');
		$res = pdo_update('wlmerchant_applydistributor', array('status' => 1), array('id' => $appid));

		if ($res) {
			$appdis = pdo_get('wlmerchant_applydistributor', array('id' => $appid), array('mobile', 'realname', 'mid', 'rank'));
			$distributor = pdo_get('wlmerchant_distributor', array('mid' => $appdis['mid'], 'uniacid' => $_W['uniacid']), array('id', 'leadid'));

			if ($distributor) {
				if ($appdis['rank'] == 1 && $base['mode']) {
					$data['leadid'] = -1;
				}

				$data['disflag'] = 1;
				$data['lockflag'] = 0;
				$res2 = pdo_update('wlmerchant_distributor', $data, array('mid' => $appdis['mid']));
			}
			else {
				$nickname = pdo_getcolumn('wlmerchant_member', array('id' => $appdis['mid']), 'nickname');
				$data2 = array('uniacid' => $_W['uniacid'], 'mid' => $appdis['mid'], 'disflag' => 1, 'leadid' => -1, 'dismoney' => 0, 'nowmoney' => 0, 'nickname' => $nickname, 'realname' => $appdis['realname'], 'mobile' => $appdis['mobile'], 'createtime' => time());
				pdo_insert('wlmerchant_distributor', $data2);
				$res2 = pdo_insertid();
				pdo_update('wlmerchant_member', array('distributorid' => $res2), array('id' => $appdis['mid']));
			}

			if ($res2) {
				$url = app_url('distribution/disappbase/index');
				$mid = pdo_getcolumn(PDO_NAME . 'applydistributor', array('id' => $appid), 'mid');
				$openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $mid), 'openid');
				Distribution::distriNotice($openid, $url, 1);
				wl_message('审核通过成功！', referer(), 'success');
			}
			else {
				wl_message('审核通过失败！', referer(), 'error');
			}
		}
		else {
			wl_message('审核通过失败！请联系管理员', referer(), 'error');
		}
	}

	public function rejectreason()
	{
		global $_W;
		global $_GPC;
		$appid = $_GPC['id'];
		$reason = $_GPC['reason'];
		$res = pdo_update('wlmerchant_applydistributor', array('status' => 2, 'reason' => $reason), array('id' => $appid));

		if ($res) {
			exit(json_encode(array('errno' => 0)));
		}
		else {
			exit(json_encode(array('errno' => 1)));
		}
	}

	public function statistics()
	{
		global $_W;
		global $_GPC;
		$disid = $_GPC['disid'];
		$allmoney = 0;
		$orders = pdo_fetchall('SELECT orderprice FROM ' . tablename('wlmerchant_disorder') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status > 0 AND (oneleadid = ' . $disid . ' or twoleadid = ' . $disid . ' or threeleadid = ' . $disid . ' ) ORDER BY id DESC'));

		if ($orders) {
			foreach ($orders as $key => $order) {
				$allmoney += $order['orderprice'];
			}
		}

		$allmoney = number_format($allmoney, 2);
		exit(json_encode(array('errno' => 0, 'message' => $allmoney)));
	}

	public function canceldis()
	{
		global $_W;
		global $_GPC;
		$disid = $_GPC['id'];
		$mid = pdo_getcolumn('wlmerchant_member', array('distributorid' => $disid), 'id');
		pdo_update('wlmerchant_member', array('distributorid' => 0), array('id' => $mid));
		$res = pdo_delete('wlmerchant_distributor', array('id' => $disid));

		if ($res) {
			$members = pdo_getall('wlmerchant_distributor', array('leadid' => $mid), array('id'));

			if ($members) {
				foreach ($members as $key => $value) {
					pdo_update('wlmerchant_distributor', array('leadid' => 0), array('id' => $value['id']));
				}
			}

			pdo_delete('wlmerchant_applydistributor', array('mid' => $mid));

			if ($res) {
				exit(json_encode(array('errno' => 0)));
			}
		}
		else {
			exit(json_encode(array('errno' => 1)));
		}
	}

	public function adddistributor()
	{
		global $_W;
		global $_GPC;
		$base = Setting::wlsetting_read('distribution');
		$memid = $_GPC['memid'];
		$todo = $_GPC['todo'] ? $_GPC['todo'] : 'appdislist';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where = array();
		$where['uniacid'] = $_W['uniacid'];
		$disid = pdo_getcolumn('wlmerchant_distributor', array('uniacid' => $_W['uniacid'], 'mid' => $memid), 'id');
		$messagesaler = pdo_get('wlmerchant_member', array('id' => $memid), array('mobile', 'realname', 'nickname', 'avatar', 'distributorid'));

		if ($todo == 'base') {
			$distributor = pdo_get('wlmerchant_distributor', array('mid' => $memid), array('nowmoney', 'dismoney'));
			$messagesaler['nowmoney'] = $distributor['nowmoney'];
			$messagesaler['dismoney'] = $distributor['dismoney'];
			$applymoney = $cashmoney = $successmoney = 0;
			$apply = pdo_getall(PDO_NAME . 'settlement_record', array('mid' => $memid, 'type' => 3), array('sgetmoney', 'status'));

			if ($apply) {
				foreach ($apply as $key => $app) {
					if ($app['status'] == 6 || $app['status'] == 7) {
						$applymoney += $app['sgetmoney'];
					}
					else if ($app['status'] == 8) {
						$cashmoney += $app['sgetmoney'];
					}
					else {
						if ($app['status'] == 9) {
							$successmoney += $app['sgetmoney'];
						}
					}
				}
			}

			$applymoney = number_format($applymoney, 2);
			$cashmoney = number_format($cashmoney, 2);
			$successmoney = number_format($successmoney, 2);
		}
		else if ($todo == 'lowpeople') {
			$where['leadid'] = $memid;
			$where['lockflag'] = 0;

			if ($_GPC['disflag']) {
				$where['disflag'] = 1;
			}

			$type = intval($_GPC['type']);
			$keyword = trim($_GPC['keyword']);

			if (!empty($keyword)) {
				switch ($type) {
				case 2:
					$where['mobile@'] .= $keyword;
					break;

				case 3:
					$where['nickname@'] .= $keyword;
					break;

				case 4:
					$where['realname@'] .= $keyword;
					break;

				case 5:
					$where['mid@'] .= $keyword;
					break;
				}
			}

			$lowpeople = Util::getNumData('mid,id', PDO_NAME . 'distributor', $where, 'ID DESC', $pindex, $psize, 1);
			$pager = $lowpeople[1];
			$lowpeople = $lowpeople[0];

			foreach ($lowpeople as $key => &$peo) {
				$member = pdo_get('wlmerchant_member', array('id' => $peo['mid']), array('mobile', 'realname', 'nickname', 'avatar'));
				$peo['nickname'] = $member['nickname'];
				$peo['realname'] = $member['realname'];
				$peo['mobile'] = $member['mobile'];
				$peo['avatar'] = $member['avatar'];
				$peo['leadmid'] = $memid;
				$peo['leadname'] = $messagesaler['nickname'];
			}
		}
		else if ($todo == 'loworder') {
			$loworderwhere['uniacid'] = $_W['uniacid'];

			if ($_GPC['time']) {
				$time = $_GPC['time'];
				$starttime = strtotime($time['start']);
				$endtime = strtotime($time['end']);
				$loworderwhere['createtime>'] = $starttime;
				$loworderwhere['createtime<'] = $endtime;
			}

			if ($_GPC['ordertype']) {
				$loworderwhere['plugin'] = $_GPC['ordertype'];
			}

			if (empty($starttime) || empty($endtime)) {
				$starttime = strtotime('-1 month');
				$endtime = time();
			}

			if ($_GPC['buymid']) {
				$buymid = $_GPC['buymid'];
				$loworderwhere['buymid'] = $buymid;
			}

			if ($_GPC['disorder']) {
				$loworderwhere['id'] = $_GPC['disorder'];
			}

			$loworderwhere['no*'] = '(oneleadid = ' . $disid . ' or twoleadid = ' . $disid . ' or threeleadid = ' . $disid . ' )';

			if ($_GPC['export'] != '') {
				$this->exportloworder($loworderwhere, $disid);
			}

			$loworder = Util::getNumData('*', PDO_NAME . 'disorder', $loworderwhere, 'ID DESC', $pindex, $psize, 1);
			$pager = $loworder[1];
			$loworder = $loworder[0];

			foreach ($loworder as $key => &$order) {
				if ($order['plugin'] == 'rush') {
					$rush = pdo_get('wlmerchant_rush_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $rush['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = Rush::getSingleActive($rush['activityid'], 'name,thumb,unit');
					$order['gnum'] = $rush['num'];
					$order['goodsprice'] = $rush['price'] / $rush['num'];
					$order['paytype'] = $rush['paytype'];
					$order['merchantname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $rush['sid']), 'storename');
					$order['orderno'] = $rush['orderno'];
					$order['gname'] = $goods['name'];
					$order['gimg'] = $goods['thumb'];
					$order['unit'] = $goods['unit'];
				}
				else if ($order['plugin'] == 'fightgroup') {
					$fightgroup = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $fightgroup['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = Wlfightgroup::getSingleGood($fightgroup['fkid'], 'name,logo,unit');
					$order['gnum'] = $fightgroup['num'];
					$order['paytype'] = $fightgroup['paytype'];
					$order['goodsprice'] = $order['orderprice'] / $fightgroup['num'];
					$order['merchantname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $fightgroup['sid']), 'storename');
					$order['orderno'] = $fightgroup['orderno'];
					$order['gname'] = $goods['name'];
					$order['gimg'] = $goods['logo'];
					$order['unit'] = $goods['unit'];
				}
				else if ($order['plugin'] == 'coupon') {
					$coupon = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $coupon['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = wlCoupon::getSingleCoupons($coupon['fkid'], 'title,logo');
					$order['gnum'] = $coupon['num'];
					$order['paytype'] = $coupon['paytype'];
					$order['goodsprice'] = $order['orderprice'] / $coupon['num'];
					$order['merchantname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $coupon['sid']), 'storename');
					$order['orderno'] = $coupon['orderno'];
					$order['gname'] = $goods['title'];
					$order['gimg'] = $goods['logo'];
					$order['unit'] = '张';
				}
				else if ($order['plugin'] == 'pocket') {
					$pocket = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $pocket['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = pdo_get('wlmerchant_pocket_informations', array('id' => $pocket['fkid']), array('share_title', 'type'));
					$type = pdo_get('wlmerchant_pocket_type', array('id' => $goods['type']), array('title', 'img'));
					$order['gnum'] = 1;
					$order['goodsprice'] = $order['orderprice'];
					$order['paytype'] = $pocket['paytype'];
					$order['merchantname'] = '掌上信息';
					$order['orderno'] = $pocket['orderno'];

					if ($goods['share_title']) {
						$order['gname'] = $goods['share_title'];
					}
					else {
						$order['gname'] = $type['title'];
					}

					$order['gimg'] = $type['img'];
					$order['unit'] = '次';
				}
				else if ($order['plugin'] == 'halfcard') {
					$halforder = pdo_get('wlmerchant_halfcard_record', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $halforder['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = pdo_get('wlmerchant_halfcard_type', array('id' => $halforder['typeid']), array('name', 'days', 'logo'));
					$order['paytype'] = $halforder['paytype'];
					$order['gnum'] = $goods['days'];
					$order['goodsprice'] = $order['orderprice'];
					$order['merchantname'] = '一卡通充值';
					$order['orderno'] = $halforder['orderno'];
					$order['gname'] = $goods['name'];
					$order['gimg'] = $member['avatar'];
					$order['unit'] = '天';
				}
				else if ($order['plugin'] == 'charge') {
					$chargeorder = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $chargeorder['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = pdo_get('wlmerchant_chargelist', array('id' => $chargeorder['fkid']), array('name', 'days'));
					$merchantdata = pdo_get('wlmerchant_merchantdata', array('id' => $chargeorder['sid']), array('storename', 'logo'));
					$order['gnum'] = $goods['days'];
					$order['goodsprice'] = $order['orderprice'];
					$order['paytype'] = $chargeorder['paytype'];
					$order['merchantname'] = $merchantdata['storename'];
					$order['orderno'] = $chargeorder['orderno'];
					$order['gname'] = $goods['name'];
					$order['gimg'] = $merchantdata['logo'];
					$order['unit'] = '天';
				}
				else if ($order['plugin'] == 'distribution') {
					$chargeorder = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $chargeorder['mid']), array('mobile', 'nickname', 'avatar'));
					$order['paytype'] = $chargeorder['paytype'];
					$order['gnum'] = 1;
					$order['goodsprice'] = $order['orderprice'];
					$order['merchantname'] = '平台业务';
					$order['orderno'] = $chargeorder['orderno'];
					$order['gname'] = '付费申请分销商';
					$order['gimg'] = $member['avatar'];
					$order['unit'] = '';
				}
				else if ($order['plugin'] == 'groupon') {
					$groupon = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $groupon['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = Groupon::getSingleActive($groupon['fkid'], 'name,thumb,unit');
					$order['paytype'] = $groupon['paytype'];
					$order['gnum'] = $groupon['num'];
					$order['goodsprice'] = $groupon['price'] / $groupon['num'];
					$order['merchantname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $groupon['sid']), 'storename');
					$order['orderno'] = $groupon['orderno'];
					$order['gname'] = $goods['name'];
					$order['gimg'] = $goods['thumb'];
					$order['unit'] = $goods['unit'];
				}
				else if ($order['plugin'] == 'consumption') {
					$groupon = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $groupon['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = pdo_get(PDO_NAME . 'consumption_goods', array('id' => $groupon['fkid']), array('thumb', 'title'));
					$order['paytype'] = $groupon['paytype'];
					$order['gnum'] = 1;
					$epxressprice = pdo_getcolumn(PDO_NAME . 'express', array('id' => $groupon['expressid']), 'expressprice');
					$order['goodsprice'] = sprintf('%.2f', $groupon['price'] - $epxressprice);
					$order['merchantname'] = '积分商城';
					$order['orderno'] = $groupon['orderno'];
					$order['gname'] = $goods['title'];
					$order['gimg'] = $goods['thumb'];
					$order['unit'] = '份';
				}
				else if ($order['plugin'] == 'payonline') {
					$payorder = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $payorder['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = pdo_get('wlmerchant_halfcardlist', array('id' => $payorder['fkid']), array('title'));
					$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $payorder['sid']), array('storename', 'logo'));

					if (empty($goods['title'])) {
						$goods['title'] = $merchant['storename'];
					}

					$order['gnum'] = 1;
					$order['goodsprice'] = $order['orderprice'];
					$order['merchantname'] = $merchant['storename'];
					$order['orderno'] = $payorder['orderno'];
					$order['gname'] = $goods['title'] . '在线买单';
					$order['gimg'] = $merchant['logo'];
					$order['unit'] = '次';
				}
				else {
					if ($order['plugin'] == 'bargain') {
						$payorder = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
						$member = pdo_get('wlmerchant_member', array('id' => $payorder['mid']), array('mobile', 'nickname', 'avatar'));
						$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $payorder['fkid']), array('name', 'unit', 'thumb'));
						$merchant = pdo_get('wlmerchant_merchantdata', array('id' => $payorder['sid']), array('storename'));
						$order['gnum'] = 1;
						$order['goodsprice'] = $order['orderprice'];
						$order['merchantname'] = $merchant['storename'];
						$order['orderno'] = $payorder['orderno'];
						$order['gname'] = $goods['name'];
						$order['gimg'] = $goods['thumb'];
						$order['unit'] = $goods['unit'];
					}
				}

				$order['nickname'] = $member['nickname'];
				$order['mobile'] = $member['mobile'];
				$order['avatar'] = $member['avatar'];

				if ($order['status'] == 0) {
					$order['statusCss'] = 'default';
					$order['statusName'] = '不可结算';
				}

				if ($order['status'] == 1) {
					$order['statusCss'] = 'info';
					$order['statusName'] = '可结算';
				}
				else {
					if ($order['status'] == 2) {
						$order['statusCss'] = 'success';
						$order['statusName'] = '已结算';
					}
				}

				$leadmoney = unserialize($order['leadmoney']);

				if ($base['mode']) {
					if ($order['oneleadid'] == $disid && $order['twoleadid'] == $disid) {
						$order['rank'] = 1;
						$order['leadmoney'] = sprintf('%.2f', $leadmoney['one'] + $leadmoney['two']);
					}
					else if ($order['oneleadid'] == $disid) {
						$order['leadmoney'] = $leadmoney['one'];
						$order['rank'] = 1;
					}
					else if ($order['twoleadid'] == $disid) {
						$order['leadmoney'] = $leadmoney['two'];
						$order['rank'] = 2;
					}
					else {
						if ($order['threeleadid'] == $disid) {
							$order['leadmoney'] = $leadmoney['three'];
							$order['rank'] = 3;
						}
					}
				}
				else if ($order['oneleadid'] == $disid) {
					$order['leadmoney'] = $leadmoney['one'];
					$order['rank'] = 1;
				}
				else if ($order['twoleadid'] == $disid) {
					$order['leadmoney'] = $leadmoney['two'];
					$order['rank'] = 2;
				}
				else {
					if ($order['threeleadid'] == $disid) {
						$order['leadmoney'] = $leadmoney['three'];
						$order['rank'] = 3;
					}
				}
			}
		}
		else if ($todo == 'applylist') {
			$where['mid'] = $memid;
			$applylist = Util::getNumData('*', PDO_NAME . 'settlement_record', $where, 'ID DESC', $pindex, $psize, 1);
			$pager = $applylist[1];
			$applylist = $applylist[0];

			if ($applylist) {
				foreach ($applylist as $key => &$apply) {
					$member = pdo_get('wlmerchant_member', array('id' => $memid), array('mobile', 'avatar', 'nickname'));
					$apply['avatar'] = $member['avatar'];
					$apply['mobile'] = $member['mobile'];
					$apply['nickname'] = $member['nickname'];
				}
			}
		}
		else if ($todo == 'appdislist') {
			$where['status'] = 0;
			$applydislist = Util::getNumData('*', PDO_NAME . 'applydistributor', $where, 'ID DESC', $pindex, $psize, 1);
			$pager = $applydislist[1];
			$dislist = $applydislist[0];

			if ($dislist) {
				foreach ($dislist as $key => &$appdis) {
					$mem = pdo_get('wlmerchant_member', array('id' => $appdis['mid']), array('avatar', 'nickname'));
					$appdis['avatar'] = $mem['avatar'];
					$appdis['nickname'] = $mem['nickname'];
				}
			}
		}
		else {
			if ($todo == 'payrecord') {
				$payrecord = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_order') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 3 AND plugin = \'distribution\' ORDER BY paytime DESC'));

				foreach ($payrecord as $key => &$reco) {
					$member = pdo_get('wlmerchant_member', array('id' => $reco['mid']), array('avatar', 'nickname'));
					$reco['avatar'] = $member['avatar'];
					$reco['nickname'] = $member['nickname'];
				}
			}
		}

		foreach ($loworder as $k => &$v) {
			if ($v['plugin'] == 'rush') {
				$stateOrder = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $v['orderid']), 'status');
			}
			else if ($v['plugin'] == 'halfcard') {
				$stateOrder = 1;
			}
			else {
				$stateOrder = pdo_getcolumn(PDO_NAME . 'order', array('id' => $v['orderid']), 'status');
			}

			if ($stateOrder == 7) {
				$v['statusName'] = '已退款';
				$v['statusCss'] = 'default';
				continue;
			}
		}

		include wl_template('disysbase/adddistributor');
	}

	public function searchmember()
	{
		global $_W;
		global $_GPC;
		$con = $con2 = 'uniacid=\'' . $_W['uniacid'] . '\' ';
		$keyword = $_GPC['keyword'];

		if ($keyword != '') {
			$con .= ' and nickname LIKE \'%' . $keyword . '%\' or uid  LIKE \'%' . $keyword . '%\' or openid LIKE \'%' . $keyword . '%\'';
			$con2 .= ' and nickname LIKE \'%' . $keyword . '%\' or uid  LIKE \'%' . $keyword . '%\'';
		}

		$ds = pdo_fetchall('select * from' . tablename('wlmerchant_member') . ('where ' . $con));
		include wl_template('disysbase/searchmember');
	}

	public function exportloworder($where, $disid)
	{
		global $_W;
		global $_GPC;
		$loworder = Util::getNumData('*', PDO_NAME . 'disorder', $where, 'ID DESC', 0, 0, 1);
		$loworder = $loworder[0];

		foreach ($loworder as $key => &$order) {
			if ($order['plugin'] == 'rush') {
				$rush = pdo_get('wlmerchant_rush_order', array('id' => $order['orderid']));
				$member = pdo_get('wlmerchant_member', array('id' => $rush['mid']), array('mobile', 'nickname', 'avatar'));
				$goods = Rush::getSingleActive($rush['activityid'], 'name,thumb,unit');
				$order['gnum'] = $rush['num'];
				$order['goodsprice'] = $rush['price'] / $rush['num'];
				$order['merchantname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $rush['sid']), 'storename');
				$order['orderno'] = $rush['orderno'];
				$order['gname'] = $goods['name'];
				$order['gimg'] = $goods['thumb'];
				$order['unit'] = $goods['unit'];
				$order['plugin'] = '抢购订单';
			}
			else if ($order['plugin'] == 'fightgroup') {
				$fightgroup = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
				$member = pdo_get('wlmerchant_member', array('id' => $fightgroup['mid']), array('mobile', 'nickname', 'avatar'));
				$goods = Wlfightgroup::getSingleGood($fightgroup['fkid'], 'name,logo,unit');
				$order['gnum'] = $fightgroup['num'];
				$order['goodsprice'] = $order['orderprice'] / $fightgroup['num'];
				$order['merchantname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $fightgroup['sid']), 'storename');
				$order['orderno'] = $fightgroup['orderno'];
				$order['gname'] = $goods['name'];
				$order['gimg'] = $goods['logo'];
				$order['unit'] = $goods['unit'];
				$order['plugin'] = '拼团订单';
			}
			else if ($order['plugin'] == 'coupon') {
				$coupon = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
				$member = pdo_get('wlmerchant_member', array('id' => $coupon['mid']), array('mobile', 'nickname', 'avatar'));
				$goods = wlCoupon::getSingleCoupons($coupon['fkid'], 'title,logo');
				$order['gnum'] = $coupon['num'];
				$order['goodsprice'] = $order['orderprice'] / $coupon['num'];
				$order['merchantname'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $coupon['sid']), 'storename');
				$order['orderno'] = $coupon['orderno'];
				$order['gname'] = $goods['title'];
				$order['gimg'] = $goods['logo'];
				$order['unit'] = '张';
				$order['plugin'] = '超级券订单';
			}
			else if ($order['plugin'] == 'pocket') {
				$pocket = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
				$member = pdo_get('wlmerchant_member', array('id' => $pocket['mid']), array('mobile', 'nickname', 'avatar'));
				$goods = pdo_get('wlmerchant_pocket_informations', array('id' => $pocket['fkid']), array('share_title', 'type'));
				$type = pdo_get('wlmerchant_pocket_type', array('id' => $goods['type']), array('title', 'img'));
				$order['gnum'] = 1;
				$order['goodsprice'] = $order['orderprice'];
				$order['merchantname'] = '掌上信息';
				$order['orderno'] = $pocket['orderno'];

				if ($goods['share_title']) {
					$order['gname'] = $goods['share_title'];
				}
				else {
					$order['gname'] = $type['title'];
				}

				$order['gimg'] = $type['img'];
				$order['unit'] = '次';
				$order['plugin'] = '掌上信息';
			}
			else if ($order['plugin'] == 'halfcard') {
				$halforder = pdo_get('wlmerchant_halfcard_record', array('id' => $order['orderid']));
				$member = pdo_get('wlmerchant_member', array('id' => $halforder['mid']), array('mobile', 'nickname', 'avatar'));
				$goods = pdo_get('wlmerchant_halfcard_type', array('id' => $halforder['typeid']), array('name', 'days', 'logo'));
				$order['gnum'] = $goods['days'];
				$order['goodsprice'] = $order['orderprice'];
				$order['merchantname'] = '一卡通充值';
				$order['orderno'] = $halforder['orderno'];
				$order['gname'] = $goods['name'];
				$order['gimg'] = $goods['logo'];
				$order['unit'] = '天';
				$order['plugin'] = '一卡通充值';
			}
			else {
				if ($order['plugin'] == 'charge') {
					$chargeorder = pdo_get('wlmerchant_order', array('id' => $order['orderid']));
					$member = pdo_get('wlmerchant_member', array('id' => $chargeorder['mid']), array('mobile', 'nickname', 'avatar'));
					$goods = pdo_get('wlmerchant_chargelist', array('id' => $chargeorder['fkid']), array('name', 'days'));
					$merchantdata = pdo_get('wlmerchant_merchantdata', array('id' => $chargeorder['sid']), array('storename', 'logo'));
					$order['gnum'] = $goods['days'];
					$order['goodsprice'] = $order['orderprice'];
					$order['merchantname'] = $merchantdata['storename'];
					$order['orderno'] = $chargeorder['orderno'];
					$order['gname'] = $goods['name'];
					$order['gimg'] = $merchantdata['logo'];
					$order['unit'] = '天';
					$order['plugin'] = '付费入驻';
				}
			}

			$order['nickname'] = $member['nickname'];
			$order['mobile'] = $member['mobile'];
			$order['avatar'] = $member['avatar'];

			if ($order['status'] == 0) {
				$order['statusCss'] = 'default';
				$order['statusName'] = '不可结算';
			}

			if ($order['status'] == 1) {
				$order['statusCss'] = 'info';
				$order['statusName'] = '可结算';
			}
			else {
				if ($order['status'] == 2) {
					$order['statusCss'] = 'success';
					$order['statusName'] = '已结算';
				}
			}

			$leadmoney = unserialize($order['leadmoney']);

			if ($order['oneleadid'] == $disid) {
				$order['leadmoney'] = $leadmoney['one'];
				$order['rank'] = '一级订单';
			}
			else if ($order['twoleadid'] == $disid) {
				$order['leadmoney'] = $leadmoney['two'];
				$order['rank'] = '二级订单';
			}
			else {
				if ($order['threeleadid'] == $disid) {
					$order['leadmoney'] = $leadmoney['three'];
					$order['rank'] = '三级订单';
				}
			}
		}

		$filter = array('id' => '订单ID', 'orderno' => '订单编号', 'plugin' => '订单类型', 'gname' => '商品名称', 'merchantname' => '商户名称', 'nickname' => '买家姓名', 'mobile' => '买家电话', 'orderprice' => '订单金额', 'leadmoney' => '提成金额', 'rank' => '订单等级', 'statusName' => '订单状态', 'createtime' => '创建时间');
		$data = array();
		$i = 0;

		while ($i < count($loworder)) {
			foreach ($filter as $key => $title) {
				if ($key == 'createtime') {
					$data[$i][$key] = date('Y-m-d H:i:s', $loworder[$i][$key]);
				}
				else {
					$data[$i][$key] = $loworder[$i][$key];
				}
			}

			++$i;
		}

		util_csv::export_csv_2($data, $filter, '下级订单列表.csv');
		exit();
	}

	public function settop()
	{
		global $_W;
		global $_GPC;
		$memid = $_GPC['memid'];
		$leadid = $_GPC['mid'];

		if ($memid) {
			if ($leadid) {
				$res1 = pdo_update('wlmerchant_distributor', array('leadid' => $leadid), array('mid' => $memid));
			}
			else {
				$res2 = pdo_update('wlmerchant_distributor', array('leadid' => 0), array('mid' => $memid));
			}
		}
		else {
			wl_message('获取用户id失败！', referer(), 'error');
		}

		if ($res1) {
			wl_message('修改上级成功！', referer(), 'success');
		}
		else if ($res2) {
			wl_message('取消上级成功！', referer(), 'success');
		}
		else {
			wl_message('修改上级失败！', referer(), 'error');
		}
	}

	public function cansett()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$order = pdo_get('wlmerchant_disorder', array('id' => $id), array('status'));

		if ($order['status']) {
			show_json(0, '状态错误');
		}
		else {
			$res = pdo_update('wlmerchant_disorder', array('status' => 1), array('id' => $id, 'status' => 0));
		}

		if ($res) {
			show_json(1);
		}
		else {
			show_json(0, '修改失败，请重试');
		}
	}

	public function dislevel()
	{
		global $_W;
		global $_GPC;
		$base = Setting::wlsetting_read('distribution');
		$default = pdo_getcolumn('wlmerchant_dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'id');

		if (empty($default)) {
			$default = array('uniacid' => $_W['uniacid'], 'name' => '默认', 'createtime' => time(), 'isdefault' => 1);
			$res = pdo_insert(PDO_NAME . 'dislevel', $default);

			if (!$res) {
				wl_message('初始化失败！请重试', referer(), 'error');
			}
		}

		$levels = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_dislevel') . ('WHERE uniacid = ' . $_W['uniacid'] . ' ORDER BY isdefault DESC,createtime ASC'));
		include wl_template('disysbase/dislevel');
	}

	public function deletelevel()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];

		if ($id) {
			$res = pdo_delete('wlmerchant_dislevel', array('id' => $id));
		}

		if ($res) {
			show_json(1);
		}
	}

	public function editdistributor()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$distri = pdo_get('wlmerchant_distributor', array('id' => $id));

		if (empty($distri['dislevel'])) {
			$distri['dislevel'] = pdo_getcolumn(PDO_NAME . 'dislevel', array('uniacid' => $_W['uniacid'], 'isdefault' => 1), 'id');
		}

		$mid = pdo_getcolumn('wlmerchant_distributor', array('id' => $id), 'mid');
		$levels = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_dislevel') . ('WHERE uniacid = ' . $_W['uniacid'] . ' ORDER BY createtime ASC'));

		if ($_W['wlsetting']['distribution']['mode']) {
			$leadlists = pdo_fetchall('SELECT nickname,mid,mobile FROM ' . tablename('wlmerchant_distributor') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disflag = 1 AND leadid < 0 AND mid != ' . $mid . ' ORDER BY createtime ASC'));
		}
		else {
			$leadlists = pdo_fetchall('SELECT nickname,mid,mobile FROM ' . tablename('wlmerchant_distributor') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND disflag = 1 AND mid != ' . $mid . ' ORDER BY createtime ASC'));
		}

		if ($_W['ispost']) {
			$data = array('nickname' => trim($_GPC['nickname']), 'realname' => trim($_GPC['realname']), 'mobile' => trim($_GPC['mobile']), 'dislevel' => trim($_GPC['dislevel']), 'leadid' => trim($_GPC['leadid']), 'source' => trim($_GPC['source']));
			$res = pdo_update('wlmerchant_distributor', $data, array('id' => $id));
			$money = trim($_GPC['money']);
			if (is_numeric($money) && 0 < $money) {
				$money = sprintf('%.2f', $money);
				$type = $_GPC['moneytype'];
				$reason = $_GPC['reason'];

				if ($type == 1) {
					$onedismoney = $distri['dismoney'] + $money;
					$onenowmoney = $distri['nowmoney'] + $money;
				}
				else {
					$onedismoney = $distri['dismoney'] - $money;
					$onenowmoney = $distri['nowmoney'] - $money;
				}

				$changeflag = pdo_update('wlmerchant_distributor', array('dismoney' => $onedismoney, 'nowmoney' => $onenowmoney), array('id' => $distri['id']));

				if ($changeflag) {
					Distribution::adddisdetail(0, $distri['mid'], -1, $type, $money, 'system', 1, $reason, $onenowmoney);
				}
			}

			if ($res || $changeflag) {
				$memid = pdo_getcolumn(PDO_NAME . 'distributor', array('id' => $id), 'mid');
				pdo_update('wlmerchant_member', array('nickname' => $data['nickname'], 'realname' => $data['realname'], 'mobile' => $data['mobile']), array('id' => $memid));
				show_json(1, '操作成功');
			}
			else {
				show_json(0, '操作失败,请重试');
			}
		}

		include wl_template('disysbase/distrilmodel');
	}

	public function editlevel()
	{
		global $_W;
		global $_GPC;
		$base = Setting::wlsetting_read('distribution');
		$id = $_GPC['id'];

		if ($id) {
			$level = pdo_get('wlmerchant_dislevel', array('id' => $id));
			$level['plugin'] = unserialize($level['plugin']);
		}

		if ($_W['ispost']) {
			if ($id) {
				$data = array('name' => trim($_GPC['name']), 'onecommission' => $_GPC['onecommission'], 'twocommission' => $_GPC['twocommission'], 'threecommission' => $_GPC['threecommission'], 'upstandard' => trim($_GPC['upstandard']), 'ownstatus' => $_GPC['ownstatus'], 'plugin' => serialize($_GPC['plugin']));
				$res = pdo_update('wlmerchant_dislevel', $data, array('id' => $id));
			}
			else {
				$data = array('uniacid' => $_W['uniacid'], 'name' => trim($_GPC['name']), 'onecommission' => $_GPC['onecommission'], 'twocommission' => $_GPC['twocommission'], 'threecommission' => $_GPC['threecommission'], 'upstandard' => trim($_GPC['upstandard']), 'ownstatus' => $_GPC['ownstatus'], 'plugin' => serialize($_GPC['plugin']), 'createtime' => time());
				$res = pdo_insert('wlmerchant_dislevel', $data);
			}

			if ($res) {
				show_json(1, '操作成功');
			}
			else {
				show_json(0, '操作失败,请重试');
			}
		}

		include wl_template('disysbase/dislevelmodel');
	}

	public function disdetail()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$where['uniacid'] = $_W['uniacid'];

		if (!empty($_GPC['keyword'])) {
			if (!empty($_GPC['keywordtype'])) {
				switch ($_GPC['keywordtype']) {
				case 1:
					$where['leadid'] = $_GPC['keyword'];
					break;

				case 5:
					$where['buymid'] = $_GPC['keyword'];
					break;

				case 3:
					$where['price>'] = $_GPC['keyword'];
					break;

				case 4:
					$where['price<'] = $_GPC['keyword'];
					break;

				default:
					break;
				}

				if ($_GPC['keywordtype'] == 2) {
					$keyword = $_GPC['keyword'];
					$params[':nickname'] = '%' . $keyword . '%';
					$goods = pdo_fetchall('SELECT id,nickname FROM ' . tablename('wlmerchant_member') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND nickname LIKE :nickname'), $params);

					if ($goods) {
						$goodids = '(';

						foreach ($goods as $key => $v) {
							if ($key == 0) {
								$goodids .= $v['id'];
							}
							else {
								$goodids .= ',' . $v['id'];
							}
						}

						$goodids .= ')';
						$where['leadid#'] = $goodids;
					}
					else {
						$where['leadid#'] = '(0)';
					}
				}

				if ($_GPC['keywordtype'] == 6) {
					$keyword = $_GPC['keyword'];
					$params[':nickname'] = '%' . $keyword . '%';
					$goods = pdo_fetchall('SELECT id,nickname FROM ' . tablename('wlmerchant_member') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND nickname LIKE :nickname'), $params);

					if ($goods) {
						$goodids = '(';

						foreach ($goods as $key => $v) {
							if ($key == 0) {
								$goodids .= $v['id'];
							}
							else {
								$goodids .= ',' . $v['id'];
							}
						}

						$goodids .= ')';
						$where['buymid#'] = $goodids;
					}
					else {
						$where['buymid#'] = '(0)';
					}
				}
			}
		}

		if ($_GPC['orderstatus']) {
			$where['type'] = $_GPC['orderstatus'];
		}

		if ($_GPC['ordertype']) {
			$where['plugin'] = $_GPC['ordertype'];
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

		if ($_GPC['exportflag']) {
			$this->exportdetail($where);
		}

		$details = Util::getNumData('*', PDO_NAME . 'disdetail', $where, 'ID DESC', $pindex, $psize, 1);
		$pager = $details[1];
		$details = $details[0];

		foreach ($details as $key => &$detail) {
			$detail['leadname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $detail['leadid']), 'nickname');

			if ($detail['buymid'] < 0) {
				$detail['buyname'] = '系统';
			}
			else {
				$detail['buyname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $detail['buymid']), 'nickname');
			}

			$detail['typetext'] = $detail['type'] == 1 ? '收入' : '支出';

			switch ($detail['plugin']) {
			case 'rush':
				$detail['pluginname'] = '抢购订单';
				break;

			case 'groupon':
				$detail['pluginname'] = '团购订单';
				break;

			case 'fightgroup':
				$detail['pluginname'] = '拼团订单';
				break;

			case 'coupon':
				$detail['pluginname'] = '卡券订单';
				break;

			case 'pocket':
				$detail['pluginname'] = '掌上信息';
				break;

			case 'halfcard':
				$detail['pluginname'] = '一卡通';
				break;

			case 'charge':
				$detail['pluginname'] = '付费入驻';
				break;

			case 'distribution':
				$detail['pluginname'] = '付费申请分销商';
				break;

			case 'cash':
				$detail['pluginname'] = '分销申请提现';
				break;

			case 'system':
				$detail['pluginname'] = '后台修改:';
				break;

			case 'bargain':
				$detail['pluginname'] = '砍价活动:';
				break;

			case 'payonline':
				$detail['pluginname'] = '在线买单:';
				break;

			default:
				$detail['pluginname'] = '未知插件';
				break;
			}

			if ($detail['plugin'] != 'cash' && $detail['plugin'] != 'system') {
				$detail['orderurl'] = web_url('distribution/dissysbase/adddistributor', array('todo' => 'loworder', 'memid' => $detail['leadid'], 'disorder' => $detail['disorderid']));
			}

			if ($detail['plugin'] != 'cash' && $detail['plugin'] != 'distribution' && $detail['plugin'] != 'system') {
				if ($detail['rank'] == 1) {
					$detail['pluginname'] = $detail['pluginname'] . '一级分销';
				}
				else if ($detail['rank'] == 2) {
					$detail['pluginname'] = $detail['pluginname'] . '二级分销';
				}
				else {
					if ($detail['rank'] == 3) {
						$detail['pluginname'] = $detail['pluginname'] . '三级分销';
					}
				}
			}

			if ($detail['plugin'] == 'system') {
				$detail['pluginname'] = $detail['pluginname'] . $detail['reason'];
			}

			$detail['createtime'] = date('Y-m-d H:i:s', $detail['createtime']);
		}

		include wl_template('disysbase/disdetail');
	}

	public function exportdetail($where)
	{
		global $_W;
		global $_GPC;
		$details = Util::getNumData('*', PDO_NAME . 'disdetail', $where, 'ID DESC', 0, 0, 1);
		$details = $details[0];

		foreach ($details as $key => &$detail) {
			$detail['leadname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $detail['leadid']), 'nickname');

			if ($detail['buymid'] < 0) {
				$detail['buyname'] = '系统';
			}
			else {
				$detail['buyname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $detail['buymid']), 'nickname');
			}

			$detail['typetext'] = $detail['type'] == 1 ? '收入' : '支出';

			switch ($detail['plugin']) {
			case 'rush':
				$detail['pluginname'] = '抢购订单';
				break;

			case 'groupon':
				$detail['pluginname'] = '团购订单';
				break;

			case 'fightgroup':
				$detail['pluginname'] = '拼团订单';
				break;

			case 'coupon':
				$detail['pluginname'] = '卡券订单';
				break;

			case 'pocket':
				$detail['pluginname'] = '掌上信息';
				break;

			case 'halfcard':
				$detail['pluginname'] = '一卡通';
				break;

			case 'charge':
				$detail['pluginname'] = '付费入驻';
				break;

			case 'distribution':
				$detail['pluginname'] = '付费申请分销商';
				break;

			case 'cash':
				$detail['pluginname'] = '分销申请提现';
				break;

			default:
				$detail['pluginname'] = '未知插件';
				break;
			}

			if ($detail['plugin'] != 'cash' && $detail['plugin'] != 'distribution') {
				if ($detail['rank'] == 1) {
					$detail['pluginname'] = $detail['pluginname'] . '一级分销';
				}
				else if ($detail['rank'] == 2) {
					$detail['pluginname'] = $detail['pluginname'] . '二级分销';
				}
				else {
					if ($detail['rank'] == 3) {
						$detail['pluginname'] = $detail['pluginname'] . '三级分销';
					}
				}
			}

			$detail['createtime'] = date('Y-m-d H:i:s', $detail['createtime']);

			if ($detail['plugin'] == 'rush') {
				$orderid = pdo_getcolumn(PDO_NAME . 'disorder', array('id' => $detail['disorderid']), 'orderid');
				$detail['orderno'] = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $orderid), 'orderno');
			}
			else {
				if ($detail['plugin'] != 'cash' && $detail['plugin'] != 'system') {
					$orderid = pdo_getcolumn(PDO_NAME . 'disorder', array('id' => $detail['disorderid']), 'orderid');
					$detail['orderno'] = pdo_getcolumn(PDO_NAME . 'order', array('id' => $orderid), 'orderno');
				}
			}

			$detail['orderno'] = $detail['orderno'] . '	';
		}

		$filter = array('id' => '记录id', 'leadid' => '分销商MID', 'leadname' => '分销商姓名', 'orderno' => '订单编号', 'typetext' => '收支', 'price' => '金额', 'buyname' => '来源', 'pluginname' => '描述', 'createtime' => '时间');
		$data = array();

		foreach ($details as $k => $v) {
			foreach ($filter as $key => $title) {
				$data[$k][$key] = $v[$key];
			}
		}

		util_csv::export_csv_2($data, $filter, '分销商明细.csv');
		exit();
	}

	public function entry()
	{
		global $_W;
		global $_GPC;
		$set['url'] = app_url('distribution/disappbase/index');
		include wl_template('disysbase/entry');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
