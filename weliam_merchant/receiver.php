<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
require_once IA_ROOT . '/addons/weliam_merchant/core/common/defines.php';
require_once PATH_CORE . 'common/autoload.php';
Func_loader::core('global');
class Weliam_merchantModuleReceiver extends WeModuleReceiver
{
	public function receive()
	{
		global $_W;
		$message = $this->message;
		file_put_contents(PATH_DATA . 'storeqr.log', var_export($message, true) . PHP_EOL, FILE_APPEND);

		if (!empty($message['scene'])) {
			$name = pdo_getcolumn('qrcode', array('scene_str' => $message['scene'], 'uniacid' => $_W['uniacid']), 'name');
			$names = explode(':', $name);
			$plugin = isset($names[1]) ? $names[1] : '';

			if (!empty($plugin)) {
				$plugin::Processor($message);
			}
		}
		else {
			$scanrecord = pdo_fetch('SELECT cardid,scantime,type FROM ' . tablename('wlmerchant_halfcard_qrscan') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND openid = \'' . $message['from'] . '\' order by id desc'));
			if (!empty($scanrecord) && time() < $scanrecord['scantime'] + 120) {
				if (empty($scanrecord['type'])) {
					$card = pdo_get('wlmerchant_halfcard_realcard', array('uniacid' => $_W['uniacid'], 'id' => $scanrecord['cardid']));

					if (!empty($card)) {
						if ($card['status'] == 1) {
							$setting = Setting::wlsetting_read('halfcard');
							$imgurl = $setting['cardimg'] ? $setting['cardimg'] : URL_MODULE . 'plugin/halfcard/app/resource/images/cord-bg.jpg';
							$returnmess = array(
								array('title' => urlencode('点击立即激活此卡'), 'description' => urlencode('激活此卡'), 'picurl' => tomedia($imgurl), 'url' => app_url('halfcard/halfcard_app/realcard', array('cardsn' => $card['cardsn'], 'salt' => $card['salt'])))
							);
							$this->send_news($returnmess, $message);
						}

						if ($card['status'] == 2) {
							$this->send_text('关注成功，请重新扫描二维码操作', $message);
						}

						if ($card['status'] == 3) {
							$this->send_text('抱歉，此卡已失效！', $message);
						}
					}
				}
				else {
					if ($scanrecord['type'] == 'rush') {
						$rushgoods = Rush::getSingleActive($scanrecord['cardid'], 'name,thumb');
						$title = $rushgoods['name'];
						$imgurl = $rushgoods['thumb'];
						$url = app_url('rush/home/detail', array('id' => $scanrecord['cardid']));
					}

					if ($scanrecord['type'] == 'wlcoupon') {
						$wlCoupon = wlCoupon::getSingleCoupons($scanrecord['cardid'], 'title,logo');
						$title = $wlCoupon['title'];
						$imgurl = $wlCoupon['logo'];
						$url = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $scanrecord['cardid']));
					}

					if ($scanrecord['type'] == 'wlfightgroup') {
						$group = Wlfightgroup::getSingleGood($scanrecord['cardid'], 'name,logo');
						$title = $group['name'];
						$imgurl = $group['logo'];
						$url = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $scanrecord['cardid']));
					}

					if ($scanrecord['type'] == 'wlgroupdetail') {
						$group = pdo_get('wlmerchant_fightgroup_group', array('id' => $scanrecord['cardid']), array('goodsid'));
						$goods = Wlfightgroup::getSingleGood($group['goodsid'], 'name,logo');
						$title = $goods['name'];
						$imgurl = $goods['logo'];
						$url = app_url('wlfightgroup/fightapp/groupdetail', array('id' => $scanrecord['cardid']));
					}

					if ($scanrecord['type'] == 'groupon') {
						$groupon = pdo_get('wlmerchant_groupon_activity', array('id' => $scanrecord['cardid']), array('name', 'thumb'));
						$title = $groupon['name'];
						$imgurl = $groupon['thumb'];
						$url = app_url('groupon/grouponapp/groupondetail', array('cid' => $scanrecord['cardid']));
					}

					if ($scanrecord['type'] == 'bargain') {
						$groupon = pdo_get('wlmerchant_bargain_activity', array('id' => $scanrecord['cardid']), array('name', 'thumb'));
						$title = $groupon['name'];
						$imgurl = $groupon['thumb'];
						$url = app_url('bargain/bargain_app/bargaindetail', array('cid' => $scanrecord['cardid']));
					}

					if ($scanrecord['type'] == 'helpbargain') {
						$user = pdo_get('wlmerchant_bargain_userlist', array('id' => $scanrecord['cardid']), array('activityid'));
						$groupon = pdo_get('wlmerchant_bargain_activity', array('id' => $user['activityid']), array('name', 'thumb'));
						$title = $groupon['name'];
						$imgurl = $groupon['thumb'];
						$url = app_url('bargain/bargain_app/bargaindetail', array('cid' => $user['activityid'], 'userid' => $scanrecord['cardid']));
					}

					if ($scanrecord['type'] == 'payOnline') {
						$title = '在线买单';
						$url = app_url('order/paybill/paycheck', array('id' => $scanrecord['cardid']));
					}

					if ($scanrecord['type'] == 'distribution') {
						$base = Setting::wlsetting_read('distribution');
						$title = $base['gztitle'] ? $base['gztitle'] : '申请分销商';
						$imgurl = tomedia($base['gzthumb']);
						$desc = $base['gzdesc'];
						$url = app_url('distribution/disappbase/applyindex', array('rank' => $scanrecord['cardid']));
					}

					if (empty($desc)) {
						$desc = '数量有限，手慢无~~';
					}

					$returnmess = array(
						array('title' => urlencode($title), 'description' => urlencode($desc), 'picurl' => tomedia($imgurl), 'url' => $url)
					);
					$this->send_news($returnmess, $message);
				}
			}
		}
	}

	public function send_news($returnmess, $message)
	{
		global $_W;
		$send['touser'] = $message['from'];
		$send['msgtype'] = 'news';
		$send['news']['articles'] = $returnmess;
		$acc = WeAccount::create($_W['acid']);
		$data = $acc->sendCustomNotice($send);

		if (is_error($data)) {
			file_put_contents(PATH_DATA . 'receiver.log', var_export($data, true) . PHP_EOL, FILE_APPEND);
			$this->salerEmpty();
		}
		else {
			$this->salerEmpty();
		}
	}

	public function send_text($mess, $message)
	{
		global $_W;
		$send['touser'] = $message['from'];
		$send['msgtype'] = 'text';
		$send['text'] = array('content' => urlencode($mess));
		$acc = WeAccount::create($_W['acid']);
		$data = $acc->sendCustomNotice($send);

		if (is_error($data)) {
			file_put_contents(PATH_DATA . 'receiver.log', var_export($data, true) . PHP_EOL, FILE_APPEND);
			$this->salerEmpty();
		}
		else {
			$this->salerEmpty();
		}
	}

	public function salerEmpty()
	{
		ob_clean();
		ob_start();
		echo '';
		ob_flush();
		ob_end_flush();
		exit(0);
	}
}

?>
