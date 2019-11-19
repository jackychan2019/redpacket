<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class UploadFile extends Commons
{
	/**
     * Comment: 开始进行文件上传操作
     * Author: zzw
     * Date: 2019/7/22 17:52
     * @param array  文件信息
     * @param $type  1=普通上传；2=微信端上传
     * @param int   $id
     * @return json
     */
	static public function uploadIndex($file = array(), $type = 1, $id = 0)
	{
		global $_W;
		global $_GPC;

		if ($type == 1) {
			if (0 < count($file)) {
				$path = self::uploadHandle($file);
				self::sRenderError('文件上传成功', $path);
			}
			else {
				self::sRenderError('请上传文件');
			}
		}
		else {
			$path = self::getWeChatImg($id);
			$imgPath['image'] = $path;
			$imgPath['img'] = tomedia($path);
			self::sRenderSuccess('文件上传成功', $imgPath);
		}
	}

	/**
     * Comment: 普通上传文件的信息处理
     * Author: zzw
     * Date: 2019/7/22 18:46
     * @param $file
     * @return array
     */
	static protected function uploadHandle($file)
	{
		foreach ($file as $key => $value) {
			list($type) = explode('/', $value['type']);

			switch ($type) {
			case 'image':
				self::imageJudge($value);
				break;
			}
		}

		$imgPath = array();

		foreach ($file as $index => $item) {
			list($type) = explode('/', $value['type']);

			switch ($type) {
			case 'image':
				$imgPath[$index]['image'] = self::imageUpload($value);
				$imgPath[$index]['img'] = tomedia($imgPath[$index]['image']);
				break;
			}
		}

		return $imgPath;
	}

	/**
     * Comment: 判断图片信息是否合格
     * Author: zzw
     * Date: 2019/7/22 18:29
     */
	static protected function imageJudge($info)
	{
		global $_W;
		$setting = $_W['setting']['upload']['image'];
		$imageType = $setting['extentions'];
		$imageSize = $setting['limit'] * 1024;
		list(, $type) = explode('/', $info['type']);

		if (!in_array(strtolower($type), $imageType)) {
			$typeStr = implode(',', $imageType);
			self::sRenderError('格式错误,只能上传' . $typeStr . '格式的图片');
		}

		if ($imageSize < $info['size']) {
			$size = $imageSize / (1024 * 1024);
			self::sRenderError('图片不能超过' . $size . 'M');
		}
	}

	/**
     * Comment: 图片上传
     * Author: zzw
     * Date: 2019/7/22 18:44
     * @param $info
     * @return string
     */
	static protected function imageUpload($info)
	{
		global $_W;
		$setting = $_W['setting']['upload']['image'];
		$setting['folder'] = 'images/' . MODULE_NAME;
		$storageSet = $_W['setting']['remote']['type'];
		list(, $type) = explode('/', $info['type']);
		$imgSuffix = '.' . strtolower($type);
		$fileName = time() . rand(10000, 99999) . $imgSuffix;
		$pathName = $setting['folder'] . '/' . $fileName;
		$fullName = PATH_ATTACHMENT . $pathName;
		$res = move_uploaded_file($info['tmp_name'], $fullName);

		if ($res) {
			if (0 < $storageSet) {
				$remotestatus = WeChat::file_remote_upload($pathName);

				if ($remotestatus) {
					self::sRenderError('远程附件上传失败，请检查配置并重新上传', $remotestatus);
				}
			}

			return $pathName;
		}

		self::sRenderError('图片上传失败，请重新上传');
	}

	/**
     * Comment: 获取微信上传的图片，储存在并且/上传到图片服务器
     * Author: zzw
     * Date: 2019/7/23 16:46
     * @param $id
     * @return string
     */
	static protected function getWeChatImg($id)
	{
		global $_W;
		$uniacccount = WeAccount::create($_W['acid']);
		$access_token = $uniacccount->fetch_available_token();
		$url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $access_token . '&media_id=' . $id;
		$res = ihttp_get($url);
		$setting = $_W['setting']['upload']['image'];
		$setting['folder'] = 'images/' . MODULE_NAME . '/' . date('Y/m/d', time());

		if (is_error($res)) {
			self::sRenderError('提取文件失败, 错误信息: ' . $res['message']);
		}

		if (intval($res['code']) != 200) {
			self::sRenderError('提取文件失败: 未找到该资源文件');
		}

		$path = PATH_ATTACHMENT . $setting['folder'];

		if (!is_dir($path)) {
			mkdir($path, 511, true);
		}

		if ($setting['limit'] * 1024 < intval($res['headers']['Content-Length'])) {
			self::sRenderError('上传的媒体文件过大,不能大于' . $setting['limit'] . 'KB');
		}

		if ($res['content']) {
			$content = $res['content'];
			$fileName = time() . rand(10000, 99999) . '.png';
			$pathName = $setting['folder'] . '/' . $fileName;
			$fullName = PATH_ATTACHMENT . $pathName;
			$result = file_put_contents($fullName, $content);

			if ($result) {
				$storageSet = $_W['setting']['remote']['type'];

				if (0 < $storageSet) {
					$remotestatus = WeChat::file_remote_upload($pathName);

					if ($remotestatus) {
						self::sRenderError('远程附件上传失败，请检查配置并重新上传', $remotestatus);
					}
				}

				return $pathName;
			}

			self::sRenderError('提取失败，请稍后重试');
		}
		else {
			self::sRenderError('获取失败，请稍后重试');
		}
	}
}

?>
