<?php

class Order_client_model extends CI_Model {

    public function list_f($filter) {
        $from = " FROM order_client_info";
        $where = " WHERE 1 ";

        $param = generate_where_by_filter( $filter, USE_SQL_OR );
        if( !empty($param) ) $where .= "AND ".array_pop( $param );

        $filter['sort_by'] = empty($filter['sort_by']) ? 'client_id' : trim($filter['sort_by']);
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
        $this->db->insert('order_client_info', $data);
        return $this->db->insert_id();
    }

    public function filter($filter) {
        $query = $this->db_r->get_where('order_client_info', $filter, 1);
        return $query->row();
    }

    public function update($data, $model_id) {
        $this->db->update('order_client_info', $data, array('client_id' => $model_id));
    }

    public function del($data) {
        $this->db->delete('order_client_info', $data);
    }

}

?>