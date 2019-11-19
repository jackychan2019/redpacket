<?php
//dezend by http://www.sucaihuo.com/
class GatherArticle
{
	public function get_caiji($url)
	{
		global $_W;
		load()->func('file');
		load()->func('communication');
		$html = ihttp_request($url, '', array('CURLOPT_REFERER' => 'http://www.qq.com'));
		$html = str_replace('<!--headTrap<body></body><head></head><html></html>-->', '', $html['content']);
		$reg = array(
			'title'    => array('#activity-name', 'text'),
			'content'  => array('#js_content', 'html'),
			'nickname' => array('.profile_nickname', 'text'),
			'video'    => array('.video_iframe', 'data-src', '', function($video) {
			$video = explode('vid=', $video);
			$video = explode('&', $video[1]);
			return $video[0];
		}),
			'logo'     => array(':contains(msg_cdn_url)', 'text', '', function($logo) {
			$logo = explode('var msg_cdn_url = "', $logo);
			$logo = explode('";', $logo[1]);
			$logo = 'web/index.php?c=utility&a=wxcode&do=image&attach=' . $logo[0];
			return $logo;
		}),
			'desc'     => array(':contains(msg_cdn_url)', 'text', '', function($desc) {
			$desc = explode('var msg_desc = "', $desc);
			$desc = explode('";', $desc[1]);
			return $desc[0];
		})
		);
		$rang = 'body';
		$ql = \QL\QueryList::Query($html, $reg, $rang, 'UTF-8');
		$con = $ql->getData();
		$contents = $con[0]['content'];
		preg_match_all('/<\\s*img\\s+[^>]*?src\\s*=\\s*(\'|\\")(.*?)\\1[^>]*?\\/?\\s*>/i', $contents, $match);
		$pic1 = $match[0];
		$img = $match[2];

		foreach ($pic1 as $key => $value) {
			$url = $value;
			$path = $_W['siteroot'] . 'web/index.php?c=utility&a=wxcode&do=image&attach=' . $img[$key];
			$imgarr = getimagesize($path);
			if (300 < $imgarr[0] && 10 < $imgarr[1]) {
				$fileurl = '<img src="' . tomedia($path) . '" width="100%"/>';
			}
			else {
				$fileurl = '<img src="' . tomedia($path) . '" width="' . $imgarr[0] . '" />';
			}

			if (300 < $imgarr[0] && 200 < $imgarr[1]) {
				if ($key < 4) {
					$pic .= tomedia($path) . ',';
				}
			}

			$contents = str_replace($url, $fileurl, $contents);
		}

		preg_match_all('/<\\s*iframe\\s+[^>]*?src\\s*=\\s*(\'|\\")(.*?)\\1[^>]*?\\/?\\s*>/i', $contents, $match);
		$fs = $match[0];
		$fskey = $match[2];

		foreach ($fs as $key => $value) {
			$fileurl = '<iframe border=\'0\' width=\'100%\' height=\'250\' src=\'http://v.qq.com/iframe/player.html?vid=' . $con[0]['video'] . '&tiny=0&auto=0\' allowfullscreen></iframe>';
			$contents = str_replace($value, $fileurl, $contents);
		}

		$pic = rtrim($pic, ',');
		$pic = explode(',', $pic);

		if (count($pic) == 3) {
			$pic = iserializer($pic);
		}
		else {
			$pic = NULL;
		}

		$data = array('title' => $con[0]['title'], 'contents' => $contents, 'desc' => $con[0]['desc'], 'pic' => $pic, 'vid' => $con[0]['video'], 'thumb' => $_W['siteroot'] . $con[0]['logo'], 'nickname' => $con[0]['nickname']);
		return $data;
	}
}

defined('IN_IA') || exit('Access Denied');
require_once PATH_CORE . 'library/querylist/QueryList.class.php';

?>
