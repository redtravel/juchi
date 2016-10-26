<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Region extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('region_model');
    }

    public function index ()
    {
       auth(array('region_view','region_edit'));
       $this->load->helper('perms_helper');
       $this->load->vars('perms' , get_region_perm());
       $region_type = intval($this->uri->segment(3,0));
       $parent_id = intval($this->uri->segment(4,0));
       $region = $this->region_model->all_region(array('parent_id' => $parent_id , 'region_type' => $region_type));
       $this->load->vars('region' , $region);
       $this->load->vars('region_type' , $region_type);
       $this->load->vars('parent_id' , $parent_id);
       $this->load->view('region/list');
    }

    public function delete($region_id)
    {
        auth('region_edit');
        $region_id = intval($region_id);
        $region = $this->region_model->filter(array('region_id' => $region_id));
        if(!$region) sys_msg('记录不存在', 1);
        $check_parent = $this->region_model->filter(array('parent_id' => $region_id));
        if(!empty($check_parent)) sys_msg('记录无法删除', 1);
        // 订单、换货单中使用过的不能删除
        if($this->region_model->region_in_order($region_id)) sys_msg('记录无法删除',1);
        if($this->region_model->region_in_change($region_id)) sys_msg('记录无法删除',1);
        $this->region_model->delete($region_id);
        sys_msg('操作成功',2,array(array('href'=>'region/index/'.$region->region_type.'/'.$region->parent_id,'text'=>'返回列表页')));
    }


    public function add()
    {
       auth('region_edit');
       $region_type = intval($this->uri->segment(3,0));
       $parent_id = intval($this->uri->segment(4,0));
       $this->load->vars('region_type' , $region_type);
       $this->load->vars('parent_id' , $parent_id);
       $this->load->view('region/add');
    }

    public function proc_add()
    {
        auth('region_edit');
        $data['parent_id'] = intval($this->uri->segment(4,0));
        $data['region_name'] = trim($this->input->post('region_name'));
        $data['region_type']  = intval($this->uri->segment(3,0));
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('region_name', 'region_name', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->region_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'region/index/'.$data['region_type'].'/'.$data['parent_id'],'text'=>'返回列表页')));
    }
    
    function edit(){
        auth('region_edit');
        $region_id = intval($this->uri->segment(3,0));
        $region_type = intval($this->uri->segment(4,0));
        $parent_id = intval($this->uri->segment(5,0));
        $this->load->vars('region_type' , $region_type);
        $this->load->vars('parent_id' , $parent_id);
		if($region_type == 1) {
			$region_shipping_fee = $this->region_model->region_shipping_fee($region_id);
			$online_shipping_fee = '';
			$cod_shipping_fee = '';
			if(!empty($region_shipping_fee)) {
				$online_shipping_fee = $region_shipping_fee->online_shipping_fee;
				$cod_shipping_fee = $region_shipping_fee->cod_shipping_fee;
			}
			$this->load->vars('online_shipping_fee' , $online_shipping_fee);
			$this->load->vars('cod_shipping_fee' , $cod_shipping_fee);
		}
        $check = $this->region_model->filter(array('region_id' => $region_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        $this->load->vars('region' , $check);
        $this->load->view('region/edit');
    }
    
    function proc_edit(){
        auth('region_edit');
        $region_id = intval($this->uri->segment(3,0));
        $region_type = intval($this->uri->segment(4,0));
        $parent_id = intval($this->uri->segment(5,0));
        $update['region_name'] = trim($this->input->post('region_name'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules('region_name', 'region_name', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->region_model->update($update , $region_id);
		if($region_type == 1) {
			$update_fee['online_shipping_fee'] = intval($this->input->post('online_shipping_fee'));
			$update_fee['cod_shipping_fee'] = intval($this->input->post('cod_shipping_fee'));
			$region_shipping_fee = $this->region_model->region_shipping_fee($region_id);
			if(empty($update_fee['online_shipping_fee']) && empty($update_fee['cod_shipping_fee']) && !empty($region_shipping_fee)) {
				$this->region_model->delete_region_shipping ($region_id);
			} else {
				if(!empty($region_shipping_fee)) {
					$this->region_model->update_region_shipping($update_fee,$region_id);
				} else {
					$update_fee['province_id'] = $region_id;
					$this->region_model->insert_region_shipping($update_fee);
				}
			}
		}
        sys_msg('操作成功',2,array(array('href'=>'region/index/'.$region_type.'/'.$parent_id,'text'=>'返回列表页')));
    }

    public function search()
    {
        $parent = intval($this->input->post('parent'));
        $target = trim($this->input->post('target'));
        $regions = $this->region_model->all_region(array('parent_id'=>$parent));
        print json_encode(array('target'=>$target,'regions'=>$regions));
    }
    
  
    
    
}