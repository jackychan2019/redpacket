<?php
//dezend by http://www.sucaihuo.com/
class Jurisdiction
{
	static public function menuList()
	{
		$list = array(
			'dashboard'  => array(
				'title' => '首页管理',
				'list'  => array(
					array('name' => '运营概况', 'url' => 'dashboard/dashboard/index'),
					array('name' => '公告', 'url' => 'dashboard/notice/index'),
					array('name' => '幻灯片', 'url' => 'dashboard/adv/index'),
					array('name' => '导航栏', 'url' => 'dashboard/nav/index'),
					array('name' => '广告栏', 'url' => 'dashboard/banner/index'),
					array('name' => '商品魔方', 'url' => 'dashboard/cube/index'),
					array('name' => '选项卡管理', 'url' => 'dashboard/plugin/index'),
					array('name' => '底部菜单', 'url' => 'dashboard/foot/index')
				)
			),
			'store'      => array(
				'title' => '商户管理',
				'list'  => array(
					array('name' => '商户列表', 'url' => 'store/merchant/index'),
					array('name' => '添加商户', 'url' => 'store/merchant/edit'),
					array('name' => '入驻申请', 'url' => 'store/register/index'),
					array('name' => '付费记录', 'url' => 'store/register/chargerecode'),
					array('name' => '商户分组', 'url' => 'store/group/index'),
					array('name' => '商户分类', 'url' => 'store/category/index'),
					array('name' => '全部评论', 'url' => 'store/comment/index'),
					array('name' => '商户动态', 'url' => 'store/comment/dynamic')
				)
			),
			'order'      => array(
				'title' => '订单管理',
				'list'  => array(
					array('name' => '商品订单', 'url' => 'order/wlOrder/orderlist'),
					array('name' => '在线买单', 'url' => 'order/wlOrder/payonlinelist'),
					array('name' => '运费模板', 'url' => 'order/wlOrder/freightlist')
				)
			),
			'finace'     => array(
				'title' => '财务管理',
				'list'  => array(
					array('name' => '账单明细', 'url' => 'finace/newCash/cashrecord'),
					array('name' => '退款记录', 'url' => 'finace/newCash/refundrecord'),
					array('name' => '余额提现', 'url' => 'finace/wlCash/cashApplyAgent'),
					array('name' => '提现账户', 'url' => 'finace/wlCash/account'),
					array('name' => '提现记录', 'url' => 'finace/wlCash/cashApplyAgentRecord'),
					array('name' => '商家账户', 'url' => 'finace/newCash/currentlist'),
					array('name' => '我的账户', 'url' => 'finace/newCash/currentlist')
				)
			),
			'datacenter' => array(
				'title' => '数据管理',
				'list'  => array(
					array('name' => '运营统计', 'url' => 'datacenter/datacenter/stat_operate'),
					array('name' => '店铺统计', 'url' => 'datacenter/datacenter/stat_store')
				)
			),
			'app'        => array('title' => '应用管理', 'list' => ''),
			'agentset'   => array(
				'title' => '设置管理',
				'list'  => array(
					array('name' => '账号信息', 'url' => 'agentset/userset/profile'),
					array('name' => '管理设置', 'url' => 'agentset/userset/adminset'),
					array('name' => '客服设置', 'url' => 'agentset/userset/customer'),
					array('name' => '通用设置', 'url' => 'agentset/userset/meroof'),
					array('name' => '分享设置', 'url' => 'agentset/userset/shareset'),
					array('name' => '社群设置', 'url' => 'agentset/userset/community'),
					array('name' => '标签设置', 'url' => 'agentset/userset/tags')
				)
			)
		);

		if (p('storeqr')) {
			$appList[] = array('name' => '商户二维码', 'url' => 'storeqr/sqrcode/qrlist');
		}

		if (p('groupon')) {
			$appList[] = array('name' => '团购活动', 'url' => 'groupon/active/activelist');
		}

		if (p('rush')) {
			$appList[] = array('name' => '抢购活动', 'url' => 'rush/active/activelist');
		}

		if (p('wlfightgroup')) {
			$appList[] = array('name' => '拼团商城', 'url' => 'wlfightgroup/fightgoods/ptgoodslist');
		}

		if (p('bargain')) {
			$appList[] = array('name' => '砍价活动', 'url' => 'bargain/bargain_web/activitylist');
		}

		if (p('chosen')) {
			$appList[] = array('name' => '同城精选', 'url' => 'chosen/diy/pagelist');
		}

		if (p('paidpromotion')) {
			$appList[] = array('name' => '支付有礼', 'url' => 'paidpromotion/payactive/activelist');
		}

		if (p('pocket')) {
			$appList[] = array('name' => '掌上信息', 'url' => 'pocket/Slide/lists');
		}

		if (p('diypage')) {
			$appList[] = array('name' => '平台装修', 'url' => 'diypage/diy/pagelist');
		}

		if (p('halfcard')) {
			$appList[] = array('name' => '一卡通', 'url' => 'halfcard/halfcard_web/halfcardList');
		}

		if (p('wlcoupon')) {
			$appList[] = array('name' => '超级券', 'url' => 'wlcoupon/couponlist/couponsList');
		}

		if (p('call')) {
			$appList[] = array('name' => '集Call', 'url' => 'call/call/callList');
		}

		if (p('headline')) {
			$appList[] = array('name' => '头条', 'url' => 'headline/headline/index');
		}

		if (p('activity')) {
			$appList[] = array('name' => '活动', 'url' => 'activity/activity_web/activitylist');
		}

		$list['app']['list'] = $appList;
		return $list;
	}

	static public function judgeMainMenu($list)
	{
		$permissionList = self::menuList();

		foreach (array_column($permissionList['dashboard']['list'], 'url') as $VisitKey => $VisitVal) {
			if (in_array($VisitVal, $list)) {
				$mainList[] = 'dashboard';
				break;
			}
		}

		foreach (array_column($permissionList['store']['list'], 'url') as $VisitKey => $VisitVal) {
			if (in_array($VisitVal, $list)) {
				$mainList[] = 'store';
				break;
			}
		}

		foreach (array_column($permissionList['order']['list'], 'url') as $VisitKey => $VisitVal) {
			if (in_array($VisitVal, $list)) {
				$mainList[] = 'order';
				break;
			}
		}

		foreach (array_column($permissionList['finace']['list'], 'url') as $VisitKey => $VisitVal) {
			if (in_array($VisitVal, $list)) {
				$mainList[] = 'finace';
				break;
			}
		}

		foreach (array_column($permissionList['datacenter']['list'], 'url') as $VisitKey => $VisitVal) {
			if (in_array($VisitVal, $list)) {
				$mainList[] = 'datacenter';
				break;
			}
		}

		foreach (array_column($permissionList['app']['list'], 'url') as $VisitKey => $VisitVal) {
			if (in_array($VisitVal, $list)) {
				$mainList[] = 'app';
				break;
			}
		}

		foreach (array_column($permissionList['agentset']['list'], 'url') as $VisitKey => $VisitVal) {
			if (in_array($VisitVal, $list)) {
				$mainList[] = 'agentset';
				break;
			}
		}

		return $mainList;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
