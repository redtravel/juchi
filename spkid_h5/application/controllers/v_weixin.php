<?php
/**
* 
*/
class V_weixin extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->time = date('Y-m-d H:i:s');
		$this->user_id = $this->session->userdata('user_id');
		$this->load->model('order_model');
	}

	public function index()
	{
		$this->load->view('mobile/v_weixin/index');
	}
    
}
