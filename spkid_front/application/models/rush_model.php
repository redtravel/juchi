<?php

/**
 * Rush_model
 */
class Rush_model extends CI_Model {

    private $_db;

    public function __construct(&$db = NULL) {
	parent::__construct();
	$this->_db = $db ? $db : $this->db;
    }

    public function filter_nav($filter) {
	$query = $this->_db->get_where('front_nav', $filter, 1);
	return $query->row();
    }

    public function all_nav($filter = array()) {
	$query = $this->_db->get_where('front_nav', $filter);
	return $query->result();
    }
    
    //--rush 模块 begin-----------------------------------------------------
    /**
     * 获取促销信息
     * @param type $campaign_type
     * @return type 
     */
    public function get_campaign() {
	$campaign = $this->cache->get('campaign');
	if (empty($campaign )) {
	    $sql = "SELECT campaign_id,campaign_name FROM ty_front_campaign WHERE campaign_type = 1 AND is_use = 1 AND start_date <= NOW() AND end_date >= NOW() LIMIT 4";
	    $campaign = $this->db_r->query($sql)->result_array();
	    $this->cache->save('campaign', $campaign, CACHE_TIME_CAMPAIGN);
	}
	return $campaign;
    }
    /**
     * 获取指定的限抢信息
     * @param int $id
     * @return array 
     */
    function get_rush_by_id($rushId) {
	$cache_data = $this->cache->get("rush_cache_info_" . $rushId);
        if (!is_array($cache_data) || empty($cache_data) ) {
	    $sql = "SELECT * FROM ty_rush_info WHERE rush_id = '$rushId'";
	    $arr_rush = $this->db_r->query($sql )->row_array();
	    //处理
	    $now_time = time();
	    $end_date = strtotime($arr_rush['end_date'] );
	    $start_date = strtotime($arr_rush['start_date'] );
	    if ($start_date <= $now_time && $end_date >= $now_time) {//促销
                $arr_rush['is_promote'] = 1;
            } elseif ($start_date > $now_time) {//还没到时间
                $arr_rush['is_promote'] = 2;
            } elseif ($end_date < $now_time) {//已经结束
                $arr_rush['is_promote'] = 3;
            }
	    
	    $this->cache->save("rush_cache_info_" . $rushId , $arr_rush, CACHE_TIME_RUSH_INFO );
	    return $arr_rush;
	}else {
	    return $cache_data;
	}
    }
    
    /**
     * 获取广告
     * @return type
     */
    function get_list_ad() {
        $sql = "SELECT a.* FROM ty_front_ad_position t
                LEFT JOIN ty_front_ad a USING(position_id)
                WHERE t.position_tag = 'category_top'
                AND a.start_date <= NOW()
                AND a.end_date >= NOW()
                AND a.is_use = 1";
        return $this->db_r->query($sql)->row_array();
    }
    
    function get_provider_brand($provider_id) {
        $sql = "SELECT b.* FROM ty_provider_brand t
                LEFT JOIN ty_product_brand b USING(brand_id)
                WHERE t.is_used = 1 AND b.is_use = 1
                AND t.provider_id = ?
                ORDER BY b.sort_order DESC
                LIMIT 5;";
        return $this->db_r->query($sql, $provider_id)->result_array();
    }
/**
 * 获取所有的一级分类
 *
*/
function get_front_types($genre_id=1){
        $cache_time = CACHE_TIME_SELECT_TYPE;
	$cache_key="rand_front_types";
	if (($cache_data = $this->cache->get($cache_key) ) === FALSE ) {
		$this->cache->delete($cache_key);
		$sql = "SELECT * FROM ty_product_type WHERE parent_id=0 and genre_id=".$genre_id;
		$cache_data= $this->db_r->query($sql)->result();
		$this->cache->save($cache_key, $cache_data, $cache_time);
	}
	return $cache_data;

}


    /**
     * 获取product_type
     *
     * @param type $args
     * @param type $slave
     * @return type 
     */
    function get_select_type($args ,$slave = TRUE ){
		$this->excdb = $slave ?$this->db_r:$this->db ;
		$cache_data = array( );
		$cache_key = 'product_';
		if ($args['nav_type'] == 1 && isset($args['type_id']) ) {
			$cache_key .= 'type_' . $args['type_id'];
			if (($cache_data = $this->cache->get($cache_key) ) === FALSE ) {
				$this->cache->delete($cache_key);
					if ($args['type_id'] != 0) {
						$sql = "SELECT * FROM ty_product_type WHERE type_id = ? ";
						$row = $this->db_r->query($sql, $args['type_id'])->row_array();
						$cache_data = $row;
						$this->cache->save($cache_key, $row , CACHE_TIME_SELECT_TYPE);
					} else {
						$sql = "SELECT pt.* FROM ty_rush_product rp
								INNER JOIN ty_product_info pi
								USING(product_id)
								INNER JOIN ty_product_sub ps
								USING(product_id)
								INNER JOIN ty_product_type_link ptl
								USING(product_id) 
								INNER JOIN ty_product_type pt
								USING(type_id)
								WHERE  ps.size_id = ?
								AND 
									pi.product_sex = ?
								GROUP BY pt.type_id
								ORDER BY pt.sort_order";
						$row = $this->db_r->query($sql, array($args['size_id'], $args["sex_id"]))->result_array();
						$cache_data["cat"] = $row;
						$sql = "SELECT 
									pb.*
								FROM
									ty_rush_product rp
										INNER JOIN
									ty_product_info pi USING (product_id)
										INNER JOIN
									ty_product_sub ps USING (product_id)
									INNER JOIN 
									ty_product_brand pb
										USING(brand_id)
								WHERE
									ps.size_id = ? AND pi.product_sex = ?
								GROUP BY pb.brand_id
								ORDER BY pb.sort_order ;";
						$row = $this->db_r->query($sql, array($args['size_id'], $args["sex_id"]))->result_array();
						$cache_data["brand"] = $row;
						$this->cache->save($cache_key, $cache_data , CACHE_TIME_SELECT_TYPE);
					}
			}
		} elseif ($args['nav_type'] == 2 && isset($args['brand_id']) ) {
			$cache_key .= 'brand' . $args['brand_id'];
				$this->cache->delete($cache_key);
			if (($cache_data = $this->cache->get($cache_key) ) === FALSE ) {
					if ($args['brand_id'] != 0) {
						if (empty($args['type_id'])) {
							$sql = "SELECT pb.brand_id, pb.brand_name, pb.cat_content 
							FROM ty_product_brand pb, ty_product_type pt WHERE pb.brand_id = ? ";
						} else {
							$sql = "SELECT pb.brand_id, pb.brand_name, pb.cat_content, pt.type_id, pt.type_name 
							FROM ty_product_brand pb, ty_product_type pt WHERE pb.brand_id = ? 
							AND pt.type_id = " .$args['type_id'];
						}
						$row = $this->db_r->query($sql, array($args['brand_id']))->row_array();
						$cache_data = $row;
						$this->cache->save($cache_key, $row , CACHE_TIME_SELECT_TYPE);
					}
				}
			} elseif ($args['nav_type'] == 3 && isset($args['provider_id']) ) {
				$cache_key .= 'provider' . $args['provider_id'];
				$this->cache->delete($cache_key);
				if (($cache_data = $this->cache->get($cache_key) ) === FALSE ) {
					if ($args['provider_id'] != 0) {
						$sql = "SELECT * FROM ty_product_provider WHERE provider_id = ? ";
						$row = $this->db_r->query($sql, $args['provider_id'])->row_array();
						$cache_data = $row;
						$this->cache->save($cache_key, $row , CACHE_TIME_SELECT_TYPE);
					}
				}
			}
		return $cache_data;
    }
    
     /**
      * 获取size_name
      *
      * @param type $args
      * @param type $slave
      * @return type 
      */
    function get_select_size($args, $slave = TRUE ) {
	$this->excdb = $slave ?$this->db_r:$this->db ;
	$cache_data = array( );
	$cache_key = 'select_size';
	if (isset($args['size_id']) && !empty($args['size_id'])) {
	    $cache_key .= '_' . $args['size_id'];
	    if (($cache_data = $this->cache->get($cache_key) ) === FALSE) {
		$sql = "SELECT size_id,size_name FROM ty_product_size WHERE size_id = ? ";
		$row = $this->excdb->query($sql, $args['size_id'])->row_array();
		$cache_data = $row;
		$this->cache->save($cache_key, $row, CACHE_TIME_SELECT_TYPE);
	    }
	}
	return $cache_data;
    }
    
    /**
     * 列表页上商品
     * @param type $filter
     * @return type 
     */
    public function brand_product_list($brand_id, $page){
        $page_size = M_LIST_PAGE_SIZE;
        $page = ($page < 1) ? 1 : intval($page);
        $start = ($page-1)*$page_size;       
        $sql = "SELECT p.product_id,p.product_name,p.market_price,p.shop_price,si.size_name,pg.`img_url`,p.price_show FROM ty_product_info AS p LEFT JOIN ty_product_sub AS ps USING(product_id) LEFT JOIN ty_product_size si USING(size_id) LEFT JOIN ty_product_gallery AS pg ON ps.`product_id`=pg.`product_id` AND ps.`color_id`=pg.`color_id` LEFT JOIN ty_product_brand pb USING(brand_id) WHERE pb.`brand_id`=$brand_id AND pg.image_type='default' AND p.`is_audit`=1 AND  ps.is_on_sale = 1 GROUP BY p.`product_id` LIMIT $start, $page_size";
        $result = $this->db->query($sql)->result();
        if (1 == $page){
            $sql = 'SELECT brand_id,brand_name,brand_logo,brand_banner,brand_info,brand_story from ty_product_brand WHERE brand_id='.$brand_id;
            $brand = $this->db->query($sql)->row(); 
            return array('brand' => $brand, 'product_list' => $result);
        } else {
            return $result;
        }
    }
    public function product_list($filter , $slave = TRUE ,$is_preview = FALSE) {
		$this->adoEx = $slave ? $this->db_r : $this->db;
		$this->load->helper("product");
		$filter['page_size'] = is_int( M_LIST_PAGE_SIZE ) ? M_LIST_PAGE_SIZE : 30;
		$filter['record_count'] = 0;
		$filter['page_count'] = 0;
		
/*		$select = "SELECT sub.product_id,sub.color_id,sub.gl_num as sub_gl_num,sub.consign_num,sub.wait_num,
				 if(SUM(GREATEST(sub.gl_num-sub.wait_num,0))+SUM(GREATEST(sub.consign_num,0)+IF(sub.consign_num=-2,1000,0))>0,1,0) AS gl_num,
					p.product_name,p.product_sn,p.shop_price,p.market_price,
					p.is_promote,p.promote_price,p.promote_start_date,p.promote_end_date,
					p.is_new,p.is_hot,p.is_offcode,p.is_best,p.is_gifts,p.product_desc_additional,
					b.brand_name,c.color_name,g.img_318_318,pp.display_name,pp.provider_id,
				   (SELECT COUNT(1) FROM ty_product_liuyan pl 
				   WHERE sub.product_id = pl.tag_id AND pl.tag_type = 1 AND pl.comment_type = 2) ly_count";

		$select = "SELECT sub.product_id,sub.color_id,sub.gl_num as sub_gl_num,sub.consign_num,sub.wait_num,
				 if(SUM(GREATEST(sub.gl_num-sub.wait_num,0))+SUM(GREATEST(sub.consign_num,0)+IF(sub.consign_num=-2,1000,0))>0,1,0) AS gl_num,
					p.product_name,p.product_sn,p.shop_price,p.market_price,
					p.is_promote,p.promote_price,p.promote_start_date,p.promote_end_date,
					p.is_new,p.is_hot,p.is_offcode,p.is_best,p.is_gifts,p.product_desc_additional,
					b.brand_name,c.color_name,g.img_url,pp.display_name,pp.provider_id ";
				   
		$from = "FROM ty_product_sub AS sub
				LEFT JOIN " . $this->adoEx->dbprefix('product_info') . " AS p ON sub.product_id = p.product_id
				LEFT JOIN " . $this->adoEx->dbprefix('product_type_link') . " AS tl ON tl.product_id = p.product_id
				LEFT JOIN " . $this->adoEx->dbprefix('product_type') . " AS pt ON pt.type_id = tl.type_id
				LEFT JOIN " . $this->adoEx->dbprefix('product_brand') . " AS b ON p.brand_id = b.brand_id
				LEFT JOIN " . $this->adoEx->dbprefix('product_color') . " AS c ON c.color_id = sub.color_id
				LEFT JOIN " . $this->adoEx->dbprefix('product_gallery') . " AS g ON g.product_id=sub.product_id AND g.color_id=sub.color_id AND g.image_type='default'
				LEFT JOIN " . $this->adoEx->dbprefix('product_provider') . " AS pp ON p.provider_id = pp.provider_id ";
*/
	  $select = "SELECT sub.product_id,sub.gl_num as sub_gl_num,sub.consign_num,sub.wait_num,
				 if(SUM(GREATEST(sub.gl_num-sub.wait_num,0))+SUM(GREATEST(sub.consign_num,0)+IF(sub.consign_num=-2,1000,0))>0,1,0) AS gl_num,
					p.product_name,p.product_sn,p.shop_price,p.market_price,p.ps_num,p.pv_num,p.is_hot,p.is_new,p.is_offcode,p.is_best as is_zhanpin,
					p.is_promote,p.promote_price,p.promote_start_date,p.promote_end_date,p.is_gifts,p.product_desc_additional,p.price_show,p.product_desc,
					b.brand_name,g.img_url,pp.display_name,pp.provider_id ";
				   
		$from = "FROM ty_product_sub AS sub
				LEFT JOIN " . $this->adoEx->dbprefix('product_info') . " AS p ON sub.product_id = p.product_id
				LEFT JOIN " . $this->adoEx->dbprefix('product_type_link') . " AS tl ON tl.product_id = p.product_id
				LEFT JOIN " . $this->adoEx->dbprefix('product_type') . " AS pt ON pt.type_id = tl.type_id
				LEFT JOIN " . $this->adoEx->dbprefix('product_brand') . " AS b ON p.brand_id = b.brand_id
				LEFT JOIN " . $this->adoEx->dbprefix('product_gallery') . " AS g ON g.product_id=sub.product_id AND g.image_type='default'
				LEFT JOIN " . $this->adoEx->dbprefix('product_provider') . " AS pp ON p.provider_id = pp.provider_id ";
	
	if($is_preview){
	    $where = "WHERE p.genre_id = '".PRODUCT_TOOTH_TYPE."' AND p.source_id IN (0, '".SOURCE_ID_WEB."')";
	}else{
	   $where = "WHERE p.genre_id = '".PRODUCT_TOOTH_TYPE."' AND p.source_id IN (0, '".SOURCE_ID_WEB."') AND p.price_show = 0 AND p.shop_price > 0 AND sub.is_on_sale=1 AND b.is_use = 1 AND pp.is_use = 1 "; 
	}
	if (!empty($filter['type_id']) )//category_id
	    $where.=" AND (pt.type_id = '{$filter['type_id']}' OR pt.parent_id = '{$filter['type_id']}' OR pt.parent_id2 = '{$filter['type_id']}')";
	if (!empty($filter['brand_id']))
	    $where.=" AND p.brand_id = '{$filter['brand_id']}'";
	if (!empty($filter['sex_id']))
	    $where.=" AND p.product_sex IN ('3','{$filter['sex_id']}')";
	if (!empty($filter['kw'])) {
	    $kw = $this->adoEx->escape_like_str($filter['kw']);
	    
	    $kw_arrs = preg_split('[\s+]', $kw);
	    array_walk($kw_arrs, create_function('&$val', '$val = trim($val);')); 
	    foreach ($kw_arrs as $k_k => $k_v) {
	    	$where .= " AND (p.product_name LIKE '%{$k_v}%' OR p.product_sn LIKE '%{$k_v}%' OR p.keywords LIKE '%{$k_v}%' OR b.brand_name LIKE '%{$k_v}%' OR pt.type_name LIKE '%{$k_v}%') ";
	    }
	    
	    // $where.=" AND (p.product_name LIKE '%{$kw}%' OR p.product_sn LIKE '%{$kw}%' OR p.keywords LIKE '%{$kw}%' OR b.brand_name LIKE '%{$kw}%' OR pt.type_name LIKE '%{$kw}%') ";
	}
    if (isset($filter['ids'])){
        $ids = $filter['ids'];
        $where = " WHERE sub.product_id IN ($ids)";
    }
	if (!empty($filter['age']) && is_array($filter['age']) && count($filter['age']) == 2) {
	    $where.=" AND (p.min_month<='{$filter['age'][1]}' AND p.max_month>='{$filter['age'][0]}') ";
	}
        if (!empty($filter['provider_id']))
	    $where.=" AND pp.provider_id = {$filter['provider_id']}";
	//$group = "GROUP BY sub.product_id, sub.color_id";
	$group = "GROUP BY sub.product_id";
	// 排序 0默认 1价格底到高 2价格高到底 3上新时间
	switch ($filter['sort']) {
		case 0:
		$sort = "ORDER BY p.pv_num DESC, gl_num DESC, p.sort_order ASC, p.product_id DESC";
		break;
	    case 1:
		$sort = "ORDER BY gl_num DESC, if(p.is_promote, p.promote_price, p.shop_price) DESC, p.sort_order DESC, p.product_id DESC";
		break;
	    case 2:
		$sort = "ORDER BY gl_num DESC, if(p.is_promote, p.promote_price, p.shop_price) ASC, p.sort_order DESC, p.product_id DESC";
		break;
	    case 3:
		$sort = "ORDER BY gl_num DESC, p.promote_start_date DESC, p.sort_order DESC, p.product_id DESC";
		break;

		// added new contents for sort
		case 4: //'price_asc'
			$sort="ORDER BY p.shop_price ASC, p.sort_order DESC, p.product_id DESC";
		break;

		case 5: //'price_desc'
			$sort="ORDER BY p.shop_price DESC, p.sort_order DESC, p.product_id DESC";
		break;

		case 6: //'xiaoliang_asc'
			$sort="ORDER BY p.ps_num ASC, p.sort_order DESC, p.product_id DESC";
		break;

		case 7: //'xiaoliang_desc'
			$sort="ORDER BY p.ps_num DESC, p.sort_order DESC, p.product_id DESC";
		break;

		case 8: //'renqi_asc'
			$sort="ORDER BY p.pv_num ASC, p.sort_order DESC, p.product_id DESC";
		break;

		case 9: //'renqi_desc'
			$sort="ORDER BY p.pv_num DESC, p.sort_order DESC, p.product_id DESC";
                    break;
                case 10: //按最新排序
                        $sort="ORDER BY p.product_id DESC";
                    break;
                case 11: 
		        $sort = 'ORDER BY p.is_hot DESC';
                    break;
		// ends added
	    default://默认按优先级
		$sort = "ORDER BY p.pv_num DESC, gl_num DESC, p.sort_order ASC, p.product_id DESC";//has_order DESC,
		break;
	}
	$sql = "SELECT COUNT(1) AS ct FROM (SELECT 1 {$from} {$where} {$group}) a";
	$query = $this->adoEx->query($sql);

	$row = $query->row();
	if (!$row)
	    return array('filter' => $filter, 'list' => array());
	$filter['record_count'] = $row->ct;
	$filter['page_count'] = ceil($filter['record_count'] / $filter['page_size']);
	$filter['page'] = $filter['page'] == 0 ? 1:$filter['page'];
	$start = $filter['page'] < 1 ? 0:($filter['page'] - 1 ) * $filter['page_size']; //开始条数
	$sql = "{$select} {$from} {$where} {$group} {$sort} LIMIT {$start}, {$filter['page_size']}";	
	$query = $this->adoEx->query($sql);

	$sql_ret = $sql;

	$list = array();
	$goods_ids = array();
	foreach ($list = $query->result() as $p){
	    format_product($p);
	    if (!in_array($p->product_id, $goods_ids)) $goods_ids[] = $p->product_id;
	    $p->sale_finish = $p->gl_num == 0?"img_yishouwan":"";
	}
	//统计商品评论总数
	$comment_arr = array();
	if (!empty($goods_ids)) {
	    $goods_ids_str = implode(",", $goods_ids);
	    $sql = "SELECT tag_id, COUNT(1) AS cnt FROM ty_product_liuyan pl 
                    WHERE pl.tag_type = 1 AND pl.comment_type = 2 AND pl.tag_id IN (".$goods_ids_str.") GROUP BY tag_id";
	    $query = $this->adoEx->query($sql);
            foreach ($query->result() as $row) {
                $comment_arr[$row->tag_id] = $row->cnt;
	    }		
	}
	return array('filter' => $filter, 'list' => $list, 'comment' => $comment_arr);
    }
    
     /**
     * 预售rush
     */
    public function pre_rush()
    {
        $now=date('Y-m-d H:i:s');
        //获取预售rush
        $sql="select date_format(start_date,'%Y-%m-%d') as date from ty_rush_info
              where start_date>='$now'
              and status=1
              group by date_format(start_date, '%Y-%m-%d')  limit 4";
        $query=$this->db_r->query($sql);
        $pre_date=$query->result();
        if(empty($pre_date)) return null;
        $where_date="";
        foreach($pre_date as $date)
        {
            if($where_date=="")
                $where_date="'".$date->date."'";
            else
                $where_date.=",'".$date->date."'";
        }
        //获取在预售时间内的rush
        $sql="select rush_id,date_format(start_date, '%Y-%m-%d') as date ,
                start_date,rush_brand,rush_category,image_before_url
                 from ty_rush_info where date_format(start_date,'%Y-%m-%d') in ($where_date) and status=1
                 and start_date>'$now'
                order by date";
        $query=$this->db_r->query($sql);
        $rush=$query->result();
        return $rush;
    }
    
    /**
     * 获取正在进行的限抢
     */
    function get_sale_rush($filter=array())
    {
        $where='';
        $where_val=array();
        $dateToday = date('Y-m-d');
        $dateTomorrow =date('Y-m-d',strtotime('1 day'));
        $dateNow =date('Y-m-d H:i:s');
        $dateAfter=date('Y-m-d H:i:s',strtotime('30 min'));
        if(!empty($filter['today']))
        {
            //今日结束的限抢
            $where.=" and start_date < '$dateNow' and end_date>'$dateToday' and end_date<'$dateTomorrow'";
        }
        else{
            //正在进行的限抢  包括未来30分钟内的限抢
            $where.=" and start_date < '$dateAfter' and end_date>='$dateTomorrow'";
        }
        if(isset($filter['nav_id'])&&$filter['nav_id']!=0){
            $where.=" and nav_id=?";
            $where_val[]=$filter['nav_id'];
        }
        $sql="select rush_id,start_date,end_date,rush_discount,rush_prompt,rush_brand,rush_category,image_before_url,jump_url
              from ty_rush_info 
              where status=1 
              $where
              order by sort_order desc";
        $query=$this->db_r->query($sql,$where_val);
        return $query->result();
    }
    
    /**
     * 获取商品尺码列表
     * @param int $goods_id
     * @param int $color_id
     * @param boolean $onsale_check
     * @use_cache
     * @return array
     */
    function get_size_rows($goods_id, $color_id, $onsale_check = TRUE,$use_cache=false)
    {
        if($use_cache){
            $rows=$this->cache->get("rush_goods_$goods_id"."_$color_id");
            if(empty($rows)){
                $rows = $this->_get_size_rows($goods_id, $color_id, $onsale_check);
                $this->cache->save("rush_goods_$goods_id"."_$color_id",$rows,CACHE_TIME_PRODUCT_SUB);
            }
        }
        else{
            $rows = $this->_get_size_rows($goods_id, $color_id, $onsale_check);
        }
        $rows = $rows ? $rows : array();
        return $rows;
    }
    
    /**
     * 获取商品尺码列表
     * @param int $goods_id
     * @param int $color_id
     * @param boolean $onsale_check
     * @return array
     */
    function _get_size_rows($goods_id, $color_id, $onsale_check = TRUE)
    {
        $time = date('Y-m-d H:i:s');
        //字段：gl_num商品数量，wait_num被占用的代销库存，consign_num代销库存（-2:不限量代销 -1:不代销 >=0限量代销）
        $size_sql = "
            SELECT `gl`.`size_id`, `gl`.`product_id`, `gl`.`color_id`, size.`size_name`, 
                GREATEST(`gl`.`gl_num` - `gl`.`wait_num`, 0) AS `gl_num2`, 
                GREATEST(`gl`.`gl_num` - `gl`.`wait_num`, 0) + GREATEST(`gl`.`consign_num`, 0) AS `gl_num`, `gl`.`consign_num`
            FROM `ty_product_sub` AS `gl`
            LEFT JOIN `ty_product_size` AS `size` ON `gl`.`size_id` = `size`.`size_id`
            WHERE `gl`.`product_id` = {$goods_id} AND `gl`.`color_id`= {$color_id} " . ($onsale_check ? "AND gl.is_on_sale = 1" : '') . " 
            ORDER BY `size`.`sort_order` ASC
        ";
        $size_rows = $this->db_r->query($size_sql)->result_array();

        //拼接待查询购物车的字符
        $arr_color_size = array();
        foreach ($size_rows as $size_row) {
            if ($size_row['consign_num'] != -2 && $size_row['gl_num'] > 0) {
                $arr_color_size[] = "{$size_row['product_id']}_{$size_row['color_id']}_{$size_row['size_id']}";
            }
        }

        //检查商品是否为限时抢购
        $goods_sql = "SELECT COUNT(*) AS `c` FROM `ty_product_info` WHERE `product_id` = {$goods_id} AND `promote_start_date` <= '{$time}' AND `promote_end_date` >= '{$time}'";
        $goods_row = $this->db_r->query($goods_sql)->row_array();
        $is_promote = $goods_row['c'] > 0 ? TRUE : FALSE;

        //如果是限时抢购，则获取购物车中的商品有效数量
        $cart_list = array();
        if (count($arr_color_size) > 0 && $is_promote === TRUE) {
            $cart_sql = "
                SELECT CONCAT(`product_id`, '_', `color_id`, '_', `size_id`) as `goods_color_size_id`, SUM(`product_num`) as `cart_num`
                FROM `ty_front_cart`
                WHERE `discount_type` = 1 
                AND `package_id` = 0 
                AND `update_date` >= '" .date('Y-m-d H:i:s', time()-CART_SAVE_SECOND). "' 
                AND CONCAT(`product_id`, '_', `color_id`, '_', `size_id`) " . db_create_in($arr_color_size) . "
                GROUP BY `product_id`, `color_id`, `size_id`
            ";
            $cartRows = $this->db_r->query($cart_sql)->result_array();
            foreach ($cartRows as $cartRow) {
                $cart_list[$cartRow['goods_color_size_id']] = $cartRow['cart_num'];
            }
        }

        //重新计算总库存
        $rows = array();
        foreach ($size_rows as $key=>$size_row) {
            $cart_key = $size_row['product_id'] . '_' . $size_row['color_id'] . '_' . $size_row['size_id'];
            $cart_num = isset($cart_list[$cart_key]) ? $cart_list[$cart_key] : 0;//获取购物车里的库存
            $size_row['gl_num_all'] = $size_row['consign_num'] != -2 ? (max($size_row['gl_num'] - $cart_num, 0)) : 0;
            $size_row['available'] = ($size_row['gl_num_all'] > 0 || $size_row['consign_num'] == -2);//尺寸库存是否有效
            $rows[] = array('size_id' => $size_row['size_id'], 'size_name' => $size_row['size_name'], 'gl_num2' => $size_row['gl_num2'], 'gl_num_all' => $size_row['gl_num_all'], 'available' => $size_row['available']);
        }
        $rows = $rows ? $rows : array();
        return $rows;
    }
    //--rush 模块 end-----------------------------------------------------
    
}
