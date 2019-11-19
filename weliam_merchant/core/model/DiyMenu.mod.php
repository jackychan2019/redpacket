<?php
//dezend by http://www.sucaihuo.com/
class DiyMenu
{
	static protected function menuSetInfo($key)
	{
		switch ($key) {
		case 'home':
			return array('imgurl' => '', 'linkurl' => app_url('dashboard/home/index', '', false), 'iconclass' => 'icon-home', 'text' => '首页', 'url_type' => '');
		case 'comment':
			return array('imgurl' => '', 'linkurl' => h5_url('pages/mainPages/headline/index'), 'iconclass' => 'icon-comment', 'text' => '头条', 'url_type' => '', 'page_path' => 'pages/mainPages/headline/index');
		case 'shop':
			return array('imgurl' => '', 'linkurl' => app_url('store/merchant/newindex', '', false), 'iconclass' => 'icon-shop', 'text' => '好店', 'url_type' => '');
		case 'my':
			return array('imgurl' => '', 'linkurl' => app_url('member/user/index', '', false), 'iconclass' => 'icon-my', 'text' => '我的', 'url_type' => '');
		case 'news_light':
			return array('imgurl' => '', 'linkurl' => app_url('halfcard/halfcard_app/userhalfcard', '', false), 'iconclass' => 'icon-news_light', 'text' => '一卡通', 'url_type' => '');
		case 'activity':
			return array('imgurl' => '', 'linkurl' => app_url('store/supervise/information', array('applyflag' => 1), false), 'iconclass' => 'icon-activity', 'text' => '入驻', 'url_type' => '');
		default:
		}

		return array('imgurl' => '', 'linkurl' => app_url('dashboard/home/index', '', false), 'iconclass' => 'icon-home', 'text' => '首页', 'url_type' => '');
	}

	/**
     * Comment: 头条默认菜单
     * Author: zzw
     * Date: 2019/7/11 16:43
     */
	static public function defaultHeadlineMenu()
	{
		global $_W;
		global $_GPC;
		$domainName = $_W['siteroot'] ? $_W['siteroot'] : 'https://' . $_SERVER['SERVER_NAME'] . '/';
		$default = array(
			'id'           => -1,
			'uniacid'      => -1,
			'aid'          => -1,
			'name'         => '平台底部默认菜单',
			'createtime'   => time(),
			'lastedittime' => time(),
			'menu_class'   => 1,
			'is_public'    => '',
			'data'         => array(
				'menu_calss' => 1,
				'name'       => '平台底部默认菜单',
				'params'     => array('navstyle' => '0', 'navfloat' => 'top'),
				'style'      => array('bgcolor' => '#FFFFFF', 'iconcolor' => '#999999', 'iconcoloron' => '#FE433F', 'textcolor' => '#999999', 'textcoloron' => '#FE433F'),
				'data'       => array('M0123456789101' => self::menuSetInfo('home'), 'M0123456789104' => self::menuSetInfo('comment'), 'M0123456789102' => self::menuSetInfo('shop'), 'M0123456789105' => self::menuSetInfo('my'))
			)
		);
		return $default;
	}

	/**
     * Comment: 默认的平台底部菜单(首页菜单)
     * Author: zzw
     * Date: 2019/7/25 16:02
     */
	static public function defaultBottomMenu()
	{
		global $_W;
		global $_GPC;
		$domainName = $_W['siteroot'] ? $_W['siteroot'] : 'https://' . $_SERVER['SERVER_NAME'] . '/';
		$default = array(
			'id'           => -1,
			'uniacid'      => -1,
			'aid'          => -1,
			'name'         => '平台底部默认菜单',
			'createtime'   => time(),
			'lastedittime' => time(),
			'menu_class'   => 1,
			'is_public'    => '',
			'data'         => array(
				'menu_calss' => 1,
				'name'       => '平台底部默认菜单',
				'params'     => array('navstyle' => '0', 'navfloat' => 'top'),
				'style'      => array('bgcolor' => '#FFFFFF', 'iconcolor' => '#999999', 'iconcoloron' => '#FE433F', 'textcolor' => '#999999', 'textcoloron' => '#FE433F'),
				'data'       => array('M0123456789101' => self::menuSetInfo('home'), 'M0123456789102' => self::menuSetInfo('shop'), 'M0123456789103' => self::menuSetInfo('news_light'), 'M0123456789104' => self::menuSetInfo('activity'), 'M0123456789105' => self::menuSetInfo('my'))
			)
		);
		return $default;
	}
}

defined('IN_IA') || exit('Access Denied');

?>
