<?php
/**
 *
 */
class Auth extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->user_id = $this->session->userdata('user_id');
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('user_model');
		$this->load->library('user_obj');
		$this->load->library('wechatauth');
	}
	
	public function index() {
		$is_login_authed = $this->session->userdata('is_login_authed');
		$wechat_info_login = $this->session->userdata('wechat_info_login');
		$referrer = urlencode($this->input->server('HTTP_REFERER'));

		if ($is_login_authed == 1 && !empty($wechat_info_login)) {		
			$url = $this->wechatauth->get_authorize_url2("http://m.yueyawang.com/auth/login_wechat?ref=$referrer",'1');
			header("Location:".$url);  
		}else{
			$this->user_obj->wechat_login_user($wechat_info_login);
			redirect($referrer);
		}
	}

	function login_wechat() {    		
    	$referrer = $_GET['ref'];
	    if(isset($_GET['code'])){		  
    		$token = $this->wechatauth->get_access_token('wxd11be5ecb1367bcf','6d05ab776fd92157d6833e2936d6f17c',$_GET['code']); //确认授权后会，根据返回的code获取token
	    	$wechat_user =  $this->wechatauth->get_user_info($token['access_token'], $token['openid']);
    		$this->user_obj->wechat_login_user($wechat_user);

    		$is_login_authed = 1;
            $this->session->set_userdata('is_login_authed',$is_login_authed);
            $this->session->set_userdata('wechat_info_login',$wechat_user);

			redirect($referrer);
	    }else{
		    echo "NO CODE";
	    }
    }
}
