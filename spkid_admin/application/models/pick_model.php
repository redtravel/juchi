<?php
class Pick_model extends CI_Model
{
	public function filter($filter){
		//$query=$this->db->get_where('pick_info',$filter,1);
        $sql = "SELECT pi.*, s.shipping_name FROM ".$this->db->dbprefix('pick_info')." pi 
            INNER JOIN ".$this->db->dbprefix('shipping_info')." s ON pi.shipping_id = s.shipping_id WHERE pi.pick_sn = '".$filter['pick_sn']."'";
        $query = $this->db->query($sql);
        return $query->row();
	}
	
	public function query_pick_sub($filter){
	    $query = $this->db->get_where('pick_sub', $filter);
            return $query->result();
	}
        
        public function lock_pick($pick_sn)
        {
            $sql="SELECT * FROM ".$this->db->dbprefix('pick_info')." WHERE pick_sn=? FOR UPDATE";
            $query=$this->db->query($sql,array($pick_sn));
            return $query->row();
        }
	
	public function insert($data){
		while(True){
			mt_srand((double) microtime() * 1000000);
			$pick_sn="PK".date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
			$sql="INSERT INTO ".$this->db->dbprefix('pick_info')." 
			SET pick_sn='{$pick_sn}',type=?,shipping_id=?,create_date=?,create_admin=?,total_num=?,over_num=?
			ON DUPLICATE KEY UPDATE pick_sn=pick_sn;";
			$this->db->query($sql,array($data['type'],$data['shipping_id'],$data['create_date'],$data['create_admin'],$data['total_num'],$data['over_num']));
			if($this->db->affected_rows()>0) break;
		}
		return $pick_sn;
	}
	
	public function update($update,$pick_sn){
		$this->db->update('pick_info',$update,array('pick_sn'=>$pick_sn));
	}
	
	public function update_pick_sub($update,$filter){
		$this->db->update('pick_sub',$update,$filter);
	}
	
	public function delete($pick_sn){
		$this->db->delete('pick_info',array('pick_sn'=>$pick_sn));
	}

    public function deleteSub($filter) {
        $this->db->delete('pick_sub', $filter);    
    }

	public function pick_list($filter){
		$from = " FROM ".$this->db->dbprefix('pick_info')." AS p 
				LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON p.create_admin = a.admin_id
				LEFT JOIN ".$this->db->dbprefix('admin_info')." AS pa ON p.pick_admin = pa.admin_id
				LEFT JOIN ".$this->db->dbprefix('admin_info')." AS q ON p.qc_admin = q.admin_id
				LEFT JOIN ".$this->db->dbprefix('shipping_info')." AS s ON p.shipping_id=s.shipping_id ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['pick_sn']))
		{
			$where .= " AND p.pick_sn LIKE ? ";
			$param[] = '%' . $filter['pick_sn'] . '%';
		}
		if (!empty($filter['start_date']))
		{
			$where .= " AND p.create_date >= ? ";
			$param[] = $filter['start_date'];
		}
		if (!empty($filter['end_date']))
		{
			$where .= " AND p.create_date <= ? ";
			$param[] = $filter['end_date'];
		}
		if ($filter['over']!=-1)
		{
			$where .= " AND p.total_num ".($filter['over']==1?'=':'>')." p.over_num ";
		}
		if ($filter['pick']!=-1)
		{
			$where .= " AND p.pick_status <= ? ";
			$param[] = $filter['pick'];
		}
		if ($filter['is_print'] == 1)
		{
			$where .= " AND p.is_print = 1 ";
		}

		if (!empty($filter['order_sn']))
		{
			if(strtoupper(substr($filter['order_sn'],0,2))=='DD'){
				$where .= " AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_info')." as o WHERE o.pick_sn=p.pick_sn AND o.order_sn=?) ";
			}else{
				$where .= " AND EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_change_info')." as c WHERE c.pick_sn=p.pick_sn AND c.change_sn=?) ";
			}
			$param[] = $filter['order_sn'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'p.create_date' : trim($filter['sort_by']);
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
        $sql = "SELECT p.*,a.admin_name,s.shipping_name, pa.admin_name AS pick_user, q.admin_name AS qc_user,".
               " case pick_status when 1 then '拣货中' when 2 then '已拣货' else '未拣货' end AS pick_status,".
               " ifnull(p.pick_admin, '') as pick_admin, ifnull(p.pick_date, '') as pick_date, ".
               "ifnull(p.qc_admin, '') as qc_admin, ifnull(p.qc_date, '') as qc_date "
			   . $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
			   . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);		
	}
	
	public function pick_status(){
		//订单不代收
		$sql="SELECT s.shipping_id,s.shipping_name,COUNT(*) AS pick_num
			FROM ".$this->db->dbprefix('order_info')." AS o
			LEFT JOIN ".$this->db->dbprefix('shipping_info')." AS s ON o.shipping_id=s.shipping_id
			LEFT JOIN ".$this->db->dbprefix('order_routing')." AS r ON o.source_id=r.source_id AND o.shipping_id=r.shipping_id AND o.pay_id=r.pay_id
			WHERE o.pick_sn='' AND o.odd=0 AND o.order_status=1 AND o.shipping_status=0 ".
			"AND (o.pay_status=1 AND r.routing='F' ) ".
			"AND o.lock_admin IN (0,-1) AND o.shipping_id!=".SHIPPING_ID_CAC."
			AND o.order_price+o.shipping_fee<=o.paid_price
			AND	NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_product')." AS op WHERE op.order_id=o.order_id AND op.consign_num>0 )
			GROUP BY o.shipping_id;";
		$query=$this->db->query($sql);
		$order_status=$query->result();
		//订单代收
		$sql="SELECT s.shipping_id,s.shipping_name,COUNT(*) AS pick_num
			FROM ".$this->db->dbprefix('order_info')." AS o
			LEFT JOIN ".$this->db->dbprefix('shipping_info')." AS s ON o.shipping_id=s.shipping_id
			LEFT JOIN ".$this->db->dbprefix('order_routing')." AS r ON o.source_id=r.source_id AND o.shipping_id=r.shipping_id AND o.pay_id=r.pay_id
			WHERE o.pick_sn='' AND o.odd=0 AND o.order_status=1 AND o.shipping_status=0  
                        AND r.routing='S' 
			AND o.lock_admin IN (0,-1) AND o.shipping_id!=".SHIPPING_ID_CAC." 
			AND o.order_price+o.shipping_fee>=o.paid_price
			AND	NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_product')." AS op WHERE op.order_id=o.order_id AND op.consign_num>0 )
			GROUP BY o.shipping_id;";
		$query=$this->db->query($sql);
		$ordercod_status=$query->result();
                /*
		//换货单
		$sql="SELECT s.shipping_id,s.shipping_name,COUNT(*) AS pick_num
			FROM ".$this->db->dbprefix('order_change_info')." AS c
			LEFT JOIN ".$this->db->dbprefix('shipping_info')." AS s ON c.shipping_id=s.shipping_id
			LEFT JOIN ".$this->db->dbprefix('order_info')." AS o ON c.order_id=o.order_id
			WHERE c.pick_sn='' AND c.odd=0 AND o.pay_status=1 AND c.change_status=1 AND c.shipped_status=1 AND c.shipping_status=0 
			AND c.lock_admin IN (-1,0) AND c.shipping_id!=".SHIPPING_ID_CAC."
			AND	NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_change_product')." AS cp WHERE cp.change_id=c.change_id AND cp.consign_num>0 )
			GROUP BY c.shipping_id";
		$query=$this->db->query($sql);
		$change_status=$query->result();
                 */
                $change_status = array();
		return array($order_status,$ordercod_status,$change_status);
	}
	
	
	public function assign_sub(){
		//取出订单换货单
		$sql="SELECT o.confirm_date,o.order_id AS id, o.order_sn AS sn
				FROM  ".$this->db->dbprefix('order_info')." AS o
				WHERE o.order_status=1 AND o.shipping_status=0 AND o.lock_admin<=0
				AND EXISTS(
					SELECT 1 FROM ".$this->db->dbprefix('order_product')." AS op,".$this->db->dbprefix('product_sub')." AS sub 
					WHERE op.order_id=o.order_id 
					AND sub.product_id=op.product_id AND sub.color_id=op.color_id AND sub.size_id=op.size_id
					AND op.consign_num>0 AND sub.gl_num>0
					LIMIT 1
				)";
		$query=$this->db->query($sql);
		$order_list=$query->result();
		/*
		$sql="SELECT c.confirm_date,c.change_id,c.change_sn
				FROM ".$this->db->dbprefix('order_change_info')." AS c
				WHERE c.change_status=1 AND c.shipping_status=0 AND c.lock_admin<=0
				AND EXISTS (
					SELECT 1 FROM ".$this->db->dbprefix('order_change_product')." AS cp,".$this->db->dbprefix('product_sub')." AS sub 
					WHERE cp.change_id=c.change_id AND sub.product_id=cp.product_id AND sub.color_id=cp.color_id AND sub.size_id=cp.size_id
					AND cp.consign_num>0 AND sub.gl_num>0
					LIMIT 1
				)";
		$query=$this->db->query($sql);
		$change_list=$query->result();
                $list=array_merge($order_list,$change_list);
                 */
                
		$list=$order_list;
		array_multisort($list,SORT_ASC);
		foreach($list as $row){
			$this->db->trans_start();
			if(substr(strtoupper($row->sn),0,2)=='DD'){
				//对于订单的处理
				$row->order_sn=$row->sn;
				$order=$this->order_model->lock_order($row->id);
				if($order->order_status!=1 || $order->shipping_status!=0 || $order->lock_admin>0) {
					$this->db->trans_rollback();
					continue;
				}
				//取出订单商品表
				$sql="SELECT op.*,sub.sub_id,info.finance_date, info.finance_admin 
					FROM ty_order_product AS op
					LEFT JOIN ty_product_sub AS sub ON sub.product_id=op.product_id AND sub.color_id=op.color_id AND sub.size_id=op.size_id
					LEFT JOIN ty_order_info info ON op.order_id = info.order_id
                                        WHERE op.order_id=? AND op.consign_num>0 AND sub.gl_num>0";
				$query=$this->db->query($sql,array($row->id));
				$op_list=$query->result();
				if(!$op_list){
					$this->db->trans_rollback();
					continue;
				}
				foreach($op_list as $op){
					$sub=$this->product_model->lock_sub(array('sub_id'=>$op->sub_id));
					$num=min($sub->gl_num,$op->consign_num);
					$info=$this->order_model->assign_trans($row,$op,$num,$op->op_id, $op->product_price);
					if($info['err']) {
						$this->db->trans_rollback();
						continue;
					}
					$this->order_model->update_product(array('consign_num'=>$op->consign_num-$num),$op->op_id);
					$this->product_model->update_sub(array('gl_num'=>$sub->gl_num-$num,'wait_num'=>max(0,$sub->wait_num-$num)),$sub->sub_id);
				}
			}elseif(substr(strtoupper($row->sn),0,2)=='HH'){
				//对于换货单的处理
				$row->change_sn=$row->sn;
				$change=$this->change_model->lock_change($row->id);
				if($change->change_status!=1 || $change->shipping_status!=0 || $change->lock_admin>0) {
					$this->db->trans_rollback();
					continue;
				}
				//取出换货单商品表
				$sql="SELECT cp.*,sub.sub_id
					FROM ty_order_change_product AS cp
					LEFT JOIN ty_product_sub AS sub ON sub.product_id=cp.product_id AND sub.color_id=cp.color_id AND sub.size_id=cp.size_id
					WHERE cp.change_id=? AND cp.consign_num>0 AND sub.gl_num>0";
				$query=$this->db->query($sql,array($row->id));
				$cp_list=$query->result();
				if(!$cp_list){
					$this->db->trans_rollback();
					continue;
				}
				foreach($cp_list as $cp){
					$sub=$this->product_model->lock_sub(array('sub_id'=>$cp->sub_id));
					$num=min($sub->gl_num,$cp->consign_num);
					$info=$this->change_model->assign_trans($row,$cp,$num,$cp->cp_id);
					if($info['err']) {
						$this->db->trans_rollback();
						continue;
					}
					$this->change_model->update_product(array('consign_num'=>$cp->consign_num-$num),$cp->cp_id);
					$this->product_model->update_sub(array('gl_num'=>$sub->gl_num-$num,'wait_num'=>max(0,$sub->wait_num-$num)),$sub->sub_id);
				}
			}
			$this->db->trans_commit();
		}
		
	}
	
	public function pick_order($pick_sn,$shipping_id,$pick_num,$hand_type=0,$admin_id=0){
		$sql="SELECT o.order_id
			FROM ".$this->db->dbprefix('order_info')." AS o
			LEFT JOIN ".$this->db->dbprefix('order_routing')." AS r ON o.source_id=r.source_id AND o.shipping_id=r.shipping_id AND o.pay_id=r.pay_id
			WHERE o.pick_sn='' AND o.odd=0 AND o.order_status=1 AND o.shipping_status=0 ".
			//"AND (o.pay_status=0 AND r.routing='S' OR o.pay_status=1) ".
                        "AND (o.pay_status=1 AND r.routing='F' ) ".
			"AND o.lock_admin IN (0,-1) AND o.shipping_id=?
			AND o.order_price+o.shipping_fee<=o.paid_price
			AND	NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_product')." AS op WHERE op.order_id=o.order_id AND op.consign_num>0 )
			ORDER BY o.order_id ASC LIMIT ?;";
		$query=$this->db->query($sql,array($shipping_id,$pick_num));
		$result=$query->result();
		if(!$result) return 0;
		$order_ids=array_keys(index_array($result,'order_id'));
		
		//添加拣货子表信息
                $pick_cell = 1;
                foreach ($order_ids as $order_id) {
			$query = $this->db->get_where('order_info', array('order_id'=>$order_id), 1);
			$orders = $query->row();
			$sql = "SELECT *  
					FROM ".$this->db->dbprefix('transaction_info')." 
					WHERE trans_status != 5 AND trans_sn = '".$orders->order_sn."' ";
			$query = $this->db->query($sql);
			$product_list = $query->result();
                        if (empty($product_list)) return false;
                        if ($hand_type == 1) {
                            //手动拣货类型（pick_num,pick_admin,pick_date）
                            $sql1 = "INSERT INTO ".$this->db->dbprefix('pick_sub')." (pick_sn, product_id,color_id,size_id,depot_id,batch_id,location_id,product_number,rel_no,pick_cell,pick_num,pick_admin,pick_date, expire_date, production_batch) VALUES ";
                            foreach ($product_list as $product) {
                                     $sql1 .= "('".$pick_sn."','".$product->product_id."','".$product->color_id."','".$product->size_id."',".
                                                    "'".$product->depot_id."','".$product->batch_id."','".$product->location_id."','".abs($product->product_number)."',".
                                                    "'".$orders->order_sn."','".$pick_cell."','".abs($product->product_number)."','".$admin_id."','".date('Y-m-d H:i:s')."','".$product->expire_date."' ,'".$product->production_batch."'),";
                            }
                        }else{
                            //扫描拣货类型
                            $sql1 = "INSERT INTO ".$this->db->dbprefix('pick_sub')." (pick_sn, product_id,color_id,size_id,depot_id,batch_id,location_id,product_number,rel_no,pick_cell, expire_date, production_batch) VALUES ";
                            foreach ($product_list as $product) {
                                     $sql1 .= "('".$pick_sn."','".$product->product_id."','".$product->color_id."','".$product->size_id."',".
                                                    "'".$product->depot_id."','".$product->batch_id."','".$product->location_id."','".abs($product->product_number)."',".
                                                    "'".$orders->order_sn."','".$pick_cell."', '".$product->expire_date."' ,'".$product->production_batch."'),";
                            }
                        }
			$sql1 = substr($sql1,0,-1);
			$this->db->query($sql1);
                        $pick_cell = $pick_cell + 1;
		}
		if ($hand_type == 1) { //手动拣货类型
                    $is_pick = 1;
                    $pick_admin = $admin_id;
                    $pick_date = date("Y-m-d H:i:s");
                } else { //扫描拣货类型
                    $is_pick = 0;
                    $pick_admin = '';
                    $pick_date = '';
                }
		$sql="UPDATE ".$this->db->dbprefix('order_info')." AS o
			LEFT JOIN ".$this->db->dbprefix('order_routing')." AS r ON o.source_id=r.source_id AND o.shipping_id=r.shipping_id AND o.pay_id=r.pay_id
			SET o.pick_sn=?,o.is_pick=?,pick_admin=?,pick_date=? 
			WHERE o.pick_sn='' AND o.odd=0 AND o.order_status=1 AND o.shipping_status=0 
			AND (o.pay_status=0 AND r.routing='S' OR o.pay_status=1) 
			AND o.lock_admin IN (0,-1) AND o.shipping_id=?
			AND o.order_price+o.shipping_fee<=o.paid_price
			AND	NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_product')." AS op WHERE op.order_id=o.order_id AND op.consign_num>0 )
			AND o.order_id ".  db_create_in($order_ids);
		$this->db->query($sql,array($pick_sn,$is_pick,$pick_admin,$pick_date,$shipping_id));
		return $this->db->affected_rows();		
	}
	
	public function pick_ordercod($pick_sn,$shipping_id,$pick_num,$hand_type=0,$admin_id=0){
		$sql="SELECT o.order_id
			FROM ".$this->db->dbprefix('order_info')." AS o
			LEFT JOIN ".$this->db->dbprefix('order_routing')." AS r ON o.source_id=r.source_id AND o.shipping_id=r.shipping_id AND o.pay_id=r.pay_id
			WHERE o.pick_sn='' AND o.odd=0 AND o.order_status=1 AND o.shipping_status=0 ".
                        //"AND o.pay_status=0 AND r.routing='S' ".
                        "AND r.routing='S' ".
			"AND o.lock_admin IN (0,-1) AND o.shipping_id=?
			AND o.order_price+o.shipping_fee>=o.paid_price
			AND	NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_product')." AS op WHERE op.order_id=o.order_id AND op.consign_num>0 )
			ORDER BY o.order_id ASC LIMIT ?;";
		$query=$this->db->query($sql,array($shipping_id,$pick_num));
		$result=$query->result();
		if(!$result) return 0;
		$order_ids=array_keys(index_array($result,'order_id'));
		
		//添加拣货子表信息
                $pick_cell = 1;
		foreach ($order_ids as $order_id) {
			$query = $this->db->get_where('order_info', array('order_id'=>$order_id), 1);
			$orders = $query->row();
			$sql = "SELECT product_id,color_id,size_id,depot_id,batch_id,location_id,product_number,expire_date,production_batch
					FROM ".$this->db->dbprefix('transaction_info')." 
					WHERE trans_status != 5 AND trans_sn = '".$orders->order_sn."' ";
			$query = $this->db->query($sql);
			$product_list = $query->result();
                        if (empty($product_list)) return false;
                         if ($hand_type == 1) {
                            //手动拣货类型（pick_num,pick_admin,pick_date）
                            $sql1 = "INSERT INTO ".$this->db->dbprefix('pick_sub')." (pick_sn, product_id,color_id,size_id,depot_id,batch_id,location_id,product_number,rel_no,pick_cell,pick_num,pick_admin,pick_date,expire_date,production_batch) VALUES ";
                            foreach ($product_list as $product) {
                                     $sql1 .= "('".$pick_sn."','".$product->product_id."','".$product->color_id."','".$product->size_id."',".
                                                    "'".$product->depot_id."','".$product->batch_id."','".$product->location_id."','".abs($product->product_number)."',".
                                                    "'".$orders->order_sn."','".$pick_cell."','".abs($product->product_number)."','".$admin_id."','".date('Y-m-d H:i:s')."','".$product->expire_date."','".$product->production_batch."'),";
                            }
                         } else {
                            $sql1 = "INSERT INTO ".$this->db->dbprefix('pick_sub')." (pick_sn, product_id,color_id,size_id,depot_id,batch_id,location_id,product_number,rel_no,pick_cell,expire_date,production_batch) VALUES ";
                            foreach ($product_list as $product) {
                                     $sql1 .= "('".$pick_sn."','".$product->product_id."','".$product->color_id."','".$product->size_id."',".
                                                    "'".$product->depot_id."','".$product->batch_id."','".$product->location_id."','".abs($product->product_number)."',".
                                                    "'".$orders->order_sn."','".$pick_cell."','".$product->expire_date."','".$product->production_batch."'),";
                            }
                         }
			$sql1 = substr($sql1,0,-1);
			$this->db->query($sql1);
                        $pick_cell = $pick_cell + 1;
		}
		if ($hand_type == 1) { //手动拣货类型
                    $is_pick = 1;
                    $pick_admin = $admin_id;
                    $pick_date = date("Y-m-d H:i:s");
                } else { //扫描拣货类型
                    $is_pick = 0;
                    $pick_admin = '';
                    $pick_date = '';
                }
		$sql="UPDATE ".$this->db->dbprefix('order_info')." AS o
			LEFT JOIN ".$this->db->dbprefix('order_routing')." AS r ON o.source_id=r.source_id AND o.shipping_id=r.shipping_id AND o.pay_id=r.pay_id
			SET o.pick_sn=?,o.is_pick=?,pick_admin=?,pick_date=? 
			WHERE o.pick_sn='' AND o.odd=0 AND o.order_status=1 AND o.shipping_status=0 AND o.pay_status=0 AND r.routing='S'
			AND o.lock_admin IN (0,-1) AND o.shipping_id=?
			AND o.order_price+o.shipping_fee>=o.paid_price
			AND	NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_product')." AS op WHERE op.order_id=o.order_id AND op.consign_num>0 )
			AND o.order_id ".  db_create_in($order_ids);
		$query=$this->db->query($sql,array($pick_sn,$is_pick,$pick_admin,$pick_date,$shipping_id));
		return $this->db->affected_rows();		
	}
	
	public function pick_change($pick_sn,$shipping_id,$pick_num){
		$sql="SELECT c.change_id
			FROM ".$this->db->dbprefix('order_change_info')." AS c
			LEFT JOIN ".$this->db->dbprefix('order_info')." AS o ON c.order_id=o.order_id
			WHERE c.pick_sn='' AND c.odd=0 AND o.pay_status=1 AND c.change_status=1 AND c.shipped_status=1 AND c.shipping_status=0
			AND c.lock_admin IN (-1,0) AND c.shipping_id=?
			AND	NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_change_product')." AS cp WHERE cp.change_id=c.change_id AND cp.consign_num>0 )
			ORDER BY c.change_id ASC LIMIT ?";
		$query=$this->db->query($sql,array($shipping_id,$pick_num));
		$result=$query->result();
		if(!$result) return 0;
		$change_ids=array_keys(index_array($result,'change_id'));
		$sql="UPDATE ".$this->db->dbprefix('order_change_info')." AS c
			LEFT JOIN ".$this->db->dbprefix('order_info')." AS o ON c.order_id=o.order_id
			SET c.pick_sn=?
			WHERE c.pick_sn='' AND c.odd=0 AND o.pay_status=1 AND c.change_status=1 AND c.shipped_status=1 AND c.shipping_status=0
			AND c.lock_admin IN (-1,0) AND c.shipping_id=?
			AND	NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('order_change_product')." AS cp WHERE cp.change_id=c.change_id AND cp.consign_num>0 )
			AND c.change_id ".  db_create_in($change_ids);
		$query=$this->db->query($sql,array($pick_sn,$shipping_id));
		return $this->db->affected_rows();		
	}
	
	public function depot_info_by_sns ($sns=array())
	{
		$sql="SELECT t.trans_sn,p.product_sn,p.provider_productcode,p.product_name,t.product_number,
			c.color_sn,c.color_name,s.size_sn,s.size_name,
			d.depot_name,l.location_name
			FROM ".$this->db->dbprefix('transaction_info')." AS t
			LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON t.product_id=p.product_id
			LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON t.color_id=c.color_id
			LEFT JOIN ".$this->db->dbprefix('product_size')." AS s ON t.size_id=s.size_id
			LEFT JOIN ".$this->db->dbprefix('depot_info')." AS d ON t.depot_id=d.depot_id
			LEFT JOIN ".$this->db->dbprefix('location_info')." AS l ON t.location_id=l.location_id
			WHERE t.trans_sn ".db_create_in($sns)." 
			AND t.trans_type IN (".TRANS_TYPE_SALE_ORDER.",".TRANS_TYPE_CHANGE_ORDER.") 
			AND t.trans_status IN (".TRANS_STAT_AWAIT_OUT.",".TRANS_STAT_OUT.") AND t.trans_direction=0
			ORDER BY t.product_id";
		$query=$this->db->query($sql);
		return $query->result();
	}
        
        /**
         * 累加计数1
         * @param type $pick_sn 
         */
        public function step($pick_sn)
        {
            $sql="UPDATE ".$this->db->dbprefix('pick_info')." SET over_num=over_num+1 WHERE pick_sn=?";
            $this->db->query($sql,array($pick_sn));
        }
		
		//以下两个方法打印时出列表用到
		public function picked_order_info ($pick_sn)
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
					WHERE o.pick_sn = ?";
			$query=$this->db->query($sql,array($pick_sn));
			return $query->result();
		}
		
		public function picked_change_info ($pick_sn)
		{
			$sql = "SELECT c.*,s.shipping_code,s.shipping_name,rp.region_name as province_name,rc.region_name as city_name,rd.region_name as district_name
				FROM ".$this->db->dbprefix('order_change_info')." AS c 
				LEFT JOIN ".$this->db->dbprefix('shipping_info')." AS s ON c.shipping_id = s.shipping_id
				LEFT JOIN ". $this->db->dbprefix('region_info') . " AS rp ON c.province = rp.region_id 
				LEFT JOIN ". $this->db->dbprefix('region_info') . " AS rc ON c.city = rc.region_id 
				LEFT JOIN ". $this->db->dbprefix('region_info') . " AS rd ON c.district = rd.region_id						
				WHERE c.pick_sn= ?";
				$query=$this->db->query($sql,array($pick_sn));
				return $query->result();
		}
	
	public function print_sale_data ($type,$ids)
	{
		$sns=array();
		if ( $type=='order' )
		{
			//主表
			$sql = "SELECT o.*, u.user_name,u.email,u.mobile,pr.region_name as province_name,cr.region_name as city_name,dr.region_name as district_name
				FROM ".$this->db->dbprefix('order_info')." AS o
				LEFT JOIN ".$this->db->dbprefix('region_info')." AS pr ON o.province = pr.region_id
				LEFT JOIN ".$this->db->dbprefix('region_info')." AS cr ON o.city = cr.region_id
				LEFT JOIN ".$this->db->dbprefix('region_info')." AS dr ON o.district = dr.region_id
				LEFT JOIN ".$this->db->dbprefix('user_info')." AS u ON o.user_id = u.user_id
				WHERE o.order_id ".db_create_in($ids);
			$query=$this->db->query($sql);
			$orders=index_array($query->result(),'order_id');
			foreach($orders as $key=>$order){				
				$order=format_order($order);
				$order->type='order';
				$order->products=array();
				$order->discount=0.00;
				$order->total_num=0;
				$sns[]=$order->order_sn;
				$orders[$key]=$order;
			}
			unset($order);
			//商品
			$sql = "SELECT op.*, p.product_name, p.product_sn,p.provider_productcode, p.unit_name, c.color_name,  s.size_name
				FROM ".$this->db->dbprefix('order_product')." AS op
				LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON op.product_id = p.product_id
				LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON c.color_id = op.color_id
				LEFT JOIN ".$this->db->dbprefix('product_size')." AS s ON s.size_id = op.size_id
				WHERE op.order_id ".db_create_in($ids);
			$query = $this->db->query($sql);
			$products=index_array($query->result(),'op_id');
			foreach( $products as $key => $product )
			{
				$product->locations=array();
				$products[$key] = $product;
			}
			unset($product);
			//储位
			$sql = "SELECT t.*,l.location_name
				FROM ".$this->db->dbprefix('transaction_info')." AS t
				LEFT JOIN ".$this->db->dbprefix('location_info')." AS l ON t.location_id = l.location_id
				WHERE t.trans_type= ".TRANS_TYPE_SALE_ORDER." AND t.trans_status IN (".TRANS_STAT_AWAIT_OUT.",".TRANS_STAT_OUT.")  
				AND t.trans_sn ".db_create_in($sns);
			$query = $this->db->query($sql);
			$trans=$query->result();
			foreach( $trans as $t )
			{
				if(!isset($products[$t->sub_id])) continue;
				$products[$t->sub_id]->locations[]=$t->location_name.' x '.abs($t->product_number);
			}
			unset($t);
			foreach( $products as $p)
			{
				if(!isset($orders[$p->order_id])) continue;
				$orders[$p->order_id]->total_num+=$p->product_num;
				$orders[$p->order_id]->products[]=$p;
			}
			unset($p);
			//支付
			$sql = "SELECT op.*, p.pay_name, p.pay_code, a.admin_name, p.is_discount
					FROM ".$this->db->dbprefix('order_payment')." AS op
					LEFT JOIN ".$this->db->dbprefix('payment_info')." AS p ON op.pay_id = p.pay_id
					LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON op.payment_admin = a.admin_id
					WHERE op.is_return = 0 AND p.is_discount=1 AND op.order_id ".db_create_in($ids);
			$query = $this->db->query($sql);
			$payments=$query->result();
			foreach( $payments as $p)
			{
				if(!isset($orders[$p->order_id])) continue;
				$orders[$p->order_id]->discount+=$p->payment_money;
			}
			unset($p);
			return $orders;
		}else
		{
			//主表
			$sql = "SELECT c.*,u.user_name,u.email,u.mobile,rp.region_name as province_name,rc.region_name as city_name,rd.region_name as district_name
				FROM ".$this->db->dbprefix('order_change_info')." AS c 
				LEFT JOIN ". $this->db->dbprefix('region_info') . " AS rp ON c.province = rp.region_id 
				LEFT JOIN ". $this->db->dbprefix('region_info') . " AS rc ON c.city = rc.region_id 
				LEFT JOIN ". $this->db->dbprefix('region_info') . " AS rd ON c.district = rd.region_id						
				LEFT JOIN ". $this->db->dbprefix('user_info') . " AS u ON c.user_id = u.user_id						
				WHERE c.change_id ".db_create_in($ids);
			$query=$this->db->query($sql);
			$changes=index_array($query->result(),'change_id');
			foreach( $changes as $key => $change )
			{
				$change->type='change';
				$change->products=array();
				$change->total_num=0;
				$sns[]=$change->change_sn;
				$changes[$key]=$change;
			}
			unset($change);
			//商品
			$sql = "SELECT cp.*,p.unit_name,
				c.color_name,s.size_name,sc.color_name as src_color_name,ss.size_name as src_size_name,p.product_name,p.product_sn, p.provider_productcode ".
				" FROM " . $this->db->dbprefix('order_change_product') . " AS cp " .
				" LEFT JOIN ". $this->db->dbprefix('product_info') . " AS p ON cp.product_id = p.product_id " .
				" LEFT JOIN " . $this->db->dbprefix('product_color') . " AS c ON c.color_id = cp.color_id " .
				" LEFT JOIN " . $this->db->dbprefix('product_size') . " AS s ON s.size_id = cp.size_id " .
				" LEFT JOIN " . $this->db->dbprefix('product_color') . " AS sc ON sc.color_id = cp.src_color_id " .
				" LEFT JOIN " . $this->db->dbprefix('product_size') . " AS ss ON ss.size_id = cp.src_size_id " .
				" WHERE cp.change_id ".db_create_in($ids);
			$query = $this->db->query($sql);
			$products=index_array($query->result(),'cp_id');
			foreach( $products as $key => $product )
			{
				$product->locations=array();
				$products[$key]=$product;
			}
			unset($product);
			//储位
			$sql = "SELECT t.*,l.location_name
				FROM ".$this->db->dbprefix('transaction_info')." AS t
				LEFT JOIN ".$this->db->dbprefix('location_info')." AS l ON t.location_id = l.location_id
				WHERE t.trans_type= ".TRANS_TYPE_CHANGE_ORDER." AND t.trans_status IN (".TRANS_STAT_AWAIT_OUT.",".TRANS_STAT_OUT.")  
				AND t.trans_sn ".db_create_in($sns);
			$query = $this->db->query($sql);
			$trans=$query->result();
			foreach( $trans as $t )
			{
				if(!isset($products[$t->sub_id])) continue;
				$products[$t->sub_id]->locations[]=$t->location_name.' x '.abs($t->product_number);
			}
			unset($t);
			foreach( $products as $p)
			{
				if(!isset($changes[$p->change_id])) continue;
				$changes[$p->change_id]->products[]=$p;
				$changes[$p->change_id]->total_num+=$p->change_num;
			}
			unset($p);
			return $changes;
		}
		
    }
    // 拣货明细
    function pick_details($pick_sn)
    {
        $result = array();
        $sql = "SELECT pi.product_name, pc.color_name, ds.size_name, br.brand_name,pi.provider_productcode,
               concat(pi.product_sn, ' ', pc.color_sn, ' ', ds.size_sn) AS sku, li.location_name, 
               ps.product_number, ps.pick_num,  ps.pick_cell, dk.provider_barcode, ps.sub_id, ps.rel_no,
	       li.location_id
               FROM ty_pick_sub ps INNER JOIN ty_product_info pi ON ps.product_id = pi.product_id 
               INNER JOIN ty_product_color pc ON ps.color_id = pc.color_id 
               INNER JOIN ty_product_size ds ON ps.size_id = ds.size_id 
               INNER JOIN ty_location_info li ON ps.location_id = li.location_id 
               INNER JOIN ty_product_sub dk ON ps.product_id = dk.product_id AND ps.color_id = dk.color_id AND ps.size_id = dk.size_id 
	       INNER JOIN ty_product_brand br ON br.brand_id = pi.brand_id
               WHERE ps.pick_sn = '".$pick_sn."' AND ps.product_number != ps.pick_num ORDER BY ps.depot_id, ps.location_id;";
        $query = $this->db->query($sql);
        $result = $query->result();
        return $result; 
    }
    // 拣货单商品明细
    function scan_pick_sub($args)
    {
        if (empty($args['pick_sn']) || intval($args['sub_id']) == 0 || intval($args['scan_num']) == 0)
        {
            return;
        }

        $sql = "UPDATE ty_pick_sub SET pick_num = pick_num + ".$args['scan_num'].", 
                pick_admin = '".$args['admin_id']."', pick_date = NOW() 
                WHERE pick_sn = '".$args['pick_sn']."' AND sub_id = ".$args['sub_id']."";
        $this->db->query($sql);
    }
    // 拣货单完成
    function scan_pick_finish($args)
    {
        if (empty($args['pick_sn']))
        {
            return;
        }
        // 更新订单拣货状态
        $sql = "UPDATE ty_order_info oi, (SELECT rel_no, sum(product_number) as dn, sum(pick_num) as pn 
                FROM ty_pick_sub WHERE pick_sn = '".$args['pick_sn']."' GROUP BY rel_no HAVING dn = pn) pk 
                SET oi.is_pick = 1, oi.pick_admin = '".$args['admin_id']."', oi.pick_date = NOW() 
                WHERE oi.order_sn = pk.rel_no";
        $this->db->query($sql);

        // 拣货单已完成
        /*$sql = "UPDATE ty_pick_info SET over_num = over_num + ".$args['finish_num'].", 
                pick_status = IF(total_num=over_num+".$args['finish_num'].", 2, 0), pick_date = NOW(), 
                pick_admin = ".$args['admin_id']." WHERE pick_sn = '".$args['pick_sn']."' ";
         */
        $sql = "UPDATE ty_pick_info pi, (SELECT pick_sn, count(1) AS cnt 
                FROM ty_order_info WHERE pick_sn = '".$args['pick_sn']."' AND is_pick = 1) oi 
                SET pick_status = 1, pick_date = NOW(), pick_admin = ".$args['admin_id']." 
                WHERE pi.pick_sn = oi.pick_sn AND pi.total_num != oi.cnt AND oi.cnt > 0";
        $this->db->query($sql);

        $sql = "UPDATE ty_pick_info pi, (SELECT pick_sn, count(1) AS cnt 
                FROM ty_order_info WHERE pick_sn = '".$args['pick_sn']."' AND is_pick = 1) oi 
                SET pick_status = 2, pick_date = NOW(), pick_admin = ".$args['admin_id']." 
                WHERE pi.pick_sn = oi.pick_sn AND pi.total_num = oi.cnt";
        $this->db->query($sql);
    
    }
    
    function get_print_pick_info($pick_sn) {
    	if (empty($pick_sn)) return false;
    	$result = array();
        $sql = "SELECT pks.pick_sn,CONCAT(tpi.product_sn,' ',pc.color_sn,' ',pz.size_sn) AS sku,
        		ps.provider_barcode,pb.brand_name,tpi.product_name,pc.color_name,pz.size_name,
        		li.location_name,pks.product_number,pks.pick_cell 
				FROM ".$this->db_r->dbprefix('pick_sub')." AS pks 
				LEFT JOIN ".$this->db_r->dbprefix('product_sub')." AS ps ON pks.product_id = ps.product_id AND pks.color_id = ps.color_id AND pks.size_id = ps.size_id 
				LEFT JOIN ".$this->db_r->dbprefix('product_info')." AS tpi ON pks.product_id = tpi.product_id 
				LEFT JOIN ".$this->db_r->dbprefix('product_brand')." AS pb ON tpi.brand_id = pb.brand_id 
				LEFT JOIN ".$this->db_r->dbprefix('product_color')." AS pc ON pks.color_id = pc.color_id 
				LEFT JOIN ".$this->db_r->dbprefix('product_size')." AS pz ON pks.size_id = pz.size_id 
				LEFT JOIN ".$this->db_r->dbprefix('location_info')." AS li ON pks.location_id = li.location_id 
				WHERE pks.pick_sn = '".$pick_sn."' ORDER BY li.location_name ";
        $query = $this->db_r->query($sql);
        $result = $query->result();
        return $result; 
    }
    // 发运列表
    function scan_shipping_list($filter)
    {
        $where = " WHERE oi.order_status = 1 AND oi.shipping_true = 1";
        if (!empty($filter['order_sn'])) $where .= " AND oi.order_sn = '".$filter['order_sn']."'"; 
        if (!empty($filter['invoice_sn'])) $where .= " AND oi.invoice_no = '".$filter['invoice_sn']."'"; 
        if ($filter['shipping_id'] != -1) $where .= " AND oi.shipping_id = '".$filter['shipping_id']."'"; 
        //BABY-583 修改 shangguannan
        if ($filter['shipping_status'] != -1){
            if($filter['shipping_status'] == 0) {
                $where .= " AND oi.pick_sn = '' and oi.is_pick = 0"; 
            }else{
                $is_pick = 0;
                $is_qc = 0;
                $is_shipping = 0;
                if($filter['shipping_status'] == 2) {
                    $is_pick = 1;
                    $is_qc = 0;
                    $is_shipping = 0;
                }
                if($filter['shipping_status'] == 3) {
                    $is_pick = 1;
                    $is_qc = 1;
                    $is_shipping = 0;
                }
                if($filter['shipping_status'] == 4) {
                    $is_pick = 1;
                    $is_qc = 1;
                    $is_shipping = 1;
                }
                //配送状态
                $where .= " AND oi.is_pick = ".$is_pick;
                $where .= " AND oi.is_qc = ".$is_qc;
                $where .= " AND oi.shipping_status = ".$is_shipping;
            }
        }
        //订单创建时间
        if (!empty($filter['create_start_date'])) $where .= " AND oi.create_date >= '".$filter['create_start_date']."'"; 
        if (!empty($filter['create_end_date'])) $where .= " AND oi.create_date <= '".$filter['create_end_date']."'"; 
        //BABY-583 end
        
        if (!empty($filter['start_date'])) $where .= " AND oi.shipping_date >= '".$filter['start_date']."'"; 
        if (!empty($filter['end_date'])) $where .= " AND oi.shipping_date <= '".$filter['end_date']."'"; 
        
		$filter['sort_by'] = empty($filter['sort_by']) ? 'oi.shipping_date' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);
        
        $from = "FROM ".$this->db_r->dbprefix('order_info')." oi INNER JOIN ".$this->db_r->dbprefix('shipping_info')." si ON oi.shipping_id = si.shipping_id 
               INNER JOIN ty_payment_info pi ON oi.pay_id = pi.pay_id LEFT JOIN ty_region_info p ON oi.province = p.region_id     
               LEFT JOIN ty_region_info c ON oi.city = c.region_id LEFT JOIN ty_region_info a ON oi.district = a.region_id"; 

        $sql = "SELECT COUNT(1) AS cnt ".$from.$where;
        
        $query = $this->db_r->query($sql);
        $row = $query->row();
        $query->free_result();
        $filter['record_count'] = intval($row->cnt);
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0)
        {
            return array('list' => array(), 'filter' => $filter);
        }

        $sql = "SELECT oi.order_id,oi.order_sn, oi.invoice_no, oi.shipping_date, si.shipping_name, oi.shipping_status, oi.create_date
               , p.region_name as province, c.region_name as city, a.region_name as district, 
               oi.address, oi.consignee, pi.pay_name, oi.order_price+oi.shipping_fee as order_amount, 
               oi.order_price+oi.shipping_fee-oi.paid_price as paid_money, oi.order_weight_unreal, round(oi.recheck_weight_unreal/1000, 2) as recheck_weight_unreal ".$from.$where." 
               ORDER BY ".$filter['sort_by']." ".$filter['sort_order']." 
               LIMIT ".($filter['page']-1)*$filter['page_size'].", ".$filter['page_size'] ;
        $query = $this->db_r->query($sql);
        $list = $query->result();
        $query->free_result();
        return array('list' => $list, 'filter' => $filter);
    }
    //导出发货订单 
    function scan_shipping_export($filter)
    {
        $where = " WHERE oi.order_status = 1 AND oi.shipping_true = 1";
        if (!empty($filter['order_sn'])) $where .= " AND oi.order_sn = '".$filter['order_sn']."'"; 
        if (!empty($filter['invoice_sn'])) $where .= " AND oi.invoice_no = '".$filter['invoice_sn']."'"; 
        if ($filter['shipping_id'] != -1) $where .= " AND oi.shipping_id = '".$filter['shipping_id']."'"; 
        /*
        if ($filter['shipping_status'] != -1) $where .= " AND oi.shipping_status = '".$filter['shipping_status']."'"; 
        if ($filter['shipping_status'] == 0){
        	$where .= " AND oi.is_qc = 1";
        	$filter['sort_by'] = 'oi.order_id';
        	$filter['sort_order'] = 'ASC';
        }
         */
        
        //BABY-583 修改 shangguannan
        if ($filter['shipping_status'] != -1){
            if($filter['shipping_status'] == 0) {
                $where .= " AND oi.pick_sn = '' and oi.is_pick = 0"; 
            }else{
                $is_pick = 0;
                $is_qc = 0;
                $is_shipping = 0;
                if($filter['shipping_status'] == 2) {
                    $is_pick = 1;
                    $is_qc = 0;
                    $is_shipping = 0;
                }
                if($filter['shipping_status'] == 3) {
                    $is_pick = 1;
                    $is_qc = 1;
                    $is_shipping = 0;
                }
                if($filter['shipping_status'] == 4) {
                    $is_pick = 1;
                    $is_qc = 1;
                    $is_shipping = 1;
                }
                //配送状态
                $where .= " AND oi.is_pick = ".$is_pick;
                $where .= " AND oi.is_qc = ".$is_qc;
                $where .= " AND oi.shipping_status = ".$is_shipping;
            }
        }
        //BABY-583 end
        if (!empty($filter['start_date'])) $where .= " AND oi.shipping_date >= '".$filter['start_date']."'"; 
        if (!empty($filter['end_date'])) $where .= " AND oi.shipping_date <= '".$filter['end_date']."'"; 
        
		$filter['sort_by'] = empty($filter['sort_by']) ? 'oi.shipping_date' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);
        
        $from = "FROM ".$this->db_r->dbprefix('order_info')." oi INNER JOIN ".$this->db_r->dbprefix('shipping_info')." si ON oi.shipping_id = si.shipping_id 
               INNER JOIN ty_payment_info pi ON oi.pay_id = pi.pay_id LEFT JOIN ty_region_info p ON oi.province = p.region_id     
               LEFT JOIN ty_region_info c ON oi.city = c.region_id LEFT JOIN ty_region_info a ON oi.district = a.region_id"; 
     
        $sql = "SELECT oi.order_sn, oi.invoice_no, oi.shipping_date, si.shipping_name, oi.shipping_status
               , p.region_name as province, c.region_name as city, a.region_name as district, 
               oi.address, oi.consignee, pi.pay_name, oi.order_price+oi.shipping_fee as order_amount, 
               oi.order_price+oi.shipping_fee-oi.paid_price as paid_money,oi.order_weight_unreal ".$from.$where." 
               ORDER BY ".$filter['sort_by']." ".$filter['sort_order'];
        $query = $this->db_r->query($sql);
        $list = $query->result();
        $query->free_result();
        return array('list' => $list, 'filter' => $filter);
    }

    function get_order_info($order_ids) {
    	if (empty($order_ids) || count($order_ids) <= 0) return false;
    	$result = array();
    	$ids = implode(",",$order_ids);
    	$sql = "SELECT oi.`order_id`,si.`shipping_name`,oi.`shipping_date`,oi.`order_sn`,oi.`invoice_no`,oi.`invoice_title`,
				ui.`user_name`,ui.`email`,oi.`consignee`,oi.`tel`,oi.`mobile`,oi.`zipcode`,oi.`order_price`,oi.`product_num`,
				ri1.`region_name` AS country_name,ri2.`region_name` AS province_name,ri3.`region_name` AS city_name,ri4.`region_name` AS district_name,oi.`address`,
				oi.`paid_price`,(oi.`order_price`+oi.`shipping_fee`-oi.`paid_price`) AS unpay_price 
				FROM ".$this->db_r->dbprefix('order_info')." AS oi 
				LEFT JOIN ".$this->db_r->dbprefix('shipping_info')." AS si ON oi.`shipping_id` = si.`shipping_id` 
				LEFT JOIN ".$this->db_r->dbprefix('user_info')." AS ui ON oi.`user_id` = ui.`user_id` 
				LEFT JOIN ".$this->db_r->dbprefix('region_info')." AS ri1 ON oi.`country` = ri1.`region_id` 
				LEFT JOIN ".$this->db_r->dbprefix('region_info')." AS ri2 ON oi.`province` = ri2.`region_id` 
				LEFT JOIN ".$this->db_r->dbprefix('region_info')." AS ri3 ON oi.`city` = ri3.`region_id` 
				LEFT JOIN ".$this->db_r->dbprefix('region_info')." AS ri4 ON oi.`district` = ri4.`region_id` 
				WHERE oi.`order_id` IN (".$ids.") ";
    	$query = $this->db_r->query($sql);
        $order_info = $query->result();
        foreach ($order_info as $order) {
        	$query = $this->db_r->get_where('pick_sub', array('rel_no'=>$order->order_sn), 1);
			$pick = $query->row();
			$order->pick_cell = $pick->pick_cell;
                $sql = "SELECT tpi.`product_sn` AS sku,
                        ps.`provider_barcode`,pb.`brand_name`,tpi.`product_name`,pc.`color_name`,
                        pz.`size_name`,tpi.`unit_name`,op.`product_price`,SUM(pks.`product_number`) as product_num,
                        SUM(op.`product_price` * pks.`product_number`) AS total_price,li.`location_name`,arc.`register_no`,arc.`unit`,bt.`batch_code`,pks.`expire_date`,pks.`production_batch`
                        FROM ".$this->db_r->dbprefix('pick_sub')." AS pks 
                        LEFT JOIN ".$this->db_r->dbprefix('product_info')." AS tpi ON pks.`product_id` = tpi.`product_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('product_brand')." AS pb ON tpi.`brand_id` = pb.`brand_id` 
                        LEFT JOIN ty_register_code AS arc ON arc.`id` = tpi.`register_code_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('product_color')." AS pc ON pks.`color_id` = pc.`color_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('product_size')." AS pz ON pks.`size_id` = pz.`size_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('product_sub')." AS ps ON pks.`product_id` = ps.`product_id` AND pks.`color_id` = ps.`color_id` AND pks.`size_id` = ps.`size_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('order_info')." AS oi ON pks.`rel_no` = oi.`order_sn` 
                        LEFT JOIN ".$this->db_r->dbprefix('order_product')." AS op ON oi.`order_id` = op.`order_id` AND pks.`product_id` = op.`product_id` AND pks.`color_id` = op.`color_id` AND pks.`size_id` = op.`size_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('location_info')." AS li ON pks.`location_id` = li.`location_id` 
                        LEFT JOIN ".$this->db_r->dbprefix('purchase_batch')." AS bt ON pks.`batch_id` = bt.`batch_id` 
                        WHERE pks.`rel_no` = '".$order->order_sn."' GROUP BY pks.product_id, pks.color_id, pks.size_id, pks.depot_id, pks.location_id;";

        	/*
                $sql = "SELECT CONCAT(tpi.`product_sn`,' ',pc.`color_sn`,' ',pz.`size_sn`) AS sku,
					ps.`provider_barcode`,pb.`brand_name`,tpi.`product_name`,pc.`color_name`,
					pz.`size_name`,tpi.`unit_name`,op.`product_price`,op.`product_num`,op.`total_price`,li.`location_name` 
					FROM ".$this->db_r->dbprefix('order_product')." AS op 
					LEFT JOIN ".$this->db_r->dbprefix('product_info')." AS tpi ON op.`product_id` = tpi.`product_id` 
					LEFT JOIN ".$this->db_r->dbprefix('product_brand')." AS pb ON tpi.`brand_id` = pb.`brand_id` 
					LEFT JOIN ".$this->db_r->dbprefix('product_color')." AS pc ON op.`color_id` = pc.`color_id` 
					LEFT JOIN ".$this->db_r->dbprefix('product_size')." AS pz ON op.`size_id` = pz.`size_id` 
					LEFT JOIN ".$this->db_r->dbprefix('product_sub')." AS ps ON op.`product_id` = ps.`product_id` AND op.`color_id` = ps.`color_id` AND op.`size_id` = ps.`size_id` 
                                        LEFT JOIN ".$this->db_r->dbprefix('order_info')." AS oi ON op.`order_id` = oi.`order_id` 
                                        LEFT JOIN ".$this->db_r->dbprefix('pick_sub')." AS pks ON op.`product_id` = pks.`product_id` AND op.`color_id` = pks.`color_id` AND op.`size_id` = pks.`size_id` AND oi.`order_sn` = pks.`rel_no` 
                                        LEFT JOIN ".$this->db_r->dbprefix('location_info')." AS li ON pks.`location_id` = li.`location_id` 
                                        WHERE op.`order_id` = '".$order->order_id."' ";
                 */
        	$query = $this->db_r->query($sql);
        	$product_info = $query->result();
        	$order->product_list = $product_info;
        	$result[$order->order_id] = $order;
        }
        return $result;
    }
    
    function get_orders_by_picksn($filter) {
    	//if (empty($pick_sn)) return false;
    	$result = array();
    	$sql = "SELECT  ps.`pick_sn`,oi.`order_sn`,oi.`order_id` 
		FROM ".$this->db_r->dbprefix('pick_info')." AS p "
                . "LEFT JOIN ".$this->db_r->dbprefix('pick_sub')." AS ps ON p.pick_sn = ps.pick_sn "
                . "LEFT JOIN ".$this->db_r->dbprefix('order_info')." AS oi ON ps.`rel_no` = oi.`order_sn` "
                . "WHERE 1";
        if (!empty($filter['pick_sn'])) $sql .= " AND ps.pick_sn = '".$filter['pick_sn']."'";
        if (isset($filter['pick_id']) && is_array($filter['pick_id'])) $sql .= " AND p.pick_id IN (".implode(",", $filter['pick_id']).")";
        $sql .= " GROUP BY oi.`order_id`";
    	$query = $this->db_r->query($sql);
        $result = $query->result();
        return $result;
    }
    
    public function search_admin($key)
    {
        $key = '%'.$key.'%';
        $sql = "SELECT admin_id, admin_name, realname, admin_email FROM ".$this->db->dbprefix('admin_info')." WHERE admin_name LIKE ? OR realname LIKE ? OR admin_email LIKE ? LIMIT 10";
        $query = $this->db->query($sql, array($key,$key,$key));
        return $query->result();
    }
    
}
