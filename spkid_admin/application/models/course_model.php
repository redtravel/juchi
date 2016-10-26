<?php
#doc
#	classname:	Product_model
#	scope:		PUBLIC
#
#/doc

class Course_model extends CI_Model
{
	public function filter($filter)
	{
		$query = $this->db->get_where('product_info', $filter, 1);
		return $query->row();
	}
        
    public function filter_product_subs($filter) 
    {
        $query = $this->db->get_where('product_sub', $filter);
        return $query->result();
    }
	
	public function product_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_info')." AS p 
				LEFT JOIN ".$this->db->dbprefix('product_category')." AS c ON p.category_id = c.category_id
				LEFT JOIN ".$this->db->dbprefix('product_style')." AS s ON p.style_id = s.style_id
				LEFT JOIN ".$this->db->dbprefix('product_season')." AS ss ON p.season_id = ss.season_id
				LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id
				LEFT JOIN ".$this->db->dbprefix('product_sub')." AS sub ON sub.product_id = p.product_id
				LEFT JOIN ".$this->db->dbprefix('product_cost')." AS cost ON cost.product_id = p.product_id
				LEFT JOIN ".$this->db->dbprefix('product_provider')." AS prov ON prov.provider_id = p.provider_id
				LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS batch ON batch.batch_id = cost.batch_id 
				LEFT JOIN ty_register_code AS reg ON reg.id = p.register_code_id ";
		$where = " WHERE 1 AND p.genre_id = 2 ";
		$group_by = " GROUP BY p.product_id ";
		$param = array();

		//商品ID
		if (!empty($filter['product_id']))
		{
			$where .= " AND p.product_id = ? ";
			$param[] = $filter['product_id'];
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

		if (!empty($filter['category_id']))
		{
			$where .= " AND (p.category_id = ? OR p.category_id IN (select category_id FROM ".$this->db->dbprefix('product_category')." WHERE parent_id = ?))";
			$param[] = $filter['category_id'];
			$param[] = $filter['category_id'];
		}

		//上/下架
		if (!empty($filter['is_on_sale']))
		{
			if ($filter['is_on_sale'] == 'is_on_sale_yes') {
				$where .= " AND sub.is_on_sale = 1 ";
			}elseif ($filter['is_on_sale'] == 'is_on_sale_no') {
				$where .= " AND sub.is_on_sale = 0 ";
			}
		}
    
        if (!empty($filter['source_id']))
		{
			$where .= " AND p.source_id = ? ";
			$param[] = $filter['source_id'];
		}

		if (!empty($filter['product_status']) && in_array($filter['product_status'],array('is_best','is_new','is_hot','is_promote','is_offcode','is_gifts','is_stop','is_audit_yes','is_audit_no','is_pic_yes','is_pic_no')))
		{
			if ($filter['product_status'] == 'is_audit_yes') {
				$where .= " AND p.is_audit = 1 ";
			}elseif ($filter['product_status'] == 'is_audit_no') {
				$where .= " AND p.is_audit = 0 ";
			}elseif ($filter['product_status'] == 'is_pic_yes') {
				$where .= " AND sub.is_pic = 1 ";
			}elseif ($filter['product_status'] == 'is_pic_no') {
				$where .= " AND sub.is_pic = 0 ";
			}else {
				$where .= " AND p.{$filter['product_status']} = 1 ";
			}
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'p.product_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);
		
		$sql = "SELECT COUNT(T.ct) AS ct FROM (SELECT p.product_id AS ct " . $from . $where . $group_by . ") as T";
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "SELECT p.*,c.category_name,s.style_name,ss.season_name,b.brand_name,prov.provider_name,reg.medical1,reg.medical2 "
				. $from . $where . $group_by . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param); 

		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function insert ($data)
	{
		$this->db->insert('product_info', $data);
		return $this->db->insert_id();
	}
	
	public function update ($data, $product_id)
	{
		$this->db->update('product_info', $data, array('product_id' => $product_id));
	}
	
	public function delete ($product_id)
	{
		$this->db->delete('product_info', array('product_id' => $product_id));
	}

	public function insert_price_record($update)
	{
		$this->db->insert('product_price_record',$update);
	}

	public function filter_sub($filter=array())
	{
		$query = $this->db->get_where('product_sub',$filter,1);
		return $query->row();
	}

	//锁sub,支持三种方式
	public function lock_sub($filter)
	{
		if(isset($filter['sub_id']) && is_array($filter['sub_id'])){
			$sql = "SELECT * FROM ".$this->db->dbprefix('product_sub')." WHERE sub_id ".db_create_in($filter['sub_id'])." FOR UPDATE";
			$query = $this->db->query($sql);
			return $query->result();
		}elseif(isset($filter['sub_id'])){
			$sql = "SELECT * FROM ".$this->db->dbprefix('product_sub')." WHERE sub_id = ? LIMIT 1 FOR UPDATE";
			$param = array($filter['sub_id']);
			$query = $this->db->query($sql,$param);
			return $query->row();
		}else{
			$sql = "SELECT * FROM ".$this->db->dbprefix('product_sub')." WHERE product_id = ? LIMIT 1 FOR UPDATE";
			$param = array($filter['product_id']);
			$query = $this->db->query($sql,$param);
			return $query->row();
		}
	}

	public function insert_sub($update)
	{
		$this->db->insert('product_sub',$update);
		return $this->db->insert_id();
	}

	public function update_sub($update, $sub_id)
	{
        if(is_array($sub_id)){
            $this->db->where_in('sub_id', $sub_id)->update('product_sub', $update);
        }else{
            $this->db->update('product_sub', $update, array('sub_id'=>$sub_id));
        }		
	}

	public function update_where_sub($update, $filter)
	{
		$this->db->update('product_sub', $update, $filter);
	}

	public function delete_sub($sub_id)
	{
		$this->db->delete('product_sub', array('sub_id'=>$sub_id));
	}

	public function filter_gallery($filter)
	{
		$query = $this->db->get_where('product_gallery', $filter);
		return $query->row();
	}

	public function insert_gallery($update)
	{
		$this->db->insert('product_gallery',$update);
		return $this->db->insert_id();
	}

	public function update_gallery($update,$image_id)
	{
		$this->db->update('product_gallery', $update, array('image_id'=>$image_id));
	}

	public function delete_gallery($image_id)
	{
		$this->db->delete('product_gallery',array('image_id'=>$image_id));
	}

	public function all_gallery($filter, $product_ids=NULL)
	{
		if(isset($filter['product_id']) && is_array($filter['product_id']))
		{
			$product_ids = $filter['product_id'];
			unset($filter['product_id']);			
		}
		foreach ($filter as $key => $value) {
			if (in_array($key,array('color_id'))) {
				unset($filter[$key]);
				$key = 'pg.'.$key;
				$filter[$key] = $value;
			}
		}
		$this->db
			->select('pg.*, color_name')
			->from('product_gallery AS pg')
			->join('product_color AS pc','pg.color_id = pc.color_id','left')
			->where($filter)
			->order_by('sort_order', 'asc');

		if ($product_ids) $this->db->where_in('pg.product_id',$product_ids);
		$query = $this->db->get();
		return $query->result();
	}

	public function all_sub($filter,$product_ids = array())
	{
		if (isset($filter['product_id']) && is_array($filter['product_id'])) {
			$product_ids = $filter['product_id'];
			unset($filter['product_id']);
		}
		foreach ($filter as $key => $value) {
			if (in_array($key,array('color_id','size_id'))) {
				unset($filter[$key]);
				$key = ($this->db->dbprefix('product_sub')).'.'.$key;
				$filter[$key] = $value;
			}
		}
		
		$this->db
			->select('ps.*, color_name, size_name')
			->from('product_sub AS ps')
			->join('product_color AS pc','ps.color_id = pc.color_id','left')
			->join('product_size AS psize', 'psize.size_id = ps.size_id', 'left')
			->where($filter);
		if ($product_ids) $this->db->where_in('ps.product_id',$product_ids);
		$query = $this->db->get();
		return $query->result();
	}
    
    /*
     * 根据给定条件查询商品子表
     * 关联color,size,purchase
     */
    public function product_sub_for_scan($filter)
	{
		$this->db_r
			->select('ps.product_id,pi.product_name,pr.provider_name,pr.provider_code,pb.brand_name,ps.color_id,ps.size_id,pps.expire_date,pbs.production_batch, 
			    color_name, size_name,product_sn,pps.product_number as p_number,pps.product_finished_number,pbs.product_number as box_number,
			    ps.provider_barcode,pi.provider_productcode,pi.create_admin,pi.create_date,pi.audit_admin,pi.audit_date')
			->from('product_sub AS ps')
			->join('product_info AS pi','ps.product_id=pi.product_id')
			->join('product_color AS pc','ps.color_id = pc.color_id','left')
			->join('product_size AS psize', 'psize.size_id = ps.size_id', 'left')
			->join('purchase_sub AS pps','ps.product_id=pps.product_id and ps.color_id=pps.color_id and ps.size_id=pps.size_id','left')
			->join('product_provider AS pr','pr.provider_id=pi.provider_id','left')
			->join('product_brand AS pb','pb.brand_id=pi.brand_id','left')
			->join('purchase_box_sub AS pbs','pbs.product_id=pps.product_id and pbs.color_id=pps.color_id and pbs.size_id=pps.size_id','left')
			->where($filter);
		$query = $this->db_r->get();
		return $query->result();
	}
    	
	public function filter_link ($filter=array())
	{
		$query = $this->db->get_where('product_link', $filter, 1);
		return $query->row();
	}
	
	public function insert_link ($update)
	{
		$this->db->insert('product_link', $update);
		return $this->db->insert_id();
	}
	
	public function update_link ($update, $link_id)
	{
		$this->db->update('product_link', $update, array('link_id'=>$link_id));
	}
	
	public function delete_link ($link_id)
	{
		$this->db->delete('product_link', array('link_id'=>$link_id));
	}

	public function link_product($product_id)
	{
		$this->db
			->select('pi.product_id,product_sn,product_name,provider_productcode, shop_price,is_bothway, link_id')
			->from('product_link AS pl')
			->join('product_info AS pi', 'pl.link_product_id = pi.product_id')
			->where(array('pl.product_id'=>$product_id));
		$query = $this->db->get();
		return $query->result();
	}

	public function link_by_product($product_id)
	{
		$this->db
			->select('pi.product_id,product_sn,product_name,provider_productcode, shop_price, is_bothway, link_id')
			->from('product_link AS pl')
			->join('product_info AS pi', 'pl.product_id = pi.product_id')
			->where(array('pl.link_product_id'=>$product_id));
		$query = $this->db->get();
		return $query->result();
	}

    public function get_index_goods(){
        $max = MOBILE_INDEX_MAX_PRODUCT_NUM;
        

        // 优先获得打标的产品 max 个
        $sql = 'SELECT p.*,is_best AS is_zhanpin,pg.`img_url` 
		    FROM ty_product_info AS p 
		    LEFT JOIN ty_product_sub AS ps USING(product_id) 
		    LEFT JOIN ty_product_gallery AS pg ON ps.`product_id`=pg.`product_id` AND ps.`color_id`=pg.`color_id` WHERE pg.image_type="default"
		    AND p.`is_audit`=1 AND  ps.is_on_sale = 1 AND (ps.consign_num>0 OR ps.consign_num=-2 OR ps.gl_num>ps.wait_num) AND 
		    (p.is_best=1 OR p.is_hot=1 OR p.is_new=1 OR p.is_offcode=1 OR p.is_gifts=1) AND p.genre_id=1 group by p.product_id ORDER BY RAND()'. " limit $max";

        $query = $this->db->query($sql);
        $res = $query->result_array();

        // 若不足max个
        $total = count($res);
        if ($total<$max){
            $left = $max-$total;
            $sql = "SELECT p.*,is_best AS is_zhanpin,pg.`img_url` 
			FROM ty_product_info AS p LEFT JOIN ty_product_sub AS ps USING(product_id) 
			LEFT JOIN ty_product_gallery AS pg ON ps.`product_id`=pg.`product_id` AND ps.`color_id`=pg.`color_id` 
			WHERE  pg.image_type='default' and
			p.`is_audit`=1 AND  ps.is_on_sale = 1 AND (ps.consign_num>0 OR ps.consign_num=-2 OR ps.gl_num>ps.wait_num) AND p.genre_id=1 
			group by p.product_id ORDER BY RAND() limit $left";
            $query = $this->db->query($sql);

            $res_left  = $query->result_array();

            if( !empty($res_left) ){
                $res = array_merge( $res, $res_left );
            }
        }
        foreach ($res as $key => &$p) {        	
        	$p['is_promote'] = $p['is_promote'] && strtotime($p['promote_start_date'])<=time() && strtotime($p['promote_end_date'])>=time() ;
			$p['shop_price'] = $p['is_promote'] ? $p['promote_price'] : $p['shop_price'];
			//$p['last_shop_price'] = $p['shop_price'];
        }
		return $res;
    }

	public function link_search ($filter)
	{

		$from = " FROM ".$this->db->dbprefix('product_info')." AS p ";
		$where = " WHERE p.product_id != '{$filter['product_id']}' AND 
					NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('product_link')." AS pl 
					WHERE (pl.product_id='{$filter['product_id']}' AND pl.link_product_id=p.product_id) 
					OR (pl.is_bothway=1 AND pl.link_product_id='{$filter['product_id']}' AND pl.product_id=p.product_id)) ";
		$param = array();

		if (!empty($filter['product_sn']))
		{
			$where .= " AND p.product_sn LIKE ? ";
			$param[] = '%' . $filter['product_sn'] . '%';
		}
                
                if (!empty($filter['product_id2']))
		{
			$where .= " AND p.product_id = ? ";
			$param[] = $filter['product_id2'];
		}

		if (!empty($filter['product_name']))
		{
			$where .= " AND p.product_name LIKE ? ";
			$param[] = '%' . $filter['product_name'] . '%';
		}

		if (!empty($filter['provider_productcode']))
		{
			$where .= " AND p.provider_productcode LIKE ? ";
			$param[] = '%' . $filter['provider_productcode'] . '%';
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
		$sql = "SELECT p.product_id,p.product_sn,p.product_name,p.provider_productcode,shop_price "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function insert_onsale_record($update)
	{
		$this->db->insert('product_onsale_record',$update);
		return $this->db->insert_id();
	}

	public function onsale_record_list($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_onsale_record')." AS r 
				LEFT JOIN ".$this->db->dbprefix('product_sub')." AS ps ON r.sub_id = ps.sub_id
				LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON ps.product_id = p.product_id
				LEFT JOIN ".$this->db->dbprefix('product_category')." AS ct ON p.category_id = ct.category_id
				LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id
				LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON ps.color_id = c.color_id
				LEFT JOIN ".$this->db->dbprefix('product_size')." AS s ON ps.size_id = s.size_id
				LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON r.create_admin = a.admin_id
				";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['product_sn']))
		{
			$where .= " AND p.product_sn LIKE ? ";
			$param[] = '%' . $filter['product_sn'] . '%';
		}
		

		if (!empty($filter['start_date']))
		{
			$where .= " AND r.create_date > ? ";
			$param[] = $filter['start_date'];
		}

		if (!empty($filter['end_date']))
		{
			$where .= " AND r.create_date < ? ";
			$param[] = $filter['end_date'];
		}

		if (!empty($filter['brand_id']))
		{
			$where .= " AND p.brand_id = ? ";
			$param[] = $filter['brand_id'];
		}

		if (!empty($filter['category_id']))
		{
			$where .= " AND p.category_id = ? ";
			$param[] = $filter['category_id'];
		}

		if (!empty($filter['create_admin']))
		{
			$where .= " AND a.admin_name = ? ";
			$param[] = $filter['create_admin'];
		}

		if (isset($filter['sr_onsale']) && $filter['sr_onsale']!=-1)
		{
			$where .= " AND r.sr_onsale = ? ";
			$param[] = $filter['sr_onsale'];
		}
		
		$filter['sort_by'] = empty($filter['sort_by']) ? 'r.onsale_id' : trim($filter['sort_by']);
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
		$sql = "SELECT r.*, p.product_name,p.product_sn,p.provider_productcode,s.size_name,c.color_name,
				ct.category_name,b.brand_name,a.admin_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function price_record_list($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_price_record')." AS r 
				LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON r.product_id = p.product_id
				LEFT JOIN ".$this->db->dbprefix('product_category')." AS ct ON p.category_id = ct.category_id
				LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id
				LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON r.create_admin = a.admin_id
				";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['product_sn']))
		{
			$where .= " AND p.product_sn LIKE ? ";
			$param[] = '%' . $filter['product_sn'] . '%';
		}
		
		if (!empty($filter['start_date']))
		{
			$where .= " AND r.create_date > ? ";
			$param[] = $filter['start_date'];
		}

		if (!empty($filter['end_date']))
		{
			$where .= " AND r.create_date < ? ";
			$param[] = $filter['end_date'];
		}

		if (!empty($filter['brand_id']))
		{
			$where .= " AND p.brand_id = ? ";
			$param[] = $filter['brand_id'];
		}

		if (!empty($filter['category_id']))
		{
			$where .= " AND p.category_id = ? ";
			$param[] = $filter['category_id'];
		}

		if (!empty($filter['create_admin']))
		{
			$where .= " AND a.admin_name = ? ";
			$param[] = $filter['create_admin'];
		}

		if (isset($filter['sr_onsale']) && $filter['sr_onsale']!=-1)
		{
			$where .= " AND r.sr_onsale = ? ";
			$param[] = $filter['sr_onsale'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'r.price_id' : trim($filter['sort_by']);
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
		$sql = "SELECT r.*, p.product_name,p.product_sn,p.provider_productcode,ct.category_name,b.brand_name,a.admin_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function all_product($filter)
	{
		if(isset($filter['product_id']) && is_array($filter['product_id']))
		{
			$this->db->where_in('product_id', $filter['product_id']);
			unset($filter['product_id']);
		}
		$query = $this->db->get_where('product_info',$filter);
		return $query->result();
	}

	public function lock_product($product_id)
	{
		$query = $this->db->query("SELECT * FROM ".$this->db->dbprefix("product_info")." WHERE product_id = ? FOR UPDATE", array($product_id));
		return $query->row();
	}
	
	public function get_product_price($product_id)
	{
		$sql="SELECT b.batch_code,d.provider_name,d.provider_cooperation,coop.cooperation_name,c.cost_price,c.consign_price,c.consign_rate,c.product_cess,c.product_income_cess ";
		$from=" FROM ".$this->db->dbprefix("product_cost")." AS c INNER JOIN ty_purchase_batch AS b ON c.batch_id = b.batch_id";
		$from.=" LEFT JOIN ".$this->db->dbprefix("product_info")." AS p ON c.product_id = p.product_id";
		$from.=" LEFT JOIN ".$this->db->dbprefix("product_provider")." AS d ON c.provider_id = d.provider_id";
		$from.=" LEFT JOIN ".$this->db->dbprefix("product_cooperation")." AS coop ON d.provider_cooperation = coop.cooperation_id";
		$where=" WHERE c.product_id = ?";
		$sql=$sql.$from.$where;
		$query = $this->db->query($sql, array($product_id));
		$list = $query->result();
		return array('list' => $list);
	}
	
	public function purcahse_order_list($filter){
		
		$from = " FROM ".$this->db->dbprefix('product_info')." AS p 
				LEFT JOIN ".$this->db->dbprefix('product_sub')." AS sub ON sub.product_id=p.product_id 
				LEFT JOIN ".$this->db->dbprefix('product_color')." AS color ON color.color_id = sub.color_id
				LEFT JOIN ".$this->db->dbprefix('product_size')." AS size ON size.size_id = sub.size_id
				LEFT JOIN ".$this->db->dbprefix('product_category')." AS c ON p.category_id = c.category_id
				LEFT JOIN ".$this->db->dbprefix('product_style')." AS s ON p.style_id = s.style_id
				LEFT JOIN ".$this->db->dbprefix('product_season')." AS ss ON p.season_id = ss.season_id
				LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id
				LEFT JOIN ".$this->db->dbprefix('product_cost')." AS cost ON cost.product_id = p.product_id
				LEFT JOIN ".$this->db->dbprefix('product_provider')." AS pro ON pro.provider_id = cost.provider_id
				LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS batch ON batch.batch_id = cost.batch_id
				";
		$where = " WHERE 1 ";
		$param = array();

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

		if (!empty($filter['provider_productcode']))
		{
			$where .= " AND p.provider_productcode LIKE ? ";
			$param[] = '%' . $filter['provider_productcode'] . '%';
		}

		if (!empty($filter['category_id']))
		{
			$where .= " AND (p.category_id = ? OR p.category_id IN (select category_id FROM ".$this->db->dbprefix('product_category')." WHERE parent_id = ?))";
			$param[] = $filter['category_id'];
			$param[] = $filter['category_id'];
		}

		if (!empty($filter['brand_id']))
		{
			$where .= " AND p.brand_id = ? ";
			$param[] = $filter['brand_id'];
		}

		if (!empty($filter['style_id']))
		{
			$where .= " AND p.style_id = ? ";
			$param[] = $filter['style_id'];
		}

		if (!empty($filter['product_sex']))
		{
			$where .= " AND p.product_sex = ? ";
			$param[] = $filter['product_sex'];
		}

		if (!empty($filter['season_id']))
		{
			$where .= " AND p.season_id = ? ";
			$param[] = $filter['season_id'];
		}

		if (!empty($filter['provider_id']))
		{
			$where .= " AND cost.provider_id = ? ";
			$param[] = $filter['provider_id'];
		}
		
		if (!empty($filter['batch_id']))
		{
			$where .= " AND cost.batch_id = ? ";
			$param[] = $filter['batch_id'];
		}
		if (!empty($filter['batch_code']))
		{
			$where .= " AND batch.batch_code = ? ";
			$param[] = $filter['batch_code'];
		}

		if (!empty($filter['product_status']) && in_array($filter['product_status'],array('is_best','is_new','is_hot','is_promote','is_offcode','is_gifts','is_stop','is_audit_yes','is_audit_no')))
		{
			if ($filter['product_status'] == 'is_audit_yes') {
				$where .= " AND p.is_audit = 1 ";
			}elseif ($filter['product_status'] == 'is_audit_no') {
				$where .= " AND p.is_audit = 0 ";
			}else {
				$where .= " AND p.{$filter['product_status']} = 1 ";
			}
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'p.product_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

		$sql = "SELECT p.product_sn,p.is_audit,pro.provider_id,pro.provider_code,pro.provider_cooperation,p.provider_productcode,color.color_name,color.color_sn,size.size_name,size.size_sn "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'];
		$query = $this->db->query($sql, $param);
		$list = $query->result_array();
		$query->free_result();
		return $list;
	}
        
    public function product_cost_record_list($filter)
	{
	    $this->load->helper("product");
		$from = " FROM ".$this->db->dbprefix('product_cost_record')." AS cr 
				LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON cr.product_id = p.product_id
				LEFT JOIN ".$this->db->dbprefix('product_category')." AS ct ON p.category_id = ct.category_id
				LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id
                LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS pb ON pb.batch_id = cr.batch_id    
                LEFT JOIN ".$this->db->dbprefix('product_provider')." AS pp ON pp.provider_id = pb.provider_id
                LEFT JOIN ".$this->db->dbprefix('product_cooperation')." AS pc ON pc.cooperation_id = pp.provider_cooperation
				LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON cr.create_admin = a.admin_id
				";
		$where = " WHERE 1 ";
		$param = array();
		if (!empty($filter['product_sn']))
		{
			$where .= " AND p.product_sn LIKE ? ";
			$param[0] = '%' . $filter['product_sn'] . '%';
		}
		
        if (!empty($filter['batch_code']))
		{
			$where .= " AND pb.batch_code LIKE ? ";
			$param[1] = '%' . $filter['batch_code'] . '%';
		}

		if (!empty($filter['start_date']))
		{
			$where .= " AND cr.create_date >= ? ";
			$param[2] = $filter['start_date'];
		}

		if (!empty($filter['end_date']))
		{
			$where .= " AND cr.create_date <= ? ";
			$param[3] = $filter['end_date'];
		}

		if (!empty($filter['brand_id']))
		{
			$where .= " AND p.brand_id = ? ";
			$param[4] = $filter['brand_id'];
		}

		if (!empty($filter['category_id']))
		{
			$where .= " AND p.category_id = ? ";
			$param[5] = $filter['category_id'];
		}

		if (!empty($filter['create_admin']))
		{
			$where .= " AND a.admin_name like ? ";
			$param[6] = '%'.$filter['create_admin'].'%';
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'cr.price_id' : trim($filter['sort_by']);
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
		$sql = "SELECT cr.*,p.product_sn,pb.batch_code,p.product_name,
		    p.shop_price,p.market_price,p.is_promote,p.promote_price,p.promote_start_date,p.promote_end_date,
		    b.brand_name, pp.provider_name,ct.category_name,pc.cooperation_name,a.admin_name,a.realname "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];  
		$query = $this->db->query($sql, $param);
                
		$list = $query->result();
		foreach ($list as $p) {
		    format_product($p);
		}
		
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
        
    /**
     * 根据products_sn获取审核后的product_id集合
     * @param type $products_sn
     * @return type 
     */
    function get_product_ids_by_sn( $products_sn ){
        $sql = "SELECT product_id,product_sn FROM ty_product_info WHERE is_audit = 1 AND product_sn ".db_create_in($products_sn );
        $res = $this->db_r->query($sql )->result_array();
        return $res;
    }
    
    /**
     * 获取批次下审核后的product_id集合
     * @param type $products_sn
     * @return type 
     */
    function get_product_ids_by_batch( $products_sn , $batch_id ){
        $sql = "SELECT
                    p.product_sn
                    FROM ty_product_info p
                    LEFT JOIN ty_product_cost pc
                        ON p.product_id = pc.product_id
                    WHERE is_audit = 1
                        AND pc.batch_id = ?
                        AND product_sn".db_create_in($products_sn );
        $res = $this->db_r->query($sql ,array($batch_id ) )->result_array();
        return $res;
    }
    
    /**
     * 根据products_sn获取ids
     * @param type $products_sn
     * @return type 
     */
    function get_product_ids( $products_sn ){
        $sql = "SELECT product_id,product_sn,provider_id FROM ty_product_info WHERE product_sn ".db_create_in($products_sn );
        $res = $this->db_r->query($sql )->result_array();
        return $res;
    }
    
    /**
    * 根据product_sn,size_sn,color_sn获取sub_id
    * @param type $pro_sn
    * @param type $size_sn
    * @param type $color_sn
    * @return type 
    */
    public function get_product_sku($pro_sn, $size_sn, $color_sn) {
        $sql = "SELECT ps.sub_id FROM ty_product_sub ps 
                    INNER JOIN ty_product_info i ON ps.product_id = i.product_id
                    INNER JOIN ty_product_size s ON ps.size_id = s.size_id
                    INNER JOIN ty_product_color c ON ps.color_id = c.color_id
                    WHERE 
                    i.product_sn = ? AND s.size_sn = ? AND c.color_sn = ? ";
        $row = $this->db_r->query($sql, array($pro_sn, $size_sn, $color_sn))->row();
        return empty($row)?0:$row->sub_id;
    }

    /**
    * 根据product_sn,size_sn,color_sn获取sub_id
    * @param type $pro_sn
    * @param type $size_sn
    * @param type $color_sn
    * @return type 
    */
    public function get_product_id_by_sku($pro_sn, $size_sn, $color_sn) {
        $sql = "SELECT ps.product_id FROM ty_product_sub ps 
                    INNER JOIN ty_product_info i ON ps.product_id = i.product_id
                    INNER JOIN ty_product_size s ON ps.size_id = s.size_id
                    INNER JOIN ty_product_color c ON ps.color_id = c.color_id
                    WHERE 
                    i.product_sn = ? AND s.size_sn = ? AND c.color_sn = ? ";

        $row = $this->db_r->query($sql, array($pro_sn, $size_sn, $color_sn))->row();
        return $row->product_id;
    }

    /**
     * 根据pro_ids获取未审核商品的总数
     * 
     * @param type $pro_ids
     * @return type 
     */
    public function is_no_audit_pros($pro_ids ){
        $sql = "SELECT COUNT(product_id ) AS num FROM ty_product_info WHERE is_audit = 0 AND product_id ".db_create_in($pro_ids );
        $row = $this->db_r->query($sql )->row();
        return $row->num;
        
    }
        
	public function audit_pic($product_id,$color_id,$is_pic){
		$result = array();
		if(!$is_pic){
		    $query = $this->db->get_where('product_gallery', array("product_id"=>$product_id,"color_id"=>$color_id));
		    $gallery = $query->result();
		    $exists_def = FALSE;
		    $exists_tonal = FALSE;
		    foreach ($gallery as $value) {
			if($value->image_type == 'default'){
			    $exists_def = TRUE;
			}else if($value->image_type == 'tonal'){
			    $exists_tonal = TRUE;
			}
		    }
		    if(!$exists_def) sys_msg('商品默认图不存在，不允许设置已拍摄',1); 
		    // if(!$exists_tonal) sys_msg('商品色片不存在，不允许设置已拍摄',1); 
		}
		$sql="SELECT max(sub.is_pic) as is_pic FROM ".$this->db->dbprefix('product_sub')." AS sub WHERE sub.product_id=? AND sub.color_id=?";
		$param = array();
		$param[]=$product_id;
		$param[]=$color_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		if($row->is_pic == $is_pic){
		    $data = array();
		    $data["is_pic"]=!$is_pic;
		    $this->db->update('product_sub', $data, array('product_id' => $product_id,'color_id' => $color_id));
		    $result["result"] = !$is_pic== true?1:0;
		}else{
		    $result["result"] = $is_pic == true?1:0;
		}
		return $result;
	}
	
	public function show_product_type_link($filter){
		$this->load->model('product_model');
	    
		$select = "SELECT p.product_id,p.product_name,p.product_sn,b.brand_name,p.product_sex,ct.category_name ";
		$from = " FROM ".$this->db->dbprefix('product_info')." AS p
		    LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id
		    LEFT JOIN ".$this->db->dbprefix('product_category')." AS ct ON p.category_id = ct.category_id";
		$param = array();
		$where =" WHERE 1 ";
		if (!empty($filter['skip_set']) && $filter['skip_set'] == true)
		{
		     $where.= " AND NOT EXISTS(SELECT * FROM ".$this->db->dbprefix('product_type_link')." AS l WHERE l.product_id = p.product_id) ";
		}else{
		    $val_type = FALSE;
		    $where_type = " AND EXISTS (SELECT * FROM ".$this->db->dbprefix('product_type_link')." AS l  
						    LEFT JOIN ".$this->db->dbprefix('product_type')." AS pt ON pt.type_id = l.type_id 
						    WHERE l.product_id = p.product_id ";
		    $param_type = array();
		    if (!empty($filter['first_type']) && $filter['first_type'] >0)
		    {
			    $val_type = TRUE ;
			    $where_type .=" AND pt.parent_id = ? ";
			    $param_type[] = $filter['first_type'];
		    }
		    if (!empty($filter['second_type']) && $filter['second_type'] >0)
		    {
			    $val_type = TRUE ;
			    $where_type .=" AND pt.parent_id2 = ? ";
			    $param_type[] = $filter['second_type'];
		    }
		     if (!empty($filter['three_type']) && $filter['three_type'] >0)
		    {
			    $val_type = TRUE ;
			    $where_type .=" AND pt.type_id = ? ";
			    $param_type[] = $filter['three_type'];
		    }
		    $where_type .= " ) ";
		    if($val_type){
			$where .= $where_type;
			$param = array_merge($param ,$param_type);
		    }
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
		if (!empty($filter['category_id']))
		{
			$where .= " AND (p.category_id = ? OR p.category_id IN (select category_id FROM ".$this->db->dbprefix('product_category')." WHERE parent_id = ?))";
			$param[] = $filter['category_id'];
			$param[] = $filter['category_id'];
		}
		if (!empty($filter['brand_id']))
		{
			$where .= " AND p.brand_id = ? ";
			$param[] = $filter['brand_id'];
		}
		if (!empty($filter['product_sex']))
		{
			$where .= " AND p.product_sex = ? ";
			$param[] = $filter['product_sex'];
		}
		$filter['sort_by'] = empty($filter['sort_by']) ? 'p.product_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);
		
		$sql = "SELECT COUNT(*) AS ct " . $from . $where ;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = $select . $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		foreach ($list as $value) {
		    $other_filter = array('product_id'=>$value -> product_id);
		    $gallery = $this->product_model->filter_gallery($other_filter);
		    if(!empty($gallery)){
			$value -> gallery = $gallery -> img_40_40;
		    }
		    $type =  $this->product_model->filter_product_type_link($other_filter);
		     if(!empty($type)){
			 $tmp_type = "";
			 foreach ($type as $k=>$v){
			      $tmp_type .= $v -> type_name.= ",";;
			 }
			 $tmp_type = substr($tmp_type,0, -1);
			 $value -> product_type = $tmp_type;
		     }
		}
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function filter_product_type_link($filter){
		$sql="SELECT l.product_id,t.type_id,t.type_name FROM "
		    .$this->db->dbprefix('product_type')." AS t
		    LEFT JOIN ".$this->db->dbprefix('product_type_link')." AS l ON t.type_id = l.type_id";
		$where = " WHERE 1 ";
		if (!empty($filter['product_id']))
		{
			$where .= " AND l.product_id = ? ";
			$param[] = $filter['product_id'];
		}
		$query = $this->db->query($sql . $where, $param);
		$list = $query->result();
		$query->free_result();
		return $list;
	}
        
    /**
     * 插入商品成本价并判断记录record
     * @param type $upd_arr 
     */
    public function insert_pro_cost ($upd_arr ,$admin_id ){
        $upd_record = $upd_arr;
        unset($upd_record['consign_type']);
        unset($upd_record['provider_id']);
        unset($upd_record['product_cess']);
        $sql = "SELECT * FROM ty_product_cost WHERE product_id = ? AND batch_id = ? AND provider_id = ? ";
        $row = $this->db->query($sql , array($upd_arr['product_id'], $upd_arr['batch_id'], $upd_arr['provider_id'] ))->row();
        if(empty($row) ){//新增
            $upd_arr['create_admin'] = $admin_id;
            $upd_arr['create_date'] = date('Y-m-d H:i:s');
            $this->db->insert('product_cost', $upd_arr);
        }else {//更新
            $upd_arr['update_admin'] = $admin_id;
            $upd_arr['update_time'] = date('Y-m-d H:i:s');
            $this->db->update('product_cost', $upd_arr, array('product_id' =>$row->product_id,'batch_id' => $row->batch_id,'provider_id' => $row->provider_id ));
        }
        $upd_record['create_admin'] = $admin_id;
        $upd_record['create_date'] = date('Y-m-d H:i:s');
        $this->db->insert('product_cost_record', $upd_record);
    }

	public function insert_product_cost( $data ){
		$this->db->insert('product_cost', $data);
	}
	
    /**
     * 获取product_cost
     * @param string $batch_id    批次id
     * @param string $provider_id 供应商id
     * @param array  $pro_ids     product_id集合
     * @return array    $res    
     */
    public function get_pro_cost( $batch_id, $provider_id, $pro_ids ){
        $sql = "SELECT * FROM ty_product_cost WHERE batch_id = ? AND provider_id = ? AND product_id ".  db_create_in($pro_ids );
        $res = $this->db_r->query($sql ,array($batch_id, $provider_id) )->result_array();
        return $res;
    }

	public function set_product_type($update){
	    $product_id = $update["product_id"];
	    $type_ids = $update["type_ids"];
	    $this->db->trans_start();
	    $this->db->delete('product_type_link', array('product_id'=>$product_id));
	    if(!empty($type_ids)){
		foreach ($type_ids as $type_id){
		    $data = array('product_id'=>$product_id,'type_id'=>$type_id);
		    $this->db->insert('product_type_link', $data);
		}
	    }
	    $this->db->trans_commit();
	    return $update;
	}

	public function filter_barcode_product($provider_barcode,$full_query = FALSE)
	{
		if (empty($provider_barcode))
		{
			return array();
		}
		$select =" SELECT a.product_id,a.product_sn,a.product_name,a.brand_id,a.provider_id,a.category_id,a.provider_productcode,b.provider_barcode,b.color_id,b.size_id ";
		$from = " FROM " .$this->db->dbprefix('product_info')." AS a " ;
		$join =	" LEFT JOIN ".$this->db->dbprefix('product_sub')." AS b ON a.product_id = b.product_id " ;
		$where = " WHERE ";
		if(is_array($provider_barcode)){
		    $where= $where . " b.provider_barcode " .db_create_in($provider_barcode);
		}else{
		    $where = $where . " b.provider_barcode = ?";
		}
		if($full_query){
		    $select .= ",p.provider_code,p.provider_name,c.color_name,s.size_name,pb.batch_code,pc.consign_price,pc.cost_price,pc.consign_rate,pc.consign_type ";
		    $join .= " LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON c.color_id = b.color_id ";
		    $join .= " LEFT JOIN ".$this->db->dbprefix('product_size')." AS s ON s.size_id = b.size_id ";
		    $join .= " LEFT JOIN ".$this->db->dbprefix('product_provider')." AS p ON p.provider_id = a.provider_id ";
		    $join .= " LEFT JOIN ".$this->db->dbprefix('product_cost')." AS pc ON pc.product_id = a.product_id ";
		    $join .= " LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS pb ON pb.batch_id = pc.batch_id ";
		}
		$sql = $select . $from . $join . $where;
		$param[] = $provider_barcode;
		$query = $this->db->query($sql, $param);
		return $query->result();
	}

	public function get_consign_single($product_id,$color_id,$size_id)
	{
		$sql =  "SELECT a.*,b.product_sn,e.provider_code,b.brand_id,b.provider_productcode,c.color_name,c.color_sn,d.size_name,d.size_sn 
				FROM " .$this->db->dbprefix('product_sub')." AS a 
				LEFT JOIN ".$this->db->dbprefix('product_info')." AS b ON a.product_id = b.product_id 
				LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON a.color_id = c.color_id 
				LEFT JOIN ".$this->db->dbprefix('product_size')." AS d ON a.size_id = d.size_id 
				LEFT JOIN ".$this->db->dbprefix('product_provider')." AS e ON b.provider_id = e.provider_id 
				WHERE a.product_id = ? AND a.color_id = ? AND a.size_id = ? ";
		$param[] = $product_id;
		$param[] = $color_id;
		$param[] = $size_id;
		$query = $this->db->query($sql, $param);
		return $query->row();
	}
	
    public function batch_query_goods_by_ids($product_id_ary) {
        $sql = "SELECT * FROM ty_product_info WHERE product_id ".db_create_in($product_id_ary);
        $res = $this->db_r->query($sql )->result();
        return $res;
    }
    
    public function batch_query_goods_color_by_ids($product_id_ary) 
    {
        $sql = "SELECT sub.product_id,sub.color_id,
                       p.is_best,p.is_new,p.is_hot,p.is_promote,p.is_offcode,
                       IF(is_promote,p.promote_price,p.shop_price) AS product_price,
                       p.product_name,p.product_sn,p.shop_price,p.market_price,
                       p.is_promote,p.promote_price,p.promote_start_date,p.promote_end_date,
                       p.is_new,p.is_hot,p.is_offcode,p.is_best,p.product_desc_additional,
                       b.brand_name,c.color_name,g.img_318_318 
                 FROM " . $this->db->dbprefix('product_sub') ." AS sub 
                 LEFT JOIN " . $this->db->dbprefix('product_info') . " AS p ON sub.product_id = p.product_id 
                 LEFT JOIN " . $this->db->dbprefix('product_brand') . " AS b ON p.brand_id = b.brand_id 
                 LEFT JOIN " . $this->db->dbprefix('product_color') . " AS c ON c.color_id = sub.color_id 
                 LEFT JOIN " . $this->db->dbprefix('product_gallery') . " AS g ON g.product_id=sub.product_id AND g.color_id=sub.color_id AND g.image_type='default' 
                 WHERE sub.product_id ".db_create_in($product_id_ary). " 
                 GROUP BY sub.product_id,sub.color_id";
        
        $res = $this->db_r->query($sql )->result();
        return $res;
    }
    
    public function has_product($related_id, $provider_id) {
        $sql = "SELECT pi.product_id, pi.product_sn FROM ty_product_info pi WHERE pi.related_id = ? AND pi.provider_id = ?;";
        return $this->db->query($sql, array($related_id, $provider_id))->row_array();
    }
    
    public function get_product_info($product_id) {
        $sql = "SELECT * FROM ty_product_info pi WHERE pi.product_id = ?;";
        return $this->db->query($sql, array($product_id))->row_array();
    }
    
    public function get_product_subs($related_id, $product_id = NULL) {
        if (empty($product_id)) {
            $sql = "SELECT ps.*, pc.color_sn, psz.size_sn FROM ty_product_sub ps 
                    INNER JOIN ty_product_color pc USING (color_id)
                    INNER JOIN ty_product_size psz USING (size_id)
                    WHERE ps.product_id = ? ;";
            $param = array($related_id);
        } else {
            $sql = "SELECT ps.*, pc.color_sn, psz.size_sn FROM ty_product_sub ps
                    INNER JOIN ty_product_color pc USING (color_id)
                    INNER JOIN ty_product_size psz USING (size_id)
                    WHERE ps.product_id = ?
                    AND NOT EXISTS (
                    SELECT 1 FROM ty_product_sub _ps
                    WHERE _ps.product_id = ? AND ps.color_id = _ps.color_id AND _ps.size_id = _ps.size_id
                    )";
            $param = array($related_id, $product_id);
        }
        return $this->db->query($sql, $param)->result_array();
    }
    
    public function get_product_galleries($related_id, $product_id = NULL) {
        if (empty($product_id)) {
            $sql = "SELECT * FROM ty_product_gallery pg WHERE pg.product_id = ?;";
            $param = array($related_id);
        } else {
            $sql = "SELECT * FROM ty_product_gallery pg
                    WHERE pg.product_id = ?
                    AND NOT EXISTS (
                    SELECT 1 FROM ty_product_gallery _pg
                    WHERE _pg.product_id = ? AND pg.image_type = _pg.image_type AND pg.color_id = _pg.color_id AND pg.sort_order = _pg.sort_order
                    );";
            $param = array($related_id, $product_id);
        }
        return $this->db->query($sql, $param)->result_array();
    }
    
    public function add_product_info($product_info) {
        $this->db->insert("ty_product_info", $product_info);
        return $this->db->insert_id();
    }
    
    public function add_product_subs($product_subs) {
        if (empty($product_subs) || count($product_subs) < 0x0001) return 0x0000;
        $this->db->insert_batch("ty_product_sub", $product_subs);
        return $this->db->affected_rows();
    }
    
    public function add_product_galleries($product_galleries) {
        if (empty($product_galleries) || count($product_galleries) < 0x0001) return 0x0000;
        $this->db->insert_batch("ty_product_gallery", $product_galleries);
        return $this->db->affected_rows();
    }
	
	/**
	 * 根据rush上下架商品 
	 */
	public function is_onsale_pro($param){
	    if (empty($param) || count($param) < 0x0001) return 0x0000;
	    $sql = "UPDATE ty_product_info SET promote_start_date = ? ,promote_end_date= ? ,is_onsale = ?  WHERE product_id IN (SELECT product_id FROM ty_rush_product WHERE rush_id = ?)";
	    $this->db->query($sql ,array($param['promote_start_date'], $param['promote_end_date'], $param['is_onsale'], $param['rush_id'], ));
	    $sql = "UPDATE ty_product_sub SET is_on_sale = ? WHERE product_id IN (SELECT product_id FROM ty_rush_product WHERE rush_id = ?)";
	    $this->db->query($sql ,array($param['is_on_sale'], $param['rush_id'], ));
	    return;
	}
	
	public function gen_product_sn(){
	    $pattern_before = "A";
	    $key ="";
	    $pattern='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
	    for($i=0;$i<9;$i++)
	    {
	      $key .= $pattern{mt_rand(0,35)};    //生成php随机数
	    }
	    $product_sn = $pattern_before . $key;
	    $old_product = $this->filter(array('product_sn'=>$product_sn));
	    if ($old_product) {
		return gen_product_sn();
	    }
	    return $product_sn;
	}
	
	public function query_all_sub($filter){
	    $this->db_r
		    ->select('ps.*,i.product_name,i.product_sn,pc.color_sn,psize.size_sn,color_name,size_name,i.provider_productcode,i.product_name,i.product_sn')
		    ->from('product_sub AS ps')
		    ->join('product_info AS i','i.product_id = ps.product_id','left')
		    ->join('product_color AS pc','ps.color_id = pc.color_id','left')
		    ->join('product_size AS psize', 'psize.size_id = ps.size_id', 'left')
		    ->where($filter);
	   $query = $this->db_r->get();
	    return $query->result_array();
	}
	
    public function filter_cost($filter) 
    {
		$query = $this->db->get_where('product_cost', $filter);
		return $query->row();
	}
	
	public function query_product_cost($filter) 
    {
		$query = $this->db->get_where('product_cost', $filter);
		return $query->result();
	}
        
    public function select_sub_by_SKU($filter)
	{
        $this->db
                ->select('ps.sub_id, ps.provider_barcode, pbs.box_sub_id')
                ->from('product_sub AS ps')
                ->join('product_color AS pc','ps.color_id = pc.color_id','left')
                ->join('product_size AS psize', 'psize.size_id = ps.size_id', 'left')
                ->join('purchase_box_sub AS pbs', 'ps.product_id = pbs.product_id and ps.color_id = pbs.color_id and ps.size_id = pbs.size_id', 'left')
                ->where($filter);
        $query = $this->db->get();
        return $query->result();
	}
	
	public function get_nav($parent_id=0){
        $list = array();
        $sql = "select type_id AS category_id, type_name AS category_name from ty_product_type where parent_id=".$parent_id." order by sort_order";
        $query = $this->db->query($sql);
        $list = $query->result_array();
        return $list;	
	}
        
    public function get_random(){
        $sql = "SELECT rand_id, rand_sn FROM ty_product_sn_rand WHERE status = 0 ORDER BY rand_id ASC LIMIT 1";
        $result = $this->db->query($sql)->row_array();
        if (empty($result))
            return false;
        $sql = "UPDATE ty_product_sn_rand SET status = 1 WHERE `rand_id` = ".$result['rand_id'];
        $this->db->query($sql);
        return $result['rand_sn'];
    }

    // 商品款号生成规则
    public function gen_p_sn($brand_code, $cate_code){
        $sn = $this->get_random();
        if (empty($sn)) return;
        $a = "gen_p_sn_".PRODUCT_SN_RULE;
        return $this->$a($sn,$brand_code, $cate_code);
    }

    // 商品款号按品牌缩写+随机数
    private function gen_p_sn_cat_rand($sn,$brand_code, $cate_code){
        return $cate_code.$sn;
    }

    // 商品款号按类别编码+随机数
    private function gen_p_sn_brandyear($sn,$brand_code, $cate_code){
        return $brand_code.date("y").$sn;
    }

	//更新产品访问量
	public function product_num_update($id,$pv)
	{
        $sql = "UPDATE ty_product_info SET pv_num = pv_num+".$pv." WHERE product_id = ".$id;
        return $this->db->query($sql);
        return true;
	}

	//查询产品信息
	public function product_sn_name($id)
	{
        $sql = "SELECT product_sn,product_name FROM ty_product_info WHERE product_id=".$id;
        $sn_name = $this->db->query($sql)->row();
        return $sn_name;
	}

	//批量更新产品销量信息
    public function ps_num_update ($update){
        $this->db->update_batch('product_info', $update, 'product_id');
        return true;
    }

	public function strip_product()
	{
		$sql = "SELECT product_id,product_desc FROM ty_product_info WHERE genre_id=1 AND product_desc IS NOT NULL AND LENGTH(product_desc) > 20";
		    $query = $this->db->query($sql);
		$list = $query->result_array();
		foreach( $list AS $row ){
			$product_id = $row['product_id'];
			$desc = preg_replace('/style=".*"/','',strip_tags( $row['product_desc'], '<p>' ));
			$data['product_desc'] = $desc;
			$this->update( $data, $product_id );
		}
		echo 'done';
	}

	public function get_check_gallery_rows($num=50){
        $sql = "select product_id, img_url from ty_product_gallery where `img_exist` = 0 order by product_id desc limit ".$num;
        $result = $this->db->query($sql)->result();
        return $result;
	}

	public function update_check_gallery_rows($data){
        $sql= 'update ty_product_gallery set img_exist = ? where product_id = ?';
        return $this->db->query($sql,$data);
	}

    //更新产品评价量
    public function product_pjnum_update($id)
    {
        $sql = "UPDATE ty_product_info SET pj_real_num = pj_real_num+1 WHERE product_id = ".$id;
        return $this->db->query($sql);
    }
		
}
###
