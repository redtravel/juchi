<?php

/**
 * 
 */
class Address extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->time = date('Y-m-d H:i:s');
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('address_model');
        $this->load->model('region_model');
        $this->load->model('user_model');
        $this->load->library('user_obj');
    }

    //PC 收货地区 获取
    public function ajax_region(){
        $type = intval($this->input->get('type'))+1;
        $parent_id = intval($this->input->get('parent_id'));
        $arr = $this->region_model->all_region(array('region_type'=> $type , 'parent_id' => $parent_id));
        echo json_encode(array('list'=>$arr,'type'=>$type));
    }

    //PC 收货地址 新增与修改
    public function address_check() {
        if ($this->user_obj->is_login())
        {
            $user_id = $this->session->userdata('user_id');
        } else
        {
            echo json_encode(array('error'=>1,'msg'=>'您还未登陆'));
            return;
        }
        $address_id = $this->input->post('address_id');
        $param = array();
        $param['user_id'] = $this->session->userdata('user_id');
        if(empty($address_id)){
            $param['province'] = trim($this->input->post('province'));
            $param['city'] = trim($this->input->post('city'));
            $param['district']  =  trim($this->input->post('district'));
        }else{
            $param['province'] = trim($this->input->post('edit-province'));
            $param['city'] = trim($this->input->post('edit-city'));
            $param['district']  =  trim($this->input->post('edit-district'));
        }
        $param['address']  =  trim($this->input->post('address'));
        $param['consignee']  =  trim($this->input->post('consignee'));
        $param['mobile']  =  trim($this->input->post('mobile'));
        $param['is_used']  =  $this->input->post('is_used');
        $param['create_admin'] = $this->session->userdata('user_id');
        $param['create_date'] = $this->time;

        //判断是否是默认地址 
        if(!empty($param['is_used'])){
            $param['is_used'] = 1;
        }else{
            $param['is_used'] = 0;
        }

        //写入数据db
        if(empty($address_id)){
            // 如果为空，则插入新纪录
            $v_address_id = $this->address_model->address_insert($param);
            if(!empty($v_address_id)){
                echo json_encode(array('error' => 0));
            }else{
                echo json_encode(array('error'=>1,'msg' => '收货地址添加失败！'));
            }
        }else{
            // 否则，更新一条记录
            $update_num = $this->address_model->address_update($param,$address_id);
            if(!empty($update_num)){
                echo json_encode(array('error' => 0));
            }else{
                echo json_encode(array('error'=>1,'msg' => '收货地址更新失败！'));
            }
        }    
    }

    //PC 修改的地址
    public function v_address_form() {
        $user_id = $this->session->userdata('user_id');
        $address_id = intval($this->input->post('address_id'));
        if ($address_id) {
            $data['address'] = $this->address_model->all_address(array('address_id' => $address_id, 'user_id' => $user_id));          
        }

        $data['province'] = $this->region_model->all_region(array('region_type'=>1, 'parent_id' => 1));
        $data['city'] = $this->region_model->all_region(array('region_type'=>2, 'parent_id' => $data['address']->province));
        $data['district'] = $this->region_model->all_region(array('region_type'=>3, 'parent_id' => $data['address']->city));

        $html = $this->load->view('user/address_edit', $data, true);
        print json_encode(array('err' => 0, 'msg' => '', 'html' => $html));
    }
    
    //PC 收货地址删除
    public function address_delete() {
        $address_id = intval($this->input->get('address_id'));
        $delete_num = $this->address_model->delete_address($address_id);
        if(!empty($delete_num)){
            echo json_encode(array('error' => 0));
        }else{
            echo json_encode(array('error' => 0));
        }
    }
    
    //PC 收货地址 设置默认
    public function address_default(){
        $address_id = intval($this->input->get('address_id'));
        $user_id = $this->session->userdata('user_id');
        $this->user_model->update_address_used($address_id,$user_id);
	    $this->user_model->update(array('address_id'=>$address_id),$user_id);
        echo json_encode(array('error' => 0, 'msg' => '设置成功'));
    }

}
