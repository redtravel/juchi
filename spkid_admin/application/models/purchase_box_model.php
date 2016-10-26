<?php
/**
 * 收货箱
 * @author:sean
 * @date:2013-02-18
 */
class Purchase_box_model extends CI_Model
{
    /**
     *收货箱列表
     */
    function purchase_box_list($filter)
    {
        $from = " FROM ".$this->db_r->dbprefix('purchase_box_main')." AS p 
                    left join ".$this->db_r->dbprefix('admin_info')." AS a 
                    on p.scan_id=a.admin_id";
		$where = " WHERE 1 ";
		$param = array();
		if (!empty($filter['purchase_code']))
		{
			$where .= " AND p.purchase_code =? ";
			$param[] =$filter['purchase_code']; 
		}
        if (!empty($filter['product_sn']))
		{
			$where .= " AND p.box_id in (select box_id from ty_purchase_box_sub where product_id=
                (select product_id from ty_product_info where product_sn=?)) ";
			$param[] =$filter['product_sn']; 
		}
        if (!empty($filter['start_time']))
		{
			$where .= " AND p.scan_start_time>=? ";
			$param[] =$filter['start_time']; 
		}
        if (!empty($filter['end_time']))
		{
			$where .= " AND p.scan_end_time<=? ";
			$param[] =$filter['end_time']; 
		}
		if (!empty($filter['user_name']))
		{
			$where .= " AND a.realname=? ";
			$param[] =$filter['user_name']; 
		}
		$filter['sort_by'] = empty($filter['sort_by']) ? 'p.box_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'ASC' : trim($filter['sort_order']);

		$sql = "SELECT COUNT(*) AS ct " . $from . $where;
		$query = $this->db_r->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "SELECT p.* ,a.realname as real_name"
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db_r->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
    }

    public function filter_purchase_box_main($filter)
    {
        $query = $this->db_r->get_where('purchase_box_main',$filter);
	return $query->result();
    }
    
    public function filter_purchase_box_sub($filter)
    {
        $query = $this->db_r->get_where('purchase_box_sub',$filter);
	return $query->result();
    }

    /*
     * 扫描收货
     */
    public function scan_in($args)
    {
        $this->db->query('begin');
        //箱子主表
        //先检查箱子是否存在
        $box_main=$args['box_main'];
	$query=$this->db->get_where('purchase_main',array('purchase_code'=>$box_main["purchase_code"]));
	$purchase_main=$query->row();
	if(empty($purchase_main)){
	    sys_msg("对应采购单不存在",1);
	}
        $query=$this->db->get_where('purchase_box_main',array('box_code'=>$box_main['box_code']));
        $box_result=$query->row();
        if(!empty($box_result))//更新
        {
            $this->db->update('purchase_box_main',
                    array('product_number'=>$box_main['product_number']+$box_result->product_number
                           ,'scan_id'=>$box_main['scan_id'], 'delivery_date' => $box_main['delivery_date']
                           ,'scan_end_time'=>$box_main['scan_end_time']),
                    array('box_id'=>$box_result->box_id));
            if(!$this->db->affected_rows())
            {
                $this->db->query('rollback');
                return false;
            }
            $box_id=$box_result->box_id;
        }
        else//添加
        {
            $this->db->insert('purchase_box_main',$box_main);
            if(!$this->db->affected_rows())
            {
                $this->db->query('rollback');
                return false;
            }
            $box_id=$this->db->insert_id();
        }
        //插入箱子sub
        $insert_arr=array();
        foreach($args['product_list'] as $product)
        {
            $product['box_id']=$box_id;
            $query=$this->db->get_where('purchase_box_sub',
                    array('product_id'=>$product['product_id'],'color_id'=>$product['color_id'],
                          'size_id'=>$product['size_id'],'box_id'=>$product['box_id']));
            $sub_result=$query->row();
            if(empty($sub_result))//添加
            {
                $insert_arr[]=$product;
            }
            else//更新
            {
                $this->db->update('purchase_box_sub',
                                   array('product_number'=>$product['product_number']+$sub_result->product_number,
                                   'production_batch'=>$product['production_batch'],'expire_date'=>$product['expire_date'],
                                   'scan_id'=>$product['scan_id'], 'check_num' => $product['check_num'],'oqc' => $product['oqc'],
                                   'scan_endtime'=>$product['scan_endtime']),
                                   array('box_sub_id'=>$sub_result->box_sub_id));
                if(!$this->db->affected_rows())
                {
                    $this->db->query('rollback');
                    return false;
                }
            }
	    //更新采购单子表实际收货数量
	    $sql="update ty_purchase_sub set product_finished_number=product_finished_number+ ".$product['product_number'].
		  " where purchase_id = ? and product_id= ? and color_id = ? and size_id = ?";
	    $this->db->query($sql,array($purchase_main->purchase_id,$product["product_id"],$product["color_id"],$product["size_id"]));
	    if(!$this->db->affected_rows())
	    {
		$this->db->query('rollback');
		return false;
	    }
        }
        //批量插入
        if(count($insert_arr)>0)
        {
            $this->db->insert_batch('purchase_box_sub',$insert_arr);
            if(!$this->db->affected_rows())
            {
                $this->db->query('rollback');
                return false;
            }
        }
        //更新采购单主表实际收货数量
        $sql="update ty_purchase_main set purchase_finished_number=purchase_finished_number+$box_main[product_number]
                where purchase_code='$box_main[purchase_code]'";
        $this->db->query($sql);
        if(!$this->db->affected_rows())
        {
            $this->db->query('rollback');
            return false;
        }
        $this->db->query('commit');
        return true;
    }

    /**
     * 查询采购单收货箱数量
     */
    function get_box_count($purchase_code)
    {
        $sql="select count(1) as ct from ty_purchase_box_main 
                where purchase_code='$purchase_code'";
        $query=$this->db_r->query($sql);
        return $query->row();
    }

    /**
     * 查询收货箱中商品信息
     */
    function get_box_product($box_id)
    {
        /*$sql="select ps.product_number as pnum,pbs.product_id,pbs.color_id,pbs.size_id,ps.product_finished_number,
                pbs.product_number,realname,pi.product_sn,color_name,size_name,pi.product_name,b.brand_name,
                pbs.provider_barcode,pi.provider_productcode,pbs.over_num,pbs.box_sub_id 
              from ty_purchase_box_sub as pbs
              left join ty_product_info as pi on pbs.product_id=pi.product_id
	      left join ty_product_brand as b on b.brand_id = pi.brand_id 
              left join ty_product_color c on pbs.color_id=c.color_id
              left join ty_product_size s on pbs.size_id=s.size_id
              left join ty_admin_info ai on pbs.scan_id=ai.admin_id
	      left join ty_purchase_sub as ps on ps.product_id =  pbs.product_id and ps.color_id = pbs.color_id and ps.size_id=pbs.size_id
              where box_id=$box_id group by pbs.product_id,pbs.color_id,pbs.size_id";*/
        $sql="select ps.product_number as pnum,pbs.product_id,pbs.color_id,pbs.size_id,ps.product_finished_number,ps.expire_date, 
                pbs.product_number,realname,pi.product_sn,color_name,size_name,pi.product_name,b.brand_name,pbs.check_num, pbs.oqc,
                pbs.provider_barcode,pi.provider_productcode,pbs.over_num,pbs.box_sub_id,pbs.production_batch,pbs.box_sub_id,pbs.expire_date v_expire_date,pbs.production_batch 
              from ty_purchase_box_sub as pbs
              left join ty_product_info as pi on pbs.product_id=pi.product_id
	      left join ty_product_brand as b on b.brand_id = pi.brand_id 
              left join ty_product_color c on pbs.color_id=c.color_id
              left join ty_product_size s on pbs.size_id=s.size_id
              left join ty_admin_info ai on pbs.scan_id=ai.admin_id
	      LEFT JOIN `ty_purchase_box_main` bm ON pbs.`box_id` = bm.`box_id` 
              LEFT JOIN `ty_purchase_main` pm ON bm.`purchase_code` = pm.`purchase_code` 
              LEFT JOIN `ty_purchase_sub` ps ON pm.purchase_id = ps.`purchase_id` AND pbs.product_id = ps.product_id AND pbs.color_id = ps.color_id AND pbs.size_id = ps.`size_id`
              where pbs.box_id=$box_id group by pbs.product_id,pbs.color_id,pbs.size_id";    
        $query=$this->db_r->query($sql);
        return $query->result();
    }
    
    public function delete_purchase_box_main ($filter)
    {
	    $query = $this->db->get_where('purchase_box_main', $filter);
	    $result = $query->result();
	    foreach ($result as $info){
		$this->delete_purchase_box_sub(array("box_id"=>$info->box_id));
	    }
	    $this->db->delete('purchase_box_main', $filter);
	    return $this->db->affected_rows();
    }
    
    public function delete_purchase_box_sub ($filter)
    {
	    $this->db->delete('purchase_box_sub', $filter);
	    return $this->db->affected_rows();
    }
    
    public function set_purchase_main($update,$purchse_code){
	$this->db->update('purchase_main', $update, array('purchase_code' => $purchse_code));
    }
    
    public function set_purchase_sub($update,$purchse_id){
	$this->db->update('purchase_sub', $update, array('purchase_id' => $purchse_id));
    }
     public function  update_purchase_main_finished_number($product_number,$purchase_code){
	 //更新采购单主表实际收货数量
        $sql="update ty_purchase_main set purchase_finished_number=purchase_finished_number-".$product_number.
              " where purchase_code='".$purchase_code."'";
        $this->db->query($sql);
    }
    public function  update_purchase_sub_finished_number($product_number,$product_id,$color_id,$size_id){
	 //更新采购单子表实际收货数量
        $sql="update ty_purchase_sub set product_finished_number=product_finished_number-".$product_number.
              " where product_id=$product_id and color_id=$color_id and size_id=$size_id";
        $this->db->query($sql);
    }
    
    public function get_purchase_product($filter){
	 $this->db_r
		 ->select("sub.product_id,sub.color_id,sub.size_id,SUM(ps.product_number) AS finished_scan_number,color_name, size_name,pi.product_sn as product_sn,
		     s.provider_barcode,s.sub_id,pi.provider_productcode,sub.product_number AS product_number,pi.product_name")
		 -> from("ty_purchase_sub AS sub")
		 ->join("ty_purchase_main AS pm","pm.purchase_id = sub.purchase_id",'left')
		 ->join('purchase_box_sub AS ps', 'sub.product_id=ps.product_id and sub.color_id=ps.color_id and sub.size_id=ps.size_id', 'left')
		 ->join('product_sub AS s', 's.product_id=sub.product_id and s.color_id=sub.color_id and s.size_id=sub.size_id', 'left')
		 ->join('product_info AS pi', 'sub.product_id=pi.product_id','left')
		 ->join("purchase_box_main AS main","ps.box_id = main.box_id",'left')
		 ->join('product_color AS pc', 'pc.color_id = sub.color_id', 'left')
		 ->join('product_size AS psize', 'psize.size_id = sub.size_id', 'left')
		 ->where($filter)
		->group_by('sub.product_id,sub.color_id,sub.size_id');
	 $query = $this->db_r->get();
	 return $query->result();
    }
    
    public function get_purchase_product_scaned($filter){
	 $this->db_r
		 ->select("distinct sub.product_id,sub.color_id,sub.size_id,color_name, size_name,pi.product_sn as product_sn,
		     s.provider_barcode,s.sub_id,pi.provider_productcode ")
		 -> from("ty_purchase_sub AS sub")
		 ->join("ty_purchase_main AS pm","pm.purchase_id = sub.purchase_id",'left')
		 ->join('purchase_box_sub AS ps', 'sub.product_id=ps.product_id and sub.color_id=ps.color_id and sub.size_id=ps.size_id', 'left')
		 ->join('product_sub AS s', 's.product_id=sub.product_id and s.color_id=sub.color_id and s.size_id=sub.size_id', 'left')
		 ->join('product_info AS pi', 'sub.product_id=pi.product_id','left')
		 ->join("purchase_box_main AS main","ps.box_id = main.box_id",'left')
		 ->join('product_color AS pc', 'pc.color_id = sub.color_id', 'left')
		 ->join('product_size AS psize', 'psize.size_id = sub.size_id', 'left')
		 ->where($filter);
	 $query = $this->db_r->get();
	 return $query->result();
    }    
    
    
    public function update_purchase_box_sub($data , $id){
	$this->db->update('purchase_box_sub', $data, array('box_sub_id' => $id));
    }
    
    public function update_purchase_box_main($data , $id){
	$this->db->update('purchase_box_main', $data, array('box_id' => $id));
    }
    
}
?>
