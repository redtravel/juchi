<?php
#doc
#	classname:	Provider_model
#	scope:		PUBLIC
#
#/doc

class Provider_model extends CI_Model
{

	public function filter($filter)
	{
		$query = $this->db->get_where('product_provider', $filter, 1);
		return $query->row();
	}
	public function provider_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_provider')." AS p ";
		$where = " WHERE 1 AND parent_id=".$filter['parent_id'];
		$param = array();
		if (!empty($filter['provider_code']))
		{
			$where .= " AND p.provider_code LIKE ? ";
			$param[] = $filter['provider_code'] . '%';
		}
		if (!empty($filter['provider_name']))
		{
			$where .= " AND p.provider_name LIKE ? ";
			$param[] = '%' . $filter['provider_name'] . '%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'p.provider_id' : trim($filter['sort_by']);
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
		$from_cooperation = " LEFT JOIN ".$this->db->dbprefix('product_cooperation')." AS c ON p.provider_cooperation = c.cooperation_id";
		$sql = "SELECT p.*,c.cooperation_name "
				. $from .$from_cooperation . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function insert ($data)
	{
		$this->db->insert('product_provider', $data);
		return $this->db->insert_id();
	}
	
	public function update ($data, $provider_id)
	{
		$this->db->update('product_provider', $data, array('provider_id' => $provider_id));
	}
	
	public function delete ($provider_id)
	{
		$this->db->delete('product_provider', array('provider_id' => $provider_id));
	}

	public function all_provider($filter=array(),$order_by = "")
	{	
		if(!empty($order_by))
			$this->db->order_by($order_by);
		$query = $this->db->get_where('product_provider',$filter);
		return $query->result();
	}

	//合作方式为三方
	public function all_provider_coop($filter=array(),$order_by = "")
	{	
		if(!empty($order_by))
			$this->db->order_by($order_by);
		$this->db->where('provider_cooperation',THIRD_DELIVERY_COOP_ID);
		$query = $this->db->get_where('product_provider',$filter);
		return $query->result();
	}

    /**
     * 供应商合作方式表查询
     * @param type $filter
     * @param type $order_by
     * @return type
     */
    public function all_cooperation($filter=array(),$order_by = "")
	{	
		if(!empty($order_by))
			$this->db->order_by($order_by);
		$query = $this->db->get_where('product_cooperation',$filter);
		return $query->result();
	}
	
	public function proc_provider_product_num () {
	    $sql = "CALL provider_onsale_product_num();";
		$this->db->query($sql);
	}
    
    /**
     * 取运费配置,没有配置的地区以默认配置代替
     * @param type $provider_id
     */
    public function get_shipping_fee_config($provider_id) {
        $CI = & get_instance();
        $CI->config->load('provider');
        $default_shipping_fee_config = $this->config->item('provider_shipping_config');
        $provider_shipping_fee_config = array();
        $provider = $this->filter(array('provider_id' => $provider_id));
        if (empty($provider) || empty($provider->shipping_fee_config)){
            return $default_shipping_fee_config;
        }
        foreach (json_decode($provider->shipping_fee_config) as $config) {
            $provider_shipping_fee_config[intval($config->regionId)] = array(floatval($config->fee), floatval($config->price));
        }
        return $provider_shipping_fee_config + $default_shipping_fee_config;
    }

    /**
	 *获取管辖区域
	 *
    */
    public function get_region($send,$id){
    	$sql = "select ".$send." from ty_product_provider where provider_id=".$id;
    	$query = $this->db->query($sql);
    	$country = $query->result();
    	return $country;
    }
	public function gen_provider_sn(){
	    return 'S'.date('ym').$this->get_random();
	}
	private function get_random(){
        $sql = "SELECT rand_id, rand_sn FROM ya_product_sn_rand WHERE status = 0 ORDER BY rand_id ASC LIMIT 1";
        $result = $this->db->query($sql)->row_array();
        if (empty($result))
            return false;
        $sql = "UPDATE ya_product_sn_rand SET status = 1 WHERE `rand_id` = ".$result['rand_id'];
        $this->db->query($sql);
        return $result['rand_sn'];
    }

}
###
