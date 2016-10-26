<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class rush_model extends CI_Model
{
        public function filter ($filter)
	{
		$query = $this->db->get_where('ty_rush_info', $filter, 1);
		return $query->row();
	}
	
	public function all_filter($filter){
		$param = array();
		$from = " FROM ty_rush_info ";
		$where = " WHERE 1 ";
		if(!empty($filter['start_time'])){
		    $where .= " AND start_date >= '".$filter['start_time']." 00:00:00' AND start_date <='".$filter['start_time']." 23:59:59' ";
		}
		$filter['sort_by'] = empty($filter['sort_by']) ? 'rush_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'desc' : trim($filter['sort_order']);
		$sql = "SELECT * " . $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return $list;
	}
	
	public function update ($data, $rush_id)
	{
		$this->db->update('ty_rush_info', $data, array('rush_id' => $rush_id));
	}

        public function ru_update($data){
            $sql = "UPDATE ".$this->db->dbprefix('product_info')." SET promote_price='0' , promote_start_date ='0',promote_end_date='0',is_promote='0' WHERE product_id ".db_create_in($data);
            $this->db->query($sql);
        }

        public function pro_rush_update($start_date,$end_date,$data){
            $sql = "UPDATE ".$this->db->dbprefix('product_info')." SET promote_start_date ='".$start_date."',promote_end_date='".$end_date."' WHERE product_id ".db_create_in($data);
            $this->db->query($sql);
        }

        public function insert ($data)
	{
		$this->db->insert('ty_rush_info', $data);
		return $this->db->insert_id();
	}

        public function delete ($data)
	{
        	$this->db->delete('ty_rush_info', $data);
	}


        public function rush_list ($filter)
	{
                $param = array();
		$from = " FROM ty_rush_info ";
		$where = " WHERE 1 ";
		
		if(!empty($filter['query_rush_id'])){
                    $where .= " AND rush_id = ? ";
                    $param[] = $filter['query_rush_id'];
                }else{
		    if(!empty($filter['rush_index'])){
			$where .= " AND rush_index like ? ";
			$param[] = '%'. $filter['rush_index'] .'%';
		    }
		    if(!empty($filter['nav_id'])){
			$where .= " AND nav_id = ? ";
			$param[] = $filter['nav_id'];
		    }
		    if(!empty($filter['status'])){
			$where .= " AND status = ? ";
			$param[] = $filter['status'] - 1;
		    }

		    if(!empty($filter['start_time'])){
			$where .= " AND start_date >= '".$filter['start_time']." 00:00:00' AND start_date <='".$filter['start_time']." 23:59:59' ";
		    }
		    if(!empty($filter['end_time'])){
			$where .= " AND end_date = ? ";
			$param[] = $filter['end_time'];
		    }
		}
		$filter['sort_by'] = empty($filter['sort_by']) ? 'start_date' : trim($filter['sort_by']);
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
		$sql = "SELECT * "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
                
                // 获取rush_id列表，再批量查询可售数量和可售金额
                if (count($list) > 0) {
                    $rush_id_ary = array();
                    foreach ($list as $rush) {
                        $rush_id_ary[] = $rush->rush_id;
                    }
                    
                    $sql = "SELECT r.rush_id, SUM(p.gl_num) AS sale_number, SUM(r.price * p.gl_num) AS sale_amount  
                        FROM ty_rush_product r 
                        LEFT JOIN ty_product_sub p ON p.product_id = r.product_id 
                        WHERE r.rush_id ".db_create_in($rush_id_ary)."
                        GROUP BY r.rush_id ";
                    $query = $this->db->query($sql);
                    $count_list = $query->result();
                    $query->free_result();
                    
                    foreach ($list as $key => $rush) {
                        $rush_id = $rush->rush_id;
                        $rush->sale_number = 0;
                        $rush->sale_amount = '0.00';
                        foreach ($count_list as $count) {
                            if ($rush_id == $count->rush_id) {
                                $rush->sale_number = $count->sale_number;
                                $rush->sale_amount = $count->sale_amount;
                                $list[$key] = $rush;
                                break;
                            }
                        }
                    }
                }
                
		return array('list' => $list, 'filter' => $filter);
	}

        function rush_product_list($rush_id){
            $sql = "SELECT t.product_id,t.rush_id,p.product_sn,p.product_name,c.category_name,p.provider_productcode,p.market_price,p.shop_price,p.promote_price,t.rec_id,t.image_before_url,t.image_ing_url,t.sort_order,t.desc
                    FROM ty_rush_product AS t
                    LEFT JOIN ty_product_info AS p ON t.product_id = p.product_id
                    LEFT JOIN ty_rush_info AS r ON t.rush_id = r.rush_id
                    LEFT JOIN ty_product_category AS c ON t.category_id = c.category_id
                    WHERE t.rush_id = ?
                    ORDER BY t.sort_order";
            $param[] = $rush_id;
            $query = $this->db->query($sql , $param);
            return $query->result();
        }

        public function link_rush_search($filter){
            $from = " FROM ".$this->db->dbprefix('product_info')." AS p" .
            		" LEFT JOIN ".$this->db->dbprefix('product_category')." AS c ON p.category_id = c.category_id ".
            		" LEFT JOIN ".$this->db->dbprefix('product_category')." AS pc ON c.parent_id = pc.category_id" ;
	    $where = " WHERE is_audit = 1 ";
            $param = array();
            if (!empty($filter['depot_id']))
            {
            		$from .= " INNER JOIN (".
								" SELECT product_id,sum(product_number) AS pn FROM ".
								$this->db->dbprefix('transaction_info').
								" WHERE depot_id = ? AND trans_status in (1,2,4) GROUP BY product_id ".
							") AS t ON p.product_id = t.product_id ";
					$where .= " AND t.pn > 0 ";
                    $param[] = $filter['depot_id'];
            }
            
            if (!empty($filter['product_sn']))
            {
                    $where .= " AND p.product_sn LIKE ? ";
                    $param[] = '%' . $filter['product_sn'] . '%';
            }

            if (!empty($filter['product_name']))
            {
                    $where .= " AND p.product_name LIKE ? ";
                    $param[] = '%' . $filter['product_name'] . '%';
            }

            if (!empty($filter['brand']))
            {
                    $where .= " AND p.brand_id = ? ";
                    $param[] = $filter['brand'];
            }

            if (!empty($filter['provider_productcode']))
            {
                    $where .= " AND p.provider_productcode LIKE ? ";
                    $param[] = '%' . $filter['provider_productcode'] . '%';
            }

            // 供应商编码
            if (!empty($filter['provider_code']))
            {
                    $from .= " LEFT JOIN ".$this->db->dbprefix('product_provider')." AS pp ON pp.provider_id = p.provider_id" ;
                    $where .= " AND pp.provider_code LIKE ? ";
                    $param[] = '%' . $filter['provider_code'] . '%';
            }

            if (!empty($filter['category_id']))
            {
                    $where .= " AND (p.category_id = ? OR pc.category_id = ?) ";
                    $param[] = $filter['category_id'];
                    $param[] = $filter['category_id'];
            }

            if (!empty($filter['style_id']))
            {
                    $where .= " AND p.style_id = ? ";
                    $param[] = $filter['style_id'];
            }

            if (!empty($filter['season_id']))
            {
                    $where .= " AND p.season_id = ? ";
                    $param[] = $filter['season_id'];
            }

            if (!empty($filter['product_sex']))
            {
                    $where .= " AND p.product_sex = ? ";
                    $param[] = $filter['product_sex'];
            }

            if (!empty($filter['batch_code']))
            {
            		$from .= " LEFT JOIN ".$this->db->dbprefix('product_cost')." AS os ON p.product_id = os.product_id ".
            				 " LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS pb ON os.batch_id = pb.batch_id ";
                    $where .= " AND pb.batch_code = ? ";
                    $param[] = $filter['batch_code'];
            }
            
            // 排除已添加的限抢商品
            // $where .= " AND NOT EXISTS (
            //                 SELECT product_id FROM ".$this->db->dbprefix('rush_product')." AS rp
            //                 LEFT JOIN ".$this->db->dbprefix('rush_info')." AS r ON r.rush_id = rp.rush_id 
            //                 WHERE rp.product_id = p.product_id AND r.status <= 1 
            //           )";
					  
			// 排除已添加的团购商品
            // $where .= " AND NOT EXISTS (
            //                 SELECT product_id FROM ".$this->db->dbprefix('mami_tuan')." AS mt 
            //                 WHERE mt.product_id = p.product_id AND mt.status <= 1 
            //           )";

            $filter['sort_by'] = empty($filter['sort_by']) ? 'p.product_id' : trim($filter['sort_by']);
            $filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

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
            $sql = "SELECT c.category_name,p.category_id,p.product_id,p.product_sn,p.product_name,p.provider_productcode,p.market_price,p.shop_price "
                            . $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
                            . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
	    $query = $this->db->query($sql, $param);
            $list = $query->result();
            $query->free_result();
            return array('list' => $list, 'filter' => $filter);
        }
        // 商品相关方法
        public function filter_product($filter)
        {
            $query = $this->db->get_where('rush_product',$filter,1);
            return $query->row();
        }

        public function all_product ($filter)
        {
            $query = $this->db->get_where('rush_product', $filter);
            return $query->result();
        }

        public function update_product ($data, $rec_id)
        {
            $this->db->update('rush_product', $data, array('rec_id' => $rec_id));
        }

        public function insert_product ($data)
        {
            $this->db->insert('ty_rush_product', $data);
            return $this->db->insert_id();
        }

        public function batch_insert_product ($data)
        {
            $this->db->insert_batch('ty_rush_product', $data);
        }

        public function insert_p_r($data,$d){
            $date = date("Y-m-d H:i:s",time());
            $sql = "INSERT INTO ".$this->db->dbprefix('rush_product')."(rush_id,product_id,price,sort_order,category_id,create_admin,create_date)
                    SELECT '".$data[0]."',product_id,'".$data[2]."','".$data[3]."','".$data[4]."','".$data[5]."','".$data[6]."' FROM ".$this->db->dbprefix('product_info')."
                    WHERE
                    promote_start_date != '0000-00-00 00:00:00'
                    AND promote_end_date != '0000-00-00 00:00:00'
                    AND is_promote = '1'
                    AND (promote_start_date >= '".$date."'
                    OR  (promote_start_date < '".$date."' AND promote_end_date > '".$date."') )
                    AND product_id = " . $d;
            $query = $this->db->query($sql);
        }


        public function delete_product ($rec_id)
        {
            $this->db->delete('rush_product', array('rec_id'=>$rec_id));
        }
        public function delete_product_where ($where)
        {
            $this->db->delete('rush_product', $where);
        }

        public function sel_pro_df($data){
            $date = date("Y-m-d H:i:s",time());
            $sql = "SELECT product_id FROM ".$this->db->dbprefix('product_info')."
                WHERE
                promote_start_date != '0000-00-00 00:00:00'
                AND promote_end_date != '0000-00-00 00:00:00'
                AND is_promote = '1'
                AND (promote_start_date >= '".$date."'
                OR  (promote_start_date < '".$date."' AND promote_end_date > '".$date."') )
                AND product_id  " . db_create_in($data);
            $query = $this->db->query($sql);
            return $query->result();
        }
        
        public function filter_rush_template ($filter)
        {
            $query=$this->db->order_by('rec_id','desc')->get_where('rush_notice_template',$filter,1);
            return $query->row();
        }
        
        public function fetch_rush_mobile ($is_user=FALSE,$limit=0)
        {
            $sql="SELECT m.rec_id,m.mobile FROM ty_rush_mobile AS m
                WHERE ".($is_user?'m.user_id>0':"m.user_id=0 AND (m.area like '江苏%' OR m.area like '浙江%' OR m.area like '上海%')")."
                AND NOT EXISTS(SELECT 1 FROM ty_mobile_blacklist AS b WHERE b.mobile=m.mobile)
                ORDER BY send_date ASC
                LIMIT {$limit}";
            $query = $this->db->query($sql);
            return $query->result();
        }
        
        public function update_rush_mobile ($update,$key)
        {
            if(is_array($key)){
                $this->db->where("rec_id ".db_create_in($key));
            }else{
                $this->db->where("rec_id={$key}");
            }
            return $this->db->update('rush_mobile',$update);
        }
        
        public function log_rush_mobile ($data,$keys)
        {
            $sql="INSERT INTO ".$this->db->dbprefix('sms_log')." (sms_from,sms_to,template_id,template_content,create_date,send_date,status)
                SELECT '',m.mobile,-1,?,?,?,?
                FROM ".$this->db->dbprefix('rush_mobile')." AS m WHERE m.rec_id ".db_create_in($keys);
            
            $this->db->query($sql,array($data['content'],$data['create_date'],$data['send_date'],$data['status']));
        }
        
        public function auto_finish_rush_off() {
//            $sql = <<<SQL
//UPDATE 
//    ty_rush_info ri
//  , ty_admin_info ai
//SET 
//    ri.status = 2
//WHERE 
//    ri.end_date < NOW()
//AND 
//    ri.status < 2
//AND 
//    ri.create_admin = ai.admin_id
//;
//SQL;
            $sql = <<< SQL
INSERT INTO ty_product_onsale_record
(sub_id, sr_onsale, create_admin, create_date, onsale_memo)
SELECT ps.sub_id, 0, -1, now(), concat('rushid=', ri.rush_id) 
FROM ty_product_sub ps 
  , ty_product_info pi
  , ty_rush_product rp
  , ty_rush_info ri
  , ty_admin_info ai
WHERE 
    ps.product_id = pi.product_id
AND 
    rp.product_id = pi.product_id
AND
    ri.rush_id = rp.rush_id
AND
    ri.end_date < DATE_ADD( NOW(), INTERVAL 10  MINUTE)
AND
    ri.end_date > NOW()
AND 
    ri.status = 1
AND 
    ri.create_admin = ai.admin_id
;
SQL;
            $this->db->query($sql);
            $sql = <<< SQL
UPDATE 
    ty_product_sub ps 
  , ty_product_info pi
  , ty_rush_product rp
  , ty_rush_info ri
  , ty_admin_info ai
SET 
    ps.is_on_sale = 0
  , pi.is_onsale = 0
  , pi.promote_start_date = '0000-00-00 00:00:00'
  , pi.promote_end_date = '0000-00-00 00:00:00'
  , ri.status = 3
  , pi.is_promote = 0
WHERE 
    ps.product_id = pi.product_id
AND 
    rp.product_id = pi.product_id
AND
    ri.rush_id = rp.rush_id
AND
    ri.end_date < DATE_ADD( NOW(), INTERVAL 10  MINUTE)
AND
    ri.end_date > NOW()
AND 
    ri.status = 1
AND 
    ri.create_admin = ai.admin_id
;
SQL;

            $this->db->query($sql);
        }
        
                public function auto_finish_rush_on() {
//            $sql = <<<SQL
//UPDATE 
//    ty_rush_info ri
//  , ty_admin_info ai
//SET 
//    ri.status = 1
//WHERE 
//    ri.start_date < NOW()
//AND
//    ri.end_date > NOW()
//AND
//    ri.status = 0
//AND 
//    ri.create_admin = ai.admin_id
//;
//SQL;
                    $sql = <<< SQL
INSERT INTO ty_product_onsale_record
(sub_id, sr_onsale, create_admin, create_date, onsale_memo)
SELECT ps.sub_id, 1, -1, now(), concat('rushid=', ri.rush_id) 
FROM ty_product_sub ps 
  , ty_product_info pi
  , ty_rush_product rp
  , ty_rush_info ri
  , ty_admin_info ai
WHERE 
    ps.product_id = pi.product_id
AND 
    rp.product_id = pi.product_id
AND
    ri.rush_id = rp.rush_id
AND
    ri.start_date < DATE_ADD( NOW(), INTERVAL 30  MINUTE)
AND 
    ri.start_date > NOW()
AND
    ri.end_date > DATE_ADD( NOW(), INTERVAL 30  MINUTE)
AND
    ri.status = 1
AND 
    ri.create_admin = ai.admin_id
;
SQL;
            $this->db->query($sql);
            $sql = <<< SQL
UPDATE 
    ty_product_sub ps 
  , ty_product_info pi
  , ty_rush_product rp
  , ty_rush_info ri
  , ty_admin_info ai
SET 
    ps.is_on_sale = 1
  , pi.is_onsale = 1
  , pi.promote_start_date = ri.start_date
  , pi.promote_end_date = ri.end_date
  , pi.is_promote = 1
WHERE 
    ps.product_id = pi.product_id
AND 
    rp.product_id = pi.product_id
AND
    ri.rush_id = rp.rush_id
AND
    ri.start_date < DATE_ADD( NOW(), INTERVAL 30  MINUTE)
AND 
    ri.start_date > NOW()
AND
    ri.end_date > DATE_ADD( NOW(), INTERVAL 30  MINUTE)
AND
    ri.status = 1
AND 
    ri.create_admin = ai.admin_id
;
SQL;
            $this->db->query($sql);
        }
        
        public function comfort_rush_cat_content($rush_id, $cat_content) {
            $sql = <<< SQL
UPDATE 
    ty_rush_info
SET
    cat_content = ?
WHERE 
    rush_id = ?
SQL;
            $this->db->query($sql, array($cat_content, $rush_id));
        }
        
        public function query_all_rushings() 
        {
                $sql = "SELECT rush_id,rush_index FROM ".$this->db->dbprefix('rush_info')
                        ." WHERE `status` = 1 AND start_date <= NOW() AND end_date > NOW()"
                        ." ORDER BY sort_order DESC";
                $query = $this->db->query($sql);
                $list = $query->result();
                $query->free_result();
                return $list;
        }

        public function batch_query_by_ids($rush_id_ary) 
        {
                $sql = "SELECT * FROM ty_rush_info WHERE rush_id ".db_create_in($rush_id_ary);
                $res = $this->db_r->query($sql )->result();
                return $res;
        }
	
	public function onsale_rush_on($rush_id){
	    $this->db->trans_start();
$sql = <<< SQL
INSERT INTO ty_product_onsale_record
(sub_id, sr_onsale, create_admin, create_date, onsale_memo)
SELECT ps.sub_id, 1, -1, now(), concat('rushid=', ri.rush_id) 
FROM ty_product_sub ps 
  , ty_product_info pi
  , ty_rush_product rp
  , ty_rush_info ri
  , ty_admin_info ai
WHERE 
    ps.product_id = pi.product_id
AND 
    rp.product_id = pi.product_id
AND
    ri.rush_id = rp.rush_id
AND
    ri.status = 1
AND 
    ri.create_admin = ai.admin_id
AND 
    ri.rush_id = $rush_id
;
SQL;
$this->db->query($sql);
$sql = <<< SQL
UPDATE 
    ty_product_sub ps 
  , ty_product_info pi
  , ty_rush_product rp
  , ty_rush_info ri
  , ty_admin_info ai
SET 
    ps.is_on_sale = 1
  , pi.is_onsale = 1
  , pi.promote_start_date = ri.start_date
  , pi.promote_end_date = ri.end_date
  , pi.is_promote = 1
WHERE 
    ps.product_id = pi.product_id
AND 
    rp.product_id = pi.product_id
AND
    ri.rush_id = rp.rush_id
AND
    ri.status = 1
AND 
    ri.create_admin = ai.admin_id
AND 
    ri.rush_id = $rush_id
;
SQL;
$this->db->query($sql);  
$this->db->trans_commit();
	}
	
	public function onsale_rush_off($rush_id){
	    $this->db->trans_start();
            $sql = <<< SQL
INSERT INTO ty_product_onsale_record
(sub_id, sr_onsale, create_admin, create_date, onsale_memo)
SELECT ps.sub_id, 0, -1, now(), concat('rushid=', ri.rush_id) 
FROM ty_product_sub ps 
  , ty_product_info pi
  , ty_rush_product rp
  , ty_rush_info ri
  , ty_admin_info ai
WHERE 
    ps.product_id = pi.product_id
AND 
    rp.product_id = pi.product_id
AND
    ri.rush_id = rp.rush_id
AND
    ri.status = 1
AND 
    ri.create_admin = ai.admin_id
AND
   ri.rush_id = $rush_id
;
SQL;
            $this->db->query($sql);
            $sql = <<< SQL
UPDATE 
    ty_product_sub ps 
  , ty_product_info pi
  , ty_rush_product rp
  , ty_rush_info ri
  , ty_admin_info ai
SET 
    ps.is_on_sale = 0
  , pi.is_onsale = 0
  , pi.promote_start_date = '0000-00-00 00:00:00'
  , pi.promote_end_date = '0000-00-00 00:00:00'
  , pi.is_promote = 0
WHERE 
    ps.product_id = pi.product_id
AND 
    rp.product_id = pi.product_id
AND
    ri.rush_id = rp.rush_id
AND
    ri.status = 1
AND 
    ri.create_admin = ai.admin_id
AND
   ri.rush_id = $rush_id
;
SQL;

            $this->db->query($sql);
	    $this->db->trans_commit();
	}
        
        public function is_rushing_product($produt_id) {
            $sql = " SELECT * FROM ty_rush_product p "
                  ." LEFT JOIN ty_rush_info r ON r.rush_id = p.rush_id "
                  ." WHERE r.status <= 1 AND p.product_id = ".$produt_id
                  ." LIMIT 1";
            return $this->db_r->query($sql)->result();
        }
        
}

?>
