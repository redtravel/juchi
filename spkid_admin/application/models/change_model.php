<?php
#doc
#	classname:	Product_model
#	scope:		PUBLIC
#
#/doc

class Change_model extends CI_Model
{
	public function filter ($filter)
	{
		$query = $this->db->get_where('order_change_info', $filter, 1);
		return $query->row();
	}

	public function lock_change($change_id){
		$is_sn=strtoupper(substr(strval($change_id),0,2))=='HH';
		$sql="SELECT * FROM ".$this->db->dbprefix('order_change_info')." WHERE ".($is_sn?'change_sn':'change_id')."=? FOR UPDATE";
		$query=$this->db->query($sql,array($change_id));
		return $query->row();
	}

	public function all_change($filter){
		$query = $this->db->get_where('order_change_info',$filter);
		return $query->result();
	}

	public function filter_product_all ($filter)
	{
		$query = $this->db->get_where('order_change_product', $filter);
		return $query->result_array();
	}

	public function update ($data, $change_id)
	{
		$this->db->update('order_change_info', $data, array('change_id' => $change_id));
	}

	public function insert ($data)
	{
		$this->db->insert('order_change_info', $data);
		return $this->db->insert_id();
	}

	public function insert_product ($data)
	{
		$this->db->insert('order_change_product', $data);
		return $this->db->insert_id();
	}

	public function insert_action($change, $action_note)
    {
        $update = array(
            'order_id' => $change['change_id'],
            'is_return' => 3,
            'order_status' => isset($change['change_status'])?$change['change_status']:0,
            'shipping_status' => isset($change['shipping_status'])?$change['shipping_status']:0,
            'pay_status' => isset($change['shipped_status'])?$change['shipped_status']:0,
            'action_note' => $action_note,
            'create_admin' => isset($this->admin_id)?$this->admin_id:intval($this->session->userdata('admin_id')),
            'create_date' => isset($this->time)?$this->time:date('Y-m-d H:i:s')
            );
        $this->db->insert('order_action',$update);
        return $this->db->insert_id();
    }

    public function delete ($change_id)
	{
            $this->db->delete('order_change_info', array('change_id' => $change_id));
	}

	public function delete_product ($where_arr)
	{
            $this->db->delete('order_change_product', $where_arr);
	}
	
	public function update_product($update,$cp_id)
	{
			$this->db->update('order_change_product',$update,array('cp_id'=>$cp_id));
	}

	public function change_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('order_change_info')." AS a ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['change_sn']))
		{
			$where .= " AND a.change_sn LIKE '%" . mysql_like_quote($filter['change_sn']) . "%' ";
		}

		if (!empty($filter['order_sn']))
		{
			$where .= " AND ord.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%' ";
		}

		if (!empty($filter['consignee']))
		{
			$where .= " AND a.consignee LIKE '%" . mysql_like_quote($filter['consignee']) . "%' ";
		}

		if (!empty($filter['provider_goods']))
		{
			$where .= " AND EXISTS(SELECT 'X' FROM ".$this->db->dbprefix('order_return_product')." s, ".$this->db->dbprefix('product_info')." v WHERE a.return_id = s.return_id AND s.product_id = v.product_id AND (v.product_name LIKE ? OR v.product_sn LIKE ? OR v.provider_productcode LIKE ? )) ";
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
			$param[] = '%' . $filter['provider_goods'] . '%';
		}

		if (!empty($filter['start_time']))
        {
            $where .= " AND TO_DAYS(a.create_date) >= TO_DAYS('".$filter['start_time']."')";
        }
        if (!empty($filter['end_time']))
        {
        	$where .= " AND TO_DAYS(a.create_date) <= TO_DAYS('".$filter['end_time']."')";
        }
/*
		if (!isset($filter['invoice_status']) && $filter['invoice_status'] != -1)
		{
			switch ($filter['invoice_status']) {
	            case 0:
	                $where .= "AND EXISTS(select 1 from fc_flc_shipping_package as sp left join fc_flc_invoice_track as st on sp.package_sn = st.package_sn where sp.order_id=o.change_id and sp.rec_type=2 and sp.shipping_id not in (4,15) and sp.shipping_status=1 and sp.virtual_shipping=0 and (st.track_status=0 or st.track_status is null) limit 1)";
	                break;
	            case 1:
	                $where .= "AND o.shipping_status=1 AND EXISTS(select 1 from fc_flc_shipping_package as sp left join fc_flc_invoice_track as st on sp.package_sn = st.package_sn where sp.order_id=o.change_id and sp.rec_type=2 and st.track_status=1)";
	                break;
	            case 2:
	                $where .= "AND EXISTS(select 1 from fc_flc_shipping_package as sp left join fc_flc_invoice_track as st on sp.package_sn = st.package_sn where sp.order_id=o.change_id and sp.rec_type=2 and sp.shipping_id not in (4,15) and sp.shipping_status=1 and sp.virtual_shipping=0 and st.track_status=2 limit 1)";
	                break;
	            case 3:
	                $where .= "AND EXISTS(select 1 from fc_flc_shipping_package as sp left join fc_flc_invoice_track as st on sp.package_sn = st.package_sn where sp.order_id=o.change_id and sp.rec_type=2 and sp.shipping_id not in (4,15) and sp.shipping_status=1 and sp.virtual_shipping=0 and st.track_status=3 limit 1)";
	                break;
	        }
		}
*/
        if (isset($filter['composite_status']))
        {
			switch($filter['composite_status'])
	        {
	            case '101' :
	                $where .= " AND a.change_status = '1' AND a.shipping_status = '0' AND a.shipped_status = '1' ".
			                " AND ord.pay_status = 1 AND ord.order_status = 1 ".
			                " AND NOT EXISTS (
			                   select 'X' from ".$this->db->dbprefix('order_change_product')." as tcg where tcg.change_id = a.change_id and tcg.consign_number>0 limit 1
			               )";
	                break;

	            case '104' :
	                $where .= " AND a.change_status = '1' AND a.shipping_status = '0' AND a.shipped_status = '0' " ;
	                break;

	            case '102' :
	                $where .= " AND a.change_status = '1' AND a.shipping_status = '1' AND a.shipped_status = '1' AND a.is_ok = '1' ";
	                break;

	            case '106' :
	                $where .= " AND a.change_status IN ('1','0') AND  a.shipping_status = '0' AND ord.pay_status = 1 AND ord.order_status = 1 ".
			                " AND EXISTS (
			                   select 'X' from ".$this->db->dbprefix('product_sub')." as consign_gl,
			                       (select sum(tcg.consign_num) as consign_num,tcg.change_id,tcg.product_id,tcg.color_id,tcg.size_id
			                       from ".$this->db->dbprefix('order_change_product')." as tcg
			                        where tcg.consign_num>0
			                        group by tcg.change_id,tcg.product_id,tcg.color_id,tcg.size_id) as consign_cg
			                        where consign_cg.change_id = a.change_id and consign_cg.product_id = consign_gl.product_id
			                        and consign_cg.color_id = consign_gl.color_id and consign_cg.size_id = consign_gl.size_id
			                        and consign_gl.gl_num< consign_cg.consign_num and consign_gl.consign_num = -1
			                        limit 1
			                    )";
	                break;

	            default:
	                if ($filter['composite_status'] != -1 && !empty($filter['composite_status']))
	                {
	                    $where .= " AND a.change_status = '".$filter['composite_status']."' ";
	                }
	        }
        }

                if ($filter['odd']) {
                    $where .= " AND a.odd = 1 ";
                }
                if ($filter['pick']) {
                    $where .= " AND a.shipping_status=0 AND a.pick_sn!='' ";
                }
                
		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.change_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

		$sql = "SELECT COUNT(*) AS ct " . $from . " LEFT JOIN " .$this->db->dbprefix('order_info'). " AS ord ON a.order_id=ord.order_id  ".$where;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "SELECT a.change_id, a.change_sn, a.create_date, a.change_status, a.user_id,a.shipping_status, a.shipped_status, au.admin_name as lock_name," .
                    "a.consignee, a.address, a.email, a.tel,a.mobile,ord.order_sn,ord.order_id,a.lock_admin, a.is_ok,a.pick_sn, " .
                    "ship.shipping_name,IFNULL(u.user_name, '匿名') AS buyer ".
                " FROM " . $this->db->dbprefix('order_change_info') . " AS a " .
                " LEFT JOIN " . $this->db->dbprefix('admin_info') . " AS au ON  a.lock_admin = au.admin_id " .
                " LEFT JOIN " .$this->db->dbprefix('order_info'). " AS ord ON a.order_id=ord.order_id  ".
                " LEFT JOIN " .$this->db->dbprefix('user_info'). " AS u ON u.user_id=a.user_id ".
                " LEFT JOIN " .$this->db->dbprefix('shipping_info'). " AS ship ON ship.shipping_id=a.shipping_id ".
                $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

		$query = $this->db->query($sql, $param);
		$list = $query->result();


		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function order_product_num($order_id) {
	    $sql = "SELECT o.product_id, o.color_id, o.size_id, o.gl_num,o.consign_num as gl_consign_num,o.wait_num FROM " . $this->db->dbprefix('product_sub') . " as o ".
	            " WHERE o.product_id IN (SELECT DISTINCT product_id FROM " . $this->db->dbprefix('order_product') . " WHERE order_id = '".$order_id."')";
	    $query = $this->db->query($sql);
        $res = $query->result_array();
	    $rs = array();
	    if (!empty($res))
	    {
	    	foreach ($res as $row)
	    	{
				$rs[$row['product_id'].'-'.$row['color_id'].'-'.$row['size_id']] = array(
		                'gl_num'=>$row['gl_num'],
		                'gl_consign_num'=>$row['gl_consign_num'],
		                'wait_num'=>$row['wait_num'],
		                'real_num'=>max($row['gl_num'] - $row['wait_num'],0)
		        );
	    	}
	    }
	    return $rs;
	}

	public function get_change_sn()
	{
	    /* 选择一个随机的方案 */
	    mt_srand((double) microtime() * 1000000);

	    return "HH".date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
	}

	public function change_product($change_id) {
	    $sql = "SELECT cg.*,ci.change_status,ci.is_ok,g.unit_name,
	            c.color_name,c.color_sn,s.size_name,s.size_sn,sc.color_name as src_color_name,sc.color_sn as src_color_sn,
	            ss.size_name as src_size_name,ss.size_sn as src_size_sn,g.product_name,g.product_sn, g.provider_productcode ,g.brand_id,g.category_id,
	            b.brand_name,tr.depot_id,tr.location_id,trp.location_name,dp.depot_name,gl.gl_num,gl.consign_num as gl_consign_num,
	            gl.wait_num".
	            " FROM " . $this->db->dbprefix('order_change_product') . " AS cg " .
	            " LEFT JOIN " . $this->db->dbprefix('order_change_info') . " AS ci ON cg.change_id = ci.change_id" .
	            " LEFT JOIN ". $this->db->dbprefix('transaction_info') . " AS tr ON ci.change_sn = tr.trans_sn AND cg.cp_id = tr.sub_id AND cg.product_id = tr.product_id AND cg.color_id = tr.color_id AND cg.size_id = tr.size_id AND tr.trans_type = '".TRANS_TYPE_CHANGE_ORDER."' AND trans_direction = '0' " .
	            " LEFT JOIN ". $this->db->dbprefix('location_info') . " AS trp ON tr.location_id = trp.location_id " .
	            " LEFT JOIN ". $this->db->dbprefix('depot_info') . " AS dp ON tr.depot_id = dp.depot_id " .
	            " LEFT JOIN ". $this->db->dbprefix('product_info') . " AS g ON cg.product_id = g.product_id " .
	            " LEFT JOIN " . $this->db->dbprefix('product_brand') . " AS b ON g.brand_id = b.brand_id " .
	            " LEFT JOIN " . $this->db->dbprefix('product_color') . " AS c ON c.color_id = cg.color_id " .
	            " LEFT JOIN " . $this->db->dbprefix('product_size') . " AS s ON s.size_id = cg.size_id " .
	            " LEFT JOIN " . $this->db->dbprefix('product_color') . " AS sc ON sc.color_id = cg.src_color_id " .
	            " LEFT JOIN " . $this->db->dbprefix('product_size') . " AS ss ON ss.size_id = cg.src_size_id " .
	            " LEFT JOIN " . $this->db->dbprefix('product_provider') . " AS gp ON gp.provider_id = g.provider_id " .
	            " LEFT JOIN " . $this->db->dbprefix('product_sub') . " AS gl ON gl.product_id = cg.product_id and gl.color_id = cg.color_id and gl.size_id = cg.size_id " .
	            " WHERE ci.change_id = '".$change_id."' ";
		$query = $this->db->query($sql);
        $res = $query->result_array();
	    return $res;
	}

	public function create_change_trans($change,$order)
	{
	    $change_id = $change['change_id'];
	    $change_sn = $change['change_sn'];
	    $order_id = $change['order_id'];
	    $shipping_id = isset($change['shipping_id'])?$change['shipping_id']:'';
	    $city = $change['city'];
	    $district = $change['district'];
	    $address = $change['address'];
	    $change_product = $this->change_product($change_id);

	    $location_first = array();
	    $consign_product_ids = array();
	    $trans_result = array();
	    $location_arr = array();

	    $this->db->query("UPDATE ".$this->db->dbprefix('transaction_info')." SET trans_status=5,cancel_date='".date('Y-m-d H:i:s')."',cancel_admin='".$this->admin_id."' WHERE trans_sn = '".$change_sn."' AND trans_status=1");

	    if (empty($change_product)) return array('error'=>0);

	    foreach ($change_product as $key=>$product)
	    {
	        if ($product['consign_num']>0) $consign_product_ids[] = $product['product_id'];
	        if ($product['consign_num']<$product['change_num'])
	        {
	            $trans_keys[] = $product['product_id'].'-'.$product['color_id'].'-'.$product['size_id'];
	            $product_ids[] = $product['product_id'];
	            $change_product[$key]['change_num'] -= $product['consign_num'];
	        }else
	        {
	            unset($change_product[$key]);
	        }
	    }
	    if (empty($change_product)) return array('error'=>0);

	    if (empty($shipping_id))
	    {
	        $shipping_id = $order['shipping_id'];
	    }

	    //找出可出库储位
	    $sql = "SELECT t.product_id,t.color_id,t.size_id,t.depot_id,t.location_id,SUM(t.product_number) AS product_num, d.depot_priority" .
	    		" FROM ".$this->db->dbprefix('transaction_info')." AS t" .
	    		" LEFT JOIN ".$this->db->dbprefix('depot_info')." AS d ON t.depot_id = d.depot_id" .
	    		" LEFT JOIN ".$this->db->dbprefix('location_info')." AS p ON p.location_id = t.location_id" .
	    		" WHERE d.is_use = 1 AND p.is_use = 1 AND d.depot_type = 1 AND t.trans_status in (1,2,4)" .
	    		" AND t.product_id ".db_create_in($product_ids).
				" AND CONCAT_WS('-',t.product_id,t.color_id,t.size_id) ".db_create_in($trans_keys).
				" GROUP BY t.location_id,t.product_id,t.color_id,t.size_id HAVING product_num>0 ORDER BY d.depot_priority DESC";
	    $query = $this->db->query($sql);
        $rs = $query->result_array();
        $l_arr = array('product_num'=>0,'trans_list'=>array(),'gl_list'=>array());
		if (!empty($rs))
		{
			foreach ($rs as $trans)
			{
				if (!isset($l_arr['gl_list'][$trans['product_id'].'-'.$trans['color_id'].'-'.$trans['size_id']]))
				{
					$l_arr['gl_list'][$trans['product_id'].'-'.$trans['color_id'].'-'.$trans['size_id']] = 0;
				}
				$l_arr['trans_list'][] = $trans;
		        $l_arr['gl_list'][$trans['product_id'].'-'.$trans['color_id'].'-'.$trans['size_id']] += $trans['product_num'];
		        $l_arr['product_num'] += $trans['product_num'];
			}
		}


		if (!empty($l_arr['trans_list']))
		{
			$trans_rs = $l_arr['trans_list'];
			$trans_result = array_merge($trans_result,$this->pick_trans_result($change_product,$trans_rs));
		}

	    if (!empty($change_product)) return array('error'=>1,'message'=>'分配储位失败^^!');
	    $sql = "INSERT INTO ".$this->db->dbprefix('transaction_info').
				"(trans_type,trans_status,trans_sn,product_id,color_id,size_id,product_number,depot_id,location_id,create_date,create_admin,trans_direction,sub_id) VALUES ";
	    $sql_data = array();
	    foreach ($trans_result as $trans)
	    {
	        $sql_data[] = "(5,1,'".$change_sn."','".$trans['product_id']."','".$trans['color_id']."','".$trans['size_id']."','".$trans['product_number']."','".$trans['depot_id']."','".$trans['location_id']."','".date('Y-m-d H:i:s')."','".$this->admin_id."',0,'".$trans['cp_id']."')";
	    }
	    $sql.=implode(',',$sql_data);

	    $this->db->query($sql);
	    return array('error'=>0,'message'=>'');
	}

	public function pick_trans_result(&$order_product,&$trans_rs)
	{
	    $trans_result = array();
	    foreach ($order_product as $gk=>$gv) {
	        if ($gv['change_num']<=0) continue;
	        foreach ($trans_rs as $tk=>$tv)
	        {
	            if ($tv['product_id']!=$gv['product_id']||$tv['color_id']!=$gv['color_id']||$tv['size_id']!=$gv['size_id']) continue;
	            if ($tv['product_num']>=$gv['change_num'])
	            {
	                $tv['product_num'] -= $gv['change_num'];
	                $trans_result[] = array('cp_id'=>$gv['cp_id'],'depot_id'=>$tv['depot_id'],'location_id'=>$tv['location_id'],'product_number'=>-1*$gv['change_num'],'product_id'=>$gv['product_id'],'color_id'=>$gv['color_id'],'size_id'=>$gv['size_id']);
	                unset($order_product[$gk]);
	                if($tv['product_num']==0)
	                    unset($trans_rs[$tk]);
	                else
	                    $trans_rs[$tk] = $tv;
	                break;//important
	            }else
	            {
	                $gv['change_num'] -= $tv['product_num'];
	                $trans_result[] = array('cp_id'=>$gv['cp_id'],'depot_id'=>$tv['depot_id'],'location_id'=>$tv['location_id'],'product_number'=>-1*$tv['change_num'],'product_id'=>$gv['product_id'],'color_id'=>$gv['color_id'],'size_id'=>$gv['size_id']);
	                unset($trans_rs[$tk]);
	                $order_product[$gk] = $gv;
	            }
	        }
	    }
	    return $trans_result;
	}

	public function change_info($change_id, $change_sn = '')
	{
		$change_id = intval($change_id);
	    $where = $change_id > 0? " where c.change_id  = $change_id " : " where c.change_sn = '".$change_sn."'";
	    $sql = "SELECT c.*,s.shipping_code,IFNULL(s.shipping_name,'') as shipping_name,o.order_status,o.shipping_status as order_shipping_status,u.user_name," .
	    		"o.pay_status,o.is_ok as order_is_ok,rp.region_name as province_name,rc.region_name as city_name,rd.region_name as district_name " .
	    		" FROM ".$this->db->dbprefix('order_change_info')." AS c" .
	    		" LEFT JOIN ".$this->db->dbprefix('shipping_info')." AS s ON c.shipping_id = s.shipping_id ".
	    		" LEFT JOIN ".$this->db->dbprefix('order_info')." AS o ON c.order_id = o.order_id ".
	    		" LEFT JOIN ". $this->db->dbprefix('region_info') . " AS rp ON c.province = rp.region_id " .
	            " LEFT JOIN ". $this->db->dbprefix('region_info') . " AS rc ON c.city = rc.region_id " .
	            " LEFT JOIN ". $this->db->dbprefix('region_info') . " AS rd ON c.district = rd.region_id " .
	            " LEFT JOIN ". $this->db->dbprefix('user_info')." AS u ON c.user_id = u.user_id " .
	    		$where;

	    $query = $this->db->query($sql);
		$change = $query->row_array();
		$query->free_result();
	    if($change) {
	        $change = $this->format_change($change);
	    }else {
	        $change = NULL;
	    }
	    return $change;
	}

	public function format_change ($change)
	{
		$change['order_isok'] = ($change['order_status'] == 1 && $change['order_is_ok'] == 1) ? 1 : 0;
	  	$change['order_ispay'] = ($change['pay_status'] == 1 && $change['order_status'] == 1) ? 1 : 0;
	  	$change['order_isship'] = ($change['order_shipping_status'] == 1 && $change['order_status'] == 1) ? 1 : 0;
		$change['status']     = $this->format_change_status($change);
	  	$change['formated_create_date']       = date('Y-m-d H:i:s', strtotime($change['create_date']));
	    $change['formated_confirm_date']   = $change['change_status']!=0?date('Y-m-d H:i:s', strtotime($change['confirm_date'])):'未客审';
	    $change['formated_shipped_date']       = $change['shipped_status']==1?date('Y-m-d H:i:s', strtotime($change['shipped_date'])):'未入库';
	    $change['formated_shipping_date']       = $change['shipping_status']==1?date('Y-m-d H:i:s', strtotime($change['shipping_date'])):'未发货';
	    return $change;
	}

	public function format_change_status ($change,$red=FALSE)
	{
		$status = array();
	    if($change['change_status']==0) $status[] = '未客审';
	    if($change['change_status']==1) $status[] = '已客审';
	    if($change['change_status']==4) $status[] = $red?'<font color="red">已作废</font>':'已作废';
	    if (isset($change['shipped_status']))
	    {
	    	$status[] = $change['shipped_status']?'已收货':'未收货';
	    } elseif (isset($change['pay_status']))
	    {
	    	$status[] = $change['pay_status']?'已收货':'未收货';
	    }
	    $status[] = $change['shipping_status']?'已发货':'未发货';
	    if (isset($change['is_ok'])) {
	        if($change['is_ok']) $status[] = $red?'<font color="red">已完结</font>':'已完结';
	        if(!$change['is_ok']) $status[] = '未完结';
	    }

	    return $status;
	}

	public function get_change_advice($change_id){
	    $sql="SELECT s.*, st.type_name,st.type_color,ad.admin_name" .
	    		" FROM ".$this->db->dbprefix('order_advice') ." AS s" .
	    		" LEFT JOIN ". $this->db->dbprefix('order_advice_type'). " AS st ON s.type_id= st.type_id" .
	    		" LEFT JOIN ". $this->db->dbprefix('admin_info') ." AS ad ON s.advice_admin = ad. admin_id" .
	    		" WHERE s.order_id = '".$change_id."' and s.is_return = 3";
	    $query = $this->db->query($sql);
	    $result = $query->result_array();
	    return $result;
	}

	function get_change_perm($change)
	{
	    /* 取得换货单状态、发货状态、付款状态 */
	    $os = $change['change_status'];
	    $ss = $change['shipping_status'];
	    $rs = $change['shipped_status'];

	    $is_complete = 1 == $os && 1 == $ss && 1 == $rs;
	    $is_ok = $change['is_ok'] == 1;
	    $order_ispay = $change['order_ispay'] == 1;
            

	    /* 取得换货单的锁定状态 */
	    $is_locked = $change['lock_admin'] != 0;
	    $locked_by_self = $is_locked && ($change['lock_admin'] == $this->admin_id); //被自己锁定
	    $locked_by_other = $is_locked && ($change['lock_admin'] != $this->admin_id); //被他人锁定
	    $is_available = ((0 == $os)||(1==$os)) && !$is_ok;  //换货单有效
	    $can_rechoice_shipping = check_perm('order_change_shipping') && $ss == 1 && $os == 1 && strtotime($change['shipping_date'])+ 1728000>=strtotime("now");
            
	    $list = array('save'=>FALSE,'depotshipsave'=>FALSE,'service_confirm'=>FALSE,'unservice_confirm'=>FALSE,
				'shipped'=>FALSE,'shipping'=>FALSE,'lock'=>FALSE,'invalid'=>FALSE,'is_ok'=>FALSE,'unlock'=>FALSE,
				'edit_shipping_type'=>FALSE,'point_shipping_type'=>FALSE,'edit_invoice_no'=>FALSE,'edit_consignee'=>FALSE,'edit_product'=>FALSE,
                                'odd'=>FALSE,'odd_cancel'=>FALSE);
            //如果拣货中，则返回全false
            if($change['pick_sn']&&!$change['shipping_status']) return $list;
            
	    /* 保存换货单 = 换货单有效 +  有编辑权限*/
	    $list['save'] = $is_available && $locked_by_self && check_perm('order_change_edit') && (0 == $os);
	    //自提单不可指派
	    $list['depotshipsave'] = (($is_available && $locked_by_self && check_perm('order_change_shipping') && (0 == $ss))||($can_rechoice_shipping && $locked_by_self))&&$os==1;
	    /* 客审:  * 退货单未确认   * 退货单被自己锁定   * 退货单有效   * 有order_service_confirm权限   * 有发货方式  */
	    $list['service_confirm'] = check_perm('order_change_confirm') && (0 == $os) && $is_available &&$locked_by_self && $change['shipping_id'] > 0;

	    // * 反客审   * 条件1：换货单已确认   * 条件2：未入库，未发货   * 条件3：有order_unservice_confirm权限   * 条件4：换货单被自己锁定  * 条件5：换货单有效
	    $list['unservice_confirm'] = (1 == $os) && (0 == $ss) && (0 == $rs) && check_perm('order_change_unconfirm')  && $is_available &&$locked_by_self;

	     //* 入库   * 条件1：未入库   * 条件2：已客审   * 条件3：order_ship权限   * 条件4：自我锁定   * 条件5：换货单有效
	    $list['shipped'] =  (1 == $os) && $is_available && (0 == $rs) &&(0 == $ss)&& check_perm('order_change_shipped') && $locked_by_self;

	     //* 发货   * 条件1：未发货，已入库   * 条件2：已确认    * 条件3：order_ship权限   * 条件4：被自己锁定   * 条件5：换货单有效   * 条件6：订单财务审核才能发货
	    $list['shipping'] = (0 == $ss) && (1 == $rs) && (1 == $os) && check_perm('order_change_shipping')  && $is_available &&$locked_by_self && $order_ispay;

	     //* 锁定与解锁 *
	    $list['unlock'] = $locked_by_self||($is_locked && check_perm('super_unlock')&&$change['lock_admin']>0);
	    $list['lock'] = !$is_locked && (!$is_ok||$can_rechoice_shipping);

	     //* 作废    * 条件1：未入库，未发货,未客审   * 条件2：order_invalid权限   * 条件3：换货单有效   * 条件4：被自己锁定
	    $list['invalid'] = $is_available && (0 == $os) && (0 == $ss)&& (0 == $rs) && check_perm('order_change_invalid') && $locked_by_self;

	     //* 完结  * 没有is_ok,被自己锁定,订单已走完所有的流程（取消和作废的单子都是自动完结的）
	    $list['is_ok'] = check_perm('order_change_ok') && $locked_by_self && $is_complete && !$is_ok;
	    // 判断是否可以编辑配送方式，条件：客服,未客审,订单有效，被自己锁定
	    $list['edit_shipping_type'] = check_perm('order_change_edit')  && 0 == $os && $is_available && $locked_by_self;
	    // 判断是否可以指定配送方式，条件：物流,非客服，未发货，可发货,原值不是自提,订单有效,被自己锁定
	    $list['point_shipping_type'] = check_perm('order_change_shipping') && 1 == $os && 0 == $ss && $list['shipping'] && $is_available && $locked_by_self;
	    // 判断是否可以编辑发货单号，条件：物流，已发货，订单有效，被自己锁定
	    $list['edit_invoice_no'] = $can_rechoice_shipping && $locked_by_self;

	    // 换货人信息，确认前可以由客服改,条件：客服，未客审，有效,被自己锁定
	    $list['edit_consignee'] = check_perm('order_change_edit') && 0 == $os && $is_available && $locked_by_self;
	    // 商品信息，确认前可以由客服改，条件：客服，未客审，有效,未审核,被自己锁定
	    $list['edit_product'] = check_perm('order_change_edit') && 0 == $os && $is_available && $locked_by_self;
            $list['odd'] = !$change['odd']&& $locked_by_self;
            $list['odd_cancel'] = $change['odd']&& $locked_by_self;
	    return $list;

	}

	public function available_change_shipping_list() {
	    $sql = 'SELECT s.shipping_id, s.shipping_code, s.shipping_name ' .
	            'FROM ' . $this->db->dbprefix('shipping_info') . ' AS s ' .
	            "WHERE s.is_use = 1  ";
	    $query = $this->db->query($sql);
	    $result = $query->result_array();
	    return $result;
	}

	public function get_change_trans ($change_sn)
	{
		$sql = "SELECT t.sub_id, t.depot_id,t.location_id,d.depot_name,p.location_name,t.product_number" .
				" FROM " . $this->db->dbprefix('transaction_info') . " as t" .
				" LEFT JOIN " . $this->db->dbprefix('depot_info') . " as d ON t.depot_id = d.depot_id" .
				" LEFT JOIN " . $this->db->dbprefix('location_info') . " as p ON p.location_id = t.location_id" .
				" WHERE t.trans_sn='" . $change_sn . "' AND t.trans_type=5 and trans_status in (1,2,6)";
		$query = $this->db->query($sql);
	    $result = $query->result_array();
	    return $result;
	}

	public function get_trans_out ($type,$src_og_ids)
	{
		$sql = "SELECT t.sub_id,t.trans_sn,t.product_number,d.depot_name,p.location_name" .
				" FROM ".$this->db->dbprefix('transaction_info')." as t" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." as d on t.depot_id = d.depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." as p on t.location_id = p.location_id" .
				" WHERE t.trans_type = ".$type." and t.trans_status = ".TRANS_STAT_OUT."" .
				" and sub_id ".db_create_in($src_og_ids);

		$query = $this->db->query($sql);
	    $result = $query->result_array();
	    return $result;
	}

	public function get_action_list($change_id)
	{
	    $act_list = array();
	    $sql = "SELECT a.*,b.admin_name FROM " . $this->db->dbprefix('order_action') . " a" .
	    		" LEFT JOIN ".$this->db->dbprefix('admin_info')." b ON a.create_admin = b.admin_id" .
	    		" WHERE a.order_id = '".$change_id."' AND a.is_return = 3 ORDER BY a.create_date DESC";
	    $query = $this->db->query($sql);
        $res = $query->result_array();
        if (!empty($res))
        {
        	foreach ($res as $row)
        	{
        		$row['change_status'] = $row['order_status'];
        		$row['status'] = $this->format_change_status($row,TRUE);
		        $act_list[] = $row;
        	}
        }
	    return $act_list;
	}

	public function get_change_reasons()
	{
	    return array('次品-具体问题','尺码不合','发错货','发错尺码','顾客原因');
	}

	public function check_change_product_same($old_arr,$new_arr){
	    foreach ($new_arr as $k=>$v)
	    {
	        if (!isset($old_arr[$k])||$old_arr[$k]!=$v)
	            return false;
	        unset($old_arr[$k]);
	    }
	    return empty($old_arr);
	}

	/**
	 *
	 * @param type $change
	 * @param type $sub
	 * @param type $num
	 * @param int $sub_id 这里指cp_id
	 * @return type
	 */
	public function assign_trans($change,$sub,$num,$sub_id)
    {
        if($num<1) return array('err'=>0,'msg'=>'');
        //取trans数据
        $sql = "SELECT t.depot_id,t.location_id,SUM(t.product_number) AS product_number
                FROM ".$this->db->dbprefix('transaction_info')." AS t
                LEFT JOIN ".$this->db->dbprefix('depot_info')." AS d ON t.depot_id=d.depot_id
                LEFT JOIN ".$this->db->dbprefix('location_info')." AS l ON t.location_id=l.location_id
                WHERE d.is_use = 1 AND d.is_return = 0 AND l.is_use = 1 AND t.trans_status IN (1,2,4)
                AND t.product_id=? AND t.color_id = ? AND t.size_id = ?
                GROUP BY l.location_id HAVING product_number>0 ORDER BY d.depot_priority;";
        $query = $this->db->query($sql, array($sub->product_id,$sub->color_id,$sub->size_id));
        $trans = $query->result();
        if(!$trans) return array('err'=>1,'msg'=>'没有库存');
        //分配储位
        $result = array();
        $row = array(
            'trans_type'=>TRANS_TYPE_CHANGE_ORDER,
            'trans_status'=>TRANS_STAT_AWAIT_OUT,
            'trans_sn'=>"'{$change->change_sn}'",
            'product_id'=>$sub->product_id,
            'color_id'=>$sub->color_id,
            'size_id'=>$sub->size_id,
            'sub_id'=>$sub_id,
            'create_admin'=>$this->admin_id,
            'create_date'=>"'{$this->time}'",
            'trans_direction'=>0
            );
        foreach($trans as $t){
            $row['depot_id'] = $t->depot_id;
            $row['location_id'] = $t->location_id;
            $row['product_number'] = min($t->product_number,$num)*-1;
            $result[] = $row;
            $num += $row['product_number']; //因为$row['product_number']为负值，所以此处用+
            if($num==0) break;
        }
        if($num) return array('err'=>1,'msg'=>'分配储位出错');
        //插入储位
        $this->insert_trans_batch($result);

        return array('err'=>0,'msg'=>'');
    }

    public function insert_trans_batch($updates)
    {
        $keys = array('trans_type','trans_status','trans_sn','product_id','color_id','size_id','sub_id','create_admin','create_date','trans_direction','depot_id','location_id','product_number');
        $sql = "INSERT INTO ".$this->db->dbprefix('transaction_info')."
        (".implode(',',$keys).") VALUES ";
        $result = array();
        foreach ($updates as $update) {
            $row = array();
            foreach($keys as $key) $row[$key] = $update[$key];
            $result[] = '('.implode(',',$row).')';
        }
        $sql .= implode(',',$result);
        $this->db->query($sql);
    }
	
	public function notify_shipping ($change)
	{
		$this->load->model('user_model');
		$this->load->model('mail_template_model');
		$this->load->model('shipping_model');
		$user=$this->user_model->filter(array('user_id'=>$change->user_id));
		$template=$this->mail_template_model->filter(array('template_code'=>'change_deliver_notice'));
		if(!$template) return;
		$shipping=$this->shipping_model->filter(array('shipping_id'=>$change->shipping_id));
		if ( $user->email && $template->template_content)
		{
			$shipping_express = $shipping?"配送方式：{$shipping->shipping_name}":'';
			$shipping_express .= $change->invoice_no?" 运单号：{$change->invoice_no}":'';
			$common_template=$this->mail_template_model->filter(array('template_code'=>'mail_frame'));
			$content=str_replace('{$content}',$template->template_content,$common_template->template_content);
			$content=str_replace(
				array('{$change.change_sn}','{$change.shipping_express}'),
				array($change->change_sn,$shipping_express),
				$content
			);
			$content=adjust_path($content);
			$this->db->insert('mail_log',array(
				'mail_from'=>'52kid_service@52kid.cn',
				'mail_to'=>$user->email,
				'template_id'=>$template->template_id,
				'template_subject'=>$template->template_subject,
				'template_content'=>$content,
				'template_priority'=>$template->template_priority,
				'create_admin'=>$this->admin_id,
				'create_date'=>$this->time,
				'status'=>0
			));           
		}
		if ( $user->mobile && $template->sms_content )
		{
			$content=str_replace(
				array('{$change.change_sn}','{$shipping_name}','{$change.invoice_no}'),
				array($change->change_sn,$shipping?$shipping->shipping_name:'',$change->invoice_no),
				$template->sms_content
			);
			$this->db->insert('sms_log',array(
				'sms_from'=>'',
				'sms_to'=>$user->mobile,
				'template_id'=>$template->template_id,
				'template_content'=>$content,
				'sms_priority'=>$template->template_priority,
				'create_admin'=>$this->admin_id,
				'create_date'=>$this->time,
				'status'=>0
			)); 
		}
	}
	
	public function notify_shipped ($change)
	{
		$this->load->model('user_model');
		$this->load->model('mail_template_model');
		$user=$this->user_model->filter(array('user_id'=>$change->user_id));
		$template=$this->mail_template_model->filter(array('template_code'=>'change_storage'));
		if(!$template) return;
		if ( $user->email && $template->template_content)
		{
			$common_template=$this->mail_template_model->filter(array('template_code'=>'mail_frame'));
			$content=str_replace('{$content}',$template->template_content,$common_template->template_content);
			$content=str_replace(
				array('{$change.change_sn}'),
				array($change->change_sn),
				$content
			);
			$content=adjust_path($content);
			$this->db->insert('mail_log',array(
				'mail_from'=>'52kid_service@52kid.cn',
				'mail_to'=>$user->email,
				'template_id'=>$template->template_id,
				'template_subject'=>$template->template_subject,
				'template_content'=>$content,
				'template_priority'=>$template->template_priority,
				'create_admin'=>$this->admin_id,
				'create_date'=>$this->time,
				'status'=>0
			));           
		}
		if ( $user->mobile && $template->sms_content )
		{
			$content=str_replace(
				array('{$change.change_sn}'),
				array($change->change_sn),
				$template->sms_content
			);
			$this->db->insert('sms_log',array(
				'sms_from'=>'',
				'sms_to'=>$user->mobile,
				'template_id'=>$template->template_id,
				'template_content'=>$content,
				'sms_priority'=>$template->template_priority,
				'create_admin'=>$this->admin_id,
				'create_date'=>$this->time,
				'status'=>0
			)); 
		}
	}


}
###