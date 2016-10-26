<?php
/**
* 
*/
class Region extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->time = date('Y-m-d H:i:s');
		$this->user_id = $this->session->userdata('user_id');
		$this->load->model('region_model');
	}
	
	public function load_region ()
	{
		$parent_id = intval($this->input->post('parent_id'));
		$all_region = $this->region_model->all_region(array('parent_id'=>$parent_id));
		print json_encode(array('err'=>0,'msg'=>'','data'=>$all_region));
	}
	
}