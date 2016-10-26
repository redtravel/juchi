<?php

/**
* Ad_model
*/
class Ad_model extends CI_Model
{
	private $_db;
	function __construct (&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	public function ad_list($page_name,$position_tag)
	{
		$CI = &get_instance();
		$sql = "SELECT a.ad_id,a.ad_code,a.end_date,p.category_id,p.brand_id
				FROM ".$this->_db->dbprefix('front_ad')." AS a
				LEFT JOIN ".$this->_db->dbprefix('front_ad_position')." AS p ON a.position_id=p.position_id
				WHERE p.page_name=? AND p.position_tag=? 
				AND a.is_use=1 AND a.start_date<=? AND a.end_date>=?";
		$query = $this->_db->query($sql,array(
			$page_name,$position_tag,$CI->time,date_change($CI->time,'P1D')
		));
		return $query->result();
	}

    function get_ad_by_position_tag($position_tag,$size=0)
    {
        // $sql="select ad_id,ad_name,ad_code,ad_link,start_date,end_date,pic_url, position_name from "
        //         .$this->db_r->dbprefix('front_ad')." a
        //         left join ".$this->db_r->dbprefix('front_ad_position')." ap
        //         on a.position_id=ap.position_id
        //         where ap.position_tag='$position_tag' and a.is_use=1
        //         and a.start_date<=now() and a.end_date>=now()
        //         order by ad_id desc";

        $sql="select ad_id,ad_name,ad_code,ad_link,start_date,end_date,pic_url, position_name from "
                .$this->db_r->dbprefix('front_ad')." a
                left join ".$this->db_r->dbprefix('front_ad_position')." ap
                on a.position_id=ap.position_id
                where ap.position_tag='$position_tag' and a.is_use=1
                
                order by ad_id desc";
        if($size>0) $sql.=" limit $size";

        $query=$this->db_r->query($sql);
        //var_export($query->result());exit;
        return $query->result();
    }
    
    function get_focus_image($type) {
        $sql = "SELECT focus_name AS title, focus_url AS href, focus_img AS img_src, small_img FROM ty_front_focus_image WHERE focus_type = ".$type." AND NOW() BETWEEN start_time AND end_time AND focus_order > 0 ORDER BY focus_order ASC";        
        $query = $this->db_r->query($sql);
        return $query->result_array();
    }

    function get_focus_image_pc($type) {
        // $sql = "SELECT focus_name AS title, focus_url AS href, focus_img AS img_src FROM ty_front_focus_image WHERE focus_type = ".$type." AND focus_order > 0 ORDER BY focus_order ASC";  
        $sql = "SELECT focus_name AS title, focus_url AS href, focus_img AS img_src FROM ty_front_focus_image WHERE focus_type = ".$type;        
        $query = $this->db_r->query($sql);
        return $query->result_array();
    }
}
