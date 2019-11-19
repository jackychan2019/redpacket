<?php
//dezend by http://www.sucaihuo.com/
class Helper_app_WeliamController
{
	public function index()
	{
		global $_W;
		global $_GPC;
		$pagetitle = '帮助中心';
		$slide = Util::getNumData('*', PDO_NAME . 'helper_slide', array('status' => 1, 'uniacid' => $_W['uniacid']), 'sort desc');
		$slide = $slide[0];
		$type = Util::getNumData('*', PDO_NAME . 'helper_type', array('status' => 1, 'uniacid' => $_W['uniacid']), 'sort desc');
		$type = $type[0];
		$question = Util::getNumData('*', PDO_NAME . 'helper_question', array('recommend' => 1, 'status' => 1, 'uniacid' => $_W['uniacid']), ' sort desc');
		$question = $question[0];

		foreach ($question as $key => $value) {
			$temp = Util::getSingelData('status', PDO_NAME . 'helper_type', array('id' => $value['type']));

			if ($temp['status'] == 0) {
				array_splice($question, $key);
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
		include wl_template('helper/index');
	}

	public function getAll()
	{
		global $_W;
		global $_GPC;
		$pagetitle = '全部问题';
		$type = $_GPC['type'];

		if ($type) {
			if ($type == 2) {
				$questions = Util::getNumData('*', PDO_NAME . 'helper_question', array('uniacid' => $_W['uniacid'], 'status' => 1), ' sort desc');
				$questions = $questions[0];

				foreach ($questions as $key => $value) {
					$temp = Util::getSingelData('status', PDO_NAME . 'helper_type', array('id' => $value['type']));

					if ($temp['status'] == 0) {
						array_splice($questions, $key);
					}
				}

				include wl_template('helper/questions');
			}
		}
	}

	public function searchs()
	{
		global $_W;
		global $_GPC;
		$pagetitle = '查询结果';
		$keyword = $_GPC['search'];

		if ($keyword) {
			$questions = Util::getNumData('*', PDO_NAME . 'helper_question', array('@title' => $keyword));
			$questions = $questions[0];
			include wl_template('helper/questions');
		}
	}

	public function getByTypeId()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];

		if ($id) {
			$type = Util::getSingelData('name', PDO_NAME . 'helper_type', array('id' => $id));
			$question = Util::getNumData('*', PDO_NAME . 'helper_question', array('type' => $id, 'status' => 1));
			$question = $question[0];
			$pagetitle = $type['name'];
			include wl_template('helper/qubytype');
		}
	}

	public function skiplink()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['skip_id'];

		if ($id) {
			$qu = Util::getSingelData('*', PDO_NAME . 'helper_question', array('id' => $id));
			include wl_template('helper/quinfo');
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
