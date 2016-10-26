<?php
class Order_source extends CI_Controller 
{
    public function __construct ()
    {
            parent::__construct();
            $this->admin_id = $this->session->userdata('admin_id');
            if ( ! $this->admin_id )
            {
                redirect('index/login');
            }
            $this->load->model('order_source_model');
    }

    public function index ()
    {
        auth(array('order_source_edit','order_source_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_order_source_perm());
        $data = $this->order_source_model->all_source();
        $this->load->vars('list' , $data);
        $this->load->view('order_source/list');
    }
    
    public function add(){
        auth('order_source_edit');
        $this->load->view('order_source/add');
    }
    
    public function proc_add(){
        auth('order_source_edit');
        $data['source_code'] = $this->input->post('source_code');
        $data['source_name'] = $this->input->post('source_name');
        $data['is_use'] = $this->input->post('is_use'); 
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s' , time());
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('source_code', 'source_code', 'trim|required');
        $this->form_validation->set_rules('source_name', 'source_name', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->order_source_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'order_source/index','text'=>'返回列表页')));
    }
    
    public function edit($source_id){
        auth('order_source_edit');
        $source_id = intval($source_id);
        $check =  $this->order_source_model->filter(array('source_id' => $source_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        $this->load->vars('source' , $check);
        $this->load->view('order_source/edit');
    }
    
    public function proc_edit($source_id){
        auth('order_source_edit');
        $source_id = intval($source_id);
        $data['source_code'] = $this->input->post('source_code');
        $data['source_name'] = $this->input->post('source_name');
        $data['is_use'] = $this->input->post('is_use'); 
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('source_code', 'source_code', 'trim|required');
        $this->form_validation->set_rules('source_name', 'source_name', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->order_source_model->update($data , $source_id);
        sys_msg('操作成功',2,array(array('href'=>'order_source/index','text'=>'返回列表页')));
    }
    
    public function delete($source_id){
        auth('order_source_edit');
        $this->load->model('order_model');
        $source_id = intval($source_id);
        $check =  $this->order_source_model->filter(array('source_id' => $source_id));
        if(empty($check)) sys_msg('记录不存在', 1);
        $check = $this->order_model->filter_routing(array('source_id'=>$source_id));
        if($check) sys_msg('该记录不能删除',1);
        $this->order_source_model->delete($source_id);
        sys_msg('操作成功',2,array(array('href'=>'order_source/index','text'=>'返回列表页')));
    }
    
}
?>
