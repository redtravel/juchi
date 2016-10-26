<?php
#doc
#	classname:	pament_model
#	scope:		PUBLIC
#
#/doc

class Order_advice_model extends CI_Model
{
	public function filter($filter)
	{
            $query = $this->db->get_where('ty_order_advice', $filter, 1);
            return $query->row();
	}
        
    public function query($filter){
        $count_sql = "select count(*) ct FROM ".$this->db->dbprefix('order_advice');
        $query = $this->db->query($count_sql);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
        //detail 
		$filter['sort_by'] = 'oa.advice_id';
		$filter['sort_order'] = 'desc';
		$filter = page_and_size($filter);
                
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}

        $sql = "SELECT oa.*, t.type_name, t.type_color, a.admin_name,if(oa.is_return = 1,oi.order_sn,if(oa.is_return = 2,ri.return_sn,'')) order_sn
                FROM ".$this->db->dbprefix('order_advice')." AS oa
                LEFT JOIN ".$this->db->dbprefix('order_advice_type')." AS t ON oa.type_id = t.type_id
                LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON oa.advice_admin = a.admin_id
                left join ".$this->db->dbprefix('order_info')." oi on oa.order_id = oi.order_id and oa.is_return = 1
                left join ".$this->db->dbprefix('order_return_info')." ri on oa.order_id = ri.return_id and oa.is_return = 2
                WHERE 1=1 ORDER BY ".$filter['sort_by']." ".$filter['sort_order']
                ." limit " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
        $query1 = $this->db->query($sql);
		$list = $query1->result();
		$query1->free_result();
		return array('list' => $list, 'filter' => $filter);
    }
}
###