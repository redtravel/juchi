<?php
#doc
#	classname:	Brand_model
#	scope:		PUBLIC
#
#/doc

class Brand_model extends CI_Model
{

	public function filter($filter)
	{
		$query = $this->db->get_where('product_brand', $filter, 1);
		return $query->row();
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
	
	public function insert ($data)
	{
		$this->db->insert('product_brand', $data);
		return $this->db->insert_id();
	}
	
	public function update ($data, $brand_id)
	{
		$this->db->update('product_brand', $data, array('brand_id' => $brand_id));
	}
	
	public function delete ($brand_id)
	{
		$this->db->delete('product_brand', array('brand_id' => $brand_id));
	}

	public function all_brand($filter = array(),$id = '')
	{
		$this->db->order_by('sort_order','desc');

		if($id != ''){
        	$this->db->where('brand_id',$id); 
    	}
		$query = $this->db->get_where('product_brand',$filter);
		return $query->result();
	}

	// public function one_brand($id='')
	// {
	// 	$this->db->order_by('sort_order','desc');
	// 	$query = $this->db->get_where('product_brand',$filter);
	// 	return $query->result();

	// 	$sql = "select p.* from ty_product_brand as p where 1 ";

 //        if($id != ''){
 //        	$sql .=" and id=".$id; 
 //    	}
    	
 //        $sql .=" and name != '' order by id desc";
 //        $query = $this->db->query($sql);
 //        return $query->result();		
	// }
        
        /**
         * 供应商的品牌列表
         * @param type $provider_id
         * @return type
         */
        public function all_provider_brand($provider_id)
        {
            $sql = "select b.* from ty_provider_brand as pb
                    left join ty_product_brand as b on pb.brand_id = b.brand_id
                    where pb.provider_id=?";
            $query = $this->db->query($sql, array($provider_id));
            return $query->result();
        }

}
###