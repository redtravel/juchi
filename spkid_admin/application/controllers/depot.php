<?php
#doc
#	classname:	Depot
#	scope:		PUBLIC
#
#/doc

class Depot extends CI_Controller
{
	public function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if ( ! $this->admin_id )
		{
			redirect('index/login');
		}
		$this->load->model('depot_model');
                $this->load->model('cooperation_model');
	}

	public function depot_list ()
	{
		auth(array('depot_edit','depot_view'));
		$filter = $this->uri->uri_to_assoc(3);
		$depot_name = trim($this->input->post('depot_name'));
		if (!empty($depot_name)) $filter['depot_name'] = $depot_name;

		$filter = get_pager_param($filter);
		$data = $this->depot_model->depot_list($filter);
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/depot_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('depot/depot_list', $data);
	}

	public function add_depot ()
	{
		auth('depot_edit');
		$this->load->helper('form');
                $this->load->vars('all_cooperations', $this->cooperation_model->all_cooperation());
		$this->load->view('depot/depot_add');
	}

	public function edit_depot ($depot_id = 0)
	{
		auth(array('depot_edit','depot_view'));
		$depot_info = $this->depot_model->filter_depot(array('depot_id' => $depot_id));
		if ( empty($depot_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('can_edit', check_perm('depot_edit')&&!$this->depot_model->is_depot_in_use($depot_id) ?'1':'0');
                $this->load->vars('all_cooperations', $this->cooperation_model->all_cooperation());
		$this->load->vars('row', $depot_info);
		$this->load->view('depot/depot_edit');
	}

	public function proc_add_depot ()
	{
		auth('depot_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('depot_name', '仓库名称', 'trim|required');
		//$this->form_validation->set_rules('depot_code', '仓库编码', 'trim|required');
		$this->form_validation->set_rules('depot_position', '仓库地点', 'trim|required');
		$this->form_validation->set_rules('depot_priority', '仓库优先级', 'trim|required');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['depot_name'] = $this->input->post('depot_name');
		//$update['depot_code'] = $this->input->post('depot_code');
		$update['depot_position'] = $this->input->post('depot_position');
		$update['depot_priority'] = $this->input->post('depot_priority');
		$update['depot_type'] = $this->input->post('depot_type');
		$update['is_return'] = $this->input->post('is_return');
		$update['is_use'] = $this->input->post('is_use');
		$update['cooperation_id'] = $this->input->post('cooperation_id');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$depot_info = $this->depot_model->filter_depot(array('depot_name'=>$update['depot_name']));
		//$depot_info2 = $this->depot_model->filter_depot(array('depot_code'=>$update['depot_code']));
		if ( $depot_info)
		{
			sys_msg('仓库名称重复', 1);
		}
		$depot_id = $this->depot_model->insert_depot($update);
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'/depot/edit_depot/'.$depot_id)));
	}

	public function proc_edit_depot ()
	{
		auth('depot_edit');
		$depot_id = intval($this->input->post('depot_id'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('depot_name', '仓库名称', 'trim|required');
		//$this->form_validation->set_rules('depot_code', '仓库编码', 'trim|required');
		$this->form_validation->set_rules('depot_position', '仓库地点', 'trim|required');
		$this->form_validation->set_rules('depot_priority', '仓库优先级', 'trim|required');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$depot_info = $this->depot_model->filter_depot(array('depot_id' => $depot_id));
		if ( empty($depot_info) )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		$update['depot_name'] = $this->input->post('depot_name');
		//$update['depot_code'] = $this->input->post('depot_code');
		$update['depot_position'] = $this->input->post('depot_position');
		$update['depot_priority'] = $this->input->post('depot_priority');
		$update['depot_type'] = $this->input->post('depot_type');
		$update['is_return'] = $this->input->post('is_return');
		$update['is_use'] = $this->input->post('is_use');
		$update['cooperation_id'] = $this->input->post('cooperation_id');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$this->depot_model->update_depot($update, $depot_id);
		sys_msg('操作成功！');
	}

	public function delete_depot ($depot_id)
	{
		auth('depot_edit');
		$depot_info = $this->depot_model->filter_depot(array('depot_id' => $depot_id));
		if ( empty($depot_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		if ($this->depot_model->is_depot_in_use($depot_id))
		{
			sys_msg('该仓库不可删除！', 1);
		}
		if ($this->depot_model->delete_depot($depot_id) == 1)
		{
			sys_msg('操作成功！',0 , array(array('text'=>'返回', 'href'=>'/depot/depot_list/')));
		} else
		{
			sys_msg('删除失败！',1);
		}
	}

	public function check_delete_depot ()
	{
		auth('depot_edit');
		$depot_id = trim($this->input->post('depot_id'));
		$depot_info = $this->depot_model->filter_depot(array('depot_id' => $depot_id));
		if ( empty($depot_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($this->depot_model->is_depot_in_use($depot_id))
		{
			echo json_encode(array('error'=>1,'msg'=>'该仓库不可删除'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_delete_depot ()
	{
		auth('depot_edit');
		$depot_id = trim($this->input->post('depot_id'));
		$depot_info = $this->depot_model->filter_depot(array('depot_id' => $depot_id));
		if ( empty($depot_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($this->depot_model->is_depot_in_use($depot_id))
		{
			echo json_encode(array('error'=>1,'msg'=>'该仓库不可删除'));
			return;
		}
		if ($this->depot_model->delete_depot($depot_id) == 1)
		{
			$filter = array();
			$depot_name = trim($this->input->post('depot_name'));
			if (!empty($depot_name)) $filter['depot_name'] = $depot_name;
			$filter = get_pager_param($filter);
			$data = $this->depot_model->depot_list($filter);
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/depot_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		} else
		{
			echo json_encode(array('error'=>1,'msg'=>'删除失败'));
			return;
		}
	}

	public function location_list ()
	{
		auth(array('location_view','location_edit'));
		$filter = $this->uri->uri_to_assoc(3);
		$location_name = trim($this->input->post('location_name'));
		$depot_id = trim($this->input->post('depot_id'));
		if (!empty($location_name)) $filter['location_name'] = $location_name;
		if (!empty($depot_id)) $filter['depot_id'] = $depot_id;

		$filter = get_pager_param($filter);
		$data = $this->depot_model->location_list($filter);
		$data['depot_list'] = $this->depot_model->sel_depot_list(1);
		if ($this->input->post('is_ajax'))
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/location_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('depot/location_list', $data);
	}

	public function add_location ()
	{
		auth('location_edit');
		$this->load->helper('form');
		$data = $this->depot_model->sel_depot_list_all();
		$this->load->vars('depot_list', $data);
		$this->load->view('depot/location_add');
	}

	public function edit_location ($location_id = 0)
	{
		auth(array('location_view','location_edit'));
		$location_info = $this->depot_model->filter_location(array('location_id' => $location_id));
		if ( empty($location_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		$data = $this->depot_model->sel_depot_list_all();
		$this->load->vars('can_edit', check_perm('location_edit')&&!$this->depot_model->is_location_in_use($location_id) ?'1':'0');
		$this->load->vars('depot_list', $data);
		$this->load->vars('row', $location_info);
		$this->load->view('depot/location_edit');
	}

	public function proc_add_location ()
	{
		auth('location_edit');
		$this->load->library('form_validation');
		//$this->form_validation->set_rules('location_name', '储位名称', 'trim|required');
		$this->form_validation->set_rules('location_code1', '储位编码1', 'trim|required|alpha_numeric|max_length[2]');
		$this->form_validation->set_rules('location_code2', '储位编码2', 'trim|required|alpha_numeric|max_length[2]');
		$this->form_validation->set_rules('location_code3', '储位编码3', 'trim|required|alpha_numeric|max_length[2]');
		$this->form_validation->set_rules('location_code4', '储位编码4', 'trim|required|alpha_numeric|max_length[2]');
                $this->form_validation->set_rules('location_code5', '储位编码4', 'trim|required|alpha_numeric|max_length[2]');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['location_code1'] = trim($this->input->post('location_code1'));
		$update['location_code2'] = trim($this->input->post('location_code2'));
		$update['location_code3'] = trim($this->input->post('location_code3'));
		$update['location_code4'] = trim($this->input->post('location_code4'));
                $update['location_code5'] = trim($this->input->post('location_code5'));
		$update['location_name'] = $update['location_code1'].'-'.$update['location_code2'].'-'.$update['location_code3'].'-'.$update['location_code4'].'-'.$update['location_code5'];
		$update['depot_id'] = $this->input->post('depot_id');
		$update['is_use'] = $this->input->post('is_use');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		//$location_code = $update['location_code1'] ."-". $update['location_code2']."-". $update['location_code3']."-". $update['location_code4'];
		$location_info = $this->depot_model->filter_location(array('location_name'=>$update['location_name']));
		$location_info2 = $this->depot_model->filter_location(array('location_code1'=>$update['location_code1'],
									    'location_code2'=>$update['location_code2'],
									    'location_code3'=>$update['location_code3'],
									    'location_code4'=>$update['location_code4'],
                                                                            'location_code5'=>$update['location_code5']));
		if ( $location_info || $location_info2 )
		{
			sys_msg('储位名称或编码重复', 1);
		}
		$location_id = $this->depot_model->insert_location($update);
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'/depot/edit_location/'.$location_id)));
	}

	public function proc_edit_location ()
	{
		auth('location_edit');
		$location_id = intval($this->input->post('location_id'));
		$this->load->library('form_validation');
		//$this->form_validation->set_rules('location_name', '储位名称', 'trim|required');
		$this->form_validation->set_rules('location_code1', '储位编码1', 'trim|required|alpha_numeric|max_length[2]');
		$this->form_validation->set_rules('location_code2', '储位编码2', 'trim|required|alpha_numeric|max_length[2]');
		$this->form_validation->set_rules('location_code3', '储位编码3', 'trim|required|alpha_numeric|max_length[2]');
		$this->form_validation->set_rules('location_code4', '储位编码4', 'trim|required|alpha_numeric|max_length[2]');
                $this->form_validation->set_rules('location_code5', '储位编码4', 'trim|required|alpha_numeric|max_length[2]');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$location_info = $this->depot_model->filter_location(array('location_id' => $location_id));
		if ( empty($location_info) )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		//$update['location_name'] = $this->input->post('location_name');
		$update['location_code1'] = trim($this->input->post('location_code1'));
		$update['location_code2'] = trim($this->input->post('location_code2'));
		$update['location_code3'] = trim($this->input->post('location_code3'));
		$update['location_code4'] = trim($this->input->post('location_code4'));
                $update['location_code5'] = trim($this->input->post('location_code5'));
		$update['location_name'] = $update['location_code1'].'-'.$update['location_code2'].'-'.$update['location_code3'].'-'.$update['location_code4'].'-'.$update['location_code5'];
		$update['depot_id'] = $this->input->post('depot_id');
		$update['is_use'] = $this->input->post('is_use');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$this->depot_model->update_location($update, $location_id);
		sys_msg('操作成功！');
	}

	public function delete_location ($location_id)
	{
		auth('location_edit');
		$location_info = $this->depot_model->filter_location(array('location_id' => $location_id));
		if ( empty($location_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		if ($this->depot_model->is_location_in_use($location_id))
		{
			sys_msg('该储位不可删除！', 1);
		}
		if ($this->depot_model->delete_location($location_id) == 1)
		{
			sys_msg('操作成功！',0 , array(array('text'=>'返回', 'href'=>'/depot/location_list/')));
		} else
		{
			sys_msg('删除失败！',1);
		}
	}

	public function check_delete_location ()
	{
		auth('location_edit');
		$location_id = trim($this->input->post('location_id'));
		$location_info = $this->depot_model->filter_location(array('location_id' => $location_id));
		if ( empty($location_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($this->depot_model->is_location_in_use($location_id))
		{
			echo json_encode(array('error'=>1,'msg'=>'该储位不可删除'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_delete_location ()
	{
		auth('location_edit');
		$location_id = trim($this->input->post('location_id'));
		$location_info = $this->depot_model->filter_location(array('location_id' => $location_id));
		if ( empty($location_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($this->depot_model->is_location_in_use($location_id))
		{
			echo json_encode(array('error'=>1,'msg'=>'该储位不可删除'));
			return;
		}
		if ($this->depot_model->delete_location($location_id) == 1)
		{
			$filter = array();
			$location_name = trim($this->input->post('location_name'));
			if (!empty($location_name)) $filter['location_name'] = $location_name;
			$depot_id = trim($this->input->post('depot_id'));
			if (!empty($depot_id)) $filter['depot_id'] = $depot_id;
			$filter = get_pager_param($filter);
			$data = $this->depot_model->location_list($filter);
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/location_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		} else
		{
			echo json_encode(array('error'=>1,'msg'=>'删除失败'));
			return;
		}
	}
	
	public function location_info_scan () {
		auth('location_info_scan');
                
		$location_name = trim($this->input->post('location_name'));
		$data['full_page'] = TRUE;
		
		if ($this->input->post('is_ajax'))
		{
			if (empty($location_name)) {
            	sys_msg('储位为空', 1);
            }
            $this->load->model('location_model');
            $location = $this->location_model->get_location(array('location_name' => $location_name));
            if (!$location) {
            	sys_msg('储位'.$location_name.'不存在！', 1);
            }
            
			$data = $this->depot_model->location_info_scan($location->location_id);
			$data['full_page'] = FALSE;
			$data['location_name'] = $location_name;
			$data['content'] = $this->load->view('depot/location_info_scan', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$this->load->view('depot/location_info_scan', $data);
	}
	
	public function barcode_scan () {
		auth('barcode_scan');
		$filter = $this->uri->uri_to_assoc(3);
		$provider_barcode = trim($this->input->post('provider_barcode'));
		if (!empty($provider_barcode)) $filter['provider_barcode'] = $provider_barcode;
		//$filter = get_pager_param($filter);
		$data['full_page'] = TRUE;
		if ($this->input->post('is_ajax'))
		{
			$list = $this->depot_model->barcode_scan($filter);
                        $data = $this->do_build_reset_set($list);
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/barcode_scan', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$this->load->view('depot/barcode_scan', $data);
	}
	
        private function do_build_reset_set($list) {
            $result = array();
            if (!empty($list['list']))
            {
                    foreach ($list['list'] as $row)
                    {
                            $key = $row->product_id . '-' . $row->color_id . '-' . $row->size_id;
                            $result[$key]['product_name']=$row->product_name;
                            $result[$key]['color_name']=$row->color_name;
                            $result[$key]['size_name']=$row->size_name;
                            $result[$key]['brand_name']=$row->brand_name;
                            $result[$key]['provider_productcode']=$row->provider_productcode;
                            $result[$key]['location'][] = $row;
                            
                            $filter = array();
                            $filter['product_id'] = $row->product_id;
                            $filter['color_id'] = $row->color_id;
                            $filter['size_id'] = $row->size_id;
                            $filter['trans_status'] = array(1,2,3,4);
                            //$filter['location_id'] = $row->location_id;
                            $trans_log = $this->depot_model->filter_trans_info($filter);
                            $result[$key]['trans_log'] = $trans_log;
                    }
            }
            
            return array('list' => $result, 'filter' => $list['filter']);
        }
        
}
###