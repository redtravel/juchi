<?php

/**
 * 
 */
class Special extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->time = date('Y-m-d H:i:s');
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('special_model');
        $this->load->model('voucher_model');
        $this->load->model('account_model');
    }

    public function index($rush_id) {
        $data = array();
        $data['special'] = $this->special_model->all_special_list($rush_id);
        $data['campaign'] = $this->voucher_model->all_special_list($data['special']->campaign_id);
        $data['special_product'] = $this->special_model->get_special_product($rush_id);
        //商品收藏 数组
        $data['collect_data'] = get_collect_data();

        //关联产品广告位(多个广告)
        $ad = $this->_get_ad('miaosha_special','miaosha_special');
        if(!empty($ad))
            $data['ad']=$ad;

        $this->load->view('mobile/product/sale_special',$data);
    }   

    //领取现金券
    public function special_voucher() {
        $user_id = $this->user_id; // 用户的id
        $release_id = intval($this->input->get('release_id'));  // 现金券活动id

        //判断用户是否已经领取
        $special_voucher = $this->voucher_model->is_special_row($release_id,$user_id);  // 现金券 活动 信息
        if(!empty($special_voucher)){
            echo json_encode(array('error'=>1,'msg_hd'=>'您已经领过咯!'));
            return;
        }

        $voucher_des = $this->voucher_model->get_voucher();  //生成现金券号
        if(!empty($voucher_des)){
            $voucher_num = $voucher_des->voucher_des;
        }else{
            $voucher_num = getVoucherDes();
        }

        $voucher_release = $this->special_model->release_row($release_id);  // 现金券 活动 信息

        //根据时间，判断活动是否过期
        if(empty($voucher_release)){
            echo json_encode(array('error'=>1,'msg_hd'=>'活动已过期!'));
            return;
        }

        //领取现金券
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
                echo json_encode(array('error'=>1,'msg_ts'=>date('Y-m-d H:i',strtotime($voucher_record['start_date'])),'msg_tn'=>date('Y-m-d H:i',strtotime($voucher_record['end_date'])),'msg_min'=>$voucher_record['min_order']));
                return;
            }
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
