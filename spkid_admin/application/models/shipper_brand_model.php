<?php
#doc
#	classname:	Provider_brand_model
#	scope:		PUBLIC
#
#/doc

class Shipper_brand_model extends CI_Model
{

	public function filter($filter)
	{
		$query = $this->db->get_where('provider_brand', $filter, 1);
		return $query->row();
	}
	
	public function all_filter($filter)
	{
		$query = $this->db->get_where('provider_brand', $filter);
		return $query->result();
	}
	
	public function insert ($data)
	{
		$this->db->insert('provider_brand', $data);
		return $this->db->insert_id();
	}
	
	public function update ($data, $id)
	{
		$this->db->update('provider_brand', $data, array('id' => $id));
	}
	
	public function delete ($id)
	{
		$this->db->delete('provider_brand', array('id' => $id));
	}
	
	public function provider_brand_list($provider_id,$brand_id = 0){
	    $sql = "SELECT a.*,b.brand_name,b.brand_initial FROM ".$this->db->dbprefix('provider_brand')." AS a 
		    LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON a.brand_id = b.brand_id
		    WHERE a.provider_id = $provider_id";
	    if ($brand_id != 0)
	    {
		    $sql .= " AND a.brand_id = $brand_id ";
	    }
	    $query = $this->db->query($sql);
	    $list = $query->result();
	    $query->free_result();
	    return $list;
	}
	
	public function brand_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_brand')." AS b 
				  LEFT JOIN ".$this->db->dbprefix('product_flag')." AS f ON b.flag_id = f.flag_id";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['brand_id']))
		{
			$where .= " AND b.brand_id = ? ";
			$param[] = $filter['brand_id'];
		}else{
		    if (!empty($filter['brand_name']))
		    {
			    $where .= " AND b.brand_name LIKE ? ";
			    $param[] = '%' . $filter['brand_name'] . '%';
		    }
		    if (!empty($filter['brand_initial']))
		    {
			    $where .= " AND b.brand_initial = ? ";
			    $param[] = $filter['brand_initial'];
		    }
		}
		if (!empty($filter['skip_set']))
		{
			$provider_id = $filter['provider_id'];
			$where .= " AND NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('provider_brand')." AS pb WHERE pb.brand_id = b.brand_id  AND pb.provider_id = $provider_id) ";
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'b.brand_id' : trim($filter['sort_by']);
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
		$sql = "SELECT b.*, f.flag_name, f.flag_url "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

}
###
