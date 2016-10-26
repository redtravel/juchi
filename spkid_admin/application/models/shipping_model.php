<?php
#doc
#	classname:	Product_model
#	scope:		PUBLIC
#
#/doc

class Shipping_model extends CI_Model
{
	public function filter_shipping_info($filter)
	{
            $query = $this->db->get_where('ty_shipping_info', $filter, 1);
            return $query->row();
	}
    
    public function filter ($filter)
    {
        return $this->filter_shipping_info($filter);
    }

	public function all_shipping ($filter = array())
	{
            $query = $this->db->get_where('shipping_info',$filter);
            return $query->result();
	}

	public function insert_shipping_info ($data)
	{
            $this->db->insert('ty_shipping_info', $data);
            return $this->db->insert_id();
	}

	public function update_shipping_info ($data, $shipping_id)
	{
            $this->db->update('ty_shipping_info', $data, array('shipping_id' => $shipping_id));
	}

	public function delete_shipping_info ($shipping_id)
	{
            $this->db->delete('ty_shipping_info', array('shipping_id' => $shipping_id));
	}

        
        
        
        public function all_shipping_area($filter){
            $param = array();
            $where = '';
            foreach($filter as $key => $val){
                $where .= " AND s.".$key." = ? ";
                $param[] = $val;
            }
            $sql = "SELECT s.shipping_area_id,s.shipping_area_name,s.is_cod,r.region_name,s.shipping_area_id,t.region_id,s.shipping_id, s.shipping_fee1, s.shipping_fee2 FROM ty_shipping_area AS s 
                LEFT JOIN ty_shipping_area_region AS t ON s.shipping_area_id = t.shipping_area_id 
                LEFT JOIN ty_region_info AS r ON t.region_id = r.region_id
                WHERE 1 ".$where;
            $query = $this->db->query($sql,$param);
            $list = $query->result();
            $query->free_result();
            return $list;
        }
        public function insert_shipping_area ($data)
	{
            $this->db->insert('ty_shipping_area', $data);
            return $this->db->insert_id();
	}
	public function filter_shipping_area($filter)
	{
            $query = $this->db->get_where('ty_shipping_area', $filter, 1);
            return $query->row();
	}
        public function update_shipping_area ($data, $shipping_area_id)
	{
            $this->db->update('ty_shipping_area', $data, array('shipping_area_id' => $shipping_area_id));
	}
        public function delete_shipping_area ($data)
	{
            $this->db->delete('ty_shipping_area', $data);
	}
        
        
        
        
        public function insert_shipping_area_region ($data)
	{
            $this->db->insert('ty_shipping_area_region', $data);
            return $this->db->insert_id();
	}
        public function all_shipping_area_region($filter){
            $param = array();
            $where = '';
            foreach($filter as $key => $val){
                $where .= " AND t.".$key." = ? ";
                $param[] = $val;
            }
            $sql = "SELECT * FROM ty_shipping_area_region AS t LEFT JOIN ty_region_info AS i ON t.region_id = i.region_id  WHERE 1 ".$where;
            $query = $this->db->query($sql,$param);
            $list = $query->result();
            $query->free_result();
            return $list;
            
        }
        public function delete_shipping_area_region ($data)
	{
            $this->db->delete('ty_shipping_area_region', $data);
	}
        //检查某物流公司下其它区域是否已存在这些地区
        public function shipping_area_filter($shipping_id, $area_id, $region_ids){
            $sql = "SELECT GROUP_CONCAT(ri.region_id) AS region_ids, GROUP_CONCAT(ri.`region_name`) AS region_names FROM `ty_shipping_area` sa 
LEFT JOIN `ty_shipping_area_region` sar ON sa.`shipping_area_id` = sar.`shipping_area_id` 
LEFT JOIN `ty_region_info` ri ON sar.`region_id` = ri.`region_id` 
WHERE sa.shipping_id = ".$shipping_id." AND sa.`shipping_area_id` <> ".$area_id." AND sar.`region_id` IN ($region_ids)";
            return $this->db->query($sql)->row_array();
        }
        
}
