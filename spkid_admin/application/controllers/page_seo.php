<?php

class Page_seo extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('page_seo_model');
    }
    public function index() {
        auth(array('page_seo_index'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("code","name");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->page_seo_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('page_seo/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }
        $data['full_page'] = TRUE;
        $this->load->view('page_seo/index', $data);
    }

    public function add() {
        auth('page_seo_add');
        $this->load->view('page_seo/add');
    }

    public function proc_add() {
        auth('page_seo_add');

        $data['code'] = $this->input->post('code');
        $data['name'] = $this->input->post('name');
        $data['title'] = $this->input->post('title');
        $data['keywords'] = $this->input->post('keywords');
        $data['description'] = $this->input->post('description');
        $data['add_aid'] = $this->admin_id;  //添加人
        $data['add_time'] = date('Y-m-d H:i:s',time());

        $pk_id = $this->page_seo_model->insert($data);
        sys_msg('操作成功', 2, array(array('href' => 'page_seo/index', 'text' => '返回列表页')));
    }

    public function delete($pk_id) {
        auth('');
        $pk_id = intval($pk_id);
        $check = $this->page_seo_model->filter(array('id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->page_seo_model->del(array('id' => $pk_id));
        sys_msg('操作成功', 2, array(array('href' => 'page_seo/index', 'text' => '返回列表页')));
    }

    public function editable() {
        if( ! auth('page_seo_editable'))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $data['update_aid'] = $this->admin_id;  //更新人
        $data['update_time'] = date('Y-m-d H:i:s',time());
        $result = $this->page_seo_model->update( $data, $pk );
        die(json_encode(Array('success'=>true,'msg'=>'操作成功！', 'value'=>443)));
       
    }        


}

?>