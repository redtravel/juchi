<?php
#doc
#	classname:	Purchase
#	scope:		PUBLIC
#
#/doc

class Purchase extends CI_Controller
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
	}



	public function index ($overtime5 = 0)
	{
		auth(array('purchase_view','purchase_add','purchase_del','purchase_stop','purchase_audit'));
		$this->load->model('brand_model');
		$filter = $this->uri->uri_to_assoc(3);
		$purchase_code = trim($this->input->post('purchase_code'));
		if (!empty($purchase_code)) $filter['purchase_code'] = $purchase_code;

		$purchase_provider = trim($this->input->post('purchase_provider'));
		if (!empty($purchase_provider)) $filter['purchase_provider'] = $purchase_provider;

		$purchase_type = trim($this->input->post('purchase_type'));
		if (!empty($purchase_type)) $filter['purchase_type'] = $purchase_type;

		$purchase_status = trim($this->input->post('purchase_status'));
		if (!empty($purchase_status)) $filter['purchase_status'] = $purchase_status;

		$provider_goods = trim($this->input->post('provider_goods'));
		if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

		$purchase_batch = trim($this->input->post('purchase_batch'));
		if (!empty($purchase_batch)) $filter['purchase_batch'] = $purchase_batch;

		$is_consign = trim($this->input->post('is_consign'));
		if ($is_consign != null && $is_consign != '') $filter['is_consign'] = $is_consign;
		
		$provider_productcode = trim($this->input->post('provider_productcode'));
		if (!empty($provider_productcode)) $filter['provider_productcode'] = $provider_productcode;
		
		$brand_id = trim($this->input->post('brand_id'));
		if (!empty($brand_id)) $filter['brand_id'] = $brand_id;
                
                $filter['overtime5'] = $overtime5;
                
		$filter = get_pager_param($filter);
		$data = $this->depot_model->purchase_list($filter);
		if ($this->input->post('is_ajax'))
		{
			$data['full_page'] = FALSE;
			$data['my_id'] = $this->admin_id;
			$data['content'] = $this->load->view('purchase/purchase_list', $data, TRUE);
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
		
		$data['brand_list'] = $this->brand_model->all_brand();

		$data['full_page'] = TRUE;
		$data['my_id'] = $this->admin_id;
		$this->load->view('purchase/purchase_list', $data);
	}

	public function add ($provider_id = 0, $batch_id = 0 )
	{
		auth('purchase_add');
		$status_list = array('0'=>'请选择','1'=>'未审核','2'=>'已审核','3'=>'<font color="red">已终止</font>','4'=>'<font color="red">完成</font>');
		$data['status_list'] = $status_list;

                //从批次管理获取得provier_id,batch_id
		if (!empty($provider_id )){
                    $data['provider_id'] = $provider_id;
                    if (!empty($batch_id) ){
                        $this->load->model("purchase_model");
                        $batch_list = $this->purchase_model->get_purchase_batch($provider_id , "batch_id,batch_code" ,1);
                        $data["batch_id"] = $batch_id;
                        $data['batch_list'] = $batch_list;
                    }
                }
                
		$type_list = $this->depot_model->sel_purchase_type_list();
		$data['type_list'] = $type_list;

		$provider_list = $this->depot_model->sel_provider_list();
		$data['provider_list'] = $provider_list;

		$brand_list = $this->depot_model->sel_brand_list();
		$data['brand_list'] = $brand_list;

		$this->load->helper('form');
		$this->load->view('purchase/purchase_add', $data);
	}

        /**
         * 获取采购批次
         */
        public function get_purchase_batch ( ){
            //此处不需要验证权限 guannan.shang
            //auth('purchase_add');
            $this->load->helper('form');
            $this->load->model('purchase_model');
            $provider_id = $_REQUEST['provider_id'];
            $batch_name = (isset($_REQUEST['batch_name'] )&& $_REQUEST['batch_name']  != '') ? $_REQUEST['batch_name'] : 'purchase_batch' ;
            $is_use = $_REQUEST['is_use'];//是否为可用批次，1：可用批次，0:全部批次。
            $is_use = ( !empty($is_use ) && intval($is_use) === 1 )?1:0;
            $batch_list = $this->purchase_model->get_purchase_batch($provider_id , "batch_id,batch_code", $is_use);
            $data['select_batch'] = form_dropdown( $batch_name , $batch_list );
            echo json_encode($data['select_batch'] );
            return ;
        }
        
        
	public function edit ($purchase_id = 0)
	{
		auth(array('purchase_view','purchase_add'));
		$this->load->model('brand_model');
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		$purchase_info = $this->depot_model->format_purchase_info($purchase_info);
		if ( empty($purchase_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		if (empty($purchase_info->purchase_check_admin) && $purchase_info->lock_admin == $this->admin_id && check_perm('purchase_add'))
		{
			$data['is_edit'] = 1;
		} else
		{
			$data['is_edit'] = 0;
		}

		$status_list = array('0'=>'请选择','1'=>'未审核','2'=>'已审核','3'=>'<font color="red">已中止</font>','4'=>'<font color="red">完成</font>');
		$data['status_list'] = $status_list;

		$type_list = $this->depot_model->sel_purchase_type_list();
		$data['type_list'] = $type_list;

		$provider_list = $this->depot_model->sel_provider_list();
		$data['provider_list'] = $provider_list;

// 		$brand_list = $this->depot_model->sel_brand_list();
// 		$data['brand_list'] = $brand_list;
		$brand = $this->brand_model->filter(array('brand_id'=>$purchase_info->purchase_brand));
		$data['brand'] = $brand;

		$this->load->model('purchase_model');
		$this->load->helper('form');
		$batch_list = $this->purchase_model->get_purchase_batch($purchase_info->purchase_provider , "batch_id,batch_code", 1 );
		$data['batch_list'] = $batch_list;
		
		$this->load->vars('row', $purchase_info);
		$this->load->view('purchase/purchase_edit', $data);
	}

	public function edit_product ($purchase_id = 0)
	{
		auth(array('purchase_view','purchase_add'));
		$filter = $this->uri->uri_to_assoc(4);
		$filter = get_pager_param($filter);
		if (!$this->input->post('is_ajax'))
		{
			$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
			if ( empty($purchase_info) )
			{
				sys_msg('记录不存在！', 1);
			}
			$this->load->vars('purchase_info', $purchase_info);

			$status_list = array('0'=>'请选择','1'=>'上架','2'=>'下架');
			$data['provider_status'] = $status_list;

			$provider_list = $this->depot_model->sel_provider_list();
			$data['provider_list'] = $provider_list;

			$brand_list = $this->depot_model->sel_brand_list();
			$data['brand_list'] = $brand_list;

			$purchase_goods = $this->depot_model->purchase_products($purchase_id,$purchase_info->purchase_code);
			$data['goods_list'] = $purchase_goods;

			$type_list = $this->depot_model->sel_purchase_type_list();
			$data['type_list'] = $type_list;

			$provider_list = $this->depot_model->sel_provider_list();
			$data['provider_list'] = $provider_list;
		}

		if ($this->input->post('is_ajax'))
		{
                        $purchase_id = trim($this->input->post('purchase_id'));
			if (!empty($purchase_id)) $filter['purchase_id'] = $purchase_id;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$brand_id = trim($this->input->post('brand_id'));
			if (!empty($brand_id)) $filter['brand_id'] = $brand_id;

			$provider_id = trim($this->input->post('provider_id'));
			if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

			$purchase_status = trim($this->input->post('provider_status'));
			if (!empty($purchase_status)) $filter['purchase_status'] = $purchase_status;

			$purchase_type = trim($this->input->post('purchase_type'));
			if (!empty($purchase_type)) $filter['purchase_type'] = $purchase_type;

			$with_not = trim($this->input->post('with_not'));
			if (!empty($with_not)) $filter['with_not'] = $with_not;

			$data = $this->depot_model->query_products($filter);

			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('purchase/purchase_edit_product', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}

		$data['list'] = array();
		$data['full_page'] = TRUE;
		if (empty($purchase_info->purchase_check_admin) && $purchase_info->lock_admin == $this->admin_id && check_perm('purchase_add'))
		{
			$this->load->view('purchase/purchase_edit_product', $data);
		} else
		{
			$this->load->view('purchase/purchase_view_product', $data);
		}

	}

        /**
         * 添加全部商品到采购单
         * @return type 
         */
	public function add_product_all ()
	{
		auth('purchase_add');
                $filter = array();
		$filter['purchase_id'] = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $filter['purchase_id'] ));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'采购单不存在'));
			return;
		}
		
		$provider_goods = trim($this->input->post('provider_goods'));
		if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

		$brand_id = trim($this->input->post('brand_id'));
		if (!empty($brand_id)) $filter['brand_id'] = $brand_id;

		$provider_id = trim($this->input->post('provider_id'));
		if (!empty($provider_id)) $filter['provider_id'] = $provider_id;

		$purchase_status = trim($this->input->post('purchase_status'));
		if (!empty($purchase_status)) $filter['purchase_status'] = $purchase_status;

		$purchase_type = trim($this->input->post('purchase_type'));
		if (!empty($purchase_type)) $filter['purchase_type'] = $purchase_type;

		if (empty($filter))
		{
			echo json_encode(array('error'=>1,'msg'=>'全部添加之前，至少使用一个筛选条件搜索商品'));
			return;
		}

		$list = $this->depot_model->query_products_all($filter);
		if (empty($list))
		{
			echo json_encode(array('error'=>1,'msg'=>'没有要添加的商品！'));
			return;
		}
		$this->db->query('BEGIN');
		foreach ($list as $item)
		{
			$rs = $this->depot_model->insert_purchase_single($item->sub_id,'1',$purchase_id,$this->admin_id );
			if (empty($rs))
			{
				echo json_encode(array('error'=>1,'msg'=>'添加商品失败，调试信息：sub_id:'.$item->sub_id));
				return;
			} elseif ($rs == -1)
			{
				echo json_encode(array('error'=>1,'msg'=>'要添加的商品已存在采购单','sub_id'=>$item->sub_id));
				return;
			}
		}

		$this->depot_model->update_purchase_total($purchase_id);
		$this->db->query('COMMIT');
		//show
		$purchase_goods = $this->depot_model->purchase_products($purchase_id,$purchase_info->purchase_code);
		$data['goods_list'] = $purchase_goods;
		$data['content'] = $this->load->view('purchase/product_lib', $data, TRUE);
		$data['row_num'] = empty($purchase_goods)?0:1;
		$data['error'] = 0;
		unset($data['goods_list']);
		echo json_encode($data);
		return;
	}

        /**
         *  添加选择商品到采购单
         */
	public function add_product_simple ()
	{
		//add
		auth('purchase_add');
		$my_post = $this->input->post();
		$purchase_id = trim($this->input->post('purchase_id'));
                $purchase_type = trim($this->input->post('purchase_type') );
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'采购单不存在'));
		}
                
		$this->db->query('BEGIN');
		foreach ($my_post as $key => $value)
		{
			if(strlen($key) > 6 && substr($key,0,6) == "check_")
			{
				$sub_id = substr($key,6);
				if ($sub_id > 0 && $value > 0 )
				{
					$rs = $this->depot_model->insert_purchase_single($sub_id,$value,$purchase_id,$this->admin_id );
					if (empty($rs))
					{
						echo json_encode(array('error'=>1,'msg'=>'添加商品失败，调试信息：sub_id:'.$sub_id.",num:".$value));
						return;
					} elseif ($rs == -1)
					{
						echo json_encode(array('error'=>1,'msg'=>'要添加的商品已存在采购单','sub_id'=>$sub_id));
						return;
					}
				} else
				{
					echo json_encode(array('error'=>1,'msg'=>'添加商品失败，调试信息：sub_id:'.$sub_id.",num:".$value));
					return;
				}
			}
		}
		$this->depot_model->update_purchase_total($purchase_id);
		$this->db->query('COMMIT');
		//show
		$purchase_goods = $this->depot_model->purchase_products($purchase_id,$purchase_info->purchase_code);
		$data['goods_list'] = $purchase_goods;
		$data['content'] = $this->load->view('purchase/product_lib', $data, TRUE);
		$data['row_num'] = empty($purchase_goods)?0:1;
		$data['error'] = 0;
		unset($data['goods_list']);
		echo json_encode($data);
		return;
	}

	public function del_purchase_product ()
	{
		auth('purchase_add');
		$my_post = $this->input->post();
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'采购单不存在'));
		}
		$this->db->query('BEGIN');
		foreach ($my_post as $key => $value)
		{
			if(strlen($key) > 7 && substr($key,0,7) == "checkp_")
			{
				$sub_id = substr($key,7);
				if ($sub_id > 0 && $value > 0)
				{
					$rs = $this->depot_model->del_purchase_product($sub_id,$purchase_id);
					if (empty($rs))
					{
						echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：purchase_sub_id:'.$sub_id.",purchase_id:".$purchase_id));
						return;
					}
				} else
				{
					echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：purchase_sub_id:'.$sub_id.",purchase_id:".$purchase_id));
					return;
				}
			}
		}
		$this->depot_model->update_purchase_total($purchase_id);
		$this->db->query('COMMIT');
		//show
		$purchase_goods = $this->depot_model->purchase_products($purchase_id,$purchase_info->purchase_code);
		$data['goods_list'] = $purchase_goods;
		$data['content'] = $this->load->view('purchase/product_lib', $data, TRUE);
		$data['row_num'] = empty($purchase_goods)?0:1;
		$data['error'] = 0;
		unset($data['goods_list']);
		echo json_encode($data);
		return;
	}

	public function update_purchase_product ()
	{
		auth('purchase_add');
		$my_post = $this->input->post();
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'采购单不存在'));
		}
		$this->db->query('BEGIN');
		foreach ($my_post as $key => $value)
		{
			if(strlen($key) > 7 && substr($key,0,7) == "checkp_")
			{
				$sub_id = substr($key,7);
				if ($sub_id > 0 && $value > 0)
				{
					$rs = $this->depot_model->update_purchase_product_x($sub_id,$purchase_id,$value);
					//if (empty($rs))
					//{
					//	echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：purchase_sub_id:'.$sub_id.",product_number:".$value));
					//	return;
					//}
				} else
				{
					echo json_encode(array('error'=>1,'msg'=>'删除商品失败，调试信息：purchase_sub_id:'.$sub_id.",product_number:".$value));
					return;
				}
			}
		}
		$this->depot_model->update_purchase_total($purchase_id);
		$this->db->query('COMMIT');
		//show
		$purchase_goods = $this->depot_model->purchase_products($purchase_id,$purchase_info->purchase_code);
		$data['goods_list'] = $purchase_goods;
		$data['content'] = $this->load->view('purchase/product_lib', $data, TRUE);
		$data['row_num'] = empty($purchase_goods)?0:1;
		$data['error'] = 0;
		unset($data['goods_list']);
		echo json_encode($data);
		return;
	}

	public function proc_add ()
	{
		auth('purchase_add');
		$this->load->library('form_validation');
		$this->load->model('purchase_model');
		$this->form_validation->set_rules('purchase_provider', '供应商', 'trim|not_empty');
		$this->form_validation->set_rules('purchase_batch', '批次号', 'trim|not_empty');
		$this->form_validation->set_rules('purchase_order_date', '采购发起时间', 'trim|required');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		
		//
		$batch_id = $this->input->post('purchase_batch');
		$purchase_batch = $this->purchase_model->filter_purchase_batch(array("batch_id"=>$batch_id));
		if(empty($purchase_batch)){
		    sys_msg("采购批次不存在。", 1);
		}
		if($purchase_batch ->is_reckoned ==1){
		    sys_msg("批次已经结算，无法新增采购单。", 1);
		}
		$update = array();
		$update['purchase_provider'] = $this->input->post('purchase_provider');
		$update['batch_id'] = $batch_id ;
		$update['purchase_order_date'] = $this->input->post('purchase_order_date');
		$update['purchase_brand'] = $this->input->post('purchase_brand');
		$update['purchase_brand'] = empty($update['purchase_brand'])?0:$update['purchase_brand'];
		$update['purchase_delivery'] = $this->input->post('purchase_delivery');
		$update['purchase_remark'] = $this->input->post('purchase_remark');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$update['purchase_code'] = $this->depot_model->get_purchase_code();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;
                $this->load->model("provider_model");
                $provider = $this->provider_model->filter(array('provider_id' => $update['purchase_provider']));
                $update['purchase_type'] = $provider->provider_cooperation;

		$purchase_info = $this->depot_model->filter_purchase(array('purchase_code'=>$update['purchase_code']));
		while (1)
		{
			if ( $purchase_info )
			{
				set_time_limit(1);
				$update['purchase_code'] = $this->depot_model->get_purchase_code();
				$purchase_info = $this->depot_model->filter_purchase(array('purchase_code'=>$update['purchase_code']));
			} else
			{
				break;
			}
		}
		$purchase_id = $this->depot_model->insert_purchase($update);
		sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'purchase/edit/'.$purchase_id)));
	}

	public function proc_edit ()
	{
		auth('purchase_add');
		$purchase_id = intval($this->input->post('purchase_id'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('purchase_provider', '供应商', 'trim|not_empty');
		$this->form_validation->set_rules('purchase_batch', '批次号', 'trim|not_empty');
		$this->form_validation->set_rules('purchase_order_date', '采购发起时间', 'trim|required');

		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		$update['purchase_provider'] = $this->input->post('purchase_provider');
		$update['batch_id'] = $this->input->post('purchase_batch');
		$update['purchase_order_date'] = $this->input->post('purchase_order_date');
		$update['purchase_brand'] = $this->input->post('purchase_brand');
		$update['purchase_brand'] = empty($update['purchase_brand'])?0:$update['purchase_brand'];
		$update['purchase_delivery'] = $this->input->post('purchase_delivery');
		$update['purchase_remark'] = $this->input->post('purchase_remark');
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
                if ($update['purchase_provider'] && !empty($update['purchase_provider'] )){
                    $this->load->model("provider_model");
                    $provider = $this->provider_model->filter(array('provider_id' => $update['purchase_provider']));
                    $update['purchase_type'] = $provider->provider_cooperation;
                }
                
		$this->depot_model->update_purchase($update, $purchase_id);
		sys_msg('操作成功！');
	}

	public function check ($purchase_id)
	{
		auth('purchase_audit');
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($purchase_info->purchase_check_admin > 0 || $purchase_info->purchase_break == 1 || empty($purchase_info->lock_admin) || $purchase_info->lock_admin != $this->admin_id)
		{
			sys_msg('该采购单不可审核！', 1);
		}

		$update = array();
		$update['purchase_check_date'] = date('Y-m-d H:i:s');
		$update['purchase_check_admin'] = $this->admin_id;
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->depot_model->update_purchase($update, $purchase_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'purchase/index')));
	}

	public function unlock ($purchase_id)
	{
		//auth(array('purchase_add','purchase_del','purchase_stop','purchase_audit'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($purchase_info->lock_admin != $this->admin_id)
		{
			sys_msg('该采购单不可解锁！', 1);
		}

		$update = array();
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->depot_model->update_purchase($update, $purchase_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'purchase/index')));
	}

	public function lock ($purchase_id)
	{
		auth(array('purchase_add','purchase_del','purchase_stop','purchase_audit'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($purchase_info->purchase_check_admin > 0 || $purchase_info->purchase_break == 1  || $purchase_info->purchase_finished == 1 || $purchase_info->lock_admin > 0)
		{
			sys_msg('该采购单不可锁定！', 1);
		}

		$update = array();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;
		$this->depot_model->update_purchase($update, $purchase_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'purchase/index')));
	}

	public function breaks ($purchase_id)
	{
		auth('purchase_stop');
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			sys_msg('记录不存在！', 1);
		}

		if ($purchase_info->purchase_check_admin == 0 || $purchase_info->purchase_break == 1  || $purchase_info->purchase_finished == 1 || empty($purchase_info->lock_admin) || $purchase_info->lock_admin != $this->admin_id)
		{
			sys_msg('该采购单不可终止！', 1);
		}

		$update = array();
		$update['purchase_break'] = 1;
		$update['purchase_break_date'] = date('Y-m-d H:i:s');
		$update['purchase_break_admin'] = $this->admin_id;
		$this->depot_model->update_purchase($update, $purchase_id);
		sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'purchase/index')));
	}

	public function delete ($purchase_id)
	{
		auth('purchase_del');
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			sys_msg('记录不存在！', 1);
		}
		if ($purchase_info->purchase_check_admin > 0 || empty($purchase_info->lock_admin) || $purchase_info->lock_admin != $this->admin_id)
		{
			sys_msg('该采购单不可删除！', 1);
		}
		if ($this->depot_model->delete_purchase($purchase_id) == 1)
		{
			$this->depot_model->delete_purchase_product(array('purchase_id'=>$purchase_id));
			sys_msg('操作成功！',0 , array(array('text'=>'返回', 'href'=>'purchase/index')));
		} else
		{
			sys_msg('删除失败！',1);
		}
	}

	public function check_delete ()
	{
		auth('purchase_del');
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($purchase_info->purchase_check_admin > 0 || empty($purchase_info->lock_admin) || $purchase_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该采购单不可删除'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_delete ()
	{
		auth('purchase_del');
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($purchase_info->purchase_check_admin > 0 || empty($purchase_info->lock_admin) || $purchase_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该采购单不可删除'));
			return;
		}
		$this->db->query('BEGIN');
		if ($this->depot_model->delete_purchase($purchase_id))
		{
			$this->depot_model->delete_purchase_product(array('purchase_id'=>$purchase_id));
			//代销采购-删除采购单时同时删除对应代销采购记录 purchase_consign . details
			if($purchase_info->purchase_type == 2){
			    $this->load->model('purchase_consign_model');
			    $del_con_where = array();
			    $del_con_where["purchase_code"] = $purchase_info->purchase_code;
			    $del_con_where["provider_id"] = $purchase_info->purchase_provider;
			    $del_con_where["batch_id"] = $purchase_info->batch_id;
			    $del_con_where["brand_id"] = $purchase_info->purchase_brand;
			    $this->purchase_consign_model->delete_purchase_consign($del_con_where);
			    $this->purchase_consign_model->delete_purchase_consign_detail($del_con_where);
			}
			$this->db->query('COMMIT');
			$filter = array();

			$purchase_code = trim($this->input->post('purchase_code'));
			if (!empty($purchase_code)) $filter['purchase_code'] = $purchase_code;

			$purchase_provider = trim($this->input->post('purchase_provider'));
			if (!empty($purchase_provider)) $filter['purchase_provider'] = $purchase_provider;

			$purchase_type = trim($this->input->post('purchase_type'));
			if (!empty($purchase_type)) $filter['purchase_type'] = $purchase_type;

			$purchase_status = trim($this->input->post('purchase_status'));
			if (!empty($purchase_status)) $filter['purchase_status'] = $purchase_status;

			$provider_goods = trim($this->input->post('provider_goods'));
			if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

			$purchase_batch = trim($this->input->post('purchase_batch'));
			if (!empty($purchase_batch)) $filter['purchase_batch'] = $purchase_batch;
		
			$filter = get_pager_param($filter);
			$data = $this->depot_model->purchase_list($filter);
			$data['my_id'] = $this->admin_id;
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('purchase/purchase_list', $data, TRUE);
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
		auth(array('purchase_add','purchase_del','purchase_stop','purchase_audit'));
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($purchase_info->purchase_break == 1  || $purchase_info->purchase_finished == 1 || $purchase_info->lock_admin > 0)
		{
			echo json_encode(array('error'=>1,'msg'=>'该采购单不可锁定'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_lock ()
	{
		auth(array('purchase_add','purchase_del','purchase_stop','purchase_audit'));
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($purchase_info->purchase_break == 1  || $purchase_info->purchase_finished == 1 || $purchase_info->lock_admin > 0)
		{
			echo json_encode(array('error'=>1,'msg'=>'该采购单不可锁定'));
			return;
		}
		$update = array();
		$update['lock_date'] = date('Y-m-d H:i:s');
		$update['lock_admin'] = $this->admin_id;
		$this->depot_model->update_purchase($update, $purchase_id);

		$filter = array();
		$purchase_code = trim($this->input->post('purchase_code'));
		if (!empty($purchase_code)) $filter['purchase_code'] = $purchase_code;

		$purchase_provider = trim($this->input->post('purchase_provider'));
		if (!empty($purchase_provider)) $filter['purchase_provider'] = $purchase_provider;

		$purchase_type = trim($this->input->post('purchase_type'));
		if (!empty($purchase_type)) $filter['purchase_type'] = $purchase_type;

		$purchase_status = trim($this->input->post('purchase_status'));
		if (!empty($purchase_status)) $filter['purchase_status'] = $purchase_status;

		$provider_goods = trim($this->input->post('provider_goods'));
		if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

		$purchase_batch = trim($this->input->post('purchase_batch'));
		if (!empty($purchase_batch)) $filter['purchase_batch'] = $purchase_batch;
		
		$filter = get_pager_param($filter);
		$data = $this->depot_model->purchase_list($filter);
		$data['full_page'] = FALSE;
		$data['my_id'] = $this->admin_id;
		$data['content'] = $this->load->view('purchase/purchase_list', $data, TRUE);
		$data['error'] = 0;
		unset($data['list']);
		echo json_encode($data);
		return;
	}

	public function check_unlock ()
	{
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($purchase_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该采购单不可解锁'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_unlock ()
	{
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($purchase_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该采购单不可解锁'));
			return;
		}
		$update = array();
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->depot_model->update_purchase($update, $purchase_id);

		$filter = array();
		$purchase_code = trim($this->input->post('purchase_code'));
		if (!empty($purchase_code)) $filter['purchase_code'] = $purchase_code;

		$purchase_provider = trim($this->input->post('purchase_provider'));
		if (!empty($purchase_provider)) $filter['purchase_provider'] = $purchase_provider;

		$purchase_type = trim($this->input->post('purchase_type'));
		if (!empty($purchase_type)) $filter['purchase_type'] = $purchase_type;

		$purchase_status = trim($this->input->post('purchase_status'));
		if (!empty($purchase_status)) $filter['purchase_status'] = $purchase_status;

		$provider_goods = trim($this->input->post('provider_goods'));
		if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

		$purchase_batch = trim($this->input->post('purchase_batch'));
		if (!empty($purchase_batch)) $filter['purchase_batch'] = $purchase_batch;
		
		$filter = get_pager_param($filter);
		$data = $this->depot_model->purchase_list($filter);
		$data['full_page'] = FALSE;
		$data['my_id'] = $this->admin_id;
		$data['content'] = $this->load->view('purchase/purchase_list', $data, TRUE);
		$data['error'] = 0;
		unset($data['list']);
		echo json_encode($data);
		return;
	}

        /**
         * 检查采购单是否可审核
         * @return type 
         */
	public function check_check ()
	{
		auth('purchase_audit');
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($purchase_info->purchase_check_admin > 0 || $purchase_info->purchase_break == 1 || empty($purchase_info->lock_admin) || $purchase_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该采购单不可审核'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

        /**
         * 审核采购单
         * @return type 
         */
	public function proc_check ()
	{
		auth('purchase_audit');
                $this->load->model("product_model");
                $this->load->model("purchase_model");
                
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($purchase_info->purchase_check_admin > 0 || $purchase_info->purchase_break == 1 || empty($purchase_info->lock_admin) || $purchase_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该采购单不可审核'));
			return;
		}
                
                $purchase_sub_info = $this->depot_model->filter_purchase_sub(array('purchase_id' => $purchase_id));
                if( empty($purchase_sub_info) ){
                    echo json_encode(array('error'=>1,'msg'=>'该采购单还未添加商品'));
                    return;
                }
                
                //判断采购单商品是否有商品成本价
                $product_ids = $this->purchase_model->get_purchase_pro_ids( $purchase_info->batch_id );
                
                //判断采购单商品是否通过审核
                $audit_num = $this->product_model->is_no_audit_pros($product_ids );
                if( !$audit_num == 0){
                    echo json_encode(array('error'=>1,'msg'=>'该采购单中存在未审核商品，请先审核'));
                    return;
                }
                
                $pro_ids = array();
                foreach ($product_ids as $key => $value) {
                    if ($value['product_id'] > 0)
                        $pro_ids[] = $value['product_id'] ;
                }
                $purchase_cost_exit = $this->purchase_model->is_purchase_cost_exit ($purchase_info->batch_id , $pro_ids );
                if( intval($purchase_cost_exit) !== 0  ){
                    echo json_encode(array('error'=>1,'msg'=>'该采购单中商品都必须有成本价'));
                    return;
                }
                
		$update = array();
		$update['purchase_check_date'] = date('Y-m-d H:i:s');
		$update['purchase_check_admin'] = $this->admin_id;
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->depot_model->update_purchase($update, $purchase_id);

		$filter = array();
		$purchase_code = trim($this->input->post('purchase_code'));
		if (!empty($purchase_code)) $filter['purchase_code'] = $purchase_code;

		$purchase_provider = trim($this->input->post('purchase_provider'));
		if (!empty($purchase_provider)) $filter['purchase_provider'] = $purchase_provider;

		$purchase_type = trim($this->input->post('purchase_type'));
		if (!empty($purchase_type)) $filter['purchase_type'] = $purchase_type;

		$purchase_status = trim($this->input->post('purchase_status'));
		if (!empty($purchase_status)) $filter['purchase_status'] = $purchase_status;

		$provider_goods = trim($this->input->post('provider_goods'));
		if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

		$purchase_batch = trim($this->input->post('purchase_batch'));
		if (!empty($purchase_batch)) $filter['purchase_batch'] = $purchase_batch;
		
		$filter = get_pager_param($filter);
		$data = $this->depot_model->purchase_list($filter);
		$data['full_page'] = FALSE;
		$data['my_id'] = $this->admin_id;
		$data['content'] = $this->load->view('purchase/purchase_list', $data, TRUE);
		$data['error'] = 0;
		unset($data['list']);
		echo json_encode($data);
		return;
	}

	public function check_break ()
	{
		auth('purchase_stop');
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($purchase_info->purchase_check_admin == 0 || $purchase_info->purchase_break == 1  || $purchase_info->purchase_finished == 1 || empty($purchase_info->lock_admin) || $purchase_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该采购单不可终止'));
			return;
		}
		echo json_encode(array('error'=>0));
	}

	public function proc_break ()
	{
		auth('purchase_stop');
		$purchase_id = trim($this->input->post('purchase_id'));
		$purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
		if ( empty($purchase_info) )
		{
			echo json_encode(array('error'=>1,'msg'=>'记录不存在'));
			return;
		}
		if ($purchase_info->purchase_check_admin == 0 || $purchase_info->purchase_break == 1  || $purchase_info->purchase_finished == 1 || empty($purchase_info->lock_admin) || $purchase_info->lock_admin != $this->admin_id)
		{
			echo json_encode(array('error'=>1,'msg'=>'该采购单不可终止'));
			return;
		}

//		if (!$this->depot_model->check_break_purchase($purchase_info->purchase_code))
//		{
//			echo json_encode(array('error'=>1,'msg'=>'该采购单已建立采购入库单，不可终止'));
//			return;
//		}

		$update = array();
		$update['purchase_break'] = 1;
		$update['purchase_break_date'] = date('Y-m-d H:i:s');
		$update['purchase_break_admin'] = $this->admin_id;
		$update['lock_date'] = '0000-00-00 00:00';
		$update['lock_admin'] = 0;
		$this->depot_model->update_purchase($update, $purchase_id);

		$filter = array();
		$purchase_code = trim($this->input->post('purchase_code'));
		if (!empty($purchase_code)) $filter['purchase_code'] = $purchase_code;

		$purchase_provider = trim($this->input->post('purchase_provider'));
		if (!empty($purchase_provider)) $filter['purchase_provider'] = $purchase_provider;

		$purchase_type = trim($this->input->post('purchase_type'));
		if (!empty($purchase_type)) $filter['purchase_type'] = $purchase_type;

		$purchase_status = trim($this->input->post('purchase_status'));
		if (!empty($purchase_status)) $filter['purchase_status'] = $purchase_status;

		$provider_goods = trim($this->input->post('provider_goods'));
		if (!empty($provider_goods)) $filter['provider_goods'] = $provider_goods;

		$purchase_batch = trim($this->input->post('purchase_batch'));
		if (!empty($purchase_batch)) $filter['purchase_batch'] = $purchase_batch;
		
		$filter = get_pager_param($filter);
		$data = $this->depot_model->purchase_list($filter);
		$data['full_page'] = FALSE;
		$data['my_id'] = $this->admin_id;
		$data['content'] = $this->load->view('purchase/purchase_list', $data, TRUE);
		$data['error'] = 0;
		unset($data['list']);
		echo json_encode($data);
		return;
	}
        
        public function export($purchase_id = 0){
            $this->load->model('provider_model');
            $purchase_info = $this->depot_model->filter_purchase(array('purchase_id' => $purchase_id));
            if ( empty($purchase_info) )
            {
                    sys_msg('记录不存在！', 1);
            }
            $this->load->vars('purchase_info', $purchase_info);


            $purchase_goods = $this->depot_model->purchase_products($purchase_id,$purchase_info->purchase_code);
            $data['goods_list'] = $purchase_goods;
            $provider = $this->provider_model->filter(array('provider_id' => $purchase_info->purchase_provider));
            $data['provider'] = $provider;
            $data['realname'] = $this->session->userdata('realname');
            $data['tag'] = '?';
            $this->load->view('purchase/purchase_export', $data);
            $file_name = "purchase-".$purchase_info->purchase_code.".xls";
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=".$file_name);               
        }
        //导入产品修改过期日期
        public function product_import(){
            $this->load->model('product_model');
            $this->load->model("purchase_model");
            $data = array();
            if (isset($_FILES['data_file'])){
                if (empty($_FILES['data_file']['tmp_name'])) sys_msg("请上传文件");
                if($_FILES['data_file']['type'] != 'text/xml') {
			sys_msg("请上传XML格式的文件", 1);
		}
                $content = file_get_contents($_FILES['data_file']['tmp_name']);
		$content = preg_replace('/&.*;/','',$content);
		$dom = new SimpleXMLElement($content);
		$dom->registerXPathNamespace('c', 'urn:schemas-microsoft-com:office:spreadsheet');
		$rows = $dom->xpath('//c:Workbook//c:Worksheet//c:Table//c:Row');
                $index = 1;
                foreach($rows as $row){
                    $row_cache = array();
                    $tmp_arr = array();
                    foreach($row as $cell) {
                        $row_cache[] = strval($cell->Data);
                    }
                    switch($index){
                        case 1:
                            $purchase_sn = trim($row_cache[1]);
                            if(empty($purchase_sn)) sys_msg('采购单号不能为空',1);
                            $purchase_info = $this->depot_model->filter_purchase(array('purchase_code' => addslashes($purchase_sn)));
                            if(empty($purchase_info)) sys_msg('采购单不存在',1);
                            if ($purchase_info->purchase_shelved_number > 0) sys_msg('采购单商品已上架，不能导入',1);
                            $data['purchase'] = $purchase_info;
                            break;
                        case 2:
                            break;
                        default:
                            if(empty($row_cache)) break;
                            if(empty($row_cache[0]) || empty($row_cache[1])) {
                                break;
                            }
                            $product = $this->product_model->get_product_ids(array(trim($row_cache[0])));
                            $tmp_arr['product_sn'] = trim($row_cache[0]);
                            $tmp_arr['exdate'] = date("Y-m-d", strtotime(trim($row_cache[1])));                        
                            if (empty($product)) {
                                $tmp_arr['err_msg'] = '商品不存在';
                            } else {

                                $purchase_sub = $this->purchase_model->get_purchase_sub($data['purchase']->purchase_id, $product[0]['product_id']);
                                if (empty($purchase_sub)){
                                    $tmp_arr['err_msg'] = '商品不存在于采购单';
                                } else {
                                    $tmp_arr['purchase_sub_id'] = $purchase_sub->purchase_sub_id;
                                }
                            }
                            $data['sub'][] = $tmp_arr;

                    }
                    $index += 1;
                }
                if (empty($data['sub'])) sys_msg('您导入的文件中，没有商品信息',1);
            }
            
            $this->load->view('purchase/product_import', $data);
        }
        //导入产品修改过期日期处理
        public function product_import_proc(){
            $this->load->model("purchase_model");
            $purchase_code = trim($this->input->post('purchase_code'));
            $sub_arr = $this->input->post('purchase_sub_id');
            $date_arr = $this->input->post('exdate');
            if (empty($purchase_code) || empty($sub_arr) || empty($date_arr)){
                sys_msg("参数错误");
            }
            $purchase_info = $this->depot_model->filter_purchase(array('purchase_code' => addslashes($purchase_code)));
            if(empty($purchase_info)) sys_msg('采购单不存在',1);
            if ($purchase_info->purchase_shelved_number > 0) sys_msg('采购单商品已上架，不能导入',1);
            foreach ($sub_arr as $k => $id){
                if (intval($id) <= 0 || empty($date_arr[$k])){
                    continue;
                }
                $this->purchase_model->update_purchase_sub($purchase_info->purchase_id, $id, array('expire_date' => $date_arr[$k]));
            }
            sys_msg('操作成功！',0,array(array('text'=>'返回', 'href'=>'purchase/product_import')));
        }



}
###
