<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Report extends CI_Controller
{
    public function __construct ()
    {
            parent::__construct();
            $this->admin_id = $this->session->userdata('admin_id');
            if ( ! $this->admin_id )
            {
                    redirect('index/login');
            }
            $this->load->model('report_model');
    }

    public function finance_report()
    {
    	auth(array('finance_invoicing_su_report','finance_invoicing_de_report'));
    	$this->load->helper('perms_helper');
        $this->load->vars('perms' , get_finance_perm());
		$all_list = $this->report_model->all_finance();
        $this->load->vars('list', $all_list);
        $this->load->vars('report_type', '财务报表');
        $this->load->view('report/report_list');
    }

    public function depot_report()
    {
    	auth(array('invoicing_summary_report','invoicing_details_report'));
    	$this->load->helper('perms_helper');
        $this->load->vars('perms' , get_depot_perm());
		$all_list = $this->report_model->all_depot();
        $this->load->vars('list', $all_list);
        $this->load->vars('report_type', '经销存报表');
        $this->load->view('report/report_list');
    }

    public function order_report()
    {
        auth(array('order_profits_detail_report','order_profits_summary_report','order_profits_summary_report_to', 'order_profits_summary_report_to2'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_order_profits_perm());
        $all_list = $this->report_model->all_order();
        $this->load->vars('list', $all_list);
        $this->load->vars('report_type', '订单利润报表');
        $this->load->view('report/report_list');
    }

    public function finance_invoicing_de_report()
    {
		auth('finance_invoicing_de_report');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_finance_perm());
        $filter = $this->uri->uri_to_assoc(3);
        $category_id = $this->input->post("category_id");
        if(!empty($category_id)) $filter['category_id'] = $category_id;
        $brand_id = $this->input->post("brand_id");
        if(!empty($brand_id)) $filter['brand_id'] = $brand_id;
        $product_sn = $this->input->post("product_sn");
        if(!empty($product_sn)) $filter['product_sn'] = $product_sn;
        
        $product_id = $this->input->post("product_id");
        if(!empty($product_id)) $filter['product_id'] = $product_id;
        
        $keyword = $this->input->post("keyword");
        if(!empty($keyword)) $filter['keyword'] = $keyword;
        $color_id = $this->input->post("color_id");
        if(!empty($color_id)) $filter['color_id'] = $color_id;
        $size_id = $this->input->post("size_id");
        if(!empty($size_id)) $filter['size_id'] = $size_id;
        $cooperation_id = $this->input->post("cooperation_id");
        if(!empty($cooperation_id)) $filter['cooperation_id'] = $cooperation_id;
        $depot_id = $this->input->post("depot_id");
        if(!empty($depot_id)) $filter['depot_id'] = $depot_id;
        //$provider_id = $this->input->post("provider_id");
        //if(!empty($provider_id)) $filter['provider_id'] = $provider_id;

        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;

        //$filter = get_pager_param($filter);
        $data = $this->report_model->finance_invoicing_de_report($filter);
		$data['product_sn']=$product_sn;
                $data['product_id']=$product_id;
		$data['keyword']=$keyword;
		$data['brand_id']=$brand_id;
		$data['category_id']=$category_id;
		$data['color_id']=$color_id;
		$data['size_id']=$size_id;
		$data['start_time']=$start_time;
		$data['end_time']=$end_time;
		$data['cooperation_id']=$cooperation_id;
		$data['depot_id']=$depot_id;
		//$data['provider_id']=$provider_id;

        $this->load->vars('cat_list',$this->report_model->cat_list(0,$category_id));
        $this->load->vars('brand_list',$this->report_model->brand_list());
        $this->load->vars('color_list',$this->report_model->color_list());
        $this->load->vars('size_list',$this->report_model->size_list());
        $this->load->vars('coop_list',$this->report_model->coop_list());
        $this->load->vars('depot_list',$this->report_model->depot_list());
        $this->load->view('report/finance_invoicing_de_report', $data);
    }

    public function finance_invoicing_su_report()
    {
		auth('finance_invoicing_su_report');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_finance_perm());
        $filter = $this->uri->uri_to_assoc(3);
        $category_id = $this->input->post("category_id");
        if(!empty($category_id)) $filter['category_id'] = $category_id;
        $brand_id = $this->input->post("brand_id");
        if(!empty($brand_id)) $filter['brand_id'] = $brand_id;
        $cooperation_id = $this->input->post("cooperation_id");
        if(!empty($cooperation_id)) $filter['cooperation_id'] = $cooperation_id;
        $depot_id = $this->input->post("depot_id");
        if(!empty($depot_id)) $filter['depot_id'] = $depot_id;

        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;

        //$filter = get_pager_param($filter);
        $rclist = $this->report_model->finance_invoicing_su_report($filter);
        $count_arr = $this->report_model->finance_invoicing_su_report_count($rclist);
//print_r($rclist);
		$data['brand_id']=$brand_id;
		$data['category_id']=$category_id;
		$data['start_time']=$start_time;
		$data['end_time']=$end_time;
		$data['cooperation_id']=$cooperation_id;
		$data['depot_id']=$depot_id;
		$data['list']=$rclist;
		$data['count']=$count_arr;


        $this->load->vars('cat_list',$this->report_model->cat_list(0,$category_id));
        $this->load->vars('brand_list',$this->report_model->brand_list());
        $this->load->vars('coop_list',$this->report_model->coop_list());
        $this->load->vars('depot_list',$this->report_model->depot_list());
        $this->load->view('report/finance_invoicing_su_report', $data);
    }

    public function invoicing_details_report()
    {
		auth('invoicing_details_report');
        $this->load->helper('perms_helper');
	$this->load->model('color_model');
	$this->load->model('size_model');
        $this->load->vars('perms' , get_depot_perm());
        $filter = $this->uri->uri_to_assoc(3);
        $category_id = $this->input->post("category_id");
        if(!empty($category_id)) $filter['category_id'] = $category_id;
        $brand_id = $this->input->post("brand_id");
        if(!empty($brand_id)) $filter['brand_id'] = $brand_id;
        $product_sn = $this->input->post("product_sn");
        if(!empty($product_sn)) $filter['product_sn'] = $product_sn;
        $keyword = $this->input->post("keyword");
        if(!empty($keyword)) $filter['keyword'] = $keyword;
        $color_id = $this->input->post("color_id");
        if(!empty($color_id)) $filter['color_id'] = $color_id;
        $size_id = $this->input->post("size_id");
        if(!empty($size_id)) $filter['size_id'] = $size_id;
        $cooperation_id = $this->input->post("cooperation_id");
        if(!empty($cooperation_id)) $filter['cooperation_id'] = $cooperation_id;
        $depot_id = $this->input->post("depot_id");
        if(!empty($depot_id)) $filter['depot_id'] = $depot_id;
        //$provider_id = $this->input->post("provider_id");
        //if(!empty($provider_id)) $filter['provider_id'] = $provider_id;

        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;

        //$filter = get_pager_param($filter);
        $data['list'] = $this->report_model->invoicing_details_report($filter);
        $data['count'] = $this->report_model->invoicing_details_count($data['list']);
		$data['product_sn']=$product_sn;
		$data['keyword']=$keyword;
		$data['brand_id']=$brand_id;
		$data['category_id']=$category_id;
		$data['color_id']=$color_id;
		$data['size_id']=$size_id;
		$data['start_time']=$start_time;
		$data['end_time']=$end_time;
		$data['cooperation_id']=$cooperation_id;
		$data['depot_id']=$depot_id;
		//$data['provider_id']=$provider_id;

        $this->load->vars('cat_list',$this->report_model->cat_list(0,$category_id));
        $this->load->vars('brand_list',$this->report_model->brand_list());
        $this->load->vars('color_list',$this->color_model->color_list());
        $this->load->vars('size_list',$this->size_model->size_list());
        $this->load->vars('coop_list',$this->report_model->coop_list());
        $this->load->vars('depot_list',$this->report_model->depot_list());
        $this->load->view('report/invoicing_details_report', $data);
    }

	public function invoicing_summary_report()
    {
		auth('invoicing_summary_report');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_depot_perm());
        $filter = $this->uri->uri_to_assoc(3);
        $category_id = $this->input->post("category_id");
        if(!empty($category_id)) $filter['category_id'] = $category_id;
        $brand_id = $this->input->post("brand_id");
        if(!empty($brand_id)) $filter['brand_id'] = $brand_id;
        $cooperation_id = $this->input->post("cooperation_id");
        if(!empty($cooperation_id)) $filter['cooperation_id'] = $cooperation_id;
        $depot_id = $this->input->post("depot_id");
        if(!empty($depot_id)) $filter['depot_id'] = $depot_id;

        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;

        //$filter = get_pager_param($filter);
        $rclist = $this->report_model->invoicing_summary_report($filter);
        $count_arr = $this->report_model->invoicing_summary_report_count($rclist);

		$data['brand_id']=$brand_id;
		$data['category_id']=$category_id;
		$data['start_time']=$start_time;
		$data['end_time']=$end_time;
		$data['cooperation_id']=$cooperation_id;
		$data['depot_id']=$depot_id;
		$data['list']=$rclist;
		$data['count']=$count_arr;

        $this->load->vars('cat_list',$this->report_model->cat_list(0,$category_id));
        $this->load->vars('brand_list',$this->report_model->brand_list());
        $this->load->vars('coop_list',$this->report_model->coop_list());
        $this->load->vars('depot_list',$this->report_model->depot_list());
        $this->load->view('report/invoicing_summary_report', $data);
    }

    public function depot_real_inventory_report()
    {
		auth('depot_real_inventory_report');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_depot_perm());
        $filter = $this->uri->uri_to_assoc(3);

        $category_id = $this->input->post("category_id");
        if(!empty($category_id)) $filter['category_id'] = $category_id;
        $brand_id = $this->input->post("brand_id");
        if(!empty($brand_id)) $filter['brand_id'] = $brand_id;

        $depot_id = $this->input->post("depot_id");
        if(!empty($depot_id)) $filter['depot_id'] = $depot_id;

        $location_name = $this->input->post("location_name");
        if(!empty($location_name)) $filter['location_name'] = $location_name;

        $onsale_id = ($this->input->post("onsale_id")!==FALSE)?$this->input->post("onsale_id"):-1;
        $filter['onsale_id'] = $onsale_id;

        $product_sn = $this->input->post("product_sn");
        if(!empty($product_sn)) $filter['product_sn'] = $product_sn;
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;
        //$filter = get_pager_param($filter);
	if ( !empty($filter) )
        $data = $this->report_model->depot_real_inventory_report($filter);

		$data['brand_id']=$brand_id;
		$data['category_id']=$category_id;
		$data['location_name']=$location_name;
		$data['depot_id']=$depot_id;
		$data['onsale_id']=$onsale_id;
		$data['product_sn']=$product_sn;
                $data['start_time']=$start_time;
                $data['end_time']=$end_time;

        $this->load->vars('cat_list',$this->report_model->cat_list(0,$category_id));
        $this->load->vars('brand_list',$this->report_model->brand_list());
        $this->load->vars('depot_list',$this->report_model->depot_list());
        $this->load->vars('onsale_list',$this->report_model->onsale_list());
        $this->load->view('report/depot_real_inventory_report', $data);
    }

    public function merge_gather_gross_report()
    {
		auth('merge_gather_gross_report');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_finance_perm());
        $filter = $this->uri->uri_to_assoc(3);
        $category_id = $this->input->post("category_id");
        if(!empty($category_id)) $filter['category_id'] = $category_id;
        $brand_id = $this->input->post("brand_id");
        if(!empty($brand_id)) $filter['brand_id'] = $brand_id;

        $cooperation_id = $this->input->post("cooperation_id");
        if(!empty($cooperation_id)) $filter['cooperation_id'] = $cooperation_id;
        $provider_id = $this->input->post("provider_id");
        if(!empty($provider_id)) $filter['provider_id'] = $provider_id;

        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;

        //$filter = get_pager_param($filter);
        $data = $this->report_model->merge_gather_gross_report($filter);

		$data['brand_id']=$brand_id;
		$data['category_id']=$category_id;
		$data['start_time']=$start_time;
		$data['end_time']=$end_time;
		$data['cooperation_id']=$cooperation_id;
		$data['provider_id']=$provider_id;

        $this->load->vars('cat_list',$this->report_model->cat_list(0,$category_id));
        $this->load->vars('brand_list',$this->report_model->brand_list());
        $this->load->vars('color_list',$this->report_model->color_list());
        $this->load->vars('size_list',$this->report_model->size_list());
        $this->load->vars('coop_list',$this->report_model->coop_list());
        $this->load->vars('provider_list',$this->report_model->provider_list());
        $this->load->view('report/merge_gather_gross_report', $data);
    }


    public function yyw_pv_report() {
        auth('yyw_pv_report');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_yyw_pv_report());
        $data['hourly_pv'] = $this->report_model->get_hourly_pv();
        $data['daily_pv'] = $this->report_model->get_daily_pv();
        $data['weekly_pv'] = $this->report_model->get_weekly_pv();
        $data['monthly_pv'] = $this->report_model->get_monthly_pv();
        $this->load->view('report/pv_report', $data);
    }

    public function order_profits_detail_report()
    {
        auth('order_profits_detail_report');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_order_profits_perm());
        $filter = $this->uri->uri_to_assoc(3);
        $order_sn = $this->input->post("order_sn");
        if(!empty($order_sn)) $filter['order_sn'] = $order_sn;
        $category_id = $this->input->post("category_id");
        if(!empty($category_id)) $filter['category_id'] = $category_id;
        $brand_id = $this->input->post("brand_id");
        if(!empty($brand_id)) $filter['brand_id'] = $brand_id;
        $product_sn = $this->input->post("product_sn");
        if(!empty($product_sn)) $filter['product_sn'] = $product_sn;
        $provider_id = $this->input->post("provider_id");
        if(!empty($provider_id)) $filter['provider_id'] = $provider_id;
        $product_id = $this->input->post("product_id");
        if(!empty($product_id)) $filter['product_id'] = $product_id;        
        $keyword = $this->input->post("keyword");
        if(!empty($keyword)) $filter['keyword'] = $keyword;
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;
        $is_start_time = $this->input->post("is_start_time");
        if(!empty($is_start_time)) $filter['is_start_time'] = $is_start_time;
        $is_end_time = $this->input->post("is_end_time");
        if(!empty($is_end_time)) $filter['is_end_time'] = $is_end_time;

        $data = $this->report_model->order_profits_detail_report($filter);
        $data['order_sn']=$order_sn;
        $data['product_sn']=$product_sn;
        $data['product_id']=$product_id;
        $data['keyword']=$keyword;
        $data['brand_id']=$brand_id;
        $data['provider_id']=$provider_id;
        $data['category_id']=$category_id;
        $data['start_time']=$start_time;
        $data['end_time']=$end_time;
        $data['is_start_time']=$is_start_time;
        $data['is_end_time']=$is_end_time;
        
        if ($this->input->post('export')){
            $data['tag'] = '?';
            $this->load->view('report/order_profits_detail_report_export', $data);
            $file_name = "order_profits_detail_report_export.xls";
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=".$file_name);           
            return;
        }
        
        $this->load->helper('category');
        $this->load->model('category_model');

        $this->load->vars('all_category',category_flatten(category_tree($this->category_model->all_category()),'-- '));
        $this->load->vars('brand_list',$this->report_model->brand_list());
        $this->load->vars('provider_list',$this->report_model->provider_list());
        $this->load->view('report/order_profits_detail_report', $data);
    }

    public function order_profits_return_report()
    {
        auth('order_profits_return_report');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_order_profits_perm());
        $filter = $this->uri->uri_to_assoc(3);
        
        $order_sn = $this->input->post("order_sn");
        if(!empty($order_sn)) $filter['order_sn'] = $order_sn;
        $admin_name = $this->input->post("admin_name");
        if(!empty($admin_name)) $filter['admin_name'] = $admin_name;
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;
        $is_start_time = $this->input->post("is_start_time");
        if(!empty($is_start_time)) $filter['is_start_time'] = $is_start_time;
        $is_end_time = $this->input->post("is_end_time");
        if(!empty($is_end_time)) $filter['is_end_time'] = $is_end_time;

        $data = $this->report_model->order_profits_return_report($filter);
        $data['order_sn']=$order_sn;
        $data['admin_name']=$admin_name;
        $data['start_time']=$start_time;
        $data['end_time']=$end_time;
        $data['is_start_time']=$is_start_time;
        $data['is_end_time']=$is_end_time;
        
	if ($this->input->post('export')){
            $data['tag'] = '?';
            $this->load->view('report/order_profits_return_report_export', $data);
            $file_name = "order_profits_return_report_export.xls";
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=".$file_name);           
            return;
        }        
        $this->load->view('report/order_profits_return_report', $data);
    }

    public function order_profits_summary_report_to()
    {
        auth('order_profits_summary_report_to');
        $this->load->helper('perms_helper');
        $this->load->helper('order_helper');
        $this->load->vars('perms' , get_order_profits_perm());
        $filter = $this->uri->uri_to_assoc(3);
        
        $order_sn = $this->input->post("order_sn");
        if(!empty($order_sn)) $filter['order_sn'] = $order_sn;
        $admin_name = $this->input->post("admin_name");
        if(!empty($admin_name)) $filter['admin_name'] = $admin_name;
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;
        $is_start_time = $this->input->post("is_start_time");
        if(!empty($is_start_time)) $filter['is_start_time'] = $is_start_time;
        $is_end_time = $this->input->post("is_end_time");
        if(!empty($is_end_time)) $filter['is_end_time'] = $is_end_time;
        
        $data = $this->report_model->order_profits_summary_report_to($filter);
        $data['order_sn']=$order_sn;
        $data['admin_name']=$admin_name;
        $data['start_time']=$start_time;
        $data['end_time']=$end_time;
        $data['is_start_time']=$is_start_time;
        $data['is_end_time']=$is_end_time;
        if ($this->input->post('export')){
            $data['tag'] = '?';
            $this->load->view('report/order_profits_summary_report_to_export', $data);
            $file_name = "order_profits_summary_report_to_export.xls";
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=".$file_name);           
            return;
        }
        $this->load->view('report/order_profits_summary_report_to', $data);
    }

    public function order_profits_summary_report_to2()
    {
        auth('order_profits_summary_report_to2');
        $this->load->helper('perms_helper');
        $this->load->helper('order_helper');
        $this->load->vars('perms' , get_order_profits_perm());
        $filter = $this->uri->uri_to_assoc(3);
        
        $order_sn = $this->input->post("order_sn");
        if(!empty($order_sn)) $filter['order_sn'] = $order_sn;
        $admin_name = $this->input->post("admin_name");
        if(!empty($admin_name)) $filter['admin_name'] = $admin_name;
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;
        $is_start_time = $this->input->post("is_start_time");
        if(!empty($is_start_time)) $filter['is_start_time'] = $is_start_time;
        $is_end_time = $this->input->post("is_end_time");
        if(!empty($is_end_time)) $filter['is_end_time'] = $is_end_time;
        
        $data = $this->report_model->order_profits_summary_report_to($filter);
        $data['order_sn']=$order_sn;
        $data['admin_name']=$admin_name;
        $data['start_time']=$start_time;
        $data['end_time']=$end_time;
        $data['is_start_time']=$is_start_time;
        $data['is_end_time']=$is_end_time;
        if ($this->input->post('export')){
            $data['tag'] = '?';
            $this->load->view('report/order_profits_summary_report_to2_export', $data);
            $file_name = "order_profits_summary_report_to2_export.xls";
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=".$file_name);           
            return;
        }
        $this->load->view('report/order_profits_summary_report_to2', $data);
    }    
	//库存明细表
    public function inventory_details_report()
    {
	auth('inventory_details_report');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_finance_perm());
        $filter = $this->uri->uri_to_assoc(3);

        $brand_id = $this->input->post("brand_id");
        if(!empty($brand_id)) $filter['brand_id'] = $brand_id;
        $product_sn = $this->input->post("product_sn");
        if(!empty($product_sn)) $filter['product_sn'] = $product_sn;
        
        $sku = $this->input->post("sku");
        if(!empty($sku)) $filter['sku'] = $sku;
                
        $keyword = $this->input->post("keyword");
        if(!empty($keyword)) $filter['keyword'] = $keyword;
        
        $is_expire_date = $this->input->post("is_expire_date");
        if(!empty($is_expire_date)) $filter['is_expire_date'] = (int)$is_expire_date;
        
        $provider_barcode = $this->input->post("provider_barcode");
        if(!empty($provider_barcode)) $filter['provider_barcode'] = $provider_barcode;
        
        $actual_stock = $this->input->post("actual_stock");
        if(!empty($actual_stock)) $filter['actual_stock'] = $actual_stock;
        
        $avail_stock = $this->input->post("avail_stock");
        if(!empty($avail_stock)) $filter['avail_stock'] = $avail_stock;
        
        $order_stock = $this->input->post("order_stock");
        if(!empty($order_stock)) $filter['order_stock'] = $order_stock;

        //$start_time = $this->input->post("start_time");
        //if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;
        
        $e_start_time = $this->input->post("e_start_time");
        if(!empty($e_start_time)) $filter['e_start_time'] = $e_start_time;
        $e_end_time = $this->input->post("e_end_time");
        if(!empty($e_end_time)) $filter['e_end_time'] = $e_end_time;

        $data = $this->report_model->inventory_details_report($filter);
        
        $data['product_sn']=$product_sn;
        $data['sku']=$sku;
        $data['provider_barcode']=$provider_barcode;
        $data['keyword']=$keyword;
        $data['brand_id']=$brand_id;
        $data['is_expire_date']=$is_expire_date;
        //$data['start_time']=$start_time;
        $data['end_time']=$end_time;
        $data['e_start_time']=$e_start_time;
        $data['e_end_time']=$e_end_time;
        $data['actual_stock']=$actual_stock;
        $data['avail_stock']=$avail_stock;
        $data['order_stock']=$order_stock;
        if ($this->input->post('export')){
            $data['tag'] = '?';
            $this->load->view('report/inventory_details_report_export', $data);
            $file_name = "inventory_details_report_export.xls";
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=".$file_name);           
            return;
        }
        $this->load->vars('brand_list',$this->report_model->brand_list());
        $this->load->view('report/inventory_details_report', $data);
    }

    public function order_profits_sales_report()
    {
        auth('order_profits_sales_report');
        $this->load->helper('perms_helper');
        $this->load->helper('order_helper');
        $this->load->vars('perms' , get_order_profits_perm());
        $filter = $this->uri->uri_to_assoc(3);
        
        $order_sn = $this->input->post("order_sn");
        if(!empty($order_sn)) $filter['order_sn'] = $order_sn;
        $admin_name = $this->input->post("admin_name");
        if(!empty($admin_name)) $filter['admin_name'] = $admin_name;
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;


        $data = $this->report_model->order_profits_sales_report($filter);
        $data['order_sn']=$order_sn;
        $data['admin_name']=$admin_name;
        $data['start_time']=$start_time;
        $data['end_time']=$end_time;
        
	if ($this->input->post('export')){
            $data['tag'] = '?';
            $this->load->view('report/order_profits_sales_report_export', $data);
            $file_name = "order_profits_sales_report_export.xls";
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=".$file_name);           
            return;
        }

        $this->load->view('report/order_profits_sales_report', $data);
    }

    public function purchase_main_report()
    {   
        auth('purchase_main_report');
        $this->load->model('admin_model');
        $this->load->helper('perms_helper');
        $this->load->helper('order_helper');
        $this->load->vars('perms' , get_depot_perm());
        $filter = $this->uri->uri_to_assoc(3);
        
        $batch_code = $this->input->post("batch_code");
        if(!empty($batch_code)) $filter['batch_code'] = $batch_code;
        $purchase_code = $this->input->post("purchase_code");
        if(!empty($purchase_code)) $filter['purchase_code'] = $purchase_code;
        $provider_id = $this->input->post("provider_id");
        if(!empty($provider_id)) $filter['provider_id'] = $provider_id;
        $admin_name = $this->input->post("admin_name");
        if(!empty($admin_name)) $filter['admin_name'] = $admin_name;
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;

        $data = $this->report_model->purchase_main_report($filter);
        $data['batch_code']=$batch_code;
        $data['purchase_code']=$purchase_code;
        $data['admin_name']=$admin_name;
        $data['start_time']=$start_time;
        $data['end_time']=$end_time;
        
	if ($this->input->post('export')){
            $data['tag'] = '?';
            $this->load->view('report/purchase_main_report_export', $data);
            $file_name = "purchase_main_report_export.xls";
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=".$file_name);           
            return;
        }
        
        $this->load->vars('all_admin', $this->admin_model->all_admin(array('user_status' => 1)));
        $this->load->vars('provider_list',$this->report_model->provider_list());
        $this->load->view('report/purchase_main_report', $data);        
    }

    public function purchase_main_detail_report()
    {   
        auth('purchase_main_detail_report');
        $this->load->model('admin_model');
        $this->load->helper('perms_helper');
        $this->load->helper('order_helper');
        $this->load->vars('perms' , get_depot_perm());
        $filter = $this->uri->uri_to_assoc(3);
        
        $brand_id = $this->input->post("brand_id");
        if(!empty($brand_id)) $filter['brand_id'] = $brand_id;
        $product_sn = $this->input->post("product_sn");
        if(!empty($product_sn)) $filter['product_sn'] = $product_sn;
        $product_name = $this->input->post("product_name");
        if(!empty($product_name)) $filter['product_name'] = $product_name;
        
        $medical1 = $this->input->post("medical1");
        if(!empty($medical1)) $filter['medical1'] = $medical1;
        
        $batch_code = $this->input->post("batch_code");
        if(!empty($batch_code)) $filter['batch_code'] = $batch_code;
        $purchase_code = $this->input->post("purchase_code");
        if(!empty($purchase_code)) $filter['purchase_code'] = $purchase_code;
        $provider_id = $this->input->post("provider_id");
        if(!empty($provider_id)) $filter['provider_id'] = $provider_id;
        $admin_name = $this->input->post("admin_name");
        if(!empty($admin_name)) $filter['admin_name'] = $admin_name;
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;
        
        $product_cess = $this->input->post("product_cess");
        if(!empty($product_cess)) $filter['product_cess'] = $product_cess;
        
        $r_start_time = $this->input->post("r_start_time");
        if(!empty($r_start_time)) $filter['r_start_time'] = $r_start_time;
        $r_end_time = $this->input->post("r_end_time");
        if(!empty($r_end_time)) $filter['r_end_time'] = $r_end_time;

        $data = $this->report_model->purchase_main_detail_report($filter);
        $data['provider_id']=$provider_id;
        $data['batch_code']=$batch_code;
        $data['purchase_code']=$purchase_code;
        $data['admin_name']=$admin_name;
        $data['start_time']=$start_time;
        $data['end_time']=$end_time;
        $data['brand_id']=$brand_id;
        $data['product_sn']=$product_sn;
        $data['product_name']=$product_name;
        $data['medical1']=$medical1;
        $data['product_cess']=$product_cess;
        $data['r_start_time']=$r_start_time;
        $data['r_end_time']=$r_end_time;
        $data['medical_arr'] = array('0' => '非医械', '1' => 'I', '2' => 'II', '3' => 'III');
        
	if ($this->input->post('export')){
            $data['tag'] = '?';
            $this->load->view('report/purchase_main_detail_report_export', $data);
            $file_name = "purchase_main_detail_report_export.xls";
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:attachment;filename=".$file_name);           
            return;
        }
        
        $this->load->vars('all_admin', $this->admin_model->all_admin(array('user_status' => 1)));
        $this->load->vars('provider_list',$this->report_model->provider_list());
        $this->load->vars('brand_list',$this->report_model->brand_list());
        $this->load->view('report/purchase_main_detail_report', $data);        
    }
}
