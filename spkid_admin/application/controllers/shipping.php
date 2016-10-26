<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Shipping extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('shipping_model');
        $this->time = date('Y-m-d H:i:s');
    }

    public function index ()
    {
        auth('shipping_view');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_shipping_perm());
        $list = $this->shipping_model->all_shipping();
        $this->load->vars('list',$list);
        $this->load->view('shipping/list');
    }

    public function add_shipping_info()
    {
       auth('shipping_edit');
       $this->load->view('shipping/add_shipping_info');
    }

    public function proc_add_shipping_info()
    {
        auth('shipping_edit');
        $data['shipping_code'] = trim($this->input->post('shipping_code'));
        $data['shipping_name'] = trim($this->input->post('shipping_name'));
        $data['shipping_desc'] = trim($this->input->post('shipping_desc'));
        $data['is_use'] = intval($this->input->post('is_use'));
        $data['sort_order'] = trim($this->input->post('sort_order'));
        $data['track_name'] = trim($this->input->post('track_name'));
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = $this->time;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('shipping_code', 'shipping_code', 'trim|required');
        $this->form_validation->set_rules('shipping_name', 'shipping_name', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->shipping_model->insert_shipping_info($data);
        sys_msg('操作成功',2,array(array('href'=>'shipping/index/','text'=>'返回列表页')));
    }
    
    function edit_shipping_info($shipping_id){
        auth(array('shipping_view','shipping_edit'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_shipping_perm());
        $shipping_id = intval($shipping_id);
        $check = $this->shipping_model->filter_shipping_info(array('shipping_id' => $shipping_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        $this->load->vars('shipping' , $check);
        $this->load->view('shipping/edit_shipping_info');
    }
    
    function proc_edit_shipping_info($shipping_id){
        auth('shipping_edit');
        $shipping_id = intval($shipping_id);
        $data['shipping_code'] = trim($this->input->post('shipping_code'));
        $data['shipping_name'] = trim($this->input->post('shipping_name'));
        $data['shipping_desc'] = trim($this->input->post('shipping_desc'));
        $data['is_use'] = intval($this->input->post('is_use'));
        $data['sort_order'] = trim($this->input->post('sort_order'));
        $data['track_name'] = trim($this->input->post('track_name'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('shipping_code', 'shipping_code', 'trim|required');
        $this->form_validation->set_rules('shipping_name', 'shipping_name', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->shipping_model->update_shipping_info($data , $shipping_id);
        sys_msg('操作成功',2,array(array('href'=>'shipping/index/','text'=>'返回列表页')));
    }
    
    public function operate($shipping_id){
        auth(array('shipping_area_edit','shipping_area_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_shipping_perm());
        $shipping_id = intval($shipping_id);
        $check_shipping = $this->shipping_model->filter_shipping_info(array('shipping_id' => $shipping_id));
        if(empty($check_shipping)){
            sys_msg('记录不存在', 1);
            return;
        }
        $all_shipping_area = $this->shipping_model->all_shipping_area(array('shipping_id' => $shipping_id));
        $list = array();
        foreach($all_shipping_area as $item){
            $list[$item->shipping_area_id]['shipping_area_id'] = $item->shipping_area_id;
            $list[$item->shipping_area_id]['shipping_area_name'] = $item->shipping_area_name;
            $list[$item->shipping_area_id]['is_cod'] = $item->is_cod;
            $list[$item->shipping_area_id]['shipping_id'] = $item->shipping_id;
            $list[$item->shipping_area_id]['shipping_fee1'] = $item->shipping_fee1;
            $list[$item->shipping_area_id]['shipping_fee2'] = $item->shipping_fee2;
            if(!empty($item->region_id)){
                $list[$item->shipping_area_id]['area'][] = $item->region_name;
            }
        }
        $this->load->vars('shipping_id' , $shipping_id);
        $this->load->vars('all_shipping_area' , $list);
        $this->load->view('shipping/shipping_area_list');
    }
    
    public function edit_shipping_area($shipping_area_id , $shipping_id){
        auth('shipping_area_edit');
        $shipping_area_id = intval($shipping_area_id);
        $shipping_id = intval($shipping_id);
        $check = $this->shipping_model->filter_shipping_area(array('shipping_area_id' => $shipping_area_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        $all_shipping_area_region = $this->shipping_model->all_shipping_area_region(array('shipping_area_id' => $shipping_area_id));
        $this->load->model('region_model');
        $province = $this->region_model->all_region(array('region_type' => 1));
        $this->load->vars('province' , $province);
        $this->load->vars('shipping_info' , $this->shipping_model->filter_shipping_info(array('shipping_id' => $shipping_id)));
        $this->load->vars('all_shipping_area_region' , $all_shipping_area_region);
        $this->load->vars('shipping_area' , $check);
        $this->load->vars('shipping_id' , $shipping_id);
        $this->load->vars('shipping_area_id' , $shipping_area_id);
        $this->load->view('shipping/edit_shipping_area');
    }
    
    public function proc_edit_shipping_area($shipping_area_id , $shipping_id){
        auth('shipping_area_edit');
        $shipping_area_id = intval($shipping_area_id);
        $shipping_id = intval($shipping_id);
        $area = $this->input->post('area');
        $data['shipping_area_name'] = trim($this->input->post('shipping_area_name'));
        $data['is_cod'] = intval($this->input->post('is_cod'));
        $data['shipping_fee1'] = floatval($this->input->post('shipping_fee1'));
        $data['shipping_fee2'] = floatval($this->input->post('shipping_fee2'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules('shipping_area_name', 'shipping_area_name', 'trim|required');
        $this->form_validation->set_rules('shipping_fee1', 'shipping_fee1', 'trim|required');
        $this->form_validation->set_rules('shipping_fee2', 'shipping_fee2', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $exist_data = array();
        if(!empty($area)){
            $area_ids = implode(",", $area);
            $exist_data = $this->shipping_model->shipping_area_filter($shipping_id, $shipping_area_id, $area_ids);
        }
        
        $this->shipping_model->update_shipping_area($data , $shipping_area_id);
        $this->shipping_model->delete_shipping_area_region(array('shipping_area_id' => $shipping_area_id));
        if(!empty($area)){
            if (!empty($exist_data['region_ids'])) {
                $exists_id_arr = explode(",", $exist_data['region_ids']);
                $area = array_diff($area, $exists_id_arr);
            }
            foreach($area as $key => $val){
                $filter['shipping_area_id'] = $shipping_area_id;
                $filter['region_id'] = $val;
                $filter['create_admin'] = $this->admin_id;
                $filter['create_date'] = $this->time;
                $this->shipping_model->insert_shipping_area_region($filter);
            }
            if (!empty($exist_data['region_names']))
                sys_msg('以下地区在其他区域已存在，不能重复添加：<br>'.$exist_data['region_names'], 0,  array(array('href' => 'shipping/edit_shipping_area/'.$shipping_area_id.'/'.$shipping_id, 'text' => '返回上一页')));            
        }
        sys_msg('操作成功',2,array(array('href'=>'shipping/operate/'.$shipping_id,'text'=>'返回列表页')));
    }
    
    public function delete_shipping_area($shipping_area_id , $shipping_id){
        auth('shipping_area_edit');
        $shipping_area_id = intval($shipping_area_id);
        $shipping_id = intval($shipping_id);
        $check = $this->shipping_model->filter_shipping_area(array('shipping_area_id' => $shipping_area_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        $this->shipping_model->delete_shipping_area_region(array('shipping_area_id' => $shipping_area_id));
        $this->shipping_model->delete_shipping_area(array('shipping_area_id' => $shipping_area_id));
        sys_msg('操作成功',2,array(array('href'=>'shipping/operate/'.$shipping_id,'text'=>'返回列表页')));
    }


    public function add_shipping_area($shipping_id){
        auth('shipping_area_edit');
        $shipping_id = intval($shipping_id);
        $check_shipping = $this->shipping_model->filter_shipping_info(array('shipping_id' => $shipping_id));
        if(empty($check_shipping)){
            sys_msg('记录不存在', 1);
        }
        $this->load->model('region_model');
        $province = $this->region_model->all_region(array('region_type' => 1));
        $this->load->vars('province' , $province);
        $this->load->vars('shipping_id' , $shipping_id);
        $this->load->vars('shipping_info' , $check_shipping);
        $this->load->view('shipping/add_shipping_area');
    }
    
    public function proc_add_shipping_area($shipping_id){
        auth('shipping_area_edit');
        $area = $this->input->post('area');
        $data['shipping_id'] = intval($shipping_id);
        $data['shipping_area_name'] = trim($this->input->post('shipping_area_name'));
        $data['is_cod'] = intval($this->input->post('is_cod'));
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = $this->time;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('shipping_area_name', 'shipping_area_name', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $shipping_area_id = $this->shipping_model->insert_shipping_area($data);
        if(!empty($area)){
            foreach($area as $key => $val){
                $filter['shipping_area_id'] = $shipping_area_id;
                $filter['region_id'] = $val;
                $filter['create_admin'] = $this->admin_id;
                $filter['create_date'] = $this->time;
                $this->shipping_model->insert_shipping_area_region($filter);
            }
        }
        sys_msg('操作成功',2,array(array('href'=>'shipping/operate/'.$data['shipping_id'],'text'=>'返回列表页')));
    }
    
    public function ajax_region(){
        $type = intval($this->input->post('type'))+1;
        $parent_id = intval($this->input->post('parent_id'));
        $this->load->model('region_model');
        $arr = $this->region_model->all_region(array('region_type'=> $type , 'parent_id' => $parent_id));
        echo json_encode(array('list'=>$arr,'type'=>$type));
    }
    
    public function delete_shipping_info($shipping_id)
    {
        auth('shipping_area_edit');
        $this->load->model('order_model');
        $shipping_id = intval($shipping_id);
        $check = $this->shipping_model->filter_shipping_info(array('shipping_id' => $shipping_id));
        if(!$check) sys_msg('记录不存在', 1);
        $check = $this->shipping_model->filter_shipping_area(array('shipping_id' => $shipping_id));
        if($check) sys_msg('记录无法删除', 1);
        $check = $this->order_model->filter_routing(array('shipping_id'=>$shipping_id));
        if($check) sys_msg('记录无法删除',1);
        $this->shipping_model->delete_shipping_info($shipping_id);
        sys_msg('操作成功',2,array(array('href'=>'shipping/index/','text'=>'返回列表页')));
    }
    
    
}