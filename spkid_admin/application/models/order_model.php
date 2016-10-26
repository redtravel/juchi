<?php 
/**
* Order_model
*/
class Order_model extends CI_Model
{
	
	public function order_list($filter)
	{
        $from = " FROM ".$this->db->dbprefix('order_info')." AS o 
				LEFT JOIN ".$this->db->dbprefix('user_info')." AS u ON o.user_id = u.user_id
				LEFT JOIN ".$this->db->dbprefix('order_source')." AS os ON o.source_id = os.source_id
				LEFT JOIN ".$this->db->dbprefix('shipping_info')." AS s ON o.shipping_id = s.shipping_id
				LEFT JOIN ".$this->db->dbprefix('payment_info')." AS p ON o.pay_id = p.pay_id
				LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON o.lock_admin = a.admin_id
				";
		$where = " WHERE 1 AND o.genre_id = 1 ";
		$param = array();

        if($filter['lock_admin']){
            $this->load->model('admin_model');
            $admin = $this->admin_model->filter(array('admin_name'=>$filter['lock_admin']));
            $lock_admin_id = $admin?$admin->admin_id:-9;
        }

		if (!empty($filter['order_sn']))
		{
			$where .= " AND o.order_sn LIKE ? ";
			$param[] = '%' . $filter['order_sn'] . '%';
		}
        if (!empty($filter['user_name']))
        {
            $where .= " AND (u.mobile LIKE ? OR u.email LIKE ?) ";
            $param[] = '%' . $filter['user_name'] . '%';
            $param[] = '%' . $filter['user_name'] . '%';
        }
        if (!empty($filter['consignee']))
        {
            $where .= " AND o.consignee LIKE ? ";
            $param[] = '%' . $filter['consignee'] . '%';
        }
        if(isset($lock_admin_id)){
            $where .= " AND o.lock_admin = ? ";
            $param[] = $lock_admin_id;
        }
        if($filter['source_id']){
            $where .= " AND o.source_id = ? ";
            $param[] = $filter['source_id'];
        }
        if($filter['pay_id']){
            $where .= " AND o.pay_id = ? ";
            $param[] = $filter['pay_id'];
        }
        if($filter['shipping_id']){
            $where .= " AND o.shipping_id = ? ";
            $param[] = $filter['shipping_id'];
        }
        if($filter['order_status']){
            if($filter['order_status']==-1) {
                $where .= " AND o.order_status = 0 ";
            }else{
                $where .= " AND o.order_status = ? ";
                $param[] = $filter['order_status'];
            }
        } else {
            $where .= " AND o.order_status <> 4 ";
        }
        if($filter['pay_status']){
            if($filter['pay_status']==-1) {
                $where .= " AND o.pay_status = 0 ";
            }else{
                $where .= " AND o.pay_status = ? ";
                $param[] = $filter['pay_status'];
            }
        }
        if($filter['shipping_status']){
            if($filter['shipping_status']==-1) {
                $where .= " AND o.shipping_status = 0 ";
            }else{
                $where .= " AND o.shipping_status = ? ";
                $param[] = $filter['shipping_status'];
            }
        }
         if($filter['is_ok']){
             if($filter['is_ok']==-1) {
                $where .= " AND o.is_ok = 0 ";
            }else{
                $where .= " AND o.is_ok = ? ";
                $param[] = $filter['is_ok'];
            }
        }
        if($filter['country']){
            $where .= " AND o.country = ? ";
            $param[] = $filter['country'];
        }
        if($filter['province']){
            $where .= " AND o.province = ? ";
            $param[] = $filter['province'];
        }
        if($filter['city']){
            $where .= " AND o.city = ? ";
            $param[] = $filter['city'];
        }
        if($filter['district']){
            $where .= " AND o.district = ? ";
            $param[] = $filter['district'];
        }
        if($filter['add_start']){
            $where .= " AND o.create_date >= ? ";
            $param[] = $filter['add_start'];
        }
        if($filter['add_end']){
            $where .= " AND o.create_date <= ? ";
            $param[] = $filter['add_end'];
        }
        if($filter['pay_start']){
            $where .= " AND o.finance_date >= ? ";
            $param[] = $filter['pay_start'];
        }
        if($filter['pay_end']){
            $where .= " AND o.finance_date <= ? ";
            $param[] = $filter['pay_end'];
        }
        if($filter['shipping_start']){
            $where .= " AND o.shipping_date >= ? ";
            $param[] = $filter['shipping_start'];
        }
        if($filter['shipping_end']){
            $where .= " AND o.shipping_date <= ? ";
            $param[] = $filter['shipping_end'];
        }
        if($filter['odd']) $where .= " AND o.odd=1 ";
        if($filter['pick']) $where .=" AND o.pick_sn!='' AND o.shipping_status=0 ";
	if($filter['consign']){
	     $where .= " AND o.order_id IN ( SELECT DISTINCT order_id  FROM ".$this->db->dbprefix('order_product')." AS op WHERE op.consign_mark > 0)";
	}
        if($filter['product_sn']||$filter['package_id']||$filter['brand_id']||$filter['category_id']||$filter['provider_id']){
            $where .= " AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_product')." AS op, ".$this->db->dbprefix('product_info')." AS p WHERE op.order_id = o.order_id AND op.product_id = p.product_id  ";            
            if($filter['product_sn']) {
                $where.=" AND p.product_sn = ? ";
                $param[] = $filter['product_sn'];
            }
            if($filter['category_id']) {
                $category_ids = array($filter['category_id']);
                $this->load->model('category_model');
                foreach($this->category_model->all_category(array('parent_id'=>$filter['category_id'])) as $cat) $category_ids[] = $cat->category_id;
                $where.=" AND p.category_id ".db_create_in($category_ids)." ";
            }
            if($filter['brand_id']) {
                $where.=" AND p.brand_id = ? ";
                $param[] = $filter['brand_id'];
            }
            if($filter['provider_id']) {
                $where.=" AND p.provider_id = ? ";
                $param[] = $filter['provider_id'];
            }
            if($filter['package_id']) {
                $where.=" AND op.package_id = ? ";
                $param[] = $filter['package_id'];
            }
            $where .=") ";
        }
	if($filter['tel']){
	   $where .= " AND o.tel = ? ";
	   $param[] = $filter['tel'];
       }
	if($filter['mobile']){
	   $where .= " AND o.mobile = ? ";
	   $param[] = $filter['mobile'];
       }
       if($filter['payment_status']){
	   if($filter['payment_status']==-1) {
	       $where .= " AND o.order_price+shipping_fee > paid_price ";
	   }else{
	       $where .= " AND o.order_price+shipping_fee = paid_price ";
	   }
       }
		$filter['sort_by'] = empty($filter['sort_by']) ? 'o.order_id' : trim($filter['sort_by']);
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
		$sql = "SELECT t.*, IF (r.order_id IS NULL, 0, 1) AS has_return,IF(rd.order_id IS NULL, 0, 1) AS has_refund, IF (c.order_id IS NULL, 0, 1) AS has_change
                          FROM
                          (
                          SELECT o.*, u.user_name, u.create_date as reg_date, os.source_name,s.shipping_name,p.pay_name,a.admin_name as lock_name"
                          . $from 
                          . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
                          . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size']
                          . " ) t"
                          . " LEFT JOIN (SELECT DISTINCT order_id FROM ty_order_return_info WHERE return_status IN (0, 1)) r ON t.order_id = r.order_id"
                          . " LEFT JOIN (SELECT DISTINCT order_id FROM ty_order_refund) rd ON t.order_id = rd.order_id"
                          . " LEFT JOIN (SELECT DISTINCT order_id FROM ty_order_change_info WHERE change_status IN (0, 1)) c ON t.order_id = c.order_id";
                $query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function filter($filter)
	{
		$query = $this->db->get_where('order_info', $filter, 1);
		return $query->row();
	}

    public function lock_order($order_id, $invoice_no = '')
    {
        $condition =(!empty($order_id)) ? (strtoupper(substr(strval($order_id),0,2))=='DD') ? "AND order_sn = '".$order_id."'" : "AND order_id = '".$order_id."'" : '';
        if ($condition == '') $condition = 'AND shipping_status = 0 AND invoice_no = "'.$invoice_no.'"'; 
        $sql = "SELECT * FROM ".$this->db->dbprefix('order_info')." WHERE 1=1 ".$condition."  FOR UPDATE";
        $query = $this->db->query($sql);
        return $query->row();
    }

	public function insert($update)
	{
		$this->db->insert('order_info', $update);
		return $this->db->insert_id();
	}

    public function update($update, $order_id)
    {
        $this->db->update('order_info', $update, array('order_id'=>$order_id));
    }

    public function delete($order_id)
    {
        $this->db->delete('order_info', array('order_id'=>$order_id));
    }
	
	public function all_order($filter){
		$query = $this->db->get_where('order_info',$filter);
		return $query->result();
	}

    public function order_info($order_id)
    {
        $sql = "SELECT distinct o.order_id, o.*, p.pay_code,p.pay_name,s.shipping_code,s.shipping_name,sc.source_code,sc.source_name,pr.region_name as province_name,cr.region_name as city_name,dr.region_name as district_name, ps.pick_cell 
                FROM ".$this->db->dbprefix('order_info')." AS o
                LEFT JOIN ".$this->db->dbprefix('order_source')." AS sc ON o.source_id = sc.source_id
                LEFT JOIN ".$this->db->dbprefix('payment_info')." AS p ON o.pay_id = p.pay_id
                LEFT JOIN ".$this->db->dbprefix('shipping_info')." AS s ON o.shipping_id = s.shipping_id
                LEFT JOIN ".$this->db->dbprefix('region_info')." AS pr ON o.province = pr.region_id
                LEFT JOIN ".$this->db->dbprefix('region_info')." AS cr ON o.city = cr.region_id
                LEFT JOIN ".$this->db->dbprefix('region_info')." AS dr ON o.district = dr.region_id
				LEFT JOIN ".$this->db->dbprefix('pick_sub')." AS ps ON o.order_sn = ps.rel_no 
                WHERE o.order_id = ? LIMIT 1";
        $query = $this->db->query($sql,array($order_id));
        return $query->row();

    }

    public function search_user($key)
    {
        $key = '%'.$key.'%';
        $sql = "SELECT user_id, user_name, real_name, email, mobile FROM ".$this->db->dbprefix('user_info')." WHERE user_name LIKE ? OR email LIKE ? OR mobile LIKE ? LIMIT 10";
        $query = $this->db->query($sql, array($key,$key,$key));
        return $query->result();
    }

	public function search_product($filter)
	{
		switch ($filter['goods_type']) {
			case 'product':
                $from = "FROM ".$this->db->dbprefix('product_info')." AS p
                LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON p.brand_id=b.brand_id
                LEFT JOIN ".$this->db->dbprefix('product_style')." AS s ON p.style_id=s.style_id
                LEFT JOIN ".$this->db->dbprefix('product_season')." AS ss ON p.season_id=ss.season_id";
                $where = " WHERE p.is_audit=1 AND p.genre_id = 1 ";
                $param = array();
                if ($filter['category_id']) {
                    $where .= "AND p.category_id = ? ";
                    $param[] = $filter['category_id'];
                }
                if ($filter['brand_id']) {
                    $where .= "AND p.brand_id = ? ";
                    $param[] = $filter['brand_id'];
                }
                if ($filter['product_sn']) {
                    $where .= "AND p.product_sn LIKE ? ";
                    $param[] = '%'.$filter['product_sn'].'%';
                }
                if ($filter['product_name']) {
                    $where .= "AND p.product_name LIKE ? ";
                    $param[] = '%'.$filter['product_name'].'%';
                }
                if ($filter['provider_productcode']) {
                    $where .= "AND p.provider_productcode LIKE ? ";
                    $param[] = '%'.$filter['provider_productcode'].'%';
                }
                if ($filter['depot_id']) {
                    $where .= "AND EXISTS (SELECT 1 FROM ya_product_depot_sub AS sub WHERE sub.product_id=p.product_id AND sub.depot_id ='".$filter['depot_id']."' AND sub.gl_num>0 ";
                } else {
                    $where .= "AND EXISTS (SELECT 1 FROM ".$this->db->dbprefix('product_sub')." AS sub WHERE sub.product_id=p.product_id  AND (sub.gl_num>0 OR sub.consign_num>0 OR sub.consign_num=-2) ";
                }
                if ($filter['size_id']) {
                   $where .= "AND sub.size_id=? ";
                   $param[] = $filter['size_id']; 
                }
                if ($filter['color_group']) {
                    $this->load->model('color_model');
                    $all_color = $this->color_model->all_color(array('group_id'=>$filter['color_group']));
                    $where .= "AND sub.color_id ".db_create_in(array_keys(get_pair($all_color,'color_id','color_name')))." ";
                }
                $where .= ") ";

                $filter['sort_by'] = 'p.product_id';
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
                $sql = "SELECT p.product_id,p.product_name,p.product_sn,p.provider_productcode,p.product_sex,p.product_year,p.product_month,p.promote_price,p.promote_start_date,p.promote_end_date,p.is_promote,p.shop_price,p.market_price,
                b.brand_name,s.style_name,ss.season_name "
                        . $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
                        . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
                $query = $this->db->query($sql, $param);
                $list = $query->result();
                $query->free_result();
                return array('list' => $list, 'filter' => $filter);
				break;

            case 'package':
                $from = "FROM ty_package_info AS pkg ";
                $where = "WHERE pkg.package_status>0 ";
                $param = array();
                if ($filter['package_name']) {
                    $where .= "pkg.package_name LIKE ? ";
                    $param[] = '%'.$filter['package_name'].'%';
                }
                $where .= "AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('package_area_product')." AS pp, ".$this->db->dbprefix('product_info')." AS p, ".$this->db->dbprefix('product_sub')." AS sub WHERE pp.package_id=pkg.package_id AND p.product_id=pp.product_id AND sub.product_id=p.product_id ";
                if ($filter['category_id']) {
                    $where .= "AND p.category_id = ? ";
                    $param[] = $filter['category_id'];
                }
                if ($filter['brand_id']) {
                    $where .= "AND p.brand_id = ? ";
                    $param[] = $filter['brand_id'];
                }
                if ($filter['product_sn']) {
                    $where .= "AND p.product_sn LIKE ? ";
                    $param[] = '%'.$filter['product_sn'].'%';
                }
                if ($filter['product_name']) {
                    $where .= "AND p.product_name LIKE ? ";
                    $param[] = '%'.$filter['product_name'].'%';
                }
                if ($filter['provider_productcode']) {
                    $where .= "AND p.provider_productcode LIKE ? ";
                    $param[] = '%'.$filter['provider_productcode'].'%';
                }
                if ($filter['size_id']) {
                   $where .= "AND sub.size_id=? ";
                   $param[] = $filter['size_id']; 
                }
                if ($filter['color_group']) {
                    $all_color = $this->color_model->all_color(array('group_id'=>$filter['color_group']));
                    $where .= "AND sub.color_id ".db_create_in(array_keys(get_pair($all_color,'color_id','color_name')))." ";
                }
                $where .= ") ";

                $filter['sort_by'] = 'pkg.package_id';
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
                $sql = "SELECT pkg.* "
                        . $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
                        . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
                $query = $this->db->query($sql, $param);
                $list = $query->result();
                $query->free_result();
                return array('list' => $list, 'filter' => $filter);
                break;
		}
		
	}

    public function order_product($order_id)
    {
        $sql = "SELECT op.*, p.provider_id, p.product_name, p.product_sn,p.provider_productcode, p.unit_name,cat.category_id, pp.provider_code, 
                cat.category_name, b.brand_id, b.brand_name, c.color_name, c.color_sn, s.size_name, s.size_sn,sub.sub_id,sub.gl_num,sub.consign_num as gl_consign_num,
                ti.batch_id,pb.batch_code,ti.consign_price,ti.cost_price,ti.consign_rate,ti.product_cess, ti.product_number,d.depot_name,li.location_name, oi.order_sn, sub.provider_barcode, pg.img_215_215    
                FROM ".$this->db->dbprefix('order_product')." AS op
                LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON op.product_id = p.product_id 
                LEFT JOIN ".$this->db->dbprefix('product_provider')." AS pp ON p.provider_id = pp.provider_id  
                LEFT JOIN ".$this->db->dbprefix('product_category')." AS cat ON cat.category_id = p.category_id
                LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON b.brand_id = p.brand_id
                LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON c.color_id = op.color_id
                LEFT JOIN ".$this->db->dbprefix('product_size')." AS s ON s.size_id = op.size_id
                LEFT JOIN ".$this->db->dbprefix('product_sub')." AS sub ON op.product_id = sub.product_id AND op.color_id = sub.color_id AND op.size_id = sub.size_id
                LEFT JOIN ".$this->db->dbprefix('order_info')." AS oi on oi.order_id=op.order_id 
                LEFT JOIN ".$this->db->dbprefix('transaction_info')." AS ti on oi.order_sn=ti.trans_sn and ti.trans_type=3 AND ti.trans_status != 5 AND op.product_id = ti.product_id AND op.color_id = ti.color_id AND op.size_id = ti.size_id 
		LEFT JOIN ".$this->db->dbprefix('depot_info')." as d ON ti.depot_id = d.depot_id 
		LEFT JOIN ".$this->db->dbprefix('location_info')." as li ON ti.location_id = li.location_id 
		LEFT JOIN ".$this->db->dbprefix('product_gallery')." as pg ON pg.product_id = op.product_id AND pg.color_id = op.color_id  
		LEFT JOIN ".$this->db->dbprefix('purchase_batch')." as pb ON ti.batch_id = pb.batch_id
		WHERE op.order_id = ?  GROUP BY op.op_id,ti.batch_id " ;
        
        $query = $this->db->query($sql, array($order_id));
        return $query->result();
    }
    
    /**
     * 获取订单拒收transaction数据
     * @param type $order_sn
     * @return type
     */
    public function order_deny($order_sn)
    {
        $sql = "SELECT sub.`sub_id`,ABS(ti.`product_number`) AS product_number,op.`consign_num`,op.`op_id`,ti.`product_id`,ti.`color_id`,ti.`size_id`,
		ti.shop_price,ti.batch_id,ti.consign_price,ti.cost_price,ti.consign_rate,ti.product_cess,ti.expire_date,ti.production_batch
                FROM ".$this->db->dbprefix('transaction_info')." AS ti
                LEFT JOIN ".$this->db->dbprefix('order_info')." AS oi ON oi.`order_sn` = ti.`trans_sn`
		LEFT JOIN ".$this->db->dbprefix('order_product')." AS op ON op.`order_id` = oi.`order_id` AND op.product_id = ti.product_id AND op.color_id = ti.color_id AND op.size_id = ti.size_id
                LEFT JOIN ".$this->db->dbprefix('product_sub')." AS sub ON sub.product_id = ti.product_id AND sub.color_id = ti.color_id AND sub.size_id = ti.size_id
                WHERE ti.trans_type = 3 AND ti.trans_status = 2 AND ti.trans_sn = ?
                GROUP BY ti.transaction_id;";
        $query = $this->db->query($sql, array($order_sn));
        return $query->result();
    }

    public function all_product($filter)
    {
        $query = $this->db->get_where('order_product',$filter);
        return $query->result();
    }

    public function order_payment($order_id)
    {
        $sql = "SELECT op.*, p.pay_name, p.pay_code, a.admin_name, p.is_discount
                FROM ".$this->db->dbprefix('order_payment')." AS op
                LEFT JOIN ".$this->db->dbprefix('payment_info')." AS p ON op.pay_id = p.pay_id
                LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON op.payment_admin = a.admin_id
                WHERE op.order_id = ? AND op.is_return = 0 ORDER BY op.payment_id";
        $query = $this->db->query($sql, array(intval($order_id)));

        return $query->result();
    }

    public function order_advice($order_id)
    {
        $sql = "SELECT oa.*, t.type_name, t.type_color, a.admin_name
                FROM ".$this->db->dbprefix('order_advice')." AS oa
                LEFT JOIN ".$this->db->dbprefix('order_advice_type')." AS t ON oa.type_id = t.type_id
                LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON oa.advice_admin = a.admin_id
                WHERE oa.order_id = ? AND oa.is_return = 1 ORDER BY oa.advice_id";
        $query = $this->db->query($sql, array(intval($order_id)));

        return $query->result();
    }

    public function delete_advice_where($filter)
    {
        $this->db->delete('order_advice',$filter);
    }

    public function order_action($order_id)
    {
        $sql = "SELECT oa.*, a.admin_name
                FROM ".$this->db->dbprefix('order_action')." AS oa
                LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON oa.create_admin = a.admin_id
                WHERE oa.order_id = ? AND oa.is_return = 0 ORDER BY oa.action_id";
        $query = $this->db->query($sql, array(intval($order_id)));

        return $query->result();
    }

    public function delete_action_where($filter)
    {
        $this->db->delete('order_action',$filter);
    }

    public function order_trans($order_sn)
    {
        $sql = "SELECT t.*,d.depot_name,l.location_name,pb.batch_code
            FROM ".$this->db->dbprefix('transaction_info')." AS t
            LEFT JOIN ".$this->db->dbprefix('depot_info')." AS d ON t.depot_id = d.depot_id
            LEFT JOIN ".$this->db->dbprefix('location_info')." AS l ON t.location_id = l.location_id
            LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS pb ON t.batch_id = pb.batch_id
            WHERE t.trans_type= ".TRANS_TYPE_SALE_ORDER." AND t.trans_sn = ? 
            AND t.trans_status IN (".TRANS_STAT_AWAIT_OUT.",".TRANS_STAT_OUT.",".TRANS_STAT_IN.")";
        $query = $this->db->query($sql,array($order_sn));
        return $query->result();
    }

    public function available_shipping($filter)
    {
        // 第三方平台订单，返回第三方配送方式
        if(!empty($filter['order_id'])){
            $CI = &get_instance();
            $CI->load->model('shipping_model');
            $order_cooperation_id = $this->get_order_cooperation_id($filter['order_id']);
            if($order_cooperation_id==COOPERATION_TYPE_TMALL){
                $shipping_info = $CI->shipping_model->filter(array('shipping_id'=>SHIPPING_ID_PINGTAI));
                return array($shipping_info);
            }
        }
        $sql = "SELECT DISTINCT s.*
                FROM ".$this->db->dbprefix('shipping_info')." AS s
                LEFT JOIN ".$this->db->dbprefix('shipping_area')." AS a ON a.shipping_id = s.shipping_id
                LEFT JOIN ".$this->db->dbprefix('shipping_area_region')." AS r ON r.shipping_area_id = a.shipping_area_id
                WHERE s.is_use=1 ";
        $param = array();
        if (isset($filter['region_ids']) && is_array($filter['region_ids'])) {
            // 如果允许CAC，则CAC不需要地域判断
            if(isset($filter['can_cac']) && $filter['can_cac'])
            $sql .= " AND (s.shipping_id = ".SHIPPING_ID_CAC." OR r.region_id ".db_create_in($filter['region_ids']).") ";
            else
            $sql .= " AND r.region_id ".db_create_in($filter['region_ids'])." ";
        }
        if (isset($filter['pay_id']) && $filter['pay_id']==PAY_ID_COD) {
            $sql .= " AND a.is_cod = 1 ";
        }
        if (isset($filter['shipping_id'])) {
            $sql .= " AND s.shipping_id = ? ";
            $param[] = intval($filter['shipping_id']);
        }
        $sql .= " AND s.shipping_id IN (SELECT shipping_id FROM ".$this->db->dbprefix('order_routing')." WHERE show_type!=4 ";
        if (!empty($filter['source_id'])) {
            $sql .= " AND source_id = ? ";
            $param[] = intval($filter['source_id']);
        }
        if (!empty($filter['pay_id'])) {
            $sql .= " AND pay_id = ? ";
            $param[] = intval($filter['pay_id']);
        }
        $sql .= ") ORDER BY s.sort_order ASC";
        $query = $this->db->query($sql, $param);
        return $query->result();
    }

    //根据routing过虑可用的支付方式，默认过滤掉了余额支付，如果不过滤，请传入can_balance
    public function available_pay($filter)
    {
        if(isset($filter['shipping_id']) && $filter['shipping_id']==SHIPPING_ID_PINGTAI){
            unset($filter['shipping_id']); //如果是第三方平台快递，不加此条件
        }
        $sql = "SELECT DISTINCT p.*
                FROM ".$this->db->dbprefix('order_routing')." AS r
                LEFT JOIN ".$this->db->dbprefix('payment_info')." AS p ON r.pay_id = p.pay_id
                WHERE r.show_type != 4 and p.enabled = 1 ";
        $param = array();
        if (!empty($filter['source_id'])) {
            $sql .= " AND r.source_id = ? ";
            $param[] = intval($filter['source_id']);
        }
        if (!empty($filter['shipping_id'])) {
            $sql .= " AND r.shipping_id = ? ";
            $param[] = intval($filter['shipping_id']);
        }
        if (!empty($filter['pay_id'])) {
            $sql .= " AND r.pay_id = ? ";
            $param[] = intval($filter['pay_id']);
        }
        if (isset($filter['is_discount'])) {
            $sql .= " AND p.is_discount = ? ";
            $param[] = intval($filter['is_discount']);
        }
        if (empty($filter['can_balance'])) {
            $sql .= " AND r.pay_id != ? ";
            $param[] = PAY_ID_BALANCE;
        }
        if (empty($filter['can_voucher'])) {
            $sql .= " AND r.pay_id != ? ";
            $param[] = PAY_ID_VOUCHER;
        }
        $sql .= "ORDER BY p.sort_order";
        $query = $this->db->query($sql, $param);
        return $query->result();
    }

    public function insert_action($order, $action_note)
    {
        $update = array(
            'order_id' => $order->order_id,
            'is_return' => 0,
            'order_status' => isset($order->order_status)?$order->order_status:0,
            'shipping_status' => isset($order->shipping_status)?$order->shipping_status:0,
            'pay_status' => isset($order->pay_status)?$order->pay_status:0,
            'action_note' => $action_note,
            'create_admin' => isset($this->admin_id)?$this->admin_id:intval($this->session->userdata('admin_id')),
            'create_date' => isset($this->time)?$this->time:date('Y-m-d H:i:s')
            );
        $this->db->insert('order_action',$update);
        return $this->db->insert_id();
    }

    public function insert_advice($update)
    {
        $this->db->insert('order_advice',$update);
        return $this->db->insert_id();
    }

    public function filter_routing($filter)
    {
        $query = $this->db->get_where('order_routing',$filter,1);
        return $query->row();
    }

    public function all_routing($filter)
    {
        $query = $this->db->get_where('order_routing',$filter);
        return $query->result();
    }

    public function filter_product($filter)
    {
        $query = $this->db->get_where('order_product',$filter,1);
        return $query->row();
    }

    public function insert_product($update)
    {
        $this->db->insert('order_product',$update);
        return $this->db->insert_id();
    }

    public function delete_product($op_id)
    {
        $this->db->delete('order_product', array('op_id'=>$op_id));
    }

    public function update_product($update,$op_id)
    {
        $this->db->update('order_product',$update,array('op_id'=>$op_id));
    }

    public function filter_payment($filter)
    {
        $query = $this->db->get_where('order_payment',$filter,1);
        return $query->row();
    }

    public function insert_payment($update)
    {
        $this->db->insert('order_payment',$update);
        return $this->db->insert_id();
    }

    public function delete_payment($payment_id)
    {
        $this->db->delete('order_payment', array('payment_id'=>$payment_id));
    }

    public function assign_trans($order,$sub,$num,$sub_id,$shop_price=NULL)
    {
        if($num<1) return array('err'=>0,'msg'=>'');
        $where = "";
        if (isset($sub->depot_id)) {
	    $where .= " AND t.depot_id = '".$sub->depot_id."'";
        } else {
	    $where = " AND d.is_return = 0";
	}
        /*
        $sql = "SELECT t.depot_id,t.location_id,SUM(t.product_number) AS product_number,
                pc.batch_id,pc.consign_price,pc.cost_price,pc.consign_rate,pc.product_cess,
                pi.shop_price
                FROM ".$this->db->dbprefix('transaction_info')." AS t
                LEFT JOIN ".$this->db->dbprefix('depot_info')." AS d ON t.depot_id=d.depot_id
                LEFT JOIN ".$this->db->dbprefix('location_info')." AS l ON t.location_id=l.location_id
                LEFT JOIN ".$this->db->dbprefix('product_cost')." as pc on pc.batch_id=t.batch_id
                and t.product_id=pc.product_id 
                LEFT JOIN ".$this->db->dbprefix('product_info')." as pi on pi.product_id=t.product_id
                WHERE d.is_use = 1 AND d.is_return = 0 AND l.is_use = 1 AND t.trans_status IN (1,2,4) 
                AND t.product_id=? AND t.color_id = ? AND t.size_id = ?
                GROUP BY l.location_id HAVING product_number>0 ORDER BY d.depot_priority,t.create_date asc;";
        */
        //取trans数据
        /*
		前提：系统在订单分配库存时同时将商品的一些价格数据记录到出入库明细表
		这其中一部分成本类价格（如t.consign_price,t.cost_price,t.consign_rate,t.product_cess），按要求，应该根据对应的批次取自商品成本价格表ty_product_cost。
		因为99.999999%（接近100%）的时候成本价格表ty_product_cost的数据和批次的入库单据的数据是一致的。
		所以本段代码实际运行过程中没有问题。
		从程序严谨性角度来讲有一点遗憾，在此做注释。 20130318 frank		
		*/
		$sql = "SELECT t.depot_id,t.location_id,SUM(t.product_number) AS product_number,
                t.batch_id,t.consign_price,t.cost_price,t.consign_rate,t.product_cess,t.expire_date,t.production_batch,
                pi.shop_price
                FROM ".$this->db->dbprefix('transaction_info')." AS t
                LEFT JOIN ".$this->db->dbprefix('depot_info')." AS d ON t.depot_id=d.depot_id
                LEFT JOIN ".$this->db->dbprefix('location_info')." AS l ON t.location_id=l.location_id
                LEFT JOIN ".$this->db->dbprefix('product_info')." as pi on pi.product_id=t.product_id
                WHERE d.is_use = 1 ".$where." AND l.is_use = 1 AND t.trans_status IN (1,2,4) 
                AND t.product_id=? AND t.color_id = ? AND t.size_id = ?
                GROUP BY t.batch_id,l.location_id HAVING product_number>0 ORDER BY MIN(expire_date) ASC, MIN(t.batch_id) ASC,d.depot_priority ASC;";
        $query = $this->db->query($sql, array($sub->product_id,$sub->color_id,$sub->size_id));
        $trans = $query->result();
        if(!$trans) return array('err'=>1,'msg'=>'没有库存');
        //分配储位
        $result = array();
        $row = array(
            'trans_type'=>TRANS_TYPE_SALE_ORDER,
            'trans_status'=>TRANS_STAT_AWAIT_OUT,
            'trans_sn'=>"'{$order->order_sn}'",
            'product_id'=>$sub->product_id,
            'color_id'=>$sub->color_id,
            'size_id'=>$sub->size_id,
            'sub_id'=>$sub_id,
            'create_admin'=>$this->admin_id,
            'create_date'=>"'{$this->time}'",
            'trans_direction'=>0,
            'finance_check_admin' => isset($sub->finance_admin) ? $sub->finance_admin : 0 , 
            'finance_check_date' => empty($sub->finance_date) ? null : "'$sub->finance_date'"
            );
        foreach($trans as $t){
            $row['depot_id'] = $t->depot_id;
            $row['location_id'] = $t->location_id;
            $row['product_number'] = min($t->product_number,$num)*-1;
            $row['batch_id'] = $t->batch_id;
            $row['shop_price'] = (NULL === $shop_price ? $t->shop_price : $shop_price);
            $row['consign_price'] = $t->consign_price;
            $row['consign_rate'] = $t->consign_rate;
            $row['cost_price'] = $t->cost_price;
            $row['product_cess'] = $t->product_cess;
            $row['expire_date'] = "'{$t->expire_date}'";
	    $row['production_batch'] = "'{$t->production_batch}'";
            $result[] = $row;
            $num += $row['product_number']; //因为$row['product_number']为负值，所以此处用+
            if($num==0) break;
        }
        if($num) return array('err'=>1,'msg'=>'分配储位出错');
        //插入储位
        $this->insert_trans_batch($result);
       
        return array('err'=>0,'msg'=>'');
    }

    public function update_trans($update,$filter)
    {
        $this->db->update('transaction_info',$update,$filter);
    }

    public function insert_trans_batch($updates)
    {
        $keys = array('trans_type','trans_status','trans_sn','product_id','color_id','size_id','sub_id','create_admin','create_date','trans_direction','depot_id','location_id','product_number',
                'batch_id','shop_price','consign_price','consign_rate','cost_price','product_cess','update_admin','update_date', 'finance_check_date', 'finance_check_admin','expire_date','production_batch');
        $sql = "INSERT INTO ".$this->db->dbprefix('transaction_info');
                //." (".implode(',',$keys).") VALUES ";
        $result = array();
        foreach ($updates as $update) {
            if (empty($updates["finance_check_date"])) {
                $updates["finance_check_date"] = "'0000-00-00 00:00:00'";
            } 
            $row = array();
            $keys_arr = array();
            foreach($keys as $key) {
                if(isset($update[$key])) {
                    $keys_arr[] = $key;
                    $row[$key] = $update[$key];
                }
            }
            $result[] = '('.implode(',',$row).')';
        }
        //$sql .= implode(',',$result);
        $sql .= '('.implode(',',$keys_arr).') VALUES '.implode(',',$result);
        $this->db->query($sql);
    }

    public function order_product_details($order_id)
    {
        $order_product = $this->order_product($order_id);
        $product_list = index_array($order_product,'product_id');
        $result = array();
        foreach ($order_product as $p) {
            $p->track_id = 0;
            $p->track_sn = '';
            $p->real_product_num = $p->product_num;
            $p->real_consign_num = $p->consign_num;
            $key = "{$p->op_id}-{$p->product_id}-{$p->color_id}-{$p->size_id}-{$p->track_id}";
            $result[$key] = $p;
        }
        
        //处理换货
        $sql = "SELECT cp.*,ci.change_status,ci.is_ok,ci.change_sn,sub.sub_id,sub.gl_num,
            sub.consign_num as gl_consign_num,c.color_sn,c.color_name,s.size_sn,s.size_name
            FROM ".$this->db->dbprefix('order_change_product')." AS cp
            LEFT JOIN ".$this->db->dbprefix('order_change_info')." AS ci ON cp.change_id = ci.change_id
            LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON c.color_id = cp.color_id
            LEFT JOIN ".$this->db->dbprefix('product_size')." AS s ON s.size_id = cp.size_id
            LEFT JOIN ".$this->db->dbprefix('product_sub')." AS sub ON sub.product_id = cp.product_id AND sub.color_id = cp.color_id AND sub.size_id = cp.size_id
            WHERE ci.order_id = ? AND ci.change_status IN (0,1)";
        $query = $this->db->query($sql,array($order_id));
        $change_product = $query->result();
        // 处理换货中的替换商品
        foreach ($change_product as $p) {            
            $key = "{$p->op_id}-{$p->product_id}-{$p->color_id}-{$p->size_id}-{$p->cp_id}";
            $row = clone $product_list[$p->product_id];
            $row->color_id = $p->color_id;
            $row->size_id = $p->size_id;
            $row->color_name = $p->color_name;
            $row->size_name = $p->size_name;
            $row->color_sn = $p->color_sn;
            $row->size_sn = $p->size_sn;
            $row->product_num = 0;
            $row->consign_num = 0;
            $row->total_price = 0;
            $row->real_product_num = $p->change_num;
            $row->real_consign_num = $p->consign_num;
            $row->package_id = $p->package_id;
            $row->extension_id = $p->extension_id;
            $row->track_id = $p->cp_id;
            $row->track_sn = $p->change_sn;
            $result[$key] = $row;
        }
        foreach ($change_product as $p) {
            $key = "{$p->op_id}-{$p->product_id}-{$p->src_color_id}-{$p->src_size_id}-{$p->parent_cp_id}";
            $result[$key]->real_product_num -= $p->change_num;
            $result[$key]->real_consign_num -= $p->src_consign_num;
        }
        // 处理退货单
        $sql = "SELECT rp.*,r.return_sn
            FROM ".$this->db->dbprefix('order_return_product')." AS rp
            LEFT JOIN ".$this->db->dbprefix('order_return_info')." AS r ON rp.return_id = r.return_id
            WHERE r.order_id = ? AND r.return_status IN (0,1)";
        $query = $this->db->query($sql,array($order_id));
        $return_product = $query->result();
        foreach ($return_product as $p) {
            $key = "{$p->op_id}-{$p->product_id}-{$p->color_id}-{$p->size_id}-{$p->cp_id}";
            $result[$key]->real_product_num -= $p->product_num;
            $result[$key]->real_consign_num -= $p->consign_num;
        }        
        return $result;
    }

    public function all_source($filter)
    {
        $query = $this->db->get_where('order_source',$filter);
        return $query->result();
    }

    public function all_gifts($filter)
    {
        $query = $this->db->get_where('front_campaign',$filter);
        return $query->result();
    }
    
    public function notify_shipping ($order)
    {
        $this->load->model('user_model');
        $this->load->model('mail_template_model');
        $this->load->model('shipping_model');
        $user=$this->user_model->filter(array('user_id'=>$order->user_id));
        $template=$this->mail_template_model->filter(array('template_code'=>'deliver_notice'));
        if(!$template) return;
        $shipping=$this->shipping_model->filter(array('shipping_id'=>$order->shipping_id));
        if ( $user->email && $template->template_content )
        {
            $shipping_express = $shipping?"配送方式：{$shipping->shipping_name}":'';
            $shipping_express .= $order->invoice_no?" 运单号：{$order->invoice_no}":'';
            $common_template=$this->mail_template_model->filter(array('template_code'=>'mail_frame'));
            $content=str_replace('{$content}',$template->template_content,$common_template->template_content);
            $content=str_replace(
                array('{$order.order_id}','{$order.order_sn}','{$order.shipping_express}'),
                array($order->order_id,$order->order_sn,$shipping_express),
                $content
            );
            $content=adjust_path($content);
            $this->db->insert('mail_log',array(
                'mail_from'=>'52kid_service@52kid.cn',
                'mail_to'=>$user->email,
                'template_id'=>$template->template_id,
                'template_subject'=>$template->template_subject,
                'template_content'=>$content,
                'template_priority'=>$template->template_priority,
                'create_admin'=>$this->admin_id,
                'create_date'=>$this->time,
                'status'=>0
            ));           
        }
        if ( $user->mobile && $template->sms_content )
        {
            $content=str_replace(
                array('{$order.order_sn}','{$shipping_name}','{$order.invoice_no}'),
                array($order->order_sn,$shipping?$shipping->shipping_name:'',$order->invoice_no),
                $template->sms_content
            );
            $this->db->insert('sms_log',array(
                'sms_from'=>'',
                'sms_to'=>$user->mobile,
                'template_id'=>$template->template_id,
                'template_content'=>$content,
                'sms_priority'=>$template->template_priority,
                'create_admin'=>$this->admin_id,
                'create_date'=>$this->time,
                'status'=>0
            )); 
        }
    }
    
    /*
     * 获取自动客审（货到付款订单）
     */
    public function get_cod_order() {
        $sql = "SELECT order_id FROM ".$this->db->dbprefix('order_info')." 
                    WHERE order_status = 0 
                    AND create_date > FROM_UNIXTIME(UNIX_TIMESTAMP() - ".TIME_OUT.") 
                    AND create_date < FROM_UNIXTIME(UNIX_TIMESTAMP() - ".MIN_CHECK_TIME.") 
                    AND pay_id = 1 AND LENGTH(user_notice) < 1 AND lock_admin = 0 
                    AND product_num > 0 AND odd = 0 LIMIT ".MAX_LIMIT_ORDER;
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    /*
     * 获取自动客审（非货到付款订单）
     */
    public function get_uncod_order() {
        $sql = "SELECT order_id FROM ".$this->db->dbprefix('order_info')." 
                    WHERE order_status = 0 
                    AND create_date > FROM_UNIXTIME(UNIX_TIMESTAMP() - ".TIME_OUT.") 
                    AND create_date < FROM_UNIXTIME(UNIX_TIMESTAMP() - ".MIN_CHECK_TIME.") 
                    AND pay_id > 1 AND lock_admin = 0 
                    AND product_num > 0 AND odd = 0 AND order_price + shipping_fee <= paid_price LIMIT ".MAX_LIMIT_ORDER;
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    function n_write_log($order,$data,$flag = 0) {
            if(!CHECK_LOG) {
                return true;
            }
            $dir = "/var/log/auto_check_log";
            if (!is_dir($dir))
            {
                    mkdir($dir, 0777);
            }
            $dir .= "/".date("Ym");
            if (!is_dir($dir))
            {
                    mkdir($dir, 0777);
            }

            switch($flag){
            case "1": //成功日志
                    $fp = fopen($dir."/".date("Ymd")."ok.txt", "a");
                    break;
            case "2": //操作日志
                    $fp = fopen($dir."/".date("Ymd")."log.txt", "a");
                    break;
            default: //错误日志
                    $fp = fopen($dir."/".date("Ymd")."error.txt", "a");
            }

            //判断非法订单
            if(is_array($order)){
                    $result = implode($order, "|");
            }else{
                    $result = $order;
            }
            flock($fp, LOCK_EX);
            fwrite($fp, "执行日期：".strftime("%Y%m%d%H%M%S",time())."  类型:".$data."\r\n".$result."\r\n" );
            flock($fp, LOCK_UN);
            fclose($fp);
    }
    
    public function update_transaction ($data, $where_arr) {
            $this->db->update('transaction_info', $data, $where_arr);
    }
	
	/**
	 * 返回某个订单商品所在批次的状态.is_lock,锁定; is_reckoned 结算
	 */
	public function get_batch_order_products( $order_id )
	{
		$sql="select ti.batch_id,pb.batch_code,if( pb.lock_admin is null,false,true ) AS is_lock, 
			pb.lock_date, pi.product_name , pb.is_reckoned, ti.product_number, ti.sub_id, ti.product_id 
			from ty_order_info as oi
			left join ty_transaction_info as ti on oi.order_sn= ti.trans_sn AND ti.trans_status = 2 
			left join ty_product_info as pi on pi.product_id=ti.product_id
			left join ty_purchase_batch as pb on pb.batch_id=ti.batch_id 
			where oi.order_id=".$order_id;
		$query = $this->db_r->query($sql);
		return $query->result_array();
	}
    // 获取订单商品属性
	public function get_order_product_cooperation($op_id) {
		$sql = "SELECT e.provider_cooperation 
		FROM ".$this->db->dbprefix('transaction_info')." AS c 
		LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS d ON c.batch_id=d.batch_id
		LEFT JOIN ".$this->db->dbprefix('product_provider')." AS e ON d.provider_id=e.provider_id
		WHERE c.trans_type = 3 AND c.trans_status = 2 AND c.sub_id=".$op_id;
        $query = $this->db_r->query($sql);
		return $query->row();
	}
	
	// 只更新虚库数量
	public function update_productsub_by_orderid($order_id)
	{
		$sql = "UPDATE ".$this->db->dbprefix('product_sub')." as gl," .
				" (SELECT SUM(consign_num) as consign_num, product_id,color_id,size_id" .
				" FROM ".$this->db->dbprefix('order_product')." WHERE order_id = '".$order_id."'" .
				" GROUP BY product_id,color_id,size_id ) as og" .
				" SET gl.consign_num = gl.consign_num + IF(gl.consign_num>=0,og.consign_num,0)" .
				" WHERE gl.product_id = og.product_id and gl.color_id = og.color_id and gl.size_id = og.size_id";
		$this->db->query($sql);
	}
	
	public function query_consign_mark($order_id){
	    $sql = "SELECT count(*) as ct FROM ".$this->db->dbprefix('order_product')." as op WHERE order_id=$order_id AND consign_mark >0";
	    $query = $this->db->query($sql);
	    $row = $query->row();
	    if(empty($row->ct)){
		return FALSE;
	    }else{
		return TRUE;
	    }
	}
        //  按一级分类、获取销量前十的品牌
        public function get_sales_topten_brand($param) {
            if ( empty($param['start_time']) || empty($param['end_time']) || empty($param['pcate_id']) ) return false;
            /*$sql = "SELECT g.brand_id, SUM(og.product_num) AS num , pb.brand_logo, pb.brand_name  
                FROM ty_order_info oi INNER JOIN ty_order_product og ON oi.order_id = og.order_id 
                INNER JOIN ty_product_info g ON og.product_id = g.product_id 
                INNER JOIN ty_product_category pc ON g.category_id = pc.category_id 
                INNER JOIN ty_product_brand pb ON pb.brand_id = g.brand_id 
                WHERE pc.parent_id = ? AND oi.order_status = 1 AND oi.confirm_date BETWEEN ? AND ? GROUP BY g.brand_id ORDER BY num DESC LIMIT 10";
*/
            $sql = "SELECT g.brand_id, SUM(og.product_num) AS num , pb.brand_logo, pb.brand_name  
                FROM ty_order_info oi INNER JOIN ty_order_product og ON oi.order_id = og.order_id 
                INNER JOIN ty_product_info g ON og.product_id = g.product_id 
                INNER JOIN ty_product_type_link pc ON g.product_id = pc.product_id 
                INNER JOIN ty_product_type pt ON pc.type_id = pt.type_id 				
                INNER JOIN ty_product_brand pb ON pb.brand_id = g.brand_id 
                WHERE pt.parent_id = ? AND oi.order_status = 1 AND oi.confirm_date BETWEEN ? AND ? GROUP BY g.brand_id ORDER BY num DESC LIMIT 10";

				$query = $this->db->query($sql, array($param['pcate_id'], $param['start_time'],$param['end_time']));
            return $query->result_array();
        }
        // 按一级分类、销量(客审口径)前25的商品
        public function get_sales_toptwentyfive_goods($param){
            if ( empty($param['start_time']) || empty($param['end_time']) || empty($param['pcate_id']) ) return false;            
            $sql = "SELECT g.product_id, SUM(og.product_num) AS num, g.product_name, g.shop_price, g.market_price, pg.img_url  
                FROM ty_order_info oi 
                INNER JOIN ty_order_product og ON oi.order_id = og.order_id 
                INNER JOIN ty_product_info g ON og.product_id = g.product_id  
                INNER JOIN ty_product_type_link pc ON g.product_id = pc.product_id 
                INNER JOIN ty_product_type pt ON pc.type_id = pt.type_id 				
                INNER JOIN ty_product_gallery pg ON g.product_id = pg.product_id AND pg.image_type = 'default' 
				INNER JOIN ty_product_sub ps ON g.product_id = ps.product_id 
                WHERE ps.is_on_sale = 1 AND (ps.consign_num>0 OR ps.consign_num=-2 OR ps.gl_num>ps.wait_num) AND pt.parent_id = ? AND oi.order_status = 1 AND oi.confirm_date BETWEEN ? AND ?  
                GROUP BY g.product_id ORDER BY num DESC limit 25";
            $query = $this->db->query($sql, array($param['pcate_id'], $param['start_time'],$param['end_time']));
            return $query->result_array();           
        }
        
        // 销量前７的供应商
        public function get_sales_topseven_provider($param) {
            if ( empty($param['start_time']) || empty($param['end_time']) || empty($param['pcate_id']) ) return false;
            /*$sql = "SELECT g.provider_id, SUM(og.product_num) AS num, pp.display_name, pp.logo  
                FROM ty_order_info oi INNER JOIN ty_order_product og ON oi.order_id = og.order_id 
                INNER JOIN ty_product_info g ON og.product_id = g.product_id 
                INNER JOIN ty_product_category pc ON g.category_id = pc.category_id 
                INNER JOIN ty_product_provider pp ON g.provider_id = pp.provider_id 
                WHERE pc.parent_id = ? AND oi.order_status = 1 AND oi.confirm_date BETWEEN ? AND ? GROUP BY g.provider_id ORDER BY num DESC LIMIT 7";
			*/	
            $sql = "SELECT g.provider_id, SUM(og.product_num) AS num, pp.display_name, pp.logo  
                FROM ty_order_info oi INNER JOIN ty_order_product og ON oi.order_id = og.order_id 
                INNER JOIN ty_product_info g ON og.product_id = g.product_id 
                INNER JOIN ty_product_type_link pc ON g.product_id = pc.product_id 
				INNER JOIN ty_product_type pt ON pc.type_id = pt.type_id 
                INNER JOIN ty_product_provider pp ON g.provider_id = pp.provider_id 
                WHERE pt.parent_id = ? AND oi.order_status = 1 AND oi.confirm_date BETWEEN ? AND ? GROUP BY g.provider_id ORDER BY num DESC LIMIT 7";
				
            $query = $this->db->query($sql, array($param['pcate_id'], $param['start_time'],$param['end_time']));
            return $query->result_array(); 
        }
        // 供应商销售前５的商品
        public function get_sales_topfive_provider_goods($param) {
            if ( empty($param['start_time']) || empty($param['end_time']) || empty($param['provider_id']) ) return false;
            $sql = "SELECT g.product_id, SUM(og.product_num) AS num, g.product_name, g.shop_price,pg.img_url    
                FROM ty_order_info oi INNER JOIN ty_order_product og ON oi.order_id = og.order_id 
                INNER JOIN ty_product_info g ON og.product_id = g.product_id 
                INNER JOIN ty_product_gallery pg ON g.product_id = pg.product_id AND pg.image_type = 'default' 
				INNER JOIN ty_product_sub ps ON g.product_id = ps.product_id 
                WHERE ps.is_on_sale = 1 AND (ps.consign_num>0 OR ps.consign_num=-2 OR ps.gl_num>ps.wait_num) AND g.provider_id = ? AND oi.order_status = 1 AND oi.confirm_date BETWEEN ? AND ? 
                GROUP BY g.product_id ORDER BY num DESC LIMIT 5";
             $query = $this->db->query($sql, array($param['provider_id'], $param['start_time'],$param['end_time']));
             return $query->result_array();           
        }
        // 销量前５的商品
        public function get_sales_topfive_goods($param) {
            if ( empty($param['start_time']) || empty($param['end_time']) ) return false;
            
            $sql = "SELECT og.product_id, SUM(og.product_num) AS num,g.product_name, g.shop_price,pg.img_url   
                FROM ty_order_info oi 
                INNER JOIN ty_order_product og ON oi.order_id = og.order_id 
                INNER JOIN ty_product_info g ON og.product_id = g.product_id 
				INNER JOIN ty_product_sub ps ON g.product_id = ps.product_id 
                INNER JOIN ty_product_gallery pg ON g.product_id = pg.product_id AND pg.image_type = 'default' 
                WHERE ps.is_on_sale = 1 AND (ps.consign_num>0 OR ps.consign_num=-2 OR ps.gl_num>ps.wait_num) AND oi.order_status = 1 AND oi.confirm_date BETWEEN ? AND ? GROUP BY og.product_id ORDER BY num DESC LIMIT 5";
            $query = $this->db->query($sql, array($param['start_time'],$param['end_time']));
            return $query->result_array();             
        }
        // 获取销量前８的品牌
        public function get_sales_topeight_brand($param) {
            if ( empty($param['start_time']) || empty($param['end_time']) ) return false;
            $sql = "SELECT g.brand_id, SUM(og.product_num) AS num , pb.brand_logo, pb.brand_name  
                FROM ty_order_info oi 
                INNER JOIN ty_order_product og ON oi.order_id = og.order_id 
                INNER JOIN ty_product_info g ON og.product_id = g.product_id 
                INNER JOIN ty_product_brand pb ON pb.brand_id = g.brand_id 
                WHERE oi.order_status = 1 AND oi.confirm_date BETWEEN ? AND ? GROUP BY g.brand_id ORDER BY num DESC LIMIT 8";
            $query = $this->db->query($sql, array($param['start_time'],$param['end_time']));
            return $query->result_array();         
        }
        // 按审核时间有库存的显示最新上线的5个商品
        public function get_onsale_last_goods(){
            $sql = "SELECT pii.product_id, IF(SUM(GREATEST(ps.gl_num-ps.wait_num,0))+SUM(GREATEST(ps.consign_num,0)) >0,1,0) AS num, pii.product_name, pii.shop_price, pg.img_url   
                FROM ty_product_info pii 
                INNER JOIN ty_product_sub ps ON pii.product_id = ps.product_id 
                INNER JOIN ty_product_gallery pg ON ps.product_id = pg.product_id AND ps.color_id = pg.color_id AND pg.image_type = 'default' 
                WHERE ps.is_on_sale=1 AND (ps.consign_num>0 OR ps.consign_num=-2 OR ps.gl_num>ps.wait_num) 
                GROUP BY pii.product_id HAVING num > 0 ORDER BY pii.audit_date DESC LIMIT 5";
            $query = $this->db->query($sql);
            return $query->result_array();           
        }
        // 获取前台导航的二级分类
        public function get_front_nav_subtype($pcategory_id) {
            /*$sql = "SELECT pc.category_id, pc.category_name, IF(SUM(GREATEST(ps.gl_num-ps.wait_num,0))+SUM(GREATEST(ps.consign_num,0)) >0,1,0) AS num 
                FROM ty_product_category pc 
                LEFT JOIN ty_product_info g ON pc.category_id = g.category_id 
                LEFT JOIN ty_product_sub ps ON g.product_id = ps.product_id 
                WHERE pc.parent_id = '".$pcategory_id."' AND pc.is_use = 1 AND ps.is_on_sale = 1 AND (ps.consign_num > 0 OR ps.consign_num = -2 OR ps.gl_num > ps.wait_num) 
                GROUP BY pc.category_id HAVING num > 0";*/
            $sql = "SELECT pt.type_id AS category_id, pt.type_name AS category_name, IF(SUM(GREATEST(ps.gl_num-ps.wait_num,0))+SUM(GREATEST(ps.consign_num,0)) >0,1,0) AS num 
                FROM ty_product_info g 
                INNER JOIN ty_product_sub ps ON g.product_id = ps.product_id 
                INNER JOIN ty_product_type_link ptl ON g.product_id = ptl.product_id 
                INNER JOIN ty_product_type pt ON ptl.type_id = pt.type_id 
                WHERE pt.parent_id = ? AND pt.is_show_cat = 1 
                AND g.is_audit = 1 AND ps.is_on_sale = 1 AND (ps.consign_num > 0 OR ps.consign_num =  - 2 OR ps.gl_num > ps.wait_num) 
                GROUP BY pt.type_id HAVING num > 0 ORDER BY pt.sort_order";
            return $this->db->query($sql, array($pcategory_id))->result_array();
        }
        
        /**
         * 根据订单ID取对应供应商的运费规则
         * @param type $order_id
         */
        public function get_shipping_config($order_id)
        {
            $sql = "select pp.shipping_fee_config
                    from ty_order_product as op
                    left join ty_product_info as p on op.product_id = p.product_id
                    left join ty_product_provider as pp on p.provider_id = pp.provider_id
                    where op.order_id = ? limit 1";
            $query = $this->db->query($sql, array($order_id));
            $row = $query->row();
            if(empty($row) || empty($row->shipping_fee_config)){
                return array();
            }
            $result = array();
            foreach(json_decode($row->shipping_fee_config) as $config){
                $result[intval($config->regionId)] = array(floatval($config->fee), floatval($config->price));
            }
            return $result;
        }
        
        /**
         * 取订单的合作方式ID
         * @param int $order_id     订单ID
         * @return int 
         */
        public function get_order_cooperation_id($order_id) {
        $sql = "select pp.provider_cooperation
                    from ty_order_product as op
                    left join ty_product_info as p on op.product_id = p.product_id
                    left join ty_product_provider as pp on p.provider_id = pp.provider_id
                    where op.order_id = ? limit 1";
        $query = $this->db->query($sql, array($order_id));
        $row = $query->row();
        return $row ? $row->provider_cooperation : 0;
    }

    //批量更新信息
    public function all_invoice_order ($update){
        $this->db->update_batch('order_info', $update, 'order_sn');
        return true;
    }

    //获取导入订单的id
    public function all_order_id ($update) {
        $this->db->select('order_id,order_status,shipping_status,pay_status,');
        $this->db->where_in('order_sn', $update);
        return $this->db->get('order_info')->result_array();
    }

    //获取在指定时间内 付款完成的订单
    public function get_time_order ( $now_time,$pass_time) {
        $row = array();
        //已付款订单 商品数量
        $payment_sql = "SELECT op.`product_id`,SUM(op.`product_num`) AS num 
                        FROM ty_order_info AS oi
                        LEFT JOIN ty_order_product AS op ON oi.`order_id`=op.`order_id`
                        WHERE oi.`pay_status`=1 AND oi.`finance_date` BETWEEN '".$pass_time."' AND '".$now_time."' AND op.`order_id` IS NOT NULL GROUP BY op.`product_id`";
        $payment_query = $this->db->query($payment_sql);
        $row['payment_row'] = $payment_query->result_array();

        //已退款订单 商品数量
        $refund_sql = " SELECT rp.`product_id`,SUM(rp.`product_num`) AS num 
                        FROM ty_order_return_info AS ri
                        LEFT JOIN ty_order_return_product AS rp ON ri.`return_id`=rp.`return_id`
                        WHERE ri.`pay_status`=1 AND ri.`finance_date` BETWEEN '".$pass_time."' AND '".$now_time."' AND rp.`return_id` IS NOT NULL GROUP BY rp.`product_id`";
        $refund_query = $this->db->query($refund_sql);
        $row['refund_row'] = $refund_query->result_array();

        return $row;
    }
    //获取超时未支付的订单
    public function get_unpay_timeout_order($filter){
        $sql = "SELECT o.* FROM ty_order_info o "
                . "INNER JOIN ty_payment_info p ON o.pay_id = p.pay_id "
                . "WHERE o.create_date <= '" . $filter['date_end']. "' "
                . "AND o.pay_status = 0 AND is_ok = 0 AND order_status = 0 "
                . "AND lock_admin = 0 AND order_price + shipping_fee - paid_price > 0 LIMIT ".ORDER_INVALID_LIMIT;
        return $this->db->query($sql)->result_array();
    }
    
    //获取订单商品总重量
    public function get_order_product_weight($order_id){
        $sql = "SELECT SUM(p.`product_weight`) AS weight "
               . "FROM ty_order_product op "
               . "LEFT JOIN ty_product_info p ON op.product_id = p.`product_id` "
               . "WHERE op.order_id = '".$order_id."'";
        return $this->db->query($sql)->row_array();
    }
    //取出订单报名人信息
    public function order_client($order_id)
    {
        $sql = "SELECT * 
                FROM ".$this->db->dbprefix('order_client_info')." 
                WHERE order_id = ?";
        $query = $this->db->query($sql, array(intval($order_id)));
        return $query->result();
    }
    
    public function get_orders_info($order_ids)
    {
        $sql = "SELECT distinct o.order_id, o.*, spi.mailno, spi.dist_code, s.shipping_code,s.shipping_name,sc.source_code,sc.source_name,pr.region_name as province_name,cr.region_name as city_name,dr.region_name as district_name, ps.pick_cell 
                FROM ".$this->db->dbprefix('order_info')." AS o
                INNER JOIN ".$this->db->dbprefix('order_source')." AS sc ON o.source_id = sc.source_id                
                INNER JOIN ".$this->db->dbprefix('shipping_info')." AS s ON o.shipping_id = s.shipping_id
                INNER JOIN ".$this->db->dbprefix('region_info')." AS pr ON o.province = pr.region_id
                INNER JOIN ".$this->db->dbprefix('region_info')." AS cr ON o.city = cr.region_id
                INNER JOIN ".$this->db->dbprefix('region_info')." AS dr ON o.district = dr.region_id
		INNER JOIN ".$this->db->dbprefix('pick_sub')." AS ps ON o.order_sn = ps.rel_no 
                INNER JOIN ".$this->db->dbprefix('shipping_package_interface')." AS spi ON o.order_id = spi.order_id 
                WHERE spi.filter_status = 1000 AND o.order_id IN (".implode(",", $order_ids).")";
        $query = $this->db->query($sql);
        return $query->result();
    }
}
