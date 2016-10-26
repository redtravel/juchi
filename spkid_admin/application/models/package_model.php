<?php

/**
* 
*/
class Package_model extends CI_Model
{
	
	public function filter($filter)
	{
		$query = $this->db->get_where('package_info', $filter);
		return $query->row();
	}

	public function package_list($filter)
	{
		$from = " FROM ".$this->db->dbprefix('package_info')." AS p ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['package_name']))
		{
			$where .= " AND p.package_name LIKE ? ";
			$param[] = '%' . $filter['package_name'] . '%';
		}
		if (isset($filter['package_status']) && $filter['package_status']!=-1) {
			$where .= " AND p.package_status = ? ";
			$param[] = $filter['package_status'];
		}

		if (isset($filter['package_type']) && $filter['package_type']!=-1) {
			$where .= " AND p.package_type = ? ";
			$param[] = $filter['package_type'];
		}

		if (isset($filter['is_recommend']) && $filter['is_recommend']!=-1) {
			$where .= " AND p.is_recommend = ? ";
			$param[] = $filter['is_recommend'];
		}

		if (!empty($filter['start_time']))
		{
			$where .= " AND p.start_time >= ? ";
			$param[] = $filter['start_time'];
		}

		if (!empty($filter['end_time']))
		{
			$where .= " AND p.end_time <= ? ";
			$param[] = $filter['end_time'];
		}

		if (!empty($filter['product_sn']))
		{
			$where .= " AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('product_info')." AS prd, ".$this->db->dbprefix('product_area_product')." AS pp  WHERE pp.package_id = p.package_id AND pp.product_id = prd.product_id AND prd.product_sn = ? ) ";
			$param[] = $filter['product_sn'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'p.package_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

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
		$sql = "SELECT p.* "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function insert($update)
	{
		$this->db->insert('package_info', $update);
		return $this->db->insert_id();
	}

	public function update($update, $package_id)
	{
		$this->db->update('package_info', $update, array('package_id'=>$package_id));
	}

	public function delete($package_id)
	{
		$this->db->delete('package_info', array('package_id'=>$package_id));
	}

	public function all_package($filter)
	{
		if (isset($filter['package_id']) && is_array($filter['package_id'])) {
			$this->db->where_in('package_id', $filter['package_id']);
			unset($filter['package_id']);
		}
		$query = $this->db->get_where('package_info',$filter);
		return $query->result();
	}

	public function all_area($filter = array())
	{
		$query = $this->db->get_where('package_area', $filter);
		return $query->result();
	}

	public function delete_area_where($filter=array())
	{
		$this->db->delete('package_area', $filter);
	}

	public function delete_area_product_where($filter=array())
	{
		$this->db->delete('package_area_product', $filter);
	}

	public function insert_area($update)
	{
		$this->db->insert('package_area', $update);
		return $this->db->insert_id();
	}

	public function filter_area_product($filter)
	{
		$query = $this->db->get_where('package_area_product', $filter,1);
		return $query->row();
	}

	public function filter_area($filter)
	{
		$query = $this->db->get_where('package_area',$filter,1);
		return $query->row();
	}

	public function delete_area($area_id)
	{
		$this->db->delete('package_area', array('area_id'=>$area_id));
	}

	public function update_area($update, $area_id)
	{
		$this->db->update('package_area', $update, array('area_id'=>$area_id));
	}

	public function package_product($package_id)
	{
		$sql = "SELECT pp.*, p.product_name, p.product_sn, p.provider_productcode, p.goods_cess,
				c.color_name AS default_color_name,cat.category_name,b.brand_name
				FROM ".$this->db->dbprefix('package_area_product')." AS pp
				LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON pp.product_id = p.product_id
				LEFT JOIN ".$this->db->dbprefix('product_category')." AS cat ON p.category_id = cat.category_id
				LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id
				LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON pp.default_color_id = c.color_id
				WHERE pp.package_id = ?";
		$query = $this->db->query($sql, array($package_id));
		return $query->result();
	}

	public function product_search($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_info')." AS p ";
		$where = " WHERE p.is_audit = 1 AND 
					NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('package_area_product')." AS pp 
					WHERE (pp.package_id='{$filter['package_id']}' AND pp.product_id=p.product_id)) ";
		$param = array();

		if (!empty($filter['product_sn']))
		{
			$where .= " AND p.product_sn LIKE ? ";
			$param[] = '%' . $filter['product_sn'] . '%';
		}

		if (!empty($filter['product_name']))
		{
			$where .= " AND p.product_name LIKE ? ";
			$param[] = '%' . $filter['product_name'] . '%';
		}

		if (!empty($filter['provider_productcode']))
		{
			$where .= " AND p.provider_productcode LIKE ? ";
			$param[] = '%' . $filter['provider_productcode'] . '%';
		}

		if (!empty($filter['style_id']))
		{
			$where .= " AND p.style_id = ? ";
			$param[] = $filter['style_id'];
		}

		if (!empty($filter['season_id']))
		{
			$where .= " AND p.season_id = ? ";
			$param[] = $filter['season_id'];
		}

		if (!empty($filter['product_sex']))
		{
			$where .= " AND p.product_sex = ? ";
			$param[] = $filter['product_sex'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'p.product_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

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
		$sql = "SELECT p.product_id,p.product_sn,p.product_name,p.provider_productcode,p.shop_price "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function all_product($filter)
	{
		$query = $this->db->get_where('package_area_product', $filter);
		return $query->result();
	}

	public function insert_product($update)
	{
		$this->db->insert('package_area_product', $update);
		return $this->db->insert_id();
	}

	public function filter_product($filter)
	{
		$query = $this->db->get_where('package_area_product', $filter, 1);
		return $query->row();
	}

	public function delete_product($rec_id)
	{
		$this->db->delete('package_area_product', array('rec_id'=>$rec_id));
	}

	public function update_product($update, $rec_id)
	{
		$this->db->update('package_area_product', $update, array('rec_id'=>$rec_id));
	}

	public function lock_package($package_id)
	{
		$query = $this->db->query("SELECT * FROM ".$this->db->dbprefix('package_info')." WHERE package_id = ? FOR UPDATE", array($package_id));
		return $query->row();
	}


}