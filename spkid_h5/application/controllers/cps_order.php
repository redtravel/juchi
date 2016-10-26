<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cps_order
 *
 * @author jasper
 */
class cps_order extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    function duomai($start = null, $end = null) {
        if (empty($start)) $start = date("Y-m-d",strtotime("last month"));
        if (empty($end)) $end = date('Y-m-d', strtotime("+1 day"));
        $sql =<<<SQL
SELECT 
    oi.order_sn
  , oi.create_date
  , (SELECT ifnull(sum(payment_money), 0) AS payment_money 
                       FROM ty_order_payment op
                       INNER JOIN ty_payment_info pai
                       USING(pay_id)
                       WHERE pai.is_discount = 1
                       AND op.order_id = oi.order_id) AS order_discount
  , oi.order_price
  , replace(group_concat(pi.product_sn), ',', '|') AS product_sn
  , replace(group_concat(pi.product_name), ',', '|') AS product_name
  , replace(group_concat(op.product_num), ',', '|') AS promote_num
  , replace(group_concat(pi.promote_price), ',', '|') AS promote_price
  , replace(group_concat('*'), ',', '|') AS product_ex
  , replace(group_concat(pc.category_id), ',', '|') AS category_id
  , replace(group_concat(pc.category_name), ',', '|') AS category_name
  , case oi.order_status 
    when 0 then '新订单'
    when 1 then if (oi.is_ok_date > '0000-00-00 00:00:00', '已完结', if (oi.shipping_date > '0000-00-00 00:00:00', '已发货', if (oi.finance_date > '0000-00-00 00:00:00', '待发货', '待支付')))
    when 4 then '已作废'
    else '新订单'
    end AS order_status
  , cl.cps_log_data
  , c.cps_data
FROM ty_order_info oi
INNER JOIN ty_order_product op
USING (order_id)
INNER JOIN ty_product_info pi
USING (product_id)
INNER JOIN ty_product_category pc
USING (category_id)
INNER JOIN ty_cps_log cl
USING (order_id)
INNER JOIN ty_cps c
USING (cps_id)
WHERE c.cps_sn = 'duomai'
AND oi.create_date BETWEEN ? AND ?
GROUP BY order_id;
SQL;
        $db_r = $this->load->database('default_r', TRUE, TRUE);
        $query = $db_r->query($sql, array($start, $end));
        $result = $query->result();
        foreach($result as $item) {
            $cps_data = json_decode($item->cps_data);
            $cps_log_data = json_decode($item->cps_log_data);
            $cps_discount = $cps_data->discount;
            $feedback = $cps_log_data->feedback;
            $order_price = $item->order_price - $item->order_discount;
            $order_balance = $order_price * $cps_discount;
            $product_ex = str_replace('*', $cps_discount, $item->product_ex);
            echo "$feedback\t$item->order_sn\t$item->create_date\t$order_price\t$item->order_discount\t$item->order_status\t$cps_discount\t$order_balance\t$item->product_sn\t$item->product_name\t$item->promote_num\t$item->promote_price\t$product_ex\t$item->category_id\t$item->category_name\r\n";
        }
    }
    
    function yiqifa($date = null, $cid = null) {
        if (empty($date)) $date = date ("Y-m-d");
        $params = array($date . " 00:00:00", $date . " 23:59:59");
        $sql = <<< SQL
SELECT 
    oi.order_sn
  , oi.create_date
  , (SELECT ifnull(sum(payment_money), 0) AS payment_money 
                       FROM ty_order_payment op
                       INNER JOIN ty_payment_info pai
                       USING(pay_id)
                       WHERE pai.is_discount = 1
                       AND op.order_id = oi.order_id) AS order_discount
  , (SELECT group_concat(op.payment_account) AS payment_account 
                       FROM ty_order_payment op
                       INNER JOIN ty_payment_info pai
                       USING(pay_id)
                       WHERE pai.is_discount = 1
                       AND op.order_id = oi.order_id) AS payment_account  
  , (SELECT group_concat(pai.pay_name) AS payment_account 
                       FROM ty_order_payment op
                       INNER JOIN ty_payment_info pai
                       USING(pay_id)
                       WHERE pai.is_discount = 0
                       AND op.order_id = oi.order_id) AS pay_name  
  , oi.order_price
  , oi.shipping_fee
  , group_concat(pi.product_sn) AS product_sn
  , group_concat(pi.product_name) AS product_name
  , group_concat(op.product_num) AS promote_num
  , group_concat(pi.promote_price) AS promote_price
  , group_concat('*') AS product_ex
  , group_concat(pc.category_id) AS category_id
  , group_concat(pc.category_name) AS category_name
  , case oi.order_status 
    when 0 then '新订单'
    when 1 then if (oi.is_ok_date > '0000-00-00 00:00:00', '已完结', if (oi.shipping_date > '0000-00-00 00:00:00', '已发货', if (oi.finance_date > '0000-00-00 00:00:00', '待发货', '待支付')))
    when 4 then '已作废'
    else '新订单'
    end AS order_status
  , if (oi.finance_date > '0000-00-00 00:00:00', '已支付', '未支付') AS pay_status
  , cl.cps_log_data
  , c.cps_data
FROM ty_order_info oi
INNER JOIN ty_order_product op
USING (order_id)
INNER JOIN ty_product_info pi
USING (product_id)
INNER JOIN ty_product_category pc
USING (category_id)
INNER JOIN ty_cps_log cl
USING (order_id)
INNER JOIN ty_cps c
USING (cps_id)
WHERE c.cps_sn = 'yiqifa'
AND oi.create_date BETWEEN ? AND ? 
SQL;
        if (!empty($cid)) {
            $sql .= " AND cl.cps_user_name = ? ";
            $params[] = $cid;
        }
        $sql .= " GROUP BY order_id;";
        $db_r = $this->load->database('default_r', TRUE, TRUE);
        $query = $db_r->query($sql, $params);
        $result = $query->result();
        foreach($result as $item) {
            $cps_log_data = json_decode($item->cps_log_data);
            echo "$cps_log_data->wi||$item->create_date||$item->order_sn||$item->order_price||$item->product_name||$item->order_status||$item->pay_status||$item->pay_name||$item->shipping_fee||$item->order_discount||$item->payment_account\n";
        }
    }
    
    function linktech($date = null ){
        if (empty($date)){
	    $date = date("Y-m-d");
	}else {
	    $date = date("Y-m-d",  strtotime($date));
	}
	$params = array($date . " 00:00:00", $date . " 23:59:59");
        $sql = <<< SQL
SELECT 
    oi.create_date
  , oi.order_sn
  , pi.product_sn
  , op.product_num
  , pi.promote_price
  , pc.category_name
  , cl.cps_log_data
FROM ty_order_info oi
INNER JOIN ty_order_product op
USING (order_id)
INNER JOIN ty_product_info pi
USING (product_id)
INNER JOIN ty_product_category pc
USING (category_id)
INNER JOIN ty_cps_log cl
USING (order_id)
INNER JOIN ty_cps c
USING (cps_id)
WHERE c.cps_sn = 'linktech'
AND oi.create_date BETWEEN ? AND ?
GROUP BY pi.product_id;
SQL;
	$db_r = $this->load->database('default_r', TRUE, TRUE);
        $query = $db_r->query($sql, $params);
        $result = $query->result();
        foreach($result as $item) {
            $cps_log_data = json_decode($item->cps_log_data);
	    $create_time = date("His",strtotime(mb_substr($item->create_date, 11) ) );
            echo "2\t$create_time\t$cps_log_data->a_id\t$item->order_sn\t$item->product_sn\t$cps_log_data->mbr_id\t$item->product_num\t$item->promote_price\t$item->category_name\n";
        }
    }
}
