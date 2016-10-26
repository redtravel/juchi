<?php
/**
*
*/
class User_api extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->user_id = $this->session->userdata('user_id');
		$this->user_name = $this->session->userdata('user_name');
	}

	public function login_status_js()
	{
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		if($this->user_id)
		{
			print "var user_id={$this->user_id};\n";
			print 'document.write("欢迎 '._smarty_modifier_truncate($this->user_name, 12, '.').' [<a href=\"user/logout\">注销</a>] |")';

		}else {
			print 'document.write("<a href=\"user/login\">登录</a> | <a href=\"user/register\">注册</a> |")';
		}

	}

	public function goto_login()
	{
		$back_url=trim($this->input->post('back_url'));
		$this->session->set_userdata('back_url',$back_url);
		print json_encode(array('err'=>0,'msg'=>''));
	}

	/**
	  *	获取用户当前的地区ID
	  */
	public function get_current_region_id()
	{
		
        $this->load->library('user_obj');
		$current_region_id = $this->user_obj->get_current_region();
		print json_encode(array('err'=>0, 'msg'=>'' ,'region_id'=>$current_region_id));
	}
}
