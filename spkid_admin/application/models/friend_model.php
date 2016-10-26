<?php
class Friend_model extends CI_Model
{
        public function list_f ($filter)
	{
		$from = " FROM ty_friend_link  ";
		$where = " WHERE 1 ";
		$param = array();

                if(!empty($filter['link_name'])){
                    $where .= " AND link_name like ? ";
                    $param[] = '%'.$filter['link_name'].'%';
                }
                if(!empty($filter['link_url'])){
                    $where .= " AND link_url like ? ";
                    $param[] = '%'.$filter['link_url'].'%';
                }

		$filter['sort_by'] = empty($filter['sort_by']) ? 'link_id' : trim($filter['sort_by']);
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
		$sql = "SELECT * "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function insert ($data)
	{
		$this->db->insert('ty_friend_link', $data);
		return $this->db->insert_id();
	}

        public function filter ($filter)
	{
		$query = $this->db->get_where('ty_friend_link', $filter, 1);
		return $query->row();
	}
        
	public function update ($data, $model_id)
	{
		$this->db->update('ty_friend_link', $data, array('link_id' => $model_id));
	}
        public function del ($data)
	{
        	$this->db->delete('ty_friend_link', $data);
	}
    
}
?>
