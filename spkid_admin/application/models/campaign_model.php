<?php
#doc
#	classname:	Brand_model
#	scope:		PUBLIC
#
#/doc

class Campaign_model extends CI_Model
{

	public function filter($filter)
	{
		$query = $this->db->get_where('front_campaign', $filter, 1);
		return $query->row();
	}
	public function campaign_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('front_campaign') ." AS a  LEFT JOIN ".
        $this->db->dbprefix('product_info')." AS b ON a.product_id = b.product_id   ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['campaign_name']))
		{
			$where .= " AND a.campaign_name LIKE ? ";
			$param[] = '%' . $filter['campaign_name'] . '%';
		}
        if (!empty($filter['start_time']))
		{
			$where .= " AND a.start_date > ? ";
			$param[] = $filter['start_date'];
		}
        if (!empty($filter['end_time']))
		{
			$where .= " AND a.end_date < ? ";
			$param[] = $filter['end_time'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.campaign_id' : trim($filter['sort_by']);
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
		$sql = "SELECT a.*,b.product_name,b.product_sn "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function insert ($data)
	{
		$this->db->insert('front_campaign', $data);
		return $this->db->insert_id();
	}

	public function update ($data, $campaign_id)
	{
		$this->db->update('front_campaign', $data, array('campaign_id' => $campaign_id));
	}

	public function delete ($campaign_id)
	{
		$this->db->delete('front_campaign', array('campaign_id' => $campaign_id));
	}

       function product($product_name){
            $sql = "SELECT i.product_id,i.product_sn,i.product_name,p.provider_name FROM ty_product_info AS i
                LEFT JOIN ty_product_provider as p ON i.provider_id = p.provider_id WHERE EXISTS(SELECT 'X' FROM ty_product_sub s WHERE i.product_id = s.product_id AND (s.gl_num > 0 OR s.consign_num = -2 OR s.consign_num > 0 )) AND i.product_name like ? LIMIT 10";
            $param = array();
            $param[] = '%'.$product_name.'%';
            $query = $this->db->query($sql , $param);
            return $query->result();
        }

       function productsn($product_sn){
            $sql = "SELECT i.product_id,i.product_sn,i.product_name,p.provider_name FROM ty_product_info AS i
                LEFT JOIN ty_product_provider as p ON i.provider_id = p.provider_id WHERE EXISTS(SELECT 'X' FROM ty_product_sub s WHERE i.product_id = s.product_id AND (s.gl_num > 0 OR s.consign_num = -2 OR s.consign_num > 0 )) AND i.product_sn like ? LIMIT 10";
            $param = array();
            $param[] = '%'.$product_sn.'%';
            $query = $this->db->query($sql , $param);
            return $query->result();
        }


}
###