<?php

class Inventory_warning extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        $this->time = date('Y-m-d H:i:s');
        if (!$this->admin_id)
            redirect('index/login');

        $this->load->model('depot_model');
        $this->load->model('purchase_model');
        $this->load->model('inventory_model');
    }

    public function index() {
        auth('inventory_warning');

        $filter = $this->uri->uri_to_assoc(3);

        $this->load->vars('provider_list', $this->depot_model->sel_provider_name_list());
        $this->load->vars('batch_list', $this->purchase_model->get_inventory_batch(0));

        $product_sn = trim($this->input->post('product_sn'));
        if (!empty($product_sn)) {
            $filter['product_sn'] = $product_sn;
        }

        $provider_id = trim($this->input->post('provider_id'));
        if (!empty($provider_id)) {
            $filter['provider_id'] = $provider_id;
        }

        $purchase_batch = trim($this->input->post('purchase_batch'));
        if (!empty($purchase_batch)) {
            $filter['purchase_batch'] = $purchase_batch;
        }

        $filter = get_pager_param($filter);

        //$data = $this->purchase_model->search_inventory($filter);
        $data = $this->inventory_model->search_warning_inventory_list($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('purchase/inventory_warning', $data, TRUE);
            $data['error'] = 0;
            unset($data['list']);
            echo json_encode($data);
            return;
        }
        $data['full_page'] = TRUE;

        $this->load->view('purchase/inventory_warning', $data);
    }

    public function search_inventory() {
        $data = $this->purchase_model->search_inventory();
        $this->load->view('purchase/inventory_warning', array('list' => $data));
    }

    public function get_inventory_batch($provider_id) {
        echo json_encode($this->purchase_model->get_inventory_batch($provider_id));
    }

    public function get_inventory_brand($provider_id) {
        echo json_encode($this->purchase_model->get_inventory_brand($provider_id));
    }
    
    public function view_warning_list($id = 0)
    {
        auth('inventory_warning_list');

        $filter = $this->uri->uri_to_assoc(3);

        $this->load->vars('provider_list', $this->depot_model->sel_provider_name_list());
        $this->load->vars('batch_list', $this->purchase_model->get_inventory_batch(0));
        $this->load->vars('perm_edit', check_perm('inventory_warning_edit'));
        $this->load->vars('perm_delete', check_perm('inventory_warning_delete'));

        $filter['id'] = $id;
        $product_sn = trim($this->input->post('product_sn'));
        if (!empty($product_sn)) {
            $filter['product_sn'] = $product_sn;
        }

        $provider_id = trim($this->input->post('provider_id'));
        if (!empty($provider_id)) {
            $filter['provider_id'] = $provider_id;
        }

        $purchase_batch = trim($this->input->post('purchase_batch'));
        if (!empty($purchase_batch)) {
            $filter['purchase_batch'] = $purchase_batch;
        }

        $filter = get_pager_param($filter);

        $data = $this->inventory_model->search_inventory_warning_list($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('purchase/inventory_warning_list', $data, TRUE);
            $data['error'] = 0;
            unset($data['list']);
            echo json_encode($data);
            return;
        }
        $data['full_page'] = TRUE;
        
        $this->load->view('purchase/inventory_warning_list', $data);
    }
    
    public function add()
    {
            auth('warning_add');
            $this->load->vars('provider_list', $this->depot_model->sel_provider_name_list());
            $this->load->vars('batch_list', $this->purchase_model->get_inventory_batch(0));
            $this->load->view('purchase/inventory_warning_add');
    }
    
    public function proc_add()
    {
        auth('warning_add');
        $update = array();
        $update['warn_type'] = $this->input->post('warn_type');
        $update['min_number'] = $this->input->post('min_number');
        $update['create_admin'] = $this->admin_id;
        $update['create_date'] = $this->time;
        
        if ($update['warn_type'] == 1)
        {
            $this->load->model('product_model');
            $product_id = $this->product_model->get_product_ids(array($this->input->post('product_sn')));
            if (empty($product_id))
            {
                sys_msg('款号不存在', 1);
            }
            $update['warn_value'] = $product_id[0]['product_id'];
            
        }
        elseif ($update['warn_type'] == 2)
        {
            $update['warn_value'] = $this->input->post('purchase_batch');
        }
        $warning_info = $this->inventory_model->filter_inventory_warning(array('warn_value' => $update['warn_value']));
        if (!empty($warning_info))
        {
                sys_msg('记录已存在', 1);
        }
        $update['warn_status'] = 1;
        
        $warning_id = $this->inventory_model->insert_inventory_warning($update);
        sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'/inventory_warning/edit_warning_info/'.$warning_id)));
    }
    
    public function edit_warning_info ($warning_id = 0)
    {
            auth(array('warning_edit','warning_view'));
            $warning_info = $this->inventory_model->filter_inventory_warning(array('id' => $warning_id));
            if (empty($warning_info) )
            {
                    sys_msg('记录不存在！', 1);
            }
            $this->load->vars('can_edit', check_perm('warning_edit'));
            $this->load->vars('row', $this->inventory_model->search_inventory_warning_info($warning_id));
            $this->load->vars('provider_list', $this->depot_model->sel_provider_name_list());
            $this->load->vars('batch_list', $this->purchase_model->get_inventory_batch(0));
            $this->load->view('purchase/inventory_warning_edit');
    }
    
    public function proc_edit_warning()
    {
            auth('warning_edit');
            $warning_id = intval($this->input->post('warning_id'));
            $warning_info = $this->inventory_model->filter_inventory_warning(array('id' => $warning_id));
            if ( empty($warning_info) )
            {
                    sys_msg('记录不存在', 1);
            }
            $update = array();
            
            $update['warn_type'] = $this->input->post('warn_type');
            $update['min_number'] = $this->input->post('min_number');
            $update['update_admin'] = $this->admin_id;
            $update['update_date'] = $this->time;

            if ($update['warn_type'] == 1)
            {
                $this->load->model('product_model');
                $product_id = $this->product_model->get_product_ids(array($this->input->post('product_sn')));
                if (empty($product_id))
                {
                    sys_msg('款号不存在', 1);
                }
                $update['warn_value'] = $product_id[0]['product_id'];
            }
            elseif ($update['warn_type'] == 2)
            {
                $update['warn_value'] = $this->input->post('purchase_batch');
            }
            
            $this->inventory_model->update_inventory_warning($update, $warning_id);
            sys_msg('操作成功！');
    }
    
    public function delete_warning_info($warning_id)
    {
            auth('warning_edit');
            $warning_info = $this->inventory_model->filter_inventory_warning(array('id' => $warning_id));
            if ( empty($warning_info) )
            {
                    sys_msg('记录不存在！', 1);
            }
            if ($this->inventory_model->update_inventory_warning(array('warn_status'=>2), $warning_id) == 1)
            {
                    sys_msg('操作成功！',0 , array(array('text'=>'返回', 'href'=>'/inventory_warning/view_warning_list')));
            } else
            {
                    sys_msg('删除失败！',1);
            }
    }

}

?>
