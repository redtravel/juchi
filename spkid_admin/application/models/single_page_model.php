<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class single_page_model extends CI_Model
{
        public function filter ($filter)
	{
		$query = $this->db->get_where('front_single_page', $filter, 1);
		return $query->row();
	}

	public function update ($data, $single_id)
	{
		$this->db->update('front_single_page', $data, array('single_id' => $single_id));
	}

	public function insert ($data)
	{
		$this->db->insert('front_single_page', $data);
		return $this->db->insert_id();
	}

        public function sg_list ($filter)
	{
                $param = array();
		$from = " FROM ".$this->db->dbprefix('front_single_page') ;
		$where = " WHERE 1 ";

                if(!empty($filter['is_use'])){
                    $where .= " AND is_use = ? ";
                    $param[] = $filter['is_use'] - 1;
                }

		$filter['sort_by'] = empty($filter['sort_by']) ? 'single_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'desc' : trim($filter['sort_order']);

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
		$sql = "SELECT * "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
        
	public function delete ($single_id)
	{
		$this->db->delete('front_single_page', array('single_id' => $single_id));
	}
	

}




?>
