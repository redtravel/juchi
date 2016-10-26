<?php

#doc
#	classname:	Purchase_virtual
#	scope:		PUBLIC
#
#/doc

class Purchase_consign extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id)
            redirect('index/login');
        $this->load->model('purchase_consign_model');
        $this->load->model('provider_model');
        $this->load->model('product_model');
    }

    public function index() {
        auth(array('purchase_consign_view', 'purchase_consign_export', 'purchase_consign_history'));
        $filter = $this->uri->uri_to_assoc(3);
        $data = array();
        $all_provider = $this->provider_model->all_provider();

        if (!$this->input->is_ajax_request()) {
            $data['full_page'] = TRUE;
            $data['end_time'] = date('Y-m-d H:i:s');
            $this->load->vars('all_provider', $all_provider);
            $this->load->view('purchase/purchase_consign_index', $data);
            return;
        }

        $filter['provider_id'] = intval($this->input->post('provider_id'));
        $filter['batch_id'] = intval($this->input->post('batch_id'));
        $start_time = trim($this->input->post('start_time'));
        $end_date = trim($this->input->post('end_date'));
        $end_time = trim($this->input->post('end_time'));

        if (!empty($filter['provider_id'])) {
            $this->load->vars('provider_id', $filter['provider_id']);
            foreach ($all_provider as $row) {
                if ($row->provider_id == $filter['provider_id']) {
                    $this->load->vars('provider_name', $row->provider_name);
                }
            }
        }
        if (!empty($filter['batch_id'])) {
            $this->load->model('purchase_batch_model');
            $batch_info = $this->purchase_batch_model->filter(array("batch_id" => $filter['batch_id']));
            $this->load->vars('batch_info', $batch_info);
        }
        if (!empty($start_time)) {
            $filter['start_time'] = $start_time;
            $this->load->vars('start_time', $filter['start_time']);
        }
        if (!empty($end_date) && !empty($end_time)) {
            $filter['end_time'] = $end_date . " " . $end_time;
            $this->load->vars('end_time', $filter['end_time']);
        }

        if (!empty($start_time) && !empty($end_date) && !empty($end_time)) {
            $begin = date($filter['start_time']);
            $end = date($filter['end_time']);
            if ($begin > $end) {
                $this->load->vars('error', '开始时间不能大于结束时间');
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('purchase/purchase_consign_index', $data, TRUE);
                echo json_encode($data);
                return;
            }
        }

        $data = $this->purchase_consign_model->get_consign_list($filter);
        $data['full_page'] = FALSE;
        $data['content'] = $this->load->view('purchase/purchase_consign_index', $data, TRUE);
        $data['error'] = 0;
        unset($data['list']);
        echo json_encode($data);
    }

    public function export() {
        auth('purchase_consign_export');
        $this->load->model('purchase_batch_model');
        $filter = $this->uri->uri_to_assoc(3);
        $filter['provider_id'] = intval($this->input->post('provider_id'));
        $provider = $this->provider_model->filter($filter);
        $filter['batch_id'] = intval($this->input->post('batch_id'));
        $batch_info = $this->purchase_batch_model->filter(array("batch_id" => $filter['batch_id']));
        $filter['start_time'] = trim($this->input->post('start_time'));
        $row = $this->purchase_consign_model->filter($filter);
        if (!empty($row)) {
            // 已导出过则直接导出文件
            //$file = "./public/download/purchase/consign_" . $row->id . ".xml";
            //header('Content-Disposition: attachment;filename="'.$file.'"');
            sys_msg('不要重复操作', 1);
            return;
        }
        $filter['end_time'] = trim($this->input->post('end_time'));

        $request_param = $this->input->post();

        $exlval = array();
        $val = array();
        foreach ($request_param as $key => $value) {
            if (strlen($key) > 4 && substr($key, 0, 4) == "num_") {
                $tmp_arr = explode('_', $key);
                $sub = $this->product_model->get_consign_single($tmp_arr[1], $tmp_arr[2], $tmp_arr[3]);
                $val['product_sn'] = $sub->product_sn; //商品款号
                $val['provider_code'] = $sub->provider_code; //供应商编码
                $val['provider_productcode'] = $sub->provider_productcode; //供应商货号
                $val['color_name'] = $sub->color_name; //颜色
                $val['color_sn'] = $sub->color_sn; //颜色编码
                $val['size_name'] = $sub->size_name; //尺寸
                $val['size_sn'] = $sub->size_sn; //尺寸编码
                $val['num'] = $value; //数量
                $exlval[] = $val;
            }
        }

        $filter['create_admin'] = $this->admin_id;
        $filter['create_date'] = date('Y-m-d H:i:s');

        $this->db->query('BEGIN');
        $rs = $this->purchase_consign_model->insert($filter);

        $title["batch_no"] = $batch_info->batch_code;
        $title["provider_code"] = $provider->provider_code;
        $title["provider_cooperation"] = $provider->provider_cooperation;
        $now = time();
        $next_time = $now + 3600 * 24 * 7;
        $title["now"] = date('Y-m-d', $now);
        $title["next"] = date('Y-m-d', $next_time);
        $title["desc"] = "";
        $info[] = array('product_sn' => '商品款号', 'provider_code' => '供应商编码', 'provider_productcode' => '供应商货号',
            'color_name' => '颜色', 'color_sn' => '颜色编码', 'size_name' => '尺寸', 'size_sn' => '尺寸编码',
            'num' => '数量');
        $head[] = array('batch_no' => '批次号', 'provider_code' => '供应商编码', 'provider_cooperation' => '合作方式ID', 'now' => '下单日期', 'next' => '预期交货日期', 'desc' => '备注');

        $this->load->helper('excel');
        $file_name = "consign_" . $rs;
        $this->load->helper('file');
        $excel_data = export_excel_xml($file_name, array($head, $title, $info, $exlval), true);
        if (write_file("./public/import/consign_purchase/" . $file_name . ".xml", $excel_data)) {
            $this->db->query('COMMIT');
            export_excel_xml($file_name, array($head, $title, $info, $exlval));
        } else {
            $this->db->query('ROLLBACK');
            sys_msg('文件写入出错', 1);
        }
    }

    public function create() {
        auth('purchase_consign_create');
        $now_time = time();
        $now = date('Y-m-d H:i:s', $now_time);
        $this->load->model('purchase_batch_model');
        $filter = $this->uri->uri_to_assoc(3);
        $filter['provider_id'] = intval($this->input->post('provider_id'));
        $provider = $this->provider_model->filter($filter);
        if (empty($provider)) {
            sys_msg('供应商不能为空', 1);
        }
        $filter['batch_id'] = intval($this->input->post('batch_id'));
        $batch_info = $this->purchase_batch_model->filter(array("batch_id" => $filter['batch_id']));
        if (empty($batch_info)) {
            sys_msg('供应商采购批次不能为空', 1);
        }
        if ($batch_info->is_reckoned == 1) {
            sys_msg("批次已经结算，无法新增采购单。", 1, array(), FALSE);
            return;
        }
        $filter['start_time'] = trim($this->input->post('start_time'));
        $row = $this->purchase_consign_model->filter($filter);
        if (!empty($row)) {
            sys_msg('不要重复操作', 1);
            return;
        }
        $filter['end_time'] = trim($this->input->post('end_time'));

        $request_param = $this->input->post();

        $exlval = array();
        $val = array();
        foreach ($request_param as $key => $value) {
            if (strlen($key) > 4 && substr($key, 0, 4) == "num_") {
                $tmp_arr = explode('_', $key);
                $sub = $this->product_model->get_consign_single($tmp_arr[1], $tmp_arr[2], $tmp_arr[3]);
                if (empty($sub))
                    continue;
                $val['sub_id'] = $sub->sub_id;
                $val['num'] = $value; //数量
                $exlval[$sub->brand_id][] = $val;
            }
        }
        $details = array();
        $order_id_list = $this->input->post("order_id");
        $op_id_list = $this->input->post("op_id");
        $brand_id_list = $this->input->post("brand_id");
        $confirm_date_list = $this->input->post("confirm_date");
        $product_id_list = $this->input->post("product_id");
        $color_id_list = $this->input->post("color_id");
        $size_id_list = $this->input->post("size_id");
        $num_list = $this->input->post("num");
        foreach ($order_id_list as $key => $order_id) {
            $op_id = $op_id_list[$key];
            $brand_id = $brand_id_list[$key];
            $confirm_date = $confirm_date_list[$key];
            $product_id = $product_id_list[$key];
            $color_id = $color_id_list[$key];
            $size_id = $size_id_list[$key];
            $num = $num_list[$key];

            $val = array();
            $val['num'] = $num; //数量
            $val['product_id'] = $product_id;
            $val['color_id'] = $color_id;
            $val['size_id'] = $size_id;
            $val['order_id'] = $order_id;
            $val['op_id'] = $op_id;
            $val['confirm_date'] = $confirm_date;
            $details[$brand_id][] = $val;
        }

        $filter['create_admin'] = $this->admin_id;
        $filter['create_date'] = $now;

        $this->db->query('BEGIN');
        foreach ($exlval as $key => $item) {
            $purchase_info = $this->create_purchase_order($provider, $filter['batch_id'], $key, $item, $now_time);
            $filter['purchase_code'] = $purchase_info['purchase_code'];
            $filter['brand_id'] = $key;
            $this->purchase_consign_model->insert($filter);
            $detail_list = $details[$key];
            if (!empty($detail_list)) {
                foreach ($detail_list as $info) {
                    $up_c = array();
                    $up_c["purchase_code"] = $purchase_info['purchase_code'];
                    $up_c["provider_id"] = $provider->provider_id;
                    $up_c["brand_id"] = $key;
                    $up_c["batch_id"] = $filter['batch_id'];
                    $up_c["order_id"] = $info['order_id'];
                    $up_c["op_id"] = $info['op_id'];
                    $up_c["confirm_date"] = $info['confirm_date'];
                    $up_c["product_id"] = $info['product_id'];
                    $up_c["color_id"] = $info['color_id'];
                    $up_c["size_id"] = $info['size_id'];
                    $up_c["consign_num"] = $info['num'];
                    $up_c["status"] = 0;
                    $up_c["create_admin"] = $this->admin_id;
                    $up_c["create_date"] = $now;
                    $this->purchase_consign_model->insert_consign_detail($up_c);
                }
            }
        }
        $this->db->query('COMMIT');
        sys_msg('操作成功！', 0, array(array('text' => '查看', 'href' => 'purchase/index/')));
    }

    private function create_purchase_order($provider_info, $batch_id, $brand_id, $exlval, $now_time) {
        $this->load->model('depot_model');
        $now = date('Y-m-d H:i:s', $now_time);
        $data = array();
        $data['purchase_provider'] = $provider_info->provider_id;
        $data['batch_id'] = $batch_id;
        $data['purchase_type'] = $provider_info->provider_cooperation;
        $data['purchase_order_date'] = $now;
        $data['purchase_brand'] = $brand_id;
        $nextWeek = $now_time + (7 * 24 * 60 * 60);
        $data['purchase_delivery'] = date("Y-m-d H:i:s", $nextWeek);
        $data['purchase_remark'] = "";
        $data['create_date'] = $now;
        $data['create_admin'] = $this->admin_id;
        $data['purchase_code'] = $this->depot_model->get_purchase_code();

        $purchase_info = $this->depot_model->filter_purchase(array('purchase_code' => $data['purchase_code']));
        while (1) {
            if ($purchase_info) {
                set_time_limit(1);
                $data['purchase_code'] = $this->depot_model->get_purchase_code();
                $purchase_info = $this->depot_model->filter_purchase(array('purchase_code' => $data['purchase_code']));
            } else {
                break;
            }
        }
        $purchase_id = $this->depot_model->insert_purchase($data);
        //purchase_sub
        foreach ($exlval as $item) {
            $this->depot_model->insert_purchase_pro_single($item['sub_id'], $item['num'], $purchase_id, $this->admin_id);
        }
        $this->depot_model->finished_summly_purchase($purchase_id);
        return $data;
    }

    public function get_start_time() {
        auth('purchase_consign_view');
        $provider_id = intval($this->input->post('provider_id'));
        $batch_id = intval($this->input->post('batch_id'));
        $last_time = $this->purchase_consign_model->get_last_time($provider_id, $batch_id);
        if (empty($last_time)) {
            $last_time = date('2013-03-11 00:00:00');
        }
        echo $last_time;
    }

    public function history() {
        auth('purchase_consign_history');
        $filter = $this->uri->uri_to_assoc(3);
        $filter['provider_id'] = intval($this->input->post('provider_id'));
        $filter['order_sn'] = trim($this->input->post('order_sn'));
        $filter = get_pager_param($filter);
        $data = $this->purchase_consign_model->find_page($filter);

        if ($this->input->is_ajax_request()) {

            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('purchase/purchase_consign_history', $data, TRUE);
            $data['error'] = 0;
            unset($data['list']);
            echo json_encode($data);
            return;
        }

        $data['full_page'] = TRUE;
        $this->load->vars('all_provider', $this->provider_model->all_provider());
        $this->load->view('purchase/purchase_consign_history', $data);
    }

    public function get_provider_batch() {
        $provider_id = intval($this->input->post('provider_id'));
        $this->load->model('purchase_batch_model');
        $list = $this->purchase_batch_model->query(array("provider_id" => $provider_id, "batch_type" => 0));
        if (empty($list) || count($list) < 0) {
            echo '{"result":"0"}';
            return;
        }
        echo json_encode(array("list" => $list));
    }

    public function show_consign_detail($purchase_code) {
        auth('purchase_consign_history');
        if (empty($purchase_code))
            sys_msg("采购单号不能为空");
        $data = array();
        $data["list"] = $this->purchase_consign_model->get_consign_detail($purchase_code);
        $this->load->view('purchase/purchase_consign_detail', $data);
    }

    public function autio() {
        auth(array('purchase_consign_view', 'purchase_consign_create', 'purchase_consign_history'));
        $data = array();
        $data['end_time'] = date('Y-m-d H:i:s', time());
        $list = $this->purchase_consign_model->query_consign_order_count($data);
        $data['list'] = $list;
        $data['full_page'] = TRUE;
        $this->load->view('purchase/purchase_consign_autio', $data);
    }

    public function query_consign_list() {
        $data = array();
        $data['provider_id'] = intval($this->input->post('provider_id'));
        $provider = $this->provider_model->filter($data);
        if (empty($provider)) {
            sys_msg('供应商不能为空', 1);
        }
        $data['batch_id'] = intval($this->input->post('batch_id'));
        $this->load->model('purchase_batch_model');
        $batch_info = $this->purchase_batch_model->filter(array("batch_id" => $data['batch_id']));
        if (empty($batch_info)) {
            sys_msg('供应商采购批次不能为空', 1);
        }
        if ($batch_info->is_reckoned == 1) {
            sys_msg("批次已经结算，无法新增采购单。", 1, array(), FALSE);
            return;
        }
        $data['start_time'] = trim($this->input->post('start_time'));
        $row = $this->purchase_consign_model->filter($data);
        if (!empty($row)) {
            sys_msg('不要重复操作', 1);
            return;
        }
        $data['end_time'] = trim($this->input->post('end_time'));
        $data["order_list"] = $this->purchase_consign_model->query_consign_list($data);
        if (count($data["order_list"]) < 1) {
            sys_msg("没有需要采购的商品，请重新刷新此页面。", 1);
        }
        $data["err"] = 0;
        $data['full_page'] = FALSE;
        $data["content"] = $this->load->view('purchase/purchase_consign_autio', $data, TRUE);
        echo json_encode($data);
    }

    public function autio_create() {
        auth('purchase_consign_create');
        $now_time = time();
        $now = date('Y-m-d H:i:s', $now_time);
        $this->load->model('purchase_batch_model');
        $filter = $this->uri->uri_to_assoc(3);
        $filter['provider_id'] = intval($this->input->post('provider_id'));
        $provider = $this->provider_model->filter($filter);
        if (empty($provider)) {
            sys_msg('供应商不能为空', 1);
        }
        $filter['batch_id'] = intval($this->input->post('batch_id'));
        $batch_info = $this->purchase_batch_model->filter(array("batch_id" => $filter['batch_id']));
        if (empty($batch_info)) {
            sys_msg('供应商采购批次不能为空', 1);
        }
        if ($batch_info->is_reckoned == 1) {
            sys_msg("批次已经结算，无法新增采购单。", 1, array(), FALSE);
            return;
        }
        $filter['start_time'] = trim($this->input->post('start_time'));
        $row = $this->purchase_consign_model->filter($filter);
        if (!empty($row)) {
            sys_msg('不要重复操作', 1);
            return;
        }
        $filter['end_time'] = trim($this->input->post('end_time'));

        $exlval = array();
        $details = array();
        $order_id_list = $this->input->post("order_id");
        $op_id_list = $this->input->post("op_id");
        $brand_id_list = $this->input->post("brand_id");
        $confirm_date_list = $this->input->post("confirm_date");
        $product_id_list = $this->input->post("product_id");
        $color_id_list = $this->input->post("color_id");
        $size_id_list = $this->input->post("size_id");
        $num_list = $this->input->post("num");
        foreach ($order_id_list as $key => $order_id) {
            $op_id = $op_id_list[$key];
            $brand_id = $brand_id_list[$key];
            $confirm_date = $confirm_date_list[$key];
            $product_id = $product_id_list[$key];
            $color_id = $color_id_list[$key];
            $size_id = $size_id_list[$key];
            $num = $num_list[$key];

            $sub = $this->product_model->get_consign_single($product_id, $color_id, $size_id);
            if (empty($sub))
                continue;
            if ($sub->brand_id != $brand_id) {
                sys_msg("详情品牌不一致，调试信息：sub_id:" . $sub->sub_id . ",brand_id:" . $brand_id);
            }

            $val = array();
            $val['num'] = $num; //数量
            $val['product_id'] = $product_id;
            $val['color_id'] = $color_id;
            $val['size_id'] = $size_id;
            $val['order_id'] = $order_id;
            $val['op_id'] = $op_id;
            $val['confirm_date'] = $confirm_date;

            $val['sub_id'] = $sub->sub_id;
            $details[$brand_id][] = $val;

            if (array_key_exists($brand_id, $exlval)){
               $arr = $exlval[$brand_id];
               $exsits = FALSE;
               foreach ($arr as $k=>$it){
                   if($it["sub_id"] == $val['sub_id']){
                       $it["num"] += $num;
		       $arr[$k] = $it;
                       $exsits = TRUE;
                   }
               }
               if(!$exsits){
                   $exlval[$brand_id][] = $val;
               }else{
		   $exlval[$brand_id] = $arr;
	       }
            }else{
                $exlval[$brand_id][] = $val;
            }
        }

        $filter['create_admin'] = $this->admin_id;
        $filter['create_date'] = $now;
        $this->db->query('BEGIN');
        foreach ($exlval as $key => $item) {
            $purchase_info = $this->create_purchase_order($provider, $filter['batch_id'], $key, $item, $now_time);
            $filter['purchase_code'] = $purchase_info['purchase_code'];
            $filter['brand_id'] = $key;
            $this->purchase_consign_model->insert($filter);
            $detail_list = $details[$key];
            if (!empty($detail_list)) {
                foreach ($detail_list as $info) {
                    $up_c = array();
                    $up_c["purchase_code"] = $purchase_info['purchase_code'];
                    $up_c["provider_id"] = $provider->provider_id;
                    $up_c["brand_id"] = $key;
                    $up_c["batch_id"] = $filter['batch_id'];
                    $up_c["order_id"] = $info['order_id'];
                    $up_c["op_id"] = $info['op_id'];
                    $up_c["confirm_date"] = $info['confirm_date'];
                    $up_c["product_id"] = $info['product_id'];
                    $up_c["color_id"] = $info['color_id'];
                    $up_c["size_id"] = $info['size_id'];
                    $up_c["consign_num"] = $info['num'];
                    $up_c["status"] = 0;
                    $up_c["create_admin"] = $this->admin_id;
                    $up_c["create_date"] = $now;
                    $this->purchase_consign_model->insert_consign_detail($up_c);
                }
            }
        }
        $this->db->query('COMMIT');
        sys_msg('操作成功！', 0, array(array('text' => '查看', 'href' => 'purchase_consign/history/')));
    }

}

###
