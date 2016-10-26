<?php

class Weixin_settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        $this->admin_name = $this->session->userdata('admin_name');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('weixin_settings_model');
        $this->load->library('memcache');
    }
    public function index() {
        auth(array('weixin_settings_index'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("key_code");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->weixin_settings_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('weixin_settings/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('weixin_settings/index', $data);
    }

    public function add() {
        auth('weixin_settings_add');
        $data = array();

        $this->load->view('weixin_settings/add',$data);
    }

    public function proc_add() {
        auth('weixin_settings_edit');
        $this->load->library('form_validation');
        $this->load->library('upload');

        $data['key_code'] = trim($this->input->post('key_code'));
        $this->form_validation->set_rules('key_code', 'key_code', 'trim|required');
        $data['title'] = $this->input->post('title');
        $this->form_validation->set_rules('title', 'title', 'trim|required');
        $data['describe'] = $this->input->post('describe');
        $this->form_validation->set_rules('describe', 'describe', 'trim|required');
        //$data['file_url'] = $this->input->post('file_url');
        //$this->form_validation->set_rules('file_url', 'file_url', 'trim|required');
        $data['create_date'] = date('Y-m-d H:i:s');
        $data['create_admin'] = $this->admin_name;

        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        
        // 上传banner
        $this->upload->initialize(array(
                        'upload_path' => CREATE_HTML_PATH.'weixin/',
                        'allowed_types' => 'jpg|gif|png',
                        'encrypt_name' => TRUE
                ));
        if ($this->upload->do_upload('file_url')) {
            $file = $this->upload->data();
            $data['file_url'] = 'weixin/'.$file['file_name'];
        }
        $mem_val = serialize(array('title' => $data['title'], 'describe' => $data['describe'], 'file_url' => $data['file_url']));
        $this->memcache->save($data['key_code'], $mem_val, WEIXIN_CACHE_TIME);
        $pk_id = $this->weixin_settings_model->insert($data);
        sys_msg('操作成功', 2, array(array('href' => 'weixin_settings/index', 'text' => '返回列表页')));
    }

    public function edit($pk_id) {
        auth('weixin_settings_edit');
        $data = array();
        $pk_id = intval($pk_id);
        $check = $this->weixin_settings_model->filter(array('id' => $pk_id));
        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }

        $this->load->vars('row', $check);
        $this->load->view('weixin_settings/edit',$data);
    }

    public function proc_edit($pk_id) {
        auth('weixin_settings_edit');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $pk_id = intval($pk_id);
        $check = $this->weixin_settings_model->filter(array('id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        
        $data['key_code'] = trim($this->input->post('key_code'));
        $this->form_validation->set_rules('key_code', 'key_code', 'trim|required');
        $data['title'] = $this->input->post('title');
        $this->form_validation->set_rules('title', 'title', 'trim|required');
        $data['describe'] = $this->input->post('describe');
        $this->form_validation->set_rules('describe', 'describe', 'trim|required');
        //$data['file_url'] = $this->input->post('file_url');
        //$this->form_validation->set_rules('file_url', 'file_url', 'trim|required');
        $data['update_date'] = date('Y-m-d H:i:s');
        $data['update_admin'] = $this->admin_name;
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        
        // 上传banner
        $this->upload->initialize(array(
                        'upload_path' => CREATE_HTML_PATH.'weixin/',
                        'allowed_types' => 'jpg|gif|png',
                        'encrypt_name' => TRUE
                ));
        if ($this->upload->do_upload('file_url')) {
            $file = $this->upload->data();
            if (!empty($check->file_url)) @unlink(CREATE_HTML_PATH.$check->file_url);
            $data['file_url'] = 'weixin/'.$file['file_name'];
        }
        $mem_val = serialize(array('title' => $data['title'], 'describe' => $data['describe'], 'file_url' => $data['file_url']));
        $this->memcache->save($data['key_code'], $mem_val, WEIXIN_CACHE_TIME);
        $this->weixin_settings_model->update($data, $pk_id);
        sys_msg('操作成功', 2, array(array('href' => 'weixin_settings/index', 'text' => '返回列表页')));
    }

    public function delete($pk_id) {
        auth('weixin_settings_delete');
        $pk_id = intval($pk_id);
        $check = $this->weixin_settings_model->filter(array('id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }

        if (!empty($check->file_url)) @unlink(CREATE_HTML_PATH.$check->file_url);
        $this->memcache->delete($check->key_code); // delete key first
        $this->weixin_settings_model->del(array('id' => $pk_id));
        sys_msg('操作成功', 2, array(array('href' => 'weixin_settings/index', 'text' => '返回列表页')));
    }

    public function editable() {
        if( ! auth(''))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->weixin_settings_model->update( $data, $pk );
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