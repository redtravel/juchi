<?php
/**
* 礼包
*/
class Package_discount extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if (!$this->admin_id) redirect('index/login');
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('package_discount_model');
		$this->load->helper('package');
		$this->config->load('package');
		$this->all_type = $this->config->item('package_discount_status');
		$this->all_status = $this->config->item('package_all_status');
	}

	public function index()
	{
		auth('package_discount_view');
		$this->load->helper('product');
		$filter = $this->uri->uri_to_assoc(3);
		$filter['pag_dis_name'] = trim($this->input->post('pag_dis_name'));
		if (FALSE!==$this->input->post('pag_dis_type')) $filter['pag_dis_type'] = intval($this->input->post('pag_dis_type'));
		if (FALSE!==$this->input->post('pag_dis_status')) $filter['pag_dis_status'] = intval($this->input->post('pag_dis_status'));
		$filter['start_time'] = trim($this->input->post('start_time'));
		$filter['end_time'] = trim($this->input->post('end_time'));
		$filter['product_sn'] = trim($this->input->post('product_sn'));

		$filter = get_pager_param($filter);
		$data = $this->package_discount_model->package_discount_list($filter);
		$data['all_type'] = $this->all_type;
		$data['all_status'] = $this->all_status;
		$this->load->vars('perm_delete', check_perm('package_discount_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('package_discount/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('package_discount/index', $data);
	}

	public function add()
	{
		auth('package_discount_edit');
		$this->load->vars('all_type', $this->all_type);
		$this->load->view('package_discount/add');
	}

	public function proc_add()
	{
		auth('package_discount_edit');
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('pag_dis_name', '礼包名称', 'trim|required');
		$this->form_validation->set_rules('start_date', '开始时间', 'trim|required');
		$this->form_validation->set_rules('end_date', '结束时间', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['pag_dis_type'] = intval($this->input->post('pag_dis_type'));
		$update['pag_dis_name'] = $this->input->post('pag_dis_name');
		$update['start_date'] = $this->input->post('start_date');
		$update['start_time'] = trim($this->input->post('start_time'));
		$update['end_date'] = $this->input->post('end_date');
		$update['end_time'] = trim($this->input->post('end_time'));	
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['pag_dis_desc'] = trim($this->input->post('pag_dis_desc'));
		$update['create_date'] = $this->time;
		$update['create_admin'] = $this->admin_id;

		if(!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $update['start_date'])
		   ||!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $update['end_date'])
		   ||($update['start_time'] && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $update['start_time']))
		   ||($update['end_time'] && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $update['end_time']))
		)sys_msg('开始结束时间格式错误');

		$update['start_time'] = $update['start_date'].' '.$update['start_time'];
		unset($update['start_date']);
		$update['end_time'] = $update['end_date'].' '.$update['end_time'];
		unset($update['end_date']);

		$pag_dis_id = $this->package_discount_model->insert($update);
		// 上传图片
		$update = array();
		$base_path = CREATE_IMAGE_PATH.'package_discount/';
		$sub_dir = ($pag_dis_id-$pag_dis_id%100)/100;
		if(!file_exists($base_path.$sub_dir)) mkdir($base_path.$sub_dir);

		$this->upload->initialize(array(
			'upload_path' => $base_path.$sub_dir,
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if($this->upload->do_upload('pag_dis_image')){
			$file = $this->upload->data();
			$update['pag_dis_image'] = 'package_discount/'.$sub_dir.'/'.$file['file_name'];
		}
		if($this->upload->do_upload('pag_dis_homepage_image')){
			$file = $this->upload->data();
			$update['pag_dis_homepage_image'] = 'package_discount/'.$sub_dir.'/'.$file['file_name'];
		}
		if($this->upload->do_upload('pag_dis_wechat_image')){
			$file = $this->upload->data();
			$update['pag_dis_wechat_image'] = 'package_discount/'.$sub_dir.'/'.$file['file_name'];
		}
		if ($update) {
			$this->package_discount_model->update($update, $pag_dis_id);
		}

		sys_msg('操作成功',0, array(array('text'=>'继续编辑', 'href'=>'package_discount/edit/'.$pag_dis_id), array('text'=>'返回列表', 'href'=>'package_discount')));

	}

	public function edit($pag_dis_id)
	{
		auth(array('package_discount_edit','package_discount_view'));
		$this->load->model('admin_model');
		$this->load->model('style_model');
		$this->load->model('season_model');
		$this->load->helper('package');
		$this->load->helper('product');
		$this->load->library('ckeditor');

		$pag_dis_id = intval($pag_dis_id);
		$package = $this->package_discount_model->filter(array('pag_dis_id'=>$pag_dis_id));
		if(!$package) sys_msg('记录不存在', 1);

		$package->create_admin_name = ($package->create_admin && $admin = $this->admin_model->filter(array('admin_id'=>$package->create_admin))) ?  $admin->admin_name:'';

		$package->check_admin_name = ($package->check_admin && $admin = $this->admin_model->filter(array('admin_id'=>$package->check_admin))) ?  $admin->admin_name:'';

		$package->over_admin_name = ($package->over_admin && $admin = $this->admin_model->filter(array('admin_id'=>$package->over_admin))) ?  $admin->admin_name:'';

		$main_product = $this->package_discount_model->package_discount_product($pag_dis_id,$dis_pro_type = 0); //主产品
		$discount_product = $this->package_discount_model->package_discount_product($pag_dis_id,$dis_pro_type = 1); //折扣产品

		attach_gallery($main_product);
		attach_sub($main_product);
		attach_gallery($discount_product);
		attach_sub($discount_product);

		$this->load->vars('package', $package);
		$this->load->vars('all_type', $this->all_type);
		$this->load->vars('all_status', $this->all_status);
		$this->load->vars('perms', get_package_discount_perm($package));
		$this->load->vars('main_product', $main_product);
		$this->load->vars('discount_product', $discount_product);
		// 商品信息取
		$this->load->view('package_discount/edit'); 


	}

	public function proc_edit()
	{
		auth('package_discount_edit');
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->load->helper('package');
		
		$this->form_validation->set_rules('pag_dis_name', '礼包名称', 'trim|required');
		$this->form_validation->set_rules('start_date', '开始时间', 'trim|required');
		$this->form_validation->set_rules('end_date', '结束时间', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}

		$pag_dis_id = intval($this->input->post('pag_dis_id'));
		$this->db->trans_begin();
		$this->package_discount_model->lock_package($pag_dis_id);
		
		$package = $this->package_discount_model->filter(array('pag_dis_id'=>$pag_dis_id));
		if(!$package) sys_msg('记录不存在', 1);
		$perms = get_package_discount_perm($package);
		if (!$perms['edit']) sys_msg('没有权限或当前不可编辑', 1);

		$update = array();
		$update['pag_dis_name'] = $this->input->post('pag_dis_name');
		$update['start_date'] = $this->input->post('start_date');
		$update['start_time'] = trim($this->input->post('start_time'));
		$update['end_date'] = $this->input->post('end_date');
		$update['end_time'] = trim($this->input->post('end_time'));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['pag_dis_desc'] = trim($this->input->post('pag_dis_desc'));
		if(!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $update['start_date'])
		   ||!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $update['end_date'])
		   ||($update['start_time'] && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $update['start_time']))
		   ||($update['end_time'] && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $update['end_time']))
		)sys_msg('开始结束时间格式错误');

		$update['start_time'] = $update['start_date'].' '.$update['start_time'];
		unset($update['start_date']);
		$update['end_time'] = $update['end_date'].' '.$update['end_time'];
		unset($update['end_date']);

		$base_path = CREATE_IMAGE_PATH.'package_discount/';
		if($this->input->post('delete_pag_dis_image') && $package->pag_dis_image) {
			@unlink(CREATE_IMAGE_PATH.$package->pag_dis_image);
			$update['pag_dis_image'] = '';
		}
		if($this->input->post('delete_pag_dis_homepage_image') && $package->pag_dis_homepage_image) {
			@unlink(CREATE_IMAGE_PATH.$package->pag_dis_homepage_image);
			$update['pag_dis_homepage_image'] = '';
		}
		if($this->input->post('delete_pag_dis_wechat_image') && $package->pag_dis_wechat_image) {
			@unlink(CREATE_IMAGE_PATH.$package->pag_dis_wechat_image);
			$update['pag_dis_wechat_image'] = '';
		}

		$sub_dir = ($pag_dis_id-$pag_dis_id%100)/100;
		if(!file_exists($base_path)) mkdir($base_path, 0700, true);
	        if(!file_exists($base_path.'/'.$sub_dir)) mkdir($base_path.'/'.$sub_dir, 0700, true);
		$this->upload->initialize(array(
				'upload_path'=> $base_path.$sub_dir,
				'allowed_types' => 'jpg|gif|png',
				'encrypt_name' => TRUE
			));
		if($this->upload->do_upload('pag_dis_image')){
			$file = $this->upload->data();
			$update['pag_dis_image'] = 'package_discount/'.$sub_dir.'/'.$file['file_name'];
			if($package->pag_dis_image) @unlink(CREATE_IMAGE_PATH.$package->pag_dis_image);
		}
		if($this->upload->do_upload('pag_dis_homepage_image')){
			$file = $this->upload->data();
			$update['pag_dis_homepage_image'] = 'package_discount/'.$sub_dir.'/'.$file['file_name'];
			if($package->pag_dis_homepage_image) @unlink(CREATE_IMAGE_PATH.$package->pag_dis_homepage_image);
		}
		if($this->upload->do_upload('pag_dis_wechat_image')){
			$file = $this->upload->data();
			$update['pag_dis_wechat_image'] = 'package_discount/'.$sub_dir.'/'.$file['file_name'];
			if($package->pag_dis_wechat_image) @unlink(CREATE_IMAGE_PATH.$package->pag_dis_wechat_image);
		}

		$this->package_discount_model->update($update, $pag_dis_id);
		$this->db->trans_commit();
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'package_discount/edit/'.$pag_dis_id), array('text'=>'返回列表','href'=>'package_discount/index')));
	}

	public function delete($pag_dis_id)
	{
		auth('package_discount_edit');
		$test = $this->input->post('test');
		$pag_dis_id = intval($pag_dis_id);
		// start transaction
		if(!$test) {
			$this->db->trans_begin();
			$package_discount = $this->package_discount_model->lock_package($pag_dis_id);
		}else{
			$package_discount = $this->package_discount_model->filter(array('pag_dis_id'=>$pag_dis_id));
		}		
		
		if(!$package_discount) sys_msg('记录不存在', 1);
		$perms = get_package_discount_perm($package_discount);
		if (!$perms['delete']) sys_msg('没有权限或当前不能删除', 1);
		if($test) sys_msg('', 0);

		$this->package_discount_model->delete_discount_product_where(array('pag_dis_id'=>$pag_dis_id));
		$this->package_discount_model->delete($pag_dis_id);
		$this->db->trans_commit();
		// end transaction
		$base_path = CREATE_IMAGE_PATH.'package_discount/';
		if($package_discount->pag_dis_image) @unlink(CREATE_IMAGE_PATH.$package_discount->pag_dis_image);
		if($package_discount->pag_dis_homepage_image) @unlink(CREATE_IMAGE_PATH.$package_discount->pag_dis_homepage_image);
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'package_discount')));
	}

	public function operate()
	{
		auth('package_discount_edit');
		$pag_dis_id = intval($this->input->post('pag_dis_id'));
		// start transaction
		$this->db->trans_begin();
		$this->package_discount_model->lock_package($pag_dis_id);
		$package = $this->package_discount_model->filter(array('pag_dis_id'=>$pag_dis_id));
		if(!$package) sys_msg('记录不存在', 1);
		$perms = get_package_discount_perm($package);

		switch (trim($this->input->post('op'))) {
			case 'check':
				if(!$perms['check']) sys_msg('没有权限或当前不可启用', 1);
				$update = array(
					'pag_dis_status' => 1,
					'check_admin' => $this->admin_id,
					'check_date' => $this->time
					);
				$this->package_discount_model->update($update, $pag_dis_id);
				break;
			
			case 'over':
				if(!$perms['over']) sys_msg('没有权限或当前不可停用', 1);
				$update = array(
					'pag_dis_status' => 2,
					'over_admin' => $this->admin_id,
					'over_date' => $this->time
					);
				$update['over_note'] = trim($this->input->post('over_note', TRUE));
				if(!$update['over_note']) sys_msg('请填写停用说明', 1);
				$this->package_discount_model->update($update, $pag_dis_id);
				break;
			
			default:
				sys_msg('参数错误', 1);
				break;
		}
		$this->db->trans_commit();
		// end transaction
		sys_msg('操作成功', 0);
	}

	public function toggle()
	{
		auth('package_discount_edit');
		$result = proc_toggle('package_discount_model','pag_dis_id',array('is_recommend'));
		print json_encode($result);
	}


	public function product_search()
	{
		auth('package_discount_edit');
		$this->load->helper('product');
		$filter = array();

		$filter['pag_dis_id'] = intval($this->input->post('pag_dis_id'));
		$filter['dis_pro_type'] = trim($this->input->post('dis_pro_type'));

		$product_sn = trim($this->input->post('product_sn'));
		if ($product_sn) $filter['product_sn'] = $product_sn;

		$product_name = trim($this->input->post('product_name'));
		if ($product_name) $filter['product_name'] = $product_name;

		$provider_productcode = trim($this->input->post('provider_productcode'));
		if ($provider_productcode) $filter['provider_productcode'] = $provider_productcode;

		$filter = get_pager_param($filter);
		$data = $this->package_discount_model->product_search($filter);
		attach_gallery($data['list']);
		attach_sub($data['list']);
		$data['dis_pro_type'] = $filter['dis_pro_type'];
		$data['pag_dis_id'] = $filter['pag_dis_id'];

		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('package_discount/product_search', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		
		$this->load->view('package_discount/product_search', $data);
	}

	public function add_product()
	{
		auth('package_discount_edit');
		$this->load->helper('product');

		$pag_dis_id = intval($this->input->post('pag_dis_id'));
		$dis_pro_type = intval($this->input->post('dis_pro_type'));
		$product = trim($this->input->post('product'));
		$package = $this->package_discount_model->filter(array('pag_dis_id'=>$pag_dis_id));
		if(!$package) sys_msg('礼包不存在', 1);
		$perms = get_package_discount_perm($package);
		if(!$perms['edit']) sys_msg('没有权限', 1);
		if($dis_pro_type == 1) {
			$discount = $this->package_discount_model->filter_discount_product(array('pag_dis_id'=>$pag_dis_id, 'dis_pro_type'=>0 ));
			if(!$discount) sys_msg('主产品不存在', 1);
		}elseif($dis_pro_type == 0){
			$discount = $this->package_discount_model->filter_discount_product(array('pag_dis_id'=>$pag_dis_id, 'dis_pro_type'=>0 ));
			if($discount) sys_msg('请先移除主产品,在添加', 1);
		}

		$product_ids = rtrim($product, ',');
		
		if(!$product_ids) sys_msg('请选择商品', 1);
		
		$products = $this->package_discount_model->search_product($product_ids);

		$update = array('pag_dis_id'=>$pag_dis_id, 'dis_pro_type'=>$dis_pro_type, 'create_admin'=>$this->admin_id, 'create_date' => $this->time);
		foreach($products as $product){
			$update['product_id'] = $product->product_id;
			$update['sub_id'] = $product->sub_id;
			$this->package_discount_model->insert_product($update);
		}
		if($dis_pro_type == 0){
			$discount_product = $this->package_discount_model->package_discount_product($pag_dis_id,$dis_pro_type); //主产品
		}elseif($dis_pro_type == 1){
			$discount_product = $this->package_discount_model->package_discount_product($pag_dis_id,$dis_pro_type); //折扣产品
		}

		attach_gallery($discount_product);
		attach_sub($discount_product);

		$this->load->vars('perms', get_package_discount_perm($package));
		
		$result = array('err'=>0,'msg'=>'');
		if($dis_pro_type == 0){
			$this->load->vars('main_product', $discount_product); 
			$result['data'] = $this->load->view('package_discount/main_product_list','',TRUE);
		}elseif($dis_pro_type == 1){	
			$this->load->vars('discount_product', $discount_product); 
			$result['data'] = $this->load->view('package_discount/product_list','',TRUE);
		}

		print json_encode($result);
	}

	public function delete_product()
	{
		auth('package_discount_edit');
		$pag_dis_id = intval($this->input->post('pag_dis_id'));
		$dis_pro_id = intval($this->input->post('dis_pro_id'));
		$package = $this->package_discount_model->filter(array('pag_dis_id'=>$pag_dis_id));
		if(!$package) sys_msg('礼包不存在', 1);
		$perms = get_package_discount_perm($package);
		if(!$perms['edit']) sys_msg('没有权限', 1);
		$product = $this->package_discount_model->filter_discount_product(array('dis_pro_id'=>$dis_pro_id,'pag_dis_id'=>$pag_dis_id));
		if(!$product) sys_msg('记录不存在', 1);
		$this->package_discount_model->delete_product($dis_pro_id);
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function edit_product_field()
	{
		auth('package_discount_edit');
		switch (trim($this->input->post('field'))) {
			case 'sort_order':
				$val = intval($this->input->post('val'));
				break;
			case 'discount_price':
				$val = trim($this->input->post('val'));
				break;			
			default:
				$val = NULL;
				break;
		}
		print(json_encode(proc_edit('package_discount_model', 'dis_pro_id', array('sort_order','discount_price'), $val, 'filter_discount_product', 'update_product')));
		return;
	}


	public function edit_field()
	{
		auth('package_edit');
		switch (trim($this->input->post('field'))) {
			case 'sort_order':
				$val = intval($this->input->post('val'));
				break;
			
			default:
				$val = NULL;
				break;
		}
		print(json_encode(proc_edit('package_discount_model', 'pag_dis_id', array('sort_order'), $val)));
		return;
	}

}
