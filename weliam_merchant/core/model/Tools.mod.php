<?php
//dezend by http://www.sucaihuo.com/
class Tools
{
	static public function getPosterTemp()
	{
		$templist = array();
		$i = 1;

		while ($i < 12) {
			$templist[$i] = array('bg' => URL_APP_RESOURCE . '/image/poster/' . 'bg' . $i . '.jpg', 'nail' => URL_APP_RESOURCE . '/image/poster/' . 's_bg' . $i . '.jpg');
			++$i;
		}

		return $templist;
	}

	static public function getRealData($data)
	{
		$data['left'] = intval(str_replace('px', '', $data['left'])) * 2;
		$data['top'] = intval(str_replace('px', '', $data['top'])) * 2;
		$data['width'] = intval(str_replace('px', '', $data['width'])) * 2;
		$data['height'] = intval(str_replace('px', '', $data['height'])) * 2;
		$data['size'] = intval(str_replace('px', '', $data['size'])) * 2;
		$data['src'] = tomedia($data['src']);
		return $data;
	}

	static public function imageRadius($target = false, $circle = false)
	{
		$w = imagesx($target);
		$h = imagesy($target);
		$w = min($w, $h);
		$h = $w;
		$img = imagecreatetruecolor($w, $h);
		imagesavealpha($img, true);
		$bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
		imagefill($img, 0, 0, $bg);
		$radius = $circle ? $w / 2 : 20;
		$r = $radius;
		$x = 0;

		while ($x < $w) {
			$y = 0;

			while ($y < $h) {
				$rgbColor = imagecolorat($target, $x, $y);
				if ($radius <= $x && $x <= $w - $radius || $radius <= $y && $y <= $h - $radius) {
					imagesetpixel($img, $x, $y, $rgbColor);
				}
				else {
					$y_x = $r;
					$y_y = $r;

					if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}

					$y_x = $w - $r;
					$y_y = $r;

					if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}

					$y_x = $r;
					$y_y = $h - $r;

					if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}

					$y_x = $w - $r;
					$y_y = $h - $r;

					if (($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y) <= $r * $r) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
				}

				++$y;
			}

			++$x;
		}

		return $img;
	}

	static public function createImage($imgurl)
	{
		load()->func('communication');
		$resp = ihttp_request($imgurl);
		if ($resp['code'] == 200 && !empty($resp['content'])) {
			return imagecreatefromstring($resp['content']);
		}

		if ($resp['errno'] == 35) {
			$imgurl = str_replace(array('https://'), 'http://', $imgurl);
		}

		$i = 0;

		while ($i < 3) {
			$resp = ihttp_request($imgurl);
			if ($resp['code'] == 200 && !empty($resp['content'])) {
				return imagecreatefromstring($resp['content']);
			}

			++$i;
		}

		return '';
	}

	static public function mergeImage($target, $data, $imgurl)
	{
		$img = self::createImage($imgurl);
		$w = imagesx($img);
		$h = imagesy($img);
		if ($data['border'] == 'radius' || $data['border'] == 'circle') {
			$img = self::imageRadius($img, $data['border'] == 'circle');
		}

		if ($data['position'] == 'cover') {
			$oldheight = $data['height'];
			$data['height'] = $data['width'] * $h / $w;

			if ($oldheight < $data['height']) {
				$data['top'] = $data['top'] - ($data['height'] - $oldheight) / 2;
			}
		}

		imagecopyresized($target, $img, $data['left'], $data['top'], 0, 0, $data['width'], $data['height'], $w, $h);
		imagedestroy($img);
		return $target;
	}

	static public function mergeText($target, $data, $text)
	{
		$font = IA_ROOT . '/addons/' . MODULE_NAME . '/web/resource/fonts/pingfang.ttf';

		if (!is_file($font)) {
			$font = IA_ROOT . '/addons/' . MODULE_NAME . '/web/resource/fonts/msyh.ttf';
		}

		$colors = self::hex2rgb($data['color']);
		$text = self::autowrap($data['size'], 0, $font, $text, $data['width'], $data['line']);

		if ($data['align'] == 'center') {
			$textbox = imagettfbbox($data['size'], 0, $font, $text);
			$textwidth = $textbox[4] - $textbox[6];
			$data['left'] = imagesx($target) / 2 - $textwidth / 2;
		}

		$text = mb_convert_encoding($text, 'html-entities', 'utf-8');
		$color = imagecolorallocate($target, $colors['red'], $colors['green'], $colors['blue']);
		imagettftext($target, $data['size'], 0, $data['left'], $data['top'] + $data['size'], $color, $font, $text);
		return $target;
	}

	static public function autowrap($fontsize, $angle, $fontface, $string, $width, $needhang = 1)
	{
		$content = '';
		$hang = 1;
		$i = 0;

		while ($i < mb_strlen($string, 'UTF8')) {
			$letter[] = mb_substr($string, $i, 1, 'UTF8');
			++$i;
		}

		foreach ($letter as $l) {
			$teststr = $content . ' ' . $l;
			$testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
			if ($width < $testbox[2] && $content !== '') {
				if ($hang < $needhang) {
					$content .= '
';
					++$hang;
				}
				else {
					break;
				}
			}

			$content .= $l;
		}

		return $content;
	}

	static public function hex2rgb($colour)
	{
		if ($colour[0] == '#') {
			$colour = substr($colour, 1);
		}

		if (strlen($colour) == 6) {
			list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
		}
		else if (strlen($colour) == 3) {
			list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
		}
		else {
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);
		return array('red' => $r, 'green' => $g, 'blue' => $b);
	}

	static public function createPoster($poster, $filename = '', $member)
	{
		global $_W;
		$path = IA_ROOT . '/addons/' . MODULE_NAME . '/data/poster/' . $_W['uniacid'] . '/';

		if (!is_dir($path)) {
			load()->func('file');
			mkdirs($path);
		}

		$md5 = $filename ? $filename : md5(json_encode(array('openid' => $member['openid'], 'time' => time())));
		$file = $md5 . '.png';

		if (!is_file($path . $file)) {
			set_time_limit(0);
			@ini_set('memory_limit', '256M');
			$bg = self::createImage(tomedia($poster['bg']));
			$target = imagecreatetruecolor(640, imagesy($bg));
			imagecopy($target, $bg, 0, 0, 0, 0, 640, imagesy($bg));
			imagedestroy($bg);
			$data = json_decode(str_replace('&quot;', '\'', $poster['data']), true);

			foreach ($data as $d) {
				$d = self::getRealData($d);

				if ($d['type'] == 'head') {
					$avatar = preg_replace('/\\/0$/i', '/96', $poster['avatar']);
					$target = self::mergeImage($target, $d, $avatar);
				}
				else if ($d['type'] == 'img') {
					$target = self::mergeImage($target, $d, $d['src']);
				}
				else if ($d['type'] == 'qr') {
					$target = self::mergeImage($target, $d, $poster['qrimg']);
				}
				else {
					if ($d['type'] == 'vip_price' || $d['type'] == 'nickname' || $d['type'] == 'title' || $d['type'] == 'sub_title' || $d['type'] == 'text' || $d['type'] == 'shopTitle' || $d['type'] == 'shopAddress' || $d['type'] == 'shopPhone') {
						$target = self::mergeText($target, $d, $d['type'] == 'text' ? $d['words'] : $poster[$d['type']]);
					}
					else {
						if ($d['type'] == 'thumb' || $d['type'] == 'shopThumb') {
							if ($d['type'] == 'thumb') {
								$thumb = tomedia($poster['thumb']);
							}
							else {
								$thumb = tomedia($poster['shopThumb']);
							}

							$target = self::mergeImage($target, $d, $thumb);
						}
						else {
							if ($d['type'] == 'marketprice' || $d['type'] == 'productprice') {
								$target = self::mergeText($target, $d, is_numeric($poster[$d['type']]) ? '￥' . $poster[$d['type']] : $poster[$d['type']]);
							}
						}
					}
				}
			}

			imagepng($target, $path . $file);
			imagedestroy($target);
		}

		$img = $_W['siteroot'] . 'addons/' . MODULE_NAME . '/data/poster/' . $_W['uniacid'] . '/' . $file;
		return $img;
	}

	static public function clearposter()
	{
		global $_W;
		load()->func('file');
		@rmdirs(IA_ROOT . '/addons/' . MODULE_NAME . '/data/poster/' . $_W['uniacid']);
	}

	static public function clearwxapp()
	{
		global $_W;
		load()->func('file');
		@rmdirs(IA_ROOT . '/addons/' . MODULE_NAME . '/data/wxapp/' . $_W['uniacid']);
	}

	static public function get_head_img($url, $num)
	{
		$imgs_array = array();
		$random_array = array();
		$files = array();

		if ($handle = opendir($url)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != '.' && $file != '..') {
					if (substr($file, -3) == 'gif' || substr($file, -3) == 'jpg') {
						$files[count($files)] = $file;
					}
				}
			}
		}

		closedir($handle);
		$i = 0;

		while ($i < $num) {
			$random = rand(0, count($files) - 1);

			while (in_array($random, $random_array)) {
				$random = rand(0, count($files) - 1);
			}

			$random_array[$i] = $random;
			$imgs_url = $url . '/' . $files[$random];
			$imgs_array[$i] = $imgs_url;
			++$i;
		}

		return $imgs_array;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
