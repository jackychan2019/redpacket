<?php
//dezend by http://www.sucaihuo.com/
class WeixinPay
{
	public function refund($arr)
	{
		global $_W;
		$setting = uni_setting($_W['uniacid'], array('payment'));
		$data['appid'] = $_W['account']['key'];
		$data['mch_id'] = $setting['payment']['wechat']['mchid'];
		$data['transaction_id'] = $arr['transid'];
		$data['out_refund_no'] = $arr['transid'] . rand(1000, 9999);
		$data['total_fee'] = $arr['totalmoney'] * 100;
		$data['refund_fee'] = $arr['refundmoney'] * 100;
		$data['op_user_id'] = $setting['payment']['wechat']['mchid'];
		$data['nonce_str'] = $this->createNoncestr();
		$data['sign'] = $this->getSign($data);
		if (empty($data['appid']) || empty($data['mch_id'])) {
			$rearr['return_msg'] = '请先在微擎的功能选项-支付参数内设置微信商户号和秘钥';
			return $rearr;
		}

		if ($data['refund_fee'] < $data['total_fee']) {
			$rearr['return_msg'] = '退款金额不能大于实际支付金额';
			return $rearr;
		}

		$xml = $this->arrayToXml($data);
		$url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
		$re = $this->wxHttpsRequestPem($xml, $url);
		$rearr = $this->xmlToArray($re);
		return $rearr;
	}

	public function checkRefund($transid)
	{
		global $_W;
		$setting = uni_setting($_W['uniacid'], array('payment'));
		$data['appid'] = $_W['account']['key'];
		$data['mch_id'] = $setting['payment']['wechat']['mchid'];
		$data['transaction_id'] = $transid;
		$data['nonce_str'] = $this->createNoncestr();
		$data['sign'] = $this->getSign($data);
		if (empty($data['appid']) || empty($data['mch_id'])) {
			$rearr['return_msg'] = '请先在微擎的功能选项-支付参数内设置微信商户号和秘钥';
			return $rearr;
		}

		$xml = $this->arrayToXml($data);
		$url = 'https://api.mch.weixin.qq.com/pay/refundquery';
		$re = $this->wxHttpsRequestPem($xml, $url);
		$rearr = $this->xmlToArray($re);
		return $rearr;
	}

	public function finance($openid = '', $money = 0, $desc = '', $realname, $trade_no)
	{
		global $_W;
		$setting = uni_setting($_W['uniacid'], array('payment'));
		$refund_setting = $setting['payment']['wechat_refund'];

		if ($refund_setting['switch'] != 1) {
			return error(1, '未开启微信退款功能！');
		}

		if (empty($refund_setting['key']) || empty($refund_setting['cert'])) {
			return error(1, '缺少微信证书！');
		}

		$cert = authcode($refund_setting['cert'], 'DECODE');
		$key = authcode($refund_setting['key'], 'DECODE');
		file_put_contents(ATTACHMENT_ROOT . $_W['uniacid'] . '_wechat_refund_all.pem', $cert . $key);
		$data = array();
		$data['mch_appid'] = $_W['account']['key'];
		$data['mchid'] = $setting['payment']['wechat']['mchid'];
		$data['nonce_str'] = $this->createNoncestr();
		$data['partner_trade_no'] = $trade_no;
		$data['openid'] = $openid;

		if (!empty($realname)) {
			$data['re_user_name'] = $realname;
		}

		$data['check_name'] = 'NO_CHECK';
		$data['amount'] = $money * 100;
		$data['desc'] = empty($desc) ? '商家佣金提现' : $desc;
		$data['spbill_create_ip'] = gethostbyname($_SERVER['HTTP_HOST']);
		$data['sign'] = $this->getSign($data);

		if (empty($data['mch_appid'])) {
			$rearr['return_msg'] = '请先在微擎的功能选项-支付参数内设置微信商户号和秘钥';
			return $rearr;
		}

		$xml = $this->arrayToXml($data);
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
		$re = $this->wxHttpsRequestPem($xml, $url);
		$rearr = $this->xmlToArray($re);
		return $rearr;
	}

	public function createNoncestr($length = 32)
	{
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$str = '';
		$i = 0;

		while ($i < $length) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
			++$i;
		}

		return $str;
	}

	public function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = '';
		ksort($paraMap);

		foreach ($paraMap as $k => $v) {
			if ($urlencode) {
				$v = urlencode($v);
			}

			$buff .= $k . '=' . $v . '&';
		}

		if (0 < strlen($buff)) {
			$reqPar = substr($buff, 0, strlen($buff) - 1);
		}

		return $reqPar;
	}

	public function getSign($Obj)
	{
		global $_W;
		$setting = uni_setting($_W['uniacid'], array('payment'));

		foreach ($Obj as $k => $v) {
			$Parameters[$k] = $v;
		}

		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		$String = $String . '&key=' . $setting['payment']['wechat']['apikey'];
		$String = md5($String);
		$result_ = strtoupper($String);
		return $result_;
	}

	public function arrayToXml($arr)
	{
		$xml = '<xml>';

		foreach ($arr as $key => $val) {
			if (is_numeric($val)) {
				$xml .= '<' . $key . '>' . $val . '</' . $key . '>';
			}
			else {
				$xml .= '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
			}
		}

		$xml .= '</xml>';
		return $xml;
	}

	public function xmlToArray($xml)
	{
		$array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $array_data;
	}

	public function wxHttpsRequestPem($vars, $url, $second = 30, $aHeader = array())
	{
		global $_W;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
		curl_setopt($ch, CURLOPT_SSLCERT, ATTACHMENT_ROOT . $_W['uniacid'] . '_wechat_refund_all.pem');

		if (1 <= count($aHeader)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
		}

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		$data = curl_exec($ch);
		unlink(ATTACHMENT_ROOT . $_W['uniacid'] . '_wechat_refund_all.pem');

		if ($data) {
			curl_close($ch);
			return $data;
		}

		$error = curl_errno($ch);
		echo 'call faild, errorCode:' . $error . '
';
		curl_close($ch);
		return false;
	}

	static public function sendingRedPackets($info)
	{
		global $_W;
		$setting = uni_setting($_W['uniacid'], array('payment'));
		$refund_setting = $setting['payment']['wechat_refund'];

		if ($refund_setting['switch'] != 1) {
			return error(1, '未开启微信付款功能！');
		}

		if (empty($refund_setting['key']) || empty($refund_setting['cert'])) {
			return error(1, '缺少微信证书！');
		}

		$cert = authcode($refund_setting['cert'], 'DECODE');
		$key = authcode($refund_setting['key'], 'DECODE');
		file_put_contents(ATTACHMENT_ROOT . $_W['uniacid'] . '_wechat_refund_all.pem', $cert . $key);
		$payment = $setting['payment'];
		$cloud = Cloud::wl_syssetting_read('auth');
		$info = array('nonce_str' => random(32), 'mch_billno' => $info['mch_billno'], 'mch_id' => $payment['wechat']['mchid'], 'wxappid' => $_W['account']['key'], 'send_name' => $_W['wlsetting']['base']['name'], 're_openid' => $info['re_openid'], 'total_amount' => $info['total_amount'], 'total_num' => 1, 'wishing' => '恭喜发财,大吉大利', 'client_ip' => $cloud['ip'], 'act_name' => '红包提现', 'remark' => $info['remark'], 'scene_id' => 'PRODUCT_5');
		ksort($info);
		$sign = '';

		foreach ($info as $k => $v) {
			if (!empty($v)) {
				$sign .= $k . '=' . $v . '&';
			}
		}

		$sign .= 'key=' . $setting['payment']['wechat']['apikey'];
		$info['sign'] = strtoupper(md5($sign));
		$pay = new WeixinPay();
		$xml = $pay->arrayToXml($info);
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
		$re = $pay->wxHttpsRequestPem($xml, $url);
		$rearr = $pay->xmlToArray($re);
		return $rearr;
	}

	static public function getRedPacketsInfo($info)
	{
		global $_W;
		$setting = uni_setting($_W['uniacid'], array('payment'));
		$refund_setting = $setting['payment']['wechat_refund'];

		if ($refund_setting['switch'] != 1) {
			return error(1, '未开启微信付款功能！');
		}

		if (empty($refund_setting['key']) || empty($refund_setting['cert'])) {
			return error(1, '缺少微信证书！');
		}

		$cert = authcode($refund_setting['cert'], 'DECODE');
		$key = authcode($refund_setting['key'], 'DECODE');
		file_put_contents(ATTACHMENT_ROOT . $_W['uniacid'] . '_wechat_refund_all.pem', $cert . $key);
		$payment = $setting['payment'];
		$info = array('nonce_str' => random(32), 'mch_billno' => $info['mch_billno'], 'mch_id' => $payment['wechat']['mchid'], 'appid' => $_W['account']['key'], 'bill_type' => 'MCHT');
		ksort($info);
		$sign = '';

		foreach ($info as $k => $v) {
			if (!empty($v)) {
				$sign .= $k . '=' . $v . '&';
			}
		}

		$sign .= 'key=' . $setting['payment']['wechat']['apikey'];
		$info['sign'] = strtoupper(md5($sign));
		$pay = new WeixinPay();
		$xml = $pay->arrayToXml($info);
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo';
		$re = $pay->wxHttpsRequestPem($xml, $url);
		$rearr = $pay->xmlToArray($re);
		return $rearr;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
