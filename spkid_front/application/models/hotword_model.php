<?php
class Hotword_model extends CI_Model
{
    public function all($filter=array())
    {
    	$this->db->order_by('sort_order','desc');
    	$query = $this->db->get_where('front_hot_word',$filter);
    	$result = array();
    	$result['list'] = $query->result_array();
    	$result['cnt'] = $query->num_rows();
    	$query->free_result();
    	return $result;
    }
}
?>
