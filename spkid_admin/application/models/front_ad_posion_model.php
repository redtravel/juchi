<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class front_ad_posion_model extends CI_Model
{
        public function filter ($filter)
	{
		$query = $this->db->get_where('ty_front_ad_position', $filter, 1);
		return $query->row();
	}

        public function all($filter = array()){
                $query = $this->db->get_where('front_ad_position', $filter);
		return $query->result();
        }
	public function update ($data, $position_id)
	{
		$this->db->update('ty_front_ad_position', $data, array('position_id' => $position_id));
	}

	public function insert ($data)
	{
		$this->db->insert('ty_front_ad_position', $data);
		return $this->db->insert_id();
	}



        public function ad_p_list ($filter)
	{
		$from = " FROM ty_front_ad_position AS f
                    LEFT JOIN ty_product_brand AS b ON f.brand_id = b.brand_id
                    LEFT JOIN ty_product_category AS c ON f.category_id = c.category_id
                    ";
		$where = " WHERE 1 ";
		$param = array();
                if(!empty($filter['page_name'])){
                    $where .= " AND f.page_name like ? ";
                    $param[] = '%'.$filter['page_name'].'%';
                }
                if(!empty($filter['position_name'])){
                    $where .= " AND f.position_name like ? ";
                    $param[] = '%'.$filter['position_name'].'%';
                }
                if(!empty($filter['position_tag'])){
                    $where .= " AND f.position_tag like ? ";
                    $param[] = '%'.$filter['position_tag'].'%';
                }
                if(!empty($filter['brand_id'])){
                    $where .= " AND f.brand_id = ? ";
                    $param[] = $filter['brand_id'];
                }
                if(!empty($filter['category_id'])){
                    $where .= " AND f.category_id = ? ";
                    $param[] = $filter['category_id'];
                }

		$filter['sort_by'] = empty($filter['sort_by']) ? 'f.position_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'desc' : trim($filter['sort_order']);

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
		$sql = "SELECT f.*,b.brand_name,c.category_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
        
	public function delete ($position_id)
	{
		$this->db->delete('ty_front_ad_position', array('position_id' => $position_id));
	}
	

}




?>
