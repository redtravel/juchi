<?php

class Cps extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('cps_model');
    }

    public function index() {
        auth(array('cps_edit', 'cps_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        $cps_name = $this->input->post("cps_name");
        if (!empty($cps_name))
            $filter['cps_name'] = $cps_name;
        $cps_sn = $this->input->post("cps_sn");
        if (!empty($cps_sn))
            $filter['cps_sn'] = $cps_sn;

        $filter = get_pager_param($filter);
        $data = $this->cps_model->list_f($filter);
        $this->load->model('admin_model');
        $this->load->vars('all_admin', $this->admin_model->all_admin());
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('cps/list', $data, TRUE);
            $data['error'] = 0;
            unset($data['list']);
            echo json_encode($data);
            return;
        }
        $data['full_page'] = TRUE;
        $this->load->view('cps/list', $data);
    }

    public function add() {
        auth('cps_edit');
        $this->load->view('cps/add');
    }

    public function proc_add() {
        auth('cps_edit');
        $data['cps_sn'] = $this->input->post('cps_sn');
        $data['cps_name'] = $this->input->post('cps_name');
        $data['cps_cookie_time'] = $this->input->post('cps_cookie_time');
        $data['cps_start_time'] = $this->input->post('cps_start_time');
        $data['cps_shut_time'] = $this->input->post('cps_shut_time');
        $data['cps_status'] = $this->input->post('cps_status');
        $data['cps_shut_time'] = $this->input->post('cps_shut_time');
        $data['cps_status'] = $this->input->post('cps_status');
        $data['cps_data'] = $this->input->post('cps_data');
        $data['cps_rtn_script'] = $this->input->post('cps_rtn_script');
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s', time());
        $data['confirm_admin'] = $this->admin_id;
        $data['confirm_date'] = date('Y-m-d H:i:s', time());
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cps_sn', 'cps_sn', 'trim|required');
        $this->form_validation->set_rules('cps_name', 'cps_name', 'trim|required');
        $this->form_validation->set_rules('cps_cookie_time', 'cps_cookie_time', 'trim|required');
        $this->form_validation->set_rules('cps_status', 'cps_status', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $cps_id = $this->cps_model->insert($data);
        sys_msg('操作成功', 2, array(array('href' => 'cps/index', 'text' => '返回列表页')));
    }

    public function edit($cps_id) {
        auth('cps_edit');
        $friend_id = intval($cps_id);
        $check = $this->cps_model->filter(array('cps_id' => $cps_id));
        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->load->vars('cps', $check);
        $this->load->view('cps/edit');
    }

    public function proc_edit($cps_id) {
        auth('cps_edit');
        $cps_id = intval($cps_id);
        $data['cps_sn'] = $this->input->post('cps_sn');
        $data['cps_name'] = $this->input->post('cps_name');
        $data['cps_cookie_time'] = $this->input->post('cps_cookie_time');
        $data['cps_start_time'] = $this->input->post('cps_start_time');
        $data['cps_shut_time'] = $this->input->post('cps_shut_time');
        $data['cps_status'] = $this->input->post('cps_status');
        $data['cps_shut_time'] = $this->input->post('cps_shut_time');
        $data['cps_status'] = $this->input->post('cps_status');
        $data['cps_data'] = $this->input->post('cps_data');
        $data['cps_rtn_script'] = $this->input->post('cps_rtn_script');
        $data['confirm_admin'] = $this->admin_id;
        $data['confirm_date'] = date('Y-m-d H:i:s', time());
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cps_sn', 'cps_sn', 'trim|required');
        $this->form_validation->set_rules('cps_name', 'cps_name', 'trim|required');
        $this->form_validation->set_rules('cps_cookie_time', 'cps_cookie_time', 'trim|required');
        $this->form_validation->set_rules('cps_status', 'cps_status', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $this->cps_model->update($data, $cps_id);
        sys_msg('操作成功', 2, array(array('href' => 'cps/index', 'text' => '返回列表页')));
    }

    public function delete($cps_id) {
        auth('cps_del');
        $cps_id = intval($cps_id);
        $test = $this->input->post('test');
        $check = $this->cps_model->filter(array('cps_id' => $cps_id));
        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        if ($test)
            sys_msg('');
        $this->cps_model->del(array('cps_id' => $cps_id));
        sys_msg('操作成功', 2, array(array('href' => 'cps/index', 'text' => '返回列表页')));
    }

}

?>
