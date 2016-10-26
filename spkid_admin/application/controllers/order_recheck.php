<?php
#doc
#	classname:	Order_recheck
#	scope:		PUBLIC
#
#/doc

class Order_recheck extends CI_Controller
{
	public function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if ( ! $this->admin_id )
		{
			redirect('index/login');
		}
		$this->load->model('order_recheck_model');
	}

	public function index ()
	{
		auth('order_recheck');
		$data = array();
		$data['full_page'] = TRUE;
		$this->load->view('depot/order_recheck', $data);
	}
	
	public function get_order_product($order_sn) {
		$order_sn = urldecode($order_sn);
		if (empty($order_sn)) {
			echo '{"result":0,"msg":"订单号为空"}';
			exit;	
		}
		//检查订单是否符合复核要求
		$check_order_recheck = $this->order_recheck_model->check_order_recheck($order_sn);
		//订单不存在
		if (empty($check_order_recheck)) {
			echo '{"result":0,"msg":"该订单不存在"}';
			exit;
		}
                //订单是否是异常订单
		if ($check_order_recheck->odd == 1) {
			echo '{"result":0,"msg":"该订单是异常订单"}';
			exit;
		}
		//订单是否已完成复核
		if ($check_order_recheck->is_qc == 1 && $check_order_recheck->qc_admin > 0) {
			echo '{"result":0,"msg":"该订单已复核"}';
			exit;
		}
		//订单是否已完成拣货
		if ($check_order_recheck->is_pick == 0 || $check_order_recheck->pick_admin <= 0) {
			echo '{"result":0,"msg":"订单还未完成拣货"}';
			exit;
		}
		
        $product_list = $this->order_recheck_model->get_order_product_info($order_sn);
        if(count($product_list) <= 0 || empty($product_list)){
        	echo '{"result":0,"msg":"订单查询失败"}';
			exit;
        }
        echo json_encode($product_list);
       	exit;
	}
        /*
	public function check_order_product($order_sn,$provider_barcode) {
		$order_sn = urldecode($order_sn);
		$provider_barcode = urldecode($provider_barcode);
		if (empty($order_sn)) {
			echo '{"result":0,"msg":"订单号不能为空"}';
			exit;
		}
		if (empty($provider_barcode)) {
			echo '{"result":0,"msg":"商品条形码不能为空"}';
			exit;
		}
		//检查订单商品条形码
		$data = $this->order_recheck_model->filter_order_product($order_sn,$provider_barcode);
		if (count($data) <= 0 || empty($data)) {
			echo '{"result":0,"msg":"该订单中没有此商品"}';
			exit;
		}
		echo json_encode($data);
       	exit;
	}
	*/
	public function recheck() {
		auth('order_recheck');
		$this->load->model('pick_model');
                
		$order_sn = trim($this->input->post('order_sn'));
		$invoice_no = trim(strtoupper($this->input->post('invoice_no')));
		$pick_sn = $this->input->post('pick_sn');
		$sub_ids = $this->input->post('sub_ids');
		//POST数量验证
		if (empty($order_sn)) 
			sys_msg('复核失败，订单号不能为空!', 1);
		if (empty($invoice_no)) 
			sys_msg('复核失败，运单号不能为空!', 1);
		if (empty($pick_sn)) 
			sys_msg('复核失败，拣货号为空!', 1);
		if (empty($sub_ids)) 
			sys_msg('复核失败，没有需要复核的商品', 1);
		$recheck_arr = array();
		foreach ($sub_ids as $sub_id) {
			//$sub_id = $this->input->post('sub_id_'.$sub_id);
			$qc_num = $this->input->post('qc_num_'.$sub_id);
			if (!empty($sub_id) && !empty($qc_num)) {
				$recheck_arr[$sub_id] = $qc_num;
			}
		}
		if (empty($recheck_arr)) 
			sys_msg('复核失败，订单没有需要复核的商品',1);
		//拣货单号检查
                $pick_info = $this->pick_model->filter(array('pick_sn'=>$pick_sn));
                if (empty($pick_info) || count($pick_info) <= 0) {
                    sys_msg('拣货单号不存在',1);
                }
		//检查商品复核数量是否正确
		$product_list = $this->order_recheck_model->get_order_product_info($order_sn);
		if (empty($product_list)) 
			sys_msg('复核失败，订单没有需要复核的商品',1);
		foreach ($product_list as $product) {
			$product_arr[$product->sub_id] = $product->product_number;
		}
		if (array_diff_assoc($product_arr,$recheck_arr)) 
			sys_msg('复核失败，该订单还有未复核的商品数量',1);
		
		$this->db->query('BEGIN');
		//更新拣货子表
		foreach ($recheck_arr as $key=>$value) {
			$filter['sub_id'] = $key;
			$pick_sub = $this->order_recheck_model->get_pick_sub($filter);
			if ($pick_sub->product_number != $value) {
				sys_msg('复核失败，复核的商品数量错误', 1);
			}
			$update = array();
			$update['qc_num'] = $value;
			$update['qc_admin'] = $this->admin_id;
			$update['qc_date'] = date('Y-m-d H:i:s');
			$this->order_recheck_model->update_pick_sub($update,$key);
		}
		//更新拣货主表
		$this->order_recheck_model->update_pick($pick_sn,$this->admin_id);
		
		//更新订单详情表
		$update2 = array();
		$update2['invoice_no'] = $invoice_no;
		$update2['is_qc'] = 1;
		$update2['qc_admin'] = $this->admin_id;
		$update2['qc_date'] = date('Y-m-d H:i:s');
		$this->order_recheck_model->update_order_info($update2,$order_sn);
		
		$this->db->query('COMMIT');
		
		sys_msg('复核成功！', 0);
	}
        
        function set_unusual_order($pick_sn,$order_sn){
            auth('order_recheck');
            $this->load->model('pick_model');
            $this->load->model('order_model');
            
            $pick_sn = urldecode($pick_sn);
            $order_sn = urldecode($order_sn);
            if (empty($pick_sn)) {
                    echo '{"result":0,"msg":"拣货单号错误"}';
                    exit;
            }
            if (empty($order_sn)) {
                    echo '{"result":0,"msg":"订单号不能为空"}';
                    exit;
            }
            //拣货单号检查
            $pick_info = $this->pick_model->filter(array('pick_sn'=>$pick_sn));
            if (empty($pick_info) || count($pick_info) <= 0) {
                echo '{"result":0,"msg":"拣货单号不存在"}';
                exit;
            }
            //订单号检查
            $order_info = $this->order_model->filter(array('order_sn'=>$order_sn));
            if (empty($order_info) || count($order_info) <= 0) {
                echo '{"result":0,"msg":"该订单不存在"}';
                exit;
            }
	    $content = $this->input->post('content');
            $this->db->query('BEGIN');
            
            //更新订单信息表
            $this->order_recheck_model->update_order_info(array('odd'=>1,'pick_sn'=>'','invoice_no'=>'','is_pick'=>'0','pick_admin'=>'','pick_date'=>'','is_qc'=>'0','qc_admin'=>'','qc_date'=>''),$order_sn);
            
            //添加订单建议表
            $update_advice = array();
            $update_advice['order_id'] = $order_info->order_id;
            $update_advice['type_id'] = 2;
            $update_advice['is_return'] = 1;
            $update_advice['advice_content'] = empty($content)?'订单复核异常':$content;
            $update_advice['advice_admin'] = $this->admin_id;
            $update_advice['advice_date'] = date("Y-m-d H:i:s");
            $this->order_recheck_model->insert_order_advice($update_advice);
            
            //添加订单操作记录
            $update_action = array();
            $update_action['order_id'] = $order_info->order_id;
            $update_action['is_return'] = 0;
            $update_action['order_status'] = $order_info->order_status;
            $update_action['shipping_status'] = $order_info->shipping_status;
            $update_action['pay_status'] = $order_info->pay_status;
            $update_action['action_note'] = '订单复核异常,标记为问题单并从拣货单'.$pick_sn.'中剔除。';
            $update_action['create_admin'] = $this->admin_id;
            $update_action['create_date'] = date("Y-m-d H:i:s");
            $this->order_recheck_model->insert_order_action($update_action);
            
            //更新拣货单主表
            $update_total_num = $pick_info->total_num - 1;
            if (empty($update_total_num)) { //拣货单中没有其他订单，则删除
                $this->pick_model->delete($pick_sn);
            } else { //拣货单中海油其他订单，则更新
                $this->pick_model->update(array('total_num'=>$update_total_num),$pick_sn);
            }
            
            //删除拣货单子表信息
            $this->order_recheck_model->delete_pick_sub($pick_sn,$order_sn);
            
            $this->db->query('COMMIT');
            
            echo '{"result":1}';
            exit;
        }

}
###