<?php

class Generate_code_model extends CI_Model {

    public function desc($table_name) {
        if(  $this->db->table_exists( $table_name )){
            return $this->db->field_data( $table_name );
        }else return false;
    }
    public function table_exists( $table_name ){
        return $this->db->table_exists( $table_name );
    }
    public function get_menu( $parent_id = 0){
        $sql = "select * from ty_admin_action where menu_name != '' and menu_name is not null";
        if( !is_null($parent_id) )
            $sql .= " and parent_id=".$parent_id;
        $sql .= " order by sort_order ASC";
        $result = $this->db->query( $sql )->result();
        return $result;
    }

}

?>
