<?php
//dezend by http://www.sucaihuo.com/
class WeChatPay
{
	protected $appid;
	protected $openid;
	protected $mch_id;
	protected $key;
	protected $out_trade_no;
	protected $body;
	protected $total_fee;

	static public function pay($mid, $orderNum, $goodDescribe, $fee)
	{
		$pay = new WeChatPay();
		$pay->getCode($mid, $orderNum, $goodDescribe, $fee);
		$return = $pay->weixinapp();
		return $return;
	}

	private function getCode($mid, $orderNum, $goodDescribe, $fee)
	{
		$set = unserialize(pdo_getcolumn(PDO_NAME . 'setting', array('key' => 'city_selection_set'), 'value'));
		$this->appid = $set['appid'];
		$this->openid = pdo_getcolumn(PDO_NAME . 'member', array('id' => $mid), array('wechat_openid'));
		$this->mch_id = $set['mch_id'];
		$this->key = $set['pay_key'];
		$this->out_trade_no = $orderNum;
		$this->body = $goodDescribe;
		$this->total_fee = $fee;
	}

	private function weixinapp()
	{
		$unifiedorder = $this->unifiedorder();
		$parameters = array('appId' => $this->appid, 'timeStamp' => '' . time() . '', 'nonceStr' => $this->createNoncestr(), 'package' => 'prepay_id=' . $unifiedorder['prepay_id'], 'signType' => 'MD5');
		$parameters['paySign'] = $this->getSign($parameters);
		return $parameters;
	}

	private function unifiedorder()
	{
		$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		$parameters = array('appid' => $this->appid, 'mch_id' => $this->mch_id, 'nonce_str' => $this->createNoncestr(), 'body' => $this->body, 'out_trade_no' => $this->out_trade_no, 'total_fee' => $this->total_fee * 100, 'notify_url' => 'http://www.weixin.qq.com/wxpay/pay.php', 'openid' => $this->openid, 'trade_type' => 'JSAPI');
		$parameters['sign'] = $this->getSign($parameters);
		$xmlData = $this->arrayToXml($parameters);
		$return = $this->xmlToArray($this->postXmlCurl($xmlData, $url, 60));
		return $return;
	}

	static private function postXmlCurl($xml, $url, $second = 30)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 40);
		set_time_limit(0);
		$data = curl_exec($ch);

		if ($data) {
			curl_close($ch);
			return $data;
		}

		$error = curl_errno($ch);
		curl_close($ch);
		return 'curl出错，错误码:' . $error;
	}

	private function arrayToXml($arr)
	{
		$xml = '<root>';

		foreach ($arr as $key => $val) {
			if (is_array($val)) {
				$xml .= '<' . $key . '>' . arrayToXml($val) . '</' . $key . '>';
			}
			else {
				$xml .= '<' . $key . '>' . $val . '</' . $key . '>';
			}
		}

		$xml .= '</root>';
		return $xml;
	}

	private function xmlToArray($xml)
	{
		libxml_disable_entity_loader(true);
		$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		$val = json_decode(json_encode($xmlstring), true);
		return $val;
	}

	private function createNoncestr($length = 32)
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

	private function getSign($Obj)
	{
		foreach ($Obj as $k => $v) {
			$Parameters[$k] = $v;
		}

		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		$String = $String . '&key=' . $this->key;
		$String = md5($String);
		$result_ = strtoupper($String);
		return $result_;
	}

	private function formatBizQueryParaMap($paraMap, $urlencode)
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
}

defined('IN_IA') || exit('Access Denied');

?>
