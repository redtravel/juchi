<?php

#doc
#	classname:	Depotio_model
#	scope:		PUBLIC
#
#/doc

class Purchase_consign_model extends CI_Model {

    public function find_page($filter) {
        $from = " FROM " . $this->db->dbprefix('purchase_consign') . " AS a "
                . " LEFT JOIN " . $this->db->dbprefix('product_provider') . " AS b ON a.provider_id=b.provider_id"
                . " LEFT JOIN " . $this->db->dbprefix('product_brand') . " AS pb ON pb.brand_id = a.brand_id"
                . " LEFT JOIN " . $this->db->dbprefix('purchase_batch') . " AS batch ON batch.batch_id = a.batch_id";
        $where = " WHERE 1 ";
        $param = array();

        if (!empty($filter['provider_id'])) {
            $where .= " AND a.provider_id = ? ";
            $param[] = $filter['provider_id'];
        }

        if (!empty($filter['order_sn'])) {
            $o_q = $this->db->query("SELECT order_id,order_sn FROM " . $this->db->dbprefix('order_info') . " AS i WHERE i.order_sn = ?", array($filter['order_sn']));
            $r_cell = $o_q->row();
            if(empty($r_cell)){
                $filter['record_count'] = 0;
                $filter = page_and_size($filter);
                return array('list' => array(), 'filter' => $filter);
            }
            $where .= " AND EXISTS ( SELECT 1 FROM  " . $this->db->dbprefix('purchase_consign_detail') . " AS d WHERE d.order_id= $r_cell->order_id AND d.purchase_code = a.purchase_code )";
        }
        $filter['sort_by'] = empty($filter['sort_by']) ? ' a.id DESC ' : trim($filter['sort_by']);
        $filter['sort_order'] = empty($filter['sort_order']) ? '' : trim($filter['sort_order']);

        $sql = "SELECT COUNT(a.id) AS total " . $from . $where;

        $query = $this->db->query($sql, $param);
        $row = $query->row();
        $query->free_result();
        $filter['record_count'] = (int) $row->total;
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0) {
            return array('list' => array(), 'filter' => $filter);
        }

        $sql = "SELECT a.*,pb.brand_name,batch.batch_code,b.provider_name,c.admin_name as create_name,d.purchase_id " .
                $from .
                " LEFT JOIN " . $this->db->dbprefix('admin_info') . " c ON c.admin_id = a.create_admin" .
                " LEFT JOIN " . $this->db->dbprefix('purchase_main') . " d ON d.purchase_code = a.purchase_code" .
                $where .
                " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'] .
                " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

        $query = $this->db->query($sql, $param);
        $return = $query->result();
        $query->free_result();

        return array('list' => $return, 'filter' => $filter);
    }

    public function get_last_time($provider_id, $batch_id) {
        $sql = "SELECT MAX(end_time) AS last_time FROM " . $this->db->dbprefix('purchase_consign') . " WHERE provider_id = ? AND batch_id = ?";
        $query = $this->db->query($sql, array('provider_id' => $provider_id, "batch_id" => $batch_id));
        return $query->row()->last_time;
    }

    public function get_consign_list($filter) {
        $from = " FROM " . $this->db->dbprefix('product_info') . " AS a " .
                " LEFT JOIN " . $this->db->dbprefix('order_product') . " AS b ON a.product_id=b.product_id " .
                " LEFT JOIN " . $this->db->dbprefix('order_info') . " AS c ON b.order_id=c.order_id " .
                " LEFT JOIN " . $this->db->dbprefix('product_brand') . " AS d ON a.brand_id=d.brand_id " .
                " LEFT JOIN " . $this->db->dbprefix('product_color') . " AS e ON b.color_id=e.color_id " .
                " LEFT JOIN " . $this->db->dbprefix('product_size') . " AS f ON b.size_id=f.size_id " .
                " LEFT JOIN " . $this->db->dbprefix('purchase_batch') . " AS i ON i.provider_id = a.provider_id AND i.batch_type = 0" .
                " INNER JOIN " . $this->db->dbprefix('product_cost') . " AS pc ON a.product_id = pc.product_id AND pc.batch_id = i.batch_id";
        $where = " WHERE 1=1 AND c.`shipping_status`=0";
        $param = array();

        if (!empty($filter['provider_id'])) {
            $where .= " AND a.provider_id = ? ";
            $param[] = $filter['provider_id'];
        }

        if (!empty($filter['batch_id'])) {
            $where .= " AND i.batch_id = ? ";
            $param[] = $filter['batch_id'];
        }

        if (!empty($filter['start_time'])) {
            $where .= " AND c.confirm_date >= ? ";
            $param[] = $filter['start_time'];
        }

        if (!empty($filter['end_time'])) {
            $where .= " AND c.confirm_date <= ? ";
            $param[] = $filter['end_time'];
        }

        $sql = "SELECT a.product_name,a.product_sn,a.provider_productcode," .
                "b.product_id,b.color_id,b.size_id,d.brand_name,e.color_name," .
                "f.size_name,sum(b.consign_num) AS num,IFNULL(pc.product_id,0) AS is_cost " .
                $from . $where .
                "GROUP BY b.product_id,b.color_id,b.size_id HAVING num > 0";
        $query = $this->db->query($sql, $param);
        $list = $query->result();
        $query->free_result();
        return array('list' => $list, "order_list" => $this->query_consign_list($filter), 'filter' => $filter);
    }

    public function query_consign_list($filter) {
        $from = " FROM " . $this->db->dbprefix('product_info') . " AS a " .
                " INNER JOIN " . $this->db->dbprefix('order_product') . " AS b ON a.product_id=b.product_id " .
                " INNER JOIN " . $this->db->dbprefix('order_info') . " AS c ON b.order_id=c.order_id AND c.order_status = 1 AND c.is_ok = 0" .
                " LEFT JOIN " . $this->db->dbprefix('product_brand') . " AS d ON a.brand_id=d.brand_id " .
                " LEFT JOIN " . $this->db->dbprefix('product_color') . " AS e ON b.color_id=e.color_id " .
                " LEFT JOIN " . $this->db->dbprefix('product_size') . " AS f ON b.size_id=f.size_id " .
                " INNER JOIN " . $this->db->dbprefix('purchase_batch') . " AS i ON i.provider_id = a.provider_id AND i.batch_type = 0 AND i.is_reckoned = 0 AND i.is_consign = 1" .
                " INNER JOIN " . $this->db->dbprefix('product_cost') . " AS pc ON a.product_id = pc.product_id AND pc.batch_id = i.batch_id";
        $where = " WHERE b.consign_num > 0 ";
        $param = array();

        if (!empty($filter['provider_id'])) {
            $where .= " AND a.provider_id = ? ";
            $param[] = $filter['provider_id'];
        }

        if (!empty($filter['batch_id'])) {
            $where .= " AND i.batch_id = ? ";
            $param[] = $filter['batch_id'];
        }

        if (!empty($filter['brand_id'])) {
            $where .= " AND a.brand_id = ? ";
            $param[] = $filter['brand_id'];
        }

        if (!empty($filter['start_time'])) {
            $where .= " AND c.confirm_date > ? ";
            $param[] = $filter['start_time'];
        }

        if (!empty($filter['end_time'])) {
            $where .= " AND c.confirm_date <= ? ";
            $param[] = $filter['end_time'];
        }

        $sql = "SELECT a.product_name,a.product_sn,a.provider_productcode,a.brand_id," .
                "c.order_id,b.op_id,c.order_sn,c.confirm_date," .
                "b.product_id,b.color_id,b.size_id,d.brand_name,e.color_name," .
                "f.size_name,b.consign_num AS num,IFNULL(pc.product_id,0) AS is_cost " .
                $from . $where;
        $query = $this->db->query($sql, $param);
        $list = $query->result();
        $query->free_result();
        $order_list = array();
        foreach ($list as $item) {
            $order_id = $item->order_id;
            $order_list[$order_id][] = $item;
        }
        return $order_list;
    }

    public function update($data, $id) {
        $this->db->update('purchase_consign', $data, array('id' => $id));
    }

    public function insert($data) {
        $this->db->insert('purchase_consign', $data);
        return $this->db->insert_id();
    }

    public function filter($filter) {
        $query = $this->db->get_where('purchase_consign', $filter, 1);
        return $query->row();
    }

    public function insert_consign_detail($data) {
        $this->db->insert('purchase_consign_detail', $data);
        return $this->db->insert_id();
    }

    public function get_consign_detail($purchase_code) {
        $sql = " SELECT a.*,p.provider_name,p.provider_code,b.brand_name,c.batch_code,i.order_sn," .
                " d.product_sn,d.product_name,pc.color_sn,pc.color_name,ps.size_sn,ps.size_name," .
                " ad.admin_name as create_name,m.purchase_id,op.consign_mark" .
                " FROM " . $this->db->dbprefix('purchase_consign_detail') . " a" .
                " LEFT JOIN " . $this->db->dbprefix('purchase_main') . " m ON m.purchase_code = a.purchase_code" .
                " LEFT JOIN " . $this->db->dbprefix('product_provider') . " p ON a.provider_id = p.provider_id" .
                " LEFT JOIN " . $this->db->dbprefix('product_brand') . " b ON b.brand_id = a.brand_id" .
                " LEFT JOIN " . $this->db->dbprefix('purchase_batch') . " c ON c.batch_id = a.batch_id" .
                " LEFT JOIN " . $this->db->dbprefix('order_info') . " i ON i.order_id = a.order_id" .
                " LEFT JOIN " . $this->db->dbprefix('order_product') . " op ON op.op_id = a.op_id" .
                " LEFT JOIN " . $this->db->dbprefix('product_info') . " d ON d.product_id = a.product_id" .
                " LEFT JOIN " . $this->db->dbprefix('product_color') . " pc ON pc.color_id =a.color_id" .
                " LEFT JOIN " . $this->db->dbprefix('product_size') . " ps ON ps.size_id=a.size_id" .
                " LEFT JOIN " . $this->db->dbprefix('admin_info') . " ad ON ad.admin_id = a.create_admin" .
                " WHERE a.purchase_code =?";
        $query = $this->db->query($sql, array("purchase_code" => $purchase_code));
        return $query->result();
    }

    /**
     * 查询出当前系统中存在虚库且还没有下采购单的订单商品数量
     * 从SQL判断方式修改为 程序判断方式
     */
    public function query_consign_order_count($filter) {
        $query_batch ="SELECT p.provider_id,p.provider_name,p.provider_code,b.batch_id,b.batch_code,pb.brand_id,pb.brand_name".
                " FROM ty_product_provider p ".
                " INNER JOIN ty_purchase_batch b ON p.provider_id = b.provider_id ".
                " INNER JOIN ty_product_brand pb ON pb.brand_id = b.brand_id ".
                " WHERE b.batch_type = 0 AND b.is_reckoned = 0 AND b.is_consign = 1";
        $query_end_time = "SELECT IFNULL(MAX(end_time),'2013-04-20 00:00:00') AS end_time FROM " . $this->db->dbprefix('purchase_consign') . " WHERE provider_id = ? AND batch_id = ? AND brand_id = ?";
        $query_order = "SELECT SUM(o.consign_num) AS num".
                " FROM ty_order_product o ".
                " INNER JOIN ty_product_info i ON o.product_id=i.product_id ".
                " INNER JOIN ty_order_info oi ON oi.order_id = o.order_id AND oi.order_status=1 AND oi.is_ok = 0 ".
                " INNER JOIN ty_product_cost c ON c.product_id = o.product_id ".
                " WHERE o.consign_num > 0 AND oi.confirm_date > ? AND oi.confirm_date <= ? ".
                " AND i.provider_id = ? AND c.batch_id = ? AND i.brand_id = ?";
        $batch_query = $this->db->query($query_batch);
        $provider_batch_list = $batch_query->result();
        $batch_query->free_result();
        if(empty($provider_batch_list)){
            return array();
        }
        foreach ($provider_batch_list as $key=>$provider_batch){
            $query = $this->db->query($query_end_time, array($provider_batch->provider_id, $provider_batch->batch_id,$provider_batch->brand_id));
            $provider_batch->end_time = $query->row()->end_time;
            
            $order_filter = array();
            $order_filter[] = $provider_batch->end_time;
            $order_filter[] = $filter['end_time'];
            $order_filter[] = $provider_batch->provider_id;
            $order_filter[] = $provider_batch->batch_id;
            $order_filter[] = $provider_batch->brand_id;
            $order_query = $this->db->query($query_order,$order_filter);
            $provider_batch->num = $order_query->row()->num;
            if(empty($provider_batch->num)){
                unset($provider_batch_list[$key]);
            }
        }
        return $provider_batch_list;
    }
    
    public function delete_purchase_consign ($where_arr)
    {
	    $this->db->delete('purchase_consign', $where_arr);
	    return $this->db->affected_rows();
    }

    public function delete_purchase_consign_detail ($where_arr)
    {
	    $this->db->delete('purchase_consign_detail', $where_arr);
	    return $this->db->affected_rows();
    }

}

###
