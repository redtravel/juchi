<?php

class Search_history extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('search_history_model');
    }
    public function index() {
        auth(array('search_history_index'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("id","keyword","count","created");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->search_history_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('search_history/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('search_history/index', $data);
    }
    public function addict(){
        if( !auth('search_history_editable'))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $kw = $this->input->get('keyword');
        $kw = str_replace(' ', '', $kw);
        $this->load->model('sphinx_word_model');
        $data = array('name' => $kw);
        $check = $this->sphinx_word_model->filter($data);
        if (empty($check)) {
            $this->sphinx_word_model->insert($data);
            $msg = '添加成功!';
        } else{
            $msg = '关键字已存在!';
        }
        echo json_encode(array('msg' => $msg));
    }


    public function delete($pk_id) {
        auth('search_history_delete');
        $pk_id = intval($pk_id);
        $check = $this->search_history_model->filter(array('id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->search_history_model->del(array('id' => $pk_id));
        sys_msg('操作成功', 2, array(array('href' => 'search_history/index', 'text' => '返回列表页')));
    }

    public function editable() {
        if( !auth('search_history_editable'))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->search_history_model->update( $data, $pk );
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
