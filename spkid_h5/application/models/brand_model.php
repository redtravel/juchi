<?php

/**
* Ad_model
*/
class Brand_model extends CI_Model
{
	private $_db;
	public function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	public function all_brand_list(){
        $sql = " SELECT brand_initial,GROUP_CONCAT(brand_id, '=', brand_name) brand_name 
            FROM " .$this->_db->dbprefix('product_brand').
            " WHERE is_use=1".
            " GROUP BY brand_initial ";
		$query = $this->_db->query($sql);
        return $query->result_array();
	}

	public function one_brand($brand_id){
		$sql = " SELECT * FROM " .$this->_db->dbprefix('product_brand')." WHERE brand_id=".$brand_id;
		$query = $this->_db->query($sql);
        return $query->row();
	}


}
