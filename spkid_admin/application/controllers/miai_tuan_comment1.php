<?php

class Miai_tuan_comment1 extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('miai_tuan_comment1_model');
    }
    public function index() {
        auth(array('miai_tuan_comment1_index'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("wechat_nickname","tuan_id","wechat_date","register_name","register_mobile","register_date");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->miai_tuan_comment1_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('miai_tuan_comment1/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('miai_tuan_comment1/index', $data);
    }





    public function delete($pk_id) {
        auth('');
        $pk_id = intval($pk_id);
        $check = $this->miai_tuan_comment1_model->filter(array('wechat_id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->miai_tuan_comment1_model->del(array('wechat_id' => $pk_id));
        sys_msg('操作成功', 2, array(array('href' => 'miai_tuan_comment1/index', 'text' => '返回列表页')));
    }

    public function editable() {
        if( ! auth(''))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->miai_tuan_comment1_model->update( $data, $pk );
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