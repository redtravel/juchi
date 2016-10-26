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
        $this->load->model('product_model');
        $this->load->helper('product');
        $this->start_time = date('Y-m-d 00:00:00');  //活动开始时间
        $this->end_time = date('Y-m-d 00:00:00');     //活动结束时间
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
        if(!empty($special_voucher) && $special_voucher->voucher_status == 0){
            echo json_encode(array('error'=>0,'msg_hd'=>'您已经领过咯!'));
            return;
        }elseif(!empty($special_voucher) && $special_voucher->voucher_status == 1){
            echo json_encode(array('error'=>0,'msg_hd'=>'您有未付款订单，请付款后再来领取！'));
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
            echo json_encode(array('error'=>0,'msg_hd'=>'活动已过期!'));
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
     *  限时抢购指定商品(只能购买一次)，并领现金券
     *
     */

    public function limit_sale($param) {
        ENVIRONMENT=='development' || $this->output->cache(CACHE_HTML_INFO);
        $this->load->model("rush_model");
                
        list($product_id,$color_id )= array_slice(array_pad(array_map('intval',explode('-',$param)),2,0),0,2);//product_id,color_id

        if(!$product_id) redirect('index'); // 商品id不存在，跳转至首页
        $ghost = valid_ghost();
        $p = NULL;
        $provider_brand = array();
        $is_preview = isset($_GET['is_preview']) && $_GET['is_preview'] == 1 ?TRUE:FALSE; 
        if (!$ghost) $p = $this->cache->get('pdetail-'.$product_id);
        if (!$p || $is_preview )
        {
            $p = $this->product_model->product_info($product_id); // 商品信息
            $p->expected_shipping_date = get_expected_shipping_date($p->product_desc_additional ); // 发货日期
            $p->product_desc_additional = get_product_desc_additional( $p ); //商品成份说明
            
            if(!$p||(!$is_preview && !$p->is_audit&&!$ghost)) redirect('index');

            format_product($p);
            $p->product_desc=adjust_path($p->product_desc);
            $p->product_desc_detail=adjust_path($p->product_desc_detail);
            
            // 取尺码对照表
            if (!$p->size_image) {//获取公用size_image
                $p->size_image = $this->product_model->get_size_img(array("brand_id"=> $p->brand_id ,"category_id"=> $p->category_id ,"sex" =>$p->product_sex ));//product_size_image
            }
            
            // 洗标
            $goods_carelabel=array();
            if ($p->goods_carelabel) {
                foreach($this->product_model->all_carelabel(array('carelabel_id'=>explode(',',$p->goods_carelabel))) as $carelabel){
                    $goods_carelabel[] = $carelabel;
                }
            }
            $p->goods_carelabel = $goods_carelabel;
            // 取颜色
            $g_list = array();
            foreach ($this->product_model->all_gallery(array('product_id'=>$product_id)) as $g) {
                if(!isset($g_list[$g->color_id]))
                    $g_list[$g->color_id] = array('default'=>NULL,'tonal'=>NULL,'part'=>array());
                if($g->image_type=='default' || $g->image_type=='tonal')
                    $g_list[$g->color_id][$g->image_type] = $g;
                else
                    $g_list[$g->color_id]['part'][] = $g;
            }
            foreach ($g_list as $key => $value) {
                if(!isset($value['default'])) unset($g_list[$key]);
            }
            $p->g_list = $g_list;

            if(!$ghost && !$is_preview) $this->cache->save('pdetail-'.$product_id, $p, CACHE_TIME_PRODUCT);
        } else {
            $g_list = $p->g_list;
        }
        
        if(!$is_preview && !$p->is_audit && !$ghost) redirect('index');
        
        // 供应商售卖前五的品牌
        $provider_brand = $this->rush_model->get_provider_brand($p->provider_id);
        // 取尺码
        $cart_list = get_pair($this->product_model->sub_in_cart($product_id),'sub_id','product_num');
        $sub_list = array();

        //product_id,color_id => 缓存库存
        $cache_key = "product_sub_" . $product_id;
                
        /**
         * 取sub信息时是否使用缓存
         * 因为目前的缓存数据方案错误（不应该把购物车数量缓存）,暂时不走缓存，等有压力后再考虑更完善的方案
         * @changed by  tony 2013-08-23
         */
        $use_cache_sub = FALSE;
        $default_sub_id = 0;
        if (!$use_cache_sub || $is_preview || ($sub_list = $this->cache->get($cache_key)) === FALSE ) {
            $sub_param = array('product_id' => $product_id );
            //sku在sub的库存
            foreach ($this->product_model->sub_list($sub_param) as $sub) {
                //if (!isset($g_list[$sub->color_id]))
                //    continue; //如果图片没有，略过,一般是由缓存引起的
                format_sub($sub);
                if ($sub->sale_num != -2 && isset($cart_list[$sub->sub_id])) {
                    $sub->sale_num = max($sub->sale_num - $cart_list[$sub->sub_id], 0);
                }
                $sub->sale_num = $sub->sale_num == -2 ? MAX_SALE_NUM : min(MAX_SALE_NUM, $sub->sale_num);
                if (!$sub->is_on_sale)
                    $sub->sale_num = 0; //如果未上架，则不能销售
                if (!isset($sub_list[$sub->color_id])) {
                    $sub_list[$sub->color_id] = array('color_id' => $sub->color_id, 'color_name' => $sub->color_name, 'sub_list' => array(), 'has_gl' => FALSE);
                }
                if ($sub->sale_num > 0) $default_sub_id = $sub->sub_id;
                $sub_list[$sub->color_id]['sub_list'][$sub->sub_id] = $sub;
                if ($sub->sale_num && !$sub_list[$sub->color_id]['has_gl']) {
                    $sub_list[$sub->color_id]['has_gl'] = TRUE;
                }
            }
            //save
            if($use_cache_sub && !$is_preview) $this->cache->save($cache_key, $sub_list, CACHE_TIME_PRODUCT_SUB);
        }

        if(!$sub_list) sys_msg('该商品不存在',1);//已下架
        if(!$color_id && $sub_list ||!isset($sub_list[$color_id])){
            $color_id = end(array_keys($sub_list));
        }
        // 分区运费用户当前所在地（省）
        $this->load->vars('provider_shipping_fee_config', $this->product_model->get_provider_shipping_fee_config($p->provider_id)); // 供应商配置
        
        // 取关联产品
        //$temp = null;
        $link_product_list = null;
        $link_product_list = $this->product_model->get_cache_link_product($product_id);
        
        //3个一组作为一个slider里面的图片展示
        //产品描述说明
        $product_additional = null;
        $product_additional = $this->product_model->get_product_additional($product_id);
        
        // 这里获取动态的seo
        $this->load->library('lib_seo');
        $seo = $this->lib_seo->get_seo_by_pagetag('pdetail', array(
                                                            'product_name' => $p->product_name,
                                                            'brand_name' => $p->brand_name
                                                            
                                                            )
        );
        $user_name = $this->session->userdata('user_name') ? $this->session->userdata('user_name') : '';
        $mobile = $this->session->userdata('mobile') ? $this->session->userdata('mobile') : '';
        $p->detail1 = adjust_path($p->detail1);
        $p->detail2 = adjust_path($p->detail2);
        $campaign = $this->product_model->get_campaign($product_id);

        //取得商品相关留言
        $param=array(
            'tag_id'=>$product_id,
            'tag_type'=>1,
            'comment_type'=>2
        );      
        $this->load->model('liuyan_model');
        $liuyan_list = $this->liuyan_model->liuyan_list($param);

        //获取限时抢购的活动
        $limit_sale = $this->special_model->limit_sale($product_id);
        if(empty($limit_sale)) redirect('index'); // 活动不存在，跳转至首页

        //当天活动商品已售数量与进度
        $num_sale = $this->special_model->num_sale($product_id);
        $num_sale = round(($num_sale->num/40.0), 2) * 100;

        if($this->start_time > $this->time){
            $down_time = 0;
        }elseif($this->start_time <= $this->time && $this->time <= $this->end_time){
            $down_time = 1;
        }elseif($this->end_time < $this->time){ 
            $down_time = 2;
        }
        //判断是否已经加入购物车
        $is_limit = $this->session->userdata('limit_user_product') == 'limit_'.$this->user_id.'_'.$product_id ? 0 : 1 ;

        $this->load->view('mobile/product/limit_sale',array(
            'title'     => $seo['title'],
            'description'   => $seo['description'],
            'keywords'  => $seo['keywords'],
            'user_name' => "{$this->session->userdata("user_name")}",
            'rank_name' => "{$this->session->userdata('rank_name')}",
            'p'     => $p,
            'g_list'    => $p->g_list,
            'default_sub_id' => $default_sub_id,
            'collect_data' => get_collect_data(),
            'provider_brand' => $provider_brand,
            'left_ad'   => array(),
            'sub_list'  => $sub_list,
            'color_id'  => $color_id,
            'page_title'    => $p->product_name."_",
            'link_product_list' => $link_product_list,
            'product_additional' => $product_additional,
            'user_name' => $user_name,
            'mobile' => $mobile,
            'campaign' => $campaign,
            'liuyan_list' => $liuyan_list['list'],
            'limit_sale' => $limit_sale,
            'num_sale' => $num_sale,
            'down_time' => $down_time,
            'is_limit' => $is_limit
        ));
    }   

    // 获取服务器时间
    function date_time(){
        echo json_encode(array('start_time' => $this->start_time,'end_time' => $this->end_time));
    }
    // 获取进度条
    function v_scroll(){
        //当天活动商品已售数量与进度
        $product_id = intval($this->input->post('product_id')); 
        $num_sale = $this->special_model->num_sale($product_id);
        $num_sale = round(($num_sale->num/40.0), 2) * 100;
        echo json_encode(array('num_sale' => $num_sale));
    }

    /**
     * 根据key或position_id获取广告
     */
    function _get_ad($cache_key,$position_tag, $size=0)
    {
        $this->load->library('lib_ad');
        return $this->lib_ad->get_ad_by_position_tag($cache_key,$position_tag, $size);
    }

    
    // 每天20:00准时发放现金券
    function limit_voucher($release_id){
        // 现金券 活动 信息
        $voucher_release = $this->special_model->release_row($release_id);  

        //根据时间，判断活动是否过期
        if(empty($voucher_release)){
            return false;
        }

        // 需要发放现金券的用户
        $user_data = $this->special_model->user_data();
        foreach ($user_data as $val_id) {
           $voucher_des = $this->voucher_model->get_voucher();  //生成现金券号
            if(!empty($voucher_des)){
                $voucher_num = $voucher_des->voucher_des;
            }else{
                $voucher_num = getVoucherDes();
            }

            //发放现金券
            if(!empty($voucher_release)){
                $voucher_record['campaign_id'] = $voucher_release->campaign_id;
                $voucher_record['release_id'] = $release_id;
                $voucher_record['voucher_sn'] = $voucher_num;
                $voucher_record['user_id'] = $val_id->user_id;
                $voucher_record['start_date'] = $this->time;
                $voucher_record['end_date'] = date_change($this->time,'P'.$voucher_release->expire_days.'D');
                $voucher_record['voucher_amount'] = $voucher_release->voucher_amount;
                $voucher_record['min_order'] = $voucher_release->min_order;
                $voucher_record['create_date'] = date('Y-m-d H:i:s');
                //插入现金券信息
                $this->account_model->insert_exchange_voucher($voucher_record); 
            }
        }
    }
}
