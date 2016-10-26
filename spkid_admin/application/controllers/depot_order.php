<?php
#doc
#	classname:	Depot_order
#	scope:		PUBLIC
#
#/doc

class Depot_order extends CI_Controller
{
	public function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if ( ! $this->admin_id )
		{
			redirect('index/login');
		}
		$this->load->model('depot_order_model');
		$this->load->model('shipping_model');
	}

	public function index(){
        auth('depot_order_view');
        //组装参数
		$filter1 = array();
        $create_date_start =  trim($this->input->post('create_date_start'));
        if (!empty($create_date_start)) $filter1['create_date_start'] = $create_date_start;
        $create_date_end =  trim($this->input->post('create_date_end'));
        if (!empty($create_date_end)) $filter1['create_date_end'] = $create_date_end;
        $order_sn =  trim($this->input->post('order_sn'));
        if (!empty($order_sn)) $filter1['order_sn'] = $order_sn;
        $order_status =  intval($this->input->post('order_status'));
        if (!empty($order_status)) $filter1['order_status'] = $order_status;
        $confirm_date_start =  trim($this->input->post('confirm_date_start'));
        if (!empty($confirm_date_start)) $filter1['confirm_date_start'] = $confirm_date_start;
        $confirm_date_end =  trim($this->input->post('confirm_date_end'));
        if (!empty($confirm_date_end)) $filter1['confirm_date_end'] = $confirm_date_end;
        $shipping_id = intval($this->input->post('shipping_id'));
        if (!empty($shipping_id)) $filter1['shipping_id'] = $shipping_id;
        $shipping_status =  intval($this->input->post('shipping_status'));
        $filter1['shipping_status'] = $shipping_status;
        //已拣货(is_pick=1)/已复核(is_qc=1)/已发货(shipping_status=1);
        if($shipping_status != 0){
            $filter1['is_all'] = 0;
            if($shipping_status == -1) {
                $filter1['is_pick'] = 0;
                $filter1['is_qc'] = 0;
                $filter1['is_shipping'] = 0;
            }
            if($shipping_status == 1) {
                $filter1['is_pick'] = 1;
                $filter1['is_qc'] = 0;
                $filter1['is_shipping'] = 0;
            }
            if($shipping_status == 2) {
                $filter1['is_pick'] = 1;
                $filter1['is_qc'] = 1;
                $filter1['is_shipping'] = 0;
            }
            if($shipping_status == 3) {
                $filter1['is_pick'] = 1;
                $filter1['is_qc'] = 1;
                $filter1['is_shipping'] = 1;
            }
        }else{
            $filter1['is_all'] = 1;
        }
        //做分页
        $filter1['sort_by'] = 'oi.order_id';
		$filter1['sort_order'] ='desc';
        $filter = get_pager_param($filter1);
        //查询
        $data = $this->depot_order_model->order_list($filter);
        //快递方式
        $shipping_list = $this->shipping_model->all_shipping();
		$this->load->vars('shipping_list',$shipping_list);
        $shipping = array();
        $shipping[0] = '无';
        foreach($shipping_list as $s){
            $shipping[$s->shipping_id] = $s->shipping_name;
        }
		$this->load->vars('shipping',$shipping);
        //返回页面
        if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('depot/order/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('depot/order/index', $data);
    }
}
###