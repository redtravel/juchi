<?php
#doc
#	classname:	Model_model
#	scope:		PUBLIC
#
#/doc

class Model_model extends CI_Model
{

	public function filter ($filter)
	{
		$query = $this->db->get_where('product_model', $filter, 1);
		return $query->row();
	}

	public function update ($data, $model_id)
	{
		$this->db->update('product_model', $data, array('model_id' => $model_id));
	}

	public function insert ($data)
	{
		$this->db->insert('product_model', $data);
		return $this->db->insert_id();
	}

	public function delete($model_id)
	{
		$this->db->delete('product_model', array('model_id'=>$model_id));
	}

	public function model_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_model')." AS m ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['model_name']))
		{
			$where .= " AND m.model_name LIKE ? ";
			$param[] = '%' . $filter['model_name'] . '%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'm.model_id' : trim($filter['sort_by']);
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
		$sql = "SELECT m.* "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function all_model ($filter = array())
	{
		$query = $this->db->get_where('product_model', $filter);
		return $query->result();
	}
}
###