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

	public function address_list($user_id){
		$sql = " SELECT ua.`address_id`,ua.`consignee`,ua.`mobile`,ua.`tel`,ua.`address`,ua.`is_used`,ri1.`region_name` country,ri2.`region_name` province,ri3.`region_name` city,ri4.`region_name` district FROM " .$this->_db->dbprefix('user_address')." AS ua ";
		$sql .= " LEFT JOIN ".$this->_db->dbprefix('region_info')." AS ri1 ON ri1.region_id=ua.country";
		$sql .= " LEFT JOIN ".$this->_db->dbprefix('region_info')." AS ri2 ON ri2.region_id=ua.province";
		$sql .= " LEFT JOIN ".$this->_db->dbprefix('region_info')." AS ri3 ON ri3.region_id=ua.city";
		$sql .= " LEFT JOIN ".$this->_db->dbprefix('region_info')." AS ri4 ON ri4.region_id=ua.district";
		$sql .= " WHERE ua.user_id = '".$user_id."' ORDER BY ua.is_used DESC";
		$query = $this->_db->query($sql);
        return $query->result();
	}

	public function address_insert($data){
		//修改 原有的默认地址
		if(!empty($data->is_used)){
			$query = $this->_db->get_where('user_address',array('is_used' => 1),1);
			$add_check = $query->row();
			if(!empty($add_check)){
				$this->_db->update('user_address', array('is_used' => 0), "address_id = $add_check->address_id");
			}
		}
		$this->_db->insert('user_address', $data);
        return $this->_db->insert_id();
	}

	public function address_update($data, $address_id)
    {
		//修改 原有的默认地址
		if(!empty($data->is_used)){
			$query = $this->_db->get_where('user_address',array('is_used' => 1),1);
			$add_check = $query->row();
			if(!empty($add_check)){
				$this->_db->update('user_address', array('is_used' => 0), "address_id = $add_check->address_id");
			}
		}
        $this->_db->update('user_address', $data, "address_id = $address_id");
        return $this->_db->affected_rows();
    }

    public function all_address ($filter)
	{
		$query = $this->_db->get_where('user_address',$filter);
		return $query->row();
	}

	public function delete_address($address_id)
    {
        $this->_db->delete('user_address', array('address_id' => $address_id));
        return $this->_db->affected_rows();
    }

    public function update_address_used($address_id,$user_id)
    {
		$this->_db->update('user_address', array('is_used'=>0), "user_id = $user_id");
		$this->_db->update('user_address', array('is_used'=>1), "address_id = $address_id");
    }

    public function update($data, $user_id)
    {
        $this->_db->update('user_info', $data, "user_id = $user_id");
        return $this->_db->affected_rows();
    }
}
