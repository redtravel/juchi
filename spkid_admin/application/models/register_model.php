<?php
#doc
#	classname:	Season_model
#	scope:		PUBLIC
#
#/doc

class Register_model extends CI_Model
{

	public function filter($filter)
	{
		// if($filter['id']){
		// 	$sql = "SELECT * FROM `ty_register_code` WHERE `id` = ".$filter['id']." LIMIT 1";
		// }elseif($filter['register_no']){
		// 	$sql = "SELECT * FROM `ty_register_code` WHERE `register_no` = '".$filter['register_no']."' LIMIT 1";
		// }

		$sql = "SELECT * FROM `ty_register_code` WHERE ";

		foreach ( $filter AS $key => $value )
			 $data[$key] = $key . "= '". $value ."'";
		$sql .= implode(' and ', $data);
		$sql .= " LIMIT 1";

		$query = $this->db->query($sql);
		$row = $query->row();
		return $row;
	}

	public function register_list ($filter)
	{
		$from = " FROM ty_register_code AS r ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['register_no']))
		{
			$where .= " AND r.register_no LIKE ? ";
			$param[] = '%' . $filter['register_no'] . '%';
		}
                if (!empty($filter['product_name']))
		{
			$where .= " AND r.product_name LIKE ? ";
			$param[] = '%' . $filter['product_name'] . '%';
		}
                if (!empty($filter['unit']))
		{
			$where .= " AND r.unit LIKE ? ";
			$param[] = '%' . $filter['unit'] . '%';
		}

		$filter['register_id'] = empty($filter['register_id']) ? 'r.id' : trim($filter['register_id']);
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
		$join = " LEFT JOIN ty_admin_info AS a ON r.add_admin_id=a.admin_id";
		$join .= " LEFT JOIN ty_dict_info as di1 on di1.field_id=r.medical1";
		$join .= " LEFT JOIN ty_dict_info as di2 on di2.field_id=r.medical2";

		$where .=  " and di1.dict_id='medical_device_class' and di2.dict_id='medical_device'";

		$sql = "SELECT r.* , a.admin_name, di1.field_value1, di2.field_value2 "
				. $from . $join . $where . " ORDER BY " . $filter['register_id'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

		$query = $this->db->query($sql, $param);
		$list = $query->result();

		$query->free_result();

		return array('list' => $list, 'filter' => $filter);
	}


	public function insert ($data)
	{
		if( empty($data) ) return true;
		// $sql = "INSERT INTO `ty_register_code` (`register_no`, `add_admin_id`, `add_admin_time`,`medical1`,`medical2`) VALUES ('".$data['register_no']."', ".$data['add_admin_id'].", ".$data['add_admin_time'].", ".$data['medical1'].", ".$data['medical2'].")";
		$sql = "INSERT INTO `ty_register_code` ";
		foreach ( $data AS $key => $value )
			 $keys[] = $key;
		$sql .= "(".implode(',', $keys).") ";

		foreach ( $data AS $key => $value )
			$vals[] = "'".$value."'";
		$sql .= "VALUES (".implode(',', $vals).")";

		$query = $this->db->query($sql);
		return $this->db->insert_id();
	}

	public function update ($data, $id)
	{	
		if( empty($data) ) return true;
		$sql = "UPDATE ty_register_code SET ";
		foreach ( $data AS $key => $value )
			 $data[$key] = $key . " = '". $value ."'";
		$sql .= implode(',', $data);
		$sql .= " WHERE id = ".$id;
		$this->db->query($sql);
	}
	
	public function delete ($id)
	{
		$sql = "DELETE FROM ty_register_code WHERE id = ".$id;
		$this->db->query($sql);
	}

	public function medical_list ($dict_id)
	{
		$sql = "SELECT * FROM `ty_dict_info` WHERE dict_id = '".$dict_id."'";
		$query = $this->db->query($sql);
		$list = $query->result();
		return $list;
	}


	public function all_register ()
	{
		$sql = "SELECT * FROM `ty_register_code`";
		$query = $this->db->query($sql);
		return $query->result();
	}

}
###
