<?php
#doc
#	classname:	Depotio_model
#	scope:		PUBLIC
#
#/doc

class Exchange_model extends CI_Model
{

	public function query_products_exchange_in ($filter)
	{
		$where = " WHERE 1 AND b.is_audit = 1 AND b.is_stop = 0 "; //商品已审核，未停止订货
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
			$where .= " AND b.provider_id = ? ";
			$param[] = $filter['provider_id'];
		}

		if (!empty($filter['cooperation_id']))
		{
			$where .= " AND c.provider_cooperation = ? ";
			$param[] = $filter['cooperation_id'];
		}

		if (!empty($filter['provider_status']))
		{
			if ($filter['provider_status'] == 1)
			{
				$where .= " AND a.is_on_sale = 1 ";
			} elseif ($filter['provider_status'] == 2)
			{
				$where .= " AND a.is_on_sale = 0 ";
			}
		}

		if (!empty($filter['exchange_id']))
		{
			$where .= " AND NOT EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('exchange_out')." s WHERE s.exchange_id = ? AND s.product_id = a.product_id" .
					" AND s.color_id = a.color_id AND s.size_id = a.size_id ) ";
			$param[] = $filter['exchange_id'];
		}

		if (!empty($filter['with_not']))
		{
			$where .= " AND NOT EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('exchange_in')." s WHERE s.exchange_id = ? AND s.product_id = a.product_id" .
					" AND s.color_id = a.color_id AND s.size_id = a.size_id ) ";
			$param[] = $filter['with_not'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'b.brand_id ASC,b.product_id ASC,a.color_id ASC,a.size_id ASC ' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? '' : trim($filter['sort_order']);

		$sql = "SELECT COUNT(*) AS ct FROM " . $this->db->dbprefix('product_sub') . " a" .
					" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = b.provider_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" . $where;

		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}

		$sql = "SELECT a.*,b.product_sn,b.product_name,b.provider_productcode,b.is_audit,b.is_stop,b.market_price," .
					" b.shop_price,b.promote_price,cost.consign_price,cost.cost_price,cost.consign_rate,cost.consign_type,c.provider_cooperation,cost.product_cess," .
					" c.provider_name,d.brand_name,e.color_name,e.color_sn,f.size_name,f.size_sn " .
					" FROM " . $this->db->dbprefix('product_sub') . " a" .
					" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = b.provider_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_cost')." AS cost ON cost.product_id = a.product_id" .
					 $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'].
					 " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$list = $this->format_product_list($list);
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function query_products_exchange_out ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_sub')." AS a ";
		$where = " AND b.is_audit = 1 AND b.is_stop = 0 "; //商品已审核，未停止订货
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
			$where .= " AND pb.provider_id = ? ";
			$param[] = $filter['provider_id'];
		}

		if (!empty($filter['cooperation_id']))
		{
			$where .= " AND pc.provider_cooperation = ? ";
			$param[] = $filter['cooperation_id'];
		}

		if (!empty($filter['provider_status']))
		{
			if ($filter['provider_status'] == 1)
			{
				$where .= " AND c.is_on_sale = 1 ";
			} elseif ($filter['provider_status'] == 2)
			{
				$where .= " AND c.is_on_sale = 0 ";
			}
		}

		if (!empty($filter['depot_id']))
		{
			$where .= " AND a.depot_id = ? ";
			$param[] = $filter['depot_id'];
		}

		if (!empty($filter['with_not']))
		{
			$where .= " AND NOT EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('exchange_out')." s WHERE s.exchange_id = ? AND s.product_id = a.product_id" .
					" AND s.color_id = a.color_id AND s.size_id = a.size_id AND s.source_depot_id = a.depot_id AND s.source_location_id = a.location_id) ";
			$param[] = $filter['with_not'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'b.brand_id ASC,b.product_id ASC,a.color_id ASC,a.size_id ASC ' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? '' : trim($filter['sort_order']);

		$sql = "SELECT COUNT(tmp.product_id) AS total FROM " .
					" (SELECT a.product_id,a.size_id,a.color_id,SUM(a.product_number) AS real_num FROM ".
					$this->db->dbprefix('transaction_info')." a" .
					" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id=a.product_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_sub')." c ON c.product_id = a.product_id AND c.color_id=a.color_id AND c.size_id=a.size_id".
					" LEFT JOIN ".$this->db->dbprefix('product_color')." d ON d.color_id=a.color_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_size')." e ON e.size_id=a.size_id" .
					" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON pb.batch_id = a.batch_id" .
					" WHERE a.trans_status IN (1,2,4) $where GROUP BY a.product_id,a.size_id,a.color_id HAVING real_num > 0) AS tmp";

		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->total;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}

		$sql = "SELECT a.product_id,a.size_id,a.color_id,SUM(a.product_number) AS real_num " .
					" FROM ".$this->db->dbprefix('transaction_info')." a" .
					" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id=a.product_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_sub')." c ON c.product_id = a.product_id AND c.color_id=a.color_id AND c.size_id=a.size_id".
					" LEFT JOIN ".$this->db->dbprefix('product_color')." d ON d.color_id=a.color_id" .
					" LEFT JOIN ".$this->db->dbprefix('product_size')." e ON e.size_id=a.size_id" .
					" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON pb.batch_id = a.batch_id" .
					" WHERE a.trans_status IN (1,2,4) $where GROUP BY a.product_id,a.size_id,a.color_id HAVING real_num > 0 ".
					" ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'].
				 	" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

		$query = $this->db->query($sql, $param);
		$goods_list = $query->result();
		$query->free_result();
		$goods_id_arr = array();
		foreach($goods_list as $item)
		{
			$goods_id_arr[] = $item->product_id.'-'.$item->color_id.'-'.$item->size_id;
		}

		$sql = "SELECT a.*,b.product_sn,b.product_name,b.provider_productcode,b.is_audit,b.is_stop,b.market_price,c.provider_barcode,c.gl_num,c.is_on_sale, " .
				" b.shop_price,b.promote_price,cost.consign_price,cost.cost_price,cost.consign_rate,cost.consign_type,pc.provider_cooperation,cost.product_cess," .
				" pc.provider_name,d.brand_name,e.color_name,e.color_sn,f.size_name,f.size_sn,h.depot_name,i.location_name,i.location_code1,i.location_code2,i.location_code3,i.location_code4 " .
				" FROM ".$this->db->dbprefix('transaction_info')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_sub')." c ON c.product_id = a.product_id AND c.color_id=a.color_id AND c.size_id=a.size_id".
				" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON pb.batch_id = a.batch_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_provider')." pc ON pc.provider_id = pb.provider_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." h ON h.depot_id = a.depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." i ON i.location_id = a.location_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_cost')." AS cost ON cost.product_id = a.product_id AND cost.batch_id = a.batch_id " .
				" WHERE a.trans_status IN (1,2,4) AND CONCAT(a.product_id,'-',a.color_id,'-',a.size_id) ".db_create_in($goods_id_arr).$where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'];
		
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$return = array();
		$help = array();
		if (!empty($list))
		{
			foreach ($list as $item)
			{
				if (!isset($return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id]))
				{
					$item->total_can_out_num = 0;
					$item->item = array();
					$return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id] = $item;
				}
				$total_tmp = $return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id];
				if ($item->trans_sn != $filter['trans_sn'])
				{
					$total_tmp->total_can_out_num += $item->product_number;
				}
				$return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id] = $total_tmp;

				$tmp1 = $return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id];
				$tmp2 = $tmp1->item;
				if (isset($tmp2[$item->depot_id.'-'.$item->location_id]))
				{
					$tmp = $tmp2[$item->depot_id.'-'.$item->location_id];
					if ($item->trans_sn != $filter['trans_sn'])
					{
						$item->can_out_num = $item->product_number + $tmp->can_out_num;
					} else
					{
						$item->can_out_num = $tmp->can_out_num;
					}

				} else
				{
					if ($item->trans_sn != $filter['trans_sn'])
					{
						$item->can_out_num = $item->product_number;
					} else
					{
						$item->can_out_num = 0;
					}

				}
				$tmp2[$item->depot_id.'-'.$item->location_id] = $item;
				$tmp1->item = $tmp2;

				$return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id] = $tmp1;
			}
		}

		$return = $this->format_product_list($return);
		$query->free_result();
		return array('list' => $return, 'filter' => $filter);
	}

	public function exchange_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('exchange_main')." AS a ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['exchange_code']))
		{
			$where .= " AND a.exchange_code LIKE ? ";
			$param[] = '%' . $filter['exchange_code'] . '%';
		}

		if (!empty($filter['in_depot_id']))
		{
			$where .= " AND a.dest_depot_id = ? ";
			$param[] = $filter['in_depot_id'];
		}

		if (!empty($filter['out_depot_id']))
		{
			$where .= " AND a.source_depot_id = ? ";
			$param[] = $filter['out_depot_id'];
		}

		if (!empty($filter['exchange_status']))
		{
			if ($filter['exchange_status'] == 1)
			{
				$where .= " AND a.out_audit_admin = 0 ";
			} elseif ($filter['exchange_status'] == 2)
			{
				$where .= " AND a.out_audit_admin > 0 AND a.in_audit_admin = 0 ";
			} elseif ($filter['exchange_status'] == 3)
			{
				$where .= " AND a.in_audit_admin > 0 ";
			}

		}

		if (!empty($filter['provider_goods']))
		{
			$where .= " AND (EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('exchange_out')." s1, ".$this->db->dbprefix('product_info')." v1 WHERE a.exchange_id = s1.exchange_id AND s1.product_id = v1.product_id AND (v1.product_name LIKE ? OR v1.product_sn LIKE ? ))" .
					" OR EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('exchange_in')." s2, ".$this->db->dbprefix('product_info')." v2 WHERE a.exchange_id = s2.exchange_id AND s2.product_id = v2.product_id AND (v2.product_name LIKE ? OR v2.product_sn LIKE ? )))";
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.exchange_id' : trim($filter['sort_by']);
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
		$sql = "SELECT a.*,di.depot_name as in_depot_name,do.depot_name as out_depot_name,e.admin_name AS out_admin_name," .
				"g.admin_name AS in_admin_name,h.admin_name as out_audit_name,i.admin_name as in_audit_name,j.admin_name as lock_name " .
				$from .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." di ON di.depot_id = a.dest_depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." do ON do.depot_id = a.source_depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." e ON e.admin_id = a.out_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." g ON g.admin_id = a.in_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." h ON h.admin_id = a.out_audit_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." i ON i.admin_id = a.in_audit_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." j ON j.admin_id = a.lock_admin" .
				 $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		if (!empty($list))
		{
			foreach ($list as $key=>$exchange_info)
			{

				if ($exchange_info->out_audit_admin == 0)
				{
					$exchange_info->exchange_status = 1; //未审核
					$exchange_info->exchange_status_name = '出库未审核';
				} elseif ($exchange_info->out_audit_admin > 0 && $exchange_info->in_audit_admin == 0)
				{
					$exchange_info->exchange_status = 2; //已审核
					$exchange_info->exchange_status_name = '出库已审核,入库未审核';
				} elseif ($exchange_info->in_audit_admin > 0)
				{
					$exchange_info->exchange_status = 3; //已审核
					$exchange_info->exchange_status_name = '出库已审核,入库已审核';
				}

				if ($exchange_info->lock_admin > 0)
				{
					$exchange_info->exchange_status_name = "已锁,".$exchange_info->exchange_status_name;
				} else
				{
					$exchange_info->exchange_status_name = "未锁,".$exchange_info->exchange_status_name;
				}

				$list[$key] = $exchange_info;
			}
		}

		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function get_exchange_code()
	{
		return 'TC'.date('YmdHis');
	}

	public function filter_exchange ($filter)
	{
		$query = $this->db->get_where('exchange_main', $filter, 1);
		return $query->row();
	}
	
	public function query_exchange_out ($filter)
	{
		$param = array();
		$sql="SELECT a.* FROM ".$this->db->dbprefix('exchange_out'). " a ".
		" LEFT JOIN ".$this->db->dbprefix('product_sub')." x ON x.product_id = a.product_id AND x.color_id = a.color_id AND x.size_id = a.size_id ";
		$where = " WHERE 1 ";
		if (!empty($filter['exchange_id']))
		{
			$where .= " AND a.exchange_id =? ";
			$param[] = $filter['exchange_id'];
		}
		//sub
		if (!empty($filter['sub_id']))
		{
			$where .= " AND x.sub_id =? ";
			$param[] = $filter['sub_id'];
		}
		//product_id color size
		if (!empty($filter['product_id']))
		{
			$where .= " AND x.product_id =? ";
			$param[] = $filter['product_id'];
		}
		
		if (!empty($filter['color_id']))
		{
			$where .= " AND x.color_id =? ";
			$param[] = $filter['color_id'];
		}
		
		if (!empty($filter['size_id']))
		{
			$where .= " AND x.size_id =? ";
			$param[] = $filter['size_id'];
		}
		//join in
		if (!empty($filter['exchange_leaf_id']))
		{
			$sql .=" LEFT JOIN ".$this->db->dbprefix('exchange_in')." i ON i.product_id = a.product_id AND i.color_id = a.color_id AND i.size_id = a.size_id ";
			$where .= " AND i.exchange_leaf_id =? ";
			$param[] = $filter['exchange_leaf_id'];
		}
		$query = $this->db->query($sql . $where, $param);
		return $query->result();
	}
	
	public function query_exchange_in ($filter)
	{
		$param = array();
		$sql="SELECT a.*,x.sub_id FROM ".$this->db->dbprefix('exchange_in'). " a ".
		" LEFT JOIN ".$this->db->dbprefix('product_sub')." x ON x.product_id = a.product_id AND x.color_id = a.color_id AND x.size_id = a.size_id ";
		$where = " WHERE 1 ";
		if (!empty($filter['exchange_leaf_id']))
		{
			$where .= " AND a.exchange_leaf_id =? ";
			$param[] = $filter['exchange_leaf_id'];
		}
		if (!empty($filter['exchange_id']))
		{
			$where .= " AND a.exchange_id =? ";
			$param[] = $filter['exchange_id'];
		}
		if (!empty($filter['sub_id']))
		{
			$where .= " AND x.sub_id =? ";
			$param[] = $filter['sub_id'];
		}
		if (!empty($filter['size_id']))
		{
			$where .= " AND x.size_id =? ";
			$param[] = $filter['size_id'];
		}
		//product_id color size
		if (!empty($filter['product_id']))
		{
			$where .= " AND x.product_id =? ";
			$param[] = $filter['product_id'];
		}
		
		if (!empty($filter['color_id']))
		{
			$where .= " AND x.color_id =? ";
			$param[] = $filter['color_id'];
		}
		
		if (!empty($filter['size_id']))
		{
			$where .= " AND x.size_id =? ";
			$param[] = $filter['size_id'];
		}
		$query = $this->db->query($sql . $where, $param);
		return $query->result();
	}

	public function filter_exchange_in ($filter)
	{
		$query = $this->db->get_where('exchange_in', $filter);
		return $query->result();
	}


	public function get_exchange_info ($exchange_id)
	{
		$param = array();
		$sql = "a.*,ds.depot_name AS source_depot_name,de.depot_name AS dest_depot_name,e.admin_name AS create_name," .
				"g.admin_name AS audit_name,h.admin_id as lock_name " .
				" FROM ".$this->db->dbprefix('exchange_main')." a" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." ds ON ds.depot_id = a.source_depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." de ON de.depot_id = a.dest_depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." e ON e.admin_id = a.create_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." g ON g.admin_id = a.audit_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." h ON h.admin_id = a.lock_admin" .
				" WHERE 1 ";
		if (empty($depot_out_id))
		{
			$depot_out_id = 0;
		}
		$sql .= " AND a.depot_out_id = ? LIMIT 1";
		$param[] = $depot_out_id;

		$query = $this->db->query($sql, $param);
		return $query->row();
	}

	public function exchange_out_products ($exchange_id)
	{
		$param = array();
		$sql = "SELECT a.*,SUM(t.product_number) AS real_num,b.product_sn,b.product_name,b.provider_productcode,b.is_audit,b.is_onsale,b.is_stop,b.market_price," .
				" b.shop_price,b.promote_price,cost.consign_price,cost.cost_price,cost.consign_rate,cost.consign_type,c.provider_cooperation,cost.product_cess," .
				" c.provider_name,d.brand_name,e.color_name,e.color_sn,f.size_name,f.size_sn," .
				" h.location_code1,h.location_code2,h.location_code3,h.location_code4,h.location_name,g.depot_name " .
				" FROM ".$this->db->dbprefix('exchange_out')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = b.provider_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." g ON g.depot_id = a.source_depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." h ON h.location_id = a.source_location_id" .
				" LEFT JOIN ".$this->db->dbprefix('transaction_info')." t ON t.product_id = a.product_id AND t.color_id = a.color_id AND t.size_id = a.size_id" .
				" AND t.depot_id = a.source_depot_id AND t.location_id = a.source_location_id AND t.trans_status IN (1,2,4) " .
				" LEFT JOIN ".$this->db->dbprefix('product_cost')." AS cost ON cost.product_id = a.product_id AND cost.batch_id = t.batch_id " .				
				" WHERE a.exchange_id = ? GROUP BY a.exchange_sub_id ORDER BY a.exchange_sub_id DESC ";
		$param[] = $exchange_id;
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$list = $this->format_product_list($list);
		return $list;
	}

	public function update_exchange ($data, $exchange_id)
	{
		$this->db->update('exchange_main', $data, array('exchange_id' => $exchange_id));
	}

	public function insert_exchange ($data)
	{
		$this->db->insert('exchange_main', $data);
		return $this->db->insert_id();
	}

	public function delete_exchange ($exchange_id)
	{
		$this->db->delete('exchange_main', array('exchange_id' => $exchange_id));
		return $this->db->affected_rows();
	}

	public function delete_exchange_product ($where_arr)
	{
		$this->db->delete('exchange_in', $where_arr);
		$this->db->delete('exchange_out', $where_arr);
		return $this->db->affected_rows();
	}

	public function update_exchange_out_product ($data, $where_arr)
	{
		$this->db->update('exchange_out', $data, $where_arr);
	}

	public function insert_exchange_out_product ($data)
	{
		$this->db->insert('exchange_out', $data);
		return $this->db->insert_id();
	}

	public function delete_exchange_out_product ($where_arr)
	{
		$this->db->delete('exchange_out', $where_arr);
		return $this->db->affected_rows();
	}

	public function format_exchange_info($exchange_info)
	{
		$sql = "SELECT a.admin_id,a.admin_name FROM ".$this->db->dbprefix('admin_info')." a WHERE a.user_status = 1";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();

		$sql = "SELECT COUNT(*) as totout FROM ".$this->db->dbprefix('exchange_out')." WHERE exchange_id = '".$exchange_info->exchange_id."'";
		$query = $this->db->query($sql);
		$resultout = $query->row();
		$query->free_result();

		$sql = "SELECT COUNT(*) as totin FROM ".$this->db->dbprefix('exchange_in')." WHERE exchange_id = '".$exchange_info->exchange_id."'";
		$query = $this->db->query($sql);
		$resultin = $query->row();
		$query->free_result();

		$rs = array();
		foreach ($list as $row)
		{
			$rs[$row->admin_id] = $row->admin_name;
		}

		if ($exchange_info->lock_admin == 0)
		{
			$exchange_info->lock_status_name = '未锁定';
			$exchange_info->lock_name = '';
		} else
		{
			$exchange_info->lock_status_name = '已锁定';
			$exchange_info->lock_name = $rs[$exchange_info->lock_admin];
		}


		if ($exchange_info->out_audit_admin == 0)
		{
			$exchange_info->exchange_status = 1; //未审核
			$exchange_info->exchange_status_name = '出库未审核';
			$exchange_info->oper_name = $rs[$exchange_info->out_admin];
		} elseif ($exchange_info->out_audit_admin > 0 && $exchange_info->in_audit_admin == 0)
		{
			$exchange_info->exchange_status = 2; //已审核
			$exchange_info->exchange_status_name = '出库已审核，入库未审核';
			$exchange_info->oper_name = $rs[$exchange_info->out_audit_admin];
		} elseif ($exchange_info->in_audit_admin > 0)
		{
			$exchange_info->exchange_status = 3; //已审核
			$exchange_info->exchange_status_name = '入库已审核';
			$exchange_info->oper_name = $rs[$exchange_info->in_audit_admin];
		}

		if(!empty($resultout) && $resultout->totout > 0)
		{
			$exchange_info->has_product_out = 1;
		} else
		{
			$exchange_info->has_product_out = 0;
		}
		if(!empty($resultin) && $resultin->totin > 0)
		{
			$exchange_info->has_product_in = 1;
		} else
		{
			$exchange_info->has_product_in = 0;
		}

		return $exchange_info;
	}

	public function update_exchange_out_total ($exchange_id)
	{
		$sql = "SELECT SUM(product_number) AS product_number_t " .
				"FROM ".$this->db->dbprefix('exchange_out')." WHERE exchange_id = ? ";
		$param = array();
		$param[] = $exchange_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		if (empty($row))
		{
			$product_number_t = 0;
		} else
		{
			$product_number_t = $row->product_number_t;
		}
		$param2 = array();
		$sql = "UPDATE ".$this->db->dbprefix('exchange_main')." SET exchange_out_number = ? WHERE exchange_id = ? ";
		$param2[] = $product_number_t;
		$param2[] = $exchange_id;
		$query = $this->db->query($sql, $param2);
	}

	public function insert_exchange_out_single ($transaction_id,$product_number,$exchange_id,$admin_id)
	{
		$sql = "SELECT a.exchange_sub_id,c.exchange_code FROM ".$this->db->dbprefix('exchange_out')." a " .
				"LEFT JOIN ".$this->db->dbprefix('transaction_info')." b ON a.product_id = b.product_id AND a.color_id = b.color_id AND a.size_id = b.size_id AND a.source_depot_id = b.depot_id AND a.source_location_id = b.location_id ".
				"LEFT JOIN ".$this->db->dbprefix('exchange_main')." c ON a.exchange_id = c.exchange_id ".
				"WHERE 1 AND b.transaction_id = ? AND c.exchange_id = ? ";
		$param = array();
		$param[] = $transaction_id;
		$param[] = $exchange_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$data = array();
		if (!empty($row) && $row->exchange_sub_id > 0)
		{
			/*
			$sql = "UPDATE ".$this->db->dbprefix('exchange_out')." SET product_number = product_number + ".$product_number . " WHERE exchange_sub_id = ".$row->exchange_sub_id;
			$this->db->query($sql);
			if ($this->db->affected_rows() == 0)
			{
				return false;
			}
			$sql = "UPDATE ".$this->db->dbprefix('transaction_info')." SET product_number = product_number - ".$product_number . " WHERE sub_id = ".$row->exchange_sub_id . " AND trans_sn = '".$row->exchange_code."' AND trans_status = ".TRANS_STAT_AWAIT_OUT;
			$this->db->query($sql);
			return $this->db->affected_rows();
			*/
			return -1;
		} else
		{
			$sql = "INSERT INTO ".$this->db->dbprefix('exchange_out')." (exchange_id,product_id,color_id,size_id,source_depot_id,source_location_id,shop_price," .
					"product_number,create_admin,create_date,batch_id,expire_date,production_batch) " .
					"SELECT '".$exchange_id."',a.product_id,a.color_id,a.size_id,a.depot_id,a.location_id,b.shop_price,".
					$product_number.",'".$admin_id."','".date('Y-m-d H:i:s')."',a.batch_id,a.expire_date,a.production_batch " .
					"FROM " . $this->db->dbprefix('transaction_info') ." a " .
					" LEFT JOIN " . $this->db->dbprefix('product_info') . " b ON a.product_id = b.product_id " .
					" LEFT JOIN ".$this->db->dbprefix('product_cost')." AS cost ON cost.product_id = a.product_id AND cost.batch_id = a.batch_id " .
					" WHERE a.transaction_id = '".$transaction_id."'";
			$this->db->query($sql);
			$sub_id = $this->db->insert_id();
			$sub_id = $sub_id > 0?$sub_id:0;
			if ($sub_id == 0)
			{
				return false;
			}
			$sql = "INSERT INTO ".$this->db->dbprefix('transaction_info')."(trans_type,trans_status,trans_sn,product_id,color_id,size_id,product_number," .
					"depot_id,location_id,batch_id,create_admin,create_date,update_admin,update_date,cancel_admin,cancel_date,trans_direction,sub_id,".
					"shop_price,consign_price,cost_price,consign_rate,product_cess,expire_date,production_batch) ".
					" SELECT ".TRANS_TYPE_PACKET_EXCHANGE.",".TRANS_STAT_AWAIT_OUT.",b.exchange_code,a.product_id,a.color_id,size_id,(0-a.product_number),".
					"a.source_depot_id,a.source_location_id,a.batch_id,a.create_admin,a.create_date,0,'0000-00-00',0,'0000-00-00',0,a.exchange_sub_id,".
					"a.shop_price,cost.consign_price,cost.cost_price,cost.consign_rate,cost.product_cess,a.expire_date,a.production_batch " .
					" FROM ".$this->db->dbprefix('exchange_out')." a" .
					" LEFT JOIN ".$this->db->dbprefix('product_cost')." AS cost ON cost.product_id = a.product_id AND cost.batch_id = a.batch_id " .
					" LEFT JOIN ".$this->db->dbprefix('exchange_main')." b ON b.exchange_id = a.exchange_id WHERE a.exchange_sub_id = '".$sub_id."' ";
			$this->db->query($sql);
			return $this->db->insert_id()>0?1:0;
		}
 	}

 	public function update_exchange_out_product_x ($exchange_sub_id,$exchange_id,$product_number,$exchange_code)
	{
		$sql = "UPDATE ".$this->db->dbprefix('exchange_out')." " .
				"SET product_number = '".$product_number."' " .
				"WHERE exchange_id = '".$exchange_id."' AND exchange_sub_id = '".$exchange_sub_id."'";
		$query = $this->db->query($sql);

		if($this->db->affected_rows())
		{
			$sql = "UPDATE ".$this->db->dbprefix('transaction_info')." SET product_number = -".$product_number . " WHERE sub_id = ".$exchange_sub_id . " AND trans_sn = '".$exchange_code."' AND trans_status = ".TRANS_STAT_AWAIT_OUT;
			$this->db->query($sql);
		}
		return $this->db->affected_rows();
	}

	public function get_exchange_out_product_sub($exchange_sub_id)
	{
		$sql = "SELECT b.sub_id " .
				" FROM ".$this->db->dbprefix('exchange_out')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_sub')." b ON b.product_id = a.product_id AND b.color_id = a.color_id AND b.size_id = a.size_id".
				" WHERE a.exchange_sub_id = ? ";
		$param = array();
		$param[] = $exchange_sub_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		return isset($row->sub_id)?$row->sub_id:'';
	}

	public function del_exchange_out_product ($exchange_sub_id,$exchange_id,$exchange_code)
	{
		$rs = $this->delete_exchange_out_product(array('exchange_id'=>$exchange_id,'exchange_sub_id'=>$exchange_sub_id));
		if ($rs > 0)
		{
			$sql = "DELETE FROM ".$this->db->dbprefix('transaction_info')." WHERE trans_type = ".TRANS_TYPE_PACKET_EXCHANGE." AND trans_status = ".TRANS_STAT_AWAIT_OUT." AND trans_sn = '".$exchange_code."' AND sub_id = '".$exchange_sub_id."'";
			$query = $this->db->query($sql);
			$rs = $this->db->affected_rows();
		}
		return $rs;
	}

	public function exchange_in_products ($exchange_id)
	{
		$param = array();
		$sql = "SELECT a.*,x.sub_id,b.product_sn,b.product_name,b.provider_productcode,b.is_audit,b.is_onsale,b.is_stop,b.market_price," .
				" b.shop_price,b.promote_price,cost.consign_price,cost.cost_price,cost.consign_rate,cost.consign_type,c.provider_cooperation,cost.product_cess," .
				" c.provider_name,d.brand_name,e.color_name,e.color_sn,f.size_name,f.size_sn," .
				" h.location_code1,h.location_code2,h.location_code3,h.location_code4,h.location_name,g.depot_name,batch.batch_code " .
				" FROM ".$this->db->dbprefix('exchange_out')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_sub')." x ON x.product_id = a.product_id AND x.color_id = a.color_id AND x.size_id = a.size_id " .
				" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = b.provider_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." g ON g.depot_id = a.source_depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." h ON h.location_id = a.source_location_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_cost')." AS cost ON cost.product_id = a.product_id AND cost.batch_id = a.batch_id " .
				" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS batch ON batch.batch_id = a.batch_id " .
				" WHERE a.exchange_id = ? ORDER BY a.exchange_sub_id DESC ";
		$param[] = $exchange_id;

		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$return = array();
		if (!empty($list))
		{
			foreach ($list as $item)
			{
				if (!isset($return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id]))
				{
					$item->out_item = array();
					$item->in_item = array();
					$return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id] = $item;
				}
				$tmp = $return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id];
				$tmp->out_item[] = $item;
				$return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id] = $tmp;
			}
		}

		$param = array();
		$sql = "SELECT a.*,x.sub_id,b.product_sn,b.product_name,b.provider_productcode,b.is_audit,b.is_onsale,b.is_stop,b.market_price," .
				" b.shop_price,b.promote_price,cost.consign_price,cost.cost_price,cost.consign_rate,cost.consign_type,c.provider_cooperation,cost.product_cess," .
				" c.provider_name,d.brand_name,e.color_name,e.color_sn,f.size_name,f.size_sn," .
				" h.location_code1,h.location_code2,h.location_code3,h.location_code4,h.location_name,g.depot_name " .
				" FROM ".$this->db->dbprefix('exchange_in')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_sub')." x ON x.product_id = a.product_id AND x.color_id = a.color_id AND x.size_id = a.size_id " .
				" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = b.provider_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." g ON g.depot_id = a.dest_depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." h ON h.location_id = a.dest_location_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_cost')." AS cost ON cost.product_id = a.product_id AND cost.batch_id = a.batch_id " .
				" WHERE a.exchange_id = ? ORDER BY a.exchange_leaf_id ASC ";
		$param[] = $exchange_id;
		$query = $this->db->query($sql, $param);
		$list2 = $query->result();
		if (!empty($list2))
		{
			foreach ($list2 as $item)
			{
				if (!isset($return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id]))
				{
					$item->out_item = array();
					$item->in_item = array();
					$return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id] = $item;
				}
				$tmp = $return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id];
				$tmp->in_item[] = $item;
				$return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id] = $tmp;
			}
		}
		$return = $this->format_product_list($return);
		return $return;
	}

	public function insert_exchange_in_single ($sub_id,$product_number,$exchange_id,$depot_id,$location_id,$admin_id,$batch_id)
	{
		$sql = "SELECT a.exchange_leaf_id,c.exchange_code FROM ".$this->db->dbprefix('exchange_in')." a " .
				"LEFT JOIN ".$this->db->dbprefix('product_sub')." b ON a.product_id = b.product_id AND a.color_id = b.color_id AND a.size_id = b.size_id ".
				"LEFT JOIN ".$this->db->dbprefix('exchange_main')." c ON a.exchange_id = c.exchange_id ".
				"WHERE 1 AND b.sub_id = ? AND a.dest_depot_id = ? AND a.dest_location_id = ? AND a.exchange_id = ? AND a.batch_id= ?";
		$param = array();
		$param[] = $sub_id;
		$param[] = $depot_id;
		$param[] = $location_id;
		$param[] = $exchange_id;
		$param[] = $batch_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$data = array();
		if (!empty($row) && $row->exchange_leaf_id > 0)
		{
			/*
			$sql = "UPDATE ".$this->db->dbprefix('exchange_in')." SET product_number = product_number + ".$product_number . " WHERE exchange_leaf_id = ".$row->exchange_leaf_id;
			$this->db->query($sql);
			if ($this->db->affected_rows() == 0)
			{
				return false;
			}
			$sql = "UPDATE ".$this->db->dbprefix('transaction_info')." SET product_number = product_number + ".$product_number . " WHERE sub_id = ".$row->exchange_leaf_id . " AND trans_sn = '".$row->exchange_code."' AND trans_status = ".TRANS_STAT_AWAIT_IN;
			$this->db->query($sql);
			if (!$this->db->affected_rows())
			{
				return false;
			}
			return $row->exchange_leaf_id;
			*/
			return -1;
		} else
		{
			$sql = "INSERT INTO ".$this->db->dbprefix('exchange_in')." (exchange_id,product_id,color_id,size_id,dest_depot_id,dest_location_id,shop_price," .
					"product_number,create_admin,create_date,batch_id,expire_date,production_batch) " .
					"SELECT '".$exchange_id."',a.product_id,a.color_id,a.size_id,'".$depot_id."','".$location_id."',b.shop_price,".
					$product_number.",".$admin_id.",'".date('Y-m-d H:i:s')."',".$batch_id.",eo.expire_date,eo.production_batch " .
					"FROM ".$this->db->dbprefix('exchange_out')." eo "
                                        . "LEFT JOIN " . $this->db->dbprefix('product_sub') ." a ON a.product_id = eo.product_id AND a.color_id = eo.color_id AND a.size_id = eo.size_id " .
					"LEFT JOIN " . $this->db->dbprefix('product_info') . " b ON a.product_id = b.product_id ".
					"WHERE a.sub_id = '".$sub_id."' AND eo.exchange_id = '".$exchange_id."'";
                        
			$this->db->query($sql);
			$sub_id = $this->db->insert_id();
			$sub_id = $sub_id > 0?$sub_id:0;
			if ($sub_id == 0)
			{
				return false;
			}

			$sql = "INSERT INTO ".$this->db->dbprefix('transaction_info')."(trans_type,trans_status,trans_sn,product_id,color_id,size_id,product_number," .
					"depot_id,location_id,create_admin,create_date,update_admin,update_date,cancel_admin,cancel_date,trans_direction,sub_id,batch_id,"
                                        . "shop_price,consign_price,cost_price,consign_rate,product_cess,expire_date,production_batch) ".
					" SELECT ".TRANS_TYPE_PACKET_EXCHANGE.",".TRANS_STAT_AWAIT_IN.",b.exchange_code,a.product_id,a.color_id,size_id,a.product_number,".
					"a.dest_depot_id,a.dest_location_id,a.create_admin,a.create_date,0,'0000-00-00',0,'0000-00-00',1,a.exchange_leaf_id,a.batch_id,"
                                        . "a.shop_price,cost.consign_price,cost.cost_price,cost.consign_rate,cost.product_cess,a.expire_date,a.production_batch" .
					" FROM ".$this->db->dbprefix('exchange_in')." a" .
					" LEFT JOIN ".$this->db->dbprefix('exchange_main')." b ON b.exchange_id = a.exchange_id "
                                        . "LEFT JOIN ".$this->db->dbprefix('product_cost')." cost ON cost.product_id = a.product_id AND cost.batch_id = a.batch_id "
                                        . "WHERE a.exchange_leaf_id = '".$sub_id."' ";
			
                        $this->db->query($sql);
			if (!$this->db->insert_id())
			{
				return false;
			}
			return $sub_id;
		}
 	}

 	public function update_exchange_in_total ($exchange_id)
	{
		$sql = "SELECT SUM(product_number) AS product_number_t " .
				"FROM ".$this->db->dbprefix('exchange_in')." WHERE exchange_id = ? ";
		$param = array();
		$param[] = $exchange_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		if (empty($row))
		{
			$product_number_t = 0;
		} else
		{
			$product_number_t = $row->product_number_t;
		}
		$param2 = array();
		$sql = "UPDATE ".$this->db->dbprefix('exchange_main')." SET exchange_in_number = ? WHERE exchange_id = ? ";
		$param2[] = $product_number_t;
		$param2[] = $exchange_id;
		$query = $this->db->query($sql, $param2);
	}

	public function del_exchange_in_product ($exchange_leaf_id,$exchange_id,$exchange_code)
	{
		$rs = $this->delete_exchange_in_product(array('exchange_id'=>$exchange_id,'exchange_leaf_id'=>$exchange_leaf_id));
		if ($rs > 0)
		{
			$sql = "DELETE FROM ".$this->db->dbprefix('transaction_info')." WHERE trans_type = ".TRANS_TYPE_PACKET_EXCHANGE." AND trans_status = ".TRANS_STAT_AWAIT_IN." AND trans_sn = '".$exchange_code."' AND sub_id = '".$exchange_leaf_id."'";
			$query = $this->db->query($sql);
			$rs = $this->db->affected_rows();
		}
		return $rs;
	}

	public function delete_exchange_in_product ($where_arr)
	{
		$this->db->delete('exchange_in', $where_arr);
		return $this->db->affected_rows();
	}

	public function update_exchange_in_product_x ($exchange_leaf_id,$exchange_id,$product_number,$exchange_code)
	{
		$sql = "UPDATE ".$this->db->dbprefix('exchange_in')." " .
				"SET product_number = '".$product_number."' " .
				"WHERE exchange_id = '".$exchange_id."' AND exchange_leaf_id = '".$exchange_leaf_id."'";
		$query = $this->db->query($sql);

		if($this->db->affected_rows())
		{
			$sql = "UPDATE ".$this->db->dbprefix('transaction_info')." SET product_number = ".$product_number . " WHERE sub_id = ".$exchange_leaf_id . " AND trans_sn = '".$exchange_code."' AND trans_status = ".TRANS_STAT_AWAIT_IN;
			$this->db->query($sql);
		}
		return $this->db->affected_rows();
	}

	public function format_product_list($list)
	{
		if (!empty($list))
		{
			foreach ($list as $key => $item)
			{
				if ($item->consign_type == 0)
				{
					$item->provider_price = $item->cost_price;
				} elseif ($item->consign_type == 1)
				{
					$item->provider_price = $item->consign_price;
				} elseif ($item->consign_type == 2)
				{
					$item->provider_price = $item->shop_price * (1+$item->consign_rate);
				}
				if (isset($item->is_on_sale))
				{
					$item->status_name = ($item->is_on_sale == 1)? '上架':'下架';
				}

				$list[$key] = $item;
			}
		}
		return $list;
	}

}
###