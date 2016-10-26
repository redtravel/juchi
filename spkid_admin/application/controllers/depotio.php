<?php
#doc
#	classname:	Depot
#	scope:		PUBLIC
#
#/doc

class Depotio extends CI_Controller
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
		$this->load->model('depotio_model');
	}

	public function type ()
	{
		auth(array('dt_view','dt_edit'));
		$filter = $this->uri->uri_to_assoc(3);
		$depot_type_name = trim($this->input->post('depot_type_name'));
		if (!empty($depot_type_name)) $filter['depot_type_name'] = $depot_type_name;

		$filter = get_pager_param($filter);
		$data = $this->depot_model->depot_type_list($filter);
		if ($this->input->post('is_ajax'))
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/type_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('depot/type_list', $data);
	}

	public function add_type ()
	{
		auth('dt_edit');
		$this->load->helper('form');
		$this->load->view('depot/type_add');
	}

	public function edit_type ($depot_type_id = 0)
	{
		auth(array('dt_view','dt_edit'));
		$depot_type_info = $this->depot_model->filter_depot_type(array('depot_type_id' => $depot_type_id));
		if ( empty($depot_type_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('can_edit', check_perm('dt_edit')&&!$this->depot_model->is_depot_type_in_use($depot_type_id) ?'1':'0');
		$this->load->vars('row', $depot_type_info);
		$this->load->view('depot/type_edit');
	}

	public function proc_add_type ()
	{
		auth('dt_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('depot_type_name', '出入库类型名称', 'trim|required');
		$this->form_validation->set_rules('depot_type_code', '出入库类型编号', 'trim|required');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['depot_type_name'] = $this->input->post('depot_type_name');
		$update['depot_type_code'] = $this->input->post('depot_type_code');
		$update['depot_type_special'] = $this->input->post('depot_type_special');
		$update['depot_type_out'] = $this->input->post('depot_type_out');
		$update['is_use'] = $this->input->post('is_use');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;

		if (!$this->depot_model->check_avail_depot_type(array('depot_type_name'=>$update['depot_type_name'],'depot_type_code'=>$update['depot_type_code'],'depot_type_special'=>$update['depot_type_special'],'depot_type_out'=>$update['depot_type_out'])))
		{
			sys_msg('添加出错！出入库类型名称必须唯一，出入库类型编号必须唯一，系统定制类型非通用型必须唯一，系统定制类型必须匹配正确的出入库方向', 1);
		}

		$depot_type_id = $this->depot_model->insert_depot_type($update);
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'/depotio/edit_type/'.$depot_type_id)));
	}

	public function proc_edit_type ()
	{
		auth('dt_edit');
		$depot_type_id = intval($this->input->post('depot_type_id'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('depot_type_name', '出入库类型名称', 'trim|required');
		$this->form_validation->set_rules('depot_type_code', '出入库类型编号', 'trim|required');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$depot_type_info = $this->depot_model->filter_depot_type(array('depot_type_id' => $depot_type_id));
		if ( empty($depot_type_info) )
		{
			sys_msg('记录不存在', 1);
		}

		$update = array();
		$update['depot_type_name'] = $this->input->post('depot_type_name');
		$update['depot_type_code'] = $this->input->post('depot_type_code');
		$update['depot_type_special'] = $this->input->post('depot_type_special');
		$update['depot_type_out'] = $this->input->post('depot_type_out');
		$update['is_use'] = $this->input->post('is_use');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;

		if (!$this->depot_model->check_avail_depot_type(array('depot_type_id'=>$depot_type_id,'depot_type_name'=>$update['depot_type_name'],'depot_type_code'=>$update['depot_type_code'],'depot_type_special'=>$update['depot_type_special'],'depot_type_out'=>$update['depot_type_out'])))
		{
			sys_msg('修改出错！出入库类型名称必须唯一，出入库类型编号必须唯一，系统定制类型非通用型必须唯一，系统定制类型必须匹配正确的出入库方向', 1);
		}

		$this->depot_model->update_depot_type($update, $depot_type_id);
		sys_msg('操作成功！');
	}

	public function delete_type ($depot_type_id)
	{
		auth('dt_edit');
		$depot_type_info = $this->depot_model->filter_depot_type(array('depot_type_id' => $depot_type_id));
		if ( empty($depot_type_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		if ($this->depot_model->is_depot_type_in_use($depot_type_id))
		{
			sys_msg('该类型不可删除！', 1);
		}
		if ($this->depot_model->delete_depot_type($depot_type_id) == 1)
		{
			sys_msg('操作成功！',0 , array(array('text'=>'返回', 'href'=>'/depotio/type')));
		} else
		{
			sys_msg('删除失败！',1);
		}
	}

	public function check_delete_type ()
	{
		auth('dt_edit');
		$depot_type_id = trim($this->input->post('depot_type_id'));
		$depot_type_info = $this->depot_model->filter_depot_type(array('depot_type_id' => $depot_type_id));
		if ( empty($depot_type_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($this->depot_model->is_depot_type_in_use($depot_type_id))
		{
			echo json_encode(array('error'=>1,'msg'=>'该类型不可删除'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_delete_type ()
	{
		auth('dt_edit');
		$depot_type_id = trim($this->input->post('depot_type_id'));
		$depot_type_info = $this->depot_model->filter_depot_type(array('depot_type_id' => $depot_type_id));
		if ( empty($depot_type_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($this->depot_model->is_depot_type_in_use($depot_type_id))
		{
			echo json_encode(array('error'=>1,'msg'=>'该类型不可删除'));
			return;
		}
		if ($this->depot_model->delete_depot_type($depot_type_id) == 1)
		{
			$filter = array();
			$depot_type_name = trim($this->input->post('depot_type_name'));
			if (!empty($depot_type_name)) $filter['depot_type_name'] = $depot_type_name;
			$filter = get_pager_param($filter);
			$data = $this->depot_model->depot_type_list($filter);
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/type_list', $data, TRUE);
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

	public function out ()
	{
		auth(array('depotout_view','depotout_add'));
		$filter = $this->uri->uri_to_assoc(3);
		$depot_out_code = trim($this->input->post('depot_out_code'));
		if (!empty($depot_out_code)) $filter['depot_out_code'] = $depot_out_code;

		$provider_id = trim($this->input->post('provider_id'));
		if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

		$depot_out_type = trim($this->input->post('depot_out_type'));
		if (!empty($depot_out_type)) $filter['depot_out_type'] = $depot_out_type;

		$depot_out_status = trim($this->input->post('depot_out_status'));
		if (!empty($depot_out_status)) $filter['depot_out_status'] = $depot_out_status;

		$depot_depot_id = trim($this->input->post('depot_depot_id'));
		if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

		$provider_goods = trim($this->input->post('provider_goods'));
		if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

		$filter = get_pager_param($filter);
		$data = $this->depotio_model->depot_out_list($filter);
		if ($this->input->post('is_ajax'))
		{
			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;
			$data['content'] = $this->load->view('depot/depot_out_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}

		$status_list = array('0'=>'请选择','1'=>'未审核','2'=>'已审核');
		$data['status_list'] = $status_list;

		$type_list = $this->depot_model->sel_depot_out_type_list();
		$data['type_list'] = $type_list;

		$provider_list = $this->depot_model->sel_provider_list();
		$data['provider_list'] = $provider_list;

		$depot_list = $this->depot_model->sel_depot_list(1);
		$data['depot_list'] = $depot_list;

		$data['full_page'] = TRUE;
		$data['my_id'] = $this->admin_id;
		$this->load->view('depot/depot_out_list', $data);
	}

	public function add_out ()
	{
		auth('depotout_add');
		$status_list = array('0'=>'请选择','1'=>'未审核','2'=>'已审核');
		$data['status_list'] = $status_list;

		$type_list = $this->depot_model->sel_depot_out_type_list();
		$data['type_list'] = $type_list;

		$provider_list = $this->depot_model->sel_provider_name_list();
		$data['provider_list'] = $provider_list;

		$depot_list = $this->depot_model->sel_depot_list(1);
		$data['depot_list'] = $depot_list;

		$this->load->helper('form');
		$this->load->view('depot/out_add', $data);
	}

	public function edit_out ($depot_out_id = 0)
	{
		auth(array('depotout_view','depotout_add'));
		$this->load->model('purchase_batch_model');
		
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		$depot_out_info = $this->depotio_model->format_depot_out_info($depot_out_info);
		if ( empty($depot_out_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		if (empty($depot_out_info->audit_admin) && $depot_out_info->lock_admin == $this->admin_id && check_perm('depotout_add'))
		{
			$data['is_edit'] = 1;
		} else
		{
			$data['is_edit'] = 0;
		}

		$status_list = array('0'=>'请选择','1'=>'未审核','2'=>'已审核');
		$data['status_list'] = $status_list;

		$type_list = $this->depot_model->sel_depot_out_type_list();
		$data['type_list'] = $type_list;

		$provider_list = $this->depot_model->sel_provider_list();
		$data['provider_list'] = $provider_list;

		$batch_list = $this->depot_model->sel_batch_list($depot_out_info->provider_id);
		$data['batch_list'] = $batch_list;

		$depot_list = $this->depot_model->sel_depot_list(1);
		$data['depot_list'] = $depot_list;

		$this->load->vars('row', $depot_out_info);
		$this->load->view('depot/out_edit', $data);
	}

	public function proc_add_out ()
	{
		auth('depotout_add');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('depot_out_type', '出库类型', 'trim|not_empty');
		$this->form_validation->set_rules('depot_depot_id', '出库仓库', 'trim|not_empty');
		$this->form_validation->set_rules('depot_out_date', '实际出库时间', 'trim|required');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['depot_out_type'] = $this->input->post('depot_out_type');
		$update['depot_out_date'] = $this->input->post('depot_out_date');
		$update['depot_depot_id'] = $this->input->post('depot_depot_id');
		$update['provider_id'] = intval($this->input->post('provider_id'));
		$update['batch_id'] = intval($this->input->post('batch_id'));
		$update['provider_id'] = empty($update['provider_id'])?0:$update['provider_id'];
		$update['depot_out_reason'] = $this->input->post('depot_out_reason');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$update['depot_out_code'] = $this->depotio_model->get_depot_out_code();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;

		if(!empty($update['depot_out_type'])) {
			$depot_type = $this->depotio_model->filter_depot_iotype(array('depot_type_id'=>$update['depot_out_type']));
			// 返供应商出库时校验供应商及批次
			if($depot_type->depot_type_code == 'ck001') {
				if(empty($update['provider_id']) || empty($update['batch_id'])) {
					sys_msg('请选择供应商及批次', 1);
				}
			}
		}
		
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_code'=>$update['depot_out_code']));
		while (1)
		{
			if ( $depot_out_info )
			{
				set_time_limit(1);
				$update['depot_out_code'] = $this->depotio_model->get_depot_out_code();
				$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_code'=>$update['depot_out_code']));
			} else
			{
				break;
			}
		}

		$depot_out_id = $this->depotio_model->insert_depot_out($update);
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'/depotio/edit_out/'.$depot_out_id)));
	}

	public function proc_edit_out ()
	{
		auth('depotout_add');
		$depot_out_id = intval($this->input->post('depot_out_id'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('depot_out_type', '出库类型', 'trim|not_empty');
		$this->form_validation->set_rules('depot_depot_id', '出库仓库', 'trim|not_empty');
		$this->form_validation->set_rules('depot_out_date', '实际出库时间', 'trim|required');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		$depot_depot_id = $this->input->post('depot_depot_id');
		if (!empty($depot_depot_id))
			$update['depot_depot_id'] = $depot_depot_id;

		$depot_out_type = $this->input->post('depot_out_type');
		if (!empty($depot_out_type))
			$update['depot_out_type'] = $depot_out_type;

		$provider_id = $this->input->post('provider_id');
		if (!empty($provider_id))
			$update['provider_id'] = $provider_id;
		$batch_id = $this->input->post('batch_id');
		if (!empty($batch_id))
			$update['batch_id'] = $batch_id;

		$update['depot_out_date'] = $this->input->post('depot_out_date');
		$update['depot_out_reason'] = $this->input->post('depot_out_reason');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;

		$this->depotio_model->update_depot_out($update, $depot_out_id);
		sys_msg('操作成功！');
	}

	public function check_out ($depot_out_id)
	{
		auth('depotout_audit');
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($depot_out_info->audit_admin > 0 || empty($depot_out_info->lock_admin) || $depot_out_info->lock_admin != $this->admin_id)
		{
			sys_msg('该出库单不可审核！', 1);
		}

		$now = date('Y-m-d H:i:s');
		$update = array();
		$update['audit_date'] = $now;
		$update['audit_admin'] = $this->admin_id;
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->db->query('BEGIN');
		$this->depotio_model->update_depot_out($update, $depot_out_id);
		
		$update_trans = array();
		$update_trans['trans_status'] = TRANS_STAT_OUT;
		$update_trans['update_admin'] = $this->admin_id;
		$update_trans['update_date'] = $now;
		$this->depot_model->update_transaction($update_trans, array('trans_status'=>TRANS_STAT_AWAIT_OUT,'trans_sn'=>$depot_out_info->depot_out_code));
		$this->db->query('COMMIT');
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/depotio/out')));

	}

	public function unlock_out ($depot_out_id)
	{
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($depot_out_info->lock_admin != $this->admin_id)
		{
			sys_msg('该出库单不可解锁！', 1);
		}

		$update = array();
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->depotio_model->update_depot_out($update, $depot_out_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/depotio/out')));
	}

	public function lock_out ($depot_out_id)
	{
		auth(array('depotout_add','depotout_del','depotout_audit'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($depot_out_info->audit_admin > 0 || $depot_out_info->lock_admin > 0)
		{
			sys_msg('该出库单不可锁定！', 1);
		}

		$update = array();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;
		$this->depotio_model->update_depot_out($update, $depot_out_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/depotio/out')));
	}

	public function delete_out ($depot_out_id)
	{
		auth('depotout_del');
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		if ($depot_out_info->audit_admin > 0 || empty($depot_out_info->lock_admin) || $depot_out_info->lock_admin != $this->admin_id)
		{
			sys_msg('该出库单不可删除！', 1);
		}
		$this->db->query('BEGIN');
		if ($this->depotio_model->delete_depot_out($depot_out_id) == 1)
		{
			$this->depot_model->update_gl_num_out($depot_out_info->depot_out_code);
			$this->depotio_model->delete_depot_out_product(array('depot_out_id'=>$depot_out_id));
			$this->depot_model->delete_transaction(array('trans_sn'=>$depot_out_info->depot_out_code));
			$this->db->query('COMMIT');
			sys_msg('操作成功！',0 , array(array('text'=>'返回', 'href'=>'/depotio/out')));
		} else
		{
			sys_msg('删除失败！',1);
		}
	}

	public function edit_out_product ($depot_out_id = 0)
	{
		auth(array('depotout_add','depotout_view'));
		$filter = $this->uri->uri_to_assoc(4);
		$filter = get_pager_param($filter);
		if (!$this->input->post('is_ajax'))
		{
			$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
			if ( empty($depot_out_info) )
			{
				sys_msg('记录不存在！', 1);
			}
			$this->load->vars('depot_out_info', $depot_out_info);

			$status_list = array('0'=>'请选择','1'=>'上架','2'=>'下架');
			$data['provider_status'] = $status_list;

			$provider_list = $this->depot_model->sel_provider_list();
			$data['provider_list'] = $provider_list;

			$brand_list = $this->depot_model->sel_brand_list();
			$data['brand_list'] = $brand_list;

			//批次选择列表 @baolm
			$batch_list = $this->depot_model->sel_batch_list();
			$data['batch_list'] = $batch_list;

			$depot_out_goods = $this->depotio_model->depot_out_products($depot_out_id,TRUE);
			$data['goods_list'] = $depot_out_goods;

			$type_list = $this->depot_model->sel_purchase_type_list();
			$data['type_list'] = $type_list;

			$data['imagedomain'] = '/public/images';

			$depot_filter = array();
			$depot_filter['sort_order'] = '';
			$depot_filter['record_count'] = count($depot_out_goods);
			$depot_filter = page_and_size($depot_filter);
			$depot_out_goods_limit = array();
			if (!empty($depot_out_goods))
			{
				$i = 0;
				foreach ($depot_out_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$depot_out_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
			}

			$data['goods_list'] = $depot_out_goods_limit;
			$data['depot_filter'] = $depot_filter;
			$data['row_num'] = count($depot_out_goods_limit);
			$depot_list = $this->depot_model->sel_depot_list(0);
			$data['depot_name'] = $depot_list[$depot_out_info->depot_depot_id];
		}

		if ($this->input->post('is_ajax'))
		{
			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$provider_barcode = trim($this->input->post('provider_barcode'));
			if (!empty($provider_barcode)) $filter['provider_barcode'] = $provider_barcode;

			$brand_id = trim($this->input->post('brand_id'));
			if (!empty($brand_id)) $filter['brand_id'] = $brand_id;

			$provider_id = trim($this->input->post('provider_id'));
			if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

			$provider_status = trim($this->input->post('provider_status'));
			if (!empty($provider_status)) $filter['provider_status'] = $provider_status;

			//批次选择列表 @baolm
			$batch_id = trim($this->input->post('batch_id'));
			if (!empty($batch_id)) $filter['batch_id'] = $batch_id;

			$cooperation_id = trim($this->input->post('cooperation_id'));
			if (!empty($cooperation_id)) $filter['cooperation_id'] = $cooperation_id;

			$depot_id = trim($this->input->post('depot_id'));
			if (!empty($depot_id)) $filter['depot_id'] = $depot_id;

			$depot_out_id = trim($this->input->post('depot_out_id'));
			if(!empty($depot_out_id)) {
				$filter['depot_out_id'] = $depot_out_id;
			}
			$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
			if (!empty($depot_out_info)) {
				$filter['trans_sn'] = $depot_out_info->depot_out_code;
				$depot_type = $this->depotio_model->filter_depot_iotype(array('depot_type_id'=>$depot_out_info->depot_out_type));
				$filter['depot_type'] = $depot_type->depot_type_code;
			}

			$with_not = trim($this->input->post('with_not'));
			if (!empty($with_not)) $filter['with_not'] = $with_not;

			$data = $this->depotio_model->query_products_out($filter, false);

			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/out_edit_product', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}

		$data['list'] = array();
		$data['full_page'] = TRUE;
		if (empty($depot_out_info->audit_admin) && $depot_out_info->lock_admin == $this->admin_id && check_perm('depotout_add'))
		{
			$this->load->view('depot/out_edit_product', $data);
		} else
		{
			$data['goods_list'] = $depot_out_goods;
			$this->load->view('depot/out_view_product', $data);
		}

	}

	public function in ()
	{
		auth(array('depotin_view','depotin_add'));
		$this->load->model('brand_model');
		$filter = $this->uri->uri_to_assoc(3);
		$depot_in_code = trim($this->input->post('depot_in_code'));
		if (!empty($depot_in_code)) $filter['depot_in_code'] = $depot_in_code;

		$provider_id = trim($this->input->post('provider_id'));
		if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

		$depot_in_type = trim($this->input->post('depot_in_type'));
		if (!empty($depot_in_type)) $filter['depot_in_type'] = $depot_in_type;

		$depot_in_status = trim($this->input->post('depot_in_status'));
		if (!empty($depot_in_status)) $filter['depot_in_status'] = $depot_in_status;

		$depot_depot_id = trim($this->input->post('depot_depot_id'));
		if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

		$provider_goods = trim($this->input->post('provider_goods'));
		if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

		$provider_productcode = trim($this->input->post('provider_productcode'));
		if (!empty($provider_productcode)) $filter['provider_productcode'] = $provider_productcode;

		$provider_barcode = trim($this->input->post('provider_barcode'));
		if (!empty($provider_barcode)) $filter['provider_barcode'] = $provider_barcode;

		$box_code = trim($this->input->post('box_code'));
		if (!empty($box_code)) $filter['box_code'] = $box_code;

		$brand_id = trim($this->input->post('brand_id'));
		if (!empty($brand_id)) $filter['brand_id'] = $brand_id;

		$filter = get_pager_param($filter);
		$data = $this->depotio_model->depot_in_list($filter);
		if ($this->input->post('is_ajax'))
		{
			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;
			$data['content'] = $this->load->view('depot/depot_in_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}

		$status_list = array('0'=>'请选择','1'=>'未审核','2'=>'已审核');
		$data['status_list'] = $status_list;

		$type_list = $this->depot_model->sel_depot_in_type_list();
		$data['type_list'] = $type_list;

		$provider_list = $this->depot_model->sel_provider_list();
		$data['provider_list'] = $provider_list;

		$depot_list = $this->depot_model->sel_depot_list(1);
		$data['depot_list'] = $depot_list;
		
		$data['brand_list'] = $this->brand_model->all_brand();

		$data['full_page'] = TRUE;
		$data['my_id'] = $this->admin_id;
		$this->load->view('depot/depot_in_list', $data);
	}

	public function add_in ()
	{
		auth('depotin_add');
		$status_list = array('0'=>'请选择','1'=>'未审核','2'=>'已审核');
		$data['status_list'] = $status_list;

		$spec_type = $this->depotio_model->depot_in_spec_type_list();
		$data['spec_type'] = $spec_type;

		$type_list = $this->depot_model->sel_depot_in_type_list();
		$data['type_list'] = $type_list;

		$provider_list = $this->depot_model->sel_provider_list();
		$data['provider_list'] = $provider_list;

		$depot_list = $this->depot_model->sel_depot_list(1);
		$data['depot_list'] = $depot_list;

		$this->load->helper('form');
		$this->load->view('depot/in_add', $data);
	}

	public function edit_in ($depot_in_id = 0)
	{
		auth(array('depotin_view','depotin_add'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		$depot_in_info = $this->depotio_model->format_depot_in_info($depot_in_info);
		if ( empty($depot_in_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		if (empty($depot_in_info->audit_admin) && $depot_in_info->lock_admin == $this->admin_id && check_perm('depotin_add'))
		{
			$data['is_edit'] = 1;
		} else
		{
			$data['is_edit'] = 0;
		}

		$spec_type = $this->depotio_model->depot_in_spec_type_list();
		$data['spec_type'] = $spec_type;

		$status_list = array('0'=>'请选择','1'=>'未审核','2'=>'已审核');
		$data['status_list'] = $status_list;

		$type_list = $this->depot_model->sel_depot_in_type_list();
		$data['type_list'] = $type_list;

		$provider_list = $this->depot_model->sel_provider_list();
		$data['provider_list'] = $provider_list;

		$depot_list = $this->depot_model->sel_depot_list(1);
		$data['depot_list'] = $depot_list;

		$this->load->vars('row', $depot_in_info);
		$this->load->view('depot/in_edit', $data);
	}

	public function proc_add_in ()
	{
		auth('depotin_add');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('depot_in_type', '出库类型', 'trim|not_empty');
		$this->form_validation->set_rules('depot_depot_id', '出库仓库', 'trim|not_empty');
		$this->form_validation->set_rules('depot_in_date', '实际出库时间', 'trim|required');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['depot_in_type'] = $this->input->post('depot_in_type');
		$update['depot_in_date'] = $this->input->post('depot_in_date');
		$update['depot_depot_id'] = $this->input->post('depot_depot_id');
		$update['order_sn'] = $this->input->post('order_sn');
		$update['order_id'] = $this->input->post('order_id');

		$update['depot_in_reason'] = $this->input->post('depot_in_reason');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$update['depot_in_code'] = $this->depotio_model->get_depot_in_code();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;
		
		//调拨入库校验
		$depot_iotype = $this->depotio_model->filter_depot_iotype(array('depot_type_id'=>$update['depot_in_type']));
		if(!empty($depot_iotype) && $depot_iotype->depot_type_special == 4) {
			if(empty($update['order_id']) || $update['order_id'] == 0) {
				sys_msg('调拨入库必须关联调拨出库单', 1);
			}
			$depot_out = $this->depotio_model->filter_depot_out(array('depot_out_code'=>$update['order_sn'],"depot_out_type"=>12));
			if(empty($depot_out)){
			    sys_msg("调拨出库单不存在",1);
			}
			$update["depot_in_number"] = $depot_out->depot_out_finished_number;
			$update["depot_in_amount"] = $depot_out->depot_out_amount;
			$update["in_type"] = 2;
			$update["lock_admin"] = -1;
		}

		//check purchase is used
		if (!empty ($update['order_sn']) && substr($update['order_sn'],0,2) == 'CG')
		{
			$purchase_info = $this->depot_model->filter_purchase(array('purchase_code' => $update['order_sn']));
			if (empty($purchase_info) || $purchase_info->purchase_break == 1 || $purchase_info->purchase_check_admin == 0)
			{
				sys_msg('采购单:'.$update['order_sn'].'状态不可入库', 1);
			}

			$same_info = $this->depotio_model->filter_depot_in(array('order_sn'=>$update['order_sn'],'audit_admin'=>0));
			if (!empty($same_info))
			{
				sys_msg('采购单:'.$update['order_sn'].'已存在未审核的入库单，不可添加新的入库单', 1);
			}
		}

		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_code'=>$update['depot_in_code']));
		while (1)
		{
			if ( $depot_in_info )
			{
				set_time_limit(1);
				$update['depot_in_code'] = $this->depotio_model->get_depot_in_code();
				$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_code'=>$update['depot_in_code']));
			} else
			{
				break;
			}
		}

		$depot_in_id = $this->depotio_model->insert_depot_in($update);
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'/depotio/edit_in/'.$depot_in_id)));
	}

	public function proc_edit_in ()
	{
		auth('depotin_add');
		$depot_in_id = intval($this->input->post('depot_in_id'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('depot_in_type', '出库类型', 'trim|not_empty');
		$this->form_validation->set_rules('depot_depot_id', '出库仓库', 'trim|not_empty');
		$this->form_validation->set_rules('depot_in_date', '实际出库时间', 'trim|required');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			sys_msg('记录不存在', 1);
		}

		$update = array();

		$depot_depot_id = $this->input->post('depot_depot_id');
		if (!empty($depot_depot_id))
			$update['depot_depot_id'] = $depot_depot_id;

		$depot_in_type = $this->input->post('depot_in_type');
		if (!empty($depot_in_type))
			$update['depot_in_type'] = $depot_in_type;

		$update['depot_in_date'] = $this->input->post('depot_in_date');
		$update['order_sn'] = $this->input->post('order_sn');
		$update['order_id'] = $this->input->post('order_id');
		$update['depot_in_reason'] = $this->input->post('depot_in_reason');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;

		//check purchase is used
		if (!empty ($update['order_sn']) && substr($update['order_sn'],0,2) == 'CG')
		{
			$purchase_info = $this->depot_model->filter_purchase(array('purchase_code' => $update['order_sn']));
			if (empty($purchase_info) || $purchase_info->purchase_break == 1 || $purchase_info->purchase_check_admin == 0)
			{
				sys_msg('采购单:'.$update['order_sn'].'状态不可入库', 1);
			}

			$same_info = $this->depotio_model->filter_depot_in_fix($update['order_sn'],$depot_in_id);
			if (!empty($same_info))
			{
				sys_msg('采购单:'.$update['order_sn'].'已存在未审核的入库单，不可添加新的入库单', 1);
			}
		}


		$this->depotio_model->update_depot_in($update, $depot_in_id);
		sys_msg('操作成功！');
	}

	public function check_in ($depot_in_id)
	{
		auth('depotin_audit');
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($depot_in_info->audit_admin > 0 || empty($depot_in_info->lock_admin) || $depot_in_info->lock_admin != $this->admin_id)
		{
			sys_msg('该入库单不可审核！', 1);
		}

		$now = date('Y-m-d H:i:s');
		$update = array();
		$update['audit_date'] = $now;
		$update['audit_admin'] = $this->admin_id;
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;

		$this->db->query('BEGIN');
		$this->depot_model->update_gl_num_in($depot_in_info->depot_in_code);
		$this->depotio_model->update_depot_in($update, $depot_in_id);
		
		$update_trans = array();
		$update_trans['trans_status'] = TRANS_STAT_IN;
		$update_trans['update_admin'] = $this->admin_id;
		$update_trans['update_date'] = $now;
		$this->depot_model->update_transaction($update_trans, array('trans_status'=>TRANS_STAT_AWAIT_IN,'trans_sn'=>$depot_in_info->depot_in_code));
		$this->db->query('COMMIT');
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/depotio/in')));
	}

	public function unlock_in ($depot_in_id)
	{
		//auth('manage_admin');
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($depot_in_info->lock_admin != $this->admin_id)
		{
			sys_msg('该入库单不可解锁！', 1);
		}

		$update = array();
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->depotio_model->update_depot_in($update, $depot_in_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/depotio/in')));
	}

	public function lock_in ($depot_in_id)
	{
		auth(array('depotin_add','depotin_del','depotin_audit'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($depot_in_info->audit_admin > 0 || $depot_in_info->lock_admin > 0)
		{
			sys_msg('该入库单不可锁定！', 1);
		}

		$update = array();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;
		$this->depotio_model->update_depot_in($update, $depot_in_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/depotio/in')));
	}

	public function delete_in ($depot_in_id)
	{
		auth('depotin_del');
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		if ($depot_in_info->audit_admin > 0 || empty($depot_in_info->lock_admin) || $depot_in_info->lock_admin != $this->admin_id)
		{
			sys_msg('该入库单不可删除！', 1);
		}
		$this->db->query('BEGIN');
		if ($this->depotio_model->delete_depot_in($depot_in_id) == 1)
		{
			$this->depotio_model->delete_depot_in_product(array('depot_in_id'=>$depot_in_id));
			$this->depot_model->delete_transaction(array('trans_sn'=>$depot_in_info->depot_in_code));
			$this->db->query('COMMIT');
			sys_msg('操作成功！',0 , array(array('text'=>'返回', 'href'=>'/depotio/in')));
		} else
		{
			sys_msg('删除失败！',1);
		}
	}

	public function edit_in_product ($depot_in_id = 0)
	{
		auth(array('depotin_add','depotin_view'));
		$filter = $this->uri->uri_to_assoc(4);
		$filter = get_pager_param($filter);

		if (!$this->input->post('is_ajax'))
		{
			$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
			if ( empty($depot_in_info) )
			{
				sys_msg('记录不存在！', 1);
			}
			$this->load->vars('depot_in_info', $depot_in_info);

			$status_list = array('0'=>'请选择','1'=>'上架','2'=>'下架');
			$data['provider_status'] = $status_list;

			$provider_list = $this->depot_model->sel_provider_list();
			$data['provider_list'] = $provider_list;

			$brand_list = $this->depot_model->sel_brand_list();
			$data['brand_list'] = $brand_list;

			$depot_in_goods = $this->depotio_model->depot_in_products($depot_in_id,$depot_in_info->order_sn);
			$depot_in_goods_limit = array();


			$type_list = $this->depot_model->sel_purchase_type_list();
			$data['type_list'] = $type_list;

			$data['imagedomain'] = '/public/images';

			$depot_filter = array();
			$depot_filter['sort_order'] = '';
			$depot_filter['record_count'] = count($depot_in_goods);
			$depot_filter = page_and_size($depot_filter);
			if (!empty($depot_in_goods))
			{
				$i = 0;
				foreach ($depot_in_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$depot_in_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
			}

			$data['goods_list'] = $depot_in_goods_limit;
			$data['depot_filter'] = $depot_filter;
			$data['row_num'] = count($depot_in_goods_limit);
			$depot_list = $this->depot_model->sel_depot_list(0);
			$data['depot_name'] = $depot_list[$depot_in_info->depot_depot_id];
			
			$data['revisable'] = true;
			$depot_type = $this->depotio_model->filter_depot_iotype(array('depot_type_id'=>$depot_in_info->depot_in_type));
			$data['depot_type'] = $depot_type->depot_type_code;
			if($depot_type->depot_type_code == 'rk006') {
				$data['revisable'] = false;
			}

		}

		if ($this->input->post('is_ajax'))
		{
			$provider_barcode = trim($this->input->post('provider_barcode'));
			if (!empty($provider_barcode)) $filter['provider_barcode'] = $provider_barcode;
			
			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$brand_id = trim($this->input->post('brand_id'));
			if (!empty($brand_id)) $filter['brand_id'] = $brand_id;

			$provider_id = trim($this->input->post('provider_id'));
			if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

			$provider_status = trim($this->input->post('provider_status'));
			if (!empty($provider_status)) $filter['provider_status'] = $provider_status;

			$cooperation_id = trim($this->input->post('cooperation_id'));
			if (!empty($cooperation_id)) $filter['cooperation_id'] = $cooperation_id;

			$depot_id = trim($this->input->post('depot_id'));
			if (!empty($depot_id)) $filter['depot_id'] = $depot_id;

			$depot_in_id = trim($this->input->post('depot_in_id'));
			if (!empty($depot_in_id)) $filter['depot_in_id'] = $depot_in_id;

			$with_not = trim($this->input->post('with_not'));
			if (!empty($with_not)) $filter['with_not'] = $with_not;

			$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
			$filter['order_sn'] = $depot_in_info->order_sn;

			$data = $this->depotio_model->query_products_in($filter);
			$data['depot_in_info'] = $depot_in_info;
			$data['full_page'] = FALSE;
			$data['imagedomain'] = '/public/images';
			$data['content'] = $this->load->view('depot/in_edit_product', $data, TRUE);
			$data['error'] = 0;

			unset($data['list']);
			echo json_encode($data);
			return;
		}

		$data['list'] = array();
		$data['full_page'] = TRUE;
		if (empty($depot_in_info->audit_admin) && $depot_in_info->lock_admin == $this->admin_id && check_perm('depotin_add'))
		{
			$this->load->view('depot/in_edit_product', $data);
		} else
		{
			$data['goods_list'] = $depot_in_goods;
			$this->load->view('depot/in_view_product', $data);
		}

	}

	public function show_sel_win ()
	{
		$filter = $this->uri->uri_to_assoc(3);
		if (empty($filter['type']))
		{
			$filter['type'] = trim($this->input->post('type'));
		}
		if (!empty($filter['type']))
		{
			if($filter['type'] == 1) // 查询采购单
			{
				$purchase_code = trim($this->input->post('purchase_code'));
				if (!empty($purchase_code)) $filter['purchase_code'] = $purchase_code;

				$purchase_provider = trim($this->input->post('purchase_provider'));
				if (!empty($purchase_provider)) $filter['purchase_provider'] = $purchase_provider;

				$purchase_type = trim($this->input->post('purchase_type'));
				if (!empty($purchase_type)) $filter['purchase_type'] = $purchase_type;

				$filter['purchase_status'] = '2';

				$provider_goods = trim($this->input->post('provider_goods'));
				if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

				$filter = get_pager_param($filter);
				$data = $this->depot_model->purchase_list($filter);
				if ($this->input->post('is_ajax'))
				{
					$data['full_page'] = FALSE;
					$data['content'] = $this->load->view('depot/window_purchase', $data, TRUE);
					$data['error'] = 0;
					unset($data['list']);
					echo json_encode($data);
					return;
				}

				$status_list = array('0'=>'请选择','1'=>'未审核','2'=>'已审核','3'=>'<font color="red">已中止</font>','4'=>'<font color="red">完成</font>');
				$data['status_list'] = $status_list;

				$type_list = $this->depot_model->sel_purchase_type_list();
				$data['type_list'] = $type_list;

				$provider_list = $this->depot_model->sel_provider_list();
				$data['provider_list'] = $provider_list;

				$data['full_page'] = TRUE;
				$data['my_id'] = $this->admin_id;
				$this->load->view('depot/window_purchase', $data);
			} else if($filter['type'] == 2) // 查询出库单
			{
				$depot_out_code = trim($this->input->post('depot_out_code'));
				if (!empty($depot_out_code)) $filter['depot_out_code'] = $depot_out_code;

				$provider_id = trim($this->input->post('provider_id'));
				if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

				$depot_out_type = trim($this->input->post('depot_out_type'));
				if (!empty($depot_out_type)) $filter['depot_out_type'] = $depot_out_type;

				$filter['depot_out_status'] = '2';

				$depot_depot_id = trim($this->input->post('depot_depot_id'));
				if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

				$provider_goods = trim($this->input->post('provider_goods'));
				if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

				$filter = get_pager_param($filter);
				$data = $this->depotio_model->depot_out_list($filter);
				if ($this->input->post('is_ajax'))
				{
					$data['full_page'] = FALSE;
					$data['content'] = $this->load->view('depot/window_depot_out', $data, TRUE);
					$data['error'] = 0;
					unset($data['list']);
					echo json_encode($data);
					return;
				}

				$type_list = $this->depot_model->sel_depot_out_type_list();
				$data['type_list'] = $type_list;

				$provider_list = $this->depot_model->sel_provider_list();
				$data['provider_list'] = $provider_list;

				$depot_list = $this->depot_model->sel_depot_list(1);
				$data['depot_list'] = $depot_list;

				$data['full_page'] = TRUE;
				$data['my_id'] = $this->admin_id;
				$this->load->view('depot/window_depot_out', $data);
			
			} else if($filter['type'] == 4)// 查询调拨出库单
			{
				$depot_io_type = $this->depotio_model->filter_depot_iotype(array('depot_type_code'=>'ck007'));
				if(!empty($depot_io_type)) {
					$filter['depot_out_type'] = $depot_io_type->depot_type_id;
				}
				$depot_out_code = trim($this->input->post('depot_out_code'));
				if (!empty($depot_out_code)) $filter['depot_out_code'] = $depot_out_code;

				$provider_id = trim($this->input->post('provider_id'));
				if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

// 				$depot_out_type = trim($this->input->post('depot_out_type'));
// 				if (!empty($depot_out_type)) $filter['depot_out_type'] = $depot_out_type;

				$filter['depot_out_status'] = '2';

				$depot_depot_id = trim($this->input->post('depot_depot_id'));
				if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

				$provider_goods = trim($this->input->post('provider_goods'));
				if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

				$filter = get_pager_param($filter);
				$data = $this->depotio_model->depot_out_list($filter);
				if ($this->input->post('is_ajax'))
				{
					$data['full_page'] = FALSE;
					$data['content'] = $this->load->view('depot/window_depot_out', $data, TRUE);
					$data['error'] = 0;
					unset($data['list']);
					echo json_encode($data);
					return;
				}

				$type_list = $this->depot_model->sel_depot_out_type_list();
				$data['type_list'] = $type_list;

				$provider_list = $this->depot_model->sel_provider_list();
				$data['provider_list'] = $provider_list;

				$depot_list = $this->depot_model->sel_depot_list(1);
				$data['depot_list'] = $depot_list;

				$data['full_page'] = TRUE;
				$data['my_id'] = $this->admin_id;
                $data['depot_type_id'] = $depot_io_type->depot_type_id;
				$this->load->view('depot/window_depot_out', $data);
			}

		} else
		{
			sys_msg('参数错误！',1);
		}
	}

	public function loaction_input_pre ()
	{
		$sub_id = trim($this->input->post('sub_id'));
		$filter['sub_id'] = $sub_id;
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$filter['depot_in_id'] = $depot_in_id;
		$order_sn = trim($this->input->post('order_sn'));
		$filter['order_sn'] = $order_sn;
		$depot_depot_id = trim($this->input->post('depot_depot_id'));
		$filter['depot_depot_id'] = $depot_depot_id;


		$data = $this->depotio_model->some_products_in($filter);
		echo json_encode($data);
		return;
	}

	public function show_location_win ($depot_id, $target='')
	{
		if (!empty($depot_id))
		{
			$location_list = $this->depot_model->sel_location_list($depot_id);
			$data['location_list'] = $location_list;
                        $data['target'] = $target;
			$this->load->view('depot/window_location', $data);

		} else
		{
			sys_msg('参数错误！',1);
		}
	}

	public function add_product_out ()
	{
		//add
		auth('depotout_add');
		$this->load->model('location_model');
		$my_post = $this->input->post();
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'出库单不存在'));
			return;
		}
		
		$depot_type = $this->depotio_model->filter_depot_iotype(array('depot_type_id'=>$depot_out_info->depot_out_type));

		$extra_trans_sn = "";
		$extra_order_sn = "";
		$update_finished_number = FALSE;
		$tmp_trans_sn = array();
		$this->db->query('BEGIN');
		foreach ($my_post as $key => $value)
		{
			if(strlen($key) > 6 && substr($key,0,6) == "check_")
			{
				$sub_id = substr($key,6);  //transaction_id
				if ($sub_id > 0 && $value > 0)
				{
					$product_sub_id = $this->depot_model->get_transaction_product_sub($sub_id);
					//TODO 校验储位是否在盘点中
					$trans_info = $this->depotio_model->filter_trans_info($sub_id);
					$location_id = $trans_info->location_id;
					$location = $this->location_model->get_location(array("location_id"=>$location_id));
					$inventory_list = $this->depotio_model->get_unfinished_inventory();
					$check_location = FALSE;
					$inv_sn = null;
					foreach ($inventory_list as $inv) {
						if($inv->inventory_type == 0) { //货架范围
							$location_code = $location->location_code1.'-'.$location->location_code2;
							if($location_code >= $inv->shelf_from && $location_code <= $inv->shelf_to) {
								$check_location = TRUE;
								$inv_sn = $inv->inventory_sn;
								break;
							}
						} else { //指定储位
							if($inv->location_id == $location_id) {
								$check_location = TRUE;
								$inv_sn = $inv->inventory_sn;
								break;
							}
						}
					}
					if($check_location) {
						echo json_encode(array('error'=>1,'msg'=>'储位'.$location->location_name.'系统盘点'.$inv_sn.'中，无法新增出库','sub_id'=>$sub_id));
						return;
					}
					//TODO BABY-235 出库数量校验
					$trans = $this->depotio_model->get_trans_out_num_transid($sub_id);
					//echo $product_sub_id."==".$trans->real_num."-".$trans->lock_num."-".$value; die;
					if($trans->real_num - $trans->lock_num - $value < 0) {
						echo json_encode(array('error'=>1,'msg'=>'库存不足','sub_id'=>$sub_id));
						return;
					}
					
					if($depot_type->depot_type_code=='ck001') { //返供应商出库
						
						$list = $this->depotio_model->get_extra_trans_status($product_sub_id);
						foreach ($list as $row) {
							if($row->trans_type==3 || $row->trans_type==4) {
								//TODO 订单退货单待入待出校验并提醒
								if(!isset($tmp_trans_sn[$row->trans_sn])) {
									$extra_order_sn .= $row->trans_sn.",";
									$tmp_trans_sn[$row->trans_sn] = $row;
								}
							} else if($row->trans_type==1 || $row->trans_type==2) {
								if($row->trans_sn != $depot_out_info->depot_out_code) {
									//TODO 其他单据待入待出校验并限制
									if(!isset($tmp_trans_sn[$row->trans_sn])) {
										$extra_trans_sn .= $row->trans_sn.",";
										$tmp_trans_sn[$row->trans_sn] = $row;
									}
								}
							}
						}
						
					} else if($depot_type->depot_type_code=='ck002' || $depot_type->depot_type_code=='ck004') {
                                                //盘点出库和物流赔付出库
						$update_finished_number = TRUE;
					}

					$rs = $this->depotio_model->insert_depot_out_single($sub_id,$value,$depot_out_id,$this->admin_id,$update_finished_number);
					if (empty($rs))
					{
						echo json_encode(array('error'=>1,'msg'=>'添加商品失败，调试信息：transaction_id:'.$sub_id.",product_number:".$value));
						return;
					} elseif ($rs == -1)
					{
						echo json_encode(array('error'=>1,'msg'=>'要添加的商品已存在出库单','sub_id'=>$sub_id));
						return;
					}

					//update gl_num
					if (!$this->depot_model->update_gl_num(array('sub_id'=>$product_sub_id)))
					{
						echo json_encode(array('error'=>1,'msg'=>'更新gl_num错误'));
						return;
					}
				} else
				{
					echo json_encode(array('error'=>1,'msg'=>'更新商品数量失败，调试信息：transaction_id:'.$sub_id.",product_number:".$value));
					return;
				}
			}
		}
		
		if(!empty($extra_order_sn)) {
			$data['msg'] = '添加的商品含有待出的订单或待入的退货单:'.$extra_order_sn;
		}
		if(!empty($extra_trans_sn)) {
			echo json_encode(array('error'=>1,'msg'=>'添加的商品含有待出或待入的单据:'.$extra_trans_sn.'请先处理'));
			return;
		}
		
		$this->depotio_model->update_depot_out_total($depot_out_id);
		//show
		$depot_out_goods = $this->depotio_model->depot_out_products($depot_out_id,TRUE);
		$error_msg = '';
		if(!empty($depot_out_goods))
		{
			foreach ($depot_out_goods as $item)
			{
				if ($item->real_num < 0)
				{
					$error_msg = '出库数量大于可出库数';
					break;
				}
			}
		}
		if (!empty($error_msg))
		{
			echo json_encode(array('error'=>1,'msg'=>$error_msg));
			return;
		}

		$this->db->query('COMMIT');
		$depot_filter = array();
		if (isset($my_post['depot_page_size']) && !empty($my_post['depot_page_size']))
		{
			$depot_filter['page_size'] = $my_post['depot_page_size'];
		}
		$depot_filter['record_count'] = count($depot_out_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);
		$depot_out_goods_limit = array();
		if (!empty($depot_out_goods))
		{
				$i = 0;
				foreach ($depot_out_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$depot_out_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $depot_out_goods_limit;
		$data['row_num'] = count($depot_out_goods_limit);
		$data['content'] = $this->load->view('depot/product_out_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}

	public function flash_product_out ()
	{
		//flush
		//$my_post = $this->input->post();
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'出库单不存在'));
			return;
		}

		$depot_out_goods = $this->depotio_model->depot_out_products($depot_out_id,TRUE);

		$depot_filter = array();
		$depot_filter['page_size'] = $depot_page_size;
		$depot_filter['page'] = $depot_page;
		$depot_filter['record_count'] = count($depot_out_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$depot_out_goods_limit = array();
		if (!empty($depot_out_goods))
		{
				$i = 0;
				foreach ($depot_out_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$depot_out_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $depot_out_goods_limit;
		$data['row_num'] = count($depot_out_goods_limit);
		$data['content'] = $this->load->view('depot/product_out_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}


	public function add_product_in_simple ()
	{
		//add
		auth('depotin_add');
		$my_post = $this->input->post();
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'入库单不存在'));
			return;
		}
		$location_code = trim($this->input->post('location_code'));
		$in_num = trim($this->input->post('in_num'));
		$sub_id = trim($this->input->post('sub_id'));
		if (empty($location_code) || empty($in_num) || empty($sub_id))
		{
			echo json_encode(array('error'=>1,'msg'=>'参数错误'));
			return;
		}
		$location_id = $this->depot_model->check_depot_location($depot_in_info->depot_depot_id,$location_code);
		if(empty($location_id))
		{
			 echo json_encode(array('error'=>1,'msg'=>'参数错误'));
			 return;
		}

		if (!$this->depotio_model->check_products_in($depot_in_info,$sub_id,$in_num))
		{
			echo json_encode(array('error'=>1,'msg'=>'无效的入库数量'));
			return;
		}

		$this->db->query('BEGIN');
		$rs = $this->depotio_model->insert_depot_in_single($sub_id,$in_num,$depot_in_id,$depot_in_info->depot_depot_id,$location_id,$this->admin_id);
		if (empty($rs))
		{
			echo json_encode(array('error'=>1,'msg'=>'添加商品失败，sub_id:'.$sub_id.",depot_in_id:".$depot_in_id.",location_id:".$location_id.",num:".$in_num));
			return;
		} elseif ($rs == -1)
		{
			echo json_encode(array('error'=>1,'msg'=>'要添加的商品已存在入库单','sub_id'=>$sub_id,'subvalue'=>$location_code));
			return;
		}
		//update gl_num
		/*
		if (!$this->depot_model->update_gl_num(array('sub_id'=>$sub_id)))
		{
			echo json_encode(array('error'=>1,'msg'=>'更新gl_num错误'));
			return;
		}
		*/

		$this->depotio_model->update_depot_in_total($depot_in_id);
		$this->db->query('COMMIT');
		//show
		$data['error'] = 0;
		$data['depot_in_sub_id'] = $rs;
		echo json_encode($data);
		return;
	}

	public function add_product_in ()
	{
		//add
		auth('depotin_add');
		$this->load->model('purchase_batch_model');
		$my_post = $this->input->post();
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		//var_dump($depot_in_info);die;
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'入库单不存在'));
			return;
		}
		
		$depot_out_sub_list = array();
		if($depot_in_info->depot_in_type == 13) {
			if(!empty($depot_in_info->order_id) && $depot_in_info->order_id > 0) {
				$depot_out_sub_list = $this->depotio_model->filter_depot_out_product($depot_in_info->order_id);
			}
			
		}
		
		$depot_type = $this->depotio_model->filter_depot_iotype(array('depot_type_id'=>$depot_in_info->depot_in_type));

		//TODO 批次是否已结算
		$batch_is_reckoned = false;
		
		$update_finished_number = FALSE;

		$this->db->query('BEGIN');
		foreach ($my_post as $key => $value)
		{
			if(strlen($key) > 8 && substr($key,0,8) == "checkp__")
			{
				$tmp_str = substr($key,8);
				$tmp_arr = explode('__',$tmp_str);
				if (count($tmp_arr) == 3 && $tmp_arr[0] > 0 && !empty($tmp_arr[1]) && $value > 0)
				{
					$sub_id = $tmp_arr[0];
					$location_code = $tmp_arr[1];
					$batch_id = $tmp_arr[2];
					//TODO 调拨入库时数量校验
					if($depot_in_info->depot_in_type == 13) {
						
						if (isset($depot_out_sub_list[$sub_id])) {
							if($value != $depot_out_sub_list[$sub_id]) {
								echo json_encode(array('error'=>1,'msg'=>'调拨入库时数量必须跟出库数量一致'));
								return;
							}
						}
						
					}
					
					//TODO BABY-235 入库时仓库属性校验
					$cooperation = $this->depotio_model->get_location_of_batch($batch_id);
					$depot = $this->depot_model->filter_depot(array('depot_id'=>$depot_in_info->depot_depot_id));
					//echo "cooperation: ".$cooperation->provider_cooperation."---".$depot->cooperation_id;die;
					if(!empty($cooperation) && !empty($depot)) {
						if($cooperation->provider_cooperation != $depot->cooperation_id) {
							echo json_encode(array('error'=>1,'msg'=>'商品合作方式与仓库属性不一致'));
							return;
						}
					}
					
					//采购单入库[rk002]/收货箱入库[rk005] 不做校验
					if($depot_in_info->depot_in_type != 4 && $depot_in_info->depot_in_type != 11) {
						$purchase_batch = $this->purchase_batch_model->filter(array('batch_id'=>$batch_id));
						if($purchase_batch && $purchase_batch->is_reckoned == 1) {
							$batch_is_reckoned = true;
							continue;
						}
					}
					
					$location_id = $this->depot_model->check_depot_location($depot_in_info->depot_depot_id,$location_code);
					if(empty($location_id))
					{
						 echo json_encode(array('error'=>1,'msg'=>'无效的储位编码'));
						 return;
					}
					
					if($depot_type->depot_type_code=='rk004') { //盘点入库
						$update_finished_number = TRUE;
					}
					
					$rs = $this->depotio_model->insert_depot_in_single($sub_id,$value,$depot_in_id,$depot_in_info->depot_depot_id,$location_id,$this->admin_id,$batch_id,$update_finished_number);
					if (empty($rs))
					{
						echo json_encode(array('error'=>1,'msg'=>'添加商品失败：sub_id:'.$sub_id.",depot_in_id:".$depot_in_id.",location_id:".$location_id.",num:".$value));
						return;
					} elseif ($rs == -1)
					{
						echo json_encode(array('error'=>1,'msg'=>'要添加的商品已存在入库单','sub_id'=>$sub_id,'subvalue'=>$location_code));
						return;
					}


				} else
				{
					echo json_encode(array('error'=>1,'msg'=>'参数错误'));
			 		return;
				}
			}
		}
		$this->depotio_model->update_depot_in_total($depot_in_id);
		//show
		$depot_in_goods = $this->depotio_model->depot_in_products($depot_in_id,$depot_in_info->order_sn);
		$error_msg = '';
		if(!empty($depot_in_goods))
		{
			foreach ($depot_in_goods as $item)
			{
				if ($item->check_num != 'nolimit' && $item->check_num < 0)
				{
					$error_msg = '入库数量大于可入库数， product_sn:'.$item->product_sn.',color_name:'.$item->color_name.',size_name:'.$item->size_name;
					break;
				}
			}
		}
		if (!empty($error_msg))
		{
			echo json_encode(array('error'=>1,'msg'=>$error_msg));
			return;
		}
		$this->db->query('COMMIT');
		//show
//echo "the end";die;
		$depot_filter = array();
		if (isset($my_post['depot_page_size']) && !empty($my_post['depot_page_size']))
		{
			$depot_filter['page_size'] = $my_post['depot_page_size'];
		}
		$depot_filter['record_count'] = count($depot_in_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);
		$depot_in_goods_limit = array();
		if (!empty($depot_in_goods))
		{
				$i = 0;
				foreach ($depot_in_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$depot_in_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $depot_in_goods_limit;
		$data['row_num'] = count($depot_in_goods_limit);
                $data['revisable'] = true;
		$data['content'] = $this->load->view('depot/product_in_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		if($batch_is_reckoned) {
			$data['msg'] = '添加的商品含有已经结算的批次，请走代转买流程';
		}
		echo json_encode($data);
		return;
	}

	public function flash_product_in ()
	{
		//flush
		//auth('manage_admin');
		//$my_post = $this->input->post();
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'入库单不存在'));
			return;
		}

		$depot_in_goods = $this->depotio_model->depot_in_products($depot_in_id,$depot_in_info->order_sn);

		$depot_filter = array();
		$depot_filter['page_size'] = $depot_page_size;
		$depot_filter['page'] = $depot_page;
		$depot_filter['record_count'] = count($depot_in_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$depot_in_goods_limit = array();
		if (!empty($depot_in_goods))
		{
				$i = 0;
				foreach ($depot_in_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$depot_in_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $depot_in_goods_limit;
		$data['row_num'] = count($depot_in_goods_limit);
		$data['content'] = $this->load->view('depot/product_in_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}



	public function del_depot_in_product ()
	{
		auth('depotin_add');
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_in_sub_id = trim($this->input->post('depot_in_sub_id'));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		if ( empty($depot_in_id) || empty($depot_in_sub_id))
		{
			echo json_encode(array('error'=>1,'msg'=>'参数错误'));
			return;
		}
		$depot_in_sub_info = $this->depotio_model->filter_depot_in_sub_x($depot_in_id,$depot_in_sub_id);
		if ( empty($depot_in_sub_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'入库单不存在'));
			return;
		}
		$this->db->query('BEGIN');
		$rs = $this->depotio_model->del_depot_in_product($depot_in_sub_id,$depot_in_id,$depot_in_sub_info->depot_in_code);
		if (empty($rs))
		{
			echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：depot_in_sub_id:'.$depot_in_sub_id.",depot_in_id:".$depot_in_id));
			return;
		}

		$this->depotio_model->update_depot_in_total($depot_in_id);

		//update gl_num
		/*
		if (!$this->depot_model->update_gl_num(array('sub_id'=>$depot_in_sub_info->product_sub_id)))
		{
			echo json_encode(array('error'=>1,'msg'=>'更新gl_num错误'));
			return;
		}
		*/
		$this->db->query('COMMIT');

		$depot_in_goods = $this->depotio_model->depot_in_products($depot_in_id,$depot_in_sub_info->order_sn);

		$depot_filter = array();
		$depot_filter['page_size'] = $depot_page_size;
		$depot_filter['page'] = $depot_page;
		$depot_filter['record_count'] = count($depot_in_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$depot_in_goods_limit = array();
		if (!empty($depot_in_goods))
		{
				$i = 0;
				foreach ($depot_in_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$depot_in_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $depot_in_goods_limit;
		$data['row_num'] = count($depot_in_goods_limit);
                $data['revisable'] = true;
		$data['content'] = $this->load->view('depot/product_in_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;

	}

	public function del_depot_out_product()
	{
		auth('depotout_add');
		$my_post = $this->input->post();
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'出库单不存在'));
			return;
		}
		$this->db->query('BEGIN');
		foreach ($my_post as $key => $value)
		{
			if(strlen($key) > 7 && substr($key,0,7) == "checkp_")
			{
				$sub_id = substr($key,7);
				if ($sub_id > 0 && $value > 0)
				{
					$product_sub_id = $this->depotio_model->get_depot_out_product_sub($sub_id);
					$rs = $this->depotio_model->del_depot_out_product($sub_id,$depot_out_id,$depot_out_info->depot_out_code);
					if (empty($rs))
					{
						echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：depot_out_sub_id:'.$sub_id.",depot_out_id:".$depot_out_id));
						return;
					}
					//update gl_num
					if (!$this->depot_model->update_gl_num(array('sub_id'=>$product_sub_id)))
					{
						echo json_encode(array('error'=>1,'msg'=>'更新gl_num错误'));
						return;
					}

				} else
				{
					echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：depot_out_sub_id:'.$sub_id.",depot_out_id:".$depot_out_id));
					return;
				}
			}
		}
		$this->depotio_model->update_depot_out_total($depot_out_id);

		$this->db->query('COMMIT');
		//show
		$depot_out_goods = $this->depotio_model->depot_out_products($depot_out_id,TRUE);
		$depot_filter = array();
		$depot_filter['page_size'] = $depot_page_size;
		$depot_filter['page'] = $depot_page;
		$depot_filter['record_count'] = count($depot_out_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$depot_out_goods_limit = array();
		if (!empty($depot_out_goods))
		{
				$i = 0;
				foreach ($depot_out_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$depot_out_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $depot_out_goods_limit;
		$data['row_num'] = count($depot_out_goods_limit);
		$data['content'] = $this->load->view('depot/product_out_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}

	public function update_depot_in_product ()
	{
		//auth('manage_admin');
		$my_post = $this->input->post();
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'入库单不存在'));
			return;
		}
		$depot_type = $this->depotio_model->filter_depot_iotype(array('depot_type_id'=>$depot_in_info->depot_in_type));
		$update_finished_number = FALSE;
		if($depot_type->depot_type_code=='rk004') {
			$update_finished_number = TRUE;
		}
		$this->db->query('BEGIN');
		foreach ($my_post as $key => $value)
		{
			if(strlen($key) > 7 && substr($key,0,7) == "checkp_")
			{
				$sub_id = substr($key,7);
				if ($sub_id > 0 && $value > 0)
				{
					//$product_sub_id = $this->depot_model->get_depot_in_product_sub($sub_id);

					$rs = $this->depotio_model->update_depot_in_product_x($sub_id,$depot_in_id,$value,$depot_in_info->depot_in_code,$update_finished_number);
					//if (empty($rs))
					//{
					//	echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：purchase_sub_id:'.$sub_id.",product_number:".$value));
					//	return;
					//}

					//update gl_num
					/*
					if (!$this->depot_model->update_gl_num(array('sub_id'=>$product_sub_id)))
					{
						echo json_encode(array('error'=>1,'msg'=>'更新gl_num错误'));
						return;
					}
					*/

				} else
				{
					echo json_encode(array('error'=>1,'msg'=>'更新商品数量失败，depot_in_sub_id:'.$sub_id.",product_number:".$value));
					return;
				}
			}
		}
		$this->depotio_model->update_depot_in_total($depot_in_id);
		//show
		$depot_in_goods = $this->depotio_model->depot_in_products($depot_in_id,$depot_in_info->order_sn);
		$error_msg = '';
		if(!empty($depot_in_goods))
		{
			foreach ($depot_in_goods as $item)
			{
				if ($item->check_num != 'nolimit' && $item->check_num < 0)
				{
					$error_msg = '入库数量大于可入库数， product_sn:'.$item->product_sn.',color_name:'.$item->color_name.',size_name:'.$item->size_name;
					break;
				}
			}
		}
		if (!empty($error_msg))
		{
			echo json_encode(array('error'=>1,'msg'=>$error_msg));
			return;
		}
		$this->db->query('COMMIT');

		$depot_filter = array();
		$depot_filter['page_size'] = $depot_page_size;
		$depot_filter['page'] = $depot_page;
		$depot_filter['record_count'] = count($depot_in_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$depot_in_goods_limit = array();
		if (!empty($depot_in_goods))
		{
				$i = 0;
				foreach ($depot_in_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$depot_in_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $depot_in_goods_limit;
		$data['row_num'] = count($depot_in_goods_limit);
                $data['revisable'] = true;
		$data['content'] = $this->load->view('depot/product_in_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}

	public function update_depot_out_product ()
	{
		//auth('manage_admin');
		$my_post = $this->input->post();
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'出库单不存在'));
			return;
		}
		$depot_type = $this->depotio_model->filter_depot_iotype(array('depot_type_id'=>$depot_out_info->depot_out_type));
		$update_finished_number = FALSE;
		if($depot_type->depot_type_code=='ck002'|| $depot_type->depot_type_code=='ck003') {
			$update_finished_number = TRUE;
		}
		$this->db->query('BEGIN');
		foreach ($my_post as $key => $value)
		{
			if(strlen($key) > 7 && substr($key,0,7) == "checkp_")
			{
				$sub_id = substr($key,7);
				if ($sub_id > 0 && $value > 0)
				{
					$product_sub_id = $this->depotio_model->get_depot_out_product_sub($sub_id);
					$rs = $this->depotio_model->update_depot_out_product_x($sub_id,$depot_out_id,$value,$depot_out_info->depot_out_code,$update_finished_number);
					//if (empty($rs))
					//{
					//	echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：purchase_sub_id:'.$sub_id.",product_number:".$value));
					//	return;
					//}

					//update gl_num
					if (!$this->depot_model->update_gl_num(array('sub_id'=>$product_sub_id)))
					{
						echo json_encode(array('error'=>1,'msg'=>'更新gl_num错误'));
						return;
					}
				} else
				{
					echo json_encode(array('error'=>1,'msg'=>'更新商品数量失败，depot_out_sub_id:'.$sub_id.",product_number:".$value));
					return;
				}
			}
		}
		$this->depotio_model->update_depot_out_total($depot_out_id);
		//show
		$depot_out_goods = $this->depotio_model->depot_out_products($depot_out_id,TRUE);
		$error_msg = '';
		if(!empty($depot_out_goods))
		{
			foreach ($depot_out_goods as $item)
			{
				if ($item->real_num < 0)
				{
					$error_msg = '出库数量大于可出库数，product_sn:'.$item->product_sn.',color_name:'.$item->color_name.',size_name:'.$item->size_name;
					break;
				}
				if ($item->product_number < $item->product_finished_number) {
					$error_msg = '出库数量小于已完成出库数，product_sn:'.$item->product_sn.',color_name:'.$item->color_name.',size_name:'.$item->size_name;
					break;
				}
			}
		}
		if (!empty($error_msg))
		{
			echo json_encode(array('error'=>1,'msg'=>$error_msg));
			return;
		}
		$this->db->query('COMMIT');
		$depot_filter = array();
		$depot_filter['page_size'] = $depot_page_size;
		$depot_filter['page'] = $depot_page;
		$depot_filter['record_count'] = count($depot_out_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$depot_out_goods_limit = array();
		if (!empty($depot_out_goods))
		{
				$i = 0;
				foreach ($depot_out_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$depot_out_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $depot_out_goods_limit;
		$data['row_num'] = count($depot_out_goods_limit);
		$data['content'] = $this->load->view('depot/product_out_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}

	public function check_delete_in ()
	{
		auth('depotin_del');
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_in_info->audit_admin > 0 || empty($depot_in_info->lock_admin) || $depot_in_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该入库单不可删除'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_delete_in ()
	{
		auth('depotin_del');
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_in_info->audit_admin > 0 || empty($depot_in_info->lock_admin) || $depot_in_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该入库单不可删除'));
			return;
		}
		$this->db->query('BEGIN');
		if ($this->depotio_model->delete_depot_in($depot_in_id) == 1)
		{
			$this->depotio_model->delete_depot_in_product(array('depot_in_id'=>$depot_in_id));
			$this->depot_model->delete_transaction(array('trans_sn'=>$depot_in_info->depot_in_code));
			$this->db->query('COMMIT');
			$filter = array();

			$depot_in_code = trim($this->input->post('depot_in_code'));
			if (!empty($depot_in_code)) $filter['depot_in_code'] = $depot_in_code;

			$provider_id = trim($this->input->post('provider_id'));
			if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

			$depot_in_type = trim($this->input->post('depot_in_type'));
			if (!empty($depot_in_type)) $filter['depot_in_type'] = $depot_in_type;

			$depot_in_status = trim($this->input->post('depot_in_status'));
			if (!empty($depot_in_status)) $filter['depot_in_status'] = $depot_in_status;

			$depot_depot_id = trim($this->input->post('depot_depot_id'));
			if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$filter = get_pager_param($filter);
			$data = $this->depotio_model->depot_in_list($filter);

			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;
			$data['content'] = $this->load->view('depot/depot_in_list', $data, TRUE);
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

	public function check_lock_in ()
	{
		auth(array('depotin_add','depotin_del','depotin_audit'));
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_in_info->audit_admin > 0 || $depot_in_info->lock_admin > 0)
		{
			echo json_encode(array('error'=>1,'msg'=>'该入库单不可锁定'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_lock_in ()
	{
		auth(array('depotin_add','depotin_del','depotin_audit'));
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_in_info->audit_admin > 0 || $depot_in_info->lock_admin > 0)
		{
			echo json_encode(array('error'=>1,'msg'=>'该入库单不可锁定'));
			return;
		}
		$update = array();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;
		$this->depotio_model->update_depot_in($update, $depot_in_id);

			$filter = array();

			$depot_in_code = trim($this->input->post('depot_in_code'));
			if (!empty($depot_in_code)) $filter['depot_in_code'] = $depot_in_code;

			$provider_id = trim($this->input->post('provider_id'));
			if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

			$depot_in_type = trim($this->input->post('depot_in_type'));
			if (!empty($depot_in_type)) $filter['depot_in_type'] = $depot_in_type;

			$depot_in_status = trim($this->input->post('depot_in_status'));
			if (!empty($depot_in_status)) $filter['depot_in_status'] = $depot_in_status;

			$depot_depot_id = trim($this->input->post('depot_depot_id'));
			if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$filter = get_pager_param($filter);
			$data = $this->depotio_model->depot_in_list($filter);

			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;
			$data['content'] = $this->load->view('depot/depot_in_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
	}

	public function check_unlock_in ()
	{
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_in_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该入库单不可解锁'));
			return;
		}
		
		echo json_encode(array('error'=>0));
	}

	public function proc_unlock_in ()
	{
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_in_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该入库单不可解锁'));
			return;
		}
		$update = array();
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->depotio_model->update_depot_in($update, $depot_in_id);

			$filter = array();

			$depot_in_code = trim($this->input->post('depot_in_code'));
			if (!empty($depot_in_code)) $filter['depot_in_code'] = $depot_in_code;

			$provider_id = trim($this->input->post('provider_id'));
			if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

			$depot_in_type = trim($this->input->post('depot_in_type'));
			if (!empty($depot_in_type)) $filter['depot_in_type'] = $depot_in_type;

			$depot_in_status = trim($this->input->post('depot_in_status'));
			if (!empty($depot_in_status)) $filter['depot_in_status'] = $depot_in_status;

			$depot_depot_id = trim($this->input->post('depot_depot_id'));
			if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$filter = get_pager_param($filter);
			$data = $this->depotio_model->depot_in_list($filter);

			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;
			$data['content'] = $this->load->view('depot/depot_in_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
	}

	public function check_check_in ()
	{
		auth('depotin_audit');
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_in_info->audit_admin > 0 || empty($depot_in_info->lock_admin) || $depot_in_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该入库单不可审核'));
			return;
		}
		
		//TODO 采购单入库[rk002]/收货箱入库[rk005] 不做校验
		if($depot_in_info->depot_in_type != 4 && $depot_in_info->depot_in_type != 11) {
			$batch_list = $this->depotio_model->get_depot_in_sub_batch($depot_in_id);
			foreach ( $batch_list as $item ) {
				if(!empty($item->lock_admin)) {
					echo json_encode(array('error'=>1,'msg'=>'退货单含有已锁定的批次的商品，暂不能审核，请等待'));
					return;
				}
			}
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_check_in ()
	{
		auth('depotin_audit');
		$depot_in_id = trim($this->input->post('depot_in_id'));
		$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
		if ( empty($depot_in_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_in_info->audit_admin > 0 || empty($depot_in_info->lock_admin) || $depot_in_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该入库单不可审核'));
			return;
		}
                
                
                //入库审核
                $this->depotio_model->check_in($depot_in_info, $this->admin_id);

                
			$filter = array();

			$depot_in_code = trim($this->input->post('depot_in_code'));
			if (!empty($depot_in_code)) $filter['depot_in_code'] = $depot_in_code;

			$provider_id = trim($this->input->post('provider_id'));
			if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

			$depot_in_type = trim($this->input->post('depot_in_type'));
			if (!empty($depot_in_type)) $filter['depot_in_type'] = $depot_in_type;

			$depot_in_status = trim($this->input->post('depot_in_status'));
			if (!empty($depot_in_status)) $filter['depot_in_status'] = $depot_in_status;

			$depot_depot_id = trim($this->input->post('depot_depot_id'));
			if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$filter = get_pager_param($filter);
			$data = $this->depotio_model->depot_in_list($filter);

			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;
			$data['content'] = $this->load->view('depot/depot_in_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
	}

	public function check_delete_out ()
	{
		auth('depotout_del');
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_out_info->audit_admin > 0 || empty($depot_out_info->lock_admin) || $depot_out_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该出库单不可删除'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_delete_out ()
	{
		auth('depotout_del');
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_out_info->audit_admin > 0 || empty($depot_out_info->lock_admin) || $depot_out_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该出库单不可删除'));
			return;
		}
		$this->db->query('BEGIN');
		if ($this->depotio_model->delete_depot_out($depot_out_id) == 1)
		{
			$this->depot_model->update_gl_num_out($depot_out_info->depot_out_code);
			$this->depotio_model->delete_depot_out_product(array('depot_out_id'=>$depot_out_id));
			$this->depot_model->delete_transaction(array('trans_sn'=>$depot_out_info->depot_out_code));
			$this->db->query('COMMIT');
			$filter = array();

			$depot_out_code = trim($this->input->post('depot_out_code'));
			if (!empty($depot_out_code)) $filter['depot_out_code'] = $depot_out_code;

			$provider_id = trim($this->input->post('provider_id'));
			if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

			$depot_out_type = trim($this->input->post('depot_out_type'));
			if (!empty($depot_out_type)) $filter['depot_out_type'] = $depot_out_type;

			$depot_out_status = trim($this->input->post('depot_out_status'));
			if (!empty($depot_out_status)) $filter['depot_out_status'] = $depot_out_status;

			$depot_depot_id = trim($this->input->post('depot_depot_id'));
			if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$filter = get_pager_param($filter);
			$data = $this->depotio_model->depot_out_list($filter);

			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;
			$data['content'] = $this->load->view('depot/depot_out_list', $data, TRUE);
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

	public function check_lock_out ()
	{
		auth(array('depotout_add','depotout_del','depotout_audit'));
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_out_info->audit_admin > 0 || $depot_out_info->lock_admin > 0)
		{
			echo json_encode(array('error'=>1,'msg'=>'该出库单不可锁定'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_lock_out ()
	{
		auth(array('depotout_add','depotout_del','depotout_audit'));
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_out_info->audit_admin > 0 || $depot_out_info->lock_admin > 0)
		{
			echo json_encode(array('error'=>1,'msg'=>'该出库单不可锁定'));
			return;
		}
		$update = array();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;
		$this->depotio_model->update_depot_out($update, $depot_out_id);

			$filter = array();

			$depot_out_code = trim($this->input->post('depot_out_code'));
			if (!empty($depot_out_code)) $filter['depot_out_code'] = $depot_out_code;

			$provider_id = trim($this->input->post('provider_id'));
			if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

			$depot_out_type = trim($this->input->post('depot_out_type'));
			if (!empty($depot_out_type)) $filter['depot_out_type'] = $depot_out_type;

			$depot_out_status = trim($this->input->post('depot_out_status'));
			if (!empty($depot_out_status)) $filter['depot_out_status'] = $depot_out_status;

			$depot_depot_id = trim($this->input->post('depot_depot_id'));
			if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$filter = get_pager_param($filter);
			$data = $this->depotio_model->depot_out_list($filter);

			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;
			$data['content'] = $this->load->view('depot/depot_out_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
	}

	public function check_unlock_out ()
	{
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_out_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该出库单不可解锁'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_unlock_out ()
	{
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_out_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该出库单不可解锁'));
			return;
		}
		$update = array();
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->depotio_model->update_depot_out($update, $depot_out_id);

			$filter = array();

			$depot_out_code = trim($this->input->post('depot_out_code'));
			if (!empty($depot_out_code)) $filter['depot_out_code'] = $depot_out_code;

			$provider_id = trim($this->input->post('provider_id'));
			if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

			$depot_out_type = trim($this->input->post('depot_out_type'));
			if (!empty($depot_out_type)) $filter['depot_out_type'] = $depot_out_type;

			$depot_out_status = trim($this->input->post('depot_out_status'));
			if (!empty($depot_out_status)) $filter['depot_out_status'] = $depot_out_status;

			$depot_depot_id = trim($this->input->post('depot_depot_id'));
			if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$filter = get_pager_param($filter);
			$data = $this->depotio_model->depot_out_list($filter);

			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;
			$data['content'] = $this->load->view('depot/depot_out_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
	}

	public function check_check_out ()
	{
		auth('depotout_audit');
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_out_info->audit_admin > 0 || empty($depot_out_info->lock_admin) || $depot_out_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该出库单不可审核'));
			return;
		}
		if($depot_out_info->depot_out_number != $depot_out_info->depot_out_finished_number){
			echo json_encode(array('error'=>1,'msg'=>'出库数不等于预计出库数，该出库单不可审核'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_check_out ()
	{
		auth('depotout_audit');
		$depot_out_id = trim($this->input->post('depot_out_id'));
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($depot_out_info->audit_admin > 0 || empty($depot_out_info->lock_admin) || $depot_out_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该出库单不可审核'));
			return;
		}
		if($depot_out_info->depot_out_number != $depot_out_info->depot_out_finished_number){
			echo json_encode(array('error'=>1,'msg'=>'出库数不等于预计出库数，该出库单不可审核'));
			return;
		}
		
                
                //出库审核
                $this->depotio_model->check_out($depot_out_info, $this->admin_id);
                
                
		$filter = array();
		$depot_out_code = trim($this->input->post('depot_out_code'));
		if (!empty($depot_out_code)) $filter['depot_out_code'] = $depot_out_code;

		$provider_id = trim($this->input->post('provider_id'));
		if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

		$depot_out_type = trim($this->input->post('depot_out_type'));
		if (!empty($depot_out_type)) $filter['depot_out_type'] = $depot_out_type;

		$depot_out_status = trim($this->input->post('depot_out_status'));
		if (!empty($depot_out_status)) $filter['depot_out_status'] = $depot_out_status;

		$depot_depot_id = trim($this->input->post('depot_depot_id'));
		if (!empty($depot_depot_id)) $filter['depot_depot_id'] = $depot_depot_id;

		$provider_goods = trim($this->input->post('provider_goods'));
		if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

		$filter = get_pager_param($filter);
		$data = $this->depotio_model->depot_out_list($filter);

		$data['full_page'] = FALSE;
		$data['my_id'] = $this->admin_id;
		$data['content'] = $this->load->view('depot/depot_out_list', $data, TRUE);
		$data['error'] = 0;
		unset($data['list']);
		echo json_encode($data);
		return;
	}
	
	/*
	 * 打印出库检货单
	 */
	public function print_out_pick($depot_out_id){
		$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
		if ( empty($depot_out_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('depot_out_info', $depot_out_info);

//		$status_list = array('0'=>'请选择','1'=>'上架','2'=>'下架');
//		$data['provider_status'] = $status_list;

//		$provider_list = $this->depot_model->sel_provider_list();
//		$data['provider_list'] = $provider_list;

//		$brand_list = $this->depot_model->sel_brand_list();
//		$data['brand_list'] = $brand_list;

		//批次选择列表 @baolm
//		$batch_list = $this->depot_model->sel_batch_list();
//		$data['batch_list'] = $batch_list;

		$depot_out_goods = $this->depotio_model->depot_out_products($depot_out_id , TRUE);
		$data['goods_list'] = $depot_out_goods;

//		$type_list = $this->depot_model->sel_purchase_type_list();
//		$data['type_list'] = $type_list;

//		$data['imagedomain'] = '/public/images';

		$depot_list = $this->depot_model->sel_depot_list(0);
		$data['depot_name'] = $depot_list[$depot_out_info->depot_depot_id];
		$data['list'] = array();
		$data['full_page'] = TRUE;
		$this->load->view('depot/print_out_pick', $data);
	}

	public function get_batch_depot($batch_id) {
		$depot_list = $this->depotio_model->get_batch_depot($batch_id);
		//TODO 是否排除已存在的返供应商出库单
		$result['depot'] = '此批次中 ';
		$result['trans_out'] = FALSE;
		if(!empty($depot_list))
		{
			foreach ($depot_list as $row)
			{
				$result['depot'] .= $row->depot_name.',';
			}
			$result['depot'] .= ' 存在可出库商品';
		} else {
			$result['depot'] .= '不存在可出库商品';
		}
		$waiting_out = $this->depotio_model->get_batch_waiting_out($batch_id);
		if(!empty($waiting_out)) {
			$result['trans_out'] = TRUE;
		}
		
		echo json_encode($result);
		return;
	}
	
	public function transaction_log($batch_id=0) {
		auth('transaction_log');
		$filter = $this->uri->uri_to_assoc(3);
		$filter['provider_barcode'] = trim($this->input->post('provider_barcode'));
		$filter['location_name'] = trim($this->input->post('location_name'));
		$filter['batch_code'] = trim($this->input->post('batch_code'));
		$filter['trans_status'] = trim($this->input->post('trans_status'));
		if($batch_id > 0) {
			$this->load->model('purchase_batch_model');
			$batch = $this->purchase_batch_model->filter(array("batch_id"=>$batch_id));
			$filter['batch_code'] = $batch->batch_code;
			$filter['trans_status'] = "1,3";
		}
		$filter = get_pager_param($filter);
		
		$data = $this->depotio_model->filter_trans_log($filter);
		
		if ($this->input->is_ajax_request()) {
				
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/transaction_log', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		
		$data['full_page'] = TRUE;
		//$this->load->vars('batch_list', $this->purchase_batch_model->all_provider());
		$this->load->view('depot/transaction_log', $data);
	}

}
###
