<?php

/**
* Region_model
*/
class Region_model extends CI_Model
{
	private $_db;
	
	function __construct(&$db = NULL)
	{
		$this->_db = $db?$db:$this->db;
	}
	
	public function filter ($filter)
	{
		$query = $this->_db->get_where('region_info',$filter,1);
		return $query->row();
	}

	public function all_region ($filter)
	{
		$query = $this->_db->get_where('region_info',$filter);
		return $query->result();
	}
	
	public function region_shipping_fee($province_id)
	{
		$sql = "SELECT * FROM ".$this->_db->dbprefix('region_shipping_fee')." 
				WHERE province_id = ? LIMIT 1";
		$query = $this->_db->query($sql,array($province_id));
		return $query->row();
	}
	
	/**
	 * 获取有设置运费的省市
	 * @return type 
	 */
	public function has_shipping_fee_region(){
	    $sql = "SELECT
			sf.id,
			sf.province_id,
			ri.region_name,
			sf.online_shipping_fee,
			sf.cod_shipping_fee
            ,sr.shipping_area_id
			FROM ty_region_shipping_fee sf
			LEFT JOIN ty_region_info ri
			    ON sf.province_id = ri.region_id
			    AND ri.region_type = 1
            LEFT JOIN (SELECT r.region_id,r.shipping_area_id FROM ty_shipping_area a 
			    INNER JOIN ty_shipping_area_region r ON r.shipping_area_id = a.shipping_area_id 
			    INNER JOIN ty_shipping_info s ON s.shipping_id=a.shipping_id
			    WHERE a.is_cod=1 AND s.is_use=1) AS sr ON sf.province_id = sr.region_id
			WHERE sf.online_shipping_fee > 0
			    AND cod_shipping_fee > 0
			    GROUP BY sf.province_id";
	    $query = $this->_db->query($sql );
	    return $query->result_array();
	}
    
    /**
	 * 获取有设置运费的省市
	 * @return type 
	 */
	public function has_shipping_fee_district($district,$city,$province){
	    $sql = "SELECT r.region_id,r.shipping_area_id FROM ty_shipping_area a 
			    INNER JOIN ty_shipping_area_region r ON r.shipping_area_id = a.shipping_area_id 
			    INNER JOIN ty_shipping_info s ON s.shipping_id=a.shipping_id
			    WHERE a.is_cod=1 AND s.is_use=1 
                AND r.region_id in ( $district,$city,$province )
			    ";
	    $query = $this->_db->query($sql );
	    return $query->result_array();
	}
    
    /**
     * 获取当前区域ID
     * @param type $ip xxx.xxx.xx.xx
     * @param type $dft 默认值
     * @return type
     */
    public function get_current_region($ip, $dft = 0) {
        $ip = ip2long($ip);
        $query = $this->db_r->get_where('region_ip', array('long_start <=' => $ip, 'long_end >=' => $ip), 1);
        $row = $query->row();
        return $row ? $row->region_id : $dft;
    }
    //按新规则获取运费
    public function get_shipping_fee_province($shipping_id, $region_id){
        $sql = "SELECT sa.`shipping_fee1`, sa.`shipping_fee2`, sa.first_weight FROM `ty_shipping_area` sa 
                LEFT JOIN `ty_shipping_area_region` sar ON sa.`shipping_area_id` = sar.`shipping_area_id` 
                WHERE sa.`shipping_id` = ".$shipping_id." AND sar.`region_id` = ".$region_id;
        return $this->_db->query($sql)->row();
    }

}