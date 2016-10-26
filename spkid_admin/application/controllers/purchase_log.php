<?php
#doc
#	classname:	Purchase_virtual
#	scope:		PUBLIC
#
#/doc

class Purchase_log extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('purchase_log_model');
	}

	public function index ()
	{
		auth('purchase_log_view');
		$filter = $this->uri->uri_to_assoc(3);
		$start_time = trim($this->input->post('start_time'));
		$end_time = trim($this->input->post('end_time'));
		if(!empty($start_time)) {
			$filter['start_time'] = $start_time . ' 00:00:00';
		}
		if(!empty($end_time)) {
			$filter['end_time'] = $end_time . ' 23:59:59';
		}
		
		$filter = get_pager_param($filter);
		$data = $this->purchase_log_model->find_page($filter);
		
		if ($this->input->is_ajax_request()) {
			
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('purchase/purchase_log_index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		
		$data['full_page'] = TRUE;
		$this->load->view('purchase/purchase_log_index', $data);
	}
	
}
###