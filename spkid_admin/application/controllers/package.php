<?php
/**
* 礼包
*/
class Package extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if (!$this->admin_id) redirect('index/login');
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('package_model');
		$this->load->helper('package');
		$this->config->load('package');
		$this->all_type = $this->config->item('package_all_type');
		$this->all_status = $this->config->item('package_all_status');
	}

	public function index()
	{
		auth('package_view');
		$this->load->helper('product');
		$filter = $this->uri->uri_to_assoc(3);
		$filter['package_name'] = trim($this->input->post('package_name'));
		if (FALSE!==$this->input->post('package_type')) $filter['package_type'] = intval($this->input->post('package_type'));
		if (FALSE!==$this->input->post('package_status')) $filter['package_status'] = intval($this->input->post('package_status'));
		if (FALSE!==$this->input->post('is_recommend')) $filter['is_recommend'] = intval($this->input->post('is_recommend'));
		$filter['start_time'] = trim($this->input->post('start_time'));
		$filter['end_time'] = trim($this->input->post('end_time'));
		$filter['product_sn'] = trim($this->input->post('product_sn'));

		$filter = get_pager_param($filter);
		$data = $this->package_model->package_list($filter);
		$data['all_type'] = $this->all_type;
		$data['all_status'] = $this->all_status;
		$this->load->vars('perm_delete', check_perm('package_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('package/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('package/index', $data);
	}

	public function add()
	{
		auth('package_edit');
		$this->load->vars('all_type', $this->all_type);
		$this->load->view('package/add');
	}

	public function proc_add()
	{
		auth('package_edit');
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('package_name', '礼包名称', 'trim|required');
		$this->form_validation->set_rules('start_date', '开始时间', 'trim|required');
		$this->form_validation->set_rules('end_date', '结束时间', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['package_type'] = intval($this->input->post('package_type'));
		$update['package_name'] = $this->input->post('package_name');
		$update['start_date'] = $this->input->post('start_date');
		$update['start_time'] = trim($this->input->post('start_time'));
		$update['end_date'] = $this->input->post('end_date');
		$update['end_time'] = trim($this->input->post('end_time'));
		$update['is_empty'] = intval($this->input->post('is_empty'));
		$update['is_liuyan'] = intval($this->input->post('is_liuyan'));
		$update['is_recommend'] = intval($this->input->post('is_recommend'));		
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['package_desc'] = trim($this->input->post('package_desc'));
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

		$package_id = $this->package_model->insert($update);
		// 上传图片
		$update = array();
		$base_path = CREATE_IMAGE_PATH.'package/';
		$sub_dir = ($package_id-$package_id%100)/100;
		if(!file_exists($base_path.$sub_dir)) mkdir($base_path.$sub_dir);

		$this->upload->initialize(array(
			'upload_path' => $base_path.$sub_dir,
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if($this->upload->do_upload('package_image')){
			$file = $this->upload->data();
			$update['package_image'] = 'package/'.$sub_dir.'/'.$file['file_name'];
		}
		if($this->upload->do_upload('package_homepage_image')){
			$file = $this->upload->data();
			$update['package_homepage_image'] = 'package/'.$sub_dir.'/'.$file['file_name'];
		}
		if($this->upload->do_upload('package_wechat_image')){
			$file = $this->upload->data();
			$update['package_wechat_image'] = 'package/'.$sub_dir.'/'.$file['file_name'];
		}
		if ($update) {
			$this->package_model->update($update, $package_id);
		}

		sys_msg('操作成功',0, array(array('text'=>'继续编辑', 'href'=>'package/edit/'.$package_id), array('text'=>'返回列表', 'href'=>'package')));

	}

	public function edit($package_id)
	{
		auth(array('package_edit','package_view'));
		$this->load->model('admin_model');
		$this->load->model('style_model');
		$this->load->model('season_model');
		$this->load->helper('package');
		$this->load->helper('product');
		$this->load->library('ckeditor');

		$package_id = intval($package_id);
		$package = $this->package_model->filter(array('package_id'=>$package_id));
		if(!$package) sys_msg('记录不存在', 1);

		$package->package_other_config = unpack_package_config($package->package_other_config);

		$package->create_admin_name = ($package->create_admin && $admin = $this->admin_model->filter(array('admin_id'=>$package->create_admin))) ?  $admin->admin_name:'';

		$package->check_admin_name = ($package->check_admin && $admin = $this->admin_model->filter(array('admin_id'=>$package->check_admin))) ?  $admin->admin_name:'';

		$package->over_admin_name = ($package->over_admin && $admin = $this->admin_model->filter(array('admin_id'=>$package->over_admin))) ?  $admin->admin_name:'';

		$area_list = $this->package_model->all_area(array('package_id'=>$package->package_id));

		$package_product = $this->package_model->package_product($package_id);
		attach_gallery($package_product);
		attach_sub($package_product);
		$package_product = split_area_product($area_list, $package_product);

		$this->load->vars('package', $package);
		$this->load->vars('all_type', $this->all_type);
		$this->load->vars('all_status', $this->all_status);
		$this->load->vars('perms', get_package_perm($package));
		$this->load->vars('all_style', $this->style_model->all_style());
		$this->load->vars('all_season', $this->season_model->all_season());
		$this->load->vars('area_list', $area_list);
		$this->load->vars('package_product', $package_product);
		// 商品信息取
		$this->load->view('package/edit');


	}

	public function proc_edit()
	{
		auth('package_edit');
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->load->helper('package');
		
		$this->form_validation->set_rules('package_name', '礼包名称', 'trim|required');
		$this->form_validation->set_rules('start_date', '开始时间', 'trim|required');
		$this->form_validation->set_rules('end_date', '结束时间', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}

		$package_id = intval($this->input->post('package_id'));
		$this->db->trans_begin();
		$this->package_model->lock_package($package_id);
		
		$package = $this->package_model->filter(array('package_id'=>$package_id));
		if(!$package) sys_msg('记录不存在', 1);
		$perms = get_package_perm($package);
		if (!$perms['edit']) sys_msg('没有权限或当前不可编辑', 1);

		$update = array();
		$update['package_name'] = $this->input->post('package_name');
		$update['start_date'] = $this->input->post('start_date');
		$update['start_time'] = trim($this->input->post('start_time'));
		$update['end_date'] = $this->input->post('end_date');
		$update['end_time'] = trim($this->input->post('end_time'));
		$update['is_empty'] = intval($this->input->post('is_empty'));
		$update['is_liuyan'] = intval($this->input->post('is_liuyan'));
		$update['is_recommend'] = intval($this->input->post('is_recommend'));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['package_desc'] = trim($this->input->post('package_desc'));
		if(!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $update['start_date'])
		   ||!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $update['end_date'])
		   ||($update['start_time'] && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $update['start_time']))
		   ||($update['end_time'] && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $update['end_time']))
		)sys_msg('开始结束时间格式错误');

		$update['start_time'] = $update['start_date'].' '.$update['start_time'];
		unset($update['start_date']);
		$update['end_time'] = $update['end_date'].' '.$update['end_time'];
		unset($update['end_date']);

		if ($perms['config']) {
			$package_config = array();
			$goods_numbers = $this->input->post('goods_number');
			$goods_prices = $this->input->post('goods_price');
			$shop_prices = $this->input->post('shop_price');
			$market_prices = $this->input->post('market_price');
			foreach ($goods_numbers as $key => $goods_number) {
				$goods_number = intval($goods_number);
				if($goods_number<1) continue;
				$package_config[$goods_number] = array(
					'goods_number' => $goods_number,
					'goods_price' => round($goods_prices[$key], 2),
					'shop_price' => round($shop_prices[$key], 2),
					'market_price' => round($market_prices[$key], 2),
				);
			}
			if(empty($package_config)) sys_msg('请填写价格配置', 1);
			$main_config = array_shift($package_config);
			$update['package_goods_number'] = $main_config['goods_number'];
			$update['package_amount'] = $main_config['goods_price'];
			$update['own_price'] = $main_config['shop_price'];
			$update['market_price'] = $main_config['market_price'];
			if ($package->package_type==1 && $package_config) {
				foreach ($package_config as $key => $value) {
					$package_config[$key] = implode('|||', $value);
				}
				$update['package_other_config'] = implode('&&&', $package_config);
			}else {
				$update['package_other_config'] = '';
			}
		}

		$base_path = CREATE_IMAGE_PATH.'package/';
		if($this->input->post('delete_package_image') && $package->package_image) {
			@unlink(CREATE_IMAGE_PATH.$package->package_image);
			$update['package_image'] = '';
		}
		if($this->input->post('delete_package_homepage_image') && $package->package_homepage_image) {
			@unlink(CREATE_IMAGE_PATH.$package->package_homepage_image);
			$update['package_homepage_image'] = '';
		}
		if($this->input->post('delete_package_wechat_image') && $package->package_wechat_image) {
			@unlink(CREATE_IMAGE_PATH.$package->package_wechat_image);
			$update['package_wechat_image'] = '';
		}

		$sub_dir = ($package_id-$package_id%100)/100;
		if(!file_exists($base_path)) mkdir($base_path, 0700, true);
	        if(!file_exists($base_path.'/'.$sub_dir)) mkdir($base_path.'/'.$sub_dir, 0700, true);
		$this->upload->initialize(array(
				'upload_path'=> $base_path.$sub_dir,
				'allowed_types' => 'jpg|gif|png',
				'encrypt_name' => TRUE
			));
		if($this->upload->do_upload('package_image')){
			$file = $this->upload->data();
			$update['package_image'] = 'package/'.$sub_dir.'/'.$file['file_name'];
			if($package->package_image) @unlink(CREATE_IMAGE_PATH.$package->package_image);
		}
		if($this->upload->do_upload('package_homepage_image')){
			$file = $this->upload->data();
			$update['package_homepage_image'] = 'package/'.$sub_dir.'/'.$file['file_name'];
			if($package->package_homepage_image) @unlink(CREATE_IMAGE_PATH.$package->package_homepage_image);
		}
		if($this->upload->do_upload('package_wechat_image')){
			$file = $this->upload->data();
			$update['package_wechat_image'] = 'package/'.$sub_dir.'/'.$file['file_name'];
			if($package->package_wechat_image) @unlink(CREATE_IMAGE_PATH.$package->package_wechat_image);
		}

		$this->package_model->update($update, $package_id);
		$this->db->trans_commit();
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'package/edit/'.$package_id), array('text'=>'返回列表','href'=>'package/index')));
	}

	public function delete($package_id)
	{
		auth('package_edit');
		$test = $this->input->post('test');
		$package_id = intval($package_id);
		// start transaction
		if(!$test) {
			$this->db->trans_begin();
			$package = $this->package_model->lock_package($package_id);
		}else{
			$package = $this->package_model->filter(array('package_id'=>$package_id));
		}		
		
		if(!$package) sys_msg('记录不存在', 1);
		$perms = get_package_perm($package);
		if (!$perms['delete']) sys_msg('没有权限或当前不能删除', 1);
		if($test) sys_msg('', 0);
		$this->package_model->delete_area_where(array('package_id'=>$package_id));
		$this->package_model->delete_area_product_where(array('package_id'=>$package_id));
		$this->package_model->delete($package_id);
		$this->db->trans_commit();
		// end transaction
		$base_path = CREATE_IMAGE_PATH.'package/';
		if($package->package_image) @unlink(CREATE_IMAGE_PATH.$package_package_image);
		if($package->package_homepage_image) @unlink(CREATE_IMAGE_PATH.$package_package_homepage_image);
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'package')));
	}

	public function operate()
	{
		auth('package_edit');
		$package_id = intval($this->input->post('package_id'));
		// start transaction
		$this->db->trans_begin();
		$this->package_model->lock_package($package_id);
		$package = $this->package_model->filter(array('package_id'=>$package_id));
		if(!$package) sys_msg('记录不存在', 1);
		$perms = get_package_perm($package);

		switch (trim($this->input->post('op'))) {
			case 'check':
				if(!$perms['check']) sys_msg('没有权限或当前不可启用', 1);
				if(!$package->package_goods_number || !$package->package_amount || !$package->own_price || !$package->market_price) sys_msg('请填写礼包价格配置', 1);
				$update = array(
					'package_status' => 1,
					'check_admin' => $this->admin_id,
					'check_date' => $this->time
					);
				$this->package_model->update($update, $package_id);
				break;
			
			case 'over':
				if(!$perms['over']) sys_msg('没有权限或当前不可停用', 1);
				$update = array(
					'package_status' => 2,
					'over_admin' => $this->admin_id,
					'over_date' => $this->time
					);
				$update['over_note'] = trim($this->input->post('over_note', TRUE));
				if(!$update['over_note']) sys_msg('请填写停用说明', 1);
				$this->package_model->update($update, $package_id);
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
		auth('package_edit');
		$result = proc_toggle('package_model','package_id',array('is_recommend'));
		print json_encode($result);
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
		print(json_encode(proc_edit('package_model', 'package_id', array('sort_order'), $val)));
		return;
	}

	public function proc_add_area()
	{
		auth('package_edit');
		$update['package_id'] = intval($this->input->post('package_id'));
		$update['area_type'] = intval($this->input->post('area_type'))==1?1:2;
		$update['area_name'] = trim($this->input->post('area_name'));
		$update['min_number'] = intval($this->input->post('min_number'));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['area_text'] = trim($this->input->post('area_text'));
		if(!$update['area_name']) sys_msg('请填写区域名称', 1);
		// start transaction
		$this->db->trans_begin();
		$this->package_model->lock_package($update['package_id']);
		$package = $this->package_model->filter(array('package_id'=>$update['package_id']));
		if (!$package) sys_msg('礼包不存在', 1);
		$perms = get_package_perm($package);
		if(!$perms['edit']) sys_msg('无权限', 1);

		if($update['area_type']==1){
			$update['min_number'] = $package->package_type==3?max($update['min_number'],1):1;
			$update['area_text'] = '';
			if($package->package_type==0 || $package->package_type==1){
				$check_area = $this->package_model->filter_area(array('package_id'=>$update['package_id'],'area_type'=>1));
				if($check_area) sys_msg('该类型礼包只能有一个商品区', 1);
			}
		} else {
			if(!$update['area_text']) sys_msg('请填写自定义内容', 1);
			$update['min_number'] = 0;
		}
		$area_id = $this->package_model->insert_area($update);
		// end transaction
		$this->db->trans_commit();
		$result = array('err'=>0,'msg'=>'');
		$all_area = $this->package_model->all_area(array('package_id'=>$package->package_id));
		$this->load->vars('area_list', $all_area);
		$this->load->vars('perms', $perms);
		$result['area_list'] = $this->load->view('package/area_list', '', TRUE);
		print json_encode($result);
	}

	public function delete_area()
	{
		auth('package_edit');
		$package_id = intval($this->input->post('package_id'));
		// start transaction
		$this->db->trans_begin();
		$this->package_model->lock_package($package_id);
		$area_id = intval($this->input->post('area_id'));
		$package = $this->package_model->filter(array('package_id'=>$package_id));
		if(!$package) sys_msg('礼包不存在', 1);
		$perms = get_package_perm($package);
		if(!$perms['edit']) sys_msg('没有权限', 1);
		$area = $this->package_model->filter_area(array('area_id'=>$area_id,'package_id'=>$package_id));
		if(!$area) sys_msg('区域不存在', 1);
		$area_product = $this->package_model->filter_area_product(array('area_id'=>$area_id));
		if($area_product) sys_msg('区域内有商品,不能删除', 1);
		$this->package_model->delete_area($area_id);
		// end transaction
		$this->db->trans_commit();
		print json_encode(array('err'=>0, 'msg'=>''));
	}

	public function proc_edit_area()
	{
		auth('package_edit');
		$package_id = intval($this->input->post('package_id'));
		// start transaction
		$this->db->trans_begin();
		$this->package_model->lock_package($package_id);
		$area_id = intval($this->input->post('area_id'));
		$update['area_text'] = trim($this->input->post('area_text'));
		$package = $this->package_model->filter(array('package_id'=>$package_id));
		if(!$package) sys_msg('礼包不存在', 1);
		$perms = get_package_perm($package);
		if(!$perms['edit']) sys_msg('没有权限', 1);
		$area = $this->package_model->filter_area(array('package_id'=>$package_id, 'area_id'=>$area_id));
		if(!$area) sys_msg('区域不存在',1);
		if($area->area_type!=2) sys_msg('该区域不是自定义类型',1);
		$this->package_model->update_area($update, $area_id);
		// end transaction
		$this->db->trans_commit();
		$result = array('err'=>0,'msg'=>'','area_text'=>$update['area_text']);
		print json_encode($result);
	}

	public function edit_area_field()
	{
		auth('package_edit');
		$area_id = intval($this->input->post('id'));
		$area = $this->package_model->filter_area(array('area_id'=>$area_id));
		if(!$area) sys_msg('区域不存在', 1);
		$package = $this->package_model->filter(array('package_id'=>$area->package_id));
		if(!$package) sys_msg('礼包不存在', 1);
		$perms = get_package_perm($package);
		if(!$perms['edit']) sys_msg('没有权限', 1);

		$field = trim($this->input->post('field'));
		$val = trim($this->input->post('val'));

		switch ($field) {
			case 'area_name':
				break;

			case 'min_number':
				$val = intval($val);
				if($area->area_type==2){
					$val = 0;
				}else {
					$val = $package->package_type==3?max($val, 1):1;
				}
				break;

			case 'sort_order':
				$val = intval($val);
				break;			
			default:
				sys_msg('参数错误', 1);
				break;
		}
		$this->package_model->update_area(array($field=>$val), $area_id);
		$area = $this->package_model->filter_area(array('area_id'=>$area_id));
		$result = array('err'=>0,'msg'=>'','content'=>$area->$field);
		print json_encode($result);
		return;
	}

	public function product_search()
	{
		auth('package_edit');
		$this->load->helper('product');
		$filter = array();

		$filter['package_id'] = intval($this->input->post('package_id'));

		$product_sn = trim($this->input->post('product_sn'));
		if ($product_sn) $filter['product_sn'] = $product_sn;

		$product_name = trim($this->input->post('product_name'));
		if ($product_name) $filter['product_name'] = $product_name;

		$provider_productcode = trim($this->input->post('provider_productcode'));
		if ($provider_productcode) $filter['provider_productcode'] = $provider_productcode;

		$style_id = intval($this->input->post('style_id'));
		if ($style_id) $filter['style_id'] = $style_id;

		$season_id = intval($this->input->post('season_id'));
		if ($season_id) $filter['season_id'] = $season_id;

		$product_sex = intval($this->input->post('product_sex'));
		if ($product_sex) $filter['product_sex'] = $product_sex;

		$filter = get_pager_param($filter);
		$data = $this->package_model->product_search($filter);
		attach_gallery($data['list']);
		attach_sub($data['list']);

		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$this->load->vars('area_list', $this->package_model->all_area(array('package_id'=>$filter['package_id'])));
			$data['content'] = $this->load->view('package/product_search', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('package/product_search', $data);
	}

	public function add_product()
	{
		auth('package_edit');
		$this->load->model('color_model');
		$this->load->model('product_model');
		$this->load->helper('product');

		$package_id = intval($this->input->post('package_id'));
		$area_id = intval($this->input->post('area_id'));
		$product = trim($this->input->post('product'));
		$package = $this->package_model->filter(array('package_id'=>$package_id));
		if(!$package) sys_msg('礼包不存在', 1);
		$perms = get_package_perm($package);
		if(!$perms['edit']) sys_msg('没有权限', 1);
		$area = $this->package_model->filter_area(array('package_id'=>$package_id, 'area_id'=>$area_id));
		if(!$area) sys_msg('区域不存在', 1);
		if($area->area_type!=1) sys_msg('区域类型错误',1);

		$product_color = array();
		$old_products = get_pair($this->package_model->all_product(array('package_id'=>$package_id)),'product_id','product_id');
		$all_color = get_pair($this->color_model->all_color(),'color_id','color_name');
		foreach(explode('|', $product) as $p){
			$p = explode('-',$p);
			if(count($p)!=2) continue;
			$product_id = intval($p[0]);
			$color_id = intval(intval($p[1]));
			if(isset($old_products[$product_id])) continue;
			if($color_id && !isset($all_color[$color_id])) $color_id = 0;
			$product_color[$product_id] = $color_id;
		}
		if(!$product_color) sys_msg('请选择商品', 1);
		
		$products = $this->product_model->all_product(array('is_audit'=>1, 'product_id'=>array_keys($product_color)));
		$update = array('package_id'=>$package_id, 'area_id'=>$area_id, 'create_admin'=>$this->admin_id, 'create_date' => $this->time);
		foreach($products as $product){
			$update['product_id'] = $product->product_id;
			$update['default_color_id'] = $product_color[$product->product_id];
			$update['market_price'] = $product->market_price;
			$update['shop_price'] = $product->shop_price;
			$update['cost_price'] = $product->cost_price;
			$update['consign_price'] = $product->consign_price;
			//$update['consign_rate'] = $product->consign_rate;
			$update['consign_rate'] = 0;
			$this->package_model->insert_product($update);
		}
		$area_list = $this->package_model->all_area(array('package_id'=>$package->package_id));
		$package_product = $this->package_model->package_product($package_id);
		attach_gallery($package_product);
		attach_sub($package_product);
		$package_product = split_area_product($area_list, $package_product);

		$this->load->vars('perms', get_package_perm($package));
		$this->load->vars('package_product', $package_product);
		$result = array('err'=>0,'msg'=>'');
		$result['data'] = $this->load->view('package/product_list','',TRUE);
		print(json_encode($result));
	}

	public function delete_product()
	{
		auth('package_edit');
		$package_id = intval($this->input->post('package_id'));
		$rec_id = intval($this->input->post('rec_id'));
		$package = $this->package_model->filter(array('package_id'=>$package_id));
		if(!$package) sys_msg('礼包不存在', 1);
		$perms = get_package_perm($package);
		if(!$perms['edit']) sys_msg('没有权限', 1);
		$product = $this->package_model->filter_product(array('rec_id'=>$rec_id,'package_id'=>$package_id));
		if(!$product) sys_msg('记录不存在', 1);
		$this->package_model->delete_product($rec_id);
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function edit_product_field()
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
		print(json_encode(proc_edit('package_model', 'rec_id', array('sort_order'), $val, 'filter_product', 'update_product')));
		return;
	}

}
