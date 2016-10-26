<?php
class Pick extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if (!$this->admin_id) sys_msg('请先登录',1,array(array('href'=>'index/login','text'=>'立即登录')));
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('pick_model');
                $this->load->helper('order');
		$this->pick_type=array('order'=>'订单不代收','ordercod'=>'订单代收','change'=>'换货单');
	}
	
	// 拣货单列表
	public function index(){
		auth(array('pick_view','pick_edit'));
		$filter = array();
		$over=$this->input->post('over');
		if($over == null || $over == "")
		    $over = -1;
		$filter['over']=intval($over);
		$pick = trim($this->input->post('pick'));
		if($pick == null || $pick == "")
		    $pick = -1;
		$filter['pick']=intval($pick);
		$filter['start_date'] = trim($this->input->post('start_date'));
		$filter['end_date'] = trim($this->input->post('end_date'));
		$filter['pick_sn'] = trim($this->input->post('pick_sn'));
		$filter['order_sn'] = trim($this->input->post('order_sn'));
		$filter['is_print'] = intval($this->input->post('is_print'));
		
		$filter = get_pager_param($filter);
		$data = $this->pick_model->pick_list($filter);
		
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('pick/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('pick/index', $data);
	}

    /**
    * 拣货单详情页
    */
   public function info($pick_sn=''){
        auth(array('pick_view'));
        $this->load->model('order_model');
        $this->load->model('change_model');
        $pick_sn=strtoupper(trim($pick_sn));
        $pick=$this->pick_model->filter(array('pick_sn'=>$pick_sn));
        if(!$pick) sys_msg('拣货单不存在',1);

        if($pick->type=='order'||$pick->type=='ordercod'){
                $pick_info=$this->order_model->all_order(array('pick_sn'=>$pick_sn));
        }else{
                $pick_info=$this->change_model->all_change(array('pick_sn'=>$pick_sn));
        }
        foreach($pick_info as $row) $row->sn=$pick->type=='change'?$row->change_sn:$row->order_sn;
        $this->load->view('pick/info',array(
                'pick'=>$pick,
                'pick_info'=>$pick_info
        ));
    }
    public function scan_shipping_edit_save(){
        auth('scan_shipping_edit');
        $order_id = intval($this->input->post('order_id'));
        $shipping_id = intval($this->input->post('shipping_id'));
        $invoice_no = trim($this->input->post('invoice_no'));
		$this->load->model('order_model');
        $order = $this->order_model->filter(array('order_id' => $order_id));
        if (!$order) {
            sys_msg('记录不存在', 1);
        }
        if ($order->is_qc == 0) {
            sys_msg('未复合不能修改运单号!', 1);
        }
        $update = array();
        $log = '';
        if($shipping_id != $order->shipping_id){
            $update['shipping_id'] = $shipping_id;
            $this->load->model('shipping_model');
            $shipping_info = $this->shipping_model->filter(array('shipping_id' => $shipping_id));
            $log = $log.'修改快递公司为 '.$shipping_info->shipping_name;
        }
        if($invoice_no != $order->invoice_no){
            $update['invoice_no'] = $invoice_no;
            $log = $log.'修改运单号为 '.$invoice_no;
        }
        if($log == ''){
            sys_msg('没有做任何改变', 0, array(array('text' => '继续编辑', 'href' => 'pick/scan_shipping_edit/' . $order_id), array('text' => '返回列表', 'href' => 'pick/scan_shipping_list')));
        }
        $this->db->query('BEGIN');
        $this->order_model->update($update, $order_id);
        $this->order_model->insert_action($order,$log);
        $this->db->query('COMMIT');
        sys_msg('操作成功', 0, array(array('text' => '继续编辑', 'href' => 'pick/scan_shipping_edit/' . $order_id), array('text' => '返回列表', 'href' => 'pick/scan_shipping_list')));
    }
    public function scan_shipping_edit($order_id){
        auth(array('scan_shipping_edit'));
		$this->load->model('order_model');
        $order = $this->order_model->filter(array('order_id'=>$order_id));
        //查询该订单允许使用的快递公司
        $shipping_list = $this->order_model->available_shipping(array('source_id'=>$order->source_id,'pay_id'=>$order->pay_id,'region_ids'=>array($order->country,$order->province,$order->city,$order->district),'can_cac'=>TRUE));
        $data['row']=$order;
        $data['shipping_list']=$shipping_list;
        $this->load->view('pick/scan_shipping_edit', $data);
    }
    public function scan_shipping_list()
    {
        auth(array('scan_shipping_list'));
		$this->load->model('shipping_model');
        $data = array('list' => array());
        $filter = array();
        $filter['order_sn'] = trim($this->input->post('order_sn'));
        $filter['invoice_sn'] = trim($this->input->post('invoice_sn'));
        $filter['start_date'] = trim($this->input->post('start_date'));
        $start_time = trim($this->input->post('start_time'));
        $filter['end_date'] = trim($this->input->post('end_date'));
        $end_time = trim($this->input->post('end_time'));
        $filter['shipping_id'] = trim($this->input->post('shipping_id'));
        $filter['shipping_status'] = trim($this->input->post('shipping_status'));
        //订单创建时间BABY-583
        $filter['create_start_date'] = trim($this->input->post('create_start_date'));
        $create_start_time = trim($this->input->post('create_start_time'));
        $filter['create_end_date'] = trim($this->input->post('create_end_date'));
        $create_end_time = trim($this->input->post('create_end_time'));
        
        if(!empty($filter['create_start_date']) && !empty($create_start_time)) {
        	$filter['create_start_date'] .= " " . $create_start_time;
        }
        if(!empty($filter['create_end_date']) && !empty($create_end_time)) {
        	$filter['create_end_date'] .= " " . $create_end_time;
        }
        //订单创建时间BABY-583 end
        if(!empty($filter['start_date']) && !empty($start_time)) {
        	$filter['start_date'] .= " " . $start_time;
        }
        if(!empty($filter['end_date']) && !empty($end_time)) {
        	$filter['end_date'] .= " " . $end_time;
        }
        // 发货列表
        if ($this->input->is_ajax_request())
        {
            $filter = get_pager_param($filter);
            $data = $this->pick_model->scan_shipping_list($filter);
            $data['full_page'] = FALSE;
            $data['perm_edit'] = check_perm('scan_shipping_edit');
			$data['content'] = $this->load->view('pick/scan_shipping_list', $data, TRUE);
			$data['error'] = 0;
            unset($data['list']);
			echo json_encode($data);
            return;
        }
        // 导出
        if ($this->input->post('export'))
        {
            $data = $this->pick_model->scan_shipping_export($filter);
            $data['tag'] = '?';
            $this->load->view('pick/order_shipping', $data);
            $file_name = "order_shipping.xls";
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=".$file_name);           
            return;
        }
        $data['filter'] = $filter;
        $data['shipping_list'] = $this->shipping_model->all_shipping();
        $data['full_page'] = TRUE;
        $this->load->view('pick/scan_shipping_list', $data);
    }    
	/**
	 * 拣货单详情页，扫描发货在此操作
	 */
	public function scan_shipping($pick_sn=''){
		auth(array('scan_shipping'));
		//$this->load->model('order_model');
		//$this->load->model('change_model');
		//$pick_sn=strtoupper(trim($pick_sn));
		//$pick=$this->pick_model->filter(array('pick_sn'=>$pick_sn));
		//if(!$pick) sys_msg('拣货单不存在',1);
		//
		//if($pick->type=='order'||$pick->type=='ordercod'){
		//	$pick_info=$this->order_model->all_order(array('pick_sn'=>$pick_sn));
		//}else{
		//	$pick_info=$this->change_model->all_change(array('pick_sn'=>$pick_sn));
		//}
		//foreach($pick_info as $row) $row->sn=$pick->type=='change'?$row->change_sn:$row->order_sn;
		$this->load->view('pick/scan_shipping');
	}
    //扫描拣货
    public function scan_pick()
    {
        auth(array('pick_scan'));
        $data = array();
        $pick_sn = $this->input->get_post('pick_sn');
        $data['pick_sn'] = $pick_sn;
        $data['list'] = array();
        if ($this->input->is_ajax_request() && !empty($pick_sn))
        {
            //$data['list'] = array();
			$data['full_page'] = FALSE;
            $data['list'] = $this->pick_model->pick_details($pick_sn);
            if ($data['list']) 
            { 
                $data['content'] = $this->load->view('pick/scan_pick', $data, TRUE);
			    $data['error'] = 0;
            }
            else
            {
                $data['error'] = 1;
                $data['message'] = '不是有效拣货单';
            }
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
        $this->load->view('pick/scan_pick', $data);
    }
    // 扫描拣货完成
    public function scan_pick_finish()
    {
        $this->load->model('pick_model');
        $pick_sn = $this->input->post('pick_sn');
        $sub_id = $this->input->post('sub_id');
        $scan_num = $this->input->post('scan_num');
        $rel_no = $this->input->post('rel_no');
        $data = array();
        $relno_list = array();
        for ($i = 0; isset($sub_id[$i]); $i++)
        {

            $unusual =  $this->input->post('is_unusual_'.$sub_id[$i]);
            // 更新商品拣货明细
            $this->pick_model->scan_pick_sub(array('pick_sn' => $pick_sn, 'sub_id' => $sub_id[$i], 'scan_num' => $scan_num[$i], 'admin_id' => $this->admin_id));
            if ($unusual != '' && !in_array($rel_no[$i], $relno_list)) $relno_list[] = $rel_no[$i];  
        }
        
        // 从拣货单中移除缺货订单
        for ($j = 0; isset($relno_list[$j]); $j++) 
        {
            $this->tick(array('pick_sn' => $pick_sn, 'odd_sn' => $relno_list[$j], 'odd_advice' => '拣货异常'));
        }

        // 扫描拣货完成
        $this->pick_model->scan_pick_finish(array('pick_sn' => $pick_sn, 'admin_id' => $this->admin_id));
        
        $data['list'] = array();
        $data['full_page'] = TRUE;
        $this->load->view('pick/scan_pick', $data);
        //$this->load->view('pick/scan_pick');
    }    
	/**
	 * 打印拣货单
	 */
	public function print_pick($pick_sn=''){
		auth(array('pick_view','pick_edit'));
		
		$pick_sn=strtoupper(trim($pick_sn));
		$pick=$this->pick_model->filter(array('pick_sn'=>$pick_sn));
		if(!$pick) sys_msg('拣货单不存在',1,array(array('href'=>'pick/','text'=>'返回订单拣货列表')));
		$pick_info = $this->pick_model->get_print_pick_info($pick_sn);
		if(!$pick_info) sys_msg('该拣货单是空的',1,array(array('href'=>'pick/','text'=>'返回订单拣货列表')));
        $this->load->view('pick/print_pick',array('pick_sn'=>$pick_sn, 'pick_info'=>$pick_info, 'pick' => $pick));
	}
	
	public function overview(){
		auth('pick_edit');
		//取出汇总信息来
		list($order_status,$ordercod_status,$change_status) = $this->pick_model->pick_status();
		
		$this->load->view('pick/overview',array(
			'order_status'=>$order_status,
			'ordercod_status'=>$ordercod_status,
			'change_status'=>$change_status
		));
	}
	
	/**
	 * 生成拣货单
	 */
	public function add(){
		auth('pick_edit');
                
		$pick_shipping = trim($this->input->post('pick_shipping'));
                $hand_type = $this->input->post('hand_type');
                if (empty($pick_shipping)) sys_msg ('快递公司不能为空', 1);
                if (!isset($hand_type)) sys_msg ('拣货类型不能为空', 1);
                $admin_id = 0;
                if ($hand_type == 1) {
                    $admin_id = intval($this->input->post('admin_id'));
                    if (empty($admin_id)) sys_msg ('拣货操作人不能为空', 1);
                }
                $pick_arr = explode("_",$pick_shipping);
                $order_type = $pick_arr[0];
                $shipping_id = $pick_arr[1];
		// if($shipping_id==SHIPPING_ID_CAC) sys_msg('自提订单不能拣货',1); // XXX:去除自提判断
		
		$this->db->trans_begin();
		$pick_sn = $this->pick_model->insert(array(
			'type'=>$order_type,
			'shipping_id'=>$shipping_id,
			'create_date'=>date("Y-m-d H:i:s"),
			'create_admin'=>$this->admin_id,
			'total_num'=>0,
			'over_num'=>0
		));
		switch($order_type){
			case 'order':
				$pick_num=$this->pick_model->pick_order($pick_sn,$shipping_id,PICK_NUM,$hand_type,$admin_id);
				break;
			case 'ordercod':
				$pick_num=$this->pick_model->pick_ordercod($pick_sn,$shipping_id,PICK_NUM,$hand_type,$admin_id);
				break;
			/* case 'change':
				$pick_num=$this->pick_model->pick_change($pick_sn,$shipping_id,PICK_NUM);
				break; */
		}
		if(!$pick_num){
			$this->db->trans_rollback();
                        sys_msg('没有订单或换货单可拣', 1);
		}
                $pick_data = array();
                $pick_data['total_num'] = $pick_num;
                if ($hand_type == 1) {
                    //手动拣货类型（pick_admin,pick_date,pick_status,pick_type）
                    $pick_data['pick_admin'] = $admin_id;
                    $pick_data['pick_date'] = date("Y-m-d H:i:s");
                    $pick_data['pick_status'] = 2;
                    $pick_data['pick_type'] = $hand_type;
                }
		$this->pick_model->update($pick_data,$pick_sn);
		$this->db->trans_commit();
                sys_msg('添加成功', 0);
	}
	
	/**
	 * 扫描发货
	 */
	public function scan_shipping_process(){
        auth('scan_shipping');
		$this->load->model('order_model');
		$this->load->model('change_model');
		$this->load->helper('order');
        //$sn=trim($this->input->post('sn'));
		$invoice_no=trim($this->input->post('invoice_no'));
                $scan_weight =  floatval($this->input->post('scan_weight'))* 1000;
		//$pick_sn=trim($this->input->post('pick_sn'));
		$this->db->trans_begin();
		//if(substr($sn,0,2)=='DD'){
			//订单发货
                $order=$this->order_model->lock_order('', $invoice_no);
                if(!$order||$order->shipping_status || !$order->is_qc || $order->odd){
                    //sys_msg('不可操作',1);
                    print json_encode(array('err' => 1, 'msg' => '订单必须是未发货、已复核、正常的订单，才可发货'));
                    exit;
		}
                $recheck_shipping_fee = calc_weight_shipping_fee($order, $scan_weight);
		$order_id=$order->order_id;
		$update = array(
			'shipping_status' => 1,
			'shipping_admin' => $this->admin_id,
			'shipping_date' => $this->time,
			'lock_admin' => 0,
			'invoice_no' => $invoice_no, 
                        'recheck_weight_unreal' => $scan_weight, 
                        'recheck_shipping_fee' => $recheck_shipping_fee
		);	
		$trans_update = array('trans_status'=>TRANS_STAT_OUT,'update_admin'=>$this->admin_id,'update_date'=>$this->time);		
		$action_note = "订单扫描发货";
		// 如果订单已全部支付，则自动财审
		$order = format_order($order);
		if($order->order_amount==0 && !$order->pay_status){
			$order->pay_status = 1; //置位，为了后面的判断
			$update['pay_status'] = 1;
			$update['finance_admin'] = $this->admin_id;
			$update['finance_date'] = $this->time;
			$trans_update['finance_check_admin'] = $this->admin_id;
			$trans_update['finance_check_date'] = $this->time;
			$action_note .= "，订单自动财审";

		}
		// 如果订单已财审，则自动完结
		if($order->pay_status){
			$update['is_ok'] = 1;
			$update['is_ok_admin'] = $this->admin_id;
			$update['is_ok_date'] = $this->time;
			$action_note .= "，订单自动完结";
		}
		// 更新事务表
		$this->order_model->update_trans(
			$trans_update,
			array('trans_type'=>TRANS_TYPE_SALE_ORDER,'trans_sn'=>$order->order_sn,'trans_status'=>TRANS_STAT_AWAIT_OUT)
		);
		$this->order_model->update($update,$order_id);
		foreach($update as $key=>$val) $order->$key = $val;
		$this->order_model->insert_action($order,$action_note);

	//}else{
			//换货单发货
	//		$change=$this->change_model->lock_change($sn);
	//		if(!$change||$change->shipping_status||$change->pick_sn!=$pick_sn){
	//			sys_msg('不可操作',1);
	//		}
	//		$change_id=$change->change_id;
	//		$update = array(
	//			'shipping_status'=>1,
	//			'shipping_admin'=>$this->admin_id,
	//			'shipping_date'=>$this->time,
	//			'invoice_no' => $invoice_no,
	//			'lock_admin'=>0,
	//			'lock_date'=>''
	//		);
	//		$trans_update = array('trans_status'=>TRANS_STAT_OUT,'update_admin'=>$this->admin_id,'update_date'=>$this->time);
	//		$this->change_model->update($update, $change_id);

	//		/* 记录log */
	//		$action_note = '换货单扫描发货';
	//		foreach($update as $key=>$val) $change->$key = $val;
	//		$this->change_model->insert_action((array)$change,$action_note);
	//		$this->order_model->update_trans($trans_update,array('trans_status'=>TRANS_STAT_AWAIT_OUT,'trans_sn'=>$change->change_sn));
	//		

	//	}
        //$this->pick_model->step($pick_sn);
		$this->db->trans_commit();
		/*if(!empty($order)){
			$this->order_model->notify_shipping($order);
		}else{
			$this->change_model->notify_shipping($change);
        }*/
        print json_encode(array('err'=>0,'msg'=>$order->order_sn));
	}
	
        /**
         * 标记异常订单
         * @param array $param
         */
	public function	tick($param=''){
                $this->load->model('order_model');
		$this->load->model('change_model');
                if(!empty($param) && is_array($param)) {
                    $odd_sn = $param['odd_sn'];
                    $pick_sn = $param['pick_sn'];
                    $odd_advice = $param['odd_advice'];
                } else {
                    auth('pick_remark_odd');
                    $odd_sn=strtoupper(trim($this->input->post('odd_sn')));
                    $pick_sn=trim($this->input->post('pick_sn'));
                    $odd_advice=trim($this->input->post('odd_advice'));
                }
                if(empty($odd_sn)) sys_msg('订单编号为空',1);
                if(empty($pick_sn)) sys_msg('拣货单号为空',1);
                if(empty($odd_advice)) sys_msg('问题单意见为空',1);
		//更改订单
		$this->db->trans_begin();
                $pick=$this->pick_model->lock_pick($pick_sn);
                if(!$pick) sys_msg('拣货单不存在',1);
		if(substr($odd_sn,0,2)=='DD')
		{
			$order=$this->order_model->filter(array('order_sn'=>$odd_sn));
			if($order->shipping_status){
			    sys_msg("订单已经发运，无法标记为问题单");
			}
			if(!$order||$order->odd||$order->pick_sn!=$pick_sn) sys_msg('该订单不能进行此操作',1);
			$this->order_model->update(array('odd'=>1,'pick_sn'=>'','invoice_no'=>'','is_pick'=>'0','pick_admin'=>'','pick_date'=>'','is_qc'=>'0','qc_admin'=>'','qc_date'=>''),$order->order_id);
			$this->order_model->insert_action($order,'订单标记为问题单，并从拣货单'.$pick_sn.'中剔除。');
			$this->order_model->insert_advice(array(
				'order_id'=>$order->order_id,
				'type_id' => ADVICE_ODD,
				'is_return' =>1,
				'advice_content'=>$odd_advice,
				'advice_admin'=>$this->admin_id,
				'advice_date'=>$this->time
			));
			$this->load->model('purchase_log_model');
			$this->purchase_log_model->insert(array("related_id"=>$order->order_id,"related_type"=>3,"desc_content"=>'订单标记为问题单，并从拣货单'.$pick_sn.'中剔除。',"create_admin"=>$this->admin_id,"create_date"=>$this->time));
                       // if($order->shipping_status) $pick_update['over_num']=$pick->over_num-1;
		}else{
		
			$change=$this->change_model->filter(array('change_sn'=>$odd_sn));
			if(!$change||$change->odd||$change->pick_sn!=$pick_sn) sys_msg('该换货单不能进行此操作',1);
			$this->change_model->update(array('odd'=>1,'pick_sn'=>''),$change->change_id);
			$this->change_model->insert_action($change,'换货单标记为问题单，并从拣货单'.$pick_sn.'中剔除。');
			$this->order_model->insert_advice(array(
				'order_id'=>$change->change_id,
				'type_id' => ADVICE_ODD,
				'is_return' =>3,
				'advice_content'=>$odd_advice,
				'advice_admin'=>$this->admin_id,
				'advice_date'=>$this->time
			));
			//if($change->shipping_status) $pick_update['over_num']=$pick->over_num-1;
	    }
	    $qc_flag = FALSE;
	    $rel_list = $this->pick_model->query_pick_sub(array('pick_sn' => $pick_sn, 'rel_no' => $odd_sn));
	    foreach ($rel_list as $pick_sub){
		if($pick_sub->qc_num == $pick_sub->product_number){
		    $qc_flag = TRUE;
		    break;
		}
	    }
            $this->pick_model->deleteSub(array('pick_sn' => $pick_sn, 'rel_no' => $odd_sn));
        
            //更新拣货单主表
            $total_num = $pick->total_num - 1;
            if (empty($total_num)) { //拣货单中没有其他订单，则删除
                $this->pick_model->delete($pick_sn);
            } else { //拣货单中海油其他订单，则更新
		if($qc_flag)
		    $over_num = $pick->over_num - 1;
                $this->pick_model->update(array('total_num'=>$total_num,"over_num"=>$over_num),$pick_sn);
            }
            
            $this->db->trans_commit();
		if ($this->input->is_ajax_request())
        { 
            print json_encode(array('err'=>0,'msg'=>''));
        }
	}
	
	/**
	 * 分配库存
	 */
	public function assign_sub(){
		auth('pick_edit');
		$this->load->model('order_model');
		$this->load->model('change_model');
		$this->load->model('product_model');
		$this->load->model('order_model');
		// 挑出订单、换货单、排序
		// 循环订单换货单
		$this->pick_model->assign_sub();
		print json_encode(array('err'=>0,'msg'=>''));
		
	}
	
	
	public function print_main ($pick_sn='')
	{
        auth('pick_edit');
        $data = array();
        $data['pick_sn'] = $pick_sn;
        $data['blank_shipping'] = '|'.implode('|', array(DB_SHIPPING_ID, DBDS_SHIPPING_ID)).'|';
        $this->load->view('pick/print_main', $data);
	}
	
	public function print_main_list ()
	{
		auth('pick_edit');
		$this->load->model('order_model');
		$this->load->model('change_model');
		$this->load->helper('order');
		$sn=strtoupper(trim($this->input->post('sn')));
		$type="";
		switch(substr($sn,0,2)){
			case 'DD':
				$order=$this->order_model->filter(array('order_sn'=>$sn));
				if(!$order) sys_msg('订单不存在',1);
				$order=$this->order_model->order_info($order->order_id);
				$order=format_order($order);
				$order->sn=$order->order_sn;
				$order->goods_num=$order->product_num;
				$order->id=$order->order_id;				
				$order->pick_cell= sprintf('%02d', $order->pick_cell);
				$p = strpos($order->address, $order->province_name);
				$c = strpos($order->address, $order->city_name);
				$d = strpos($order->address, $order->district_name);
                                if ($p === 0 && $c === 0 && $d === 0) {
                                    $order->address=$order->address;
                                } else {				
				    $order->address=$order->province_name.' '.$order->city_name.' '.$order->district_name.' '.$order->address;
				}
				$order->city=$order->district_name;
				$order->codAmount=($order->pay_id==PAY_ID_COD && $order->order_amount>0)?$order->order_amount:0;
				$html=$this->load->view('pick/print_main_list',array('list'=>array($order)),TRUE);
				print json_encode(array('err'=>0,'msg'=>'','html'=>$html, 'shipping_id' => $order->shipping_id,'type'=>'order'));
				break;
			case 'HH':
				$change=$this->change_model->change_info(0,$sn);
				if(!$change) sys_msg('换货单不存在',1);
				$change=(object)$change;
				$change->sn=$change->change_sn;
				$change->id=$change->change_id;
				$change->address=$change->province_name.' '.$change->city_name.' '.$change->district_name.' '.$change->address;
				$change->codAmount=0;
				$change->best_time='';
				$html=$this->load->view('pick/print_main_list',array('list'=>array($change)),TRUE);
				print json_encode(array('err'=>0,'msg'=>'','html'=>$html,'type'=>'change'));
				break;
			case 'PK':
				$pick=$this->pick_model->filter(array('pick_sn'=>$sn));
				if(!$pick) sys_msg('拣货单不存在');
				if($pick->type=='order' || $pick->type=='ordercod'){
					$orders=$this->pick_model->picked_order_info($sn);
					if(!$orders) sys_msg('拣货单中没有相关订单数据',1);
					foreach( $orders as $key => $order )
					{
						$order=format_order($order);
						$order->sn=$order->order_sn;
						$order->id=$order->order_id;
						$order->pick_cell= sprintf('%02d', $order->pick_cell);				
						$order->goods_num = $order->product_num;
						$order->weight= $order->order_weight_unreal;
						$p = strpos($order->address, $order->province_name);
						$c = strpos($order->address, $order->city_name);
						$d = strpos($order->address, $order->district_name);
						if ($p === 0 && $c === 0 && $d === 0) {
							$order->address=$order->address;
						} else {				
							$order->address=$order->province_name.' '.$order->city_name.' '.$order->district_name.' '.$order->address;
						}
						$order->city=$order->district_name;
						$order->codAmount=($order->pay_id==PAY_ID_COD && $order->order_amount>0)?$order->order_amount:0;
						$orders[$key] = $order;						
					}
					$html=$this->load->view('pick/print_main_list',array('list'=>$orders),TRUE);
					print json_encode(array('err'=>0,'msg'=>'','html'=>$html, 'shipping_id' => $pick->shipping_id,'type'=>'order'));
				}else{
					$changes=$this->pick_model->picked_change_info($sn);
					if(!$changes) sys_msg('拣货单中没有相关换货单数据');
					foreach( $changes as $key => $change )
					{
						$change->sn=$change->change_sn;
						$change->id=$change->change_id;
						$change->address=$change->province_name.' '.$change->city_name.' '.$change->district_name.' '.$change->address;
						$change->codAmount=0;
						$change->best_time='';
						$changes[$key] = $change;
					}
					$html=$this->load->view('pick/print_main_list',array('list'=>$changes),TRUE);
					print json_encode(array('err'=>0,'msg'=>'','html'=>$html,'type'=>'change'));
				}
				break;
			default:
				sys_msg('单号错误，必须是拣货单号或订单号或换货单号',1);
		}
	}
	
	public function print_sale ($type='',$ids='')
	{
		auth('pick_edit');
		$this->load->helper('order');
		if($type==''||$ids=='') sys_msg('参数错误',1);
		$ids=explode('-',$ids);
		$list=$this->pick_model->print_sale_data($type,$ids);
		$this->load->view('pick/print_sale',array('list'=>$list,'print_bgcolor'=>'#FFF'));
	}
	
	public function print_order($pick_sn='') {
		auth(array('pick_view','pick_edit'));
		
		$pick_sn = strtoupper(trim($pick_sn));
		$pick = $this->pick_model->filter(array('pick_sn'=>$pick_sn));
		if(!$pick) 
			sys_msg('拣货单不存在',1,array(array('href'=>'pick/','text'=>'返回订单拣货列表')));
		$orders = $this->pick_model->get_orders_by_picksn(array('pick_sn' => $pick_sn));
		if (empty($orders) || count($orders) <= 0) 
			sys_msg("没有需要打印的包裹装箱单",1,array(array('href'=>'pick/','text'=>'返回订单拣货列表')));
		$order_ids = array();
		foreach ($orders as $row) {
			$order_ids[] = $row->order_id;
		}
		
		$order_info = $this->pick_model->get_order_info($order_ids);
		
		$this->load->view('pick/print_order',array('order_info'=>$order_info, 'pick_sn' => $pick_sn));
	}
        
        public function print_orders($pick_ids){
            $pick_id_arr = explode('-', $pick_ids);
            $orders = $this->pick_model->get_orders_by_picksn(array('pick_id' => $pick_id_arr));
            if (empty($orders) || count($orders) <= 0) 
                sys_msg("没有需要打印的包裹装箱单",1,array(array('href'=>'pick/','text'=>'返回订单拣货列表')));
            $order_ids = array();
            foreach ($orders as $row) {
                $order_ids[] = $row->order_id;
            }
            $order_info = $this->pick_model->get_order_info($order_ids);
            $this->load->view('pick/print_order',array('order_info'=>$order_info, 'pick_sn' => $pick_sn));
        }
        
        public function search_admin() {
		auth('pick_edit');
		$admin_name = trim($this->input->post('admin_name'));
		if(!$admin_name) sys_msg('请填写搜索关键字',1);
		$admin_list = $this->pick_model->search_admin($admin_name);
		print json_encode(array('err'=>0,'msg'=>'','admin_list'=>$admin_list));
	}
	
    public function scan_pick_rf(){
        auth(array('pick_scan_rf'));
        $data = array();
       
        $data['cur_menu']='out';
        $data['list'] = array();
        if ($this->input->is_ajax_request())
        { 
	    $pick_sn = $this->input->get_post('pick_sn');
	    $data['pick_sn'] = $pick_sn;
	    if(empty($pick_sn)){
		$data['error'] = 1;
                $data['message'] = '请输入拣货单号';
		echo json_encode($data);
		return;
	    }
            $data['list'] = $this->pick_model->pick_details($pick_sn);
            if ($data['list']) 
            { 
		$data['error'] = 0;
            }
            else
            {
                $data['error'] = 1;
                $data['message'] = '不是有效拣货单';
            }
	    echo json_encode($data);
	    return;
	}
        $this->load->view('pick/scan_pick_rf', $data);
    }
    
    function scan_pick_rf_finish(){
        $data = array();
	$data['cur_menu']='out';
        $this->load->model('pick_model');
        $pick_sn = trim($this->input->post('pick_sn'));
	if(empty($pick_sn)){
		$data['pick_sn'] ="";
		$data['finished']=FALSE;
		$this->load->view('pick/scan_pick_rf', $data);
		return;
	}
        $sub_id = $this->input->post('sub_id');
        $scan_num = $this->input->post('scan_num');
        $rel_no = $this->input->post('rel_no');
        $relno_list = array();
        for ($i = 0; isset($sub_id[$i]); $i++)
        {

            $unusual = intval($this->input->post('is_unusual_'.$sub_id[$i]));
            // 更新商品拣货明细
            $this->pick_model->scan_pick_sub(array('pick_sn' => $pick_sn, 'sub_id' => $sub_id[$i], 'scan_num' => $scan_num[$i], 'admin_id' => $this->admin_id));
            if ($unusual != 0 && !in_array($rel_no[$i], $relno_list)) $relno_list[] = $rel_no[$i];  
        }
        
        // 从拣货单中移除缺货订单
        for ($j = 0; isset($relno_list[$j]); $j++) 
        {
            $this->tick(array('pick_sn' => $pick_sn, 'odd_sn' => $relno_list[$j], 'odd_advice' => '拣货异常'));
        }

        // 扫描拣货完成
        $this->pick_model->scan_pick_finish(array('pick_sn' => $pick_sn, 'admin_id' => $this->admin_id));
        
	$data['pick_sn'] = $pick_sn;
	$list = $this->pick_model->pick_details($pick_sn);
	if (count($list)>0) { $data['finished'] = FALSE; }
	else { $data['finished'] = TRUE; }
        $this->load->view('pick/scan_pick_rf', $data);
    }
    
    /**
     * 设置拣货单打印状态，打印状态仅做标识无实际功能,仅给提示功能
     */
    public function set_print_flag(){
	$pick_sn = trim($this->input->post('pick_sn'));
	$flag = trim($this->input->post('flag'));
	if(empty($pick_sn)){
	    echo json_encode(array('err'=>1,"msg"=>"拣货单号为空",));
	    return;
	}
	$this->pick_model->update(array('is_print'=>$flag),$pick_sn);
	echo json_encode(array('err'=>0,"msg"=>"","flag"=>$flag));
	return;
    }
    
    public function blank_print($order_ids){
        $this->load->model('order_model');
	 $order_ids2 = urldecode($order_ids);
        $code = '';
        $order_id_arr = explode('|', $order_ids2);
        $orders=$this->order_model->get_orders_info($order_id_arr);
	//print_r($orders);
        if(!$orders) sys_msg('订单不存在或运单号缺失',1);
        foreach ($orders as $key => $order) {
            $order=format_order($order);
            if ($code == '') $code = $order->shipping_code;
            if ($code != $order->shipping_code) sys_msg('订单中存在不同的快递公司，系统无法处理',1);
            $order->sn=$order->order_sn;
            $order->goods_num=$order->product_num;
            $order->id=$order->order_id;				
            $order->pick_cell= sprintf('%02d', $order->pick_cell);
            $p = strpos($order->address, $order->province_name);
            $c = strpos($order->address, $order->city_name);
            $d = strpos($order->address, $order->district_name);
            if ($p === 0 && $c === 0 && $d === 0) {
                $order->address=$order->address;
            } else {				
                $order->address=$order->province_name.' '.$order->city_name.' '.$order->district_name.' '.$order->address;
            }
            $order->city=$order->district_name;
            $order->codAmount=($order->pay_id==PAY_ID_COD && $order->order_amount>0)?$order->order_amount:0;
            $order->transportType = ($order->shipping_id == DBDS_SHIPPING_ID) ? '360特惠件' : '标准快递';
            $order->insuranceValue = 300;
            if ($order->order_price >= 300) $order->insuranceValue = intval($order->order_price/300)*300;
            $orders[$key] = $order;
        }
        $this->load->view('pick/'.$code,array('list'=>$orders, 'full_src' => true));
    }
        
}
