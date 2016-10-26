<?php
#doc
#	classname:	Product
#	scope:		PUBLIC
#
#/doc

class Course extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		$this->time = date('Y-m-d H:i:s');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('course_model');
		$this->load->model('product_model');
		$this->load->model('brand_model');
		$this->load->model('category_model');
		$this->load->model('carelabel_model');
		$this->load->model('flag_model');
		$this->load->model('model_model');
		$this->load->model('admin_model');
		$this->load->model('provider_model');
		$this->load->model('season_model');
		$this->load->model('tmall_model');
		$this->load->model('purchase_batch_model');
		$this->load->model('register_model');
		$this->load->model('order_source_model');
		$this->load->model('style_model');
		$this->load->model('color_model');
		$this->load->model('size_model');
		$this->load->model('product_genre_model');
		$this->load->model('depot_model');
		$this->load->model("purchase_model");

		$this->load->helper('category');
		$this->load->helper('product');

		$this->load->library('upload');
		$this->load->library('ckeditor');

		$this->config->load('product');
	}
	
	public function index ()
	{
		auth('course_view');
		$filter = $this->uri->uri_to_assoc(3);
		$filter['product_id'] = trim($this->input->post('product_id'));
		$filter['product_sn'] = trim($this->input->post('product_sn'));
		$filter['product_name'] = trim($this->input->post('product_name'));
		$filter['provider_productcode'] = trim($this->input->post('provider_productcode'));
		$filter['category_id'] = intval($this->input->post('category_id'));
		$filter['product_status'] = trim($this->input->post('product_status'));
		$filter['is_on_sale'] = trim($this->input->post('is_on_sale'));
        $filter['source_id'] = trim($this->input->post('source_id'));

		$filter = get_pager_param($filter);
		$data = $this->course_model->product_list($filter);
		attach_sub($data['list']);
        attach_tmall_num_iid($data['list']);
		$this->load->vars('perm_delete', check_perm('course_edit'));
		$this->load->vars('pro_cost_price',check_perm('cost_price_check'));
		$this->load->vars('keycode',get_ghost());
		$this->load->vars('all_age',$this->config->item('product_age'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('course/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;

		$this->load->vars('all_category',category_flatten(category_tree($this->category_model->all_category(array('genre_id' => 2))),'-- '));
        $this->load->vars('all_source', $this->order_source_model->all_source(array('is_use' => 1)));
        
		$this->load->view('course/index', $data);
	}

	public function add()
	{
		auth('course_edit');    
		$this->load->vars('all_admin', $this->admin_model->all_admin(array('user_status' => 1)));
		$this->load->vars('all_provider_coop', $this->provider_model->all_provider_coop());
		$this->load->vars('all_category',category_tree($this->category_model->all_category(array('genre_id' => 2))));
        $this->load->vars('all_source', $this->order_source_model->all_source(array('is_use' => 1)));
        $this->load->vars('self_shop',SELF_SHOP);
       
		$this->load->view('course/add');
	}
	public function proc_add()
	{
		auth('course_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('product_name', '课程名称', 'trim|required');
		$this->form_validation->set_rules('unit_name', '单位', 'trim|required');
		$this->form_validation->set_rules('shop_price', '本站价', 'trim|required');
		$this->form_validation->set_rules('market_price', '市场价', 'trim|required');	
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}

		$now = date('Y-m-d H:i:s');
		$update = array();
		
		//商品大类
		$update['genre_id'] = GENRE_COURSE_ID;
		//注册证号
		$update["register_code_id"] = DEFAULT_REGISTER_NO;
		//品牌
		$update['brand_id'] = DEFAULT_BRAND_ID;
		$brand_name = $this->brand_model->all_brand($filter = array(),$update['brand_id']);
		$update['brand_name'] = $brand_name[0]->brand_name;
		//国旗 中国
		$update['flag_id'] = 4;

		//平台类型
        $update['source_id'] = intval($this->input->post('source_id'));
        //课程名称
		$update['product_name'] = $this->input->post('product_name');
		//后台品名
		$update['product_name_alias'] = trim($this->input->post('product_name_alias'));
		//运营专员
		$update['operator'] = trim($this->input->post('operator'));
		//商品内容来源
		$update["content_source"] = trim($this->input->post('content_source'));
		//可询价
		$update["price_show"] = trim($this->input->post('price_show'));
		//访问量
		$update["pv_num"] = trim($this->input->post('pv_num'));
		//销售量
		$update["ps_num"] = trim($this->input->post('ps_num'));
		//课程编号
		$update['product_sn'] = strtoupper($this->input->post('product_sn'));

		$update['is_best'] = intval($this->input->post('is_best'))?1:0;
		$update['is_hot'] = intval($this->input->post('is_hot'))?1:0;
		$update['is_promote'] = 0;
		$update['is_new'] = intval($this->input->post('is_new'))?1:0;
		$update['is_offcode'] = intval($this->input->post('is_offcode'))?1:0;
		$update['is_gifts'] = intval($this->input->post('is_gifts'))?1:0;
		
		$update['is_audit'] = 0;  //添加时不审核
		$update['is_onsale'] = 0; //添加时不上架
		$update['is_stop'] = intval($this->input->post('is_stop'))?1:0; // 课程状态
		$update['sort_order'] = intval($this->input->post('sort_order'));

		$update['package_name'] = $this->input->post('package_name');
		$update['subhead'] = $this->input->post('subhead');
		$update['category_id'] = intval($this->input->post('category_id'));
		//自营店铺
		$update["shop_id"] = intval($this->input->post('shop_id'));
		$update['provider_id'] = intval($this->input->post('shop_id'));

		$update['unit_name'] = trim($this->input->post('unit_name'));
		$update['shop_price'] = fix_price($this->input->post('shop_price'));
		$update['market_price'] = fix_price($this->input->post('market_price'));

		$update['keywords'] = trim($this->input->post('keywords'));
		$update['product_desc'] = trim($this->input->post('product_desc'));
		$update['product_desc_detail'] = trim($this->input->post('product_desc_detail'));
		// 新增逻辑，主要针对课程修改
		$update['detail1'] = trim($this->input->post('detail1') ? $this->input->post('detail1') : '');
		$update['detail2'] = trim($this->input->post('detail2') ? $this->input->post('detail2') : '');
		$update['detail3'] = trim($this->input->post('detail3') ? $this->input->post('detail3') : '');
		$update['detail4'] = trim($this->input->post('detail4') ? $this->input->post('detail4') : '');
		$update['detail5'] = trim($this->input->post('detail5') ? $this->input->post('detail5') : '');

		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = $now;
		$update['stop_admin'] = $update['is_stop']?$this->admin_id:0;
		$update['stop_date'] = $update['is_stop']?$now:'';
                
		$desc_array = array();
		$desc_array["desc_composition"] = $this->input->post('desc_composition');
		$desc_array["desc_dimensions"] = $this->input->post('desc_dimensions');
		$desc_array["desc_material"] = $this->input->post('desc_material');
		$desc_array["desc_waterproof"] = $this->input->post('desc_waterproof');
		$desc_array["desc_crowd"] = $this->input->post('desc_crowd');
		$desc_array["desc_expected_shipping_date"] = $this->input->post('desc_expected_shipping_date');

		$update['desc_crowd_val'] = $desc_array["desc_crowd"];
		$update["product_desc_additional"] = json_encode($desc_array);

        $cate_obj = $this->category_model->filter(array('category_id'=>$update['category_id']));
		if (!$cate_obj) {
			sys_msg('所选分类不存在');
		}
        
        $brand_obj = $this->brand_model->filter(array('brand_id'=>$update['brand_id']));
		if (!$brand_obj) {
			sys_msg('所选品牌不存在');
		}

        if (empty($update['product_sn'])){
            $update['product_sn'] = $this->course_model->gen_p_sn($brand_obj->brand_initial, $cate_obj->cate_code);
            if (empty($update['product_sn'])){
                sys_msg('没有可用课程编号，请联系技术部');
            }
        }

		$check_product = $this->course_model->filter(array('product_sn'=>$update['product_sn']));
		if ($check_product) {
			sys_msg('课程编号重复', 1);
		}

		$product_id = $this->course_model->insert($update);

		//调价记录
		log_product_price(array(), $update, $product_id);
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'course/edit/'.$product_id), array('text'=>'返回列表','href'=>'course/index')));
	}

	public function edit($product_id)
	{
		auth(array('course_edit','course_view'));
		$product = $this->course_model->filter(array('product_id'=>$product_id));
		if (!$product) {
			sys_msg('记录不存在', 1);
		}
		
		$product_desc_additional = $product ->product_desc_additional;
		if(!empty($product_desc_additional)){
		    $desc_array = json_decode($product_desc_additional, true);
		    $product->desc_composition = isset($desc_array["desc_composition"]) ? $desc_array["desc_composition"] : '';
            $product->desc_dimensions = isset($desc_array["desc_dimensions"]) ? $desc_array["desc_dimensions"] : '';
            $product->desc_material = isset($desc_array["desc_material"]) ? $desc_array["desc_material"] : '';
            $product->desc_waterproof = isset($desc_array["desc_waterproof"]) ? $desc_array["desc_waterproof"] : '';
            $product->desc_crowd = isset($desc_array["desc_crowd"]) ? $desc_array["desc_crowd"] : '';
            $product->desc_expected_shipping_date = isset($desc_array["desc_expected_shipping_date"]) ? $desc_array["desc_expected_shipping_date"] : '';
		}else{
		    $product -> desc_composition = "";
		    $product -> desc_dimensions = "";
		    $product -> desc_material = "";
		    $product -> desc_waterproof = "";
		    $product -> desc_crowd = "";
		    $product -> desc_notes = "";
		    $product -> desc_expected_shipping_date = "";
		}

		$this->load->vars('all_provider_coop', $this->provider_model->all_provider_coop());
		$this->load->vars('all_category',category_tree($this->category_model->all_category(array('genre_id' => 2))));
		$this->load->vars('all_color_group',$this->color_model->all_group());
		$this->load->vars('all_color',$this->color_model->all_color());
		$this->load->vars('all_size',$this->size_model->all_size());
        $this->load->vars('all_source', $this->order_source_model->all_source(array('is_use' => 1)));
        $this->load->vars('all_admin', $this->admin_model->all_admin(array('user_status' => 1)));

		// fetch the galleray and product_sub
		$all_gallery = $this->course_model->all_gallery(array('product_id' => $product_id));
		$all_sub = $this->course_model->all_sub(array('product_id' => $product_id));
		$this->load->vars('all_gallery_sub',format_gallery_sub($all_gallery, $all_sub));

		// fetch linked product
		$link_product = $this->course_model->link_product($product_id);
		$link_by_product = $this->course_model->link_by_product($product_id);
		attach_gallery($link_product); attach_sub($link_product);
		attach_gallery($link_by_product); attach_sub($link_by_product);

		$this->load->vars('link_product', $link_product);
		$this->load->vars('link_by_product', $link_by_product);

		$this->load->vars('row', $product);
		$this->load->view('course/edit');
	}

	public function proc_edit()
	{
		auth('course_edit');
		$product_id = intval($this->input->post('product_id'));
		$product = $this->course_model->filter(array('product_id'=>$product_id));
		if (!$product) {
			sys_msg('记录不存在!', 1);
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('product_name', '课程名称', 'trim|required');
		$this->form_validation->set_rules('shop_price', '本站价', 'trim|required');
		$this->form_validation->set_rules('market_price', '市场价', 'trim|required');	
		$this->form_validation->set_rules('unit_name', '单位', 'trim|required');

		if(!$product->is_audit){
			$this->form_validation->set_rules('unit_name', '计量单位', 'trim|required');
		}
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}

		$now = date('Y-m-d H:i:s');
		$update = array();
		if (!$product->is_audit) {
			$update['product_sn'] = strtoupper($this->input->post('product_sn'));
		}            
		$update['provider_productcode'] = $this->input->post('provider_productcode');
		$update['category_id'] = intval($this->input->post('category_id'));
		$update['unit_name'] = trim($this->input->post('unit_name'));
        $update['source_id'] = intval($this->input->post('source_id'));
		//商品内容来源
		$update["content_source"] = trim($this->input->post('content_source'));
		//可询价
		$update["price_show"] = trim($this->input->post('price_show'));
		//店铺
		$update["shop_id"] = trim($this->input->post('shop_id'));
		//访问量
		$update["pv_num"] = trim($this->input->post('pv_num'));
		//销售量
		$update["ps_num"] = trim($this->input->post('ps_num'));

		$update['operator'] = $this->input->post('operator');
		$update['package_name'] = $this->input->post('package_name');
		$update['subhead'] = $this->input->post('subhead');
		$update['product_name'] = $this->input->post('product_name');
		$update['is_best'] = intval($this->input->post('is_best'))?1:0;
		$update['is_hot'] = intval($this->input->post('is_hot'))?1:0;
		$update['is_new'] = intval($this->input->post('is_new'))?1:0;
		$update['is_offcode'] = intval($this->input->post('is_offcode'))?1:0;
		$update['is_gifts'] = intval($this->input->post('is_gifts'))?1:0;
		$update['is_stop'] = intval($this->input->post('is_stop'))?1:0;
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['shop_price'] = fix_price($this->input->post('shop_price'));
		$update['market_price'] = fix_price($this->input->post('market_price'));
	
		$update['keywords'] = trim($this->input->post('keywords'));
		$update['product_desc'] = trim($this->input->post('product_desc'));
		$update['product_desc_detail'] = trim($this->input->post('product_desc_detail'));
		// 新增逻辑，主要针对课程和产品的修改
		$update['detail1'] = trim($this->input->post('detail1') ? $this->input->post('detail1') : '');
		$update['detail2'] = trim($this->input->post('detail2') ? $this->input->post('detail2') : '');
		$update['detail3'] = trim($this->input->post('detail3') ? $this->input->post('detail3') : '');
		$update['detail4'] = trim($this->input->post('detail4') ? $this->input->post('detail4') : '');
		$update['detail5'] = trim($this->input->post('detail5') ? $this->input->post('detail5') : '');

		$update['stop_admin'] = $update['is_stop']?$this->admin_id:0;
		$update['stop_date'] = $update['is_stop']?$now:0;

		$desc_array = array();
		$desc_array["desc_composition"] = $this->input->post('desc_composition');
		$desc_array["desc_dimensions"] = $this->input->post('desc_dimensions');
		$desc_array["desc_material"] = $this->input->post('desc_material');
		$desc_array["desc_waterproof"] = $this->input->post('desc_waterproof');
		$desc_array["desc_crowd"] = $this->input->post('desc_crowd');
		$desc_array["desc_expected_shipping_date"] = $this->input->post('desc_expected_shipping_date');

        $update['desc_crowd_val'] = $desc_array["desc_crowd"];
		$update["product_desc_additional"] = json_encode($desc_array);
                
        $update['update_time'] = $now;
        if (!empty($update['category_id'])){
            $cate_obj = $this->category_model->filter(array('category_id'=>$update['category_id']));
            if (!$product->is_audit && !$cate_obj) {
                sys_msg('所选分类不存在');
            }
        }

        $this->product_model->update($update, $product_id);        
		//调价记录
		log_product_price($product, $update, $product_id);
		
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'course/edit/'.$product_id), array('text'=>'返回列表','href'=>'course/index')));
	}

	public function delete($product_id)
	{
		auth('course_edit');
		$product_fields = array_keys($this->config->item('product_fields'));
		$test = $this->input->post('test');
		// @todo:检查外键约束
		if(!$test){
			$this->db->trans_begin();
			$product = $this->course_model->lock_product($product_id);
		}else{
			$product = $this->course_model->filter(array('product_id'=>$product_id));
		}		
		
		if(!$product) sys_msg('记录不存在',1);
		if($product->is_audit) sys_msg('该课程已审核不能删除',1);

		if($test) sys_msg('', 0);

		$all_sub = $this->course_model->all_sub(array('product_id'=>$product_id));
		foreach ($all_sub as $sub) {
			$this->course_model->delete_sub($sub->sub_id);
		}
		$all_gallery = $this->course_model->all_gallery(array('product_id'=>$product_id));
		foreach ($all_gallery as $gallery) {
            if($gallery->img_url) @unlink('public/data/images/'.$gallery->img_url);
			foreach ($this->config->item('product_fields') as $field) {
				@unlink('public/data/images/'.$gallery->img_url.'.'.$field['width'].'x'.$field['height'].'.jpg');//去掉gallery/
			}
			$this->course_model->delete_gallery($gallery->image_id);
		}

		$this->course_model->delete($product_id);
        $this->tmall_model->delete_product($product_id);
		$this->db->trans_commit();
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'course/index')));
	}
	

	
	public function toggle()
	{
		$no_valid_ops = array('is_best','is_new','is_hot','is_offcode','is_gifts');
		$field = trim($this->input->post('field'));
		if (in_array($this->input->post('field'),$no_valid_ops)) {
			auth('course_edit');
			$result = proc_toggle('course_model','product_id',$no_valid_ops);
			print json_encode($result);
			return;
		}

		$id = intval($this->input->post('id'));
		$yes_exp = trim($this->input->post('yes_exp'));
		$no_exp = trim($this->input->post('no_exp'));
		if (in_array(substr($yes_exp,-4),array('.gif','.jpg','.png'))) $yes_exp = "<img src='public/images/{$yes_exp}' />";
		if (in_array(substr($no_exp,-4),array('.gif','.jpg','.png'))) $no_exp = "<img src='public/images/{$no_exp}' />";
		$row = $this->course_model->filter(array('product_id'=>$id));
		if(!$row) sys_msg('课程不存在',1);
		switch ($field) {
			case 'is_stop':
				auth('course_edit');
				if($row->is_stop){
					$this->course_model->update(array('is_stop'=>0,'stop_admin'=>0,'stop_date'=>'0000-00-00'),$id);
				}else{
					$this->course_model->update(array('is_stop'=>1,'stop_admin'=>$this->admin_id,'stop_date'=>date('Y-m-d H:i:s')),$id);
				}
				print json_encode(array('err'=>0, 'msg'=>'','content'=>$row->is_stop?$no_exp:$yes_exp));
				break;

			case 'is_audit':
				auth('course_audit');
				if($row->is_audit){
					sys_msg('课程审核后不能反审核',1);
				}else{
					$cs = $this->course_model->filter_sub(array('product_id'=>$id));
					if(!$cs) sys_msg('该课程没有录入颜色规格',1);
					$this->course_model->update(array('is_audit'=>1,'audit_admin'=>$this->admin_id,'audit_date'=>date('Y-m-d H:i:s')),$id);
				}
				print json_encode(array('err'=>0, 'msg'=>'','content'=>$row->is_audit?$no_exp:$yes_exp));
				break;
			
			default:
				sys_msg('参数错误',1);
		}
	}

	public function edit_field()
	{
		auth('course_edit');
		switch (trim($this->input->post('field'))) {
			case 'sort_order':
				$val = intval($this->input->post('val'));
				break;
			
			default:
				$val = NULL;
				break;
		}
		print(json_encode(proc_edit('course_model', 'product_id', array('sort_order'), $val)));
		return;
	}

	//按商品上下架
	public function toggle_product($v) {
	    $id = intval($this->input->post('id'));
		$field = trim($this->input->post('field'));
		$row = $this->course_model->filter_product_subs(array('product_id'=>$id, $field => !$v));
		if(empty($row)) sys_msg('记录不存在',1);
		$product = $this->course_model->filter(array('product_id'=>$id));
		if(!$product || !$product->is_audit) {
			sys_msg('课程未审核',1);
		} else if ($product->shop_price <= 0) {
			sys_msg('售价不大于0，不能上架',1);
		}
		
		switch ($field) {
			case 'is_on_sale':
				if($v){
				    auth('course_onsale');
				}else{
				    auth('course_offsale');
				}

				if ($v) {
					$gallery_list = $this->course_model->all_gallery(array('product_id'=>$id));
					$gallery_list = get_pair($gallery_list,'image_type','image_id');
					if(!isset($gallery_list['default'])) sys_msg('图片未上传', 1);
				}
				
				$this->course_model->update_where_sub(array('is_on_sale'=>$v),array('product_id' => $id, 'is_on_sale' => !$v));
				foreach ($row as $sub) {
				    $this->course_model->insert_onsale_record(array('sub_id'=>$sub->sub_id,'sr_onsale'=>$v,'create_admin'=>$this->admin_id,'create_date'=>date('Y-m-d H:i:s'),'onsale_memo'=>'商品列表手工操作'));
				}
				sys_msg('操作成功',0);
				break;
			default:
				sys_msg('参数错误',1);
		}		
		
	}
	
	public function toggle_sub()
	{
		$id = intval($this->input->post('id'));
		$field = trim($this->input->post('field'));
		$yes_exp = trim($this->input->post('yes_exp'));
		$no_exp = trim($this->input->post('no_exp'));
		if(empty($yes_exp)){
		   $yes_exp = " <span class='yesForGif' ></span>";
		}else{
		    $yes_exp = " <span>".$yes_exp."</span>";
		}
		if(empty($no_exp)){
		   $no_exp = " <span class='noForGif' ></span>";
		}else{
		   $no_exp = " <span>".$no_exp."</span>";
		}
		$row = $this->course_model->filter_sub(array('sub_id'=>$id));
		if(!$row) sys_msg('记录不存在',1);
		$product = $this->course_model->filter(array('product_id'=>$row->product_id));
                if(!$product || !$product->is_audit) {
                    sys_msg('商品未审核',1);
                } else if ($product->shop_price <= 0) {
                    sys_msg('售价不大于0，不能上架',1);
                }
		
		switch ($field) {
			case 'is_on_sale':
				if($row->is_on_sale){
				    auth('course_offsale');
				}else{
				    auth('course_onsale');
				}
				$is_on_sale = $row->is_on_sale ? 0 : 1;
				if ($is_on_sale) {
					$gallery_list = $this->course_model->all_gallery(array('product_id'=>$row->product_id,'color_id'=>$row->color_id));
					$gallery_list = get_pair($gallery_list,'image_type','image_id');
					if(!isset($gallery_list['default'])) sys_msg('图片未上传', 1);
				}
				$this->course_model->update_sub(array('is_on_sale'=>$is_on_sale),$id);
				$this->course_model->insert_onsale_record(array('sub_id'=>$id,'sr_onsale'=>$is_on_sale,'create_admin'=>$this->admin_id,'create_date'=>date('Y-m-d H:i:s'),'onsale_memo'=>'商品列表手工操作'));
				print json_encode(array('err'=>0, 'msg'=>'','content'=>$is_on_sale?$yes_exp:$no_exp));
				break;
			default:
				sys_msg('参数错误',1);
		}
	}

	public function edit_field_sub()
	{
		auth('course_consign');
		$id = intval($this->input->post('id'));
		$field = trim($this->input->post('field'));
		$val = trim($this->input->post('val'));
                
		$row = $this->course_model->filter_sub(array('sub_id'=>$id));
		if (!$row) {
			sys_msg('记录不存在',1);
		}

		switch (trim($this->input->post('field'))) {
			case 'consign_num':
				if($val=='不') $val= -1;
				if($val=='无限') $val= -2;
				$val = intval($val);
				if($val<-2) $val=0;
				$row = $this->course_model->filter_sub(array('sub_id'=>$id));
				if(empty($row)){
				    print json_encode(array('err'=>1,'msg'=>'数据不存在'));
				    break;
				}
				//校验是否存在实库批次，如存在则此次操作有误。
				$res = $this->course_model->query_product_cost(array("product_id"=>$row->product_id));
				$vali_b_flag = TRUE;
				foreach ($res as $rs){
				    $batch = $this->purchase_model->filter_purchase_batch(array("batch_id"=>$rs->batch_id));
				    if(!empty($batch)){
				       if($val !== -1 && $batch->batch_type == 0 && $batch->is_consign == 0 && $batch->is_reckoned == 0){
					   print json_encode(array('err'=>1,'msg'=>'课程不属于虚库批次，对应实库销售批次号为['.$batch->batch_code.']，不可导入虚库库存。'));
					   $vali_b_flag = FALSE;
					   break;
				       }
				    }
				}
				if(!$vali_b_flag)
				    break;
				$this->course_model->update_sub(array('consign_num'=>$val), $id);
				$row = $this->course_model->filter_sub(array('sub_id'=>$id));
				$content = $row->consign_num;
				if($content==-1) $content='不';
				if($content==-2) $content='无限';
				print json_encode(array('err'=>0,'msg'=>'','content'=>$content));
				break;
			
			default:
				sys_msg('参数错误',1);
		}
		return;
	}

	public function onsale_record_index()
	{
		auth('course_onsale_record');
		$this->load->helper('product');
		$filter = $this->uri->uri_to_assoc(3);
		$filter['product_sn'] = trim($this->input->post('product_sn'));
		$filter['category_id'] = intval($this->input->post('category_id'));
		$filter['brand_id'] = intval($this->input->post('brand_id'));
		$filter['create_admin'] = trim($this->input->post('create_admin'));
		$filter['start_date'] = trim($this->input->post('start_date'));
		$filter['end_date'] = trim($this->input->post('end_date'));
		if ($this->input->post('sr_onsale')!==FALSE) {
			$filter['sr_onsale'] = intval($this->input->post('sr_onsale'));
		}

		$filter = get_pager_param($filter);
		$data = $this->course_model->onsale_record_list($filter);
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('course/onsale_record_index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->model('brand_model');
		$this->load->model('category_model');
		$this->load->helper('category');

		$this->load->vars('all_brand', $this->brand_model->all_brand());
		$this->load->vars('all_category',category_flatten(category_tree($this->category_model->all_category()),'-- '));
		$this->load->view('course/onsale_record_index', $data);
	}

	public function price_record_index()
	{
		auth('course_price_record');
		$this->load->helper('product');
		$filter = $this->uri->uri_to_assoc(3);
		$filter['product_sn'] = trim($this->input->post('product_sn'));
		$filter['category_id'] = intval($this->input->post('category_id'));
		$filter['brand_id'] = intval($this->input->post('brand_id'));
		$filter['create_admin'] = trim($this->input->post('create_admin'));
		$filter['start_date'] = trim($this->input->post('start_date'));
		$filter['end_date'] = trim($this->input->post('end_date'));
		

		$filter = get_pager_param($filter);
		$data = $this->course_model->price_record_list($filter);
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('course/price_record_index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->model('brand_model');
		$this->load->model('category_model');
		$this->load->helper('category');

		$this->load->vars('all_brand', $this->brand_model->all_brand());
		$this->load->vars('all_category',category_flatten(category_tree($this->category_model->all_category()),'-- '));
		$this->load->view('course/price_record_index', $data);
	}

	public function export_purcahse_course()
	{
	    auth('export_purcahse_course');
	    $this->load->model('purchase_batch_model');
        $this->load->model('provider_model');
	    $filter = $this->uri->uri_to_assoc(3);
	    $filter['product_sn'] = trim($this->input->post('product_sn'));
	    $filter['product_name'] = trim($this->input->post('product_name'));
	    $filter['provider_productcode'] = trim($this->input->post('provider_productcode'));
	    $filter['category_id'] = intval($this->input->post('category_id'));
	    $filter['brand_id'] = intval($this->input->post('brand_id'));
	    $filter['style_id'] = intval($this->input->post('style_id'));
	    $filter['product_sex'] = intval($this->input->post('product_sex'));
	    $filter['season_id'] = intval($this->input->post('season_id'));
	    $filter['product_status'] = trim($this->input->post('product_status'));
	    $filter['provider_id'] = intval($this->input->post('provider_id'));
	    $filter['batch_code'] = trim($this->input->post('batch_code'));
	    $filter['is_pic'] = intval($this->input->post('is_pic'));

	    $purcahse_order_list = $this->course_model->purcahse_order_list($filter);
	    if(empty($purcahse_order_list)){
			sys_msg('没有需要导出的记录',1);
	    }
	    $exlval=array();
	    $val=array();
	    $title = array();
	    $is_audit = TRUE;
	    //Purchase_batch_model
	    $query_filter["batch_code"] = $filter['batch_code'];
	    $batch = $this->purchase_batch_model->filter($query_filter);
        $provider = $this->provider_model->filter(array('provider_id' => $batch->provider_id));
	    $title["batch_no"] =$batch ->batch_code;
	    $title['provider_code'] =$provider->provider_code;
	    $title['provider_cooperation'] =$provider->provider_cooperation;
	    foreach($purcahse_order_list as $key=>$value){
			//title
			if($key == 1){
			    $title['provider_code'] = $value["provider_code"];
			    $title['provider_cooperation'] = $value["provider_cooperation"];
			}
			//data
			$is_audit = $value['is_audit'];
			if(!$is_audit)
				break;
			$val['product_sn'] = $value['product_sn'];//商品款号
			$val['provider_code'] = $value['provider_code'];//供应商编码
			$val['provider_productcode'] = $value['provider_productcode'];//供应商货号
			$val['color_name'] = $value['color_name'];//颜色
			$val['color_sn'] = $value['color_sn'];//颜色编码
			$val['size_name'] = $value['size_name'];//尺寸
			$val['size_sn'] = $value['size_sn'];//尺寸编码
			$val['num'] = 0;//数量
			$exlval[] = $val;
	    }
	    if(!$is_audit)
		sys_msg('导出商品中存在未审核的商品，不允许导出',1);
	    $now = time();
	    $next_time = $now + 3600*24*7;
	    $title["now"] = date('Y-m-d',$now);
	    $title["next"] =date('Y-m-d',$next_time);
	    $title["desc"] = "";
	    $info[]=array('product_sn'=>'商品款号','provider_code'=>'供应商编码','provider_productcode'=>'供应商货号','color_name'=>'颜色','color_sn'=>'颜色编码','size_name'=>'尺寸','size_sn'=>'尺寸编码','num'=>'数量');
	    $head[] = array('batch_no'=>'批次号','provider_code'=>'供应商编码','provider_cooperation'=>'合作方式ID','now'=>'下单日期','next'=>'预期交货日期','desc'=>'备注');
	    $this->load->helper('excel');
	    export_excel_xml('purchase_order',array($head,$title,$info,$exlval));
	}
        
    public function price_record_search()
	{
		auth('course_cost_view');
		$this->load->helper('product');
		$filter = $this->uri->uri_to_assoc(3);
		$filter['product_sn'] = trim($this->input->post('product_sn'));
		$filter['batch_code'] = trim($this->input->post('batch_code'));
		$filter['category_id'] = intval($this->input->post('category_id'));
		$filter['brand_id'] = intval($this->input->post('brand_id'));
		$filter['create_admin'] = trim($this->input->post('create_admin'));
		$filter['start_date'] = trim($this->input->post('start_date'));
		$filter['end_date'] = trim($this->input->post('end_date'));
		

		$filter = get_pager_param($filter);
		$data = $this->course_model->product_cost_record_list($filter);
        
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('course/price_record_view', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->model('brand_model');
		$this->load->model('category_model');
		$this->load->helper('category');

		$this->load->vars('all_brand', $this->brand_model->all_brand());
		$this->load->vars('all_category',category_flatten(category_tree($this->category_model->all_category()),'-- '));
		$this->load->view('course/price_record_view', $data);
	}
	
	
	public function batch_provider_barcode(){
	    $data = array();
	    $data['full_page'] = FALSE;
	    $data['index'] = 0;
	    $this->load->view('course/purchase_batch_check',$data);
	}
    
	public function  batch_check_provider_barcode(){
	    $upload_path = APPPATH."../public/upload/provider_barcode/";
	    $file_name = "provider_barcode.xml";
	    $this->load->library('upload');
	    $this->load->model('course_model');
	    $this->load->model('admin_model');
	    //添加文件上传格式限制
	    if($_FILES["data_file"]["type"] != 'text/xml') {
		    sys_msg("请上传XML格式的文件", 1);
	    }
	    $this->upload->initialize(array(
		    'upload_path' => $upload_path,
		    'file_name' => $file_name,
		    'allowed_types' => '*',
		    'overwrite' => TRUE
		    ));
	    
	    if (!$this->upload->do_upload('data_file')) {
		    sys_msg($this->upload->display_errors(), 1);
	    }
	    $file = $upload_path . $file_name;
	    if (!file_exists($file)) {
		    sys_msg('数据库文件不存在', 1);
	    }
	    $content = file_get_contents($file);
	    $dom = new SimpleXMLElement($content);
	    $dom->registerXPathNamespace('c', 'urn:schemas-microsoft-com:office:spreadsheet');
	    $rows = $dom->xpath('//c:Workbook//c:Worksheet//c:Table//c:Row');
	    $keys = array('provider_barcode');
	    $provider_barcodes = array();
	    foreach ($rows as $key => $row) {
		if ($key == 0)  continue;
		$product = array();
		foreach ($row as $cell) $product[] = trim(strval($cell->Data));
		if (!isset($product[0]) || empty($product[0])) continue;
		$product = array_pad($product, count($keys), '');
		$product = array_slice($product, 0, count($keys));
		$product = array_combine($keys, $product);
		
		$provider_barcodes[] = $product["provider_barcode"];
	    }
	    $data = $this->course_model->filter_barcode_product($provider_barcodes,TRUE);
	    $i = 0 ;
	    $muti_data = array();
	    $result_data = array();
	    foreach ($provider_barcodes as $index=>$provider_barcode){
		$exstis = FALSE;
		$count = 0;
		foreach ($data as $prod){
		    if($prod ->provider_barcode == $provider_barcode){
			$result = array();
			$exstis = TRUE;
			$result["index"] = $index +1;
			$result["provider_barcode"] = $provider_barcode;
			$result["product_sn"] = $prod -> product_sn;
			$provider = $prod -> provider_name;
			if(!empty($prod -> provider_code)){
			   $provider = $provider."【".$prod -> provider_code."】";
			}
			$result["provider"] = $provider;
			$result["provider_productcode"] = $prod -> provider_productcode;
			$result["color_name"] = $prod -> color_name;
			$result["size_name"] = $prod -> size_name;
			$result["batch_code"] = $prod -> batch_code;
			$result["consign_type"] = $prod -> consign_type;
			$result["consign_price"] = $prod -> consign_price;
			$result["cost_price"] = $prod -> cost_price;
			$result["consign_rate"] = $prod -> consign_rate;
			$result_data[] = $result;
			$count ++;
		    }
		}
		$muti_data[$index+1] =$count;
		if(!$exstis){
		    $result = array();
		    $result["index"] = $index +1;
		    $result["provider_barcode"] = $provider_barcode;
		    $result["product_sn"] = "";
		    $result["provider"] = "";
		    $result["provider_productcode"] = "";
		    $result["color_name"] = "";
		    $result["size_name"] = "";
		    $result["batch_code"] = "";
		    $result["consign_type"] = "";
		    $result["consign_price"] = "";
		    $result["cost_price"] = "";
		    $result["consign_rate"] = "";
		    $result_data[] = $result;
		}
		$i = $index +1;
	    }
	    $data = array();
	    $data['full_page'] = TRUE;
	    $data['result_data'] = $result_data;
	    $data['muti_data'] = $muti_data;
	    $data['index'] = $i;
	    $this->load->view('course/purchase_batch_check',$data);
	}

	public function strip_product_tags(){
		$this->course_model->strip_product();
	}
    
}
###
