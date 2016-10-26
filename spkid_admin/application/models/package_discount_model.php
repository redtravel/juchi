<?php

/**
* 
*/
class Package_discount_model extends CI_Model
{
	
	public function filter($filter)
	{
		$query = $this->db->get_where('package_discount_info', $filter);
		return $query->row();
	}

	public function package_discount_list($filter)
	{
		$from = " FROM ".$this->db->dbprefix('package_discount_info')." AS p ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['pag_dis_name']))
		{
			$where .= " AND p.pag_dis_name LIKE ? ";
			$param[] = '%' . $filter['pag_dis_name'] . '%';
		}
		if (isset($filter['pag_dis_status']) && $filter['pag_dis_status']!=-1) {
			$where .= " AND p.pag_dis_status = ? ";
			$param[] = $filter['pag_dis_status'];
		}

		if (isset($filter['pag_dis_type']) && $filter['pag_dis_type']!=-1) {
			$where .= " AND p.pag_dis_type = ? ";
			$param[] = $filter['pag_dis_type'];
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
			$where .= " AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('product_info')." AS prd, ".$this->db->dbprefix('product_area_product')." AS pp  WHERE pp.package_id = p.pag_dis_id AND pp.product_id = prd.product_id AND prd.product_sn = ? ) ";
			$param[] = $filter['product_sn'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'p.pag_dis_id' : trim($filter['sort_by']);
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
		$this->db->insert('package_discount_info', $update);
		return $this->db->insert_id();
	}

	public function update($update, $pag_dis_id)
	{
		$this->db->update('package_discount_info', $update, array('pag_dis_id'=>$pag_dis_id));
	}

	public function delete($pag_dis_id)
	{
		$this->db->delete('package_discount_info', array('pag_dis_id'=>$pag_dis_id));
	}

	public function all_package($filter)
	{
		if (isset($filter['pag_dis_id']) && is_array($filter['pag_dis_id'])) {
			$this->db->where_in('pag_dis_id', $filter['pag_dis_id']);
			unset($filter['pag_dis_id']);
		}
		$query = $this->db->get_where('package_discount_info',$filter);
		return $query->result();
	}

	public function delete_area_where($filter=array())
	{
		$this->db->delete('package_area', $filter);
	}

	public function delete_discount_product_where($filter=array())
	{
		$this->db->delete('package_discount_product', $filter);
	}

	public function package_discount_product($pag_dis_id,$dis_pro_type)
	{
		$sql = "SELECT pp.*, p.product_name, p.product_sn, p.provider_productcode,p.shop_price,sub.sub_id,si.size_name
				FROM ".$this->db->dbprefix('package_discount_product')." AS pp
				LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON pp.product_id = p.product_id
				LEFT JOIN ".$this->db->dbprefix('product_sub')." AS sub ON p.product_id = sub.product_id
				LEFT JOIN ".$this->db->dbprefix('product_size')." AS si ON sub.size_id = si.size_id
				WHERE pp.pag_dis_id = ? AND pp.dis_pro_type = ?";
		$query = $this->db->query($sql, array($pag_dis_id,$dis_pro_type));
		return $query->result();
	}

	public function product_search($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_info')." AS p LEFT JOIN ".$this->db->dbprefix('product_sub')." AS sub ON p.product_id = sub.product_id
				LEFT JOIN ".$this->db->dbprefix('product_size')." AS si ON sub.size_id = si.size_id ";
		$where = " WHERE p.is_audit = 1 AND 
					NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('package_discount_product')." AS pp 
					WHERE (pp.pag_dis_id='{$filter['pag_dis_id']}'  AND pp.product_id=p.product_id)) ";
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
		$sql = "SELECT p.product_id,p.product_sn,p.product_name,p.provider_productcode,p.shop_price,si.size_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function all_product($filter)
	{
		$query = $this->db->get_where('package_discount_product', $filter);
		echo $this->db->last_query();
		return $query->result();
	}

	public function insert_product($update)
	{
		$this->db->insert('package_discount_product', $update);
		return $this->db->insert_id();
	}

	public function filter_discount_product($filter)
	{
		$query = $this->db->get_where('package_discount_product', $filter,1);
		return $query->row();
	}

	public function delete_product($dis_pro_id)
	{
		$this->db->delete('package_discount_product', array('dis_pro_id'=>$dis_pro_id));
	}

	public function update_product($update, $dis_pro_id)
	{
		$this->db->update('package_discount_product', $update, array('dis_pro_id'=>$dis_pro_id));
	}

	public function lock_package($pag_dis_id)
	{
		$query = $this->db->query("SELECT * FROM ".$this->db->dbprefix('package_discount_info')." WHERE pag_dis_id = ? FOR UPDATE", array($pag_dis_id));
		return $query->row();
	}


	public function search_product($product_ids)
	{
		$sql = "SELECT p.product_id, p.product_name, p.product_sn, sub.sub_id, p.provider_productcode, si.size_name
				FROM ".$this->db->dbprefix('product_info')." AS p 
				LEFT JOIN ".$this->db->dbprefix('product_sub')." AS sub ON p.product_id = sub.product_id
				LEFT JOIN ".$this->db->dbprefix('product_size')." AS si ON sub.size_id = si.size_id
				WHERE p.is_audit = 1 AND p.product_id IN ($product_ids)";
		$query = $this->db->query($sql);
		return $query->result();
	}

}