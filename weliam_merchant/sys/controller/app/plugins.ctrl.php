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
		}
		else {
			$plugins = App::get_apps($_W['uniacid']);
		}

		$category = App::getCategory();
		include wl_template('app/plugins_list');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
