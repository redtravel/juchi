<?php
/**
* Order_api
*/
class Order_course_api extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
                $sys_user = $this->input->post('sys_user');
		if (empty($sys_user) && !$this->admin_id) redirect('index/login');
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('order_course_model');
		$this->load->model('course_model');
		$this->load->helper('order');
		$this->load->helper('product');
		$this->config->load('package');
	}

	public function search_user()
	{
		auth('order_course_edit');
		$user_name = trim($this->input->post('user_name'));
		if(!$user_name) sys_msg('请填写搜索关键字',1);
		$user_list = $this->order_course_model->search_user($user_name);
		print json_encode(array('err'=>0,'msg'=>'','user_list'=>$user_list));
	}

	public function search_product()
	{
		auth('order_course_edit');

		$filter['category_id'] = intval($this->input->post('category_id'));
		$filter['product_sn'] = trim($this->input->post('product_sn'));
		$filter['product_name'] = trim($this->input->post('product_name'));

		$filter = get_pager_param($filter);
		$data = $this->order_course_model->search_product($filter);
		attach_sub($data['list']);
		$data['content'] = $this->load->view('order_course/search_product', $data, TRUE);
		$data['error'] = 0;
		unset($data['list']);
		print json_encode($data);        
	}

	public function add_product()
	{
		auth('order_course_edit');
		$order_id = intval($this->input->post('order_id'));
		$product_id = intval($this->input->post('product_id'));
		$num = max(intval($this->input->post('num')),1);
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order)sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作',1);
		$payment = $this->order_course_model->filter_payment(array('order_id'=>$order_id,'pay_id'=>PAY_ID_VOUCHER));
		if($payment) sys_msg('请先取消折扣支付，如现金券',1);
		$product = $this->order_course_model->filter_product(array('order_id'=>$order_id,'product_id'=>$product_id,'package_id'=>0,'discount_type !='=>4));
		if($product) sys_msg('所选商品已在订单中，不能重复添加!',1);
		$product = $this->course_model->filter(array('product_id'=>$product_id));
		if(!$product) sys_msg('商品不存在',1);
                
        // 2013-12-11: 检查商品是否属于同一个供应商
        $order_product_ary = $this->order_course_model->order_product($order_id);
                
		//检查库存
        $wait_num = 0;
        $consign_num2 = 0;

        $sub = $this->course_model->lock_sub(array('product_id'=>$product_id));
        if(!$sub) sys_msg('没有库存',1);
        if($num>(max($sub->gl_num-$sub->wait_num,0)+max($sub->consign_num,0)) && $sub->consign_num!=-2) sys_msg('库存不足',1);
        $wait_num = $sub->wait_num;
        $consign_num2 = $sub->consign_num;


		//插入商品
		$is_promote = $product->is_promote && $product->promote_start_date<$this->time && $product->promote_end_date>$this->time;
		$price = $is_promote?$product->promote_price:$product->shop_price;
		$gl_num = min(max($sub->gl_num-$wait_num,0),$num);//实库数量
		$consign_num = $num - $gl_num;//虚库数量
		$update = array(
			'order_id'=>$order_id,
			'product_id'=>$product_id,
                        'color_id' => $sub->color_id, 
                        'size_id' => $sub->size_id, 
			'product_num'=>$num,
			'market_price'=>$product->market_price, 
			'shop_price'=>$product->shop_price,
			'product_price'=>$price,
			'total_price'=>fix_price($num*$price),
			'consign_num'=>$consign_num,
			'consign_mark'=>$consign_num,
			'discount_type' => $is_promote?1:0, 
            'operator' => $product->operator
			);
		$op_id = $this->order_course_model->insert_product($update);
		//更新订单主表价格
		$order->product_num += $num;
		$order->order_price = fix_price($order->order_price+$num*$price);
		$this->order_course_model->update(array('product_num'=>$order->product_num,'order_price'=>$order->order_price),$order_id);
		//扣除库存
        $update = array('gl_num'=>$sub->gl_num-$gl_num,'wait_num'=>$wait_num+$consign_num);
        if($consign_num && $consign_num2>0) $update['consign_num'] = $consign_num2-$consign_num;
        $this->course_model->update_sub($update,$sub->sub_id);
		//分配储位
		if ($gl_num) {
			$info = $this->order_course_model->assign_trans($order,$sub,$gl_num,$op_id,$price);
			if($info['err']) sys_msg($info['msg'],1);
		}
		update_shipping_fee($order);
		$this->db->trans_commit();
		// 检测赠品
		check_gifts($order_id);
		//取订单商品
		$order_product = split_package_product($this->order_course_model->order_product($order_id));
		$this->load->vars('order', format_order($order));
		$this->load->vars('order_product', $order_product['product']);
		$this->load->vars('order_package', $order_product['package']);
		$result = array('err'=>0,'msg'=>'','data'=>$this->load->view('order_course/order_product',array('edit_product'=>TRUE),TRUE));
		print json_encode($result);
	}

	public function remove_product()
	{
		auth('order_course_edit');
		$this->load->model('course_model');
		$order_id = intval($this->input->post('order_id'));
		$op_id = intval($this->input->post('op_id'));
		
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order)sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作',1);
		$payment = $this->order_course_model->filter_payment(array('order_id'=>$order_id,'pay_id'=>PAY_ID_VOUCHER));
		if($payment) sys_msg('请先取消折扣支付，如现金券',1);
		$product = $this->order_course_model->filter_product(array('order_id'=>$order_id,'op_id'=>$op_id,'package_id'=>0));
		if(!$product) sys_msg('记录不存在',1);
		if($product->discount_type==4) sys_msg('赠品不能手动删除');
		//取库存记录
		$sub = $this->course_model->lock_sub(array('product_id'=>$product->product_id));
		if(!$sub) sys_msg('没有库存记录',1);
		
		//删除商品
		$this->order_course_model->delete_product($op_id);		
		//更新商品主表价格,注意同步更新order对像
		$order->product_num -= $product->product_num;
		$order->order_price = fix_price($order->order_price - $product->total_price);
		$this->order_course_model->update(array('product_num'=>$order->product_num,'order_price'=>$order->order_price),$order_id);
		//恢复库存
		$update = array('gl_num'=>$sub->gl_num+$product->product_num-$product->consign_num,'wait_num'=>$sub->wait_num-$product->consign_num);
		if($sub->consign_num>=0) $update['consign_num'] = $sub->consign_num+$product->consign_num;
		$this->course_model->update_sub($update,$sub->sub_id);
		//作废已分配的储位
		$this->order_course_model->update_trans(
			array('trans_status'=>TRANS_STAT_CANCELED,'cancel_admin'=>$this->admin_id,'cancel_date'=>$this->time),
			array('trans_type'=>TRANS_TYPE_SALE_ORDER,'trans_sn'=>$order->order_sn,'sub_id'=>$op_id,'trans_status'=>TRANS_STAT_AWAIT_OUT)
		);
		update_shipping_fee($order);
		$this->db->trans_commit();
		// 检测赠品
		check_gifts($order_id);
		//取订单商品
		$order_product = split_package_product($this->order_course_model->order_product($order_id));
		$this->load->vars('order', format_order($order));
		$this->load->vars('order_product', $order_product['product']);
		$this->load->vars('order_package', $order_product['package']);
		$result = array('err'=>0,'msg'=>'','data'=>$this->load->view('order_course/order_product',array('edit_product'=>TRUE),TRUE));
		print json_encode($result);
	}

	public function switch_lock()
	{
		$order_id = intval($this->input->post('order_id'));
		$op = trim($this->input->post('op'))=='lock'?'lock':'unlock';
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('记录不存在', 1);
		$perms=get_order_perm($order);
		if(!$perms[$op]) sys_msg('不能操作',1);
		$update['lock_admin'] = $op=='lock'?$this->admin_id:0;
		$this->order_course_model->update($update,$order_id);
		//$this->order_course_model->insert_action($order,$op=='lock'?'锁定':'解锁');
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}
        
        public function odd(){
                $order_id = intval($this->input->post('order_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('记录不存在', 1);
		$perms=get_order_perm($order);
		if(!$perms['odd']) sys_msg('不能操作',1);
		$update['odd'] = 1;
		$this->order_course_model->update($update,$order_id);
		$this->order_course_model->insert_action($order,'将订单标记为问题单');
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
        }
        
        public function odd_cancel(){
                $order_id = intval($this->input->post('order_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('记录不存在', 1);
		$perms=get_order_perm($order);
		if(!$perms['odd_cancel']) sys_msg('不能操作',1);
		$update['odd'] = 0;
		$this->order_course_model->update($update,$order_id);
		$this->order_course_model->insert_action($order,'取消该订单的问题单标记');
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
        }

	public function post_advice()
	{
		$this->load->model('order_advice_type_model');
		$update['order_id'] = intval($this->input->post('order_id'));
		$update['type_id'] = intval($this->input->post('type_id'));
		$update['advice_content'] = trim($this->input->post('advice_content'));

		if(!$update['advice_content']) sys_msg('请填写意见内容',1);
		$order = $this->order_course_model->filter(array('order_id'=>$update['order_id']));
		if(!$order) sys_msg('记录不存在', 1);
		$perms=get_order_perm($order);
		if(!$perms['advice']) sys_msg('不能操作',1);
		$type = $this->order_advice_type_model->filter(array('type_id'=>$update['type_id']));
		if(!$type) sys_msg('请选择意见类型',1);
		
		$update['advice_admin'] = $this->admin_id;
		$update['advice_date'] = $this->time;
		$this->order_course_model->insert_advice($update);
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function pay_balance()
	{
		auth('order_course_edit');
		$this->load->model('user_model');
		$this->load->model('user_account_log_model');
		$order_id = intval($this->input->post('order_id'));
		$balance_amount = fix_price($this->input->post('balance_amount'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$order = format_order($order);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作',1);
		$user = $this->user_model->lock_user($order->user_id);
		if(!$user) sys_msg('用户不存在',1);
		if($order->order_amount<$balance_amount||$balance_amount<0||$user->user_money<$balance_amount) sys_msg('支付金额错误',1);
		// 添加支付记录
		$this->order_course_model->insert_payment(array(
			'order_id' => $order->order_id,
			'is_return' => 0,
			'pay_id' => PAY_ID_BALANCE,
			'bank_code' => '',
			'payment_account' => '',
			'payment_money' => $balance_amount,
			'trade_no' => '',
			'payment_remark' => '',
			'payment_admin' => $this->admin_id,
			'payment_date' => $this->time
		));
		
		// 更新订单金额
		$order->paid_price = fix_price($order->paid_price + $balance_amount);
		$this->order_course_model->update(array('paid_price'=>$order->paid_price),$order_id);
		// 添加用户帐户变动记录
		$this->user_account_log_model->insert(array(
			'link_id' => $order->order_id,
			'user_id' => $user->user_id,
			'user_money' => $balance_amount*-1,
			'change_desc' => sprintf("订单 %s 余额支付",$order->order_sn),
			'change_code' => 'order_pay',
			'create_admin' => $this->admin_id,
			'create_date' => $this->time
		));
		
		// 更新用户表
		$this->user_model->update(array('user_money'=>fix_price($user->user_money-$balance_amount)),$user->user_id);
		update_shipping_fee($order);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function pay_voucher()
	{
		auth('order_course_edit');
		$this->load->model('voucher_model');
		$order_id = intval($this->input->post('order_id'));
		$voucher_sn = trim($this->input->post('voucher_sn'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作',1);
		$voucher = $this->voucher_model->lock_voucher($voucher_sn);
		if(!$voucher) sys_msg('现金券不存在',1);
		$campaign = $this->voucher_model->filter_campaign(array('campaign_id'=>$voucher->campaign_id));
		
		// 检查voucher的可用性
		if(!valid_voucher($voucher,$order)) sys_msg('现金券不可用',1);
		$payment_amount = fix_price(min($voucher->voucher_amount, $order->order_price));	
		// 添加支付记录
		$this->order_course_model->insert_payment(array(
			'order_id' => $order->order_id,
			'is_return' => 0,
			'pay_id' => PAY_ID_VOUCHER,
			'bank_code' => '',
			'payment_account' => $voucher->voucher_sn,
			'payment_money' => $payment_amount,
			'trade_no' => '',
			'payment_remark' => '',
			'payment_admin' => $this->admin_id,
			'payment_date' => $this->time
		));
		// 更新voucher记录
		$this->voucher_model->update(array('used_number'=>$voucher->used_number+1),$voucher->voucher_id);
		// 更新主订单表
		$order->paid_price += $payment_amount;
		$this->order_course_model->update(array('paid_price'=>fix_price($order->paid_price)),$order_id);
		update_shipping_fee($order);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function remove_voucher()
	{
        $auto_invalid = $this->input->post('auto_invalid');
        if (empty($auto_invalid)) auth('order_edit');
		$this->load->model('voucher_model');
		$order_id = intval($this->input->post('order_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(empty($auto_invalid) && !$perms['edit_order']) sys_msg('不能操作',1);
		$payment = $this->order_course_model->filter_payment(array('order_id'=>$order_id,'pay_id'=>PAY_ID_VOUCHER));
		if(!$payment) sys_msg('未使用过现金券');
		$voucher = $this->voucher_model->lock_voucher($payment->payment_account);
		// 删除支付记录
		$this->order_course_model->delete_payment($payment->payment_id);
		// 恢复现金券的可用数量
		$this->voucher_model->update(array('used_number'=>$voucher->used_number-1, 'voucher_status' => 0),$voucher->voucher_id);
		// 更新订单
		$order->paid_price = fix_price($order->paid_price - $payment->payment_money);
		$this->order_course_model->update(array('paid_price'=>$order->paid_price),$order_id);
		update_shipping_fee($order);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function remove_payment($payment_id)
	{
		
	}

	public function invalid()
	{
            $auto_invalid = $this->input->post('auto_invalid');
            if (empty($auto_invalid)) auth('order_edit');
		$this->load->model('product_model');
		$this->load->model('voucher_model');
		$this->load->model('user_model');
		$this->load->model('user_account_log_model');
		$order_id = intval($this->input->post('order_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(empty($auto_invalid) && !$perms['invalid']) sys_msg('不能操作',1);
		$user = $this->user_model->lock_user($order->user_id);
		// 返还余额支付
		$order_payment = $this->order_course_model->order_payment($order_id);
		$balance_amount = 0;
		foreach ($order_payment as $payment) {
			if ($payment->is_discount) {
                            if (!empty($auto_invalid)){
                                $this->remove_voucher();
				continue;
                            } else {
                                sys_msg('请先取消现金券等折扣支付',1);
                            }
                        }
			$balance_amount += $payment->payment_money;
		}
		if($balance_amount){
			$this->order_course_model->insert_payment(array(
				'order_id' => $order->order_id,
				'is_return' => 0,
				'pay_id' => PAY_ID_PAYBACK,
				'bank_code' => '',
				'payment_account' => '',
				'payment_money' => fix_price($balance_amount*-1),
				'trade_no' => '',
				'payment_remark' => '订单作废，已付金额返还帐户。',
				'payment_admin' => $this->admin_id,
				'payment_date' => $this->time
			));
		
			$this->user_account_log_model->insert(array(
				'link_id' => $order->order_id,
				'user_id' => $user->user_id,
				'user_money' => fix_price($balance_amount),
				'change_desc' => sprintf("订单 %s 作废，已付金额返还帐户。",$order->order_sn),
				'change_code' => 'order_payback',
				'create_admin' => $this->admin_id,
				'create_date' => $this->time
			));		
			// 更新用户表
			$this->user_model->update(array('user_money'=>fix_price($user->user_money+$balance_amount)),$user->user_id);
		}
		// 返回库存
		$this->order_course_model->update_trans(
			array('trans_status'=>TRANS_STAT_CANCELED,'cancel_admin'=>$this->admin_id,'cancel_date'=>$this->time),
			array('trans_type'=>TRANS_TYPE_SALE_ORDER,'trans_sn'=>$order->order_sn,'trans_status'=>TRANS_STAT_AWAIT_OUT)
		);
		$order_product = $this->order_course_model->order_product($order_id);
		$subs = array();
		foreach ($order_product as $product) {
			if (!isset($subs[$product->sub_id])) {
				$subs[$product->sub_id] = array('gl_num'=>0,'consign_num'=>0);
			}
			$subs[$product->sub_id]['gl_num'] += $product->product_num-$product->consign_num;
			$subs[$product->sub_id]['consign_num'] += $product->consign_num;
		}
		
		if($subs) {
			$sub_list = $this->product_model->lock_sub(array('sub_id'=>array_keys($subs)));
			foreach ($sub_list as $sub) {
				$update = array('gl_num'=>$sub->gl_num+$subs[$sub->sub_id]['gl_num'],'wait_num'=>$sub->wait_num-$subs[$sub->sub_id]['consign_num']);
				if($sub->consign_num>=0) $update['consign_num'] = $sub->consign_num+$subs[$sub->sub_id]['consign_num'];
				$this->product_model->update_sub($update,$sub->sub_id);
			}
		}	
		// 置订单为作废
		if(!$order_payment && !$order_product){
			$delete = TRUE;
			$this->order_course_model->delete($order_id);
			$this->order_course_model->delete_advice_where(array('order_id'=>$order_id,'is_return'=>0));
			$this->order_course_model->delete_action_where(array('order_id'=>$order_id,'is_return'=>0));
		}else{
			$delete = FALSE;
			$order->order_status = 4;
			$this->order_course_model->update(
				array(
					'order_status' => $order->order_status,
					'is_ok'	=> 1,
					'is_ok_admin' => $this->admin_id,
					'is_ok_date' => $this->time,
					'lock_admin' => 0
				),$order_id);
			$this->order_course_model->insert_action($order,'订单作废');
		}
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>$delete?'订单作废并已删除，现转向列表页':'','redirect'=>intval($delete)));		
	}

	public function free_shipping_fee()
	{
		auth('order_course_edit');
		$order_id = intval($this->input->post('order_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作',1);
		$this->order_course_model->update(array('shipping_fee'=>0),$order_id);
		$this->order_course_model->insert_action($order,'免运费');
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function reset_shipping_fee()
	{
		auth('order_course_edit');
		$order_id = intval($this->input->post('order_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作',1);
		update_shipping_fee($order);
		$this->order_course_model->insert_action($order,'重新计算运费');
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}
	
	public function update_shipping_fee()
	{
		auth('order_course_edit');
		$order_id = intval($this->input->post('order_id'));
		$new_shipping_fee = round(floatval($this->input->post('new_shipping_fee')),2);
		if($new_shipping_fee<0) sys_msg('运费必须是非负数',1);
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作',1);
		update_shipping_fee($order,$new_shipping_fee);
		$this->order_course_model->insert_action($order,'更新运费');
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}
        
        public function reset_saler()
	{
		auth('order_course_edit');
		$order_id = intval($this->input->post('order_id'));
		$saler = trim($this->input->post('saler'));
		if(empty($saler)) sys_msg('请填写销售员',1);
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作',1);
                $this->order_course_model->update(array('saler' => $saler),$order_id);
                
		$this->order_course_model->insert_action($order,'更新销售员');
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function confirm()
	{
		auth('order_course_confirm');
		$this->load->model('user_model');
		$this->load->model('user_account_log_model');
		$order_id = intval($this->input->post('order_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['confirm']) sys_msg('不能操作',1);
		$user = $this->user_model->lock_user($order->user_id);
		// 避免空订单被客审
		$product = $this->order_course_model->filter_product(array('order_id'=>$order->order_id));
		if(!$product) sys_msg('订单中没有商品',1);
		// 检查订单流程
		$routing = $this->order_course_model->filter_routing(array('source_id'=>$order->source_id,'shipping_id'=>$order->shipping_id,'pay_id'=>$order->pay_id,'show_type !='=>4));
		if(!$routing) sys_msg('订单流程错误', 1);
		if($order->shipping_id!=SHIPPING_ID_CAC){
			$available_shipping = $this->order_course_model->available_shipping(array(
				'source_id'=>$order->source_id,
				'pay_id'=>$order->pay_id, 'shipping_id'=>$order->shipping_id,
				'region_ids' => array($order->country, $order->province, $order->city, $order->district)
			));
			if(!$available_shipping) sys_msg('配送地区与配送方式不匹配',1);
		}
		$order = format_order($order);
		if($order->order_amount>0 && in_array($order->pay_id, array(PAY_ID_VOUCHER,PAY_ID_BALANCE)))
		sys_msg('有待付金额的订单，不能选择余额或现金券支付，请重新选择！',1);

		$update = array(
			'order_status' => 1,
			'confirm_admin' => $this->admin_id,
			'confirm_date' => $this->time,
			'lock_admin' => 0
		);
		$action_note = '审核订单';
		// 返还多付的金额
		if($order->order_amount<0){
			$this->order_course_model->insert_payment(array(
				'order_id' => $order->order_id,
				'is_return' => 0,
				'pay_id' => PAY_ID_PAYBACK,
				'bank_code' => '',
				'payment_account' => '',
				'payment_money' => fix_price($order->order_amount),
				'trade_no' => '',
				'payment_remark' => '订单客审，多付金额返还帐户。',
				'payment_admin' => $this->admin_id,
				'payment_date' => $this->time
			));
		
			$this->user_account_log_model->insert(array(
				'link_id' => $order->order_id,
				'user_id' => $user->user_id,
				'user_money' => fix_price($order->order_amount*-1),
				'change_desc' => sprintf("订单 %s 客审，多付金额返还帐户。",$order->order_sn),
				'change_code' => 'order_payback',
				'create_admin' => $this->admin_id,
				'create_date' => $this->time
			));		
			// 更新用户表
			$this->user_model->update(array('user_money'=>fix_price($user->user_money-$order->order_amount)),$user->user_id);
			$update['paid_price'] = fix_price($order->paid_price + $order->order_amount);
		}
		
                // ---- BABY-834: 来源为 京东/一号店 的订单，系统自动支付，并财审。
                global $third_parts;
                if (in_array($order->source_id, array_keys($third_parts))) {
                        // 自动支付
                        if ($order->order_amount > 0) {
                                $pay_info = $third_parts[$order->source_id];
                                
                                $this->order_course_model->insert_payment(array(
                                        'order_id' => $order->order_id,
                                        'is_return' => 0,
                                        'pay_id' => $pay_info['pay_id'],
                                        'bank_code' => '',
                                        'payment_account' => '',
                                        'payment_money' => fix_price($order->order_amount),
                                        'trade_no' => '',
                                        'payment_remark' => $pay_info['payment_remark'],
                                        'payment_admin' => $this->admin_id,
                                        'payment_date' => $this->time
                                ));
                        }
                        
                        // 更新订单表，已支付金额
                        $paid_price = fix_price($order->paid_price + $order->order_amount);
                        $this->order_course_model->update(array('paid_price'=>$paid_price), $order->order_id);

                        // 重设订单属性，供下面自动财审之用。
                        $routing->routing = 'F'; // 先财审
                        $order->order_amount = 0; // 无代付
                }
                // -------------------------------------------------------------
                
		if($routing->routing=='F' && $order->order_amount<=0){
			$update['pay_status'] = 1;
			$update['finance_admin'] = $this->admin_id;
			$update['finance_date'] = $this->time;
			$this->order_course_model->update_trans(
				array('finance_check_admin'=>$this->admin_id,'finance_check_date'=>$this->time),
				array('trans_type'=>TRANS_TYPE_SALE_ORDER,'trans_sn'=>$order->order_sn,'trans_status'=>TRANS_STAT_AWAIT_OUT)
			);
			$action_note .= '，订单自动财审';
		}
		$this->order_course_model->update($update,$order_id);
		foreach($update as $key=>$val) $order->$key = $val;
		$this->order_course_model->insert_action($order,$action_note);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function unconfirm()
	{
		auth('order_course_unconfirm');
		$order_id = intval($this->input->post('order_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['unconfirm']) sys_msg('不能操作',1);
		$update = array(
			'order_status' => 0,
			'confirm_admin' => 0,
			'confirm_date' => '0000-00-00',
			'lock_admin' => 0
		);
                if($order->shipping_id==SF_SHIPPING_ID){
                    $update['invoice_no'] = '';
                    $this->load->model('package_sf_model');
                    $this->package_sf_model->delete_package_interface($order->order_id);
                }
		$this->order_course_model->update($update,$order_id);
		foreach($update as $key=>$val) $order->$key = $val;
		$this->order_course_model->insert_action($order,'订单反审核');
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}
	public function unpay()
	{
		auth('order_course_pay');
		$order_id = intval($this->input->post('order_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if($perms['pay']) sys_msg('不能操作',1);
		$update = array(
			'pay_status' => 0,
			'finance_admin' => 0		
		);
		$action_note = '订单反财审';
		// 如果订单已发货，则自动完结
		if($order->shipping_status){
                    sys_msg('已发货不能反财审',1);
		}
		// 更新事务表
		$this->order_course_model->update_trans(
			array('finance_check_admin'=>0),
			array('trans_type'=>TRANS_TYPE_SALE_ORDER,'trans_sn'=>$order->order_sn,'trans_status'=>TRANS_STAT_AWAIT_OUT)
		);
		$this->order_course_model->update($update,$order_id);
		foreach($update as $key=>$val) $order->$key=$val;
		$this->order_course_model->insert_action($order,$action_note);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}
	public function shipping()
	{
		auth('order_course_shipping');
		$this->load->model('product_model');
		$order_id = intval($this->input->post('order_id'));
		$shipping_true = intval($this->input->post('shipping_true'));
		$invoice_no = trim($this->input->post('invoice_no')); //运单号
		$logistics = trim($this->input->post('logistics')); //物流公司
		$mobile = trim($this->input->post('mobile'));   //用户手机
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['shipping']) sys_msg('不能操作',1);
		
		$update = array(
			'shipping_status' => 1,
			'shipping_admin' => $this->admin_id,
			'shipping_date' => $this->time,
			'lock_admin' => 0,
            'invoice_no' => $invoice_no, 
            'shipping_true' => $shipping_true
		);	
		$trans_update = array('trans_status'=>TRANS_STAT_OUT,'update_admin'=>$this->admin_id,'update_date'=>$this->time);		
		$action_note = "订单发货";

		$product = $this->order_course_model->filter_product(array('order_id'=>$order->order_id,'consign_num !='=>0));
		if($product){
			if($shipping_true) {
				print json_encode(array('err'=>1,'msg'=>'订单中有虚库， 是否要设为虚发？','confirm'=>1));
				return;
			}
		}
		
		// 更新wait_num
		if($product){
			$order_product = $this->order_course_model->order_product($order_id);
			$subs = array();
			foreach ($order_product as $p) {
				if($p->consign_num==0) continue;
				if(!isset($subs[$p->sub_id])) $subs[$p->sub_id] = 0;
				$subs[$p->sub_id] += $p->consign_num;
			}
			if($subs){
				$sub_list = $this->product_model->lock_sub(array('sub_id'=>array_keys($subs)));
				foreach($sub_list as $sub){
					$wait_num = $sub->wait_num-$subs[$sub->sub_id];
					$this->product_model->update_sub(array('wait_num'=>$wait_num),$sub->sub_id);
				}
			}
		}
		
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
		$this->order_course_model->update_trans(
			$trans_update,
			array('trans_type'=>TRANS_TYPE_SALE_ORDER,'trans_sn'=>$order->order_sn,'trans_status'=>TRANS_STAT_AWAIT_OUT)
		);
		$this->order_course_model->update($update,$order_id);
		foreach($update as $key=>$val) $order->$key = $val;
		$this->order_course_model->insert_action($order,$action_note);
		$this->db->trans_commit();

		//发送通知邮件和短信
		//$this->load->library("mobile");
		$content = LOGISTISC_COMPANY. $logistics. INVOICE_NO. $invoice_no; 
		//$smscallback = $this->mobile->send($content, $mobile);
                if (!empty($mobile)){
                    $url = ERP_HOST.'/api/do_sms';
                    $pdata = array('msg' => $content, 'mob' => $mobile);
                    curl_post($url, $pdata);
                }
		/*if ( $shipping_true )
		{
			$this->order_course_model->notify_shipping($order);
        }*/
		print json_encode(array('err'=>0,'msg'=>''));
	}	

	public function payment()
	{
		auth('order_course_payment');
		$order_id = intval($this->input->post('order_id'));
		$pay_id = intval($this->input->post('pay_id'));
		$payment_money = fix_price($this->input->post('payment_money'));
		$payment_account = trim($this->input->post('payment_account'));
		$trade_no = trim($this->input->post('trade_no'));
		$payment_remark = trim($this->input->post('payment_remark'));
		if($payment_money<=0) sys_msg('支付金额错误',1);

		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_pay']) sys_msg('不能操作',1);

		$available_pay = $this->order_course_model->available_pay(array('source_id'=>$order->source_id,'shipping_id'=>$order->shipping_id));
		if(!in_array($pay_id,array_keys(get_pair($available_pay,'pay_id','pay_id'))))
		sys_msg('支付方式错误',1);

		// 添加支付记录
		$this->order_course_model->insert_payment(array(
			'order_id' =>$order_id,
			'pay_id' =>$pay_id,
			'payment_money' => $payment_money,
			'payment_account' =>$payment_account,
			'payment_remark' => $payment_remark,
			'trade_no' => $trade_no,
			'payment_admin' =>$this->admin_id,
			'payment_date' =>$this->time
		));

		// 更新订单表
		$this->order_course_model->update(array('paid_price'=>fix_price($order->paid_price+$payment_money)),$order_id);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));

	}

	public function delete_payment()
	{
		auth('order_course_payment');
		$order_id = intval($this->input->post('order_id'));
		$payment_id = intval($this->input->post('payment_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_pay']) sys_msg('不能操作',1);
		$payment = $this->order_course_model->filter_payment(array('order_id'=>$order_id,'payment_id'=>$payment_id,'is_return'=>0));
		if(!$payment) sys_msg('支付记录不存在',1);
		// 删除支付记录
		$this->order_course_model->delete_payment($payment_id);
		// 更新订单表
		$this->order_course_model->update(array('paid_price'=>fix_price($order->paid_price-$payment->payment_money)),$order_id);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function pay()
	{
		auth('order_course_pay');
		$order_id = intval($this->input->post('order_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['pay']) sys_msg('不能操作',1);
		$update = array(
			'pay_status' => 1,
			'finance_admin' => $this->admin_id, 
			'finance_date'  =>$this->time,
			'lock_admin' =>0		
		);
		$action_note = '订单财审';
		// 如果订单已发货，则自动完结
		if($order->shipping_status){
			$update['is_ok'] = 1;
			$update['is_ok_admin'] = $this->admin_id;
			$update['is_ok_date'] = $this->time;
			$action_note .= "，订单自动完结";
		}
		// 更新事务表
		$this->order_course_model->update_trans(
			array('finance_check_admin'=>$this->admin_id,'finance_check_date'=>$this->time),
			array('trans_type'=>TRANS_TYPE_SALE_ORDER,'trans_sn'=>$order->order_sn,'trans_status'=>TRANS_STAT_AWAIT_OUT)
		);
		$this->order_course_model->update($update,$order_id);
		foreach($update as $key=>$val) $order->$key=$val;
		$this->order_course_model->insert_action($order,$action_note);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function load_location()
	{
		$this->load->model('depot_model');
		$depot_id = intval($this->input->post('depot_id'));
		$location_list = $this->depot_model->all_location(array('depot_id'=>$depot_id,'is_use'=>1));
		print json_encode(array('err'=>0,'msg'=>'','data'=>get_pair($location_list,'location_id','location_name')));
	}

	public function edit_price()
	{
		auth('order_course_edit');

		$order_id = intval($this->input->post('order_id'));
		$op_id = intval($this->input->post('op_id'));
		$new_price = fix_price($this->input->post('new_price'));
		$reason = trim($this->input->post('reason'));
		if($new_price<0) sys_msg('价格错误',1);
		if(!$reason) sys_msg('请输入原因',1);
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_price']) sys_msg('不能操作',1);
		
		$order_product = index_array($this->order_course_model->order_product($order_id),'op_id');
		if(!isset($order_product[$op_id])) sys_msg('商品不存在',1);
		$op = $order_product[$op_id];
		// if($op->package_id || $op->discount_type==4) sys_msg('礼包商品和赠品不能修改价格',1);
		if($this->order_course_model->filter_payment(array('order_id'=>$order_id,'pay_id'=>PAY_ID_VOUCHER))) sys_msg('请先取消现金券',1);

		// 修改订单商品表
		$this->order_course_model->update_product(array(
			'product_price' => $new_price,
			'total_price' => fix_price($new_price*$op->product_num),
			'discount_type' => 3
		),$op->op_id);
                
		// 修改订单主表
		$this->order_course_model->update(array(
			'order_price' => fix_price($order->order_price - ($op->product_price-$new_price)*$op->product_num)
		),$order_id);
		$this->order_course_model->insert_action($order,"修改商品 {$op->product_name} {$op->color_name} {$op->size_name} 价格，由 {$op->product_price} 改为 {$new_price} 【{$reason}】");
		$this->db->trans_commit();
		// check_gifts($order_id);
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function load_package()
	{
		auth('order_course_edit');
		$this->load->model('package_model');
		$this->load->helper('product');
		$this->load->helper('package');
		$this->config->load('package');
		$order_id = intval($this->input->post('order_id'));
		$package_id = intval($this->input->post('package_id'));
		$extension_id = intval($this->input->post('extension_id'));
		$package = $this->package_model->filter(array('package_id'=>$package_id,'package_status >'=>0));
		if(!$package) sys_msg('礼包不存在,或未审核.');
		
		$package->package_other_config = unpack_package_config($package->package_other_config);
		$package_product = index_array($this->package_model->package_product($package_id),'product_id');
		$package_area = index_array($this->package_model->all_area(array('package_id'=>$package_id,'area_type'=>1)),'area_id');

		$order_product = array();
		if($extension_id){
			foreach ($this->order_course_model->order_product($order_id) as $p) {
				if($p->package_id!=$package_id || $p->extension_id!=$extension_id) continue;
				if(isset($package_product[$p->product_id]) && isset($package_area[$package_product[$p->product_id]->area_id])){
					$p->area_id = $package_product[$p->product_id]->area_id;
					$p->area_name = $package_area[$package_product[$p->product_id]->area_id]->area_name;
				}else{
					$p->area_id = 0;
					$p->area_name='';
				}
				$order_product[] = $p;			
			}
		}

		attach_sub($package_product);
		foreach($package_area as $k=>$area) $package_area[$k]->area_product=array();
		foreach ($package_product as $p) $package_area[$p->area_id]->area_product[] = $p;

		$this->load->vars(array(
			'package'=>$package,
			'package_area' => $package_area,
			'order_product' => $order_product,
			'package_id' => $package_id,
			'extension_id' => $extension_id,
			'all_type' => $this->config->item('package_all_type'),
			'all_status' => $this->config->item('package_all_status'),
		));
		$result = array('err'=>0,'msg'=>'','data'=>$this->load->view('order/package',NULL,TRUE));
		print json_encode($result);
	}

	public function add_package()
	{
		auth('order_course_edit');
		$this->load->model('product_model');
		$this->load->model('package_model');
		$this->load->helper('package');
		$order_id = intval($this->input->post('order_id'));
		$package_id = intval($this->input->post('package_id'));
		$sub_ids = trim($this->input->post('sub_ids'));
		if(!$sub_ids) sys_msg('请选择商品',1);
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作',1);

		$package = $this->package_model->filter(array('package_id'=>$package_id));
		if(!$package || $package->package_status<1) sys_msg('礼包不存在或未审核。',1);
		
		// 根据sub_ids取得product_list
		$sub_ids = explode('|',$sub_ids);
		foreach($sub_ids as $key=>$sub_id) $sub_ids[$key] = intval($sub_id);
		$sub_list = index_array($this->product_model->lock_sub(array('sub_id'=>$sub_ids)),'sub_id');
		$product_list = array();
		foreach($sub_ids as $sub_id){
			if(!isset($sub_list[$sub_id])) sys_msg('商品没有库存',1);
			$sub = $sub_list[$sub_id];
			$p = (object)array('product_id'=>$sub->product_id,'color_id'=>$sub->color_id,'size_id'=>$sub->size_id);
			if($sub->gl_num-$sub->wait_num>0){
				$p->gl_num = 1;
				$p->consign_num = 0;
				$sub->gl_num -= 1;
			}else{
				if($sub->consign_num!=-2 && $sub->consign_num<1) sys_msg('库存不足',1);
				$p->gl_num = 0;
				$p->consign_num = 1;
				//if($sub->consign_num!=-2) $sub->consign_num -= 1;
				$sub->wait_num += 1;
			}
			$sub_list[$sub_id] = $sub;
			$product_list[] = $p;

		}

		// 判断礼包的规则
		$package_area = index_array($this->package_model->all_area(array('package_id'=>$package_id,'area_type'=>1)),'area_id');
		$package_product = index_array($this->package_model->package_product($package_id),'product_id');
		$total_num = 0;
		$shop_amount = 0;
		foreach($package_area as $key=>$area) $package_area[$key]->product_num = 0;
		foreach($product_list as $key=>$p){
			if(!isset($package_product[$p->product_id])) sys_msg('商品不存礼包内',1);
			$pp = $package_product[$p->product_id];
			$p->market_price = $pp->market_price;
			$p->shop_price = $pp->shop_price;
			$p->cost_price = $pp->shop_price;
			$p->consign_price = $pp->consign_price;
			$p->consign_rate = $pp->consign_rate;
			$p->goods_cess = $pp->goods_cess;
			$package_area[$pp->area_id]->product_num += 1;
			$total_num += 1;			
			$shop_amount += $p->shop_price;
			$product_list[$key] = $p;
		}
		foreach($package_area as $area){
			if($area->min_number>$area->product_num) sys_msg('商品数量不符合礼包要求',1);
		}
		// 计算价格
		$price_config = array($package->package_goods_number=>$package->package_amount);
		foreach(unpack_package_config($package->package_other_config) as $config){
			$price_config[$config[0]] = $config[1];
		}
		
		if(!isset($price_config[$total_num])) sys_msg('商品数量不符合礼包要求',1);
		$package_amount = fix_price($price_config[$total_num]);
		$assigned_amount = 0;
		foreach ($product_list as $key => $p) {
			$p->product_price = fix_price($package_amount/$shop_amount*$p->shop_price);
			$assigned_amount += $p->product_price;
			$product_list[$key] = $p;
		}
		$product_list[0]->product_price = fix_price($product_list[0]->product_price+$package_amount-$assigned_amount);
		// 插入订单商品
		$extension_id = 0;
		foreach ($product_list as $p) {
			$op_id = $this->order_course_model->insert_product(array(
				'order_id'=>$order_id,
				'product_id'=>$p->product_id,
				'color_id'=>$p->color_id,
				'size_id'=>$p->size_id,
				'product_num'=>1,
				'market_price'=>$p->market_price,
				'cost_price'=>$p->cost_price,
				'consign_price'=>$p->consign_price,
				'consign_rate'=>$p->consign_rate,
				'shop_price'=>$p->shop_price,
				'product_price'=>$p->product_price,
				'provider_cess'=>$p->goods_cess,
				'total_price'=>$p->product_price,
				'consign_num'=>$p->consign_num,
				'consign_mark'=>$p->consign_num,
				'discount_type' => 2,
				'package_id' => $package_id,
				'extension_id' => $extension_id
			));
			if(!$extension_id){
				$extension_id = $op_id;
				$this->order_course_model->update_product(array('extension_id'=>$extension_id),$op_id);
			}
			$info = $this->order_course_model->assign_trans($order,$p,$p->gl_num,$op_id);
			if($info['err']) sys_msg('分配库存错误',1);
		}
		// 更新库存
		foreach ($sub_list as $sub) {
			$this->product_model->update_sub(array(
				'gl_num' => $sub->gl_num,
				'consign_num' => $sub->consign_num,
				'wait_num' => $sub->wait_num
			),$sub->sub_id);
		}
		$update = array(
			'order_price'=> fix_price($order->order_price + $package_amount),
			'product_num' => $order->product_num + $total_num,
		);
		foreach($update as $key=>$val) $order->$key = $val;
		$this->order_course_model->update($update,$order_id);
		update_shipping_fee($order);
		$this->db->trans_commit();
		// 检测赠品
		check_gifts($order_id);
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function remove_package()
	{
		auth('order_course_edit');
		$this->load->model('product_model');
		$order_id = intval($this->input->post('order_id'));
		$extension_id = intval($this->input->post('extension_id'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作',1);

		$package_product = array();
		$sub_ids = array();
		$order_product = $this->order_course_model->order_product($order_id);
		foreach ($order_product as $p) {
			if($p->extension_id != $extension_id || $p->discount_type!=2) continue;
			$package_product[] = $p;
			$sub_ids[] = $p->sub_id;
		}
		if(!$package_product) sys_msg('商品不存在',1);
		$sub_list = index_array($this->product_model->lock_sub(array('sub_id'=>$sub_ids)),'sub_id');

		foreach ($package_product as $p) {
			if(!isset($sub_list[$p->sub_id])) sys_msg('没有库存记录',1);
			$sub = $sub_list[$p->sub_id];
			$this->order_course_model->delete_product($p->op_id);
			// 更新订单主表
			$order->product_num -= 1;
			$order->order_price -= $p->product_price;
			// 恢复库存
			if($p->consign_num){
				$sub->wait_num -= 1;
				if($sub->consign_num>=0) $sub->consign_num += 1;
			}else{
				$sub->gl_num += 1;
			}
			$sub_list[$p->sub_id] = $sub;
			//作废已分配的储位
			$this->order_course_model->update_trans(
				array('trans_status'=>TRANS_STAT_CANCELED,'cancel_admin'=>$this->admin_id,'cancel_date'=>$this->time),
				array('trans_type'=>TRANS_TYPE_SALE_ORDER,'trans_sn'=>$order->order_sn,'sub_id'=>$p->op_id,'trans_status'=>TRANS_STAT_AWAIT_OUT)
			);
		}
		foreach ($sub_list as $sub) {
			$this->product_model->update_sub(array('gl_num'=>$sub->gl_num,'consign_num'=>$sub->consign_num,'wait_num'=>$sub->wait_num),$sub->sub_id);
		}
		$order->order_price = fix_price($order->order_price);
		$this->order_course_model->update(array('product_num'=>$order->product_num,'order_price'=>$order->order_price),$order_id);
		update_shipping_fee($order);
		$this->db->trans_commit();
		// 检测赠品
		check_gifts($order_id);
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function edit_invoice_no()
	{
		auth('order_course_shipping');
		$order_id = intval($this->input->post('order_id'));
		$invoice_no = trim($this->input->post('invoice_no'));
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		if(!$order->shipping_status) sys_msg('不能操作',1);
		$this->order_course_model->update(array('invoice_no'=>$invoice_no),$order_id);
		$this->order_course_model->insert_action($order,'更改运单号为'.$invoice_no);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function edit_real_shipping_fee()
	{
		auth('order_course_shipping');
		$order_id = intval($this->input->post('order_id'));
		$real_shipping_fee = round(floatval($this->input->post('real_shipping_fee')),2);
		$this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		if(!$order->shipping_status) sys_msg('不能操作',1);
		$this->order_course_model->update(array('real_shipping_fee'=>$real_shipping_fee),$order_id);
		$this->order_course_model->insert_action($order,'更改实际运费为'.number_format($real_shipping_fee,2));
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}
	
	public function export_inv(){
	    auth('order_course_view');
	    $inv_export_date = trim($_GET['inv_export_date']);
	    if(preg_match("/^\d{4}-\d{2}-\d{2}$/", $inv_export_date)===false) {
		die('请选择导出日期');
	    }
	    $sql = "SELECT
			o.order_id,
			o.order_sn,
			o.order_price,
			o.order_status,
			o.invoice_title,
			o.consignee,pr.region_name AS province_name,cr.region_name AS city_name,dr.region_name AS district_name,o.address,o.zipcode,o.tel,o.mobile
			FROM ty_order_info o
			    LEFT JOIN ty_region_info AS pr ON o.province = pr.region_id
			    LEFT JOIN ty_region_info AS cr ON o.city = cr.region_id
			    LEFT JOIN ty_region_info AS dr ON o.district = dr.region_id
			WHERE shipping_status = 1
			    AND o.invoice_title <> ''
			    AND o.order_status = 1
			    AND o.shipping_date >= '$inv_export_date'
			    AND o.shipping_date < FROM_UNIXTIME(UNIX_TIMESTAMP('$inv_export_date')+86400,'%Y-%m-%d')
			    ORDER BY o.order_id DESC";
	    $rs = $this->db->query($sql)->result_array();
	    $result = $order_ids = array();
	    foreach ($rs as $row ) {
		$result[$row['order_id']] = $row;
		$order_ids[] = $row['order_id'];
	    }
	    if(!empty($order_ids)) {
		$sql = "SELECT
			    op.order_id,
			    op.payment_money
			    FROM ty_order_payment AS op
			    LEFT JOIN ty_payment_info AS p
				ON p.pay_id = op.pay_id
			    WHERE op.is_return = 0
				AND p.is_discount = 1
				AND op.payment_money > 0
				AND op.order_id ".db_create_in($order_ids);
		$rs = $this->db->query($sql)->result_array();
		foreach ($rs as $row) {
		    $result[$row['order_id']]['order_price'] -= $row['payment_money'];
		}
	    }
	    $result_str = "";
	    if(!empty($result)) {
            $i = 1;
            foreach($result as $order) {
                $result_str .= mb_convert_encoding( $i.",", 'GB2312');
                $result_str .= mb_convert_encoding( $inv_export_date.",", 'GB2312');
                $result_str .= mb_convert_encoding( $order['order_sn'].",", 'GB2312');
                $result_str .= mb_convert_encoding( str_replace(array(',',"\n"),array('，',' '),$order['invoice_title']).",", 'GB2312');
                $result_str .= mb_convert_encoding( $order['order_price'].",", 'GB2312');
		$result_str .= mb_convert_encoding( $order['consignee'].",", 'GB2312');
		$result_str .= mb_convert_encoding( $order['province_name']." ".$order['city_name']." ".$order['district_name'].",", 'GB2312');
		$result_str .= mb_convert_encoding( $order['address'].",", 'GB2312');
		$result_str .= mb_convert_encoding( $order['tel'].",", 'GB2312');
		$result_str .= mb_convert_encoding( $order['mobile'].",", 'GB2312');
		$result_str .= mb_convert_encoding( $order['zipcode'].",", 'GB2312');
                $result_str .= mb_convert_encoding( "\n", 'GB2312');
                $i += 1;
            }

            $header_str = mb_convert_encoding( $inv_export_date."电脑增票普票开票明细"."\n", 'GB2312');
            $header_str .= mb_convert_encoding( '序号,', 'GB2312');
            $header_str .= mb_convert_encoding( '发票日期,', 'GB2312');
            $header_str .= mb_convert_encoding( '订单编号,', 'GB2312');
            $header_str .= mb_convert_encoding( '发票抬头,', 'GB2312');
            $header_str .= mb_convert_encoding( '普通发票价税合计,', 'GB2312');
	    
	    $header_str .= mb_convert_encoding( '收货人,', 'GB2312');
	    $header_str .= mb_convert_encoding( '所在区域,', 'GB2312');
	    $header_str .= mb_convert_encoding( '详细地址,', 'GB2312');
	    $header_str .= mb_convert_encoding( '手机,', 'GB2312');
	    $header_str .= mb_convert_encoding( '电话,', 'GB2312');
	    $header_str .= mb_convert_encoding( '邮编,', 'GB2312');
	    
            $header_str .= mb_convert_encoding( "\n", 'GB2312');
            $result_str = $header_str.$result_str;

	    }
	    if(empty($result_str)){
		header("Content-type: text/html; charset=utf-8"); 
		die('当日没有订单发货');
	    }
	    $file_name = date('YmdHis');
	    @set_time_limit(0);
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Content-type: application/vnd.ms-excel; charset=utf-8");
	    Header("Content-Disposition: attachment; filename=$file_name.csv");
	    print_r($result_str);
	}
}
