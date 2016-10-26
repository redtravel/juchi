<?php
#doc
#	classname:	Provider
#	scope:		PUBLIC
#
#/doc

class Provider extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('provider_model');
	}
	
	public function index ()
	{
		auth('provider_view');
		$filter = $this->uri->uri_to_assoc(3);
		$provider_code = trim($this->input->post('provider_code'));
		if (!empty($provider_code)) $filter['provider_code'] = $provider_code;
		$provider_name = trim($this->input->post('provider_name'));
		if (!empty($provider_name)) $filter['provider_name'] = $provider_name;
        $filter['parent_id'] = 0;

		$filter = get_pager_param($filter);
		$data = $this->provider_model->provider_list($filter);
		//
		$this->load->model('provider_brand_model');
		foreach ($data["list"] as $item){
		     $item->provider_brand_list = $this->provider_brand_model->provider_brand_list($item->provider_id);
		}
		$this->load->vars('perm_delete', check_perm('provider_edit'));
		$this->load->vars('perm_provider_brand_setup', check_perm('provider_brand_setup'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('provider/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('provider/index', $data);
	}

	public function add($parent_id='')
	{
		auth('provider_edit');
        $this->load->model('cooperation_model');

        //获取上级供应商
        if(!empty($parent_id)){
        	$parent = $this->provider_model->filter(array('provider_id'=>$parent_id));
        	$this->load->vars('parent', $parent);
        }

        $this->load->vars('all_cooperation', $this->cooperation_model->all_cooperation());
        $this->load->vars('top_providers', $this->provider_model->all_provider(Array('parent_id'=>0)));
		$this->load->view('provider/add');
	}

	public function proc_add()
	{
		auth('provider_edit');
        $this->load->library('upload');
        $this->load->library('myupload');   
        $this->config->load('provider');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('provider_name', '名称', 'trim|required');
                //$this->form_validation->set_rules('provider_code', '代码', 'trim|required');
                $this->form_validation->set_rules('provider_cooperation', '合作方式', 'trim|required');
                $this->form_validation->set_rules('legal_provider', '法人代表', 'trim|required');
                $this->form_validation->set_rules('sales_name', '销售员', 'trim|required');
                $this->form_validation->set_rules('sales_mobile', '销售员手机号', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
        $update['provider_code'] = trim($this->input->post('provider_code'));
        // 自动生成供应商编号
		if( empty($update['provider_code']) ){
		    $update['provider_code'] = $this->provider_model->gen_provider_sn();
		}
		$update['provider_name'] = $this->input->post('provider_name');
        $update['provider_cooperation'] = $this->input->post('provider_cooperation');
        $update['official_name'] = trim($this->input->post('official_name'));
        $update['legal_provider'] = trim($this->input->post('legal_provider'));
        $update['sales_name'] = trim($this->input->post('sales_name'));
        $update['sales_mobile'] = trim($this->input->post('sales_mobile'));
		$update['provider_bank'] = trim($this->input->post('provider_bank'));
		$update['provider_account'] = trim($this->input->post('provider_account'));
		$update['tax_no'] = trim($this->input->post('tax_no'));
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');
        $update['display_name'] = trim($this->input->post('display_name'));
        $update['return_address'] = trim($this->input->post('return_address'));
        $update['return_postcode'] = trim($this->input->post('return_postcode'));
        $update['return_consignee'] = trim($this->input->post('return_consignee'));
        $update['return_mobile'] = trim($this->input->post('return_mobile'));
        $sms_price = trim($this->input->post('sms_price'));
        $update['sms_price'] = $sms_price <= 0 ? DEFAULT_SMS_PRICE : $sms_price;
        //$update['shipping_fee_config'] = json_encode($this->config->item('provider_shipping_config'));
        $check_provider_code = $this->provider_model->filter(array('provider_code'=>$update['provider_code']));
		if ($check_provider_code) {
			sys_msg('代码重复', 1);
		}
		$provider = $this->provider_model->filter(array('provider_name'=>$update['provider_name']));
		if ($provider) {
			sys_msg('名称重复', 1);
		}
                
        // 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'provider/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		$parent_id = trim($this->input->post('parent_id'));
		$update['parent_id'] = $parent_id;
		$provider_id = $this->provider_model->insert($update);
                
		if($this->upload->do_upload('logo')){
			$file = $this->upload->data();
			$this->provider_model->update(array('logo'=>'provider/'.$file['file_name']), $provider_id);
		}
        //资质图片
        $this->myupload->initialize(array(
            'upload_path' => CREATE_IMAGE_PATH.'provider/',
            'allowed_types' => 'gif|jpg|png',
            'encrypt_name' => TRUE
        ));
        for ($j = 1; $j <= 4; $j++) {
            $aptitude_img = 'aptitude_img'.$j;
            if ($this->myupload->do_multi_upload($aptitude_img)) {
                $file1 = $this->myupload->get_multi_upload_data();
                $file_name = array();
                foreach ($file1[$aptitude_img] as $key => $val) {
                    $file_name[] = 'provider/'.$val['file_name'];
                }
                $file_name_encode = json_encode($file_name);
                $this->provider_model->update(array($aptitude_img =>$file_name_encode), $provider_id);
            }
        }
		
		if(!empty($parent_id)){
        	sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'provider/scm_edit/'.$provider_id."/".$parent_id), array('text'=>'返回列表','href'=>'provider/scm_index/'.$parent_id)));
        }else{
        	sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'provider/scm_edit/'.$provider_id), array('text'=>'返回列表','href'=>'provider/index')));        	
        }
	}

	public function edit($provider_id,$parent_id='')
	{
		auth(array('provider_edit','provider_view'));
		$provider = $this->provider_model->filter(array('provider_id'=>$provider_id));

		if(!empty($parent_id)){
        	$parent = $this->provider_model->filter(array('provider_id'=>$parent_id));
        	$this->load->vars('parent', $parent);
        }

		if (!$provider) {
			sys_msg('记录不存在', 1);
		}
                $this->load->model('cooperation_model');
		$this->load->vars('row', $provider);
                $this->load->vars('all_cooperation', $this->cooperation_model->all_cooperation());
		$this->load->vars('perm_edit', check_perm('provider_edit'));
		$this->load->view('provider/edit');
	}

	public function proc_edit()
	{
		auth('provider_edit');
        $this->load->library('upload');
        $this->load->library('myupload');	
		$this->load->library('form_validation');
        $this->form_validation->set_rules('provider_name', '名称', 'trim|required');
        $this->form_validation->set_rules('legal_provider', '法人代表', 'trim|required');
        $this->form_validation->set_rules('sales_name', '销售员', 'trim|required');
        $this->form_validation->set_rules('sales_mobile', '销售员手机号', 'trim|required');
        if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['provider_name'] = $this->input->post('provider_name');
		$update['official_name'] = trim($this->input->post('official_name'));
        $update['legal_provider'] = trim($this->input->post('legal_provider'));
        $update['sales_name'] = trim($this->input->post('sales_name'));
        $update['sales_mobile'] = trim($this->input->post('sales_mobile'));
		$update['provider_bank'] = trim($this->input->post('provider_bank'));
		$update['provider_account'] = trim($this->input->post('provider_account'));
		$update['tax_no'] = trim($this->input->post('tax_no'));
		$update['is_use'] = intval($this->input->post('is_use'));
        $update['display_name'] = trim($this->input->post('display_name'));
        $update['return_address'] = trim($this->input->post('return_address'));
        $update['return_postcode'] = trim($this->input->post('return_postcode'));
        $update['return_consignee'] = trim($this->input->post('return_consignee'));
        $update['return_mobile'] = trim($this->input->post('return_mobile'));

		$provider_id = intval($this->input->post('provider_id'));
		$provider = $this->provider_model->filter(array('provider_id'=>$provider_id));

		if (!$provider) {
			sys_msg('记录不存在!', 1);
		}
                
		$check_provider = $this->provider_model->filter(array('provider_name'=>$update['provider_name'], 'provider_id !='=>$provider_id));
		if ($check_provider) {
			sys_msg('名称重复', 1);
		}

		$parent_id = trim($this->input->post('parent_id'));               
		$this->provider_model->update($update, $provider_id);
                
        // 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'provider/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if ($this->upload->do_upload('logo')) {
			$file = $this->upload->data();
			if($provider->logo) @unlink(CREATE_IMAGE_PATH.$provider->logo);
			$this->provider_model->update(array('logo'=>'provider/'.$file['file_name']), $provider_id);
		}

        //资质图片
        $this->myupload->initialize(array(
            'upload_path' => CREATE_IMAGE_PATH.'provider/',
            'allowed_types' => 'gif|jpg|png',
            'encrypt_name' => TRUE
        ));
        $provider = get_object_vars($provider);
        for ($j = 1; $j <= 4; $j++) {
            $aptitude_img = 'aptitude_img'.$j;
            if ($this->myupload->do_multi_upload($aptitude_img)) {
                $file1 = $this->myupload->get_multi_upload_data();
                $file_name = array();
                foreach ($file1[$aptitude_img] as $key => $val) {
                    $file_name[] = 'provider/'.$val['file_name'];
                }
                $file_name_encode = json_encode($file_name);

                if($provider[$aptitude_img]){
                    $file_name_decode = json_decode($provider[$aptitude_img]);
                    for ($i=0; $i < count($file_name_decode); $i++) { 
                        @unlink(CREATE_IMAGE_PATH.$file_name_decode[$i]);
                    }                
                }
                $this->provider_model->update(array($aptitude_img =>$file_name_encode), $provider_id);
            }
        }     

		if(!empty($parent_id)){
        	sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'provider/edit/'.$provider_id."/".$parent_id), array('text'=>'返回列表','href'=>'provider/scm_index/'.$parent_id)));
        }else{
        	sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'provider/edit/'.$provider_id), array('text'=>'返回列表','href'=>'provider/index')));        	
        }
	}

	public function delete($provider_id)
	{
		auth('provider_edit');
		$this->load->model('product_model');
		$test = $this->input->post('test');
		$product = $this->product_model->filter(array('provider_id'=>$provider_id));
		if($product) sys_msg('该供应商不能删除', 1);
		if($test) sys_msg('',0);
		$this->provider_model->delete($provider_id);
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'provider/index')));		
	}

	public function toggle()
	{
		auth('provider_edit');
		$result = proc_toggle('provider_model','provider_id',array('is_use'));
		print json_encode($result);
	}
    
    /**
     * 供应商运费配置
     * @param type $provider_id
     */
    public function shipping($provider_id)
    {
        $this->config->load('provider');
        $this->load->model('region_model');
        auth(array('provider_edit','provider_view'));
		$provider = $this->provider_model->filter(array('provider_id'=>$provider_id));
		if (!$provider) {
			sys_msg('记录不存在', 1);
		}
        $all_region = $this->region_model->all_region(array('region_type'=>1));
        $shipping_fee_config = $this->provider_model->get_shipping_fee_config($provider_id);
        foreach($all_region as &$region)
        {
            $region->shipping_fee = $shipping_fee_config[$region->region_id][0];
            $region->free_price = $shipping_fee_config[$region->region_id][1];
        }
        unset($region);
		$this->load->vars('row', $provider);
        $this->load->vars('all_region', $all_region);
		$this->load->vars('perm_edit', check_perm('provider_edit'));
		$this->load->view('provider/shipping');
    }
    
    /**
     * 提交运费设置
     */
    public function proc_shipping() {
        auth('provider_edit');
        $this->load->model('region_model');
        $provider_shipping_config = array();
        $provider_id = intval($this->input->post('provider_id'));
        $provider = $this->provider_model->filter(array('provider_id' => $provider_id));
        if (!$provider) {
            sys_msg('记录不存在!', 1);
        }
        $all_region = $this->region_model->all_region(array('region_type'=>1));
        $default_shipping_config = $this->config->item('provider_shipping_config');
        foreach($all_region as $region)
        {
            $region_id = intval($region->region_id);
            $shipping_fee = $this->input->post('shipping_fee_'.$region_id);
            $free_price = $this->input->post('free_price_'.$region_id);
            if($shipping_fee===FALSE){
                $shipping_fee = floatval($default_shipping_config[$region_id][0]);
            }else{
                $shipping_fee = floatval($shipping_fee);
            }
            if($free_price===FALSE){
                $free_price = floatval($default_shipping_config[$region_id][1]);
            }else{
                $free_price = floatval($free_price);
            }
            $provider_shipping_config[] = array(
                'regionId' => $region_id,
                'fee' => $shipping_fee,
                'price' => $free_price
            );
        }
        $update['shipping_fee_config'] = json_encode($provider_shipping_config);
        $this->provider_model->update($update, $provider_id);
        
        sys_msg('操作成功', 0, array(array('text' => '继续编辑', 'href' => 'provider/shipping/' . $provider_id), array('text' => '返回列表', 'href' => 'provider/index')));
    }
    
    public function scm_edit($provider_id,$parent_id='') {
        auth(array('provider_edit','provider_view'));

        $provider = $this->provider_model->filter(array('provider_id'=>$provider_id));
        if(!empty($parent_id)){
        	$parent = $this->provider_model->filter(array('provider_id'=>$parent_id));
        	$this->load->vars('parent', $parent);
        }
        if (!$provider) {
            sys_msg('记录不存在', 1);
        }

        $this->load->vars('row', $provider);
        $this->load->vars('perm_edit', check_perm('provider_edit'));

        $this->load->model('region_model');

        //获取所辖地区
        $region = explode(',',$provider->send_country);
        $region = array_merge($region,explode(',',$provider->send_province));
        $region = array_merge($region,explode(',',$provider->send_city));
        $region = array_merge($region,explode(',',$provider->send_district));
        $reg = array();
        foreach ($region as $value) {
        	$reg = array_merge($reg,$this->region_model->all_data(array('region_id'=>$value)));
        }
        $this->load->vars('all_region', $reg);

        //展示所有省份
        $province = $this->region_model->all_region(array('region_type' => 1));
        $this->load->vars('province', $province);

        $this->load->view('provider/scm_edit');
    }
    
    public function proc_scm_edit() {
        auth('provider_edit');
        
        $provider_id = intval($this->input->post('provider_id'));
        $parent_id = intval($this->input->post('parent_id'));

        $provider = $this->provider_model->filter(array('provider_id'=>$provider_id));
        if (!$provider) {
            sys_msg('记录不存在!', 1);
        }
        
        $update = array();
        $update['provider_status'] = trim($this->input->post('provider_status'));
        $update['user_name'] = $this->input->post('user_name');
        $password = trim($this->input->post('password'));
        if (!empty($password)) {
            $update['password'] = md5($password);
        }
        $update['official_name'] = trim($this->input->post('official_name'));
        $update['official_address'] = trim($this->input->post('official_address'));
        $update['provider_bank'] = trim($this->input->post('provider_bank'));
        $update['provider_account'] = trim($this->input->post('provider_account'));
        $update['provider_cess'] = fix_price($this->input->post('provider_cess'));
        $update['scm_responsible_user'] = trim($this->input->post('scm_responsible_user'));
        $update['scm_responsible_phone'] = trim($this->input->post('scm_responsible_phone'));
        $update['scm_responsible_qq'] = trim($this->input->post('scm_responsible_qq'));
        $update['scm_responsible_mail'] = trim($this->input->post('scm_responsible_mail'));
        $update['scm_order_process_user'] = trim($this->input->post('scm_order_process_user'));
        $update['scm_order_process_phone'] = trim($this->input->post('scm_order_process_phone'));
        $update['scm_order_process_qq'] = trim($this->input->post('scm_order_process_qq'));
        $update['scm_order_process_mail'] = trim($this->input->post('scm_order_process_mail'));
        $update['return_address'] = trim($this->input->post('return_address'));
        $update['return_postcode'] = trim($this->input->post('return_postcode'));
        $update['return_consignee'] = trim($this->input->post('return_consignee'));
        $update['return_mobile'] = trim($this->input->post('return_mobile'));
        $sms_price = trim($this->input->post('sms_price'));
        $update['sms_price'] = $sms_price <= 0 ? DEFAULT_SMS_PRICE : $sms_price;

        $area = $this->input->post('area');

        $this->load->model('region_model');

        $areas = array();
        $areas['send_country']=array();
        $areas['send_province']=array();
        $areas['send_city']=array();
        $areas['send_district']=array();
        $ary = array( 'send_country', 'send_province', 'send_city', 'send_district' ); //先后顺序要保持
        if(!empty($area)){
        	$regions = $this->region_model->get_specified_region($area);
        	$regions = get_pair( $regions, 'region_id', 'region_type');

	        foreach ($area as $value) {
	        	$areas[$ary[$regions[$value]]][] = $value;
	        }
	        // 将选择的区域，组成字符串
	       $areas =  array_map(array($this,'local_implode'),$areas);
	       // 将数据还给update
	       $update = array_merge($update,$areas);
        }

        $this->provider_model->update($update, $provider_id);
        if(!empty($parent_id)){
        	sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'provider/scm_edit/'.$provider_id."/".$parent_id), array('text'=>'返回列表','href'=>'provider/scm_index/'.$parent_id)));
        }else{
        	sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'provider/scm_edit/'.$provider_id), array('text'=>'返回列表','href'=>'provider/index')));        	
        }

    }
    private  function local_implode($ary){
	 	if( empty($ary)) return null;
	    	return implode(',',$ary);
	 }

    public function scm_index($provider_id = 0) {
        auth(array('provider_edit','provider_view'));
        
        $provider = $this->provider_model->filter(array('provider_id'=>$provider_id));
        if (!$provider) {
            sys_msg('记录不存在', 1);
        }
		$filter = $this->uri->uri_to_assoc(3);
		$provider_code = trim($this->input->post('provider_code'));
		if (!empty($provider_code)) $filter['provider_code'] = $provider_code;
		$provider_name = trim($this->input->post('provider_name'));
		if (!empty($provider_name)) $filter['provider_name'] = $provider_name;
        $filter['parent_id'] = $provider_id;

		$filter = get_pager_param($filter);
		$data = $this->provider_model->provider_list($filter);
		//
		$this->load->model('provider_brand_model');
		foreach ($data["list"] as $item){
		     $item->provider_brand_list = $this->provider_brand_model->provider_brand_list($item->provider_id);
		}
		$this->load->vars('perm_delete', check_perm('provider_edit'));
		$this->load->vars('perm_provider_brand_setup', check_perm('provider_brand_setup'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('provider/scm_index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
        $this->load->view('provider/scm_index', $data);
    }

}
###
