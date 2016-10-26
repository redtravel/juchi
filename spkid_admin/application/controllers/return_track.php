<?php
class Return_track extends CI_Controller {

    function __construct() {
	parent::__construct();
	$this->admin_id = $this->session->userdata('admin_id');
        if(!$this->admin_id) redirect('index/login');
        $this->load->model('order_model');
	$this->load->model('return_model');
        $this->load->model('return_track_model');
	$this->load->model('apply_return_model');
	$this->load->model('user_model');
    }
	
    public function index() {
        auth('return_track_view');
        $filter = $this->uri->uri_to_assoc(3);
        $all_post = $this->input->post();
        $filter['apply_id'] = isset($all_post['apply_id'])?trim($all_post['apply_id']):'';
        $filter['order_sn'] = isset($all_post['order_sn'])?trim($all_post['order_sn']):'';
        $filter['track_return_sn'] = isset($all_post['track_return_sn'])?trim($all_post['track_return_sn']):'';
        //$filter['start_time'] = trim($this->input->post('start_time'));
        //$filter['end_time'] = trim($this->input->post('end_time'));
        $searchType = isset($all_post['searchType'])?$all_post['searchType']:0;
        if ($searchType == 1) {
            $filter['searchType'] = " AND t.track_return_sn is null ";
        } else if ($searchType == 2) {
            $filter['searchType'] = " AND t.track_return_sn is not null AND t.track_shipping_sn is null ";
        } else if ($searchType == 3) {
            $filter['searchType'] = " AND (r.pay_status = 0 OR r.is_ok = 0) ";
        } else {
            $filter['searchType'] = '';
        }

        $filter = get_pager_param($filter);

        $data = $this->return_track_model->query_list($filter);
        
        $order_status = array('-1'=>'请选择','0'=>'未客审','1'=>'已客审','4'=>'已作废','5'=>'已拒收');
        $data['order_status'] = $order_status;
        
        $data['my_id'] = $this->admin_id;
        
        if ($this->input->is_ajax_request()) {
                $data['full_page'] = FALSE;
                $data['my_id'] = $this->admin_id;
                $data['content'] = $this->load->view('return_track/index', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }
        $data['full_page'] = TRUE;

        $this->load->view('return_track/index', $data);
    }

    public function edit($invoice_no) {
        auth(array('return_track_edit','return_track_view'));
        $apply_return = $this->apply_return_model->filter(array('invoice_no'=>$invoice_no));
        if (!$apply_return) {
            sys_msg('退货申请不存在!', 1);
        }
        
        $order = $this->order_model->filter(array('order_id'=>$apply_return['order_id']));
        if (!$order) {
            sys_msg('系统订单不存在!', 1);
        }
        
        $return_track = $this->return_track_model->filter(array('apply_id'=>$apply_return['apply_id']));
        
        $this->load->vars('order', $order);
        $this->load->vars('apply_return', $apply_return);
        $this->load->vars('return_track', $return_track);
        $this->load->vars('perm_edit', check_perm('return_track_edit'));
        $this->load->view('return_track/edit');
    }

    public function proc_edit() {
        auth('return_track_edit');
//        $this->load->library('form_validation');
//        $this->form_validation->set_rules('order_sn', '系统订单号', 'trim|required');
//        $this->form_validation->set_rules('track_order_sn', '平台订单号', 'trim|required');
//        if (!$this->form_validation->run()) {
//            sys_msg(validation_errors(), 1);
//        }
        
        $apply_id = intval($this->input->post('apply_id'));
        $invoice_no = trim($this->input->post('invoice_no'));
        
        $update = array();
        $update['track_return_sn'] = trim($this->input->post('track_return_sn'));
        $update['track_shipping_name'] = trim($this->input->post('track_shipping_name'));
        $track_shipping_sn = trim($this->input->post('track_shipping_sn'));
        if (!empty($track_shipping_sn)) {
            $update['track_shipping_sn'] = $track_shipping_sn;
        }
        
        $return_track = $this->return_track_model->filter(array('apply_id'=>$apply_id));
        if (!$return_track) {
            $update['apply_id'] = $apply_id;
            $update['track_create_aid'] = $this->admin_id;
            $update['track_create_time'] = date('Y-m-d H:i:s');
            
            $this->return_track_model->insert($update);
        } else {
            $this->return_track_model->update($update, $return_track->track_id);
        }
        
        sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'return_track/edit/'.$invoice_no), array('text'=>'返回列表','href'=>'return_track/index')));
    }

    public function recive($invoice_no='') {
        if (!empty($invoice_no)) {
            $this->load->vars('invoice_no', $invoice_no);
        }
        $this->load->view('return_track/recive');
    }
    
    public function proc_recive() {
        $apply_id = intval($this->input->post('apply_id'));
        
        $apply_info = $this->apply_return_model->filter(array('apply_id'=>$apply_id));
        if (empty($apply_info)) {
            echo json_encode(array('error'=>1, 'result'=>'退货申请不存在！'));
            return;
        }
        
        $this->db->trans_begin();
        $this->db->query('SELECT 1 FROM ty_order_info WHERE order_id = '.$apply_info['order_id'].' for update ');
        // 创建退货单
        $return_info = $this->do_create_return($apply_info);
        // 退货入库
        $this->do_insert_return_transaction($return_info);
        // 更新自助退货申请
        $this->do_update_apply_return($apply_id);
        
        //XXX: 退款、现金券、运费在财审时退还
        $this->db->trans_commit();
        
        echo json_encode(array('error'=>0));
    }
    
    public function return_info($invoice_no) {
        auth('return_track_edit');
        //申请退货理由 0:尺寸偏大 1:尺寸偏小 2:款式不喜欢 3:配送错误 4:其他
        $apply_reason_list = array(
                '0'=>'尺寸偏大',
                '1'=>'尺寸偏小',
                '2'=>'款式不喜欢',
                '3'=>'配送错误',
                '4'=>'其他问题',
                '5'=>'商品质量问题'
        );

        //供应商审核状态
        $apply_provider_status = array(
                '0'=>'未审核',
                '1'=>'正常审核',
                '2'=>'异常审核',
                '3'=>'正常审核'
        );
        $data = array();
        
        //先按退货申请运单号取得申请退货单
        $apply_info = $this->apply_return_model->filter(array('invoice_no'=>$invoice_no));
        if (empty($apply_info)) {
            // 如果取不到，再按订单号取
            $order = $this->order_model->filter(array('order_sn'=>$invoice_no));
            if (!empty($order)) {
                $apply_info = $this->apply_return_model->filter(array('order_id'=>$order->order_id, 'apply_status IN (0,1)'=>NUlL));
            }
            
            if (empty($apply_info)) {
                echo json_encode(array('error'=>1, 'result'=>'相关的退货申请不存在！'));
                return;
            }
        }

        $apply_id = $apply_info['apply_id'];
        //计算已退货数量
        $return_goods_num = $this->apply_return_model->get_return_goods_num($apply_info['order_id']);
        //取得申请退货单商品
        $apply_product = $this->apply_return_model->apply_return_goods($apply_id,$apply_info['order_id']);

        foreach($apply_product as $key=>$v){
            //可退数量
            $k = $v['product_id'].' '.$v['color_id'].' '.$v['size_id'];
            if(isset($return_goods_num[$k])) {
                    $v['n_product_num'] = (int)$v['o_product_number'] - (int)$return_goods_num[$k];
            } else {
                    $v['n_product_num'] = $v['o_product_number'];
            }

            $v['reason'] = $apply_reason_list[$v['return_reason']];
            if($apply_info['order_type'] == 1&&$apply_info['provider_status']==0)
            {
                    $v['apply_provider_status'] = $apply_provider_status[$apply_info['provider_status']];
            }
            if($apply_info['order_type'] == 1&&$apply_info['provider_status']>0)
            {
                    $v['apply_provider_status'] = $apply_provider_status[$apply_info['suggest_type']];
            }
            if(!empty($v['img'])) {
                    $img_arr = explode(";",$v['img']);
                    $v['img_list'] = $img_arr;
            }
            $apply_product[$key] = $v;
        }

        $data['apply_info'] = $apply_info;
        $data['apply_product'] = $apply_product;

        $result = $this->load->view('return_track/info', $data, TRUE);
        echo json_encode(array('error'=>0, 'result'=>$result));
    }
    
    private function do_create_return($apply_info) {
        $user = $this->user_model->filter(array('user_id'=>$apply_info['user_id']));
        $now = date('Y-m-d H:i:s');
        $return_info = array(
            'apply_id'              =>$apply_info['apply_id'],
            'order_id'              =>$apply_info['order_id'],
            'user_id'               =>$apply_info['user_id'],
            'consignee'             =>$apply_info['sent_user_name'],
            'email'                 =>$user->email,
            'address'               =>$apply_info['back_address'],
            'zipcode'               =>'',
            'tel'                   =>$apply_info['tel'],
            'mobile'                =>$apply_info['mobile'],
            'product_num'           =>$apply_info['product_number'],
            'return_price'          =>0,
            'paid_price'            =>0,
            'return_shipping_fee'   =>$apply_info['shipping_fee'], //不返运费不要设置
            'create_date'           =>$now,
            'create_admin'          =>$this->admin_id,
            'pay_status'            =>0,
            'shipping_status'       =>1,
            'shipping_admin'        =>$this->admin_id,
            'shipping_date'         =>$now,
            'invoice_no'            =>$apply_info['invoice_no'],
            'return_status'         =>1,
            'return_reason'         =>'根据自助退货申请自动生成天猫退货单',
        );
        
        // 创建退货单主信息
        do {
            $return_info['return_sn'] = $this->return_model->get_return_sn();
            $return_id = $this->return_model->insert($return_info);
            $return_info['return_id'] = $return_id;
            $err_no = $this->db->_error_number();
            if ($err_no == '1062') continue;
            if ($err_no == '0') break;
            echo json_encode(array('error'=>1, '操作失败！'));
            return;
        } while (true); // 防止订单号重复
        
        // 插入退单商品
        $this->return_track_model->insert_return_products($apply_info, $return_info);
        
        // 更新退单主信息
        $this->return_track_model->update_return_info($return_info['return_id']);
        
        $this->return_model->insert_action($return_info, '天猫退单确认收货');
        
        return $return_info;
    }
    
    private function do_insert_return_transaction($return_info) {
        // 插入出入库表
        $this->return_track_model->insert_return_info_to_transaction($return_info, $this->admin_id);
        
        $this->return_model->insert_action($return_info, '天猫退单商品自动入库');
    }
    
    private function do_update_apply_return($apply_id) {
        // 更新自助退货信息
        $update = array(
            'apply_status' => 2
        );
        $this->apply_return_model->update($update, $apply_id);
    }
    
}
###