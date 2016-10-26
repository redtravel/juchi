<?php
class Friend extends CI_Controller 
{
    public function __construct ()
    {
            parent::__construct();
            $this->admin_id = $this->session->userdata('admin_id');
            if ( ! $this->admin_id )
            {
                    redirect('index/login');
            }
            $this->load->model('friend_model');
    }

    public function index ()
    {
        auth(array('friendlink_edit','friendlink_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);
        
        $link_name = $this->input->post("link_name");
        if(!empty($link_name)) $filter['link_name'] = $link_name;
        $link_url = $this->input->post("link_url");
        if(!empty($link_url)) $filter['link_url'] = $link_url;

        $filter = get_pager_param($filter);
        $data = $this->friend_model->list_f($filter);
        $this->load->model('admin_model');
        $this->load->vars('all_admin' ,  $this->admin_model->all_admin());
        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('friend/list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }
        $data['full_page'] = TRUE;
        $this->load->view('friend/list', $data);
    }
    
    public function add(){
        auth('friendlink_edit');
        $this->load->view('friend/add');
    }
    
    public function proc_add(){
        auth('friendlink_edit');
        $data['link_name'] = $this->input->post('link_name');
        $data['link_url'] = $this->input->post('link_url');
        $data['sort_order'] = $this->input->post('sort_order'); 
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s' , time());
        $this->load->library('form_validation');
        $this->form_validation->set_rules('link_name', 'link_name', 'trim|required');
        $this->form_validation->set_rules('link_url', 'link_url', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $friend_id = $this->friend_model->insert($data);
        // 上传图片link_url
        $this->load->library('upload');
        $update = array();
        $base_path = CREATE_IMAGE_PATH.'friend/';
        if(!file_exists($base_path)) mkdir($base_path);

        $this->upload->initialize(array(
                'upload_path' => $base_path,
                'allowed_types' => 'gif|jpg|png',
                'encrypt_name' => TRUE
        ));
        if($this->upload->do_upload('logo')){
                $file = $this->upload->data();
                $update['link_logo'] = 'friend/'.$file['file_name'];
                $this->friend_model->update($update , $friend_id);
        }
        sys_msg('操作成功',2,array(array('href'=>'friend/index','text'=>'返回列表页')));
    }
    
    public function edit($friend_id){
        auth('friendlink_edit');
        $friend_id = intval($friend_id);
        $check =  $this->friend_model->filter(array('link_id' => $friend_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        $this->load->vars('link' , $check);
        $this->load->view('friend/edit');
    }
    
    public function proc_edit($link_id){
        auth('friendlink_edit');
        $link_id = intval($link_id);
        $data['link_name'] = $this->input->post('link_name');
        $data['link_url'] = $this->input->post('link_url');
        $data['sort_order'] = $this->input->post('sort_order'); 
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s' , time());
        $this->load->library('form_validation');
        $this->form_validation->set_rules('link_name', 'link_name', 'trim|required');
        $this->form_validation->set_rules('link_url', 'link_url', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->friend_model->update($data,$link_id);
        // 上传图片link_url
        $this->load->library('upload');
        $update = array();
        $base_path = CREATE_IMAGE_PATH.'friend/';
        if(!file_exists($base_path)) mkdir($base_path);

        $this->upload->initialize(array(
                'upload_path' => $base_path,
                'allowed_types' => 'gif|jpg|png',
                'encrypt_name' => TRUE
        ));
        if($this->upload->do_upload('logo')){
                $file = $this->upload->data();
                $update['link_logo'] = 'friend/'.$file['file_name'];
                $this->friend_model->update($update , $link_id);
        }
        sys_msg('操作成功',2,array(array('href'=>'friend/index','text'=>'返回列表页')));
    }
    
    public function delete($friend_id){
        auth('friendlink_del');
        $friend_id = intval($friend_id);
        $test = $this->input->post('test');
        $check =  $this->friend_model->filter(array('link_id' => $friend_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        if($test) sys_msg('');
        if ($check->link_logo) {
            @unlink(CREATE_IMAGE_PATH.$check->link_logo);
        }
        $this->friend_model->del(array('link_id' => $friend_id));
        sys_msg('操作成功',2,array(array('href'=>'friend/index','text'=>'返回列表页')));
    }
    
}
?>
