<?php
/*
 * 代销转买断管理。
 */
class CTB extends CI_Controller {
    
    function __construct () {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if(!$this->admin_id) redirect('index/login');
        $this->load->model('ctb_model');
    }
    
    function index() {
        auth('ctb_manage');
        
        $this->load->view('depot/ctb/index');
    }
    
    /*
     * 根据商品条码查询代转买商品信息。
     */
    function get_product() {
        auth('ctb_manage');
        
        //1.条码不能为空
        $provider_barcode = trim($this->input->post('provider_barcode'));
        if (!$provider_barcode) {
            sys_msg('商品条码为空！', 1);
        }
        
        //2.判断商品及批次是否存在
        $this->load->model('product_model');
        $product_batchs = $this->ctb_model->query_product_batchs($provider_barcode);
        if (empty($product_batchs) || count($product_batchs) <= 0) {
            sys_msg('指定条码的商品信息不存在！', 1);
        }
        
        //3.判断商品的所有代销批次是否已结算
        //4.判断商品是否已有买断批次
        $c_batch = null; // 代销商品批次
        $b_batch = null; // 买断商品批次
        $ctb_batch = null; // 代转买批次
        foreach ($product_batchs as $value) {
            //查找代销批次未结算
            if ($value->cooperation_id == 2 && $value->is_reckoned == 0) {
                $c_batch = $value;
            } else if ($value->cooperation_id == 1) {
                if ($value->batch_type == 1) {
                    $ctb_batch = $value;
                } else {
                    $b_batch = $value;
                }
            }
        }
        
        if (!empty($c_batch)) {
            sys_msg('指定条码的商品含有代销未结算的批次，无需代转买！', 1);
        } else if (!empty($b_batch)) {
            sys_msg('指定条码的商品含有买断批次，无需代转买！', 1);
        } else if (!empty($ctb_batch)) {
            $result = $ctb_batch;
        } else {
            $result = $product_batchs[count($product_batchs) - 1];
        }
        
        //5.返回商品基本信息和结算批次
        echo json_encode(array('err' => 0, 'product' => $result));
    }
    
    /*
     * 根据提交的商品信息，做代销转买断。
     */
    public function ctb_products() {
        auth('ctb_manage');
        
        $product_ary = $this->input->post('product_ary');
        if (count($product_ary) <= 0) {
            sys_msg('请添加代转买商品！', 1);
        }
        
        $this->db->trans_begin();
        $this->ctb_model->ctb($product_ary, $this->admin_id);
        $this->db->trans_commit();
    }
    
}

?>
