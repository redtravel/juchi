<?php

/**
 * Description of box_onsale
 * 前置功能参考 pick_out.php
 *   上架功能。前置条件，已经扫描下架入箱
 * @author mickey
 */
class box_onsale extends CI_Controller {

    function __construct() {
	parent::__construct();
	$this->admin_id = $this->session->userdata('admin_id');
	if (!$this->admin_id)
	    redirect('index/login');
	$this->load->model('pick_out_model');
	$this->load->model('location_model');
	$this->load->helper("box");
    }

    public function index($doc_type) {
	auth(array('allot_in'));
	$data = array();
	$data['doc_type'] = $doc_type;
	$data['is_finished'] = '-1';
	$data['cur_menu']='in';
	vali_doc_type_in($doc_type);
	$this->load->view('box/onsale_rf', $data);
    }

    public function check_doc_code() {
	$doc_code = trim($this->input->get_post('doc_code'));
	$doc_type = intval($this->input->get_post('doc_type'));
	vali_doc_type_in($doc_type);
	echo json_encode($this->check_depot_code($doc_code, $doc_type));
    }

    public function check_box_code() {
	$data = array();
	$doc_code = trim($this->input->get_post('doc_code'));
	$doc_type = trim($this->input->get_post('doc_type'));
	$box_code = trim($this->input->get_post('box_code'));
	vali_doc_type_in($doc_type);
	$db_box = $this->pick_out_model->filter_box(array("box_code" => $box_code));
	if (empty($db_box)) {
	    $data["err"] = 1;
	    $data["msg"] = "箱子不存在";
	    echo json_encode($data);
	    return;
	}
	$doc_code = $this->get_doc_code($doc_type,$doc_code);
	$doc_type = $this->get_doc_type($doc_type);
	if ($db_box->doc_type != $doc_type || $db_box->doc_code != $doc_code) {
	    $data["err"] = 1;
	    $data["msg"] = "不是此业务单据的箱子，不允许打开";
	    echo json_encode($data);
	    return;
	}
	$data["err"] = 0;
	$data['number'] = $db_box->scan_number;
	echo json_encode($data);
	return;
    }

    public function check_location_code() {
	$doc_code = trim($this->input->get_post('doc_code'));
	$doc_type = intval($this->input->get_post('doc_type'));
	$location_code = trim($this->input->get_post('location_code'));
	vali_doc_type_in($doc_type);
	$depot_in = $this->pick_out_model->filter_depot_in(array("depot_in_code" => $doc_code));
	$location = $this->location_model->get_location(array("location_name"=>$location_code,"is_use"=>1,"depot_id"=>$depot_in->depot_depot_id));
	if(empty($location)){
	    echo json_encode(array("err"=>1,"msg"=>"该储位不合法"));
	    return;
	}
	echo json_encode(array("err"=>0,"msg"=>""));
    }

    /**
     * 上架商品
     * 同时更新表 
     * ty_depot_in_main 完成数量
     * ty_depot_in_sub 完成数量
     * ty_box 上架数量
     * ty_box_sub 上架数量
     * ty_box_leaf ? 是否更新
     * 
     */
    public function onsale() {
	auth(array('allot_in'));
	$this->load->model('depot_model');
	$this->load->model('depotio_model');
	$data = array();
	$data['cur_menu'] = 'out';
	$doc_code = trim($this->input->post('doc_code'));
	$doc_type = intval($this->input->post('doc_type'));
	$box_code = trim($this->input->post('box_code'));
	$location_code = trim($this->input->post('depot_code'));
	$data['doc_type'] = $doc_type;
	$data['shelve_num'] = 0;
	$data['unshelve_num'] = 0;
	vali_doc_type_in($doc_type);
	if (empty($doc_code) || empty($box_code) || empty($location_code)) {
	    $data['doc_code'] = "";
	    $data['box_code'] = "";
	    $data['msg'] = "单据号、箱号、储位 不能为空";
	    $data['is_finished'] = -1;
	    $this->load->view('box/onsale_rf', $data);
	    return;
	}
	$depot_in = $this->pick_out_model->filter_depot_in(array("depot_in_code" => $doc_code, "depot_in_type"=>get_doc_type_in_id($doc_type)));
	$location_info = $this->depot_model->filter_location(array("location_name" => $location_code));
	if (empty($depot_in) || $depot_in == null || empty($location_info) || $location_info == null) {
	    $data['doc_code'] = "";
	    $data['box_code'] = "";
	    $data['msg'] = "单据、储位 不能存在";
	    $data['is_finished'] = -1;
	    $this->load->view('box/onsale_rf', $data);
	    return;
	}
	$goods_code = $this->input->post('goods_code');
	$goods_array = explode(",",$goods_code);
	if (empty($goods_code) || empty($goods_array)) sys_msg('商品不能为空!', 1);
	$time = date('Y-m-d H:i:s');
	$product_array = array();
	foreach ($goods_array as $goods){
	    $goods_item = explode(":",$goods);
	    if(empty($goods_item))
		continue;
	    $goods_code = $goods_item[0];
	    $goods_num = $goods_item[1];
	    $product_array [] = array("provider_barcode" => $goods_code, "scan_num" => $goods_num);
	}
	// 更新商品拣货明细
	$content = array(
	    'depot_in' => $depot_in, 
	    'doc_type'=>$doc_type,
	    'box_code' => $box_code, 
	    'location_info' => $location_info, 
	    'product_array' => $product_array, 
	    'admin_id' => $this->admin_id, 
	    'time' => $time
	 );
	$this->pick_out_model->onsale_finish($content);
	$data['doc_code'] = $doc_code;
	$data['box_code'] = $box_code;
	$reload_depot_in = $this->pick_out_model->filter_depot_in(array("depot_in_code" => $doc_code));
	$data['is_finished'] = 0;
	if ($reload_depot_in->depot_in_number == $reload_depot_in->depot_in_finished_number) {
	    //入库审核
            $this->depotio_model->check_in($reload_depot_in, $this->admin_id);
	    $data['is_finished'] = 1;
	}
	if($data['is_finished'] == 0){
	    $db_box = $this->pick_out_model->filter_box(array("box_code" => $box_code));
	    $data['shelve_num'] = $db_box->shelve_number;
	    $data['unshelve_num'] = $db_box->scan_number - $db_box->shelve_number;
	}
	$this->load->view('box/onsale_rf', $data);
    }

    public function onsale_details($doc_type, $doc_code) {
	auth(array('allot_in'));
        $data['doc_type'] = $doc_type;
        $data['doc_code'] = $doc_code;
        vali_doc_type_in($doc_type);
        $data['biz_content'] = $this->pick_out_model->get_doc_content($data);
        $data['all_scan_number'] = $this->pick_out_model->query_all_scan_number($doc_type, $doc_code);
        $data['all_check_number'] = $this->pick_out_model->query_all_shelve_number($doc_type, $doc_code);
        $data['box_count'] = $this->pick_out_model->query_box_count($doc_type, $doc_code);
        //
        $list = $this->pick_out_model->query_box_main(array("doc_type" => $doc_type, "doc_code" => $doc_code));
        if (empty($list) || count($list) < 1) {
            sys_msg("没有此单据的记录。", 1);
        }
        foreach ($list as $item) {
            $item->details = $this->pick_out_model->query_box_details($doc_type, $doc_code, $item->box_code);
        }
        $data["box_details"] = $list;
        $this->load->view('box/details', $data);
    }

    public function cancel_box_onsale($box_id) {//TODO 暂不使用
	if (empty($box_id)) {
            echo json_encode(array("err" => 1, "msg" => "没有对应箱号，请联系管理员！"));
            return;
        }
        $box = $this->pick_out_model->filter_box(array("box_id" => $box_id));
        if (empty($box)) {
            echo json_encode(array("err" => 1, "msg" => "没有对应箱子记录，请确认是否已经取消。"));
            return;
        }
        $this->load->model('purchase_log_model');
        $this->db->trans_begin();
        $this->pick_out_model->update_box(array("shelve_number" => 0, "shelve_id" => 0, "shelve_starttime" => "", "shelve_endtime" => ""), array("box_id" => $box_id));
        $this->pick_out_model->update_box_sub(array("shelve_number" => 0, "shelve_id" => 0, "shelve_starttime" => "", "shelve_endtime" => ""), array("box_id" => $box_id));
        $desc_content = $this->session->userdata('admin_name') . "于" . $now_time . "取消箱子" . $box->box_code . "的出库下架复核记录";
        $this->purchase_log_model->insert(array("related_id" => $box_id, "related_type" => 5, "desc_content" => $desc_content, "create_admin" => $this->admin_id, "create_date" => $now_time));
        $this->db->trans_commit();
        echo json_encode(array("err" => 0));
        return;
    }

    public function cancel_location_onsale() {
	//TODO 暂不使用
    }

    public function edit_onsale_num() {
	//TODO 暂不使用
    }

    //~=================== doc_type: full ============================//

    /**
     * 验证单据编码是否正确
     * @param type $depot_code
     * @param type $doc_type_id 出库单类型ID
     * @return type
     */
    function check_depot_code($depot_code, $doc_type) {
	$doc_type_id = get_doc_type_in_id($doc_type);
	$data = array();
	$depot_in = $this->pick_out_model->filter_depot_in(array("depot_in_code" => $depot_code));
	if (empty($depot_in) || $depot_in == null) {
	    $data["err"] = 1;
	    $data["msg"] = "没有对应的出库单号";
	    return $data;
	} else {
	    if ($depot_in->is_deleted == 1) {
		$data["err"] = 1;
		$data["msg"] = "该出库单已经删除";
		return $data;
	    }
	    if ($depot_in->depot_in_number == $depot_in->depot_in_finished_number) {
		$data["err"] = 1;
		$data["msg"] = "该出库单已经全部完成";
		return $data;
	    }
	    if ($doc_type_id != $depot_in->depot_in_type) {
		$data["err"] = 1;
		$data["msg"] = "该出库单不属于此业务";
		return $data;
	    }
	    $data["err"] = 0;
	    $data["number"] = intval($depot_in->depot_in_number);
	    $data["finished_number"] = intval($depot_in->depot_in_finished_number);
	    return $data;
	}
    }
    
    private function get_doc_code($doc_type,$doc_code){
	if($doc_type == 11){
	    $depot_in = $this->pick_out_model->filter_depot_in(array("depot_in_code" => $doc_code));
	    if (empty($depot_in) || $depot_in == null) {
		return null;
	    }
	    return $depot_in->order_sn;
	}  else {
	    return $doc_code;
	}
    }
    
    private function get_doc_type($doc_type){
	if($doc_type == 11){
	    return 2;
	}
    }

}

?>
