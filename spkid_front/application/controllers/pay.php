<?php
/**
* Pay_Controller
*/
class Pay extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->time = date('Y-m-d H:i:s');
		$this->user_id = $this->session->userdata('user_id');
		$this->load->model('order_model');
	}

	public function wxpay_notify() {	
    	$this->load->helper('file');
	//$log_name ='./cache/paylogs/'.'m_wxpay_'.time().rand(100, 999).'.db';
        $log_name ='./cache/paylogs/'.'m_wxpay_'.date("Ymdhis").'.db';
	$this->load->library('wxpay');
	//使用通用通知接口
	$notify = new Notify_pub();
	//存储微信的回调
	$xml = $GLOBALS['HTTP_RAW_POST_DATA'];

	$notify->saveData($xml);


	try {
            $this->order_model->insert_onlinepay_log(array(
                'pay_code' => 'wxpay',
                'data' => $xml,
                'create_date' => $this->time
            ));
        } catch (Exception $e) {
            
        }
        
	//验证签名，并回应微信。
	//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
	//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
	//尽可能提高通知的成功率，但微信不保证通知最终能成功。
	if($notify->checkSign() == FALSE){
		$notify->setReturnParameter("return_code","FAIL");//返回状态码
		$notify->setReturnParameter("return_msg","签名失败");//返回信息
	}else{
		$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
	}

	$returnXml = $notify->returnXml();

	append_write_file($log_name, $returnXml);
	if($notify->checkSign() == TRUE)
	{
		if ($notify->data["return_code"] == "FAIL") {
			//此处应该更新一下订单状态，商户自行增删操作
			append_write_file($log_name,"【通信出错】:\n".$xml."\n");
			die('fail');
		}
		elseif($notify->data["result_code"] == "FAIL"){
			//此处应该更新一下订单状态，商户自行增删操作
			append_write_file($log_name,"【业务出错】:\n".$xml."\n");
			die('fail');
		}
		else{
			//此处应该更新一下订单状态，商户自行增删操作
			append_write_file($log_name,"【支付成功】:\n".$xml."\n");
			$o = simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA );
			$o1 = json_decode(json_encode((array) $o), 1);
			append_write_file($log_name, '1 track_sn:' . var_export($track_sn,true));
			$track_sn = $o1['out_trade_no'];
			append_write_file($log_name,"订单：$track_sn  【支付成功】:\n".$xml."\n");
			append_write_file($log_name, '2 track_sn:' . var_export($track_sn,true));
			$this->db->trans_begin();
			$pay_track = $this->order_model->lock_pay_track($track_sn);
			// 检查记录状态
			if (empty($pay_track) || $pay_track->pay_status != 0) {
			    append_write_file($log_name, '记录状态不对或者已经支付'); die('fail');  
			}

			// 检查支付金额
			/*
			if (round($pay_track->pay_price, 2) != round($post['total_fee'], 2)) {
				append_write_file($log_name, 'round($pay_track->pay_price, 2):' .  round($pay_track->pay_price, 2));
				append_write_file($log_name, 'round($post[total_fee], 2):' . var_export($post, true));
			    append_write_file($log_name, '支付金额不对'); die('fail');
			}*/
			$order_ids = explode('-', $pay_track->order_ids);

			$order_list = $this->order_model->order_list_by_ids($order_ids);
			$total_price = floatval($pay_track->pay_price);
			$order_price = 0;
			$order_changed = FALSE;
			// 订单状态预检测
			foreach ($order_list as $order) {
			    // 订单状态不对，不添加支付记录
			    if ($order->order_status != 0 || $order->pay_status != 0) {
			        append_write_file($log_name, '订单状态不对，不添加支付记录');
			        continue;
			    }
    
			    $unpay_price = round($order->order_price + $order->shipping_fee - $order->paid_price, 2);
			    // 已付完，不添加支付记录
			    if ($unpay_price <= 0) {
	             	        append_write_file($log_name, '$unpay_price <= 0');
			        continue;
			    }
			    $order_price += $unpay_price;
			}
			// 如果待付金额不符，标识为问题
			if (round($order_price, 2) != round($total_price, 2)) {
			    $order_changed = TRUE;
			}
			$genre_id = PRODUCT_TOOTH_TYPE;
			$arr_order = array();
                        $admin_id = $this->cache->get('aname'.$pay_track->order_ids);
			foreach ($order_list as $order) {
			    // 金额有问题或已全部付完，退出循环
			    if($order_changed || $total_price<=0) {
			        break;
			    }
			    // 订单状态不对，不添加支付记录
			    if ($order->order_status != 0 || $order->pay_status != 0) {
			        continue;
			    }
    
			    $unpay_price = round($order->order_price + $order->shipping_fee - $order->paid_price, 2);
			    // 已付完，不添加支付记录
			    if($unpay_price<=0) {
			        continue;
			    }
			    $payment_money = min($unpay_price, $total_price);
			    $total_price -= $payment_money;

			    $this->order_model->insert_payment(array(
			        'order_id' => $order->order_id,
			        'pay_id' => PAY_ID_WXPAY,
			        'is_return' => 0,
			        'payment_account' => $o1['openid'],
			        'payment_money' => $payment_money,
			        'trade_no' => $o1['transaction_id'],
                                'payment_admin' => (int)$admin_id,
			        'payment_date' => $this->time,
			        'payment_remark' => 'weixin notify'
			    ));
			    append_write_file($log_name, 'insert pay log' . $this->db->last_query());
			    $this->order_model->update(array('paid_price' => round($order->paid_price + $unpay_price, 2), 'pay_id' => PAY_ID_WXPAY), $order->order_id);
    			    append_write_file($log_name, 'update paid_price' . $this->db->last_query());
			    $genre_id = $order->genre_id;
			    $arr_order[$order->order_id] = $order;
			}
			// 如果还有金额未进入订单，转入用户余额
			/*if (round($total_price, 2) > 0) {
			    $sql_log = "INSERT INTO 
			            ty_user_account_log
			            (link_id, user_id, user_money, pay_points, change_desc, change_code, create_admin, create_date)
			            VALUES
			            (?, ?, ?, 0, '订单支付转余额', 'change_account', 0, now());";
			    $this->db->query($sql_log, array($pay_track->track_id, $pay_track->user_id, $total_price));
			    $sql_user = "UPDATE ty_user_info SET user_money = user_money + ? WHERE user_id = ?";
			    $this->db->query($sql_user, array($total_price, $pay_track->user_id)); 
			}*/
			append_write_file($log_name, '2 track_sn:' . $track_sn);
			$this->order_model->update_pay_track_by_sn(array('pay_status' => 1),$track_sn);
			$this->db->trans_commit();   
			append_write_file($log_name, $this->db->last_query().':@'.date("Y-m-d H:i:s"));
                        $this->alipay_success_return($pay_track);
                        
			if ($genre_id == PRODUCT_COURSE_TYPE) {
			   append_write_file($log_name, '课程订单支付成功'.':@'.date("Y-m-d H:i:s"));
			   
			} else {
			    append_write_file($log_name, '产品订单支付成功'.':@'.date("Y-m-d H:i:s"));    
			}
		}
	}
	else {
    		append_write_file($write_filename, 'checkSign failed.@'.date("Y-m-d H:i:s"));
   	}
    
	}
        
        public function wxpay_check($track_sn)
	{
            //$track_sn = $this->input->get('sn');
            /*if(!$track_sn)
            {
                echo 'fail';
                return;
            }*/
            //$pay_track = $this->order_model->get_pay_track($track_sn);
            $info = $this->cache->get('pt_pay_status'.$track_sn);

            //if($pay_track->pay_status) 
            if (!empty($info)) 
            {
                echo 'success';
            }else {
                usleep(500);
                $this->wxpay_check($track_sn);
                //echo 'fail';
            }
	}
    

    public function alipay($type = 'return') {
		$this->load->helper('file');
		$write_filename = 	'./cache/paylogs/alipay-'. $type . '_' . date("Ymdhis"). '.txt';
		append_write_file($write_filename, 'alpay_return:@'.date("Y-m-d H:i:s"));
		append_write_file($write_filename, 'alpay_return:POST=:@'.date("Y-m-d H:i:s"));
		append_write_file($write_filename, var_export($_POST,true));
		append_write_file($write_filename, 'alpay_return:GET=:@'.date("Y-m-d H:i:s"));
		append_write_file($write_filename, var_export($_GET,true));
        $this->load->library('AlipaySubmit');
        $post = $type == 'return' ? $this->input->get(NULL, TRUE) : $this->input->post(NULL, TRUE);

        try {
            $this->order_model->insert_onlinepay_log(array(
                'pay_code' => 'alipay',
                'data' => json_encode($post),
                'create_date' => $this->time
            ));
        } catch (Exception $e) {
            
        }

		$verifyResult = $this->alipaysubmit->verify($post);
        append_write_file($write_filename, "verify_result: $verifyResult");	
        if (!$verifyResult){
	    	append_write_file($write_filename, 'verify_result failed.@'.date("Y-m-d H:i:s"));
            $type == 'return' ? redirect('/index/index') : die('fail');
        }

        $track_sn = trim($post['out_trade_no']);
        $this->db->trans_begin();
        $pay_track = $this->order_model->lock_pay_track($track_sn);

        $order_ids = explode('-', $pay_track->order_ids);
        append_write_file($write_filename, 'order_list' . var_export($order_list, true));
        $order_list = $this->order_model->order_list_by_ids($order_ids);
		$genre_id = PRODUCT_TOOTH_TYPE;
		foreach ($order_list as $order) {
			 $genre_id = $order->genre_id;
	    }



        // 检查记录状态
        if (empty($pay_track) || $pay_track->pay_status != 0) {
            append_write_file($write_filename, '记录状态不对');

            $type == 'return' ? $this->alipay_success_return($pay_track):die('fail');
        }
        // 检查支付金额
        if (round($pay_track->pay_price, 2) != round($post['total_fee'], 2)) {
	       append_write_file($write_filename, '支付金额不对');
            $type == 'return' ? redirect('/index/index') : die('fail');
        }

        $total_price = floatval($pay_track->pay_price);
        $order_price = 0;
        $order_changed = FALSE;
        // 订单状态预检测
        foreach ($order_list as $order) {
            // 订单状态不对，不添加支付记录
            if ($order->order_status != 0 || $order->pay_status != 0 || $order->pay_id != $pay_track->pay_id || $order->bank_code != $pay_track->bank_code) {
                continue;
            }
            
            $unpay_price = round($order->order_price + $order->shipping_fee - $order->paid_price, 2);
            // 已付完，不添加支付记录
            if ($unpay_price <= 0) {
                continue;
            }
            $order_price += $unpay_price;
        }
		append_write_file($write_filename, '如果待付金额不符，标识为问题');
        // 如果待付金额不符，标识为问题
        if (round($order_price, 2) != round($total_price, 2)) {
            $order_changed = TRUE;
        }
        
        $arr_order = array();
        $admin_id = $this->cache->get('aname'.$pay_track->order_ids);
        foreach ($order_list as $order) {
            // 金额有问题或已全部付完，退出循环
            if($order_changed || $total_price<=0) {
                break;
            }
            // 订单状态不对，不添加支付记录
            if ($order->order_status != 0 || $order->pay_status != 0 || $order->pay_id != $pay_track->pay_id || $order->bank_code != $pay_track->bank_code) {
                continue;
            }
            
            $unpay_price = round($order->order_price + $order->shipping_fee - $order->paid_price, 2);
            // 已付完，不添加支付记录
            if($unpay_price<=0) {
                continue;
            }
            $payment_money = min($unpay_price, $total_price);
            $total_price -= $payment_money;
            if($type != 'return') {
            	$this->order_model->insert_payment(array(
            	    'order_id' => $order->order_id,
            	    'pay_id' => PAY_ID_ALIPAY,
            	    'is_return' => 0,
            	    'payment_account' => trim($post['buyer_email']),
            	    'payment_money' => $payment_money,
            	    'trade_no' => trim($post['trade_no']),
                    'payment_admin' => (int)$admin_id,
            	    'payment_date' => $this->time,
            	    'payment_remark' => $type
            	));
            	$this->order_model->update(array('paid_price' => round($order->paid_price + $unpay_price, 2)), $order->order_id);	
            }
            
            
            $genre_id = $order->genre_id;
            $arr_order[$order->order_id] = $order;
        }
		append_write_file($write_filename, '支付' . var_export($order, true));
        // 如果还有金额未进入订单，转入用户余额
        /*if (round($total_price, 2) > 0) {
            $sql_log = "INSERT INTO 
                    ty_user_account_log
                    (link_id, user_id, user_money, pay_points, change_desc, change_code, create_admin, create_date)
                    VALUES
                    (?, ?, ?, 0, '订单支付转余额', 'change_account', 0, now());";
            $this->db->query($sql_log, array($pay_track->track_id, $pay_track->user_id, $total_price));
            $sql_user = "UPDATE ty_user_info SET user_money = user_money + ? WHERE user_id = ?";
            $this->db->query($sql_user, array($total_price, $pay_track->user_id)); 
	    append_write_file($write_filename, '状态改变' . $sql_user);
        }*/
        $this->order_model->update_pay_track_by_sn(array('pay_status' => 1),$track_sn);
        $this->db->trans_commit();   
	
        append_write_file($write_filename, $this->db->last_query().':@'.date("Y-m-d H:i:s"));
	//$this->alipay_success_return($pay_track);
        $type == 'return' ? $this->alipay_success_return($pay_track) : die('success');

        if ($genre_id == PRODUCT_COURSE_TYPE) {
            redirect('/pay/success_return');
        }
    }
    public function alipay_success_return($pay_track) {
         //$this->input->set_cookie('pt_pay_status'.$pay_track->order_ids, 1, CART_SAVE_SECOND);
        $this->cache->save('pt_pay_status'.$pay_track->order_ids,1,CART_SAVE_SECOND);       
    }
    
    public function success_return() {         
    	/* $str = '';
    	 $info_type = '';
    	 if ($genre_id == PRODUCT_COURSE_TYPE) {
    	 	$str = '课程';
    	 	$info_type = 'course_info';
    	 } else {
    	 	$str = '产品';
    	 	$info_type = 'info';
    	 }

    	 $this->load->vars(array(
    	     'title' => '订单支付成功',
    	     'order_list' => $arr_order,
    	     'info_type' => $info_type
    	 ));*/
	 $this->load->vars(array(
            'page_type' => 'course'
            ));
    	 $this->load->view('cart/paid_course');
    }

    public function recharge($type='return')
	{
		$this->load->library('AlipaySubmit');
		$this->load->model('user_model');
		$post=$type=='return'?$this->input->get(NULL,TRUE):$this->input->post(NULL,TRUE);

                try {
			$this->order_model->insert_onlinepay_log(array(
				'pay_code'=>'alipay',
				'data' => serialize($post),
				'create_date' => $this->time
			));			
		} catch (Exception $e) {}
		
		if(!$this->alipaysubmit->verify($post)) die('fail');
		
                $recharge_sn = trim($post['out_trade_no']);
                $recharge_id = substr($recharge_sn, 2);
                if ($this->user_model->recharge_success($recharge_id)) {
                    $type=='return'?redirect('/user/account'):print 'success';
                } else {
                    $type=='return'?redirect('/user/account'):die('fail');
                }
	}
	public function bill(){
		$this->load->library('bill');
		$get=$this->input->get(NULL,TRUE);
		try {
			$this->order_model->insert_onlinepay_log(array(
				'pay_code'=>'99bill',
				'data' => serialize($get),
				'create_date' => $this->time
			));			
		} catch (Exception $e) {}
		$order_sn=$get['orderId'];
		$order = $this->order_model->lock_order(array('order_sn'=>$order_sn));
		if(!$order){
			print $this->bill->fail();
			return;
		}
		if(!$this->bill->verify($get)){
			print $this->bill->fail($order);
			return;
		}
		/* 检查支付的金额是否相符 */
		$unpay_price = round(($order->order_price+$order->shipping_fee-$order->paid_price)*100);
		$total_fee = intval($get['orderAmount']);
		if($unpay_price!=$total_fee) 
		{
			print $this->bill->fail($order);
			return;
		}
		//插入支付记录		
		$this->order_model->insert_payment(array(
			'order_id' => $order->order_id,
			'pay_id' => PAY_ID_99BILL,
			'is_return' => 0,
			'payment_account' => '',
			'payment_money' => $total_fee/100,
			'trade_no' => trim($get['dealId']),
			'payment_date' => $this->time
		));
		$this->order_model->update(array('paid_price'=>round($order->paid_price+$total_fee/100,2)),$order->order_id);
		$this->db->trans_commit();
		print $this->bill->success($order);
	}
}
