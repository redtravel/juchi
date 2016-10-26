<?php

/**
* Cart_model
*/
class Cart_model extends CI_Model
{

	private $_db;
	public function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	public function filter ($filter)
	{
		$query = $this->_db->get_where('front_cart',$filter,1);
		return $query->row();
	}

	// 购物车多条查询
	public function all_cart($filter)
	{
		$CI = &get_instance();
		$filter['update_date >='] = date_change($CI->time,'-'.CART_SAVE_TIME);
		$query = $this->_db->get_where('front_cart',$filter);
		return $query->result();
	}
        
        /**
         * 购物车商品明细
         * @param type $cart_sn
         * @param type $gallery
         * @param type $package_product
         * @return type
         */
	public function cart_info ($cart_sn,$gallery=FALSE,$package_product=FALSE, $provider_id=0)
	{
		$CI = &get_instance();
		$select = "select ct.*,
            p.brand_id,b.brand_name,p.category_id,p.product_sn,p.product_name,
             p.is_promote,p.promote_start_date,p.promote_end_date,p.promote_price,
			p.market_price, p.limit_num, p.limit_day, c.color_name,s.size_name,
            p.shop_id AS provider_id, p.product_weight, pp.display_name as provider_name, pp.shipping_fee_config";
		$from = " from ".$this->_db->dbprefix('front_cart')." as ct
			left join ".$this->_db->dbprefix('product_info')." as p on ct.product_id = p.product_id
			left join ".$this->_db->dbprefix('product_provider')." as pp on p.provider_id = pp.provider_id
			left join ".$this->_db->dbprefix('product_color')." as c on ct.color_id = c.color_id
			left join ".$this->_db->dbprefix('product_size')." as s on ct.size_id = s.size_id 
			left join ".$this->_db->dbprefix('product_brand')." as b on p.brand_id = b.brand_id ";
		if ($gallery) {
			$select .=",c.color_name,s.size_name 
                                   , g.img_318_318, g.img_418_418, g.img_url";
			$from .= "left join ".$this->_db->dbprefix('product_gallery')." as g on g.product_id = ct.product_id and g.color_id = ct.color_id and g.image_type = 'default' ";
		}
		if ($package_product) {
			$select .=",pkg.market_price as pkg_market_price, pkg.shop_price as pkg_shop_price, pkg.cost_price as pkg_cost_price, pkg.consign_price as pkg_consign_price, pkg.consign_rate as pkg_consign_rate";
			$from .= "left join ".$this->_db->dbprefix('package_area_product')." as pkg on ct.package_id = pkg.package_id and ct.product_id = pkg.product_id";
		}
                $where = '';
                if ($provider_id){
                    $where .= " AND p.shop_id = ".$provider_id;
                }
		$sql = $select.$from."where  ct.session_id = ? ".$where. " ORDER BY ct.create_date DESC ";
		$query = $this->_db->query($sql,array($cart_sn));
		return $query->result();
	}
	//购物车中占有的商品库存
	public function sub_num_in_cart($sub_id)
	{
		$CI = &get_instance();
		$sql = "SELECT sub_id,SUM(product_num) AS product_num
				FROM ".$this->_db->dbprefix('front_cart')."
				WHERE sub_id ".(is_array($sub_id)? db_create_in($sub_id):"={$sub_id}")." "
				.(is_array($sub_id)?'GROUP BY sub_id': '');
		$query = $this->_db->query($sql);
		return is_array($sub_id)?$query->result():$query->row();
	}
	public function insert($update)
	{
		$this->_db->insert('front_cart',$update);
		return $this->_db->insert_id();
	}
	// 更新购物车记录(单条)
	public function update($update,$rec_id)
	{
		$this->_db->update('front_cart',$update,array('rec_id'=>$rec_id));
	}
	// 批量更新购物车
	public function update_batch($update,$filter)
	{
		$this->_db->update('front_cart',$update, $filter);
	}
	// 刷新购物车过期时间
	public function refresh_cart($session_id)
	{
		$CI = &get_instance();
		$filter = array('session_id' =>$session_id,'update_date >=' => date_change($CI->time,'-'.CART_SAVE_TIME));
		$this->_db->update('front_cart',array('update_date'=>$CI->time),$filter);
	}

	public function delete ($rec_id)
	{
		$this->_db->delete('front_cart',array('rec_id'=>$rec_id));
	}

	public function delete_where ($filter)
	{
		$this->_db->delete('front_cart',$filter);
	}

	public function last_order ($user_id)
	{
		$sql = "SELECT * FROM ".$this->_db->dbprefix('order_info')." WHERE user_id =? AND order_status in (0,1) ORDER BY order_id DESC LIMIT 1";
		$query = $this->_db->query($sql, array(intval($user_id)));
		return $query->row();
	}

	public function available_pay_list()
	{
		$sql = "SELECT DISTINCT p.*
			FROM ".$this->_db->dbprefix('order_routing')." AS r
			LEFT JOIN ".$this->_db->dbprefix('payment_info')." AS p ON r.pay_id=p.pay_id
			WHERE r.source_id = ? AND r.show_type!=4 AND p.is_discount=0 AND p.enabled = 1 AND p.is_online = 1 ORDER BY p.sort_order DESC";
		$query = $this->_db->query($sql,array(SOURCE_ID_WEB));
		return $query->result();
	}

	public function cart_region($filter)
	{
		$country_id = !empty($filter['country'])?intval($filter['country']):1;
		$province_id = !empty($filter['province'])?intval($filter['province']):-1;
		$city_id = !empty($filter['city'])?intval($filter['city']):-1;
		$province_list = $city_list = $district_list = array();
		$sql = "SELECT region_id, parent_id, region_name FROM ".$this->_db->dbprefix('region_info')."
				WHERE parent_id".db_create_in(array($country_id,$province_id,$city_id));

		$query = $this->_db->query($sql);
		foreach ($query->result() as $region) {
			if(!$region->parent_id) continue;
			switch ($region->parent_id) {
				case $country_id:
					$province_list[] = $region;
					break;
				case $province_id:
					$city_list[] = $region;
					break;
				case $city_id:
					$district_list[] = $region;
					break;
			}
		}
		return array($province_list,$city_list,$district_list);
	}

	public function available_voucher_list($user_id)
	{
		$CI = &get_instance();
		$sql = "SELECT v.*,r.voucher_name,c.product,c.brand,c.category,c.provider
			FROM ".$this->_db->dbprefix('voucher_record')." AS v
			LEFT JOIN ".$this->_db->dbprefix('voucher_release')." AS r ON v.release_id = r.release_id
			LEFT JOIN ".$this->_db->dbprefix('voucher_campaign')." AS c ON v.campaign_id = c.campaign_id
			WHERE v.user_id=? AND v.start_date <=? AND v.end_date>=?
			AND v.used_number<v.repeat_number ORDER BY v.create_date DESC";
		$query = $this->_db->query($sql,array($user_id,$CI->time,$CI->time));
		return $query->result();
	}

	public function lock_voucher($voucher_sn)
	{
                if(is_array($voucher_sn)){
                    $sql = "SELECT * FROM ".$this->_db->dbprefix('voucher_record')." WHERE voucher_sn ".  db_create_in($voucher_sn)." FOR UPDATE";
                    $query = $this->_db->query($sql);
                    return $query->result();
                }else{
                    $sql = "SELECT * FROM ".$this->_db->dbprefix('voucher_record')." WHERE voucher_sn=? FOR UPDATE";
                    $query = $this->_db->query($sql,array($voucher_sn));
                    return $query->row();
                }
		
	}

	public function voucher_info($voucher_sn)
	{
		$CI = &get_instance();
		$sql = "SELECT v.*,c.product,c.brand,c.category,c.provider,c.campaign_type
			FROM ".$this->_db->dbprefix('voucher_record')." AS v
			LEFT JOIN ".$this->_db->dbprefix('voucher_campaign')." AS c ON v.campaign_id = c.campaign_id
			WHERE voucher_sn =? AND v.start_date <=? AND v.end_date>=? AND v.used_number<v.repeat_number LIMIT 1";

		$query = $this->_db->query($sql,array($voucher_sn,$CI->time,$CI->time));
		return $query->row();
	}

	public function update_voucher($update,$voucher_sn)
	{
		$this->_db->update('voucher_record',$update,array('voucher_sn'=>$voucher_sn));
	}

	public function available_shipping_list($filter)
	{
		$sql = "SELECT DISTINCT s.*
                FROM ".$this->_db->dbprefix('shipping_info')." AS s
                LEFT JOIN ".$this->_db->dbprefix('shipping_area')." AS a ON a.shipping_id = s.shipping_id
                LEFT JOIN ".$this->_db->dbprefix('shipping_area_region')." AS r ON r.shipping_area_id = a.shipping_area_id
                WHERE s.is_use=1 ";
        $param = array();
        if (isset($filter['region_ids']) && is_array($filter['region_ids'])) {
            $sql .= " AND r.region_id ".db_create_in($filter['region_ids'])." ";
        }
        if (isset($filter['pay_id']) && $filter['pay_id']==PAY_ID_COD) {
            $sql .= " AND a.is_cod = 1 ";
        }
        $sql .= " AND s.shipping_id IN (SELECT shipping_id FROM ".$this->_db->dbprefix('order_routing')." WHERE show_type!=4 AND source_id = ? ";
        $param[] = SOURCE_ID_WEB;
        if (!empty($filter['pay_id'])) {
            $sql .= " AND pay_id = ? ";
            $param[] = intval($filter['pay_id']);
        }
        $sql .= ") ORDER BY s.sort_order DESC";
        $query = $this->_db->query($sql, $param);
        return $query->result();
	}

	public function insert_order($update)
	{
		$this->_db->insert('order_info',$update);
		return $this->_db->insert_id();
	}

	public function insert_product($update)
	{
		$this->_db->insert('order_product',$update);
		return $this->_db->insert_id();
	}

	public function update_product($update,$op_id)
	{
		$this->_db->update('order_product',$update,array('op_id'=>$op_id));
	}

	public function assign_trans($order,$op,$shop_price=NULL)
    {
        if($op['product_num']<=$op['consign_num']) return TRUE;
        $num = $op['product_num'] - $op['consign_num'];
        //取trans数据
        $sql = "SELECT t.depot_id,t.location_id,SUM(t.product_number) AS product_number, t.batch_id, t.consign_price, t.cost_price, t.consign_rate, t.product_cess, t.shop_price
                FROM ".$this->_db->dbprefix('transaction_info')." AS t
                LEFT JOIN ".$this->_db->dbprefix('depot_info')." AS d ON t.depot_id=d.depot_id
                LEFT JOIN ".$this->_db->dbprefix('location_info')." AS l ON t.location_id=l.location_id
                WHERE d.is_use = 1 AND d.is_return = 0 AND l.is_use = 1 AND t.trans_status IN (1,2,4)
                AND t.product_id=? AND t.color_id = ? AND t.size_id = ?
                GROUP BY l.location_id, t.batch_id HAVING product_number>0 ORDER BY MIN(expire_date) ASC,min(t.batch_id) ASC, d.depot_priority;";
        $query = $this->_db->query($sql, array($op['product_id'],$op['color_id'],$op['size_id']));
        $trans = $query->result();
        if(!$trans) return FALSE;
        //分配储位
        $result = array();
        $row = array(
            'trans_type'=>TRANS_TYPE_SALE_ORDER,
            'trans_status'=>TRANS_STAT_AWAIT_OUT,
            'trans_sn'=>"'{$order['order_sn']}'",
            'product_id'=>$op['product_id'],
            'color_id'=>$op['color_id'],
            'size_id'=>$op['size_id'],
            'sub_id'=>$op['op_id'],
            'create_admin'=>0,
            'create_date'=>"'{$this->time}'",
            'trans_direction'=>0
        );
        foreach($trans as $t){
            $row['depot_id'] = $t->depot_id;
            $row['location_id'] = $t->location_id;
            $row['product_number'] = min($t->product_number,$num)*-1;
            $row['batch_id'] = "'" . $t->batch_id . "'";
            $row['consign_price'] = "'" . $t->consign_price . "'";
            $row['cost_price'] = "'" . $t->cost_price . "'";
            $row['consign_rate'] = "'" . $t->consign_rate . "'";
            $row['shop_price'] = "'" . (NULL === $shop_price ? $t->shop_price : $shop_price) . "'";
            $row['product_cess'] = "'" . $t->product_cess . "'";
            $result[] = $row;
            $num += $row['product_number']; //因为$row['product_number']为负值，所以此处用+
            if($num==0) break;
        }
        if($num) return FALSE;
        //插入储位
        $keys = array('trans_type','trans_status','trans_sn','product_id','color_id','size_id','sub_id','create_admin','create_date','trans_direction','depot_id','location_id','product_number', 'batch_id', 'shop_price','consign_price', 'cost_price', 'consign_rate', 'product_cess');
        $sql = "INSERT INTO ".$this->_db->dbprefix('transaction_info')."
        (".implode(',',$keys).") VALUES ";
        $update = array();
        foreach ($result as $v) {
            $row = array();
            foreach($keys as $key) $row[$key] = $v[$key];
            $update[] = '('.implode(',',$row).')';
        }
        $sql .= implode(',',$update);
        $this->_db->query($sql);
        return TRUE;
    }

    public function insert_payment($update)
    {
    	$this->_db->insert('order_payment',$update);
    	return $this->_db->insert_id();
    }

    public function available_gifts($price)
    {
    	$sql="SELECT g.product_id, p.market_price,p.shop_price 
    		FROM  ".$this->_db->dbprefix('front_campaign')." AS g
    		LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON g.product_id=p.product_id
    		LEFT JOIN ".$this->_db->dbprefix('product_provider')." AS s ON s.provider_id=p.provider_id
			WHERE g.is_use=1 AND g.start_date<=? AND g.end_date>=? 
			AND g.limit_price<=?";
		$query=$this->_db->query($sql,array($this->time,$this->time,$price));
		return $query->result();
    }

    //实际取的是关联商品的数据
    public function buy_buy($product_ids)
    {
    	$sql="SELECT sub.product_id,sub.color_id,p.product_name,p.product_sn,b.brand_name,
			g.img_318_318 as img_170_227,p.market_price,p.shop_price,p.promote_price,p.is_promote,p.promote_start_date,p.promote_end_date
                        , g.img_318_318, g.img_418_418, g.img_85_85, g.img_760_760, g.img_850_850, g.img_215_215, g.img_58_58, g.img_48_48, g.img_40_40, g.img_40_40 AS img_30_40, g.img_85_85 AS img_63_84
			FROM ty_product_sub AS sub
			LEFT JOIN ty_product_info AS p ON sub.product_id=p.product_id
			LEFT JOIN ty_product_brand AS b ON p.brand_id=b.brand_id
			LEFT JOIN ty_product_gallery AS g ON sub.product_id=g.product_id AND sub.color_id=g.color_id AND g.image_type='default'
			WHERE sub.is_on_sale=1 AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num)
			AND sub.product_id IN (
			SELECT l1.link_product_id FROM ty_product_link AS l1 WHERE l1.product_id ".db_create_in($product_ids)."
			UNION
			SELECT l2.product_id FROM ty_product_link AS l2 WHERE l2.link_product_id ".db_create_in($product_ids)." AND l2.is_bothway=1 
			)
			GROUP BY p.product_id
			ORDER BY p.sort_order LIMIT 8";
		$query=$this->_db->query($sql);
		return $query->result();
    }

    public function all_gifts($filter)
    {
    	$query=$this->_db->get_where('front_campaign',$filter);
    	return $query->result();
    }
    // 得到购物车中某条记录信息
    public function get_cart_goods_item($rec_id){
        $sql = "SELECT 
  g.product_id,
  g.product_name,
  fc.*,
  s.size_name, 
  pg.img_url 
FROM
  `ty_front_cart` fc 
  LEFT JOIN ty_product_info g 
    ON fc.product_id = g.product_id 
  LEFT JOIN `ty_product_gallery` pg 
    ON fc.product_id = pg.product_id 
    AND fc.color_id = pg.color_id 
    AND pg.image_type = 'default' 
  LEFT JOIN `ty_product_size` s 
    ON fc.size_id = s.size_id 
WHERE fc.rec_id = ".$rec_id;
        return $this->_db->query($sql)->row();
    }
    //获取所有的快递公司
    public function get_shipping_list(){
        $sql = "SELECT shipping_id, shipping_name FROM `ty_shipping_info` WHERE is_use = 1 ORDER BY sort_order ASC";
        return $this->_db->query($sql)->result();
    }
    
    //获取指定快递公司
    public function filter_shipping($filter){
        $query = $this->_db->get_where('ty_shipping_info',$filter,1);
        return $query->row();
    } 
    
    //获取用户的发票信息
    public function get_user_invoice_list($user_id){
        $sql = "SELECT title FROM `ty_user_invoice` WHERE user_id = ".$user_id." ORDER BY add_date DESC";
        return $this->_db->query($sql)->result();
    }  
    //添加用户的发票信息
    public function user_invoice_add($user_id, $content){
        $sql = "SELECT COUNT(1) AS cnt FROM `ty_user_invoice` WHERE user_id = ".$user_id." AND title = '".$content."'";
        $count = $this->_db->query($sql)->row();
        if ($count->cnt) {
            $this->_db->update('ty_user_invoice',array('add_date' => date('Y-m-d H:i:s')), array('user_id' => $user_id));
            return 0;
        }

        $data = array('title' => $content, 'user_id' => $user_id, 'add_date' => date('Y-m-d H:i:s'));
        $this->_db->insert('ty_user_invoice',$data);
	return $this->_db->insert_id();
    }

    //免邮商品
 	public function campaign_product($product_id_data,$campaign_type){
 		$where = 'campaign_type ='.$campaign_type." and unix_timestamp(start_date) <= unix_timestamp() and unix_timestamp(end_date)>unix_timestamp() and ";
 		
 		$more = sizeof($product_id_data)>1?true:false;
 		$where .= $more?"(":"";
 		foreach ($product_id_data as $i=>$p) {
 			if( $i>0 ) $where .= " or ";
 			$where .= $more?"(":"";
 			$where .= "product_id=". $p[0]. ' and limit_price<='.$p[1];
 			$where .= $more?")":"";
 		}
 		$where .= $more?")":"";
		$sql = "select * from ". $this->_db->dbprefix('front_campaign'). " where ". $where;
 		$query = $this->_db->query($sql);
    	return $query->result();
 	}        
}
