<?php
/**
* Order
*/
class Order_course extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if (!$this->admin_id) redirect('index/login');
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('order_course_model');
		$this->load->model('order_source_model');
		$this->load->model('product_genre_model');
		$this->load->model('shipping_model');
		$this->load->model('payment_model');
		$this->load->model('region_model');
		$this->load->model('brand_model');
		$this->load->model('user_model');
		$this->load->model('category_model');
		$this->load->model('provider_model');
		$this->load->model('color_model');
		$this->load->model('size_model');
		$this->load->model('voucher_model');
		$this->load->model('return_model');
		$this->load->model('change_model');	
		$this->load->model('depot_model');	
		$this->load->model('depotio_model');	
		$this->load->model('course_model');	
		$this->load->model('user_account_log_model');
        $this->load->model('admin_model');
		$this->load->model('order_advice_type_model');	

		$this->load->helper('category');
		$this->load->helper('order');

		$this->load->library('form_validation');
	}

	public function index()
	{
		auth('order_course_view');
		$filter = $this->uri->uri_to_assoc(3);
		$filter['order_sn'] = trim($this->input->post('order_sn'));
		$filter['user_name'] = trim($this->input->post('user_name'));
		$filter['consignee'] = trim($this->input->post('consignee'));
		$filter['product_sn'] = trim($this->input->post('product_sn'));
		$filter['lock_admin'] = trim($this->input->post('lock_admin'));
		$filter['source_id'] = intval($this->input->post('source_id'));
		$filter['pay_id'] = intval($this->input->post('pay_id'));
		$filter['shipping_id'] = intval($this->input->post('shipping_id'));
		$filter['order_status'] = $this->input->post('order_status')===FALSE?0:intval($this->input->post('order_status'));
		$filter['pay_status'] = $this->input->post('pay_status')===FALSE?0:intval($this->input->post('pay_status'));
		$filter['shipping_status'] = $this->input->post('shipping_status')===FALSE?0:intval($this->input->post('shipping_status'));
		$filter['is_ok'] = $this->input->post('is_ok')===FALSE?0:intval($this->input->post('is_ok'));
		$filter['category_id'] = intval($this->input->post('category_id'));
		$filter['country'] = intval($this->input->post('country'));
		$filter['province'] = intval($this->input->post('province'));
		$filter['city'] = intval($this->input->post('city'));
		$filter['district'] = intval($this->input->post('district'));
		$filter['add_start'] = trim($this->input->post('add_start'));
		$filter['add_end'] = trim($this->input->post('add_end'));
		$filter['pay_start'] = trim($this->input->post('pay_start'));
		$filter['pay_end'] = trim($this->input->post('pay_end'));
		$filter['shipping_start'] = trim($this->input->post('shipping_start'));
		$filter['shipping_end'] = trim($this->input->post('shipping_end'));
        $filter['odd'] = intval($this->input->post('odd'));
        $filter['pick'] = intval($this->input->post('pick'));
		$filter['consign'] = intval($this->input->post('consign'));
		$filter['tel'] = trim($this->input->post('tel'));
		$filter['mobile'] = trim($this->input->post('mobile'));
		$filter['payment_status'] = $this->input->post('payment_status')===FALSE?0:intval($this->input->post('payment_status'));
                
		$filter = get_pager_param($filter);
		$data = $this->order_course_model->order_list($filter);
		$list = $data['list'];
		if(!empty($list)){
		    foreach ($list as $order){
			if($this->order_course_model->query_consign_mark($order->order_id)){
			    $order->consign = TRUE;
			}else{
			    $order->consign = FALSE;
			}
		    }
		}
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('order_course/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}

		$this->load->vars('source_list',$this->order_source_model->all_source());
		$this->load->vars('shipping_list',$this->shipping_model->all_shipping());
		$this->load->vars('pay_list',$this->payment_model->all_payment(array('is_discount'=>0)));
		$this->load->vars('country_list',$this->region_model->all_region(array('parent_id'=>0)));
		$this->load->vars('category_list',category_tree($this->category_model->all_category(array('genre_id' => GENRE_COURSE_ID))));
		$data['full_page'] = TRUE;
		$this->load->view('order_course/index', $data);
	}

	public function info($order_id)
	{
		auth('order_course_view');

		$order_id = intval($order_id);
		$order = $this->order_course_model->order_info($order_id);
		if(!$order) sys_msg('记录不存在',1);
		$perms = get_order_perm($order);
		$order = format_order($order);
		$order_payment = $this->order_course_model->order_payment($order_id);
		$order_product = $this->order_course_model->order_product_details($order_id);
        $order_client = $this->order_course_model->order_client($order_id);
        $this->load->vars('order_client',$order_client);
		//附加储位信息
		$trans_list = $this->order_course_model->order_trans($order->order_sn);
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
			$this->load->vars('available_pay', $this->order_course_model->available_pay(array(
					'source_id'=>$order->source_id,
					'shipping_id' => $order->shipping_id
				)));
		}
		//更改订单流程
		$source_list = $shipping_list = array();
		if($perms['edit_order']) $source_list = $this->order_course_model->all_source(array('is_use'=>1));
		if($perms['shipping'] || $perms['change_shipping']) $shipping_list = $this->order_course_model->available_shipping(array('source_id'=>$order->source_id,'pay_id'=>$order->pay_id,'region_ids'=>array($order->country,$order->province,$order->city,$order->district),'can_cac'=>TRUE));

		//如果被锁定，则显示锁定人
		if($order->lock_admin){
			$this->load->model('admin_model');
			$lock_admin = $this->admin_model->filter(array('admin_id'=>$order->lock_admin));
			$this->load->vars('lock_admin',$lock_admin?$lock_admin->admin_name:"[$order->lock_admin]");
		}

		$this->load->vars('order',$order);
		$this->load->vars('user', $this->user_model->filter(array('user_id'=>$order->user_id)));
		$this->load->vars('voucher_list',$this->voucher_model->all_available_voucher(array('user_id'=>$order->user_id)));
		$this->load->vars('order_payment',$order_payment);
		$this->load->vars('voucher_payment', filter_payment($order_payment,'coupon'));
		$this->load->vars('order_product', $order_product);
		$this->load->vars('order_advice', $this->order_course_model->order_advice($order_id));
		$this->load->vars('all_advice_type', $this->order_advice_type_model->all(array('is_use'=>1)));
		$this->load->vars('order_action', $this->order_course_model->order_action($order_id));
        $this->load->vars('all_admin', $this->admin_model->all_admin(array('user_status' => 1)));
		$this->load->vars('perms', $perms);
		$this->load->vars('source_list',$source_list);
		$this->load->vars('shipping_list',$shipping_list);
        $this->load->vars('front_url',FRONT_URL);
        $this->load->vars('course_type',PRODUCT_COURSE_TYPE);
        $this->load->vars('admin_name',$this->session->userdata('admin_name'));
		$this->load->view('order_course/info');
	}

	// 订单详情页的流程更改
	public function load_pay_list()
	{
		$source_id = intval($this->input->post('source_id'));
		$pay_list = $this->order_course_model->available_pay(array('source_id'=>$source_id,'is_discount'=>0));
		print json_encode(array('err'=>0,'msg'=>'','data'=>get_pair($pay_list,'pay_id','pay_name')));
	}

	public function load_shipping_list()
	{
		$order_id = intval($this->input->post('order_id'));
		$order = $this->order_course_model->filter(array('order_id'=>$order_id));
		$source_id = intval($this->input->post('source_id'));
		$pay_id = intval($this->input->post('pay_id'));
        $shipping_list = $this->order_course_model->available_shipping(array('source_id'=>$source_id,'pay_id'=>$pay_id,'region_ids'=>array($order->country,$order->province,$order->city,$order->district),'can_cac'=>TRUE, 'order_id'=>$order_id));	
		print json_encode(array('err'=>0,'msg'=>'','data'=>get_pair($shipping_list,'shipping_id','shipping_name')));
	}

	public function change_routing()
	{
		$this->load->library('form_validation');
		$this->load->model('order_source_model');
		$this->load->model('shipping_model');
		$this->load->model('payment_model');
		$order_id = intval($this->input->post('order_id'));
		$source_id = intval($this->input->post('source_id'));
		$pay_id = intval($this->input->post('pay_id'));
		$shipping_id = intval($this->input->post('shipping_id'));
		if(!$shipping_id) sys_msg('请选择配送方式',1);
		$act = trim($this->input->post('act'));
		$this->db->trans_begin(); // start transaction
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在', 1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order'] && !$perms['shipping'] && !$perms['change_shipping']) sys_msg('不能操作',1);
		if($perms['edit_order']){
			$pay = $this->order_course_model->available_pay(array('source_id'=>$source_id,'pay_id'=>$pay_id,'shipping_id'=>$shipping_id,'is_discount'=>0));
			if(!$pay) sys_msg('选择的流程不可用',1);
			$shipping = $this->order_course_model->available_shipping(array('source_id'=>$source_id,'pay_id'=>$pay_id,'shipping_id'=>$shipping_id,'region_ids'=>array($order->country,$order->province,$order->city,$order->district),'can_cac'=>TRUE, 'order_id'=>$order_id));
			if(!$shipping) sys_msg('选择的流程不可用',1);
			$update = array('source_id'=>$source_id,'pay_id'=>$pay_id,'shipping_id'=>$shipping_id);

		}else{
			$shipping = $this->order_course_model->available_shipping(array('source_id'=>$order->source_id,'pay_id'=>$order->pay_id,'shipping_id'=>$shipping_id,'region_ids'=>array($order->country,$order->province,$order->city,$order->district),'can_cac'=>TRUE, 'order_id'=>$order_id));
			if(!$shipping) sys_msg('选择的流程不可用',1);
			$update = array('shipping_id'=>$shipping_id);
		}
		$this->order_course_model->update($update,$order_id);
		foreach($update as $key=>$val) $order->$key=$val;
		if($perms['edit_order']) update_shipping_fee($order);
		$log = '更改订单流程';
		$source = $this->order_source_model->filter(array('source_id'=>$order->source_id));
		if($source) $log.=' '.$source->source_name;
		$pay = $this->payment_model->filter(array('pay_id'=>$order->pay_id));
		if($pay) $log.=' '.$pay->pay_name;
		$shipping = $this->shipping_model->filter(array('shipping_id'=>$order->shipping_id));
		if($shipping) $log .= ' '.$shipping->shipping_name;
		$this->order_course_model->insert_action($order,$log);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function add()
	{
		auth('order_course_edit');
		$this->load->vars('all_source', $this->order_source_model->all_source());
		$this->load->view('order_course/add');
	}

	public function proc_add()
	{
		auth('order_course_edit');
		$update['source_id'] = intval($this->input->post('source_id'));
		$update['user_id'] = intval($this->input->post('user_id'));
		$update['genre_id'] = intval($this->input->post('genre_id'));
		$source = $this->order_source_model->filter(array('source_id'=>$update['source_id']));
		if(!$source) sys_msg('请选择订单来源', 1);
		$user = $this->user_model->filter(array('user_id'=>$update['user_id']));
		if(!$user) sys_msg('请选择用户', 1);	
		$genre = $this->product_genre_model->filter(array('id'=>$update['genre_id']));
		if(!$genre) sys_msg('请选择订单类型', 1);			
		$update['lock_admin'] = $this->admin_id;
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = $this->time;
		$update['shipping_fee'] = 0;
		while (true)
        {
            $update['order_sn'] = get_order_sn();
            $order_id = $this->order_course_model->insert($update);
            $err_no = $this->db->_error_number();
            if ($err_no == '1062') continue;
            if ($err_no == '0') break;
            sys_msg('操作失败', 1);
            return;
        }
        $order = $this->order_course_model->filter(array('order_id'=>$order_id));
        update_shipping_fee($order);
        $this->order_course_model->insert_action((object)array('order_id'=>$order_id),'添加订单');
		redirect(site_url('order_course/product/'.$order_id).'?act=add');
	}

	public function product($order_id)
	{
		auth('order_course_edit');

		$order_id = intval($order_id);
		$act = isset($_GET['act'])?trim($_GET['act']):'';
		$order = $this->order_course_model->filter(array('order_id'=>$order_id));
		if(!$order) sys_msg('订单不存在', 1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作',1);
		$order_product = split_package_product($this->order_course_model->order_product($order_id));
		$this->load->vars('all_category',category_tree($this->category_model->all_category(array('genre_id' => GENRE_COURSE_ID))));
		$this->load->vars('order', format_order($order));
		$this->load->vars('order_product', $order_product['product']);
		$this->load->vars('edit_product',TRUE);
		$this->load->vars('act',$act);
		$this->load->view('order_course/product');
	}

	public function consignee($order_id)
	{
		auth('order_course_edit');
		$order_id = intval($order_id);
		$act = isset($_GET['act'])?trim($_GET['act']):'';
		$order = $this->order_course_model->filter(array('order_id'=>$order_id));
		if(!$order) sys_msg('订单不存在', 1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作');
		$all_address = index_array($this->user_model->all_address(array('user_id'=>$order->user_id)),'address_id');
		$address_id = isset($_GET['address_id'])?intval($_GET['address_id']):0;
		if($address_id && isset($all_address[$address_id])){
			$address = $all_address[$address_id];
			foreach(array('country','province','city','district','address','consignee','mobile','tel','zipcode') as $key) $order->$key = $address->$key;
		}
		if (!$order->country)  $order->country = 1;
		$country_list = $province_list = $city_list = $district_list = array();
		$country_list = $this->region_model->all_region(array('parent_id'=>0));
		$province_list = $this->region_model->all_region(array('parent_id'=>$order->country));
		if ($order->province) {
			$city_list = $this->region_model->all_region(array('parent_id'=>$order->province));			
			if ($order->city) {
				$district_list = $this->region_model->all_region(array('parent_id'=>$order->city));
			}
		}
		$this->load->vars(array(
			'country_list'=>$country_list,
			'province_list'=>$province_list,
			'city_list'=>$city_list,
			'district_list'=>$district_list,
			'address_list' => $all_address
		));
		
		$this->load->vars('order', format_order($order));
		$this->load->vars('all_address', $all_address);
		$this->load->vars('address_id', $address_id);
		$order_product = $this->order_course_model->order_product($order_id);
		$order_product = split_package_product($order_product);
		$this->load->vars('order_product', $order_product['product']);
		$this->load->vars('order_package', $order_product['package']);
		$this->load->vars('act', $act);
		$this->load->view('order_course/consignee');		
	}

	public function proc_consignee()
	{
		auth('order_course_edit');

		$order_id = intval($this->input->post('order_id'));
		$act = trim($this->input->post('act'));
		$this->db->trans_begin(); // start transaction
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在', 1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作');
		$cac = intval($this->input->post('cac'));
  		$this->form_validation->set_rules('consignee', '收件人', 'trim|required');
  		if(!$cac){
  			$this->form_validation->set_rules('address', '收货地址', 'trim|required');
	  		$this->form_validation->set_rules('country', '国家', 'required');
	  		$this->form_validation->set_rules('province', '省', 'required');
	  		$this->form_validation->set_rules('district', '市', 'required');
  		}
		if ($this->form_validation->run() == FALSE)
		{
			sys_msg($this->form_validation->error_string('', '<br/>'), 1);
		}
		$update = array();
		$update['consignee'] = trim($this->input->post('consignee'));
		$update['address'] = trim($this->input->post('address'));
		$update['zipcode'] = trim($this->input->post('zipcode'));
		$update['tel'] = trim($this->input->post('tel'));
		$update['mobile'] = trim($this->input->post('mobile'));
		$update['best_time'] = trim($this->input->post('best_time'));
		$update['country'] = intval($this->input->post('country'));
		$update['province'] = intval($this->input->post('province'));
		$update['city'] = intval($this->input->post('city'));
		$update['district'] = intval($this->input->post('district'));
		// 获取合适的配送方式，自提需要单独判断
		$msg = '操作成功！';
        if ($cac) {
			//检查流程是否允许，如果允许判断pay_id是否还符合，如果不符合则清空
			$routing = index_array($this->order_course_model->all_routing(array('source_id'=>$order->source_id,'shipping_id'=>SHIPPING_ID_CAC,'show_type !='=>4)),'pay_id');
			if(!$routing) sys_msg('该订单来源不能允许自提',1);
			if(!isset($routing[$order->pay_id])) $update['pay_id'] = 0;
			$update['shipping_id'] = SHIPPING_ID_CAC;
		} else {
			$available_shipping = $this->order_course_model->available_shipping(array(
				'source_id'=>$order->source_id,
				'pay_id'=>$order->pay_id,
				'region_ids' => array($update['country'], $update['province'], $update['city'], $update['district']),
                'order_id' => $order->order_id,
			));
			if(empty($available_shipping)){
				$msg .= '但是没有合适的配送方式';
				$update['shipping_id'] = 0;
			}else{
				$available_shipping = array_keys(index_array($available_shipping,'shipping_id'));
				if(!in_array($order->shipping_id, $available_shipping)){
					$update['shipping_id'] = $available_shipping[0];
				}
			}
		}
		
		$this->order_course_model->update($update, $order_id);
		foreach($update as $key=>$val) $order->$key=$val;
		update_shipping_fee($order);
		$this->db->trans_commit();
		if ($act=='add') {
			redirect(site_url('order_course/payment/'.$order_id).'?act=add');
		}else{
			sys_msg($msg,0,array(array('href'=>'order_course/info/'.$order_id,'text'=>'返回详情页')));
		}
	}

	public function payment($order_id)
	{
		auth('order_course_edit');
		$order_id = intval($order_id);
		$act = isset($_GET['act'])?trim($_GET['act']):'';
		$order = $this->order_course_model->filter(array('order_id'=>$order_id));
		if(!$order) sys_msg('订单不存在', 1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作');
		//可用支付方式列表,去掉余额支付
		$pay_list = $this->order_course_model->available_pay(array('source_id'=>$order->source_id,'shipping_id'=>$order->shipping_id,'is_discount'=>0));
		$pay_list = index_array($pay_list,'pay_code');
		//取订单商品
		$order_product = split_package_product($this->order_course_model->order_product($order_id));
		$order_payment = $this->order_course_model->order_payment($order_id);

		$this->load->vars('order', format_order($order));
		$this->load->vars('order_product', $order_product['product']);
		$this->load->vars('order_package', $order_product['package']);
		$this->load->vars('voucher_list',$this->voucher_model->all_available_voucher(array('user_id'=>$order->user_id)));
		$this->load->vars('pay_list',$pay_list);
		$this->load->vars('order_payment',$order_payment);
		$this->load->vars('voucher_payment', filter_payment($order_payment,'voucher'));
		$this->load->vars('user', $this->user_model->filter(array('user_id'=>$order->user_id)));
		$this->load->vars('act', $act);
		$this->load->view('order_course/payment');
	}

	// 支付方式只在添加的时候才会用，修改的时候直接在详情页操作,所以act永远以add向下传送
	public function proc_payment()
	{
		auth('order_course_edit');

		$order_id = intval($this->input->post('order_id'));
		$this->db->trans_begin(); // start transaction
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在', 1);
		$perms = get_order_perm($order);
		if(!$perms['edit_order']) sys_msg('不能操作');
		$pay_id = intval($this->input->post('pay_id'));
		$order = format_order($order);
		//if($order->order_amount>0){
                if (!$pay_id) {
                    sys_msg('请选择支付方式',1);
                }
                $pay_list = $this->order_course_model->available_pay(array('source_id'=>$order->source_id,'shipping_id'=>$order->shipping_id,'is_discount'=>0,'can_balance'=>PAY_ID_BALANCE));
                if (!in_array($pay_id,get_pair($pay_list,'pay_id','pay_id'))) {
                    sys_msg('您选择的支付方式不符合订单流程', 1);
                }
                $this->order_course_model->update(array('pay_id'=>$pay_id), $order_id);
                //}
		
		$new_order = $this->order_course_model->filter(array('order_id'=>$order_id));
		update_shipping_fee($new_order);
		
		$this->db->trans_commit();
		redirect(site_url('order_course/other/'.$order_id).'?act=add');
	}

	public function other($order_id)
	{
		auth('order_course_edit');

		$order_id = intval($order_id);
		$act = isset($_GET['act'])?trim($_GET['act']):'';
		$order = $this->order_course_model->filter(array('order_id'=>$order_id));
		if(!$order) sys_msg('订单不存在', 1);
		$perms = get_order_perm($order);
		if(!$perms['edit_other']) sys_msg('不能操作');
		//取订单商品
		$order_product = split_package_product($this->order_course_model->order_product($order_id));

		$this->load->vars('order', format_order($order));
		$this->load->vars('order_product', $order_product['product']);
		$this->load->vars('order_package', $order_product['package']);
		$this->load->vars('act', $act);
		$this->load->view('order_course/other');
	}

	public function proc_other()
	{
		auth('order_course_edit');
		$this->load->library('form_validation');
		$order_id = intval($this->input->post('order_id'));
		$act = trim($this->input->post('act'));
		$this->db->trans_begin(); // start transaction
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在', 1);
		$perms = get_order_perm($order);
		if(!$perms['edit_other']) sys_msg('不能操作');
		$update['invoice_title'] = trim($this->input->post('invoice_title'));
		$update['invoice_content'] = trim($this->input->post('invoice_content'));
		$update['user_notice'] = trim($this->input->post('user_notice'));
		$update['to_buyer'] = trim($this->input->post('to_buyer'));
		$this->order_course_model->update($update,$order_id);

		$this->db->trans_commit();
		if ($act=='add') {
			redirect('order_course/info/'.$order_id);
		}
		sys_msg('操作成功',0,array(array('text'=>'返回订单详情','href'=>'order_course/info/'.$order_id)));
	}

	public function deny($order_id)
	{
		auth('order_course_deny');

		$order_id = intval($order_id);
		$order = $this->order_course_model->filter(array('order_id'=>$order_id));
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['deny']) sys_msg('不能操作',1);
		$return = $this->return_model->filter(array('order_id'=>$order_id,'return_status !='=>4));
		$change = $this->change_model->filter(array('order_id'=>$order_id,'change_status !='=>4));
		if($return || $change) sys_msg('订单已有退货或换货，不能拒收！',1);
		foreach($this->order_course_model->order_payment($order_id) as $payment){
			if($payment->pay_id == PAY_ID_VOUCHER || $payment->pay_id == PAY_ID_BALANCE) continue;
			if(!$payment->payment_admin) continue;
			sys_msg("请选删除财务人员添加的支付记录",1);
		}
		$tmp_links[] = array('text' => '返回订单详情', 'href' => "/order_course/info/".$order_id );
		$batch_order_products = $this->order_course_model->get_batch_order_products( $order_id );

		// 验证订单商品的所在批次是否锁定；哪些批次是已经结算过

		$reckoned_batches = Array();
		for ( $i=0; $i<sizeof($batch_order_products); $i++ )
		{
			$product = $batch_order_products[$i];
			if( $product['is_reckoned'] ) 
			{
				if( isset( $reckoned_batches[$product['sub_id']] ) )
					$reckoned_batches[$product['sub_id']]  = $product['product_number'];
				else 
					$reckoned_batches[$product['sub_id']]  += $product['product_number'];

			}

		}

		
		$order_product = $this->order_course_model->order_product($order_id);
        $order_products = array();


        foreach($order_product as $key=>$val){

            $val = (array)$val;
            if ($val['product_num'] <= $val['consign_num']) {

                continue;
            }
			$val['out_depot'] = $val['order_sn'].'<br/>'.$val['depot_name'].'-'.$val['location_name'].' => '.$val['product_number'];// 订单出库仓
			$val['ctb_number'] = isset( $reckoned_batches[$val['op_id']] )?$reckoned_batches[$val['op_id']]:0;//ctb数量
			if($val['ctb_number'] <0){
			    $val['ctb_number'] = 0- $val['ctb_number'];
			}
			$val['real_number'] = $val['product_num'] - $val['consign_num'] - $val['ctb_number'];//不需要代转买的商品数量
			
			//入库到哪个仓
            if($val['ctb_number'] > 0) { //代转买
                $val['order_depot_id'] = CTB_RETURN_DEPOT_ID;
                $val['order_depot_name'] = CTB_RETURN_DEPOT_NAME;
                $val['order_location_id'] = CTB_RETURN_DEPOT_LOCATION_ID;
                $val['order_location_name'] = CTB_RETURN_DEPOT_LOCATION_NAME;
            } else {
                $coop = $this->order_course_model->get_order_product_cooperation($val['op_id']);
                if ($coop->provider_cooperation == COOPERATION_TYPE_COST) { //买断(XXX:暂未使用)
//                   
                } elseif ($coop->provider_cooperation == COOPERATION_TYPE_CONSIGN) { //代销(XXX:暂未使用)
//                    
                }  elseif ($coop->provider_cooperation == COOPERATION_TYPE_TMALL) { //天猫发货
                    $return_product[$key]['return_depot_id'] = DEPOT_ID_TMALL_RETURN;
                    $return_product[$key]['return_depot_name'] = DEPOT_NAME_TMALL_RETURN;
                    $return_product[$key]['return_location_id'] = LOCATION_ID_TMALL_RETURN;
                    $return_product[$key]['return_location_name'] = LOCATION_NAME_TMALL_RETURN;
                }  elseif ($coop->provider_cooperation == COOPERATION_TYPE_FW_VIRTUAL) { //MT服务(虚库)
                    $return_product[$key]['return_depot_id'] = DEPOT_ID_FW_VIRTUAL_RETURN;
                    $return_product[$key]['return_depot_name'] = DEPOT_NAME_FW_VIRTUAL_RETURN;
                    $return_product[$key]['return_location_id'] = LOCATION_ID_FW_VIRTUAL_RETURN;
                    $return_product[$key]['return_location_name'] = LOCATION_NAME_FW_VIRTUAL_RETURN;
                }  elseif ($coop->provider_cooperation == COOPERATION_TYPE_MT_REAL) { //MT代销(实库)
                    $return_product[$key]['return_depot_id'] = DEPOT_ID_MT_REAL_RETURN;
                    $return_product[$key]['return_depot_name'] = DEPOT_NAME_MT_REAL_RETURN;
                    $return_product[$key]['return_location_id'] = LOCATION_ID_MT_REAL_RETURN;
                    $return_product[$key]['return_location_name'] = LOCATION_NAME_MT_REAL_RETURN;
                }  elseif ($coop->provider_cooperation == COOPERATION_TYPE_MT_VIRTUAL) { //MT服务(虚库)
                    $return_product[$key]['return_depot_id'] = DEPOT_ID_MT_VIRTUAL_RETURN;
                    $return_product[$key]['return_depot_name'] = DEPOT_NAME_MT_VIRTUAL_RETURN;
                    $return_product[$key]['return_location_id'] = LOCATION_ID_MT_VIRTUAL_RETURN;
                    $return_product[$key]['return_location_name'] = LOCATION_NAME_MT_VIRTUAL_RETURN;
                }
            }
            
            $order_products[$key] = $val;
        }

        $this->load->vars(array(
                'order' => $order,
                'order_product' => $order_products 
        ));
        $this->load->view('order_course/deny');
	} 

	public function proc_deny()
    {
		auth('order_course_deny');

		$order_id = intval($this->input->post('order_id'));
		$return_shipping_fee = $this->input->post('return_shipping_fee');
        $all_post_other = $this->input->post();
        $aid = $this->admin_id;
        $this->db->trans_begin();
		$order = $this->order_course_model->lock_order($order_id);
		if(!$order) sys_msg('订单不存在',1);
		$perms = get_order_perm($order);
		if(!$perms['deny']) sys_msg('不能操作',1);
		$user = $this->user_model->lock_user($order->user_id);
		$return = $this->return_model->filter(array('order_id'=>$order_id,'return_status !='=>4));
		$change = $this->change_model->filter(array('order_id'=>$order_id,'change_status !='=>4));
		if($return || $change) sys_msg('订单已有退货或换货，不能拒收！',1);
		// 如果有财务类支付，需要先删除
		$order_payment = $this->order_course_model->order_payment($order_id);
		foreach ($order_payment as $payment) {
			if($payment->pay_id == PAY_ID_VOUCHER || $payment->pay_id == PAY_ID_BALANCE) continue;
			if(!$payment->payment_admin) continue;
			sys_msg("请选删除财务人员添加的支付记录",1);
		}
		
		$tmp_links[] = array('text' => '返回订单详情', 'href' => "/order_course/info/".$order_id );
		$batch_order_products = $this->order_course_model->get_batch_order_products( $order_id );

        // 验证订单商品的所在批次是否锁定；哪些批次是已经结算过
//		$batch_locked_result = '';
		$reckoned_batches = Array();
		for ( $i=0; $i<sizeof($batch_order_products); $i++ )
		{
			$product = $batch_order_products[$i];
		}	
		
        $trans_arr = array(); 
		$ctb_trans_arr = array();
        $location_ids = array();
        //print_r($all_post_other);
        // 指定仓库／储位 1: 正常商品
		if (isset($all_post_other['op_id']) && !empty($all_post_other['op_id']))
		{
			foreach ($all_post_other['op_id'] as $key=>$rec_id)
			{
				$location_id = $all_post_other['location_id'][$key];
				
				//TODO BABY-235 退货入库-仓库属性校验
				$depot_id = $all_post_other['depot_id'][$key];
				$cooperation = $this->order_course_model->get_order_product_cooperation($rec_id);
				$depot = $this->depot_model->filter_depot(array('depot_id'=>$depot_id));
				if(!empty($cooperation) && !empty($depot)) {
					if($cooperation->provider_cooperation != $depot->cooperation_id) {
						sys_msg('拒收商品合作方式与仓库属性不一致',1,$tmp_links);
					}
				}
				
				$num = $all_post_other['rec_num'][$key];
				$trans_arr[$rec_id] = array('rec_id'=>$rec_id,'location_id'=>$location_id, 'product_number'=>$num, 'depot_id' => $depot_id);
				//$location_ids[] = $location_id;
			}
		}
		// 指定仓库／储位 2: 代销转买断商品
		if (isset($all_post_other['ctb_op_id']) && !empty($all_post_other['ctb_op_id']))
		{
			foreach ($all_post_other['ctb_op_id'] as $key=>$rec_id)
			{
				$location_id = $all_post_other['ctb_location_id'][$key];
				
				$depot_id = $all_post_other['ctb_depot_id'][$key];
				$cooperation = $this->order_course_model->get_order_product_cooperation($rec_id);
				$depot = $this->depot_model->filter_depot(array('depot_id'=>$depot_id));
				//echo "cooperation: ".$cooperation->provider_cooperation."---".$depot->cooperation_id;die;
				if(!empty($cooperation) && !empty($depot)) {
					if($cooperation->provider_cooperation != $depot->cooperation_id) {
						sys_msg('拒收商品合作方式与仓库属性不一致',1,$tmp_links);
					}
				}
				
				$num = $all_post_other['ctb_rec_num'][$key];
				$ctb_trans_arr[$rec_id] = array('rec_id'=>$rec_id,
				        'location_id'=>$location_id,
						'product_number'=>$num,  
						'depot_id' => $depot_id
						);
			}
		}
        $result_rs = $this->return_model->filter_transaction_all(array('trans_type'=>TRANS_TYPE_SALE_ORDER, 
					'trans_status'=>TRANS_TYPE_DIRECT_OUT, 
					'trans_sn'=>$order->order_sn
				     ));
        
        /**
		 * 正常商品入库的实际库存；CTB入库操作记录的覆盖和新增
		 * 正常商品自动入库完结；如果有代销转买断商品要扣除CTB数量
		 * 
		 */
        if (!empty($result_rs))
        {
            $to_add_rec = array();
            $ctb_unsale_rec = array();
            $ctbArray = array();
            foreach ($result_rs as $key=>$val)
            {
                if (!isset($trans_arr[$val['sub_id']]) && !isset($ctb_trans_arr[$val['sub_id']]))
                {
                    $this->db->query('ROLLBACK');
                    sys_msg('储位信息丢失！',1,$tmp_links);exit;
                }
				// 有正常商品和CTB商品同时存在
				if( isset($trans_arr[$val['sub_id']]) && isset($ctb_trans_arr[$val['sub_id']])  )
				{
					if ( isset($trans_arr[$val['sub_id']]) ) $trans = $trans_arr[$val['sub_id']];
					if ( isset($ctb_trans_arr[$val['sub_id']]) ) $ctb_trans = $ctb_trans_arr[$val['sub_id']];
					$val['trans_status'] = TRANS_STAT_IN;
					$val['depot_id'] = $trans['depot_id'];
					$val['location_id'] = $trans['location_id'];
					$val['create_admin'] = $aid;
					$val['create_date'] = date('Y-m-d H:i:s');
					$val['update_admin'] = $aid;
					$val['update_date'] = date('Y-m-d H:i:s');
					$val['trans_direction'] = 1;
					$val['product_number'] = abs($val['product_number']);
					unset( $val['transaction_id'] );
					$this->return_model->insert_transaction( $val );
				}else{
					if ( isset($ctb_trans_arr[$val['sub_id']]) ) {
						$ctb_trans = $ctb_trans_arr[$val['sub_id']];
						// transaction 的赋值 
								   
					    $val['depot_id'] = RETURN_DEPOT_ID;
					    $val['location_id'] = RETURN_DEPOT_LOCATION_ID;
					}

					if ( isset($trans_arr[$val['sub_id']]) ) {
						$trans = $trans_arr[$val['sub_id']];
						// transaction 的赋值 
					    $val['depot_id'] = $trans['depot_id'];
					    $val['location_id'] = $trans['location_id'];
					}
					
					$val['trans_status'] = TRANS_STAT_IN;
					$val['create_admin'] = $aid;
					$val['create_date'] = date('Y-m-d H:i:s');
					$val['update_admin'] = $aid;
					$val['update_date'] = date('Y-m-d H:i:s');
					$val['trans_direction'] = 1;
					$val['product_number'] = abs($val['product_number']);
					unset( $val['transaction_id'] );
                    
					$this->return_model->insert_transaction( $val );
				}
				// 这里面的数组是要生成新商品。做ctb的
				if ( isset($ctb_trans_arr[$val['sub_id']]) ) {
				    unset($val['depot_id']);
					unset($val['location_id']);
					unset($val['product_number']);
					array_push( $ctbArray, array_merge( $val, $ctb_trans ) );
				}				
            }		 
        }
        // 更新虚库库存
		$this->order_course_model->update_productsub_by_orderid($order_id);
		
		// 查看哪些批次是已经结算过的，准备做代销转买断 
		// CTB 生成新的商品
		if( !empty($ctbArray) ) {
			sys_ctb_operation( $ctbArray );
		}					 
		
		$update = array(
			'order_status' => 5,
			'is_ok' => 1,
			'is_ok_admin' => $this->admin_id,
			'is_ok_date' => $this->time,
			'lock_admin' => 0
		);
		$action_note = '订单拒收';
		// 处理sub表和trans表
		$sub_ids = array();
		$location_ids = array();
		$trans_list = array();

		// 返还余额
		$balance_amount = 0;
		$voucher_payment = NULL;
		foreach ($order_payment as $payment) {
			if ($payment->pay_id == PAY_ID_VOUCHER) $voucher_payment=$payment;
			if ($payment->is_discount) continue;
			$balance_amount += $payment->payment_money;
		}
		if(!$return_shipping_fee) $balance_amount = max($balance_amount-$order->shipping_fee,0);
		if($balance_amount){
			$this->order_course_model->insert_payment(array(
				'order_id' => $order->order_id,
				'is_return' => 0,
				'pay_id' => PAY_ID_PAYBACK,
				'bank_code' => '',
				'payment_account' => '',
				'payment_money' => fix_price($balance_amount*-1),
				'trade_no' => '',
				'payment_remark' => '订单拒收，已付金额返还帐户。',
				'payment_admin' => $this->admin_id,
				'payment_date' => $this->time
			));
		
			$this->user_account_log_model->insert(array(
				'link_id' => $order->order_id,
				'user_id' => $user->user_id,
				'user_money' => fix_price($balance_amount),
				'change_desc' => sprintf("订单 %s 拒收，已付金额返还帐户。",$order->order_sn),
				'change_code' => 'order_payback',
				'create_admin' => $this->admin_id,
				'create_date' => $this->time
			));		
			// 更新用户表
			$this->user_model->update(array('user_money'=>fix_price($user->user_money+$balance_amount)),$user->user_id);
			$order->paid_price -= $balance_amount;
		}
		// 取消现金券
		if($voucher_payment){
			$voucher_sn = $payment->payment_account;
			$voucher = $this->voucher_model->lock_voucher($voucher_sn);
			$this->voucher_model->update(array('used_number'=>$voucher->used_number+1),$voucher->voucher_id);
			$this->order_course_model->delete_payment($voucher_payment->payment_id);
			$action_note.="，取消使用现金券{$voucher_sn}";
			$order->paid_price -= $voucher_payment->payment_money;
		}		
		// 更新订单
		$update['paid_price'] = fix_price($order->paid_price);
		$this->order_course_model->update($update,$order_id);
		foreach($update as $key=>$val) $order->$key=$val;
		$this->order_course_model->insert_action($order,$action_note);
		// 添加日志
		$this->db->trans_commit();
		sys_msg('操作成功',0,array(array('href'=>'order_course/info/'.$order_id,'text'=>'返回订单详情页')));
	}
}
