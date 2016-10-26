<?php
class Hotword extends CI_Controller 
{
    public function __construct ()
    {
            parent::__construct();
            $this->admin_id = $this->session->userdata('admin_id');
            if ( ! $this->admin_id )
            {
                    redirect('index/login');
            }
            $this->load->model('hotword_model');
            $this->perms = array();
            $this->perms['hotword_edit'] = check_perm('hotword_edit') ? '1' : '2';
            $this->perms['hotword_view'] = check_perm('hotword_view') ? '1' : '2';
    }

    public function index ()
    {
        auth(array('hotword_edit','hotword_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , $this->perms);
        $filter = $this->uri->uri_to_assoc(3);
        
        $hotword_name = $this->input->post("hotword_name");
        if(!empty($hotword_name)) $filter['hotword_name'] = $hotword_name;

        $filter = get_pager_param($filter);
        $data = $this->hotword_model->list_f($filter);
        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('hotword/list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }
        $data['full_page'] = TRUE;
        $this->load->view('hotword/list', $data);
    }
    
    public function add(){
        auth('hotword_edit');
        $this->load->view('hotword/add');
    }
    
    public function proc_add(){
        auth('hotword_edit');
        $data['hotword_name'] = $this->input->post('hotword_name');
        $data['hotword_url'] = $this->input->post('hotword_url');
        $data['sort_order'] = $this->input->post('sort_order'); 
        $data['click_count'] = $this->input->post('click_count'); 
        $data['hotword_type'] = $this->input->post('hotword_type'); 
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s' , time());
        $this->load->library('form_validation');
        $this->form_validation->set_rules('hotword_name', 'hotword_name', 'trim|required');
        $this->form_validation->set_rules('hotword_url', 'hotword_url', 'trim|required');
        $this->form_validation->set_rules('sort_order', 'sort_order', 'trim|required');
        $this->form_validation->set_rules('click_count', 'click_count', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->hotword_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'hotword/index','text'=>'返回列表页')));
    }
    
    public function edit($hotword_id){
        auth('hotword_edit');
        $hotword_id = intval($hotword_id);
        $check =  $this->hotword_model->filter(array('hotword_id' => $hotword_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        $this->load->vars('arr' , $check);
        $this->load->view('hotword/edit');
    }
    
    public function proc_edit($hotword_id){
        auth('hotword_edit');
        $hotword_id = intval($hotword_id);
        $data['hotword_name'] = $this->input->post('hotword_name');
        $data['hotword_url'] = $this->input->post('hotword_url');
        $data['sort_order'] = $this->input->post('sort_order'); 
        $data['click_count'] = $this->input->post('click_count'); 
        $data['hotword_type'] = $this->input->post('hotword_type'); 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('hotword_name', 'hotword_name', 'trim|required');
        $this->form_validation->set_rules('hotword_url', 'hotword_url', 'trim|required');
        $this->form_validation->set_rules('sort_order', 'sort_order', 'trim|required');
        $this->form_validation->set_rules('click_count', 'click_count', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->hotword_model->update($data,$hotword_id);
        sys_msg('操作成功',2,array(array('href'=>'hotword/index','text'=>'返回列表页')));
    }
    
    public function delete($hotword_id){
        auth('hotword_edit');
        $hotword_id = intval($hotword_id);
        $test = $this->input->post('test');
        $check =  $this->hotword_model->filter(array('hotword_id' => $hotword_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        if($test) sys_msg('');
        $this->hotword_model->delete(array('hotword_id' => $hotword_id));
        sys_msg('操作成功',2,array(array('href'=>'hotword/index','text'=>'返回列表页')));
    }
    
}
?>
