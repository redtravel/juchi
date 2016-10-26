<?php
class frontnav_model extends CI_Model
{
    
    	public function insert ($data)
	{
		$this->db->insert('front_nav', $data);
		return $this->db->insert_id();
	}

        public function filter ($filter)
	{
		$query = $this->db->get_where('front_nav', $filter, 1);
		return $query->row();
	}
        
        public function all ($filter =array())
	{
		$this->db->order_by('sort_order','desc');
       $query = $this->db->get_where('front_nav',$filter);
       return $query->result();
	}
        
	public function update ($data, $nav_id)
	{
		$this->db->update('front_nav', $data, array('nav_id' => $nav_id));
	}
        
        public function delete ($nav_id)
	{
        	$this->db->delete('front_nav', array('nav_id' => $nav_id));
	}

	public function nav_category($category_ids)
	{
		$sql = "SELECT c.category_id,c.category_name
				FROM ".$this->db->dbprefix('product_category')." AS c
				WHERE c.is_use=1 AND c.parent_id ".db_create_in($category_ids)."
				AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('product_sub')." AS sub, 
					".$this->db->dbprefix('product_info')." AS p WHERE sub.product_id=p.product_id 
					AND p.category_id=c.category_id AND sub.is_on_sale=1 LIMIT 1)
				ORDER BY c.sort_order DESC";
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function nav_brand($category_ids)
	{
		$sql = "SELECT b.brand_id, b.brand_name
				FROM ".$this->db->dbprefix('product_brand')." AS b
				WHERE b.is_use=1 
				AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('product_sub')." AS sub, ".$this->db->dbprefix('product_info')." AS p, ".$this->db->dbprefix('product_category')." AS c 
					WHERE sub.product_id=p.product_id AND p.category_id=c.category_id 
					AND c.parent_id ".db_create_in($category_ids)." AND p.brand_id=b.brand_id AND sub.is_on_sale=1 LIMIT 1)
				ORDER BY b.sort_order DESC";
		$query = $this->db->query($sql);
		return $query->result();
	}
                        
}
?>
