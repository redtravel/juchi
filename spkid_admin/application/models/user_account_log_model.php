<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class user_account_log_model extends CI_Model
{
        public function filter ($filter)
	{
		$query = $this->db->get_where('user_account_log', $filter, 1);
		return $query->row();
	}

	public function update ($data, $log_id)
	{
		$this->db->update('user_account_log', $data, array('log_id' => $log_id));
	}

	public function insert ($data)
	{
		$this->db->insert('user_account_log', $data);
		return $this->db->insert_id();
	}

        public function del ($data)
	{
        	$this->db->delete('user_account_log', $data);
	}


        public function log_list ($filter)
	{
                $param = array();
		$from = " FROM ".$this->db->dbprefix('user_account_log')." AS l
                    LEFT JOIN ".$this->db->dbprefix('user_info')." AS u ON l.user_id = u.user_id
                    LEFT JOIN ".$this->db->dbprefix('user_account_log_kind')." AS k ON l.change_code = k.change_code
                    ";
		$where = " WHERE l.user_id = ?";
                $param[] = $filter['user_id'];
                if(!empty($filter['change_code'])){
                    $where .= " AND l.change_code = ? ";
                    $param[] = $filter['change_code'];
                }
                
                if(!empty($filter['start_time'])){
                    $where .= " AND l.create_date >= ? ";
                    $param[] = $filter['start_time'];
                }
                if(!empty($filter['end_time'])){
                    $where .= " AND l.create_date <= ? ";
                    $param[] = $filter['end_time'];
                }
		$filter['sort_by'] = empty($filter['sort_by']) ? 'l.log_id' : trim($filter['sort_by']);
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
		$sql = "SELECT l.*,u.user_name,k.change_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	// @todo:delete
  	public function distinct_log_kind(){
        $sql = "SELECT DISTINCT change_code FROM ty_user_account_log";
        $query = $this->db->query($sql);
        $arr = $query->result();
        $res = array();
        foreach($arr as $item){
            $res[] = $item->change_code;
        }
        return $res;
    }

}




?>
