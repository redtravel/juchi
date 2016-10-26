<?php

/**
 *Product_type_mode
 */

class Product_type_model extends CI_Model {
	private $_db;

	public function __construct(&$db = NULL) {
		parent::__construct();
		$this->_db = $db ? $db : $this->_db;
	}

	public function filter($filter = array()) {
		//$sql = "SELECT `type_id`, `type_code`, `type_name`, `parent_id`, `parent_id2`, `is_show_cat`, `sort_order` FROM ty_product_type";
		//$query = $this->_db->query($sql);
		$query = $this->db_r->get_where('product_type', $filter, 1);
		return $query->row();
	}

	//获取一级分类
	public function get_first_classes() {
		//$sql = 'select `type_id`, `type_name` from ty_product_type where parent_id = 0 and parent_id2 = 0 and is_show_cat = 1';
		$sql = 'select `type_id`, `type_name` from ty_product_type where is_show_cat = 1';

		$query = $this->db_r->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function product_type_list() {
		$result = array();
		$nav_list = array();
		$filter = array(
			'genre_id' => 1,
			'is_show_cat' => 1,
		);
		// get all type list if $filter condition not null
		$query = $this->db_r->select('type_id, type_name, parent_id, parent_id2')->order_by('parent_id2 asc')->get_where('product_type', $filter);
		//$query = $this->db_r->get_where('product_type', $filter);

		$result = $query->result_array();
		$query->free_result();

		if (!empty($result)) {
			// get top type list
			foreach ($result as $key => $value) {
				if ($value['parent_id'] == 0) {
					$nav_list[$value['type_id']] = $value;
				}
			}

			// get the second type list
			foreach ($nav_list as $key => $value) {
				foreach ($result as $key1 => $value1) {
					if ($value1['parent_id'] == $key && $value1['parent_id2'] == 0) {
						$nav_list[$key]['list'][] = $value1;
					}

				}
			}

			// get the third type list
			foreach ($nav_list as $key => &$value) {
				if ($value['list']) {
					foreach ($value['list'] as $key1 => &$value1) {
						foreach ($result as $key2 => $value2) {
							if ($value2['parent_id2'] != 0 && $value2['parent_id2'] == $value1['type_id']) {
								$value1['list'][] = $value2;
							}
						}
					}
				}
			}
		}
		//释放变量
		unset($result);

		return $nav_list;
	}
}

?>
