<?php


class rf extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
        // 入库下的菜单
        $this->in_action_list=array(
//            array("name"=>"扫描上架","url"=>"purchasebox_scanning","action_code"=>"purchasebox_scanning")
//	    ,
            array("name"=>"扫描上架(新)","url"=>"purchasebox_scanning/muti","action_code"=>"purchasebox_scanning")
	    ,array("name"=>"调拨上架","url"=>"box_onsale/index/11","action_code"=>"allot_in")
        );
        // 出库下的菜单
        $this->out_action_list=array(
            array("name"=>"订单拣货","url"=>"pick/scan_pick_rf","action_code"=>"pick_scan"),
	    array("name"=>"返还出库下架","url"=>"pick_out/index/1","action_code"=>"pick_out"),
	    array("name"=>"调拨出库下架","url"=>"pick_out/index/2","action_code"=>"allot_out"),
	    array("name"=>"领用出库下架","url"=>"pick_out/index/3","action_code"=>"company_use_out")
        );
        // 盘点下的菜单
        $this->pan_action_list=array(
            array("name"=>"储位查询","url"=>"depot/location_info_scan","action_code"=>"location_info_scan")
            ,array("name"=>"条形码查询","url"=>"depot/barcode_scan","action_code"=>"barcode_scan")
            ,array("name"=>"系统盘点","url"=>"inventory/scan_list","action_code"=>"inventory_scan")
            ,array("name"=>"整储调拨","url"=>"exchange_location/index","action_code"=>"exchange_location")
            ,array("name"=>"商品移储","url"=>"return_onshelf/index","action_code"=>"return_onshelf")
        );
        // 一级菜单
        $this->rf_menu=array("in"=>array("name"=>"入库","action_list"=>$this->in_action_list),
            "out"=>array("name"=>"出库","action_list"=>$this->out_action_list),
            "pan"=>array("name"=>"盘点","action_list"=>$this->pan_action_list)
        );
        $this->_check_avail_menu();
    }

    function login()
    {
        if(!$this->admin_id)
        {
            $data['sub_page']='login';
            $this->load->view('rf/index',$data);
        }
        else
        {
            redirect('/rf/index');
        }
    }

    function proc_login()
    {
        $this->load->model('admin_model');
		$filter['admin_name'] = trim($this->input->post('admin_name', TRUE));
		$filter['admin_password'] = md5(trim($this->input->post('admin_password', TRUE)));
		$filter['user_status'] = 1;
		$admin = $this->admin_model->filter($filter);
        if( ! $admin ){
			sys_msg('登录失败',1,array(array('href'=>'rf/login','text'=>'返回')));
		}
        $last_login=date('Y-m-d H:i:s');
		$this->session->set_userdata(array(
			'admin_id' => $admin->admin_id,
			'admin_name' => $admin->admin_name,
            'real_name'=>$admin->realname,
			'action_list' => $admin->action_list,
            'last_login'=>$last_login
		));
        $update = array(
			'last_ip' => $this->input->ip_address(),
			'last_login' =>$last_login 
		);
		$this->admin_model->update($update, $admin->admin_id);
        //$this->index();
        redirect('rf');
    }

    function _check_avail_menu(){
        $rf_menu = $this->rf_menu;
        //对菜单过滤
        foreach($rf_menu as $key=>$menu)
        {
            foreach($menu['action_list'] as $action_key=>$action)
            {
                //没有权限访问则unset掉
                if(!check_perm($action['action_code']))
                {
                    unset($rf_menu[$key]['action_list'][$action_key]);
                }
            }
        }
        return $this->rf_menu = $rf_menu;
    }


    function index()
    {
        if(!$this->admin_id) { redirect('rf/login'); return; }

        /*登录用户信息*/
        $admin['admin_name']=$this->session->userdata('admin_name');
        $admin['real_name']=$this->session->userdata('real_name');
        $admin['last_login']=$this->session->userdata('last_login');
        $data['admin']=$admin;
        $data['sub_page']='info';
        $data['cur_menu']='index';

        $this->load->view('rf/index',$data);
    }
    function out(){
        if(!$this->admin_id) { redirect('rf/login'); return; }

        $data['sub_menu']=$this->rf_menu['out'];
        $data['sub_page']='sub_menu';
        $data['cur_menu']='out';

        $this->load->view('rf/index',$data);
    }
    function pan(){
        if(!$this->admin_id) { redirect('rf/login'); return; }

        $data['sub_menu']=$this->rf_menu['pan'];
        $data['sub_page']='sub_menu';
        $data['cur_menu']='pan';

        $this->load->view('rf/index',$data);
    }
    function in(){
        if(!$this->admin_id) { redirect('rf/login'); return; }

        $data['sub_menu']=$this->rf_menu['in'];
        $data['sub_page']='sub_menu';
        $data['cur_menu']='in';

        $this->load->view('rf/index',$data);
    }

    function login_out()
    {
        $this->session->sess_destroy();
        redirect('rf/login');
    }
}
?>
