<?php
#doc
#	classname:	Purchase_batch
#	scope:		PUBLIC
#
#/doc

class Purchase_batch extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('purchase_batch_model');
		$this->load->model('provider_model');
		$this->load->model('brand_model');
	}
	
	public function index ()
	{
		auth('purchase_batch_view');
        //组装参数
		$filter = $this->uri->uri_to_assoc(3);
//        $batch_name = trim($this->input->post('batch_name'));
//        if (!empty($batch_name)) $filter['batch_name'] = $batch_name;
        $batch_code =  trim($this->input->post('batch_code'));
        if (!empty($batch_code)) $filter['batch_code'] = $batch_code;
        $provider_id =  trim($this->input->post('provider_id'));
        if (!empty($provider_id)) $filter['provider_id'] = $provider_id;
        $brand_id =  trim($this->input->post('brand_id'));
        if (!empty($brand_id)) $filter['brand_id'] = $brand_id;
//        $batch_status =  trim($this->input->post('batch_status'));
//        if ( !empty($batch_status) || $batch_status == '0') $filter['batch_status'] = $batch_status;
//        $plan_arrive_date =  trim($this->input->post('plan_arrive_date'));
//        if (!empty($plan_arrive_date)) $filter['plan_arrive_date'] = $plan_arrive_date;
        $create_admin =  trim($this->input->post('create_admin'));
        if (!empty($create_admin)) $filter['create_admin'] = $create_admin;
        $create_date_start =  trim($this->input->post('create_date_start'));
        if (!empty($create_date_start)) $filter['create_date_start'] = $create_date_start;
        $create_date_end =  trim($this->input->post('create_date_end'));
        if (!empty($create_date_end)) $filter['create_date_end'] = $create_date_end;
        $provider_list = $this->provider_model->all_provider(array(),"provider_code asc");
        $providers = array();
        foreach ($provider_list as $provider){
            $providers[$provider->provider_id] = $provider;
        }
        $coop_list = $this->provider_model->all_cooperation(array());
        $cooperation = array();
        foreach ($coop_list as $coop){
            $cooperation[$coop->cooperation_id] = $coop->cooperation_name;
        }
        $this->load->vars('provider_list', $provider_list);//查询条件中的下啦列表框使用
        $this->load->vars('brand_list', $this->brand_model->all_brand());
        $this->load->vars('providers', $providers);
        $this->load->vars('cooperation', $cooperation);
        $batch_type = array('采购单','代转买批次','盘赢','其他');
        $this->load->vars('batch_type', $batch_type);
        //设置权限
		$this->load->vars('perm_add', check_perm('purchase_batch_add'));
		$this->load->vars('perm_edit', check_perm('purchase_batch_edit'));
		$this->load->vars('perm_delete', check_perm('purchase_batch_delete'));
		$this->load->vars('perm_lock', check_perm('purchase_batch_lock'));
		$this->load->vars('perm_reckon', check_perm('purchase_batch_reckon'));
		$this->load->vars('admin_id', $this->admin_id);
        //做分页
        $filter['sort_by'] = 'b.batch_id';
		$filter['sort_order'] ='desc';
        $filter = get_pager_param($filter);
        //查询
        $data = $this->purchase_batch_model->purchase_batch_list($filter);
        if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/purchase_batch/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('depot/purchase_batch/index', $data);
	}
       
	public function add()
	{
		auth('purchase_batch_add');
        $provider_list = $this->provider_model->all_provider(array('is_use'=>1),"provider_code asc");
        $this->load->vars('provider_list', $provider_list);
//        $this->load->vars('brand_list', $this->brand_model->all_brand());
        $this->load->view('depot/purchase_batch/add');
	}

	public function proc_add()
	{
		auth('purchase_batch_add');
		$this->load->vars('perm_add', check_perm('purchase_batch_add'));
		$this->load->vars('perm_edit', check_perm('purchase_batch_edit'));
		$this->load->vars('perm_delete', check_perm('purchase_batch_delete'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('batch_name', '批次名称', 'trim|required');
		$this->form_validation->set_rules('plan_num', '预计收货数量', 'trim|required');
		$this->form_validation->set_rules('plan_arrive_date', '预计收货日期', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
        //检查当前选择的供应商是否有批次未结算,如果有未结算批次,则该供应商不允许新增批次
        $provider_id = intval($this->input->post('provider_id'));
        $brand_id = intval($this->input->post('brand_id'));
//         $purchase_batch1 = $this->purchase_batch_model->filter(array('provider_id'=>$provider_id,'is_reckoned'=>0));
//         if($purchase_batch1){
//             sys_msg('该供应商还有批次未结算,不能继续新增批次!', 1);
//         }
        //生成batch_code
        $purchase_batch = $this->purchase_batch_model->filter_batch_code(array('batch_code like'=>'BT'. date('Ymd')."%"));
        $char_num = "";
        $batch_code = "";
        if($purchase_batch){
            $batch_code = $purchase_batch->batch_code;
            $char_num = substr($batch_code, 10);
            $int_num = intval($char_num)+1;
            
            if($int_num <= 9){
                $char_num = "00".$int_num;
            }else if($int_num <= 99){
                $char_num = "0".$int_num;
            }else if($int_num <= 999){
                $char_num = $int_num;
            }else{
                sys_msg(validation_errors(), 1);
            }
        }else{
            $char_num = "000";
        }
        $batch_code = 'BT'. date('Ymd').$char_num;
        //构造要插入的对象
		$update = array();
		$update['batch_name'] = $this->input->post('batch_name');
		$update['batch_type'] = intval($this->input->post('batch_type'));
		$update['provider_id'] = $provider_id;
		$update['brand_id'] = $brand_id;
		$update['is_consign'] = intval($this->input->post('is_consign'));
		$update['plan_num'] = intval($this->input->post('plan_num'));
		$update['plan_arrive_date'] = $this->input->post('plan_arrive_date');
		$update['batch_status'] = 1;
		$update['create_admin'] = intval($this->admin_id);
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['batch_code'] = $batch_code;
        //执行插入
		$batch_id = $this->purchase_batch_model->insert($update);
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'purchase_batch/edit/'.$batch_id), array('text'=>'返回列表','href'=>'purchase_batch')));
	}
        
        public function view($batch_id) {
            auth('purchase_batch_view');
            $purchase_batch = $this->purchase_batch_model->filter(array('batch_id'=>$batch_id));
            if (!$purchase_batch) {
                    sys_msg('记录不存在', 1);
            }
            
            $provider_list = $this->provider_model->all_provider(array('is_use'=>1),"provider_code asc");
            $this->load->vars('perm_edit', check_perm('purchase_batch_edit'));
            $this->load->vars('provider_list', $provider_list);
            $this->load->vars('brand_list', $this->brand_model->all_brand());
            $this->load->vars('row', $purchase_batch);
            $this->load->view('depot/purchase_batch/edit');
        }
    
	public function edit($batch_id)
	{
		auth('purchase_batch_edit');
		$purchase_batch = $this->purchase_batch_model->filter(array('batch_id'=>$batch_id));
		if (!$purchase_batch) {
			sys_msg('记录不存在', 1);
		}
		if($purchase_batch->batch_status==0){
		    sys_msg('批次已关闭,不能修改!', 1);
		}
		$provider_list = $this->provider_model->all_provider(array('is_use'=>1),"provider_code asc");
		$this->load->vars('perm_edit', check_perm('purchase_batch_edit'));
		$this->load->vars('provider_list', $provider_list);
		$this->load->model('provider_brand_model');
		$brand_list = $this->provider_brand_model->provider_brand_list($purchase_batch->provider_id);
		$extists = FALSE;
		if(!empty($brand_list)){
		    foreach ($brand_list as $brand){
			if($brand->brand_id == $purchase_batch->brand_id){
			    $extists = TRUE;
			    break;
			}
		    }
		}else{
		    $brand_list = array();
		}
		if(!$extists && !empty($purchase_batch->brand_id)){
		    $brand = $this->brand_model->filter(array("brand_id"=>$purchase_batch->brand_id));
		    if(!empty($brand))
		    $brand_list[] = $brand;
		}
		$this->load->vars('brand_list',$brand_list);
		$this->load->vars('row', $purchase_batch);
		$this->load->view('depot/purchase_batch/edit');
	}

	public function proc_edit()
	{
		auth('purchase_batch_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('batch_name', '批次名称', 'trim|required');
		$this->form_validation->set_rules('plan_num', '预计收货数量', 'trim|required');
		$this->form_validation->set_rules('plan_arrive_date', '预计收货日期', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['batch_name'] = $this->input->post('batch_name');
		$update['batch_type'] = intval($this->input->post('batch_type'));
		$update['provider_id'] = intval($this->input->post('provider_id'));
		$update['brand_id'] = intval($this->input->post('brand_id'));
		$update['is_consign'] = intval($this->input->post('is_consign'));
		$update['plan_num'] = $this->input->post('plan_num');
		$update['plan_arrive_date'] = $this->input->post('plan_arrive_date');
		$batch_id = intval($this->input->post('batch_id'));
		$purchase_batch = $this->purchase_batch_model->filter(array('batch_id'=>$batch_id));
		if (!$purchase_batch) {
			sys_msg('记录不存在!', 1);
		}
        if($purchase_batch->batch_status==0){
            sys_msg('批次已关闭,不能修改!', 1);
        }
        //检查当前选择的供应商是否有批次未结算,如果有未结算批次,则该供应商不允许新增批次
        $provider_id = intval($this->input->post('provider_id'));
//         $purchase_batch1 = $this->purchase_batch_model->filter(array('provider_id'=>$provider_id,'is_reckoned'=>0));
//         if($purchase_batch1){
//             sys_msg('该供应商还有批次未结算,不能继续新增批次!', 1);
//         }
		$this->purchase_batch_model->update($update, $batch_id);
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'purchase_batch/edit/'.$batch_id), array('text'=>'返回列表','href'=>'purchase_batch')));
	}
    
	public function toggle()
	{
        $result =  array();
		if(!check_perm('purchase_batch_close')){
            $result['err'] = 1;
            $result['msg'] = '没有权限!';
            echo json_encode($result);
            return;
        }
        $batch_id = intval($this->input->post('id'));
        $batch_status = intval($this->input->post('value'));
        //验证该记录
		$purchase_batch = $this->purchase_batch_model->filter(array('batch_id'=>$batch_id));
		if (!$purchase_batch) {
			$result['err'] = 1;
            $result['msg'] = '记录不存在,请刷新页面!';
            echo json_encode($result);
            return;
		}
        //定义需要修改的属性
        $update = array();
        $update['batch_status'] = $batch_status;
        $update['close_admin'] = intval($this->admin_id);
        $update['close_date'] = date('Y-m-d H:i:s');
        $this->purchase_batch_model->update($update, $batch_id);
        $result['err'] = 0;
		echo json_encode($result);
	}

	public function delete($batch_id)
	{
		auth('purchase_batch_delete');
		$test = $this->input->post('test');
        //添加验证代码，是否有采购单
		$num = $this->purchase_batch_model->get_purchase_num($batch_id);
		if($num > 0){
		    sys_msg('已存在采购单,不能删除!', 1, array(array('text'=>'返回列表', 'href'=>'purchase_batch')));
		}
		if($test) sys_msg('', 0);
		$this->purchase_batch_model->delete($batch_id);
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'purchase_batch')));
	}
	
	public function lock ($batch_id)
	{
		auth(array('purchase_batch_lock'));
		$purchase_batch = $this->purchase_batch_model->filter(array('batch_id'=>$batch_id));
		if (!$purchase_batch) {
			sys_msg('记录不存在', 1);
		}
        if($purchase_batch->batch_status==0){
            sys_msg('批次已关闭,不能锁定!', 1);
        }

		$update = array();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;
		$this->purchase_batch_model->update($update, $batch_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/purchase_batch/index')));
	}
	
	public function unlock ($batch_id)
	{
		auth(array('purchase_batch_lock'));
		$purchase_batch = $this->purchase_batch_model->filter(array('batch_id'=>$batch_id));
		if (!$purchase_batch) {
			sys_msg('记录不存在', 1);
		}

		$update = array();
		$update['lock_date'] = null;
		$update['lock_admin'] = null;
		$this->purchase_batch_model->update($update, $batch_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/purchase_batch/index')));
	}
	
	public function reckon ($batch_id)
	{
		auth(array('purchase_batch_reckon'));
		$purchase_batch = $this->purchase_batch_model->filter(array('batch_id'=>$batch_id));
		if (!$purchase_batch) {
			sys_msg('记录不存在', 1);
		}
//		if (empty($purchase_batch->lock_admin)) {
//			sys_msg('未锁定，不能设置已结算', 1);
//		}
		$provider = $this->provider_model->filter(array('provider_id'=>$purchase_batch->provider_id));
		if($provider->provider_cooperation == 1) {
			sys_msg('买断合作方式的批次不能设已结算', 1);
		}
		
		$out_num = $this->purchase_batch_model->get_out_num($batch_id);
		//echo $purchase_batch->provider_id."->";echo $out_num; die;
		if($out_num != 0) {
			sys_msg('当前批次库存不为0，不能设置已结算', 1);
		}
		
		$waiting_outin = $this->purchase_batch_model->get_waiting_outin($batch_id);
		if(!empty($waiting_outin)) {
			sys_msg('当前批次存在待入或待出，不能设置已结算', 1);
		}
		
		$order_notok = $this->purchase_batch_model->get_order_notok($batch_id);
		if(!empty($order_notok)) {
			sys_msg('当前批次存在未完结订单，不能设置已结算', 1);
		}
		
		//有调拨出无调拨入
		$transfer = $this->purchase_batch_model->get_unfinished_transfer($batch_id);
		if(!empty($transfer)) {
			sys_msg('当前批次存在未完成的调拨入库，不能设置已结算', 1);
		}
		
		$inventory_list = $this->purchase_batch_model->get_unfinished_inventory();
		foreach ($inventory_list as $inv) {
			if($inv->inventory_type == 0) { //货架范围
				$trans = $this->purchase_batch_model->get_trans_locations($batch_id,$inv->shelf_from,$inv->shelf_to);
			} else { //指定储位
				$trans = $this->purchase_batch_model->get_trans_location($batch_id,$inv->location_id);
			}
			if(!empty($trans)) {
				sys_msg('当前批次有未完成盘点单，不能设置已结算', 1);
			}
		}
		
		$this->db->query('BEGIN');
//		$insert = array();
//		$insert['batch_code'] = $purchase_batch->batch_code.'D';
//		$insert['batch_name'] = '设置已结算批次';//待定
//		$insert['related_id'] = $batch_id;
//		$insert['batch_type'] = 1;
//		$insert['provider_id'] = 12; //TODO待定
//		$insert['create_admin'] = -1;
//		$insert['create_date'] = date('Y-m-d H:i:s');
//		$this->purchase_batch_model->insert($insert);

		$update = array();
		$update['is_reckoned'] = 1;
		$update['batch_status'] = 0;
		$update['reckon_date'] = date('Y-m-d H:i:s');
		$update['reckon_admin'] = $this->admin_id;
		$update['close_date'] = date('Y-m-d H:i:s');
		//$update['lock_admin'] = $this->admin_id;
		$this->purchase_batch_model->update($update, $batch_id);
		$this->db->query('COMMIT');
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/purchase_batch/index')));
	}
	
	public function get_provider_batch($provider_id)
	{
		$batch_list = $purchase_batch = $this->purchase_batch_model->get_provider_batcch($provider_id);
		echo json_encode($batch_list);
	}
	
	public function get_batch_brand($batch_id)
	{
		$brand = $this->purchase_batch_model->get_batch_brand($batch_id);
		echo json_encode($brand);
	}
	
	public function get_provider_brand($provider_id){
		if(empty($provider_id)){
		    echo json_encode(array("result"=>0));
		    return;
		}
		$this->load->model('provider_brand_model');
		$list = $this->provider_brand_model->provider_brand_list($provider_id);
		if(empty($list)){
		     echo json_encode(array("result"=>0));
		     return;
		}
		$data = array();
		$data["result"]= count($list);
		$data["list"]= $list;
		echo json_encode($data);
	}
	
}
###
