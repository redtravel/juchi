<?php
#doc
#	classname:	Category_model
#	scope:		PUBLIC
#
#/doc

class Category_model extends CI_Model
{
	public function filter($filter)
	{
		$query = $this->db->get_where('product_category', $filter, 1);
		return $query->row();
	}
	
	public function insert ($data)
	{
		$this->db->insert('product_category', $data);
		return $this->db->insert_id();
	}
	
	public function update ($data, $category_id)
	{
		$this->db->update('product_category', $data, array('category_id' => $category_id));
	}
	
	public function delete ($category_id)
	{
		$this->db->delete('product_category', array('category_id' => $category_id));
	}

	public function all_category($filter=array())
	{
		$this->db->order_by('sort_order', 'desc');
		$this->db->join('product_genre as g', 'g.id=product_category.genre_id','left');
		$query = $this->db->get_where('product_category', $filter);
		return $query->result();
	}

}
###
