<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class DistributionModuleUniapp extends Uniapp
{
	/**
     * Comment: 分销申请条件获取
     * Author: zzw
     * Date: 2019/7/16 9:20
     */
	public function applyCondition()
	{
		global $_W;
		global $_GPC;

		if ($this->disSetting['switch'] == 0) {
			$this->renderError('未开启分销功能');
		}

		$data['appdetail'] = htmlspecialchars_decode($this->disSetting['appdetail']);
		$data['rollstatus'] = $this->disSetting['rollstatus'];

		if ($this->disSetting['rollstatus'] == 1) {
			$list = pdo_fetchall('SELECT mid,sapplymoney FROM ' . tablename(PDO_NAME . 'settlement_record') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND `type` = 3 AND sapplymoney > 1  ORDER BY applytime DESC limit 20'));

			foreach ($list as $key => &$va) {
				$va['nickname'] = pdo_getcolumn(PDO_NAME . 'member', array('id' => $va['mid']), 'nickname');
				$va['nickname'] = mb_substr($va['nickname'], 0, 1, 'utf-8') . '***' . mb_substr($va['nickname'], -1, 1, 'utf-8');
				$va['applymoney'] = sprintf('%.2f', $va['sapplymoney']);
				unset($va['mid']);
				unset($va['sapplymoney']);
			}
		}
		else {
			if ($this->disSetting['rollstatus'] == 2) {
				$list = Cache::getCache('syssetting', 'cash_withdrawal_list');

				if (!$list) {
					$string = '半夜时分一个人躺在床上四处静谧无声有一种孤独的感觉如爬虫般悄悄爬上我的心头辗转反侧无法入眠轻轻起来戴上耳机听音乐打开书本曾经看见过这样一句话有一种心情叫';
					$string .= '无助有一种美丽叫孤独对耐不住寂寞的人来说孤独是可怕的是恐惧的而对我来说孤独是生命圆满的开始是一种静美不喧嚣不繁华是在静谧中独享一个人的清欢你看那独钓寒江雪的柳宗元是';
					$string .= '孤独的在下着大雪的江面上一叶小舟一个老渔翁独自在寒冷的江心垂钓天地之间是如此纯洁而寂静只剩下他一个人与万物共谋一尘不染万籁无声这清高孤傲的渔翁正是柳宗元自己在政治上';
					$string .= '失意郁闷和苦恼时隐居在山水之间寄托自己清高而孤傲情感的真实写照他的幽静过于孤独过于冷清不带一点人间烟火的气味其实人人骨子里皆有一份别人无法理解也无法自拔的孤独只是很';
					$string .= '多时候这孤独总会被周遭的喧嚣浮华所蒙蔽以致造成繁荣的假象殊不知不理会这种孤独在某种意义上而言我们便不算真正活过或许我们本就应该学会享受孤独在孤独的时候你才可以听到自';
					$string .= '己的心跳和呼吸寻找到迷失的自我知不觉我也喜欢上了孤独我的孤独和风月无关和苦闷无关有着浓浓的烟火气息只是一种一个人独处时的欢喜我喜欢孤独不与任何人说话在一份静谧中安然';
					$string .= '地做自己喜欢的事任身心徜徉暂时忘却柴米油盐酱醋茶的烦琐去体验琴棋书画诗酒花的高雅暂时抛开追名逐利的忙碌奔波去感受心无杂念的宁静淡泊暂时摆脱困扰你的喜怒哀乐去体味生活';
					$string .= '中的充实祥和于是体会孤独感受孤独不失为一种最佳的休闲身体可以在孤独中得到休养繁重的体力超负的劳动使身体需要有一份适时的孤独来调养心灵可以在孤独中寻找到一份难得的宁静';
					$string .= '不再为生活中尔虞我诈的争斗而烦恼不再为日常生活的重负而苦闷而在孤独中寻找适合调整心情的方式让心情在孤独中拥有一份独特的享受孤独的最高修为莫过于在孤独中创造亦或是读读';
					$string .= '古今写写心声种种花草多一份孤独的快乐少一份无为的浪费让生命在富有创造精神的孤独中度过让生命时光的每一分每一秒不至于虚度在孤独中拥有了自己的一切你会觉得你一点也不孤独';
					$string .= '于是你就会明白能够真正拥有孤独的人是世界上最幸福的人是的我就是这样享受孤独因为我只是万千世界中一株最不起眼的小草静是我的姿态淡是我的心境孤独是我的享受孤独是一种境界';
					$string .= '它折射出一个人潜藏的能量孤独是一味珍宝它蕴涵着高贵的情愫和追求孤独是一场燃烧它灿烂的火光给人温暖和力量孤独是一份爱因为无爱的人不会孤独';
					$string = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
					$lowestmoney = $this->disSetting['lowestmoney'];
					$list = array();
					$i = 0;

					while ($i < 20) {
						shuffle($string);
						$list[$i]['nickname'] = $string[0] . '***' . $string[1];
						$list[$i]['applymoney'] = sprintf('%.2f', rand(0, 500) + $lowestmoney);
						++$i;
					}

					Cache::setCache('syssetting', 'cash_withdrawal_list', $list);
				}
			}
		}

		$data['list'] = $list;
		$distributor = pdo_get(PDO_NAME . 'applydistributor', array('mid' => $_W['mid'], 'status' => 0));
		$data['is_apply'] = is_array($distributor) && 0 < count($distributor) ? 1 : 0;
		$distributorInfo = pdo_get(PDO_NAME . 'distributor', array('mid' => $_W['mid'], 'disflag' => 1));
		$data['is_dis'] = is_array($distributorInfo) && 0 < count($distributorInfo) ? 1 : 0;
		$data['user_link'] = app_url('member/user/index');
		$this->renderSuccess('申请条件信息', $data);
	}

	/**
     * Comment: 申请成为分销商
     * Author: zzw
     * Date: 2019/7/19 11:24
     */
	public function disApply()
	{
		global $_W;
		global $_GPC;
		$base = $this->disSetting;
		$rank = $_GPC['rank'] ? $_GPC['rank'] : 1;
		$distributor = pdo_get(PDO_NAME . 'distributor', array('mid' => $_W['mid']));
		$member = pdo_get(PDO_NAME . 'member', array('id' => $_W['mid']), array('mobile', 'nickname', 'realname'));
		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
		if (empty($member['mobile']) && in_array('distribution', $mastmobile)) {
			$this->renderError('您未绑定手机,请先绑定');
		}

		if ($base['switch'] == 0) {
			$this->renderError('分销商申请已关闭');
		}

		$isDis = pdo_fetchcolumn('SELECT id FROM ' . tablename(PDO_NAME . 'distributor') . (' WHERE mid = ' . $_W['mid'] . ' AND disflag IN (1,-1)  '));

		if ($isDis) {
			$this->renderError('申请失败，分销商不能进行申请');
		}

		$appflag = pdo_getcolumn(PDO_NAME . 'applydistributor', array('mid' => $_W['mid'], 'status' => 0), 'id');

		if ($appflag) {
			$this->renderError('请勿重复申请');
		}

		$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'realname' => $member['realname'], 'mobile' => $member['mobile'], 'createtime' => time());
		if ($base['mode'] == 1 && $rank == 2) {
			$examine = $base['twoexamine'];
			$applymoney = $base['twoapplymoney'];
			$appdis = $base['twoappdis'];
		}
		else {
			$examine = $base['examine'];
			$applymoney = $base['applymoney'];
			$appdis = $base['appdis'];
		}

		$url = app_url('distribution/disappbase/index');

		if ($appdis == 2) {
			$flag = Member::checkhalfmember();

			if ($flag) {
				if ($examine == 1) {
					$data['status'] = 0;
					$data['rank'] = $rank;
					$res = pdo_insert(PDO_NAME . 'applydistributor', $data);

					if ($res) {
						Distribution::toadmin($data['realname'], $_W['aid']);
						$this->renderSuccess('申请成功,请等待审核', array('state' => 3));
					}
					else {
						$this->renderError('申请失败，请稍后重试');
					}
				}
				else {
					if ($distributor && $distributor['disflag'] == 0) {
						if ($rank == 1 && $base['mode']) {
							$res = pdo_update(PDO_NAME . 'distributor', array('disflag' => 1, 'leadid' => -1, 'lockflag' => 0, 'realname' => $member['realname'], 'mobile' => $member['mobile']), array('id' => $distributor['id']));
						}
						else {
							$res = pdo_update(PDO_NAME . 'distributor', array('disflag' => 1, 'lockflag' => 0), array('id' => $distributor['id']));
						}
					}
					else {
						$data['disflag'] = 1;
						$data['realname'] = $member['realname'];
						$data['leadid'] = -1;
						$res = pdo_insert(PDO_NAME . 'distributor', $data);
						$disid = pdo_insertid();
						pdo_update(PDO_NAME . 'member', array('distributorid' => $disid), array('id' => $_W['mid']));
					}

					if ($res) {
						Distribution::distriNotice($_W['wlmember']['openid'], $url, 1);
						$this->renderSuccess('申请成功，您已成为分销商', array('state' => 1));
					}
					else {
						$this->renderError('申请失败，请稍后重试');
					}
				}
			}
			else {
				$this->renderError('申请失败,请先开启一卡通');
			}
		}
		else if ($appdis == 3) {
			$orderData = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'fkid' => 0, 'sid' => 0, 'status' => 0, 'paytype' => 0, 'createtime' => time(), 'orderno' => createUniontid(), 'price' => $applymoney, 'num' => 1, 'plugin' => 'distribution', 'payfor' => 'applydis', 'specid' => $rank);
			$res = pdo_insert(PDO_NAME . 'order', $orderData);

			if ($res) {
				$orderid = pdo_insertid();

				if ($orderid) {
					$this->renderSuccess('申请成功，请支付', array('state' => 2, 'url' => app_url('distribution/disappbase/topayorder', array('orderid' => $orderid))));
				}
				else {
					$this->renderError('申请失败,请稍后重试');
				}
			}
			else {
				$this->renderError('申请失败,请稍后重试');
			}
		}
		else if ($examine == 1) {
			$data['status'] = 0;
			$data['rank'] = $rank;
			$res = pdo_insert(PDO_NAME . 'applydistributor', $data);

			if ($res) {
				Distribution::toadmin($data['realname'], $_W['aid']);
				$this->renderSuccess('申请成功，请等待审核', array('state' => 3));
			}
			else {
				$this->renderError('申请失败，请稍后重试');
			}
		}
		else {
			if ($distributor && $distributor['disflag'] == 0) {
				if ($rank == 1 && $base['mode']) {
					$res = pdo_update(PDO_NAME . 'distributor', array('disflag' => 1, 'leadid' => -1, 'lockflag' => 0, 'realname' => $member['realname'], 'mobile' => $member['mobile']), array('id' => $distributor['id']));
				}
				else {
					$res = pdo_update(PDO_NAME . 'distributor', array('disflag' => 1, 'lockflag' => 0), array('id' => $distributor['id']));
				}
			}
			else {
				$data['disflag'] = 1;
				$data['realname'] = $member['realname'];
				$data['leadid'] = -1;
				$res = pdo_insert(PDO_NAME . 'distributor', $data);
				$disid = pdo_insertid();
				pdo_update(PDO_NAME . 'member', array('distributorid' => $disid), array('id' => $_W['mid']));
			}

			if ($res) {
				Distribution::distriNotice($_W['wlmember']['openid'], $url, 1);
				$this->renderSuccess('申请成功，您已成为分销商', array('state' => 1));
			}
			else {
				$this->renderError('申请失败，请稍后重试');
			}
		}
	}

	/**
     * Comment: 获取分销商详细信息
     * Author: zzw
     * Date: 2019/7/16 14:53
     */
	public function distributorInfo()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$field = 'id,leadid,dismoney,nowmoney,nickname,realname,dislevel,mobile';
		$info = pdo_fetch('SELECT ' . $field . ' FROM ' . tablename(PDO_NAME . 'distributor') . (' WHERE mid = ' . $mid . ' AND disflag = 1 '));

		if (!$info) {
			$this->renderError('请先成为分销商');
		}

		if (-1 < $info['leadid']) {
			$info['recommender_name'] = pdo_fetchcolumn('SELECT nickname FROM ' . tablename(PDO_NAME . 'member') . (' WHERE id = ' . $info['leadid'] . ' '));
		}

		$info['dislevel'] = 0 < $info['dislevel'] ? $info['dislevel'] : 1;
		$levelName = pdo_fetchcolumn('SELECT name FROM ' . tablename(PDO_NAME . 'dislevel') . (' WHERE id = ' . $info['dislevel'] . ' '));
		$info['level_name'] = $levelName ? $levelName : '无等级';
		$dayWhere = $monthWhere = ' a.status IN (1,2) AND (a.oneleadid = ' . $info['id'] . ' OR a.twoleadid = ' . $info['id'] . ' OR a.threeleadid = ' . $info['id'] . ') ';
		$field = 'a.leadmoney,a.oneleadid,a.twoleadid,a.threeleadid';
		$dayMoney = 0;
		$monthMoney = 0;
		$dayStart = strtotime(date('Y-m-d'));
		$dayEnd = strtotime(date('Y-m-d') . ' 23:59:59');
		$dayWhere .= ' AND b.createtime > ' . $dayStart . ' AND b.createtime < ' . $dayEnd . ' ';
		$dayList = Distribution::getDisOrder($dayWhere, $field);
		if (is_array($dayList) && 0 < count($dayList)) {
			foreach ($dayList as $dayK => $dayV) {
				$moneyArr = unserialize($dayV['leadmoney']);
				if ($dayV['oneleadid'] == $info['id'] && 0 < $moneyArr['one']) {
					$dayMoney += $moneyArr['one'];
				}
				else {
					if ($dayV['twoleadid'] == $info['id'] && 0 < $moneyArr['two']) {
						$dayMoney += $moneyArr['two'];
					}
					else {
						if ($dayV['threeleadid'] == $info['id'] && 0 < $moneyArr['three']) {
							$dayMoney += $moneyArr['three'];
						}
						else {
							$dayMoney += 0;
						}
					}
				}
			}
		}

		$monthStart = strtotime(date('Y-m') . '-1 ');
		$monthEnd = strtotime(date('Y-m-t') . ' 23:59:59');
		$monthWhere .= ' AND b.createtime > ' . $monthStart . ' AND b.createtime < ' . $monthEnd . ' ';
		$monthList = Distribution::getDisOrder($monthWhere, $field);
		if (is_array($monthList) && 0 < count($monthList)) {
			foreach ($monthList as $monthK => $monthV) {
				$moneyArr = unserialize($monthV['leadmoney']);
				if ($monthV['oneleadid'] == $info['id'] && 0 < $moneyArr['one']) {
					$monthMoney += $moneyArr['one'];
				}
				else {
					if ($monthV['twoleadid'] == $info['id'] && 0 < $moneyArr['two']) {
						$monthMoney += $moneyArr['two'];
					}
					else {
						if ($monthV['threeleadid'] == $info['id'] && 0 < $moneyArr['three']) {
							$monthMoney += $moneyArr['three'];
						}
						else {
							$monthMoney += 0;
						}
					}
				}
			}
		}

		$info['day_money'] = 0 < $dayMoney ? sprintf('%.2f', $dayMoney) : sprintf('%.2f', 0);
		$info['month_money'] = 0 < $monthMoney ? sprintf('%.2f', $monthMoney) : sprintf('%.2f', 0);
		$info['team_total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'distributor') . (' WHERE leadid = ' . $mid . ' AND disflag IN (0,1) AND lockflag = 0 '));
		$info['shop_total'] = 200;
		$extensionWhere = ' uniacid = ' . $_W['uniacid'] . ' AND (oneleadid = ' . $info['id'] . ' OR twoleadid = ' . $info['id'] . ' OR threeleadid = ' . $info['id'] . ') ';
		$info['order_total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'disorder') . (' WHERE ' . $extensionWhere . ' '));
		unset($info['leadid']);
		unset($info['dislevel']);
		$info['avatar'] = $_W['wlmember']['avatar'] ?: 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
		$info['nickname'] = $info['nickname'] ? $info['nickname'] : $info['realname'] ? $info['realname'] : $_W['wlmember']['nickname'] ?: '用户昵称';
		$info['invitation_posters_link'] = app_url('common/tools/poster', array('id' => $mid, 'type' => 'distribution'), true);
		$info['invitation_shop_link'] = app_url('dashboard/home/index', '', true);
		$info['show_lv'] = $this->disSetting['levelshow'];
		$this->renderSuccess('分销商详细信息', $info);
	}

	/**
     * Comment: 获取分销提现申请设置信息
     * Author: zzw
     * Date: 2019/7/16 15:45
     */
	public function getCashWithdrawalSet()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$frequency = $this->disSetting['frequency'];

		if (0 < $frequency) {
			$lastTime = pdo_fetchcolumn('SELECT applytime FROM ' . tablename(PDO_NAME . 'settlement_record') . (' WHERE mid = ' . $mid . ' ORDER BY applytime DESC '));

			if (0 < $lastTime) {
				$lastTime = $lastTime + $frequency * 86400;

				if (time() < $lastTime) {
					$distance = $lastTime - time();
					$day = floor($distance / 86400);
					$hour = floor($distance % 86400 / 3600);
					$str = '请于';
					0 < $day && ($str .= $day . '天');
					0 < $hour && ($str .= $hour . '时');
					$str .= '后进行提现申请';
					$this->renderError($str);
				}
			}
		}

		$data['payment_set'] = $this->disSetting['payment_type'];
		$data['min_money'] = 1 < $this->disSetting['lowestmoney'] ? $this->disSetting['lowestmoney'] : 1;
		$data['withdrawcharge'] = $this->disSetting['withdrawcharge'];
		$data['user_info'] = pdo_fetch('SELECT bank_name,card_number,bank_username,alipay FROM ' . tablename(PDO_NAME . 'member') . (' WHERE id = ' . $mid . ' '));
		$data['drawPrice'] = pdo_fetchcolumn('SELECT nowmoney FROM ' . tablename(PDO_NAME . 'distributor') . (' WHERE mid = ' . $mid));
		$this->renderSuccess('提现申请设置信息', $data);
	}

	/**
     * Comment: 分销商申请提现
     * Author: zzw
     * Date: 2019/7/16 17:17
     */
	public function cashWithdrawalApply()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		empty($_GPC['sapplymoney']) && $this->renderError('请输入申请提现金额');
		empty($_GPC['payment_type']) && $this->renderError('请选择打款方式');

		if ($_GPC['payment_type'] == 1) {
			empty($_GPC['alipay']) && $this->renderError('请输入支付宝账号');
			$alipay = array('alipay' => $_GPC['alipay'], 'id' => $mid);
			$res = pdo_get(PDO_NAME . 'member', $alipay);

			if (!$res) {
				unset($alipay['id']);
				$res = pdo_update(PDO_NAME . 'member', $alipay, array('id' => $mid));
				!$res && $this->renderError('支付宝账号保存失败，请联系管理员!');
			}
		}
		else {
			if ($_GPC['payment_type'] == 3) {
				empty($_GPC['bank_name']) && $this->renderError('请输入银行卡开户行');
				empty($_GPC['card_number']) && $this->renderError('请输入银行卡账号');
				empty($_GPC['bank_username']) && $this->renderError('请输入银行卡开户人的姓名');
				$bankCard = array('bank_name' => $_GPC['bank_name'], 'card_number' => $_GPC['card_number'], 'bank_username' => $_GPC['bank_username'], 'id' => $mid);
				$res = pdo_get(PDO_NAME . 'member', $bankCard);

				if (!$res) {
					unset($bankCard['id']);
					$res = pdo_update(PDO_NAME . 'member', $bankCard, array('id' => $mid));
					!$res && $this->renderError('银行卡信息保存失败，请联系管理员!');
				}
			}
		}

		$frequency = $this->disSetting['frequency'];

		if (0 < $frequency) {
			$lastTime = pdo_fetchcolumn('SELECT applytime FROM ' . tablename(PDO_NAME . 'settlement_record') . (' WHERE mid = ' . $mid . ' ORDER BY applytime DESC '));

			if (0 < $lastTime) {
				$lastTime = $lastTime + $frequency * 86400;

				if (time() < $lastTime) {
					$distance = $lastTime - time();
					$day = floor($distance / 86400);
					$hour = floor($distance % 86400 / 3600);
					$str = '请于';
					0 < $day && ($str .= $day . '天');
					0 < $hour && ($str .= $hour . '时');
					$str .= '后进行提现申请';
					$this->renderError($str);
				}
			}
		}

		$minMoney = 1 < $this->disSetting['lowestmoney'] ? $this->disSetting['lowestmoney'] : 1;

		if ($_GPC['sapplymoney'] < $minMoney) {
			$this->renderError('提现金额必须大于' . $minMoney . '元');
		}

		$distributor = pdo_get(PDO_NAME . 'distributor', array('mid' => $mid), array('nowmoney', 'id'));

		if ($distributor['nowmoney'] < $_GPC['sapplymoney']) {
			$this->renderError('可提现金额不足');
		}

		$appmoney = $_GPC['sapplymoney'];
		$payment_type = $_GPC['payment_type'];
		$disset = $this->disSetting;
		$cashsets = Setting::wlsetting_read('cashset');
		$nowmoney = $distributor['nowmoney'] - $appmoney;
		$res1 = pdo_update(PDO_NAME . 'distributor', array('nowmoney' => $nowmoney), array('id' => $distributor['id']));

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
				$this->renderSuccess('申请成功！', array('id' => $disorderid));
			}
			else {
				$this->renderError('申请失败，请联系管理员！');
			}
		}
		else {
			$this->renderError('更新个人数据失败，请联系管理员！');
		}
	}

	/**
     * Comment: 推广商品列表获取
     * Author: zzw
     * Date: 2019/7/17 11:49
     */
	public function distributionGoods()
	{
		global $_W;
		global $_GPC;
		$order = $_GPC['orders'];
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$page_index = $_GPC['page_index'] ? $_GPC['page_index'] : 10;
		$name = $_GPC['name'];
		$isVip = WeChat::VipVerification($_W['wlmember']['id']);
		$vipId = is_array($isVip) ? $isVip['id'] : 0;
		$groupWhere = ' aa.aid = ' . $_W['aid'] . ' AND aa.uniacid = ' . $_W['uniacid'] . ' ';
		$rushWhere = ' ba.aid = ' . $_W['aid'] . ' AND ba.uniacid = ' . $_W['uniacid'] . ' ';
		$fightWhere = ' ca.aid = ' . $_W['aid'] . ' AND ca.uniacid = ' . $_W['uniacid'] . ' ';
		$bargainWhere = ' da.aid = ' . $_W['aid'] . ' AND da.uniacid = ' . $_W['uniacid'] . ' ';
		$couponWhere = ' ea.aid = ' . $_W['aid'] . ' AND ea.uniacid = ' . $_W['uniacid'] . ' ';
		$integralWhere = ' fa.uniacid = ' . $_W['uniacid'] . ' ';

		if (!empty($name)) {
			$groupWhere .= ' AND aa.name LIKE \'%' . $name . '%\' ';
			$rushWhere .= ' AND ba.name LIKE \'%' . $name . '%\' ';
			$fightWhere .= ' AND ca.name LIKE \'%' . $name . '%\' ';
			$bargainWhere .= ' AND da.name LIKE \'%' . $name . '%\' ';
			$couponWhere .= ' AND ea.title LIKE \'%' . $name . '%\' ';
			$integralWhere .= ' AND fa.title LIKE \'%' . $name . '%\' ';
		}

		$commissionRate = pdo_getcolumn(PDO_NAME . 'dislevel', array('id' => $_W['wlmember']['level']), 'onecommission');
		$rate = $commissionRate ? $commissionRate / 100 : 0;
		$sql = 'SELECT aa.`id`,aa.name,SUM(ab.num) as sales_volume,
                CASE WHEN aa.onedismoney > 0 AND aa.onedismoney > aa.viponedismoney THEN  aa.onedismoney
                     WHEN aa.onedismoney > 0 AND aa.viponedismoney is null THEN  aa.onedismoney
                     WHEN aa.viponedismoney > 0 AND aa.viponedismoney > aa.onedismoney THEN  aa.viponedismoney
                     WHEN aa.viponedismoney > 0 AND aa.onedismoney is null  THEN  aa.viponedismoney
                     ELSE 
                        CASE 
                            WHEN aa.`price`*' . $rate . ' > aa.oldprice*' . $rate . ' AND aa.`price`*' . $rate . ' > aa.vipprice*' . $rate . ' THEN ROUND(aa.`price`*' . $rate . ',1)
                            WHEN aa.`oldprice`*' . $rate . ' > aa.price*' . $rate . ' AND aa.`oldprice`*' . $rate . ' > aa.vipprice*' . $rate . ' THEN ROUND(aa.`oldprice`*' . $rate . ',1)
                            WHEN aa.`vipprice`*' . $rate . ' > aa.oldprice*' . $rate . ' AND aa.`vipprice`*' . $rate . ' > aa.price*' . $rate . ' THEN ROUND(aa.`vipprice`*' . $rate . ',1)
                            ELSE ROUND(aa.`price`*' . $rate . ',1)
                        END 
                END as commission,\'group\' as `plugin`,aa.thumb,
                CASE WHEN ' . $vipId . ' > 0 THEN aa.vipprice 
                     ELSE aa.price
                END as price,aa.oldprice,aa.createtime FROM ' . tablename(PDO_NAME . 'groupon_activity') . ' as aa LEFT JOIN ' . tablename(PDO_NAME . 'order') . (' as ab ON aa.id = ab.fkid AND plugin = \'groupon\' WHERE aa.`status` = 2 AND ' . $groupWhere . ' GROUP BY aa.id
                UNION ALL SELECT ba.id,ba.name,SUM(bb.num) as sales_volume,
                CASE WHEN ba.onedismoney > 0 AND ba.onedismoney > ba.viponedismoney THEN  ba.onedismoney
                     WHEN ba.onedismoney > 0 AND ba.viponedismoney is null THEN  ba.onedismoney
                     WHEN ba.viponedismoney > 0 AND ba.viponedismoney > ba.onedismoney THEN  ba.viponedismoney
                     WHEN ba.viponedismoney > 0 AND ba.onedismoney is null  THEN  ba.viponedismoney
                     ELSE 
                        CASE 
                            WHEN ba.`price`*' . $rate . ' > ba.oldprice*' . $rate . ' AND ba.`price`*' . $rate . ' > ba.vipprice*' . $rate . ' THEN ROUND(ba.`price`*' . $rate . ',1)
                            WHEN ba.`oldprice`*' . $rate . ' > ba.price*' . $rate . ' AND ba.`oldprice`*' . $rate . ' > ba.vipprice*' . $rate . ' THEN ROUND(ba.`oldprice`*' . $rate . ',1)
                            WHEN ba.`vipprice`*' . $rate . ' > ba.oldprice*' . $rate . ' AND ba.`vipprice`*' . $rate . ' > ba.price*' . $rate . ' THEN ROUND(ba.`vipprice`*' . $rate . ',1)
                            ELSE ROUND(ba.`price`*' . $rate . ',1)
                        END 
                END as commission,\'rush\' as `plugin`,ba.thumb, 
                CASE WHEN ' . $vipId . ' > 0 THEN ba.vipprice 
                     ELSE ba.price
                END as price,ba.oldprice,ba.starttime as createtime  FROM ') . tablename(PDO_NAME . 'rush_activity') . ' as ba LEFT JOIN ' . tablename(PDO_NAME . 'rush_order') . (' as bb ON ba.id = bb.activityid WHERE ba.`status` = 2 AND ' . $rushWhere . ' GROUP BY ba.id
                UNION ALL SELECT ca.id,ca.name,SUM(cb.num) as sales_volume,
                CASE WHEN ca.onedismoney > 0 THEN ca.onedismoney
                     ELSE 
                        CASE 
                            WHEN ca.`price`*' . $rate . ' > ca.aloneprice*' . $rate . ' AND ca.`price`*' . $rate . ' > ca.oldprice*' . $rate . ' THEN ROUND(ca.`price`*' . $rate . ',1)
                            WHEN ca.`aloneprice`*' . $rate . ' > ca.price*' . $rate . ' AND ca.`aloneprice`*' . $rate . ' > ca.oldprice*' . $rate . ' THEN ROUND(ca.`aloneprice`*' . $rate . ',1)
                            WHEN ca.`oldprice`*' . $rate . ' > ca.price*' . $rate . ' AND ca.`oldprice`*' . $rate . ' > ca.aloneprice*' . $rate . ' THEN ROUND(ca.`oldprice`*' . $rate . ',1)
                            ELSE ROUND(ca.`price`*' . $rate . ',1)
                        END 
                END  as commission,\'fight\' as `plugin`,ca.logo as thumb,ca.price,ca.oldprice,ca.limitstarttime as createtime FROM ') . tablename(PDO_NAME . 'fightgroup_goods') . ' as ca LEFT JOIN ' . tablename(PDO_NAME . 'order') . (' as cb ON ca.id = cb.fkid AND plugin = \'wlfightgroup\' WHERE ca.`status` = 1 AND ' . $fightWhere . ' GROUP BY ca.id
                UNION ALL SELECT da.id,da.name,SUM(db.num) as sales_volume,
                CASE WHEN da.onedismoney > 0 AND da.onedismoney > da.viponedismoney THEN  da.onedismoney
                     WHEN da.onedismoney > 0 AND da.viponedismoney is null THEN  da.onedismoney
                     WHEN da.viponedismoney > 0 AND da.viponedismoney > da.onedismoney THEN  da.viponedismoney
                     WHEN da.viponedismoney > 0 AND da.onedismoney is null  THEN  da.viponedismoney
                     ELSE 
                        CASE 
                            WHEN da.`price`*' . $rate . ' > da.oldprice*' . $rate . ' AND da.`price`*' . $rate . ' > da.vipprice*' . $rate . ' THEN ROUND(da.`price`*' . $rate . ',1)
                            WHEN da.`oldprice`*' . $rate . ' > da.price*' . $rate . ' AND da.`oldprice`*' . $rate . ' > da.vipprice*' . $rate . ' THEN ROUND(da.`oldprice`*' . $rate . ',1)
                            WHEN da.`vipprice`*' . $rate . ' > da.oldprice*' . $rate . ' AND da.`vipprice`*' . $rate . ' > da.price*' . $rate . ' THEN ROUND(da.`vipprice`*' . $rate . ',1)
                            ELSE ROUND(da.`price`*' . $rate . ',1)
                        END
                END as commission,\'bargain\' as `plugin`,da.thumb,
                CASE WHEN ' . $vipId . ' > 0 THEN da.vipprice 
                     ELSE da.price
                END as price,da.oldprice,da.createtime FROM ') . tablename(PDO_NAME . 'bargain_activity') . '  as da LEFT JOIN ' . tablename(PDO_NAME . 'order') . (' as db ON da.id = db.fkid AND plugin = \'bargain\' WHERE da.`status` = 2 AND ' . $bargainWhere . ' GROUP BY da.id
                UNION ALL SELECT ea.id,ea.title as name,SUM(eb.num) as sales_volume,
                   CASE WHEN ea.onedismoney > 0 AND ea.onedismoney > ea.viponedismoney THEN  ea.onedismoney
                     WHEN ea.onedismoney > 0 AND ea.viponedismoney is null THEN  ea.onedismoney
                     WHEN ea.viponedismoney > 0 AND ea.viponedismoney > ea.onedismoney THEN  ea.viponedismoney
                     WHEN ea.viponedismoney > 0 AND ea.onedismoney is null  THEN  ea.viponedismoney
                     ELSE 
                        CASE 
                            WHEN ea.`price`*' . $rate . ' > ea.vipprice*' . $rate . ' THEN ROUND(ea.`price`*' . $rate . ',1)
                            WHEN ea.`vipprice`*' . $rate . ' > ea.price*' . $rate . ' THEN ROUND(ea.`vipprice`*' . $rate . ',1)
                            ELSE ROUND(ea.`price`*' . $rate . ',1)
                        END
                END as commission,\'coupon\' as `plugin`,ea.logo as thumb,
                CASE WHEN ' . $vipId . ' > 0 THEN ea.vipprice 
                     ELSE ea.price
                END as price,ea.price as oldprice,ea.createtime FROM ') . tablename(PDO_NAME . 'couponlist') . '  as ea LEFT JOIN ' . tablename(PDO_NAME . 'order') . (' as eb ON ea.id = eb.fkid AND plugin = \'coupon\' WHERE ea.`status` = 1 AND ea.is_charge = 1 AND ' . $couponWhere . ' GROUP BY ea.id
                UNION ALL SELECT fa.id,fa.title as name,SUM(fb.num) as sales_volume,
                CASE WHEN fa.onedismoney > 0 THEN  fa.onedismoney
                     ELSE 
                        CASE 
                            WHEN fa.`use_credit2`*' . $rate . ' > fa.old_price*' . $rate . ' AND fa.`use_credit2`*' . $rate . ' > fa.vipcredit2*' . $rate . ' THEN ROUND(fa.`use_credit2`*' . $rate . ',1)
                            WHEN fa.`old_price`*' . $rate . ' > fa.use_credit2*' . $rate . ' AND fa.`old_price`*' . $rate . ' > fa.vipcredit2*' . $rate . ' THEN ROUND(fa.`old_price`*' . $rate . ',1)
                            WHEN fa.`vipcredit2`*' . $rate . ' > fa.old_price*' . $rate . ' AND fa.`vipcredit2`*' . $rate . ' > fa.use_credit2*' . $rate . ' THEN ROUND(fa.`vipcredit2`*' . $rate . ',1)
                            ELSE ROUND(fa.`use_credit2`*' . $rate . ',1)
                        END
                END as commission,\'integral\' as `plugin`,fa.thumb,
                CASE WHEN ' . $vipId . ' > 0 THEN fa.vipcredit2 
                     ELSE fa.use_credit2
                END as price,fa.old_price as oldprice,fa.id as createtime FROM ') . tablename(PDO_NAME . 'consumption_goods') . '  as fa LEFT JOIN ' . tablename(PDO_NAME . 'order') . (' as fb ON fa.id = fb.fkid AND plugin = \'consumption\' WHERE fa.`status` = 1 AND ' . $integralWhere . ' GROUP BY fa.id
                ');
		$list = pdo_fetchall($sql);

		switch ($order) {
		case 1:
			$orderWhere = array_column($list, 'createtime');
			array_multisort($orderWhere, SORT_DESC, $list);
			break;

		case 2:
			$orderWhere = array_column($list, 'commission');
			array_multisort($orderWhere, SORT_DESC, $list);
			break;

		case 3:
			$orderWhere = array_column($list, 'commission');
			array_multisort($orderWhere, SORT_ASC, $list);
			break;

		case 4:
			$orderWhere = array_column($list, 'sales_volume');
			array_multisort($orderWhere, SORT_DESC, $list);
			break;

		case 5:
			$orderWhere = array_column($list, 'sales_volume');
			array_multisort($orderWhere, SORT_ASC, $list);
			break;
		}

		$info['page_total'] = ceil(count($list) / $page_index);
		$pageStart = $page * $page_index - $page_index;
		$list = array_slice($list, $pageStart, $page_index);

		foreach ($list as $key => &$val) {
			$val['thumb'] = tomedia($val['thumb']);

			switch ($val['plugin']) {
			case 'group':
				$val['link_url'] = app_url('groupon/grouponapp/groupondetail', array('cid' => $val['id']), true);
				break;

			case 'rush':
				$val['link_url'] = app_url('rush/home/detail', array('id' => $val['id']), true);
				break;

			case 'fight':
				$val['link_url'] = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $val['id']), true);
				break;

			case 'bargain':
				$val['link_url'] = app_url('bargain/bargain_app/bargaindetail', array('cid' => $val['id']), true);
				break;

			case 'coupon':
				$val['link_url'] = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $val['id']), true);
				break;

			case 'integral':
				$val['link_url'] = app_url('consumption/goods/goods_detail', array('id' => $val['id']), true);
				break;
			}

			unset($val['createtime']);
			unset($val['sales_volume']);
		}

		$info['list'] = $list;
		$this->renderSuccess('推广商品列表', $info);
	}

	/**
     * Comment: 分销商佣金明细
     * Author: zzw
     * Date: 2019/7/17 14:19
     */
	public function detailedCommission()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$field = 'id,dismoney';
		$info = pdo_fetch('SELECT ' . $field . ' FROM ' . tablename(PDO_NAME . 'distributor') . (' WHERE mid = ' . $mid . ' AND disflag = 1 '));

		if (!$info) {
			$this->renderError('请先成为分销商');
		}

		$dayWhere = $monthWhere = ' a.status IN (1,2) AND (a.oneleadid = ' . $info['id'] . ' OR a.twoleadid = ' . $info['id'] . ' OR a.threeleadid = ' . $info['id'] . ') ';
		$field = 'a.leadmoney,a.oneleadid,a.twoleadid,a.threeleadid';
		$dayMoney = 0;
		$monthMoney = 0;
		$dayStart = strtotime(date('Y-m-d'));
		$dayEnd = strtotime(date('Y-m-d') . ' 23:59:59');
		$dayWhere .= ' AND b.createtime > ' . $dayStart . ' AND b.createtime < ' . $dayEnd . ' ';
		$dayList = Distribution::getDisOrder($dayWhere, $field);
		if (is_array($dayList) && 0 < count($dayList)) {
			foreach ($dayList as $dayK => $dayV) {
				$moneyArr = unserialize($dayV['leadmoney']);
				if ($dayV['oneleadid'] == $info['id'] && 0 < $moneyArr['one']) {
					$dayMoney += $moneyArr['one'];
				}
				else {
					if ($dayV['twoleadid'] == $info['id'] && 0 < $moneyArr['two']) {
						$dayMoney += $moneyArr['two'];
					}
					else {
						if ($dayV['threeleadid'] == $info['id'] && 0 < $moneyArr['three']) {
							$dayMoney += $moneyArr['three'];
						}
						else {
							$dayMoney += 0;
						}
					}
				}
			}
		}

		$monthStart = strtotime(date('Y-m') . '-1 ');
		$monthEnd = strtotime(date('Y-m-t') . ' 23:59:59');
		$monthWhere .= ' AND b.createtime > ' . $monthStart . ' AND b.createtime < ' . $monthEnd . ' ';
		$monthList = Distribution::getDisOrder($monthWhere, $field);
		if (is_array($monthList) && 0 < count($monthList)) {
			foreach ($monthList as $monthK => $monthV) {
				$moneyArr = unserialize($monthV['leadmoney']);
				if ($monthV['oneleadid'] == $info['id'] && 0 < $moneyArr['one']) {
					$monthMoney += $moneyArr['one'];
				}
				else {
					if ($monthV['twoleadid'] == $info['id'] && 0 < $moneyArr['two']) {
						$monthMoney += $moneyArr['two'];
					}
					else {
						if ($monthV['threeleadid'] == $info['id'] && 0 < $moneyArr['three']) {
							$monthMoney += $moneyArr['three'];
						}
						else {
							$monthMoney += 0;
						}
					}
				}
			}
		}

		$info['day_money'] = 0 < $dayMoney ? sprintf('%.2f', $dayMoney) : sprintf('%.2f', 0);
		$info['month_money'] = 0 < $monthMoney ? sprintf('%.2f', $monthMoney) : sprintf('%.2f', 0);
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$page_index = $_GPC['page_index'] ? $_GPC['page_index'] : 10;
		$pageStart = $page * $page_index - $page_index;
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename(PDO_NAME . 'disdetail') . ' as a LEFT JOIN ' . tablename(PDO_NAME . 'disorder') . ' as b ON a.disorderid = b.id ' . (' WHERE a.leadid = ' . $mid . ' '));
		$info['page_total'] = ceil($total / $page_index);
		$list = pdo_fetchall('SELECT a.disorderid,a.reason,FROM_UNIXTIME(a.createtime,\'%Y-%m-%d %H:%i:%S\') as createtime,a.price,a.type,
                            CASE b.plugin
                                WHEN \'rush\' THEN (SELECT orderno FROM ' . tablename(PDO_NAME . 'rush_order') . ' WHERE id = b.orderid ) 
                                ELSE (SELECT orderno FROM ' . tablename(PDO_NAME . 'order') . ' WHERE id = b.orderid ) 
                            END as orderno,
                            CASE 
                                WHEN a.disorderid > 0 AND (a.reason = \'分销提现驳回\' OR a.reason = \'分销佣金提现\') 
                                    THEN (SELECT id FROM ' . tablename(PDO_NAME . 'settlement_record') . ' WHERE id = a.disorderid)
                                ELSE -1
                            END as is_cash_withdrawal FROM ' . tablename(PDO_NAME . 'disdetail') . ' as a LEFT JOIN ' . tablename(PDO_NAME . 'disorder') . ' as b ON a.disorderid = b.id ' . (' WHERE a.leadid = ' . $mid . ' ORDER BY a.createtime DESC LIMIT ' . $pageStart . ',' . $page_index . ' '));
		$info['detail_list'] = $list;
		$this->renderSuccess('分销商佣金明细', $info);
	}

	/**
     * Comment: 分销商等级信息(等级说明)
     * Author: zzw
     * Date: 2019/7/17 15:03
     */
	public function disLvInfo()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$lvId = $_GPC['id'];
		$field = 'id,dislevel';
		$info = pdo_fetch('SELECT ' . $field . ' FROM ' . tablename(PDO_NAME . 'distributor') . (' WHERE mid = ' . $mid . ' AND disflag = 1 '));

		if (!$info) {
			$this->renderError('请先成为分销商');
		}

		$member = pdo_get(PDO_NAME . 'member', array('id' => $mid), array('nickname', 'avatar'));
		$info['nickname'] = $member['nickname'];
		$info['avatar'] = $member['avatar'];
		$info['team_total'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'distributor') . (' WHERE leadid = ' . $mid . ' AND disflag IN (0,1) AND lockflag = 0 '));

		if (0 < $lvId) {
			$lvWhere = ' id = ' . $lvId . ' ';
			$profitWhere = ' id = ' . $lvId . ' ';
		}
		else if (0 < $info['dislevel']) {
			$lvWhere = ' upstandard > ' . $info['team_total'] . ' ORDER BY upstandard ASC ';
			$profitWhere = ' id = ' . $info['dislevel'] . ' ';
		}
		else {
			$lvWhere = ' id = 1 ';
			$profitWhere = ' id = 1 ';
			$info['dislevel'] = 1;
		}

		$distance = pdo_fetchcolumn('SELECT upstandard FROM ' . tablename(PDO_NAME . 'dislevel') . (' WHERE ' . $lvWhere . ' '));
		$info['distance'] = 0 < $distance ? $distance - $info['team_total'] : -1;
		$info['distance'] = $info['distance'] < 0 ? 0 : $info['distance'];
		$proportion = pdo_fetch('SELECT onecommission,twocommission,threecommission,upstandard FROM ' . tablename(PDO_NAME . 'dislevel') . (' WHERE ' . $profitWhere . ' '));
		$info['onecommission'] = $proportion['onecommission'] ?: sprintf('%.2f', 0);
		$info['twocommission'] = $proportion['twocommission'] ?: sprintf('%.2f', 0);
		$info['threecommission'] = $proportion['threecommission'] ?: sprintf('%.2f', 0);
		$info['upstandard'] = $proportion['upstandard'] ?: 0;
		$info['lv_list'] = pdo_fetchall('SELECT id,name FROM ' . tablename(PDO_NAME . 'dislevel') . ' WHERE upstandard > 0 OR id = 1 ORDER BY upstandard DESC,createtime DESC ');
		$this->renderSuccess('分销商等级信息(等级说明)', $info);
	}

	/**
     * Comment: 获取我的团队人员信息
     * Author: zzw
     * Date: 2019/7/17 18:33
     */
	public function myTeam()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$state = $_GPC['state'] ? $_GPC['state'] : 0;
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$page_index = $_GPC['page_index'] ? $_GPC['page_index'] : 10;
		$name = $_GPC['name'];
		$type = $_GPC['type'] ? $_GPC['type'] : 0;
		$disId = pdo_fetchcolumn('SELECT id FROM ' . tablename(PDO_NAME . 'distributor') . (' WHERE mid = ' . $mid . ' AND disflag = 1 '));
		if (!$disId || $disId < 0) {
			$this->renderError('请先成为分销商');
		}

		$where = ' WHERE leadid = ' . $mid . ' AND `uniacid` = ' . $_W['uniacid'] . ' ';

		if ($type == 1) {
			$where .= ' AND disflag = 0  ';
		}
		else if ($type == 2) {
			$where .= ' AND disflag IN = 1  ';
		}
		else {
			$where .= ' AND disflag IN (0,1) ';
		}

		$isPurchaseWhere = '';
		$caseStr = 'CASE disflag
                         WHEN 0 THEN 
                            CASE WHEN ((SELECT count(*) FROM ' . tablename(PDO_NAME . 'order') . ' WHERE mid = a.`mid`  AND `status` IN (0,1,2,3,4,6,8,9) AND createtime > a.`createtime` ) 
                                       + (SELECT count(*) FROM ' . tablename(PDO_NAME . 'rush_order') . ' WHERE mid = a.`mid` AND `status` IN (0,1,2,3,4,6,8,9) AND createtime > a.`createtime` )) > 0 
                                 THEN ((SELECT count(*) FROM ' . tablename(PDO_NAME . 'order') . ' WHERE mid = a.`mid`  AND `status` IN (0,1,2,3,4,6,8,9) AND createtime > a.`createtime` ) 
                                       + (SELECT count(*) FROM ' . tablename(PDO_NAME . 'rush_order') . ' WHERE mid = a.`mid` AND `status` IN (0,1,2,3,4,6,8,9) AND createtime > a.`createtime` ))
                                 ELSE 0
                            END 
                         WHEN 1 THEN 
                            CASE WHEN ((SELECT count(*) FROM ' . tablename(PDO_NAME . 'order') . ' WHERE mid = a.`mid`  AND `status` IN (0,1,2,3,4,6,8,9) AND createtime > a.`createtime` ) 
                                       + (SELECT count(*) FROM ' . tablename(PDO_NAME . 'rush_order') . ' WHERE mid = a.`mid` AND `status` IN (0,1,2,3,4,6,8,9)  AND createtime > a.`createtime` )
                                       + (SELECT count(*) FROM ' . tablename(PDO_NAME . 'disorder') . ' WHERE oneleadid = a.`id` OR twoleadid = a.`id` OR threeleadid = a.`id` AND createtime > a.`createtime`)) > 0 
                    
                                 THEN ((SELECT count(*) FROM ' . tablename(PDO_NAME . 'order') . ' WHERE mid = a.`mid`  AND `status` IN (0,1,2,3,4,6,8,9) AND createtime > a.`createtime` ) 
                                       + (SELECT count(*) FROM ' . tablename(PDO_NAME . 'rush_order') . ' WHERE mid = a.`mid` AND `status` IN (0,1,2,3,4,6,8,9)  AND createtime > a.`createtime` )
                                       + (SELECT count(*) FROM ' . tablename(PDO_NAME . 'disorder') . ' WHERE oneleadid = a.`id` OR twoleadid = a.`id` OR threeleadid = a.`id` AND createtime > a.`createtime`))
                                 ELSE 0
                            END 
                    END';
		if ($state == 1 || $state == 2) {
			$isPurchaseWhere .= ' AND ' . $caseStr;

			if ($state == 1) {
				$isPurchaseWhere .= '> 0 ';
			}
			else {
				if ($state == 2) {
					$isPurchaseWhere .= '< 1';
				}
			}
		}

		$dayStart = strtotime(date('Y-m-d'));
		$dayEnd = strtotime(date('Y-m-d') . ' 23:59:59');
		$dayWhere = ' AND a.createtime > ' . $dayStart . ' AND a.createtime < ' . $dayEnd . ' ';
		$order = ' ORDER BY `createtime` DESC  ';
		$field = 'a.disflag,a.mid,a.nickname,FROM_UNIXTIME(a.`createtime`,\'%Y-%m-%d %H:%i:%S\') as createtime,a.dislevel  ';
		$sql = 'SELECT ' . $caseStr . ' as order_number,' . $field . ' FROM ' . tablename(PDO_NAME . 'distributor') . (' as a ' . $where . ' ');
		$info['day_number'] = count(pdo_fetchall($sql . $dayWhere . $order));
		$info['total_number'] = count(pdo_fetchall($sql . $order));
		$pageStart = $page * $page_index - $page_index;
		$limit = ' LIMIT ' . $pageStart . ',' . $page_index . ' ';

		if ($name) {
			$searchWhere = ' AND a.nickname LIKE \'%' . $name . '%\' ';
		}

		$info['page_total'] = ceil(count(pdo_fetchall($sql . $searchWhere . $isPurchaseWhere)) / $page_index);
		$list = pdo_fetchall($sql . $searchWhere . $isPurchaseWhere . $order . $limit);
		$defaultLvName = pdo_fetchcolumn('SELECT name FROM ' . tablename(PDO_NAME . 'dislevel') . ' WHERE id = 1 ');

		foreach ($list as $key => &$val) {
			$userInfo = pdo_get(PDO_NAME . 'member', array('id' => $val['mid']), array('nickname', 'avatar'));
			$val['avatar'] = $userInfo['avatar'];

			if ($val['disflag'] == 1) {
				if (0 < $val['dislevel']) {
					$lvName = pdo_fetchcolumn('SELECT name FROM ' . tablename(PDO_NAME . 'dislevel') . (' WHERE id = ' . $val['dislevel'] . ' '));
					$val['lv_name'] = $lvName;
				}
				else {
					$val['lv_name'] = $defaultLvName;
				}
			}
			else {
				$val['lv_name'] = '普通用户';
			}

			$val['commission'] = pdo_fetchcolumn('SELECT SUM(price) FROM ' . tablename(PDO_NAME . 'disdetail') . (' WHERE type = 1 AND leadid = ' . $mid . ' AND buymid = ' . $val['mid'] . ' AND uniacid = ' . $_W['uniacid'] . ' '));
			unset($val['disflag']);
			unset($val['mid']);
			unset($val['dislevel']);
		}

		$info['list'] = $list;
		$this->renderSuccess('团队人员信息列表', $info);
	}

	/**
     * Comment: 推广订单列表
     * Author: zzw
     * Date: 2019/7/18 18:07
     */
	public function disOrder()
	{
		global $_W;
		global $_GPC;
		$mid = $_W['mid'];
		$state = $_GPC['state'] ? $_GPC['state'] : 0;
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$page_index = $_GPC['page_index'] ? $_GPC['page_index'] : 10;
		$orderno = $_GPC['order_no'] ? $_GPC['order_no'] : '';
		$disId = pdo_fetchcolumn('SELECT id FROM ' . tablename(PDO_NAME . 'distributor') . (' WHERE mid = ' . $mid . ' AND disflag = 1 '));
		if (!$disId || $disId < 0) {
			$this->renderError('请先成为分销商');
		}

		$select = tablename(PDO_NAME . 'disorder') . (' as a WHERE a.uniacid = ' . $_W['uniacid'] . ' AND ( a.oneleadid = ' . $disId . ' OR a.twoleadid = ' . $disId . ' OR a.threeleadid = ' . $disId . ' ) ');
		$info['not_settlement'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . $select . ' AND a.status = 0');
		$info['unsettled_order'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . $select . ' AND a.status = 1');
		$info['settled_order'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . $select . ' AND a.status = 2');
		$info['refunded_order'] = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . $select . ' AND a.status = 3');
		$where = '';

		if (0 < $state) {
			$where = ' AND a.status = ' . $state . ' ';
		}

		$pageStart = $page * $page_index - $page_index;

		if (!empty($orderno)) {
			$where .= ' AND 
                        CASE `plugin` 
                             WHEN \'rush\' THEN ( SELECT orderno FROM ' . tablename(PDO_NAME . 'rush_order') . ' WHERE id = a.orderid )
                             ELSE ( SELECT orderno FROM ' . tablename(PDO_NAME . 'order') . (' WHERE id = a.orderid )
                        END = \'' . $orderno . '\' ');
		}

		$field = ' a.id,a.status,a.plugin,a.orderid,a.orderprice,a.buymid,a.leadmoney,a.oneleadid,a.twoleadid,a.threeleadid ';
		$list = pdo_fetchall('SELECT ' . $field . ' FROM ' . $select . $where . (' ORDER BY a.createtime DESC LIMIT ' . $pageStart . ',' . $page_index));
		$info['page_total'] = ceil(count(pdo_fetchall('SELECT ' . $field . ' FROM ' . $select . $where)) / $page_index);

		foreach ($list as $key => &$val) {
			$userInfo = pdo_get(PDO_NAME . 'member', array('id' => $val['buymid']), array('nickname', 'avatar'));
			$val['nickname'] = $userInfo['nickname'];
			$val['avatar'] = $userInfo['avatar'];

			if ($val['plugin'] == 'rush') {
				$orderInfo = pdo_get(PDO_NAME . 'rush_order', array('id' => $val['orderid']), array('activityid', 'num', 'orderno', 'paytime', 'expressid', 'checkcode', 'usedtime'));
			}
			else {
				$orderInfo = pdo_get(PDO_NAME . 'order', array('id' => $val['orderid']), array('fkid', 'num', 'orderno', 'paytime', 'expressid', 'recordid'));
			}

			$consumption_type = 3;

			if (0 < $orderInfo['expressid']) {
				$timeList = pdo_fetchall(' SELECT receivetime FROM ' . tablename(PDO_NAME . 'express') . (' WHERE receivetime is not null AND id = ' . $orderInfo['expressid'] . ' '));
				$timeList = is_array($timeList) && 0 < count($timeList) ? array_column($timeList, 'receivetime') : '';
				$consumption_type = 1;
			}
			else {
				if (!empty($orderInfo['checkcode']) && $val['plugin'] == 'rush') {
					if (!empty($orderInfo['usedtime'])) {
						$timeInfo = unserialize($orderInfo['usedtime']);
						$timeList = is_array($timeInfo) ? array_column($timeInfo, 'time') : '';
					}

					$consumption_type = 2;
				}
				else {
					if ($val['plugin'] != 'rush') {
						$consumption_type = 2;

						switch ($val['plugin']) {
						case 'bargain':
							$usedtime = pdo_fetchcolumn(' SELECT usedtime FROM ' . tablename(PDO_NAME . 'bargain_userlist') . (' WHERE orderid = ' . $val['orderid'] . ' '));

							if ($usedtime) {
								$usedtime = unserialize($usedtime);
								$timeList = is_array($usedtime) && 0 < count($usedtime) ? array_column($usedtime, 'time') : '';
							}

							break;

						case 'coupon':
							$usedtime = pdo_fetchcolumn(' SELECT usedtime FROM ' . tablename(PDO_NAME . 'member_coupons') . (' WHERE id = ' . $orderInfo['recordid'] . ' '));

							if ($usedtime) {
								$usedtime = unserialize($usedtime);
								$timeList = is_array($usedtime) && 0 < count($usedtime) ? array_column($usedtime, 'time') : '';
							}

							break;

						case 'fightgroup':
							$usedtime = pdo_fetchcolumn(' SELECT usedtime FROM ' . tablename(PDO_NAME . 'fightgroup_userecord') . (' WHERE id = ' . $orderInfo['recordid'] . ' '));

							if ($usedtime) {
								$usedtime = unserialize($usedtime);
								$timeList = is_array($usedtime) && 0 < count($usedtime) ? array_column($usedtime, 'time') : '';
							}

							break;

						case 'groupon':
							$usedtime = pdo_fetchcolumn(' SELECT usedtime FROM ' . tablename(PDO_NAME . 'groupon_userecord') . (' WHERE id = ' . $orderInfo['recordid'] . ' '));

							if ($usedtime) {
								$usedtime = unserialize($usedtime);
								$timeList = is_array($usedtime) && 0 < count($usedtime) ? array_column($usedtime, 'time') : '';
							}

							break;

						default:
							$timeList = array($orderInfo['paytime']);
							$consumption_type = 3;
							break;
						}
					}
				}
			}

			if (is_array($timeList) && 0 < count($timeList)) {
				$i = 0;

				while ($i < count($timeList)) {
					$timeList[$i] = date('Y-m-d H:i:s', $timeList[$i]);
					++$i;
				}
			}

			switch ($val['plugin']) {
			case 'bargain':
				$goodsInfo = pdo_get(PDO_NAME . 'bargain_activity', array('id' => $orderInfo['fkid']), array('name', 'thumb'));
				$val['goods_name'] = $goodsInfo['name'] ? $goodsInfo['name'] : '砍价商品';
				$val['goods_logo'] = $goodsInfo['thumb'] ? tomedia($goodsInfo['thumb']) : 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;

			case 'charge':
				$val['goods_name'] = '商家入驻';
				$val['goods_logo'] = 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;

			case 'consumption':
				$goodsInfo = pdo_get(PDO_NAME . 'consumption_goods', array('id' => $orderInfo['fkid']), array('title', 'thumb'));
				$val['goods_name'] = $goodsInfo['title'] ? $goodsInfo['title'] : '积分兑换';
				$val['goods_logo'] = $goodsInfo['thumb'] ? tomedia($goodsInfo['thumb']) : 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;

			case 'coupon':
				$goodsInfo = pdo_get(PDO_NAME . 'couponlist', array('id' => $orderInfo['fkid']), array('title', 'logo'));
				$val['goods_name'] = $goodsInfo['title'] ? $goodsInfo['title'] : '卡卷';
				$val['goods_logo'] = $goodsInfo['logo'] ? tomedia($goodsInfo['logo']) : 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;

			case 'distribution':
				$val['goods_name'] = '申请分销商';
				$val['goods_logo'] = 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;

			case 'fightgroup':
				$goodsInfo = pdo_get(PDO_NAME . 'fightgroup_goods', array('id' => $orderInfo['fkid']), array('name', 'logo'));
				$val['goods_name'] = $goodsInfo['name'] ? $goodsInfo['name'] : '拼团商品';
				$val['goods_logo'] = $goodsInfo['logo'] ? tomedia($goodsInfo['logo']) : 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;

			case 'groupon':
				$goodsInfo = pdo_get(PDO_NAME . 'groupon_activity', array('id' => $orderInfo['fkid']), array('name', 'thumb'));
				$val['goods_name'] = $goodsInfo['name'] ? $goodsInfo['name'] : '团购商品';
				$val['goods_logo'] = $goodsInfo['thumb'] ? tomedia($goodsInfo['thumb']) : 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;

			case 'halfcard':
				$val['goods_name'] = '会员卡';
				$val['goods_logo'] = 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;

			case 'payonline':
				$val['goods_name'] = '在线买单';
				$val['goods_logo'] = 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;

			case 'pocket':
				$val['goods_name'] = '掌上信息';
				$val['goods_logo'] = 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;

			case 'rush':
				$goodsInfo = pdo_get(PDO_NAME . 'rush_activity', array('id' => $orderInfo['activityid']), array('name', 'thumb'));
				$val['goods_name'] = $goodsInfo['name'] ? $goodsInfo['name'] : '团购商品';
				$val['goods_logo'] = $goodsInfo['thumb'] ? tomedia($goodsInfo['thumb']) : 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;

			case 'vip':
				$val['goods_name'] = '会员卡';
				$val['goods_logo'] = 'https://weixin.weliam.cn/web/resource/images/nopic.jpg';
				break;
			}

			$moneyArr = unserialize($val['leadmoney']);
			if ($val['oneleadid'] == $disId && 0 < $moneyArr['one']) {
				$val['commission'] = $moneyArr['one'];
			}
			else {
				if ($val['twoleadid'] == $disId && 0 < $moneyArr['two']) {
					$val['commission'] = $moneyArr['two'];
				}
				else {
					if ($val['threeleadid'] == $disId && 0 < $moneyArr['three']) {
						$val['commission'] = $moneyArr['three'];
					}
					else {
						$val['commission'] = 0;
					}
				}
			}

			$val['consumption_type'] = $consumption_type;
			$val['num'] = $orderInfo['num'];
			$val['orderno'] = $orderInfo['orderno'];
			$val['paytime'] = date('Y-m-d H:i:s', $orderInfo['paytime']);
			$val['use_time'] = is_array($timeList) && 0 < count($timeList) ? $timeList : array();
			unset($val['id']);
			unset($val['plugin']);
			unset($val['orderid']);
			unset($val['buymid']);
			unset($val['leadmoney']);
			unset($val['oneleadid']);
			unset($val['twoleadid']);
			unset($val['threeleadid']);
		}

		$info['list'] = $list;
		$this->renderSuccess('推广订单列表', $info);
	}

	/**
     * Comment: 分销提现详细信息
     * Author: zzw
     * Date: 2019/7/19 9:18
     */
	public function detailsOfWithdrawal()
	{
		global $_W;
		global $_GPC;
		$detailId = $_GPC['detail_id'];

		if (empty($detailId)) {
			$this->renderError('缺少参数:detail_id');
		}

		$field = ' payment_type,status,sapplymoney,sgetmoney,spercentmoney,FROM_UNIXTIME(applytime,\'%Y-%m-%d %H:%i:%S\') as applytime,FROM_UNIXTIME(updatetime,\'%Y-%m-%d %H:%i:%S\') as updatetime ';
		$info = pdo_fetch('SELECT ' . $field . ' FROM ' . tablename(PDO_NAME . 'settlement_record') . (' WHERE id = ' . $detailId . ' '));
		$res = Distribution::getStatusDetailInfo($info['status']);
		$info['status_title'] = $res['title'];
		$this->renderSuccess('分销提现详细信息', $info);
	}

	/**
     * Comment: 分销商佣金排行榜信息
     * Author: zzw
     * Date: 2019/7/19 15:10
     */
	public function commissionRanking()
	{
		global $_W;
		global $_GPC;
		$state = $_GPC['state'] ? $_GPC['state'] : 1;
		$where = ' WHERE a.uniacid = ' . $_W['uniacid'] . ' AND a.type = 1 ';
		$groupOrder = ' GROUP BY a.leadid ORDER BY total_price DESC ';

		switch ($state) {
		case 1:
			$defaultDate = date('Y-m-d', time());
			$first = 1;
			$w = date('w', strtotime($defaultDate));
			$startTime = strtotime($defaultDate . ' -' . ($w ? $w - $first : 6) . ' days');
			$endTime = strtotime(date('Y-m-d H:i:s', $startTime) . ' +7 days ');
			$where .= ' AND createtime > ' . $startTime . ' AND createtime < ' . $endTime . ' ';
			break;

		case 2:
			$startTime = strtotime(date('Y-m' . '-1 00:00:00'));
			$endTime = strtotime(date('Y-m' . '-1 00:00:00') . ' +1 month');
			$where .= ' AND createtime > ' . $startTime . ' AND createtime < ' . $endTime . ' ';
			break;
		}

		$sql = 'SELECT a.id,a.leadid,SUM(a.price) as total_price,b.nickname,b.avatar FROM ' . tablename(PDO_NAME . 'disdetail') . ' a RIGHT JOIN ' . tablename(PDO_NAME . 'member') . ' as b ON a.leadid = b.id ';
		$list = pdo_fetchall($sql . $where . $groupOrder);
		$userInfo = pdo_fetch($sql . (' WHERE leadid = ' . $_W['mid'] . ' '));
		$userInfo['sort'] = -1;

		if (is_array($list)) {
			foreach ($list as $key => &$val) {
				if ($val['leadid'] == $_W['mid']) {
					$userInfo['sort'] = $key;
				}

				unset($val['id']);
				unset($val['leadid']);
			}
		}

		unset($userInfo['id']);
		unset($userInfo['leadid']);
		$info['user'] = $userInfo;
		$info['list'] = $list;
		$this->renderSuccess('分销商佣金排行榜信息', $info);
	}

	/**
     * Comment: 联系方式设置
     * Author: zzw
     * Date: 2019/7/22 9:35
     */
	public function setContactInformation()
	{
		global $_W;
		global $_GPC;

		if (!empty($_GPC['wechat_number'])) {
			$data['wechat_number'] = $_GPC['wechat_number'];
		}

		if (!empty($_GPC['wechat_qrcode'])) {
			$data['wechat_qrcode'] = $_GPC['wechat_qrcode'];
		}

		if (count($data) == 0) {
			$this->renderError('请提交修改内容');
		}

		$data['id'] = $_W['mid'];
		$have = pdo_get(PDO_NAME . 'member', $data);

		if ($have) {
			$this->renderError('请修改后在提交');
		}

		$result = pdo_update(PDO_NAME . 'member', $data, array('id' => $_W['mid']));

		if ($result) {
			$this->renderSuccess('修改成功');
		}
		else {
			$this->renderError('修改失败');
		}
	}

	/**
     * Comment: 联系方式获取
     * Author: zzw
     * Date: 2019/7/22 9:16
     */
	public function getContactInformation()
	{
		global $_W;
		global $_GPC;
		$info = pdo_get(PDO_NAME . 'member', array('id' => $_W['mid']), array('wechat_number', 'wechat_qrcode'));
		$info['wechat_number'] = $info['wechat_number'] ?: '';
		$info['wechat_qrcode'] = $info['wechat_qrcode'] ? tomedia($info['wechat_qrcode']) : '';
		$info['is_head'] = pdo_getcolumn(PDO_NAME . 'distributor', array('mid' => $_W['mid']), 'leadid');

		if (-1 < $info['is_head']) {
			$headInfo = pdo_get(PDO_NAME . 'member', array('id' => $info['is_head']), array('wechat_number', 'wechat_qrcode', 'nickname', 'mobile', 'avatar'));
			$headInfo['wechat_qrcode'] = tomedia($headInfo['wechat_qrcode']);
			$headInfo['type'] = 1;
		}

		if (!is_array($headInfo) || empty($headInfo['wechat_number']) || empty($headInfo['wechat_qrcode'])) {
			$headInfo['type'] = 2;
			$headInfo['title'] = $this->disSetting['communname'];
			$headInfo['desc'] = $this->disSetting['commundesc'];
			$headInfo['qrcode'] = tomedia($this->disSetting['communqrcode']);
			$headInfo['images'] = tomedia($this->disSetting['communimg']);
		}

		$data['user_info'] = $info;
		$data['head_info'] = $headInfo;
		$this->renderSuccess('联系方式获取', $data);
	}

	/**
     * Comment: 获取分销商帮助说明
     * Author: zzw
     * Date: 2019/7/24 14:10
     */
	public function getHelpNote()
	{
		$info = $this->disSetting['distriqa'];
		$this->renderSuccess('帮助说明', $info);
	}

	/**
     * Comment: 获取分销商设置信息
     * Author: zzw
     * Date: 2019/7/25 10:11
     */
	public function getSetting()
	{
		$info['diy_text'] = array('fxtext' => !empty($this->disSetting['fxtext']) ? $this->disSetting['fxtext'] : '分销', 'xxtext' => !empty($this->disSetting['xxtext']) ? $this->disSetting['xxtext'] : '客户', 'sjtext' => !empty($this->disSetting['sjtext']) ? $this->disSetting['sjtext'] : '上级', 'yjtext' => !empty($this->disSetting['yjtext']) ? $this->disSetting['yjtext'] : '佣金', 'fxstext' => !empty($this->disSetting['fxstext']) ? $this->disSetting['fxstext'] : '分销商', 'myposter' => !empty($this->disSetting['myposter']) ? $this->disSetting['myposter'] : '我的海报');
		$this->renderSuccess('分销商设置信息', $info);
	}
}

?>
