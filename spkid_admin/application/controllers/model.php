<?php
#doc
#	classname:	Model
#	scope:		PUBLIC
#
#/doc

class Model extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('model_model');
	}
	
	public function index ()
	{
		auth('model_view');
		$filter = $this->uri->uri_to_assoc(3);
		$model_name = trim($this->input->post('model_name'));
		if (!empty($model_name)) $filter['model_name'] = $model_name;

		$filter = get_pager_param($filter);
		$data = $this->model_model->model_list($filter);
		$this->load->vars('perm_delete', check_perm('model_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('model/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('model/index', $data);
	}
	
	public function edit ($model_id = 0)
	{
		auth(array('model_edit','model_view'));
		$model = $this->model_model->filter(array('model_id' => $model_id));
		if ( empty($model) )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('row', $model);
		$this->load->vars('perm_edit', check_perm('model_edit'));
		$this->load->view('model/edit');
	}

	public function proc_edit ()
	{
		auth('model_edit');
		$model_id = intval($this->input->post('model_id'));
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('model_name', '模特名称', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$model = $this->model_model->filter(array('model_id' => $model_id));
		if ( ! $model )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		$update['model_name'] = $this->input->post('model_name');
		
		$old_model = $this->model_model->filter(array('model_name'=>$update['model_name'], 'model_id !='=>$model_id));
		if ( $old_model )
		{
			sys_msg('模特名称重复！',1);
		}
		$this->model_model->update($update, $model_id);
		// 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'model/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if ( $this->upload->do_upload('model_image') )
		{
			$file = $this->upload->data();
			if($model->model_image) @unlink(CREATE_IMAGE_PATH.$model->model_image);
			$this->model_model->update(array('model_image'=>'model/'.$file['file_name']), $model_id);
		}
		sys_msg('操作成功！', 0, array(array('text'=>'继续编辑', 'href'=>'model/edit/'.$model_id), array('text'=>'返回列表', 'href'=>'model/index')));
	}

	public function add ()
	{
		auth('model_edit');
		$this->load->view('model/add');
	}

	public function proc_add ()
	{
		auth('model_edit');

		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('model_name', '模特名称', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['model_name'] = $this->input->post('model_name');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$model = $this->model_model->filter(array('model_name'=>$update['model_name']));
		if ( $model )
		{
			sys_msg('模特名称重复', 1);
		}
		$model_id = $this->model_model->insert($update);
		// 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'model/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if($this->upload->do_upload('model_image')){
			$file = $this->upload->data();
			$this->model_model->update(array('model_image'=>'model/'.$file['file_name']), $model_id);
		}
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'model/edit/'.$model_id)));
	}
	public function delete ($model_id)
	{
		auth('model_edit');
		$this->load->model('product_model');
		$test = $this->input->post('test');
		$model = $this->model_model->filter(array('model_id'=>$model_id));
		if(!$model) sys_msg('记录不存在', 1);
		$product = $this->product_model->filter(array('model_id'=>$model_id));
		if($product) sys_msg('该模特不能删除', 1);
		if($test) sys_msg('', 0);
		$this->model_model->delete($model_id);
		if($model->model_image){
			@unlink(CREATE_IMAGE_PATH.$model->model_image);
		}
		sys_msg('操作成功！',0 , array(array('text'=>'返回列表', 'href'=>'model')));
	}
}
###