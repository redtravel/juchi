<?php
#doc
#	classname:	Product_model
#	scope:		PUBLIC
#
#/doc

class Return_model extends CI_Model
{
	public function filter ($filter)
	{
		$query = $this->db->get_where('order_return_info', $filter, 1);
		return $query->row();
	}

	public function filter_order ($filter)
	{
		$query = $this->db->get_where('order_info', $filter, 1);
		return $query->row();
	}

	public function filter_payment ($filter)
	{
		$query = $this->db->get_where('payment_info', $filter, 1);
		return $query->row_array();
	}

	public function filter_order_payment ($filter)
	{
		$query = $this->db->get_where('order_payment', $filter, 1);
		return $query->row_array();
	}

	public function filter_voucher_release ($filter)
	{
		$query = $this->db->get_where('voucher_release', $filter, 1);
		return $query->row_array();
	}

	public function filter_voucher ($filter)
	{
		$query = $this->db->get_where('voucher_record', $filter, 1);
		return $query->row_array();
	}

	public function filter_transaction($filter)
	{
		$query = $this->db->get_where('transaction_info', $filter, 1);
		return $query->row_array();
	}
	public function filter_transaction_all ($filter)
	{
		$query = $this->db->get_where('transaction_info', $filter);
		return $query->result_array();
	}

	public function update ($data, $return_id)
	{
		$this->db->update('order_return_info', $data, array('return_id' => $return_id));
	}

	public function update_product ($data, $where_arr)
	{
		$this->db->update('order_return_product', $data, $where_arr);
	}

	public function update_transaction ($data, $where_arr)
	{
		$this->db->update('transaction_info', $data, $where_arr);
	}

	/**
	 * 找到where_arr一个记录。copy成一个新的记录。
	 * 分别对where_arr和新记录进行，data和data_copy进行赋值
	 */
	public function update_transaction_copy($data, $data_copy, $where_arr)
	{
		$trans = $this->filter_transaction( $where_arr );
		unset( $trans['transaction_id'] );
		foreach( $data_copy AS $key=>$val )
			$trans[$key] = $val;

		$this->insert_transaction ( $trans );
		$this->db->update('transaction_info', $data, $where_arr);
	}


	public function update_order_payment ($data, $where_arr)
	{
		$this->db->update('order_payment', $data, $where_arr);
	}

	public function insert ($data)
	{
		$this->db->insert('order_return_info', $data);
		return $this->db->insert_id();
	}

	public function insert_product ($data)
	{
		$this->db->insert('order_return_product', $data);
		return $this->db->insert_id();
	}

	public function insert_payment ($data)
	{
		$this->db->insert('order_payment', $data);
		return $this->db->insert_id();
	}

	public function insert_transaction ($data)
	{
		$this->db->insert('transaction_info', $data);
		return $this->db->insert_id();
	}

	public function insert_advice ($data)
	{
		$this->db->insert('order_advice', $data);
		return $this->db->insert_id();
	}

	public function insert_action($return, $action_note)
    {
        $update = array(
            'order_id' => $return['return_id'],
            'is_return' => 2,
            'order_status' => isset($return['return_status'])?$return['return_status']:0,
            'shipping_status' => isset($return['shipping_status'])?$return['shipping_status']:0,
            'pay_status' => isset($return['pay_status'])?$return['pay_status']:0,
            'action_note' => $action_note,
            'create_admin' => isset($this->admin_id)?$this->admin_id:intval($this->session->userdata('admin_id')),
            'create_date' => isset($this->time)?$this->time:date('Y-m-d H:i:s')
            );
        $this->db->insert('order_action',$update);
        return $this->db->insert_id();
    }

    public function delete ($return_id)
	{
            $this->db->delete('order_return_info', array('return_id' => $return_id));
	}

	public function delete_product ($where_arr)
	{
            $this->db->delete('order_return_product', $where_arr);
	}

	public function delete_payment ($where_arr)
	{
            $this->db->delete('order_payment', $where_arr);
	}

	public function can_return($order)
	{
	    $order_id = $order['order_id'];
	    if ($order['order_status'] != 1 || $order['shipping_status'] != 1 || $order['pay_status'] != 1)
	    {
			return false;
	    }
	    $sql = "SELECT COUNT(return_id) AS ct FROM ".$this->db->dbprefix('order_return_info')." WHERE order_id = '".$order_id."'" .
	    		" AND return_status in ('1','0') AND shipping_status = '0' LIMIT 1";
	    $query = $this->db->query($sql);
		$row = $query->row();
		$query->free_result();
		if ($row->ct > 0)
	    {
	        return false;
	    }
	    return true;
	}
        /**
         * 根据trans_sn号，返回非作废的数据
         * 
         * @param type $order_id
         *  $key = $row['product_id'].'-'.$row['color_id'].'-'.$row['size_id'];
         * @return Array( key=>Array( batch_id=>product_number
         *                               ,batch_id=>product_number,..)
         *                 ,key=>Array( batch_id=>product_number,....)
         *                 );
         */
        public function getSubBatchNumByOrderId( $order_id ){
            //获取与这个订单ID相关的订单号 和退货单号，放入变量$transSn中
            $sql = "SELECT return_sn FROM ty_order_return_info WHERE order_id=".$order_id." AND return_status in (0,1)";
            $query = $this->db_r->query($sql);
            $rs = $query->result_array();$query->free_result(); // get result and free space
            $transSn = Array();
            if( !empty($rs) ){
                foreach( $rs As $row )array_push($transSn,$row['return_sn']);
            }
            $sql = "SELECT order_sn FROM ty_order_info WHERE order_id=".$order_id;
            $query = $this->db_r->query($sql);
            $rs = $query->result_array();$query->free_result(); // get result and free space
            if( !empty($rs) ){
                foreach( $rs As $row )array_push($transSn,$row['order_sn']);
            }
            
            
            if( empty($transSn) ) return Array();
            if(is_array($transSn) ) {
                if ( sizeof($transSn)>1 ) $cond = "IN('".  implode("','", $transSn)."')";
                else $cond = "='".$transSn[0]."'";
            }else $cond = "='".$transSn."'";
             
            // 计算此订单商品批次 未返还库存
            /*
            $sql = "SELECT product_id,color_id,size_id,batch_id,SUM(product_number) as product_number"
                    ." FROM ".$this->db->dbprefix('transaction_info')
                    ." WHERE trans_status in (1,2,4) and trans_sn ".$cond
                    ." GROUP BY product_id,color_id,size_id,batch_id";
            */
            $sql = "SELECT product_id,color_id,size_id,batch_id,shop_price,consign_price,cost_price,consign_rate,product_cess,SUM(product_number) as product_num,expire_date,production_batch"
                    ." FROM ".$this->db->dbprefix('transaction_info')
                    ." WHERE trans_status in (1,2,4) and trans_sn ".$cond
                    ." GROUP BY product_id,color_id,size_id,batch_id HAVING product_num < 0 ";
            
            $query = $this->db_r->query($sql);
            $rs = $query->result_array();   $query->free_result(); // get result and free space
            $result = Array();
            foreach( $rs As $row )
            {                
                $key = $row['product_id'].'-'.$row['color_id'].'-'.$row['size_id'];
                /*
                if(!isset($result[$key])) 
                    $result[$key][$row['batch_id']]=-$row['product_number'] ;
                else $result[$key][$row['batch_id']] = -$row['product_number'];
                 */
                $result[$key][$row['batch_id']]['product_num'] = -$row['product_num'] ;
                $result[$key][$row['batch_id']]['shop_price'] = $row['shop_price'] ;
                $result[$key][$row['batch_id']]['consign_price'] = $row['consign_price'] ;
                $result[$key][$row['batch_id']]['cost_price'] = $row['cost_price'] ;
                $result[$key][$row['batch_id']]['consign_rate'] = $row['consign_rate'] ;
                $result[$key][$row['batch_id']]['product_cess'] = $row['product_cess'] ;
                $result[$key][$row['batch_id']]['expire_date'] = $row['expire_date'] ;
                $result[$key][$row['batch_id']]['production_batch'] = $row['production_batch'] ;
                
            }
            return $result;
        }

        public function order_product_can_return($order_id)
	{
	    $result = array();
	    //订单中的已发货商品
        $sql = "SELECT og.op_id,og.order_id,og.product_id,og.discount_type,og.color_id,og.size_id, ".
                " SUM(og.product_num) AS product_num,SUM(og.consign_num) AS consign_num,gl.sub_id,og.package_id," .
	    		" og.extension_id,og.market_price,og.shop_price,og.product_price,gl.provider_barcode " .
	    		" FROM ".$this->db->dbprefix('order_product')." AS og" .
	    		" LEFT JOIN ".$this->db->dbprefix('order_info')." oi ON oi.order_id = og.order_id" .
	    		" LEFT JOIN ".$this->db->dbprefix('product_sub')." AS gl ON gl.product_id = og.product_id AND gl.color_id = og.color_id AND gl.size_id = og.size_id" .
	    		" WHERE og.order_id = '".$order_id."' AND oi.shipping_status = 1 GROUP BY og.op_id";
	    $query = $this->db->query($sql);
	    $rs = $query->result_array();
	    $query->free_result();
	    if (!empty($rs))
	    {
	    	foreach ($rs as $row)
	    	{
 	    		if (empty($result[$row['op_id']]))
	    		{
					$result[$row['op_id']][0] = $row;
	    		} else
	    		{
	    			$result[$row['op_id']][0]['product_num'] += $row['product_num'];
	            	$result[$row['op_id']][0]['consign_num'] += $row['consign_num'];
	    		}
	    	}
	    }
	    if(empty($result)) return $result;

            // 取得的退货单中商品储位

	    //减去退货单中的商品 cp_id换货单子表主键
	    $sql = "SELECT og.rp_id,og.return_id,og.cp_id,og.op_id,og.product_id,og.color_id,og.size_id,og.product_num,og.consign_num,gl.sub_id" .
	    		" FROM ".$this->db->dbprefix('order_return_product')." AS og" .
	    		" LEFT JOIN ".$this->db->dbprefix('order_return_info')." oi ON oi.return_id = og.return_id" .
	    		" LEFT JOIN ".$this->db->dbprefix('product_sub')." AS gl ON gl.product_id = og.product_id AND gl.color_id = og.color_id AND gl.size_id = og.size_id" .
	    		" WHERE oi.order_id = '".$order_id."' AND oi.return_status in (0,1) ";
		$query = $this->db->query($sql);
	    $rs = $query->result_array();
	    $query->free_result();
            // 计算可退数量:从订单中，减去已退数量。
	    if (!empty($rs))
	    {
	    	foreach ($rs as $row)
	    	{
                    $result[$row['op_id']][$row['cp_id']]['product_num'] -= $row['product_num'];
	            $result[$row['op_id']][$row['cp_id']]['consign_num'] -= $row['consign_num'];
	    	}
	    }

	    //附加商品等信息
	    $gl_ids = array();
	    $product_arr = array();
	    $gl_arr = array();
	    foreach($result as $rec_id=>$value){
	        foreach($value as $track_id=>$v){
	            if(!isset($v['product_num']) || $v['product_num']<=0) continue;
	            $v['rec_id'] = $rec_id;
	            $v['track_id'] = $track_id;
	            $product_arr[] = $v;
	            $gl_ids[] = $v['sub_id'];
	        }
	    }

	    if(!empty($gl_ids)){
	    	$sql = "SELECT gl.sub_id,g.product_name,g.product_sn,g.provider_productcode,b.brand_name,c.color_name,c.color_sn," .
	    			"s.size_name,s.size_sn, g.unit_name,g.is_gifts,gl.gl_num,gl.consign_num as gl_consign_num,gl.wait_num,g.brand_id,g.category_id, gl.provider_barcode, pg.img_url ".
	    		" FROM ".$this->db->dbprefix('product_sub')." AS gl" .
	    		" LEFT JOIN ".$this->db->dbprefix('product_info')." g ON g.product_id = gl.product_id" .
	    		" LEFT JOIN ".$this->db->dbprefix('product_size')." AS s ON s.size_id = gl.size_id" .
	    		" LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON c.color_id = gl.color_id" .
	    		" LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON b.brand_id = g.brand_id" .
	    		" LEFT JOIN ".$this->db->dbprefix('product_gallery')." AS pg ON pg.product_id = gl.product_id AND pg.color_id = gl.color_id AND pg.image_type = 'default'" .
	    		" WHERE gl.sub_id ".db_create_in($gl_ids);
	    	$query = $this->db->query($sql);
		    $rs = $query->result_array();
		    $query->free_result();
		    if (!empty($rs))
		    {
		    	foreach ($rs as $row)
		    	{
		    		$gl_arr[$row['sub_id']] = $row;
		    	}
		    }
		    foreach($product_arr as $key=>$product)
		    {
	            $product_arr[$key] = array_merge($product,$gl_arr[$product['sub_id']]);
	        }
	    }
	    return $product_arr;
	}

	public function get_order_info($order_id,$order_sn = '',$apply_id = 0)
	{
	    $order_id = intval($order_id);
	    $where = $order_id > 0? " where o.order_id  = $order_id " : ($apply_id > 0 ? " where o.order_sn = '$order_sn' and ri.apply_id = '$apply_id'" : " where o.order_sn = '$order_sn'");
	    $sql = "select o.*,o.order_price+o.shipping_fee as total_amount,u.*,
	                r.region_name as country_name,pr.region_name as province_name,cr.region_name as city_name,dr.region_name as district_name,
	                s.shipping_name,s.shipping_code,p.pay_name,p.pay_code, p.is_online as online_pay, f.source_name,f.source_code, ri.apply_id
	                from ".$this->db->dbprefix('order_info')." as o
	                left join ".$this->db->dbprefix('user_info')." as u on o.user_id = u.user_id
	                left join ".$this->db->dbprefix('region_info')." as r on o.country = r.region_id
	                left join ".$this->db->dbprefix('region_info')." as pr on o.province = pr.region_id
	                left join ".$this->db->dbprefix('region_info')." as cr on o.city = cr.region_id
	                left join ".$this->db->dbprefix('region_info')." as dr on o.district = dr.region_id
	                left join ".$this->db->dbprefix('shipping_info')." as s on o.shipping_id = s.shipping_id
	                left join ".$this->db->dbprefix('payment_info')." as p on o.pay_id = p.pay_id
	                left join ".$this->db->dbprefix('order_source')." as f on f.source_id = o.source_id
                        left join ".$this->db->dbprefix('apply_return_info')." as ri on o.order_id = ri.order_id
	                ".$where;
	    $query = $this->db->query($sql);
		$order = $query->row_array();
		$query->free_result();
	    if($order) {
	        $order = $this->format_order($order);
	    }else {
	        $order = NULL;
	    }
	    return $order;
	}

	public function format_order($order)
	{
	    $order['formated_order_price']   = number_format($order['order_price'], 2, '.', '');
	    $order['formated_paid_price']     = number_format($order['paid_price'], 2, '.', '');
	    $order['formated_user_money'] = number_format($order['user_money'], 2, '.', '');
	    $order['formated_real_shipping_fee'] = number_format($order['real_shipping_fee'], 2, '.', '');
	    $order['formated_shipping_fee'] = number_format($order['shipping_fee'], 2, '.', '');
	    $order['formated_total_amount']   = number_format($order['total_amount'], 2, '.', '');
	    $order['formated_create_date']       = date('Y-m-d H:i:s', strtotime($order['create_date']));
	    $order['formated_confirm_date']  = $order['order_status']!=0?date('Y-m-d H:i:s', strtotime($order['confirm_date'])):'未客审';
	    $order['formated_finance_date']  = $order['pay_status']==1?date('Y-m-d H:i:s', strtotime($order['finance_date'])):'未财审';
	    $order['formated_shipping_date']       = $order['shipping_status']==1?date('Y-m-d H:i:s', strtotime($order['shipping_date'])):'未发货';
	    $order['money_refund'] = $order['order_price'] - $order['paid_price'];
	    $order['formated_money_refund'] = number_format($order['money_refund'], 2, '.', '');
	    $order['invoice_no']    = $order['shipping_status'] == 0 ?'' : $order['invoice_no'];
	    $order['status']        = '暂空';
	    $order['is_available'] = !((4 == $order['order_status'])||(3==$order['order_status'])) && !$order['is_ok'];
	    $order['is_complete'] = 1 == $order['order_status'] && 1 == $order['pay_status'] && 1 == $order['shipping_status'];
	    if($order['lock_admin']==-1)
	        $order['lock_name'] = '拣货中';
	    elseif($order['lock_admin']==-2)
	        $order['lock_name'] = '调仓中';
	    elseif($order['lock_admin']==-3)
	        $order['lock_name'] = '物流对帐中';
	    return $order;
	}

	public function format_return($return) {
	    $return['formated_return_price']   = number_format($return['return_price'], 2, '.', '');
	    $return['formated_paid_price']     = number_format($return['paid_price'], 2, '.', '');
	    $return['formated_total_amount']  = number_format($return['total_amount'], 2, '.', '');
	    $return['returned_amount']  = $return['return_price'] - $return['paid_price'];
	    $return['formated_returned_amount']  = number_format($return['returned_amount'], 2, '.', '');
	    $return['formated_create_date']       = date('Y-m-d H:i:s', strtotime($return['create_date']));
	    $return['formated_confirm_date']       = $return['return_status']!=0?date('Y-m-d H:i:s', strtotime($return['confirm_date'])):'未客审';
	    $return['formated_finance_date']       = $return['pay_status']==1?date('Y-m-d H:i:s', strtotime($return['finance_date'])):'未财审';
	    $return['formated_shipping_date']       = $return['shipping_status']==1?date('Y-m-d H:i:s', strtotime($return['shipping_date'])):'未入库';
	    $return['status']     = $this->format_return_status($return);
	    $return['is_available'] = !((4 == $return['return_status'])||(3==$return['return_status']));
	    $return['hope_time'] = date('Y-m-d', strtotime($return['hope_time']));
	    $return['formated_return_shipping_fee'] = number_format($return['return_shipping_fee'], 2, '.', '');
	    return $return;
	}


	public function get_voucher_payment($order_id)
	{
	    $sql = "select op.*,p.pay_code,p.pay_name ,v.voucher_amount,v.voucher_sn,v.release_id,v.campaign_id,a.product,a.brand,a.category,v.min_order,v.start_date,v.end_date
	            from ".$this->db->dbprefix('order_payment')." as op
	            left join ".$this->db->dbprefix('payment_info')." as p on op.pay_id = p.pay_id
	            left join ".$this->db->dbprefix('voucher_record')." as v on v.voucher_sn = op.payment_account
	            left join ".$this->db->dbprefix('voucher_campaign')." as a on v.campaign_id = a.campaign_id
	            where op.is_return = 0 and p.pay_code = 'voucher' and op.order_id = '".$order_id."'";
	    $query = $this->db->query($sql);
		$result = $query->row_array();
		$query->free_result();
	    if(empty($result)) {return NULL;}
	    $result['formated_payment_money'] = number_format($result['payment_money'], 2, '.', '');
    	$result['formated_voucher_amount'] = number_format($result['voucher_amount'], 2, '.', '');
	    return $result;
	}

	public function get_return_reasons(){
	    return array('次品-具体问题','尺码不合','发错货','发错尺码','顾客原因');
	}

	public function return_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('order_return_info')." AS a ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['return_sn']))
		{
			$where .= " AND a.return_sn LIKE '%" . mysql_like_quote($filter['return_sn']) . "%' ";
		}

		if (!empty($filter['order_sn']))
		{
			$where .= " AND ord.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%' ";
		}

		if (!empty($filter['consignee']))
		{
			$where .= " AND a.consignee LIKE '%" . mysql_like_quote($filter['consignee']) . "%' ";
		}

		if (isset($filter['return_status']) && $filter['return_status'] != -1)
        {
            $where .= " AND a.return_status  = '".$filter['return_status']."'";
        }
        if(isset($filter['shipping_id']) && $filter['shipping_id'] > 0 ){
            $where .= " AND ord.shipping_id  = '".$filter['shipping_id']."'";
        }
        if (isset($filter['shipping_status']) && $filter['shipping_status'] != -1)
        {
            $where .= " AND a.shipping_status = '".$filter['shipping_status']."'";
        }
        if (isset($filter['pay_status']) && $filter['pay_status'] != -1)
        {
            $where .= " AND a.pay_status = '".$filter['pay_status']."'";
        }
        if (isset($filter['is_ok']) && $filter['is_ok'] != -1)
        {
            $where .= " AND a.is_ok = '".$filter['is_ok']."'";
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

        if (isset($filter['composite_status']))
        {
			switch($filter['composite_status'])
	        {
	            case '100' :
	                $where .= " AND a.return_status = '1' AND a.shipping_status = '1' AND a.pay_status = '0' ";
	                break;

	            case '101' :
	                $where .= " AND a.return_status = '1' AND a.shipping_status = '0' AND a.pay_status = '0' ";
	                break;

	            case '102' :
	                $where .= " AND a.return_status = '1' AND a.shipping_status = '1' AND a.pay_status = '1' ";
	                break;

	            default:
	                if ($filter['composite_status'] != -1)
	                {
	                    $where .= " AND a.return_status = '".$filter['composite_status']."' ";
	                }
	        }
        }


		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.return_id' : trim($filter['sort_by']);
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
		$sql = "SELECT a.return_id, a.return_sn, a.create_date, a.return_status, a.shipping_status, a.return_price, au.admin_name as lock_name," .
                    "a.pay_status, a.consignee, a.address, a.email, a.tel,a.mobile,ord.order_sn,ord.order_id,a.lock_admin, a.is_ok, " .
                    "ship.shipping_name,IFNULL(u.user_name, '匿名') AS buyer, a.apply_id ".
                " FROM " . $this->db->dbprefix('order_return_info') . " AS a " .
                " LEFT JOIN " . $this->db->dbprefix('admin_info') . " AS au ON  a.lock_admin = au.admin_id " .
                " LEFT JOIN " .$this->db->dbprefix('order_info'). " AS ord ON a.order_id=ord.order_id  ".
                " LEFT JOIN " .$this->db->dbprefix('shipping_info')." AS ship ON ship.shipping_id=ord.shipping_id ".
                " LEFT JOIN " .$this->db->dbprefix('user_info'). " AS u ON u.user_id=a.user_id ".
                $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

		$query = $this->db->query($sql, $param);
		$list = $query->result();
		if (!empty($list))
		{
			foreach ($list as $key=>$return_info)
			{

				if ($return_info->return_status == 0)
				{
					$return_info->return_status_name = '未确认';
				} elseif ($return_info->return_status == 1)
				{
					$return_info->return_status_name = '已确认';
				} elseif ($return_info->return_status == 4)
				{
					$return_info->return_status_name = '<font color="red">作废</font>';
				}
				if ($return_info->pay_status == 0)
				{
					$return_info->pay_status_name = '未返款';
				} elseif ($return_info->pay_status == 1)
				{
					$return_info->pay_status_name = '已返款';
				}

				if ($return_info->shipping_status == 0)
				{
					$return_info->shipping_status_name = '未入库';
				} elseif ($return_info->shipping_status == 1)
				{
					$return_info->shipping_status_name = '已入库';
				}
				$return_info->formated_return_price = $return_info->return_price;
				$return_info->short_return_time = date('m-d H:i', strtotime($return_info->create_date));
				$list[$key] = $return_info;
			}
		}

		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function return_info($return_id, $return_sn = '')
	{
	    $return_id = intval($return_id);
	    $where = $return_id > 0? " where return_id  = $return_id " : " where return_sn = '$return_sn'";
	    $sql = "SELECT r.*,oi.shipping_true, r.return_price + r.return_shipping_fee as total_amount,
	            u.user_name,u.user_money,u.pay_points,u.real_name
                FROM ".$this->db->dbprefix('order_return_info')." AS r 
                LEFT JOIN ".$this->db->dbprefix('order_info')." AS oi ON r.order_id = oi.order_id 
	            LEFT JOIN ".$this->db->dbprefix('user_info')." AS u ON r.user_id = u.user_id
	            ".$where;

	    $query = $this->db->query($sql);
		$return = $query->row_array();
		$query->free_result();
	    if($return) {
	        $return = $this->format_return($return);
	    }else {
	        $return = NULL;
	    }
	    return $return;
	}


	public function return_product ($return_id)
	{
		$product_list = array();
	    $sql = "SELECT o.*, gl.gl_num,gl.consign_num as gl_consign_num,gl.wait_num,
	    		g.product_name,g.product_sn,g.unit_name,g.provider_productcode,g.brand_id,g.category_id,g.is_gifts,
	    		c.color_name, s.size_name,b.brand_name,cat.category_name,bcat.category_name as bcategory_name,gl.provider_barcode,l.location_name,d.depot_name,t.production_batch,t.expire_date " .
	        "FROM " . $this->db->dbprefix('order_return_product') . " AS o ".
	        "LEFT JOIN " . $this->db->dbprefix('product_sub') . " AS gl ON o.product_id = gl.product_id and o.color_id = gl.color_id and o.size_id = gl.size_id " .
	        "LEFT JOIN " . $this->db->dbprefix('product_info') ." AS g ON o.product_id = g.product_id " .
	        "LEFT JOIN " . $this->db->dbprefix('product_brand') . " AS b ON g.brand_id = b.brand_id " .
	        "LEFT JOIN " . $this->db->dbprefix('product_color') . " AS c ON o.color_id = c.color_id " .
	        "LEFT JOIN " . $this->db->dbprefix('product_size') . " AS s ON o.size_id = s.size_id " .
	        "LEFT JOIN " . $this->db->dbprefix('product_category') . " AS cat ON g.category_id = cat.category_id " .
	        "LEFT JOIN " . $this->db->dbprefix('product_category') . " AS bcat ON cat.parent_id = bcat.category_id " .
	        "LEFT JOIN " . $this->db->dbprefix('order_return_info') . " AS ori ON ori.return_id = o.return_id " .
	        "LEFT JOIN " . $this->db->dbprefix('transaction_info') . " AS t ON t.trans_sn = ori.return_sn AND t.product_id = gl.product_id AND t.color_id = gl.color_id AND t.size_id = gl.size_id " .
	        "LEFT JOIN " . $this->db->dbprefix('location_info') . " AS l ON l.location_id = t.location_id " .
	        "LEFT JOIN " . $this->db->dbprefix('depot_info') . " AS d ON d.depot_id = l.depot_id " .
	        "WHERE o.return_id = '$return_id' ";

	    $query = $this->db->query($sql);
	    $product_list = $query->result_array();
	    $product_allnum = 0;
	    if (!empty($product_list))
	    foreach ($product_list as $key =>$row)
	    {
			$row['formated_subtotal']       = number_format($row['product_price'] * $row['product_num'], 2, '.', '');
	        $row['formated_product_price']    = number_format($row['product_price'], 2, '.', '');
	        $product_allnum += $row['product_num'];
	        $product_list[$key] = $row;
	    }
	    return $product_list;
	}

	public function order_product($order_id)
    {
        $sql = "SELECT op.*, p.product_name, p.product_sn,p.provider_productcode, p.unit_name,op.shop_price,cat.category_id,
                cat.category_name, b.brand_id,b.brand_name, c.color_name, c.color_sn, s.size_name, s.size_sn,sub.sub_id,sub.gl_num,sub.consign_num as gl_consign_num,
                ti.batch_id,ti.consign_price,ti.cost_price,ti.consign_rate,ti.product_cess
                FROM ".$this->db->dbprefix('order_product')." AS op
                LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON op.product_id = p.product_id
                LEFT JOIN ".$this->db->dbprefix('product_category')." AS cat ON cat.category_id = p.category_id
                LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON b.brand_id = p.brand_id
                LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON c.color_id = op.color_id
                LEFT JOIN ".$this->db->dbprefix('product_size')." AS s ON s.size_id = op.size_id
                LEFT JOIN ".$this->db->dbprefix('product_sub')." AS sub ON op.product_id = sub.product_id AND op.color_id = sub.color_id AND op.size_id = sub.size_id
                LEFT JOIN ".$this->db->dbprefix('order_info')." AS oi on oi.order_id=op.order_id 
                LEFT JOIN ".$this->db->dbprefix('transaction_info')." AS ti on oi.order_sn=ti.trans_sn and ti.trans_type=3 AND ti.trans_status != 5 AND op.product_id = ti.product_id AND op.color_id = ti.color_id AND op.size_id = ti.size_id 
                WHERE op.order_id = ? 
                GROUP BY op.op_id";
        $query = $this->db->query($sql, array($order_id));
        return $query->result_array();
    }

	public function get_order_returned_product($order_id,$return_id,$payed = false)
	{
	    $product_list = array();
	    $sql = "SELECT rg.*, g.product_name,g.product_sn, g.provider_productcode,g.brand_id,g.category_id " .
	        "FROM " . $this->db->dbprefix('order_return_product') . " AS rg ".
	        "left join " . $this->db->dbprefix('order_return_info') . " AS r on rg.return_id = r.return_id ".
	        "LEFT JOIN " . $this->db->dbprefix('product_info') ." AS g ON rg.product_id = g.product_id " .
	        "WHERE r.order_id = '".$order_id."' and r.return_id != '".$return_id."' and r.return_status in (0,1) ";

	    if ($payed==true)
	    {
	    	$sql .= " and r.pay_status = 1";//未财审的不算
	    }
	    $query = $this->db->query($sql);
	    $rs = $query->result_array();
	    if (!empty($rs))
	    {
	    	foreach ($rs as $row)
	    	{
	    		$row['formated_subtotal'] = number_format($row['product_price'] * $row['product_num'], 2, '.', '');
	       		$row['formated_product_price']    = number_format($row['product_price'], 2, '.', '');
	        	$product_list[] = $row;
	    	}
	    }
	    return $product_list;
	}

	public function get_return_sn()
	{
	    /* 选择一个随机的方案 */
	    mt_srand((double) microtime() * 1000000);
	    return "TH".date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
	}

	public function return_discount_payment($return_id,$split = 1,$return_product=NULL,$order_id=NULL)
	{
	    $discount_payment = array(); //result to return
	    if($return_product===NULL) $return_info = $this->return_info($return_id); //如果预测，则不必要取return_info
	    if($return_product===NULL) $order_id = $return_info['order_id']; // order_id,如果是预测，则用传入的参数
	    $order_payment_group = $this->get_payment_group($order_id);

	    //如果有多项折扣作用于订单，报错
	    $discount_number = 0;
	    foreach($order_payment_group as $key=>$payment){
	        if($payment['is_discount']==1 && $payment['payment_money']>0){
	            $discount_number += 1;
	            $discount = $payment;
	        }
	    }
	    if($discount_number > 1){ // more than one discount payment for the order.
	        return array('error'=>1,'message'=>'原订单使用了多于一种的折扣，不能处理退货');
	    }

	    if(empty($discount)){
	        return array('error'=>0,'message'=>'原订单没有折扣');
	    }
	    switch($discount['pay_code']){
	        case 'voucher':
	            $voucher = $this->get_voucher_payment($order_id);
	            $voucher['product_arr'] = empty($voucher['product'])?array():explode(',',$voucher['product']);
	            $voucher['category_arr'] = empty($voucher['category'])?array():explode(',',$voucher['category']);
	            $voucher['brand_arr'] = empty($voucher['brand'])?array():explode(',',$voucher['brand']);

				$order_product = $this->order_product($order_id);
	            if($return_product===NULL) $return_product = $this->return_product($return_id); //如果是预测，则手工传入商品列表
	            $voucher_product_amount = 0;//原订单中涉及抵用券的所有商品总价
	            $voucher_product_amount_return = 0;//退单中涉及的商品总价

	            foreach($return_product as $product) {
	                if($product['shop_price']!=$product['product_price'] || $product['package_id']!=0) {
	                    continue;
	                }
	                if(($voucher['product_arr']===array()||in_array($product['product_id'],$voucher['product_arr']))&&($voucher['category_arr']===array()||in_array($product['category_id'],$voucher['category_arr']))&&($voucher['brand_arr']===array()||in_array($product['brand_id'],$voucher['brand_arr']))) {
	                    $voucher_product_amount_return += $product['product_price']*$product['product_num'];
	                }
	            }
	            if($voucher_product_amount_return==0) {
	                return array('error'=>0,'message'=>'退单中不涉及现金券');
	            }

	            foreach($order_product as $product) {
	                if($product['shop_price']!=$product['product_price'] || $product['package_id']!=0) {
	                    continue;
	                }
	                if(($voucher['product_arr']===array()||in_array($product['product_id'],$voucher['product_arr']))&&($voucher['category_arr']===array()||in_array($product['category_id'],$voucher['category_arr']))&&($voucher['brand_arr']===array()||in_array($product['brand_id'],$voucher['brand_arr']))) {
	                    $voucher_product_amount += $product['product_price']*$product['product_num'];
	                }
	            }
	            $voucher_amount_returned = 0; //已退还过的抵用券金额，不计本退单
	            $sql = "SELECT IFNULL(SUM(op.payment_money),0) as voucher_amount_returned" .
	            		" FROM ".$this->db->dbprefix('order_payment')." as op" .
	            		" LEFT JOIN ".$this->db->dbprefix('order_return_info')." as r ON r.return_id = op.order_id AND op.is_return = 1" .
	            		" LEFT JOIN ".$this->db->dbprefix('payment_info')." as p ON op.pay_id = p.pay_id" .
	            		" WHERE p.pay_code IN ('voucher_payback','voucher_deduct') AND r.order_id = '".$order_id."' AND r.return_id != '".$return_id."' AND r.return_status in (0,1)";
	            $query = $this->db->query($sql);
			    $therow = $query->row_array();
			    $query->free_result();
	            $voucher_amount_returned = $therow['voucher_amount_returned'];
	            $voucher_amount_returned = abs($voucher_amount_returned);
	            if(isset($voucher['payment_money']) && $voucher['payment_money']>=$voucher_product_amount && $voucher_amount_returned==0) {//全退反券
	                if($voucher_product_amount_return!=$voucher_product_amount) {
	                    return array('error'=>1,'message'=>'该订单涉及现金券的商品如果要退货需要全部退。');
	                }else {
	                    $discount_payment = array('pay_code'=>'voucher_payback','payment_money'=>$voucher_product_amount_return);
	                    return array('error'=>0,'message'=>'','discount_payment'=>$discount_payment);
	                }
	            }

	            $voucher_amount_now = (isset($voucher['payment_money'])?$voucher['payment_money']:0) - $voucher_amount_returned;
	            if($voucher_amount_now <= 0){
	                return array('error'=>0,'message'=>'退单中不涉及现金券。');
	            }
	            $voucher_product_amount_returned = 0;
	            $returned_product = $this->get_order_returned_product($order_id,$return_id);
	            foreach($returned_product as $product) {
	                if($product['shop_price']!=$product['product_price'] || $product['package_id']!=0) {
	                    continue;
	                }
	                if(($voucher['product_arr']===array()||in_array($product['product_id'],$voucher['product_arr']))&&($voucher['category_arr']===array()||in_array($product['category_id'],$voucher['category_arr']))&&($voucher['brand_arr']===array()||in_array($product['brand_id'],$voucher['brand_arr']))) {
	                    $voucher_product_amount_returned += $product['product_price']*$product['product_num'];
	                }
	            }
	            $voucher_product_amount_now = $voucher_product_amount - $voucher_product_amount_returned;
	            $voucher_product_amount_left = $voucher_product_amount_now - $voucher_product_amount_return;
	            $real_payment = $voucher_product_amount_now - $voucher_amount_now;
	            if($voucher_amount_now >= $voucher_product_amount_return){ //如果券值 >= 退品价格
	                if($voucher_product_amount_left >= $voucher['min_order']){//余下满足
	                    if($voucher_product_amount_return > $real_payment){
	                        $discount_payment = array('pay_code'=>'voucher_deduct','payment_money'=>$voucher_product_amount_return - $real_payment);
	                        return array('error'=>0,'message'=>'','discount_payment'=>$discount_payment);
	                    }else{
	                        return array('error'=>0,'message'=>'不必退现金券');
	                    }
	                }else{//余下不满足
	                    $discount_payment = array('pay_code'=>'voucher_deduct','payment_money'=>round($voucher['payment_money']*($voucher_product_amount_return/$voucher_product_amount),2));
	                    if($voucher_product_amount_left<=0){
	                        $discount_payment['payment_money'] = $voucher_amount_now;
	                    }
	                    return array('error'=>0,'message'=>'','discount_payment'=>$discount_payment);
	                }
	            }else{//券值小于退品额
	                if($voucher_product_amount_left >= $voucher['min_order'] && $voucher_product_amount_left!=0 ){//余下满足
	                    if($voucher_product_amount_return > $real_payment){
	                        $discount_payment = array('pay_code'=>'voucher_deduct','payment_money'=>$voucher_product_amount_return - $real_payment);
	                        return array('error'=>0,'message'=>'','discount_payment'=>$discount_payment);
	                    }else{
	                        return array('error'=>0,'message'=>'不必退现金券');
	                    }
	                }else{//余下不满足
	                    if(($split==2||$voucher_product_amount_left==0) && $voucher_amount_now == $voucher['payment_money']){//返还券
	                        $discount_payment = array('pay_code'=>'voucher_payback','payment_money'=>$voucher_amount_now);
	                        return array('error'=>0,'message'=>'','discount_payment'=>$discount_payment);
	                    }else{// 拆分券
	                        $discount_payment = array('pay_code'=>'voucher_deduct','payment_money'=>round($voucher['payment_money']*($voucher_product_amount_return/$voucher_product_amount),2));
	                        if($voucher_product_amount_left<=0) {
	                            $discount_payment['payment_money'] = $voucher_amount_now;
	                        }
	                        return array('error'=>0,'message'=>'','discount_payment'=>$discount_payment);
	                    }
	                }
	            }
	            break;
	        default:
	            return array('error'=>0,'message'=>'订单中不涉及现金券折扣');
    	}
	}

	public function get_payment_group($order_id)
	{
	    $sql = "SELECT IFNULL(SUM(op.payment_money),0) as payment_money, p.pay_id, p.pay_code,p.pay_name,p.is_discount,p.back_type" .
	    		" FROM ".$this->db->dbprefix('payment_info')." as p" .
	    		" LEFT JOIN ".$this->db->dbprefix('order_payment')." as op ON p.pay_id = op.pay_id AND op.order_id = '".$order_id."' AND op.is_return = 0" .
	    		" GROUP by p.pay_code";
	    $query = $this->db->query($sql);
	    $rs = $query->result_array();
	    $query->free_result();
		$group_arr = array();
	    if (!empty($rs))
	    {
	    	foreach ($rs as $group)
	    	{
				$group['formated_payment_money'] = number_format($group['payment_money'], 2, '.', '');
	        	$group_arr[$group['pay_code']] = $group;
	    	}
	    }

	    return $group_arr;
	}

	public function get_return_payment_group($return_id)
	{
	    $sql = "SELECT IFNULL(SUM(op.payment_money),0) as payment_money, p.pay_id, p.pay_code,p.pay_name" .
	    		" FROM ".$this->db->dbprefix('payment_info')." as p" .
	    		" LEFT JOIN ".$this->db->dbprefix('order_payment')." as op ON p.pay_id = op.pay_id AND op.order_id = '".$return_id."' AND op.is_return = 1" .
	    		" GROUP by p.pay_code";
	    $query = $this->db->query($sql);
	    $rs = $query->result_array();
	    $query->free_result();
		$group_arr = array();
	    if (!empty($rs))
	    {
	    	foreach ($rs as $group)
	    	{
				$group['formated_payment_money'] = number_format($group['payment_money'], 2, '.', '');
	        	$group_arr[$group['pay_code']] = $group;
	    	}
	    }

	    return $group_arr;
	}

	public function update_return_amount($return_id){
	    $sql = "UPDATE ".$this->db->dbprefix('order_return_info')." as r" .
	    		" LEFT JOIN (SELECT IFNULL(SUM(payment_money),0) as payment_money, IFNULL(order_id,'".$return_id."') as return_id" .
	    		" FROM ".$this->db->dbprefix('order_payment').
	    		" WHERE is_return = 1 and order_id = '".$return_id."') as p ON p.return_id = r.return_id" .
	    		" LEFT JOIN (SELECT IFNULL(SUM(product_price*product_num),0) as product_amount,IFNULL(SUM(product_num),0) as product_num, IFNULL(return_id,'".$return_id."') as return_id" .
	    		" FROM ".$this->db->dbprefix('order_return_product').
				" WHERE return_id = '".$return_id."') as g ON g.return_id = r.return_id" .
				" SET r.return_price = g.product_amount,r.product_num = g.product_num,r.paid_price = -1 * p.payment_money" .
				" WHERE r.return_id = '".$return_id."'";
	    $this->db->query($sql);
	}

	/**
	 * 更新退货单的运费
	 * @param <type> $return_id
	 * @param <type> $order_id
	 * @param <type> $return
	 * @param <type> $order
	 * @param <type> $choice
	 */
	public function update_return_shipping_fee($return_id,$order_id=NULL,$return=NULL,$order=NULL,$choice=NULL) {

	    if($return===NULL) $return = $this->return_info($return_id);
	    if($order_id===NULL) $order_id = $return['order_id'];
	    if($order===NULL) $order = $this->filter_order(array('order_id'=>$order_id));
	    $return_shipping_fee = $this->calc_return_shipping_fee($order_id,$order,$choice);
	    if($return['return_shipping_fee']!=$return_shipping_fee) {
	        $update_arr = array(
	            'return_shipping_fee' =>$return_shipping_fee
	        );
	        $this->update($update_arr,$return_id);
			$return['return_shipping_fee'] = $return_shipping_fee;
	    }
	    return array('error'=>0,'content'=>$return);
	}

	public function calc_return_shipping_fee($order_id,$order=NULL,$choice=NULL) {
	    $shipping_fee = 0;
	    if($order===NULL) {
	        $order = $this->filter_order(array('order_id'=>$order_id));
	    }
	    if($order['shipping_fee']==0) return 0;

		$sql = "SELECT SUM(return_price) as returned_return_price FROM ".$this->db->dbprefix('order_return_info')." WHERE order_id = '".$order_id."' and return_status IN (0,1)";
	    $query = $this->db->query($sql);
	    $rs = $query->row_array();
	    $query->free_result();
	    $returned_product_amount = $rs['returned_return_price'];
	    if($returned_product_amount < $order['order_price']) return 0;
	    if($choice===NULL) return NULL;
	    if($choice==1){
	        return $order['shipping_fee'];
	    }else{
	        return 0;
	    }
	}

	/**
	 * 返回某个退货单可执行的操作列表，包括权限判断
	 * @param   array   $order      退货单信息 order_status, shipping_status, pay_status
	 * @param   bool    $is_cod     支付方式是否货到付款
	 * @return  array   可执行的操作  confirm, pay, unpay, prepare, ship, unship, receive, cancel, invalid, return, drop
	 * 格式 array('confirm' => true, 'pay' => true)
	 */
	function get_return_perm($return)
	{
	    /* 取得退货单状态、发货状态、付款状态 */
	    $os = $return['return_status'];
	    $ss = $return['shipping_status'];
	    $ps = $return['pay_status'];

	    /* 退货单是否已全额支付,并且没有多付 */
	    $has_pay_all = ($return['return_price']-$return['paid_price'] == 0);// 财务完全结完

	    /* 退货单是否有支付 */
	    $sql = "SELECT SUM(payment_money) AS payment_money FROM ".$this->db->dbprefix('order_payment')." WHERE is_return = 1 and order_id = ".$return['return_id']. " AND payment_admin != -1";
	    $query = $this->db->query($sql);
	    $rs = $query->row_array();
	    $query->free_result();
	    $manual_payment_amount = $rs['payment_money'];
	    $has_pay    = $manual_payment_amount >0 ? true : false;
	    $is_complete = 1 == $os && 1 == $ps && 1 == $ss;
	    $is_ok = $return['is_ok'] == 1;

	    /* 取得退货单的锁定状态 */
	    $is_locked = $return['lock_admin'] != 0;
	    $locked_by_self = $is_locked && ($return['lock_admin'] == $this->admin_id); //被自己锁定
	    $locked_by_other = $is_locked && ($return['lock_admin'] != $this->admin_id); //被他人锁定
	    $is_available = ((0 == $os)||(1==$os)) && !$is_ok;  //退货单有效

	    /* 根据状态返回可执行操作 */
	    $list = array('save'=>FALSE,'service_confirm'=>FALSE,'unservice_confirm'=>FALSE,'pay'=>FALSE,'ship'=>FALSE,'lock'=>FALSE,'invalid'=>FALSE,'is_ok'=>FALSE,'unlock'=>FALSE);

	     // 保存退货单  退货单有效  有编辑权限
	    if ($is_available && $locked_by_self && check_perm('order_return_edit') && (0 == $os))
	    {
	    	$list['save'] = TRUE;
	    }

	     // 客审:退货单未确认  退货单被自己锁定  退货单有效 有order_service_confirm权限
	    if (check_perm('order_return_confirm') && (0 == $os) && $is_available &&$locked_by_self)
	    {
	        $list['service_confirm'] = TRUE;
	    }

	     // 反客审  条件1：退货单已确认  条件2：未入库  条件3：有order_unservice_confirm权限  条件4：退货单被自己锁定  条件5：退货单有效
	    if ((1 == $os) && (0 == $ss) && check_perm('order_return_unconfirm') && $is_available &&$locked_by_self)
	    {
	    	$list['unservice_confirm'] = TRUE;
	    }


	    // 财审 条件1：未财审 条件2：已入库 条件3：order_pay权限 条件4：自我锁定 条件5：已全额返扣款 条件6：退货单有效
	    if ($is_available && (0 == $ps) &&(1 == $ss)&& check_perm('order_return_pay')  && $has_pay_all &&$locked_by_self) {
	    	$list['pay'] =  TRUE;
	    }

	     // 入库 条件1：未入库 条件2：已确认 条件3：order_ship权限  条件4：被自己锁定 条件5：退货单有效
	    if ((0 == $ss) && (1 == $os) && check_perm('order_return_ship') && $is_available &&$locked_by_self) {
	    	$list['ship'] = TRUE;
	    }

	     // 锁定与解锁
	    if($locked_by_self||($is_locked&&check_perm('super_unlock')&&$return['lock_admin']>0))
	    {
	    	$list['unlock'] = TRUE;
	    }
	    if (!$is_locked && !$is_ok) {
	    	$list['lock'] = TRUE;
	    }


	     // 作废 条件1：未入库，未返款,未客审 条件2：order_invalid权限 条件3：退货单有效 条件4：被自己锁定
	    if ((0 == $os) && (0 == $ss)&& !$has_pay && check_perm('order_return_invalid') && $is_available && $locked_by_self ) {
	    	$list['invalid'] = TRUE;
	    }

	    /**
	     * 完结
	     * 客服，编辑，订单complete，没有is_ok,被自己锁定,订单已走完所有的流程（取消和作废的单子都是自动完结的）
	     */
	    if (check_perm('order_return_ok') && $locked_by_self && $is_complete && !$is_ok) {
	    	$list['is_ok'] = TRUE;
	    }

	    // 退货人信息，确认前可以由客服改,条件：客服，未客审，有效,被自己锁定
	    $list['edit_consignee'] = check_perm('order_return_edit') && 0 == $os && $is_available &&$locked_by_self;
	    // 商品信息，付款前可以由客服改，条件：客服，未客审，有效,未审核,被自己锁定
	    $list['edit_product'] = (check_perm('order_return_edit') || check_perm('order_return_confirm') || check_perm('order_return_unconfirm') || check_perm('order_return_ok') || check_perm('order_invalid')) && 0 == $os && $is_available &&$locked_by_self;
            // 支付明细:财务，未财审,已入库,被自己锁定
	    $list['pay_list'] = check_perm('order_return_pay') && 0 == $ps && 1 ==$ss && $is_available && $locked_by_self;
		
	    return $list;
	}

	/**
	 * 获取当前退货单的所有支付记录
	 * @author Tony
	 * @return array
	 */
	public function get_pay_detail_arr($order_id, $is_return = 0) {
	    $alipay_bank_list = array(
		    'ICBCB2C'    => array('pay_code'=>'ICBCB2C','pay_name'=>'中国工商银行','pay_logo'=>'data/payment/ICBCB2C.gif'),
		    'BOCB2C'    => array('pay_code'=>'BOCB2C','pay_name'=>'中国银行在','pay_logo'=>''),
		    'CMB'       => array('pay_code'=>'CMB','pay_name'=>'招商银行','pay_logo'=>''),
		    'CCB'       => array('pay_code'=>'CCB','pay_name'=>'中国建设银行','pay_logo'=>''),
		    'ABC'       => array('pay_code'=>'ABC','pay_name'=>'中国农业银行','pay_logo'=>''),
		    'SPDB'      => array('pay_code'=>'SPDB','pay_name'=>'上海浦东发展银行','pay_logo'=>''),
		    'CIB'       => array('pay_code'=>'CIB','pay_name'=>'兴业银行','pay_logo'=>''),
		    'GDB'       => array('pay_code'=>'GDB','pay_name'=>'广东发展银行','pay_logo'=>''),
		    'SDB'       => array('pay_code'=>'SDB','pay_name'=>'深圳发展银行','pay_logo'=>''),
		    'CMBC'      => array('pay_code'=>'CMBC','pay_name'=>'中国民生银行','pay_logo'=>''),
		    'COMM'      => array('pay_code'=>'COMM','pay_name'=>'交通银行','pay_logo'=>''),
		    'CITIC'     => array('pay_code'=>'CITIC','pay_name'=>'中信银行','pay_logo'=>''),
		    'HZCBB2C'   => array('pay_code'=>'HZCBB2C','pay_name'=>'杭州银行','pay_logo'=>''),
		    'CEBBANK'   => array('pay_code'=>'CEBBANK','pay_name'=>'中国光大银行','pay_logo'=>''),
		);
	    $sql="SELECT a.* , a.payment_remark as payment_desc,b.pay_name, b.pay_code, c.admin_name FROM ".$this->db->dbprefix('order_payment') ." AS a" .
	    		" LEFT JOIN ". $this->db->dbprefix('payment_info') ." AS b on a.pay_id = b.pay_id" .
	    		" LEFT JOIN ". $this->db->dbprefix('admin_info') ." AS c on a.payment_admin = c.admin_id" .
	    		" WHERE a.order_id= '".$order_id."' AND is_return = '".$is_return."'";
	    $query = $this->db->query($sql);
	    $payment_recordset = $query->result_array();

	    foreach ($payment_recordset as $key=>$payment_record)
	    {
	        $payment_recordset[$key]['formated_payment_money'] = number_format(abs($payment_record['payment_money']), 2, '.', '');
	        if ($payment_record['payment_money']<0)
	        {
	            $payment_recordset[$key]['formated_payment_money'] = "<font color=red>".$payment_recordset[$key]['formated_payment_money']."</font>";
	        }
	        if ($payment_record['payment_admin'] == -1)
	        {
	            $payment_recordset[$key]['admin_name'] = "<font color=red>系统</font>";
	        }
	        if ($payment_record['pay_code']=='deduct')
	        {
	            $payment_desc=explode("$$$@$$$",$payment_record['payment_desc']);
	            $payment_record['payment_desc']='';
	            if (isset($payment_desc[0]))
	            {
	            	$payment_desc[0]!='' && $payment_record['payment_desc'].='['.$payment_desc[0]."] ";
	            }
	            if (isset($payment_desc[1]))
	            {
					$payment_desc[1]!='' && $payment_record['payment_desc'].='['.$payment_desc[1]."] ";
	            }
				if (isset($payment_desc[2]))
				{
					$payment_desc[2]!='' && $payment_record['payment_desc'].=''.$payment_desc[2];
				}

	            $payment_recordset[$key]['payment_desc'] = $payment_record['payment_desc'];
	        }
	        if($payment_record['pay_code'] == 'credit')
	        {
	            if(preg_match('/^现金\$\$\$@\$\$\$/',$payment_record['payment_desc'])!=0)
	            {
	                $payment_recordset[$key]['pay_name'] = '现金';
	                $payment_recordset[$key]['payment_desc'] = str_replace('现金$$$@$$$','',$payment_record['payment_desc']);
	            }elseif(preg_match('/^信用卡\$\$\$@\$\$\$/',$payment_record['payment_desc'])!=0)
	            {
	                $payment_recordset[$key]['pay_name'] = '信用卡';
	                $payment_recordset[$key]['payment_desc'] = str_replace('信用卡$$$@$$$','',$payment_record['payment_desc']);
	            }
	        }
	        if (isset($payment_record['bank_code']) && !empty($payment_record['bank_code']))
	        	$payment_recordset[$key]['pay_name'] .= '&nbsp;'.$alipay_bank_list[$payment_record['bank_code']]['pay_name'];
	    }
	    return $payment_recordset;
	}

	/**
	 * 获取所有意见类型
	 * @author Tony
	 * @return array
	 */
	public function get_advice_type_arr () {

	    $sql 	= "SELECT type_id, type_name FROM ". $this->db->dbprefix('order_advice_type') ." ORDER BY type_code ASC";
	    $query = $this->db->query($sql);
	    $result = $query->result_array();
	    $arr=array();
	    foreach ($result as $item) {
	        $arr[$item['type_id']]	=	$item['type_name'];
	    }
	    return $arr;
	}

	public function get_return_advice($order_id){
	    /*$sql="SELECT s.*, st.type_name,st.type_color,ad.admin_name
	            FROM ".$this->db->dbprefix('order_advice') ." AS s
	            LEFT JOIN ". $this->db->dbprefix('order_advice_type'). " AS st ON s.type_id= st.type_id
	            LEFT JOIN ". $this->db->dbprefix('admin_info') ." AS ad ON s.advice_admin = ad. admin_id
	            WHERE s.order_id = '".$return_id."' and s.is_return = 2";
            //*/
	        $order_id = intval($order_id);
            $sql = "select oa.*,ai.admin_name,t.type_name,t.type_color from ".$this->db->dbprefix('order_advice') ." oa 
                left join ".$this->db->dbprefix('admin_info') ." ai on oa.advice_admin = ai.admin_id 
                left join ". $this->db->dbprefix('order_advice_type'). " t on oa.type_id = t.type_id
                where oa.is_return = 2 and oa.order_id = '".$order_id."' 
                or oa.is_return = 1 
                and oa.order_id in (select order_id from ". $this->db->dbprefix('order_return_info'). " where return_id = '".$order_id."') ORDER BY oa.advice_date DESC";

            /*$sql = "select oa.*,ai.admin_name,t.type_name,t.type_color from ".$this->db->dbprefix('order_advice') ." oa 
                left join ".$this->db->dbprefix('admin_info') ." ai on oa.advice_admin = ai.admin_id 
                left join ". $this->db->dbprefix('order_advice_type'). " t on oa.type_id = t.type_id
                where oa.order_id = $order_id";*/
	    $query = $this->db->query($sql);
	    $result = $query->result_array();
	    return $result;
	}

	public function get_return_voucher_back($return_id)
	{
		$sql = "SELECT op.* FROM ".$this->db->dbprefix('order_payment')." AS op" .
				" LEFT JOIN ".$this->db->dbprefix('payment_info')." AS p ON p.pay_id = op.pay_id" .
				" WHERE op.is_return=1 and op.order_id = '".$return_id."' and p.pay_code = 'voucher_payback'";
		$query = $this->db->query($sql);
	    return $query->row_array();
	}

	public function get_return_product_location($return)
	{
			$sql = "SELECT t.*, concat(d.depot_name,'<br/>',p.location_name) as location_name" .
				" FROM ".$this->db->dbprefix('transaction_info')." as t" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." as p ON p.location_id = t.location_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." as d ON d.depot_id = p.depot_id" .
				" WHERE t.trans_type = ".TRANS_TYPE_RETURN_ORDER." AND t.trans_sn = '".$return['return_sn']."' AND t.trans_status in (".TRANS_STAT_AWAIT_IN.",".TRANS_STAT_IN.")  ";
            $query = $this->db->query($sql);
            $trans_rs = $query->result_array();
            $return_product = $this->return_product($return['return_id']);
            if (!empty($trans_rs))
            {
                $product_arr = array();
                foreach($return_product as $product) {
                    $product_arr[$product['rp_id']] = $product;
                }
                $return_product = array();
                foreach($trans_rs as $key=>$trans) {
                    $trans_product = $product_arr[$trans['sub_id']];
                    $trans_product['product_num'] = abs($trans['product_num']);
                    $trans_product['product_amount'] = $trans_product['product_num'] * $trans_product['product_price'];
                    $trans_product['subtotal'] = number_format($trans_product['product_amount'], 2, '.', '');
                    $trans_product['location_code'] = $trans['location_name'];
                    $trans_product['no'] = $key + 1;
                    $return_product[] = $trans_product;
                }
            }else {
                foreach($return_product as $key=>$product) {
                    $return_product[$key]['no'] = $key + 1;
                }
            }
            return $return_product;
	}

	/* 取得退货单操作记录 */
	public function get_action_list($return_id)
	{
	    $act_list = array();
	    $sql = "SELECT a.*,b.admin_name FROM " . $this->db->dbprefix('order_action') . " a" .
	    		" LEFT JOIN ".$this->db->dbprefix('admin_info')." b ON a.create_admin = b.admin_id" .
	    		" WHERE a.order_id = '".$return_id."' AND a.is_return = 2 ORDER BY a.create_date DESC";
	    $query = $this->db->query($sql);
        $res = $query->result_array();
        if (!empty($res))
        {
        	foreach ($res as $row)
        	{
        		$row['return_status'] = $row['order_status'];
        		$row['status'] = $this->format_return_status($row,TRUE);
		        $act_list[] = $row;
        	}
        }
	    return $act_list;
	}

	public function format_return_status($return,$red=FALSE)
	{
	    $status = array();
	    if($return['return_status']==0) $status[] = '未客审';
	    if($return['return_status']==1) $status[] = '已客审';
	    if($return['return_status']==4) $status[] = $red?'<font color="red">已作废</font>':'已作废';
	    if($return['return_status']==5) $status[] = $red?'<font color="red">已拒收</font>':'已拒收';
	    $status[] = $return['pay_status']?'已财审':'未财审';
	    $status[] = $return['shipping_status']?'已收货':'未收货';
	    if (isset($return['is_ok'])) {
	        if($return['is_ok']) $status[] = $red?'<font color="red">已完结</font>':'已完结';
	        if(!$return['is_ok']) $status[] = '未完结';
	    }

	    return $status;
	}

	public function get_order_by_subid($src_og_ids)
	{
		$sql = "SELECT t.sub_id,t.trans_sn,t.product_number,d.depot_name,p.location_name, t.depot_id, t.location_id " .
				" FROM ".$this->db->dbprefix('transaction_info')." as t" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." as d ON t.depot_id = d.depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." as p ON t.location_id = p.location_id" .
				" WHERE t.trans_type = ".TRANS_TYPE_SALE_ORDER." AND t.trans_status = ".TRANS_STAT_OUT." AND sub_id ".db_create_in($src_og_ids);

		$query = $this->db->query($sql);
        return $query->result_array();
	}

	public function get_change_by_subid($src_cg_ids)
	{
		$sql = "SELECT t.sub_id,t.trans_sn,t.product_number,d.depot_name,p.location_name" .
				" FROM ".$this->db->dbprefix('transaction_info')." as t" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." as d ON t.depot_id = d.depot_id" .
				" LEFT JOIN ".$this->db->dbprefix('location_info')." as p ON t.location_id = p.location_id" .
				" WHERE t.trans_type = ".TRANS_TYPE_CHANGE_ORDER." AND t.trans_status = ".TRANS_STAT_OUT." AND sub_id ".db_create_in($src_cg_ids);
		$query = $this->db->query($sql);
        return $query->result_array();
	}

	/**
	 * 自动生成退货单返扣款记录 ,处理的折扣有：voucher,employee_discount,group_discount
	 * @param int $return_id
	 * @return array
	 */
	public function auto_discount_payment($return_id){
	    $discount_payment = array(); //result to return
	    $return_info = $this->return_info($return_id);
	    $order_id = $return_info['order_id']; // order_id
	    $order_payment_group = $this->get_payment_group($order_id);

	    //如果有多项折扣作用于订单，报错
	    $discount_number = 0;
	    foreach($order_payment_group as $key=>$payment){
	        if($payment['is_discount']==1 && $payment['payment_money']>0){
	            $discount_number += 1;
	            $discount = $payment;
	        }
	    }
	    if($discount_number > 1){ // more than one discount payment for the order.
	        return array('error'=>1,'message'=>'原订单使用了多于一种的折扣，不能处理退货');
	    }

	    if(empty($discount)){
	        return array('error'=>0,'message'=>'原订单没有折扣');
	    }
	    switch($discount['pay_code']){
	        case 'employee_discount'://员工折扣
	        case 'group_discount'://团购折扣
	            $discount_product_amount_return = 0;
	            $return_product = $this->return_product($return_id);
	            foreach($return_product as $product){
	                if(($product['shop_price']!=$product['product_price']) || $product['package_id']!=0){
	                    continue;
	                }
	                $discount_product_amount_return += $product['product_price']*$product['product_num'];
	            }
	            if($discount_product_amount_return == 0){
	                return array('error'=>0,'message'=>'');
	            }
	            $discount_product_amount = 0;
	            $order_product = $this->order_product($order_id);
	            foreach($order_product as $product){
	                if(($product['shop_price']!=$product['product_price']) || $product['package_id']!=0){
	                    continue;
	                }
	                $discount_product_amount += $product['product_price']*$product['product_num'];
	            }

	            $discount_payment = array('pay_code'=>$discount['pay_code']."_deduct",'payment_money'=>0);

	            //计算已退涉及商品的总价
	            $discount_product_amount_returned = 0;
	            $returned_product = $this->get_order_returned_product($order_id,$return_id,true);
	            foreach($returned_product as $product) {
	                if(($product['shop_price']!=$product['product_price']) || $product['package_id']!=0) {
	                    continue;
	                }
	                $discount_product_amount_returned += $product['product_price']*$product['product_num'];
	            }
	            if($discount_product_amount_returned + $discount_product_amount_return >= $discount_product_amount) {//如果已退商品+现退商品= 总涉及商品，则计算已退还的金额
	                $discount_amount_returned = 0;

	                $sql = "SELECT IFNULL(SUM(payment_money),0) as discount_amount_returned" .
							" FROM ".$this->db->dbprefix('order_payment')." as op" .
							" LEFT JOIN ".$this->db->dbprefix('order_return_info')." as r ON r.return_id = op.order_id AND op.is_return = 1" .
							" LEFT JOIN ".$this->db->dbprefix('payment_info')." as p ON op.pay_id = p.pay_id" .
							" WHERE p.pay_code ='".$discount['pay_code']."_deduct' AND r.order_id = '".$order_id."' and r.return_id != '".$return_id."' AND r.return_status IN (0,1) AND r.pay_status=1 ";
				    $query = $this->db->query($sql);
				    $discount_amount_row = $query->row_array();
				    $discount_amount_returned = $discount_amount_row['discount_amount_returned'];
	                $discount_payment['payment_money'] = $discount['payment_money'] + $discount_amount_returned;
	            }else{
	                $discount_payment['payment_money'] = round($discount['payment_money']*($discount_product_amount_return/$discount_product_amount),2);
	            }
	            return array('error'=>0,'message'=>'','discount_payment'=>$discount_payment);
	            break;
	        default:
	            return array('error'=>0,'message'=>'不可自动处理的折扣方式');
	    }
	}

	public function query_order_payment ($payment_amount,$pay_code,$return_id)
	{
		$sql = "SELECT * FROM ".$this->db->dbprefix('order_payment')." as rp" .
				" LEFT JOIN ".$this->db->dbprefix('payment_info')." as p ON rp.pay_id = p.pay_id" .
				" WHERE rp.order_id = $return_id AND p.pay_code = '".$pay_code."' AND rp.payment_money = '".$payment_amount."' ";
	    $query = $this->db->query($sql);
        return $query->row_array();
	}

	public function log_account_change($user_id, $user_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_code = '',$link_id='')
	{
		if($user_money != 0)
		{
			/* 插入帐户变动记录 */
		    $account_log = array(
		        'user_id'       => $user_id,
		        'user_money'    => $user_money,
		        'pay_points'    => 0,
		        'create_date'   => date('Y-m-d H:i:s'),
		        'change_desc'   => $change_desc,
		        'change_code'   => $change_code,
				'link_id'		=>$link_id,
				'create_admin'		=>$this->admin_id
		    );
			$this->db->insert('user_account_log', $account_log);

		    /* 更新用户信息 */
		    $sql = "UPDATE " . $this->db->dbprefix('user_info') .
		            " SET user_money = user_money + ('$user_money')," .
		            " pay_points = pay_points + ('0')" .
		            " WHERE user_id = '$user_id' LIMIT 1";
		    $this->db->query($sql);
		}
		if($pay_points != 0)
		{
			if($change_code == 'money_balance') $change_code = 'point_balance';
			/* 插入帐户变动记录 */
		    $account_log = array(
		        'user_id'       => $user_id,
		        'user_money'    => 0,
		        'pay_points'    => $pay_points,
		        'create_date'   => date('Y-m-d H:i:s'),
		        'change_desc'   => $change_desc,
		        'change_code'   => $change_code,
				'link_id'		=>$link_id,
				'create_admin'		=>$this->admin_id
		    );
		    $this->db->insert('user_account_log', $account_log);

		    /* 更新用户信息 */
		    $sql = "UPDATE " . $this->db->dbprefix('user_info') .
		            " SET user_money = user_money + ('0')," .
		            " pay_points = pay_points + ('$pay_points')" .
		            " WHERE user_id = '$user_id' LIMIT 1";
		    $this->db->query($sql);
		}
	}

	public function get_depot_by_location($location_ids)
	{
		$sql = "SELECT DISTINCT p.location_id,p.depot_id,d.depot_type" .
				" FROM ".$this->db->dbprefix('location_info')." as p" .
				" LEFT JOIN ".$this->db->dbprefix('depot_info')." as d on p.depot_id = d.depot_id" .
				" WHERE p.is_use = 1 and d.is_use = 1 and  p.location_id ".db_create_in($location_ids);
		$query = $this->db->query($sql);
        return $query->result_array();
	}

	public function update_productsub_by_recid($rec_arr)
	{
		$sql = "UPDATE ".$this->db->dbprefix('product_sub')." as gl, " .
				"(SELECT SUM(product_num-consign_num) as product_num, product_id,color_id,size_id" .
				" FROM ".$this->db->dbprefix('order_return_product')." WHERE rp_id ".db_create_in($rec_arr)."" .
				" GROUP BY product_id,color_id,size_id ) as og" .
				" SET gl.gl_num = gl.gl_num + og.product_num" .
				" WHERE gl.product_id = og.product_id and gl.color_id = og.color_id and gl.size_id = og.size_id";
		$this->db->query($sql);
	}

	// 去掉里面的库存
	public function update_productsub_by_ctb_unsale($rec_arr)
	{
		foreach( $rec_arr AS $key=>$val )
		{
			$sql = "UPDATE ".$this->db->dbprefix('product_sub')." as gl " .
				" SET gl.gl_num = gl.gl_num - " . $val['num'] .
				" WHERE gl.product_id = ".$val['product_id']. 
				" and gl.color_id = ".$val['color_id']. 
				" and gl.size_id = ". $val['size_id'];
			$this->db->query($sql);

		}
	}

	// 只更新虚库数量
	public function update_productsub_by_returnid($return_id)
	{
		$sql = "UPDATE ".$this->db->dbprefix('product_sub')." as gl," .
				" (SELECT SUM(consign_num) as consign_num, product_id,color_id,size_id" .
				" FROM ".$this->db->dbprefix('order_return_product')." WHERE return_id = '".$return_id."'" .
				" GROUP BY product_id,color_id,size_id ) as og" .
				" SET gl.consign_num = gl.consign_num + IF(gl.consign_num>=0,og.consign_num,0)" .
				" WHERE gl.product_id = og.product_id and gl.color_id = og.color_id and gl.size_id = og.size_id";
		$this->db->query($sql);
	}

	public function get_pay_arr()
	{
		$array=array();
		$sql="SELECT pay_id, pay_name from ".$this->db->dbprefix('payment_info')." WHERE pay_code IN ('payback','deduct') ORDER BY pay_code";
		$query = $this->db->query($sql);
        $result = $query->result_array();
		foreach ($result as $item)
		{
			$array[$item['pay_id']]=$item['pay_name'];
		}
		return $array;
	}

	public function order_product_list($order_id)
	{
	    $sql = "SELECT o.sub_id as gl_id,o.product_id, o.color_id, o.size_id, o.gl_num,o.consign_num as gl_consign_num,o.wait_num, c.color_name, s.size_name" .
	    		" FROM " . $this->db->dbprefix('product_sub') . " as o ".
	        	" LEFT JOIN " . $this->db->dbprefix('product_color') . " AS c ON c.color_id = o.color_id " .
	       		" LEFT JOIN " . $this->db->dbprefix('product_size') . " AS s ON s.size_id = o.size_id " .
	        	" WHERE o.product_id IN (SELECT DISTINCT product_id FROM " . $this->db->dbprefix('order_product') . " WHERE order_id = '".$order_id."')";

	    $query = $this->db->query($sql);
        $res = $query->result_array();
	    $now_product = 0;
	    $rs = array();
	    if (!empty($res))
	    {
	    	foreach ($res as $row)
		    {
		    	$row['real_num'] = max($row['gl_num'] - $row['wait_num'],0);
	        	$rs[$row['product_id']][$row['product_id'].'-'.$row['color_id'].'-'.$row['size_id']] = $row;
		    }
	    }
	    return $rs;
	}

	public function cart_product_number($product_id,$color_id,$size_id)
	{
	    $sql = "SELECT SUM(product_num) as num FROM ".$this->db->dbprefix('cart').
				" WHERE  goods_id = '".$product_id."' AND color_id = '".$color_id."' AND size_id = '".$size_id."'" .
				" AND  update_time>=$now-$cart_expired_time";
	    $query = $this->db->query($sql);
        $row = $query->row_array();
	    return isset($$row['num'])?$$row['num']:0;
	}

	public function get_regions($type = 0, $parent = 0)
	{
	    $sql = 'SELECT region_id, region_name FROM ' . $this->db->dbprefix('region_info') .
	            " WHERE region_type = '".$type."' AND parent_id = '".$parent."'";
		$query = $this->db->query($sql);
        return $query->result_array();
	}
	
	public function notify_ship ($return)
	{
		$this->load->model('user_model');
		$this->load->model('mail_template_model');
		$user=$this->user_model->filter(array('user_id'=>$return->user_id));
		$template=$this->mail_template_model->filter(array('template_code'=>'return_storage'));
		if(!$template) return;
		if ( $user->email && $template->template_content)
		{
			$common_template=$this->mail_template_model->filter(array('template_code'=>'mail_frame'));
			$content=str_replace('{$content}',$template->template_content,$common_template->template_content);
			$content=str_replace(
				array('{$return.return_sn}'),
				array($return->return_sn),
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
				array('{$return.return_sn}'),
				array($return->return_sn),
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
	/**
	 * 返回某个退货单商品所在批次的状态.is_lock,锁定; is_reckoned 结算
	 */
	public function get_batch_return_products( $return_id )
	{
		$sql="select ti.batch_id,pb.batch_code,if( pb.lock_admin is null,false,true ) AS is_lock, 
			pb.lock_date, pi.product_name , pb.is_reckoned, ti.product_number, ti.sub_id, ti.product_id 
			from ty_order_return_info  as ri
			left join ty_transaction_info as ti on ri.return_sn= ti.trans_sn AND ti.trans_status = 2 
			left join ty_product_info as pi on pi.product_id=ti.product_id
			left join ty_purchase_batch as pb on pb.batch_id=ti.batch_id 
			where ri.return_id=".$return_id;
		$query = $this->db_r->query($sql);
		return $query->result_array();
	}
	
	public function get_return_product_cooperation($rp_id) {
		$sql = "SELECT e.provider_cooperation 
		FROM ".$this->db->dbprefix('order_return_product')." AS a
		LEFT JOIN ".$this->db->dbprefix('order_return_info')." AS b ON a.return_id=b.return_id
		LEFT JOIN ".$this->db->dbprefix('transaction_info')." AS c ON b.return_sn=c.trans_sn AND a.product_id=c.product_id 
		LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS d ON c.batch_id=d.batch_id
		LEFT JOIN ".$this->db->dbprefix('product_provider')." AS e ON d.provider_id=e.provider_id
		WHERE a.rp_id=?";
		$query = $this->db_r->query($sql,array($rp_id));
		return $query->row();
	}
	
}
###
