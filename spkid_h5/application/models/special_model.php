<?php

/**
* special_model
*/
class Special_model extends CI_Model
{
	private $_db;
	public function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
		$this->user_id = $this->session->userdata('user_id');
	}

	public function all_special_list($rush_id){
        $sql = "SELECT rush_index,image_before_url,campaign_id FROM " .$this->_db->dbprefix('rush_info').
               " WHERE rush_id=".$rush_id;
		$query = $this->_db->query($sql);
        return $query->row();
	}

	//获取极限抢购的商品
	public function get_special_product($rush_id){
		$sql = "SELECT pif.product_id,pif.product_name,pif.subhead,pif.promote_price,pif.shop_price,pgy.img_url FROM ".
				$this->_db->dbprefix('rush_product')." AS rpd LEFT JOIN ".
				$this->_db->dbprefix('product_info')." AS pif ON pif.product_id = rpd.product_id LEFT JOIN ".
				$this->_db->dbprefix('product_gallery')." AS pgy ON pgy.product_id = rpd.product_id WHERE rush_id=".$rush_id;
		$query = $this->_db->query($sql);
        return $query->result();
	}

	//获取 指定 现金券 活动
	public function release_row($release_id)
	{
		$sql = " SELECT vc.`campaign_id`,vc.`campaign_name`,vc.`start_date` start,vc.`end_date` end,vr.* FROM `ty_voucher_release` AS vr LEFT JOIN `ty_voucher_campaign` AS vc ON vc.`campaign_id` = vr.`campaign_id` WHERE vr.`release_id` = ".$release_id." AND vc.`campaign_status` = 1  AND vr.`release_status`=1 AND vc.`start_date` <= CURRENT_DATE() AND vc.`end_date` >= CURRENT_DATE()"; 
		$query = $this->_db->query($sql);
		return $query->row();
	}

	//获取限时抢购活动信息
	public function limit_sale($product_id){
		$sql = " SELECT * FROM ".$this->_db->dbprefix('front_campaign')." WHERE campaign_type = 3 AND is_use = 1 AND start_date <=NOW() AND end_date >=NOW() AND product_id = ".$product_id;
		$query = $this->_db->query($sql);
		return $query->row();
	}

	//获取限时抢购 的 商品数量 信息
	public function num_sale($product_id){
		$sql = " SELECT COUNT(id) num FROM ".$this->_db->dbprefix('front_sale')." WHERE product_id = ".$product_id." AND pay_status = 1 AND pay_date BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00') AND DATE_FORMAT(NOW(),'%Y-%m-%d 23:59:59')";
		$query = $this->_db->query($sql);
		return $query->row(); 
	}

	// 向限时抢购表中插入记录
	public function insert_sale($sale_data){
		$this->_db->insert('front_sale',$sale_data);
		return $this->_db->insert_id();
	}

	//当用户 抢购付款后，记录表修改已支付状态
	public function update_sale($order_id,$user_id)
	{
		$this->_db->update('front_sale',array('pay_status' => 1,'pay_date' => date('Y-m-d H:i:s')),array('order_id'=>$order_id,'user_id'=>$user_id));
	}

	//获取已抢购用户的id
	public function user_data(){
		$sql = " SELECT user_id FROM ".$this->_db->dbprefix('front_sale')." WHERE pay_status = 1 AND pay_date BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00') AND DATE_FORMAT(NOW(),'%Y-%m-%d 23:59:59')";
		$query = $this->_db->query($sql);
		return $query->result();
	}
}
