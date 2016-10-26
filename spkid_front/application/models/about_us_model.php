<?php
/**
* About_us
*/
class About_us_model extends CI_Model
{	
	private $_db;
	public function __construct (&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	//合作咨询
	public function team_work_insert ($param){ 
		$this->_db->insert('user_teamwork', $param);
		return $this->_db->insert_id();
	}

	//意见反馈
	public function feedback_insert ($param){ 
		$this->_db->insert('product_liuyan', $param);
		return $this->_db->insert_id();
	}
}