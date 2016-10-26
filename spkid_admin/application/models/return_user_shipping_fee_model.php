<?php
#doc
#	classname:	Return_user_shipping_fee_model
#	scope:		PUBLIC
#
#/doc

class Return_user_shipping_fee_model extends CI_Model
{
    public function filter($filter)
	{
        $this->db_r->order_by('return_id', 'desc');
		$query = $this->db_r->get_where('order_return_shipping_fee', $filter, 1);
		return $query->row();
	}
    public function get($return_id){
        $fields = "select t.return_id,t.return_sn,t.shipping_name,t.order_id,oi.order_sn,oi.address,ri.return_reason,ri.consignee,t.user_shipping_fee,if(t.finance_admin=0,null,t.finance_admin) finance_admin,a.admin_name,if(t.finance_admin=0,null,t.finance_date) finance_date,ri.create_date,ri.finance_admin return_finance_admin ";
        $from = "from ".$this->db_r->dbprefix('order_return_shipping_fee')." t ";
        $from .= "left join ".$this->db_r->dbprefix('order_info')." oi on oi.order_id = t.order_id ";
        $from .= "left join ".$this->db_r->dbprefix('order_return_info')." ri on t.return_id = ri.return_id ";
        $from .= "left join ".$this->db_r->dbprefix('admin_info')." a on t.finance_admin = admin_id ";
        $where = "where ri.return_id = ".$return_id." and t.return_id = ".$return_id;
        $query = $this->db_r->query($fields.$from.$where);
		return $query->row();
    }
    public function query($filter){
        $fields = "select t.return_id,t.return_sn,t.shipping_name,t.order_id,oi.order_sn,oi.address,ri.return_reason,ri.consignee,t.user_shipping_fee,if(t.finance_admin=0,null,t.finance_admin) finance_admin,a.admin_name,if(t.finance_admin=0,null,t.finance_date) finance_date,ri.create_date,ri.finance_admin return_finance_admin ";
        $from = "from ".$this->db_r->dbprefix('order_return_shipping_fee')." t ";
        $from .= "left join ".$this->db_r->dbprefix('order_info')." oi on oi.order_id = t.order_id ";
        $from .= "left join ".$this->db_r->dbprefix('order_return_info')." ri on t.return_id = ri.return_id ";
        $from .= "left join ".$this->db_r->dbprefix('admin_info')." a on t.finance_admin = admin_id ";
        $where = "where 1=1 ";
        if(!empty($filter['return_sn'])){
            $where = $where." and ri.return_sn like '%".$filter['return_sn']."%'";
        }
        if(!empty($filter['order_sn'])){
            $where = $where." and oi.order_sn like '%".$filter['order_sn']."%'";
        }
        if($filter['finance_status']==1){//已财审
            $where = $where." and t.finance_admin > 0 and ri.finance_admin != 0";
        }
        if($filter['finance_status']==2){//未财审
            $where = $where." and t.finance_admin = 0 and ri.finance_admin != 0";
        }
        if($filter['finance_status']==3){//退货单未财审
            $where = $where." and t.finance_admin = 0 and ri.finance_admin = 0";
        }
        //$where = $where." and ri.return_id = t.return_id";
        //$where = $where." and oi.order_id = t.order_id ";
         //先查总数
		$sql = "SELECT COUNT(*) AS ct " . $from . $where;
		$query = $this->db_r->query($sql);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
        //查询详细内容
        $sql = $fields . $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
        //var_dump($filter);
		$query = $this->db_r->query($sql);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
    }
    public function get_all_shipping(){
        $sql = "select distinct shipping_name from ".$this->db_r->dbprefix('order_return_shipping_fee')." where shipping_name is not null and shipping_name not in('顺丰速递','申通速递','圆通速递','中通快递','EMS')";
		$query = $this->db_r->query($sql);
		return $query->result();
    }
	public function insert ($data)
	{
	    $this->db->insert('order_return_shipping_fee', $data);
	    return $this->db->insert_id();
	}
    
	public function delete ($return_id)
	{
		$this->db->delete('order_return_shipping_fee', array('return_id' => $return_id));
    }
	public function update ($data, $return_id)
	{
		$this->db->update('order_return_shipping_fee', $data, array('return_id' => $return_id));
	}
}
###