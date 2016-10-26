<?php
#doc
#	classname:	Brand
#	scope:		PUBLIC
#
#/doc

class Campaign extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('campaign_model');
	}
	
	public function index ()
	{
        auth(array('campaign_view','campaign_edit'));
        $campaign_type =array(1=>'其他',2=>'免邮',3=>'抢购');
		$filter = $this->uri->uri_to_assoc(3);
		$campaign_name = trim($this->input->post('campaign_name'));
		if (!empty($campaign_name)) $filter['campaign_name'] = $campaign_name;
                $start_time = trim($this->input->post('start_time'));
		if (!empty($start_time)) $filter['start_time'] = $start_time;
                $end_time = trim($this->input->post('end_time'));
		if (!empty($end_time)) $filter['end_time'] = $end_time;

		$filter = get_pager_param($filter);
		$data = $this->campaign_model->campaign_list($filter);
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('campaign/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;

		$this->load->vars('campaign_type', $campaign_type);
		$this->load->view('campaign/index', $data);
	}
        
    function sel_product(){
        $pro = $this->input->post('pro');
        if($pro == ''){
            echo json_encode(array('type'=>4));
            exit;
        }
        $this->load->model('liuyan_model');
        $list = $this->campaign_model->product($pro);
        if(empty($list)){
            $list = $this->campaign_model->productsn($pro);
        }
        echo json_encode(array('type'=>1 , 'list' => $list));
    }

	public function add($campaign_type = "")
	{
        auth(array('campaign_edit'));
        $this->load->vars('campaign_type', $campaign_type);
        $this->load->view('campaign/add');
	}

	public function proc_add()
	{
        auth(array('campaign_edit'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('campaign_name', '活动名称', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['campaign_type'] = $this->input->post('campaign_type');;
        $update['campaign_name'] = $this->input->post('campaign_name');
        $update['limit_price'] = $this->input->post('limit_price');
		$update['product_id'] = $this->input->post('tag_id');
		$update['start_date'] = $this->input->post('start_time');
        $update['end_date'] = $this->input->post('end_time');
		$update['is_use'] = intval($this->input->post('is_use'));
		$update['create_admin'] = $this->admin_id;
		$update['create_date'] = date('Y-m-d H:i:s');

		$campaign_id = $this->campaign_model->insert($update);
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'campaign/edit/'.$campaign_id), array('text'=>'返回列表','href'=>'campaign/index')));
	}

	public function edit($campaign_id)
	{
        auth(array('campaign_edit'));
        $campaign_id = intval($campaign_id);
		$campaign = $this->campaign_model->filter(array('campaign_id'=>$campaign_id));
		if (!$campaign) {
			sys_msg('记录不存在', 1);
		}
        $this->load->model('product_model');
        $pro_arr = $this->product_model->filter(array('product_id' => $campaign->product_id));
        $this->load->vars('pro_arr', $pro_arr);
		$this->load->vars('cam_arr', $campaign);
		$this->load->view('campaign/edit');
	}

	public function proc_edit($campaign_id)
	{
        auth(array('campaign_edit'));
        $campaign_id = intval($campaign_id);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('campaign_name', '活动名称', 'trim|required');
		$this->form_validation->set_rules('limit_price', '最小金额', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
        $update['campaign_name'] = $this->input->post('campaign_name');
		$update['limit_price'] = $this->input->post('limit_price');
		$update['product_id'] = $this->input->post('tag_id');
		$update['start_date'] = $this->input->post('start_time');
        $update['end_date'] = $this->input->post('end_time');
		$update['is_use'] = intval($this->input->post('is_use'));

		$this->campaign_model->update($update,$campaign_id);
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'campaign/edit/'.$campaign_id), array('text'=>'返回列表','href'=>'campaign/index')));
	}

	public function delete($campaign_id)
	{
        auth(array('campaign_edit'));
		$campaign_id = intval($campaign_id);
		$test = $this->input->post('test');
		$campaign = $this->campaign_model->filter(array('campaign_id'=>$campaign_id));
		if (empty ($campaign)) {
			sys_msg('记录不存在', 1);
		}
		if($test) sys_msg('');
		$this->campaign_model->delete($campaign_id);
		sys_msg('操作成功', 0, array('text'=>'返回列表', 'href'=>'campaign/index'));		
	}


}
###