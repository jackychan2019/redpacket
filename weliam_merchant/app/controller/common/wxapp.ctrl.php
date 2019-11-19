<?php
//dezend by http://www.sucaihuo.com/
class Wxapp_WeliamController
{
	public function index()
	{
		global $_W;
		global $_GPC;
		$_W['uniacid'] = Wxapp::get_uniacid($_W['uniacid']);

		if (empty($_W['uniacid'])) {
			exit('小程序未关联公众号');
		}

		$url = $_GPC['url'];

		if (empty($url)) {
			$url = app_url('dashboard/home/index');
		}
		else {
			$urlarr = explode('#', urldecode($url));

			if (strexists($urlarr[0], 'gzqrcode')) {
				$qrarray = explode(':', $urlarr[0]);
				$qrinfo = Diyposter::geturlinfo($qrarray[1]);
				$url = $qrinfo['url'];
			}
			else {
				$params = array();

				foreach ($urlarr as $key => $value) {
					if ($key != 0 && !empty($value)) {
						$value = explode('=', $value);
						$params[$value[0]] = $value[1];
					}
				}

				$allurl = array('store' => 'store/merchant/detail', 'rush' => 'rush/home/detail', 'rushspecial' => 'rush/home/specialindex', 'coupon' => 'wlcoupon/coupon_app/couponsdetail', 'distri' => 'dashboard/home/index', 'user' => 'member/user/index', 'order' => 'order/userorder/orderlist', 'storeindex' => 'store/merchant/newindex', 'vipindex' => 'halfcard/halfcard_app/userhalfcard', 'rushindex' => 'rush/home/index', 'couponindex' => 'wlcoupon/coupon_app/couponslist', 'ptindex' => 'wlfightgroup/fightapp/fightindex', 'tcindex' => 'pocket/pocket/index', 'storeenter' => 'store/storeManage/enter', 'supervise' => 'store/supervise/storelist', 'distribution' => 'distribution/disappbase/index', 'helper' => 'helper/helper_app/index', 'cardopen' => 'halfcard/halfcardopen/open', 'pocketissue' => 'pocket/pocket/category', 'signindex' => 'wlsign/signapp/signindex', 'groupon' => 'groupon/grouponapp/grouponlist', 'groupond' => 'groupon/grouponapp/groupondetail', 'bargain' => 'bargain/bargain_app/bargaindetail', 'fightgroup' => 'wlfightgroup/fightapp/goodsdetail', 'consumption' => 'consumption/goods/goods_index');
				$urltext = $allurl[$urlarr[0]] ? $allurl[$urlarr[0]] : 'dashboard/home/index';
				$url = app_url($urltext, $params);
			}
		}

		header('location: ' . $url);
		exit();
	}
}

defined('IN_IA') || exit('Access Denied');

?>
