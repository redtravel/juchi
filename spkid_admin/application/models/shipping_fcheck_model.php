<?php
#doc
#	classname:	Depotio_model
#	scope:		PUBLIC
#
#/doc

class Shipping_fcheck_model extends CI_Model
{

	public function filter ($filter)
	{
		$query = $this->db->get_where('shipping_fcheck', $filter, 1);
		return $query->row();
	}
	
	public function get_max_id ($batch_id)
	{
		$sql = "SELECT max(id) as max_id FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS a " .
				" WHERE a.batch_id=? ";
		$query = $this->db->query($sql, array($batch_id));
		return $query->row()->max_id;
	}

	public function get ($batch_id)
	{
		$sql = "SELECT a.batch_id,a.batch_sn,a.batch_type,a.shipping_id,a.lock_admin,a.lock_date,a.shipping_check," .
				"a.shipping_check_admin,a.shipping_check_date,a.finance_check,a.finance_check_admin,a.finance_check_date," .
				"a.create_admin,a.create_date,a.from_time,a.to_time, " .
				"b.shipping_name,c.admin_name as lock_user,d.admin_name as shipping_check_user,e.admin_name as finance_check_user " .
				" FROM ".$this->db->dbprefix('shipping_fcheck')." AS a " .
				" LEFT JOIN ".$this->db->dbprefix('shipping_info')." AS b ON a.shipping_id=b.shipping_id " .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." c ON c.admin_id = a.lock_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." d ON d.admin_id = a.shipping_check_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." e ON e.admin_id = a.finance_check_admin" .
				" WHERE a.batch_id=?";
		
		$query = $this->db->query($sql, array($batch_id));
		return $query->row();
	}
	
	public function get_summery($batch_id)
	{
	    $sql = "SELECT SUM(express_fee) AS express_fee, SUM(cod_fee) AS cod_fee, SUM(cod_amount) AS cod_amount
	            FROM ".$this->db->dbprefix('shipping_fcheck_sub')." WHERE batch_id = ?";
	    $row = $this->db->query($sql, array($batch_id))->row();
	    $row->cod_amount = floatval($row->cod_amount);
	    $row->express_fee = floatval($row->express_fee);
	    $row->cod_fee = floatval($row->cod_fee);
	    $row->total = $row->cod_amount - $row->express_fee - $row->cod_fee;
	    return $row;
	}

	public function find_page ($filter)
	{
		$from =  " FROM ".$this->db->dbprefix('shipping_fcheck')." AS a ";
		$where = " WHERE 1 ";
		$param = array();
		//$filter['invoice_no']
		if (!empty($filter['batch_sn']))
		{
			$where .= " AND a.batch_sn = ? ";
			$param[] = $filter['batch_sn'];
		}
		if ($filter['lock_status'] != '')
		{
			if($filter['lock_status'] == '0') {
				$where .= " AND a.lock_admin = 0 ";
			} else {
				$where .= " AND a.lock_admin > 0 ";
			}
			$param[] = $filter['lock_status'];
		}
		if ($filter['shipping_check'] != '')
		{
			$where .= " AND a.shipping_check = ? ";
			$param[] = $filter['shipping_check'];
		}
		if ($filter['finance_check'] != '')
		{
			$where .= " AND a.finance_check = ? ";
			$param[] = $filter['finance_check'];
		}
		if (!empty($filter['invoice_no']))
		{
			$where .= " AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS b WHERE b.batch_id = a.batch_id AND b.invoice_no  LIKE '%".$filter['invoice_no']."%' )";
		}
		
		$filter['sort_by'] = empty($filter['sort_by']) ? ' a.batch_id DESC ' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? '' : trim($filter['sort_order']);
		
		$sql = "SELECT COUNT(a.batch_id) AS total " . $from . $where;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->total;
		$filter = page_and_size($filter);
		
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		
		$sql =  "SELECT a.batch_id,a.batch_sn,a.batch_type,a.shipping_id,a.lock_admin,a.lock_date,a.shipping_check," .
				"a.shipping_check_admin,a.shipping_check_date,a.finance_check,a.finance_check_admin,a.finance_check_date," .
				"a.create_admin,a.create_date,a.from_time,a.to_time, " .
				"c.admin_name as lock_user,d.admin_name as shipping_check_user,e.admin_name as finance_check_user " .
				$from . 
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." c ON c.admin_id = a.lock_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." d ON d.admin_id = a.shipping_check_admin" .
				" LEFT JOIN ".$this->db->dbprefix('admin_info')." e ON e.admin_id = a.finance_check_admin" .
				$where .
				" ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'].
			 	" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		
		$query = $this->db->query($sql, $param);
		$return = $query->result();
		$query->free_result();
		
		return array('list' => $return, 'filter' => $filter);
	}

	public function find_page_sub ($batch_id,$filter)
	{
		$from = " FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS a ";
		$where = " WHERE batch_id = ?";
		
		$filter['sort_by'] = empty($filter['sort_by']) ? ' a.id DESC ' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? '' : trim($filter['sort_order']);
		
		$sql = "SELECT COUNT(a.id) AS total " . $from . $where;
		
		$query = $this->db->query($sql, $batch_id);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->total;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		
		$sql =  "SELECT a.id,a.batch_id,a.shipping_id,a.invoice_no,a.destination,a.weight,a.goods_number,a.order_amount," .
				"a.cod_amount,a.express_fee,a.cod_fee,a.order_id,a.rec_type,a.sign_date,a.create_admin,a.create_date,o.order_sn " .
				$from . 
				" LEFT JOIN ".$this->db->dbprefix('order_info')." AS o ON a.order_id = o.order_id AND a.rec_type=1 " .
				$where .
				" ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order'].
			 	" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		
		$query = $this->db->query($sql, $batch_id);
		$return = $query->result();
		$query->free_result();
		
		return array('list' => $return, 'filter' => $filter);
	}

	public function update ($data, $id)
	{
		$this->db->update('shipping_fcheck', $data, array('batch_id' => $id));
	}
	
	function update_shipping_sub($batch_id, $start=0) {
	    $sql = "UPDATE ".$this->db->dbprefix('shipping_fcheck_sub')." AS a, ".$this->db->dbprefix('order_info')." AS b
	        	SET a.rec_type=1,a.order_id=b.order_id,a.order_amount=b.order_price+b.shipping_fee-b.paid_price
				WHERE a.invoice_no=b.invoice_no AND a.batch_id=$batch_id AND a.id > $start ";
	    $this->db->query($sql);
	}
	
	function update_shipping_sub_deny($batch_id, $start=0) {
		$sql = "UPDATE ".$this->db->dbprefix('shipping_fcheck_sub')." AS a, ".$this->db->dbprefix('order_info')." AS b
		SET a.rec_type=1,a.order_id=b.order_id
		WHERE a.invoice_no=b.invoice_no AND a.batch_id=$batch_id AND a.id > $start ";
		$this->db->query($sql);
	}
	
	function db_lock_shipping_data($batch_id, $start=0){
	    $order_ids = array();
	    $sql = "select order_id,rec_type from ".$this->db->dbprefix('shipping_fcheck_sub')." where batch_id=$batch_id and id>$start ";
	    $query = $this->db->query($sql);
		$list = $query->result();
		if (!empty($list))
		{
			foreach ($list as $item)
			{
				if($item->rec_type==1){
					$order_ids[] = $item->order_id;
				}
			}
		}
	    if(!empty($order_ids)) $this->db->query("select order_id from ".$this->db->dbprefix('order_info')." where order_id ".  db_create_in($order_ids)." for update");
	    //if(!empty($change_ids)) $this->db->query("select change_id from ".$ecs->table('flc_change_info')." where change_id ".  db_create_in($change_ids)." for update");
	}
	
	function check_batch($batch,&$data,$start=0,$step='upload'){
		
	    $batch_id = intval($batch->batch_id);
	    $batch_sn = addslashes($batch->batch_sn);
	    $batch_type = intval($batch->batch_type);
	    
	    //echo '-execute delete order_id=0...';
	    //未匹配订单
	    $rec_ids = array();
	    $sql = "SELECT * FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd WHERE order_id=0 AND batch_id=$batch_id AND id >$start;";
	    $query = $this->db->query($sql);
		$list = $query->result();
		if (!empty($list))
		{
			foreach ($list as $item)
			{
				$rec_ids[] = $item->id;
		        $data[] = $this->convert2Array($item, '未找到匹配订单');
			}
		}
	    if(!empty($rec_ids) && $step=='upload') $this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck_sub')." where id ".  db_create_in($rec_ids));
	    
	    //echo '-execute delete duplicate invoice_no...';
	    //快递单号是否有重复
	    $rec_ids = array();
	    $arr = array(1,2,3,4);
	    if($batch_type==2){
	        $arr = array(1,2,4);
	    }elseif($batch_type==3){
	        $arr = array(1,3);
	    }
	    $sql = "SELECT sd.*
	            FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
	            WHERE sd.batch_id=$batch_id AND sd.id>$start 
	            AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS t WHERE t.invoice_no=sd.invoice_no AND t.id != sd.id AND t.batch_type ".  db_create_in($arr).")";
	    $query = $this->db->query($sql);
		$list = $query->result();
		if (!empty($list))
		{
			foreach ($list as $item)
			{
				$rec_ids[] = $item->id;
				$data[] = $this->convert2Array($item, '运单号有重复');
			}
		}
	    if (!empty($rec_ids) && $step=='upload') $this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck_sub')." where id " . db_create_in($rec_ids));
	
	    //换货单待付金额不等于0
	    /*
	    $sql = "SELECT * FROM ".$ecs->table('flc_shipping_data')." AS sd WHERE rec_type=2 AND cod_amount!=0 AND batch_id=$batch_id AND rec_id >$start;";
	    $rs = $this->db->query($sql);
	    $rec_ids = array();
	    while($row = $this->db->fetchRow($rs)){
	        $rec_ids[] = $row['rec_id'];
	        $row['err_msg'] = '换货运单代收金额大于零';
	        $data[] = $row;
	    }
	    if(!empty($rec_ids) && $step=='upload') $this->db->query("delete from ".$ecs->table('flc_shipping_data')." where rec_id ".  db_create_in($rec_ids));
		*/
		
		//echo '-execute delete cod_amount error...';
		//TODO 特殊支付方式处理
		//$special_pay_ids = array(100);
		//AND !(o.pay_id ".  db_create_in($special_pay_ids)." AND sd.cod_amount=0)
	    if($batch_type ==1 || $batch_type==2){
	        //订单待付金额与实收金额不相符
	        $rec_ids = array();
	        $sql = "SELECT sd.*
	                FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
	                LEFT JOIN ".$this->db->dbprefix('order_info')." AS o ON sd.order_id = o.order_id
	                WHERE sd.rec_type=1 AND sd.batch_id=$batch_id AND sd.id>$start
	                AND sd.cod_amount!=IF(o.order_status=1,(o.order_price+o.shipping_fee-o.paid_price),0) ";
	        $query = $this->db->query($sql);
			$list = $query->result();
			if (!empty($list))
			{
				foreach ($list as $item)
				{
					$rec_ids[] = $item->id;
					$data[] = $this->convert2Array($item, '待收货款金额不正确');
				}
			}
	        if (!empty($rec_ids) && $step=='upload') $this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck_sub')." where id " . db_create_in($rec_ids));
	
	        //代收款订单被锁定
	        if ($step == 'upload') {            
	            $rec_ids = array();
	            $sql = "SELECT sd.*
	                FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
	                LEFT JOIN ".$this->db->dbprefix('order_info')." AS o ON sd.order_id = o.order_id
	                WHERE sd.rec_type=1 AND sd.batch_id=$batch_id AND sd.id>$start
	                AND sd.cod_amount!=0 AND o.lock_admin!=0";
	            $query = $this->db->query($sql);
				$list = $query->result();
				if (!empty($list))
				{
					foreach ($list as $item)
					{
						$rec_ids[] = $item->id;
						$data[] = $this->convert2Array($item, '待收货款订单已被锁定，不能对帐');
					}
				}
	            if (!empty($rec_ids) && $step=='upload')  $this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck_sub')." where id " . db_create_in($rec_ids));
	        }
	
	        //代收款的订单号是否有重复
	        //TODO WHERE sd.batch_id=1 ???
	        $rec_ids = array();
	        $sql = "SELECT sd.*
	                FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
	                WHERE sd.batch_id=$batch_id AND sd.id>0 AND sd.rec_type=1
	                AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS t WHERE t.order_id = sd.order_id AND t.rec_type=1 AND t.id!=sd.id AND t.batch_type IN (1,2))";
	        $query = $this->db->query($sql);
			$list = $query->result();
			if (!empty($list))
			{
				foreach ($list as $item)
				{
					$rec_ids[] = $item->id;
					$data[] = $this->convert2Array($item, '同一订单存在两笔代收款记录');
				}
			}
	        if (!empty($rec_ids) && $step=='upload') $this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck_sub')." where id " . db_create_in($rec_ids));
	        
	        //订单状态检查
	        $rec_ids = array();
	        $sql = "SELECT sd.* FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
	        LEFT JOIN ".$this->db->dbprefix('order_info')." o ON sd.order_id=o.order_id
	        WHERE sd.batch_id=$batch_id AND sd.id >".$start."
	        AND sd.cod_amount > 0 
	        AND EXISTS (SELECT 1 FROM ".$this->db->dbprefix('order_info')." t
	        WHERE t.order_id=sd.order_id AND (t.is_ok=1 OR t.finance_admin>0 OR t.shipping_status=0))";//排除已完结或已财审或未发货的订单
	        $query = $this->db->query($sql);
	        $list = $query->result();
	        if (!empty($list))
	        {
	        	foreach ($list as $item)
	        	{
	        		$rec_ids[] = $item->id;
	        		$data[] = $this->convert2Array($item, '订单已财审或已完结');
	        	}
	        }
	        if(!empty($rec_ids) && $step=='upload') $this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck_sub')." where id ".  db_create_in($rec_ids));
	        
	    }
	}
	
	function check_batch_deny($batch,&$data,$start=0,$step='upload') {
		$batch_id = intval($batch->batch_id);
		$batch_sn = addslashes($batch->batch_sn);
		$batch_type = intval($batch->batch_type);
		 
		//未匹配订单
		$rec_ids = array();
		$sql = "SELECT * FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd WHERE order_id=0 AND batch_id=$batch_id AND id >$start;";
		$query = $this->db->query($sql);
		$list = $query->result();
		if (!empty($list))
		{
			foreach ($list as $item)
			{
				$rec_ids[] = $item->id;
				$data[] = $this->convert2Array($item, '未找到匹配订单');
			}
		}
		if(!empty($rec_ids) && $step=='upload') $this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck_sub')." where id ".  db_create_in($rec_ids));
		
		//订单状态无法拒收
		$rec_ids = array();
		$sql = "SELECT sd.* FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
		LEFT JOIN ".$this->db->dbprefix('order_info')." o ON sd.order_id=o.order_id
		WHERE batch_id=$batch_id AND id >".$start."
		AND NOT EXISTS (SELECT 1 FROM ".$this->db->dbprefix('order_info')." t 
		WHERE t.order_id=sd.order_id AND t.order_status=1 AND t.shipping_status=1 AND t.finance_admin=0 AND t.pay_id=1)";
		$query = $this->db->query($sql);
		$list = $query->result();
		if (!empty($list))
		{
			foreach ($list as $item)
			{
				$rec_ids[] = $item->id;
				$data[] = $this->convert2Array($item, '关联的订单状态不对');
			}
		}
		if(!empty($rec_ids) && $step=='upload') $this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck_sub')." where id ".  db_create_in($rec_ids));
		
		//快递单号是否有重复
		$rec_ids = array();
		$sql = "SELECT sd.*
		FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
		WHERE sd.batch_id=$batch_id AND sd.id>$start
		AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS t WHERE t.invoice_no=sd.invoice_no AND t.id != sd.id AND t.batch_type in (1,2,4) )";
		$query = $this->db->query($sql);
		$list = $query->result();
		if (!empty($list))
		{
			foreach ($list as $item)
			{
				$rec_ids[] = $item->id;
				$data[] = $this->convert2Array($item, '运单号有重复');
			}
		}
		if (!empty($rec_ids) && $step=='upload') $this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck_sub')." where id " . db_create_in($rec_ids));
		
	}
	
	function lock_order_change($batch, $admin_id, $start=0) {
	    $batch_id = intval($batch->batch_id);
	    $batch_sn = addslashes($batch->batch_sn);
	    $now = date('Y-m-d H:i:s');
	    //批量插入订单操作（加锁）日志
	    $sql = "INSERT INTO ".$this->db->dbprefix('order_action')."(order_id,order_status,shipping_status,pay_status,action_note,create_admin,create_date,is_return)
	            SELECT o.order_id,o.order_status,o.shipping_status,o.pay_status,'物流对帐单 $batch_sn 加对帐锁，订单锁定','$admin_id','$now',0
	            FROM ".$this->db->dbprefix('shipping_fcheck_sub')."  AS sd, ".$this->db->dbprefix('order_info')."  AS o
	            WHERE sd.order_id = o.order_id AND sd.rec_type=1 AND o.lock_admin=0 AND batch_id=$batch_id AND sd.id>$start";
	    $this->db->query($sql);
	    //批量加锁订单
	    $sql = "UPDATE ".$this->db->dbprefix('order_info')." AS o, ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
	            SET o.lock_admin=-1 
	            WHERE sd.batch_id = $batch_id AND sd.id>$start AND sd.order_id = o.order_id AND sd.rec_type=1 AND o.lock_admin=0 ";
	    $this->db->query($sql);
	    /*//批量插入换货单操作（加锁）日志
	    $sql = "INSERT INTO " . $ecs->table('flc_change_action') . "(order_id,action_user,order_status,shipping_status,shipped_status,action_note,log_time)
	            SELECT c.change_id,'$admin_name',c.change_status,c.shipping_status,c.shipped_status,'物流对帐单 $batch_sn 加对帐锁，换货单锁定',$now
	            FROM " . $ecs->table('flc_shipping_data') . " AS sd, " . $ecs->table('flc_change_info') . " AS c
	            WHERE sd.order_id = c.change_id AND sd.rec_type=2 AND c.locked_aid=0 AND batch_id=$batch_id AND sd.rec_id>$start";
	    $this->db->query($sql);
	    //批量加锁换货单
	    $sql = "UPDATE " . $ecs->table('flc_change_info') . " AS c, " . $ecs->table('flc_shipping_data') . " AS sd
	            SET c.locked_aid=-3 ,c.locked_time=$now
	            WHERE sd.batch_id = $batch_id AND sd.rec_id>$start AND sd.order_id = c.change_id AND sd.rec_type=2 AND c.locked_aid=0";
	    $this->db->query($sql);*/
	}
	
	function unlock_order_change($batch, $admin_id, $rec_id=0, $reason='财审'){
	    $batch_id = $batch->batch_id;
	    $batch_sn = addslashes($batch->batch_sn);
	    $reason = "物流对帐单 $batch_sn $reason ，自动解锁";
	    $now = date('Y-m-d H:i:s');
	    //批量插入订单操作（解锁）日志
	    $sql = "INSERT INTO ".$this->db->dbprefix('order_action')."(order_id,order_status,shipping_status,pay_status,action_note,create_admin,create_date,is_return)
	            SELECT o.order_id,o.order_status,o.shipping_status,o.pay_status,'$reason',$admin_id,'$now',0
	            FROM ".$this->db->dbprefix('shipping_fcheck_sub')."  AS sd, " . $this->db->dbprefix('order_info'). "  AS o
	            WHERE sd.order_id = o.order_id AND sd.rec_type=1 AND o.lock_admin=-1 AND sd.batch_id=$batch_id".($rec_id>0?" AND sd.id=$rec_id":"").
	            " AND NOT EXISTS(
					SELECT 1 FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS t ,".$this->db->dbprefix('shipping_fcheck')." AS ta
					WHERE t.batch_id = ta.batch_id AND ta.shipping_check=0 AND t.order_id=sd.order_id AND ".($rec_id>0?" t.id!=sd.id ":" t.batch_id!=sd.batch_id ")."
			    )";
	    $this->db->query($sql);
	    //批量解锁订单
	    $sql = "UPDATE ".$this->db->dbprefix('order_info')." AS o, ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
	            SET o.lock_admin=0
	            WHERE sd.batch_id = $batch_id AND sd.order_id = o.order_id AND sd.rec_type=1 AND o.lock_admin=-1".($rec_id>0?" AND sd.id=$rec_id":"").
	            " AND NOT EXISTS(
					SELECT 1 FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS t ,".$this->db->dbprefix('shipping_fcheck')." AS ta
					WHERE t.batch_id = ta.batch_id AND ta.shipping_check=0 AND t.order_id=sd.order_id AND ".($rec_id>0?" t.id!=sd.id ":" t.batch_id!=sd.batch_id ")."
			    )";
	    $this->db->query($sql);
	    /*//批量插入换货单操作日志
	    $sql = "INSERT INTO " . $ecs->table('flc_change_action') . "(order_id,action_user,order_status,shipping_status,shipped_status,action_note,log_time)
	            SELECT c.change_id,'$admin_name',c.change_status,c.shipping_status,c.shipped_status,'$reason',$now
	            FROM " . $ecs->table('flc_shipping_data') . " AS sd, " . $ecs->table('flc_change_info') . " AS c
	            WHERE sd.order_id = c.change_id AND sd.rec_type=2 AND c.locked_aid=-3 AND sd.batch_id=$batch_id".($rec_id>0?" AND sd.rec_id=$rec_id":"").
	             " AND NOT EXISTS(
			SELECT 1 FROM ".$ecs->table('flc_shipping_data')." AS t ,".$ecs->table('flc_shipping_data_info')." AS ta
			WHERE t.batch_id = ta.batch_id AND ta.shipping_check=0 AND t.order_id=sd.order_id AND ".($rec_id>0?" t.rec_id!=sd.rec_id ":" t.batch_id!=sd.batch_id ")."
		    )";
	    $this->db->query($sql);
	    //批量解锁换货单
	    $sql = "UPDATE " . $ecs->table('flc_change_info') . " AS c, " . $ecs->table('flc_shipping_data') . " AS sd
	            SET c.locked_aid=0 ,c.locked_time=0
	            WHERE sd.batch_id = $batch_id AND sd.order_id = c.change_id AND sd.rec_type=2 AND c.locked_aid=-3".($rec_id>0?" AND sd.rec_id=$rec_id":"").
	             " AND NOT EXISTS(
			SELECT 1 FROM ".$ecs->table('flc_shipping_data')." AS t ,".$ecs->table('flc_shipping_data_info')." AS ta
			WHERE t.batch_id = ta.batch_id AND ta.shipping_check=0 AND t.order_id=sd.order_id AND ".($rec_id>0?" t.rec_id!=sd.rec_id ":" t.batch_id!=sd.batch_id ")."
		    )";
	    $this->db->query($sql);*/
	}
	
	public function finance_order($batch, $admin_id) {
		$PAY_ID = 1;
		$now = date('Y-m-d H:i:s');
		$batch_id = $batch->batch_id;
		//批量添加支付记录
        $sql = "INSERT INTO ".$this->db->dbprefix('order_payment')." (order_id,is_return,pay_id,payment_money,payment_admin,payment_date,payment_remark)
                SELECT o.order_id,0,$PAY_ID,sd.cod_amount,$admin_id,'$now','物流对帐单 ".addslashes($batch->batch_sn)." 财审添加支付记录'
                FROM ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
                LEFT JOIN ".$this->db->dbprefix('order_info')."  AS o ON sd.order_id = o.order_id
                WHERE sd.batch_id = $batch_id AND sd.rec_type=1 AND sd.cod_amount>0";
        $this->db->query($sql);                    
        //批量更新订单价格，并完结订单 去除o.order_price = o.order_price-sd.cod_amount,
        $sql = "UPDATE ".$this->db->dbprefix('order_info')." AS o, ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
                SET
                o.paid_price = o.paid_price+sd.cod_amount ,
                o.pay_status=1,o.finance_admin=$admin_id, o.finance_date='$now',
                o.is_ok=1, o.is_ok_admin=$admin_id, o.is_ok_date='$now'
                WHERE sd.batch_id=$batch_id AND o.order_id = sd.order_id AND sd.rec_type=1 AND sd.cod_amount>0";
        $this->db->query($sql);
        //批量更新事务表的财审时间
        $sql = "UPDATE ".$this->db->dbprefix('transaction_info')." AS t,
                ".$this->db->dbprefix('order_info')." AS o,
				".$this->db->dbprefix('shipping_fcheck_sub')."  AS sd
                SET t.finance_check_admin = $admin_id,t.finance_check_date = '$now'
                WHERE sd.batch_id=$batch_id AND o.order_id = sd.order_id AND sd.rec_type=1 AND sd.cod_amount>0
                AND t.trans_sn = o.order_sn AND t.trans_type=3 AND t.trans_status = 2";
        $this->db->query($sql);
        //批量添加操作日志
        $sql = "INSERT INTO ".$this->db->dbprefix('order_action')." (order_id,order_status,shipping_status,pay_status,action_note,create_admin,create_date,is_return)
                SELECT order_id,1,1,1,'物流对帐单 ".addslashes($batch->batch_sn)." 财审，订单自动财审并完结',$admin_id,'$now',0
                FROM ".$this->db->dbprefix('shipping_fcheck_sub')."
                WHERE batch_id=$batch_id AND rec_type=1 AND cod_amount>0";
        $this->db->query($sql);
	}
        //物流公司实际派送包裹的运费
        public function finance_order_shipping($batch){            
            $sql = "UPDATE ".$this->db->dbprefix('order_info')." AS o, ".$this->db->dbprefix('shipping_fcheck_sub')." AS sd
                    SET
                    o.real_shipping_fee = sd.express_fee 
                    WHERE sd.batch_id=$batch->batch_id AND o.order_id = sd.order_id AND sd.rec_type=1 AND sd.express_fee>0";
            $this->db->query($sql);
        }
	
	public function deny_check($batch, $admin_id) {
		//批量添加操作日志
		$now = date('Y-m-d H:i:s');
		$batch_id = $batch->batch_id;
		$sql = "INSERT INTO ".$this->db->dbprefix('order_action')." (order_id,order_status,shipping_status,pay_status,action_note,create_admin,create_date,is_return)
		SELECT a.order_id,o.order_status,o.shipping_status,o.pay_status,'物流对帐单 ".addslashes($batch->batch_sn)." 客户拒收',$admin_id,'$now',0
		FROM ".$this->db->dbprefix('shipping_fcheck_sub')." a LEFT JOIN ".$this->db->dbprefix('order_info')." o ON a.order_id=o.order_id WHERE batch_id=$batch_id AND rec_type=1 ";
		//echo $sql;die;
		$this->db->query($sql);
	}

	public function insert ($data)
	{
		$this->db->insert('shipping_fcheck', $data);
		return $this->db->insert_id();
	}

	public function insert_sub ($data)
	{
		//echo "insert shipping_fcheck_sub: batch_id=".$data['batch_id'].".....";
		$this->db->insert('shipping_fcheck_sub', $data);
		return $this->db->insert_id();
	}
	
	public function delete_related($batch_id) {
		$this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck_sub')." where batch_id = $batch_id ");
        $this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck'). " where batch_id = $batch_id ");
	}
	
	public function delete_sub($id) {
		$this->db->query("delete from ".$this->db->dbprefix('shipping_fcheck_sub')." where id = $id ");
	}

	private function convert2Array($item, $msg) {
		$row = array();
		$row['invoice_no'] = $item->invoice_no;
		$row['destination'] = $item->destination;
		$row['weight'] = $item->weight;
		$row['goods_number'] = $item->goods_number;
		$row['cod_amount'] = $item->cod_amount;
		$row['express_fee'] = $item->express_fee;
		$row['cod_fee'] = $item->cod_fee;
		$row['sign_date'] = $item->sign_date;
        $row['err_msg'] = $msg;
        return $row;
	}
}
###