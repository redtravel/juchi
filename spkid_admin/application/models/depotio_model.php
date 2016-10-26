<?php
#doc
#	classname:	Depotio_model
#	scope:		PUBLIC
#
#/doc

class Depotio_model extends CI_Model
{

	public function query_products_out ($filter, $has_empty=true)
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

		if (!empty($filter['provider_barcode']))
		{
			$where .= " AND c.provider_barcode = ? ";
			$param[] = $filter['provider_barcode'];
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

		//批次选择列表 @baolm
		if (!empty($filter['batch_id']))
		{
			$where .= " AND a.batch_id = ? ";
			$param[] = $filter['batch_id'];
		}

		if (!empty($filter['cooperation_id']))
		{
			$where .= " AND b.cooperation_id = ? ";
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
			$where .= " AND NOT EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('depot_out_sub')." s WHERE s.depot_out_id = ? AND s.product_id = a.product_id" .
					" AND s.color_id = a.color_id AND s.size_id = a.size_id AND s.depot_id = a.depot_id AND s.location_id = a.location_id) ";
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
					" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON a.batch_id = pb.batch_id " .
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
					" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON a.batch_id = pb.batch_id " .
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

		//b.consign_price,b.cost_price,b.consign_rate,b.consign_type,b.cooperation_id,b.goods_cess,
		$sql = "SELECT a.*,b.product_sn,b.product_name,b.provider_productcode,b.is_audit,b.is_stop,b.market_price,c.provider_barcode,c.gl_num,c.is_on_sale, a.batch_id, " .
				" b.shop_price,b.promote_price," .
				" pc.provider_name,d.brand_name,e.color_name,e.color_sn,f.size_name,f.size_sn,h.depot_name,i.location_name,i.location_code1,i.location_code2,i.location_code3,i.location_code4 " .
				" ,pb.batch_code " .
				" FROM ".$this->db->dbprefix('transaction_info')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_sub')." c ON c.product_id = a.product_id AND c.color_id=a.color_id AND c.size_id=a.size_id".
				" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." h ON h.depot_id = a.depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." i ON i.location_id = a.location_id" .
				" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON a.batch_id = pb.batch_id " .
				" LEFT JOIN ".$this->db->dbprefix('product_provider')." pc ON pc.provider_id = pb.provider_id" .
				" WHERE a.trans_status IN (1,2,4) AND CONCAT(a.product_id,'-',a.color_id,'-',a.size_id) ".db_create_in($goods_id_arr).$where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'];
			
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$return = array();
		$help = array();
		if (!empty($list))
		{
			foreach ($list as $item)
			{
				if (!isset($return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id]))
				{
					$item->total_can_out_num = 0;
					$item->item = array();
					$return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id] = $item;
				}
				$total_tmp = $return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id];
				if ($item->trans_sn != $filter['trans_sn'])
				{
					$total_tmp->total_can_out_num += $item->product_number;
				}
				$return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id] = $total_tmp;

				$tmp1 = $return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id];
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

				$return[$item->product_id.'-'.$item->color_id.'-'.$item->size_id.'-'.$item->batch_id] = $tmp1;
			}
		}

		$return = $this->format_product_list($return, $has_empty);
		$query->free_result();
		return array('list' => $return, 'filter' => $filter);
	}

	public function depot_in_spec_type_list ()
	{
		$sql = "SELECT a.depot_type_id,a.depot_type_code,a.depot_type_name,a.depot_type_special FROM ".$this->db->dbprefix('depot_iotype')." a WHERE a.is_use = 1 AND a.depot_type_out = 0 AND a.depot_type_special > 0";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		$rs = array();
		foreach ($list as $row)
		{
			$rs[$row->depot_type_special] = $row->depot_type_id;
		}
		return $rs;
	}

	public function query_products_in ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_sub')." AS a ";
		$where = " WHERE 1 AND b.is_audit = 1 AND pb.batch_status = 1 "; //商品已审核，未停止订货
		$cloumns = "";
		$param = array();
		
		if (!empty($filter['provider_barcode']))
		{
			$where .= " AND a.provider_barcode = ? ";
			$param[] = $filter['provider_barcode'];
		}

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
			$where .= " AND c.provider_id = ? ";
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
		$type=0;
		if (!empty($filter['depot_in_id']))
		{
			$spec_type = $this->depot_in_spec_type_list();
			$depot_in_info = $this->filter_depot_in(array('depot_in_id'=>$filter['depot_in_id']));
			if ($depot_in_info->depot_in_type == $spec_type['1'])
			{
				$type=1; //purchase
				//$where .= " AND EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('purchase_sub')." p WHERE p.purchase_id = '".$depot_in_info->order_id."' AND p.product_id = a.product_id AND p.color_id = a.color_id AND p.size_id = a.size_id) ";
				$where .= " AND p.purchase_id = ? ";
				$param[] = $depot_in_info->order_id;
				$cloumns = ",pb.batch_id,pb.batch_code,pb.is_reckoned,c.provider_name ";
			} elseif ($depot_in_info->depot_in_type == $spec_type['2'])
			{
				$type=2; //depot out
				//$where .= " AND EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('depot_out_sub')." p WHERE p.depot_out_id = '".$depot_in_info->order_id."' AND p.product_id = a.product_id AND p.color_id = a.color_id AND p.size_id = a.size_id) ";
				$where .= " AND p.depot_out_id = ? ";
				$param[] = $depot_in_info->order_id;
				$cloumns = ",pb.batch_id,pb.batch_code,pb.is_reckoned,c.provider_name ";
			} elseif ($depot_in_info->depot_in_type == $spec_type['4'])
			{
				$type=4; //调拨入库
				$where .= " AND p.depot_out_id = ? ";
				$param[] = $depot_in_info->order_id;
				$cloumns = ",pb.batch_id,pb.batch_code,pb.is_reckoned,c.provider_name ";
			} else {
				//TODO other //盘点入
				$cloumns = ",pb.batch_id,pb.batch_code,pb.is_reckoned,c.provider_name ";
			}
			$filter['type'] = $type;
		}

                //去除已添加的
		if (!empty($filter['with_not']))
		{
			$where .= " AND NOT EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('depot_in_sub')." s WHERE s.depot_in_id = ? AND s.product_id = a.product_id AND s.color_id = a.color_id AND s.size_id = a.size_id) ";
			$param[] = $filter['with_not'];
		}


		$filter['sort_by'] = empty($filter['sort_by']) ? 'b.brand_id ASC,b.product_id ASC,a.color_id ASC,a.size_id ASC ' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? '' : trim($filter['sort_order']);


		$sql = "SELECT COUNT(*) AS ct FROM " . $this->db->dbprefix('product_sub') . " a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
				//" LEFT JOIN ".$this->db->dbprefix('product_cost')." pc ON b.product_id = pc.product_id" .
				//" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = pc.provider_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id";
		if($type==1) {
			$sql .= " LEFT JOIN ".$this->db->dbprefix('purchase_sub')." p ON p.product_id = a.product_id AND p.color_id = a.color_id AND p.size_id = a.size_id" .
				" LEFT JOIN ".$this->db->dbprefix('purchase_main')." pm ON pm.purchase_id = p.purchase_id" .
				" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON pb.batch_id = pm.batch_id" .
                                " LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = pb.provider_id" ;
		}
		elseif($type==2 || $type==4) {
			$sql .= " LEFT JOIN ".$this->db->dbprefix('depot_out_sub')." p ON p.product_id = a.product_id AND p.color_id = a.color_id AND p.size_id = a.size_id" .
			" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON pb.batch_id = p.batch_id" .
                        " LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = pb.provider_id" ;
 		} else {
 			$sql .= " LEFT JOIN ".$this->db->dbprefix('product_cost')." p ON p.product_id = a.product_id " .
 					" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON pb.batch_id = p.batch_id " .
 					" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = pb.provider_id " ;
		}
                
		$sql .= $where;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}

		//b.consign_price,b.cost_price,b.consign_rate,b.consign_type,b.cooperation_id,b.goods_cess,
		$sql = "SELECT a.*,b.product_sn,b.pack_method,b.package_name,b.product_name,b.provider_productcode,b.is_audit,b.is_stop,b.market_price," .
				" b.shop_price,b.promote_price,d.brand_name,e.color_name,e.color_sn,f.size_name,f.size_sn,l.location_name" . $cloumns .
				" FROM " . $this->db->dbprefix('product_sub') . " a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
				//" LEFT JOIN ".$this->db->dbprefix('product_cost')." pc ON b.product_id = pc.product_id" .
				//" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = pc.provider_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
                                " LEFT JOIN (SELECT product_id,color_id,size_id,location_id FROM ".$this->db->dbprefix('transaction_info').
                                    " WHERE trans_status = 2 GROUP BY size_id,color_id,product_id ORDER BY create_date DESC) AS t ON t.product_id = a.product_id AND t.color_id = a.color_id AND t.size_id = a.size_id ".
                                " LEFT JOIN ".$this->db->dbprefix('location_info')." l ON l.location_id = t.location_id";
		if($type==1) {
			$sql .= " LEFT JOIN ".$this->db->dbprefix('purchase_sub')." p ON p.product_id = a.product_id AND p.color_id = a.color_id AND p.size_id = a.size_id" .
				" LEFT JOIN ".$this->db->dbprefix('purchase_main')." pm ON pm.purchase_id = p.purchase_id" .
				" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON pb.batch_id = pm.batch_id" .
                                " LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = pb.provider_id" ;
		} 
		if($type==2 || $type==4) {
			$sql .= " LEFT JOIN ".$this->db->dbprefix('depot_out_sub')." p ON p.product_id = a.product_id AND p.color_id = a.color_id AND p.size_id = a.size_id" .
				" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON pb.batch_id = p.batch_id" .
                                " LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = pb.provider_id" ;
		} else {
			$sql .= " LEFT JOIN ".$this->db->dbprefix('product_cost')." ps ON ps.product_id = a.product_id " .
					" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON pb.batch_id = ps.batch_id " .
					" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = pb.provider_id " ;
		}
		
		$sql .= $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'].
				" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();

		$source = array();
		$ode = array();
		if ($type == 2 || $type==4)
		{
			// 出库入库时删除重复的记录（从出库单关联时，同个SKU由于出库储位不同会有多条记录）
			if (!empty($list))
			{
				$temp = array();
				foreach ($list as $key =>$item)
				{
					$keyitem = $item->product_id.'-'.$item->color_id.'.'.$item->size_id.'_'.$item->batch_id;
					if(isset($temp[$keyitem])) {
						unset($list[$key]);
					} else {
						$temp[$keyitem] = $list;
					}
				}
			}
			
			$sql = "SELECT b.product_id,b.color_id,b.size_id,SUM(b.product_number) as max_num FROM ".$this->db->dbprefix('depot_out_main')." a LEFT JOIN ".$this->db->dbprefix('depot_out_sub')." b ON b.depot_out_id = a.depot_out_id WHERE a.depot_out_code = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $filter['order_sn'];
			$query = $this->db->query($sql, $param);
			$source_arr = $query->result();
			if (!empty($source_arr))
			{
				foreach ($source_arr as $item)
				{
					$source[$item->product_id.'-'.$item->color_id.'.'.$item->size_id] = empty($item->max_num)?0:$item->max_num;
				}
			}
			$query->free_result();

			$sql = "SELECT b.product_id,b.color_id,b.size_id,SUM(b.product_number) as had_num FROM ".$this->db->dbprefix('depot_in_main')." a LEFT JOIN ".$this->db->dbprefix('depot_in_sub')." b ON b.depot_in_id = a.depot_in_id WHERE a.order_sn = ? AND a.depot_in_id <> ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $filter['order_sn'];
			$param[] = $filter['depot_in_id'];
			$query = $this->db->query($sql, $param);
			$ode_arr = $query->result();
			if (!empty($ode_arr))
			{
				foreach ($ode_arr as $item)
				{
					$ode[$item->product_id.'-'.$item->color_id.'.'.$item->size_id] = empty($item->had_num)?0:$item->had_num;
				}
			}
			$query->free_result();
		} elseif ($type == 1)
		{
			$sql = "SELECT b.product_id,b.color_id,b.size_id,SUM(b.product_number) as max_num FROM ".$this->db->dbprefix('purchase_main')." a LEFT JOIN ".$this->db->dbprefix('purchase_sub')." b ON b.purchase_id = a.purchase_id WHERE a.purchase_code = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $filter['order_sn'];
			$query = $this->db->query($sql, $param);
			$source_arr = $query->result();
			if (!empty($source_arr))
			{
				foreach ($source_arr as $item)
				{
					$source[$item->product_id.'-'.$item->color_id.'.'.$item->size_id] = empty($item->max_num)?0:$item->max_num;
				}
			}
			$query->free_result();

			$sql = "SELECT b.product_id,b.color_id,b.size_id,SUM(b.product_number) as had_num FROM ".$this->db->dbprefix('depot_in_main')." a LEFT JOIN ".$this->db->dbprefix('depot_in_sub')." b ON b.depot_in_id = a.depot_in_id WHERE a.order_sn = ? AND a.depot_in_id <> ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $filter['order_sn'];
			$param[] = $filter['depot_in_id'];
			$query = $this->db->query($sql, $param);
			$ode_arr = $query->result();
			if (!empty($ode_arr))
			{
				foreach ($ode_arr as $item)
				{
					$ode[$item->product_id.'-'.$item->color_id.'.'.$item->size_id] = empty($item->had_num)?0:$item->had_num;
				}
			}
			$query->free_result();
		}
		if (!empty($list))
		{
			foreach ($list as $key =>$item)
			{
				$list[$key]->max_num = 'big';
				$keyitem = $item->product_id.'-'.$item->color_id.'.'.$item->size_id;
				if (isset($source[$keyitem]))
				{
					$ode_num = isset($ode[$keyitem])?$ode[$keyitem]:0;
					$list[$key]->max_num = $source[$keyitem]-$ode_num;
					if($type == 4) {
						$list[$key]->need_in_num = $list[$key]->max_num;
					}
				}
			}
		}
		$list = $this->format_product_list($list);
		return array('list' => $list, 'filter' => $filter);

	}

	public function format_product_list($list, $has_empty=true)
	{
		if (!empty($list))
		{
			foreach ($list as $key => $item)
			{
				/*if ($item->consign_type == 0)
				{
					$item->provider_price = $item->cost_price;
				} elseif ($item->consign_type == 1)
				{
					$item->provider_price = $item->consign_price;
				} elseif ($item->consign_type == 2)
				{
					$item->provider_price = $item->shop_price * (1+$item->consign_rate);
				}*/
				if (isset($item->is_on_sale))
				{
					$item->status_name = ($item->is_on_sale == 1)? '上架':'下架';
				}
				$list[$key] = $item;
				if (!$has_empty && $item->total_can_out_num<=0){
					unset($list[$key]);
				}
			}
		}
		return $list;
	}

	public function depot_out_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('depot_out_main')." AS a ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['depot_out_code']))
		{
			$where .= " AND a.depot_out_code LIKE ? ";
			$param[] = '%' . $filter['depot_out_code'] . '%';
		}

		if (!empty($filter['provider_id']))
		{
			$where .= " AND a.provider_id = ? ";
			$param[] = $filter['provider_id'];
		}

		if (!empty($filter['depot_depot_id']))
		{
			$where .= " AND a.depot_depot_id = ? ";
			$param[] = $filter['depot_depot_id'];
		}

		if (!empty($filter['depot_out_status']))
		{
			if ($filter['depot_out_status'] == 1)
			{
				$where .= " AND a.audit_admin = 0 ";
			} elseif ($filter['depot_out_status'] == 2)
			{
				$where .= " AND a.audit_admin > 0 ";
			}

		}
		if (!empty($filter['depot_out_type']))
		{
			$where .= " AND a.depot_out_type = ? ";
			$param[] = $filter['depot_out_type'];
		}

		if (!empty($filter['provider_goods']))
		{
			$where .= " AND EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('depot_out_sub')." s, ".$this->db->dbprefix('product_info')." v WHERE a.depot_out_id = s.depot_out_id AND s.product_id = v.product_id AND (v.product_name LIKE ? OR v.product_sn LIKE ? )) ";
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.depot_out_id' : trim($filter['sort_by']);
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
		$sql = "SELECT a.*,b.depot_type_code,b.depot_type_special,b.depot_type_out,b.depot_type_name,di.depot_name,c.provider_name,e.admin_name AS create_name," .
				"g.admin_name AS audit_name,h.admin_name as lock_name,inv.inventory_id " .
				$from .
				" LEFT JOIN ".$this->db->dbprefix('depot_iotype')." b ON b.depot_type_id = a.depot_out_type" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." di ON di.depot_id = a.depot_depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = a.provider_id" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." e ON e.admin_id = a.create_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." g ON g.admin_id = a.audit_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." h ON h.admin_id = a.lock_admin" .
				" LEFT JOIN ".$this->db->dbprefix('depot_inventory')." inv ON inv.depot_out_sn = a.depot_out_code" .
				 $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		if (!empty($list))
		{
			foreach ($list as $key=>$depot_info)
			{

				if ($depot_info->audit_admin == 0)
				{
					$depot_info->depot_status = 1; //未审核
					$depot_info->depot_status_name = '未审核';
				} elseif ($depot_info->audit_admin > 0)
				{
					$depot_info->depot_status = 2; //已审核
					$depot_info->depot_status_name = '已审核';
				}
				if(empty($depot_info->depot_status_name))
				    $depot_info->depot_status_name="";
				if ($depot_info->lock_admin > 0)
				{
					$depot_info->depot_status_name = "已锁,".$depot_info->depot_status_name;
				} else
				{
					$depot_info->depot_status_name = "未锁,".$depot_info->depot_status_name;
				}

				$list[$key] = $depot_info;
			}
		}

		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function get_depot_out_code()
	{
		return 'CK'.date('YmdHis');
	}

	public function filter_depot_out ($filter)
	{
		$query = $this->db->get_where('depot_out_main', $filter, 1);
		return $query->row();
	}

	public function get_depot_out_info ($depot_out_id)
	{
		$param = array();
		$sql = "a.*,b.depot_type_code,b.depot_type_special,b.depot_type_out,b.depot_type_name,di.depot_name,c.provider_name,e.admin_name AS create_name," .
				"g.admin_name AS audit_name,h.admin_id as lock_name " .
				" FROM ".$this->db->dbprefix('depot_out_main')." a" .
				" LEFT JOIN ".$this->db->dbprefix('depot_iotype')." b ON b.depot_type_id = a.depot_out_type" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." di ON di.depot_id = a.depot_depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = a.provider_id" .
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

	public function depot_out_products ($depot_out_id,$query_barcode= FALSE)
	{
		$param = array();
		$select = "SELECT a.*,SUM(t.product_number) AS real_num,b.product_sn,b.product_name,b.provider_productcode,b.is_audit,b.is_onsale,b.is_stop," .
				" b.shop_price,b.promote_price," .
				" c.provider_name,d.brand_name,e.color_name,e.color_sn,f.size_name,f.size_sn," .
				" h.location_code1,h.location_code2,h.location_code3,h.location_code4,h.location_name,g.depot_name " .
				" ,pb.batch_code,a.expire_date,a.production_batch " ;
		$from = " FROM ".$this->db->dbprefix('depot_out_sub')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." g ON g.depot_id = a.depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." h ON h.location_id = a.location_id" .
				" LEFT JOIN ".$this->db->dbprefix('transaction_info')." t ON t.product_id = a.product_id AND t.color_id = a.color_id AND t.size_id = a.size_id" .
				" AND t.depot_id = a.depot_id AND t.location_id = a.location_id AND t.batch_id=a.batch_id AND t.trans_status IN (1,2,4) " .
				" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON a.batch_id = pb.batch_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = pb.provider_id" ;
		if($query_barcode){
		    $select .= ",psub.provider_barcode ";
		    $from .= " LEFT JOIN ".$this->db->dbprefix('product_sub')." psub ON psub.product_id = a.product_id AND psub.color_id = a.color_id AND psub.size_id = a.size_id";
		}
		$where =" WHERE a.depot_out_id = ? GROUP BY a.depot_out_sub_id ORDER BY location_name";
		$sql = $select . $from . $where ;
		$param[] = $depot_out_id;
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$list = $this->format_product_list($list);
		return $list;
	}

	public function update_depot_out ($data, $depot_out_id)
	{
		$this->db->update('depot_out_main', $data, array('depot_out_id' => $depot_out_id));
	}

	public function insert_depot_out ($data)
	{
		$this->db->insert('depot_out_main', $data);
		return $this->db->insert_id();
	}

	public function delete_depot_out ($depot_out_id)
	{
		$this->db->delete('depot_out_main', array('depot_out_id' => $depot_out_id));
		return $this->db->affected_rows();
	}

	public function update_depot_out_product ($data, $where_arr)
	{
		$this->db->update('depot_out_sub', $data, $where_arr);
	}

	public function insert_depot_out_product ($data)
	{
		$this->db->insert('depot_out_sub', $data);
		return $this->db->insert_id();
	}

	public function delete_depot_out_product ($where_arr)
	{
		$this->db->delete('depot_out_sub', $where_arr);
		return $this->db->affected_rows();
	}

	public function format_depot_out_info($depot_out_info)
	{
		$sql = "SELECT a.admin_id,a.admin_name FROM ".$this->db->dbprefix('admin_info')." a WHERE a.user_status = 1";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();

		$sql = "SELECT COUNT(*) as tot FROM ".$this->db->dbprefix('depot_out_sub')." WHERE depot_out_id = '".$depot_out_info->depot_out_id."'";
		$query = $this->db->query($sql);
		$result = $query->row();
		$query->free_result();
		$rs = array();
		foreach ($list as $row)
		{
			$rs[$row->admin_id] = $row->admin_name;
		}

		if ($depot_out_info->lock_admin == 0)
		{
			$depot_out_info->lock_status_name = '未锁定';
			$depot_out_info->lock_name = '';
		} else
		{
			$depot_out_info->lock_status_name = '已锁定';
			$depot_out_info->lock_name = $rs[$depot_out_info->lock_admin];
		}


		if ($depot_out_info->audit_admin == 0)
		{
			$depot_out_info->depot_out_status = 1; //未审核
			$depot_out_info->depot_out_status_name = '未审核';
			$depot_out_info->oper_name = $rs[$depot_out_info->create_admin];
		} elseif ($depot_out_info->audit_admin > 0)
		{
			$depot_out_info->depot_out_status = 2; //已审核
			$depot_out_info->depot_out_status_name = '已审核';
			$depot_out_info->oper_name = $rs[$depot_out_info->audit_admin];
		}

		if(!empty($result) && $result->tot > 0)
		{
			$depot_out_info->has_product = 1;
		} else
		{
			$depot_out_info->has_product = 0;
		}

		return $depot_out_info;
	}

	public function depot_in_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('depot_in_main')." AS a ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['depot_in_code']))
		{
			$where .= " AND a.depot_in_code LIKE ? ";
			$param[] = '%' . $filter['depot_in_code'] . '%';
		}

		if (!empty($filter['depot_depot_id']))
		{
			$where .= " AND a.depot_depot_id = ? ";
			$param[] = $filter['depot_depot_id'];
		}

		if (!empty($filter['depot_in_status']))
		{
			if ($filter['depot_in_status'] == 1)
			{
				$where .= " AND a.audit_admin = 0 ";
			} elseif ($filter['depot_in_status'] == 2)
			{
				$where .= " AND a.audit_admin > 0 ";
			}

		}

		if (!empty($filter['depot_in_type']))
		{
			$where .= " AND a.depot_in_type = ? ";
			$param[] = $filter['depot_in_type'];
		}


		if (!empty($filter['provider_goods']))
		{
			$where .= " AND EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('depot_in_sub')." s, ".$this->db->dbprefix('product_info')." v WHERE a.depot_in_id = s.depot_in_id AND s.product_id = v.product_id AND (v.product_name LIKE ? OR v.product_sn LIKE ? )) ";
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
		}

		if (!empty($filter['provider_productcode']))
		{
			$where .= " AND EXISTS(
							SELECT 1 FROM ".$this->db->dbprefix('depot_in_sub')." s 
							LEFT JOIN ".$this->db->dbprefix('product_info')." v ON s.product_id = v.product_id 
							WHERE a.depot_in_id = s.depot_in_id AND v.provider_productcode = ?
						) ";
			$param[] = $filter['provider_productcode'];
		}

		if (!empty($filter['provider_barcode']))
		{
			$where .= " AND EXISTS(
							SELECT 1 FROM ".$this->db->dbprefix('depot_in_sub')." s 
							LEFT JOIN ".$this->db->dbprefix('product_sub')." v ON s.product_id = v.product_id AND s.color_id=v.color_id AND s.size_id=v.size_id 
							WHERE a.depot_in_id = s.depot_in_id AND v.provider_barcode = ?
						) ";
			$param[] = $filter['provider_barcode'];
		}

		if (!empty($filter['brand_id']))
		{
			$where .= " AND EXISTS(
							SELECT 1 FROM ".$this->db->dbprefix('depot_in_sub')." s 
							LEFT JOIN ".$this->db->dbprefix('product_info')." v ON s.product_id=v.product_id 
							WHERE a.depot_in_id = s.depot_in_id AND v.brand_id = ?
						) ";
			$param[] = $filter['brand_id'];
		}

		if (!empty($filter['box_code']))
		{
			$where .= " AND EXISTS(
							SELECT 1 FROM ".$this->db->dbprefix('purchase_box_main')." s 
							WHERE a.order_id = s.box_id AND a.depot_in_type = 11 AND s.box_code = ?
						) ";
			$param[] = $filter['box_code'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.depot_in_id' : trim($filter['sort_by']);
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
		$sql = "SELECT a.*,b.depot_type_code,b.depot_type_special,b.depot_type_out,b.depot_type_name,di.depot_name,e.admin_name AS create_name," .
				"g.admin_name AS audit_name,h.admin_name as lock_name,inv.inventory_id " .
				",box.box_code" .
				$from .
				" LEFT JOIN ".$this->db->dbprefix('depot_iotype')." b ON b.depot_type_id = a.depot_in_type" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." di ON di.depot_id = a.depot_depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." e ON e.admin_id = a.create_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." g ON g.admin_id = a.audit_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." h ON h.admin_id = a.lock_admin" .
				" LEFT JOIN ".$this->db->dbprefix('purchase_box_main')." box ON a.order_id = box.box_id AND a.depot_in_type = 11" .
				" LEFT JOIN ".$this->db->dbprefix('depot_inventory')." inv ON inv.depot_in_sn = a.depot_in_code" .
				 $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		if (!empty($list))
		{
			foreach ($list as $key=>$depot_info)
			{

				if ($depot_info->audit_admin == 0)
				{
					$depot_info->depot_status = 1; //未审核
					$depot_info->depot_status_name = '未审核';
				} elseif ($depot_info->audit_admin > 0 || $depot_info->audit_admin == -1)
				{
					$depot_info->depot_status = 2; //已审核
					$depot_info->depot_status_name = '已审核';
				}

				if ($depot_info->lock_admin > 0 || $depot_info->lock_admin == -1)
				{
					$depot_info->depot_status_name = "已锁,".$depot_info->depot_status_name;
				} else
				{
					$depot_info->depot_status_name = "未锁,".$depot_info->depot_status_name;
				}

				$list[$key] = $depot_info;
			}
		}

		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function get_depot_in_code()
	{
		return 'RK'.date('YmdHis');
	}

	public function filter_depot_in ($filter)
	{
		$query = $this->db->get_where('depot_in_main', $filter, 1);
		return $query->row();
	}

	public function filter_depot_in_fix ($order_sn,$depot_in_id)
	{
		$query = $this->db->query('SELECT * FROM '.$this->db->dbprefix('depot_in_main')." WHERE order_sn = '".$order_sn."' AND audit_admin = 0 AND depot_in_id != '".$depot_in_id."' LIMIT 1");
		return $query->row();
	}

	public function filter_depot_in_sub_x ($depot_in_id,$depot_in_sub_id)
	{
		$sql = "SELECT a.*,b.depot_in_code,c.sub_id AS product_sub_id,b.order_sn FROM ".$this->db->dbprefix('depot_in_sub')." a" .
				" LEFT JOIN ".$this->db->dbprefix('depot_in_main')." b ON a.depot_in_id = b.depot_in_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_sub')." c ON c.product_id = a.product_id AND c.color_id = a.color_id AND c.size_id = a.size_id" .
				" WHERE a.depot_in_id = ? AND a.depot_in_sub_id = ? ";
		$param = array();
		$param[] = $depot_in_id;
		$param[] = $depot_in_sub_id;
		$query = $this->db->query($sql, $param);
		return $query->row();
	}

	public function get_depot_in_info ($depot_in_id)
	{
		$param = array();
		$sql = "a.*,b.depot_type_code,b.depot_type_special,b.depot_type_out,b.depot_type_name,di.depot_name,c.provider_name,e.admin_name AS create_name," .
				"g.admin_name AS audit_name,h.admin_id as lock_name " .
				" FROM ".$this->db->dbprefix('depot_in_main')." a" .
				" LEFT JOIN ".$this->db->dbprefix('depot_iotype')." b ON b.depot_type_id = a.depot_out_type" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." di ON di.depot_id = a.depot_depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = a.provider_id" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." e ON e.admin_id = a.create_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." g ON g.admin_id = a.audit_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." h ON h.admin_id = a.lock_admin" .
				" WHERE 1 ";
		if (empty($depot_in_id))
		{
			$depot_in_id = 0;
		}
		$sql .= " AND a.depot_in_id = ? LIMIT 1";
		$param[] = $depot_in_id;

		$query = $this->db->query($sql, $param);
		return $query->row();
	}

	public function depot_in_products ($depot_in_id,$order_sn)
	{
		$param = array();
		//b.consign_price,b.cost_price,b.consign_rate,b.consign_type,b.cooperation_id,b.goods_cess,
		$sql = "SELECT a.*,b.pack_method,b.package_name, b.product_weight, b.product_sn,b.product_name,b.provider_productcode,b.is_audit,b.is_onsale,b.is_stop,b.market_price," .
				" b.shop_price,b.promote_price," .
				" c.provider_name,d.brand_name,e.color_name,e.color_sn,f.size_name,f.size_sn," .
				" h.location_code1,h.location_code2,h.location_code3,h.location_code4,h.location_name,g.depot_name,x.sub_id,x.provider_barcode " .
				" ,pb.batch_code ".
				" FROM ".$this->db->dbprefix('depot_in_sub')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_brand')." d ON d.brand_id = b.brand_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." g ON g.depot_id = a.depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." h ON h.location_id = a.location_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_sub')." x ON x.product_id = a.product_id AND x.color_id = a.color_id AND x.size_id = a.size_id" .
				" LEFT JOIN ".$this->db->dbprefix('purchase_batch')." pb ON pb.batch_id = a.batch_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_provider')." c ON c.provider_id = pb.provider_id" .
				" WHERE a.depot_in_id = ? ORDER BY a.depot_in_sub_id DESC ";
		$param[] = $depot_in_id;

		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();

		$type=0;
		if (!empty($order_sn))
		{
			if(substr($order_sn,0,2) == 'CK')
			{
				$type=2; //depot out
			}
			if(substr($order_sn,0,2) == 'CG')
			{
				$type=1; //purchase
			}
		}
		$source = array();
		$ode = array();
		if ($type == 2)
		{
			$sql = "SELECT b.product_id,b.color_id,b.size_id,SUM(b.product_number) as max_num FROM ".$this->db->dbprefix('depot_out_main')." a LEFT JOIN ".$this->db->dbprefix('depot_out_sub')." b ON b.depot_out_id = a.depot_out_id WHERE a.depot_out_code = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $order_sn;
			$query = $this->db->query($sql, $param);
			$source_arr = $query->result();
			if (!empty($source_arr))
			{
				foreach ($source_arr as $item)
				{
					$source[$item->product_id.'-'.$item->color_id.'.'.$item->size_id] = empty($item->max_num)?0:$item->max_num;
				}
			}
			$query->free_result();

			$sql = "SELECT b.product_id,b.color_id,b.size_id,SUM(b.product_number) as had_num FROM ".$this->db->dbprefix('depot_in_main')." a LEFT JOIN ".$this->db->dbprefix('depot_in_sub')." b ON b.depot_in_id = a.depot_in_id WHERE a.order_sn = ? AND a.depot_in_id <> ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $order_sn;
			$param[] = $depot_in_id;
			$query = $this->db->query($sql, $param);
			$ode_arr = $query->result();
			if (!empty($ode_arr))
			{
				foreach ($ode_arr as $item)
				{
					$ode[$item->product_id.'-'.$item->color_id.'.'.$item->size_id] = empty($item->had_num)?0:$item->had_num;
				}
			}
			$query->free_result();
		} elseif ($type == 1)
		{
			$sql = "SELECT b.product_id,b.color_id,b.size_id,SUM(b.product_number) as max_num FROM ".$this->db->dbprefix('purchase_main')." a LEFT JOIN ".$this->db->dbprefix('purchase_sub')." b ON b.purchase_id = a.purchase_id WHERE a.purchase_code = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $order_sn;
			$query = $this->db->query($sql, $param);
			$source_arr = $query->result();
			if (!empty($source_arr))
			{
				foreach ($source_arr as $item)
				{
					$source[$item->product_id.'-'.$item->color_id.'.'.$item->size_id] = empty($item->max_num)?0:$item->max_num;
				}
			}
			$query->free_result();

			$sql = "SELECT b.product_id,b.color_id,b.size_id,SUM(b.product_number) as had_num FROM ".$this->db->dbprefix('depot_in_main')." a LEFT JOIN ".$this->db->dbprefix('depot_in_sub')." b ON b.depot_in_id = a.depot_in_id WHERE a.order_sn = ? AND a.depot_in_id <> ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $order_sn;
			$param[] = $depot_in_id;
			$query = $this->db->query($sql, $param);
			$ode_arr = $query->result();
			if (!empty($ode_arr))
			{
				foreach ($ode_arr as $item)
				{
					$ode[$item->product_id.'-'.$item->color_id.'.'.$item->size_id] = empty($item->had_num)?0:$item->had_num;
				}
			}
			$query->free_result();
		}
		$final = array();
		if (!empty($list))
		{
			foreach ($list as $item)
			{
				$key = $item->product_id.'-'.$item->color_id.'.'.$item->size_id;
				if (!isset($final[$key]))
				{
					$final[$key] = $item;
					if ($type == 0)
					{
						$final[$key]->max_num = "不限";
						$final[$key]->check_num = "nolimit";
					} else
					{
						$source[$key] = isset($source[$key])?$source[$key]:0;
						$ode[$key] = isset($ode[$key])?$ode[$key]:0;
						$final[$key]->max_num = $source[$key]-$ode[$key];
						$final[$key]->check_num = $final[$key]->max_num;
					}
					$final[$key]->item = array();
				}
				$tmp_arr = $final[$key]->item;
				$tmp_arr[] = $item;
				$final[$key]->item = $tmp_arr;
				if ($type > 0)
				{
					$final[$key]->check_num = $final[$key]->check_num - $item->product_number;
				}
			}
		}

		return $final;
	}

	public function update_depot_in ($data, $depot_in_id)
	{
		$this->db->update('depot_in_main', $data, array('depot_in_id' => $depot_in_id));
	}

	public function insert_depot_in ($data)
	{
		$this->db->insert('depot_in_main', $data);
		return $this->db->insert_id();
	}

	public function delete_depot_in ($depot_in_id)
	{
		$this->db->delete('depot_in_main', array('depot_in_id' => $depot_in_id));
		return $this->db->affected_rows();
	}

	public function update_depot_in_product ($data, $where_arr)
	{
		$this->db->update('depot_in_sub', $data, $where_arr);
	}

	public function insert_depot_in_product ($data)
	{
		$this->db->insert('depot_in_sub', $data);
		return $this->db->insert_id();
	}


	public function format_depot_in_info($depot_in_info)
	{
		$sql = "SELECT a.admin_id,a.admin_name FROM ".$this->db->dbprefix('admin_info')." a ";//WHERE a.user_status = 1
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();

		$sql = "SELECT COUNT(*) as tot FROM ".$this->db->dbprefix('depot_in_sub')." WHERE depot_in_id = '".$depot_in_info->depot_in_id."'";
		$query = $this->db->query($sql);
		$result = $query->row();
		$query->free_result();
		$rs = array('-1' => '系统');
		foreach ($list as $row)
		{
			$rs[$row->admin_id] = $row->admin_name;
		}

		if ($depot_in_info->lock_admin == 0)
		{
			$depot_in_info->lock_status_name = '未锁定';
			$depot_in_info->lock_name = '';
		} else
		{
			$depot_in_info->lock_status_name = '已锁定';
			$depot_in_info->lock_name = $rs[$depot_in_info->lock_admin];
		}


		if ($depot_in_info->audit_admin == 0)
		{
			$depot_in_info->depot_in_status = 1; //未审核
			$depot_in_info->depot_in_status_name = '未审核';
			$depot_in_info->oper_name = $rs[$depot_in_info->create_admin];
		} else
		{
			$depot_in_info->depot_in_status = 2; //已审核
			$depot_in_info->depot_in_status_name = '已审核';
			$depot_in_info->oper_name = $rs[$depot_in_info->audit_admin];
		}

		if(!empty($result) && $result->tot > 0)
		{
			$depot_in_info->has_product = 1;
		} else
		{
			$depot_in_info->has_product = 0;
		}

		return $depot_in_info;
	}

	public function some_products_in ($filter)
	{
		if(empty($filter['depot_in_id']) || empty($filter['sub_id']) || empty($filter['depot_depot_id']))
		{
			return array('message' => '无效的参数', 'error' => 1);
		}
		$type=0;
		if (!empty($filter['order_sn']))
		{
			if(substr($filter['order_sn'],0,2) == 'CK')
			{
				$type=2; //depot out
			}
			if(substr($filter['order_sn'],0,2) == 'CG')
			{
				$type=1; //purchase
			}
		}

		$sql = "SELECT a.sub_id,a.product_id,a.color_id,a.size_id,b.product_name,b.product_sn,b.provider_productcode,f.size_sn,e.color_sn FROM ".$this->db->dbprefix('product_sub'). " a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" WHERE a.sub_id = ? ";
		$param = array();
		$param[] = $filter['sub_id'];
		$query = $this->db->query($sql, $param);
		$product_info = $query->row();
		$query->free_result();

		if ($type == 2)
		{
			$sql = "SELECT SUM(b.product_number) as max_num FROM ".$this->db->dbprefix('depot_out_main')." a LEFT JOIN ".$this->db->dbprefix('depot_out_sub')." b ON b.depot_out_id = a.depot_out_id WHERE a.depot_out_code = ? AND b.product_id = ? AND b.color_id = ? AND b.size_id = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $filter['order_sn'];
			$param[] = $product_info->product_id;
			$param[] = $product_info->color_id;
			$param[] = $product_info->size_id;
			$query = $this->db->query($sql, $param);
			$row = $query->row();
			$max_num = empty($row)?0:$row->max_num;
			$query->free_result();

			$sql = "SELECT SUM(b.product_number) as had_num FROM ".$this->db->dbprefix('depot_in_main')." a LEFT JOIN ".$this->db->dbprefix('depot_in_sub')." b ON b.depot_in_id = a.depot_in_id WHERE a.order_sn = ? AND b.product_id = ? AND b.color_id = ? AND b.size_id = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $filter['order_sn'];
			$param[] = $product_info->product_id;
			$param[] = $product_info->color_id;
			$param[] = $product_info->size_id;
			$query = $this->db->query($sql, $param);
			$row = $query->row();
			$had_num = empty($row)?0:$row->had_num;
			$max_num = $max_num - $had_num;
			$query->free_result();
		} elseif ($type == 1)
		{
			$sql = "SELECT SUM(b.product_number) as max_num FROM ".$this->db->dbprefix('purchase_main')." a LEFT JOIN ".$this->db->dbprefix('purchase_sub')." b ON b.purchase_id = a.purchase_id WHERE a.purchase_code = ? AND b.product_id = ? AND b.color_id = ? AND b.size_id = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $filter['order_sn'];
			$param[] = $product_info->product_id;
			$param[] = $product_info->color_id;
			$param[] = $product_info->size_id;
			$query = $this->db->query($sql, $param);
			//return array('message' => '无效的参数', 'error' => 1,'sql'=>$sql,'par'=>$filter['order_sn']." ".$product_info->product_id." ".$product_info->color_id." ".$product_info->size_id);
			$row = $query->row();
			$max_num = empty($row)?0:$row->max_num;
			$query->free_result();

			$sql = "SELECT SUM(b.product_number) as had_num FROM ".$this->db->dbprefix('depot_in_main')." a LEFT JOIN ".$this->db->dbprefix('depot_in_sub')." b ON b.depot_in_id = a.depot_in_id WHERE a.order_sn = ? AND b.product_id = ? AND b.color_id = ? AND b.size_id = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $filter['order_sn'];
			$param[] = $product_info->product_id;
			$param[] = $product_info->color_id;
			$param[] = $product_info->size_id;
			$query = $this->db->query($sql, $param);
			$row = $query->row();
			$had_num = empty($row)?0:$row->had_num;
			$max_num = $max_num - $had_num;
			$query->free_result();
		}

			$sql = "SELECT SUM(b.product_number) as had_num,a.depot_depot_id FROM ".$this->db->dbprefix('depot_in_main')." a LEFT JOIN ".$this->db->dbprefix('depot_in_sub')." b ON b.depot_in_id = a.depot_in_id WHERE a.depot_in_id = ? AND b.product_id = ? AND b.color_id = ? AND b.size_id = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $filter['depot_in_id'];
			$param[] = $product_info->product_id;
			$param[] = $product_info->color_id;
			$param[] = $product_info->size_id;
			$query = $this->db->query($sql, $param);
			$row = $query->row();
			if (empty($type))
			{
				if (empty($row))
				{
					$had_num = 0;
					$max_num = 'big';
					$finish_num = 0;
				} else
				{
					$had_num = $row->had_num;
					$max_num = 'big';
					$finish_num = $row->had_num;
				}
			} else
			{
				$finish_num = empty($row)?0:$row->had_num;
			}


			$depot_id = $filter['depot_depot_id'];
			$query->free_result();


		$product_info->had_num = $had_num;
		$product_info->finish_num = $finish_num;
		$product_info->max_num = $max_num;

		$sql = "SELECT DISTINCT h.location_code1,h.location_code2,h.location_code3,h.location_code4,h.location_name FROM ".$this->db->dbprefix('transaction_info')." a" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." h ON h.location_id = a.location_id" .
				" WHERE a.product_id = ? AND a.color_id = ? AND a.size_id = ? AND trans_status <> 5 AND a.depot_id = ? LIMIT 3";
		$param = array();
		$param[] = $product_info->product_id;
		$param[] = $product_info->color_id;
		$param[] = $product_info->size_id;
		$param[] = $depot_id;
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		$recomd_location = '';
		if (!empty($list))
		{
			foreach ($list as $item)
			{
				$recomd_location .= empty($recomd_location)?
					$item->location_code1.'-'.$item->location_code2.'-'.$item->location_code3.'-'.$item->location_code4:
					','.$item->location_code1.'-'.$item->location_code2.'-'.$item->location_code3.'-'.$item->location_code4;
			}
		}
		$product_info->recomd_location = $recomd_location;
		$content1 = "商品:".$product_info->product_sn." ".$product_info->color_sn." ".$product_info->size_sn." 已入库数:".$product_info->finish_num." 可入库数:".($product_info->max_num === 'big'?'不限':$product_info->max_num);
		$content2 = " 参考储位:".$product_info->recomd_location;
		return array('message' => '', 'error' => 0,'max_num'=>$product_info->max_num, 'pre_sub_id'=>$filter['sub_id'],'content1'=>$content1, 'content2'=>$content2, 'valuable'=>($product_info->max_num === 'big' || $product_info->max_num > 0?'1':'0'));
	}

	public function check_products_in ($depot_in_info, $sub_id, $product_num)
	{
		if(empty($depot_in_info->depot_in_id) || empty($sub_id) || empty($product_num))
		{
			return FALSE;
		}
		$type=0;
		if (!empty($depot_in_info->order_sn))
		{
			if(substr($depot_in_info->order_sn,0,2) == 'CK')
			{
				$type=2; //depot out
			}
			if(substr($depot_in_info->order_sn,0,2) == 'CG')
			{
				$type=1; //purchase
			}
		}
		if ($type == 0)
		{
			return TRUE;
		}

		$sql = "SELECT a.sub_id,a.product_id,a.color_id,a.size_id,b.product_name,b.product_sn,b.provider_productcode,f.size_sn,e.color_sn FROM ".$this->db->dbprefix('product_sub'). " a" .
				" LEFT JOIN ".$this->db->dbprefix('product_info')." b ON b.product_id = a.product_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_color')." e ON e.color_id = a.color_id" .
				" LEFT JOIN ".$this->db->dbprefix('product_size')." f ON f.size_id = a.size_id" .
				" WHERE a.sub_id = ? ";
		$param = array();
		$param[] = $sub_id;
		$query = $this->db->query($sql, $param);
		$product_info = $query->row();
		$query->free_result();

		if ($type == 2)
		{
			$sql = "SELECT SUM(b.product_number) as max_num FROM ".$this->db->dbprefix('depot_out_main')." a LEFT JOIN ".$this->db->dbprefix('depot_out_sub')." b ON b.depot_out_id = a.depot_out_id WHERE a.depot_out_code = ? AND b.product_id = ? AND b.color_id = ? AND b.size_id = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $depot_in_info->order_sn;
			$param[] = $product_info->product_id;
			$param[] = $product_info->color_id;
			$param[] = $product_info->size_id;
			$query = $this->db->query($sql, $param);
			$row = $query->row();
			$max_num = empty($row)?0:$row->max_num;
			$query->free_result();

			$sql = "SELECT SUM(b.product_number) as had_num FROM ".$this->db->dbprefix('depot_in_main')." a LEFT JOIN ".$this->db->dbprefix('depot_in_sub')." b ON b.depot_in_id = a.depot_in_id WHERE a.order_sn = ? AND b.product_id = ? AND b.color_id = ? AND b.size_id = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $depot_in_info->order_sn;
			$param[] = $product_info->product_id;
			$param[] = $product_info->color_id;
			$param[] = $product_info->size_id;
			$query = $this->db->query($sql, $param);
			$row = $query->row();
			$had_num = empty($row)?0:$row->had_num;
			$max_num = $max_num - $had_num;
			$query->free_result();
		} elseif ($type == 1)
		{
			$sql = "SELECT SUM(b.product_number) as max_num FROM ".$this->db->dbprefix('purchase_main')." a LEFT JOIN ".$this->db->dbprefix('purchase_sub')." b ON b.purchase_id = a.purchase_id WHERE a.purchase_code = ? AND b.product_id = ? AND b.color_id = ? AND b.size_id = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $depot_in_info->order_sn;
			$param[] = $product_info->product_id;
			$param[] = $product_info->color_id;
			$param[] = $product_info->size_id;
			$query = $this->db->query($sql, $param);
			//return array('message' => '无效的参数', 'error' => 1,'sql'=>$sql,'par'=>$filter['order_sn']." ".$product_info->product_id." ".$product_info->color_id." ".$product_info->size_id);
			$row = $query->row();
			$max_num = empty($row)?0:$row->max_num;
			$query->free_result();

			$sql = "SELECT SUM(b.product_number) as had_num FROM ".$this->db->dbprefix('depot_in_main')." a LEFT JOIN ".$this->db->dbprefix('depot_in_sub')." b ON b.depot_in_id = a.depot_in_id WHERE a.order_sn = ? AND b.product_id = ? AND b.color_id = ? AND b.size_id = ? GROUP BY b.product_id,b.color_id,b.size_id ";
			$param = array();
			$param[] = $depot_in_info->order_sn;
			$param[] = $product_info->product_id;
			$param[] = $product_info->color_id;
			$param[] = $product_info->size_id;
			$query = $this->db->query($sql, $param);
			$row = $query->row();
			$had_num = empty($row)?0:$row->had_num;
			$max_num = $max_num - $had_num;
			$query->free_result();
		}
		if ($max_num >= $product_num)
		{
			return TRUE;
		} else
		{
			return FALSE;
		}


	}

	public function insert_depot_out_single ($transaction_id,$product_number,$depot_out_id,$admin_id,$update_finished_number=FALSE)
	{
		$sql = "SELECT a.depot_out_sub_id,c.depot_out_code FROM ".$this->db->dbprefix('depot_out_sub')." a " .
				"LEFT JOIN ".$this->db->dbprefix('transaction_info')." b ON a.product_id = b.product_id AND a.color_id = b.color_id AND a.size_id = b.size_id AND a.depot_id = b.depot_id AND a.location_id = b.location_id and a.batch_id=b.batch_id ".
				"LEFT JOIN ".$this->db->dbprefix('depot_out_main')." c ON a.depot_out_id = c.depot_out_id ".
				"WHERE 1 AND b.transaction_id = ? AND c.depot_out_id = ? ";
		$param = array();
		$param[] = $transaction_id;
		$param[] = $depot_out_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$data = array();
		if (!empty($row) && $row->depot_out_sub_id > 0)
		{
			/*
			$sql = "UPDATE ".$this->db->dbprefix('depot_out_sub')." SET product_number = product_number + ".$product_number . " WHERE depot_out_sub_id = ".$row->depot_out_sub_id;
			$this->db->query($sql);
			if ($this->db->affected_rows() == 0)
			{
				return false;
			}
			$sql = "UPDATE ".$this->db->dbprefix('transaction_info')." SET product_number = product_number - ".$product_number . " WHERE sub_id = ".$row->depot_out_sub_id . " AND trans_sn = '".$row->depot_out_code."' AND trans_status = ".TRANS_STAT_AWAIT_OUT;
			$this->db->query($sql);
			return $this->db->affected_rows();
			*/
			return -1;
		} else
		{
			//market_price,cost_price,consign_price,consign_rate,
			//b.market_price,b.cost_price,b.consign_price,b.consign_rate,
			$_select = "";
			$_update_finished_number = "";
			if($update_finished_number) {
				$_select = ",product_finished_number";
				$_update_finished_number = ",'".$product_number."' ";
			}
			$sql = "INSERT INTO ".$this->db->dbprefix('depot_out_sub')." (depot_out_id,product_id,product_name,color_id,size_id,depot_id,location_id,shop_price," .
					"product_number,product_amount,create_admin,create_date,batch_id".$_select.",expire_date,production_batch) " .
					"SELECT '".$depot_out_id."',a.product_id,b.product_name,a.color_id,a.size_id,a.depot_id,a.location_id,b.shop_price,".
					"'".$product_number."',b.shop_price*".$product_number.",'".$admin_id."','".date('Y-m-d H:i:s')."',batch_id " .$_update_finished_number.",a.expire_date,a.production_batch ".
					"FROM " . $this->db->dbprefix('transaction_info') ." a " .
					"LEFT JOIN " . $this->db->dbprefix('product_info') . " b ON a.product_id = b.product_id " .
					"WHERE a.transaction_id = '".$transaction_id."'";
			//echo $sql;die;
			$this->db->query($sql);
			
			$sub_id = $this->db->insert_id();
			$sub_id = $sub_id > 0?$sub_id:0;
			if ($sub_id == 0)
			{
				return false;
			}
			
			$sql = "INSERT INTO ".$this->db->dbprefix('transaction_info')."(trans_type,trans_status,trans_sn,product_id,color_id,size_id,product_number," .
					"depot_id,location_id,create_admin,create_date,update_admin,update_date,cancel_admin,cancel_date,trans_direction,sub_id," .
					"batch_id,product_cess,cost_price,consign_price,consign_rate,shop_price,expire_date,production_batch) ".
					" SELECT ".TRANS_TYPE_DIRECT_OUT.",".TRANS_STAT_AWAIT_OUT.",b.depot_out_code,a.product_id,a.color_id,size_id,(0-a.product_number),".
					"a.depot_id,a.location_id,a.create_admin,a.create_date,0,'0000-00-00',0,'0000-00-00',0,a.depot_out_sub_id," .
					"a.batch_id,c.product_cess,c.cost_price,c.consign_price,c.consign_rate,d.shop_price,a.expire_date,a.production_batch" .
					" FROM ".$this->db->dbprefix('depot_out_sub')." a" .
					" LEFT JOIN ".$this->db->dbprefix('depot_out_main')." b ON b.depot_out_id = a.depot_out_id " .
					" LEFT JOIN ".$this->db->dbprefix('product_cost')." c ON c.product_id = a.product_id AND c.batch_id = a.batch_id " .
					" LEFT JOIN ".$this->db->dbprefix('product_info')." d ON d.product_id = a.product_id" .
					" WHERE a.depot_out_sub_id = '".$sub_id."' ";
			$this->db->query($sql);
			return $this->db->insert_id()>0?1:0;
		}
 	}

	public function insert_depot_in_single ($sub_id,$product_number,$depot_in_id,$depot_id,$location_id,$admin_id,$batch_id,$update_finished_number=FALSE)
	{
		$sql = "SELECT a.depot_in_sub_id,c.depot_in_code FROM ".$this->db->dbprefix('depot_in_sub')." a " .
				"LEFT JOIN ".$this->db->dbprefix('product_sub')." b ON a.product_id = b.product_id AND a.color_id = b.color_id AND a.size_id = b.size_id ".
				"LEFT JOIN ".$this->db->dbprefix('depot_in_main')." c ON a.depot_in_id = c.depot_in_id ".
				"WHERE 1 AND b.sub_id = ? AND a.depot_id = ? AND a.location_id = ? AND a.depot_in_id = ? AND a.batch_id = ?";
		$param = array();
		$param[] = $sub_id;
		$param[] = $depot_id;
		$param[] = $location_id;
		$param[] = $depot_in_id;
		$param[] = $batch_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$data = array();
		if (!empty($row) && $row->depot_in_sub_id > 0)
		{
			/*
			$sql = "UPDATE ".$this->db->dbprefix('depot_in_sub')." SET product_number = product_number + ".$product_number . " WHERE depot_in_sub_id = ".$row->depot_in_sub_id;
			$this->db->query($sql);
			if ($this->db->affected_rows() == 0)
			{
				return false;
			}
			$sql = "UPDATE ".$this->db->dbprefix('transaction_info')." SET product_number = product_number + ".$product_number . " WHERE sub_id = ".$row->depot_in_sub_id . " AND trans_sn = '".$row->depot_in_code."' AND trans_status = ".TRANS_STAT_AWAIT_IN;
			$this->db->query($sql);
			if (!$this->db->affected_rows())
			{
				return false;
			}
			return $row->depot_in_sub_id;
			*/
			return -1;
		} else
		{
			$_select = "";
			$_update_finished_number = "";
			if($update_finished_number) {
				$_select = ",product_finished_number";
				$_update_finished_number = ",'".$product_number."' ";
			}
			//market_price,cost_price,consign_price,consign_rate,
			//b.market_price,b.cost_price,b.consign_price,b.consign_rate,
			$sql = "INSERT INTO ".$this->db->dbprefix('depot_in_sub')." (depot_in_id,product_id,product_name,color_id,size_id,depot_id,location_id,shop_price," .
					"product_number,product_amount,create_admin,create_date,batch_id".$_select.") " .
					"SELECT '".$depot_in_id."',a.product_id,b.product_name,a.color_id,a.size_id,'".$depot_id."','".$location_id."',b.shop_price,".
					"'".$product_number."',b.shop_price*".$product_number.",'".$admin_id."','".date('Y-m-d H:i:s')."',".$batch_id .$_update_finished_number.
					" FROM " . $this->db->dbprefix('product_sub') ." a " .
					"LEFT JOIN " . $this->db->dbprefix('product_info') . " b ON a.product_id = b.product_id " .
					"WHERE a.sub_id = '".$sub_id."'";
			$this->db->query($sql);
			$sub_id = $this->db->insert_id();
			$sub_id = $sub_id > 0?$sub_id:0;
			if ($sub_id == 0)
			{
				return false;
			}
                
			$sql = "INSERT INTO ".$this->db->dbprefix('transaction_info')."(trans_type,trans_status,trans_sn,product_id,color_id,size_id,product_number," .
					"depot_id,location_id,create_admin,create_date,update_admin,update_date,cancel_admin,cancel_date,trans_direction,sub_id," .
					"batch_id,product_cess,cost_price,consign_price,consign_rate,shop_price,expire_date,production_batch) ".
					" SELECT ".TRANS_TYPE_DIRECT_IN.",".TRANS_STAT_AWAIT_IN.",b.depot_in_code,a.product_id,a.color_id,size_id,a.product_number,".
					"a.depot_id,a.location_id,a.create_admin,a.create_date,0,'0000-00-00',0,'0000-00-00',1,a.depot_in_sub_id,".
                                        "a.batch_id,c.product_cess,c.cost_price,c.consign_price,c.consign_rate,d.shop_price,a.expire_date,a.production_batch" .
					" FROM ".$this->db->dbprefix('depot_in_sub')." a" .
					" LEFT JOIN ".$this->db->dbprefix('depot_in_main')." b ON b.depot_in_id = a.depot_in_id " .
					" LEFT JOIN ".$this->db->dbprefix('product_cost')." c ON c.product_id = a.product_id AND c.batch_id = a.batch_id " .
					" LEFT JOIN ".$this->db->dbprefix('product_info')." d ON d.product_id = a.product_id" .
					" WHERE a.depot_in_sub_id = '".$sub_id."' ";
			$this->db->query($sql);
                        //echo $this->db->last_query();
			if (!$this->db->insert_id())
			{
				return false;
			}
			return $sub_id;
		}
 	}

	public function update_depot_in_total ($depot_in_id)
	{
		$sql = "SELECT SUM(product_number) AS product_number_t,SUM(product_amount) AS product_amount_t,SUM(product_finished_number) AS product_finished_number_t  " .
				"FROM ".$this->db->dbprefix('depot_in_sub')." WHERE depot_in_id = ? ";
		$param = array();
		$param[] = $depot_in_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		if (empty($row))
		{
			$product_number_t = 0;
			$product_amount_t = 0;
			$product_finished_number_t = 0;
		} else
		{
			$product_number_t = $row->product_number_t;
			$product_amount_t = $row->product_amount_t;
			$product_finished_number_t = empty($row->product_finished_number_t)?0:$row->product_finished_number_t;
		}
		$param2 = array();
		$sql = "UPDATE ".$this->db->dbprefix('depot_in_main')." SET depot_in_number = ? ,depot_in_amount = ?, depot_in_finished_number=? WHERE depot_in_id = ? ";
		$param2[] = $product_number_t;
		$param2[] = $product_amount_t;
		$param2[] = $product_finished_number_t;
		$param2[] = $depot_in_id;
		$query = $this->db->query($sql, $param2);
	}

	public function update_depot_out_total ($depot_out_id)
	{
		$sql = "SELECT SUM(product_number) AS product_number_t,SUM(product_amount) AS product_amount_t,SUM(product_finished_number) AS product_finished_number_t " .
				"FROM ".$this->db->dbprefix('depot_out_sub')." WHERE depot_out_id = ? ";
		$param = array();
		$param[] = $depot_out_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		if (empty($row))
		{
			$product_number_t = 0;
			$product_amount_t = 0;
			$product_finished_number_t = 0;
		} else
		{
			$product_number_t = $row->product_number_t;
			$product_amount_t = $row->product_amount_t;
			$product_finished_number_t = empty($row->product_finished_number_t)?0:$row->product_finished_number_t;
		}
		$param2 = array();
		$sql = "UPDATE ".$this->db->dbprefix('depot_out_main')." SET depot_out_number = ? ,depot_out_amount = ?, depot_out_finished_number=? WHERE depot_out_id = ? ";
		$param2[] = $product_number_t;
		$param2[] = $product_amount_t;
		$param2[] = $product_finished_number_t;
		$param2[] = $depot_out_id;
		$query = $this->db->query($sql, $param2);
	}

	public function get_depot_out_product_sub($depot_out_sub_id)
	{
		$sql = "SELECT b.sub_id " .
				" FROM ".$this->db->dbprefix('depot_out_sub')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_sub')." b ON b.product_id = a.product_id AND b.color_id = a.color_id AND b.size_id = a.size_id".
				" WHERE a.depot_out_sub_id = ? ";
		$param = array();
		$param[] = $depot_out_sub_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		return isset($row->sub_id)?$row->sub_id:'';
	}

	public function get_depot_in_product_sub($depot_in_sub_id)
	{
		$sql = "SELECT b.sub_id " .
				" FROM ".$this->db->dbprefix('depot_in_sub')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_sub')." b ON b.product_id = a.product_id AND b.color_id = a.color_id AND b.size_id = a.size_id".
				" WHERE a.depot_in_sub_id = ? ";
		$param = array();
		$param[] = $depot_in_sub_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		return isset($row->sub_id)?$row->sub_id:'';
	}

	public function del_depot_in_product ($depot_in_sub_id,$depot_in_id,$depot_in_code)
	{
		$rs = $this->delete_depot_in_product(array('depot_in_id'=>$depot_in_id,'depot_in_sub_id'=>$depot_in_sub_id));
		if ($rs > 0)
		{
			$sql = "DELETE FROM ".$this->db->dbprefix('transaction_info')." WHERE trans_type = ".TRANS_TYPE_DIRECT_IN." AND trans_status = ".TRANS_STAT_AWAIT_IN." AND trans_sn = '".$depot_in_code."' AND sub_id = '".$depot_in_sub_id."'";
			$query = $this->db->query($sql);
			$rs = $this->db->affected_rows();
		}
		return $rs;
	}

	public function del_depot_out_product ($depot_out_sub_id,$depot_out_id,$depot_out_code)
	{
		$rs = $this->delete_depot_out_product(array('depot_out_id'=>$depot_out_id,'depot_out_sub_id'=>$depot_out_sub_id));
		if ($rs > 0)
		{
			$sql = "DELETE FROM ".$this->db->dbprefix('transaction_info')." WHERE trans_type = ".TRANS_TYPE_DIRECT_OUT." AND trans_status = ".TRANS_STAT_AWAIT_OUT." AND trans_sn = '".$depot_out_code."' AND sub_id = '".$depot_out_sub_id."'";
			$query = $this->db->query($sql);
			$rs = $this->db->affected_rows();
		}
		return $rs;
	}

	public function delete_depot_in_product ($where_arr)
	{
		$this->db->delete('depot_in_sub', $where_arr);
		return $this->db->affected_rows();
	}

	public function update_depot_in_product_x ($depot_in_sub_id,$depot_in_id,$product_number,$depot_in_code,$update_finished_number=FALSE)
	{
		$_set = "";
		if($update_finished_number) {
			$_set = ",product_finished_number=".$product_number;
		}
		$sql = "UPDATE ".$this->db->dbprefix('depot_in_sub')." " .
				" SET product_number = '".$product_number."',product_amount = shop_price * ".$product_number." " .$_set.
				" WHERE depot_in_id = '".$depot_in_id."' AND depot_in_sub_id = '".$depot_in_sub_id."'";
		$query = $this->db->query($sql);

		if($this->db->affected_rows())
		{
			$sql = "UPDATE ".$this->db->dbprefix('transaction_info')." SET product_number = ".$product_number . " WHERE sub_id = ".$depot_in_sub_id . " AND trans_sn = '".$depot_in_code."' AND trans_status = ".TRANS_STAT_AWAIT_IN;
			$this->db->query($sql);
		}
		return $this->db->affected_rows();
	}

	public function update_depot_out_product_x ($depot_out_sub_id,$depot_out_id,$product_number,$depot_out_code,$update_finished_number=FALSE)
	{
		$_set = "";
		if($update_finished_number) {
			$_set = ",product_finished_number=".$product_number;
		}
		$sql = "UPDATE ".$this->db->dbprefix('depot_out_sub')." " .
				" SET product_number = '".$product_number."',product_amount = shop_price * ".$product_number." " .$_set.
				" WHERE depot_out_id = '".$depot_out_id."' AND depot_out_sub_id = '".$depot_out_sub_id."'";
		//echo $sql;die;
		$query = $this->db->query($sql);

		if($this->db->affected_rows())
		{
			$sql = "UPDATE ".$this->db->dbprefix('transaction_info')." SET product_number = -".$product_number . " WHERE sub_id = ".$depot_out_sub_id . " AND trans_sn = '".$depot_out_code."' AND trans_status = ".TRANS_STAT_AWAIT_OUT;
			$this->db->query($sql);
		}
		return $this->db->affected_rows();
	}

	public function filter_depot_in_sub($filter)
	{
		$query = $this->db->get_where('depot_in_sub', $filter, 1);
		return $query->row();
	}

	public function update_depot_in_purchase ($depot_in_info)
	{
		$sql = "UPDATE ".$this->db->dbprefix('purchase_main')." a" .
				" LEFT JOIN ".$this->db->dbprefix('depot_in_main')." b ON a.purchase_code = b.order_sn" .
				" SET a.purchase_finished_number = a.purchase_finished_number + b.depot_in_number" .
				" WHERE a.purchase_code = '".$depot_in_info->order_sn."' AND b.depot_in_id = '".$depot_in_info->depot_in_id."'";
		$this->db->query($sql);

		$sql = "UPDATE ".$this->db->dbprefix('purchase_main')."" .
				" SET purchase_finished = 1" .
				" WHERE purchase_code = '".$depot_in_info->order_sn."' AND purchase_number = purchase_finished_number";
		$this->db->query($sql);
	}

	public function get_depot_in_sub_batch($depot_in_id) {
		$sql = "SELECT a.depot_in_id,a.depot_in_sub_id,b.batch_id,b.lock_admin
		FROM ".$this->db->dbprefix('depot_in_sub')." AS a
		LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS b ON a.batch_id=b.batch_id
		WHERE a.depot_in_id=?";
		$query = $this->db_r->query($sql,array($depot_in_id));
		return $query->result();
	}
	
	public function get_trans_out_num($sub_id) {
		$sql = "SELECT SUM(a.product_number) AS real_num, b.lock_num 
		FROM ".$this->db->dbprefix('transaction_info')." AS a 
		LEFT JOIN ".$this->db->dbprefix('product_sub')." AS b ON b.product_id = a.product_id AND b.color_id=a.color_id AND b.size_id=a.size_id 
		WHERE a.trans_status IN (1,2,4) AND b.sub_id=? ";
		$query = $this->db_r->query($sql,array($sub_id));
		return $query->row();
	}
	
	public function get_location_of_batch($batch_id) {
		$sql = "SELECT b.provider_cooperation
		FROM ".$this->db->dbprefix('purchase_batch')." AS a
		LEFT JOIN ".$this->db->dbprefix('product_provider')." AS b ON a.provider_id=b.provider_id
		WHERE a.batch_id=?";
		$query = $this->db_r->query($sql,array($batch_id));
		return $query->row();
	}
        
        public function has_ctb_depot_in($date, $depot_in_code, $slave = TRUE) {
            $adoEx = $slave ? $this->db_r : $this->db;
            $sql = <<< SQL
SELECT 
    1 
FROM 
    ty_depot_in_main dim
WHERE 
    dim.depot_in_type = 12
AND 
    dim.depot_in_code = ?
AND
    dim.create_admin = -1
AND
    dim.audit_admin = -1
AND 
    dim.audit_date = concat(?, ' 23:00:00')
;
SQL;
            $query = $adoEx->query($sql, array($depot_in_code, $date));
            return $query->num_rows() > 0;
        }
        
        public function has_ctb_depot_out($date, $depot_out_code, $slave = TRUE) {
            $adoEx = $slave ? $this->db_r : $this->db;
            $sql = <<< SQL
SELECT 
    1 
FROM 
    ty_depot_out_main dom
WHERE 
    dom.depot_out_type = 16
AND
    dom.depot_out_code = ?
AND
    dom.create_admin = -1
AND
    dom.audit_admin = -1
AND 
    dom.audit_date = concat(?, ' 23:00:00')
;

SQL;
            $query = $adoEx->query($sql, array($depot_out_code, $date));
            return $query->num_rows() > 0;
        }
        
        public function add_ctb_depot_in ($ctb_depot_in) {
            $this->db->insert("ty_depot_in_main", $ctb_depot_in);
            return $this->db->insert_id();
        }
        
        public function add_ctb_depot_out ($ctb_depot_out) {
            $this->db->insert("ty_depot_out_main", $ctb_depot_out);
            return $this->db->insert_id();
        }
        
        public function filter_depot_iotype($filter) {
            $query = $this->db->get_where('depot_iotype', $filter, 1);
            return $query->row();
        }
        
        public function filter_depot_out_product($depot_out_id) {
        	$sql = "SELECT b.product_id,b.color_id,b.size_id,c.sub_id,SUM(b.product_number) as out_num 
        			FROM ".$this->db->dbprefix('depot_out_sub')." b 
        			LEFT JOIN ".$this->db->dbprefix('product_sub')." c ON b.product_id=c.product_id AND b.color_id=c.color_id AND b.size_id=c.size_id
        			WHERE b.depot_out_id = ? GROUP BY b.product_id,b.color_id,b.size_id ";
        	$query = $this->db_r->query($sql,array($depot_out_id));
        	$list = $query->result();
        	$query->free_result();
        	$result = array();
        	foreach($list as $item)
        	{
        		$result[$item->sub_id] = $item->out_num;
        	}
        	return $result;
        }
        
        public function get_batch_depot($batch_id) {
        	$sql = "SELECT DISTINCT b.depot_name
					FROM ".$this->db->dbprefix('transaction_info')." a
					LEFT JOIN ".$this->db->dbprefix('depot_info')." b ON a.depot_id=b.depot_id
					WHERE a.batch_id=? AND a.trans_status in (2,4)";
        	$query = $this->db_r->query($sql,array($batch_id));
        	$list = $query->result();
        	$query->free_result();
        	return $list;
        }
        
        public function get_batch_waiting_out($batch_id) {
        	$sql = "SELECT a.transaction_id 
		        	FROM ".$this->db->dbprefix('transaction_info')." a
		        	WHERE a.batch_id=? AND a.trans_status in (1,3) limit 1";
        	$query = $this->db_r->query($sql,array($batch_id));
        	$result = $query->row();
        	$query->free_result();
        	return $result;
        }
        
        public function get_extra_trans_status($product_sub_id) {
        	$sql = "SELECT t.trans_type,t.trans_sn 
					FROM ".$this->db->dbprefix('transaction_info')." t
					LEFT JOIN ".$this->db->dbprefix('product_sub')." b ON t.product_id=b.product_id AND t.color_id=b.color_id AND t.size_id=b.size_id
					WHERE b.sub_id=240 
					AND t.trans_status in (1,3)";
        	$query = $this->db_r->query($sql,array($product_sub_id));
        	$list = $query->result();
        	$query->free_result();
        	return $list;
        }
       
		public function filter_trans_info($trans_id) {
			$query = $this->db_r->get_where('transaction_info', array('transaction_id'=>$trans_id), 1);
        	return $trans = $query->row();
		}
		
		/**
		 * 查询所有未完成的盘点
		 */
		public function get_unfinished_inventory() {
			$sql = "SELECT * FROM ".$this->db_r->dbprefix('depot_inventory')." WHERE `status` < 3 ";
			$query = $this->db_r->query($sql);
			$result = $query->result();
			$query->free_result();
			return $result;
		}
	 
        public function get_trans_out_num_transid($trans_id) {
        	
        	$query = $this->db_r->get_where('transaction_info', array('transaction_id'=>$trans_id), 1);
        	$trans = $query->row();
        	
        	$sql = "SELECT SUM(a.product_number) AS real_num, b.lock_num
        	FROM ".$this->db->dbprefix('transaction_info')." AS a
        	LEFT JOIN ".$this->db->dbprefix('product_sub')." AS b ON b.product_id = a.product_id AND b.color_id=a.color_id AND b.size_id=a.size_id
        	WHERE a.trans_status IN (1,2,4) AND a.product_id=? AND a.color_id=? AND a.size_id=? AND a.batch_id=? AND a.location_id=? ";
        	$param = array();
        	$param[] = $trans->product_id;
        	$param[] = $trans->color_id;
        	$param[] = $trans->size_id;
        	$param[] = $trans->batch_id;
        	$param[] = $trans->location_id;
        	$query = $this->db_r->query($sql,$param);
        	return $query->row();
        }
       /*
         * 出库审核。
         */
        public function check_out($depot_out_info, $admin_id) {
                $this->load->model('depot_model');
                
		$this->db->query('BEGIN');
                
                $now = date('Y-m-d H:i:s');
		$update = array();
		$update['audit_date'] = $now;
		$update['audit_admin'] = $admin_id;
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->update_depot_out($update, $depot_out_info->depot_out_id);
		
		$update_trans = array();
		$update_trans['trans_status'] = TRANS_STAT_OUT;
		$update_trans['update_admin'] = $this->admin_id;
		$update_trans['update_date'] = $now;
		$this->depot_model->update_transaction($update_trans, array('trans_status'=>TRANS_STAT_AWAIT_OUT,'trans_sn'=>$depot_out_info->depot_out_code));
                
		$this->db->query('COMMIT');
        }
        
        /*
         * 入库审核。
         */
        public function check_in($depot_in_info, $admin_id) {
		$this->db->query('BEGIN');
                
                $now = date('Y-m-d H:i:s');
		$update = array();
		$update['audit_date'] = $now;
		$update['audit_admin'] = $admin_id;
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->update_gl_num_in($depot_in_info->depot_in_code);
		$this->update_depot_in($update, $depot_in_info->depot_in_id);
		
		$update_trans = array();
		$update_trans['trans_status'] = TRANS_STAT_IN;
		$update_trans['update_admin'] = $this->admin_id;
		$update_trans['update_date'] = $now;
		$this->update_transaction($update_trans, array('trans_status'=>TRANS_STAT_AWAIT_IN,'trans_sn'=>$depot_in_info->depot_in_code));
		if (!empty($depot_in_info->order_sn) && substr($depot_in_info->order_sn,0,2) == 'CG')
		{
			$this->update_depot_in_purchase($depot_in_info);
		}

		$this->db->query('COMMIT');
        }
	public function update_transaction ($data, $where_arr)
	{
		$this->db->update('transaction_info', $data, $where_arr);
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
	public function filter_trans_log($filter) {
		$from = "FROM ".$this->db->dbprefix('transaction_info')." t 
				LEFT JOIN ".$this->db->dbprefix('product_sub')." e ON t.product_id=e.product_id AND t.color_id=e.color_id AND t.size_id=e.size_id
				LEFT JOIN ".$this->db->dbprefix('location_info')." g ON t.location_id=g.location_id
				LEFT JOIN ".$this->db->dbprefix('purchase_batch')." f ON t.batch_id=f.batch_id
				";
		$where = " WHERE 1 ";
		$param = array();
		
		if (!empty($filter['provider_barcode'])) {
			$where .= " AND e.provider_barcode = ? ";
			$param[] = $filter['provider_barcode'];
		}
		if (!empty($filter['location_name'])) {
			$where .= " AND g.location_name = ? ";
			$param[] = $filter['location_name'];
		}
		if (!empty($filter['batch_code'])) {
			$where .= " AND f.batch_code = ? ";
			$param[] = $filter['batch_code'];
		}
		if (!empty($filter['trans_status'])) {
			$where .= " AND t.trans_status " . db_create_in(explode(",",$filter['trans_status']));
		} else {
			$where .= " AND t.trans_status IN (1,2,3,4) ";
		}
		
		$sql = "SELECT COUNT(t.transaction_id) AS total " . $from . $where;
		
		$query = $this->db_r->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->total;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0) {
			return array('list' => array(), 'filter' => $filter);
		}
		
		$filter['sort_by'] = empty($filter['sort_by']) ? ' t.product_id,t.color_id,t.size_id,t.transaction_id ' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? '' : trim($filter['sort_order']);
		
		$sql = "SELECT t.*,b.product_sn,b.product_name,b.provider_productcode,c.color_sn,c.color_name,d.size_sn,d.size_name,
				e.provider_barcode,g.location_name,f.batch_code,j.depot_name,h.depot_in_id,i.depot_out_id,o.order_id,r.return_id,ex.exchange_id ".
				$from . "
				LEFT JOIN ".$this->db->dbprefix('product_info')." b ON t.product_id=b.product_id
				LEFT JOIN ".$this->db->dbprefix('product_color')." c ON t.color_id=c.color_id
				LEFT JOIN ".$this->db->dbprefix('product_size')." d ON t.size_id=d.size_id
				LEFT JOIN ".$this->db->dbprefix('depot_info')." j ON t.depot_id=j.depot_id
				LEFT JOIN ".$this->db->dbprefix('depot_in_main')." h ON t.trans_sn=h.depot_in_code AND t.trans_type=1
				LEFT JOIN ".$this->db->dbprefix('depot_out_main')." i ON t.trans_sn=i.depot_out_code AND t.trans_type=2
				LEFT JOIN ".$this->db->dbprefix('order_info')." o ON t.trans_sn=o.order_sn AND t.trans_type=3
				LEFT JOIN ".$this->db->dbprefix('order_return_info')." r ON t.trans_sn=r.return_sn AND t.trans_type=4
				LEFT JOIN ".$this->db->dbprefix('exchange_main')." ex ON ex.exchange_code=t.trans_sn AND t.trans_type=6 ".
				$where . 
				" ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'] .
                " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		
		$query = $this->db_r->query($sql, $param);
		$return = $query->result();
		$query->free_result();
		
		return array('list' => $return, 'filter' => $filter);
	}
}
###
