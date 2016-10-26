<?php
#doc
#	classname:	pament_model
#	scope:		PUBLIC
#
#/doc

class payment_model extends CI_Model
{

	public function filter($filter)
	{
            $query = $this->db->get_where('payment_info', $filter, 1);
            return $query->row();
	}
        
    public function all_payment($filter = array()){
        $query = $this->db->get_where('payment_info',$filter);
        return $query->result();
    }
        
	public function insert ($data)
	{
            $this->db->insert('ty_payment_info', $data);
            return $this->db->insert_id();
	}

	public function update ($data, $pay_id)
	{
            $this->db->update('ty_payment_info', $data, array('pay_id' => $pay_id));
	}

	public function delete ($pay_id)
	{
            $this->db->delete('ty_payment_info', array('pay_id' => $pay_id));
	}


}