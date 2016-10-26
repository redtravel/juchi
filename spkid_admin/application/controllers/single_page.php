<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Single_page extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('single_page_model');
    }

    public function index ()
    {
        auth(array('single_page_edit','single_page_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_single_page_perm());
        $filter = $this->uri->uri_to_assoc(3);
        $is_use = $this->input->post("is_use");
        if(!empty($is_use)) $filter['is_use'] = $is_use;

        $filter = get_pager_param($filter);
        $data = $this->single_page_model->sg_list($filter);

        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('single_page/list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('single_page/list', $data);
    }

    public function del($single_id)
    {
        auth(array('single_page_edit'));
        $single_id = intval($single_id);
        $test = $this->input->post('test');
        $check = $this->single_page_model->filter(array('single_id' => $single_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        if($test) sys_msg('');
        $this->single_page_model->delete($single_id);
        sys_msg('删除成功',2,array(array('href'=>'/single_page/index','text'=>'返回列表页')));
    }


    public function add()
    {
        auth(array('single_page_edit'));
        $this->load->library('ckeditor');
        $this->load->view('single_page/add');
    }

    public function proc_add()
    {
        auth(array('single_page_edit'));
        $data['page_name'] = $this->input->post('page_name');
        $data['page_content'] = $this->input->post('page_content');
        $data['is_use'] = $this->input->post('is_use');
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('page_name', 'page_name', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->single_page_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'/single_page/index','text'=>'返回列表页')));
    }

    public function edit($single_id)
    {
        auth(array('single_page_edit','single_page_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_single_page_perm());
        $single_id = intval($single_id);
        $check = $this->single_page_model->filter(array('single_id' => $single_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->vars('arr' , $check);
        $this->load->library('ckeditor');
        $this->load->view('single_page/edit');
    }

    public function proc_edit($single_id)
    {
        auth(array('single_page_edit'));
        $single_id = intval($single_id);
        $data['page_name'] = $this->input->post('page_name');
        $data['page_content'] = $this->input->post('page_content');
        $data['is_use'] = $this->input->post('is_use');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('page_name', 'page_name', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->single_page_model->update($data , $single_id);
        sys_msg('操作成功',2,array(array('href'=>'/single_page/index','text'=>'返回列表页')));
    }
    
    
}