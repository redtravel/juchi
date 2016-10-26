<?php
/**
 * Description of purchase_log_model
 * TYPE:
 * 5-出库复核取消
 */
class purchase_log_model extends CI_Model{
    //put your code here
    
    function __construct() {
        parent::__construct();
    }
    
    public function filter($filter)
    {
	    $query = $this->db->get_where('purchase_log', $filter, 1);
	    return $query->row();
    }
    
    public function find_page ($filter)
	{
		$from =  " FROM ".$this->db->dbprefix('purchase_log')." AS a ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['start_time']))
		{
			$where .= " AND a.create_date > ? ";
			$param[] = $filter['start_time'];
		}
		if (!empty($filter['end_time']))
		{
			$where .= " AND a.create_date < ? ";
			$param[] = $filter['end_time'];
		}
		
		$filter['sort_by'] = empty($filter['sort_by']) ? ' a.id DESC ' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? '' : trim($filter['sort_order']);
		
		$sql = "SELECT COUNT(a.id) AS total " . $from . $where;
		
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->total;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		
		$sql =  "SELECT a.*,b.admin_name as create_user,c.box_code " .
				$from . 
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." b ON b.admin_id = a.create_admin" .
				" LEFT JOIN ".$this->db->dbprefix('purchase_box_main')." c ON c.box_id = a.related_id AND a.related_type in (0,1)" .
				$where .
				" ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'].
			 	" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		
		$query = $this->db->query($sql, $param);
		$return = $query->result();
		$query->free_result();
		
		return array('list' => $return, 'filter' => $filter);
	}
    
    public function insert ($data)
    {
	    $this->db->insert('purchase_log', $data);
	    return $this->db->insert_id();
    }

    public function update ($data, $id)
    {
	    $this->db->update('purchase_log', $data, array('id' => $id));
    }
}
?>
