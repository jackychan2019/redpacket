<?php
//dezend by http://www.sucaihuo.com/
class Poster
{
	/**
     * Comment: 抢购商品
     * @param $id
     * @param string $agent
     * @param string $bgimg
     * @return array|string
     */
	static public function createRushPoster($id, $agent = '', $bgimg = '', $mid)
	{
		global $_W;
		global $_GPC;
		$diyposter = Setting::wlsetting_read('diyposter');
		$member = $_W['wlmember'];
		if ($mid && !$member) {
			$member = pdo_get(PDO_NAME . 'member', array('id' => $mid));
		}

		if ($diyposter['h5_poster'] == '1' || $agent == 'wxapp' && $diyposter['wxapp_poster'] == '1') {
			$disqrcode = Diyposter::getgzqrcode($id, $member['id'] . ':rush:' . $agent);
			$qrcode = $disqrcode['url'];
		}
		else {
			$qrcode = app_url('rush/home/detail', array('id' => $id, 'invitid' => $member['id'], 'aid' => $_W['aid']));
		}

		$goods = Rush::getSingleActive($id, '*');
		$goods = self::checkprice($goods);
		$filename = md5($id . 'rushid' . $member['id'] . $bgimg . $agent);
		$data = '[{"left":"0px","top":"0px","type":"thumb","width":"320px","height":"320px","position":"cover"},
        {"left":"0px","top":"0px","type":"img","width":"320px","height":"578.5px","src":"' . tomedia('/addons/weliam_merchant/app/resource/image/poster/rushposterbg.png') . '"},
        {"left":"21.3px","top":"304.6px","type":"head","width":"55px","height":"55px","border":""},
        {"left":"93px","top":"332px","type":"nickname","width":"200px","height":"23px","line":"1","size":"9px","color":"#343434","words":"昵称","align":"left"},
        {"left":"30px","top":"480px","type":"productprice","width":"101px","height":"24px","line":"1","size":"9px","color":"#878787","words":"原价","align":"left"},';

		if ($goods['vipstatus'] != 0) {
			$data .= '{"left":"30px","top":"495px","type":"vip_price","width":"101px","height":"24px","line":"1","size":"9px","color":"#878787","words":"会员价","align":"left"},';
		}

		$data .= '{"left":"25px","top":"380px","type":"title","width":"266px","height":"75px","line":"3","size":"11px","color":"#343434","words":"商品名称","align":"left"},
        {"left":"197px","top":"450px","type":"qr","width":"85px","height":"85px","size":""},
        {"left":"75px","top":"466px","type":"text","width":"10px","height":"26px","line":"1","size":"10px","color":"#ff4744","words":"￥","align":"left"},
        {"left":"88px","top":"453px","type":"text","width":"150px","height":"40px","line":"1","size":"24px","color":"#ff4744","words":"' . $goods['price'] . '","align":"left"},
        {"left":"35px","top":"539px","type":"text","width":"150px","height":"18px","line":"1","size":"8px","color":"#343434","words":"已有' . $goods['pv'] . '人喜欢这款商品","align":"left"}]';
		$bg = '/addons/weliam_merchant/app/resource/image/poster/posterbg.jpg';
		$store = Store::getSingleStore($goods['sid']);
		$fliename = 'rush' . $id . 'mid' . $member['id'] . 'aid' . $_W['aid'];
		self::qrcodeimg($qrcode, $fliename);
		$qrimg = 'addons/weliam_merchant/data/poster/' . $_W['uniacid'] . '/qrcode_' . $fliename . '.png';
		$poster = array('bg' => tomedia($bg), 'data' => $data, 'qrimg' => tomedia($qrimg), 'nickname' => $member['nickname'], 'avatar' => $member['avatar'], 'title' => $goods['name'], 'thumb' => $goods['thumb'], 'marketprice' => $goods['price'], 'productprice' => '原价:￥' . $goods['oldprice'], 'shopTitle' => $store['storename'], 'shopThumb' => tomedia($store['logo']), 'shopAddress' => $store['address'], 'shopPhone' => $store['mobile']);

		if ($goods['vipstatus'] != 0) {
			$poster['vip_price'] = '会员价:￥' . $goods['vipprice'];
		}

		if (p('diyposter') && !empty($diyposter['rushpid'])) {
			$postertpl = pdo_get(PDO_NAME . 'poster', array('uniacid' => $_W['uniacid'], 'id' => $diyposter['rushpid']), array('data', 'bg'));
			$poster['bg'] = $bgimg ? $bgimg : $postertpl['bg'];
			$poster['data'] = $postertpl['data'];
			$poster['productprice'] = '￥' . $goods['oldprice'];
			$poster['vip_price'] = '￥' . $goods['vipprice'];
		}

		if (p('wxapp') && $agent == 'wxapp' && $diyposter['wxapp_poster'] != '1') {
			$src = Wxapp::get_wxapp_qrcode('rush#id=' . $id . '#invitid=' . $_W['mid'], $filename);

			if (!is_error($src)) {
				$poster['qrimg'] = tomedia('../addons/' . MODULE_NAME . '/data/wxapp/' . $_W['uniacid'] . '/' . $filename . '.png');
				$filename = md5($id . 'wxapp_rushid' . $_W['mid']);
			}
		}

		$poster = Tools::createPoster($poster, $filename, $member);
		return $poster;
	}

	/**
     * Comment: 团购商品
     * Author: zzw
     * @param $id
     * @param string $agent
     * @param string $bgimg
     * @return array|string
     */
	public function createGrouponPoster($id, $agent = '', $bgimg = '', $mid)
	{
		global $_W;
		global $_GPC;
		$diyposter = Setting::wlsetting_read('diyposter');
		$member = $_W['wlmember'];
		if ($mid && !$member) {
			$member = pdo_get(PDO_NAME . 'member', array('id' => $mid));
		}

		if ($diyposter['h5_poster'] == '1' || $agent == 'wxapp' && $diyposter['wxapp_poster'] == '1') {
			$disqrcode = Diyposter::getgzqrcode($id, $member['id'] . ':groupon:' . $agent);
			$qrcode = $disqrcode['url'];
		}
		else {
			$qrcode = app_url('groupon/grouponapp/groupondetail', array('cid' => $id, 'invitid' => $member['id'], 'aid' => $_W['aid']));
		}

		$goods = Groupon::getSingleActive($id, '*');
		$goods = self::checkprice($goods);
		$filename = md5($id . 'grouponid' . $member['id'] . $bgimg . $agent);
		$data = '[{"left":"0px","top":"0px","type":"thumb","width":"320px","height":"320px","position":"cover"},
        {"left":"0px","top":"0px","type":"img","width":"320px","height":"578.5px","src":"' . tomedia('/addons/weliam_merchant/app/resource/image/poster/grouponposterbg.png') . '"},
        {"left":"21.3px","top":"304.6px","type":"head","width":"55px","height":"55px","border":""},
        {"left":"93px","top":"332px","type":"nickname","width":"200px","height":"23px","line":"1","size":"9px","color":"#343434","words":"昵称","align":"left"},
        {"left":"25px","top":"380px","type":"title","width":"266px","height":"75px","line":"3","size":"11px","color":"#343434","words":"商品名称","align":"left"},
        {"left":"30px","top":"480px","type":"productprice","width":"101px","height":"24px","line":"1","size":"9px","color":"#878787","words":"原价","align":"left"},';

		if ($goods['vipstatus'] != 0) {
			$data .= '{"left":"30px","top":"495px","type":"vip_price","width":"101px","height":"24px","line":"1","size":"9px","color":"#878787","words":"会员价","align":"left"},';
		}

		$data .= '{"left":"197px","top":"450px","type":"qr","width":"85px","height":"85px","size":""},
        {"left":"75px","top":"466px","type":"text","width":"10px","height":"26px","line":"1","size":"10px","color":"#ff4744","words":"￥","align":"left"},
        {"left":"88px","top":"453px","type":"text","width":"150px","height":"40px","line":"1","size":"24px","color":"#ff4744","words":"' . $goods['price'] . '","align":"left"},
        {"left":"35px","top":"539px","type":"text","width":"150px","height":"18px","line":"1","size":"8px","color":"#343434","words":"已有' . $goods['pv'] . '人喜欢这款商品","align":"left"}]';
		$bg = '/addons/weliam_merchant/app/resource/image/poster/posterbg.jpg';
		$store = Store::getSingleStore($goods['sid']);
		$fliename = 'groupon' . $id . 'mid' . $member['id'] . 'aid' . $_W['aid'];
		self::qrcodeimg($qrcode, $fliename);
		$qrimg = 'addons/weliam_merchant/data/poster/' . $_W['uniacid'] . '/qrcode_' . $fliename . '.png';
		$poster = array('bg' => tomedia($bg), 'data' => $data, 'qrimg' => tomedia($qrimg), 'nickname' => $member['nickname'], 'avatar' => $member['avatar'], 'title' => $goods['name'], 'thumb' => $goods['thumb'], 'marketprice' => $goods['price'], 'productprice' => '原价:￥' . $goods['oldprice'], 'shopTitle' => $store['storename'], 'shopThumb' => tomedia($store['logo']), 'shopAddress' => $store['address'], 'shopPhone' => $store['mobile']);

		if ($goods['vipstatus'] != 0) {
			$poster['vip_price'] = '会员价:￥' . $goods['vipprice'];
		}

		if (p('diyposter') && !empty($diyposter['grouponpid'])) {
			$postertpl = pdo_get(PDO_NAME . 'poster', array('uniacid' => $_W['uniacid'], 'id' => $diyposter['grouponpid']), array('data', 'bg'));
			$poster['bg'] = $bgimg ? $bgimg : $postertpl['bg'];
			$poster['data'] = $postertpl['data'];
			$poster['productprice'] = '￥' . $goods['oldprice'];

			if ($goods['vipstatus'] != 0) {
				$poster['vip_price'] = '￥' . $goods['vipprice'];
			}
		}

		if (p('wxapp') && $agent == 'wxapp' && $diyposter['wxapp_poster'] != '1') {
			$src = Wxapp::get_wxapp_qrcode('groupond#cid=' . $id . '#invitid=' . $member['id'], $filename);

			if (!is_error($src)) {
				$poster['qrimg'] = tomedia('../addons/' . MODULE_NAME . '/data/wxapp/' . $_W['uniacid'] . '/' . $filename . '.png');
				$filename = md5($id . 'wxapp_grouponid' . $member['id']);
			}
			else if ($_W['wlmember']) {
				wl_json(1, $src['message']);
			}
			else {
				return array('state' => 1, 'erron' => $src['message']);
			}
		}

		$poster = Tools::createPoster($poster, $filename, $member);
		return $poster;
	}

	/**
     * Comment: 卡卷
     * Author: zzw
     * @param $id
     * @param string $agent
     * @param string $bgimg
     * @return array|string
     */
	public function createCouponPoster($id, $agent = '', $bgimg = '', $mid)
	{
		global $_W;
		global $_GPC;
		$diyposter = Setting::wlsetting_read('diyposter');
		$member = $_W['wlmember'];
		if ($mid && !$member) {
			$member = pdo_get(PDO_NAME . 'member', array('id' => $mid));
		}

		if ($diyposter['h5_poster'] == '1' || $agent == 'wxapp' && $diyposter['wxapp_poster'] == '1') {
			$disqrcode = Diyposter::getgzqrcode($id, $member['id'] . ':wlcoupon:' . $agent);
			$qrcode = $disqrcode['url'];
		}
		else {
			$qrcode = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $id, 'invitid' => $member['id'], 'aid' => $_W['aid']));
		}

		$coupons = wlCoupon::getSingleCoupons($id, '*');
		$coupons = self::checkprice($coupons);
		$filename = md5($id . 'couponid' . $member['id'] . $bgimg . $agent);
		$coupon_price = $coupons['is_charge'] == 1 ? $coupons['price'] : '免费领';
		$data = '[{"left":"0px","top":"0px","type":"thumb","width":"320px","height":"320px","position":"cover"},
        {"left":"0px","top":"0px","type":"img","width":"320px","height":"578.5px","src":"' . tomedia('/addons/weliam_merchant/app/resource/image/poster/couponposterbg.png') . '"},
        {"left":"21.3px","top":"304.6px","type":"head","width":"55px","height":"55px","border":""},
        {"left":"93px","top":"332px","type":"nickname","width":"200px","height":"23px","line":"1","size":"9px","color":"#343434","words":"昵称","align":"left"},
        {"left":"25px","top":"380px","type":"title","width":"266px","height":"75px","line":"3","size":"11px","color":"#343434","words":"商品名称","align":"left"},
        {"left":"30px","top":"490px","type":"text","width":"101px","height":"24px","line":"1","size":"9px","color":"#878787","words":"","align":"left"},
        {"left":"197px","top":"450px","type":"qr","width":"85px","height":"85px","size":""},';

		if ($coupon_price == '免费领') {
			$data .= '{"left":"85px","top":"453px","type":"text","width":"150px","height":"40px","line":"1","size":"24px","color":"#ff4744","words":"' . $coupon_price . '","align":"left"},';
		}
		else {
			$data .= '{"left":"75px","top":"466px","type":"text","width":"10px","height":"26px","line":"1","size":"10px","color":"#ff4744","words":"￥","align":"left"},
            {"left":"88px","top":"453px","type":"text","width":"150px","height":"40px","line":"1","size":"24px","color":"#ff4744","words":"' . $coupon_price . '","align":"left"},';
		}

		if ($coupons['vipstatus'] != 0 && $coupons['is_charge'] == 1) {
			$data .= '{"left":"30px","top":"490px","type":"vip_price","width":"101px","height":"24px","line":"1","size":"9px","color":"#878787","words":"会员价:￥' . $coupons['vipprice'] . '","align":"left"},';
		}

		$data .= '{"left":"35px","top":"539px","type":"text","width":"150px","height":"18px","line":"1","size":"8px","color":"#343434","words":"已有' . $coupons['pv'] . '人喜欢这款商品","align":"left"}]';
		$bg = '/addons/weliam_merchant/app/resource/image/poster/posterbg.jpg';
		$store = Store::getSingleStore($coupons['merchantid']);
		$fliename = 'coupon' . $id . 'mid' . $member['id'] . 'aid' . $_W['aid'];
		self::qrcodeimg($qrcode, $fliename);
		$qrimg = 'addons/weliam_merchant/data/poster/' . $_W['uniacid'] . '/qrcode_' . $fliename . '.png';
		$poster = array('bg' => tomedia($bg), 'data' => $data, 'qrimg' => tomedia($qrimg), 'nickname' => $member['nickname'], 'avatar' => $member['avatar'], 'title' => $coupons['title'], 'sub_title' => $coupons['sub_title'], 'thumb' => tomedia($coupons['logo']), 'marketprice' => $coupon_price, 'shopTitle' => $store['storename'], 'shopThumb' => tomedia($store['logo']), 'shopAddress' => $store['address'], 'shopPhone' => $store['mobile']);
		if ($coupons['vipstatus'] != 0 && $coupons['is_charge'] == 1) {
			$poster['vip_price'] = '会员价:￥' . $coupons['vipprice'];
		}

		if (p('diyposter') && !empty($diyposter['cardpid'])) {
			$postertpl = pdo_get(PDO_NAME . 'poster', array('uniacid' => $_W['uniacid'], 'id' => $diyposter['cardpid']), array('data', 'bg'));
			$poster['bg'] = $bgimg ? $bgimg : $postertpl['bg'];
			$poster['data'] = $postertpl['data'];
			if ($coupons['vipstatus'] != 0 && $coupons['is_charge'] == 1) {
				$poster['vip_price'] = '￥' . $coupons['vipprice'];
			}
		}

		if (p('wxapp') && $agent == 'wxapp' && $diyposter['wxapp_poster'] != '1') {
			$src = Wxapp::get_wxapp_qrcode('coupon#id=' . $id . '#invitid=' . $member['id'], $filename);

			if (!is_error($src)) {
				$poster['qrimg'] = tomedia('../addons/' . MODULE_NAME . '/data/wxapp/' . $_W['uniacid'] . '/' . $filename . '.png');
				$filename = md5($id . 'wxapp_couponid' . $member['id']);
			}
		}

		$poster = Tools::createPoster($poster, $filename, $member);
		return $poster;
	}

	/**
     * Comment: 拼团商品
     * Author: zzw
     * @param $id
     * @param string $agent
     * @param string $bgimg
     * @return array|string
     */
	public function createFightgroupPoster($id, $agent = '', $bgimg = '')
	{
		global $_W;
		global $_GPC;
		$member = $_W['wlmember'];
		if ($_W['wlsetting']['diyposter']['h5_poster'] == '1' || $agent == 'wxapp' && $_W['wlsetting']['diyposter']['wxapp_poster'] == '1') {
			$disqrcode = Diyposter::getgzqrcode($id, $_W['mid'] . ':wlfightgroup:' . $agent);
			$qrcode = $disqrcode['url'];
		}
		else {
			$qrcode = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $id, 'invitid' => $_W['mid'], 'aid' => $_W['aid']));
		}

		$goods = Wlfightgroup::getSingleGood($id, '*');
		$goods = self::checkprice($goods);
		$filename = md5($id . 'fightgroup' . $_W['mid'] . $bgimg . $agent);
		$data = '[{"left":"0px","top":"0px","type":"thumb","width":"320px","height":"320px","position":"cover"},
        {"left":"0px","top":"0px","type":"img","width":"320px","height":"578.5px","src":"' . URL_APP_RESOURCE . '/image/poster/fgroupposterbg.png' . '"},
        {"left":"21.3px","top":"304.6px","type":"head","width":"55px","height":"55px","border":""},
        {"left":"93px","top":"332px","type":"nickname","width":"200px","height":"23px","line":"1","size":"9px","color":"#343434","words":"昵称","align":"left"},
        {"left":"25px","top":"380px","type":"title","width":"266px","height":"75px","line":"3","size":"11px","color":"#343434","words":"商品名称","align":"left"},
        {"left":"30px","top":"490px","type":"text","width":"101px","height":"24px","line":"1","size":"9px","color":"#878787","words":"单买价:￥' . $goods['aloneprice'] . '","align":"left"},
        {"left":"197px","top":"450px","type":"qr","width":"85px","height":"85px","size":""},
        {"left":"75px","top":"466px","type":"text","width":"10px","height":"26px","line":"1","size":"10px","color":"#ff4744","words":"￥","align":"left"},
        {"left":"88px","top":"453px","type":"text","width":"150px","height":"40px","line":"1","size":"24px","color":"#ff4744","words":"' . $goods['price'] . '","align":"left"},
        {"left":"35px","top":"539px","type":"text","width":"150px","height":"18px","line":"1","size":"8px","color":"#343434","words":"已有' . $goods['pv'] . '人喜欢这款商品","align":"left"}]';
		$store = Store::getSingleStore($goods['merchantid']);
		$fliename = 'fight' . $id . 'mid' . $member['id'] . 'aid' . $_W['aid'];
		self::qrcodeimg($qrcode, $fliename);
		$qrimg = 'addons/weliam_merchant/data/poster/' . $_W['uniacid'] . '/qrcode_' . $fliename . '.png';
		$poster = array('bg' => URL_APP_RESOURCE . '/image/poster/posterbg.jpg', 'data' => $data, 'qrimg' => tomedia($qrimg), 'nickname' => $_W['wlmember']['nickname'], 'avatar' => $_W['wlmember']['avatar'], 'title' => $goods['name'], 'thumb' => $goods['logo'], 'marketprice' => $goods['price'], 'productprice' => $goods['aloneprice'], 'shopTitle' => $store['storename'], 'shopThumb' => tomedia($store['logo']), 'shopAddress' => $store['address'], 'shopPhone' => $store['mobile']);
		if (p('diyposter') && !empty($_W['wlsetting']['diyposter']['fgrouppid'])) {
			$postertpl = pdo_get(PDO_NAME . 'poster', array('uniacid' => $_W['uniacid'], 'id' => $_W['wlsetting']['diyposter']['fgrouppid']), array('data', 'bg'));
			$poster['bg'] = $bgimg ? $bgimg : $postertpl['bg'];
			$poster['data'] = $postertpl['data'];
		}

		if (p('wxapp') && $agent == 'wxapp' && $_W['wlsetting']['diyposter']['wxapp_poster'] != '1') {
			$src = Wxapp::get_wxapp_qrcode('fightgroup#id=' . $id . '#invitid=' . $_W['mid'], $filename);

			if (!is_error($src)) {
				$poster['qrimg'] = tomedia('../addons/' . MODULE_NAME . '/data/wxapp/' . $_W['uniacid'] . '/' . $filename . '.png');
				$filename = md5($id . 'wxapp_fightgroup' . $_W['mid']);
			}
		}

		$poster = Tools::createPoster($poster, $filename, $member);
		return $poster;
	}

	/**
     * Comment: 砍价海报
     * Author: zzw
     * @param $id
     * @param $userid
     * @param string $agent
     * @param string $bgimg
     * @return array|string
     */
	public function createBargainPoster($id, $userid, $agent = '', $bgimg = '')
	{
		global $_W;
		global $_GPC;
		$member = $_W['wlmember'];
		if ($_W['wlsetting']['diyposter']['h5_poster'] == '1' || $agent == 'wxapp' && $_W['wlsetting']['diyposter']['wxapp_poster'] == '1') {
			$disqrcode = Diyposter::getgzqrcode($id, $_W['mid'] . ':bargain:' . $agent . ':' . $userid);
			$qrcode = $disqrcode['url'];
		}
		else {
			$qrcode = app_url('bargain/bargain_app/bargaindetail', array('cid' => $id, 'userid' => $userid, 'invitid' => $_W['mid'], 'aid' => $_W['aid']));
		}

		$goods = pdo_get('wlmerchant_bargain_activity', array('id' => $id, 'uniacid' => $_W['uniacid']));
		$goods = self::checkprice($goods);
		$filename = md5($id . 'bargain' . $_W['mid'] . $bgimg . $agent);
		$data = '[{"left":"0px","top":"0px","type":"thumb","width":"320px","height":"320px","position":"cover"},
        {"left":"0px","top":"0px","type":"img","width":"320px","height":"578.5px","src":"' . URL_APP_RESOURCE . '/image/poster/bargainbg.png' . '"},
        {"left":"21.3px","top":"304.6px","type":"head","width":"55px","height":"55px","border":""},
        {"left":"93px","top":"332px","type":"nickname","width":"200px","height":"23px","line":"1","size":"9px","color":"#343434","words":"昵称","align":"left"},
        {"left":"25px","top":"380px","type":"title","width":"266px","height":"75px","line":"3","size":"11px","color":"#343434","words":"商品名称","align":"left"},
        {"left":"30px","top":"480px","type":"productprice","width":"101px","height":"24px","line":"1","size":"9px","color":"#878787","words":"原价","align":"left"},';

		if ($goods['vipstatus'] != 0) {
			$data .= '{"left":"30px","top":"495px","type":"vip_price","width":"101px","height":"24px","line":"1","size":"9px","color":"#878787","words":"会员底价","align":"left"},';
		}

		$data .= '{"left":"197px","top":"450px","type":"qr","width":"85px","height":"85px","size":""},
        {"left":"75px","top":"466px","type":"text","width":"10px","height":"26px","line":"1","size":"10px","color":"#ff4744","words":"￥","align":"left"},
        {"left":"88px","top":"453px","type":"text","width":"150px","height":"40px","line":"1","size":"24px","color":"#ff4744","words":"' . $goods['price'] . '","align":"left"},
        {"left":"35px","top":"539px","type":"text","width":"150px","height":"18px","line":"1","size":"8px","color":"#343434","words":"已有' . $goods['pv'] . '人喜欢这款商品","align":"left"}]';
		$store = Store::getSingleStore($goods['sid']);
		$fliename = 'bargain' . $id . 'mid' . $member['id'] . 'aid' . $_W['aid'] . 'userid' . $userid;
		self::qrcodeimg($qrcode, $fliename);
		$qrimg = 'addons/weliam_merchant/data/poster/' . $_W['uniacid'] . '/qrcode_' . $fliename . '.png';
		$poster = array('bg' => URL_APP_RESOURCE . '/image/poster/posterbg.jpg', 'data' => $data, 'qrimg' => tomedia($qrimg), 'nickname' => $_W['wlmember']['nickname'], 'avatar' => $_W['wlmember']['avatar'], 'title' => $goods['name'], 'thumb' => $goods['thumb'], 'marketprice' => $goods['price'], 'productprice' => '原价:￥' . $goods['oldprice'], 'shopTitle' => $store['storename'], 'shopThumb' => tomedia($store['logo']), 'shopAddress' => $store['address'], 'shopPhone' => $store['mobile']);

		if ($goods['vipstatus'] != 0) {
			$poster['vip_price'] = '会员低价:￥' . $goods['vipprice'];
		}

		if (p('diyposter') && !empty($_W['wlsetting']['diyposter']['bargainid'])) {
			$postertpl = pdo_get(PDO_NAME . 'poster', array('uniacid' => $_W['uniacid'], 'id' => $_W['wlsetting']['diyposter']['bargainid']), array('data', 'bg'));
			$poster['bg'] = $bgimg ? $bgimg : $postertpl['bg'];
			$poster['data'] = $postertpl['data'];
			$poster['productprice'] = '￥' . $goods['oldprice'];

			if ($goods['vipstatus'] != 0) {
				$poster['vip_price'] = '￥' . $goods['vipprice'];
			}
		}

		if (p('wxapp') && $agent == 'wxapp' && $_W['wlsetting']['diyposter']['wxapp_poster'] != '1') {
			$src = Wxapp::get_wxapp_qrcode('bargain#cid=' . $id . '#userid=' . $userid, $filename);

			if (!is_error($src)) {
				$poster['qrimg'] = tomedia('../addons/' . MODULE_NAME . '/data/wxapp/' . $_W['uniacid'] . '/' . $filename . '.png');
				$filename = md5($id . 'wxapp_bargain' . $_W['mid']);
			}
		}

		$poster = Tools::createPoster($poster, $filename, $member);
		return $poster;
	}

	/**
     * Comment: 店铺
     * Author: zzw
     * @param $id
     * @param string $agent
     * @param string $bgimg
     * @return array|string
     */
	public function createStorePoster($id, $agent = '', $bgimg = '', $mid)
	{
		global $_W;
		global $_GPC;
		$diyposter = Setting::wlsetting_read('diyposter');
		$member = $_W['wlmember'];
		if ($mid && !$member) {
			$member = pdo_get(PDO_NAME . 'member', array('id' => $mid));
		}

		$qrcode = app_url('store/merchant/detail', array('id' => $id, 'fansflag' => 1, 'invitid' => $member['id'], 'aid' => $_W['aid']));
		$store = Store::getSingleStore($id);

		if (!empty($store['cardsn'])) {
			$qrid = pdo_getcolumn(PDO_NAME . 'qrcode', array('sid' => $id, 'status' => 2), 'qrid');
			$url = pdo_getcolumn('qrcode', array('id' => $qrid), 'url');
		}

		$filename = md5($id . 'storeid' . $member['id'] . $bgimg);
		$data = '[{"left":"117px","top":"95px","type":"shopTitle","width":"148px","height":"38px","size":"12px","color":"#333"},
        {"left":"55px","top":"84px","type":"shopThumb","width":"57px","height":"57px"},
        {"left":"64px","top":"379px","type":"qr","width":"63px","height":"63px","size":""}]';
		$qrcode = $url ? $url : $qrcode;
		$fliename = 'store' . $id . 'mid' . $member['id'] . 'aid' . $_W['aid'];
		self::qrcodeimg($qrcode, $fliename);
		$qrimg = 'addons/weliam_merchant/data/poster/' . $_W['uniacid'] . '/qrcode_' . $fliename . '.png';
		$poster = array('bg' => tomedia('/addons/weliam_merchant/app/resource/image/poster/storeposterbg.jpg'), 'data' => $data, 'qrimg' => tomedia($qrimg), 'nickname' => $member['nickname'], 'avatar' => $member['avatar'], 'shopTitle' => $store['storename'], 'shopThumb' => tomedia($store['logo']), 'shopAddress' => $store['address'], 'shopPhone' => $store['mobile']);
		if (p('diyposter') && !empty($diyposter['storepid'])) {
			$postertpl = pdo_get(PDO_NAME . 'poster', array('uniacid' => $_W['uniacid'], 'id' => $diyposter['storepid']), array('data', 'bg'));
			$poster['bg'] = $bgimg ? $bgimg : $postertpl['bg'];
			$poster['data'] = $postertpl['data'];
		}

		if (is_wxapp() && p('wxapp') || $agent == 'wxapp') {
			$src = Wxapp::get_store_qrcode($id);

			if (!is_error($src)) {
				$poster['qrimg'] = tomedia($src);
				$filename = md5($id . 'wxapp_storeid' . $member['id']);
			}
		}

		$poster = Tools::createPoster($poster, $filename, $member);
		return $poster;
	}

	/**
     * Comment: 分销合伙人海报生成
     * Author: zzw
     * @param $id
     * @param $disflag
     * @param string $agent
     * @param string $bgimg
     * @return array|string
     */
	public function createDistriPoster($id, $disflag, $agent = '', $bgimg = '')
	{
		global $_W;
		global $_GPC;
		$member = $_W['wlmember'];
		if ($_W['wlsetting']['distribution']['posterqr'] == '1' && empty($disflag)) {
			$disqrcode = Distribution::getgzqrcode($id);
			$url = $disqrcode['url'];
		}
		else {
			$url = app_url('distribution/disappbase/index', array('invitid' => $id, 'disflag' => $disflag, 'qrentry' => 1, 'aid' => $_W['aid']));
		}

		$filename = md5($id . 'distri' . $disflag . $bgimg);
		$data = '[{"left":"115px","top":"93px","type":"head","width":"86px","height":"86px"},{"left":"115px","top":"186px","type":"nickname","width":"86px","height":"29px","size":"10px","color":"#999999","words":"昵称","align":"center"},{"left":"115px","top":"406px","type":"qr","width":"90px","height":"90px","size":""}]';
		$fliename = 'distri' . $id . 'mid' . $member['id'] . 'aid' . $_W['aid'] . 'flag' . $disflag;
		self::qrcodeimg($url, $fliename);
		$qrimg = 'addons/weliam_merchant/data/poster/' . $_W['uniacid'] . '/qrcode_' . $fliename . '.png';
		$poster = array('bg' => URL_APP_RESOURCE . '/image/poster/distposterbg.jpg', 'data' => $data, 'qrimg' => tomedia($qrimg), 'nickname' => $_W['wlmember']['nickname'], 'avatar' => $_W['wlmember']['avatar']);
		if (p('diyposter') && !empty($_W['wlsetting']['diyposter']['distpid']) && empty($disflag)) {
			$postertpl = pdo_get(PDO_NAME . 'poster', array('uniacid' => $_W['uniacid'], 'id' => $_W['wlsetting']['diyposter']['distpid']), array('data', 'bg'));
			$poster['bg'] = $bgimg ? $bgimg : $postertpl['bg'];
			$poster['data'] = $postertpl['data'];
		}

		if (is_wxapp() && p('wxapp') || $agent == 'wxapp') {
			$src = Wxapp::get_wxapp_qrcode('distri#invitid=' . $id, $filename);

			if (!is_error($src)) {
				$poster['qrimg'] = tomedia('../addons/' . MODULE_NAME . '/data/wxapp/' . $_W['uniacid'] . '/' . $filename . '.png');
				$filename = md5($id . 'wxapp_distri');
			}
		}

		$poster = Tools::createPoster($poster, $filename, $member);
		return $poster;
	}

	/**
     * Comment: 分销合伙人邀请会员海报生成
     * Author: hexin
     * @param $id
     * @param $disflag
     * @param string $agent
     * @param string $bgimg
     * @return array|string
     */
	public function createInvitevipPoster($id, $agent = '', $bgimg = '')
	{
		global $_W;
		global $_GPC;
		$member = $_W['wlmember'];
		$url = app_url('halfcard/halfcardopen/open', array('invitid' => $id, 'qrentry' => 1, 'aid' => $_W['aid']));
		$filename = md5($id . 'invitevip' . $bgimg);
		$data = '[{"left":"115px","top":"93px","type":"head","width":"86px","height":"86px"},{"left":"115px","top":"186px","type":"nickname","width":"86px","height":"29px","size":"10px","color":"#999999","words":"昵称","align":"center"},{"left":"115px","top":"406px","type":"qr","width":"90px","height":"90px","size":""}]';
		self::qrcodeimg($url, $filename);
		$qrimg = 'addons/weliam_merchant/data/poster/' . $_W['uniacid'] . '/qrcode_' . $filename . '.png';
		$poster = array('bg' => URL_APP_RESOURCE . '/image/poster/invitevipbg.jpg', 'data' => $data, 'qrimg' => tomedia($qrimg), 'nickname' => $_W['wlmember']['nickname'], 'avatar' => $_W['wlmember']['avatar']);
		if (p('diyposter') && !empty($_W['wlsetting']['diyposter']['invitevippid']) && empty($disflag)) {
			$postertpl = pdo_get(PDO_NAME . 'poster', array('uniacid' => $_W['uniacid'], 'id' => $_W['wlsetting']['diyposter']['invitevippid']), array('data', 'bg'));
			$poster['bg'] = $bgimg ? $bgimg : $postertpl['bg'];
			$poster['data'] = $postertpl['data'];
		}

		$poster = Tools::createPoster($poster, $filename, $member);
		return $poster;
	}

	public function checkprice($goods)
	{
		if (999.99000000000001 < $goods['price']) {
			$goods['price'] = sprintf('%.1f', $goods['price']);
		}

		if (9999.9899999999998 < $goods['price']) {
			$goods['price'] = sprintf('%.0f', $goods['price']);
		}

		if (999.99000000000001 < $goods['oldprice']) {
			$goods['oldprice'] = sprintf('%.1f', $goods['oldprice']);
		}

		if (9999.9899999999998 < $goods['oldprice']) {
			$goods['oldprice'] = sprintf('%.0f', $goods['oldprice']);
		}

		if (999.99000000000001 < $goods['vipprice']) {
			$goods['vipprice'] = sprintf('%.1f', $goods['vipprice']);
		}

		if (9999.9899999999998 < $goods['vipprice']) {
			$goods['vipprice'] = sprintf('%.0f', $goods['vipprice']);
		}

		return $goods;
	}

	public function qrcodeimg($url, $fliename)
	{
		global $_W;
		global $_GPC;
		load()->library('qrcode');

		if (empty($_W['wlsetting']['base']['qrstatus'])) {
			ob_clean();
		}

		$result = Util::long2short($url);

		if (!is_error($result)) {
			$url = $result['short_url'];
		}

		$path = IA_ROOT . '/addons/' . MODULE_NAME . '/data/poster/' . $_W['uniacid'];

		if (!is_dir($path)) {
			load()->func('file');
			mkdirs($path);
		}

		$file = 'qrcode_' . $fliename . '.png';
		$qrcode_file = $path . '/' . $file;

		if (!is_file($qrcode_file)) {
			QRcode::png($url, $qrcode_file, QR_ECLEVEL_L, 16, 2);
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
