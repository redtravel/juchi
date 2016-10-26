<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Exchange_location_model extends CI_Model {
    
    /*
     * 整储调拨，将一个储位的所有商品移动到另一储位。
     */
    public function get_products_by_location($location) {
        $select = " SELECT pi.shop_price, "
                 ." ps.product_id, ps.color_id, ps.size_id, ps.provider_barcode, "
                 ." t.batch_id, SUM(t.product_number) AS product_number ";
        
        $from = " FROM ".$this->db->dbprefix('transaction_info') . " AS t"
               ." LEFT JOIN ".$this->db->dbprefix('product_info') . " AS pi ON t.product_id = pi.product_id "
               ." LEFT JOIN ".$this->db->dbprefix('product_sub') . " AS ps ON t.product_id = ps.product_id AND t.color_id = ps.color_id AND t.size_id = ps.size_id "
               ." LEFT JOIN ".$this->db->dbprefix('location_info') . " AS l ON l.location_id = t.location_id ";
               
        $where = " WHERE t.trans_status IN (2,4) AND l.location_id = ". $location->location_id;
        
        $group_by = " GROUP BY t.size_id, t.color_id, t.product_id ";
        
        $having = " HAVING product_number > 0 ";
        
        // query
        $sql = $select . $from . $where . $group_by . $having;
	$query = $this->db->query($sql);
        
        $list = $query->result_array();
        $query->free_result();
        
        return $list;
    }
    
    public function check_has_in_out_operation($location_id) {
        $sql = " SELECT * FROM " .$this->db->dbprefix('transaction_info') 
              ." WHERE location_id = " . $location_id
              ." AND trans_status in (1,3)"
              ." LIMIT 1";
        
	$query = $this->db->query($sql);
        $row = $query->row();
        $query->free_result();
        
        return $row;
    }
    
    public function exchange($from_location, $to_location, $products, $admin_id) {
        $depot_id = $from_location->depot_id;
        $exchange_number = $this->do_count_product_number($products);
        
        // 开启事务
        $this->db->trans_begin();
        
        // 生成调仓单
        $exchange_id = $this->do_create_exchange_main($exchange_number, $depot_id, $admin_id);
        
        // 批量插入调仓出库明细
        $this->do_batch_insert_exchange_out($products, $exchange_id, $depot_id, $from_location->location_id, $admin_id);
        
        // 批量插入调仓入库明细
        $this->do_batch_insert_exchange_in($products, $exchange_id, $depot_id, $to_location->location_id, $admin_id);
        
        // 批量插入事务表已出记录
        $this->do_batch_insert_trans_out($exchange_id, $admin_id);
        
        // 批量插入事务表已入记录
        $this->do_batch_insert_trans_in($exchange_id, $admin_id);
        
        // 自动审核调仓单
        $this->do_finish_exchange_main($exchange_id, $admin_id);
       
        // 提交事务
        $this->db->trans_commit();
    }
    
    /* ---- private methods ------------------------------------------------- */
    private function do_count_product_number($products) {
        $number = 0;
        foreach ($products as $product) {
            $number += $product['product_number'];
        }
        return $number;
    }
    
    private function do_create_exchange_main($exchange_product_number, $depot_id, $admin_id) {
        $exchange = array();
        $exchange['exchange_code'] = $this->do_generate_exchange_code();
        $exchange['exchange_reason'] = '仓内储位商品移动';
        $exchange['exchange_out_number'] = $exchange_product_number;
        $exchange['exchange_in_number'] = $exchange_product_number;
        $exchange['source_depot_id'] = $depot_id;
        $exchange['dest_depot_id'] = $depot_id;
        $exchange['lock_admin'] = $admin_id;
        $exchange['lock_date'] = date('Y-m-d H:i:s');
        $exchange['out_type'] = 2;
        
        $this->db->insert('exchange_main', $exchange);
        $exchange_id = $this->db->insert_id();
        
        return $exchange_id;
    }
    
    private function do_batch_insert_exchange_out($products, $exchange_id, $depot_id, $from_location_id, $admin_id) {
        $now_date = date('Y-m-d H:i:s');
        
        $exchange_sku_out_ary = array();
        foreach ($products as $value) {
            $exchange_sku_out = array();
            $exchange_sku_out['exchange_id'] = $exchange_id;
            $exchange_sku_out['product_id'] = $value['product_id'];
            $exchange_sku_out['color_id'] = $value['color_id'];
            $exchange_sku_out['size_id'] = $value['size_id'];
            $exchange_sku_out['shop_price'] = $value['shop_price'];
            $exchange_sku_out['batch_id'] = $value['batch_id'];
            $exchange_sku_out['product_number'] = $value['product_number'];
            $exchange_sku_out['create_admin'] = $admin_id;
            $exchange_sku_out['create_date'] = $now_date;
            $exchange_sku_out['source_depot_id'] = $depot_id;
            $exchange_sku_out['source_location_id'] = $from_location_id;
            
            $exchange_sku_out_ary[] = $exchange_sku_out;
        }
        
        $this->db->insert_batch('exchange_out', $exchange_sku_out_ary);
    }
    
    private function do_batch_insert_exchange_in($products, $exchange_id, $depot_id, $to_location_id, $admin_id) {
        $now_date = date('Y-m-d H:i:s');
        
        $exchange_sku_in_ary = array();
        foreach ($products as $value) {
            $exchange_sku_in = array();
            $exchange_sku_in['exchange_id'] = $exchange_id;
            $exchange_sku_in['product_id'] = $value['product_id'];
            $exchange_sku_in['color_id'] = $value['color_id'];
            $exchange_sku_in['size_id'] = $value['size_id'];
            $exchange_sku_in['shop_price'] = $value['shop_price'];
            $exchange_sku_in['batch_id'] = $value['batch_id'];
            $exchange_sku_in['product_number'] = $value['product_number'];
            $exchange_sku_in['create_admin'] = $admin_id;
            $exchange_sku_in['create_date'] = $now_date;
            $exchange_sku_in['dest_depot_id'] = $depot_id;
            $exchange_sku_in['dest_location_id'] = $to_location_id;
            
            $exchange_sku_in_ary[] = $exchange_sku_in;
        }
        
        $this->db->insert_batch('exchange_in', $exchange_sku_in_ary);
    }
    
    private function do_batch_insert_trans_in($exchange_id, $admin_id) {
        $sql = " INSERT INTO ". $this->db->dbprefix('transaction_info') 
              ." (trans_type, trans_status, trans_sn, product_id, color_id, size_id, 
                  product_number, depot_id, location_id, create_admin, create_date, update_admin, update_date, 
                  trans_direction, sub_id, related_id, batch_id, shop_price, 
                  consign_price, cost_price, consign_rate, product_cess,expire_date,production_batch) "
              ." SELECT "
              .TRANS_TYPE_PACKET_EXCHANGE." AS trans_type, ".TRANS_STAT_IN." AS trans_status, em.exchange_code AS trans_sn, e.product_id, e.color_id, e.size_id, 
                  e.product_number, e.dest_depot_id AS depot_id, e.dest_location_id AS location_id, 
                  ". $admin_id ." AS create_admin, '". date('Y-m-d H:i:s') ."' AS create_date, 
                  ". $admin_id ." AS update_admin, '". date('Y-m-d H:i:s') ."' AS update_date, 
                  1 AS trans_direction, e.exchange_leaf_id AS sub_id, 0 AS related_id, e.batch_id, pi.shop_price, 
                  pc.consign_price, pc.cost_price, pc.consign_rate, pc.product_cess,e.expire_date,e.production_batch "
              ." FROM " . $this->db->dbprefix('exchange_in') . " AS e "
              ." LEFT JOIN " . $this->db->dbprefix('exchange_main') . " AS em ON em.exchange_id = e.exchange_id "
              ." LEFT JOIN " . $this->db->dbprefix('product_info') . " AS pi ON pi.product_id = e.product_id "
              ." LEFT JOIN " . $this->db->dbprefix('product_sub') . " AS ps ON ps.product_id = e.product_id AND ps.color_id = e.color_id AND ps.size_id = e.size_id "
              ." LEFT JOIN " . $this->db->dbprefix('product_cost') . " AS pc ON pc.product_id = e.product_id AND pc.batch_id = e.batch_id "
              ." WHERE e.exchange_id = " . $exchange_id;
        
	$this->db->query($sql);
    }
    
    private function do_batch_insert_trans_out($exchange_id, $admin_id) {
        $sql = " INSERT INTO ". $this->db->dbprefix('transaction_info') 
              ." (trans_type, trans_status, trans_sn, product_id, color_id, size_id, 
                  product_number, depot_id, location_id, create_admin, create_date, update_admin, update_date, 
                  trans_direction, sub_id, related_id, batch_id, shop_price, 
                  consign_price, cost_price, consign_rate, product_cess,expire_date,production_batch) "
              ." SELECT "
              .TRANS_TYPE_PACKET_EXCHANGE." AS trans_type, ".TRANS_STAT_OUT." AS trans_status, em.exchange_code AS trans_sn, e.product_id, e.color_id, e.size_id, 
                  -1 * e.product_number AS product_number, e.source_depot_id AS depot_id, e.source_location_id AS location_id, 
                  ". $admin_id ." AS create_admin, '". date('Y-m-d H:i:s') ."' AS create_date, 
                  ". $admin_id ." AS update_admin, '". date('Y-m-d H:i:s') ."' AS update_date, 
                  1 AS trans_direction, e.exchange_sub_id AS sub_id, 0 AS related_id, e.batch_id, pi.shop_price, 
                  pc.consign_price, pc.cost_price, pc.consign_rate, pc.product_cess,e.expire_date,e.production_batch "
              ." FROM " . $this->db->dbprefix('exchange_out') . " AS e "
              ." LEFT JOIN " . $this->db->dbprefix('exchange_main') . " AS em ON em.exchange_id = e.exchange_id "
              ." LEFT JOIN " . $this->db->dbprefix('product_info') . " AS pi ON pi.product_id = e.product_id "
              ." LEFT JOIN " . $this->db->dbprefix('product_sub') . " AS ps ON ps.product_id = e.product_id AND ps.color_id = e.color_id AND ps.size_id = e.size_id "
              ." LEFT JOIN " . $this->db->dbprefix('product_cost') . " AS pc ON pc.product_id = e.product_id AND pc.batch_id = e.batch_id "
              ." WHERE e.exchange_id = " . $exchange_id;
        
	$this->db->query($sql);
    }
    
    private function do_finish_exchange_main($exchange_id, $admin_id) {
        $now_date = date('Y-m-d H:i:s');
        
        $update = array();
        $update['out_admin'] = $admin_id;
        $update['out_date'] = $now_date;
        $update['in_admin'] = $admin_id;
        $update['in_date'] = $now_date;
        $update['out_audit_admin'] = $admin_id;
        $update['out_audit_date'] = $now_date;
        $update['in_audit_admin'] = $admin_id;
        $update['in_audit_date'] = $now_date;
        $update['lock_admin'] = 0;
        $update['lock_date'] = date('Y-m-d H:i:s');
        
        $this->db->update('exchange_main', $update, array('exchange_id' => $exchange_id));
    }
    
    private function do_generate_exchange_code() {
        $this->load->model('exchange_model');
        $exchange_code = $this->exchange_model->get_exchange_code();
        while ($this->exchange_model->filter_exchange(array('exchange_code'=>$exchange_code))) {
            $exchange_code = $this->exchange_model->get_exchange_code();
        }
        return $exchange_code;
    }
   
}
?>
