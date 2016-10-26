<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Manual_sms extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('manual_sms_model');
        $this->upload_data=APPPATH.'../public/upload/admin_data';
    }

    public function index(){
        auth('manual_sms_handle');
        $this->load->vars('mobile_template',"public/import/cachefile/mobiles.csv");
        $this->load->view('sms/info');
    }

    public function add()
    {
    	auth('manual_sms_handle');
    	$excel = $_FILES['excel'];

    	$filter = $this->uri->uri_to_assoc();
        $inputMobiles = $this->input->post("mobiles");
        if( empty($inputMobiles) && empty($excel) )
        {
            redirect('manual_sms');
            exit;
        }

		if (!empty($excel))
		{
			$unload_name=$excel['name'];
        	$arr_unload_name=@explode('.',$unload_name);
        	$file_type=$arr_unload_name[count($arr_unload_name)-1];
		}
        $mobiles = Array();

        //EXCEL 导入手机号
        if( !empty($excel)&& $excel['error']==0 )/*{{{*/
        {
            if($file_type!='csv') {
                echo "<script>alert ('上传的文件必须是csv文件');history.go(-1);</script>";
                exit;
            }
            $saveToFile = $this->upload_data.'/mobiles.csv';
            $rs=@copy($excel['tmp_name'], $saveToFile);
            $arr_cont=@file( $saveToFile);
            if( !empty($arr_cont) )array_shift($arr_cont);
            foreach($arr_cont as $key=>$value) {
                $arr_rs=@explode(",",$value);
                array_push( $mobiles,trim($arr_rs[0]) );
            }

            //过滤下数据
            $mobiles = $this->manual_sms_model->filterAvailMobiles($mobiles);

            // 将手机号加入数据库
            foreach( $mobiles AS $mobile )
                $this->manual_sms_model->addMobileNumber($mobile, -3 );
        }/*}}}*/
        // 手工输入手机号
        if( !empty($inputMobiles) )/*{{{*/
        {
            $mobiles = preg_split("/\n/", $inputMobiles );

            //过滤下数据
            $mobiles = $this->manual_sms_model->filterAvailMobiles($mobiles);

            // 将手机号加入数据库
            foreach( $mobiles AS $mobile )
                $this->manual_sms_model->addMobileNumber($mobile, -2 );
        }/*}}}*/
        $this->manual_sms_model->removeBlackMobiles();
        redirect('manual_sms/sms_list');
        exit;
    }

    public function sms_list(){
        auth('manual_sms_handle');
        $filter = $this->uri->uri_to_assoc();
        $mobile = $this->input->post("mobile");
        if (isset($filter['page']) && !empty($filter['page']))
        {
        	$page = $filter['page'];
        } else
        {
        	$page = trim($this->input->post('page'));
        }
		if (empty($page)) $page = 1;
		if ($page <= 1) $page = 1;
        $rows = $this->manual_sms_model->getRecords($page, '20',$mobile);
        $total = $this->manual_sms_model->getTotalRecords($mobile);

        $pageCount = ceil($total/20);
        $filter = Array( 'page'=>$page, 'page_size'=>20, 'page_count'=>$pageCount, 'record_count'=>$total,'mobile'=>$mobile);
		$data = $filter;
		$data['filter'] = $filter;
		$data['list'] = $rows;

        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('sms/list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('sms/list', $data);
    }

    public function oper()
    {
        auth('manual_sms_handle');
        $filter = $this->uri->uri_to_assoc();
        $referId = isset($filter['refer_id'])?$filter['refer_id']:'';
        $page = isset($filter['page'])?$filter['page']:'1';
        $this->manual_sms_model->deleteRecord($referId);
		redirect('manual_sms/sms_list/page/'.$page);
        exit;
    }

    public function send()
    {
    	//$this->load->library('mobile');
		$content = $this->input->post("content");
        $mobiles = $this->manual_sms_model->getCurrentBatchMobiles();
        if (empty($mobiles) )
        {
        	echo json_encode(array('error'=>1));
        	exit;
        }

        $mobileChunks = array_chunk($mobiles,290);
	    foreach ( $mobileChunks AS $rows ){ // 获得手机号码
	        $batchSend=0;
	        $mobile_arr = array();
	        foreach ( $rows AS $mobile )
	        {
	            if (!$this->manual_sms_model->isMobile($mobile) ){ continue; }
	            if (!in_array($mobile,$mobile_arr))
	            {
	            	$mobile_arr[] = $mobile;
	            }
	        }
	        if (count($mobile_arr) > 0)
	        {
	        	$input_mobile = '';
	        	if (count($mobile_arr) == 1)
	        	{
					$input_mobile = $mobile_arr[0];
	        	} else
	        	{
	        		$input_mobile = $mobile_arr;
	        	}
	        	//$rs = $this->mobile->send($content,$input_mobile);
                        
                        $url = ERP_HOST.'/api/do_sms';
                        $pdata = array('msg' => $content, 'mob' => $input_mobile);
                        curl_post($url, $pdata);
                        
	        }
	    }
        $this->manual_sms_model->flagMobileSended();
        echo json_encode(array('error'=>0));
        exit;
    }

}