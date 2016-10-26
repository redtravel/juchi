<?php

class Order_refund_model extends CI_Model {

    public function list_f($filter) {
        $from = " FROM ty_order_refund";
        $where = " WHERE 1 ";
        if (!empty($filter['create_date_start'])) $where .= " AND create_date >= '".$filter['create_date_start']."'";
        if (!empty($filter['create_date_end'])) $where .= " AND create_date <= '".$filter['create_date_end']." 23:59:59'";
        if (!empty($filter['finance_date_start'])) $where .= " AND finance_date >= '".$filter['finance_date_start']."'";
        if (!empty($filter['finance_date_end'])) $where .= " AND finance_date <= '".$filter['finance_date_end']." 23:59:59'";
        if (!empty($filter['create_name'])) $where .= " AND create_name LIKE '%".$filter['create_name']."%'";
        if (!empty($filter['finance_name'])) $where .= " AND finance_name LIKE '%".$filter['finance_name']."%'";
        if (!empty($filter['finance_status'])) $where .= ($filter['finance_status'] == 2) ? " AND finance_admin > 0" : " AND finance_admin = 0";

        $param = generate_where_by_filter( $filter);
        if( !empty($param) ) $where .= " AND ".array_pop( $param );

        $filter['sort_by'] = empty($filter['sort_by']) ? 'id' : trim($filter['sort_by']);
        $filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

        $sql = "SELECT COUNT(*) AS ct " . $from . $where;
        $query = $this->db_r->query($sql, $param);
        $row = $query->row();
        $query->free_result();
        $filter['record_count'] = (int) $row->ct;
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0) {
            return array('list' => array(), 'filter' => $filter);
        }
        $sql = "SELECT * "
                . $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
                . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
        $query = $this->db_r->query($sql, $param);
        $list = $query->result();
        $query->free_result();
        return array('list' => $list, 'filter' => $filter);
    }

    public function insert($data) {
        $this->db->insert('order_refund', $data);
        return $this->db->insert_id();
    }

    public function filter($filter) {
        $query = $this->db_r->get_where('order_refund', $filter, 1);
        return $query->row();
    }

    public function update($data, $model_id) {
        $this->db->update('order_refund', $data, array('id' => $model_id));
    }

    public function del($data) {
        $this->db->delete('order_refund', $data);
    }

}

?>