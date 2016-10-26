<?php
#doc
#	classname:	Season_model
#	scope:		PUBLIC
#
#/doc

class Invoice_manage_model extends CI_Model
{

	public function invoice_list ($filter)
	{
		$from = " FROM ty_order_info AS o ";
		$where = " WHERE 1 ";
		$param = array();


		if (!empty($filter['start_time']) && !empty($filter['end_time']))
		{
			$where .= " AND o.finance_date BETWEEN '". $filter['start_time'] ."' AND '". $filter['end_time']."'";
		}

		if (!empty($filter['order_sn']))
		{
			$where .= " AND o.order_sn LIKE ? ";
			$param[] = '%' . $filter['order_sn'] . '%';
		}

		if (!empty($filter['invoice_status']))
		{
			$where .= " AND o.invoice_status LIKE ? ";
			$param[] = '%' . $filter['invoice_status'] . '%';
		}

		$filter['order_id'] = empty($filter['order_id']) ? 'o.order_id' : trim($filter['order_id']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'ASC' : trim($filter['sort_order']);
		
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

		$from .= " LEFT JOIN ".$this->db->dbprefix('order_product')." AS op ON o.order_id = op.order_id";
		$from .= " LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON op.product_id = p.product_id";

		$sql = "SELECT o.order_id,o.order_sn,o.paid_price,o.invoice_title,o.invoice_content,o.invoice_status,o.finance_date,op.product_price,op.product_num,op.total_price,p.product_name,p.unit_name,p.product_desc_additional "
				. $from . $where . " ORDER BY " . $filter['order_id'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();

		return array('list' => $list, 'filter' => $filter);
	}


	public function all_invoice ($data)
	{
		$from = " FROM ty_order_info AS o";
		$where = " WHERE 1 ";

		if (!empty($data['start_time']) && !empty($data['end_time']))
		{
			$where .= " AND o.finance_date BETWEEN '". $data['start_time'] ."' AND '". $data['end_time']."'";
		}

		if (!empty($data['order_sn']))
		{
			$where .= " AND o.order_sn LIKE ? ";
			$param[] = '%' . $data['order_sn'] . '%';
		}

		if (!empty($data['invoice_status']))
		{
			$where .= " AND o.invoice_status LIKE ? ";
			$param[] = '%' . $data['invoice_status'] . '%';
		}

		$from .= " LEFT JOIN ".$this->db->dbprefix('order_product')." AS op ON o.order_id = op.order_id";
		$from .= " LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON op.product_id = p.product_id";

		$sql = "SELECT o.order_id,o.order_status,o.shipping_status,o.pay_status,o.order_sn,p.product_name,p.product_desc_additional,op.product_price,op.product_num,p.unit_name,op.total_price,o.paid_price,o.invoice_title,o.invoice_content,o.finance_date,o.invoice_status ". $from . $where;
		$list =  $this->db->query($sql)->result_array();

		return $list;
	}

	/**
	*  添加导入导出记录
	*/
	public function add_invoice_record ($update)
	{
		$sql = " INSERT INTO `ty_order_action` (`order_id`, `is_return`, `order_status`, `shipping_status`, `pay_status`, `action_note`, `create_admin`, `create_date`) VALUES ";
        foreach ($update as $value) {
            $sql .=" ('".$value['order_id']."','".$value['is_return']."','".$value['order_status']."','".$value['shipping_status']."','".$value['pay_status']."','".$value['action_note']."','".$value['create_admin']."','".$value['create_date']."'),";
        }
        $sql = trim($sql,',');
        $query = $this->db->query($sql);
        return true;
	}

}
###