<?php
#doc
#	classname:	pament_model
#	scope:		PUBLIC
#
#/doc

class Order_source_model extends CI_Model
{

	public function filter($filter)
	{
            $query = $this->db->get_where('order_source', $filter, 1);
            return $query->row();
	}
        
        public function all_source($filter=array()){
            $query = $this->db->get_where('order_source',$filter);
            return $query->result();
        }
        
	public function insert ($data)
	{
            $this->db->insert('order_source', $data);
            return $this->db->insert_id();
	}

	public function update ($data, $source_id)
	{
            $this->db->update('order_source', $data, array('source_id' => $source_id));
	}

	public function delete ($source_id)
	{
            $this->db->delete('order_source', array('source_id' => $source_id));
	}


}
###