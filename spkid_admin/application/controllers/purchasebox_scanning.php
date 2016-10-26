<?php
#doc
#	classname:	Purchasebox_scanning
#	scope:		PUBLIC
#
#/doc

class Purchasebox_scanning extends CI_Controller
{
	public function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if ( ! $this->admin_id )
		{
			redirect('rf/login');
		}
		$this->load->model('purchasebox_scanning_model');
		$this->load->model('depotio_model');
		$this->load->model('depot_model');
	}

	public function index ()
	{

		auth('purchasebox_scanning');
		$data = array();
		$data['is_finished'] = '-1';
		$data['cur_menu']='in';
		$this->load->view('depot/purchasebox_scanning', $data);
	}
	
	public function add ()
	{
		auth('purchasebox_scanning');

		$box_code = $this->input->post('box_code');
		$goods_code = $this->input->post('goods_code');
		$goods_num = $this->input->post('goods_num');
		$depot_code = $this->input->post('depot_code');
		
		//POST数量验证
		if (empty($box_code)) sys_msg('箱号不能为空!', 1);
		if (empty($goods_code)) sys_msg('商品条码不能为空!', 1);
		if (empty($goods_num)) sys_msg('商品数量不能为空!', 1);
		if (empty($depot_code)) sys_msg('储位不能为空!', 1);
		
		//检测箱号
		$purchasebox_info = $this->purchasebox_scanning_model->filter_purchase_box(array('box_code'=>$box_code));
		if (empty($purchasebox_info))
		{
			sys_msg('箱号不存在！', 1);
		}
		//检测箱中商品是否已全部上架
		if ($purchasebox_info->product_shelve_num == $purchasebox_info->product_number) {
			sys_msg('箱子所有商品已经上架', 1);
		}
		//检测储位
		$location_info = $this->purchasebox_scanning_model->get_location_depot(array('location_name'=>$depot_code));
		if (empty($location_info)) {
			sys_msg('储位不存在！', 1);
		}
		//检测商品条码
		$product_info = $this->purchasebox_scanning_model->filter_purchasebox_sub(array('box_id'=>$purchasebox_info->box_id,'provider_barcode'=>$goods_code));
		if (empty($product_info)) {
			sys_msg('上架的商品不属于对应箱号！', 1);
		}
                //检测仓库属性
                $this->checkCooperation($product_info->product_id, $location_info->depot_id);
		//检测商品数量
		$require_num = intval($product_info->product_number) - intval($product_info->over_num);
		if ($goods_num > $require_num) {
			sys_msg('超出数量，此款商品只剩下 '.$require_num.' 件未上架', 1);
		}
		$this->db->query('BEGIN');
		$depot_in_info = $this->depotio_model->filter_depot_in(array('order_id'=>$purchasebox_info->box_id,'depot_in_type'=>11,'in_type'=>2,'audit_admin'=>0));
		
		//如没有入库单，则新建入库单
		if (empty($depot_in_info))
		{
			//新建入库单
			$update = array();
			$update['depot_in_type'] = 11;//入库类型
			$update['depot_in_date'] = date('Y-m-d H:i:s');//入库时间
			$update['depot_depot_id'] = $location_info->depot_id;//储位编号
			$update['order_sn'] = $purchasebox_info->purchase_code;//采购单号
			$update['order_id'] = $purchasebox_info->box_id;//收货箱编号
			$update['depot_in_reason'] = '扫描入库';//入库描述
			$update['create_date'] = date('Y-m-d H:i:s');
			$update['create_admin'] = '-1';
			$update['depot_in_code'] = $this->depotio_model->get_depot_in_code();
			$update['lock_date'] = date('Y-m-d H:i:s');
			$update['lock_admin'] = '-1';
			$update['in_type'] = 2;
			$check_depot_in = $this->depotio_model->filter_depot_in(array('depot_in_code'=>$update['depot_in_code']));
			while (1)
			{
				if ( $check_depot_in )
				{
					set_time_limit(1);
					$update['depot_in_code'] = $this->depotio_model->get_depot_in_code();
					$check_depot_in = $this->depotio_model->filter_depot_in(array('depot_in_code'=>$update['depot_in_code']));
				} else
				{
					break;
				}
			}
			$depot_in_id = $this->depotio_model->insert_depot_in($update);
		} else {//已有入库单
			//检测是仓库是否一致
			if ($location_info->depot_id != $depot_in_info->depot_depot_id) {
				$depot_info = $this->purchasebox_scanning_model->get_depot_info(array('depot_id'=>$depot_in_info->depot_depot_id));
				sys_msg('上架所在仓是：'.$depot_info->depot_name.',请在同一仓位上架', 1);
			}
			$depot_in_id = $depot_in_info->depot_in_id;
		}
		
		//添加或更新子表商品信息
		$depot_in_sub_id = $this->purchasebox_scanning_model->add_depot_in_product($depot_in_id,$location_info->depot_id,$goods_num,$location_info->location_id,$this->admin_id,$purchasebox_info->box_id,$product_info->product_id,$product_info->color_id,$product_info->size_id);
		
		//更新入库主表总数量，总价格
		$this->depotio_model->update_depot_in_total($depot_in_id);
		
		//更新收获箱子表上架信息
		$over_num = intval($goods_num) + intval($product_info->over_num);
		$this->purchasebox_scanning_model->update_purchasebox_sub($product_info->box_sub_id,$over_num,$this->admin_id);
		
		//更新收货箱主表上架信息
		$this->purchasebox_scanning_model->update_purchasebox($purchasebox_info->box_id,$this->admin_id);
		
		//更新采购单主表上架信息
		$this->purchasebox_scanning_model->update_purchase($purchasebox_info->purchase_code);
			
		//添加入库单明细记录
		$this->purchasebox_scanning_model->insert_transaction_info($depot_in_sub_id,$goods_num,$this->admin_id);
		
		//检测箱子物品是否全部上架
		$data = array();
		$purchasebox_main_info = $this->purchasebox_scanning_model->filter_purchase_box(array('box_id'=>$purchasebox_info->box_id));
		$data['product_number'] = $purchasebox_main_info->product_number;
		$data['shelve_num'] = intval($purchasebox_main_info->product_shelve_num);
		if ($purchasebox_main_info->product_number == $purchasebox_main_info->product_shelve_num) {
			//已全部上架（更新可售库存数量、入库单为审核状态、入库明细表为已入库状态）
			if (empty($depot_in_info)) {
				$depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
			}
			
			//判断仓库是否启用
			$depot_info2 = $this->purchasebox_scanning_model->get_depot_info(array('depot_id'=>$depot_in_info->depot_depot_id));
			if ($depot_info2->is_use == 1) {
				//更新可售库存数量
				$this->depot_model->update_gl_num_in($depot_in_info->depot_in_code);
			}
			
			//入库单为审核状态
			$update2 = array();
			$update2['audit_date'] = date('Y-m-d H:i:s');
			$update2['audit_admin'] = '-1';
			$update2['lock_date'] = '0000-00-00 00:00';
			$update2['lock_admin'] = 0;
			$this->depotio_model->update_depot_in($update2, $depot_in_id);
			
			//入库明细表为已入库状态
			$this->depot_model->update_transaction(array('trans_status'=>TRANS_STAT_IN,'update_admin'=>$this->admin_id,'update_date'=>date('Y-m-d H:i:s')), array('trans_status'=>TRANS_STAT_AWAIT_IN,'trans_sn'=>$depot_in_info->depot_in_code));
			
			$this->db->query('COMMIT');
			
			
			$data['is_finished'] = 1;
			$this->load->view('depot/purchasebox_scanning', $data);
			
		} else {
			//未全部上架
			$this->db->query('COMMIT');
			
			$data['is_finished'] = 0;
			$data['box_code'] = $box_code;
			$data['unshelve_num'] = intval($purchasebox_main_info->product_number) - intval($purchasebox_main_info->product_shelve_num);
			$this->load->view('depot/purchasebox_scanning', $data);
		}
		
		
	}
	
	/**
	 * 支持多SKU扫描上架
	 */
	public function muti(){
	    auth('purchasebox_scanning');
	    $data = array();
	    $data['is_finished'] = '-1';
	    $data['cur_menu']='in';
	    $this->load->view('depot/purchasebox_scanning_muti', $data);
	}
	
	/**
	 * 支持多SKU扫描上架，逻辑和单SKU一致
	 */
	public function mu_add(){
		auth('purchasebox_scanning');

		$box_code = $this->input->post('box_code');
		$goods_code = $this->input->post('goods_code');
//		$goods_num = $this->input->post('goods_num');
		$depot_code = $this->input->post('depot_code');
		
		$goods_array = explode(",",$goods_code);
		//POST数量验证
		if (empty($box_code)) sys_msg('箱号不能为空!', 1);
		if (empty($goods_code) || empty($goods_array)) sys_msg('商品不能为空!', 1);
//		if (empty($goods_num)) sys_msg('商品数量不能为空!', 1);
		if (empty($depot_code)) sys_msg('储位不能为空!', 1);
		
		//检测箱号
		$purchasebox_info = $this->purchasebox_scanning_model->filter_purchase_box(array('box_code'=>$box_code));
		if (empty($purchasebox_info))
		{
			sys_msg('箱号不存在！', 1);
		}
		//检测箱中商品是否已全部上架
		if ($purchasebox_info->product_shelve_num == $purchasebox_info->product_number) {
			sys_msg('箱子所有商品已经上架', 1);
		}
		//检测储位
		$location_info = $this->purchasebox_scanning_model->get_location_depot(array('location_name'=>$depot_code));
		if (empty($location_info)) {
			sys_msg('储位不存在！', 1);
		}
		$finish = FALSE;
		$this->db->query('BEGIN');
		foreach ($goods_array as $goods){
		    $goods_item = explode(":",$goods);
		    if(empty($goods_item))
			continue;
		    $goods_code = $goods_item[0];
		    $goods_num = $goods_item[1];
		
		    //检测商品条码
		    $product_info = $this->purchasebox_scanning_model->filter_purchasebox_sub(array('box_id'=>$purchasebox_info->box_id,'provider_barcode'=>$goods_code));
		    if (empty($product_info)) {
			    sys_msg('上架的商品['.$goods_code.']不属于对应箱号！', 1);
		    }
                    //检测仓库属性
                    $this->checkCooperation($product_info->product_id, $location_info->depot_id);
		    //检测商品数量
		    $require_num = intval($product_info->product_number) - intval($product_info->over_num);
		    if ($goods_num > $require_num) {
			    sys_msg('超出数量，此款商品['.$goods_code.']只剩下 '.$require_num.' 件未上架', 1);
		    }

		    $depot_in_info = $this->depotio_model->filter_depot_in(array('order_id'=>$purchasebox_info->box_id,'depot_in_type'=>11,'in_type'=>2,'audit_admin'=>0));

		    //如没有入库单，则新建入库单
		    if (empty($depot_in_info))
		    {
			    //新建入库单
			    $update = array();
			    $update['depot_in_type'] = 11;//入库类型
			    $update['depot_in_date'] = date('Y-m-d H:i:s');//入库时间
			    $update['depot_depot_id'] = $location_info->depot_id;//储位编号
			    $update['order_sn'] = $purchasebox_info->purchase_code;//采购单号
			    $update['order_id'] = $purchasebox_info->box_id;//收货箱编号
			    $update['depot_in_reason'] = '扫描入库';//入库描述
			    $update['create_date'] = date('Y-m-d H:i:s');
			    $update['create_admin'] = '-1';
			    $update['depot_in_code'] = $this->depotio_model->get_depot_in_code();
			    $update['lock_date'] = date('Y-m-d H:i:s');
			    $update['lock_admin'] = '-1';
			    $update['in_type'] = 2;
			    $check_depot_in = $this->depotio_model->filter_depot_in(array('depot_in_code'=>$update['depot_in_code']));
			    while (1)
			    {
				    if ( $check_depot_in )
				    {
					    set_time_limit(1);
					    $update['depot_in_code'] = $this->depotio_model->get_depot_in_code();
					    $check_depot_in = $this->depotio_model->filter_depot_in(array('depot_in_code'=>$update['depot_in_code']));
				    } else
				    {
					    break;
				    }
			    }
			    $depot_in_id = $this->depotio_model->insert_depot_in($update);
		    } else {//已有入库单
			    //检测是仓库是否一致
			    if ($location_info->depot_id != $depot_in_info->depot_depot_id) {
				    $depot_info = $this->purchasebox_scanning_model->get_depot_info(array('depot_id'=>$depot_in_info->depot_depot_id));
				    sys_msg('上架所在仓是：'.$depot_info->depot_name.',请在同一仓位上架', 1);
			    }
			    $depot_in_id = $depot_in_info->depot_in_id;
		    }

		    //添加或更新子表商品信息
		    $depot_in_sub_id = $this->purchasebox_scanning_model->add_depot_in_product($depot_in_id,$location_info->depot_id,$goods_num,$location_info->location_id,$this->admin_id,$purchasebox_info->box_id,$product_info->product_id,$product_info->color_id,$product_info->size_id);

		    //更新入库主表总数量，总价格
		    $this->depotio_model->update_depot_in_total($depot_in_id);

		    //更新收获箱子表上架信息
		    $over_num = intval($goods_num) + intval($product_info->over_num);
		    $this->purchasebox_scanning_model->update_purchasebox_sub($product_info->box_sub_id,$over_num,$this->admin_id);

		    //更新收货箱主表上架信息
		    $this->purchasebox_scanning_model->update_purchasebox($purchasebox_info->box_id,$this->admin_id);

		    //更新采购单主表上架信息
		    $this->purchasebox_scanning_model->update_purchase($purchasebox_info->purchase_code);

		    //添加入库单明细记录
		    $this->purchasebox_scanning_model->insert_transaction_info($depot_in_sub_id,$goods_num,$this->admin_id);

		    //检测箱子物品是否全部上架
		    $data = array();
		    $purchasebox_main_info = $this->purchasebox_scanning_model->filter_purchase_box(array('box_id'=>$purchasebox_info->box_id));
		    $data['product_number'] = $purchasebox_main_info->product_number;
		    $data['shelve_num'] = intval($purchasebox_main_info->product_shelve_num);
		    if ($purchasebox_main_info->product_number == $purchasebox_main_info->product_shelve_num) {
			    //已全部上架（更新可售库存数量、入库单为审核状态、入库明细表为已入库状态）
			    if (empty($depot_in_info)) {
				    $depot_in_info = $this->depotio_model->filter_depot_in(array('depot_in_id' => $depot_in_id));
			    }

			    //判断仓库是否启用
			    $depot_info2 = $this->purchasebox_scanning_model->get_depot_info(array('depot_id'=>$depot_in_info->depot_depot_id));
			    if ($depot_info2->is_use == 1) {
				    //更新可售库存数量
				    $this->depot_model->update_gl_num_in($depot_in_info->depot_in_code);
			    }

			    //入库单为审核状态
			    $update2 = array();
			    $update2['audit_date'] = date('Y-m-d H:i:s');
			    $update2['audit_admin'] = '-1';
			    $update2['lock_date'] = '0000-00-00 00:00';
			    $update2['lock_admin'] = 0;
			    $this->depotio_model->update_depot_in($update2, $depot_in_id);

			    //入库明细表为已入库状态
			    $this->depot_model->update_transaction(array('trans_status'=>TRANS_STAT_IN,'update_admin'=>$this->admin_id,'update_date'=>date('Y-m-d H:i:s')), array('trans_status'=>TRANS_STAT_AWAIT_IN,'trans_sn'=>$depot_in_info->depot_in_code));
			   $finish =TRUE;
		    }
		}
		$this->db->query('COMMIT');
		$data['is_finished'] = $finish ?1:0;
		if(!$finish){
		    $data['box_code'] = $box_code;
		    $data['unshelve_num'] = intval($purchasebox_main_info->product_number) - intval($purchasebox_main_info->product_shelve_num);
		}
		$this->load->view('depot/purchasebox_scanning_muti', $data);
		
	}
        
        /**
         * 检测仓库属性
         */
        private function checkCooperation($product_id, $depot_id) {
                $cooperation = $this->purchasebox_scanning_model->get_cooperation_by_product_id($product_id);
                $depot = $this->depot_model->filter_depot(array('depot_id'=>$depot_id));
                if(!empty($cooperation) && !empty($depot)) {
                        if($cooperation->provider_cooperation != $depot->cooperation_id) {
                                sys_msg('商品合作方式与仓库属性不一致', 1);
                        }
                }
        }


}
###
