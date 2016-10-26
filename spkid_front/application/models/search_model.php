<?php
/**
* 
*/
class Search_model extends CI_Model
{
	private $_db;
	public function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	// 搜索页热搜的5个产品
	public function all_hot($filter=array())
    {
    	$this->db->limit('5');
    	$this->db->order_by('sort_order','desc');
    	$query = $this->db->get_where('front_hot_word',$filter);
    	$result = array();
    	$result['list'] = $query->result_array();
    	$result['cnt'] = $query->num_rows();
    	$query->free_result();
    	return $result;
    }
	
}
