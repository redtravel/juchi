<?php
/*
 * RF枪仓间移储。
 * @Author  ZhangShixi
 */
class Exchange_location extends CI_Controller {
    
    function __construct () {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if(!$this->admin_id) redirect('index/login');
        $this->load->model('exchange_location_model');
    }
    
    public function index() {
        auth('exchange_location');
        
        $this->load->view('depot/exchange_location/scan');
    }
    
    public function get_products() {
        auth('exchange_location');
        
        $location_name = trim($this->input->post('location_name'));
        if (empty($location_name)) {
            sys_msg('请扫描储位编码！', 1);
        }
        
        $this->load->model('location_model');
        $location = $this->location_model->get_location(array('location_name' => $location_name));
        if (!$location) {
            sys_msg('储位['.$location_name.']不存在！', 1);
        }
        
        $source_location = trim($this->input->post('source_location'));
        if (!empty($source_location) && $source_location == true) {
            // 检查源储位是否有待出待入操作
            $check = $this->exchange_location_model->check_has_in_out_operation($location->location_id);
            if ($check) {
                sys_msg('源储位有待出待入操作，不能移储！', 1);
            }
        }
        
        $data = $this->do_get_products_by_location($location->location_id);
        echo json_encode(array('location_id' => $location->location_id, 'products' => $data));
    }
    
    public function exchange() {
        auth('exchange_location');
        
        // 检查参数
        $from_location_name = trim($this->input->post('from_location_name'));
        $to_location_name = trim($this->input->post('to_location_name'));
        if (empty($from_location_name)) {
            sys_msg('请扫描源储位编码！', 1);
        } else if (empty($to_location_name)) {
            sys_msg('请扫描目标储位编码！', 1);
        }
        
        // 检查源储位
        $this->load->model('location_model');
        $from_location = $this->location_model->get_location(array('location_name' => $from_location_name));
        if (!$from_location) {
            sys_msg('源储位['.$from_location_name.']不存在！', 1);
        }
        
        // 检查目标储位
        $this->load->model('location_model');
        $to_location = $this->location_model->get_location(array('location_name' => $to_location_name));
        if (!$to_location) {
            sys_msg('目标储位['.$to_location_name.']不存在！', 1);
        }
        
        // 检查源储位和目标储位是否相同
        if ($from_location->location_id == $to_location->location_id) {
            sys_msg('源储位和目标储位相同，无需移动！', 1);
        }
        
        // 检查源储位和目标储位是否在同一仓库
        if ($from_location->depot_id != $to_location->depot_id) {
            sys_msg('源储位和目标储位不在同一仓库！', 1);
        }
        
        // 检查源储位是否有待出待入操作
        $data = $this->exchange_location_model->check_has_in_out_operation($from_location->location_id);
        if ($data) {
            sys_msg('源储位有待出待入操作，不能移储！', 1);
        }
                
        // 查询商品
        $products = $this->exchange_location_model->get_products_by_location($from_location);
        if (count($products) <= 0) {
            sys_msg('源储位中无商品！', 1);
        }
        
        // 自动调仓
        $this->exchange_location_model->exchange($from_location, $to_location, $products, $this->admin_id);
        
        echo json_encode(array('err'=>0, 'msg'=>'succeed'));
    }
    
    /* ---- private methods -------------------------------------------------- */
    private function do_get_products_by_location($location_id) {
        $this->load->model('depot_model');
        $data = $this->depot_model->location_info_scan($location_id);
        
        return $data['list'];
    }
    
}

?>
