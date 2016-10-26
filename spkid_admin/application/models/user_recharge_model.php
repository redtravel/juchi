<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class user_recharge_model extends CI_Model
{
        public function filter ($filter)
	{
		$query = $this->db->get_where('ty_user_recharge', $filter, 1);
		return $query->row();
	}

	public function update ($data, $recharge_id)
	{
		$this->db->update('ty_user_recharge', $data, array('recharge_id' => $recharge_id));
	}

	public function insert ($data)
	{
		$this->db->insert('ty_user_recharge', $data);
		return $this->db->insert_id();
	}

        public function recharge_list ($filter)
	{
		$from = " FROM ty_user_recharge AS r
                    LEFT JOIN ty_user_info AS u ON r.user_id = u.user_id
                    LEFT JOIN ty_payment_info AS p ON r.pay_id = p.pay_id
                    ";
		$where = " WHERE r.is_del = 0 ";
		$param = array();
                if(!empty($filter['mobile'])){
                    $where .= " AND u.mobile like ? ";
                    $param[] = '%'.$filter['mobile'].'%';
                }
                if(!empty($filter['email'])){
                    $where .= " AND u.email like ? ";
                    $param[] = '%'.$filter['email'].'%';
                }
                if(!empty($filter['is_paid'])){
                    $where .= " AND r.is_paid = ? ";
                    $param[] = $filter['is_paid'] - 1;
                }
                if(!empty($filter['is_audit'])){
                    $where .= " AND r.is_audit = ? ";
                    $param[] = $filter['is_audit'] - 1;
                }

                if(!empty($filter['start_time'])){
                    $where .= " AND r.paid_date >= ? ";
                    $param[] = $filter['start_time'];
                }
                if(!empty($filter['end_time'])){
                    $where .= " AND r.paid_date <= ? ";
                    $param[] = $filter['end_time'];
                }

		$filter['sort_by'] = empty($filter['sort_by']) ? 'r.recharge_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'desc' : trim($filter['sort_order']);

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
		$sql = "SELECT r.*,u.user_name,p.pay_name,u.mobile,u.email,u.discount_percent "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	

}




?>
