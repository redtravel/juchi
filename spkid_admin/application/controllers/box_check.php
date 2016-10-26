<?php

#doc
#	classname:	box_check
#	scope:		PUBLIC
#  前置功能参考 pick_out.php
#   复核功能。前置条件，已经扫描下架入箱
#/doc

class box_check extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id)
            redirect('index/login');
        $this->load->model('pick_out_model');
	$this->load->helper('box');
    }

    public function index($doc_type, $doc_code) {
        auth(array('box_check'));
        $data = array();
        $data['doc_type'] = $doc_type;
        $data['doc_code'] = $doc_code;
        $this->vali_doc_type($doc_type);
	$data['depot_type'] = get_doc_type_out_id($doc_type);
        $data['biz_content'] = $this->pick_out_model->get_doc_content($data);
        $data['all_scan_number'] = $this->pick_out_model->query_all_scan_number($doc_type, $doc_code);
        $data['all_check_number'] = $this->pick_out_model->query_all_shelve_number($doc_type, $doc_code);
        $data['box_count'] = $this->pick_out_model->query_box_count($doc_type, $doc_code);
        $this->load->view('box/check', $data);
    }

    public function get_box_detail($doc_type, $doc_code, $box_code) {
        auth(array('box_check'));
        $this->vali_doc_type($doc_type);
        if (empty($doc_code)) {
            echo json_encode(array("err" => 1, "msg" => "无效的业务单号"));
            return;
        }
        if (empty($box_code)) {
            echo json_encode(array("err" => 1, "msg" => "请扫描箱号"));
            return;
        }
        $box_details = $this->pick_out_model->query_box_details($doc_type, $doc_code, $box_code);
        if (empty($box_details) || count($box_details) < 1) {
            echo json_encode(array("err" => 1, "msg" => "没有此箱子的下架记录，请确认箱号！"));
            return;
        }
        echo json_encode(array("err" => 0, "list" => $box_details));
    }

    public function do_check() {
        auth(array('box_check'));
        $box_code = $this->input->post('box_code');
        $doc_code = $this->input->post('doc_code');
        $doc_type = $this->input->post('doc_type');
        $provider_barcode_array = $this->input->post('provider_barcode_array');
        $box_sub_id_array = $this->input->post('box_sub_id_array');
        $number_array = $this->input->post('number_array');
        $this->vali_doc_type($doc_type);
        if (empty($doc_code)) {
            sys_msg('请传递业务单据号！', 1);
        }
        if (empty($box_code)) {
            sys_msg('箱子记录不存在！', 1);
        }
        if (empty($box_sub_id_array) || count($box_sub_id_array) < 1) {
            sys_msg('复核记录不存在！', 1);
        }
        $product_array = array();
        $num = 0;
        foreach ($box_sub_id_array as $i => $box_sub_id) {
            $product = array();
            $product["product_code"] = $provider_barcode_array[$i];
            $product["box_sub_id"] = $box_sub_id;
            $product["num"] = $number_array[$i];
            $num += $product["num"];
            $product_array[] = $product;
        }
        $this->pick_out_model->do_check($doc_type, $doc_code, $box_code, $num, $this->admin_id, $product_array);
        echo json_encode(array("err" => 0));
    }

    public function check_details($doc_type, $doc_code) {
        auth(array('box_check'));
        $data['doc_type'] = $doc_type;
        $data['doc_code'] = $doc_code;
        $this->vali_doc_type($doc_type);
	$data['depot_type'] = get_doc_type_out_id($doc_type);
        $data['biz_content'] = $this->pick_out_model->get_doc_content($data);
        $data['all_scan_number'] = $this->pick_out_model->query_all_scan_number($doc_type, $doc_code);
        $data['all_check_number'] = $this->pick_out_model->query_all_shelve_number($doc_type, $doc_code);
        $data['box_count'] = $this->pick_out_model->query_box_count($doc_type, $doc_code);
        //
        $list = $this->pick_out_model->query_box_main(array("doc_type" => $doc_type, "doc_code" => $doc_code));
        if (empty($list) || count($list) < 1) {
            sys_msg("没有此单据的复核记录。", 1);
        }
        foreach ($list as $item) {
            $item->details = $this->pick_out_model->query_box_details($doc_type, $doc_code, $item->box_code);
        }
        $data["box_details"] = $list;
        $this->load->view('box/details', $data);
    }

    public function cancel_box_check($box_id) {
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

    /**
     * 修改下架商品数量。应用场景，当商品下架之后进行复核，复核时发现数量不对时进行数据修正。
     * 修改后的数量不允许小于复核数
     * 修改的数量为0时，直接删除对应记录
     * 不允许重复修订到相同的值
     * 修改出库下架商品数量时会自动匹配对应储位，随机删除或者修改储位的下架数量，直到减去对应的商品数量时停止。
     * 修订涉及对应表，（出库）
     * 装箱表： 主表（ty_box），子表（ty_box_sub），详情表（ty_box_leaf）
     * 业务表（修改完成数）： 出库单主表（ty_depot_out_main），出库单子表（ty_depot_out_sub）
     */
    public function eidt_pick_val() {
        auth(array('eidt_pick_val'));
        $sub_id = intval($this->input->post('sub_id'));
        $val = intval($this->input->post('val'));
        $sub_list = $this->pick_out_model->filter_box_sub(array("box_sub_id" => $sub_id));
        if (empty($sub_list)) {
            sys_msg("对应详情为空", 1);
        }
        $sub = $sub_list[0];
        if ($val < $sub->shelve_number) {
            sys_msg("修改后的下架数不允许小于复核数", 1);
        }
        if ($val == $sub->scan_number) {
            sys_msg("已经做完修改，请勿重复提交", 1);
        }
        $this->db->trans_begin();
        $box = $this->pick_out_model->filter_box(array("box_id" => $sub->box_id));
        $leaf_filter = array("box_id" => $sub->box_id, "product_id" => $sub->product_id, "color_id" => $sub->color_id, "size_id" => $sub->size_id);
        $leaf_list = $this->pick_out_model->filter_box_leaf($leaf_filter);
        if ($val == 0) {
            $box_scan_number = $box->scan_number - $sub->scan_number;
            $this->pick_out_model->update_box(array("scan_number" => $box_scan_number), array("box_id" => $sub->box_id));
            $this->pick_out_model->delete_box_sub_filter(array("box_sub_id" => $sub_id));
            $this->pick_out_model->delete_box_leaf_filter($leaf_filter);
            foreach ($leaf_list as $leaf) {
                if ($sub->doc_type == 1)
                    $this->pick_out_model->decrease_depot_out_sub_finished_number($sub->doc_id, $leaf->product_id, $leaf->color_id, $leaf->size_id, $leaf->location_id, $leaf->num);
            }
            $this->pick_out_model->decrease_doc_finished_number($sub->doc_code, $sub->doc_type, $sub->scan_number);
        }else {
            $box_scan_number = $box->scan_number - ($sub->scan_number - $val);
            $this->pick_out_model->update_box(array("scan_number" => $box_scan_number), array("box_id" => $sub->box_id));
            $this->pick_out_model->update_box_sub(array("scan_number" => $val), array("box_sub_id" => $sub_id));
            $temp = $sub->scan_number - $val;
            foreach ($leaf_list as $leaf) {
                $sub_scan_num = 0;
                if ($leaf->num - $temp > 0) {
                    $this->pick_out_model->update_box_leaf(array("num" => $leaf->num - $temp), array("id" => $leaf->id));
                    $sub_scan_num = $temp;
                    $temp = 0;
                    break;
                } else {
                    $temp = $temp - $leaf->num;
                    $this->pick_out_model->delete_box_leaf_filter(array("id" => $leaf->id));
                    $sub_scan_num = $leaf->num;
                }
                if ($sub->doc_type == 1)
                    $this->pick_out_model->decrease_depot_out_sub_finished_number($sub->doc_id, $leaf->product_id, $leaf->color_id, $leaf->size_id, $leaf->location_id, $sub_scan_num);
                if ($temp == 0)
                    break;
            }
            $this->pick_out_model->decrease_doc_finished_number($sub->doc_code, $sub->doc_type, $sub->scan_number - $val);
        }
        $this->db->trans_commit();
        echo json_encode(array("err" => 0));
    }

    private function vali_doc_type($doc_type) {
        if ($doc_type != 1 && $doc_type!=3) {
            sys_msg("不支持此业务类型", 1);
        }
    }

}

###