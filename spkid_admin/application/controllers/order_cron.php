<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Order_cron extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        ini_set('max_execution_time', '0');
        $this->time=date('Y-m-d H:i:s');
        $this->admin_id=0;
    }
    
    /**
     * 设置定单的快递方式
     */
    public function express($lifetime=600)
    {
        $this->load->model('order_model');
        $start_time = time();
        $batch_num = 100;
        $sql = "select order_id, province, city, district, order_status, shipping_status, pay_status, genre_id 
                from ty_order_info 
                where shipping_id = 0 and order_status = 0 and lock_admin = 0 and create_date >= date_sub(now(), INTERVAL 60  MINUTE)
                limit ?";
        $shipping_sql = "select si.shipping_id, si.shipping_name
                        from ty_shipping_area_region as sar
                        left join ty_shipping_area as sa on sar.shipping_area_id = sa.shipping_area_id
                        left join ty_shipping_info as si on sa.shipping_id = si.shipping_id
                        where sar .region_id in (?,?,?) and si.is_use=1 order by sort_order ASC
                        limit 1";
        $order_sql = "update ty_order_info set shipping_id = ? where order_id = ? and shipping_id = 0 and lock_admin=0";
        //while(true)
        //{
            if(time()-$start_time>$lifetime) return;
            $query = $this->db->query($sql, array($batch_num));
            $result = $query->result();
            if(empty($result))
            {
                return;
            }
            foreach($result as $order)
            {

                $coop_id = $this->order_model->get_order_cooperation_id($order->order_id);
                if($coop_id == COOPERATION_TYPE_TMALL){
                    $shipping_id = SHIPPING_ID_PINGTAI;
                    $shipping_name = '平台快递';
                }elseif($order->genre_id == PRODUCT_COURSE_TYPE){
                    $shipping_id = SHIPPING_ID_DEFAULT; 
                    $shipping_name = '快捷';
                }else{
                    $row = $this->db->query($shipping_sql, array($order->province, $order->city, $order->district))->row();

                    if(empty($row))
                    {
                        continue;
                    }
                    $shipping_id = $row->shipping_id;
                    $shipping_name = $row->shipping_name;
                }

                $this->db->query($order_sql, array($shipping_id, $order->order_id));
                $this->order_model->insert_action($order, '自动分配配送方式'.$shipping_name);
                usleep(1000);
            }
        //}
    }
    
    //订单客审
    public function confirm(){
        $this->load->model('order_model');
        $this->load->model('user_model');
        $this->load->model('user_account_log_model');
        $this->load->helper('order');
        //货到付款订单
        $cod_order_list = $this->order_model->get_cod_order();
        $cod_order_ids = array();
        if(!empty($cod_order_list)) {
            foreach($cod_order_list as $cod_order){
                $cod_order_ids[] = $cod_order->order_id;
            }
        }
        //非货到付款订单
        $uncod_order_list = $this->order_model->get_uncod_order();
        $uncod_order_ids = array();
        if(!empty($uncod_order_list)) {
            foreach($uncod_order_list as $uncod_order){
                $uncod_order_ids[] = $uncod_order->order_id;
            }
        }
        $order_ids = array_unique(array_merge($cod_order_ids,$uncod_order_ids));
        if(empty($order_ids)) return false;

        //执行订单自动客审
        $i = 0;
        foreach($order_ids as $key => $order_id){
            if($i >= 20) {
                $i = 0;
                sleep(1);
            }
            //处理订单开始
            $this->order_model->n_write_log($order_id, $order_id.'订单处理开始-------------------------------------------------------', '2');
            $this->db->trans_begin();
            $order = $this->order_model->lock_order($order_id);
            if(!$order) {
                $this->order_model->n_write_log($order_id,'读取订单信息错误!');
                continue;
            }
            $user = $this->user_model->lock_user($order->user_id);
            $product = $this->order_model->filter_product(array('order_id'=>$order->order_id));
            if(!$product) {
                $this->order_model->n_write_log($order_id,'订单中没有商品!');
                continue;
            }
            $routing = $this->order_model->filter_routing(array('source_id'=>$order->source_id,'shipping_id'=>$order->shipping_id,'pay_id'=>$order->pay_id,'show_type !='=>4));
            if(!$routing) {
                $this->order_model->n_write_log($order_id,'订单流程错误!');
                continue;
            }
            if( $order->genre_id != PRODUCT_COURSE_TYPE && $order->shipping_id!=SHIPPING_ID_CAC){
                    $available_shipping = $this->order_model->available_shipping(array(
                            'source_id'=>$order->source_id,
                            'pay_id'=>$order->pay_id, 'shipping_id'=>$order->shipping_id,
                            'region_ids' => array($order->country, $order->province, $order->city, $order->district)
                    ));
                    if(!$available_shipping) {
                        $this->order_model->n_write_log($order_id,'配送地区与配送方式不匹配!');
                        continue;
                    }
            }
            $order = format_order($order);
            if($order->order_amount>0 && in_array($order->pay_id, array(PAY_ID_VOUCHER,PAY_ID_BALANCE))) {
                $this->order_model->n_write_log($order_id,'有待付金额的订单，不能选择余额或现金券支付!');
                continue;
            }

            $update = array(
                    'order_status' => 1,
                    'confirm_admin' => $this->admin_id,
                    'confirm_date' => $this->time,
                    'lock_admin' => 0
            );
            $action_note = '自动审核订单';
            if($order->order_amount<0){
                    $this->order_model->insert_payment(array(
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
                            'change_desc' => sprintf("订单 %s 自动客审，多付金额返还帐户。",$order->order_sn),
                            'change_code' => 'order_payback',
                            'create_admin' => $this->admin_id,
                            'create_date' => $this->time
                    ));		
                    // 更新用户表
                    $this->user_model->update(array('user_money'=>fix_price($user->user_money-$order->order_amount)),$user->user_id);
                    $update['paid_price'] = fix_price($order->paid_price + $order->order_amount);
            }

            if($routing->routing=='F' && $order->order_amount<=0){
                    $update['pay_status'] = 1;
                    $update['finance_admin'] = $this->admin_id;
                    $update['finance_date'] = $this->time;
                    $this->order_model->update_trans(
                            array('finance_check_admin'=>$this->admin_id,'finance_check_date'=>$this->time),
                            array('trans_type'=>TRANS_TYPE_SALE_ORDER,'trans_sn'=>$order->order_sn,'trans_status'=>TRANS_STAT_AWAIT_OUT)
                    );
                    $action_note .= '，订单自动财审';
            }
            $this->order_model->update($update,$order_id);
            foreach($update as $key=>$val) $order->$key = $val;
            $this->order_model->insert_action($order,$action_note);
            $this->db->trans_commit();

            $this->order_model->n_write_log($order_id,'自动客审成功!', 1);
            $this->order_model->n_write_log($order_id, $order_id.'-------------------------------------------------------订单处理结束', '2');
            $i = $i + 1;
        }
        return true;
    }
}
