<?php

class Memcache_key_model extends CI_Model {

    public function list_f($filter) {
        $from = " FROM ty_front_memcache_data";
        $where = " WHERE 1 ";

        $param = generate_where_by_filter( $filter, USE_SQL_OR );
        if( !empty($param) ) $where .= "AND ".array_pop( $param );

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

        $join = " LEFT JOIN ty_admin_info AS a ON ty_front_memcache_data.update_aid=a.admin_id";

        $sql = "SELECT ty_front_memcache_data.* , a.admin_name "
                . $from . $join . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
                . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
        $query = $this->db_r->query($sql, $param);
        $list = $query->result();
        $query->free_result();
        return array('list' => $list, 'filter' => $filter);
    }

    public function filter($filter) {
        $query = $this->db_r->get_where('ty_front_memcache_data', $filter, 1);
        return $query->row();
    }

    public function del($data) {
        $this->db->delete('ty_front_memcache_data', $data);
    }

    public function record($data) {
        $key = $data['key'];
        $sql = "SELECT id,content FROM ty_front_memcache_data WHERE `key` ='". $key ."'";
        $query = $this->db->query($sql);
        $list = $query->row_array();
        return $list;
    }

    public function insert($data) {
        $this->db->insert('ty_front_memcache_data', $data);
        return $this->db->insert_id();
    }

    public function update($data) {
        $key = $data['key'];
        $this->db->update('ty_front_memcache_data', $data, array('key' => $key));
    }

}

?>