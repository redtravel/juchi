<?php
class Order_track extends CI_Controller {

    function __construct() {
	parent::__construct();
	$this->admin_id = $this->session->userdata('admin_id');
        if(!$this->admin_id) redirect('index/login');
        $this->load->model('order_model');
	$this->load->model('order_track_model');
        $this->load->model('return_model');
        $this->load->model('pick_model');
        $this->load->helper('order');
    }
	
    public function index() {
        auth('order_track_view');
        $filter = $this->uri->uri_to_assoc(3);
        $all_post = $this->input->post();
        $filter['order_sn'] = isset($all_post['order_sn'])?trim($all_post['order_sn']):'';
        $filter['order_status'] = isset($all_post['order_status'])?intval($all_post['order_status']):'-1';
        $filter['track_order_sn'] = isset($all_post['track_order_sn'])?trim($all_post['track_order_sn']):'';
        $filter['track_shipping_sn'] = isset($all_post['track_shipping_sn'])?trim($all_post['track_shipping_sn']):'';
        //$filter['start_time'] = trim($this->input->post('start_time'));
        //$filter['end_time'] = trim($this->input->post('end_time'));
        $searchType = isset($all_post['searchType'])?$all_post['searchType']:0;
        if ($searchType == 1) {
            $filter['searchType'] = " AND o.order_status = 1 AND t.track_order_sn is null ";
        } else if ($searchType == 2) {
            $filter['searchType'] = " AND t.track_order_sn is not null AND t.track_shipping_sn is null ";
        } else if ($searchType == 3) {
           $filter['searchType'] = " AND t.track_order_sn is not null AND t.track_shipping_sn is not null AND o.shipping_status = 0 ";
        } else {
            $filter['searchType'] = '';
        }
        
        $filter = get_pager_param($filter);

        $data = $this->order_track_model->query_list($filter);

        $order_status = array('-1'=>'请选择','0'=>'未客审','1'=>'已客审','4'=>'已作废','5'=>'已拒收');
        $data['order_status'] = $order_status;
        
        $data['my_id'] = $this->admin_id;
        
        if ($this->input->is_ajax_request()) {
                $data['full_page'] = FALSE;
                $data['my_id'] = $this->admin_id;
                $data['content'] = $this->load->view('order_track/index', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }
        $data['full_page'] = TRUE;
        
        $this->load->view('order_track/index', $data);
    }

    public function edit($order_sn) {
        auth(array('order_track_edit','order_track_view'));
        $order = $this->order_model->filter(array('order_sn'=>$order_sn));
        if (!$order) {
            sys_msg('系统订单不存在!', 1);
        }
        
        $order_track = $this->order_track_model->filter(array('order_sn'=>$order_sn));
        
        $this->load->vars('order', $order);
        $this->load->vars('order_track', $order_track);
        $this->load->vars('perm_edit', check_perm('order_track_edit'));
        $this->load->view('order_track/edit');
    }

    public function proc_edit() {
        auth('order_track_edit');
//        $this->load->library('form_validation');
//        $this->form_validation->set_rules('order_sn', '系统订单号', 'trim|required');
//        $this->form_validation->set_rules('track_order_sn', '平台订单号', 'trim|required');
//        if (!$this->form_validation->run()) {
//            sys_msg(validation_errors(), 1);
//        }
        
        $order_sn = trim($this->input->post('order_sn'));
        
        $update = array();
        $update['track_order_sn'] = trim($this->input->post('track_order_sn'));
        $track_shipping_sn = trim($this->input->post('track_shipping_sn'));
        if (empty($track_shipping_sn)) {
            $update['track_shipping_sn'] = NULL;
        } else {
            $update['track_shipping_sn'] = $track_shipping_sn;
        }
        
        $order_track = $this->order_track_model->filter(array('order_sn'=>$order_sn));
        if (!$order_track) {
            $update['order_sn'] = $order_sn;
            $update['track_create_aid'] = $this->admin_id;
            $update['track_create_time'] = date('Y-m-d H:i:s');
            
            $this->order_track_model->insert($update);
        } else {
            $this->order_track_model->update($update, $order_track->track_id);
        }
        
        sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'order_track/edit/'.$order_sn), array('text'=>'返回列表','href'=>'order_track/index')));
    }
    
    public function send($order_sn='') {
        if (!empty($order_sn)) {
            $this->load->vars('order_sn', $order_sn);
        }
        $this->load->view('order_track/send');
    }
    
    public function proc_send($order_id, $order_sn) {
        $this->load->model('depot_model');
        $this->load->model('location_model');
        $this->load->model('depotio_model');
        $this->load->model('product_model');
        
        $depotio = $this->depot_model->filter_depot_type(array('depot_type_code'=>DEPOT_TYPE_TMALL_IN));
        $depot_in_type = $depotio->depot_type_id;
        $depot_id = DEPOT_ID_TMALL_SEND;
        $location_id = LOCATION_ID_TMALL_SEND;
        $curDate = date('Y-m-d H:i:s');
        
        $order = $this->order_model->filter(array('order_id'=>$order_id));
        if (empty($order)) {
            echo json_encode(array('error'=>1, 'msg'=>'订单不存在！'));
            return;
        }
        $order_product = $this->order_model->all_product(array('order_id'=>$order_id));
        
        $update = array();
        $update['depot_in_type'] = $depot_in_type;
        $update['depot_in_date'] = $curDate;
        $update['depot_depot_id'] = $depot_id;
        $update['order_sn'] = $order_sn;
        $update['order_id'] = $order_id;

        $update['depot_in_reason'] = '天猫订单虚拟入库';
        $update['create_date'] = $curDate;
        $update['create_admin'] = $this->admin_id;
        $update['depot_in_code'] = $this->depotio_model->get_depot_in_code();
        $update['lock_date'] = $curDate;
        $update['lock_admin'] = $this->admin_id;
        
        $this->db->trans_begin();
        $depot_in_id = $this->depotio_model->insert_depot_in($update);
        foreach ($order_product as $product) {
            $product_sub = $this->product_model->filter_sub(array('product_id'=>$product->product_id, 'color_id'=>$product->color_id, 'size_id'=>$product->size_id));
            $cost = $this->order_track_model->get_cost($product->product_id);
            if (!$cost) {
                echo json_encode(array('error'=>1,'msg'=>'商品批次不正确[不存在/已结算]'));
                return;
            }
            
            $num = $product->product_num;
            $sub_id = $product_sub->sub_id;
            $batch_id = $cost->batch_id;
            $rs = $this->order_track_model->insert_depot_in_single($sub_id,$num,$depot_in_id,$depot_id,$location_id,$this->admin_id,$batch_id);
            if (empty($rs)) {
                echo json_encode(array('error'=>1,'msg'=>'添加商品失败：sub_id:'.$sub_id.",depot_in_id:".$depot_in_id.",location_id:".$location_id.",num:".$num));
                return;
            } elseif ($rs == -1) {
                echo json_encode(array('error'=>1,'msg'=>'要添加的商品已存在入库单：sub_id:'.$sub_id));
                return;
            }
            $update = array(
                        'trans_type'=>TRANS_TYPE_SALE_ORDER,
                        'trans_status'=>TRANS_STAT_OUT,
                        'trans_sn'=>$order_sn,
                        'product_id'=>$product->product_id,
                        'color_id'=>$product->color_id,
                        'size_id'=>$product->size_id,
                        'product_number'=>-1*$num,
                        'depot_id'=>$depot_id,
                        'location_id'=>$location_id,
                        'sub_id'=>$product->op_id,
                        'create_admin'=>$this->admin_id,
                        'create_date'=>$curDate,
                        'shop_price'=>$product->shop_price,
                        'batch_id'=>$batch_id,
                        'consign_price'=>$cost->consign_price,
                        'cost_price'=>$cost->cost_price,
                        'consign_rate'=>$cost->consign_rate,
                        'product_cess'=>$cost->product_cess
                );
            $this->order_track_model->insert_trans($update);
            
            // 订单商品虚转实
            $op_update = array();
            $op_update['consign_num'] = 0;
            $this->order_model->update_product($op_update, $product->op_id);
            
            // 更新sub表wait_num
            $this->order_track_model->update_wait_num($product->product_id, $product->color_id, $product->size_id, $num);
        }
        
        // 更新入库统计
        $this->depotio_model->update_depot_in_total($depot_in_id);
        
        // 更新订单发货
        $update = array(
			'shipping_status' => 1,
			'shipping_admin' => $this->admin_id,
			'shipping_date' => $curDate,
			'lock_admin' => 0,
                        'is_ok' => 1
		);
        $this->order_model->update($update, $order_id);
        $order->shipping_status = 1;
        $this->order_model->insert_action($order, "天猫订单发货");
        $this->db->trans_commit();
        echo json_encode(array('error'=>0,'msg'=>'发货成功！'));
    }
    
    public function print_order($order_id) {
        $order_ids = array();
        $order_ids[] = $order_id;
        $order_info = $this->order_track_model->get_order_info($order_ids);

        $this->load->view('order_track/print_order',array('order_info'=>$order_info, 'pick_sn' => ''));
    }
    
    public function order_info($order_sn) {
        auth('order_track_edit');
        $this->load->model('user_model');
        $this->load->model('admin_model');
        $this->load->model('voucher_model');
        $this->load->model('order_advice_type_model');
        
        $response_date = array();
        $order_sn = trim($order_sn);
        
        // 先按照天猫运单号查询
        $order_track = $this->order_track_model->filter(array('track_shipping_sn'=>$order_sn));
        if ($order_track) {
            $order_sn = $order_track->order_sn;
        } else {
            // 若不存在，再按系统订单号查询
            $order_track = $this->order_track_model->filter(array('order_sn'=>$order_sn));
        }
        $response_date['order_track'] = $order_track;
        
        $order = $this->order_model->filter(array('order_sn'=>$order_sn));
        $order_id = $order->order_id;
        $order = $this->order_model->order_info($order_id);
        
        if(!$order) {
            echo '记录不存在';
            return;
        }
        
        $perms = get_order_perm($order);
        $order = format_order($order);
        
        $order_payment = $this->order_model->order_payment($order_id);
        $order_product = $this->order_model->order_product_details($order_id);
        
        //附加储位信息
        $trans_list = $this->order_model->order_trans($order->order_sn);
        $op_list = $deny_list = array();
        foreach ($trans_list as $t) {
            if($t->trans_status==TRANS_STAT_AWAIT_OUT || $t->trans_status==TRANS_STAT_OUT){
                if(!isset($sub_list[$t->sub_id])) $sub_list[$t->sub_id] = array();
                $op_list[$t->sub_id][] = "<span title='".$t->batch_code."'>{$t->depot_name}[{$t->location_name}]{$t->product_number}</span>";
            }else{
                if(!isset($deny_list[$t->sub_id])) $deny_list[$t->sub_id] = array();
                $deny_list[$t->sub_id][] = "<span title='".$t->batch_code."'>{$t->depot_name}[{$t->location_name}]{$t->product_number}</span>";
            }
        }
        foreach($order_product as $k=>$p){
            if(!$p->product_num) {
                $order_product[$k]->op_depot = $order_product[$k]->deny_depot = array();
                continue;
            }
            $order_product[$k]->op_depot = isset($op_list[$p->op_id])?$op_list[$p->op_id]:array();
            $order_product[$k]->deny_depot = isset($deny_list[$p->op_id])?$deny_list[$p->op_id]:array();
        }
        //附加储位信息结束
        
        if ($perms['edit_pay']) {
            // 取出可用的支付试
            $response_date['available_pay'] = $this->order_model->available_pay(array(
                'source_id'=>$order->source_id,
                'shipping_id' => $order->shipping_id
            ));
        }
        
        //更改订单流程
        $source_list = $shipping_list = array();
        if($perms['edit_order']) $source_list = $this->order_model->all_source(array('is_use'=>1));
        if($perms['shipping'] || $perms['change_shipping']) $shipping_list = $this->order_model->available_shipping(array('source_id'=>$order->source_id,'pay_id'=>$order->pay_id,'region_ids'=>array($order->country,$order->province,$order->city,$order->district),'can_cac'=>TRUE));

        //如果被锁定，则显示锁定人
        if($order->lock_admin){
            $this->load->model('admin_model');
            $lock_admin = $this->admin_model->filter(array('admin_id'=>$order->lock_admin));
            $response_date['lock_admin'] = $lock_admin?$lock_admin->admin_name:"[$order->lock_admin]";
        }

        $response_date['order'] = $order;
        $response_date['user'] = $this->user_model->filter(array('user_id'=>$order->user_id));
        $response_date['voucher_list'] = $this->voucher_model->all_available_voucher(array('user_id'=>$order->user_id));
        $response_date['order_payment'] = $order_payment;
        $response_date['voucher_payment'] = filter_payment($order_payment,'voucher');
        $response_date['order_product'] = $order_product;
        $response_date['order_advice'] = $this->order_model->order_advice($order_id);
        $response_date['all_advice_type'] = $this->order_advice_type_model->all(array('is_use'=>1));
        $response_date['order_action'] = $this->order_model->order_action($order_id);
        $response_date['perms'] = $perms;
        $response_date['source_list'] = $source_list;
        $response_date['shipping_list'] = $shipping_list;
        
        $response_date['format_order'] = $this->format_print_order($order);
        $response_date['front_url'] = FRONT_URL;
        $result = $this->load->view('order_track/info', $response_date, TRUE);
        
        echo $result;
    }
    
    public function format_print_order($order) {
        $this->load->helper('order');
        $format_order=format_order($order);
        $format_order->sn=$order->order_sn;
        $format_order->goods_num=$order->product_num;
        $format_order->id=$order->order_id;				
        $format_order->pick_cell= sprintf('%02d', $order->pick_cell);
        $p = strpos($order->address, $order->province_name);
        $c = strpos($order->address, $order->city_name);
        $d = strpos($order->address, $order->district_name);
        if ($p === 0 && $c === 0 && $d === 0) {
            $format_order->address=$order->address;
        } else {				
            $format_order->address=$order->province_name.' '.$order->city_name.' '.$order->district_name.' '.$order->address;
        }
        $format_order->city=$order->district_name;
        $format_order->codAmount=($order->pay_id==PAY_ID_COD && $order->order_amount>0)?$order->order_amount:0;
        return $format_order;
    }
    
}
###