<?php
class Hotword_model extends CI_Model
{
        public function list_f ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('front_hot_word') ;
		$where = " WHERE 1 ";
		$param = array();

                if(!empty($filter['hotword_name'])){
                    $where .= " AND hotword_name like ? ";
                    $param[] = '%'.$filter['hotword_name'].'%';
                }

                $filter['sort_by'] = empty($filter['sort_by']) ? 'hotword_id' : trim($filter['sort_by']);
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
		$this->db->insert('front_hot_word', $data);
		return $this->db->insert_id();
	}

        public function filter ($filter)
	{
		$query = $this->db->get_where('front_hot_word', $filter, 1);
		return $query->row();
	}
        
	public function update ($data, $model_id)
	{
		$this->db->update('front_hot_word', $data, array('hotword_id' => $model_id));
	}
        public function delete ($data)
	{
        	$this->db->delete('front_hot_word', $data);
	}
    
    public function all($filter=array())
    {
    	$this->db->order_by('sort_order','desc');
    	$query = $this->db->get_where('front_hot_word',$filter);
    	return $query->result();
    }
}
?>
