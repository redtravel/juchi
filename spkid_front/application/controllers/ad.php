<?php
/**
* 
*/
class Ad extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->user_id = $this->session->userdata('user_id');
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('ad_model');
	}

	public function info()
	{
		$this->load->library('Memcache');
		$this->load->model('product_model');
		$page_name = trim($this->input->post('page_name'));
		$position_tag = trim($this->input->post('position_tag'));
		$category_id = intval($this->input->post('category_id'));
		$brand_id = intval($this->input->post('brand_id'));		
		$parent_category_id = 0;
		if($category_id){
			$category = $this->product_model->filter_category(array('category_id'=>$category_id));
			if($category) $parent_category_id = $category->parent_id;
		}
		if(!$page_name||!$position_tag) sys_msg('参数错误',1);
		$cache_key = $page_name.'-'.$position_tag;
		if(!$ads=$this->memcache->get($cache_key)){
			$ads = $this->ad_model->ad_list($page_name,$position_tag);
			$this->memcache->save($cache_key,$ads,CACHE_TIME_AD);
		}
		$ad = NULL;
		foreach ($ads as $v) {
			if($v->end_date<$this->time) continue;
			if($v->brand_id&&$v->brand_id!=$brand_id) continue;
			if($v->category_id&&$v->category_id!=$category_id&&$v->category_id!=$parent_category_id) continue;
			if(!$v->brand_id&&$ad&&$ad->brand_id) continue;
			if(!$v->category_id&&$ad&&$ad->category_id) continue;
			if($v->category_id==$parent_category_id&&$ad&&$ad->category_id==$category_id) continue;
			$ad=$v;
		}
		if($ad){
			$ad->ad_code = adjust_path($ad->ad_code);
			print json_encode(array('err'=>0,'msg'=>'','html'=>$ad->ad_code));
		}else{
			print json_encode(array('err'=>1,'msg'=>''));
		}
	}
}