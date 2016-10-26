<?php
class Order_advice_type extends CI_Controller 
{
    public function __construct ()
    {
            parent::__construct();
            $this->admin_id = $this->session->userdata('admin_id');
            if ( ! $this->admin_id )
            {
                redirect('index/login');
            }
            $this->load->model('order_advice_type_model');
    }

    public function index ()
    {
        auth(array('suggestion_view','suggestion_edit'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_order_advice_type_perm());
        $data = index_array($this->order_advice_type_model->all(),'type_id');
        $this->load->vars('list' , $data);
        $this->load->view('order_advice_type/list');
    }
    
    public function add(){
        auth('suggestion_edit');
        $this->load->view('order_advice_type/add');
    }
    
    public function proc_add(){
        auth('suggestion_edit');
        $data['type_name'] = $this->input->post('type_name');
        $data['type_code'] = $this->input->post('type_code');
        $data['type_color'] = $this->input->post('type_color');
        $data['is_use'] = $this->input->post('is_use'); 
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s' , time());
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('type_name', 'type_name', 'trim|required');
        $this->form_validation->set_rules('type_code', 'type_code', 'trim|required');
        $this->form_validation->set_rules('type_color', 'type_color', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->order_advice_type_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'order_advice_type/index','text'=>'返回列表页')));
    }
    
    public function edit($type_id){
        auth('suggestion_edit');
        $type_id = intval($type_id);
        $check =  $this->order_advice_type_model->filter(array('type_id' => $type_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        $this->load->vars('type' , $check);
        $this->load->view('order_advice_type/edit');
    }
    
    public function proc_edit($type_id){
        auth('suggestion_edit');
        $type_id = intval($type_id);
        $data['type_name'] = $this->input->post('type_name');
        $data['type_code'] = $this->input->post('type_code');
        $data['type_color'] = $this->input->post('type_color');
        $data['is_use'] = $this->input->post('is_use'); 
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('type_name', 'type_name', 'trim|required');
        $this->form_validation->set_rules('type_code', 'type_code', 'trim|required');
        $this->form_validation->set_rules('type_color', 'type_color', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->order_advice_type_model->update($data , $type_id);
        sys_msg('操作成功',2,array(array('href'=>'order_advice_type/index','text'=>'返回列表页')));
    }
    
    public function delete($type_id){
        auth('suggestion_del');
        $type_id = intval($type_id);
        $check =  $this->order_advice_type_model->filter(array('type_id' => $type_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        $this->order_advice_type_model->delete($type_id);
        sys_msg('操作成功',2,array(array('href'=>'order_advice_type/index','text'=>'返回列表页')));
    }
    
}
?>
