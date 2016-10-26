<?php
#doc
#	classname:	Flag_model
#	scope:		PUBLIC
#
#/doc

class Flag_model extends CI_Model
{

	public function filter ($filter)
	{
		$query = $this->db->get_where('product_flag', $filter, 1);
		return $query->row();
	}

	public function update ($data, $flag_id)
	{
		$this->db->update('product_flag', $data, array('flag_id' => $flag_id));
	}

	public function insert ($data)
	{
		$this->db->insert('product_flag', $data);
		return $this->db->insert_id();
	}

	public function delete($flag_id)
	{
		$this->db->delete('product_flag', array('flag_id'=>$flag_id));
	}

	public function flag_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_flag')." AS f ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['flag_name']))
		{
			$where .= " AND f.flag_name LIKE ? ";
			$param[] = '%' . $filter['flag_name'] . '%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'f.flag_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'ASC' : trim($filter['sort_order']);

		$sql = "SELECT COUNT(*) AS ct " . $from . $where;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "SELECT f.* "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function all_flag ()
	{
		$query = $this->db->get('product_flag');
		return $query->result();
	}
}
###