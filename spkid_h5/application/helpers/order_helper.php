<?php

function calc_point_amount($order,$order_payment,$rank)
{
    $point = $order->order_price;
    foreach ($order_payment as $p) {
        if ($p->is_discount) $point -= $p->payment_money;
    }
    if($order->is_online) $point*=2;
    $point *= $rank->buying_point_rate;
    return round($point);
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

function order_status($order)
{
    $status = '';
    switch ($order->order_status) {
        case 0:
            $status = ($order->is_online && $order->paid_price < ($order->order_price + $order->shipping_fee))  ?'待付款':'已付款';
            break;

        case 1:
            if($order->shipping_id==SHIPPING_ID_CAC||$order->pay_id==PAY_ID_COD){
                //先发货,不存在待付款的情况。
                $status=$order->shipping_status?'已发货':'待发货';                   
            }else{
                //先付款
                if (round($order->order_price+$order->shipping_fee-$order->paid_price,2)>0) {
                    $status='待付款';
                }elseif (!$order->shipping_status) {
                    $status='待发货';
                }else {
                    $status='已发货';
                }
            }
            break;

        default:
            $status = '已作废';
            break;
    }
    return $status;
}

function pay_status($order)
{
    if($order->order_status==4 || $order->order_status==5) return '--';
    return $order->order_price + $order->shipping_fee > $order->paid_price?'等待付款':'已付款';

}

function get_order_perm($order)
{
    $perms = array(
        'edit_order'=>FALSE,
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
    $order->waiting_S = !$order->shipping_status && ($order->routing=='S'||$order->routing=='F'&&$order->pay_status);
    $order->order_amount = fix_price($order->order_price + $order->shipping_fee - $order->paid_price);
    // 编辑商品|收货人信息|其它信息|支付方式 被自己锁定 订单未审核 有编辑权限
    $perms['edit_order'] = $order->order_status==0 && check_perm('order_edit');
    // 添加删除支付记录
    $perms['edit_pay'] = $order->order_status==1 && check_perm('order_payment') && $order->waiting_F;
    // 财审
    $perms['pay'] = $order->order_amount==0 && $order->order_status && $order->waiting_F && check_perm('order_pay');
    // 发货
    $perms['shipping'] = $order->order_status==1 && $order->waiting_S && check_perm('order_shipping');
    // 更改发货方式    
    $perms['change_shipping'] = $order->order_status==1 && check_perm('order_change_shipping');
    // 拒收
    $perms['deny'] = $order->order_status==1 && $order->shipping_status && !$order->pay_status && check_perm('order_shipping');
    // 客审/反客审/完结
    $perms['confirm'] = $order->order_status==0 && check_perm('order_confirm');
    $perms['unconfirm'] = $order->order_status==1 && !$order->shipping_status && !$order->pay_status &&check_perm('order_unconfirm');
    $perms['ok'] = !$order->is_ok && $order->pay_status && $order->shipping_status && check_perm('order_ok');
    $perms['invalid'] = $order->order_status==0 && check_perm('order_edit');
    $perms['edit_price'] = $order->order_status==0 && check_perm('order_edit_price');
    $perms['odd'] = !$order->odd && check_perm('order_edit');
    $perms['odd_cancel'] = $order->odd && check_perm('order_edit');
    $perms['lock'] = !$order->lock_admin && in_array(TRUE,$perms);
    
    $order->self_lock = empty($order->lock_admin) || $order->lock_admin == 0;
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

function check_perm($v) {
    return true;
}

function update_shipping_fee($order,$shipping_fee=NULL)
{
    //自提不需要运费
    $CI = & get_instance();
    if($shipping_fee===NULL) $shipping_fee = calc_shipping_fee($order);
    if($order->shipping_fee!=$shipping_fee){
        $CI->order_model->update(array('shipping_fee'=>$shipping_fee),$order->order_id);
    }
}

function calc_shipping_fee($order){
    $shipping_fee = SHIPPING_FEE_DEFAULT;
    if($order->shipping_id==SHIPPING_ID_CAC) $shipping_fee = 0;
    if($order->order_price>=SHIPPING_FREE_ORDER_PRICE && SHIPPING_FREE_ORDER_PRICE!=-1) $shipping_fee = 0;

    return fix_price($shipping_fee);
}

/**
 * 获取订单支付跟踪号
 * @return type
 */
function get_pay_track_sn() {
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);
    return "PT".date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
* 验证订单是否满足申请退货
* $check_goods_num 是否检查数量
*/
function check_order_apply_return($order,$check_goods_num=false){
   $apply_return=false;
   //已退完货的商品不能再申请退货
   if($order->product_num == $order->return_number){
       return false;
   }
   //合作方式非天猫发货(3)、第三方直发(4)的订单不能申请退货
//   if($order->provider_cooperation != 3 && $order->provider_cooperation != 4){
//       return false;
//   }
   // 虚发订单不能申请退货
   if (!$order->shipping_true) {
       return false;
   }
   // 已作废订单不能申请退货
   if ($order->order_status == 5) {
       return false;
   }
   if($order->shipping_status == 1){
       //大于15天不能申请退货
       if(time()+28800-strtotime($order->shipping_date)<86400*15){
           $apply_return=true;
       }
   }
   //所有商品都已申请退货则不能再申请退货
   if($check_goods_num){
       if($order->product_num == $order->ari_product_number){
           $apply_return=false;
       }
   }
   return $apply_return;
}
