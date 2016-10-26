<?php
#doc
#	classname:	pament_model
#	scope:		PUBLIC
#
#/doc

class Order_advice_type_model extends CI_Model
{

	public function filter($filter)
	{
            $query = $this->db->get_where('ty_order_advice_type', $filter, 1);
            return $query->row();
	}
        
        public function all($filter=array()){
            $query = $this->db->get_where('order_advice_type',$filter);
            return $query->result();
        }
        
	public function insert ($data)
	{
            $this->db->insert('ty_order_advice_type', $data);
            return $this->db->insert_id();
	}

	public function update ($data, $type_id)
	{
            $this->db->update('ty_order_advice_type', $data, array('type_id' => $type_id));
	}

	public function delete ($type_id)
	{
            $this->db->delete('ty_order_advice_type', array('type_id' => $type_id));
	}


}
###