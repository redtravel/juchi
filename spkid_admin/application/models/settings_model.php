<?php
#doc
#	classname:	Season_model
#	scope:		PUBLIC
#
#/doc

class Settings_model extends CI_Model
{

	public function filter($filter)
	{
		$sql = "SELECT * FROM `ty_system_settings` WHERE ";

		foreach ( $filter AS $key => $value )
			 $data[$key] = $key . "= '". $value ."'";
		$sql .= implode(' and ', $data);
		$sql .= " LIMIT 1";

		$query = $this->db->query($sql);
		$row = $query->row();
		return $row;
	}

	/**
	*	var $sys_display_types = Array(1=>'输入框',2=>'单选框',3=>'TEXTAREA');
	*	var $sys_store_types = Array(1=>'字符串',2=>'数字',3=>'数组',4=>'布尔');
	**/
	public function settings_list ($filter=array())
	{
		$from = " FROM ty_system_settings AS s ";
		$where = " WHERE 1 ";
		$param = array();
		$join = '';


		$sql = "SELECT s.* "
				. $from . $join . $where . " ORDER BY sort ASC";

		$query = $this->db->query($sql, $param);

		$list = $query->result();

		$query->free_result();

		if( !empty($list) )
			foreach( $list AS $key=>$row ){
				if( $row->type == 2 ) {
					$ary = unserialize($row->comment);
					$list[$key]->options = array();
					foreach( $ary as $i=>$v )
						$list[$key]->options[$v[0]] = $v[1];
					$list[$key]->comment = var_export($ary,true);
				}
				if( $row->storage_type == 3)$list[$key]->config_value = var_export(unserialize($row->config_value),true); // 作为数组显示
			}
		return array('list' => $list, 'filter' => $filter);
	}


	public function insert ($data)
	{
		if( empty($data) ) return true;

		$sql = "INSERT INTO `ty_system_settings` ";
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
		$sql = "UPDATE ty_system_settings SET ";
		foreach ( $data AS $key => $value )
			 $data[$key] = $key . " = '". $value ."'";
		$sql .= implode(',', $data);
		$sql .= " WHERE id = ".$id;

		$this->db->query($sql);
	}

	public function delete ($id)
	{
		$sql = "DELETE FROM ty_system_settings WHERE id = ".$id;
		$this->db->query($sql);
	}

}
###
