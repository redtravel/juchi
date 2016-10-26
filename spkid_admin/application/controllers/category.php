<?php
#doc
#	classname:	Category
#	scope:		PUBLIC
#
#/doc

class Category extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('category_model');
	}
	
	public function index ()
	{
		auth('category_view');
		$this->load->helper('category');
		$all_category = $this->category_model->all_category();
		$all_category = category_tree($all_category);
		$all_category = category_flatten($all_category, '---- ');
		$this->load->vars('list', $all_category);
		$this->load->vars('perm_delete', check_perm('category_edit'));
		$this->load->view('category/index');
	}

	public function add()
	{
		auth('category_edit');
		$this->load->helper('category');
		$all_category = $this->category_model->all_category();
		$all_category = category_tree($all_category);
		$all_category = category_flatten($all_category, '-- ');
		$this->load->vars('all_category', $all_category);

		//商品所属大类
        $this->load->model('product_genre_model');
        $all_genre = $this->product_genre_model->all_genre();
		$this->load->vars('all_genre', $all_genre);

		$this->load->view('category/add');
	}

	public function proc_add()
	{
		auth('category_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('category_name', '分类名称', 'trim|required');
		$this->form_validation->set_rules('cate_code', '分类代号', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['genre_id'] = $this->input->post('genre_id');
		$update['cate_code'] = $this->input->post('cate_code');
		$update['category_name'] = $this->input->post('category_name');
		$update['parent_id'] = intval($this->input->post('parent_id'));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');

		if($update['cate_code'] != ''){
			$cate_code = $this->category_model->filter(array('cate_code'=>$update['cate_code']));
			if (!empty($cate_code)) {
				sys_msg('分类代号已存在，请重新填写', 1);
			}
		}

		if ($update['parent_id'] != 0) {
			$category = $this->category_model->filter(array('category_id'=>$update['parent_id']));
			if (!$category) {
				sys_msg('父分类不存在', 1);
			}
            if( $category->genre_id != $update['genre_id'] ){
				sys_msg('商品类型选择错误。', 1);
            }
		}		

		$category_id = $this->category_model->insert($update);
		
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'category/edit/'.$category_id), array('text'=>'返回列表','href'=>'category/index')));
	}

	public function edit($category_id)
	{
		auth(array('category_edit','category_view'));
		$this->load->helper('category');
		$category = $this->category_model->filter(array('category_id'=>$category_id));
		if (!$category) {
			sys_msg('记录不存在', 1);
		}
		$all_category = $this->category_model->all_category(array('category_id !='=>$category_id));
		$all_category = category_tree($all_category);
		$all_category = category_flatten($all_category, '-- ');
		$this->load->vars('all_category', $all_category);
		$this->load->vars('row', $category);
		$this->load->vars('perm_edit', check_perm('category_edit'));
		
		//商品所属大类
        $this->load->model('product_genre_model');
        $all_genre = $this->product_genre_model->all_genre();
		$this->load->vars('all_genre', $all_genre);

		$this->load->view('category/edit');
	}

	public function proc_edit()
	{
		auth('category_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('category_name', '分类名称', 'trim|required');
		$this->form_validation->set_rules('cate_code', '分类代号', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$category_id = intval($this->input->post('category_id'));
		$update['genre_id'] = $this->input->post('genre_id');
		$update['cate_code'] = $this->input->post('cate_code');
		$update['category_name'] = $this->input->post('category_name');
		$update['parent_id'] = intval($this->input->post('parent_id'));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');

		if($update['cate_code'] != ''){
			$cate_code = $this->category_model->filter(array('cate_code'=>$update['cate_code'], 'category_id !='=>$category_id));
			if (!empty($cate_code)) {
				sys_msg('分类代号已存在，请重新填写', 1);
			}
		}

		$category = $this->category_model->filter(array('category_id'=>$category_id));
		if (!$category) {
			sys_msg('记录不存在!', 1);
		}
		if($update['parent_id']!=0){
			$check_category = $this->category_model->filter(array('category_id'=>$update['parent_id'], 'category_id !='=>$category_id));
			if (!$check_category) {
				sys_msg('父级分类不存在', 1);
			}			
            if( $check_category->genre_id != $update['genre_id'] ){
				sys_msg('商品类型选择错误。', 1);
            }
		}
		
		$this->category_model->update($update, $category_id);
		
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'category/edit/'.$category_id), array('text'=>'返回列表','href'=>'category/index')));
	}

	public function delete($category_id)
	{
		auth('category_edit');
		$this->load->model('product_model');
		$test = $this->input->post('test');
		$check_category = $this->category_model->filter(array('parent_id'=>$category_id));
		if ($check_category) sys_msg('该分类下尚有子类，不能删除。',1);

		$product = $this->product_model->filter(array('category_id'=>$category_id));
		if($product) sys_msg('该分类不能删除', 1);
		if($test) sys_msg('', 0);
		$this->category_model->delete($category_id);
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'category/index')));		
	}

	public function toggle()
	{
		auth('category_edit');
		$result = proc_toggle('category_model','category_id',array('is_use'));
		print json_encode($result);
	}

	public function edit_field()
	{
		auth('category_edit');
		switch (trim($this->input->post('field'))) {
			case 'sort_order':
				$val = intval($this->input->post('val'));
				break;
			
			default:
				$val = NULL;
				break;
		}
		print json_encode(proc_edit('category_model','category_id', array('sort_order'), $val));
	}

}
###
