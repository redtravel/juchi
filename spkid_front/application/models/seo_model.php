<?php

class Seo_model extends CI_Model {
	private $_db;

	function __construct($db = NULL) {
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	public function get_page_seo($pagetag = '') {
		$sql = 'SELECT title, keywords, description from ty_front_page_seo where `code` = "' . $pagetag . '"';
		$query = $this->db_r->query($sql);
		$result = $query->result_array();
		$query->free_result();
		$result = isset($result[0]) ? $result[0] : array('keywords' => '', 'description' => '', 'title' => '');
		return $result;
	}
}

?>