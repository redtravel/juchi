<?php
#doc
#	classname:	Purchase_batch_model
#	scope:		PUBLIC
#
#/doc

class Purchase_batch_model extends CI_Model
{
    public function filter($filter)
	{
		$query = $this->db_r->get_where('purchase_batch', $filter, 1);
		return $query->row();
	}
	public function filter_batch_code($filter)
	{
		$this->db->order_by('batch_code', 'desc');
		$query = $this->db->get_where('purchase_batch', $filter, 1);
		return $query->row();
	}
    public function query($filter=array()){
        $this->db_r->order_by('batch_id', 'asc');
        $query = $this->db_r->get_where('purchase_batch', $filter);
        return $query->result();
    }
    public function get_purchase_num($batch_id){
        $sql = "SELECT COUNT(*) AS ct from ".$this->db_r->dbprefix('purchase_main')." where batch_id = ?";
		$query = $this->db_r->query($sql,array($batch_id));
		$row = $query->row();
		$query->free_result();
		return (int) $row->ct;
    }
    public function purchase_batch_list ($filter)
	{
		$from = " FROM ".$this->db_r->dbprefix('purchase_batch')." AS b 
				  LEFT JOIN ".$this->db_r->dbprefix('admin_info')." AS a ON b.create_admin = a.admin_id";
		$where = " WHERE 1 ";
		$param = array();
        $i=0;
		if (!empty($filter['batch_name']))
		{
			$where .= " AND b.batch_name LIKE ? ";
			$param[$i++] = '%' . $filter['batch_name'] . '%';
		}
		if (!empty($filter['batch_code']))
		{
			$where .= " AND b.batch_code LIKE ? ";
			$param[$i++] = '%' . $filter['batch_code'] . '%';
		}
		if (!empty($filter['provider_id']))
		{
			$where .= " AND b.provider_id = ? ";
			$param[$i++] = $filter['provider_id'];
		}
		if (!empty($filter['brand_id']))
		{
			$where .= " AND b.brand_id = ? ";
			$param[$i++] = $filter['brand_id'];
		}
		if (isset($filter['batch_status']) )
		{
			$where .= " AND b.batch_status = ? ";
			$param[$i++] = $filter['batch_status'];
		}
		if (!empty($filter['plan_arrive_date']))
		{
			$where .= " AND b.plan_arrive_date >= ? ";
			$param[$i++] = $filter['plan_arrive_date'];
			$where .= " AND b.plan_arrive_date < date_add(?, interval 1 day) ";
			$param[$i++] = $filter['plan_arrive_date'];
		}
		if (!empty($filter['create_admin']))
		{
			$where .= " AND a.realname like ? ";
			$param[$i++] = '%' . $filter['create_admin'] . '%';
		}
		if (!empty($filter['create_date_start']))
		{
			$where .= " AND b.create_date >= ? ";
			$param[$i++] = $filter['create_date_start'];
			//$where .= " AND b.create_date < date_add(?, interval 1 day) ";
			//$param[$i++] = $filter['create_date'];
		}
        
		if (!empty($filter['create_date_end']))
		{
			$where .= " AND b.create_date < date_add(?, interval 1 day) ";
			$param[$i++] = $filter['create_date_end'];
		}
        //先查总数
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
        //查询详细内容
		$sql = "SELECT b.*, a.realname,c.brand_name "
				. $from 
				. " LEFT JOIN ".$this->db_r->dbprefix('product_brand')." AS c ON b.brand_id=c.brand_id "
				. $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
        //var_dump($filter);
		$query = $this->db_r->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function insert ($data)
	{
	    $this->db->insert('purchase_batch', $data);
	    return $this->db->insert_id();
	}
	
	public function update ($data, $batch_id)
	{
		$this->db->update('purchase_batch', $data, array('batch_id' => $batch_id));
	}
	
	public function delete ($batch_id)
	{
		$this->db->delete('purchase_batch', array('batch_id' => $batch_id));
    }
	
	public function get_out_num($batch_id) {
		$sql =  "SELECT SUM(t.product_number) AS pn FROM ".$this->db_r->dbprefix('transaction_info')." AS t " .
				"WHERE t.batch_id=? AND t.trans_status in (1,2,4)";
		$query = $this->db_r->query($sql,array($batch_id));
		$row = $query->row();
		$query->free_result();
		return (int) $row->pn;
	}
	
	public function get_waiting_outin($batch_id) {
		$sql =  "SELECT t.* FROM ".$this->db_r->dbprefix('transaction_info')." AS t " .
				"WHERE t.batch_id=? AND t.trans_status in (1,3) limit 1";
		$query = $this->db_r->query($sql,array($batch_id));
		$row = $query->row();
		$query->free_result();
		return $row;
	}
	
	public function get_order_notok($batch_id) {
		$sql =  "SELECT t.* FROM ".$this->db_r->dbprefix('transaction_info')." AS t " .
				" INNER JOIN ".$this->db_r->dbprefix('order_info')." AS o ON t.trans_sn=o.order_sn ".
				" WHERE t.batch_id=? AND o.is_ok=0 limit 1 ";
		$query = $this->db_r->query($sql,array($batch_id));
		$row = $query->row();
		$query->free_result();
		return $row;
	}
        
        /*
         * 生成batch_code
         */
        public function gen_purchase_batch_code() {
                $purchase_batch = $this->purchase_batch_model->filter_batch_code(array('batch_code like'=>'BT'. date('Ymd')."%"));
                $char_num = "000";
                if($purchase_batch){
                    $batch_code = $purchase_batch->batch_code;
                    $char_num = substr($batch_code, 10);
                    $int_num = intval($char_num)+1;

                    if($int_num <= 9){
                        $char_num = "00".$int_num;
                    }else if($int_num <= 99){
                        $char_num = "0".$int_num;
                    }else if($int_num <= 999){
                        $char_num = $int_num;
                    }else{
                        sys_msg(validation_errors(), 1);
                    }
                }
                
                return 'BT'. date('Ymd').$char_num;
        }
	
	public function get_provider_batcch($provider_id)
	{
		$sql = "SELECT batch_id,batch_code FROM ".$this->db_r->dbprefix('purchase_batch')." WHERE provider_id=? ORDER BY batch_id DESC";
		$query = $this->db_r->query($sql, array($provider_id));
		$list = $query->result();
		$query->free_result();
		return $list;
	}
	
	public function get_batch_brand($batch_id)
	{
		$sql = "SELECT b.brand_id,b.brand_name FROM ".$this->db_r->dbprefix('purchase_batch')." AS a ".
				" LEFT JOIN ".$this->db_r->dbprefix('product_brand')." AS b ON a.brand_id=b.brand_id ".
				" WHERE a.batch_id=?";
		$query = $this->db_r->query($sql, array($batch_id));
		return $query->row();
	}
	
	/**
	 * 查询所有未完成的盘点
	 */
	public function get_unfinished_inventory() {
		$sql = "SELECT * FROM ".$this->db_r->dbprefix('depot_inventory')." WHERE `status` < 3 ";
		$query = $this->db_r->query($sql);
		$result = $query->result();
		$query->free_result();
		return $result;
	}
	
	/**
	 * 查询某批次是否存在有调拨出库而无调拨入库
	 */
	public function get_unfinished_transfer($batch_id) {
		$sql = "SELECT a.* 
				FROM ".$this->db_r->dbprefix('depot_out_main')." AS a 
				LEFT JOIN ".$this->db_r->dbprefix('depot_out_sub')." AS b ON a.depot_out_id=b.depot_out_id
				LEFT JOIN ".$this->db_r->dbprefix('depot_iotype')." AS c ON a.depot_out_type=c.depot_type_id
				WHERE c.depot_type_code='ck007'
				AND b.batch_id=?
				AND NOT EXISTS (
					SELECT 1 FROM ".$this->db_r->dbprefix('depot_in_main')." AS d
					LEFT JOIN ".$this->db_r->dbprefix('depot_iotype')." AS e ON d.depot_in_type=e.depot_type_id
					WHERE e.depot_type_code='rk006' AND d.order_sn=a.depot_out_code
					AND d.audit_admin>0
				)";
		$query = $this->db_r->query($sql, array($batch_id));
		$result = $query->result();
		$query->free_result();
		return $result;
	}
	
	/**
	 * 查询某储位是否存在某批次的商品出入库信息
	 */
	public function get_trans_location($batch_id,$location_id) {
		$sql = "SELECT * FROM ".$this->db_r->dbprefix('transaction_info')." WHERE batch_id=? AND location_id=? limit 1";
		$query = $this->db_r->query($sql,array($batch_id,$location_id));
		$result = $query->row();
		$query->free_result();
		return $result;
	}
	
	/**
	 * 查询储位范围内是否存在某批次的商品出入库信息
	 */
	public function get_trans_locations($batch_id,$shelf_from,$shelf_to) {
		$sql = "SELECT * FROM ".$this->db_r->dbprefix('transaction_info')." t 
				LEFT JOIN ".$this->db_r->dbprefix('location_info')." l ON t.location_id=l.location_id
				WHERE t.batch_id=? AND CONCAT(CONCAT(l.location_code1,'-'),l.location_code2) BETWEEN ? AND ? limit 1";
		$query = $this->db_r->query($sql,array($batch_id,$shelf_from,$shelf_to));
		$result = $query->row();
		$query->free_result();
		return $result;
	}
}
###