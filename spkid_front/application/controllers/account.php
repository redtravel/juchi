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

    // PC 个人中心  我的优惠
    public function privilege()
    {   
        if ($this->user_obj->is_login())
        {
                $user_id = $this->session->userdata('user_id');
        } else
        {
                goto_login('user/privilege');
        }
        $data = array();
        $user_id = $this->session->userdata('user_id');
        $data['voucher_unused'] = $this->account_model->user_voucher_unused($user_id);    //可用的现金券
        $data['voucher_used'] = $this->account_model->user_voucher_used($user_id);        //使用过的现金券
        $data['voucher_expired'] = $this->account_model->user_voucher_expired($user_id);  //已过期的现金券
        $this->load->view('user/privilege',$data);
    }

    // PC 个人中心  删除  我的优惠
    public function remove_voucher()
    {
        $user_id = $this->session->userdata('user_id');
        $voucher_id = intval($this->input->post('voucher_id'));  //优惠券id

        //查询优惠券是否存在
        $voucher_data = $this->account_model->check_voucher($user_id,$voucher_id);
        if(!$voucher_data){
            print json_encode(array('err' => 1, 'msg' => '该现金券不存在'));
            return;
        }
        $this->account_model->del_voucher($voucher_data->voucher_id);
        print json_encode(array('err' => 0, 'msg' => ''));

    }

    // PC 个人中心  我的积分
    public function integral()
    {
        if ($this->user_obj->is_login())
        {
                $user_id = $this->session->userdata('user_id');
        } else
        {
                goto_login('user/integral');
        }
        $data = array();
        $page = 'recently';   //最近三个月
        $data['before'] = 1;

        //前三个月
        if($this->input->get('before')) {  
            $page = 'before'; 
            $data['before'] = 2;
        }
        $user_id = $this->session->userdata('user_id');
        $data['user_info'] = $this->user_model->lock_user($user_id); //获取用户(积分和余额)信息
        $data['integral'] = $this->account_model->integral_balance($user_id,1,$page); //用户积分明细
        $data['voucher_campaign'] = $this->account_model->voucher_campaign();  //获取 积分兑换现金券 活动
        $this->load->view('user/integral',$data);
    }

    //积分兑换现金券
    public function exchange_voucher() {
        $user_id = $this->session->userdata('user_id'); // 兑换用户的id
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
            print json_encode(array('error'=>1,'msg_hd'=>'活动已过期!'));
            return;
        }

        // 判断用户的积分是否可以兑换现金券
        if($user_info->pay_points < $voucher_release->worth){
            print json_encode(array('error'=>1,'msg_jf'=>'您的积分还不够!'));
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

                print json_encode(array('error'=>0,'msg_yes'=>'现金券兑换成功!'));
                return;
            }
        }
    }

}
