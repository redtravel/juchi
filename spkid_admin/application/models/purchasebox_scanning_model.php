<?php
#doc
#	classname:	Purchasebox_scanning_model
#	scope:		PUBLIC
#
#/doc

class Purchasebox_scanning_model extends CI_Model
{

	public function filter_purchase_box ($filter)
	{
		$query = $this->db->get_where('purchase_box_main', $filter, 1);
		return $query->row();
	}
	
	public function filter_purchasebox_sub($filter) {
		$query = $this->db->get_where('purchase_box_sub', $filter, 1);
		return $query->row();
	}
	
	public function get_location_depot($filter) {
		$query = $this->db->get_where('location_info', $filter, 1);
		return $query->row();
	}
	
	public function get_depot_info($filter) {
		$query = $this->db->get_where('depot_info', $filter, 1);
		return $query->row();
	}
	
	public function add_depot_in_product($depot_in_id,$depot_id,$product_number,$location_id,$admin_id,$box_id,$product_id,$color_id,$size_id) {
		$filter = array('depot_in_id'=>$depot_in_id,'depot_id'=>$depot_id,'location_id'=>$location_id,'product_id'=>$product_id,'color_id'=>$color_id,'size_id'=>$size_id);
		$query = $this->db->get_where('depot_in_sub', $filter, 1);
		$product_info = $query->row();
		//检测对应入库单中是否已添加此商品
		if (empty($product_info)) {
			//添加入库单子表商品信息
			$sql = "INSERT INTO ".$this->db->dbprefix('depot_in_sub')." (depot_in_id,product_id,product_name,color_id,size_id,depot_id,location_id,shop_price," .
					"product_number,product_amount,create_admin,create_date,batch_id, product_finished_number, expire_date, production_batch) " .
					"SELECT '".$depot_in_id."',pbs.product_id,ps.product_name,pbs.color_id,pbs.size_id,'".$depot_id."','".$location_id."',ps.shop_price,".
					"'".$product_number."',ps.shop_price*".$product_number.",'".$admin_id."','".date('Y-m-d H:i:s')."',pm.batch_id, '".$product_number."', pbs.expire_date , pbs.production_batch " .
					"FROM ".$this->db->dbprefix('purchase_box_sub')." pbs ".
                                        "LEFT JOIN ".$this->db->dbprefix('purchase_box_main')." pbm ON pbs.box_id = pbm.box_id ".
                                        "LEFT JOIN ".$this->db->dbprefix('purchase_main')." pm ON pbm.`purchase_code` = pm.`purchase_code` ".
                                        "LEFT JOIN ".$this->db->dbprefix('purchase_sub')." ps ON pbs.product_id = ps.product_id AND pbs.color_id = ps.color_id AND pbs.size_id = ps.size_id AND pm.`purchase_id` = ps.`purchase_id` " .
					"WHERE pbs.box_id = '".$box_id."' AND pbs.product_id = '".$product_id."' AND pbs.color_id = '".$color_id."' AND pbs.size_id = '".$size_id."' ";
			$this->db->query($sql);
			$sub_id = $this->db->insert_id();
			$sub_id = $sub_id > 0?$sub_id:0;
			if ($sub_id == 0)
			{
				return false;
			}
		} else {
			//更新入库单子表商品信息
			$sub_id = $product_info->depot_in_sub_id;
			$sub_id = $sub_id > 0?$sub_id:0;
			if ($sub_id == 0)
			{
				return false;
			}
			$total_num = intval($product_info->product_number) + intval($product_number);
                        $total_finish_num = intval($product_info->product_finished_number) + intval($product_number);
			$sql = "UPDATE ".$this->db->dbprefix('depot_in_sub')." " .
					"SET product_number = '".$total_num."',product_amount = shop_price * ".$total_num.",location_id = '".$location_id."', product_finished_number = '".$total_finish_num."' " .
					"WHERE depot_in_id = '".$depot_in_id."' AND depot_in_sub_id = '".$sub_id."'";
			$query = $this->db->query($sql);
		}
		return $sub_id;
	}
	
	public function insert_transaction_info($sub_id,$product_num,$admin_id) {
		//添加入库单明细记录
		if (empty($sub_id)) {
			return false;
		}
		$sql = "INSERT INTO ".$this->db->dbprefix('transaction_info')."(trans_type,trans_status,trans_sn,product_id,color_id,size_id,product_number," .
				"depot_id,location_id,create_admin,create_date,update_admin,update_date,cancel_admin,cancel_date,trans_direction,sub_id,batch_id,shop_price,consign_price,cost_price,consign_rate,product_cess,expire_date,production_batch) ".
				" SELECT ".TRANS_TYPE_DIRECT_IN.",".TRANS_STAT_AWAIT_IN.",b.depot_in_code,a.product_id,a.color_id,size_id,'".$product_num."',".
				"a.depot_id,a.location_id,'".$admin_id."','".date('Y-m-d H:i:s')."',0,'0000-00-00',0,'0000-00-00',1,a.depot_in_sub_id,a.batch_id,a.shop_price,pc.consign_price,pc.cost_price,pc.consign_rate,pc.product_cess,a.expire_date,a.production_batch" .
				" FROM ".$this->db->dbprefix('depot_in_sub')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_cost')." pc ON a.product_id = pc.product_id AND a.batch_id = pc.batch_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_in_main')." b ON b.depot_in_id = a.depot_in_id WHERE a.depot_in_sub_id = '".$sub_id."' ";
		$this->db->query($sql);
		return $this->db->insert_id();
	}
	
	public function update_purchasebox_sub($box_sub_id,$over_num,$admin_id) {
		$param = array();
		$sql = "UPDATE ".$this->db->dbprefix('purchase_box_sub')." SET over_num = ? ,shelve_id = ? ,shelve_starttime = ? ,shelve_endtime = ? WHERE box_sub_id = ? ";
		$param[] = $over_num;
		$param[] = $admin_id;
		$param[] = date('Y-m-d H:i:s');
		$param[] = date('Y-m-d H:i:s');
		$param[] = $box_sub_id;
		$query = $this->db->query($sql, $param);
	}
	
	public function update_purchasebox($box_id,$admin_id) {
		$sql = "SELECT SUM(over_num) AS product_shelve_num " .
				"FROM ".$this->db->dbprefix('purchase_box_sub')." WHERE box_id = ? ";
		$param = array();
		$param[] = $box_id;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		if (empty($row))
		{
			$product_shelve_num = 0;
		} else
		{
			$product_shelve_num = $row->product_shelve_num;
		}
		$param2 = array();
		$sql = "UPDATE ".$this->db->dbprefix('purchase_box_main')." SET product_shelve_num = ? ,shelve_id = ? ,shelve_starttime = ? ,shelve_endtime = ? WHERE box_id = ? ";
		$param2[] = $product_shelve_num;
		$param2[] = $admin_id;
		$param2[] = date('Y-m-d H:i:s');
		$param2[] = date('Y-m-d H:i:s');
		$param2[] = $box_id;
		$query = $this->db->query($sql, $param2);
	}
	
	public function update_purchase($purchase_code){
	    $sql = "SELECT SUM(product_shelve_num) AS product_shelve_num " .
				"FROM ".$this->db->dbprefix('purchase_box_main')." WHERE purchase_code = ? ";
		$param = array();
		$param[] = $purchase_code;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		if (empty($row))
		{
			$product_shelve_num = 0;
		} else
		{
			$product_shelve_num = $row->product_shelve_num;
		}
		$param2 = array();
		$sql = "UPDATE ".$this->db->dbprefix('purchase_main')." SET purchase_shelved_number = ? WHERE purchase_code = ? ";
		$param2[] = $product_shelve_num;
		$param2[] = $purchase_code;
		$query = $this->db->query($sql, $param2);
	}
        
         public function get_cooperation_by_product_id($product_id) {
		$sql = "SELECT b.provider_cooperation
		FROM ".$this->db->dbprefix('product_info')." AS a
		LEFT JOIN ".$this->db->dbprefix('product_provider')." AS b ON a.provider_id=b.provider_id
		WHERE a.product_id=?";
		$query = $this->db_r->query($sql,array($product_id));
		return $query->row();
	}

}
###