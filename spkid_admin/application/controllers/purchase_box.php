<?php
#doc
#	收货箱列表及扫描收货
#	@author:sean
#   @date:2012-2-18
#/doc
class Purchase_box extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if ( ! $this->admin_id )
		{
			redirect('index/login');
		}
        $this->load->model('purchase_box_model');
    }

    /**
     * 收货箱列表
     */
    public function index($param_purchase_code='')
    {
        auth('purchase_box_view');
		$filter = $this->uri->uri_to_assoc(3);
		$purchase_code= trim($this->input->post('purchase_code'));
        if(empty($purchase_code)) $purchase_code=$param_purchase_code;
		$product_sn= trim($this->input->post('product_sn'));
		$start_time= trim($this->input->post('start_time'));
		$end_time= trim($this->input->post('end_time'));
		$user_name= trim($this->input->post('user_name'));

		if (!empty($purchase_code)) $filter['purchase_code'] = $purchase_code;
		if (!empty($product_sn)) $filter['product_sn'] = $product_sn;
		if (!empty($start_time)) $filter['start_time'] = $start_time;
		if (!empty($end_time)) $filter['end_time'] = $end_time;
		if (!empty($user_name)) $filter['user_name'] = $user_name;

		$filter = get_pager_param($filter);
		$data = $this->purchase_box_model->purchase_box_list($filter);
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('purchase/purchase_box_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
        $data['purchase_code']=$purchase_code;
        $this->load->view('purchase/purchase_box_list',$data);
    }


    /**
     * 收货扫描
     * param: $purchase_code  采购单id
     */
    public function scan($purchase_id=0)
    {
        auth('purchase_box_scanning');
	$box_code= trim($this->input->get('box_code'));
        $this->load->model('depot_model');
        $purchase=$this->depot_model->filter_purchase(array('purchase_id'=>$purchase_id));
        if(empty($purchase)){
	        sys_msg('记录不存在！', 1);
        }
        //审核后的采购单才可以扫描收货
        if(empty($purchase->purchase_check_admin))
        {
            sys_msg('未审核的采购单不能进行扫描收货',1);
        }
        if($purchase->purchase_finished==1)
        {
            sys_msg('已完成的采购单不能进行扫描收货',1);
        }
	
	$purchase ->provider_name ="";
	if(!empty($purchase -> purchase_provider)){
	    $this->load->model('provider_model');
	    $provider = $this->provider_model->filter(array("provider_id"=>$purchase -> purchase_provider));
	    if(!empty($provider))$purchase ->provider_name = $provider->provider_name;
	}
	$purchase ->brand_name = "";
	if(!empty($purchase -> purchase_brand)){
	    $this->load->model('brand_model');
	    $brand = $this->brand_model->filter(array("brand_id"=>$purchase -> purchase_brand));
	    if(!empty($brand))$purchase ->brand_name = $brand->brand_name;
	}
        $data['purchase']=$purchase;
        //查询该采购单收货箱数量
        $box_count=$this->purchase_box_model->get_box_count($purchase->purchase_code);
	$data['box_code'] ="";
	if(!empty($box_code)){
	    $data['box_code']=$box_code;
	    $box_list = $this->purchase_box_model->filter_purchase_box_main(array("box_code"=>$box_code));
	    if(!empty($box_list)){
		$box_id = $box_list[0]->box_id;
                $data['delivery_date']=$box_list[0]->delivery_date;
		$data['details_list'] = $this->purchase_box_model->get_box_product($box_id);
	    }
	}
        $data['box_count']=$box_count->ct;
        $data['full_page'] = TRUE;
        $this->load->view('purchase/purchase_box_scan',$data);
    }

    /**
     * 扫描条码后查找商品
     */
    public function get_product($purchase_id){
        $this->load->model('product_model');
	$provider_productcode =trim($this->input->post('product_code'));
	$box_code =trim($this->input->post('box_code'));
	if(empty($provider_productcode)){
	    echo '{"result":0,"msg":1}';
            exit;
	}
	$box_id = 0;
	if(!empty($box_code)){
	    $box_list = $this->purchase_box_model->filter_purchase_box_main(array("box_code"=>$box_code));
	    if(!empty($box_list)){
		    $box_id = $box_list[0]->box_id;
	     }
	}
        
        $this->load->model('depot_model');
        $purchase=$this->depot_model->filter_purchase(array('purchase_id'=>$purchase_id));
        $purchase->is_consign = 0;
        if (!empty($purchase->batch_id))
        {
            $this->load->model("purchase_batch_model");
            $batch = $this->purchase_batch_model->filter(array("batch_id"=>$purchase->batch_id));
            if (!empty($batch))
                $purchase->is_consign = $batch->is_consign;
        }
        $product_list=$this->product_model->product_sub_for_scan(array('ps.provider_barcode'=>$provider_productcode,
                        'purchase_id'=>$purchase_id));

        if(count($product_list)>0){
	    $product = $product_list[0];
            $product->box_number = 0;
	    $product->production_batch = '';
            $product->check_num = 0;
            $product->oqc = '';
	    if($box_id >0){
		$result = $this->purchase_box_model->filter_purchase_box_sub(array("box_id"=>$box_id,"provider_barcode"=>$provider_productcode));
		if(count($result)>0){
		    $item = $result[0];
                    $product->box_number = $item ->product_number;
                    $product->production_batch = $item ->production_batch;
                    $product->expire_date = $item ->expire_date;
                    $product->check_num = $item ->check_num;
                    $product->oqc = $item ->oqc;
		}
	    }
            $product->is_consign = $purchase->is_consign;
            echo json_encode($product);
            exit;
        }
        echo '{"result":0,"msg":2}';
    }

    /**
     * 检查箱子是否已存在和是否关闭
     * 箱子上架数量=箱子收货数量则视为已关闭
     */
    public function check_box($purchase_code,$box_code)
    {
        $box_list=$this->purchase_box_model->filter_purchase_box_main(array('box_code'=>$box_code));
        $return_arr=array();
        if(count($box_list)>0)
        {
            $return_arr['is_exists']=true;
            $box=$box_list[0];
            if($box->purchase_code!=$purchase_code)
            {
                $return_arr['is_match']=false;
            }
            else
            {
                $return_arr['is_match']=true;
            }
            if($box->product_number==$box->product_shelve_num)
            {
                $return_arr['is_close']=true;
            }
            else
            {
                $return_arr['is_close']=false;
            }
        }
        else
        {
            $return_arr['is_exists']=false;
        }
        echo json_encode($return_arr);
    }

    /*
     * 执行扫描收货
     */
    public function do_scan($purchase_id = 0)
    {
        auth('purchase_box_scanning');
        $purchase_code=$this->input->post('purchase_code');
        $box_code=$this->input->post('box_code');
        $delivery_date=trim($this->input->post('delivery_date'));
        
        // 检查箱号和采购单号是否已关联
        $box_list=$this->purchase_box_model->filter_purchase_box_main(array('box_code'=>$box_code));
        if(count($box_list) > 0) {
            $box=$box_list[0];
            if($box->purchase_code != $purchase_code) {
                echo '2'; // 表示该箱子已被其他采购单占用，请重新选择！
                return;
            }
        }
        
        /*scan_info结构
          barcode_productid_colorid_sizeid_quantity,....,....,.....
        */
        $provider_barcode_array=$this->input->post('provider_barcode_array');
	$product_id_array=$this->input->post('product_id_array');
	$color_id_array=$this->input->post('color_id_array');
	$size_id_array=$this->input->post('size_id_array');
        $is_consign_array = $this->input->post('is_consign_array');
        $number_array=$this->input->post('number_array');
        $v_batch_array=$this->input->post('v_batch_array');
	$v_expire_array=$this->input->post('v_expire_array');
        $v_oqc_array=$this->input->post('v_oqc_array');
        $v_check_num_array=$this->input->post('v_check_num_array');
        $total_num=0;
	if(count($provider_barcode_array) != count($product_id_array) 
		|| count($product_id_array) != count($color_id_array)
		|| count($color_id_array) != count($size_id_array)
                || count($size_id_array) != count($is_consign_array)
		|| count($is_consign_array) != count($number_array)){
		echo false;
		return;
	}
        $this->load->model('product_model');
        //组织箱子子表数据
        foreach($provider_barcode_array as $i=>$provider_barcode)
        {
            $product_id = $product_id_array[$i];
            $color_id = $color_id_array[$i];
            $size_id = $size_id_array[$i];
            $number = $number_array[$i];
            $v_batch = $v_batch_array[$i];
            $v_expire = $v_expire_array[$i];
            $v_oqc = $v_oqc_array[$i];
            $v_check_num = $v_check_num_array[$i];
          
            $product_list=$this->product_model->product_sub_for_scan(array('ps.provider_barcode'=>$provider_barcode,
                          'purchase_id'=>$purchase_id));
            if(count($product_list)>0)
            {
                $product = $product_list[0];
                $is_consign = $is_consign_array[$i];
                //取消虚库销售类型，预期收货数量必须与实际收货数量一致的限制
                /*if ($is_consign==1 && $product->p_number<$product->product_finished_number+$number)
                {
                    echo false;
                    return;
                }*/
            }
	  
           $scan_info_arr[]=array('provider_barcode'=>$provider_barcode
                                  ,'product_id'=>$product_id,'color_id'=>$color_id
                                  ,'size_id'=>$size_id,'product_number'=>$number
                                  ,'production_batch'=>$v_batch,'expire_date'=>$v_expire
                                  ,'scan_id'=>$this->admin_id,'scan_starttime'=>date('Y-m-d H:i:s')
                                  ,'scan_endtime'=>date('Y-m-d H:i:s'), 'check_num' => $v_check_num, 'oqc' => $v_oqc
                                );
           $total_num+=$number;
        }
	if($total_num<1){
	    echo false;
	    return;
	}
        //组织箱子主表数据
        $box_main=array('box_code'=>$box_code,'purchase_code'=>$purchase_code,'delivery_date' => $delivery_date,
                        'product_number'=>$total_num,'product_shelve_num'=>0,
                        'scan_id'=>$this->admin_id,'scan_start_time'=>date('Y-m-d H:i:s'),
                        'scan_end_time'=>date('Y-m-d H:i:s'));
        $args=array('box_main'=>$box_main,'product_list'=>$scan_info_arr);
        $result=$this->purchase_box_model->scan_in($args);
        echo $result;
    }

    /**
     * 查看收货箱中商品
     */
    function box_product($box_id)
    {
        auth('purchase_box_product_view');
	    $data['full_page'] = true;
        $box=$this->purchase_box_model->filter_purchase_box_main(array('box_id'=>$box_id));
        if(empty($box))
        {
            sys_msg('箱号不存在',1);
        }
        $data['box']=$box[0];
        $list=$this->purchase_box_model->get_box_product($box_id);
        $data['list']=$list;
	$data['edit']=FALSE;
        $this->load->view('purchase/purchase_box_product',$data);
    }
    
    /**
     * 编辑收货箱中商品收货数量
     */
    function box_product_edit($box_id)
    {
        auth('purchase_box_product_view');
	$data['full_page'] = true;
        $box=$this->purchase_box_model->filter_purchase_box_main(array('box_id'=>$box_id));
        if(empty($box))
        {
            sys_msg('箱号不存在',1);
        }
        $data['box']=$box[0];
        $list=$this->purchase_box_model->get_box_product($box_id);
        $data['list']=$list;
	$data['edit']=TRUE;
        $this->load->view('purchase/purchase_box_product',$data);
    }
    
    function proc_box_prodcut_edit($box_id){
	$this->load->model('purchase_log_model');
	$now_time = date('Y-m-d H:i:s');
	$admin_name = $this->session->userdata('admin_name');
	$box=$this->purchase_box_model->filter_purchase_box_main(array('box_id'=>$box_id));
        if(empty($box))
        {
            sys_msg('箱号不存在',1);
        }
	$desc_content = "";
	$desc_msg = "";
	$this->db->trans_start();
        $list = $this->purchase_box_model->get_box_product($box_id);
	foreach ($list as $item){
	    $p_num = intval($this->input->post('p_num_'.$item ->box_sub_id));
	    if($p_num < intval($item->over_num)){
		echo json_encode(array("err"=>1,"msg"=>"收货数量不能小于上架数量","target"=>$item ->box_sub_id));
		return;
	    }
	    $this->purchase_box_model->update_purchase_box_sub(array("product_number"=>$p_num),$item ->box_sub_id);
	    $desc_content .= "箱子ID".$box_id."修改：SKU【".$item ->box_sub_id."】收货数量从".$item->product_number."更改为".$p_num."。修改人：".$admin_name."；修改时间：".$now_time;
	    $desc_msg .= "箱号".$box->box_code."修改：SKU【".$item ->box_sub_id."】收货数量从".$item->product_number."更改为".$p_num."。修改人：".$admin_name."；修改时间：".$now_time;
	}
	$this->purchase_log_model->insert(array("related_id"=>$box_id,"related_type"=>0,"desc_content"=>$desc_content,"create_admin"=>$this->admin_id,"create_date"=>$now_time));
	$this->db->trans_commit();
	echo json_encode(array("err"=>0,"msg"=>$desc_msg));
	return;
    }
    
    function proc_box_prodcut_statistics($box_id){
	$this->load->model('depot_model');
	$this->load->model('depotio_model');
	$this->load->model('purchasebox_scanning_model');
	$this->db->query('BEGIN');
	$box = $this->purchase_box_model->filter_purchase_box_main(array('box_id'=>$box_id));
        if(empty($box))
        {
            sys_msg('箱号不存在',1);
        }
	$depot_in_info = $this->depotio_model->filter_depot_in(array('order_id'=>$box_id,'depot_in_type'=>11,'in_type'=>2));
		
	//已全部上架（更新可售库存数量、入库单为审核状态、入库明细表为已入库状态）
	if (empty($depot_in_info)) {
		echo json_encode(array("err"=>1,"msg"=>"没对应的入库数量，请先扫描上架"));
		return;
	}
	if($depot_in_info->audit_admin !=0){
		echo json_encode(array("err"=>1,"msg"=>"该箱对应入库单已经审核，不需再次审核"));
		return;
	}
        $list = $this->purchase_box_model->filter_purchase_box_sub(array("box_id"=>$box_id));
	if(empty($list)){
	    echo json_encode(array("err"=>1,"msg"=>"该箱不存在对应扫描记录"));
	    return;
	}
	foreach ($list as $item){
	    if(intval($item->product_number) != intval($item->over_num)){
		echo json_encode(array("err"=>1,"msg"=>"该箱的收货数量和上架数量不一致，请先修改不一致商品的收货数量。"));
		return;
	    }
	}
	//判断仓库是否启用
	$depot_info2 = $this->purchasebox_scanning_model->get_depot_info(array('depot_id'=>$depot_in_info->depot_depot_id));
	if ($depot_info2->is_use == 1) {
		//更新可售库存数量
		$this->depot_model->update_gl_num_in($depot_in_info->depot_in_code);
	}

	//入库单为审核状态
	$update2 = array();
	$now_time = date('Y-m-d H:i:s');
	$update2['audit_date'] = $now_time;
	$update2['audit_admin'] = $this->admin_id;;
	$update2['lock_date'] = '0000-00-00 00:00';
	$update2['lock_admin'] = 0;
	$this->depotio_model->update_depot_in($update2, $depot_in_info->depot_in_id);

	//入库明细表为已入库状态
	$this->depot_model->update_transaction(array('trans_status'=>TRANS_STAT_IN,'update_admin'=>$this->admin_id,'update_date'=>date('Y-m-d H:i:s')), array('trans_status'=>TRANS_STAT_AWAIT_IN,'trans_sn'=>$depot_in_info->depot_in_code));

	$this->db->query('COMMIT');
	
	echo json_encode(array("err"=>0,"msg"=>"对应单据【".$depot_in_info->depot_in_code."】审核完成。审核人：".$this->session->userdata('admin_name')."，审核时间：".$now_time."。"));
	return;
    }
    
    public function proc_edit_purchase_num(){
	$box_id = intval($this->input->post("box_id"));
	$box_sub_id = intval($this->input->post("box_sub_id"));
	$box_sub_val = intval($this->input->post("box_sub_val"));
	$this->load->model('purchase_log_model');
	$now_time = date('Y-m-d H:i:s');
	$admin_name = $this->session->userdata('admin_name');
	$desc_content = "";
	$desc_msg = "";
	$box = $this->purchase_box_model->filter_purchase_box_main(array('box_id'=>$box_id));
        if(empty($box))
        {
            sys_msg('箱号不存在',1);
        }
	$this->db->trans_start();
        $list = $this->purchase_box_model->filter_purchase_box_sub(array("box_sub_id"=>$box_sub_id,"box_id"=>$box_id));
	if(empty($list)){
	    echo json_encode(array("err"=>1,"msg"=>"不存在对应记录"));
	    return;
	}
	foreach ($list as $item){
	    if(intval($item->product_number) == $box_sub_val){
		echo json_encode(array("err"=>0,"msg"=>"数量没做修改"));
		return;
	    }
	    if(intval($item->product_number) < $box_sub_val){
		echo json_encode(array("err"=>2,"msg"=>"非法输入","val"=>intval($item->product_number)));
		return;
	    }
	    if($box_sub_val < intval($item->over_num)){
		echo json_encode(array("err"=>1,"msg"=>"收货数量不能小于上架数量","target"=>$item ->box_sub_id));
		return;
	    }
	    $num = intval($item->product_number) -$box_sub_val;
	    if($box_sub_val != 0){
		$this->purchase_box_model->update_purchase_box_sub(array("product_number"=>$box_sub_val),$item ->box_sub_id);
	    }else{
		$this->purchase_box_model->delete_purchase_box_sub(array("box_sub_id"=>$item ->box_sub_id));
	    }
	    $this->purchase_box_model->update_purchase_main_finished_number($num,$box[0]->purchase_code);
	    $this->purchase_box_model->update_purchase_sub_finished_number($num,$item->product_id,$item->color_id,$item->size_id);
	    $this->purchase_box_model->update_purchase_box_main(array("product_number"=>intval(intval($box[0]->product_number)-$num)),$box_id);
	    $desc_content .= "箱子ID".$box_id."修改：BOX_SUB_ID ".$item ->box_sub_id."收货数量从".$item->product_number."更改为".$box_sub_val."。修改人：".$admin_name."；修改时间：".$now_time;
	    $desc_msg .= "箱号".$box[0]->box_code."修改：条码【".$item ->provider_barcode."】收货数量从".$item->product_number."更改为".$box_sub_val."。修改人：".$admin_name."；修改时间：".$now_time;
	}
	$this->purchase_log_model->insert(array("related_id"=>$box_id,"related_type"=>0,"desc_content"=>$desc_content,"create_admin"=>$this->admin_id,"create_date"=>$now_time));
	$this->db->trans_commit();
	echo json_encode(array("err"=>0,"msg"=>$desc_msg));
	return;
    }
    
    public function pruchase_box_scan_list($purchase_code){
	auth('pruchase_box_scan_list');
	$this->load->model('depot_model');
	$this->load->model('admin_model');
        $all_admin = $this->admin_model->all_admin();
	$purchase=$this->depot_model->filter_purchase(array('purchase_code'=>$purchase_code));
	if(!empty($purchase->purchase_provider)){
	    $this->load->model('provider_model');
	    $provider = $this->provider_model->filter(array("provider_id"=>$purchase->purchase_provider));
	    if(!empty($provider)){
		$purchase -> provider_name ="[".$provider->provider_code."]".$provider->provider_name;
	    }
	}else{
	    $purchase -> provider_name = "";
	}
	$box_list = $this->purchase_box_model->filter_purchase_box_main(array('purchase_code'=>$purchase_code));
	foreach ($box_list as $box){
	   $box_sub_list = $this->purchase_box_model->get_box_product($box->box_id);
	   $box->box_sub_list = $box_sub_list;
	   if(!empty($box->scan_id)){
	      $box->scan_name = $all_admin[$box->scan_id]->realname ;
	   }else{
	       $box->scan_name ="";
	   }
	   if(!empty($box->shelve_id)){
	      $box->shelve_name = $all_admin[$box->shelve_id]->realname ;
	   }else{
	       $box->shelve_name = "";
	   }
	}
	$data["purchase"] = $purchase;
	$data["box_list"] = $box_list;
	$this->load->view('purchase/purchase_box_scan_list',$data);
    }
    
    /**
     * 取消采购单收货
     * @param type $purchase_code
     */
    public function cancel_purchase_scan($purchase_code){
	auth('cancel_purchase_scan');
	$this->load->model('depot_model');
	$this->load->model('depotio_model');
	$this->load->model('purchase_log_model');
	$now_time = date('Y-m-d H:i:s');
	$admin_name = $this->session->userdata('admin_name');
        $purchase = $this->depot_model->filter_purchase(array('purchase_code'=>$purchase_code));
	if(empty($purchase)){
	     echo json_encode(array("msg"=>"不存在对应采购单",1));
	     return;
	}
	$purchase_box_list = $this->purchase_box_model->filter_purchase_box_main(array('purchase_code'=>$purchase_code));
	if(empty($purchase_box_list) || count($purchase_box_list) < 1){
	     echo json_encode(array("msg"=>"不存在对应扫描记录",1));
	     return;
	}
	$depot_in_id_array = array();
	$depot_in_code_array = array();
	foreach ($purchase_box_list as $purchase_box){
	    $depot_in_main = $this->depotio_model->filter_depot_in(array("order_sn"=>$purchase_code,"order_id"=>$purchase_box->box_id,"depot_in_type"=>11));
	    if(!empty($depot_in_main)){
		 $depot_in_id_array[] = $depot_in_main ->depot_in_id;
		$depot_in_code_array[] = $depot_in_main ->depot_in_code;
		if($depot_in_main -> audit_admin != 0){
		     echo json_encode(array("msg"=>"对应商品已上架不允许取消","err"=>1));
		     return;
		}
	    }
	    $desc_content = $admin_name."于".$now_time."取消箱子".$purchase_box->box_code."的收货记录";
	    $this->purchase_log_model->insert(array("related_id"=>$purchase_box->box_id,"related_type"=>1,"desc_content"=>$desc_content,"create_admin"=>$this->admin_id,"create_date"=>$now_time));
	}
	if(count($purchase_box_list) < count($depot_in_id_array)){
	    echo json_encode(array("msg"=>"入库单数量大于采购单数量，不允许操作，请联系管理员。","err"=>1));
	    return;
	}
	$this->db->query('begin');
	
	$this->purchase_box_model->delete_purchase_box_main(array('purchase_code'=>$purchase_code));//删除扫描箱子记录
	$this->purchase_box_model->set_purchase_main(array("purchase_finished_number"=>0,"purchase_shelved_number"=>0),$purchase_code);//设置完成数量、上架数量 采购主表
	$this->purchase_box_model->set_purchase_sub(array("product_finished_number"=>0),$purchase->purchase_id);//设置完成数量 采购子表
	//删除入库单.逻辑删除
	foreach ($depot_in_id_array as $deot_in_id){
	    $this->depotio_model->update_depot_in(array("is_deleted"=>1),$deot_in_id);
	}
	//revert出入库记录，入库明细表为取消状态 status=5
	foreach ($depot_in_code_array as $depot_in_code)
	    $this->depot_model->update_transaction(array('trans_status'=>TRANS_STAT_CANCELED,'update_admin'=>$this->admin_id,'update_date'=>$now_time), array('trans_status'=>TRANS_STAT_AWAIT_IN,'trans_sn'=>$depot_in_code));
	$this->db->query('commit');
	echo json_encode(array("msg"=>"操作完成","err"=>0));
    }
    
    /**
     * 取消采购单中箱子收货
     * @param type $purchase_code
     */
    public function cancel_purchase_box_scan($purchase_box_id){
	auth('cancel_purchase_box_scan');
	$this->load->model('depotio_model');
	$this->load->model('depot_model');
	$this->load->model('purchasebox_scanning_model');
	$this->load->model('purchase_log_model');
	$now_time = date('Y-m-d H:i:s');
	$admin_name = $this->session->userdata('admin_name');
	$box=$this->purchase_box_model->filter_purchase_box_main(array('box_id'=>$purchase_box_id));
	$purchase_code = $box[0]->purchase_code;
	$box_sub_list = $this->purchase_box_model->filter_purchase_box_sub(array('box_id'=>$purchase_box_id));
	if(empty($box_sub_list) || count($box_sub_list) <1){
	    echo json_encode(array("msg"=>"不存在对应箱子记录","err"=>1));
	    return;
	}
	$deot_in_id = 0;
	$depot_in_code = '';
	$depot_in_main = $this->depotio_model->filter_depot_in(array("order_sn"=>$purchase_code,"order_id"=>$purchase_box_id,"depot_in_type"=>11));
	if(!empty($depot_in_main)){
	    $deot_in_id = $depot_in_main -> depot_in_id;
	    $depot_in_code = $depot_in_main ->depot_in_code;
	    if($depot_in_main -> audit_admin != 0){
		 echo json_encode(array("msg"=>"对应商品已上架不允许取消","err"=>1));
		 return;
	    }
	}
	$this->db->query('begin');
	$cancel_count = 0; 
	foreach ($box_sub_list as $box_sub){
	    $cancel_count += $box_sub->product_number;
	    $this->purchase_box_model->update_purchase_sub_finished_number($box_sub->product_number,$box_sub->product_id,$box_sub->color_id,$box_sub->size_id);
	}
	$this->purchase_box_model->update_purchase_main_finished_number($cancel_count,$purchase_code);
	$this->purchase_box_model->delete_purchase_box_main(array("box_id"=>$purchase_box_id));
	//更新采购单主表上架信息
	$this->purchasebox_scanning_model->update_purchase($purchase_code);
	if($deot_in_id != 0){ $this->depotio_model->update_depot_in(array("is_deleted"=>1),$deot_in_id);	}
	if(!empty($depot_in_code)){ $this->depot_model->update_transaction(array('trans_status'=>TRANS_STAT_CANCELED,'update_admin'=>$this->admin_id,'update_date'=>$now_time), array('trans_status'=>TRANS_STAT_AWAIT_IN,'trans_sn'=>$depot_in_code));	}
	$desc_content = $admin_name."于".$now_time."取消箱子".$box[0]->box_code."的收货记录";
	$this->purchase_log_model->insert(array("related_id"=>$purchase_box_id,"related_type"=>1,"desc_content"=>$desc_content,"create_admin"=>$this->admin_id,"create_date"=>$now_time));
	$this->db->query('commit');
	echo json_encode(array("msg"=>"操作完成","err"=>0));
    }
    
    
    public function pruchase_provider_barcode($purchase_code){
	auth('pruchase_provider_barcode');
	$this->load->model('depot_model');
	$this->load->model('admin_model');
        $all_admin = $this->admin_model->all_admin();
	$purchase=$this->depot_model->filter_purchase(array('purchase_code'=>$purchase_code));
	$list = $this->purchase_box_model->get_purchase_product(array('pm.purchase_code'=>$purchase_code));
	$data["purchase"] = $purchase;
	$data["list"] = $list;
	$this->load->view('purchase/pruchase_provider_barcode',$data);
    }
    
    public function pruchase_provider_barcode_scaned($purchase_code){
	auth('pruchase_provider_barcode');
	$this->load->model('depot_model');
	$this->load->model('admin_model');
	$purchase=$this->depot_model->filter_purchase(array('purchase_code'=>$purchase_code));
	$list = $this->purchase_box_model->get_purchase_product(array('pm.purchase_code'=>$purchase_code));
	$data["purchase"] = $purchase;
	$data["list"] = $list;
	$this->load->view('purchase/pruchase_provider_barcode_scaned',$data);
    }
    
    public function print_provider_barcode(){
	auth('pruchase_provider_barcode');
	$this->load->model('product_model');
	$sub_ids = $this->input->post('product_id');
	if(empty($sub_ids) && count($sub_ids)<1){
	    sys_msg("请选择需要打印的商品",1);
	}
	$print_list = array();
	foreach ($sub_ids as $sub_id){
	    $count = $this->input->post('p_'.$sub_id);
	    $index = intval($this->input->post('in_'.$sub_id));
	    $sub = $this->product_model->query_all_sub(array("sub_id" =>$sub_id));
	    foreach ($sub as $row) {
                $row['sub_count'] = intval($count);
		$row['index'] = $index;
                $print_list[$row['sub_id']] = $row;
            }
	}
	$data = array();
	$data["list"] = $print_list;
        if(strpos($_SERVER["HTTP_USER_AGENT"],"Firefox") && strpos($_SERVER["HTTP_USER_AGENT"],"Windows NT 6.1")) {
            $data['browser'] = "Firefox";
        } elseif(strpos($_SERVER["HTTP_USER_AGENT"],"Chrome")) {
            $data['browser'] = "Chrome";
        } elseif (strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")) {
            $data['browser'] = "IE";
        }else {
            sys_msg('目前只支持Chrome浏览器、IE浏览器或win7+Firefox浏览器打印',1);
        }
	$this->load->view('purchase/print_purchase_barcode',$data);
    }
    
    public function export_provider_barcode(){
	auth('pruchase_provider_barcode');
	$this->load->model('product_model');
	$sub_ids = $this->input->post('product_id');
	$purchase_code =  trim($this->input->post('purchase_code'));
	$type =  trim($this->input->post('type'));
	if(empty($sub_ids) && count($sub_ids)<1){
	    sys_msg("请选择需要导出的商品",1);
	}
	$exlval = array();
	$list = $this->purchase_box_model->get_purchase_product(array('pm.purchase_code'=>$purchase_code));
	$history = "";$index = 0;
	foreach ($list as $item){
		if($type != "scaned" && intval($item->product_number) - intval($item->finished_scan_number) <=0){
		    continue;
		}
		if(empty($history)){
		    $history =$item->product_sn;
		    $index+=1;
		}elseif ($history != $item->product_sn) {
		    $history =$item->product_sn;
		    $index+=1;
		}
		$product_number = 1;
		if($type != "scaned"){
		    $product_number = intval($this->input->post('p_'.$item->sub_id));
		}
		$exlval[] = array('index'=>$index,'product_sn'=>$item->product_sn,'product_name'=>$item->product_name,"provider_productcode"=>$item->provider_productcode,
		     "color_name"=>$item->color_name,"size_name"=>$item->size_name,"provider_barcode"=>$item->provider_barcode,"product_number"=>$product_number);
	}
	$info_title = array('index'=>'序号','product_sn'=>'商品款号','product_name'=>'商品名称',"provider_productcode"=>"货号",
	    "color_name"=>"颜色","size_name"=>"尺寸","provider_barcode"=>"条码","product_number"=>"打印条码数");
	$this->load->helper('excel');
	$name = $purchase_code;
	$name .= $type == "scaned"?"_已收货条码":"_需收货条码";
	$name .= "_".date('YmdHis');
	export_excel_xml($name,array($info_title,$exlval));
    }
    
}
