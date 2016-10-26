<?php
/**
* 
*/
class Voucher extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('voucher_model');
		$this->load->helper('voucher');
		$this->config->load('voucher');
		$this->campaign_status_list = $this->config->item('voucher_campaign_status_list');
		$this->release_status_list = $this->config->item('voucher_release_status_list');
		$this->record_status_list = $this->config->item('voucher_record_status_list');
		$this->release_rules = $this->config->item('voucher_release_rules');
		$this->voucher_config = index_array($this->config->item('voucher_config'),'code');
	}

	public function index()
	{
		auth('voucher_campaign_view');
		$filter = $this->uri->uri_to_assoc(3);
		$filter['compaign_name'] = trim($this->input->post('compaign_name'));
		$filter['compaign_type'] = trim($this->input->post('compaign_type'));
		if (FALSE!==$this->input->post('campaign_status')) $filter['campaign_status'] = intval($this->input->post('campaign_status'));

		$filter['start_time'] = trim($this->input->post('start_time'));
		$filter['end_time'] = trim($this->input->post('end_time'));

		$filter = get_pager_param($filter);
		$data = $this->voucher_model->campaign_list($filter);
		$data['all_type'] = get_pair($this->voucher_config, 'code', 'name');
		$data['all_status'] = $this->campaign_status_list;
		$this->load->vars('perm_delete', check_perm('voucher_campaign_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('voucher/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('voucher/index', $data);

	}

	public function query()
	{
		auth('voucher_view');
		$this->load->model('user_model');
		$filter = $this->uri->uri_to_assoc(3);

		$filter['campaign_type'] = trim($this->input->post('campaign_type'));
		$filter['campaign_name'] = trim($this->input->post('campaign_name'));
		$filter['voucher_name'] = trim($this->input->post('voucher_name'));
		$filter['voucher_sn'] = trim($this->input->post('voucher_sn'));
		$filter['user_name'] = trim($this->input->post('user_name'));

		$release_id = intval($this->input->post('release_id'));
		if($release_id) $filter['release_id'] = $release_id;
		$filter['voucher_status'] = intval($this->input->post('voucher_status'));

		$filter = get_pager_param($filter);
		$data = $this->voucher_model->voucher_list($filter);
		$data['all_type'] = get_pair($this->voucher_config, 'code', 'name');
		
		$user_ids = array_keys(index_array($data['list'], 'user_id'));
		$this->load->vars('all_user', index_array($this->user_model->all_user(array('user_id'=>$user_ids)),'user_id'));
		
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('voucher/query', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('voucher/query', $data);

	}

	// 新增现金券活动
	public function add()
	{
		auth('voucher_campaign_edit');
		$this->load->model('category_model');
		$this->load->model('brand_model');
                $this->load->model('provider_model');
		$this->load->helper('category');

		$this->load->vars('voucher_config', $this->voucher_config);
		$this->load->vars('all_status', $this->campaign_status_list);
		$this->load->vars('all_category', category_tree($this->category_model->all_category()));
		$this->load->vars('all_brand', $this->brand_model->all_brand());
        $this->load->vars('all_provider', $this->provider_model->all_provider_coop());
		$this->load->view('voucher/add');
	}

	public function proc_add()
	{
		auth('voucher_campaign_edit');
		$update = array();
		$update['campaign_type'] = trim($this->input->post('campaign_type'));
		$update['campaign_name'] = trim($this->input->post('campaign_name'));
		$update['start_date'] = trim($this->input->post('start_date'));
		$update['end_date'] = trim($this->input->post('end_date'));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['desc'] = trim($this->input->post('desc'));
		$category_ids = $this->input->post('category_ids');
		$update['category'] = is_array($category_ids) ? implode(',',$category_ids):'';
		$brand_ids = $this->input->post('brand_ids');
		$update['brand'] = is_array($brand_ids) ? implode(',',$brand_ids):'';
                $provider_ids = $this->input->post('provider_ids');
		//$update['provider'] = is_array($provider_ids) ? implode(',',$provider_ids):'';
                $update['provider'] = $provider_ids;
		$product_ids = $this->input->post('product_ids');
		$update['product'] = is_array($product_ids) ? implode(',',$product_ids):'';
		if($update['product']) $update['category'] = $update['brand'] = $update['provider'] = '';
		
		if(!isset($this->voucher_config[$update['campaign_type']])) sys_msg('活动类型错误', 1);
		
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = $this->time;

		$campaign_id = $this->voucher_model->insert_campaign($update);

		sys_msg('操作成功',0, array(array('text'=>'继续编辑', 'href'=>'voucher/edit/'.$campaign_id), array('text'=>'返回列表', 'href'=>'voucher')));
	}

	public function edit($campaign_id)
	{
		auth(array('voucher_campaign_edit','voucher_campaign_view'));
		$this->load->model('category_model');
		$this->load->model('brand_model');
                $this->load->model('provider_model');
		$this->load->model('admin_model');
		$this->load->helper('category');
		$this->load->helper('product');

		$campaign = $this->voucher_model->filter_campaign(array('campaign_id'=>$campaign_id));
		if (!$campaign) {
			sys_msg('记录不存在', 1);
		}
		$perms = get_voucher_campaign_perm($campaign);
		$campaign->category = explode(',',$campaign->category);
		$campaign->brand = explode(',',$campaign->brand);
		$campaign->product = explode(',',$campaign->product);
        //$campaign->provider = explode(',',$campaign->provider);
                $campaign->provider = $campaign->provider;
		$product_list = $this->voucher_model->product_list($campaign->product);
		attach_gallery($product_list);
		attach_sub($product_list);
		$release_list = $this->voucher_model->all_release(array('campaign_id'=>$campaign_id));
		$admin_ids = array($campaign->create_admin, $campaign->audit_admin, $campaign->stop_admin);
		foreach($release_list as $release) {
			$admin_ids[] = $release->create_admin;
			$admin_ids[] = $release->audit_admin;
			$admin_ids[] = $release->back_admin;
		}
		$admin_ids = array_unique($admin_ids);
		$this->load->vars('campaign', $campaign);
		$this->load->vars('perms', $perms);
		$this->load->vars('config', $this->voucher_config[$campaign->campaign_type]);
		$this->load->vars('all_status', $this->campaign_status_list);
		$this->load->vars('all_category', category_tree($this->category_model->all_category()));
		$this->load->vars('all_brand', $this->brand_model->all_brand());
        $this->load->vars('all_provider', $this->provider_model->all_provider_coop());
		$this->load->vars('product_list', $product_list);
		$this->load->vars('all_admin', $this->admin_model->all_admin(array('admin_id'=>$admin_ids)));
		$this->load->vars('release_list', $release_list);
		$this->load->vars('all_release_status', $this->release_status_list);
		$this->load->view('voucher/edit');
	}

	public function proc_edit()
	{
		auth('voucher_campaign_edit');
		$campaign_id = intval($this->input->post('campaign_id'));
		$this->db->trans_begin();
		$campaign = $this->voucher_model->lock_campaign($campaign_id);
		if(!$campaign) sys_msg('记录不存在', 1);
		$perms = get_voucher_campaign_perm($campaign);
		if(!$perms['edit']) sys_msg('无权限', 1);

		$update['campaign_name'] = trim($this->input->post('campaign_name'));
		$update['start_date'] = trim($this->input->post('start_date'));
		$update['end_date'] = trim($this->input->post('end_date'));
		$update['sort_order'] = intval($this->input->post('sort_order'));
		$update['desc'] = trim($this->input->post('desc'));
		$category_ids = $this->input->post('category_ids');
		$update['category'] = is_array($category_ids) ? implode(',',$category_ids):'';
		$brand_ids = $this->input->post('brand_ids');
		$update['brand'] = is_array($brand_ids) ? implode(',',$brand_ids):'';
                $provider_ids = $this->input->post('provider_ids');
                $update['provider'] = $provider_ids;
		//$update['provider'] = is_array($provider_ids) ? implode(',',$provider_ids):'';
		if($campaign->product) $update['category'] = $update['brand'] = $update['provider'] = '';

		$this->voucher_model->update_campaign($update, $campaign_id);

		$this->db->trans_commit();
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑', 'href'=>'voucher/edit/'.$campaign_id),array('text'=>'返回列表', 'href'=>'voucher')));
	}

	public function delete($campaign_id)
	{
		auth('voucher_campaign_edit');
		$test = $this->input->post('test');
		$campaign_id = intval($campaign_id);
		if(!$test){
			$this->db->trans_begin();
			$campaign = $this->voucher_model->lock_campaign($campaign_id);
		}else{
			$campaign = $this->voucher_model->filter_campaign(array('campaign_id'=>$campaign_id));
		}
		
		if(!$campaign) sys_msg('记录不存在');
		$perms = get_voucher_campaign_perm($campaign);
		if(!$perms['delete']) sys_msg('没有权限', 1);
		if($test) sys_msg('',0);
		$this->voucher_model->delete_campaign($campaign_id);
		$this->voucher_model->delete_release_where(array('campaign_id'=>$campaign_id));
		$this->db->trans_commit();
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'voucher')));
	}

	public function operate()
	{
		$campaign_id = intval($this->input->post('campaign_id'));
		$operation = trim($this->input->post('operation'));
		$stop_reason = trim($this->input->post('stop_reason'));
		$this->db->trans_begin();
		$campaign = $this->voucher_model->lock_campaign($campaign_id);
		if(!$campaign) sys_msg('记录不存在', 1);
		$perms = get_voucher_campaign_perm($campaign);
		if(empty($perms[$operation])) sys_msg('没有权限', 1);
		switch ($operation) {
			case 'audit':
				$update = array(
					'campaign_status' => 1,
					'audit_admin' => $this->admin_id,
					'audit_date' => $this->time
				);				
				break;

			case 'stop':
				if(empty($stop_reason)) sys_msg('请填写停止理由', 1);
				$update = array(
					'campaign_status' => 2,
					'stop_admin' => $this->admin_id,
					'stop_date' => $this->time,
					'stop_reason' => $stop_reason
				);
				$this->voucher_model->delete_release_where(array('campaign_id'=>$campaign_id,'release_status'=>0));
				break;
					
			default:
				sys_msg('参数错误', 1);
				break;
		}
		$this->voucher_model->update_campaign($update,$campaign_id);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function search_product()
	{
		auth('voucher_campaign_edit');
		$this->load->model('product_model');
		$this->load->helper('product');
		$filter = array();
		$filter['product_ids'] = array();
		$campaign_id = intval($this->input->post('campaign_id'));
		if ($campaign_id) {
			$campaign = $this->voucher_model->filter_campaign(array('campaign_id'=>$campaign_id));
			if ($campaign && $campaign->product) {
				$filter['product_ids'] = explode(',', $campaign->product);
			}
		} else {
			$product_ids = trim($this->input->post('product_ids'));
			if($product_ids) $filter['product_ids'] = explode('|', $product_ids);
		}
		
		$filter['product_sn'] = trim($this->input->post('product_sn'));
		$filter['category_id'] = intval($this->input->post('category_id'));
		$filter['brand_id'] = intval($this->input->post('brand_id'));
		$filter['min_price'] = floatval($this->input->post('min_price'));
		$filter['max_price'] = floatval($this->input->post('max_price'));

		$filter = get_pager_param($filter);
		$data = $this->voucher_model->product_search($filter);
		attach_gallery($data['list']);
		attach_sub($data['list']);

		$data['content'] = $this->load->view('voucher/product_search', $data, TRUE);
		$data['error'] = 0;
		unset($data['list']);
		echo json_encode($data);
		return;
	}

	public function add_product()
	{
		auth('voucher_campaign_edit');
		$this->load->helper('product');
		$campaign_id = intval($this->input->post('campaign_id'));
		$product_ids = trim($this->input->post('product_ids'));
		if (empty($product_ids)) {
			sys_msg('请选择商品', 1);
		}
		if ($campaign_id) {
			$campaign = $this->voucher_model->filter_campaign(array('campaign_id'=>$campaign_id));
			if(!$campaign) sys_msg('活动不存在', 1);
			$perms = get_voucher_campaign_perm($campaign);
			if(!$perms['edit']) sys_msg('没有权限', 1);
			$old_product_ids = $campaign->product;
		} else {
			$old_product_ids = str_replace('|',',',trim($this->input->post('old_product_ids')));
		}

		$ids = array();
		$old_product_ids = explode(',', $old_product_ids);
		$product_ids = explode('|', $product_ids);
		foreach($old_product_ids as $id){
			$id = intval($id);
			if($id>0) $ids[$id] = 1;
		}
		foreach ($product_ids as $id) {
			$id = intval($id);
			if($id>0) $ids[$id] = 1;
		}
		$ids = array_keys($ids);
		if($campaign_id){
			$this->voucher_model->update_campaign(array('product'=>implode(',',$ids),'category'=>'','brand'=>''), $campaign_id);
		}

		$product_list = $this->voucher_model->product_list($ids);
		attach_sub($product_list);
		attach_gallery($product_list);
		$this->load->vars('product_list', $product_list);
		$result = array('err'=>0,'msg'=>'');
		$result['product_list'] = $this->load->view('voucher/product_list','',TRUE);
		print json_encode($result);
	}

	public function remove_product()
	{
		auth('voucher_campaign_edit');
		$campaign_id = intval($this->input->post('campaign_id'));
		$product_id = intval($this->input->post('product_id'));
		$campaign = $this->voucher_model->filter_campaign(array('campaign_id'=>$campaign_id));
		if(!$campaign) sys_msg('活动不存在', 1);
		$perms = get_voucher_campaign_perm($campaign);
		if(!$perms['edit']) sys_msg('没有权限', 1);
		$ids = $campaign->product;
		$ids = ','.$ids.',';
		$ids = str_replace(','.$product_id.',',',',$ids);
		$ids = substr($ids, 1, -1);
		$this->voucher_model->update_campaign(array('product'=>$ids), $campaign_id);
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function add_release($campaign_id)
	{
		auth('voucher_release_edit');
		$campaign = $this->voucher_model->filter_campaign(array('campaign_id'=>$campaign_id));
		if (!$campaign) sys_msg('活动不存在', 1);
		$perms = get_voucher_campaign_perm($campaign);
		if(!$perms['release']) sys_msg('没有权限');

		$this->load->vars('campaign', $campaign);
		$this->load->vars('config', $this->voucher_config[$campaign->campaign_type]);
		$this->load->vars('release_rules', $this->release_rules);
		$this->load->view('voucher/add_release');
	}

	public function proc_add_release()
	{
		auth('voucher_release_edit');
		$update['campaign_id'] = intval($this->input->post('campaign_id'));
		$this->db->trans_begin();
		$campaign = $this->voucher_model->lock_campaign($update['campaign_id']);
		if (!$campaign) sys_msg('活动不存在', 1);
		$perms = get_voucher_campaign_perm($campaign);
		if (!$perms['release']) sys_msg('没有权限');
		$config = $this->voucher_config[$campaign->campaign_type];

		$update['voucher_name'] = trim($this->input->post('voucher_name'));
		$update['voucher_amount'] = fix_price($this->input->post('voucher_amount'));
		$update['min_order'] = max(fix_price($this->input->post('min_order')),0);
		$update['start_date'] = trim($this->input->post('start_date')).' '.trim($this->input->post('start_time'));
		$update['end_date'] = trim($this->input->post('end_date')).' '.trim($this->input->post('end_time'));
		$update['expire_days'] = $config['sys']?max(intval($this->input->post('expire_days')),1):0;
		$update['repeat_number'] = max(intval($this->input->post('repeat_number')),1);
		$update['worth'] = $config['worth']?max(intval($this->input->post('worth')),0):0;
		$update['release_rules'] = get_voucher_release_rule($config);
		$update['release_note'] = trim($this->input->post('release_note'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = $this->time;
		// validation
		if (!$update['voucher_name']) sys_msg('请填写现金券描述', 1);
		if ($update['voucher_amount']<=0) sys_msg('请填写现金券金额', 1);
		if (!$config['sys'] && !is_datetime_string($update['start_date'])) sys_msg('请填写有效期开始时间', 1);
		if (!$config['sys'] && !is_datetime_string($update['end_date'])) sys_msg('请填写有效期结束时间', 1);
		if (!$config['sys'] && ($update['end_date']<=$update['start_date'])) sys_msg('有效期错误', 1);
		
		$release_id = $this->voucher_model->insert_release($update);
		if ($config['logo']) {
			$base_dir = CREATE_IMAGE_PATH.'voucher/';
			$sub_dir = floor($update['campaign_id']/1000);
			if(!file_exists($base_dir.$sub_dir.'/')) @mkdir($base_dir.$sub_dir.'/');
			$this->load->library('upload');
			$this->upload->initialize(array(
				'upload_path' => $base_dir.$sub_dir,
				'allowed_types' => 'jpg|gif|png',
				'encrypt_name' => TRUE
			));
			if ($this->upload->do_upload('logo')) {
				$file = $this->upload->data();
				$this->voucher_model->update_release(array('logo'=>'voucher/'.$sub_dir.'/'.$file['file_name']), $release_id);
			}
		}
		$this->db->trans_commit();
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑', 'href'=>'voucher/edit_release/'.$release_id),array('text'=>'返回活动', 'href'=>'voucher/edit/'.$update['campaign_id'])));
	}

	public function edit_release($release_id)
	{
		auth(array('voucher_release_edit','voucher_release_view'));
		$this->load->model('admin_model');
		$release_id = intval($release_id);
		$release = $this->voucher_model->filter_release(array('release_id'=>$release_id));
		if (!$release) sys_msg('记录不存在', 1);
		$campaign = $this->voucher_model->filter_campaign(array('campaign_id'=>$release->campaign_id));
		if (!$campaign) sys_msg('活动不存在', 1);
		$perms = get_voucher_release_perm($release);
		$release->release_rules = unserialize($release->release_rules);
		$this->load->vars('campaign', $campaign);
		$this->load->vars('release', $release);
		$this->load->vars('config', $this->voucher_config[$campaign->campaign_type]);
		$this->load->vars('perms', $perms);
		$this->load->vars('release_rules', $this->release_rules);
		$this->load->vars('all_admin', $this->admin_model->all_admin(array('admin_id'=>array($release->create_admin, $release->audit_admin, $release->back_admin))));
		$this->load->view('voucher/edit_release');
	}

	public function proc_edit_release()
	{
		auth('voucher_release_edit');
		$release_id = intval($this->input->post('release_id'));
		$this->db->trans_begin();
		$release = $this->voucher_model->lock_release($release_id);
		if (!$release) sys_msg('记录不存在', 1);
		$perms = get_voucher_release_perm($release);
		if (!$perms['edit']) sys_msg('没有权限');
		$campaign = $this->voucher_model->filter_campaign(array('campaign_id'=>$release->campaign_id));
		if (!$campaign) sys_msg('活动不存在', 1);	
		$config = $this->voucher_config[$campaign->campaign_type];

		$update['voucher_name'] = trim($this->input->post('voucher_name'));
		$update['voucher_amount'] = fix_price($this->input->post('voucher_amount'));
		$update['min_order'] = max(fix_price($this->input->post('min_order')),0);
		$update['start_date'] = trim($this->input->post('start_date')).' '.trim($this->input->post('start_time'));
		$update['end_date'] = trim($this->input->post('end_date')).' '.trim($this->input->post('end_time'));
		$update['expire_days'] = $config['sys']?max(intval($this->input->post('expire_days')),1):0;
		$update['repeat_number'] = max(intval($this->input->post('repeat_number')),1);
		$update['worth'] = $config['worth']?max(intval($this->input->post('worth')),0):0;
		$update['release_rules'] = get_voucher_release_rule($config);
		$update['release_note'] = trim($this->input->post('release_note'));
		// validation
		if (!$update['voucher_name']) sys_msg('请填写现金券描述', 1);
		if ($update['voucher_amount']<=0) sys_msg('请填写现金券金额', 1);
		if (!$config['sys'] && !is_datetime_string($update['start_date'])) sys_msg('请填写有效期开始时间', 1);
		if (!$config['sys'] && !is_datetime_string($update['end_date'])) sys_msg('请填写有效期结束时间', 1);
		if (!$config['sys'] && ($update['end_date']<=$update['start_date'])) sys_msg('有效期错误', 1);
		
		$base_dir = CREATE_IMAGE_PATH.'voucher/';
		if($this->input->post('delete_logo') && $release->logo) {
			@unlink(CREATE_IMAGE_PATH.$release->logo); 
			$update['logo'] = '';
		}
		
		if ($config['logo']) {
			$sub_dir = floor($release->campaign_id/1000);
			if(!file_exists($base_dir.$sub_dir.'/')) @mkdir($base_dir.$sub_dir.'/');
			$this->load->library('upload');
			$this->upload->initialize(array(
				'upload_path' => $base_dir.$sub_dir,
				'allowed_types' => 'jpg|gif|png',
				'encrypt_name' => TRUE
			));
			if ($this->upload->do_upload('logo')) {
				if($release->logo) {
					@unlink($base_dir.$release->logo); 
					$update['logo'] = '';
				}
				$file = $this->upload->data();
				$update['logo'] = 'voucher/'.$sub_dir.'/'.$file['file_name'];
			}
		}
		$this->voucher_model->update_release($update, $release_id);
		$this->db->trans_commit();
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑', 'href'=>'voucher/edit_release/'.$release_id),array('text'=>'返回活动', 'href'=>'voucher/edit/'.$release->campaign_id)));
	}

	public function operate_release()
	{
		$release_id = intval($this->input->post('release_id'));
		$operation = trim($this->input->post('operation'));
		$back_note = trim($this->input->post('back_note'));
		$this->db->trans_begin();
		$release_id = intval($this->input->post('release_id'));
		$this->db->trans_begin();
		$release = $this->voucher_model->lock_release($release_id);
		if (!$release) sys_msg('记录不存在', 1);
		$perms = get_voucher_release_perm($release);
		if (empty($perms[$operation])) sys_msg('没有权限');
		$campaign = $this->voucher_model->filter_campaign(array('campaign_id'=>$release->campaign_id));
		if (!$campaign) sys_msg('活动不存在', 1);	
		$config = $this->voucher_config[$campaign->campaign_type];

		switch ($operation) {
			case 'audit':
				$update = array(
					'release_status' => 1,
					'audit_admin' => $this->admin_id,
					'audit_date' => $this->time
				);
				$update['voucher_count'] = $this->voucher_model->do_release($release, $config);
				break;

			case 'back':
				if(empty($back_note)) sys_msg('请填写停止理由', 1);
				$update = array(
					'release_status' => 2,
					'back_admin' => $this->admin_id,
					'back_date' => $this->time,
					'back_note' => $back_note
				);
				$update['voucher_count'] = $this->voucher_model->back_release($release_id);
				break;
					
			default:
				sys_msg('参数错误', 1);
				break;
		}
		$this->voucher_model->update_release($update,$release_id);
		$this->db->trans_commit();
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function delete_release($release_id)
	{
		auth('voucher_release_edit');
		$test = $this->input->post('test');
		if(!$test){
			$this->db->trans_begin();
			$release = $this->voucher_model->lock_release($release_id);
		}else{
			$release = $this->voucher_model->filter_release(array('release_id'=>$release_id));
		}		
		if(!$release) sys_msg('记录不存在', 1);
		$perms = get_voucher_release_perm($release);
		if(!$perms['delete']) sys_msg('没有权限或不可删除', 1);
		if($test) sys_msg('',0);
		$this->voucher_model->delete_release($release_id);
		$this->db->trans_begin();
		if($release->logo) @unlink(CREATE_IMAGE_PATH.$release->logo);
		sys_msg('操作成功', 0);
	}
	
        /*
         * 导出指定批次发放的现金券，只有发放人才能导出。
         */
        public function export($release_id) {
                auth('voucher_view');
                
                $release_id = intval($release_id);
		$release = $this->voucher_model->filter_release(array('release_id'=>$release_id));
		if (!$release) {
                    sys_msg('记录不存在！', 1);
                }
                if ($release->audit_admin != $this->admin_id) {
                    sys_msg('只有发放人才能导出！', 1);
                }
                
                $voucher_list = $this->voucher_model->release_voucher_list($release_id);
                // 现金券全部类型
		$voucher_types = get_pair($this->voucher_config, 'code', 'name');
                foreach ($voucher_list as $key => $row) {
                    // 设置使用情况
                    $row['used_number'] = $row['used_number']." / ".$row['repeat_number'];
                    unset($row['repeat_number']);
                    // 设置现金券类型
                    $row['campaign_type'] = $voucher_types[$row['campaign_type']];
                    // 设置使用用户信息
                    if ($row['email'] || $row['mobile']) {
                        $row['email'] = $row['email']." / ".$row['mobile'];
                    }
                    unset($row['mobile']);
                    // 设置有效期
                    $row['start_date'] = $row['start_date']."至".$row['end_date'];
                    unset($row['end_date']);
                    
                    $voucher_list[$key] = $row;
                }
		
                $title = array('编号', '现金券编号', '现金券金额', '现金券描述', '生成时间', '使用情况', '活动名称', '活动类型', 'Email/手机', '最小订单金额', '有效期');
        
                $this->load->helpers('excel');
                export_excel_xml($release_id, array($title, $voucher_list));
        }
        
}
