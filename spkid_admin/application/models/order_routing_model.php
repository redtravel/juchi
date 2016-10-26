<?php
/**
 * Description of purchase_log_model
 *
 */
class Order_routing_model extends CI_Model{
    //put your code here
    
    function __construct() {
        parent::__construct();
    }
    
    public function filter($filter)
    {
	    $query = $this->db->get_where('order_routing', $filter, 1);
	    return $query->row();
    }
    
    public function exists($routing,$routing_id)
    {
    	$sql = "SELECT routing_id FROM " . $this->db->dbprefix('order_routing') . 
				" WHERE source_id = ".$routing['source_id']." AND shipping_id = ".$routing['shipping_id']." AND pay_id = ".$routing['pay_id'];
		if(!empty($routing_id) && $routing_id > 0) {
			$sql .= " AND routing_id != ".$routing_id;
		}
		$sql .= " LIMIT 1";
	    $row = $this->db->query($sql)->row();
	    return (!empty($row)) ? $row->routing_id : 0;
    }
    
    public function check_in_use($routing_id)
    {
    	$sql = "SELECT a.order_id 
				FROM ".$this->db->dbprefix('order_info')." a
				LEFT JOIN ".$this->db->dbprefix('order_routing')." b ON a.source_id=b.source_id AND a.shipping_id=b.shipping_id AND a.pay_id=b.pay_id
				WHERE b.routing_id=".$routing_id.= " LIMIT 1";;
	    $row = $this->db->query($sql)->row();
	    return (!empty($row)) ? $row->order_id : 0;
    }
    
    public function order_routing_list ($filter)
	{
		$from =  " FROM ".$this->db->dbprefix('order_routing')." AS a ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['source_id']))
		{
			$where .= " AND a.source_id = ? ";
			$param[] = $filter['source_id'];
		}
		if (!empty($filter['shipping_id']))
		{
			$where .= " AND a.shipping_id = ? ";
			$param[] = $filter['shipping_id'];
		}
		if (!empty($filter['pay_id']))
		{
			$where .= " AND a.pay_id = ? ";
			$param[] = $filter['pay_id'];
		}
		
//		$filter['sort_by'] = empty($filter['sort_by']) ? ' a.routing_id DESC ' : trim($filter['sort_by']);
//		$filter['sort_order'] = empty($filter['sort_order']) ? '' : trim($filter['sort_order']);
		
		/*$sql = "SELECT COUNT(a.routing_id) AS total " . $from . $where;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->total;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}*/
		
		$sql =  "SELECT a.*,b.source_name,c.shipping_name,d.pay_name " .
				$from . 
				" LEFT JOIN ".$this->db->dbprefix('order_source')." b ON a.source_id=b.source_id" .
				" LEFT JOIN ".$this->db->dbprefix('shipping_info')." c ON a.shipping_id=c.shipping_id" .
				" LEFT JOIN ".$this->db->dbprefix('payment_info')." d ON a.pay_id=d.pay_id" .
				$where .
				" ORDER BY a.source_id, a.shipping_id, a.pay_id ";
				//" ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'];
			 	//" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		
		$result = array();
		if (!empty($list))
		{
			foreach ($list as $row)
			{
				if(!isset($result[$row->source_id])){
		            $result[$row->source_id] = array('source_name'=>$row->source_name,'shipping_list'=>array(),'span_count'=>0);
		        }
		        if(!isset($result[$row->source_id]['shipping_list'][$row->shipping_id])){
		            $result[$row->source_id]['shipping_list'][$row->shipping_id] = array('shipping_name'=>$row->shipping_name,'pay_list'=>array(),'span_count'=>0);
		        }
		        $result[$row->source_id]['shipping_list'][$row->shipping_id]['pay_list'][] = $row;
		        $result[$row->source_id]['shipping_list'][$row->shipping_id]['span_count'] += 1;
		        $result[$row->source_id]['span_count'] += 1;
			}
		}
		foreach($result as $key=>$val){
	    	$val['shipping_list'] = array_values($val['shipping_list']);
	    	$result[$key] = $val;
	    }
	    $result = array_values($result);
		//var_dump($result);die;
		return array('list' => $result, 'filter' => $filter);
	}
    
    public function insert ($data)
    {
	    $this->db->insert('order_routing', $data);
	    return $this->db->insert_id();
    }

    public function update ($data, $id)
    {
	    $this->db->update('order_routing', $data, array('routing_id' => $id));
    }

    public function del ($id)
    {
    	$this->db->delete('order_routing', array('routing_id' => $id));
    }
}
?>
