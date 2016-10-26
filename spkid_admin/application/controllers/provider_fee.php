<?php
#doc
#	classname:	Style
#	scope:		PUBLIC
#
#/doc

class Provider_fee extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('provider_fee_model');
		$this->load->model('provider_model');
		$this->load->model('brand_model');
		$this->load->model('purchase_batch_model');
	}
	
	public function index ()
	{
		auth('provider_fee_view');
		$filter = $this->uri->uri_to_assoc(3);
		$category_id = trim($this->input->post('category_id'));
		if ($category_id!=0) $filter['category_id'] = $category_id;
		$provider_id = trim($this->input->post('provider_id'));
		if ($provider_id!=0) $filter['provider_id'] = $provider_id;
		$check_status = trim($this->input->post('check_status'));
		if ($check_status!=0) $filter['check_status'] = $check_status;
        
		$check_date_start = trim($this->input->post('check_date_start'));
		if (!empty($check_date_start)) $filter['check_date_start'] = $check_date_start;
		$check_date_end = trim($this->input->post('check_date_end'));
		if ($check_date_end!='') $filter['check_date_end'] = $check_date_end;
		$filter2 = get_pager_param($filter);
		$data = $this->provider_fee_model->query($filter2);
		$this->load->vars('perm_add', check_perm('provider_fee_add'));
		$this->load->vars('perm_del', check_perm('provider_fee_del'));
		$this->load->vars('perm_edit', check_perm('provider_fee_edit'));
		if ($this->input->post('is_ajax'))
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('provider_fee/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
        //搜索条件
        $provider_list = $this->provider_model->all_provider(array('is_use'=>1));
        $this->load->vars('provider_list',$provider_list);
        $fee_category_list = $this->provider_fee_model->all_fee_category(array('is_use'=>1));
        $this->load->vars('fee_category_list',$fee_category_list);
		$data['full_page'] = TRUE;
		$this->load->view('provider_fee/index', $data);
	}

	public function add()
	{
		auth('provider_fee_add');
        $provider_list = $this->provider_model->all_provider(array('is_use'=>1));
        $this->load->vars('provider_list',$provider_list);
        $brand_list = $this->brand_model->all_brand(array());
        $this->load->vars('brand_list',$brand_list);
        $fee_category_list = $this->provider_fee_model->all_fee_category(array('is_use'=>1));
        $this->load->vars('fee_category_list',$fee_category_list);
		$this->load->view('provider_fee/add');
	}

	public function proc_add()
	{
		auth('provider_fee_add');
		$update = array();
		$update['provider_id'] = intval($this->input->post('provider_id'));
		$update['brand_id'] = intval($this->input->post('brand_id'));
		$update['batch_id'] = intval($this->input->post('batch_id'));
		$update['category_id'] = intval($this->input->post('category_id'));
		$update['detail_price'] = $this->input->post('detail_price');
		$update['remark'] = trim($this->input->post('remark'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');
        if ($update['provider_id']==0) {
			sys_msg('必须选择供应商', 1);
		}
        if ($update['brand_id']==0) {
			sys_msg('必须选择品牌', 1);
		}
        if ($update['batch_id']==0) {
			sys_msg('必须选择批次', 1);
		}else{
            $batch_list = $this->purchase_batch_model->query(array('batch_status'=>1,'batch_id'=>$update['batch_id'],'provider_id'=>$update['provider_id']));
            if(empty($batch_list)){
                sys_msg('所选供应商下没有这个批次!', 1);
            }
        }
        if ($update['category_id']==0) {
			sys_msg('必须选择必须选择一个费用明目', 1);
		}
        if(empty($update['detail_price'])){
            sys_msg('必须输入费用', 1);
		}else{
            if (!intval($update['detail_price'])) {
                sys_msg('费用参数错误', 1);
            }
        }
        
		$id = $this->provider_fee_model->insert($update);
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'provider_fee/edit/'.$id), array('text'=>'返回列表','href'=>'provider_fee')));
	}
   /**
    * 编辑或者查看
    * @param type $id
    */
	public function edit($id)
	{
		auth(array('provider_fee_edit','provider_fee_view'));
		$provider_fee = $this->provider_fee_model->filter(array('id'=>$id));
		if (!$provider_fee) {
			sys_msg('记录不存在', 1);
		}
        $provider_list = $this->provider_model->all_provider(array('is_use'=>1));
        $this->load->vars('provider_list',$provider_list);
        $brand_list = $this->brand_model->all_brand(array());
        $this->load->vars('brand_list',$brand_list);
        $fee_category_list = $this->provider_fee_model->all_fee_category(array('is_use'=>1));
        $this->load->vars('fee_category_list',$fee_category_list);
        $batch_list = $this->purchase_batch_model->query(array('batch_status'=>1,'provider_id'=>$provider_fee->provider_id));
        $this->load->vars('batch_list',$batch_list);
		$this->load->vars('row', $provider_fee);
        $perm_edit = check_perm('provider_fee_edit');
        //如果已审核则不能编辑
        if(!empty($provider_fee->check_admin)){
            $perm_edit = 0;
        }
		$this->load->vars('perm_edit', $perm_edit);
		$this->load->view('provider_fee/edit');
	}

	public function proc_edit()
	{
		auth('provider_fee_edit');
		$update = array();
		$update['provider_id'] = intval($this->input->post('provider_id'));
		$update['brand_id'] = intval($this->input->post('brand_id'));
		$update['batch_id'] = intval($this->input->post('batch_id'));
		$update['category_id'] = intval($this->input->post('category_id'));
		$update['detail_price'] = $this->input->post('detail_price');
		$update['remark'] = trim($this->input->post('remark'));
        
        if ($update['provider_id']==0) {
			sys_msg('必须选择供应商', 1);
		}
        if ($update['brand_id']==0) {
			sys_msg('必须选择品牌', 1);
		}
        if ($update['batch_id']==0) {
			sys_msg('必须选择批次', 1);
		}else{
            $batch_list = $this->purchase_batch_model->query(array('batch_status'=>1,'batch_id'=>$update['batch_id'],'provider_id'=>$update['provider_id']));
            if(empty($batch_list)){
                sys_msg('所选供应商下没有这个批次!', 1);
            }
        }
        if ($update['category_id']==0) {
			sys_msg('必须选择必须选择一个费用明目', 1);
		}
        if(empty($update['detail_price'])){
            sys_msg('必须输入费用', 1);
		}else{
            if (!intval($update['detail_price'])) {
                sys_msg('费用参数错误', 1);
            }
        }
		$id = intval($this->input->post('id'));
        if($id==0){
            sys_msg('ID参数错误', 1);
        }
		$provider_fee = $this->provider_fee_model->filter(array('id'=>$id));
		if (!$provider_fee) {
			sys_msg('记录不存在', 1);
		}
        if(!empty($provider_fee->check_admin)){
            sys_msg('已经审核不能编辑!', 1);
        }

		$this->provider_fee_model->update($update, $id);
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'provider_fee/edit/'.$id), array('text'=>'返回列表','href'=>'provider_fee')));
	}

	public function delete($id)
	{
		auth('provider_fee_del');
		$test = $this->input->post('test');
		$provider_fee = $this->provider_fee_model->filter(array('id'=>$id));
		if(!empty($provider_fee->check_admin)) sys_msg('已经审核,不能删除!',1);
		if($test) sys_msg('',0);
		$this->provider_fee_model->delete($id);
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'provider_fee')));		
	}
    public function check($id){
        auth('provider_fee_check');

		$provider_fee = $this->provider_fee_model->filter(array('id'=>$id));
		if (!$provider_fee) {
			echo json_encode(array('err'=>1,'msg'=>'记录不存在!'));
            return;
		}
        if(!empty($provider_fee->check_admin)){
			echo json_encode(array('err'=>1,'msg'=>'已经审核不能编辑!'));
            return;
        }
        $update = array();
		$update['check_admin'] = $this->admin_id;
		$update['check_date'] = date('Y-m-d H:i:s');
		$this->provider_fee_model->update($update, $id);
		$update['check_admin_name'] = $this->session->userdata('admin_name');
		$update['err'] = 0;
        $update['msg'] = '已审核';
        echo json_encode($update);
        return;
    }
}
###