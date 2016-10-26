<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Data_alert extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('data_alert_model');
    }

    public function index(){
        auth('alert_view');
        $filter = $this->uri->uri_to_assoc();
        $start_date = $this->input->post("start_date");
        if(!empty($start_date)) $filter['start_date'] = $start_date;
        $end_date = $this->input->post("end_date");
        if(!empty($end_date)) $filter['end_date'] = $end_date;
        $status = $this->input->post("status");
        $filter['status'] = empty($status)?'-1':$status;
        $filter['status'] = $filter['status'] ==9?'0':$filter['status'];

        $filter = get_pager_param($filter);
        $data = $this->data_alert_model->alert_index($filter);
        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('alert/alert_list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('alert/alert_list', $data);
    }

    public function check()
    {
        auth('alert_view');
        $admin_id = $this->session->userdata('admin_id');
        $data = $this->data_alert_model->alert_check($admin_id);
        $this->load->view('alert/check',$data);
    }

    public function read($sys_log_id){
        auth('alert_view');
        $sys_log_id = intval($sys_log_id);
        $check = $this->data_alert_model->filter(array('sys_log_id' => $sys_log_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->vars('date_insert',$check->date_insert);
        $this->load->vars('content',$check->content);
        $this->load->vars('status',$check->status);
        $this->load->view('alert/check');
    }

}