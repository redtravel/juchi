<?php

/**
* Ad_model
*/
class Brand_model extends CI_Model
{
	private $_db;
	public function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	public function all_brand_list(){
        $sql = " SELECT brand_initial,GROUP_CONCAT(brand_id, '=', brand_name) brand_name 
            FROM " .$this->_db->dbprefix('product_brand').
            " WHERE is_use=1".
            " GROUP BY brand_initial ";
		$query = $this->_db->query($sql);
        return $query->result_array();
	}

	public function one_brand($brand_id){
	}
	public function get_category_ids_by_parent_id( $cat_id ){
		$cache_key = "brand_model_get_category_ids_by_parent_id_" . $cat_id;
		$cache_data = $this->cache->get($cache_key);
		if( !empty($cache_data) ) return $cache_data;
		$sql = 'SELECT category_id FROM ty_product_category WHERE parent_id='.$cat_id;
		$res = $this->_db->query($sql)->result_array();
		$cids = array();
		foreach($res as $r){
			array_push($cids, $r['category_id']);
		}
		$this->cache->save($cache_key , $cids, CACHE_TIME_BRAND_M_CATEGORYES);
		return $cids;
	}

    public function brand_list_by_category($flag, $cat_id, $continent=false){
	$cids = $this->get_category_ids_by_parent_id( $cat_id );
        if (empty($cids)){
            $cids = $cat_id;
        } else {
            $cids = implode(',', $cids);
        }
	

        if (0 == $flag){
            $sql = "SELECT DISTINCT pb.brand_id, pb.brand_logo, pb.brand_name, pb.brand_info, pl.flag_url FROM ty_product_info AS p LEFT JOIN ty_product_flag AS pl ON p.flag_id= pl.`flag_id` LEFT JOIN ty_product_category AS pc ON p.`category_id`=pc.`category_id` LEFT JOIN ty_product_brand AS pb ON p.`brand_id`=pb.`brand_id` WHERE pb.is_use = 1 AND pl.is_use = 1 AND pc.`category_id` IN ($cids)";
        } else {
	// Èç¹ûÆôÓÃÖÞ
		if( $continent ) $continent_sql = 'IN (SELECT GROUP_CONCAT(flag_id) FROM ty_product_flag WHERE continent=(SELECT continent FROM ty_product_flag WHERE flag_id='.$flag.')   )';
		else $continent_sql = '= '. $flag;
		
		$sql = "SELECT DISTINCT pb.brand_id, pb.brand_logo, pb.brand_name, pb.brand_info, pl.flag_url FROM ty_product_info AS p LEFT JOIN ty_product_flag AS pl ON p.flag_id= pl.`flag_id` LEFT JOIN ty_product_category AS pc ON p.`category_id`=pc.`category_id` LEFT JOIN ty_product_brand AS pb ON p.`brand_id`=pb.`brand_id` WHERE pb.is_use = 1 AND pl.`flag_id` $continent_sql AND pc.`category_id` IN ($cids)";
        }
	$cache_key = md5( $sql );
	$cache_data = $this->cache->get($cache_key);
	if( !empty($cache_data) ) return $cache_data;

        $res = $this->_db->query($sql)->result_array();
        $result = array();
        foreach($res as $r){
            if ( is_null($r['brand_info']) ){
                $r['brand_info'] = '';
            }
            $r['brand_logo'] = img_url($r['brand_logo']);
            $r['flag_url'] = img_url($r['flag_url']);
            array_push($result, $r);
        }
	$this->cache->save($cache_key , $result, CACHE_TIME_BRAND_M_CATEGORYES);

        return $result;

    }
    public function get_flag_category($continent=false){
        $result = array();

	if( $continent ) $sql = 'SELECT continent as flag_name, flag_id FROM ty_product_flag WHERE is_use=1 group by continent order by sort_order DESC';
	else $sql = 'SELECT flag_name, flag_id FROM ty_product_flag WHERE is_use=1';
        $result['flags'] = $this->_db->query($sql)->result_array();

        $sql = 'SELECT category_id,category_name,parent_id FROM ty_product_category WHERE genre_id = 1';
        $category = $this->_db->query($sql)->result_array();
        $category_map = array();
        foreach($category as $c){
            $pid = $c['parent_id'];
            $category_map[$pid][] = array('id' => $c['category_id'], 'name' => $c['category_name']);
        }
        $parent = $category_map[0];
        unset($category_map[0]);
        //print_r($category_map);
        foreach($parent as $p){            
            $category_map[$p['id']]['name'] = $p['name'];
        }
        $result['categorys'] = $category_map;
        return $result;
    }
}
