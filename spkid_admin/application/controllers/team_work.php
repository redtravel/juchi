<?php

class Team_work extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->team_type = array( 0 => '口腔医疗商品', 1 => '口腔医学培训', 2 => '新牙医同盟会', 3 => '其他'); 
        $this->load->model('team_work_model');
    }
    public function index() {
        auth(array('team_work_index'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("team_type","team_company","team_name");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->team_work_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('team_work/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }

        $data['full_page'] = TRUE;
        //合作属性
        $data['team_type'] = $this->team_type;

        $this->load->view('team_work/index', $data);
    }

    public function add() {
        auth('team_work_add');
        $data = array();
        //合作属性
        $this->load->vars('team_type', $this->team_type);

        $this->load->view('team_work/add',$data);
    }

    public function proc_add() {
        auth('team_work_edit');
        $this->load->library('form_validation');

        $data['team_type'] = $this->input->post('team_type');
        $this->form_validation->set_rules('team_type', 'team_type', 'trim|required');
        $data['team_company'] = $this->input->post('team_company');
        $this->form_validation->set_rules('team_company', 'team_company', 'trim|required');
        $data['team_name'] = $this->input->post('team_name');
        $this->form_validation->set_rules('team_name', 'team_name', 'trim|required');
        $data['team_tel'] = $this->input->post('team_tel');
        $this->form_validation->set_rules('team_tel', 'team_tel', 'trim|required');
        $data['team_email'] = $this->input->post('team_email');
        $this->form_validation->set_rules('team_email', 'team_email', 'trim|required');

        if (!$this->form_validation->run()) {
           sys_msg(validation_errors(), 1);
        }
        $pk_id = $this->team_work_model->insert($data);
        sys_msg('操作成功', 2, array(array('href' => 'team_work/index', 'text' => '返回列表页')));
    }

    public function edit($pk_id) {
        auth('team_work_edit');
        $data = array();
        $pk_id = intval($pk_id);
        $check = $this->team_work_model->filter(array('team_id' => $pk_id));
        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->load->vars('row', $check);
        $this->load->vars('team_type', $this->team_type);
        $this->load->view('team_work/edit',$data);
    }

    public function proc_edit($pk_id) {
        auth('team_work_edit');
        $this->load->library('form_validation');

        $data['team_type'] = $this->input->post('team_type');
        $this->form_validation->set_rules('team_type', 'team_type', 'trim|required');
        $data['team_company'] = $this->input->post('team_company');
        $this->form_validation->set_rules('team_company', 'team_company', 'trim|required');
        $data['team_name'] = $this->input->post('team_name');
        $this->form_validation->set_rules('team_name', 'team_name', 'trim|required');
        $data['team_tel'] = $this->input->post('team_tel');
        $this->form_validation->set_rules('team_tel', 'team_tel', 'trim|required');
        $data['team_email'] = $this->input->post('team_email');
        $this->form_validation->set_rules('team_email', 'team_email', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $this->team_work_model->update($data, $pk_id);
        sys_msg('操作成功', 2, array(array('href' => 'team_work/index', 'text' => '返回列表页')));
    }

    public function delete($pk_id) {
        auth('team_work_delete');
        $pk_id = intval($pk_id);
        $check = $this->team_work_model->filter(array('team_id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->team_work_model->del(array('team_id' => $pk_id));
        sys_msg('操作成功', 2, array(array('href' => 'team_work/index', 'text' => '返回列表页')));
    }

    public function editable() {
        if( ! auth(''))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->team_work_model->update( $data, $pk );
        die(json_encode(Array('success'=>true,'msg'=>'操作成功！', 'value'=>443)));
       
    }        

         /**
          * 将一维数组(key=>value)对应样子的，生成可以editable的select 数据源
          */
        function _to_js_json( $ary ){
            $tmp = array();
            foreach( $ary AS $key => $value )
                $tmp[] = '{value:"'.$key.'",text:"'.$value.'"}';
            $tmp = implode(',',$tmp);
            return '['.$tmp.'];';
        }

}

?>