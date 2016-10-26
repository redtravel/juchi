<?php
#doc
#	classname:	Product_model
#	scope:		PUBLIC
#
#/doc

class Apply_return_model extends CI_Model
{
	public function apply_return_list($filter) {
            $where = " WHERE 1 ";
            if (!empty($filter['order_sn']))
            {
                    $where .= " AND ord.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%' ";
            }
            if (!empty($filter['user_name']))
            {
                    $where .= " AND ari.sent_user_name LIKE '%" . mysql_like_quote($filter['user_name']) . "%' ";
            }
            if (!empty($filter['apply_id']))
            {
                    $where .= " AND ari.apply_id LIKE '%" . mysql_like_quote($filter['apply_id']) . "%' ";
            }
            if (!empty($filter['start_time']))
            {
                $where .= " AND TO_DAYS(ari.apply_time) >= TO_DAYS('".$filter['start_time']."')";
            }
            if (!empty($filter['end_time']))
            {
                    $where .= " AND TO_DAYS(ari.apply_time) <= TO_DAYS('".$filter['end_time']."')";
            }
            if (isset($filter['order_type']) && $filter['order_type'] != -1)
            {
                $where .= " AND ari.order_type  = ".$filter['order_type'];
            }
            if (isset($filter['provider_status']) && $filter['provider_status'] != -1)
            {
                $where .= " AND ari.provider_status  = ".$filter['provider_status'];
            }
            if (!empty($filter['invoice_no']))
            {
                    $where .= " AND ari.invoice_no LIKE '%" . mysql_like_quote($filter['invoice_no']) . "%' ";
            }
            if (isset($filter['apply_status']) && $filter['apply_status'] != -1)
            {
                $where .= " AND ari.apply_status  = ".$filter['apply_status'];
            }

            

            $filter['sort_by'] = empty($filter['sort_by']) ? 'ari.apply_id' : trim($filter['sort_by']);
            $filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

            $from = " FROM ".$this->db->dbprefix('apply_return_info')." AS ari ";
            $sql = "SELECT COUNT(*) AS ct " . $from . " LEFT JOIN " .$this->db->dbprefix('order_info'). " AS ord ON ari.order_id=ord.order_id  ".$where;
            
            $query = $this->db->query($sql);
            $row = $query->row();
            $query->free_result();
            $filter['record_count'] = (int) $row->ct;
            $filter = page_and_size($filter);
            if ($filter['record_count'] <= 0)
            {
                    return array('list' => array(), 'filter' => $filter);
            }
            $sql = "SELECT ari.apply_id, ari.shipping_name, ari.invoice_no, ord.order_sn, ari.sent_user_name, ari.product_number, ari.apply_time, ari.apply_status ".
                    ", ari.provider_status, ari.order_type, ord.order_id " .
                    " FROM " . $this->db->dbprefix('apply_return_info') . " AS ari " .
                    " LEFT JOIN " .$this->db->dbprefix('order_info'). " AS ord ON ari.order_id=ord.order_id  ".
            $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
                            . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

            $query = $this->db->query($sql);
            $list = $query->result_array();
            
            foreach ($list AS $key => $value)
            {
//                $row[$key]['packet_type'] = 0;
//                //买包（结算后拒收）
//                if(in_array($value['advance_payments_flag'],array(1,3))) {
//                        $row[$key]['packet_type'] = 1;
//                }
                //是否能退货
                $list[$key]['is_process'] = false;
                // 去除订单类型判断
                if($value['order_type'] == 0 || $value['order_type'] == 1) {
                        if($value['apply_status'] == 0) {
                                $list[$key]['is_process'] = true;
                        }
                        if($value['apply_status'] == 1) {
                                $return_num = $this->return_apply_num($value['apply_id']);
                                if($return_num < $value['product_num']) {
                                        $list[$key]['is_process'] = true;
                                }
                        }
                } elseif($value['order_type'] == 1) {
                        if($value['apply_status'] == 0 && $value['provider_status'] == 1) {
                                $list[$key]['is_process'] = true;
                        }
                        if($value['apply_status'] == 1 && $value['provider_status'] == 1) {
                                $return_num = $this->return_apply_num($value['apply_id']);
                                if($return_num < $value['product_num']) {
                                        $list[$key]['is_process'] = true;
                                }
                        }
                }
            }

            $query->free_result();
            return array('list' => $list, 'filter' => $filter);
        }
        
        public function return_apply_num($apply_id) {
            $sql = "SELECT SUM(product_num) product_num" . 
                            " FROM ".$this->db->dbprefix('order_return_info').
                            " WHERE return_status != 4 AND apply_id = " . $apply_id . 
                            " GROUP BY apply_id ";
            $result = $this->db->query($sql)->row_array();
            return $result['product_num'];
        }
        
        public function apply_info($apply_id) {
            $sql = "SELECT ari.apply_id,ari.order_id,ari.user_id,ari.provider_id,ari.shipping_name,ari.invoice_no" . 
			",ari.sent_user_name,ari.mobile,ari.tel,ari.shipping_fee,ari.product_number" . 
			",ari.apply_status,ari.provider_status,ari.order_type,ari.apply_time,oi.order_sn,rs.suggest_type" . 
			" FROM ".$this->db->dbprefix('apply_return_info')." AS ari" .  
			" LEFT JOIN ".$this->db->dbprefix('order_info')." AS oi ON oi.order_id = ari.order_id" . 
                        " LEFT JOIN ".$this->db->dbprefix('apply_return_suggest')." AS rs ON rs.apply_id = ari.apply_id ".
			" WHERE ari.apply_id = " . $apply_id;
            return $this->db->query($sql)->row_array();
        }
        
        public function get_return_goods_num($order_id) {
            $result = array();
            $sql = "SELECT rp.product_id,rp.color_id,rp.size_id,rp.product_num" . 
                            " FROM ".$this->db->dbprefix('order_return_info')." AS ri" . 
                            " LEFT JOIN ".$this->db->dbprefix('order_return_product')." AS rp ON rp.return_id = ri.return_id" . 
                            " WHERE ri.order_id = ".$order_id." AND return_status <> 4 ;";
            $return_goods = $this->db->query($sql)->result();
            if(!empty($return_goods)) {
                    foreach($return_goods as $val) {
                            $k = $val->product_id.' '.$val->color_id.' '.$val->size_id;
                            $result[$k] = $val->product_num;
                    }
            }
            return $result;
        }
        
        public function apply_return_goods($apply_id,$order_id) {
            $sql = "SELECT ap.rec_id,ap.apply_id,ap.product_id,ap.color_id,ap.size_id,ap.product_price" . 
			",ap.product_sn,ap.product_name,ap.product_number,ap.return_reason,ap.description,ap.img" . 
			",p.provider_productcode,b.brand_name,op.shop_price,op.product_price,c.color_name" . 
			",s.size_name,op.product_num AS o_product_number,p.unit_name" . 
			" FROM ".$this->db->dbprefix('apply_return_product')." AS ap" . 
			" LEFT JOIN ".$this->db->dbprefix('product_info')." AS p ON p.product_id = ap.product_id" . 
			" LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON b.brand_id = p.brand_id" . 
			" LEFT JOIN ".$this->db->dbprefix('product_color')." AS c ON c.color_id = ap.color_id" . 
			" LEFT JOIN ".$this->db->dbprefix('product_size')." AS s ON s.size_id = ap.size_id" . 
			" LEFT JOIN ".$this->db->dbprefix('order_product')." AS op ON op.product_id = ap.product_id AND op.color_id = ap.color_id AND op.size_id = ap.size_id" . 
			" WHERE ap.apply_id = " . $apply_id . " AND op.order_id = " . $order_id  ." GROUP BY op.op_id ";
            return $this->db->query($sql)->result_array();
        }
        
        public function apply_return_suggest($apply_id) {
            $apply_suggest = array();
            $sql = "SELECT rec_id,apply_id,suggest_type,suggest_content,create_id,create_date" . 
                            " FROM ".$this->db->dbprefix('apply_return_suggest').
                            " WHERE apply_id = ".$apply_id;
            $apply_suggest = $this->db->query($sql)->result_array();
            if(!empty($apply_suggest)) {
                    foreach($apply_suggest as $key=>$v){
                            $v['user_name'] = $this->get_suggest_uname($v['create_id'],$v['suggest_type']);
                            $v['suggest_type_name'] = $v['suggest_type'] == 0 ? '客服意见' : '其他意见';
                            $apply_suggest[$key] = $v;
                    }
            }
            return $apply_suggest;
        }
        
        public function get_suggest_uname($user_id,$suggest_type)
        {
                if($suggest_type == 1 || $suggest_type == 2) {
                        $sql = "SELECT provider_name FROM ".$this->db->dbprefix('product_provider')." WHERE provider_id = ".$user_id;
                } else {
                        $sql = "SELECT admin_name FROM ".$this->db->dbprefix('admin_info')." WHERE admin_id = ".$user_id;
                }
                $result = $this->db->query($sql)->row_array();
                return isset($result['provider_name']) ? $result['provider_name'] : $result['admin_name'];
        }
        
        public function add_apply_suggest($suggest_data)
        {
            return $this->db->insert($this->db->dbprefix('apply_return_suggest'),$suggest_data);
        }
        
        public function cancel_order($cancel_reason,$apply_id, $admin_id)
        {
            $sql = "UPDATE " . $this->db->dbprefix('apply_return_info') . " SET apply_status = 3,cancel_reason='".$cancel_reason."',cancel_admin_id=".$admin_id.",cancel_time='".  date('Y-m-d H:i:s', time())."'  WHERE apply_id = $apply_id "; 
            $this->db->query($sql);
            return true;
        }
        
        public function check_apply_return($order_id) {
            $sql = "SELECT count(*) ct FROM ".$this->db->dbprefix('apply_return_info')." WHERE apply_status IN (0,1) AND order_id = ".$order_id;
            return $this->db->query($sql)->row_array();
        }
        
        public function filter($filter) {
            $query = $this->db->get_where('apply_return_info', $filter, 1);
            return $query->row_array();
        }
        
        //获取申请退货运费
        public function get_apply_shipping_fee($invoice_no) {
            if(empty($invoice_no)) return false;
            $sql = "SELECT SUM(ri.return_shipping_fee) AS apply_shipping_fee" . 
                            " FROM ".$this->db->dbprefix('apply_return_info')." AS ari" . 
                            " LEFT JOIN ".$this->db->dbprefix('order_return_info')." AS ri ON ri.order_id = ari.order_id AND ri.invoice_no = ari.invoice_no" . 
                            " WHERE ri.return_status != 4 AND ri.return_shipping_fee > 0 AND ari.apply_status IN (1,2) AND ri.invoice_no = '".$invoice_no."'" . 
                            " GROUP BY ari.invoice_no ";
            $result = $this->db->query($sql)->row_array();
            return $result['apply_shipping_fee'];
        }
        
        public function update($data,$apply_id)
	{
            $this->db->update('apply_return_info',$data,array('apply_id'=>$apply_id));
	}
        
        //获取申请退货单数量
        public function finish_return_apply($apply_id) {
            $sql = "SELECT SUM(product_num) AS product_num" . 
                            " FROM ".$this->db->dbprefix('order_return_info').
                            " WHERE is_ok = 1 AND return_status = 1 AND apply_id = " . $apply_id . 
                            " GROUP BY apply_id ";
            $result = $this->db->query($sql)->row_array();
            return $result['product_num'];
        }
}
###
