<?php

/**
* Ad_model
*/
class Address_model extends CI_Model
{
	private $_db;
	public function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}
	
	//PC 
	public function address_insert($param){
		//修改 原有的默认地址
		if($param['is_used'] == 1){
			$query = $this->_db->get_where('user_address',array('is_used' => 1,'user_id' => $param['user_id']),1);
			$add_check = $query->row();
			if(!empty($add_check)){
				$this->_db->update('user_address', array('is_used' => 0), "address_id = $add_check->address_id");
			}
		}
		$this->_db->insert('user_address', $param);
        return $this->_db->insert_id();
	}

	//PC
	public function address_update($param, $address_id)
    {
		//修改 原有的默认地址
		if($param['is_used'] == 1){
			$query = $this->_db->get_where('user_address',array('is_used' => 1,'user_id' => $param['user_id']),1);
			$add_check = $query->row();
			if(!empty($add_check)){
				$this->_db->update('user_address', array('is_used' => 0), "address_id = $add_check->address_id");
			}
		}
        $this->_db->update('user_address', $param, "address_id = $address_id");
        return $this->_db->affected_rows();
    }

    //PC
    public function all_address ($filter)
	{
		$query = $this->_db->get_where('user_address',$filter,1);
		return $query->row();
	}

	//PC 
	public function delete_address($address_id)
    {
        $this->_db->delete('user_address', array('address_id' => $address_id));
        return $this->_db->affected_rows();
    }
}
