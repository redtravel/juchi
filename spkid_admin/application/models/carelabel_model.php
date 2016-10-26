<?php
#doc
#	classname:	Carelabel_model
#	scope:		PUBLIC
#
#/doc

class Carelabel_model extends CI_Model
{

	public function filter ($filter)
	{
		$query = $this->db->get_where('product_carelabel', $filter, 1);
		return $query->row();
	}

	public function update ($data, $carelabel_id)
	{
		$this->db->update('product_carelabel', $data, array('carelabel_id' => $carelabel_id));
	}

	public function insert ($data)
	{
		$this->db->insert('product_carelabel', $data);
		return $this->db->insert_id();
	}

	public function delete($carelabel_id)
	{
		$this->db->delete('product_carelabel',array('carelabel_id'=>$carelabel_id));
	}

	public function carelabel_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_carelabel')." AS c ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['carelabel_name']))
		{
			$where .= " AND c.carelabel_name LIKE ? ";
			$param[] = '%' . $filter['carelabel_name'] . '%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'c.carelabel_id' : trim($filter['sort_by']);
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
	
	public function all_carelabel ($filter=array())
	{
		$query = $this->db->get_where('product_carelabel',$filter);
		return $query->result();
	}

	public function filter_refer($carelabel_id)
	{
		$sql = "SELECT 1 FROM ".$this->db->dbprefix('product_info')." WHERE CONCAT(',',goods_carelabel,',') LIKE ?";
		$query = $this->db->query($sql, array('%,'.$carelabel_id.',%'));
		return $query->row();
	}
}
###