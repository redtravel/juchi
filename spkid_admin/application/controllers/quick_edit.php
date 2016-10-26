<?php
/**
 * for jquery plugin x editable, popup/inline editor.
 * 2015/09/01
 */
class Quick_edit extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
    }
    // 编辑商品的包装方式
    public function pack_method(){
        if( ! check_perm(array('depotin_add','depotin_view')) ) die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $this->load->model('product_model');
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->product_model->update( $data, $pk );
        die(json_encode(Array('success'=>true,'msg'=>'操作成功！', 'value'=>443)));

    }

    // 权限编辑相关
    public function action() {
        if( ! check_perm(array('action_edit')) ) die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $this->load->model('action_model');
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->action_model->update( $data, $pk );
        die(json_encode(Array('success'=>true,'msg'=>'操作成功！', 'value'=>443)));
    }

    // 参数配置，编辑备注
    public function system_settings(){
        if( ! check_perm(array('system_settings')) ) die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $this->load->model('settings_model');
        $sys_store_types = Array(1=>'字符串',2=>'数字',3=>'数组');  // 存储类型
        
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        
        $settings = $this->settings_model->filter(array('id'=>$pk));
        if (!$settings) {
            sys_msg('记录不存在!', 1);
            die(json_encode(Array('success'=>false,'msg'=>'记录不存在！')));
        }
        $sys_store_types = array_keys($sys_store_types);
        if($settings->storage_type == $sys_store_types['1']){
            eval("\$value=$value;");
            $value = serialize($value);
        }
        $data[$name] = $value;
        $result = $this->settings_model->update( $data, $pk );
        die(json_encode(Array('success'=>true,'msg'=>'操作成功！', 'value'=>443)));

    }
}

?>
