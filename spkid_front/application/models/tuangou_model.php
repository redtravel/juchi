<?php

/**
 * Tuangou_model
 */
class Tuangou_model extends CI_Model {

    private $_db;

    public function __construct(&$db = NULL) {
	parent::__construct();
	$this->_db = $db ? $db : $this->db;
    }
	
	public function filter($filter = array()) {
		$query = $this->_db->get_where('ty_mami_tuan',$filter,1);
		return $query->row();
	}
	
	public function tuan_info($tuan_id, $is_preview=0) {
		$sql = "select tuan_id,product_id, tuan_name, buy_num, tuan_price, STATUS, 
	            tuan_online_time, tuan_offline_time, tuan_desc, userdefine1, 
	            userdefine2, userdefine3, userdefine4, img_315_207, img_168_110, 
	            img_500_450, product_discount, tuan_sort 
				FROM ".$this->_db->dbprefix('mami_tuan') ;
        if($is_preview) $sql .= " WHERE tuan_id = ? ";
        else $sql .= " WHERE status = 1 AND tuan_online_time<=NOW() AND tuan_offline_time>=NOW() and tuan_id = ? ";
		$query = $this->_db->query($sql,array(intval($tuan_id)));
		return $query->row();
	}
	
	public function randTuanProduct() {
		$arr = array();
		$sql = "select t.tuan_id,t.product_id, t.tuan_name, t.buy_num, t.tuan_price, t.STATUS, 
	            t.tuan_online_time, t.tuan_offline_time, t.tuan_desc, t.userdefine1, 
	            t.userdefine2, t.userdefine3, t.userdefine4, t.img_315_207, t.img_168_110, 
	            t.img_500_450, t.product_discount, t.tuan_sort, 
                p.market_price
				FROM ".$this->_db->dbprefix('mami_tuan')." AS t 
                LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON p.product_id = t.product_id  
				WHERE status = 1 AND t.tuan_online_time<=NOW() AND t.tuan_offline_time>=NOW() ORDER BY t.tuan_online_time desc LIMIT 10 ";
		$query = $this->_db->query($sql);
		$arr = $query->result();
		shuffle($arr);
		$arr=array_slice($arr,0,5);
		return $arr;
	}
	
	public function getTuaninfo($sort_type=0,$sort=0,$page=0,$pagesize=6) {
        $desc = '';
		if($sort_type==0) {
			$order = 'ORDER BY t.tuan_sort ';
			$desc = 'desc ';
		}
        else if($sort_type==1) {
			$order = 'ORDER BY t.tuan_online_time ';
			$desc = 'desc ';
		}
        else if($sort_type==2) {
			$order = 'ORDER BY t.buy_num ';
			$desc = 'desc ';
		}
        else {
			$order = 'ORDER BY t.product_discount ';
			if($sort) $desc = 'desc ';
		}
    	$sql = "select t.tuan_id,t.product_id, t.tuan_name, t.buy_num, t.tuan_price, t.STATUS, 
                t.tuan_online_time, t.tuan_offline_time, t.tuan_desc, t.userdefine1, 
                t.userdefine2, t.userdefine3, t.userdefine4, t.img_315_207, t.img_168_110, 
                t.img_500_450, t.product_discount, t.tuan_sort, 
                p.market_price
    			FROM ".$this->_db->dbprefix('mami_tuan')." AS t 
                LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON p.product_id = t.product_id  
    			WHERE status = 1 AND t.tuan_online_time<=NOW() AND t.tuan_offline_time>=NOW() $order $desc LIMIT ?,?";
    	$query = $this->_db->query($sql, array($page*$pagesize, $pagesize));
    	return $query->result();
	}
    
    public function getTuanCount() {
    	$sql = "select count(*) as cot
    			FROM ".$this->_db->dbprefix('mami_tuan')." 
    			WHERE status = 1 AND tuan_online_time<=NOW() AND tuan_offline_time>=NOW() ";
    	$query = $this->_db->query($sql);
        $ret = $query->row_array();
    	return $ret['cot'];
	}
	
	public function getTuaninfoByTuanIdList($TuanIdList) {
    	$sql = "select t.tuan_id,t.product_id, t.tuan_name, t.buy_num, t.tuan_price, t.STATUS, 
                t.tuan_online_time, t.tuan_offline_time, t.tuan_desc, t.userdefine1, 
                t.userdefine2, t.userdefine3, t.userdefine4, t.img_315_207, t.img_168_110, 
                t.img_500_450, t.product_discount, t.tuan_sort, 
                p.market_price
    			FROM ".$this->_db->dbprefix('mami_tuan')." AS t 
                LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON p.product_id = t.product_id  
    			WHERE status = 1 AND t.tuan_online_time<=NOW() AND t.tuan_offline_time>=NOW() and t.tuan_id in ($TuanIdList) 
                order by find_in_set(t.tuan_id,'$TuanIdList') LIMIT 6 ";
    	$query = $this->_db->query($sql);
    	return $query->result();
	}
    
    public function update_tuan_buy_num($num,$pid) {
    	$sql = "UPDATE ty_mami_tuan SET buy_num = buy_num + ? WHERE product_id = ?; ";
    	$query = $this->_db->query($sql, array($num, $pid));
    	return ;
	}
}
