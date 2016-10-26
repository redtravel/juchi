<?php

class Wechat_info extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('wechat_info_model');
    }
    public function index() {
        auth(array('wechat_info_index'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("wechat_nickname","tuan_id","wechat_date","register_name","register_mobile","register_date");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->wechat_info_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('wechat_info/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('wechat_info/index', $data);
    }

    public function add() {
        auth('wechat_info_add');
        $data = array();

        $this->load->view('wechat_info/add',$data);
    }

    public function proc_add() {
        auth('wechat_info_edit');
        $this->load->library('form_validation');

        $data['wechat_openid'] = $this->input->post('wechat_openid');
        $this->form_validation->set_rules('wechat_openid', 'wechat_openid', 'trim|required');
        $data['wechat_nickname'] = $this->input->post('wechat_nickname');
        $this->form_validation->set_rules('wechat_nickname', 'wechat_nickname', 'trim|required');
        $data['wechat_sex'] = $this->input->post('wechat_sex');
        $this->form_validation->set_rules('wechat_sex', 'wechat_sex', 'trim|required');
        $data['wechat_city'] = $this->input->post('wechat_city');
        $this->form_validation->set_rules('wechat_city', 'wechat_city', 'trim|required');
        $data['wechat_province'] = $this->input->post('wechat_province');
        $this->form_validation->set_rules('wechat_province', 'wechat_province', 'trim|required');
        $data['wechat_country'] = $this->input->post('wechat_country');
        $this->form_validation->set_rules('wechat_country', 'wechat_country', 'trim|required');
        $data['wechat_headimgurl'] = $this->input->post('wechat_headimgurl');
        $this->form_validation->set_rules('wechat_headimgurl', 'wechat_headimgurl', 'trim|required');
        $data['tuan_id'] = $this->input->post('tuan_id');
        $this->form_validation->set_rules('tuan_id', 'tuan_id', 'trim|required');
        $data['wechat_date'] = $this->input->post('wechat_date');
        $this->form_validation->set_rules('wechat_date', 'wechat_date', 'trim|required');
	$wechat_day = $this->input->post('wechat_day');
        $this->form_validation->set_rules('wechat_day', 'wechat_day', 'trim|required');
	$data['wechat_date'] = $data['wechat_date'].' '.$wechat_day;
        $data['register_name'] = $this->input->post('register_name');
        $this->form_validation->set_rules('register_name', 'register_name', 'trim|required');
        $data['register_mobile'] = $this->input->post('register_mobile');
        $this->form_validation->set_rules('register_mobile', 'register_mobile', 'trim|required');
        $data['register_num'] = $this->input->post('register_num');
        $this->form_validation->set_rules('register_num', 'register_num', 'trim|required');
        $data['register_date'] = $this->input->post('register_date');
        $this->form_validation->set_rules('register_date', 'register_date', 'trim|required');
	$register_day = $this->input->post('register_day');
        $this->form_validation->set_rules('register_day', 'register_day', 'trim|required');
	$data['register_date'] = $data['register_date'].' '.$register_day;

        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $pk_id = $this->wechat_info_model->insert($data);
        sys_msg('操作成功', 2, array(array('href' => 'wechat_info/index', 'text' => '返回列表页')));
    }

    public function edit($pk_id) {
        auth('wechat_info_edit');
        $data = array();
        $pk_id = intval($pk_id);
        $check = $this->wechat_info_model->filter(array('wechat_id' => $pk_id));
        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }

        $this->load->vars('row', $check);
        $this->load->view('wechat_info/edit',$data);
    }

    public function proc_edit() {
        auth('wechat_info_edit');
        $this->load->library('form_validation');
	$wechat_id = $this->input->post('wechat_id');
        $data['wechat_openid'] = $this->input->post('wechat_openid');
        $this->form_validation->set_rules('wechat_openid', 'wechat_openid', 'trim|required');
        $data['wechat_nickname'] = $this->input->post('wechat_nickname');
        $this->form_validation->set_rules('wechat_nickname', 'wechat_nickname', 'trim|required');
        $data['wechat_sex'] = $this->input->post('wechat_sex');
        $this->form_validation->set_rules('wechat_sex', 'wechat_sex', 'trim|required');
        $data['wechat_city'] = $this->input->post('wechat_city');
        $this->form_validation->set_rules('wechat_city', 'wechat_city', 'trim|required');
        $data['wechat_province'] = $this->input->post('wechat_province');
        $this->form_validation->set_rules('wechat_province', 'wechat_province', 'trim|required');
        $data['wechat_country'] = $this->input->post('wechat_country');
        $this->form_validation->set_rules('wechat_country', 'wechat_country', 'trim|required');
        $data['wechat_headimgurl'] = $this->input->post('wechat_headimgurl');
        $this->form_validation->set_rules('wechat_headimgurl', 'wechat_headimgurl', 'trim|required');
        $data['tuan_id'] = $this->input->post('tuan_id');
        $this->form_validation->set_rules('tuan_id', 'tuan_id', 'trim|required');
        $data['wechat_date'] = $this->input->post('wechat_date');
        $this->form_validation->set_rules('wechat_date', 'wechat_date', 'trim|required');
	$wechat_day = $this->input->post('wechat_day');
        $this->form_validation->set_rules('wechat_day', 'wechat_day', 'trim|required');
	$data['wechat_date'] = $data['wechat_date'].' '.$wechat_day;
        $data['register_name'] = $this->input->post('register_name');
        $this->form_validation->set_rules('register_name', 'register_name', 'trim|required');
        $data['register_mobile'] = $this->input->post('register_mobile');
        $this->form_validation->set_rules('register_mobile', 'register_mobile', 'trim|required');
        $data['register_num'] = $this->input->post('register_num');
        $this->form_validation->set_rules('register_num', 'register_num', 'trim|required');
        $data['register_date'] = $this->input->post('register_date');
        $this->form_validation->set_rules('register_date', 'register_date', 'trim|required');
	$register_day = $this->input->post('register_day');
        $this->form_validation->set_rules('register_day', 'register_day', 'trim|required');
	$data['register_date'] = $data['register_date'].' '.$register_day;
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $this->wechat_info_model->update($data, $wechat_id);
        sys_msg('操作成功', 2, array(array('href' => 'wechat_info/index', 'text' => '返回列表页')));
    }

    public function delete($pk_id) {
        auth('wechat_info_delete');
        $pk_id = intval($pk_id);
        $check = $this->wechat_info_model->filter(array('wechat_id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->wechat_info_model->del(array('wechat_id' => $pk_id));
        sys_msg('操作成功', 2, array(array('href' => 'wechat_info/index', 'text' => '返回列表页')));
    }

    public function editable() {
        if( ! auth(''))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->wechat_info_model->update( $data, $pk );
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