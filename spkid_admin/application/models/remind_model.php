<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class remind_model extends CI_Model
{
    public function query_advice_list(){
        $sql="select concat(ai.admin_name,'发布',b.type_name,':',a.advice_content) context
                from ".$this->db->dbprefix('order_advice')." a
                left join ".$this->db->dbprefix('order_advice_type')." b on a.type_id = b.type_id 
                left join ".$this->db->dbprefix('admin_info')." ai on a.advice_admin = ai.admin_id
                order by a.advice_id desc limit 10;";
        $query = $this->db->query($sql);
        return $query->result();
    }
    public function query_order_over24confirm(){
        $sql="select count(*) ct from ".$this->db->dbprefix('order_info')." 
            where order_status = 0 
            and UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(create_date)  > 24*3600";
        $query = $this->db->query($sql);
		$row = $query->row();
		$query->free_result();
		return (int) $row->ct;
    }
    public function query_order_over24shipping(){
        //货到付款
        $sql="select count(*) ct from ".$this->db->dbprefix('order_info')." 
            where shipping_status = 0
            and order_status = 1 
            and pay_id = 1 
            and UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(confirm_date)  > 24*3600";
        $query = $this->db->query($sql);
		$row = $query->row();
		$query->free_result();
		return (int) $row->ct;
    }
    public function query_order_over24shipping2(){
        //款到发货 
        $sql="select count(*) ct from ".$this->db->dbprefix('order_info')." 
            where shipping_status = 0 
            and order_status = 1 
            and pay_id != 1 
            and UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(finance_date)  > 24*3600 ";
        $query = $this->db->query($sql);
		$row = $query->row();
		$query->free_result();
		return (int) $row->ct;
    }
    
    public function query_order_odd(){
        //问题单
        $sql="select count(*) ct from ".$this->db->dbprefix('order_info')." 
            where odd = 1 
            and order_status != 4 
            and order_status != 5 
            and is_ok != 1";
        $query = $this->db->query($sql);
		$row = $query->row();
		$query->free_result();
		return (int) $row->ct;
    }
    
    public function query_order_abs(){
        //问题单
        $sql="select count(*) ct from(
            select oi.order_id,sum(op.consign_num) consign_num from ".$this->db->dbprefix('order_info')."  oi
            left join ".$this->db->dbprefix('order_product')." op on oi.order_id = op.order_id
            where op.consign_num > 0
            and UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(oi.create_date)  > 4*24*3600 
            group by order_id 
            ) t where consign_num > 0";
        $query = $this->db->query($sql);
		$row = $query->row();
		$query->free_result();
		return (int) $row->ct;
    }
    
    
    
    public function remind_list ($user_id,$type_id)
	{
        $type_arr = array('1'=>'order','2'=>'return','3'=>'change','4'=>'purchase','5'=>'depotin','6'=>'depotout','7'=>'exchange');
        if (empty($type_id))	$type_id = 0;
		$list_str = '';
        for ($i=1;$i<=count($type_arr);$i++)
        {
        	$j = $i + $type_id;
        	$j = ($j > 7)?$j-7:$j;
			if ($type_arr[$j] == 'order')
			{
				$sql = "SELECT order_sn FROM ".$this->db->dbprefix('order_info')." WHERE lock_admin = '".$user_id."' LIMIT 8";
				$query = $this->db->query($sql);
				$list = $query->result();
				$query->free_result();
				if (!empty($list))
				{
					foreach ($list as $item)
					{
						$list_str .= $item->order_sn."&nbsp;&nbsp;";
					}
				}
				if (!empty($list_str))
				{
					$list_str = '<font class="f_gh">订单：</font>'.$list_str.'<a href="/order">更多>></a>';
				}
			} elseif ($type_arr[$j] == 'return')
			{
				$sql = "SELECT return_sn FROM ".$this->db->dbprefix('order_return_info')." WHERE lock_admin = '".$user_id."' LIMIT 8";
				$query = $this->db->query($sql);
				$list = $query->result();
				$query->free_result();
				if (!empty($list))
				{
					foreach ($list as $item)
					{
						$list_str .= $item->return_sn."&nbsp;&nbsp;";
					}
				}
				if (!empty($list_str))
				{
					$list_str = '<font class="f_gh">退货单：</font>'.$list_str.'<a href="/order">更多>></a>';
				}
			} elseif ($type_arr[$j] == 'change')
			{
				$sql = "SELECT change_sn FROM ".$this->db->dbprefix('order_change_info')." WHERE lock_admin = '".$user_id."' LIMIT 8";
				$query = $this->db->query($sql);
				$list = $query->result();
				$query->free_result();
				if (!empty($list))
				{
					foreach ($list as $item)
					{
						$list_str .= $item->change_sn."&nbsp;&nbsp;";
					}
				}
				if (!empty($list_str))
				{
					$list_str = '<font class="f_gh">换货单：</font>'.$list_str.'<a href="/order">更多>></a>';
				}
			} elseif ($type_arr[$j] == 'purchase')
			{
				$sql = "SELECT purchase_code FROM ".$this->db->dbprefix('purchase_main')." WHERE lock_admin = '".$user_id."' LIMIT 8";
				$query = $this->db->query($sql);
				$list = $query->result();
				$query->free_result();
				if (!empty($list))
				{
					foreach ($list as $item)
					{
						$list_str .= $item->purchase_code."&nbsp;&nbsp;";
					}
				}
				if (!empty($list_str))
				{
					$list_str = '<font class="f_gh">采购单：</font>'.$list_str.'<a href="/purchase">更多>></a>';
				}
			} elseif ($type_arr[$j] == 'depotin')
			{
				$sql = "SELECT depot_in_code FROM ".$this->db->dbprefix('depot_in_main')." WHERE lock_admin = '".$user_id."' LIMIT 8";
				$query = $this->db->query($sql);
				$list = $query->result();
				$query->free_result();
				if (!empty($list))
				{
					foreach ($list as $item)
					{
						$list_str .= $item->depot_in_code."&nbsp;&nbsp;";
					}
				}
				if (!empty($list_str))
				{
					$list_str = '<font class="f_gh">入库单：</font>'.$list_str.'<a href="/depotio/in">更多>></a>';
				}
			} elseif ($type_arr[$j] == 'depotout')
			{
				$sql = "SELECT depot_out_code FROM ".$this->db->dbprefix('depot_out_main')." WHERE lock_admin = '".$user_id."' LIMIT 8";
				$query = $this->db->query($sql);
				$list = $query->result();
				$query->free_result();
				if (!empty($list))
				{
					foreach ($list as $item)
					{
						$list_str .= $item->depot_out_code."&nbsp;&nbsp;";
					}
				}
				if (!empty($list_str))
				{
					$list_str = '<font class="f_gh">出库单：</font>'.$list_str.'<a href="/depotio/out">更多>></a>';
				}
			} elseif ($type_arr[$j] == 'exchange')
			{
				$sql = "SELECT exchange_code FROM ".$this->db->dbprefix('exchange_main')." WHERE lock_admin = '".$user_id."' LIMIT 8";
				$query = $this->db->query($sql);
				$list = $query->result();
				$query->free_result();
				if (!empty($list))
				{
					foreach ($list as $item)
					{
						$list_str .= $item->exchange_code."&nbsp;&nbsp;";
					}
				}
				if (!empty($list_str))
				{
					$list_str = '<font class="f_gh">调仓单：</font>'.$list_str.'<a href="/exchange/exchange_list">更多>></a>';
				}
			}
			if (empty($list_str))
			{
				$i++;
				continue;
			} else
			{
				return array('index'=>$j,'str'=>$list_str);
			}
        }
        return array('index'=>1,'str'=>$list_str);
    }
}

?>
