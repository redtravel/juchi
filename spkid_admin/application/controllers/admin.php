<?php
#doc
#	classname:	Admin
#	scope:		PUBLIC
#
#/doc

class Admin extends CI_Controller
{
	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if ( ! $this->admin_id )
		{
			redirect('index/login');
		}
		$this->load->model('admin_model');
	}
	
	public function index ()
	{
		auth('admin_view');
		$filter = $this->uri->uri_to_assoc(3);
		$admin_name = trim($this->input->post('admin_name'));
		if (!empty($admin_name)) $filter['admin_name'] = $admin_name;

		$filter = get_pager_param($filter);
		$data = $this->admin_model->admin_list($filter);
		$this->load->vars('perm_perm', check_perm('admin_perm'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('admin/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('admin/index', $data);
	}
	
	public function edit ($admin_id = 0)
	{
		auth(array('admin_edit','admin_view'));
		$admin = $this->admin_model->filter(array('admin_id' => $admin_id));
		if ( empty($admin) )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('row', $admin);
		$this->load->vars('perm_edit', check_perm('admin_edit'));
		$this->load->view('admin/edit');
	}
	
	public function proc_edit ()
	{
		auth('admin_edit');
		$admin_id = intval($this->input->post('admin_id'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('admin_email', 'Email', 'trim|valid_email');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$admin = $this->admin_model->filter(array('admin_id' => $admin_id));
		if ( empty($admin) )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		$update['realname'] = $this->input->post('realname');
		$update['admin_email'] = $this->input->post('admin_email');
		$update['sex'] = $this->input->post('sex');
		$update['birthday'] = $this->input->post('birthday');
		$update['join_date'] = $this->input->post('join_date');
		$update['user_status'] = $this->input->post('user_status') == 1 ? 1 : 0;
		$update['dept_name'] = $this->input->post('dept_name');
		$admin_password = trim($this->input->post('admin_password'));
		if($admin_password) $update['admin_password'] = md5($admin_password);
		$this->admin_model->update($update, $admin_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回','href'=>'admin/edit/'.$admin_id)));
	}
	
	public function add ()
	{
		auth('admin_edit');
		$this->load->view('admin/add');
	}
	
	public function proc_add ()
	{
		auth('admin_edit');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('admin_name', '管理员帐号', 'trim|required');
		$this->form_validation->set_rules('admin_password', '密码', 'trim|required');
		$this->form_validation->set_rules('admin_email', 'Email', 'trim|valid_email');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		
		$update = array();
		$update['admin_name'] = $this->input->post('admin_name');
		$update['admin_password'] = md5($this->input->post('admin_password'));
		$update['realname'] = $this->input->post('realname');
		$update['admin_email'] = $this->input->post('admin_email');
		$update['sex'] = $this->input->post('sex');
		$update['birthday'] = $this->input->post('birthday');
		$update['join_date'] = $this->input->post('join_date');
		$update['user_status'] = $this->input->post('user_status') == 1 ? 1 : 2;
		$update['dept_name'] = $this->input->post('dept_name');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$admin = $this->admin_model->filter(array('admin_name'=>$update['admin_name']));
		if ( $admin )
		{
			sys_msg('帐号重复', 1);
		}
		$admin_id = $this->admin_model->insert($update);
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'admin/edit/'.$admin_id)));
	}
	public function delete ($admin_id)
	{
		sys_msg('管理员暂不能删除！', 1);
	}
	
	public function perm ($admin_id)
	{
		auth('admin_perm');
		$this->load->helper('category');
		$admin = $this->admin_model->filter(array('admin_id'=>$admin_id));
		if ( ! $admin )
		{
			sys_msg('记录不存在！', 1);
		}
		$all_action = $this->admin_model->all_action();
		$all_action = category_tree($all_action, 0, 'action_id', 'parent_id');
		
		$this->load->vars('all_action', $all_action);
		$this->load->vars('admin_action', ','.$admin->action_list.',');
		$this->load->vars('admin', $admin);
		$this->lang->load('perm');
		$this->load->view('admin/perm');
	}
	
	public function proc_perm ()
	{
		auth('admin_perm');
		$admin_id = intval($this->input->post('admin_id'));
		$admin = $this->admin_model->filter(array('admin_id'=>$admin_id));
		if ( ! $admin )
		{
			sys_msg('记录不存在');
		}
		$perms = $this->input->post('perm');
		$update['action_list'] = implode(',', $perms);
		$this->admin_model->update($update, $admin_id);
		sys_msg('操作成功！',0,array(array('href'=>'admin/perm/'.$admin_id,'text'=>'返回')));
	}

	public function toggle_perm()
	{
		auth('admin_perm');
		$admin_id = intval($this->input->post('admin_id'));
		$action_code = trim($this->input->post('action_code'));
		$admin = $this->admin_model->filter(array('admin_id'=>$admin_id));
		$action = $this->admin_model->filter_action(array('action_code'=>$action_code));
		if(!$admin) sys_msg('管理员不存在', 1);
		if(!$action) sys_msg('权限不存在', 1);
		if($admin->action_list == '-1') sys_msg('请选取消该管理员的超级权限', 1);
		$action_list = explode(',',$admin->action_list);
		$key = array_search($action_code, $action_list);
		$status = FALSE;
		if($key !== FALSE){
			unset($action_list[$key]);
			$status = FALSE;
		} else {
			$action_list[] = $action_code;
			$status = TRUE;
		}
		$this->admin_model->update(array('action_list'=>implode(',',$action_list)), $admin_id);
		print json_encode(array('err'=>0,'msg'=>'', 'status'=>$status));
	}

	public function toggle_group()
	{
		auth('admin_perm');
		$admin_id = intval($this->input->post('admin_id'));
		$action_code = trim($this->input->post('action_code'));
		$admin = $this->admin_model->filter(array('admin_id'=>$admin_id));
		$action = $this->admin_model->filter_action(array('action_code'=>$action_code));
		if(!$admin) sys_msg('管理员不存在', 1);
		if(!$action) sys_msg('权限不存在', 1);
		if($admin->action_list == '-1') sys_msg('请选取消该管理员的超级权限', 1);

		$sub_action = $this->admin_model->all_action(array('parent_id'=>$action->action_id));
		if(!$sub_action) sys_msg('该组下没有权限', 1);
		$sub_action = get_pair($sub_action, 'action_id', 'action_code');
		$action_list = explode(',',$admin->action_list);

		$status = FALSE;
		//print_r(array_intersect($sub_action, $action_list));
		if (array_intersect($sub_action, $action_list)) {
			$action_list = array_unique(array_diff($action_list, $sub_action));
		}else{
			$action_list = array_unique(array_merge($action_list,$sub_action));
			$status = TRUE;
		}
		
		$this->admin_model->update(array('action_list'=>implode(',',$action_list)), $admin_id);
		print json_encode(array('err'=>0,'msg'=>'', 'status'=>$status));
	}

	public function toggle_supper()
	{
		auth('admin_perm');
		$admin_id = intval($this->input->post('admin_id'));
		$action_code = trim($this->input->post('action_code'));
		$admin = $this->admin_model->filter(array('admin_id'=>$admin_id));
		if(!$admin) sys_msg('管理员不存在', 1);

		$status = FALSE;
		if($admin->action_list == '-1') {
			$update = array('action_list'=>'');
		}else{
			$update = array('action_list'=>'-1');
			$status = TRUE;
		}

		$this->admin_model->update($update, $admin_id);
		print json_encode(array('err'=>0,'msg'=>'', 'status'=>$status));
	}

	public function toggle()
	{
		auth('admin_edit');
		$result = proc_toggle('admin_model','admin_id',array('user_status'));
		print json_encode($result);
	}


}
###