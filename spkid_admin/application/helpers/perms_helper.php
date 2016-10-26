<?php
function get_user_perm(){
    $perms = array();
    $perms['user_edit'] = check_perm('user_edit') ? '1' : '2';
    $perms['user_view'] = check_perm('user_view') ? '1' : '2';
    $perms['useraddr_edit'] = check_perm('useraddr_edit') ? '1' : '2';
    $perms['useraddr_view'] = check_perm('useraddr_view') ? '1' : '2';
    $perms['user_type'] = check_perm('user_type') ? '1' : '2';
    $perms['uaccount_l_edit'] = check_perm('uaccount_l_edit') ? '1' : '2';
    $perms['uaccount_l_view'] = check_perm('uaccount_l_view') ? '1' : '2';
    return $perms;
}

function get_user_recharge_perm(){
    $perms = array();
    $perms['user_recharge_view'] = check_perm('user_recharge_view') ? '1' : '2';
    $perms['user_recharge_del'] = check_perm('user_recharge_del') ? '1' : '2';
    $perms['user_recharge_author'] = check_perm('user_recharge_author') ? '1' : '2';
    $perms['user_recharge_edit'] = check_perm('user_recharge_edit') ? '1' : '2';
    return $perms;
}

function get_user_rank_perm(){
    $perms = array();
    $perms['rank_view'] = check_perm('rank_view') ? '1' : '2';
    $perms['rank_edit'] = check_perm('rank_edit') ? '1' : '2';
    return $perms;
}

function get_account_kind_perm(){
    $perms = array();
    $perms['uaccount_k_view'] = check_perm('uaccount_k_view') ? '1' : '2';
    $perms['uaccount_k_edit'] = check_perm('uaccount_k_edit') ? '1' : '2';
    return $perms;
}

function get_shipping_perm(){
    $perms = array();
    $perms['shipping_edit'] = check_perm('shipping_edit') ? '1' : '2';
    $perms['shipping_view'] = check_perm('shipping_view') ? '1' : '2';
    $perms['shipping_area_edit'] = check_perm('shipping_area_edit') ? '1' : '2';
    $perms['shipping_area_view'] = check_perm('shipping_area_view') ? '1' : '2';
    return $perms;
}

function get_finance_perm(){
    $perms = array();
    $perms['finance_invoicing_su_report'] = check_perm('finance_invoicing_su_report') ? '1' : '2';
    $perms['finance_invoicing_de_report'] = check_perm('finance_invoicing_de_report') ? '1' : '2';
    return $perms;
}

function get_depot_perm(){
    $perms = array();
    $perms['invoicing_summary_report'] = check_perm('invoicing_summary_report') ? '1' : '2';
    $perms['invoicing_details_report'] = check_perm('invoicing_details_report') ? '1' : '2';
    //$perms['depot_real_inventory_report'] = check_perm('depot_real_inventory_report') ? '1' : '2';
    $perms['inventory_details_report'] = check_perm('inventory_details_report') ? '1' : '2';
    
    $perms['purchase_main_report'] = check_perm('purchase_main_report') ? '1' : '2';
    $perms['purchase_main_detail_report'] = check_perm('purchase_main_detail_report') ? '1' : '2';
    return $perms;
}

function get_order_profits_perm(){
    $perms = array();
    $perms['order_profits_detail_report'] = check_perm('order_profits_detail_report') ? '1' : '2';
    $perms['order_profits_return_report'] = check_perm('order_profits_return_report') ? '1' : '2';
    $perms['order_profits_summary_report_to'] = check_perm('order_profits_summary_report_to') ? '1' : '2';
    $perms['order_profits_summary_report_to2'] = check_perm('order_profits_summary_report_to2') ? '1' : '2';
    $perms['order_profits_sales_report'] = check_perm('order_profits_sales_report') ? '1' : '2';
    return $perms;
}

function get_region_perm(){
    $perms = array();
    $perms['region_edit'] = check_perm('region_edit') ? '1' : '2';
    $perms['region_view'] = check_perm('region_view') ? '1' : '2';
    return $perms;
}


function get_order_source_perm(){
    $perms = array();
    $perms['order_source_view'] = check_perm('order_source_view') ? '1' : '2';
    $perms['order_source_edit'] = check_perm('order_source_edit') ? '1' : '2';
    return $perms;
}

function get_order_advice_type_perm(){
    $perms = array();
    $perms['suggestion_edit'] = check_perm('suggestion_edit') ? '1' : '2';
    $perms['suggestion_view'] = check_perm('suggestion_view') ? '1' : '2';
    return $perms;
}

function get_friend_perm(){
    $perms = array();
    $perms['friendlink_view'] = check_perm('friendlink_view') ? '1' : '2';
    $perms['friendlink_edit'] = check_perm('friendlink_edit') ? '1' : '2';
    return $perms;
}

function get_art_perm(){
    $perms = array();
    $perms['art_view'] = check_perm('art_view') ? '1' : '2';
    $perms['art_edit'] = check_perm('art_edit') ? '1' : '2';
    $perms['art_cat_view'] = check_perm('art_cat_view') ? '1' : '2';
    $perms['art_cat_edit'] = check_perm('art_cat_edit') ? '1' : '2';
    return $perms;
}

function get_rush_perm($perm){
    $CI=&get_instance();
    $perms = array();
    $perms['rush_view'] = check_perm('rush_view');
    $perms['rush_product_edit'] = check_perm('rush_product_edit')&&$perm->status<2&&$perm->end_date>$CI->time;
    $perms['rush_edit'] = ((check_perm('rush_edit')&&$perm->status==0)||(check_perm('rush_supper_edit')&&$perm->status==1))&&$perm->end_date>$CI->time;
    $perms['rush_audit'] = check_perm('rush_audit') && $perm->status==0&&$perm->end_date>$CI->time;
    return $perms;
}

function get_liuyan_perm(){
    $perms = array();
    $perms['liuyan_view'] = check_perm('liuyan_view') ? '1' : '2';
    $perms['liuyan_edit'] = check_perm('liuyan_edit') ? '1' : '2';
    $perms['liuyan_aurep'] = check_perm('liuyan_aurep') ? '1' : '2';
    return $perms;
}

function get_front_perm(){
    $perms = array();
    $perms['front_ad_po_edit'] = check_perm('front_ad_po_edit') ? '1' : '2';
    $perms['front_ad_po_view'] = check_perm('front_ad_po_view') ? '1' : '2';
    $perms['front_ad_edit'] = check_perm('front_ad_edit') ? '1' : '2';
    $perms['front_ad_view'] = check_perm('front_ad_view') ? '1' : '2';
    return $perms;
}

function get_single_page_perm(){
    $perms = array();
    $perms['single_page_edit'] = check_perm('single_page_edit') ? '1' : '2';
    $perms['single_page_view'] = check_perm('single_page_view') ? '1' : '2';
    return $perms;
}

function get_mail_template_perm(){
    $perms = array();
    $perms['mail_template_edit'] = check_perm('mail_template_edit') ? '1' : '2';
    $perms['mail_template_view'] = check_perm('mail_template_view') ? '1' : '2';
    return $perms;
}

function get_payment_perm(){
    $perms = array();
    $perms['payment_edit'] = check_perm('payment_edit') ? '1' : '2';
    $perms['payment_view'] = check_perm('payment_view') ? '1' : '2';
    return $perms;
}

function get_yyw_pv_report() {
    $perms = array();
    $perms['yyw_pv_report_view'] = check_perm('yyw_pv_report_view') ? '1' : '2';
    
    return $perms;
}

function get_order_refund_perm(){
    $perms = array();
    $perms['order_refund_delete'] = check_perm('order_refund_delete') ? '1' : '2';
    $perms['order_refund_edit'] = check_perm('order_refund_edit') ? '1' : '2';
    return $perms;
}
?>
