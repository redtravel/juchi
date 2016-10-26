<?php
class Memcache_key extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('memcache_key_model');

    }
    public function index() {
        auth(array('memcache_key_index'));

        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("key","name");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->memcache_key_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('memcache_key/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }

        //管理员信息
        $this->load->model('admin_model');
        $data['all_admin'] = $this->admin_model->all_admin();

        $data['full_page'] = TRUE;
        $this->load->view('memcache_key/index', $data);
    }

    public function key_update($key_id) {
        auth('memcache_key_update');
        $row = $this->memcache_key_model->filter(array('id'=>$key_id));

        require_once( ROOT_PATH.$row->file_path);
        $className = $row->class;

        $class = new $className();
        $function = $row->function;

        $class->$function();

        sys_msg('操作成功', 2, array(array('href' => 'memcache_key/index', 'text' => '返回列表页')));
    }

}

?>