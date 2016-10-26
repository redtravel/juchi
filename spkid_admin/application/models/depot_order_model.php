<?php
#doc
#	classname:	Color_model
#	scope:		PUBLIC
#
#/doc

class Depot_order_model extends CI_Model
{
	public function order_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('order_info')." AS oi ";
		$where = " WHERE 1 ";
		$param = array();
        //下单时间
		if (!empty($filter['create_date_start'])){
			$where .= " AND oi.create_date >= STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') ";
			$param[] = $filter['create_date_start'];
		}
		if (!empty($filter['create_date_end'])){
			$where .= " AND oi.create_date <= STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') ";
			$param[] = $filter['create_date_end'];
		}
        //审核时间
		if (!empty($filter['confirm_date_start'])){
          $where .= " AND oi.confirm_date >= STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') ";
          $param[] = $filter['confirm_date_start'];
        }
        if (!empty($filter['confirm_date_end'])){
          $where .= " AND oi.confirm_date <= STR_TO_DATE(?,'%Y-%m-%d %H:%i:%s') ";
          $param[] = $filter['confirm_date_end'];
        }
        //单号
        if (!empty($filter['order_sn'])){
			$where .= " AND oi.order_sn LIKE ? ";
			$param[] = '%' . $filter['order_sn'] . '%';
		}
        //订单状态
		if (!empty($filter['order_status'])){
			$where .= " AND oi.order_status = ? ";
			if($filter['order_status'] == -1){
               $param[] = 0; 
            }else{
               $param[] = $filter['order_status']; 
            }
		}
        //配送方式
		if (!empty($filter['shipping_id'])){
			$where .= " AND oi.shipping_id = ? ";
			$param[] = $filter['shipping_id'];
		}
        //配送状态
		if (empty($filter['is_all'])){
			$where .= " AND oi.is_pick = ? ";
			$param[] = $filter['is_pick'];
			$where .= " AND oi.is_qc = ? ";
			$param[] = $filter['is_qc'];
			$where .= " AND oi.shipping_status = ? ";
			$param[] = $filter['is_shipping'];
		}
        //排序
		$filter['sort_by'] = empty($filter['sort_by']) ? 'oi.order_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'ASC' : trim($filter['sort_order']);
        //查询总数
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
        
		$sql = "select oi.order_id, oi.create_date,oi.order_sn,
                if(oi.order_status = 0,'未确认',if(oi.order_status = 1,'已确认',if(oi.order_status = 4,'作废','其他'))) order_status,
                oi.confirm_date,oi.shipping_id,
                if(oi.shipping_status=1,'已发货',if(oi.is_qc=1,'已复核',if(oi.is_pick=1,'已拣货','未拣货'))) shipping_status "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
}
###