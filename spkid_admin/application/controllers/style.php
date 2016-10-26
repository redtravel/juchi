<?php
#doc
#	classname:	Style
#	scope:		PUBLIC
#
#/doc

class Style extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('style_model');
	}
	
	public function index ()
	{
		auth('style_view');
		$filter = $this->uri->uri_to_assoc(3);
		$style_name = trim($this->input->post('style_name'));
		if (!empty($style_name)) $filter['style_name'] = $style_name;

		$filter = get_pager_param($filter);
		$data = $this->style_model->style_list($filter);
		$this->load->vars('perm_delete', check_perm('style_edit'));
		if ($this->input->post('is_ajax'))
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('style/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('style/index', $data);
	}

	public function add()
	{
		auth('style_edit');
		$this->load->view('style/add');
	}

	public function proc_add()
	{
		auth('style_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('style_name', '合作名称', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['style_name'] = $this->input->post('style_name');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');

		$style = $this->style_model->filter(array('style_name'=>$update['style_name']));
		if ($style) {
			sys_msg('合作名称重复', 1);
		}

		$style_id = $this->style_model->insert($update);
		
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'style/edit/'.$style_id), array('text'=>'返回列表','href'=>'style/index')));
	}

	public function edit($style_id)
	{
		auth(array('style_edit','style_view'));
		$style = $this->style_model->filter(array('style_id'=>$style_id));
		if (!$style) {
			sys_msg('记录不存在', 1);
		}
		$this->load->vars('row', $style);
		$this->load->vars('perm_edit', check_perm('style_edit'));
		$this->load->view('style/edit');
	}

	public function proc_edit()
	{
		auth('style_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('style_name', '合作名称', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['style_name'] = $this->input->post('style_name');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');

		$style_id = intval($this->input->post('style_id'));
		$style = $this->style_model->filter(array('style_id'=>$style_id));
		if (!$style) {
			sys_msg('记录不存在!', 1);
		}

		$check_style = $this->style_model->filter(array('style_name'=>$update['style_name'], 'style_id !='=>$style_id));
		if ($check_style) {
			sys_msg('合作名称重复', 1);
		}

		$this->style_model->update($update, $style_id);
		
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'style/edit/'.$style_id), array('text'=>'返回列表','href'=>'style/index')));
	}

	public function delete($style_id)
	{
		auth('style_edit');
		$this->load->model('product_model');
		$test = $this->input->post('test');
		$product = $this->product_model->filter(array('style_id'=>$style_id));
		if($product) sys_msg('该款式不能删除',1);
		if($test) sys_msg('',0);
		$this->style_model->delete($style_id);
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'style/index')));		
	}

	public function toggle()
	{
		auth('style_edit');
		$result = proc_toggle('style_model','style_id',array('is_use'));
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
		print(json_encode(proc_edit('style_model', 'style_id', array('sort_order'), $val)));
		return;
	}

}
###