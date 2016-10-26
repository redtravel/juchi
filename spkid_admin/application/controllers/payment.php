<?php
class Payment extends CI_Controller 
{
    public function __construct ()
    {
            parent::__construct();
            $this->admin_id = $this->session->userdata('admin_id');
            if ( ! $this->admin_id )
            {
                redirect('index/login');
            }
            $this->load->model('payment_model');
            $this->time = date('Y-m-d H:i:s');
            $this->back_type = array(1=>'拆分','返等价物','二选一');
    }

    public function index ()
    {
        auth(array('payment_edit','payment_view'));
        $data = $this->payment_model->all_payment();
        $this->load->vars('list' , $data);
        $this->load->view('payment/list');
    }
    
    public function add(){
        auth('payment_edit');
        $this->load->view('payment/add');
    }
    
    public function proc_add(){
        auth('payment_edit');
        $data['pay_code'] = trim($this->input->post('pay_code')); 
        $data['pay_name'] = trim($this->input->post('pay_name'));
        $data['pay_desc'] = $this->input->post('pay_desc');
        $data['sort_order'] = intval($this->input->post('sort_order'));
        $data['enabled'] = $this->input->post('enabled'); 
        $data['is_online'] = intval($this->input->post('is_online'))==1?1:0; 
        $data['is_discount'] = $this->input->post('is_discount'); 
        $data['back_type'] = $this->input->post('back_type'); 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('pay_code', 'pay_code', 'trim|required');
        $this->form_validation->set_rules('pay_name', 'pay_name', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $pay_id = $this->payment_model->insert($data);
        // 上传图片link_url
        $this->load->library('upload');
        $update = array();
        $base_path = CREATE_IMAGE_PATH.'payment/';
        if(!file_exists($base_path)) mkdir($base_path);

        $this->upload->initialize(array(
                'upload_path' => $base_path,
                'allowed_types' => 'gif|jpg|png',
                'encrypt_name' => TRUE
        ));
        if($this->upload->do_upload('pay_logo')){
                $file = $this->upload->data();
                $update['pay_logo'] = 'payment/'.$file['file_name'];
                $this->payment_model->update($update , $data['pay_code']);
        }
        sys_msg('操作成功',2,array(array('href'=>'payment/index','text'=>'返回列表页')));
    }
    
    public function edit($pay_id){
        auth(array('payment_edit','payment_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_payment_perm());
        $pay_id = trim($pay_id);
        $check =  $this->payment_model->filter(array('pay_id' => $pay_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        $this->load->vars('payment' , $check);
        $this->load->view('payment/edit');
    }
    
    public function proc_edit($pay_id){
        auth('payment_edit');
        $pay_id = trim($pay_id);
        $data['pay_code'] = trim($this->input->post('pay_code')); 
        $data['pay_name'] = trim($this->input->post('pay_name'));
        $data['pay_desc'] = $this->input->post('pay_desc');
        $data['sort_order'] = intval($this->input->post('sort_order'));
        $data['enabled'] = $this->input->post('enabled'); 
        $data['is_online'] = intval($this->input->post('is_online'))==1?1:0; 
        $data['is_discount'] = $this->input->post('is_discount'); 
        $data['back_type'] = $this->input->post('back_type'); 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('pay_code', 'pay_code', 'trim|required');
        $this->form_validation->set_rules('pay_name', 'pay_name', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }

        $this->payment_model->update($data , $pay_id);
        // 上传图片link_url
        $this->load->library('upload');
        $update = array();
        $base_path = CREATE_IMAGE_PATH.'payment/';
        if(!file_exists($base_path)) mkdir($base_path);

        $this->upload->initialize(array(
                'upload_path' => $base_path,
                'allowed_types' => 'gif|jpg|png',
                'encrypt_name' => TRUE
        ));
        if($this->upload->do_upload('pay_logo')){
                $file = $this->upload->data();
                $update['pay_logo'] = 'payment/'.$file['file_name'];
                $this->payment_model->update($update , $pay_id);
        }
        sys_msg('操作成功',2,array(array('href'=>'payment/index','text'=>'返回列表页')));
    }
    
    public function delete($pay_id){
        auth('payment_edit');
        $this->load->model('order_model');
        $this->load->model('user_recharge_model');
        $pay_id = trim($pay_id);
        $test = $this->input->post('test');
        $check =  $this->payment_model->filter(array('pay_id' => $pay_id));
        if(empty($check)) sys_msg('记录不存在', 1); 
        $check = $this->order_model->filter(array('pay_id' => $pay_id));
        if($check) sys_msg('无法删除', 1); 
        $check = $this->order_model->filter_payment(array('pay_id' => $pay_id));
        if($check) sys_msg('无法删除', 1); 
        $check = $this->order_model->filter_routing(array('pay_id' => $pay_id));
        if($check) sys_msg('无法删除', 1); 
        $check = $this->user_recharge_model->filter(array('pay_id' => $pay_id));
        if($check) sys_msg('无法删除', 1); 
        if($test) sys_msg('');
        $this->payment_model->delete($pay_id);
        sys_msg('删除成功',2,array(array('href'=>'payment/index','text'=>'返回列表页')));
    }
    
}
?>
