<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Mail_template extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('mail_template_model');
    }

    public function index ()
    {
        auth(array('mail_template_edit','mail_template_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_mail_template_perm());
        $filter = $this->uri->uri_to_assoc(3);
        $is_html = $this->input->post("is_html");
        if(!empty($is_html)) $filter['is_html'] = $is_html;

        $filter = get_pager_param($filter);
        $data = $this->mail_template_model->t_list($filter);

        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('mail_template/list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('mail_template/list', $data);
    }


    public function add()
    {
        auth(array('mail_template_edit'));
        $this->load->library('ckeditor');
        $this->load->view('mail_template/add');
    }

    public function proc_add()
    {
        auth(array('mail_template_edit'));
        $data['template_code'] = $this->input->post('template_code');
        $data['template_name'] = $this->input->post('template_name');
        $data['is_html'] = $this->input->post('is_html');
        $data['template_subject'] = $this->input->post('template_subject');
        $data['template_content'] = $this->input->post('template_content');
        $data['sms_content'] = $this->input->post('sms_content');
        $data['template_priority'] = $this->input->post('template_priority');
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('template_code', 'template_code', 'trim|required');
        $this->form_validation->set_rules('template_name', 'template_name', 'trim|required');
        $this->form_validation->set_rules('template_priority', 'template_priority', 'trim|required');
        $this->form_validation->set_rules('template_subject', 'template_subject', 'trim|required');
        
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->mail_template_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'/mail_template/index','text'=>'返回列表页')));
    }

    public function edit($template_id)
    {
        auth(array('mail_template_edit','mail_template_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_mail_template_perm());
        $template_id = intval($template_id);
        $check = $this->mail_template_model->filter(array('template_id' => $template_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->vars('arr' , $check);
        $this->load->library('ckeditor');
        $this->load->view('mail_template/edit');
    }

    public function proc_edit($template_id)
    {
        auth(array('mail_template_edit'));
        $template_id = intval($template_id);
        $data['template_code'] = $this->input->post('template_code');
        $data['template_name'] = $this->input->post('template_name');
        $data['is_html'] = $this->input->post('is_html');
        $data['template_subject'] = $this->input->post('template_subject');
        $data['template_content'] = $this->input->post('template_content');
        $data['sms_content'] = $this->input->post('sms_content');
        $data['template_priority'] = $this->input->post('template_priority');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('template_code', 'template_code', 'trim|required');
        $this->form_validation->set_rules('template_name', 'template_name', 'trim|required');
        $this->form_validation->set_rules('template_priority', 'template_priority', 'trim|required');
        $this->form_validation->set_rules('template_subject', 'template_subject', 'trim|required');
        
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->mail_template_model->update($data , $template_id);
        sys_msg('操作成功',2,array(array('href'=>'/mail_template/index','text'=>'返回列表页')));
    }
    
    public function del($template_id)
    {
        auth(array('mail_template_edit'));
        $template_id = intval($template_id);
        $test = $this->input->post('test');
        $check = $this->mail_template_model->filter(array('template_id' => $template_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        if($test) sys_msg('');
        $this->mail_template_model->delete($template_id);
        sys_msg('删除成功',2,array(array('href'=>'/mail_template/index','text'=>'返回列表页')));
    }

}