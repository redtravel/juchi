<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class CTB_model extends CI_Model {
    
    public function query_product_batchs($provider_barcode) {
        // 这里如果商品资料存在，批次不存在，需要做排除，故使用inner join
        $sql = " SELECT "
              ." ps.sub_id, ps.product_id, ps.color_id, ps.size_id, t.batch_id, ps.provider_barcode, "
              //." pc.consign_price, pc.cost_price, pc.consign_rate, pc.product_cess, "
              ." p.product_name, p.product_sn, p.provider_productcode, p.shop_price, c.color_name, s.size_name, "
              ." b.brand_name, d.depot_name, d.cooperation_id, pb.batch_code, pb.is_reckoned, pb.batch_type "
              ." FROM ".$this->db->dbprefix('product_sub')." AS ps "
              ." INNER JOIN ".$this->db->dbprefix('transaction_info')." AS t ON t.product_id = ps.product_id AND t.color_id = ps.color_id AND t.size_id = ps.size_id "
              ." INNER JOIN ".$this->db->dbprefix('product_info')." AS p ON p.product_id = ps.product_id "
              ." INNER JOIN ".$this->db->dbprefix('product_color')." AS c ON c.color_id = ps.color_id "
              ." INNER JOIN ".$this->db->dbprefix('product_size')." AS s ON s.size_id = ps.size_id "
              ." INNER JOIN ".$this->db->dbprefix('product_brand')." AS b ON b.brand_id = p.brand_id "
              ." INNER JOIN ".$this->db->dbprefix('depot_info')." AS d ON d.depot_id = t.depot_id "
              ." INNER JOIN ".$this->db->dbprefix('purchase_batch')." AS pb ON pb.batch_id = t.batch_id "
              //." INNER JOIN ".$this->db->dbprefix('product_cost')." AS pc ON pc.product_id = ps.product_id AND pc.batch_id = t.batch_id "
              ." WHERE ps.provider_barcode = '".$provider_barcode."'"
              ." GROUP BY t.batch_id ";
        
        $query = $this->db->query($sql);
        $list = $query->result();
        $query->free_result();
        
        return $list;
    }
    
    public function ctb($product_ary, $admin_id) {
        // 商品归集
        $product_ary = $this->product_imputation($product_ary);
        $ctb_product_ary = $product_ary['ctb_product_ary'];
        $update_product_ary = $product_ary['update_product_ary'];
        if (count($ctb_product_ary) <= 0 && count($update_product_ary) <= 0) {
            sys_msg('没有需要代转买的商品！', 1);
        }
        
        // 创建代转买入库单
        $date = date('Y-m-d H:i:s');
        $depot_in_info = $this->do_create_ctb_depot_in($admin_id, $date);
        
        // 批量插入已存在代转买批次的入库商品明细
        if (count($update_product_ary) > 0) {
            $this->do_batch_insert_depot_in($update_product_ary, $depot_in_info->depot_in_id, $admin_id, $date);
        }
        
        if (count($ctb_product_ary) > 0) {
            // 创建新批次
            $new_batch_id = $this->do_create_purchase_batch($admin_id, $date);

            // 批量插入需要做代转买的入库商品明细
            $this->do_batch_insert_depot_in_ctb($ctb_product_ary, $depot_in_info->depot_in_id, $new_batch_id, $admin_id, $date);
        }
        
        // 根据入库单生成入库待入记录
        $this->do_batch_insert_trans_in($depot_in_info, $admin_id, $date);
        
        // 统计入库单主信息
        $this->do_count_depot_in_main($depot_in_info->depot_in_id);
        
        // 根据入库单自动财审
        $this->load->model('depotio_model');
        $this->depotio_model->check_in($depot_in_info, $admin_id);
    }
    
    /* ---- private methods ------------------------------------------------- */
    private function product_imputation($product_ary) {
        $ctb_product_ary = array();
        $update_product_ary = array();
        foreach ($product_ary as $product) {
            $in = false;
            foreach ($ctb_product_ary as $index => $value) {
                if ($product['sub_id'] == $value['sub_id']) {
                    $in = true;
                    $value['product_number'] += 1;
                    $ctb_product_ary[$index] = $value;
                }
            }
            
            foreach ($update_product_ary as $index => $value) {
                if ($product['sub_id'] == $value['sub_id']) {
                    $in = true;
                    $value['product_number'] += 1;
                    $update_product_ary[$index] = $value;
                }
            }
            
            if ($in == false) {
                $product['product_number'] = 1;
                if ($product['batch_type'] == 1) { // 代转买批次更新
                    $update_product_ary[] = $product;
                } else {
                    $ctb_product_ary[] = $product;
                }
            }
        }
        
        return array("ctb_product_ary" => $ctb_product_ary, "update_product_ary" => $update_product_ary);
    }
    
    /*
     * 创建一个新的批次。
     */
    private function do_create_purchase_batch($admin_id, $date) {
        $this->load->model('purchase_batch_model');
        
        $purchase_batch = array();
        $purchase_batch['batch_code'] = $this->purchase_batch_model->gen_purchase_batch_code();
        $purchase_batch['provider_id'] = SYS_CTB_PROVIDER_ID; //系统供应商
        $purchase_batch['batch_status'] = 1; //批次状态：1-打开
        $purchase_batch['batch_type'] = 1; //批次类型：1-代转买
        $purchase_batch['plan_num'] = 0; //后续更新
        $purchase_batch['batch_name'] = '代转买新建批次';
        $purchase_batch['plan_arrive_date'] = $date;
        $purchase_batch['create_admin'] = $admin_id;
        $purchase_batch['create_date'] = $date;
        $purchase_batch['update_admin'] = $admin_id;
        $purchase_batch['update_time'] = $date;
        $purchase_batch['related_id'] = 0;
        $purchase_batch['is_reckoned'] = 0; //未结算
        
        return $this->purchase_batch_model->insert($purchase_batch);
    }
    
    /*
     * 创建代转买入库单。
     */
    private function do_create_ctb_depot_in($admin_id, $date) {
        $depot_in_code = $this->do_get_depot_in_code();
        
        $data = array();
        $data['depot_in_type'] = CTB_DEPOT_IN_TYPE;
        $data['depot_depot_id'] = CTB_RETURN_DEPOT_ID;
        $data['depot_in_reason'] = '代转买入库';
        $data['depot_in_code'] = $depot_in_code;
        $data['create_date'] = $date;
        $data['create_admin'] = $admin_id;
        $data['lock_date'] = $date;
        $data['lock_admin'] = -1;
        $data['depot_in_date'] = $date;
        
        $this->load->model('depotio_model');
        $this->depotio_model->insert_depot_in($data);
        $depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_code' => $depot_in_code));
        
        return $depot_in_info;
    }
    
    /*
     * 生成一个唯一的入库单编号。
     */
    private function do_get_depot_in_code() {
        $this->load->model('depotio_model');
        $depot_in_code = $this->depotio_model->get_depot_in_code();
        
        while ($this->depotio_model->filter_depot_in(array('depot_in_code'=>$depot_in_code))) {
            set_time_limit(1);
            $depot_in_code = $this->depotio_model->get_depot_in_code();
        }
        
        return $depot_in_code;
    }
    
    /*
     * 批量插入已存在代转买批次的入库商品明细
     */
    private function do_batch_insert_depot_in($product_ary, $depot_in_id, $admin_id, $date) {
        $depot_in_products = array();
        foreach ($product_ary as $product_sku) {
            // 入库单商品详情
            $data_in = array();
            $data_in['depot_in_id'] = $depot_in_id;
            $data_in['depot_id'] = CTB_RETURN_DEPOT_ID;
            $data_in['location_id'] = CTB_RETURN_DEPOT_LOCATION_ID;
            $data_in['batch_id'] = $product_sku['batch_id'];
            $data_in['product_id'] = $product_sku['product_id'];
            $data_in['color_id'] = $product_sku['color_id'];
            $data_in['size_id'] = $product_sku['size_id'];
            $data_in['product_name'] = $product_sku['product_name'];
            $data_in['shop_price'] = $product_sku['shop_price'];
            $data_in['product_number'] = $product_sku['product_number'];
            $data_in['product_amount'] = $product_sku['product_number'] * $product_sku['shop_price'];
            $data_in['create_admin'] = $admin_id;
            $data_in['create_date'] = $date;
            
            $depot_in_products[] = $data_in;
        }
        
        $this->db->insert_batch('depot_in_sub', $depot_in_products);
    }
 
    /*
     * 批量插入需要做代转买的入库商品明细
     */
    private function do_batch_insert_depot_in_ctb($product_ary, $depot_in_id, $new_batch_id, $admin_id, $date) {
        $depot_in_products = array();
        
        $this->load->helper('product');
        foreach ($product_ary as $product_sku) {
            // 重建商品信息：包括info,gallery,sub,cost信息；
            // 新商品related_id关联老商品，与老商品条码相同，后台分类相同...
            $new_product = create_product_with_product($product_sku['product_id'], SYS_CTB_PROVIDER_ID, false);
            $this->do_create_product_cost($product_sku['product_id'], $product_sku['batch_id'], $new_product['product_id'], $new_batch_id, $date);
            
            // 入库单商品详情
            $data_in = array();
            $data_in['depot_in_id'] = $depot_in_id;
            $data_in['depot_id'] = CTB_RETURN_DEPOT_ID;
            $data_in['location_id'] = CTB_RETURN_DEPOT_LOCATION_ID;
            $data_in['batch_id'] = $new_batch_id;
            $data_in['product_id'] = $new_product['product_id'];
            $data_in['color_id'] = $product_sku['color_id'];
            $data_in['size_id'] = $product_sku['size_id'];
            $data_in['product_name'] = $product_sku['product_name'];
            $data_in['shop_price'] = $product_sku['shop_price'];
            $data_in['product_number'] = $product_sku['product_number'];
            $data_in['product_amount'] = $product_sku['product_number'] * $product_sku['shop_price'];
            $data_in['create_admin'] = $admin_id;
            $data_in['create_date'] = $date;
            
            $depot_in_products[] = $data_in;
        }
        
        $this->db->insert_batch('depot_in_sub', $depot_in_products);
    }
    
    /*
     * 根据老商品的cost信息，创建一个新商品的cost信息，并关联指定批次。
     */
    private function do_create_product_cost($old_product_id, $old_batch_id, $new_product_id, $new_batch_id, $date) {
        $cost_filter = array(
            'product_id'=>$old_product_id, 'batch_id'=>$old_batch_id
        );
        
        $this->load->model('product_model');
        $product_cost = $this->product_model->filter_cost($cost_filter);

        // 插入商品cost信息
        $data = array();
        $data['batch_id'] = $new_batch_id;
        $data['product_id'] = $new_product_id;
        $data['provider_id'] = SYS_CTB_PROVIDER_ID;
        $data['consign_price'] = $product_cost->consign_price;
        $data['cost_price'] = $product_cost->cost_price;
        $data['consign_rate'] = $product_cost->consign_rate;
        if (!empty($product_cost->product_cess)) {
            $data['product_cess'] = $product_cost->product_cess;
        }
        $data['create_admin'] = 1;
        $data['create_date'] = $date;
        $this->product_model->insert_product_cost($data);
    }
    
    /*
     * 根据入库单生成入库待入记录
     */
    private function do_batch_insert_trans_in($depot_in_info, $admin_id, $date) {
        $sql = " INSERT INTO ".$this->db->dbprefix('transaction_info')
              ." (
                  trans_type, trans_status, trans_sn, sub_id, product_id, color_id, size_id, 
                  product_number, depot_id, location_id, batch_id, create_admin, create_date, 
                  shop_price, trans_direction, consign_price, cost_price, consign_rate, product_cess "
              ." ) "
              ." SELECT "
              .TRANS_TYPE_DIRECT_IN." AS trans_type, ".TRANS_STAT_AWAIT_IN." AS trans_status, '".$depot_in_info->depot_in_code."' AS trans_sn, "
              ." d.depot_in_sub_id AS sub_id, d.product_id, d.color_id, d.size_id, d.product_number, d.depot_id, d.location_id, d.batch_id, "
              .$admin_id." AS create_admin, '".$date."' AS create_date, d.shop_price, 1 AS trans_direction, "
              ." c.consign_price, c.cost_price, c.consign_rate, c.product_cess "
              ." FROM ".$this->db->dbprefix('depot_in_sub')." AS d "
              ." LEFT JOIN ".$this->db->dbprefix('product_cost')." AS c ON c.product_id = d.product_id AND c.batch_id = d.batch_id "
              ." WHERE d.depot_in_id = ".$depot_in_info->depot_in_id;
        
        $this->db->query($sql);
    }
    
    /*
     * 统计入库单主信息
     */
    private function do_count_depot_in_main($depot_in_id) {
        $sql = " UPDATE ".$this->db->dbprefix('depot_in_main')." AS d, "
              ." (SELECT SUM(product_number) as total_number, SUM(product_amount) AS total_amount "
                    ." FROM ".$this->db->dbprefix('depot_in_sub')
                    ." WHERE depot_in_id = ".$depot_in_id
              ." ) AS c "
              ." SET d.depot_in_number = c.total_number, d.depot_in_finished_number = c.total_number, d.depot_in_amount = c.total_amount "
              ." WHERE d.depot_in_id = ".$depot_in_id;
        
        $this->db->query($sql);
    }
    
}
?>
