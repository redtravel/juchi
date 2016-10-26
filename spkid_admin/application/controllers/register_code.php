<?php
#doc
#	classname:	Season
#	scope:		PUBLIC
#
#/doc

class Register_code extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('register_model');
	}
	
	public function index ()
	{
		auth('register_code_view');
		$filter = $this->uri->uri_to_assoc(3);

		$register_no = trim($this->input->post('register_no'));
                $product_name = trim($this->input->post('product_name'));
                $unit = trim($this->input->post('unit'));

		if (!empty($register_no)) $filter['register_no'] = $register_no;
                if (!empty($product_name)) $filter['product_name'] = $product_name;
                if (!empty($unit)) $filter['unit'] = $unit;

		$filter = get_pager_param($filter);
		$data = $this->register_model->register_list($filter);

		$this->load->vars('perm_delete', check_perm('register_code_delete'));  // 设置权限
		$this->load->vars('perm_fetch', check_perm('register_code_fetch'));  // 设置权限
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('register/index', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;		

		$this->load->view('register/index', $data);
	}

	public function add()
	{
		auth(array('register_code_add','register_code_view'));

		$data['mdc_1'] = $this->register_model->medical_list('medical_device_class');
		$data['mdc_2'] = $this->register_model->medical_list('medical_device');

		$this->load->view('register/add',$data);
	}

	public function proc_add()
	{
		auth('register_code_add');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('register_no', '注册号', 'trim|required');
		$this->form_validation->set_rules('medical_1', 'MDC_1', 'trim|required');
		$this->form_validation->set_rules('medical_2', 'MDC_2', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$insert = array();
		$insert['register_no'] = $this->input->post('register_no');
		$insert['medical1'] = $this->input->post('medical_1');
		$insert['medical2'] = $this->input->post('medical_2');
		$insert['add_admin_id'] = $this->admin_id;
		$insert['add_admin_time'] = time();

		$register = $this->register_model->filter(array('register_no'=>$insert['register_no']));
		if ($register) {
			sys_msg('注册号重复', 1);
		}

		$id = $this->register_model->insert($insert);

		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'register_code/edit/'.$id), array('text'=>'返回列表','href'=>'register_code/index')));
	}

	public function edit($id)
	{
		auth(array('register_code_edit','register_code_view'));
		$register = $this->register_model->filter(array('id'=>$id));
		if (!$register) {
			sys_msg('记录不存在', 1);
		}
		$data['mdc_1'] = $this->register_model->medical_list('medical_device_class');
		$data['mdc_2'] = $this->register_model->medical_list('medical_device');
		
		$this->load->vars('row', $register);
		$this->load->vars('perm_edit', check_perm('register_code_edit'));
		$this->load->view('register/edit',$data);
	}

	public function proc_edit()
	{
		auth('register_code_edit');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('register_no', '注册号', 'trim|required');
		$this->form_validation->set_rules('medical_1', 'MDC_1', 'trim|required');
		$this->form_validation->set_rules('medical_2', 'MDC_2', 'trim|required');
		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}
		$update = array();
		$update['register_no'] = $this->input->post('register_no');
		$update['medical1'] = $this->input->post('medical_1');
		$update['medical2'] = $this->input->post('medical_2');
		$update['add_admin_id'] = $this->admin_id;
		$update['add_admin_time'] = time();

		$register_id = intval($this->input->post('id'));
		$register = $this->register_model->filter(array('id'=>$register_id));
		if (!$register) {
			sys_msg('记录不存在!', 1);
		}

		// $check_register = $this->register_model->filter(array('register_no'=>$update['register_no'], 'id !'=>$register_id));
		// if ($check_register) {
		// 	sys_msg('注册号重复', 1);
		// }

		$this->register_model->update($update, $register_id);
		
		sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'register_code/edit/'.$register_id), array('text'=>'返回列表','href'=>'register_code/index')));
	}


	public function delete($id)
	{
		auth('register_code_delete');
		$register = $this->register_model->filter(array('id'=>$id));

		$this->register_model->delete($id);
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'register_code/index')));		
	}

	public function grab($id)
	{
		auth('register_code_fetch');
		
		header("Content-type:text/html;charset=utf-8");

		$row = $this->register_model->filter(Array('id'=>$id));
		$name = $row->register_no;

		$uri = "http://app1.sfda.gov.cn/datasearch/face3/search.jsp";

		$header = array( "Content-Type: application/x-www-form-urlencoded; charset=utf-8" );

		$result_keys = array('standard'=>'产品标准','property'=>'产品性能结构及组成','scope'=>'产品适用范围');
		if (strpos($name, '(准)') !==false || strpos($name, '（准）') !==false || strpos($name, '准') !==false) {
			$tableId = 26;
			$bcId = '118103058617027083838706701567';
			$result_keys['product_name'] = '产品名称';
			$result_keys['unit'] = '生产单位';
			$result_keys['valid_time'] = '有效期';
		}elseif (strpos($name, '(进)') !==false || strpos($name, '（进）') !==false) {
			$tableId = 27;
			$bcId = '118103063506935484150101953610';
			$result_keys['product_name'] = '产品名称（中文）';
			$result_keys['unit'] = '生产厂商名称（英文）';
			$result_keys['valid_time'] = '有效期截止日';
		}elseif (strpos($name, '械备') !==false) {
			$tableId = 104;
			$bcId = '140599784696472870332308528649';
			$result_keys['product_name'] = '产品名称或产品分类名称';
			$result_keys['unit'] = '备案人名称';
			$result_keys['valid_time'] = '产品有效期';
			$result_keys['property'] = '产品描述或主要组成成份';
			$result_keys['scope'] = '预期用途';
			$result_keys['standard'] = '型号/规格或包装规格';
		}elseif (strpos($name, '国械注进') !==false) {
			$tableId = 27;
			$bcId = '118103063506935484150101953610';
			$result_keys['product_name'] = '产品名称（中文）';
			$result_keys['unit'] = '代理人名称';
			$result_keys['valid_time'] = '有效期至';
			$result_keys['property'] = '结构及组成';
			$result_keys['scope'] = '适用范围';
			$result_keys['standard'] = '型号、规格';
		}else{
			sys_msg('不支持查询此注册证号！', 0, array(array('text'=>'返回列表', 'href'=>'register_code/index')));
		}
		// $bcId = ($tableId == 27 )?'118103063506935484150101953610':'118103058617027083838706701567';
		// 参数数组
		$data = array (
				'bcId' => $bcId,
				'State' => 1,
				'keyword' => $name,
				'tableId' => $tableId
		);

		$return =curl_post( $uri, $data, $header );

		if( preg_match( "/callbackC(.*)'/", $return, $matches) ){
			$query_str = parse_url($matches[1]);
			parse_str($query_str['query'], $params);

			$result = curl('http://app1.sfda.gov.cn/datasearch/face3/content.jsp?'. http_build_query($params));

			$result = preg_replace("/\s/"," ",$result);
			// if( preg_match_all("/<tr>/iU", $result, $mat)){
			// if( preg_match_all("/<tr>\r\n.*<td.*>(.*)<\/td>\r\n.*<td.*>(.*)<\/td>/iU", $result, $mat)){
			if( preg_match_all("/<tr>.*<td.*>(.*)<\/td>.*<td.*>(.*)<\/td>/iU", $result, $mat)){
				$ary = array_combine($mat[1], $mat[2]);

				$update_ary = array();
				foreach ($result_keys as $key => $value) {
					$update_ary[$key] = isset($result_keys[$key])?$ary[$result_keys[$key]]:'';
				}
				$update_ary["grab_time"] = time();
				$update_ary["content"] = serialize($ary);
			}
			if ( !empty($update_ary) )
				$this->register_model->update ( $update_ary,$id);
		}
		sys_msg('抓取成功！', 0, array(array('text'=>'返回列表', 'href'=>'register_code/index')));	
	}

	 public function editable() {
        if( ! auth('register_code_edit'))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->register_model->update( $data, $pk );
        die(json_encode(Array('success'=>true,'msg'=>'操作成功！', 'value'=>443)));
       
    }  

}
###