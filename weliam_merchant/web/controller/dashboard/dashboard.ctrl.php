<?php
//dezend by http://www.sucaihuo.com/
class Dashboard_WeliamController
{
	public function index()
	{
		global $_W;
		global $_GPC;
		$name = pdo_getcolumn(PDO_NAME . 'agentusers', array('id' => $_W['aid']), 'agentname');
		Area::initAgent();

		if ($_W['isajax']) {
			$data = Merchant::newSurvey($_W['aid']);
			$data2 = Cache::getCache('cachesur' . $_W['aid'], 'data');
			if ($data2['time'] < strtotime(date('Y-m-d'), time()) || $_GPC['refresh']) {
				$data2 = Merchant::cacheSurvey($_W['aid']);
				Cache::setCache('cachesur' . $_W['aid'], 'data', $data2);
			}

			$data = array_merge($data, $data2);
			$li = array('year' => date('m-d', time()), '金额' => $data['allmoney']);
			$data['list'][] = $li;
			exit(json_encode($data));
		}

		include wl_template('dashboard/index');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
