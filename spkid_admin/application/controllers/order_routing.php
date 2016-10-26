<?php
#doc
#	classname:	Purchase_virtual
#	scope:		PUBLIC
#
#/doc

class Order_routing extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('order_routing_model');
		$this->load->model('order_source_model');
		$this->load->model('shipping_model');
		$this->load->model('payment_model');
	}
	
	public function index ()
	{
		auth(array('order_routing_view','order_routing_edit'));

		$this->load->vars('all_show_type', array(0=>'显示方式',1=>'前台',2=>'后台',3=>'前后台',4=>'不显示'));
		$this->load->vars('all_routing', array(''=>'流程','F'=>'先财审后发货','S'=>'先发货后财审'));
		$this->load->vars('all_routing_show', array('F'=>'客审->财审->物流','S'=>'客审->物流->财审'));
		
		$filter = $this->uri->uri_to_assoc(3);
		$filter['source_id'] = intval($this->input->post('source_id'));
		$filter['shipping_id'] = intval($this->input->post('shipping_id'));
		$filter['pay_id'] = intval($this->input->post('pay_id'));
		$data = $this->order_routing_model->order_routing_list($filter);
		
		if ($this->input->is_ajax_request()) {
			
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('order_routing/order_routing_index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		
		$data['full_page'] = TRUE;
		$this->load->vars('all_source', $this->order_source_model->all_source());
		$this->load->vars('all_shipping', $this->shipping_model->all_shipping());
		$this->load->vars('all_payment', $this->payment_model->all_payment());
		$this->load->view('order_routing/order_routing_index', $data);
	}
	
	public function save () {
		
		auth('order_routing_edit');
		
		$routing_id = intval($this->input->post('routing_id'));
        $result = array('error'=>0,'msg'=>'添加成功！');
        if($routing_id>0){
            $result['msg'] = "更新成功";
            $result['routing_id'] = $routing_id;
        }
        
        $routing = array();
        $routing['source_id'] = intval($this->input->post('source_id'));
        $routing['shipping_id'] = intval($this->input->post('shipping_id'));
        $routing['pay_id'] = intval($this->input->post('pay_id'));
        $routing['show_type'] = intval($this->input->post('show_type'));
        if($routing['source_id']*$routing['shipping_id']*$routing['pay_id']*$routing['show_type']==0){
            echo json_encode(array('error'=>1,'msg'=>'订单来源，配送方式，支付方式，显示方式必须选择！'));
            return;
        }
        $routing['routing'] = trim($this->input->post('routing'));
        if(!in_array($routing['routing'],array('F','S'))){
            echo json_encode(array('error'=>1,'msg'=>'请选择正确的流程！'));
            return;
        }
        $exists_routing = $this->order_routing_model->exists($routing,$routing_id);
        if(!empty($exists_routing) && $exists_routing>0){
            echo json_encode(array('error'=>1,'msg'=>'记录已存在！'));
            return;
        }
        if($routing_id > 0){
            unset($routing['source_id']);
            unset($routing['shipping_id']);
            unset($routing['pay_id']);
            $this->order_routing_model->update($routing, $routing_id);
        }else{
        	$this->order_routing_model->insert($routing);
        }
        echo json_encode($result);
	}
	
	public function del () {
		
		auth('order_routing_del');
		$routing_id = intval($this->input->post('routing_id'));
        $in_use = $this->order_routing_model->check_in_use($routing_id);
        if(!empty($in_use) && $in_use > 0) {
        	echo json_encode(array('error'=>1,'msg'=>'该流程规则已被使用过，不能删除!'));
        	return;
        }
        $this->order_routing_model->del($routing_id);
        echo json_encode(array('error'=>0,'msg'=>'删除成功!'));
	}
	
}
###