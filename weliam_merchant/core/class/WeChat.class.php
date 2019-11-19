<?php
//dezend by http://www.sucaihuo.com/
class WeChat
{
	/**
     * Comment: 通过code获取小程序用户的openid & session_key
     * Author: zzw
     * @param $code     临时code信息
     * @return array    获取的信息
     */
	static public function getOpenid($code)
	{
		$set = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'city_selection_set'), 'value'));
		$appid = trim($set['appid']);
		$secret = trim($set['secret']);
		$url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $secret . '&js_code=' . $code . '&grant_type=authorization_code';
		$cache_code = cache_load('WeChat_code');

		if ($cache_code != $code) {
			$info = get_object_vars(json_decode(file_get_contents($url)));
			cache_write('WeChat_openid_info', $info);
		}
		else {
			$WeChat_openid = cache_load('WeChat_openid_info');
			return $WeChat_openid;
		}

		cache_write('WeChat_code', $code);
		return $info;
	}

	/**
     * Comment: 验证是否为一卡通会员
     * Author: zzw
     */
	static public function VipVerification($mid)
	{
		global $_W;
		$wlsetting = pdo_get(PDO_NAME . 'setting', array('uniacid' => $_W['uniacid'], 'key' => 'halfcard'));
		$wlsetting['value'] = unserialize($wlsetting['value']);
		$now = time();

		if ($wlsetting['value']['halfcardtype'] == 2) {
			$halfcardflag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $mid . ' AND aid = ' . $_W['aid'] . ' AND expiretime > ' . $now . ' AND disable != 1'));
		}
		else {
			$halfcardflag = pdo_fetch('SELECT id FROM ' . tablename('wlmerchant_halfcardmember') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND mid = ' . $mid . ' AND expiretime > ' . $now . ' AND disable != 1'));
		}

		return $halfcardflag;
	}

	/**
     * Comment: 小程序获取openid  返回判断
     * Author: zzw
     * @param $code            返回码
     * @param string $errmsg 错误内容
     * @return mixed|string
     */
	static public function errorCode($code, $errmsg = '未知错误')
	{
		$errors = array(-1 => '系统繁忙', 0 => '请求成功', 40001 => '获取access_token时AppSecret错误，或者access_token无效', 40002 => '不合法的凭证类型', 40003 => '不合法的OpenID', 40004 => '不合法的媒体文件类型', 40005 => '不合法的文件类型', 40006 => '不合法的文件大小', 40007 => '不合法的媒体文件id', 40008 => '不合法的消息类型', 40009 => '不合法的图片文件大小', 40010 => '不合法的语音文件大小', 40011 => '不合法的视频文件大小', 40012 => '不合法的缩略图文件大小', 40013 => '不合法的APPID', 40014 => '不合法的access_token', 40015 => '不合法的菜单类型', 40016 => '不合法的按钮个数', 40017 => '不合法的按钮个数', 40018 => '不合法的按钮名字长度', 40019 => '不合法的按钮KEY长度', 40020 => '不合法的按钮URL长度', 40021 => '不合法的菜单版本号', 40022 => '不合法的子菜单级数', 40023 => '不合法的子菜单按钮个数', 40024 => '不合法的子菜单按钮类型', 40025 => '不合法的子菜单按钮名字长度', 40026 => '不合法的子菜单按钮KEY长度', 40027 => '不合法的子菜单按钮URL长度', 40028 => '不合法的自定义菜单使用用户', 40029 => '不合法的oauth_code', 40030 => '不合法的refresh_token', 40031 => '不合法的openid列表', 40032 => '不合法的openid列表长度', 40033 => '不合法的请求字符，不能包含\\uxxxx格式的字符', 40035 => '不合法的参数', 40038 => '不合法的请求格式', 40039 => '不合法的URL长度', 40050 => '不合法的分组id', 40051 => '分组名字不合法', 41001 => '缺少access_token参数', 41002 => '缺少appid参数', 41003 => '缺少refresh_token参数', 41004 => '缺少secret参数', 41005 => '缺少多媒体文件数据', 41006 => '缺少media_id参数', 41007 => '缺少子菜单数据', 41008 => '缺少oauth code', 41009 => '缺少openid', 42001 => 'access_token超时', 42002 => 'refresh_token超时', 42003 => 'oauth_code超时', 43001 => '需要GET请求', 43002 => '需要POST请求', 43003 => '需要HTTPS请求', 43004 => '需要接收者关注', 43005 => '需要好友关系', 44001 => '多媒体文件为空', 44002 => 'POST的数据包为空', 44003 => '图文消息内容为空', 44004 => '文本消息内容为空', 45001 => '多媒体文件大小超过限制', 45002 => '消息内容超过限制', 45003 => '标题字段超过限制', 45004 => '描述字段超过限制', 45005 => '链接字段超过限制', 45006 => '图片链接字段超过限制', 45007 => '语音播放时间超过限制', 45008 => '图文消息超过限制', 45009 => '接口调用超过限制', 45010 => '创建菜单个数超过限制', 45015 => '回复时间超过限制', 45016 => '系统分组，不允许修改', 45017 => '分组名字过长', 45018 => '分组数量超过上限', 45056 => '创建的标签数过多，请注意不能超过100个', 45057 => '该标签下粉丝数超过10w，不允许直接删除', 45058 => '不能修改0/1/2这三个系统默认保留的标签', 45059 => '有粉丝身上的标签数已经超过限制', 45157 => '标签名非法，请注意不能和其他标签重名', 45158 => '标签名长度超过30个字节', 45159 => '非法的标签', 46001 => '不存在媒体数据', 46002 => '不存在的菜单版本', 46003 => '不存在的菜单数据', 46004 => '不存在的用户', 47001 => '解析JSON/XML内容错误', 48001 => 'api功能未授权', 50001 => '用户未授权该api', 40070 => '基本信息baseinfo中填写的库存信息SKU不合法。', 41011 => '必填字段不完整或不合法，参考相应接口。', 40056 => '无效code，请确认code长度在20个字符以内，且处于非异常状态（转赠、删除）。', 43009 => '无自定义SN权限，请参考开发者必读中的流程开通权限。', 43010 => '无储值权限,请参考开发者必读中的流程开通权限。', 43011 => '无积分权限,请参考开发者必读中的流程开通权限。', 40078 => '无效卡券，未通过审核，已被置为失效。', 40079 => '基本信息base_info中填写的date_info不合法或核销卡券未到生效时间。', 45021 => '文本字段超过长度限制，请参考相应字段说明。', 40080 => '卡券扩展信息cardext不合法。', 40097 => '基本信息base_info中填写的参数不合法。', 49004 => '签名错误。', 43012 => '无自定义cell跳转外链权限，请参考开发者必读中的申请流程开通权限。', 40099 => '该code已被核销。', 61005 => '缺少接入平台关键数据，等待微信开放平台推送数据，请十分钟后再试或是检查“授权事件接收URL”是否写错（index.php?c=account&amp;a=auth&amp;do=ticket地址中的&amp;符号容易被替换成&amp;amp;）', 61023 => '请重新授权接入该公众号');
		$code = strval($code);

		if ($errors[$code]) {
			return $errors[$code];
		}

		return $errmsg;
	}

	/**
     * Comment: 获取好评信息
     * Author: zzw
     * @param bool $mid 用户id
     * @param $where            查询条件
     * @param string $limit 查询条数
     * @param bool $getGoods 是否查询商品信息
     * @return array
     */
	static public function getPraise($mid = false, $where, $limit = '', $getGoods = false)
	{
		global $_W;
		$query = '\'coupon\',\'wlfightgroup\',\'groupon\',\'rush\'';
		$sql = 'SELECT a.id,a.pic,a.sid,a.text,a.createtime,a.headimg,a.nickname,a.star,a.plugin,b.fkid as goods_id,r.activityid as rush_id FROM ' . tablename(PDO_NAME . 'comment') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'order') . ' b ON a.idoforder = b.id AND a.plugin = b.plugin AND b.plugin <> \'\' ' . ' LEFT JOIN ' . tablename(PDO_NAME . 'rush_order') . ' r ON a.idoforder = r.id AND a.plugin = \'rush\' ' . (' WHERE a.level = 1 AND a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'] . ' ' . $where . ' AND a.plugin IN (' . $query . ')');
		$count = count(pdo_fetchall($sql));
		$sql .= $limit;
		$info = pdo_fetchall($sql);

		foreach ($info as $k => &$v) {
			$v['createtime'] = date('Y-m-d', $v['createtime']);
			$v['pic'] = unserialize($v['pic']);
			$v['star'] = intval($v['star']);

			foreach ($v['pic'] as $key => &$val) {
				$val = tomedia($val);
			}

			$goods = '';

			if ($getGoods == 'true') {
				if ($v['plugin'] == 'coupon') {
					$goods = wlCoupon::getSingleCoupons($v['goods_id'], 'logo,title as name,price,merchantid as sid');
				}
				else if ($v['plugin'] == 'wlfightgroup') {
					$goods = Wlfightgroup::getSingleGood($v['goods_id'], 'logo,name,price,merchantid as sid');
				}
				else if ($v['plugin'] == 'groupon') {
					$goods = pdo_fetch('SELECT thumb as logo,`name`,price,sid FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE id= ' . $v['goods_id'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid']));
				}
				else {
					if ($v['plugin'] == 'rush') {
						$goods = pdo_fetch('SELECT thumb as logo,`name`,price,sid FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE id= ' . $v['rush_id'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid']));
					}
				}
			}

			if ($goods) {
				$v['goods_logo'] = tomedia($goods['logo']);
				$v['goods_name'] = $goods['name'];
				$v['goods_price'] = $goods['price'];
				$v['sid'] = $goods['sid'];
			}

			$v['fabulousNum'] = intval(implode(pdo_fetch('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'fabulous') . (' WHERE `relation_id` = ' . $v['id'] . ' AND `class` = 1'))));

			if ($mid) {
				$v['fabulousState'] = pdo_get(PDO_NAME . 'fabulous', array('relation_id' => $v['id'], 'class' => 1, 'mid' => $mid)) ? true : false;
			}
			else {
				$v['fabulousState'] = false;
			}

			switch ($v['plugin']) {
			case 'rush':
				$v['praise_type'] = 1;
				break;

			case 'groupon':
				$v['praise_type'] = 2;
				break;

			case 'wlfightgroup':
				$v['praise_type'] = 3;
				break;

			case 'coupon':
				$v['praise_type'] = 4;
				break;
			}

			$v['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $v['sid']), 'storename');
		}

		$data['info'] = $info;
		$data['count'] = $count;
		return $data;
	}

	/**
     * Comment: 获取头条信息列表
     * Author: zzw
     * @param bool $shop_id 商品id，存在时只获取当前商品的头条信息
     * @param int $page 分页页数
     * @param int $pageNum 每页的数量
     * @return array
     */
	static public function getHeadline($shop_id = false, $page = 1, $pageNum = 10)
	{
		global $_W;

		if ($shop_id) {
			$where['sid'] = $shop_id;
		}

		$where['uniacid'] = $_W['uniacid'];
		$where['aid'] = $_W['aid'] ? $_W['aid'] : 0;
		$list = Util::paging('headline_content', $page, $pageNum, $where, array('id', 'title', 'summary', 'display_img', 'author', 'author_img', 'browse', 'one_id', 'two_id'), array('release_time DESC'));

		foreach ($list as $k => &$v) {
			$v['display_img'] = tomedia($v['display_img']);
			$v['author_img'] = tomedia($v['author_img']);
			$v['one_name'] = implode(pdo_get(PDO_NAME . 'headline_class', array('id' => $v['one_id']), array('name')));
			$v['two_name'] = implode(pdo_get(PDO_NAME . 'headline_class', array('id' => $v['two_id']), array('name')));
			unset($v['one_id']);
			unset($v['two_id']);
		}

		return $list;
	}

	/**
     * Comment: 获取某个店铺销量最好的商品
     * Author: zzw
     * @param $Atable       商品表
     * @param $Btable       订单表
     * @param $field        查询的字段信息
     * @param $where        查询条件
     * @param $group        分组信息
     * @param $relation     两表之间的关联信息
     * @param $SpareW       备用条件，如果没有销量最好的商品时 查询任意一条本店铺的商品
     * @param $SpareF       备用查询字段
     * @return array        销量最好的商品的信息
     */
	static public function getSalesChampion($Atable, $Btable, $field, $where, $group, $relation, $SpareW, $SpareF)
	{
		$info = pdo_fetchall('SELECT ' . $field . ' FROM ' . tablename(PDO_NAME . $Atable) . ' a LEFT JOIN ' . tablename(PDO_NAME . $Btable) . (' b ON ' . $relation . ' ') . (' WHERE ' . $where . ' GROUP BY ' . $group));
		array_multisort(array_column($info, 'num'), SORT_DESC, $info);
		$info = $info[0];

		if (!$info) {
			$info = pdo_fetchall('SELECT ' . $SpareF . ' FROM ' . tablename(PDO_NAME . $Atable) . (' WHERE ' . $SpareW));
			$info = $info[0];
		}

		return $info ? $info : '';
	}

	/**
     * Comment: 通过店铺列表 获取店铺每种类型的商品中销量最好的一个
     * Author: zzw
     * @param $shopList     店铺列表
     * @return bool
     */
	static public function getStoreList($shopList)
	{
		global $_W;
		global $_GPC;

		foreach ($shopList as $k => &$v) {
			$id = $v['id'];
			$headline = WeChat::getHeadline($id, 1, 1);
			$headline = $headline[0] ? $headline[0] : '';

			if ($headline) {
				unset($headline['summary']);
				unset($headline['display_img']);
				unset($headline['author']);
				unset($headline['author_img']);
				unset($headline['browse']);
				unset($headline['one_name']);
				unset($headline['two_name']);
			}

			$v['headline'] = $headline;
			$goods['active'] = self::getSalesChampion('rush_activity', 'rush_order', 'a.id,a.name,count(b.activityid) as num', 'a.sid = ' . $id . ' AND a.status IN (1,2) ', 'b.activityid', 'a.id = b.activityid', ' sid = ' . $id . ' AND status IN (1,2) ', 'id,name');
			$goods['groupon'] = self::getSalesChampion('groupon_activity', 'order', 'a.id,a.name,count(b.fkid) as num', 'a.sid = ' . $id . ' AND a.status IN (1,2) AND b.plugin = \'groupon\' ', 'b.fkid', 'a.id = b.fkid', ' sid = ' . $id . ' AND status IN (1,2) ', 'id,name');
			$goods['halfcard'] = self::getSalesChampion('halfcardlist', 'timecardrecord', 'a.id,a.title as name,count(b.activeid) as num ', 'a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'] . ' AND a.status = 1 AND b.type = 1 AND b.merchantid = ' . $id, 'b.activeid', 'a.id = b.activeid', ' uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 1  AND merchantid = ' . $id . ' ', 'id,title as name');
			$goods['packages'] = self::getSalesChampion('package', 'timecardrecord', 'a.id,a.title as name,count(b.activeid) as num ', 'a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'] . ' AND a.status = 1 AND b.type = 2 AND b.merchantid = ' . $id, 'b.activeid', 'a.id = b.activeid', ' uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 1  AND merchantid = ' . $id . ' ', 'id,title as name');
			$goods['coupon'] = self::getSalesChampion('couponlist', 'order', 'a.id,a.title as name,count(b.fkid) as num ', 'a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'] . ' AND a.merchantid = ' . $id . ' AND a.status = 1 AND b.plugin = \'coupon\'', 'b.fkid', 'a.id = b.fkid', ' uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND merchantid = ' . $id . ' AND status = 1 ', 'id,title as name');
			$goods['fightgroup'] = self::getSalesChampion('fightgroup_goods', 'order', 'a.id,a.name,count(b.fkid) as num ', 'a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'] . ' AND a.merchantid = ' . $id . ' AND a.status = 1 AND b.plugin = \'wlfightgroup\'', 'b.fkid', 'a.id = b.fkid', ' uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND merchantid = ' . $id . ' AND status = 1 ', 'id,name');
			unset($v['location']);
			unset($v['url']);
			unset($v['cate']);
			$v['goods'] = $goods;
		}

		return $shopList;
	}

	/**
     * Comment: 图片上传到远程服务器
     * Author: zzw
     * @param $filename
     * @param bool $auto_delete_local
     * @return array|bool
     */
	static public function file_remote_upload($filename, $auto_delete_local = true)
	{
		global $_W;

		if (empty($_W['setting']['remote']['type'])) {
			return false;
		}

		if ($_W['setting']['remote']['type'] == '1') {
			load()->library('ftp');
			$ftp_config = array('hostname' => $_W['setting']['remote']['ftp']['host'], 'username' => $_W['setting']['remote']['ftp']['username'], 'password' => $_W['setting']['remote']['ftp']['password'], 'port' => $_W['setting']['remote']['ftp']['port'], 'ssl' => $_W['setting']['remote']['ftp']['ssl'], 'passive' => $_W['setting']['remote']['ftp']['pasv'], 'timeout' => $_W['setting']['remote']['ftp']['timeout'], 'rootdir' => $_W['setting']['remote']['ftp']['dir']);
			$ftp = new Ftp($ftp_config);

			if (true === $ftp->connect()) {
				$response = $ftp->upload(ATTACHMENT_ROOT . '/' . $filename, $filename);

				if ($auto_delete_local) {
					self::file_delete($filename);
				}

				if (!empty($response)) {
					return true;
				}

				return '远程附件上传失败，请检查配置并重新上传';
			}

			return '远程附件上传失败，请检查配置并重新上传';
		}

		if ($_W['setting']['remote']['type'] == '2') {
			load()->library('oss');
			load()->model('attachment');
			$buckets = attachment_alioss_buctkets($_W['setting']['remote']['alioss']['key'], $_W['setting']['remote']['alioss']['secret']);
			$endpoint = 'http://' . $buckets[$_W['setting']['remote']['alioss']['bucket']]['location'] . '.aliyuncs.com';

			try {
				$ossClient = new \OSS\OssClient($_W['setting']['remote']['alioss']['key'], $_W['setting']['remote']['alioss']['secret'], $endpoint);
				$ossClient->uploadFile($_W['setting']['remote']['alioss']['bucket'], $filename, ATTACHMENT_ROOT . $filename);
			}
			catch (\OSS\Core\OssException $e) {
				return $e->getMessage();
			}

			if ($auto_delete_local) {
				self::file_delete($filename);
			}
		}
		else {
			if ($_W['setting']['remote']['type'] == '3') {
				load()->library('qiniu');
				$auth = new \Qiniu\Auth($_W['setting']['remote']['qiniu']['accesskey'], $_W['setting']['remote']['qiniu']['secretkey']);
				$config = new \Qiniu\Config();
				$uploadmgr = new \Qiniu\Storage\UploadManager($config);
				$putpolicy = \Qiniu\base64_urlSafeEncode(json_encode(array('scope' => $_W['setting']['remote']['qiniu']['bucket'] . ':' . $filename)));
				$uploadtoken = $auth->uploadToken($_W['setting']['remote']['qiniu']['bucket'], $filename, 3600, $putpolicy);
				list($ret, $err) = $uploadmgr->putFile($uploadtoken, $filename, ATTACHMENT_ROOT . '/' . $filename);

				if ($auto_delete_local) {
					self::file_delete($filename);
				}

				if ($err !== NULL) {
					return '远程附件上传失败，请检查配置并重新上传';
				}

				return true;
			}

			if ($_W['setting']['remote']['type'] == '4') {
				if (!empty($_W['setting']['remote']['cos']['local'])) {
					load()->library('cos');
					\qcloudcos\Cosapi::setRegion($_W['setting']['remote']['cos']['local']);
					$uploadRet = \qcloudcos\Cosapi::upload($_W['setting']['remote']['cos']['bucket'], ATTACHMENT_ROOT . $filename, '/' . $filename, '', 3 * 1024 * 1024, 0);
				}
				else {
					load()->library('cosv3');
					$uploadRet = \Qcloud_cos\Cosapi::upload($_W['setting']['remote']['cos']['bucket'], ATTACHMENT_ROOT . $filename, '/' . $filename, '', 3 * 1024 * 1024, 0);
				}

				if ($uploadRet['code'] != 0) {
					switch ($uploadRet['code']) {
					case -62:
						$message = '输入的appid有误';
						break;

					case -79:
						$message = '输入的SecretID有误';
						break;

					case -97:
						$message = '输入的SecretKEY有误';
						break;

					case -166:
						$message = '输入的bucket有误';
						break;
					}

					return $message;
				}

				if ($auto_delete_local) {
					self::file_delete($filename);
				}
			}
		}
	}

	/**
     * Comment: 图片上传后删除本地图片
     * Author: zzw
     * @param $file
     * @return bool
     */
	private function file_delete($file)
	{
		if (empty($file)) {
			return false;
		}

		if (file_exists($file)) {
			@unlink($file);
		}

		if (file_exists(ATTACHMENT_ROOT . '/' . $file)) {
			@unlink(ATTACHMENT_ROOT . '/' . $file);
		}

		return true;
	}

	/**
     * Comment: 获取已购买当前商品的用户信息   已参与的人数
     * Author: zzw
     * @param $state    状态：代表商品类型  1=抢购商品   2=团购商品  3=拼团商品  5=优惠卷
     * @param $id       商品id
     * @return mixed
     */
	static public function PurchaseUser($state, $id)
	{
		global $_W;
		global $_GPC;
		$limit = 5;
		$where = ' a.uniacid = ' . $_W['uniacid'] . ' AND a.aid = ' . $_W['aid'];
		$table = 'order';

		switch ($state) {
		case 1:
			$table = 'rush_order';
			$where .= ' AND `activityid` = ' . $id . ' ';
			break;

		case 2:
			$where .= ' AND a.plugin = \'groupon\' AND a.fkid = ' . $id . ' ';
			break;

		case 3:
			$where .= ' AND a.plugin = \'wlfightgroup\' AND a.fkid = ' . $id . ' ';
			break;

		case 5:
			$where .= ' AND a.plugin = \'coupon\' AND a.fkid = ' . $id . ' ';
			break;

		case 7:
			$where .= ' AND a.plugin = \'bargain\' AND a.fkid = ' . $id . ' ';
			break;
		}

		$info = pdo_fetchall('SELECT b.id,b.nickname,b.avatar FROM ' . tablename(PDO_NAME . $table) . ' a LEFT JOIN ' . tablename(PDO_NAME . 'member') . (' b ON a.mid = b.id WHERE ' . $where . ' AND b.nickname <> \'\' GROUP BY a.mid ORDER BY a.id DESC  LIMIT ' . $limit . ' '));
		$count = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . $table) . ' a LEFT JOIN ' . tablename(PDO_NAME . 'member') . (' b ON a.mid = b.id WHERE ' . $where . ' AND b.nickname <> \'\' GROUP BY a.mid '));
		$data['info'] = $info;
		$data['count'] = count($count);
		return $data;
	}

	/**
     * Comment: 获取某个商品的信息
     * Author: zzw
     * @param $id       商品id
     * @param $plugin   商品类型
     * @return bool
     */
	static public function getGoods($id, $plugin)
	{
		$where = ' WHERE id = ' . $id . ' ';

		switch ($plugin) {
		case 'coupon':
			$goods = pdo_fetch('SELECT title as name,logo FROM ' . tablename(PDO_NAME . 'couponlist') . $where);
			$goods['goods_class'] = '优惠卷';
			break;

		case 'wlfightgroup':
			$goods = pdo_fetch('SELECT `name`,logo,oldprice FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . $where);
			$goods['goods_class'] = '拼团';
			break;

		case 'groupon':
			$goods = pdo_fetch('SELECT `name`,thumb as logo,oldprice FROM ' . tablename(PDO_NAME . 'groupon_activity') . $where);
			$goods['goods_class'] = '团购';
			break;

		case 'activity':
			$goods = pdo_fetch('SELECT title as `name`,thumb as logo FROM ' . tablename(PDO_NAME . 'activitylist') . $where);
			$goods['goods_class'] = '活动';
			break;

		case 'rush':
			$goods = pdo_fetch('SELECT `name`,thumb as logo,oldprice FROM ' . tablename(PDO_NAME . 'rush_activity') . $where);
			$goods['goods_class'] = '抢购';
			break;
		}

		return $goods;
	}

	/**
     * Comment: 获取当前商品的所有规格信息
     * Author: zzw
     * @param $id     商品id
     * @param $type   商品类型
     * @return array|string
     */
	static public function getSpec($id, $type, $vip)
	{
		global $_W;

		switch ($type) {
		case 1:
			$type = 1;
			break;

		case 2:
			$type = 3;
			break;

		case 3:
			$type = 2;
			break;

		default:
			return '没有规格信息';
		}

		if ($vip) {
			$field = 'vipprice as price';
		}
		else {
			$field = 'price';
		}

		$spec = pdo_getall(PDO_NAME . 'goods_spec', array('goodsid' => $id, 'type' => $type), array('id', 'title', 'content'));

		if ($spec) {
			foreach ($spec as $k => &$v) {
				$content = unserialize($v['content']);
				unset($v['content']);

				foreach ($content as $key => $val) {
					$v['content'][$key] = pdo_get(PDO_NAME . 'goods_spec_item', array('id' => $val, 'show' => 1), array('id', 'title'));
				}
			}
		}

		$type = pdo_fetchall('SELECT id,title,specs,stock as stk,' . $field . ' FROM ' . tablename(PDO_NAME . 'goods_option') . (' WHERE goodsid = ' . $id . ' AND `type` = ' . $type . ' AND uniacid = ' . $_W['uniacid'] . ' '));
		$data['spec'] = $spec;
		$data['type'] = $type;
		return $data;
	}

	/**
     * Comment: 长链接地址转短连接
     * Author: zzw
     * @param $url    长链接地址
     * @return mixed  转换后的短链接地址
     */
	static public function getShortConnection($url)
	{
		$result = Util::long2short($url);

		if (!is_error($result)) {
			$url = $result['short_url'];
		}

		return $url;
	}

	/**
     * Comment: 小程序首页商品信息查询
     * Author: zzw
     * @param $plugin
     * @param $id
     * @return bool
     */
	static public function getHomeGoods($plugin, $id)
	{
		global $_W;

		switch ($plugin) {
		case 1:
			$goods = pdo_fetch('SELECT a.status,a.id,a.thumb as logo,a.name as goods_name,a.price,a.oldprice,a.num as totalnum,b.storename,b.id as sid,a.starttime,a.endtime,a.vipprice,a.vipstatus,a.allsalenum,b.address FROM ' . tablename(PDO_NAME . 'rush_activity') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.sid = b.id WHERE a.id = ' . $id . ' '));
			$goods['plugin'] = 'rush';
			break;

		case 2:
			$goods = pdo_fetch('SELECT b.address,a.status,a.id,a.thumb as logo,a.name as goods_name,a.price,a.oldprice,a.num as totalnum,b.storename,b.id as sid,a.starttime,a.endtime,a.vipprice,a.vipstatus,a.falsesalenum as allsalenum FROM ' . tablename(PDO_NAME . 'groupon_activity') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.sid = b.id WHERE a.id = ' . $id . ' '));
			$goods['plugin'] = $pluginType = 'groupon';
			break;

		case 3:
			$goods = pdo_fetch('SELECT b.address,a.status,a.id,a.logo,a.name as goods_name,a.price,a.aloneprice as oldprice,stock as totalnum,a.peoplenum,b.storename,b.id as sid,a.vipdiscount as discount_price,a.realsalenum,a.falsesalenum as allsalenum FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.merchantid = b.id WHERE a.id = ' . $id . ' '));
			$goods['plugin'] = $pluginType = 'wlfightgroup';
			break;

		case 4:
			$goods = pdo_fetch('SELECT a.id,a.limit,a.datestatus,a.title as `name`,a.price,a.usetimes,a.usetimes as surplus,b.storename,b.logo,b.id as sid,REPLACE(\'table\',\'table\',\'package\') as `plugin`,a.datestatus,allnum  FROM ' . tablename(PDO_NAME . 'package') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . ('b ON a.merchantid = b.id WHERE a.id = ' . $id . ' '));
			$goods['logo'] = tomedia($goods['logo']);
			$hasUsed = pdo_fetchcolumn('SELECT COUNT(*) as stk FROM ' . tablename(PDO_NAME . 'timecardrecord') . (' WHERE `type` = 2 AND activeid = ' . $id));
			$goods['stk'] = 999;

			if (0 < $goods['allnum']) {
				$goods['stk'] = $goods['allnum'] - $hasUsed;
			}

			if ($_W['mid']) {
				$where = ' WHERE `type` = 2 AND activeid = ' . $id . ' AND mid = ' . $_W['mid'] . ' ';

				switch ($goods['datestatus']) {
				case 2:
					$where .= ' AND usetime >= ' . strtotime('-1 week');
					break;

				case 3:
					$where .= ' AND usetime >= ' . strtotime('-1 month');
					break;

				case 4:
					$where .= ' AND usetime >= ' . strtotime('-1 year');
					break;
				}

				$surplus = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(PDO_NAME . 'timecardrecord') . $where);
				$goods['surplus'] = $goods['usetimes'] - $surplus;
				$goods['card'] = self::VipVerification($_W['mid'])['id'];
			}

			unset($goods['allnum']);
			return $goods;
		case 5:
			$goods = pdo_fetch('SELECT a.status,a.id,a.logo,a.title as goods_name,a.price,a.vipstatus,a.vipprice,quantity as totalnum,b.storename,b.id as sid,a.surplus FROM ' . tablename(PDO_NAME . 'couponlist') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.merchantid = b.id WHERE a.id = ' . $id . ' '));
			$goods['plugin'] = $pluginType = 'coupon';
			break;

		case 6:
			$goods = pdo_fetch('SELECT a.id,a.title as `name`,a.limit,a.datestatus,a.week,a.day,a.activediscount,a.discount,a.daily,b.id as sid,b.storename,b.logo FROM ' . tablename(PDO_NAME . 'halfcardlist') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.merchantid = b.id WHERE a.id = ' . $id . ' '));
			$goods['plugin'] = $pluginType = 'halfcard';
			$goods['logo'] = tomedia($goods['logo']);
			$weekflag = date('w', time());
			$dayflag2 = date('j', time());

			switch ($goods['datestatus']) {
			case 1:
				$goods['week'] = unserialize($goods['week']);

				if (in_array($weekflag, $goods['week'])) {
					$goods['discount'] = $goods['activediscount'];
				}
				else {
					if ($goods['daily'] != 1) {
						$goods['discount'] = '暂不可用';
					}
				}

				break;

			case 2:
				$goods['day'] = unserialize($goods['day']);

				if (in_array($dayflag2, $goods['day'])) {
					$goods['discount'] = $goods['activediscount'];
				}
				else {
					if ($goods['daily'] != 1) {
						$goods['discount'] = '暂不可用';
					}
				}

				break;

			case 3:
				if ($goods['daily'] != 1) {
					$goods['discount'] = '暂不可用';
				}

				break;
			}

			if ($_W['mid']) {
				$goods['card'] = self::VipVerification($_W['mid'])['id'];
			}

			unset($goods['week']);
			unset($goods['day']);
			unset($goods['activediscount']);
			unset($goods['daily']);
			unset($goods['datestatus']);
			return $goods;
		case 7:
			$goods = pdo_fetch('SELECT b.address,a.status,a.id,a.thumb as logo,a.name as goods_name,stock as totalnum,b.storename,b.id as sid,a.starttime,a.endtime,a.vipprice,a.vipstatus,a.oldprice,a.price FROM ' . tablename(PDO_NAME . 'bargain_activity') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . (' b ON a.sid = b.id WHERE a.id = ' . $id . ' '));
			$goods['plugin'] = 'bargain';
			break;
		}

		$goods['logo'] = tomedia($goods['logo']);

		if ($plugin == 1) {
			$stopBuyNum = implode(pdo_fetch('SELECT sum(num) FROM ' . tablename(PDO_NAME . 'rush_order') . (' WHERE activityid = ' . $goods['id'] . ' AND uniacid = ' . $_W['uniacid'] . ' AND status IN (0,1,2,3,6,9,4,8) AND aid = ' . $_W['aid'])));
		}
		else if ($plugin == 5) {
			$stopBuyNum = $goods['surplus'];
		}
		else if ($plugin == 3) {
			$stopBuyNum = $goods['realsalenum'];
		}
		else if ($plugin == 7) {
			$stopBuyNum = pdo_fetchcolumn('SELECT count(id) FROM ' . tablename('wlmerchant_order') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND  fkid = ' . $id . ' AND plugin = \'bargain\' AND status IN (1,2,3,4,8,6,7,9) '));
		}
		else {
			$stopBuyNum = implode(pdo_fetch('SELECT sum(num) FROM ' . tablename(PDO_NAME . 'order') . (' WHERE  fkid = ' . $goods['id'] . ' AND plugin = \'' . $pluginType . '\'  AND uniacid = ' . $_W['uniacid'] . ' AND status IN (0,1,2,3,6,7,9,4,8) AND aid = ' . $_W['aid'])));
		}

		if ($goods['allsalenum']) {
			$stopBuyNum = $stopBuyNum + $goods['allsalenum'];
			$goods['totalnum'] = $goods['totalnum'] + $goods['allsalenum'];
		}

		$purchaseUser = WeChat::PurchaseUser($plugin, $goods['id']);
		$goods['user_list'] = array_column($purchaseUser['info'], 'avatar');
		$goods['user_num'] = $purchaseUser['count'];
		$goods['buy_num'] = $stopBuyNum ? $stopBuyNum : 0;
		$goods['buy_percentage'] = sprintf('%.2f', $goods['buy_num'] / $goods['totalnum'] * 100);
		$goods['stk'] = $goods['totalnum'] - $stopBuyNum;
		return $goods;
	}

	/**
     * Comment: 小程序首页获取头条信息
     * Author: zzw
     * @param $id
     * @return bool
     */
	static public function getHomeLine($id)
	{
		$line = pdo_fetch('SELECT id,title,summary,display_img,author,author_img,browse,one_id,two_id FROM ' . tablename(PDO_NAME . 'headline_content') . (' WHERE id = ' . $id . ' '));
		$line['display_img'] = tomedia($line['display_img']);
		$line['author_img'] = tomedia($line['author_img']);
		$line['one_name'] = implode(pdo_get(PDO_NAME . 'headline_class', array('id' => $line['one_id']), array('name')));
		$line['two_name'] = implode(pdo_get(PDO_NAME . 'headline_class', array('id' => $line['two_id']), array('name')));
		unset($line['one_id']);
		unset($line['two_id']);
		return $line;
	}

	/**
     * Comment: 搜索内容 店铺，头条，商品(抢购商品，团购商品，拼团商品)
     * Author: zzw
     * @param $page    页数
     * @param $search  搜索内容
     * @param $lng     经度
     * @param $lat    纬度
     * @return mixed  搜索结果
     */
	static public function getSearch($page, $search, $lng, $lat)
	{
		global $_W;
		$pageNum = 10;
		$startLine = $page * $pageNum - $pageNum;
		$where = 'uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND (`name` LIKE \'%' . $search . '%\' OR price LIKE \'%' . $search . '%\')';
		$goodsList = pdo_fetchall('SELECT id,price,thumb as logo,`name`,oldprice,sid,levelnum, "peoplenum",REPLACE("type",\'type\',\'rush\') as \'type\',REPLACE("group",\'group\',\'抢购\') as \'group\'  FROM ' . tablename(PDO_NAME . 'rush_activity') . (' WHERE ' . $where . ' AND status IN (1,2) ') . ' UNION ALL SELECT id,price,thumb as logo,`name`,oldprice,sid,levelnum,  "peoplenum","groupon","团购" FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE ' . $where . ' AND status IN (1,2) ') . ' UNION ALL SELECT id,price,logo,`name`,aloneprice as oldprice,merchantid as sid,stock as levelnum,peoplenum, "fightgroup","拼团" FROM ' . tablename(PDO_NAME . 'fightgroup_goods') . (' WHERE ' . $where . ' AND status = 1 ') . ' UNION ALL SELECT id,price,logo,`name`,"oldprice",merchantid as sid,quantity as levelnum,"peoplenum", "coupon","卡券" FROM ' . tablename(PDO_NAME . 'couponlist') . (' WHERE ' . $where . ' AND status = 1 ') . ' UNION ALL SELECT id,price,thumb as logo,`name`,oldprice,sid,stock as levelnum,  "peoplenum","bargain","砍价" FROM ' . tablename(PDO_NAME . 'bargain_activity') . (' WHERE ' . $where . ' AND status IN (1,2) '));
		$data['count']['goodsList'] = count($goodsList);
		$goodsList = array_slice($goodsList, $startLine, $pageNum);

		foreach ($goodsList as $k => &$v) {
			$v['logo'] = tomedia($v['logo']);
			$shopInfo = pdo_get(PDO_NAME . 'merchantdata', array('id' => $v['sid']), array('storename', 'location'));
			$location = unserialize($shopInfo['location']);
			$distance = Store::getdistance($location['lng'], $location['lat'], $lng, $lat);

			if ($distance) {
				if (1000 < $distance) {
					$distance = floor($distance / 1000 * 10) / 10 . 'km';
				}
				else {
					$distance = round($distance) . 'm';
				}
			}

			$v['store_name'] = $shopInfo['storename'];
			$v['distance'] = $distance;
			$buyW = ' uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' ';

			if ($v['type'] == 'rush') {
				$buyNum = pdo_fetch('SELECT count(*) as num FROM ' . tablename(PDO_NAME . 'rush_order') . (' WHERE ' . $buyW . ' AND activityid = ' . $v['id'] . ' AND status IN (1,2,3,4,5,6)'));
			}
			else if ($v['type'] == 'groupon') {
				$buyNum = pdo_fetch('SELECT count(*) as num FROM ' . tablename(PDO_NAME . 'order') . (' WHERE ' . $buyW . ' AND plugin = \'groupon\' AND fkid = ' . $v['id'] . ' AND status IN (1,2,3,4,5,6)'));
			}
			else if ($v['type'] == 'fightgroup') {
				$buyNum = pdo_fetch('SELECT count(*) as num FROM ' . tablename(PDO_NAME . 'order') . (' WHERE ' . $buyW . ' AND plugin = \'wlfightgroup\' AND fkid = ' . $v['id'] . ' AND status IN (1,2,3,4,5,6)'));
			}
			else if ($v['type'] == 'coupon') {
				$buyNum = pdo_fetch('SELECT count(*) as num FROM ' . tablename(PDO_NAME . 'order') . (' WHERE ' . $buyW . ' AND plugin = \'coupon\' AND fkid = ' . $v['id'] . ' AND status IN (1,2,3,4,5,6)'));
			}
			else {
				if ($v['type'] == 'bargain') {
					$buyNum = pdo_fetch('SELECT count(*) as num FROM ' . tablename(PDO_NAME . 'order') . (' WHERE ' . $buyW . ' AND plugin = \'bargain\' AND fkid = ' . $v['id'] . ' AND status IN (1,2,3,4,5,6)'));
					$v['is_charge'] = pdo_getcolumn(PDO_NAME . 'couponlist', array('id' => $v['id']), 'is_charge');
				}
			}

			$v['buyNum'] = 0;

			if ($buyNum) {
				$v['buyNum'] = intval(implode($buyNum));
			}

			unset($v['sid']);
		}

		$headlineList = pdo_fetchall('SELECT id,display_img,title,browse FROM ' . tablename(PDO_NAME . 'headline_content') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND title LIKE \'%' . $search . '%\' ORDER BY release_time DESC'));
		$data['count']['headlineList'] = count($headlineList);
		$headlineList = array_slice($headlineList, $startLine, $pageNum);

		foreach ($headlineList as $k => &$v) {
			$v['display_img'] = tomedia($v['display_img']);
		}

		$shopList = pdo_fetchall('SELECT id,storename,logo,address,location,storehours,pv,score FROM ' . tablename(PDO_NAME . 'merchantdata') . (' WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND status = 2 AND enabled = 1 AND storename LIKE \'%' . $search . '%\''));
		$shopList = Store::getstores($shopList, $lng, $lat, 4);
		$shopList = self::getStoreList($shopList);
		$data['count']['shopList'] = count($shopList);
		$shopList = array_slice($shopList, $startLine, $pageNum);
		$data['goodsList'] = $goodsList;
		$data['headlineList'] = $headlineList;
		$data['shopList'] = $shopList;
		return $data;
	}

	/**
     * Comment: 获取某个状态的全部订单数量
     * Author: zzw
     * @param $mid      用户id
     * @param $status   订单状态 ''全部 0未支付/代付款 1已支付/待使用 2已消费/待评价 3已完成 4待收货/待消费 5已取消 6待退款 7已退款  8待发货  9已过期
     * @return int
     */
	static public function getOrderNum($mid, $status)
	{
		global $_W;
		$where = ' uniacid = ' . $_W['uniacid'] . ' and mid = ' . $mid . ' AND aid = ' . $_W['aid'] . ' ';
		$orderType = '\'coupon\',\'wlfightgroup\',\'groupon\',\'activity\'';

		if (is_numeric($status)) {
			$where .= ' AND status = {intval(' . $status . ')}';
		}

		$order = pdo_fetchall('SELECT id FROM ' . tablename(PDO_NAME . 'order') . (' WHERE ' . $where . ' AND orderno != 666666 AND plugin IN (' . $orderType . ')') . ' UNION ALL SELECT id FROM ' . tablename(PDO_NAME . 'rush_order') . (' WHERE ' . $where . ' '));
		return count($order);
	}

	/**
     * Comment: 根据信息获取页面信息，菜单信息，广告信息
     * Author: zzw
     * @param $pageInfo
     * @param $menuid
     * @param $advid
     * @return mixed
     */
	static public function getPageInfo($pageInfo, $menuid, $advid)
	{
		global $_W;
		global $_GPC;
		$page['title'] = $pageInfo['title'];
		$page['background'] = $pageInfo['background'];
		$page['share_title'] = $pageInfo['share_title'];
		$page['share_image'] = tomedia($pageInfo['share_image']);

		if (0 < $menuid) {
			$menudata = Diy::getMenu($menuid)['data'];
		}

		if (0 < $advid) {
			$advdata = Diy::BeOverdue($advid, false)['data'];
		}

		$data['page'] = $page;
		$data['menu'] = $menudata;
		$data['adv'] = $advdata;
		return $data;
	}

	/**
     * Comment: 验证码发送组一
     * Author: zzw
     * @param $code     验证码
     * @param $mobile   手机号
     * @return mixed
     */
	static public function smsSF($code, $mobile, $mid)
	{
		global $_W;
		$smsset = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'smsset'), 'value'));
		$baseset = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'base'), 'value'));
		$smses = pdo_get(PDO_NAME . 'smstpl', array('uniacid' => $_W['uniacid'], 'id' => $smsset['dy_sf']));
		$param = unserialize($smses['data']);
		$datas = array(
			array('name' => '系统名称', 'value' => $baseset['name']),
			array('name' => '版权信息', 'value' => $baseset['copyright']),
			array('name' => '验证码', 'value' => $code)
		);

		foreach ($param as $d) {
			$params[$d['data_temp']] = self::replaceTemplate($d['data_shop'], $datas);
		}

		return self::sendSms($smses, $params, $mobile);
	}

	/**
     * Comment: 验证码发送组二
     * Author: zzw
     * @param $str
     * @param array $datas
     * @return mixed
     */
	static public function replaceTemplate($str, $datas = array())
	{
		foreach ($datas as $d) {
			$str = str_replace('【' . $d['name'] . '】', $d['value'], $str);
		}

		return $str;
	}

	/**
     * Comment: 验证码发送组三
     * Author: zzw
     * @param $smstpl
     * @param $param
     * @param $mobile
     * @return array
     */
	static public function sendSms($smstpl, $param, $mobile, $mid)
	{
		global $_W;
		$smsset = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'sms'), 'value'));

		if ($smstpl['type'] == 'aliyun') {
			include PATH_CORE . 'library/aliyun/Config.php';
			$profile = DefaultProfile::getProfile('cn-hangzhou', $smsset['note_appkey'], $smsset['note_secretKey']);
			DefaultProfile::addEndpoint('cn-hangzhou', 'cn-hangzhou', 'Dysmsapi', 'dysmsapi.aliyuncs.com');
			$acsClient = new DefaultAcsClient($profile);
			m('aliyun/sendsmsrequest')->setSignName($smsset['note_sign']);
			m('aliyun/sendsmsrequest')->setTemplateParam(json_encode($param));
			m('aliyun/sendsmsrequest')->setTemplateCode($smstpl['smstplid']);
			m('aliyun/sendsmsrequest')->setPhoneNumbers($mobile);
			$resp = $acsClient->getAcsResponse(m('aliyun/sendsmsrequest'));
			$res = Util::object_array($resp);

			if ($res['Code'] == 'OK') {
				self::create_apirecord(-1, '', $mid, $mobile, 1, '阿里云身份验证');
				$recode = array('result' => 1);
			}
			else {
				$recode = array('result' => 2, 'msg' => $res['Message']);
			}
		}
		else {
			m('alidayu/topclient')->appkey = $smsset['note_appkey'];
			m('alidayu/topclient')->secretKey = $smsset['note_secretKey'];
			m('alidayu/smsnum')->setSmsType('normal');
			m('alidayu/smsnum')->setSmsFreeSignName($smsset['note_sign']);
			m('alidayu/smsnum')->setSmsParam(json_encode($param));
			m('alidayu/smsnum')->setRecNum($mobile);
			m('alidayu/smsnum')->setSmsTemplateCode($smstpl['smstplid']);
			$resp = m('alidayu/topclient')->execute(m('alidayu/smsnum'), '6100e23657fb0b2d0c78568e55a3031134be9a3a5d4b3a365753805');
			$res = Util::object_array($resp);

			if ($res['result']['success'] == 1) {
				self::create_apirecord(-1, '', $mid, $mobile, 1, '阿里大于身份验证');
				$recode = array('result' => 1);
			}
			else {
				$recode = array('result' => 2, 'msg' => $res['sub_msg']);
			}
		}

		return $recode;
	}

	/**
     * Comment: 验证码发送组四
     * Author: zzw
     * @param $sendmid
     * @param string $sendmobile
     * @param $takemid
     * @param $takemobile
     * @param $type
     * @param $remark
     */
	static public function create_apirecord($sendmid, $sendmobile = '', $takemid, $takemobile, $type, $remark)
	{
		global $_W;
		$data = array('uniacid' => $_W['uniacid'], 'sendmid' => $sendmid, 'sendmobile' => $sendmobile, 'takemid' => $takemid, 'takemobile' => $takemobile, 'type' => $type, 'remark' => $remark, 'createtime' => time());
		pdo_insert(PDO_NAME . 'apirecord', $data);
	}

	/**
     * Comment: 获取用户登录令牌  这里mid必须使用传递的mid
     * Author: zzw
     * @param $mid   用户id
     * @return string  返回令牌信息
     */
	public function SecurityVerification($mid)
	{
		$time = date('Y-m', time());
		$random = random(32);
		$tokey = strtoupper(MD5(sha1($time . $random . $mid)));
		$token = preg_replace('/\\d+/', '', $tokey) . '&' . $random;
		pdo_update(PDO_NAME . 'member', array('tokey' => $tokey), array('id' => $mid));
		return base64_encode($token);
	}

	/**
     * Comment: 登录判断 & 密钥判断    这里mid必须使用传递的mid
     * Author: zzw
     */
	static public function loginJudge($mid, $token)
	{
		if (!$mid) {
			return array('erron' => 1, 'message' => '请登录后在进行操作');
		}

		if (!$token) {
			return array('erron' => 1, 'message' => '请重新登录');
		}

		$token = explode('&', base64_decode($token));
		$time = date('Y-m', time());
		$shortTokey = $token[0];
		$random = $token[1];
		$tokey = strtoupper(MD5(sha1($time . $random . $mid)));
		$userTokey = pdo_getcolumn(PDO_NAME . 'member', array('id' => $mid), 'tokey');
		$usershortTokey = preg_replace('/\\d+/', '', $userTokey);
		if ($tokey == $userTokey && $shortTokey == $usershortTokey) {
			return array('erron' => 0);
		}

		return array('erron' => 1, 'message' => '请重新登录');
	}

	/**
     * Comment: 小程序客服设置  ——  请求验证
     * Author: zzw
     */
	public function doPagePleaseVerification($info)
	{
		$signature = $info['signature'];
		$timestamp = $info['timestamp'];
		$nonce = $info['nonce'];
		$echostr = $info['echostr'];
		if ($signature && $timestamp && $nonce && $echostr) {
			$token = 'Q1pL5InL8o1Vnpw1NmJvpiLP7L1N1d21';
			$tmpArr = array($token, $timestamp, $nonce);
			sort($tmpArr, SORT_STRING);
			$tmpStr = implode($tmpArr);
			$tmpStr = sha1($tmpStr);

			if ($tmpStr == $signature) {
				return $echostr;
			}

			return false;
		}

		return false;
	}

	/**
     * Comment: 小程序客服设置  ——  获取access_token
     * Author: zzw
     */
	public function doPageGetAccessToken()
	{
		$token = cache_load('asd_token');

		if (!$token) {
			$set = Setting::wlsetting_read('city_selection_set');
			$appid = trim($set['appid']);
			$secret = trim($set['secret']);
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
			$token = json_decode(file_get_contents($url), true)['access_token'];
			$time = 3600 * 2 - 300;
			cache_write('asd_token', $token, $time);
			return $token;
		}

		return $token;
	}

	/**
     * Comment: 小程序客服设置  ——  进行转码编译
     * Author: zzw
     * @param $array
     * @return mixed|string
     */
	public function ijson_encode($array)
	{
		if (version_compare(PHP_VERSION, '5.4.0', '<')) {
			$str = json_encode($array);
			$str = preg_replace_callback('#\\\\u([0-9a-f]{4})#i', function($matchs) {
				return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
			}, $str);
			return $str;
		}

		return json_encode($array, JSON_UNESCAPED_UNICODE);
	}
}

defined('IN_IA') || exit('Access Denied');

?>
