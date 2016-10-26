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

	//获取 可用现金券 信息/数量
	public function user_voucher_num($user_id){

		$sql = " SELECT vrc.`voucher_sn`,vrc.`voucher_amount`,vrc.`min_order`,vrc.`voucher_status`,vrc.`repeat_number`,vrc.`used_number` FROM `ty_voucher_record` AS vrc WHERE vrc.`voucher_status`= 0 AND vrc.`used_number` < vrc.`repeat_number` AND vrc.`start_date` <= NOW() AND vrc.`end_date` >= NOW() AND vrc.`user_id` = ".$user_id;
		$query = $this->_db->query($sql);
		return $query->result();	
	}

	//获取 使用过的现金券 信息
	public function user_voucher_all($user_id){
		$sql = " SELECT vrc.`voucher_sn`,vrc.`voucher_amount`,vrc.`min_order`,vrc.`voucher_status`,oif.`order_id`,oif.`order_sn`,oif.`genre_id`,vrc.`repeat_number`,vrc.`used_number` FROM `ty_voucher_record` AS vrc 
				LEFT JOIN `ty_voucher_release` AS vre ON vre.`release_id` = vrc.`release_id` 
				LEFT JOIN `ty_voucher_campaign` AS vca ON vca.`campaign_id` = vrc.`campaign_id` 
				LEFT JOIN `ty_order_payment` AS opm ON opm.`payment_account` = vrc.`voucher_sn` 
				LEFT JOIN `ty_payment_info` AS pmi ON pmi.`pay_id` = opm.`pay_id` 				
				LEFT JOIN `ty_order_info` AS oif ON oif.`order_id` = opm.`order_id` 				
				WHERE (vrc.`voucher_status`= 1 or vrc.`used_number` = vrc.`repeat_number`) AND vre.`release_status` = 1 AND pmi.`pay_code`= 'coupon' AND vca.`campaign_status` = 1 AND vrc.`user_id` = ".$user_id;
		$query = $this->_db->query($sql);
		return $query->result();	
	}

	// 获得 积分与余额 的信息
	public function integral_balance($user_id,$change_type)
	{	
		$this->_db->select('change_code');
		$query = $this->_db->get_where('user_account_log_kind',array('change_type'=>$change_type));
		$account_kind = $query->result();
		foreach ($account_kind as $act) {
			$kind_data[] = $act->change_code; 
		}

		$this->_db->where('user_id',$user_id);
		$this->_db->where_in('change_code',$kind_data);
		$this->_db->order_by('create_date','desc');
		$query = $this->_db->get('user_account_log');
        return $query->result();
	}

	//获取 积分兑换现金券 活动
	public function voucher_campaign()
	{
		$sql = " SELECT vc.`campaign_id`,vc.`campaign_name`,vc.`start_date` start,vc.`end_date` end,vr.* FROM `ty_voucher_release` AS vr LEFT JOIN `ty_voucher_campaign` AS vc ON vc.`campaign_id` = vr.`campaign_id` WHERE vc.`campaign_type` = 'ex' AND vc.`campaign_status` = 1 AND vr.`release_status`=1 AND UNIX_TIMESTAMP(vc.`start_date`) <= ".time()." <= UNIX_TIMESTAMP(vc.`end_date`) ORDER BY create_date DESC"; 
		$query = $this->_db->query($sql);
		return $query->result();
	}

	//获取 指定 现金券 活动
	public function release_row($release_id)
	{
		$sql = " SELECT vc.`campaign_id`,vc.`campaign_name`,vc.`start_date` start,vc.`end_date` end,vr.* FROM `ty_voucher_release` AS vr LEFT JOIN `ty_voucher_campaign` AS vc ON vc.`campaign_id` = vr.`campaign_id` WHERE vr.`release_id` = ".$release_id." AND vc.`campaign_type` = 'ex' AND vc.`campaign_status` = 1  AND vr.`release_status`=1 AND UNIX_TIMESTAMP(vc.`start_date`) <= ".time()." AND UNIX_TIMESTAMP(vc.`end_date`) >= ".time(); 
		$query = $this->_db->query($sql);
		return $query->row();
	}

	//兑换积分
	public function insert_exchange_voucher($voucher_record)
	{	
		$this->_db->insert('voucher_record', $voucher_record);
		return $this->_db->insert_id();
	}

	// 更改积分
	public function update_exchange_voucher($user_id,$voucher_release_worth,$account_log)
	{	
		$sql = " UPDATE `ty_user_info` SET `pay_points` = `pay_points`- ".$voucher_release_worth." WHERE `user_id` = ".$user_id;
		$query = $this->_db->query($sql);

		$this->_db->insert('user_account_log',$account_log);
		return;
	}

}
