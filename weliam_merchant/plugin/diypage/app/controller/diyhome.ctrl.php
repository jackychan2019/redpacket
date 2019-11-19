<?php
//dezend by http://www.sucaihuo.com/
defined('IN_IA') || exit('Access Denied');
class Diyhome_WeliamController extends Weliam_merchantModuleSite
{
	/**
     * Comment: app端首页
     * Author: zzw
     */
	public function home()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$pageset = Diy::getPage($id, false);
		$pageInfo = $pageset['data']['page'];
		$page['poster'] = $pageInfo['poster'];
		$page['bgm_music'] = $pageInfo['bgm_music'];
		$page['title'] = $pageInfo['title'];
		$page['background'] = $pageInfo['background'];
		$page_type = $pageInfo['type'];

		if ($page_type == 2) {
			$pageInfo['diymenu'] = $_W['agentset']['diypageset']['menu_index'];
			$pageInfo['diyadv'] = $_W['agentset']['diypageset']['adv_index'];
		}
		else if ($page_type == 3) {
			$pageInfo['diymenu'] = $_W['agentset']['diypageset']['menu_rush'];
			$pageInfo['diyadv'] = $_W['agentset']['diypageset']['adv_rush'];
		}
		else if ($page_type == 4) {
			$pageInfo['diymenu'] = $_W['agentset']['diypageset']['menu_groupon'];
			$pageInfo['diyadv'] = $_W['agentset']['diypageset']['adv_groupon'];
		}
		else if ($page_type == 5) {
			$pageInfo['diymenu'] = $_W['agentset']['diypageset']['menu_wlcoupon'];
			$pageInfo['diyadv'] = $_W['agentset']['diypageset']['adv_wlcoupon'];
		}
		else if ($page_type == 6) {
			$pageInfo['diymenu'] = $_W['agentset']['diypageset']['menu_wlfightgroup'];
			$pageInfo['diyadv'] = $_W['agentset']['diypageset']['adv_wlfightgroup'];
		}
		else {
			if ($page_type == 7) {
				$pageInfo['diymenu'] = $_W['agentset']['diypageset']['menu_bargain'];
				$pageInfo['diyadv'] = $_W['agentset']['diypageset']['adv_bargain'];
			}
		}

		$menudata = '';

		if (0 < $pageInfo['diymenu']) {
			$menudata = Diy::getMenu($pageInfo['diymenu'], true);
		}

		if (0 < $pageInfo['diyadv']) {
			$advdata = Diy::BeOverdue($pageInfo['diyadv']);
		}

		if ($pageInfo['share_title'] && $pageInfo['share_description'] && $pageInfo['share_image']) {
			$_W['wlsetting']['share']['share_title'] = $pageInfo['share_title'];
			$_W['wlsetting']['share']['share_desc'] = $pageInfo['share_description'];
			$_W['wlsetting']['share']['share_image'] = tomedia($pageInfo['share_image']);
		}

		$meroof = Setting::agentsetting_read('meroof');
		$shout = rtrim($meroof['shout'], ',');
		$shout = explode(',', $shout);
		$pagetitle = $page['title'];
		include wl_template('diypage/diyhome');
	}

	/**
     * Comment: 获取页面的配置信息
     * Author: zzw
     */
	public function getPageInfo()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$lat = $_GPC['lat'];
		$lng = $_GPC['lng'];
		$pageset = Diy::getPage($id, true);
		$item = $pageset['data']['items'];
		wl_json(1, '页面配置信息', $item);
	}
}

?>
