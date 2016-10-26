<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Location_Model extends CI_Model {
    
    public function get_location($filter) {
        $query = $this->db->get_where('location_info', $filter, 1);
        $row = $query->row();
        $query->free_result();
        
        return $row;
    }
    
    public function query_locations($filter) {
        $query = $this->db->get_where('location_info', $filter, 1);
        $list = $query->result();
        $query->free_result();
        
        return $list;
    }

    public function batch_get_locations($location_ids_str) {
        $sql = " SELECT * FROM ".$this->db->dbprefix('location_info')
              ." WHERE location_id in (".$location_ids_str.")";
        
        $query = $this->db->query($sql);
        $list = $query->result();
        $query->free_result();
        
        return $list;
    }
    
}

?>
