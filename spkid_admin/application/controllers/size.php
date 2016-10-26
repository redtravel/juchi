<?php
#doc
#	classname:	Size
#	scope:		PUBLIC
#
#/doc

class Size extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('size_model');
	}
	
	public function index ()
	{
		auth('size_view');
		$filter = $this->uri->uri_to_assoc(3);
		$size_name = trim($this->input->post('size_name'));
		if (!empty($size_name)) $filter['size_name'] = $size_name;

		$category_id = intval($this->input->post('category_id'));
		if ($category_id) $filter['category_id'] = $category_id;

		$filter = get_pager_param($filter);
		$data = $this->size_model->size_list($filter);
		$this->load->vars('perm_delete', check_perm('size_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('size/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('size/index', $data);
	}
	
	public function edit ($size_id = 0)
	{
		auth(array('size_edit','size_view'));
		$size = $this->size_model->filter(array('size_id' => $size_id));
		if ( empty($size) )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('row', $size);
		$this->load->vars('perm_edit', check_perm('size_edit'));
		$this->load->view('size/edit');
	}

	public function proc_edit ()
	{
		auth('size_edit');
		$size_id = intval($this->input->post('size_id'));
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->form_validation->set_rules('size_name', '尺寸名称', 'trim|required');
//		$this->form_validation->set_rules('size_sn', '尺寸编码', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$size = $this->size_model->filter(array('size_id' => $size_id));
		if ( ! $size )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		$update['size_name'] = $this->input->post('size_name');
//		$update['size_sn'] = $this->input->post('size_sn');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = $this->input->post('is_use') == 1 ? 1 : 0;
		
		$old_size = $this->size_model->filter(array('size_name'=>$update['size_name'], 'size_id !='=>$size_id));
		if ( $old_size )
		{
			sys_msg('尺寸名称重复！',1);
		}
//		$old_size = $this->size_model->filter(array('size_sn'=>$update['size_sn'], 'size_id !='=>$size_id));
//		if ( $old_size )
//		{
//			sys_msg('尺寸编码重复！',1);
//		}
		$this->size_model->update($update, $size_id);
		
		sys_msg('操作成功！', 0, array(array('text'=>'继续编辑', 'href'=>'size/edit/'.$size_id), array('text'=>'返回列表', 'href'=>'size/index')));
	}

	public function add ()
	{
		auth('size_edit');
		$this->load->view('size/add');
	}

	public function proc_add ()
	{
		auth('size_edit');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('size_name', '尺寸名称', 'trim|required');
//		$this->form_validation->set_rules('size_sn', '尺寸编码', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['size_name'] = $this->input->post('size_name');
//		$update['size_sn'] = $this->input->post('size_sn');
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = $this->input->post('is_use') == 1 ? 1 : 0;
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$size = $this->size_model->filter(array('size_name'=>$update['size_name']));
		if ( $size )
		{
			sys_msg('尺寸名称重复', 1);
		}
//		$size = $this->size_model->filter(array('size_sn'=>$update['size_sn']));
//		if ( $size )
//		{
//			sys_msg('尺寸编码重复', 1);
//		}
		
		$update['size_sn'] = $this->size_model->gen_size_sn();
		$size_id = $this->size_model->insert($update);
		
		sys_msg('操作成功！',0 , array(array('text'=>'继续编辑', 'href'=>'size/edit/'.$size_id), array('text'=>'返回列表', 'href'=>'size/index')));
	}

	public function delete ($size_id)
	{
		auth('size_edit');
		$this->load->model('product_model');
		$test = $this->input->post('test');
		$sub = $this->product_model->filter_sub(array('size_id'=>$size_id));
		if($sub) sys_msg('该颜色不能删除', 1);
		if($test) sys_msg('', 0);
		$this->size_model->delete($size_id);
		sys_msg('操作成功！',0 , array(array('text'=>'返回列表', 'href'=>'size/index')));
	}

	public function image_index ()
	{
		auth('sizeimg_view');
		$this->load->model('category_model');
		$this->load->model('brand_model');
		$this->load->helper('category');
		$filter = $this->uri->uri_to_assoc(3);
		$brand_id = intval($this->input->post('brand_id'));
		if (!empty($brand_id)) $filter['brand_id'] = $brand_id;

		$category_id = intval($this->input->post('category_id'));
		if ($category_id) $filter['category_id'] = $category_id;

		$sex = intval($this->input->post('sex'));
		if ($sex) $filter['sex'] = $sex;

		$filter = get_pager_param($filter);
		$data = $this->size_model->image_list($filter);
		$this->load->vars('perm_delete', check_perm('sizeimg_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('size/image_index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->vars('all_category', category_flatten(category_tree($this->category_model->all_category()),'--'));
		$this->load->vars('all_brand', $this->brand_model->all_brand());
		$this->load->view('size/image_index', $data);
	}

	public function image_add ()
	{
		auth('sizeimg_edit');
		$this->load->model('category_model');
		$this->load->model('brand_model');
		$this->load->helper('category');
		$this->load->vars('all_category', category_flatten(category_tree($this->category_model->all_category()),'--'));
		$this->load->vars('all_brand', $this->brand_model->all_brand());
		$this->load->view('size/image_add');
	}

	public function proc_image_add ()
	{
		auth('sizeimg_edit');

		$this->load->library('form_validation');
		$this->load->model('category_model');
		$this->load->model('brand_model');
		$this->load->library('upload');
		$this->form_validation->set_rules('brand_id', '品牌', 'trim|required');
		$this->form_validation->set_rules('category_id', '分类', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['brand_id'] = intval($this->input->post('brand_id'));
		$update['category_id'] = intval($this->input->post('category_id'));
		$update['sex'] = intval($this->input->post('sex')) == 1 ? 1 : 2;
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
		$brand = $this->brand_model->filter(array('brand_id'=>$update['brand_id']));
		if ( ! $brand )
		{
			sys_msg('品牌不存在', 1);
		}
		$category = $this->category_model->filter(array('category_id'=>$update['category_id']));
		if ( ! $category )
		{
			sys_msg('分类不存在', 1);
		}
		$image_size = $this->size_model->filter_image(array('category_id'=>$update['category_id'], 'brand_id'=>$update['brand_id'], 'sex'=>$update['sex']));
		if ($image_size) {
			sys_msg('记录重复',1);
		}
		$size_image_id = $this->size_model->insert_image($update);
		// 上传图片
		$this->upload->initialize(array(
				'upload_path' => SIZE_IMAGE_PATH . PRO_SIZE_IMAGE_TAG,
				'allowed_types' => 'gif|jpg|png',
				'encrypt_name' => TRUE
			));
		if ($this->upload->do_upload('image_url')) {
			$file = $this->upload->data();
			$this->size_model->update_image(array('image_url'=> PRO_SIZE_IMAGE_TAG . $file['file_name']), $size_image_id);
		}
		sys_msg('操作成功！',0 , array(array('text'=>'继续编辑', 'href'=>'size/image_edit/'.$size_image_id), array('text'=>'返回列表', 'href'=>'size/image_index')));
	}

	public function image_edit ($size_image_id = 0)
	{
		auth('sizeimg_edit');
		$this->load->model('category_model');
		$this->load->model('brand_model');
		$this->load->helper('category');
		$size_image = $this->size_model->filter_image(array('size_image_id' => $size_image_id));
		if ( empty($size_image) )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('row', $size_image);
		$this->load->vars('all_category', category_flatten(category_tree($this->category_model->all_category()),'--'));
		$this->load->vars('all_brand', get_pair($this->brand_model->all_brand(), 'brand_id', 'brand_name'));
		$this->load->vars('perm_edit', check_perm('sizeimg_edit'));
		$this->load->view('size/image_edit');
	}

	public function proc_image_edit ()
	{
		auth('sizeimg_edit');
		$size_image_id = intval($this->input->post('size_image_id'));
		$this->load->library('form_validation');
		$this->load->library('upload');
		$this->load->model('category_model');
		$this->load->model('brand_model');
		$this->form_validation->set_rules('brand_id', '品牌', 'trim|required');
		$this->form_validation->set_rules('category_id', '分类', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}
		$size_image = $this->size_model->filter_image(array('size_image_id' => $size_image_id));
		if ( ! $size_image )
		{
			sys_msg('记录不存在', 1);
		}
		$update = array();
		$update['brand_id'] = intval($this->input->post('brand_id'));
		$update['category_id'] = intval($this->input->post('category_id'));
		$update['sex'] = intval($this->input->post('sex')) == 1 ? 1 : 2;
		
		$brand = $this->brand_model->filter(array('brand_id'=>$update['brand_id']));
		if ( ! $brand )
		{
			sys_msg('品牌不存在', 1);
		}
		$category = $this->category_model->filter(array('category_id'=>$update['category_id']));
		if ( ! $category )
		{
			sys_msg('分类不存在', 1);
		}

		$old_size_image = $this->size_model->filter_image(array('brand_id'=>$update['brand_id'], 'category_id'=>$update['category_id'],'sex'=>$update['sex'], 'size_image_id !='=>$size_image_id));
		if ( $old_size_image )
		{
			sys_msg('记录重复',1);
		}
		
		$this->size_model->update_image($update, $size_image_id);
		//上传图片
		$this->upload->initialize(array(
				'upload_path'=>SIZE_IMAGE_PATH . PRO_SIZE_IMAGE_TAG ,
				'allowed_types' => 'jpg|gif|png',
				'encrypt_name' => TRUE
			));
		if ($this->upload->do_upload('image_url')) {
			$file = $this->upload->data();
			if ($size_image->image_url) {
				@unlink( SIZE_IMAGE_PATH . PRO_SIZE_IMAGE_TAG . $size_image->image_url);
			}
			$this->size_model->update_image(array('image_url'=> PRO_SIZE_IMAGE_TAG . $file['file_name']), $size_image_id);
		}
		
		sys_msg('操作成功！', 0, array(array('text'=>'继续编辑', 'href'=>'size/image_edit/'.$size_image_id), array('text'=>'返回列表', 'href'=>'size/image_index')));
	}

	public function image_delete($size_image_id)
	{
		auth('sizeimg_edit');
		$test = $this->input->post('test');
		$size_image = $this->size_model->filter_image(array('size_image_id'=>$size_image_id));
		if(!$size_image) sys_msg('记录不存在',1);
		if($test) sys_msg('', 0);
		$this->size_model->delete_image($size_image_id);
		if($size_image->image_url){
			@unlink( SIZE_IMAGE_PATH . PRO_SIZE_IMAGE_TAG . $size_image->image_url);
		}
		sys_msg('操作成功！', 0, array(array('text'=>'返回列表', 'href'=>'size/image_index')));
	}

	public function toggle()
	{
		auth('brand_edit');
		$result = proc_toggle('size_model','size_id',array('is_use'));
		print json_encode($result);
	}

	public function edit_field()
	{
		auth('brand_edit');
		switch (trim($this->input->post('field'))) {
			case 'sort_order':
				$val = intval($this->input->post('val'));
				break;
			
			default:
				$val = NULL;
				break;
		}
		print(json_encode(proc_edit('size_model', 'size_id', array('sort_order'), $val)));
		return;
	}
        
        /* ---- 尺寸表 ---- */
        public function size_table_edit ($size_image_id = 0)
	{
		auth('sizetable_edit');
		$this->load->model('category_model');
		$this->load->model('brand_model');
		$this->load->helper('category');
		$size_image = $this->size_model->filter_image(array('size_image_id' => $size_image_id));
		if ( empty($size_image) )
		{
			sys_msg('记录不存在！', 1);
		}
		$this->load->vars('row', $size_image);
		$this->load->vars('all_category', category_flatten(category_tree($this->category_model->all_category()),'--'));
		$this->load->vars('all_brand', get_pair($this->brand_model->all_brand(), 'brand_id', 'brand_name'));
		$this->load->vars('perm_edit', check_perm('sizetable_edit'));
		$this->load->view('size/size_table_edit');
	}
        
        public function size_table_show ($size_image_id = 0)
	{
		auth('sizetable_edit');
		$size_image = $this->size_model->filter_image(array('size_image_id' => $size_image_id));
		if ( empty($size_image) )
		{
			sys_msg('记录不存在！', 1);
		}

                echo $size_image->size_table;
	}
        
        public function size_table_delete ($size_image_id = 0)
	{
		auth('sizetable_edit');
		$size_image = $this->size_model->filter_image(array('size_image_id' => $size_image_id));
		if ( empty($size_image) )
		{
			sys_msg('记录不存在！', 1);
		}

                $data = array();
                $data['size_table'] = null;
                $this->size_model->update_image($data, $size_image_id);
                
                sys_msg('操作成功！', 0, array(array('text'=>'返回列表', 'href'=>'size/image_index')));
	}
        
}
###