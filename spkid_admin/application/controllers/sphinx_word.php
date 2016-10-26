<?php

class Sphinx_word extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('sphinx_word_model');
    }
    public function index() {
        auth(array('sphinx_word_index'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("id","name");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->sphinx_word_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('sphinx_word/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('sphinx_word/index', $data);
    }

    public function add() {
        auth('sphinx_word_add');
        $data = array();

        $this->load->view('sphinx_word/add',$data);
    }

    public function proc_add() {
        auth('sphinx_word_edit');
        #$this->load->library('form_validation');

        $data['name'] = $this->input->post('name');
        # $this->form_validation->set_rules('name', 'name', 'trim|required');

        #if (!$this->form_validation->run()) {
        #    sys_msg(validation_errors(), 1);
        #}
        $pk_id = $this->sphinx_word_model->insert($data);
        sys_msg('操作成功', 2, array(array('href' => 'sphinx_word/index', 'text' => '返回列表页')));
    }

    public function edit($pk_id) {
        auth('sphinx_word_edit');
        $data = array();
        $pk_id = intval($pk_id);
        $check = $this->sphinx_word_model->filter(array('id' => $pk_id));
        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }

        $this->load->vars('row', $check);
        $this->load->view('sphinx_word/edit',$data);
    }

    public function proc_edit($pk_id) {
        auth('sphinx_word_edit');
        $this->load->library('form_validation');

        $data['name'] = $this->input->post('name');
        #$this->form_validation->set_rules('name', 'name', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $this->sphinx_word_model->update($data, $pk_id);
        sys_msg('操作成功', 2, array(array('href' => 'sphinx_word/index', 'text' => '返回列表页')));
    }

    public function delete($pk_id) {
        auth('sphinx_word_delete');
        $pk_id = intval($pk_id);
        $check = $this->sphinx_word_model->filter(array('id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->sphinx_word_model->del(array('id' => $pk_id));
        sys_msg('操作成功', 2, array(array('href' => 'sphinx_word/index', 'text' => '返回列表页')));
    }

    public function editable() {
        if( ! auth('sphinx_word_editable'))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->sphinx_word_model->update( $data, $pk );
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
