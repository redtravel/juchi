<?php
class Order_advice extends CI_Controller 
{
    public function __construct ()
    {
            parent::__construct();
            $this->admin_id = $this->session->userdata('admin_id');
            if ( ! $this->admin_id )
            {
                redirect('index/login');
            }
            $this->load->model('order_advice_model');
    }

    public function index ()
    {
        $filter = get_pager_param(array());
        //查询
        $data = $this->order_advice_model->query($filter);
        if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('order_advice/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('order_advice/index', $data);
    }
}
?>
