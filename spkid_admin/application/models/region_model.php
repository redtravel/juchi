<?php
#doc
#	classname:	Product_model
#	scope:		PUBLIC
#
#/doc

class region_model extends CI_Model
{
	public function filter($filter)
	{
            $query = $this->db->get_where('region_info', $filter, 1);
            return $query->row();
	}

	public function all_region ($filter)
	{
           $query = $this->db->get_where('region_info', $filter);
           return $query->result();
	}
	public function all ($filter)
	{
           $query = $this->db->get_where('region_info', $filter);
           return $query->result();
	}

	public function insert ($data)
	{
            $this->db->insert('region_info', $data);
            return $this->db->insert_id();
	}

	public function update ($data, $region_id)
	{
            $this->db->update('region_info', $data, array('region_id' => $region_id));
	}

	public function delete ($region_id)
	{
            $this->db->delete('region_info', array('region_id' => $region_id));
	}

	public function region_in_order($region_id)
	{
		$sql = "SELECT * FROM ".$this->db->dbprefix('order_info')." 
				WHERE country = ? OR province = ? OR city = ? OR district = ? LIMIT 1";
		$query = $this->db->query($sql,array($region_id,$region_id,$region_id,$region_id));
		return $query->row();
	}

	public function region_in_change($region_id)
	{
		$sql = "SELECT * FROM ".$this->db->dbprefix('order_change_info')." 
				WHERE country = ? OR province = ? OR city = ? OR district = ? LIMIT 1";
		$query = $this->db->query($sql,array($region_id,$region_id,$region_id,$region_id));
		return $query->row();
	}
	
	public function region_shipping_fee($province_id)
	{
		$sql = "SELECT * FROM ".$this->db->dbprefix('region_shipping_fee')." 
				WHERE province_id = ? LIMIT 1";
		$query = $this->db->query($sql,array($province_id));
		return $query->row();
	}
	
	public function insert_region_shipping ($data)
	{
            $this->db->insert('region_shipping_fee', $data);
            return $this->db->insert_id();
	}

	public function update_region_shipping ($data, $province_id)
	{
            $this->db->update('region_shipping_fee', $data, array('province_id' => $province_id));
	}
	
	public function delete_region_shipping ($province_id)
	{
            $this->db->delete('region_shipping_fee', array('province_id' => $province_id));
	}

	public function all_data ($data)
	{
           $query = $this->db->get_where('region_info', $data);
           return $query->result();
	}
	public function get_specified_region( $ids ){
		$this->db->where_in('region_id', $ids);
		return $this->db->get('region_info')->result_array();

	}
        //按新规则获取运费
        public function get_shipping_fee_province($shipping_id, $region_id){
            $sql = "SELECT sa.`shipping_fee1`, sa.`shipping_fee2`, sa.first_weight FROM `ty_shipping_area` sa 
                    LEFT JOIN `ty_shipping_area_region` sar ON sa.`shipping_area_id` = sar.`shipping_area_id` 
                    WHERE sa.`shipping_id` = ".$shipping_id." AND sar.`region_id` = ".$region_id;
            return $this->db->query($sql)->row();
        }

}
###