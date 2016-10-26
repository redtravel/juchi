<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Inventory_Model extends CI_Model {
    
    /*
     * 查询一个盘点主信息。
     */
    public function get_inventory($filter) {
        $query = $this->db->get_where('depot_inventory', $filter, 1);
        return $query->row();
    }
    
    /*
     * 查询一个盘点详情信息。
     */
    public function get_inventory_detail($filter) {
        $query = $this->db->get_where('depot_inventory_detail', $filter, 1);
        return $query->row();
    }
    
    /*
     * 查询指定盘点编号的，某个储位已盘点的商品信息。
     */
    public function get_inventory_details($filter) {
        $select = " SELECT * ";
        $from = " FROM ".$this->db->dbprefix('depot_inventory_detail');
        $where = " WHERE 1 ";
        if (!empty($filter['inventory_id'])) {
            $where .= " AND inventory_id = ".$filter['inventory_id'];
        }
        if (!empty($filter['location_id'])) {
            $where .= " AND location_id = ".$filter['location_id'];
        }
        
        $sql = $select . $from . $where;
        
        $query = $this->db->query($sql);
        $list = $query->result();
        $query->free_result();
        
        return $list;
    }
    
    /*
     * 查询盘点列表。
     */
    public function inventory_list($filter) {
        $from = " FROM ".$this->db->dbprefix('depot_inventory')." AS i "
               ." LEFT JOIN ".$this->db->dbprefix('location_info')." AS l ON l.location_id = i.location_id ";
        $where = " WHERE 1 ";
        
        if (!empty($filter['inventory_sn'])) {
            $where .= " AND i.inventory_sn LIKE '%" . $filter['inventory_sn'] . "%'";
        }
        if (!empty($filter['start_date'])) {
            $where .= " AND i.create_date >= '" . $filter['start_date'] . "'";
        }
        if (!empty($filter['end_date'])) {
            $where .= " AND i.create_date <= '" . $filter['end_date'] . "'";
        }
        
        $filter['sort_by'] = empty($filter['sort_by']) ? 'i.inventory_id' : trim($filter['sort_by']);
        $filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);
        
        // count
        $sql = "SELECT COUNT(i.inventory_id) AS ct " . $from . $where;
        $query = $this->db->query($sql);
        $row = $query->row();
        $query->free_result();
        
        $filter['record_count'] = (int) $row->ct;
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0) {
            return array('list' => array(), 'filter' => $filter);
        }
        
        // query
        $sql = "SELECT i.*, l.location_name ". $from . $where 
            . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
            . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
	$query = $this->db->query($sql);
        
        $list = $query->result();
        $query->free_result();
        return array('list' => $list, 'filter' => $filter);
    }
    
    /*
     * 倒序查询最近的5个已审核的盘点。
     */
    public function latest_inventory_list($number) {
        $sql = "SELECT * FROM ".$this->db->dbprefix('depot_inventory')
            . " WHERE status = 1 ORDER BY inventory_id DESC LIMIT " . $number;
	$query = $this->db->query($sql);
        $list = $query->result();
        $query->free_result();
        return array('list' => $list);
    }
    
    /*
     * 插入一条盘点主信息。
     */
    public function insert_inventory($data) {
        $this->db->trans_begin();
        
        $this->db->insert('depot_inventory', $data);
        $id = $this->db->insert_id();
        
        $this->db->trans_commit();
        
        return $id;
    }
    
    /*
     * 更新指定的盘点主信息。
     */
    public function update_inventory($data, $inventory_id) {
        $this->db->update('depot_inventory', $data, array('inventory_id' => $inventory_id));
    }
    
    /*
     * 删除指定的盘点主信息。
     */
    public function delete_inventory($inventory_id) {
        $this->db->trans_begin();
        $this->db->delete('depot_inventory', array('inventory_id' => $inventory_id));
        $this->db->trans_commit();
    }
    
    /*
     * 更新指定的盘点盘点详情信息。
     */
    public function update_inventory_details($data, $inventory_id, $location_id) {
        $this->db->trans_begin();
        $this->db->update('depot_inventory_detail', $data, array('inventory_id' => $inventory_id, 'location_id' => $location_id));
        $this->db->trans_commit();
    }
    
    /*
     * 批量删除指定的盘点详情信息。
     */
    public function delete_inventory_details($filter) {
        $this->db->trans_begin();
        $this->db->delete('depot_inventory_detail', $filter);
        $this->db->trans_commit();
    }
    
    /*
     * 财务审核。
     */
    public function financial_check($inventory, $admin_id) {
        $this->load->model('depotio_model');
        
        // 查询出库单
        $depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_code' => $inventory->depot_out_sn));
        // 查询入库单
        $depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_code' => $inventory->depot_in_sn));
        
        $this->db->trans_begin();
        
        // 出库财审
        if (!empty($depot_out_info)) {
            $this->depotio_model->check_out($depot_out_info, $admin_id);
        }
        // 入库财审
        if (!empty($depot_in_info)) {
            $this->depotio_model->check_in($depot_in_info, $admin_id);
        }
        
        //更新状态
        $data = array();
        $data['status'] = 4;
        $this->update_inventory($data, $inventory->inventory_id);
        
        $this->db->trans_commit();
    }
    
    /*
     * 终止一个已结束的盘点。
     */
    public function stop_inventory($inventory, $admin_id) {
        $depot_out_code = $inventory->depot_out_sn;
        $depot_in_code = $inventory->depot_in_sn;
        
        $this->db->trans_begin();
        
        // 删除出入库记录
        $this->load->model('depot_model');
        if (!empty($depot_out_code)) {
            $this->depot_model->update_gl_num_out($depot_out_code); // 加库存
            $this->cancel_transaction_by_sn($depot_out_code);
        }
        if (!empty($depot_in_code)) {
            $this->cancel_transaction_by_sn($depot_in_code);
        }
        
        // 删除出入库信息
        $this->load->model('depotio_model');
        if (!empty($depot_out_code)) {
            $depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_code' => $depot_out_code));
            $this->depotio_model->update_depot_out(array('is_deleted' => 1), $depot_out_info->depot_out_id);
        }
        if (!empty($depot_in_code)) {
            $depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_code' => $depot_in_code));
            $this->depotio_model->update_depot_in(array('is_deleted' => 1), $depot_in_info->depot_in_id);
        }
        
        // 更新终止状态
        $data = array();
        $data['status'] = 3;
        $data['stop_admin'] = $admin_id;
        $data['stop_date'] = date('Y-m-d H:i:s');
        $this->inventory_model->update_inventory($data, $inventory->inventory_id);
        
        $this->db->trans_commit();
    }

    /*
     * 分页查询一个盘点的详情商品列表。
     */
    public function inventory_product_list($filter) {
        $select = " p.product_id, p.product_name, p.product_sn, pp.provider_code, " 
               ." l.location_id, l.location_name, " 
               ." c.color_id, c.color_name, c.color_sn, s.size_id, s.size_name, s.size_sn, ps.provider_barcode, "
               ." d.inventory_number, d.product_number, d.update_admin, d.update_date ";
        
        $from = " FROM ".$this->db->dbprefix('depot_inventory_detail') . " AS d "
               ." LEFT JOIN ".$this->db->dbprefix('product_info') . " AS p ON p.product_id = d.product_id "
               ." LEFT JOIN ".$this->db->dbprefix('product_provider') . " AS pp ON pp.provider_id = p.provider_id "
               ." LEFT JOIN ".$this->db->dbprefix('location_info') . " AS l ON l.location_id = d.location_id "
               ." LEFT JOIN ".$this->db->dbprefix('product_sub') . " AS ps ON ps.product_id = d.product_id AND ps.color_id = d.color_id AND ps.size_id = d.size_id " 
               ." LEFT JOIN ".$this->db->dbprefix('product_color') . " AS c ON c.color_id = d.color_id "
               ." LEFT JOIN ".$this->db->dbprefix('product_size') . " AS s ON s.size_id = d.size_id ";
        
        $where = " WHERE d.inventory_id = " . $filter['inventory_id'] . " AND (d.inventory_number > 0 || d.product_number > 0)";
        if ($filter['only_show_diff'] == 1) {
            $where .= " AND d.inventory_number != d.product_number ";
        }
        
        $order_by = " ORDER BY l.location_name ASC, d.product_id ASC ";
        
        $sql = "SELECT COUNT(1) AS ct " . $from . $where;
        $query = $this->db->query($sql);
        $row = $query->row();
        $query->free_result();
        
        $filter['record_count'] = (int) $row->ct;
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0) {
            return array('results' => array(), 'filter' => $filter);
        }
        
        // query
        $sql = "SELECT ". $select . $from . $where . $order_by 
            . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
        $query = $this->db->query($sql);
        
        $list = $query->result();
        $query->free_result();
        
        // 按储位分组
        $results = array();
        foreach ($list as $value) {
            if (!isset($results[$value->location_name])) {
                $results[$value->location_name] = array();
            }
            $results[$value->location_name][] = $value;
        }
        
        return array('results' => $results, 'filter' => $filter);
    }
    
    /*
     * 查询一个盘点的全部详情商品列表。
     */
    public function all_inventory_product_list($inventory) {
        $select = " l.location_id, p.product_id, c.color_id, s.size_id, "
               ." l.location_name, p.product_name, ps.provider_barcode, c.color_name, "
               ." c.color_sn, s.size_name, s.size_sn, " 
               ." SUM(t.product_number) AS inventory_product_number, "
               ." d.product_number AS scaned_product_number ";
        
        $from = " FROM ".$this->db->dbprefix('transaction_info') . " AS t "
               ." LEFT JOIN ".$this->db->dbprefix('product_info') . " AS p ON t.product_id = p.product_id "
               ." LEFT JOIN ".$this->db->dbprefix('product_provider') . " AS pp ON pp.provider_id = p.provider_id "
               ." LEFT JOIN ".$this->db->dbprefix('location_info') . " AS l ON l.location_id = t.location_id "
               ." LEFT JOIN ".$this->db->dbprefix('depot_inventory_detail') . " AS d ON d.product_id = t.product_id AND d.color_id = t.color_id AND d.size_id = t.size_id "
               ." LEFT JOIN ".$this->db->dbprefix('product_sub') . " AS ps ON ps.product_id = t.product_id AND ps.color_id = t.color_id AND ps.size_id = t.size_id "
               ." LEFT JOIN ".$this->db->dbprefix('product_color') . " AS c ON c.color_id = t.color_id "
               ." LEFT JOIN ".$this->db->dbprefix('product_size') . " AS s ON s.size_id = t.size_id ";
        
        $where = " WHERE t.depot_id = ". $inventory->depot_id . " AND t.trans_status IN (2,4) ";
        if ($inventory->shelf_from && $inventory->shelf_to) {
            $where .= " AND CONCAT(CONCAT(l.location_code1,'-'),l.location_code2) BETWEEN '" . $inventory->shelf_from . "' AND '" . $inventory->shelf_to . "'";
        }
        
        $group_by = " GROUP BY t.size_id, t.color_id, t.product_id, l.location_id ";
        
        $having = " HAVING inventory_product_number > 0 ";
        
        $order_by = " ORDER BY l.location_name ASC, t.product_id ASC ";
        
        // query
        $sql = "SELECT ". $select . $from . $where . $group_by . $having . $order_by;
	$query = $this->db->query($sql);
        
        $list = $query->result_array();
        $query->free_result();
        
        return $list;
    }
    
    /*
     * 按储位，批量插入盘点详情商品。
     */
    public function insert_inventory_details($inventory, 
            $location, $product_barcode_ary, $product_number_ary, $admin_id) {
        // 扫描商品归集
        $product_ary = array();
        foreach ($product_barcode_ary as $key => $product_barcode) {
            $product_number = intval($product_number_ary[$key]);
            $in = false;
            foreach ($product_ary as $index => $value) {
                if ($product_barcode == $value['provider_barcode']) {
                    $in = true;
                    $value['product_number'] += $product_number;
                    $product_ary[$index] = $value;
                }
            }
            
            if ($in == false) {
                $product = array();
                $product['provider_barcode'] = $product_barcode;
                $product['product_number'] = $product_number;
                $product_ary[] = $product;
            }
        }
        
        // 盘点插入或更新
        $insert_data_ary = array();
        $update_data_ary = array();
        $this->load->model('product_model');
        foreach ($product_ary as $product) {
            $provider_barcode = $product['provider_barcode'];
            $product_number = $product['product_number'];
            
            $product_sub = $this->filter_detail_product_sub(
                $inventory->inventory_id, $location->location_id, $provider_barcode);
            if (!$product_sub) {
                $product_subs = $this->product_model->filter_product_subs(array('provider_barcode' => $provider_barcode));
                $product_sub = $product_subs[count($product_subs) - 1];
            }
            
            $data = array();
            $data['inventory_id'] = $inventory->inventory_id;
            $data['product_id'] = $product_sub->product_id;
            $data['color_id'] = $product_sub->color_id;
            $data['size_id'] = $product_sub->size_id;
            $data['depot_id'] = $inventory->depot_id;
            $data['location_id'] = $location->location_id;

            $inventory_detail = $this->get_inventory_detail($data);
            if (!$inventory_detail) {
                $data['product_number'] = $product_number;
                $data['update_admin'] = $admin_id;
                $data['update_date'] = date('Y-m-d H:i:s');

                $insert_data_ary[] = $data;
            } else {
                $update = array();
                $update['id'] = $inventory_detail->id;
                $update['update_admin'] = $admin_id;
                $update['update_date'] = date('Y-m-d H:i:s');
                $update['product_number'] = $product_number + $inventory_detail->product_number;

                $update_data_ary[] = $update;
            }
        }
        
        // 批量操作
        $this->db->trans_begin();
        if (count($insert_data_ary) > 0) {
            $this->db->insert_batch('depot_inventory_detail', $insert_data_ary);
        }
        if (count($update_data_ary) > 0) {
            $this->db->update_batch('depot_inventory_detail', $update_data_ary, 'id');
        }
        $this->db->trans_commit();
    }
    
    /*
     * 生成盘点清单商品。
     */
    public function generate_details($inventory) {
        // 获取盘点储位列表
        $location_id_list = $this->doGetInventoryLocationIdList($inventory);
        if (count($location_id_list) <= 0) {
            sys_msg('指定范围的储位不能盘点，请重新编辑！', 1);
            return;
        }
        
        $this->db->trans_begin();
        
        // 先删除此盘点已生成的清单
        $this->delete_inventory_details(array('inventory_id' => $inventory->inventory_id));
        
        // 插入盘点详情
        $insert_sql = " INSERT INTO ".$this->db->dbprefix('depot_inventory_detail')
                     ." (inventory_id,depot_id,location_id,product_id,color_id,size_id,product_number,inventory_number) " 
                     ." SELECT "
                     .$inventory->inventory_id." AS inventory_id,".$inventory->depot_id." AS depot_id,"
                     ." i.location_id,i.product_id,i.color_id,i.size_id,0 AS product_number,t.product_number AS inventory_number "
                     ." FROM ".$this->db->dbprefix('transaction_info') . " AS i "
                     ." LEFT JOIN (SELECT product_id,color_id,size_id,location_id,SUM(product_number) AS product_number " 
                                ." FROM ".$this->db->dbprefix('transaction_info')
                                ." WHERE trans_status IN (2,4) "
                                ." GROUP BY size_id,color_id,product_id,location_id "
                                ." HAVING product_number > 0 "
                     ." ) AS t ON t.product_id = i.product_id AND t.color_id = i.color_id AND t.size_id = i.size_id AND t.location_id = i.location_id "
                     ." WHERE i.depot_id = ". $inventory->depot_id 
                     ." AND t.product_number > 0 "
                     ." AND i.location_id IN ".$this->doBuildInWhere($location_id_list)
                     ." GROUP BY i.size_id,i.color_id,i.product_id,i.location_id";
        
        $this->db->query($insert_sql);
        
        // 更新盘点单据生成人和生成时间
        $data = array();
        $data['gen_admin'] = $this->admin_id;
        $data['gen_date'] = date('Y-m-d H:i:s');
        $this->update_inventory($data, $inventory->inventory_id);
        
        $this->db->trans_commit();
    }
    
    /*
     * 导入盘点商品清单。
     */
    public function import_details($inventory, $inventory_detail_ary, $admin_id) {
        $insert_data_ary = array();
        $update_data_ary = array();
        foreach ($inventory_detail_ary as $detail) {
            if (!$detail[10] || $detail[10] <= 0) {
                continue;
            }
            
            $data = array();
            $data['inventory_id'] = $inventory->inventory_id;
            $data['depot_id'] = $inventory->depot_id;
            $data['location_id'] = $detail[0];
            $data['product_id'] = $detail[1];
            $data['color_id'] = $detail[2];
            $data['size_id'] = $detail[3];

            $scan_product_number = $detail[10];
            
            $inventory_detail = $this->get_inventory_detail($data);
            if (!$inventory_detail) {
                $data['product_number'] = $scan_product_number;
                $data['create_admin'] = $admin_id;
                $data['create_date'] = date('Y-m-d H:i:s');
                
                $insert_data_ary[] = $data;
            } else {
                $update = array();
                $update['id'] = $inventory_detail->id;
                $update['update_admin'] = $admin_id;
                $update['update_date'] = date('Y-m-d H:i:s');
                $update['product_number'] = $scan_product_number + $inventory_detail->product_number;
                
                $update_data_ary[] = $update;
            }
        }
        
        // 批量操作
        $this->db->trans_begin();
        if (count($insert_data_ary) > 0) {
            $this->db->insert_batch('depot_inventory_detail', $insert_data_ary);
        }
        if (count($update_data_ary) > 0) {
            $this->db->update_batch('depot_inventory_detail', $update_data_ary, 'id');
        }
        $this->db->trans_commit();
    }
    
    /*
     * 生成盘点差异商品信息。
     */
    public function generate_diff($inventory, $admin_id) {
        $this->db->trans_begin();
        
        // 先删除
        $this->db->delete('depot_inventory_diff', array('inventory_id' => $inventory->inventory_id));
        
        // 查询
        $create_date = date('Y-m-d H:i:s');
        $result_ary = $this->get_inventory_details(array('inventory_id' => $inventory->inventory_id));
        
        $diff_ary = array();
        foreach ($result_ary as $detail) {
            $diff_number = $detail->product_number - $detail->inventory_number;
            if ($diff_number == 0) {
                continue;
            }
            
            $data = array();
            $data['inventory_id'] = $inventory->inventory_id;
            $data['location_id'] = $detail->location_id;
            $data['product_id'] = $detail->product_id;
            $data['color_id'] = $detail->color_id;
            $data['size_id'] = $detail->size_id;
            $data['product_number'] = $diff_number;
            $data['create_admin'] = $admin_id;
            $data['create_date'] = $create_date;
            
            $diff_ary[] = $data;
        }
        
        // 批量插入差异
        if (count($diff_ary) > 0) {
            $this->db->insert_batch('depot_inventory_diff', $diff_ary);
        }
        
        // 更新差异信息
        $update = array();
        $update['diff_admin'] = $admin_id;
        $update['diff_date'] = $create_date;
        $this->update_inventory($update, $inventory->inventory_id);
        
        $this->db->trans_commit();
    }
    
    /*
     * 生成盘点差异商品出入库单据。
     */
    public function generate_invoice($inventory, $admin_id) {
        $this->load->model('depot_model');
        $this->load->model('depotio_model');
        
        // 开启事务
        $this->db->trans_begin();
        
        // 查询盘点差异出库商品
        $out_list = $this->doQueryInventoryDepotOutProductSkus($inventory->inventory_id, $inventory->depot_id);
        if (count($out_list) > 0) {
            // 生成盘亏商品出库单
            $depot_out_code = $this->doGenerateDepotOut($out_list, $inventory->depot_id, $inventory->inventory_id, $inventory->inventory_sn, $admin_id);
        }
        
        // 查询盘点差异入库商品
        $in_list = $this->doQueryInventoryDepotInProductSkus($inventory->inventory_id, $inventory->depot_id);
        if (count($in_list) > 0) {
            // 生成盘赢商品入库单
            $depot_in_code = $this->doGenerateDepotIn($in_list, $inventory->depot_id, $inventory->inventory_id, $inventory->inventory_sn, $admin_id);
        }
        
        // 更新盘点单据结束信息
        $this->doFinishInventory($depot_out_code, $depot_in_code, $inventory->inventory_id);
        
        // 提交事务
        $this->db->trans_commit();
    }
    
    /*
     * 检查指定储位是否可盘点。
     */
    public function checkLocationInInventory($inventory_id, $location_id) {
        $details_list = $this->get_inventory_details(array('inventory_id'=>$inventory_id, 'location_id'=>$location_id));
        if (count($details_list) <= 0) {
            return FALSE;
        }
        return TRUE;
    }
    
    /*
     * 获取指定储位的库存量。
     */
    public function getInventoryNumberByLocation($location_id) {
        $sql = ' SELECT SUM(product_number) AS inventory_product_number '
             . " FROM ".$this->db->dbprefix('transaction_info') 
             . " WHERE location_id = " . $location_id
             . " AND trans_status IN (2,4) ";
        $query = $this->db->query($sql);
        $inventory_number = $query->row()->inventory_product_number;
        $query->free_result();
        
        return $inventory_number;
    }
    
    /*
     * 获取指定盘点的，某个储位上已盘点的商品数量。
     */
    public function getScanedNumberByLocation($inventory_id, $location_id) {
        $sql = ' SELECT SUM(product_number) AS scaned_product_number '
             . " FROM ".$this->db->dbprefix('depot_inventory_detail') 
             . " WHERE inventory_id = " . $inventory_id
             . " AND location_id = " . $location_id;
        $query = $this->db->query($sql);
        $scaned_number = $query->row()->scaned_product_number;
        $query->free_result();
        
        return $scaned_number;
    }
    
    public function doGetUpdateAdmin($inventory_id, $loaction_id) {
        $sql = "SELECT * FROM ".$this->db->dbprefix('depot_inventory_detail')
                ." WHERE inventory_id=".$inventory_id." AND location_id=".$loaction_id." AND update_admin IS NOT NULL LIMIT 1";
        $query = $this->db->query($sql);
        $row = $query->row();
        $query->free_result();
        
        return $row;
    }
    
    public function filterOrderPickedOutTransactionInfos($location_id) {
        $sql = ' SELECT * '
             . " FROM ".$this->db->dbprefix('transaction_info')." AS ti "
             . " INNER JOIN ".$this->db->dbprefix('order_info')." AS oi "
             . " ON oi.order_sn = ti.trans_sn "
             . " WHERE oi.is_pick = 1 AND ti.trans_type = 3 AND ti.trans_status = 1 AND ti.location_id = ".$location_id;
        $query = $this->db->query($sql);
        $list = $query->result();
        $query->free_result();
        
        return $list;
    }
    
    public function getProductBarcodesByLocation($location_id) {
        $sql = ' SELECT SUM(ti.product_number) AS total_number, pi.provider_barcode '
             . " FROM ".$this->db->dbprefix('transaction_info')." AS ti "
             . " LEFT JOIN ".$this->db->dbprefix('product_sub')." AS pi "
             . " ON pi.product_id = ti.product_id AND pi.color_id = ti.color_id AND pi.size_id = ti.size_id "
             . " WHERE ti.location_id = ".$location_id
             . " AND ti.trans_status IN (2,4) "
             . " GROUP BY pi.provider_barcode "
             . " HAVING total_number > 0 ";
        $query = $this->db->query($sql);
        $list = $query->result();
        $query->free_result();
        
        return $list;
    }
    
    public function check_barcode_exist($provider_barcode) {
        $sql = "SELECT p.* FROM ".$this->db->dbprefix('product_sub')." AS p "
              ." INNER JOIN ".$this->db->dbprefix('transaction_info')." AS t "
                    ." ON t.product_id = p.product_id AND t.color_id = p.color_id AND t.size_id = p.size_id "
              ." WHERE p.provider_barcode = '".$provider_barcode."'"
              ." LIMIT 1 ";
        
        $query = $this->db->query($sql);
        $result = $query->row();
        
        return $result;
    }
    
    /* ---- private methods ------------------------------------------------- */
    /*
     * 生成盘点出库单。
     * 盘亏出库：按大的批次号出
     */
    public function doGenerateDepotOut($product_list, $depot_id, $inventory_id, $inventory_sn, $admin_id) {
        // 获取盘点出库类型
        $depot_out_type = $this->depotio_model->filter_depot_iotype(array('depot_type_code' => 'ck002')); // 出库类型ID为盘点出库
        if (!$depot_out_type) {
            sys_msg('盘点出库类型不存在！', 1);
        }

        // 生成盘点出库单
        $date = date('Y-m-d H:i:s');
        $depot_out_code = $this->doGetDepotOutCode();
        $depot_out_id = $this->doGenerateInventoryDepotOut(
                $depot_id, $inventory_id, $inventory_sn, $depot_out_type->depot_type_id, $depot_out_code, $admin_id, $date);

        // 生成盘点出库商品明细
        $depot_out_number = 0;
        $depot_out_amount = 0;
        $depot_out_products = array();
        $trans_out_products = array();
        foreach ($product_list as $product_sku) {
            // 出库商品详情
            $data_out = array();
            $data_out['depot_out_id'] = $depot_out_id;
            $data_out['depot_id'] = $depot_id;
            $data_out['product_id'] = $product_sku->product_id;
            $data_out['color_id'] = $product_sku->color_id;
            $data_out['size_id'] = $product_sku->size_id;
            $data_out['location_id'] = $product_sku->location_id;
            $data_out['product_name'] = $product_sku->product_name;
            $data_out['shop_price'] = $product_sku->shop_price;
            $data_out['product_number'] = -1 * $product_sku->product_number;
            $data_out['product_finished_number'] = -1 * $product_sku->product_number;
            $data_out['product_amount'] = -1 * $product_sku->product_number * $product_sku->shop_price;
            $data_out['create_admin'] = $admin_id;
            $data_out['create_date'] = $date;
            $data_out['location_id'] = $product_sku->location_id;
            $data_out['batch_id'] = $product_sku->batch_id;

            $depot_out_products[] = $data_out;

            $depot_out_number += $data_out['product_number'];
            $depot_out_amount += $data_out['product_amount'];
            
            // 出入库记录详情
            $data_trans = array();
            $data_trans['trans_type'] = TRANS_TYPE_DIRECT_OUT;
            $data_trans['trans_status'] = TRANS_STAT_AWAIT_OUT;
            $data_trans['trans_sn'] = $depot_out_code;
            $data_trans['depot_id'] = $depot_id;
            $data_trans['location_id'] = $product_sku->location_id;
            $data_trans['batch_id'] = $product_sku->batch_id;
            $data_trans['product_id'] = $product_sku->product_id;
            $data_trans['color_id'] = $product_sku->color_id;
            $data_trans['size_id'] = $product_sku->size_id;
            $data_trans['product_number'] = $product_sku->product_number;
            $data_trans['location_id'] = $product_sku->location_id;
            $data_trans['create_admin'] = $admin_id;
            $data_trans['create_date'] = $date;
//            $data_trans['sub_id'] = 0;
//            $data_trans['related_id'] = 0;
            $data_trans['trans_direction'] = 0; // 0=出库 1=入库
            $data_trans['shop_price'] = $product_sku->shop_price;
            $data_trans['consign_price'] = $product_sku->consign_price;
            $data_trans['cost_price'] = $product_sku->cost_price;
            $data_trans['consign_rate'] = $product_sku->consign_rate;
            $data_trans['product_cess'] = $product_sku->product_cess;
            
            $trans_out_products[] = $data_trans;
        }
        
        // 批量插入盘点出库待出记录
        $this->db->insert_batch('transaction_info', $trans_out_products);

        // 批量更新出库商品库存数量
        $this->update_gl_num_out($depot_out_code);
        
        // 检查是否会出现负库存，如果出现，回滚提示
        $this->doCheckOutNegativeGlnum($depot_out_code);
        
        // 批量插入盘点出库单商品明细
        $this->db->insert_batch('depot_out_sub', $depot_out_products);
        
        // 更新出库数量信息
        $modify = array();
        $modify['depot_out_number'] = $depot_out_number;
        $modify['depot_out_finished_number'] = $modify['depot_out_number'];
        $modify['depot_out_amount'] = $depot_out_amount;
        $this->depotio_model->update_depot_out($modify, $depot_out_id);

        // 批量更新出库记录关联信息
        $this->doBatchUpdateRelatedTransOut($depot_out_id, $depot_out_code, $admin_id, $date);
        
        return $depot_out_code;
    }
    
    /*
     * 生成盘点入库单。
     * 盘赢入库：按大的批次号出（同盘亏出库，当关联多个批次时，都默认使用大的批次号）
     */
    public function doGenerateDepotIn($product_list, $depot_id, $inventory_id, $inventory_sn, $admin_id) {
        // 获取盘点入库类型
        $depot_in_type = $this->depotio_model->filter_depot_iotype(array('depot_type_code' => 'rk004')); // 出库类型ID为盘点入库
        if (!$depot_in_type) {
            sys_msg('盘点入库类型不存在！', 1);
        }
        
        // 生成盘点入库单
        $date = date('Y-m-d H:i:s');
        $depot_in_code = $this->doGetDepotInCode();
        $depot_in_id = $this->doGenerateInventoryDepotIn(
                $depot_id, $inventory_id, $inventory_sn, $depot_in_type->depot_type_id, $depot_in_code, $admin_id, $date);
        
        // 生成盘点入库商品明细
        $depot_in_number = 0;
        $depot_in_amount = 0;
        $depot_in_products = array();
        $trans_in_products = array();
        foreach ($product_list as $product_sku) {
            // 构造入库单商品详细列表
            $new_product_sku = array();
            $new_product_sku['depot_in_id'] = $depot_in_id;
            $new_product_sku['depot_id'] = $depot_id;
            $new_product_sku['product_id'] = $product_sku->product_id;
            $new_product_sku['batch_id'] = $product_sku->batch_id;
            $new_product_sku['color_id'] = $product_sku->color_id;
            $new_product_sku['size_id'] = $product_sku->size_id;
            $new_product_sku['location_id'] = $product_sku->location_id;
            $new_product_sku['product_name'] = $product_sku->product_name;
            $new_product_sku['shop_price'] = $product_sku->shop_price;
            $new_product_sku['product_number'] = $product_sku->product_number;
            $new_product_sku['product_amount'] = $product_sku->product_number * $product_sku->shop_price;
            $new_product_sku['create_admin'] = $admin_id;
            $new_product_sku['create_date'] = $date;
            
            $depot_in_products[] = $new_product_sku;
            
            // 出入库记录详情
            $data_trans = array();
            $data_trans['trans_type'] = TRANS_TYPE_DIRECT_IN;
            $data_trans['trans_status'] = TRANS_STAT_AWAIT_IN;
            $data_trans['trans_sn'] = $depot_in_code;
            $data_trans['depot_id'] = $depot_id;
            $data_trans['location_id'] = $product_sku->location_id;
            $data_trans['product_id'] = $product_sku->product_id;
            $data_trans['batch_id'] = $product_sku->batch_id;
            $data_trans['color_id'] = $product_sku->color_id;
            $data_trans['size_id'] = $product_sku->size_id;
            $data_trans['product_number'] = $product_sku->product_number;
            $data_trans['create_admin'] = $admin_id;
            $data_trans['create_date'] = $date;
//            $data_trans['sub_id'] = 0;
//            $data_trans['related_id'] = 0;
            $data_trans['trans_direction'] = 1; // 0=出库 1=入库
            $data_trans['shop_price'] = $product_sku->shop_price;
            $data_trans['consign_price'] = $product_sku->consign_price;
            $data_trans['cost_price'] = $product_sku->cost_price;
            $data_trans['consign_rate'] = $product_sku->consign_rate;
            $data_trans['product_cess'] = $product_sku->product_cess;
            
            $trans_in_products[] = $data_trans;

            // 统计入库商品数量和金额
            $depot_in_number += $new_product_sku['product_number'];
            $depot_in_amount += $new_product_sku['product_amount'];
        }
        
        // 批量插入盘点入库待出记录
        $this->db->insert_batch('transaction_info', $trans_in_products);

        // 批量插入盘点入库单商品明细，关联盘赢新商品
        $this->db->insert_batch('depot_in_sub', $depot_in_products);
        
        // 更新入库数量信息
        $modify = array();
        $modify['depot_in_number'] = $depot_in_number;
        $modify['depot_in_finished_number'] = $modify['depot_in_number'];
        $modify['depot_in_amount'] = $depot_in_amount;
        $this->depotio_model->update_depot_in($modify, $depot_in_id);

        // 批量更新入库记录关联信息
        $this->doBatchUpdateRelatedTransIn($depot_in_id, $depot_in_code, $admin_id, $date);
        
        return $depot_in_code;
    }
    
    /*
     * 更新盘点出库sub表的gl_num
     */
    private function update_gl_num_out($trans_sn) {
        $sql = "UPDATE ".$this->db->dbprefix('product_sub')." a, " .
               "(SELECT z.product_id,z.color_id,z.size_id,SUM(z.product_number) as total" .
               " FROM ".$this->db->dbprefix('transaction_info')." z" .
               " LEFT JOIN ".$this->db->dbprefix('depot_info')." x ON x.depot_id = z.depot_id" .
               " WHERE x.depot_type = 1 AND z.trans_sn = '".$trans_sn."' AND z.trans_status = ".TRANS_STAT_AWAIT_OUT." GROUP BY z.product_id,z.color_id,z.size_id) b" .
               " SET a.gl_num = a.gl_num + b.total" .
               " WHERE b.product_id = a.product_id AND b.color_id = a.color_id AND b.size_id = a.size_id";
        $this->db->query($sql);
    }
    
    /*
     * 检查出库单是否会出现负库存
     */
    private function doCheckOutNegativeGlnum($depot_out_code) {
        $sql = " SELECT p.gl_num, p.provider_barcode "
              ." FROM ".$this->db->dbprefix('product_sub')." AS p "
              ." INNER JOIN ".$this->db->dbprefix('transaction_info')." AS t ON t.product_id = p.product_id AND t.color_id = p.color_id AND t.size_id = p.size_id "
              ." WHERE t.trans_sn = '".$depot_out_code."'"
              ." HAVING p.gl_num < 0";
        
        $query = $this->db->query($sql);
        $list = $query->result();
        $query->free_result();
        
        $size = count($list);
        if ($size > 0) {
            $msg = "生成出库单据失败，以下盘出商品出现负库存：";
            foreach ($list as $key => $value) {
                $msg .= $value->provider_barcode;
                if ($key != $size - 1) {
                    $msg .= ",";
                }
            }
            
            // 回滚事务
            $this->db->trans_rollback();
            // 返回异常
            sys_msg($msg, 1);
        }
    }
    
    /*
     * 查询指定盘点差异出库商品列表。
     */
    private function doQueryInventoryDepotOutProductSkus($inventory_id, $depot_id) {
        $select = "SELECT d.*, p.product_name, p.shop_price, c.consign_price, c.cost_price, c.consign_rate, c.product_cess, t.batch_id, t.depot_id ";
        
        $from = " FROM ".$this->db->dbprefix('depot_inventory_diff') . " AS d"
               ." LEFT JOIN ".$this->db->dbprefix('product_info') . " AS p ON p.product_id = d.product_id"
               ." LEFT JOIN (SELECT product_id, color_id, size_id, depot_id, MAX(batch_id) AS batch_id "
                          ." FROM " .$this->db->dbprefix('transaction_info') 
                          ." WHERE depot_id = " . $depot_id
                          ." GROUP BY product_id, color_id, size_id "
               ." ) AS t ON t.product_id = d.product_id AND t.color_id = d.color_id AND t.size_id = d.size_id "
               ." LEFT JOIN ".$this->db->dbprefix('product_cost') . " AS c ON c.product_id = d.product_id AND c.batch_id = t.batch_id";
        
        $where = " WHERE d.inventory_id = ". $inventory_id . " AND d.product_number < 0";
        
        // query
        $sql = $select . $from . $where;
	$query = $this->db->query($sql);
        $list = $query->result();
        $query->free_result();
        
        return $list;
    }
    
    /*
     * 查询指定盘点差异入库商品列表。
     */
    private function doQueryInventoryDepotInProductSkus($inventory_id, $depot_id) {
        $select = "SELECT d.*, p.product_name, p.shop_price, c.consign_price, c.cost_price, c.consign_rate, c.product_cess, t.batch_id, t.depot_id ";
        
        $from = " FROM ".$this->db->dbprefix('depot_inventory_diff') . " AS d"
               ." LEFT JOIN ".$this->db->dbprefix('product_info') . " AS p ON p.product_id = d.product_id"
               ." LEFT JOIN (SELECT product_id, color_id, size_id, depot_id, MAX(batch_id) AS batch_id "
                          ." FROM " .$this->db->dbprefix('transaction_info') 
                          ." WHERE depot_id = " . $depot_id
                          ." GROUP BY product_id, color_id, size_id "
               ." ) AS t ON t.product_id = d.product_id AND t.color_id = d.color_id AND t.size_id = d.size_id "
               ." LEFT JOIN ".$this->db->dbprefix('product_cost') . " AS c ON c.product_id = d.product_id AND c.batch_id = t.batch_id";
        
        $where = " WHERE d.inventory_id = ". $inventory_id . " AND d.product_number > 0";
        
        // query
        $sql = $select . $from . $where;
	$query = $this->db->query($sql);
        $list = $query->result();
        $query->free_result();
        
        return $list;
    }
    
    /*
     * 生成盘点出库单。
     */
    private function doGenerateInventoryDepotOut($depot_id, $inventory_id, $inventory_sn, $depot_type_id, $depot_out_code, $admin_id, $date) {
        $update = array();
        $update['depot_out_type'] = $depot_type_id;
        if ($inventory_id != null) {
            $update['order_id'] = $inventory_id;
        }
        if ($inventory_sn != null) {
            $update['order_sn'] = $inventory_sn;
        }
        $update['depot_depot_id'] = $depot_id;
        $update['depot_out_reason'] = '盘点出库';
        $update['depot_out_code'] = $depot_out_code;
        $update['create_date'] = $date;
        $update['create_admin'] = $admin_id;
        $update['lock_date'] = $date;
        $update['lock_admin'] = -1;
        $update['depot_out_date'] = $date;

        $depot_out_id = $this->depotio_model->insert_depot_out($update);
        
        return $depot_out_id;
    }
    
    /*
     * 生成盘点入库单。
     */
    private function doGenerateInventoryDepotIn($depot_id, $inventory_id, $inventory_sn, $depot_type_id, $depot_in_code, $admin_id, $date) {
        $update = array();
        $update['depot_in_type'] = $depot_type_id;
        // 关联盘点ID和盘点单号
        if ($inventory_id != null) {
            $update['order_id'] = $inventory_id;
        }
        if ($inventory_sn != null) {
            $update['order_sn'] = $inventory_sn;
        }
        $update['depot_depot_id'] = $depot_id;
        $update['depot_in_reason'] = '盘点入库';
        $update['depot_in_code'] = $depot_in_code;
        $update['create_date'] = $date;
        $update['create_admin'] = $admin_id;
        $update['lock_date'] = $date;
        $update['lock_admin'] = -1;
        $update['depot_in_date'] = $date;
        
        $depot_in_id = $this->depotio_model->insert_depot_in($update);
        
        return $depot_in_id;
    }
       
    /*
     * 获取指定盘点单的储位列表。
     */
    private function doGetInventoryLocationIdList($inventory) {
        $all_location_id_list = array();
        $tmp_obj = new stdClass();
        if ($inventory->inventory_type == 0) { // 指定货架范围盘点
            // 查询指定仓库，指定货架范围的储位列表
            $location_select_sql = "SELECT location_id "
                    ." FROM ".$this->db->dbprefix('location_info')
                    ." WHERE depot_id = ". $inventory->depot_id;
            if ($inventory->shelf_from && $inventory->shelf_to) {
                $location_select_sql .= " AND CONCAT(CONCAT(location_code1,'-'),location_code2) BETWEEN '" . $inventory->shelf_from . "' AND '" . $inventory->shelf_to . "'";
            }

            $location_query = $this->db->query($location_select_sql);
            $all_location_id_list = $location_query->result();
            $location_query->free_result();

            if (count($all_location_id_list) <= 0) {
                return;
            }
        } else if ($inventory->inventory_type == 1) { // 指定批次盘点
            $tmp_obj->location_id = $inventory->location_id;
            $all_location_id_list[] = $tmp_obj;
        }
        
        // 查询待入/订单已拣货待出/或者出库待出的储位，作为排除储位
        $exclude_select_sql = "SELECT location_id FROM ".$this->db->dbprefix('transaction_info')
                    ." WHERE ((trans_status = 3) OR (trans_type = 2 AND trans_status = 1)) "
                    ." AND location_id IN ".$this->doBuildInWhere($all_location_id_list)
                    ." UNION "
                    ." SELECT ti.location_id FROM ".$this->db->dbprefix('transaction_info')." AS ti "
                    ." INNER JOIN ".$this->db->dbprefix('order_info')." AS oi "
                    ." ON oi.order_sn = ti.trans_sn "
                    ." WHERE ti.trans_type = 3 AND oi.is_pick = 1 AND ti.trans_status = 1"
                    ." AND location_id IN ".$this->doBuildInWhere($all_location_id_list);
        $exclude_query = $this->db->query($exclude_select_sql);
        $exclude_location_id_list = $exclude_query->result();
        $exclude_query->free_result();
        
        // 记录排除的不能盘点的储位列表
        $exclude_size = count($exclude_location_id_list);
        if ($exclude_size > 0) {
            $exclude_locations = "";
            foreach ($exclude_location_id_list as $key => $value) {
                $exclude_locations .= $value->location_id;
                if ($key != $exclude_size - 1) {
                    $exclude_locations .= ",";
                }
            }
            
            $update = array();
            $update['exclude_locations'] = $exclude_locations;
            $this->update_inventory($update, $inventory->inventory_id);
        }
        
        // 查询合法的储位列表。
        $select_sql = "SELECT DISTINCT t.location_id "
                ." FROM ".$this->db->dbprefix('transaction_info') . " AS t "
                ." WHERE t.location_id IN ".$this->doBuildInWhere($all_location_id_list);
        if ($exclude_size > 0) {
            $select_sql .= " AND t.location_id NOT IN ".$this->doBuildInWhere($exclude_location_id_list);
        }

        $query = $this->db->query($select_sql);
        $location_id_list = $query->result();
        $query->free_result();
        
        return $location_id_list;
    }
    
    /*
     * 生成一个唯一的出库单编号。
     */
    private function doGetDepotOutCode() {
        $depot_out_code = $this->depotio_model->get_depot_out_code();
        
        while ($this->depotio_model->filter_depot_out(array('depot_out_code'=>$depot_out_code))) {
            set_time_limit(1);
            $depot_out_code = $this->depotio_model->get_depot_out_code();
        }
        
        return $depot_out_code;
    }
    
    /*
     * 生成一个唯一的入库单编号。
     */
    private function doGetDepotInCode() {
        $depot_in_code = $this->depotio_model->get_depot_in_code();
        
        while ($this->depotio_model->filter_depot_in(array('depot_in_code'=>$depot_in_code))) {
            set_time_limit(1);
            $depot_in_code = $this->depotio_model->get_depot_in_code();
        }
        
        return $depot_in_code;
    }
    
    /*
     * 设置盘点结束。
     */
    private function doFinishInventory($depot_out_code, $depot_in_code, $inventory_id) {
        $update = array();
        $update['status'] = 2; // 结束
        if (!empty($depot_out_code)) {
            $update['depot_out_sn'] = $depot_out_code;
        }
        if (!empty($depot_in_code)) {
            $update['depot_in_sn'] = $depot_in_code;
        }
        $this->update_inventory($update, $inventory_id);
    }
    
    /*
     * 设置出入库记录关联的出库明细sub_id
     */
    private function doBatchUpdateRelatedTransOut($depot_out_id, $depot_out_code, $admin_id, $date) {
        $sql = " UPDATE ".$this->db->dbprefix('transaction_info')." AS t, "
              ." (SELECT depot_out_sub_id,product_id,color_id,size_id "
                    ." FROM ".$this->db->dbprefix('depot_out_sub')
                    ." WHERE depot_out_id = ".$depot_out_id
              ." ) AS d "
              ." SET t.sub_id = d.depot_out_sub_id, update_admin = ".$admin_id.", update_date = '".$date."'"
              ." WHERE t.trans_sn = '".$depot_out_code."'"
              ." AND t.product_id = d.product_id AND t.color_id = d.color_id AND t.size_id = d.size_id ";
        
        $this->db->query($sql);
    }
    
    /*
     * 设置出入库记录关联的入库明细sub_id
     */
    private function doBatchUpdateRelatedTransIn($depot_in_id, $depot_in_code, $admin_id, $date) {
        $sql = " UPDATE ".$this->db->dbprefix('transaction_info')." AS t, "
              ." (SELECT depot_in_sub_id,product_id,color_id,size_id "
                    ." FROM ".$this->db->dbprefix('depot_in_sub')
                    ." WHERE depot_in_id = ".$depot_in_id
              ." ) AS d "
              ." SET t.sub_id = d.depot_in_sub_id, update_admin = ".$admin_id.", update_date = '".$date."'"
              ." WHERE t.trans_sn = '".$depot_in_code."'"
              ." AND t.product_id = d.product_id AND t.color_id = d.color_id AND t.size_id = d.size_id ";
        
        $this->db->query($sql);
    }
    
    private function doBuildInWhere($location_id_list) {
        $location_id_size = count($location_id_list);
        $in = '(';
        foreach ($location_id_list as $key => $value) {
            $in .= $value->location_id;
            if ($key != $location_id_size - 1) {
                $in .= ",";
            }
        }
        $in .= ")";
        
        return $in;
    }
    
    private function filter_detail_product_sub($inventory_id, $location_id, $provider_barcode) {
        $sql = "SELECT p.* FROM ".$this->db->dbprefix('product_sub')." AS p "
              ." INNER JOIN ".$this->db->dbprefix('depot_inventory_detail')." AS d "
                    ." ON d.product_id = p.product_id AND d.color_id = p.color_id AND d.size_id = p.size_id "
              ." WHERE p.provider_barcode = '".$provider_barcode."'"
              ." AND d.inventory_id = ".$inventory_id
              ." AND d.location_id = ".$location_id
              ." LIMIT 1 ";
        
        $query = $this->db->query($sql);
        $result = $query->row();
        
        return $result;
    }
    
    private function cancel_transaction_by_sn($trans_sn) {
        $this->db->update('transaction_info', array('trans_status' => 5), array('trans_sn' => $trans_sn));
    }
    
    /**
     * 查询库存预警条目
     * @param type $filter
     * @return type
     */
    public function search_inventory_warning_list($filter)
    {
        $from = " FROM ty_inventory_warning iw
                  LEFT JOIN ty_product_info pi on pi.product_id = iw.warn_value
                  LEFT JOIN ty_product_provider pp on pp.provider_id = pi.provider_id
                  LEFT JOIN ty_purchase_batch pb on pb.batch_id = iw.warn_value";
        $where = " WHERE 1 ";
        if (!empty($filter['id']))
        {
            $where .= " AND iw.id = " .$filter['id'];
        }
        if (!empty($filter['provider_id'])) {
            $where .= " AND pi.provider_id = ".$filter['provider_id'];
        }
        if (!empty($filter['purchase_batch'])) {
            $where .= " AND pb.batch_id = ".$filter['purchase_batch'];
        }
        if (!empty($filter['product_sn'])) {
            $where .= " AND pi.product_sn = '".$filter['product_sn']."'";
        }
        
        $sql = "SELECT count(iw.id) ct " .$from .$where;
        $query = $this->db->query($sql);
        $row = $query->row();
        $query->free_result();
        
        $filter['record_count'] = (int) $row->ct;
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0) {
            return array('list' => array(), 'filter' => $filter);
        }
        
        $sql = "SELECT iw.*, pi.product_name, pb.batch_name " .$from .$where . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
        $res = $this->db_r->query($sql)->result();
        
        return array('list' => $res, 'filter' => $filter);
    }
    
    public function insert_inventory_warning($update)
    {
            $this->db->insert('inventory_warning', $update);
            return $this->db->insert_id();
    }
    
    public function filter_inventory_warning ($filter)
    {
            $query = $this->db->get_where('inventory_warning', $filter, 1);
            return $query->row();
    }
    
    public function search_inventory_warning_info($warning_id = 0)
    {
        $sql = " SELECT iw.id, iw.warn_type, pb.provider_id, pb.batch_id, pi.product_sn, iw.min_number, iw.warn_status 
                 FROM ty_inventory_warning iw
                 LEFT JOIN ty_product_info pi on pi.product_id = iw.warn_value
                 LEFT JOIN ty_product_provider pp on pp.provider_id = pi.provider_id
                 LEFT JOIN ty_purchase_batch pb on pb.batch_id = iw.warn_value
                 WHERE iw.id = " .$warning_id;
        $query = $this->db_r->query($sql);
        
        return $query->row();
    }
    
    public function update_inventory_warning($data, $warning_id)
    {
            $this->db->update('inventory_warning', $data, array('id' => $warning_id));
            return $this->db->affected_rows();
    }
    
    /**
     * 查询预警库存
     * @param type $filter
     * @return type
     */
    public function search_warning_inventory_list($filter)
    {
        $from = " FROM ty_product_info pi
                  LEFT JOIN ty_product_sub ps ON ps.product_id = pi.product_id
                  LEFT JOIN ty_product_color pc ON pc.color_id = ps.color_id
                  LEFT JOIN ty_product_size psz ON psz.size_id = ps.size_id
                  LEFT JOIN ty_product_brand pb ON pi.brand_id = pb.brand_id
                  INNER JOIN (
                        SELECT t.id, t.product_id, MAX(t.min_number) min_number
                        FROM (
                              SELECT iw.id, ti.product_id, MAX(iw.min_number) min_number
                                FROM ty_transaction_info ti
                                LEFT JOIN ty_inventory_warning iw ON iw.warn_value = ti.batch_id
                                LEFT JOIN ty_product_sub ps ON ps.product_id = ti.product_id AND ps.color_id = ti.color_id AND ps.size_id = ti.size_id
                                WHERE iw.warn_type = 2 AND iw.warn_status = 1
                                  AND ti.trans_type = 1
                                GROUP BY ti.product_id
                              UNION
                              SELECT iw.id, iw.warn_value, MAX(iw.min_number)
                                FROM ty_inventory_warning iw
                                WHERE iw.warn_type = 1 AND iw.warn_status = 1
                                GROUP BY iw.warn_value) t
                        GROUP BY t.product_id
                  ) tt ON tt.product_id = pi.product_id AND ps.consign_num < tt.min_number ";
        $where = " WHERE 1 ";
        if (!empty($filter['provider_id'])) {
            $where .= " AND pi.provider_id = ".$filter['provider_id'];
        }
        if (!empty($filter['purchase_batch'])) {
            $where .= " AND pba.batch_id = ".$filter['purchase_batch'];
        }
        if (!empty($filter['product_sn'])) {
            $where .= " AND pi.product_sn = '".$filter['product_sn']."'";
        }
        
        $sql = "SELECT count(1) ct " .$from .$where;
        $query = $this->db->query($sql);
        $row = $query->row();
        $query->free_result();
        
        $filter['record_count'] = (int) $row->ct;
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0) {
            return array('list' => array(), 'filter' => $filter);
        }
        
        $sql = "SELECT pi.product_id, pi.product_name, pi.product_sn, pc.color_name, psz.size_name, pi.provider_productcode, pb.brand_name, ps.consign_num, tt.min_number, tt.id " 
                .$from .$where . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
        $result = $this->db_r->query($sql)->result();
        
        return array('list' => $result, 'filter' => $filter);
    }
    
}

?>
