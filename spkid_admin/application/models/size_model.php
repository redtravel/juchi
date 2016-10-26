<?php
#doc
#	classname:	Size_model
#	scope:		PUBLIC
#
#/doc

class Size_model extends CI_Model
{

	public function filter ($filter)
	{
		$query = $this->db->get_where('product_size', $filter, 1);
		return $query->row();
	}

	public function update ($data, $size_id)
	{
		$this->db->update('product_size', $data, array('size_id' => $size_id));
	}

	public function insert ($data)
	{
		$this->db->insert('product_size', $data);
		return $this->db->insert_id();
	}

	public function delete($size_id)
	{
		$this->db->delete('product_size', array('size_id'=>$size_id));
	}

	public function size_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_size')." AS s ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['size_name']))
		{
			$where .= " AND s.size_name LIKE ? ";
			$param[] = '%' . $filter['size_name'] . '%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 's.size_id' : trim($filter['sort_by']);
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
		$sql = "SELECT s.* "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function all_size ()
	{
		$query = $this->db->get('product_size');
		return $query->result();
	}

	public function filter_image($filter)
	{
		$query = $this->db->get_where('product_size_image', $filter, 1);
		return $query->row();
	}

	public function update_image ($data, $size_image_id)
	{
		$this->db->update('product_size_image', $data, array('size_image_id' => $size_image_id));
	}

	public function insert_image ($data)
	{
		$this->db->insert('product_size_image', $data);
		return $this->db->insert_id();
	}

	public function delete_image($size_image_id)
	{
		$this->db->delete('product_size_image', array('size_image_id'=>$size_image_id));
	}

	public function image_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_size_image')." AS s 
				LEFT JOIN ".$this->db->dbprefix('product_category')." AS c ON s.category_id = c.category_id
				LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON s.brand_id = b.brand_id ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['brand_id']))
		{
			$where .= " AND s.brand_id = ? ";
			$param[] = $filter['brand_id'];
		}

		if (!empty($filter['category_id']))
		{
			$where .= " AND s.category_id = ? ";
			$param[] = $filter['category_id'];
		}

		if (!empty($filter['sex']))
		{
			$where .= " AND s.sex = ? ";
			$param[] = $filter['sex'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 's.size_image_id' : trim($filter['sort_by']);
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
		$sql = "SELECT s.*, c.category_name, b.brand_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function all_image($filter=array())
	{
		$query = $this->db->get_where('product_size_image', $filter);
		return $query->result();
	}

	public function gen_size_sn(){
	     $sql =" SELECT size_sn FROM ty_product_size ORDER BY size_id DESC LIMIT 1";
	    $query = $this->db->query($sql);
	    $last_sn = $query->row();
	    if(empty($last_sn)){
		return "0001";
	    }else{
		$now_fix = intval($last_sn->size_sn)+1;
		if($now_fix >= 10000){
		    sys_msg("尺寸数量超出系统边界，请联系管理员",1);
		}
		return str_pad($now_fix,4,"0",STR_PAD_LEFT);
	    }
	}
		

}
###