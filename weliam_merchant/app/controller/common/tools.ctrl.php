<?php
//dezend by http://www.sucaihuo.com/
class Tools_WeliamController
{
	public function poster()
	{
		global $_W;
		global $_GPC;
		$pagetitle = !empty($_W['wlsetting']['base']['name']) ? '生成海报 - ' . $_W['wlsetting']['base']['name'] : '生成海报';
		$id = intval($_GPC['id']);
		$type = trim($_GPC['type']);
		$disflag = intval($_GPC['disflag']) ? intval($_GPC['disflag']) : 0;
		$noshare = 1;

		if ($type == 'distribution') {
			$flag = Distribution::checkdisflag($_W['mid']);

			if (empty($flag)) {
				wl_message('您不是分销商,无法生成分销海报', app_url('distribution/disappbase/applyindex'), 'error');
			}
		}

		if ($type == 'store') {
			$backurl = app_url('store/merchant/detail', array('id' => $id));
		}
		else {
			if ($type == 'distribution' || $type == 'invitevip') {
				$backurl = app_url('distribution/disappbase/index');
			}
			else if ($type == 'rush') {
				$backurl = app_url('rush/home/detail', array('id' => $id));
			}
			else if ($type == 'groupon') {
				$backurl = app_url('groupon/grouponapp/groupondetail', array('cid' => $id));
			}
			else if ($type == 'coupon') {
				$backurl = app_url('wlcoupon/coupon_app/couponsdetail', array('id' => $id));
			}
			else if ($type == 'fightgroup') {
				$backurl = app_url('wlfightgroup/fightapp/goodsdetail', array('id' => $id));
			}
			else {
				if ($type == 'bargain') {
					$backurl = app_url('bargain/bargain_app/bargaindetail', array('cid' => $id));
				}
			}
		}

		if ($_W['ispost']) {
			$useagent = trim($_GPC['useagent']);
			$bgimg = trim($_GPC['bgimg']);

			if ($type == 'store') {
				$poster = Poster::createStorePoster($id, $useagent, $bgimg, $_W['mid']);
			}
			else if ($type == 'distribution') {
				$poster = Poster::createDistriPoster($id, $disflag, $useagent, $bgimg);
			}
			else if ($type == 'rush') {
				$poster = Poster::createRushPoster($id, $useagent, $bgimg, $_W['mid']);
			}
			else if ($type == 'groupon') {
				$poster = Poster::createGrouponPoster($id, $useagent, $bgimg, $_W['mid']);
			}
			else if ($type == 'coupon') {
				$poster = Poster::createCouponPoster($id, $useagent, $bgimg, $_W['mid']);
			}
			else if ($type == 'fightgroup') {
				$poster = Poster::createFightgroupPoster($id, $useagent, $bgimg);
			}
			else if ($type == 'bargain') {
				$poster = Poster::createBargainPoster($id, $disflag, $useagent, $bgimg);
			}
			else {
				if ($type == 'invitevip') {
					$poster = Poster::createInvitevipPoster($id, $useagent, $bgimg);
				}
			}

			$posterimg = Tools::createImage($poster);
			wl_json(0, '', array('url' => $poster . '?v=' . time(), 'width' => imagesx($posterimg), 'height' => imagesy($posterimg)));
		}

		if (p('diyposter')) {
			$tplarr = array('store' => $_W['wlsetting']['diyposter']['storepid'], 'distribution' => $_W['wlsetting']['diyposter']['distpid'], 'rush' => $_W['wlsetting']['diyposter']['rushpid'], 'groupon' => $_W['wlsetting']['diyposter']['grouponpid'], 'coupon' => $_W['wlsetting']['diyposter']['cardpid']);
			if (!empty($tplarr[$type]) && empty($disflag)) {
				$bgtpl = pdo_get(PDO_NAME . 'poster', array('uniacid' => $_W['uniacid'], 'id' => $tplarr[$type]), array('bg', 'otherbg'));
				$templist = iunserializer($bgtpl['otherbg']);
			}
		}

		include wl_template('common/poster');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
