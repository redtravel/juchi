<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class user_account_log_kind_model extends CI_Model
{
        public function filter ($filter)
	{
		$query = $this->db->get_where('ty_user_account_log_kind', $filter, 1);
		return $query->row();
	}

	public function update ($data, $change_code)
	{
		$this->db->update('ty_user_account_log_kind', $data, array('change_code' => $change_code));
	}

	public function insert ($data)
	{
		$this->db->insert('ty_user_account_log_kind', $data);
		return $this->db->insert_id();
	}

	public function all_kind($filter=array())
	{
		$query = $this->db->get_where('ty_user_account_log_kind', $filter);
		return $query->result();
	}
        
        public function delete ($data)
	{
        	$this->db->delete('ty_user_account_log_kind', $data);
	}

        
}
?>
