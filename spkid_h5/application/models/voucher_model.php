<?php
/**
* Voucher_model
*/
class Voucher_model extends CI_Model
{	
	private $_db;
	function __construct (&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}
	public function filter($filter){
		$query = $this->_db->get_where('voucher_record',$filter,1);
		return $query->row();		
	}
	
	public function filter_campaign($filter){
		$query = $this->_db->get_where('voucher_campaign',$filter,1);
		return $query->row();	
	}
	
	public function lock_release($release_id){
		$sql="SELECT * FROM ".$this->_db->dbprefix('voucher_release')." WHERE release_id = ? LIMIT 1 FOR UPDATE";
		$query = $this->_db->query($sql,array($release_id));
		return $query->row();
	}
	
	public function update_release($update,$release_id){
		$this->_db->update('voucher_release',$update,array('release_id'=>$release_id));			
	}
	
	public function all_exchange_release(){
		$sql="SELECT r.release_id,r.voucher_name,r.voucher_amount,r.min_order,r.worth,r.logo
			FROM ".$this->_db->dbprefix('voucher_release')." AS r
			LEFT JOIN ".$this->_db->dbprefix('voucher_campaign')." AS c ON r.campaign_id=c.campaign_id
			WHERE c.campaign_type='ex' AND c.campaign_status=1 #and c.start_date<='{$this->time}' and c.end_date>='{$this->time}'
			AND r.release_status=1
			ORDER BY r.voucher_amount";
		$query = $this->_db->query($sql);
		return $query->result();
	}
	
	public function insert_voucher($voucher){

		$voucher_des = $this->get_voucher();  //生成现金券号
        if(!empty($voucher_des)){
            $voucher_num = $voucher_des->voucher_des;
        }else{
            $voucher_num = getVoucherDes();
        }
        // voucher_sn = LPAD(CAST(FLOOR(RAND()*1000000000000) AS CHAR(12)),12,'0'),

		$sql = "insert into  ".$this->_db->dbprefix('voucher_record')." set
                voucher_sn = '{$voucher_num}',
				campaign_id='{$voucher['campaign_id']}',
				release_id='{$voucher['release_id']}',
				user_id='{$voucher['user_id']}',
				voucher_status='{$voucher['voucher_status']}',
				repeat_number='{$voucher['repeat_number']}',
				used_number='{$voucher['used_number']}',
				start_date='{$voucher['start_date']}',
				end_date='{$voucher['end_date']}',
				voucher_amount='{$voucher['voucher_amount']}',
				min_order='{$voucher['min_order']}',
				create_admin='{$voucher['create_admin']}',
				create_date='{$voucher['create_date']}'
                ";
		while (true)
        {
			$this->_db->query($sql);
			$err_no = $this->_db->_error_number();
			if ($err_no == '1062') continue;
            if ($err_no == '0') break;
			$this->_db->trans_rollback();
            sys_msg('生成订单失败', 1);
        }
		return $this->_db->insert_id();
	}
	
	//  新用户注册 送现金券
	public function release_register_voucher ($user_id)
	{
		$campaign_ids=$this->config->item('register_voucher_campaign_ids'); // 活动ID

		if(!$campaign_ids) return TRUE;
		foreach( $campaign_ids as $campaign_id)
		{

			$campaign=$this->filter_campaign(array('campaign_id'=>$campaign_id));
			if($campaign->campaign_type!='auto' || $campaign->campaign_status!=1 || $campaign->start_date>$this->time || $campaign->end_date<$this->time)  continue;

			$release_ids=$this->lock_release_v(array('campaign_id'=>$campaign_id));

			if(isset($release_ids) && !empty($release_ids)){
				foreach ($release_ids as $release) {
					if($release->release_status!=1) continue;
					//发放现金券
					$voucher=array(
						'campaign_id' => $release->campaign_id,
						'release_id' => $release->release_id,
						'user_id' => $user_id,
						'voucher_status' => 0,
						'repeat_number' => 1,
						'used_number' => 0,
						'start_date' => $this->time,
						'end_date' => date_change($this->time,'P'.$release->expire_days.'D'),
						'voucher_amount' => $release->voucher_amount,
						'min_order'=>$release->min_order,
						'create_admin' => 0,
						'create_date' =>$this->time
					);

					$voucher_id = $this->insert_voucher($voucher);	
					$this->update_release(array('voucher_count'=>$release->voucher_count+1),$release->release_id);
				}
			}
		}
	}

	public function lock_release_v($filter){
		$query = $this->_db->get_where('voucher_release',$filter);
		return $query->result();
	}
	
	//  获取 已经生成的 可用的 现金券账号 
	public function get_voucher(){
		$t_sql = "SELECT * FROM ya_voucher_log WHERE voucher_status = 0 ORDER BY voucher_id ASC LIMIT 1";
        $result = $this->_db->query($t_sql)->row();
        if(!empty($result)){
	        $vsql = "UPDATE ya_voucher_log SET edit_time = NOW(), voucher_status = 1 WHERE voucher_id = ".$result->voucher_id;
	        if(!$this->_db->query($vsql)) {
	            $this->_db->query("ROLLBACK");
	        }
	    }else{
	        $result = '';
	    }
	    return $result;
	}


	// 获取限时抢购的 现金券
	public function all_special_list($campaign_id){
		$sql = " SELECT vre.release_id,vre.voucher_amount,vre.min_order,vca.start_date,vca.end_date FROM ".$this->_db->dbprefix('voucher_release')." AS vre LEFT JOIN ".$this->_db->dbprefix('voucher_campaign')." AS vca ON vre.campaign_id = vca.campaign_id WHERE vca.campaign_status = 1 AND vre.release_status = 1 AND vca.start_date <= NOW() AND vca.end_date >= NOW() AND vre.campaign_id = ".$campaign_id;
		$query = $this->_db->query($sql);
        return $query->result();
	}

	public function is_special_row($release_id,$user_id){
		$sql = " SELECT * FROM ty_voucher_record AS vr  
				 WHERE release_id = ".$release_id." AND user_id = ".$user_id."
				 AND (voucher_status = 0  OR voucher_status=1 AND  EXISTS(
				 SELECT 1 FROM ty_order_info AS oi 
				 LEFT JOIN ty_order_payment AS op ON op.`order_id`=oi.`order_id`
				 WHERE  vr.`voucher_sn`=op.`payment_account` AND oi.`pay_status`!=1 AND oi.`user_id`= ".$user_id."
				 )) 
				";
		$query = $this->_db->query($sql);
		return $query->row();
	}
	
}