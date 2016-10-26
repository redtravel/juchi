<?php
/**
* Order_service
*/
class Order_service extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if (!$this->admin_id) redirect('index/login');
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('order_model');
		$this->load->helper('order');
	}

	public function order_product($order_id)
	{
		$this->load->model('product_model');
		$order_id = intval($order_id);
		$order_product = $this->order_model->order_product($order_id);
		if(!$order_product) {
			print '该订单没有商品';
			return;
		}
		$product_ids = array_keys(get_pair($order_product,'product_id','product_id'));
		$product_image = $this->product_model->all_gallery(array('image_type'=>'default','product_id'=>$product_ids));
		$img_list = array();
		foreach($product_image as $img) $img_list[$img->product_id.'-'.$img->color_id]=$img->img_url;
		foreach($order_product as $k=>$p){
			$order_product[$k]->img = isset($img_list[$p->product_id.'-'.$p->color_id])?$img_list[$p->product_id.'-'.$p->color_id]:'';
		}
		$this->load->vars('order_product',$order_product);
		$this->load->view('order/service_order_product');
	}
}