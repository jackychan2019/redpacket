<?php
//dezend by http://www.sucaihuo.com/
class Task_WeliamController
{
	public function lists()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$list1 = Jkactive::getslides($uniacid);
		$data = $list1[0];
		$pager = $list1[1];
		
		//var_dump($data);
		//exit;
			$arr_task=['1'=>'推荐绑定手机会员数','2'=>'推荐成为分销会员数','3'=>'推荐开通一卡通会员数','4'=>'个人交易笔数','5'=>'个人结算佣金数'];
			$arr_condition=['1'=>'所有人','2'=>'分销商','3'=>'一卡通会员'];
			$arr_reward=['1'=>'积分','2'=>'余额','3'=>'平台优惠券'];
			
		foreach($data as &$val){
			
					$val['task_name']=$arr_task[$val['type']];
					$val['condition_name']=$arr_condition[$val['condition']];
					$val['reward_name']=$arr_reward[$val['reward_type']];	
		            $val['begin']=date( "Y-m-d H:i:s",$val['begin']);
					$val['end']=date( "Y-m-d H:i:s",$val['end']);
					$val['updatetime']=date( "Y-m-d H:i:s",$val['updatetime']);
		}
			
		include wl_template('task/list');
	}
	
	
	public function add()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		$temp = $_GPC['data'];
		
		if($temp){
			$temp['begin']=strtotime($temp['begin']);
			$temp['end']=strtotime($temp['end']);
			$temp['status']=1;
			$temp['updatetime']=time();
			//var_dump($temp);exit;
			if(!empty($id)){				
				pdo_update('wlmerchant_jk_task',$temp,array('id'=>$id));
				wl_message('操作成功!', web_url('jkactive/Task/lists'), 'success');
			}else{				
				pdo_insert('wlmerchant_jk_task',$temp);
				wl_message('操作成功!', web_url('jkactive/Task/lists'), 'success');
			}
			
		}else{
			if(!empty($id)){
				$data=pdo_get('wlmerchant_jk_task',array('id'=>$id));
				//var_dump($data);//exit;
				$arr_task=['1'=>'推荐绑定手机会员数','2'=>'推荐成为分销会员数','3'=>'推荐开通一卡通会员数','4'=>'个人交易笔数','5'=>'个人结算佣金数'];
				$arr_condition=['1'=>'所有人','2'=>'分销商','3'=>'一卡通会员'];
				$arr_reward=['1'=>'积分','2'=>'余额','3'=>'平台优惠券'];
				
					$data['task_name']=$arr_task[$data['type']];
					$data['condition_name']=$arr_condition[$data['condition']];
					$data['reward_name']=$arr_reward[$data['reward_type']];												
			}
			
			//var_dump($data);exit;
			
		}


		$conpons=pdo_getall('wlmerchant_couponlist',array('uniacid'=>$_W['uniacid'],'status'=>1));
		foreach ($conpons as &$conpon){
			$conpon['name']=$conpon['title'];
		}
		
	
		include wl_template('task/add');
	}

	public function operating()
	{
		global $_W;
		global $_GPC;
		$did = $_GPC['did'];
		$eid = $_GPC['id'];
		$temp = $_GPC['data'];

		if (!empty($did)) {
			pdo_delete(PDO_NAME . 'pocket_slide', array('id' => $did));
			wl_message('删除数据成功!', web_url('pocket/Slide/lists'), 'success');
		}

		if (!empty($eid)) {
			$data = Util::getSingelData('*', PDO_NAME . 'pocket_slide', array('id' => $eid));
		}

		if ($temp) {
			$temp['uniacid'] = $_W['uniacid'];
			$temp['aid'] = $_W['aid'];

			if ($temp['id']) {
				pdo_update(PDO_NAME . 'pocket_slide', $temp, array('id' => $temp['id']));
			}
			else {
				pdo_insert(PDO_NAME . 'pocket_slide', $temp);
			}

			wl_message('操作成功!', web_url('pocket/Slide/lists'), 'success');
		}

		include wl_template('pocket/slideadd');
	}
}

defined('IN_IA') || exit('Access Denied');

?>
