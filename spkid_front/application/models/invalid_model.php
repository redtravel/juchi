<?php

class Invalid_model extends CI_Model {

    public function lock_order($order_id, $invoice_no = '') {
        if (!empty($order_id))
            $condition = "AND order_id = '$order_id'";
        else
            $condition = '';
        if ($condition == '')
            $condition = 'AND invoice_no = "' . $invoice_no . '"';
        $sql = "SELECT * FROM " . $this->db->dbprefix('order_info') . " WHERE 1=1 " . $condition . "  FOR UPDATE";
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function order_payment($order_id) {
        $sql = "SELECT op.*, p.pay_name, p.pay_code, a.admin_name, p.is_discount
                FROM " . $this->db->dbprefix('order_payment') . " AS op
                LEFT JOIN " . $this->db->dbprefix('payment_info') . " AS p ON op.pay_id = p.pay_id
                LEFT JOIN " . $this->db->dbprefix('admin_info') . " AS a ON op.payment_admin = a.admin_id
                WHERE op.order_id = ? AND op.is_return = 0 ORDER BY op.payment_id";
        $query = $this->db->query($sql, array(intval($order_id)));

        return $query->result();
    }

    public function order_product($order_id) {
        $sql = "SELECT op.*, p.product_name, p.product_sn,p.provider_productcode, p.unit_name,ti.shop_price,
                cat.category_name, b.brand_name, c.color_name, c.color_sn, s.size_name, s.size_sn,sub.sub_id,sub.gl_num,sub.consign_num as gl_consign_num,
                ti.batch_id,ti.consign_price,ti.cost_price,ti.consign_rate,ti.product_cess
                FROM " . $this->db->dbprefix('order_product') . " AS op
                LEFT JOIN " . $this->db->dbprefix('product_info') . " AS p ON op.product_id = p.product_id
                LEFT JOIN " . $this->db->dbprefix('product_category') . " AS cat ON cat.category_id = p.category_id
                LEFT JOIN " . $this->db->dbprefix('product_brand') . " AS b ON b.brand_id = p.brand_id
                LEFT JOIN " . $this->db->dbprefix('product_color') . " AS c ON c.color_id = op.color_id
                LEFT JOIN " . $this->db->dbprefix('product_size') . " AS s ON s.size_id = op.size_id
                LEFT JOIN " . $this->db->dbprefix('product_sub') . " AS sub ON op.product_id = sub.product_id AND op.color_id = sub.color_id AND op.size_id = sub.size_id
                LEFT JOIN " . $this->db->dbprefix('order_info') . " AS oi on oi.order_id=op.order_id 
                LEFT JOIN " . $this->db->dbprefix('transaction_info') . " AS ti on oi.order_sn=ti.trans_sn and ti.trans_type=3 AND ti.trans_status != 5 AND op.product_id = ti.product_id AND op.color_id = ti.color_id AND op.size_id = ti.size_id 
                WHERE op.order_id = ? 
                GROUP BY op.op_id";
        $query = $this->db->query($sql, array($order_id));
        return $query->result();
    }

    public function insert_payment($update) {
        $this->db->insert('order_payment', $update);
        return $this->db->insert_id();
    }

    public function update_trans($update, $filter) {
        $this->db->update('transaction_info', $update, $filter);
    }

    public function insert_user_account_log($data) {
        $this->db->insert('user_account_log', $data);
        return $this->db->insert_id();
    }

    //锁sub,支持三种方式
    public function lock_sub($filter) {
        if (isset($filter['sub_id']) && is_array($filter['sub_id'])) {
            $sql = "SELECT * FROM " . $this->db->dbprefix('product_sub') . " WHERE sub_id " . db_create_in($filter['sub_id']) . " FOR UPDATE";
            $query = $this->db->query($sql);
            return $query->result();
        } elseif (isset($filter['sub_id'])) {
            $sql = "SELECT * FROM " . $this->db->dbprefix('product_sub') . " WHERE sub_id = ? LIMIT 1 FOR UPDATE";
            $param = array($filter['sub_id']);
            $query = $this->db->query($sql, $param);
            return $query->row();
        } else {
            $sql = "SELECT * FROM " . $this->db->dbprefix('product_sub') . " WHERE product_id = ? AND color_id = ? AND size_id = ? LIMIT 1 FOR UPDATE";
            $param = array($filter['product_id'], $filter['color_id'], $filter['size_id']);
            $query = $this->db->query($sql, $param);
            return $query->row();
        }
    }

    public function update_sub($update, $sub_id) {
        $this->db->update('product_sub', $update, array('sub_id' => $sub_id));
    }
    public function delete_order($order_id)
    {
        $this->db->delete('order_info', array('order_id'=>$order_id));
    }
    
    public function delete_action_where($filter)
    {
        $this->db->delete('order_action',$filter);
    }
    public function delete_advice_where($filter)
    {
        $this->db->delete('order_advice',$filter);
    }
    public function update_order($update, $order_id)
    {
        $this->db->update('order_info', $update, array('order_id'=>$order_id));
    }
    public function insert_action($order, $action_note)
    {
        $update = array(
            'order_id' => $order->order_id,
            'is_return' => 0,
            'order_status' => isset($order->order_status)?$order->order_status:0,
            'shipping_status' => isset($order->shipping_status)?$order->shipping_status:0,
            'pay_status' => isset($order->pay_status)?$order->pay_status:0,
            'action_note' => $action_note,
            'create_admin' => -1,
            'create_date' => date('Y-m-d H:i:s')
            );
        $this->db->insert('order_action',$update);
        return $this->db->insert_id();
    }
    
    public function lock_voucher($voucher_sn) {
        $sql = "SELECT * FROM " . $this->db->dbprefix('voucher_record') . " WHERE voucher_sn = ? LIMIT 1 FOR UPDATE";
        $query = $this->db->query($sql, array($voucher_sn));
        return $query->row();
    }
    
    public function delete_payment($payment_id)
    {
        $this->db->delete('order_payment', array('payment_id'=>$payment_id));
    }
    
    public function update_voucher($update, $voucher_id) {
        $this->db->update('voucher_record', $update, array('voucher_id' => $voucher_id));
    }
}

?>
