<?php
#doc
#	classname:	Style_model
#	scope:		PUBLIC
#
#/doc

class Provider_fee_model extends CI_Model
{

	public function filter($filter)
	{
		$query = $this->db->get_where('provider_fee', $filter, 1);
		return $query->row();
	}
	public function query ($filter)
	{
        $from = " from ".$this->db->dbprefix('provider_fee')." f
                  left join ".$this->db->dbprefix('provider_fee_category')." fc
                    on f.category_id = fc.category_id
                  left join ".$this->db->dbprefix('admin_info')." a
                    on f.create_admin = a.admin_id
                  left join ".$this->db->dbprefix('admin_info')." a2
                    on f.check_admin = a2.admin_id
                  left join ".$this->db->dbprefix('product_brand')." b
                    on f.brand_id = b.brand_id
                  left join ".$this->db->dbprefix('product_provider')." p
                    on f.provider_id = p.provider_id";
		$where = " WHERE 1 ";
		$param = array();
		if (!empty($filter['category_id']))
		{
			$where .= " AND f.category_id = ? ";
			$param[] = $filter['category_id'];
		}
        
		if (!empty($filter['provider_id']))
		{
			$where .= " AND f.provider_id = ? ";
			$param[] = $filter['provider_id'];
        }
        if (!empty($filter['check_status'])){
            if ($filter['check_status']==1)
            {
                $where .= " AND f.check_date is not null ";
            }else{
                $where .= " AND f.check_date is null ";
            }
        }
		if (!empty($filter['check_date_start']))
		{
			$where .= " AND f.check_date >= ? ";
			$param[] = $filter['check_date_start'];
		}
        
		if (!empty($filter['check_date_end']))
		{
			$where .= " AND f.check_date < date_add(?, interval 1 day) ";
			$param[] = $filter['check_date_end'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'f.id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'ASC' : trim($filter['sort_order']);
        
		$sql = "SELECT COUNT(*) AS ct " . $from . $where;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "select f.*,fc.category_name,a.admin_name create_admin_name,a2.admin_name check_admin_name,
                p.provider_code,if(check_date is null,0,1) check_status,b.brand_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function insert ($data)
	{
		$this->db->insert('provider_fee', $data);
		return $this->db->insert_id();
	}
	
	public function update ($data, $id)
	{
		$this->db->update('provider_fee', $data, array('id' => $id));
	}
	
	public function delete ($id)
	{
		$this->db->delete('provider_fee', array('id' => $id));
	}
	public function all_fee_category($filter=array())
	{
		$query = $this->db->get_where('provider_fee_category',$filter);
		return $query->result();
	}

}
###