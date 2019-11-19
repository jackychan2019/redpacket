<?php
//dezend by http://www.sucaihuo.com/
namespace QL;

class QueryList
{
	public $data;
	public $html;
	private $pqHtml;
	private $outputEncoding = false;
	private $inputEncoding = false;
	private $htmlEncoding;
	static public $instances;

	public function __construct()
	{
	}

	/**
     * 静态方法，访问入口
     * @param string $page            要抓取的网页URL地址(支持https);或者是html源代码
     * @param array  $rules         【选择器数组】说明：格式array("名称"=>array("选择器","类型"[,"标签过滤列表"][,"回调函数"]),.......[,"callback"=>"全局回调函数"]);
     *                               【选择器】说明:可以为任意的jQuery选择器语法
     *                               【类型】说明：值 "text" ,"html" ,"HTML标签属性" ,
     *                               【标签过滤列表】:可选，要过滤的选择器名，多个用空格隔开,当标签名前面添加减号(-)时（此时标签可以为任意的元素选择器），表示移除该标签以及标签内容，否则当【类型】值为text时表示需要保留的HTML标签，为html时表示要过滤掉的HTML标签
     *                               【回调函数】/【全局回调函数】：可选，字符串（函数名） 或 数组（array("类名","类的静态方法")），回调函数应有俩个参数，第一个参数是选择到的内容，第二个参数是选择器数组下标，回调函数会覆盖全局回调函数
     *
     * @param string $range       【块选择器】：指 先按照规则 选出 几个大块 ，然后再分别再在块里面 进行相关的选择
     * @param string $outputEncoding【输出编码格式】指要以什么编码输出(UTF-8,GB2312,.....)，防止出现乱码,如果设置为 假值 则不改变原字符串编码
     * @param string $inputEncoding 【输入编码格式】明确指定输入的页面编码格式(UTF-8,GB2312,.....)，防止出现乱码,如果设置为 假值 则自动识别
     * @param bool|false $removeHead 【是否移除页面头部区域】 乱码终极解决方案
     * @return mixed
     */
	static public function Query($page, array $rules, $range = '', $outputEncoding = NULL, $inputEncoding = NULL, $removeHead = false)
	{
		return self::getInstance()->_query($page, $rules, $range, $outputEncoding, $inputEncoding, $removeHead);
	}

	/**
     * 运行QueryList扩展
     * @param $class
     * @param array $args
     * @return mixed
     * @throws Exception
     */
	static public function run($class, $args = array())
	{
		$extension = self::getInstance('QL\\Ext\\' . $class);
		return $extension->run($args);
	}

	/**
     * 获取任意实例
     * @return mixed
     * @throws Exception
     */
	static public function getInstance()
	{
		$args = func_get_args();
		count($args) || ($args = array('QL\\QueryList'));
		$key = md5(serialize($args));
		$className = array_shift($args);

		if (!class_exists($className)) {
			throw new \Exception('no class ' . $className);
		}

		if (!isset(self::$instances[$key])) {
			$rc = new \ReflectionClass($className);
			self::$instances[$key] = $rc->newInstanceArgs($args);
		}

		return self::$instances[$key];
	}

	/**
     * 获取目标页面源码(主要用于调试)
     * @param bool|true $rel
     * @return string
     */
	public function getHtml($rel = true)
	{
		return $rel ? $this->qpHtml : $this->html;
	}

	/**
     * 获取采集结果数据
     * @param callback $callback
     * @return array
     */
	public function getData($callback = NULL)
	{
		if (is_callable($callback)) {
			return array_map($callback, $this->data);
		}

		return $this->data;
	}

	/**
     * 重新设置选择器
     * @param $rules
     * @param string $range
     * @param string $outputEncoding
     * @param string $inputEncoding
     * @param bool|false $removeHead
     * @return QueryList
     */
	public function setQuery(array $rules, $range = '', $outputEncoding = NULL, $inputEncoding = NULL, $removeHead = false)
	{
		return $this->_query($this->html, $rules, $range, $outputEncoding, $inputEncoding, $removeHead);
	}

	private function _query($page, array $rules, $range, $outputEncoding, $inputEncoding, $removeHead)
	{
		$this->data = array();
		$this->html = $this->_isURL($page) ? $this->_request($page) : $page;
		$outputEncoding && ($this->outputEncoding = $outputEncoding);
		$inputEncoding && ($this->inputEncoding = $inputEncoding);
		$removeHead && ($this->html = $this->_removeHead($this->html));
		$this->pqHtml = '';

		if (empty($this->html)) {
			trigger_error('The received content is empty!', E_USER_NOTICE);
		}

		$this->htmlEncoding = $this->inputEncoding ? $this->inputEncoding : $this->_getEncode($this->html);
		$this->regArr = $rules;
		$this->regRange = $range;
		$this->_getList();
		return $this;
	}

	private function _getList()
	{
		$this->inputEncoding && (\phpQuery::$defaultCharset = $this->inputEncoding);
		$document = \phpQuery::newDocumentHTML($this->html);
		$this->qpHtml = $document->htmlOuter();

		if (!empty($this->regRange)) {
			$robj = pq($document)->find($this->regRange);
			$i = 0;

			foreach ($robj as $item) {
				while (list($key, $reg_value) = each($this->regArr)) {
					if ($key == 'callback') {
						continue;
					}

					$tags = isset($reg_value[2]) ? $reg_value[2] : '';
					$iobj = pq($item)->find($reg_value[0]);

					switch ($reg_value[1]) {
					case 'text':
						$this->data[$i][$key] = $this->_allowTags(pq($iobj)->html(), $tags);
						break;

					case 'html':
						$this->data[$i][$key] = $this->_stripTags(pq($iobj)->html(), $tags);
						break;

					default:
						$this->data[$i][$key] = pq($iobj)->attr($reg_value[1]);
						break;
					}

					if (isset($reg_value[3])) {
						$this->data[$i][$key] = call_user_func($reg_value[3], $this->data[$i][$key], $key);
					}
					else {
						if (isset($this->regArr['callback'])) {
							$this->data[$i][$key] = call_user_func($this->regArr['callback'], $this->data[$i][$key], $key);
						}
					}
				}

				reset($this->regArr);
				++$i;
			}
		}
		else {
			while (list($key, $reg_value) = each($this->regArr)) {
				if ($key == 'callback') {
					continue;
				}

				$document = \phpQuery::newDocumentHTML($this->html);
				$tags = isset($reg_value[2]) ? $reg_value[2] : '';
				$lobj = pq($document)->find($reg_value[0]);
				$i = 0;

				foreach ($lobj as $item) {
					switch ($reg_value[1]) {
					case 'text':
						$this->data[$i][$key] = $this->_allowTags(pq($item)->html(), $tags);
						break;

					case 'html':
						$this->data[$i][$key] = $this->_stripTags(pq($item)->html(), $tags);
						break;

					default:
						$this->data[$i][$key] = pq($item)->attr($reg_value[1]);
						break;
					}

					if (isset($reg_value[3])) {
						$this->data[$i][$key] = call_user_func($reg_value[3], $this->data[$i][$key], $key);
					}
					else {
						if (isset($this->regArr['callback'])) {
							$this->data[$i][$key] = call_user_func($this->regArr['callback'], $this->data[$i][$key], $key);
						}
					}

					++$i;
				}
			}
		}

		if ($this->outputEncoding) {
			$this->data = $this->_arrayConvertEncoding($this->data, $this->outputEncoding, $this->htmlEncoding);
		}

		\phpQuery::$documents = array();
	}

	/**
     * URL请求
     * @param $url
     * @return string
     */
	private function _request($url)
	{
		if (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_REFERER, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
		}
		else if (0 <= version_compare(PHP_VERSION, '5.0.0')) {
			$opts = array(
				'http' => array('header' => 'Referer:' . $url)
			);
			$result = file_get_contents($url, false, stream_context_create($opts));
		}
		else {
			$result = file_get_contents($url);
		}

		return $result;
	}

	/**
     * 移除页面head区域代码
     * @param $html
     * @return mixed
     */
	private function _removeHead($html)
	{
		return preg_replace('/<head.+?>.+<\\/head>/is', '<head></head>', $html);
	}

	/**
     * 获取文件编码
     * @param $string
     * @return string
     */
	private function _getEncode($string)
	{
		return mb_detect_encoding($string, array('ASCII', 'GB2312', 'GBK', 'UTF-8'));
	}

	/**
     * 转换数组值的编码格式
     * @param  array $arr           
     * @param  string $toEncoding   
     * @param  string $fromEncoding 
     * @return array                
     */
	private function _arrayConvertEncoding($arr, $toEncoding, $fromEncoding)
	{
		return $arr;
	}

	/**
     * 简单的判断一下参数是否为一个URL链接
     * @param  string  $str 
     * @return boolean      
     */
	private function _isURL($str)
	{
		if (preg_match('/^http(s)?:\\/\\/.+/', $str)) {
			return true;
		}

		return false;
	}

	/**
     * 去除特定的html标签
     * @param  string $html 
     * @param  string $tags_str 多个标签名之间用空格隔开
     * @return string       
     */
	private function _stripTags($html, $tags_str)
	{
		$tagsArr = $this->_tag($tags_str);
		$html = $this->_removeTags($html, $tagsArr[1]);
		$p = array();

		foreach ($tagsArr[0] as $tag) {
			$p[] = '/(<(?:\\/' . $tag . '|' . $tag . ')[^>]*>)/i';
		}

		$html = preg_replace($p, '', trim($html));
		return $html;
	}

	/**
     * 保留特定的html标签
     * @param  string $html 
     * @param  string $tags_str 多个标签名之间用空格隔开
     * @return string       
     */
	private function _allowTags($html, $tags_str)
	{
		$tagsArr = $this->_tag($tags_str);
		$html = $this->_removeTags($html, $tagsArr[1]);
		$allow = '';

		foreach ($tagsArr[0] as $tag) {
			$allow .= '<' . $tag . '> ';
		}

		return strip_tags(trim($html), $allow);
	}

	private function _tag($tags_str)
	{
		$tagArr = preg_split('/\\s+/', $tags_str, -1, PREG_SPLIT_NO_EMPTY);
		$tags = array(
			array(),
			array()
		);

		foreach ($tagArr as $tag) {
			if (preg_match('/-(.+)/', $tag, $arr)) {
				array_push($tags[1], $arr[1]);
			}
			else {
				array_push($tags[0], $tag);
			}
		}

		return $tags;
	}

	/**
     * 移除特定的html标签
     * @param  string $html 
     * @param  array  $tags 标签数组    
     * @return string       
     */
	private function _removeTags($html, $tags)
	{
		$tag_str = '';

		if (count($tags)) {
			foreach ($tags as $tag) {
				$tag_str .= $tag_str ? ',' . $tag : $tag;
			}

			\phpQuery::$defaultCharset = $this->inputEncoding ? $this->inputEncoding : $this->htmlEncoding;
			$doc = \phpQuery::newDocumentHTML($html);
			pq($doc)->find($tag_str)->remove();
			$html = pq($doc)->htmlOuter();
			$doc->unloadDocument();
		}

		return $html;
	}
}

require 'phpQuery.php';

?>
