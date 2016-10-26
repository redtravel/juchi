<?php
    class Product_cost_price extends CI_Controller{
        function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('brand_model');
		$this->load->model('cost_price_model');
        
        
	}
         public function search(){
        auth('cost_price_check');
		$this->load->helper('product');
		$filter = $this->uri->uri_to_assoc(3);
		$filter['product_sn'] = trim($this->input->post('product_sn'));
		$filter['batch_code'] = $this->input->post('batch_code');
		$filter['brand_id'] = $this->input->post('brand_id');
		
		$filter['sort_by'] = empty($filter['sort_by']) ? 'pi.product_sn' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'asc' : trim($filter['sort_order']);
		$filter = get_pager_param($filter);
        
		$data = $this->cost_price_model->product_cost_price_list($filter);
		$this->load->vars('perm_edit', check_perm('cost_price_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('product/cost_price/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->vars('all_brand', $this->brand_model->all_brand());
		$this->load->view('product/cost_price/index', $data);
        }
        public function edit_cost_price()
	{
        auth('cost_price_edit');
		$id = intval($this->input->post('id'));
		$field = trim($this->input->post('field'));
		$val = trim($this->input->post('val'));

		$row = $this->cost_price_model->filter(array('id'=>$id));
		if (!$row) {
			sys_msg('记录不存在',1);
		}
        $cnt = $this->cost_price_model->get_product_record(array('product_id'=>$row->product_id,'batch_id'=>$row->batch_id));
        if($cnt>0){
            sys_msg('此商品已经形成单据，不能修改.',1);
        }
		$update = array();
		$update['update_admin'] = intval($this->admin_id);
		$update['update_time'] = date('Y-m-d H:i:s');
		switch ($field) {
			case 'cost_price':
                $update['cost_price'] = $val;	
				break;
            case 'consign_price':
                $update['consign_price'] = $val;
				break;
            case 'consign_rate':
                $update['consign_rate'] = $val;
                if($val>=1){
                    sys_msg('代销率只能小于1',1);
                }
				break;
            case 'product_cess':
                $update['product_cess'] = $val;
                if($val>=1){
                    sys_msg('税率只能小于1',1);
                }
				break;
			default:
				sys_msg('参数错误',1);
		}
        $this->cost_price_model->update($update, $id);
        $row = $this->cost_price_model->filter(array('id'=>$id));
        $insert= array();
        $insert['batch_id']=$row->batch_id;
        $insert['product_id']=$row->product_id;
        $insert['cost_price']=$row->cost_price;
        $insert['consign_price']=$row->consign_price;
        $insert['consign_rate']=$row->consign_rate;
		$insert['create_admin'] = intval($this->admin_id);
		$insert['create_date'] = date('Y-m-d H:i:s');
        $this->cost_price_model->insert($insert);
        $content ="";
        
        switch ($field) {
			case 'cost_price':
                $content = $row->cost_price;
				break;
            case 'consign_price':
                $content = $row->consign_price;
				break;
            case 'consign_rate':
                $content = $row->consign_rate;
				break;
            case 'product_cess':
                $content = $row->product_cess;
				break;
		}
        print json_encode(array('err'=>0,'msg'=>'','content'=>$content));
		return;
	}
    }


?>
