<?php

class Mami_tuan_comment extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('mami_tuan_comment_model');
    }
    public function index() {
        auth(array('mami_tuan_comment_index'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("wechat_id","tuan_id","comment_date");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->mami_tuan_comment_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('mami_tuan_comment/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('mami_tuan_comment/index', $data);
    }

    public function add() {
        auth('mami_tuan_comment_add');
        $data = array();

        $this->load->view('mami_tuan_comment/add',$data);
    }

    public function proc_add() {
        auth('mami_tuan_comment_edit');
        $this->load->library('form_validation');

        $data['wechat_id'] = $this->input->post('wechat_id');
         $this->form_validation->set_rules('wechat_id', 'wechat_id', 'trim|required');
        $data['tuan_id'] = $this->input->post('tuan_id');
         $this->form_validation->set_rules('tuan_id', 'tuan_id', 'trim|required');
        $data['comment_content'] = $this->input->post('comment_content');
        $this->form_validation->set_rules('comment_content', 'comment_content', 'trim|required');
        $data['comment_date'] = $this->input->post('comment_date');
         $this->form_validation->set_rules('comment_date', 'comment_date', 'trim|required');

        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $pk_id = $this->mami_tuan_comment_model->insert($data);
        sys_msg('操作成功', 2, array(array('href' => 'mami_tuan_comment/index', 'text' => '返回列表页')));
    }

    public function edit($pk_id) {
        auth('mami_tuan_comment_edit');
        $data = array();
        $pk_id = intval($pk_id);
        $check = $this->mami_tuan_comment_model->filter(array('comment_id' => $pk_id));
        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }

        $this->load->vars('row', $check);
        $this->load->view('mami_tuan_comment/edit',$data);
    }

    public function proc_edit() {
        auth('mami_tuan_comment_edit');
        $this->load->library('form_validation');
	
	$comment_id = $this->input->post('comment_id');
        $data['wechat_id'] = $this->input->post('wechat_id');
        $this->form_validation->set_rules('wechat_id', 'wechat_id', 'trim|required');
        $data['tuan_id'] = $this->input->post('tuan_id');
        $this->form_validation->set_rules('tuan_id', 'tuan_id', 'trim|required');
        $data['comment_content'] = $this->input->post('comment_content');
        $this->form_validation->set_rules('comment_content', 'comment_content', 'trim|required');
        $data['comment_date'] = $this->input->post('comment_date');
        $this->form_validation->set_rules('comment_date', 'comment_date', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $this->mami_tuan_comment_model->update($data, $comment_id);
        sys_msg('操作成功', 2, array(array('href' => 'mami_tuan_comment/index', 'text' => '返回列表页')));
    }

    public function delete($pk_id) {
        auth('mami_tuan_comment_delete');
        $pk_id = intval($pk_id);
        $check = $this->mami_tuan_comment_model->filter(array('comment_id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->mami_tuan_comment_model->del(array('comment_id' => $pk_id));
        sys_msg('操作成功', 2, array(array('href' => 'mami_tuan_comment/index', 'text' => '返回列表页')));
    }

    public function editable() {
        if( ! auth(''))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->mami_tuan_comment_model->update( $data, $pk );
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