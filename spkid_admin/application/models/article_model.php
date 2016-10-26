<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class article_model extends CI_Model
{
        public function cat_filter ($filter)
	{
		$query = $this->db->get_where('ty_article_cat', $filter, 1);
		return $query->row();
	}

	public function cat_update ($data, $model_id)
	{
		$this->db->update('ty_article_cat', $data, array('cat_id' => $model_id));
	}

	public function cat_insert ($data)
	{
		$this->db->insert('ty_article_cat', $data);
		return $this->db->insert_id();
	}

	public function all_cat($filter=array())
	{
		$this->db->order_by('sort_order', 'desc');
		$query = $this->db->get_where('ty_article_cat', $filter);
		return $query->result();
	}
        public function cat_del ($data)
	{
        	$this->db->delete('ty_article_cat', $data);
	}

        public function article_list ($filter)
	{
		$from = " FROM ty_article_info AS a
                    LEFT JOIN ty_article_cat AS i ON a.cat_id = i.cat_id
                    ";
		$where = " WHERE 1 ";
		$param = array();

                if(!empty($filter['cat_id'])){
                    $where .= " AND a.cat_id = ? ";
                    $param[] = $filter['cat_id'];
                }

                if(!empty($filter['author'])){
                    $where .= " AND a.author like  ? ";
                    $param[] = '%'.$filter['author'].'%';
                }
                
                if(!empty($filter['is_use'])){
                    if($filter['is_use'] == 1){
                        $where .= " AND a.is_use =  1 ";
                    }
                    if($filter['is_use'] == 2){
                        $where .= " AND a.is_use =  0 ";
                    }
                }
                
                if(!empty($filter['start_time'])){
                    $where .= " AND a.create_date >= ? ";
                    $param[] = $filter['start_time'];
                }
                if(!empty($filter['end_time'])){
                    $where .= " AND a.create_date <= ? ";
                    $param[] = $filter['end_time'];
                }

		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.article_id' : trim($filter['sort_by']);
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
		$sql = "SELECT a.*,i.cat_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function article_insert ($data)
	{
		$this->db->insert('ty_article_info', $data);
		return $this->db->insert_id();
	}

        public function article_filter ($filter)
	{
		$query = $this->db->get_where('ty_article_info', $filter, 1);
		return $query->row();
	}
        
	public function article_update ($data, $model_id)
	{
		$this->db->update('ty_article_info', $data, array('article_id' => $model_id));
	}
        public function article_del ($data)
	{
        	$this->db->delete('ty_article_info', $data);
	}

}




?>
