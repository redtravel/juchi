<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class User_account_log_kind extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('user_account_log_kind_model');
    }

    public function index ()
    {
        auth(array('uaccount_k_view','uaccount_k_edit'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_account_kind_perm());
        $this->load->model('user_account_log_model');
        $log_arr = $this->user_account_log_model->distinct_log_kind();
        $this->load->vars('log_arr' , $log_arr);
        $arr = $this->user_account_log_kind_model->all_kind();
        $this->load->vars('arr' , $arr);
        $this->load->view('user_account_log_kind/list');
    }

    public function del($change_code)
    {
        auth('uaccount_k_edit');
        $check = $this->user_account_log_kind_model->filter(array('change_code' => $change_code));
        $test = $this->input->post('test');
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->model('user_account_log_model');
        $check_con = $this->user_account_log_model->filter(array('change_code' => $change_code)); 
         if(!empty($check_con)){
            sys_msg('不能删除',1);
            return;
        }
        if($test) sys_msg('');
        $this->user_account_log_kind_model->delete(array('change_code' => $change_code));
        sys_msg('操作成功',2,array(array('href'=>'/user_account_log_kind/index','text'=>'返回列表页')));
    }


    public function add()
    {
        auth('uaccount_k_edit');
        $this->load->view('user_account_log_kind/add');
    }

    public function proc_add()
    {
        auth('uaccount_k_edit');
        $data['change_code'] = $this->input->post('change_code');
        $data['change_name'] = $this->input->post('change_name');
        $data['is_use'] = $this->input->post('is_use');
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('change_code', '变动CODE', 'trim|required');
        $this->form_validation->set_rules('change_name', '变动名称', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $che = $this->user_account_log_kind_model->filter(array('change_code' => $data['change_code']));
        if(!empty($che)){
            sys_msg('记录已经存在',1);
            return;
        }
        $this->user_account_log_kind_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'/user_account_log_kind/index','text'=>'返回列表页')));
    }

    public function edit($change_code){
        auth(array('uaccount_k_view','uaccount_k_edit'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_account_kind_perm());
        $check = $this->user_account_log_kind_model->filter(array('change_code' => $change_code));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->model('user_account_log_model');
        $check_con = $this->user_account_log_model->filter(array('change_code' => $change_code)); 
        if(empty($check_con)){
            $this->load->vars('use_type','1');
        }else{
            $this->load->vars('use_type','2');
        }
        $this->load->vars('arr',$check);
        $this->load->view('user_account_log_kind/edit');
    }

    public function proc_edit($change_code){
        auth('uaccount_k_edit');
        $data['change_code'] = $this->input->post('change_code');
        $data['change_name'] = $this->input->post('change_name');
        $data['is_use'] = $this->input->post('is_use');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('change_code', 'change_code', 'trim|required');
        $this->form_validation->set_rules('change_name', '变动名称', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $che = $this->user_account_log_kind_model->filter(array('change_code' => $change_code));
        if(empty($che)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->user_account_log_kind_model->update($data , $change_code);
        sys_msg('操作成功',2,array(array('href'=>'/user_account_log_kind/index','text'=>'返回列表页')));
    }

}
