<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc

class Index extends CI_Controller
{
	public function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
	}

	public function login ()
	{
		if ( $this->admin_id ) redirect('index');
		$this->load->view('index/login');
	}

	public function proc_login ()
	{
		$this->load->model('admin_model');
                
                //限制浏览器登录
                //if(!strpos($_SERVER["HTTP_USER_AGENT"],"Chrome")) {
                 //   sys_msg("系统不支持此浏览器类型，不允许登录，请下载Chrome浏览器。<br>下载链接：<a href='http://www.google.cn/intl/zh-CN/chrome/browser/' style='color:blue;' target='_blank'>http://www.google.cn/intl/zh-CN/chrome/browser/</a>",1,array(array('href'=>'index/login','text'=>'返回')),FALSE);
                //}
                
		$filter['admin_name'] = trim($this->input->post('admin_name', TRUE));
		$filter['admin_password'] = md5(trim($this->input->post('admin_password', TRUE)));
		$filter['user_status'] = 1;
		$login_err_times = intval($this->session->userdata('login_err_times'));

		if($login_err_times > 2){
			$captcha = trim($this->input->post('captcha'));
			if(strtoupper(trim($this->input->post('captcha'))) != $this->session->userdata('captcha')) sys_msg('验证码错误',1,array(array('href'=>'index/login','text'=>'返回')));
		}

		$admin = $this->admin_model->filter($filter);

		if( empty($admin) ){
			$this->session->set_userdata('login_err_times', $login_err_times+1);
			sys_msg('登录失败',1,array(array('href'=>'index/login','text'=>'返回')));
		}
		$this->session->set_userdata(array(
			'admin_id' => $admin->admin_id,
			'admin_name' => $admin->admin_name,
			'realname' => $admin->realname,
			'action_list' => $admin->action_list,
			'is_online' => $admin->is_online
		));
		$this->session->unset_userdata('login_err_times');
		$update = array(
			'last_ip' => $this->input->ip_address(),
			'last_login' => date('Y-m-d H:i:s'),

		);

		$this->admin_model->update($update, $admin->admin_id);
                $this->input->set_cookie(SSO_COOKIE_USERNAME, $admin->admin_id, SSO_COOKIE_EXPRIE, SSO_COOKIE_DOMAIN);
                $this->input->set_cookie(SSO_COOKIE_PASSWORD, MD5($filter['admin_password']), SSO_COOKIE_EXPRIE, SSO_COOKIE_DOMAIN);
		redirect('index');
	}

	public function logout ()
	{
            if ($this->admin_id) {
                $this->input->set_cookie(SSO_COOKIE_USERNAME, "", 0, SSO_COOKIE_DOMAIN);
                $this->input->set_cookie(SSO_COOKIE_PASSWORD, "", 0, SSO_COOKIE_DOMAIN);
                $this->session->sess_destroy();
            }
		redirect('index/login');
	}

	public function index ()
	{
		if ( ! $this->admin_id ) { 
                    $filter['admin_id'] = $this->input->cookie(SSO_COOKIE_USERNAME);
                    $admin_password = $this->input->cookie(SSO_COOKIE_PASSWORD);
		    
                    $filter['user_status'] = 1;

                    if (empty($filter['admin_name']) || empty($admin_password)) {

			
                        redirect('index/login');    
                    }
                    $this->load->model('admin_model');
                    $admin = $this->admin_model->filter($filter);
                    if( ! $admin ){
                            				
                            $this->session->set_userdata('login_err_times', $login_err_times+1);
                            sys_msg('登录失败',1,array(array('href'=>'index/login','text'=>'返回')));
                    }
                    if (MD5($admin->admin_password) != $admin_password) {
		    					
                            sys_msg('登录失败',1,array(array('href'=>'index/login','text'=>'返回')));
                    }
                    $this->session->set_userdata(array(
                            'admin_id' => $admin->admin_id,
                            'admin_name' => $admin->admin_name,
                            'realname' => $admin->realname,
                            'action_list' => $admin->action_list,
                            'is_online' => $admin->is_online
                    ));
                    $this->session->unset_userdata('login_err_times');
                    $update = array(
                            'last_ip' => $this->input->ip_address(),
                            'last_login' => date('Y-m-d H:i:s'),

                    );
                    $this->admin_model->update($update, $admin->admin_id);
                    $url = $this->input->get_post("url");
                    redirect(isset($url) ? $url : "index/index");    
                }
		$this->load->view('index/index');
	}

	public function top ()
	{
		if ( ! $this->admin_id ) return false;
		$this->load->vars('admin_name', $this->session->userdata('admin_name'));		
		$this->load->view('index/top');
	}

	public function left ()
	{
		if ( ! $this->admin_id ) return false;
		$this->load->model('admin_model');
		$this->load->helper('category');
		$all_action = $this->admin_model->all_action();
		$all_action = category_tree($all_action,0,'action_id','parent_id');
        foreach ($all_action as $l1_k => $l1_v) {					
			foreach($l1_v->sub_items as $l2_k=>$l2_v){
				if(empty($l2_v->sub_items) || !check_perm(get_pair($l2_v->sub_items, 'action_id', 'action_code'))){
					unset($l1_v->sub_items[$l2_k]);
					continue;
				}				
			}
			if(empty($l1_v->sub_items)){
				unset($all_action[$l1_k]);
				continue;
			}
		}

		$this->load->vars('admin_menu', $all_action);
		$this->load->view('index/left');
	}

	public function drag ()
	{
		if ( ! $this->admin_id ) return false;
		$this->load->view('index/drag');
	}

	public function main ()
	{
		if ( ! $this->admin_id ) return false;
		$this->load->view('index/main');
	}

	public function footer ()
	{
		if ( ! $this->admin_id ) return false;
		$this->load->view('index/footer');
	}

	public function change_password ()
	{
		if ( !$this->admin_id ) redirect('index/login');
		$this->load->view('index/change_password');
	}

	public function proc_change_password()
	{
		if ( !$this->admin_id ) redirect('index/login');
		$this->load->model('admin_model');
		$admin_password = trim($this->input->post('admin_password'));
		if(empty($admin_password)) sys_msg('密码不能为空', 1);
		$this->admin_model->update(array('admin_password'=>md5($admin_password)), $this->admin_id);
		sys_msg('操作成功，下次登录时请使用新密码。',0,array(array('text'=>'返回','href'=>'index/main')));
	}

	public function captcha()
	{
		header("Content-type: image/PNG");
		$im   = imagecreate(46,20);
		srand((double)microtime()*1000000);
		$Red  = rand(0,200);
		$Green  = rand(0,200);
		$Blue  = rand(0,200);
		$Color  = imagecolorallocate($im, $Red, $Green, $Blue);
		$BackGround = imagecolorallocate($im, 255,255,255);
		imagefill($im,0,0,$BackGround);
		
		$captcha = strtoupper(mt_rand(1000,9999));
		$this->session->set_userdata('captcha',$captcha);
		
		imagestring($im, 5, 5, 2, $captcha, $Color);
		for($i=0;$i<200;$i++)   //加入干扰象素
		{
		    $randcolor = imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));
		    imagesetpixel($im, rand()%70 , rand()%30 , $randcolor);
		}
		imagepng($im);
		imagedestroy($im);
		
	}
	public function barcode ($code)
	{
		$this->load->library('barcode');
		$code=trim($code);
        //$code = str_replace('-',' ',$code);
        $code = urldecode($code);
		$this->barcode->createBarCode($code);
	}

	public function cls_barcode ($code)
	{
		$this->load->library('cls_barcode');
		$code=trim($code);
        //$code = str_replace('-',' ',$code);
        $code = urldecode($code);
		$this->cls_barcode->createBarCode($code);
	}
	
	public function cls_barcode_plus ($code)
	{
		$this->load->library('cls_barcode');
		$code=trim($code);
        //$code = str_replace('%20',' ',$code);
        $code = urldecode($code);
		$this->cls_barcode->createBarCode($code);
	}	
}
###
