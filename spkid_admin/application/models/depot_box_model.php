<?php

/**
 * 收货箱
 * @author:sean
 * @date:2013-02-18
 */
class Depot_box_model extends CI_Model {

    public function filter_depot_box_main($filter) {
	$query = $this->db_r->get_where('depot_out_box', $filter);
	return $query->result();
    }
    
    public function query_depot_box_main($filter){
	$this->db_r
		 ->select("main.*,info.admin_name as qc_name")
		 -> from("depot_out_box AS main")
		 ->join('ty_admin_info AS info', 'main.qc_id=info.admin_id','left')
		 ->where($filter);
	 $query = $this->db_r->get();
	 return $query->result();
    }
	    
    
    public function filter_depot_box_sub($filter){
	$query = $this->db_r->get_where('depot_out_box_sub', $filter);
	return $query->result();
    }
    
    
    public function filter_depot_out_main($filter){
	$query = $this->db_r->get_where('depot_out_main', $filter,1);
	return $query->row();
    }
    
    public function query_depot_box_sub_group($filter){
	 $this->db_r
		 ->select("sub.product_id,sub.color_id,sub.size_id,SUM(sub.product_number) AS finished_scan_number,color_name, size_name,pi.product_sn as product_sn")
		 -> from("depot_out_box_sub AS sub")
		 ->join('product_info AS pi', 'sub.product_id=pi.product_id','left')
		 ->join("depot_out_box AS main","sub.box_id = main.box_id",'left')
		 ->join('product_color AS pc', 'pc.color_id = sub.color_id', 'left')
		 ->join('product_size AS psize', 'psize.size_id = sub.size_id', 'left')
//		->join('depot_out_sub AS dos', 'sub.product_id=dos.product_id and sub.color_id=dos.color_id and sub.size_id=dos.size_id', 'left')
		 ->where($filter)
		->group_by('sub.product_id,sub.color_id,sub.size_id');
	 $query = $this->db_r->get();
	 return $query->result();
    }
    
    public function query_depot_box_sub($filter){
	 $this->db_r
		 ->select("sub.product_id,sub.color_id,sub.size_id,SUM(sub.product_number) AS finished_scan_number,br.brand_name,
		     color_name, size_name,pi.product_sn,pi.product_name,pi.provider_productcode,ps.provider_barcode")
		 -> from("depot_out_box_sub AS sub")
		 ->join('product_info AS pi', 'sub.product_id=pi.product_id','left')
		 ->join('product_color AS pc', 'pc.color_id = sub.color_id', 'left')
		 ->join('product_size AS psize', 'psize.size_id = sub.size_id', 'left')
		 ->join('product_sub AS ps', 'sub.product_id=ps.product_id and sub.color_id=ps.color_id and sub.size_id=ps.size_id', 'left')
		 ->join('product_brand AS br', 'br.brand_id=pi.brand_id', 'left')
		 ->where($filter)
		->group_by('sub.product_id,sub.color_id,sub.size_id');
	 $query = $this->db_r->get();
	 return $query->result();
    }

    public function product_sub_for_scan($filter) {
	$this->db_r
		->select('ps.product_id,pi.product_name,pr.provider_name,pr.provider_code,ps.color_id,ps.size_id, color_name, size_name,product_sn,br.brand_name,' . 
			'SUM(dos.product_number) as product_number' .
			',ps.provider_barcode,pi.provider_productcode,pi.create_admin,pi.create_date,pi.audit_admin,pi.audit_date')
		->from('product_sub AS ps')
		->join('product_info AS pi', 'ps.product_id=pi.product_id')
		->join('product_color AS pc', 'ps.color_id = pc.color_id', 'left')
		->join('product_size AS psize', 'psize.size_id = ps.size_id', 'left')
		->join('depot_out_sub AS dos', 'ps.product_id=dos.product_id and ps.color_id=dos.color_id and ps.size_id=dos.size_id', 'left')
		->join('product_provider AS pr', 'pr.provider_id=pi.provider_id', 'left')
		->join('product_brand AS br', 'br.brand_id=pi.brand_id', 'left')
		->where($filter)
		->group_by(" ps.product_id,ps.color_id,ps.size_id");
	$query = $this->db_r->get();
	return $query->result();
    }
    
    public function do_scan($depot_out_code,$box_code,$num,$qc_id,$product_array){
	$filter = array();
	$box = array();
	$time = date('Y-m-d H:i:s');
	$filter["box_code"] = $box_code;
	$this->db->trans_begin();
	$query = $this->db->get_where('depot_out_box', $filter);
	$db_box = $query->row_array();
	if(empty($db_box)){
	    $box["box_code"] = $box_code;
	    $box["depot_out_code"] = $depot_out_code;
	    $box["product_number"] = $num;
	    $box["qc_id"] = $qc_id;
	    $box["qc_starttime"] = $time;
	    $box["qc_endtime"] = $time;
	    $this->db->insert('depot_out_box', $box);
	    $box["box_id"] = $this->db->insert_id();
	}elseif($db_box["depot_out_code"] != $depot_out_code){
	    sys_msg("不是同一采购单，不允许重新打开",1);
	    return;
	}else{
	    $box["qc_endtime"] = $time;
	    $box["product_number"] = $db_box["product_number"] + $num;
	    $this-> db ->update('depot_out_box', $box, array('box_id' => $db_box["box_id"]));
	    $box["box_id"] = $db_box["box_id"];
	}
	$sub_scan_array = array();
	foreach ($product_array as $product){
	    $product_filter = array();
	    $product_filter["box_id"] = $box["box_id"];
	    $product_filter["product_id"] = $product["product_id"];
	    $product_filter["color_id"] = $product["color_id"];
	    $product_filter["size_id"] = $product["size_id"];
	    $query =  $this->db->get_where('depot_out_box_sub', $product_filter,1);
	    $box_sub = $query->row_array();
	    if(empty($box_sub)){
		$box_sub = array();
		$box_sub["box_id"] = $box["box_id"];
		$box_sub["product_id"] = $product["product_id"];
		$box_sub["color_id"] = $product["color_id"];
		$box_sub["size_id"] = $product["size_id"];
		$box_sub["product_number"] = $product["num"];
		$box_sub["qc_id"] = $qc_id;
		$box_sub["qc_starttime"] = $time;
		$box_sub["qc_endtime"] = $time;
		$this->db->insert('depot_out_box_sub', $box_sub);
	    }else{
		$box_sub_update = array();
		$box_sub_update["product_number"] = $product["num"]+$box_sub["product_number"];
		$box_sub_update["qc_endtime"] = $time;
		$this-> db ->update('depot_out_box_sub', $box_sub_update, array('box_sub_id' => $box_sub["box_sub_id"]));
	    }
	}
	$this->update_depot_out_finished_num($depot_out_code,$num);
	$this->db->trans_commit();
    }
    
    public function update_depot_out_finished_num($depot_out_code,$all_number,$type ='+'){
	$sql="UPDATE ty_depot_out_main SET depot_out_finished_number = depot_out_finished_number $type $all_number WHERE depot_out_code = '$depot_out_code'";
	$this->db->query($sql);
    }
    
    public function insert_depot_out_box ($update)
    {
	    $this->db->insert('depot_out_box', $update);
	    return $this->db->insert_id();
    }
    
     public function insert_depot_out_box_sub ($update)
    {
	    $this->db->insert('depot_out_box_sub', $update);
	    return $this->db->insert_id();
    }
    
    public function delete_depot_out_box($box_id)
    {
	    $this->db->delete('depot_out_box', array('box_id'=>$box_id));
    }
    
     public function delete_depot_out_box_sub($box_id)
    {
	    $this->db->delete('depot_out_box_sub', array('box_id'=>$box_id));
    }
    
     public function query_depot_box_sub_scan($filter){
	 $this->db_r
		 ->select("sub.product_id,sub.color_id,sub.size_id,sub.product_number as product_number,br.brand_name,SUM(s.product_number) as depot_num,
		     color_name, size_name,pi.product_sn,pi.product_name,pi.provider_productcode,ps.provider_barcode")
		 -> from("depot_out_box_sub AS sub")
		 ->join('product_info AS pi', 'sub.product_id=pi.product_id','left')
		 ->join('product_color AS pc', 'pc.color_id = sub.color_id', 'left')
		 ->join('product_size AS psize', 'psize.size_id = sub.size_id', 'left')
		 ->join('product_sub AS ps', 'sub.product_id=ps.product_id and sub.color_id=ps.color_id and sub.size_id=ps.size_id', 'left')
		 ->join('product_brand AS br', 'br.brand_id=pi.brand_id', 'left')
		 ->join('depot_out_box AS main', 'main.box_id=sub.box_id', 'left')
		 ->join('depot_out_main AS o', 'o.depot_out_code=main.depot_out_code', 'left')
		 ->join('depot_out_sub AS s', 'sub.product_id=s.product_id and sub.color_id=s.color_id and sub.size_id=s.size_id and o.depot_out_id=s.depot_out_id', 'left')
		 ->where($filter)
		->group_by("s.product_id,s.color_id,s.size_id");
	 $query = $this->db_r->get();
	 return $query->result();
    }
}

?>
