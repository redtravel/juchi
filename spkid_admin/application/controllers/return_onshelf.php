<?php
/*
 * Return_Onshelf 退货上架。
 * @Author  ZhangShixi
 */
class Return_Onshelf extends CI_Controller {
    
    function __construct () {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if(!$this->admin_id) redirect('index/login');
    }
    
    public function index() {
        auth('return_onshelf');
        $this->load->view('depot/return_onshelf/index');
    }
    
    public function select($type = 0) {
        auth('return_onshelf');
        
        // 查询所有仓库列表
        $this->load->model('depot_model');
        if ($type == 0) { // 0-商品移储
            $all_depot = $this->depot_model->all_depot(null);
        } else {
            $all_depot = $this->depot_model->all_depot(array('is_return' => 1));
        }
        $this->load->view('depot/return_onshelf/select', array('all_depot' => $all_depot, 'type' => $type));
    }
    
    public function scan($depot_id, $type = 0) {
        auth('return_onshelf');
        
        // 查询仓库
        $this->load->model('depot_model');
        $depot = $this->depot_model->filter_depot(array('depot_id' => $depot_id));
        if (!$depot) {
            sys_msg('所选仓库不存在！', 1);
        }
        
        $data = array();
        $data['type'] = $type;
        $data['from_depot'] = $depot;
        if ($type != 0) { // 0-商品移储
            // 查询默认原储位
            $from_location_name = $this->do_get_default_from_location_name($depot);
            $data['from_location_name'] = $from_location_name;
        }
        
        $this->load->view('depot/return_onshelf/scan', $data);
    }
    
    public function get_location() {
        $depot_id = $this->input->post('depot_id');
        $location_name = trim($this->input->post('location_name'));
        $this->load->model('location_model');
        $location = $this->location_model->get_location(array('depot_id' => $depot_id, 'location_name' => $location_name));
        if (!$location) {
            sys_msg('储位['.$location_name.']不存在！', 1);
        } else {
            sys_msg('success', 0);
        }
    }

    public function get_products() {
        $location_name = trim($this->input->post('location_name'));
        $this->load->model('location_model');
        $location = $this->location_model->get_location(array('location_name' => $location_name));
        if (!$location) {
            sys_msg('原储位不存在！', 1);
        }
        
        $provider_barcode = trim($this->input->post('provider_barcode'));
        
        $this->load->model('depot_model');
        $data = $this->depot_model->barcode_scan(array('location_id' => $location->location_id, 'provider_barcode' => $provider_barcode));
        
        $products = $data['list'];
        if (count($products) <= 0) {
            sys_msg('商品不存在！', 1);
        }
        
        $results = array();
        foreach ($products as $product) {
            if (($product->num_shiji + $product->num_daicu) <= 0) {
                continue;
            } else {
                $results[] = $product;
            }
        }
        
        if (count($results) <= 0) {
            sys_msg('商品可出库数为0，不能扫描！', 1);
        }
        
        echo json_encode(array('list' => $results));
    }
    
    public function exchange() {
        auth('return_onshelf');
        
        $this->load->model('location_model');
        
        // 检查原储位
        $from_location_name = trim($this->input->post('from_location_name'));
        if (empty($from_location_name)) {
            sys_msg('请扫描原储位！', 1);
        }
        $from_location = $this->location_model->get_location(array('location_name' => $from_location_name));
        if (!$from_location) {
            sys_msg('原储位['.$from_location_name.']不存在！', 1);
        }
        
        // 检查目标储位
        $to_location_name = trim($this->input->post('to_location_name'));
        if (empty($to_location_name)) {
            sys_msg('请扫描目标储位！', 1);
        }
        $to_location = $this->location_model->get_location(array('location_name' => $to_location_name));
        if (!$to_location) {
            sys_msg('目标储位['.$to_location_name.']不存在！', 1);
        }
        
        // 检查原储位和目标储位是否相同
        if ($from_location->location_id == $to_location->location_id) {
            sys_msg('原储位和目标储位相同，无需移动！', 1);
        }
        
        // 检查原储位和目标储位是否在同一仓库
        if ($from_location->depot_id != $to_location->depot_id) {
            sys_msg('原储位和目标储位不在同一仓库，不能移动！', 1);
        }
        
        // 检查商品
        $product_id_ary = $this->input->post('product_id_ary');
        $color_id_ary = $this->input->post('color_id_ary');
        $size_id_ary = $this->input->post('size_id_ary');
        $shop_price_ary = $this->input->post('shop_price_ary');
        $batch_id_ary = $this->input->post('batch_id_ary');
        $product_number_ary = $this->input->post('product_number_ary');
        if (empty($product_id_ary) || empty($product_number_ary)) {
            sys_msg('未扫描商品！', 1);
        }
        
        // 查询商品
        $products = $this->do_get_exchange_location_products($product_id_ary, $color_id_ary, $size_id_ary, $shop_price_ary, $batch_id_ary, $product_number_ary);
        if (count($products) <= 0) {
            sys_msg('未移动商品！', 1);
        }
        
        // 实际自动调仓
        $this->load->model('exchange_location_model');
        $this->exchange_location_model->exchange($from_location, $to_location, $products, $this->admin_id);
        
        echo json_encode(array('err'=>0, 'msg'=>'succeed'));
    }
    
    /* ---- private methods ------------------------------------------------- */
    private function do_get_default_from_location_name($depot) {
        $depot_id = $depot->depot_id;
        if ($depot_id == DT_RETURN_DEPOT_ID) { //代销退货仓
            return DT_RETURN_DEPOT_LOCATION_NAME;
        } else if ($depot_id == MT_RETURN_DEPOT_ID) {  //买断退货仓
            return MT_RETURN_DEPOT_LOCATION_NAME;
        } else if ($depot_id == BT_RETURN_DEPOT_ID) { //第三方退货仓
            return BT_RETURN_DEPOT_LOCATION_NAME;
        }
        return null;
    }
    
    private function do_get_exchange_location_products($product_id_ary, $color_id_ary, $size_id_ary, $shop_price_ary, $batch_id_ary, $product_number_ary) {
        $list = array();
        foreach ($product_id_ary as $key => $product_id) {
            $product = array();
            $product['product_id'] = $product_id;
            $product['color_id'] = $color_id_ary[$key];
            $product['size_id'] = $size_id_ary[$key];
            $product['shop_price'] = $shop_price_ary[$key];
            $product['batch_id'] = $batch_id_ary[$key];
            $product['product_number'] = $product_number_ary[$key];
            
            $list[] = $product;
        }
        
        return $list;
    }
}

?>
