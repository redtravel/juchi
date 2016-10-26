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

}
###