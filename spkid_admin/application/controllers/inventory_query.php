<?php

class Inventory_query extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        $this->time = date('Y-m-d H:i:s');
        if (!$this->admin_id)
            redirect('index/login');

        $this->load->model('depot_model');
        $this->load->model('purchase_model');
    }

    public function index() {
        auth('inventory_query');

        $filter = $this->uri->uri_to_assoc(3);

        $this->load->vars('provider_list', $this->depot_model->sel_provider_name_list());
        $this->load->vars('batch_list', $this->purchase_model->get_inventory_batch(0));
        $this->load->vars('brand_list', $this->purchase_model->get_inventory_brand(0));
        $this->load->vars('depot_list', $this->depot_model->sel_depot_list());

        $product_sn = trim($this->input->post('product_sn'));
        if (!empty($product_sn)) {
            $filter['product_sn'] = $product_sn;
        }
        
        $provider_barcode = trim($this->input->post('provider_barcode'));
        if (!empty($provider_barcode)) {
            $filter['provider_barcode'] = $provider_barcode;
        }

        $provider_id = trim($this->input->post('provider_id'));
        if (!empty($provider_id)) {
            $filter['provider_id'] = $provider_id;
        }

        $brand_id = trim($this->input->post('brand_id'));
        if (!empty($brand_id)) {
            $filter['brand_id'] = $brand_id;
        }

        $purchase_batch = trim($this->input->post('purchase_batch'));
        if (!empty($purchase_batch)) {
            $filter['purchase_batch'] = $purchase_batch;
        }

        $sell_mode = trim($this->input->post('sell_mode'));
        if (!empty($sell_mode)) {
            $filter['sell_mode'] = $provider_id;
        }

        $depot_id = trim($this->input->post('depot_id'));
        if (!empty($depot_id)) {
            $filter['depot_id'] = $depot_id;
        }

        $filter = get_pager_param($filter);

        $data = $this->purchase_model->search_inventory($filter);
	$data['inventory_export'] = check_perm( 'all_inventory_export');
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('purchase/inventory_query', $data, TRUE);
            $data['error'] = 0;
            unset($data['list']);
            echo json_encode($data);
            return;
        }
        $data['full_page'] = TRUE;

        $this->load->view('purchase/inventory_query', $data);
    }
    public function export(){
	auth( 'all_inventory_export');

	$data = $this->purchase_model->get_export_inventory();
	$data['tag'] = '?';
	$this->load->view('purchase/inventory_export', $data);
	$file_name = "inventory_".(date('YmdHis')).".xls";
	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition:attachment;filename=".$file_name);
    }

    public function search_inventory() {
        $data = $this->purchase_model->search_inventory();
        $this->load->view('purchase/inventory_query', array('list' => $data));
    }

    public function get_inventory_batch($provider_id) {
        echo json_encode($this->purchase_model->get_inventory_batch($provider_id));
    }

    public function get_inventory_brand($provider_id) {
        echo json_encode($this->purchase_model->get_inventory_brand($provider_id));
    }
    
    public function get_product_location(){
        $product_id = $this->input->post('product_id');
        $color_id = $this->input->post('color_id');
        $size_id = $this->input->post('size_id');
        $data = $this->purchase_model->get_product_location($product_id, $color_id, $size_id);
        $data['content'] = $this->load->view('/purchase/view_product_location', $data, TRUE);
        $data['error'] = 0;
        unset($data['list']);
        echo json_encode($data);
        return;
    }

}

?>
