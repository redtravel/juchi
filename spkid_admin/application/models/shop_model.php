<?php
/**
 * 店铺 
 * @author:sean
 * @date:2013-02-20
 */
class Shop_model extends CI_Model
{
    /**
     * 店铺列表
     * @param all是否显示所有店铺
     */
    function shop_list($filter,$all=SHOP_STATUS_ALL)
    {
        $from = " FROM ".$this->db_r->dbprefix('shop')." AS s
                  left join ".$this->db_r->dbprefix('admin_info')." as a
                  on s.create_admin=a.admin_id";
		$where = " WHERE 1 ";
		$param = array();
		$filter['sort_by'] = empty($filter['sort_by']) ? 's.shop_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'ASC' : trim($filter['sort_order']);
		
		// 要显示什么状态的店铺
		if( $all === SHOP_STATUS_ACTIVE || $all === SHOP_STATUS_DISABLE){ $where .= " AND shop_status=".$all; }

		$sql = "SELECT COUNT(*) AS ct " . $from . $where;
		$query = $this->db_r->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "SELECT s.shop_id,s.shop_name,s.shop_sn,s.is_cod,s.single_order,s.shop_status,
            s.shop_shipping,s.create_admin,s.create_date,s.update_admin,s.update_date ,realname as admin_name"
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db_r->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
    }

    function filter($filter)
    {
        $query=$this->db_r->get_where('shop',$filter);
        return $query->row();
    }
    
    public function update ($data,$shop_id)
	{
		$this->db->update('shop', $data, array('shop_id' => $shop_id));
	}

	public function insert ($data)
	{
		$this->db->insert('shop', $data);
		return $this->db->insert_id();
	}
	
	public function all_shop($filter = array()){
	     $query=$this->db_r->get_where('shop',$filter);
	     return $query->result();
	}

}

