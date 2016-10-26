<?php

/**
 * Description of Outbound
 * 扫描出库逻辑控制
 * @author PUBLIC
 */
class Outbound extends CI_Controller {

    public function __construct() {
	parent::__construct();
	$this->admin_id = $this->session->userdata('admin_id');
	if (!$this->admin_id) {
	    redirect('index/login');
	}
	$this->load->model('depot_model');
	$this->load->model('depotio_model');
	$this->load->model('depot_box_model');
    }

    /**
     * 出库扫描
     * param: $purchase_code  出库单id
     */
    public function scan($depot_out_id = 0) {
	auth(array('outbound_scanning','outbound_scan_view'));
	$box_code =trim($this->input->get('box_code'));
	$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_id' => $depot_out_id));
	if (empty($depot_out_info)) {
	    echo json_encode(array('error' => 1, 'msg' => '记录不存在'));
	    return;
	}
	//Query out BOX 
	$depot_out_code = $depot_out_info ->depot_out_code;
	$box_count = 0 ;
	$finished_scan_number = 0;
	$depot_out_boxes = $this->depot_box_model->filter_depot_box_main(array('depot_out_code' => $depot_out_code));
	if (!empty($depot_out_boxes)) {
	    $box_count = count($depot_out_boxes);
	    foreach ($depot_out_boxes as $depot_out_box){
		$product_number = $depot_out_box ->product_number ;
		$finished_scan_number += $product_number;
	    }
	}
	$data['box_code'] ="";
	if(!empty($box_code)){
	    $data['box_code']=$box_code;
	    $box_list = $this->depot_box_model->filter_depot_box_main(array('box_code' => $box_code));
	    if(!empty($box_list)){
		$box_id = $box_list[0]->box_id;
		$details_list = $this->depot_box_model->query_depot_box_sub_scan(array("main.box_id"=>$box_id,"o.depot_out_code"=>$depot_out_code));
		foreach ($details_list as $item){
		    $item ->finished_scan_number = 0;
		    $depot_out_box_subs =$this->depot_box_model->query_depot_box_sub_group(
			    array('main.depot_out_code' => $depot_out_code,
				"sub.product_id"=>$item->product_id,"sub.color_id"=>$item->color_id,"sub.size_id"=>$item->size_id));
		    if(count($depot_out_box_subs) >0){
			$item ->finished_scan_number = $depot_out_box_subs[0]->finished_scan_number;
		    }
		}
		$data['details_list'] = $details_list;
	    }
	}
	$data['depot_content'] = $depot_out_info;
	$data['full_page'] = TRUE;
	$data['finished_scan_number'] = $finished_scan_number;
	$data['box_count'] = $box_count;
	
	$this->load->view('depot/outbound/scaning', $data);
    }
    
    public function gen_box($depot_out_code){
	$box_code ="";
	$box_list = $this->depot_box_model->filter_depot_box_main(array('depot_out_code' => $depot_out_code));
	if(empty($box_list)){
	    $box_code = $depot_out_code . "-001";
	    echo json_encode(array("err"=>0,"box_code"=>$box_code));
	    return ;
	}
	$index= array();
	foreach($box_list as $box){
	    $tmp_box_code = $box->box_code;
	    $code_array = explode("-", $tmp_box_code);
	    if(count($code_array)>1){
		$index[] = intval($code_array[1]);
	    }
	}
	$code = 1;
	if(count($index) >0){
	    $code = max($index)+1;
	}
	$code = $depot_out_code . "-" . str_pad($code, 3,"0",STR_PAD_LEFT);
	echo json_encode(array("err"=>0,"box_code"=>$code));
	return ;
    }
    
    /**
     * 检查箱子是否已存在
     */
    public function check_box($depot_out_code, $box_code) {
	auth('outbound_scanning');
	$box_list = $this->depot_box_model->filter_depot_box_main(array('box_code' => $box_code));
	if(empty($box_list)){
	    echo json_encode(array("err"=>0));
	    return ;
	}
	if (count($box_list) > 1) {
	    sys_msg('箱子不允许重新打开！', 1);
	}
	$box = $box_list[0];
	if($box->depot_out_code == $depot_out_code){
	     echo json_encode(array("err"=>0,"msg"=>"重新打开箱子"));
	}else{
	    echo json_encode(array("err"=>1,"msg"=>"箱号重复，请重新扫描新箱号"));
	}
	 return ;
    }

    /**
     * 扫描条码后查找商品
     */
    public function get_product($depot_out_id) {
	auth('outbound_scanning');
	$provider_productcode =trim($this->input->post('product_code'));
	$box_code =trim($this->input->post('box_code'));
	if(empty($provider_productcode)){
	    echo '{"result":0}';
            exit;
	}
	$depot_out_main = $this->depot_box_model->filter_depot_out_main(array("depot_out_id"=>$depot_out_id));
	if(empty($depot_out_main)){
	    echo '{"result":0}';
            exit;
	}
	$box_id = 0;
	if(!empty($box_code)){
	   $out_box_list = $this->depot_box_model->filter_depot_box_main(array("box_code"=>$box_code,"depot_out_code"=>$depot_out_main->depot_out_code));
	   if(!empty($out_box_list) && count($out_box_list)>0){
	       $box_id = $out_box_list[0]->box_id;
	   }
	}
	$product_list = $this->depot_box_model->product_sub_for_scan(array('provider_barcode' => $provider_productcode,
	    'depot_out_id' => $depot_out_id));
	if (!empty($product_list) && count($product_list) > 0) {
	    $product = $product_list[0];
	    $product ->finished_scan_number = 0;
	    $depot_out_box_subs =$this->depot_box_model->query_depot_box_sub_group(
		    array('main.depot_out_code' => $depot_out_main->depot_out_code,
			"sub.product_id"=>$product->product_id,"sub.color_id"=>$product->color_id,"sub.size_id"=>$product->size_id));
	    if(count($depot_out_box_subs) >0){
		foreach ($depot_out_box_subs as $depot_out_box_sub){
		    $product ->finished_scan_number += $depot_out_box_sub->finished_scan_number;
		}
	    }
	    $product ->box_finished_scan_number = 0;
	    if($box_id >0){
		$depot_out_box_subs =$this->depot_box_model->query_depot_box_sub_group(
			array('main.depot_out_code' => $depot_out_main->depot_out_code,"sub.box_id"=>$box_id,
			    "sub.product_id"=>$product->product_id,"sub.color_id"=>$product->color_id,"sub.size_id"=>$product->size_id));
		if(count($depot_out_box_subs) >0){
		    $product ->box_finished_scan_number = $depot_out_box_subs[0]->finished_scan_number;
		}
	    }
	    echo json_encode($product);
	    exit;
	}
	echo '{"result":0}';
    }
    
    public function do_scan(){
	auth('outbound_scanning');
	$box_code = $this->input->post('box_code');
	$depot_out_code = $this->input->post('depot_out_code');
	$provider_barcode_array=$this->input->post('provider_barcode_array');
	$product_id_array=$this->input->post('product_id_array');
	$color_id_array=$this->input->post('color_id_array');
	$size_id_array=$this->input->post('size_id_array');
	$number_array=$this->input->post('number_array');
	if(empty($depot_out_code)){
	    sys_msg('记录不存在！', 1);
	    return ;
	}
	if(empty($box_code)){
	     sys_msg('箱子记录不存在！', 1);
	     return ;
	}
	if(empty($provider_barcode_array) || count($provider_barcode_array)<1){
	     sys_msg('扫描记录不存在！', 1);
	     return ;
	}
	$product_array = array();
	
	$num = 0 ;
	if(!empty($provider_barcode_array)){
	    foreach($provider_barcode_array as $i =>$provider_barcode){
		$product = array();
		$product["product_code"] = $provider_barcode;
		$product["product_id"] = $product_id_array[$i];
		$product["color_id"] = $color_id_array[$i];
		$product["size_id"] = $size_id_array[$i];
		$product["num"] = $number_array[$i];
		$num += $product["num"];
		$product_array[] = $product;
	    }
	}
	$this->depot_box_model->do_scan($depot_out_code,$box_code,$num,$this->admin_id,$product_array);
	echo json_encode(array("err"=>0));
    }
    
    public function box_detail($depot_out_code){
	auth(array('outbound_scanning','outbound_scan_view'));
	$data = array();
	$depot_out_info = $this->depotio_model->filter_depot_out(array('depot_out_code' => $depot_out_code));
	if (empty($depot_out_info)) {
	    echo json_encode(array('error' => 1, 'msg' => '记录不存在'));
	    return;
	}
	$box_list = $this->depot_box_model->query_depot_box_main(array("depot_out_code"=>$depot_out_code));
	if(empty($box_list)){
	    sys_msg("没有装箱清单",1);
	}
	$scan_num = 0;
	foreach ($box_list as $box){
	    $scan_num += $box->product_number;
	    $box ->detail_list = $this->depot_box_model->query_depot_box_sub(array("box_id"=>$box->box_id));
	}
	$data["depot_content"] = $depot_out_info;
	$data["content"] = $box_list;
	$data["scan_num"] = $scan_num;
	$data["box_count"] = count($box_list);
	$this->load->view('depot/outbound/box_detail', $data);
    }
    
    public function print_box_order($box_id){
	$data = array();
	if(empty($box_id))
	    sys_msg ("数据有误，请联系管理员",1);
	$box = $this->depot_box_model->query_depot_box_main(array("box_id"=>$box_id));
	if(empty($box))
	    sys_msg ("箱子不存在，请联系管理员",1);
	$list = $this->depot_box_model->query_depot_box_sub(array("box_id"=>$box_id));
	if(empty($list) || count($list) ==0)
	    sys_msg ("此箱子没有对应记录");
	$data["list"] = $list;
	$data["box"] = $box[0];
	$this->load->view('depot/outbound/print_outbound_box', $data);
    }
    
    public function cancel_depot_out_box_scan($box_id){
	auth('cancel_depot_out_box_scan');
	$this->load->model('purchase_log_model');
	$now_time = date('Y-m-d H:i:s');
	if(empty($box_id)){
	     echo json_encode(array("err"=>1,"msg"=>'箱子记录不存在！'));
	     return ;
	}
	$this->db->trans_begin();
	$box_list = $this->depot_box_model->filter_depot_box_main(array("box_id"=>$box_id));
	if(empty($box_list) || count($box_list) <1){
	     echo json_encode(array("err"=>1,"msg"=>'箱子记录不存在！'));
	     return ;
	}
	$box = $box_list[0];
	$this->depot_box_model->delete_depot_out_box($box_id);
	$this->depot_box_model->delete_depot_out_box_sub($box_id);
	$this->depot_box_model->update_depot_out_finished_num($box->depot_out_code,$box->product_number,'-');
	$desc_content = $this->session->userdata('admin_name')."于".$now_time."取消箱子".$box->box_code."的出库扫描记录";
	$this->purchase_log_model->insert(array("related_id"=>$box_id,"related_type"=>2,"desc_content"=>$desc_content,"create_admin"=>$this->admin_id,"create_date"=>$now_time));
	$this->db->trans_commit();
	echo json_encode(array("err"=>0));
    }

}

?>
