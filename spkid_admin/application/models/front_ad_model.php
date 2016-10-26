<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class front_ad_model extends CI_Model
{
        public function filter ($filter)
	{
		$query = $this->db->get_where('ty_front_ad', $filter, 1);
		return $query->row();
	}

	public function update ($data, $ad_id)
	{
		$this->db->update('ty_front_ad', $data, array('ad_id' => $ad_id));
	}

	public function insert ($data)
	{
		$this->db->insert('ty_front_ad', $data);
		return $this->db->insert_id();
	}

        public function ad_list ($filter)
	{
                $param = array();
		$from = " FROM ty_front_ad AS f
                    LEFT JOIN ty_front_ad_position AS p ON f.position_id = p.position_id
                    ";
		$where = " WHERE f.position_id = ? ";
		$param[] = $filter['position_id'];
                if(!empty($filter['ad_name'])){
                    $where .= " AND f.ad_name like ? ";
                    $param[] = '%'.$filter['ad_name'].'%';
                }
                if(!empty($filter['start_date'])){
                    $where .= " AND f.start_date > ? ";
                    $param[] = $filter['start_date'];
                }
                if(!empty($filter['end_date'])){
                    $where .= " AND f.end_date < ? ";
                    $param[] = $filter['end_date'];
                }
                if(!empty($filter['is_use'])){
                    $where .= " AND f.is_use = ? ";
                    $param[] = $filter['is_use'] - 1;
                }

		$filter['sort_by'] = empty($filter['sort_by']) ? 'f.ad_id' : trim($filter['sort_by']);
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
		$sql = "SELECT f.*,p.position_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
        
	public function delete ($ad_id)
	{
		$this->db->delete('ty_front_ad', array('ad_id' => $ad_id));
	}
	
         public function ad_index ($filter)
	{
                $param = array();
		$from = " FROM ty_front_ad AS f
                    LEFT JOIN ty_front_ad_position AS p ON f.position_id = p.position_id
                    ";
		$where = " WHERE 1 ";
                if(!empty($filter['position_id'])){
                    $where .= " AND f.position_id = ? ";
                    $param[] = $filter['position_id'];
                }
                if(!empty($filter['ad_name'])){
                    $where .= " AND f.ad_name like ? ";
                    $param[] = '%'.$filter['ad_name'].'%';
                }
                if(!empty($filter['start_date'])){
                    $where .= " AND f.start_date > ? ";
                    $param[] = $filter['start_date'];
                }
                if(!empty($filter['end_date'])){
                    $where .= " AND f.end_date < ? ";
                    $param[] = $filter['end_date'];
                }
                if(!empty($filter['is_use'])){
                    $where .= " AND f.is_use = ? ";
                    $param[] = $filter['is_use'] - 1;
                }

		$filter['sort_by'] = empty($filter['sort_by']) ? 'f.ad_id' : trim($filter['sort_by']);
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
		$sql = "SELECT f.*,p.position_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

}




?>
