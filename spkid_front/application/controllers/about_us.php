<?php

/**
 *  About_us
 */
class About_us extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->time = date('Y-m-d H:i:s');
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('about_us_model');
        $this->team_type = array( 0 => '口腔医疗商品', 1 => '口腔医学培训', 2 => '新牙医同盟会', 3 => '其他'); 
    }

    //关于演示站
    public function index(){
        $data = array();
        $this->load->view('about_us/index',$data);
    }
    //服务条款
    public function service(){
        $data = array();
        $this->load->view('about_us/service',$data);
    }
    //意见反馈
    public function feedback(){
        $data = array();
        $this->load->view('about_us/feedback',$data);
    }
    //意见反馈
    public function feedback_two(){
        $data = array();
        $this->load->view('about_us/feedback_two',$data);
    }
    //售后政策
    public function sales_policy(){
        $data = array();
        $this->load->view('about_us/sales_policy',$data);
    }
    //加入我们
    public function join_us(){
        $data = array();
        $this->load->view('about_us/join_us',$data);
    }

    //合作咨询
    public function team_work() {    
        $data = array();
        //合作中心热线
        $cooperation_center = $this->_get_ad('pc_cooperation_center','pc_cooperation_center');
        if(!empty($cooperation_center))
            $data['cooperation_center']=$cooperation_center;
        $this->load->view('about_us/team_work',$data);
    }


    public function team_work_add() { 
        $data = array();
        $data['team_type'] = $this->team_type; 

        //合作中心热线
        $cooperation_center = $this->_get_ad('pc_cooperation_center','pc_cooperation_center');
        if(!empty($cooperation_center))
            $data['cooperation_center']=$cooperation_center;
        $this->load->view('about_us/team_work_add',$data);
    }

    //合作咨询提交
    public function check_add() { 
        $param = array();

        $param['team_type'] = trim($this->input->post('team_type'));
        $param['team_company'] = trim($this->input->post('team_company'));
        $param['team_name']  =  trim($this->input->post('team_name'));
        $param['team_tel']  =  trim($this->input->post('team_tel'));
        $param['team_email']  =  trim($this->input->post('team_email'));
        $param['team_date'] = $this->time;
        $team_code = trim($this->input->post('team_code'));

        $validate_code = $this->session->userdata('validate_code'); //验证码

        if ($param['team_company'] == ''){
            echo json_encode(array('error' => 0, 'team_msg' => '请填写正确的名称', 'team_err' => 'team_company'));
            return;
        }
        if ($param['team_name'] == ''){
            echo json_encode(array('error' => 0, 'team_msg' => '请输入您的姓名', 'team_err' => 'team_name'));
            return;
        }
        if (!preg_match("/^(1[0-9]{10})|(0\d{2,3}-?\d{7,8})$/",$param['team_tel'])){
            echo json_encode(array('error' => 0, 'team_msg' => '请填写正确的联系电话', 'team_err' => 'team_tel'));
            return;
        }
        if (!preg_match("/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/",$param['team_email'])){
            echo json_encode(array('error' => 0, 'team_msg' => '邮箱格式输入错误', 'team_err' => 'team_email'));
            return;
        }
        if (strtolower($team_code) != $validate_code){
            echo json_encode(array('error' => 0, 'team_msg' => '验证码输入错误', 'team_err' => 'team_code'));
            return;
        }

        $team_work_id = $this->about_us_model->team_work_insert($param);
        if(!empty($team_work_id)){
            $team_msg = "您的合作申请演示站已经收到，请耐心等待客服与您联系";
            echo json_encode(array('error' => 1, 'team_msg' => $team_msg));
            return;
        }

    }

    //意见反馈提交
    public function feedback_add() { 
        // if(!$this->user_id){
        //     echo json_encode(array('error' => 2));
        //     return;
        // }
        $param = array();
        $comment_content = trim($this->input->post('comment_content'));
        $comment_name  =  trim($this->input->post('comment_name'));
        $comment_tel  =  trim($this->input->post('comment_tel'));
        $param['comment_content'] = $comment_name."--".$comment_tel."--".$comment_content;
        $param['grade'] = trim($this->input->post('grade'));
        $param['tag_type'] = 4;
        $param['comment_type'] = 1;
        $param['comment_date'] = $this->time;
        $param['user_id'] = $this->user_id;

        if (!preg_match("/^(1[0-9]{10})|(0\d{2,3}-?\d{7,8})$/",$comment_tel)){
            echo json_encode(array('error' => 0, 'comment_msg' => '请输入正确的联系方式', 'comment_err' => 'comment_tel'));
            return;
        }

        $feedback_id = $this->about_us_model->feedback_insert($param);
        if(!empty($feedback_id)){
            echo json_encode(array('error' => 1));
            return;
        }

    }

    /**
     * 根据key或position_id获取广告
     */
    function _get_ad($cache_key,$position_tag, $size=0)
    {
        $this->load->library('lib_ad');
        return $this->lib_ad->get_ad_by_position_tag($cache_key,$position_tag, $size);
    }

}
