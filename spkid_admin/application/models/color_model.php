<?php
#doc
#	classname:	Color_model
#	scope:		PUBLIC
#
#/doc

class Color_model extends CI_Model
{

	public function filter ($filter)
	{
		$query = $this->db->get_where('product_color', $filter, 1);
		return $query->row();
	}

	public function update ($data, $color_id)
	{
		$this->db->update('product_color', $data, array('color_id' => $color_id));
	}

	public function insert ($data)
	{
		$this->db->insert('product_color', $data);
		return $this->db->insert_id();
	}

	public function delete($color_id)
	{
		$this->db->delete('product_color', array('color_id'=>$color_id));
	}

	public function color_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_color')." AS c 
				LEFT JOIN ".$this->db->dbprefix('product_color_group')." AS g ON c.group_id = g.group_id ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['color_name']))
		{
			$where .= " AND c.color_name LIKE ? ";
			$param[] = '%' . $filter['color_name'] . '%';
		}

		if (!empty($filter['group_id']))
		{
			$where .= " AND g.group_id = ? ";
			$param[] = $filter['group_id'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'c.color_id' : trim($filter['sort_by']);
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
		$sql = "SELECT c.*, g.group_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function all_color ($filter = array())
	{
		$query = $this->db->get_where('product_color',$filter);
		return $query->result();
	}

	public function filter_group($filter)
	{
		$query = $this->db->get_where('product_color_group', $filter, 1);
		return $query->row();
	}

	public function group_list($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_color_group')." AS g ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['group_name']))
		{
			$where .= " AND g.group_name LIKE ? ";
			$param[] = '%' . $filter['group_name'] . '%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'g.group_id' : trim($filter['sort_by']);
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
		$sql = "SELECT g.* "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		// 附加颜色数据
		$list = index_array($list,'group_id');
		$group_ids = array_keys($list);
		$sql = "SELECT color_id, color_name,group_id FROM ".$this->db->dbprefix('product_color')." WHERE group_id ".db_create_in($group_ids)." ORDER BY sort_order desc";
		$query = $this->db->query($sql);
		foreach ($query->result() as $key => $value) {
			if (!isset($list[$value->group_id]->color_list)) {
				$list[$value->group_id]->color_list = array();
			}
			$list[$value->group_id]->color_list[] = $value;
		}
		return array('list' => $list, 'filter' => $filter);
	}

	public function update_group ($data, $group_id)
	{
		$this->db->update('product_color_group', $data, array('group_id' => $group_id));
	}

	public function insert_group ($data)
	{
		$this->db->insert('product_color_group', $data);
		return $this->db->insert_id();
	}

	public function delete_group($group_id)
	{
		$this->db->delete('product_color_group',array('group_id'=>$group_id));
	}

	public function all_group($filter=array())
	{
		$query = $this->db->get_where('product_color_group');
		return $query->result();
	}
	
	/*
	 * 获取颜色编码，最多4位
	 * 颜色组最多99个，对应的子颜色个数最多99个
	 */
	public function gen_color_sn($color_group){
	    $sql =" SELECT color_sn FROM ty_product_color WHERE group_id = $color_group ORDER BY color_id DESC LIMIT 1";
	    $query = $this->db->query($sql);
	    $last_sn = $query->row();
	    if(empty($last_sn)){
		return str_pad($color_group,2,"0",STR_PAD_LEFT) . "01";
	    }else{
		$group = substr($last_sn->color_sn, 0,2);
		$subfix = substr($last_sn->color_sn, 2);
		$now_fix = intval($subfix)+1;
		if($now_fix >= 100){
		    sys_msg("颜色数量超出系统边界，请联系管理员",1);
		}
		return $group .str_pad($now_fix,2,"0",STR_PAD_LEFT);
	    }
	}
}
###