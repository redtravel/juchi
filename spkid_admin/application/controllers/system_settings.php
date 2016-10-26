<?php
#doc
#	classname:	Season
#	scope:		PUBLIC
#
#/doc

class System_settings extends CI_Controller
{
	var $sys_display_types = Array(1=>'输入框',2=>'单选框',3=>'TEXTAREA'); //显示类型
	var $sys_store_types = Array(1=>'字符串',2=>'数字',3=>'数组');  // 存储类型

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('settings_model');
	}
	
	public function index ()
	{
		auth('system_settings_view');
		$filter = $this->uri->uri_to_assoc(3);

		$data = $this->settings_model->settings_list($filter);

		$this->load->vars('perm_edit', check_perm('system_settings_edit'));  // 设置编辑权限
		$this->load->vars('perm_delete', check_perm('system_settings_delete'));  // 设置删除权限

		$data['full_page'] = TRUE;

		$this->load->view('settings/index', $data);
	}

	public function add()
	{
		auth(array('system_settings_add','system_settings_view'));

		$data = Array();
		$data['store_types'] = $this->sys_store_types;
		$data['display_types'] = $this->sys_display_types;

		$this->load->view('settings/add',$data);
	}

	public function proc_add()
	{
		auth('system_settings_add');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('config_code', '参数代码', 'trim|required');
		$this->form_validation->set_rules('config_name', '参数名称', 'trim|required');
		$this->form_validation->set_rules('type', '类型', 'trim|required');
		$this->form_validation->set_rules('storage_type', '存储类型', 'trim|required');
		$this->form_validation->set_rules('config_value', '数值', 'trim|required');
		$this->form_validation->set_rules('comment', '备注', 'trim|required');

		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}

		$insert = array();
		$insert['config_code'] = $this->input->post('config_code');
		$insert['config_name'] = $this->input->post('config_name');
		$insert['type'] = $this->input->post('type');
		$insert['storage_type'] = $this->input->post('storage_type');
		$insert['config_value']= $this->input->post('config_value');  //数值
		$insert['comment'] = $this->input->post('comment'); //备注
		$insert['settings_time'] = time();

		// 根据ID获取当前DB的数据，活动的存储类型
		// 如果存储类型是数组，那么数值一定是数组
		$sys_store_types = array_keys($this->sys_store_types);
		if($insert['storage_type'] == $sys_store_types['1']){
			$value = $insert['config_value'];
			eval("\$value=$value;");
			$insert['config_value'] = serialize($value);
		}

		// 如果显示类型是 单选框，那么备注一定是数组
		$sys_display_types = array_keys($this->sys_display_types);
		if($insert['type'] == $sys_display_types['1']){
			$comment = $insert['comment'];
			eval("\$comment=$comment;");
			$insert['comment'] = serialize($comment);
		}else{
			$insert['comment'] = str_replace("'","\'",$insert['comment']);
		}

		$config_code = $this->settings_model->filter(array('config_code'=>$insert['config_code']));
		$config_name = $this->settings_model->filter(array('config_name'=>$insert['config_name']));

		if ($config_code) {
			sys_msg('参数代码', 1);
		}

		if ($config_name) {
			sys_msg('参数名称', 1);
		}

		$id = $this->settings_model->insert($insert);

		sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>'system_settings/index')));
	}

	public function edit($id)
	{
		auth(array('system_settings_edit','system_settings_view'));
		$settings = $this->settings_model->filter(array('id'=>$id));
		if (!$settings) {
			sys_msg('记录不存在', 1);
		}
		
		$this->load->vars('row', $settings);
		$this->load->vars('perm_edit', check_perm('system_settings_edit'));

		$this->load->view('settings/edit');
	}

	public function proc_edit()
	{
		auth('system_settings_edit');

		$this->load->library('form_validation');

		$config_id = $this->input->post('id');
		$code_name = $this->input->post('config_code');
		$storage_type = $this->input->post('storage_type'); //存储类型

		$this->form_validation->set_rules($code_name, '参数代码', 'trim|required');

		if (!$this->form_validation->run()) {
			sys_msg(validation_errors(), 1);
		}

		$update = array();

		$value = $this->input->post($code_name);
		$update['config_value'] = $value;

		// 根据ID获取当前DB的数据，活动的存储类型
		// 如果存储类型是数组
		$sys_store_types = array_keys($this->sys_store_types);

		if($storage_type == $sys_store_types['2']){
			eval("\$value=$value;");
			$update['config_value'] = serialize($value);
		}
		
		$update['settings_time'] = time();

		$config_id = intval($config_id);
		$settings = $this->settings_model->filter(array('id'=>$config_id));
		if (!$settings) {
			sys_msg('记录不存在!', 1);
		}

		$this->settings_model->update($update, $config_id);
		
		sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>'system_settings/index')));
	}

	public function delete($id)
	{
		auth('system_settings_delete');

		$this->settings_model->delete($id);
		sys_msg('操作成功', 0, array(array('text'=>'返回列表', 'href'=>'register_code/index')));		
	}


	public function generate(){

		auth('system_settings_edit');

		$dirname = SYSTEM_SETTINGS; //文件路径

		$sys_display_types = array_keys($this->sys_display_types);
		$sys_store_types = array_keys($this->sys_store_types);
		$file = fopen($dirname,'w');
		$data = $this->settings_model->settings_list();

$gen_date = date('Y-m-d');
$declare =<<<EOD
/**
 * 系统参数配置，所生成的文件。
 * 生成日期：$gen_date
 * @author: nobody
 */


EOD;
		fwrite($file,"<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n".$declare);

		foreach ($data['list'] as $value) {

			fwrite($file,"\n"."//".preg_replace("/\s/","",$value->config_name)."\n");

			if($value->type == $sys_display_types['1']){        // 如果显示类型是 单选框

				fwrite($file,"defined('".strtoupper($value->config_code)."') || define('".strtoupper($value->config_code)."', '".$value->config_value."'); // ".$value->config_name."\n");

				$comment = $value->comment;
				eval("\$comment=$comment;");
				$comment_key = array_keys($comment);
				$options_key = array_keys($value->options);

				for ($i=0; $i < count($comment_key); $i++) { 
					fwrite($file,"defined('".strtoupper($value->config_code)."_".strtoupper($comment_key[$i])."') || define('".strtoupper($value->config_code)."_".strtoupper($comment_key[$i])."', '".$options_key[$i]."'); // ".$value->options[$options_key[$i]]."\n");
				}

			}elseif($value->storage_type == $sys_store_types['2']){     // 如果存储类型是数组

				$config_value = '$'.$value->config_code." = ".$value->config_value.";\n";
				fwrite($file,$config_value);

				eval("\$config_value=$config_value;");

				$config_value_key = array_keys($config_value);
	
				// 若数组是二维的或以上，则将第二维及以上进行 serialize
				for ($i=0; $i < count($config_value_key); $i++) { 
				  if( is_array($config_value[$config_value_key[$i]]) )
					fwrite($file,"defined('".strtoupper($value->config_code)."_".strtoupper($config_value_key[$i])."') || define('".strtoupper($value->config_code)."_".strtoupper($config_value_key[$i])."', '".serialize($config_value[$config_value_key[$i]])."'); // ".$config_value_key[$i]."\n");
				  else
					fwrite($file,"defined('".strtoupper($value->config_code)."_".strtoupper($config_value_key[$i])."') || define('".strtoupper($value->config_code)."_".strtoupper($config_value_key[$i])."', '".$config_value_key[$i]."'); // ".$config_value[$config_value_key[$i]]."\n");
				}

			}else{
				fwrite($file,"defined('".strtoupper($value->config_code)."') || define('".strtoupper($value->config_code)."', '".$value->config_value."'); \n");
			}		
		}

		fclose($file);

		sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>'system_settings/index')));
	}

}
###
