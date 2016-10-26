<?php
#doc
#	classname:	Cooperation
#	scope:		PUBLIC
#
#/doc

class Cooperation extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('cooperation_model');
	}
	
	public function index ()
	{
		auth('cooperation_view');
		$filter = $this->uri->uri_to_assoc(3);
		$cooperation_name = trim($this->input->post('cooperation_name'));
		if (!empty($cooperation_name)) $filter['cooperation_name'] = $cooperation_name;

		$filter = get_pager_param($filter);
		$data = $this->cooperation_model->cooperation_list($filter);
		$this->load->vars('perm_delete', check_perm('cooperation_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('cooperation/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('cooperation/index', $data);
	}

	public function add()
	{
		auth('cooperation_edit');
		$this->load->view('cooperation/add');
	}

	public function proc_add()
	{
		auth('cooperation_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('cooperation_name', '合作方式名称', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['cooperation_name'] = $this->input->post('cooperation_name');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');

		$cooperation = $this->cooperation_model->filter(array('cooperation_name'=>$update['cooperation_name']));
		if ($cooperation) {
			sys_msg('合作方式名称重复', 1);
		}

		$cooperation_id = $this->cooperation_model->insert($update);
		
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'cooperation/edit/'.$cooperation_id), array('text'=>'返回列表','href'=>'cooperation/index')));
	}

	public function edit($cooperation_id)
	{
		auth(array('cooperation_edit','cooperation_view'));
		$cooperation = $this->cooperation_model->filter(array('cooperation_id'=>$cooperation_id));
		if (!$cooperation) {
			sys_msg('记录不存在', 1);
		}
		$this->load->vars('row', $cooperation);
		$this->load->vars('perm_edit', check_perm('cooperation_edit'));
		$this->load->view('cooperation/edit');
	}

	public function proc_edit()
	{
		auth('cooperation_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('cooperation_name', '合作方式名称', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['cooperation_name'] = $this->input->post('cooperation_name');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');

		$cooperation_id = intval($this->input->post('cooperation_id'));
		$cooperation = $this->cooperation_model->filter(array('cooperation_id'=>$cooperation_id));
		if (!$cooperation) {
			sys_msg('记录不存在!', 1);
		}

		$check_cooperation = $this->cooperation_model->filter(array('cooperation_name'=>$update['cooperation_name'], 'cooperation_id !='=>$cooperation_id));
		if ($check_cooperation) {
			sys_msg('合作方式名称重复', 1);
		}

		$this->cooperation_model->update($update, $cooperation_id);
		
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'cooperation/edit/'.$cooperation_id), array('text'=>'返回列表','href'=>'cooperation/index')));
	}

	public function delete($cooperation_id)
	{
		auth('cooperation_edit');
		$this->load->model('provider_model');
		//$test = $this->input->post('test');
		$product = $this->provider_model->filter(array('provider_cooperation'=>$cooperation_id));
		if($product) sys_msg('该合作方式不能删除', 1);
		//if($test) sys_msg('', 0);
        $this->cooperation_model->delete($cooperation_id);
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'cooperation/index')));		
	}

	public function toggle()
	{
		auth('cooperation_edit');
		$result = proc_toggle('cooperation_model','cooperation_id',array('is_use'));
		print json_encode($result);
	}

	public function edit_field()
	{
		auth('cooperation_edit');
		switch (trim($this->input->post('field'))) {
			case 'sort_order':
				$val = intval($this->input->post('val'));
				break;
			
			default:
				$val = NULL;
				break;
		}
		print(json_encode(proc_edit('cooperation_model', 'cooperation_id', array('sort_order'), $val)));
		return;
	}

}
###
