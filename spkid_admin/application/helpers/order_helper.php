<?php
function get_order_sn(){
	mt_srand((double) microtime() * 1000000);
    return "DD".substr(date('Ymd'),2) . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
}

function format_order($order)
{
	$order->order_amount = fix_price($order->order_price + $order->shipping_fee - $order->paid_price);
	return $order;
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
    	foreach ($order_package as $key => $package) {
    		$package->package_name = $package_list[$package->package_id]->package_name;
    		$order_package[$key] = $package;
    	}
    }
    return array('product'=>$order_product, 'package'=>$order_package);
}

function filter_payment($order_payment, $pay_code)
{
    $order_payment = index_array($order_payment, 'pay_code');
    return isset($order_payment[$pay_code])?$order_payment[$pay_code]:array();
}

function format_order_status($order,$red=FALSE,$full=FALSE)
{
    $status = array();
    if($order->order_status==0) $status[] = '未客审';
    if($order->order_status==1) $status[] = '已客审';
    if($order->order_status==4) $status[] = $red?'<font color="red">已作废</font>':'已作废';
    if($order->order_status==5) $status[] = $red?'<font color="red">已拒收</font>':'已拒收';
    $status[] = $order->pay_status?'已财审':'未财审';
    if($full) {
        $status[] = $order->is_pick?'已拣货':'未拣货';
        $status[] = $order->is_qc?'已复核':'未复核';
    }
    $status[] = $order->shipping_status?'已发货':'未发货';
    if (isset($order->is_ok)) {
        if($order->is_ok) $status[] = $red?'<font color="red">已完结</font>':'已完结';
        if(!$order->is_ok) $status[] = '未完结';
    }
    if (isset($order->odd)&&$order->odd) $status[] = $red?'<font color="red">问题单</font>':'问题单';
    if(!$full) {
        if (!empty($order->pick_sn)&&!$order->shipping_status) $status[] = $red?'<font color="red">拣货中</font>':'拣货中';
    }
    
    return $status;
}

function get_order_perm($order)
{
    $perms = array(
        'edit_order'=>FALSE,
        'edit_other'=>FALSE,
        'edit_pay'=>FALSE,'pay'=>FALSE,
        'lock'=>FALSE,'unlock'=>FALSE,
        'confirm'=>FALSE,'unconfirm'=>FALSE,
        'shipping'=>FALSE,'deny'=>FALSE,
        'change_shipping'=>FALSE,
        'invalid'=>FALSE,'advice' => FALSE,
        'edit_price' => FALSE,
        'odd' => FALSE, 'odd_cancel' => FALSE
        );
    //为提高性能，对于作废或拒收的单子，全部返回FALSE
    $order->is_available = ($order->order_status==0 || $order->order_status==1)&&!($order->pick_sn&&!$order->shipping_status);
    if(!$order->is_available) return $perms;
    
    $order->routing = get_order_routing($order);
    $order->waiting_F = !$order->pay_status && ($order->routing=='F'||$order->routing=='S'&&$order->shipping_status);
    //$order->waiting_S = !$order->shipping_status && ($order->routing=='S'||$order->routing=='F'&&$order->pay_status);
    $order->waiting_S = !$order->shipping_status && ($order->routing=='F'&& $order->pay_status);
    $order->order_amount = fix_price($order->order_price + $order->shipping_fee - $order->paid_price);
    // 编辑商品|收货人信息|支付方式 被自己锁定 订单未审核 有编辑权限
    $perms['edit_order'] = $order->order_status==0 && check_perm('order_edit');
    //可编辑其他信息,
    $perms['edit_other'] = check_perm('order_edit');
    // 添加删除支付记录
    $perms['edit_pay'] = $order->order_status==1 && check_perm('order_payment') && $order->waiting_F;
    // 财审
    $perms['pay'] = $order->order_amount==0 && $order->order_status && $order->waiting_F && check_perm('order_pay');
    $perms['unpay'] = $order->pay_status==1 && !$order->shipping_status && check_perm('order_pay');
    // 发货
    $perms['shipping'] = $order->order_status==1 && $order->waiting_S && check_perm('order_shipping');
    // 更改发货方式    
    $perms['change_shipping'] = $order->order_status==1 && check_perm('change_shipping');
    // 拒收
    $perms['deny'] = $order->order_status==1 && $order->shipping_status && !$order->pay_status && check_perm('order_deny');
    // 客审/反客审/完结
    $perms['confirm'] = $order->order_status==0 && check_perm('order_confirm') && !$order->odd;
    $perms['unconfirm'] = $order->order_status==1 && !$order->shipping_status && !$order->pay_status &&check_perm('order_unconfirm');
    $perms['ok'] = !$order->is_ok && $order->pay_status && $order->shipping_status && check_perm('order_ok');
    $perms['invalid'] = $order->order_status==0 && check_perm('order_edit');
    $perms['edit_price'] = $order->order_status==0 && check_perm('order_edit_price');
    $perms['odd'] = !$order->odd && check_perm('order_edit');
    // 未财审订单不允许取消异常标记
    $perms['odd_cancel'] = $order->odd && check_perm('order_edit');
    $perms['lock'] = !$order->lock_admin && in_array(TRUE,$perms);
    $perms['paying'] = $order->order_status==0 && check_perm('order_confirm') && !$order->odd && $order->order_amount > 0;
    
    $CI = & get_instance();
    $order->self_lock = $order->lock_admin==$CI->admin_id;
    if (!$order->self_lock) {
        foreach($perms as $key=>$perm) {
            if($key!='lock') $perms[$key] = FALSE;
        }
    }else{
        $perms['unlock'] = TRUE;
    }
    
    //以下是宽松权限，只要有解锁和加锁权限的都能进行操作
    $perms['advice'] = $perms['lock'] || $perms['unlock'];
    return $perms;
}

function update_shipping_fee($order,$shipping_fee=NULL)
{
    //自提不需要运费
    $CI = & get_instance();
	$CI->load->model('order_model');
    if($shipping_fee===NULL) $shipping_fee = calc_shipping_fee($order);
    if($order->shipping_fee!=$shipping_fee){
        $CI->order_model->update(array('shipping_fee'=>$shipping_fee),$order->order_id);
    }
}

function calc_shipping_fee($order){
    $CI = & get_instance();
    $CI->load->model('order_model');
    $CI->load->model('region_model');
    //$CI->load->model('provider_model');    
    //$CI->config->load('provider');
    //$default_shipping_config = $CI->config->item('provider_shipping_config');
    if (empty($order->shipping_id) || empty($order->province)){
        return 0;
    }
    $weight_obj = $CI->order_model->get_order_product_weight($order->order_id);
    $weight = $weight_obj['weight'];
    $fee = $CI->region_model->get_shipping_fee_province($order->shipping_id, $order->province);
    
    if (!$fee){
        $shipping_fee = SHIPPING_FEE_DEFAULT;
    } else {
        $first_wt = ($fee->first_weight > 0) ? $fee->first_weight : 1000;
        if ($weight <= $first_wt){
            $shipping_fee = $fee->shipping_fee1;
        } else {
            $shipping_fee = $fee->shipping_fee1 + ceil($weight-$first_wt)/1000*$fee->shipping_fee2;
        }
    }
    return fix_price($shipping_fee);
    // 取订单商品,判断供应商
    /*if(empty($order->province)){
        return SHIPPING_FEE_DEFAULT;
    }
    $provider_shipping_config = $CI->order_model->get_shipping_config($order->order_id);
    $shipping_config = $provider_shipping_config?$provider_shipping_config[$order->province]:$default_shipping_config[$order->province];
    $shipping_fee = $order->order_price>=$shipping_config[1]?0:$shipping_config[0];
    return fix_price($shipping_fee);*/
	/*
	if($order->province > 0 && $order->pay_id > 0){
		$CI = & get_instance();
		$CI->load->model('region_model');
		$region_shipping_fee = $CI->region_model->region_shipping_fee($order->province);
		if(!empty($region_shipping_fee)){
			if($order->pay_id == 1 && $region_shipping_fee->cod_shipping_fee > 0) {
				$shipping_fee = $region_shipping_fee->cod_shipping_fee;
			}else{
				if($region_shipping_fee->online_shipping_fee > 0) $shipping_fee = $region_shipping_fee->online_shipping_fee;
			}
		}
	}
    if($order->shipping_id==SHIPPING_ID_CAC) $shipping_fee = 0;
    if($order->order_price>=SHIPPING_FREE_ORDER_PRICE && SHIPPING_FREE_ORDER_PRICE!=-1) $shipping_fee = 0;
    */
}
//根据重量计算运费
function calc_weight_shipping_fee($order, $weight){
    $CI = & get_instance();
    $CI->load->model('order_model');
    $CI->load->model('region_model');
    if (empty($order->shipping_id) || empty($order->province)){
        return 0;
    }

    $fee = $CI->region_model->get_shipping_fee_province($order->shipping_id, $order->province);
    
    if (!$fee){
        $shipping_fee = SHIPPING_FEE_DEFAULT;
    } else {
        $first_wt = ($fee->first_weight > 0) ? $fee->first_weight : 1000;
        
        if ($weight <= $first_wt){
            $shipping_fee = $fee->shipping_fee1;
        } else {
            $shipping_fee = $fee->shipping_fee1 + ceil($weight-$first_wt)/1000*$fee->shipping_fee2;
        }
    }
    return fix_price($shipping_fee);
}

function get_order_routing($order)
{
    $routing = 'F';
    if(empty($order->source_id)||empty($order->shipping_id)||empty($order->pay_id)){
        return $routing;
    }
    $CI = & get_instance();
    $CI->load->model('order_model');
    $row = $CI->order_model->filter_routing(array(
            'source_id'=>$order->source_id,
            'shipping_id'=>$order->shipping_id,
            'pay_id'=>$order->pay_id
        ));
    return $row?$row->routing:$routing;
}

function valid_voucher($voucher,$order)
{
    $result = FALSE;
    $CI = & get_instance();
    if($voucher->user_id>0 && $voucher->user_id!=$order->user_id) return FALSE;
    if($voucher->used_number>=$voucher->repeat_number) return FALSE;
    if($voucher->start_date > $CI->time || $voucher->end_date < $CI->time) return FALSE;
    $order_payment = index_array($CI->order_model->order_payment($order->order_id),'is_discount');
    if(isset($order_payment[1])) return FALSE;
    
    $campaign = $CI->voucher_model->filter_campaign(array('campaign_id'=>$voucher->campaign_id));
    if($campaign->campaign_type!='repeat' && !$voucher->user_id){
        $CI->voucher_model->update(array('user_id'=>$order->user_id), $voucher->voucher_id);
    }
    $product_price = 0;
    $product_list = $campaign->product?explode(',',$campaign->product):array();
    $brand_list = $campaign->brand?explode(',',$campaign->brand):array();
    $category_list = $campaign->category?explode(',',$campaign->category):array();
    $provider_list = $campaign->provider?explode(',',$campaign->provider):array();
    $order_product = $CI->order_model->order_product($order->order_id);
    foreach ($order_product as $product) {
        if($product->package_id) continue;
        if(fix_price($product->product_price)!=fix_price($product->shop_price)) continue;
        if($product_list && !in_array(strval($product->product_id), $product_list)) continue;
        if($brand_list && !in_array(strval($product->brand_id), $brand_list)) continue;
        if($category_list && !in_array(strval($product->category_id), $category_list)) continue;
        if($provider_list && !in_array(strval($product->provider_id), $provider_list)) continue;
        $product_price += $product->total_price;
    }

    if(fix_price($product_price) < $voucher->min_order || $product_price<=0) return FALSE;

    return TRUE;
}

function check_gifts($order_id)
{
    $CI = &get_instance();
    $CI->db->trans_begin();
    $order = $CI->order_model->lock_order($order_id);
    if($order->order_status!=0) return FALSE;
    $now_gifts = $CI->order_model->all_product(array('order_id'=>$order_id,'discount_type'=>4));
    
    $gifts = $CI->order_model->all_gifts(array(
        'campaign_type' => 1,
        'is_use' => 1,
        'start_date <=' =>$CI->time,
        'end_date >=' =>$CI->time,
        'limit_price <=' =>$order->order_price
    ));
    foreach ($gifts as $k=>$g) {
        foreach ($now_gifts as $nk=>$ng) {
            if($ng->product_id==$g->product_id){
                unset($now_gifts[$nk],$gifts[$k]);
                break;
            }
        }
    }
    $product_ids = array();
    foreach($now_gifts as $g) $product_ids[] = $g->product_id;
    foreach($gifts as $g) $product_ids[] = $g->product_id;
    if(!$product_ids) return TRUE;
    $all_sub = $CI->product_model->all_sub(array('product_id'=>$product_ids));
    $sub_ids = array();
    foreach($all_sub as $sub) $sub_ids[] = $sub->sub_id;
    $all_sub = index_array($CI->product_model->lock_sub(array('sub_id'=>$sub_ids)),'sub_id');
    
    // 删除礼品
    $product_num = 0;
    foreach ($now_gifts as $p) {        
        //取sub值
        $sub_id = 0;
        foreach ($all_sub as $sub) {
            if ($sub->product_id==$p->product_id && $sub->color_id==$p->color_id && $sub->size_id==$p->size_id) {
                $sub_id=$sub->sub_id;
                break;
            }
        }
        if(!$sub_id) sys_msg('库存记录丢失',1);
        $sub = $all_sub[$sub_id];
        //删除商品
        $CI->order_model->delete_product($p->op_id);        
        //恢复库存
        $sub->gl_num += $p->product_num-$p->consign_num;
        $sub->wait_num -= $p->consign_num;
        if($sub->consign_num>=0) $sub->consign_num += $p->consign_num;
        $update = array('gl_num'=>$sub->gl_num,'wait_num'=>$sub->wait_num,'consign_num'=>$sub->consign_num);
        $CI->product_model->update_sub($update,$sub->sub_id);
        $all_sub[$sub_id] = $sub;
        //作废已分配的储位
        $CI->order_model->update_trans(
            array('trans_status'=>TRANS_STAT_CANCELED,'cancel_admin'=>$CI->admin_id,'cancel_date'=>$CI->time),
            array('trans_type'=>TRANS_TYPE_SALE_ORDER,'trans_sn'=>$order->order_sn,'sub_id'=>$p->op_id,'trans_status'=>TRANS_STAT_AWAIT_OUT)
        );
        //累计商品数变化
        $product_num -=1;
    }

    // 添加礼品
    foreach ($gifts as $g) {        
        $p = $CI->product_model->filter(array('product_id'=>$g->product_id));
        if(!$p) sys_msg('商品不存在',1);        
        //检查库存
        $sub_id = 0; 
        foreach ($all_sub as $sub) {
            if($sub->product_id!=$g->product_id) continue;
            if($sub->gl_num>$sub->wait_num){
                $sub_id = $sub->sub_id;
                break;
            }
            if($sub->consign_num>0 || $sub->consign_num==-2) $sub_id = $sub->sub_id;
        }
        if(!$sub_id) continue;//如果 没有库存，就不加了
        $sub = $all_sub[$sub_id];        
        
        //插入商品        
        $gl_num = $sub->gl_num>$sub->wait_num?1:0;//实库数量
        $consign_num = 1 - $gl_num;//虚库数量
        $update = array(
            'order_id'=>$order_id,
            'product_id'=>$sub->product_id,
            'color_id'=>$sub->color_id,
            'size_id'=>$sub->size_id,
            'product_num'=>1,
            'market_price'=>$p->market_price,
            //'cost_price'=>$p->cost_price,
            //'consign_price'=>$p->consign_price,
            //'consign_rate'=>$p->consign_rate,
            'shop_price'=>$p->shop_price,
            'product_price'=>0,
            //'provider_cess'=>$p->goods_cess,
            'total_price'=>0,
            'consign_num'=>$consign_num,
            'consign_mark'=>$consign_num,
            'discount_type' => 4
            );
        $op_id = $CI->order_model->insert_product($update);
        $sub->gl_num -= $gl_num;
        $sub->wait_num += $consign_num;
        if($sub->consign_num>0) $sub->consign_num -= $consign_num;
        $all_sub[$sub_id] = $sub;
        //扣除库存
        $update = array('gl_num'=>$sub->gl_num,'wait_num'=>$sub->wait_num,'consign_num'=>$sub->consign_num);
        $CI->product_model->update_sub($update,$sub->sub_id);
        //分配储位
        if ($gl_num) {
            $info = $CI->order_model->assign_trans($order,$sub,$gl_num,$op_id,0);
            if($info['err']) sys_msg($info['msg'],1);
        }
        //累计商品数变化
        $product_num +=1;
    }
    if($product_num) $CI->order_model->update(array('product_num'=>$order->product_num+$product_num),$order_id);
    $CI->db->trans_commit();
    return TRUE;

}
