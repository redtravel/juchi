<?php
class CI_Mobile{
	function __construct(){
		$this->CI=&get_instance();
		$this->CI->config->load('mobile');
		$this->config=$this->CI->config->item('mobile');		
	}	
	/**
	* @param msg 短信内容
	* @param $mobs 以分号分隔的手机号码,或是手机号码组成的数组
	* @return error message if fail. blank string if susccess
	*/
	public function send($msg,$mobs){
		if(empty($mobs)) return "手机号错误";
		if(empty($msg)) return "信息为空";
		if(is_array($mobs)) $mobs=implode(',',$mobs);
        $this->CI->load->library('sms/e_mas.php');
        return $this->CI->e_mas->sendMsg($mobs,$msg . "【宝贝购】"); 
	}
}
