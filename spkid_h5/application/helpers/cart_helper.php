<?php

function summary_cart($cart_list, &$voucher_list = NULL, $balance = NULL, $region_shipping = NULL) {
    $CI = &get_instance();
    $result = array(
        'product_price' => 0, // 本店售价合计（无格式）
        'product_num' => 0, // 购物车内商品的种类
        'shipping_fee' => 0,
        'voucher' => 0,
        'voucher_sn' => '',
        'product_list' => array(),
        'balance' => 0,
        'point' => 0,
        'unpay_price' => 0,
        'left_time' => 0,
        'product_weight' => 0
    );

    $discount = min($CI->session->userdata('discount_percent'), $CI->session->userdata('rank_discount'));
$discount = 1;
    $product_list = array(); // 根据供应商对商品进行汇总
    foreach ($cart_list as $cart) {
        if (!isset($product_list[$cart->provider_id])) {
            $shipping_fee_config = array();
            /*foreach(($cart->shipping_fee_config ? json_decode($cart->shipping_fee_config) : array()) as $config)
            {
                $shipping_fee_config[intval($config->regionId)] = array(floatval($config->fee), floatval($config->price));
            }*/
            $product_list[$cart->provider_id] = array(
                'provider_id' => $cart->provider_id,
                'provider_name' => $cart->provider_name,
                'shipping_fee_config' => $shipping_fee_config,
                'product_price' => 0,
                'voucher' => null,
                'product_list' => array(),
                'product_num' => 0
            );
        }
        $product_list[$cart->provider_id]['product_price'] += $cart->product_price * $cart->product_num;
        $product_list[$cart->provider_id]['product_list'][] = $cart;
        $product_list[$cart->provider_id]['product_num'] += $cart->product_num;
        
        
        $result['product_price'] += $cart->product_price * $cart->product_num * $discount;
        $result['product_num'] += $cart->product_num;
        $result['product_weight'] += $cart->product_num * $cart->product_weight;
        if (!$result['left_time'])
            $result['left_time'] = strtotime($cart->update_date) - strtotime($CI->time) + CART_SAVE_SECOND;
    }

    if (!empty($region_shipping)) {
        if (!empty($region_shipping['province'])) {
            $result['province'] = $region_shipping['province'];
            $result['pay_id'] = $region_shipping['pay_id'];
        }
    }
    
    if ($voucher_list) {
        foreach ($voucher_list as $provider_id => $voucher) {
            if (!isset($product_list[$provider_id]))
                continue;
            if (($voucher->payment_amount = calc_voucher_payment_amount($voucher, $product_list[$provider_id]['product_list'], $provider_id)) <= 0)
                continue;
            $result['voucher'] += $voucher->payment_amount;
            $result['voucher_sn'] = $voucher->voucher_sn;
            $product_list[$provider_id]['voucher'] = $voucher;
        }
    }
    $result['shipping_fee'] = calc_shipping_fee($result);
    if ($balance)
        $result['balance'] = min($balance, $result['product_price'] + $result['shipping_fee'] - $result['voucher']);
    $result['point'] = round($result['product_price'] - $result['voucher']);
    $result['unpay_price'] = $result['product_price'] + $result['shipping_fee'] - $result['voucher'] - $result['balance'];

    /**
     * v 2016.04.18 指定商品 购买立减活动
     */
    global $product_minus_activity;
    if(!empty($product_minus_activity) && strtotime($product_minus_activity['end_time']) >= time()){
        foreach ($product_list as $provider_id => &$provider){
            foreach ($provider['product_list'] as $product){
                if( $product->product_id == $product_minus_activity['product_id'] ){
                    $provider['product_price'] = $provider['product_price'] - ($product_minus_activity['minus_price'] * $product->product_num); 
                }
            }
        }
    }

    $result['product_list'] = $product_list;
    $result['product_weight'] = $result['product_weight'] * 1.05;
    return $result;
}
//获取当前省的在线支付运费/货到付款运费
function calc_shipping_fee($cart_summary)
{
    return 0;
    $pay_amount=$cart_summary['product_price']-(isset($cart_summary['voucher'])?$cart_summary['voucher']:0);
	if ($pay_amount>=SHIPPING_FREE_ORDER_PRICE&&SHIPPING_FREE_ORDER_PRICE!=-1) return 0;
	
	//计算区域运费
	$shipping_fee = SHIPPING_FEE_DEFAULT;
	if(!empty($cart_summary['province']))
	{
		$CI = & get_instance();
		$CI->load->model('region_model');
		$region_shipping_info = $CI->region_model->region_shipping_fee($cart_summary['province']);
		if(!empty($region_shipping_info))
		{
			if($cart_summary['pay_id'] == 1 && $region_shipping_info->cod_shipping_fee > 0)
			{
				$shipping_fee = $region_shipping_info->cod_shipping_fee;
			}
			else
			{
				if($region_shipping_info->online_shipping_fee > 0) $shipping_fee = $region_shipping_info->online_shipping_fee;
			}
		}
	}
	
	return $shipping_fee;
}

/**
 * 计算现鑫券的金额
 * @param type $voucher
 * @param type $cart_list
 * @param type $provider_id
 * @return int
 */
function calc_voucher_payment_amount($voucher,$cart_list, $provider_id)
{    
    $product_price = 0;
    $product_list = $voucher->product?explode(',',$voucher->product):array();
    $brand_list = $voucher->brand?explode(',',$voucher->brand):array();
    $category_list = $voucher->category?explode(',',$voucher->category):array();
    $provider_list = $voucher->provider ? explode(',', $voucher->provider) : array();
    foreach ($cart_list as $product) {
        if($product->package_id) continue;
        if($product->product_price!=$product->shop_price) continue;
        if($provider_id > 0 && $product->provider_id!=$provider_id) continue;
        if($product_list && !in_array(strval($product->product_id), $product_list)) continue;
        if($brand_list && !in_array(strval($product->brand_id), $brand_list)) continue;
        if($category_list && !in_array(strval($product->category_id), $category_list)) continue;
        if($provider_list && !in_array(strval($product->provider_id), $provider_list)) continue;
        $product_price += $product->product_price*$product->product_num;
    }

    if($product_price < $voucher->min_order) return 0;
    return min($product_price,$voucher->voucher_amount);	
}

/**
 * 商品计算现鑫券的金额
 * @param type $voucher
 * @param type $cart_list
 * @param type $provider_id
 * @return int
 */
function calc_voucher_payment_amount_product($voucher,$product)
{    
    $product_price = 0;
    $product_list = $voucher->product?explode(',',$voucher->product):array();
    $brand_list = $voucher->brand?explode(',',$voucher->brand):array();
    $category_list = $voucher->category?explode(',',$voucher->category):array();
    $provider_list = $voucher->provider ? explode(',', $voucher->provider) : array();
    //foreach ($cart_list as $product) {
        //if($product->package_id) continue;
        if($product->product_price!=$product->shop_price) return 0;
        //if($provider_id > 0 && $product->provider_id!=$provider_id) continue;
        if($product_list && !in_array(strval($product->product_id), $product_list)) return 0;
        if($brand_list && !in_array(strval($product->brand_id), $brand_list)) return 0;
        if($category_list && !in_array(strval($product->category_id), $category_list)) return 0;

        if($provider_list && !in_array(strval($product->shop_id), $provider_list)) return 0;
        $product_price += $product->product_price*$product->product_num;
    //}

    if($product_price < $voucher->min_order) return 0;
    return min($product_price,$voucher->voucher_amount);	
}

function get_order_sn() {
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);
    return "DD".substr(date('Ymd'), 2) . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
}

function split_package_product($order_product)
{
    $CI = & get_instance();
    $CI->load->model('package_model');
    $package_ids = array();
    $order_package = array();
    foreach ($order_product as $key => $product) {
    	if (!$product->package_id) continue;
    	$package_ids[] = $product->package_id;
    	if (!isset($order_package[$product->extension_id])) {
    		$order_package[$product->extension_id] = (object)array(
    			'product_list' => array(),
    			'package_id' => $product->package_id,
    			'extension_id' => $product->extension_id,
    			'package_real_amount' => 0
	    	);	    	
    	}
    	$order_package[$product->extension_id]->product_list[] = $product;
    	$order_package[$product->extension_id]->package_real_amount += $product->total_price;
    	unset($order_product[$key]);
    }
    if ($package_ids) {
    	$package_list = $CI->package_model->all_package(array('package_id'=>$package_ids));
    	$package_list = index_array($package_list,'package_id');
    	foreach ($order_package as &$package) {
    		$package->package_name = $package_list[$package->package_id]->package_name;
    		$package->package_image = $package_list[$package->package_id]->package_image;
    		
    	}
    }
    return array($order_product, $order_package);
}

function remove_voucher_history()
{
    $CI = &get_instance();
    $checkout = $CI->session->userdata('checkout');
    if(is_array($checkout) && isset($checkout['payment']['voucher'])){
        unset($checkout['payment']['voucher']);
        $CI->session->set_userdata('checkout',$checkout);
    }
}

/**
 * 按供应商将购物车商品进行拆分
 */
function split_by_provider($product_list)
{
    $result = array();
    foreach($product_list as $product)
    {
        if(!isset($result[$product->provider_id]))
        {
            $result[$product->provider_id] = array(
                'provider_id'=>$product->provider_id, 
                'provider_name'=>$product->provider_name, 
                'product_price' => 0,
                'voucher' => 0,
                'product_list'=>array()
                );
        }
        $result[$product->provider_id]['product_price'] += $product->shop_price;
        $result[$product->provider_id]['product_list'][] = $product;
    }
    return $result;
}

/**
 * 回收现金券垃圾支付
 * @param type $cart_list
 * @return array $voucher_payment
 */
function voucher_gc($cart_list, $key)
{
    $CI = &get_instance();
    $checkout = $CI->session->userdata('checkout');
    if(empty($checkout['payment']['voucher'][$key])) return;
    $need_save = false;
    $voucher_payment = $checkout['payment']['voucher'][$key];
    if ($key == 'product'){
        $cart_list->product_num = $cart_list->buy_num;
        $payment_amount = calc_voucher_payment_amount_product($voucher_payment[$cart_list->product_id], $cart_list);
        if (!$payment_amount) {
            unset($voucher_payment[$cart_list->product_id]);
            $need_save = true;
        }
    } else {
        $cart_summary = summary_cart($cart_list, $voucher_payment);
        foreach ($voucher_payment as $provider_id => $voucher) {
            if (!isset($cart_summary['product_list'][$provider_id]) || empty($cart_summary['product_list'][$provider_id]['voucher'])) {
                unset($voucher_payment[$provider_id]);
                $need_save = true;
                continue;
            }
        }
    }
    if($need_save){
        $checkout['payment']['voucher'] = $voucher_payment;
        $CI->session->set_userdata('checkout', $checkout);
    }
}

/**
 * 当前session保存的结算信息
 * @return array
 */
function get_checkout()
{
    $CI = &get_instance();
    $checkout = $CI->session->userdata('checkout');
    if(empty($checkout)){
        $CI->load->model('cart_model');
        $checkout = array('shipping' => array(), 'payment' => array());
        if($CI->user_id){
           $last_order = $CI->cart_model->last_order($CI->user_id); 
           if($last_order){
               $checkout['payment']['pay_id'] = $last_order->pay_id;
               $checkout['shipping']['best_time'] = $last_order->best_time;
               $checkout['shipping']['shipping_id'] = $last_order->shipping_id;
           }           
        }
        
    }
    if(empty($checkout['payment']['voucher'])){
        $checkout['payment']['voucher'] = array();
    }
    return $checkout;
}

/**
*有些支付方式會受到限制
*
*/

function isWxpayEnable($pay_id) {
    if ($pay_id == PAY_ID_WXPAY) {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function format_pay_list(&$pay_list) {
    if (empty($pay_list)) {
        return;
    }
    foreach ($pay_list as $key => &$value) {
        if ($value->pay_id == PAY_ID_WXPAY) {
            if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
                unset($pay_list[$key]);
            } else {
                //do nothing
            }            
        } else {
           //do nothing
        }
    }
}


/**
 *  判断 商品是否参加免邮活动
 *  $campaign_type = 2 免邮类型
 *  $product_id_data  商品数组(id,price)
*/
function campaign_package_product_v($product_id_data){
    $CI = &get_instance();
    $CI->load->model('cart_model');
    if(!empty($product_id_data)){
        return $CI->cart_model->campaign_product($product_id_data,2);       
    }

}
