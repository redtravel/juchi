<?php
/**
*
*/
class Order_model extends CI_Model
{
	private $_db;
	public function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	public function filter($filter)
	{
		$query = $this->_db->get_where('order_info',$filter,1);
		return $query->row();
	}

	public function lock_order($filter)
	{
		$sql="SELECT * FROM ".$this->_db->dbprefix('order_info')." ";
		$param = array();
		if(isset($filter['order_id'])){
			$sql .= " WHERE order_id = ? ";
			$param[] = $filter['order_id'];
		}else{
			$sql .= " WHERE order_sn = ? ";
			$param[] = $filter['order_sn'];
		}
		$sql.=" LIMIT 1 FOR UPDATE";
		$query = $this->_db->query($sql,$param);
		return $query->row();
	}

        public function get_wait_pay_ing_order_num($user_id) {
            $result = array();
            $sql = "SELECT count(1) as cnt FROM ty_order_info o" .
	            " LEFT JOIN ty_payment_info AS p ON o.pay_id = p.pay_id".
	            " LEFT JOIN ty_order_action AS a ON o.order_id = a.order_id AND a.is_return=1 AND a.order_status = 4 
                      WHERE o.user_id = $user_id AND o.shipping_true=1 AND o.order_status = '0' AND (order_price + shipping_fee - paid_price) > 0 AND p.is_online = 1";
            $row = $this->db->query($sql)->row_array();
            if (isset($row)) {
                $result["wait_pay_num"] = $row["cnt"];
            } else {
                $result["wait_pay_num"] = 0;
            }
            
            
            $sql = "SELECT count(1) as cnt FROM ty_order_info o WHERE o.user_id = $user_id AND o.shipping_true=1 AND o.order_status = '0' AND o.is_ok = '0' ; ";
            $row = $this->db->query($sql)->row_array();
            if (isset($row)) {
                $result["ing_num"] = $row["cnt"];
            } else {
                $result["ing_num"] = 0;
            }
            
            return $result;
        }
    public function course_list($user_id){
        $sql = 'SELECT o.*,op.shop_price,p.`product_name`,p.`brand_name`,p.`subhead`,p.`package_name`,p.`product_desc_additional`,p.`ps_num`,pg.img_url,pi.is_online FROM ty_order_info o LEFT JOIN ty_payment_info pi ON o.pay_id = pi.pay_id LEFT JOIN ty_order_product op ON o.`order_id`=op.`order_id` LEFT JOIN ty_product_info p ON p.product_id=op.`product_id` LEFT JOIN ty_product_gallery pg ON p.`product_id`=pg.`product_id` WHERE o.`user_id`='.$user_id.' AND o.genre_id = 2 ORDER BY order_id DESC LIMIT 30';
        $rows = $this->db->query($sql)->result();
        /*foreach ($rows as &$row){
            $row->order_amount = $row->order_price + $row->shipping_fee - $row->paid_price;
            $row->is_online = 1;
        }*/
        return $rows;
    }
        
    public function order_simple_list($user_id, $page, $status = null){
        $page_size = M_LIST_PAGE_SIZE*5;
        $page = ($page < 1) ? 1 : intval($page);
        $start = ($page-1)*$page_size;
        //AND o.order_status = '1'
        $sql = "SELECT o.order_id,o.create_date,o.order_sn,o.paid_price,o.order_price,o.shipping_fee,o.shipping_id,o.shipping_status,o.pay_id,o.order_status,o.pay_status,o.is_ok,o.product_num,p.is_online FROM ".$this->db->dbprefix('order_info')." o LEFT JOIN ty_payment_info AS p ON o.pay_id = p.pay_id WHERE o.user_id = $user_id AND o.order_status != 3 AND o.genre_id = 1"; 
        switch($status){
        case 'pending' ://待付款
            $sql .= " AND o.pay_status = 0 AND o.order_status IN (0,1) AND (o.order_price + o.shipping_fee)>o.paid_price AND p.is_online = 1";
            break;
        case 'wait_shipping' ://待发货
            $sql .= ' AND o.pay_status = 1 AND o.shipping_status = 0';
            break;
        case 'wait_comment' ://待评价
            //$sql .= 'not in (select 1 from ty_product_liuyan as pl where pl.tag_id = )';
            //$where .= ' o.is_ok = 1 ';
            break;
        default :
            break;


        }
        $sql .= " ORDER BY order_id DESC LIMIT $start, $page_size";
        $rows = $this->db->query($sql)->result();
        //$row->order_amount>0 && 
        foreach ($rows as &$row){
            $row->format_create_date=date("Y-m-d",strtotime($row->create_date));
            $row->order_amount = $row->order_price + $row->shipping_fee - $row->paid_price;
            $row->can_pay = $row->order_amount>0 && $row->is_online == 1 && 0 == $row->pay_status;
            $row->total_fee=$row->order_price+$row->shipping_fee;
            $row->total_fee=sprintf('%.2f',$row->total_fee);
            $row->order_amount=sprintf('%.2f',$row->order_amount);
        }
        return $rows;
    }
	public function order_list($filter,$user_id)
	{
		$where = " WHERE o.user_id = '" . $user_id . "' AND o.order_status != '3'";//显示虚发虚腿订单// AND o.shipping_true=1 ";
                if (!empty($filter['order_id'])) {
                    $where .= " AND o.order_id = " .$filter['order_id'];
                }
		$from = " FROM ".$this->_db->dbprefix('order_info')." AS o ";

		if (!isset($filter['order_status']))
		{
			$filter['order_status'] = 1;
		}
		switch ($filter['order_status'])
		{
				case '1' : //所有订单
					break;

				case '2' : //未审核
					$where .= " AND o.order_status = '0' AND o.is_ok = '0' ";
					break;

				case '3' : //处理中
					$where .= " AND o.order_status != '0' AND o.is_ok = '0' ";
					break;

				case '4' : //已成交
					$where .= " AND o.order_status = '1' AND o.shipping_status = '1' AND o.pay_status = '1' AND o.is_ok = '1' ";
					break;
				case '5' : //已作废
					$where .= " AND o.order_status in ('4','5') AND o.is_ok = '1' ";
					break;
                                case '6'://待付款
                                        $where .= " AND o.order_status in (0, 1) AND o.order_price + o.shipping_fee > o.paid_price ";
                                        break;
                                case '7'://待收货
                                        $where .= " AND o.order_status = '1' AND o.shipping_status = '0' AND o.pay_status = '1'";
                                        break;
				default:
					break;
		}

		$filter['sort_by'] = 'o.create_date';
		$filter['sort_order'] = 'DESC';

		$sql = "SELECT COUNT(*) AS ct " . $from . $where;
		$query = $this->_db->query($sql);
		$row = $query->row();
		$query->free_result();

		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}

		/* 查询记录 */
	    $sql = "SELECT o.*,p.pay_code,p.is_online,p.is_online as online_pay, a.action_note, pr.region_name as province_name, s.shipping_name " .$from.
                    " LEFT JOIN ".$this->_db->dbprefix('shipping_info')." s ON o.shipping_id = s.shipping_id ".
	            " LEFT JOIN ".$this->_db->dbprefix('payment_info')." AS p ON o.pay_id = p.pay_id".
	            " LEFT JOIN ".$this->_db->dbprefix('order_action')." AS a ON o.order_id = a.order_id AND a.is_return=1 AND a.order_status = 4 ".
                    " LEFT JOIN ".$this->_db->dbprefix('region_info')." AS pr ON o.province = pr.region_id ".
	             $where ." ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->_db->query($sql);
		$rows = $query->result();
		$orderIds = Array();
		if (!empty($rows))
		{
			foreach ($rows as &$row)
			{
                            $row->order_goods = $this->order_product($row->order_id);
				//格式化状态值
		    	$row->format_create_date=date("Y-m-d",strtotime($row->create_date));
		    	$row->order_amount = $row->order_price + $row->shipping_fee - $row->paid_price;
		    	$row->order_status_txt = order_status($row);
		    	$row->pay_status_txt = pay_status($row);
		    	$row->can_pay = $row->order_amount>0 && $row->online_pay==1 && !$row->order_status;
		    	$row->invalid = $row->order_status==4 || $row->order_status==5;
		    	$row->total_fee=$row->order_price+$row->shipping_fee;
		    	$row->total_fee=sprintf('%.2f',$row->total_fee);
		    	$row->order_amount=sprintf('%.2f',$row->order_amount);
		        array_push( $orderIds, $row->order_id );
			}
			unset($row);
		}

	    if( !empty($orderIds) ){
	        $return_arr = array();
	        $change_arr = array();
	        // 其中退货的单子
	        $sql = "select IFNULL(sum(rg.product_num),0) as return_number, r.order_id
	            from ".$this->_db->dbprefix('order_return_product')." as rg
	            left join ".$this->_db->dbprefix('order_return_info')." as r on rg.return_id = r.return_id
	            where order_id ".db_create_in($orderIds)."
	            and r.return_status in (0,1) group by r.order_id";
	        $query = $this->_db->query($sql);
			$rs = $query->result();
			if (!empty($rs))
			{
				foreach ($rs as $row)
				{
					$return_arr[$row->order_id] = $row->return_number;
				}
			}

	        // 其中换货的单子
	        $sql = "select count(change_id) as change_number, order_id
	            from  ".$this->_db->dbprefix('order_change_info')."
	            where order_id ".db_create_in($orderIds)."
	            and change_status in (0,1) group by order_id";
	        $query = $this->_db->query($sql);
			$rs = $query->result();
			if (!empty($rs))
			{
				foreach ($rs as $row)
				{
					$change_arr[$row->order_id] = $row->change_number;
				}
			}
                // 供应商合作方式
                $sql = "SELECT t.order_id, p.provider_id, p.provider_cooperation, concat(p.return_address, '<br/>收件人:', p.return_consignee, '&nbsp;&nbsp;联系电话:', p.return_mobile) AS return_address 
                        FROM ty_order_product t
                        LEFT JOIN ty_product_info i USING(product_id)
                        LEFT JOIN ty_product_provider p USING(provider_id)
                        WHERE t.order_id ".db_create_in($orderIds)."
                        GROUP BY t.order_id";
                $query = $this->_db->query($sql);
                $rs = $query->result();
                if (!empty($rs))
                {
                        foreach ($rs as $row)
                        {
                                $cooper_arr[$row->order_id] = $row->provider_cooperation;
                                $provider_arr[$row->order_id] = $row->provider_id;
                               
                                if ($row->provider_cooperation == 3) {
                                    $sql = "SELECT concat(p.return_address, '<br/>收件人:', p.return_consignee, '&nbsp;&nbsp;联系电话:', p.return_mobile) AS return_address FROM ty_product_provider p WHERE p.provider_id = 1";
                                    $result = $this->_db->query($sql)->row();
                                    $returnaddr_arr[$row->order_id] = $result->return_address;
                                } elseif ($row->provider_cooperation == 4) {
                                    $returnaddr_arr[$row->order_id] = $row->return_address;
                                }
                        }
                }
	        // 填充订单列表
	        if( !empty($change_arr) || !empty($return_arr) || !empty($cooper_arr) || !empty($provider_arr) || !empty($returnaddr_arr))
	        {
                        $this->load->helper('order_helper');
	        	foreach( $rows AS $key=>$value ){
	                if(isset($return_arr[$value->order_id])) {
	                    $value->return_number = $return_arr[$value->order_id];
	                }else {
	                    $value->return_number = 0;
	                }
	                if(isset($change_arr[$value->order_id])) {
	                    $value->change_number = $change_arr[$value->order_id];
	                }else {
	                    $value->change_number = 0;
	                }
                        if(isset($cooper_arr[$value->order_id])) {
	                    $value->provider_cooperation = $cooper_arr[$value->order_id];
	                }else {
	                    $value->provider_cooperation = 0;
	                }
                        if(isset($provider_arr[$value->order_id])) {
	                    $value->provider_id = $provider_arr[$value->order_id];
	                }else {
	                    $value->provider_id = 0;
	                }
                        if(isset($returnaddr_arr[$value->order_id])) {
	                    $value->return_address = $returnaddr_arr[$value->order_id];
	                }else {
	                    $value->return_address = '';
	                }
                        $this->load->model('apply_return_model');
                        $has_return_product = $this->apply_return_model->get_apply_return_product_by_order_id($value->order_id);
                        $ari_product_number = 0;
                        foreach ($has_return_product as $product) {
                            $ari_product_number += $product['product_number'];
                        }
                        $value->ari_product_number = $ari_product_number;
                        $value->apply_return=check_order_apply_return($value,true);
	                $rows[$key] = $value;
	            }
	        }

	    }
	    return $arr = array('list' => $rows, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	}

	/**
	* 可能感兴趣的商品
	*/
	public function link_order_goods($user_id)
	{
		$arr_rs=array();
		$sql = "SELECT g.category_id,g.brand_id,g.product_id,g.product_sex FROM ".$this->_db->dbprefix('product_info')." AS g ".
			" , ".$this->_db->dbprefix('order_info')." AS og ".
			" , ".$this->_db->dbprefix('order_product')." AS o ".
			" WHERE g.product_id=og.product_id AND o.order_id=og.order_id AND o.user_id='$user_id' ORDER BY o.order_id DESC LIMIT 10 ";
		$query = $this->_db->query($sql);
		$rs = $query->result();
		if (!empty($rs))
		{
			foreach ($rs as $rows)
			{
				$arr_cat[]=$rows['category_id'];
				$arr_brand[]=$rows['brand_id'];
				$arr_product[]=$rows['product_id'];
				$product_sex=$rows['product_sex'];
			}
		}


		if (count($arr_product))
		{
			$arg=array('category_id'=>$arr_cat,'brand_id'=>$arr_brand,'no_product_id'=>$arr_product,'product_sex'=>$product_sex,'page_size'=>4,'sort'=>3,'name'=>4,'groupby'=>'product_id');
			//$row=$obj_goodsbase->listGoods($arg);
			//$arr_rs=$row['product'];
		}
		$num = 4-count($arr_rs);
		if($num>0){		//如果数量不足
			if(count($arr_rs)>0){
				foreach($arr_rs as $key=>$value){
					$arr_product[]=$value['product_id'];
				}
			}
			$arg=array('no_product_id'=>$arr_product,'is_best'=>1,'product_sex'=>$product_sex,'page_size'=>$num,'sort'=>3,'name'=>$num,'groupby'=>'product_id');
			//$row=$obj_goodsbase->listGoods($arg);
			//if(count($row['product'])>0){
			//	foreach($row['product'] as $key=>$value){
			//		$arr_rs[$key]=$value;
			//	}
			//}
		}
		return $arr_rs;
	}

	/**
	* 订单信息
	*/
	public function order_info($order_id,$order_sn = '')
    {
	    if(!empty($order_id) && empty($order_sn) ){
		$where = 'o.order_id = ?';
		$param = array($order_id);
	    }else{
		$where = 'o.order_sn = ?';
		$param = array($order_sn);
	    }
    	$sql = "SELECT o.*, p.pay_code,p.pay_name,p.pay_logo,p.is_online,s.shipping_code,s.shipping_name,s.shipping_desc,pr.region_name as province_name,
    		cr.region_name as city_name,dr.region_name as district_name
                FROM ".$this->_db->dbprefix('order_info')." AS o
                LEFT JOIN ".$this->_db->dbprefix('payment_info')." AS p ON o.pay_id = p.pay_id
                LEFT JOIN ".$this->_db->dbprefix('shipping_info')." AS s ON o.shipping_id = s.shipping_id
                LEFT JOIN ".$this->_db->dbprefix('region_info')." AS pr ON o.province = pr.region_id
                LEFT JOIN ".$this->_db->dbprefix('region_info')." AS cr ON o.city = cr.region_id
                LEFT JOIN ".$this->_db->dbprefix('region_info')." AS dr ON o.district = dr.region_id
                WHERE ".$where." LIMIT 1";
        $query = $this->_db->query($sql,$param);
        return $query->row();
    }

    public function order_product($order_id)
    {
    	$sql = "SELECT op.*, p.product_name, p.genre_id, p.product_sn,p.provider_productcode, p.unit_name, c.color_name, c.color_sn, s.size_name, s.size_sn, b.brand_id, b.brand_name
                , g.img_url
                FROM ".$this->_db->dbprefix('order_product')." AS op
                LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON op.product_id = p.product_id
                LEFT JOIN ".$this->_db->dbprefix('product_color')." AS c ON c.color_id = op.color_id
                LEFT JOIN ".$this->_db->dbprefix('product_size')." AS s ON s.size_id = op.size_id
                LEFT JOIN ".$this->_db->dbprefix('product_gallery')." AS g ON op.product_id = g.product_id AND op.color_id = g.color_id AND g.image_type = 'default'
                LEFT JOIN ".$this->_db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id
                WHERE op.order_id = ?";
        $query = $this->db->query($sql, array($order_id));
        return $query->result();
    }

    public function order_payment($order_id)
    {
    	$sql = "SELECT op.*, p.pay_name, p.pay_code, p.is_discount
                FROM ".$this->db->dbprefix('order_payment')." AS op
                LEFT JOIN ".$this->_db->dbprefix('payment_info')." AS p ON op.pay_id = p.pay_id
                WHERE op.order_id = ? AND op.is_return = 0 ORDER BY op.payment_id";
        $query = $this->_db->query($sql, array(intval($order_id)));

        return $query->result();
    }

    public function filter_payment($filter)
    {
    	$query = $this->_db->get_where('payment_info',$filter,1);
    	return $query->row();
    }

    public function insert_onlinepay_log($data)
    {
    	$this->_db->insert('onlinepay_log',$data);
    	return $this->_db->insert_id();
    }
	
	public function insert_payment ($data)
	{
		$this->_db->insert('order_payment',$data);
		return $this->_db->insert_id();
	}
	
	public function update ($data,$order_id)
	{
		$this->_db->update('order_info',$data,array('order_id'=>$order_id));
	}
        
        public function update_where ($data,$where)
	{
		$this->_db->update('order_info',$data,$where);
	}
        
        public function invalid($order_id, $order_sn) {
            $this->_db->update('ty_order_info',array("order_status" => 4, 'is_ok' => 1, 'is_ok_admin' => -1, 'is_ok_date' => date('Y-m-d H:i:s')),array('order_id' => $order_id));
            $this->_db->update('ty_transaction_info',array("trans_status" => 5),array('trans_sn' => $order_sn));
        }
        public function filter_routing($filter)
        {
            $query = $this->db->get_where('order_routing',$filter,1);
            return $query->row();
        }
        
        /**
         * 根据订单ID数组列表取订单记录
         * @param type $arr_order_id
         */
        public function order_list_by_ids($arr_order_id, $genre_id=0)
        {
            //$sql = "select * from ty_order_info left join  where order_id ".  db_create_in($arr_order_id);
            $sql = "  SELECT oi.*, p.region_name AS province_name, c.region_name AS city_name, d.region_name AS district_name 
                FROM ty_order_info oi 
                LEFT JOIN ty_region_info p ON oi.province = p.region_id 
                LEFT JOIN ty_region_info c ON oi.city = c.region_id 
                LEFT JOIN ty_region_info d ON oi.district = d.region_id 
                WHERE order_id ".  db_create_in($arr_order_id);;
            if ($genre_id > 0) 
                $sql .= " AND genre_id = ".$genre_id;
            $query = $this->_db->query($sql);
            return $query->result();
        }
        
        /**
         * 根据订单ID数组列表取订单商品记录
         * @param type $arr_order_id
         * @return type
         */
        public function product_list_by_order_ids($arr_order_id)
        {
            $sql = "select op.*, p.product_name, p.brand_name, c.color_name, s.size_name, g.img_url, pp.provider_id, pp.provider_name
                    from ty_order_product as op
                    left join ty_product_info as p on op.product_id = p.product_id
                    left join ty_product_provider as pp on p.provider_id = pp.provider_id
                    left join ty_product_color as c on op.color_id = c.color_id
                    left join ty_product_size as s on op.size_id = s.size_id
                    left join ty_product_gallery as g on op.product_id = g.product_id and op.color_id=g.color_id and g.image_type='default'
                    where op.order_id ".  db_create_in($arr_order_id);
            $query = $this->db->query($sql);
            return $query->result();
        }
        
        /**
         * 根据订单ID数组列表到订单支付记录
         * @param type $arr_order_id
         * @return type
         */
        public function payment_list_by_order_ids($arr_order_id)
        {
            $sql = "select op.*
                    from ty_order_payment as op
                    where op.is_return = 0 and op.order_id ".  db_create_in($arr_order_id);
            $query = $this->db->query($sql);
            return $query->result();
        }
        
        /**
         * 锁定一条支付跟踪记录
         * @param type $track_sn
         * @return type
         */
        public function lock_pay_track($track_sn)
	{
		$sql="SELECT * FROM ".$this->db->dbprefix('order_pay_track')." WHERE track_sn = ? LIMIT 1 FOR UPDATE";
                $query = $this->_db->query($sql,array($track_sn));
                return $query->row();
	}
        
        /**
         * 获取一条支付跟踪记录
         * @param type $track_sn
         * @return type
         */
        public function get_pay_track($track_sn)
	{
		$sql="SELECT * FROM ".$this->db->dbprefix('order_pay_track')." WHERE track_sn = ? LIMIT 1";
                $query = $this->_db->query($sql,array($track_sn));
                return $query->row();
	}
        
        /**
         * 根据跟踪号更新记录
         * @param type $data
         * @param type $track_sn
         */
        public function update_pay_track_by_sn($data, $track_sn){
            $this->db->update('order_pay_track', $data, array('track_sn'=>$track_sn));
        }
        
        /**
         * 创建支付跟踪记录,如果有已存在的记录，可以重用，否则重新生成
         * @param array $order_ids
         * @param type $pay_price
         */
        public function create_pay_track($order_ids, $pay_id, $bank_code, $pay_price, $user_id)
        {
            sort($order_ids);
            $key = implode('-', $order_ids);
            $query = $this->db->get_where('order_pay_track', array('order_ids'=>implode('-', $order_ids), 'pay_id'=>$pay_id, 'bank_code'=>$bank_code, 'pay_price'=>$pay_price, 'pay_status'=>0, 'user_id'=>$user_id));
            $row = $query->row();
            if($row){
                return $row;
            }
            $data = array(
                'order_ids' => $key,
                'pay_price' => $pay_price,
                'pay_id' =>$pay_id,
                //'bank_code' => $bank_code, //支付调试中遇到此问题，上线前check
                'user_id' => $user_id,
                'add_time' => date('Y-m-d H:i:s'),                
            );
            while(true)
            {
                $data['track_sn'] = get_pay_track_sn();
                $this->db->insert('order_pay_track', $data);
                $track_id = $this->db->insert_id();
                $err_no = $this->db->_error_number();
                if ($err_no == '1062')
                    continue;
                if ($err_no == '0')
                    break;
                return false;
            }
            $result_query = $this->db->get_where('order_pay_track', array('track_id'=>$track_id));
            return $result_query->row();            
        }
        
        /**
         * n天内购买某商品的数量
         * @param type $user_id
         * @param type $product_id
         * @param type $days
         * @return int
         */
        public function get_bought_num($user_id, $product_id, $days) {
            if (empty($user_id)) {
                return 0;
            }
            $days = intval($days);
            if ($days < 1) {
                return 0;
            }
            $sql = "select sum(op.product_num) as num
                        from ty_order_product as op
                        left join ty_order_info as o on op.order_id = o.order_id
                        where op.product_id=? and o.user_id = ? and o.order_status in (0,1)
                        and o.create_date>= date_sub(now(), interval $days DAY)";
            $query = $this->db->query($sql, array($product_id, $user_id));
            $result = $query->row();
            return $result ? $result->num : 0;
        }
        
        public function insert_order_client ($data)
	{
            $this->_db->insert('order_client_info',$data);
            return $this->_db->insert_id();
	}
        
        public function insert_order_advice($data)
	{
            $this->_db->insert('order_advice',$data);
            return $this->_db->insert_id();
	}
        
        public function get_order_advice($order_id)
	{
            $sql="SELECT * FROM ".$this->db->dbprefix('order_advice')." WHERE type_id = 2 AND is_return = 1 AND order_id = '".$order_id."' LIMIT 1";
            $query = $this->_db->query($sql);
            return $query->row();
	}

	//PC端 个人中心订单状态数量
	public function get_user_ordernum($order_status,$user_id){
		if($order_status == 6){
			$where = " AND o.order_status in (0, 1) AND o.order_price + o.shipping_fee > o.paid_price ";
		}elseif($order_status == 7){
        	$where = " AND o.order_status = '1' AND o.shipping_status = '0' AND o.pay_status = '1'";
		}

		$sql = "SELECT o.*,p.pay_code,p.is_online,p.is_online AS online_pay,a.action_note,pr.region_name AS province_name,s.shipping_name FROM ty_order_info AS o 
				LEFT JOIN ty_shipping_info s ON o.shipping_id = s.shipping_id 
				LEFT JOIN ty_payment_info AS p ON o.pay_id = p.pay_id 
  				LEFT JOIN ty_order_action AS a ON o.order_id = a.order_id AND a.is_return = 1 AND a.order_status = 4 
  				LEFT JOIN ty_region_info AS pr ON o.province = pr.region_id 
				WHERE o.user_id = '" . $user_id . "' AND o.order_status != '3' ". $where ." ORDER BY o.create_date DESC ";
		$query = $this->_db->query($sql);
        return $query->result();
	}
}
