<?php
#doc
#	classname:	Depot_model
#	scope:		PUBLIC
#
#/doc

class Depot_model extends CI_Model
{

	public function filter_depot ($filter)
	{
		$query = $this->db->get_where('depot_info', $filter, 1);
		return $query->row();
	}

	public function filter_depot_all ($filter)
	{
		$query = $this->db->get_where('depot_info', $filter);
		return $query->result_array();
	}

	public function update_depot ($data, $depot_id)
	{
		$this->db->update('depot_info', $data, array('depot_id' => $depot_id));
	}

	public function insert_depot ($data)
	{
		$this->db->insert('depot_info', $data);
		return $this->db->insert_id();
	}

	public function delete_depot ($depot_id)
	{
		$this->db->delete('depot_info', array('depot_id' => $depot_id));
		return $this->db->affected_rows();
	}

	public function filter_location ($filter)
	{
		$query = $this->db->get_where('location_info', $filter, 1);
		return $query->row();
	}

	public function update_location ($data, $location_id)
	{
		$this->db->update('location_info', $data, array('location_id' => $location_id));
	}

	public function insert_location ($data)
	{
		$this->db->insert('location_info', $data);
		return $this->db->insert_id();
	}

	public function delete_location ($location_id)
	{
		$this->db->delete('location_info', array('location_id' => $location_id));
		return $this->db->affected_rows();
	}

	public function depot_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('depot_info')." AS a LEFT JOIN "
                       .$this->db->dbprefix('product_cooperation')." c ON c.cooperation_id = a.cooperation_id ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['depot_name']))
		{
			$where .= " AND a.depot_name LIKE ? ";
			$param[] = '%' . $filter['depot_name'] . '%';
			//$param[] = '%' . $filter['depot_name'] . '%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.depot_id' : trim($filter['sort_by']);
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
		$sql = "SELECT a.*, c.cooperation_name, IF(EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('location_info')." b WHERE b.depot_id = a.depot_id),1,0) AS in_use "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function location_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('location_info')." AS a LEFT JOIN  ".$this->db->dbprefix('depot_info')." b ON b.depot_id = a.depot_id ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['location_name']))
		{
			$where .= " AND a.location_name LIKE ? OR CONCAT(a.location_code1,'-',a.location_code2,'-',a.location_code3,'-',a.location_code4,'-',a.location_code5) LIKE ? ";
			$param[] = '%' . $filter['location_name'] . '%';
			$param[] = '%' . $filter['location_name'] . '%';
		}

		if (!empty($filter['depot_id']))
		{
			$where .= " AND a.depot_id = ? ";
			$param[] = $filter['depot_id'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.location_id' : trim($filter['sort_by']);
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
		$sql = "SELECT a.*,b.depot_name,IF(EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('transaction_info')." b WHERE b.location_id = a.location_id),1,0) AS in_use "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function is_depot_in_use ($depot_id)
	{
		if ($this->filter_location(array('depot_id'=>$depot_id)))
		{
			return true;
		}
		return false;
	}

	public function is_location_in_use ($location_id)
	{
		$sql = 'SELECT count(*) as count FROM ' . $this->db->dbprefix('transaction_info') . " WHERE location_id = ? ";
		$param = array();
		$param[] = $location_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		if($row->count)
		{
			return true;
		}
		return false;
	}

	public function sel_depot_list ($flag=0)
	{
		$sql = "SELECT a.depot_id,a.depot_name FROM ".$this->db->dbprefix('depot_info')." a WHERE a.is_use = 1 AND EXISTS (SELECT 'X' FROM ".$this->db->dbprefix('location_info')." b WHERE b.depot_id = a.depot_id AND b.is_use = 1)";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		$rs = array();
		if($flag == 1)
		{
			$rs[0] = "请选择仓库";
		}
		foreach ($list as $row)
		{
			$rs[$row->depot_id] = $row->depot_name;
		}
		return $rs;
	}

	public function sel_depot_list_all ($flag=0)
	{
		$sql = "SELECT a.depot_id,a.depot_name FROM ".$this->db->dbprefix('depot_info')." a WHERE a.is_use = 1 ";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		$rs = array();
		if($flag == 1)
		{
			$rs[0] = "请选择仓库";
		}
		foreach ($list as $row)
		{
			$rs[$row->depot_id] = $row->depot_name;
		}
		return $rs;
	}

	public function sel_location_list ($depot_id=0)
	{
		$sql = "SELECT a.location_id,a.location_code1,a.location_code2,a.location_code3,a.location_code4,a.location_code5,a.location_name FROM ".$this->db->dbprefix('location_info')." a WHERE a.is_use = 1";
		if ($depot_id > 0)
		{
			$sql .= " AND a.depot_id = '".$depot_id."'";
		}
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		$rs = array();
		foreach ($list as $row)
		{
			$tmp = $row->location_code1.'-'.$row->location_code2.'-'.$row->location_code3.'-'.$row->location_code4.'-'.$row->location_code5;
			$rs[$row->location_id."::::".$tmp] = $tmp;
		}
		return $rs;
	}

	public function sel_purchase_type_list ()
	{
		$sql = "SELECT a.cooperation_id,a.cooperation_name FROM ".$this->db->dbprefix('product_cooperation')." a WHERE a.is_use = 1";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		$rs = array();
		$rs[0] = "请选择";
		foreach ($list as $row)
		{
			$rs[$row->cooperation_id] = $row->cooperation_name;
		}
		return $rs;
	}

	public function sel_depot_out_type_list ()
	{
		$sql = "SELECT a.depot_type_id,a.depot_type_code,a.depot_type_name FROM ".$this->db->dbprefix('depot_iotype')." a WHERE a.is_use = 1 AND a.depot_type_out = 1";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		$rs = array();
		$rs[0] = "请选择";
		foreach ($list as $row)
		{
			$rs[$row->depot_type_id] = $row->depot_type_name."[".$row->depot_type_code."]";
		}
		return $rs;
	}

	public function sel_depot_in_type_list ()
	{
		$sql = "SELECT a.depot_type_id,a.depot_type_code,a.depot_type_name FROM ".$this->db->dbprefix('depot_iotype')." a WHERE a.is_use = 1 AND a.depot_type_out = 0";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		$rs = array();
		$rs[0] = "请选择";
		foreach ($list as $row)
		{
			$rs[$row->depot_type_id] = $row->depot_type_name."[".$row->depot_type_code."]";
		}
		return $rs;
	}

	public function sel_provider_list ()
	{
		$sql = "SELECT a.provider_id,a.provider_code,a.provider_name FROM ".$this->db->dbprefix('product_provider')." a WHERE a.is_use = 1 ORDER BY provider_code ASC";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		$rs = array();
		$rs[0] = "请选择";
		foreach ($list as $row)
		{
			$rs[$row->provider_id] = $row->provider_code.'-'.$row->provider_name;
		}
		return $rs;
	}

	public function sel_provider_name_list ()
	{
		$sql = "SELECT a.provider_id,a.provider_code,a.provider_name FROM ".$this->db->dbprefix('product_provider')." a WHERE a.is_use = 1 ORDER BY provider_code ASC";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		$rs = array();
		$rs[0] = "请选择";
		foreach ($list as $row)
		{
			$rs[$row->provider_id] = $row->provider_code.' - '.$row->provider_name;
		}
		return $rs;
	}

	public function sel_brand_list ()
	{
		$sql = "SELECT a.brand_id,a.brand_name FROM ".$this->db->dbprefix('product_brand')." a WHERE a.is_use = 1";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		$rs = array();
		$rs[0] = "请选择";
		foreach ($list as $row)
		{
			$rs[$row->brand_id] = $row->brand_name;
		}
		return $rs;
	}

	public function sel_batch_list ($provider_id=0)
	{
		$sql = "SELECT a.batch_id,a.batch_code FROM ".$this->db->dbprefix('purchase_batch')." a WHERE 1 = 1";
		$param = array();
		if ($provider_id > 0)
		{
			$sql .= " AND a.provider_id = ? ";
			$param[] = $provider_id;
		}
		$query = $this->db->query($sql,$param);
		$list = $query->result();
		$query->free_result();
		$rs = array();
		$rs[0] = "请选择";
		foreach ($list as $row)
		{
			$rs[$row->batch_id] = $row->batch_code;
		}
		return $rs;
	}

	public function depot_type_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('depot_iotype')." AS a ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['depot_type_name']))
		{
			$where .= " AND (a.depot_type_name LIKE ? OR a.depot_type_code LIKE ? )";
			$param[] = '%' . $filter['depot_type_name'] . '%';
			$param[] = '%' . $filter['depot_type_name'] . '%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.depot_type_id' : trim($filter['sort_by']);
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
		$sql = "SELECT a.*,IF(EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('depot_in_main')." b WHERE b.depot_in_type = a.depot_type_id) " .
				"OR EXISTS(SELECT 'Y' FROM ".$this->db->dbprefix('depot_out_main')." c WHERE c.depot_out_type = a.depot_type_id),1,0) AS in_use "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function check_break_purchase ($purchase_code)
	{
		$sql = "SELECT COUNT(*) AS tot FROM ".$this->db->dbprefix('depot_in_main')." WHERE order_sn = '".$purchase_code."' LIMIT 1";
		$query = $this->db->query($sql);
		$row = $query->row();
		$tot = $row->tot;
		$query->free_result();
		if ($tot > 0) return FALSE;
		return TRUE;
	}

	public function check_avail_depot_type ($filter)
	{
		$where1 = " WHERE 1 ";
		$param = array();
		if (!empty($filter['depot_type_id']))
		{
			$where1 .= " AND depot_type_id <> ? ";
			$param[] = $filter['depot_type_id'];
		}
		$where = "";
		if (!empty($filter['depot_type_name']))
		{
			$where .= empty($where)?" depot_type_name = ? ":" OR depot_type_name = ? ";
			$param[] = $filter['depot_type_name'];
		}
		if (!empty($filter['depot_type_code']))
		{
			$where .= empty($where)?" depot_type_code = ? ":" OR depot_type_code = ? ";
			$param[] = $filter['depot_type_code'];
		}
		if (!empty($filter['depot_type_special']))
		{
			$where .= empty($where)?" depot_type_special = ? ":" OR depot_type_special = ? ";
			$param[] = $filter['depot_type_special'];
		}
		if (!empty($where))
		{
			$where = " AND (".$where.")";
		}
		$where = $where1.$where;

		$sql = "SELECT COUNT(*) AS tot FROM ".$this->db->dbprefix('depot_iotype').$where." LIMIT 1";
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$tot = $row->tot;
		$query->free_result();

		if ($tot > 0) return FALSE;
		if ($filter['depot_type_special'] > 0 && $filter['depot_type_out'] == 1) return FALSE;
		return TRUE;
	}

	public function filter_depot_type ($filter)
	{
		$query = $this->db->get_where('depot_iotype', $filter, 1);
		return $query->row();
	}

	public function update_depot_type ($data, $depot_type_id)
	{
		$this->db->update('depot_iotype', $data, array('depot_type_id' => $depot_type_id));
	}

	public function insert_depot_type ($data)
	{
		$this->db->insert('depot_iotype', $data);
		return $this->db->insert_id();
	}

	public function delete_depot_type ($depot_type_id)
	{
		$this->db->delete('depot_iotype', array('depot_type_id' => $depot_type_id));
		return $this->db->affected_rows();
	}

	public function is_depot_type_in_use ($depot_type_id)
	{
		$sql = 'SELECT count(*) as count1 FROM ' . $this->db->dbprefix('depot_in_main') . " WHERE depot_in_type = ? ";
		$param = array();
		$param[] = $depot_type_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$count1 = $row->count1;
		$query->free_result();
		$sql = 'SELECT count(*) as count2 FROM ' . $this->db->dbprefix('depot_out_main') . " WHERE depot_out_type = ? ";
		$param = array();
		$param[] = $depot_type_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$count2 = $row->count2;
		$query->free_result();
		if($count1 + $count2 > 0)
		{
			return true;
		}
		return false;
	}

	public function purchase_list ($filter)
	{
		$from = " FROM ".$this->db_r->dbprefix('purchase_main')." AS a ".
				" LEFT JOIN ".$this->db_r->dbprefix('purchase_batch')." AS pb ON pb.batch_id = a.batch_id ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['purchase_code']))
		{
			$where .= " AND a.purchase_code LIKE ? ";
			$param[] = '%' . $filter['purchase_code'] . '%';
		}

		if (!empty($filter['purchase_provider']))
		{
			$where .= " AND a.purchase_provider = ? ";
			$param[] = $filter['purchase_provider'];
		}

		if (!empty($filter['purchase_type']))
		{
			$where .= " AND a.purchase_type = ? ";
			$param[] = $filter['purchase_type'];
		}
                
		if (!empty($filter['purchase_status']))
		{
			if ($filter['purchase_status'] == 1)
			{
				$where .= " AND a.purchase_check_admin = 0 ";
			} elseif ($filter['purchase_status'] == 2)
			{
				$where .= " AND a.purchase_check_admin > 0 AND a.purchase_break != 1 ";
			} elseif ($filter['purchase_status'] == 3)
			{
				$where .= " AND a.purchase_break = 1 ";
			} elseif ($filter['purchase_status'] == 4)
			{
				$where .= " AND a.purchase_finished = 1 ";
			}
			//$where .= " AND a.purchase_code LIKE ? ";
			//$param[] = '%' . $filter['purchase_status'] . '%';
		}

		if (!empty($filter['provider_goods']))
		{
			$where .= " AND EXISTS(SELECT 'X' FROM ".$this->db_r->dbprefix('purchase_sub')." s, ".$this->db_r->dbprefix('product_info')." v WHERE a.purchase_id = s.purchase_id AND s.product_id = v.product_id AND (v.product_name LIKE ? OR v.product_sn LIKE ? )) ";
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
		}
		if (!empty($filter['purchase_batch']))
		{
		    $where .= " AND a.batch_id = ?";
		    $param[] = $filter['purchase_batch'];
		}
		
		if (isset($filter['is_consign']))
		{
			$where .= " AND pb.is_consign = ? ";
			$param[] = $filter['is_consign'];
		}
		
		if (!empty($filter['brand_id']))
		{
			$where .= " AND a.purchase_brand = ? ";
			$param[] = $filter['brand_id'];
		}
		
		if (!empty($filter['provider_productcode']))
		{
			$where .= " AND EXISTS(
							SELECT 1 FROM ".$this->db->dbprefix('purchase_sub')." s 
							LEFT JOIN ".$this->db->dbprefix('product_info')." v ON s.product_id = v.product_id 
							WHERE a.purchase_id = s.purchase_id AND v.provider_productcode = ?
						) ";
			$param[] = $filter['provider_productcode'];
		}
                
                if (isset($filter['overtime5']) && $filter['overtime5'] == 1)
                {
                        $where .= " AND a.purchase_check_date <> '0000-00-00 00:00:00' AND a.purchase_break_date = '0000-00-00 00:00:00' AND a.purchase_check_date <= DATE_SUB(CURDATE(), INTERVAL 5 DAY)  ";
                }
                
                

		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.purchase_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

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

		$sql = "SELECT a.*,b.cooperation_name AS purchase_type_name,c.provider_name,c.provider_code,d.brand_name,e.admin_name AS create_name,f.admin_name AS purchase_break_name," .
				"g.admin_name AS purchase_check_name,h.admin_name as lock_name,pb.is_consign " .
				$from .
				" LEFT JOIN ".$this->db_r->dbprefix('product_cooperation')." b ON b.cooperation_id = a.purchase_type" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_provider')." c ON c.provider_id = a.purchase_provider" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_brand')." d ON d.brand_id = a.purchase_brand" .
				" LEFT JOIN ".$this->db_r->dbprefix('admin_info')." e ON e.admin_id = a.create_admin" .
				" LEFT JOIN ".$this->db_r->dbprefix('admin_info')." f ON f.admin_id = a.purchase_break_admin" .
				" LEFT JOIN ".$this->db_r->dbprefix('admin_info')." g ON g.admin_id = a.purchase_check_admin" .
				" LEFT JOIN ".$this->db_r->dbprefix('admin_info')." h ON h.admin_id = a.lock_admin" .
                                $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'] 
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db_r->query($sql, $param);
		$list = $query->result();
		if (!empty($list))
		{
			foreach ($list as $key=>$purchase_info)
			{

				if ($purchase_info->purchase_check_admin == 0)
				{
					$purchase_info->purchase_status = 1; //未审核
					$purchase_info->purchase_status_name = '未审核';
				} elseif ($purchase_info->purchase_check_admin > 0)
				{
					$purchase_info->purchase_status = 2; //已审核
					$purchase_info->purchase_status_name = '已审核';
				}
				if ($purchase_info->purchase_break == 1)
				{
					$purchase_info->purchase_status = 3; //已终止
					$purchase_info->purchase_status_name = '已终止';
				} elseif ($purchase_info->purchase_finished == 1)
				{
					$purchase_info->purchase_status = 4; //完成
					$purchase_info->purchase_status_name = '已完成';
				}
				if ($purchase_info->lock_admin > 0)
				{
					$purchase_info->purchase_status_name = "已锁,".$purchase_info->purchase_status_name;
				} else
				{
					$purchase_info->purchase_status_name = "未锁,".$purchase_info->purchase_status_name;
				}

				$list[$key] = $purchase_info;
			}
		}

		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function filter_purchase ($filter)
	{
		$query = $this->db->get_where('purchase_main', $filter, 1);
		return $query->row();
	}

	public function get_purchase_info ($purchase_id)
	{
		$param = array();
		$sql = "SELECT a.*,b.cooperation_name AS purchase_type_name,c.provider_name,d.brand_name,e.admin_name AS create_admin_name,f.admin_name AS purchase_break_admin_name,g.admin_name AS purchase_check_admin_name" .
				" FROM ".$this->db->dbprefix('purchase_main')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_cooperation')." b ON b.cooperation_id = a.purchase_type" .
				" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = a.purchase_provider" .
				" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = a.purchase_brand" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." e ON c.admin_id = a.create_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." f ON c.admin_id = a.purchase_break_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." g ON c.admin_id = a.purchase_check_admin" .
				" WHERE 1 ";
		if (empty($purchase_id))
		{
			$purchase_id = 0;
		}
		$sql .= " AND a.purchase_id = ? LIMIT 1";
		$param[] = $purchase_id;

		$query = $this->db->query($sql, $param);
		return $query->row();
	}

        /**
         * 获取采购单商品
	 * 2013-04-03采购单编辑页面，展示商品列表，修改完工数获取逻辑，从之前的depot_in获取方式修改为直接从box表获取 modified：lkp
         * @param type $purchase_id
         * @param type $order_sn
         * @return type 
         */
	public function purchase_products ($purchase_id,$order_sn = '')
	{
//		$param = array();
//		$sql = "SELECT a.product_id,a.color_id,a.size_id,SUM(a.product_number) AS num FROM ".$this->db_r->dbprefix('depot_in_sub')." a" .
//				" LEFT JOIN ".$this->db_r->dbprefix('depot_in_main')." b ON a.depot_in_id = b.depot_in_id" .
//				" WHERE b.order_id = '".$purchase_id."' AND b.order_sn = '".$order_sn."' GROUP BY a.product_id,a.color_id,a.size_id ";
//		$query = $this->db_r->query($sql);
//		$list_arr = $query->result_array();
//		$num_array = array();
//		if (!empty($list_arr))
//		{
//			foreach ($list_arr as $item)
//			{
//				$num_array[$item['product_id'].'-'.$item['color_id'].'-'.$item['size_id']] = empty($item['num'])?0:$item['num'];
//			}
//		}

		$sql = "SELECT a.*,b.product_sn,c.provider_name,c.provider_code,b.provider_productcode,b.is_audit,b.is_onsale,b.is_stop,b.product_name as b_product_name," .
				" b.shop_price,b.promote_price,b.unit_name, IF(pc.consign_price > 0, pc.consign_price, pc.cost_price) AS cost_price," .
				" c.provider_code,d.brand_name,e.color_name,e.color_sn,f.size_name,f.size_sn,s.provider_barcode,SUM(ps.over_num) as finish_product_number,ps.production_batch" .
				" FROM ".$this->db_r->dbprefix('purchase_sub')." a" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_info')." b ON b.product_id = a.product_id" .
                                " LEFT JOIN ".$this->db_r->dbprefix('purchase_main')." pm ON pm.purchase_id = a.purchase_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_provider')." c ON c.provider_id = pm.purchase_provider" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_sub')." s ON s.product_id = a.product_id AND s.color_id = a.color_id AND s.size_id = a.size_id " .
                                " LEFT JOIN ".$this->db_r->dbprefix('product_cost')." pc ON pm.`batch_id` = pc.`batch_id` AND a.`product_id` = pc.`product_id` " .
				"LEFT JOIN ".$this->db_r->dbprefix('purchase_box_main')." pb ON pb.purchase_code=pm.purchase_code" .
				" LEFT JOIN ".$this->db_r->dbprefix('purchase_box_sub')." ps ON ps.product_id = a.product_id AND ps.color_id = a.color_id AND ps.size_id = a.size_id AND ps.box_id = pb.box_id" .
				" WHERE a.purchase_id = ? GROUP BY a.product_id,a.color_id,a.size_id";
                
		$param[] = $purchase_id;

		$query = $this->db_r->query($sql, $param);
		$list = $query->result();
//		if (!empty($list))
//		{
//			foreach ($list as $key=>$value)
//			{
//				$value->finish_product_number = isset($num_array[$value->product_id.'-'.$value->color_id.'-'.$value->size_id])?$num_array[$value->product_id.'-'.$value->color_id.'-'.$value->size_id]:0;
//				$list[$key] = $value;
//			}
//		}
		$list = $this->format_product_list($list);
		return $list;
	}

	public function query_products_all($filter)
	{
		$from = " FROM ".$this->db_r->dbprefix('product_sub')." AS a ";
		$where = " WHERE 1 AND b.is_audit = 1 AND b.is_stop = 0 AND pm.purchase_id = $filter[purchase_id]"; //商品已审核，未停止订货
		$param = array();

		if (!empty($filter['provider_goods']))
		{
			$where .= " AND b.product_sn LIKE ? OR b.product_name LIKE ? OR b.provider_productcode LIKE ? ";
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
		}

		if (!empty($filter['brand_id']))
		{
			$where .= " AND b.brand_id = ? ";
			$param[] = $filter['brand_id'];
		}

		if (!empty($filter['provider_id']))
		{
			$where .= " AND pc.provider_id = ? ";
			$param[] = $filter['provider_id'];
		}

		if (!empty($filter['purchase_type']))
		{
			$where .= " AND c.provider_cooperation = ? ";
			$param[] = $filter['purchase_type'];
		}

		if (!empty($filter['purchase_status']))
		{
			if ($filter['purchase_status'] == 1)
			{
				$where .= " AND a.is_on_sale = 1 ";
			} elseif ($filter['purchase_status'] == 2)
			{
				$where .= " AND a.is_on_sale = 0 ";
			}
		}

		$filter['sort_by'] = 'b.brand_id ASC,b.product_id ASC,a.color_id ASC,a.size_id ASC ';

		$sql = "SELECT a.sub_id " .
				 $from .
				" LEFT JOIN ".$this->db_r->dbprefix('product_info')." b ON b.product_id = a.product_id" .
                                " LEFT JOIN ".$this->db_r->dbprefix('product_cost')." pc ON b.product_id = pc.product_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_provider')." c ON c.provider_id = pc.provider_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('purchase_main')." pm pm.batch_id = pc.batch_id" .
				 $where . " ORDER BY " . $filter['sort_by'];
		$query = $this->db_r->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return $list;
	}

	public function query_products ($filter)
	{
		$from = " FROM ".$this->db_r->dbprefix('product_sub')." AS a ";
		$where = " WHERE 1 AND b.is_audit = 1 AND b.is_stop = 0 AND pm.purchase_id = $filter[purchase_id]"; //商品已审核，未停止订货
		$param = array();

		if (!empty($filter['provider_goods']))
		{
			$where .= " AND ( b.product_sn LIKE ? OR b.product_name LIKE ? OR b.provider_productcode LIKE ? ) ";
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
		}

		if (!empty($filter['brand_id']))
		{
			$where .= " AND b.brand_id = ? ";
			$param[] = $filter['brand_id'];
		}

		if (!empty($filter['provider_id']))
		{
			$where .= " AND pc.provider_id = ? ";
			$param[] = $filter['provider_id'];
		}

		if (!empty($filter['purchase_type']))
		{
			$where .= " AND c.provider_cooperation = ? ";
			$param[] = $filter['purchase_type'];
		}

		if (!empty($filter['purchase_status']))
		{
			if ($filter['purchase_status'] == 1)
			{
				$where .= " AND a.is_on_sale = 1 ";
			} elseif ($filter['purchase_status'] == 2)
			{
				$where .= " AND a.is_on_sale = 0 ";
			}
		}

		if (!empty($filter['with_not']))
		{
			$where .= " AND NOT EXISTS(SELECT 'X' FROM ".$this->db_r->dbprefix('purchase_sub')." s WHERE s.purchase_id = ? AND s.product_id = a.product_id AND s.color_id = a.color_id AND s.size_id = a.size_id) ";
			$param[] = $filter['with_not'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'b.brand_id ASC,b.product_id ASC,a.color_id ASC,a.size_id ASC ' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? '' : trim($filter['sort_order']);

		$sql = "SELECT COUNT(*) AS ct " . $from .
				" LEFT JOIN ".$this->db_r->dbprefix('product_info')." b ON b.product_id = a.product_id" .
                                " LEFT JOIN ".$this->db_r->dbprefix('product_cost')." pc ON pc.product_id = a.product_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_provider')." c ON c.provider_id = pc.provider_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_size')." f ON f.size_id = a.size_id" . 
				" LEFT JOIN ".$this->db_r->dbprefix('purchase_main')." pm ON pm.batch_id = pc.batch_id" . $where;
		$query = $this->db_r->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "SELECT a.*,b.product_sn,b.product_name,b.provider_productcode,b.is_audit,b.is_stop,b.market_price," .
				" b.shop_price,b.promote_price," .
				" c.provider_name,d.brand_name,e.color_name,e.color_sn,f.size_name,f.size_sn " .
				 $from .
				" LEFT JOIN ".$this->db_r->dbprefix('product_info')." b ON b.product_id = a.product_id" .
                                " LEFT JOIN ".$this->db_r->dbprefix('product_cost')." pc ON pc.product_id = a.product_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_provider')." c ON c.provider_id = pc.provider_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" LEFT JOIN ".$this->db_r->dbprefix('purchase_main')." pm ON pm.batch_id = pc.batch_id" . 
				 $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'].
				 " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size']; 
				$query = $this->db_r->query($sql, $param);
		$list = $query->result();
		$list = $this->format_product_list($list);
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function format_product_list($list)
	{
		if (!empty($list))
		{
			foreach ($list as $key => $item)
			{
//				if ($item->consign_type == 0)
//				{
//					$item->provider_price = $item->cost_price;
//				} elseif ($item->consign_type == 1)
//				{
//					$item->provider_price = $item->consign_price;
//				} elseif ($item->consign_type == 2)
//				{
//					$item->provider_price = $item->shop_price * (1+$item->consign_rate);
//				}
				if (isset($item->is_on_sale))
				{
					$item->status_name = ($item->is_on_sale == 1)? '上架':'下架';
				}

				$list[$key] = $item;
			}
		}
		return $list;
	}

	public function update_purchase ($data, $purchase_id)
	{
		$this->db->update('purchase_main', $data, array('purchase_id' => $purchase_id));
	}

	public function insert_purchase ($data)
	{
		$this->db->insert('purchase_main', $data);
		return $this->db->insert_id();
	}

	public function delete_purchase ($purchase_id)
	{
		$this->db->delete('purchase_main', array('purchase_id' => $purchase_id));
		return $this->db->affected_rows();
	}

	public function update_purchase_product ($data, $where_arr)
	{
		$this->db->update('purchase_sub', $data, $where_arr);
	}

	public function insert_purchase_product ($data)
	{
		$this->db->insert('purchase_sub', $data);
		return $this->db->insert_id();
	}

	public function delete_purchase_product ($where_arr)
	{
		$this->db->delete('purchase_sub', $where_arr);
		return $this->db->affected_rows();
	}

	public function get_purchase_code()
	{
		return 'CG'.date('YmdHis');
	}

	public function del_purchase_product ($purchase_sub_id,$purchase_id)
	{
		$rs = $this->delete_purchase_product(array('purchase_id'=>$purchase_id,'purchase_sub_id'=>$purchase_sub_id));
		return $rs;
	}

	public function update_purchase_product_x ($purchase_sub_id,$purchase_id,$product_number)
	{
		$sql = "UPDATE ".$this->db->dbprefix('purchase_sub')." " .
				"SET product_number = '".$product_number."',product_amount = shop_price * ".$product_number." " .
				"WHERE purchase_id = '".$purchase_id."' AND purchase_sub_id = '".$purchase_sub_id."'";
		$query = $this->db->query($sql);
		return $this->db->affected_rows();
	}

	public function update_purchase_total ($purchase_id)
	{
		//$sql = "SELECT SUM(product_number) AS product_number_t,SUM(product_amount) AS product_amount_t,SUM(product_finished_number) AS product_finished_number_t " .
		//		"FROM ".$this->db->dbprefix('purchase_sub')." WHERE purchase_id = ? ";
                $sql = "SELECT SUM(ps.product_number) AS product_number_t, SUM(ps.product_finished_number) AS product_finished_number_t, SUM(GREATEST(pc.consign_price, pc.cost_price)*ps.product_number) AS product_amount_t  
                        FROM `ty_purchase_main` pm 
                        LEFT JOIN ty_purchase_sub ps ON pm.purchase_id = ps.purchase_id 
                        LEFT JOIN ty_product_cost pc ON pm.batch_id = pc.batch_id AND ps.product_id = pc.product_id 
                        WHERE pm.purchase_id = ?";
		$param = array();
		$param[] = $purchase_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		if (empty($row))
		{
			$product_number_t = 0;
			$product_amount_t = 0;
			$product_finished_number_t = 0;
		} else
		{
			$product_number_t = $row->product_number_t;
			$product_amount_t = $row->product_amount_t;
			$product_finished_number_t = $row->product_finished_number_t;
		}
		$param2 = array();
		$sql = "UPDATE ".$this->db->dbprefix('purchase_main')." SET purchase_number = ? ,purchase_amount = ? , purchase_finished_number = ? WHERE purchase_id = ? ";
		$param2[] = $product_number_t;
		$param2[] = $product_amount_t;
		$param2[] = $product_finished_number_t;
		$param2[] = $purchase_id;
		$query = $this->db->query($sql, $param2);

	}

        /**
         * 插入采购商品
         *
         * @param type $sub_id
         * @param type $product_number
         * @param type $purchase_id
         * @param type $admin_id
         * @return type 
         */
	public function insert_purchase_single($sub_id, $product_number, $purchase_id, $admin_id ) {
        $sql = "SELECT a.purchase_sub_id FROM " . $this->db->dbprefix('purchase_sub') . " a " .
                "LEFT JOIN " . $this->db->dbprefix('product_sub') . " b ON a.product_id = b.product_id AND a.color_id = b.color_id AND a.size_id = b.size_id " .
                "WHERE 1 AND b.sub_id = ? AND a.purchase_id = ? ";
        $param = array();
        $param[] = $sub_id;
        $param[] = $purchase_id;
        $query = $this->db->query($sql, $param);
        $row = $query->row();
        $data = array();
        if (!empty($row) && $row->purchase_sub_id > 0) {
            return -1;
            /*
              $sql = "UPDATE ".$this->db->dbprefix('purchase_sub')." SET product_number = product_number + ".$product_number . " WHERE purchase_sub_id = ".$row->purchase_sub_id;
              $this->db->query($sql);
              return $this->db->affected_rows();
             */
        } else {
            $sql = "INSERT INTO " . $this->db->dbprefix('purchase_sub') . " (purchase_id,product_id,product_name,color_id,size_id,shop_price," .
                    "product_number,product_amount,product_finished_number,create_admin,create_date) " .
                    "SELECT '" . $purchase_id . "',a.product_id,b.product_name,a.color_id,a.size_id,b.shop_price," .
                     $product_number . ",b.shop_price * " . $product_number. ",0,'" . $admin_id . "','" . date('Y-m-d H:i:s') . "' " .
                    "FROM " . $this->db->dbprefix('product_sub') . " a " .
                    "LEFT JOIN " . $this->db->dbprefix('product_info') . " b ON a.product_id = b.product_id " .
                    "WHERE a.sub_id = '" . $sub_id . "'";
            $this->db->query($sql);
            return $this->db->insert_id() > 0 ? 1 : 0;
        }
    }
    
         /**
         * 插入采购商品
         *
         * @param type $sub_id
         * @param type $product_number
         * @param type $purchase_id
         * @param type $admin_id
         * @return type 
         */
	public function insert_purchase_pro_single($sub_id, $product_number, $purchase_id, $admin_id ) {
            $sql = "INSERT INTO " . $this->db->dbprefix('purchase_sub') . " (purchase_id,product_id,product_name,color_id,size_id,shop_price," .
                    "product_number,product_amount,product_finished_number,create_admin,create_date) " .
                    "SELECT '" . $purchase_id . "',a.product_id,b.product_name,a.color_id,a.size_id,b.shop_price," .
                     $product_number . ",b.shop_price * " . $product_number. ",0,'" . $admin_id . "','" . date('Y-m-d H:i:s') . "' " .
                    "FROM " . $this->db->dbprefix('product_sub') . " a " .
                    "LEFT JOIN " . $this->db->dbprefix('product_info') . " b ON a.product_id = b.product_id " .
                    "WHERE a.sub_id = '" . $sub_id . "'";
            $this->db->query($sql);
            return $this->db->insert_id() > 0 ? 1 : 0;
	}
    
	public function finished_summly_purchase($purchase_id){
            $sql2 = "SELECT SUM(GREATEST(pc.consign_price, pc.cost_price)*ps.product_number) AS price FROM `ty_purchase_main` pm 
LEFT JOIN ty_purchase_sub ps ON pm.purchase_id = ps.purchase_id 
LEFT JOIN ty_product_cost pc ON pm.batch_id = pc.batch_id AND ps.product_id = pc.product_id 
WHERE pm.purchase_id = ".$purchase_id;
            $row2 = $this->db->query($sql2)->row();
            $sql3 = "SELECT SUM(ps.product_number) as num FROM `ty_purchase_main` pm 
                LEFT JOIN ty_purchase_sub ps ON pm.purchase_id = ps.purchase_id 
                LEFT JOIN ty_product_cost pc ON pm.batch_id = pc.batch_id AND ps.product_id = pc.product_id 
                WHERE pm.purchase_id = ".$purchase_id;
            $row3 = $this->db->query($sql3)->row();
	    /*$sql = " UPDATE ty_purchase_main a SET a.purchase_amount = (";
	    $sql .= $sql2;
	    $sql .= " ),a.purchase_number=(";
	    $sql .= $sql3;
	    $sql .= " ) WHERE a.purchase_id = $purchase_id";*/
	    
	    $sql = " UPDATE ty_purchase_main a SET a.purchase_amount = (";
	    $sql .= $row2->price;
	    $sql .= " ),a.purchase_number=(";
	    $sql .= $row3->num;
	    $sql .= " ) WHERE a.purchase_id = $purchase_id";
	    
	    $this->db->query($sql);
	}

	public function format_purchase_info($purchase_info)
	{
		$sql = "SELECT a.admin_id,a.admin_name FROM ".$this->db->dbprefix('admin_info')." a WHERE a.user_status = 1";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();

		$sql = "SELECT COUNT(*) as tot FROM ".$this->db->dbprefix('purchase_sub')." WHERE purchase_id = '".$purchase_info->purchase_id."'";
		$query = $this->db->query($sql);
		$result = $query->row();
		$query->free_result();
		$rs = array();
		foreach ($list as $row)
		{
			$rs[$row->admin_id] = $row->admin_name;
		}

		if ($purchase_info->lock_admin == 0)
		{
			$purchase_info->lock_status_name = '未锁定';
			$purchase_info->lock_name = '';
		} else
		{
			$purchase_info->lock_status_name = '锁定';
			$purchase_info->lock_name = $rs[$purchase_info->lock_admin];
		}


		if ($purchase_info->purchase_check_admin == 0)
		{
			$purchase_info->purchase_status = 1; //未审核
			$purchase_info->purchase_status_name = '未审核';
			$purchase_info->oper_name = $rs[$purchase_info->create_admin];
		} elseif ($purchase_info->purchase_check_admin > 0)
		{
			$purchase_info->purchase_status = 2; //已审核
			$purchase_info->purchase_status_name = '已审核';
			$purchase_info->oper_name = $rs[$purchase_info->purchase_check_admin];
		}
		if ($purchase_info->purchase_break == 1)
		{
			$purchase_info->purchase_status = 3; //已终止
			$purchase_info->purchase_status_name = '已终止';
			$purchase_info->oper_name = $rs[$purchase_info->purchase_break_admin];
		} elseif ($purchase_info->purchase_finished == 1)
		{
			$purchase_info->purchase_status = 4; //完成
			$purchase_info->purchase_status_name = '已完成';
			$purchase_info->oper_name = $rs[$purchase_info->create_admin];
		}
		if(!empty($result) && $result->tot > 0)
		{
			$purchase_info->has_product = 1;
		} else
		{
			$purchase_info->has_product = 0;
		}
                
		return $purchase_info;
	}

	public function check_depot_location($depot_id,$location_code)
	{
		$sql = "SELECT location_id FROM ".$this->db->dbprefix('location_info')." WHERE depot_id = ? AND CONCAT(location_code1,'-',location_code2,'-',location_code3,'-',location_code4,'-',location_code5) = ? ";
		$param = array();
		$param[] = $depot_id;
		$param[] = $location_code;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		if(!empty($row) && $row->location_id > 0)
		{
			return $row->location_id;
		} else
		{
			return false;
		}

	}

 	public function update_gl_num($param)
 	{

 		$sql = "SELECT SUM(a.product_number) as total FROM ".$this->db->dbprefix('transaction_info')." a" .
 				" LEFT JOIN ".$this->db->dbprefix('depot_info')." b ON b.depot_id = a.depot_id" .
 				" LEFT JOIN ".$this->db->dbprefix('product_sub')." c ON c.product_id = a.product_id AND c.color_id = a.color_id AND c.size_id = a.size_id" .
 				" WHERE b.depot_type = 1 AND a.trans_status IN (1,2,4) ";

		if (isset($param['sub_id']) && $param['sub_id'] > 0)
		{
				$sql .= " AND c.sub_id = '".$param['sub_id']."'";
				$query = $this->db->query($sql);
				$row = $query->row();
				$query->free_result();
				$total_num = isset($row->total)&&!empty($row->total)?$row->total:0;
				$sql = "UPDATE ".$this->db->dbprefix('product_sub')." SET gl_num = ".$total_num." WHERE sub_id = '".$param['sub_id']."'";
				$this->db->query($sql);
				return TRUE;
		}
		return FALSE;
 	}

 	public function update_gl_num_in($trans_sn)
 	{
 		//for check
		$sql = "UPDATE ".$this->db->dbprefix('product_sub')." a, " .
				"(SELECT z.product_id,z.color_id,z.size_id,SUM(z.product_number) as total" .
				" FROM ".$this->db->dbprefix('transaction_info')." z" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." x ON x.depot_id = z.depot_id" .
				" WHERE x.depot_type = 1 AND z.trans_sn = '".$trans_sn."' AND z.trans_status = ".TRANS_STAT_AWAIT_IN." GROUP BY z.product_id,z.color_id,z.size_id) b" .
				" SET a.gl_num = a.gl_num + b.total" .
				" WHERE b.product_id = a.product_id AND b.color_id = a.color_id AND b.size_id = a.size_id";
		$query = $this->db->query($sql);
 	}

 	public function update_gl_num_out($trans_sn)
 	{
 		//for delete
		$sql = "UPDATE ".$this->db->dbprefix('product_sub')." a, " .
				"(SELECT z.product_id,z.color_id,z.size_id,SUM(z.product_number) as total" .
				" FROM ".$this->db->dbprefix('transaction_info')." z" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." x ON x.depot_id = z.depot_id" .
				" WHERE x.depot_type = 1 AND z.trans_sn = '".$trans_sn."' AND z.trans_status = ".TRANS_STAT_AWAIT_OUT." GROUP BY z.product_id,z.color_id,z.size_id) b" .
				" SET a.gl_num = a.gl_num - b.total" .
				" WHERE b.product_id = a.product_id AND b.color_id = a.color_id AND b.size_id = a.size_id";
		$query = $this->db->query($sql);
 	}

	public function get_product_by_subid ($sub_id)
	{
		$sql = "SELECT a.sub_id,a.product_id,a.color_id,a.size_id,b.product_name,b.product_sn,b.provider_productcode,f.size_sn,e.color_sn,f.size_name,e.color_name" .
				" FROM ".$this->db->dbprefix('product_sub')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id ".
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" WHERE a.sub_id = ? ";
		$param = array();
		$param[] = $sub_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		return $row;
	}

	public function get_transaction_product_sub($transaction_id)
	{
		$sql = "SELECT b.sub_id " .
				" FROM ".$this->db->dbprefix('transaction_info')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_sub')." b ON b.product_id = a.product_id AND b.color_id = a.color_id AND b.size_id = a.size_id".
				" WHERE a.transaction_id = ? ";
		$param = array();
		$param[] = $transaction_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		return isset($row->sub_id)?$row->sub_id:'';
	}

	public function delete_transaction ($where_arr)
	{
		$this->db->delete('transaction_info', $where_arr);
		return $this->db->affected_rows();
	}

	public function update_transaction ($data, $where_arr)
	{
		$this->db->update('transaction_info', $data, $where_arr);
	}

	public function filter_purchase_sub($filter)
	{
		$query = $this->db->get_where('purchase_sub', $filter, 1);
		return $query->row();
	}

	public function filter_depot_in_sub($filter)
	{
		$query = $this->db->get_where('depot_in_sub', $filter, 1);
		return $query->row();
	}

	public function all_depot($filter)
	{
		$query = $this->db->get_where('depot_info',$filter);
		return $query->result();
	}

	public function all_location($filter)
	{
		$query = $this->db->get_where('location_info',$filter);
		return $query->result();
	}

	public function lock_location($location_id)
	{
		if(is_array($location_id)){
			$sql = "SELECT * FROM ".$this->db->dbprefix('location_info')." WHERE location_id ".db_create_in($location_id)." FOR UPDATE";
			$query = $this->db->query($sql);
			return $query->result();
		}else{
			$sql = "SELECT * FROM ".$this->db->dbprefix('location_info')." WHERE location_id = ? LIMIT 1 FOR UPDATE";
			$query = $this->db->query($sql,array($location_id));
			return $query->row();
		}
	}
	
	public function check_conform_depot_out(){
//	    $sql="SELECT * FROM .$this->db->dbprefix('location_info')."
	}
	
	public function location_info_scan ($location_id) {
				
		$where = " AND a.location_id = '" . $location_id . "'";
                
		$from = " FROM (
			SELECT a.product_id,a.color_id,a.size_id,SUM(a.product_number) AS sum_daichu,0 AS sum_shiji,0 AS sum_dairu
			FROM ty_transaction_info AS a 
			WHERE a.trans_status = 1 " . $where . "
			GROUP BY a.product_id,a.color_id,a.size_id
			UNION
			SELECT a.product_id,a.color_id,a.size_id,0 AS sum_daichu,SUM(a.product_number) AS sum_shiji,0 as sum_dairu 
			FROM ty_transaction_info AS a 
			WHERE a.trans_status in (2,4) " . $where . "
			GROUP BY a.product_id,a.color_id,a.size_id
			UNION
			SELECT a.product_id,a.color_id,a.size_id,0 AS sum_daichu,0 AS sum_shiji,SUM(a.product_number) AS sum_dairu
			FROM ty_transaction_info AS a 
			WHERE a.trans_status = 3 " . $where . "
			GROUP BY a.product_id,a.color_id,a.size_id
		) as bb ";
		
		$groupby = " GROUP BY bb.product_id,bb.color_id,bb.size_id";
                $having = " HAVING num_daichu < 0 OR num_shiji > 0 OR num_dairu > 0";
		
		$sql = " SELECT bb.product_id,bb.color_id,bb.size_id,sum(bb.sum_daichu) AS num_daichu,sum(bb.sum_shiji) AS num_shiji,sum(bb.sum_dairu) AS num_dairu, 
				b.product_name,b.product_sn,b.provider_productcode,c.color_name,c.color_sn,d.size_name,d.size_sn,e.brand_name,f.provider_barcode "
				. $from . "
				LEFT JOIN ty_product_info AS b ON b.product_id = bb.product_id 
				LEFT JOIN ty_product_sub AS f ON f.product_id=bb.product_id AND f.color_id=bb.color_id AND f.size_id=bb.size_id 
				LEFT JOIN ty_product_color AS c ON c.color_id = bb.color_id 
				LEFT JOIN ty_product_size AS d ON d.size_id = bb.size_id 
				LEFT JOIN ty_product_brand AS e ON e.brand_id = b.brand_id " . 
				$groupby . $having;
                      
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		
		$sql = " SELECT sum(bb.sum_daichu) as sum_daichu, sum(bb.sum_shiji) AS sum_shiji, sum(bb.sum_dairu) AS sum_dairu "
				. $from ;
		//echo $sql;die;
		$query = $this->db->query($sql);
		$row = $query->row();
		$query->free_result();
                
                $filter = array();
		$filter['sum_daichu'] = (int) $row->sum_daichu;
		$filter['sum_shiji'] = (int) $row->sum_shiji;
		$filter['sum_dairu'] = (int) $row->sum_dairu;
		
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function barcode_scan ($filter) {
		
		$where = "";
		if (!empty($filter['provider_barcode']))
		{
			$where .= " AND a.provider_barcode = '" . $filter['provider_barcode'] . "'";
		}
		if (!empty($filter['location_id']))
		{
			$where .= " AND t.location_id = " . $filter['location_id'];
		}
		
		$from = " FROM (
			SELECT a.sub_id,t.product_id,t.color_id,t.size_id,t.location_id,SUM(t.product_number) AS num_daicu,0 AS num_shiji,0 AS num_dairu,a.provider_barcode,t.batch_id 
			FROM ".$this->db->dbprefix('product_sub')." a
			LEFT JOIN ".$this->db->dbprefix('transaction_info')." t ON a.product_id=t.product_id AND a.color_id=t.color_id AND a.size_id=t.size_id
			WHERE t.trans_status=1 " . $where . "
			GROUP BY t.product_id,t.color_id,t.size_id,t.location_id,t.batch_id
			
			UNION
			
			SELECT a.sub_id,t.product_id,t.color_id,t.size_id,t.location_id,0 AS num_daicu,SUM(t.product_number) AS num_shiji,0 AS num_dairu,a.provider_barcode,t.batch_id
			FROM ".$this->db->dbprefix('product_sub')." a
			LEFT JOIN ".$this->db->dbprefix('transaction_info')." t ON a.product_id=t.product_id AND a.color_id=t.color_id AND a.size_id=t.size_id
			WHERE t.trans_status in (2,4) " . $where . "
			GROUP BY t.product_id,t.color_id,t.size_id,t.location_id,t.batch_id
			
			UNION
			
			SELECT a.sub_id,t.product_id,t.color_id,t.size_id,t.location_id,0 AS num_daicu,0 AS num_shiji,SUM(t.product_number) AS num_dairu,a.provider_barcode,t.batch_id 
			FROM ".$this->db->dbprefix('product_sub')." a
			LEFT JOIN ".$this->db->dbprefix('transaction_info')." t ON a.product_id=t.product_id AND a.color_id=t.color_id AND a.size_id=t.size_id
			WHERE t.trans_status=3 " . $where . "
			GROUP BY t.product_id,t.color_id,t.size_id,t.location_id,t.batch_id
		
		) AS aa 
		";
		
		$sql = " SELECT aa.sub_id,aa.product_id,aa.color_id,aa.size_id,aa.provider_barcode,aa.batch_id,b.shop_price,
			b.product_name,b.product_sn,b.provider_productcode,c.color_name,d.size_name,e.brand_name,f.location_name,
			aa.location_id,sum(aa.num_daicu) num_daicu,sum(aa.num_shiji) num_shiji,sum(aa.num_dairu) num_dairu "
			. $from . "
			LEFT JOIN ".$this->db->dbprefix('product_info')." AS b ON aa.product_id=b.product_id
			LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON aa.color_id=c.color_id
			LEFT JOIN ".$this->db->dbprefix('product_size')." AS d ON aa.size_id=d.size_id
			LEFT JOIN ".$this->db->dbprefix('product_brand')." AS e ON b.brand_id=e.brand_id 
			LEFT JOIN ".$this->db->dbprefix('location_info')." AS f ON aa.location_id=f.location_id  
			GROUP BY aa.product_id,aa.color_id,aa.size_id,aa.location_id,aa.batch_id";
		//echo $sql;die;
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		
		//var_dump($result);die;
		return array('list' => $list, 'filter' => $filter);
	}
        
        public function filter_transaction_infos($filter) {
            $query = $this->db->get_where('transaction_info', $filter);
            return $query->result();
        }
        
	public function filter_trans_info($filter) {
		$from = " FROM ".$this->db->dbprefix('transaction_info')." AS t ";
        $where = " WHERE 1=1 ";
        
		if (!empty($filter['product_id']))
		{
			$where .= " AND t.product_id = ? ";
			$param[] = $filter['product_id'];
		}
		if (!empty($filter['color_id']))
		{
			$where .= " AND t.color_id = ? ";
			$param[] = $filter['color_id'];
		}
		if (!empty($filter['size_id']))
		{
			$where .= " AND t.size_id = ? ";
			$param[] = $filter['size_id'];
		}
		if (!empty($filter['trans_status']))
		{
			$where .= " AND t.trans_status ".db_create_in($filter['trans_status']);
		}
        
		$sql = "SELECT t.* ".$from.$where." ORDER BY t.transaction_id";
		//echo $sql;die;
		$query = $this->db_r->query($sql,$param);
		$result = $query->result();
		$query->free_result();
        return $result;
	}
}
###
