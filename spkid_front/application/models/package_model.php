<?php

/**
* 
*/
class Package_model extends CI_Model
{
	
	function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	public function filter($filter=array())
	{
		$query = $this->_db->get_where('package_info',$filter,1);
		return $query->result();
	}

	public function all_product($filter=array())
	{
		if (isset($filter['product_id']) && is_array($filter['product_id'])) {
			$this->_db->where_in('product_id', $filter['product_id']);
			unset($filter['product_id']);
		}
		$query = $this->_db->get_where('package_area_product',$filter);
		return $query->result();
	}

	public function all_area($filter=array())
	{
		$query = $this->db->get_where('package_area', $filter);
		return $query->result();
	}

	public function all_package($filter)
	{
		if (isset($filter['package_id']) && is_array($filter['package_id'])) {
			$this->_db->where_in('package_id', $filter['package_id']);
			unset($filter['package_id']);
		}
		$query = $this->_db->get_where('package_info',$filter);
		return $query->result();
	}

}