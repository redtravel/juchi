<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of product_type_link
 *
 * @author mickey
 */
class product_type_link extends CI_Controller {

    function __construct() {
	parent::__construct();
	$this->admin_id = $this->session->userdata('admin_id');
	$this->time = date('Y-m-d H:i:s');
	if (!$this->admin_id)
	    redirect('index/login');
	$this->load->model('product_model');
    }

    public function index() {
	auth('pro_type_link_view');
	$filter = $this->uri->uri_to_assoc(3);
	$filter['product_sn'] = trim($this->input->post('product_sn'));
	$filter['product_name'] = trim($this->input->post('product_name'));
	$filter['category_id'] = intval($this->input->post('category_id'));
	$filter['first_type'] = intval($this->input->post('first_type'));
	$filter['second_type'] = intval($this->input->post('second_type'));
	$filter['three_type'] = intval($this->input->post('three_type'));
	$filter['brand_id'] = intval($this->input->post('brand_id'));
	$filter['product_sex'] = intval($this->input->post('product_sex'));
	$filter['skip_set'] = intval($this->input->post('skip_set'));
	$filter = get_pager_param($filter);
	$data = $this->product_model->show_product_type_link($filter);
	if ($this->input->is_ajax_request())
	{
		$data['full_page'] = FALSE;
		$data['content'] = $this->load->view('product/product_type_link', $data, TRUE);
		$data['error'] = 0;
		unset($data['list']);
		echo json_encode($data);
		return;
	}
	$data['full_page'] = TRUE;
	$this->load->model('brand_model');
	$this->load->model('category_model');
	$this->load->model('product_type_model');
	$this->load->helper('category');

	$this->load->vars('all_brand', $this->brand_model->all_brand());
	$this->load->vars('all_category',category_flatten(category_tree($this->category_model->all_category()),'-- '));
	//一级分类
        $first_type=$this->product_type_model->filter(array('parent_id'=>0));
	$data['first_type']=$first_type;
	$this->load->view('product/product_type_link', $data);
    }
    
    public function pre_set_type(){
	auth('pro_type_link_set');
	$filter = $this->uri->uri_to_assoc(3);
	$filter['product_id'] = intval($this->input->post('product_id'));
	$data = $this->product_model->filter($filter);
	$owner_types = $this->product_model->filter_product_type_link($filter);
	$value = array();
	if(empty($data))sys_msg('数据异常，请联系管理员', 1);
	$this->load->model('brand_model');
	$this->load->model('category_model');
	$this->load->model('product_type_model');
    $this->load->helper('category');
	
	$brand = $this->brand_model->filter(array('brand_id'=>$data->brand_id));
	if(!empty($brand))
	    $value["brand_name"] = $brand->brand_name;
	
	$category = $this->category_model->filter(array('category_id'=>$data->category_id));
	if(!empty($category))
	     $value["category_name"] = $category->category_name;
	$value["product_id"] = $data->product_id;
	$value["product_sn"] = $data->product_sn;
	$value["product_name"] = $data->product_name;
	$value["owner_types"] = $owner_types;
	
	//Init
    $selected = array_keys(index_array($owner_types, 'type_id'));
    $types = category_tree($this->product_type_model->filter(array('genre_id'=>$data->genre_id)), 0, 'type_id');
    foreach($types as $key=>$sub_types)
    {
        foreach($sub_types->sub_items as $k=>$type){
            $types[$key]->sub_items[$k]->checked = in_array($type->type_id, $selected);
        }        
    }

 
	$value["types"] = $types;
	$value["content"] = $this->load->view('product/product_type_link_view',$value,TRUE);
	$value['error'] = 0;
	echo json_encode($value);
	return;
    }
    
    public function set_type(){
	auth('pro_type_link_set');
	$update = array();
	$update['product_id'] = intval($this->input->post('product_id'));
	$update["type_ids"] = $this->input->post('type_ids');
	$value["list"] = $this->product_model->set_product_type($update);
	$value['error'] = 0;
	echo json_encode($value);
	return;
    }

}

?>
