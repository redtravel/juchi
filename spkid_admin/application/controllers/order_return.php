<?php
#doc
#	classname:	Exchange
#	scope:		PUBLIC
#
#/doc

class Order_return extends CI_Controller
{
	public function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if ( ! $this->admin_id )
		{
			redirect('index/login');
		}
                $this->time=date('Y-m-d H:i:s');
		$this->load->model('depot_model');
		$this->load->model('return_model');
	}

	public function index ()
	{
		//auth(array('order_return_view','order_return_edit','exchangeout_audit','exchangein_audit'));
		$filter = $this->uri->uri_to_assoc(3);
		$all_post = $this->input->post();
		$filter['return_sn'] = isset($all_post['return_sn'])?trim($all_post['return_sn']):'';
		$filter['order_sn'] = isset($all_post['order_sn'])?trim($all_post['order_sn']):(isset($filter['order_sn'])?$filter['order_sn']:'');
		$filter['consignee'] = isset($all_post['consignee'])?trim($all_post['consignee']):'';
		$filter['provider_goods'] = isset($all_post['provider_goods'])?trim($all_post['provider_goods']):'';
		$filter['composite_status'] = isset($all_post['composite_status'])?intval($all_post['composite_status']):'-1';
		$filter['pay_status'] = isset($all_post['pay_status'])?intval($all_post['pay_status']):'-1';
		$filter['shipping_status'] = isset($all_post['shipping_status'])?intval($all_post['shipping_status']):'-1';
		$filter['return_status'] = isset($all_post['return_status'])?intval($all_post['return_status']):'-1';
		$filter['shipping_id'] = isset($all_post['shipping_id'])?intval($all_post['shipping_id']):'-1';
		$filter['is_ok'] = isset($all_post['is_ok'])?intval($all_post['is_ok']):'-1';
		$filter['start_time'] = trim($this->input->post('start_time'));
		$filter['end_time'] = trim($this->input->post('end_time'));

		$filter = get_pager_param($filter);

		$data = $this->return_model->return_list($filter);

		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;
			$data['content'] = $this->load->view('order_return/list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->model('shipping_model');
		$this->load->vars('all_shipping', $this->shipping_model->all_shipping());

		$status_list = array('-1'=>'请选择','0'=>'待确认','100'=>'待返款','101'=>'待入库','102'=>'已完成','4'=>'作废');
		$data['status_list'] = $status_list;

		$data['my_id'] = $this->admin_id;
		$this->load->view('order_return/list', $data);
	}

	public function edit ($return_id = 0)
	{
		auth(array('order_return_edit','order_return_view','order_return_confirm','order_return_unconfirm','order_return_pay','order_return_ship','order_return_ok'));
		$data = array();

		$return = $this->return_model->return_info($return_id);
        if (empty($return))
        {
        	sys_msg('退货单不存在！',1);
        }
        /* 关联订单信息 */
		$order_id = $return['order_id'];
        $order = $this->return_model->get_order_info($order_id);
        $data['order'] = $order;
        $data['return'] = $return;

            $pay_arr = $this->return_model->get_pay_detail_arr($return_id,1);// 取得支付明细数组
            $data['pay_arr'] = $pay_arr;
            $data['suggestiontype_arr'] = $this->return_model->get_advice_type_arr(); //获取意见类型名称
            $data['suggestion_list'] = $this->return_model->get_return_advice($return_id); 
            //$data['suggestion_list'] = $this->return_model->get_return_advice($order_id); 
            // 获取意见列表 @author Tony

        /* 取得退货单商品 */
        $order_product = $return_product = array();
        $arr = $this->return_model->order_product_can_return($order_id);
        foreach($arr as $v){
            //if(isset($v['discount_type']) && $v['discount_type'] == 4) continue;
            $v['return_product_num'] = $v['return_consign_num'] = 0;
            $v['return_rec_id'] = 0;
            $order_product[$v['rec_id'].'-'.$v['product_id'].'-'.$v['color_id'].'-'.$v['size_id'].'-'.$v['track_id']] = $v;
        }
        //是否是虚库订单
        $this->load->model('order_model');
        $order_list = $this->order_model->all_product(array('order_id'=>$order_id));
        $is_consign_num = 0;
        foreach ($order_list as $order_val) {
            $is_consign_num = $is_consign_num + $order_val->consign_num;
        }
        $data['is_consign_num'] = $is_consign_num;
        
        $arr = $this->return_model->return_product($return_id);
        foreach($arr as $v){
            $k = $v['op_id'].'-'.$v['product_id'].'-'.$v['color_id'].'-'.$v['size_id'].'-'.$v['cp_id'];
            if(isset($order_product[$k])){
                $order_product[$k]['return_rec_id'] = $v['rp_id'];
                $order_product[$k]['product_num'] += $v['product_num'];
                $order_product[$k]['consign_num'] += $v['consign_num'];
                $order_product[$k]['return_product_num'] += $v['product_num'];
                $order_product[$k]['return_consign_num'] += $v['consign_num'];
                $order_product[$k]['formated_subtotal'] = $v['formated_subtotal'];
                
                $order_product[$k]['depot_name'] = $v['depot_name'];
                $order_product[$k]['location_name'] = $v['location_name'];
            }else{
                $v['return_rec_id'] = $v['rp_id'];
                $v['rec_id'] = $v['op_id'];
                $v['track_id'] = $v['cp_id'];
                $v['return_product_num'] = $v['product_num'];
                $v['return_consign_num'] = $v['consign_num'];
                $order_product[$k] = $v;
            }
        }
        $data['return_product'] = $order_product;
        $voucher = $this->return_model->get_voucher_payment($order_id);
        if(!empty($voucher)) {
            $voucher['product_arr'] = empty($voucher['product'])?array():explode(',',$voucher['product']);
            $voucher['category_arr'] = empty($voucher['category'])?array():explode(',',$voucher['category']);
            $voucher['brand_arr'] = empty($voucher['brand'])?array():explode(',',$voucher['brand']);
        }
        $voucher_product_amount = 0;

        foreach($order_product as $key=>$product) {
            if(!empty($voucher) && $product['shop_price']==$product['product_price'] && $product['package_id']==0 && ($voucher['product_arr']===array()||in_array($product['product_id'],$voucher['product_arr']))&&($voucher['category_arr']===array()||in_array($product['category_id'],$voucher['category_arr']))&&($voucher['brand_arr']===array()||in_array($product['brand_id'],$voucher['brand_arr']))) {
                $product['product_name'] = "<font color='red'>[券]</font> ".$product['product_name'];
                $voucher_product_amount += $product['product_num']*$product['product_price'];
            }
            $order_product[$key] = $product;
        }
        if(!empty($voucher) && $voucher['payment_money']==$voucher_product_amount) {
            $product_alert = "<font color='red'>该订单涉及现金券的商品如果要退货需要全部退。</font>";
            $data['product_alert'] = $product_alert;
        }
        //查询用户运费信息//add by shangguannan 2013-04-18
        $this->load->model('return_user_shipping_fee_model');
        $user_shipping_fee = $this->return_user_shipping_fee_model->filter(array('return_id'=>$return_id));
        $user_shipping_fee_info = array();
        if($user_shipping_fee){
            $user_shipping_fee_info['has_fee'] = true;
            $user_shipping_fee_info['fee'] = $user_shipping_fee->user_shipping_fee;
        }else{
            $user_shipping_fee_info['has_fee'] = false;
            $user_shipping_fee_info['fee'] = '';
        }
        $data['user_shipping_fee_info'] = $user_shipping_fee_info;
        //add by shangguannan 2013-04-18
    /* 取得能执行的操作列表 */
        $operable_list = $this->return_model->get_return_perm($return);
        $data['operable_list'] = $operable_list;

        //取得voucher_back
        $voucher_back = 'deduct';
        $back_rs = $this->return_model->get_return_voucher_back($return_id);
        if(!empty($back_rs)) {
            $voucher_back = 'payback';
        }
        $data['voucher_back'] = $voucher_back;


    	/* 是否打印退货单，分别赋值 */
    	$print = $this->input->post('print');
        if ($print == 1) {

        	$data['shop_name'] = 'yueyawang';
        	$data['shop_url'] = FRONT_URL;
        	$data['shop_address'] = '';
        	$data['service_phone'] = '4000-966-021';
        	$data['print_time'] = date('Y-m-d H:i:s');
        	$data['action_user'] = $this->admin_id;

            $return_product = $this->return_model->get_return_product_location($return);
            $data['return_product'] = $return_product;
            $this->load->view('order_return/print', $data);
        }
        else {
        	$data['action_list'] = $this->return_model->get_action_list($return_id);
        	$data['return_reasons'] = json_encode($this->return_model->get_return_reasons());
        	$this->load->helper('form');
            $this->load->view('order_return/edit', $data);
        }

	}

	public function add ($order_id = 0, $apply_id = 0)
	{
		auth('order_return_edit');
		$data = array();
		if (!empty($order_id))
		{
			$order_info = $this->return_model->filter_order(array('order_id'=>$order_id));
			$data['order_info'] = $order_info;
		}
                $data['apply_id'] = $apply_id;
		$this->load->helper('form');
		$this->load->view('order_return/add', $data);
	}

	public function pay ($return_id = 0)
	{
		auth('order_return_pay');
		$return = $this->return_model->return_info($return_id);
		$data = array();
		$data['return_id'] = $return_id;
		$data['return'] = $return;

        $pay_arr = $this->return_model->get_pay_detail_arr($return_id,1);
        $data['pay_arr'] = $pay_arr;
		$data['pay_type_arr'] = $this->return_model->get_pay_arr();

		$this->load->helper('form');
		$this->load->view('order_return/return_pay', $data);
	}

	public function pay_post()
	{
		auth('order_return_pay');
		$all_post = $this->input->post();
		$filter = $this->uri->uri_to_assoc(3);
		$return_id = $filter['return_id'];

		$payment['payment_money']=floatval($all_post['payment_amount']);
        $payment['payment_date'] = date('Y-m-d H:i:s');
        $payment['pay_id'] = intval($all_post['pay_id']);
        $payment['payment_admin'] = $this->admin_id;
        $payment['order_id'] = $return_id;
        $payment['payment_remark'] = trim($all_post['payment_desc']);
        $payment['is_return'] = 1;
        $pay = $this->return_model->filter_payment(array('pay_id'=>$payment['pay_id']));
        $return = $this->return_model->return_info($return_id);
        $order_id=$return['order_id'];

        if ('deduct'==$pay['pay_code'])
        {
            $deduct_type=trim($all_post['deduct_type'])=='其它'?trim($all_post['deduct_type_express']):trim($all_post['deduct_type']);
            $deduct_no='';
            $payment['payment_remark']=$deduct_type."$$$@$$$".$deduct_no."$$$@$$$".$payment['payment_remark'];
        }
        $this->db->query('BEGIN');
        //锁定退货单表
        $this->db->query("SELECT * FROM ".$this->db->dbprefix('order_return_info')." WHERE order_id= '".$order_id."' FOR UPDATE");
        //权限判断
        $operable_list = $this->return_model->get_return_perm($return);
        if (!$operable_list['pay_list'])
        {
            sys_msg('没有权限',1);
        }

        if (in_array($pay['pay_code'],array('payback','deduct')))
        {
        	//返扣款在数据库中只存负值
            $payment['payment_money'] = (-1)*abs($payment['payment_money']);
        }
        $this->return_model->insert_payment($payment);

        //更新退货单的财务状态
        $this->return_model->update_return_amount($return_id);
        $this->db->query('COMMIT');
        redirect('/order_return/pay/'.$return_id);
	}

	public function pay_remove()
	{
		auth('order_return_pay');
		$all_post = $this->input->post();
		$filter = $this->uri->uri_to_assoc(3);
		$return_id = $filter['return_id'];
		$payment_id = $filter['payment_id'];

        $return = $this->return_model->return_info($return_id);
        $order_id = $return['order_id'];
        $this->db->query('BEGIN');
        //锁定退货单表
        $this->db->query("SELECT * FROM ".$this->db->dbprefix('order_return_info')." WHERE order_id= '".$order_id."' FOR UPDATE");
        $operable_list = $this->return_model->get_return_perm($return);
        if (!$operable_list['pay_list'])
        {
            sys_msg('没有权限',1);
        }
		$payment = $this->return_model->filter_order_payment(array('payment_id'=>$payment_id));
        if ($payment['payment_admin']== -1)
        {
            sys_msg("系统支付，不能人工删除！",1);
        }
        $this->return_model->delete_payment(array('payment_id'=>$payment_id));

        //更新退货单的财务状态
        $this->return_model->update_return_amount($return_id);
        $this->db->query('COMMIT');
		redirect('/order_return/pay/'.$return_id);

	}

	public function auto_deduct ()
	{
		$all_post = $this->input->post();
		$return_id = trim($all_post['return_id']);
        $return = $this->return_model->return_info($return_id);
        if (empty($return))
        {
            die(json_encode(array('error'=>1,'msg'=>'退单不存在')));
        }
        $order_id = $return['order_id'];

        $this->db->query('BEGIN');
        $this->db->query("SELECT * FROM ".$this->db->dbprefix('order_return_info')." WHERE order_id= '".$order_id."' FOR UPDATE");
        $order = $this->return_model->get_order_info($order_id);
        if ($order['pay_status']!=1)
        {
            die(json_encode(array('error'=>1,'msg'=>'订单未财审，不能计算折扣')));
        }
        $discount_payment = $this->return_model->auto_discount_payment($return_id);
        if ($discount_payment['error']!=0)
        {
            die(json_encode(array('error'=>1,'msg'=>$discount_payment['message'])));
        }
        if (!empty($discount_payment['discount_payment']))
        {
        	//是否已存在该支付，如果存在，否则update
            $pay_code = $discount_payment['discount_payment']['pay_code'];
            $pay_info = $this->return_model->filter_payment(array('pay_code'=>$pay_code));
            $pay_id = $pay_info['pay_id'];
            $payment_amount = -1 * $discount_payment['discount_payment']['payment_money'];
            $return_payment = $this->return_model->filter_order_payment(array('is_return'=>1,'order_id'=>$return_id,'pay_id'=>$pay_id));
            if (!empty($return_payment))
            {
                $payment_id = $return_payment['payment_id'];
                $this->return_model->update_order_payment(array('payment_money'=>$payment_amount),array('payment_id'=>$payment_id));
            }else {
                $arr = array(
                    'is_return'=>1,
                    'order_id'=>$return_id,
                    'pay_id'=>$pay_id,
                    'payment_money'=>$payment_amount,
                    'payment_admin'=>-1,
                    'payment_date'=>date('Y-m-d H:i:s')
                );
                $payment_id = $this->return_model->insert_payment($arr);
            }
        }else {
            die(json_encode(array('error'=>1,'msg'=>'无需扣除折扣！')));
        }
        $this->return_model->update_return_amount($return_id);
        $this->db->query('COMMIT');
        die(json_encode(array('error'=>0,'msg'=>'成功扣除折扣！')));
	}

	public function post_suggest ($return_id)
	{
		$all_post = $this->input->post();
        $suggestion_type=intval($all_post['suggestiontype_id']);
        $suggestion_content=$all_post['suggestion_content'];
        $suggestion_item=array
            (
            'type_id'=>$suggestion_type,
            'advice_content'=>$suggestion_content,
            'order_id'=>$return_id,
            'advice_admin'=>$this->admin_id,
            'advice_date'=>date('Y-m-d H:i:s'),
            'is_return'	=> 2
        );
        $advice_id = $this->return_model->insert_advice($suggestion_item);
        if ($advice_id > 0)
        {
            $links[0]['text'] = '返回退货单明细';
            $links[0]['href'] = "/order_return/edit/".$return_id;
            sys_msg('添加意见成功！',0,$links);
        }else {
            sys_msg('Post Advice Error!',1);
        }
	}

	public function test ()
	{
		$this->load->view('order_return/operate');
	}

	public function operate ()
	{

		$all_post = $this->input->post();
		$data = array();
		/* 取得退货单id（可能是多个，多个sn）和操作备注（可能没有） */
        $return_id       = $all_post['return_id'];
        $return = $this->return_model->return_info($return_id);
        $action_note    = isset($all_post['action_note']) ? trim($all_post['action_note']) : '';

        if (isset($all_post['lock'])) {/* 锁定 */
            $jump           = true;
            $action         = '锁定';
            $operation      = 'lock';
        }
        if (isset($all_post['unlock'])) { /* 解锁 */
            $jump           = true;
            $action         = '解锁';
            $operation      = 'unlock';
        }
        if (isset($all_post['service_confirm'])) { /* 客服审核 */
            $jump           = true;
            $action         = '客审';
            $operation      = 'service_confirm';
        }
        if (isset($all_post['unservice_confirm'])) { /* 客服反审核 */
            $jump           = true;
            $action         = '反客审';
            $operation      = 'unservice_confirm';
        }
        elseif (isset($all_post['pay'])) { /* 财审 */
            $jump           = false;
            $require_note   = 1;
            $action         = '财审';
            $operation      = 'pay';
        }
	elseif (isset($all_post['ship'])) { /* 入库 */
                $tmp_links[] = array('text' => '返回退货单详情', 'href' => "/order_return/edit/".$return_id );
		$jump           = false;
		$require_note   = 1;
		$action         = '入库';
		$action_note	= '退货单入库';
		$operation      = 'ship';


		$batch_return_products = $this->return_model->get_batch_return_products( $return_id );

		// 验证退货商品的所在批次是否锁定；哪些批次是已经结算过
//		$batch_locked_result = '';
		$reckoned_batches = Array();
		for ( $i=0; $i<sizeof($batch_return_products); $i++ )
		{
			$product = $batch_return_products[$i];
			if( $product['is_reckoned'] ) 
			{
				if( isset( $reckoned_batches[$product['sub_id']] ) )
					$reckoned_batches[$product['sub_id']]  = $product['product_number'];
				else 
					$reckoned_batches[$product['sub_id']]  += $product['product_number'];

			}
//			if( $product['is_lock'] ) $batch_locked_result .= $product['product_name'].' 所在批次['.$product['batch_code'].']处于锁定状态。<br/>';
		}
//		if( !empty($batch_locked_result) ) $batch_locked_result .= "请等待批次解锁后再做入库操作";
		// 如果有批次被锁，告诉用户，请等待批次解锁后再做入库操作
//		(! empty($batch_locked_result)) && sys_msg($batch_locked_result,1,$tmp_links,FALSE);

		$return_product = $this->return_model->return_product($return_id);
		foreach($return_product as $key=>$val){
			if($val['product_num']<= $val['consign_num']){
				unset($return_product[$key]);
				continue;
			}
			$val['ctb_number'] = isset( $reckoned_batches[$val['rp_id']] )?$reckoned_batches[$val['rp_id']]:0;
			$val['real_number'] = $val['product_num'] - $val['consign_num'] - $val['ctb_number'];
			$return_product[$key] = $val;
		}
		$src_og_ids = $src_cg_ids = $src_order_depot = $src_change_depot = $src_order_goods_depot =  array();
		//取出原出库储位,以判断应该入哪个仓库
		foreach($return_product as $product){
			if($product['cp_id']==0){
				$src_og_ids[] = $product['op_id'];
			}else{
				$src_cg_ids[] = $product['cp_id'];
			}
		}

		// 处理退货单
		if(!empty($src_og_ids)){
			$rs = $this->return_model->get_order_by_subid($src_og_ids);

			if (!empty($rs))
			{
				foreach ($rs as $row)
				{
					if (!isset($src_order_depot[$row['sub_id']]))
					{
						$src_order_depot[$row['sub_id']] = $row['trans_sn'];
					}
					$src_order_depot[$row['sub_id']] .= '<br/>'.$row['depot_name'].'-'.$row['location_name'].' => '.$row['product_number'];
                    $src_order_goods_depot[$row['sub_id']] = array('depot_id' => $row['depot_id'], 
                                                                   'depot_name' => $row['depot_name'], 
                                                                   'location_id' => $row['location_id'], 
                                                                   'location_name' => $row['location_name']);                        
				}
			}
		}
		if(!empty($src_cg_ids)){
			$rs = $this->return_model->get_change_by_subid($src_cg_ids);
			if (!empty($rs))
			{
				foreach ($rs as $row)
				{
					if (!isset($src_change_depot[$row['sub_id']]))
					{
						$src_change_depot[$row['sub_id']] = $row['trans_sn'];
					}
					$src_change_depot[$row['sub_id']] .= '<br/>'.$row['depot_name'].'-'.$row['location_name'].' => '.$row['product_number'];
				}
			}
		}

		foreach($return_product as $key=>$product){
			$return_product[$key]['out_depot'] = $product['cp_id']==0?$src_order_depot[$product['op_id']]:$src_change_depot[$product['cp_id']];
                    // 虚发虚退入库至原仓原储
                    if (!$return['shipping_true']) {
                        $return_product[$key]['return_depot_id'] = $src_order_goods_depot[$product['op_id']]['depot_id'];
                        $return_product[$key]['return_depot_name'] = $src_order_goods_depot[$product['op_id']]['depot_name'];
                        $return_product[$key]['return_location_id'] = $src_order_goods_depot[$product['op_id']]['location_id'];
                        $return_product[$key]['return_location_name'] = $src_order_goods_depot[$product['op_id']]['location_name'];
                        continue;
                    }
                    if($product['ctb_number'] > 0) { //代转买
                        $return_product[$key]['return_depot_id'] = CTB_RETURN_DEPOT_ID;
                        $return_product[$key]['return_depot_name'] = CTB_RETURN_DEPOT_NAME;
                        $return_product[$key]['return_location_id'] = CTB_RETURN_DEPOT_LOCATION_ID;
                        $return_product[$key]['return_location_name'] = CTB_RETURN_DEPOT_LOCATION_NAME;
                    } else {
                        $coop = $this->return_model->get_return_product_cooperation($product['rp_id']);
                        if ($coop->provider_cooperation == COOPERATION_TYPE_COST) { //买断(XXX:暂未使用)
                            $return_product[$key]['return_depot_id'] = MD_RETURN_DEPOT_ID;
                            $return_product[$key]['return_depot_name'] = MD_RETURN_DEPOT_NAME;
                            $return_product[$key]['return_location_id'] = MD_RETURN_DEPOT_LOCATION_ID;
                            $return_product[$key]['return_location_name'] = MD_RETURN_DEPOT_LOCATION_NAME;
                        } elseif ($coop->provider_cooperation == COOPERATION_TYPE_CONSIGN) { //代销(XXX:暂未使用)
                            $return_product[$key]['return_depot_id'] = DT_RETURN_DEPOT_ID;
                            $return_product[$key]['return_depot_name'] = DT_RETURN_DEPOT_NAME;
                            $return_product[$key]['return_location_id'] = DT_RETURN_DEPOT_LOCATION_ID;
                            $return_product[$key]['return_location_name'] = DT_RETURN_DEPOT_LOCATION_NAME;
                        } elseif ($coop->provider_cooperation == COOPERATION_TYPE_TMALL) { //天猫发货
                            $return_product[$key]['return_depot_id'] = DEPOT_ID_TMALL_RETURN;
                            $return_product[$key]['return_depot_name'] = DEPOT_NAME_TMALL_RETURN;
                            $return_product[$key]['return_location_id'] = LOCATION_ID_TMALL_RETURN;
                            $return_product[$key]['return_location_name'] = LOCATION_NAME_TMALL_RETURN;
                        }  elseif ($coop->provider_cooperation == COOPERATION_TYPE_FW_VIRTUAL) { //MT服务(虚库)
                            $return_product[$key]['return_depot_id'] = DEPOT_ID_FW_VIRTUAL_RETURN;
                            $return_product[$key]['return_depot_name'] = DEPOT_NAME_FW_VIRTUAL_RETURN;
                            $return_product[$key]['return_location_id'] = LOCATION_ID_FW_VIRTUAL_RETURN;
                            $return_product[$key]['return_location_name'] = LOCATION_NAME_FW_VIRTUAL_RETURN;
                        }  elseif ($coop->provider_cooperation == COOPERATION_TYPE_MT_REAL) { //MT代销(实库)
                            $return_product[$key]['return_depot_id'] = DEPOT_ID_MT_REAL_RETURN;
                            $return_product[$key]['return_depot_name'] = DEPOT_NAME_MT_REAL_RETURN;
                            $return_product[$key]['return_location_id'] = LOCATION_ID_MT_REAL_RETURN;
                            $return_product[$key]['return_location_name'] = LOCATION_NAME_MT_REAL_RETURN;
                        }  elseif ($coop->provider_cooperation == COOPERATION_TYPE_MT_VIRTUAL) { //MT服务(虚库)
                            $return_product[$key]['return_depot_id'] = DEPOT_ID_MT_VIRTUAL_RETURN;
                            $return_product[$key]['return_depot_name'] = DEPOT_NAME_MT_VIRTUAL_RETURN;
                            $return_product[$key]['return_location_id'] = LOCATION_ID_MT_VIRTUAL_RETURN;
                            $return_product[$key]['return_location_name'] = LOCATION_NAME_MT_VIRTUAL_RETURN;
                        }  elseif ($coop->provider_cooperation == COOPERATION_TYPE_THIRD) { //MT服务(虚库)
                            $return_product[$key]['return_depot_id'] = THIRD_DEPOT_ID;
                            $return_product[$key]['return_depot_name'] = THIRD_DEPOT_NAME;
                            $return_product[$key]['return_location_id'] = THIRD_DEPOT_LOCATION_ID;
                            $return_product[$key]['return_location_name'] = LOCATION_NAME_THIRD_RETURN;
                        }
                    }
		}
		$data['return_product'] = $return_product;
		$data['depot_arr'] = get_default_return_depot(); // array(RETURN_DEPOT_ID=>RETURN_DEPOT);
		$data['depot_ctb_arr'] = get_ctb_return_depot();// array(CTB_RETURN_DEPOT_ID=>'代销转买断退货仓 [不可售]');
	}


        elseif (isset($all_post['invalid'])) {/* 作废 */
            $jump           = false;
            $require_note   = 1;
            $action         = '作废';
            $operation      = 'invalid';
        }

        elseif (isset($all_post['is_ok'])) {/* 完结 */
            $jump           = false;
            $require_note   =  1;
            $action         = '完结';
            $operation      = 'is_ok';
        }



        if (!$jump)
        {  /* 直接处理还是跳到详细页面 */
        	$data['require_note'] = $require_note;
        	$data['action_note'] = $action_note;
        	$data['show_invoice_no'] = isset($show_invoice_no);
        	$data['return_id'] = $return_id;
        	$data['operation'] = $operation;
        	$data['return'] = $return;

       		$this->load->helper('form');
			$this->load->view('order_return/operate', $data);
        }
        else
        {
        	redirect('/order_return/operate_post/return_id/'.$return_id.'/operation/'.$operation.'/action_note/'.urlencode($action_note));
        }
	}

	public function operate_post ()
	{
		$all_post = $this->uri->uri_to_assoc(3);
		$all_post_other = $this->input->post();
        $aid = $this->admin_id;
        $return_id   = isset($all_post['return_id'])?$all_post['return_id']:$all_post_other['return_id'];        // 退货单id
        $operation  = isset($all_post['operation'])?$all_post['operation']:$all_post_other['operation'];	 // 退货单操作
        $action_note = isset($all_post['action_note'])?$all_post['action_note']:(isset($all_post_other['action_note'])?$all_post_other['action_note']:'');
        $return = $this->return_model->return_info($return_id);
        $order_id = $return['order_id'];
        $order = $this->return_model->get_order_info($order_id);

        $this->db->query('BEGIN');
		$this->db->query('SELECT * FROM '.$this->db->dbprefix('order_info')." WHERE order_id = '".$order_id."' FOR UPDATE");

		$operable_list = $this->return_model->get_return_perm($return);
        if (!$operable_list[$operation])
        {
            $links[0]['text'] = '返回退货单明细';
            $links[0]['href'] = "/order_return/edit/".$return_id;
            sys_msg('此退货单的状态已被他人更改，您不再具有此次操作的权限，请刷新后再进行操作。',1,$links);
        }
        switch($operation)
        {
            case 'lock':/* 锁定 */
                $arr['lock_admin'] = $aid;
                $arr['lock_date'] = date('Y-m-d H:i:s');
                $this->return_model->update($arr, $return_id);

                $action_note = '锁定退货单'.' '.$action_note;/* 记录log */
                $this->return_model->insert_action($return,$action_note);
                break;
            case 'unlock':/* 解锁 */
                $arr['lock_admin'] = 0;
                $arr['lock_date'] = '';
                $this->return_model->update($arr, $return_id);

                $action_note = '解锁退货单'.' '.$action_note;/* 记录log */
                $this->return_model->insert_action($return,$action_note);
                break;

            case 'service_confirm':/* 客服审核 */
            	$this->return_model->update(array('return_status' => 1, 'confirm_admin' => $aid, 'confirm_date' =>date('Y-m-d H:i:s'), 'lock_admin' => 0, 'lock_date' => ''), $return_id);

                $action_note = '客服审核退货单'.' '.$action_note;/* 记录log */
                $return_copy = $return;
                $return_copy['return_status'] = 1;
                $this->return_model->insert_action($return_copy,$action_note);
                break;

            case 'unservice_confirm':/* 客服反审核 */
            	$this->return_model->update(array('return_status' => 0, 'confirm_admin' => 0, 'confirm_date' =>'', 'lock_admin' => 0, 'lock_date' => ''), $return_id);

                $action_note = '客服反审核退货单'.' '.$action_note;/* 记录log */
                $return_copy = $return;
                $return_copy['return_status'] = 0;
                $this->return_model->insert_action($return_copy,$action_note);
                break;

            case 'pay':/* 财务审核 */

                $user_id=$return['user_id'];
                $this->db->query('SELECT * FROM '.$this->db->dbprefix('user_info')." WHERE user_id = '".$user_id."' FOR UPDATE");
                $order = $this->return_model->get_order_info($order_id);
                if ($order['pay_status'] != 1)
                {
                    sys_msg('退货单必须在原订单财审后才可以进行财审，请先处理订单！',1,array(array('href'=>"/order_return/edit/".$return_id,'text'=>'返回退单明细页')));
                }
                $return_discount_payment = $this->return_model->auto_discount_payment($return_id);
                if ($return_discount_payment['error']!=0)
                {
                    sys_msg($return_discount_payment['message'],1);
                }
                if (!empty($return_discount_payment['discount_payment']))
                {
                    $payment = $return_discount_payment['discount_payment'];
                    $pay_code = $payment['pay_code'];
                    $payment_amount = -1 * round($payment['payment_money'],2);
                    $rs = $this->return_model->query_order_payment($payment_amount,$pay_code,$return_id);
                    if (empty($rs))
                    {
                        sys_msg('退单未扣除应扣折扣，请返回修改！',1,array(array('href'=>"/order_return/edit/".$return_id,'text'=>'返回退单明细页')));
                    }
                }

                $arr = array(
                    'pay_status'=>1,
                    'finance_admin'=>$aid,
                    'finance_date'=>date('Y-m-d H:i:s'),
                    'lock_admin'=>0,
                    'is_ok'=>1,
                    'is_ok_date'=>date('Y-m-d H:i:s'),
                    'is_ok_admin'=>$aid
                );
                $this->return_model->update($arr, $return_id);
                $arr = array('finance_check_date'=>date('Y-m-d H:i:s'),'finance_check_admin'=>$aid);// Update transaction
				$this->return_model->update_transaction($arr,array('trans_type'=>TRANS_TYPE_RETURN_ORDER,'trans_sn'=>$return['return_sn'],'trans_status'=>TRANS_STAT_AWAIT_IN));
				$this->return_model->update_transaction($arr,array('trans_type'=>TRANS_TYPE_RETURN_ORDER,'trans_sn'=>$return['return_sn'],'trans_status'=>TRANS_STAT_IN));

                $return_payment_group = $this->return_model->get_return_payment_group($return_id);
                // 更新用户帐户变动表
                if($return_payment_group['payback']['payment_money']<0) {
                    $payment_amount = -1*$return_payment_group['payback']['payment_money'];
                    $this->return_model->log_account_change($return['user_id'], $payment_amount, 0, 0, '退货单'.$return['return_sn'].'返款', 'money_return_payback');
                }
                if($return['return_shipping_fee']>0) {
                    $this->return_model->log_account_change($return['user_id'], $return['return_shipping_fee'], 0, 0, '退货单'.$return['return_sn'].'返还原订单运费', 'money_return_payback');
                }

                //返券
                if($return_payment_group['voucher_payback']['payment_money']<0) {
                    $voucher_payment = $this->return_model->get_voucher_payment($order_id);
                    $release_id = $voucher_payment['release_id'];

                    //$release = $this->return_model->filter_voucher_release(array('release_id'=>$release_id));

                    $campaign_id = $voucher_payment['campaign_id'];
                    $user_id = $return['user_id'];
                    $now = date('Y-m-d 00:00:00');
                    $end_time = date('Y-m-d H:i:s',strtotime($now)+ strtotime($voucher_payment['end_date']) - strtotime($voucher_payment['start_date'])) ;
                    $voucher_amount = $voucher_payment['voucher_amount'];
                    $min_order = $voucher_payment['min_order'];
                    $sql = "INSERT INTO  ".$this->db->dbprefix('voucher_record')." SET" .
                    		" voucher_sn = LPAD(CAST(FLOOR(RAND()*1000000000000) AS CHAR(12)),12,'0')," .
                    		" user_id = '".$user_id."', campaign_id = '".$campaign_id."', release_id = '".$release_id."', " .
                    		" start_date = '".$now."', end_date = '".$end_time."', repeat_number = 1, voucher_amount = '".$voucher_amount."'," .
                    		" min_order = '".$min_order."', create_date = '".date('Y-m-d H:i:s')."', create_admin = -1";
                    do {

                        if($this->db->query($sql)!==false) {
                            break;
                        }
                        else {
                            if ($this->db->errno() != 1062) {
                                $result['error'] = 1;
                                $result['message'] =$this->db->error();//没有活动
                                return $result;
                            }
                        }
                    }while (true); // 防止券号号重复

                    $voucher_id = $this->db->insert_id();
                    $sql = "";
                    $voucher_info = $this->return_model->filter_voucher(array('voucher_id'=>$voucher_id));
                    $voucher_sn = $voucher_info['voucher_sn'];
                    $this->db->query("UPDATE ".$this->db->dbprefix('voucher_release')." SET voucher_count=voucher_count+1 where release_id = '".$release_id."'");

                    $sql = "UPDATE ".$this->db->dbprefix('order_payment')." as rp LEFT JOIN ".$this->db->dbprefix('payment_info')." as p ON p.pay_id = rp.pay_id" .
                    		" SET rp.payment_account = '$voucher_sn' WHERE rp.is_return=1 and rp.order_id = '".$return_id."' and p.pay_code = 'voucher_payback'";
                    $this->db->query($sql);
                }
                
                //如果有申请单，将完结。
                if(!empty($return['apply_id'])) {
                        $this->load->model('apply_return_model');
                        $finish_num = $this->apply_return_model->finish_return_apply($return['apply_id']);
                        $apply_info = $this->apply_return_model->apply_info($return['apply_id']);
                        if($finish_num >= $apply_info['product_number']) {
                                $up_apply = array('apply_status'=>2);
                                $this->apply_return_model->update($up_apply, $return['apply_id']);
                        }
                }

                $action_note = '财务审核'.' '.$action_note.' 退货单自动完结';
                $return_copy = $return;
                $return_copy['pay_status'] = 1;
                $this->return_model->insert_action($return_copy,$action_note);
                 break;

            case 'ship': /* 入库，如果订单已财审，则退货单自动财审 */

                $tmp_links[] = array('text' => '返回退货单详情', 'href' => "/order_return/edit/".$return_id );
                $batch_return_products = $this->return_model->get_batch_return_products( $return_id );
		
		// 验证退货商品的所在批次是否锁定；哪些批次是已经结算过
//		$batch_locked_result = '';
		$reckoned_batches = Array();
		for ( $i=0; $i<sizeof($batch_return_products); $i++ )
		{
			$product = $batch_return_products[$i];
			if( $product['is_reckoned'] ) 
			{
				// $product['sub_id'] 是退货商品表的主键
				$reckoned_batches[$product['sub_id']]['ctb_number']  = $product['product_number'];
				array_push($reckoned_batches[$product['sub_id']], $product);
			}
//			if( $product['is_lock'] ) $batch_locked_result .= $product['product_name'].' 所在批次['.$product['batch_code'].']处于锁定状态。<br/>';
		}
//		if( !empty($batch_locked_result) ){
//			$batch_locked_result .= "请等待批次解锁后再做入库操作";
//		}
		// 如果有批次被锁，告诉用户，请等待批次解锁后再做入库操作
//		(!empty($batch_locked_result)) && sys_msg($batch_locked_result,1,$tmp_links,FALSE);


                //$back_depot_arr = get_back_depot_arr();
                $trans_arr = array(); $ctb_trans_arr = array();
                $location_ids = array();

		// 指定仓库／储位 1: 正常商品
		if (isset($all_post_other['rec_id']) && !empty($all_post_other['rec_id']))
		{
			foreach ($all_post_other['rec_id'] as $key=>$rec_id)
			{
				$location_id = $all_post_other['location_id'][$key];
				
				//TODO BABY-235 退货入库-仓库属性校验
				$depot_id = $all_post_other['depot_id'][$key];
				$cooperation = $this->return_model->get_return_product_cooperation($rec_id);
				$depot = $this->depot_model->filter_depot(array('depot_id'=>$depot_id));
				//echo "cooperation: ".$cooperation->provider_cooperation."---".$depot->cooperation_id;die;
				if(!empty($cooperation) && !empty($depot)) {
					if($cooperation->provider_cooperation != $depot->cooperation_id) {
						sys_msg('退货商品合作方式与仓库属性不一致',1,$tmp_links);
					}
				}
				
				$num = $all_post_other['rec_num'][$key];
				$trans_arr[$rec_id] = array('rec_id'=>$rec_id,'location_id'=>$location_id, 'product_number'=>$num);
				$location_ids[] = $location_id;
			}

		}
		// 指定仓库／储位 2: 代销转买断商品
		if (isset($all_post_other['ctb_rec_id']) && !empty($all_post_other['ctb_rec_id']))
		{
			foreach ($all_post_other['ctb_rec_id'] as $key=>$rec_id)
			{
				$location_id = $all_post_other['ctb_location_id'][$key];
				
				$depot_id = $all_post_other['ctb_depot_id'][$key];
				$cooperation = $this->return_model->get_return_product_cooperation($rec_id);
				$depot = $this->depot_model->filter_depot(array('depot_id'=>$depot_id));
				//echo "cooperation: ".$cooperation->provider_cooperation."---".$depot->cooperation_id;die;
				if(!empty($cooperation) && !empty($depot)) {
					if($cooperation->provider_cooperation != $depot->cooperation_id) {
						sys_msg('退货商品合作方式与仓库属性不一致',1,$tmp_links);
					}
				}
				
				$num = $all_post_other['ctb_rec_num'][$key];
				$ctb_trans_arr[$rec_id] = array('rec_id'=>$rec_id,
						'location_id'=>$location_id
						,'product_number'=>$num
						,'product_name'=>$reckoned_batches[$rec_id]['product_name']
						);
				$location_ids[] = $location_id;
			}
			$location_ids = array_unique( $location_ids );
		}

                /* 更新发货时间和发货单号 */
                $arr['shipping_status']     = 1;
                $arr['shipping_date']       = date('Y-m-d H:i:s');
                $arr['shipping_admin']       = $aid;
                $arr['invoice_no']          = isset($all_post_other['invoice_no'])?trim($all_post_other['action_note']):'';
                $arr['lock_admin']          = 0;
                $arr['lock_date']          = '';
                $this->return_model->update($arr, $return_id);
                
		// 确认仓库和储位的合法性
		if( !empty( $location_ids ) )
		{
			$rs = $this->return_model->get_depot_by_location($location_ids);
			$location_arr = array();
			if (!empty($rs))
			{
				foreach ($rs as $row)
				{
					$location_arr[$row['location_id']] = $row;
				}
			}
			// normal products; $key=product_sub的rec_id
			foreach ($trans_arr as $key=>$trans) {
				$trans_location = array_merge($location_arr[$trans['location_id']],$trans);

				empty($trans_location) && sys_msg('储位错误',1,$tmp_links);
				$trans_location['rec_id'] = $key;
				$trans_arr[$key] = $trans_location;
			}
			// ctb products; $key=product_sub的rec_id
			foreach ($ctb_trans_arr as $key=>$trans) {
				$trans_location = array_merge($location_arr[$trans['location_id']],$trans);
				empty($trans_location) && sys_msg('储位错误',1,$tmp_links);
				$trans_location['rec_id'] = $key;
				$ctb_trans_arr[$key] = $trans_location;
			}
		}



		$result_rs = $this->return_model->filter_transaction_all(
				array('trans_type'=>TRANS_TYPE_RETURN_ORDER, 
					'trans_status'=>TRANS_STAT_AWAIT_IN, 
					'trans_sn'=>$return['return_sn']
				     )
				);
		/**
		 * 正常商品入库的实际库存；CTB入库操作记录的覆盖和新增
		 * 正常商品自动入库完结；如果有代销转买断商品要扣除CTB数量
		 * 
		 */
                if (!empty($result_rs))
                {
                    $to_add_rec = array();
                    $ctb_unsale_rec = array();
                    $ctbArray = array();
                    foreach ($result_rs as $key=>$val)
                    {
                        if (!isset($trans_arr[$val['sub_id']]) && !isset($ctb_trans_arr[$val['sub_id']]))
                        {
                            $this->db->query('ROLLBACK');
                            sys_msg('储位信息丢失！',1,$tmp_links);exit;
                        }
			// 有正常商品和CTB商品同时存在
			if( isset($trans_arr[$val['sub_id']]) && isset($ctb_trans_arr[$val['sub_id']])  )
			{
				if ( isset($trans_arr[$val['sub_id']]) ) $trans = $trans_arr[$val['sub_id']];
				// 正常商品 transaction 的赋值 
				$setter = array('trans_status'=>TRANS_STAT_IN
						,'depot_id'=>$trans['depot_id'],'location_id'=>$trans['location_id']
						,'update_admin'=>$aid,'update_date'=>date('Y-m-d H:i:s')
					//	,'product_number'=>$trans['product_number']
					       );
				if ( isset($ctb_trans_arr[$val['sub_id']]) ) $ctb_trans = $ctb_trans_arr[$val['sub_id']];
				// CTB商品 transaction 的赋值 
				$ctbSetter = array('trans_status'=>TRANS_STAT_IN
						,'depot_id'=>RETURN_DEPOT_ID,'location_id'=>RETURN_DEPOT_LOCATION_ID
						,'update_admin'=>$aid,'update_date'=>date('Y-m-d H:i:s')
					//	,'product_number'=>$ctb_trans['product_number']
					       );
				// 正常商品可售，CTB不可售：要去掉CTB不可售
				if($trans['depot_type']==1 && $ctb_trans['depot_type']==0)
				{
					array_push ( $ctb_unsale_rec, Array( 
								'product_id'=>$val['product_id'], 
								'color_id'=>$val['color_id'], 
								'size_id'=>$val['size_id'], 
								'num'=>$ctb_trans['num'] 
								) 
						  );
				}
				$this->return_model->update_transaction_copy($setter,$ctbSetter ,array('transaction_id'=>$val['transaction_id']));
			}else{
				if ( isset($ctb_trans_arr[$val['sub_id']]) ) {
					$trans = $ctb_trans_arr[$val['sub_id']];
					// transaction 的赋值 
					$setter = array('trans_status'=>TRANS_STAT_IN
							,'depot_id'=>RETURN_DEPOT_ID,'location_id'=>RETURN_DEPOT_LOCATION_ID
							,'update_admin'=>$aid,'update_date'=>date('Y-m-d H:i:s')
							//,'product_number'=>$trans['product_number']
						       );
				}

				if ( isset($trans_arr[$val['sub_id']]) ) {
					$trans = $trans_arr[$val['sub_id']];
					// transaction 的赋值 
					$setter = array('trans_status'=>TRANS_STAT_IN
							,'depot_id'=>$trans['depot_id'],'location_id'=>$trans['location_id']
							,'update_admin'=>$aid,'update_date'=>date('Y-m-d H:i:s')
						//	,'product_number'=>$trans['product_number']
						       );
				}

				$this->return_model->update_transaction($setter ,array('transaction_id'=>$val['transaction_id']));
			}
			// 这里面的数组是要生成新商品。做ctb的
			if ( isset($ctb_trans_arr[$val['sub_id']]) ) {
				array_push( $ctbArray, array_merge( $val, $ctb_trans ) );
			}


			// 这个depot_type是什么意思，仓库是不是可售
			if($trans['depot_type']==1)
			{
				$to_add_rec[] = $trans['rec_id'];
			}
                    }
		    // gl_num :1 可售商品，要更新可售库存。要扣掉CTB入库仓为不可售的库存
                    if (!empty($to_add_rec))
                    {
                    	$this->return_model->update_productsub_by_recid($to_add_rec);
                    }
		    // gl_num :2 可售商品，要更新可售库存。要扣掉CTB入库仓为不可售的库存
                    if (!empty($ctb_unsale_rec))
                    {
                    	$this->return_model->update_productsub_by_ctb_unsale($ctb_unsale_rec);
                    }
                }
                $this->return_model->update_productsub_by_returnid($return_id);

		// 查看哪些批次是已经结算过的，准备做代销转买断 
		// CTB 生成新的商品
		if( !empty($ctbArray) ) {
			sys_ctb_operation( $ctbArray );
		}



		//下面处理退货财务审核
                if ($order['pay_status']==1)
                {
                    //自动扣除折扣
                    $discount_payment = $this->return_model->auto_discount_payment($return_id);
                    if ($discount_payment['error'] != 0) {
                        die(json_encode(array('error' => 1, 'msg' => $discount_payment['message'])));
                    }
                    if (!empty($discount_payment['discount_payment']))
                    {
                        $pay_code = $discount_payment['discount_payment']['pay_code'];
                        $pay_info = $this->return_model->filter_payment(array('pay_code'=>$pay_code));
						$pay_id = $pay_info['pay_id'];

                        $payment_amount = -1 * $discount_payment['discount_payment']['payment_money'];

                        $return_payment = $this->return_model->filter_order_payment(array('is_return'=>1,'order_id'=>$return_id,'pay_id'=>$pay_id));
                        if (!empty($return_payment))
                        {
                            $payment_id = $return_payment['payment_id'];
                            $this->return_model->update_order_payment(array('payment_money' => $payment_amount),array('payment_id'=>$payment_id));

                        } else {
                            $arr = array(
                                'is_return' => 1,
                                'order_id' => $return_id,
                                'pay_id' => $pay_id,
                                'payment_money' => $payment_amount,
                                'payment_admin' => -1,
                                'payment_date' => date('Y-m-d H:i:s')
                            );
                            $payment_id = $this->return_model->insert_payment($arr);
                        }
                        $this->return_model->update_return_amount($return_id);
                        $return = $this->return_model->return_info($return_id);
                    }
                    //添加返款
                    if ($return['returned_amount'] > 0) {
                        $arr = array(
                            'is_return' => 1,
                            'order_id' => $return_id,
                            'pay_id' => 8,//返款
                            'payment_money' => -1*$return['returned_amount'],
                            'payment_admin' => -1,
                            'payment_date' => date('Y-m-d H:i:s')
                        );
                        $this->return_model->insert_payment($arr);

                        $this->return_model->log_account_change($return['user_id'], $return['returned_amount'], 0, 0, '退货单'.$return['return_sn'].'返款', 'money_return_payback');
                        $this->return_model->update_return_amount($return_id);
                        $return = $this->return_model->return_info($return_id);
                    }
                    //返运费
                    if ($return['return_shipping_fee'] > 0) {
                        $this->return_model->log_account_change($return['user_id'], $return['return_shipping_fee'], 0, 0, '退货单' . $return['return_sn'] . '返还原订单运费', 'money_return_payback');
                    }
                    //返券
                    $return_payment_group = $this->return_model->get_return_payment_group($return_id);
                    if ($return_payment_group['voucher_payback']['payment_money'] < 0) {
                        $voucher_payment = $this->return_model->get_voucher_payment($order_id);
                        $release_id = $voucher_payment['release_id'];
                        //$release = $this->return_model->filter_voucher_release(array('release_id'=>$release_id));
                        $campaign_id = $voucher_payment['campaign_id'];
                        $user_id = $return['user_id'];
                        $now = date('Y-m-d 00:00:00');
	                    $end_time = date('Y-m-d H:i:s',strtotime($now)+ (strtotime($voucher_payment['end_date']) - strtotime($voucher_payment['start_date']))) ;
	                    $voucher_amount = $voucher_payment['voucher_amount'];
	                    $min_order = $voucher_payment['min_order'];

						$sql = "INSERT INTO  ".$this->db->dbprefix('voucher_record')." SET" .
	                    		" voucher_sn = LPAD(CAST(FLOOR(RAND()*1000000000000) AS CHAR(12)),12,'0')," .
	                    		" user_id = '".$user_id."', campaign_id = '".$campaign_id."', release_id = '".$release_id."', " .
	                    		" start_date = '".$now."', end_date = '".$end_time."', repeat_number = 1, voucher_amount = '".$voucher_amount."'," .
	                    		" min_order = '".$min_order."', create_date = '".date('Y-m-d H:i:s')."', create_admin = -1";
	                    do {

	                        if($this->db->query($sql)!==false) {
	                            break;
	                        }
	                        else {
	                            if ($this->db->errno() != 1062) {
	                                $result['error'] = 1;
	                                $result['message'] =$this->db->error();//没有活动
	                                return $result;
	                            }
	                        }
	                    }while (true); // 防止券号号重复

	                    $voucher_id = $this->db->insert_id();
	                    $voucher_info = $this->return_model->filter_voucher(array('voucher_id'=>$voucher_id));
	                    $voucher_sn = $voucher_info['voucher_sn'];
	                    $this->db->query("UPDATE ".$this->db->dbprefix('voucher_release')." SET voucher_count=voucher_count+1 where release_id = '".$release_id."'");

	                    $sql = "UPDATE ".$this->db->dbprefix('order_payment')." as rp LEFT JOIN ".$this->db->dbprefix('payment_info')." as p ON p.pay_id = rp.pay_id" .
	                    		" SET rp.payment_account = '$voucher_sn' WHERE rp.is_return=1 and rp.order_id = '".$return_id."' and p.pay_code = 'voucher_payback'";
	                    $this->db->query($sql);
                    }
                    //更新事务表,已财审
                    $arr = array('finance_check_admin'=>$aid,'finance_check_date'=>date('Y-m-d H:i:s'));// Update transaction
                    $this->return_model->update_transaction($arr,array('trans_type'=>TRANS_TYPE_RETURN_ORDER,'trans_sn'=>$return['return_sn'],'trans_status'=>TRANS_STAT_AWAIT_IN));
                    $this->return_model->update_transaction($arr,array('trans_type'=>TRANS_TYPE_RETURN_ORDER,'trans_sn'=>$return['return_sn'],'trans_status'=>TRANS_STAT_IN));

                    //更新换货单状态（财审、完结）
                    $arr = array(
                        'pay_status' => 1,
                        'finance_admin' => $aid,
                        'finance_date' => date('Y-m-d H:i:s'),
                        'lock_admin' => 0,
                        'lock_date' => '',
                        'is_ok' => 1,
                        'is_ok_date' => date('Y-m-d H:i:s'),
                        'is_ok_admin' => $aid
                    );
                    $this->return_model->update($arr, $return_id);
                    
                    //如果有申请单，也将完结。
                    if(!empty($return['apply_id'])) {
                            $this->load->model('apply_return_model');
                            $finish_num = $this->apply_return_model->finish_return_apply($return['apply_id']);
                            $apply_info = $this->apply_return_model->apply_info($return['apply_id']);
                            if($finish_num >= $apply_info['product_number']) {
                                    $up_apply = array('apply_status'=>2);
                                    $this->apply_return_model->update($up_apply, $return['apply_id']);
                            }
                    }

                    $return['pay_status'] = 1;//记logo时引用
                    $action_note .= " 自动财审并完结。";
                }

                /* 记录log */
                $action_note = '退货单入库'.' '.$action_note;
                $return_copy = $return;
                $return_copy['shipping_status'] = 1;
                $this->return_model->insert_action($return_copy,$action_note);
                $this->return_model->notify_ship((object)$return);
                break;


            case 'is_ok':/* 完结 */
				/* 标记退货单为“完结”，记录取消原因 */
                $action_note = isset($all_post['action_note']) ? trim($all_post['action_note']) : '';
                $arr = array(
                    'is_ok'  => 1,
                    'is_ok_date' => date('Y-m-d H:i:s'),
                    'is_ok_admin' => $aid,
                    'lock_admin' => 0,
                    'lock_date' => '',
                );
				$this->return_model->update($arr, $return_id);
                
                //如果有申请单，也将完结。
                if(!empty($return['apply_id'])) {
                        $this->load->model('apply_return_model');
                        $finish_num = $this->apply_return_model->finish_return_apply($return['apply_id']);
                        $apply_info = $this->apply_return_model->apply_info($return['apply_id']);
                        if($finish_num >= $apply_info['product_number']) {
                                $up_apply = array('apply_status'=>2);
                                $this->apply_return_model->update($up_apply, $return['apply_id']);
                        }
                }
                
        		/* 记录log */
                $action_note = '完结退货单 '.$action_note;
                $this->return_model->insert_action($return,$action_note);
                break;

            case 'invalid': /* 作废 */
        		/* 标记退货单为“无效”、“未付款” */
        		$arr = array(
                    'is_ok'  => 1,
                    'is_ok_date' => date('Y-m-d H:i:s'),
                    'is_ok_admin' => $aid,
                    'lock_admin' => 0,
                    'lock_date' => '',
                    'return_status'=>4,
                );
        		$this->return_model->update($arr, $return_id);

                $this->return_model->update_transaction(array('trans_status'=>TRANS_STAT_CANCELED,'cancel_admin'=>$aid,'cancel_date'=>date('Y-m-d H:i:s')),array('trans_type'=>TRANS_TYPE_RETURN_ORDER,'trans_sn'=>$return['return_sn'],'trans_status'=>TRANS_STAT_AWAIT_IN));

                //如果有申请单，将初始化。
                if(!empty($return['apply_id'])) {
                        $this->load->model('apply_return_model');
                        $finish_num = $this->apply_return_model->finish_return_apply($return['apply_id']);
                        $apply_info = $this->apply_return_model->apply_info($return['apply_id']);
                        if($finish_num >= $apply_info['product_number']) {
                                $up_apply = array('apply_status'=>0);
                                $this->apply_return_model->update($up_apply, $return['apply_id']);
                        }
                }
                
        		/* 记录log */
                if ($action_note == "") {
                    $action_note == '退货单作废';
                }
                $return_copy = $return;
                $return_copy['return_status'] = 4;
                $this->return_model->insert_action($return_copy,$action_note);
                break;
            default:
                die('invalid params');
                break;
        }
        $this->db->query('COMMIT');
        $links[] = array('text' => '退货明细', 'href' => "/order_return/edit/".$return_id);
        sys_msg('操作成功', 0, $links);
        break;
	}

	public function get_order_data ()
	{
            $order_sn = trim($this->input->post('order_sn'));
            $apply_id = $this->input->post('apply_id');
            $order_info = $this->return_model->get_order_info(0,$order_sn, $apply_id);
            if ( empty($order_info) )
            {
                    echo json_encode(array('error'=>1,'msg'=>'订单不存在'));
                    return;
            }
            if (!$this->return_model->can_return($order_info))
            {
                    echo json_encode(array('error'=>1,'msg'=>'订单不满足退货条件'));
                    return;
            }
                
            $this->load->model('apply_return_model');
            if (empty($apply_id)) {
                $returnCt = $this->apply_return_model->check_apply_return($order_info['order_id']);
                if (!empty($returnCt['ct'])) {
                    echo json_encode(array('error'=>1,'msg'=>'订单有未完结的申请退货单，请先处理后再操作。'));
                    return;
                }
            } else {
                $apply_info = $this->apply_return_model->apply_info($apply_id);
                if (empty($apply_info)) {
                    echo json_encode(array('error'=>1,'msg'=>'没有找到申请退货单。'));
                    return;
                }
                //申请退货理由 0:尺寸偏大 1:尺寸偏小 2:款式不喜欢 3:配送错误 4:其他
                $apply_reason_list = array(
                        '0'=>'尺寸偏大',
                        '1'=>'尺寸偏小',
                        '2'=>'款式不喜欢',
                        '3'=>'配送错误',
                        '4'=>'其他问题',
                        '5'=>'商品质量问题'
                );
                //计算已退货数量
                $return_goods_num = $this->apply_return_model->get_return_goods_num($apply_info['order_id']);
                //取得申请退货单商品
                $apply_product = $this->apply_return_model->apply_return_goods($apply_id,$apply_info['order_id']);
                foreach($apply_product as $key=>$v){
                    //可退数量
                    $k = $v['product_id'].' '.$v['color_id'].' '.$v['size_id'];
                    if(isset($return_goods_num[$k])) {
                            $v['n_product_num'] = (int)$v['o_product_number'] - (int)$return_goods_num[$k];
                    } else {
                            $v['n_product_num'] = $v['o_product_number'];
                    }

                    $v['reason'] = $apply_reason_list[$v['return_reason']];
                    $apply_product[$key] = $v;
                }
                //取得申请退货单意见列表
                $apply_suggest = $this->apply_return_model->apply_return_suggest($apply_id);
                //$data['apply_id'] = $apply_id;
                $data['apply_product'] = $apply_product;
                $data['apply_suggest'] = $apply_suggest;
            }
                
            $order_product = $this->return_model->order_product_can_return($order_info['order_id']);

            $data['add'] = $order_product;
            if (empty($order_product))
            {
                    echo json_encode(array('error'=>1,'msg'=>'没有可退商品'));
                    return;
            }
            $voucher = $this->return_model->get_voucher_payment($order_info['order_id']);
            if(!empty($voucher)) {
                $voucher['product_arr'] = empty($voucher['product'])?array():explode(',',$voucher['product']);
                $voucher['category_arr'] = empty($voucher['category'])?array():explode(',',$voucher['category']);
                $voucher['brand_arr'] = empty($voucher['brand'])?array():explode(',',$voucher['brand']);
            }

            $voucher_product_amount = 0;
            foreach($order_product as $key=>$product) {
                //if(isset($product['discount_type']) && $product['discount_type'] == 4) unset($order_product[$key]); //赠品不能退滴
                $product['real_num'] = max($product['gl_num']-$product['wait_num'],0);
                if(!empty($voucher) && $product['shop_price']==$product['product_price'] && $product['package_id'] == 0 && ($voucher['product_arr']===array()||in_array($product['product_id'],$voucher['product_arr']))&&($voucher['category_arr']===array()||in_array($product['category_id'],$voucher['category_arr']))&&($voucher['brand_arr']===array()||in_array($product['brand_id'],$voucher['brand_arr']))) {
                    $product['product_name'] = "<font color='red'>[券]</font> ".$product['product_name'];
                    $voucher_product_amount += $product['product_num']*$product['product_price'];
                }
                $order_product[$key] = $product;
            }
            if(!empty($voucher) && $voucher['payment_money']==$voucher_product_amount) {
                    $data['product_alert'] = "<font color='red'>该订单涉及现金券的商品如果要退货需要全部退。</font>";
            }
            $order_info['hope_time'] = date('Y-m-d',time() + 86400 * 7);
            $data['imagedomain'] = '/public/images';

            $data['return'] = $order_info;
            $data['return_product'] = $order_product;
            $data['return_reasons'] = $this->return_model->get_return_reasons();
            $data['my_id'] = $this->admin_id;
            $data['apply_id'] = $apply_id;
            // 下退货单时显示订单意见
            $this->load->model('order_model');
            $order_advice = $this->order_model->order_advice($order_info['order_id']);
            $data['order_advice'] = $order_advice;
            // add by shangguannan
            //用户退货运费，快递公司选择
            $this->load->model('return_user_shipping_fee_model');
            $shipping_name_list = $this->return_user_shipping_fee_model->get_all_shipping();
            $this->load->vars('shipping_name_list', $shipping_name_list);
                
            $data['content'] = $this->load->view('order_return/add_lib', $data, TRUE);
            $data['error'] = 0;
            unset($data['return_product']);
            unset($data['return']);
            echo json_encode($data);
            return;
	}

    public function print_barcode($barcode='', $product_name='', $color_name='', $size_name='', $provider_productcode='') {
        if (empty($barcode) || empty($product_name) || empty($color_name) || empty($size_name) || empty($provider_productcode))
        {
            sys_msg('参数错误', 1);
        }
        $data = array('barcode' => urldecode($barcode), 
                      'product_name' => urldecode($product_name), 
                      'color_name' => urldecode($color_name), 
                      'size_name' => urldecode($size_name), 
                      'provider_productcode' => urldecode($provider_productcode));

        $this->load->view('order_return/print_barcode', $data);
    }

	public function post_save ()
	{
		auth('order_return_edit');
		$all_post = $this->input->post();
        $order_id = isset($all_post['order_id']) ? intval($all_post['order_id']) : 0;
        $return_id = isset($all_post['return_id']) ? intval($all_post['return_id']) : 0;

        $this->db->query('BEGIN');
        $this->db->query('SELECT * FROM '.$this->db->dbprefix('order_info')." WHERE order_id = '".$order_id."' FOR UPDATE");
        $order = $this->return_model->get_order_info($order_id);

        if (empty($order))
        {
        	sys_msg('订单不存在!',1);
        }
        $return = $this->return_model->return_info($return_id);
        if (empty($return))
        {
        	sys_msg('退货单不存在!',1);
        }
        $operable_list = $this->return_model->get_return_perm($return);
        if (!$operable_list['save'])
        {
            sys_msg('无操作权限!',1);
        }

        if (isset($all_post['priv_edit_consignee']) && !empty($all_post['priv_edit_consignee']))
        {
            $return_c['consignee'] 	= $all_post['consignee'];
            $return_c['email'] 		= $all_post['email'];
            $return_c['address'] 		= $all_post['address'];
            $return_c['zipcode'] 		= $all_post['zipcode'];
            $return_c['tel'] 		= $all_post['tel'];
            $return_c['mobile'] 		= $all_post['mobile'];
            $return_c['return_reason']    = $all_post['return_reason'];
            $return_c['hope_time']	= date('Y-m-d H:i:s',strtotime($all_post['hope_time']));
            $this->return_model->update($return_c, $return_id);
        }

        $to_delete = array();
        $to_update = array();
        $to_insert = array();
        $order_product = $return_product = array();
    	/* 更新商品明细 */
        // 条件语句中加入FALSE，用来取消商品明细的更新
        if (isset($all_post['priv_edit_product']) && !empty($all_post['priv_edit_product']) && false)
        {
            $to_delete = array();
            $to_update = array();
            $to_insert = array();
            $order_product = $return_product = array();
            $arr = $this->return_model->order_product_can_return($order_id);
            foreach ($arr as $v)
            {
                $order_product[$v['rec_id'].'-'.$v['product_id'].'-'.$v['color_id'].'-'.$v['size_id'].'-'.$v['track_id']] = $v;
            }
            $arr = $this->return_model->return_product($return_id);
            foreach ($arr as $v)
            {
                $return_product[$v['rp_id']] = $v;
                $k = $v['op_id'].'-'.$v['product_id'].'-'.$v['color_id'].'-'.$v['size_id'].'-'.$v['cp_id'];
                if(isset($order_product[$k])){
                    $order_product[$k]['product_num'] += $v['product_num'];
                    $order_product[$k]['consign_num'] += $v['consign_num'];
                }else{
                    $order_product[$k] = $v;
                }
            }
            $only_check = array();
            $total_product_number = 0; // 待退商品的总和，至少要有一件商品退货
            $insert_sql = '';
            $i=1;
            foreach ($all_post['rec_id'] as $key=>$value) {
                $return_rec_id = intval($all_post['rec_id'][$key]);
                $op_id = intval($all_post['op_id'][$key]);
                $product_id = intval($all_post['product_id'][$key]);
                $color_id = intval($all_post['color_id'][$key]);
                $size_id = intval($all_post['size_id'][$key]);
                $track_id = intval($all_post['track_id'][$key]);
                $product_num = intval($all_post['product_number'][$key]);
                //重复性校验
                $k= $op_id.'-'.$product_id.'-'.$color_id.'-'.$size_id.'-'.$track_id;
                if (!isset($order_product[$k]))
                {
                	sys_msg('该商品在订单中不存在,不可退!',1);
                }
                in_array($k,$only_check) && sys_msg('重复性错误',1);
                $only_check[] = $k;
                if($product_num>=1)  $total_product_number += $product_num;

                if($return_rec_id>0 && $product_num<=0) {//to delete
                    $to_delete[] = $return_rec_id;
                    unset($return_product[$return_rec_id]);
                    continue;
                }
                if($product_num<=0)  continue;
                //if(isset($order_product[$k]['discount_type']) && $order_product[$k]['discount_type'] == 4 &&$product_num>0) sys_msg('赠品不可退',1);
                if($order_product[$k]['product_num']<$product_num) sys_msg($order_product[$k]['product_name'].'实退数量超过可退数量!！',1);
                $consign_num = min($product_num,$order_product[$k]['consign_num']);//优先退虚货

                if($return_rec_id>0 && $product_num>0) {
                    if(isset($return_product[$return_rec_id]) && $return_product[$return_rec_id]['product_num']!=$product_num) {//to update
                        ($return_product[$return_rec_id]['product_id']!=$product_id||$return_product[$return_rec_id]['color_id']!=$color_id||$return_product[$return_rec_id]['size_id']!=$size_id||$return_product[$return_rec_id]['cp_id']!=$track_id) && sys_msg('数据错误',1);
                        $arr = array(
                            'product_num'=>$product_num,
                            'consign_num'=>$consign_num,
                            'total_price'=>$return_product[$return_rec_id]['product_price']*$product_num
                        );
                        $this->return_model->update_product($arr,array('rp_id'=>$return_rec_id,'return_id'=>$return_id));

                        $to_update[] = $return_rec_id;
                        if($product_num>$consign_num)
                        {
                            $item = array(TRANS_TYPE_RETURN_ORDER,TRANS_STAT_AWAIT_IN,"'".$return['return_sn']."'",$product_id,$color_id,$size_id,$product_num-$consign_num,0,0,"'".date('Y-m-d H:i:s')."'",$this->admin_id,0,$return_rec_id);
                            $insert_sql .= "(".implode(',',$item)."),";
                        }
                    }
                    unset($return_product[$return_rec_id]);
                    continue;
                }
                $product = $order_product[$k];
                $arr = array(
                    'return_id' => $return_id,
                    'op_id' => $op_id,
                    'product_id' => $product_id,
                    'color_id' => $color_id,
                    'size_id' => $size_id,
                    'cp_id' => $track_id,
                    'product_num'=>$product_num,
                    'consign_num'=>$consign_num,
                    'market_price'=>$product['market_price'],
                    'product_price'=>$product['product_price'],
                    //'consign_price'=>$product['consign_price'],
                    //'cost_price'=>$product['cost_price'],
                    //'consign_rate'=>$product['consign_rate'],
                    'shop_price'=>$product['shop_price'],
                    //'provider_cess'=>$product['provider_cess'],
                    'total_price'=>$product['product_price']*$product_num,
                    'package_id'=>$product['package_id'],
                    'extension_id'=>$product['extension_id']
                );
                $return_rec_id = $this->return_model->insert_product($arr);
                if($product_num>$consign_num)
                {
                    $item = array(TRANS_TYPE_RETURN_ORDER,TRANS_STAT_AWAIT_IN,"'".$return['return_sn']."'",$product_id,$color_id,$size_id,$product_num-$consign_num,0,0,"'".date('Y-m-d H:i:s')."'",$this->admin_id,1,$return_rec_id);
                    $insert_sql .= "(".implode(',',$item)."),";
                }

            }
            !empty($return_product) && sys_msg('数据错误！',1);
        }
        //($total_product_number < 1) && sys_msg('请选择退货商品!',1);

        if (!empty($to_delete))
        {
            $sql = "DELETE FROM ".$this->db->dbprefix('order_return_product')." WHERE rp_id ".db_create_in($to_delete);
            $this->db->query($sql);
        }
        $trans_arr = array_merge($to_delete,$to_update);
        if (!empty($trans_arr))
        {
            $sql = "UPDATE ".$this->db->dbprefix('transaction_info')."" .
            		" SET trans_status = ".TRANS_STAT_CANCELED.", cancel_admin = '".$this->admin_id."' , cancel_date = '".date('Y-m-d H:i:s')."'" .
            		" WHERE trans_sn = '".$return['return_sn']."' AND trans_type = ".TRANS_TYPE_RETURN_ORDER." AND trans_status = ".TRANS_STAT_AWAIT_IN." AND sub_id ".db_create_in($trans_arr);
            $this->db->query($sql);
        }

        if (!empty($insert_sql))
        {
            $sql = "INSERT INTO ".$this->db->dbprefix('transaction_info')." (trans_type,trans_status,trans_sn,product_id,color_id,size_id," .
            		"product_number,depot_id,location_id,create_date,create_admin,trans_direction,sub_id) VALUES".$insert_sql;
            $sql = substr($sql,0,-1);
            $this->db->query($sql);
        }

        $voucher_back = intval($_REQUEST['voucher_back']);
        $discount_payment = $this->return_model->return_discount_payment($return_id,$voucher_back);//todo 取第二参数

        if ($discount_payment['error']!=0)
        {
            sys_msg($discount_payment['message'],1);
        }

        $payment_id = 0; //如果已存在，记在此变量中
        if (!empty($discount_payment['discount_payment']))
        {
        //是否已存在该支付，如果存在，否则update
            $pay_code = $discount_payment['discount_payment']['pay_code'];
            $pay_info = $this->return_model->filter_payment(array('pay_code'=>$pay_code));
            $pay_id = $pay_info['pay_id'];
            $payment_amount = -1 * $discount_payment['discount_payment']['payment_money'];

            $return_payment = $this->return_model->filter_order_payment(array('is_return'=>1,'order_id'=>$return_id,'pay_id'=>$pay_id));
            if (!empty($return_payment))
            {
				$payment_id = $return_payment['payment_id'];
				$this->return_model->update_order_payment(array('payment_money' => $payment_amount),array('payment_id'=>$payment_id));

            } else {
					$arr = array(
                                'is_return' => 1,
                                'order_id' => $return_id,
                                'pay_id' => $pay_id,
                                'payment_money' => $payment_amount,
                                'payment_admin' => -1,
                                'payment_date' => date('Y-m-d H:i:s')
					);
					$payment_id = $this->return_model->insert_payment($arr);
           }
        }

        //删除其余的支付方式
        $sql = "DELETE FROM ".$this->db->dbprefix('order_payment')."" .
        		" WHERE order_id = '".$return_id."' AND is_return=1 AND payment_id !='".$payment_id."'" .
        		" AND pay_id NOT IN (SELECT pay_id FROM ".$this->db->dbprefix('payment_info')."" .
        		" WHERE pay_code IN('payback','deduct'))";
        $this->db->query($sql);
        $this->return_model->update_return_amount($return_id);

        $return = $this->return_model->return_info($return_id);
        $choice = isset($all_post['return_shipping_fee'])?intval($all_post['return_shipping_fee']):NULL;
        $info = $this->return_model->update_return_shipping_fee($return_id,$order_id,$return,$order,$choice);
        if ($info['error']!==0)
        {
        	sys_msg($info['message'],1);
        }
        //BABY-599 添加返回用户运费 shangguannan 2013-04-18
        //先删除,如果没输入则认为取消了退运费,如果有输入会再次插入;
        //$this->return_user_shipping_fee_model->delete($return_id);
        
		//$return_user_shipping_fee = intval($this->input->post('return_user_shipping_fee'));
		//$shipping_name = trim($this->input->post('shipping_name'));
        //$insert_user_shipping_fee = $this->insert_return_user_shipping_fee($return_user_shipping_fee,shipping_name,$return_id,$return_sn,$order_id);
        //if($insert_user_shipping_fee){
        //    sys_msg($insert_user_shipping_fee,1);
        //}
        
        //BABY-599 添加返回用户运费 end
		$this->return_model->insert_action($return,'修改退货单');
        $this->db->query('COMMIT');
        $links[] = array('text' => '返回继续操作改退货申请。', 'href' => "/order_return/edit/".$return_id);
        $links[] = array('text' => '返回退货单列表', 'href' => "/order_return");
        sys_msg('编辑退货单成功。', 0, $links);
        exit;
	}

	public function post_add ()
	{
		auth('order_return_edit');
		//$this->load->model('user_model');
		$this->load->model('payment_model');
                $this->load->model('apply_return_model');
		//$this->load->model('order_model');
		$update['order_id'] = intval($this->input->post('order_id'));
                $apply_id = intval($this->input->post('apply_id'));
		if (empty($update['order_id'])) sys_msg('订单不存在，不能退货',1);
		$this->db->query('BEGIN');
		$this->db->query('SELECT * FROM '.$this->db->dbprefix('order_info')." where order_id = '".$update['order_id']."' for update");

		$order_info = $this->return_model->get_order_info($update['order_id']);/* 验证订单的有效性 */
        $order_id = $update['order_id'];
        if (empty($order_info)) sys_msg('订单不存在，不能退货',1);
        if (!$this->return_model->can_return($order_info)) sys_msg('订单未发货或已送出积分或有未入库的退货单，不能退货',1);

        $order_product = $this->return_model->order_product_can_return($order_info['order_id']);
        if (empty($order_product)) sys_msg('订单没有可退商品。');
        
        $invoice_no = '';
        if(!empty($apply_id)) {
                $apply_info = $this->apply_return_model->apply_info($apply_id);
                if(empty($apply_info)) sys_msg('没有找到申请退货单。');
                $invoice_no = $apply_info['invoice_no'];
                //检查是否有申请单已返运费
                //$apply_shipping_fee = $this->apply_return_model->get_apply_shipping_fee($invoice_no);
        }

        $order_product_num = array();
        $check_product = array();
        $is_consign_num = 0;
        foreach($order_product as $product){
            $is_consign_num = $is_consign_num + $product['consign_num'];
            $order_product_num[$product['op_id']] = $product['product_num'];
            $check_product[$product['rec_id'].'-'.$product['product_id'].'-'.$product['color_id'].'-'.$product['size_id'].'-'.$product['track_id']] = $product;
        }
        unset($order_product);
        $return = array();
    	/* 生成退货单 */
        do {
            $update['return_sn'] = $this->return_model->get_return_sn();
            $return_id = $this->return_model->insert($update);
            $err_no = $this->db->_error_number();
            if ($err_no == '1062') continue;
            if ($err_no == '0') break;
            sys_msg('操作失败', 1);
            return;
        }while (true); // 防止订单号重复

        $return_sn = $update['return_sn'];

        /* basic information */
        $return = array(
            'consignee'     =>trim($this->input->post('consignee')),
            'email'         =>trim($this->input->post('email')),
            'address'       =>trim($this->input->post('address')),
            'zipcode'       =>trim($this->input->post('zipcode')),
            'tel'           =>trim($this->input->post('tel')),
            'mobile'        =>trim($this->input->post('mobile')),
            'user_id'       =>$order_info['user_id'],
            'create_date'      =>date('Y-m-d H:i:s'),
            'create_admin'       =>$this->admin_id,
            'product_num'  =>0,
            'return_price'  =>0,
            'return_shipping_fee' =>0,
            'paid_price'    =>0,
            'return_reason' =>trim($this->input->post('return_reason')),
            'hope_time'     =>date('Y-m-d H:i:s',strtotime($this->input->post('hope_time'))),
            'apply_id'		=>$apply_id,
            'invoice_no'	=>$invoice_no
        );
        $this->return_model->update($return,$return_id);
        
        /* 更新商品明细 */
        $return_product = array();
		$order_product = $this->return_model->order_product($update['order_id']);
        $product_price = array();
        foreach ($order_product as $key=>$val)
        {
            $product_price[$val['op_id']] = $val;
        }
        unset($order_product);

        $track_arr = $this->input->post('track_id');
        $op_arr = $this->input->post('op_id');
        $product_arr = $this->input->post('product_id');
        $color_arr = $this->input->post('color_id');
        $size_arr = $this->input->post('size_id');
        $product_num_arr = $this->input->post('product_num');
        $input_order_product_num = array();
        foreach ($track_arr as $key=>$value)
        {
            $op_id = intval($op_arr[$key]);
            $product_id = intval($product_arr[$key]);
            $color_id = intval($color_arr[$key]);
            $size_id = intval($size_arr[$key]);
            $track_id = intval($track_arr[$key]);
            $product_num = intval($product_num_arr[$key]);
            if ($product_num < 1)
            {
                continue;
            }
            $k = $op_id."-".$product_id."-".$color_id."-".$size_id."-".$track_id;
            if (isset($return_product[$k]))  sys_msg('记录重复!',1);
            $check = $check_product[$k];
            if (empty($check)||$check['product_num']<$product_num) sys_msg('超过可退数量1！', 1);            
            //if(isset($check['discount_type']) && $check['discount_type'] == 4) sys_msg('赠品不可退');
            $consign_num = max($product_num-($check['product_num']-$check['consign_num']),0);
            $cur_product_price = $product_price[$op_id];
            $input_order_product_num[$op_id] = $product_num;
            $return_product[$op_id."-".$product_id."-".$color_id."-".$size_id."-".$track_id] = array(
                'op_id'=> $op_id,
                'return_id'=>$return_id,
                'product_id'=>$product_id,
                'color_id'=>$color_id,
                'size_id'=>$size_id,
                'cp_id'=>$track_id,
                'product_num'=>$product_num,
                'consign_num'=>$consign_num,
                'market_price'=>$cur_product_price['market_price'],
                'product_price'=>$cur_product_price['product_price'],
                'consign_price'=>$cur_product_price['consign_price'],
                'cost_price'=>$cur_product_price['cost_price'],
                'consign_rate'=>$cur_product_price['consign_rate'],
                'shop_price'=>$cur_product_price['shop_price'],
                //'provider_cess'=>$cur_product_price['provider_cess'],
                'total_price'=>$cur_product_price['product_price']*$product_num,
                'package_id'=>$cur_product_price['package_id'],
                'extension_id'=>$cur_product_price['extension_id'],
                'batch_id'=>$cur_product_price['batch_id'],
                'product_cess'=>$cur_product_price['product_cess']
            );
        }

        if(empty($return_product)) sys_msg('请选择要退货的商品!',1);
        
        //TODO BABY-219 添加退货单时，检测原订单是否有虚库，如有虚库，则要求全部退货，不能部分退货。
        if($is_consign_num > 0) {
            $diff_arr = array();
            $diff_arr = array_diff_assoc($order_product_num, $input_order_product_num);
            if(!empty($diff_arr) || count($diff_arr) > 0) sys_msg('原订单商品含有虚库，要求商品一次性全部退还！',1);
        }
        
        $sql = '';
        // 取得的订单的商品储位
        $orderBatchNums = $this->return_model->getSubBatchNumByOrderId($order_id);
        foreach($return_product as $key=>$val) {//insert return product
            $key = $val['product_id'].'-'.$val['color_id'].'-'.$val['size_id'];
            if(isset($orderBatchNums[$key])) 
            $batchNums = $this->getAvailableBatchNums( $orderBatchNums[$key], $val['product_num'] );
        	$rp_id= $this->return_model->insert_product(array(
                        'op_id'=> $val['op_id'],
                        'return_id'=>$val['return_id'],
                        'product_id'=>$val['product_id'],
                        'color_id'=>$val['color_id'],
                        'size_id'=>$val['size_id'],
                        'cp_id'=>$val['cp_id'],
                        'product_num'=>$val['product_num'],
                        'consign_num'=>$val['consign_num'],
                        'market_price'=>$val['market_price'],
                        'product_price'=>$val['product_price'],
                        //'consign_price'=>$val['consign_price'],
                        //'cost_price'=>$val['cost_price'],
                        'shop_price'=>$val['shop_price'],
                        //'provider_cess'=>$val['provider_cess'],
                        'total_price'=>$val['product_price']*$product_num,
                        'package_id'=>$val['package_id'],
                        'extension_id'=>$val['extension_id']
                        ));

            if($val['product_num']>$val['consign_num']){
                foreach( $batchNums AS $batchId=>$availNum ){
                    $p_num = $availNum['product_num']-$val['consign_num'];
                    if($p_num <= 0) {
                        continue;
                    }
                    $trans = array();
                    $trans['trans_type'] = TRANS_TYPE_RETURN_ORDER;
                    $trans['trans_status'] = TRANS_STAT_AWAIT_IN;
                    $trans['trans_sn'] = $return_sn;
                    $trans['product_id'] = $val['product_id'];
                    $trans['color_id'] = $val['color_id'];
                    $trans['size_id'] = $val['size_id'];
                    $trans['product_number'] = $p_num;
                    $trans['depot_id'] = 0;
                    $trans['location_id'] = 0;
                    $trans['create_admin'] = $this->admin_id;
                    $trans['create_date'] = date('Y-m-d H:i:s');
                    $trans['trans_direction'] = 1;
                    $trans['sub_id'] = $rp_id;
                    $trans['batch_id']=$batchId;
                    $trans['consign_price']=$availNum['consign_price'];
                    $trans['cost_price']=$availNum['cost_price'];
                    $trans['consign_rate']=$availNum['consign_rate'];
                    $trans['product_cess']=$availNum['product_cess'];
                    $trans['shop_price']=$availNum['shop_price'];
                    $trans['expire_date']=$availNum['expire_date'];
                    $trans['production_batch']=$availNum['production_batch'];

                    $this->return_model->insert_transaction($trans);
                }
            }
        }

        //拆分现金券
        $voucher_back = intval($_REQUEST['voucher_back']);
        $discount_payment = $this->return_model->return_discount_payment($return_id,$voucher_back);//todo 取第二参数
        if($discount_payment['error']!=0) {
            sys_msg($discount_payment['message'],1);
        }
        if(!empty($discount_payment['discount_payment'])) {

            $pay_code = $discount_payment['discount_payment']['pay_code'];
            $pay_info = $this->payment_model->filter(array('pay_code'=>$pay_code));

            $pay_id = $pay_info->pay_id;

            $payment_money = -1 * $discount_payment['discount_payment']['payment_money'];
            $arr = array(
                'is_return'=>1,
                'order_id'=>$return_id,
                'pay_id'=>$pay_id,
                'payment_money'=>$payment_money,
                'payment_admin'=>-1,
                'payment_date'=>date('Y-m-d H:i:s')
            );
            $payment_id = $this->return_model->insert_payment($arr);
        }
        $this->return_model->update_return_amount($return_id);
        //处理运费
        $return = $this->return_model->return_info($return_id);
        $return_shipping_fee = intval($this->input->post('return_shipping_fee'));
        $choice = empty($return_shipping_fee)?NULL:$return_shipping_fee;
        $info = $this->return_model->update_return_shipping_fee($return_id,$update['order_id'],$return,$order_info,$choice);
        if($info['error']!==0) sys_msg($info['message'],1);
        //BABY-599 添加返回用户运费 shangguannan 2013-04-18
		$return_user_shipping_fee = intval($this->input->post('return_user_shipping_fee'));
        if($return_user_shipping_fee>0){
            $shipping_name = trim($this->input->post('shipping_name'));
            $insert_user_shipping_fee = $this->insert_return_user_shipping_fee($return_user_shipping_fee,$shipping_name,$return_id,$return_sn,$order_id);
            if($insert_user_shipping_fee){
                sys_msg($insert_user_shipping_fee,1);
            }
            //添加返回用户运费end shangguannan 2013-04-18 
        }
        //更改申请退货单状态
        if($apply_id > 0) {
                $up_apply = array('apply_status'=>1);
                $this->apply_return_model->update($up_apply, $apply_id);
        }
		$this->return_model->insert_action($return,'新增退货单');
		$this->db->query('COMMIT');
		redirect('order_return/edit/'.$return_id);
		exit;
	}

	public function pre_calc_voucher ()
	{
		//$this->load->model('user_model');
		//$this->load->model('order_model');
		$order_id = intval($this->input->post('order_id'));
        $return_id = intval($this->input->post('return_id'));
        $return_product = $this->input->post('return_product');

        if (empty($return_product))
        {
        	echo json_encode(array('error'=>1,'msg'=>'请选则要退货的商品'));
			return;
        }
        $op_ids = array();
        $return_product = explode('$',$return_product);
        foreach ($return_product as $key=>$product) {
            $product = explode('|',$product);
            if (count($product)!=2)
            {
            	echo json_encode(array('error'=>1,'msg'=>'参数错误'));
				return;
            }
            $return_product[$key] = array('op_id'=>$product[0],'product_num'=>$product[1]);
            $op_ids[] = $product[0];
        }
        $info = $this->return_model->order_product($order_id);

        $order_product = array();
        foreach ($info as $product)
        {
            if(in_array($product['op_id'],$op_ids))
            {
                $order_product[$product['op_id']] = $product;
            }
        }
        foreach ($return_product as $key=>$product)
        {
            $val = $order_product[$product['op_id']];
            $val['product_num'] = $product['product_num'];
            $val['product_amount'] = $val['product_price']*$val['product_num'];
            $return_product[$key] = $val;
        }
        $message = '';
        //$split=1
        $message .= "拆分方式：";
        $info = $this->return_model->return_discount_payment($return_id,1,$return_product,$order_id);
        if(empty($info['discount_payment'])){
            $message .= $info['message']."\r";
        }else{
            $message .= ($info['discount_payment']['pay_code']=='voucher_deduct'?'扣款':'返还现金券')."   ";
            $message .=  round($info['discount_payment']['payment_money'],2)."元 \r";
        }
        //$split=2
        $message .= "返券方式：";
        $info = $this->return_model->return_discount_payment($return_id,2,$return_product,$order_id);
        if(empty($info['discount_payment'])){
            $message .= $info['message']."\r";
        }else{
            $message .= ($info['discount_payment']['pay_code']=='voucher_deduct'?'扣款':'返还现金券')."   ";
            $message .= round($info['discount_payment']['payment_money'],2)."元\r";
        }
        //output
        die(json_encode(array('error'=>0,'msg'=>$message)));
        break;
	}
        private function getAvailableBatchNums( $batchNums, $num )
        {
            $result = Array();
            foreach( $batchNums AS $batchNum=>$availNum )
            {
                if( $num > 0 )
                {
                    if( $availNum['product_num']>=$num )
                    {
                        $result[$batchNum]['product_num'] = $num;
                        $result[$batchNum]['shop_price'] = $availNum['shop_price'];
                        $result[$batchNum]['consign_price'] = $availNum['consign_price'];
                        $result[$batchNum]['cost_price'] = $availNum['cost_price'];
                        $result[$batchNum]['consign_rate'] = $availNum['consign_rate'];
                        $result[$batchNum]['product_cess'] = $availNum['product_cess'];
                        $result[$batchNum]['expire_date'] = $availNum['expire_date'];
                        $result[$batchNum]['production_batch'] = $availNum['production_batch'];
                        break;
                    }else{
                        $result[$batchNum]['product_num'] = $availNum['product_num'];
                        $result[$batchNum]['shop_price'] = $availNum['shop_price'];
                        $result[$batchNum]['consign_price'] = $availNum['consign_price'];
                        $result[$batchNum]['cost_price'] = $availNum['cost_price'];
                        $result[$batchNum]['consign_rate'] = $availNum['consign_rate'];
                        $result[$batchNum]['product_cess'] = $availNum['product_cess'];
                        $result[$batchNum]['expire_date'] = $availNum['expire_date'];
                        $result[$batchNum]['production_batch'] = $availNum['production_batch'];
                        //$result[$batchNum] = $availNum;
                        $num -= $availNum['product_num'];
                    }
                }
            }
            return $result;
        }
    /**
     * 添加一条退货运费信息
     * @param type $fee
     * @param type $return_id
     * @param type $return_sn
     * @param type $order_id
     */
    function insert_return_user_shipping_fee($fee,$shipping_name,$return_id,$return_sn,$order_id){
        //TODO 判断地区和运费是否符合标准
        if($fee>SHIPPING_FEE_FAR){
            return '运费超标,江浙沪<='.SHIPPING_FEE_NEAR.'元;其他地区<='.SHIPPING_FEE_FAR.'元!';
        }else{
            //大于10元还要进行判断,否则可以直接通过
            if($fee>SHIPPING_FEE_NEAR){
                //判断是否是江浙沪,如果大于10元,并且是江浙沪为不合法数据
                $this->load->model('order_model');
                $source_order = $this->order_model->filter(array('order_id'=>$order_id));
                $province = $source_order->province;
                $is_jzh = false;
                //province = 16(江苏),25(上海),31(浙江)
                if(in_array($province, $shipping_jzh)){
                    $is_jzh = true;
                }
                if($is_jzh)
                    return '运费超标,江浙沪<='.SHIPPING_FEE_NEAR.'元!';
            }
        }
        $return_user_shipping_fee_data['return_id'] = $return_id;
        $return_user_shipping_fee_data['return_sn'] = $return_sn;
        $return_user_shipping_fee_data['order_id'] = $order_id;
        $return_user_shipping_fee_data['user_shipping_fee'] = $fee;
        $return_user_shipping_fee_data['shipping_name'] = $shipping_name;
        $this->load->model('return_user_shipping_fee_model');
        $this->return_user_shipping_fee_model->insert($return_user_shipping_fee_data);
    }
}

###
