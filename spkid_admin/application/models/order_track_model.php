<?php

class Order_track_model extends CI_Model {
    
    public function filter($filter) {
        $query = $this->db->get_where('tmall_order_track', $filter, 1);
        return $query->row();
    }
    
    public function insert($data) {
        $this->db->insert('tmall_order_track', $data);
        return $this->db->insert_id();
    }
    
    public function update($data, $track_id) {
        $this->db->update('tmall_order_track', $data, array('track_id' => $track_id));
    }
    
    public function query_list($filter) {
        $from = " FROM ty_order_info AS o 
                  LEFT JOIN ty_tmall_order_track AS t ON t.order_sn = o.order_sn 
                  LEFT JOIN ty_order_product op ON op.order_id = o.order_id 
                  LEFT JOIN ty_product_info i ON i.product_id = op.product_id 
                  LEFT JOIN ty_product_provider pp ON pp.provider_id = i.provider_id ";
        $where = " WHERE o.order_status <> 4 AND pp.provider_cooperation = 3 ";
        $groupby = " GROUP BY o.order_id ";
                
        if (!empty($filter['order_sn'])) {
            $where .= " AND o.order_sn LIKE '%" . $filter['order_sn'] . "%'";
        }
        if (!empty($filter['order_status']) && $filter['order_status'] >= 0) {
            $where .= " AND o.order_status = " . $filter['order_status'];
        }
        if (!empty($filter['track_order_sn'])) {
            $where .= " AND t.track_order_sn LIKE '%" . $filter['track_order_sn'] . "%'";
        }
        if (!empty($filter['track_shipping_sn'])) {
            $where .= " AND t.track_shipping_sn LIKE '%" . $filter['track_shipping_sn'] . "%'";
        }
        $where .= $filter['searchType'];
        
        $filter['sort_by'] = empty($filter['sort_by']) ? 'o.create_date' : trim($filter['sort_by']);
        $filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);
        
        // count
        $sql = "SELECT COUNT(1) AS ct FROM (SELECT 1 " . $from . $where . $groupby . ") t";
        $query = $this->db->query($sql);
        $row = $query->row();
        $query->free_result();
        
        $filter['record_count'] = (int) $row->ct;
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0) {
            return array('list' => array(), 'filter' => $filter);
        }
        
        // query
        $sql = "SELECT o.order_id, o.order_sn, o.order_status, o.create_date, "
            . " t.track_order_sn, t.track_shipping_sn, t.track_create_time ". $from . $where . $groupby  
            . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
            . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
        
	$query = $this->db->query($sql);
        
        $list = $query->result();
        $query->free_result();
        return array('list' => $list, 'filter' => $filter);
    }
    
    public function get_cost($product_id) {
        $sql = "SELECT pc.* FROM ty_product_cost pc 
                LEFT JOIN ty_purchase_batch pb ON pb.batch_id = pc.batch_id 
                WHERE pc.product_id = " .$product_id .
               " AND pb.is_reckoned = 0 LIMIT 1";
        return $this->db->query($sql)->row();
    }
    
    public function insert_trans($filter) {
        $this->db->insert('transaction_info',$filter);
        return $this->db->insert_id();
    }
    
    public function update_wait_num($product_id, $color_id, $size_id, $num) {
        $sql = "UPDATE ty_product_sub SET wait_num = wait_num - ".$num."
                WHERE product_id = ".$product_id ." AND color_id = ".$color_id." AND size_id = ".$size_id;
        $this->db->query($sql);
    }
    
    function get_order_info($order_ids) {
    	if (empty($order_ids) || count($order_ids) <= 0) return false;
    	$result = array();
    	$ids = implode(",",$order_ids);
    	$sql = "SELECT oi.`order_id`,si.`shipping_name`,oi.`order_sn`,oi.`invoice_no`,oi.`invoice_title`,
				ui.`user_name`,ui.`email`,oi.`consignee`,oi.`tel`,oi.`mobile`,oi.`zipcode`,oi.`order_price`,oi.`product_num`,
				ri1.`region_name` AS country_name,ri2.`region_name` AS province_name,ri3.`region_name` AS city_name,ri4.`region_name` AS district_name,oi.`address`,
				oi.`paid_price`,(oi.`order_price`+oi.`shipping_fee`-oi.`paid_price`) AS unpay_price 
				FROM ".$this->db_r->dbprefix('order_info')." AS oi 
				LEFT JOIN ".$this->db_r->dbprefix('shipping_info')." AS si ON oi.`shipping_id` = si.`shipping_id` 
				LEFT JOIN ".$this->db_r->dbprefix('user_info')." AS ui ON oi.`user_id` = ui.`user_id` 
				LEFT JOIN ".$this->db_r->dbprefix('region_info')." AS ri1 ON oi.`country` = ri1.`region_id` 
				LEFT JOIN ".$this->db_r->dbprefix('region_info')." AS ri2 ON oi.`province` = ri2.`region_id` 
				LEFT JOIN ".$this->db_r->dbprefix('region_info')." AS ri3 ON oi.`city` = ri3.`region_id` 
				LEFT JOIN ".$this->db_r->dbprefix('region_info')." AS ri4 ON oi.`district` = ri4.`region_id` 
				WHERE oi.`order_id` IN (".$ids.") ";
    	$query = $this->db_r->query($sql);
        $order_info = $query->result();
        foreach ($order_info as $order) {
//        	$query = $this->db_r->get_where('pick_sub', array('rel_no'=>$order->order_sn), 1);
//			$pick = $query->row();
//			$order->pick_cell = $pick->pick_cell;
                $sql = "SELECT CONCAT(tpi.`product_sn`,' ',pc.`color_sn`,' ',pz.`size_sn`) AS sku,
                        ps.`provider_barcode`,pb.`brand_name`,tpi.`product_name`,pc.`color_name`,
                        pz.`size_name`,tpi.`unit_name`,op.`product_price`,op.`product_num` as product_num,
                        (op.`product_price` * op.`product_num`) AS total_price
                        FROM ".$this->db_r->dbprefix('order_product')." AS op 
                        LEFT JOIN ".$this->db_r->dbprefix('product_info')." AS tpi ON op.`product_id` = tpi.`product_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('product_brand')." AS pb ON tpi.`brand_id` = pb.`brand_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('product_color')." AS pc ON op.`color_id` = pc.`color_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('product_size')." AS pz ON op.`size_id` = pz.`size_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('product_sub')." AS ps ON op.`product_id` = ps.`product_id` AND op.`color_id` = ps.`color_id` AND op.`size_id` = ps.`size_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('order_info')." AS oi ON op.`order_id` = oi.`order_id` 
                        WHERE op.`order_id` = '".$order->order_id."' ;";
        	
        	$query = $this->db_r->query($sql);
        	$product_info = $query->result();
        	$order->product_list = $product_info;
        	$result[$order->order_id] = $order;
        }
        return $result;
    }
    
    public function insert_depot_in_single ($sub_id,$product_number,$depot_in_id,$depot_id,$location_id,$admin_id,$batch_id)
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
                    return -1;
            } else {
                    $sql = "INSERT INTO ".$this->db->dbprefix('depot_in_sub')." (depot_in_id,product_id,product_name,color_id,size_id,depot_id,location_id,shop_price," .
                                    "product_number,product_amount,create_admin,create_date,batch_id) " .
                                    "SELECT '".$depot_in_id."',a.product_id,b.product_name,a.color_id,a.size_id,'".$depot_id."','".$location_id."',b.shop_price,".
                                    "'".$product_number."',b.shop_price*".$product_number.",'".$admin_id."','".date('Y-m-d H:i:s')."',".$batch_id .
                                    " FROM " . $this->db->dbprefix('product_sub') ." a " .
                                    "LEFT JOIN " . $this->db->dbprefix('product_info') . " b ON a.product_id = b.product_id " .
                                    "WHERE a.sub_id = '".$sub_id."'";
                    $this->db->query($sql);
                    $sub_id = $this->db->insert_id();
                    $sub_id = $sub_id > 0?$sub_id:0;
                    if ($sub_id == 0) {
                            return false;
                    }

                    $sql = "INSERT INTO ".$this->db->dbprefix('transaction_info')."(trans_type,trans_status,trans_sn,product_id,color_id,size_id,product_number," .
                                    "depot_id,location_id,create_admin,create_date,update_admin,update_date,cancel_admin,cancel_date,trans_direction,sub_id," .
                                    "batch_id,product_cess,cost_price,consign_price,consign_rate,shop_price,expire_date,production_batch) ".
                                    " SELECT ".TRANS_TYPE_DIRECT_IN.",".TRANS_STAT_IN.",b.depot_in_code,a.product_id,a.color_id,size_id,a.product_number,".
                                    "a.depot_id,a.location_id,a.create_admin,a.create_date,0,'0000-00-00',0,'0000-00-00',1,a.depot_in_sub_id," .
                                    "a.batch_id,c.product_cess,c.cost_price,c.consign_price,c.consign_rate,d.shop_price,a.expire_date,a.production_batch" .
                                    " FROM ".$this->db->dbprefix('depot_in_sub')." a" .
                                    " LEFT JOIN ".$this->db->dbprefix('depot_in_main')." b ON b.depot_in_id = a.depot_in_id " .
                                    " LEFT JOIN ".$this->db->dbprefix('product_cost')." c ON c.product_id = a.product_id AND c.batch_id = a.batch_id " .
                                    " LEFT JOIN ".$this->db->dbprefix('product_info')." d ON d.product_id = a.product_id" .
                                    " WHERE a.depot_in_sub_id = '".$sub_id."' ";
                    $this->db->query($sql);
                    if (!$this->db->insert_id()) {
                            return false;
                    }
                    return $sub_id;
            }
    }
}

?>
