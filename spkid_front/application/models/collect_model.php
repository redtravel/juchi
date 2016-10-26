<?php

/**
* Ad_model
*/
class Collect_model extends CI_Model
{
	private $_db;
	public function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	//查询 产品和课程的信息
	public function collect_list($user_id)
	{
		$sql = " SELECT fcp.*,pf.`is_promote`,pf.`subhead`, pf.`market_price`, pf.`promote_start_date`, pf.`promote_end_date`,pf.`product_desc`, pf.`product_name`,pf.`shop_price`,pf.`is_onsale`,pf.`product_desc_additional`,pf.`package_name`,pg.`img_url` FROM " .$this->_db->dbprefix('front_collect_product')." AS fcp ";
		$sql .= " LEFT JOIN ".$this->_db->dbprefix('product_info')." AS pf ON pf.`product_id`=fcp.`product_id`";
		$sql .= " LEFT JOIN ".$this->_db->dbprefix('product_gallery')." AS pg ON pg.`product_id`=fcp.`product_id`";
		$sql .= " WHERE fcp.`user_id` = '".$user_id."'  AND (fcp.`product_type` = 0 OR fcp.`product_type` = 3) ORDER BY fcp.`create_date` DESC";
		$query = $this->_db->query($sql);

        return $query->result();
	}

	
	public function delete_collect($rec_id)
    {
        $this->_db->delete('front_collect_product', array('rec_id' => $rec_id));
        return $this->_db->affected_rows();
    }
}
