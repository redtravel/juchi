<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Rush extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        $this->time = date('Y-m-d H:i:s');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('rush_model');
	$this->load->model('product_type_model');
        $this->status_list=array(0=>'未激活',1=>'已激活',2=>'停止',3=>'结束');
    }

    public function index ()
    {
        auth(array('rush_view','rush_edit'));
        $this->load->model('frontnav_model');
        $this->load->helper('perms_helper');
        $this->load->vars(array(
            'perm_edit' => check_perm('rush_edit'),
            'perm_audit' => check_perm('rush_audit'),
	    'perm_set' =>check_perm('rush_set_properties'),
        ));
        // $this->load->vars('all_nav', index_array($this->frontnav_model->all(),'nav_id'));
        $filter = $this->uri->uri_to_assoc(3);
	
	$query_rush_id = $this->input->post("query_rush_id");
	if(!empty($query_rush_id)) $filter['query_rush_id'] = $query_rush_id;
        $rush_index = $this->input->post("rush_index");
        if(!empty($rush_index)) $filter['rush_index'] = $rush_index;
        // $nav_id = $this->input->post("nav_id");
        // if(!empty($nav_id)) $filter['nav_id'] = intval($nav_id);
        $status = $this->input->post("status");
        if(!empty($status)) $filter['status'] = $status;
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;

        $filter = get_pager_param($filter);
        $data = $this->rush_model->rush_list($filter);
        $this->load->model('admin_model');
        $admin_arr = $this->admin_model->all_admin();
        $this->load->vars('admin_arr',$admin_arr);
        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('rush/list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('rush/list', $data);
    }


    public function add()
    {
        auth('rush_edit');
        $this->load->model('frontnav_model');
        // $this->load->vars('all_nav', index_array($this->frontnav_model->all(array('nav_name <>'=>'首页')),'nav_id'));
        $this->load->view('rush/add');
    }

    public function proc_add()
    {
        auth('rush_edit');
        $data['rush_index'] = trim($this->input->post('rush_index'));
	    $data["campaign_id"] = $this->input->post('campaign_id');
	    // $data["rush_category"] = $this->input->post('rush_category');
	    // $data["rush_discount"] = $this->input->post('rush_discount');
        // $data['nav_id'] = intval($this->input->post('nav_id'));
        $data['sort_order'] = trim($this->input->post('sort_order'));
        $data['jump_url'] = trim($this->input->post('jump_url'));
        $data['status'] = 0;
        $data['desc'] = trim($this->input->post('desc'));
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');

        $data['start_date_p'] = trim($this->input->post('start_date_p'));
        $data['start_time'] = trim($this->input->post('start_time'));
        $data['end_date_p'] = trim($this->input->post('end_date_p'));
        $data['end_time'] = trim($this->input->post('end_time'));
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $data['start_date_p'])
           ||!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $data['end_date_p'])
           ||($data['start_time'] && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $data['start_time']))
           ||($data['end_time'] && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $data['end_time']))
        ){sys_msg('开始结束时间格式错误',1);return;}
        $data['start_date'] = $data['start_date_p'].' '.$data['start_time'];
        $data['end_date'] = $data['end_date_p'].' '.$data['end_time'];
        unset($data['start_date_p']);unset($data['start_time']);unset($data['end_date_p']);unset($data['end_time']);
        if($data['end_date']<$this->time) sys_msg('结束日期小于当前日期',1);
        if($data['end_date']<$data['start_date']) sys_msg('结束日期小于开始日期',1);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('rush_index', 'rush_index', 'trim|required');
        $this->form_validation->set_rules('start_date_p', 'start_date_p', 'trim|required');
        $this->form_validation->set_rules('end_date_p', 'end_date_p', 'trim|required');
        $this->form_validation->set_rules('sort_order', 'sort_order', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
    	$this->vali_length($data["rush_index"], 26 , '限抢名称');
    	
    	// $this->vali_length($data["rush_brand"], 16 , '限抢品牌');
    	// $this->vali_length($data["rush_category"], 20 , '限抢分类');
    	// $this->vali_length($data["rush_discount"], 4 , '限抢折扣');
        $rush_id = $this->rush_model->insert($data);
        // 上传图片
        $this->load->library('upload');
        $update = array();
        //$base_path = APPPATH.'../public/upload/rush/';
        $sub_dir = ($rush_id-$rush_id%100)/100;
        if(!file_exists(UPLOAD_PATH_RUSH . UPLOAD_TAG_RUSH.$sub_dir)) mkdir(UPLOAD_PATH_RUSH.UPLOAD_TAG_RUSH.$sub_dir, 0777, true);

        $this->upload->initialize(array(
                'upload_path' => UPLOAD_PATH_RUSH.UPLOAD_TAG_RUSH.$sub_dir,
                'allowed_types' => 'gif|jpg|png',
                'encrypt_name' => TRUE
        ));
        if($this->upload->do_upload('image_before_url')){
                $file = $this->upload->data();
                $update['image_before_url'] = UPLOAD_TAG_RUSH . $sub_dir.'/'.$file['file_name'];
                $this->process_rush_logo(UPLOAD_PATH_RUSH.UPLOAD_TAG_RUSH.$sub_dir.'/'.$file['file_name']);
        }
        if($this->upload->do_upload('image_ing_url')){
                $file = $this->upload->data();
                $update['image_ing_url'] =  UPLOAD_TAG_RUSH . $sub_dir.'/'.$file['file_name'];
        }
        if ($update) {
                $this->rush_model->update($update, $rush_id);
        }
        sys_msg('操作成功',2,array(array('href'=>'rush/index','text'=>'返回列表页')));
    }
    
    /**
     * 删除rush logo
     */
    private function unlink_rush_logo($source_file) {
    	$ext = strtolower(substr($source_file,-4));
        $basename = substr($source_file, 0, -4);
    	@unlink($source_file);
    	@unlink($basename."_1".$ext);
    	@unlink($basename."_2".$ext);
    	@unlink($basename."_3".$ext);
    }
    
    /**
     * 处理rush logo
     */
    private function process_rush_logo($source_file) {
        $ext = strtolower(substr($source_file,-4));
        $basename = substr($source_file, 0, -4);
        $this->resize_image($source_file,$basename."_1".$ext,43,49);
        $this->resize_image($source_file,$basename."_2".$ext,218,253);
        $this->resize_image($source_file,$basename."_3".$ext,316,366);
    }
    
    /**
     * 剪裁图片
     */
    private function resize_image($source_image,$new_image,$width,$height) {
    	$this->load->library('image_lib');
    	$this->image_lib->initialize(array(
			'source_image' => $source_image,
			'new_image' => $new_image,
			'quality'=>85,
			'maintain_ratio'=>FALSE,
			'width'=>$width,
			'height'=>$height
		));
		if(!$this->image_lib->resize()) {
			sys_msg('剪裁图片失败：'.$this->image_lib->display_errors(), 1);
		}
		$this->image_lib->clear();
    }

    function edit($rush_id){
        auth(array('rush_view','rush_edit'));
        $this->load->model('depot_model');
        $this->load->model('style_model');
        $this->load->model('season_model');
        $this->load->model('category_model');
        $this->load->model('brand_model');
        $this->load->model('frontnav_model');
        $this->load->helper('perms_helper');
        $this->load->helper('category_helper');
        $rush_id = intval($rush_id);
        $check = $this->rush_model->filter(array('rush_id' => $rush_id));
        if(empty($check)){
            sys_msg('记录不存在', 1);
            return;
        }
        $start_arr = explode(' ',$check->start_date);
        $end_arr = explode(' ',$check->end_date);
        $link_product = $this->rush_model->rush_product_list($rush_id);
        $this->load->helper('product_helper');
        attach_sub($link_product);
        $this->load->vars('perms' , get_rush_perm($check));
        $this->load->vars('all_depot' , $this->depot_model->all_depot(array('depot_type' => 1)));
        // $this->load->vars('all_brand' , $this->brand_model->all_brand());
        $this->load->vars('style_arr' ,  $this->style_model->all_style());
        $this->load->vars('season_arr' ,  $this->season_model->all_season());
        // $this->load->vars('all_category',category_flatten(category_tree($this->category_model->all_category()),'-- '));
        $this->load->vars('link_product' , $link_product);
        $this->load->vars('start_arr' , $start_arr);
        $this->load->vars('end_arr' , $end_arr);
        $this->load->vars('check' , $check);
	$this->load->vars('all_nav', index_array($this->frontnav_model->all(array('nav_name <>'=>'首页')),'nav_id'));
        $this->load->view('rush/edit');
    }

    function proc_edit($rush_id){
        $this->load->helper('perms_helper');
        $rush_id = intval($rush_id);
        $rush = $this->rush_model->filter(array('rush_id' => $rush_id));
        if(empty($rush)){
            sys_msg('记录不存在', 1);
            return;
        }
        $perms=get_rush_perm($rush);
        if(!$perms['rush_edit']) sys_msg('没有权限',1);
        $data['rush_index'] = trim($this->input->post('rush_index'));
	$data["campaign_id"] = $this->input->post('campaign_id');
	// $data["rush_category"] = $this->input->post('rush_category');
	// $data["rush_discount"] = $this->input->post('rush_discount');
        // $data['nav_id'] = intval($this->input->post('nav_id'));
        $data['sort_order'] = trim($this->input->post('sort_order'));
        $data['jump_url'] = trim($this->input->post('jump_url'));

//        $data['desc'] = trim($this->input->post('desc'));

        $data['start_date_p'] = trim($this->input->post('start_date_p'));
        $data['start_time'] = trim($this->input->post('start_time'));
        $data['end_date_p'] = trim($this->input->post('end_date_p'));
        $data['end_time'] = trim($this->input->post('end_time'));
	$this->vali_length($data["rush_index"], 26 , '限抢名称');
	// $this->vali_length($data["rush_brand"], 16 , '限抢品牌');
	// $this->vali_length($data["rush_category"], 20 , '限抢分类');
	// $this->vali_length($data["rush_discount"], 4 , '限抢折扣');
	
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $data['start_date_p'])
           ||!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $data['end_date_p'])
           ||($data['start_time'] && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $data['start_time']))
           ||($data['end_time'] && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $data['end_time']))
        ){sys_msg('开始结束时间格式错误',1);return;}
        $data['start_date'] = $data['start_date_p'].' '.$data['start_time'];
        $data['end_date'] = $data['end_date_p'].' '.$data['end_time'];
        unset($data['start_date_p']);unset($data['start_time']);unset($data['end_date_p']);unset($data['end_time']);
        if($data['end_date']<$this->time) sys_msg('结束日期小于当前日期',1);
        if($data['end_date']<$data['start_date']) sys_msg('结束日期小于开始日期',1);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('rush_index', 'rush_index', 'trim|required');
        $this->form_validation->set_rules('start_date_p', 'start_date_p', 'trim|required');
        $this->form_validation->set_rules('end_date_p', 'end_date_p', 'trim|required');
        $this->form_validation->set_rules('sort_order', 'sort_order', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->rush_model->update($data,$rush_id);
        $product_id_r = $this->rush_model->all_product(array('rush_id' => $rush_id));
        $r_a = array();
        foreach($product_id_r as $item){
            $r_a[] = $item->product_id;
        }
        $this->rush_model->pro_rush_update($data['start_date'],$data['end_date'],$r_a);
        // 上传图片
        $this->load->library('upload');
        $update = array();
        //$base_path = APPPATH.'../public/upload/rush/';
        $sub_dir = ($rush_id-$rush_id%100)/100;
        if(!file_exists(UPLOAD_PATH_RUSH.UPLOAD_TAG_RUSH.$sub_dir)) mkdir(UPLOAD_PATH_RUSH.UPLOAD_TAG_RUSH.$sub_dir, 0777, true);

        $this->upload->initialize(array(
                'upload_path' => UPLOAD_PATH_RUSH.UPLOAD_TAG_RUSH.$sub_dir,
                'allowed_types' => 'gif|jpg|png',
                'encrypt_name' => TRUE
        ));
        if($this->upload->do_upload('image_before_url')){
                $file = $this->upload->data();
                //if($rush->image_before_url) @unlink($base_path.$rush->image_before_url);
                if($rush->image_before_url) $this->unlink_rush_logo(UPLOAD_PATH_RUSH.UPLOAD_TAG_RUSH.$rush->image_before_url);
                $update['image_before_url'] = UPLOAD_TAG_RUSH.$sub_dir.'/'.$file['file_name'];
                $this->process_rush_logo(UPLOAD_PATH_RUSH.UPLOAD_TAG_RUSH.$sub_dir.'/'.$file['file_name']);
        }
        if($this->upload->do_upload('image_ing_url')){
                $file = $this->upload->data();
                if($rush->image_ing_url) @unlink(UPLOAD_PATH_RUSH.UPLOAD_TAG_RUSH.$rush->image_ing_url);
                $update['image_ing_url'] = UPLOAD_TAG_RUSH.$sub_dir.'/'.$file['file_name'];
        }
        if (!empty($update)) {
                $this->rush_model->update($update, $rush_id);
        }
        sys_msg('操作成功',2,array(array('href'=>'rush/index','text'=>'返回列表页')));
    }

    public function link_search()
    {
        $this->load->model('product_model');
        $filter = array();
        $product_sn = trim($this->input->post('product_sn'));
        if ($product_sn) $filter['product_sn'] = $product_sn;

        $product_name = trim($this->input->post('product_name'));
        if ($product_name) $filter['product_name'] = $product_name;

        $brand = trim($this->input->post('brand'));
        if ($brand) $filter['brand'] = $brand;

        $provider_productcode = trim($this->input->post('provider_productcode'));
        if ($provider_productcode) $filter['provider_productcode'] = $provider_productcode;
        
        $provider_code = trim($this->input->post('provider_code'));
        if ($provider_code) $filter['provider_code'] = $provider_code;

        $category_id = intval($this->input->post('category_id'));
        if ($category_id) $filter['category_id'] = $category_id;

        $style_id = intval($this->input->post('style_id'));
        if ($style_id) $filter['style_id'] = $style_id;

        $season_id = intval($this->input->post('season_id'));
        if ($season_id) $filter['season_id'] = $season_id;

        $product_sex = intval($this->input->post('product_sex'));
        if ($product_sex) $filter['product_sex'] = $product_sex;

        $batch_code = trim($this->input->post('batch_code'));
        if ($batch_code) $filter['batch_code'] = $batch_code;

        $depot_id = intval($this->input->post('depot_id'));
        if ($depot_id) $filter['depot_id'] = $depot_id;
	
        $percent = floatval($this->input->post('percent'));
        if(empty($percent)){$percent = 0;}
        $filter['percent'] = $percent;
        
        $filter = get_pager_param($filter);
        $data = $this->rush_model->link_rush_search($filter);
        
        foreach($data['list'] as $key => $val){
            $data['list'][$key]->percent_price = round($val->shop_price * $percent , 0);
        }

        $this->load->helper('product_helper');
        attach_sub($data['list']);
        attach_gallery($data['list']);
//        foreach($data['list'] as $key => $val){
//            if($val->sub_total != -2 && $val->sub_total == 0){
//                unset($data['list'][$key]);
//            }
//        }
        if ($this->input->is_ajax_request())
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('rush/link_search', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }
        $data['full_page'] = TRUE;
        $this->load->view('rush/link_search', $data);
    }

    /**
     * 2013-03-30 补充逻辑：当限抢正在上架售卖中时，添加限抢商品时自动上架对应商品，前台可直接售卖。
     * 移除限抢商品时，自动下架对应商品。
     * @return type
     */
    function add_rush_product(){
        auth('rush_product_edit');
        $sel_product_checkbox_price = $this->input->post('value');
        $rush_id = $this->input->post('rush_id');
        $arr_rush = $this->rush_model->filter(array('rush_id' => $rush_id));
        if($arr_rush->status == 2 || $arr_rush->status == 3){
            sys_msg('限抢状态不正确，无法添加抢购商品',1);
            return;
        }
        $arr = array();
        $res = array();
        $arr = explode(',' , $sel_product_checkbox_price);
        foreach($arr as $item){
            $res[] = explode('-' , $item);
        }

        $pro_id_arr = array();
        foreach($res as $item){
            $pro_id_arr[] = $item[0];
        }
        $sel_pro_df = $this->rush_model->sel_pro_df($pro_id_arr);
        $arr_p = array();
        foreach($res as $item){
            $arr_p[$item[0]] = $item;
        }
        foreach($sel_pro_df as $item){
            unset($arr_p[$item->product_id]);
        }
	if(count($arr_p) == 0 ){
	    echo json_encode(array('type' => 1,'msg' => '没有可以加入限抢的商品'));
            exit;
	}
        foreach($arr_p as $item){
            if(empty($item[1])){
                echo json_encode(array('type' => 1,'msg' => ''));
                exit;
            }
            if(!preg_match('/^[1-9]{1}[0-9]{0,7}$/', $item[1])){
                if(!preg_match('/^[0-9]{0,8}\.[0-9]{1,2}$/',$item[1])){
                        echo json_encode(array('type' => 1,'msg' => ''));
                        exit;
                }
            }
        }
        $filter_rush = $this->rush_model->filter(array('rush_id' => $rush_id));
	$activation = FALSE;
	if($filter_rush->status == 1 && $filter_rush->start_date<$this->time && $filter_rush->end_date>$this->time){
	    $activation = TRUE;
	}
        $this->load->model('product_model');
	$success = 0;
	$fail = 0;
        foreach($arr_p as $item){
	    $this->db->trans_begin();
            $this->rush_model->insert_product(array('rush_id'=>$rush_id , 'product_id' => $item[0] , 'price' => $item[1] , 'sort_order'=> $filter_rush->sort_order , 'category_id' => $item[2] , 'create_admin' => $this->admin_id , 'create_date' => date('Y-m-d H:i:s' , time())));
	    if($activation){
		    $status = $this->rush_sale_product($item[0] , "ON_SALE",$rush_id);
		    if($status["status"] == "ALL_OFF_SALE"){
			$this->db->trans_rollback();
			$fail += 1;
			continue;
		    }
	    }
	    $this->product_model->update(array('promote_price' => $item[1] , 'promote_start_date' => $filter_rush->start_date , 'promote_end_date' => $filter_rush->end_date , 'is_promote' => 1) , $item[0]);
	    $this->db->trans_commit();
	    $success += 1;
        }
        echo json_encode(array('type' => 2,'msg' => '处理完成，总'.count($arr_p).'共笔，成功'.$success.'笔，失败'.$fail.'笔'));
    }
    
    public function rush_sale_product($product_id,$type,$rush_id){
	    $sub_list = $this->product_model->all_sub(array('product_id'=>$product_id));
	    $sub_fail = 0;
	    $sub_all = count($sub_list);
	    foreach ($sub_list as $row){
		if((!$row->is_on_sale && $type == 'OFF_SALE')|| ($row->is_on_sale && $type == 'ON_SALE')){
		    continue;
		}
		if($type == 'ON_SALE'){
		    $gallery_list = $this->product_model->all_gallery(array('product_id'=>$row->product_id,'color_id'=>$row->color_id));
		    $gallery_list = get_pair($gallery_list,'image_type','image_id');
		    if(!isset($gallery_list['default']) || !isset($gallery_list['part'])){
			$sub_fail += 1;
			continue;
		    }
		}
		$this->product_model->update_sub(array('is_on_sale'=>$type == 'ON_SALE'?1:0),$row->sub_id);
		$this->product_model->insert_onsale_record(array('sub_id'=>$row->sub_id,'sr_onsale'=>$type == 'ON_SALE'?1:0,'create_admin'=>$this->admin_id,'create_date'=>date('Y-m-d H:i:s'),"onsale_memo"=>"限抢手工上下架，rushID=".$rush_id));
	    }
	    if($type == 'ON_SALE'){
		if($sub_fail == 0){
		    return array("type"=>$type,"product_id"=>$product_id,"status"=>"ALL_ON_SALE","result"=>"SUCCESS");
		}elseif($sub_fail == $sub_all){
		    return array("type"=>$type,"product_id"=>$product_id,"status"=>"ALL_OFF_SALE","result"=>"FAIL");
		}else{
		    return array("type"=>$type,"product_id"=>$product_id,"status"=>"PART_ON_SALE","result"=>"SUCCESS");
		}
	    }else{
		return array("type"=>$type,"product_id"=>$product_id,"status"=>"ALL_OFF_SALE","result"=>"FAIL");
	    }
	  
    }



    public function remove_link(){
        auth('rush_product_edit');
        $rec_id = intval($this->input->post('value'));
        $rec = $this->rush_model->filter_product(array('rec_id' => $rec_id));
        if(empty($rec)){
            echo json_encode(array('type' => 1,'msg' => ''));
            exit;
        }
	$this->db->trans_begin();
        $this->load->model('product_model');
	$filter_rush = $this->rush_model->filter(array('rush_id' => $rec ->rush_id));
	if($filter_rush->status == 1 && $filter_rush->start_date<$this->time && $filter_rush->end_date>$this->time){
	     $status = $this->rush_sale_product($rec->product_id , "OFF_SALE", $rec ->rush_id);
	    if($status["status"] != "ALL_OFF_SALE"){
		$this->db->trans_rollback();
		echo json_encode(array('type' => 2,'msg' => ''));
		return;
	    }
	}
        $this->product_model->update(array('promote_price' => 0 , 'promote_start_date'=>0 , 'promote_end_date' => 0 , 'is_promote' => 0) , $rec->product_id);
        $this->rush_model->delete_product($rec_id);
	$this->db->trans_commit();
        if($rec->image_before_url) @unlink(UPLOAD_PATH_RUSH_PRODUCT.UPLOAD_TAG_RUSH_PRODUCT.$rec->image_before_url);
        if($rec->image_ing_url) @unlink(UPLOAD_PATH_RUSH_PRODUCT.UPLOAD_TAG_RUSH_PRODUCT.$rec->image_ing_url);
        echo json_encode(array('type' => 3,'msg' => ''));
    }

    public function del($rush_id){
        auth('rush_audit');
        $rush_id = intval($rush_id);
        $test = $this->input->post('test');
        $check = $this->rush_model->filter(array('rush_id' => $rush_id));
        if(empty ($check)) sys_msg('记录不存在',1);
        if($check->status != 0) sys_msg('无法删除',1);
        if($check->end_date<$this->time) sys_msg('已过期不能删除',1);

        if($test) sys_msg('');
        $this->db->trans_begin();
        if($check->image_before_url) $this->unlink_rush_logo(UPLOAD_PATH_RUSH.UPLOAD_TAG_RUSH.$check->image_before_url);
		if($check->image_ing_url) @unlink(UPLOAD_PATH_RUSH.UPLOAD_TAG_RUSH.$check->image_ing_url);
        $this->rush_model->delete(array('rush_id' => $rush_id));
        $product_id_r = $this->rush_model->all_product(array('rush_id' => $rush_id));
        $r_a = array();
        foreach($product_id_r as $rec){
           $r_a[] = $rec->product_id;
           if($rec->image_before_url) @unlink(UPLOAD_PATH_RUSH_PRODUCT.UPLOAD_TAG_RUSH_PRODUCT.$rec->image_before_url);
            if($rec->image_ing_url) @unlink(UPLOAD_PATH_RUSH_PRODUCT.UPLOAD_TAG_RUSH_PRODUCT.$rec->image_ing_url);
        }
        $this->rush_model->ru_update($r_a);
        $this->rush_model->delete_product_where(array('rush_id' => $rush_id));
        $this->db->trans_commit();
        sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>base_url().'rush/index')));
    }

    public function act(){
        auth('rush_audit');
	$this->load->model("product_model");
        $rush_id = intval($this->input->post('rush_id'));
        $check = $this->rush_model->filter(array('rush_id' => $rush_id));
        if(empty ($check)){
            echo json_encode(array('type' => 1,'msg' => ''));
            exit;
        }
        if($check->end_date<$this->time) sys_msg('活动已过期，不能操作',1);
        if($check->status == 3){
            echo json_encode(array('type' => 3,'msg' => ''));
            exit;
        }
        $data['status'] = $check->status + 1;
        if($data['status'] == 1){
            $data['audit_admin'] = $this->admin_id;
            $data['audit_date'] = date('Y-m-d H:i:s' , time());
        }
         if($data['status'] == 2){
            $data['stop_admin'] = $this->admin_id;
            $data['stop_date'] = date('Y-m-d H:i:s' , time());
        }
        $data['modify_admin'] = $this->admin_id;
        $data['modify_date'] = date('Y-m-d H:i:s' , time());
        $this->rush_model->update($data , $rush_id);
	
        if($data['status'] == 2){
            $product_id_r = $this->rush_model->all_product(array('rush_id' => $rush_id));
            $r_a = array();
            foreach($product_id_r as $item){
                $r_a[] = $item->product_id;
            }
            $this->rush_model->ru_update($r_a);
	    //rush已激活了[status->1]，更新商品信息:下架
	    $onsale_pro = array("rush_id"=>$rush_id, "promote_start_date"=>'',"promote_end_date"=>'',"is_onsale"=>1 ,"is_on_sale"=>0);//product_info
	    $this->product_model->is_onsale_pro($onsale_pro );//商品上下架
        }
        echo json_encode(array('type' => 2 , 'status' => $check->status+1,'msg' => ''));
    }

    public function upload_image()
    {
        auth('rush_product_edit');
        $this->load->library('upload');
        $rec_id = intval($this->input->post('rec_id'));
        $rec = $this->rush_model->filter_product(array('rec_id'=>$rec_id));
        if(!$rec) sys_msg('记录不存在',1);
        // 上传图片
        //$base_path = APPPATH.'../public/upload/rush_product/';
        $sub_dir = ($rec_id-$rec_id%100)/100;
        if(!file_exists(UPLOAD_PATH_RUSH_PRODUCT.UPLOAD_TAG_RUSH_PRODUCT.$sub_dir)) mkdir(UPLOAD_PATH_RUSH_PRODUCT.UPLOAD_TAG_RUSH_PRODUCT.$sub_dir, 0777, true);
        $this->upload->initialize(array(
            'upload_path' => UPLOAD_PATH_RUSH_PRODUCT.UPLOAD_TAG_RUSH_PRODUCT.$sub_dir,
            'allowed_types' => 'gif|jpg|png',
            'encrypt_name' => TRUE
        ));
        $update = array();
        if ( $this->upload->do_upload('image_before_url') )
        {
            $file = $this->upload->data();
            if($rec->image_before_url) @unlink(UPLOAD_PATH_RUSH_PRODUCT.UPLOAD_TAG_RUSH_PRODUCT.$rec->image_before_url);
            $update['image_before_url'] = UPLOAD_TAG_RUSH_PRODUCT.$sub_dir.'/'.$file['file_name'];
        }else{
                print $this->upload->display_errors('<p>', '</p>');
        }
        if ( $this->upload->do_upload('image_ing_url') )
        {
            $file = $this->upload->data();
            if($rec->image_ing_url) @unlink(UPLOAD_PATH_RUSH_PRODUCT.UPLOAD_TAG_RUSH_PRODUCT.$rec->image_ing_url);
            $update['image_ing_url'] = UPLOAD_TAG_RUSH_PRODUCT.$sub_dir.'/'.$file['file_name'];
        }
        else{
                print $this->upload->display_errors('<p>', '</p>');
        }
        if($update) $this->rush_model->update_product($update,$rec_id);
        sys_msg('操作成功', 0, array(array('text'=>'返回','href'=>'rush/edit/'.$rec->rush_id.'?tab=1')));
    }

    public function edit_product_field()
    {
        auth('rush_product_edit');
        $placeholder='';
        switch (trim($this->input->post('field'))) {
            case 'sort_order':
                $val = intval($this->input->post('val'));
                break;
            case 'desc':
                $placeholder='点击填写';
                $val = trim($this->input->post('val'));              
                break;
            default:
                $val = NULL;
                break;
        }
        print(json_encode(proc_edit('rush_model', 'rec_id', array('sort_order','desc'), $val,'filter_product','update_product',$placeholder)));
    }
    
    public function  set_properties(){
	    auth('rush_set_properties');
	    $rush_id = intval($this->input->post('rush_id'));
	    $check = $this->rush_model->filter(array('rush_id' => $rush_id));
	    if(empty($check)){
		sys_msg('记录不存在', 1);
		return;
	    }
	    $value["content"] = $this->load->view('rush/set_properties',array("rush"=>$check),TRUE);
	    $value['error'] = 0;
	    echo json_encode($value);
    }
    
    public function proc_set_properties(){
	   auth('rush_set_properties');
	    $rush_id = intval($this->input->post('rush_id'));
	    $rush = $this->rush_model->filter(array('rush_id' => $rush_id));
	    if(empty($rush)){
		sys_msg('记录不存在', 1);
		return;
	    }
	    $update = array();
	    $update["rush_tag"] = $this->input->post('rush_tag');
//	    $update["rush_index"] = $this->input->post('rush_index');
	    $update["desc"] = $this->input->post('desc');
//	    $update["rush_brand"] = $this->input->post('rush_brand');
//	    $update["rush_category"] = $this->input->post('rush_category');
//	    $update["rush_discount"] = $this->input->post('rush_discount');
	    $update["rush_prompt"] = $this->input->post('rush_prompt');
	    $this->vali_length($update["rush_tag"], 12 , '限抢标签');
//	    $this->vali_length($update["rush_index"], 26 , '限抢名称');
	    $this->vali_length($update["desc"], 30 , '限抢描述');
//	    $this->vali_length($update["rush_brand"], 16 , '限抢品牌');
//	    $this->vali_length($update["rush_category"], 20 , '限抢分类');
//	    $this->vali_length($update["rush_discount"], 4 , '限抢折扣');
	    $this->vali_length($update["rush_prompt"], 30 , '限抢简介');
	    
	    $this->rush_model->update($update,$rush_id);
	    print json_encode(array('err'=>0,'msg'=>'','data'=>array()));
        }
	
	public function vali_length($str , $length , $desc){
	    $this ->load->helper("str");
	    $tag_len = my_strlen($str);
	    if($tag_len > $length){
		sys_msg('【'.$desc.'】长度过长,最大只能输入【'. $length .'】个字符。（1个汉字=2个字符，1个字母=1个字符）', 1);
	    }
	}
	
	public function sort_view($rush_id){
	    auth('rush_edit');
	    $rush = $this->rush_model->filter(array('rush_id' => $rush_id));
	    $link_product = $this->rush_model->rush_product_list($rush_id);
	    //query product gallery
	    $product_ids = array();
	    foreach ($link_product as $prodcut){
		$product_ids[] = $prodcut->product_id;
	    }
	    $this->load->model('product_model');
	    $all_gallery =$this->product_model->all_gallery(array(),$product_ids);
	    foreach ($link_product as $prodcut){
		if(!isset($prodcut->gallery))
		    foreach ($all_gallery as $gallery){
			if($gallery->product_id == $prodcut->product_id && $gallery->image_type=='default'){
			    if(!empty($gallery->img_318_318)){
				$prodcut->gallery =$gallery;
				break;
			    }
			}
		    }
	    }
	    
	    $this->load->vars('rush' , $rush);
	    $this->load->vars('link_product' , $link_product);
	    $this->load->view('rush/sort_product');
	}
	public function sort_view_85($rush_id){
	    auth('rush_edit');
	    $rush = $this->rush_model->filter(array('rush_id' => $rush_id));
	    $link_product = $this->rush_model->rush_product_list($rush_id);
	    //query product gallery
	    $product_ids = array();
	    foreach ($link_product as $prodcut){
		$product_ids[] = $prodcut->product_id;
	    }
	    $this->load->model('product_model');
	    $all_gallery =$this->product_model->all_gallery(array(),$product_ids);
	    foreach ($link_product as $prodcut){
		if(!isset($prodcut->gallery))
		    foreach ($all_gallery as $gallery){
			if($gallery->product_id == $prodcut->product_id && $gallery->image_type=='default'){
			    if(!empty($gallery->img_318_318)){
				$prodcut->gallery =$gallery;
				break;
			    }
			}
		    }
	    }
	    
	    $this->load->vars('rush' , $rush);
	    $this->load->vars('link_product' , $link_product);
	    $this->load->view('rush/sort_product_85');
	}
	
	public function proc_sort(){
	    auth('rush_edit');
	    $rec_ids = $this->input->post('rec_ids');
	    if(empty($rec_ids)){
		 sys_msg("无法读取商品信息",1);
	    }
	    $rec_ids_array = explode(',', $rec_ids);
	    $rec_array = array();
	    foreach ($rec_ids_array as $rec_id_str){
		$rec_id_array = explode('_', $rec_id_str);
		if(is_array($rec_id_array) && count($rec_id_array) >= 2){
		    $rec_array[] = array("rec_id"=>$rec_id_array[0],"sort"=>$rec_id_array[1]);
		}
	    }
	    foreach ($rec_array as $rec){
		$this->rush_model->update_product(array("sort_order"=>$rec["sort"]),$rec["rec_id"]);
	    }
	    echo json_encode(array('err'=>0,'msg'=>'','data'=>array()));
	    return ;
	}
	
	/**
	 * 排序指定日期开始的限抢
	 * @param type $start_time 限抢开始时间
	 */
	public function sort_rush_view($start_time){
	    auth('rush_edit');
	    $filter = array();
	    $filter['start_time'] = $start_time;
	    $filter['sort_by'] = "sort_order";
	    $filter['sort_order'] = "ASC";
	    $data = $this->rush_model->all_filter($filter);
	    $this->load->vars('start_time' , $start_time);
	    $this->load->vars('rush_list' , $data);
	    $this->load->view('rush/sort_rush');
	}
	
	public function proc_sort_rush(){
	    auth('rush_edit');
	    $rec_ids = $this->input->post('rec_ids');
	    if(empty($rec_ids)){
		 sys_msg("无法读取限抢信息",1);
	    }
	    $rec_ids_array = explode(',', $rec_ids);
	    $rec_array = array();
	    foreach ($rec_ids_array as $rec_id_str){
		$rec_id_array = explode('_', $rec_id_str);
		if(is_array($rec_id_array) && count($rec_id_array) >= 2){
		    $rec_array[] = array("rec_id"=>$rec_id_array[0],"sort"=>$rec_id_array[1]);
		}
	    }
	    foreach ($rec_array as $rec){
		$this->rush_model->update(array("sort_order"=>$rec["sort"]),$rec["rec_id"]);
	    }
	    echo json_encode(array('err'=>0,'msg'=>'','data'=>array()));
	    return ;
	}
	
	function sale(){
	    $rush_id = intval($this->input->post('rush_id'));
	    $sale = $this->input->post('sale');
	    if($rush_id <1 || ($sale!= 1 && $sale !=0)){
		echo json_encode(array("msg"=>"获取系统值错误，请联系管理员！"));
		return;
	    }
	    if($sale == 1){
		$this->rush_model->onsale_rush_on($rush_id);
	    }else{
		$this->rush_model->onsale_rush_off($rush_id);	
	    }
	    echo json_encode(array("msg"=>"操作完成"));
	}
        
        function importProducts() {
            auth('rush_product_edit');
            
            $rush_id = intval($this->input->post('rush_id'));
            // 上传文件验证
            $this->load->library('upload');
            if (!$_FILES["data_file"]["name"]) {
                sys_msg("请选择要上传的文件", 1);
            }
            if($_FILES["data_file"]["type"] != 'text/xml') {
                sys_msg("请上传XML格式的文件", 1);
            }

            $rush = $this->rush_model->filter(array('rush_id'=>$rush_id));
            if (!$rush) {
                sys_msg('限抢不存在！', 1);
            }
            if ($rush->status > 1) {
                sys_msg("限抢已停止/已结束，不能再导入商品！", 1);
            }

            // 获取excel中商品sn
            $this->load->helpers('excelxml');
            $product_sn_ary = read_xml($_FILES["data_file"]["tmp_name"]);
            if (count($product_sn_ary) <= 0) {
                sys_msg('文件中无要导入的商品款号！', 1);
            }
            
            // 排重
            $product_sns = array();
            foreach ($product_sn_ary as $value) {
                $contains = false;
                foreach ($product_sns as $sn) {
                    if ($value == $sn) {
                        $contains = true;
                        break;
                    }
                }
                if ($contains == false) {
                    $product_sns[] = $value;
                }
            }
            
            // 分类
            $unExistedAry = array();
            $giftsAry = array();
            $rushingAry = array();
            $succeedAry = array();
            $insert_ary = array();
            $this->load->model('product_model');
            foreach ($product_sns as $key => $value) {
                // 判断商品是否存在，是否已添加到限抢中
                $product_sn = $value[0];
                $product = $this->product_model->filter(array('product_sn' => $product_sn));
                if (empty($product)) {
                    $unExistedAry[] = $product_sn;
                } else {
                    if ($this->is_gifts($product)) {
                        $giftsAry[] = $product_sn;
                    } else if ($this->is_rushing($product->product_id)) {
                        $rushingAry[] = $product_sn;
                    } else {
                        $succeedAry[] = $product_sn;
                        $insert_ary[] = $product;
                    }
                }
            }
            
            // 插入可添加的商品
            if (count($insert_ary) > 0) {
                $activation = FALSE;
                if($rush->status == 1 && $rush->start_date < $this->time && $rush->end_date > $this->time){
                    $activation = TRUE;
                }
                
                $success = 0;
                $fail = 0;
                $now_time = $this->time;
                $this->load->model('product_model');
                foreach ($insert_ary as $key => $product) {
                    $product_id = $product->product_id;
                    $category_id = $product->category_id;
                    $promote_price = $product->shop_price;

                    $this->db->trans_begin();
                    $this->rush_model->insert_product(array('rush_id'=>$rush_id, 'product_id' => $product_id, 'price' => $promote_price, 'sort_order'=> $key, 'category_id' => $category_id, 'create_admin' => $this->admin_id , 'create_date' => $now_time));
                    // 如果活动正在进行，新添加的商品自动上架可售
                    if($activation){
                        $status = $this->rush_sale_product($product_id, "ON_SALE", $rush_id);
                        if($status["status"] == "ALL_OFF_SALE"){
                            $this->db->trans_rollback();
                            $fail += 1;
                            continue;
                        }
                    }
                    $this->product_model->update(array('promote_price' => $promote_price, 'promote_start_date' => $rush->start_date, 'promote_end_date' => $rush->end_date, 'is_promote' => 1) , $product_id);
                    $this->db->trans_commit();
                    $success += 1;
                }
            }
            
            sys_msg('导入成功', 0, array(array('text'=>'继续编辑','href'=>'rush/edit/'.$rush_id.'?tab=1')));
        }
        
        private function is_gifts($product) {
            if (empty($product)) {
                return false;
            } else if ($product->is_promote == 1) {
                return intval($product->promote_price) == 0;
            } else {
                return intval($product->shop_price) == 0;
            }
        }
        
        private function is_rushing($produt_id) {
            if (empty($produt_id)) {
                return false;
            }
            $row = $this->rush_model->is_rushing_product($produt_id);
            return empty($row) ? false : true;
        }
        
}
