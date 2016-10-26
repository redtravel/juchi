<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class data_alert_model extends CI_Model
{
    public function filter ($filter)
	{
		$query = $this->db->get_where('ty_system_alert_log', $filter, 1);
		return $query->row();
	}

	public function insert ($data)
	{
		$this->db->insert('ty_system_alert_log', $data);
		return $this->db->insert_id();
	}


    public function alert_index ($filter)
	{
        $param = array();
		$from = " FROM ty_system_alert_log a LEFT JOIN ty_admin_info b ON a.admin_id = b.admin_id";
		$where = " WHERE 1 ";
                if(!empty($filter['start_date'])){
                    $where .= " AND TO_DAYS(a.date_insert) > TO_DAYS(?) ";
                    $param[] = $filter['start_date'];
                }
                if(!empty($filter['end_date'])){
                    $where .= " AND TO_DAYS(a.date_insert) <= TO_DAYS(?) ";
                    $param[] = $filter['end_date'];
                }
                if($filter['status'] !=  -1){
                    $where .= " AND a.status = ? ";
                    $param[] = $filter['status'];
                }

		$filter['sort_by'] = empty($filter['sort_by']) ? 'sys_log_id' : trim($filter['sort_by']);
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
		$sql = "SELECT a.*,b.admin_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function error_foramt($list,$break)
	{
		$return = '';
		if (!empty($list) && isset($list[0]) && is_array($list[0]))
		{
			$return .= "<table width='95%'><tr>";
			foreach ($list[0] as $key=>$fields)
			{
				$return .= "<td>".$key."</td>";
			}
			$return .= "</tr>";
			reset($list);
			foreach ($list as $items)
			{
				$return .= "<tr>";
				foreach ($items as $subitem)
				{
					$return .= "<td>".$subitem."</td>";
				}
				$return .= "</tr>";

			}
			$return .= "</table>".$break;
		}
		return $return;
	}

	public function alert_check($admin_id)
	{
		$error = 0;
		$break = "<p>&nbsp;</p>";
		$echo_whole = "";
		$echo_whole .= "开始数据验证!".$break;

		$echo_whole .= "开始验证库存模块".$break;

		$now_check = "验证事务表==可售库存表";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT gl.product_id,gl.color_id,gl.size_id,gl.gl_num,t.product_number FROM ty_product_sub AS gl
		LEFT JOIN
		(
		SELECT SUM(product_number) AS product_number, product_id,color_id,size_id
			FROM ty_transaction_info AS tt
			LEFT JOIN ty_depot_info AS td ON tt.depot_id = td.depot_id
			WHERE  tt.trans_status IN (1,2,4) AND td.depot_type =1
			GROUP BY  tt.product_id,tt.color_id,tt.size_id
		) AS t ON gl.product_id = t.product_id AND gl.color_id = t.color_id AND gl.size_id = t.size_id
		WHERE gl.gl_num!=t.product_number ";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);

		}

		$sql = "SELECT t.product_id,t.color_id,t.size_id,gl.gl_num,t.product_number FROM
		(
		SELECT SUM(product_number) AS product_number, product_id,color_id,size_id
			FROM ty_transaction_info AS tt
			LEFT JOIN ty_depot_info AS td ON tt.depot_id = td.depot_id
			WHERE  tt.trans_status IN (1,2,4) AND td.depot_type =1
			GROUP BY  tt.product_id,tt.color_id,tt.size_id
		) AS t
		LEFT JOIN
		ty_product_sub AS gl ON gl.product_id = t.product_id AND gl.color_id = t.color_id AND gl.size_id = t.size_id
		WHERE gl.gl_num!=t.product_number OR gl.product_id IS NULL";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);

		} else{
			$echo_whole .= "数据正确".$break;
		}

		$now_check = "验证是否出现了负库存";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT SUM(product_number) AS product_number, product_id,color_id,size_id
				FROM ty_transaction_info
				WHERE  trans_status IN (1,2,4)
				GROUP BY  product_id,color_id,size_id,depot_id,location_id HAVING product_number<0";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证wait_num";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT t.*
				FROM ty_transaction_info AS t
				LEFT JOIN ty_order_return_info AS r ON t.trans_sn = r.return_sn
				WHERE t.trans_type = 4
				AND t.trans_status IN(3,4)
				AND r.pay_status=1 AND t.finance_check_date=0";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}



		$echo_whole .= "结束验证库存模块".$break;

		$echo_whole .= "开始验证订单数据模块".$break;

		$now_check = "验证每笔transaction记录是否有对应的订单记录";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT o.order_sn,t.* FROM ty_transaction_info AS t
				LEFT JOIN ty_order_product AS og ON og.op_id = t.sub_id AND og.product_id = t.product_id AND og.size_id = t.size_id AND og.color_id = t.color_id
				LEFT JOIN ty_order_info AS o ON t.trans_sn = o.order_sn
				WHERE trans_type=3 AND og.op_id IS NULL AND o.order_id IS NOT NULL AND t.trans_status !=5";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证每笔订单商品记录是否都有对应的transaction记录";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT * FROM ty_order_product AS og
				LEFT JOIN ty_transaction_info AS t ON og.op_id = t.sub_id AND og.product_id = t.product_id AND og.size_id = t.size_id AND og.color_id = t.color_id AND t.trans_status!=5
				WHERE trans_type=3 AND t.sub_id IS NULL";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证每笔transaction记录是否有对应的退单记录";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT * FROM ty_transaction_info AS t
				LEFT JOIN ty_order_return_product AS og ON og.rp_id = t.sub_id AND og.product_id = t.product_id AND og.size_id = t.size_id AND og.color_id = t.color_id
				LEFT JOIN ty_order_return_info AS r ON r.return_id = og.return_id
				WHERE trans_type=4 AND og.rp_id IS NULL AND r.return_id IS NOT NULL";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证每笔退单商品记录是否都有对应的transaction记录";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT * FROM ty_order_return_product AS og
				LEFT JOIN ty_transaction_info AS t ON og.rp_id = t.sub_id AND og.product_id = t.product_id AND og.size_id = t.size_id AND og.color_id = t.color_id AND t.trans_status!=5
				WHERE trans_type=4 AND t.sub_id IS NULL";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证订单的发货时间与事务的更新时间是否相等，对于拒收的，更新时间应与is_ok_time相等";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT o.shipping_date,o.shipping_admin,t.* FROM ty_transaction_info AS t
				LEFT JOIN ty_order_info AS o ON t.trans_sn = o.order_sn
				WHERE trans_type = 3 AND t.trans_status!=5 AND o.order_sn IS NOT NULL AND (((t.update_date != o.shipping_date OR t.update_admin != o.shipping_admin) AND t.product_number<0) OR((t.update_date != o.is_ok_date OR t.update_admin != o.is_ok_admin) AND t.product_number>0))";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证换货单的发货时间与事务的更新时间是否相等，对于拒收的，更新时间应与is_ok_time相等";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT c.shipping_date,c.shipping_admin,t.* FROM ty_transaction_info AS t
				LEFT JOIN ty_order_change_info AS c ON t.trans_sn = c.change_sn
				WHERE trans_type = 5 AND t.trans_status!=5 AND t.trans_status!=4 AND c.change_sn IS NOT NULL AND (((t.update_date != c.shipped_date OR t.update_admin != c.shipping_admin) AND t.product_number<0) OR((t.update_date != c.is_ok_date OR t.update_admin != c.is_ok_admin) AND t.product_number>0))";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证退单的入库时间与事务的更新时间是否相等";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT o.shipping_date,t.* FROM ty_transaction_info AS t
				LEFT JOIN ty_order_return_info AS o ON t.trans_sn = o.return_sn
				WHERE trans_type = 4 AND t.trans_status!=5 AND o.return_sn IS NOT NULL AND (t.update_date != o.shipping_date OR t.update_admin != o.shipping_admin)";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证订单的财审时间与事务的财审时间是否相等";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT o.finance_date,o.finance_admin,t.* FROM ty_transaction_info AS t
				LEFT JOIN ty_order_info AS o ON t.trans_sn = o.order_sn
				WHERE trans_type = 3 AND t.trans_status!=5 AND o.order_sn IS NOT NULL AND (t.finance_check_date != o.finance_date OR t.finance_check_admin != o.finance_admin )";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证退单的财审时间与事务的财审时间是否相等";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT o.shipping_date,t.* FROM ty_transaction_info AS t
				LEFT JOIN ty_order_return_info AS o ON t.trans_sn = o.return_sn
				WHERE trans_type = 4 AND t.trans_status!=5 AND o.return_sn IS NOT NULL AND (t.finance_check_date != o.finance_date OR t.finance_check_admin != o.finance_admin)";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证取消而没有取消时间的";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT * FROM ty_transaction_info WHERE trans_status = 5 AND (cancel_date = 0 OR cancel_admin = 0)";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证发货但没有发货时间的";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT * FROM ty_transaction_info WHERE trans_status IN(2,4) AND (update_date = 0 OR update_admin = 0)";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证订单作废但作废的时间与作废人不对应的";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT * FROM ty_transaction_info AS t
				LEFT JOIN ty_order_product AS og ON og.op_id = t.sub_id
				LEFT JOIN ty_order_info AS o ON t.trans_sn = o.order_sn
				WHERE trans_type = 3 AND og.op_id IS NOT NULL AND t.trans_status=5 AND (o.is_ok_date!=t.cancel_date OR o.is_ok_admin!=t.cancel_admin)";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证退单的作废时间与作废人不相对应的";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT * FROM ty_transaction_info AS t
				LEFT JOIN ty_order_return_product AS og ON og.rp_id = t.sub_id
				LEFT JOIN ty_order_return_info AS o ON t.trans_sn = o.return_sn
				WHERE trans_type = 4 AND og.rp_id IS NOT NULL AND t.trans_status=5 AND (o.is_ok_date!=t.cancel_date OR o.is_ok_admin!=t.cancel_admin)";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证事务表的状态与订单的状态是否一致";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT * FROM ty_transaction_info AS t
				LEFT JOIN ty_order_info AS o ON t.trans_sn = o.order_sn
				WHERE t.trans_type=3 AND trans_status!=5 AND (
				(o.shipping_status = 1 AND t.trans_status NOT IN(2,4))
				OR (o.shipping_status = 0 AND t.trans_status NOT IN(1)))";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证事务表的状态与退单的状态是否一致";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT * FROM ty_transaction_info AS t
				LEFT JOIN ty_order_return_info AS o ON t.trans_sn = o.return_sn
				WHERE t.trans_type=4 AND trans_status!=5 AND (
				(o.shipping_status = 1 AND t.trans_status NOT IN(4))
				OR (o.shipping_status = 0 AND t.trans_status NOT IN(3)))";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证换货单的成本代销价是否正确";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT cg.cp_id,cg.change_id,cg.consign_price,cg.cosnign_rate,cg.cost_price,og.consign_price,og.consign_rate,og.cost_price
				 FROM ty_order_change_product AS cg
				 LEFT JOIN ty_order_product AS og ON cg.op_id=og.op_id
				 WHERE cg.cosnign_rate!=og.consign_rate OR cg.cost_price!=og.cost_price OR cg.consign_price!=og.consign_price";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证退货单的成本代销价是否正确";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT rg.rp_id,rg.return_id,rg.consign_price,rg.consign_rate,rg.cost_price,og.consign_price,og.consign_rate,og.cost_price FROM ty_order_return_product AS rg
				LEFT JOIN ty_order_product AS og ON rg.op_id = og.op_id WHERE rg.consign_rate!=og.consign_rate OR rg.consign_price != og.consign_price OR rg.cost_price!=og.cost_price";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证退货单的成本代销价是否正确";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT rg.rp_id,rg.return_id,rg.consign_price,rg.consign_rate,rg.cost_price,og.consign_price,og.consign_rate,og.cost_price FROM ty_order_return_product AS rg
				LEFT JOIN ty_order_product AS og ON rg.op_id = og.op_id WHERE rg.consign_rate!=og.consign_rate OR rg.consign_price != og.consign_price OR rg.cost_price!=og.cost_price";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}


		$echo_whole .= "结束验证订单数据模块".$break;

		$echo_whole .= "开始验证商品信息模块".$break;

		$now_check = "验证色片重复";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT product_id,color_id,COUNT(img_850_850) AS aaa FROM ty_product_gallery WHERE  image_type='tonal'
				GROUP BY product_id,color_id
				HAVING aaa > 1";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证默认图重复";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT product_id,color_id,COUNT(img_850_850) AS aaa FROM ty_product_gallery WHERE  image_type='default'
				GROUP BY product_id,color_id
				HAVING aaa > 1";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$echo_whole .= "结束验证商品信息模块".$break;

		$echo_whole .= "开始验证用户数据模块".$break;

/*
		$now_check = "验证是否有重复放积分";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT COUNT(rec_id) AS ct FROM z_order_integal
					GROUP BY order_id HAVING ct!=1";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$now_check = "验证是否有放过积分但没有标记";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT * FROM z_order_integal AS z
				WHERE EXISTS(SELECT 'x' FROM ty_order_info AS o WHERE o.order_id = z.order_id AND point_sent=0)";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}
*/
		$now_check = "验证完善资料未给积分的";
		$echo_whole .= $now_check.$break;
		$sql = "SELECT * FROM ty_user_info AS u WHERE (real_name!='' AND sex!=0 AND birthday!='0000-00-00') AND NOT EXISTS(SELECT 1 FROM ty_user_account_log AS l WHERE l.user_id = u.user_id AND l.change_code ='point_detail' LIMIT 1) ORDER BY u.user_id DESC LIMIT 100";
		$query = $this->db->query($sql);
		$list = $query->result_array();
		if (!empty($list))
		{
			$echo_whole .= "发生错误".$break;
			$error = 1;
			$echo_whole .= "sql:".$sql.$break;
			$echo_whole .= $this->error_foramt($list,$break);
		}

		$echo_whole .= "结束验证用户数据模块".$break;

		$echo_whole .= "结束数据验证!".$break;
		$in_data = array();
		$in_data['admin_id'] = $admin_id;
		$in_data['status'] = $error;
		$in_data['content'] = $echo_whole;
		$in_data['date_insert'] = date('Y-m-d H:i:s');
		$this->insert($in_data);
		return $in_data;
	}

}




?>
