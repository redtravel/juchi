<?php

class Dict extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('dict_model');
    }
    public function index() {
        auth(array('dict_index'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("id","dict_id","field_id","field_value1","field_value2");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->dict_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('dict/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }

        $data['fields_source']['dict_id'] = $GLOBALS['dict_types'];
        $data['fields_source_data']['dict_id'] = $this->_to_js_json($GLOBALS['dict_types']);
        $data['full_page'] = TRUE;
        $this->load->view('dict/index', $data);
    }

    public function add() {
        auth('dict_add');
        $data = array();

        $data['fields_source']['dict_id'] = $GLOBALS['dict_types'];
        $data['fields_source_data']['dict_id'] = $this->_to_js_json($GLOBALS['dict_types']);
        $this->load->view('dict/add',$data);
    }

    public function proc_add() {
        auth('dict_edit');
        #$this->load->library('form_validation');

        $data['dict_id'] = $this->input->post('dict_id');
        # $this->form_validation->set_rules('dict_id', 'dict_id', 'trim|required');
        $data['field_id'] = $this->input->post('field_id');
        # $this->form_validation->set_rules('field_id', 'field_id', 'trim|required');
        $data['field_value1'] = $this->input->post('field_value1');
        # $this->form_validation->set_rules('field_value1', 'field_value1', 'trim|required');
        $data['field_value2'] = $this->input->post('field_value2');
        # $this->form_validation->set_rules('field_value2', 'field_value2', 'trim|required');

        #if (!$this->form_validation->run()) {
        #    sys_msg(validation_errors(), 1);
        #}
        $pk_id = $this->dict_model->insert($data);
        sys_msg('操作成功', 2, array(array('href' => 'dict/index', 'text' => '返回列表页')));
    }

    public function edit($pk_id) {
        auth('dict_edit');
        $data = array();
        $pk_id = intval($pk_id);
        $check = $this->dict_model->filter(array('id' => $pk_id));
        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }

        $data['fields_source']['dict_id'] = $GLOBALS['dict_types'];
        $data['fields_source_data']['dict_id'] = $this->_to_js_json($GLOBALS['dict_types']);
        $this->load->vars('row', $check);
        $this->load->view('dict/edit',$data);
    }

    public function proc_edit($pk_id) {
        auth('dict_edit');
        $this->load->library('form_validation');

        $data['dict_id'] = $this->input->post('dict_id');
        #$this->form_validation->set_rules('dict_id', 'dict_id', 'trim|required');
        $data['field_id'] = $this->input->post('field_id');
        #$this->form_validation->set_rules('field_id', 'field_id', 'trim|required');
        $data['field_value1'] = $this->input->post('field_value1');
        #$this->form_validation->set_rules('field_value1', 'field_value1', 'trim|required');
        $data['field_value2'] = $this->input->post('field_value2');
        #$this->form_validation->set_rules('field_value2', 'field_value2', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $this->dict_model->update($data, $pk_id);
        sys_msg('操作成功', 2, array(array('href' => 'dict/index', 'text' => '返回列表页')));
    }

    public function delete($pk_id) {
        auth('dict_delete');
        $pk_id = intval($pk_id);
        $check = $this->dict_model->filter(array('id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->dict_model->del(array('id' => $pk_id));
        sys_msg('操作成功', 2, array(array('href' => 'dict/index', 'text' => '返回列表页')));
    }

    public function editable() {
        if( ! auth('dict_editable'))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->dict_model->update( $data, $pk );
        die(json_encode(Array('success'=>true,'msg'=>'操作成功！', 'value'=>443)));
       
    }        

        function _to_js_json( $ary ){
            $tmp = array();
            foreach( $ary AS $key => $value )
                $tmp[] = '{value:"'.$key.'",text:"'.$value.'"}';
            $tmp = implode(',',$tmp);
            return '['.$tmp.'];';
        }

}

?>