<?php
#doc
#	classname:	Brand
#	scope:		PUBLIC
#
#/doc

class Brand extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('brand_model');
	}
	
	public function index ()
	{
		auth('brand_view');
		$filter = $this->uri->uri_to_assoc(3);
		$brand_name = trim($this->input->post('brand_name'));
		if (!empty($brand_name)) $filter['brand_name'] = $brand_name;

		$filter = get_pager_param($filter);
		$data = $this->brand_model->brand_list($filter);
		$this->load->vars('perm_delete', check_perm('brand_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('brand/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('brand/index', $data);
	}

	public function add()
	{
		auth('brand_edit');
		$this->load->model('flag_model');
		$this->load->library('ckeditor');
		$this->load->vars('all_flag', $this->flag_model->all_flag());
		$this->load->view('brand/add');
	}

	public function proc_add()
	{
		auth('brand_edit');
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->load->library('image_lib');
		$this->config->load('brand');
		$thumb_arr = $this->config->item('brand_fields');
		$this->form_validation->set_rules('brand_name', '品牌名称', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['brand_name'] = $this->input->post('brand_name');
                $update['brand_info'] = $this->input->post('brand_info');
		$update['brand_story'] = $this->input->post('brand_story');
		$update['brand_initial'] = strtoupper(trim($this->input->post('brand_initial')));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['flag_id'] = intval($this->input->post('flag_id'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');

		$brand = $this->brand_model->filter(array('brand_name'=>$update['brand_name']));
		if ($brand) {
			sys_msg('品牌名称重复', 1);
		}

		$brand_id = $this->brand_model->insert($update);
		// 上传图片
		$update = array();
		// 上传logo
		$this->upload->initialize(array(
				'upload_path' => CREATE_IMAGE_PATH.'brand/logo/',
				'allowed_types' => 'jpg',
				'encrypt_name' => TRUE
			));
		if ($this->upload->do_upload('brand_logo')) {
			$file = $this->upload->data();
			$update['brand_logo'] = 'brand/logo/'.$file['file_name'];
			$raw_name = $file['raw_name'];
			$file_ext = $file['file_ext'];
			foreach ($thumb_arr as $field=>$thumb) {
				if ($field=='brand_logo') continue;
				$this->image_lib->initialize(array(
					'source_image' => CREATE_IMAGE_PATH.'brand/logo/'.$file['file_name'],
					'quality'=>100,
					'create_thumb'=>TRUE,
					'maintain_ratio'=>FALSE,
					'thumb_marker'=>$thumb['sufix'],
					'width'=>$thumb['width'],
					'height'=>$thumb['height']
				));
				if ($this->image_lib->resize()) {
					//$update[$field] = $raw_name.$thumb['sufix'].$file_ext;
				}
				$this->image_lib->clear();
			}
		}
		// 上传banner
		$this->upload->initialize(array(
				'upload_path' => CREATE_IMAGE_PATH.'brand/banner/',
				'allowed_types' => 'jpg|gif|png',
				'encrypt_name' => TRUE
			));
		if ($this->upload->do_upload('brand_banner')) {
			$file = $this->upload->data();
			$update['brand_banner'] = 'brand/banner/'.$file['file_name'];
		}
		// 上传video
		$this->upload->initialize(array(
				'upload_path' => CREATE_IMAGE_PATH.'brand/video/',
				'allowed_types' => 'swf|flv|ogg',
				'encrypt_name' => TRUE
			));
		if ($this->upload->do_upload('brand_video')) {
			$file = $this->upload->data();
			$update['brand_video'] = 'brand/video/'.$file['brand_video'];
		}
		if ($update) {
			$this->brand_model->update($update, $brand_id);
		}
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'brand/edit/'.$brand_id), array('text'=>'返回列表','href'=>'brand/index')));
	}

	public function edit($brand_id)
	{
		auth(array('brand_edit','brand_view'));
		$this->load->model('flag_model');
		$this->load->library('ckeditor');
		$brand = $this->brand_model->filter(array('brand_id'=>$brand_id));
		if (!$brand) {
			sys_msg('记录不存在', 1);
		}
		$this->load->vars('all_flag', $this->flag_model->all_flag());
		$this->load->vars('row', $brand);
		$this->load->vars('perm_edit', check_perm('brand_edit'));
		$this->load->view('brand/edit');
	}

	public function proc_edit()
	{
		auth('brand_edit');
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->load->library('image_lib');
		$this->config->load('brand');
		$thumb_arr = $this->config->item('brand_fields');
		$this->form_validation->set_rules('brand_name', '品牌名称', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['brand_name'] = $this->input->post('brand_name');
                $update['brand_info'] = $this->input->post('brand_info');
		$update['brand_story'] = $this->input->post('brand_story');
		$update['brand_initial'] = strtoupper(trim($this->input->post('brand_initial')));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['flag_id'] = intval($this->input->post('flag_id'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');

		$brand_id = intval($this->input->post('brand_id'));
		$brand = $this->brand_model->filter(array('brand_id'=>$brand_id));
		if (!$brand) {
			sys_msg('记录不存在!', 1);
		}

		$check_brand = $this->brand_model->filter(array('brand_name'=>$update['brand_name'], 'brand_id !='=>$brand_id));
		if ($check_brand) {
			sys_msg('品牌名称重复', 1);
		}

		// 上传logo
		$this->upload->initialize(array(
				'upload_path' => CREATE_IMAGE_PATH.'brand/logo/',
				'allowed_types' => 'jpg',
				'encrypt_name' => TRUE
			));
		if ($this->upload->do_upload('brand_logo')) {
			$file = $this->upload->data();
			$update['brand_logo'] = 'brand/logo/'.$file['file_name'];
			$raw_name = $file['raw_name'];
			$file_ext = $file['file_ext'];
			foreach ($thumb_arr as $field=>$thumb) {
				if($brand->$field) @unlink(CREATE_IMAGE_PATH.'brand/logo/'.$brand->$field);
			}
			foreach ($thumb_arr as $field=>$thumb) {
				if ($field=='brand_logo') continue;
				$this->image_lib->initialize(array(
					'source_image' => CREATE_IMAGE_PATH.'brand/logo/'.$file['file_name'],
					'quality'=>100,
					'create_thumb'=>TRUE,
					'maintain_ratio'=>FALSE,
					'thumb_marker'=>$thumb['sufix'],
					'width'=>$thumb['width'],
					'height'=>$thumb['height']
				));
				if ($this->image_lib->resize()) {
					//$update[$field] = $raw_name.$thumb['sufix'].$file_ext;
				}
				$this->image_lib->clear();
			}

		}
		// 上传banner
		$this->upload->initialize(array(
				'upload_path' => CREATE_IMAGE_PATH.'brand/banner/',
				'allowed_types' => 'jpg|gif|png',
				'encrypt_name' => TRUE
			));
		if ($this->upload->do_upload('brand_banner')) {
			$file = $this->upload->data();
			$update['brand_banner'] = 'brand/banner/'.$file['file_name'];
			if ($brand->brand_banner) {
				@unlink(CREATE_IMAGE_PATH.$brand->brand_banner);
			}
		}
		// 上传video
		$this->upload->initialize(array(
				'upload_path' => CREATE_IMAGE_PATH.'brand/video/',
				'allowed_types' => 'swf|flv|ogg',
				'encrypt_name' => TRUE
			));
		if ($this->upload->do_upload('brand_video')) {
			$file = $this->upload->data();
			$update['brand_video'] = 'brand/video/'.$file['file_name'];
			if ($brand->brand_video) {
				@unlink(CREATE_IMAGE_PATH.$brand->brand_video);
			}
		}

		$this->brand_model->update($update, $brand_id);

		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'brand/edit/'.$brand_id), array('text'=>'返回列表','href'=>'brand/index')));
	}

	public function delete($brand_id)
	{
		auth('brand_edit');
		$this->load->model('product_model');
		$this->config->load('brand');
		$thumb_arr = $this->config->item('brand_fields');
		$test = $this->input->post('test');
		$brand = $this->brand_model->filter(array('brand_id'=>$brand_id));
		if (!$brand_id) {
			sys_msg('记录不存在', 1);
		}
		$check_product = $this->product_model->filter(array('brand_id'=>$brand_id));
		if ($check_product) {
			sys_msg('该品牌不能删除', 1);
		}
		if($test) sys_msg('');
		$this->brand_model->delete($brand_id);
		foreach ($thumb_arr as $field => $thumb) {
			if ($brand->$field) {
				@unlink(CREATE_IMAGE_PATH.$brand->$field);
			}
		}		

		if ($brand->brand_banner) {
			@unlink(CREATE_IMAGE_PATH.$brand->brand_banner);
		}

		if ($brand->brand_video) {
			@unlink(CREATE_IMAGE_PATH.$brand->brand_video);
		}

		sys_msg('操作成功', 0, array('text'=>'返回列表', 'href'=>'brand/index'));		
	}

	public function toggle()
	{
		auth('brand_edit');
		$result = proc_toggle('brand_model','brand_id',array('is_use'));
		print json_encode($result);
	}

	public function edit_field()
	{
		auth('brand_edit');
		switch (trim($this->input->post('field'))) {
			case 'sort_order':
				$val = intval($this->input->post('val'));
				break;
			
			default:
				$val = NULL;
				break;
		}
		print(json_encode(proc_edit('brand_model', 'brand_id', array('sort_order'), $val)));
		return;
	}

}
###