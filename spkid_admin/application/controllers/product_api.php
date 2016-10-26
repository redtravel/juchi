<?php
/**
* 
*/
class Product_api extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if (!$this->admin_id) sys_msg('未登录或没有权限',1);
		$this->time = date('Y-m-d H:i:s');
	}

	public function color_list()
	{
		$this->load->model('color_model');
		$filter = array();
		$group_id = intval($this->input->post('group_id'));
		if($group_id) $filter['group_id'] = $group_id;
		$result = array(
			'err'=>0,
			'msg'=>'',
			'data'=>get_pair($this->color_model->all_color($filter),'color_id','color_name')
			);
		print json_encode($result);
	}

	public function size_list()
	{
		$this->load->model('size_model');
		$filter = array();
		$result = array(
			'err'=>0,
			'msg'=>'',
			'data'=>get_pair($this->size_model->all_size($filter),'size_id','size_sn,size_name')
			);
		print json_encode($result);
	}

	public function add_sub()
	{
		auth(array('pro_edit','course_edit'));
		$this->load->model('color_model');
		$this->load->model('size_model');
		$this->load->model('product_model');
		$product_id = intval($this->input->post('product_id'));
		$color_id = intval($this->input->post('color_id'));
		$size_id = intval($this->input->post('size_id'));
		$provider_barcode = trim($this->input->post('provider_barcode'));
		$product = $this->product_model->filter(array('product_id'=>$product_id));
		if (!$product) sys_msg('商品不存在',1);
		$color = $this->color_model->filter(array('color_id'=>$color_id));
		if (!$color) sys_msg('颜色不存在',1);
		$size = $this->size_model->filter(array('size_id'=>$size_id));
		if (!$size) sys_msg('尺码不存在',1);
		$sub = $this->product_model->filter_sub(array('product_id'=>$product_id,'color_id'=>$color_id,'size_id'=>$size_id));
		if ($sub) sys_msg('记录已存在，不能重复添加',1);
		
		//颜色尺寸导入增加条形码 @baolm
		if(empty($provider_barcode)) {
			//sys_msg('商品条形码不能为空',1);
			$provider_barcode = $product->product_sn . " " . $color->color_sn . " " . $size->size_sn;
		}
		$barcode_list = $this->product_model->filter_barcode_product($provider_barcode);
		$flag = false;
		if(!empty($barcode_list)) {
			foreach ($barcode_list as $sub_barcode) {
				if($sub_barcode->provider_id == $product->provider_id) {
					sys_msg('商品条形码重复',1);
				}
				//|| $sub_barcode->category_id != $product->category_id
				if(/*$sub_barcode->provider_productcode != $product->provider_productcode 
						|| */$sub_barcode->brand_id != $product->brand_id 
						|| $sub_barcode->color_id != $color_id
						|| $sub_barcode->size_id != $size_id ) {
					sys_msg('不是同一货品条形码不能重复',1);
				}
			}
		}

		$sub_id = $this->product_model->insert_sub(array(
				'product_id'=>$product_id,
				'color_id'=>$color_id,
				'size_id'=>$size_id,
				'gl_num'=>0,
				'is_on_sale'=>0,
				'consign_num'=>-1,
				'wait_num'=>0,
				'sort_order'=>0,
				'provider_barcode'=>$provider_barcode,
				'create_admin'=>$this->admin_id,
				'create_date'=>date('Y-m-d H:i:s')
			));
		print json_encode(array('err'=>0,'msg'=>'','data'=>array('sub_id'=>$sub_id)));
	}

	public function delete_sub()
	{
		auth(array('pro_edit','course_edit'));
		$this->load->model('product_model');
		$this->load->model('depot_model');
		$filter['product_id'] = intval($this->input->post('product_id'));
		$filter['color_id'] = intval($this->input->post('color_id'));
		$filter['size_id'] = intval($this->input->post('size_id'));
		$this->db->trans_begin();
		$sub = $this->product_model->filter_sub($filter);
		if (!$sub) sys_msg('记录不存在',1);
		$this->product_model->lock_product($sub->product_id);
		//@todo:订单判断
		$product = $this->product_model->filter(array('product_id'=>$sub->product_id));
		if($product->is_audit){
			$check = $this->product_model->filter_sub(array('product_id'=>$sub->product_id,'sub_id !='=>$sub->sub_id));
			if(!$check) sys_msg('已审核商品至少应保留一个颜色尺码', 1);
		}
		$check = $this->depot_model->filter_purchase_sub($filter);
		if($check) sys_msg('已进采购单，不能删除', 1);
		$check = $this->depot_model->filter_depot_in_sub($filter);
		if($check) sys_msg('已进入库单，不能删除', 1);
		$this->product_model->delete_sub($sub->sub_id);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>'','data'=>array()));
	}

	public function delete_color()
	{
		auth(array('pro_edit','course_edit'));
		$this->load->model('product_model');
		$this->load->model('depot_model');
		$this->config->load('product');
		$product_fields = array_keys($this->config->item('product_fields'));
		$product_id = intval($this->input->post('product_id'));
		$color_id = intval($this->input->post('color_id'));
		$this->db->trans_begin();
		$this->product_model->lock_product($product_id);
		$product = $this->product_model->filter(array('product_id'=>$product_id));
		if(!$product) sys_msg('商品不存在', 1);
		if($product->is_audit){
			$check = $this->product_model->filter_sub(array('product_id'=>$product_id,'color_id !='=>$color_id));
			if(!$check) sys_msg('已审核商品至少应保留一个颜色尺码', 1);
			$check = $this->product_model->filter_sub(array('product_id'=>$product_id,'color_id'=>$color_id,'is_on_sale'=>1));
			if($check) sys_msg('该颜色已有商品上架，不能删除', 1);
		}
		$check = $this->depot_model->filter_purchase_sub(array('product_id'=>$product_id,'color_id !='=>$color_id));
		if($check) sys_msg('已进采购单，不能删除', 1);
		$check = $this->depot_model->filter_depot_in_sub(array('product_id'=>$product_id,'color_id !='=>$color_id));
		if($check) sys_msg('已进入库单，不能删除', 1);

		$all_sub = $this->product_model->all_sub(array('product_id'=>$product_id,'ps.color_id'=>$color_id));
		foreach ($all_sub as $sub) {
			// @todo:判断
			$this->product_model->delete_sub($sub->sub_id);
		}
		$all_gallery = $this->product_model->all_gallery(array('product_id'=>$product_id,'color_id'=>$color_id));
		foreach ($all_gallery as $gallery) {
			foreach ($this->config->item('product_fields') as $field) {
				@unlink('public/data/images/' . $gallery->img_url . '.' . $field['width'] . 'x' . $field['height'] . '.jpg');
            }
			$this->product_model->delete_gallery($gallery->image_id);
		}
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function add_gallery()
	{

		auth(array('pro_edit','course_edit'));
		$this->load->model('product_model');
		$this->load->model('color_model');
		$this->load->library('upload');
		$this->load->library('image_lib');
		$this->config->load('product');
		$thumb_arr = $this->config->item('product_fields');
		$update['product_id'] = intval($this->input->post('cs_upload_product_id'));
		$update['color_id'] = intval($this->input->post('cs_upload_color_id'));
		$update['image_type'] = trim($this->input->post('cs_upload_image_type'));
		if (!in_array($update['image_type'], array('default','part','tonal'))) {
			die(json_encode(array('err'=>1,'msg'=>'图片类型错误')));
		}
		$product = $this->product_model->filter(array('product_id'=>$update['product_id']));
		if (!$product) {
			die(json_encode(array('err'=>1,'msg'=>'商品不存在')));
		}
		$color = $this->color_model->filter(array('color_id'=>$update['color_id']));
		if (!$color) {
			die(json_encode(array('err'=>1,'msg'=>'颜色不存在')));
		}

		$base_dir = CREATE_IMAGE_PATH;//APPPATH.'../public/data/images/gallery/';
		// 如果已有默认图或片色，则先删除原有的记录。
		if ($update['image_type']=='default' || $update['image_type']=='total') {
			$gallery = $this->product_model->filter_gallery($update);
			if ($gallery) {
				foreach ($thumb_arr as $field=>$thumb) {
					@unlink($base_dir . $gallery->img_url . '.' . $thumb['width'] . 'x' . $thumb['height'] . '.jpg');
                }
				$this->product_model->delete_gallery($gallery->image_id);
			}
		}
		
		$sub_dir = GALLERY_PATH . intval(($update['product_id']-($update['product_id']%100))/100);
		if(!file_exists($base_dir.$sub_dir)) mkdir($base_dir.$sub_dir, 0777, true);
		$file_name = $product->product_id.'_'.$color->color_id.'_'.substr($update['image_type'],0,1).'_'.mt_rand (10000,99999).'.jpg';
		$this->upload->initialize(array(
				'upload_path' => $base_dir.$sub_dir.'/',
				'allowed_types' => 'jpg',
				'file_name' => $file_name
			));
		if ($this->upload->do_upload('cs_upload_image')) {
			$file = $this->upload->data();
			$image = $file['file_name'];
			$raw_name = $file['raw_name'];
			$file_ext = $file['file_ext'];
			$update['img_url'] = $sub_dir.'/'.$image;
			$client_name=$file['client_name'];
			
			foreach ($thumb_arr as $field=>$thumb) {
				$this->image_lib->initialize(array(
					'source_image' => $base_dir.$sub_dir.'/'.$image,
					'quality'=>85,
					'create_thumb'=>TRUE,
					'maintain_ratio'=>FALSE,
					'thumb_marker'=>$thumb['sufix'],
					'width'=>$thumb['width'],
					'height'=>$thumb['height']
				));
				if ($this->image_lib->resize()) {
					$this->image_lib->clear();
					
					if($thumb['wm']){
						$this->image_lib->initialize(array(
							'source_image' => $base_dir.$update[$field],
							'quality'=>85,
							'create_thumb'=>FALSE,
							'wm_type'=>'overlay',	
							'wm_overlay_path'=>APPPATH.'../public/images/'.$thumb['wm_file'],
							'wm_hor_offset'=>$thumb['wm_x'],
							'wm_vrt_offset'=>$thumb['wm_y'],
						));
						$this->image_lib->watermark();
						$this->image_lib->clear();
					}
					
				}else {
					die(json_encode(array('err'=>1,'msg'=>'生成缩略图失败')));
				}
			}
			//@unlink($base_dir.$sub_dir.'/'.$image);
			$update['img_desc'] = '';
			$update['sort_order'] = intval(preg_replace('/[a-zA-Z\_\.]/','',$client_name));
			$update['create_admin'] = $this->admin_id;
			$update['create_date'] = date('Y-m-d H:i:s');
			$image_id = $this->product_model->insert_gallery($update);
			//'image_path'=>'public/data/images/gallery/'.
			die(json_encode(array('err'=>0,'msg'=>'','image_id'=>$image_id,'image_type'=>$update['image_type'],'image_path'=>'public/data/images/'.$update['img_url'].'.85x85.jpg','sort_order'=>$update['sort_order'],'img_desc'=>'')));
		} else {
			die(json_encode(array('err'=>1,'msg'=>'上传失败，请检查图片格式。')));
		}
	}

	public function delete_gallery()
	{
		auth(array('pro_edit','course_edit'));
		$this->load->model('product_model');
		$this->config->load('product');
		$product_fields = array_keys($this->config->item('product_fields'));
		$image_id = intval($this->input->post('image_id'));
		$image = $this->product_model->filter_gallery(array('image_id'=>$image_id));
		if (!$image) sys_msg('记录不存在',1);
		$sub = $this->product_model->filter_sub(array('product_id'=>$image->product_id,'color_id'=>$image->color_id,'is_on_sale'=>1));
		if ($sub) {
			switch ($image->image_type) {
				case 'default':
				case 'tonal':
					sys_msg('该颜色已有商品上架，不能删除该图片', 1);
					break;
				
				default:
					$check = $this->product_model->filter_gallery(array('image_id !='=>$image_id,'product_id'=>$image->product_id,'color_id'=>$image->color_id,'image_type'=>'part'));
					if(!$check) sys_msg('该颜色已有商品上架，不能删除该图片', 1);
					break;
			}
		}
		foreach ($this->config->item('product_fields') as $field) {
            @unlink('public/data/images/' . $image->img_url . '.' . $field['width'] . 'x' . $field['height'] . '.jpg'); //去掉gallery/
        }
        $this->product_model->delete_gallery($image_id);
		print json_encode(array('err'=>0,'msg'=>'','color_id'=>$image->color_id));
	}

	public function sort_sub()
	{
		auth(array('pro_edit','course_edit'));
		$this->load->model('product_model');
		$product_id = intval($this->input->post('field'));
		$color_id = intval($this->input->post('id'));
		$val = intval($this->input->post('val'));
		$this->product_model->update_where_sub(array('sort_order'=>$val), array('product_id'=>$product_id,'color_id'=>$color_id));
		print json_encode(array('err'=>0,'msg'=>'','content'=>$val));
	}

	public function edit_gallery()
	{
		auth(array('pro_edit','course_edit'));
		$this->load->model('product_model');
		$field = trim($this->input->post('field'));
		$val = trim($this->input->post('val'));
		$id = intval($this->input->post('id'));
		if (!in_array($field,array('sort_order','img_desc'))) {
			sys_msg('参数错误');
		}
		switch ($field) {
			case 'sort_order':
				$val = intval($val);
				break;
			case 'img_desc':
				if($val=='无描述') $val = '';
				break;
		}
		
		$this->product_model->update_gallery(array($field=>$val), $id);
		$gallery = $this->product_model->filter_gallery(array('image_id'=>$id));
		if (!$gallery) {
			sys_msg('记录不存在');
		}
		if (!$gallery->img_desc) {
			$gallery->img_desc = '无描述';
		}
		print json_encode(array('err'=>0,'msg'=>'','content'=>$gallery->$field));
	}

	public function link_search()
	{
		auth(array('pro_edit','course_edit'));
		$this->load->model('product_model');
		$this->load->helper('product');
		$filter = array();

		$filter['product_id'] = intval($this->input->post('product_id'));

		$product_sn = trim($this->input->post('product_sn'));
		if ($product_sn) $filter['product_sn'] = $product_sn;
                
                $product_id2 = intval($this->input->post('product_id2'));
		if ($product_id2) $filter['product_id2'] = $product_id2;

		$product_name = trim($this->input->post('product_name'));
		if ($product_name) $filter['product_name'] = $product_name;

		$provider_productcode = trim($this->input->post('provider_productcode'));
		if ($provider_productcode) $filter['provider_productcode'] = $provider_productcode;

		$style_id = intval($this->input->post('style_id'));
		if ($style_id) $filter['style_id'] = $style_id;

		$season_id = intval($this->input->post('season_id'));
		if ($season_id) $filter['season_id'] = $season_id;

		$product_sex = intval($this->input->post('product_sex'));
		if ($product_sex) $filter['product_sex'] = $product_sex;

		$filter = get_pager_param($filter);
		$data = $this->product_model->link_search($filter);
		attach_gallery($data['list']);
		attach_sub($data['list']);

		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('product/link_search', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('product/link_search', $data);
	}
	
	public function add_link ()
	{
		auth(array('pro_edit','course_edit'));
		$this->load->model('product_model');
		$this->load->helper('product');
		$product_id = intval($this->input->post('product_id'));
		$is_bothway = intval($this->input->post('is_bothway'))==1 ? 1: 0;
		$link_product_ids = trim($this->input->post('link_product_ids'));
		if ( !$link_product_ids ) sys_msg('请选择关联商品', 1);
		$link_product_ids = explode('|', $link_product_ids);
		$product = $this->product_model->filter(array('product_id'=>$product_id));
		if ( !$product ) sys_msg('商品不存在', 1);
		
		foreach( $link_product_ids as $link_product_id )
		{
			$link_product = $this->product_model->filter(array('product_id'=>$link_product_id));
			if(!$link_product) continue;
			$link = $this->product_model->filter_link(array('product_id'=>$product_id,'link_product_id'=>$link_product_id));
			if ( $link ) continue;
			$this->product_model->insert_link(array('product_id'=>$product_id,'link_product_id'=>$link_product_id,'is_bothway'=>$is_bothway));
			
		}
		// fetch linked product
		$link_product = $this->product_model->link_product($product_id);
		$link_by_product = $this->product_model->link_by_product($product_id);
		attach_gallery($link_product); attach_sub($link_product);
		attach_gallery($link_by_product); attach_sub($link_by_product);
		$this->load->vars('link_product', $link_product);
		$this->load->vars('link_by_product', $link_by_product);
		$result = array('err'=>0,'msg'=>'','data'=>$this->load->view('product/link_list', '',TRUE));
		print json_encode($result);
	}
	
	public function delete_link ()
	{
		auth(array('pro_edit','course_edit'));
		$this->load->model('product_model');
		$link_id = intval($this->input->post('link_id'));
		$this->product_model->delete_link($link_id);
		print json_encode(array('err'=>0,'msg'=>''));
	}
	
	public function toggle_link ()
	{
		auth(array('pro_edit','course_edit'));
		$this->load->model('product_model');
		$result = proc_toggle('product_model','link_id',array('is_bothway'),'filter_link','update_link');
		print json_encode($result);
	}
	
	public function audit ($product_id)
	{
		auth(array('pro_audit','course_audit'));
		$this->load->model('product_model');
		$product_id = intval($product_id);
		$product = $this->product_model->filter(array('product_id'=>$product_id));
		if (!$product) sys_msg('商品不存在',1);
		if ($product->is_audit) sys_msg('商品已审核',1);
		$sub = $this->product_model->filter_sub(array('product_id'=>$product_id));
		if(!($sub)) sys_msg('至少需要一个色码', 1);
		$this->product_model->update(array('is_audit'=>1,'audit_admin'=>$this->admin_id, 'audit_date'=>$this->time), $product_id);
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'product/edit/'.$product_id), array('text'=>'返回列表','href'=>'product/index')));
	}

	public function gallery_preview($product_id,$size='140')
	{
		$this->load->model('product_model');
		$gallery=$this->product_model->filter_gallery(array('product_id'=>$product_id,'image_type'=>'default'));
		if (!$gallery) {
			print '图片未上传';
			return;
		}
		$html = "<div>";
		//去掉gallery/
        $html .= "<img src=\"public/data/images/{$gallery->img_url}.{$size}x{$size}.jpg\"/>";
		$html .= "</div>";
		print $html;
		return;
	}

	public function batch_audit()
	{
		$this->load->model('product_model');
		auth(array('pro_audit','course_audit'));
		$product_ids = trim($this->input->post('product_ids'));
		if(empty($product_ids)) sys_msg('请选择商品', 1);
		$count = 0 ;
		$skip_count = 0;
		foreach (explode(',', $product_ids) as $product_id) {
			$count += 1;
			$product_id = intval($product_id);
			$product = $this->product_model->filter(array('product_id'=>$product_id));
			if(!$product || $product->is_audit) { $skip_count += 1 ; continue;}
			$sub = $this->product_model->filter_sub(array('product_id'=>$product_id));
			if(!($sub)){ $skip_count += 1 ; continue;}
			$this->product_model->update(array('is_audit'=>1,'audit_admin'=>$this->admin_id, 'audit_date'=>$this->time), $product_id);
		}
		sys_msg("操作完成，共审核[".$count."]件商品，其中[".$skip_count."]件审核失败。", 0);
		
	}
	public function get_cost_price(){
		auth(array('cost_price_check',''));
		$product_id = trim($this->input->post('product_id'));
		if(empty($product_id)) sys_msg('请选择商品', 1);
		$this->load->model('product_model');
		$data = $this->product_model->get_product_price($product_id);
		$data['content'] = $this->load->view('/product/view_cost_price', $data, TRUE);
		$data['error'] = 0;
		unset($data['list']);
		echo json_encode($data);
		return;
	}
	
	public function audit_pic(){
		$this->load->model('product_model');
		auth(array('pro_audit_pic','course_audit_pic'));
		$product_id = trim($this->input->post('product_id'));
		$color_id = trim($this->input->post('color_id'));
		$is_pic = trim($this->input->post('is_pic'));
		if(empty($product_id)||empty($color_id)) sys_msg('系统异常请联系管理员', 1);
		$data = $this->product_model->audit_pic($product_id,$color_id,$is_pic);
		$data["err"] = 0;
		$data["msg"] = "设置拍摄状态成功";
		echo json_encode($data);
		return;
	}
	
	public function batch_provider_barcode(){
	    $this->load->model('product_model');
	    $this->load->model('admin_model');
	    $provider_barcode = $this->input->post('provider_barcode');
	    $data = $this->product_model->filter_barcode_product($provider_barcode,TRUE);
	    if(!empty($data)){
		echo json_encode($data);
	    }
	    return ;
	}
}
