<?php
//dezend by http://www.sucaihuo.com/
class Plugins_WeliamController
{
	public function index()
	{
		global $_W;
		global $_GPC;

		if (is_agent()) {
			$plugins = App::get_apps($_W['aid'], 'agent');
			//die('aaaa');
		}
		else {
			$plugins = App::get_apps($_W['uniacid']);
		//	die('bbb');
		}

		$category = App::getCategory();
		
		var_dump($plugins);exit;
		
		include wl_template('app/plugins_list');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
