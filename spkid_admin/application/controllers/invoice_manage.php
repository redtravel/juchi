<?php
#doc
#	classname:	Season
#	scope:		PUBLIC
#
#/doc

class Invoice_manage extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('invoice_manage_model');
		$this->load->model('order_model');
	}
	
	public function index ()
	{
		auth('invoice_manage_view');
		$filter = $this->uri->uri_to_assoc(3);
		$filter = get_pager_param($filter);

		$filter['all_invoice_status'] = array(	
			array('invoice_status'=>0,'invoice_value'=>'未打印'),
			array('invoice_status'=>1,'invoice_value'=>'已经导出'),
			array('invoice_status'=>2,'invoice_value'=>'已经打印')
		);

		//判断订单财审时间是否超过 INVOICE_PRINT_DAYS 订单财审后多少天后的订单，是待打印发票的。
		$max_endtime = strtotime(DATE('Y-m-d')) - INVOICE_PRINT_DAYS * 86400;

		$start_time = $this->input->post('start_time'); 		// 财审时间
		$end_time = $this->input->post('end_time'); 		// 财审时间

		if( strtotime($end_time) > $max_endtime ) $end_time = date('Y-m-d',$max_endtime);

		$order_sn = trim($this->input->post('order_sn'));         		// 订单号
		$invoice_status = trim($this->input->post('invoice_status'));   // 打印状态

		if (!empty($start_time)) $filter['start_time'] = $start_time;
		else $filter['start_time'] =  date('Y-m-d',$max_endtime-86400);
		if (!empty($end_time)) $filter['end_time'] = $end_time;
		else $filter['end_time'] =  date('Y-m-d',$max_endtime);
		$filter['order_sn'] = $order_sn;
		if (!empty($invoice_status)) $filter['invoice_status'] = $invoice_status;
		else $filter['invoice_status'] = 0;

		$data = $this->invoice_manage_model->invoice_list($filter);
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('invoice_manage/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}

		$data['full_page'] = TRUE;		

		$this->load->view('invoice_manage/index', $data);
	}

	public function invoice_data_export()
	{
		auth('invoice_manage_export');

		$export['start_time'] = $this->input->post('start_time'); 		// 财审时间
		$export['end_time'] = $this->input->post('end_time'); 		// 财审时间
		$export['order_sn'] = trim($this->input->post('order_sn'));         		// 订单号
		$export['invoice_status'] = trim($this->input->post('invoice_status'));   // 打印状态

	    $list = $this->invoice_manage_model->all_invoice($export);

		$this->output->set_header('Pragma: public');
		$this->output->set_header('Expires: 0');
		$this->output->set_header('Content-Type:text/html; charset=gbk');
		$this->output->set_header('Content-Type: application/vnd.ms-excel');
		$this->output->set_header('Content-Disposition: attachment; filename=invoice'.date('Ymdhis').'.xls');
		$this->output->set_header('Content-Type:application/octet-stream');
		$this->output->set_header('Content-Type:application/download');
		$this->output->set_header('Content-Type:application/force-download');
		$this->output->set_header('Cache-Control:must-revalidate,post-check=0,pre-check=0');

		$title = array('订单号', '商品名称', '规格型号', '单价', '数量', '单位', '商品金额', '支付金额', '发票抬头', '发票内容', '财审时间', '打印状态(未打印/已经打印)');
	    echo iconv('utf-8', 'gbk', implode("\t", $title)), "\n";
	    
	    $invoice_status = array(	
			array('invoice_status'=>0,'invoice_value'=>'未打印'),
			array('invoice_status'=>1,'invoice_value'=>'已经导出'),
			array('invoice_status'=>2,'invoice_value'=>'已经打印')
		);
	    foreach ($list as $value) {

	    	//更新发票导出打印状态
	    	$order_invoice_status[] = array('order_sn' =>$value['order_sn'],'invoice_status' => 1);

	    	//添加记录
		    $update[] = array(
	            'order_id' => $value['order_id'],
	            'is_return' => 0,
	            'order_status' => isset($value['order_status'])?$value['order_status']:0,
	            'shipping_status' => isset($value['shipping_status'])?$value['shipping_status']:0,
	            'pay_status' => isset($value['pay_status'])?$value['pay_status']:0,
	            'action_note' => '导出未打印的发票记录',
	            'create_admin' => isset($this->admin_id)?$this->admin_id:intval($this->session->userdata('admin_id')),
	            'create_date' => isset($this->time)?$this->time:date('Y-m-d H:i:s')
	        );

	    	unset($value['order_id']); 
	    	unset($value['order_status']); 
	    	unset($value['shipping_status']); 
	    	unset($value['pay_status']); 

	    	if(!empty($value['product_desc_additional'])){
	    		$dimensions = json_decode($value['product_desc_additional']);
	    		$dimensions->desc_dimensions == false ? $value['product_desc_additional'] ='': $value['product_desc_additional'] = $dimensions->desc_dimensions;
	    	}

	    	foreach ($invoice_status as $val) {
				if ($value['invoice_status'] == $val['invoice_status']) $value['invoice_status'] = $val['invoice_value'];
			}

	    	echo iconv('utf-8', 'gbk', implode("\t", $value)), "\n";
	    }

	    $this->invoice_manage_model->add_invoice_record($update);  //添加导出记录

	    //更新发票导出打印状态
	    $this->order_model->all_invoice_order($order_invoice_status);  

	}

	public function invoice_data_import()
	{
		auth('invoice_manage_import');

		$invoice_status = array(	
			array('invoice_status'=>0,'invoice_value'=>'未打印'),
			array('invoice_status'=>1,'invoice_value'=>'已经导出'),
			array('invoice_status'=>2,'invoice_value'=>'已经打印')
		);

		if (! empty ( $_FILES ['file_invoice'] ['name'] )) 
		{
	        //载入PHPExcel类
			include('./application/libraries/PHPExcel.php');
			$Obj = new PHPExcel_Reader_Excel5();
			$Obj->setReadDataOnly(true);
			//读取demo.xls文件
			$phpExcel = $Obj->load($_FILES ['file_invoice'] ['tmp_name']);
			//获取当前活动sheet
			$objWorksheet = $phpExcel->getActiveSheet();
			//获取行数
			$highestRow = $objWorksheet->getHighestRow();
			//获取列数
			$highestColumn = $objWorksheet->getHighestColumn();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			//循环输出数据
			$data = array();	
			$invoice = array('order_sn','product_name','product_desc_additional','product_price','product_num','unit_name','total_price','paid_price','invoice_title','invoice_content','finance_date','invoice_status');
			for($row = 2; $row <= $highestRow; ++$row) {
			 	for($col = 0; $col < $highestColumnIndex; ++$col) {
			 		$val = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
			 		$data[$row][$invoice[$col]] = trim($val);
			 	}
			}
			foreach ($data as $key => $value) {

				foreach ($invoice_status as $ino) {
					if ($value['invoice_status'] === $ino['invoice_value']) $value['invoice_status'] = $ino['invoice_status'];
				}

				$updata[$key]['order_sn'] = $value['order_sn'];
				$updata[$key]['invoice_status'] = $value['invoice_status'];
			}

			$this->order_model->all_invoice_order($updata);  //更新发票打印状态

			//添加导入excel数据记录
			foreach ($updata as $key => $value) {
				$updata_order[$key] = $value['order_sn'];
			}
			$updata_order = array_unique($updata_order); //去除重复记录
			$invoice_order_id = $this->order_model->all_order_id($updata_order);

			foreach ($invoice_order_id as  $value) {
				$update[] = array(
		            'order_id' => $value['order_id'],
		            'is_return' => 0,
		            'order_status' => isset($value['order_status'])?$value['order_status']:0,
		            'shipping_status' => isset($value['shipping_status'])?$value['shipping_status']:0,
		            'pay_status' => isset($value['pay_status'])?$value['pay_status']:0,
		            'action_note' => '导入已经打印的发票记录',
		            'create_admin' => isset($this->admin_id)?$this->admin_id:intval($this->session->userdata('admin_id')),
		            'create_date' => isset($this->time)?$this->time:date('Y-m-d H:i:s')
		        );
		    }
		    $this->invoice_manage_model->add_invoice_record($update);
			sys_msg('导入成功！');

	    }    
	}
}
###