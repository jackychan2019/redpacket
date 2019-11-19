<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class User_WeliamController extends Weliam_merchantModuleSite
{
	public function index()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '会员中心 - ' . $_W['wlsetting']['base']['name'] : '会员中心';

		if ($_W['mid']) {
			if ($_W['wlsetting']['halfcard']['halfcardtype'] == 1) {
				$where = array('mid' => $_W['mid'], 'expiretime >' => time());
			}
			else {
				$where = array('mid' => $_W['mid'], 'aid' => $_W['aid'], 'expiretime >' => time());
			}

			$halfcardflag = Member::checkhalfmember();
			$halfcardbase = Setting::wlsetting_read('halfcard');
			$collectnum = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'collect') . (' WHERE uniacid=' . $_W['uniacid'] . ' and mid=' . $_W['mid']));
			$num_1 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and mid=' . $_W['mid'] . ' and status = 0  and plugin <> \'distribution\' and plugin <> \'consumption\''));
			$num_2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and mid=' . $_W['mid'] . ' and status IN (1,8,4) and plugin <> \'distribution\' and plugin <> \'consumption\''));
			$num_3 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and mid=' . $_W['mid'] . ' and status = 2 and plugin <> \'distribution\' and plugin <> \'consumption\''));
			$num_4 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and mid=' . $_W['mid'] . ' and status = 6 and plugin <> \'distribution\' and plugin <> \'consumption\''));
			$rnum_1 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'rush_order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and mid=' . $_W['mid'] . ' and status = 0 '));
			$rnum_2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'rush_order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and mid=' . $_W['mid'] . ' and status = 1 '));
			$rnum_3 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'rush_order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and mid=' . $_W['mid'] . ' and status = 2 '));
			$rnum_4 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'rush_order') . (' WHERE uniacid=' . $_W['uniacid'] . ' and mid=' . $_W['mid'] . ' and status = 6 '));
			$num1 = $num_1 + $rnum_1;
			$num2 = $num_2 + $rnum_2;
			$num3 = $num_3 + $rnum_3;
			$num4 = $num_4 + $rnum_4;
			$distributionbase = Setting::wlsetting_read('distribution');

			if ($distributionbase['switch']) {
				if ($_W['wlmember']['distributorid']) {
					$distributor = pdo_get('wlmerchant_distributor', array('id' => $_W['wlmember']['distributorid']));

					if ($distributor['disflag']) {
						$disflag = 1;
					}
					else if ($distributionbase['mode']) {
						if ($distributor['leadid']) {
							if ($distributionbase['twoappdis']) {
								$disflag = 2;
								$rank = 2;
							}
							else {
								$disflag = 0;
							}
						}
						else if ($distributionbase['appdis']) {
							$disflag = 2;
						}
						else {
							$disflag = 0;
						}
					}
					else if ($distributionbase['appdis']) {
						$disflag = 2;
					}
					else {
						$disflag = 0;
					}
				}
				else if ($distributionbase['appdis']) {
					$disflag = 2;
				}
				else {
					$disflag = 0;
				}
			}
			else {
				$disflag = 0;
			}

			$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
			$swhere['mid'] = $_W['mid'];
			$swhere['uniacid'] = $_W['uniacid'];
			$allorderprice = pdo_getcolumn('wlmerchant_timecardrecord', $swhere, array('SUM(ordermoney)'));
			$realprice = pdo_getcolumn('wlmerchant_timecardrecord', $swhere, array('SUM(realmoney)'));
			$ellipsismoney = $allorderprice - $realprice;
			$freepackage = pdo_fetchall('SELECT title,id,merchantid,extinfo,`limit` FROM ' . tablename('wlmerchant_package') . ' WHERE `uniacid`=:uniacid AND aid IN (0,' . $_W['aid'] . ') AND status = 1 ORDER BY RAND() LIMIT 5', array(':uniacid' => $_W['uniacid']));

			foreach ($freepackage as $key => &$value) {
				if ($value['merchantid']) {
					$value['logo'] = pdo_getcolumn('wlmerchant_merchantdata', array('id' => $value['merchantid']), 'logo');
				}
				else {
					$extinfo = unserialize($value['extinfo']);
					$value['logo'] = $extinfo['storelogo'];
				}

				$value['deurl'] = app_url('halfcard/halfcard_app/packagedetail', array('id' => $value['id'], 'cardid' => $halfcardflag));
			}

			$storever = pdo_get('wlmerchant_merchantuser', array('mid' => $_W['mid'], 'uniacid' => $_W['uniacid']), array('id'));
			$storeadmin = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_merchantuser') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $_W['mid'] . ' AND ismain IN (1,3)'));

			if (p('diypage')) {
				if (!empty($_W['agentset']['diypageset']['menu_index'])) {
					$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_index']);
				}
			}

			include wl_template('member/index');
		}
		else {
			header('location:' . app_url('member/user/signin'));
		}
	}

	public function signin()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '登录 - ' . $_W['wlsetting']['base']['name'] : '登录';

		if (is_h5app()) {
			$webapp = $_W['wlsetting']['wbappset'];
		}

		if ($_W['mid']) {
			header('location:' . app_url('member/user/index'));
		}

		$backurl = urldecode($_GPC['backurl']);

		if ($_W['ispost']) {
			$mobile = $_GPC['mobile'];
			$pwd = $_GPC['pwd'];
			if (empty($mobile) || empty($pwd)) {
				exit(json_encode(array('status' => 2, 'msg' => '信息传输错误，请重试')));
			}

			$mobileinfo = pdo_get('wlmerchant_member', array('mobile' => $mobile, 'uniacid' => $_W['uniacid']), array('tokey', 'password', 'salt', 'unionid'));

			if ($mobileinfo) {
				$password = md5($pwd . $mobileinfo['salt']);

				if ($password == $mobileinfo['password']) {
					$userinfo = array();
					$userinfo['mobile'] = $mobile;
					$userinfo['pwd'] = $password;
					$userinfo['tokey'] = $mobileinfo['tokey'];
					$_SESSION['usersign'] = $userinfo;
					wl_setcookie('usersign', $userinfo, 3600 * 24 * 30);
					wl_setcookie('user_token', $userinfo['tokey'], 3600 * 24 * 30);

					if ($mobile) {
						Member::AccountMerging($mobile, true);
					}

					exit(json_encode(array('status' => 1)));
				}
				else {
					exit(json_encode(array('status' => 2, 'msg' => '手机号或密码错误')));
				}
			}
			else {
				exit(json_encode(array('status' => 2, 'msg' => '该手机号未注册')));
			}
		}

		include wl_template('member/signin');
	}

	public function wechatsign()
	{
		global $_W;
		global $_GPC;
		$auth_userinfo = mc_oauth_userinfo();
		$backurl = urldecode($_GPC['backurl']);

		if ($_GPC['vueurl']) {
			$backurl = h5_url($_GPC['vueurl']);
		}

		if (empty($_W['openid'])) {
			$_W['openid'] = $auth_userinfo['openid'];
		}

		if (!empty($_W['openid'])) {
			$member = pdo_get('wlmerchant_member', array('openid' => $_W['openid'], 'uniacid' => $_W['uniacid']), array('id', 'tokey'));
		}

		if (empty($member)) {
			if ($_W['fans']['follow'] == 1) {
				$uid = mc_openid2uid($_W['openid']);
			}
			else {
				$uid = $_W['member']['uid'];
			}

			if (!empty($uid)) {
				$member = pdo_get('wlmerchant_member', array('uid' => $uid, 'uniacid' => $_W['uniacid']), array('id'));
			}

			if ($member && $_W['openid']) {
				pdo_update('wlmerchant_member', array('openid' => $_W['openid']), array('id' => $member['id']));
			}
			else if ($_W['openid']) {
				if (empty($uid)) {
					$uid = pdo_getcolumn('mc_mapping_fans', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']), 'uid');
				}

				$data = array('uid' => $uid, 'openid' => $_W['openid'], 'uniacid' => $_W['uniacid'], 'nickname' => $auth_userinfo['nickname'], 'mobile' => '', 'password' => '', 'avatar' => str_replace('132132', '132', $auth_userinfo['headimgurl']), 'tokey' => strtoupper(MD5(sha1(time() . random(12)))), 'createtime' => time());
				pdo_insert('wlmerchant_member', $data);
			}
			else {
				wl_message('授权信息获取失败，请退出重试');
			}
		}

		$userinfo = array();
		$userinfo['mobile'] = '';
		$userinfo['pwd'] = '';
		$userinfo['openid'] = $_W['openid'];
		$userinfo['tokey'] = $member['tokey'] ? $member['tokey'] : $data['tokey'];
		$_SESSION['usersign'] = $userinfo;
		wl_setcookie('usersign', $userinfo, 3600 * 24 * 30);
		wl_setcookie('user_token', $userinfo['tokey'], 3600 * 24 * 30);
		wl_setcookie('exitlogin_code', array(), 100);

		if ($backurl) {
			header('location:' . $backurl);
		}
		else {
			header('location:' . app_url('member/user/index'));
		}
	}

	public function register()
	{
		global $_W;
		global $_GPC;

		if ($_GPC['changepwd']) {
			$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '忘记密码 - ' . $_W['wlsetting']['base']['name'] : '忘记密码';
		}
		else {
			$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '注册 - ' . $_W['wlsetting']['base']['name'] : '注册';
		}

		if ($_W['ispost']) {
			$session = json_decode(base64_decode($_GPC['__auth_sfsession']), true);

			if (is_array($session)) {
				if ($session['mobile'] != $_GPC['mobile']) {
					exit(json_encode(array('result' => 2, 'msg' => '手机号码不匹配')));
				}

				if ($session['code'] != $_GPC['contacts']) {
					exit(json_encode(array('result' => 2, 'msg' => '验证码错误，请重试')));
				}
			}
			else {
				exit(json_encode(array('result' => 2, 'msg' => '验证码已过期，请重新发送')));
			}

			exit(json_encode(array('result' => 1)));
		}

		include wl_template('member/register');
	}

	public function registering()
	{
		global $_W;
		global $_GPC;
		$mobile = $_GPC['mobile'];
		$pwd = $_GPC['pwd'];
		if (empty($mobile) || empty($pwd)) {
			exit(json_encode(array('result' => 2, 'msg' => '信息传输错误，请重试')));
		}

		$member = pdo_get('wlmerchant_member', array('mobile' => $mobile, 'uniacid' => $_W['uniacid']), array('tokey', 'id', 'salt', 'password'));

		if ($member) {
			if (empty($member['salt'])) {
				$salt = random(8);
				$data['salt'] = $salt;
			}
			else {
				$salt = $member['salt'];
			}

			$data['password'] = md5($pwd . $salt);

			if ($data['password'] == $member['password']) {
				exit(json_encode(array('result' => 2, 'msg' => '请勿使用与之前相同的密码')));
			}

			$res = pdo_update('wlmerchant_member', $data, array('id' => $member['id']));
		}
		else {
			$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' . tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
			$salt = random(8);
			$nickname = substr($mobile, 0, 3) . '****' . substr($mobile, -4, 4);
			$data = array('uniacid' => $_W['uniacid'], 'salt' => $salt, 'groupid' => $default_groupid, 'createtime' => TIMESTAMP, 'nickname' => $nickname);
			$data['mobile'] = $mobile;

			if (!empty($pwd)) {
				$data['password'] = md5($pwd . $data['salt'] . $_W['config']['setting']['authkey']);
			}

			pdo_insert('mc_members', $data);
			$uid = pdo_insertid();
			$data2 = array('uid' => $uid, 'uniacid' => $_W['uniacid'], 'nickname' => $nickname, 'mobile' => $mobile, 'salt' => $salt, 'avatar' => './addons/weliam_merchant/app/resource/image/touxiang.png', 'registerflag' => 1, 'tokey' => strtoupper(MD5(sha1(time() . random(12)))), 'createtime' => time());
			$data['password'] = $data2['password'] = md5($pwd . $salt);
			$res = pdo_insert('wlmerchant_member', $data2);
			$mid = pdo_insertid();
		}

		if ($res) {
			$userinfo = array();
			$userinfo['mobile'] = $mobile;
			$userinfo['pwd'] = $data['password'];
			$userinfo['tokey'] = $member['tokey'] ? $member['tokey'] : $data2['tokey'];
			$_SESSION['usersign'] = $userinfo;
			wl_setcookie('usersign', $userinfo, 3600 * 24 * 30);
			wl_setcookie('user_token', $userinfo['tokey'], 3600 * 24 * 30);
			exit(json_encode(array('result' => 1)));
		}
		else {
			exit(json_encode(array('result' => 0)));
		}
	}

	public function exitlogin()
	{
		wl_setcookie('usersign', '', -100);
		wl_setcookie('user_token', '', -100);
		wl_setcookie('exitlogin_code', array('code' => 1), 100);
		$_SESSION['usersign'] = '';
		exit(json_encode(array('status' => 1, 'message' => '')));
	}

	public function storecollect()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '我的收藏 - ' . $_W['wlsetting']['base']['name'] : '我的收藏';
		$collects = pdo_getall(PDO_NAME . 'collect', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid']));

		foreach ($collects as $key => $value) {
			$merchant = Store::getSingleStore($value['storeid']);

			if ($merchant) {
				$collects[$key]['store'] = $merchant;
			}
			else {
				pdo_delete('wlmerchant_collect', array('storeid' => $value['storeid']));
			}
		}

		include wl_template('member/storecollect');
	}

	public function binding()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '绑定手机号 - ' . $_W['wlsetting']['base']['name'] : '绑定手机号';

		if ($_W['ispost']) {
			$mobile = $_GPC['mobile'];
				$res = pdo_get('wlmerchant_member', array('mobile' => $mobile));
				if($res){
					exit(json_encode(array('result' => 2, 'msg' => '该号码已被绑定，请更换其他号码!')));	
				}
			if ($mobile) {
				$result = Member::AccountMerging($mobile, true);

				if ($result) {
					exit(json_encode(array('result' => 2, 'msg' => '账号已合并，请重新的登录!')));
				}
			}

			if (empty($_W['wlsetting']['base']['smsver'])) {
				$session = json_decode(base64_decode($_GPC['__auth_sfsession']), true);

				if (is_array($session)) {
					if ($session['mobile'] != $mobile) {
						exit(json_encode(array('result' => 2, 'msg' => '手机号码不匹配')));
					}

					if ($session['code'] != $_GPC['inputcode']) {
						exit(json_encode(array('result' => 2, 'msg' => '验证码错误，请重试')));
					}
				}
				else {
					exit(json_encode(array('result' => 2, 'msg' => '验证码已过期，请重新发送')));
				}
			}

			if ($_GPC['pwd']) {
				$member = pdo_get('wlmerchant_member', array('id' => $_W['mid']), array('salt'));

				if ($member) {
					if (empty($member['salt'])) {
						$salt = random(8);
						$data['salt'] = $salt;
					}
					else {
						$salt = $member['salt'];
					}

					$data['password'] = md5($_GPC['pwd'] . $salt);
				}
			}

			$data['mobile'] = $mobile;
			pdo_update(PDO_NAME . 'member', $data, array('id' => $_W['mid']));
			Message::checkMessage($_W['openid']);
			exit(json_encode(array('result' => 1)));
		}

		$reurl = $_GPC['backurl'] ? urldecode($_GPC['backurl']) : app_url('member/user');
		include wl_template('member/binding');
	}

	public function vercode()
	{
		global $_W;
		global $_GPC;
		$mobile = $_GPC['mobile'];
		$change = $_GPC['flag'];
		$areacode = $_GPC['areacode'];

		if ($_W['wlsetting']['base']['verifycode']) {
			$captcha = trim($_GPC['verifycode']);
			$hash = md5($captcha . $_W['config']['setting']['authkey']);

			if ($_GPC['__code'] != $hash) {
				exit(json_encode(array('result' => 2, 'msg' => '图形验证码错误, 请重新输入')));
			}
		}

		if (!empty($mobile)) {
			$where = ' WHERE mobile = ' . $mobile . ' AND uniacid = ' . $_W['uniacid'] . ' AND ';

			if (is_h5app()) {
				$where .= ' openid != \'\' ';
			}
			else {
				$where .= ' webapp_openid != \'\' ';
			}

			$flag = pdo_fetchcolumn('SELECT id FROM ' . tablename(PDO_NAME . 'member') . $where);
			if ($flag && empty($change)) {
				exit(json_encode(array('result' => 2, 'msg' => '该手机号已被绑定')));
			}
			else {
				$code = rand(1000, 9999);
				$res = Sms::smsSF($code, $areacode . $mobile);

				if ($res['result'] == 1) {
					$cookie = array();
					$cookie['mobile'] = $mobile;
					$cookie['code'] = $code;
					$session = base64_encode(json_encode($cookie));
					isetcookie('__auth_sfsession', $session, 600, true);
					exit(json_encode($res));
				}
				else {
					exit(json_encode($res));
				}
			}
		}

		exit(json_encode(array('result' => 2, 'msg' => '手机号不得为空')));
	}

	public function addresslist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '地址列表 - ' . $_W['wlsetting']['base']['name'] : '地址列表';
		$flag = $_GPC['flag'];

		if ($flag) {
			$url = $_GPC['backurl'];
			$reurl = urldecode($url);
		}
		else {
			$reurl = app_url('member/user/index');
		}

		$address = pdo_getall('wlmerchant_address', array('mid' => $_W['mid'], 'uniacid' => $_W['uniacid']));
		include wl_template('member/addresslist');
	}

	public function setmorenaddress()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$moren = pdo_get('wlmerchant_address', array('mid' => $_W['mid'], 'status' => 1), array('id'));
		pdo_update('wlmerchant_address', array('status' => 0), array('id' => $moren['id']));
		pdo_update('wlmerchant_address', array('status' => 1), array('id' => $id));
		header('location:' . app_url('member/user/addresslist'));
	}

	public function addwechat()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '添加地址 - ' . $_W['wlsetting']['base']['name'] : '添加地址';
		$reurl = $_GPC['backurl'] ? urldecode($_GPC['backurl']) : app_url('member/user/addresslist');
		$shareAddress = Member::shareAddress();
		include wl_template('member/createadd');
	}

	public function deletes()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$res = pdo_delete('wlmerchant_address', array('id' => $id));

		if ($res) {
			exit(json_encode(array('status' => 1, 'message' => $res)));
		}
		else {
			exit(json_encode(array('status' => 0, 'message' => '删除地址失败')));
		}
	}

	public function createaddress()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '添加地址 - ' . $_W['wlsetting']['base']['name'] : '添加地址';
		$id = $_GPC['id'];

		if ($id) {
			$addres = pdo_get('wlmerchant_address', array('id' => $id));
		}

		$reurl = $_GPC['backurl'] ? urldecode($_GPC['backurl']) : app_url('member/user/addresslist');

		if ($_W['ispost']) {
			$citys = explode(' ', $_GPC['citys']);

			if (!empty($id)) {
				$data = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'name' => $_GPC['myname'], 'tel' => $_GPC['myphone'], 'province' => $citys[0], 'city' => $citys[1], 'county' => $citys[2], 'detailed_address' => $_GPC['detailed'], 'status' => $_GPC['status'], 'addtime' => time());

				if ($_GPC['status'] == 1) {
					$moren = pdo_get('wlmerchant_address', array('mid' => $_W['mid'], 'status' => 1), array('id'));
					pdo_update('wlmerchant_address', array('status' => 0), array('id' => $moren['id']));
				}

				if (pdo_update('wlmerchant_address', $data, array('id' => $id))) {
					exit(json_encode(array('status' => 1, 'message' => $res)));
				}
				else {
					exit(json_encode(array('status' => 0, 'message' => '收货地址编辑失败')));
				}
			}
			else {
				$data1 = array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'mid' => $_W['mid'], 'name' => $_GPC['myname'], 'tel' => $_GPC['myphone'], 'province' => $citys[0], 'city' => $citys[1], 'county' => $citys[2], 'detailed_address' => $_GPC['detailed'], 'addtime' => time(), 'status' => $_GPC['status']);

				if ($_GPC['status'] == 1) {
					$moren = pdo_get('wlmerchant_address', array('mid' => $_W['mid'], 'status' => 1), array('id'));
					pdo_update('wlmerchant_address', array('status' => 0), array('id' => $moren['id']));
				}

				if (pdo_insert('wlmerchant_address', $data1)) {
					exit(json_encode(array('status' => 1, 'message' => $res)));
				}
				else {
					exit(json_encode(array('status' => 0, 'message' => '收货地址编辑失败')));
				}
			}
		}

		include wl_template('member/createadd');
	}

	public function balancerecord()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '会员中心 - ' . $_W['wlsetting']['base']['name'] : '会员中心';
		include wl_template('member/balancerecord');
	}

	public function getbalancerecord()
	{
		global $_W;
		global $_GPC;
		$records = pdo_fetchall('SELECT id,num,createtime,remark FROM ' . tablename('mc_credits_record') . (' WHERE uid = ' . $_W['wlmember']['uid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND credittype = \'credit2\' ORDER BY createtime DESC LIMIT 2000'));

		if ($records) {
			foreach ($records as $key => &$va) {
				$va['createtime'] = date('Y-m-d H:i:s', $va['createtime']);
			}

			$records = array_values($records);
			exit(json_encode(array('errno' => 0, 'data' => $records)));
		}
		else {
			exit(json_encode(array('errno' => 1, 'data' => 0)));
		}
	}

	public function creditrecord()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '会员中心 - ' . $_W['wlsetting']['base']['name'] : '会员中心';
		include wl_template('member/creditrecord');
	}

	public function getcreditrecord()
	{
		global $_W;
		global $_GPC;
		$records = pdo_fetchall('SELECT id,num,createtime,remark FROM ' . tablename('mc_credits_record') . (' WHERE uid = ' . $_W['wlmember']['uid'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND credittype = \'credit1\' ORDER BY createtime DESC LIMIT 2000'));

		if ($records) {
			foreach ($records as $key => &$va) {
				$va['createtime'] = date('Y-m-d H:i:s', $va['createtime']);
			}

			$records = array_values($records);
			exit(json_encode(array('errno' => 0, 'data' => $records)));
		}
		else {
			exit(json_encode(array('errno' => 1, 'data' => 0)));
		}
	}

	public function userdata()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '个人信息 - ' . $_W['wlsetting']['base']['name'] : '个人信息';
		$member = pdo_get(PDO_NAME . 'member', array('id' => $_W['mid']));

		if ($_GPC['data']) {
			pdo_update('wlmerchant_member', $_GPC['data'], array('id' => $_W['mid']));
			wl_message('资料保存成功');
		}

		include wl_template('member/userdata');
	}

	public function recharge()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '余额充值 - ' . $_W['wlsetting']['base']['name'] : '余额充值';
		$settings = Setting::wlsetting_read('recharge');

		if ($settings['status']) {
			$count = count($settings['kilometre']);
			$i = 0;

			while ($i < $count) {
				$array[$i]['kilometre'] = $settings['kilometre'][$i];
				$array[$i]['kilmoney'] = $settings['kilmoney'][$i];
				++$i;
			}
		}
		else {
			wl_message('余额充值暂未开启', referer(), 'error');
		}

		foreach ($array as $key => $val) {
			$dos[$key] = $val['kilometre'];
		}

		array_multisort($dos, SORT_ASC, $array);
		include wl_template('member/recharge');
	}

	public function rechargeorder()
	{
		global $_W;
		global $_GPC;
		$money = sprintf('%.2f', trim($_GPC['money']));

		if ($money < 0.01) {
			exit(json_encode(array('errno' => 1, 'msg' => '充值金额错误')));
		}

		$member = pdo_get('wlmerchant_member', array('id' => $_W['mid']), array('uid'));

		if (empty($member['uid'])) {
			exit(json_encode(array('errno' => 1, 'msg' => '用户UID错误')));
		}

		$mastmobile = unserialize($_W['wlsetting']['base']['plugin']);
		if (empty($_W['wlmember']['mobile']) && in_array('recharge', $mastmobile)) {
			exit(json_encode(array('errno' => 2, 'msg' => '未绑定手机号，去绑定？')));
		}

		$data = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'aid' => $_W['aid'], 'fkid' => 0, 'sid' => 0, 'status' => 0, 'paytype' => 0, 'createtime' => time(), 'orderno' => createUniontid(), 'price' => $money, 'num' => 1, 'plugin' => 'member', 'payfor' => 'recharge', 'issettlement' => 3);
		$res = pdo_insert(PDO_NAME . 'order', $data);
		$orderid = pdo_insertid();

		if ($orderid) {
			exit(json_encode(array('errno' => 0, 'msg' => $orderid)));
		}
		else {
			exit(json_encode(array('errno' => 1, 'msg' => '订单创建失败，请重试')));
		}
	}

	public function topay()
	{
		global $_W;
		global $_GPC;
		$orderid = $_GPC['orderid'];
		$order = pdo_get('wlmerchant_order', array('id' => $orderid));
		$params = array('tid' => $order['orderno'], 'ordersn' => $order['orderno'], 'title' => '余额充值', 'fee' => $order['price'], 'rechargeflag' => 1);
		$log = pdo_get(PDO_NAME . 'paylog', array('uniacid' => $_W['uniacid'], 'tid' => $params['tid']));

		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['openid'], 'module' => 'weliam_merchant', 'plugin' => 'Member', 'payfor' => 'Charge', 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0');
			pdo_insert(PDO_NAME . 'paylog', $log);
		}

		$this->pay($params);
	}

	/**
     * Comment: webapp 用户使用微信登录
     * Author: zzw
     */
	public function WeChatLogin()
	{
		global $_W;
		global $_GPC;
		if ($_POST || $_W['ispost']) {
			$getUserInfo = json_decode($_POST['token'], true);
			$APPopenid = $getUserInfo['openid'];
			$uid = $getUserInfo['unionid'];
			$userInfo = pdo_fetch('SELECT * FROM ' . tablename(PDO_NAME . 'member') . (' WHERE webapp_openid = \'' . $APPopenid . '\' OR unionid = \'' . $uid . '\' '));
			if ($userInfo['webapp_openid'] && $userInfo['unionid']) {
			}
			else {
				if (!$userInfo['webapp_openid'] && $userInfo['unionid']) {
					pdo_update(PDO_NAME . 'member', array('webapp_openid' => $APPopenid), array('id' => $userInfo['id']));
				}
				else {
					if ($userInfo['webapp_openid'] && !$userInfo['unionid']) {
					}
					else {
						if (!$userInfo['webapp_openid'] && !$userInfo['unionid']) {
							$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' . tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
							$salt = random(8);
							$nickname = $getUserInfo['nickname'];
							$data = array('uniacid' => $_W['uniacid'], 'salt' => $salt, 'groupid' => $default_groupid, 'createtime' => TIMESTAMP, 'nickname' => $nickname);
							pdo_insert('mc_members', $data);
							$uid = pdo_insertid();
							$insert['uid'] = $uid;
							$insert['unionid'] = $getUserInfo['unionid'];
							$insert['uniacid'] = $_W['uniacid'];
							$insert['aid'] = $_W['aid'] ? $_W['aid'] : 0;
							$insert['webapp_openid'] = $APPopenid;
							$insert['nickname'] = $getUserInfo['nickname'];
							$insert['avatar'] = $getUserInfo['headimgurl'];
							$insert['gender'] = $getUserInfo['sex'];
							$insert['createtime'] = time();
							$insert['tokey'] = strtoupper(MD5(sha1(time() . random(12))));
							pdo_insert(PDO_NAME . 'member', $insert);
						}
					}
				}
			}
		}

		if (!$_W['ispost']) {
			$info['unionid'] = $_GPC['unionid'];
			$info['tokey'] = $userInfo['tokey'] ? $userInfo['tokey'] : $insert['tokey'];
			$_SESSION['usersign'] = $info;
			wl_setcookie('usersign', $info, 3600 * 24 * 30);
			wl_setcookie('user_token', $info['tokey'], 3600 * 24 * 30);
			$url = app_url('member/user/index');
			header('location:' . $url);
		}
	}

	public function withdrawals()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '申请提现 - ' . $_W['wlsetting']['base']['name'] : '申请提现';
		$memberInfo = pdo_get('wlmerchant_member', array('id' => $_W['mid']));
		$nowmoney = pdo_getcolumn('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $memberInfo['uid']), 'credit2');
		$alcredit = pdo_getcolumn('wlmerchant_settlement_record', array('mid' => $memberInfo['id'], 'type' => 4, 'status' => 4), array('SUM(sapplymoney)'));
		$cashsets = Setting::wlsetting_read('cashset');

		if (empty($cashsets['withdrawals'])) {
			wl_message('用户余额提现已关闭', app_url('member/user/recharge'), 'warning');
		}

		$_GPC['type'] = $_GPC['type'] ? $_GPC['type'] : 'apply';
		$payment_type = $cashsets['payment_type'];
		$syssalepercent = sprintf('%.2f', $cashsets['memberpercent']);

		if (0 < $_GPC['settlementmoney']) {
			$shopIntervalTime = $cashsets['shopIntervalTime'];

			if (0 < $shopIntervalTime) {
				$startTime = pdo_fetchcolumn('SELECT applytime FROM ' . tablename(PDO_NAME . 'settlement_record') . (' WHERE mid = ' . $_W['mid'] . ' AND type = 4 AND uniacid = ' . $_W['uniacid'] . ' ORDER BY applytime DESC '));
				$interval = time() - $startTime;
				$intervalDay = $interval / 3600 / 24;
				$intercalRes = ceil($shopIntervalTime - $intervalDay);

				if (0 < $intercalRes) {
					exit(json_encode(array('errno' => 1, 'message' => '请等' . $intercalRes . '天后再申请！')));
				}
			}

			if ($_GPC['settlementmoney'] < $cashsets['lowsetmoney']) {
				exit(json_encode(array('errno' => 1, 'message' => '申请失败，最低提现金额为' . $cashsets['lowsetmoney'] . '元。')));
			}

			$settlementmoney = $_GPC['settlementmoney'];
			$payment_type = $_GPC['payment_type'];
			if ($payment_type == 1 && empty($memberInfo['alipay'])) {
				wl_json(1, '请先前往个人中心完善支付宝账号信息');
			}
			else {
				if ($payment_type == 3 && (empty($memberInfo['card_number']) || empty($memberInfo['bank_name']))) {
					wl_json(1, '请先前往个人中心完善银行账号信息');
				}
			}

			$nowmoney = pdo_getcolumn('mc_members', array('uniacid' => $_W['uniacid'], 'uid' => $memberInfo['uid']), 'credit2');

			if ($nowmoney < $settlementmoney) {
				wl_json(1, '用户可提现余额不足');
			}

			$data = array('uniacid' => $_W['uniacid'], 'mid' => $memberInfo['id'], 'aid' => $_W['aid'], 'status' => 2, 'type' => 4, 'sapplymoney' => $settlementmoney, 'sgetmoney' => sprintf('%.2f', $settlementmoney - $syssalepercent * $settlementmoney / 100), 'spercentmoney' => sprintf('%.2f', $syssalepercent * $settlementmoney / 100), 'spercent' => $syssalepercent, 'applytime' => TIMESTAMP, 'updatetime' => TIMESTAMP, 'sopenid' => $memberInfo['openid'], 'payment_type' => $payment_type);

			if (pdo_insert(PDO_NAME . 'settlement_record', $data)) {
				$orderid = pdo_insertid();
				$res = Member::credit_update_credit2($memberInfo['id'], 0 - $settlementmoney, '用户余额提现', $orderid);

				if ($res) {
					if (!empty($_W['wlsetting']['noticeMessage']['adminopenid'])) {
						$first = '您好，有一个用户余额提现申请待审核。';
						$keyword1 = '用户[' . $memberInfo['nickname'] . ']申请提现' . $settlementmoney . '元';
						$keyword2 = '待审核';
						$remark = '请尽快前往系统后台审核';
						$url = app_url('dashboard/home/index');
						Message::jobNotice($_W['wlsetting']['noticeMessage']['adminopenid'], $first, $keyword1, $keyword2, $remark, $url);
					}

					exit(json_encode(array('errno' => 0, 'message' => '申请成功！')));
				}
				else {
					exit(json_encode(array('errno' => 1, 'message' => '申请失败，请重试')));
				}
			}

			exit(json_encode(array('errno' => 1, 'message' => '申请失败！')));
		}

		if ($_GPC['type'] == 'apply') {
			if ($_GPC['num']) {
				$num = $_GPC['num'];

				if ($nowmoney < $num) {
					exit(json_encode(array('errno' => 1, 'msg' => '账户余额不足')));
				}
				else {
					$surplusmoney = sprintf('%.2f', $nowmoney - $num);
					$realmoney = sprintf('%.2f', $num - $syssalepercent * $num / 100);
					exit(json_encode(array('errno' => 0, 'syssalepercent' => $syssalepercent, 'realmoney' => $realmoney, 'num' => $num, 'surplusmoney' => $surplusmoney)));
				}
			}
		}

		if ($_GPC['type'] == 'deling' || $_GPC['type'] == 'finish' || $_GPC['type'] == 'reject') {
			$where = array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'type' => 4);

			if ($_GPC['type'] == 'deling') {
				$where['#status#'] = '(1,2,3)';
			}

			if ($_GPC['type'] == 'finish') {
				$where['status'] = 5;
			}

			if ($_GPC['type'] == 'reject') {
				$where['#status#'] = '(-1,-2)';
			}

			$recordData = Util::getNumData('*', PDO_NAME . 'settlement_record', $where);
			$record = $recordData[0];
		}

		include wl_template('member/cash');
	}

	public function veriflist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '核销记录 - ' . $_W['wlsetting']['base']['name'] : '核销记录';
		$halfcardtext = !empty($_W['agentset']['halfcard']['halfcardtext']) ? $_W['agentset']['halfcard']['halfcardtext'] : '一卡通';
		$type = trim($_GPC['type']);
		$admin = intval($_GPC['admin']);
		$time = trim($_GPC['time']);
		$pindex = intval($_GPC['pindex']);
		$where = array('uniacid' => $_W['uniacid'], 'verifmid' => $_W['mid']);

		if (!empty($type)) {
			$where['plugin'] = $type;
		}

		if (!empty($admin)) {
			$where['storeid'] = $admin;
			$adname = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $admin), 'storename');
		}

		if (!empty($time)) {
			if ($time == 'today') {
				$starttime = strtotime(date('Y-m-d'));
				$endtime = $starttime + 86399;
			}
			else if ($time == 'week') {
				$starttime = strtotime('previous monday');
				$endtime = time();
			}
			else if ($time == 'month') {
				$starttime = mktime(0, 0, 0, date('m'), 1, date('Y'));
				$endtime = time();
			}
			else {
				$times = explode('.', $time);
				$starttime = time() < strtotime($times[0]) ? strtotime(date('Y-m-d')) : strtotime($times[0]);
				$endtime = strtotime($times[1]) < $starttime ? $starttime + 86399 : strtotime($times[1]) + 86399;
			}

			$where['createtime>'] = $starttime;
			$where['createtime<'] = $endtime;
		}
		else {
			$starttime = strtotime('previous monday');
			$endtime = strtotime(date('Y-m-d'));
		}

		$alladmin = pdo_getall(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid']), array('storeid'));

		if ($alladmin) {
			foreach ($alladmin as $ak => &$av) {
				$av['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $av['storeid']), 'storename');
			}
		}

		if ($_W['ispost']) {
			$data = Util::createStandardWhereString($where);
			$allfen = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename(PDO_NAME . 'verifrecord') . (' WHERE ' . $data[0] . ' '), $data[1]);
			$myorder = Util::getNumData('*', PDO_NAME . 'verifrecord', $where, 'ID DESC', $pindex, 10, 1);
			$allnum = $myorder[2];
			$myorder = $myorder[0];

			foreach ($myorder as $key => &$value) {
				$member = Member::getMemberByMid($value['mid'], array('nickname', 'mobile'));
				$value['nickname'] = $member['nickname'];
				$value['mobile'] = $member['mobile'];
				$verifnickname = pdo_getcolumn(PDO_NAME . 'merchantuser', array('uniacid' => $_W['uniacid'], 'mid' => $_W['mid'], 'storeid' => $value['storeid']), 'name');
				$value['verifnickname'] = $verifnickname;
				$value['createtime'] = date('Y-m-d H:i:s', $value['createtime']);

				if ($value['plugin'] == 'rush') {
					$optionid = pdo_getcolumn(PDO_NAME . 'rush_order', array('id' => $value['orderid']), 'optionid');
				}
				else {
					if ($value['plugin'] == 'groupon' || $value['plugin'] == 'wlfightgroup') {
						$optionid = pdo_getcolumn(PDO_NAME . 'order', array('id' => $value['orderid']), 'specid');
					}
				}

				if ($optionid) {
					$value['optionname'] = pdo_getcolumn(PDO_NAME . 'goods_option', array('id' => $optionid), 'title');
				}

				$value['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('uniacid' => $_W['uniacid'], 'id' => $value['storeid']), 'storename');
			}

			exit(json_encode(array('errno' => 0, 'data' => $myorder, 'allnum' => intval($allnum), 'allfen' => intval($allfen))));
		}

		$alltype = array('halfcard1' => $halfcardtext, 'halfcard2' => '大礼包', 'rush' => '抢购', 'wlcoupon' => '卡券', 'wlfightgroup' => '拼团', 'groupon' => '团购', 'bargain' => '砍价');
		include wl_template('member/veriflist');
	}
}

?>
