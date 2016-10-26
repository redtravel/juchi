<?php

class Action extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('action_model');
    }

    public function index() {
        auth(array('action_edit', 'action_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array('action_code','action_name','parent_id','menu_name');
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->action_model->list_f($filter);
        $this->load->model('admin_model');
        $this->load->vars('all_admin', $this->admin_model->all_admin());
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('action/list', $data, TRUE);
            $data['error'] = 0;
            unset($data['list']);
            echo json_encode($data);
            return;
        }
        $data['full_page'] = TRUE;
        $this->load->view('action/list', $data);
    }

    public function add() {
        auth('action_edit');
        $this->load->view('action/add');
    }

    public function proc_add() {
        auth('action_edit');
        $data['parent_id'] = $this->input->post('parent_id');
        $data['action_code'] = $this->input->post('action_code');
        $data['action_name'] = $this->input->post('action_name');
        $data['menu_name'] = $this->input->post('menu_name');
        $data['url'] = $this->input->post('url');
        $data['sort_order'] = $this->input->post('sort_order');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('parent_id', 'parent_id', 'trim|required');
        $this->form_validation->set_rules('action_code', 'action_code', 'trim|required');
        $this->form_validation->set_rules('action_name', 'action_name', 'trim|required');
        $this->form_validation->set_rules('sort_order', 'sort_order', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $action_id = $this->action_model->insert($data);
        sys_msg('操作成功', 2, array(array('href' => 'action/index', 'text' => '返回列表页')));
    }

    public function edit($action_id) {
        auth('action_edit');
        $friend_id = intval($action_id);
        $check = $this->action_model->filter(array('action_id' => $action_id));
        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->load->vars('action', $check);
        $this->load->view('action/edit');
    }

    public function proc_edit($action_id) {
        auth('action_edit');
        $data['parent_id'] = $this->input->post('parent_id');
        $data['action_code'] = $this->input->post('action_code');
        $data['action_name'] = $this->input->post('action_name');
        $data['menu_name'] = $this->input->post('menu_name');
        $data['url'] = $this->input->post('url');
        $data['sort_order'] = $this->input->post('sort_order');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('parent_id', 'parent_id', 'trim|required');
        $this->form_validation->set_rules('action_code', 'action_code', 'trim|required');
        $this->form_validation->set_rules('action_name', 'action_name', 'trim|required');
        $this->form_validation->set_rules('sort_order', 'sort_order', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $this->action_model->update($data, $action_id);
        sys_msg('操作成功', 2, array(array('href' => 'action/index', 'text' => '返回列表页')));
    }

    public function delete($action_id) {
        auth('action_del');
        $action_id = intval($action_id);
        $test = $this->input->post('test');
        $check = $this->action_model->filter(array('action_id' => $action_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        if ($test)
            sys_msg('');
        $this->action_model->del(array('action_id' => $action_id));
        sys_msg('操作成功', 2, array(array('href' => 'action/index', 'text' => '返回列表页')));
    }

}

?>
