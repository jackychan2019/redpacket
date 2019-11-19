<?php
//dezend by http://www.sucaihuo.com/
class Home_WeliamController
{
	/**
     * Comment: 进入平台首页
     */
	public function index()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '首页 - ' . $_W['wlsetting']['base']['name'] : '首页';
		if (p('diypage') && !empty($_W['agentset']['diypageset']['page_index'])) {
			header('Location:' . app_url('diypage/diyhome/home', array('aid' => $_W['aid'], 'id' => $_W['agentset']['diypageset']['page_index'])));
			exit();
		}
		else {
			if ($_W['agentset']['foot']['onestatus'] && !empty($_W['agentset']['foot']['oneurl']) && !strstr($_W['agentset']['foot']['oneurl'], 'p=dashboard&ac=home')) {
				header('location: ' . $_W['agentset']['foot']['oneurl']);
				exit();
			}

			$data = Dashboard::get_app_data();
			$navs = $data['nav'];
			$i = 0;

			while ($i < count($data['adv']) - 1) {
				$j = 0;

				while ($j < count($data['adv']) - 1 - $i) {
					if ($data['adv'][$j]['displayorder'] < $data['adv'][$j + 1]['displayorder']) {
						$temp = $data['adv'][$j + 1];
						$data['adv'][$j + 1] = $data['adv'][$j];
						$data['adv'][$j] = $temp;
					}

					++$j;
				}

				++$i;
			}

			$halfcardbase = Setting::agentsetting_read('halfcard');
			$fightgroup = Setting::agentsetting_read('fightgroup');
			$plugin = Setting::agentsetting_read('pluginlist');
			$meroof = Setting::agentsetting_read('meroof');
			$pluginlist[] = array('name' => '商家', 'status' => $plugin['sjstatus'], 'id' => 1, 'order' => $plugin['sjorder'], 'newname' => $plugin['sjname']);
			$pluginlist[] = array('name' => '抢购', 'status' => $plugin['qgstatus'], 'id' => 3, 'order' => $plugin['qgorder'], 'newname' => $plugin['qgname']);
			$pluginlist[] = array('name' => '特权', 'status' => $plugin['wzstatus'], 'id' => 2, 'order' => $plugin['wzorder'], 'newname' => $plugin['wzname']);
			$pluginlist[] = array('name' => '拼团', 'status' => $plugin['ptstatus'], 'id' => 5, 'order' => $plugin['ptorder'], 'newname' => $plugin['ptname']);
			$pluginlist[] = array('name' => '卡券', 'status' => $plugin['kqstatus'], 'id' => 4, 'order' => $plugin['kqorder'], 'newname' => $plugin['kqname']);
			$pluginlist[] = array('name' => '同城', 'status' => $plugin['tcstatus'], 'id' => 6, 'order' => $plugin['tcorder'], 'newname' => $plugin['tcname']);

			if (p('groupon')) {
				$pluginlist[] = array('name' => '团购', 'status' => $plugin['tgstatus'], 'id' => 8, 'order' => $plugin['tgorder'], 'newname' => $plugin['tgname']);
			}

			if (p('bargain')) {
				$pluginlist[] = array('name' => '砍价', 'status' => $plugin['kjstatus'], 'id' => 9, 'order' => $plugin['kjorder'], 'newname' => $plugin['kjname']);
			}

			$pluginlist[] = array('name' => '积分', 'status' => $plugin['jfstatus'], 'id' => 10, 'order' => $plugin['jforder'], 'newname' => $plugin['jfname']);
			$pluginlist[] = array('name' => '礼包', 'status' => $plugin['gpstatus'], 'id' => 11, 'order' => $plugin['gporder'], 'newname' => $plugin['gpname']);
			$nulpl = 0;

			foreach ($pluginlist as $k => $v) {
				if ($v['status'] == 1) {
					$nulpl = 1;
					break;
				}
			}

			$i = 0;

			while ($i < count($pluginlist) - 1) {
				$j = 0;

				while ($j < count($pluginlist) - 1 - $i) {
					if ($pluginlist[$j]['order'] < $pluginlist[$j + 1]['order']) {
						$temp = $pluginlist[$j + 1];
						$pluginlist[$j + 1] = $pluginlist[$j];
						$pluginlist[$j] = $temp;
					}

					++$j;
				}

				++$i;
			}

			$opennum = 0;

			foreach ($pluginlist as $key => $ppp) {
				if ($ppp['status']) {
					++$opennum;
				}
			}

			$first = $pluginlist[0]['id'];
			$shout = rtrim($meroof['shout'], ',');
			$shout = explode(',', $shout);
			$sys = Setting::agentsetting_read('community');

			if ($sys['communqrcode']) {
				$community['qrcode'] = tomedia($sys['communqrcode']);
				$community['name'] = $sys['communname'];
				$community['desc'] = $sys['commundesc'];
				$community['img'] = tomedia($sys['communimg']);
			}
		}

		if (p('diypage')) {
			if (!empty($_W['agentset']['diypageset']['adv_index'])) {
				$advdata = Diy::BeOverdue($_W['agentset']['diypageset']['adv_index']);
			}

			if (!empty($_W['agentset']['diypageset']['menu_index'])) {
				$menudata = Diy::getMenu($_W['agentset']['diypageset']['menu_index']);
			}
		}

		$page_type = 2;
		$JQpostiton = true;
		include wl_template('dashboard/index');
	}

	/**
     * Comment: 进入消息(公告)列表
     */
	public function noticelist()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '消息列表 - ' . $_W['wlsetting']['base']['name'] : '消息列表';
		$notice = pdo_fetchall('SELECT * FROM ' . tablename(PDO_NAME . 'notice') . (' WHERE enabled = 1 and uniacid = \'' . $_W['uniacid'] . '\' and aid = ' . $_W['aid'] . ' ORDER BY id DESC'));
		include wl_template('dashboard/noticelist');
	}

	/**
     * Comment: 进入消息(公告)详情
     */
	public function noticedetail()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '公告详情 - ' . $_W['wlsetting']['base']['name'] : '公告详情';
		$id = intval($_GPC['id']);

		if (empty($id)) {
			wl_message('缺少关键参数');
		}

		$notice = Dashboard::getSingleNotice($id);
		include wl_template('dashboard/noticedetail');
	}

	/**
     * Comment: 搜索内容,支持店铺名称、商品名称
     * Author: zzw
     */
	public function searchInfo()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '搜索内容 - ' . $_W['wlsetting']['base']['name'] : '搜索内容';
		$search = $_GPC['search'];

		if ($_W['ispost']) {
			$lng = $_GPC['lng'] ? $_GPC['lng'] : 0;
			$lat = $_GPC['lat'] ? $_GPC['lat'] : 0;
			$page = $_GPC['page'] ? $_GPC['page'] : 1;
			$data = WeChat::getSearch($page, $search, $lng, $lat);
			unset($data['count']);
			unset($data['headlineList']);
			wl_json(1, '搜索内容', $data);
		}

		$meroof = Setting::agentsetting_read('meroof');
		$shout = rtrim($meroof['shout'], ',');
		$shout = explode(',', $shout);
		include wl_template('dashboard/searchResult');
	}
}

defined('IN_IA') || exit('Access Denied');
session_start();

?>
