<?php

class Product_genre extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('product_genre_model');
        $this->load->model('settings_model');
    }
    public function index() {
        auth(array('product_genre_index'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("name","code","virtual","delivery");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->product_genre_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('product_genre/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }

        $data['fields_source']['virtual'] = get_yes_no();
        $data['fields_source_data']['virtual'] = $this->_to_js_json(get_yes_no());
        $data['fields_source']['delivery'] = get_yes_no();
        $data['fields_source_data']['delivery'] = $this->_to_js_json(get_yes_no());
        $data['full_page'] = TRUE;
        $this->load->view('product_genre/index', $data);
    }

    public function add() {
        auth('product_genre_add');
        $data = array();
        $client_conf = $this->settings_model->filter(array('config_code' => 'sys_order_client_map', 'type' => 3, 'storage_type' => 1));
        $product_conf = $this->settings_model->filter(array('config_code' => 'product_info_field_map', 'type' => 3, 'storage_type' => 1));
        
        if (empty($client_conf) || (!empty($client_conf) && empty($client_conf->config_value))) {
            $client_conf_val = array();
        } else {
            $client_conf_val = array_map("trim", array_unique(explode(",", $client_conf->config_value)));
        }
        
        if (empty($product_conf) || (!empty($product_conf) && empty($product_conf->config_value))) {
            $product_conf_val = array();
        } else {
            $product_conf_val = array_map("trim", array_unique(explode(",", $product_conf->config_value)));
        }
        $data['client_conf'] = $client_conf_val;
        $data['product_conf'] = $product_conf_val;
        $data['fields_source']['virtual'] = get_yes_no();
        $data['fields_source_data']['virtual'] = $this->_to_js_json(get_yes_no());
        $data['fields_source']['delivery'] = get_yes_no();
        $data['fields_source_data']['delivery'] = $this->_to_js_json(get_yes_no());
        $this->load->view('product_genre/add',$data);
    }

    public function proc_add() {
        auth('product_genre_edit');
        $this->load->library('form_validation');

        $data['name'] = $this->input->post('name');
        $this->form_validation->set_rules('name', 'name', 'trim|required');
        $data['code'] = $this->input->post('code');
        $this->form_validation->set_rules('code', 'code', 'trim|required');
        $data['virtual'] = $this->input->post('virtual');
        # $this->form_validation->set_rules('virtual', 'virtual', 'trim|required');
        $data['delivery'] = $this->input->post('delivery');
        # $this->form_validation->set_rules('delivery', 'delivery', 'trim|required');
        $data_tmp['product_field'] = $this->input->post('product_field');
        $data_tmp['product_val'] = $this->input->post('product_val');
        # $this->form_validation->set_rules('product_name_map', 'product_name_map', 'trim|required');
        $data_tmp['client_field'] = $this->input->post('client_field');
        $data_tmp['client_val'] = $this->input->post('client_val');
        # $this->form_validation->set_rules('client_info_map', 'client_info_map', 'trim|required');

        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $product_conf = array();
        foreach ($data_tmp['product_field'] as $k => $val) {
            if (empty($data_tmp['product_val'][$k])) {
                continue;
            }
            $product_conf[trim($val)] = trim($data_tmp['product_val'][$k]);
        }
        
        $client_conf = array();
        foreach ($data_tmp['client_field'] as $k => $val) {
            if (empty($data_tmp['client_val'][$k])) {
                continue;
            }
            $client_conf[trim($val)] = trim($data_tmp['client_val'][$k]);
        }        
        $data['product_name_map'] = json_encode($product_conf);
        $data['client_info_map'] = json_encode($client_conf);
        $pk_id = $this->product_genre_model->insert($data);
        sys_msg('操作成功', 2, array(array('href' => 'product_genre/index', 'text' => '返回列表页')));
    }

    public function edit($pk_id) {
        auth('product_genre_edit');
        $data = array();
        $pk_id = intval($pk_id);
        $check = $this->product_genre_model->filter(array('id' => $pk_id));
        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $client_conf = $this->settings_model->filter(array('config_code' => 'sys_order_client_map', 'type' => 3, 'storage_type' => 1));
        $product_conf = $this->settings_model->filter(array('config_code' => 'product_info_field_map', 'type' => 3, 'storage_type' => 1));
        
        if (empty($client_conf) || (!empty($client_conf) && empty($client_conf->config_value))) {
            $client_conf_val = array();
        } else {
            $client_conf_val = array_map("trim", array_unique(explode(",", $client_conf->config_value)));
        }
        
        if (empty($product_conf) || (!empty($product_conf) && empty($product_conf->config_value))) {
            $product_conf_val = array();
        } else {
            $product_conf_val = array_map("trim", array_unique(explode(",", $product_conf->config_value)));
        }
        $data['client_conf'] = $client_conf_val;
        $data['product_conf'] = $product_conf_val;

        $data['fields_source']['virtual'] = get_yes_no();
        $data['fields_source_data']['virtual'] = $this->_to_js_json(get_yes_no());
        $data['fields_source']['delivery'] = get_yes_no();
        $data['fields_source_data']['delivery'] = $this->_to_js_json(get_yes_no());
        $this->load->vars('row', $check);
        $this->load->view('product_genre/edit',$data);
    }

    public function proc_edit($pk_id) {
        auth('product_genre_edit');
        $this->load->library('form_validation');

        $data['name'] = $this->input->post('name');
        $this->form_validation->set_rules('name', 'name', 'trim|required');
        $data['code'] = $this->input->post('code');
        $this->form_validation->set_rules('code', 'code', 'trim|required');
        $data['virtual'] = $this->input->post('virtual');
        #$this->form_validation->set_rules('virtual', 'virtual', 'trim|required');
        $data['delivery'] = $this->input->post('delivery');
        #$this->form_validation->set_rules('delivery', 'delivery', 'trim|required');
        $data_tmp['product_field'] = $this->input->post('product_field');
        $data_tmp['product_val'] = $this->input->post('product_val');
        $data_tmp['client_field'] = $this->input->post('client_field');
        $data_tmp['client_val'] = $this->input->post('client_val');        
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $product_conf = array();
        foreach ($data_tmp['product_field'] as $k => $val) {
            if (empty($data_tmp['product_val'][$k])) {
                continue;
            }
            $product_conf[trim($val)] = trim($data_tmp['product_val'][$k]);
        }
        
        $client_conf = array();
        foreach ($data_tmp['client_field'] as $k => $val) {
            if (empty($data_tmp['client_val'][$k])) {
                continue;
            }
            $client_conf[trim($val)] = trim($data_tmp['client_val'][$k]);
        }        
        $data['product_name_map'] = json_encode($product_conf);
        $data['client_info_map'] = json_encode($client_conf);
        
        $this->product_genre_model->update($data, $pk_id);
        sys_msg('操作成功', 2, array(array('href' => 'product_genre/index', 'text' => '返回列表页')));
    }

    public function delete($pk_id) {
        auth('product_genre_delete');
        $pk_id = intval($pk_id);
        $check = $this->product_genre_model->filter(array('id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->product_genre_model->del(array('id' => $pk_id));
        sys_msg('操作成功', 2, array(array('href' => 'product_genre/index', 'text' => '返回列表页')));
    }

    public function editable() {
        if( ! auth('product_genre_editable'))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->product_genre_model->update( $data, $pk );
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
