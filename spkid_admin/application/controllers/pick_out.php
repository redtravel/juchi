<?php

/**
 * Description of pick_out
 *
 * @author mickey
 */
class pick_out extends CI_Controller {

    function __construct() {
	parent::__construct();
	$this->admin_id = $this->session->userdata('admin_id');
	if (!$this->admin_id)
	    sys_msg('请先登录', 1, array(array('href' => 'index/login', 'text' => '立即登录')));
	$this->time = date('Y-m-d H:i:s');
	$this->load->model('pick_out_model');
	$this->load->model('depot_model');
	$this->load->helper("box");
	$this->pick_type = array('order' => '订单不代收', 'ordercod' => '订单代收', 'change' => '换货单');
    }

    // 出库拣货
    public function index($doc_type) {
	auth(array('pick_scan_rf'));
	$data = array();
	$data['cur_menu'] = 'out';
	$data['doc_type'] = $doc_type;
	$this->load->view('pick_out/pick_out_rf', $data);
    }

    //检查单据是否存在，返回应该拣货多少件商品，已经拣多少
    public function check_doc_code() {
	$doc_code = trim($this->input->get_post('doc_code'));
	$doc_type = intval($this->input->get_post('doc_type'));
	vali_doc_type_out($doc_type);
	echo json_encode($this->check_depot_out_code($doc_code, get_doc_type_out_id($doc_type)));
    }

    //检查箱号是否存在
    public function check_box_code() {
	$data = array();
	$doc_code = trim($this->input->get_post('doc_code'));
	$doc_type = trim($this->input->get_post('doc_type'));
	$box_code = trim($this->input->get_post('box_code'));
	vali_doc_type_out($doc_type);
	$db_box = $this->pick_out_model->filter_box(array("box_code" => $box_code));
	$number = 0;
	if (!empty($db_box)) {
	    if ($db_box->doc_type != $doc_type || $db_box->doc_code != $doc_code) {
		$data["err"] = 1;
		$data["msg"] = "不是此业务单据的箱子，不允许打开";
		echo json_encode($data);
		return;
	    }
	    $number = $db_box->scan_number;
	}
	$data["err"] = 0;
	$data['number'] = $number;
	echo json_encode($data);
	return;
    }

    //检查出库单中某个储位是否存在，返回应该出多少件商品，已经出库多少
    public function check_location_code() {
	$doc_code = trim($this->input->get_post('doc_code'));
	$doc_type = intval($this->input->get_post('doc_type'));
	$location_code = trim($this->input->get_post('location_code'));
	vali_doc_type_out($doc_type);
	$this->check_location($doc_code, $location_code, get_doc_type_out_id($doc_type));
    }

    //完成
    public function finish() {
	$data = array();
	$data['cur_menu'] = 'out';
	$doc_code = trim($this->input->post('doc_code'));
	$doc_type = intval($this->input->post('doc_type'));
	$box_code = trim($this->input->post('box_code'));
	$location_code = trim($this->input->post('location_code'));
	$data['doc_type'] = $doc_type;
	vali_doc_type_out($doc_type);
	if (empty($doc_code) || empty($box_code) || empty($location_code)) {
	    $data['doc_code'] = "";
	    $data['finished'] = FALSE;
	    $this->load->view('pick_out/pick_out_rf', $data);
	    return;
	}
	$depot_out = $this->pick_out_model->filter_depot_out(array("depot_out_code" => $doc_code, "depot_out_type"=>get_doc_type_out_id($doc_type)));
	$location_info = $this->depot_model->filter_location(array("location_name" => $location_code));
	if (empty($depot_out) || $depot_out == null || empty($location_info) || $location_info == null) {
	    $data['doc_code'] = "";
	    $data['finished'] = FALSE;
	    $this->load->view('pick_out/pick_out_rf', $data);
	    return;
	}
	$sub_id = $this->input->post('sub_id');
	$scan_num = $this->input->post('scan_num');
	$time = date('Y-m-d H:i:s');
	$product_array = array();
	for ($i = 0; isset($sub_id[$i]); $i++) {
	    $product_array [] = array("sub_id" => $sub_id[$i], "scan_num" => $scan_num[$i]);
	}
	// 更新商品拣货明细
	$content = array(
	    'depot_out' => $depot_out, 
	    'doc_type'=>$doc_type,
	    'box_code' => $box_code, 
	    'location_info' => $location_info, 
	    'product_array' => $product_array, 
	    'admin_id' => $this->admin_id, 
	    'time' => $time
	 );
	$this->pick_out_model->pick_out_finish($content);
	$data['doc_code'] = $doc_code;
	$data['box_code'] = $box_code;
	$reload_depot_out = $this->pick_out_model->filter_depot_out(array("depot_out_code" => $doc_code, "depot_out_type"=>get_doc_type_out_id($doc_type)));
	$data['finished'] = FALSE;
	if ($reload_depot_out->depot_out_number == $reload_depot_out->depot_out_finished_number) {
	    $data['finished'] = TRUE;
	}
	$data["location_name"] = $this->pick_out_model->query_location($doc_code,$location_info->location_id);
	$this->load->view('pick_out/pick_out_rf', $data);
    }

    public function pick_details($doc_type, $doc_code) {
	auth('depot_out_view_pick');
	$data = array();
	vali_doc_type_out($doc_type);
	$filter = array('doc_code' => $doc_code, "doc_type" => $doc_type, "depot_type" => get_doc_type_out_id($doc_type));
	$doc_content = $this->pick_out_model->get_doc_content($filter);
	if (empty($doc_content)) {
	    sys_msg("没有此业务对象");
	}
	$box_list = $this->pick_out_model->query_box_main(array('doc_code' => $doc_code, "doc_type" => $doc_type));
	if (empty($box_list)) {
	    sys_msg("没有装箱清单", 1);
	}
	$scan_num = 0;
	foreach ($box_list as $box) {
	    $scan_num += $box->scan_number;
	    $box->detail_list = $this->pick_out_model->quer_box_sub(array("box_id" => $box->box_id));
	}
	$data["doc_code"] = $doc_code;
	$data["doc_type"] = $doc_type;
	$data["doc_content"] = $doc_content;
	$data["content"] = $box_list;
	$data["scan_num"] = $scan_num;
	$data["box_count"] = count($box_list);
	$this->load->view('pick_out/box_detail', $data);
    }

    public function print_box_order($box_id) {
	$data = array();
	if (empty($box_id))
	    sys_msg("数据有误，请联系管理员", 1);
	$box = $this->pick_out_model->query_box_main(array("box_id" => $box_id));
	if (empty($box))
	    sys_msg("箱子不存在，请联系管理员", 1);
	$list = $this->pick_out_model->quer_box_sub(array("box_id" => $box_id));
	if (empty($list) || count($list) == 0)
	    sys_msg("此箱子没有对应记录");
	$data["list"] = $list;
	$data["box"] = $box[0];
	$this->load->view('pick_out/print_box_order', $data);
    }

    public function cancel_box_pick($box_id) {
	auth('cancel_depot_out_box_pick');
	$this->load->model('purchase_log_model');
	$now_time = date('Y-m-d H:i:s');
	if (empty($box_id)) {
	    echo json_encode(array("err" => 1, "msg" => '箱子记录不存在！'));
	    return;
	}
	$this->db->trans_begin();
	$box_list = $this->pick_out_model->query_box_main(array("box_id" => $box_id));
	if (empty($box_list) || count($box_list) < 1) {
	    echo json_encode(array("err" => 1, "msg" => '箱子记录不存在！'));
	    return;
	}
	$box = $box_list[0];
	vali_doc_type_out($box->doc_type);
	
	$depot_out = $this->pick_out_model->filter_depot_out(array("depot_out_id" => $box->doc_id));
	if(!empty($depot_out->audit_admin)){
	    echo json_encode(array("err" => 1, "msg" => '箱子关联单据已经审核,不允许取消下架。'));
	    return;
	}
	$this->pick_out_model->decrease_doc_sub_finished_number($box->doc_id, $box->doc_type, $box_id);
	$this->pick_out_model->delete_box($box_id);
	$this->pick_out_model->delete_box_sub($box_id);
	$this->pick_out_model->delete_box_leaf($box_id);
	$this->pick_out_model->decrease_doc_finished_number($box->doc_code, $box->doc_type, $box->scan_number);
	$desc_content = $this->session->userdata('admin_name') . "于" . $now_time . "取消箱子" . $box->box_code . "的出库扫描记录";
	$this->purchase_log_model->insert(array("related_id" => $box_id, "related_type" => 2, "desc_content" => $desc_content, "create_admin" => $this->admin_id, "create_date" => $now_time));
	$this->db->trans_commit();
	echo json_encode(array("err" => 0));
    }

    //~=================== doc_type: full ============================//
    /**
     * 验证出库单编码是否正确
     * @param type $depot_out_code
     * @param type $doc_type_id 出库单类型ID
     * @return type
     */
    function check_depot_out_code($depot_out_code, $doc_type_id) {
	$data = array();
	$depot_out = $this->pick_out_model->filter_depot_out(array("depot_out_code" => $depot_out_code));
	if (empty($depot_out) || $depot_out == null) {
	    $data["err"] = 1;
	    $data["msg"] = "没有对应的出库单号";
	    return $data;
	} else {
	    if ($depot_out->is_deleted == 1) {
		$data["err"] = 1;
		$data["msg"] = "该出库单已经删除";
		return $data;
	    }
	    if ($depot_out->depot_out_number == $depot_out->depot_out_finished_number) {
		$data["err"] = 1;
		$data["msg"] = "该出库单已经全部完成";
		return $data;
	    }
	    if ($doc_type_id != $depot_out->depot_out_type) {
		$data["err"] = 1;
		$data["msg"] = "该出库单不属于此业务";
		return $data;
	    }
	    $data["err"] = 0;
	    $data["number"] = intval($depot_out->depot_out_number);
	    $data["finished_number"] = intval($depot_out->depot_out_finished_number);
	    $data["location_name"] = $this->pick_out_model->query_location($depot_out_code);
	    return $data;
	}
    }

    /**
     * 验证储位是否存在-类型- 出库单
     * @param type $doc_code
     * @param type $location_code
     * @return type
     */
    function check_location($doc_code, $location_code, $doc_type_id) {
	$data = array();
	$product_number = $this->pick_out_model->check_depot_out_location($doc_code, $location_code, $doc_type_id);
	if ($product_number < 1) {
	    $data["err"] = 1;
	    $data["msg"] = "该储位【" . $location_code . "】没有需下架商品";
	    echo json_encode($data);
	    return;
	} else {
	    $scan_number = $this->pick_out_model->query_pick_depot_out_location($doc_code, $location_code);
	    if ($scan_number == $product_number) {
		$data["err"] = 0;
		$data["type"] = 1;
		$data["msg"] = "该储位【" . $location_code . "】已经全部拣货完成";
		echo json_encode($data);
		return;
	    }
	    $depot_out_list = $this->pick_out_model->query_depot_out_location($doc_code, $location_code);
	    $data["err"] = 0;
	    $data["type"] = 0;
	    $data["product_number"] = $product_number;
	    $data["scan_number"] = $scan_number;
	    $data["list"] = $depot_out_list;
	    echo json_encode($data);
	}
    }
    //~=================== doc_type: 1 type_code:ck001 ============================//
    //~=================== doc_type: 2 type_code:ck007 ============================//
}

?>
