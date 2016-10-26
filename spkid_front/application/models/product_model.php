<?php
/**
* 
*/
class Product_model extends CI_Model
{
	private $_db;
	public function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
		$this->load->helper("product_helper");
	}
	
	public function filter($filter = array())
	{
		$query = $this->_db->get_where('product_info',$filter,1);
		return $query->row();
	}

	public function product_info($product_id)
	{
		$sql = "SELECT p.*, p.is_best as is_zhanpin,f.flag_name,f.flag_url,b.brand_name,b.brand_info, b.brand_story, b.logo_160_73,b.brand_story, pp.display_name, pp.logo, pp.product_num, pp.provider_cooperation, p.product_desc_additional
				FROM ".$this->_db->dbprefix('product_info')." AS p
				LEFT JOIN ".$this->_db->dbprefix('product_flag')." AS f ON p.flag_id = f.flag_id
				LEFT JOIN ".$this->_db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id 
                                LEFT JOIN ".$this->_db->dbprefix('product_provider')." AS pp ON p.provider_id = pp.provider_id 
				WHERE p.product_id = ? AND p.source_id IN (0, '".SOURCE_ID_WEB."')";
		$query = $this->db_r->query($sql,array(intval($product_id)));
		return $query->row();
	}

	public function all_gallery($filter)
	{
		$this->db_r->order_by('sort_order','desc');
		$query = $this->db_r->get_where('product_gallery',$filter);
		return $query->result();
	}

	public function filter_sub($filter)
	{
		$query = $this->_db->get_where('product_sub',$filter,1);
		return $query->row();
	}

	public function all_sub($filter)
	{
		if (isset($filter['sub_id']) && is_array($filter['sub_id'])) {
			$this->_db->where_in('sub_id',$filter['sub_id']);
			unset($filter['sub_id']);
		}
		if (isset($filter['product_id']) && is_array($filter['product_id'])) {
			$this->_db->where_in('product_id',$filter['product_id']);
			unset($filter['product_id']);
		}
		$query = $this->_db->get_where('product_sub',$filter);
		return $query->result();
	}

	public function sub_list($filter)
	{
		$sql = "SELECT sub.*,c.color_sn,c.color_name,s.size_sn,s.size_name
				FROM ".$this->_db->dbprefix('product_sub')." AS sub
				LEFT JOIN ".$this->_db->dbprefix('product_color')." AS c ON sub.color_id = c.color_id
				LEFT JOIN ".$this->_db->dbprefix('product_size')." AS s ON sub.size_id = s.size_id
				WHERE 1 ";
		$param = array();
		if(isset($filter['product_id']) && !is_array($filter['product_id'])){
			$sql .= " AND sub.product_id = ? ";
			$param[] = intval($filter['product_id']);
		}
		if(isset($filter['product_id']) && is_array($filter['product_id'])){
			$sql .= " AND sub.product_id = ".db_create_in($filter['product_id']);
		}
		if(isset($filter['color_id'])){
			$sql .= " AND sub.color_id = ? ";
			$param[] = intval($filter['color_id']);
		}
		if(isset($filter['size_id'])){
			$sql .= " AND sub.size_id = ? ";
			$param[] = intval($filter['size_id']);
		}
		if(isset($filter['is_on_sale'])){
			$sql .= " AND sub.is_on_sale = ? ";
			$param[] = intval($filter['is_on_sale']);
		}
		$sql .= " ORDER BY sub.sort_order DESC, s.sort_order DESC";
		$query = $this->_db->query($sql,$param);
		return $query->result();
	}

	public function lock_sub($sub_id)
	{
		if (is_array($sub_id)) {
			$sql = "SELECT * FROM ".$this->_db->dbprefix('product_sub')." WHERE sub_id ".db_create_in($sub_id)." FOR UPDATE";
			$query = $this->_db->query($sql,$sub_id);
			return $query->result();
		} else {
			$sql = "SELECT * FROM ".$this->_db->dbprefix('product_sub')." WHERE sub_id = ? FOR UPDATE";
			$query = $this->_db->query($sql,array(intval($sub_id)));
			return $query->row();
		}
		
	}

	public function sub_info($sub_id)
	{
		$sql = "SELECT sub.*,p.*,c.color_name,s.size_name, pt.type_name 
				FROM ".$this->_db->dbprefix('product_sub')." AS sub
				LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON sub.product_id = p.product_id
				LEFT JOIN ".$this->_db->dbprefix('product_color')." AS c ON sub.color_id = c.color_id
				LEFT JOIN ".$this->_db->dbprefix('product_size')." AS s ON sub.size_id = s.size_id 
                                LEFT JOIN ty_product_type_link ptl ON ptl.product_id = p.product_id 
				LEFT JOIN ty_product_type pt ON pt.type_id = ptl.type_id 
				WHERE sub_id = ?";
		$query = $this->_db->query($sql,array(intval($sub_id)));
		return $query->row();
	}

	public function update_sub($update,$sub_id)
	{
		$this->db->update('product_sub',$update,array('sub_id'=>$sub_id));
	}

	public function filter_size_image($filter)
	{
		$query = $this->_db->get_where('product_size_image',$filter,1);
		return $query->row();
    }

	public function all_carelabel($filter=array())
	{
		if(isset($filter['carelabel_id']) && is_array($filter['carelabel_id'])){
			$this->db_r->where_in('carelabel_id',$filter['carelabel_id']);
			unset($filter['carelabel_id']);
		}
		$query = $this->db_r->get_where('product_carelabel',$filter);
		return $query->result();
	}

	//购物车中占有的商品库存
	public function sub_in_cart($product_id)
	{
		$CI = &get_instance();
		$sql = "SELECT sub_id,SUM(product_num) AS product_num
				FROM ".$this->_db->dbprefix('front_cart')." 
				WHERE product_id ".(is_array($product_id)? db_create_in($product_id):"={$product_id}")." 
				AND update_date>= '".date_change($CI->time,'-'.CART_SAVE_TIME)."' GROUP BY sub_id";
		$query = $this->_db->query($sql);
		return $query->result();
	}

	public function filter_category($filter)
	{
		$query = $this->_db->get_where('product_category',$filter,1);
		return $query->row();
	}
   
        
	public function get_category($parent_id=0)
	{
            $list = array();
            $sql = "select type_id AS category_id, type_name AS category_name, cat_content from ty_product_type where parent_id=".$parent_id." and is_show_cat = 1 order by sort_order";
            $query = $this->_db->query($sql);
            $list = $query->result_array();
            return $list;
	}

	public function category_number()
	{
		$query=$this->_db->order_by('sort_order','desc')->get_where('product_category',array('is_use'=>1));
		$all_category = index_array($query->result(),'category_id');
		foreach ($all_category as &$category) $category->number = 0;
		
		$sql = "SELECT COUNT(DISTINCT sub.product_id,sub.color_id) AS ct, p.category_id
				FROM ".$this->_db->dbprefix('product_sub')." AS sub
				LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON sub.product_id=p.product_id
				WHERE p.is_audit=1 AND sub.is_on_sale=1 
					AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num)
				GROUP BY p.category_id";
		$query = $this->_db->query($sql);
		foreach ($query->result() as $row) {
			if (!isset($all_category[$row->category_id])) continue;
			$all_category[$row->category_id]->number += $row->ct;
			$parent_id = $all_category[$row->category_id]->parent_id;
			if($parent_id && isset($all_category[$parent_id])) $all_category[$parent_id]->number+=$row->ct;
		}
		return $all_category;
	}

	public function category_size($brand_id=0)
	{
		$sql = "SELECT DISTINCT p.category_id,s.size_id,s.size_name
				FROM ".$this->_db->dbprefix('product_sub')." AS sub
				LEFT JOIN ".$this->_db->dbprefix('product_size')." AS s ON sub.size_id = s.size_id
				LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON sub.product_id = p.product_id
				WHERE sub.is_on_sale=1 
				".($brand_id?" AND p.brand_id='{$brand_id}' ":'')."
				AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num)
				ORDER BY s.sort_order DESC";
		$query = $this->_db->query($sql);
		$result = array();
		foreach ($query->result() as $size) {
			if(!isset($result[$size->category_id])) $result[$size->category_id] = array();
			$result[$size->category_id][$size->size_id] = $size;
		}
		return $result;
	}

	public function category_brand()
	{
		$sql = "SELECT DISTINCT p.category_id,b.brand_id,b.brand_name
				FROM ".$this->_db->dbprefix('product_sub')." AS sub
				LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON sub.product_id = p.product_id
				LEFT JOIN ".$this->_db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id
				WHERE p.brand_id>0 AND sub.is_on_sale=1 AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num);";
		$query = $this->_db->query($sql);
		$result = array();
		foreach ($query->result() as $brand) {
			if(!isset($result[$brand->category_id])) $result[$brand->category_id] = array();
			$result[$brand->category_id][$brand->brand_id] = $brand;
		}
		return $result;
	}

	public function brand_category()
	{
		$sql = "SELECT DISTINCT p.brand_id,c.category_id,c.category_name
				FROM ".$this->_db->dbprefix('product_sub')." AS sub
				LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON sub.product_id = p.product_id
				LEFT JOIN ".$this->_db->dbprefix('product_category')." AS c ON p.category_id = c.category_id
				WHERE p.brand_id>0 AND sub.is_on_sale=1 AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num);";
		$query = $this->_db->query($sql);
		$result = array();
		foreach ($query->result() as $cat) {
			if(!isset($result[$cat->brand_id])) $result[$cat->brand_id] = array();
			$result[$cat->brand_id][$cat->category_id] = $cat;
		}
		return $result;
		
	}

	public function filter_brand($filter)
	{
		$query = $this->_db->get_where('product_brand',$filter,1);
		return $query->row();
	}

	public function all_brand($filter)
	{
		$query = $this->_db->get_where('product_brand',$filter);
		return $query->result();
	}

	public function brand_info($brand_id)
	{
		$sql = "SELECT b.*,f.flag_name,f.flag_url
				FROM ".$this->_db->dbprefix('product_brand')." AS b 
				LEFT JOIN ".$this->_db->dbprefix('product_flag')." AS f ON b.flag_id=f.flag_id
				WHERE b.brand_id=?";
		$query = $this->_db->query($sql,array($brand_id));
		return $query->row();
	}

	public function brand_list()
	{
		$sql="SELECT b.brand_id,b.brand_name,b.logo_75_34,b.brand_banner
				FROM ".$this->_db->dbprefix('product_brand')." AS b
				WHERE EXISTS(SELECT 1 FROM ".$this->_db->dbprefix('product_sub')." AS sub LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON sub.product_id=p.product_id
					WHERE p.brand_id=b.brand_id AND sub.is_on_sale=1 AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num) LIMIT 1)
				ORDER BY b.sort_order DESC";
		$query = $this->_db->query($sql);
		return $query->result();
	}

	public function buy_buy($product_id)
	{
		$order_ids = array();
		$result = array();
		$sql = "SELECT DISTINCT op.order_id 
				FROM ".$this->_db->dbprefix('order_product')." AS op
				WHERE op.product_id='{$product_id}' AND op.discount_type!=4
				ORDER BY op.order_id DESC LIMIT 50";
		$query = $this->_db->query($sql);
		foreach($query->result() as $order) $order_ids[]=$order->order_id;
		if(!$order_ids) return array();
		$sql = "SELECT p.product_id,p.product_name,g.img_170_170, p.market_price,p.shop_price,
				p.is_promote,p.promote_start_date,p.promote_end_date,p.promote_price,pb.brand_name
				FROM ".$this->_db->dbprefix('order_product')." AS op
				LEFT JOIN ".$this->_db->dbprefix('product_sub')." AS sub ON sub.product_id=op.product_id AND sub.color_id=op.color_id AND sub.size_id=op.size_id
				LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON op.product_id=p.product_id
				LEFT JOIN ".$this->_db->dbprefix('product_brand')."  AS pb ON pb.brand_id = p.brand_id
				LEFT JOIN ".$this->_db->dbprefix('product_gallery')." AS g ON g.product_id=op.product_id AND g.color_id=op.color_id AND g.image_type='default'
				WHERE op.order_id ".db_create_in($order_ids)." AND op.product_id!='{$product_id}' AND op.discount_type!=4
				AND sub.is_on_sale=1 AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num)
				GROUP BY op.product_id ORDER BY op.order_id DESC LIMIT 5";
		$query = $this->_db->query($sql);
		foreach($result=$query->result() as $p) format_product($p);
		return $result;
	}

	public function link_product($product_id)
	{
		$sql="SELECT p.product_id,p.product_name,g.img_170_170, g.img_url, p.market_price,p.shop_price, p.subhead,
				p.is_promote,p.promote_start_date,p.promote_end_date,p.promote_price, p.brand_name 
			FROM ty_product_sub AS sub
			LEFT JOIN ty_product_info AS p ON sub.product_id=p.product_id
			LEFT JOIN ty_product_gallery AS g ON sub.product_id=g.product_id AND  g.image_type='default'
			WHERE sub.is_on_sale=1 AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num)
			AND sub.product_id IN (
			SELECT l1.link_product_id FROM ty_product_link AS l1 WHERE l1.product_id=? 
			UNION
			SELECT l2.product_id FROM ty_product_link AS l2 WHERE l2.link_product_id=? AND l2.is_bothway=1 
			)
			GROUP BY p.product_id
			ORDER BY p.sort_order LIMIT 6";
		$query=$this->_db->query($sql, array($product_id,$product_id));		
		foreach($result=$query->result() as $p) format_product($p);
		return $result;
	}

	public function get_link_product_by_rule($product_id, $left, $arr_exist_ids = array())
	{
		$exist_ids = count($arr_exist_ids) > 0 ? '(' . implode($arr_exist_ids, ',') . ')' : '';
		$sql = "(";
		$sql .="SELECT p.product_id,p.product_name,g.img_170_170, g.img_url, p.market_price,p.shop_price,p.subhead,
				p.is_promote,p.promote_start_date,p.promote_end_date,p.promote_price,p.brand_name 
			FROM ty_product_sub AS sub
			LEFT JOIN ty_product_info AS p ON sub.product_id=p.product_id
			LEFT JOIN ty_product_gallery AS g ON sub.product_id=g.product_id AND  g.image_type='default'
			WHERE sub.is_on_sale=1 AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num)
			AND ( p.product_name = (select product_name from ty_product_info where product_id = ?))
			AND p.product_id != ? ";
		$sql .= ($exist_ids ? " AND p.product_id not in " .  $exist_ids . ' ' : ' ');
		$sql .=	" GROUP BY p.product_id
			ORDER BY p.sort_order LIMIT " . $left;
		$sql .= ")";
		$sql .=" union ";
		$sql .= "(";

		$sql .= "SELECT p.product_id,p.product_name,g.img_170_170, g.img_url, p.market_price,p.shop_price,p.subhead,
				p.is_promote,p.promote_start_date,p.promote_end_date,p.promote_price,p.brand_name 
			FROM ty_product_sub AS sub
			LEFT JOIN ty_product_info AS p ON sub.product_id=p.product_id
			LEFT JOIN ty_product_gallery AS g ON sub.product_id=g.product_id AND  g.image_type='default'
			WHERE sub.is_on_sale=1 AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num)
			AND ( p.brand_id = (select brand_id from ty_product_info where product_id = ?))
			AND p.product_id != ? ";
		$sql .= ($exist_ids ? " AND p.product_id not in " .  $exist_ids . ' ' : ' ');
		$sql .=" GROUP BY p.product_id
			ORDER BY p.sort_order LIMIT " . $left;
		$sql .= ")";
		$query=$this->_db->query($sql, array($product_id,$product_id, $product_id, $product_id));	
		foreach($result=$query->result() as $p) format_product($p);
		return $result;
	}

	/**
	* 按照产品id获取关联的产品
	*/
	public function get_cache_link_product($product_id) 
	{		
		if (empty($product_id)) {
			return array();
		}
		
		$cached_link_key = 'link-product-'. $product_id;				
		$list = $this->cache->get($cached_link_key);

		if (empty($list)) {
			$list = $this->link_product($product_id);
			$this->cache->save($cached_link_key, $list, CACHE_TIME_RECOMMEND_PRO);			
		}
		if(count($list) >=6) {
			return $list;
		}
		$list2 = array();
		$left = 6 - count($list);
		$arr_exist_ids = array();
		foreach($list as $k => $v) {
			$arr_exist_ids[] = $v->product_id;	
		}
		if(empty($list) || $left > 0) {			
			$list2 = $this->get_link_product_by_rule($product_id, $left, $arr_exist_ids);
		}		
		if(count($list2) > $left) {
			$list2 = array_slice($list2, 0, $left );
		}
		$list = array_merge($list, $list2);
		return $list;
	}
	/**
	 * 按商品id获取默认的推荐商品 
	 * @param type $product_id
	 * @param int $start
	 * @param int $limit 
	 */
	function get_cache_recommend_pro($product_id, $start =  0, $limit = 5){
	    if(empty($product_id ) ){
		return array();
	    }
	    $recommend_key = "recommend-product-".$product_id."-".$start."-".$limit;	
	    if (($list = $this->cache->get($recommend_key )) === FALSE ) {
		$list = $this->get_recommend_pro($product_id, $start =  0, $limit = 5);
		$this->cache->save($recommend_key, $list, CACHE_TIME_RECOMMEND_PRO);
	    }
	    return $list;
	}
	
	/**
	 * 按商品id获取默认的推荐商品 
	 * @param type $product_id
	 * @param type $start
	 * @param type $limit
	 * @param type $slave
	 * @return type 
	 */
	function get_recommend_pro($product_id, $start =  0, $limit = 5, $slave = TRUE){
	    $this->adoEx= $slave ? $this->db_r:$this->db;
	    $sql = "SELECT
			p.product_id,
			p.product_name,
			g.img_170_170,
			p.market_price,
			p.shop_price,
			p.is_promote,
			p.promote_start_date,
			p.promote_end_date,
			p.promote_price,
			pb.brand_name,
			p.brand_name,
			p.subhead
			FROM ty_product_sub AS sub
			LEFT JOIN ty_product_info AS p
			    ON sub.product_id = p.product_id
			LEFT JOIN ty_product_gallery AS g
			    ON sub.product_id = g.product_id
			    AND sub.color_id = g.color_id
			    AND g.image_type = 'default'
			LEFT JOIN ty_product_brand  AS pb 
			    ON pb.brand_id = p.brand_id
			WHERE sub.is_on_sale = 1
			    AND (sub.consign_num =  - 2
				OR sub.consign_num > 0
				OR sub.gl_num > sub.wait_num)
			    AND p.category_id = (SELECT p1.category_id FROM ty_product_info p1 WHERE product_id = ?)
			    AND p.product_sex = (SELECT p1.product_sex FROM ty_product_info p1 WHERE product_id = ?)
			    AND p.product_id <> ?
				GROUP BY p.product_id
				ORDER BY p.sort_order
				LIMIT ?,?";
	    //前5个给history，后5个给buy_buy,末尾5个给link_product；
	    $query=$this->adoEx->query($sql, array($product_id, $product_id, $product_id, $start, $limit));
	    foreach($result=$query->result() as $p) format_product($p);
	    return $result;
	}

	public function all_nav($filter=array())
	{
		$query=$this->_db->get_where('front_nav',$filter);
		return $query->result();
	}

	public function filter_collect($filter)
	{
		$query = $this->_db->get_where('front_collect_product',$filter,1);
		return $query->row();
	}

	public function insert_collect($data)
	{
		$this->_db->insert('front_collect_product',$data);
		return $this->_db->insert_id();
	}

    /**
     * 获取热卖分类文件路径
     * @param int $cat_id
     * @param string $data_type
     * @return string
     */
    function get_cat_hot_goods_file($cat_id, $data_type = '')
    {
        $cat_id = strlen($cat_id) < 6 ? str_repeat('0', 6 - strlen($cat_id)) . $cat_id : $cat_id;

        $len = strlen($cat_id);
        $dir2 = substr($cat_id, -3);
        $dir1 = substr($cat_id, $len * (-1), $len - 3);
        $path = "/hot_goods_cat/" . (trim($data_type) !== '' ? "{$data_type}/" : '') . "{$dir1}";

        $file = $path . "/{$dir2}.inc";
        return $file;
    }
    
    function get_product_gallery($product_id, $color_id, $filed = '*', $slave = TRUE ){//img_318_318,img_418_418,img_85_85,img_760_760,img_850_850
	$this->excdb = $slave ? $this->db_r : $this->db;
	$where = !empty($product_id) &&  !empty($color_id) ? "AND pg.product_id = $product_id AND pg.color_id = $color_id" : "AND pg.product_id = $product_id ";
	$sql = "SELECT $filed FROM ty_product_gallery pg WHERE (pg.image_type ='default' OR pg.image_type = 'part') ".$where;
	return $this->excdb->qurey($sql )->result_array();
    }
    
    //获取商品详情页的面包屑导航
    function get_pro_url_map($from ) {
	$this->load->model("rush_model");
	$map = "&gt;<a href='/rushlist'>限时抢购</a>&gt;";//默认去首页
	if(!isset($from[1]) || empty($from[1])){
	    return $map;
	}
	if($from[0] == 1 ){//category
	    $type = $this->rush_model->get_select_type(array("type_id"=>$from[1] ));
	    $map = !empty($type)?"&nbsp;&gt;&nbsp;<a href='/category-".$type["type_id"].".html'>".$type["type_name"]."</a>&nbsp;&gt;":$map;
	}else{//rush
	    $rush = $this->rush_model->get_rush_by_id($from[1] );
	    $map = !empty($rush )?"&nbsp;&gt;&nbsp;<a href='/rushlist'>限时抢购</a>&nbsp;&gt;&nbsp;<a href='/rush-".$rush["rush_id"].".html'>".$rush['rush_index']."</a>&nbsp;&gt;":$map;
	}
	return $map;
    }
    
    function get_size_img($args, $slave = TRUE ){
	$this->adoEx = $slave ? $this->db_r :$this->db;
	$sql = "SELECT image_url FROM ty_product_size_image WHERE 1 ";
	$sql_1 = $sql ;
	$param = array();
	if (isset($args['brand_id']) ){
	    $sql .= "AND brand_id = ? ";
	    $sql_1 .= "AND brand_id = ? ";
	    $param['brand_id'] = $args['brand_id'];
	}
	if (isset($args['category_id']) ){
	    $sql .= "AND category_id = ? ";
	    $sql_1 .= "AND category_id = (SELECT c.parent_id FROM ty_product_category c WHERE c.category_id = ? )";
	    $param['category_id'] = $args['category_id'];
	}
	if (isset($args['sex']) ){
	    $sql .= "AND sex = ? ";
	    $sql_1 .= "AND sex = ? ";
	    $param['sex'] = $args['sex'];
	}
	$sql .=  "limit 1";
	$row = $this->adoEx->query($sql,$param )->row();
	if(isset($row->image_url) ){
	   return  $row->image_url;
	}else{
	    $sql_1 .=  "limit 1";
	    $row = $this->adoEx->query($sql_1,$param )->row();
	    return isset($row->image_url) ? $row->image_url :"";
	}
    }
    
    /**
     * 所有供应商
     * @param array $filter 过滤条件
     */
    public function all_provider($filter)
    {
        $query = $this->_db->get_where('product_provider', $filter);
        return $query->result();
    }
    
    /**
     * 供应商区域运费
     * @param type $provider_id
     */
    public function get_provider_shipping_fee_config($provider_id)
    {
        $CI = &get_instance();
        $key = 'provider_shipping_fee_config_'.$provider_id;
        $cache_config = $CI->cache->get($key);
        if(FALSE && $cache_config) {
            return $cache_config;
        }
        
        $config = array();
        $this->db_r->select('shipping_fee_config');
        $query = $this->db_r->get_where('product_provider', array('provider_id'=>$provider_id));
        $row = $query->row();        
        if($row && $row->shipping_fee_config){
            foreach(json_decode($row->shipping_fee_config, true) as $val){
                $config[intval($val['regionId'])] = array(floatval($val['fee']), floatval($val['price']));
            }
        }
        $CI->config->load('provider');
        $config += $CI->config->item('provider_shipping_config');
        $CI->cache->save($key, $config, CACHE_TIME_PROVIDER_SHIPPING_FEE_CONFIG);
        return $config;
    }
    
    public function get_provider_brand_link()
    {
        $this->db->select('provider_id, brand_id');
        $query = $this->db->get('provider_brand');
        return $query->result();
    }
    public function get_course_list($page, $expire = false, $cid = 0){
        if ($expire){
            $condition = 'UNIX_TIMESTAMP(p.`package_name`) < UNIX_TIMESTAMP(NOW()) ';
        } else {
            $condition = 'UNIX_TIMESTAMP(p.`package_name`) >= UNIX_TIMESTAMP(NOW()) ';
        }
        if (0 != $cid) {
            $condition .= "AND p.category_id=$cid ";
        }
        $sql = "SELECT COUNT(1) total FROM (SELECT p.product_id FROM ". 
            $this->_db->dbprefix('product_sub')." sub ". 
            "INNER JOIN ".$this->_db->dbprefix('product_info').' p ON sub.product_id=p.product_id  
            WHERE p.genre_id = 2 AND sub.is_on_sale=1 AND '.$condition. 
            'AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num) 
            AND p.is_audit=1 GROUP BY p.product_id) a';

        $row = $this->db_r->query($sql)->row();
        $record_count = $row->total;
        if (0 == $record_count)
            return false;
        $page_size = M_LIST_PAGE_SIZE;
        $page_cnt = ceil($record_count/$page_size);
        if ($page>$page_cnt){
            return false;
        }
        $page = ($page < 1) ? 1 : intval($page);
        $start = ($page-1)*$page_size;

        $sql = 'SELECT UNIX_TIMESTAMP(p.`package_name`) - UNIX_TIMESTAMP(NOW()) new_time, p.product_id, p.product_desc_additional, p.product_name, p.package_name, p.subhead, p.ps_num, p.shop_price, pg.`img_url` FROM '.$this->_db->dbprefix('product_sub').' sub INNER JOIN '.
            $this->_db->dbprefix('product_info').' p ON sub.product_id=p.product_id '.
            'INNER JOIN ty_product_gallery pg ON pg.product_id = sub.product_id AND pg.color_id = sub.color_id AND image_type = \'default\' '.
            'WHERE p.genre_id = 2 AND sub.is_on_sale=1 AND '.$condition. 
            'AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num) 
            AND p.is_audit=1 GROUP BY p.product_id ORDER BY new_time ASC,p.package_name DESC LIMIT '.$start.", ".$page_size;
        $query=$this->db_r->query($sql);
        $result=$query->result_array();
        $course_list = $ids = array();
        foreach($result as $r){
            $id = $r['product_id'];
            unset($r['product_id']);
            $course_list[$id] = $r;
            array_push($ids, $id);
        }

        if (!empty($ids)){
        $ids = implode(',', $ids);
        $sql = "SELECT COUNT(1) total,product_id FROM ty_front_collect_product WHERE product_id IN ($ids) GROUP BY product_id";
        $result=$this->db_r->query($sql)->result_array();
        foreach($result as $r){
            $id = $r['product_id'];
            unset($r['product_id']);
            $course_list[$id]['total'] = $r['total'];
        }
        }
        return $course_list;
    }
    
    public function get_product_onsale($genre_id, $page){
        $result = array();
        $sql = "SELECT COUNT(1) AS cnt FROM (SELECT p.*, pg.img_url ". 
               "FROM ".$this->_db->dbprefix('product_sub')." AS sub ". 
               "INNER JOIN ".$this->_db->dbprefix('product_info')." AS p ON sub.product_id=p.product_id 
               INNER JOIN ty_product_gallery pg ON pg.product_id = sub.product_id AND pg.color_id = sub.color_id AND image_type = 'default' 
               WHERE p.is_audit=1 AND sub.is_on_sale=1 
               AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num) 
               AND p.genre_id = ".$genre_id." GROUP BY p.product_id) a";
        $row = $this->db_r->query($sql)->row();
        if (empty($row)) {
            return $result;
        }
        $record_count = $row->cnt;
        $page_size = M_LIST_PAGE_SIZE;
        $page_cnt = ceil($record_count/$page_size);
        $page = ($page < 1) ? 1 : intval($page);
        $start = ($page-1)*$page_size;
	$order_by = '';
	// 课程按照开始时间 小的在前
	if( $genre_id == PRODUCT_COURSE_TYPE){
		$order_by =' ORDER BY p.package_name DESC';
	}
        $sql = "SELECT p.*, pg.img_url ". 
               "FROM ".$this->_db->dbprefix('product_sub')." AS sub ". 
               "INNER JOIN ".$this->_db->dbprefix('product_info')." AS p ON sub.product_id=p.product_id 
               INNER JOIN ty_product_gallery pg ON pg.product_id = sub.product_id AND pg.color_id = sub.color_id AND image_type = 'default' 
               WHERE p.is_audit=1 AND sub.is_on_sale=1 
               AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num) 
               AND p.genre_id = ".$genre_id." GROUP BY p.product_id ".$order_by." LIMIT ".$start.", ".$page_size;
        $query=$this->db_r->query($sql);
        $result=$query->result_array();
        /*foreach ($product as $row) {
            $result[$row['product_id']] = $row;
        }
	*/
        
        /*$product_ids = array_keys($result);
        if( empty($product_ids) ) return FALSE;
        $sql = "SELECT product_id, img_url FROM ty_product_gallery WHERE product_id IN (".implode(",", $product_ids).") AND image_type = 'default' GROUP BY product_id";
        $query=$this->db_r->query($sql);
        $gallery=$query->result();
        foreach ($gallery as $g) {
            $result[$g->product_id]['img_url'] = $g->img_url;
        }*/
        return $result;
    }

    //获取大类
    public function get_goods_genre($genre_id){
        $sql = "SELECT * FROM `ty_product_genre` WHERE id = ".$genre_id;
        $result = $this->db_r->query($sql)->row_array();
        if (!empty($result['product_name_map'])){
            $result['product_map'] = json_decode($result['product_name_map'], true);
        }
        if (!empty($result['client_info_map'])){
            $result['client_map'] = json_decode($result['client_info_map'], true);
        }
        return $result;
    }

    //获取 指定 条件的 产品信息
    public function get_product_list($brand_id){
    	$sql = "SELECT pi.*,pg.img_url 
				FROM ".$this->_db->dbprefix('product_info')." AS pi 
				LEFT JOIN ".$this->_db->dbprefix('product_gallery')." AS pg ON pi.product_id=pg.product_id
				WHERE pi.brand_id=?";
		$query = $this->_db->query($sql,array($brand_id));
        return $query->result();
    }

    //获取产品的注册号
    public function get_product_additional($product_id) {
    	$sql = "select p.`register_code_id`, r.* from ty_product_info p left join ya_register_code r on p.register_code_id = r.id where p.product_id = " . $product_id . " limit 1";
    	$query = $this->_db->query($sql);
    	$result = $query->result_array();
    	$query->free_result();
    	return $result;
    }

    //获取特卖商品列表
    public function get_products_temai($genre_id, $page){
        $result = array();
        $sql = "SELECT COUNT(1) AS cnt FROM (SELECT p.*, pg.img_url ". 
               "FROM ".$this->_db->dbprefix('product_sub')." AS sub ". 
               "INNER JOIN ".$this->_db->dbprefix('product_info')." AS p ON sub.product_id=p.product_id 
               INNER JOIN ty_product_gallery pg ON pg.product_id = sub.product_id AND pg.color_id = sub.color_id AND image_type = 'default' 
               WHERE p.is_audit=1 AND sub.is_on_sale=1 AND p.is_promote = 1 AND promote_start_date <= now() AND promote_end_date >= now()
               AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num) 
               AND p.genre_id = ".$genre_id." GROUP BY p.product_id) a";
        $row = $this->db_r->query($sql)->row();
        if (empty($row)) {
            return $result;
        }
        $record_count = $row->cnt;
        $page_size = M_LIST_PAGE_SIZE;

        $page_cnt = ceil($record_count/$page_size);
        $page = ($page < 1) ? 1 : intval($page);
        $start = ($page-1)*$page_size;
        $sql = "SELECT p.*, pg.img_url ". 
               "FROM ".$this->_db->dbprefix('product_sub')." AS sub ". 
               "INNER JOIN ".$this->_db->dbprefix('product_info')." AS p ON sub.product_id=p.product_id 
               INNER JOIN ty_product_gallery pg ON pg.product_id = sub.product_id AND pg.color_id = sub.color_id AND image_type = 'default' 
               WHERE p.is_audit=1 AND sub.is_on_sale=1 AND p.is_promote = 1 AND promote_start_date <= now() AND promote_end_date >= now()
               AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num) 
               AND p.genre_id = ".$genre_id." GROUP BY p.product_id order by promote_start_date desc LIMIT ".$start.", ".$page_size;
        $query=$this->db_r->query($sql);

        $result=$query->result_array();
        /*foreach ($product as $row) {
            $result[$row['product_id']] = $row;
        }
        
        /*$product_ids = array_keys($result);
        if( empty($product_ids) ) return FALSE;
        $sql = "SELECT product_id, img_url FROM ty_product_gallery WHERE product_id IN (".implode(",", $product_ids).") AND image_type = 'default' GROUP BY product_id";
        $query=$this->db_r->query($sql);
        $gallery=$query->result();
        foreach ($gallery as $g) {
            $result[$g->product_id]['img_url'] = $g->img_url;
        }*/
        return $result;
    }

    //取得产品的详情
    //$genre_id = 1表示产品
    //$genre_id = 2表示课程
    public function get_product_detail($genre_id = 1) {
    	$sql = 'SELECT product_id, product_desc_detail from ty_product_info where `genre_id` = ' . $genre_id . ' and product_id in (57,969,1322,2784,4239)';
    	$query = $this->db_r->query($sql);    	
    	$result = $query->result_array();
    	return $result;
    }
    public function get_product_detail2($genre_id = 1) {
    	$sql = 'SELECT product_id, detail1 from ty_product_info where `genre_id` = ' . $genre_id . ' and product_id in (57,969,1322,2784,2813,2815,2816,2817,2818,2819,2820,2821,2822,2823,2824,2825,2826,2827,2828,2829,2830,2831,2832,2833,2834,2835,2836,2837,2838,2839,2840,2841,2842,2843,2844,2845,2846,2847,2848,2849,2851,2852,2853,2854,2857,2858,2859,2860,2862,2863,2865,2867,2868,2869,2870,2871,2875,2876,2877,2879,2880,2881,2882,2883,2884,2886,2887,2888,2889,2890,2891,2892,2893,4098,4099,4106,4109,4111,4112,4114,4115,4116,4117,4119,4121,4122,4123,4126,4129,4131,4132,4133,4135,4137,4138,4139,4140,4141,4143,4144,4145,4146,4147,4148,4149,4158,4159,4161,4163,4164,4165,4166,4167,4168,4169,4170,4171,4172,4173,4174,4175,4176,4178,4179,4180,4181,4185,4187,4188,4189,4190,4191,4192,4193,4194,4195,4196,4197,4199,4200,4201,4202,4203,4204,4205,4206,4207,4208,4209,4210,4211,4213,4215,4216,4217,4218,4219,4220,4221,4222,4223,4224,4225,4227,4228,4229,4230,4231,4232,4233,4234,4236,4237,4238,4239,4242,4243,4244,4245,4247,4248,4249,4250,4251,4252,4253,4254,4255,4256,4257,4259,4260,4261,4262,4264,4265,4266,4268,4269,4270,4271,4275,4281,4282,42)';
    	$query = $this->db_r->query($sql);    		
    	$result = $query->result_array();
    	var_export($result);
    	return $result;
    }
    public function update_product_detail($product_id, $update) {
    	// $sql = 'UPDATE ty_product_info set detail1 = "' .htmlspecialchars($prouct_detail_desc) . '" where product_id = ' . $product_id;
    	// $this->db->query($sql);
    	$this->db->update('product_info', $update, array('product_id'=>$product_id));
    }

    public function get_pc_index_product_info($product_id) {
    	$sql="SELECT
				p.*, p.is_best AS is_zhanpin,
				f.flag_name,
				f.flag_url,
				b.brand_name,
				b.brand_info,
				b.brand_story,
				b.logo_160_73,
				b.brand_story,
				pp.display_name,
				pp.logo,
				pp.product_num,
				pp.provider_cooperation,
			g.img_url
			FROM
				 ty_product_info AS p
			LEFT JOIN ty_product_flag AS f ON p.flag_id = f.flag_id
			LEFT JOIN ty_product_brand AS b ON p.brand_id = b.brand_id
			LEFT JOIN ty_product_provider AS pp ON p.provider_id = pp.provider_id
			LEFT JOIN ty_product_gallery as g on p.product_id = g.product_id and image_type = 'default'
			WHERE
				p.product_id =  ?";
    	$query=$this->_db->query($sql, array($product_id));		
    	foreach($result=$query->result() as $p) format_product($p);
    	return $result;
    }

    public function get_product_collect($product_id) {
    	$sql = "select count(product_id) as collect_num from ty_front_collect_product 

			where product_id = ?
			group by product_id";
		
		$query = $this->_db->query($sql, array($product_id));
		$result = $query->result_array();
		
		if (count($result)) {
			return $result[0]['collect_num'];
		} else {
			return 0;
		}

    }

    //PC 分词搜索的产品
    public function get_search_product($ids){
    	$sql = "SELECT pif.`product_id`,pif.`product_name`,pif.`market_price`,pif.`shop_price`,pgy.`img_url`,pze.`size_name`,pif.`subhead`,pif.`package_name` "
                . "FROM ".$this->_db->dbprefix('product_info')." AS pif "
                . "LEFT JOIN ".$this->_db->dbprefix('product_sub')." AS psb ON psb.`product_id` = pif.`product_id`	"
                . "LEFT JOIN ".$this->_db->dbprefix('product_size')." AS pze ON pze.`size_id` = psb.`size_id` "
                . "LEFT JOIN ".$this->_db->dbprefix('product_gallery')." AS pgy ON pgy.`product_id` = pif.`product_id` "
                . "WHERE pif.product_id in (".$ids.") AND pif.source_id IN (0, '".SOURCE_ID_WEB."')  LIMIT 8 ";
    	$query=$this->_db->query($sql);		
    	$result=$query->result();
    	return $result;
    }

    public function get_words_from_sphinx($keywords = array()) {
    	if (empty($keywords)) {
    		return false;
    	}
    	$sql = "select name from ty_sphinx_word where ";

    	foreach ($keywords as $key => $value) {
    		if ($key == 0) {
    			$sql .= '';
    		} else {
    			$sql .= ' or ';
    		}
    		$sql .= " name like '%" . $value . "%' ";
    	}
		$query =  $this->db_r->query($sql);
		$result = $query->result_array();
		
		return $result;
    }
    //获取关联商品
    public function get_relation_product($category_id_arr){
        $sql = "SELECT p.*, pg.`img_url` FROM ty_product_info p 
            LEFT JOIN ty_product_sub ps ON p.product_id = ps.`product_id` 
            LEFT JOIN `ty_product_gallery` pg ON p.product_id = pg.product_id AND ps.color_id = pg.`color_id` AND pg.`image_type` = 'default' 
            WHERE p.is_audit=1 AND ps.is_on_sale = 1 AND (ps.consign_num =  - 2 OR ps.consign_num > 0 OR ps.gl_num > ps.wait_num) AND p.category_id IN (".implode(",", $category_id_arr).") 
            GROUP BY p.product_id LIMIT 5";
        $query =  $this->db_r->query($sql);
        $result = $query->result();
        return $result;
    }
    
    //获取热门商品
    public function get_hot_product($is_hot=0, $is_new=0){
        $sql = "SELECT p.*, pg.`img_url` FROM ty_product_info p 
            LEFT JOIN ty_product_sub ps ON p.product_id = ps.`product_id` 
            LEFT JOIN `ty_product_gallery` pg ON p.product_id = pg.product_id AND ps.color_id = pg.`color_id` AND pg.`image_type` = 'default' 
            WHERE p.is_audit=1 AND ps.is_on_sale = 1 AND (ps.consign_num =  - 2 OR ps.consign_num > 0 OR ps.gl_num > ps.wait_num)";
        if ($is_hot > 0) $sql .= " AND p.is_hot = 1";
        if ($is_new > 0) $sql .= " AND p.is_new = 1";
        $sql .= " LIMIT 5";
        $query =  $this->db_r->query($sql);
        $result = $query->result();
        return $result;
    }

    public function get_course_by_time($start, $end, $type) {

    	/*$sql = "SELECT p.product_name, p.product_id, unix_timestamp(p.package_name) as course_start_date, p.product_desc_additional,p.subhead
               FROM ty_product_sub AS sub
               INNER JOIN ty_product_info AS p ON sub.product_id=p.product_id 
               
               WHERE p.is_audit=1 AND sub.is_on_sale=1 
               AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num) 
               AND p.genre_id = 2 
			   AND unix_timestamp(p.package_name) >= unix_timestamp('" . $start . "') " . 
			   " AND unix_timestamp(p.package_name) <= unix_timestamp('" . $end . "')" . 
			   " GROUP BY p.product_id";*/

		$sql = "SELECT p.product_name, p.product_id, unix_timestamp(p.package_name) as course_start_date, p.product_desc_additional,p.subhead, p.package_name
		              from ty_product_info p
		               
		               WHERE p.is_audit=1 
		               AND p.genre_id = 2 
		               
					   AND unix_timestamp(p.package_name) >= unix_timestamp('" . $start . "') " . 
					   " AND unix_timestamp(p.package_name) <= unix_timestamp('" . $end . "')" . 
					   " GROUP BY p.product_id";
		//var_export($sql);exit();
	    $query = $this->db_r->query($sql);
	    $result = $query->result_array();
	    return $result;

    }

    public function get_related_courses($product_id) {
    	//规则：获取当前日期之后的课程
    	$sql = "SELECT
					p.*, g.img_url
				FROM
					ty_product_sub AS sub
				JOIN ty_product_info AS p ON sub.product_id = p.product_id
				join ty_product_gallery as g on sub.product_id = g.product_id
				WHERE
					p.genre_id = 2
				AND sub.is_on_sale = 1
				and g.image_type = 'default' 
				AND unix_timestamp(package_name) >= " . time() .
				" and sub.product_id !=  " . $product_id . 
				" ORDER BY
					package_name DESC
				LIMIT 3";
    	$query = $this->db_r->query($sql);
    	$result = $query->result();    	
    	return $result;
    }
}
