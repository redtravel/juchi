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
		$sql = " SELECT vc.`campaign_id`,vc.`campaign_name`,vc.`start_date` start,vc.`end_date` end,vr.* FROM `ty_voucher_release` AS vr LEFT JOIN `ty_voucher_campaign` AS vc ON vc.`campaign_id` = vr.`campaign_id` WHERE vr.`release_id` = ".$release_id." AND vc.`campaign_status` = 1  AND vr.`release_status`=1 AND vc.`start_date` <= NOW() AND vc.`end_date` >= NOW()"; 
		$query = $this->_db->query($sql);
		return $query->row();
	}

}
