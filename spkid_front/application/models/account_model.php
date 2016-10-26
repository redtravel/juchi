<?php

/**
* Ad_model
*/
class Account_model extends CI_Model
{
	private $_db;
	public function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	// PC 获取 未使用 的现金券 信息 最近六个月
	public function user_voucher_unused($user_id){
		$sql = " SELECT vrc.`voucher_id`,vcp.`campaign_name`,vrc.`voucher_sn`,vrc.`voucher_amount`,vrc.`min_order`,vrc.`start_date`,vrc.`end_date` FROM `ty_voucher_record` AS vrc LEFT JOIN ty_voucher_campaign AS vcp ON vcp.`campaign_id` = vrc.`campaign_id` WHERE vrc.`voucher_status`= 0 AND vrc.`used_number` < vrc.`repeat_number` AND UNIX_TIMESTAMP(vrc.`start_date`) >= ".(time()-(3600*24*30*6))." AND UNIX_TIMESTAMP(vrc.`start_date`) <= ".time()." AND UNIX_TIMESTAMP(vrc.`end_date`) >= ".time()." AND vrc.`user_id` = ".$user_id." ORDER BY vrc.`end_date` DESC";
		$query = $this->_db->query($sql);
		return $query->result();	
	}

	// PC 获取 使用过的现金券 信息 最近六个月
	public function user_voucher_used($user_id){
		$sql = " SELECT vrc.`voucher_id`,vca.`campaign_name`,vrc.`voucher_sn`,vrc.`voucher_amount`,vrc.`min_order`,vrc.`start_date`,vrc.`end_date` FROM `ty_voucher_record` AS vrc LEFT JOIN `ty_voucher_campaign` AS vca ON vca.`campaign_id` = vrc.`campaign_id` WHERE (vrc.`voucher_status` = 1 OR vrc.`used_number` = vrc.`repeat_number`) AND UNIX_TIMESTAMP(vrc.`start_date`) >= ".(time()-(3600*24*30*6))." AND vrc.`user_id` = ".$user_id." ORDER BY vrc.`end_date` DESC";
		$query = $this->_db->query($sql);
		return $query->result();	
	}

	// PC 获取 未使用已过期的现金券 信息  最近一周
	public function user_voucher_expired($user_id){
		$sql = " SELECT vrc.`voucher_id`,vcp.`campaign_name`,vrc.`voucher_sn`,vrc.`voucher_amount`,vrc.`min_order`,vrc.`start_date`,vrc.`end_date` FROM `ty_voucher_record` AS vrc LEFT JOIN ty_voucher_campaign AS vcp ON vcp.`campaign_id` = vrc.`campaign_id` WHERE vrc.`voucher_status`= 0 AND vrc.`used_number` < vrc.`repeat_number` AND UNIX_TIMESTAMP(vrc.`end_date`) >= ".(time()-(3600*24*7))."  AND UNIX_TIMESTAMP(vrc.`end_date`) <= ".time()." AND vrc.`user_id` = ".$user_id." ORDER BY vrc.`end_date` DESC";
		$query = $this->_db->query($sql);
		return $query->result();	
	}

	//PC 查询指定优惠券信息(未使用)
	public function check_voucher($user_id,$voucher_id){
		$sql = " SELECT * FROM `ty_voucher_record` AS vrc WHERE vrc.`voucher_status`= 0 AND vrc.`used_number` < vrc.`repeat_number` AND UNIX_TIMESTAMP(vrc.`start_date`) <= ".time()." AND UNIX_TIMESTAMP(vrc.`end_date`) >= ".time()." AND vrc.`user_id` = ".$user_id." AND vrc.`voucher_id` = ".$voucher_id;
		$query = $this->_db->query($sql);
		return $query->row();
	}

	public function del_voucher($voucher_id){
		$this->_db->delete('voucher_record',array('voucher_id'=>$voucher_id));
	}

	//PC 获得 积分与余额 的信息
	/**
	  * change_type = 1 积分
	  * change_type = 0 余额
	*/
	public function integral_balance($user_id,$change_type,$page)
	{	
		$this->_db->select('change_code');
		$query = $this->_db->get_where('user_account_log_kind',array('change_type'=>$change_type));
		$account_kind = $query->result();
		foreach ($account_kind as $act) {
			$kind_data[] = $act->change_code; 
		}

		$this->_db->where('user_id',$user_id);
		$this->_db->where_in('change_code',$kind_data);

		if($page == 'recently'){
			$this->_db->where('UNIX_TIMESTAMP(`create_date`) >=',time()-(3600*24*90));
		}elseif($page == 'before'){
			$this->_db->where('UNIX_TIMESTAMP(`create_date`) <',time()-(3600*24*90));
			$this->_db->where('UNIX_TIMESTAMP(`create_date`) >',time()-2*(3600*24*90));
		}		

		$this->_db->order_by('create_date','desc');
		$query = $this->_db->get('user_account_log');
		// echo $this->_db->last_query();
        return $query->result();
	}

	//PC 获取 积分兑换现金券 活动
	public function voucher_campaign()
	{
		$sql = " SELECT vc.`campaign_id`,vc.`campaign_name`,vc.`start_date` start,vc.`end_date` end,vr.* FROM `ty_voucher_release` AS vr LEFT JOIN `ty_voucher_campaign` AS vc ON vc.`campaign_id` = vr.`campaign_id` WHERE vc.`campaign_type` = 'ex' AND vc.`campaign_status` = 1 AND vr.`release_status`=1 AND UNIX_TIMESTAMP(vc.`start_date`) <= ".time()." <= UNIX_TIMESTAMP(vc.`end_date`) ORDER BY create_date DESC"; 
		$query = $this->_db->query($sql);
		return $query->result();
	}

	//PC 获取 指定 现金券 活动
	public function release_row($release_id)
	{
		$sql = " SELECT vc.`campaign_id`,vc.`campaign_name`,vc.`start_date` start,vc.`end_date` end,vr.* FROM `ty_voucher_release` AS vr LEFT JOIN `ty_voucher_campaign` AS vc ON vc.`campaign_id` = vr.`campaign_id` WHERE vr.`release_id` = ".$release_id." AND vc.`campaign_type` = 'ex' AND vc.`campaign_status` = 1  AND vr.`release_status`=1 AND UNIX_TIMESTAMP(vc.`start_date`) <= ".time()." AND UNIX_TIMESTAMP(vc.`end_date`) >= ".time(); 
		$query = $this->_db->query($sql);
		return $query->row();
	}

	//PC 兑换积分
	public function insert_exchange_voucher($voucher_record)
	{	
		$this->_db->insert('voucher_record', $voucher_record);
		return $this->_db->insert_id();
	}

	//PC 更改积分
	public function update_exchange_voucher($user_id,$voucher_release_worth,$account_log)
	{	
		$sql = " UPDATE `ty_user_info` SET `pay_points` = `pay_points`- ".$voucher_release_worth." WHERE `user_id` = ".$user_id;
		$query = $this->_db->query($sql);

		$this->_db->insert('user_account_log',$account_log);
		return;
	}

}
