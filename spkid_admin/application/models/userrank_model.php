<?php
#doc
#	classname:	Product_model
#	scope:		PUBLIC
#
#/doc

class userrank_model extends CI_Model
{

	public function filter($filter)
	{
		$query = $this->db->get_where('ty_user_rank', $filter, 1);
		return $query->row();
	}
	public function userrank_list ()
	{
		$sql = "SELECT * FROM ty_user_rank order by rank_id desc";
		$query = $this->db->query($sql);
		$list = $query->result();
		$query->free_result();
		return $list;
	}
	
	public function insert ($data)
	{
		$this->db->insert('ty_user_rank', $data);
		return $this->db->insert_id();
	}
	
	public function update ($data, $rank_id)
	{
		$this->db->update('ty_user_rank', $data, array('rank_id' => $rank_id));
	}
	
	public function delete ($rank_id)
	{
		$this->db->delete('ty_user_rank', array('rank_id' => $rank_id));
	}

}
###