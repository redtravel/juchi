<?php

#doc
#	classname:	Article_model
#	scope:		PUBLIC
#
#/doc

class Article_model extends CI_Model
{
	private $_db;
	function __construct (&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}
	
	public function filter ($filter)
	{
		$query = $this->_db->get_where('article_info',$filter,1);
		return $query->row();
	}
	
	public function all_cat ($filter)
	{
		$this->_db->order_by('sort_order','DESC');
		$query = $this->_db->get_where('article_cat',$filter);
		return $query->result();
	}
	
	public function filter_cat($filter){
		$query = $this->_db->get_where('article_cat',$filter,1);
		return $query->row();
	}
	
	public function all_article ($filter)
	{
		$this->_db->select("article_id,cat_id,title,url,title_size,title_color");
		$this->_db->order_by('sort_order desc, article_id desc');
		if ( isset($filter['cat_id']) && is_array($filter['cat_id']) )
		{
			$this->_db->where_in('cat_id',$filter['cat_id']);
			unset($filter['cat_id']);
		}
		if(isset($filter['limit'])){
			$this->_db->limit($filter['limit']);
			unset($filter['limit']);
		}
		$query = $this->_db->get_where('article_info',$filter);
		return $query->result();
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
			return false;
		}
		$sql = "SELECT a.*,i.cat_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return $list;
	}

}
###
