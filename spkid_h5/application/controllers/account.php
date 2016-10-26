<?php

/**
 *  Account
 */
class Account extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->time = date('Y-m-d H:i:s');
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('account_model');
        $this->load->model('user_model');
        $this->load->model('voucher_model');
        $this->load->library('user_obj');
    }

    public function index() {
        if ($this->user_obj->is_login())
        {
            $user_id = $this->session->userdata('user_id');
        } else
        {
            redirect('/user/login');
        }
        $user_id = $this->user_id;
        $data['user_info'] = $this->user_model->lock_user($user_id); //获取用户(积分和余额)信息
        $data['voucher_num']  = count($this->account_model->user_voucher_num($user_id)); //获取用户(现金券)信息  

        $this->load->view('mobile/user/account',$data);
    }

    public function account_content() {
        if ($this->user_obj->is_login())
        {
            $user_id = $this->session->userdata('user_id');
        } else
        {
            redirect('/user/login');
        }
        $type = $this->input->get('type');
        $user_id = $this->user_id;

        if ($type == 'integral'){         //获取用户积分信息
            $data['integral'] = $this->account_model->integral_balance($user_id,1);
            $data['title'] = '积分收支记录';

        }elseif ($type == 'balance') {    //获取用户余额信息 
            $data['balance'] = $this->account_model->integral_balance($user_id,0);
            $data['title'] = '余额收支记录';

        }elseif ($type == 'voucher') {    //获取用户现金券信息 
            $data['voucher_ok'] = $this->account_model->user_voucher_num($user_id);  //可用的现金券
            $data['voucher_no'] = $this->account_model->user_voucher_all($user_id);  // 使用过的现金券
            $data['voucher_all'] = array_merge($data['voucher_ok'],$data['voucher_no']);
            $data['title'] = '现金券收支记录';
        }

        $this->load->view('mobile/user/account_content',$data);
    }

    //获取 积分兑换现金券 活动
    public function account_voucher() {
        if ($this->user_obj->is_login())
        {
            $user_id = $this->session->userdata('user_id');
        } else
        {
            redirect('/user/login');
        }
        $user_id = $this->user_id;
        $data['voucher_campaign'] = $this->account_model->voucher_campaign();
        $this->load->view('mobile/user/account_voucher',$data);
    }

    //兑换积分
    public function exchange_voucher() {
        $user_id = $this->user_id; // 兑换用户的id
        $release_id = intval($this->input->get('release_id'));  // 现金券活动id

        $voucher_des = $this->voucher_model->get_voucher();  //生成现金券号
        if(!empty($voucher_des)){
            $voucher_num = $voucher_des->voucher_des;
        }else{
            $voucher_num = getVoucherDes();
        }

        $voucher_release = $this->account_model->release_row($release_id);  // 积分兑换现金券 活动 信息
        $user_info = $this->user_model->lock_user($user_id); //获取用户(积分和余额)信息


        //根据时间，判断活动是否过期/兑换现金券
        if(empty($voucher_release)){
            echo json_encode(array('error'=>1,'msg_hd'=>'活动已过期!'));
            return;
        }

        // 判断用户的积分是否可以兑换现金券
        if($user_info->pay_points < $voucher_release->worth){
            echo json_encode(array('error'=>1,'msg_jf'=>'您的积分还不够!'));
            return;
        }

        //根据时间，判断活动是否过期/兑换现金券
        if(!empty($voucher_release)){
            
            $voucher_record['campaign_id'] = $voucher_release->campaign_id;
            $voucher_record['release_id'] = $release_id;
            $voucher_record['voucher_sn'] = $voucher_num;
            $voucher_record['user_id'] = $user_id;
            $voucher_record['start_date'] = $this->time;
            $voucher_record['end_date'] = date_change($this->time,'P'.$voucher_release->expire_days.'D');
            $voucher_record['voucher_amount'] = $voucher_release->voucher_amount;
            $voucher_record['min_order'] = $voucher_release->min_order;
            $voucher_record['create_date'] = date('Y-m-d H:i:s');

            $isert_id_voucher = $this->account_model->insert_exchange_voucher($voucher_record);
            if($isert_id_voucher > 0){

                $account_log['user_id'] = $user_id;
                $account_log['pay_points'] = '-'.$voucher_release->worth;
                $account_log['change_desc'] = '积分兑换 '.$voucher_release->voucher_name;
                $account_log['change_code'] = 'voucher_balance';
                $account_log['create_date'] = date('Y-m-d H:i:s');

                $this->account_model->update_exchange_voucher($user_id,$voucher_release->worth,$account_log);

                echo json_encode(array('error'=>1,'msg_yes'=>'现金券兑换成功!'));
                return;
            }
        }
    }

}
