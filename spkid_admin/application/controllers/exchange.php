<?php
#doc
#	classname:	Exchange
#	scope:		PUBLIC
#
#/doc

class Exchange extends CI_Controller
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
		$this->load->model('exchange_model');
		$this->load->model('purchase_batch_model');
	}

        /*
         * 查询调仓单列表，可指定搜索条件。
         */
	public function exchange_list ()
	{
		auth(array('exchange_view','exchange_add','exchangeout_edit','exchangein_edit','exchange_del','exchangeout_audit','exchangein_audit'));
		$filter = $this->uri->uri_to_assoc(3);
		$exchange_code = trim($this->input->post('exchange_code'));
		if (!empty($exchange_code)) $filter['exchange_code'] = $exchange_code;

		$exchange_status = trim($this->input->post('exchange_status'));
		if (!empty($exchange_status)) $filter['exchange_status'] = $exchange_status;

		$in_depot_id = trim($this->input->post('in_depot_id'));
		if (!empty($in_depot_id)) $filter['in_depot_id'] = $in_depot_id;

		$out_depot_id = trim($this->input->post('out_depot_id'));
		if (!empty($out_depot_id)) $filter['out_depot_id'] = $out_depot_id;

		$provider_goods = trim($this->input->post('provider_goods'));
		if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

		$filter = get_pager_param($filter);
		$data = $this->exchange_model->exchange_list($filter);
		$data['imagedomain'] = '/public/images';
		if ($this->input->post('is_ajax'))
		{
			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;

			$data['content'] = $this->load->view('depot/exchange_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}

		$status_list = array('0'=>'请选择','1'=>'出库未审核','2'=>'出库已审核，入库未审核','3'=>'入库已审核');
		$data['status_list'] = $status_list;

		$depot_list = $this->depot_model->sel_depot_list(1);
		$data['in_depot_list'] = $depot_list;
		$data['out_depot_list'] = $depot_list;

		$data['full_page'] = TRUE;
		$data['my_id'] = $this->admin_id;
		$this->load->view('depot/exchange_list', $data);
	}

        /*
         * 跳转到添加调仓单主信息的页面。
         */
	public function add_exchange ()
	{
		auth('exchange_add');
		$depot_list = $this->depot_model->sel_depot_list(1);
		$data['depot_list'] = $depot_list;

		$this->load->helper('form');
		$this->load->view('depot/exchange_add', $data);
	}

        /*
         * 跳转到编辑调仓单主信息的页面。
         */
	public function edit_exchange ($exchange_id = 0)
	{
		auth(array('exchange_view','exchange_add','exchangeout_edit','exchangein_edit','exchange_del','exchangeout_audit','exchangein_audit'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		$exchange_info = $this->exchange_model->format_exchange_info($exchange_info);
		if ( empty($exchange_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		if (empty($exchange_info->out_audit_admin) && $exchange_info->lock_admin == $this->admin_id && check_perm('exchangeout_edit'))
		{
			$data['is_edit_out'] = 1;
		} else
		{
			$data['is_edit_out'] = 0;
		}
		if (empty($exchange_info->in_audit_admin) && $exchange_info->lock_admin == $this->admin_id && check_perm('exchangein_edit'))
		{
			$data['is_edit_in'] = 1;
		} else
		{
			$data['is_edit_in'] = 0;
		}

		$status_list = array('0'=>'请选择','1'=>'出库未审核','2'=>'出库已审核，入库未审核','3'=>'入库已审核');
		$data['status_list'] = $status_list;

		$depot_list = $this->depot_model->sel_depot_list(1);
		$data['depot_list'] = $depot_list;

		$this->load->vars('row', $exchange_info);
		$this->load->view('depot/exchange_edit', $data);
	}

        /*
         * 添加调仓单主信息。
         */
	public function proc_add_exchange ()
	{
		auth('exchange_add');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('source_depot_id', '出库仓库', 'trim|not_empty');
		$this->form_validation->set_rules('dest_depot_id', '入库仓库', 'trim|not_empty');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['source_depot_id'] = $this->input->post('source_depot_id');
		$update['dest_depot_id'] = $this->input->post('dest_depot_id');
		$update['exchange_reason'] = $this->input->post('exchange_reason');
		$update['out_date'] = date('Y-m-d H:i:s');
		$update['out_admin'] = $this->admin_id;
		$update['exchange_code'] = $this->exchange_model->get_exchange_code();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;

		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_code'=>$update['exchange_code']));
		while (1)
		{
			if ( $exchange_info )
			{
				set_time_limit(1);
				$update['exchange_code'] = $this->exchange_model->get_exchange_code();
				$exchange_info = $this->exchange_model->filter_exchange(array('exchange_code'=>$update['exchange_code']));
			} else
			{
				break;
			}
		}

		$exchange_id = $this->exchange_model->insert_exchange($update);
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'/exchange/edit_exchange/'.$exchange_id)));
	}

        /*
         * 编辑调仓单主信息。
         */
	public function proc_edit_exchange ()
	{
		auth('exchange_add');
		$exchange_id = intval($this->input->post('exchange_id'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('source_depot_id', '出库仓库', 'trim|not_empty');
		$this->form_validation->set_rules('dest_depot_id', '入库仓库', 'trim|not_empty');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();

		$source_depot_id = $this->input->post('source_depot_id');
		if (!empty($source_depot_id))
			$update['source_depot_id'] = $source_depot_id;

		$dest_depot_id = $this->input->post('dest_depot_id');
		if (!empty($dest_depot_id))
			$update['dest_depot_id'] = $dest_depot_id;

		$update['exchange_reason'] = $this->input->post('exchange_reason');
		$update['out_date'] = date('Y-m-d H:i:s');
		$update['out_admin'] = $this->admin_id;
		$this->exchange_model->update_exchange($update, $exchange_id);
		sys_msg('操作成功！');
	}

        /*
         * 解锁调仓单。
         */
	public function unlock_exchange ($exchange_id)
	{
		//auth('manage_admin');
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($exchange_info->lock_admin != $this->admin_id)
		{
			sys_msg('该调仓单不可解锁！', 1);
		}

		$update = array();
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->exchange_model->update_exchange($update, $exchange_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/exchange/exchange_list')));
	}

        /*
         * 锁定调仓单。
         */
	public function lock_exchange ($exchange_id)
	{
		auth(array('exchange_add','exchangeout_edit','exchangein_edit','exchange_del','exchangeout_audit','exchangein_audit'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($exchange_info->in_audit_admin > 0 || $exchange_info->lock_admin > 0)
		{
			sys_msg('该调仓单不可锁定！', 1);
		}

		$update = array();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;
		$this->exchange_model->update_exchange($update, $exchange_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/exchange/exchange_list')));
	}

        /*
         * 查询已添加到调仓单的出库商品列表。
         */
	public function edit_out_exchange ($exchange_id = 0)
	{
		auth(array('exchange_view','exchange_add','exchangeout_edit','exchangein_edit','exchange_del','exchangeout_audit','exchangein_audit'));
		$filter = $this->uri->uri_to_assoc(4);
		$filter = get_pager_param($filter);
		if (!$this->input->post('is_ajax'))
		{
			$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
			if ( empty($exchange_info) )
			{
				sys_msg('记录不存在！', 1);
			}
			$this->load->vars('exchange_info', $exchange_info);

			$status_list = array('0'=>'请选择','1'=>'上架','2'=>'下架');
			$data['provider_status'] = $status_list;

			$provider_list = $this->depot_model->sel_provider_list();
			$data['provider_list'] = $provider_list;

			$brand_list = $this->depot_model->sel_brand_list();
			$data['brand_list'] = $brand_list;

			$exchange_out_goods = $this->exchange_model->exchange_out_products($exchange_id);
			$data['goods_list'] = $exchange_out_goods;

			$type_list = $this->depot_model->sel_purchase_type_list();
			$data['type_list'] = $type_list;

			$depot_list = $this->depot_model->sel_depot_list(0);
			$data['depot_name'] = $depot_list[$exchange_info->source_depot_id];

			$depot_filter = array();
			$depot_filter['sort_order'] = '';
			$depot_filter['record_count'] = count($exchange_out_goods);
			$depot_filter = page_and_size($depot_filter);
			$exchange_out_goods_limit = array();
			if (!empty($exchange_out_goods))
			{
				$i = 0;
				foreach ($exchange_out_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$exchange_out_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
			}

			$data['goods_list'] = $exchange_out_goods_limit;
			$data['depot_filter'] = $depot_filter;
		}

		if ($this->input->post('is_ajax'))
		{
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

			$with_not = trim($this->input->post('with_not'));
			if (!empty($with_not)) $filter['with_not'] = $with_not;

			$exchange_id = trim($this->input->post('exchange_id'));
			$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
			if (!empty($exchange_info)) $filter['trans_sn'] = $exchange_info->exchange_code;

			$data = $this->exchange_model->query_products_exchange_out($filter);

			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/out_edit_exchange', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}

		$data['list'] = array();
		$data['full_page'] = TRUE;
		if (empty($exchange_info->out_audit_admin) && $exchange_info->lock_admin == $this->admin_id && check_perm('exchangeout_edit'))
		{
			$this->load->view('depot/out_edit_exchange', $data);
		} else
		{
			$data['goods_list'] = $exchange_out_goods;
			$this->load->view('depot/out_view_exchange', $data);
		}

	}

        /*
         * 添加调仓出库商品。
         */
	public function add_exchange_product_out ()
	{
		//add
		auth('exchangeout_edit');
		$my_post = $this->input->post();
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'调仓单不存在'));
			return;
		}

		$this->db->query('BEGIN');
		foreach ($my_post as $key => $value)
		{
			if(strlen($key) > 6 && substr($key,0,6) == "check_")
			{
				$sub_id = substr($key,6);  //transaction_id
				if ($sub_id > 0 && $value > 0)
				{
					$product_sub_id = $this->depot_model->get_transaction_product_sub($sub_id);
					$rs = $this->exchange_model->insert_exchange_out_single($sub_id,$value,$exchange_id,$this->admin_id);
					if (empty($rs))
					{
						echo json_encode(array('error'=>1,'msg'=>'添加商品失败，调试信息：transaction_id:'.$sub_id.",product_number:".$value));
						return;
					} elseif ($rs == -1)
					{
						echo json_encode(array('error'=>1,'msg'=>'要添加出库的商品已存在调仓单','sub_id'=>$sub_id));
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
		$this->exchange_model->update_exchange_out_total($exchange_id);
		//show	
		$exchange_goods = $this->exchange_model->exchange_out_products($exchange_id);

		$error_msg = ''; 
		if(!empty($exchange_goods))
		{
			foreach ($exchange_goods as $item)
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
		$depot_filter['record_count'] = count($exchange_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);
		$exchange_out_goods_limit = array();
		if (!empty($exchange_goods))
		{
				$i = 0;
				foreach ($exchange_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$exchange_out_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $exchange_out_goods_limit;
		$data['row_num'] = count($exchange_out_goods_limit);
		$data['exchange_info'] = $exchange_info;

		$data['content'] = $this->load->view('depot/exchange_out_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}

        /*
         * 更新调仓出库商品。
         */
	public function update_exchange_out_product ()
	{
		auth('exchangeout_edit');
		$my_post = $this->input->post();
		$exchange_id = trim($this->input->post('exchange_id'));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'调仓单不存在'));
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
					$product_sub_id = $this->exchange_model->get_exchange_out_product_sub($sub_id);
					$rs = $this->exchange_model->update_exchange_out_product_x($sub_id,$exchange_id,$value,$exchange_info->exchange_code);
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
					echo json_encode(array('error'=>1,'msg'=>'更新商品数量失败，调试信息：exchange_sub_id:'.$sub_id.",product_number:".$value));
					return;
				}
			}
		}
		$this->exchange_model->update_exchange_out_total($exchange_id);
		//show
		$exchange_out_goods = $this->exchange_model->exchange_out_products($exchange_id);
		$error_msg = '';
		if(!empty($exchange_out_goods))
		{
			foreach ($exchange_out_goods as $item)
			{
				if ($item->real_num < 0)
				{
					$error_msg = '出库数量大于可出库数，调试信息 product_sn:'.$item->product_sn.',color_name:'.$item->color_name.',size_name:'.$item->size_name;
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
		$depot_filter['record_count'] = count($exchange_out_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$exchange_out_goods_limit = array();
		if (!empty($exchange_out_goods))
		{
				$i = 0;
				foreach ($exchange_out_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$exchange_out_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['exchange_info'] = $exchange_info;
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $exchange_out_goods_limit;
		$data['row_num'] = count($exchange_out_goods_limit);

		$data['content'] = $this->load->view('depot/exchange_out_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}

        /*
         * 删除调仓出库商品。
         */
	public function del_exchange_out_product()
	{
		auth('exchangeout_edit');
		$my_post = $this->input->post();
		$exchange_id = trim($this->input->post('exchange_id'));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'调仓单不存在'));
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
					$product_sub_id = $this->exchange_model->get_exchange_out_product_sub($sub_id);
					$rs = $this->exchange_model->del_exchange_out_product($sub_id,$exchange_id,$exchange_info->exchange_code);
					if (empty($rs))
					{
						echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：exchange_sub_id:'.$sub_id.",exchange_id:".$exchange_id));
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
					echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：exchange_sub_id:'.$sub_id.",exchange_id:".$exchange_id));
					return;
				}
			}
		}
		$this->exchange_model->update_exchange_out_total($exchange_id);

		$this->db->query('COMMIT');
		//show
		$exchange_out_goods = $this->exchange_model->exchange_out_products($exchange_id);

		$depot_filter = array();
		$depot_filter['page_size'] = $depot_page_size;
		$depot_filter['page'] = $depot_page;
		$depot_filter['record_count'] = count($exchange_out_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$exchange_out_goods_limit = array();
		if (!empty($exchange_out_goods))
		{
				$i = 0;
				foreach ($exchange_out_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$exchange_out_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['exchange_info'] = $exchange_info;
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $exchange_out_goods_limit;
		$data['row_num'] = count($exchange_out_goods_limit);

		$data['content'] = $this->load->view('depot/exchange_out_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}

        /*
         * 出库审核。
         */
	public function check_out_exchange ($exchange_id)
	{
		auth('exchangeout_audit');
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($exchange_info->out_audit_admin > 0 || empty($exchange_info->lock_admin) || $exchange_info->lock_admin != $this->admin_id)
		{
			sys_msg('该调仓单不可审核！', 1);
		}

		$update = array();
		$update['out_audit_date'] = date('Y-m-d H:i:s');
		$update['out_audit_admin'] = $this->admin_id;
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->db->query('BEGIN');
		$this->exchange_model->update_exchange($update, $exchange_id);
		$this->depot_model->update_transaction(array('trans_status'=>TRANS_STAT_OUT), array('trans_status'=>TRANS_STAT_AWAIT_OUT,'trans_sn'=>$exchange_info->exchange_code));
		$this->db->query('COMMIT');
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/exchange/exchange_list')));

	}

        /*
         * 入库审核。
         * TODO:校验入库商品信息和出库商品信息是否相同。
         */
	public function check_in_exchange ($exchange_id)
	{
		auth('exchangein_audit');
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($exchange_info->out_audit_admin == 0 || $exchange_info->in_audit_admin > 0 || empty($exchange_info->lock_admin) || $exchange_info->lock_admin != $this->admin_id)
		{
			sys_msg('该调仓单不可审核！', 1);
		}

		$update = array();
		$update['in_audit_date'] = date('Y-m-d H:i:s');
		$update['in_audit_admin'] = $this->admin_id;
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;

		$this->db->query('BEGIN');
		$this->depot_model->update_gl_num_in($exchange_info->exchange_code);
		$this->exchange_model->update_exchange($update, $exchange_id);
		$this->depot_model->update_transaction(array('trans_status'=>TRANS_STAT_IN), array('trans_status'=>TRANS_STAT_AWAIT_IN,'trans_sn'=>$exchange_info->exchange_code));
		$this->db->query('COMMIT');
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'/exchange/exchange_list')));
	}

        /*
         * 查询已添加到调仓单的入库商品列表。
         */
	public function edit_in_exchange ($exchange_id = 0)
	{
		auth(array('exchange_view','exchange_add','exchangeout_edit','exchangein_edit','exchange_del','exchangeout_audit','exchangein_audit'));
		$filter = $this->uri->uri_to_assoc(4);
		$filter = get_pager_param($filter);

		if (!$this->input->post('is_ajax'))
		{
			$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
			if ( empty($exchange_info) )
			{
				sys_msg('记录不存在！', 1);
			}
			$this->load->vars('exchange_info', $exchange_info);

			$status_list = array('0'=>'请选择','1'=>'上架','2'=>'下架');
			$data['provider_status'] = $status_list;

			$provider_list = $this->depot_model->sel_provider_list();
			$data['provider_list'] = $provider_list;

			$brand_list = $this->depot_model->sel_brand_list();
			$data['brand_list'] = $brand_list;

			$exchange_in_goods = $this->exchange_model->exchange_in_products($exchange_id);
			$data['goods_list'] = $exchange_in_goods;

			$type_list = $this->depot_model->sel_purchase_type_list();
			$data['type_list'] = $type_list;
			$data['imagedomain'] = '/public/images';

			$depot_filter = array();
			$depot_filter['sort_order'] = '';
			$depot_filter['record_count'] = count($exchange_in_goods);
			$depot_filter = page_and_size($depot_filter);
			$exchange_in_goods_limit = array();
			if (!empty($exchange_in_goods))
			{
				$i = 0;
				foreach ($exchange_in_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$exchange_in_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
			}

			$data['goods_list'] = $exchange_in_goods_limit;
			$data['depot_filter'] = $depot_filter;

			$depot_list = $this->depot_model->sel_depot_list(0);
			$data['depot_name'] = $depot_list[$exchange_info->dest_depot_id];
		}

		if ($this->input->post('is_ajax'))
		{
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

			$exchange_id = trim($this->input->post('exchange_id'));
			if (!empty($exchange_id)) $filter['exchange_id'] = $exchange_id;

			$with_not = trim($this->input->post('with_not'));
			if (!empty($with_not)) $filter['with_not'] = $with_not;

			$data = $this->exchange_model->query_products_exchange_in($filter);

			$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
			$data['exchange_info'] = $exchange_info;

			$data['full_page'] = FALSE;
			$data['imagedomain'] = '/public/images';
			$data['content'] = $this->load->view('depot/in_edit_exchange', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}

		$data['list'] = array();
		$data['full_page'] = TRUE;
		if (empty($exchange_info->in_audit_admin) && $exchange_info->lock_admin == $this->admin_id && check_perm('exchangein_edit'))
		{
			$this->load->view('depot/in_edit_exchange', $data);
		} else
		{
			$data['goods_list'] = $exchange_in_goods;
			$this->load->view('depot/in_view_exchange', $data);
		}

	}

        /*
         * 为入库商品分配储位及数量。
         * TODO:
         */
	public function add_exchange_product_in_simple ()
	{
		//add
		auth('exchangein_edit');
		$my_post = $this->input->post();
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'调仓单不存在'));
			return;
		}
		$location_code = trim($this->input->post('location_code'));
		$in_num = trim($this->input->post('in_num'));
		$sub_id = trim($this->input->post('sub_id'));
		$batch_id = trim($this->input->post('batch_id'));
		if (empty($location_code) || empty($in_num) || empty($sub_id) || empty($batch_id))
		{
			echo json_encode(array('error'=>1,'msg'=>'参数错误'));
			return;
		}
		$location_id = $this->depot_model->check_depot_location($exchange_info->dest_depot_id,$location_code);
		if(empty($location_id))
		{
			 echo json_encode(array('error'=>1,'msg'=>'参数错误'));
			 return;
		}
		$this->db->query('BEGIN');

		$exchange_out = $this->exchange_model->query_exchange_out(array("sub_id"=>$sub_id,"exchange_id"=>$exchange_id,"batch_id"=>$batch_id));
		if(empty($exchange_out)){
		     echo json_encode(array('error'=>1,'msg'=>'不存在对应出库信息记录'));
		     return;
		}
		/*$exchange_out_info = $exchange_out[0];
		if(empty($exchange_out_info)){
		    echo json_encode(array('error'=>1,'msg'=>'不存在对应出库信息'));
		    return;
		}*/
                $out_num = 0;
                if(!empty($exchange_out)){
		    foreach ($exchange_out as $exchange_out_info){
			$out_num += $exchange_out_info->product_number;
		    }
		}
                
		$old_in_num = 0;
		$exchange_in = $this->exchange_model->query_exchange_in(array("sub_id"=>$sub_id,"exchange_id"=>$exchange_id,"batch_id"=>$batch_id));
		
		if(!empty($exchange_in)){
		    foreach ($exchange_in as $exchange_in_info){
			$old_in_num += $exchange_in_info->product_number;
		    }
		}
		//$out_num = $exchange_out_info->product_number;
		if($in_num > $out_num - $old_in_num){
		    echo json_encode(array('error'=>1,'msg'=>'商品入库数量不能大于出库数量'));
		    return;
		}
		$rs = $this->exchange_model->insert_exchange_in_single($sub_id,$in_num,$exchange_id,$exchange_info->dest_depot_id,$location_id,$this->admin_id,$batch_id);
		if (empty($rs))
		{
			echo json_encode(array('error'=>1,'msg'=>'添加商品失败，调试信息：sub_id:'.$sub_id.",exchange_id:".$exchange_id.",location_id:".$location_id.",num:".$in_num));
			return;
		} elseif ($rs == -1)
		{
			echo json_encode(array('error'=>1,'msg'=>'要添加的入库商品已存在调仓单','sub_id'=>$sub_id,'subvalue'=>$location_code));
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

		$this->exchange_model->update_exchange_in_total($exchange_id);
		$this->db->query('COMMIT');
		//show
		$data['error'] = 0;
		$data['exchange_leaf_id'] = $rs;
		echo json_encode($data);
		return;
	}

	public function add_exchange_product_in ()
	{
		//add
		auth('exchangein_edit');
		$my_post = $this->input->post();
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'调仓单不存在'));
			return;
		}

		$this->db->query('BEGIN');
		foreach ($my_post as $key => $value)
		{
			if(strlen($key) > 8 && substr($key,0,8) == "checkp__")
			{
				$tmp_str = substr($key,8);
				$tmp_arr = explode('__',$tmp_str);
				if (count($tmp_arr) == 2 && $tmp_arr[0] > 0 && !empty($tmp_arr[1]) && $value > 0)
				{
					$sub_id = $tmp_arr[0];
					$location_code = $tmp_arr[1];
					$location_id = $this->depot_model->check_depot_location($exchange_info->dest_depot_id,$location_code);
					if(empty($location_id))
					{
						 echo json_encode(array('error'=>1,'msg'=>'无效的储位编码'));
						 return;
					}
					$rs = $this->exchange_model->insert_exchange_in_single($sub_id,$value,$exchange_id,$exchange_info->dest_depot_id,$location_id,$this->admin_id);
					if (empty($rs))
					{
						echo json_encode(array('error'=>1,'msg'=>'添加商品失败，调试信息：sub_id:'.$sub_id.",exchangeid:".$exchange_id.",location_id:".$location_id.",num:".$value));
						return;
					} elseif ($rs == -1)
					{
						echo json_encode(array('error'=>1,'msg'=>'要添加的入库商品已存在调仓单','sub_id'=>$sub_id,'subvalue'=>$location_code));
						return;
					}


				} else
				{
					echo json_encode(array('error'=>1,'msg'=>'参数错误'));
			 		return;
				}
			}
		}
		$this->exchange_model->update_exchange_in_total($exchange_id);
		//show
		$exchange_in_goods = $this->exchange_model->exchange_in_products($exchange_id);
		$this->db->query('COMMIT');
		//show

		$depot_filter = array();
		if (isset($my_post['depot_page_size']) && !empty($my_post['depot_page_size']))
		{
			$depot_filter['page_size'] = $my_post['depot_page_size'];
		}
		$depot_filter['record_count'] = count($exchange_in_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);
		$exchange_in_goods_limit = array();
		if (!empty($exchange_in_goods))
		{
				$i = 0;
				foreach ($exchange_in_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$exchange_in_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $exchange_in_goods_limit;
		$data['row_num'] = count($exchange_in_goods_limit);
		$data['exchange_info'] = $exchange_info;
		$data['content'] = $this->load->view('depot/exchange_in_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}


	public function flash_exchange_in ()
	{
		//flush
		//auth('manage_admin');
		//$my_post = $this->input->post();
		$exchange_id = trim($this->input->post('exchange_id'));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'调仓单不存在'));
			return;
		}

		$exchange_in_goods = $this->exchange_model->exchange_in_products($exchange_id);

		$depot_filter = array();
		$depot_filter['page_size'] = $depot_page_size;
		$depot_filter['page'] = $depot_page;
		$depot_filter['record_count'] = count($exchange_in_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$exchange_in_goods_limit = array();
		if (!empty($exchange_in_goods))
		{
				$i = 0;
				foreach ($exchange_in_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$exchange_in_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['exchange_info'] = $exchange_info;
		$data['goods_list'] = $exchange_in_goods_limit;
		$data['row_num'] = count($exchange_in_goods_limit);
		$data['content'] = $this->load->view('depot/exchange_in_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}

	public function flash_exchange_out ()
	{
		//flush
		//auth('manage_admin');
		//$my_post = $this->input->post();
		$exchange_id = trim($this->input->post('exchange_id'));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'调仓单不存在'));
			return;
		}

		$exchange_out_goods = $this->exchange_model->exchange_out_products($exchange_id);

		$depot_filter = array();
		$depot_filter['page_size'] = $depot_page_size;
		$depot_filter['page'] = $depot_page;
		$depot_filter['record_count'] = count($exchange_out_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$exchange_out_goods_limit = array();
		if (!empty($exchange_out_goods))
		{
				$i = 0;
				foreach ($exchange_out_goods as $key=>$item)
				{

					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$exchange_out_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['depot_filter'] = $depot_filter;
		$data['exchange_info'] = $exchange_info;
		$data['goods_list'] = $exchange_out_goods_limit;
		$data['row_num'] = count($exchange_out_goods_limit);
		$data['content'] = $this->load->view('depot/exchange_out_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}

	public function del_exchange_in_product ()
	{
		auth('exchangein_edit');
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_leaf_id = trim($this->input->post('exchange_leaf_id'));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		if ( empty($exchange_id) || empty($exchange_leaf_id))
		{
			echo json_encode(array('error'=>1,'msg'=>'参数错误'));
			return;
		}
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'调仓单不存在'));
			return;
		}
		$this->db->query('BEGIN');
		$rs = $this->exchange_model->del_exchange_in_product($exchange_leaf_id,$exchange_id,$exchange_info->exchange_code);
		if (empty($rs))
		{
			echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：exchange_leaf_id:'.$exchange_leaf_id.",exchange_id:".$exchange_id));
			return;
		}

		$this->exchange_model->update_exchange_in_total($exchange_id);

		//update gl_num
		/*
		if (!$this->depot_model->update_gl_num(array('sub_id'=>$depot_in_sub_info->product_sub_id)))
		{
			echo json_encode(array('error'=>1,'msg'=>'更新gl_num错误'));
			return;
		}
		*/
		$this->db->query('COMMIT');

		$exchange_in_goods = $this->exchange_model->exchange_in_products($exchange_id);

		$depot_filter = array();
		$depot_filter['page_size'] = $depot_page_size;
		$depot_filter['page'] = $depot_page;
		$depot_filter['record_count'] = count($exchange_in_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$exchange_in_goods_limit = array();
		if (!empty($exchange_in_goods))
		{
				$i = 0;
				foreach ($exchange_in_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$exchange_in_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['exchange_info'] = $exchange_info;
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $exchange_in_goods_limit;
		$data['row_num'] = count($exchange_in_goods_limit);
		$data['content'] = $this->load->view('depot/exchange_in_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;

	}

	public function update_exchange_in_product ()
	{
		auth('exchangein_edit');
		$my_post = $this->input->post();
		$exchange_id = trim($this->input->post('exchange_id'));
		$depot_page = trim($this->input->post('depot_page'));
		$depot_page_size = trim($this->input->post('depot_page_size'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'调仓单不存在'));
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
					$old_in_num = 0;
					$in_num = 0;
					$old_sub_id = 0;
					$exchange_in = $this->exchange_model->query_exchange_in(array("exchange_leaf_id"=>$sub_id,"exchange_id"=>$exchange_id));
					if(empty($exchange_in)){
					     echo json_encode(array('error'=>1,'msg'=>'不存在对应入库信息记录'));
					     return ;
					}else{
					    $exchange_in_info = $exchange_in[0];
					    
					    $old_sub_id = $exchange_in_info ->sub_id;                                           
                                            foreach ($exchange_in as $exchange_in_tmp2){
					       $old_in_num += $exchange_in_tmp2->product_number;
                                            }
					}
					$exchange_in_list = $this->exchange_model->query_exchange_in(array("sub_id"=>$old_sub_id,"exchange_id"=>$exchange_id));
					foreach ($exchange_in_list as $exchange_in_tmp){
					       $in_num += $exchange_in_tmp->product_number;
					}
					$old_in_num = $in_num - $old_in_num;
					$exchange_out = $this->exchange_model->query_exchange_out(array("exchange_leaf_id"=>$sub_id,"exchange_id"=>$exchange_id));
					if(empty($exchange_out)){
					     echo json_encode(array('error'=>1,'msg'=>'不存在对应出库信息记录'));
					     return;
					}
					//$exchange_out_info = $exchange_out[0];
					//$out_num = $exchange_out_info ->product_number;
					if(empty($exchange_out)){
					    echo json_encode(array('error'=>1,'msg'=>'不存在对应出库信息'));
					    return;
					}
                                        
                                        foreach ($exchange_out as $exchange_out_tmp){
					    $out_num += $exchange_out_tmp->product_number;
                                        }
                                        
					if($value > ($out_num - $old_in_num)){
					    echo json_encode(array('error'=>1,'msg'=>'商品入库数量不能大于出库数量'));
					    return;
					}
					$rs = $this->exchange_model->update_exchange_in_product_x($sub_id,$exchange_id,$value,$exchange_info->exchange_code);
				} else
				{
					echo json_encode(array('error'=>1,'msg'=>'更新商品数量失败，调试信息：exchange_leaf_id:'.$sub_id.",product_number:".$value));
					return;
				}
			}
		}
		$this->exchange_model->update_exchange_in_total($exchange_id);
		//show
		$exchange_in_goods = $this->exchange_model->exchange_in_products($exchange_id);
		$this->db->query('COMMIT');

		$depot_filter = array();
		$depot_filter['page_size'] = $depot_page_size;
		$depot_filter['page'] = $depot_page;
		$depot_filter['record_count'] = count($exchange_in_goods);
		$depot_filter['sort_order'] = '';
		$depot_filter = page_and_size($depot_filter);

		$exchange_in_goods_limit = array();
		if (!empty($exchange_in_goods))
		{
				$i = 0;
				foreach ($exchange_in_goods as $key=>$item)
				{
					if ($i >= ($depot_filter['page']-1)*$depot_filter['page_size'] && $i < $depot_filter['page']*$depot_filter['page_size'])
					{
						$exchange_in_goods_limit[$key] = $item;
					} else
					{

					}

					$i += 1;
				}
		}
		$data['imagedomain'] = '/public/images';
		$data['exchange_info'] = $exchange_info;
		$data['depot_filter'] = $depot_filter;
		$data['goods_list'] = $exchange_in_goods_limit;
		$data['row_num'] = count($exchange_in_goods_limit);
		$data['content'] = $this->load->view('depot/exchange_in_lib', $data, TRUE);
		unset($data['goods_list']);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}

        /*
         * 删除调仓单。
         */
	public function delete_exchange ($exchange_id)
	{
		auth('exchange_del');
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		if ($exchange_info->out_audit_admin > 0 || empty($exchange_info->lock_admin) || $exchange_info->lock_admin != $this->admin_id)
		{
			sys_msg('该调仓单不可删除！', 1);
		}
		$this->db->query('BEGIN');
		if ($this->exchange_model->delete_exchange($exchange_id) == 1)
		{
			$this->depot_model->update_gl_num_out($exchange_info->exchange_code);
			$this->exchange_model->delete_exchange_product(array('exchange_id'=>$exchange_id));
			$this->depot_model->delete_transaction(array('trans_sn'=>$exchange_info->exchange_code));
			$this->db->query('COMMIT');
			sys_msg('操作成功！',0 , array(array('text'=>'返回', 'href'=>'/exchange/exchange_list')));
		} else
		{
			sys_msg('删除失败！',1);
		}
	}

	public function check_delete ()
	{
		auth('exchange_del');
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($exchange_info->out_audit_admin > 0 || empty($exchange_info->lock_admin) || $exchange_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该调仓单不可删除'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_delete ()
	{
		auth('exchange_del');
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($exchange_info->out_audit_admin > 0 || empty($exchange_info->lock_admin) || $exchange_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该调仓单不可删除'));
			return;
		}
		$this->db->query('BEGIN');
		if ($this->exchange_model->delete_exchange($exchange_id) == 1)
		{
			$this->depot_model->update_gl_num_out($exchange_info->exchange_code);
			$this->exchange_model->delete_exchange_product(array('exchange_id'=>$exchange_id));
			$this->depot_model->delete_transaction(array('trans_sn'=>$exchange_info->exchange_code));
			$this->db->query('COMMIT');

			$filter = array();
			$exchange_code = trim($this->input->post('exchange_code'));
			if (!empty($exchange_code)) $filter['exchange_code'] = $exchange_code;

			$exchange_status = trim($this->input->post('exchange_status'));
			if (!empty($exchange_status)) $filter['exchange_status'] = $exchange_status;

			$in_depot_id = trim($this->input->post('in_depot_id'));
			if (!empty($in_depot_id)) $filter['in_depot_id'] = $in_depot_id;

			$out_depot_id = trim($this->input->post('out_depot_id'));
			if (!empty($out_depot_id)) $filter['out_depot_id'] = $out_depot_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$filter = get_pager_param($filter);
			$data = $this->exchange_model->exchange_list($filter);
			$data['imagedomain'] = '/public/images';

			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;

			$data['content'] = $this->load->view('depot/exchange_list', $data, TRUE);
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

	public function check_lock ()
	{
		auth(array('exchange_add','exchangeout_edit','exchangein_edit','exchange_del','exchangeout_audit','exchangein_audit'));
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($exchange_info->in_audit_admin > 0 || $exchange_info->lock_admin > 0)
		{
			echo json_encode(array('error'=>1,'msg'=>'该调仓单不可锁定'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_lock ()
	{
		auth(array('exchange_add','exchangeout_edit','exchangein_edit','exchange_del','exchangeout_audit','exchangein_audit'));
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($exchange_info->in_audit_admin > 0 || $exchange_info->lock_admin > 0)
		{
			echo json_encode(array('error'=>1,'msg'=>'该调仓单不可锁定'));
			return;
		}
		$update = array();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;
		$this->exchange_model->update_exchange($update, $exchange_id);

			$filter = array();
			$exchange_code = trim($this->input->post('exchange_code'));
			if (!empty($exchange_code)) $filter['exchange_code'] = $exchange_code;

			$exchange_status = trim($this->input->post('exchange_status'));
			if (!empty($exchange_status)) $filter['exchange_status'] = $exchange_status;

			$in_depot_id = trim($this->input->post('in_depot_id'));
			if (!empty($in_depot_id)) $filter['in_depot_id'] = $in_depot_id;

			$out_depot_id = trim($this->input->post('out_depot_id'));
			if (!empty($out_depot_id)) $filter['out_depot_id'] = $out_depot_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$filter = get_pager_param($filter);
			$data = $this->exchange_model->exchange_list($filter);
			$data['imagedomain'] = '/public/images';

			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;

			$data['content'] = $this->load->view('depot/exchange_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
	}

	public function check_unlock ()
	{
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($exchange_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该调仓单不可解锁'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_unlock ()
	{
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($exchange_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该调仓单不可解锁'));
			return;
		}
		$update = array();
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->exchange_model->update_exchange($update, $exchange_id);

			$filter = array();
			$exchange_code = trim($this->input->post('exchange_code'));
			if (!empty($exchange_code)) $filter['exchange_code'] = $exchange_code;

			$exchange_status = trim($this->input->post('exchange_status'));
			if (!empty($exchange_status)) $filter['exchange_status'] = $exchange_status;

			$in_depot_id = trim($this->input->post('in_depot_id'));
			if (!empty($in_depot_id)) $filter['in_depot_id'] = $in_depot_id;

			$out_depot_id = trim($this->input->post('out_depot_id'));
			if (!empty($out_depot_id)) $filter['out_depot_id'] = $out_depot_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$filter = get_pager_param($filter);
			$data = $this->exchange_model->exchange_list($filter);
			$data['imagedomain'] = '/public/images';

			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;

			$data['content'] = $this->load->view('depot/exchange_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
	}

	public function check_check_out ()
	{
		auth('exchangeout_audit');
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($exchange_info->out_audit_admin > 0 || empty($exchange_info->lock_admin) || $exchange_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该调仓单不可审核'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_check_out ()
	{
		auth('exchangeout_audit');
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($exchange_info->out_audit_admin > 0 || empty($exchange_info->lock_admin) || $exchange_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该调仓单不可审核'));
			return;
		}

		$update = array();
		$update['out_audit_date'] = date('Y-m-d H:i:s');
		$update['out_audit_admin'] = $this->admin_id;
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->db->query('BEGIN');
		$this->exchange_model->update_exchange($update, $exchange_id);
		$this->depot_model->update_transaction(array('trans_status'=>TRANS_STAT_OUT,'update_admin'=>$this->admin_id,'update_date'=>date('Y-m-d H:i:s')), array('trans_status'=>TRANS_STAT_AWAIT_OUT,'trans_sn'=>$exchange_info->exchange_code));
		$this->db->query('COMMIT');

			$filter = array();
			$exchange_code = trim($this->input->post('exchange_code'));
			if (!empty($exchange_code)) $filter['exchange_code'] = $exchange_code;

			$exchange_status = trim($this->input->post('exchange_status'));
			if (!empty($exchange_status)) $filter['exchange_status'] = $exchange_status;

			$in_depot_id = trim($this->input->post('in_depot_id'));
			if (!empty($in_depot_id)) $filter['in_depot_id'] = $in_depot_id;

			$out_depot_id = trim($this->input->post('out_depot_id'));
			if (!empty($out_depot_id)) $filter['out_depot_id'] = $out_depot_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$filter = get_pager_param($filter);
			$data = $this->exchange_model->exchange_list($filter);
			$data['imagedomain'] = '/public/images';

			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;

			$data['content'] = $this->load->view('depot/exchange_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
	}

        /*
         * 检查调仓单是否可以入库审核。
         */
	public function check_check_in ()
	{
		auth('exchangein_audit');
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		$exchange_in = $this->exchange_model->filter_exchange_in(array('exchange_id' => $exchange_id));
		if (count($exchange_in) == 0)
		{
			echo json_encode(array('error'=>0,'msgr'=>'该调仓单没有入库商品'));
			return;
		}
		if ($exchange_info->out_audit_admin == 0 || $exchange_info->in_audit_admin > 0 || empty($exchange_info->lock_admin) || $exchange_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该调仓单不可审核'));
			return;
		}
		//TODO 验证批次 - 已结算、锁定
		$batch_is_reckoned = false;
		foreach ( $exchange_in as $val ) 
		{
			$purchase_batch = $this->purchase_batch_model->filter(array('batch_id'=>$val->batch_id));
			if($purchase_batch->is_reckoned == 1) 
			{
				$batch_is_reckoned = true;
			}
			if(!empty($purchase_batch->lock_admin)) {
				echo json_encode(array('error'=>1,'msg'=>'调仓单含有已锁定批次的商品，暂不能审核，请等待'));
				return;
			}
		}
		if($batch_is_reckoned) {
			echo json_encode(array('error'=>0,'msgr'=>'调仓单含有已经结算批次的商品'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

        /*
         * 入库审核。
         */
	public function proc_check_in ()
	{
		auth('exchangein_audit');
		$exchange_id = trim($this->input->post('exchange_id'));
		$exchange_info = $this->exchange_model->filter_exchange(array('exchange_id' => $exchange_id));
		if ( empty($exchange_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($exchange_info->out_audit_admin == 0 || $exchange_info->in_audit_admin > 0 || empty($exchange_info->lock_admin) || $exchange_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该调仓单不可审核'));
			return;
		}
		$update = array();
		$update['in_audit_date'] = date('Y-m-d H:i:s');
		$update['in_audit_admin'] = $this->admin_id;
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;

                //TODO:检查出库商品信息和入库商品信息是否相同
                
		$this->db->query('BEGIN');
		$this->depot_model->update_gl_num_in($exchange_info->exchange_code);
		$this->exchange_model->update_exchange($update, $exchange_id);
		$this->depot_model->update_transaction(array('trans_status'=>TRANS_STAT_IN,'update_admin'=>$this->admin_id,'update_date'=>date('Y-m-d H:i:s')), array('trans_status'=>TRANS_STAT_AWAIT_IN,'trans_sn'=>$exchange_info->exchange_code));
		$this->db->query('COMMIT');

                $filter = array();
                $exchange_code = trim($this->input->post('exchange_code'));
                if (!empty($exchange_code)) $filter['exchange_code'] = $exchange_code;

                $exchange_status = trim($this->input->post('exchange_status'));
                if (!empty($exchange_status)) $filter['exchange_status'] = $exchange_status;

                $in_depot_id = trim($this->input->post('in_depot_id'));
                if (!empty($in_depot_id)) $filter['in_depot_id'] = $in_depot_id;

                $out_depot_id = trim($this->input->post('out_depot_id'));
                if (!empty($out_depot_id)) $filter['out_depot_id'] = $out_depot_id;

                $provider_goods = trim($this->input->post('provider_goods'));
                if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

                $filter = get_pager_param($filter);
                $data = $this->exchange_model->exchange_list($filter);
                $data['imagedomain'] = '/public/images';

                $data['full_page'] = FALSE;
                $data['my_id'] = $this->admin_id;

                $data['content'] = $this->load->view('depot/exchange_list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
	}

}
###