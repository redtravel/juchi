<?php
#doc
#	classname:	Purchase_virtual
#	scope:		PUBLIC
#
#/doc

class Shipping_fcheck extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		$this->time = date('Y-m-d H:i:s');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('shipping_fcheck_model');
		$this->load->model('shipping_model');
	}
	
	public function index ()
	{
		auth('shipping_fcheck_view');
		$filter = $this->uri->uri_to_assoc(3);
		$filter['batch_sn'] = trim($this->input->post('batch_sn'));
		$filter['invoice_no'] = trim($this->input->post('invoice_no'));
		$filter['lock_status'] = trim($this->input->post('lock_status'));
		$filter['shipping_check'] = trim($this->input->post('shipping_check'));
		$filter['finance_check'] = trim($this->input->post('finance_check'));
		$filter = get_pager_param($filter);
		$data = $this->shipping_fcheck_model->find_page($filter);
		
		if ($this->input->is_ajax_request()) {
			
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('shipping_fcheck/shipping_fcheck_index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		
		$data['full_page'] = TRUE;
		$this->load->vars('all_shipping', $this->shipping_model->all_shipping());
		$this->load->view('shipping_fcheck/shipping_fcheck_index', $data);
	}
	
	public function info ($batch_id)
	{
		auth('shipping_fcheck_view');
		$filter = $this->uri->uri_to_assoc(3);
//		$filter['shipping_check'] = $this->input->post('shipping_check');
		$filter = get_pager_param($filter);
		$data = $this->shipping_fcheck_model->find_page_sub($batch_id, $filter);
		
		$batch = $this->shipping_fcheck_model->get($batch_id);
		$operation_list = $this->operation_list($batch);
		$batch_summery = $this->shipping_fcheck_model->get_summery($batch_id);
		
		$this->load->vars('shipping_fcheck', $batch);
		$this->load->vars('operation_list', $operation_list);
		$this->load->vars('batch_summery', $batch_summery);
		
		if ($this->input->is_ajax_request()) {
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('shipping_fcheck/shipping_fcheck_info', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		
		$data['full_page'] = TRUE;

		$file_path = 'public/data/static/shipping/'.$batch->batch_sn.'.xml';
        if(!file_exists($file_path)) $file_path = "";
		
		$this->load->vars('file_path', $file_path);
		$this->load->view('shipping_fcheck/shipping_fcheck_info', $data);
	}
	
	public function upload () {
		
		auth('shipping_fcheck_upload');
		$shipping_id = intval($this->input->post('shipping_id'));
        $batch_type = intval($this->input->post('batch_type'));
        $shipping = $this->shipping_model->filter(array('shipping_id'=>$shipping_id));
        if(empty($shipping)) sys_msg('请指定配送方式');
        if(!in_array($batch_type, array(1,2,3,4))) sys_msg("请指定对帐类型");
        if(empty($_FILES['file']['tmp_name'])) sys_msg("请上传对帐文件");
		
		if($_FILES['file']['type'] != 'text/xml') {
			sys_msg("请上传XML格式的文件", 1);
		}
		
		$content = file_get_contents($_FILES['file']['tmp_name']);
		$content = preg_replace('/&.*;/','',$content);
		$dom = new SimpleXMLElement($content);
		$dom->registerXPathNamespace('c', 'urn:schemas-microsoft-com:office:spreadsheet');
		$rows = $dom->xpath('//c:Workbook//c:Worksheet//c:Table//c:Row');
		//$keys = array('product_sn','provider_productcode','category_id','brand_name','market_price','shop_price','season_name','flag_name','provider_code','product_year','product_month','min_month','max_month');
		//$success_records = array();
		//$error_records = array();
		
		$time = date('Y-m-d H:i:s');
        $index = 1;
        $data = array();
        $data_err = array();
        $this->db->query('BEGIN');
        foreach($rows as $row){
            $row_cache = array();
            foreach($row as $cell) {
                $row_cache[] = strval($cell->Data);
            }
            switch($index){
                case 1:
                    $batch_sn = trim($row_cache[1]);
                    $insert = array('batch_sn'=>addslashes($batch_sn),'batch_type'=>$batch_type,'shipping_id'=>$shipping_id,'create_admin'=>$this->admin_id,'create_date'=>$time,'lock_admin'=>$this->admin_id,'lock_date'=>$time);
                    $batch_exists = $this->shipping_fcheck_model->filter(array('batch_sn'=>addslashes($batch_sn)));
                    if(!empty($batch_exists)) sys_msg('添加新批次失败：批次号重复',1);
                    $batch_id = $this->shipping_fcheck_model->insert($insert);
                    //TODO 如何处理数据库异常
                    $batch = $this->shipping_fcheck_model->filter(array('batch_id'=>$batch_id));
                    break;
                case 2:
                    $shipping_code = trim($row_cache[1]);
                    if(strtoupper($shipping_code)!=strtoupper($shipping->shipping_code)){
                        sys_msg("您选择的快递方式与导入文件不相符");
                    }
                    break;
                case 3:
                    //***这一行只是标题，不作处理***//
                    break;
                default:
                    if(empty($row_cache)) break;
                    if(empty($row_cache[0]) && empty($row_cache[1]) && empty($row_cache[3])) {
                    	break;
                    }
                    
                    $this->format_row_data($row_cache,$data,$data_err,$batch);
                    if(count($data)>=1000) {
                    	$this->batch_insert_sub($data,$batch);
                    }
            }
            $index += 1;
        }
        if($index<=4) {
            sys_msg('您上传的文件没有对帐数据！',1);
            $this->db->query('ROLLBACK');
        }
        if (!empty($data)) {
        	$this->batch_insert_sub($data,$batch);
        }
        if($batch->batch_type == 4) {
        	$this->shipping_fcheck_model->update_shipping_sub_deny($batch_id);
        	$this->shipping_fcheck_model->check_batch_deny($batch, $data_err);
        } else {
	        $this->shipping_fcheck_model->update_shipping_sub($batch_id);
	        //echo '|execute db_lock_shipping_data...';
	        $this->shipping_fcheck_model->db_lock_shipping_data($batch_id);
	        //echo '|execute check_batch...';
	        $this->shipping_fcheck_model->check_batch($batch, $data_err);
	        //echo '|execute lock_order_change...';
	        $this->shipping_fcheck_model->lock_order_change($batch,$this->admin_id);
        }
		if (!empty($data_err)) {
			if($batch->batch_type == 4) {
				$this->write_err_data_deny($data_err, $batch, $shipping);
			} else {
	            $this->write_err_data($data_err, $batch, $shipping);
			}
		}
        $this->db->query('COMMIT');
        $links[] = array('text' => "返回查看对帐单详情", 'href' => 'shipping_fcheck/info/' . $batch_id);
        sys_msg("操作成功！".(!empty($data_err)?"但有部分数据需要修正！":""), 0, $links);
		
	}
	
	/**
	 * 增量上传
	 */
	public function upload_more($batch_id) {
		
		auth('shipping_fcheck_upload');
		if (empty($_FILES['file']['tmp_name'])) sys_msg("请上传对帐文件");
		if($_FILES['file']['type'] != 'text/xml') {
			sys_msg("请上传XML格式的文件", 1);
		}
		
		$batch = $this->shipping_fcheck_model->filter(array('batch_id'=>$batch_id));
        $shipping = $this->shipping_model->filter(array('shipping_id'=>$batch->shipping_id));
        
        $content = file_get_contents($_FILES['file']['tmp_name']);
        $content = preg_replace('/&.*;/','',$content);
        $dom = new SimpleXMLElement($content);
        $dom->registerXPathNamespace('c', 'urn:schemas-microsoft-com:office:spreadsheet');
        $rows = $dom->xpath('//c:Workbook//c:Worksheet//c:Table//c:Row');
        $result = array();
        $index = 1;
        $data = array();
        $data_err = array();
        
        $this->db->query('BEGIN');
        foreach ($rows as $row) {
            $row_cache = array();
            foreach ($row as $cell)  $row_cache[] = strval($cell->Data);
            switch ($index) {
                case 1:
                    $batch_sn = trim($row_cache[1]);
                    if(strtoupper($batch_sn)!=strtoupper($batch->batch_sn)) sys_msg('批次号错误！', 1);
                    break;
                case 2:
                    $shipping_code = trim($row_cache[1]);
                    if (strtoupper($shipping_code) != strtoupper($shipping->shipping_code)) {
                        sys_msg("此批次的快递方式与导入文件不相符");
                    }
                    break;
                case 3:
                    $max_id = $this->shipping_fcheck_model->get_max_id($batch_id);
                    if(empty($max_id)) {
                    	$max_id = 0;
                    }
                    break;
                default:
                    if(empty($row_cache)) break;
                    if(empty($row_cache[0]) && empty($row_cache[1]) && empty($row_cache[3])) {
                    	break;
                    }
                    $this->format_row_data($row_cache, $data, $data_err,$batch);
                    if (count($data) >= 1000) $this->batch_insert_sub($data, $batch);
            }
            $index += 1;
        }
        if ($index <= 4) {
            $this->db->query('ROLLBACK');
            sys_msg('您上传的文件没有对帐数据！', 1);
        }
        if (!empty($data)) $this->batch_insert_sub($data, $batch);
        
        if($batch->batch_type == 4) {
        	$this->shipping_fcheck_model->update_shipping_sub_deny($batch_id, $max_id);
        	$this->shipping_fcheck_model->check_batch_deny($batch, $data_err, $max_id);
        } else {
	        $this->shipping_fcheck_model->update_shipping_sub($batch_id,$max_id);
	        //echo '|execute db_lock_shipping_data...';
	        $this->shipping_fcheck_model->db_lock_shipping_data($batch_id,$max_id);
	        //echo '|execute check_batch...';
	        $this->shipping_fcheck_model->check_batch($batch, $data_err,$max_id);
	        //echo '|execute lock_order_change...';
	        $this->shipping_fcheck_model->lock_order_change($batch,$this->admin_id,$max_id);
        }
        if (!empty($data_err)) {
        	if($batch->batch_type == 4) {
				$this->write_err_data_deny($data_err, $batch, $shipping);
			} else {
	            $this->write_err_data($data_err, $batch, $shipping);
			}
        } else {
        	$this->unlink_error_file($batch->batch_sn);
        }
        $this->db->query('COMMIT');
        $links[] = array('text' => "返回查看对帐单详情", 'href' => 'shipping_fcheck/info/' . $batch_id);
        sys_msg("操作成功！".(!empty($data_err)?"但有部分数据需要修正！":""), 0, $links);
	}
	
	public function lock($batch_id) {
		$update = array('lock_admin' => $this->admin_id, 'lock_date' => $this->time);
		$this->shipping_fcheck_model->update($update, $batch_id);
        $links[] = array('text' => "返回查看对帐单详情", 'href' => 'shipping_fcheck/info/' . $batch_id);
        sys_msg("操作成功！", 0, $links);
	}
	
	public function unlock($batch_id) {
		$update = array('lock_admin' => 0, 'lock_date' => null);
		$this->shipping_fcheck_model->update($update, $batch_id);
        $links[] = array('text' => "返回查看对帐单详情", 'href' => 'shipping_fcheck/info/' . $batch_id);
        sys_msg("操作成功！", 0, $links);
	}
	
	public function shipping_check($batch_id) {
		auth('shipping_fcheck_shipping_check');
		$batch = $this->shipping_fcheck_model->filter(array('batch_id'=>$batch_id));
        $update = array('shipping_check'=>1,'shipping_check_admin'=>$this->admin_id,'shipping_check_date'=>$this->time,'lock_admin'=>0,'lock_date'=>null);
		$this->shipping_fcheck_model->update($update, $batch_id);
        $this->unlink_error_file($batch->batch_sn);
        $links[] = array('text' => "返回查看对帐单详情", 'href' => 'shipping_fcheck/info/' . $batch_id);
        sys_msg("操作成功！", 0, $links);
	}
	
	public function shipping_uncheck($batch_id) {
		auth('shipping_fcheck_shipping_check');
		$batch = $this->shipping_fcheck_model->filter(array('batch_id'=>$batch_id));
        $update = array('shipping_check'=>0,'shipping_check_admin'=>0,'shipping_check_date'=>null,'lock_admin'=>0,'lock_date'=>null);
		$this->shipping_fcheck_model->update($update, $batch_id);
        $this->unlink_error_file($batch->batch_sn);
        $links[] = array('text' => "返回查看对帐单详情", 'href' => 'shipping_fcheck/info/' . $batch_id);
        sys_msg("操作成功！", 0, $links);
	}
	
	public function finance_check($batch_id) {
            auth('shipping_fcheck_finance_check');
            $batch = $this->shipping_fcheck_model->filter(array('batch_id'=>$batch_id));
            $this->db->query('BEGIN');
            if($batch->batch_type==1 || $batch->batch_type==2){
                //锁住关联订单
                $this->shipping_fcheck_model->db_lock_shipping_data($batch_id);
                $data_err = $invoice_nos = array();
                $this->shipping_fcheck_model->check_batch($batch,$data_err,0,'finance_check');
                if(!empty($data_err)){
                    foreach($data_err as $row) $invoice_nos[] = $row['invoice_no'];
                    $this->db->query("ROLLBACK");
                    sys_msg("以下运单号对应的订单待收金额不符，请检查：".implode(',',$invoice_nos),1,array(),false);
                }
                $this->shipping_fcheck_model->finance_order($batch, $this->admin_id);
            } elseif ($batch->batch_type==3){
                $this->shipping_fcheck_model->finance_order_shipping($batch);
            }
            $this->shipping_fcheck_model->unlock_order_change($batch, $this->admin_id);
        
            $update = array('finance_check'=>1,'finance_check_admin'=>$this->admin_id,'finance_check_date'=>$this->time,'lock_admin'=>0,'lock_date'=>null);
            $this->shipping_fcheck_model->update($update, $batch_id);
		
            $this->db->query('COMMIT');
            $links[] = array('text' => "返回查看对帐单详情", 'href' => 'shipping_fcheck/info/' . $batch_id);
            sys_msg("操作成功！", 0, $links);
	}
	
	public function deny_check($batch_id) {
		auth(array('shipping_fcheck_shipping_check','shipping_fcheck_finance_check'));
		$batch = $this->shipping_fcheck_model->filter(array('batch_id'=>$batch_id));
		$this->db->query('BEGIN');
	
		$this->shipping_fcheck_model->deny_check($batch, $this->admin_id);
	
		$update = array('shipping_check'=>1,'shipping_check_admin'=>$this->admin_id,'shipping_check_date'=>$this->time,'lock_admin'=>0,'lock_date'=>null);
		$this->shipping_fcheck_model->update($update, $batch_id);
		
// 		$update = array('finance_check'=>1,'finance_check_admin'=>$this->admin_id,'finance_check_date'=>$this->time,'lock_admin'=>0,'lock_date'=>null);
// 		$this->shipping_fcheck_model->update($update, $batch_id);
	
		$this->db->query('COMMIT');
		$links[] = array('text' => "返回查看对帐单详情", 'href' => 'shipping_fcheck/info/' . $batch_id);
		sys_msg("操作成功！", 0, $links);
	}
	
	public function del($batch_id) {
		auth('shipping_fcheck_upload');
		$batch = $this->shipping_fcheck_model->filter(array('batch_id'=>$batch_id));
		$this->db->query('BEGIN');
		$this->shipping_fcheck_model->unlock_order_change($batch, $this->admin_id, 0, '删除');
		$this->shipping_fcheck_model->delete_related($batch_id);
        $this->unlink_error_file($batch->batch_sn);
        $links[] = array('text' => "返回查看物流对账管理", 'href' => 'shipping_fcheck');
        $this->db->query('COMMIT');
        sys_msg("操作成功！", 0, $links);
	}
	
	public function del_sub($batch_id) {
		auth('shipping_fcheck_upload');
        $sub_id = $this->input->get('sub_id');
		$batch = $this->shipping_fcheck_model->filter(array('batch_id'=>$batch_id));
		$this->db->query('BEGIN');
		$this->shipping_fcheck_model->unlock_order_change($batch, $this->admin_id, $sub_id, '移除运单记录');
		$this->shipping_fcheck_model->delete_sub($sub_id);
        $links[] = array('text' => "返回查看对帐单详情", 'href' => 'shipping_fcheck/info/' . $batch_id);
        $this->db->query('COMMIT');
        sys_msg("操作成功！", 0, $links);
	}
	
	private function format_row_data($row,&$data,&$data_err,$batch){
	    $batch_type = $batch->batch_type;
	    $result = array();
        $result['invoice_no'] = trim($row[0]);
	    if($batch->batch_type != 4) {
	        $result['destination'] = trim($row[1]);
	        $result['weight'] = round(floatval($row[2]),2);
	        $result['goods_number'] = intval($row[3]);
	        $result['cod_amount'] = ($batch_type ==1||$batch_type==2 || $batch_type==4)?round(floatval($row[4]),2):0;
	        $result['express_fee'] = ($batch_type ==1||$batch_type==3 || $batch_type==4)?round(floatval($row[5]),2):0;
	        $result['cod_fee'] = ($batch_type ==1||$batch_type==2 || $batch_type==4)?round(floatval($row[6]),2):0;
	        $result['sign_date'] = strtotime($row[7]);
	        if($result['sign_date']) $result['sign_date'] = date ("Y-m-d H:i:s", $result['sign_date']);
	    }
	    
	    if(empty($result['invoice_no'])) {
	        $result['err_msg'] = "运单号缺失";
	        $data_err[] = $result;
	    }else{
	        $data[] = $result;
	    }
	}
	
	private function batch_insert_sub(&$data,$batch){
	    foreach($data as $row){
	    	$row['batch_id'] = $batch->batch_id;
	    	$row['shipping_id'] = $batch->shipping_id;
	    	$row['batch_type'] = $batch->batch_type;
	    	$row['create_admin'] = $this->admin_id;
	    	$row['create_date'] = $this->time;
	    	$this->shipping_fcheck_model->insert_sub($row);
	    }
	    $data = array();
	}
	
	private function write_err_data($data, $batch, $shipping){
	    $batch_sn = $batch->batch_sn;
	    $shipping_code = $shipping->shipping_code;
	    $fp = fopen(SHIPPING_HTML_PATH.$batch_sn.".xml", 'w');
	    $file_str = '<?xml version="1.0"?>
			<?mso-application progid="Excel.Sheet"?>
			<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
			 xmlns:o="urn:schemas-microsoft-com:office:office"
			 xmlns:x="urn:schemas-microsoft-com:office:excel"
			 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
			 xmlns:html="http://www.w3.org/TR/REC-html40">
			 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
			  <Author>fclub</Author>
			  <LastAuthor>fclub</LastAuthor>
			  <Created>2010-08-26T06:36:52Z</Created>
			  <LastSaved>2010-08-26T06:37:01Z</LastSaved>
			  <Company>fclub</Company>
			  <Version>12</Version>
			 </DocumentProperties>
			 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
			  <WindowHeight>8505</WindowHeight>
			  <WindowWidth>17235</WindowWidth>
			  <WindowTopX>120</WindowTopX>
			  <WindowTopY>105</WindowTopY>
			  <ProtectStructure>False</ProtectStructure>
			  <ProtectWindows>False</ProtectWindows>
			 </ExcelWorkbook>
			 <Styles>
			  <Style ss:ID="Default" ss:Name="Normal">
			   <Alignment ss:Vertical="Center"/>
			   <Borders/>
			   <Font ss:FontName="宋体" x:CharSet="134" ss:Size="12"/>
			   <Interior/>
			   <NumberFormat/>
			   <Protection/>
			  </Style>
			  </Styles>
			 <Worksheet ss:Name="物流对帐表">
			  <Table ss:ExpandedColumnCount="9" ss:ExpandedRowCount="'.(count($data)+3).'" x:FullColumns="1"
			   x:FullRows="1" ss:DefaultColumnWidth="54" ss:DefaultRowHeight="14.25">
			   <Column ss:Index="8" ss:Width="63"/>
			   <Column ss:Width="57"/>
			   <Row ss:AutoFitHeight="0">
			    <Cell><Data ss:Type="String">导入批号</Data></Cell>
			    <Cell><Data ss:Type="String">'.$batch_sn.'</Data></Cell>
			   </Row>
			   <Row ss:AutoFitHeight="0">
			    <Cell><Data ss:Type="String">快递公司</Data></Cell>
			    <Cell><Data ss:Type="String">'.$shipping_code.'</Data></Cell>
			   </Row>
			   <Row ss:AutoFitHeight="0">
			    <Cell><Data ss:Type="String">运单号</Data></Cell>
			    <Cell><Data ss:Type="String">到达地</Data></Cell>
			    <Cell><Data ss:Type="String">重量</Data></Cell>
			    <Cell><Data ss:Type="String">件数</Data></Cell>
			    <Cell><Data ss:Type="String">实收货款</Data></Cell>
			    <Cell><Data ss:Type="String">运费</Data></Cell>
			    <Cell><Data ss:Type="String">手续费</Data></Cell>
			    <Cell><Data ss:Type="String">签收时间</Data></Cell>
			    <Cell><Data ss:Type="String">错误信息</Data></Cell>
			   </Row>';
	    foreach($data as $row){
	        $file_str .= '<Row ss:AutoFitHeight="0">
	                        <Cell><Data ss:Type="String" x:Ticked="1">'.$row['invoice_no'].'</Data></Cell>
	                        <Cell><Data ss:Type="String">'.$row['destination'].'</Data></Cell>
	                        <Cell><Data ss:Type="Number">'.$row['weight'].'</Data></Cell>
	                        <Cell><Data ss:Type="Number">'.$row['goods_number'].'</Data></Cell>
	                        <Cell><Data ss:Type="Number">'.$row['cod_amount'].'</Data></Cell>
	                        <Cell><Data ss:Type="Number">'.$row['express_fee'].'</Data></Cell>
	                        <Cell><Data ss:Type="Number">'.$row['cod_fee'].'</Data></Cell>
	                        <Cell><Data ss:Type="String">'.$row['sign_date'].'</Data></Cell>
	                        <Cell><Data ss:Type="String">'.$row['err_msg'].'</Data></Cell>
	                       </Row>';
	    }
	    $file_str .= '  </Table>
			  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
			   <Unsynced/>
			   <Selected/>
			   <Panes>
			    <Pane>
			     <Number>3</Number>
			     <ActiveRow>4</ActiveRow>
			     <ActiveCol>6</ActiveCol>
			    </Pane>
			   </Panes>
			   <ProtectObjects>False</ProtectObjects>
			   <ProtectScenarios>False</ProtectScenarios>
			  </WorksheetOptions>
			 </Worksheet>
			</Workbook>';
		
	    fwrite($fp, $file_str);
	    fclose($fp);
	}
	
	private function write_err_data_deny($data, $batch, $shipping){
	    $batch_sn = $batch->batch_sn;
	    $shipping_code = $shipping->shipping_code;
	    $fp = fopen(SHIPPING_HTML_PATH.$batch_sn.".xml", 'w');
	    $file_str = '<?xml version="1.0"?>
			<?mso-application progid="Excel.Sheet"?>
			<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
			 xmlns:o="urn:schemas-microsoft-com:office:office"
			 xmlns:x="urn:schemas-microsoft-com:office:excel"
			 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
			 xmlns:html="http://www.w3.org/TR/REC-html40">
			 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
			  <Author>fclub</Author>
			  <LastAuthor>fclub</LastAuthor>
			  <Created>2010-08-26T06:36:52Z</Created>
			  <LastSaved>2010-08-26T06:37:01Z</LastSaved>
			  <Company>fclub</Company>
			  <Version>12</Version>
			 </DocumentProperties>
			 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
			  <WindowHeight>8505</WindowHeight>
			  <WindowWidth>17235</WindowWidth>
			  <WindowTopX>120</WindowTopX>
			  <WindowTopY>105</WindowTopY>
			  <ProtectStructure>False</ProtectStructure>
			  <ProtectWindows>False</ProtectWindows>
			 </ExcelWorkbook>
			 <Styles>
			  <Style ss:ID="Default" ss:Name="Normal">
			   <Alignment ss:Vertical="Center"/>
			   <Borders/>
			   <Font ss:FontName="宋体" x:CharSet="134" ss:Size="12"/>
			   <Interior/>
			   <NumberFormat/>
			   <Protection/>
			  </Style>
			  </Styles>
			 <Worksheet ss:Name="物流对帐表">
			  <Table ss:ExpandedColumnCount="9" ss:ExpandedRowCount="'.(count($data)+3).'" x:FullColumns="1"
			   x:FullRows="1" ss:DefaultColumnWidth="54" ss:DefaultRowHeight="14.25">
			   <Column ss:Index="8" ss:Width="63"/>
			   <Column ss:Width="57"/>
			   <Row ss:AutoFitHeight="0">
			    <Cell><Data ss:Type="String">导入批号</Data></Cell>
			    <Cell><Data ss:Type="String">'.$batch_sn.'</Data></Cell>
			   </Row>
			   <Row ss:AutoFitHeight="0">
			    <Cell><Data ss:Type="String">快递公司</Data></Cell>
			    <Cell><Data ss:Type="String">'.$shipping_code.'</Data></Cell>
			   </Row>
			   <Row ss:AutoFitHeight="0">
			    <Cell><Data ss:Type="String">运单号</Data></Cell>
			    <Cell><Data ss:Type="String">错误信息</Data></Cell>
			   </Row>';
	    foreach($data as $row){
	        $file_str .= '<Row ss:AutoFitHeight="0">
	                        <Cell><Data ss:Type="String" x:Ticked="1">'.$row['invoice_no'].'</Data></Cell>
	                        <Cell><Data ss:Type="String">'.$row['err_msg'].'</Data></Cell>
	                       </Row>';
	    }
	    $file_str .= '  </Table>
			  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
			   <Unsynced/>
			   <Selected/>
			   <Panes>
			    <Pane>
			     <Number>3</Number>
			     <ActiveRow>4</ActiveRow>
			     <ActiveCol>6</ActiveCol>
			    </Pane>
			   </Panes>
			   <ProtectObjects>False</ProtectObjects>
			   <ProtectScenarios>False</ProtectScenarios>
			  </WorksheetOptions>
			 </Worksheet>
			</Workbook>';
		
	    fwrite($fp, $file_str);
	    fclose($fp);
	}
	
	private function unlink_error_file($batch_sn) {
		$file_path = SHIPPING_HTML_PATH.$batch_sn.".xml";
        if(file_exists($file_path)) @unlink($file_path);
	}
	
	private function operation_list($batch) {
	    $result = array(
	        'shipping_fcheck_view'   => check_perm('shipping_fcheck_view'),
	        'shipping_fcheck_upload' => check_perm('shipping_fcheck_upload'),
	        'shipping_fcheck_shipping_check' => check_perm('shipping_fcheck_shipping_check'),
	        'shipping_fcheck_finance_check'  => check_perm('shipping_fcheck_finance_check')
	    );
	    $can_op = $result['shipping_fcheck_upload'] || $result['shipping_fcheck_shipping_check'] || $result['shipping_fcheck_finance_check'];
	    $result['lock'] = ($batch->lock_admin == 0) && $can_op;
	    $result['unlock'] = $batch->lock_admin == $this->admin_id;
	    $result['shipping_check'] = $batch->lock_admin == $this->admin_id && $result['shipping_fcheck_shipping_check'] && $batch->shipping_check==0;
	    $result['shipping_uncheck'] = $batch->lock_admin == $this->admin_id && $result['shipping_fcheck_shipping_check'] && $batch->shipping_check==1 && $batch->finance_check==0;
	    $result['finance_check'] = $batch->lock_admin == $this->admin_id && $result['shipping_fcheck_finance_check'] && $batch->shipping_check==1 && $batch->finance_check==0;
	    $result['upload'] = $batch->lock_admin == $this->admin_id && $result['shipping_fcheck_upload'] && $batch->shipping_check==0 && $batch->finance_check==0;
	    $result['del'] = $batch->lock_admin == $this->admin_id && $result['shipping_fcheck_upload'] && $batch->shipping_check==0 && $batch->finance_check==0;
	    $result['del_sub'] = $batch->lock_admin == $this->admin_id && $result['shipping_fcheck_upload'] && $batch->shipping_check==0 && $batch->finance_check==0;
	    return $result;
	}
}
###