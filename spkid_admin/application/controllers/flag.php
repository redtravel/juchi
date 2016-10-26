<?php
#doc
#	classname:	Flag
#	scope:		PUBLIC
#
#/doc

class Flag extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('flag_model');
	}
	
	public function index ()
	{
		auth('flag_view');
		$filter = $this->uri->uri_to_assoc(3);
		$flag_name = trim($this->input->post('flag_name'));
		if (!empty($flag_name)) $filter['flag_name'] = $flag_name;

		$filter = get_pager_param($filter);
		$data = $this->flag_model->flag_list($filter);
		$this->load->vars('perm_delete', check_perm('flag_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('flag/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('flag/index', $data);
	}
	
	public function edit ($flag_id = 0)
	{
		auth(array('flag_edit','flag_view'));
		$flag = $this->flag_model->filter(array('flag_id' => $flag_id));
		if ( empty($flag) )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('row', $flag);
		$this->load->vars('perm_edit', check_perm('flag_edit'));
		$this->load->view('flag/edit');
	}

	public function proc_edit ()
	{
		auth('flag_edit');
		$flag_id = intval($this->input->post('flag_id'));
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('flag_name', '国旗名称', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$flag = $this->flag_model->filter(array('flag_id' => $flag_id));
		if ( ! $flag )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		$update['flag_name'] = $this->input->post('flag_name');
		$update['continent'] = $this->input->post('continent');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = $this->input->post('is_use') == 1 ? 1 : 0;
		
		$old_flag = $this->flag_model->filter(array('flag_name'=>$update['flag_name'], 'flag_id !='=>$flag_id));
		if ( $old_flag )
		{
			sys_msg('国旗名称重复！',1);
		}
		$this->flag_model->update($update, $flag_id);
		// 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'flag/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if ( $this->upload->do_upload('flag_url') )
		{
			$file = $this->upload->data();
			if($flag->flag_url) @unlink(CREATE_IMAGE_PATH.$flag->flag_url);
			$this->flag_model->update(array('flag_url'=>'flag/'.$file['file_name']), $flag_id);
		}
		sys_msg('操作成功！', 0, array(array('text'=>'继续编辑', 'href'=>'flag/edit/'.$flag_id), array('text'=>'返回列表', 'href'=>'flag/index')));
	}

	public function add ()
	{
		auth('flag_edit');
		$this->load->view('flag/add');
	}

	public function proc_add ()
	{
		auth('flag_edit');

		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('flag_name', '国旗名称', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['flag_name'] = $this->input->post('flag_name');
		$update['continent'] = $this->input->post('continent');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = $this->input->post('is_use') == 1 ? 1 : 0;
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$flag = $this->flag_model->filter(array('flag_name'=>$update['flag_name']));
		if ( $flag )
		{
			sys_msg('国旗名称重复', 1);
		}
		$flag_id = $this->flag_model->insert($update);
		// 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'flag/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if($this->upload->do_upload('flag_url')){
			$file = $this->upload->data();
			$this->flag_model->update(array('flag_url'=>'flag/'.$file['file_name']), $flag_id);
		}
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'flag/edit/'.$flag_id)));
	}
	public function delete ($flag_id)
	{
		auth('flag_edit');
		$this->load->model('product_model');
		$test = $this->input->post('test');
		$flag = $this->flag_model->filter(array('flag_id'=>$flag_id));
		if(!$flag) sys_msg('记录不存在', 1);
		$product = $this->product_model->filter(array('flag_id'=>$flag_id));
		if($product) sys_msg('该国旗不能删除', 1);
		if($test) sys_msg('',0);
		$this->flag_model->delete($flag_id);
		if($flag->flag_url){
			@unlink(CREATE_IMAGE_PATH.$flag->flag_url);
		}
		sys_msg('操作成功！',0 , array(array('text'=>'返回列表', 'href'=>'flag')));
	}

	public function toggle()
	{
		auth('flag_edit');
		$result = proc_toggle('flag_model','flag_id',array('is_use'));
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
		print(json_encode(proc_edit('flag_model', 'flag_id', array('sort_order'), $val)));
		return;
	}
}
###