<?php
//dezend by http://www.sucaihuo.com/
class Active_WeliamController
{
	public function activelist()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$data = array();

		if ($_GPC['status'] == 4) {
			$data['status#'] = '(0,4)';
		}
		else {
			if (!empty($_GPC['status'])) {
				$data['status'] = intval($_GPC['status']);
			}
		}

		$data['aid'] = $_W['aid'];

		if (!empty($_GPC['keyword'])) {
			if (!empty($_GPC['keywordtype'])) {
				switch ($_GPC['keywordtype']) {
				case 1:
					$data['@name@'] = $_GPC['keyword'];
					break;

				case 2:
					$data['@id@'] = $_GPC['keyword'];
					break;

				default:
					break;
				}

				if ($_GPC['keywordtype'] == 3) {
					$keyword = $_GPC['keyword'];
					$params[':storename'] = '%' . $keyword . '%';
					$merchants = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_merchantdata') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' AND storename LIKE :storename'), $params);

					if ($merchants) {
						$sids = '(';

						foreach ($merchants as $key => $v) {
							if ($key == 0) {
								$sids .= $v['id'];
							}
							else {
								$sids .= ',' . $v['id'];
							}
						}

						$sids .= ')';
						$data['sid#'] = $sids;
					}
					else {
						$data['sid#'] = '(0)';
					}
				}
			}
		}

		$activity = Groupon::getNumActive('*', $data, 'sort DESC,ID DESC', $pindex, $psize, 1);
		$pager = $activity[1];
		$activity = $activity[0];

		foreach ($activity as $key => &$value) {
			$value['storename'] = pdo_getcolumn(PDO_NAME . 'merchantdata', array('id' => $value['sid']), 'storename');
			Groupon::changeActivestatus($value);
			$value['placeorder'] = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE fkid = ' . $value['id'] . ' AND plugin = \'groupon\' AND status in (0,1,2,3,4,6,8,9)'));

			if (empty($value['placeorder'])) {
				$value['placeorder'] = 0;
			}

			$value['alreadypay'] = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE fkid = ' . $value['id'] . ' AND plugin = \'groupon\' AND status in (1,2,3,4,6,8,9)'));

			if (empty($value['alreadypay'])) {
				$value['alreadypay'] = 0;
			}

			$value['alreadyuse'] = pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('wlmerchant_order') . (' WHERE fkid = ' . $value['id'] . ' AND plugin = \'groupon\' AND status in (2, 3)'));

			if (empty($value['alreadyuse'])) {
				$value['alreadyuse'] = 0;
			}
		}

		$status0 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and aid=' . $_W['aid']));
		$status1 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=1 and aid=' . $_W['aid']));
		$status2 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=2 and aid=' . $_W['aid']));
		$status3 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=3 and aid=' . $_W['aid']));
		$status4 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status IN (0,4) and aid=' . $_W['aid']));
		$status5 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=5 and aid=' . $_W['aid']));
		$status6 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=6 and aid=' . $_W['aid']));
		$status7 = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename(PDO_NAME . 'groupon_activity') . (' WHERE uniacid=' . $_W['uniacid'] . ' and status=7 and aid=' . $_W['aid']));
		include wl_template('grouponactive/active_list');
	}

	public function createactive()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$grouponset = Setting::agentsetting_read('groupon');

		if (p('distribution')) {
			$distriset = Setting::wlsetting_read('distribution');
		}
		else {
			$distriset = 0;
		}

		if (p('userlabel')) {
			$labels = pdo_getall('wlmerchant_userlabel', array('uniacid' => $_W['uniacid']), array('id', 'name'), '', 'sort DESC');
		}

		$levels = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_halflevel') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND status = 1 ORDER BY sort DESC'));

		if ($id) {
			$goods = Groupon::getSingleActive($id, '*');
			$merchant = Groupon::getSingleMerchant($goods['sid'], 'id,storename,logo');
			$goods['thumbs'] = unserialize($goods['thumbs']);
			$tags = unserialize($goods['tag']);
			$orderinfo = unserialize($goods['orderinfo']);
			$userlabel = unserialize($goods['userlabel']);
			$goods['level'] = unserialize($goods['level']);
			$allspecs = pdo_fetchall('select * from ' . tablename('wlmerchant_goods_spec') . ' where goodsid=:id AND type = 3 order by displayorder asc', array(':id' => $id));

			foreach ($allspecs as &$s) {
				$s['items'] = pdo_fetchall('SELECT * FROM ' . tablename('wlmerchant_goods_spec_item') . ('WHERE uniacid = ' . $_W['uniacid'] . ' AND specid = ' . $s['id'] . ' ORDER BY displayorder ASC'));
			}

			unset($s);
			$html = '';
			$options = pdo_fetchall('select * from ' . tablename('wlmerchant_goods_option') . ' where goodsid=:id AND type = 3 order by id asc', array(':id' => $id));
			$specs = array();

			if (0 < count($options)) {
				$specitemids = explode('_', $options[0]['specs']);

				foreach ($specitemids as $itemid) {
					foreach ($allspecs as $ss) {
						$items = $ss['items'];

						foreach ($items as $it) {
							if ($it['id'] == $itemid) {
								$specs[] = $ss;
								break;
							}
						}
					}
				}

				$html = '';
				$html .= '<table class="table table-bordered table-condensed">';
				$html .= '<thead>';
				$html .= '<tr class="active">';
				$len = count($specs);
				$newlen = 1;
				$h = array();
				$rowspans = array();
				$i = 0;

				while ($i < $len) {
					$html .= '<th>' . $specs[$i]['title'] . '</th>';
					$itemlen = count($specs[$i]['items']);

					if ($itemlen <= 0) {
						$itemlen = 1;
					}

					$newlen *= $itemlen;
					$h = array();
					$j = 0;

					while ($j < $newlen) {
						$h[$i][$j] = array();
						++$j;
					}

					$l = count($specs[$i]['items']);
					$rowspans[$i] = 1;
					$j = $i + 1;

					while ($j < $len) {
						$rowspans[$i] *= count($specs[$j]['items']);
						++$j;
					}

					++$i;
				}

				$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">库存</div><div class="input-group"><input type="text" class="form-control input-sm option_stock_all"  VALUE=""/><span class="input-group-addon" ><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';
				$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">团购价</div><div class="input-group"><input type="text" class="form-control input-sm option_price_all"  VALUE=""/><span class="input-group-addon" ><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_price\');"></a></span></div></div></th>';
				$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">会员价</div><div class="input-group"><input type="text" class="form-control input-sm option_vipprice_all"  VALUE=""/><span class="input-group-addon" ><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_vipprice\');"></a></span></div></div></th>';
				$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">结算价</div><div class="input-group"><input type="text" class="form-control input-sm option_settlementmoney_all"  VALUE=""/><span class="input-group-addon" ><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_settlementmoney\');"></a></span></div></div></th>';
				$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">会员结算价</div><div class="input-group"><input type="text" class="form-control input-sm option_vipsettlementmoney_all"  VALUE=""/><span class="input-group-addon" ><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_vipsettlementmoney\');"></a></span></div></div></th>';
				$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">一级分销</div><div class="input-group"><input type="text" class="form-control input-sm option_onedismoney_all"  VALUE=""/><span class="input-group-addon" ><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_onedismoney\');"></a></span></div></div></th>';

				if (1 < $distriset['ranknum']) {
					$html .= '<th><div class=""><div style="padding-bottom:10px;text-align:center;">二级分销</div><div class="input-group"><input type="text" class="form-control input-sm option_twodismoney_all"  VALUE=""/><span class="input-group-addon" ><a href="javascript:;" class="fa fa-angle-double-down" title="批量设置" onclick="setCol(\'option_twodismoney\');"></a></span></div></div></th>';
				}

				$html .= '</tr></thead>';
				$m = 0;

				while ($m < $len) {
					$k = 0;
					$kid = 0;
					$n = 0;
					$j = 0;

					while ($j < $newlen) {
						$rowspan = $rowspans[$m];

						if ($j % $rowspan == 0) {
							$h[$m][$j] = array('html' => '<td class=\'full\' rowspan=\'' . $rowspan . '\'>' . $specs[$m]['items'][$kid]['title'] . '</td>', 'id' => $specs[$m]['items'][$kid]['id']);
						}
						else {
							$h[$m][$j] = array('html' => '', 'id' => $specs[$m]['items'][$kid]['id']);
						}

						++$n;

						if ($n == $rowspan) {
							++$kid;

							if (count($specs[$m]['items']) - 1 < $kid) {
								$kid = 0;
							}

							$n = 0;
						}

						++$j;
					}

					++$m;
				}

				$hh = '';
				$i = 0;

				while ($i < $newlen) {
					$hh .= '<tr>';
					$ids = array();
					$j = 0;

					while ($j < $len) {
						$hh .= $h[$j][$i]['html'];
						$ids[] = $h[$j][$i]['id'];
						++$j;
					}

					$ids = implode('_', $ids);
					$val = array('id' => '', 'title' => '', 'stock' => '', 'price' => '', 'vipprice' => '', 'settlementmoney' => '', 'vipsettlementmoney' => '', 'onedismoney' => '', 'twodismoney' => '', 'threedismoney' => '');

					foreach ($options as $o) {
						if ($ids === $o['specs']) {
							$val = array('id' => $o['id'], 'title' => $o['title'], 'stock' => $o['stock'], 'price' => $o['price'], 'vipprice' => $o['vipprice'], 'settlementmoney' => $o['settlementmoney'], 'vipsettlementmoney' => $o['vipsettlementmoney'], 'onedismoney' => $o['onedismoney'], 'twodismoney' => $o['twodismoney'], 'threedismoney' => $o['threedismoney']);
							break;
						}
					}

					$hh .= '<td>';
					$hh .= '<input data-name="option_stock_' . $ids . '"  type="text" class="form-control option_stock option_stock_' . $ids . '" value="' . $val['stock'] . '"/>';
					$hh .= '</td>';
					$hh .= '<input data-name="option_id_' . $ids . '"  type="hidden" class="form-control option_id option_id_' . $ids . '" value="' . $val['id'] . '"/>';
					$hh .= '<input data-name="option_ids"  type="hidden" class="form-control option_ids option_ids_' . $ids . '" value="' . $ids . '"/>';
					$hh .= '<input data-name="option_title_' . $ids . '"  type="hidden" class="form-control option_title option_title_' . $ids . '" value="' . $val['title'] . '"/>';
					$hh .= '<td><input data-name="option_price_' . $ids . '" type="text" class="form-control option_price option_price_' . $ids . '" value="' . $val['price'] . '"/></td>';
					$hh .= '<td><input data-name="option_vipprice_' . $ids . '" type="text" class="form-control option_vipprice option_vipprice_' . $ids . '" value="' . $val['vipprice'] . '"/></td>';
					$hh .= '<td><input data-name="option_settlementmoney_' . $ids . '" type="text" class="form-control option_settlementmoney option_settlementmoney_' . $ids . '" " value="' . $val['settlementmoney'] . '"/></td>';
					$hh .= '<td><input data-name="option_vipsettlementmoney_' . $ids . '" type="text" class="form-control option_vipsettlementmoney option_vipsettlementmoney_' . $ids . '" " value="' . $val['vipsettlementmoney'] . '"/></td>';
					$hh .= '<td><input data-name="option_onedismoney_' . $ids . '" type="text" class="form-control option_onedismoney option_onedismoney_' . $ids . '" " value="' . $val['onedismoney'] . '"/></td>';

					if (1 < $distriset['ranknum']) {
						$hh .= '<td><input data-name="option_twodismoney_' . $ids . '" type="text" class="form-control option_twodismoney option_twodismoney_' . $ids . '" " value="' . $val['twodismoney'] . '"/></td>';
					}

					$hh .= '</tr>';
					++$i;
				}

				$html .= $hh;
				$html .= '</table>';
			}
		}

		if (empty($goods['starttime']) || empty($goods['endtime'])) {
			$goods['starttime'] = time();
			$goods['endtime'] = strtotime('+1 month');
			$goods['cutofftime'] = strtotime('+2 month');
		}

		$presettags = pdo_getall('wlmerchant_tags', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'type' => 3), array('id', 'title'), '', 'sort DESC');
		$categoryes = pdo_getall('wlmerchant_groupon_category', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']));

		if ($grouponset['catelevel'] == 1) {
			$par = $chil = array();

			foreach ($categoryes as $cid => $cate) {
				if (!empty($cate['parentid'])) {
					$chil[$cate['parentid']][] = $cate;
				}
				else {
					$par[$cate['id']] = $cate;
				}
			}

			if (!empty($goods['cateid'])) {
				$onelevel = pdo_getcolumn('wlmerchant_groupon_category', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid'], 'id' => $goods['cateid']), 'parentid');
				$goods['onelevel'] = $onelevel ? $onelevel : $goods['cateid'];
			}
		}

		$express = Store::getNumExpress('*', array('uniacid' => $_W['uniacid'], 'aid' => $_W['aid']), 'ID DESC', 0, 0, 0);
		$express = $express[0];
		$falseorders = $goods['falseorder'];

		if ($falseorders) {
			$falseorders = unserialize($falseorders);
			$falsenum = count($falseorders);
		}
		else {
			$falsenum = 0;
		}

		$goods['listtag'] = unserialize($goods['listtag']);

		if ($_W['ispost']) {
			$goods = $_GPC['goods'];

			if (empty($goods['sid'])) {
				wl_message('请选择商户');
			}

			if (empty($goods['price'])) {
				wl_message('请填写市场价格');
			}

			if (empty($goods['name'])) {
				wl_message('请填写商品名称');
			}

			if (empty($goods['num'])) {
				wl_message('请填写团购数量');
			}

			$goods['cutoffstatus'] = $_GPC['cutoffstatus'];
			$goods['optionstatus'] = $_GPC['optionstatus'];
			$goods['is_indexshow'] = $_GPC['is_indexshow'];
			$goods['recommend'] = $_GPC['recommend'];
			$goods['sharestatus'] = $_GPC['sharestatus'];
			$goods['independent'] = $_GPC['independent'];
			$goods['isdistri'] = $_GPC['isdistri'];
			$goods['vipstatus'] = $_GPC['vipstatus'];
			$goods['fastpay'] = $_GPC['fastpay'];
			$goods['detail'] = htmlspecialchars_decode($goods['detail']);
			$goods['describe'] = htmlspecialchars_decode($goods['describe']);
			$goods['thumbs'] = serialize($goods['thumbs']);
			$tag = $_GPC['tag'];
			$goods['tag'] = serialize($tag);
			$userlabel = $_GPC['userlabel'];
			$goods['userlabel'] = serialize($userlabel);
			$level = $_GPC['level'];
			$goods['level'] = serialize($level);
			$time = $_GPC['time'];
			$goods['starttime'] = strtotime($time['start']);
			$goods['endtime'] = strtotime($time['end']);
			$goods['cutofftime'] = strtotime($_GPC['cutofftime']);
			$goods['aid'] = $_W['aid'];

			if ($grouponset['catelevel'] == 1) {
				$goods['cateid'] = intval($_GPC['category']['childid']);
			}
			else {
				$goods['cateid'] = intval($_GPC['cateid']);
			}

			if (time() < $goods['starttime']) {
				$goods['status'] = 1;
			}
			else {
				if ($goods['starttime'] < time() && time() < $goods['endtime']) {
					$goods['status'] = 2;
				}
				else {
					if ($goods['endtime'] < time()) {
						$goods['status'] = 3;
					}
				}
			}

			$tagcontent = $_GPC['tagcontent'];
			$textcolor = $_GPC['textcolor'];
			$bgcolor = $_GPC['bgcolor'];
			$len = count($tagcontent);
			$listtag = array();
			$k = 0;

			while ($k < $len) {
				$listtag[$k]['tagcontent'] = $tagcontent[$k];
				$listtag[$k]['textcolor'] = $textcolor[$k];
				$listtag[$k]['bgcolor'] = $bgcolor[$k];
				++$k;
			}

			$listtag = serialize($listtag);
			$goods['listtag'] = $listtag;

			if ($id) {
				$res = Groupon::updateActive($goods, array('id' => $id));
			}
			else {
				$goods['levelnum'] = $goods['num'];
				$id = Groupon::savegrouponActive($goods);
			}

			Tools::clearposter();

			if ($goods['optionstatus']) {
				$totalstocks = 0;
				$spec_ids = $_POST['spec_id'];
				$spec_titles = $_POST['spec_title'];
				$specids = array();
				$len = count($spec_ids);
				$specids = array();
				$spec_items = array();
				$k = 0;

				while ($k < $len) {
					$spec_id = '';
					$get_spec_id = $spec_ids[$k];
					$a = array('uniacid' => $_W['uniacid'], 'goodsid' => $id, 'displayorder' => $k, 'title' => $spec_titles[$get_spec_id]);

					if (is_numeric($get_spec_id)) {
						pdo_update('wlmerchant_goods_spec', $a, array('id' => $get_spec_id));
						$spec_id = $get_spec_id;
					}
					else {
						$a['type'] = 3;
						pdo_insert('wlmerchant_goods_spec', $a);
						$spec_id = pdo_insertid();
					}

					$spec_item_ids = $_POST['spec_item_id_' . $get_spec_id];
					$spec_item_titles = $_POST['spec_item_title_' . $get_spec_id];
					$spec_item_shows = $_POST['spec_item_show_' . $get_spec_id];
					$spec_item_thumbs = $_POST['spec_item_thumb_' . $get_spec_id];
					$spec_item_oldthumbs = $_POST['spec_item_oldthumb_' . $get_spec_id];
					$spec_item_virtuals = $_POST['spec_item_virtual_' . $get_spec_id];
					$itemlen = count($spec_item_ids);
					$itemids = array();
					$n = 0;

					while ($n < $itemlen) {
						$item_id = '';
						$get_item_id = $spec_item_ids[$n];
						$d = array('uniacid' => $_W['uniacid'], 'specid' => $spec_id, 'displayorder' => $n, 'title' => $spec_item_titles[$n], 'show' => $spec_item_shows[$n], 'thumb' => $spec_item_thumbs[$n]);

						if (is_numeric($get_item_id)) {
							pdo_update('wlmerchant_goods_spec_item', $d, array('id' => $get_item_id));
							$item_id = $get_item_id;
						}
						else {
							pdo_insert('wlmerchant_goods_spec_item', $d);
							$item_id = pdo_insertid();
						}

						$itemids[] = $item_id;
						$d['get_id'] = $get_item_id;
						$d['id'] = $item_id;
						$spec_items[] = $d;
						++$n;
					}

					if (0 < count($itemids)) {
						pdo_query('delete from ' . tablename('wlmerchant_goods_spec_item') . ' where specid=' . $spec_id . ' and id not in (' . implode(',', $itemids) . ')');
					}
					else {
						pdo_query('delete from ' . tablename('wlmerchant_goods_spec_item') . ' where specid=' . $spec_id);
					}

					pdo_update('wlmerchant_goods_spec', array('content' => serialize($itemids)), array('id' => $spec_id));
					$specids[] = $spec_id;
					++$k;
				}

				if (0 < count($specids)) {
					$res = pdo_query('delete from ' . tablename('wlmerchant_goods_spec') . ' where goodsid =' . $id . ' and type = 3 and id NOT IN (' . implode(',', $specids) . ')');
				}
				else {
					$res = pdo_query('delete from ' . tablename('wlmerchant_goods_spec') . ' where type = 3 and goodsid =' . $id);
				}

				$optionArray = json_decode($_POST['optionArray'], true);
				$option_idss = $optionArray['option_ids'];
				$len = count($option_idss);
				$optionids = array();
				$k = 0;

				while ($k < $len) {
					$option_id = '';
					$ids = $option_idss[$k];
					$get_option_id = $optionArray['option_id'][$k];
					$idsarr = explode('_', $ids);
					$newids = array();

					foreach ($idsarr as $key => $ida) {
						foreach ($spec_items as $it) {
							if ($it['get_id'] == $ida) {
								$newids[] = $it['id'];
								break;
							}
						}
					}

					$newids = implode('_', $newids);
					$a = array('uniacid' => $_W['uniacid'], 'stock' => $optionArray['option_stock'][$k], 'title' => $optionArray['option_title'][$k], 'price' => $optionArray['option_price'][$k], 'vipprice' => $optionArray['option_vipprice'][$k], 'settlementmoney' => $optionArray['option_settlementmoney'][$k], 'vipsettlementmoney' => $optionArray['option_vipsettlementmoney'][$k], 'onedismoney' => $optionArray['option_onedismoney'][$k], 'twodismoney' => $optionArray['option_twodismoney'][$k], 'threedismoney' => $optionArray['option_threedismoney'][$k], 'goodsid' => $id, 'specs' => $newids, 'type' => 3);
					$totalstocks += $a['stock'];

					if (empty($get_option_id)) {
						pdo_insert('wlmerchant_goods_option', $a);
						$option_id = pdo_insertid();
					}
					else {
						pdo_update('wlmerchant_goods_option', $a, array('id' => $get_option_id));
						$option_id = $get_option_id;
					}

					$optionids[] = $option_id;
					++$k;
				}

				if (0 < count($optionids)) {
					pdo_query('delete from ' . tablename('wlmerchant_goods_option') . ' where type = 3 AND goodsid=' . $id . ' and id not in ( ' . implode(',', $optionids) . ')');
				}
				else {
					pdo_query('delete from ' . tablename('wlmerchant_goods_option') . ' where type = 3 AND goodsid=' . $id);
				}
			}

			wl_message('保存成功！', web_url('groupon/active/activelist'), 'success');
		}

		include wl_template('grouponactive/createactive');
	}

	public function spec()
	{
		global $_W;
		global $_GPC;
		$spec = array('id' => random(32), 'title' => $_GPC['title']);
		include wl_template('grouponactive/spec');
	}

	public function spec_item()
	{
		global $_W;
		global $_GPC;
		$spec = array('id' => $_GPC['specid']);
		$specitem = array('id' => random(32), 'title' => $_GPC['title'], 'show' => 1);
		include wl_template('grouponactive/spec_item');
	}

	public function ajax()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$da = Groupon::getSingleGoods($id, '*');
		exit(json_encode($da));
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$status = $_GPC['status'];

		if ($status == 4) {
			$res = Groupon::updateActive(array('status' => 1), array('id' => $id));
		}
		else {
			$res = Groupon::updateActive(array('status' => 4), array('id' => $id));
		}

		if ($res) {
			exit(json_encode(array('errno' => 0)));
		}
		else {
			exit(json_encode(array('errno' => 1)));
		}
	}

	public function delall()
	{
		global $_W;
		global $_GPC;
		$res = Groupon::deleteActive(array('id' => intval($_GPC['id']), 'uniacid' => $_W['uniacid']));

		if ($res) {
			show_json(1, '团购删除成功');
		}
		else {
			show_json(0, '团购删除失败，请重试');
		}
	}

	public function recommend()
	{
		global $_W;
		global $_GPC;
		$res = pdo_update('wlmerchant_groupon_activity', array('recommend' => 1), array('id' => intval($_GPC['id'])));

		if ($res) {
			show_json(1, '设置推荐成功');
		}
		else {
			show_json(0, '设置推荐失败，请重试');
		}
	}

	public function examine()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$flag = $_GPC['flag'];

		if ($flag == 1) {
			$res = Groupon::updateActive(array('status' => 1), array('id' => $id));
		}
		else {
			$res = Groupon::updateActive(array('status' => 6), array('id' => $id));
		}

		if ($res) {
			exit(json_encode(array('errno' => 0)));
		}
		else {
			exit(json_encode(array('errno' => 1)));
		}
	}

	public function tag()
	{
		include wl_template('grouponactive/listtag');
	}

	public function copygood()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$da = Groupon::getSingleActive($id, '*');
		unset($da['id']);
		unset($da['a']);
		unset($da['plugin']);
		$da['levelnum'] = $da['num'];
		$da['status'] = 4;
		$res = pdo_insert('wlmerchant_groupon_activity', $da);

		if ($res) {
			exit(json_encode(array('errno' => 0)));
		}
		else {
			exit(json_encode(array('errno' => 1)));
		}
	}

	public function changepv()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$type = $_GPC['type'];
		$newvalue = trim($_GPC['value']);

		if ($type == 1) {
			$res = pdo_update('wlmerchant_groupon_activity', array('pv' => $newvalue), array('id' => $id));
		}
		else if ($type == 2) {
			$res = pdo_update('wlmerchant_groupon_activity', array('sort' => $newvalue), array('id' => $id));
		}
		else {
			if ($type == 3) {
				$res = pdo_update('wlmerchant_groupon_activity', array('num' => $newvalue), array('id' => $id));
			}
		}

		if ($res) {
			show_json(1, '修改成功');
		}
		else {
			show_json(0, '修改失败，请重试');
		}
	}

	/**
     * Comment: 根据条件获取团购商品
     * Author: zzw
     * Date: 2019/7/11 13:50
     */
	public function groupList()
	{
		global $_W;
		global $_GPC;
		$where = ' a.aid = ' . $_W['aid'] . ' AND a.uniacid = ' . $_W['uniacid'];
		!empty($_GPC['name']) && ($where .= ' AND a.name LIKE \'%' . $_GPC['name'] . '%\' ');
		-1 < $_GPC['status'] && ($where .= ' AND a.status = ' . $_GPC['status'] . ' ');
		!empty($_GPC['goods_id']) && ($where .= ' AND a.id = ' . $_GPC['goods_id'] . ' ');
		!empty($_GPC['shop_name']) && ($where .= ' AND m.storename LIKE \'%' . $_GPC['shop_name'] . '%\' ');
		-1 < $_GPC['cate_id'] && ($where .= ' AND a.cateid = ' . $_GPC['cate_id'] . ' ');
		!empty($_GPC['shop_id']) && ($where .= ' AND a.sid = ' . $_GPC['shop_id'] . ' ');
		$order = ' a.sort DESC ,a.id DESC ';
		$page = $_GPC['page'] ? $_GPC['page'] : 1;
		$index = $_GPC['index'] ? $_GPC['index'] : 10;
		$start = $page * $index - $index;
		$limit = ' LIMIT ' . $start . ',' . $index;
		$field = 'a.id,a.thumb,a.name,a.starttime,a.endtime,a.status,a.pv,a.sort,a.num,a.recommend,m.storename,b.name as cate_name';
		$sql = 'SELECT ' . $field . ' FROM ' . tablename(PDO_NAME . 'groupon_activity') . ' a LEFT JOIN ' . tablename(PDO_NAME . 'groupon_category') . ' b ON a.cateid = b.id LEFT JOIN ' . tablename(PDO_NAME . 'merchantdata') . ' m ON a.sid = m.id';
		!empty($where) && ($sql .= ' WHERE ' . $where . ' ');
		$sql .= ' GROUP BY a.id ';
		!empty($order) && ($sql .= ' ORDER BY ' . $order . ' ');
		$total = count(pdo_fetchall(str_replace($field, 'a.id', $sql)));
		$data['page_num'] = ceil($total / $index);
		!empty($limit) && ($sql .= $limit);
		$data['list'] = pdo_fetchall($sql);
		$orderModel = new Order();

		foreach ($data['list'] as $k => &$v) {
			$v['thumb'] = tomedia($v['thumb']);
			$orderW = ' fkid = ' . $v['id'] . ' AND plugin = \'groupon\' AND status in ';
			$v['order_purchase'] = $orderModel->getPurchaseQuantity($orderW . ' (0,1,2,3,4,6,8,9) ') ?: 0;
			$v['order_payment'] = $orderModel->getPurchaseQuantity($orderW . ' (1,2,3,4,6,8,9) ') ?: 0;
			$v['order_used'] = $orderModel->getPurchaseQuantity($orderW . ' (2,3) ') ?: 0;
			$v['starttime'] = date('Y-m-d H:i:s', $v['starttime']);
			$v['endtime'] = date('Y-m-d H:i:s', $v['endtime']);
		}

		wl_json(1, '团购商品列表', $data);
	}

	/**
     * Comment: 获取团购分类列表
     * Author: zzw
     * Date: 2019/7/11 13:57
     */
	public function getClassList()
	{
		global $_W;
		global $_GPC;
		$where = ' uniacid = ' . $_W['uniacid'] . ' AND aid = ' . $_W['aid'] . ' ';
		$list = pdo_fetchall('SELECT id,name FROM ' . tablename(PDO_NAME . 'groupon_category') . (' WHERE ' . $where . ' ORDER BY sort DESC'));
		wl_json(1, '团购分类列表', $list);
	}

	/**
     * Comment: 修改团购商品的某个单项数据信息
     * Author: zzw
     * Date: 2019/7/12 15:20
     */
	public function updateInfo()
	{
		global $_W;
		global $_GPC;

		if (empty($_GPC['field'])) {
			show_json(0, '缺少参数：修改的字段名称');
		}

		$data[$_GPC['field']] = $_GPC['value'];
		$res = pdo_update(PDO_NAME . 'groupon_activity', $data, array('id' => $_GPC['id']));

		if ($res) {
			show_json(1, '修改成功');
		}
		else {
			show_json(0, '修改失败');
		}
	}
}

defined('IN_IA') || exit('Access Denied');

?>
