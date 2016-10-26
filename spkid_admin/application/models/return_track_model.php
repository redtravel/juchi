<?php

class Return_track_model extends CI_Model {
    
    public function filter($filter) {
        $query = $this->db->get_where('tmall_return_track', $filter, 1);
        return $query->row();
    }
    
    public function insert($data) {
        $this->db->insert('tmall_return_track', $data);
        return $this->db->insert_id();
    }
    
    public function update($data, $track_id) {
        $this->db->update('tmall_return_track', $data, array('track_id' => $track_id));
    }
    
    public function query_list($filter) {
        $from = " FROM ty_apply_return_info AS a 
                  LEFT JOIN ty_order_info o ON o.order_id = a.order_id 
                  LEFT JOIN ty_apply_return_product arp ON arp.apply_id = a.apply_id 
                  LEFT JOIN ty_product_info i ON i.product_id = arp.product_id 
                  LEFT JOIN ty_product_provider pp ON pp.provider_id = i.provider_id 
                  
                  LEFT JOIN ty_order_return_info r ON r.apply_id = a.apply_id 
                  LEFT JOIN ty_tmall_order_track s ON s.order_sn = o.order_sn 
                  LEFT JOIN ty_tmall_return_track AS t ON t.apply_id = a.apply_id ";
        $where = " WHERE a.apply_status <> 3 AND pp.provider_cooperation = 3 ";
        $groupby = " GROUP BY a.apply_id";
        
        if (!empty($filter['apply_id'])) {
            $where .= " AND a.apply_id LIKE '%" . $filter['apply_id'] . "%'";
        }
        if (!empty($filter['order_sn'])) {
            $where .= " AND o.order_sn LIKE '%" . $filter['order_sn'] . "%'";
        }
        if (!empty($filter['track_return_sn'])) {
            $where .= " AND t.track_return_sn LIKE '%" . $filter['track_return_sn'] . "%'";
        }
        $where .= $filter['searchType'];
        
        $filter['sort_by'] = empty($filter['sort_by']) ? 'a.apply_time' : trim($filter['sort_by']);
        $filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);
        
        // count
        $sql = "SELECT COUNT(a.apply_id) AS ct " . $from . $where . $groupby;
        $query = $this->db->query($sql);
        $row = $query->row();
        $query->free_result();
        
        $filter['record_count'] = (int) $row->ct;
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0) {
            return array('list' => array(), 'filter' => $filter);
        }
        
        // query
        $sql = "SELECT a.apply_id, a.invoice_no, a.apply_time, o.order_id, o.order_sn, o.order_status, "
            . " r.return_sn, s.track_order_sn, t.track_return_sn, t.track_shipping_sn, t.track_create_time ". $from . $where . $groupby
            . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
            . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
	$query = $this->db->query($sql);
        
        $list = $query->result();
        $query->free_result();
        return array('list' => $list, 'filter' => $filter);
    }
    
    public function insert_return_products($apply_info, $return_info) {
        $return_id = $return_info['return_id'];
        
        $sql = "INSERT INTO ty_order_return_product(
                    return_id, product_id, product_num, max_number, market_price, shop_price, product_price, 
                    color_id, size_id, total_price, cp_id, op_id, package_id, extension_id, consign_num
                ) SELECT ".
                    $return_id ." AS return_id, rp.product_id, rp.product_number AS product_num, op.product_num AS max_number, op.market_price, op. shop_price, op.product_price, 
                    rp.color_id, rp.size_id, rp.product_price*rp.product_number AS total_price, 0 AS cp_id, op.op_id, 0 AS package_id, 0 AS extension_id, op.consign_num AS consign_num
                FROM ty_apply_return_product rp 
                LEFT JOIN ty_order_product op ON op.product_id = rp.product_id 
                WHERE rp.apply_id = ? AND op.order_id = ?";
        
        $this->db->query($sql, array($apply_info['apply_id'], $apply_info['order_id']));
    }
    
    public function update_return_info($return_id) {
        $sql = "UPDATE ty_order_return_info i 
                INNER JOIN (SELECT return_id, SUM(product_num) AS product_num, SUM(product_price) AS product_price
                     FROM ty_order_return_product WHERE return_id = ".$return_id.") p ON p.return_id = i.return_id 
                SET i.product_num = p.product_num, i.return_price = p.product_price, return_status = 1 "; // 待返款
        $this->db->query($sql);
    }
    
    public function insert_return_info_to_transaction($return_info, $admin_id) {
        $sql = "INSERT INTO ty_transaction_info (
                    trans_type, trans_status, trans_sn, product_id, color_id, size_id, product_number,
                    depot_id, location_id, create_admin, create_date, trans_direction, sub_id, related_id,
                    batch_id, shop_price, consign_price, cost_price, consign_rate, product_cess
                ) SELECT 
                    ?, ?, ?, orp.product_id, orp.color_id, orp.size_id, orp.product_num, 
                    ?, ?, ?, NOW(), 1, orp.rp_id, 0, 
                    c.batch_id, p.shop_price, c.consign_price, c.cost_price, c.consign_rate, c.product_cess
                FROM ty_order_return_product orp 
                LEFT JOIN ty_product_cost c ON c.product_id = orp.product_id 
                LEFT JOIN ty_product_info p ON p.product_id = orp.product_id 
                WHERE orp.return_id = ?";
        
        $params = array(
            TRANS_TYPE_RETURN_ORDER, TRANS_STAT_IN, $return_info['return_sn'], 
            DEPOT_ID_TMALL_RETURN, LOCATION_ID_TMALL_RETURN, $admin_id, $return_info['return_id']
        );
        
        $this->db->query($sql, $params);
    }
    
}

?>
