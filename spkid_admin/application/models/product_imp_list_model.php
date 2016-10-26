<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of product_imp_list_model
 *
 * @author mickey
 */
class product_imp_list_model extends CI_Model{
    //put your code here
    
    public function filter($filter){
	$query = $this->db->get_where('product_imp_list', $filter, 1);
	return $query->row();
    }
    
     public function all_filter($filter){
	 $from = " FROM ty_product_imp_list ";
	 $where = " WHERE 1 ";
	 $order_by =" ORDER BY id desc ";
	 $param = array();
	 if($filter['create_admin']){
            $where .= " AND create_admin = ? ";
            $param[] = $filter['create_admin'];                 
        }
	if($filter['start_date']){
            $where .= " AND create_date >= ? ";
            $param[] = $filter['start_date'];                 
        }
	if($filter['end_date']){
            $where .= " AND create_date <= ? ";
            $param[] = $filter['end_date'];                 
        }
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
	$sql = "SELECT id,product_id_list,status,create_admin,create_date,confirm_admin,confirm_date " . $from . $where  .$order_by 
		. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
	$query = $this->db->query($sql, $param);
	$list = $query->result();
	$query->free_result();
	return array('list' => $list, 'filter' => $filter);
    }
    
    public function all_import_admin(){
	$sql =" SELECT l.create_admin,admin_name  FROM ty_product_imp_list l LEFT JOIN ty_admin_info a ON a.admin_id = l.create_admin";
	$query = $this->db->query($sql);
	$list = $query->result();
	$query->free_result();
	return $list;
    }
    
    public function insert ($data)
    {
	    $this->db->insert('product_imp_list', $data);
	    return $this->db->insert_id();
    }
    
    public function update($update,$imp_list_id){
	$this->db->update('product_imp_list',$update,array('id'=>$imp_list_id));
    }
    
    public function query_product_color_size($product_id_array){
	$this->db_r
		->select('pi.product_sn,pi.provider_productcode, color_name, size_name,ps.provider_barcode')
		->from('product_info AS pi')
		->join('product_sub AS ps','ps.product_id=pi.product_id')
		->join('product_color AS pc','ps.color_id = pc.color_id','left')
		->join('product_size AS psize', 'psize.size_id = ps.size_id', 'left')
		->where_in('pi.product_id',$product_id_array);
	$query = $this->db_r->get();
	return $query->result_array();
    }
    
     public function query_product_sub($product_id_array){
	$this->db_r
		->select('pi.product_id,pi.product_sn,pi.provider_productcode,pi.product_name,style_id,pi.product_sex,
		    unit_name,goods_carelabel,model_id,product_desc_additional')
		->from('product_info AS pi')
		->join('product_sub AS ps','ps.product_id=pi.product_id')
		->join('product_color AS pc','ps.color_id = pc.color_id','left')
		->join('product_size AS psize', 'psize.size_id = ps.size_id', 'left')
		->join('purchase_sub AS pps','ps.product_id=pps.product_id and ps.color_id=pps.color_id and ps.size_id=pps.size_id','left')
		->join('product_provider AS pr','pr.provider_id=pi.provider_id','left')
		->where_in('pi.product_id',$product_id_array);
	$query = $this->db_r->get();
	$list = $query->result_array();
	foreach ($list as $item){
	    if(empty($item["product_sex"])){
		continue;
	    }
	    if($item["product_sex"] == 1){
		$item["product_sex"] = 'm';
	    }else if($item["product_sex"] == 2){
		$item["product_sex"] = 'w';
	    }else if($item["product_sex"] == 3){
		$item["product_sex"] = 'a';
	    }
	}
	return $list;
    }
}

?>
