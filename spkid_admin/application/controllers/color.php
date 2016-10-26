<?php
#doc
#	classname:	color
#	scope:		PUBLIC
#
#/doc

class color extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('color_model');
	}
	
	public function index ()
	{
		auth('color_view');
		$filter = $this->uri->uri_to_assoc(3);
		$color_name = trim($this->input->post('color_name'));
		if (!empty($color_name)) $filter['color_name'] = $color_name;

		$filter['group_id'] = intval($this->input->post('group_id'));

		$filter = get_pager_param($filter);
		$data = $this->color_model->color_list($filter);
		$this->load->vars('perm_delete', check_perm('color_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('color/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$all_group = $this->color_model->all_group();
		array_unshift($all_group, array('group_id'=>0,'group_name'=>'所有颜色组'));
		$data['all_group'] = $all_group;
		$data['full_page'] = TRUE;

		$this->load->view('color/index', $data);
	}
	
	public function edit ($color_id = 0)
	{
		auth(array('color_edit','color_view'));
		$color = $this->color_model->filter(array('color_id' => $color_id));
		if ( empty($color) )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('row', $color);
		$this->load->vars('all_group', $this->color_model->all_group());
		$this->load->vars('perm_edit', check_perm('color_edit'));
		$this->load->view('color/edit');
	}

	public function proc_edit ()
	{
		auth('color_edit');
		$color_id = intval($this->input->post('color_id'));
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('color_name', '颜色名称', 'trim|required');
//		$this->form_validation->set_rules('color_sn', '颜色编码', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$color = $this->color_model->filter(array('color_id' => $color_id));
		if ( ! $color )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		$update['color_name'] = $this->input->post('color_name');
//		$update['color_sn'] = $this->input->post('color_sn');
		$update['color_color'] = trim($this->input->post('color_color'));
		$update['group_id'] = intval($this->input->post('group_id'));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = $this->input->post('is_use') == 1 ? 1 : 0;
		
		$old_color = $this->color_model->filter(array('color_name'=>$update['color_name'], 'color_id !='=>$color_id));
		if ( $old_color )
		{
			sys_msg('颜色名称重复！',1);
		}
//		$old_color = $this->color_model->filter(array('color_sn'=>$update['color_sn'], 'color_id !='=>$color_id));
//		if ( $old_color )
//		{
//			sys_msg('颜色编码重复！',1);
//		}
		$this->color_model->update($update, $color_id);
		// 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'color/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if ( $this->upload->do_upload('color_img') )
		{
			$file = $this->upload->data();
			if($color->color_img) @unlink(CREATE_IMAGE_PATH.$color->color_img);
			$this->color_model->update(array('color_img'=>'color/'.$file['file_name']), $color_id);
		}
		sys_msg('操作成功！', 0, array(array('text'=>'继续编辑', 'href'=>'color/edit/'.$color_id), array('text'=>'返回列表', 'href'=>'color/index')));
	}

	public function add ()
	{
		auth('color_edit');
		$this->load->vars('all_group', $this->color_model->all_group());
		$this->load->view('color/add');
	}

	public function proc_add ()
	{
		auth('color_edit');

		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('color_name', '颜色名称', 'trim|required');
//		$this->form_validation->set_rules('color_sn', '编辑编码', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['color_name'] = $this->input->post('color_name');
//		$update['color_sn'] = $this->input->post('color_sn');
		$update['color_color'] = trim($this->input->post('color_color'));
		$update['group_id'] = intval($this->input->post('group_id'));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = $this->input->post('is_use') == 1 ? 1 : 0;
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$color = $this->color_model->filter(array('color_name'=>$update['color_name']));
		if ( $color )
		{
			sys_msg('颜色名称重复', 1);
		}
		$update['color_sn'] = $this->color_model->gen_color_sn($update['group_id']);
//		$color = $this->color_model->filter(array('color_sn'=>$update['color_sn']));
//		if ( $color )
//		{
//			sys_msg('颜色编码重复', 1);
//		}
		$color_id = $this->color_model->insert($update);
		// 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'color/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if($this->upload->do_upload('color_img')){
			$file = $this->upload->data();
			$this->color_model->update(array('color_img'=>'color/'.$file['file_name']), $color_id);
		}
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'color/edit/'.$color_id)));
	}
	public function delete ($color_id)
	{
		auth('color_edit');
		$this->load->model('product_model');
		$test = $this->input->post('test');
		$color = $this->color_model->filter(array('color_id'=>$color_id));
		if(!$color) sys_msg('记录不存在', 1);
		$sub = $this->product_model->filter_sub(array('color_id'=>$color_id));
		if ($sub) sys_msg('该颜色不能删除', 1);
		$gallery = $this->product_model->filter_gallery(array('color_id'=>$color_id));
		if ($gallery) sys_msg('该颜色不能删除', 1);
		if($test) sys_msg('', 0);
		$this->color_model->delete($color_id);
		if($color->color_img){
			@unlink(CREATE_IMAGE_PATH.$color->color_img);
		}
		sys_msg('操作成功！',0 , array(array('text'=>'返回列表', 'href'=>'color')));
	}


	public function group_index ()
	{
		auth('colorgroup_view');
		$filter = $this->uri->uri_to_assoc(3);
		$group_name = trim($this->input->post('group_name'));
		if (!empty($group_name)) $filter['group_name'] = $group_name;

		$filter = get_pager_param($filter);
		$data = $this->color_model->group_list($filter);
		$this->load->vars('perm_delete', check_perm('colorgroup_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('color/group_index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('color/group_index', $data);
	}
	
	public function group_edit ($group_id = 0)
	{
		auth('colorgroup_edit');
		$group = $this->color_model->filter_group(array('group_id' => $group_id));
		if ( ! $group )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('row', $group);
		$this->load->vars('perm_edit', check_perm('colorgroup_edit'));
		$this->load->view('color/group_edit');
	}

	public function proc_group_edit ()
	{
		auth('colorgroup_edit');
		$group_id = intval($this->input->post('group_id'));
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('group_name', '颜色组名称', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$group = $this->color_model->filter_group(array('group_id' => $group_id));
		if ( ! $group )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		$update['group_name'] = $this->input->post('group_name');
		$update['group_color'] = trim($this->input->post('group_color'));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = $this->input->post('is_use') == 1 ? 1 : 0;
		
		$old_group = $this->color_model->filter_group(array('group_name'=>$update['group_name'], 'group_id !='=>$group_id));
		if ( $old_group )
		{
			sys_msg('颜色组名称重复！',1);
		}
		$this->color_model->update_group($update, $group_id);
		// 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'color_group/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if ( $this->upload->do_upload('group_img') )
		{
			$file = $this->upload->data();
			if($group->group_img) @unlink(CREATE_IMAGE_PATH.$group->group_img);
			$this->color_model->update_group(array('group_img'=>'color_group/'.$file['file_name']), $group_id);
		}
		sys_msg('操作成功！', 0, array(array('text'=>'继续编辑', 'href'=>'color/group_edit/'.$group_id), array('text'=>'返回列表', 'href'=>'color/group_index')));
	}

	public function group_add ()
	{
		auth('colorgroup_edit');
		$this->load->view('color/group_add');
	}

	public function proc_group_add ()
	{
		auth('colorgroup_edit');

		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('group_name', '颜色组名称', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['group_name'] = $this->input->post('group_name');
		$update['group_color'] = trim($this->input->post('group_color'));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = $this->input->post('is_use') == 1 ? 1 : 0;
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$group = $this->color_model->filter_group(array('group_name'=>$update['group_name']));
		if ( $group )
		{
			sys_msg('颜色组名称重复', 1);
		}
		$group_id = $this->color_model->insert_group($update);
		// 上传图片
		$this->upload->initialize(array(
			'upload_path' => CREATE_IMAGE_PATH.'color_group/',
			'allowed_types' => 'gif|jpg|png',
			'encrypt_name' => TRUE
		));
		if($this->upload->do_upload('group_img')){
			$file = $this->upload->data();
			$this->color_model->update_group(array('group_img'=>'color_group/'.$file['file_name']), $group_id);
		}
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'color/group_edit/'.$group_id)));
	}
	public function group_delete ($group_id)
	{
		auth('colorgroup_edit');
		$test = $this->input->post('test');
		$group = $this->color_model->filter_group(array('group_id'=>$group_id));
		if(!$group) sys_msg('记录不存在', 1);
		$color = $this->color_model->filter(array('group_id'=>$group_id));
		if ($color) sys_msg('该颜色组不能删除', 1);
		if($test) sys_msg('',0);
		$this->color_model->delete_group($group_id);
		if($group->group_img){
			@unlink(CREATE_IMAGE_PATH.$group->group_img);
		}
		sys_msg('操作成功！',0 , array(array('text'=>'返回列表', 'href'=>'color/group_index')));
	}

	public function toggle()
	{
		auth('color_edit');
		$result = proc_toggle('color_model','color_id',array('is_use'));
		print json_encode($result);
	}

	public function edit_field()
	{
		auth('color_edit');
		switch (trim($this->input->post('field'))) {
			case 'sort_order':
				$val = intval($this->input->post('val'));
				break;
			
			default:
				$val = NULL;
				break;
		}
		print(json_encode(proc_edit('color_model', 'color_id', array('sort_order'), $val)));
		return;
	}

	public function toggle_group()
	{
		auth('color_edit');
		$result = proc_toggle('color_model','group_id',array('is_use'),'filter_group','update_group');
		print json_encode($result);
	}

	public function edit_group_field()
	{
		auth('color_edit');
		switch (trim($this->input->post('field'))) {
			case 'sort_order':
				$val = intval($this->input->post('val'));
				break;
			
			default:
				$val = NULL;
				break;
		}
		print(json_encode(proc_edit('color_model', 'group_id', array('sort_order'), $val,'filter_group','update_group')));
		return;
	}
}
###