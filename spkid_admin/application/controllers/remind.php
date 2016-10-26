<?php
#doc
#	classname:	Brand
#	scope:		PUBLIC
#
#/doc

class Remind extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('remind_model');
	}

	public function query ()
	{
		$my_post = $this->input->post();
		$user_id = $this->admin_id;
		$index = intval($this->input->post('index_id'));
		$re = $this->remind_model->remind_list($user_id, $index);

		$data['index'] = $re['index'];
		$data['restr'] = $re['str'];
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}
    public function query_advice(){
		$list = $this->remind_model->query_advice_list();
        $detail = array();
        foreach ($list as $value) {
            $detail[] = $value->context;
        }
		$data['detail'] = $detail;
		$data['error'] = 0;
		echo json_encode($data);
		return;
    }
    public function query_order(){
        $detail = array();
        //$detail[] = '<a href ="javascript:void(0);" onclick="link_to(1);">24小时未客审订单有 '.$this->remind_model->query_order_over24confirm().'个</a>';
        //$detail[] = '<a href ="javascript:void(0);" onclick="link_to(2);">货到付款待发货超过24小时订单 '.$this->remind_model->query_order_over24shipping().'个</a>';
        //$detail[] = '<a href ="javascript:void(0);" onclick="link_to(3);">在线支付待发货超过24小时订单 '.$this->remind_model->query_order_over24shipping2().'个</a>';
        //$detail[] = '<a href ="javascript:void(0);" onclick="link_to(5);">系统当前还存在的问题单 '.$this->remind_model->query_order_odd().'个</a>';
        $cnt = $this->remind_model->query_order_over24confirm();
		if($cnt>0) $detail[] = '24小时未客审订单有 '.$cnt.'个';
        $cnt = $this->remind_model->query_order_over24shipping();
        if($cnt>0) $detail[] = '货到付款待发货超过24小时订单 '.$cnt.'个';
        $cnt = $this->remind_model->query_order_over24shipping2();
        if($cnt>0) $detail[] = '在线支付待发货超过24小时订单 '.$cnt.'个';
        $cnt = $this->remind_model->query_order_abs();
        if($cnt>0) $detail[] = '虚拟订单待分配库存超过4天 '.$cnt.'个';
        $cnt = $this->remind_model->query_order_odd();
        if($cnt>0) $detail[] = '系统当前还存在的问题单 '.$cnt.'个';
        
        $data['detail'] = $detail;
		$data['error'] = 0;
		echo json_encode($data);
		return;
    }
}
###