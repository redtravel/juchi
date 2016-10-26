<?php
#doc
#	classname:	Carelabel
#	scope:		PUBLIC
#
#/doc

class Carelabel extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('carelabel_model');
	}
	
	public function index ()
	{
		auth('carelabel_view');
		$filter = $this->uri->uri_to_assoc(3);
		$carelabel_name = trim($this->input->post('carelabel_name'));
		if (!empty($carelabel_name)) $filter['carelabel_name'] = $carelabel_name;

		$filter = get_pager_param($filter);
		$data = $this->carelabel_model->carelabel_list($filter);
		$this->load->vars('perm_delete', check_perm('carelabel_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('carelabel/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('carelabel/index', $data);
	}
	
	public function edit ($carelabel_id = 0)
	{
		auth(array('carelabel_edit','carelabel_view'));
		$carelabel = $this->carelabel_model->filter(array('carelabel_id' => $carelabel_id));
		if ( empty($carelabel) )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('row', $carelabel);
		$this->load->vars('perm_edit', check_perm('perm_edit'));
		$this->load->view('carelabel/edit');
	}

	public function proc_edit ()
	{
		auth('carelabel_edit');
		$carelabel_id = intval($this->input->post('carelabel_id'));
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('carelabel_name', '洗标名称', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$carelabel = $this->carelabel_model->filter(array('carelabel_id' => $carelabel_id));
		if ( ! $carelabel )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		$update['carelabel_name'] = $this->input->post('carelabel_name');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		
		$old_carelabel = $this->carelabel_model->filter(array('carelabel_name'=>$update['carelabel_name'], 'carelabel_id !='=>$carelabel_id));
		if ( $old_carelabel )
		{
			sys_msg('洗标名称重复！',1);
		}
		$this->carelabel_model->update($update, $carelabel_id);
		// 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'carelabel/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if ( $this->upload->do_upload('carelabel_url') )
		{
			$file = $this->upload->data();
			if($carelabel->carelabel_url) @unlink(CREATE_IMAGE_PATH.$carelabel->carelabel_url);
			$this->carelabel_model->update(array('carelabel_url'=>"carelabel/".$file['file_name']), $carelabel_id);
		}
		sys_msg('操作成功！', 0, array(array('text'=>'继续编辑', 'href'=>'carelabel/edit/'.$carelabel_id), array('text'=>'返回列表', 'href'=>'carelabel/index')));
	}

	public function add ()
	{
		auth('carelabel_edit');
		$this->load->view('carelabel/add');
	}

	public function proc_add ()
	{
		auth('carelabel_edit');
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('carelabel_name', '洗标名称', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['carelabel_name'] = $this->input->post('carelabel_name');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$carelabel = $this->carelabel_model->filter(array('carelabel_name'=>$update['carelabel_name']));
		if ( $carelabel )
		{
			sys_msg('洗标名称重复', 1);
		}
		$carelabel_id = $this->carelabel_model->insert($update);
		// 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'carelabel/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if($this->upload->do_upload('carelabel_url')){
			$file = $this->upload->data();
			$this->carelabel_model->update(array('carelabel_url'=>"carelabel/".$file['file_name']), $carelabel_id);
		}
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'carelabel/edit/'.$carelabel_id)));
	}
	public function delete ($carelabel_id)
	{
		auth('carelabel_edit');
		$test = $this->input->post('test');
		$carelabel = $this->carelabel_model->filter(array('carelabel_id'=>$carelabel_id));
		if(!$carelabel) sys_msg('记录不存在', 1);
		$product = $this->carelabel_model->filter_refer($carelabel_id);
		if($product) sys_msg('该洗标不能删除', 1);
		if($test) sys_msg('',0);
		$this->carelabel_model->delete($carelabel_id);
		if ($carelabel->carelabel_url) {
			@unlink(CREATE_IMAGE_PATH.$carelabel->carelabel_url);
		}
		sys_msg('操作成功！',0 , array(array('text'=>'返回列表', 'href'=>'carelabel/')));
	}

	public function edit_field()
	{
		auth('carelabel_edit');
		switch (trim($this->input->post('field'))) {
			case 'sort_order':
				$val = intval($this->input->post('val'));
				break;
			
			default:
				$val = NULL;
				break;
		}
		print(json_encode(proc_edit('carelabel_model', 'carelabel_id', array('sort_order'), $val)));
		return;
	}
}
###