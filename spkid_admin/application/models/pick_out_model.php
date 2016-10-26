<?php

/**
 * 检货出库模块功能独立，箱子操作功能独立
 * Description of pick_out_model
 *
 * @author mickey
 */
class pick_out_model extends CI_Model {

    public function filter_depot_out($filter) {
	$query = $this->db->get_where('depot_out_main', $filter, 1);
	return $query->row();
    }

    public function filter_depot_in($filter) {
	$query = $this->db->get_where('depot_in_main', $filter, 1);
	return $query->row();
    }

    public function filter_box($filter) {
	$query = $this->db->get_where('box', $filter, 1);
	return $query->row();
    }

    public function filter_box_sub($filter) {
	$query = $this->db->get_where('box_sub', $filter);
	return $query->result();
    }

    public function filter_box_leaf($filter) {
	$query = $this->db->get_where('box_leaf', $filter);
	return $query->result();
    }

    public function query_all_scan_number($doc_type, $doc_code) {
	$sql = "SELECT SUM(scan_number) AS ct FROM " . $this->db->dbprefix('box') . " WHERE doc_code = ? AND doc_type = ?";
	$query = $this->db_r->query($sql, array($doc_code, $doc_type));
	$row = $query->row();
	$query->free_result();
	return intval($row->ct);
    }

    public function query_all_shelve_number($doc_type, $doc_code) {
	$sql = "SELECT SUM(shelve_number) AS ct FROM " . $this->db->dbprefix('box') . " WHERE doc_code = ? AND doc_type = ?";
	$query = $this->db_r->query($sql, array($doc_code, $doc_type));
	$row = $query->row();
	$query->free_result();
	return intval($row->ct);
    }

    public function query_box_count($doc_type, $doc_code) {
	$sql = "SELECT COUNT(1) AS ct FROM " . $this->db->dbprefix('box') . " WHERE doc_code = ? AND doc_type = ?";
	$query = $this->db_r->query($sql, array($doc_code, $doc_type));
	$row = $query->row();
	$query->free_result();
	return intval($row->ct);
    }

    public function query_box_details($doc_type, $doc_code, $box_code = "") {
	$select = "s.box_sub_id,p.product_name,p.product_sn,b.brand_name,p.provider_productcode,sub.provider_barcode,";
	$select .="s.product_id,s.color_id,s.size_id,color_name,size_name,s.scan_number as product_number,s.shelve_number as box_finished_check_number";
	$sql = "SELECT " . $select . " FROM " . $this->db->dbprefix('box_sub') . " s ";
	$sql .= " LEFT JOIN " . $this->db->dbprefix('product_info') . " p ON s.product_id = p.product_id ";
	$sql .= " LEFT JOIN " . $this->db->dbprefix('product_brand') . " b ON b.brand_id = p.brand_id ";
	$sql .= " LEFT JOIN " . $this->db->dbprefix('product_sub') . " sub ON sub.product_id = s.product_id AND sub.color_id = s.color_id AND sub.size_id = s.size_id ";
	$sql .= " LEFT JOIN " . $this->db->dbprefix('product_color') . " color ON color.color_id = s.color_id";
	$sql .= " LEFT JOIN " . $this->db->dbprefix('product_size') . " size ON size.size_id = s.size_id";
	$sql .= " WHERE s.doc_type = ? AND s.doc_code =?";
	$param = array();
	$param[] = $doc_type;
	$param[] = $doc_code;
	if ($box_code != "") {
	    $sql .= "  AND s.box_code =? ";
	    $param[] = $box_code;
	}
	$query = $this->db->query($sql, $param);
	$list = $query->result();
	$query->free_result();
	return $list;
    }

    //校验当前储位总共需要下架多少件商品
    public function check_depot_out_location($depot_out_code, $location_name, $doc_type) {
	$sql = "SELECT SUM(s.product_number) AS ct FROM " . $this->db->dbprefix('depot_out_sub') . " s ";
	$sql .= " INNER JOIN " . $this->db->dbprefix('depot_out_main') . " m ON m.depot_out_id = s.depot_out_id ";
	$sql .= " INNER JOIN " . $this->db->dbprefix('location_info') . " l ON l.location_id = s.location_id ";
	$sql .= " WHERE m.depot_out_code = ? AND l.location_name = ? AND m.depot_out_type = ? ";
	$query = $this->db->query($sql, array($depot_out_code, $location_name, $doc_type));
	$row = $query->row();
	$query->free_result();
	return intval($row->ct);
    }

    //查询当前储位的实际出库详情
    public function query_depot_out_location($depot_out_code, $location_name) {
	//box
	$sql = "SELECT s.location_id,ps.sub_id,ps.provider_barcode,s.product_id,s.color_id,s.size_id,s.product_number AS product_number,s.product_finished_number AS scan_number";
	$sql .= " FROM " . $this->db->dbprefix('depot_out_sub') . " s ";
	$sql .= " INNER JOIN " . $this->db->dbprefix('depot_out_main') . " m ON m.depot_out_id = s.depot_out_id ";
	$sql .= " INNER JOIN " . $this->db->dbprefix('location_info') . " l ON l.location_id = s.location_id ";
	$sql .= " INNER JOIN " . $this->db->dbprefix('product_sub') . " ps ON s.product_id = ps.product_id AND s.color_id = ps.color_id AND s.size_id = ps.size_id";
	$sql .= " WHERE s.product_number>s.product_finished_number AND m.depot_out_code = ? AND l.location_name = ? ";
	$query = $this->db->query($sql, array($depot_out_code, $location_name));
	$list = $query->result();
	$query->free_result();
	return $list;
    }

    //查询当前储位已经下架多少件商品
    public function query_pick_depot_out_location($depot_out_code, $location_name) {
	$sql = "SELECT IFNULL(SUM(num),0) AS ct FROM " . $this->db->dbprefix('box_leaf') . " l";
	$sql .= " LEFT JOIN " . $this->db->dbprefix('box') . " b ON b.box_id = l.box_id";
	$sql .= " LEFT JOIN " . $this->db->dbprefix('location_info') . " i ON l.location_id = i.location_id";
	$sql .= " WHERE b.doc_code = ? AND b.doc_type=1 AND i.location_name = ?";
	$query = $this->db->query($sql, array($depot_out_code, $location_name));
	$row = $query->row();
	$query->free_result();
	return intval($row->ct);
    }

    //查询当前储位已经下架多少件指定商品
    public function query_pick_SKU_count_of_location($depot_out_code, $location_id, $product_id, $color_id, $size_id) {
	$sql = "SELECT IFNULL(SUM(num),0) AS ct FROM " . $this->db->dbprefix('box_leaf') . " l";
	$sql .= " LEFT JOIN " . $this->db->dbprefix('box') . " b ON b.box_id = l.box_id";
	$sql .= " WHERE b.doc_code = ? AND b.doc_type=1 AND i.location_id =?";
	$sql .=" AND b.product_id = ? AND b.color_id =? AND b.size_id = ?";
	$query = $this->db->query($sql, array($depot_out_code, $location_id, $product_id, $color_id, $size_id));
	$row = $query->row();
	$query->free_result();
	return intval($row->ct);
    }

    //拣货对应的Depot记录
    public function pick_out_finish($args) {
	$this->db->trans_begin();
	$query = $this->db->get_where('box', array("box_code" => $args['box_code']), 1);
	$box = $query->row_array();
	if (empty($box)) {
	    $box = array();
	    $box["box_code"] = $args['box_code'];
	    $box["doc_code"] = $args['depot_out']->depot_out_code;
	    $box["doc_id"] = $args['depot_out']->depot_out_id;
	    $box["doc_type"] = $args['doc_type'];
	    $box["scan_number"] = 0;
	    $box["scan_id"] = $args['admin_id'];
	    $box["scan_starttime"] = $args['time'];
	    $box["scan_endtime"] = $args['time'];
	    $this->db->insert('box', $box);
	    $box["box_id"] = $this->db->insert_id();
	} elseif ($box["doc_type"] != $args['doc_type']) {
	    sys_msg("不是此出库单对应箱子，不允许打开", 1, array(), FALSE);
	    return;
	} elseif ($box["doc_code"] != $args['depot_out']->depot_out_code) {
	    sys_msg("不是同一出库单，不允许打开", 1, array(), FALSE);
	    return;
	}
	$scan_num = 0;
	foreach ($args["product_array"] as $product) {
	    if ($product["scan_num"] == 0)
		continue;
	    $query = $this->db->get_where('product_sub', array("sub_id" => $product['sub_id']), 1);
	    $db_product_sub = $query->row_array();
	    if (empty($db_product_sub)) {
		continue;
	    }
	    //check depot_out_sub对应的储位下架是否正确
	    $d_o_s_filter = array();
	    $d_o_s_filter["location_id"] = $args["location_info"]->location_id;
	    $d_o_s_filter["product_id"] = $db_product_sub["product_id"];
	    $d_o_s_filter["color_id"] = $db_product_sub["color_id"];
	    $d_o_s_filter["size_id"] = $db_product_sub["size_id"];
	    $d_o_s_filter["depot_out_id"] = $args['depot_out']->depot_out_id;
	    $query = $this->db->get_where('depot_out_sub', $d_o_s_filter, 1);
	    $db_d_o_s = $query->row_array();
	    if (empty($db_d_o_s)) {
		sys_msg("该储位不需要下架对应商品【" . $db_product_sub["provider_barcode"] . "】", 1, array(), FALSE);
	    } else {
		$wait_number = intval($db_d_o_s["product_number"]) - intval($db_d_o_s["product_finished_number"]);
		if (intval($product["scan_num"]) > intval($wait_number)) {
		    sys_msg("该储位只需要下架" . $wait_number . "件，目前下架" . $product["scan_num"] . "件，条码【" . $db_product_sub["provider_barcode"] .
			    "】,调试信息:p_num[" . $db_d_o_s["product_number"] . "],f_num[" . $db_d_o_s["product_finished_number"] . "]", 1, array(), FALSE);
		}
	    }
	    //check 完成 执行操作
	    $product_filter = array();
	    $product_filter["box_id"] = $box["box_id"];
	    $product_filter["product_id"] = $db_product_sub["product_id"];
	    $product_filter["color_id"] = $db_product_sub["color_id"];
	    $product_filter["size_id"] = $db_product_sub["size_id"];
	    $query = $this->db->get_where('box_sub', $product_filter, 1);
	    $box_sub = $query->row_array();
	    if (empty($box_sub)) {
		$box_sub = array();
		$box_sub["box_id"] = $box["box_id"];
		$box_sub["box_code"] = $args['box_code'];
		$box_sub["doc_code"] = $args['depot_out']->depot_out_code;
		$box_sub["doc_id"] = $args['depot_out']->depot_out_id;
		$box_sub["doc_type"] = $args['doc_type'];
		$box_sub["product_id"] = $db_product_sub["product_id"];
		$box_sub["color_id"] = $db_product_sub["color_id"];
		$box_sub["size_id"] = $db_product_sub["size_id"];
		$box_sub["scan_number"] = $product["scan_num"];
		$box_sub["scan_id"] = $args['admin_id'];
		$box_sub["scan_starttime"] = $args['time'];
		$box_sub["scan_endtime"] = $args['time'];
		$this->db->insert('box_sub', $box_sub);
	    } else {
		$box_sub_update = array();
		$box_sub_update["scan_number"] = $product["scan_num"] + $box_sub["scan_number"];
		$box_sub_update["scan_endtime"] = $args['time'];
		$this->db->update('box_sub', $box_sub_update, array('box_sub_id' => $box_sub["box_sub_id"]));
	    }
	    //同步更新details 表
	    $box_detail_filter = array();
	    $box_detail_filter["box_id"] = $box["box_id"];
	    $box_detail_filter["location_id"] = $args["location_info"]->location_id;
	    $box_detail_filter["product_id"] = $db_product_sub["product_id"];
	    $box_detail_filter["color_id"] = $db_product_sub["color_id"];
	    $box_detail_filter["size_id"] = $db_product_sub["size_id"];
	    $query = $this->db->get_where('box_leaf', $box_detail_filter, 1);
	    $box_detail = $query->row_array();
	    if (empty($box_detail)) {
		$box_detail = array();
		$box_detail["box_id"] = $box["box_id"];
		$box_detail["box_code"] = $args['box_code'];
		$box_detail["location_id"] = $args["location_info"]->location_id;
		$box_detail["product_id"] = $db_product_sub["product_id"];
		$box_detail["color_id"] = $db_product_sub["color_id"];
		$box_detail["size_id"] = $db_product_sub["size_id"];
		$box_detail["num"] = $product["scan_num"];
		$this->db->insert('box_leaf', $box_detail);
	    } else {
		$box_detail_update = array();
		$box_detail_update["num"] = $product["scan_num"] + $box_detail["num"];
		$this->db->update('box_leaf', $box_detail_update, array('id' => $box_detail["id"]));
	    }

	    $depot_out_sub_up_sql = "UPDATE " . $this->db->dbprefix('depot_out_sub') . " SET product_finished_number = product_finished_number + ? ";
	    $depot_out_sub_up_sql .=" WHERE depot_out_id = ? AND location_id = ? AND product_id = ? AND color_id = ? AND size_id = ?";
	    $this->db->query($depot_out_sub_up_sql, array($product["scan_num"], $args['depot_out']->depot_out_id, $args['location_info']->location_id, $db_product_sub["product_id"], $db_product_sub["color_id"], $db_product_sub["size_id"]));
	    $scan_num += $product["scan_num"];
	}

	$box_up_sql = "UPDATE " . $this->db->dbprefix('box') . " SET scan_number = scan_number + ? WHERE box_id = ? ";
	$this->db->query($box_up_sql, array($scan_num, $box["box_id"]));

	$depot_out_up_sql = "UPDATE " . $this->db->dbprefix('depot_out_main') . " SET depot_out_finished_number = depot_out_finished_number + ? WHERE depot_out_code = ? ";
	$this->db->query($depot_out_up_sql, array($scan_num, $args['depot_out']->depot_out_code));
	$this->db->trans_commit();
    }

    public function query_box_main($filter) {
	$this->db_r
		->select("main.*,scan.admin_name as scan_name,shelve.admin_name as shelve_name")
		->from("ty_box AS main")
		->join('ty_admin_info AS scan', 'main.scan_id=scan.admin_id', 'left')
		->join('ty_admin_info AS shelve', 'main.scan_id=shelve.admin_id', 'left')
		->where($filter);
	$query = $this->db_r->get();
	return $query->result();
    }

    public function quer_box_sub($filter) {
	$this->db_r
		->select("sub.product_id,sub.color_id,sub.size_id,SUM(sub.scan_number) AS finished_scan_number,br.brand_name,
		     color_name, size_name,pi.product_sn,pi.product_name,pi.provider_productcode,ps.provider_barcode,dos.expire_date,dos.production_batch")
		->from("box_sub AS sub")
		->join('product_info AS pi', 'sub.product_id=pi.product_id', 'left')
		->join('product_color AS pc', 'pc.color_id = sub.color_id', 'left')
		->join('product_size AS psize', 'psize.size_id = sub.size_id', 'left')
		->join('product_sub AS ps', 'sub.product_id=ps.product_id and sub.color_id=ps.color_id and sub.size_id=ps.size_id', 'left')
		->join('product_brand AS br', 'br.brand_id=pi.brand_id', 'left')
		->join('depot_out_sub AS dos', 'dos.product_id=pi.product_id', 'left')
		->where($filter)
		->group_by('sub.product_id,sub.color_id,sub.size_id');
	$query = $this->db_r->get();
	return $query->result();
    }

    public function get_doc_content($filter) {
	if ($filter["doc_type"] == 1 || $filter["doc_type"] == 2 || $filter["doc_type"] == 3) {
	    return $this->filter_depot_out(array("depot_out_code" => $filter["doc_code"], "depot_out_type" => $filter["depot_type"]));
	}
	return null;
    }

    public function delete_box($box_id) {
	$this->db->delete('box', array('box_id' => $box_id));
    }

    public function delete_box_sub_filter($filter) {
	$this->db->delete('box_sub', $filter);
    }

    public function delete_box_sub($box_id) {
	$this->db->delete('box_sub', array('box_id' => $box_id));
    }

    public function delete_box_leaf($box_id) {
	$this->db->delete('box_leaf', array('box_id' => $box_id));
    }

    public function delete_box_leaf_filter($filter) {
	$this->db->delete('box_leaf', $filter);
    }

    public function update_box($data, $filter) {
	$this->db->update('box', $data, $filter);
    }

    public function update_box_sub($data, $filter) {
	$this->db->update('box_sub', $data, $filter);
    }

    public function update_box_leaf($data, $filter) {
	$this->db->update('box_leaf', $data, $filter);
    }

    public function decrease_doc_sub_finished_number($doc_id, $doc_type, $box_id) {

	$query = $this->db->get_where('box_leaf', array("box_id" => $box_id));
	$leaf_list = $query->result();
	foreach ($leaf_list as $leaf) {
	    if ($doc_type == 1) {
		$this->decrease_depot_out_sub_finished_number($doc_id, $leaf->product_id, $leaf->color_id, $leaf->size_id, $leaf->location_id, $leaf->num);
	    }
	}
    }

    public function decrease_depot_out_sub_finished_number($doc_id, $product_id, $color_id, $size_id, $location_id, $num) {
	$decrease_sql = "UPDATE " . $this->db->dbprefix('depot_out_sub') . " SET product_finished_number = product_finished_number - $num ";
	$decrease_sql .= " WHERE depot_out_id = ? AND product_id=? AND color_id=? AND size_id=? AND location_id=?";
	$this->db->query($decrease_sql, array($doc_id, $product_id, $color_id, $size_id, $location_id));
    }

    public function decrease_doc_finished_number($doc_code, $doc_type, $number) {
	if ($doc_type == 1) {
	    $decrease_sql = "UPDATE " . $this->db->dbprefix('depot_out_main') . " SET depot_out_finished_number = depot_out_finished_number - $number WHERE depot_out_code = '$doc_code' ";
	    $this->db->query($decrease_sql);
	}
    }

    public function do_check($doc_type, $doc_code, $box_code, $num, $admin_id, $product_array) {
	$time = date('Y-m-d H:i:s');
	$filter = array();
	$this->db->trans_begin();
	$filter["doc_type"] = $doc_type;
	$filter["doc_code"] = $doc_code;
	$filter["box_code"] = $box_code;
	$box = $this->filter_box($filter);
	if (empty($box))
	    sys_msg("没有对应的箱号", 1);
	$box_sub_list = $this->filter_box_sub($filter);
	$box_sub_map = index_array($box_sub_list, "box_sub_id");
	foreach ($product_array as $product) {
	    $box_sub_id = $product["box_sub_id"];
	    $sub_num = $product["num"];
	    $box_sub = $box_sub_map[$box_sub_id];
	    if (empty($box_sub))
		continue;
	    $update = array();
	    $update["shelve_endtime"] = $time;
	    $update["shelve_id"] = $admin_id;
	    $shelve_number = intval($box_sub->shelve_number);
	    if ($shelve_number <= 0) {
		$update["shelve_starttime"] = $time;
		$shelve_number = 0;
	    }
	    $update["shelve_number"] = $shelve_number += $sub_num;
	    $this->update_box_sub($update, array("box_sub_id" => $box_sub_id));
	}
	$update = array();
	$update["shelve_endtime"] = $time;
	$update["shelve_id"] = $admin_id;
	$shelve_number = intval($box->shelve_number);
	if ($shelve_number <= 0) {
	    $update["shelve_starttime"] = $time;
	    $shelve_number = 0;
	}
	$update["shelve_number"] = $shelve_number += $num;
	$this->update_box($update, array("box_id" => $box->box_id));
	$this->db->trans_commit();
    }

    //拣货对应的Depot记录
    public function onsale_finish($args) {
	$this->db->trans_begin();
	$query = $this->db->get_where('box', array("box_code" => $args['box_code']), 1);
	$box = $query->row_array();
	$doc_code = $args['depot_in']->depot_in_code;
	$doc_type= $args['doc_type'];
	if($doc_type == 11){
	    $doc_type= 2;
	    $doc_code = $args['depot_in']->order_sn;
	}
	if (empty($box)) {
	    sys_msg("箱子不存在", 1, array(), FALSE);
	    return;
	} elseif ($box["doc_type"] != $doc_type || $box["doc_code"] !=$doc_code) {
	    sys_msg("不是此单据对应箱子，不允许打开", 1, array(), FALSE);
	    return;
	}
	$scan_num = 0;
	$all_shelve_number = 0;
	foreach ($args["product_array"] as $product) {
	    if ($product["scan_num"] == 0)
		continue;
	    $sql_q_box_sub = "SELECT b.* FROM ty_box_sub b 
		INNER JOIN ty_product_sub s ON b.product_id=s.product_id AND b.color_id=s.color_id AND b.size_id=s.size_id 
		WHERE s.provider_barcode =? AND b.box_id=?";
	    $query = $this->db->query($sql_q_box_sub, array($product["provider_barcode"],$box["box_id"]));
	    $box_sub = $query->row_array();
	    if (empty($box_sub)) {
		sys_msg("该箱子不存在对应商品【" . $product["provider_barcode"] . "】", 1, array(), FALSE);
	    }
	    $scan_number = intval($box_sub["scan_number"]);
	    $shelve_number = intval($box_sub["shelve_number"]);
	    $scan_num = intval($product["scan_num"]);
	    if ($scan_number - $shelve_number - $scan_num < 0) {
		sys_msg("该箱子还需上架对应商品【" . $product["provider_barcode"] . "】" 
			. $scan_number - $shelve_number . "件，目前上架" . $scan_num . "件", 1, array(), FALSE);
	    }
	    $box_sub_update = array();
	    $box_sub_update["shelve_number"] = $scan_num + $shelve_number;
	    if($shelve_number == 0){
		$box_sub_update["shelve_starttime"] = $args['time'];	
	    }
	    $box_sub_update["shelve_endtime"] = $args['time'];
	    $box_sub_update["shelve_id"] = $args['admin_id'];
	    $this->db->update('box_sub', $box_sub_update, array('box_sub_id' => $box_sub["box_sub_id"]));
	    $d_o_s_filter = array();
	    $d_o_s_filter["location_id"] = $args["location_info"]->location_id;
	    $d_o_s_filter["product_id"] = $box_sub["product_id"];
	    $d_o_s_filter["color_id"] = $box_sub["color_id"];
	    $d_o_s_filter["size_id"] = $box_sub["size_id"];
	    $d_o_s_filter["depot_in_id"] = $args['depot_in']->depot_in_id;
	    $query = $this->db->get_where('depot_in_sub', $d_o_s_filter, 1);
	    $db_d_o_s = $query->row_array();
	    $depot_in_sub_id = 0;
	    if (empty($db_d_o_s)) {
		$query_depot_out = array();
		$query_depot_out["depot_out_id"]  = $args['depot_in']->order_id;
		$query_depot_out["product_id"] = $box_sub["product_id"];
		$query_depot_out["color_id"] = $box_sub["color_id"];
		$query_depot_out["size_id"] = $box_sub["size_id"];
		$query = $this->db->get_where('depot_out_sub', $query_depot_out, 1);
		$db_out_sub = $query->row();
		if(empty($db_out_sub)){
		    sys_msg("对应调拨出库单不存在此商品信息，调试信息，
			product_id【".$box_sub["product_id"]."】，color_id【".$box_sub["color_id"]."】，size_id【".$box_sub["size_id"]."】",1);
		}
		$depot_in_sub = array();
		$depot_in_sub["depot_in_id"] = $args['depot_in']->depot_in_id;
		$depot_in_sub["product_id"] = $box_sub["product_id"];
		$depot_in_sub["product_name"] = $db_out_sub->product_name;
		$depot_in_sub["color_id"] = $box_sub["color_id"];
		$depot_in_sub["size_id"] = $box_sub["size_id"];
		$depot_in_sub["depot_id"] = $args['depot_in']->depot_depot_id;
		$depot_in_sub["location_id"] = $args["location_info"]->location_id;
		$depot_in_sub["shop_price"] = $db_out_sub->shop_price;
		$depot_in_sub["product_number"] = $scan_num;
		$depot_in_sub["create_admin"] = $args['admin_id'];
		$depot_in_sub["create_date"] = $args['time'];
		$depot_in_sub["batch_id"] = $db_out_sub->batch_id;
		$depot_in_sub["product_finished_number"] = $scan_num;
		$this->db->insert('depot_in_sub', $depot_in_sub);
		$depot_in_sub_id = $this->db->insert_id();
	    } else {
		$depot_in_sub_id = $db_d_o_s["depot_in_sub_id"];
		$depot_in_sub_up = array();
		$depot_in_sub_up["product_number"] = intval($db_d_o_s["product_number"])+$scan_num;
		$depot_in_sub_up["product_finished_number"] = $depot_in_sub_up["product_number"];
		$this->db->update('depot_in_sub',$depot_in_sub_up, $d_o_s_filter);
	    }
	    //添加入库单明细记录
	    $this->insert_transaction_info($depot_in_sub_id,$scan_num,$this->admin_id);
	    /*
	    //同步更新details 表
	    $box_detail_filter = array();
	    $box_detail_filter["box_id"] = $box["box_id"];
	    $box_detail_filter["type"] = "onsale";
	    $box_detail_filter["location_id"] = $args["location_info"]->location_id;
	    $box_detail_filter["product_id"] = $box_sub["product_id"];
	    $box_detail_filter["color_id"] = $box_sub["color_id"];
	    $box_detail_filter["size_id"] = $box_sub["size_id"];
	    $query = $this->db->get_where('box_leaf', $box_detail_filter, 1);
	    $box_detail = $query->row_array();
	    if (empty($box_detail)) {
		$box_detail = array();
		$box_detail["box_id"] = $box["box_id"];
		$box_detail["type"] = "onsale";
		$box_detail["box_code"] = $args['box_code'];
		$box_detail["location_id"] = $args["location_info"]->location_id;
		$box_detail["product_id"] = $box_sub["product_id"];
		$box_detail["color_id"] = $box_sub["color_id"];
		$box_detail["size_id"] = $box_sub["size_id"];
		$box_detail["num"] = $product["scan_num"];
		$this->db->insert('box_leaf', $box_detail);
	    } else {
		$box_detail_update = array();
		$box_detail_update["num"] = intval($product["scan_num"]) + intval($box_detail["num"]);
		$this->db->update('box_leaf', $box_detail_update, array('id' => $box_detail["id"]));
	    }*/
	    $all_shelve_number += $scan_num;
	}
	$box_query = array("box_id"=>$box["box_id"]);
	 $box_up = array();
	if(empty($box["shelve_number"])|| $shelve_number == 0){
	   
	    $box_up["shelve_number"] = $all_shelve_number;
	    $box_up["shelve_starttime"] = $args['time'];	
	    $box_up["shelve_endtime"] = $args['time'];
	    $box_up["shelve_id"] = $args['admin_id'];
	    $this->db->update('box',$box_up, $box_query);
	}else{
	    $box_up["shelve_number"] = $shelve_number + $all_shelve_number;
	    $box_up["shelve_endtime"] = $args['time'];
	    $this->db->update('box',$box_up, $box_query);
	}
	$depot_in_up_sql = "UPDATE " . $this->db->dbprefix('depot_in_main') . " SET depot_in_finished_number = depot_in_finished_number + ? WHERE depot_in_code = ? ";
	$this->db->query($depot_in_up_sql, array($all_shelve_number, $args['depot_in']->depot_in_code));
	$this->db->trans_commit();
    }
    public function insert_transaction_info($sub_id,$product_num,$admin_id) {
		//添加入库单明细记录
		if (empty($sub_id)) {
			return false;
		}
		$sql = "INSERT INTO ".$this->db->dbprefix('transaction_info')."(trans_type,trans_status,trans_sn,product_id,color_id,size_id,product_number," .
				"depot_id,location_id,create_admin,create_date,update_admin,update_date,cancel_admin,cancel_date,trans_direction,sub_id,batch_id,shop_price,consign_price,cost_price,consign_rate,product_cess,expire_date,production_batch) ".
				" SELECT ".TRANS_TYPE_DIRECT_IN.",".TRANS_STAT_AWAIT_IN.",b.depot_in_code,a.product_id,a.color_id,size_id,'".$product_num."',".
				"a.depot_id,a.location_id,'".$admin_id."','".date('Y-m-d H:i:s')."',0,'0000-00-00',0,'0000-00-00',1,a.depot_in_sub_id,a.batch_id,a.shop_price,pc.consign_price,pc.cost_price,pc.consign_rate,pc.product_cess,a.expire_date,a.production_batch" .
				" FROM ".$this->db->dbprefix('depot_in_sub')." a" .
				" LEFT JOIN ".$this->db->dbprefix('product_cost')." pc ON a.product_id = pc.product_id AND a.batch_id = pc.batch_id" .
				" LEFT JOIN ".$this->db->dbprefix('depot_in_main')." b ON b.depot_in_id = a.depot_in_id WHERE a.depot_in_sub_id = '".$sub_id."' ";
		$this->db->query($sql);
		return $this->db->insert_id();
	}
	
	public function query_location($doc_code,$location_id = 0){
	    $sql = "SELECT s.location_id as location_id,l.location_name,s.product_number,s.product_finished_number FROM ".$this->db->dbprefix('depot_out_sub')." s 
		INNER JOIN ".$this->db->dbprefix('depot_out_main')." m ON s.depot_out_id = m.depot_out_id
		INNER JOIN ".$this->db->dbprefix('location_info')." l ON s.location_id = l.location_id ";
	    $where = " WHERE m.depot_out_code ='$doc_code' ";
	    $order_by =" GROUP BY s.location_id ORDER BY s.location_id ";
	    if(empty($location_id)){
		$where .=" AND s.product_number <> s.product_finished_number";
		$query = $this->db->query($sql . $where . $order_by);
		$row = $query->row();
		$query->free_result();
		if(empty($row)){
		    return "";
		}
		return $row->location_name;
	    }else{
		$query = $this->db->query($sql . $where . $order_by);
		$list = $query->result();
		$query->free_result();
		$flag = FALSE;
		foreach($list as $item){
		    if($flag == TRUE && $item->product_number != $item->product_finished_number){
			return $item->location_name;
		    }
		    if($location_id == $item->location_id){
			$flag = TRUE;
		    }
		}
		return "";
	    }
	}
}

?>
