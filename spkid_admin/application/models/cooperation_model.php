<?php
#doc
#	classname:	cooperation_model
#	scope:		PUBLIC
#
#/doc

class Cooperation_model extends CI_Model
{

	public function filter($filter)
	{
		$query = $this->db->get_where('product_cooperation', $filter, 1);
		return $query->row();
	}
	public function cooperation_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_cooperation')." AS c ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['cooperation_name']))
		{
			$where .= " AND a.cooperation_name LIKE ? ";
			$param[] = '%' . $filter['cooperation_name'] . '%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'c.cooperation_id' : trim($filter['sort_by']);
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
		$sql = "SELECT c.* "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function insert ($data)
	{
		$this->db->insert('product_cooperation', $data);
		return $this->db->insert_id();
	}
	
	public function update ($data, $cooperation_id)
	{
		$this->db->update('product_cooperation', $data, array('cooperation_id' => $cooperation_id));
	}
	
	public function delete ($cooperation_id)
	{
		$this->db->delete('product_cooperation', array('cooperation_id' => $cooperation_id));
	}

	public function all_cooperation($filter=array())
	{
		$query = $this->db->get_where('product_cooperation', $filter);
		return $query->result();
	}
}
###