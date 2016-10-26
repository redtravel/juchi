<?php
#doc
#	classname:	Product_model
#	scope:		PUBLIC
#
#/doc

class useraddress_model extends CI_Model
{

	public function filter($filter)
	{
		$query = $this->db->get_where('user_address', $filter, 1);
		return $query->row();
	}
        
        public function filter_array($filter)
	{
		$query = $this->db->get_where('user_address', $filter, 1);
		return $query->result_array();
	}
        
        public function filter_r($filter)
	{
		$query = $this->db->get_where('user_address', $filter);
		return $query->result_array();
	}

        public function all_address($filter)
	{
            $this->db->order_by("address_id", "desc"); 
            $query = $this->db->get_where('user_address', $filter);
            return $query->result();
	}
        
	public function userrank_list ()
	{
		$sql = "SELECT * FROM ty_user_address";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		return $list;
	}

	public function insert ($data)
	{
		$this->db->insert('user_address', $data);
		return $this->db->insert_id();
	}

	public function update ($data, $address_id)
	{
		$this->db->update('ty_user_address', $data, array('address_id' => $address_id));
	}
        
        public function update_condition ($data, $user_id)
	{
		$this->db->update('ty_user_address', $data, array('user_id' => $user_id));
	}

	public function delete ($address_id)
	{
		$this->db->delete('ty_user_address', array('address_id' => $address_id));
	}

}
###