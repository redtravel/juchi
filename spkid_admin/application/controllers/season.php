<?php
#doc
#	classname:	Season
#	scope:		PUBLIC
#
#/doc

class Season extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('season_model');
	}
	
	public function index ()
	{
		auth('season_view');
		$filter = $this->uri->uri_to_assoc(3);
		$season_name = trim($this->input->post('season_name'));
		if (!empty($season_name)) $filter['season_name'] = $season_name;

		$filter = get_pager_param($filter);
		$data = $this->season_model->season_list($filter);
		$this->load->vars('perm_delete', check_perm('season_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('season/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('season/index', $data);
	}

	public function add()
	{
		auth('season_edit');
		$this->load->view('season/add');
	}

	public function proc_add()
	{
		auth('season_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('season_name', '合作名称', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['season_name'] = $this->input->post('season_name');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');

		$season = $this->season_model->filter(array('season_name'=>$update['season_name']));
		if ($season) {
			sys_msg('合作名称重复', 1);
		}

		$season_id = $this->season_model->insert($update);
		
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'season/edit/'.$season_id), array('text'=>'返回列表','href'=>'season/index')));
	}

	public function edit($season_id)
	{
		auth(array('season_edit','season_view'));
		$season = $this->season_model->filter(array('season_id'=>$season_id));
		if (!$season) {
			sys_msg('记录不存在', 1);
		}
		$this->load->vars('row', $season);
		$this->load->vars('perm_edit', check_perm('season_edit'));
		$this->load->view('season/edit');
	}

	public function proc_edit()
	{
		auth('season_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('season_name', '合作名称', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['season_name'] = $this->input->post('season_name');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');

		$season_id = intval($this->input->post('season_id'));
		$season = $this->season_model->filter(array('season_id'=>$season_id));
		if (!$season) {
			sys_msg('记录不存在!', 1);
		}

		$check_season = $this->season_model->filter(array('season_name'=>$update['season_name'], 'season_id !='=>$season_id));
		if ($check_season) {
			sys_msg('合作名称重复', 1);
		}

		$this->season_model->update($update, $season_id);
		
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'season/edit/'.$season_id), array('text'=>'返回列表','href'=>'season/index')));
	}

	public function delete($season_id)
	{
		auth('season_edit');
		$this->load->model('product_model');
		$test = $this->input->post('test');
		$product = $this->product_model->filter(array('season_id'=>$season_id));
		if($product) sys_msg('该季节不能删除', 1);
		if($test) sys_msg('',0);
		$this->season_model->delete($season_id);
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'season/index')));		
	}

	public function toggle()
	{
		auth('season_edit');
		$result = proc_toggle('season_model','season_id',array('is_use'));
		print json_encode($result);
	}

	public function edit_field()
	{
		auth('season_edit');
		switch (trim($this->input->post('field'))) {
			case 'sort_order':
				$val = intval($this->input->post('val'));
				break;
			
			default:
				$val = NULL;
				break;
		}
		print(json_encode(proc_edit('season_model', 'season_id', array('sort_order'), $val)));
		return;
	}

}
###